<?php

namespace App\Services\Agent;

class QueryIntentParser
{
    public function isGreeting(string $message): bool
    {
        $normalized = strtolower(trim($message));
        return preg_match('/^(hi|hello|hey|good morning|good evening)([!. ]|$)/i', $normalized) === 1;
    }

    public function parseHardConstraints(string $message): array
    {
        $text = strtolower($message);
        $constraints = [
            'language' => null,
            'min_price' => null,
            'max_price' => null,
            'mood' => null,
            'currency' => null,
        ];

        if (preg_match('/(?:₹|rs\.?|inr|rupees?|rupess)\b/i', $message) === 1) {
            $constraints['currency'] = 'INR';
        } elseif (preg_match('/(?:\$|usd|dollars?)\b/i', $message) === 1) {
            $constraints['currency'] = 'USD';
        }

        if (preg_match('/\b(?:only\s+in|in)\s+(english|hindi|tamil|telugu)\b/i', $message, $m) === 1) {
            $constraints['language'] = strtolower(trim($m[1]));
        } elseif (preg_match('/\b(english|hindi|tamil|telugu)\b/i', $message, $m) === 1 && str_contains($text, 'only')) {
            $constraints['language'] = strtolower(trim($m[1]));
        }

        if (preg_match('/\b(?:under|below|less than|max(?:imum)?|at most)\s*\$?\s*(\d+(?:\.\d+)?)\b/i', $message, $m) === 1) {
            $constraints['max_price'] = (float) $m[1];
        }

        if (preg_match('/\b(?:over|above|more than|min(?:imum)?|at least)\s*\$?\s*(\d+(?:\.\d+)?)\b/i', $message, $m) === 1) {
            $constraints['min_price'] = (float) $m[1];
        }

        if (preg_match('/\bbetween\s*\$?\s*(\d+(?:\.\d+)?)\s*(?:and|-)\s*\$?\s*(\d+(?:\.\d+)?)\b/i', $message, $m) === 1) {
            $a = (float) $m[1];
            $b = (float) $m[2];
            $constraints['min_price'] = min($a, $b);
            $constraints['max_price'] = max($a, $b);
        }

        $hasMoodIntent = str_contains($text, 'light')
            || str_contains($text, 'comedy')
            || str_contains($text, 'funny')
            || str_contains($text, 'humor')
            || str_contains($text, 'stress')
            || str_contains($text, 'feel-good')
            || str_contains($text, 'feel good');

        if ($hasMoodIntent) {
            $constraints['mood'] = 'light_comedy_stress_relief';
        }

        return $constraints;
    }

    public function hasHardConstraints(array $constraints): bool
    {
        return $constraints['language'] !== null
            || $constraints['min_price'] !== null
            || $constraints['max_price'] !== null
            || $constraints['mood'] !== null;
    }

    public function isAuthorFactQuery(string $message): bool
    {
        return preg_match('/\b(who\s+is\s+the\s+author\s+of|who\s+is\s+author\s+of|author\s+of)\b/i', $message) === 1;
    }

    public function extractBookTitleFromAuthorQuery(string $message): ?string
    {
        $trimmed = trim($message);

        if (preg_match('/author\s+of\s+["\']?(.+?)["\']?(?:\s+book)?[\?\.\!]*$/i', $trimmed, $m) === 1) {
            return trim($m[1], " \t\n\r\0\x0B\"'");
        }

        if (preg_match('/who\s+is\s+the\s+author\s+of\s+["\']?(.+?)["\']?[\?\.\!]*$/i', $trimmed, $m) === 1) {
            return trim($m[1], " \t\n\r\0\x0B\"'");
        }

        return null;
    }

    public function parseListIntent(string $message): ?array
    {
        $normalized = strtolower(trim($message));
        $hasListVerb = preg_match('/\b(name|give|show|suggest|recommend|list)\b/i', $normalized) === 1;
        $hasBooksWord = preg_match('/\bbooks?\b/i', $normalized) === 1;

        if (! $hasListVerb || ! $hasBooksWord) {
            return null;
        }

        $limit = 3;
        if (preg_match('/\b(\d{1,2})\b/', $normalized, $m) === 1) {
            $limit = max(1, min((int) $m[1], 10));
        }

        $topics = [
            'comic', 'drama', 'fantasy', 'thriller', 'romance', 'horror',
            'anime', 'manga', 'science', 'social', 'family', 'historical',
            'humor', 'mystery', 'fiction', 'poetry', 'business', 'technology',
        ];

        $topic = null;
        foreach ($topics as $candidate) {
            if (preg_match('/\b' . preg_quote($candidate, '/') . '\b/i', $normalized) === 1) {
                $topic = $candidate;
                break;
            }
        }

        if ($topic === null) {
            return null;
        }

        return [
            'limit' => $limit,
            'topic' => $topic,
        ];
    }

    public function parseAuthorBooksIntent(string $message): ?array
    {
        $trimmed = trim($message);
        if (preg_match('/\bbooks?\s+by\s+(.+?)\s*[\?\.\!]*$/i', $trimmed, $m) !== 1) {
            return null;
        }

        $author = trim($m[1], " \t\n\r\0\x0B\"'");
        if ($author === '') {
            return null;
        }

        $limit = 3;
        if (preg_match('/\b(\d{1,2})\b/', strtolower($trimmed), $count) === 1) {
            $limit = max(1, min((int) $count[1], 10));
        }

        return [
            'author' => $author,
            'limit' => $limit,
        ];
    }

    public function isBookPriceQuery(string $message): bool
    {
        $normalized = strtolower(trim($message));
        $hasPriceWord = preg_match('/\b(price|cost|rate)\b/i', $normalized) === 1;
        $hasBookHint = preg_match('/\b(book|novel|title)\b/i', $normalized) === 1;

        return $hasPriceWord || ($hasBookHint && str_contains($normalized, 'how much'));
    }

    public function extractBookTitleFromPriceQuery(string $message): ?string
    {
        $trimmed = trim($message);

        if (preg_match('/(?:price|cost|rate)\s+of\s+["\']?(.+?)["\']?(?:\s+book)?[\?\.\!]*$/i', $trimmed, $m) === 1) {
            return trim($m[1], " \t\n\r\0\x0B\"'");
        }

        if (preg_match('/how\s+much\s+is\s+["\']?(.+?)["\']?(?:\s+book)?[\?\.\!]*$/i', $trimmed, $m) === 1) {
            return trim($m[1], " \t\n\r\0\x0B\"'");
        }

        return null;
    }
}
