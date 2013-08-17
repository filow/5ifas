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
        $this->display();
    }

    public function Node_ctrl_add()
    {
        $this->display();
    }

    public function Node_ctrl_add_handle()
    {
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

    public function Role()
    {
        $this->display();
    }

}

?>