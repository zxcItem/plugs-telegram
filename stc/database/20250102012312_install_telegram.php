<?php

use think\admin\extend\PhinxExtend;
use think\migration\Migrator;

class InstallTelegram extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->_create_plugin_telegram_channel();
        $this->_create_plugin_telegram_channel_source();
        $this->_create_plugin_telegram_channel_resources();
        $this->_create_plugin_telegram_source_resources();
        $this->_create_plugin_telegram_resources_media();
        $this->_create_plugin_telegram_resources_link();
    }

    /**
     * 发布素材资源管理
     * @class PluginTelegramChannelResources
     * @table plugin_telegram_channel_resources
     * @return void
     */
    private function _create_plugin_telegram_channel_resources()
    {
        // 创建数据表对象
        $table = $this->table('plugin_telegram_channel_resources', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '发布素材资源管理',
        ]);
        PhinxExtend::upgrade($table, [
            ['channel_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '归档频道ID']],
            ['source_channel_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '来源频道ID']],
            ['message_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '频道消息ID']],
            ['media_group_id', 'string', ['limit' => 32,'default' => 0, 'null' => true, 'comment' => '组合消息ID']],
            ['caption', 'text', ['default' => NULL, 'null' => true, 'comment' => '消息内容']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '状态(0未处理,1已处理)']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
        ], [
            'channel_id','source_channel_id','media_group_id','message_id','create_at',
        ], true);
    }

    /**
     * 网络素材资源管理
     * @class PluginTelegramSourceResources
     * @table plugin_telegram_source_resources
     * @return void
     */
    private function _create_plugin_telegram_source_resources()
    {
        // 创建数据表对象
        $table = $this->table('plugin_telegram_source_resources', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '网络素材资源管理',
        ]);
        PhinxExtend::upgrade($table, [
            ['channel_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '归档频道ID']],
            ['source_channel_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '来源频道ID']],
            ['source_message_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '来源频道消息ID']],
            ['message_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '频道消息ID']],
            ['media_group_id', 'string', ['limit' => 32,'default' => 0, 'null' => true, 'comment' => '组合消息ID']],
            ['caption', 'text', ['default' => NULL, 'null' => true, 'comment' => '消息内容']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '状态(0未处理,1已处理)']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
        ], [
            'channel_id','source_channel_id','source_message_id','message_id','media_group_id','create_at',
        ], true);
    }

    /**
     * 网络资源媒体信息
     * @class PluginTelegramResourcesMedia
     * @table plugin_telegram_resources_media
     * @return void
     */
    private function _create_plugin_telegram_resources_media()
    {
        // 创建数据表对象
        $table = $this->table('plugin_telegram_resources_media', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '网络资源媒体信息',
        ]);
        PhinxExtend::upgrade($table, [
            ['media_group_id', 'string', ['limit' => 32,'default' => 0, 'null' => true, 'comment' => '组合消息ID']],
            ['caption', 'text', ['default' => NULL, 'null' => true, 'comment' => '消息内容']],
            ['type', 'string', ['limit' => 20, 'default' => NULL, 'null' => true, 'comment' => '媒体类型']],
            ['media', 'string', ['limit' => 255, 'default' => null, 'null' => true, 'comment' => '媒体文件ID']],
            ['width', 'integer', ['limit' => 10, 'default' => 0, 'null' => true, 'comment' => '宽度']],
            ['height', 'integer', ['limit' => 10, 'default' => 0, 'null' => true, 'comment' => '宽度']],
            ['duration', 'integer', ['limit' => 10, 'default' => 0, 'null' => true, 'comment' => '时长']],
            ['file_size', 'integer', ['limit' => 10, 'default' => 0, 'null' => true, 'comment' => '文件大小']],
            ['thumbnail', 'string', ['limit' => 255, 'default' => null, 'null' => true, 'comment' => '视频缩略图媒体文件ID']],
            ['local_url', 'string', ['limit' => 255, 'default' => NULL, 'null' => true, 'comment' => '媒体信息,base64信息']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '状态(0未处理,1已处理)']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
        ], [
            'media_group_id','type','create_at',
        ], true);
    }

    /**
     * 网络资源链接
     * @class PluginTelegramResourcesLink
     * @table plugin_telegram_resources_link
     * @return void
     */
    private function _create_plugin_telegram_resources_link()
    {
        // 创建数据表对象
        $table = $this->table('plugin_telegram_resources_link', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '网络资源链接',
        ]);
        PhinxExtend::upgrade($table, [
            ['release_channel_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '来源频道ID']],
            ['release_message_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '来源频道消息ID']],
            ['caption', 'string', ['limit' => 500, 'default' => NULL, 'null' => true, 'comment' => '文本内容']],
            ['type', 'string', ['limit' => 32, 'default' => NULL, 'null' => true, 'comment' => '类型']],
            ['local_url', 'string', ['limit' => 200, 'default' => NULL, 'null' => true, 'comment' => '媒体链接']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
        ], [
            'release_channel_id','create_at',
        ], true);
    }

    /**
     * 经营管理频道
     * @class PluginTelegramChannel
     * @table plugin_telegram_channel
     * @return void
     */
    private function _create_plugin_telegram_channel()
    {
        // 创建数据表对象
        $table = $this->table('plugin_telegram_channel', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '经营管理频道',
        ]);

        PhinxExtend::upgrade($table, [
            ['channel_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '频道ID']],
            ['channel_title', 'string', ['limit' => 16, 'default' => NULL, 'null' => true, 'comment' => '频道标题']],
            ['channel_name', 'string', ['limit' => 32, 'default' => NULL, 'null' => true, 'comment' => '频道名']],
            ['channel_link', 'string', ['limit' => 64, 'default' => NULL, 'null' => true, 'comment' => '频道链接']],
            ['remark', 'string', ['limit' => 500, 'default' => NULL, 'null' => true, 'comment' => '备注(内部使用)']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 1, 'null' => true, 'comment' => '状态(0拉黑,1正常)']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
            ['update_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间']],
        ], [
            'channel_id'
        ], true);
    }

    /**
     * 网络素材频道
     * @class PluginTelegramChannelSource
     * @table plugin_telegram_channel_source
     * @return void
     */
    private function _create_plugin_telegram_channel_source()
    {
        // 创建数据表对象
        $table = $this->table('plugin_telegram_channel_source', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '网络素材频道',
        ]);

        PhinxExtend::upgrade($table, [
            ['channel_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '频道ID']],
            ['release_channel_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '所属频道ID']],
            ['channel_title', 'string', ['limit' => 16, 'default' => NULL, 'null' => true, 'comment' => '频道标题']],
            ['channel_name', 'string', ['limit' => 32, 'default' => NULL, 'null' => true, 'comment' => '频道名']],
            ['channel_link', 'string', ['limit' => 64, 'default' => NULL, 'null' => true, 'comment' => '频道链接']],
            ['remark', 'string', ['limit' => 500, 'default' => NULL, 'null' => true, 'comment' => '备注(内部使用)']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 1, 'null' => true, 'comment' => '状态(0拉黑,1正常)']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
            ['update_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间']],
        ], [
            'channel_id','release_channel_id'
        ], true);
    }
}
