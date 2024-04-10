<?php

namespace B24\Devtools\Cache;

interface CacheInterface
{
    public function getData(string $cacheKey): bool|array;

    public function setTtl(int $ttl): void;

    public function write(string $cacheKey, $value): bool|array;
}