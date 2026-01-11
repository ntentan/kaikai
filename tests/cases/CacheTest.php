<?php

namespace ntentan\kaikai\tests\cases;

use ntentan\kaikai\Cache;
use ntentan\kaikai\CacheBackendInterface;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    private $cacheBackend;

    public function setUp(): void
    {
        parent::setUp();
        $this->cacheBackend = $this->getMockBuilder(CacheBackendInterface::class)->getMock();
    }

    public function testWriteAndDefaultTtl()
    {
        $this->cacheBackend->expects($this->once())->method('write')->willReturnCallback(
            function($key, $value, $ttl) {
                $this->assertEquals('greeting', $key);
                $this->assertEquals('Hello World!', $value);
                $this->assertEquals(null, $ttl);
            }
        );
        $cache = new Cache($this->cacheBackend);
        $cache->write('greeting', 'Hello World!');
    }

    public function testWriteAndCustomTtl()
    {
        $this->cacheBackend->expects($this->once())->method('write')->willReturnCallback(
            function($key, $value, $ttl) {
                $this->assertEquals('greeting', $key);
                $this->assertEquals('Hello World!', $value);
                $this->assertEquals(null, $ttl);
            }
        );
        $cache = new Cache($this->cacheBackend);
        $cache->write('greeting', 'Hello World!');
    }

    public function testRead()
    {
        $this->cacheBackend->expects($this->once())->method('read')->willReturnCallback(
            function ($key) {
                $this->assertEquals('greeting', $key);
                return 'Returned!';
            }
        );
        $cache = new Cache($this->cacheBackend);
        $this->assertEquals('Returned!', $cache->read('greeting'));
    }

    public function testReadFunction()
    {
        $this->cacheBackend->expects($this->once())->method('read')->willReturnCallback(
            function($key) {
                $this->assertEquals('greeting', $key);
                return 'Returned!';
            }
        );
        $cache = new Cache($this->cacheBackend);
        $this->assertEquals('Returned!', $cache->read('greeting'));
    }


    public function testExists()
    {
        $this->cacheBackend->expects($this->once())->method('exists')->willReturnCallback(
            function($key) {
                $this->assertEquals('some_key', $key);
                return true;
            }
        );
        $cache = new Cache($this->cacheBackend);
        $this->assertEquals(true, $cache->exists('some_key'));
    }

    public function testDelete()
    {
        $this->cacheBackend->expects($this->once())->method('delete')->willReturnCallback(
            function($key) {
                $this->assertEquals('some_key', $key);
            }
        );
        $cache = new Cache($this->cacheBackend);
        $cache->delete('some_key');
    }
}
