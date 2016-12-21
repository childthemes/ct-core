<?php
/**
 * Name: Current Date
 * Desc: Display current date with custom format.
 * Ends: false
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
	'format' => ''
), $atts);

if ( empty( $atts['format'] ) ) {
	return;
}

$nowis = date_i18n( $atts['format'], time() );
$micro = date_i18n( 'Y-m-d', time() );

if ( $nowis )
	echo '<time datetime="' . esc_attr($micro) . '">' . $nowis . '</time>';
