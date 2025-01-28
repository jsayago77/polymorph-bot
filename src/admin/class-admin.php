<?php
/**
 * Class Admin
 *
 * This class handles the admin page for the Polymorph Bot plugin.
 */
class Admin {
    private $options;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    public function add_plugin_page() {
        add_menu_page(
            'Polymorph Bot Settings', 
            'Polymorph Bot', 
            'manage_options', 
            'polymorph-bot-admin', 
            array($this, 'create_admin_page'),
            'dashicons-admin-generic'
        );
    }

    public function create_admin_page() {
        $this->options = get_option('polymorph_bot_options');
        ?>
        <div class="wrap">
            <h1>Polymorph Bot Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('polymorph_bot_option_group');
                do_settings_sections('polymorph-bot-admin');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        register_setting(
            'polymorph_bot_option_group', 
            'polymorph_bot_options', 
            array($this, 'sanitize')
        );

        add_settings_section(
            'setting_section_id', 
            'Configuration', 
            array($this, 'print_section_info'), 
            'polymorph-bot-admin'
        );

        add_settings_field(
            'openai_key', 
            'OpenAI Key', 
            array($this, 'openai_key_callback'), 
            'polymorph-bot-admin', 
            'setting_section_id'
        );

        add_settings_field(
            'botpress_key', 
            'Botpress Key', 
            array($this, 'botpress_key_callback'), 
            'polymorph-bot-admin', 
            'setting_section_id'
        );
    }

    public function sanitize($input) {
        $new_input = array();
        if(isset($input['openai_key']))
            $new_input['openai_key'] = sanitize_text_field($input['openai_key']);

        if(isset($input['botpress_key']))
            $new_input['botpress_key'] = sanitize_text_field($input['botpress_key']);

        return $new_input;
    }

    public function print_section_info() {
        print 'Enter your settings below:';
    }

    public function openai_key_callback() {
        printf(
            '<input type="text" id="openai_key" name="polymorph_bot_options[openai_key]" value="%s" />',
            isset($this->options['openai_key']) ? esc_attr($this->options['openai_key']) : ''
        );
    }

    public function botpress_key_callback() {
        printf(
            '<input type="text" id="botpress_key" name="polymorph_bot_options[botpress_key]" value="%s" />',
            isset($this->options['botpress_key']) ? esc_attr($this->options['botpress_key']) : ''
        );
    }
}
