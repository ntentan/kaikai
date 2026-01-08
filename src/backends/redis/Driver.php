<?php

namespace ntentan\kaikai\backends\redis;

interface Driver
{
    public function set(string $key, mixed $value, int $ttl): void;
    public function get(string $key): mixed;
    public function exists(string $key): bool;
    public function delete(string $key): void;
    public function clear(string $prefix): void;
}