<?php
/**
 * INfxCommissionConfig.php
 *
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @desc : 佣金设置接口
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace data\api;

interface INfxCommissionConfig
{
    /**
     * 查询商品分销分红佣金设置
     * @param unknown $goods_id
     */
    function getGoodsCommissionRate($goods_id);
    /**
     * 商品佣金比率设置（按照商品利润分成）
     * @param unknown $condition  条件
     * @param unknown $isopen  是否启用
     * @param unknown $distribution_commission_rate  分销佣金比率
     * @param unknown $partner_commission_rate       股东分红比率
     * @param unknown $global_commission_rate        股东全球分红比率
     * @param unknown $distribution_team_commission_rate  分销商团队分红比率
     * @param unknown $partner_team_commission_rate       股东团队分红比率
     * @param unknown $regionagent_commission_rate        区域代理佣金比率
     * @param unknown $channelagent_commission_rate       渠道代理佣金比率
     */
    function updateGoodsCommissionRate($condition, $type, $distribution_commission_rate, $partner_commission_rate, $global_commission_rate, 
        $distribution_team_commission_rate, $partner_team_commission_rate, $regionagent_commission_rate, $channelagent_commission_rate, $shop_id);
    /**
     * 商品开启分销
     * @param unknown $condition
     * @param unknown $is_open
     */
    function modifyGoodsIsOpenDistribution($condition, $is_open);
    /**
     * 商品分销列表
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     */
    function getGoodsCommissionRateList($page_index = 1, $page_size = 0, $condition = '', $order = '');
    /**
     * 获取所有分销商品
     */
    function getGoodsCommiddionAll($condition);
}