<?php
define('SITE_ADDR',substr($_SERVER['SCRIPT_NAME'],0,strlen($_SERVER['SCRIPT_NAME'])-10));
define('THINK_PATH','./ThinkPHP/'); //thinkphp框架的地址
define('APP_NAME','Home');          //预设app的名称  一般是home
define('APP_PATH','./Home/');       //预设app的地址
define('APP_DEBUG',true);			//调试模式
//define('SAE_RUNTIME',true);       
require './ThinkPHP/Extend/Engine/Sae.php';	//sae引擎
