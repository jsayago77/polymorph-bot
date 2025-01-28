<?php

namespace Polymorph_Bot\Includes\Wrappers;

class Meta
{
    public static function create_meta( int $id, string $context, string $key, $value, bool $unique)
    {
        switch ($context) {
            case 'post':
                add_post_meta( $id, $key, $value, $unique );
                break;
            case 'user':
                add_user_meta( $id, $key, $value, $unique );
            default:
                return "Bad context";
                break;
        }
    }
    public static function get_meta(int $id, string $context, string $key, bool $single)
    {
        switch ($context) {
            case 'post':
                get_post_meta( $id, $key, $single );
                break;
            case 'user':
                get_user_meta( $id, $key, $single );
            default:
                return "Bad context";
                break;
        }
    }

    public static function update_meta(int $id, string $context, string $key, $value)
    {
        switch ($context) {
            case 'post':
                update_post_meta( $id, $key, $value );
                break;
            case 'user':
                update_user_meta( $id, $key, $value );
            default:
                return "Bad context";
                break;
        }
    }

    public static function delete_meta( int $id, string $context, string $key )
    {
        switch ($context) {
            case 'post':
                delete_post_meta( $id, $key );
                break;
            case 'user':
                delete_user_meta( $id, $key );
            default:
                return "Bad context";
                break;
        }
    }

    public static function get_select_input($field_options, $actual_value)
    {
        $options = $field_options['options'];

        $html = '';
        foreach($options as $option){
            $selected = ($actual_value == $option['value']) ? 'selected' : '';
            $html .= '<option value="'.$option['value'].'" '.$selected.'>'.$option['name'].'</option>';
        }

        return '<select class="'.$field_options['classes'].'" name="'.$field_options['name'].'" id="'.$field_options['id'].'">'.$html.'</select>';
    }

    public static function get_meta_html(int $id, string $context, string $key, bool $single, array $field_options) 
    {
        $field_html = '';

        if($field_options['nonce']) {
            $field_html .= wp_nonce_field( $field_options['name'], $field_options['name'] . '_nonce' );
        }

        $field_html .= '<label for="'.$field_options['id'].'">'._($field_options['label']).'</label>';

        $meta = self::get_meta( $id, $context, $key, $single );
        if(is_array($meta)){
            $name = $field_options['name'];
            $meta = array_pop(array_filter($meta, function ($clave) use ($name) {
                return strpos($name, $clave) !== false;
            }, ARRAY_FILTER_USE_KEY));
        }
        
        $value = esc_attr( ($meta !== false) ? $meta : $field_options['default_value']);

        if(preg_match('/radio|checkbox/', $field_options['type'])){
            if($value === $field_options['default_value']){
                $checked = 'checked';
            } else $value = $field_options['default_value'];
        } else $checked = '';

        $input_html = '<input class="'.$field_options['classes'].'" type="'.$field_options['type'].'" name="'.$field_options['name'].'" id="'.$field_options['id'].'" value="'.$value.'" '.$checked.' />';
        switch ($field_options['type']) {
            case 'select':
                $field_html .= self::get_select_input($field_options, $value);
                break;
            case 'textarea':
                $field_html .= '<br/><textarea class="'.$field_options['classes'].'" name="'.$field_options['name'].'" id="'.$field_options['id'].'" rows="10" cols="50">'.$value.'</textarea>';
                break;
            default:
                $field_html .= $input_html;
                break;
        }

        return '<span>'.$field_html.'</span>';
    }

    public static function get_meta_html_form(array $meta_array, string $context, string $nonce)
    {
        $html = '';

        //$html .= wp_nonce_field( $nonce, $nonce . '_nonce' );

        foreach( $meta_array as $meta){
            $html .= self::get_meta_html($meta['id'], $context, $meta['key'], true, $meta['args']);
        }

        echo '<div>'.$html.'</div>';
    }
}
