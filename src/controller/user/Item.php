<?php

declare (strict_types=1);

namespace plugin\telegram\controller\user;

use plugin\telegram\model\PluginTelegramUser;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * Class Item
 * @package plugin\telegram\controller\user
 */
class Item extends Controller
{

    /**
     * 会员用户管理
     * @return void
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->type = $this->get['type'] ?? 'index';
        PluginTelegramUser::mQuery()->layTable(function () {
            $this->title = '用户管理';
        }, function (QueryHelper $query) {
            $query->equal('agent_entry')->dateBetween('create_time');
            $query->with(['agent', 'user'])->like('name|phone#user')->dateBetween('create_time');
            $query->where(['status' => intval($this->type === 'index'), 'deleted' => 0]);
        });
    }
}