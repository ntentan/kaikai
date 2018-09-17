<?php

namespace ntentan\kaikai\backends;

use ntentan\kaikai\CacheBackendInterface;

/**
 * A cache backend that holds its values for the lifetime of the session.
 *
 * @package ntentan\kaikai\backends
 */
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
        return $this->cache[$key] ?? null;
    }

    public function write(string $key, $value, int $ttl): void
    {
        $this->cache[$key] = $value;
    }
}
