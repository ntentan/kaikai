<?php

namespace ntentan\kaikai\tests\cases;

use ntentan\kaikai\backends\RedisCache;
use ntentan\kaikai\backends\redis\PhpRedisDriver;
use ntentan\kaikai\tests\lib\BackendTest;
use Redis;

class PhpRedisCacheTest extends BackendTest
{
    public function getBackend()
    {
        if (!class_exists('Redis')) {
            $this->markTestSkipped('Redis extension not installed');
        }
        $client = $this->createMock(Redis::class);
            
        // Simple in-memory storage for the mock
        $storage = [];

        $client->method('setex')->willReturnCallback(function($key, $ttl, $value) use (&$storage) {
            $storage[$key] = ['value' => $value, 'expires' => time() + $ttl];
        });

        $client->method('get')->willReturnCallback(function($key) use (&$storage) {
            if (isset($storage[$key]) && $storage[$key]['expires'] < time()) {
                unset($storage[$key]);
            }
            return $storage[$key]['value'] ?? false; // PhpRedis returns false if key doesn't exist
        });

        $client->method('exists')->willReturnCallback(function($key) use (&$storage) {
            if (isset($storage[$key]) && $storage[$key]['expires'] < time()) {
                unset($storage[$key]);
            }
            return isset($storage[$key]);
        });

        $client->method('del')->willReturnCallback(function($keys) use (&$storage) {
            foreach ((array)$keys as $key) {
                unset($storage[$key]);
            }
            return 1;
        });

        $client->method('scan')->willReturnCallback(function(&$iterator, $pattern, $count) use (&$storage) {
            if ($iterator === 0) {
                return false;
            }
            $regexPattern = str_replace(['*', '?'], ['.*', '.'], $pattern);
            $keys = array_keys($storage);
            $matchedKeys = array_filter($keys, function($key) use ($regexPattern) {
                return preg_match("/^$regexPattern$/", $key);
            });
            $iterator = 0;
            return array_values($matchedKeys);
        });

        return new RedisCache(new PhpRedisDriver($client), 'test_prefix');
    }
}
