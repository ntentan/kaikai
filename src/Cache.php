<?php

namespace ntentan\kaikai;

/**
 * A wrapper class for the cache backends that provides built in cache expiration control.
 *
 * @package ntentan\kaikai
 */
class Cache
{

    /**
     * The backend for the cache.
     *
     * @var CacheBackendInterface
     */
    private CacheBackendInterface $backend;

    /**
     * Creates a new Cache wrapper.
     *
     * @param CacheBackendInterface $backend
     */
    public function __construct(CacheBackendInterface $backend)
    {
        $this->backend = $backend;
    }

    /**
     * Write a value to the cache.
     *
     * @param string $key A unique key for the item to be written.
     * @param mixed $value The value to be written.
     * @param ?int $ttl An optional time to live for the item. Without this value or when this value is null, item will be
     *                  cached indefinitely.
     */
    public function write(string $key, mixed $value, ?int $ttl = null) : void
    {
        if ($ttl < 0) {
            throw new \InvalidArgumentException('TTL for cache must be a positive integer');
        }
        $this->backend->write($key, $value, $ttl);
    }

    /**
     * Read a value from the cache and optionally call a factory to create the item if it doesn't exist.
     *
     * @param string $key
     * @param callable|null $factory
     * @return mixed
     */
    public function read(string $key, ?callable $factory = null, ?int $ttl = null) : mixed
    {
        $object = $this->backend->read($key);
        if ($object === null) {
            $object = $factory();
            $this->write($key, $object, $ttl);
        }
        return $object;
    }

    /**
     * Checks if an item exists in the cache.
     *
     * @param string $key
     * @return bool
     */
    public function exists(string $key) : bool
    {
        return $this->backend->exists($key);
    }

    /**
     * Deletes an item from the cache.
     *
     * @param string $key
     */
    public function delete(string $key) : void
    {
        $this->backend->delete($key);
    }
}
