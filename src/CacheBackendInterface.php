<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ntentan\kaikai;

/**
 *
 * @author ekow
 */
interface CacheBackendInterface
{
    public function write($key, $value);
    public function read($key);
    public function exists($key);
    public function delete($key);
    public function clear($key);
}
