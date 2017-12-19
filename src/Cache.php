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
    private $backend;

    /**
     * The lenght of the default lifetime of items written to cache.
     *
     * @var int
     */
    private $defaultTtl = 3600;

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
     * Set the default value of the time items remain valid in cache.
     *
     * @param int $ttl
     */
    public function setDefaultTtl(int $ttl): void
    {
        $this->defaultTtl = $ttl;
    }

    /**
     * Write a value to the cache.
     *
     * @param string $key A unique key for the item to be written.
     * @param mixed $value
     * @param int|null $ttl
     */
    public function write(string $key, $value, int $ttl = null) : void
    {
        $this->backend->write($key, $value, $ttl ?? $this->defaultTtl);
    }

    /**
     * Read a value from the cache and optionally call a factory to create the item if it doesn't exist.
     *
     * @param string $key
     * @param callable|null $factory
     * @return mixed
     */
    public function read(string $key, callable $factory = null)
    {
        $object = $this->backend->read($key);
        if ($object === null) {
            $object = $factory();
            $this->write($key, $object);
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
