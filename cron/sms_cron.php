<?php
 $sms = apibus::init( "sms"); //创建短信服务对象
header("content-type:text/html;charset=utf-8");
$mysql=new SaeMysql();
$sql="select * from think_sms where is_sent=0 group by tele_num ";
$data_all=$mysql->getData($sql);
foreach($data_all as $key=>$value){
     $obj = $sms->send( $value["tele_num"], $value["send_content"] , "UTF-8"); 
	  if (!$sms->isError( $obj ) ) { 
          echo $sql="update think_sms set send_time=".time()." , is_sent=1 where id=".$value["id"];
		  $mysql->runSql($sql);
		  if( $mysql->errno() != 0 )
			{
				die( "Error:" . $mysql->errmsg() );
			}
 
       }else{
			print_r( $obj->ApiBusError->errcode ); 
	   }
}