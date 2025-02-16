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

    public static function token($type = 0)
    {
        if ($type){
            $token = ConfigService::get('forward_token');
        }else{
            $token = ConfigService::get('bot_token');
        }
        return $token;
    }

    /**
     * 发布集合媒体信息
     * @param $params
     * @param $type
     * @return mixed
     */
    public static function sendMediaGroup($params,$type = 0)
    {
        $token = self::token($type);
        $result = json_decode(http_post("https://api.telegram.org/bot{$token}/sendMediaGroup",$params),true);
        if ($result && $result['ok'] === true){
            return $result;
        }
        return false;
    }

    /**
     * 获取文件的地址
     * @param $file_id
     * @param $
     * @param int $type
     * @return mixed
     */
    public static function getFile($file_id,$type = 0)
    {
        $token = self::token($type);
        $result = json_decode(http_get("https://api.telegram.org/bot{$token}/getFile?file_id={$file_id}"),true);
        if ($result && $result['ok'] === true){
            return "https://api.telegram.org/file/bot{$token}/{$result['result']['file_path']}";
        }
        return false;
    }

    /**
     * 获取频道的ID
     * @param $channel_name
     * @param int $type
     * @return mixed
     */
    public static function getChat($channel_name,$type = 0)
    {
        $token = self::token($type);
        $result = json_decode(http_get("https://api.telegram.org/bot{$token}/getChat?chat_id={$channel_name}"),true);
        if ($result && $result['ok'] === true){
            return $result['result'];
        }
        return false;
    }
}