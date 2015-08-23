<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ntentan\kaikai;

use ntentan\utils\Text;
use ntentan\utils\Utils;

/**
 * Description of Cache
 *
 * @author ekow
 */
class Cache
{
    private $options;
    private $backendClass = '\ntentan\kaikai\backends\VolatileCache';
    private $backendObject;
    
    public static function init($config)
    {
        $this->backendClass = '\ntentan\kaikai\backends\\' . Text::ucamelize($config['backend']) . 'Cache';
        $this->options = $config;
    }
    
    /**
     * 
     * @return \ntentan\kaikai\CacheBackendInterface
     */
    private static function getInstance()
    {
        return Utils::factory($this->backendObject, 
            function(){
                $class = $this->backendClass;
                return new $class($this->options);
            }
        );
    }
    
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
}
