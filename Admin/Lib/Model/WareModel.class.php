<?php
 class WareModel extends RelationModel{
     
    public $_link=array(
          
           "ss"=>array(
                "class_name"=>"Shop",
                "mapping_type"=>BELONGS_TO,
                "foreign_key"=>"id",
                "parent_key"=>"shop_id",
               
                 
               
                 
               
            ),
     );
 }