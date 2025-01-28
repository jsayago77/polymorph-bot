<?php

namespace Polymorph_Bot\Includes\Wrappers;

class DB
{

    private $db;

    private array $query;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    public function select($columns)
    {
        $this->query['select'] = $columns;

        return $this;
    }

    public function where($operator, string $column, string $condition, $value)
    {
        $where = [];

        $where = array(
            'operator' => ($operator == null) ? '' : $operator,
            'column' => $column,
            'condition' => $condition,
            'value' => $value
        );

        if (is_string($value)) {
            $where['placeholder'] = '%s';
        } elseif (is_numeric($value)) {
            $where['placeholder'] = '%d';
        } elseif (is_float($value) || is_double($value)) {
            $where['placeholder'] = '%f';
        }

        if ($condition === 'LIKE')
            $where['value'] = "%" . $this->db->esc_like($value) . "%";

        $this->query['where'][] = $where;

        return $this;
    }

    public function group_by(string $value)
    {
        $this->query['group_by'] = $value;

        return $this;
    }

    public function join($type_join, $table, $condition)
    {
        $this->query['join'][] = array(
            'type' => $type_join,
            'table' => $table,
            'condition' => $condition
        );

        return $this;
    }

    /**
     * The function creates a new instance of DB class and sets the table name for a database query.
     * 
     * @param string $table name of the database table
     * 
     * @return object $instance instance of DB class
     */
    public static function table(string $table)
    {
        $instance = new self;
        $instance->query['table'] = $instance->db->prefix . $table;

        return $instance;
    }

    public function format_query()
    {
        $query_string = "";

        if ($this->query['select'] === null)
            $query_string = " SELECT *";
        else
            $query_string = "SELECT " . $this->query['select'];

        if ($this->query['table'] === null)
            return "ERROR";
        else
            $query_string .= " FROM " . $this->query['table'];

        if ($this->query['join'] !== null) {
            $join = $this->query['join'];

            foreach ($join as $j) {
                if (!empty($j)) {
                    $query_string .= " {$j['type']} {$j['table']} ON {$j['condition']}";
                }
            }
        }

        if ($this->query['where'] !== null) {
            $where = $this->query['where'];

            $where_conditions = array();
            $where_values = array();

            foreach ($where as $w) {
                if (!empty($w)) {
                    $where_conditions[] = "{$w['operator']} {$w['column']} {$w['condition']} {$w['placeholder']}";
                    $where_values[] = $w['value'];
                }
            }

            if (!empty($where_conditions)) {
                $query_string .= " WHERE " . implode(" ", $where_conditions);
                $query_string = $this->db->prepare($query_string, $where_values);
            }
        }

        if ($this->query['group_by'] !== null)
            $query_string .= " GROUP BY " . $this->query['group_by'];

        return $query_string;
    }

    /**
     * Execute query and get results
     *
     * @param string $type
     * @param string $output
     * @return array|object|null the results
     */
    public function get( $type = null, $output = OBJECT )
    {
        $query_string = self::format_query();

        if($type === 'row')
            return $this->db->get_row($query_string, $output);
        elseif($type === 'col')
            return $this->db->get_col($query_string, $output);
        else
            return $this->db->get_results($query_string, $output);
    }

    public function exec()
    {
        $query_string = self::format_query();
        return $this->db->query($query_string);
    }

    public static function create($table, $data)
    {
        $instance = new self;
        $table_name = $instance->db->prefix . $table;

        $instance->db->insert($table_name, $data);
        return $instance->db->insert_id;
    }

    public static function update($table, $data, $where)
    {
        $instance = new self;
        $table_name = $instance->db->prefix . $table;

        $instance->db->update($table_name, $data, $where);
        return $instance->db->rows_affected;
    }

    public static function delete($table, $where)
    {
        $instance = new self;
        $table_name = $instance->db->prefix . $table;

        $instance->db->delete($table_name, $where);
        return $instance->db->rows_affected;
    }

    public static function create_table(string $table, array $args)
    {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $columns_sql = [];
        foreach($args['columns'] as $column){
            $columns_sql[] = "{$column['name']} {$column['type']} {$column['options']}";
        }
        $columns_str = implode(",", $columns_sql);

        if($args['keys']){
            $keys_sql = [];
            foreach($args['keys'] as $key){
                $ref = $key['reference'] ? ("REFERENCES" . $key['reference']) : '';

                $keys_sql[] = "{$key['type']} ({$key['columns']}) $ref";
            }

            $keys_str = implode(",", $keys_sql);

            $sql = "CREATE TABLE IF NOT EXISTS $table($columns_str, $keys_str);";
        } else 
            $sql = "CREATE TABLE IF NOT EXISTS $table($columns_str);";

        dbDelta( $sql );
    }

    public static function alter_table(string $table, array $args)
    {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        switch($args['operation']){
            case 'DROP COLUMN':
                $sql = "ALTER TABLE $table DROP COLUMN {$args['column_name']}";
                break;
            case 'ADD':
                $sql = "ALTER TABLE $table ADD {$args['column_name']} {$args['data_type']}";
                break;
            case 'MODIFY':
                $sql = "ALTER TABLE $table MODIFY {$args['column_name']} {$args['data_type']}";
                break;
            default:
                return 'Bad operation';
        }

        dbDelta( $sql );
    }

    public static function empty_table(string $table)
    {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $sql = "TRUNCATE TABLE $table;";

        dbDelta( $sql );
    }

    public static function delete_table(string $table)
    {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $sql = "DELETE TABLE $table;";

        dbDelta( $sql );
    }
}
