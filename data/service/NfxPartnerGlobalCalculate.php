<?php
/**
 * NfxPartner.php
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
use data\service\Partner\NfxPartnerGlobal;
use data\model\NfxPartnerModel;
use data\model\NfxCommissionPartnerGlobalRecordsModel;
use data\api\INfxPartnerGlobalCalculate;
use data\model\NfxCommissionPartnerGlobalModel;
/**
 * 股东全球分红服务层
 */

class NfxPartnerGlobalCalculate extends BaseService  implements INfxPartnerGlobalCalculate
{
	
    /**
     * 查询某个店铺最后一次进行全球分红
     * @param unknown $shop_id
     */
    public function getPartnerGlobalLastInfo($shop_id){
        $partner_global_model=new NfxPartnerGlobal($shop_id);
        $global_last_info=$partner_global_model->getPartnerGlobalLastInfo();
        return $global_last_info;
    }
    /**
     * 查询某个店铺指定之间内可分红金额
     * @param unknown $shop_id
     * @param unknown $start_time
     * @param unknown $end_time
     */
    public function getPartnerGlobalMoney($shop_id, $start_time, $end_time){
        $partner_global_model=new NfxPartnerGlobal($shop_id);
        $global_money=$partner_global_model->getPartnerGlobalMoney($start_time, $end_time);
        return $global_money;
    }
    /**
     * 查询店铺某个等级的分值
     * @param unknown $shop_id
     * @param unknown $level_id
     */
    public function getPartnerLevelValue($shop_id, $level_id){
        $partner_global_model=new NfxPartnerGlobal($shop_id);
        $level_value=$partner_global_model->getPartnerLevelValue($level_id);
        return $level_value;
    }
    /**
     * 股东全球分红
     * @param unknown $shop_id
     * @param unknown $start_time
     * @param unknown $end_time
     * @param unknown $global_money
     */
    public function getPartnerGlobalCommission($shop_id, $start_time, $end_time, $global_money){
        $partner_global_model=new NfxPartnerGlobal($shop_id);
        //该段时间可分的金额
        $partner_can_money=$partner_global_model->getPartnerGlobalMoney($start_time, $end_time);
        if($global_money>$partner_can_money){
            return -1; 
        }else{
            $partner_model=new NfxPartnerModel();
            $condition["shop_id"]=$shop_id;
            $partner_list=$partner_model->getQuery($condition, "*", "");
            $global_total_value=0;
            if(!empty($partner_list) && count($partner_list)>0){
                foreach($partner_list as $k=>$partner_obj){
                    $partner_value=$partner_global_model->getPartnerValue($partner_obj["partner_id"]);
                    $partner_list[$k]["global_value"]=$partner_value;
                    $global_total_value=$global_total_value+$partner_value;
                }
            }
            $partner_global_records_model=new NfxCommissionPartnerGlobalRecordsModel();
            $user_model=new NfxUser();
            $record_id=0;
            $partner_global_records_model->startTrans();
            try {
                $record_id=$partner_global_model->addCommissionPartnerGlobalRecords($shop_id, $start_time, $end_time, $global_money);
                if(!empty($partner_list) && count($partner_list)>0){
                    foreach($partner_list as $k=>$partner_obj){
                        $serial_no=getSerialNo();
                        $partner_rate=$partner_obj["global_value"]/$global_total_value;
                        $fenhong_money=$global_money*$partner_rate;
                        $insert_id=$partner_global_model->addCommissionPartnerGlobal($serial_no, $shop_id, $partner_obj["partner_id"], 
                            $partner_obj["uid"], $start_time, $end_time, $global_money, $global_total_value, 
                            $partner_obj["global_value"], $partner_rate, $fenhong_money,$record_id);
                        $user_model->addNfxUserAccountRecords($partner_obj["uid"], $shop_id, $fenhong_money, 5, 
                            $insert_id, 1, 1, "商家发放股东股东全球分红，佣金已结算。", $serial_no);
                    }
                }
                $partner_global_records_model->commit();
                return $record_id;
            } catch (\Exception $e) {
                $partner_global_records_model->rollback();
                print($e->getMessage());
                return $e->getMessage();
            }
            
        }
    }
	/* (non-PHPdoc)
     * @see \data\api\INfxPartnerGlobalCalculate::getPartnerGlobalCommissionCount()
     */
    public function getPartnerGlobalCommissionCount($condition)
    {
        // TODO Auto-generated method stub
        $partner_global_commission = new NfxCommissionPartnerGlobalRecordsModel();
        $result = $partner_global_commission->getQuery($condition, "sum(fenhong_money) as sum",'');
        return $result[0]["sum"];
    }

}
