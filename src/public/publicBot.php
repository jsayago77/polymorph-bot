<?php

namespace Polymorph_Bot;

class PublicBot {

    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'add_footer'));
    }

    public function enqueue_scripts()
    {

        $asset_file = Polymorph_Bot::$plugin_path . 'assets/build/index.asset.php';
        $asset = include $asset_file;

        wp_enqueue_style('polybot-fa-icons', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css', array(), null, 'all');
        wp_enqueue_style('polybot-tailwind', Polymorph_Bot::$plugin_url . 'assets/css/output.css', array(), null, 'all');
        wp_enqueue_style('polybot', Polymorph_Bot::$plugin_url . 'assets/css/tupi.css', array(), null, 'all');
        wp_enqueue_script('polybot-chatbot', Polymorph_Bot::$plugin_url . 'assets/build/index.js', $asset['dependencies'], $asset['version'], true);

    }

    public function add_footer(){ 
        
        // Obtener los posts
        $posts = get_posts(array(
            'post_type'      => 'polybot-chatbot',
            'posts_per_page' => -1,
            //'fields'         => 'ids, post_title',
        ));

        $pages = get_post_meta($posts[0]->ID, 'polybot_pages', true);

        if ($pages[0] !== 'all') {
            if (!is_page($pages)) {
                return;
            }
        }

        $avatar = get_post_meta($posts[0]->ID, 'polybot_avatar', true);

        foreach($avatar as $key => $val){
            $avatar[$key] = wp_get_attachment_url($avatar[$key]);
        }

        wp_localize_script('polybot-chatbot', 'polybotObj', array(
            'botID' => $posts[0]->ID,
            'name' => $posts[0]->post_title,
            'color' => get_post_meta($posts[0]->ID, 'polybot_color', true),
            'ui' => get_post_meta($posts[0]->ID, 'polybot_ui', true),
            'avatar' => $avatar,
            'messageUrl' => rest_url('')
        ));

        echo '<div id="polybot_chatbot"></div>';
    }
}