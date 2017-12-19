<?php

namespace ntentan\kaikai\tests\cases;

use ntentan\kaikai\Cache;
use ntentan\kaikai\CacheBackendInterface;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{

    public function testWriteAndDefaultTtl()
    {
        $cacheBackend = $this->getMockBuilder(CacheBackendInterface::class)->getMock();
        $cacheBackend->expects($this->at(0))->method('write')->willReturnCallback(
            function($key, $value, $ttl) {
                $this->assertEquals('greeting', $key);
                $this->assertEquals('Hello World!', $value);
                $this->assertEquals(3600, $ttl);
            }
        );
        $cacheBackend->expects($this->at(1))->method('write')->willReturnCallback(
            function($key, $value, $ttl) {
                $this->assertEquals('greeting', $key);
                $this->assertEquals('Hello World!', $value);
                $this->assertEquals(100, $ttl);
            }
        );
        $cache = new Cache($cacheBackend);
        $cache->write('greeting', 'Hello World!');
        $cache->setDefaultTtl(100);
        $cache->write('greeting', 'Hello World!');
    }

    public function testRead()
    {
        $cacheBackend = $this->getMockBuilder(CacheBackendInterface::class)->getMock();
        $cacheBackend->expects($this->at(0))->method('read')->willReturnCallback(
            function($key) {
                $this->assertEquals('greeting', $key);
                return 'Returned!';
            }
        );
        $cacheBackend->expects($this->at(1))->method('read')->willReturnCallback(
            function($key) {
                $this->assertEquals('greeting', $key);
                return null;
            }
        );
        $cacheBackend->expects($this->at(2))->method('write')->willReturnCallback(
            function($key, $value, $ttl) {
                $this->assertEquals('greeting', $key);
                $this->assertEquals('From a function', $value);
                $this->assertEquals(3600, $ttl);
            }
        );
        $cache = new Cache($cacheBackend);
        $this->assertEquals('Returned!', $cache->read('greeting'));
        $this->assertEquals('From a function', $cache->read('greeting', function(){
            return 'From a function';
        }));
    }

    public function testExists()
    {
        $cacheBackend = $this->getMockBuilder(CacheBackendInterface::class)->getMock();
        $cacheBackend->expects($this->once())->method('exists')->willReturnCallback(
            function($key) {
                $this->assertEquals('some_key', $key);
                return true;
            }
        );
        $cache = new Cache($cacheBackend);
        $this->assertEquals(true, $cache->exists('some_key'));
    }

    public function testDelete()
    {
        $cacheBackend = $this->getMockBuilder(CacheBackendInterface::class)->getMock();
        $cacheBackend->expects($this->once())->method('delete')->willReturnCallback(
            function($key) {
                $this->assertEquals('some_key', $key);
            }
        );
        $cache = new Cache($cacheBackend);
        $this->assertEquals(null, $cache->delete('some_key'));

    }
}
