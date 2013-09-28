<?php

class AnalyseAction extends CommonAction {
	function index(){
		headerutf8();
		//检测是否可以查看其他人的业绩报表
		if($see_other=$this->checkPermission('_see_other_report') && I('username')){
			$username=I('username');
		}else{
			$username=session("username");
		}
		$this->assign('see_other',$see_other);

		//查询业务员信息
		$admin=M('admin');
		$admininfo=$admin->where(array('username' => $username))->find();//当前业务员信息
		$this->assign('admininfo',$admininfo);
		//输出业务员列表
		if(!S('adminlist')){
			$adminlist=$admin->where(array('isdelete'=>0))->field('username')->select();
			S('adminlist',$adminlist,3600);
		}else{
			$adminlist=S('adminlist');
		}
		$this->assign('adminlist',$adminlist);

		//检测时间设定信息
		if(isset($_GET['date_from'])){
			$date_from=$_GET['date_from'];
			$date_to=$_GET['date_to'];
		}else{
			$date_from=date('Y-m-01');
			$date_to=date('Y-m-d');
		}
		$this->assign('date_from',$date_from);
		$this->assign('date_to',$date_to);

		//商品代扣统计
		$order=M('order');
		$dk_data=$order->where(array(
					'operator' => $username."代扣",
					'delivertime' => array('between',$date_from.",".$date_to),
					))
				->field('beizhu,delivername,delivertime,info,amount,order_id')
				->order('delivertime')->select();
		$dk_sum=0;
		foreach ($dk_data as $key => $value) {
			$dk_sum+=(float) $value['amount'];
			$dk_info=json_decode($dk_data[$key]['info'],true);
			$temp="";
			foreach($dk_info as $v){
				$temp.=$v['w_name']."：".$v['pnum']."份 | ";
			}
			$dk_data[$key]['info']=$temp;
		}
		$this->assign('dk_sum',$dk_sum);
		$this->assign('dk_data',$dk_data);

		//账单统计
		$bill=M('bill');
		$bill_data=$bill->where(array(
					'operater' => $username,
					'check_state' => 1,
					'abandon' => 0,
					'order_date' => array('between',$date_from.",".$date_to),
					))->field('orderid,customer_name,order_date,contacter,price_sum,price_received,remark,desn')->order('order_date')->select();
		// z();
		$bill_sum=0;
		$bill_received_sum=0;

		foreach ($bill_data as $key => $value) {
			$bill_sum+=(float) $value['price_sum'];
			$bill_received_sum+=(float) $value['price_received'];
		}
		$this->assign('bill_data',$bill_data);
		// dump($bill_data);die;
		$this->assign('bill_sum',$bill_sum);
		$this->assign('bill_received_sum',$bill_received_sum);


		$this->display();
	}

	/**
     * 生成指定月份统计数据
     * @param  [int] $datatype  业务类型  1-充值代扣,2-账单,3-商家订单
     * @param  [int] $yearmonth 年月信息,格式为201201
     * @return [int]            若成功返回1,失败返回0
     */
    function build_analyze_data($datatype,$yearmonth=0){
    	
    	$data['type']=$datatype;
    	$data['date']=$yearmonth?intval($yearmonth):intval(date('Ym'));
    	$thismonth=($data['date']==date('Ym'));
    	//寻找是否已经存在统计数据,若不存在,则开始建立
    	if($this->check_analyze_data($data['type'],$data['date'])){
    		//如果找到,那么检测是否是当前月份
    		//如果是,则重建月份数据,如果不是,终止流程
    		if($data['date']==date('Ym')){
    			$rebuild=1;
    		}else{
	    		return 0;
    		}
    	}
    	$datefrom=mktime(0,0,0,$data['date']%100,1,$data['date']/100);
    	$dateto=mktime(0,0,0,$data['date']%100+1,1,$data['date']/100)-1;

    	if($datatype==1){
    		$amtinfo=M('amountinfo');
    		$data['total']=$amtinfo->where(array('createtime'=>array('ELT',$dateto)))->count();
    		$data['month']=$amtinfo->where(array('createtime'=>array('between',$datefrom.",".$dateto)))->count();
    	}else if($datatype==2){
    		$bill=M('bill');
    		$data['total']=$bill->where(
    			array('order_date'=>
    					array('ELT',date('Y-m-d',$dateto))
    			))->count();
    		$data['month']=$bill->where(array('order_date'=>
    			array('between',date('Y-m-d',$datefrom).",".date('Y-m-d',$dateto))
    			))->count();
    	}else if($datatype==3){
    		$order=M('order');
    		$data['total']=$order->where(array('createtime'=>array('ELT',$dateto)))->count();
    		$data['month']=$order->where(array('createtime'=>array('between',$datefrom.",".$dateto)))->count();
    	}else{
    		return 0;
    	}
    	$analyze=M('analyze');
    	if(isset($rebuild)){
    		return $analyze->where(array('date'=>$data['date']))->save($data);
    	}else{
	    	return $analyze->add($data);
    	}
    }
    /**
     * 检测指定月份是否存在统计数据
     * @param  [int] $datatype  业务类型  1-充值代扣,2-账单,3-商家订单
     * @param  [int] $yearmonth 年月信息,格式为201201
     * @return [int]            若存在返回1,否则返回0
     */
    function check_analyze_data($datatype,$yearmonth=0){
    	if(!$yearmonth) $yearmonth=date('Ym');
    	$analyze=M('analyze');
    	$result=$analyze->where(array('type'=>$datatype,'date'=>intval($yearmonth)))->find();
    	return $result?1:0;
    }
    function rebuild(){
    	for($i=2012;$i<=date('Y');$i++){
    		for($j=1;$j<=12;$j++){
    			for($time=1;$time<=3;$time++){
    				if($j<10){
    					$date=$i . '0' . $j;
    				}else{
    					$date=$i . $j;
    				}
    				if($date>date('Ym')) break;
    				if($this->build_analyze_data($time,$date)){
    					echo "重建".$date."数据成功<br>";
    				}else{
    					echo "重建".$date."数据失败<br>";
    				}
    			}
    		}
    	}
    }

}