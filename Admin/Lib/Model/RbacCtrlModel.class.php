<?php
class RbacCtrlModel extends Model
{
    protected $tableName = 'node'; 
    protected $_validate = array(
        array('name','require','名称不符合要求',Model::MUST_VALIDATE),
        array('title','require','必须输入项目描述',Model::MUST_VALIDATE),
        array('status','number','确认开启项只能是一个数字值',Model::MUST_VALIDATE,'regex'), 
        array('sort','number','排序必须是1位以上数字',Model::MUST_VALIDATE), 
    );


}
