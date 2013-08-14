<?php

class ShopAction extends CommonAction {

    function show_area($aid, $ban_id) {
        $result = array();
        $Area = D("Area");
        if ($aid == 0) {
            $result[] = $Area->where(" ban_id=" . $ban_id . " and pid=0 ")->select();

            return $result;
            exit;
        }

        $data = $Area->find($aid);
        $area_path = $data["area_path"];
        $level_num = count($level_data = explode("-", $area_path)); //计算层级
        $top_id = $level_data[1]; //顶级分类
        $top_data = $Area->where("pid=0 and ban_id=" . $ban_id)->select();
        foreach ($top_data as $key => $value) {
            if ($value["id"] == $level_data[1] || $value["id"] == $aid) {
                $top_data[$key]["is_active"] = 1;
            } else {
                $top_data[$key]["is_active"] = 0;
            }
        }
        $result[] = $top_data;
        for ($i = 1; $i < $level_num; $i++) {
            $area_path = "0";
            for ($j = 1; $j < $i + 1; $j++) {
                $area_path.="-" . $level_data[$j];
            }

            $row = $Area->where("area_path=" . "'$area_path' and ban_id=" . $ban_id)->select();
            foreach ($row as $key => $value) {
                if ($value["id"] == $level_data[$i + 1] || $value["id"] == $aid) {
                    $row[$key]["is_active"] = 1;
                } else {
                    $row[$key]["is_active"] = 0;
                }
            }

            //  z();
            $result[] = $row;
        }
        $row = $Area->where("pid=" . $aid . " and ban_id=" . $ban_id)->select();
        $result[] = $row;
        /*
          foreach ($result as $key => $value) {
          print_r($value);
          echo "<br><br><br>";
          }

         */
        return $result;
    }

    function index() {
        $area = D("Area");
        $aid = (int) $_GET["aid"];
        if (!$aid) {
            $aid = 0;
        }
        $ban_id = (int) $_GET["banid"];
        //$this->show_area($aid, $ban_id);
        //print_r($this->show_area($aid,$ban_id));
        $this->assign("show_area", $this->show_area($aid, $ban_id));

        $w = D("ware");
        $query = "1=1 and ";
        if ($aid > 0 && $area_data = $area->find($aid)) {
            $area_path = $area_data["area_path"];
            $area_path.="-" . $aid;
            $shop_area_data = $area->where("left(area_path," . strlen($area_path) . ")='" . $area_path . "'")->select();
            $query.="( area_id=" . $aid . " or "; //不要忘了挂在本级的产品
            foreach ($shop_area_data as $key => $value) {
                $query.=" area_id=" . $value["id"] . " or ";
            }
            $query.=" 1=3 ) and ";
        }
        $this->getArea($ban_id);
        $now_date = date("H:i", time());
        $now_date = explode(":", $now_date);
        $now_h = (int) $now_date[0];
        $now_m = (int) $now_date[1];
        $query .= "ban_id=" . $ban_id . " ";
        if ($_GET["tuijian"])
            $query.=" and is_hot =1";
        $order = "shop_order desc ,";
        if ($_GET['rate'])
            $order = "rating_service desc , ";
        if ($_GET['price'])
            $order = "service_price  desc , ";
        $order.="id asc";
        $shop = D("shop");
        import("ORG.Util.Page");
        $count = $shop->where($query)->count();
        $page = new Page($count, 15);
        $show = $page->show();
        $data = $shop->where($query)->limit($page->firstRow . ',' . $page->listRows)->order($order)->select();
        // z();
        foreach ($data as $key => $value) {
            if ($w->where("shop_id=" . $value["id"] . " and dz=1")->find())
                $data[$key]["dz"] = 1;
            if ($w->where("shop_id=" . $value["id"] . " and dz=0")->find())
                $data[$key]["jc"] = 1;
            $s_date = explode(":", $value["start_time"]);
            $e_date = explode(":", $value["end_time"]);
            if (($s_date[0] < $now_h || ($s_date[0] == $now_h && $s_date[1] < $now_m)) && ($e_date[0] > $now_h || ($e_date[0] == $now_h && $e_date[1] > $now_m))) {
                $data[$key]["can_buy"] = 1;
            } else {
                $data[$key]["can_buy"] = 0;
            }
            $data[$key]["shop_desn"] = str_replace('"', "'", $data[$key]["shop_desn"]);
        }
        if (isset($_GET["canbuy"])) {
            foreach ($data as $key => $value) {
                if ($value["can_buy"] == 0)
                    unset($data[$key]);
            }
        }
        $this->assign('data', $data);
        $this->assign("show", $show);
		$this->inews();
		$this->order();

        $this->display();
    }

