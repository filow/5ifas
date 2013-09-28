<?php

class UsercommonAction extends Action {

    function _initialize() {
        if (!isset($_SESSION["cardn"]))
            $this->redirect("Public/login");
    }
}