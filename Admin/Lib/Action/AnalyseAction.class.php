<?php

class AnalyseAction extends CommonAction {
	function index(){
		headerutf8();
		$username=session("username");
		$order=M('order');
		$admin=M('admin');
		$bill=M('bill');
		$admininfo=$admin->where(array('username' => $username))->find();


		$dk_data=$order->where(array('operator' => $username."代扣"))->field('amount')->select();
		$bill_data=$bill->where(array('operator' => $username))->select();
		//z();
		//echo 1;
		//print_r($dk_data);
		$dk_sum=0;
		$bill_sum=0;
		$bill_received_sum=0;
		foreach ($dk_data as $key => $value) {
			$dk_sum+=(float) $value['amount'];
		}
		foreach ($bill_data as $key => $value) {
			$bill_sum+=(float) $value['price_sum'];
			$bill_received_sum+=(float) $value['price_received'];
		}
		$this->assign('admininfo',$admininfo);
		$this->assign('dk_sum',$dk_sum);
		$this->assign('bill_sum',$bill_sum);
		$this->assign('bill_received_sum',$bill_received_sum);
		$this->display();
	}

}