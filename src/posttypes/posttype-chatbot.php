<?php

class Chatbot
{

    private function __construct()
    {
        add_action('init', [$this, 'create_Chatbot'], 5);
        add_action('admin_init', [$this, 'add_chatbot_meta']);
        add_action('save_post', [$this, 'save'], 10, 2);
    }

    public function create_Chatbot()
    {
        register_post_type(
            'Chatbot',
            array(
                'labels' => array(
                    'chatbot' => 'Chatbot',
                    'singular_chatbot' => 'Chatbot',
                    'add_new' => 'Add New',
                    'add_new_item' => 'Add New Chatbot',
                    'edit' => 'Edit',
                    'edit_item' => 'Edit Chatbot',
                    'new_item' => 'New Chatbot',
                    'view' => 'View',
                    'view_item' => 'View Chatbot',
                    'search_items' => 'Search Chatbot',
                    'not_found' => 'No Chatbots Found',
                    'not_found_in_trash' => 'No Chatbots found in Trash',
                    'parent' => 'Parent Chatbot'
                ),
                'public' => true,
                'rewrite' => array('slug' => 'Chatbot'),
                'menu_position' => 20,
                'supports' => array(
                    'title',
                    'editor',
                    'author',
                    'thumbnail'
                ),
                'show_in_rest' => true,
                'has_archive' => true,
                'taxonomies' => array(''),
                'exclude_from_search' => true
            )
        );
    }

    public function save($post_id, $post)
    {
        /* Verify the nonce before proceeding.*/
        if (!isset($_POST['polybot_bot_nonce']) || !wp_verify_nonce($_POST['polybot_bot_nonce'], 'polybot_bot_edit'))
            return $post_id;

        /* Get the post type object.*/
        $post_type = get_post_type_object($post->post_type);

        /* Check if the current user has permission to edit the post.*/
        if (!current_user_can($post_type->cap->edit_post, $post_id))
            return $post_id;

        //------------------------BOT OPTIONS
        $agent = sanitize_text_field($_POST['polybot_agent']);
        $color = sanitize_text_field($_POST['polybot_color']);
        $ui = sanitize_text_field($_POST['polybot_ui']);

        $avatar = array_map(function ($value) {
            return sanitize_text_field($value);
        }, $_POST['polybot_avatar']);
        //------------------------BOT OPTIONS

        /* Get the meta key. \*/
        $meta_keys = array(
            'polybot_agent' => $agent,
            'polybot_color' => $color,
            'polybot_ui' => $ui,
            'polybot_avatar' => array(
                'default' => '',
                'writing' => '',
                'userWriting' => '',
                'userSend' => '',
                'defaultHide' => ''
            )
        );

        foreach ($meta_keys as $meta_key => $new_meta_value) {
            /* Get the meta value of the custom field key. */
            $meta_value = get_post_meta($post_id, $meta_key, true);

            /* If a new meta value was added and there was no previous value, add it. */
            if ($new_meta_value && false == $meta_value)
                add_post_meta($post_id, $meta_key, $new_meta_value, true);

            /* If the new meta value does not match the old value, update it. */
            elseif ($new_meta_value && $new_meta_value != $meta_value)
                update_post_meta($post_id, $meta_key, $new_meta_value);

            /* If there is no new meta value but an old value exists, delete it. */
            elseif ('' == $new_meta_value && $meta_value)
                delete_post_meta($post_id, $meta_key, $meta_value);
        }
    }

    public function add_chatbot_meta()
    {
        add_meta_box('chatbot_basic_meta', 'Chatbot Config', [$this, 'config_meta_box'], 'Chatbot', 'normal');
    }

    public function config_meta_box($post)
    {
        $select_value = get_post_meta($post->ID, 'mi_select', true);
        $color_value = get_post_meta($post->ID, 'mi_color', true);
        $radio_value = get_post_meta($post->ID, 'mi_radio', true);
        $imagenes = get_post_meta($post->ID, 'mi_imagenes', true);
        // Nonce para verificación
        wp_nonce_field('polybot_bot_edit', 'polybot_bot_nonce');
    ?>
        <label for="mi_select">Selecciona una opción:</label>
        <select id="mi_select" name="mi_select">
            <option value="opcion1" <?php selected($select_value, 'opcion1'); ?>>Opción 1</option>
            <option value="opcion2" <?php selected($select_value, 'opcion2'); ?>>Opción 2</option>
            <option value="opcion3" <?php selected($select_value, 'opcion3'); ?>>Opción 3</option>
        </select><br><br>

        <label for="mi_color">Selecciona un color:</label>
        <input type="color" id="mi_color" name="mi_color" value="<?php echo esc_attr($color_value); ?>"><br><br>

        <label>Selecciona una imagen:</label><br>
        <input type="radio" id="radio1" name="mi_radio" value="imagen1" <?php checked($radio_value, 'imagen1'); ?>>
        <label for="radio1"><img src="<?php echo get_template_directory_uri(); ?>/imagenes/imagen1.jpg" alt="Imagen 1" style="width:100px;"></label><br>
        <input type="radio" id="radio2" name="mi_radio" value="imagen2" <?php checked($radio_value, 'imagen2'); ?>>
        <label for="radio2"><img src="<?php echo get_template_directory_uri(); ?>/imagenes/imagen2.jpg" alt="Imagen 2" style="width:100px;"></label><br><br>

        <label>Subir hasta 5 imágenes:</label><br>
        <button id="subir_imagenes" class="button">Subir Imágenes</button><br>
        <div id="imagenes_preview">
            <?php if ($imagenes) : ?>
                <?php foreach ($imagenes as $imagen) : ?>
                    <img src="<?php echo esc_url($imagen); ?>" style="width:100px; margin:5px;">
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <input type="hidden" id="mi_imagenes" name="mi_imagenes" value="<?php echo esc_attr(json_encode($imagenes)); ?>">
    <?php
    }
}
