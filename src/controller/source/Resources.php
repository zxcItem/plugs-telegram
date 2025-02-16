<?php

declare (strict_types=1);

namespace plugin\telegram\controller\source;

use plugin\telegram\model\PluginTelegramChannel;
use plugin\telegram\model\PluginTelegramChannelResources;
use plugin\telegram\model\PluginTelegramSourceResources;
use plugin\telegram\model\PluginTelegramResourcesMedia;
use plugin\telegram\service\RedisService;
use plugin\telegram\service\TelegramApi;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * 网络素材资源
 * @class Content
 * @package plugin\telegram\controller\source
 */
class Resources extends Controller
{
    /**
     * 网络素材资源
     * @auth true
     * @menu true
     */
    public function index()
    {
        $this->title = '网络素材资源';
        PluginTelegramSourceResources::mQuery(null, static function (QueryHelper $query) {
            $query->where('status',0)->with(['media'])->page(true, true, false, 12);
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
            $data = PluginTelegramSourceResources::mk()
                ->where('id',$this->id)
                ->with(['media','channelTitle'])->find()->toArray();
            if ($this->request->get('output') === 'json') {
                $this->success('获取数据成功！', $data);
            } else {
                $this->title = '编辑素材';
                $this->channels = PluginTelegramChannel::getChannelID();
                $this->fetch('form',['vo'=>$data]);
            }
        } else {
            $data = $this->request->post('data', []);
            $channel = $this->request->post('channel', []);
            if (PluginTelegramResourcesMedia::mk()->saveAll($data)) {
                PluginTelegramSourceResources::mk()->where('id',$this->id)->update($channel);
                $this->success('素材更新成功！', 'javascript:history.back()');
            } else {
                $this->error('更新失败，请稍候再试！');
            }
        }
    }

    /**
     * 自动刷新文件信息
     * @auth true
     */
    public function thumbnail()
    {
        $this->_queue('自动刷新资源信息', "plugin:telegram:thumbnail", 0,[],0,600);
    }

    /**
     * 删除
     * auth true
     */
    public function remove()
    {
        PluginTelegramSourceResources::mDelete('media_group_id',$this->_vali([
            'media_group_id.require' => '组合编号不能为空！',
        ]));
    }



    /**
     * 删除结果处理
     * @param boolean $result
     * @throws \think\Exception
     * @throws \think\exception
     */
    protected function _remove_delete_result($result)
    {
        if ($result) {
            $where = ['media_group_id' => $this->request->post('media_group_id')];
            PluginTelegramResourcesMedia::mk()->where($where)->delete();
            $this->success("删除成功！", '');
        } else {
            $this->error("删除失败，请稍候再试！");
        }
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
     * 素材收录
     */
    public function include()
    {
        $map = $this->_vali(['id.require' => 'ID不可为空！']);
        $data = PluginTelegramSourceResources::mk()->where($map)->field('channel_id,source_channel_id,media_group_id,caption')->find();
        PluginTelegramChannelResources::mk()->save($data);
        PluginTelegramSourceResources::mk()->where($map)->save(['status'=>1]);
        $this->success("收录成功！");
    }

}