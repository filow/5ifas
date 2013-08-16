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
        var_dump($_POST);
    }

    public function Role()
    {
        $this->display();
    }

}

?>