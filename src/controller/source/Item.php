<?php

declare (strict_types=1);

namespace plugin\telegram\controller\source;

use plugin\telegram\model\PluginTelegramChannel;
use plugin\telegram\model\PluginTelegramChannelSource;
use think\admin\Controller;
use think\admin\helper\QueryHelper;


/**
 * 网络素材频道
 * Class Item
 * @package plugin\telegram\controller\source
 */
class Item extends Controller
{

    /**
     * 网络素材频道
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
        PluginTelegramChannelSource::mQuery()->layTable(function () {
            $this->title = '网络素材频道';
        }, function (QueryHelper $query) {
            $query->like('channel_name,channel_title')->dateBetween('create_at');
            $query->where(['status' => intval($this->type === 'index')]);
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
        $this->channel = PluginTelegramChannel::getChannelID();
        foreach ($data as &$datum) $datum['belong_channel'] = $this->channel[$datum['belong_channel_id']] ?? '';
    }

    /**
     * 添加
     * @auth true
     */
    public function add()
    {
        PluginTelegramChannelSource::mForm('form');
    }

    /**
     * 编辑
     * @auth true
     */
    public function edit()
    {
        PluginTelegramChannelSource::mForm('form');
    }

    /**
     * 修改状态
     * @auth true
     */
    public function state()
    {
        PluginTelegramChannelSource::mSave($this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 表单数据处理
     * @param array $data
     */
    protected function _form_filter(array &$data)
    {
        if ($this->request->isGet()){
            $this->channel = PluginTelegramChannel::getChannelID();
        }
    }

    /**
     * 删除
     * @auth true
     */
    public function remove()
    {
        PluginTelegramChannelSource::mDelete();
    }
}