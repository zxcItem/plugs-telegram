<?php

declare (strict_types=1);

namespace plugin\telegram\controller\source;

use plugin\telegram\model\PluginTelegramChannel;
use plugin\telegram\model\PluginTelegramChannelCollect;
use plugin\telegram\model\PluginTelegramChannelSource;
use plugin\telegram\model\PluginTelegramSourceResources;
use plugin\telegram\model\PluginTelegramResourcesMedia;
use plugin\telegram\service\RedisService;
use plugin\telegram\service\TelegramApi;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\admin\service\QueueService;

/**
 * 网络素材收藏
 * @class Content
 * @package plugin\telegram\controller\source
 */
class Collect extends Controller
{
    /**
     * 网络素材收藏
     * @auth true
     * @menu true
     */
    public function index()
    {
        $this->title = '网络素材收藏';
        $this->source = PluginTelegramChannelSource::getChannelID('channel_title');
        $this->channel = PluginTelegramChannel::getChannelID('channel_title');
        PluginTelegramChannelCollect::mQuery(null, static function (QueryHelper $query) {
            $query->where('status',0)
                ->equal('source_channel_id')
                ->with(['media','source','channel'])->page(true, true, false, 20);
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
     * 图文选择器
     * @auth true
     */
    public function select()
    {
        $this->index();
    }

    /**
     * 编辑
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        $this->id = $this->request->get('id');
        if (empty($this->id)) $this->error('参数错误，请稍候再试！');
        if ($this->request->isGet()) {
            $data = PluginTelegramChannelCollect::mk()
                ->where('id',$this->id)
                ->with(['media','source'])->find()->toArray();
            if ($this->request->get('output') === 'json') {
                $this->success('获取数据成功！', $data);
            } else {
                $this->title = '编辑素材';
                $this->channels = PluginTelegramChannel::getChannelID('channel_title');
                $this->fetch('form',['vo'=>$data]);
            }
        } else {
            $data = $this->request->post('data', []);
            $channel = $this->request->post('channel', []);
            if (PluginTelegramResourcesMedia::mk()->saveAll($data)) {
                PluginTelegramChannelCollect::mk()->where('id',$this->id)->update($channel);
                $this->success('素材更新成功！', 'javascript:history.back()');
            } else {
                $this->error('更新失败，请稍候再试！');
            }
        }
    }

    /**
     * 删除
     * auth true
     */
    public function remove()
    {
        PluginTelegramChannelCollect::mDelete();
    }

}