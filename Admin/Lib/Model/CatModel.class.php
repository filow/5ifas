<?php

class CatModel extends Model {

    function select1($name = "cid", $value = "0", $get = array()) {
        $w = D('ware');
        if (isset($get['id'])) {
            $data1 = $w->where(array('id' => $get['id']))->find();
        } else {
            $data1['w_cat'] = "";
        }

        $data = $this->field('id,c_name,concat(c_path,"-",id) abspath')->where("shop_id=" . (int) $_SESSION["shop_id"])->order("abspath,id asc")->select();

        $html = '<select name="' . $name . '">';
        $html.='<option value="0">根分类</option>';
        foreach ($data as $val) {
            if ($data1['w_cat'] == $val["abspath"])
                $html.='<option selected value="' . $val['abspath'] . '">';
            else
                $html.='<option value="' . $val['abspath'] . '">';

            $num = count(explode("-", $val["abspath"])) - 2;
            $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $num);
            $name = $val["c_name"];
            $html.=$space . "|--" . $name;
            $html.='</option>';
        }
        $html.='</select>';

        return $html;
    }

    function select2($name = "pid", $value = "0") {
        $data = $this->field('id,c_name,concat(c_path,"-",id) abspath')->order("abspath,id asc")->where("shop_id=" . (int) $_SESSION["shop_id"])->select();
        $html = '<select name="' . $name . '">';
        $html.='<option value="0">根分类</option>';
        foreach ($data as $val) {
            if ($value == $val["id"])
                $html.='<option selected value="' . $val['id'] . '">';
            else
                $html.='<option value="' . $val['id'] . '">';

            $num = count(explode("-", $val["abspath"])) - 2;
            $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $num);
            $name = $val["c_name"];
            $html.=$space . "|--" . $name;
            $html.='</option>';
        }
        $html.='</select>';

        return $html;
    }
    /***************帮商家添加东西****************************/
     function select3($name = "cid",$shop_id, $value = "0", $get = array()) {
        $w = D('ware');
        if (isset($get['id'])) {
            $data1 = $w->where(array('id' => $get['id']))->find();
        } else {
            $data1['w_cat'] = "";
        }

        $data = $this->field('id,c_name,concat(c_path,"-",id) abspath')->where("shop_id=" .$shop_id)->order("abspath,id asc")->select();

        $html = '<select name="' . $name . '">';
        $html.='<option value="0">根分类</option>';
        foreach ($data as $val) {
            if ($data1['w_cat'] == $val["abspath"])
                $html.='<option selected value="' . $val['abspath'] . '">';
            else
                $html.='<option value="' . $val['abspath'] . '">';

            $num = count(explode("-", $val["abspath"])) - 2;
            $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $num);
            $name = $val["c_name"];
            $html.=$space . "|--" . $name;
            $html.='</option>';
        }
        $html.='</select>';

        return $html;
    }

}

?>
