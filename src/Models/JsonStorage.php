<?php

declare(strict_types=1);

namespace App\Models;

class JsonStorage
{
    private string $dataDirectory;

    public function __construct(string $dataDirectory)
    {
        $this->dataDirectory = rtrim($dataDirectory, DIRECTORY_SEPARATOR);
    }

    public function loadAll(string $filename): array
    {
        $path = $this->dataDirectory . DIRECTORY_SEPARATOR . $filename;
        if (!is_file($path) || !is_readable($path)) {
            return [];
        }

        $raw = file_get_contents($path);
        if ($raw === false || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return [];
        }

        return $decoded;
    }

    public function saveAll(string $filename, array $records): bool
    {
        if (!is_dir($this->dataDirectory)) {
            if (!mkdir($this->dataDirectory, 0775, true) && !is_dir($this->dataDirectory)) {
                return false;
            }
        }

        $path = $this->dataDirectory . DIRECTORY_SEPARATOR . $filename;
        if (file_exists($path) && !is_writable($path)) {
            return false;
        }
        if (!file_exists($path) && !is_writable($this->dataDirectory)) {
            return false;
        }

        $encoded = json_encode(
            array_values($records),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
        if ($encoded === false) {
            return false;
        }

        return file_put_contents($path, $encoded . PHP_EOL, LOCK_EX) !== false;
    }
}

