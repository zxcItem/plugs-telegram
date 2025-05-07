<?php


namespace plugin\telegram\controller\api;


use plugin\telegram\service\TelegramApi;
use think\admin\Controller;
use think\admin\Storage;
use Exception;
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

    /**
     * Base64 图片上传
     */
    public function image()
    {
        try {
            $data = $this->_vali(['base64.require' => '图片内容不为空！','channel_id.default'=>date('ymd')]);
            if (preg_match($preg = '|^data:image/(.*?);base64,|i', $data['base64'])) {
                [$ext, $img] = explode('|||', preg_replace($preg, '$1|||', $data['base64']));
                if (empty($ext) || !in_array(strtolower($ext), ['png', 'jpg', 'jpeg'])) {
                    $this->error('图片格式异常！');
                }
                $name = self::name($img, $ext,abs($data['channel_id']).'/'.date('ymd'));
                $info = Storage::instance()->set($name, base64_decode($img));
                $this->success('图片上传成功！', ['url' => $info['url']]);
            } else {
                $this->error('解析内容失败！');
            }
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (Exception $exception) {
            trace_file($exception);
            $this->error($exception->getMessage());
        }
    }

    /**
     * 获取文件相对名称
     * @param string $url 文件访问链接
     * @param string $ext 文件后缀名称
     * @param string $pre 文件存储前缀
     * @param string $fun 名称规则方法
     * @return string
     */
    public static function name(string $url, string $ext = '', string $pre = '', string $fun = 'md5'): string
    {
        [$hah, $ext] = [$fun($url), trim($ext ?: pathinfo($url, 4), '.\\/')];
        $attr = [trim($pre, '.\\/'), substr($hah, 2, 30)];
        return trim(join('/', $attr), '/') . '.' . strtolower($ext ?: 'tmp');
    }

    /**
     * 二进制文件上传
     * @throws \think\admin\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function upload()
    {
        $file = $this->request->file('file');
        if (empty($file)) $this->error('文件上传异常！');
        $extension = strtolower($file->getOriginalExtension());
        if (in_array($extension, ['php', 'sh'])) $this->error('禁止上传此类文件！');
        $bina = file_get_contents($file->getRealPath());
        $name = Storage::name($file->getPathname(), $extension, '', 'md5_file');
        $info = Storage::instance()->set($name, $bina, false, $file->getOriginalName());
        if (is_array($info) && isset($info['url'])) {
            $this->success('文件上传成功！', $info);
        } else {
            $this->error('文件上传失败！');
        }
    }
}