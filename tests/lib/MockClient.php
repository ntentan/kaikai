<?php

namespace Predis {
    if (!interface_exists(ClientInterface::class)) {
        /**
         * @method mixed get(string $key)
         * @method void set(string $key, mixed $value)
         * @method void setex(string $key, int $ttl, mixed $value)
         * @method bool exists(string $key)
         * @method int del(array|string $keyOrKeys, ...$keys)
         * @method array scan(string &$cursor, ?array $options = null)
         */
        interface ClientInterface
        {
        }
    }
}

namespace ntentan\kaikai\tests\lib {

    use Predis\ClientInterface;

    interface MockClient extends ClientInterface
    {
        public function scan($cursor, ?array $options = null);
        public function del($keyOrKeys, ...$keys);
        public function setex($key, $ttl, $value);
        public function get($key);
        public function exists($key);
    }
}
