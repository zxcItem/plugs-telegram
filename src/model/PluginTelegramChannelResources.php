<?php

declare (strict_types=1);

namespace plugin\telegram\model;


/**
 * 发布素材资源管理
 * @class PluginTelegramChannelResources
 * @package plugin\telegram\model
 */
class PluginTelegramChannelResources extends Abs
{
    
    public function media()
    {
        return $this->hasMany(PluginTelegramResourcesMedia::class,'media_group_id','media_group_id');
    }

    public function source()
    {
        return $this->belongsTo(PluginTelegramChannelSource::class,'source_channel_id','channel_id');
    }

    public function channel()
    {
        return $this->belongsTo(PluginTelegramChannel::class,'channel_id','channel_id');
    }
}