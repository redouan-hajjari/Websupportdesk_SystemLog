<?php

declare(strict_types=1);

namespace WebSupportDesk\SystemLogs\Model;

class LogReader
{
    public function __construct(
        private readonly LogDirectory $logDirectory
    ) {
    }

    /**
     * Full file contents (validated path under var/log only).
     */
    public function readFull(string $basename): string
    {
        $path = $this->logDirectory->resolveValidatedPath($basename);
        if ($path === null) {
            return '';
        }

        $contents = @file_get_contents($path);

        return $contents !== false ? $contents : '';
    }

    public function getFileSize(string $basename): int
    {
        $path = $this->logDirectory->resolveValidatedPath($basename);
        if ($path === null) {
            return 0;
        }

        $size = @filesize($path);

        return $size !== false ? (int)$size : 0;
    }
}
