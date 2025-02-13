<?php

declare (strict_types=1);

namespace plugin\telegram\controller\content;

use plugin\telegram\model\PluginTelegramChannelContent;
use plugin\telegram\model\PluginTelegramContentMedia;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\admin\service\AdminService;

/**
 * 图文管理
 * @class News
 * @package plugin\telegram\controller\content
 */
class Content extends Controller
{
    /**
     * 微信图文管理
     * @auth true
     * @menu true
     */
    public function index()
    {
        $this->title = '微信图文列表';
        PluginTelegramChannelContent::mQuery(null, static function (QueryHelper $query) {
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
     * 添加微信图文
     * @auth true
     */
    public function add()
    {
        if ($this->request->isGet()) {
            $this->title = '新建微信图文';
            $this->fetch('form');
        } else {
            $update = [
                'create_by'  => AdminService::getUserId(),
                'article_id' => $this->_buildArticle($this->request->post('data', [])),
            ];
            if (PluginTelegramChannelContent::mk()->save($update) !== false) {
                $this->success('图文添加成功！', 'javascript:history.back()');
            } else {
                $this->error('图文添加失败，请稍候再试！');
            }
        }
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
                $this->success('获取数据成功！', MediaService::news($this->id));
            } else {
                $this->title = '编辑微信图文';
                $this->fetch('form');
            }
        } else {
            $ids = $this->_buildArticle($this->request->post('data', []));
            [$map, $data] = [['id' => $this->id], ['article_id' => $ids]];
            if (PluginTelegramChannelContent::mk()->where($map)->update($data) !== false) {
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
        PluginTelegramChannelContent::mDelete();
    }

    /**
     * 图文更新操作
     * @param array $data
     * @return string
     */
    private function _buildArticle(array $data): string
    {
        $ids = [];
        foreach ($data as $vo) {
            if (empty($vo['digest'])) {
                $vo['digest'] = mb_substr(strip_tags(preg_replace('#(\s+|　)#', '', $vo['content'])), 0, 120);
            }
            $vo['create_at'] = date('Y-m-d H:i:s');
            if (empty($vo['id'])) {
                $result = $id = PluginTelegramContentMedia::mk()->insertGetId($vo);
            } else {
                $id = intval($vo['id']);
                $result = PluginTelegramContentMedia::mk()->where('id', $id)->update($vo);
            }
            if ($result) $ids[] = $id;
        }
        return join(',', $ids);
    }
}