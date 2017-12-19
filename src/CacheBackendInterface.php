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

    /**
     * Reads values from the cache.
     *
     * @param string $key The unique key of the item to be read.
     * @return mixed Returns null when item is not in the cache
     */
    public function read(string $key);

    /**
     * Checks if an item exists in the cache.
     *
     * @param string $key
     * @return bool
     */
    public function exists(string $key) : bool;

    /**
     * Deletes an item from the cache.
     *
     * @param string $key
     */
    public function delete(string $key) : void;

    /**
     * Clears the entire cache.
     */
    public function clear() : void;
}
