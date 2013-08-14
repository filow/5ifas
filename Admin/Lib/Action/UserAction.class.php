<?php

class UserAction extends CommonAction {

    function index() {
        $user = D("user");
        import("ORG.Util.Page");
        $query_data = getQuery();

        $query = $query_data["like_query"];
        $query.=" and zx=0 and is_bigcustomer=0";
        $count = $user->where($query)->count();
// z();
        $page = new Page($count, 20);
        $show = $page->show();
        $u_data = $user->field("loginname,cardn,username,address,tele,qq,amount,jf,dj,referrer,reg_time,beizhu")->where($query)->order("id desc ")->limit($page->firstRow . ',' . $page->listRows)->select();
        //	z();
        $export_data[] = array("用户名", "卡号", "真实姓名", "地址", "电话号码", "QQ号", "账户余额", "积分余额", "等级", "推荐人", "注册时间", "备注");
        foreach ($u_data as $key => $value) {
            $value["reg_time"] = date("Y-m-d", $value["reg_time"]);
            $export_data[] = $value;
        }
        // print_r($u_data);
        $this->assign("data", $u_data);
        $this->assign("query", $query_data["array"]);
        $this->assign("show", $show);
        $this->assign("export_data", json_encode($export_data));
        $this->display();
    }
    function list_big(){
        $user = D("user");
        import("ORG.Util.Page");
        $query_data = getQuery();

        $query = $query_data["like_query"];
        $query.=" and zx=0 and is_bigcustomer=1";
        $count = $user->where($query)->count();
// z();
        $page = new Page($count, 20);
        $show = $page->show();
        $u_data = $user->field("loginname,cardn,username,address,tele,qq,amount,jf,dj,referrer,reg_time,beizhu")->where($query)->order("id desc ")->limit($page->firstRow . ',' . $page->listRows)->select();
        //  z();
        $export_data[] = array("客户名", "卡号", "联系人", "地址", "电话号码", "QQ号", "账户余额", "积分余额", "等级", "推荐人", "注册时间", "备注");
        foreach ($u_data as $key => $value) {
            $value["reg_time"] = date("Y-m-d", $value["reg_time"]);
            $export_data[] = $value;
        }
        // print_r($u_data);
        $this->assign("data", $u_data);
        $this->assign("query", $query_data["array"]);
        $this->assign("show", $show);
        $this->assign("export_data", json_encode($export_data));
        $this->display();
    }
    function add() {
        $info = D('Info');
        $sushe = $info->field("name,value")->where(array("type" => 1))->order('value')->select();
        $data = $info->where(array("type" => 3))->select();
        $this->assign("data", $data);
        $this->assign('sushe', $sushe);
        $this->display();
    }

