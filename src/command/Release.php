<?php


namespace plugin\telegram\command;

use plugin\telegram\model\PluginTelegramChannelResources;
use plugin\telegram\model\PluginTelegramResourcesMedia;
use plugin\telegram\service\TelegramApi;
use think\admin\Command;
use think\admin\Exception;
use think\console\Input;
use think\console\Output;

class Release extends Command
{

    /**
     * 指令参数配置
     * @return void
     */
    public function configure()
    {
        $this->setName('plugin:telegram:Release')->setDescription('自动发布素材');
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
            $material = PluginTelegramChannelResources::mk()->where('status',0)->find();
            if ($material){
                $result = self::preview($material['channel_id'],$material['media_group_id']);
                if ($result) $material->save(['status'=>1]);
                $this->setQueueSuccess('素材发布成功！');
            }else{
                $this->setQueueSuccess('暂无素材发布！');
            }
        } catch (Exception $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->setQueueError($exception->getMessage());
        }
    }


    /**
     * 素材发布
     * @param $chat_id
     * @param $group_id
     * @return bool|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function preview($chat_id,$group_id)
    {
        $contents = PluginTelegramResourcesMedia::mk()
            ->where('media_group_id',$group_id)
            ->field('caption,type,media')
            ->order('sort')->select()->map(function ($content){
                if ($content['type'] == 'video/mp4') $content['type'] = 'video';
                $content['parse_mode'] = 'HTML';
                return $content;
            })->toArray();
        return TelegramApi::sendMediaGroup([
            'chat_id'    => $chat_id,
            'media'      => json_encode($contents)
        ],1);
    }
}