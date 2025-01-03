<?php

declare (strict_types=1);

namespace plugin\telegram\model;


/**
 * 账号管理
 * Class PluginTelegramAccount
 * @package plugin\telegram\model
 */
class PluginTelegramAccount extends Abs
{

    public static function getTelegramId($TelegramId)
    {
        return self::mk()->where('telegram_id',$TelegramId)->column('phone_number,api_id,api_hash','telegram_id');
    }
}