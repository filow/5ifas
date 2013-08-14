<?php

class CarAction extends CommonAction {

    function mycar() {

        $w = d("ware");
        $car = $_SESSION['car'];
        $amount = 0;
        $count = 0;
        $data = array();
        $jf = 0;
        unset($car["sum"]);
        foreach ($car as $key => $value) {
            $count+=$value;
            $row = $w->find($key);
            $jf+=$row['price'] * $value;
            $amount+=$row['w_price'] * $value;
            $output['id'] = $key;
            $output['price'] = $row['w_price'];
            $output['name'] = $row['w_name'];
            $output['jf'] = $row['price'];
            $output['pic'] = $row['w_pic'];
            $output['amount'] = $output['price'] * $value;
            $output['num'] = $value;
            $data[] = $output;
        }
        //print_r($data);
        $this->assign('jf', $jf);
        //  print_r($data);
        $this->assign('data', $data);



        if (isset($_SESSION['cardn'])) {
            $cardn = $_SESSION['cardn'];
            $user = D("user");
            $result = $user->field('username,cardn,tele,susheh,jf,sushel')->where(array("cardn" => $cardn))->find();

            $info = D('info');
            $sushe = $info->field("name,value")->where(array("type" => 1, "value" => $result['sushel']))->find();

            $result['sushel'] = $sushe['name'];
            $userdata = $result;

            $this->assign('userdata', $userdata);
        } else {

        }
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

            $data[$j] = array("data" => date("Y-m-d", time() + 86400 * $j), "value" => "星期" . $zhongwen[$i]);
            $j++;
        }


