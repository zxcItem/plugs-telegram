<?php


namespace plugin\telegram\controller\api;


use plugin\telegram\service\TelegramApi;
use think\admin\Controller;
use think\exception\HttpResponseException;

/**
 * Telegram API
 * Class Data
 * @package plugin\telegram\controller\api
 */
class Data extends Controller
{

    /**
     * 获取频道信息
     */
    public function getChat()
    {
        try {
            $map = $this->_vali(['chat_id.require'=>'chat_id不可为空！']);
            $this->success('获取成功',TelegramApi::getChat($map['chat_id']));
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}