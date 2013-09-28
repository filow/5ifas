<?php

class IndexAction extends CommonAction {

    function index() {
    	$cache_expire=28800;//缓存8小时
    	$admin=M('admin');
    	//开始统计管理员数量
    	if(!S('admin_count')){

	    	$admin_count['total']=$admin->count();
	    	$admin_count['zx']=$admin->where(array('isdelete'=> 0))->count();
	    	S('admin_count',$admin_count,$cache_expire);
	    }else{
			$admin_count=S('admin_count');
	    }
	    $this->assign('admin_count',$admin_count);

	    //开始统计用户数量
    	if(!S('user_count')){
	    	$user=M('user');
	    	$user_count['total']=$user->count();
	    	$user_count['zx']=$user->where(array('zx'=> 0))->count();
	    	$user_count['bigcustomer']=$user->where(array('zx'=> 0,'is_bigcustomer'=>1))->count();
	    	$user_count['havemoney']=$user->where(array('zx'=> 0,'amount'=>array('GT',0)))->count();
	    	S('user_count',$user_count,$cache_expire);
	    }else{
			$user_count=S('user_count');
	    }
	    $this->assign('user_count',$user_count);

	    //建立当月统计信息
	    if(!S('analyse_rebuild')){
	    	for($i=1;$i<=3;$i++){
			    A('Analyse')->build_analyze_data($i);
			}
			S('analyse_rebuild',1,3600*6);
	    }

		$analyze=M('analyze');
		if(!S('ana_data')){
			for($i=1;$i<=3;$i++){
			    $ana_data[$i]=$analyze->where(array('type'=>$i))->order('date desc')->field('id,type',true)->limit(30)->select();
			    foreach($ana_data[$i] as $key => $value){
			    	$ana_data[$i][$key]['date']=substr($value['date'],0,4)."-".substr($value['date'],4,2);
			    }
			}
			S('ana_data',$ana_data,3600*12);
		}else{
			$ana_data=S('ana_data');
		}

		$this->assign('amountinfo_data',json_encode($ana_data[1]));
		$this->assign('bill_data',json_encode($ana_data[2]));
		$this->assign('order_data',json_encode($ana_data[3]));

	    //
        $this->display();
    }
    

}

?>