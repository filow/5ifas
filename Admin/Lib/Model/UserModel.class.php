<?php
class UserModel extends Model{
    protected $_validate=array(
          array("loginname","require","信息不全"),
         array("username","require","信息不全"),
         array("qq","require","信息不全"),
         array("password","require","信息不全",1,"",1),
         array("susheh","require","信息不全"),
         array("tele","require","信息不全"),
         array("loginname","","登录名已经存在",0,"unique",1),
         //array("qq","","qq已经存在",0,"unique",1),
        // array("tele","","电话号已经存在",0,"unique",1),
         array("tele","/^\d+$/","电话格式不正确",0,"regex"),//2013-1-12  将/^\d{11}$/替换成这个,取消11位电话号码的限制
          array("susheh","/^\d+$/","宿舍号格式不正确",0,"regex"),
        array("qq","/^\d+$/","QQ号格式不正确",0,"regex"),
    );
        
     
}