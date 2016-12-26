<?php
/**
 * Date module
 *
 * @package CT_Core
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'CT_Core_Contact_Form_7_Modules_Date' ) ) :
class CT_Core_Contact_Form_7_Modules_Date {

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
      'date',
		  'date*',
    );
    foreach ( $tags as $tag ) {
      call_user_func( $remove_func, $tag );
    }

    call_user_func( $add_func, $tags, array( $this, 'shortcode_handler' ), true );
  }

  public function shortcode_handler( $tag ) {

    $classname = class_exists( 'WPCF7_FormTag' ) ? 'WPCF7_FormTag' : 'WPCF7_Shortcode';

    $tag_obj = new $classname( $tag );

    if ( empty( $tag_obj->name ) ) {
      return '';
    }

    wp_enqueue_style( 'ctcore-bootstrap-datepicker' );
    wp_enqueue_script( 'ctcore-bootstrap-datepicker' );

    $mode = $status = 'default';

    $validation_error = wpcf7_get_validation_error( $tag_obj->name );

    $class = wpcf7_form_controls_class( $tag_obj->type );
    $class .= ' wpcf7-validates-as-date';

    if ( $validation_error ) {
      $class .= ' wpcf7-not-valid';
      $status = 'danger';
    }

    if ( $tag_obj->is_required() ) {
      $mode = 'required';
    }

    $value = (string) reset( $tag_obj->values );
    $placeholder = '';
    if ( $tag_obj->has_option( 'placeholder' ) || $tag_obj->has_option( 'watermark' ) ) {
      $placeholder = $value;
      $value = '';
    }

    $value = $tag_obj->get_default_option( $value );

    if ( wpcf7_is_posted() && isset( $_POST[ $tag_obj->name ] ) ) {
      $value = stripslashes_deep( $_POST[ $tag_obj->name ] );
    } elseif( isset( $_GET ) && array_key_exists( $tag_obj->name, $_GET ) ) {
      $value = stripslashes_deep( rawurldecode( $_GET[ $tag_obj->name ] ) );
    }

    $field = new CF7BS_Form_Field( array(
      'name'				=> $tag_obj->name,
      'id'				=> $tag_obj->get_option( 'id', 'id', true ),
      'class'				=> $tag_obj->get_class_option( $class ),
      'type'				=> wpcf7_support_html5() ? $tag_obj->basetype : 'text',
      'value'				=> $value,
      'placeholder'		=> $placeholder,
      'label'				=> $tag_obj->content,
      'options'			=> array(
        'min'					=> $tag_obj->get_option( 'min', 'date', true ),
        'max'					=> $tag_obj->get_option( 'max', 'date', true ),
      ),
      'help_text'			=> $validation_error,
      'size'				=> cf7bs_get_form_property( 'size', 0, $tag_obj ),
      'grid_columns'		=> cf7bs_get_form_property( 'grid_columns', 0, $tag_obj ),
      'form_layout'		=> cf7bs_get_form_property( 'layout', 0, $tag_obj ),
      'form_label_width'	=> cf7bs_get_form_property( 'label_width', 0, $tag_obj ),
      'form_breakpoint'	=> cf7bs_get_form_property( 'breakpoint', 0, $tag_obj ),
      'mode'				=> $mode,
      'status'			=> $status,
      'readonly'			=> $tag_obj->has_option( 'readonly' ) ? true : false,
      'tabindex'			=> $tag_obj->get_option( 'tabindex', 'int', true ),
      'wrapper_class'		=> $tag_obj->name,
      'label_class'       => $tag_obj->get_option( 'label_class', 'class', true ),
    ) );

    $html = $field->display();

    return $html;
  }

}
new CT_Core_Contact_Form_7_Modules_Date();
endif;
