<?php
/**
 * TinyMCE editor fields
 * lorem_ipsum shortcode.
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
    'type'    => 'listbox',
    'name'    => 'type',
    'label'   => esc_html__( 'Text Type', 'ctcore' ),
    'values'  => array(
      array( 'value' => 'words', 'text' => esc_attr__( 'Words', 'ctcore' ) ),
      array( 'value' => 'sentences', 'text' => esc_attr__( 'Sentences', 'ctcore' ) ),
      array( 'value' => 'paragraphs', 'text' => esc_attr__( 'Paragraphs', 'ctcore' ) )
    )
  ),

	array(
    'type'    => 'textbox',
    'name'    => 'number',
    'value'   => '3',
    'label'   => esc_attr__( 'Generated Count', 'ctcore' )
  ),

	array(
    'type'    => 'checkbox',
    'name'    => 'unique',
    'text'    => esc_attr__( 'Force Unique Random Generated', 'ctcore' ),
		'label'   => esc_attr__( 'Always Generate New', 'ctcore' ),
		'tooltip' => esc_attr__( 'Check to force generate new text every page loaded', 'ctcore' ),
  ),

);
