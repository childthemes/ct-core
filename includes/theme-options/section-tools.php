<?php
/**
 * Name: Tools
 * Icon: Exchange
 * Sort: 99
 *
 * @link       http://childthemes.net/
 * @author     Rizal Fauzie <fauzie@childthemes.net>
 * @since      1.0.0
 */

/* get theme options data */
$data = get_option( ot_options_id() );
$data = ! empty( $data ) ? ot_encode( serialize( $data ) ) : '';

return array(

  array(
    'id'          => 'export_options',
    'label'       => esc_html__( 'Export Options Data', 'ctcore' ),
    'desc'        => esc_html__( 'Export your Theme Options data by highlighting this text and doing a copy/paste into a blank .txt file. Then save the file for importing into another install of WordPress later. Or just paste it into the &lt;strong&gt;Import Options Data&lt;/strong&gt; textarea on another web site.', 'ctcore' ),
    'type'        => 'textarea_simple',
    'rows'        => 8,
    'std'         => $data
  ),
  array(
    'id'          => 'import_options',
    'label'       => esc_html__( 'Import Options Data', 'ctcore' ),
    'desc'        => esc_html__( 'To import your Theme Options copy and paste what appears to be a random string of alpha numeric characters into this textarea and press the &lt;strong&gt;Save Changes&lt;/strong&gt; button.', 'ctcore' ),
    'type'        => 'textarea_simple',
    'rows'        => 8,
  ),

);