<?php

class UserAction extends UsercommonAction {
    public function _initialize() {
        $c = M('news');
        $u2n = M("user2news");
        $data = $c->where("newstype=1")->select();
        $unreaded_num=0;
        foreach ($data as $key => $value) 
            if (!$u2n->where(array("userid" => $_SESSION['cardn'], "newsid" => $value['id']))->find()) 
                $unreaded_num++;
        $this->assign('unreaded_num',$unreaded_num);
    }
    function index() {
        $user = M("user");
        $amountinfo = M("amountinfo");
        $u_data = $user->find($_SESSION["cardn"]);
        //消费记录
        $order = M("order");
        $sum = $order->where("cardn=" . $_SESSION["cardn"]." and type>0")->sum('amount');
        $u_data["sum"] = $sum;
        //充值记录
        $cz = $amountinfo->where("type=1 and userid=" . $_SESSION["cardn"])->sum('amount');
        $u_data["cz"] = $cz;
        //账单记录
        $bill=M('bill');
        $bill_count=$bill->where(array('customer_name'=>session('loginname'),'abandon'=>0))->sum('price_sum');
        $u_data["bill_count"] = $bill_count;
        $this->assign("u_data", $u_data);
        $this->display();
    }

    function change() {
        $info = M('info');
        $sushe = $info->field("name,value")->where(array("type" => 1))->select();
        $this->assign('sushe', $sushe);

        $user = M('user');
        $data = $user->where(array("cardn" => $_SESSION['cardn']))->find();
        $this->assign("data", $data);
        $this->display();
    }

    function mod() {
        $info = M("info");
        $id = I('id');
        $qq = I('qq');
        $sushelou = $info->field('name')->where(array('type' => 1, 'value' => $_POST['sushel']))->find();
        $_POST["address"] = $sushelou['name'] . $_POST['susheh'];
        $user = M("user");
        if ((isset($_POST['password'])) && ($_POST['password'] != "")) {
            $_POST['password'] = md5(trim($_POST['password']));
        } else {
            unset($_POST['password']);
        }
        $user->create();
        if ($user->save()) {
            $this->success("修改成功");
        } else {
            $this->error("修改失败");
        }
    }

    function newslist() {
        $c = M('news');
        $u2n = M("user2news");
        import("ORG.Util.Page");
        $count = $c->where(array("newstype" => 1))->count();
        $page = new Page($count, 10);
        $show = $page->show();
        $data = $c->where(array("newstype" => 1))->limit($page->firstRow . ',' . $page->listRows)->order('createtime desc')->select();
        foreach ($data as $key => $value) {
            $id = $value['id'];
            if ($row = $u2n->where(array("userid" => $_SESSION['cardn'], "newsid" => $id))->find()) {
                $data[$key]['isreaded'] = 1;
            } else {
                $data[$key]['isreaded'] = 0;
            }
        }
        $this->assign('data', $data);
        $this->assign('show', $page->show());
        $this->display();
    }

    function read() {
        $id = I('id');

        //如果该新闻还没读过的话就标记为已读
        $u2n = M("user2news");
        if (!($row = $u2n->where(array("userid" => $_SESSION['cardn'], "newsid" => $id))->find())) {
            $allu = $u2n->add(array("userid" => $_SESSION['cardn'], "newsid" => $id));
        }

        $news = M("news");
        $data = $news->find($id);

        $data['text'] = $data['text'];
        $data['title'] = htmlspecialchars_decode($data['title']);
        $this->assign('data', $data);
        $this->display();
    }

