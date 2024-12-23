<?php
/**
 * 作者:郭磊
 * 邮箱:174000902@qq.com
 * 电话:15210720528
 * Git:https://github.com/guolei19850528/laravel-brhk
 */

namespace Guolei19850528\Laravel\Brhk;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

/**
 * 博瑞皓科 收款云音箱云喇叭 Api Class
 *
 * @see https://www.yuque.com/lingdutuandui/ugcpag/umbzsd?
 */
class Speaker
{
    /**
     * @var string
     */
    protected string $baseUrl = '';

    /**
     * @var string|int
     */
    protected string|int $id = '';

    /**
     * @var string
     */
    protected string $token = '';

    /**
     * @var string|int
     */
    protected string|int $version = '';

    public function getBaseUrl(): string
    {
        if (\str($this->baseUrl)->endsWith('/')) {
            return \str($this->baseUrl)->substr(0, -1)->toString();
        }
        return $this->baseUrl;
    }

    public function setBaseUrl(string $baseUrl): Speaker
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    public function getId(): int|string
    {
        return $this->id;
    }

    public function setId(int|string $id): Speaker
    {
        $this->id = $id;
        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): Speaker
    {
        $this->token = $token;
        return $this;
    }

    public function getVersion(): int|string
    {
        return $this->version;
    }

    public function setVersion(int|string $version): Speaker
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @param string|int $id
     * @param string $token
     * @param string|int $version
     * @param string $baseUrl
     */
    public function __construct(
        string|int $id = '',
        string     $token = '',
        string|int $version = '1',
        string     $baseUrl = 'https://speaker.17laimai.cn/'
    )
    {
        $this->setId($id);
        $this->setToken($token);
        $this->setVersion($version);
        $this->setBaseUrl($baseUrl);
    }

    /**
     * 通知语音播报（不支持WIFI版，流量版专用）
     * 将通知消息提交到云音箱服务器、服务器将支付结果推送给云音箱，云音箱接收后播报。
     * 备注：该接口为流量版（2G\4G）音箱专用接口，通过流量版（2G\4G）音箱自带的TTS播放，WIFI版音箱不可用
     * @see https://www.yuque.com/lingdutuandui/ugcpag/umbzsd
     * @param string $message 通知消息内容， 可推送任意中文（多音字可能有误差）、阿拉伯数字、英文字母，限制64个字符以内  。 数字处理策略见3.1.1备注
     * 如果需要断句，则添加逗号“,”编码格式UTF-8
     * @param array|Collection|null $options Guzzle 请求选项
     * @param string $url url
     * @param array|Collection|null $urlParameters
     * @param \Closure|null $responseHandler
     * @return bool
     */
    public function notify(
        string                $message = '',
        string                $url = '/notify.php',
        array|Collection|null $urlParameters = [],
        array|Collection|null $options = [],
        \Closure|null         $responseHandler = null
    ): bool
    {
        $data = \collect([
            "id" => $this->getId(),
            "token" => $this->getToken(),
            "version" => $this->getVersion(),
            "message" => $message,
        ]);
        $options = \collect($options);
        $urlParameters = \collect($urlParameters);
        $response = Http::baseUrl($this->getBaseUrl())
            ->withOptions($options->toArray())
            ->withUrlParameters($urlParameters->toArray())
            ->post($url, $data->toArray());
        if ($responseHandler instanceof \Closure) {
            return \value($responseHandler($response));
        }
        if ($response->ok()) {
            $json = $response->json();
            if (Validator::make($json, ['errcode' => 'required|integer|size:0'])->messages()->isEmpty()) {
                return true;
            }
        }
        return false;
    }
}
