<?php

namespace ntentan\kaikai\backends;
use ntentan\kaikai\CacheBackendInterface;

class RedisCache implements CacheBackendInterface
{
    private redis\Driver $driver;

    private string $prefix;

    public function __construct(redis\Driver $driver, string $prefix = 'cache')
    {
        $this->driver = $driver;
        $this->prefix = $prefix;
    }

    private function getKey(string $key): string
    {
        return "{$this->prefix}:{$key}";
    }

    public function write(string $key, mixed $value, ?int $ttl): void
    {
        if ($ttl === null) {
            $this->driver->set($this->getKey($key), serialize($value));
        } else {
            $this->driver->setex($this->getKey($key), serialize($value), $ttl);
        }
    }

    public function read(string $key): mixed
    {
        $data = $this->driver->get($this->getKey($key));
        if ($data === null) {
            return null;
        }
        return unserialize($this->driver->get($this->getKey($key)));
    }

    public function exists(string $key): bool
    {
        return $this->driver->exists($this->getKey($key));
    }

    public function delete(string $key): void
    {
        $this->driver->delete($this->getKey($key));
    }

    public function clear(): void
    {
        $this->driver->clear($this->prefix);
    }
}