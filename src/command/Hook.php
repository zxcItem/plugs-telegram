<?php

declare (strict_types=1);

namespace plugin\telegram\command;

use plugin\telegram\service\TelegramApi;
use think\admin\Command;
use think\admin\Exception;
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
            $this->message($data);
        } catch (Exception $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->setQueueError($exception->getMessage());
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
}