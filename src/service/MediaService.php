<?php

declare (strict_types=1);

namespace plugin\telegram\service;

use plugin\telegram\model\PluginTelegramNews;
use plugin\telegram\model\PluginTelegramNewsArticle;
use think\admin\Service;


/**
 * 素材管理
 * @class MediaService
 * @package aplugin\telegram\service
 */
class MediaService extends Service
{
    /**
     * 通过图文ID读取图文信息
     * @param mixed $id 本地图文ID
     * @param mixed $map 额外的查询条件
     * @return array
     */
    public static function news($id, $map = []): array
    {
        // 文章主体数据
        $data = PluginTelegramNews::mk()->where(['id' => $id, 'is_deleted' => 0])->where($map)->findOrEmpty()->toArray();
        if (empty($data)) return [];

        // 文章内容编号
        $data['articles'] = [];
        $aids = $data['articleids'] = str2arr($data['article_id']);
        if (empty($aids)) return $data;

        // 文章内容列表
        $items = PluginTelegramNewsArticle::mk()->whereIn('id', $aids)->withoutField('create_by,create_at')->select()->toArray();
        foreach ($aids as $aid) foreach ($items as $item) if (intval($item['id']) === intval($aid)) $data['articles'][] = $item;

        // 返回文章内容
        return $data;
    }
}
