<?PHP
 class BanAction extends CommonAction{
     function index(){
         $ban=D("Ban");
         $data=$ban->order("ban_order desc ,id asc")->select();
        // z();
         $this->assign("data",$data);
         $this->display();
     }
     function add(){
         $this->display();
     }
     function insert(){
          $ban=D("Ban");
          $ban->create();
          if($ban->add()){
              $this->success("添加板块成功");
          }else{
              $this->error("添加板块失败");
          }
     }
     function mod(){
         $ban=D("ban");
         $id=(int)$_GET["id"];
         $data=$ban->find($id);
        // print_r($data);
         $this->assign("data",$data);
         $this->display();
      }
      function delete() {
        $ban = M("ban");
        if($ban->where("id=".(int)$_GET["id"])->delete()) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }
     function update(){
         $ban = M("ban");
         if(!$ban->create()){
               $this->error("修改失败");
         }
        if($ban->where("id=".(int)$_POST["id"])->save()) {
            $this->success("修改成功","index");
        } else {
            $this->error("修改失败");
        }
    }
    /**************************************上下移位排序****************************/
    function up(){
        $id=(int)$_GET["id"];
        $ban=D("ban");
        $row=$ban->find($id);
        $ban_order=$row["ban_order"];//目前的order
        $next_data=$ban->where("ban_order >=".$ban_order." and id !=".$row["id"])->order("ban_order asc ")->find();
        $next_order=$next_data["ban_order"];//需要换位的order
        if($next_order==$ban_order)
            $next_order+=1;
        if($ban->where("id=".$id)->setField("ban_order",$next_order)){
            $ban->where("id=".$next_data["id"])->setField("ban_order",$ban_order);
            $this->redirect("Ban/index");
        }else{
            $this->error("已经排在首位，无效操作");
        }
    }
    function down(){
         $id=(int)$_GET["id"];
        $ban=D("ban");
        $row=$ban->find($id);
        $ban_order=$row["ban_order"];//目前的order
        $next_data=$ban->where("ban_order <=".$ban_order." and id !=".$row["id"])->order("ban_order desc ")->find();
        $next_order=$next_data["ban_order"];//需要换位的order
        if($next_order==$ban_order)
            $ban_order+=1;
        if( $ban->where("id=".$next_data["id"])->setField("ban_order",$ban_order)){
          $ban->where("id=".$id)->setField("ban_order",$next_order);
            $this->redirect("Ban/index");
        }else{
            $this->error("已经排在最后一位无效操作");
        }
    }
 }