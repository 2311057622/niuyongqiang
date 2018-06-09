<?php
/**
 * NbsBusinessAssistantAccountModel.php
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
use data\api\INfxPartner;
use data\model\NfxPartnerModel;
use data\model\NfxPartnerLevelModel;
use data\model\UserModel as UserModel;
use data\service\Order as OrderService;
use data\model\NfxCommissionPartnerModel;
use data\service\NfxPartnerGlobalCalculate;
use data\model\NfxCommissionPartnerGlobalModel;
use data\model\NfxCommissionPartnerGlobalRecordsModel;
use data\model\NfxShopMemberAssociationModel;
use data\model\NfxShopConfigModel;
use data\model\NfxCommissionDistributionModel;
use data\model\NfxPromoterModel;

/**
 * 股东服务层
 */
class NfxPartner extends BaseService implements INfxPartner
{

    /*
     * (non-PHPdoc)
     * @see \data\api\IPartner::getPartnerList()
     */
    public function getPartnerList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        // TODO Auto-generated method stub
        $partner = new NfxPartnerModel();
        $list = $partner->pageQuery($page_index, $page_size, $condition, $order, '*');
        if (! empty($list['data'])) {
            foreach ($list['data'] as $k => $v) {
                $user = new UserModel();
                $userinfo = $user->getInfo([
                    'uid' => $v['uid']
                ]);
                $list['data'][$k]['real_name'] = $userinfo["nick_name"];
                $list['data'][$k]['user_tel'] = $userinfo["user_tel"];
                $promoter = new NfxPromoterModel();
                $prometerinfo = $promoter->getInfo(['uid' => $v['uid']]);
                $list['data'][$k]['promoter_no'] = $prometerinfo['promoter_no'];
                $partner_level = new NfxPartnerLevelModel();
                $level_info = $partner_level->getInfo([
                    'level_id' => $v['partner_level']
                ]);
                $list['data'][$k]['level_name'] = $level_info["level_name"];
                $parent_realname = "";
                $parent_promoter_no = "";
                if ($v['parent_partner'] != 0) {
                    $partner = new NfxPartnerModel();
                    $parent_info = $partner->getInfo([
                        'partner_id' => $v['parent_partner']
                    ]);
                    $parent_uid = $parent_info["uid"];
                    $parent_userinfo = $user->getInfo([
                        'uid' => $parent_uid
                    ]);
                    $parent_realname = $parent_userinfo["user_name"];
                    $parent_promoter_info = $promoter->getInfo(["promoter_id"=>$parent_info["promoter_id"]], "*");
                    $parent_promoter_no = $parent_promoter_info['promoter_no'];
                }
                $list['data'][$k]['parent_realname'] = $parent_realname;
                $list['data'][$k]['parent_promoter_no'] = $parent_promoter_no;
            }
        }
        return $list;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPartner::partnerApplay()
     */
    public function partnerApplay($shop_id, $uid)
    {
        $shop_member_association = new NfxShopMemberAssociationModel();
        $condition = array(
            "shop_id" => $shop_id,
            "uid" => $uid
        );
        $user_shop_info = $shop_member_association->getInfo($condition, "partner_id, promoter_id");
        $parent_partner = 0;
        $promoter_id = 0;
        if (! empty($user_shop_info)) {
            $parent_partner = $user_shop_info["partner_id"];
            $promoter_id = $user_shop_info["promoter_id"];
        }
        $partner = new NfxPartnerModel();
        $partner_apply_count = $partner->getCount(["shop_id"=>$shop_id, "uid"=>$uid,"is_audit"=>["neq",-1]]);
        if($partner_apply_count > 0){
            return 0;
        }
        $partner_level = new NfxPartnerLevelModel();
        $level_info = $partner_level->where(array(
            "shop_id" => $shop_id
        ))
            ->order("level_money asc")
            ->limit(0, 1)
            ->select();
        $level = $level_info[0]["level_id"];
        $partner = new NfxPartnerModel();
        $data = array(
            "promoter_id" => $promoter_id,
            "parent_partner" => $parent_partner,
            "uid" => $uid,
            "shop_id" => $shop_id,
            "partner_level" => $level,
            "register_time" => time()
        );
        $retval = $partner->save($data);
        return $partner->partner_id;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPartner::partnerAudit()
     */
    public function partnerAudit($partner_id, $is_audit, $shop_id)
    {
        $partner = new NfxPartnerModel();
        $data = array(
            "is_audit" => $is_audit,
            "audit_time" => time()
        );
        $retval = $partner->save($data, [
            "partner_id" => $partner_id
        ]);
        if ($retval > 0) {
            if ($is_audit == 1) {
                $partner = new NfxPartnerModel();
                $partner_info = $partner->getInfo(array(
                    "partner_id" => $partner_id,
                    "shop_id" => $shop_id
                ), "uid");
                $uid = $partner_info["uid"];
                $shop_member_association = new NfxShopMemberAssociationModel();
                $result = $shop_member_association->where(array(
                    "shop_id" => $shop_id,
                    "uid" => $uid
                ))->update(array(
                    "partner_id" => $partner_id,
                    'is_partner' => 1
                ));
            }
        }
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPartner::updatePartner()
     */
    public function updatePartner($partner_level, $partner_id)
    {
        $partner = new NfxPartnerModel();
        $data = array(
            "modify_time" => time(),
            "partner_level" => $partner_level
        );
        $retval = $partner->save($data, [
            "partner_id" => $partner_id
        ]);
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPartner::partnerCommissionCalculate()
     */
    public function partnerCommissionCalculate($order_id)
    {
        
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPartner::getPartnerLevelList()
     */
    public function getPartnerLevelList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $partner_level = new NfxPartnerLevelModel();
        $list = $partner_level->pageQuery($page_index, $page_size, $condition, $order, '*');
        return $list;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPartner::addPartnerLevel()
     */
    public function addPartnerLevel($level_money, $level_name, $commission_rate, $shop_id)
    {
        $partner_level = new NfxPartnerLevelModel();
        $data = array(
            "level_money" => $level_money,
            "level_name" => $level_name,
            "commission_rate" => $commission_rate,
            "create_time" => time(),
            "shop_id" => $shop_id
        );
        $partner_level->save($data);
        return $partner_level->level_id;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPartner::updatePartnerLevel()
     */
    public function updatePartnerLevel($level_id, $level_name, $level_money, $commission_rate, $shop_id)
    {
        $partner_level = new NfxPartnerLevelModel();
        $data = array(
            "level_money" => $level_money,
            "level_name" => $level_name,
            "commission_rate" => $commission_rate,
            "modify_time" => time()
        );
        $retval = $partner_level->save($data, [
            "level_id" => $level_id,
            "shop_id" => $shop_id
        ]);
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPartner::getPartnerDetail()
     */
    public function getPartnerDetail($partner_id)
    {
        $partner = new NfxPartnerModel();
        $data = $partner->get($partner_id);
        return $data;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPartner::getPartnerLevelDetail()
     */
    public function getPartnerLevelDetail($level_id)
    {
        $partner_level = new NfxPartnerLevelModel();
        $data = $partner_level->get($level_id);
        return $data;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPartner::modifyPartnerLock()
     */
    public function modifyPartnerLock($partner_id, $is_lock)
    {
        $partner = new NfxPartnerModel();
        $data = array(
            "is_lock" => $is_lock
        );
        $retval = $partner->save($data, [
            "partner_id" => $partner_id
        ]);
        return $retval;
        // TODO Auto-generated method stub
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\INfxPartner::getPertnerParents()
     */
    public function getPartnerParents($partner_id)
    {
        $partner = new NfxPartnerModel();
        $partner_info = $partner->getInfo([
            'partner_id' => $partner_id
        ], 'partner_id,promoter_id,uid,parent_partner,shop_id,partner_level,is_audit,is_lock');
        $parents_array = array();
        $parents_array[] = $partner_info;
        while ($partner_info['parent_partner'] != 0) {
            $partner_info = $partner->getInfo([
                'partner_id' => $partner_info['parent_partner']
            ], 'partner_id,promoter_id,uid,parent_partner,shop_id,partner_level,is_audit,is_lock');
            $parents_array[] = $partner_info;
        }
        return $parents_array;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\INfxPartner::getCommissionPartnerList()
     */
    public function getCommissionPartnerList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        // TODO Auto-generated method stub
        // TODO Auto-generated method stub
        $order_service = new OrderService();
        $order_list = $order_service->getOrderList($page_index, $page_size, $condition, $order);
        foreach ($order_list["data"] as $k => $v) {
            $commission_money = 0;
            
            foreach ($v["order_item_list"] as $l => $b) {
                $order_item = $v["order_item_list"][$l];
                $commission_distribution_list = array();
                $commission_partner = new NfxCommissionPartnerModel();
                $commission_partner_condition = array(
                    "order_id" => $b["order_id"],
                    "order_goods_id" => $b["order_goods_id"]
                );
                $commission_partner_list = $commission_partner->all($commission_partner_condition);
                if (count($commission_partner_list) > 0) {
                    foreach ($commission_partner_list as $commission_k => $commission_v) {
                        $protner = new NfxPartnerModel();
                        $uid = 0;
                        $realname = "";
                        // var_dump($commission_v["promoter_id"]);
                        $protner_info = $protner->getInfo([
                            'partner_id' => $commission_v["partner_id"]
                        ], 'uid');
                        $uid = $protner_info["uid"];
                        $user = new UserModel();
                        $user_info = $user->getInfo([
                            'uid' => $uid
                        ], "nick_name");
                        $realname = $user_info["nick_name"];
                        $commission_partner_list[$commission_k]["realname"] = $realname;
                        $commission_money = $commission_money + $commission_v["commission_money"];
                        $promoter = new NfxPromoterModel();
                        $prometerinfo = $promoter->getInfo(['uid' => $uid]);
                        $commission_partner_list[$commission_k]['promoter_no'] = $prometerinfo['promoter_no'];
                        $commission_partner_list[$commission_k]['promoter_shop_name'] = $prometerinfo['promoter_shop_name'];
                    }
                }
                // var_dump($commission_distribution_list);
                $order_item["commission_partner_list"] = $commission_partner_list;
            }
            $order_list["data"][$k]["commission_money"] = $commission_money;
        }
        return $order_list;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\INfxPartner::getCommissionPartnerGlobalList()
     */
    public function getCommissionPartnerGlobalList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        // TODO Auto-generated method stub
        $commission_partner_global = new NfxCommissionPartnerGlobalModel();
        $list = $commission_partner_global->pageQuery($page_index, $page_size, $condition, $order, '*');
        if (! empty($list['data'])) {
            foreach ($list['data'] as $k => $v) {
                $user = new UserModel();
                $userinfo = $user->get([
                    'uid' => $v['uid']
                ]);
                $list['data'][$k]['real_name'] = $userinfo["user_name"];
            }
        }
        return $list;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\INfxPartner::getPartnerLevelAll()
     */
    public function getPartnerLevelAll($shop_id)
    {
        $partner_level = new NfxPartnerLevelModel();
        $condition = array(
            "shop_id" => $shop_id
        );
        $all = $partner_level->all($condition);
        return $all;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\INfxPartner::updatePartnerGlobal()
     */
    public function updatePartnerGlobal($level_array, $shop_id)
    {
        $partner_level = new NfxPartnerLevelModel();
        $partner_level->startTrans();
        try {
            foreach ($level_array as $k => $v) {
                $partner_level = new NfxPartnerLevelModel();
                $data = array(
                    "global_value" => $v[1],
                    "global_weight" => $v[2],
                    "modify_time" => time()
                );
                $res = $partner_level->save($data, [
                    "level_id" => $v[0],
                    "shop_id" => $shop_id
                ]);
            }
            $partner_level->commit();
            return 1;
        } catch (\Exception $e) {
            $partner_level->rollback();
            return $e->getMessage();
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\INfxPartner::getPartnerLevelGlobalList()
     */
    public function getPartnerLevelGlobalList($shop_id)
    {
        $partner_level_list = $this->getPartnerLevelAll($shop_id);
        foreach ($partner_level_list as $k => $v) {
            $partner_global_calculate = new NfxPartnerGlobalCalculate();
            $partner_level_list[$k]["global_value_num"] = $partner_global_calculate->getPartnerLevelValue($shop_id, $v["level_id"]);
            $partner = new NfxPartnerModel();
            $where["partner_level"] = $v["level_id"];
            $where["shop_id"] = $shop_id;
            $partner_level_list[$k]["level_num"] = $partner->where($where)->count("partner_id");
        }
        return $partner_level_list;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\INfxPartner::getCommissionPartnerGlobalRecordsList()
     */
    public function getCommissionPartnerGlobalRecordsList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $commission_partner_global_records = new NfxCommissionPartnerGlobalRecordsModel();
        $list = $commission_partner_global_records->pageQuery($page_index, $page_size, $condition, $order, '*');
        return $list;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\INfxPartner::getPartnerValidDetail()
     */
    public function getPartnerValidDetail($shop_id, $uid)
    {
        $partner = new NfxPartnerModel();
        $data = $partner->where(array(
            "shop_id" => $shop_id,
            "uid" => $uid
        ))
            ->order("partner_id desc")
            ->limit(0, 1)
            ->select();
        return empty($data) ? '' : $data[0];
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\INfxPartner::updatePartnerGlobal()
     */
    public function updatePartnerLevelAll($level_array, $shop_id)
    {
        $partner_level = new NfxPartnerLevelModel();
        $partner_level->startTrans();
        try {
            // 循环修改股东等级配置
            foreach ($level_array as $k => $v) {
                $partner_level = new NfxPartnerLevelModel();
                $data = array(
                    "level_money" => $v[1],
                    "commission_rate" => $v[2],
                    "modify_time" => time()
                );
                $res = $partner_level->save($data, [
                    "level_id" => $v[0],
                    "shop_id" => $shop_id
                ]);
            }
            $partner_level->commit();
            return 1;
        } catch (\Exception $e) {
            $partner_level->rollback();
            return $e->getMessage();
        }
    }
    /**
     * 修改店铺分销配置
     */
    public function updateNfxShopConfigField($shop_id, $field_name, $field_value){
        $shop_config = new NfxShopConfigModel();
        $retval = $shop_config -> ModifyTableField("shop_id", $shop_id, $field_name, $field_value);
        return $retval;
    }
    /*
     * (non-PHPdoc)
     * @see \data\api\INfxPartner::deletePartnerLevel()
     */
    public function deletePartnerLevel($shop_id, $level_id)
    {
        $partner = new NfxPartnerModel();
        $count = $partner->where(array(
            "shop_id" => $shop_id,
            "partner_level" => $level_id
        ))->count();
        if ($count == 0) {
            $partner_level = new NfxPartnerLevelModel();
            $retval = $partner_level->where(array(
                "shop_id" => $shop_id,
                "level_id" => $level_id
            ))->delete();
            return $retval;
        } else {
            return 0;
        }
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\INfxPartner::modifyPartnerLevelNum()
     */
    public function modifyPartnerLevelNum($shop_id, $uid, $level_id)
    {
        // TODO Auto-generated method stub
        $partner = new NfxPartnerModel();
        $retval = $partner->where(array(
            "shop_id" => $shop_id,
            "uid" => $uid
        ))->update(array(
            "partner_level" => $level_id
        ));
        return $retval;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\INfxPartner::getPartnerAll()
     */
    public function getPartnerAll($condition)
    {
        // TODO Auto-generated method stub
        $partner = new NfxPartnerModel();
        $partner_all = $partner->all($condition);
        return $partner_all;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\INfxPartner::getPartnerCommissionCount()
     */
    public function getPartnerCommissionCount($condition)
    {
        // TODO Auto-generated method stub
        $partner_commission = new NfxCommissionPartnerModel();
        $result = $partner_commission->getQuery($condition, "sum(commission_money) as sum", '');
        return $result[0]["sum"];
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\INfxPartner::getPartnerParentByUidAndShopid()
     */
    public function getPartnerParentByUidAndShopid($shop_id, $uid){
        $partner = new NfxPartnerModel();
        $array = array(
            'parent_partner' => 0,
            'parent_uid' => 0
        );
        $data = $partner->getInfo(['shop_id' => $shop_id, 'uid' => $uid], 'parent_partner');
        $array['parent_partner'] = $data['parent_partner'] > 0 ? $data['parent_partner'] : 0;
        if($data['parent_partner'] > 0){
            $parent_uid = $partner->getInfo(['partner_id' => $data['parent_partner']], 'uid');
            $array['parent_uid'] = $parent_uid['uid'] > 0 ? $parent_uid['uid'] : 0;
        }
        return $array;
    }
	/* (non-PHPdoc)
     * @see \data\api\INfxPartner::modifyPartherParnet()
     */
    public function modifyPartherParnet($partner_id, $parent_no, $shop_id)
    {
        // TODO Auto-generated method stub
        $promoter = new NfxPromoterModel();
        $promoter_info = $promoter->getInfo(array(
            "promoter_no" => $parent_no,
            "shop_id" => $shop_id,
            "is_audit" => 1
        ));
        if (count($promoter_info) == 0) {
            return 0;
        }
        $partner = new NfxPartnerModel();
        $partner_info = $partner->getInfo(["promoter_id"=>$promoter_info["promoter_id"],"is_audit"=>1, "shop_id"=>$shop_id],"*");
        if (empty($partner_info)){
            return 0;
        }
        if($partner_info["partner_id"] == $partner_id){
            return 0;
        }
        // 判断所改父级是否是自己的自级
        $parent_array = $this->getPartnerParentQuery($partner_id, $shop_id);
        if (in_array($partner_info["partner_id"], $parent_array)) {
            return 0;
        }

        $partner = new NfxPartnerModel();
        $data = array(
            "parent_partner" => $partner_info["partner_id"]
        );
        $retval = $partner->save($data, [
            "partner_id" => $partner_id
        ]);
        return $retval;
    }
    
    /**
     * 查询股东的上级
     * 返回上级推广云uid 以，隔开
     */
    private function getPartnerParentQuery($partner_id, $shop_id)
    {
        $is_go = 0;
        $parent_array = array();
        while ($is_go == 0) {
            $partner = new NfxPartnerModel();
            $promoter_info = $partner->getInfo(array(
                "shop_id" => $shop_id,
                "partner_id" => $partner_id,
                "is_audit" => 1
            ), "*");
            if (!empty($promoter_info)) {
                $partner_id = $promoter_info["parent_partner"];
                $parent_array[] = $promoter_info["parent_partner"];
            } else {
                $is_go = 1;
            }
        }
        return $parent_array;
    }

}
