<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CacheService
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getOrSet(string $cacheKey, callable $callback, int $expiresAfter = 2): mixed
    {
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($callback, $expiresAfter) {
            $item->expiresAfter($expiresAfter);
            return $callback($item);
        });
    }

    public function clearAll(): bool
    {
        return $this->cache->clear();
    }
}