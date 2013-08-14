<?php

class SorderAction Extends CommonAction {
    /*
     * 最好的权限验证是传入sign验证密钥，加密解密
     */

    function index() {
        /*         * **
         * 查询所有商家
         */
        $ban_id = (int) $_GET["ban_id"];
        $ban = D("ban");
        $ban_data = $ban->select();
        $this->assign("ban_data", $ban_data);
        $shop = D("shop");
        $query = "1=1";
        if ($ban_id)
            $query.=" and  ban_id=" . $ban_id;
        $shop_data = $shop->where($query)->select();
        // z();
        $this->assign("shop_data", $shop_data);
        // print_r($shop_data);

        $order = d("order");
        import("ORG.Util.Page");
        if (isset($_GET["shopid"]))
            $shop_id = (int) $_GET["shopid"];

        $getdata = $_GET;
        unset($getdata["shopid"], $getdata["ban_id"]);
        $query_data = getQuery($getdata);
        $query = $query_data["like_query"];
        // echo $query;
        $query.=" and type > 0 ";
        if ($shop_id)
            $query.=" and shop_id=" . $shop_id;
        $count = $order->where($query)->count();

        $page = new Page($count, 40);
        $show = $page->show();
        $data = $order->where($query)->limit($page->firstRow . ',' . $page->listRows)->order("id desc ")->select();
        $price_sum=0;
        $export_data[] = array("订单号", "用户卡号", "收货人", "电话号码", "商品详情", "金额", "优惠金额", "送货地址", "送货时间", "是否货到付款", "备注", "操作人");
        foreach ($data as $key => $value) {
            $info = json_decode($value["info"], true);
            $desn = "";
            $data[$key]["type"] = $type = $value["type"] == 1 ? "否" : "是";
            $data[$key]['type1']=$value["type"];
            foreach ($info as $k => $v) {
                $desn.=$v["w_name"] . ":" . $v["pnum"] . "份|";
            }
            $price_sum+=$value['amount'];
            $data[$key]["desn"] = $desn;
            $export_data[] = array($value["order_id"], $value["cardn"], $value["delivername"], $value["delivertele"], $desn, $value["amount"], $value["youhui"], $value["deliveraddress"], $value["delivertime"]." ".$value["hour"]." :".$value["minute"], $type, $value["beizhu"], $value["operator"]);

        }
// print_r($data);
        $this->assign("data", $data);
        $this->assign("show", $show);
        $this->assign("query", $query_data["array"]);
        $this->assign("export_data", json_encode($export_data));
        $this->assign('price_sum',$price_sum);
        $this->display();
    }

    /*     * ***********退款订单查看*************** */

    function seetuikuan() {

        $ban_id = (int) $_GET["ban_id"];
        $ban = D("ban");
        $ban_data = $ban->select();
        $this->assign("ban_data", $ban_data);
        $shop = D("shop");
        $query = "1=1";
        if ($ban_id)
            $query.=" and  ban_id=" . $ban_id;
        $shop_data = $shop->where($query)->select();
        $this->assign("shop_data", $shop_data);
        $order = d("order");
        import("ORG.Util.Page");
        if (isset($_GET["shopid"]))
            $shop_id = (int) $_GET["shopid"];

        $getdata = $_GET;
        unset($getdata["shopid"], $getdata["ban_id"]);
        $query_data = getQuery($getdata);
        $query = $query_data["like_query"];
// echo $query;
        $query.=" and type=0 ";
        if ($shop_id)
            $query.=" and shop_id=" . $shop_id;

        $count = $order->where($query)->count();
        $page = new Page($count, 50);
        $show = $page->show();
        $data = $order->where($query)->limit($page->firstRow . ',' . $page->listRows)->order("id desc ")->select();
        $export_data[] = array("订单号", "用户卡号", "收货人", "电话号码", "退款商品详情", "退款金额", "送货地址", "送货时间", "备注", "操作人");
        foreach ($data as $key => $value) {
            $info = json_decode($value["info"], true);
            $desn = "";
            $data[$key]["type"] = $type = $value["type"] == 1 ? "否" : "是";
            foreach ($info as $k => $v) {
                $desn.=$v["w_name"] . ":" . $v["pnum"] . "份|";
            }
            $data[$key]["desn"] = $desn;
            $export_data[] = array($value["order_id"], $value["cardn"], $value["delivername"], $value["delivertele"], $desn, $value["amount"], $value["deliveraddress"], $value["delivertime"]." ".$value["hour"]." :".$value["minute"], $value["beizhu"], $value["operator"]);
        }
        // print_r($data);
        $this->assign("data", $data);
        $this->assign("show", $show);
        $this->assign("query", $query_data["array"]);
        $this->assign("export_data", json_encode($export_data));
        $this->display();
    }

