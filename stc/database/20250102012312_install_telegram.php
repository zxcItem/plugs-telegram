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
        $this->_create_plugin_telegram_channel_content();
        $this->_create_plugin_telegram_source_content();
        $this->_create_plugin_telegram_content_media();
    }

    /**
     * 内容素材管理
     * @class PluginTelegramChannelContent
     * @table plugin_telegram_channel_content
     * @return void
     */
    private function _create_plugin_telegram_channel_content()
    {
        // 创建数据表对象
        $table = $this->table('plugin_telegram_channel_content', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '内容素材管理',
        ]);
        PhinxExtend::upgrade($table, [
            ['channel_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '频道ID']],
            ['message_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '频道消息ID']],
            ['media_group_id', 'string', ['limit' => 32,'default' => 0, 'null' => true, 'comment' => '组合消息ID']],
            ['caption', 'text', ['default' => NULL, 'null' => true, 'comment' => '消息内容']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '状态(0未处理,1已处理)']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
        ], [
            'channel_id','media_group_id','message_id','create_at',
        ], true);
    }

    /**
     * 素材来源管理
     * @class PluginTelegramSourceContent
     * @table plugin_telegram_source_content
     * @return void
     */
    private function _create_plugin_telegram_source_content()
    {
        // 创建数据表对象
        $table = $this->table('plugin_telegram_source_content', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '素材来源管理',
        ]);
        PhinxExtend::upgrade($table, [
            ['channel_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '频道ID']],
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
     * 素材来源管理
     * @class PluginTelegramContentMedia
     * @table plugin_telegram_content_media
     * @return void
     */
    private function _create_plugin_telegram_content_media()
    {
        // 创建数据表对象
        $table = $this->table('plugin_telegram_content_media', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '媒体文件信息管理',
        ]);
        PhinxExtend::upgrade($table, [
            ['media_group_id', 'string', ['limit' => 32,'default' => 0, 'null' => true, 'comment' => '组合消息ID']],
            ['caption', 'text', ['default' => NULL, 'null' => true, 'comment' => '消息内容']],
            ['type', 'string', ['limit' => 20, 'default' => NULL, 'null' => true, 'comment' => '媒体类型']],
            ['media', 'string', ['limit' => 255, 'default' => null, 'null' => true, 'comment' => '媒体文件ID']],
            ['width', 'integer', ['limit' => 10, 'default' => 0, 'null' => true, 'comment' => '宽度']],
            ['height', 'integer', ['limit' => 10, 'default' => 0, 'null' => true, 'comment' => '宽度']],
            ['file_size', 'integer', ['limit' => 10, 'default' => 0, 'null' => true, 'comment' => '文件大小']],
            ['thumbnail', 'string', ['limit' => 255, 'default' => null, 'null' => true, 'comment' => '视频缩略图媒体文件ID']],
            ['local_url', 'text', ['default' => NULL, 'null' => true, 'comment' => '媒体信息,base64信息']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
        ], [
            'media_group_id','type','create_at',
        ], true);
    }
}
