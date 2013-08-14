<?php
define('SITE_ADDR',substr($_SERVER['SCRIPT_NAME'],0,strlen($_SERVER['SCRIPT_NAME'])-10));
define('THINK_PATH','./ThinkPHP/');
define('APP_NAME','m');
define('APP_PATH','./m/');
define('APP_DEBUG',true);
//define('SAE_RUNTIME',true);
require './ThinkPHP/Extend/Engine/Sae.php';
