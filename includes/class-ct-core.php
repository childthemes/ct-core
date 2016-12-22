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

    $this->features = get_option( 'ctcore_features', array() );

		$this->load_dependencies();

		add_action( 'plugins_loaded', array( $this, 'set_locale' ) );
		add_action( 'admin_init', array( $this, 'set_features' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
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
	   * Defining all theme features
	   * enabled by defaults, except cleanup features.
     *
     * set of features from option
	   */
		$opts_temp = array();

		if ( empty( $this->features ) ) {
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
		load_plugin_textdomain( 'ctcore', false, CT_PATH . 'languages' );
	}

	/**
	 * Update features on current theme.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function set_features() {

    $theme_name = get_stylesheet();
    $theme_features = get_theme_support( 'ctcore' );

    if ( !$theme_name || !is_array( $theme_features ) ) {
      return;
    }

    if ( isset( $_GET['ctcore'] ) && $_GET['ctcore'] == 'updated' ) {
      add_admin_notice( 'success', esc_html__( 'Child Themes Core feature for current active theme has been updated', 'ctcore' ) );
    }

    $theme_features = $theme_features[0];
    $old_option_value = get_option( 'ctcore_features', array() );

    if ( !is_array_equal( $theme_features, $old_option_value ) ) {
      update_option( 'ctcore_features', $theme_features );
      wp_safe_redirect( add_query_arg( 'ctcore', 'updated', $_SERVER['HTTP_REFERER'] ) );
      exit();
    }
	}

  /**
	 * Frontpage area scripts & styles.
	 *
	 * @since     1.0.0
	 */
	public function front_scripts() {}

  /**
	 * Admin area scripts & styles.
	 *
	 * @since     1.0.0
	 */
	public function admin_scripts( $hook ) {
		wp_enqueue_style( CT_SLUG.'-admin', ctcore_css( 'admin-style' ), array(), CT_VERSION );
		wp_enqueue_script( CT_SLUG.'-admin', ctcore_js( 'admin-script' ), array( 'jquery' ), CT_VERSION, true );
	}

  /**
	 * Public get and check single feature object.
	 *
	 * @since     1.0.0
	 * @return    boolean
	 */
	public function get( $feature = '' ) {
		if ( method_exists( $this, $feature ) && in_array( $feature, $this->features ) ) {
      return $this->{$feature};
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