    function order() {
        $order = M("order");
        $bill = M('bill');
        $user = M("user");
        import("ORG.Util.Page");
        $u_data = $user->field('loginname')->find($_SESSION["cardn"]);

        $query_order = "type!=0 and  cardn=" . $_SESSION["cardn"];
        $query_bill=array('abandon'=>0,'customer_name'=>$u_data['loginname']);
       
        $count_order = $order->where($query_order)->count();
        $count_bill =$bill->where($query_bill)->count();

        $count=$count_order>$count_bill?$count_order:$count_bill;
        $page = new Page($count, 30);
        $show = $page->show();
        //账单的查询
        $data_bill=$bill->where($query_bill)->limit($page->firstRow . ',' . $page->listRows)->order("id desc ")->select();
        //普通订单的查询
        $data_order = $order->where($query_order)->limit($page->firstRow . ',' . $page->listRows)->order("id desc ")->select();
        foreach ($data_order as $key => $value) {
            $info = json_decode($value["info"], true);
            $desn = "";
            foreach ($info as $k => $v) {
                $desn.=$v["w_name"] . ":" . $v["pnum"] . "份|";
            }
            $data_order[$key]["desn"] = $desn;
        }
        $this->assign('data_bill',$data_bill);
        $this->assign("data_order", $data_order);
        $this->assign('user_loginname',$u_data['loginname']);
        $this->assign("show", $show);
        $this->display();
    }
    function order_bill_detail(){
        if(!empty($_GET['orderid'])){
            $bill=M('bill');
            $bill_detail=M('bill_detail');
            $billdata=$bill->where(array('orderid'=>$_GET['orderid']))->field('id')->find();
            if($billdata){
                $bill_detail_data=$bill_detail->where(array('bill_id' => $billdata['id']))->select();
                $this->ajaxreturn($bill_detail_data);
            }
        }
    }
    function th() {
        $order = M("order");
        import("ORG.Util.Page");
        $query = "type =0 and  cardn=" . $_SESSION["cardn"];
        $count = $order->where($query)->count();
        $page = new Page($count, 20);
        $show = $page->show();
        $data = $order->where($query)->limit($page->firstRow . ',' . $page->listRows)->order("id desc ")->select();
        foreach ($data as $key => $value) {
            $info = json_decode($value["info"], true);
            $desn = "";
            foreach ($info as $k => $v) {
                $desn.=$v["w_name"] . ":" . $v["pnum"] . "份|";
            }
            $data[$key]["desn"] = $desn;
        }
        $this->assign("data", $data);
        $this->assign("show", $show);

        $this->display();
    }

    function amount() {
        $user = M("user");
        import("ORG.Util.Page");
        $query = "userid=" . $_SESSION["cardn"];
        $query.=" and type !=3";
        // print_r($query_data);
        $amountinfo = M("Amountinfo");
        $u = M("user");
        $count = $amountinfo->where($query)->count();
        // z();
        $page = new Page($count, 20);
        $show = $page->show();
        $data = $amountinfo->where($query)->order("userid asc ")->limit($page->firstRow . ',' . $page->listRows)->select();
        $search_data = array();
        foreach ($data as $key => $value) {
            $userid = $u->where(array("cardn" => $value['userid']))->field('username,tele,address')->find();
            $value['username'] = $userid['username'];
            $value['address'] = $userid['address'];
            $value['tele'] = $userid['tele'];
            $value['createtime'] = date("Y-m-d H:i:s", $value['createtime']);
            $value['suctime'] = date("Y-m-d H:i:s", $value['suctime']);
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
        }
        $this->assign("data", $search_data);
        $this->assign("show", $show);
        $this->display();
    }

    function tz() {
        for($i=1;$i<=4;$i++){
            $data[$i-1]=$i*50;
        }
        $this->assign('data',$data);
        $this->display();
    }

    function tzserver() {
        $amount = I('amount');
        $cardn = $_SESSION['cardn'];
        $amountinfo = D("Amountinfo");
        $row1 = $amountinfo->where(array("userid" => $_SESSION['cardn'], "type" => 0))->count();
        if ($row1) {
            $this->error("对不起您的上次透支还没处理结束");
        }
        if ($amountinfo->tz($cardn, $amount, "会员自行透支")) {
            $this->success("申请透支成功,客服稍后会去向您收费");
        }
    }

}