<?php

namespace ntentan\kaikai;

use ntentan\utils\Text;
use ntentan\config\Config;

class Cache
{
    private static $backendClass;
    
    /**
     * Initialize the caching engine.
     * Reads the current configuration to determine the caching backend to use.
     */
    public static function init()
    {
        $backend = Config::get('ntentan:cache.backend', 'volatile');
        self::$backendClass = '\ntentan\kaikai\backends\\' . Text::ucamelize($backend) . 'Cache';
    }
    
    /**
     * Returns an instance of the current caching backend.
     * 
     * @return \ntentan\kaikai\CacheBackendInterface
     */
    private static function getInstance()
    {
        return \ntentan\panie\InjectionContainer::singleton(self::$backendClass);
    }
    
    /**
     * Write to the currently initialized cache backend.
     * 
     * @param string $key
     * @param string $value
     */
    public static function write($key, $value)
    {
        self::getInstance()->write($key, $value);
    }
    
    public static function read($key, $notExists = null)
    {
        $object = self::getInstance()->read($key);
        if($object === false && is_callable($notExists)) {
            $object = $notExists();
            self::write($key, $object);
        }
        return $object;
    }
    
    public static function exists($key)
    {
        return self::getInstance()->exists($key);
    }
    
    public static function delete($key)
    {
        return self::getInstance()->delete($key);
    }
    
    public static function reset()
    {
        self::$backendObject = null;
    }
}
