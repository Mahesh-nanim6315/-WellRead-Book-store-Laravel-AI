<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use Illuminate\Support\Facades\App;
use App\Services\Contracts\LLMServiceInterface;

class GenerateBookEmbeddings extends Command
{
    protected $signature = 'books:generate-embeddings 
                        {--provider= : LLM provider}
                        {--force : Regenerate all embeddings}';

    protected $description = 'Generate AI embeddings for books';

    public function handle()
    {
        $provider = $this->option('provider') ?? config('ai.embedding_provider', config('ai.provider'));

        $this->info("Starting embedding generation using: {$provider}");

        $llm = $this->resolveModel($provider);

        $booksQuery = Book::with(['author', 'category', 'genre']);

        if (! $this->option('force')) {
            $booksQuery->where(function ($q) use ($provider) {
                $q->whereNull('embedding')
                    ->orWhereNull('embedding_provider')
                    ->orWhere('embedding_provider', '!=', $provider);
            });
        }

        $books = $booksQuery->get();

        if ($books->isEmpty()) {
            $this->info("No books found to process.");
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($books->count());
        $bar->start();

        foreach ($books as $book) {

            try {
                $text = $this->buildEmbeddingText($book);

                $embedding = $llm->embedding($text);

                if (! empty($embedding)) {
                    $book->embedding = json_encode($embedding);
                    $book->embedding_provider = $provider;
                    $book->save();
                } else {
                    $this->error("\nFailed embedding for: {$book->name}");
                }

            } catch (\Throwable $e) {
                $this->error("\nError for {$book->name}: " . $e->getMessage());
            }

            $bar->advance();

            usleep(200000); // 0.2 sec throttle
        }

        $bar->finish();

        $this->info("\nEmbeddings generation completed successfully!");

        return Command::SUCCESS;
    }

    /**
     * Resolve LLM provider dynamically via config.
     */
    private function resolveModel(string $provider): LLMServiceInterface
    {
        $class = config("ai.providers.$provider");

        if (! $class || ! class_exists($class)) {
            $class = config("ai.providers.openai");
        }

        return App::make($class);
    }

    /**
     * Build rich embedding context
     */
    private function buildEmbeddingText($book): string
    {
        return trim("
    Book Name: {$book->name}
    Author: " . ($book->author->name ?? 'Unknown') . "
    Category: " . ($book->category->name ?? 'Unknown') . "
    Genre: " . ($book->genre->name ?? 'Unknown') . "
    Description: {$book->description}
    Language: {$book->language}
    Base Price: {$book->price}

    Formats Available:
    Ebook: " . ($book->has_ebook ? 'Yes' : 'No') . "
    Audio: " . ($book->has_audio ? 'Yes' : 'No') . "
    Paperback: " . ($book->has_paperback ? 'Yes' : 'No') . "
            ");
        }
}
