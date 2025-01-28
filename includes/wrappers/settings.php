<?php

class Settings {
    
    private $settings;

    public function __construct() {
        $this->settings = array(
            'option_1' => 'Opción 1',
            'option_2' => 'Opción 2',
            'option_3' => 'Opción 3'
        );

        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    public function add_plugin_page() {
        add_options_page(
            'Configuración',
            'Configuración',
            'manage_options',
            'polymorph-bot-settings',
            array($this, 'create_admin_page')
        );
    }

    public function create_admin_page() {
        // Verificar permisos de administrador
        if (!current_user_can('manage_options')) {
            return;
        }

        ?>
        <div class="wrap">
            <h1>Configuración del Plugin</h1>
            <form method="post" action="options.php">
                <?php
                    // Generar los campos de configuración
                    settings_fields('polymorph_bot_settings_group');
                    do_settings_sections('polymorph-bot-settings');
                    submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        register_setting(
            'polymorph_bot_settings_group',
            'polymorph_bot_settings',
            array($this, 'sanitize_settings')
        );

        foreach ($this->settings as $option => $label) {
            add_settings_section(
                $option . '_section',
                $label,
                array($this, 'print_section_info'),
                'polymorph-bot-settings'
            );

            add_settings_field(
                $option,
                $label,
                array($this, 'create_setting_field'),
                'polymorph-bot-settings',
                $option . '_section',
                array('option' => $option)
            );
        }
    }

    public function sanitize_settings($input) {
        // Aquí puedes realizar cualquier validación o saneamiento de los ajustes
        return $input;
    }

    public function print_section_info() {
        // Aquí puedes agregar información adicional para cada sección si lo deseas
    }

    public function create_setting_field($args) {
        $option = $args['option'];
        $value = get_option('polymorph_bot_settings');

        echo "<input type='text' name='polymorph_bot_settings[$option]' value='" . esc_attr($value[$option]) . "' />";
    }
}