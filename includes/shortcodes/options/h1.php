<?php
/**
 * TinyMCE editor fields
 * heading H1 shortcode.
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
    'name'    => 'class',
    'value'   => '',
    'label'   => esc_attr__( 'Extra Class', 'ctcore' )
  ),

	array(
    'type'    => 'listbox',
    'name'    => 'align',
    'label'   => esc_html__( 'Heading Align', 'ctcore' ),
    'values'  => array(
      array( 'value' => 'aligncenter', 'text' => esc_attr__( 'Align Center', 'ctcore' ), 'selected' => true ),
      array( 'value' => 'alignleft', 'text' => esc_attr__( 'Align Left', 'ctcore' ) ),
      array( 'value' => 'alignright', 'text' => esc_attr__( 'Align Right', 'ctcore' ) )
    )
  ),

);
