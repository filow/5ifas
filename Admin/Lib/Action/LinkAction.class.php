<?php

class LinkAction extends CommonAction{

    function index() {
        $link = M("link");
        $this->assign("link", $link->select());
        $this->display();
    }

    function add() {
        $this->display();
    }

    function insert() {
        $link = M("link");
        if ($link->create()) {
            $link->add();
            $this->success("添加成功");
        } else {
            $this->error("添加失败");
        }
    }

    function delete() {
        $link = M("link");
        if($link->where("id=".(int)$_GET["id"])->delete()) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }
    function mod(){
        $id=(int)$_GET["id"];
        $link=M("link");
       $this->assign("link",$link->find($id));
       $this->display();
    }
    function update(){
         $link = M("link");
         if(!$link->create()){
               $this->error("修改失败");
         }
        if($link->where("id=".(int)$_POST["id"])->save()) {
            $this->success("修改成功","index");
        } else {
            $this->error("修改失败");
        }
    }

}