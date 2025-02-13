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

    public static function getChannelID()
    {
        return self::mk()->where('status',1)->column('channel_title','channel_id');
    }
}