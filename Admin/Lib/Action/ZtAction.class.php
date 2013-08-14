<?php

class ZtAction extends CommonAction {

    function index() {
        $zt = D("zt");
        $data = $zt->select();
        $this->assign("data", $data);
        $this->display();
    }

    function add() {
        $ban_id = (int) $_GET["banid"];
        $ban = D("ban");
        $ban_data = $ban->where("type=4")->select();
        $this->assign("ban_data", $ban_data);
        $Area = D("Area");
        $this->assign("select", $Area->select_list($ban_id));
        $this->display();
    }

    function insert() {
        $_POST["area_id"] = $_POST["pid"];
        $_POST["content"] = $_POST["text"];
        $zt = D("zt");
        $zt->create();
        if ($zt->add()) {
            $this->success("添加专题成功");
        } else {
            $this->error("添加专题失败");
        }
    }

    function mod() {
        $zt = D("zt");
        $id = (int) $_GET["id"];
        $data = $zt->find($id);
        $data['content'] = htmlspecialchars_decode($data['content']);
        $data['title'] = htmlspecialchars_decode($data['title']);
        $this->assign("data", $data);
        $this->display();
    }
    function update(){
         
        $_POST["content"] = $_POST["text"];
        $zt = D("zt");
        $zt->create();
        if ($zt->save()) {
            $this->success("修改专题成功");
        } else {
            $this->error("修改专题失败");
        }
        
    }

}