        $info = D('info');
        $sushe = $info->field("name,value")->where(array("type" => 1))->select();
        // print_r($sushe);
        $this->assign('sushe', $sushe);
        $this->assign('timedata', $data);
        $this->display();
    }

    function pay() {
       if($_SESSION["zx"]){
           $this->error("请联系管理员，您的账号被注销");
       }
        if (!$_SESSION["car"]["sum"])
            $this->error("您没有选购商品");
        $order = D('Order');
        $if = d("info");
        $ware = d("ware");
        $user = d("user");
        $o2p = d("order2product");
        $input_jf = (int) $_POST['jf']; //用户输入的积分
        $sid = (int) $_POST['sushel'];
        $row = $if->where(array("type" => 1, "value" => $sid))->find();
        $sushel = $row['name'];
        $_POST['createtime'] = time();
        $_POST['delivertime'] = $_POST['shijian'];
        $_POST['deliveraddress'] = $sushel . $_POST['susheh'];
        $_POST['delivername'] = $_POST['name'];
        $_POST['delivertele'] = $_POST['tele'];
        $_POST["operator"] = $_SESSION["loginname"];
        $_POST['zt'] = 1;
        //不匿名type=1 匿名type=2 退款type=0
        if ($_POST["pay_type"]==2) {
            $_POST['type'] = 2;
        } else {
            $_POST['type'] = 1;
        }

        if (isset($_SESSION["cardn"])) {
            $_POST["cardn"] = $_SESSION["cardn"];

        }
        if (!isset($_SESSION["cardn"])) {

            $_POST['type'] = 2;
        }
        if ((trim($_POST['shijian'] == "")) || (trim($_POST['susheh'] == "")) || (trim($_POST['sushel'] == 0)) || (trim($_POST['name'] == "")) || (trim($_POST['tele'] == ""))) {
            $this->error("信息不完整！");
        }
        $car = $_SESSION["car"];
        $result = array();
        unset($car["sum"]);
        $sum = 0; //订单总额
        $jf_sum = 0; //积分总额
        foreach ($car as $key => $value) {
            $ware_li = $ware->find($key);
            $sum+=$ware_li["w_price"] * $value;
            $jf_sum+=$ware_li["price"] * $value;
            $result[$ware_li["shop_id"]][] = array("w_name" => $ware_li["w_name"], "w_price" => $ware_li["w_price"], "pnum" => $value, "shop_id" => $ware_li["shop_id"], "productid" => $ware_li["id"]);
            $result[$ware_li["shop_id"]]["amount"]+=$ware_li["w_price"] * $value;

            if ($ware_li["send_sms"])
                $result[$ware_li["shop_id"]]["send_sms"] = 1;
        }
        $input_jf = $input_jf <= $jf_sum ? $input_jf : $jf_sum; //如果输入的积分不多于能用的积分则使用输入的积分，否则使用能用的积分
        /*         * *****如果是登录用户，则需要判断jf是足够，并且读取钱换分的比例**************************** */
        $u_data = array();
        // echo $sum;
        if ($_POST["type"] == 1) {
            $dj = $if->where(array("type" => 5))->find();
            $jf_money = $dj['value1']; //积分不足时，每个积分抵多少元
            $u_data = $user->find($_SESSION["cardn"]);
            $u_jf = $u_data["jf"];
            $input_jf = $input_jf <= $u_jf ? $input_jf : $u_jf;
            //  echo $jf_sum-$input_jf;
            $sum = $sum + ($jf_sum - $input_jf) * $jf_money;
            if ($sum > $u_data["amount"])
                $this->error("您的余额不足，请在个人中心申请透支");
        }
        $shop = D("shop");
        $error_msg = "";
        foreach ($result as $key => $value) {
            $shop_data = $shop->find($key);
            $_POST["order_id"] = $orderid = generateId($key, $_POST["sushel"]);
            $_POST["shop_id"] = $key;
            $_POST["amount"] = $value["amount"];
            if ($shop_data["service_price"] >= $value["amount"]) {
                $error_msg.=$shop_data["shop_name"] . "不满足起送价格" . $shop_data["service_price"];
                $sum = $sum - $_POST["amount"];
                continue;
            }

            $msg = $_POST["amount"] . "元 " . $_POST["delivername"] . ":" . $_POST["deliveraddress"] . " " . $_POST["delivertele"];
            if (isset($_SESSION["cardn"])&&$_POST["pay_type"]!=2) {
                $msg.="已付费 ";
            } else {
                $msg.="货到付款 ";
            }


            $msg.="订单详情:";
            unset($value["amount"]);
            $s_sum = 0; //商家总数量
            foreach ($value as $k => $v) {

                $s_sum+=$v["pnum"];
            }
            if ($shop_data["service_num"] > $s_sum) {
                $error_msg.=$shop_data["shop_name"] . " 不满足起送数量" . $shop_data["service_num"];
                $sum = $sum - $value["amount"];
                continue;
            }
            if ($value["send_sms"]) {
                unset($value["send_sms"]);
                foreach ($value as $k => $v) {
                    $msg.=$v["w_name"] . ":" . $v["pnum"] . "份|";
                }
                $msg.=" 送货时间：".$_POST["delivertime"]." ".$_POST["hour"].":".$_POST["minute"];
                $msg.="备注： " . $_POST["beizhu"];
                sendSms($shop_data["tele"],  $msg );
               sendSms($_POST["tele"], "您的订单已生效,若您购买了优惠券，请在消费时出示订单号.您可登陆www.5ifas.com查询你的消费记录。艾星网络祝您生活愉快!");
            }


            $_POST["info"] = ch_json_encode($value);
            $order->create();
            $order->add();
            /*             * ***插入到order2product中********* */
            /**
             * order2product type=1 购买 type=2是退货
             *
             */
            foreach ($value as $w_k => $w_v) {
                $w_v["order_id"] = $orderid;
                $w_v["type"] = 1;
                $w_v["cardn"] = $_SESSION["cardn"];
                $o2p->add($w_v);
            }
        }
        /*         * *******************积分的计算开始**************************** */
        if ($_POST["type"] == 2) {
             $_SESSION["car"] = array();
            if ($error_msg == "") {
                $this->success("下单成功");
            } else {
                 echo "<script> alert('" . $error_msg . "请重新购买') </script>";
                $this->success($error_msg);
            }
            exit;
        };
        /*         * ***********先计算每消费一元产生的积分***************** */
        /*         * **********
         * 积分 type=1得到积分 type=2消费积分
         */

        if ($user->save(array("id" => $_SESSION["cardn"], "amount" => $u_data["amount"] - $sum, "jf" => $u_data["jf"] - $input_jf + $sum * $u_data["djjf"]))) {
            /*             * ******************************更新jf表****************************** */
            //z();
            $jf = D("jf");
            if ($u_data["djjf"] >= 0.0001)
                $jf->add(array("cardn" => $_SESSION["cardn"], "type" => 1, "amount" => $sum * $u_data["djjf"], "desn" => date("Y-m-d H:i", time()) . "购买商品得到的积分"));
            if ($input_jf >= 0.00001)
                $jf->add(array("cardn" => $_SESSION["cardn"], "type" => 2, "amount" => $input_jf, "desn" => date("Y-m-d H:i", time()) . "购买商品使用的积分"));
             $_SESSION["car"] = array();
            // z();
        }
        
        if ($error_msg == "") {
            $this->success("下单成功");
        } else {
            echo "<script> alert('" . $error_msg . "请重新购买') </script>";
            $this->success("下单成功");
        }
    }

}