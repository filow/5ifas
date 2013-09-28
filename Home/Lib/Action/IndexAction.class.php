<?php

class IndexAction extends CommonAction {

    public function index() {
		$this->order();
		$this->inews();
        $this->display();
    }

    function ware() {
        import("ORG.Util.Page");
        $ware = M("ware");
        $count = $ware->count();
        $page = new Page($count, 15);
        $data = $ware->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign("data", $data);
        $this->assign("show", $page->show());

		$this->order();//获取新订单
		$this->inews();
        $this->display();
    }

    function detial() {
        $id = (int) $_GET["id"];
        $news = M("news");
        $data = $news->find($id);
        $data['text'] = $data['text'];
        $data['title'] = htmlspecialchars_decode($data['title']);
        $this->assign('data', $data);
		$this->order();//获取新订单
		$this->inews();
        $this->display();
    }

    function newslist() {
        $news = M('news');
        import("ORG.Util.Page");
        $count = $news->where(array('newstype'=>0))->count();
        $page = new Page($count, 15);
        $show = $page->show();
        $data = $news->where(array('newstype'=>0))->limit($page->firstRow . ',' . $page->listRows)->order('createtime desc')->select();
        $this->assign('data', $data);
        $this->assign("show", $show);
		$this->order();//获取新订单
		$this->inews();
        $this->display();
    }

    function w() {
        $ware = M("ware");
        $id = (int) $_GET["id"];
        $data = $ware->find($id);
        $this->assign("data", $data);
		$this->order();//获取新订单
		$this->inews();
        $this->display();
    }

    function search() {
        G('begin');
        $ware = M("ware");
        $item = $_GET["item"];
        $data = $ware->where("w_name like '%" . $item . "%' and is_show=1")->select();
        $num=count($data);
        G('end');
        $this->assign("data", $data);
		$this->assign('num',$num);
        $this->assign('timeused',G('begin','end'));
		$this->order();//获取新订单
		$this->inews();
        $this->display();
    }
}