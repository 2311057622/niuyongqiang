<?php
// +----------------------------------------------------------------------
// | test [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.zzstudio.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Byron Sampson <xiaobo.sun@gzzstudio.net>
// +----------------------------------------------------------------------
namespace addons\zhangxiumsg;

use data\model\AlbumPictureModel;
use data\model\NsGoodsModel;
use data\model\NsGoodsSkuModel;
use data\model\NsGoodsSpecModel;
use data\model\NsGoodsSpecValueModel;
use data\model\NsOrderGoodsModel;
use data\model\NsOrderModel;
use data\service\Goods as GoodsService;
use think\Config;
use addons\zhangxiumsg\OpenClient;
use think\Db;
use think\Exception;

class Zhangxiumsg extends \addons\Addons
{
    public $info = array(
        'name' => 'zhangxiumsg', // 插件名称标识
        'title' => '掌秀', // 插件中文名
        'description' => '掌秀', // 插件概述
        'status' => 1, // 状态 1启用 0禁用
        'author' => 'niuyongqiang', // 作者
        'version' => '1.0', // 版本号
        'has_addonslist' => 0, // 是否有下级插件 例如：第三方登录插件下有 qq登录，微信登录
        'content' => '', // 插件的详细介绍或使用方法
        'config_hook' => 'zhangxiumsg'
    );
    // 设置文件单独的钩子
    public $table = 'sys_addons_zhangxiu_msg';

//    public $menu_info = array(
//        [
//            'module_name' => '掌秀设置',
//            'parent_module_name' => '掌秀', // 上级模块名称 用来确定上级目录
//            'last_module_name' => '掌秀内容设置', // 上一个菜单名称 用来确定菜单排序
//            'is_menu' => 1, // 是否是菜单
//            'is_dev' => 0, // 是否是开发模式可见
//            'desc' => '掌秀插件菜单', // 菜单描述
//            'module_picture' => '', // 图片（一般为空）
//            'icon_class' => '', // 字体图标class（一般为空）
//            'is_control_auth' => 1, // 是否有控制权限
//            'hook_name' => 'zhangxiumsg'
//        ]
//    );
    // 钩子名称（需要该钩子调用的页面）

    /**
     * 实现第三方钩子
     *
     * @param array $params
     */
    public function zhangxiumsg($params = [])
    {
        $this->fetch('zhangxiumsg');
    }

    /**
     * 掌秀云地址
     * @param array $params
     */
    public function zhangxiuyunArea($params = [])
    {
        $sysParams = [
            'method' => 'front.area.areamap.detail',
        ];
        $apiParams = [
            'area_id' => 10010,
        ];
        $OpenClient = new OpenClient();
        $res = $OpenClient->execute($apiParams, $sysParams);
        echo "<pre>";
        var_dump($res);
        die;


    }

    /**
     * 掌秀云商品列表
     */
    public function zhangxiuyunProductList($apiParams = [])
    {
        $sysParams = [
            'method' => 'customer.product.product.index',
        ];
        $apiParams = [
            //查询字段
            'fields' => isset($apiParams['fields']) ? $apiParams['fields'] : 'product_id, product_code, cat_id, brand_id, line_id, product_name, keyword, unit, big_unit, unit_convert, buy_unit_type, weight, sale_num, stock_num, pur_price, step_price, product_tag, sort_order, putaway_status, is_delete, is_different_price, market_price, cost_price, whole_price, style_name, source_from, updated_at, updated_by, created_by, created_at, cat_name, line_name, brand_name, unit_name, big_unit_name, ext_list, sku_num, sku_info, sku_list, main_pic',
            //当前页
            'page' => isset($apiParams['page']) ? $apiParams['page'] : 1,
            //每页显示条数
            'page_size' => isset($apiParams['page_size']) ? $apiParams['page_size'] : 20,
            //是否has分页
            'use_has_next' => isset($apiParams['use_has_next']) ? TRUE : FALSE,
            //搜索商品标签 0,1,2
            'product_tag' => isset($apiParams['product_tag']) ? $apiParams['product_tag'] : '',
            //搜索商品关键字
            'keyword' => isset($apiParams['keyword']) ? $apiParams['keyword'] : '',
            //搜索商品编码P10001
            'product_code' => isset($apiParams['product_code']) ? $apiParams['product_code'] : '',
            //搜索商品分类ID
            'cat_id' => isset($apiParams['cat_id']) ? $apiParams['cat_id'] : '',
            //搜索商品品牌ID(支持多个品牌搜索)1,2,3
            'brand_id' => isset($apiParams['brand_id']) ? $apiParams['brand_id'] : '',
            //排序字段   whole_price //价格 sale_num, //销量 sort_order //排序值 created_at //新增时间 updated_at //修改时间
            'sort_order' => isset($apiParams['sort_order']) ? $apiParams['sort_order'] : '',
            //desc倒序 asc //升序
            'sort_type' => isset($apiParams['sort_type']) ? $apiParams['sort_type'] : '',
        ];
        $OpenClient = new OpenClient();
        $res = $OpenClient->execute($apiParams, $sysParams);
        echo "<pre>";
        print_r($res);
        $this->assign('data', $res);
        $this->fetch('zhangxiumsg_list');


    }

