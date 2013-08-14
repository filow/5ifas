<?php

class AreaModel extends Model {

    function select_list($ban_id,$name = "pid", $value = "0") {
        $data = $this->field('id,area_name,concat(area_path,"-",id) abspath')->order("abspath,id asc")->where("ban_id=".$ban_id)->select();
        $html = '<select name="' . $name . '">';
        $html.='<option value="0">根分类</option>';
        foreach ($data as $val) {
            if ($value == $val["id"])
                $html.='<option selected value="' . $val['id'] . '">';
            else
                $html.='<option value="' . $val['id'] . '">';

            $num = count(explode("-", $val["abspath"])) - 2;
            $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $num);
            $name = $val["area_name"];
            $html.=$space . "|--" . $name;
            $html.='</option>';
        }
        $html.='</select>';

        return $html;
    }

}