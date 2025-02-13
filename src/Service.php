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
     * 插件服务注册
     * @return void
     */
    public function register(): void
    {
        $this->commands([]);
    }

    /**
     * 增加Telegram服务配置
     * @return array[]
     */
    public static function menu(): array
    {
        $code = self::getAppCode();
        // 设置插件菜单
        return [

            [
                'name' => '内容管理',
                'subs' => [
                    ['name' => '内容管理', 'icon' => 'layui-icon layui-icon-read', 'node' => "{$code}/content.content/index"],
                    ['name' => '素材管理', 'icon' => 'layui-icon layui-icon-read', 'node' => "{$code}/source.content/index"],
                ],
            ],
        ];
    }
}