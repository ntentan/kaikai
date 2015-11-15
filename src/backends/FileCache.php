<?php

namespace ntentan\kaikai\backends;

class FileCache implements \ntentan\kaikai\CacheBackendInterface
{
    private $path;
    private $ttl = 3600;
    
    public function __construct($options)
    {
        $this->path = isset($options['path']) ? $options['path'] : 'cache';
    }
    
    private function getPath($key)
    {
        return "{$this->path}" . DIRECTORY_SEPARATOR . md5($key);
    }
    
    public function clear($key)
    {
        $directory = new \DirectoryIterator($this->path);
        foreach($directory as $file) {
            unlink($file->getPath());
        }
    }

    public function delete($key)
    {
        unlink($this->getPath($key));
    }

    public function exists($key)
    {
        return file_exists($this->getPath($key));
    }

    public function read($key)
    {
        if($this->exists($key)) {
            $item = unserialize(file_get_contents($this->getPath($key)));
            if($item['expires'] < time()) {
                $this->delete($key);
                return false;
            }
            return $item['object'];
        }
        return false;
    }

    public function write($key, $value)
    {
        file_put_contents($this->getPath($key), 
            serialize([
                'expires' => time() + $this->ttl,
                'object' => $value
            ])
        );
    }
}
