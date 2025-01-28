<?php

use Polymorph_Bot\Includes\Wrappers\DB;

class Activator {
    public static function activate()
    {
        self::create_tables();
    }

/*     public static function get_models()
    {
        $models_np = 'Plugin0Name\Src\Classes\Models';
        $classes = get_declared_classes();

        foreach($classes as $class){
            if (strpos($class, $models_np) === 0) {
                $properties = get_class_vars($class);

                $table_name = $properties['table_name'];
                unset($properties['table_name']);

                foreach($properties as $key => $value){
                    $prop_type = gettype($key);

                    switch($prop_type){
                        case 'integer':
                        break;
                        case 'string':
                        break;
                        case 'double':
                        break;
                    }
                }
            }
        }
    } */

    public static function create_tables()
    {
        $tables = array(
            array(
                'name' => 'modelo1',
                'args' => array(
                    'columns' => array(
                        array( 'name' => 'ID', 'type' => 'BIGINT', 'options' => 'AUTO_INCREMENT' ),
                    ),
                    'keys' => array(
                        array( 'type' => 'PRIMARY KEY', 'columns' => 'ID')
                    )
                )
            )
        );

        foreach($tables as $table){
            DB::create_table($table['name'],$table['args']);
        }
    }
}