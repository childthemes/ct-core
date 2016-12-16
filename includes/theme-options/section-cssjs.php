<?php
/**
 * Name: Custom CSS & JS
 * Icon: File Code Outline
 * Sort: 98
 *
 * @link       http://childthemes.net/
 * @author     Rizal Fauzie <fauzie@childthemes.net>
 * @since      1.0.0
 */

return array(

  array(
    'id'          => 'custom_dynamic_css',
    'label'       => esc_html__( 'Custom CSS', 'ctcore' ),
    'desc'        => esc_html__( 'Add your custom modifier stylesheet here.', 'ctcore' ),
    'std'         => "body.childthemes {\n\twidth: 100%;\n}",
    'type'        => 'css'
  ),
  array(
    'id'          => 'custom_dynamic_js',
    'label'       => esc_html__( 'Custom jQuery Script', 'ctcore' ),
    'desc'        => esc_html__( 'Add your custom modifier script here. This script will be wrapped inside &lt;br/&gt; &lt;code&gt; jQuery(document).ready(function($){ --your code goes here-- }); &lt;/code&gt;.', 'ctcore' ),
    'std'         => "$( 'body' ).addClass( 'childthemes' );",
    'type'        => 'javascript'
  )

);
