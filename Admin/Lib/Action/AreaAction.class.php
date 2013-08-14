<?php

class AreaAction extends CommonAction {

    function index() {
        $ban_id=(int)$_GET["banid"];
        $ban=D("ban");
        $ban_data=$ban->select();
        $this->assign("ban_data",$ban_data);
        $Area = D("Area");
        $data = $Area->field('id,area_name,area_desn,concat(area_path,"-",id) abspath')->order('abspath,id asc')->where("ban_id=".$ban_id)->select();
        foreach ($data as $key => $val) {
            $num = substr_count($val['abspath'], '-') - 1;
            $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $num);
            $data[$key]['area_name'] = $space . $val['area_name'] . '<br>';
        }
       
        $this->assign('data', $data);
        $this->display();
    }

    function add() {
        $ban_id=(int)$_GET["banid"];
        $ban=D("ban");
        $ban_data=$ban->select();
        $this->assign("ban_data",$ban_data);
        $Area = D("Area");
        $this->assign("select", $Area->select_list($ban_id));
        $this->display();
    }

    function insert() {
        $Area = M("Area");
        if ($_POST['pid'] == 0) {
            $_POST['area_path'] = 0;
        } else {
            $data = $Area->find($_POST['pid']);
            $_POST['area_path'] = $data['area_path'] . '-' . $data['id'];
        }
        if ($Area->add($_POST))
            $this->success("添加成功");
        else
            $this->error("添加失败");
    }

    function delete() {
        $Area = M('Area');
        if ($Area->where('pid=' . $_GET['id'])->select()) {
            $this->error('该类别下面有子类，请先删除子类');
        } else {
            $Area->where("id =" . $_GET['id'])->delete();
            $this->success("删除成功");
        }
    }

    function mod() {
        $Area = D('Area');
        $data = $Area->where('id=' . $_GET['id'])->find();

        $this->assign('data', $data);
        $this->display();
    }

    function update() {
        $Area = D('Area');
        if ($id = $Area->where("id=" . $_POST['id'])->data($_POST)->save()) {
            $this->success('修改类别成功');
        } else {
            $this->error("修改失败");
        }
    }


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