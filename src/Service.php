<?php

declare (strict_types=1);

namespace plugin\telegram;


use plugin\telegram\command\Hook;
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
        $this->commands([Hook::class]);
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
                'name' => '属性配置',
                'subs' => [
                    ['name' => 'Telegram属性', 'icon' => 'layui-icon layui-icon-read', 'node' => "{$code}/base.config/index"],
                ],
            ],
            [
                'name' => '频道管理',
                'subs' => [
                    ['name' => '运营素材频道', 'icon' => 'layui-icon layui-icon-read', 'node' => "{$code}/channel.item/index"],
                    ['name' => '网络素材频道', 'icon' => 'layui-icon layui-icon-read', 'node' => "{$code}/source.item/index"],
                ],
            ],
            [
                'name' => '素材资源',
                'subs' => [
                    ['name' => '频道素材资源', 'icon' => 'layui-icon layui-icon-read', 'node' => "{$code}/channel.resources/index"],
                    ['name' => '网络素材资源', 'icon' => 'layui-icon layui-icon-read', 'node' => "{$code}/source.resources/index"],
                ],
            ],
        ];
    }
}