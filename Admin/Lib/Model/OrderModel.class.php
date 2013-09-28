<?php

class OrderModel extends Model {

    /**
     * 商品购买/代扣方法
     *
     * @param  [array] $cardesc [要购买的商品数组]
     * @param  [date]  $time    [送货日期]
     * @param  [int]   $cardn   [用户卡号]
     * @param  [string]$beizhu  [备注]
     * @param  [] $zk      
     * @return [int]   1        [用户余额不足]
     * @return [int]   2        [扣费成功]
     * @return [int]   3        [没有选取商品]
     * @return [int]   4        [新建订单失败]
     * @return [int]   5        [建立订单商品列表失败]
     * @return [int]   6        [订单建立成功,但用户扣费失败]
     */
    function pay($cardesc, $time, $cardn, $beizhu,$zk) {

        //初始化模型对象
        $order = M('Order');
        $ware = M("ware");
        $user = M("user");
        $o2p = M("order2product");

        $row = $user->find($cardn);
        $insert_data=array(
            'cardn' => $cardn,
            'type' => 1,
            'beizhu' => $beizhu,
            'createtime' => time(),
            'delivertime' => $time,
            'deliveraddress' => $row["address"],
            'delivername' => $row['username'],
            'delivertele' => $row['tele'],
            'sushel' => $row['sushel'],
            'operator' => $_SESSION["username"] . "代扣",
            'zt' => 1,
            'type' => 1   //不匿名type=1 匿名type=2 退款type=0
            );

        $car = $cardesc;

        $sum = 0; //订单总额
        //遍历商品列表
        $result = array();
        foreach ($car as $key => $value) {
            $ware_li = $ware->find($key);
            $sum+=$ware_li["w_price"] * $value*$zk[$key];
            $result[$ware_li["shop_id"]][] = array("w_name" => $ware_li["w_name"], "w_price" => $ware_li["w_price"], "pnum" => $value, "shop_id" => $ware_li["shop_id"], "productid" => $ware_li["id"]);
            $result[$ware_li["shop_id"]]["amount"]+=$ware_li["w_price"] * $value*$zk[$key];
            $result[$ware_li["shop_id"]]["youhui"]+=$ware_li["w_price"] * $value*(1-$zk[$key]);
        }
        //如果总价为0,则返回"没有选择商品"错误
        if ($sum == 0)
           return 3;
        if($row['amount']<$sum)
            return 1;
        
        foreach ($result as $key => $value) {
            $insert_data["order_id"] = $orderid = generateId($key, $insert_data["sushel"]);
            $insert_data["shop_id"] = $key;
            $insert_data["amount"]=$value["amount"];
			$insert_data["youhui"]=$value["youhui"];
             
            unset($value["amount"],$value["youhui"]);

            $insert_data["info"] = json_encode($value);
            if(!$order->add($insert_data)){
                return 4;
            }
            //插入到order2product中
            //type=1 购买 type=2是退货
            foreach ($value as $w_k => $w_v) {
                $w_v["order_id"] = $orderid;
                $w_v["type"] = 1;
                $w_v["cardn"] = $cardn;
                if(!$o2p->add($w_v)){
                    return 5;
                }
            }
        }
		
        if ($user->where(array('id' => $cardn))->setDec('amount',$sum)) {
            return 2;
        }else{
            return 6;
        }

    }

}