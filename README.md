# Kaikai

[![Latest Stable Version](https://img.shields.io/packagist/v/ntentan/kaikai.svg)](https://packagist.org/packages/ntentan/kaikai)
[![Total Downloads](https://img.shields.io/packagist/dt/ntentan/kaikai.svg)](https://packagist.org/packages/ntentan/kaikai)
[![License](https://img.shields.io/packagist/l/ntentan/kaikai.svg)](https://packagist.org/packages/ntentan/kaikai)
[![Tests](https://github.com/ntentan/kaikai/actions/workflows/tests.yml/badge.svg)](https://github.com/ntentan/kaikai/actions)

A flexible, lightweight caching library for PHP and the Ntentan framework. Kaikai provides a unified interface for caching data with built-in expiration controls, lazy generation (cache-aside pattern), and multiple interchangeable storage backends.

## Features

- **Unified API**: Consistent interface across various caching backends via `CacheBackendInterface`.
- **Flexible Expiration**: Support for time-to-live (TTL) in seconds or indefinite caching.
- **Cache-Aside / Factory Loading**: Lazy-load and cache items on-demand using callable factories in `read()`.
- **Multiple Storage Backends**:
  - **FileCache**: File-system storage with automatic TTL invalidation.
  - **RedisCache**: High-performance Redis caching supporting both `ext-redis` (`PhpRedisDriver`) and `predis/predis` (`PredisDriver`).
  - **VolatileCache**: In-memory array cache for request-scoped caching or testing.
- **Service Configuration**: Built-in `Cache::getService()` helper for dependency injection containers.

## Requirements

- PHP 8.2 or higher
- Optional dependencies:
  - `ext-redis` (for Redis caching with `PhpRedisDriver`)
  - `predis/predis` (for Redis caching with `PredisDriver`)

## Installation

Install via Composer:

```bash
composer require ntentan/kaikai
```

If you plan to use Redis with the Predis driver:

```bash
composer require predis/predis
```

## Basic Usage

The `Cache` class wraps any `CacheBackendInterface` implementation and provides a convenient API.

```php
use ntentan\kaikai\Cache;
use ntentan\kaikai\backends\FileCache;

// Initialize a cache backend
$backend = new FileCache('/path/to/cache/dir');

// Create the cache wrapper
$cache = new Cache($backend);

// Write data (cached indefinitely)
$cache->write('site_settings', ['theme' => 'dark']);

// Write data with a TTL in seconds (e.g., 1 hour)
$cache->write('weather_data', $weatherArray, 3600);

// Check if a key exists
if ($cache->exists('weather_data')) {
    // Read cached data
    $weather = $cache->read('weather_data');
}

// Delete an item
$cache->delete('weather_data');
```

### Lazy Loading with Factories

You can pass a callable factory to `read()`. If the item is not found or has expired, the factory is executed, its result is cached and returned:

```php
$userProfile = $cache->read('user_profile_42', function () use ($userId, $db) {
    // Expensive database query or API call
    return $db->findUserProfile($userId);
}, 1800); // Optional TTL: 30 minutes
```

## Available Backends

### 1. FileCache

Stores cache entries as serialized files inside a directory.

```php
use ntentan\kaikai\backends\FileCache;

// Specify directory path (defaults to 'cache' in the current working directory)
$backend = new FileCache(__DIR__ . '/var/cache');
```

### 2. RedisCache

Supports Redis via either `PhpRedisDriver` (PHP extension) or `PredisDriver` (pure PHP library), with configurable key prefixing.

#### Using PhpRedis (`ext-redis` extension)

```php
use Redis;
use ntentan\kaikai\backends\RedisCache;
use ntentan\kaikai\backends\redis\PhpRedisDriver;

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$driver = new PhpRedisDriver($redis);
$backend = new RedisCache($driver, 'app_prefix');
```

#### Using Predis (`predis/predis` package)

```php
use Predis\Client;
use ntentan\kaikai\backends\RedisCache;
use ntentan\kaikai\backends\redis\PredisDriver;

$client = new Client('tcp://127.0.0.1:6379');

$driver = new PredisDriver($client);
$backend = new RedisCache($driver, 'app_prefix');
```

### 3. VolatileCache

Keeps cached entries in PHP memory for the duration of the current process/request. Ideal for unit tests or per-request memoization.

```php
use ntentan\kaikai\backends\VolatileCache;

$backend = new VolatileCache();
```

## Service Configuration

Kaikai provides a convenience method `Cache::getService()` to generate dependency injection binding definitions for container configurations:

```php
use ntentan\kaikai\Cache;

// Returns [CacheBackendInterface::class => "\ntentan\kaikai\backends\FileCache"]
$bindings = Cache::getService(['driver' => 'file']);

// Returns [CacheBackendInterface::class => "\ntentan\kaikai\backends\RedisCache"]
$bindings = Cache::getService(['driver' => 'redis']);

// Returns [CacheBackendInterface::class => "\ntentan\kaikai\backends\VolatileCache"]
$bindings = Cache::getService(['driver' => 'volatile']);
```

## Running Tests

To run the test suite:

```bash
vendor/bin/phpunit -c tests/config.xml
```

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
