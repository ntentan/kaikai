<?php

namespace ntentan\kaikai\tests\cases;

use ntentan\kaikai\backends\redis\PhpRedisDriver;
use PHPUnit\Framework\TestCase;
use Redis;

class PhpRedisDriverTest extends TestCase
{
    public function testClear()
    {
        if (!class_exists('Redis')) {
            $this->markTestSkipped('Redis extension not installed');
        }
        $client = $this->createMock(Redis::class);

        $client->expects($this->exactly(3))
            ->method('scan')
            ->willReturnCallback(function(&$iterator, $pattern, $count) {
                if ($iterator === null) {
                    $iterator = 10;
                    return ['test:key1', 'test:key2'];
                } else if ($iterator === 10) {
                    $iterator = 0;
                    return ['test:key3'];
                } else if ($iterator === 0) {
                    return false;
                }
                return false;
            });

        $client->expects($this->exactly(2))
            ->method('del')
            ->willReturnCallback(function($keys) {
                static $callCount = 0;
                $callCount++;
                if ($callCount === 1) {
                    $this->assertEquals(['test:key1', 'test:key2'], $keys);
                } else if ($callCount === 2) {
                    $this->assertEquals(['test:key3'], $keys);
                }
                return 1;
            });

        $driver = new PhpRedisDriver($client);
        $driver->clear('test');
    }
}
