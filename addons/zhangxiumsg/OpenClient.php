<?php
namespace addons\zhangxiumsg;
class OpenClient
{
    const VERSION = "1.0";
    protected $auth_code_url = "";
    protected $access_token_url = "";
    protected $refresh_token_url = "";
    public $gateway_url = 'http://easy.ezhangxiu.com/openapi'; // 请求基本路径
    protected $redirect_uri = ''; // 登录授权回调地址
    protected $app_key = '90000143442610855058'; // app_key
    protected $app_secret = '16e78ce323545e323a4fe9854a8a3b9f'; // app_secret
    protected $scope = ''; // 授权域
    protected $connect_timeout = 30; // 在发起连接前等待的时间，如果设置为0，则无限等待
    protected $read_timeout = 600; // 设置CURL允许执行的最长秒数。
    protected $timeout = 120; // 设置url最长有效秒数，防止url被截取后执行。
    public $access_token = '4BFB3284E2DB57A2932ADEC984EBB991';
    private $url = ''; // 访问路径
    private $urlParams = ''; // 访问参数
    private $sysParams = [];
    private $apiParams = [];
    private $request_url = '';//请求路径

    /**
     * 初始化
     * @param string $app_key 应用KEY
     * @param string $app_secret 应用密钥
     * @param string $access_token 应用TOKEN
     * @throws \Exception
     */
    public function __construct()
    {
        $app_key = trim($this->app_key);
        $app_secret = trim($this->app_secret);
        if (empty($app_key) || empty($app_secret)) {
            throw new \Exception('不合法的调用');
        }
        $this->app_key = $app_key;
        $this->app_secret = $app_secret;
        $this->access_token = trim($this->access_token);
    }

    /**
     * 获取请求路径
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->request_url;
    }

    /**
     * 生成签名
     * 签名中不包含文件类型的参数，即@前缀
     *
     * @param $params array 参数
     * @return string 签名
     */
    protected function generateSign($params)
    {
        ksort($params);
        $stringToBeSigned = $this->app_secret;
        foreach ($params as $k => $v) {
            if ("@" != substr($v, 0, 1)) {
                $stringToBeSigned .= "$k$v";
            }
        }
        unset($k, $v);
        $stringToBeSigned .= $this->app_secret;
        return strtoupper(md5($stringToBeSigned));
    }

    /**
     * 产生随机字符串，不长于32位
     *
     * @param int $length
     * @return string
     */
    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 批量调用之添加请求
     * @param $apiParams
     * @param array $sysParams
     * @return array|mixed
     */
    public function addRequest($apiParams, $sysParams = [])
    {
        $this->sysParams = [
            'app_key' => $this->app_key,
            'token' => $this->access_token,
            'v' => self::VERSION,
            'timestamp' => time(),
            'timeout' => $this->timeout,
            'noncestr' => self::getNonceStr()
        ];
        $this->apiParams = [];
        // 组装参数
        $sysParams = array_merge($sysParams, $this->sysParams);
        $apiParams = array_merge($apiParams, $this->apiParams);
        // 签名
        $sysParams["sign"] = $this->generateSign(array_merge($apiParams, $sysParams));
        return ['sysParams' => $sysParams, 'apiParams' => $apiParams];
    }

    /**
     * 发送批量请求
     * @param $request
     * @return array|mixed
     */
    public function batchExecute($request)
    {
        $this->sysParams = [
            'app_key' => $this->app_key,
            'token' => $this->access_token,
            'v' => self::VERSION,
            'timestamp' => time(),
            'timeout' => $this->timeout,
            'noncestr' => self::getNonceStr()
        ];
        // 系统参数放入GET请求串
        $requestUrl = $this->gateway_url . "/batch" . "?";
        foreach ($this->sysParams as $sysParamKey => $sysParamValue) {
            $requestUrl .= "$sysParamKey=" . urlencode($sysParamValue) . "&";
        }
        $request_url = substr($requestUrl, 0, -1);
        $request = base64_encode(json_encode($request));
        // 系统参数放入GET请求串
        $this->request_url = $request_url;
        // 发起HTTP请求
        try {
            $resp = $this->curl($request_url, ['request' => $request]);
        } catch (\Exception $e) {
            $result = ['code' => $e->getCode(), 'msg' => $e->getMessage()];
            return $result;
        }
        $respObject = json_decode($resp, true);
        if (null === $respObject) {
            $respObject = $resp;
        }
        return $respObject;
    }

