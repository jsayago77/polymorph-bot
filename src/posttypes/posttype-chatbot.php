<?php

namespace Polymorph_Bot;

class PostType_Chatbot
{

    public function __construct()
    {
        add_action('init', [$this, 'create_Chatbot'], 5);
        add_action('admin_init', [$this, 'add_chatbot_meta']);
        add_action('save_post', [$this, 'save'], 10, 2);
    }

    public function create_Chatbot()
    {
        register_post_type(
            'polybot-chatbot',
            array(
                'labels' => array(
                    'name' => 'Chatbot',
                    'singular_name' => 'Chatbot',
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
                'publicly_queryable' => false,
                'show_ui' => true,
                'show_in_menu' => true,
                'rewrite' => array('slug' => 'Chatbot'),
                'menu_position' => 20,
                'supports' => array(
                    'title',
                    'editor',
                    'author',
                    'thumbnail'
                ),
                'show_in_rest' => false,
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
        $assist = sanitize_text_field($_POST['polybot_assistant']);
        $color = sanitize_text_field($_POST['polybot_color']);
        $ui = sanitize_text_field($_POST['polybot_ui']);

        $avatar = array_map(function ($value) {
            return sanitize_text_field($value);
        }, $_POST['polybot_avatar']);

        $selected_pages = array_map(function ($value) {
            return sanitize_text_field($value);
        }, $_POST['polybot_pages']);
        //------------------------BOT OPTIONS

        /* Get the meta key. */
        $meta_keys = array(
            'polybot_agent' => $agent,
            'polybot_assistant' => $assist,
            'polybot_color' => $color,
            'polybot_ui' => $ui,
            'polybot_avatar' => $avatar,
            'polybot_pages' => $selected_pages
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
        add_meta_box('chatbot_basic_meta', 'Polymorph Bot Config', [$this, 'config_meta_box'], 'polybot-chatbot', 'normal');
    }

    public function config_meta_box($post)
    {
        wp_enqueue_media();
        wp_enqueue_script('polybot-media-upload', Polymorph_Bot::$plugin_url . 'assets/js/admin.js', array('jquery'), null, true);

        $available_agents = ['OpenAI', 'Botpress'];
        $action_images = ['default', 'writing', 'userWriting', 'userSend', 'error'];

        $agent = get_post_meta($post->ID, 'polybot_agent', true);
        $assist = get_post_meta($post->ID, 'polybot_assistant', true);
        $color = get_post_meta($post->ID, 'polybot_color', true);
        $ui = get_post_meta($post->ID, 'polybot_ui', true);
        $avatar = get_post_meta($post->ID, 'polybot_avatar', true);
        $selected_pages = get_post_meta($post->ID, 'polybot_pages', true);

        $pages = get_pages(['ID','post_title', 'post_name']);
        
        wp_nonce_field('polybot_bot_edit', 'polybot_bot_nonce');
    ?>
        <label for="polybot-select-agent"><b>Selecciona una agente:</b></label>
        <select id="polybot-select-agent" name="polybot_agent">
            <?php foreach($available_agents as $av_agent): ?>
                <option value="<?php echo $av_agent; ?>" <?php selected($agent, $av_agent); ?>><?php echo $av_agent; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="polybot-assistant"><b>Ingresa el ID del asistente:</b></label>
        <input type="text" id="polybot-assistant" name="polybot_assistant" value="<?php echo esc_attr($assist); ?>"><br><br>

        <label for="polybot-select-color"><b>Selecciona el color:</b></label>
        <input type="color" id="polybot-select-color" name="polybot_color" value="<?php echo esc_attr($color); ?>"><br><br>

        <label><b>Selecciona un tipo de UI:</b></label><br>
        <input type="radio" id="polybot-ui-1" name="polybot_ui" value="bubble" <?php checked($ui, 'bubble'); ?>>
        <label for="polybot-ui-1">Bubble</label><br>
        <input type="radio" id="polybot-ui-2" name="polybot_ui" value="horizontal" <?php checked($ui, 'horizontal'); ?>>
        <label for="polybot-ui-2">Horizontal</label><br>
        <input type="radio" id="polybot-ui-3" name="polybot_ui" value="animation" <?php checked($ui, 'animation'); ?>>
        <label for="polybot-ui-3">Animation</label><br><br>

        <label><b>Selecciona las paginas en donde aparecera el bot:</b></label><br>
        <div>
        <div style="display: inline-block; margin: .2em;">
            <input type="checkbox" id="polybot_page_all" name="polybot_pages[]" value="all" <?php checked($selected_pages[0], 'all'); ?>>
            <label for="polybot_page_all">Todas las paginas</label>
        </div>
        <?php foreach($pages as $page): ?>
            <div style="display: inline-block; margin: .2em;">
                <input type="checkbox" id="polybot_page_<?php echo esc_attr($page->ID); ?>" name="polybot_pages[]" value="<?php echo esc_attr($page->post_name); ?>" <?php checked($selected_pages, $page->post_name); ?>>
                <label for="polybot_page_<?php echo esc_attr($page->ID); ?>"><?php echo esc_attr($page->post_title); ?></label>
            </div>
        <?php endforeach; ?>
        </div><br><br>

        <label><b>Sube las animaciones del bot:</b></label><br>
        <div class="polybot-image" style="display: flex; justify-content: space-around; flex-direction: row;">  
            <?php
            foreach($action_images as $image):
                if(is_array($avatar) && !empty($avatar)) $imagen_url = wp_get_attachment_url($avatar[$image]);
            ?>
            <div class="polybot-image-upload">
                <label for="polybot-image-<?php echo $image; ?>"><?php echo ucfirst($image); ?></label>
                <input type="hidden" id="polybot-image-<?php echo $image; ?>" name="polybot_avatar[<?php echo $image; ?>]" value="<?php echo is_array($avatar) && !empty($avatar) ? esc_attr($avatar[$image]) : ''; ?>" />
                <div id="polybot-preview-<?php echo $image; ?>" style="margin: 10px 0;">
                    <?php if ($imagen_url) : ?>
                        <img src="<?php echo esc_url($imagen_url); ?>" style="max-width: 100px; height: auto;" />
                    <?php endif; ?>
                </div>
                <div>
                    <input type="button" class="button button-secondary" id="polybot-upload-<?php echo $image; ?>" value="Subir Imagen" />
                    <input type="button" class="button button-secondary" id="polybot-remove-<?php echo $image; ?>" value="Eliminar Imagen" style="<?php echo empty($avatar) ? 'display:none;' : ''; ?>" />
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php
    }
}
