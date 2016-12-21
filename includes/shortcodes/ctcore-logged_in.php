<?php
/**
 * Name: Content Restriction
 * Desc: Content restriction for guest or logged in user only.
 * Ends: true
 * Menu: other
 *
 *
 * @param      $atts  shortcode attributes user value
 *
 * @link       http://childthemes.net/
 * @since      1.0.0
 *
 * @package    CT_Core
 * @subpackage CT_Core/includes/shortcodes
 */

$atts = shortcode_atts(array(
	'hide'		=> '',
	'role'	  => 'subscriber',
	'message'	=> ''
), $atts);

$roles = !empty($atts['role']) ? $atts['role'] : 'subscriber';

if ( empty($atts['hide']) || $atts['hide'] != '1' ) {
	$is_can = ( is_user_logged_in() && current_user_can( $roles ) );
} else {
	$is_can = ( !is_user_logged_in() );
}

if ( $is_can ) {
	echo do_shortcode($content);
}
elseif ( !empty($atts['message']) ) {
	echo do_shortcode($atts['message']);
}