    /**
     * 掌秀云商品详情
     */
    public function zhangxiuyunProductDetail($apiParams = [])
    {
        $OpenClient = new OpenClient();
        $goods_info = new NsGoodsModel();
        $sysParams = [
            'method' => 'customer.product.product.detail',
        ];
        $apiParams = [
            //商品ID
            'product_id' => isset($apiParams['product_code']) ? $apiParams['product_code'] : "",
            //商品编码
            'product_code' => isset($apiParams['product_code']) ? $apiParams['product_code'] : "",
            //查询字段
            'fields' => isset($apiParams['fields']) ? $apiParams['fields'] : 'product_id, product_code, cat_id, brand_id, line_id, product_name, keyword, unit, big_unit, unit_convert, buy_unit_type, weight, sale_num, stock_num, pur_price, step_prcie, product_tag, sort_order, putaway_status, is_delete, is_different_price, market_price, cost_price, whole_price, style_name, source_from, updated_at, updated_by, created_by, created_at, cat_name, line_name, brand_name, unit_name, big_unit_name, ext_list, sku_num, sku_info, sku_list, detail_info, main_pic, pic_list, enable_min_buy_num, enable_max_buy_num, stock_zero_allow_sell, show_stock_num,detail_url',
        ];
        try {
            $res = $OpenClient->execute($apiParams, $sysParams);
            //ERP 商品同步到商城
            $goods ['external_id'] = $res['data']['product_id'];
            //$goods ['goods_name'] = $res['data']['product_name']; //商品名称
            $goods ['state'] = 0; //商品状态 0下架，1正常，10违规（禁售）
            $goods ['stock'] = $res['data']['stock_num']; //商品库存
            $goods ['market_price'] = $res['data']['market_price']; //市场价
            $goods ['cost_price'] = $res['data']['cost_price']; //成本价
            $goods ['description'] = $res['data']['detail_info']; //商品详情
            $goods ['goods_unit'] = $res['data']['unit_name']; //商品单位

            //获取商品sku名称
            $spec = $res['data']['style_name'];
            $spec_value = $res['data']['sku_info'];
            //处理规格名称
            //拼接多维数组
            $sku_info = array();
            $sku_spec_info = array();
            $goods_spec_format = array();
            foreach ($spec_value as $key => $value) {
                $spec_arr_value[$spec[$key]] = $value;
            }
            $i = 0;
            $y = 0;
            foreach ($spec_arr_value as $key => $val) {
                $goods_spec = new NsGoodsSpecModel();
                $spec_info = $goods_spec->get(['spec_name' => "$key"]);
                //echo Db::table('ns_goods_spec')->getLastSql();die;
                if (empty($spec_info)) { //规格名称不存在
                    $spec_name['spec_name'] = $key;
                    $spec_name['is_visible'] = 1;
                    $spec_name['sort'] = 0;
                    $spec_name['show_type'] = 1;
                    $spec_name['create_time'] = time();
                    $spec_name['is_screen'] = 1;
                    $spec_id = $goods_spec->save($spec_name);//添加规格名称

                    $goods_spec_format[] = array(
                        'spec_name' => $spec_name['spec_name'],
                        'spec_id' => $spec_id,
                    );
                } else {
                    $spec_info->toArray();
                    $spec_id = $spec_info['spec_id'];
                    $spec_name['spec_name'] = $spec_info['spec_name'];
                    $goods_spec_format[] = array(
                        'spec_name' => $spec_name['spec_name'],
                        'spec_id' => $spec_id,
                    );
                }
                $sku_info[$i]['sku_id'] = $spec_id;
                $sku_info[$i]['sku_value'] = $key;
                foreach ($val as $k => $v) {
                    $goods_spec_value = new NsGoodsSpecValueModel();
                    $spec_value = $goods_spec_value->get(['spec_value_name' => "$v", 'spec_id' => $spec_id]);
                    if (empty($spec_value)) {
                        $spec_value_name['spec_id'] = $spec_id;
                        $spec_value_name['spec_value_name'] = $v;
                        $spec_value_name['spec_value_data'] = '';
                        $spec_value_name['is_visible'] = 0;
                        $spec_value_name['sort'] = 255;
                        $spec_value_name['create_time'] = time();
                        $spec_value_id = $goods_spec_value->save($spec_value_name); //添加规格值

                        $goods_spec_format[$i]['value'][] = array(
                            'spec_value_name' => $spec_value_name['spec_value_name'],
                            'spec_name' => $spec_name['spec_name'],
                            'spec_id' => $spec_id,
                            'spec_value_id' => $spec_value_id,
                            'spec_show_type' => 1,
                            'spec_value_data' => '',
                        );
                    } else {
                        $spec_value->toArray();
                        $spec_value_id = $spec_value['spec_value_id'];
                        $goods_spec_format[$i]['value'][] = array(
                            'spec_value_name' => $spec_value['spec_value_name'],
                            'spec_name' => $spec_name['spec_name'],
                            'spec_id' => $spec_id,
                            'spec_value_id' => $spec_value_id,
                            'spec_show_type' => 1,
                            'spec_value_data' => '',
                        );
                    }
                    $sku_spec_info[$y]['spec_value_id'] = $spec_value_id;
                    $sku_spec_info[$y]['spec_value_name'] = $v;
                    $y++;
                }
                $i++;

            }
            $goods ['goods_spec_format'] = json_encode($goods_spec_format, JSON_UNESCAPED_UNICODE);//商品规格
            //判断商品是否存在
            $result = $goods_info->get(['code' => $apiParams['product_code']]);
            $goods_id = $result['goods_id'];
            $goods_info->save($goods, ['code' => $apiParams['product_code']]);
            //删除商品sku
            $goods_sku = new  NsGoodsSkuModel();
            //重组商品sku
            $sku_list = $res['data']['sku_list'];
            if (!empty($sku_list)) {
                $sku_arr = array();
                foreach ($sku_list as $key => $value) {
                    $sku_arr [] = $value['sku_str'];
                }
                $sku_id_arr = array();
                foreach ($sku_arr as $k => $v) {
                    $spec_val_arr = explode(',', $v);
                    foreach ($spec_val_arr as $key => $value) {
                        //多重规格
                        $spec_val = explode(':', $value);
                        $spec_name_val = array();
                        foreach ($sku_info as $sku_val) {
                            if (trim($spec_val[0]) == trim($sku_val['sku_value'])) {
                                $spec_name_val[] = $sku_val['sku_id'];
                            }
                        }
                        foreach ($sku_spec_info as $sku_spec_val) {
                            if (trim($spec_val[1]) == trim($sku_spec_val['spec_value_name'])) {
                                $spec_name_val[] = $sku_spec_val['spec_value_id'];
                            }
                        }
                        $sku_id_arr_[] = implode(':', $spec_name_val);

                    }
                    $sku_id_arr[] = implode(';', $sku_id_arr_);
                    unset($sku_id_arr_);
                }
                //重组sku数组
                $sku_list_arr = array();
                for ($i = 0; $i < count($sku_list); $i++) {
                    //判断数据库是否存在，存在，修改库存
                    $sku_res = $goods_sku->get(['external_sku_id' => $sku_list[$i]['sku_id']]);
                    if (empty($sku_res)) {
                        $sku_list_arr[$i]['external_sku_id'] = $sku_list[$i]['sku_id'];
                        $sku_list_arr[$i]['goods_id'] = $goods_id;
                        $sku_list_arr[$i]['sku_name'] = $sku_list[$i]['style1_value'] . $sku_list[$i]['style2_value'] . $sku_list[$i]['style3_value'];
                        $sku_list_arr[$i]['attr_value_items'] = $sku_id_arr[$i];
                        $sku_list_arr[$i]['attr_value_items_format'] = $sku_id_arr[$i];
                        $sku_list_arr[$i]['market_price'] = $sku_list[$i]['market_price'];
                        $sku_list_arr[$i]['price'] = $sku_list[$i]['whole_price'];
                        $sku_list_arr[$i]['cost_price'] = $sku_list[$i]['cost_price'];
                        $sku_list_arr[$i]['code'] = $sku_list[$i]['sku_code'];
                        $sku_list_arr[$i]['stock'] = $sku_list[$i]['stock'];
                        $sku_list_arr[$i]['create_date'] = time();
                    } else {
                        $sku_list_['stock'] = $sku_list[$i]['stock'];
                        $goods_sku->save($sku_list_, ['sku_id' => $sku_res['sku_id']]);
                    }
                }
                if ($sku_list_arr){
                    $goods_sku->saveAll($sku_list_arr, false);
                }

            }
        } catch (\Exception $e) {
            $msg = $message = $e->getMessage();
            $result = $goods_info->get(['code' => $apiParams['product_code']]);
            $error ['goods_code'] = $result['code'];
            $error ['goods_name'] = $result['goods_name'];
            $error ['message'] = $msg;
            $error ['type'] = '自动导入';
            $error ['create_time'] = time();
            $res = Db::table('ns_goods_error')->insert($error);
        }
    }