    function insert() {
        $user = D('User');
        $info = d("info");
        $amountinfo = D("Amountinfo");
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
    function addbig(){
        $this->display();
    }
    function insert_big(){
        $user=M('user');
        if($user->where(array('loginname' =>$_POST['loginname']))->find()) $this->error('登录名已经被使用过了，请换一个');
        $data['username']=$_POST['username'];
        $data['password']=md5(trim($_POST['password']));
        $data['qq']=$_POST['qq'];
        $data['reg_time']=time();
        $data['tele']=$_POST['tele'];
        $data['amount']=0;
        $data['activecode']=md5($_POST['qq'] . time());
        $data['dj']="大客户";
        $data['isactive']=2;
        $data['referrer']=NULL;
        $data['address']=$_POST['address'];
        $data['loginname']=$_POST['loginname'];
        $data['beizhu']=$_POST['beizhu'];
        $data['is_bigcustomer']=1;
        $cardn=$user->add($data);
        if($cardn){
            $somedata['cardn']=$cardn;
            $user->where('id='.$cardn)->save($somedata);
            $this->success('添加成功','list_big');
        }else{
            $this->error('添加大客户失败'.$cardn.z());
        }

    }
    function mod() {
        $id = (int) $_GET['id'];
        $info = D('Info');
        $sushe = $info->field("name,value")->where(array("type" => 1))->select();
        $this->assign('sushe', $sushe);
        $user = D('user');
        $data = $user->where(array("cardn" => $id))->find();
        $this->assign("data", $data);
        $this->display();
    }
    function mod_big() {
        $id = (int) $_GET['id'];
        $user = D('user');
        $data = $user->where(array("cardn" => $id))->find();
        $this->assign("data", $data);
        $this->display();
    }
    function update_big() {
        $user = D("User");
        $id = (int) $_POST['id'];

        if ((isset($_POST['password'])) && ($_POST['password'] != "")) {
            $_POST['password'] = md5($_POST['password']);
        } else {
            unset($_POST['password']);
        }

        if (!$user->create()) {
            $this->error($user->getError());
        }
        if ($user->save()) {
            $this->success("修改成功");
        } else {
            $this->error("修改失败");
        }
        $this->redirect('list_big');
    }
    function update() {
        $user = D("User");
        $info = D("info");
        $loginname = $_POST['loginname'];
        $id = (int) $_POST['id'];
        $sushelou = $info->field('name')->where(array('type' => 1, 'value' => $_POST['sushel']))->find();
        $address = $sushelou['name'] . $_POST['susheh'];
        $susheh = $_POST['susheh'];
        $_POST['address'] = $address;
        if ((isset($_POST['password'])) && ($_POST['password'] != "")) {
            $_POST['password'] = md5($_POST['password']);
        } else {
            unset($_POST['password']);
        }
        if (!$user->create()) {
            $this->error($user->getError());
        }
        if ($user->save()) {
            $this->success("修改成功");
        } else {
            $this->error("修改失败");
        }
    }

    function check_loginname() {
        $loginname =trim($_GET["loginname"]);
        $user = D("user");
        if ($user->where("loginname='" . $loginname. "'")->find()) {
            echo "***";
        } else {
            echo "###";
        }
    }
    function check_qq() {
        $qq_num = $_GET["qq"];
        $user = D("user");
        if ($user->where("qq=" . $qq_num)->find()) {
            echo "***";
        } else {
            echo "###";
        }
    }

    function check_tele() {
        $tele_num = $_GET["tele"];
        $user = D("user");
        if ($user->where("tele=" . $tele_num)->find()) {
            echo "***";
        } else {
            echo "###";
        }
    }

    function zx() {
        $id = (int) $_GET["id"];
        $User = D("User");
        if ($User->where("cardn=" . $id)->setField("zx", 1)) {
            $this->success("注销成功");
        } else {
            $this->error("注销失败");
        }
    }
 function qzx() {
        $id = (int) $_GET["id"];
        $User = D("User");
        if ($User->where("cardn=" . $id)->setField("zx", 0)) {
            $this->success("取消注销成功");
        } else {
            $this->error("取消注销失败");
        }
    }
    function show_zx() {
        $user = D("user");
        import("ORG.Util.Page");
        $query_data = getQuery();

        $query = $query_data["like_query"];
        $query.=" and zx=1";
        $count = $user->where($query)->count();
// z();
        $page = new Page($count, 20);
        $show = $page->show();
        $u_data = $user->field("loginname,cardn,username,address,tele,qq,amount,jf,dj,referrer,reg_time,beizhu")->where($query)->order("id asc ")->limit($page->firstRow . ',' . $page->listRows)->select();
        //	z();
        $export_data[] = array("用户名", "卡号", "真实姓名", "地址", "电话号码", "QQ号", "账户余额", "积分余额", "等级", "推荐人", "注册时间", "备注");
        foreach ($u_data as $key => $value) {
            $value["reg_time"] = date("Y-m-d", $value["reg_time"]);
            $export_data[] = $value;
        }
        // print_r($u_data);
        $this->assign("data", $u_data);
        $this->assign("query", $query_data["array"]);
        $this->assign("show", $show);
        $this->assign("export_data", json_encode($export_data));
        $this->display();
    }
    function output500(){
        $user = D("user");
        $query_data = getQuery();
        $query = $query_data["like_query"];
        $query.=" and zx=0 and is_bigcustomer=0";
        $u_data = $user->field("loginname,cardn,username,address,tele,qq,amount,jf,dj,referrer,reg_time,beizhu")->where($query)->order("id desc ")->limit(500)->select();
        $export_data[] = array("用户名", "卡号", "真实姓名", "地址", "电话号码", "QQ号", "账户余额", "积分余额", "等级", "推荐人", "注册时间", "备注");
        foreach ($u_data as $key => $value) {
            $value["reg_time"] = date("Y-m-d", $value["reg_time"]);
            $export_data[] = $value;
        }
        custom_output(json_encode($export_data),"User");
    }
    function outputall(){
        $user = D("user");
        $query_data = getQuery();
        $query = $query_data["like_query"];
        $query.=" and zx=0 and is_bigcustomer=0";
        $u_data = $user->field("loginname,cardn,username,address,tele,qq,amount,jf,dj,referrer,reg_time,beizhu")->where($query)->order("id desc ")->select();
        $export_data[] = array("用户名", "卡号", "真实姓名", "地址", "电话号码", "QQ号", "账户余额", "积分余额", "等级", "推荐人", "注册时间", "备注");
        foreach ($u_data as $key => $value) {
            $value["reg_time"] = date("Y-m-d", $value["reg_time"]);
            $export_data[] = $value;
        }
        custom_output(json_encode($export_data),"User");
    }
    function output500_big(){
        $user = D("user");
        $query_data = getQuery();
        $query = $query_data["like_query"];
        $query.=" and zx=0 and is_bigcustomer=1";
        $u_data = $user->field("loginname,cardn,username,address,tele,qq,amount,jf,dj,referrer,reg_time,beizhu")->where($query)->order("id desc ")->limit(500)->select();
        $export_data[] = array("用户名", "卡号", "真实姓名", "地址", "电话号码", "QQ号", "账户余额", "积分余额", "等级", "推荐人", "注册时间", "备注");
        foreach ($u_data as $key => $value) {
            $value["reg_time"] = date("Y-m-d", $value["reg_time"]);
            $export_data[] = $value;
        }
        custom_output(json_encode($export_data),"UserBig");
    }
    function transfer(){
        if(!empty($_GET['cardn'])){
            $user=M('user');
            $data['is_bigcustomer']= (int) $_GET['method'];
            $result=$user->where(array('cardn' => $_GET['cardn']))->save($data);
            if($result){
                header("Content-type: text/html; charset=utf-8");
                echo "成功修改卡号为".$_GET['cardn']."的用户<br />接下来将修改商家订单的属性<br />";
                $order=M('order');
                $number1=$order->where(array('cardn' => $_GET['cardn']))->setField($data);
                echo "修改了".$number1."条相关的商家订单记录<br />接下来将修改充值代扣订单的属性<br />";
                $amountinfo=M('amountinfo');
                $number2=$amountinfo->where(array('userid' => $_GET['cardn']))->setField($data);
                echo "修改了".$number2."条相关的充值代扣订单记录<br />";
                echo "<p><a href=".U('transfer').">返回</a></p>";
            }
            else  $this->error("修改失败",'transfer');
        }else{
            $this->display();
        }

    }
}

?>
