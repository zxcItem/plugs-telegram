<?php

declare (strict_types=1);

namespace plugin\telegram\command;

use plugin\telegram\model\PluginTelegramChannelSource;
use plugin\telegram\model\PluginTelegramResourcesLink;
use plugin\telegram\model\PluginTelegramResourcesMedia;
use plugin\telegram\model\PluginTelegramSourceResources;
use plugin\telegram\service\RedisService;
use plugin\telegram\service\TelegramApi;
use think\admin\Command;
use think\admin\Exception;
use think\admin\extend\CodeExtend;
use think\console\Input;
use think\console\Output;

/**
 * Telegram WedHook推送
 * Class Hook
 * @package plugin\telegram\command
 */
class Hook extends Command
{

    /**
     * 指令参数配置
     * @return void
     */
    public function configure()
    {
        $this->setName('plugin:telegram:hook');
        $this->setDescription('处理WebHook推送消息');
    }

    /**
     * 执行指令
     * @param Input $input
     * @param Output $output
     * @throws Exception
     */
    protected function execute(Input $input, Output $output)
    {
        try {
            $data = $this->queue->data['data'];
            $this->grouping($data);
        } catch (Exception $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->setQueueError($exception->getMessage());
        }
    }

    /**
     * 按照消息类型处理
     * @param $data
     * @throws Exception
     */
    protected function grouping($data)
    {
        $result = $data['channel_post'];
        if (isset($result['entities'])){
            $this->entities($result);
        }
        if (isset($result['photo'])){
            $this->photo($result);
        }
        if (isset($result['video'])){
            $this->video($result);
        }
    }


    protected function message($result)
    {
        $channelPost = $result['channel_post'] ?? null;
        if ($channelPost && isset($channelPost['forward_from_message_id'], $channelPost['forward_from_chat'])) {
            // 提取消息 ID 和频道 ID
            $messageId = $channelPost['forward_from_message_id'];
            $channelId = $channelPost['forward_from_chat']['id'];
            // 获取媒体内容
            $mediaContent = $channelPost['photo'] ?? $channelPost['video'] ?? null;
            // 如果有媒体内容，更新记录
            if ($mediaContent) {
                // 获取最后一个媒体文件 ID
                $base64Image = '';
                if (isset($channelPost['photo'])) $media = end($mediaContent)['file_id'] ?? '';
                if (isset($channelPost['video'])) $media = $channelPost['video']['thumbnail']['file_id'];
                $url =TelegramApi::getFile($media);
                $imageData = file_get_contents($url);
                if ($imageData !== false) {
                    $base64Image = "data:image/png;base64,".base64_encode($imageData);
                }
            }
        }
    }

    /**
     * 保存文本链接
     * @param $result
     * @throws Exception
     */
    protected function entities($result)
    {
        $data = [];
        foreach ($result['entities'] as &$item){
            $data[] = [
                'release_channel_id' => $result['forward_origin']['chat']['id'],
                'release_message_id' => $result['forward_origin']['message_id'],
                'type'              => $item['type'] ?? '',
                'caption'           => $result['text'] ?? '',
                'local_url'         => $item['url'] ?? ''
            ];
        }
        PluginTelegramResourcesLink::mk()->saveAll($data);
        $this->setQueueSuccess("网络文本链接资源保存成功");
    }

    /**
     * 保存图片信息
     * @param $result
     * @throws Exception
     */
    protected function photo($result)
    {
        $media_group_id = CodeExtend::uniqidNumber(17);
        $maxFile = array_reduce($result['photo'], function($carry, $item) {
            return ($carry === null || $item['file_size'] > $carry['file_size']) ? $item : $carry;
        });
        $resource = [
            'channel_id'        => $this->getChannelId($result['forward_origin']['chat']['id']),
            'source_channel_id' => $result['forward_origin']['chat']['id'],
            'source_message_id' => $result['forward_origin']['message_id'],
            'message_id'        => $result['message_id'],
            'media_group_id'    => $result['media_group_id'] ?? $media_group_id,
            'caption'           => $result['caption'] ?? '',
            'type'              => 'photo',
            'media'             => $maxFile['file_id'],
            'width'             => $maxFile['width'],
            'height'            => $maxFile['height'],
            'file_size'         => $maxFile['file_size'],
            'thumbnail'         => $maxFile['file_id'],
        ];
        $this->saveData($resource);
        $this->setQueueSuccess("图文资源保存成功");
    }

    /**
     * 视频资源
     * @param $result
     * @throws Exception
     */
    protected function video($result)
    {
        $media_group_id = CodeExtend::uniqidNumber(17);
        $resource = [
            'channel_id'        => $this->getChannelId($result['forward_origin']['chat']['id']),
            'source_channel_id' => $result['forward_origin']['chat']['id'],
            'source_message_id' => $result['forward_origin']['message_id'],
            'message_id'        => $result['message_id'],
            'media_group_id'    => $result['media_group_id'] ?? $media_group_id,
            'caption'           => $result['caption'] ?? '',
            'type'              => 'video/mp4',
            'media'             => $result['video']['file_id'],
            'width'             => $result['video']['width'],
            'height'            => $result['video']['height'],
            'file_size'         => $result['video']['file_size'],
            'thumbnail'         => $result['video']['thumbnail']['file_id'],
        ];
        $this->saveData($resource);
        $this->setQueueSuccess("视频资源保存成功");
    }

    /**
     * 检测是否已存在MediaGroupId
     * @param $grouped_id
     * @return bool
     */
    protected function redisCache($grouped_id)
    {
        $grouped_id = RedisService::instance()->get("MediaGroupId:{$grouped_id}");
        return $grouped_id ? true : false;
    }

    /**
     * 获取关联频道
     * @param $channel
     * @return mixed|string
     */
    protected function getChannelId($channel)
    {
        $channelSources  = RedisService::instance()->get("channelSource");
        if ($channelSources == null){
            $channelSources = PluginTelegramChannelSource::getChannelID('release_channel_id');
            RedisService::instance()->set("channel_source", $channelSources, 3600);
        }
        return $channelSources[$channel] ?? '';
    }

    /**
     * 保存数据
     * @param $resource
     */
    protected function saveData($resource)
    {
        if (!self::redisCache($resource['media_group_id'])){
            RedisService::instance()->set("MediaGroupId:{$resource['media_group_id']}",$resource['media_group_id'],3600);
            PluginTelegramSourceResources::mk()->save($resource);
        }
        PluginTelegramResourcesMedia::mk()->save($resource);
    }
}