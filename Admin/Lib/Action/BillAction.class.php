<?php

class BillAction extends CommonAction {
	function index(){
		$this->redirect('billlist');
	}
	function billcreate(){
		$admin=M('admin');
		$admin_list=$admin->field('username')->select();
		$this->assign('admin_list',$admin_list);
		$this->display();
	}
	function billlist() {
		$bill=M('bill');
		$query=getQuery();
		$query=$query['string'];
		$query.=' and abandon=0';
		$username=$_SESSION['username'];
	//	if(session('admin_type')<"1"){
	//		$query.=" and operater=`$username`";
	//	}
		$count=$bill->where($query)->count();
		import("ORG.Util.Page");
		$page=new Page($count,50);
		$data=$bill->where($query)->limit($page->firstRow . ',' . $page->listRows)->order("id desc ")->select();
		$price_sum_received=0;
		$price_sum_notreceived=0;
		foreach ($data as $key => $value) {
			$price_sum_received+=$value['price_sum'];
			$price_sum_notreceived+=$value['price_received'];
			$export[$key]['price_sum']=$value['price_sum'];
			$export[$key]['price_received']=$value['price_received'];
			$export[$key]['orderid']=$value['orderid'];
		}
		$show = $page->show();
	    $this->assign('data',$data);
	    $this->assign('price_sum_received',$price_sum_received);
	    $this->assign('price_sum_notreceived',$price_sum_notreceived);
	    $this->assign('export',json_encode($export));
	    $this->assign('show',$show);
        $this->display();

	}
	function billdetail(){
		$bill=M('bill');
		$bill_detail=M('bill_detail');
		$bill_number=$this->_get('id');
		if(isset($_GET['price_received'])){
			if(session('admin_type')>="2"){
				$data['price_received']=(float) $this->_get('price_received');
				$data['remark_sk']=$this->_get('remark_sk');
				$data['receive_date']=date("Y-m-d");
				$bill->where(array("id" => $bill_number))->save($data);
				//z();
			}else{
				$this->error('您的权限不足以更改实收金额');
			}
		}
		if(isset($_GET['verify'])){
			if(session('admin_type')>="1"){
				if($_GET['verify']=="1") $data['check_state']=1;
				if($_GET['verify']=="0") $data['check_state']=2;
				$data['checker']=session('username');
				if($data['check_state'])
					$bill->where(array("id" => $bill_number))->save($data);
				//z();
			}
		}
		$bill_data=$bill->where(array("id" => $bill_number))->find();
		if(!$bill_data) $this->error("该账单不存在");
		else{
			$detail_data=$bill_detail->where(array("bill_id" => $bill_number))->select();
		}
		if(session('admin_type')<1){
			if($bill_data['operater']!=session('username')) $this->error("您的权限不够");
		}
		
		$this->assign('bill_data',$bill_data);
		$this->assign('detail_data',$detail_data);
		$this->display();
	}
	function billremark(){
		$bill=M('bill');
		$bill_number=$this->_post('id');
		if(session('admin_type')>="1"){
			$data['remark']=$this->_post('verify-remark');
			$bill->where(array("id" => $bill_number))->save($data);
			if($bill) $this->success("审核备注提交成功","billdetail?id=".$bill_number);
		}
	}
	function billedit(){
		$bill=M('bill');
		$bill_detail=M('bill_detail');
		$bill_number=$this->_get('id');
		$bill_data=$bill->where(array("id" => $bill_number))->find();
		if($bill_data['check_state']>0 && session('admin_type')<2)
				$this->error('您不能修改已经通过审核的账单');
		if(!$bill_data) $this->error("该账单不存在");
		else{
			$detail_data=$bill_detail->where(array("bill_id" => $bill_number))->order("id")->select();
		}
		if(session('admin_type')<1){
			if($bill_data['operater']!=session('username')) $this->error("您的权限不够");
		}
		$this->assign('bill_data',$bill_data);
		$this->assign('detail_data',$detail_data);
		$this->display();
	}
	function billedit_submit(){
		$bill_number=$this->_post('id');
		if(empty($_POST['customerid'])) $this->error("必须填写客户名");
		$data['customer_name']=$this->_post('customerid');
		$data['customer_phone']=$this->_post('contacter_number');
		$data['contacter']=$this->_post('contacter');
		$data['order_date']=$this->_post('orderdate');
		$data['dilivery_date']=$this->_post('checkdate');
		$data['dilivery_location']=$this->_post('checkplace');
		$data['settlement']=$this->_post('checkmethod');
		if(session('admin_type')>=2 && !empty($_POST['operater']))
			$data['operater']=$this->_post('operater');
		else
			$data['operater']=session('username');
		if(isset($_POST['invoice'])){
			$data['invoice']=1;
		}else{
			$data['invoice']=0;
		}
		$data['check_state']=0;
		$ware=M('ware');
		$have_product=false;
		for($i=1;$i<=5;$i++){
			if(!empty($_POST['product'.$i])){
				$ware_item=$ware->where("w_name= '".$this->_post('product'.$i)."'")->field("w_name,w_price")->find();
				if(!is_numeric($_POST['number'.$i])) $this->error("商品：“".$this->_post('product'.$i)."”的数量：".$_POST['number'.$i]."不是一个有效的值");
				if(!is_numeric($_POST['discount'.$i])) $this->error("商品：“".$this->_post('product'.$i)."”的折扣：".$_POST['discount'.$i]."不是一个有效的值");;
				if(empty($ware_item)) $this->error("找不到您商品：“".$this->_post('product'.$i)."”");
				$have_product=true;
				$bill_detail_data[$i]=$ware_item;
				$price_sum[$i]=$_POST['number'.$i]*$_POST['discount'.$i]*$ware_item['w_price'];
			}
		}
		if(!$have_product) $this->error('请至少选择一个产品');
		$data['price_sum']=0;
		foreach($price_sum as $val){
			$data['price_sum']+=$val;
		}
		$bill=M('bill');
		$state=$bill->where(array("id" => $bill_number))->save($data);
		$bill_detail=M('bill_detail');
		$bill_detail->where(array("bill_id" => $bill_number))->delete();
		foreach($bill_detail_data as $key => $value){
			$bill_detail_items['bill_id']=$bill_number;
			$bill_detail_items['product_name']=$value['w_name'];
			$bill_detail_items['remark']=$this->_post('remark'.$key);
			$bill_detail_items['number']=$this->_post('number'.$key);
			$bill_detail_items['unit_price']=$value['w_price'];
			$bill_detail_items['discount']=$this->_post('discount'.$key);
			$bill_detail_items['price_sum']=$price_sum[$key];
			if(!$bill_detail->add($bill_detail_items))
				$this->error("数据条目：".$value['w_name']."写入出错，请联系管理员解决此问题。");
		}
		$this->success('账单已成功提交！','Billlist');

	}
	function billwasted(){
		$bill=M('bill');
		if(session('admin_type')>="1"){
			if(!empty($_GET['operater'])) $query=array("abandon" => "1","operater" => $this->_get('operater'));
			else $query=array("abandon" => "1");
		}else{
			$query=array("abandon" => "1","operater" => session("username"));
		}

		$count=$bill->where($query)->count();
		import("ORG.Util.Page");
		$page=new Page($count,50);
		$data=$bill->where($query)->limit($page->firstRow . ',' . $page->listRows)->order("id desc ")->select();
		$show = $page->show();
	    $this->assign('data',$data);
	    $this->assign('show',$show);
        $this->display();
	}
	function billrecover(){
		$bill_number=$this->_get('id');
		$bill=M('bill');
		if(session('admin_type')<"2")
			$this->error("只有系统管理员能恢复账单");
		$data['abandon']=0;
		if($bill->where(array('id' => $bill_number))->save($data)) $this->redirect('billwasted');
		else $this->error("恢复失败");
	}
	function billabandon(){
		$bill_number=$this->_get('id');
		$bill=M('bill');
		$bill_data=$bill->where(array('id' => $bill_number))->find();
		if($bill_data['check_state']>0){
			//已经被审核过，若管理员不是系统管理员则报错
			if(session('admin_type')<"2")	$this->error('您不能删除已经被审核过的订单');
		}else{
			//没有被审核过，则若不是自己本人操作时报错（管理员权限时忽略）
			if(session('admin_type')=="0"){
				if($bill_data['operater']!=session('username')) $this->error('您只能删除自己的订单');
			}
		}
		$data['abandon']=1;
		if($bill->where(array('id' => $bill_number))->save($data)) $this->redirect('billlist');
		else $this->error("删除失败");
	}
	function product_ajax(){
        $ware = d("ware");
        $row = $ware->where("w_name like '%" . $item . "%'")->field("w_name")->select();
        
        foreach($row as $key => $value){
        	$data[$key]=$value['w_name'];
        }
		$this->ajaxReturn($data,'JSON');
	}
	public function product_info_ajax(){
		$ware = M("ware");

        $data = $ware->where("w_name like '%" . $this->_get('name') . "%'")->field("w_price")->find();
		echo $data[w_price] ;

	}
	public function create_order(){
		if(!session('?username')) $this->error('您尚未登录！');
		import("ORG.Util.String");
		header("Content-type: text/html; charset=utf-8");


		$data['orderid']="B".date("YmdG").strtoupper(string::buildFormatRand("**##"));
		if(empty($_POST['customerid'])) $this->error("必须填写客户名");
		$data['customer_name']=$this->_post('customerid');
		$data['customer_phone']=$this->_post('contacter_number');
		$data['contacter']=$this->_post('contacter');
		$data['order_date']=$this->_post('orderdate');
		$data['dilivery_date']=$this->_post('checkdate');
		$data['dilivery_location']=$this->_post('checkplace');
		$data['settlement']=$this->_post('checkmethod');
		if(session('admin_type')>=2 && !empty($_POST['operater']))
			$data['operater']=$this->_post('operater');
		else
			$data['operater']=session('username');
		if(isset($_POST['invoice'])){
			$data['invoice']=1;
		}else{
			$data['invoice']=0;
		}
		$data['check_state']=0;
		$ware=M('ware');
		$have_product=false;
		for($i=1;$i<=5;$i++){
			if(!empty($_POST['product'.$i])){
				$ware_item=$ware->where("w_name= '".$this->_post('product'.$i)."'")->field("w_name,w_price")->find();
				if(!is_numeric($_POST['number'.$i])) $this->error("商品：“".$this->_post('product'.$i)."”的数量：".$_POST['number'.$i]."不是一个有效的值");
				if(!is_numeric($_POST['discount'.$i])) $this->error("商品：“".$this->_post('product'.$i)."”的折扣：".$_POST['discount'.$i]."不是一个有效的值");;
				if(empty($ware_item)) $this->error("找不到您商品：“".$this->_post('product'.$i)."”");
				$have_product=true;
				$bill_detail_data[$i]=$ware_item;
				$price_sum[$i]=$_POST['number'.$i]*$_POST['discount'.$i]*$ware_item['w_price'];
			}
		}
		if(!$have_product) $this->error('请至少选择一个产品');
		$data['price_sum']=0;
		foreach($price_sum as $val){
			$data['price_sum']+=$val;
		}
		$bill=M('bill');
		$bill_number=$bill->add($data);
		if(!$bill_number) $this->error("数据写入错误，请联系管理员解决此问题。");
		else{
			$bill_detail=M('bill_detail');
			foreach($bill_detail_data as $key => $value){
				$bill_detail_items['bill_id']=$bill_number;
				$bill_detail_items['product_name']=$value['w_name'];
				$bill_detail_items['remark']=$this->_post('remark'.$key);
				$bill_detail_items['number']=$this->_post('number'.$key);
				$bill_detail_items['unit_price']=$value['w_price'];
				$bill_detail_items['discount']=$this->_post('discount'.$key);
				$bill_detail_items['price_sum']=$price_sum[$key];
				if(!$bill_detail->add($bill_detail_items)) $this->errer("数据条目：".$value['w_name']."写入出错，请联系管理员解决此问题。");
			}
		}
		$this->success('账单已成功提交！','Billlist');
	}
	function permission(){

		if(session('admin_type')<1) $this->error("您的权限不足");
		$admin=M('admin');
		if(isset($_GET['type'])){
			if(session('admin_type')<2) $this->error("您的权限不足以执行管理员权限更改");
			if($_GET['type']=="up")	$data['admin_type']=1;
			else if($_GET['type']=="down")	$data['admin_type']=0;
			if(isset($data) && isset($_GET['id'])){
				$result=$admin->where(array('id' => $this->_get('id')))->save($data);
				if(!$result) $this->error("更改权限失败");
			}else{
				$this->error('参数错误，修改权限失败，请确认您是从正常渠道提交的消息。');
			}
		}
		$admin_list=$admin->order('id')->field("id,username,post,admin_type")->select();
		$this->assign('admin_list',$admin_list);
		$this->display();
	}

