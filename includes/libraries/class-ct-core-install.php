<?php
/**
 * WordPress CleanUp & White Labeling Loader Class
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
 *  CT_Core_Clean - Minimal Themes Framework
 *
 * @author  Minimal Themes
 * @since 	1.0.0
 */
class CT_Core_Install {

	/**
	 * Active var of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var      boolean
	 */
	protected $active = false;

  /**
	 * Fire it up
	 *
	 * @since  1.0.0
	 */
	public function __construct( $method = 'activate' ) {
		if ( 'deactivate' === $method ) {
			$this->deactivate();
		} elseif ( 'activate' === $method ) {
			$this->activate();
		}
	}

  /**
	 * Activate function.
	 *
	 * @since     1.0.0
	 *
	 * @return    void
	 */
	public function activate() {
		// create plugin temp dir
		$this->mkdir();
    // clean all transient
		$this->delete_transient();
		// set globals value
		$GLOBALS['CT_Core'] = null;
    // add default features to option
		add_option( 'ctcore_features', array() );
		add_option( 'ctcore_theme_options_id', '' );
		// flush option cache
		wp_cache_delete( 'alloptions', 'options' );
		// flush rewrite rules always
		flush_rewrite_rules();
	}
  /**
	 * Deactivate function.
	 *
	 * @since     1.0.0
	 *
	 * @return    void
	 */
	public function deactivate() {
		// delete shortcode js
		$this->clean_temp();
		// clean all transient
		$this->delete_transient();
		// remove globals value
		unset( $GLOBALS['CT_Core'] );
    // delete features option
		delete_option( 'ctcore_features' );
		delete_option( 'ctcore_theme_options_id' );
		// flush option cache
		wp_cache_delete( 'alloptions', 'options' );
		// flush rewrite rules always
		flush_rewrite_rules();
	}

  /**
	 * Create new dir for this plugin.
	 *
	 * @since     1.0.0
	 *
	 * @return    boolean
	 */
	public function mkdir() {

		$upload_dir = wp_upload_dir();
		$upload_path = trailingslashit( $upload_dir['basedir'] );
		if ( $upload_dir['error'] )
			return false;

		if ( wp_mkdir_p( $upload_path.'ct-core' ) ) {
			$sc_temp = file_get_contents( CT_PATH.'assets/js/shortcodes.js' );
			file_put_contents( $upload_path.'ct-core/shortcodes.js', $sc_temp );
		}
		return true;
	}

	/**
	 * Remove shortcode temp for this plugin.
	 *
	 * @since     1.0.0
	 *
	 * @return    boolean
	 */
	public function clean_temp() {
		$upload_dir = wp_upload_dir();
		$upload_path = trailingslashit( $upload_dir['basedir'] );
		if ( !$upload_dir['error'] && is_dir( $upload_path.'ct-core' ) ) {
      $files = glob( $upload_path.'ct-core/*' );
      foreach ( $files as $file ) {
        if ( is_file( $file ) )
          unlink( $file ); // delete file
      }
			$sc_temp = file_get_contents( CT_PATH.'assets/js/shortcodes.js' );
			file_put_contents( $upload_path.'ct-core/shortcodes.js', $sc_temp );
			return true;
		}
		return false;
	}

	/**
	 * Remove all transient create by plugin.
	 *
	 * @since     1.0.0
	 *
	 * @return    boolean
	 */
	public function delete_transient() {

		global $wpdb;
		$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE ('\_transient%\_CT\_%')");

    return true;
	}

} //CT_Core_Install
