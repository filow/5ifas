<?php
/**
 * RBAC角色权限控制类
 */
class RbacAction extends CommonAction {
    public function Index()
    {
        $adminRelation=D('AdminRelation')->field('id,username,phone,isdelete,tc_perc,bx_perc')->relation(true)->select();
     //   print_r($adminRelation);
     //   die;
        $this->assign('admin',$adminRelation);
        $this->display();
    }
    public function user_mod()
    {
        //查询用户是否存在
        $uid=I('id',0,'intval');
        $admin=M('admin');
        $admin_user=$admin->where(array('id'=>$uid))->find();
        if(!$admin_user) $this->error('id不存在');
        $this->assign('admin',$admin_user);
        //提取角色列表
        $role=M('role');
        $role_data=$role->where(array('status' => 1))->field('id,remark')->select();
        $this->assign('role',$role_data);

        $this->display();
    }
    public function User_mod_handle()
    {
        headerutf8();
        //密码处理
        $data=$this->_post();
        if($data['password']){
            $data['password']=md5($data['password']);
        }else{
            unset($data['password']);
        }
        //角色处理
        $uid=$data['id'];
        foreach($data['role'] as $k => $v){
            $role_data[$k]['user_id']=$uid;
            $role_data[$k]['role_id']=$v;
        }
        unset($data['role']);  //释放role键
        //删除所有原用户角色
        $role_user=M('role_user');
        $role_user->where(array('user_id'=>$uid))->delete();
        //添加角色信息
        if($role_user->addAll($role_data)){
            $msg="职位变更成功";
            $success=1;
        }else{
            $msg="职位变更失败";
            $success=0;
        }
        //变更用户信息
        $admin=M('admin');
        if($admin->save($data)){
            $msg.="<br />用户信息变更成功";
            $success=$success|1;
        }else{
            $msg.="<br />用户信息变更失败";
            $success=$success|0;
        }
        if($success) $this->success($msg,U('index'));
        else $this->error($msg);
    }
    public function user_add()
    {
        $role=M('role');
        $role_data=$role->where(array('status' => 1))->field('id,remark')->select();
        $this->assign('role',$role_data);
        $this->display();
    }
    public function user_add_handle()
    {
        //信息处理
        if(!($_POST['username'] && $_POST['password']))
            $this->error('必须输入用户名和密码');
        $data['username']=$_POST['username'];
        $data['password']=md5($data['password']);
        $data['isdelete']=I('isdelete',0,'intval');
        $data['phone']=$_POST['phone'];
        $data['tc_perc']=I('tc_perc',0,'intval');
        $data['bx_perc']=I('bx_perc',0,'intval');
        $data['utime']=time();
        //添加用户
        $admin=M('admin');
        if(!$uid=$admin->add($data)){
            $this->error('用户添加失败');
        }

        //角色处理
        foreach($_POST['role'] as $k => $v){
            $role_data[$k]['user_id']=$uid;
            $role_data[$k]['role_id']=$v;
        }
        //添加角色信息
        $role_user=M('role_user');
        if($role_user->addAll($role_data)){
            $this->success('用户添加成功',U('index'));
        }else{
            $this->error('用户添加成功,但未能写入职位信息');
        }

    }
    public function user_delete()
    {
        $uid=I('id',0,'intval');
        $admin=M('admin');
        if($admin->where(array('id'=> $uid))->delete()){
            M('role_user')->where(array('user_id' => $uid))->delete();
            $this->success('删除成功',U('index'));
        }
        else $this->error('删除失败');
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
                M('access')->where(array('node_id'=>$_GET['id']))->delete();
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

    //角色管理
    public function Role()
    {
        $role=M('role');
        $data=$role->select();
        $this->assign('data',$data);
        $this->display();
    }
    public function role_add()
    {
        $info['status']=1;
        $this->assign('info',$info);
        $this->display();
    }
    public function Role_add_handle()
    {
        $role=M('role');
        if($role->add($this->_post())){
            $this->success('添加成功',U('Role'));
        }else{
            $this->error('添加失败');
        }
    }
    public function role_edit(){
        $role=M('role');
        $info=$role->where(array('id' => $_GET['id']))->find();
        $this->assign('info',$info);
        $this->assign('edit',1);
        $this->display('role_add');
    }
    public function role_edit_handle(){
        $role=M('role');
        if($role->save($_POST)){
            $this->success('修改成功',U('Role'));
        }else{
            $this->error('修改失败');
        }

    }

    public function role_delete()
    {
        if(isset($_GET['id']) && is_numeric($_GET['id'])){
            $role=M('role');
            $result=$role->where(array('id' => $_GET['id']))->limit(1)->delete();
            if($result){
                M('access')->where(array('role_id'=>$_GET['id']))->delete();
                M('role_user')->where(array('role_id'=>$_GET['id']))->delete();
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('操作非法');
        }

    }

    //角色权限配置
    public function access()
    {
        $rid=I('rid',0,'intval');
        //读取可用节点列表
        $nodes=M('node');
        $data=$nodes->where(array('status' => 1))->order('sort,id')->field('id,title,pid,isshow')->select();
        
        //读取权限信息
        $access=M('access');
        $acc_data=M('access')->where(array('role_id'=>$rid))->order('node_id')->getField('node_id',true);

        // 检测当前用户是否拥有该节点权限
        foreach($data as $k => $v){
            if(in_array($v['id'], $acc_data)){
                $data[$k]['acc_owned']=1;
            }
        }

        //将数组整合成树形结构,利于输出
        $data=node_merge($data);
        $this->assign('rid',$rid);
        $this->assign('nodes',$data);
        $this->display();
    }
    public function access_handle()
    {

        $rid=I('rid',0,'intval');
        if(!$rid) $this->error('没有传入rid');

        $access=M('access');
        $access->where(array('role_id' => $rid))->delete();
        $data=array();
        foreach($_POST['access'] as $v){
            $tmp=explode('_', $v);
            $data[]=array(
                'role_id' => $rid,
                'node_id' => $tmp[0],
                'level' => $tmp[1]
                );
        }

        if($access->addAll($data)){
            $this->success('修改成功',U('role'));
        }else{
            $this->error('修改失败');
        }
    }
    
    //
}

?>