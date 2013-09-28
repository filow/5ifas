<?php

class AmountinfoModel  extends Model {

    /**
     * 确认某个id的充值信息
     +-----------------------------------
     * 订单type:
     * 0 未收费    1 已收费     2 返现      4 未返现
     +-----------------------------------
     * @param  $orderid [订单号]
     * @return [type]          [description]
     */
    function charge_confirm($orderid) {
        //info:宿舍楼和会员信息
        $info = M('info');

        //订单号充值
        $amountinfo = D("amountinfo");
        $time = time();
        $amount_data=$amountinfo->where(array("orderid" => $orderid))->find();

        //如果已经充值过,则退出
        if ($amount_data['type']==1) {
            return false;
        }

        //将未充值的项目修改为已充值
        $result_charge = $amountinfo->
                  where(array("orderid" => $orderid))->
                  save(array("suctime" => $time, "type" => 1, "operator" =>$amount_data["operator"]. "/".$_SESSION['username']));
        if ($result_charge) {
            //将未返现的项目修改为已返现
            $result_return = $amountinfo->
                      where(array("orderid" => $orderid, "type" => 4))->
                      save(array("suctime" => $time, "type" => 2, "operator" =>$amount_data["operator"]."/". $_SESSION['username']));
            return true;
        } else {
            return false;
        }
    }

    function recharge($cardn, $amount, $beizhu1) {
        $amountinfo = M("Amountinfo");
        $user = M("user");
        $user_info = $user->where(array('cardn' => $cardn))->find();
        
        import('ORG.Util.String');
        $recharge=array("userid" => $cardn,
                        "beizhu" => "充值",
                        "username"=>$user_info["username"],
                        "loginname"=>$user_info["loginname"],
                        "beizhu1" => $beizhu1,
                        "orderid" => 'R'.date("YmdG").strtoupper(string::buildFormatRand("**##")),
                        "createtime" => time(), 
                        "amount" => $amount,
                        "suctime" => time(), 
                        "type" => 1,
                        "operator" => $_SESSION['username']);
        if($amountinfo->add($recharge))
            $result2=$user->where(array('cardn' => $cardn))->setInc('amount',$amount); 
        return $result2;
    }

    function overdraw($cardn, $amount, $beizhu1) {
        $amountinfo = M("Amountinfo");
        $user = M('user');
        $user_info = $user->where(array('cardn' => $cardn))->find();
        import('ORG.Util.String');
        $overdraw=array("amount" => $amount,
                        "username"=>$user_info["username"],
                        "loginname"=>$user_info["loginname"],
                        "beizhu" => "透支", 
                        "beizhu1" => $beizhu1, 
                        "createtime" => time(), 
                        'userid' => $cardn, 
                        'type' => 0, 
                        "suctime" => time(), 
                        'orderid' => 'O'.date("YmdG").strtoupper(string::buildFormatRand("**##")),
                        "operator" => $_SESSION['username'] . "代透支");
        if($amountinfo->add($overdraw))
            $result2=$user->where(array('cardn' => $cardn))->setInc('amount',$amount); 
        return $result2;
    }

}