    /**
     * 掌秀云订单详情
     */
    public function zhangxiuyunOrderDetail($apiParams = [])
    {
        $sysParams = [
            'method' => 'customer.order.order.info',
        ];
        $apiParams = [
            //需返回的字段列表，可选值为返回示例值中的可以看到的字段，多个
            'fields' => isset($apiParams['fields']) ? $apiParams['fields'] : 'order_sn, external_order_sn, supplier_id, customer_id,user_id, delivery_id, consignee, zipcode, email, telephone, mobile, province, city, district, province_code,city_code, district_code, address, buyer_message, supplier_message, product_price, freight, discount_special, order_amount, pay_status, pay_money, coupon_code,coupon_amount,order_status, is_cold, shipment_status, pay_time, order_time, ship_time, order_from, delivery_time, delivery_default, created_at, created_by, updated_at, updated_by,pay_status_text, order_status_text, shipment_status_text, audit_status_text,ex_warehouse_status_text,delivery_name, pay_type_name, pay_type_info,pay_type, need_pay_money, pay_url,receive_time',
            //订单号   **必填**
            'order_sn' => isset($apiParams['fields']) ? $apiParams['fields'] : 'D-20180524-00636',
            //外部单号
            'external_order_sn' => isset($apiParams['external_order_sn']) ? $apiParams['external_order_sn'] : '',
            //需返回的订单商品字段字段列表，可选值为返回示例值中的可以看到的字段，多个
            'product_fields' => isset($apiParams['product_fields']) ? $apiParams['product_fields'] : 'order_sn, order_product_id, supplier_id, product_id, sku_id, quantity, product_name, cancel_quantity, real_quantity, apply_return_quantity, return_quantity, lack_quantity,selling_price, price_type, can_return_quantity, sku_info, is_gift, remarks, created_at, created_by, updated_at, updated_by,shipment_quantity, pic',
        ];
        $OpenClient = new OpenClient();
        $res = $OpenClient->execute($apiParams, $sysParams);
        echo "<pre>";
        var_dump($res);

    }

