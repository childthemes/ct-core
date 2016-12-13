<?php
/**
 * WordPress Shortcode Loader Class
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
 *  CT_Core_Shortcode - ChildThemes Core
 *
 * @author  Child Themes
 * @since 	1.0.0
 */
class CT_Core_Theme_Options {

  /**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version = '2.6.0';

	/**
	 * Active var of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected $active = false;

  /**
	 * Hold current active theme instance.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
  public $theme;

  /**
	 * Hold current theme options sections.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
  public $sections = array();

  /**
	 * Hold the theme options files inside theme.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
  public $path = 'includes/options';

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

		if ( false !== ( $opts = get_option( 'ctcore_features' ) ) && in_array( 'theme-options', $opts ) ) {
	    $this->active = true;

			add_action( 'plugin_loaded', array( $this, 'include_ot_loader' ), 20 );
			add_action( 'after_setup_theme', array( $this, 'register_theme_options' ), 16 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}
	}

	/**
	 * Include Option Tree core files.
	 *
	 * @since     1.0.0
	 */
	public function include_ot_loader() {
		if ( ! class_exists( 'OT_Loader' ) ) {
			include_once CT_INC . 'option-tree/ot-loader.php';
		}
	}

	/**
	 * Register any theme options exists on theme.
	 *
	 * @since     1.0.0
	 */
	public function register_theme_options() {

		$this->theme = wp_get_theme();

		/* OptionTree is not loaded yet, or this is not an admin request */
		if ( ! function_exists( 'ot_settings_id' ) || ! is_admin() ) {
			return;
		}

		/* Customize Option Tree Defaults Option ID */
		add_filter( 'ot_show_pages',      '__return_false' );
		add_filter( 'ot_show_new_layout', '__return_false' );
		add_filter( 'ot_theme_mode',      '__return_true'  );
		add_filter( 'ot_post_formats',    '__return_true'  );
		add_filter( 'ot_options_id', 			array( $this, 'set_option_id' )   );
		add_filter( 'ot_settings_id', 		array( $this, 'set_setting_id' )  );
		add_filter( 'ot_upload_text', 		array( $this, 'set_upload_text' ) );
		add_filter( 'ot_header_logo_link',array( $this, 'set_header_logo' ) );
		add_filter( 'ot_theme_options_page_title', '__return_empty_string'  );
		add_filter( 'ot_header_version_text', '__return_empty_string' );

		$path = apply_filters( 'childthemes_theme_options_path', $this->path, self::$instance );

		// Child Theme Source
		if ( is_child_theme() )
			$sources[] = get_stylesheet_directory() . '/' . $path;

		// Parent Theme Source
		$sources[] = get_template_directory() . '/' . $path;

		// Plugin Option Source
		if ( apply_filters( 'ctcore_theme_options_default_options', true ) ) {
			$sources[] = CT_INC . 'theme-options';
		}

		$source_temp = array();
		$sources = array_reverse( $sources );

		foreach ( $sources as $dir ) {

			if ( !is_dir( $dir ) ) {
				continue;
			}

			$source_files = glob( $dir.'/*.php', GLOB_NOSORT );

			if ( empty( $source_files ) ) {
				continue;
			}

			foreach ( $source_files as $file ) {
				if ( !file_exists( $file ) ) {
					continue;
				}
				$basename = basename( $file, '.php' );
				$basename = str_replace( array( 'section-', 'extra-' ), '', $basename );
				$filedata = get_file_data( $file, array( 'name' => 'Name', 'section' => 'Section' ) );

				$section_name = !empty( $filedata['name'] ) ? $filedata['name'] : ucwords( $basename );
				$section_fields = include_once( $file );

				if ( empty( $section_fields ) || !is_array( $section_fields ) ) {
					continue;
				}

				if ( !empty( $filedata['section'] ) && isset( $source_temp[ $basename ] ) ) {
					$section_fields_temp = $source_temp[ $basename ]['fields'];
					if ( is_array( $section_fields_temp ) ) {
						$section_fields = array_merge( $section_fields, $section_fields_temp );
					}
				}

				foreach ( $section_fields as $fkey => $field ) {
					$section_fields[ $fkey ]['section'] = $basename;
				}

				$source_temp[ $basename ] = array(
					'name' => esc_html__( $section_name, 'ctcore' ),
					'fields' => $section_fields
				);

			}
		}

		/*
		 * Custom settings array that will eventually be
		 * passes to the OptionTree Settings API Class.
		 */
		$ot_settings = $this->set_settings( $source_temp );

		/*
		 * Get a copy of the saved settings array.
		 */
		$saved_settings = get_option( ot_settings_id(), array() );

		/* settings are not the same update the DB */
		if ( $saved_settings !== $ot_settings ) {
			update_option( ot_settings_id(), $ot_settings );
		}

		/* Lets OptionTree know the UI Builder is being overridden */
		global $ot_has_custom_theme_options;
		$ot_has_custom_theme_options = true;

	}

	/**
	 * Set the theme option setting fields.
	 *
	 * @since     1.0.0
	 *
	 * @return    mixed
	 */
	public function set_settings( array $settings ) {

		$sections = $fields = array();

		foreach ( $settings as $section => $setting ) {
			$sections[] = array(
				'id'			=> $section,
				'title'		=> $setting['name']
			);
			$fields = array_merge( $fields, $setting['fields'] );
		}

		$the_settings = array(
			'sections'	=> $sections,
			'settings'	=> $fields
		);

		return apply_filters( ot_settings_id() . '_args', $the_settings );
	}

	/**
	 * Set the theme option name.
	 *
	 * @since     1.0.0
	 *
	 * @return    string
	 */
	public function set_option_id( $option_id ) {
		$option_id = !empty( $this->theme->get('Template') ) ? $this->theme->get('Template') : get_template();
		return sanitize_key( 'ct_'.$option_id );
	}

	/**
	 * Set the theme setting option key name.
	 *
	 * @since     1.0.0
	 *
	 * @return    string
	 */
	public function set_setting_id( $setting_id ) {
		$setting_id = !empty( $this->theme->get('Template') ) ? $this->theme->get('Template') : get_template();
		return sanitize_key( 'ct_'.$setting_id.'_options' );
	}

	/**
	 * Set upload field text button.
	 *
	 * @since     1.0.0
	 *
	 * @return    string
	 */
	public function set_upload_text( $text ) {
		return esc_html__( 'Insert', 'ctcore' );
	}

	/**
	 * Set text header on Theme Options Page.
	 *
	 * @since     1.0.0
	 *
	 * @return    string
	 */
	public function set_header_logo( $header_html ) {
		$themename = $this->theme->get('Name');
		return '<span class="title">'. esc_html( $themename ) .'.</span><span class="sub-title">'. esc_html( sprintf( _x( 'Theme Options v%s', '%s = option tree version', 'ctcore' ), $this->version ) ) .'</span>';
	}

	/**
	 * Enqueue admin styles & scripts.
	 *
	 * @since     1.0.0
	 */
	public function admin_scripts( $hook ) {
		wp_enqueue_style( 'ctcore-ot-style', CT_ASSETS . 'css/admin-options.css', array( 'ot-admin-css' ), CT_VERSION );
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

}
