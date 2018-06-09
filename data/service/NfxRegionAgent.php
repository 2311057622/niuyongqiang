<?php
/**
 * NfxRegionAgent.php
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
use data\service\BaseService;
use data\api\INfxRegionAgent;
use data\model\NfxShopRegionAgentConfigModel;
use data\model\NfxPromoterRegionAgentModel;
use data\model\UserModel as UserModel;
use data\model\NfxShopMemberAssociationModel;
use data\model\NfxCommissionRegionAgentModel;
use data\service\Order as OrderService;
use data\model\ProvinceModel;
use data\model\CityModel;
use data\model\DistrictModel;
use data\model\NfxShopConfigModel;
use data\service\Shop;
use data\model\NfxPromoterModel;
/**
 * 区域代理服务层
 */

class NfxRegionAgent extends BaseService implements INfxRegionAgent
{
	/* (non-PHPdoc)
     * @see \data\api\INfxRegionAgent::getShopRegionAgentConfig()
     */
    public function getShopRegionAgentConfig($shop_id)
    {
        // TODO Auto-generated method stub

        
        $shop_region_agent = new NfxShopRegionAgentConfigModel();
         $count = $shop_region_agent->where(["shop_id"=>$shop_id])->count();
         if($count == 0)
        {
             //默认添加
             $shop_region_agent = new NfxShopRegionAgentConfigModel();
             $data = array(
                 "shop_id"=>$shop_id ,
                 "create_time"=>time()
             );
            $shop_region_agent->save($data);
         }
        $data = $shop_region_agent->get(["shop_id"=>$shop_id]);
        return $data;
    }

	/* (non-PHPdoc)
     * @see \data\api\INfxRegionAgent::updateShopRegionAgentConfig()
     */
    public function updateShopRegionAgentConfig($shop_id, $province_rate, $city_rate, $district_rate, $application_require_province, $application_require_city, $application_require_district)
    {
        
        // TODO Auto-generated method stub
        $shop_region_agent = new NfxShopRegionAgentConfigModel();
        $shop_region_agent->startTrans();
        try{
            $data = array(
                "province_rate"=>$province_rate,
                "city_rate"=>$city_rate,
                "district_rate"=>$district_rate,
                "application_require_province"=>$application_require_province,
                "application_require_city"=>$application_require_city,
                "application_require_district"=>$application_require_district
                
            );
            $retval = $shop_region_agent->where(["shop_id"=>$shop_id])->update($data);
            $shop_region_agent->commit();
            return 1;
        } catch (\Exception $e)
        {
            $shop_region_agent->rollback();
            return $e->getMessage();
        }
    }

