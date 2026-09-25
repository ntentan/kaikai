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
    /**
     * The filesystem path to the directory where cache files are stored.
     *
     * @var string
     */
    private string $path;

    /**
     * Temporarily holds the deserialized item structure retrieved during an existence check.
     *
     * @var array{expires: ?int, object: mixed}
     */
    private array $tempItem;

    /**
     * Creates a new FileCache backend instance.
     *
     * @param ?string $path Directory path for storing cache files (defaults to 'cache').
     */
    public function __construct(?string $path = null)
    {
        $this->path = $path ?? 'cache';
    }

    /**
     * Generates a filesystem path for a given cache key.
     *
     * @param string $key The unique key for the cached item.
     * @return string The file path corresponding to the cache key.
     */
    private function getPath(string $key): string
    {
        return "{$this->path}" . DIRECTORY_SEPARATOR . md5($key);
    }

    /**
     * Clears all cached items from the cache directory.
     *
     * @return void
     */
    public function clear(): void
    {
        $directory = new \DirectoryIterator($this->path);
        foreach ($directory as $file) {
            if ($file->getFilename() != '.' && $file->getFilename() != '..') {
                unlink($file->getPathname());
            }
        }
    }

    /**
     * Deletes an item from the cache by its key.
     *
     * @param string $key The unique key of the item to delete.
     * @return void
     */
    public function delete(string $key): void
    {
        unlink($this->getPath($key));
    }

    /**
     * Checks if an item exists in the cache and has not expired.
     *
     * @param string $key The unique key of the item to check.
     * @return bool True if the item exists and is still valid, false otherwise.
     */
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

    /**
     * Reads a value from the cache.
     *
     * @param string $key The unique key of the item to read.
     * @return mixed The cached value, or null if the item does not exist or has expired.
     */
    public function read(string $key): mixed
    {
        if ($this->exists($key)) {
            return $this->tempItem['object'];
        }
        return null;
    }

    /**
     * Writes a value to the cache with an optional time-to-live.
     *
     * @param string $key The unique key for the item to be written.
     * @param mixed $value The value to be cached.
     * @param ?int $ttl The lifetime of the item in seconds, or null for indefinite caching.
     * @return void
     */
    public function write(string $key, mixed $value, ?int $ttl): void
    {
        file_put_contents(
            $this->getPath($key),
            serialize(['expires' => $ttl === null ? null : $ttl + time(), 'object' => $value])
        );
    }
}
