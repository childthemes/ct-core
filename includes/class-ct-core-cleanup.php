<?php
/**
 * WordPress CleanUp & White Labeling Loader Class
 *
 * @link       http://minimalthemes.net/
 * @since      1.0.0
 *
 * @package    CT_Core
 * @subpackage CT_Core/includes
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 *  CT_Core_Clean - Minimal Themes Framework
 *
 * @author  Minimal Themes
 * @since 	1.0.0
 */
class CT_Core_Cleanup {

  /**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

  /**
	 * Active var of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected $active = false;

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

  /**
	 * Fire it up
	 *
	 * @since  1.0.0
	 */
	public function __construct() {

    $this->version = CT_VERSION;
		if ( false !== ( $opts = get_theme_support( 'ctcore' ) ) && in_array( 'cleanup', $opts ) ) {
	    $this->active = true;
      add_action( 'init', array( 'CT_Core_Clean', 'wp_head' ) );
			add_action( 'init', array( 'CT_Core_Clean', 'generator' ) );
			add_action( 'admin_init', array( 'CT_Core_Clean', 'wp_admin' ) );
      add_action( 'plugins_loaded', array( 'CT_Core_Clean', 'core_updates' ) );
			add_action( 'registered_post_type', array( 'CT_Core_Clean', 'remove_revslider_metabox' ) );
      add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
		}
	}

  /**
	 * Set hook for later after theme loaded.
	 *
	 * @since     1.0.0
	 */
	public function after_setup_theme() {
    add_filter( 'style_loader_tag', array( 'CT_Core_Clean', 'style_loader_tag' ) );
    add_filter( 'script_loader_tag', array( 'CT_Core_Clean', 'script_loader_tag' ) );
    add_filter( 'script_loader_src', array( 'CT_Core_Clean', 'script_version' ) );
    add_filter( 'style_loader_src', array( 'CT_Core_Clean', 'script_version' ) );
    add_filter( 'body_class', array( 'CT_Core_Clean', 'body_class' ) );
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
	 * Set this feature active.
	 *
	 * @since     1.0.0
	 */
	public function is_active() {
		return $this->active;
	}

} //CT_Core_Cleanup

/**
 *  CT_Core_Clean - Minimal Themes Framework
 *
 * @author  Minimal Themes
 * @since 	1.0.0
 */
class CT_Core_Clean {