    /**
     * 掌秀云添加订单
     */
    public function zhangxiuyunAddOrder($apiParams = [])
    {
        $order_no = '2018053118270001';
        $order = new NsOrderModel();
        $order_info = $order->get(['order_no' => $order_no])->toArray();
        $order_goods = new NsOrderGoodsModel();
        $order_goods_info = $order_goods->where(['order_id' => $order_info['order_id']])->select();
        $product = array();
        foreach ($order_goods_info as $key => $value) {
            $product[$key]['product_id'] = $value['goods_id'];
            $product[$key]['sku_id'] = $value['sku_id'];
            $product[$key]['quantity'] = $value['num'];
            $product[$key]['remarks'] = $value['memo'];
        }
        echo "<pre>";
//        print_r($order_info);
//        print_r($order_goods_info);
//        print_r($product);
//
//        die;
        $sysParams = [
            'method' => 'customer.order.order.add',
        ];
        $apiParams = [
            //收货人姓名 **必填**
            'consignee' => $order_info['receiver_name'],
            //收货人手机 ****
            'mobile' => $order_info['receiver_mobile'],
            //收货人电话 ****
            'telephone' => '',
            //省 ****
            'province' => $order_info['receiver_province'],
            //市 ****
            'city' => $order_info['receiver_city'],
            //区 ****
            'district' => $order_info['receiver_district'],
            //详细地址
            'address' => $order_info['receiver_address'],
            //2016-12-26 12:31:21	预计到货时间
            'delivery_time' => $order_info['shipping_time'],
            //外部单号
            'external_order_sn' => $order_info['order_no'],
            //订单来源
            'order_from' => $order_info['order_from'],
            //配送方式
            'delivery_type_id' => $order_info['shipping_type'],
            //订单商品 json格式：product_info=[{"product_id":1,"sku_id":1,"quantity":1,"remarks":""},{"product_id":2,"sku_id":2,"quantity":2,"remarks":""}] quantity：数量 remarks：备注
            'product_info' => json_encode($product, JSON_UNESCAPED_UNICODE),
            //订单留言
            'buyer_message' => $order_info['buyer_message'],

        ];
        $OpenClient = new OpenClient();
        $res = $OpenClient->execute($apiParams, $sysParams);
        echo "<pre>";
        var_dump($res);
        die;
    }

