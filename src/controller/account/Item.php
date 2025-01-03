<?php

declare (strict_types=1);

namespace plugin\telegram\controller\account;

use danog\MadelineProto\API;
use plugin\telegram\model\PluginTelegramAccount;
use plugin\telegram\service\AuthTelegramService;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\exception\HttpResponseException;
use function danog\MadelineProto\phoneLogin;

/**
 * Class Item
 * @package plugin\telegram\controller\account
 */
class Item extends Controller
{

    /**
     * 会员用户管理
     * @return void
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->type = $this->get['type'] ?? 'index';
        PluginTelegramAccount::mQuery()->layTable(function () {
            $this->title = '用户管理';
        }, function (QueryHelper $query) {
            $query->equal('username,phone_number')->dateBetween('create_time');
            $query->where(['status' => intval($this->type === 'index'), 'deleted' => 0]);
        });
    }

    /**
     * 添加账号
     * @auth true
     */
    public function add()
    {
        PluginTelegramAccount::mForm('form');
    }

    /**
     * 编辑账号
     * @auth true
     */
    public function edit()
    {
        PluginTelegramAccount::mForm('form');
    }

    /**
     * 修改账号状态
     * @auth true
     */
    public function state()
    {
        PluginTelegramAccount::mSave($this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 表单数据处理
     * @param array $data
     */
    protected function _form_filter(array &$data)
    {

    }

    /**
     * 删除投票项目
     * @auth true
     */
    public function remove()
    {
        PluginTelegramAccount::mDelete();
    }

    // 初始化 MadelineProto
    private function initMadelineProto($session_file)
    {
        return new API($session_file);
    }

    /**
     * 登录方法，渲染验证码输入表单
     */
    public function login()
    {
        try {
            $data = $this->_vali([
                'telegram_id.require' => '账号ID不可为空！',
                'phone_number.require' => '注册手机不可为空！',
            ]);
            $session_file = "session_{$data['telegram_id']}.madeline";
            if (!file_exists($session_file)) {
//                $this->MadelineProto = $this->initMadelineProto($session_file);
//                $this->MadelineProto->phoneLogin($data['phone_number']);
                $this->fetch('sms',['vo'=>$data]);
            }else{
                $this->success('已经授权，无需重新登录');
            }
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * 提交验证码，完成登录
     */
    public function submitCode()
    {
        try {
            $data = $this->_vali([
                'code.require'         => '验证码不可为空',
                'telegram_id.require'  => '账号ID不可为空！',
                'phone_number.require' => '注册手机不可为空！',
            ]);
            $session_file = "session_{$data['telegram_id']}.madeline";  // 获取会话文件名
            // 重新初始化MadelineProto对象
//            $this->MadelineProto = $this->initMadelineProto($session_file);
//            // 使用验证码完成登录
//            $this->MadelineProto->completePhoneLogin($data['code']);
            $this->success('授权成功，登录完成！');
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}