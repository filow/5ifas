<?php
header("Content-type:text/html;charset=utf-8");
$sms = apibus::init( "sms");
$tele_num=(int)$_POST["tele"];
$msg=$_POST["msg"];
$obj = $sms->send($tele_num, $msg , "UTF-8"); 
print_r( $obj ); 
?>
<html>
<head>
	<script>
 function go(){
 window.history.go(-1);
 }
 setTimeout("go()",1000);
 

</script>
</head>

<body>
</body>
</html>