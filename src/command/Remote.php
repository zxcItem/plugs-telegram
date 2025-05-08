<?php


namespace plugin\telegram\command;

use plugin\telegram\model\PluginTelegramResourcesMedia;
use plugin\telegram\service\TelegramApi;
use think\admin\Command;
use think\console\Input;
use think\console\Output;

class Remote extends Command
{

    /**
     * 指令参数配置
     * @return void
     */
    public function configure()
    {
        $this->setName('plugin:telegram:Remote')->setDescription('自动保存素材');
    }

    /**
     * 执行指令
     * @param \think\console\Input $input
     * @param \think\console\Output $output
     * @throws \think\admin\Exception
     * @throws \think\db\exception\DbException
     */
    protected function execute(Input $input, Output $output)
    {
        $group_id = $this->queue->data['group_id'];
        $list = PluginTelegramResourcesMedia::mk()->whereIn('media_group_id',$group_id)->select()->toArray();
        [$total, $count] = [count($list), 0];
        foreach ($list as $media) try {
            $this->queue->message($total, ++$count, "刷新素材 [{$media['id']}] 数据...");
            $file_path = TelegramApi::getFile($media['thumbnail']);
            $imageData = file_get_contents($file_path);
            if ($imageData !== false) {
                $base64Image = "data:image/png;base64,".base64_encode($imageData);
                $file = self::upload($base64Image,$media['source_channel_id']);$media->where('id',$media['id'])->save(['file_url'=>$file]);
            }
            $this->queue->message($total, $count, "刷新素材 [{$media['id']}] 数据成功", 1);
        } catch (\Exception $exception) {
            $this->queue->message($total, $count, "刷新素材 [{$media['id']}] 数据失败, {$exception->getMessage()}", 1);
        }
        $this->setQueueSuccess("此次共处理 {$total} 个刷新操作。");
    }


    protected function upload($base64,$channel_id)
    {
        $result =json_decode(http_post("https://resource.mrzhou.top/plugin-telegram/api.data/image",['base64'=>$base64,'channel_id'=>$channel_id]),true);
        if ($result && $result['code'] == 1){
            return $result['data']['url'];
        }
    }
}