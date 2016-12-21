<?php
/**
 * TinyMCE editor fields
 * playlist shortcode.
 *
 * @return     mixed
 *
 * @link       http://childthemes.net/
 * @since      1.0.0
 *
 * @package    CT_Core
 * @subpackage CT_Core/includes/shortcodes
 */

return array(

	array(
    'type'    => 'combobox',
    'name'    => 'ids',
    'label'   => esc_html__( 'Image IDs', 'ctcore' ),
    'values'  => array(),
		'tooltip' => esc_attr__( 'attachment IDs separated by comma', 'ctcore' )
  ),

	array(
		'type'		=> 'container',
		'html'		=> __( 'leave empty to collect all audio or video from current post', 'ctcore' ),
	),

	array(
    'type'    => 'listbox',
    'name'    => 'type',
    'label'   => esc_html__( 'Content Type', 'ctcore' ),
    'values'  => array(
      array( 'value' => 'audio', 'text' => esc_attr__( 'Audio Playlist', 'ctcore' ) ),
      array( 'value' => 'video', 'text' => esc_attr__( 'Video Playlist', 'ctcore' ) ),
    )
  ),

	array(
    'type'    => 'listbox',
    'name'    => 'style',
    'label'   => esc_html__( 'Color Style', 'ctcore' ),
    'values'  => array(
      array( 'value' => 'dark', 'text' => esc_attr__( 'Light Color', 'ctcore' ) ),
      array( 'value' => 'light', 'text' => esc_attr__( 'Dark Color', 'ctcore' ) ),
    )
  )

);
