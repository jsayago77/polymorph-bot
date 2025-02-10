<?php

namespace Polymorph_Bot;

use Polymorph_Bot\OpenAI_Bot;

class Rest_Routes {

    public function __construct()
    {
        add_action('rest_api_init', array($this, 'load_endpoints'));
    }

    public function load_endpoints()
    {
        register_rest_route('polybot/v1', '/send-message/', array(
            'methods' => 'POST',
            'callback' => array($this, 'send_message'),
        ));
    }

    public function send_message(\WP_REST_Request $request)
    {
        $bot_id = $request->get_param('bot_id');

        $assist_id = get_post_meta($bot_id, 'polybot_assistant', true);
        $agent =  get_post_meta($bot_id, 'polybot_agent', true);

        if($agent == 'OpenAI') {
            $key = get_option('polybot_openai_key');
            $thread = $request->get_param('thread_id');

            $bot = new OpenAI_Bot($key, $assist_id, $thread);

            $message = $request->get_param('message');
            $bot_response = $bot->sendMessageGeneral($message);

            wp_send_json($bot_response);
        }

        if($agent == 'Botpress') {

        }
    }
}