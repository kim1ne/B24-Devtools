<?php

namespace B24\Devtools\Cache;

use Bitrix\Main\Data\Cache;

class CacheFile implements CacheInterface
{
    private Cache $cache;
    private int $ttl = 3600;

    public function __construct(
        private readonly string $cachePath = 'b24.devtools'
    )
    {
        $this->cache = Cache::createInstance();
    }

    public function setTtl(int $ttl): void
    {
        $this->ttl = $ttl;
    }

    public function getData(string $cacheKey): bool|array
    {
        if ($this->cache->initCache($this->ttl, $cacheKey, $this->cachePath)) {
            return $this->cache->getVars();
        }

        return false;
    }

    public function write(string $cacheKey, $value): bool|array
    {
        if ($this->cache->startDataCache($this->ttl, $cacheKey, $this->cachePath)) {

            $vars = [
                'value' => $value
            ];


            $this->cache->endDataCache($vars);
        }

        return $vars ?? false;
    }

    public function clean(string $cacheKey): void
    {
        $this->cache->clean($cacheKey, $this->cachePath);
    }
}