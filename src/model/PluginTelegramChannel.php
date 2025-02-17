<?php

declare (strict_types=1);

namespace plugin\telegram\model;


/**
 * 经营管理频道
 * @class PluginTelegramChannel
 * @package plugin\telegram\model
 */
class PluginTelegramChannel extends Abs
{

    public static function getChannelID($field = '*')
    {
        return self::mk()->where('status',1)->column($field,'channel_id');
    }
}