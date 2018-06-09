<?php
/**
 * NfxUserAccountViewModel.php
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
 * 会员账户账单(佣金)
 *
 */
class NfxUserAccountViewModel extends BaseModel {

    protected $table = 'nfx_user_account';
    
    public function getViewQuery($condition, $order){
        $viewObj = $this->alias('nua')
        ->join('ns_shop ns','ns.shop_id = nua.shop_id','left')
        ->field('nua.id,nua.shop_id,nua.uid,nua.commission,nua.commission_cash,nua.commission_withdraw,nua.commission_promoter,nua.commission_partner,nua.commission_partner_global,nua.commission_region_agent,nua.commission_partner_team,nua.commission_promoter_team,nua.commission_channelagent,nua.create_time,ns.shop_name');
        $list = $this->viewPageQuery($viewObj, 1, 0, $condition, $order);
        return $list;
    }
   
}