<?php

class CommonAction extends Action {

    function _initialize() {
        //判断是否登录
        if(!isset($_SESSION[C('USER_AUTH_KEY')])){
            $this->redirect("Public/login");
        }
 
        import('@.ORG.RBAC');

        //检测是否拥有权限
        if(C('USER_AUTH_ON')){

            //如果是超级管理员,则不验证,且给予所有节点列表
            if(C('RBAC_SUPERADMIN')==session('username')){
                //给予所有权限
                if(!isset($_SESSION['NAV'])){
                    $_SESSION['NAV']=$this->getNavAll();
                }
            }else{
                $auth=RBAC::AccessDecision();
                if(!$auth){
                    $this->error('您没有访问此功能的权限!');
                }else{
                    if(!isset($_SESSION['NAV'])){
                        $_SESSION['NAV']=RBAC::getNav();
                    }
                // print_r($_SESSION['NAV']);die;
                }
            }
        }else{
            if(!isset($_SESSION['NAV'])){
                $_SESSION['NAV']=$this->getNavAll();
            }
        }
        $this->assign("nav",$_SESSION['NAV']);
    }

    function getNavAll(){
        $nodes=M('node');
        $data=array();

        $module=$nodes->where(array('isshow' => 1,'level' => 2))->field('id,name,title')->order('sort')->select();
        foreach($module as $value){
            $action=$nodes->where(array('isshow' => 1,'pid' => $value['id']))
                    ->field('name,title')->order('sort')->select();
            foreach($action as $act_val){
                $tmp[$act_val['name']]=$act_val['title'];
            }
            $data[$value['name']]=$tmp;
            $data[$value['name']]['MODULE_TITLE']=$value['title'];
            unset($tmp);
        }
        
        return $data;
    }

}