<?php

function show_db_errorxx() {
    exit('系统访问量大，请稍等添加数据');
}

function z() {
    echo M()->getLastSql();
}

function generateId($shop_id, $sushel) {
    $orderid = "";
    $date = date("YmdHi", time());
    $orderid = "T" . $date . "-" . $sushel . "-" . $shop_id . "-" . time();
    return $orderid;
}

function ch_json_encode($data) {

    /**
     * 将中文编码
     * @param array $data
     * @return string
     */
    function ch_urlencode($data) {
        if (is_array($data) || is_object($data)) {
            foreach ($data as $k => $v) {
                if (is_scalar($v)) {
                    if (is_array($data)) {
                        $data[$k] = urlencode($v);
                    } else if (is_object($data)) {
                        $data->$k = urlencode($v);
                    }
                } else if (is_array($data)) {
                    $data[$k] = ch_urlencode($v); //递归调用该函数
                } else if (is_object($data)) {
                    $data->$k = ch_urlencode($v);
                }
            }
        }
        return $data;
    }

    $ret = ch_urlencode($data);
    $ret = json_encode($ret);
    return urldecode($ret);
}
function sendSms($tele_num,$msg){
    header("Content-type:text/html;charset=utf-8");
	$sms=D("sms");
    $msg_data=utf8_array($msg);
	$msg_count=count($msg_data);//字数
	$msg_num=1;//短信条数
	if($msg_count>65){
		  $msg_num=ceil($msg_count/62);
	}
   
	if($msg_num==1){
	    $insert_data=array("is_sent"=>0,"tele_num"=>$tele_num,"add_time"=>time(),"send_time"=>0,"send_content"=>$msg);
		$sms->add($insert_data);
	}else{
		for($i=0;$i<$msg_num;$i++){
		     $msg_send="";
			 for($j=$i*62;$j<($i+1)*62;$j++){
			    if($j<$msg_count)
				   $msg_send.=$msg_data[$j];
				
			 }
			 $msg_send.="(".($i+1).")";
			  $insert_data=array("is_sent"=>0,"tele_num"=>$tele_num,"add_time"=>time(),"send_time"=>0,"send_content"=>$msg_send);
		      $sms->add($insert_data);
		}
	}
 
	 

}
function utf8_array($msg){
	$result=array();
	$strlen = strlen($msg);
	for($i=0; $i<$strlen; $i++) {
		$str=substr($msg, $i, 1);
		if(ord($str)>0xA0 ){
			$result[]=substr($msg, $i, 3);
			$i =$i+2;
		}else{
			$result[]=substr($msg, $i,1);
		}
	}
	return $result;

}
function track($msg){
$kv = new SaeKV();
$ret = $kv->init();
$ret = $kv->set(time(), $msg);
 
}
?>