<?php

class ScatAction extends ScommonAction {

    function index() {
        $c = D("cat");
        $data = $c->field('id,c_name,c_desn,concat(c_path,"-",id) abspath')->order('abspath,id asc')->where("shop_id=".$this->getShopId())->select();
        foreach ($data as $key => $val) {
            $num = substr_count($val['abspath'], '-') - 1;
            $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $num);
            $data[$key]['c_name'] = $space . $val['c_name'] . '<br>';
        }
        $this->assign('data', $data);
        $this->display();
    }

    //转入添加类别页
    function add() {
       $c = D("Cat");
        $this->assign("select", $c->select2("pid"));
        $this->display();
    }

    //添加类别
    function insert() {
        
        $c = D('Cat');
        if ($_POST['pid'] == 0) {
            $_POST['c_path'] = 0;
        } else {
            $data = $c->find($_POST['pid']);
            $_POST['c_path'] = $data['c_path'] . '-' . $data['id'];
        }
        $_POST["shop_id"]=$this->getShopId();
        if ($c->add($_POST)) {
            $this->redirect('index');
        } else {
            $this->error('添加类别失败', 3, 'add');
            p('bbbbbbbbbbbbbb');
        }
    }

    //删除类别
    function delete() {
        $c = M('Cat');
        if ($c->where('pid='.(int) $_GET['id'])->select()) {
            $this->error('该类别下面有子类，请先删除子类', 2, index);
        } else {
            $c->where("id=".(int)$_GET['id'])->delete();
            $this->redirect('index');
        }
    }

    //修改类别
    function mod() {
        
        $c = D('cat');
        $data = $c->where('id='. $_GET['id'])->find();
        $this->assign('data', $data);
        $this->display();
    }

    function update() {
        
        $c = D('Cat');
        $c->create();
        if ($c->save()) {
            $this->success('修改类别成功', 'index');
        } else {
             
            $this->redirect("Scat/mod",array("id"=>$_POST["id"]));
        }
    }

}

?>
