<?php

namespace App\Services;

use App\Models\Book;

class BookAgentToolService
{
    public function getToolDefinitions(): array
    {
        return [
            [
                'name' => 'search_books',
                'description' => 'Search books relevant to a user query from the catalog using keyword matching and catalog filters.',
                'arguments' => [
                    'query' => 'string',
                    'category_id' => 'integer optional',
                    'language' => 'string optional',
                    'author_id' => 'integer optional',
                    'genre_id' => 'integer optional',
                    'sort' => 'string optional (price_asc|price_desc|latest)',
                    'limit' => 'integer optional (default 5, max 10)',
                    'provider' => 'string optional',
                ],
            ],
            [
                'name' => 'filter_by_mood',
                'description' => 'Filter books by tone/mood intent such as light, funny, stress relief, or avoid dark/heavy content.',
                'arguments' => [
                    'book_ids' => 'array<int> optional',
                    'mood' => 'string required (example: light_comedy, stress_relief)',
                    'limit' => 'integer optional (default 5, max 10)',
                ],
            ],
            [
                'name' => 'filter_by_price',
                'description' => 'Filter books by minimum and/or maximum price based on available format prices.',
                'arguments' => [
                    'book_ids' => 'array<int> optional',
                    'min_price' => 'number optional',
                    'max_price' => 'number optional',
                    'limit' => 'integer optional (default 5, max 10)',
                ],
            ],
            [
                'name' => 'check_stock',
                'description' => 'Check stock and purchasable format availability (ebook/audio/paperback) for selected books.',
                'arguments' => [
                    'book_ids' => 'array<int> required',
                    'limit' => 'integer optional (default 5, max 10)',
                ],
            ],
        ];
    }

    public function execute(string $toolName, array $args): array
    {
        return match ($toolName) {
            'search_books' => $this->searchBooks($args),
            'filter_by_mood' => $this->filterByMood($args),
            'filter_by_price' => $this->filterByPrice($args),
            'check_stock' => $this->checkStock($args),
            default => [
                'ok' => false,
                'tool' => $toolName,
                'error' => 'Unknown tool requested by agent.',
                'data' => [],
            ],
        };
    }

    private function searchBooks(array $args): array
    {
        $query = trim((string) ($args['query'] ?? ''));
        $limit = $this->clampLimit($args['limit'] ?? 5);
        $provider = $args['provider'] ?? null;

        if ($query === '') {
            return [
                'ok' => false,
                'tool' => 'search_books',
                'error' => 'query is required',
                'data' => [],
            ];
        }

        $queryBuilder = Book::with(['author', 'category', 'genre']);
        $this->applyCatalogFilters($queryBuilder, $args);

        $terms = $this->extractKeywords($query);
        $keywordBooks = [];
        if (! empty($terms)) {
            $keywordBooks = $queryBuilder
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
                ->when(($args['sort'] ?? null) === 'price_asc', fn ($q) => $q->orderBy('price', 'asc'))
                ->when(($args['sort'] ?? null) === 'price_desc', fn ($q) => $q->orderBy('price', 'desc'))
                ->when(! in_array(($args['sort'] ?? 'latest'), ['price_asc', 'price_desc'], true), fn ($q) => $q->latest())
                ->limit($limit)
                ->get()
                ->map(fn (Book $book) => ['book' => $book, 'score' => 0.0])
                ->all();
        }

        // Provider kept in args for forward compatibility (provider-specific search behavior).
        if (is_string($provider) && $provider !== '') {
            $keywordBooks = array_values($keywordBooks);
        }

        $merged = $this->dedupeBooksById($keywordBooks);

        return [
            'ok' => true,
            'tool' => 'search_books',
            'data' => array_slice($this->mapBooksForToolResponse($merged), 0, $limit),
        ];
    }

    private function filterByMood(array $args): array
    {
        $limit = $this->clampLimit($args['limit'] ?? 5);
        $bookIds = $this->normalizeIntArray($args['book_ids'] ?? []);
        $mood = strtolower(trim((string) ($args['mood'] ?? '')));

        if ($mood === '') {
            return [
                'ok' => false,
                'tool' => 'filter_by_mood',
                'error' => 'mood is required',
                'data' => [],
            ];
        }

        $query = Book::with(['author', 'category', 'genre']);
        if (! empty($bookIds)) {
            $query->whereIn('id', $bookIds);
        }

        $books = $query->limit(50)->get();
        $rows = [];

        foreach ($books as $book) {
            $text = strtolower(implode(' ', [
                (string) $book->name,
                (string) $book->description,
                (string) ($book->category->name ?? ''),
                (string) ($book->genre->name ?? ''),
            ]));

            $score = 0.0;
            $lightKeywords = ['light', 'fun', 'funny', 'humor', 'comedy', 'uplifting', 'feel-good', 'stress', 'relax'];
            $heavyKeywords = ['dark', 'horror', 'murder', 'tragic', 'trauma', 'war', 'violent', 'depress', 'grim'];

            foreach ($lightKeywords as $word) {
                if (str_contains($text, $word)) {
                    $score += 1.0;
                }
            }
            foreach ($heavyKeywords as $word) {
                if (str_contains($text, $word)) {
                    $score -= 1.0;
                }
            }

            if (str_contains($mood, 'comedy') && str_contains($text, 'comedy')) {
                $score += 1.5;
            }
            if ((str_contains($mood, 'stress') || str_contains($mood, 'relief')) && ! str_contains($text, 'dark')) {
                $score += 0.5;
            }

            if ($score <= 0) {
                continue;
            }

            $rows[] = [
                'book' => $book,
                'score' => $score,
            ];
        }

        usort($rows, fn (array $a, array $b) => ($b['score'] <=> $a['score']));

        return [
            'ok' => true,
            'tool' => 'filter_by_mood',
            'data' => array_slice($this->mapBooksForToolResponse($rows), 0, $limit),
        ];
    }

