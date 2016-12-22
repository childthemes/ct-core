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
	 * Hold the theme options ID current theme.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
  public $option_id = false;

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
      $this->option_id = get_option( 'ctcore_theme_options_id', false );

			add_action( 'plugins_loaded', array( $this, 'include_ot_loader' ), 20 );
			add_action( 'after_setup_theme', array( $this, 'register_theme_options' ), 16 );
			add_action( 'ot_admin_styles_after', array( $this, 'admin_scripts' ) );
			add_filter( 'css_option_file_path', array( $this, 'set_dynamic_css_path' ), 20, 2 );
      add_action( 'wp_print_footer_scripts', array( $this, 'dynamic_scripts' ), 99 );

      add_filter( 'option_'.$this->option_id, array( $this, 'get_exclude_tools' ), 20 );
      add_filter( 'pre_update_option_'.$this->option_id, array( $this, 'pre_exclude_tools' ), 20 );
		}
	}

	/**
	 * Include Option Tree core files.
	 *
	 * @since     1.0.0
	 */
	public function include_ot_loader() {

    if ( ! class_exists( 'OT_Loader' ) ) {
      require CT_INC . 'option-tree/ot-loader.php';
    }

		add_filter( 'ot_show_new_layout', '__return_false' );
    add_filter( 'ot_show_options_ui', '__return_false' );
    if ( !( defined( 'WP_DEBUG' ) && WP_DEBUG == true ) ) {
      add_filter( 'ot_show_pages',      '__return_false' );
      add_filter( 'ot_show_docs',       '__return_false' );
      add_filter( 'ot_show_settings_import', '__return_false' );
      add_filter( 'ot_show_settings_export', '__return_false' );
    }
	}

	/**
	 * Register any theme options exists on theme.
	 *
	 * @since     1.0.0
	 */
	public function register_theme_options() {

		$this->theme = wp_get_theme();

    /* Customize Option Tree Defaults Option ID */
    add_filter( 'ot_theme_mode',        '__return_true' );
    add_filter( 'ot_use_theme_options', '__return_true' );
    add_filter( 'ot_post_formats',      '__return_true' );
    add_filter( 'ot_theme_options_page_title', '__return_empty_string' );
		add_filter( 'ot_header_version_text', '__return_empty_string' );
		add_filter( 'ot_options_id', 			array( $this, 'set_option_id' )   );
		add_filter( 'ot_settings_id', 		array( $this, 'set_setting_id' )  );
		add_filter( 'ot_upload_text', 		array( $this, 'set_upload_text' ) );
		add_filter( 'ot_header_logo_link',array( $this, 'set_header_logo' ) );
		add_filter( 'ot_theme_options_menu_slug', array( $this, 'set_menu_slug' ) );
		add_filter( 'ot_theme_options_position', array( $this, 'set_menu_position' ) );
		add_filter( 'ot_dequeue_jquery_ui_css_screen_ids', array( $this, 'screen_ids' ) );

		/* OptionTree is not loaded yet, or this is not an admin request */
		if ( ! function_exists( 'ot_settings_id' ) || ! is_admin() ) {
			return;
		}

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

      $s = 1;
			foreach ( $source_files as $file ) {
				if ( !file_exists( $file ) ) {
					continue;
				}
				$basename = basename( $file, '.php' );
				$basename = str_replace( array( 'section-', 'extra-' ), '', $basename );
				$filedata = get_file_data( $file, array(
          'name' => 'Name',
          'icon' => 'Icon',
          'sort' => 'Sort',
          'section' => 'Section'
        ) );

        if ( $basename == 'general' ) {
          $section_name = esc_html__( 'General', 'ctcore' );
        } elseif ( $basename == 'tools' ) {
          $section_name = esc_html__( 'Export Import', 'ctcore' );
        } else {
          $section_name = !empty( $filedata['name'] ) ? esc_html__( $filedata['name'] ) : ucwords( $basename );
        }
        $section_icon = !empty( $filedata['icon'] ) ? strtolower( $filedata['icon'] ) : 'setting';
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
					'name' => '<i class="' . esc_attr( $section_icon ) . ' icon"></i>' . trim( $section_name ),
          'sort' => !empty($filedata['sort']) ? absint($filedata['sort']) : $s,
					'fields' => $section_fields
				);
        $s++;
			}
		}

    /*
     * Sort section settings by sort value
     */
    uasort( $source_temp, array( $this, 'sort_order' ) );

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
	protected function set_settings( array $settings ) {

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
	 * Sort array by sort value
   *
   * @since 1.0.0
   *
	 * @param array $a
	 * @param array $b
	 * @return int
	 */
	protected function sort_order( $a, $b ) {
    if ( $a['sort'] == $b['sort'] ) {
      return 0;
    }
    return ( $a['sort'] < $b['sort'] ) ? -1 : 1;
	}

	/**
	 * Set the theme option name.
	 *
	 * @since     1.0.0
	 *
	 * @return    string
	 */
	public function set_option_id( $option_id ) {
    $template = $this->theme->get('Template');
		$option_id = !empty( $template ) ? $template : get_template();
    $option_id = 'ctcore_'.$option_id;
    update_option( 'ctcore_theme_options_id', $option_id );
		return sanitize_key( $option_id );
	}

	/**
	 * Set the theme setting option key name.
	 *
	 * @since     1.0.0
	 *
	 * @return    string
	 */
	public function set_setting_id( $setting_id ) {
    $template = $this->theme->get('Template');
		$setting_id = !empty( $template ) ? $template : get_template();
		return sanitize_key( 'ctcore_'.$setting_id.'_options' );
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
	 * Set menu URL slug theme options page.
	 *
	 * @since     1.0.0
	 *
	 * @return    string
	 */
	public function set_menu_slug( $slug ) {
		return 'theme-options';
	}

	/**
	 * Set menu position theme options page.
	 *
	 * @since     1.0.0
	 *
	 * @return    int
	 */
	public function set_menu_position() {
		return 58;
	}

	/**
	 * Set menu position theme options page.
	 *
	 * @since     1.0.0
	 *
	 * @return    int
	 */
	public function screen_ids( $screen_ids ) {
		$screen_ids[] = 'appearance_page_theme-options';
    return $screen_ids;
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
		wp_enqueue_style( 'ctcore-ot-style', CT_ASSETS . 'css/admin-options.css', array(), CT_VERSION );
	}

	/**
	 * Enqueue dynamic scripts from theme options.
	 *
	 * @since     1.0.0
	 */
	public function dynamic_scripts() {

    $options = get_option( ot_options_id(), array() );
    $scripts = isset($options['custom_dynamic_js']) ? $options['custom_dynamic_js'] : '';

    $js = '';
    if ( !empty( $scripts ) ) {
      $js .= '<script id="dynamic-js-theme-options" type="text/javascript">';
      $js .= "\n/* <![CDATA[ */\n";
      $js .= "jQuery(document).ready( function($) {\n";
      $js .= "\t" . $scripts;
      $js .= "\n});";
      $js .= "\n/* ]]> */\n";
      $js .= '</script>';
    }
    echo $js;
	}

	/**
	 * Set default dynamic css file path.
	 *
	 * @since     1.0.0
	 */
	public function set_dynamic_css_path( $path, $file_id ) {

    $css_dir = wp_upload_dir();
    $opt_id  = str_replace( 'ctcore_', '', $this->option_id );
		$css_path = trailingslashit( $css_dir['basedir'] );

		if ( !$css_dir['error'] && !empty($opt_id) ) {
      $path = $css_path.'dynamic-'.sanitize_key($opt_id).'.css';
			if ( wp_mkdir_p( $css_path.'ct-core' ) ) {
        file_put_contents( $css_path.'ct-core/dynamic-'.sanitize_key($opt_id).'.css', '' );
        $path = $css_path.'ct-core/dynamic-'.sanitize_key($opt_id).'.css';
      }
    }
    return $path;
	}

	/**
	 * Add import option data to database.
	 *
	 * @since     1.0.0
	 */
	public function pre_exclude_tools( $new ) {

    if ( !is_array( $new ) ) {
      return $new;
    }

    if ( isset( $new['export_options'] ) ) {
      unset( $new['export_options'] );
    }

    if ( isset( $new['import_options'] ) ) {
      if ( !empty( $new['import_options'] ) ) {
        $new_data = unserialize( ot_decode( $new['import_options'] ) );
        if ( !empty( $new_data ) && is_array( $new_data ) ) {
          $new = $new_data;
          add_settings_error( 'option-tree', 'import_success', esc_html__( 'Import Options Data Success.', 'ctcore' ), 'updated' );
        }
      }
      unset( $new['import_options'] );
    }
		return $new;
	}

	/**
	 * Exclude tools section (Import & Export) to get from database.
	 *
	 * @since     1.0.0
	 */
	public function get_exclude_tools( $option ) {

    if ( !is_array( $option ) ) {
      return $option;
    }

    if ( isset( $option['export_options'] ) ) {
      unset( $option['export_options'] );
    }

    if ( isset( $option['import_options'] ) ) {
      unset( $option['import_options'] );
    }

		return $option;
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
