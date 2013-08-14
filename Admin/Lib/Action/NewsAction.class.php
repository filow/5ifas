<?php

class NewsAction extends CommonAction {

    function index() {
        $news = D("news");
        import("ORG.Util.Page");
        $count = $news->where($query)->count();
        $page = new Page($count, 10);
        $show = $page->show();
        $data = $news->limit($page->firstRow . ',' . $page->listRows)->order("ishot desc ,id desc")->select();
        $this->assign("data", $data);
        $this->assign("show", $show);
        $this->display();
    }

    function add() {
        $this->display();
    }

    function insert() {
        $news = D("news");
        $_POST['createtime'] = time();
        $news->create();
        if ($news->add()) {
            $this->success("添加成功");
        } else {
            $this->error('添加失败');
        }
    }

    function mod() {

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $news = D("news");
            $data = $news->where("id=".$id)->find();
            $data['text'] = htmlspecialchars_decode($data['text']);
            $data['title'] = htmlspecialchars_decode($data['title']);
            $this->assign('data', $data);
            $this->display();
        }
    }

    function update() {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $news = D("news");
            $news->create();
            if ($news->where("id=".$id)->save()) {
                $this->success("修改成功");
            } else {
                $this->error("没有修改或信息不完整");
            }
        }
    }

    function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $news = D("news");
            if ($news->where("id=" . $id)->delete()) {
                $this->success("删除成功");
            } else {
                $this->error("删除失败");
            }
        }
    }

    function hot() {
        $id = (int) $_GET["id"];
        $news = M("news");
        if ($news->where("id=" . $id)->setField("ishot", 1)) {
            $this->success("置顶成功");
        } else {
            $this->error("首页置顶失败");
        }
    }

    function qhot() {
        $id = (int) $_GET["id"];
        $news = M("news");
        if ($news->where("id=" . $id)->setField("ishot", 0)) {
            $this->success("取消置顶成功");
        } else {
            $this->error("取消置顶失败");
        }
    }

}