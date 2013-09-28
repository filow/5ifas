<?php

class CommonAction extends Action {

    function _initialize() {

    }
   public function order(){
    /**
     * 侧边栏最新订单生成函数
     * @access public
     * @output array $new_order  包含10条购买信息的二维数组
    */
        if(!S('new_order')){
			$order = M("order");
	        $new_order = $order->limit(10)->order("id desc")->field('info,createtime')->select();
	        foreach ($new_order as $key => $value) {
	            $info = json_decode($value["info"], true);
	            $desn = date("H:i", $value['createtime'])."&nbsp;&nbsp;";
	            foreach ($info as $k => $v) {
	                $desn.=$v["w_name"] . ":" . $v["pnum"] . "份 |";
	            }
	            $new_order[$key]["desn"] = $desn;
	        }
	        S('new_order',$new_order,1800);
        }else{
        	$new_order=S('new_order');
        }
        $this->assign("new_order", $new_order);
   }
   public function inews(){
    /**
     * 侧边栏网站公告生成函数
     * @access public
     * @output array $list  包含10条公告信息的二维数组,以ishot作为优先级排序
    */
	    if(!S('inews')){
		    $news = M('news');
		    $list = $news->limit(10)->where(array("newstype" => 0))->order('ishot desc,createtime desc')->field('title,id,ishot')->select();
		    S('inews',$list,1800);
		}else{
			$list=S('inews');
		}
		$this->assign('list',$list);
	}

}