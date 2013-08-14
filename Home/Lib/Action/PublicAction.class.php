<?php

class PublicAction extends CommonAction {

    function login() {
        if(empty($_SESSION['loginname']))
            $this->display();
        else
            $this->error("您已经登录,要切换账户请先注销.");
    }

    function logout() {
        $username = $_SESSION["loginname"];
        $_SESSION = array();

        if (isset($_COOKIE[session_name()])) {
            setCookie(session_name(), '', time() - 100, '/');
        }
        session_destroy();

        $this->redirect('index/index');
    }

    function code() {
        import("@.ORG.Vcode");
        $Vcode = new Vcode(80, 30, 4);
        $Vcode->showImg();
        $_SESSION["verify"] = md5(strtolower($Vcode->getCaptcha()));
    }

    function islogin() {
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
                $this->redirect("Index/index");
            } else {
                $this->error("密码错误");
            }
        }
    }

    function forget() {
        $this->display();
    }

    function reset() {
        if ($_SESSION['verify'] != md5(strtolower(trim($_POST['code'])))) {
            $this->error('验证码错误！');
        }
        $user = D("user");
        $username = $_POST["username"];
        $tele = $_POST["tele"];
        if ($user = $user->where("username='" . $username . "' and tele=" . $tele)->find()) {
            $code = rand(1000, 9999);
            sendSms($tele, "您正在申请修改密码，短信校检码：" . $code . "艾星网络");
            $this->assign("loginname", $user["loginname"]);
            $this->assign("tele", $user["tele"]);
            $this->assign("cardn", $user["cardn"]);
            $this->assign("verify", md5($code . "lvxinwei" . $tele));
            $this->display();
        } else {
            $this->error("相关信息输入错误");
        }
    }

    function change() {

        if ($_POST["verify"] != md5(trim($_POST["code"]) . "lvxinwei" . $_POST["tele"])) {
            $this->error("手机校检码错误");
        } else {
            $user = D("user");
            if ($user = $user->where("cardn='" . $_POST["cardn"] . "' and tele=" . $_POST["tele"])->find()) {

                $this->assign("loginname", $user["loginname"]);
                $this->assign("tele", $user["tele"]);
                $this->assign("cardn", $user["cardn"]);

                $this->display();
            } else {
                $this->error("未知错误");
            }
        }
    }

    function c() {
        $user = D("user");
        if ($user->where("cardn=" . $_POST["cardn"])->setField("password", md5(trim($_POST["password"])))) {
            $this->success("修改成功", "login");
        } else {
            $this->error("未知错误");
        }
    }

    function reg() {
        $this->error("系统管理员已关闭公共注册功能");
        $info = D('Info');
        $sushe = $info->field("name,value")->where(array("type" => 1))->select();
        $data = $info->where(array("type" => 3))->select();
        $this->assign("data", $data);
        $this->assign('sushe', $sushe);
        $this->display();
    }
    function reg_check(){
        $user=D('User');
        if(!$user->create())
            $this->error($user->getError());

        $post_data=json_encode($_POST);
        $tele_num=(int)$_POST["tele"];
        $code=rand(1000,9999);
        $active_code=md5($code."lvxinwei".$tele_num);
        sendSms($tele_num,"您正注册艾星网络用户账号，手机校检码：".$code."。");
       // echo $code;
        $this->assign("active_code",$active_code);
        $this->assign("post_data",$post_data);
        $this->display();
    }
    function reg_add() {
        $post_data=json_decode($_POST["post_data"],true);
        if($_POST["active_code"]!=md5($_POST["code"]."lvxinwei".$post_data["tele"])){
            $this->error("手机校检码错误，请确认后输入");
            //print_r($_POST);
           // echo md5($_POST["code"]."lvxinwei".$post_data["tele"]);
        }
    
        $user = D('User');
        $info = d("info");
        $amountinfo = D("Amountinfo");
        $_POST=$post_data;
        $sushelou = $info->field('name')->where(array('type' => 1, 'value' => $_POST['sushel']))->find();
        $_POST['address'] = $sushelou['name'] . $_POST['susheh'];
        $_POST['reg_time'] = time();
        $_POST['password'] = md5(trim($_POST['password']));
        if (!isset($_POST['referrer'])) {
            $_POST['referrer'] = 0;
        }
        $activecode = md5($_POST['qq'] . time());
        $to = $_POST['qq'] . "@qq.com";
        $name = trim($_POST['loginname']);
        if ($user->create()) {
            $id = $user->add();
            $user->save(array("id" => $id, "cardn" => $id, 'activecode' => $activecode));

            $amount2 = $_POST['amount2'];
            $amount1 = $_POST['amount1'];
            if ($amount2 > 0) {
                $amountinfo->tz($id, $amount2);
            }
            if ($amount1 > 0) {
                $amountinfo->cz($id, $amount1);
            }
            $this->success("注册成功");
        } else {
            $this->error($user->getError());
        }
    }

    function check_qq() {
        $qq_num = $_GET["qq"];
        $user = D("user");
        if ($user->where("qq=" . $qq_num)->find()) {
            echo "<font color=red>QQ号码已存在</font>";
        } else {
            echo "<font color=green>该号码已存在</font>";
        }
    }

    function check_tele() {
        $tele_num = $_GET["tele"];
        $user = D("user");
        if ($user->where("tele=" . $tele_num)->find()) {
            echo "<font color=red>电话号码已存在</font>";
        } else {
            echo "<font color=green>该号码可以使用</font>";
        }
    }
     function check_loginname() {
        $loginname =trim($_GET["loginname"]);
        $user = D("user");
        if ($user->where("loginname='" . $loginname."'")->find()) {
            echo "<font color=red>用户名已存在</font>";
        } else {
            echo "<font color=green>用户名可以使用</font>";
        }
        
    }

}