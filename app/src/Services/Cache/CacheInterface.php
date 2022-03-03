<?php

namespace App\Services\Cache;

interface CacheInterface
{
    public function set($key, $value);

    public function get($key);

    public function push($key, $value);

    public function pop($key);
}