    function tuikuan() {
        $id = (int) $_GET["id"];
        $order = d("order");
        $data = $order->find($id);
        if ($order->where("order_id='" . $data["order_id"] . "' and type=0")->find()) {
           // $this->error("该订单已经退款过，请勿重复操作");
        }
        $data["info"] = json_decode($data["info"], TRUE);
        //print_r($data);
        $this->assign("data", $data);
        //print_r($data);
        $this->display();
    }

    function tuikuanserver() {
        $id = (int) $_POST["id"];
        $order = D("order");
        $o2p = D("order2product");
        $order_data = $order->where("id=" . $id)->find();
		$type=$order_data["type"];
        $n_order_data = $order_data;
        $o_info = json_decode($order_data["info"], true);
        $n_info = $o_info;
        $n_amount = 0;
        $t_info; //退款插入的info

        foreach ($o_info as $key => $value) {
            $productid = $value["productid"];
            if (isset($_POST[$productid])) {
                $n_pnum = $value["pnum"] - (int) $_POST[$productid];
                if ($n_pnum <= 0)
                    $n_pnum = 0;
                if ($n_pnum) {
                    $n_info[$key]["pnum"] = $n_pnum;
                    $o2p->where(array("order_id" => $order_data["order_id"], "productid" => $productid))->setField("pnum", $n_pnum);
                } else {
                    unset($n_info[$key]);
                    $o2p->where(array("order_id" => $order_data["order_id"], "productid" => $productid))->delete();
                }
                $value["pnum"] = (int) $_POST[$productid];
                $t_info[] = $value;
            }
        }
		
        foreach ($n_info as $key => $value) {
            $n_amount+=$value["w_price"] * $value["pnum"];
        }
        $add_amount = $order_data["amount"] - $n_amount; //计算要退款的金额
        if ($n_amount > 0) {
            $n_order_data["amount"] = $n_amount;
            $n_order_data["info"] = json_encode($n_info);
            unset($n_order_data["id"]);
            $order->where("id=" . $id)->save($n_order_data);
        } else {
            $order->where("id=" . $id)->delete();
        }
        /* 插入退款商品************** */
        $t_order_data = $order_data;
        unset($t_order_data["id"]);
        $t_order_data["info"] = json_encode($t_info);
        $t_order_data["type"] = 0;
        $t_order_data["amount"] = $add_amount;

        $t_order_data["operator"] = $_SESSION["username"];
        $t_order_data["createtime"] = time();

        $order->add($t_order_data);

        /*         * *****更改用户信息************************ */
		if($type==1){
			$user = D("user");
			$user_data = $user->where("cardn=" . $order_data["cardn"])->find();
			$user->where("cardn=" . $order_data["cardn"])->setField("amount", $user_data["amount"] + $add_amount);
		}
        $this->success("退款成功", "index/index");
    }

    function export500(){
        $ban_id = (int) $_GET["ban_id"];
        $ban = D("ban");
        $ban_data = $ban->select();
        $this->assign("ban_data", $ban_data);
        $shop = D("shop");
        $query = "1=1";
        if ($ban_id)
            $query.=" and  ban_id=" . $ban_id;
        $shop_data = $shop->where($query)->select();

        $this->assign("shop_data", $shop_data);

        $order = d("order");
        if (isset($_GET["shopid"]))
            $shop_id = (int) $_GET["shopid"];

        $getdata = $_GET;
        unset($getdata["shopid"], $getdata["ban_id"]);
        $query_data = getQuery($getdata);
        $query = $query_data["like_query"];
        // echo $query;
        $query.=" and type > 0 ";
        if ($shop_id)
            $query.=" and shop_id=" . $shop_id;

        $data = $order->where($query)->limit(500)->order("id desc ")->select();
        $export_data[] = array("订单号", "用户卡号", "收货人", "电话号码", "商品详情", "金额", "优惠金额", "送货地址", "送货时间", "是否货到付款", "备注", "操作人");
        foreach ($data as $key => $value) {
            $info = json_decode($value["info"], true);
            $desn = "";
            $data[$key]["type"] = $type = $value["type"] == 1 ? "否" : "是";
            $data[$key]['type1']=$value["type"];
            foreach ($info as $k => $v) {
                $desn.=$v["w_name"] . ":" . $v["pnum"] . "份|";
            }
            $data[$key]["desn"] = $desn;
            $export_data[] = array($value["order_id"], $value["cardn"], $value["delivername"], $value["delivertele"], $desn, $value["amount"], $value["youhui"], $value["deliveraddress"], $value["delivertime"]." ".$value["hour"]." :".$value["minute"], $type, $value["beizhu"], $value["operator"]);
        }
        custom_output(json_encode($export_data),"Sorder");
    }

}

?>
