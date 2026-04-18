<?php

declare(strict_types=1);

namespace WebSupportDesk\SystemLogs\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Persists per-log metadata (dates) in var/.websupportdesk_systemlogs.json
 * and removes entries when the file no longer exists under var/log.
 */
class LogFileRegistry
{
    private const STATE_FILENAME = '.websupportdesk_systemlogs.json';

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly LogDirectory $logDirectory,
        private readonly Json $json
    ) {
    }

    /**
     * @return array{files: array<string, array{first_seen?: string, last_seen?: string, last_cleared_at?: string|null}>}
     */
    private function loadState(): array
    {
        $dir = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
        if (!$dir->isExist(self::STATE_FILENAME)) {
            return ['files' => []];
        }

        try {
            $raw = $dir->readFile(self::STATE_FILENAME);
        } catch (\Throwable) {
            return ['files' => []];
        }

        try {
            $data = $this->json->unserialize($raw);
        } catch (\InvalidArgumentException) {
            return ['files' => []];
        }

        if (!is_array($data) || !isset($data['files']) || !is_array($data['files'])) {
            return ['files' => []];
        }

        return ['files' => $data['files']];
    }

    /**
     * @param array{files: array<string, mixed>} $state
     */
    private function saveState(array $state): void
    {
        $payload = $this->json->serialize($state) . "\n";
        $dir = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $dir->writeFile(self::STATE_FILENAME, $payload);
    }

    private function nowIso(): string
    {
        return (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format(\DateTimeInterface::ATOM);
    }

    /**
     * Sync filesystem with state: drop missing files, add/update dates for existing logs.
     *
     * @return list<array{name: string, first_seen: string, last_seen: string, last_cleared_at: ?string}>
     */
    public function syncAndDescribe(): array
    {
        $onDisk = $this->logDirectory->listLogBasenames();
        $state = $this->loadState();
        $files = $state['files'];
        $now = $this->nowIso();

        foreach (array_keys($files) as $name) {
            if (!in_array($name, $onDisk, true)) {
                unset($files[$name]);
            }
        }

        foreach ($onDisk as $name) {
            if (!isset($files[$name])) {
                $files[$name] = [
                    'first_seen' => $now,
                    'last_seen' => $now,
                    'last_cleared_at' => null,
                ];
            } else {
                $files[$name]['last_seen'] = $now;
                if (!isset($files[$name]['first_seen'])) {
                    $files[$name]['first_seen'] = $now;
                }
                if (!array_key_exists('last_cleared_at', $files[$name])) {
                    $files[$name]['last_cleared_at'] = null;
                }
            }
        }

        $this->saveState(['files' => $files]);

        $out = [];
        foreach ($onDisk as $name) {
            $meta = $files[$name] ?? [];
            $out[] = [
                'name' => $name,
                'first_seen' => (string)($meta['first_seen'] ?? $now),
                'last_seen' => (string)($meta['last_seen'] ?? $now),
                'last_cleared_at' => isset($meta['last_cleared_at']) && $meta['last_cleared_at'] !== null
                    ? (string)$meta['last_cleared_at']
                    : null,
            ];
        }

        return $out;
    }

    /**
     * Call after truncating a log file in admin.
     */
    public function markCleared(string $basename): void
    {
        $basename = basename($basename);
        $state = $this->loadState();
        $now = $this->nowIso();

        if (!isset($state['files'][$basename])) {
            $state['files'][$basename] = [
                'first_seen' => $now,
                'last_seen' => $now,
                'last_cleared_at' => $now,
            ];
        } else {
            $state['files'][$basename]['last_cleared_at'] = $now;
            $state['files'][$basename]['last_seen'] = $now;
        }

        $this->saveState($state);
    }
}
