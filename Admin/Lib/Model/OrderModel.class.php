<?php

class OrderModel extends Model {

    function pay($cardesc, $time, $cardn, $beizhu,$zk) {

        $order = D('Order');
        $if = d("info");
        $ware = d("ware");
        $user = d("user");
        $o2p = d("order2product");
        $input_jf = 0; //用户输入的积分
        $row = $user->find($cardn);
        $insert_data['cardn']=$cardn;
       $insert_data["type"]=1;
        $insert_data['beizhu']=$beizhu;
        $insert_data['createtime'] = time();
        $insert_data['delivertime'] = $time;
        $insert_data['deliveraddress'] = $row["address"];
        $insert_data['delivername'] = $row['username'];
        $insert_data['delivertele'] = $row['tele'];
		$insert_data['sushel'] = $row['sushel'];
        $insert_data["operator"] = $_SESSION["username"] . "代扣";
        $insert_data['zt'] = 1;
        //不匿名type=1 匿名type=2 退款type=0
        $insert_data['type'] = 1;
        $car = $cardesc;
        $result = array();

        $sum = 0; //订单总额
        $jf_sum = 0; //积分总额
        foreach ($car as $key => $value) {
            $ware_li = $ware->find($key);
            $sum+=$ware_li["w_price"] * $value*$zk[$key];
            $jf_sum+=$ware_li["price"] * $value;
            $result[$ware_li["shop_id"]][] = array("w_name" => $ware_li["w_name"], "w_price" => $ware_li["w_price"], "pnum" => $value, "shop_id" => $ware_li["shop_id"], "productid" => $ware_li["id"]);
            $result[$ware_li["shop_id"]]["amount"]+=$ware_li["w_price"] * $value*$zk[$key];
            $result[$ware_li["shop_id"]]["youhui"]+=$ware_li["w_price"] * $value*(1-$zk[$key]);
            if($ware_li["send_sms"])
                $result[$ware_li["shop_id"]]["send_sms"]=1;
        }
        $input_jf = $input_jf <= $jf_sum ? $input_jf : $jf_sum; //如果输入的积分不多于能用的积分则使用输入的积分，否则使用能用的积分
        /*         * *****如果是登录用户，则需要判断jf是足够，并且读取钱换分的比例**************************** */
        $u_data = array();
        // echo $sum;
        if (isset($cardn)) {
            $dj = $if->where(array("type" => 5))->find();
            $jf_money = $dj['value1']; //积分不足时，每个积分抵多少元
            $u_data = $user->find($cardn);
            $u_jf = $u_data["jf"];
            $input_jf = $input_jf <= $u_jf ? $input_jf : $u_jf;
            //  echo $jf_sum-$input_jf;
            $sum = $sum + ($jf_sum - $input_jf) * $jf_money;
            if($sum>$u_data["amount"])
                return 1;
        }
        if ($sum == 0)
           return 4;
         $shop=D("shop");
		 $error_msg="";
        foreach ($result as $key => $value) {
            $shop_data=$shop->find($key);
             $insert_data["order_id"] = $orderid = generateId($key, $insert_data["sushel"]);
             $insert_data["shop_id"] = $key;
             $insert_data["amount"]=$value["amount"];
			  if($shop_data["service_price"]>=$value["amount"]){
				$error_msg.=$shop_data["shop_name"]."不满足起送价格".$shop_data["service_price"];
				$sum=$sum-$value["amount"];
				continue;
			 }
			 $insert_data["youhui"]=$value["youhui"];
             $msg=$insert_data["amount"]."元 ".$insert_data["delivername"].":".$insert_data["deliveraddress"]."  ".$insert_data["delivertele"];
             
              $msg.="已付费 ";
              
             $msg.="订单详情:";
            unset($value["amount"],$value["youhui"]);
			 $s_sum=0;//商家总数量
			  foreach($value as $k=>$v){
                      
					 $s_sum+=$v["pnum"];
              }
			  if($shop_data["service_num"]>$s_sum){
				$error_msg.=$shop_data["shop_name"]." 不满足起送数量".$shop_data["service_num"];
				$sum=$sum-$value["amount"];
				continue;
			 }
            if($value["send_sms"]){
                unset($value["send_sms"]);
                foreach($value as $k=>$v){
                     $msg.=$v["w_name"] . ":" . $v["pnum"] . "份|";
                }
                $msg.=" 送货时间：".$insert_data["delivertime"];
                $msg.="备注:".$beizhu;
                 sendSms($shop_data["tele"], $msg);
                 sendSms($insert_data["delivertele"], "您的订单已生效，若您购买了优惠券，请在消费时出示订单号.您可登陆www.5ifas.com查询你的消费记录。艾星网络祝您生活愉快!");
               
            }
            
            $insert_data["info"] = json_encode($value);
            $order->create();
            $order->add($insert_data);
            /*             * ***插入到order2product中********* */
            /**
             * order2product type=1 购买 type=2是退货
             *  
             */
            foreach ($value as $w_k => $w_v) {
                $w_v["order_id"] = $orderid;
                $w_v["type"] = 1;
                $w_v["cardn"] = $cardn;
                $o2p->add($w_v);
            }
        }
        /*         * *******************积分的计算开始**************************** */
       
        /*         * ***********先计算每消费一元产生的积分***************** */
        /*         * **********
         * 积分 type=1得到积分 type=2消费积分
         */
		
        if ($user->save(array("id" => $cardn, "amount" => $u_data["amount"] - $sum, "jf" => $u_data["jf"] - $input_jf + $sum * $u_data["djjf"]))) {
            /*             * ******************************更新jf表****************************** */
            //z();
            $jf = D("jf");
            if ($u_data["djjf"] >= 0.0001)
                $jf->add(array("cardn" => $cardn, "type" => 1, "amount" => $sum * $u_data["djjf"], "desn" => date("Y-m-d H:i", time()) . "购买商品得到的积分"));
            if ($input_jf >= 0.00001)
                $jf->add(array("cardn" => $cardn, "type" => 2, "amount" => $input_jf, "desn" => date("Y-m-d H:i", time()) . "购买商品使用的积分"));
        
            // z();
            
        }
		if($error_msg==""){
			 return 2 ;
	   }else{
			return $error_msg;
	   }
		 
    }

}