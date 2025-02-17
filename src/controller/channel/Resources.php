<?php

declare (strict_types=1);

namespace plugin\telegram\controller\channel;

use plugin\telegram\model\PluginTelegramChannel;
use plugin\telegram\model\PluginTelegramChannelResources;
use plugin\telegram\model\PluginTelegramChannelSource;
use plugin\telegram\model\PluginTelegramResourcesMedia;
use plugin\telegram\model\PluginTelegramSourceResources;
use plugin\telegram\service\SourceService;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\admin\service\AdminService;

/**
 * 发布素材资源
 * @class Content
 * @package plugin\telegram\controller\channel
 */
class Resources extends Controller
{
    /**
     * 发布素材资源
     * @auth true
     * @menu true
     */
    public function index()
    {
        $this->title = '发布素材资源';
        $this->source = PluginTelegramChannelSource::getChannelID('channel_title');
        $this->channel = PluginTelegramChannel::getChannelID('channel_title');
        PluginTelegramChannelResources::mQuery(null, static function (QueryHelper $query) {
            $query->with(['media','source','channel'])->page();
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
            $data = PluginTelegramChannelResources::mk()->where('id',$this->id)->with(['media','source'])->find()->toArray();
            if ($this->request->get('output') === 'json') {
                $this->success('获取数据成功！', $data);
            } else {
                $this->title = '编辑素材';
                $this->channels = PluginTelegramChannel::getChannelID('channel_title');
                $this->fetch('form',['vo'=>$data]);
            }
        } else {
            $data = $this->request->post('data', []);
            $index = array_search(true, array_column($data, 'caption'));
            $caption = $index !== false ? $data[$index]['caption'] : '';
            if (PluginTelegramResourcesMedia::mk()->saveAll($data)) {
                PluginTelegramChannelResources::mk()->where('id',$this->id)->update(['caption'=>$caption]);
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
        PluginTelegramChannelResources::mDelete();
    }

    /**
     * 删除媒体
     * auth true
     */
    public function delSource()
    {
        $map = $this->_vali(['id.require' => 'ID不可为空！']);
        PluginTelegramResourcesMedia::mk()->where($map)->delete();
        $this->success("删除成功！");
    }

    /**
     * 素材预览
     * auth true
     */
    public function preview()
    {
        $map = $this->_vali(['media_group_id.require' => 'media_group_id不可为空！']);
        $result = SourceService::preview($map['media_group_id']);
        $this->success("已发布预览成功！");
    }
}