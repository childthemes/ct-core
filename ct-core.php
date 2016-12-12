<?php
/**
 * Plugin Name:       ChildThemes Core
 * Plugin URI:        http://childthemes.net/plugin/childthemes-core
 * Description:       Core functionality post meta, taxonomy meta, theme options, page builder for all themes developed by ChildThemes.
 * Version:           1.0.0
 * Author:            ChildThemes
 * Author URI:        http://childthemes.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ct-core
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'CT_VERSION', '1.0.0' );
define( 'CT_CORE', plugin_basename( __FILE__ ) );
define( 'CT_SLUG', basename( CT_CORE, '.php' ) );
define( 'CT_DIR', trailingslashit( dirname( CT_CORE ) ) );
define( 'CT_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'CT_URI', trailingslashit( plugins_url( CT_DIR ) ) );
define( 'CT_ASSETS', trailingslashit( CT_URI . 'assets' ) );
define( 'CT_INC', trailingslashit( CT_PATH . 'includes' ) );

define( 'CT_THEME', trailingslashit( get_template_directory() ) );
define( 'CT_CHILD', trailingslashit( get_stylesheet_directory() ) );

/**
 * Helpel function ChildThemes Core
 * Use this can be as static function and callable.
 */
require CT_PATH . 'functions.php';

/**
 * The core plugin class that is used to define theme fetures,
 * admin-specific hooks, and public-facing site hooks.
 */
require CT_INC . 'class-ct-core.php';

register_activation_hook( __FILE__, 'activate_mtcore' );
register_deactivation_hook( __FILE__, 'deactivate_mtcore' );
