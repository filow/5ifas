<?php

class AdAction extends CommonAction {
    function index() {
        $ad = d("ad");
        $data = $ad->select();

        foreach ($data as $key => $value) {
            switch ($value["type"]) {
                case 1:$data[$key]["type"] = "首页广告";
                    break;
                case 2:$data[$key]["type"] = "板块页广告";
                    break;
                case 3:$data[$key]["type"] = "商家详细页";
                    break;
                case 4:$data[$key]["type"] = "商品详细页广告";
                    break;
            }
        }

        $this->assign("data", $data);
        $this->display();
    }

    function update() {
        $ad = d("ad");
        $data = $this->upload_file();
        print_r($data);
    }

    function insert() {
        $ad = D("ad");
        $data = $this->upload_file();
        $_POST["pic"] = $data["savename"];
        if ($ad->create()) {
            if ($ad->add()) {
                $this->success("上传成功");
            } else {
                $this->error("上传失败");
            }
        }
    }

    function add() {
        $this->display();
    }
     function delete() {
        $ad = M("ad");
        if($ad->where("id=".(int)$_GET["id"])->delete()) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    } 

    private function upload_file() {

        import("@.ORG.UploadFile");
        $upload = new UploadFile('', 'jpg,gif,png', '', './Public/upload/', 'time');
        $upload->hashType
        $upload->imageClassPath = "@.ORG.Image";
        $upload->thumb = true;
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