<?php

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
        $this->_create_plugin_telegram_account();
    }

    /**
     * Telegram用户
     * @class PluginTelegramAccount
     * @table plugin_telegram_account
     * @return void
     */
    private function _create_plugin_telegram_account()
    {

        // 当前数据表
        $table = 'plugin_telegram_account';

        // 存在则跳过
        if ($this->hasTable($table)) return;

        // 创建数据表
        $this->table($table, [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'Telegram-用户',
        ])
            ->addColumn('telegram_id', 'string', ['limit' => 16, 'default' => '', 'null' => true, 'comment' => '用户的唯一标识符'])
            ->addColumn('title', 'string', ['limit' => 16, 'default' => '', 'null' => true, 'comment' => '账号标题'])
            ->addColumn('api_id', 'string', ['limit' => 16, 'default' => '', 'null' => true, 'comment' => 'API ID'])
            ->addColumn('api_hash', 'string', ['limit' => 16, 'default' => '', 'null' => true, 'comment' => 'API Hash'])
            ->addColumn('first_name', 'string', ['limit' => 16, 'default' => '', 'null' => true, 'comment' => '用户的名字'])
            ->addColumn('last_name', 'string', ['limit' => 16, 'default' => '', 'null' => true, 'comment' => '用户的姓氏'])
            ->addColumn('username', 'string', ['limit' => 16, 'default' => '', 'null' => true, 'comment' => '用户名'])
            ->addColumn('phone_number', 'string', ['limit' => 16, 'default' => '', 'null' => true, 'comment' => '用户的手机号'])
            ->addColumn('photo', 'string', ['limit' => 16, 'default' => '', 'null' => true, 'comment' => '用户的头像'])
            ->addColumn('access_hash', 'string', ['limit' => 16, 'default' => '', 'null' => true, 'comment' => '用户的访问哈希'])
            ->addColumn('user_status', 'string', ['limit' => 16, 'default' => '', 'null' => true, 'comment' => ' 用户的状态（如 online, last_seen 等'])
            ->addColumn('remark', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '备注(内部使用)'])
            ->addColumn('sort', 'biginteger', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '排序权重'])
            ->addColumn('status', 'integer', ['limit' => 1, 'default' => 1, 'null' => true, 'comment' => '用户状态(0拉黑,1正常)'])
            ->addColumn('deleted', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '删除状态(0未删,1已删)'])
            ->addColumn('create_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间'])
            ->addIndex('telegram_id', ['name' => 'idx_plugin_telegram_account_telegram_id'])
            ->addIndex('phone_number', ['name' => 'idx_plugin_telegram_account_phone_number'])
            ->addIndex('sort', ['name' => 'idx_plugin_telegram_account_sort'])
            ->addIndex('status', ['name' => 'idx_plugin_telegram_account_status'])
            ->addIndex('deleted', ['name' => 'idx_plugin_telegram_account_deleted'])
            ->addIndex('create_time', ['name' => 'idx_plugin_telegram_account_create_time'])
            ->create();

        // 修改主键长度
        $this->table($table)->changeColumn('id', 'integer', ['limit' => 11, 'identity' => true]);
    }


}
