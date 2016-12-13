<?php

/**
 * Custom template tags for this plugin used by theme.
 * List of static function Helper.
 *
 * functions used across both the public-facing side of the site and the admin area.
 *
 * @link       http://childthemes.net/
 * @since      1.0.0
 *
 * @package    CT_Core
 * @subpackage CT_Core/includes
 */

 /**
  * The code that runs during plugin activation.
  */
 function activate_ctcore() {
	 require CT_INC.'libraries/class-ct-core-install.php';
	 new CT_Core_Install( 'activate' );
 }

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_ctcore() {
	require CT_INC.'libraries/class-ct-core-install.php';
	new CT_Core_Install( 'deactivate' );
}

/**
 * Add action links to the plugin list for Page Builder.
 *
 * @since 	1.0.0
 * @param   $links
 * @return  array
 */
function ct_core_plugin_action_links($links) {
	unset( $links['edit'] );
	$links[] = '<a href="https://themeforest.net/user/childthemes" target="_blank">' . __('Download Themes', 'ctcore') . '</a>';
	return $links;
}
add_action( 'plugin_action_links_'.CT_CORE, 'ct_core_plugin_action_links' );

/**
 * Cleanup shortcode javascript generated when switching theme.
 *
 * @since 	1.0.0
 */
function ctcore_remove_theme_cache() {
	deactivate_mtcore();
}
add_action( 'switch_theme', 'ctcore_remove_theme_cache' );


/**
 * Get all sub-directory in a directory.
 *
 * @since 	1.0.0
 * @param   $dir   string   dir path
 * @return  mixed
 */
if ( ! function_exists( 'get_sub_dirs' ) ) :
function get_sub_dirs( $path ) {
	$dirs = array();
  if ( empty($path) || !is_dir($path) ) return $dirs;
  $path = trailingslashit( $path );
  $dir  = new DirectoryIterator( $path );
  foreach ( $dir as $file ) {
    if ( $file->isDir() && !$file->isDot() ) {
      if ( is_dir( $path.'/'.$file->getFilename() ) )
        $dirs[] = $file->getFilename();
    }
  }
  return $dirs;
}
endif;

/**
 * Convert string file name to class name.
 *
 * @since 	1.0.0
 * @return 	string
 */
if ( !function_exists( 'file_to_classname' ) ) :
function file_to_classname( $file, $prefix = '', $suffix = '' ) {

 	$class_name	= basename( $file, ".php" );
 	$class_name = explode('-', $class_name);
 	$class_name = array_map('ucfirst', $class_name);

	foreach ($class_name as $key => $name) {
		if ( $name == 'Class' ) {
			unset( $class_name[ $key ] );
		} elseif ( $name == 'Ct' ) {
			$class_name[ $key ] = 'CT';
		}
	}
 	$class_name = implode('_', $class_name);

 	if ( ! empty( $prefix ) ) {
 		$class_name = esc_attr(ucfirst($prefix)).'_'.$class_name;
 	}

 	if ( ! empty( $suffix ) ) {
 		$class_name = $class_name.'_'.esc_attr(ucfirst($suffix));
 	}

 	return $class_name;
}
endif;

/**
 * Add admin notice
 *
 * @since 	1.0.0
 * @return 	string
 */
if ( !function_exists( 'add_admin_notice' ) ) :
function add_admin_notice( $status, $cause, $args = array() ) {
	include_once CT_INC . 'libraries/class-ct-core-notice.php';
	new CT_Core_Notice( $status, $cause, $args );
}
endif;

/**
 * Get stylesheet file.
 *
 * @since 	1.0.0
 * @return 	string  URL of CSS File
 */
if ( !function_exists( 'ctcore_css' ) ) :
function ctcore_css( $name, $min = false ) {
	$root = trailingslashit( 'assets/css' );
  $file = sanitize_title( basename( $name, '.css' ) );
  if ( false === SCRIPT_DEBUG || false !== $min ) {
    if ( file_exists( CT_PATH.$root.$file.'.min.css' ) ) {
      $file .= '.min';
    }
  }
  return esc_url( CT_URI.$root.$file.'.css' );
}
endif;

/**
 * Get script file.
 *
 * @since 	1.0.0
 * @return 	string  URL of JS File
 */
