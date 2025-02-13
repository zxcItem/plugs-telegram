<?php

declare (strict_types=1);

namespace plugin\telegram\controller\api;

use plugin\telegram\service\ConfigService;
use think\admin\Controller;
use think\admin\service\QueueService;
use think\exception\HttpResponseException;

/**
 * Telegram API WebHook
 * @package plugin\telegram\controller\api
 */
class Hook extends Controller
{

    /**
     * 获取Telegram推送内容
     */
    public function hook()
    {
        try {
            // 获取请求的数据
            $result = $this->request->post('');
            $queue = QueueService::register('Telegram WebHook推送数据', 'plugin:telegram:hook', 0, ['data'=>$result]);
            $this->success('创建任务成功！', $queue->code);
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            trace_file($exception);
            $this->error($exception->getMessage());
        }
    }


}