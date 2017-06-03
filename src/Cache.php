<?php

namespace ntentan\kaikai;

use ntentan\utils\Text;
use ntentan\panie\Container;

class Cache {

    private $backend;

    public function __construct(CacheBackendInterface $backend) {
        $this->backend = $backend;
    }

    public function write($key, $value) {
        $this->backend->write($key, $value);
    }

    public function read($key, $notExists = null) {
        $object =$this->backend->read($key);
        if ($object === false && is_callable($notExists)) {
            $object = $notExists();
            $this->write($key, $object);
        }
        return $object;
    }

    public function exists($key) {
        return $this->backend->exists($key);
    }

    public function delete($key) {
        return $this->backend->delete($key);
    }
    
    public static function getBackendClassName($backend) {
        return '\ntentan\kaikai\backends\\' . Text::ucamelize($backend) . 'Cache';
    }

}

