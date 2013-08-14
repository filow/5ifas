<?php

class AmountinfoModel  extends Model {

    function czid($orderid) {
        $info = D('info');

        //订单号充值

        $amountinfo = D("amountinfo");
        $time = time();
        if ($amountinfo->where(array("orderid" => $orderid, "type" => 1))->find()) {
            return false;
        }
        $amount = 0;
        $o_order = $amountinfo->where(array("orderid" => $orderid, "type" => 0))->find();
        if ($result = $amountinfo->where(array("orderid" => $orderid, "type" => 0))->save(array("suctime" => $time, "type" => 1, "operator" =>$o_order["operator"]. "/".$_SESSION['username']))) {
            if ($result = $amountinfo->where(array("orderid" => $orderid, "type" => 4))->save(array("suctime" => $time, "type" => 2, "operator" =>$o_order["operator"]."/". $_SESSION['username']))) {
                $result = $amountinfo->where(array("orderid" => $orderid, "type" => 2))->find();
                $amount = $result['amount'];
            }
		 
            $result2 = $amountinfo->where(array("orderid" => $orderid, "type" => 1))->find();
            $dj1 = $info->where("type=4 and value2 <=".$result2['amount'])->order("value2 desc ")->select();
            
            $dj = $dj1[0]['name'];
            $djjf = $dj1[0]['value1'];
            $cardn = $result2['userid'];
            $user = d("user");
            $row = $user->field("amount")->where(array('cardn' => $cardn))->find();
            $amount+=$row['amount'];
            $row1 = $user->field("amount")->where(array('cardn' => $cardn))->save(array("dj" => $dj, "djjf" => $djjf, "amount" => $amount));
            return true;
        } else {
            return false;
        }
    }

    function cz($cardn, $amount, $beizhu1) {
        $amountinfo = D("Amountinfo");
         $user = d("user");
          $row = $user->where(array('cardn' => $cardn))->find();
          
        $info = D("info");
        $fanxian = $info->where("type=2 and value2 <=". $amount)->order('value2 desc')->select();

        if ($fanxian) {
            $fan = $fanxian[0]['value1'];
        } else {
            $fan = 0;
        }

        $amountu = $amount * (1 + $fan);

        $time = time();

        if ($result = $amountinfo->add(array("userid" => $cardn, "beizhu" => "充值","username"=>$row["username"],"loginname"=>$row["loginname"],"beizhu1" => $beizhu1, "orderid" => $time, "createtime" => $time, "amount" => $amount, "suctime" => $time, "type" => 1, "operator" => $_SESSION['username']))) {
          
            $time = time();
            $dj1 = $info->where("type=4 and value2 <= ". $amount)->order("value2 desc ")->find();

            $dj = $dj1['name'];
            $djjf = $dj1['value1'];
            if ($fan != 0) {
                $result1 = $amountinfo->add(array("userid" => $cardn,"username"=>$row["username"],"loginname"=>$row["loginname"], "beizhu" => "返现", "beizhu1" => $beizhu1, "orderid" => $time, "createtime" => $time, "amount" => $amount * $fan, "suctime" => $time, "type" => 2, "operator" => $_SESSION['username']));
            }
            
            $row1 = $user->field("amount")->where(array('cardn' => $cardn))->save(array("amount" => $amountu + $row['amount'], "dj" => $dj, "djjf" => $djjf));
        }
        return true;
    }

    function tz($cardn, $amount, $beizhu1) {
        $amountinfo = D("Amountinfo");
        $info = d("info");
         $user = d('user');
          $urow = $user->where(array('cardn' => $cardn))->find();
          
        $uamount = $urow['amount'];
        $fanxian = $info->where("type=2 and value2<=". $amount)->order('value2 desc')->select();

        if ($fanxian) {
            $fan = $fanxian[0]['value1'];
        } else {
            $fan = 0;
        }

        $time = time();
        if ($fan != 0) {
            $result1 = $amountinfo->add(array("userid" => $cardn,"username"=>$urow["username"],"loginname"=>$urow["loginname"], "beizhu" => "返现", "beizhu1" => $beizhu1, "orderid" => $time, "createtime" => $time, "amount" => $amount * $fan, "suctime" => $time, "type" => 4, "operator" => $_SESSION['username']));
        }
       


        $row = $amountinfo->add(array("amount" => $amount,"username"=>$urow["username"],"loginname"=>$urow["loginname"], "beizhu" => "透支", "beizhu1" => $beizhu1, "createtime" => $time, 'userid' => $cardn, 'type' => 0, 'orderid' => $time, "type" => 0, "operator" => $_SESSION['username'] . "代透支"));
       
        $row = $user->where(array('cardn' => $cardn))->save(array("amount" => $amount + $uamount));
        return true;
    }
    function test(){
        
    }

}