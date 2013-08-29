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
    // 'RBAC_SUPERADMIN' => 'filowlee', //超级管理员名称
    'ADMIN_AUTH_KEY' => 'superadmin',
    'USER_AUTH_ON' => true,
    'USER_AUTH_TYPE' => 1,
    'USER_AUTH_KEY' => 'admin_id',  //认证识别号

    //无需验证的模块和控制器
    'NOT_AUTH_MODULE' => 'Index,Common,Public,Export',
    'NOT_AUTH_ACTION' => 'user/check_loginname,
                        user/check_qq,
                        user/check_tele,
                        Amountinfo/manage_searchusr,
                        Bill/index,
                        Bill/product_ajax,
                        Bill/product_info_ajax,
                        Bill/billoutput_detail_one,
                        Bill/billoutput_detail,
                        Bill/billoutput_list,
                        DK/waresearch,
                        DK/usersearch,
                        ware/upload_file',

    'RBAC_ROLE_TABLE' => 'think_role',
    'RBAC_USER_TABLE' => 'think_role_user',
    'RBAC_ACCESS_TABLE' => 'think_access',
    'RBAC_NODE_TABLE' => 'think_node',

    // 显示页面追踪信息
    'SHOW_PAGE_TRACE'        =>true
);
?>