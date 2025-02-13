<?php

declare (strict_types=1);

namespace plugin\telegram\controller\source;

use plugin\telegram\model\PluginTelegramSourceContent;
use plugin\telegram\model\PluginTelegramContentMedia;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * 内容管理
 * @class Content
 * @package plugin\telegram\controller\source
 */
class Content extends Controller
{
    /**
     * 内容管理
     * @auth true
     * @menu true
     */
    public function index()
    {
        $this->title = '内容管理';
        PluginTelegramSourceContent::mQuery(null, static function (QueryHelper $query) {
            $query->with(['media'])->order('id desc')->page();
        });
    }

    /**
     * 图文列表数据处理
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _page_filter(array &$data)
    {
        die(dump($data));
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
     * 编辑微信图文
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
                $data = PluginTelegramSourceContent::mk()->where('id',$this->id)->with('media')->find()->toArray();
                $this->success('获取数据成功！', $data);
            } else {
                $this->title = '编辑微信图文';
                $this->fetch('form');
            }
        } else {
            $data = $this->request->post('data', []);
            $index = array_search(true, array_column($data, 'caption'));
            $caption = $index !== false ? $data[$index]['caption'] : '';
            if (PluginTelegramContentMedia::mk()->saveAll($data)) {
                PluginTelegramSourceContent::mk()->where('id',$this->id)->update(['caption'=>$caption]);
                $this->success('图文更新成功！', 'javascript:history.back()');
            } else {
                $this->error('更新失败，请稍候再试！');
            }
        }
    }

    /**
     * 删除微信图文
     * auth true
     */
    public function remove()
    {
        PluginTelegramSourceContent::mDelete();
    }

}