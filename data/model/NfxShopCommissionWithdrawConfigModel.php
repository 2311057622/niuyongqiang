<?php
/**
 * NfxShopCommissionWithdrawConfigModel.php
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
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 佣金（提现条件）
 *
 * shop_id int(11) NOT NULL COMMENT '店铺Id',
  min_cash_money decimal(10, 2) NOT NULL DEFAULT 100.00 COMMENT '最低提现金额为元',
  div_money decimal(10, 2) NOT NULL DEFAULT 100.00 COMMENT '提现金额为的整数倍',
  PRIMARY KEY (shopId)
 */
class NfxShopCommissionWithdrawConfigModel extends BaseModel {

    protected $table = 'nfx_shop_commission_withdraw_config';

}