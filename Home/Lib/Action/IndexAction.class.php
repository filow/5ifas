<?php

class IndexAction extends CommonAction {

    public function index() {
        //当前时间
        $now_date = date("H:i", time());
        $now_date = explode(":", $now_date);
        $now_h = (int) $now_date[0];
        $now_m = (int) $now_date[1];
        //数据查询
		$shop = M("shop");
		$ban = M("ban");
        $hot = $shop->where("is_hot=1 ")->order("shop_order desc ,id desc")->limit(6)->select();
		$new = $shop->where("is_hot=0 ")->order("shop_order desc ,id desc")->limit(6)->select();

        foreach ($hot as $key => $value) {
            //判断目前是否在营业
            $s_date = explode(":", $value["start_time"]);
            $e_date = explode(":", $value["end_time"]);
            if (($s_date[0] < $now_h || ($s_date[0] == $now_h && $s_date[1] < $now_m)) && ($e_date[0] > $now_h || ($e_date[0] == $now_h && $e_date[1] > $now_m))) {
                $hot[$key]["can_buy"] = false;
            } else {
                $hot[$key]["can_buy"] = true;
            }
            //$ban_data = $ban->find($value["ban_id"]);
            //$hot[$key]["ban_type"] = $ban_data["type"];
            $hot[$key]["shop_desn"] = str_replace('"', "'", $hot[$key]["shop_desn"]);
        }
        $this->assign("hot_data", $hot);
        $this->assign("new_data", $new);
        //$this->assign("hot_num", count($hot));
        
		$this->order();
		$this->inews();

        $this->display();
    }

    function ware() {
        import("ORG.Util.Page");
        $shop = D("shop");
         //检查有没有地域限制
        $ban_id = (int) $_GET["banid"];
        $this->getArea($ban_id);
        $query = "ban_id=" . $ban_id;
        $aid = $_COOKIE["area_id" . $ban_id];
        if ($aid > 0)
            $query.=" and area_id=" . $aid;

        $shop_id = $shop->where($query)->find();
        $shop_id = $shop_id["id"];

        $cat = D("cat");
        $cat_list = $cat->where("shop_id=" . $shop_id)->field('id,c_name,concat(c_path,"-",id) abspath')->order("abspath,id asc")->select();
        trace($cat_list,'显示标签');
        $query = "1=1 ";
        if (isset($_GET["cid"])) {
            $cid = (int) $_GET["cid"];
            $data = $cat->find($cid);
            $cid = $data["c_path"] . "-" . $cid;
            $query .= " and left(w_cat," . strlen($cid) . ")=\'".$cid."'";
        }

        $query.=" and is_show = 1 and shop_id=" . $shop_id;

        $ware = D("ware");
        $count = $ware->where($query)->count();
        $page = new Page($count, 16);
        $show = $page->show();
        $data = $ware->where($query)->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign("data", $data);
        $this->assign("cat_list", $cat_list);
        $this->assign("show", $show);

		$this->order();//获取新订单
		$this->inews();
        $this->display();
    }

    function detial() {
        $id = (int) $_GET["id"];
        $news = D("news");
        $data = $news->find($id);
        $data['text'] = htmlspecialchars_decode($data['text']);
        $data['title'] = htmlspecialchars_decode($data['title']);
        $list = $news->limit(10)->where(array("newstype" => 0))->order('createtime desc ')->select();
        $this->assign('list', $list);
        $this->assign('data', $data);
		$this->order();//获取新订单
		$this->inews();
        $this->display();
    }

    function newslist() {
        $news = D('news');
        import("ORG.Util.Page");
        $query = "newstype = 0";
        $count = $news->where($query)->count();
        $page = new Page($count, 15);
        $show = $page->show();
        $data = $news->where($query)->limit($page->firstRow . ',' . $page->listRows)->order('createtime desc ')->select();
        $this->assign('data', $data);
        $this->assign("show", $show);
		$this->order();//获取新订单
		$this->inews();
        $this->display();
    }

    function w() {
        $now_date = date("H:i", time());
        $now_date = explode(":", $now_date);
        $now_h = (int) $now_date[0];
        $now_m = (int) $now_date[1];
        $ware = D("ware");
        $shop = D("shop");
        //$comment = d("comment");
        $id = (int) $_GET["id"];
        $data = $ware->find($id);
        $shop_data = $shop->find($data["shop_id"]);
        $s_date = explode(":", $shop_data["start_time"]);
        $e_date = explode(":", $shop_data["end_time"]);
        if (($s_date[0] < $now_h || ($s_date[0] == $now_h && $s_date[1] < $now_m)) && ($e_date[0] > $now_h || ($e_date[0] == $now_h && $e_date[1] > $now_m))) {
            $data["shop_can_buy"] = false;
        } else {
            $data["shop_can_buy"] = true;
        }
        $data["shop_name"] = $shop_data["shop_name"];
        //$comment_data = $comment->where(array("w_id" => $id))->order("id ,pid desc")->select();
        $this->assign("data", $data);
        //$this->assign("comment_data", $comment_data);

		$this->order();//获取新订单
		$this->inews();
        $this->display();
    }

