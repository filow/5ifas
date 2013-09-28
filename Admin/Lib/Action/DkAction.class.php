<?php

class DkAction extends CommonAction {

    function index() {
        $date_time_array = getdate(time());
        $todayn = $date_time_array["wday"];

        $j = 0;
        $zhongwen1 = array("日", "一", "二", "三", "四", "五", "六");
        $zhongwen = array("", "一", "二", "三", "四", "五", "六", "日");

        $wnum = 8;
        if ($todayn == 0) {
            $zhongwen = $zhongwen1;
            $wnum = 7;
        }
        for ($i = $todayn; $i < $wnum; $i++) {

            $data[$j] = array("data" => date("Ymd", time() + 86400 * $j), "value" => "星期" . $zhongwen[$i]);
            $j++;
        }
        $this->assign('timedata', $data);
        $this->display();
    }

    function waresearch() {

        $item = $_GET['item'];
        $ware = d("ware");
        $row = $ware->where("w_name like '%" . $item . "%'")->limit('0,6')->select();
      //z();
        $count = count($row);

        foreach ($row as $key => $value) {

            $output.="<div class='pro' id=" . $value['id'] . ">" . $value['w_name'] ."单价：".$value["w_price"]."元". "</div><br/>";
        }
        if ($count > 0) {
            echo $output;
        } else {
            echo "还没有数据";
        }
    }

    function usersearch() {
        $query = array();
        $type = $_GET['type'];
        $item = $_GET['item'];

        $user = d("user");
        $row = $user->where("$type like '%" . $item . "%' and zx=0 and is_bigcustomer=0")->limit("0,5")->select();
        $count = count($row);
        foreach ($row as $key => $value) {
            $output.="<div class='userpro' id=" . $value['id'] . ">卡号：" . $value['cardn'] . "登录名:" . $value['loginname'] . " 姓名：" . $value['username'] . " 电话号码：" . $value['tele'] . " 地址：" . $value['address'] . "余额：" . $value['amount'] . "</div><br/>";
        }
        if ($count > 0) {
            echo $output;
        } else {
            echo "还没有数据";
        }
    }

    function daikouserver() {
        $order = D("Order");
        $beizhu = $_POST['beizhu'];
        $cardn = $_POST['cardn'];
        $zk=$_POST["zk"];
        
        if ($cardn < 0.11) {
            $this->error("没有选取用户");
        }

        $time = $_POST['shijian'];
        $cardesc = $_POST['car'];
        $result =$order->pay($cardesc, $time, $cardn, $beizhu,$zk);
        switch ($result) {
            case 1: $this->error("用户余额不足");break;
            case 2: $this->success("扣费成功");break;
            case 3: $this->error("您没有选取商品");break;
            case 4: $this>error('新建订单失败');break;
            case 5: $this>error('建立订单商品列表失败');break;
            case 6: $this>error('订单建立成功,但用户扣费失败');break;
        }
    }

}