    /**
     * 掌秀云获取单个SKU库存信息
     */
    public function zhangxiuyunOneSku($apiParams = [])
    {
        $sysParams = [
            'method' => 'customer.inventory.stock.sku',
        ];
        $apiParams = [
            //需要返回的字段列表，可选值为返回示例值中的可以看到的字段。
            'fields' => isset($apiParams['fields']) ? $apiParams['fields'] : 'sku_id, sku_code, sku_str, usable_qty, unit, unit_name, big_unit, big_unit_name, unit_convert',
            //sku_id   **必填**
            'sku_id' => isset($apiParams['sku_id']) ? $apiParams['sku_id'] : 61522,
        ];
        $OpenClient = new OpenClient();
        $res = $OpenClient->execute($apiParams, $sysParams);
        echo "<pre>";
        var_dump($res);
        die;
    }

    /**
     * 掌秀云获取单个商品信息
     */
    public function zhangxiuyunOneProduct($apiParams = [])
    {

        $sysParams = [
            'method' => 'customer.inventory.stock.product',
        ];
        $apiParams = [
            //需要返回的字段列表，可选值为返回示例值中的可以看到的字段。
            'fields' => isset($apiParams['fields']) ? $apiParams['fields'] : 'sku_id, sku_code, sku_str, usable_qty, unit, unit_name, big_unit, big_unit_name, unit_convert',
            //商品id **必填**
            'product_id' => isset($apiParams['product_id']) ? $apiParams['product_id'] :"",
        ];
        $OpenClient = new OpenClient();
        $res = $OpenClient->execute($apiParams, $sysParams);
        if ($res) {
            $goods_info = new NsGoodsModel();
            $goods_sku = new  NsGoodsSkuModel();
            try {
                $erp_info = $res['data'];
                for ($i = 0; $i<count($erp_info);$i++){
                    $sku_info = array();
                    $sku_info['stock'] = $erp_info[$i]['usable_qty'];//可用库存
                    $sku_info['update_date'] = time();
                    $res = $goods_sku ->where(['external_sku_id'=>$erp_info[$i]['sku_id']])->update($sku_info); //保存当前sku的库存
                    if (!$res){
                        throw exception($erp_info[$i]['sku_str']);
                    }
                }
            } catch (\Exception $e) {
                $msg = $message = $e->getMessage();
                $result = $goods_info->get(['external_id' => $apiParams['product_id']]);
                $error ['goods_code'] = $result['code'];
                $error ['goods_name'] = $result['goods_name'];
                $error ['message'] = $msg;
                $error ['type'] = '手动导入';
                $error ['create_time'] = date('Y-m-d H:i:s',time());
                $res = Db::table('ns_goods_error')->insert($error);
            }
        }
    }

