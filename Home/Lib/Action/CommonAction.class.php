<?php

class CommonAction extends Action {

    function _initialize() {
      //暂时不启用友情链接
           // $this->assign("link",M("link")->select());
           $ad_data=D("ad")->order("id desc ")->select();
           $this->assign("ad_data",$ad_data);
           $this->assign("nav", M("ban")->order("ban_order desc ,id asc ")->select());
    }
    function getArea($ban_id){
          $Area=D("Area");
          $area_data=$Area->where("pid=0 and ban_id=".$ban_id)->select();
         // z();
          $this->assign("area_data",$area_data);
    }
   public function order(){
    /**
     * 侧边栏最新订单生成函数
     * @access public
     * @output array $new_order  包含10条购买信息的二维数组
    */
        $order = M("order");
        $new_order = $order->limit(10)->order("id desc")->select();
        foreach ($new_order as $key => $value) {
            $info = json_decode($value["info"], true);
            $desn = date("H:i", $value['createtime'])."&nbsp;&nbsp;";
            foreach ($info as $k => $v) {
                $desn.=$v["w_name"] . ":" . $v["pnum"] . "份 |";
            }
            $new_order[$key]["desn"] = $desn;
        }
        $this->assign("new_order", $new_order);
   }
   public function inews(){
    /**
     * 侧边栏网站公告生成函数
     * @access public
     * @output array $list  包含10条公告信息的二维数组,以ishot作为优先级排序
    */
      $news=M('news');
      $list_hot = $news->limit(10)->where(array("newstype" => 0, "ishot" => 1))->order('ishot desc,createtime desc')->select();
      $list_normal = $news->limit(10)->where(array("newstype" => 0, "ishot" => 0))->order('createtime desc ')->select();
      $list=array();
      for($i=0,$count=0;$count<=10;){
        if(!empty($list_hot[$i])){
          $list[$count++]=$list_hot[$i++];
          $end_count=$count;
        }else{
          $list[$count]=$list_normal[$i++-$end_count];
          $count++;
        }
      }
      $this->assign('list',$list);
   }

}