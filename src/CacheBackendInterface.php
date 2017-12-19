<?php

namespace ntentan\kaikai;

/**
 * Interface for cache backend drivers.
 *
 * @package ntentan\kaikai
 */
interface CacheBackendInterface
{
    /**
     * Writes data to the cache.
     *
     * @param string $key A unique key for the value to be written.
     * @param mixed $value Value to be written.
     * @param int $ttl The lifetime of items in the cache.
     * @return void
     */
    public function write(string $key, $value, int $ttl) : void;
    public function read(string $key);
    public function exists(string $key) : bool;
    public function delete(string $key) : void;
    public function clear() : void;
}
