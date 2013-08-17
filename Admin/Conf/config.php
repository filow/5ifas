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

    //用户验证设置
    'NOT_AUTH_MODULE' => 'Index',
    'RBAC_ROLE_TABLE' => 'think_role',
    'RBAC_USER_TABLE' => 'think_user',
    'RBAC_ACCESS_TABLE' => 'think_access',
    'RBAC_NODE_TABLE' => 'think_node',
    // USER_AUTH_ON 是否需要认证
    // USER_AUTH_TYPE 认证类型
    // USER_AUTH_KEY 认证识别号
    // REQUIRE_AUTH_MODULE  需要认证模块
    'SHOW_PAGE_TRACE'        =>true
);
?>