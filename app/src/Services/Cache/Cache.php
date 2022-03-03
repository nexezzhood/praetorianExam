<?php

namespace App\Services\Cache;

use Symfony\Component\Cache\Adapter\RedisAdapter;

class Cache implements CacheInterface
{
    public function __construct()
    {
        $this->redis = RedisAdapter::createConnection(
            'redis://' . $_ENV['REDIS_PASSWORD'] . '@' . $_ENV['REDIS_HOST'] . ':' . $_ENV['REDIS_PORT']
        );
    }

    public function set($key, $value): bool
    {
        return $this->redis->set($key, serialize($value));
    }

    public function get($key)
    {
        return unserialize($this->redis->get($key));
    }

    public function push($key, $value): bool|int
    {
        return $this->redis->rPush($key, serialize($value));
    }

    public function pop($key)
    {
        return unserialize($this->redis->lPop($key));
    }
}