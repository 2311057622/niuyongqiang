<?php
/**
 * Index.php
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
namespace app\api\controller;

use data\service\Config;
use data\extend\WchatOauth;
use data\service\GoodsCategory;
use data\service\Platform;
use data\service\GoodsBrand;
use data\service\Goods;
use data\service\Member;
use data\service\Pintuan;
use data\service\GoodsGroup;
use think\Cache;

class Index extends BaseController
{

    /**
     * 商品楼层板块每层显示商品个数
     *
     * @var unknown
     */
    public $category_good_num = 4;

    /**
     * 商品标签板块每层显示商品个数
     *
     * @var unknown
     */
    public $recommend_goods_num = 4;

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 查询首页数据
     */
    public function getIndexData()
    {
        $title = "首页广告微展示,adv_index:首页轮播广告位,adv_new:首页新品推荐下广告位,adv_brand:首页商品推荐广告位";
        
        // 首页公告
        $platform = new Platform();
        $notice = $platform->getNoticeList(1, '', [
            "shop_id" => $this->instance_id
        ], "sort");
        
        // 首页楼层
        $good_category = new GoodsCategory();
        $block_list = $good_category->getGoodsCategoryBlockQuery(0, 4);
        
        // 拼团推荐
        if (IS_SUPPORT_PINTUAN == 1) {
            $pintuan = new Pintuan();
            $pintuan_condition["npg.is_open"] = 1;
            $pintuan_condition["npg.is_show"] = 1;
            $pintuan_list = $pintuan->getTuangouGoodsList(1, 5, $pintuan_condition, 'npg.create_time desc');
        } else {
            $pintuan_list = array();
        }
        
        // 标签板块
        $goods_platform = new Platform();
        $goods_platform_list = $goods_platform->getRecommendGoodsList(0, 4);
        
        // 获取当前时间
        $current_time = $this->getCurrentTime();
        
        // 限时折扣列表
        $goods = new Goods();
        $condition['status'] = 1;
        $condition['ng.state'] = 1;
        $discount_list = $goods->getDiscountGoodsList(1, 2, $condition, 'end_time');
        if (! empty($discount_list['data'])) {
            foreach ($discount_list['data'] as $k => $v) {
                $v['discount'] = str_replace('.00', '', $v['discount']);
            }
        }
        
        // 首页轮播图
        $platform = new Platform();
        $plat_adv_list = $platform->getPlatformAdvPositionDetail(1105);
        $base_url = "https://" . $_SERVER['SERVER_NAME'];
        if (! empty($plat_adv_list)) {
            foreach ($plat_adv_list["adv_list"] as $k => $v) {
                if(strpos($plat_adv_list["adv_list"][$k]["adv_image"], 'https://') == false && 
                strpos($plat_adv_list["adv_list"][$k]["adv_image"], 'http://') == false ){
                    $url = $base_url . "/" . $plat_adv_list["adv_list"][$k]["adv_image"];
                    $plat_adv_list["adv_list"][$k]["adv_image"] = $url;
                }
            }
        }
        
        // 首页优惠券
        $member = new Member();
        $coupon_list = $member->getMemberCouponTypeList($this->instance_id, $this->uid);
        
        // 首页新品推荐下方广告位
        $index_adv_one = $platform->getPlatformAdvPositionDetail(1188);
        
        // 首页品牌推荐下方广告位
        $index_adv_two = $platform->getPlatformAdvPositionDetail(1189);
        $data = array(
            'adv_index' => $plat_adv_list,
            'adv_new' => $index_adv_one,
            'adv_brand' => $index_adv_two
        );
        
        $result = array(
            "notice" => $notice,
            "block_list" => $block_list,
            "goods_platform_list" => $goods_platform_list,
            "adv_list" => $data,
            "coupon_list" => $coupon_list,
            "discount_list" => $discount_list,
            "pintuan_list" => $pintuan_list,
            "current_time" => $current_time
        );
        return $this->outMessage("首页数据", $result);
    }

    /**
     * 查询首页轮播图，APP用
     * 创建时间：2018年3月20日14:34:27
     *
     * @return Ambigous <\think\response\Json, string>
     */
    public function getHomePageShufflingFigureData()
    {
        // 轮播图，商品一级分类5个，商品标签
        // 首页轮播图
        $platform = new Platform();
        $plat_adv_list = $platform->getPlatformAdvPositionDetail(1105);
        if (! empty($plat_adv_list)) {
            foreach ($plat_adv_list["adv_list"] as $k => $v) {
                if (strpos($plat_adv_list["adv_list"][$k]["adv_image"], "http://") === false && 
                strpos($plat_adv_list["adv_list"][$k]["adv_image"], "https://") === false) {
                    $plat_adv_list["adv_list"][$k]["adv_image"] = getBaseUrl() . "/" . $plat_adv_list["adv_list"][$k]["adv_image"];
                }
            }
        }
        
        return $this->outMessage("APP首页轮播图", $plat_adv_list);
    }

    /**
     * 查询首页一级商品分类，APP用，限定5个
     * 创建时间：2018年3月20日14:37:53
     *
     * @return Ambigous <\think\response\Json, string>
     */
    public function getHomePageGoodsCategoryList()
    {
        $goods_category = new GoodsCategory();
        $res = $goods_category->getGoodsCategoryList(1, 5, [
            'pid' => 0,
            'category_pic' => [
                '<>',
                ''
            ],
            'is_visible' => 1
        ], "sort asc", "category_id,category_name,short_name,category_pic");
        if (! empty($res['data'])) {
            foreach ($res['data'] as $k => $v) {
                if (! empty($res['data'][$k]['category_pic'])) {
                    if (strpos($res['data'][$k]['category_pic'], "http") === false) {
                        $res['data'][$k]['category_pic'] = getBaseUrl() . "/" . $res['data'][$k]['category_pic'];
                    }
                }
            }
        }
        return $this->outMessage("APP首页一级商品分类列表", $res);
    }

    /**
     * 查询首页商品标签列表，APP用
     * 创建时间：2018年3月20日15:10:10
     *
     * @return Ambigous <\think\response\Json, string>
     */
    public function getHomePageGoodsGroupList()
    {
        $goods_group = new GoodsGroup();
        $page_index = request()->post("page_index", 1);
        $page_size = request()->post("page_size", PAGESIZE);
        $condition = array();
        $condition['is_visible'] = 1;
        $condition['group_pic'] = [
            '<>',
            ''
        ];
        $res = $goods_group->getGoodsGroupList($page_index, $page_size, $condition, "sort asc", $field = 'group_id,group_name,group_pic,group_dec');
        if (! empty($res['data'])) {
            foreach ($res['data'] as $k => $v) {
                if (! empty($res['data'][$k]['group_pic'])) {
                    if (strpos($res['data'][$k]['group_pic'], "http") === false) {
                        $res['data'][$k]['group_pic'] = getBaseUrl() . "/" . $res['data'][$k]['group_pic'];
                    }
                }
            }
        }
        
        return $this->outMessage("APP首页商品标签列表", $res);
    }

    /**
     * 根据商品标签id查询商品标签信息以及旗下的商品列表实体
     * 创建时间：2018年3月21日15:04:53
     */
    public function getGoodsListByGoodsGroupId()
    {
        $goods = new Goods();
        $group_id = request()->post("group_id", 1);
        $page_index = request()->post("page_index", 1);
        $page_size = request()->post("page_size", PAGESIZE);
        $res = $goods->getGroupGoodsListForApp($page_index, $page_size, $group_id, "goods_id,goods_name,introduction,picture,group_id_array,promotion_price");
        
        return $this->outMessage("APP指定商品标签下的商品列表", $res);
    }

    /**
     * 得到当前时间戳的毫秒数
     *
     * @return number
     */
    public function getCurrentTime()
    {
        $time = time();
        $time = $time * 1000;
        return $time;
    }

    /**
     * 获取分享相关票据
     */
    public function getShareTicket($url)
    {
        $title = "获取微信票据";
        $config = new Config();
        $auth_info = $config->getInstanceWchatConfig(0);
        // 获取票据
        if (! empty($auth_info['value']['appid'])) {
            // 针对单店版获取微信票据
            $wexin_auth = new WchatOauth();
            $signPackage['appId'] = $auth_info['value']['appid'];
            $signPackage['jsTimesTamp'] = time();
            $signPackage['jsNonceStr'] = $wexin_auth->get_nonce_str();
            $jsapi_ticket = $wexin_auth->jsapi_ticket();
            $signPackage['ticket'] = $jsapi_ticket;
            $Parameters = "jsapi_ticket=" . $signPackage['ticket'] . "&noncestr=" . $signPackage['jsNonceStr'] . "&timestamp=" . $signPackage['jsTimesTamp'] . "&url=" . $url;
            $signPackage['jsSignature'] = sha1($Parameters);
            return $this->outMessage($title, $signPackage);
        } else {
            $signPackage = array(
                'appId' => '',
                'jsTimesTamp' => '',
                'jsNonceStr' => '',
                'ticket' => '',
                'jsSignature' => ''
            );
            return $this->outMessage($title, $signPackage, '-9001', "当前微信没有配置!");
        }
    }

    /**
     * 获取首页相关推荐商品
     */
    public function getIndexReconmmendGoods()
    {
        $title = "获取首页相关推荐商品,goods_category_block:首页商品分类楼层,goods_platform_recommend:首页推荐商品列表,goods_brand_list:首页品牌相关列表，显示6个,current_time:当前时间,goods_hot_list:首页商城热卖,goods_recommend_list:首页商城推荐商品列表,goods_discount_list:首页限时周口商品列表";
        // 首页商品分类楼层
        $shop_id = 0;
        $good_category = new GoodsCategory();
        $block_list = $good_category->getGoodsCategoryBlockQuery($shop_id, 4);
        
        // 首页新品推荐列表
        $goods_platform = new Platform();
        $goods_platform_list = $goods_platform->getRecommendGoodsList($shop_id, 4);
        
        // 品牌列表
        $goods_brand = new GoodsBrand();
        $goods_brand_list = $goods_brand->getGoodsBrandList(1, 6, '', 'sort');
        
        // 限时折扣列表
        $goods = new Goods();
        $condition['status'] = 1;
        $condition['ng.state'] = 1;
        $discount_list = $goods->getDiscountGoodsList(1, 6, $condition, 'end_time');
        
        foreach ($discount_list['data'] as $k => $v) {
            $v['discount'] = str_replace('.00', '', $v['discount']);
        }
        // 获取当前时间
        $current_time = $this->getCurrentTime();
        
        // 首页商城热卖
        $val['is_hot'] = 1;
        $goods_hot_list = $goods_platform->getPlatformGoodsList(1, 0, $val);
        
        // 首页商城推荐
        $val1['is_recommend'] = 1;
        $goods_recommend_list = $goods_platform->getPlatformGoodsList(1, 0, $val1);
        $data = array(
            'goods_category_block' => $block_list,
            'goods_platform_recommend' => $goods_platform_list,
            'goods_brand_list' => $goods_brand_list['data'],
            'goods_discount_list' => $discount_list['data'],
            'current_time' => $current_time,
            'goods_hot_list' => $goods_hot_list['data'],
            'goods_recommend_list' => $goods_recommend_list['data'],
            'is_support_pintuan' => IS_SUPPORT_PINTUAN
        );
        return $this->outMessage($title, $data);
    }

    /**
     * 获取限时折扣相关数据
     */
    public function getDiscountData()
    {
        $title = "获取限时折扣相关数据,discount_adv:限时折扣广告位,goods_category_list:限时折扣需要查询一级分类,current_time:获取当前时间";
        // 限时折扣广告位
        $platform = new Platform();
        $discounts_adv = $platform->getPlatformAdvPositionDetail(1163);
        // 限时折扣商品一级分类数据
        $goods_category = new GoodsCategory();
        $goods_category_list_1 = $goods_category->getGoodsCategoryList(1, 0, [
            "is_visible" => 1,
            "level" => 1
        ]);
        $current_time = time() * 1000;
        $data = array(
            'discount_adv' => $discounts_adv,
            'goods_category_list' => $goods_category_list_1,
            'current_time' => $current_time
        );
        return $this->outMessage($title, $data);
    }

    /**
     * 获取限时折扣页面商品数据
     */
    public function getDiscountGoods()
    {
        $title = "获取限时折扣的商品列表，需要必填参数对应商品分类category_id";
        // 对应商品分类id
        $category_id = request()->post('category_id', '0');
        // 对应分页
        $page_index = request()->post("page", 1);
        $goods = new Goods();
        $condition['status'] = 1;
        $condition['ng.state'] = 1;
        if (! empty($category_id)) {
            $condition['category_id_1'] = $category_id;
        }
        $discount_list = $goods->getDiscountGoodsList($page_index, PAGESIZE, $condition, "ng.sort desc,ng.create_time desc");
        foreach ($discount_list['data'] as $k => $v) {
            $v['discount'] = str_replace('.00', '', $v['discount']);
            $v['promotion_price'] = str_replace('.00', '', $v['promotion_price']);
            $v['price'] = str_replace('.00', '', $v['price']);
        }
        return $this->outMessage($title, $discount_list);
    }

    /**
     * 公告详情
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function noticeContent()
    {
        $title = '公告详情';
        $notice_id = request()->post('id', '');
        $goods_platform = new Platform();
        $notice_info = $goods_platform->getNoticeDetail($notice_id);
        
        if (empty($notice_info)) {
            return $this->outMessage($title, '', - 50, '未获取到公告信息');
        }
        
        // 上一篇
        $prev_info = $goods_platform->getNoticeList(1, 1, [
            "id" => array(
                "<",
                $notice_id
            )
        ], "id desc");
        
        // 下一篇
        $next_info = $goods_platform->getNoticeList(1, 1, [
            "id" => array(
                ">",
                $notice_id
            )
        ], "id asc");
        
        $prev_info = array();
        $next_info = array();
        
        if (! empty($prev_info['data']) && ! empty($prev_info['data'][0]) && ! empty($prev_info['data'][0]['id'])) {
            unset($prev_info['data'][0]['notice_content']);
            unset($next_info['data'][0]['notice_content']);
        }
        $data = array(
            'notice_info' => $notice_info,
            'prev_info' => $prev_info['data'][0],
            'next_info' => $next_info['data'][0]
        );
        return $this->outMessage($title, $data);
    }

    /**
     * 公告列表
     */
    public function noticeList()
    {
        $title = '公告列表';
        $page = request()->post("page", 1);
        $goods_platform = new Platform();
        $article_list = $goods_platform->getNoticeList($page, 0, '', 'sort desc');
        return $this->outMessage($title, $article_list);
    }

    /**
     * 获取网址前缀，APP用
     * 创建时间：2018年3月27日16:57:49
     * 
     * @return string
     */
    public function getSitePrefix()
    {
        return getBaseUrl();
    }
    
    /**
     * 获取验证码
     */
    public function getVertification()
    {
        $title = '获取验证码';
        $key = request()->post('key', '-504*504');
        $key = md5('@' . $key . '*');
        $code = '';
        for($i = 0; $i < 4; $i++){
            $code .= (int)rand(0, 9);            
        }
        Cache::set($key, $code, 60);
        return $this->outMessage($title, $code);
    }
}