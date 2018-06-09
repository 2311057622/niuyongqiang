<?php
/**
 * NfxShopConfig.php
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
use data\api\INfxShopConfig;
use data\service\BaseService;
use data\model\NfxShopConfigModel;
use data\model\NfxPromoterModel;
use data\model\UserModel as UserModel;
class NfxShopConfig extends BaseService implements INfxShopConfig{
	/* (non-PHPdoc)
     * @see \data\api\INfxShopConfig::modifyShopConfigIsDistribution()
     */
    public function modifyShopConfigIsDistributionOrPromoterIsAudit($shop_id, $is_distribution_enableopen,$is_audit,$is_start,$is_set,$distribution_use)
    {
        // TODO Auto-generated method stub
        $shop_config = new NfxShopConfigModel();
        $data = array(
            "is_distribution_enable"=>$is_distribution_enableopen,                      
            "modify_date"=>time());
        if($is_distribution_enableopen == 0){
            $data["is_regional_agent"]=0;
            $data["is_partner_enable"]=0;
            $data["is_global_enable"]=0;
            $data["is_distribution_audit"]=0;
            $data["is_distribution_start"]=0;
            $data["is_distribution_set"]=0;
            $data["distribution_use"]=0;
        }else{
            $data["is_distribution_audit"]=$is_audit;
            $data["is_distribution_start"]=$is_start;
            $data["distribution_use"]=$distribution_use;
            $data["is_distribution_set"]=$is_set;
            if($is_set == 1){
                 //设置所有会员为推广员
                $user_model = new UserModel();
                $user_list = $user_model->getQuery('1=1', '*', '');
                foreach ($user_list as $user_item){
                    $nfx_promoter_model = new NfxPromoterModel();
                    $nfx_count = $nfx_promoter_model->getInfo(['uid'=>$user_item['uid']],'*');
                    if(empty($nfx_count)){
                        $nfx_promoter = new NfxPromoter();
                        $nfx_promoter->promoterApplay($user_item['uid'], 0, empty($user_item['nick_name'])?$user_item['user_name']:$user_item['nick_name']);
                    }
                }
                 
            }
        }
        $retval = $shop_config->where(['shop_id' => $shop_id])->update($data);
        return $retval;
    }
	/* (non-PHPdoc)
     * @see \data\api\INfxShopConfig::modifyShopConfigIsRegionalAgent()
     */
    public function modifyShopConfigIsRegionalAgent($shop_id, $is_open)
    {
        // TODO Auto-generated method stub
        $shop_config = new NfxShopConfigModel();
        $data = ["is_regional_agent"=>$is_open,"modify_date"=>time()];
        $retval = $shop_config->where(['shop_id' => $shop_id])->update($data);
        return $retval;
    }

	/* (non-PHPdoc)
     * @see \data\api\INfxShopConfig::modifyShopConfigIsPartnerEnable()
     */
    public function modifyShopConfigIsPartnerEnable($shop_id, $is_open)
    {
        // TODO Auto-generated method stub
        $shop_config = new NfxShopConfigModel();
        $data = ["is_partner_enable"=>$is_open,"modify_date"=>time()];
        $retval = $shop_config->where(['shop_id' => $shop_id])->update($data);
        return $retval;
    }

	/* (non-PHPdoc)
     * @see \data\api\INfxShopConfig::modifyShopConfigIsGlobalEnable()
     */
    public function modifyShopConfigIsGlobalEnable($shop_id, $is_open)
    {
        // TODO Auto-generated method stub
        $shop_config = new NfxShopConfigModel();
        $data =array(
            "is_global_enable"=>$is_open,
            "modify_date"=>time()
                );
        $retval = $shop_config->where(['shop_id' => $shop_id])->update($data);
        return $retval;
    }

	/* (non-PHPdoc)
     * @see \data\api\INfxShopConfig::getShopConfigDetail()
     */
    public function getShopConfigDetail($shop_id)
    {
        // TODO Auto-generated method stub
        $shop_config = new NfxShopConfigModel();
        $count = $shop_config->where(['shop_id' => $shop_id])->count();
        if($count == 0)
        {
            //默认添加
            $shop_config = new NfxShopConfigModel();
            $data = array(
                "shop_id"=>$shop_id ,
                "create_date"=>time()
            );
            $shop_config->save($data);
        }
        $shop_config = new NfxShopConfigModel();
        $data = $shop_config->get(['shop_id' => $shop_id]);
        return $data;
    }

    
}