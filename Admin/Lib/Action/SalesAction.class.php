<?php

class SalesAction extends CommonAction {

    function index() {
        $ware = D("ware");
        $o2p = d("order2product");
        $shop = d("shop");
        $shop_name = 0;
        if (isset($_GET["shop_name"]) && $_GET["shop_name"] != "") {
            $shop_name = $_GET["shop_name"];
            $shop_data = $shop->where("shop_name like '%$shop_name%'")->find();
            //  z();
            $shop_id = $shop_data["id"];
            unset($_GET["shop_name"]);
        }
        import("ORG.Util.Page");
        if ($_GET["left_time"] > 0) {
            $left_time = $_GET["left_time"];
            unset($_GET["left_time"]);
        }
        if ($_GET["right_time"] > 0) {
            $right_time = $_GET["right_time"];
            unset($_GET["right_time"]);
        }
        $query_data = getQuery();
        $query = $query_data["like_query"];
        if ($shop_id)
            $query.="  and  shop_id=" . $shop_id;
        $count = $ware->where($query)->count();

        $page = new Page($count, 50);
        $show = $page->show();
        $data = $ware->where($query)->limit($page->firstRow . ',' . $page->listRows)->select();
        // z();
        $export_data[] = array("商品ID", "商品名", "销量", "销售额");
        $query1 = "1=1"; //
        if ($left_time) {
            $query1.=" and SUBSTRING(order_id,2,12) >=" . $left_time;
        }
        if ($right_time) {
            $query1.=" and SUBSTRING(order_id,2,12) <=" . $right_time;
        }
        foreach ($data as $key => $value) {
            $query2 = $query1 . " and productid=" . $value["id"];
            $sum_data= $o2p->field("sum(pnum) as sum,sum(pnum*w_price) as sum1")->where($query2)->find();
         //   z();
            // z();
            //  print_r($sum);
            $sum = $sum_data["sum"];
            $sum1 = $sum_data["sum1"];
            $data[$key]["sum"] = $sum;
            $data[$key]["sum1"] = $sum1;
            $export_data[] = array($value["id"], $value["w_name"], $sum, $sum1);
        }
        $this->assign("data", $data);
        $this->assign("query", $query_data["array"]);
        $this->assign("show", $show);
        $this->assign("export_data", json_encode($export_data));
        $this->display();
    }

}