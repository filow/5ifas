<?php
 class ShopModel extends RelationModel{
     protected $_validate=array(
         array("shop_name","require","必须填写商家名"),
         array("address","require","必须填写商家地址"),
         array("tele","require","必须填写商家电话"),
         array("login_name","require","必须填写商家登录名"),
         array("password","require","必须填写商家登陆密码",2),
         array("tele","/^\d{11}$/","电话格式不正确",0,"regex"),
         array("login_name","","登录名已经存在",0,"unique",1),
     );
     
 }