<?php

declare(strict_types=1);

namespace Continuum\Service;

use DateTimeImmutable;
use Psr\Cache\CacheItemPoolInterface;

final readonly class DatabaseDumpCache
{
    private const string KEY = 'app.db.backup_last_file';

    public function __construct(
        private CacheItemPoolInterface $cache,
    ) {}

    public function save(string $backupPath): void
    {
        $item = $this->cache->getItem(self::KEY);
        $item->set($backupPath);
        $item->expiresAt(new DateTimeImmutable('+1 day'));

        $this->cache->save($item);
    }

    public function has(): bool
    {
        return $this->cache->hasItem(self::KEY);
    }
}
