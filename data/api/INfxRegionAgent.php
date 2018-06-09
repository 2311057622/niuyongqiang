<?php
/**
 * INfxRegionAgent.php
 *
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @desc : 区域代理接口
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace data\api;

interface INfxRegionAgent
{
    /**
     * 获取店铺区域分红配置
     * @param unknown $shop_id
     */
    function getShopRegionAgentConfig($shop_id);
    /**
     * 配置店铺区域分红
     * @param unknown $shop_id
     * @param unknown $province_rate
     * @param unknown $city_rate
     * @param unknown $district_rate
     * @param unknown $application_require_province
     * @param unknown $application_require_city
     * @param unknown $application_require_district
     */
    function updateShopRegionAgentConfig($shop_id, $province_rate, $city_rate, $district_rate, $application_require_province, $application_require_city, $application_require_district);
    /**
     * 获取区域代理
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    function getPromoterRegionAgent($page_index = 1, $page_size = 0, $condition = '', $order = '');
    /**
     * 区域代理审核
     * @param unknown $shop_id
     * @param unknown $is_audit
     */
    function modifyPromoterRegionAgentIsAudit($shop_id, $is_audit, $region_agent_id,$province_id, $city_id, $district_id);
    /**
     * 申请区域代理
     * @param unknown $shop_id
     * @param unknown $uid
     * @param unknown $agent_type
     * @param unknown $real_name
     * @param unknown $mobile
     * @param unknown $address
     */
    function promoterRegionAgentApplay($shop_id, $uid, $agent_type, $real_name, $mobile, $address);
    /**
     * 区域代理分红佣金
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    function getCommissionRegionAgentList($page_index = 1, $page_size = 0, $condition = '', $order = '');
    /**
     * 获取代理详情
     * @param unknown $region_agent_id
     */
    function getPromoterRegionAgentValidDetail($shop_id, $uid);
    /**
     * 获取代理
     * @param unknown $condition
     */
    function getPromoterRegionAgentAll($condition);
    /**
     * 获取区域代理申请条件
     */
    function getPromoterRegionAgentApplicationRequire($shop_id, $agent_type, $uid);
    /**
     * 获取区域代理分红
     * @param unknown $condition
    */
    function getRegionAgentCommissionCount($condition);
    /**
     * 删掉区域代理资格
     * @param unknown $condition
     */
    function removePromoterRegionAgent($shop_id, $region_agent_id);
}