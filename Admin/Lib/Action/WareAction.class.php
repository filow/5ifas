<?php

class WareAction extends CommonAction {

    function index() {
        $ware = M("ware");
        import("ORG.Util.Page");
        $query_data = getQuery();
        $query = $query_data["like_query"];
        $query.=" and is_show=1";

        $count = $ware->where($query)->count();
        $page = new Page($count, 50);
        $show = $page->show();
        $w_data = $ware->field("id,w_name,w_price,onlinepay,w_time")->where($query)->limit($page->firstRow . ',' . $page->listRows)->select();
        $export_data[] = array("商品编号", "商品名称", "商品售价", "是否支持货到付款", "商品添加时间");
        foreach ($w_data as $key => $value) {
            $onlinepay = $value["onlinepay"] == 2 ? "是" : "否";
            $export_data[] = array($value["id"], $value["w_name"], $value["w_price"], $onlinepay, date("Y-m-d", $value["w_time"]));
        }
        $this->assign("data", $w_data);
        $this->assign("query", $query_data["array"]);
        $this->assign("show", $show);

        $this->assign("export_data", json_encode($export_data));
        $this->display();
    }
     function add() {
        $info = M("info");
        $sushe = $info->where("type=1")->order('value')->select();
        $this->assign("sushe", $sushe);
        $this->display();
    }

    //商品添加页面
    function insert() {
        $w = M('ware');
        $_POST["w_desn"]=$_POST["text"];
        //如果设置了积分，该商品为礼品，type类型为1否者为正常商品type为2
        $_POST["w_type"] = 2;
        $_POST["is_hot"] = 0;

        $_POST["rate"] = 0;
        $_POST["ratecount"] = 0;

        $_POST['w_time'] = time();
        $file_data = $this->upload_file();
        $_POST['w_pic'] = $file_data["savename"];

        $_POST["can_buy"]=$_POST["can_buy"]? 1: 0;

        //强制设置shop_id为16
        $_POST["shop_id"]=16;

        //以json格式储存宿舍信息到info列
        $num = count($_POST['sushe']);
        $json = array();
        $sushe = $_POST['sushe'];
        foreach ($sushe as $key => $value) {
            $json[] = (int) $value;
        }
        $jsonsushe = json_encode($json);
        $_POST['info'] = $jsonsushe;
        $f2s = M("f2s");

        if (!$w->create()) {
            $this->error("信息错误或不全");
        }
        if ($id = $w->add()) {
            //设定送货宿舍与商品的关系
            for ($i = 0; $i < $num; $i++) {
                $f2s->add(array("cid" => $id, "sid" => $sushe[$i]));
            }
            $this->success('添加商品成功');
        } else {
            sae_unlink('./Public/upload/' . $_POST['pic']);
            $this->error("添加失败");
        }
    }
    //商品修改跳转页
    function mod() {
        $info = M("info");
        $sushe = $info->where(array("type" => 1))->order('value')->select();
        $w = M('ware');
        $data = $w->where('id=' . $_GET['id'])->find();
        $data["w_desn"]=htmlspecialchars_decode($data["w_desn"]);
        $ws = json_decode($data['info'], true);
        $output = array();
        foreach ($sushe as $key => $value) {
            if (in_array($value['value'], $ws)) {
                $value['isselected'] = 1;
            } else {
                $value['isselected'] = 0;
            }
            $output[] = $value;
        }
        $this->assign('data', $data);
        $this->assign("sushe", $output);
        $this->display();
    }
    //商品修改
    function update() {
        $_POST["w_desn"]=$_POST["text"];
        IF ($_FILES["w_pic"]['size'] > 0) {
            $file_data = $this->upload_file();
            $_POST['w_pic'] = $file_data["savename"];
        }
         if($_POST["can_buy"]){
            $_POST["can_buy"]=1;
        }else{
            $_POST["can_buy"]=0;
        }
        $w = M('ware');
        $f2s = M("f2s");
        $id = $_POST['id'];
        $num = count($_POST['sushe']);
        $json = array();
        $sushe = $_POST['sushe'];
        foreach ($sushe as $key => $value) {
            $json[] = (int) $value;
        }
        $jsonsushe = json_encode($json);
        $_POST['info'] = $jsonsushe;
        $w->create();
        if ($w->save()) {
            $f2s->where("cid=" . $id)->delete();
            for ($i = 0; $i < $num; $i++) {
                $f2s->add(array("cid" => $id, "sid" => $sushe[$i]));
            }
            $this->success('修改成功',U('index'));
        } else {
            $this->error("修改失败");
        }
    }
    //商品删除
    function delete() {
        $w = M('ware');
        $f2s = M("F2s");
        $id = (int) $_GET['id'];
        $pic = $w->where("id=" . $id)->field('w_pic')->find();
        if (($w->where("id=" . $id)->delete()) && (sae_unlink('./Public/upload/' . $pic['w_pic'])) && (sae_unlink('./Public/upload/thumb_' . $pic['w_pic']))) {
            $f2s->where("cid=" . $id)->delete();
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
            z();
        }
    }

    //商品回收站页
    function recover() {
        import("ORG.Util.Page");
        $query_data = getQuery();
        // print_r($query_data);
        $query = $query_data["like_query"];
        $query.=" and is_show=0 ";
        $ware = M('ware');
        $count = $ware->where($query)->count();
        $page = new Page($count, 25);
        $show = $page->show();
        $list = $ware->field('id,w_name,w_price,onlinepay,w_time')->where($query)->order("id asc")->limit($page->firstRow . ',' . $page->listRows)->select();
        $export_data[] = array("商品ID", "商品名", "商品价格", "是否支持货到付款");
        foreach ($list as $key => $value) {
            $onlinepay = $value["onlinepay"] == 2 ? "是" : "否";
            $export_data[] = array($value["id"], $value["w_name"], $value["w_price"], $onlinepay);
        }

        $this->assign("query", $query_data["array"]);
        $this->assign("data", $list);
        $this->assign("show", $show);
        $this->assign("export_data", json_encode($export_data));
        $this->display();
    }

    //下架
    function cover() {
        $w = M('ware');
        if ($w->where(array('id'=>I('id',0,"intval")))->setField("is_show", 0)) {
            $this->redirect('index');
        } else {
            $this->error("下架失败，刷新重试");
        }
    }

    function restore() {
        $w = M('ware');
        if ($w->where(array('id'=>I('id',0,"intval")))->setField("is_show", 1)) {
            $this->redirect('recover');
        } else {
            $this->error("上架失败");
        }
    }

    private function upload_file() {

        import("@.ORG.UploadFile");
        $upload = new UploadFile('', 'jpg,gif,png', '', './Public/upload/', 'time');
        $upload->imageClassPath = "@.ORG.Image";
        $upload->thumb = true;
        //$upload->maxSize =150*1024;
        $upload->thumbMaxHeight = 100;
        $upload->thumbMaxWidth = 100;
        if (!$upload->upload()) {
            $this->error($upload->getErrorMsg());
        } else {
            $info = $upload->getUploadFileInfo();
            return $info[0];
        }
    }

}

?>
