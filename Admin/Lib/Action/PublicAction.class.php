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
        $admin=M("admin");
        if(!APP_DEBUG){
            if ($_SESSION['verify'] != md5(strtolower(trim($_POST['code'])))) 
                $this->error('验证码错误！');
        }
        $data=$admin->where(array("username"=>$_POST["username"],"password"=>md5($_POST["password"])))->find();
        if($data){

            if($data['isdelete']=="1") 
                $this->error('您的账户已被删除,更多信息请联系管理员');

            session('username',$data["username"]);
            session(C('USER_AUTH_KEY'),$data["id"]);
            session('logintime',time());
            
            //超级管理员权限
            if($data["username"]==C('RBAC_SUPERADMIN')){
                session(C('ADMIN_AUTH_KEY'),true);
            }

            //引入权限管理类
			import('@.ORG.RBAC');
            RBAC::saveAccessList();

            $this->redirect("Index/index");
        }else{
            $this->error("密码输入错误");
        }
    }

    function logout(){
        session(NULL);
        $this->success("退出登录成功");
    }

}