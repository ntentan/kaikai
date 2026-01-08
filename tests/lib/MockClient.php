<?php

namespace ntentan\kaikai\tests\lib;

use Predis\ClientInterface;

interface MockClient extends ClientInterface
{
    public function scan($cursor, ?array $options = null);
    public function del($keyOrKeys, ...$keys);
    public function setex($key, $ttl, $value);
    public function get($key);
    public function exists($key);
}
