<?php
/**
 * TinyMCE editor fields
 * audio shortcode.
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
    'type'    => 'textbox',
    'name'    => 'src',
    'value'   => '',
		'placeholder' => '.mp3, .m4a, .ogg, .wav, .wma',
    'label'   => esc_attr__( 'Audio File', 'ctcore' ),
    'tooltip' => esc_attr__( 'leave empty to get first audio file attached to the post', 'ctcore' ),
  ),

	array(
    'type'    => 'listbox',
    'name'    => 'loop',
    'value'   => 'off',
    'label'   => esc_attr__( 'Looping Media', 'ctcore' ),
    'values'  => array(
			array( 'value' => 'off', 'text' => esc_attr__( 'Do not loop the media', 'ctcore' ) ),
			array( 'value' => 'on', 'text' => esc_attr__( 'Loop to beginning when finished', 'ctcore' ) ),
		)
  ),

	array(
    'type'    => 'listbox',
    'name'    => 'autoplay',
    'value'   => 'off',
    'label'   => esc_attr__( 'Automatically Play', 'ctcore' ),
    'values'  => array(
			array( 'value' => 'off', 'text' => esc_attr__( 'Do not automatically play the media', 'ctcore' ) ),
			array( 'value' => 'on', 'text' => esc_attr__( 'Media will play as soon as it is ready', 'ctcore' ) ),
		)
  ),

	array(
    'type'    => 'listbox',
    'name'    => 'preload',
    'value'   => 'none',
    'label'   => esc_attr__( 'Auto Load', 'ctcore' ),
    'values'  => array(
			array( 'value' => 'none', 'text' => esc_attr__( 'Do not load media when page loads', 'ctcore' ) ),
			array( 'value' => 'auto', 'text' => esc_attr__( 'Load entirely when the page loads', 'ctcore' ) ),
			array( 'value' => 'metadata', 'text' => esc_attr__( 'Only metadata should be loaded', 'ctcore' ) ),
		)
  )

);
