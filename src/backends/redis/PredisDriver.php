<?php

namespace ntentan\kaikai\backends\redis;

use Predis\ClientInterface;

class PredisDriver implements Driver
{
    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function set(string $key, mixed $value, int $ttl): void
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
        $this->client->del([$key]);
    }

    public function clear(string $prefix): void
    {
        $cursor = '0';
        do {
            $response = $this->client->scan($cursor, ['MATCH' => "$prefix:*", 'COUNT' => 100]);
            $cursor = $response[0];
            $keys = $response[1];
            if (!empty($keys)) {
                $this->client->del($keys);
            }
        } while ($cursor !== '0');
    }
}