  /**
   * Clean up wp_head()
   *
   * Remove unnecessary <link>'s
   * Remove inline CSS and JS from WP emoji support
   * Remove inline CSS used by Recent Comments widget
   * Remove inline CSS used by posts with galleries
   * Remove self-closing tag and change ''s to "'s on rel_canonical()
   *
   * add_action - init
   *
   * @since     1.0.0
   */
  static function wp_head() {

    // Originally from http://wpengineer.com/1438/wordpress-header/
    remove_action('wp_head', 'feed_links_extra', 3);
    add_action('wp_head', 'ob_start', 1, 0);
    add_action('wp_head', function () {
      $pattern = '/.*' . preg_quote(esc_url(get_feed_link('comments_' . get_default_feed())), '/') . '.*[\r\n]+/';
      echo preg_replace($pattern, '', ob_get_clean());
    }, 3, 0);
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
    remove_action('wp_head', 'wp_shortlink_wp_head', 10);
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('use_default_gallery_style', '__return_false');
    add_filter('language_attributes', array('CT_Core_Clean', 'language_attributes'));

    global $wp_widget_factory;
    if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
      remove_action('wp_head', [$wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style']);
    }

    if (!class_exists('WPSEO_Frontend')) {
      remove_action('wp_head', 'rel_canonical');
      add_action('wp_head', array('CT_Core_Clean', 'rel_canonical'));
    }

  }

  /**
   * Remove self-closing tag and change ''s to "'s on rel_canonical()
   *
   * @since     1.0.0
   */
  static function rel_canonical() {
    global $wp_the_query;
    if (!is_singular()) {
      return;
    }
    if (!$id = $wp_the_query->get_queried_object_id()) {
      return;
    }
    $link = get_permalink($id);
    echo "\t<link rel=\"canonical\" href=\"$link\">\n";
  }

  /**
   * Helper for removing the Revslider Metabox from being on every CPT edit screen
   *
   * add_action - registered_post_type
   *
   * @since			1.0.0
   * @param 		$post_type
   */
  static function remove_revslider_metabox( $post_type ) {
  	add_action('do_meta_boxes', function () use ($post_type) {
  		remove_meta_box('mymetabox_revslider_0', $post_type, 'normal');
  	});
  }

  /**
   * Remove any type of generator label
   *
   * add_action - init
   *
   * @since    1.0.0
   */
  static function generator() {
    remove_action( 'wp_head', 'wp_generator' );
    // Remove the WordPress version from RSS feeds
    add_filter( 'the_generator', '__return_false' );
    //remove Revolution Slider Meta Generator
    add_filter( 'revslider_meta_generator', '__return_empty_string' );
    //remove meta generator by Visual Composer
    if (function_exists('visual_composer')) {
    	remove_action( 'wp_head', array(visual_composer(), 'addMetaData') );
    }
  }

  /**
   * Clean up language_attributes() used in <html> tag
   * Remove dir="ltr"
   *
   * add_filter - language_attributes
   *
   * @since    1.0.0
   */
  static function language_attributes() {
    $attributes = [];

    if (is_rtl()) {
      $attributes[] = 'dir="rtl"';
    }

    $lang = get_bloginfo('language');

    if ($lang) {
      $attributes[] = "lang=\"$lang\"";
    }

    $output = implode(' ', $attributes);

    return $output;
  }

  /**
   * HTML 5 tags
   * Clean up output of stylesheet <link> tags
   *
   * add_filter - style_loader_tag
   *
   * @since    1.0.0
   */
  static function style_loader_tag($input) {
    preg_match_all("!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input, $matches);
    if (empty($matches[2])) {
     return $input;
    }
    // Only display media if it is meaningful
    $media = $matches[3][0] !== '' && $matches[3][0] !== 'all' ? ' media="' . $matches[3][0] . '"' : '';
    return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
  }

  /**
   * HTML 5 tags
   * Clean up output of <script> tags
   *
   * add_filter - script_loader_tag
   *
   * @since    1.0.0
   */
  static function script_loader_tag($input) {
    $input = str_replace("type='text/javascript' ", '', $input);
    return str_replace("'", '"', $input);
  }

  /**
   * Remove version query string from all styles and scripts
   * And also add file modify time to version if file from current theme folder
   * with SCRIPT_DEBUG enabled.
   *
   * add_filter - script_loader_src
   * add_filter - style_loader_src
   *
   * @since     1.0.0
   */
  static function script_version($src) {
  	if ( is_admin() ) {
  		return $src;
  	}
  	if ($src) {
  		$src = esc_url(remove_query_arg('ver', $src));
      $url = trailingslashit( get_template_directory_uri() );
      $rel = trailingslashit( get_template_directory() );
  		if ( false !== SCRIPT_DEBUG && false !== strpos( $src, $url ) ) {
  			$part_src = str_replace( $url, '', $src );
  			$part_src = $rel . $part_src;
  			$version  = filemtime( $part_src );
  			$src = $version ? esc_url(add_query_arg('ver', $version, $src)) : $src;
  		}
  	} else {
  		return false;
  	}
    return $src;
  }

  /**
   * Clean body class
   * - remove page template file name
   * - remove visual composer version
   *
   * add_filter - body_class
   *
   * @since   1.0.0
   */
  static function body_class( $classes ) {
    foreach( $classes as $keyc => $arr ){
  		if ( false !== strpos( $arr, 'page-template' ) ) {
  			unset( $classes[ $keyc ] );
  		}
  		elseif ( false !== strpos( $arr, 'js-comp-ver' ) ) {
  			unset( $classes[ $keyc ] );
  		}
  	}
  	return $classes;
  }

  /**
   * Whitelabeling WP Admin
   * - Remove WordPress Logo admin menu bar.
   * - Change wp-admin footer version.
   * - Change wp-admin footer copyright.
   * - Remove Help tab from WordPress.
   * - Remove welcome panel from WordPress Dashboard.
   * - Remove unnecessary dashboard widgets.
   *
   * add_action - admin_init
   *
   * @since    1.0.0
   */
  static function wp_admin() {
    add_action('wp_before_admin_bar_render', array( 'CT_Core_Clean', 'wp_logo' ), 0);
    add_filter('update_footer', array( 'CT_Core_Clean', 'update_footer' ), 998);
    add_filter('admin_footer_text', array( 'CT_Core_Clean', 'admin_footer_text' ), 998);
    add_action('admin_head', array( 'CT_Core_Clean', 'help_tabs' ), 99);
    remove_action( 'welcome_panel', 'wp_welcome_panel' );
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
    remove_meta_box('dashboard_primary', 'dashboard', 'normal');
    remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
  }

  /**
   * Remove WordPress Logo admin menu bar.
   */
  static function wp_logo() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');
  }

  /**
   * Change wp-admin footer version.
   */
  static function update_footer() {
    $theme = wp_get_theme();
	  return $theme->get('Name').' v'.$theme->get('Version');
  }

  /**
   * Change wp-admin footer copyright.
   */
  static function admin_footer_text() {
    return sprintf( '&copy; %1s <a href="%2s">%3s</a> All Right Reserved.', date('Y'), get_home_url('/'), get_bloginfo( 'name' ) );
  }

  /**
   * Remove Help tab from WordPress.
   */
  static function help_tabs() {
    $screen = get_current_screen();
    $screen->remove_help_tabs();
  }

  /**
   * Force Disable WordPress Update (except super administrator)
   * - Remove update admin menu.
   * - Remove version check.
   * - Remove transiest update core.
   * - Remove plugin update check.
   *
   * add_action - plugins_loaded
   *
   * @since    1.0.0
   */
  static function core_updates() {
    add_action('admin_menu', array( 'CT_Core_Clean', 'update_menu' ), 999 );
  	add_action('init', array( 'CT_Core_Clean', 'wp_version_check' ), 1);
  	add_filter('pre_option_update_core', '__return_null');
  	add_filter('pre_site_transient_update_core', '__return_null');
		remove_action( 'admin_init', '_maybe_update_core' );
  	//plugin update
  	remove_action( 'load-plugins.php', 'wp_update_plugins' );
  	remove_action( 'load-update.php', 'wp_update_plugins' );
  	remove_action( 'load-update-core.php','wp_update_plugins' );
  	remove_action( 'admin_init', '_maybe_update_plugins' );
  	remove_action( 'wp_update_plugins', 'wp_update_plugins' );
  	add_filter('pre_site_transient_update_plugins','__return_null');
  }

  /**
   * Remove update admin menu.
   */
  static function update_menu() {
    remove_submenu_page( 'index.php', 'update-core.php' );
  }

  /**
   * Remove version check.
   */
  static function wp_version_check() {
    remove_action( 'init', 'wp_version_check' );
	  remove_action( 'init', 'wp_schedule_update_checks' );
  }

}
