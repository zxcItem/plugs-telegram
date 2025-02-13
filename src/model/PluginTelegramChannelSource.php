<?php

declare (strict_types=1);

namespace plugin\telegram\model;


/**
 * 网络素材频道
 * @class PluginTelegramChannel
 * @package plugin\telegram\model
 */
class PluginTelegramChannelSource extends Abs
{
    /**
     * 更新字段
     * @var string
     */
    protected $updateTime = 'update_at';

    /**
     * 关联频道
     * @return \think\model\relation\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(PluginTelegramChannel::class,'release_channel_id','channel_id');
    }
}