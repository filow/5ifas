<?php

function show_db_errorxx() {
    exit('系统访问量大，请稍等添加数据');
}
function headerutf8(){
    header("Content-type:text/html;charset=utf-8");
}
function getQuery($data=array()) {
    
    $query_data =count($data)==0?$_GET:$data;
    $result = array();
    unset($query_data["p"], $query_data["__hash__"], $query_data["_URL_"]);
    foreach ($query_data as $key => $value) {
        if (trim($value) == "") {
            unset($query_data[$key]);
            continue;
        }
        $result["array"][$key] = $value;
        if (!is_numeric($value))
            $query_data[$key] = "'" . urldecode($value) . "'";
    }

    if (count($query_data) < 1)
        $query = "1=1";
    $query_data = urldecode(http_build_query($query_data));
    //echo $query_data;
    $query.=str_replace("&", " and  ", $query_data);
    $like_query = "";
    foreach ($result["array"] as $key => $value) {
        $like_query.=$key . " like '%" . $value . "%'and ";
    }
    $like_query.="1=1";
    $result["string"] = $query;
    $result["like_query"] = $like_query;
    return $result;
}

function z() {
    echo M()->getLastSql();
}

function email($to, $name, $type, $code) {
    
}
function generateId($shop_id,$sushel){
    $orderid="";
    $date=date("YmdHi",time());
    $orderid="T".$date."-".$sushel."-".$shop_id;
    return  $orderid;
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

function custom_output($datasource,$prefix){
    $data=json_decode($datasource,true);
   // print_r($data);
   // exit;
    header("Content-type:application/vnd.ms-excel");
    header("Content-Disposition:filename=" . $prefix. date("Ymd") . ".xls");
    echo'
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
    echo'<table   border="0" cellspacing="0" cellpadding="0">';


    foreach ($data as $key => $ln) {
        echo "<tr>";
        foreach ($ln as $ln_key => $ln_value) {
            echo "<td > " . $ln_value . "</td>";
        }
        echo "</tr>";
    }

    echo'</table>';
    echo'</body>';
    echo'</html>';
  }
/**
 * 递归重组节点信息为多维数组
 * @param  [array]  $node [要处理的节点]
 * @param  integer $pid  [父级id]
 * @return [array]        [多维数组]
 */
function node_merge($node,$pid=0){
    $arr= array();
    foreach($node as $v){
        if($v['pid']==$pid){
            $temp=node_merge($node,$v['id']);
            if($temp) $v['child']=$temp;
            
            $arr[]=$v;
        }
    }

    return $arr;
}
/**
 * 包含筛选条件的URL生成函数
 * 将根据目前的get参数和传入的新参数,组装新url
 * 默认地址为当前页面
 * @param array  $vars [description]
 * @param string $url  [description]
 */
function UG($vars=array(),$url=''){
    $get=$_GET;
    unset($get['_URL_']);
    $get=array_merge($get,$vars);
    return U($url,$get);
}


?>