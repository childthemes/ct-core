<?php
/**
 * Name: Display Heading 4
 * Desc: Heading styling with big size.
 * Ends: true
 * Menu: content
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
  'class' 		=> '',
  'align'		  => 'aligncenter'
), $atts);

$class = !empty($atts['class']) ? $atts['class'].' '.$atts['align'] : $atts['align'];

echo '<h4 class="display-4 ' . esc_attr($class) . '">' . do_shortcode($content) . '</h4>';
