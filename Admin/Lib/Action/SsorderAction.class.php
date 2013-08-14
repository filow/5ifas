<?php
class SsorderAction extends ScommonAction{
    function index() {
        $order = d("order");
        import("ORG.Util.Page");
        
        $getdata = $_GET;
        
        $query_data = getQuery($getdata);
        $query = $query_data["like_query"];
// echo $query;
        $query.=" and type >0  and shop_id=" . $_SESSION["shop_id"];

        $count = $order->where($query)->count();
        $page = new Page($count, 20);
        $show = $page->show();
        $data = $order->where($query)->limit($page->firstRow . ',' . $page->listRows)->order("id desc ")->select();
        $export_data[] = array("订单号", "用户卡号", "收货人", "电话号码", "商品详情", "金额","优惠金额", "送货地址", "送货时间", "是否货到付款", "备注", "操作人");
        foreach ($data as $key => $value) {
            $info = json_decode($value["info"], true);
            $desn = "";
            $data[$key]["type"] = $type = $value["type"] == 1 ? "否" : "是";
            foreach ($info as $k => $v) {
                $desn.=$v["w_name"] . ":" . $v["pnum"] . "份|";
            }
            $data[$key]["desn"] = $desn;
            $export_data[] = array($value["order_id"], $value["cardn"], $value["delivername"], $value["delivertele"], $desn, $value["amount"], $value["youhui"], $value["deliveraddress"], $value["delivertime"]." ".$value["hour"]." :".$value["minute"], $type, $value["beizhu"], $value["operator"]);
        }
// print_r($data);
        $this->assign("data", $data);
        $this->assign("show", $show);
        $this->assign("query", $query_data["array"]);
        $this->assign("export_data", json_encode($export_data));
        $this->display();
    }
    function seetuikuan() {
        $order = d("order");
        import("ORG.Util.Page");
        
        $getdata = $_GET;
      
        $query_data = getQuery($getdata);
        $query = $query_data["like_query"];
// echo $query;
        $query.=" and type=0  and shop_id=" . $_SESSION["shop_id"];

        $count = $order->where($query)->count();
        $page = new Page($count, 20);
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
}
 
?>
