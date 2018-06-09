<?php
/**
 * NfxPartnerGlobal.php
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
namespace data\service\Partner;
use data\service\BaseService;
use data\model\NsOrderGoodsModel;
use data\model\NsOrderGoodsPromotionDetailsModel;
use data\model\NfxGoodsCommissionRateModel;
use data\model\NsOrderModel;
use data\model\NfxCommissionPartnerGlobalRecordsModel;
use data\model\NfxPartnerModel;
use data\model\NfxPartnerLevelModel;
use data\model\NfxCommissionPartnerGlobalModel;
/**
 * 股东的全球分红计算
 * @author Administrator
 *
 */
class NfxPartnerGlobal extends BaseService
{
	private $shop_id;  //店铺id
	
	/**
	 * 构造函数
	 * @param unknown $shop_id
	 */
	public function __construct($shop_id){
	    $this->shop_id=$shop_id;
	}
	/**
	 * 查询指定时间内加权分红金额
	 * @param unknown $start_time
	 * @param unknown $end_time
	 */
	public function getPartnerGlobalMoney($start_time, $end_time){
	    $order_model=new NsOrderModel();
	    $condition["finish_time"] = [
	        [">",$start_time],
	        ["<",$end_time]
	    ];
	    $condition["shop_id"]=$this->shop_id;
	    $condition["order_status"]=4;
	    $order_list=$order_model->getQuery($condition, "*" ,"");
	    $total_global_commission=0;
	    foreach($order_list as $k => $order_object){
	        $order_global_commission=0;
	        $order_global_commission=$this->getOrderGlobalMoney($order_object["order_id"]);
	        $total_global_commission=$total_global_commission+$order_global_commission;
	    }
	    return $total_global_commission;
	}
	/**
	 * 通过订单id  来查询订单的加权分红可分金额
	 * @param unknown $order_id
	 */
	private function getOrderGlobalMoney($order_id){
	    $order_global_money=0;
	    if($order_id!=0){
	        $order_goods_model=new NsOrderGoodsModel();
	        $order_goods_list = $order_goods_model->getQuery(['order_id' => $order_id], '*', '');
	        foreach ($order_goods_list as $k => $order_goods)
	        {
	            //计算佣金分配信息
	            $goods_global_rate=0;
	            $goods_commission_rate_model = new NfxGoodsCommissionRateModel();
	            $commission_rate = $goods_commission_rate_model->getInfo(['goods_id' => $order_goods['goods_id']], '*');
	            if(empty($commission_rate))
	            {
	                $goods_global_rate = 0;
	            }else{
	                $global_rate=$commission_rate["global_commission_rate"];
	                $is_open=$commission_rate["is_open"];
	                if($is_open==1){
	                    $goods_global_rate=$global_rate;
	                }else{
	                    $goods_global_rate=0;
	                }
	            }
	            $order_goods_promotion = new NsOrderGoodsPromotionDetailsModel();
	            $promotion_money = $order_goods_promotion->where(['order_id' => $order_id, 'sku_id' => $order_goods['sku_id']])->sum('discount_money');
	            if(empty($promotion_money))
	            {
	                $promotion_money = 0;
	            }
	            $order_cost_price = $order_goods['cost_price']*$order_goods['num'];    
	            $goods_return = $order_goods['goods_money']+$order_goods['adjust_money']-$order_goods['refund_real_money']-$promotion_money-$order_cost_price;
	            $goods_return=$goods_return*$goods_global_rate/100;
	            $order_global_money=$order_global_money+$goods_return;
	        }
	    }
	    return $order_global_money;
	}
	
