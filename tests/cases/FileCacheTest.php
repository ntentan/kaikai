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

    public function setUp()
    {
        parent::setUp();
        vfsStream::setup('cache');
        $this->time = $this->getFunctionMock('\ntentan\kaikai\backends\\', 'time');
        $this->time->expects($this->at(0))->willReturn(10000);
        $this->time->expects($this->at(1))->willReturn(10100);
    }

    public function getBackend()
    {
        return new FileCache(['path' => vfsStream::url('cache')]);
    }
}