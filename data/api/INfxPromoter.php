<?php
/**
 * INfxPromoter.php
 *
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @desc : 推广员接口
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace data\api;

interface INfxPromoter
{
   /**
     * 获取推广员列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    function getPromoterList($page_index = 1, $page_size = 0, $condition = '', $order = '');
    /**
     * 推广员申请
     */
    function promoterApplay($uid, $shop_id, $promoter_shop_name);
    /**
     * 推广员审核
     * @param unknown $promoter_id
     * @param unknown $state
     */
    function promoterAudit($promoter_id, $is_audit, $shop_id);
    /**
     * 修改推广员
     */
    function updatePromoter();
    /**
     * 订单推广员佣金计算
     * @param unknown $order_id
     */
    function promoterCommissionCalculate($order_id);
    /**
     * 推广员等级列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    function getPromoterLevelList($page_index = 1, $page_size = 0, $condition = '', $order = '');
    /**
     * 推广员等级添加
     */
    function addPromoterLevel($shop_id, $level_name, $level_money, $level_0, $level_1, $level_2);
    /**
     * 推广员等级修改
     */
    function updatePromoterLevel($level_id, $level_name, $level_money, $level_0, $level_1, $level_2);
    /**
     * 获取推广员详细信息
     * @param unknown $promoter_id
     */
    function getPromoterDetail($promoter_id);
    /**
     * 获取推广员上级
     * @param unknown $promoter_id
     */
    function getPromoterParent($promoter_id);
    /**
     * 修改推广员上级
     * @param unknown $promoter_id
     * @param unknown $parent_promoter_id
    */
    function modifyPromoterParent($promoter_id, $parent_username, $shop_id);
    /**
     * 获取推广员上级股东
     * @param unknown $promoter_id
     */
    function getPromoterPartner($promoter_id);
    /**
     * 获取推广员等级详细信息
     * @param unknown $promoter_id
     */
    function getPromoterLevalDetail($level_id);
    /**
     * 推广员冻结或解冻
     * @param unknown $promoter_id
     * @param unknown $is_lock
     */
    function modifyPromoterLock($promoter_id,$is_lock);
    /**
     * 获取三级分销佣金列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    function getCommissionDistributionList($page_index = 1, $page_size = 0, $condition = '', $order = '');
    
    /**
     * 根据会员id与店铺id获取单条推广员信息
     * @param unknown $uid
     */
    function getUserPromoter($uid,$shop_id);
    
    /**
     * 获取会员推广店铺列表
     * @param unknown $uid
     */
    function getUserPromoterList($uid);
    /**
     * 获取店铺所有推广员等级
     * @param unknown $shop_id
     */
    function getPromoterLevelAll($shop_id);
    
    /**
     * 获取团队列表（自己，包括下 三级）
     * @param unknown $promoter
     */
    function getPromoterTeamList($promoter_id);
    /**
     * 获取推广员
     * @param unknown $condition
     */
    function getPromoterAll($condition);
    /**
     * 删除推广员等级
     * @param unknown $shop_id
     * @param unknown $uid
     */
    function deletePromoterLevel($shop_id, $level_id);
    /**
     * 获取推广员分销佣金
     * @param unknown $condition
     */
    function getDistributionCommissionCount($condition);
    /**
     * 获取推广员上级 根据 uid 和 shop_id
     * @param unknown $shop_id
     * @param unknown $uid
     */
    function getPromoterParentByUidAndShopid($shop_id, $uid);
    /**
     * 获取 推广员的下级 推广员
     * @param unknown $promoter_id
     */
    function getPromoterChildren($promoter_id);
    /**
     * 删除推广员
     * @param unknown $shop_id
     * @param unknown $promoter_id
     */
    function deletePromoter($shop_id, $promoter_id);
    /**
     * 修改推广员的等级
     * @param unknown $shop_id
     * @param unknown $promoter_id
     * @param unknown $level_id
     */
    function modifyPromoterLevel($shop_id, $promoter_id, $level_id);
    /**
     * 修改店铺名称
     * @param unknown $shop_id
     * @param unknown $promoter_id
     * @param unknown $promoter_shop_name
     */
    function modifyShopName($shop_id, $promoter_id, $promoter_shop_name);

}