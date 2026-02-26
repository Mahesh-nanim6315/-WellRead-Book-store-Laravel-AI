<?php

namespace App\Services\Agent;

use App\Models\Book;
use App\Services\BookVectorSearchService;
use App\Services\Contracts\LLMServiceInterface;
use Throwable;

class BookRetriever
{
    public function __construct(
        private readonly BookVectorSearchService $vectorSearch,
    ) {
    }

    public function retrieveRelevantBooks(string $userMessage, LLMServiceInterface $llm, string $provider, int $limit = 5): array
    {
        try {
            $queryEmbedding = $llm->embedding($userMessage);
        } catch (Throwable $e) {
            return $this->keywordSearch($userMessage, $limit);
        }

        if (empty($queryEmbedding) || count($queryEmbedding) < 10) {
            return $this->keywordSearch($userMessage, $limit);
        }

        $topBooks = $this->vectorSearch->search($queryEmbedding, $limit, 0.35, $provider);
        if (empty($topBooks)) {
            $topBooks = $this->vectorSearch->search($queryEmbedding, $limit, 0.35, null);
        }
        if (empty($topBooks)) {
            $topBooks = $this->vectorSearch->search($queryEmbedding, $limit, 0.20, null);
        }

        return empty($topBooks) ? $this->keywordSearch($userMessage, $limit) : $topBooks;
    }

