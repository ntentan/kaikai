CHANGELOG
=========
v0.7.4 - 2026-01-11
-------------------
- Fixed broken persistent storage of cache data in Redis caching backend when ttl is set to null.

v0.7.3 - 2026-01-11
-------------------
- Fixed broken persistent storage of cache data in File caching backend when ttl is set to null.

v0.7.2 - 2026-01-11
-------------------
- Fixed broken persistent storage of cache data in Redis and File caching backends when ttl is set to null.

v0.7.1 - 2026-01-10
-------------------
- Fixed broken tests for Redis caching backend.

v0.7.0 - 2026-01-10
-------------------
- Fixed minor bugs in how the cache expiration is handled in file caches.
- Implemented support for Redis caching backend.

v0.6.0 - 2025-01-20
--------------------
- First release with a changelog.