<?php
/**
 * Created by PhpStorm.
 * User: ekow
 * Date: 12/19/17
 * Time: 3:03 AM
 */

namespace ntentan\kaikai\tests\cases;


use ntentan\kaikai\backends\VolatileCache;
use ntentan\kaikai\tests\lib\BackendTest;

class VolatileCacheTest extends BackendTest
{

    public function getBackend()
    {
        return new VolatileCache();
    }

    public function testExpires()
    {
        $this->markTestSkipped();
    }
}