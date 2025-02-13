<?php

declare (strict_types=1);

namespace plugin\telegram\service;

use think\admin\Service;
use think\cache\driver\Redis;

/**
 * redis
 * Class RedisService
 * @package plugin\telegram\service
 */
class RedisService extends Service
{
    protected $redis;

    /**
     *初始化
     */
    protected function initialize()
    {
        $this->redis = new Redis([
            // 驱动方式
            'type'     => 'redis',
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'port'     => env('REDIS_PORT', 6379),
            'select'   => env('REDIS_SELECT', 0),
            'password' => env('REDIS_PASSWORD', ''),
        ]);
    }

    /**
     * 保存
     * @param $key
     * @param $value
     * @param int $expiry
     * @return
     */
    public function set($key, $value, $expiry = 0)
    {
        return $this->redis->set($key, $value, $expiry);
    }

    /**
     * 获取
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->redis->get($key);
    }
}