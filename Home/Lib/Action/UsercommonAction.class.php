<?php

class UsercommonAction extends Action {

    function _initialize() {
        if (!isset($_SESSION["cardn"]))
            $this->redirect("Public/login");
        $this->assign("link", M("link")->select());
        $Area = D("Area");
        $area_data = $Area->where("pid=0")->select();
        $this->assign("area_data", $area_data);
        $this->assign("nav", M("cat")->where("pid=0 and shop_id=0")->select());
        $this->assign("link", M("link")->select());
        $ad_data = D("ad")->order("id desc ")->select();
        $this->assign("ad_data", $ad_data);
        $this->assign("nav", M("ban")->order("ban_order desc ,id asc ")->select());
    }

    function getArea($ban_id) {
        $Area = D("Area");
        $area_data = $Area->where("pid=0 and ban_id=" . $ban_id)->select();
        // z();
        $this->assign("area_data", $area_data);
    }

}