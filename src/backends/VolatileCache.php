<?php

namespace ntentan\kaikai\backends;

use ntentan\kaikai\CacheBackendInterface;

class VolatileCache implements CacheBackendInterface
{
    private $cache;
    public function clear(): void
    {
        $this->cache = [];
    }

    public function delete(string $key): void
    {
        unset($this->cache[$key]);
    }

    public function exists(string $key): bool
    {
        return isset($this->cache[$key]);
    }

    public function read(string $key)
    {
        return isset($this->cache[$key]) ? $this->cache[$key] : false;
    }

    public function write(string $key, $value, int $ttl): void
    {
        $this->cache[$key] = $value;
    }
}
