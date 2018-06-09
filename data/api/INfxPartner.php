<?php
/**
 * INfxPartner.php
 *
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @desc : 股东接口
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace data\api;

interface INfxPartner
{
    /**
     * 获取股东列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    function getPartnerList($page_index = 1, $page_size = 0, $condition = '', $order = '');
   /**
    * 股东申请
    * @param unknown $promoter_id
    * @param unknown $parent_partner
    */
    function partnerApplay($shop_id, $uid);
    /**
     * 股东审核
     * @param unknown $partner_id
     * @param unknown $state
     */
    function partnerAudit($partner_id, $is_audit, $shop_id);
    /**
     * 股东修改
     */
    function updatePartner($partner_level,$partner_id);
    /**
     * 订单股东分红计算
     * @param unknown $order_id
     */
    function partnerCommissionCalculate($order_id);
    /**
     * 获取股东等级列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    function getPartnerLevelList($page_index = 1, $page_size = 0, $condition = '', $order = '');
    /**
     * 添加股东等级
     */
    function addPartnerLevel($level_money, $level_name, $commission_rate, $shop_id);
    /**
     * 修改股东等级
     */
    function updatePartnerLevel($level_id, $level_name, $level_money, $commission_rate, $shop_id);
    /**
     * 获取股东详情
     * @param unknown $partner_id
     */
    function getPartnerDetail($partner_id);
    /**
     * 获取股东等级详情
     */
    function getPartnerLevelDetail($level_id);
    /**
     * 股东冻结\解冻
     * @param unknown $partner_id
     * @param unknown $is_lock
     */
    function modifyPartnerLock($partner_id, $is_lock);
    /**
     * 获取股东的上级股东组返回数组（等级和ID）
     * @param unknown $partner_id
     */
    function getPartnerParents($partner_id);
    /**
     * 获取股东分红列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    function getCommissionPartnerList($page_index = 1, $page_size = 0, $condition = '', $order = '');
    /**
     * 获取全球分红列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    function getCommissionPartnerGlobalList($page_index = 1, $page_size = 0, $condition = '', $order = '');
   /**
    * 获取全部股东等级
    * @param unknown $shop_id
    */
    function getPartnerLevelAll($shop_id);
    /**
     * 全球分红设置
     * @param unknown $level_array
     */
    function updatePartnerGlobal($level_array,$shop_id);
    /**
     * 获取等级人数，分值
     * @param unknown $shop_id
     */
    function getPartnerLevelGlobalList($shop_id);
    /**
     * 获取全球分红流水
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    function getCommissionPartnerGlobalRecordsList($page_index = 1, $page_size = 0, $condition = '', $order = '');
    /**
     * 获取有效的股东信息
     * @param unknown $shop_id
     * @param unknown $uid
     */
    function getPartnerValidDetail($shop_id, $uid); 
    /**
     * 修改所有股东等级
     * @param unknown $level_array
     * @param unknown $shop_id
     */
    function updatePartnerLevelAll($level_array,$shop_id);  
    /**
     * 删除股东等级
     * @param unknown $shop_id
     * @param unknown $level_id
     */
    function deletePartnerLevel($shop_id, $level_id);
    /**
     * 修改股东用户等级
     * @param unknown $shop_id
     * @param unknown $uid
     * @param unknown $level_id
     */
    function modifyPartnerLevelNum($shop_id, $uid, $level_id);
    /**
     * 获取股东
     * @param unknown $condition
     */
    function getPartnerAll($condition);
    /**
     * 获取股东分销佣金
     * @param unknown $condition
    */
    function getPartnerCommissionCount($condition);
    /**
     * 获取 股东上级根据 uid 和 shop_id
     * @param unknown $shop_id
     * @param unknown $uid
     */
    function getPartnerParentByUidAndShopid($shop_id, $uid);
    /**
     * 修改股东上级
     * @param unknown $uid
     * @param unknown $promoter
     * @param unknown $shop_id
     */
    function modifyPartherParnet($uid , $parent_no, $shop_id);
}