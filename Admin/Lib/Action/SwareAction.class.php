<?php

class SwareAction extends ScommonAction {

    function index() {
        import("ORG.Util.Page");
        $query_data = getQuery();
        // print_r($query_data);
        $query = $query_data["string"];
        $query.=" and is_show=1 and shop_id=".$_SESSION["shop_id"];
        $ware = D('ware');
        $count = $ware->where($query)->count();
        $page = new Page($count, 25);
        $show = $page->show();
        $list = $ware->field('id,w_name,w_price,onlinepay,w_time')->where($query)->order("id asc")->limit($page->firstRow . ',' . $page->listRows)->select();
        $export_data[] = array("商品ID", "商品名", "商品价格", "是否支持货到付款");
        foreach ($list as $key => $value) {
            $onlinepay = $value["onlinepay"] == 2 ? "是" : "否";
            $export_data[] = array($value["id"], $value["w_name"], $value["w_price"], $onlinepay);
        }

        //print_r($export_data);
        $this->assign("query", $query_data["array"]);
        $this->assign("data", $list);
        $this->assign("show", $show);
        $this->assign("export_data", json_encode($export_data));
        $this->display();
    }

    function add() {
        $info = D("info");
        $sushe = $info->where("type=1")->select();
        // z();
        // print_r($sushe);
        $cat = D('Cat');
        //$catname = $cat->field('c_name')->where("shop_id=" . (int)$_SESSION["shop_id"])->select();
        $this->assign('select', $cat->select1('w_cat'));

        $this->assign("sushe", $sushe);
        $this->display();
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
        if($_POST["dz"]){
            $_POST["dz"]=1;
        }else{
            $_POST["dz"]=0;
        }
        if($_POST["is_youhui"]){
            $_POST["is_youhui"]=1;
        }else{
            $_POST["is_youhui"]=0;
        }
        if($_POST["send_sms"]){
            $_POST["send_sms"]=1;
        }else{
            $_POST["send_sms"]=0;
        }
        if($_POST["is_tuan"]){
            $_POST["is_tuan"]=1;
        }else{
            $_POST["is_tuan"]=0;
        }
        $w = D('ware');
        $f2s = D("f2s");
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

            $this->success('修改成功');
        } else {
            $this->error("修改失败");
        }
    }

    //商品修改跳转页
    function mod() {
        $info = D("info");
        $sushe = $info->where(array("type" => 1))->select();
        $w = D('ware');
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
        //  print_r($output);
        //类别
        $cat = D('Cat');
        $get['id'] = $_GET['id'];
        $this->assign('select', $cat->select1('w_cat', '0', $get));
        $this->assign('data', $data);
        $this->assign("sushe", $output);
        $this->display();
    }

    //商品添加页面
    function insert() {
		$_POST["w_desn"]=$_POST["text"];
        $w = D('ware');
        //如果设置了积分，该商品为礼品，type类型为1否者为正常商品type为2
        $_POST["w_type"] = 2;
        $_POST["is_hot"] = 0;
        $_POST["shop_id"] = (int) $_SESSION["shop_id"];
        $_POST["rate"] = 0;
        $_POST["ratecount"] = 0;

        $_POST['w_time'] = time();
        $file_data = $this->upload_file();
        $_POST['w_pic'] = $file_data["savename"];
        if($_POST["can_buy"]){
            $_POST["can_buy"]=1;
        }else{
            $_POST["can_buy"]=0;
        }
        if($_POST["dz"]){
            $_POST["dz"]=1;
        }else{
            $_POST["dz"]=0;
        }
        if($_POST["is_youhui"]){
            $_POST["is_youhui"]=1;
        }else{
            $_POST["is_youhui"]=0;
        }
        if($_POST["send_sms"]){
            $_POST["send_sms"]=1;
        }else{
            $_POST["send_sms"]=0;
        }
        if($_POST["is_tuan"]){
            $_POST["is_tuan"]=1;
        }else{
            $_POST["is_tuan"]=0;
        }
        //以json格式储存宿舍信息到info列
        $num = count($_POST['sushe']);
        $json = array();
        $sushe = $_POST['sushe'];
        foreach ($sushe as $key => $value) {

            $json[] = (int) $value;
        }
        $jsonsushe = json_encode($json);
        $_POST['info'] = $jsonsushe;
        $f2s = D("f2s");

        if (!$w->create()) {
            $this->error("信息错误或不全");
        }
        if ($id = $w->add()) {
            for ($i = 0; $i < $num; $i++) {
                $f2s->add(array("cid" => $id, "sid" => $sushe[$i]));
            }
          //  print_r($_POST);
            $this->success('添加商品成功');
        } else {
            sae_unlink('./Public/upload/' . $_POST['pic']);
            $this->error("添加失败");
        }
    }

    //商品回收站页
    function recover() {
        import("ORG.Util.Page");
        $query_data = getQuery();
        // print_r($query_data);
        $query = $query_data["string"];
        $query.=" and is_show=0 and shop_id=".$_SESSION["shop_id"];
        $ware = D('ware');
        $count = $ware->where($query)->count();
        $page = new Page($count, 25);
        $show = $page->show();
        $list = $ware->field('id,w_name,w_price,onlinepay,w_time')->where($query)->order("id asc")->limit($page->firstRow . ',' . $page->listRows)->select();
        $export_data[] = array("商品ID", "商品名", "商品价格", "是否支持货到付款");
        foreach ($list as $key => $value) {
            $onlinepay = $value["onlinepay"] == 2 ? "是" : "否";
            $export_data[] = array($value["id"], $value["w_name"], $value["w_price"], $onlinepay);
        }

        //print_r($export_data);
        $this->assign("query", $query_data["array"]);
        $this->assign("data", $list);
        $this->assign("show", $show);
        $this->assign("export_data", json_encode($export_data));
        $this->display();
    }

    //下架
    function cover() {
        $w = D('ware');
        if ($w->where('id=' . (int) $_GET['id']." and shop_id=".$_SESSION["shop_id"])->setField("is_show", 0)) {
            $this->redirect('index');
        } else {
            $this->error("下架失败，刷新重试");
        }
    }

    function restore() {

        $w = D('ware');
        if ($w->where('id=' . $_GET['id']." and shop_id=".$_SESSION["shop_id"])->setField("is_show", 1)) {
            $this->redirect('index');
        } else {
            $this->error("上架失败");
        }
    }

    //商品评论页


    private function upload_file() {

        import("@.ORG.UploadFile");
        $upload = new UploadFile('', 'jpg,gif,png', '', './Public/upload/', 'time');
        $upload->imageClassPath = "@.ORG.Image";
        $upload->thumb = true;
       // $upload->maxSize =150*1024;
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