    function index1() {
        $area = D("Area");
        $aid = (int) $_GET["aid"];
        $ban_id = (int) $_GET["banid"];
        //$this->show_area($aid, $ban_id);
        if (!$aid) {
            $aid = 0;
        }
        $this->assign("show_area", $this->show_area($aid, $ban_id));

        $w = D("ware");

        $query = "1=1 and ";
        if ($aid > 0 && $area_data = $area->find($aid)) {
            $area_path = $area_data["area_path"];
            $area_path.="-" . $aid;
            $shop_area_data = $area->where("left(area_path," . strlen($area_path) . ")='" . $area_path . "'")->select();
            $query.="( area_id=" . $aid . " or "; //不要忘了挂在本级的产品
            foreach ($shop_area_data as $key => $value) {
                $query.=" area_id=" . $value["id"] . " or ";
            }
            $query.=" 1=3 ) and ";
        }
        $this->getArea($ban_id);
        $now_date = date("H:i", time());
        $now_date = explode(":", $now_date);
        $now_h = (int) $now_date[0];
        $now_m = (int) $now_date[1];
        $query .= "ban_id=" . $ban_id . " ";
        if ($_GET["tuijian"])
            $query.=" and is_hot =1";
        $order = "shop_order desc ,";
        if ($_GET['rate'])
            $order = "rating_service desc , ";
        if ($_GET['price'])
            $order = "service_price  desc , ";
        $order.="id asc";
        $shop = D("shop");
        import("ORG.Util.Page");
        $count = $shop->where($query)->count();
        $page = new Page($count, 15);
        $show = $page->show();
        $data = $shop->where($query)->limit($page->firstRow . ',' . $page->listRows)->order($order)->select();
        // z();
        foreach ($data as $key => $value) {
            $s_date = explode(":", $value["start_time"]);
            $e_date = explode(":", $value["end_time"]);
            if (($s_date[0] < $now_h || ($s_date[0] == $now_h && $s_date[1] < $now_m)) && ($e_date[0] > $now_h || ($e_date[0] == $now_h && $e_date[1] > $now_m))) {
                $data[$key]["can_buy"] = 1;
            } else {
                $data[$key]["can_buy"] = 0;
            }
            $data[$key]["shop_desn"] = str_replace('"', "'", $data[$key]["shop_desn"]);
        }
        if (isset($_GET["canbuy"])) {
            foreach ($data as $key => $value) {
                if ($value["can_buy"] == 0)
                    unset($data[$key]);
            }
        }
        $this->assign('data', $data);
        $this->assign("show", $show);
		$this->order();
		$this->inews();
        $this->display();
    }

    function detial() {
        $id = $_GET["id"]; //shopid
        $comment = d("comment");
        $w = D("ware");
        $comment_data = $comment->where(array("shop_id" => $id))->order("id ,pid desc")->select();
        $this->assign("comment_data", $comment_data);
        $liuyan = d("liuyan");
        $liuyan_data = $liuyan->where(array("shop_id" => $id))->order("id ,pid desc")->select();
        $this->assign("liuyan_data", $liuyan_data);
        $shop = D("shop");
        $cat = d("cat");
        $ware = D("ware");
        $shop_data = $shop->find($id);
        if ($w->where("shop_id=" . $shop_data["id"] . " and dz=1 and is_tuan=0")->find())
            $shop_data["dz"] = 1;
        if ($w->where("shop_id=" . $shop_data["id"] . " and dz=0 and is_tuan=0")->find())
            $shop_data["jc"] = 1;
        if ($w->where("shop_id=" . $shop_data["id"] . " and is_tuan=1")->find())
            $shop_data["t"] = 1;
        /*         * *************************判断是否支持购买************************************ */
        $now_date = date("H:i", time());
        $now_date = explode(":", $now_date);
        $now_h = (int) $now_date[0];
        $now_m = (int) $now_date[1];
        $s_date = explode(":", $shop_data["start_time"]);
        $e_date = explode(":", $shop_data["end_time"]);
        if (($s_date[0] < $now_h || ($s_date[0] == $now_h && $s_date[1] < $now_m)) && ($e_date[0] > $now_h || ($e_date[0] == $now_h && $e_date[1] > $now_m))) {
            $shop_data["can_buy"] = 1;
        } else {
            $shop_data["can_buy"] = 0;
        }
        $cat_data = $cat->where("shop_id=" . $id)->order("id asc ")->select();
        $w_query = "shop_id=" . $id . " and ";
        if ($catid = (int) $_GET["catid"]) {
            $cat_li = $cat->find($catid);
            $c_path = $cat_li["c_path"] . "-" . $catid;
            $w_query.=" left(w_cat," . strlen($c_path) . ")='" . $c_path . "' and  ";
        }
        $w_query.="is_show=1 ";
        $dz_query = $w_query . " and dz=1 and is_tuan=0";
        $t_query = $w_query . " and  is_tuan=1";
        $query = $w_query . " and dz=0  ";
        import("ORG.Util.Page");
        $count = $ware->where($query)->count();
        $page = new Page($count, 16);
        $show = $page->show();
        $w_data = $ware->where($query)->limit($page->firstRow . ',' . $page->listRows)->order("is_tuan desc,id desc")->select();
         

        // z();
        $dz_data = $ware->where($dz_query)->select();
        $t_data = $ware->where($t_query)->select();
       // z();
        $this->assign('shop_data', $shop_data);
       // print_r($shop_data);
        $this->assign('t_data', $t_data);//团购
        $this->assign('cat_data', $cat_data);
        $this->assign('w_data', $w_data);

        $this->assign('dz_data', $dz_data);
        $this->assign("show", $show);
         //print_r($t_data);
        // z();
		$this->order();
		$this->inews();
        $this->display();
    }

