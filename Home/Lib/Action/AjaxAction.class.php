<?php

class AjaxAction extends Action {

    function select_area() {
        $Area = D("Area");
        $id = (int) $_GET["area_id"];
        $data = $Area->find($id);
        $area_path = $data["area_path"];
        $ban_id = $data["ban_id"];
        $level_num = count($level_data = explode("-", $area_path)); //计算层级
        $result = "";
        for ($i = 0; $i < $level_num; $i++) {
            $area_path = "0";
            for ($j = 1; $j < $i + 1; $j++) {
                $area_path.="-" . $level_data[$j];
            }
            $row = $Area->where("area_path=" . "'$area_path' and ban_id=" . $ban_id)->select();

            $str = '<select class="span2 area" onchange="area_select(this.value,true)" id="select' . $i . '">';
            $str.='<option value="0">选择</option>';
            foreach ($row as $key => $value) {
                $selected = "";
                if ($value["id"] == $level_data[$i + 1] || $value["id"] == $id)
                    $selected = "selected";
                $str.="<option value=" . $value['id'] . " " . $selected . " >" . $value["area_name"] . "</option>";
            }
            $str.="</select>";
            $result.=$str;
        }
        /*         * ******
         * 下面是判断有无子级元素并取出来
         */
        $data = $Area->where("pid=" . $id)->select();
        if ($data) {
            $str = '<select class="span2 area" onchange="area_select(this.value,true)" id="select' . $level_num . '">';
            $str.='<option value="0">选择</option>';
            foreach ($data as $key => $value) {
                $str.="<option value=" . $value['id'] . " >" . $value["area_name"] . "</option>";
            }
            $str.="</select>";
            $result.=$str;
        }
        echo $result;
    }

    function rating_shop() {
        $rate = (int) $_GET['rate'];
        $shop_id = $_GET["shop_id"];
        $shop_id = explode("_", $shop_id);
        $shop_id = (int) $shop_id[1];
        if (!isset($_SESSION['cardn'])) {
            echo "111";
            exit;
        }
        $cardn = $_SESSION['cardn'];
        $r = d("ratingShop");
        if ($r->where(array("userid" => $cardn, "shop_id" => $shop_id))->find()) {
            echo "222";
            exit;
        }
        if ($r->add(array("userid" => $cardn, "shop_id" => $shop_id, "rating" => $rate))) {
            $s = d("shop");
            $row = $s->field("rating_num,rating_service")->where("id=" . $shop_id)->find();
            $count = $row['rating_num'];
            $orate = $row['rating_service'];
            $rate = ($count * $orate + $rate) / ($count + 1);

            $s->where("id=" . $shop_id)->save(array("rating_service" => $rate, "rating_num" => $count + 1));
            echo "###";
        }
    }

    function rating_ware() {
        $rate = (int) $_GET['rate'];
        $ware_id = $_GET["ware_id"];
        $ware_id = explode("_", $ware_id);
        $ware_id = (int) $ware_id[1];
        if (!isset($_SESSION['cardn'])) {
            echo "111";
            exit;
        }
        $cardn = $_SESSION['cardn'];
        $r = d("ratingWare");
        if ($r->where(array("userid" => $cardn, "ware_id" => $ware_id))->find()) {
            echo "222";
            exit;
        }
        if ($r->add(array("userid" => $cardn, "ware_id" => $ware_id, "rating" => $rate))) {
            $s = d("ware");
            $row = $s->field("rate,ratecount")->where("id=" . $ware_id)->find();
            $count = $row['ratecount'];
            $orate = $row['rate'];
            $rate = ($count * $orate + $rate) / ($count + 1);

            $s->where("id=" . $ware_id)->save(array("rate" => $rate, "ratecount" => $count + 1));
            echo "###";
        }
    }

    /*     * *******************************判断是否支持****************************** */

    function checkbuy() {
        $id = (int) $_GET["id"];
        $num = (int) $_GET["num"];
        $ware = D("ware");
        $data = $ware->find($id);
        $w_price = $data["w_price"];
        if ($data["can_buy"] == 0) {
            echo "##**";
            exit;
        }

        if (!isset($_SESSION["cardn"]) && $data["onlinepay"] == 1) {
            echo "###";
            exit;
        }
        $sushe = json_decode($data["info"], true);
        // print_r($sushe);
        if (isset($_SESSION["sushel"]) && !in_array($_SESSION["sushel"], $sushe)) {
            echo "***";
            exit;
        }
        if ($_SESSION["car"][$id]) {
            $_SESSION["car"][$id] = $_SESSION["car"][$id] + $num;
        } else {
            $_SESSION["car"][$id] = $num;
        }
        $_SESSION["car"]["sum"]+=$num;
        $_SESSION["car_money"]+=$num * $w_price;
        echo $_SESSION["car"]["sum"]."#".$_SESSION["car_money"];
    }

