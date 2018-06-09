<?php
/**
 * NfxCommissionConfig.php
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
namespace data\service;
use data\api\INfxCommissionConfig;
use data\service\BaseService;
use data\model\NfxGoodsCommissionRateModel;
use data\model\NsGoodsModel as NsGoodsModel;
use data\model\NsGoodsViewModel as NsGoodsViewModel;
use data\model\ConfigModel as ConfigModel;
use data\model\NfxShopMemberAssociationModel;
/**
 * 商品佣金设置服务层
 */

class NfxCommissionConfig extends BaseService implements INfxCommissionConfig
{
	/* (non-PHPdoc)
     * @see \data\api\INfxCommissionConfig::getGoodsCommissionRate()
     */
    public function getGoodsCommissionRate($goods_id)
    {
        $goods_commission_rate = new NfxGoodsCommissionRateModel();
        $count = $goods_commission_rate->where(['goods_id' => $goods_id])->count();
        if($count == 0)
        {
            //默认添加
            $goods_commission_rate = new NfxGoodsCommissionRateModel();
            $data = array(
                "goods_id"=>$goods_id ,
                "create_time"=> time()
            );
            $goods_commission_rate->save($data);
        }
        
        $data = $goods_commission_rate->get(["goods_id"=>$goods_id]);
        return $data;
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \data\api\INfxCommissionConfig::updateGoodsCommissionRate()
     */
    public function updateGoodsCommissionRate($condition, $type,  $distribution_commission_rate, $partner_commission_rate, $global_commission_rate, $distribution_team_commission_rate, $partner_team_commission_rate, $regionagent_commission_rate, $channelagent_commission_rate , $shop_id)
    {

        $goods_list = array();
        if($type == 1 ){
            //平台店铺独立分销单独设定
            if(NS_VERSION == NS_VER_B2C_FX)
            {
                $condition = " goods_id =".$condition." and shop_id = ".$shop_id ;
            }else{
                $condition = " goods_id =".$condition;
            }
           
            $goods = new NsGoodsModel();
            $goods_list = $goods->getQuery($condition, "*", "");
        }elseif($type == 2){
            //平台店铺独立分销单独设定
            if(NS_VERSION == NS_VER_B2C_FX)
            {
                $where["shop_id"] = $shop_id;
            }else{
                 $where = '';
            }
           
            $goods_list = $this->getGroupGoodsList($condition, $where);
        }elseif($type == 3){
            //平台店铺独立分销单独设定
            $goods = new NsGoodsModel();
            if(NS_VERSION == NS_VER_B2C_FX)
            {
                $goods_list = $goods->getQuery(" shop_id =".$shop_id, "*", "");
            }else{
                $goods_list = $goods->getQuery('', "*", "");
            }
            
        }
        $goods_id_string = "";
        foreach($goods_list  as $k=>$v){
            $this->getGoodsCommissionRate($v["goods_id"]);
            //$retval = $goods_commission_rate->save($data,["goods_id"=>$goods_id]);
            $goods_id_string = $goods_id_string .",".$v["goods_id"];
        }
          
        $goods_id_string = substr($goods_id_string,1);
        $data = array(
            "distribution_commission_rate"=>$distribution_commission_rate,
            "partner_commission_rate"=>$partner_commission_rate,
            "global_commission_rate"=>$global_commission_rate,
            "distribution_team_commission_rate"=>$distribution_team_commission_rate,
            "partner_team_commission_rate"=>$partner_team_commission_rate,
            "regionagent_commission_rate"=>$regionagent_commission_rate,
            "channelagent_commission_rate"=>$channelagent_commission_rate,
            "modify_time"=>time()
        );
        $goods_commission_rate = new NfxGoodsCommissionRateModel(); 
        if($goods_id_string != ""){
            
            $rate_total = $distribution_commission_rate + $partner_commission_rate + $global_commission_rate + $distribution_team_commission_rate + $partner_team_commission_rate + $regionagent_commission_rate + $channelagent_commission_rate;
            if($rate_total>100){
                $retval = 1;
            }else{
                $retval = $goods_commission_rate->where(" goods_id in (".$goods_id_string.") ")->update($data);
            }
            
          
            
        }else{
            $retval = 1;
        }      
        return $retval;
        // TODO Auto-generated method stub
        
    }
	/* (non-PHPdoc)
     * @see \data\api\INfxCommissionConfig::modifyGoodsIsOpenDistribution()
     */
    public function modifyGoodsIsOpenDistribution($condition, $is_open)
    {
        // TODO Auto-generated method stub
        $goods_commission_rate = new NfxGoodsCommissionRateModel();
        $data = array(
            "is_open" => $is_open
        );
        $retval = $goods_commission_rate->save($data, "goods_id  in ($condition)");
        // TODO Auto-generated method stub
        return $retval;
    }   
    
    
    public function getGoodsCommissionRateList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $goods_view = new NsGoodsViewModel();
        $list = $goods_view->getGoodsViewList($page_index, $page_size, $condition, $order);
        if(!empty($list['data']))
        {
            //用户针对商品的收藏
            foreach ($list['data'] as $k => $v)
            {               

                $goods_info = $this->getGoodsCommissionRate($v["goods_id"]);
                $list['data'][$k]['is_open'] = $goods_info["is_open"];
                $list['data'][$k]['distribution_commission_rate'] = $goods_info["distribution_commission_rate"];
                $list['data'][$k]['partner_commission_rate'] = $goods_info["partner_commission_rate"];
                $list['data'][$k]['global_commission_rate'] = $goods_info["global_commission_rate"];
                $list['data'][$k]['distribution_team_commission_rate'] = $goods_info["distribution_team_commission_rate"];
                $list['data'][$k]['partner_team_commission_rate'] = $goods_info["partner_team_commission_rate"];
                $list['data'][$k]['regionagent_commission_rate'] = $goods_info["regionagent_commission_rate"];
                $list['data'][$k]['channelagent_commission_rate'] = $goods_info["channelagent_commission_rate"];
                $list['data'][$k]['fenxiao_create_time'] = $goods_info["create_time"];
                $list['data'][$k]['fenxiao_modify_time'] = $goods_info["modify_time"];
            }
    
        }
        return $list;
    
        // TODO Auto-generated method stub
    }
    /**
     * (non-PHPdoc)
     *
     * @see \data\api\niushop\IGoods::getGroupGoodsList()
     */
    private function getGroupGoodsList($goods_group_id, $condition = '', $num = 0, $order = '')
    {
        $goods_list = array();
        $goods = new NsGoodsModel();
        //$condition['state'] = 1;
        $list = $goods->getQuery($condition, '*', $order);
        foreach ($list as $k => $v) {
            $group_id_array = explode(',', $v['group_id_array']);
            if (in_array($goods_group_id, $group_id_array) || $goods_group_id == 0) {
                $goods_list[] = $v;
            }
        }
        return $goods_list;
    }
	/* (non-PHPdoc)
     * @see \data\api\INfxCommissionConfig::getGoodsCommiddionAll()
     */
    public function getGoodsCommiddionAll($condition)
    {
        // TODO Auto-generated method stub
        $goods_commission_rate = new NfxGoodsCommissionRateModel();
        $all = $goods_commission_rate->all($condition);
        return $all;
    }

