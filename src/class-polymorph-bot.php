<?php

namespace Polymorph_Bot;
use Polymorph_Bot\PostType_Chatbot;

class Polymorph_Bot
{

    public static $instance;

    public static $plugin_path;
    public static $plugin_url;
    public static $namespace;

    public function __construct()
    {
        //$this->set_locale();

        /*
        1. setear hooks de inicio
        2. setear atributos globales
        3. setear cargador de dependencias (autoloader)
        4. llamar clases de inicio (activador, desactivador)
        */
        $this->set_enviroment();
        $this->set_autoload();
        $this->init_hooks();

        $chatbot = new PostType_Chatbot();
    }

    public function init_hooks()
    {
        register_activation_hook('./class-activator.php', array('Activator', 'activate'));
        register_deactivation_hook('./class-deactivator.php', array('Deactivator', 'deactivate'));
    }

    public function set_enviroment()
    {
        self::$plugin_path = plugin_dir_path(dirname(__FILE__));
        self::$plugin_url = plugin_dir_url(dirname(__FILE__));
        self::$namespace = 'polymorph_bot';
    }

    public function set_autoload()
    {
        if (file_exists('../vendor/autoload.php')) {
            require_once '../vendor/autoload.php';
        } else {
            spl_autoload_register(array($this, 'load_dependencies'));
        }
    }

    public function load_dependencies($class)
    {
        $parts = explode('\\', $class);

        if ('Polymorph_Bot' !== $parts[0]) {
            return;
        }

        array_shift($parts);

        $file_name = array_pop($parts) . '.php';
        $file_name = strtolower($file_name);
        $file_name = str_replace('_', '-', $file_name);

        $parts = array_map(
            function ($part) {
                return strtolower($part);
            },
            $parts
        );
        
        $directories = array(
            'admin',
            'classes',
            'posttypes'
        );

        foreach ($directories as $directory) {
            $file_path_a = self::$plugin_path . 'src/' . implode('/', $parts) . $directory . '/' . $file_name;

            if (\file_exists($file_path_a)) {
                require_once($file_path_a);
                return;
            }
        }
    }

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}