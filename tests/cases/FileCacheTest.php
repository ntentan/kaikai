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
    }

    public function getBackend()
    {
        return new FileCache(vfsStream::url('cache'));
    }

    public function testIndifiniteExpiration()
    {
        $this->cache->write('permanent', 'I persist', null);
        $this->assertEquals(true, $this->cache->exists('permanent'));
        $stored = file_get_contents(vfsStream::url('cache/' . md5('permanent')));
        $data = unserialize($stored);
        $this->assertEquals(null, $data['expires']);
    }
}