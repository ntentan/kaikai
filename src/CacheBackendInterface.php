<?php

namespace ntentan\kaikai;

interface CacheBackendInterface
{
    public function write(string $key, $value, int $ttl) : void;
    public function read(string $key);
    public function exists(string $key) : bool;
    public function delete(string $key) : void;
    public function clear() : void;
}