if ( !function_exists( 'ctcore_js' ) ) :
function ctcore_js( $name, $min = false ) {
	$root = trailingslashit( 'assets/js' );
  $file = sanitize_title( basename( $name, '.js' ) );
  if ( false === SCRIPT_DEBUG || false !== $min ) {
    if ( file_exists( CT_PATH.$root.$file.'.min.js' ) ) {
      $file .= '.min';
    }
  }
  return esc_url( CT_URI.$root.$file.'.js' );
}
endif;

/**
 * Escaped HTML Color HEX Value
 *
 * @since		1.0.0
 *
 * @return string color or empty
 */
if ( !function_exists( 'esc_color' ) ) :
function esc_color( $color ) {
	if ( preg_match( '/rgba/', $color ) ) {
    $color = preg_replace( array(
      '/\s+/',
      '/^rgba\((\d+)\,(\d+)\,(\d+)\,([\d\.]+)\)$/',
    ), array(
      '',
      'rgb($1,$2,$3)',
    ), $color );
  } else {
    $color = strtolower( ltrim($color, '#') );
    if ( ctype_xdigit($color) && (strlen($color) == 6 || strlen($color) == 3) ) {
      $color = '#'.$color;
    } else {
      $color = '';
    }
  }
  return $color;
}
endif;

/**
 * Getting all values for a custom field key
 *
 * @since		1.0.0
 *
 * @return mixed    list of meta key value from all posts
 */
if ( !function_exists( 'get_meta_values' ) ) :
function get_meta_values( $key = '', $type = 'post', $status = 'any', $unique = true ) {

  if( empty( $key ) )
      return;

  $cache_key = 'CT_'.md5( serialize( array( $key, $type, $status ) ) );

  if ( false === ( $r = get_transient( $cache_key ) ) ) {

  	global $wpdb,$wp_post_statuses;

		$std_status = array_keys( $wp_post_statuses );

		$stat = is_array( $status ) ? array_intersect( $status, $std_status ) : ( in_array( $status, $std_status ) ? $status : $std_status );
		$stat = is_array( $stat ) ? implode( "', '", $stat ) : $stat;
		$type = is_array( $type ) ? implode( "', '", $type ) : $type;
		$unique = $unique ? ' DISTINCT' : '';

  	$r = $wpdb->get_col( $wpdb->prepare("
  		SELECT{$unique} pm.meta_value FROM {$wpdb->postmeta} pm
  		LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
  		WHERE pm.meta_key = '%s'
  		AND p.post_type IN ( '{$type}' )
			AND p.post_status IN ( '{$stat}' )
  	", $key ) );

    set_transient( $cache_key, $r, HOUR_IN_SECONDS );
  }
	return $r;
}
endif;

/**
 * Retrieve domain name from URL.
 *
 * @since  1.0.0
 */
if ( ! function_exists( 'get_domain_name' ) ) :
function get_domain_name( $url ) {
  $pieces = parse_url($url);
  $domain = isset($pieces['host']) ? $pieces['host'] : '';
  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
    return $regs['domain'];
  }
  return false;
}
endif;

/**
 * Check if array is diffrence by value only.
 *
 * @since  1.0.0
 */
if ( ! function_exists( 'is_array_equal' ) ) :
function is_array_equal($a, $b) {
  return (
   is_array($a) && is_array($b) &&
   count($a) == count($b) &&
   array_diff($a, $b) === array_diff($b, $a)
  );
}
endif;

/**
 * Check if plugin is active by plugin slug.
 *
 * @since  1.0.0
 */
if ( ! function_exists( 'is_plugin_active_by_slug' ) ) :
function is_plugin_active_by_slug( $plugin ) {

  if ( empty( $plugin ) ) {
    return false;
  }

  $plugin = $plugin.'/'.$plugin.'.php';

  $network_active = false;
	if ( is_multisite() ) {
		$plugins = get_site_option( 'active_sitewide_plugins', array() );
		if ( isset( $plugins[ $plugin ] ) ) {
			$network_active = true;
		}
	}
	return in_array( $plugin, get_option( 'active_plugins', array() ) ) || $network_active;
}
endif;
