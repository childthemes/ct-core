<?php
/**
 * Integration Plugin with Siteorigin Panels Page Builder
 * 
 * @wordpress-plugin https://wordpress.org/plugins/siteorigin-panels/
 *
 * @link       http://childthemes.net/
 * @author     Rizal Fauzie <fauzie@childthemes.net>
 *
 * @since      1.0.0
 * @package    CT_Core
 * @subpackage CT_Core/includes/builders
 */

class CT_Core_Siteorigin_Panels extends CT_Core_Builder {

  /**
	 * Class Constructor.
	 *
	 * @since  1.0.0
	 */
  public function __construct() {
    $this->includes = array(
     'class-siteorigin-panels-template.php'
    );
    $this->builder = 'siteorigin-panels';
    parent::__construct();
  }

  /**
	 * Action and Filter hooks.
	 */
	public function init() {

    add_action( 'plugins_loaded', array( $this, 'remove_so_hooks' ) );
    add_action( 'siteorigin_panel_enqueue_admin_styles', array( $this, 'add_admin_style' ) );

    add_action( 'admin_print_scripts-post-new.php', array( $this, 'add_admin_script' ), 9, 2 );
    add_action( 'admin_print_scripts-post.php', array( $this, 'add_admin_script' ), 9, 2 );
    add_action( 'admin_print_scripts-appearance_page_so_panels_home_page', array( $this, 'add_admin_script' ), 9, 2 );
    add_action( 'admin_print_scripts-widgets.php', array( $this, 'add_admin_script' ), 9, 2 );
    add_action( 'admin_print_scripts', array( $this, 'remove_admin_script' ) );

	  remove_action( 'customize_controls_print_footer_scripts', 'siteorigin_panels_customize_controls_print_footer_scripts' );
    add_action( 'customize_controls_print_footer_scripts', array( $this, 'js_templates' ) );

    add_filter( 'siteorigin_panels_row_style_fields', array( $this, 'set_row_fields' ), 99 );
	}

  public function remove_so_hooks() {
    remove_action('wp_head', 'siteorigin_panels_print_inline_css', 12);
    remove_action('wp_footer', 'siteorigin_panels_print_inline_css');
    remove_action('wp_enqueue_scripts', 'siteorigin_panels_enqueue_styles', 1);
  }

  /**
	 * Enqueue scripts and styles.
	 *
	 * @since  1.0.0
	 */
  public function enqueue_styles() {}

  /**
	 * Enqueue scripts and styles on admin area.
	 *
	 * @since  1.0.0
	 */
  public function add_admin_style() {
    wp_enqueue_style( 'so-panel-custom', $this->get_css( 'so-custom' ) );
  }

  /**
	 * Custom JS Template for page builder.
	 *
	 * @since  1.0.0
	 */
  public function js_templates() {
    include $this->path.'templates/js-template.php';
  }

  /**
	 * Custom JS Template for page builder.
	 *
	 * @since  1.0.0
	 */
  public function add_admin_script( $prefix = '', $force = false ) {

    if ( $force || siteorigin_panels_is_admin_page() ) {
      wp_enqueue_script( 'so-panels-admin', $this->url . 'js/so-custom.js', array( 'jquery', 'jquery-ui-resizable', 'jquery-ui-sortable', 'jquery-ui-draggable', 'underscore', 'backbone', 'plupload', 'plupload-all' ), null, true );
      add_action( 'admin_footer', array( $this, 'js_templates' ) );
    }
  }

  public function remove_admin_script() {
    remove_action( 'admin_footer', 'siteorigin_panels_js_templates' );
  }

  /**
	 * Set custom fields for row builder.
	 *
	 * @since  1.0.0
	 */
  public function set_row_fields( $fields ) {

    if ( isset( $fields['gutter'] ) )
      unset( $fields['gutter'] );

    if ( isset( $fields['row_stretch'] ) )
      unset( $fields['row_stretch'] );

    if ( isset( $fields['border_color'] ) )
      unset( $fields['border_color'] );

    if ( isset( $fields['collapse_order'] ) )
      unset( $fields['collapse_order'] );

    $fields['container'] = array(
      'name'    => esc_html__( 'Container Style', 'ctcore' ),
      'type'    => 'select',
      'group'   => 'layout',
      'priority'=> 1,
      'options' => array(
        'container' => esc_attr__( 'Default Container', 'ctcore' ),
        'fluid'     => esc_attr__( 'Fluid Container', 'ctcore' ),
        'none'      => esc_attr__( 'No Container', 'ctcore' ),
      )
    );
    
    $fields['breakpoint'] = array(
      'name'    => esc_html__( 'Default Column Views', 'ctcore' ),
      'type'    => 'select',
      'group'   => 'layout',
      'default' => 'md',
      'priority'=> 2,
      'options' => array(
        'xs'  => esc_attr__( 'Mobile', 'ctcore' ),
        'sm'  => esc_attr__( 'Tablet', 'ctcore' ),
        'md'  => esc_attr__( 'Desktop', 'ctcore' ),
        'lg'  => esc_attr__( 'Large Desktop', 'ctcore' ),
      )
    );

    return $fields;
  }

}
