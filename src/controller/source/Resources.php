<?php

declare (strict_types=1);

namespace plugin\telegram\controller\source;

use plugin\telegram\model\PluginTelegramSourceResources;
use plugin\telegram\model\PluginTelegramResourcesMedia;
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
            $query->with(['media'])->order('id desc')->page();
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
            if ($this->request->get('output') === 'json') {
                $data = PluginTelegramSourceResources::mk()->where('id',$this->id)->with('media')->find()->toArray();
                $this->success('获取数据成功！', $data);
            } else {
                $this->title = '编辑素材';
                $this->fetch('form');
            }
        } else {
            $data = $this->request->post('data', []);
            $index = array_search(true, array_column($data, 'caption'));
            $caption = $index !== false ? $data[$index]['caption'] : '';
            if (PluginTelegramResourcesMedia::mk()->saveAll($data)) {
                PluginTelegramSourceResources::mk()->where('id',$this->id)->update(['caption'=>$caption]);
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
        PluginTelegramSourceResources::mDelete();
    }

}