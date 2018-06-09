<?php
/**
 * NfxPartnerGlobalCalculate.php
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

interface NfxPartnerGlobalCalculate
{
    /**
    * 查询某个店铺最后一次进行全球分红
    * @param unknown $shop_id
    */
    function getPartnerGlobalLastInfo($shop_id);
    /**
     * 查询某个店铺指定之间内可分红金额
     * @param unknown $shop_id
     * @param unknown $start_time
     * @param unknown $end_time
     */
    function getPartnerGlobalMoney($shop_id, $start_time, $end_time);
    /**
     * 查询店铺某个等级的分值
     * @param unknown $shop_id
     * @param unknown $level_id
     */
    function getPartnerLevelValue($shop_id, $level_id);
    /**
     * 股东全球分红
     * @param unknown $shop_id
     * @param unknown $start_time
     * @param unknown $end_time
     * @param unknown $global_money
     */
    function getPartnerGlobalCommission($shop_id, $start_time, $end_time, $global_money);
}