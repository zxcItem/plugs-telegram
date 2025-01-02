<?php

declare (strict_types=1);

namespace plugin\telegram;

use think\admin\Plugin;

/**
 * 组件注册服务
 * @class Service
 * @package plugin\telegram
 */
class Service extends Plugin
{
    /**
     * 定义插件名称
     * @var string
     */
    protected $appName = 'Telegram服务';

    /**
     * 定义安装包名
     * @var string
     */
    protected $package = 'xiaochao/plugs-telegram';


    /**
     * 增加微信配置
     * @return array[]
     */
    public static function menu(): array
    {
        $code = app(static::class)->appCode;
        // 设置插件菜单
        return [
            [
                'name' => 'Telegram服务',
                'subs' => [
                    ['name' => '用户管理', 'icon' => 'layui-icon layui-icon-user', 'node' => "{$code}/user/index"]
                ],
            ],
        ];
    }
}