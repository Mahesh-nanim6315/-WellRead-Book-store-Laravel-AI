<?php

namespace App\Console\Commands;

use App\Services\DocumentIngestionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RuntimeException;

class IngestDocuments extends Command
{
    protected $signature = 'docs:ingest
        {path : File or directory path}
        {--provider= : Embedding provider}
        {--source=upload : Source type label (upload|catalog|manual)}';

    protected $description = 'Ingest PDF/DOCX/TXT documents into vector chunks';

    public function handle(DocumentIngestionService $ingestor): int
    {
        $path = (string) $this->argument('path');
        $provider = $this->option('provider') ?: null;
        $source = (string) $this->option('source');

        $files = $this->resolveFiles($path);
        if (empty($files)) {
            $this->error('No supported files found. Use pdf, docx, txt, or md.');
            return self::FAILURE;
        }

        $this->info('Found ' . count($files) . ' file(s) to ingest.');
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        $success = 0;
        foreach ($files as $file) {
            try {
                $result = $ingestor->ingest($file, $provider, $source);
                $success++;
                $this->line("\nIngested: {$file} [chunks={$result['chunks']}]");
            } catch (RuntimeException $e) {
                $this->error("\nSkipped {$file}: {$e->getMessage()}");
            } catch (\Throwable $e) {
                $this->error("\nFailed {$file}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Done. Success: {$success}/" . count($files));

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function resolveFiles(string $path): array
    {
        if (is_file($path)) {
            return $this->isSupported($path) ? [$path] : [];
        }

        if (! is_dir($path)) {
            return [];
        }

        $out = [];
        foreach (File::allFiles($path) as $file) {
            $real = $file->getRealPath();
            if (! is_string($real)) {
                continue;
            }
            if ($this->isSupported($real)) {
                $out[] = $real;
            }
        }

        sort($out);

        return $out;
    }

    private function isSupported(string $path): bool
    {
        $ext = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));
        $allowed = array_map(
            static fn ($v) => strtolower((string) $v),
            (array) config('ai.docs.supported_extensions', ['pdf', 'docx', 'txt', 'md'])
        );

        return in_array($ext, $allowed, true);
    }
}

