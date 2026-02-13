<?php

declare(strict_types=1);

namespace Continuum\Service\Database;

use DateTimeImmutable;
use Psr\Cache\CacheItemPoolInterface;

final readonly class DatabaseDumpCache
{
    private const string KEY = 'app.db.backup_last_file';

    public function __construct(
        private CacheItemPoolInterface $cache,
    ) {}

    public function save(?DateTimeImmutable $lastBackupTime): void
    {
        $item = $this->cache->getItem(self::KEY);
        $item->set($lastBackupTime?->getTimestamp() ?? 0);
        $item->expiresAt(new DateTimeImmutable('+8 hours'));

        $this->cache->save($item);
    }

    public function get(): ?DateTimeImmutable
    {
        $item = $this->cache->getItem(self::KEY);

        if (!is_int($time = $item->get())) {
            return null;
        }

        return DateTimeImmutable::createFromTimestamp($time);
    }
}
