<?php

namespace App\Services;

use RuntimeException;
use ZipArchive;

class DocumentExtractorService
{
    /**
     * @return array<int, array{page_no:int|null,text:string}>
     */
    public function extract(string $path): array
    {
        if (! is_file($path)) {
            throw new RuntimeException("File not found: {$path}");
        }

        $ext = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));

        return match ($ext) {
            'pdf' => $this->extractPdf($path),
            'docx' => $this->extractDocx($path),
            'txt', 'md' => $this->extractPlainText($path),
            default => throw new RuntimeException("Unsupported file type: .{$ext}"),
        };
    }

    /**
     * @return array<int, array{page_no:int|null,text:string}>
     */
    private function extractPdf(string $path): array
    {
        if (! class_exists(\Smalot\PdfParser\Parser::class)) {
            throw new RuntimeException('smalot/pdfparser is required for PDF ingestion.');
        }

        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($path);
        $pages = $pdf->getPages();

        $rows = [];
        foreach ($pages as $i => $page) {
            $text = trim((string) $page->getText());
            if ($text === '') {
                continue;
            }
            $rows[] = [
                'page_no' => $i + 1,
                'text' => $this->normalizeWhitespace($text),
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array{page_no:int|null,text:string}>
     */
    private function extractDocx(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException("Unable to open DOCX: {$path}");
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (! is_string($xml) || $xml === '') {
            throw new RuntimeException('DOCX body is empty or unreadable.');
        }

        $text = strip_tags($xml);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $text = $this->normalizeWhitespace($text);

        if ($text === '') {
            return [];
        }

        return [[
            'page_no' => null,
            'text' => $text,
        ]];
    }

    /**
     * @return array<int, array{page_no:int|null,text:string}>
     */
    private function extractPlainText(string $path): array
    {
        $text = file_get_contents($path);
        if (! is_string($text)) {
            throw new RuntimeException("Unable to read text file: {$path}");
        }

        $text = $this->normalizeWhitespace($text);
        if ($text === '') {
            return [];
        }

        return [[
            'page_no' => null,
            'text' => $text,
        ]];
    }

    private function normalizeWhitespace(string $text): string
    {
        $text = preg_replace("/\r\n|\r/", "\n", $text) ?? $text;
        $text = preg_replace('/[ \t]+/', ' ', $text) ?? $text;
        $text = preg_replace('/\n{3,}/', "\n\n", $text) ?? $text;

        return trim($text);
    }
}

