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

    public function setDefaultTtl($ttl)
    {
        $this->defaultTtl = $ttl;
    }

    public function write($key, $value, $ttl = null)
    {
        $this->backend->write($key, $value, $ttl ?? $this->defaultTtl);
    }

    public function read($key, $factory = null)
    {
        $object = $this->backend->read($key);
        if ($object === null && is_callable($factory)) {
            $object = $factory();
            $this->write($key, $object);
        }
        return $object;
    }

    public function exists($key)
    {
        return $this->backend->exists($key);
    }

    public function delete($key)
    {
        return $this->backend->delete($key);
    }

}