    function detial1() {
        $id = $_GET["id"]; //shopid
        $comment = d("comment");
        $w = D("ware");
        $comment_data = $comment->where(array("shop_id" => $id))->order("id ,pid desc")->select();
        $this->assign("comment_data", $comment_data);
        $liuyan = d("liuyan");
        $liuyan_data = $liuyan->where(array("shop_id" => $id))->order("id ,pid desc")->select();
        $this->assign("liuyan_data", $liuyan_data);
        $shop = D("shop");
        $cat = d("cat");
        $ware = D("ware");
        $shop_data = $shop->find($id);
        if ($w->where("shop_id=" . $shop_data["id"] . " and dz=1")->find())
            $shop_data["dz"] = 1;
        if ($w->where("shop_id=" . $shop_data["id"] . " and dz=0")->find())
            $shop_data["jc"] = 1;
        /*         * *************************判断是否支持购买************************************ */
        $now_date = date("H:i", time());
        $now_date = explode(":", $now_date);
        $now_h = (int) $now_date[0];
        $now_m = (int) $now_date[1];
        $s_date = explode(":", $shop_data["start_time"]);
        $e_date = explode(":", $shop_data["end_time"]);
        if (($s_date[0] < $now_h || ($s_date[0] == $now_h && $s_date[1] < $now_m)) && ($e_date[0] > $now_h || ($e_date[0] == $now_h && $e_date[1] > $now_m))) {
            $shop_data["can_buy"] = 1;
        } else {
            $shop_data["can_buy"] = 0;
        }
        $cat_data = $cat->where("shop_id=" . $id)->order("id asc ")->select();
        $w_query = "shop_id=" . $id . " and ";
        if ($catid = (int) $_GET["catid"]) {
            $cat_li = $cat->find($catid);
            $c_path = $cat_li["c_path"] . "-" . $catid;
            $w_query.=" left(w_cat," . strlen($c_path) . ")='" . $c_path . "' and  ";
        }
       $w_query.="is_show=1 ";
        $dz_query = $w_query . " and dz=1 and is_tuan=0";
        $t_query = $w_query . " and  is_tuan=1";
        $query = $w_query . " and dz=0  ";
        import("ORG.Util.Page");
        $count = $ware->where($query)->count();
        $page = new Page($count, 16);
        $show = $page->show();
        $w_data = $ware->where($query)->limit($page->firstRow . ',' . $page->listRows)->order("is_tuan desc,id desc")->select();
        // z();
        $dz_data = $ware->where($dz_query)->select();
        $this->assign('shop_data', $shop_data);

        $this->assign('cat_data', $cat_data);
        $this->assign('w_data', $w_data);
        $this->assign('dz_data', $dz_data);
        $this->assign("show", $show);
        // print_r($cat_data);
        // z();
		$this->order();
		$this->inews();
        $this->display();
    }

    function comment() {
        $_POST["create_time"] = time();
        $comment = d("comment");
        $comment->create();
        if ($comment->add()) {
            $this->success("留言成功");
        } else {
            $this->error("留言失败");
        }
    }

    function liuyan() {
        $_POST["create_time"] = time();
        $comment = d("liuyan");
        $comment->create();
        if ($comment->add()) {
            $this->success("留言成功");
        } else {
            $this->error("留言失败");
        }
    }

    function delete_comment() {
        $id = (int) $_GET["id"];
        $comment = d("comment");

        if ($comment->where("id=" . $id)->delete()) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }

    function delete_liuyan() {
        $id = (int) $_GET["id"];
        $comment = d("liuyan");

        if ($comment->where("id=" . $id)->delete()) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }

}