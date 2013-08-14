<?php

class ShopAction extends CommonAction {

    function index() {
        $ban_id = (int) $_GET["banid"];
        $ban = D("ban");
        $ban_data = $ban->select();
        $this->assign("ban_data", $ban_data);
        import("ORG.Util.Page");
        $shop = D("Shop");
        $query = "1=1 ";

        if ($_GET["banid"])
            $query.=" and  ban_id =" . (int) $_GET["banid"];
        if ($_GET["area_id"])
            $query.=" and area_id=" . (int) $_GET["area_id"];
        $Area = D("Area");
        $select = $Area->where("ban_id=" . $ban_id)->order("pid asc ,id asc")->select();
        //z();
        // print_r($select);
        $this->assign("select", $select);
        $count = $shop->where($query)->count();

        $page = new Page($count, 20);
        $show = $page->show();
        $Shop_data = $shop->where($query)->limit($page->firstRow . ',' . $page->listRows)->order("shop_order desc ,id asc ")->select();
        // z();
        $this->assign("data", $Shop_data);
        $this->assign("show", $show);
        $this->display();
    }

    function index1() {
        $ban_id = (int) $_GET["banid"];
        $ban = D("ban");
        $ban_data = $ban->select();
        $this->assign("ban_data", $ban_data);
        import("ORG.Util.Page");
        $shop = D("Shop");
        $query = "1=1";
        if ($_GET["banid"])
            $query.=" and  ban_id =" . (int) $_GET["banid"];
        $count = $shop->where($query)->count();

        $page = new Page($count, 20);
        $show = $page->show();
        $Shop_data = $shop->where($query)->limit($page->firstRow . ',' . $page->listRows)->order("shop_order desc ,id asc ")->select();
        // z();
        $this->assign("data", $Shop_data);
        $this->assign("show", $show);
        $this->display();
    }

    function add() {
        $ban_id = (int) $_GET["banid"];
        $ban = D("ban");
        $ban_data = $ban->select();
        $this->assign("ban_data", $ban_data);
        $Area = D("Area");
        $this->assign("select", $Area->select_list($ban_id));
        $this->display();
    }

    function insert() {
        $Area = M("Area");
        $shop = D("Shop");
        $_POST["area_id"] = (int) $_POST["pid"];

        $Area_data = $Area->find($_POST["area_id"]);
        $_POST["area_name"] = $Area_data["area_name"];
        $data = $this->upload_file();
        $_POST["logo_href"] = $data["savename"];
        $_POST["password"] = md5($_POST["password"]);
        if ($shop->create()) {
            $shop->add();
            //print_r($_POST);
            $this->success("添加成功");
        } else {
            $this->error($shop->getError());
        }
    }

    function delete() {
        $Shop = M('Shop');
        if ($Shop->where("id=" . (int) $_GET["id"])->delete()) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }

    function mod() {
        $ban_id = (int) $_GET["banid"];


        $Area = D("Area");
        $Shop = D("Shop");
        $Shop_data = $Shop->find((int) $_GET["id"]);
        $shop_data["shop_desn"] = htmlspecialchars_decode($shop_data["shop_desn"]);
        if ($ban_id < 1)
            $ban_id = $Shop_data["ban_id"];
        $ban = D("ban");
        $ban_data = $ban->select();
        $this->assign("ban_data", $ban_data);
        $this->assign("select", $Area->select_list($ban_id, "pid", $Shop_data["area_id"]));
        $this->assign("data", $Shop_data);
        $this->display();
    }

    function update() {
        $Area = M("Area");
        $shop = D("Shop");
        $_POST["area_id"] = (int) $_POST["pid"];
        $_POST["shop_desn"] = $_POST["text"];
        $Area_data = $Area->find($_POST["area_id"]);
        $_POST["area_name"] = $Area_data["area_name"];
        if ($_FILES["logo_href"]["size"] > 0) {
            $data = $this->upload_file();
            $_POST["logo_href"] = $data["savename"];
        }
        if ($_POST["password"]) {
            $_POST["password"] = md5($_POST["password"]);
        } else {
            unset($_POST["password"]);
        }
        $shop->create();
        if ($shop->save()) {
            $shop->add();
            $this->success("修改成功");
        } else {
            // $this->error($shop->getError());
            //echo M()->getLastSql();
            z();
        }
    }

    function hot() {
        $id = (int) $_GET["id"];
        $shop = M("shop");
        if ($shop->where("id=" . $id)->setField("is_hot", 1)) {
            $this->success("首页推荐成功");
        } else {
            $this->error("首页推荐失败");
        }
    }

    function qhot() {
        $id = (int) $_GET["id"];
        $shop = M("shop");
        if ($shop->where("id=" . $id)->setField("is_hot", 0)) {
            $this->success("取消推荐成功");
        } else {
            $this->error("取消推荐失败");
        }
    }

    /*     * ************************************上下移位排序*************************** */

    function up() {
        $id = (int) $_GET["id"];
        $shop = D("shop");
        $row = $shop->find($id);
        $shop_order = $row["shop_order"]; //目前的order
        $next_data = $shop->where("shop_order >=" . $shop_order . " and id !=" . $row["id"])->order("shop_order asc ")->find();
        $next_order = $next_data["shop_order"]; //需要换位的order
        if ($next_order == $shop_order)
            $next_order+=1;
        if ($shop->where("id=" . $id)->setField("shop_order", $next_order)) {
            $shop->where("id=" . $next_data["id"])->setField("shop_order", $shop_order);
            echo "<script>location.href='" . $_SERVER["HTTP_REFERER"] . "';</script>";
        } else {
            $this->error("已经排在首位，无效操作");
        }
    }

    function down() {
        $id = (int) $_GET["id"];
        $shop = D("shop");
        $row = $shop->find($id);
        $shop_order = $row["shop_order"]; //目前的order
        $next_data = $shop->where("shop_order <=" . $shop_order . " and id !=" . $row["id"])->order("shop_order desc ")->find();
        $next_order = $next_data["shop_order"]; //需要换位的order
        if ($next_order == $shop_order)
            $shop_order+=1;
        if ($shop->where("id=" . $next_data["id"])->setField("shop_order", $shop_order)) {
            $shop->where("id=" . $id)->setField("shop_order", $next_order);
            echo "<script>location.href='" . $_SERVER["HTTP_REFERER"] . "';</script>";
        } else {
            $this->error("已经排在最后一位无效操作");
        }
    }

    private function upload_file() {

        import("@.ORG.UploadFile");
        $upload = new UploadFile('', 'jpg,gif,png', '', './Public/upload/', 'time');
        $upload->imageClassPath = "@.ORG.Image";
        $upload->thumb = true;
        $upload->thumbMaxHeight = 150;
        $upload->thumbMaxWidth = 150;
        if (!$upload->upload()) {
            $this->error($upload->getErrorMsg());
        } else {
            $info = $upload->getUploadFileInfo();
            return $info[0];
        }
    }

}