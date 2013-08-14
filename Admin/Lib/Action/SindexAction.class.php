<?php

class SindexAction extends ScommonAction {
    function index(){
        $Shop=D("shop");
        $data= $Shop->where("id=".$_SESSION["shop_id"])->find();
         $data["shop_desn"]=htmlspecialchars_decode($data["shop_desn"]);
        $this->assign("data",$data);
        $this->display();
    }
    function update(){
        $shop=M("Shop");
         
         IF ($_FILES["logo_href"]['size'] > 0) {
            $file_data = $this->upload_file();
            $_POST['logo_href'] = $file_data["savename"];
        }
         if($_POST["password"]){
            $_POST["password"]=md5( $_POST["password"]);
        }else{
            unset($_POST["password"]);
        }
        $_POST["shop_desn"]=$_POST["text"];
        $shop->create();
        if($shop->save()){
            $this->success("修改成功");
        }else{
            $this->error("修改失败");
            z();
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