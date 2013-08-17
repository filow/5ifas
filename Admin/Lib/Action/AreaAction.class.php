<?php

class AreaAction extends CommonAction {

    //以下是宿舍管理部分
    function dorm(){
        $info=M('info');
        if(!empty($_GET['action']) && !empty($_GET['id'])){
            $item=$info->where(array('id'=>$_GET['id']))->find();
            if($_GET['action']=="up"){
                if($item['order']>1){
                    $changed_data['order']=$item['value']-1;
                }else{
                    $changed_data['order']=1;
                }
                $info->where(array('id'=>$_GET['id']))->save($changed_data);
            }
            if($_GET['action']=="down"){
                $changed_data['order']=$item['order']+1;
                $info->where(array('id'=>$_GET['id']))->save($changed_data);
            }
            if($_GET['action']=="delete"){
                $info->where(array('id'=>$_GET['id']))->delete();
            }
        }
        $data=$info->where('type=1')->order("`order`")->field('id,name,order')->select();
        $this->assign('data',$data);
        $this->display();
    }
    function dorm_edit(){
        $info=M('info');
        if(!empty($_GET['dormname']) && !empty($_GET['id'])){
            $data['name']=$this->_get('dormname');
            $info->where(array('id' => $this->_get('id')))->save($data);
            $this->redirect('Area/dorm');
        }else{
            $this->error("参数错误");
        }
    }
    function dorm_add(){
        $info=M('info');
        if(!empty($_GET['dormname']) && !empty($_GET['order'])){
            $data['type']=1;
            $data['name']=$this->_get('dormname');
            $data['order']=$this->_get('order');
            $info->add($data);
            $this->redirect('Area/dorm');
        }else{
            $this->error("你没有写全，请再试一次");
        }
    }
}