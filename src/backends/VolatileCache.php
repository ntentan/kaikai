<?php

namespace ntentan\kaikai\backends;

class VolatileCache implements \ntentan\kaikai\CacheBackendInterface
{
    private $cache;
    public function clear($key)
    {
        $this->cache = [];
    }

    public function delete($key)
    {
        unset($this->cache[$key]);
    }

    public function exists($key)
    {
        isset($this->cache[$key]);
    }

    public function read($key)
    {
        return isset($this->cache[$key]) ? $this->cache[$key] : false;
    }

    public function write($key, $value)
    {
        $this->cache[$key] = $value;
    }
}