    /*     * ********************数量**************************、
     *
     */

    function add() {
        $id = (int) $_GET["id"];
        $num = (int) $_GET["num"];
        $ware = D("ware");
        $data = $ware->find($id);
        $w_price = $data["w_price"];
        $_SESSION["car"][$id] = $_SESSION["car"][$id] + $num;
        $_SESSION["car_money"]+=$num * $w_price;
        $_SESSION["car"]["sum"]+=$num;
        echo $_SESSION["car"]["sum"]."#".$_SESSION["car_money"];
    }

    /*     * **********************减少数量*********************** */

    function minus() {
        $id = (int) $_GET["id"];
        $num = (int) $_GET["num"];
        $ware = D("ware");
        $data = $ware->find($id);
        $w_price = $data["w_price"];
        if ($num >= $_SESSION["car"][$id]) {
            $num = $_SESSION["car"][$id];
        }
         $_SESSION["car_money"]-=$num * $w_price;
         $_SESSION["car"][$id] = $_SESSION["car"][$id] - $num;
         $_SESSION["car"]["sum"]-=$num;
        echo $_SESSION["car"]["sum"]."#".$_SESSION["car_money"];
    }

    function remove() {
        $id = (int) $_GET["id"];
         $ware = D("ware");
        $data = $ware->find($id);
        $w_price = $data["w_price"];
        $_SESSION["car"]["sum"]-=$_SESSION["car"][$id];
        $_SESSION["car_money"]-=$_SESSION["car"][$id] * $w_price;
        unset($_SESSION["car"][$id]);
        echo "###";
    }

    function end() {
        $id = (int) $_GET["id"];
        $num = (int) $_GET["num"];
        $_SESSION["car"][$id] = $num;
        $sum = 0;
        foreach ($_SESSION["car"] as $key => $value) {
            if ($key == "sum")
                continue;
            $sum+=$value;
        }
        $_SESSION["car"]["sum"] = $sum;
    }

    function check() {
        $w = d("ware");
        $id = (int) $_GET['id'];
        $sid = (int) $_GET['sid'];
        $row = $w->find($id);
        $sushe = json_decode($row['info'], true);
        //如果是积分产品不用判断
        if ($row['w_type'] == 1) {
            echo -$id . "#" . ".color" . $id;
            exit;
        }
        if (in_array($sid, $sushe)) {
            echo -$id . "#" . ".color" . $id;
        } else {
            echo $id . "#" . ".color" . $id;
        }
    }

    /*     * **************************collect商品**************************** */

    function collect() {
        $id = $_GET["productid"];
        $collect = d("collect");
        if (!isset($_SESSION["loginname"])) {
            echo "###";
            exit;
        }
        $uid = $_SESSION["id"];
        if ($collect->where(array("userid" => $uid, "collect_id" => $id))->find()) {
            echo "***";
            exit;
        } else {
            if ($collect->add(array("userid" => $uid, "collect_id" => $id))) {
                echo "111";
            } else {
                echo "222";
            }
        }
    }

    function collect_shop() {
        $id = $_GET["shop_id"];
        $collect = d("collectShop");
        if (!isset($_SESSION["loginname"])) {
            echo "###";
            exit;
        }
        $uid = $_SESSION["id"];
        if ($collect->where(array("userid" => $uid, "shop_id" => $id))->find()) {
            echo "***";
            exit;
        } else {
            if ($collect->add(array("userid" => $uid, "shop_id" => $id))) {
                echo "111";
            } else {
                echo "222";
            }
        }
    }

    function share() {
        $share = D("share");
        $user = d("user");
        $info = D("info");
        $jf = D("jf");
        if (!isset($_SESSION["cardn"])) {
            exit;
        }
        $share_url = $_GET["share_url"];
        if (!$share->where("cardn=" . $_SESSION["cardn"] . " and share_url='" . $share_url . "'")->find()) {

            if ($share->add(array("cardn" => $_SESSION["cardn"], "share_url" => $share_url))) {
                $info = $info->where("type=7")->find();
                $add_jf = $info["value2"];
                $user->where("cardn=" . $_SESSION["cardn"])->setInc("jf", $add_jf);
                $jf->add(array("cardn" => $_SESSION["cardn"], "amount" => $add_jf, "type" => 1, "desn" => "分享页面得到的积分"));
            }
        }
    }

}