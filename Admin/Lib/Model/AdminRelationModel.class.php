<?php
/**
 * 用户与角色关联模型
 */
class AdminRelationModel extends RelationModel {
    //主表名称
    protected $tableName = 'admin';

    //定义关联关系
    protected $_link = array(
        'role' => array(
            'mapping_type' => MANY_TO_MANY,
            'foreign_key' => 'user_id',
            'relation_key' => 'role_id',
            'relation_table' => 'think_role_user',
            'mapping_fields' => 'remark'
            )
        );
}