	/**
	 *  通过店铺id 查询最后一次群球分红的详情
	 */
	public function getPartnerGlobalLastInfo(){
	     $global_record_model=new NfxCommissionPartnerGlobalRecordsModel();
	     $condition["shop_id"]=$this->shop_id;
	     $global_record_list=$global_record_model->getQuery($condition, "*" ,"create_time desc");
	     if(!empty($global_record_list) && count($global_record_list)>0){
	         return $global_record_list[0];
	     }else{
	         return null;
	     }
	}
	/**
	 * 查询股东的分值
	 * @param unknown $partner_id
	 */
	public function getPartnerValue($partner_id){
	    $partner_total_value=0;
	    $partner_model=new NfxPartnerModel();
	    $partner_level_model=new NfxPartnerLevelModel();
	    $condition["shop_id"]=$this->shop_id;
	    $condition["parent_partner"]=$partner_id;
	    $partner_child_list=$partner_model->getQuery($condition, "*" ,"");
	    $child_value=0;
	    if(!empty($partner_child_list) && count($partner_child_list)>0){
	        foreach ($partner_child_list as $k=>$child_object){
	            $partner_level=$child_object["partner_level"];
	            $level_object=$partner_level_model->get($partner_level);
	            $child_value=$child_value+$level_object["global_value"];
	        }
	    }
	    $partner_object=$partner_model->get($partner_id);
	    $level_object=$partner_level_model->get($partner_object["partner_level"]);
	    $partner_value=$level_object["global_value"];
	    $global_weight=$level_object["global_weight"];
	    $partner_total_value=($child_value+$partner_value)*$global_weight;
	    return $partner_total_value;
	}

	/**
	 * 查询某个股东等级的总分值
	 * @param unknown $level_id
	 */
	public function getPartnerLevelValue($level_id){
	    $partner_model=new NfxPartnerModel();
	    $condition["shop_id"]=$this->shop_id;
	    $condition["partner_level"]=$level_id;
	    $partner_list=$partner_model->getQuery($condition, "*", "");
	    $level_value=0;
	    if(!empty($partner_list) && count($partner_list)>0){
	        foreach ($partner_list as $k=>$partner_Obj){
	            $partner_values=$this->getPartnerValue($partner_Obj["partner_id"]);
	            $level_value=$level_value+$partner_values;
	        }
	    }
	    return $level_value;
	}
	/**
	 * 股东全球分红记录添加
	 * @param unknown $shop_id
	 * @param unknown $start_time
	 * @param unknown $end_time
	 * @param unknown $fenhong_money
	 */
	public function addCommissionPartnerGlobalRecords($shop_id, $start_time, $end_time, $fenhong_money){
	    $partner_global_records_model=new NfxCommissionPartnerGlobalRecordsModel();
	    $data = array(
	        'shop_id'            => $shop_id,
	        'start_time'              => $start_time,
	        'end_time'          => $end_time,
	        'fenhong_money'       => $fenhong_money,
	        'create_time'          => time()
	    );
	    $partner_global_records_model->save($data);
	    return $partner_global_records_model->id;
	}
	/**
	 * 添加股东的全球分红记录流水
	 * @param unknown $serial_no
	 * @param unknown $shop_id
	 * @param unknown $partner_id
	 * @param unknown $uid
	 * @param unknown $start_time
	 * @param unknown $end_time
	 * @param unknown $yingye_money
	 * @param unknown $shop_value
	 * @param unknown $partner_value
	 * @param unknown $partner_rate
	 * @param unknown $fenhong_money
	 */
	public function addCommissionPartnerGlobal($serial_no, $shop_id, $partner_id, $uid, $start_time, $end_time, $yingye_money, $shop_value, $partner_value, $partner_rate, $fenhong_money, $records_id){
	    $partner_global_model=new NfxCommissionPartnerGlobalModel();
	    $data=array(
	        "serial_no"=>$serial_no, 
	        "shop_id"=>$shop_id, 
	        "records_id"=>$records_id,
	        "partner_id"=>$partner_id, 
	        "uid"=>$uid, 
	        "start_time"=>$start_time, 
	        "end_time"=>$end_time, 
	        "yingye_money"=>$yingye_money, 
	        "shop_value"=>$shop_value, 
	        "partner_value"=>$partner_value, 
	        "partner_rate"=>$partner_rate, 
	        "fenhong_money"=>$fenhong_money,
	        "create_time"=>time()
	    );
	    $partner_global_model->save($data);
	    return $partner_global_model->id;
	}
}
