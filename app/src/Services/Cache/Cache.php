<?php

namespace App\Services\Cache;

use Predis\Client;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class Cache implements CacheInterface
{
    public function __construct()
    {
        $this->redis = new Client(
            [
                'scheme' => 'tcp',
                'host'   => $_ENV['REDIS_HOST'],
                'port'   => $_ENV['REDIS_PORT'],
                'password' => $_ENV['REDIS_PASSWORD']
            ]
        );
    }

    public function set($key, $value): bool|string
    {
        return $this->redis->set($key, serialize($value));
    }

    public function get($key)
    {
        return unserialize($this->redis->get($key));
    }

    public function push($key, $value): bool|int
    {
        return $this->redis->rPush($key, (array)serialize($value));
    }

    public function pop($key)
    {
        return unserialize($this->redis->lPop($key));
    }
}