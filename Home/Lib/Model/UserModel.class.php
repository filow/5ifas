<?php
class UserModel extends Model{
    protected $_validate=array(
            array("loginname","5,20","登录名错误(5-12位)",1,"length"),
            array("password","require","没有填写密码",1,""),
            array("username","require","没有填写名字",1),
            array("qq","/^\d+$/","QQ号格式不正确",1,"regex"),
            array("tele","/^\d{11}$/","电话格式不正确",1,"regex"),
            array("sushel","require","宿舍楼选择错误",0),
            array("susheh","/^\d+$/","宿舍号格式不正确",0,"regex"),
    );
     
}