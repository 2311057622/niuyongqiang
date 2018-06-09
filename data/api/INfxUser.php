<?php
/**
 * INfxUser.php
 *
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @desc : 分销会员（分销）
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace data\api;

interface INfxUser
{
    /**
     * 会员关注店铺成为店铺会员
     * @param unknown $uid
     * @param unknown $shop_id
     * @param unknown $source_uid
     */
    function userAssociateShop($uid, $shop_id, $session_id);
    /**
     * 获取会员推广员
     * @param unknown $uid
     */
    function getUserPromoter($uid,$shop_id);
    /**
     * 修改会员的上级推广员
     * @param unknown $uid
     * @param unknown $shop_id
     * @param unknown $promoter_id
     */
    function modifyUserPromoter($uid, $shop_id, $promoter_id);
    /**
     * 获取会员在某个店铺角色
     * @param unknown $uid
     * @param unknown $shop_id
     */
    function getUserRole($uid, $shop_id);
    /**
     * 添加会员账户佣金记录
     * @param unknown $uid
     * @param unknown $shop_id
     * @param unknown $money
     * @param unknown $account_type
     * @param unknown $type_alis_id
     * @param unknown $is_display
     * @param unknown $is_calculate
     * @param unknown $text
     */
    function addNfxUserAccountRecords($uid, $shop_id, $money, $account_type, $type_alis_id, $is_display, $is_calculate, $text, $batchcode);
    /**
     * 查询会员佣金统计情况
     * @param unknown $uid
     * @param unknown $shop_id
     */
    function getNfxUserAccount($uid, $shop_id);
    /**
     * 获取会员佣金账户列表
     * @param unknown $uid
     */
    function getUserAccountList($uid);
    /**
     * 获取会员佣金明细表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    function getNfxUserAccountRecordsList($page_index = 1, $page_size = 0, $condition = '', $order = '');
   
    /**
     * 会员提现账号列表
     */
    function getUserBankAccount($is_default=0);
    
    /**
     * 添加会员提现账号
    */
    function addUserBankAccount($uid,$bank_type,$branch_bank_name,$realname,$account_number,$mobile);
    
    /**
     * 修改会员提现账号
    */
    function updateUserBankAccount($account_id,$branch_bank_name,$realname,$account_number,$mobile);
    
    /**
     * 删除会员提现账号
     * @param unknown $id
    */
    function delUserBankAccount($account_id);
    
    /**
     * 设定会员默认账户
     * @param unknown $uid
     * @param unknown $account_id
     */
    function setUserBankAccountDefault($uid, $account_id);
    
    /**
     * 获取提现账号详情信息
     * @param unknown $id
    */
    function getUserBankAccountDetail($id);
    
    /**
     * 获取提现记录
     * @param unknown $uid
     * @param unknown $shop_id
     */
    function getUserCommissionWithdraw($page_index = 1, $page_size = 0, $condition = '', $order = '');
    
    /**
     * 申请提现
     * @param unknown $shop_id
     * @param unknown $withdraw_no
     * @param unknown $distributor_uid
     * @param unknown $bank_account_id
     * @param unknown $cash
     */
    public function addNfxCommissionWithdraw($shop_id, $withdraw_no, $uid, $bank_account_id, $cash);
    /**
     * 发放这个订单的三级分销
     * @param unknown $order_id
     */
    function updateCommissionDistributionIssue($order_id);
    /**
     * 发放订单的全球分红
     * @param unknown $order_id
     */
    function updateCommissionPartnerIssue($order_id);
    /**
     * 发放订单的区域代理
     * @param unknown $order_id
     */
    function updateCommissionRegionAgentIssue($order_id);
    /**
     * 更新 推广员的等级
     * @param unknown $uid
     */
    function updatePromoterLevel($uid, $shop_id);
    /**
     * 更新股东的等级
     * @param unknown $uid
     */
    function updatePartnerLevel($uid, $shop_id);
    
    /**
     * 获取微信粉丝详情
     * @param unknown $uid
     * @param unknown $shop_id
     */
    function getWeixinFansDetail($uid, $shop_id);
    /**
     * 获取用户店铺会员信息
     * @param unknown $uid
     * @param unknown $shop_id
     */
    function getShopMemberAssociation($uid, $shop_id);
    /**
     * 店铺会员佣金列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    function getShopUserAccountList($page_index = 1, $page_size = 0, $condition = '', $order = '');
    /**
     * 用户提现审核
     * @param unknown $shop_id
     * @param unknown $id
     * @param unknown $status
     */
    function UserCommissionWithdrawAudit($shop_id, $id, $status, $transfer_type, $transfer_name, $transfer_money, $transfer_remark, $transfer_no, $transfer_account_no, $type_id);
    
    /**
     * 拒绝提现申请
     * @param unknown $shop_id
     * @param unknown $id
     * @param unknown $status
     * @param unknown $remark
     */
    function userCommissionWithdrawRefuse($shop_id, $id, $status, $remark);
    
    /**
     * 获取提现详情
     * @param unknown $id
     */
    function getMemberWithdrawalsDetails($id);
    /**
     * 佣金类型详情
     * @param unknown $account_type_id
     */
    function getUserAccountType($account_type_id);
    
    /**
     * 佣金类型列表
     * @param string $condition
     */
    function getUserAccountTypeList($condition = '1=1');
    
    /**
     * 获取店铺会员列表
     * @param unknown $shop_id
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    function getShopMemberList($shop_id, $page_index=1, $page_size=0, $condition = '', $order = '');
    /**
     * 获取店铺佣金记录
     * @param unknown $condition
     */
    function getShopUserAccountRecord($condition);
    function getShopCommissionCount($shop_id, $start_date, $end_date);
    /**
     * 用户店铺提现统计
     */
    function getShopWithdrawCount($condition);
    /**
     * 待结算佣金  可发放佣金
     */
    function getShopUserCommissionCount($shop_id);    
    /**
     * 判断当前会员是否是店铺会员
     * @param unknown $shop_id
     * @param unknown $uid
     */
    function isShopMember($shop_id,$uid);
    /**
     * 获取当前会员的上级uid
     * @param unknown $shop_id
     * @param unknown $uid
     */
    function getUserParent($shop_id, $uid);
}