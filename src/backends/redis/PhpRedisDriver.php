<?php

namespace ntentan\kaikai\backends\redis;

use Redis;

class PhpRedisDriver implements Driver
{
    private Redis $client;

    public function __construct(Redis $client)
    {
        $this->client = $client;
    }

    public function setex(string $key, mixed $value, int $ttl): void
    {
        $this->client->setex($key, $ttl, $value);
    }

    public function get(string $key): mixed
    {
        return $this->client->get($key);
    }

    public function exists(string $key): bool
    {
        return $this->client->exists($key);
    }

    public function delete(string $key): void
    {
        $this->client->del($key);
    }

    public function clear(string $prefix): void
    {
        $iterator = null;
        while (false !== ($keys = $this->client->scan($iterator, "$prefix:*", 100))) {
            $this->client->del($keys);
        }
    }

    public function set(string $key, mixed $value): void
    {
        $this->client->set($key, $value);
    }
}