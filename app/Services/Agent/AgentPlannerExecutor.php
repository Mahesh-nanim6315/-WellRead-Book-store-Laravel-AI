<?php

namespace App\Services\Agent;

use App\Services\BookAgentToolService;
use App\Services\Contracts\LLMServiceInterface;
use Illuminate\Support\Str;
use Throwable;

class AgentPlannerExecutor
{
    public function __construct(
        private readonly QueryIntentParser $parser,
        private readonly BookRetriever $retriever,
        private readonly BookAgentToolService $toolService,
        private readonly AnswerComposer $composer,
    ) {
    }

    public function execute(
        LLMServiceInterface $llm,
        string $userMessage,
        string $conversation,
        array $topBooks,
        array $constraints,
        array $topDocChunks = []
    ): array {
        $workingBookIds = $this->retriever->extractIdsFromRows($topBooks);
        $observations = [];

        if ($this->parser->hasHardConstraints($constraints)) {
            $workingBookIds = $this->applyHardConstraintSequence($userMessage, $workingBookIds, $constraints, $observations);
            $topBooks = $this->retriever->hydrateBookRows($workingBookIds, $topBooks);

            if (empty($topBooks)) {
                return [
                    'answer' => $this->composer->buildNoConstraintMatchAnswer($constraints),
                    'books' => [],
                    'source' => 'catalog_constraints',
                ];
            }
        }

        $maxSteps = max(0, (int) config('ai.max_agent_steps', 1));
        if ($maxSteps === 0) {
            $filteredRows = $this->retriever->enforceRowsAgainstConstraints(
                $this->retriever->hydrateBookRows($workingBookIds, $topBooks),
                $constraints
            );

            if (empty($filteredRows)) {
                return [
                    'answer' => $this->composer->buildNoConstraintMatchAnswer($constraints),
                    'books' => [],
                    'source' => 'catalog_constraints',
                ];
            }

            $usedFallback = false;
            $answer = $this->composer->buildAgentFinalAnswer(
                $llm,
                $userMessage,
                $conversation,
                $filteredRows,
                $observations,
                $topDocChunks,
                $usedFallback
            );

            return [
                'answer' => $answer,
                'books' => $filteredRows,
                'source' => $usedFallback ? 'catalog_fallback' : 'llm',
            ];
        }

        for ($step = 1; $step <= $maxSteps; $step++) {
            $decision = $this->decideNextAction(
                $llm,
                $userMessage,
                $conversation,
                $observations,
                $topBooks,
                $workingBookIds
            );

            $action = strtolower((string) ($decision['action'] ?? 'final_answer'));
            if ($action === 'final_answer') {
                $filteredRows = $this->retriever->enforceRowsAgainstConstraints(
                    $this->retriever->hydrateBookRows($workingBookIds, $topBooks),
                    $constraints
                );

                if (empty($filteredRows)) {
                    return [
                        'answer' => $this->composer->buildNoConstraintMatchAnswer($constraints),
                        'books' => [],
                        'source' => 'catalog_constraints',
                    ];
                }

                $answer = trim((string) ($decision['final_answer'] ?? ''));
                $source = 'llm';
                if ($answer === '') {
                    $usedFallback = false;
                    $answer = $this->composer->buildAgentFinalAnswer(
                        $llm,
                        $userMessage,
                        $conversation,
                        $filteredRows,
                        $observations,
                        $topDocChunks,
                        $usedFallback
                    );
                    $source = $usedFallback ? 'catalog_fallback' : 'llm';
                }

                return [
                    'answer' => $answer,
                    'books' => $filteredRows,
                    'source' => $source,
                ];
            }

            if ($action !== 'call_tool') {
                $observations[] = [
                    'step' => $step,
                    'tool' => 'none',
                    'result' => ['ok' => false, 'error' => 'Invalid action from model, expected call_tool or final_answer.'],
                ];
                continue;
            }

            $toolName = (string) ($decision['tool_name'] ?? '');
            $toolArgs = is_array($decision['tool_args'] ?? null) ? $decision['tool_args'] : [];

            if (! isset($toolArgs['book_ids']) && in_array($toolName, ['filter_by_price', 'check_stock'], true)) {
                $toolArgs['book_ids'] = $workingBookIds;
            }

            $toolResult = $this->toolService->execute($toolName, $toolArgs);
            $observations[] = [
                'step' => $step,
                'tool' => $toolName,
                'args' => $toolArgs,
                'result' => $toolResult,
            ];

            $resultIds = $this->retriever->extractIdsFromToolData($toolResult['data'] ?? []);
            if (! empty($resultIds)) {
                $workingBookIds = $resultIds;
                if ($this->parser->hasHardConstraints($constraints)) {
                    $workingBookIds = $this->retriever->filterBookIdsByConstraints($workingBookIds, $constraints);
                }
            }
        }

        $finalRows = $this->retriever->enforceRowsAgainstConstraints(
            $this->retriever->hydrateBookRows($workingBookIds, $topBooks),
            $constraints
        );

        if (empty($finalRows)) {
            return [
                'answer' => $this->composer->buildNoConstraintMatchAnswer($constraints),
                'books' => [],
                'source' => 'catalog_constraints',
            ];
        }

        return [
            'answer' => $this->composer->buildAgentFallbackAnswer($finalRows, $observations),
            'books' => $finalRows,
            'source' => 'catalog_fallback',
        ];
    }

