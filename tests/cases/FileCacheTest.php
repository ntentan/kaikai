<?php

namespace ntentan\kaikai\tests\cases;


use ntentan\kaikai\backends\FileCache;
use ntentan\kaikai\tests\lib\BackendTest;
use org\bovigo\vfs\vfsStream;
use phpmock\phpunit\PHPMock;

class FileCacheTest extends BackendTest
{
    use PHPMock;

    private $time;

    public function setUp(): void
    {
        parent::setUp();
        vfsStream::setup('cache');
//        $this->time = $this->getFunctionMock('\ntentan\kaikai\backends\\', 'time');
//        $this->time->expects($this->once(0))->willReturn(10000);
//        $this->time->expects($this->once(1))->willReturn(10100);
    }

    public function getBackend()
    {
        return new FileCache(vfsStream::url('cache'));
    }
}