<?php
/**
 * Integration Plugin with Contact Form 7
 *
 * @wordpress-plugin https://wordpress.org/plugins/contact-form-7/
 * @wordpress-plugin https://wordpress.org/plugins/bootstrap-for-contact-form-7/
 *
 * @link       http://childthemes.net/
 * @author     Rizal Fauzie <fauzie@childthemes.net>
 * @author     Felix Arntz <felix-arntz@leaves-and-love.net>
 *
 * @since      1.0.0
 * @package    CT_Core
 * @subpackage CT_Core/includes/builders
 */

class CT_Core_Contact_Form_7 extends CT_Core_Builder {

  /**
	 * Class Constructor.
	 *
	 * @since  1.0.0
	 */
  public function __construct() {
    if ( is_plugin_active_by_slug( 'contact-form-7' ) ) {
      $GLOBALS['ctcore_laodform_cssjs'] = false;
      $this->builder = 'contact-form-7';
      parent::__construct();
    } else {
      return null;
    }
  }

  /**
	 * Action and Filter hooks.
	 */
	public function init() {

    if ( !defined( 'WPCF7_AUTOP' ) ) {
      define( 'WPCF7_AUTOP', false );
    }

    if ( !defined( 'WPCF7_LOAD_CSS' ) ) {
      define( 'WPCF7_LOAD_CSS', false );
    }

    add_filter( 'wpcf7_default_template', array( $this, 'default_template' ), 20, 2 );
    add_action( 'plugins_loaded', array( $this, 'init_wpcf7_module' ), 60 );
	}

  /**
	 * Default tempplate when user installing Contact Form 7 Plugin.
	 *
	 * @since  1.0.0
	 */
  public function default_template( $template, $wpcf7 ) {
    return $template;
  }

  /**
	 * Enqueue scripts and styles.
	 *
	 * @since  1.0.0
	 */
  public function enqueue_cssjs() {

    wp_dequeue_style( 'contact-form-7' );

    // Default Form Style & Script
    wp_register_style( 'ctcore-cf7bs-style', CT_ASSETS . 'css/form-style.css', array(), null );
    wp_register_script( 'ctcore-cf7bs-script', CT_ASSETS . 'js/form-script.js', array( 'jquery', 'jquery-form', 'contact-form-7' ), null, true );

    // Form input number/range slider style & script
    wp_register_style( 'ctcore-bootstrap-slider', CT_ASSETS . 'css/bootstrap-slider.min.css', array(), '9.5.4' );
    wp_register_script( 'ctcore-bootstrap-slider', CT_ASSETS . 'js/bootstrap-slider.min.js', array( 'jquery', 'ctcore-cf7bs-script' ), '9.5.4', true );

    // Form field date/time picker
    wp_register_style( 'ctcore-bootstrap-datepicker', CT_ASSETS . 'css/bootstrap-datepicker.min.css', array(), '1.6.4' );
    wp_register_script( 'ctcore-bootstrap-datepicker', CT_ASSETS . 'js/bootstrap-datepicker.min.js', array( 'jquery', 'ctcore-cf7bs-script' ), '1.6.4', true );

  }

  /**
	 * Init required files.
	 *
	 * @since  1.0.0
	 */
  public function init_wpcf7_module() {

    /**
     * Check for compatibility with : Bootstrap for Contact Form 7
     * if user has installed that plugin, this feature is not required anymore.
     * @More Info: https://wordpress.org/plugins/bootstrap-for-contact-form-7/
     */
    if ( defined( 'CF7BS_VERSION' ) ) {
      return;
    }

    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_cssjs' ), 1 );

    /**
     * Init Bootstrap for Contact Form 7 libraries.
     */
    $this->init_cf7bs();

    $modules = array(
			'acceptance',
			'count',
			'date',
			'file',
			'select',
			'submit',
			'number',
			'text',
			'checkbox',
			'textarea',
		);

    foreach ( $modules as $module ) {
      $file = sprintf( '%smodules/%s.php', $this->path, $module );
      if ( file_exists( $file ) ) {
        include_once $file;
      }
    }

  }

  /**
	 * Hooks for Bootstrap Contact Form 7.
	 *
	 * @since  1.0.0
	 */
  private function init_cf7bs() {

    $libraries = array(
      'Component',
      'Functions',
      'Alert',
      'Button',
      'Button_Group',
      'Form_Field' );

    foreach ( $libraries as $lib ) {
      $file = sprintf( '%sclasses/CF7BS_%s.php', $this->path, $lib );
      if ( file_exists( $file ) ) {
        include_once $file;
      }
    }
  }

}
