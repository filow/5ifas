<?php

class AmountinfoAction extends CommonAction {

    function searchexport() {
        $user = D("user");
        import("ORG.Util.Page");
		$amountinfo = D("Amountinfo");
		$user=D("user");
		if(isset($_GET["username"])&&trim($_GET["username"])!=""){
			$user_data=$user->where("username like '%".$_GET["username"]."%'")->find();
			$_GET["userid"]=$user_data["cardn"];
			unset($_GET["username"]);
		}
        if(!empty($_GET["query_type"])){
            unset($_GET["query_type"]);
            $query_data = getQuery();
            $query = $query_data["like_query"];
        }else{
            $query_data = getQuery();
            $query = $query_data["string"];
        }
        $query.=" and type !=3";
        // print_r($query_data);
       
        $u = d("user");
        $count = $amountinfo->where($query)->count();
        //z();
        $page = new Page($count, 40);
        $show = $page->show();
        $data = $amountinfo->where($query)->order("id desc ")->limit($page->firstRow . ',' . $page->listRows)->select();
        $export_data[] = array("充值订单号", "用户卡号", "用户名", "手机号", "地址", "申请充值时间", "操作人", "金额", "状态", "处理时间", "备注", "说明","余额");
        $search_data = array();
        $price_sum=0;//统计当前页总价
        foreach ($data as $key => $value) {
            $userid = $u->where(array("cardn" => $value['userid']))->field('username,tele,address,amount')->find();
            $value['username'] = $userid['username'];
            $value['address'] = $userid['address'];
            $value['tele'] = $userid['tele'];
            $value['createtime'] = date("Y-m-d H:i:s", $value['createtime']);
            $value['suctime'] = date("Y-m-d H:i:s", $value['suctime']);
			$value["yue"]=$userid["amount"];
            $value['type1']=$value["type"];
            $price_sum+=$value["amount"];
            switch ($value["type"]) {
                case 0:$value['type'] = "未收费";
                    $value['suctime'] = "";
                    break;
                case 1: $value['type'] = "已收费";
                    break;
                case 2 :$value['type'] = "返现";
                    break;
                case 4 : $value['type'] = "未返现";
                    break;
            }
            $search_data[] = $value;
            $export_data[] = array($value['orderid'], $value['userid'], $value['username'], $value['tele'], $value['address'], $value['createtime'], $value['operator'], $value['amount'], $value['type'], $value['suctime'], $value['beizhu'], $value['beizhu1'],$value["yue"]);
        }
        //print_r($search_data);
        $query_array = $query_data["array"];



        $this->assign("data", $search_data);
        $this->assign("query", $query_data);
        $this->assign("show", $show);
        $this->assign("export_data", json_encode($export_data));
        $this->assign("price_sum",$price_sum);
        $this->display();
    }
    function export500(){
        $user = D("user");
        $amountinfo = D("Amountinfo");
        $user=D("user");
        if(isset($_GET["username"])&&trim($_GET["username"])!=""){
            $user_data=$user->where("username like '%".$_GET["username"]."%'")->find();
            $_GET["userid"]=$user_data["cardn"];
            unset($_GET["username"]);
        }
        $query_data = getQuery();
        $query = $query_data["like_query"];
        $query.=" and type !=3";
        $data = $amountinfo->where($query)->order("id desc ")->limit(500)->select();
        $export_data[] = array("充值订单号", "用户卡号", "用户名", "手机号", "地址", "申请充值时间", "操作人", "金额", "状态", "处理时间", "备注", "说明","余额");
        foreach ($data as $key => $value) {
            $userid = $user->where(array("cardn" => $value['userid']))->field('username,tele,address,amount')->find();
            $value['username'] = $userid['username'];
            $value['address'] = $userid['address'];
            $value['tele'] = $userid['tele'];
            $value['createtime'] = date("Y-m-d H:i:s", $value['createtime']);
            $value['suctime'] = date("Y-m-d H:i:s", $value['suctime']);
            $value["yue"]=$userid["amount"];
            $value['type1']=$value["type"];
            switch ($value["type"]) {
                case 0:$value['type'] = "未收费";
                    $value['suctime'] = "";
                    break;
                case 1: $value['type'] = "已收费";
                    break;
                case 2 :$value['type'] = "返现";
                    break;
                case 4 : $value['type'] = "未返现";
                    break;
            }
            $export_data[] = array($value['orderid'], $value['userid'], $value['username'], $value['tele'], $value['address'], $value['createtime'], $value['operator'], $value['amount'], $value['type'], $value['suctime'], $value['beizhu'], $value['beizhu1'],$value["yue"]);
        }
        custom_output(json_encode($export_data),"Amountinfo");
    }
    function manage() {
        $amtinfo=M('Amountinfo');
        $u = d("user");
        import("ORG.Util.Page");
        $query="type=0 ";
        if(!empty($_GET['is_bigcustomer']))
            $query.="and is_bigcustomer=".$_GET['is_bigcustomer'];
        $count = $amtinfo->where($query)->count();
        $page = new Page($count, 20);
        $show = $page->show();
        $data=$amtinfo->where($query)->limit($page->firstRow . ',' . $page->listRows)->order('id desc')->select();
        foreach ($data as $key => $value){
            $userid = $u->where(array("cardn" => $value['userid']))->field('username,amount,is_bigcustomer')->find();
            $data[$key]['username'] = $userid['username'];
            $data[$key]['is_bigcustomer'] = $userid['is_bigcustomer'];
            $data[$key]['createtime'] = date("m月d日 H:i", $value['createtime']);
            $data[$key]["yue"]=$userid["amount"];
        }
        $this->assign('data',$data);
        $this->assign('show',$show);
        $this->display();
    }
    function manageserver() {
        $amount = D("Amountinfo");
        $orderid = trim($_GET['dingdanhao']);
        $result=$amount->where(array('userid' => $_GET['cardn'],'orderid' => $_GET['dingdanhao']))->find();
        if(!$result) $this->error("卡号与订单不匹配，请重新输入");
        if ($amount->czid($orderid)) {
            $this->success("处理成功");
        } else {
            $this->error("无效订单号");
        }
    }
    function manage_searchusr(){
        if(empty($_GET['usrname'])) return;
        $user=M('user');
        $userdata=$user->where('loginname like \'%'.$_GET['usrname'].'%\' and zx=0')->field('cardn,username,loginname,is_bigcustomer')->limit(10)->order('is_bigcustomer desc')->select();
        echo json_encode($userdata);
    }
    function recharge(){
        $this->display();
    }
    function rechargeserver(){
        $amountinfo = D("Amountinfo");
        $user = d("user");
        $username = $_POST['username'];
        $cardn = $_POST['cardn'];
        $amount = $_POST['amount'];
        $beizhu1 = $_POST['beizhu1'];
        if (!($user->where(array("cardn" => $cardn, "username" => $username))->find())) {
            $this->error("用户信息输入错误");
        }
        if ($amountinfo->cz($cardn, $amount, $beizhu1)) {
            $this->success("充值成功");
        } else {
            $this->error("处理失败，请检查用户信息是否正确");
        }
    }
    function overdraw(){
        $this->display();
    }
    function overdrawserver() {

        $amountinfo = D("Amountinfo");
        $user = d("user");
        $username = $_POST['username'];
        $cardn = $_POST['cardn'];
        $amount = $_POST['amount'];
        $beizhu1 = $_POST['beizhu1'];
        if (!($user->where(array("cardn" => $cardn, "username" => $username))->find())) {
            $this->error("用户信息输入错误");
        }

        if ($amountinfo->tz($cardn, $amount, $beizhu1)) {

            $this->success("透支成功");
        } else {

            $this->error("处理失败，请检查用户信息是否正确");
        }
    }