    /**
     * 拼接请求路径，执行请求
     * @param array $apiParams 业务参数
     * @param array $sysParams 系统参数
     * @return array|mixed
     */
    public function execute($apiParams, $sysParams = [])
    {
        $this->sysParams = [
            'app_key' => $this->app_key,
            'token' => $this->access_token,
            'v' => self::VERSION,
            'timestamp' => time(),
            'timeout' => $this->timeout,
            'noncestr' => self::getNonceStr()
        ];
        $this->apiParams = [];
        // 组装参数
        $sysParams = array_merge($sysParams, $this->sysParams);
        $apiParams = array_merge($apiParams, $this->apiParams);
        // 签名
        $sysParams["sign"] = $this->generateSign(array_merge($apiParams, $sysParams));
        // 系统参数放入GET请求串
        $requestUrl = $this->gateway_url . "?";
        foreach ($sysParams as $sysParamKey => $sysParamValue) {
            $requestUrl .= "$sysParamKey=" . urlencode($sysParamValue) . "&";
        }
        $request_url = substr($requestUrl, 0, -1);
        $this->request_url = $request_url;
        //var_dump($apiParams);die;
        // 发起HTTP请求
        try {
            $resp = $this->curl($request_url, $apiParams);
        } catch (\Exception $e) {
            $result = ['code' => $e->getCode(), 'msg' => $e->getMessage()];
            return $result;
        }
        $respObject = json_decode($resp, true);
        if (null === $respObject) {
            $respObject = $resp;
        }
        return $respObject;
    }

    /**
     * 发送请求
     *
     * @param $url string 请求路径
     * @param array $postFields
     * @return mixed
     * @throws \Exception
     */
    protected function curl($url, $postFields = null)
    {
        $this->url = $url;
        $this->urlParams = $postFields;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->read_timeout) {
            // 设置cURL允许执行的最长秒数。
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->read_timeout);
        }
        if ($this->connect_timeout) {
            // 在发起连接前等待的时间，如果设置为0，则无限等待
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connect_timeout);
        }
        if (is_array($postFields) && 0 < count($postFields)) {
            $postBodyString = "";
            $postMultipart = false;
            foreach ($postFields as $k => $v) {
                if ("@" != substr($v, 0, 1)) // 判断是不是文件上传
                {
                    $postBodyString .= "$k=" . urlencode($v) . "&";
                } else // 文件上传用multipart/form-data，否则用www-form-urlencoded
                {
                    $postMultipart = true;
                }
            }
            unset($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            if ($postMultipart) {
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            } else {
                $header = array("content-type: application/x-www-form-urlencoded; charset=UTF-8");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
            }
        }
        $reponse = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch), 500);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new \Exception($reponse, $httpStatusCode);
            }
        }
        curl_close($ch);
        return $reponse;
    }

    /**
     * combineURL 拼接url
     *
     * @param string $baseURL 基于的url
     * @param array $paramArr 参数列表数组
     * @return string 返回拼接的url
     */
    public function combineURL($baseURL, $paramArr)
    {
        if (strpos($baseURL, '?') === false) {
            $combined = $baseURL . "?";
        } else {
            $combined = $baseURL . "&";
        }
        $valueArr = [];
        foreach ($paramArr as $key => $val) {
            $valueArr[] = "$key=$val";
        }
        $keyStr = implode("&", $valueArr);
        $combined .= ($keyStr);
        return $combined;
    }
}