<?php

declare (strict_types=1);

namespace plugin\telegram\controller\channel;

use plugin\telegram\model\PluginTelegramChannel;
use plugin\telegram\service\TelegramApi;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\exception\HttpResponseException;


/**
 * 经营频道管理
 * Class Item
 * @package plugin\telegram\controller\channel
 */
class Item extends Controller
{

    /**
     * 经营频道管理
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
        PluginTelegramChannel::mQuery()->layTable(function () {
            $this->title = '经营频道管理';
        }, function (QueryHelper $query) {
            $query->like('channel_name,channel_title')->dateBetween('create_at');
            $query->where(['status' => intval($this->type === 'index')]);
        });
    }

    /**
     * 添加
     * @auth true
     */
    public function add()
    {
        PluginTelegramChannel::mForm('form');
    }

    /**
     * 编辑
     * @auth true
     */
    public function edit()
    {
        PluginTelegramChannel::mForm('form');
    }

    /**
     * 修改状态
     * @auth true
     */
    public function state()
    {
        PluginTelegramChannel::mSave($this->_vali([
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

        }
    }

    /**
     * 删除
     * @auth true
     */
    public function remove()
    {
        PluginTelegramChannel::mDelete();
    }

    /**
     * 获取频道的id
     * @return mixed
     */
    public function getChannelID()
    {
        try {
            $channel_name = $this->request->post('channel_name');
            $channel = TelegramApi::getChat($channel_name);
            return $channel['id'];
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}