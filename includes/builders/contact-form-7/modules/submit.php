<?php
/**
 * Submit module
 *
 * @package CT_Core
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'CT_Core_Contact_Form_7_Modules_Submit' ) ) :
class CT_Core_Contact_Form_7_Modules_Submit {

  /**
	 * Class Constructor.
	 */
  public function __construct() {
    add_action( 'wpcf7_init', array( $this, 'add_shortcode' ), 11 );
  }

  public function add_shortcode() {
    $add_func    = function_exists( 'wpcf7_add_form_tag' )    ? 'wpcf7_add_form_tag'    : 'wpcf7_add_shortcode';
    $remove_func = function_exists( 'wpcf7_remove_form_tag' ) ? 'wpcf7_remove_form_tag' : 'wpcf7_remove_shortcode';

    $tags = array(
      'submit'
    );
    foreach ( $tags as $tag ) {
      call_user_func( $remove_func, $tag );
    }

    call_user_func( $add_func, $tags, array( $this, 'shortcode_handler' ), true );
  }

  public function shortcode_handler( $tag ) {

    $classname = class_exists( 'WPCF7_FormTag' ) ? 'WPCF7_FormTag' : 'WPCF7_Shortcode';

    $tag_obj = new $classname( $tag );

    $class = wpcf7_form_controls_class( $tag_obj->type );

    $value = isset( $tag_obj->values[0] ) ? $tag_obj->values[0] : '';
    if ( empty( $value ) ) {
      $value = esc_attr__( 'Send', 'contact-form-7' );
    }

    $size = cf7bs_get_form_property( 'submit_size', 0, $tag_obj );
    if ( ! $size ) {
      $size = cf7bs_get_form_property( 'size', 0, $tag_obj );
    }

    $button = new CF7BS_Button( array(
      'mode'				=> 'submit',
      'id'				  => $tag_obj->get_option( 'id', 'id', true ),
      'class'				=> $tag_obj->get_class_option( $class ),
      'title'				=> $value,
      'type'				=> cf7bs_get_form_property( 'submit_type', 0, $tag_obj ),
      'icon'				=> $tag_obj->get_option( 'icon', 'class', false ),
      'size'				=> $size,
      'tabindex'		=> $tag_obj->get_option( 'tabindex', 'int', true ),
      'align'				=> $tag_obj->get_option( 'align', '[A-Za-z]+', true ),
      'outline'			=> $tag_obj->get_option( 'outline', 'int', true ),
      'grid_columns'	=> $tag_obj->get_option( 'grid_columns', 'int', true ),
      'form_layout'		=> cf7bs_get_form_property( 'layout', 0, $tag_obj ),
      'form_label_width'=> cf7bs_get_form_property( 'label_width', 0, $tag_obj ),
      'form_breakpoint'	=> cf7bs_get_form_property( 'breakpoint', 0, $tag_obj ),
    ) );

    $html = $button->display();

    return $html;
  }

}
new CT_Core_Contact_Form_7_Modules_Submit();
endif;
