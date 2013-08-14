<?php

class ScommonAction extends Action {

    function _initialize() {
          if(!isset($_SESSION["shop_id"]))
              $this->redirect("Public/login");
       
    }
    function getShopId(){
        return (int)$_SESSION["shop_id"];
    }

     

}