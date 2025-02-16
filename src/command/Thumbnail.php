<?php


namespace plugin\telegram\command;

use plugin\telegram\model\PluginTelegramResourcesMedia;
use plugin\telegram\service\RedisService;
use plugin\telegram\service\TelegramApi;
use think\admin\Command;
use think\console\Input;
use think\console\Output;

class Thumbnail extends Command
{

    /**
     * 指令参数配置
     * @return void
     */
    public function configure()
    {
        $this->setName('plugin:telegram:thumbnail')->setDescription('更新文件的封面图信息');
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
        [$total, $count] = [10, 0];
        foreach (PluginTelegramResourcesMedia::mk()->where('status',0)->limit(10)->field('id,thumbnail')->cursor() as $media) try {
            $this->queue->message($total, ++$count, "刷新素材 [{$media['id']}] 数据...");
            $file_path = TelegramApi::getFile($media['thumbnail']);
            $media->save(['status'=>1,'local_url'=>$file_path]);
            $imageData = file_get_contents($file_path);
            if ($imageData !== false) {
                $base64Image = "data:image/png;base64,".base64_encode($imageData);
                if (!self::redisCache($media['thumbnail'])){
                    RedisService::instance()->set("MediaThumbnail:{$media['thumbnail']}",$base64Image);
                }
            }
            $this->queue->message($total, $count, "刷新素材 [{$media['id']}] 数据成功", 1);
        } catch (\Exception $exception) {
            $this->queue->message($total, $count, "刷新素材 [{$media['id']}] 数据失败, {$exception->getMessage()}", 1);
        }
        $this->setQueueSuccess("此次共处理 {$total} 个刷新操作。");
    }

    /**
     * 检测是否已存在thumbnail
     * @param $thumbnail
     * @return bool
     */
    protected function redisCache($thumbnail)
    {
        $base64Image = RedisService::instance()->get("MediaThumbnail:{$thumbnail}");
        return $base64Image ? true : false;
    }
}