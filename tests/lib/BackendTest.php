<?php

namespace ntentan\kaikai\tests\lib;


use ntentan\kaikai\Cache;
use PHPUnit\Framework\TestCase;

abstract class BackendTest extends TestCase
{
    private $cache;

    abstract public function getBackend();

    public function setUp()
    {
        $this->cache = $this->getBackend();
    }

    public function testReadWrite()
    {
        $this->cache->write('test', 'Hello!', 100);
        $this->assertEquals('Hello!', $this->cache->read('test'));
        $this->assertEquals(null, $this->cache->read('nonexistent'));

        $this->cache->write('object', new Dummy(), 100);
        $this->assertInstanceOf(Dummy::class, $this->cache->read('object'));
    }

    public function testExists()
    {
        $this->cache->write('test', 'Hello!', 100);
        $this->assertEquals(true, $this->cache->exists('test'));
        $this->assertEquals(false, $this->cache->exists('test2'));
    }

    public function testDelete()
    {
        $this->cache->write('test', 'Hello!', 100);
        $this->assertEquals(true, $this->cache->exists('test'));
        $this->cache->delete('test');
        $this->assertEquals(false, $this->cache->exists('test'));
    }

    public function testExpires()
    {
        $this->cache->write('expires', 'Should Expire', 10);
        $this->assertEquals(false, $this->cache->exists('expires'));
        $this->assertEquals(null, $this->cache->read('expires'));
    }

    public function testClear()
    {
        $this->cache->write('first', 'First item', 100);
        $this->cache->write('second', 'Second item', 100);
        $this->cache->clear();
        $this->assertEquals(false, $this->cache->exists('first'));
        $this->assertEquals(false, $this->cache->exists('second'));
    }
}
