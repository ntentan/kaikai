<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ntentan\kaikai\backends;

/**
 * Description of VolatileCache
 *
 * @author ekow
 */
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
