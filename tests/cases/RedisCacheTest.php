<?php

namespace ntentan\kaikai\tests\cases;

use ntentan\kaikai\backends\RedisCache;
use ntentan\kaikai\backends\redis\PredisDriver;
use ntentan\kaikai\tests\lib\BackendTest;
use ntentan\kaikai\tests\lib\MockClient;

class RedisCacheTest extends BackendTest
{
    public function getBackend()
    {
        $client = $this->createMock(MockClient::class);
            
        // Simple in-memory storage for the mock
        $storage = [];

        $client->method('setex')->willReturnCallback(function($key, $ttl, $value) use (&$storage) {
            $storage[$key] = ['value' => $value, 'expires' => time() + $ttl];
        });

        $client->method('get')->willReturnCallback(function($key) use (&$storage) {
            if (isset($storage[$key]) && $storage[$key]['expires'] < time()) {
                unset($storage[$key]);
            }
            return $storage[$key]['value'] ?? null;
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
        });

        $client->method('scan')->willReturnCallback(function($cursor, $options) use (&$storage) {
            $match = $options['MATCH'] ?? '*';
            $pattern = str_replace(['*', '?'], ['.*', '.'], $match);
            $keys = array_keys($storage);
            $matchedKeys = array_filter($keys, function($key) use ($pattern) {
                return preg_match("/^$pattern$/", $key);
            });
            return ['0', array_values($matchedKeys)];
        });

        return new RedisCache(new PredisDriver($client), 'test_prefix');
    }
}
