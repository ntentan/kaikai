<?php

namespace ntentan\kaikai;

interface CacheBackendInterface
{
    public function write($key, $value);
    public function read($key);
    public function exists($key);
    public function delete($key);
    public function clear($key);
}
