<?php
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
        '__PUB__'=> substr(SITE_ADDR,0,strlen(SITE_ADDR)-2).'/Public',
        '__ROOT__'=>substr(SITE_ADDR,0,strlen(SITE_ADDR)-2),
    ),
  //'SHOW_PAGE_TRACE'        =>true
);
?>