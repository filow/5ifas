<?php

class CommonAction extends Action {

    function _initialize() {
        if(!isset($_SESSION["isLogin"])){
            $this->redirect("Public/login");
        }
        $this->assign("nav_list", $this->getNav());
        $user_id=$_SESSION["admin_id"];
        $p=D("permission");
        $nav=D("nav");
        $nav_id=$nav->where(array("module_name"=>MODULE_NAME))->find();
        $nav_id=$nav_id["id"];
        if(!$p->where(array("userid"=>$user_id,"nav_id"=>$nav_id))->find()&&MODULE_NAME!="Index")
                 $this->error("权限不够");
       
    }

    function getNav() {
        $nav = M("nav");
        $nav_list = $nav->order("pid asc, id asc ")->select();
        $nav_data = array();
        $user_id=$_SESSION["admin_id"];
        $p=D("permission");
        
        foreach ($nav_list as $key => $value) {
            if(!$p->where("userid=".$user_id." and nav_id=".$value["id"])->find())
                    continue;
            if ($value["pid"] == 0) {
                $nav_data[$value["id"]]["action_desc"] = $value["action_desc"];
            } else {
                $nav_data[$value["pid"]]["children"][] = $value;
            }
        }
        return $nav_data;
    }

}