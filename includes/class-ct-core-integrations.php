<?php
/**
 * Integration CT_Core with plugin.
 *
 * @link     http://childthemes.net/
 * @author   Rizal Fauzie <fauzie@childthemes.net>
 *
 * @since      1.0.0
 * @package    CT_Core
 * @subpackage CT_Core/includes
 */

class CT_Core_Integrations {

  /**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

  /**
	 * Hold plugin integration directory name.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
  public $path;

  /**
	 * Hold plugin integration URL name.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
  public $url;

  /**
	 * Hold plugin slug and folder name curernt integration.
	 *
	 * @since    1.0.0
	 * @access   protected
	 */
  protected $integration;

  /**
	 * List of relative files to include with plugin integration.
	 *
	 * @since    1.0.0
	 * @access   protected
	 */
  protected $includes = array();

  /**
	 * This hold list of plugins slug name.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public static $integrations = array();

  /**
	 * Constructor parent.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {

    $this->version = CT_VERSION;
    $this->path = trailingslashit( CT_INC . 'integrations/' . $this->integration() );
		$this->url  = trailingslashit( CT_URI . 'includes/integrations/' . $this->integration() );

		self::$integrations[ $this->integration() ] = array(
			'dir' => $this->path,
			'url' => $this->url
		);

		$this->includes();
		$this->init();
	}

  /**
	 * Include and run inherit files from current integration.
	 *
	 * @since  1.0.0
   * @access private
	 */
	private function includes() {
		if ( ! isset( $this->includes ) || empty( $this->includes ) ) {
			return;
		}

		foreach ( $this->includes as $file ) {
			if( ! file_exists( $this->path.$file ) ) {
				continue;
			}
      include_once( $this->path.$file );
      $base_name = str_replace('class-', '', basename($file,'.php'));
      $base_name = str_replace($this->integration().'-', '', $base_name);
      $class_name = file_to_classname( $file, 'CT_Core' );
      if ( class_exists( $class_name ) && !isset( $GLOBALS['ctcore_integrations'][ $this->integration ][ $base_name ] ) )
        $GLOBALS['ctcore_integrations'][ $this->integration ][ $base_name ] = new $class_name;
		}
	}

  /**
	 * Load 'em All.
	 *
	 * @since  1.0.0
   * @access public
	 */
	public static function get_instance() {

    global $ctcore_integrations;

    $integrations = apply_filters( 'ctcore_integrations_dirs', get_sub_dirs( CT_INC.'integrations' ) );

    if ( empty( $integrations ) || !is_array( $integrations ) ) {
      return $ctcore_integrations;
    }

    foreach ( $integrations as $id_base ) {

      if ( ! is_plugin_active_by_slug( $id_base ) )
        continue;

      $plugfile = sprintf( '%1$sintegrations/%2$s/class-%2$s.php', CT_INC, $id_base );

      if ( file_exists( $plugfile ) && !isset($ctcore_integrations[ $id_base ]['instance']) ) {
        include_once( $plugfile );
        $class_name = file_to_classname( $id_base, 'CT_Core' );
        if ( class_exists( $class_name ) )
          $ctcore_integrations[ $id_base ]['instance'] = new $class_name;
      }
    }
    return $ctcore_integrations;
	}

  /**
	 * Locate template path if file exists.
   *
   * @since  1.0.0
   * @access public
	 */
	public function locate_template( $template, $template_name, $template_path ) {

    if ( $this->integration !== $template_path )
      return $template;

		$default_path = trailingslashit( $this->path.'templates' );

		if ( is_dir( $default_path ) && file_exists( $default_path.$template_name ) ) {
			$template = $default_path.$template_name;
		}

		return $template;
	}

  /**
	 * Function to get css file fron current integration.
	 *
	 * @since  1.0.0
   * @access public
	 */
  public function get_css( $name, $min = false ) {

    if ( !is_dir( $this->path.'css' ) )
      return $name.'.css';

    $root = trailingslashit( 'css' );
    $file = sanitize_title( basename( $name, '.css' ) );

    if ( false === SCRIPT_DEBUG || false !== $min ) {
      if ( file_exists( $this->path.$root.$file.'.min.css' ) ) {
        $file .= '.min';
      }
    }
    return esc_url( $this->url.$root.$file.'.css' );
  }

  /**
	 * Function to get javascript file fron current integration.
	 *
	 * @since  1.0.0
   * @access public
	 */
  public function get_js( $name, $min = false ) {

    if ( !is_dir( $this->path.'js' ) )
      return $name.'.js';

    $root = trailingslashit( 'js' );
    $file = sanitize_title( basename( $name, '.js' ) );

    if ( false === SCRIPT_DEBUG || false !== $min ) {
      if ( file_exists( $this->path.$root.$file.'.min.js' ) ) {
        $file .= '.min';
      }
    }
    return esc_url( $this->url.$root.$file.'.js' );
  }

  /**
	 * Method to get current integration slug.
	 *
	 * @since  1.0.0
   * @access public
	 */
	public function integration() {
		return $this->integration;
	}

  /**
	 * Get list of integration
	 *
	 * @since  1.0.0
   * @access public
	 */
	public static function get_integrations() {
		return self::$integrations;
	}

  /**
	 * Method to get current integration directory.
	 *
	 * @since  1.0.0
   * @access public
	 */
	public static function get_dir( $plug = null ) {
		return ! empty( $plug ) ? self::$integrations[ $plug ][ 'dir' ] : $this->path;
	}

  /**
	 * Method to get current integration URL.
	 *
	 * @since  1.0.0
   * @access public
	 */
	public static function get_url( $plug = null ) {
		return ! empty( $plug ) ? self::$integrations[ $plug ][ 'url' ] : $this->url;
	}

}