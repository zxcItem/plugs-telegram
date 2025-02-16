<?php


namespace plugin\telegram\service;


use plugin\telegram\model\PluginTelegramResourcesMedia;
use think\admin\Service;

/**
 * 素材处理
 * Class SourceService
 * @package plugin\telegram\service
 */
class SourceService extends Service
{

    /**
     * 素材预览接口
     * @param $media_group_id
     * @return bool|mixed
     * @throws \think\admin\Exception
     */
    public static function preview($media_group_id)
    {
        $contents = PluginTelegramResourcesMedia::mk()
            ->where('media_group_id',$media_group_id)
            ->field('caption,type,media')
            ->order('sort')->select()->map(function ($content){
                $content['parse_mode'] = 'HTML';
                return $content;
            })->toArray();
        return TelegramApi::sendMediaGroup([
            'chat_id'    => ConfigService::get('forward_channel'),
            'media'      => json_encode($contents),
            'parse_mode' => 'html'
        ]);
    }
}