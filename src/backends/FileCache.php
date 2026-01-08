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
    private string $path;

    private array $tempItem;

    public function __construct(?string $path = null)
    {
        $this->path = $path ?? 'cache';
    }

    private function getPath(string $key): string
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
            if ($this->tempItem['expires'] != null && $this->tempItem['expires'] < time()) {
                $this->delete($key);
                return false;
            }
            return true;
        }
        return false;
    }

    public function read(string $key): mixed
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
