<?php

namespace B24\Devtools\Cache;

class CacheMapper
{
    public int $ttl = 3600;

    public function __construct(
        private readonly CacheInterface $cacheHelper = new CacheFile()
    ) {}

    public function get(string $cacheName, callable $callback)
    {
        $data = $this->cacheHelper->getData($cacheName);

        if ($data === false) {
            $data = $callback($this);
            $this->cacheHelper->setTtl($this->ttl);
            $this->cacheHelper->write($cacheName, $data);
        } else {
            $data = $data['value'];
        }

        return $data;
    }
}