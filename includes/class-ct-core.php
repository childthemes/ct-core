<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://childthemes.net/
 * @since      1.0.0
 *
 * @package    CT_Core
 * @subpackage CT_Core/includes
 */

 // Exit if accessed directly
 if ( !defined( 'ABSPATH' ) ) exit;

/**
 * The core plugin class.
 *
 * This is used to define theme features, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    CT_Core
 * @subpackage CT_Core/includes
 * @author     Minimal Themes <support@minimalthemes.net>
 */
class CT_Core {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Get all theme features
	 * by deafult the active theme has this features
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      mixed 			array theme fature pairing with theme relative path
	 */
	public $features = array();

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = CT_SLUG;
		$this->version = CT_VERSION;

    $this->features = get_theme_support( 'ct-core' );

		$this->load_dependencies();

		add_action( 'plugins_loaded', array( $this, 'set_locale' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - CT_Core_Admin. Defines all hooks for the admin area.
	 * - CT_Core_Public. Defines all hooks for the public side of the site.
	 * - CT_Core_Feature. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once CT_INC . 'class-ct-core-integrations.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once CT_INC . 'class-ct-core-fronts.php';

    /**
		 * Helper class method to cleanup and whitelabeling WordPress site
     * use this with caution, this is the part of one theme fetaures
     * if cleanup features enabled, you dont need to call this class method anymore!
     *
     * USE WITH CAUTION!!! THIS MAY BREAK YOUR WORDPRESS SITE!!!
		 */
		require_once CT_INC . 'class-ct-core-cleanup.php';

		/**
	   * Defining all theme features
	   * enabled by defaults, except cleanup features.
     *
     * set of features from option
	   */
		$opts_temp = array();

		if ( ! $this->features || empty( $this->features ) ) {
		  return;
		}

    foreach ( $this->features as $feature ) {

      $class_file_helper = sprintf( '%sclass-ct-core-%s.php', CT_INC, $feature );
      $class_name_helper = file_to_classname( $class_file_helper );

      if ( !file_exists( $class_file_helper ) ) {
        continue;
      }
      require_once $class_file_helper;
      if ( class_exists( $class_name_helper ) ) {
        if ( method_exists( $class_name_helper, 'get_instance' ) ) {
          $this->{$feature} = $class_name_helper::get_instance();
        } else {
          $this->{$feature} = new $class_name_helper;
        }
      }
    }

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the CT_Core_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function set_locale() {
		load_plugin_textdomain( 'ctcore', false, CT_DIR . 'languages' );
	}

  /**
	 * Public get and check single feature object.
	 *
	 * @since     1.0.0
	 * @return    boolean
	 */
	public function get( $feature = '' ) {
		if ( isset( $this->$feature ) && in_array( $feature, $this->features ) ) {
      return $this->$feature;
    }
    return false;
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the plugin absolute folder path.
	 *
	 * @since     1.0.0
	 * @return    string
	 */
	public function get_path() {
		return CT_PATH;
	}

	/**
	 * Retrieve the plugin absolute URL.
	 *
	 * @since     1.0.0
	 * @return    string
	 */
	public function get_uri() {
		return CT_URI;
	}

}
$GLOBALS['CT_Core'] = CT_Core::get_instance();
