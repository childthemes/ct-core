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

    add_action( 'wp_head', array( $this, 'removeinline_css' ), 11 );
    add_action( 'wp_footer', array( $this, 'removeinline_css' ), 9 );

    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 99 );
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

  public function removeinline_css() {
    $GLOBALS['siteorigin_panels_inline_css'] = array();
  }

  /**
	 * Enqueue scripts and styles.
	 *
	 * @since  1.0.0
	 */
  public function enqueue_styles() {
    if ( wp_style_is( 'siteorigin-panels-front' ) ) {
      wp_dequeue_style( 'siteorigin-panels-front' );
    }
  }

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
      wp_enqueue_script( 'so-panels-admin', $this->get_js( 'so-custom' ), array( 'jquery', 'jquery-ui-resizable', 'jquery-ui-sortable', 'jquery-ui-draggable', 'underscore', 'backbone', 'plupload', 'plupload-all' ), null, true );
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

    return $fields;
  }

}
