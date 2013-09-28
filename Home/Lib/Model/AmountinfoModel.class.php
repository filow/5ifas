<?php

class AmountinfoModel  extends Model {
    function tz($cardn, $amount, $beizhu1) {
        $amountinfo = M("Amountinfo");
        $user = M('user');
        $urow = $user->where(array('cardn' => $cardn))->find();
          
        $uamount = $urow['amount'];

        $time = time();
       
        $row = $amountinfo->add(array(
            "amount" => $amount,
            "username"=>$urow["username"],
            "loginname"=>$urow["loginname"],
            "beizhu" => "透支", 
            "beizhu1" => $beizhu1, 
            "createtime" => $time, 
            'userid' => $cardn, 
            'type' => 0, 
            'orderid' => $time, 
            "type" => 0, 
            "operator" => $_SESSION['username'] . "代透支"));
       
        $row = $user->where(array('cardn' => $cardn))->save(array("amount" => $amount + $uamount));
        return true;
    }
}