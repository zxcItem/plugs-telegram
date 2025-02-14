<?php

declare (strict_types=1);

namespace plugin\telegram\controller\source;

use plugin\telegram\model\PluginTelegramChannelSource;
use plugin\telegram\model\PluginTelegramResourcesLink;
use think\admin\Controller;
use think\admin\helper\QueryHelper;


/**
 * 网络链接素材
 * Class Link
 * @package plugin\telegram\controller\source
 */
class Link extends Controller
{

    /**
     * 网络链接素材
     * @return void
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        PluginTelegramResourcesLink::mQuery()->layTable(function () {
            $this->title = '网络链接素材';
            $this->source = PluginTelegramChannelSource::getChannelID('channel_title');
        }, function (QueryHelper $query) {
            $query->with('channel')->equal('release_channel_id')->dateBetween('create_at');
        });
    }

    /**
     * 数据处理
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _page_filter(array &$data)
    {

    }

    /**
     * 添加
     * @auth true
     */
    public function add()
    {
        PluginTelegramResourcesLink::mForm('form');
    }

    /**
     * 编辑
     * @auth true
     */
    public function edit()
    {
        PluginTelegramResourcesLink::mForm('form');
    }

    /**
     * 修改状态
     * @auth true
     */
    public function state()
    {
        PluginTelegramResourcesLink::mSave($this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 删除
     * @auth true
     */
    public function remove()
    {
        PluginTelegramResourcesLink::mDelete();
    }
}