	function billoutput(){
		header("Content-type: text/html; charset=utf-8");
		if($_POST['submit']=="导出账单列表"){
			$this->billoutput_list($_POST);
		}else if($_POST['submit']=="导出账单详情表"){
			$this->billoutput_detail($_POST);
		}else{
			$this->error("调用方式不正确");
		}
	}
	function billoutput_detail_one(){
		$array = array('checkbox1' => $this->_get('id'));
		$this->billoutput_detail($array);
	}
	public function billoutput_detail(array $datarow){
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>电子对账单</title><style>
			body{font-family:"微软雅黑", "黑体", "宋体";}
			h2{font-weight:normal;	font-size:30px;}
			.underline{
				padding:0px 20px 0px 20px;
				border-bottom-width: 1px;
				border-bottom-style: solid;
				border-bottom-color: #000;	
			}
			.haveborder td{
				border-top-width: 1px;
				border-bottom-width: 1px;
				border-left-width: 1px;
				border-top-style: solid;
				border-bottom-style: solid;
				border-left-style: solid;
				border-top-color: #333;
				border-bottom-color: #333;
				border-left-color: #333;
				text-align:center;
			}
			.borderlast{
				border-right-width: 1px;
				border-right-style: solid;
				border-right-color: #333;
			}
			</style>
			</head>
			<body>';


		$bill=M('bill');
		$bill_detail=M('bill_detail');
		$date=date("Y-m-d H:i");
		$this->assign("date",$date);
		foreach ($datarow as $key => $value) {
			if(substr($key, 0,8)=="checkbox"){
				$bill_data=$bill->where(array("id" => $value))->find();
				$bill_detail_data=$bill_detail->where(array("bill_id" => $value))->order('id')->select();
				$this->assign('bill_data',$bill_data);
				$this->assign('bill_detail_data',$bill_detail_data);
				$this->display('billoutput_detail');
			}
		}
		echo '<p>&nbsp;<br /><br /></p></body></html>';
	}
	public function billoutput_list(array $datarow){
		header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:filename=" ."Bill". substr(time(),5) . ".xls");
        echo '
        <html xmlns:o="urn:schemas-microsoft-com:office:office"
        xmlns:x="urn:schemas-microsoft-com:office:excel"
        xmlns="[url=http://www.w3.org/TR/REC-html40]http://www.w3.org/TR/REC-html40[/url]">
        <head>
        <meta http-equiv="expires" content="Mon, 06 Jan 1999 00:00:01 GMT">
        <meta http-equiv=Content-Type content="text/html; charset=utf-8">
        <!--[if gte mso 9]><xml>
        <x:ExcelWorkbook>
           <x:ExcelWorksheets>
                           <x:ExcelWorksheet>
                                   <x:Name></x:Name>
                                   <x:WorksheetOptions>
                                           <x:DisplayGridlines/>
                                   </x:WorksheetOptions>
                           </x:ExcelWorksheet>
           </x:ExcelWorksheets>
        </x:ExcelWorkbook>
        </xml><![endif]-->
        </head>
        <body link=blue vlink=purple leftmargin=0 topmargin=0>';
        echo '<table   border="0" cellspacing="0" cellpadding="0">';
        echo "<thead><tr>
        		<th>帐单号</th><th>客户名称</th>
        		<th>联系人</th><th>联系电话</th>
        		<th>交货日期</th><th>交货地点</th>
        		<th>结算方式</th><th>总价</th>
        		<th>实收</th><th>审核状态</th>
        		<th>审核人</th><th>是否要求发票</th>
        		<th>下单日期</th><th>备注</th>
        		<th>操作人</th>
        		</tr></thead><tbody>";

		$bill=M('bill');
		foreach ($datarow as $key => $value) {
			if(substr($key, 0,8)=="checkbox"){
				echo "<tr>";
				$bill_data=$bill->where(array("id" => $value))->find();
				foreach ($bill_data as $key1 => $value1) {
					if($key1=="id"||$key1=="abandon") continue;
					if($key1=="settlement"){
						if($value1=="1")	$value1="现金账户";
						else if($value1=="2")	$value1="记账客户";
					}
					if($key1=="check_state"){
						if($value1=="0")	$value1="未审核";
						else if($value1=="1")	$value1="审核通过";
						else if($value1=="2")	$value1="审核不通过";
					}
					if($key1=="invoice"){
						if($value1=="1")	$value1="√";
						else if($value1=="0")	$value1="--";
					}
					echo "<td > " . $value1 . "</td>";
				}
				echo "</tr>";
			}
		}
		echo'</tbody></table>';
        echo'</body>';
        echo'</html>';
	}
	function submit_invoiceid(){
		headerutf8();
		if(empty($_POST['Billlist_selected'])) $this->error('你没有选中任何一个订单!');
		if(session('admin_type')<2) $this->error('您的权限不足');
		$billarr=explode(",",$_POST['Billlist_selected']);
		$bill=M('bill');
		if(!empty($_POST['submit1'])){
			if(!empty($_POST['invoiceid'])){
				foreach ($billarr as $key => $value) {
					if(empty($value)) continue;
					$bill_order=$bill->where(array('orderid' => $value))->find();
					if($bill_order){
						if($bill_order['invoice']==0)	echo "[失败]订单：".$value."不要求发票<br />";
						else{
							$bill_order['invoice']=2;
							$bill_order['invoice_id']=$_POST['invoiceid'];
							$bill_order['invoice_date']=date('Y-m-d');
							if($bill->save($bill_order))	echo "[成功]订单：".$value."的发票号码已修改为".$_POST['invoiceid']."<br />";
							else echo "[失败]订单：".$value."在修改时遭遇未知错误<br />";
						}
					}else echo "[失败]订单：".$value."不存在<br />";
				}
				echo "操作完成 &nbsp;&nbsp;&nbsp;<a href=\"".U('Bill/billlist')."\">返回订单列表</a>";
			}else  $this->error('你没有填写发票号码');			
		}else if(!empty($_POST['submit2'])){
			$_POST['receive_sum']=trim($_POST['receive_sum']);
			if(!empty($_POST['receive_sum'])){
				if(empty($_POST['price_sum'])) $this->error('提交的值存在问题');
				$receive_rate=$_POST['receive_sum']/$_POST['price_sum'];
				if($receive_rate<=0) $this->error('提交的值不正确（请确认不包含非数字成分）');
			}else{
				$receive_rate=1;
			}
			foreach ($billarr as $key => $value) {
				if(empty($value)) continue;
				$bill_order=$bill->where(array('orderid' => $value))->find();
				if($bill_order){
					$bill_order['price_received']=$bill_order['price_sum']*$receive_rate;
					$bill_order['remark_sk']=$_POST['receive_remark'];
					$bill_order['receive_date']=date('Y-m-d');
					if($bill->save($bill_order))	echo "[成功]订单：".$value."的实收价格已修改为".$bill_order['price_sum']*$receive_rate."<br />";
					else echo "[失败]订单：".$value."在修改时遭遇未知错误<br />";
				}else echo "[失败]订单：".$value."不存在<br />";
			}
			echo "操作完成 &nbsp;&nbsp;&nbsp;<a href=\"".U('Bill/billlist')."\">返回订单列表</a>";
		}else{
			$this->error('提交出错。'.$_POST['submit']);
		}
	}
	function list_big(){
		$this->redirect('User/list_big');
	}
}

?>