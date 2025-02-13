<?php

declare (strict_types=1);

namespace plugin\telegram\model;


/**
 * 网络资源链接模型
 * @class PluginTelegramResourcesLink
 * @package plugin\telegram\model
 */
class PluginTelegramResourcesLink extends Abs
{

    /**
     * 关联频道
     * @return \think\model\relation\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(PluginTelegramChannelSource::class,'release_channel_id','channel_id');
    }
}