    public function retrieveByTopic(string $topic, int $limit = 3): array
    {
        $topic = strtolower(trim($topic));
        if ($topic === '') {
            return [];
        }

        $books = Book::with(['author', 'category', 'genre'])
            ->where(function ($q) use ($topic) {
                $like = '%' . $topic . '%';
                $q->where('name', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', $like))
                    ->orWhereHas('genre', fn ($gq) => $gq->where('name', 'like', $like));
            })
            ->limit(max($limit * 4, 12))
            ->get();

        $rows = [];
        foreach ($books as $book) {
            $score = 0.0;
            if (str_contains(strtolower((string) ($book->category->name ?? '')), $topic)) {
                $score += 2.0;
            }
            if (str_contains(strtolower((string) ($book->genre->name ?? '')), $topic)) {
                $score += 2.0;
            }
            if (str_contains(strtolower((string) $book->name), $topic)) {
                $score += 1.0;
            }
            if (str_contains(strtolower((string) $book->description), $topic)) {
                $score += 0.5;
            }

            $rows[] = [
                'book' => $book,
                'score' => $score,
            ];
        }

        usort($rows, fn (array $a, array $b) => ($b['score'] <=> $a['score']));

        return array_slice($rows, 0, $limit);
    }

    public function findBookByTitle(string $title): ?Book
    {
        $book = Book::with(['author', 'category', 'genre'])
            ->whereRaw('LOWER(name) = ?', [strtolower($title)])
            ->first();

        if ($book) {
            return $book;
        }

        return Book::with(['author', 'category', 'genre'])
            ->where('name', 'like', '%' . $title . '%')
            ->orderByRaw('CASE WHEN LOWER(name) LIKE ? THEN 0 ELSE 1 END', [strtolower($title) . '%'])
            ->first();
    }

    public function findBooksByAuthor(string $authorName, int $limit = 3): array
    {
        $authorName = trim($authorName);
        if ($authorName === '') {
            return [];
        }

        $like = '%' . $authorName . '%';
        $books = Book::with(['author', 'category', 'genre'])
            ->whereHas('author', fn ($q) => $q->where('name', 'like', $like))
            ->limit(max($limit, 1))
            ->get();

        $rows = [];
        foreach ($books as $book) {
            $rows[] = [
                'book' => $book,
                'score' => 1.0,
            ];
        }

        return $rows;
    }

    public function hydrateBookRows(array $bookIds, array $fallbackTopBooks): array
    {
        if (empty($bookIds)) {
            return $fallbackTopBooks;
        }

        $books = Book::with(['author', 'category', 'genre'])
            ->whereIn('id', $bookIds)
            ->get()
            ->keyBy('id');

        $rows = [];
        foreach ($bookIds as $id) {
            $book = $books->get($id);
            if (! $book) {
                continue;
            }
            $rows[] = ['book' => $book, 'score' => 0.0];
        }

        return empty($rows) ? $fallbackTopBooks : $rows;
    }

    public function extractIdsFromRows(array $rows): array
    {
        $ids = [];
        foreach ($rows as $row) {
            $book = $row['book'] ?? null;
            if ($book instanceof Book) {
                $ids[] = (int) $book->id;
            }
        }

        return array_values(array_unique($ids));
    }

    public function extractIdsFromToolData(array $data): array
    {
        $ids = [];
        foreach ($data as $row) {
            if (isset($row['id']) && is_numeric($row['id'])) {
                $ids[] = (int) $row['id'];
            }
        }

        return array_values(array_unique($ids));
    }

    public function filterBookIdsByConstraints(array $bookIds, array $constraints): array
    {
        if (empty($bookIds)) {
            return [];
        }

        $rows = $this->hydrateBookRows($bookIds, []);
        $rows = $this->enforceRowsAgainstConstraints($rows, $constraints);

        return $this->extractIdsFromRows($rows);
    }

    public function enforceRowsAgainstConstraints(array $rows, array $constraints): array
    {
        $filtered = [];

        foreach ($rows as $row) {
            /** @var Book $book */
            $book = $row['book'];

            if (($constraints['language'] ?? null) !== null) {
                $bookLang = strtolower((string) $book->language);
                if ($bookLang !== strtolower((string) $constraints['language'])) {
                    continue;
                }
            }

            $price = $this->bestAvailablePrice($book);
            if (($constraints['min_price'] ?? null) !== null && $price < (float) $constraints['min_price']) {
                continue;
            }
            if (($constraints['max_price'] ?? null) !== null && $price > (float) $constraints['max_price']) {
                continue;
            }

            if (($constraints['mood'] ?? null) !== null) {
                $text = strtolower(implode(' ', [
                    (string) $book->name,
                    (string) $book->description,
                    (string) ($book->category->name ?? ''),
                    (string) ($book->genre->name ?? ''),
                ]));

                $hasPositiveMood = str_contains($text, 'comedy')
                    || str_contains($text, 'humor')
                    || str_contains($text, 'funny')
                    || str_contains($text, 'light')
                    || str_contains($text, 'feel-good')
                    || str_contains($text, 'feel good');

                $hasNegativeMood = str_contains($text, 'horror')
                    || str_contains($text, 'dark')
                    || str_contains($text, 'tragic')
                    || str_contains($text, 'violent')
                    || str_contains($text, 'grim');

                if (! $hasPositiveMood || $hasNegativeMood) {
                    continue;
                }
            }

            $filtered[] = $row;
        }

        return $filtered;
    }

    public function bestAvailablePrice(Book $book): float
    {
        $prices = [];
        if ($book->has_ebook && $book->ebook_price !== null) {
            $prices[] = (float) $book->ebook_price;
        }
        if ($book->has_audio && $book->audio_price !== null) {
            $prices[] = (float) $book->audio_price;
        }
        if ($book->has_paperback && $book->paperback_price !== null) {
            $prices[] = (float) $book->paperback_price;
        }
        if (empty($prices)) {
            $prices[] = (float) ($book->price ?? 0);
        }

        return min($prices);
    }

    private function keywordSearch(string $message, int $limit = 3): array
    {
        $terms = $this->extractKeywords($message);

        if (empty($terms)) {
            return [];
        }

        $books = Book::with(['author', 'category', 'genre'])
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $like = '%' . $term . '%';
                    $q->orWhere('name', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', $like))
                        ->orWhereHas('genre', fn ($gq) => $gq->where('name', 'like', $like))
                        ->orWhereHas('author', fn ($aq) => $aq->where('name', 'like', $like));
                }
            })
            ->limit($limit)
            ->get();

        $rows = [];
        foreach ($books as $book) {
            $rows[] = ['book' => $book, 'score' => 0.0];
        }

        return $rows;
    }

    private function extractKeywords(string $message): array
    {
        $normalized = strtolower($message);
        $tokens = preg_split('/[^a-z0-9]+/', $normalized) ?: [];
        $stopWords = [
            'the', 'a', 'an', 'and', 'or', 'of', 'for', 'to', 'in', 'on', 'with',
            'by', 'me', 'my', 'i', 'give', 'show', 'suggest', 'recommend', 'books', 'book',
        ];

        $terms = [];
        foreach ($tokens as $token) {
            if ($token === '' || strlen($token) < 3) {
                continue;
            }
            if (in_array($token, $stopWords, true)) {
                continue;
            }
            $terms[] = $token;
        }

        return array_values(array_unique($terms));
    }
}