    /**
     * 掌秀云获取商品库存列表
     */
    public function zhangxiuyunSkuList($apiParams = [])
    {

        $sysParams = [
            'method' => 'customer.inventory.stock.index',
        ];
        $apiParams = [
            //需要返回的字段列表，可选值为返回示例值中的可以看到的字段。
            'fields' => isset($apiParams['fields']) ? $apiParams['fields'] : 'product_id, product_name, product_code, unit, unit_name, big_unit, big_unit_name, unit_convert, usable_qty, occupy_qty',
            //页码。取值范围:大于零的整数; 默认值:1
            'page' => isset($apiParams['page']) ? $apiParams['page'] : 1,
            //每页条数。取值范围:大于零的整数; 默认值:20;最大值:5
            'page_size' => isset($apiParams['page_size']) ? $apiParams['page_size'] : 20,
            //是否启用has_next的分页方式，如果指定true,则返回
            'use_has_next' => isset($apiParams['use_has_next']) ? TRUE : FALSE,
            //商品编码
            'product_code' => isset($apiParams['product_code']) ? $apiParams['product_code'] : '',
            //商品id 12,13,14
            'product_id' => isset($apiParams['product_id']) ? $apiParams['product_id'] : '',

        ];
        $OpenClient = new OpenClient();
        $res = $OpenClient->execute($apiParams, $sysParams);
        echo "<pre>";
        var_dump($res);
        die;
    }

    /**
     * 订单发货单
     */
    public function zhangxiuyunOrderOut()
    {

    }


    /**
     * 安装方法
     */
    public function install()
    {
        // TODO: Implement install() method.
        $this->uninstall();
        $table_name = $this->table;
        $sql = <<<SQL
        CREATE TABLE `{$table_name}` (
          id int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
          instance_id int(11) NOT NULL COMMENT '店铺id（单店版为0）',
          template_no varchar(55) NOT NULL COMMENT '模版编号',
          template_id varbinary(55) DEFAULT NULL COMMENT '掌秀的ID',
          title varchar(100) NOT NULL COMMENT '模版标题',
          is_enable tinyint(4) NOT NULL DEFAULT 1 COMMENT '是否启用',
          headtext varchar(255) NOT NULL COMMENT '头部文字',
          bottomtext varchar(255) NOT NULL COMMENT '底部文字',
          PRIMARY KEY (id)
        )
        ENGINE = INNODB
        AUTO_INCREMENT = 1
        AVG_ROW_LENGTH = 4096
        CHARACTER SET utf8
        COLLATE utf8_general_ci
        COMMENT = '掌秀';
SQL;
        \think\Db::execute($sql);
        if (count(db()->query("SHOW TABLES LIKE '{$table_name}'")) != 1) {
            session('addons_install_error', ',掌秀表未创建成功，请手动检查插件中的sql，修复后重新安装');
            return false;
        } else {
            $data = array(
                [
                    'instance_id' => 0,
                    'template_no' => 'OPENTM203347141',
                    'template_id' => '',
                    'title' => '会员注册成功通知',
                    'is_enable' => 1,
                    'headtext' => '恭喜，您已成功注册为会员！',
                    'bottomtext' => '恭喜，您已成为会员，您将享受会员所有权利！'
                ]
            );
            \think\Db::table("$table_name")->insertAll($data);
        }
        return true;
    }

    /**
     * 卸载方法
     */
    public function uninstall()
    {
        $table_name = $this->table;
        $sql = "DROP TABLE IF EXISTS `{$table_name}`;";
        \think\Db::execute($sql);
        return true;
        // TODO: Implement uninstall() method.
    }
}