<?php

class TestAction extends Action {

    public function index() {

        $this->display();
    }
    function cookie(){
        print_r($_COOKIE);
    }
   FUNCTION session(){
        print_r($_SESSION);
    }
	function track(){
		track("sssssssssss");
	}
	function lookKv(){
	header("content-type:text/html;charset=utf-8;");
		$kv = new SaeKV();
		$ret = $kv->init();
		$ret = $kv->pkrget('', 100);
		print_r($ret);
	}
        function pop(){
            $this->display();
            
        }

}