    private function applyHardConstraintSequence(
        string $userMessage,
        array $workingBookIds,
        array $constraints,
        array &$observations
    ): array {
        if (empty($workingBookIds)) {
            $searchArgs = [
                'query' => $userMessage,
                'language' => $constraints['language'],
                'limit' => 10,
                'sort' => 'latest',
            ];
            $searchResult = $this->toolService->execute('search_books', $searchArgs);
            $observations[] = [
                'step' => 0,
                'tool' => 'search_books',
                'args' => $searchArgs,
                'result' => $searchResult,
            ];
            $workingBookIds = $this->retriever->extractIdsFromToolData($searchResult['data'] ?? []);
        }

        $workingBookIds = $this->retriever->filterBookIdsByConstraints($workingBookIds, [
            'language' => $constraints['language'],
            'min_price' => null,
            'max_price' => null,
            'mood' => null,
        ]);

        if (($constraints['mood'] ?? null) !== null && ! empty($workingBookIds)) {
            $moodArgs = [
                'book_ids' => $workingBookIds,
                'mood' => $constraints['mood'],
                'limit' => 10,
            ];
            $moodResult = $this->toolService->execute('filter_by_mood', $moodArgs);
            $observations[] = [
                'step' => 0,
                'tool' => 'filter_by_mood',
                'args' => $moodArgs,
                'result' => $moodResult,
            ];
            $workingBookIds = $this->retriever->extractIdsFromToolData($moodResult['data'] ?? []);
        }

        if ((($constraints['min_price'] ?? null) !== null || ($constraints['max_price'] ?? null) !== null) && ! empty($workingBookIds)) {
            $priceArgs = [
                'book_ids' => $workingBookIds,
                'min_price' => $constraints['min_price'],
                'max_price' => $constraints['max_price'],
                'limit' => 10,
            ];
            $priceResult = $this->toolService->execute('filter_by_price', $priceArgs);
            $observations[] = [
                'step' => 0,
                'tool' => 'filter_by_price',
                'args' => $priceArgs,
                'result' => $priceResult,
            ];
            $workingBookIds = $this->retriever->extractIdsFromToolData($priceResult['data'] ?? []);
        }

        return $workingBookIds;
    }

    private function decideNextAction(
        LLMServiceInterface $llm,
        string $userMessage,
        string $conversation,
        array $observations,
        array $topBooks,
        array $workingBookIds
    ): array {
        $toolDefs = json_encode($this->toolService->getToolDefinitions(), JSON_PRETTY_PRINT) ?: '[]';
        $obsJson = json_encode($this->composer->compactObservations($observations), JSON_PRETTY_PRINT) ?: '[]';
        $workingIds = empty($workingBookIds) ? '[]' : '[' . implode(', ', $workingBookIds) . ']';
        $catalog = $this->composer->buildBookContextCompact($topBooks, $this->retriever);

        $prompt = <<<PROMPT
You are an autonomous ReAct-style bookstore agent.
Your job is to decide the next action.

Conversation memory:
{$conversation}

Current user message:
{$userMessage}

Retrieved catalog candidates (id | title | author | category | genre | best_price):
{$catalog}

Current working book ids:
{$workingIds}

Available tools:
{$toolDefs}

Prior tool observations:
{$obsJson}

Rules:
- Use tools only when needed for filtering, stock checks, or narrowing options.
- Never invent books or ids.
- Prefer max 3 final recommendations.
- If user intent includes mood/tone goals (examples: light, comedy, stress relief, feel-good), call filter_by_mood before final_answer.
- If enough info is available, return final_answer.
- Return ONLY strict JSON with this schema:
{
  "action": "call_tool" or "final_answer",
  "tool_name": "search_books|filter_by_mood|filter_by_price|check_stock" or "",
  "tool_args": {},
  "final_answer": ""
}
PROMPT;

        try {
            $raw = $llm->generate($prompt);
        } catch (Throwable $e) {
            return ['action' => 'final_answer', 'final_answer' => ''];
        }

        $json = $this->extractJsonObject($raw);
        if ($json === null) {
            return ['action' => 'final_answer', 'final_answer' => ''];
        }

        $action = strtolower((string) ($json['action'] ?? 'final_answer'));
        if (! in_array($action, ['call_tool', 'final_answer'], true)) {
            $action = 'final_answer';
        }

        return [
            'action' => $action,
            'tool_name' => (string) ($json['tool_name'] ?? ''),
            'tool_args' => is_array($json['tool_args'] ?? null) ? $json['tool_args'] : [],
            'final_answer' => (string) ($json['final_answer'] ?? ''),
        ];
    }

    private function extractJsonObject(string $text): ?array
    {
        $trimmed = trim($text);
        if (Str::startsWith($trimmed, '```')) {
            $trimmed = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $trimmed) ?? $trimmed;
            $trimmed = trim($trimmed);
        }

        $decoded = json_decode($trimmed, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{.*\}/s', $text, $m) === 1) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}
