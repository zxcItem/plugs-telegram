<?php

declare (strict_types=1);

namespace plugin\telegram\service;

use think\admin\Service;
use think\App;

/**
 * Telegram Api 接口服务
 * Class TelegramApi
 * @package plugin\telegram\service
 */
class TelegramApi extends Service
{
    protected static $bot;

    public static function token()
    {
        return ConfigService::get('bot_token');
    }

    /**
     * 发布集合媒体信息
     * @param $params
     * @return mixed
     */
    public static function sendMediaGroup($params)
    {
        $token = self::token();
        $result = json_decode(http_post("https://api.telegram.org/bot{$token}/sendMediaGroup",$params),true);
        if ($result && $result['ok'] === true){
            return $result;
        }
        return false;
    }

    /**
     * 获取文件的地址
     * @param $file_id
     * @return mixed
     */
    public static function getFile($file_id)
    {
        $token = self::token();
        $result = json_decode(http_get("https://api.telegram.org/bot{$token}/getFile?file_id={$file_id}"),true);
        if ($result && $result['ok'] === true){
            return "https://api.telegram.org/file/bot{$token}/{$result['result']['file_path']}";
        }
        return false;
    }

    /**
     * 获取频道的ID
     * @param $channel_name
     * @return mixed
     */
    public static function getChat($channel_name)
    {
        $token = self::token();
        $result = json_decode(http_get("https://api.telegram.org/bot{$token}/getChat?chat_id={$channel_name}"),true);
        if ($result && $result['ok'] === true){
            return $result['result'];
        }
        return false;
    }
}