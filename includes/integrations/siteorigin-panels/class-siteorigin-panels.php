<?php
/**
 * Integration Plugin with Child Themes.
 *
 * @link       http://childthemes.net/
 * @author     Rizal Fauzie <fauzie@childthemes.net>
 *
 * @since      1.0.0
 * @package    CT_Core
 * @subpackage CT_Core/includes/integrations
 */

class CT_Core_Siteorigin_Panels extends CT_Core_Integrations {

  /**
	 * Class Constructor.
	 *
	 * @since  1.0.0
	 */
  public function __construct() {
    $this->includes = array(

    );
    $this->integration = 'siteorigin-panels';
    parent::__construct();
  }

  /**
	 * Action and Filter hooks.
	 */
	public function init() {

    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 1 );
    add_action( 'siteorigin_panel_enqueue_admin_styles', array( $this, 'add_admin_style' ) );

    add_action( 'admin_print_scripts-post-new.php', array( $this, 'add_admin_script' ), 9, 2 );
    add_action( 'admin_print_scripts-post.php', array( $this, 'add_admin_script' ), 9, 2 );
    add_action( 'admin_print_scripts-appearance_page_so_panels_home_page', array( $this, 'add_admin_script' ), 9, 2 );
    add_action( 'admin_print_scripts-widgets.php', array( $this, 'add_admin_script' ), 9, 2 );
    add_action( 'admin_print_scripts', array( $this, 'remove_admin_script' ) );

	  remove_action( 'customize_controls_print_footer_scripts', 'siteorigin_panels_customize_controls_print_footer_scripts' );
    add_action( 'customize_controls_print_footer_scripts', array( $this, 'js_templates' ) );

    add_filter( 'siteorigin_panels_column_ratios', array( $this, 'set_aspect_ratio' ) );
	}

  /**
	 * Enqueue scripts and styles.
	 *
	 * @since  1.0.0
	 */
  public function enqueue_styles() {

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
	 * Set custom aspect ration for row builder.
	 *
	 * @since  1.0.0
	 */
  public function set_aspect_ratio() {
    return array( 'Container' => 1, 'Container Fluid' => 1, '100% Width' => 1 );
  }

}