    /**
     *************************团购******************************* 
     */

    function tuan() {
        $ware = D("ware");
        $shop = D("shop");
        $o2p=d("order2product");
        $id = (int) $_GET["id"];
        $sum_data= $o2p->field("sum(pnum) as sum")->where("productid=".$id)->find();
        $ware_data = $ware->find($id);
        $shop_data = $shop->find($ware_data["shop_id"]);
        $this->assign("ware_data", $ware_data);
        $this->assign("shop_data", $shop_data);
        $this->assign("sum",$sum_data["sum"]);
		$this->order();//获取新订单
		$this->inews();
        $this->display();
    }

    function comment() {//暂时不知道这个函数的作用
        $_POST["create_time"] = time();
        $comment = d("comment");
        $comment->create();
        if ($comment->add()) {
            $this->success("评论成功");
        } else {
            $this->error("评论失败");
        }
    }

    function search() {
        $now_date = date("H:i", time());
        $now_date = explode(":", $now_date);
        $now_h = (int) $now_date[0];
        $now_m = (int) $now_date[1];

        $ware = M("ware");
        $shop = M("shop");
        $comment = M("comment");
        $item = $_GET["item"];
		$num = 0;//计数
        $data = $ware->where("w_name like '%" . $item . "%' and is_show=1")->select();
        foreach ($data as $key => $value) {
            $shop_data = $shop->where("id=" . $value["shop_id"])->find();
            $s_date = explode(":", $shop_data["start_time"]);
            $e_date = explode(":", $shop_data["end_time"]);
            if (($s_date[0] < $now_h || ($s_date[0] == $now_h && $s_date[1] < $now_m)) && ($e_date[0] > $now_h || ($e_date[0] == $now_h && $e_date[1] > $now_m))) {
                $data[$key]["shop_can_buy"] = false;
            } else {
                $data[$key]["shop_can_buy"] = true;
            }
			$num=$num+1;
        }
        $this->assign("ware_data", $data);

        $w = D("ware");
        $data = $shop->where("shop_name like '%" . $_GET["item"] . "%'")->select();
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
			$num=$num+1;
        }

        $this->assign('shop_data', $data);
		$this->assign('num',$num);
		$this->order();//获取新订单
		$this->inews();
        $this->display();
    }
    function ly() {
        $liuyan = d("siteLiuyan");
        import("ORG.Util.Page");
        $query = "pid= 0";
        $count = $liuyan->where($query)->count();
        $page = new Page($count, 15);
        $show = $page->show();
        $data = $liuyan->where($query)->limit($page->firstRow . ',' . $page->listRows)->order('createtime desc ')->select();
        $this->assign("data", $data);
        $this->assign("show", $show);
		$this->order();//获取新订单
		$this->inews();
        $this->display();
    }

    function lyi() {
        $liuyan = d("siteLiuyan");
        if(empty($_POST['pid']))
            if (strlen(trim($_POST["title"])) < 5) 
                $this->error("抱歉,标题过短.");
        if(strlen(strip_tags(trim($_POST["text"]))) < 5)
            $this->error("抱歉,您的内容太短。（只有大于5个字符的长度才能通过验证）");
        if(empty($_SESSION["loginname"]))
            $this->error("用户没有登录,不能发表评论.");
        $_POST["author"] = $_SESSION["loginname"];
        $_POST["is_admin"] = 0;
        $_POST["createtime"] = time();
        $_POST["content"] = $_POST["text"];
        if ($_SESSION["admin_id"] == 1) {
            $_POST["is_admin"] = 1;
        }
        $liuyan->create();
        if ($liuyan->add()) {
            $this->success("留言成功");
        } else {
            $this->error("留言失败");
        }
    }

    function c() {
        $id = (int) $_GET["id"];
        $liuyan = d("siteLiuyan");
        $data = $liuyan->find($id);
        $reply_data = $liuyan->where("pid =" . $id)->order("createtime desc ")->select();
        $this->assign("data", $data);
        $this->assign("reply_data", $reply_data);

		$query = "pid= 0";//筛选出非留言部分
		$li_data = $liuyan->where($query)->limit(10)->order('createtime desc ')->select();
        $this->assign("li_data", $li_data);


		$this->order();//获取新订单
		$this->inews();

        $this->display();
    }

    function lyd() {
        if($_SESSION["admin_id"]==1){
            $id = (int) $_GET["id"];
            $comment = d("siteLiuyan");
            if ($comment->where("id=" . $id)->delete()) {
                $this->success("删除成功");
            } else {
                $this->error("删除失败");
            }
        }else{
            $this->error("您不是管理员,不能删除留言");
        }

    }

}