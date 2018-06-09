<?php
/**
 * New.php
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
 * @date : 2017年9月18日
 * @version : v1.0.0.0
 */
namespace data\service;
use data\service\BaseService;
use data\api\IBargain;
use data\model\NsPromotionBargainModel;
use data\model\NsGoodsModel;
use data\model\NsPromotionBargainGoodsModel;
use data\model\NsPromotionBargainLaunchModel;
use data\model\NsPromotionBargainPartakeModel;
use data\model\NsGoodsViewModel;
use data\model\NsGoodsSkuModel;
use data\service\User;
use data\service\Album;
use think\Log;
use data\model\BaseModel;
class Bargain extends BaseService implements IBargain
{
    private $config_key = 'Bargain';
    private $order_type = 7;
    /**
     * (non-PHPdoc)
     * @see \data\api\IBargain::setConfig()
     */
    public function setConfig($is_use, $activity_time, $bargain_max_number, $cut_methods, $launch_cut_method, $propaganda, $rule){
        
        $value_array = array(
            'activity_time' => $activity_time,
            'bargain_max_number' => $bargain_max_number,
            'cut_methods' => $cut_methods,
            'launch_cut_method' => $launch_cut_method,
            'propaganda' => $propaganda,
            'rule' => $rule
        );
  
        $config_service = new Config();
        $data[0] = array(
            'is_use' => $is_use,
            'instance_id' => $this->instance_id,
            'key' => $this->config_key,
            'value' => $value_array,
            'desc' => '砍价配置信息'
        );
        
        $res = $config_service->setConfig($data);
        return $res;
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IBargain::getConfig()
     */
    public function getConfig(){
        
        $config_service = new Config();
        $config_info = $config_service->getConfig($this->instance_id, $this->config_key);
        
        $config_arr = json_decode($config_info['value'], true);
        $config_arr['is_use'] = $config_info['is_use'];
        
        return $config_arr;
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IBargain::setBargain()
     */
    public function setBargain($bargain_id, $bargain_name, $start_time, $end_time, $bargain_min_rate, $bargain_min_number, $one_min_rate, $one_max_rate, $goods_array, $remark = ''){
       
        $bargain_model = new NsPromotionBargainModel();
        $bargain_model->startTrans();
        
        try {
            
            $data = array(
                'bargain_name' => $bargain_name,
                'shop_id' => $this->instance_id,
                'shop_name' => $this->instance_name,
                'start_time' => getTimeTurnTimeStamp($start_time),
                'end_time' => getTimeTurnTimeStamp($end_time),
                'bargain_min_rate' => $bargain_min_rate,
                'bargain_min_number' => $bargain_min_number,
                'one_min_rate' => $one_min_rate,
                'one_max_rate' => $one_max_rate,
                'remark' => $remark
            );
            
            $result = '';
            if(empty($bargain_id)){
            
                $data['create_time'] = time();
                $bargain_id = $bargain_model->save($data);
                $result = $bargain_id;
            }else{
            
                $data['modify_time'] = time();
                $result = $bargain_model->save($data, ['bargain_id' => $bargain_id, 'status' => 0]);
            }
            
            $this->addBargainGoods($bargain_id, $goods_array);
            
            $bargain_model->commit();
            return $result;
            
        } catch (\Exception $e) {
            
            $bargain_model->rollback();
            
            dump($e->getMessage());
            return $e->getMessage();
        }
    
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IBargain::getBargainInfo()
     */
    public function getBargainInfo($bargain_id, $condition = []){
        
        if(!empty($bargain_id))$condition['bargain_id'] = $bargain_id;
        
        $bargain_model = new NsPromotionBargainModel();
        $bargain_info = $bargain_model->getInfo($condition, '*');

        return $bargain_info;
        
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IBargain::getBargainDetail()
     */
    public function getBargainDetail($bargain_id, $condition = []){
        
        if(!empty($bargain_id))$condition['bargain_id'] = $bargain_id;
        
        $bargain_model = new NsPromotionBargainModel();
        $bargain_info = $bargain_model->getInfo($condition, '*');
        
        $bargain_info['goods_list'] = $this->getBargainGoodsPage(1, 0, ['npbg.bargain_id'=>$bargain_id])['data'];
        return $bargain_info;
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IBargain::getBargainList()
     */
    public function getBargainList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*'){
        
        $bargain_model = new NsPromotionBargainModel();
        $bargain_list = $bargain_model->pageQuery($page_index, $page_size, $condition, $order, $field);
        return $bargain_list;
    }
    
    /**
     * 只有未开始才可删除
     * (non-PHPdoc)
     * @see \data\api\IBargain::delBargain()
     */
    public function delBargain($bargain_id){

        $bargain_model = new NsPromotionBargainModel();
        $bargain_goods = new NsPromotionBargainGoodsModel();
        $bargain_id_array = explode(',', $bargain_id);

        $bargain_model->startTrans();

        try {
            foreach ($bargain_id_array as $k => $v) {
                if(!empty($v)){
                    $bargain_model->destroy($v);
                    $bargain_goods->destroy(['bargain_id'=>$v]);
                }

            }
            $bargain_model->commit();
            return 1;

        } catch (\Exception $e) {
            $bargain_model->rollback();
            return $e->getMessage();
        }

    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IBargain::addBargainGoods()
     */
    public function addBargainGoods($bargain_id, $goods_array){
        
        //删除此活动下的所有商品
        $bargain_goods_model = new NsPromotionBargainGoodsModel();
        $bargain_goods_model->destroy(['bargain_id' => $bargain_id]);
        
        //获取活动的信息
        $bargain_info = $this->getBargainInfo($bargain_id);
        
        //获取配置信息
        $config = $this->getConfig();

        foreach($goods_array as $item){
            
            // 查询商品名称图片
            $goods_model = new NsGoodsModel();
            $goods_info = $goods_model->getInfo(['goods_id' => $item], 'goods_name,picture');
            
            $bargain_goods_model = new NsPromotionBargainGoodsModel();
            $data = array(
                'bargain_id' => $bargain_id,
                'goods_id' => $item,
                'goods_name' => $goods_info['goods_name'],
                'goods_picture' => $goods_info['picture'],
                'start_time' => $bargain_info['start_time'],
                'end_time' => $bargain_info['end_time'],
//                 'fictitious_sales' => $item['fictitious_sales'],
                'bargain_min_rate' => $bargain_info['bargain_min_rate'],
                'one_min_rate' => $bargain_info['one_min_rate'],
                'one_max_rate' => $bargain_info['one_max_rate'],
                'bargain_min_number' => $bargain_info['bargain_min_number']
            );
            
            $bargain_goods_model->save($data);
        }
        
        return 1; 
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IBargain::getBargainGoodsList()
     */
    public function getBargainGoodsList($bargain_id, $condition =[]){
        
        if(!empty($bargain_id)) $condition['bargain_id'] = $bargain_id;
        $bargain_goods_model = new NsPromotionBargainGoodsModel();
        $bargain_goods_list = $bargain_goods_model->getQuery($condition, '*', '');
        
        return $bargain_goods_list;
    }
    
    /**
     * 砍价商品分页
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
     */
    public function getBargainGoodsPage($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*'){
        
        $goods_view = new NsGoodsViewModel();
        $list = $goods_view->getBargainGoodsViewList($page_index, $page_size, $condition, $order);
        return $list;
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IBargain::getBargainGoodsInfo()
     */
    public function getBargainGoodsInfo($bargain_id, $goods_id){

        $bargain_goods_model = new NsPromotionBargainGoodsModel();
        $condition = array(
            'bargain_id' => $bargain_id,
            'goods_id' => $goods_id
        );

        $info = $bargain_goods_model->getInfo($condition, '*');

        $album_service = new Album();

        $info['pic'] = $album_service->getAlbumDetail($info['goods_picture']);
        return $info;
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IBargain::addBargainLaunch()
     */
    public function addBargainLaunch($bargain_id, $sku_id, $address_id){

        
        $bargain_launch_model = new NsPromotionBargainLaunchModel();
        $bargain_launch_model->startTrans();
        try {
            //计算发起失效时间
            $config_info = $this->getConfig();
            $activity_time = $config_info['activity_time'] * 60 * 60 * 24;
            $start_time = time();
           
            //获取地址信息
            $member_service = new Member();
            $address_info = $member_service->getMemberExpressAddressDetail($address_id);
            
            //获取选择该商品规格的信息
            $goods_sku_model = new NsGoodsSkuModel();
            $goods_sku_info = $goods_sku_model->getInfo(['sku_id' => $sku_id], 'goods_id, price');
            
            //获取参与该活动的商品信息
            $bargain_goods_info = $this->getBargainGoodsInfo($bargain_id, $goods_sku_info['goods_id']);
            
            //判断是否已经发起了该商品的砍价
            $bargain_launch_info = $this->getBargainLaunchInfo(0, ['uid' => $this->uid, 'bargain_id' => $bargain_id, 'status'=>1, 'goods_id' => $goods_sku_info['goods_id']]);
            if(!empty($bargain_launch_info)){
                
                $bargain_launch_model->commit();
                return $bargain_launch_info['launch_id'];
            }
            
            //生成该商品可砍到的最低价格
            
            $goods_money = $goods_sku_info['price'];
            $bargain_min_money = round($goods_money * $bargain_goods_info['bargain_min_rate'] / 100, 2);
            
            $data = array(
                'uid' => $this->uid,
                'bargain_id' => $bargain_id,
                'start_time' => $start_time,
                'end_time' => ($start_time + $activity_time),
                'receiver_mobile' => $address_info['mobile'],
                'receiver_province' => $address_info['province'],
                'receiver_city' => $address_info['city'],
                'receiver_district' => $address_info['district'],
                'receiver_address' => $address_info['address'],
                'receiver_zip' => $address_info['zip_code'],
                'receiver_name' => $address_info['consigner'],
                'sku_id' => $sku_id,
                'goods_money' => $goods_money,
                'bargain_min_number' => $bargain_goods_info['bargain_min_number'],
                'bargain_min_money' => $bargain_min_money,
                'goods_id' => $goods_sku_info['goods_id']
            );
            
            $launch_id = $bargain_launch_model->save($data);
            
            $one_min = ($goods_money - $bargain_min_money) * $bargain_goods_info['one_min_rate'] / 100;
            $one_max = ($goods_money - $bargain_min_money) * $bargain_goods_info['one_max_rate'] / 100;
            
            //自动砍第一刀
            $bargain_money = mt_rand($one_min * 100, $one_max * 100) / 100;
            $result = $this->addBargainPartake($launch_id, $bargain_money);
            
            //更新多少人发起砍价
            $this->setBargainLaunchSales($bargain_id, $goods_sku_info['goods_id']);
            
            $bargain_launch_model->commit();
            return $launch_id;
        } catch (\Exception $e) {
            
            $bargain_launch_model->rollback();
            Log::write('wwwwwwwwwwwwwwwwwwwwwwwwwwww'. $e->getMessage());
            return $e->getMessage();
        }
        
    }
    
    /**
     * 更新多少人发起砍价
     * @param unknown $bargain_id
     * @return boolean
     */
    public function setBargainLaunchSales($bargain_id, $goods_id){
        
        $bargain_launch_model = new NsPromotionBargainLaunchModel();
        $condition = ['bargain_id' => $bargain_id, 'goods_id' => $goods_id];
        $bargain_launch_count = $bargain_launch_model->getCount($condition);
        
        $bargain_model = new NsPromotionBargainGoodsModel();
        $result = $bargain_model->save(['sales' => $bargain_launch_count], $condition);
        return $result;
        
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IBargain::getBargainLaunchInfo()
     */
    public function getBargainLaunchInfo($launch_id, $condition = []){
        
        if(!empty($launch_id)) $condition['launch_id'] = $launch_id;
        
        $launch_model = new NsPromotionBargainLaunchModel();
        $launch_info = $launch_model->getInfo($condition, '*');
        
        return $launch_info;
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IBargain::addBargainPartake()
     */
    public function addBargainPartake($launch_id, $bargain_money = 0){
        
       
    	//根据砍价记录
 
        $partake_model = new NsPromotionBargainPartakeModel();
        $partake_model->startTrans();
  
        try {
            $launch_info = $this->getBargainLaunchInfo($launch_id);
            $surplus_money = $launch_info['goods_money'] - $launch_info['bargain_money'] - $launch_info['bargain_min_money'];
            $surplus_money = $surplus_money < 0 ? 0 : $surplus_money;
            
            if(empty($this->uid)){
                
                $partake_model->rollback();
                return NO_LOGIN;
            }
            
            //砍价活动已结束的
            if($launch_info['status'] != 1){
                
                $partake_model->rollback();
                return BARGAIN_LAUNCH_ALREADY_CLOSE;
            }
            
            $config = $this->getConfig();
            $partake_count = $partake_model->getCount(['launch_id' => $launch_id, 'uid' => $this->uid]);
            if($partake_count >= $config['bargain_max_number']){
                $partake_model->rollback();
                return BARGAIN_LAUNCH_MAX_PARTAKE;
            }
           
            //如果没有传入砍掉的金额就计算砍掉的金额
            if(empty($bargain_money) ){

                $first_partake = $partake_model->getFirstData(['launch_id'=> $launch_id], 'create_time asc');
                $min_price = 0.01;
                $max_price = round(($launch_info['goods_money'] - $first_partake['bargain_money'] - $launch_info['bargain_min_money']) / ($launch_info['bargain_min_number'] - 1), 2);

                $bargain_money = mt_rand($min_price * 100, $max_price * 100) /100;
                $bargain_money = sprintf("%.2f", $bargain_money);   //砍掉的金额
            }
            $bargain_money = ($surplus_money < $bargain_money) ? $surplus_money : $bargain_money;
         
            //随机获取刀法说明
            $config = $this->getConfig();

            if($this->uid == $launch_info['uid']){
                $remark = $config['launch_cut_method'];
            }else{
                $cut_method_array = explode('，', $config['cut_methods']);
                $cut_method_index = mt_rand(0, count($cut_method_array) -1);
                $remark = $cut_method_array[$cut_method_index];
                $remark = trim($remark);
            }
  
            $data = array(
                'launch_id' => $launch_id,
                'uid' => $this->uid,
                'bargain_money' => $bargain_money,
                'create_time' => time(),
                'remark' => $remark
            );
            $result = $partake_model->save($data);
            $this->setBargainPartakeRecord($launch_id);
            $partake_model->commit();
            return $result;
        } catch (\Exception $e) {
            
            dump($e->getMessage());
            $partake_model->rollback();
            Log::write('wwwwwwwwwwwwwwwwwww'. $e->getMessage());
            return $e->getMessage();
        }
        
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IBargain::setBargainPartakeRecord()
     */
    public function setBargainPartakeRecord($launch_id){
        
        $partake_model = new NsPromotionBargainPartakeModel();
        $sum_bargain_money = $partake_model->getSum(['launch_id' => $launch_id], 'bargain_money');
        $count_bargain_number = $partake_model->getCount(['launch_id' => $launch_id]);
        
        $bargain_launch_model = new NsPromotionBargainLaunchModel();
        $data = array(
            'partake_number' => $count_bargain_number,
            'bargain_money' => $sum_bargain_money
        );
        
        $condition = ['launch_id' => $launch_id]; 
        
        $bargain_launch_info = $bargain_launch_model->getInfo($condition, '*');
        
        $bargain_money = $bargain_launch_info['goods_money'] - $bargain_launch_info['bargain_min_money'];
        $bargain_money = round($bargain_money, 2);
        $sum_bargain_money = round($sum_bargain_money, 2);
        
        if($sum_bargain_money >= $bargain_money){
            $data['status'] = 2;
            //地址
            $address = new Address();
            $address_info = $address -> getAddress($bargain_launch_info['receiver_province'], $bargain_launch_info['receiver_city'], $bargain_launch_info['receiver_district']);
            $bargain_launch_info['receiver_address'] = $address_info.'&nbsp;'.$bargain_launch_info['receiver_address'];
            //创建订单
            $order_service = new Order();
            $out_trade_no = $order_service->getOrderTradeNo();
            $order_id = $order_service->orderCreateBragain($out_trade_no, $bargain_launch_info['sku_id'].":1",
                $bargain_launch_info['receiver_mobile'], $bargain_launch_info['receiver_province'], $bargain_launch_info['receiver_city'],
                $bargain_launch_info['receiver_district'],$bargain_launch_info['receiver_address'], $bargain_launch_info['receiver_zip'], 
                $bargain_launch_info['receiver_name'], $sum_bargain_money, $bargain_launch_info['uid']);
            
            $data['order_id'] = $order_id;
        } 
        $result = $bargain_launch_model->save($data, $condition);
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IBargain::getBargainLaunchList()
     */
    public function getBargainLaunchList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*'){
        
        $bargain_launch_model = new NsPromotionBargainLaunchModel();
        $list = $bargain_launch_model->pageQuery($page_index, $page_size, $condition, $order, $field);
        foreach ($list['data'] as  $k=>$v){
        	$album_service = new Album();
        	
        	$bargain_goods_info = $this->getBragainBySkuGoodsInfo($v['bargain_id'], $v['sku_id']);
			
        	$v['goods_info'] = $bargain_goods_info;
        }
        $user = new User();
        $list['user_info'] = $user->getUserInfoByUid($this->uid);
        return $list;
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IBargain::getBargainPartakeList()
     */
    public function getBargainPartakeList($launch_id){
        
        $bargain_partake_model = new NsPromotionBargainPartakeModel();
        $list = $bargain_partake_model->getQuery(['launch_id' => $launch_id], '*', 'create_time desc');
        foreach ($list as  $k=>$v){
        	$user = new User();
        	$list[$k]['user_info'] = $user->getUserInfoByUid($v['uid']);
        }
        return $list;
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IBargain::setBargainLaunchStatus()
     */
    public function setBargainLaunchStatus($launch_id, $status){
        
        $bargain_launch_model = new NsPromotionBargainLaunchModel();
        $result = $bargain_launch_model->save(['status' => $status], ['launch_id' => $launch_id]);
        return $result;
    }
    
    public function getBragainBySkuGoodsInfo($bargain_id, $sku_id)
    {
    	$goods_sku_model = new NsGoodsSkuModel();
    	$album_service = new Album();
    	$goods_sku_info = $goods_sku_model->getInfo(['sku_id' => $sku_id], 'goods_id, price');
    	
    	//获取参与该活动的商品信息
    	$bargain_goods_info = $this->getBargainGoodsInfo($bargain_id, $goods_sku_info['goods_id']);
    	
    	$bargain_goods_info['pic'] = $album_service->getAlbumDetail($bargain_goods_info['goods_picture']);
    	
    	$bargain_goods_info['price'] = $goods_sku_info['price'];
    	return $bargain_goods_info;
    	
    }
    
    /**
     * 获取当前会员绑好友砍价是否已达最大次数
     */
    public function getBragainLaunchIsPartakeMax($uid, $launch_id){
        
        $config_info = $this->getConfig();
        $bargain_partake_model = new NsPromotionBargainPartakeModel();
        $partake_count = $bargain_partake_model->getCount(['launch_id' => $launch_id, 'uid' => $uid]);
        if($partake_count >= $config_info['bargain_max_number']){
            return 1;
        }else{
            return 0;
        }
    }

    public function closeBargain($bargain_id, $condition = []){

        if(!empty($bargain_id))$condition['bargain_id'] = $bargain_id;

        $bargain_model = new NsPromotionBargainModel();
        $bargain_goods = new NsPromotionBargainGoodsModel();
        $bargain_model -> startTrans();
        try {
            $res = $bargain_model->save(['status'=>3],$condition);
            $goods_res = $bargain_goods->save(['status'=>3],$condition);
            if($goods_res > 0){
                $bargain_model->commit();
            }else{
                $bargain_model->rollback();
            }
            return $res;
        }catch (\Exception $e) {

            $bargain_model->rollback();
            return $e->getMessage();
        }
    }
}