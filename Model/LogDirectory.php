<?php

declare(strict_types=1);

namespace WebSupportDesk\SystemLogs\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File as FileDriver;

/**
 * Safe access to files under var/log only (*.log, basename).
 */
class LogDirectory
{
    private const BASENAME_PATTERN = '/^[a-zA-Z0-9._-]+\.log$/';

    public function __construct(
        private readonly DirectoryList $directoryList,
        private readonly FileDriver $fileDriver
    ) {
    }

    public function getLogDir(): string
    {
        return $this->directoryList->getPath(DirectoryList::LOG);
    }

    /**
     * @return list<string> basenames, sorted
     */
    public function listLogBasenames(): array
    {
        $dir = $this->getLogDir();
        if (!$this->fileDriver->isExists($dir)) {
            return [];
        }

        $paths = glob($dir . '/*.log') ?: [];
        $names = array_map(static fn (string $p): string => basename($p), $paths);
        sort($names, SORT_STRING);

        return array_values(array_unique($names));
    }

    public function resolveValidatedPath(string $basename): ?string
    {
        $basename = basename($basename);
        if ($basename === '' || !preg_match(self::BASENAME_PATTERN, $basename)) {
            return null;
        }

        $path = $this->getLogDir() . '/' . $basename;
        if (!$this->fileDriver->isExists($path) || !$this->fileDriver->isFile($path)) {
            return null;
        }

        $realLog = realpath($this->getLogDir());
        $realFile = realpath($path);
        if ($realLog === false || $realFile === false) {
            return null;
        }

        if (!str_starts_with($realFile, $realLog . DIRECTORY_SEPARATOR) && $realFile !== $realLog) {
            return null;
        }

        return $path;
    }

    /**
     * Truncate log file to empty.
     */
    public function clear(string $basename): void
    {
        $path = $this->resolveValidatedPath($basename);
        if ($path === null) {
            throw new \InvalidArgumentException('Invalid or missing log file.');
        }

        $this->fileDriver->filePutContents($path, '');
    }
}
