<?php

namespace ntentan\kaikai\tests\cases;

use ntentan\kaikai\backends\redis\PredisDriver;
use ntentan\kaikai\tests\lib\MockClient;
use PHPUnit\Framework\TestCase;

class PredisDriverTest extends TestCase
{
    public function testClear()
    {
        $client = $this->createMock(MockClient::class);

        $client->expects($this->exactly(2))
            ->method('scan')
            ->willReturnCallback(function($cursor, $options) {
                if ($cursor === '0') {
                    return ['10', ['test:key1', 'test:key2']];
                } else if ($cursor === '10') {
                    return ['0', ['test:key3']];
                }
                return ['0', []];
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
            });

        $driver = new PredisDriver($client);
        $driver->clear('test');
    }
}
