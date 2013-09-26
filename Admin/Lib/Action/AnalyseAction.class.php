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

}