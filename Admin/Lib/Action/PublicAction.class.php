<?php

class PublicAction extends Action {

    function login() {

        $this->display();
    }

    function code() {
        
        import("@.ORG.Vcode");
        $Vcode=new Vcode(80,30,4);
		$Vcode->showImg();
		$_SESSION["verify"]=md5(strtolower($Vcode->getCaptcha()));
   
    }
    /*管理员登录 */
    function adminLogin() {
          $admin=d("admin");
          
        
        if ($_SESSION['verify'] != md5(strtolower(trim($_POST['code'])))) {
            $this->error('验证码错误！');
        }
        if($data=$admin->where(array("username"=>$_POST["username"],"password"=>md5($_POST["password"])))->find()){
            $_SESSION["isLogin"]=1;
            $_SESSION["username"]=$data["username"];
            $_SESSION["admin_id"]=$data["id"];
            $_SESSION["admin_type"]=$data["admin_type"];
			 $this->redirect("Index/index");
            
        }else{
            $this->error("密码输入错误");
            z();
        }
    }
    /* 商户登录 */
    function shopLogin(){
        $Shop=M("Shop");
       if ($_SESSION['verify'] != md5(strtolower(trim($_POST['code'])))) {
            $this->error('验证码错误！');
        }
        if($data=$Shop->where("login_name='".$_POST["login_name"]."' and password='".md5($_POST["password"])."'")->find()){
            $_SESSION["shop_id"]=$data["id"];
            $_SESSION["login_name"]=$data["login_name"];
            $this->redirect("Sware/index");
        }else{
            $this->error("密码错误");
            echo M()->getLastSql();
            
        }
    }
    function logout(){
        $_SESSION=array();
        $this->success("退出登录成功");
    }

}