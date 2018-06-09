<?php
/**
 * AliPay.php
 *
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace data\service\Upload;
require_once 'data/extend/aliyun-oss-php-sdk/autoload.php';
require_once 'data/extend/aliyun-oss-php-sdk/src/OSS/OssClient.php';

use data\service\Upload\UploadParam;
use OSS\OssClient;
use think\Request;

use data\extend\path_to_sdk\src\Qiniu\Auth;
use data\extend\path_to_sdk\src\Qiniu\Storage\UploadManager;
use Qiniu\Config;
use data\extend\path_to_sdk\src\Qiniu\Storage\BucketManager;
/**
 * 功能说明：阿里云存储上传
 */

class AliyunImg extends UploadParam{
    function __construct(){
        parent::__construct();
    }

    public function index(){
        //防止默认目录错误
    }
    /**
     * 阿里云基本设置
     * @return unknown
     */
    public function getQiniuConfig(){
        //用于签名的公钥
        $qiniu_config['Accesskey']  = $this->Accesskey;
        //用于签名的私钥
        $qiniu_config['Secretkey']  = $this->Secretkey;
        //存储空间名称
        $qiniu_config['Bucket']     = $this->Bucket;
        //七牛用户自定义访问域名
        $qiniu_config['QiniuUrl']   = $this->QiniuUrl;
        return $qiniu_config;
    }
    /**
     * 设置阿里云参数配置
     * @param unknown $filePath  上传图片路径
     * @param unknown $key 上传到七牛后保存的文件名
     */
    public function setQiniuUplaod($filePath, $key){
        $config = $this->getQiniuConfig();
        //Access Key 和 Secret Key
        $accessKey = $config["Accesskey"];
        $secretKey = $config["Secretkey"];
        //构建鉴权对象
        $ossClient = new OssClient($config['Accesskey'],$config['Secretkey'],$config['QiniuUrl']);
        //要上传的空间
        $bucket = $config["Bucket"];
        $res = $ossClient->uploadFile($bucket,$key,$filePath);
        $domain = "";
        if (empty($res)) {
            return ["code"=>false,"path"=>"","domain"=>"", "bucket"=>""];
        } else {
            //返回图片的完整URL
            return ["code"=>true,"path"=>$res['info']['url'],"domain"=>$this->QiniuUrl, "bucket"=>$this->Bucket];
        }
    }

    /**
     * 删除阿里图片
     * @param unknown $key
     */
    public function deleteQiNiuImage($key,$daomin){

        $config = $this->getQiniuConfig();
        //Access Key 和 Secret Key
        $accessKey = $config["Accesskey"];
        $secretKey = $config["Secretkey"];
        //要上传的空间
        $bucket = $config["Bucket"];
        //构建鉴权对象
        $ossClient = new OssClient($config['Accesskey'],$config['Secretkey'],$config['QiniuUrl']);
        //
        $res = $ossClient->deleteObject($bucket,$key);

        return array('code' => 1, 'message' => '删除成功');

    }
}