    /**
     * 获取商品佣金
     * @param unknown $goods_id
     */
    public function getGoodsCommissionCalculate($goods_id)
    {
        $goods_model = new NsGoodsModel();
        $goods_info = $goods_model->getInfo(['goods_id' => $goods_id], 'promotion_price,cost_price');
        $goods_return = $goods_info['promotion_price']-$goods_info['cost_price'];
        if($goods_return < 0)
        {
            $goods_return = 0;
        }
        $goods_commission_rate = $this->getGoodsCommissionRate($goods_id);
        $is_open = 1;
        if(!empty($goods_commission_rate))
        {
            if($goods_commission_rate['is_open'] == 0)
            {
                $is_open = 0;
            }else{
                if($goods_return > 0)
                {
                    $distribution_commission = $goods_return * $goods_commission_rate['distribution_commission_rate'];
                    $partner_commission = $goods_return * $goods_commission_rate['partner_commission_rate'];
                    $global_commission = $goods_return * $goods_commission_rate['global_commission_rate'];;
                    $regionagent_commission = $goods_return * $goods_commission_rate['regionagent_commission_rate'];;
                }else{
                    $is_open = 0;
                }
               
            }
        
        }else{
            $is_open = 0;
        }
        if($is_open == 1)
        {
            return array(
                'distribution_commission' => $distribution_commission,
                'partner_commission'      => $partner_commission,
                'global_commission'       => $global_commission,
                'regionagent_commission'  => $regionagent_commission
            );
        }else{
            return array(
                'distribution_commission' => 0,
                'partner_commission'      => 0,
                'global_commission'       => 0,
                'regionagent_commission'  => 0
            );
        }
    }
    /**
     * 获取用户商品佣金
     * @param unknown $goods_id
     */
    public function getGoodsUserCommission($goods_id, $uid)
    {
        $shop_member_association = new NfxShopMemberAssociationModel();
        $promoter_info = $shop_member_association->getInfo(['uid' => $uid], 'is_promoter,is_partner,region_center_id');

        $goods_commission = $this->getGoodsCommissionCalculate($goods_id);
        if(empty($promoter_info))
        {
            return array(
                'distribution_commission' => 0,
                'partner_commission'      => 0,
                'global_commission'       => 0,
                'regionagent_commission'  => 0
            );
        }
        if($promoter_info['is_promoter'] == 0)
        {
            $goods_commission['distribution_commission'] = 0;
        }
        if($promoter_info['is_partner'] == 0)
        {
            $goods_commission['partner_commission'] = 0;
            $goods_commission['global_commission'] = 0;
        }
        if($promoter_info['region_center_id'] == 0)
        {
            $goods_commission['regionagent_commission'] = 0;
        }
        return $goods_commission;
    }
    
    
}
