<?php

class JfAction extends CommonAction {

    function index() {
        $jf = D("jf");
        import("ORG.Util.Page");
        $query_data = getQuery();
        $query = $query_data["like_query"];
         
        $count = $jf->where($query)->count();
// z();
        $page = new Page($count, 20);
        $show = $page->show();
        $data = $jf->where($query)->limit($page->firstRow . ',' . $page->listRows)->select();
        $export_data[]=array("ID","用户卡号","金额","类型","详情");
        foreach($data as $key=>$value){
            $type=$value["type"]==1?"收入":"支出";
            $export_data[]=array($value["id"],$value["cardn"],$value["amount"],$type,$value["desn"]);
        }
        $this->assign("data", $data);
        $this->assign("query", $query_data["array"]);
        $this->assign("show", $show);
        $this->assign("export_data", json_encode($export_data));
        $this->display();
    }
    function set(){
        $info=d("info");
       $data=$info->where("type=7")->find();
        $this->assign("data",$data);
        $this->display();
    }
    function update(){
        $info=D("info");
        if($info->where("type=7")->setField("value2",(int)$_POST["value2"])){
            $this->success("修改成功");
        }else{
            $this->error("修改失败");
        }
    }

}