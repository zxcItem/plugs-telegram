<?php

declare (strict_types=1);

namespace plugin\telegram\model;

use think\admin\Model;

/**
 * 图文主模型
 * @class PluginTelegramChannelContent
 * @package plugin\telegram\model
 */
class PluginTelegramChannelContent extends Model
{
    
    public function media()
    {
        return $this->hasMany(PluginTelegramContentMedia::class,'media_group_id','media_group_id');
    }
}