    /*     * ***********会员等级设定***** */

    function grade() {

        $info = d('info');
        $grade = $info->where(array("type" => 4))->order('name desc')->select();
        $this->assign('grade', $grade);
        $this->display();
    }

    function gradeadd() {

        $this->display();
    }

    function gradedelete() {

        $info = d("info");
        $id = (int) $_GET['id'];
        if ($info->where("id=" . $id)->delete()) {
            $this->success("删除成功");
        }
    }

    function gradeinsert() {
        $info = d("info");
        $_POST['type'] = 4;
        $info->create();
        if ($info->add()) {
            $this->success("添加成功");
        }
    }

    function touzi() {

        $info = d("info");
        $data = $info->where(array("type" => 3))->select();
        $this->assign("data", $data);
        $this->display();
    }

    function deletetouzi() {

        $info = d("info");
        if ($result = $info->where(array("id" => $_GET['id']))->delete()) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }

    function touziserver() {

        $info = d("info");
        if (isset($_POST['touzie'])) {
            $amount = $_POST['touzie'];
            if ($result = $info->add(array("type" => 3, "value2" => $amount))) {
                $this->success("添加成功");
            } else {
                $this->error("添加失败");
            }
        }
    }

    function fanli() {

        $info = d('info');
        $fanxian = $info->where(array("type" => 2))->order('name desc')->select();
        $this->assign('fanxian', $fanxian);
        $this->display();
    }

    function fanlidelete() {

        $info = d("info");
        $id = (int) $_GET['id'];
        if ($info->delete($id)) {
            $this->success("删除成功");
        }
    }

    function fanliadd() {

        $info = d('info');

        if (isset($_POST['value2']) && ($_POST['value2'] != "")) {

            if ($num = $info->where(array("type" => 2, "value2" => $_POST['value2']))->count()) {
                $this->error("您添加的返现金额已经存在");
            }
            $_POST["type"] = 2;
            $info->create();

            if ($fanxian = $info->add()) {

                $this->success("增加成功");
            }
        }
    }

    function jf() {
        
        $info = d("info");
        $row = $info->where(array("type" => 5))->find();
        $this->assign("jf", $row['value1']);
        $this->display();
    }

    function jfupdate() {
        
        $info = d("info");
        if ($row = $info->where(array("type" => 5))->save(array("value1" => $_POST['jf']))) {
            $this->success("设定成功");
        } else {
            $this->ERROR("设定失败");
        }
    }
    function  dcz(){
        $this->display();
    }
    function cz(){
        $this->display();
    }

}