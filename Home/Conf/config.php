<?php
//$script_name=$_SERVER['SCRIPT_NAME'];
//$script_name=explode("/",$script_name);
return array(
    "DB_TYPE" => "mysql",
    "DB_HOST" => "localhost",
    "DB_NAME" => "thinkphp",
    "DB_USER" => "root",
    "DB_PWD" => "",
    "DB_PORT" => "3306",
    "DB_PREFIX" => "think_",
    'URL_CASE_INSENSITIVE' => true,
    "TMPL_PARSE_STRING" => array(
        '__PUB__'=> SITE_ADDR.'Public',
        '__ROOT__'=>SITE_ADDR,
    ),
//    'SHOW_PAGE_TRACE'        =>true
);
?>