    private function filterByPrice(array $args): array
    {
        $min = isset($args['min_price']) ? (float) $args['min_price'] : null;
        $max = isset($args['max_price']) ? (float) $args['max_price'] : null;
        $limit = $this->clampLimit($args['limit'] ?? 5);
        $bookIds = $this->normalizeIntArray($args['book_ids'] ?? []);

        $query = Book::with(['author', 'category', 'genre']);

        if (! empty($bookIds)) {
            $query->whereIn('id', $bookIds);
        }

        $books = $query->limit(40)->get();
        $rows = [];

        foreach ($books as $book) {
            $bestPrice = $this->bestAvailablePrice($book);
            if ($bestPrice === null) {
                continue;
            }

            if ($min !== null && $bestPrice < $min) {
                continue;
            }

            if ($max !== null && $bestPrice > $max) {
                continue;
            }

            $rows[] = [
                'book' => $book,
                'score' => 0.0,
                'best_price' => $bestPrice,
            ];
        }

        usort($rows, fn (array $a, array $b) => ($a['best_price'] <=> $b['best_price']));

        return [
            'ok' => true,
            'tool' => 'filter_by_price',
            'data' => array_slice($this->mapBooksForToolResponse($rows), 0, $limit),
        ];
    }

    private function checkStock(array $args): array
    {
        $bookIds = $this->normalizeIntArray($args['book_ids'] ?? []);
        $limit = $this->clampLimit($args['limit'] ?? 5);

        if (empty($bookIds)) {
            return [
                'ok' => false,
                'tool' => 'check_stock',
                'error' => 'book_ids is required',
                'data' => [],
            ];
        }

        $books = Book::with(['author', 'category', 'genre'])
            ->whereIn('id', $bookIds)
            ->limit($limit)
            ->get();

        $data = [];
        foreach ($books as $book) {
            $data[] = [
                'id' => $book->id,
                'name' => $book->name,
                'author' => $book->author->name ?? 'Unknown',
                'category' => $book->category->name ?? 'Unknown',
                'genre' => $book->genre->name ?? 'Unknown',
                'available_formats' => [
                    'ebook' => (bool) $book->has_ebook,
                    'audio' => (bool) $book->has_audio,
                    'paperback' => (bool) $book->has_paperback,
                ],
                'stock' => (int) $book->stock,
                'paperback_available_now' => (bool) ($book->has_paperback && (int) $book->stock > 0),
                'is_premium' => (bool) $book->is_premium,
            ];
        }

        return [
            'ok' => true,
            'tool' => 'check_stock',
            'data' => $data,
        ];
    }

    private function mapBooksForToolResponse(array $rows): array
    {
        $data = [];
        foreach ($rows as $row) {
            /** @var Book $book */
            $book = $row['book'];
            $data[] = [
                'id' => $book->id,
                'name' => $book->name,
                'author' => $book->author->name ?? 'Unknown',
                'category' => $book->category->name ?? 'Unknown',
                'genre' => $book->genre->name ?? 'Unknown',
                'score' => (float) ($row['score'] ?? 0.0),
                'best_price' => $this->bestAvailablePrice($book),
                'prices' => [
                    'ebook' => $book->ebook_price !== null ? (float) $book->ebook_price : null,
                    'audio' => $book->audio_price !== null ? (float) $book->audio_price : null,
                    'paperback' => $book->paperback_price !== null ? (float) $book->paperback_price : null,
                ],
                'stock' => (int) $book->stock,
                'is_premium' => (bool) $book->is_premium,
            ];
        }

        return $data;
    }

    private function bestAvailablePrice(Book $book): ?float
    {
        $candidates = [];
        if ($book->has_ebook && $book->ebook_price !== null) {
            $candidates[] = (float) $book->ebook_price;
        }
        if ($book->has_audio && $book->audio_price !== null) {
            $candidates[] = (float) $book->audio_price;
        }
        if ($book->has_paperback && $book->paperback_price !== null) {
            $candidates[] = (float) $book->paperback_price;
        }

        if (empty($candidates) && $book->price !== null) {
            $candidates[] = (float) $book->price;
        }

        return empty($candidates) ? null : min($candidates);
    }

    private function dedupeBooksById(array $rows): array
    {
        $seen = [];
        $out = [];
        foreach ($rows as $row) {
            /** @var Book|null $book */
            $book = $row['book'] ?? null;
            if (! $book instanceof Book) {
                continue;
            }
            if (isset($seen[$book->id])) {
                continue;
            }
            $seen[$book->id] = true;
            $out[] = $row;
        }

        return $out;
    }

    private function applyCatalogFilters($query, array $args): void
    {
        if (! empty($args['category_id']) && is_numeric($args['category_id'])) {
            $query->where('category_id', (int) $args['category_id']);
        }

        if (! empty($args['language']) && is_string($args['language'])) {
            $query->where('language', $args['language']);
        }

        if (! empty($args['author_id']) && is_numeric($args['author_id'])) {
            $query->where('author_id', (int) $args['author_id']);
        }

        if (! empty($args['genre_id']) && is_numeric($args['genre_id'])) {
            $query->where('genre_id', (int) $args['genre_id']);
        }
    }

    private function clampLimit(mixed $raw): int
    {
        $value = (int) $raw;
        if ($value < 1) {
            return 5;
        }

        return min($value, 10);
    }

    private function normalizeIntArray(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($id) => is_numeric($id) ? (int) $id : null,
            $value
        )));
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
