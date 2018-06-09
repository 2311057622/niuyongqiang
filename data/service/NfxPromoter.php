<?php
/**
 * NfxPromoter.php
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
use data\model\NfxPromoterModel;
use data\model\UserModel as UserModel;
use data\api\INfxPromoter;
use data\model\NfxPromoterLevelModel;
use data\service\Order as OrderService;
use data\model\NfxCommissionDistributionModel;
use data\model\NfxShopMemberAssociationModel;
use data\model\NsShopModel;
use data\model\NfxShopConfigModel;
use data\model\NfxUserAccountModel;
use data\model\NsShopAccountModel;
use data\model\BaseModel;
use data\model\NsMemberViewModel;
use data\service\Member\MemberAccount;

/**
 * 会员流水账户
 */
class NfxPromoter extends BaseService implements INfxPromoter
{

    /**
     * 获取推广员列表
     *
     * @param unknown $uid            
     * @param unknown $account_type            
     * @param unknown $start_time            
     * @param unknown $end_time            
     */
    public function getPromoterList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $promoter = new NfxPromoterModel();
        $promoter_list = $promoter->pageQuery($page_index, $page_size, $condition, $order, '*');
        if (! empty($promoter_list['data'])) {
            foreach ($promoter_list['data'] as $k => $v) {
                $user = new UserModel();
                $userinfo = $user->getInfo(['uid' => $v['uid']], "nick_name, user_tel, user_headimg");
                $user_name = "";
                $user_tel = "";
                $user_headimg = '';
                if (count($userinfo) > 0) {
                    $user_name = $userinfo["nick_name"];
                    $user_tel = $userinfo["user_tel"];
                    $user_headimg = $userinfo["user_headimg"];
                }
                $promoter_list['data'][$k]['real_name'] = $user_name;
                $promoter_list['data'][$k]['user_tel'] = $user_tel;
                $promoter_list['data'][$k]['user_headimg'] = $user_headimg;
                $promoter_list['data'][$k]['order_total'] = $this->getPromoterOrderTotal($v['promoter_id']);
                $parent_realname = "";
                $parent_promoter_no = "";
                $promoter_level = new NfxPromoterLevelModel();
                $level_name = "";
                $level_info = $promoter_level->getInfo(['level_id' => $v['promoter_level']], "level_name");
                if (count($level_info) > 0) {
                    $level_name = $level_info["level_name"];
                }
                $promoter_list['data'][$k]['level_name'] = $level_name;
                $level_info = $promoter_level->get($v['promoter_level']);
                $promoter_list['data'][$k]['level_name'] = $level_info["level_name"];
                if ($v['parent_promoter'] != 0) {
                    $parent_info = $promoter->getInfo(['promoter_id' => $v['parent_promoter']], "uid, promoter_no");
                    $parent_uid = $parent_info["uid"];
                    $parent_userinfo = $user->getInfo(['uid' => $parent_uid], "nick_name");
                    $parent_realname = $parent_userinfo["nick_name"];
                    $parent_promoter_no = $parent_info['promoter_no'];
                }
                $promoter_list['data'][$k]['parent_realname'] = $parent_realname;
                $promoter_list['data'][$k]['parent_promoter_no'] = $parent_promoter_no;
                $team_list = $this->getPromoterTeamList($v["promoter_id"]);
                $promoter_num = 0;
                $fans_num = 0;
                foreach($team_list as $t){
                    if($t["is_promoter"] == 0 && $t["is_partner"] == 0 ){
                        $fans_num = $fans_num +1;
                    }else{
                        $promoter_num = $promoter_num +1;
                    }
                }
                $promoter_list['data'][$k]['promoter_num'] = $promoter_num;
                $promoter_list['data'][$k]['fans_num'] = $fans_num;
                
            }
        }
        return $promoter_list;
        // TODO Auto-generated method stub
    }
    /**
     * 获取推广员业绩总额
     * @param unknown $promoter_id
     */
    public function getPromoterOrderTotal($promoter_id)
    {
        $commissin_distribution_model = new NfxCommissionDistributionModel();
        $money = $commissin_distribution_model->getSum(['promoter_id' => $promoter_id,'promoter_level' => 0], 'goods_money');
        if(empty($money))
        {
            $money = 0;
        }
        return $money;
    }
    

    /*
     * (non-PHPdoc)
     * @see \data\api\IPromoter::promoterApplay()
     */
    public function promoterApplay($uid, $shop_id, $promoter_shop_name)
    {
        $promoter = new NfxPromoterModel();
        $audit_count = $promoter->getCount(
            array(
            "shop_id" => $shop_id,
            "uid" => $uid
            )
        );
        
        if($audit_count > 0) {
            return - 1;
        }
        $shop_member_association = new NfxShopMemberAssociationModel();
        $condition = array(
            "shop_id" => $shop_id,
            "uid" => $uid
        );
        $user_shop_info = $shop_member_association->getInfo($condition, "promoter_id");
        $parent_promoter = 0;//上级推广员
        if (! empty($user_shop_info)) {
            $parent_promoter = $user_shop_info["promoter_id"];
        }
        $promoter_level = new NfxPromoterLevelModel();
        $level_info = $promoter_level->getFirstData(array("shop_id" => $shop_id),"level_money asc");
		$level=0;
		if(!empty($level_info)){
			$level = $level_info["level_id"];
			
		}else{
		    return -1;
		}
        $promoter = new NfxPromoterModel();
        $data = array(
            "parent_promoter" => $parent_promoter,
            "uid" => $uid,
            "shop_id" => $shop_id,
            "promoter_level" => $level,
            "promoter_shop_name" => $promoter_shop_name,
            "regidter_time" => time()
        );
        $promoter_return = $promoter->save($data);
        $promoter_id = $promoter->promoter_id;
        $shop_config = new NfxShopConfigModel();
        $shop_config_info = $shop_config->getInfo(array(
            "shop_id" => $shop_id
        ), "is_distribution_audit");
        $is_distribution_audit = $shop_config_info["is_distribution_audit"];
        if ($is_distribution_audit == 0) {
            $this->promoterAudit($promoter_id, 1, $shop_id);
        }
        return $promoter_id;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPromoter::promoterAudit()
     */
    public function promoterAudit($promoter_id, $is_audit, $shop_id)
    {
        $promoter = new NfxPromoterModel();
        $promoter_no = $this->createPromoterNo();
        $data = array(
            'is_audit' => $is_audit,
            "audit_time" => time(),
            "promoter_no"=>$promoter_no
        );
        $retval = $promoter->save($data, [
            'promoter_id' => $promoter_id
        ]);
        if ($retval > 0) {
            if ($is_audit == 1) {
                $promoter = new NfxPromoterModel();
                
                $promoter_info = $promoter->getInfo(array(
                    "promoter_id" => $promoter_id,
                    "shop_id" => $shop_id
                ), "uid");
                $uid = $promoter_info["uid"];
                $shop_member_association = new NfxShopMemberAssociationModel();
                $result = $shop_member_association->save(
                    array(
                    "promoter_id" => $promoter_id,
                    'is_promoter' => 1
                ),
                   array(
                    "shop_id" => $shop_id,
                    "uid" => $uid
                ) );
                
                $user_account = new NfxUserAccountModel();
                $user_account_data = array(
                    "shop_id" => $shop_id,
                    "uid" => $uid
                );
                $res = $user_account->save($user_account_data);
            }
        }
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPromoter::updatePromoter()
     */
    public function updatePromoter()
    {
        $promoter = new NfxPromoterModel();
        $data = array();
        $retval = $promoter->save($data);
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPromoter::promoterCommissionCalculate()
     */
    public function promoterCommissionCalculate($order_id)
    {
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPromoter::getPromoterLevelList()
     */
    public function getPromoterLevelList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $promoter_level = new NfxPromoterLevelModel();
        $list = $promoter_level->pageQuery($page_index, $page_size, $condition, $order, '*');
        return $list;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPromoter::addPromoterLevel()
     */
    public function addPromoterLevel($shop_id, $level_name, $level_money, $level_0, $level_1, $level_2)
    {
        $promoter_level = new NfxPromoterLevelModel();
        $data = array(
            "shop_id" => $shop_id,
            "level_name" => $level_name,
            "level_money" => $level_money,
            "level_0" => $level_0,
            "level_1" => $level_1,
            "level_2" => $level_2,
            "create_time" => time()
        );
        $promoter_level->save($data);
        return $promoter_level->level_id;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPromoter::updatePromoterLevel()
     */
    public function updatePromoterLevel($level_id, $level_name, $level_money, $level_0, $level_1, $level_2)
    {
        $promoter_level = new NfxPromoterLevelModel();
        $data = array(
            "level_name" => $level_name,
            "level_money" => $level_money,
            "level_0" => $level_0,
            "level_1" => $level_1,
            "level_2" => $level_2,
            "modify_time" => time()
        );
        $retval = $promoter_level->save($data, [
            "level_id" => $level_id
        ]);
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPromoter::getPromoterDetail()
     */
    public function getPromoterDetail($promoter_id)
    {
        $promoter = new NfxPromoterModel();
        $data = $promoter->get($promoter_id);
        if (! empty($data)) {
            // 查询推广员等级
            $data['promoter_level_info'] = $this->getPromoterLevalDetail($data['promoter_level']);
            // 查询推广员佣金
            $user_commission = new NfxUserAccountModel();
            $data['commission'] = $user_commission->getInfo([
                'uid' => $data['uid'],
                'shop_id' => $data['shop_id']
            ], '*');
            
            // 查询推广员用户信息
            $shop_member_association = new NfxShopMemberAssociationModel();
            $data['shop_user_info'] = $shop_member_association->getInfo([
                'uid' => $data['uid'],
                'shop_id' => $data['shop_id']
            ], '*');
            
            // 查询团队人数
            
            // 查询会员团队人数
            $team_count = $this->getPromoterTeamList($promoter_id);
            $data['team_count'] = count($team_count);
        }
        return $data;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPromoter::getPromoterParent()
     */
    public function getPromoterParent($promoter_id)
    {
        $promoter = new NfxPromoterModel();
        $data = $promoter->getInfo([
            'promoter_id' => $promoter_id
        ], 'parent_promoter');
        return $data;
        
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPromoter::modifyPromoterParent()
     */
    public function modifyPromoterParent($promoter_id, $parent_no, $shop_id)
    {
        
        $promoter = new NfxPromoterModel();
        $promoter_info = $promoter->getInfo(array(
            "promoter_no" => $parent_no,
            "shop_id" => $shop_id,
            "is_audit" => 1
        ));
        if (count($promoter_info) == 0) {
            return 0;
        }
        if($promoter_info["promoter_id"] == $promoter_id){
            return 0;
        }
        // 判断所改父级是否是自己的自级
        $parent_array = $this->getPromoterParentQuery($promoter_info["promoter_id"], $shop_id);
        if (in_array($promoter_id, $parent_array)) {
            return 0;
        }
        $promoter = new NfxPromoterModel();
        $data = array(
            "parent_promoter" => $promoter_info["promoter_id"]
        );
        $retval = $promoter->save($data, [
            "promoter_id" => $promoter_id
        ]);
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPromoter::getPromoterPartner()
     */
    public function getPromoterPartner($promoter_id)
    {
        $promoter = new NfxPromoterModel();
        $data = $promoter->getInfo([
            'promoter_id' => $promoter_id,
            "is_audit" => 1
        ], '');
        return $data;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPromoter::getPromoterLevalDetail()
     */
    public function getPromoterLevalDetail($level_id)
    {
        $promoter_level = new NfxPromoterLevelModel();
        $data = $promoter_level->get($level_id);
        return $data;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPromoter::modifyPromoterLock()
     */
    public function modifyPromoterLock($promoter_id, $is_lock)
    {
        $promoter = new NfxPromoterModel();
        $data = array(
            "is_lock" => $is_lock
        );
        $retval = $promoter->save($data, [
            "promoter_id" => $promoter_id
        ]);
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPromoter::getCommissionDistributionList()
     */
    public function getCommissionDistributionList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        // TODO Auto-generated method stub
        $order_service = new OrderService();
        $order_list = $order_service->getOrderList($page_index, $page_size, $condition, $order);
        foreach ($order_list["data"] as $k => $v) {
            $commission_money = 0;
            
            foreach ($v["order_item_list"] as $l => $b) {
                $order_item = $v["order_item_list"][$l];
                $commission_distribution_list = array();
                $commossion_distribution = new NfxCommissionDistributionModel();
                $commossion_distribution_condition = array(
                    "order_id" => $b["order_id"],
                    "order_goods_id" => $b["order_goods_id"]
                );
                $commission_distribution_list = $commossion_distribution->all($commossion_distribution_condition);
                // var_dump($commission_distribution_list);
                if (count($commission_distribution_list) > 0) {
                    foreach ($commission_distribution_list as $commission_k => $commission_v) {
                        $promoter = new NfxPromoterModel();
                        $uid = 0;
                        $realname = "";
                        // var_dump($commission_v["promoter_id"]);
                        $promoter_info = $promoter->getInfo([
                            'promoter_id' => $commission_v["promoter_id"]
                        ], 'uid');
                        $uid = $promoter_info["uid"];
                        $user = new UserModel();
                        $user_info = $user->getInfo([
                            'uid' => $uid
                        ], "nick_name");
                        $realname = $user_info["nick_name"];
                        $commission_distribution_list[$commission_k]["realname"] = $realname;
                        $commission_money = $commission_money + $commission_v["commission_money"];
                        $promoter = new NfxPromoterModel();
                        $prometerinfo = $promoter->getInfo(['uid' => $uid]);
                        $commission_distribution_list[$commission_k]['promoter_no'] = $prometerinfo['promoter_no'];
                        $commission_distribution_list[$commission_k]['promoter_shop_name'] = $prometerinfo['promoter_shop_name'];
                    }
                }
                // var_dump($commission_distribution_list);
                $order_item["commission_distribution_list"] = $commission_distribution_list;
            }
            $order_list["data"][$k]["commission_money"] = $commission_money;
        }
        return $order_list;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\INfxPromoter::getUserPromoter()
     */
    public function getUserPromoter($uid, $shop_id)
    {
        $promoter = new NfxPromoterModel();
        $data = $promoter->where(array(
            "shop_id" => $shop_id,
            "uid" => $uid
        ))
            ->order("promoter_id desc")
            ->limit(0, 1)
            ->select();
        return empty($data) ? '' : $data[0];
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\INfxPromoter::getUserPromoterList()
     */
    public function getUserPromoterList($uid)
    {
        $promoter = new NfxPromoterModel();
        $user_promoter_list = $promoter->getQuery([
            'uid' => $uid,
            'is_audit' => 1
        ], '*', 'regidter_time desc');
        $shop = new NsShopModel();
        $shop_member_assiociation = new NfxShopMemberAssociationModel();
        $shop_account = new NsShopAccountModel();
        $user_account = new NfxUserAccountModel();
        foreach ($user_promoter_list as $item) {
            $shop_info = $shop->getInfo([
                'shop_id' => $item['shop_id']
            ], 'shop_name,shop_logo');
            $item['shop_name'] = $shop_info['shop_name'];
            $item['shop_logo'] = $shop_info['shop_logo'];
            // 查询会员相关信息
            $user_info = $shop_member_assiociation->getInfo([
                'shop_id' => $item['shop_id'],
                'uid' => $uid
            ], 'is_promoter, is_partner, region_center_id');
            $item['is_promoter'] = $user_info['is_promoter'];
            $item['is_partner'] = $user_info['is_partner'];
            $item['region_center_id'] = $user_info['region_center_id'];
            // 查询会员团队人数
            $team_count = $this->getPromoterTeamList($item['promoter_id']);
          
            $item['team_count'] = count($team_count);
            // 查询店铺信息
            $shop_account_info = $shop_account->getInfo([
                'shop_id' => $item['shop_id']
            ], 'shop_profit');
            $item['shop_profit'] = $shop_account_info['shop_profit'];
            // 查询会员佣金信息
            $account_info = $user_account->getInfo([
                'shop_id' => $item['shop_id'],
                'uid' => $uid
            ], 'commission');
            $item['commission'] = round($account_info['commission'], 2);
        }
        return $user_promoter_list;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\INfxPromoter::getPromoterLevelAll()
     */
    public function getPromoterLevelAll($shop_id)
    {
        // TODO Auto-generated method stub
        $promoter_level = new NfxPromoterLevelModel();
        $data = $promoter_level->all(array(
            "shop_id" => $shop_id
        ));
        return $data;
    }

    /**
     * 查询推广员的上级
     * 返回上级推广云uid 以，隔开
     */
    private function getPromoterParentQuery($promoter_id, $shop_id)
    {
        $is_go = 0;
        $parent_array = array();
        while ($is_go == 0) {
            $promoter = new NfxPromoterModel();
            $promoter_info = $promoter->getInfo(array(
                "shop_id" => $shop_id,
                "promoter_id" => $promoter_id,
                "is_audit" => 1
            ));
            if (!empty($promoter_info)) {
                $promoter_id = $promoter_info["parent_promoter"];
                $parent_array[] = $promoter_info["parent_promoter"];
            } else {
                $is_go = 1;
            }
        }
        return $parent_array;
    }


    /*
     * (non-PHPdoc)
     * @see \data\api\INfxPromoter::getPromoterAll()
     */
    public function getPromoterAll($condition)
    {
        // TODO Auto-generated method stub
        $promoter = new NfxPromoterModel();
        $promoter_all = $promoter->all($condition);
        return $promoter_all;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\INfxPromoter::deletePromoterLevel()
     */
    public function deletePromoterLevel($shop_id, $level_id)
    {
        // TODO Auto-generated method stub
        $promoter = new NfxPromoterModel();
        $count = $promoter->getCount(array(
            "shop_id" => $shop_id,
            "promoter_level" => $level_id
        ));
        if ($count == 0) {
            $promoter_level = new NfxPromoterLevelModel();
            $retval = $promoter_level->where(array(
                "shop_id" => $shop_id,
                "level_id" => $level_id
            ))->delete();
            return $retval;
        } else {
            return 0;
        }
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\INfxPromoter::getDistributionCommissionCount()
     */
    public function getDistributionCommissionCount($condition)
    {
        // TODO Auto-generated method stub
        $distribution_commission = new NfxCommissionDistributionModel();
        $result = $distribution_commission->getQuery($condition, "sum(commission_money) as sum", '');
        return $result[0]["sum"];
    }
    
    /*
     * (non-PHPdoc)
     * @see \data\api\INfxPromoter::getPromoterParentByUidAndShopid()
     */
    public function getPromoterParentByUidAndShopid($shop_id, $uid){
        $promoter = new NfxPromoterModel();
        $array = array(
            'parent_promoter' => 0,
            'parent_uid' => 0
        );
        $data = $promoter->getInfo(['shop_id' => $shop_id, 'uid' => $uid], 'parent_promoter');
        $array['parent_promoter'] = $data['parent_promoter'] > 0 ? $data['parent_promoter'] : 0;
        if($data['parent_promoter'] > 0){
            $parent_uid = $promoter->getInfo(['promoter_id' => $data['parent_promoter']], 'uid');
            $array['parent_uid'] = $parent_uid['uid'] > 0 ? $parent_uid['uid'] : 0;
        }
        return $array;
    }

    /**
     * (non-PHPdoc)
     * @see \data\api\INfxPromoter::getPromoterChildren()
     */
    public function getPromoterChildren($promoter_id){
        $promoter = new NfxPromoterModel();
        $list = $promoter->getQuery(['parent_promoter'=>$promoter_id], 'promoter_id', '');
        return $list;
    }
    
    /**
     * 查询团队列表
     *
     * {@inheritdoc}
     *
     * @see \data\api\INfxPromoter::getPromoterTeamList()
     */
    public function getPromoterTeamList($promoter_id)
    {
        $array_self = array(
            '0' => array('promoter_id' => $promoter_id)
        );
        $array_one = array();
        $array_two = array();
        $array_three = array();
        $shop_member = new NfxShopMemberAssociationModel();
        //下一级推广员
        $array_one = $this->getPromoterChildren($promoter_id);
//         var_dump($array_one);
        if(!empty($array_one)){
            $array_two_new = array();
            foreach ($array_one as $k=>$v){
                $array_two_new = $this->getPromoterChildren($v['promoter_id']);
                //下两级推广员
                $array_two = array_merge($array_two,$array_two_new);
                if(!empty($array_two_new)){
                    $array_three_new = array();
                    foreach ($array_two_new as $ko => $vo){
                        $array_three_new = $this->getPromoterChildren($vo['promoter_id']);
                        //下三级推广员
                        $array_three = array_merge($array_three,$array_three_new);
                    }
                }
            }
        }
        //推广员数组 包括自己  下级   下下级   下下下级
        $array = array_merge($array_self, $array_one, $array_two, $array_three);
        $data = array();
        $data_new = array();
        foreach ($array as $ka=>$va){
            $data_new = $shop_member->getQuery(['promoter_id'=>$va['promoter_id']], '*', '');
            $data = array_merge($data_new,$data);
        }
        $user = new UserModel();
        //查询会员信息
        if (! empty($data)) {
            foreach ($data as $k => $v) {
                if($v['source_uid'] > 0){
                    $recommender_name = $user->getInfo(["uid" => $v['source_uid']], "nick_name");
                    $data[$k]["recommender_name"] = $recommender_name['nick_name'];
                }else{
                    $data[$k]["recommender_name"] = '';
                }
                if($v['is_partner'] == 1){
                    $data[$k]["role_name"] = '股东';
                }else if($v['is_promoter'] == 1){
                    $data[$k]["role_name"] = '推广员';
                }else{
                    $data[$k]['role_name'] = '会员';
                }
                $data[$k]["user_info"] = $user->getInfo(["uid" => $v["uid"]], "nick_name,user_headimg,reg_time");
            }
        }
        return $data;
    }
    /*
     * (non-PHPdoc)
     * @see \data\api\INfxPromoter::createPromoterNo()
     */
    private function createPromoterNo(){
    
        $promoter_no = '';
        $promoter = new NfxPromoterModel();
        $promoter_info = $promoter->getFirstData("promoter_no != ''", "promoter_no desc");
        
        if(empty($promoter_info)){
            $promoter_no = "FX".sprintf("%06d", 1);
        }else{
            $max_id = substr($promoter_info["promoter_no"],2) +1;
            $promoter_no = "FX".sprintf("%06d", $max_id);
//             $max_id = $promoter_info["promoter_id"]+1;
//             $no = "FX".sprintf("%06d", $max_id);
//             $promoter_list=$promoter->getInfo(['promoter_no'=>$no],'*');
//             while(!empty($promoter_list)){
//                 $max_id++;                
//                 $no = "FX".sprintf("%06d", $max_id);
//                 $promoter_list=$promoter->getInfo(['promoter_no'=>$no],'*');
//             }
//             $promoter_no = $no;            
        }               
        return $promoter_no;
    }
	/* (non-PHPdoc)
     * @see \data\api\INfxPromoter::deletePromoter()
     */
    public function deletePromoter($shop_id, $promoter_id)
    {
        $promoter = new NfxPromoterModel();
        $retval = $promoter->destroy(["shop_id"=>$shop_id, "promoter_id"=>$promoter_id, "is_audit"=>-1]);
        return $retval;

        // TODO Auto-generated method stub
        
    }
	/* (non-PHPdoc)
     * @see \data\api\INfxPromoter::modifyPromoterLevel()
     */
    public function modifyPromoterLevel($shop_id, $promoter_id, $level_id)
    {
        // TODO Auto-generated method stub
        $promoter = new NfxPromoterModel();
        $promoter_info = $promoter->getInfo(array("shop_id"=>$shop_id,"promoter_id"=>$promoter_id,"is_audit"=>1),"*");
        if(empty($promoter_info)){
            return 0;
        }
        if($promoter_info["promoter_level"] == $level_id){
            return 0;
        }
        $retval = $promoter->save(array("promoter_level"=>$level_id),array("shop_id"=>$shop_id,"promoter_id"=>$promoter_id));
        return $retval;
    }
    /*
     * 
     * @param unknown $promoter_id
     * @param unknown $promoter_shop_name
     */
    public function modifyShopName($shop_id, $promoter_id, $promoter_shop_name){
        $promoter = new NfxPromoterModel();
        $data = array(
            'shop_id' => $shop_id,
            "promoter_shop_name" => $promoter_shop_name
        );
        $retval = $promoter->save($data, [ "promoter_id" => $promoter_id]);
        return $retval;
  
    }
    
    /**
     * 查询团队列表  后台
     *
     * {@inheritdoc}
     *
     * @see \data\api\INfxPromoter::getPromoterTeamList()
     */
    public function getAdminPromoterTeamList($promoter_id)
    {
        $array_self = array(
            '0' => array('promoter_id' => $promoter_id)
        );
        $array_one = array();
        $array_two = array();
        $array_three = array();
        $shop_member = new NfxShopMemberAssociationModel();
        //下一级推广员
        $array_one = $this->getPromoterChildren($promoter_id);
        //         var_dump($array_one);
        if(!empty($array_one)){
            $array_two_new = array();
            foreach ($array_one as $k=>$v){
                $array_two_new = $this->getPromoterChildren($v['promoter_id']);
                //下两级推广员
                $array_two = array_merge($array_two,$array_two_new);
                if(!empty($array_two_new)){
                    $array_three_new = array();
                    foreach ($array_two_new as $ko => $vo){
                        $array_three_new = $this->getPromoterChildren($vo['promoter_id']);
                        //下三级推广员
                        $array_three = array_merge($array_three,$array_three_new);
                    }
                }
            }
        }
        //推广员数组 包括自己  下级   下下级   下下下级
        $array = array_merge($array_self, $array_one, $array_two, $array_three);
        $data = array();
        $data_new = array();
        $data_promoter = array();
        $promoter = new NfxPromoterModel();

        foreach ($array as $ka=>$va){
            $data_new = $shop_member->getQuery(['promoter_id'=>$va['promoter_id']], '*', '');
            $data = array_merge($data_new, $data);
        }
        $user = new UserModel();
        //查询会员信息
        if (! empty($data)) {
            foreach ($data as $k => $v) {
                if($v['source_uid'] > 0){
                    $recommender_name = $user->getInfo(["uid" => $v['source_uid']], "nick_name");
                    $data[$k]["recommender_name"] = $recommender_name['nick_name'];
                }else{
                    $data[$k]["recommender_name"] = '';
                }
                if($v['is_partner'] == 1){
                    $data[$k]["role_name"] = '股东';
                }else if($v['is_promoter'] == 1){
                    $data[$k]["role_name"] = '推广员';
                }else{
                    $data[$k]['role_name'] = '会员';
                }
                $data[$k]["user_info"] = $user->getInfo([
                    "uid" => $v["uid"]
                ], "nick_name,user_headimg,reg_time");
                
                $promoter_info = $promoter->getInfo(['uid'=>$v['uid']], '*');
                $promoter_level = new NfxPromoterLevelModel();
                $level_name = $promoter_level->getInfo(['level_id'=>$promoter_info['promoter_level']], 'level_name');
                $promoter_info['level_name'] = $level_name['level_name'];
                $data[$k]["promoter_info"] = $promoter_info;

            }
        }
        
        return $data;
    }

    /**
     * 查询会员是否是推广员 和编号
     */
    public function getShopMemberAssociation($uid){
        $member_association = new NfxShopMemberAssociationModel();
        $member_association_info = $member_association->getInfo(['uid'=>$uid],'*');
    
        $promoter_id = $member_association_info['promoter_id'];
    
        $member_promoter = array();
        $member_promoter['is_promoter'] = $member_association_info['is_promoter'];
        $member_promoter['promoter_id'] = $member_association_info['promoter_id'];
    
        if($promoter_id != 0){
            $promoter = new NfxPromoterModel();
            $promoter_no = $promoter->getInfo(['promoter_id'=>$promoter_id],'promoter_no')['promoter_no'];
            $member_promoter['promoter_no'] = $promoter_no;
        }else{
            $member_promoter['promoter_no'] = '';
        }
         
        return $member_promoter;
    }
    
    /**
     * 验证是否存在该会员编号
     * @param unknown $promoter_no
     */
    public function  modifyPromoterNo($promoter_no){
        $promoter = new NfxPromoterModel();
        $promoter_info = $promoter->getInfo(['promoter_no'=>$promoter_no],'*');
        if(empty($promoter_info)){
            return 0;
        }else{
            return 1;
        }
    
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IMember::updateMemberByAdmin()
     */
    public function updateMemberByAdmin($uid, $promoter_no)
    {
        if(!empty($promoter_no)){
            //根据会员编号查询promoter_id
            $promoter = new NfxPromoterModel();
            $promoter_id = $promoter->getInfo(['promoter_no'=>$promoter_no],'promoter_id')['promoter_id'];
            //修改会员推广员上级
            $member_association = new NfxShopMemberAssociationModel();
            $res = $member_association->save(['promoter_id'=>$promoter_id], ['uid' => $uid]);
            return $res;
        }

        
    }
    /**
     * 修改推广员上级会员
     * @param unknown $uid
     * @param unknown $promoter_uid
     * @param unknown $shop_id
     * @param unknown $type
     */
    public function updateMemberPromoter($uid, $promoter_uid, $shop_id)
    {
        $shop_member_association = new NfxShopMemberAssociationModel();
        $promoter_info = $shop_member_association->getInfo(['uid' => $uid], 'promoter_id,is_promoter');
        if(!empty($promoter_info))
        {
            if($promoter_info['is_promoter'] == 1){
                return 1;
            }else{
                if($promoter_info['promoter_id'] != 0)
                {
                    return 1;
                }else{
                    if($promoter_uid)
                    {
                        $parent_promoter = $shop_member_association->getInfo(['uid' => $promoter_uid], 'promoter_id');
                        $res = $shop_member_association->save(['promoter_id'=>$parent_promoter['promoter_id']], ['uid' => $uid]);
                        return $res;
                        
                    }else{
                        return 1;
                    }
                }
            }
        }else{
            return 1;
        }
    }
    
    /**
     * 会员列表
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
     */
    public function getMemberList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        $member_view = new NsMemberViewModel();
        $result = $member_view->getViewList($page_index, $page_size, $condition, $order);
        foreach ($result['data'] as $k => $v) {
            $member_account = new MemberAccount();
            $result['data'][$k]['point'] = $member_account->getMemberPoint($v['uid'], '');
            $result['data'][$k]['balance'] = $member_account->getMemberBalance($v['uid']);
            $result['data'][$k]['coin'] = $member_account->getMemberCoin($v['uid']);
             
            $result['data'][$k]['member_promoter'] = $this->getShopMemberAssociation($v['uid']);
        }
        return $result;
    }
    
    
    
}