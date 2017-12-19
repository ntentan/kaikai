<?php

namespace ntentan\kaikai;

class Cache
{

    private $backend;
    private $defaultTtl = 3600;

    public function __construct(CacheBackendInterface $backend)
    {
        $this->backend = $backend;
    }

    /**
     * @param int $ttl
     */
    public function setDefaultTtl(int $ttl): void
    {
        $this->defaultTtl = $ttl;
    }

    public function write(string $key, $value, int $ttl = null) : void
    {
        $this->backend->write($key, $value, $ttl ?? $this->defaultTtl);
    }

    public function read(string $key, callable $factory = null)
    {
        $object = $this->backend->read($key);
        if ($object === null) {
            $object = $factory();
            $this->write($key, $object);
        }
        return $object;
    }

    public function exists(string $key) : bool
    {
        return $this->backend->exists($key);
    }

    public function delete(string $key) : void
    {
        $this->backend->delete($key);
    }

}