	/* (non-PHPdoc)
     * @see \data\api\INfxRegionAgent::getPromoterRegionAgent()
     */
    public function getPromoterRegionAgent($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        // TODO Auto-generated method stub
        $promoter_region_agent = new NfxPromoterRegionAgentModel();       
        $list = $promoter_region_agent->pageQuery($page_index, $page_size, $condition, $order, '*');
        
        
        if(!empty($list['data'])){
            foreach ($list['data'] as $k=>$v)
            {
                $province_name = "";
                $city_name = "";
                $district_name = "";
                $user = new UserModel();
                $userinfo = $user->where(['uid' => $v['uid']])->select();
                $list['data'][$k]['real_name'] = $userinfo[0]["nick_name"];
                $list['data'][$k]['user_tel'] = $userinfo[0]["user_tel"]; 
                
                $promoter = new NfxPromoterModel();
                $prometerinfo = $promoter->getInfo(['uid' => $v['uid']]);
                $list['data'][$k]['promoter_no'] = $prometerinfo['promoter_no'];
                $province= new ProvinceModel();
                $province_info = $province->getInfo(array("province_id"=>$v["agent_provinceid"]),"*");
                if(count($province_info) >0){
                    $province_name = $province_info["province_name"];                    
                }
                $list['data'][$k]['province_name'] = $province_name;                
                $city= new CityModel();
                $city_info = $city->getInfo(array("city_id"=>$v["agent_cityid"]),"*");
                if(count($city_info) >0){
                    $city_name = $city_info["city_name"];                    
                }
                $list['data'][$k]['city_name'] = $city_name;               
                $district= new DistrictModel();
                $district_info = $district->getInfo(array("district_id"=>$v["agent_districtid"]),"*");
                if(count($district_info) >0){
                    $district_name = $district_info["district_name"];                   
                }
                $list['data'][$k]['district_name'] = $district_name;               
            }
        
        }             
        return $list;
        
    }
	/* (non-PHPdoc)
     * @see \data\api\INfxRegionAgent::modifyPromoterRegionAgentIsAudit()
     */
    public function modifyPromoterRegionAgentIsAudit($shop_id, $is_audit, $region_agent_id,$province_id, $city_id, $district_id)
    {
        // TODO Auto-generated method stub
        $promoter_region_agent = new NfxPromoterRegionAgentModel();  
        $promoter_region_agent->startTrans();
        $promoter_region_agent_info = $promoter_region_agent->getInfo(array("shop_id"=>$shop_id,"region_agent_id"=>$region_agent_id),"agent_type,uid");
        try{
            if($is_audit == 1)
            {
                $promoter_region_agent = new NfxPromoterRegionAgentModel();
                $data["is_audit"] =$is_audit;
                $agent_type = $promoter_region_agent_info["agent_type"];
                if($agent_type == 1){
                    if($province_id > 0){
                        $promoter_region_agent = new NfxPromoterRegionAgentModel();
                        $count = $promoter_region_agent ->where(array("agent_provinceid"=>$province_id,"shop_id"=>$shop_id))->count();
                        if($count >0){
                            return 0;
                        }
                    }else{
                        return 0;
                    }
                    $data["agent_provinceid"] = $province_id;
                }else if($agent_type == 2){
                    if($city_id > 0){
                        $promoter_region_agent = new NfxPromoterRegionAgentModel();
                        $count = $promoter_region_agent ->where(array("agent_provinceid"=>$province_id,"agent_cityid"=>$city_id,"shop_id"=>$shop_id))->count();
                        if($count >0){
                            return 0;
                        }
                    }else{
                        return 0;
                    }
                    $data["agent_provinceid"] = $province_id;
                    $data["agent_cityid"] = $city_id;
                }else if($agent_type == 3){
                    if($city_id > 0){
                        $promoter_region_agent = new NfxPromoterRegionAgentModel();
                        $count = $promoter_region_agent ->where(array("agent_provinceid"=>$province_id,"agent_cityid"=>$city_id,"shop_id"=>$shop_id,"agent_districtid"=>$district_id))->count();
                        if($count >0){
                            return 0;
                        }
                    }else{
                        return 0;
                    }
                    $data["agent_provinceid"] = $province_id;
                    $data["agent_cityid"] = $city_id;
                    $data["agent_districtid"] = $district_id;
                }
            
                //修改
                $shop_member_association = new NfxShopMemberAssociationModel();
                $shop_member_association->save(['region_center_id' => $region_agent_id], ['shop_id' => $shop_id, 'uid' => $promoter_region_agent_info['uid']]);
            
            }else if($is_audit == -1){
                $data["is_audit"] = -1;
            }
           
            if($is_audit == 1 ||$is_audit == -1){
                $data["audit_time"] =time();
            }elseif($is_audit == 1){
                $data["cancel_time"] = time();
            }
            $condition = array(
                "shop_id"=>$shop_id,
                "region_agent_id"=>$region_agent_id
            );
            $retval = $promoter_region_agent->where($condition)->update($data);
            $promoter_region_agent->commit();
            return $retval;
        } catch (\Exception $e)
        {
            $promoter_region_agent->rollback();
            return 0;
        }
            
    }
	/* 
	 * 申请区域代理
	 * (non-PHPdoc)
     * @see \data\api\INfxRegionAgent::PromoterRegionAgentApplay()
     */
    public function promoterRegionAgentApplay($shop_id, $uid, $agent_type, $real_name, $mobile, $address)
    {
        // TODO Auto-generated method stub
        $res = $this->getPromoterRegionAgentApplicationRequire($shop_id, $agent_type, $uid);
        if($res > 0){
            $shop_member_association = new NfxShopMemberAssociationModel();
            $shop_member_association_info = $shop_member_association->get(array("uid"=>$uid));
            $promoter_id = $shop_member_association_info["promoter_id"];
            $promoter_region_agent = new NfxPromoterRegionAgentModel();  
            $data = array(
                "shop_id"=>$shop_id,
                "promoter_id"=>$promoter_id,
                "uid"=>$uid,
                "agent_type"=>$agent_type ,
                "reg_time" =>time(),
                "real_name"=>$real_name,
                "agent_mobile"=>$mobile,
                "agent_address"=>$address  
                );
            $retval = $promoter_region_agent->save($data);
            return $promoter_region_agent->region_agent_id;
            
        }else{
            return 0;
        }
    }
	/* (non-PHPdoc)
     * @see \data\api\INfxRegionAgent::getCommissionRegionAgentList()
     */
    public function getCommissionRegionAgentList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        // TODO Auto-generated method stub                
        $order_service= new OrderService();
        $order_list = $order_service->getOrderList($page_index , $page_size , $condition , $order );
        foreach($order_list["data"] as $k=>$v){
            $commission_money = 0;
        
            foreach($v["order_item_list"] as $l=>$b){
                $order_item = $v["order_item_list"][$l];
                $commission_region_agent_list = array();
                $commission_region_agent = new NfxCommissionRegionAgentModel();
                $commission_region_agent_condition = array("order_id"=>$b["order_id"],"order_goods_id"=>$b["order_goods_id"]);
                $commission_region_agent_list = $commission_region_agent->all($commission_region_agent_condition);
                if(count($commission_region_agent_list)>0){
                    foreach($commission_region_agent_list as $commission_k=>$commission_v){
                        $user = new UserModel();
                        $userinfo = $user->getInfo(['uid' => $commission_v['uid']]);
                        $commission_region_agent_list[$commission_k]['real_name'] = $userinfo["nick_name"];
                        $commission_money = $commission_money+$commission_v["commission"];
                        $promoter = new NfxPromoterModel();
                        $prometerinfo = $promoter->getInfo(['uid' => $commission_v['uid']]);
                        $commission_region_agent_list[$commission_k]['promoter_no'] = $prometerinfo['promoter_no'];
                        $commission_region_agent_list[$commission_k]['promoter_shop_name'] = $prometerinfo['promoter_shop_name'];
                    }
                }
                //var_dump($commission_region_agent_list);
                $order_item["commission_partner_list"] = $commission_region_agent_list;
            }
            $order_list["data"][$k]["commission_money"] = $commission_money;
        }
        return $order_list;
    }
	/* (non-PHPdoc)
     * @see \data\api\INfxRegionAgent::getPromoterRegionAgentDetail()
     */
    public function getPromoterRegionAgentValidDetail($shop_id, $uid)
    {
        // TODO Auto-generated method stub
        $promoter_region_agent = new NfxPromoterRegionAgentModel();  
        $data = $promoter_region_agent->where(array("shop_id"=>$shop_id,"uid"=>$uid))->order("region_agent_id desc")->limit(0,1)->select();
        return empty($data)?'':$data[0];
    }
	/* (non-PHPdoc)
     * @see \data\api\INfxRegionAgent::getPromoterRegionAgentAll()
     */
    public function getPromoterRegionAgentAll($condition)
    {
        // TODO Auto-generated method stub
        $promoter_region_agent = new NfxPromoterRegionAgentModel();
        $promoter_region_agent_all = $promoter_region_agent->all($condition);
        return $promoter_region_agent_all;
    }
	/* (non-PHPdoc)
     * @see \data\api\INfxRegionAgent::getPromoterRegionAgentApplicationRequire()
     */
    public function getPromoterRegionAgentApplicationRequire($shop_id, $agent_type, $uid)
    {
        // TODO Auto-generated method stub
        $shop_region_agent = new NfxShopRegionAgentConfigModel();
        $application_require = $shop_region_agent->getInfo(array("shop_id"=>$shop_id),"*");
        if($agent_type == 1){ 
            $application_require_money = $application_require["application_require_province"];
        }else if($agent_type == 2){
            $application_require_money = $application_require["application_require_city"];
        }else{
            $application_require_money = $application_require["application_require_district"];
        }
        $shop = new Shop();
        $shop_user_account = $shop->getShopUserConsume($shop_id, $uid);
        if($shop_user_account < $application_require_money){
            return 0;
        }else{
            return 1;
        }       
    }
	/* (non-PHPdoc)
     * @see \data\api\INfxRegionAgent::getRegionAgentCommissionCount()
     */
    public function getRegionAgentCommissionCount($condition)
    {
        // TODO Auto-generated method stub
        $region_agent_commission = new NfxCommissionRegionAgentModel();
        $result = $region_agent_commission->getQuery($condition, "sum(commission) as sum", '');
        return $result[0]["sum"];
    }
	/* (non-PHPdoc)
     * @see \data\api\INfxRegionAgent::deleteRegionAgentCommission()
     */
    public function removePromoterRegionAgent($shop_id, $region_agent_id)
    {
        // TODO Auto-generated method stub
        $promoter_region_agent = new NfxPromoterRegionAgentModel();
        $promoter_region_agent->startTrans();
        $promoter_region_agent_info = $promoter_region_agent->getInfo(array("shop_id"=>$shop_id,"region_agent_id"=>$region_agent_id),"agent_type,uid");
        try{
            
            $retval = $promoter_region_agent->destroy(array("shop_id"=>$shop_id,"region_agent_id"=>$region_agent_id));
            //修改
            $shop_member_association = new NfxShopMemberAssociationModel();
            $shop_member_association->save(['region_center_id' => 0], ['shop_id' => $shop_id, 'uid' => $promoter_region_agent_info['uid']]);
        
            
            $promoter_region_agent->commit();
            return $retval;
        } catch (\Exception $e)
        {
            $promoter_region_agent->rollback();
            return 0;
        }
    }
	
}
