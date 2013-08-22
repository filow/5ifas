<?php
/**
 * RBAC角色权限控制类
 */
class RbacAction extends CommonAction {
    public function Index()
    {
        $this->display();
    }

    public function Node()
    {
        $nodes=M('node');
        $data=$nodes->order('sort,id')->select();
        $data=node_merge($data);
        //print_r($data);
        $this->assign("nodes",$data);
        $this->display();
    }

    public function Node_ctrl_add()
    {
        $this->max=M('node')->max('sort')+1;
        $this->display();
    }

    public function Node_ctrl_add_handle()
    {
        if(!IS_POST) $this->error('非法提交数据');
        $ctrl=D('RbacCtrl');
        
        $result=$ctrl->create($_POST);
        if(!$result)
            $this->error($ctrl->getError());
        $result['level']=2;
        if($ctrl->add($result))
        {
            $this->success('添加成功',U('Rbac/Node'));
        }else{
            $this->error('添加失败');
        }
    }
    public function node_edit()
    {
        if(!isset($_GET['id'])) $this->error('没有操作对象');
        $this->info=M('node')->where(array('id' => $this->_get('id') ))->find();
        $this->display();

    }
    public function Node_edit_handle()
    {
        if(!IS_POST) $this->error('非法提交数据');
        $edit=D('RbacCtrl');
        $node=$edit->create($_POST);
        if(!$node) $this->error('数据格式错误');
        else{
            $node["id"]=intval($node["id"]);
            $node["pid"]=intval($node["pid"]);
            $node["level"]=intval($node["level"]);

            $edit->save($node)? $this->success('操作成功',U('node')):$this->success('操作失败');

        }
    }
    public function node_delete()
    {
        if(isset($_GET['id']) && is_numeric($_GET['id'])){
            $node=M('node');
            if($node->where(array('pid' => $_GET['id']))->count()) $this->error('不能删除含有子节点的项目');

            $result=$node->where(array('id' => $_GET['id']))->limit(1)->delete();

            if($result){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }

        }else{
            $this->error('操作非法');
        }
    }
    public function node_child()
    {
        if(!isset($_GET['pid'])) $this->error('没有指定父项目');
        $this->max=M('node')->where(array('pid' => $this->_get('pid')))->max('sort')+1;
        //z();die;
        $this->pid=intval($_GET['pid']);
        $this->display();
    }
    public function Node_child_handle(){
        if(!IS_POST) $this->error('非法提交数据');
        $edit=D('RbacCtrl');
        $node=$edit->create($_POST);
        if(!$node) $this->error('数据格式错误');
        else{
            $node["pid"]=intval($node["pid"]);
            $node["level"]=3;

            $edit->add($node)? $this->success('操作成功',U('node')):$this->success('操作失败');
        }
    }
    public function Role()
    {
        $this->display();
    }

}

?>