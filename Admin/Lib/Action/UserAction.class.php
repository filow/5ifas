<?php

class UserAction extends CommonAction {

    function index() {
        $user = M("user");
        import("ORG.Util.Page");
        //读取筛选信息
        $query_data = getQuery();
        $query = $query_data["like_query"];
        $query.=" and zx=0 and is_bigcustomer=0";
        $count = $user->where($query)->count();
        //分页
        $page = new Page($count, 20);
        $show = $page->show();
        $u_data = $user->field("loginname,cardn,username,address,tele,qq,amount,jf,dj,referrer,reg_time,beizhu")->where($query)->order("id desc ")->limit($page->firstRow . ',' . $page->listRows)->select();

        $export_data[] = array("用户名", "卡号", "真实姓名", "地址", "电话号码", "QQ号", "账户余额", "积分余额", "等级", "推荐人", "注册时间", "备注");

        foreach ($u_data as $key => $value) {
            $value["reg_time"] = date("Y-m-d", $value["reg_time"]);
            $export_data[] = $value;
        }

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

        $page = new Page($count, 20);
        $show = $page->show();
        $u_data = $user->field("loginname,cardn,username,address,tele,qq,amount,jf,dj,referrer,reg_time,beizhu")->where($query)->order("id desc ")->limit($page->firstRow . ',' . $page->listRows)->select();

        $export_data[] = array("客户名", "卡号", "联系人", "地址", "电话号码", "QQ号", "账户余额", "积分余额", "等级", "推荐人", "注册时间", "备注");
        foreach ($u_data as $key => $value) {
            $value["reg_time"] = date("Y-m-d", $value["reg_time"]);
            $export_data[] = $value;
        }

        $this->assign("data", $u_data);
        $this->assign("query", $query_data["array"]);
        $this->assign("show", $show);
        $this->assign("export_data", json_encode($export_data));
        $this->display();
    }
    function add() {
        $info = M('Info');
        $sushe = $info->field("name,value")->where(array("type" => 1))->order('value')->select();
        $data = $info->where(array("type" => 3))->select();
        $this->assign("data", $data);
        $this->assign('sushe', $sushe);
        $this->display();
    }

    function insert() {
        $user = M('User');
        $info = M("info");
        $amountinfo = D("Amountinfo");

        $sushelou = $info->field('name')->where(array('type' => 1, 'value' => $_POST['sushel']))->find();
        $_POST['address'] = $sushelou['name'] . $_POST['susheh'];
        $_POST['reg_time'] = time();
        $_POST['password'] = md5(trim($_POST['password']));
        $_POST['amount']=0;
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
                $amountinfo->overdraw($id, $amount2);
            }
            if ($amount1 > 0) {
                $amountinfo->recharge($id, $amount1);
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
        $id = I('id',0,'intval');
        //读取用户信息
        $user = M('user');
        $data = $user->where(array("cardn" => $id))->find();
        $this->assign("data", $data);

        //读取宿舍信息
        $info = M('Info');
        $sushe = $info->field("name,value")->where(array("type" => 1))->select();
        $this->assign('sushe', $sushe);
        
        $this->display();
    }
    function mod_big() {
        $id = I('id',0,'intval');
        $user = M('user');
        $data = $user->where(array("cardn" => $id))->find();
        $this->assign("data", $data);
        $this->display();
    }
    function update_big() {
        $user = M("User");
        $id = I('id',0,'intval');

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
        $user = M("User");
        
        $loginname = $_POST['loginname'];
        $id = I('id',0,'intval');

        $info = M("info");
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
        $id = I('id',0,'intval');
        $User = M("User");
        $data['zx']=1;
        $data['zx_time']=time();
        if ($User->where("cardn=" . $id)->save($data)) {
            $this->success("注销成功");
        } else {
            $this->error("注销失败");
        }
    }
    function qzx() {
        $id = I('id',0,'intval');
        $User = M("User");
        $data['zx']=0;
        $data['zx_time']=0;
        if ($User->where("cardn=" . $id)->save($data)) {
            $this->success("取消注销成功");
        } else {
            $this->error("取消注销失败");
        }
    }
    function show_zx() {
        $user = M("user");
        import("ORG.Util.Page");
        $query_data = getQuery();

        $query = $query_data["like_query"];
        $query.=" and zx=1";
        $count = $user->where($query)->count();

        $page = new Page($count, 20);
        $show = $page->show();
        $u_data = $user->field("loginname,cardn,username,address,tele,qq,amount,jf,dj,referrer,reg_time,beizhu")->where($query)->order("zx_time desc,id desc")->limit($page->firstRow . ',' . $page->listRows)->select();

        $export_data[] = array("用户名", "卡号", "真实姓名", "地址", "电话号码", "QQ号", "账户余额", "积分余额", "等级", "推荐人", "注册时间", "备注");
        foreach ($u_data as $key => $value) {
            $value["reg_time"] = date("Y-m-d", $value["reg_time"]);
            $export_data[] = $value;
        }

        $this->assign("data", $u_data);
        $this->assign("query", $query_data["array"]);
        $this->assign("show", $show);
        $this->assign("export_data", json_encode($export_data));
        $this->display();
    }
    function output500(){
        $user = M("user");
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
        $user = M("user");
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
        $user = M("user");
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

    //以下是宿舍管理部分
    function dorm(){
        $info=M('info');
        if(!empty($_GET['action']) && !empty($_GET['id'])){
            $item=$info->where(array('id'=>$_GET['id']))->find();
            if($_GET['action']=="up"){
                if($item['order']>1){
                    $changed_data['order']=$item['value']-1;
                }else{
                    $changed_data['order']=1;
                }
                $info->where(array('id'=>$_GET['id']))->save($changed_data);
            }
            if($_GET['action']=="down"){
                $changed_data['order']=$item['order']+1;
                $info->where(array('id'=>$_GET['id']))->save($changed_data);
            }
            if($_GET['action']=="delete"){
                $info->where(array('id'=>$_GET['id']))->delete();
            }
        }
        $data=$info->where('type=1')->order("`order`")->field('id,name,order')->select();
        $this->assign('data',$data);
        $this->display();
    }
    function dorm_edit(){
        $info=M('info');
        if(!empty($_GET['dormname']) && !empty($_GET['id'])){
            $data['name']=$this->_get('dormname');
            $info->where(array('id' => $this->_get('id')))->save($data);
            $this->redirect('Area/dorm');
        }else{
            $this->error("参数错误");
        }
    }
    function dorm_add(){
        $info=M('info');
        if(!empty($_GET['dormname']) && !empty($_GET['order'])){
            $data['type']=1;
            $data['name']=$this->_get('dormname');
            $data['order']=$this->_get('order');
            $info->add($data);
            $this->redirect('Area/dorm');
        }else{
            $this->error("你没有写全，请再试一次");
        }
    }


}

?>
