<?php
/**
 * Name: Lorem Ipsum Generator
 * Desc: Displaying a generated random text lorem ipsum
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
	'type' 		=> 'words',
	'number'	=> '5',
	'unique'	=> ''
), $atts);

$unique = ( empty( $atts['unique'] ) || $atts['unique'] != '1' ) ? false : true;
$number = !empty( $atts['number'] ) ? $atts['number'] : 1;
$type   = !empty( $atts['type'] ) ? $atts['type'] : 'sentences';
$lpsum  = get_lorem_ipsum( $type, $number, $unique );

echo wpautop($lpsum);
