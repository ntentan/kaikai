<?php

namespace ntentan\kaikai\backends;

use ntentan\kaikai\CacheBackendInterface;

/**
 * A cache backend that stores its items in a directory.
 *
 * @package ntentan\kaikai\backends
 */
class FileCache implements CacheBackendInterface
{
    private $path;

    private $tempItem;

    public function __construct($options = null)
    {
        $this->path = isset($options['path']) ? $options['path'] : 'cache';
    }

    private function getPath($key)
    {
        return "{$this->path}" . DIRECTORY_SEPARATOR . md5($key);
    }

    public function clear(): void
    {
        $directory = new \DirectoryIterator($this->path);
        foreach ($directory as $file) {
            if ($file->getFilename() != '.' && $file->getFilename() != '..') {
                unlink($file->getPathname());
            }
        }
    }

    public function delete(string $key): void
    {
        unlink($this->getPath($key));
    }

    public function exists(string $key): bool
    {
        if (file_exists($this->getPath($key))) {
            $this->tempItem = unserialize(file_get_contents($this->getPath($key)));
            if ($this->tempItem['expires'] < time()) {
                $this->delete($key);
                return false;
            }
            return true;
        }
        return false;
    }

    public function read(string $key)
    {
        if ($this->exists($key)) {
            return $this->tempItem['object'];
        }
        return null;
    }

    public function write(string $key, $value, int $ttl): void
    {
        file_put_contents($this->getPath($key), serialize(['expires' => time() + $ttl, 'object' => $value]));
    }
}
