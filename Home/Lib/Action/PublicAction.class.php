<?php

class PublicAction extends CommonAction {

    function login() {
        if(!session('loginname'))
            $this->display();
        else
            $this->error("您已经登录,要切换账户请先注销.");
    }

    function logout() {
        session(null);
        $this->success('已成功注销',U('index/index'));
    }

    function code() {
        import("@.ORG.Vcode");
        $Vcode = new Vcode(80, 30, 4);
        $Vcode->showImg();
        $_SESSION["verify"] = md5(strtolower($Vcode->getCaptcha()));
    }

    function logincheck() {
        $u = M("user");
        if ($_SESSION['verify'] != md5(strtolower(trim($_POST['code'])))) {
            $this->error('验证码错误！');
        }

        if (trim($_POST['loginname']) == "" || trim($_POST['password']) == "") {
            $this->error('用户名或密码不能为空');
        } else {
            if ($user = $u->where(array('loginname' => $_POST['loginname'], "password" => md5($_POST['password'])))->find()) {
                foreach ($user as $key => $value) {
                    $_SESSION[$key] = $user[$key];
                }
                $this->redirect("User/index");
            } else {
                $this->error("密码错误");
            }
        }
    }
}