<?php

class IndexAction extends Action {
    public function index(){
    	
        $this->assign("title","标题");
        $this->display();
    }
}