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
        $this->cacheBackend->expects($this->once())->method('read')->with('greeting')->willReturn('Returned!');
        $this->cacheBackend->expects($this->never())->method('write');
        $cache = new Cache($this->cacheBackend);
        $this->assertEquals('Returned!', $cache->read('greeting'));
    }

    public function testReadExistingItemDoesNotInvokeFactory()
    {
        $this->cacheBackend->expects($this->once())->method('read')->with('greeting')->willReturn('Cached Value');
        $this->cacheBackend->expects($this->never())->method('write');
        $cache = new Cache($this->cacheBackend);

        $factoryCalled = false;
        $result = $cache->read('greeting', function() use (&$factoryCalled) {
            $factoryCalled = true;
            return 'New Value';
        });

        $this->assertEquals('Cached Value', $result);
        $this->assertFalse($factoryCalled);
    }

    public function testReadMissingItemReturnsNullWhenNoFactory()
    {
        $this->cacheBackend->expects($this->once())->method('read')->with('missing_key')->willReturn(null);
        $this->cacheBackend->expects($this->never())->method('write');
        $cache = new Cache($this->cacheBackend);

        $this->assertNull($cache->read('missing_key'));
    }

    public function testReadFunction()
    {
        $this->cacheBackend->expects($this->once())->method('read')->with('greeting')->willReturn(null);
        $this->cacheBackend->expects($this->once())->method('write')->with('greeting', 'Generated!', null);
        $cache = new Cache($this->cacheBackend);

        $factoryCalled = false;
        $result = $cache->read('greeting', function() use (&$factoryCalled) {
            $factoryCalled = true;
            return 'Generated!';
        });

        $this->assertEquals('Generated!', $result);
        $this->assertTrue($factoryCalled);
    }

    public function testReadMissingItemWithFactoryAndCustomTtl()
    {
        $this->cacheBackend->expects($this->once())->method('read')->with('greeting')->willReturn(null);
        $this->cacheBackend->expects($this->once())->method('write')->with('greeting', 'Generated with TTL!', 3600);
        $cache = new Cache($this->cacheBackend);

        $result = $cache->read('greeting', function() {
            return 'Generated with TTL!';
        }, 3600);

        $this->assertEquals('Generated with TTL!', $result);
    }

    public function testReadMissingItemFactoryReturnsNull()
    {
        $this->cacheBackend->expects($this->once())->method('read')->with('null_key')->willReturn(null);
        $this->cacheBackend->expects($this->once())->method('write')->with('null_key', null, 500);
        $cache = new Cache($this->cacheBackend);

        $result = $cache->read('null_key', function() {
            return null;
        }, 500);

        $this->assertNull($result);
    }

    public function testReadMissingItemWithNegativeTtlThrowsException()
    {
        $this->cacheBackend->expects($this->once())->method('read')->with('invalid_ttl')->willReturn(null);
        $this->cacheBackend->expects($this->never())->method('write');
        $cache = new Cache($this->cacheBackend);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('TTL for cache must be a positive integer');

        $cache->read('invalid_ttl', function() {
            return 'value';
        }, -10);
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

    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testGetService()
    {
        $drivers = ['file', 'volatile', 'redis'];
        foreach ($drivers as $driver) {
            $service = Cache::getService(['driver' => $driver]);
            $expectedClass = "\\ntentan\\kaikai\\backends\\" . ucfirst($driver) . "Cache";
            $this->assertArrayHasKey(CacheBackendInterface::class, $service);
            $this->assertEquals($expectedClass, $service[CacheBackendInterface::class]);
            $this->assertTrue(class_exists($service[CacheBackendInterface::class]));
            $this->assertTrue(is_subclass_of($service[CacheBackendInterface::class], CacheBackendInterface::class));
        }
    }
}
