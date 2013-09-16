<?php
define('SITE_ADDR',substr($_SERVER['SCRIPT_NAME'],0,strlen($_SERVER['SCRIPT_NAME'])-10));
define('THINK_PATH','./ThinkPHP/');
define('APP_NAME','Admin');
define('APP_PATH','./Admin/');
define('APP_DEBUG',false);
//define('SAE_RUNTIME',true);
define('ENGINE_PATH', THINK_PATH . '/Extend/Engine/');
require './ThinkPHP/Extend/Engine/Sae.php'; //sae引擎
