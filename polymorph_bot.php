<?php

/**
 *
 * @link              https://example.com
 * @since             1.0.0
 * @package           polymorph_bot
 *
 * @wordpress-plugin
 * Plugin Name:       Polymorph Bot
 * Plugin URI:        https://example.com
 * Description:       Un bot que se adapta y cambia de forma según las configuraciones.
 * Version:           1.0.0
 * Author:            Jose Sayago
 * Author URI:        https://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       polymorph_bot
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'POLYMORPH_BOT_VERSION', '1.0.0' );

function run_polymorph_bot() {

	require_once plugin_dir_path( __FILE__ ) . 'src/class-polymorph-bot.php';
	$polybot = Polymorph_Bot\Polymorph_Bot::get_instance();
	register_activation_hook(__FILE__, array($polybot, 'activate'));
	return $polybot;
}

$GLOBALS['polymorph_bot'] = run_polymorph_bot();