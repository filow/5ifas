<?php

class AdminAction extends CommonAction {

    function index() {
        $admin = D("admin");
        $data = $admin->select();
        $this->assign("data", $data);
        $this->display();
    }

    function delete() {
        $admin = D("admin");
        $id = (int) $_GET['id'];
        if ($admin->where("id=" . $id)->delete()) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }

    function mod() {
        $admin = D("admin");
        $id = (int) $_GET['id'];
        $this->assign("data", $admin->find($id));
       // print_r($admin->find($id));
        $this->display();
    }
    function update(){
        $admin=d("admin");
        if(isset($_POST["password"])&&trim($_POST["password"])!=""){
            if(strlen($_POST["password"])<6){
                $this->error("密码至少六位！");
            }
            $_POST["password"]=md5($_POST["password"]);
        }else{
            unset($_POST["password"]);
        }
        $admin->create();
        if($admin->save()){
            $this->success("修改成功");
        }else{
            $this->error("修改失败");
        }
        
    }
    function p(){
        $nav=D("Nav");
        $p=D("permission");
        $id=(int)$_GET["id"];
        $nav_data=$nav->field("id,pid,action_name,module_name,action_desc,concat(module_name,'-',id) pnav")->order("pnav asc ")->select();
         
        foreach($nav_data as $key=>$value){
            if($p->where("userid=".$id." and nav_id=".$value["id"])->find())
                   $nav_data[$key]["is_enabled"]=1;
        }
       
       // print_r($nav_data);
        $this->assign("nav_data",$nav_data);
       // print_r($nav_data);
        $this->display();
    }
    function p_update(){
        $userid=(int)$_POST["userid"];
        $p=d("permission");
        $p->where(array("userid"=>$userid))->delete();
        foreach($_POST["nav"] as $key=>$value){
            $p->add(array("userid"=>$userid,"nav_id"=>$value));
              
        }
        $this->success("修改成功");
    }
    function insert(){
        $admin=D("admin");
        $admin->create();
        if($admin->add()){
            $this->success("添加成功");
        }else{
            $this->error("添加失败");
        }
    }
    
}