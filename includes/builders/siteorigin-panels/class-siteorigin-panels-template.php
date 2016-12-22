<?php
/**
 * Integration Plugin with Siteorigin Panels Page Builder
 * 
 * @wordpress-plugin https://wordpress.org/plugins/siteorigin-panels/  
 *
 * @link       http://childthemes.net/
 * @author     Rizal Fauzie <fauzie@childthemes.net>
 *
 * @since      1.0.0
 * @package    CT_Core
 * @subpackage CT_Core/includes/integrations
 */

class CT_Core_Siteorigin_Panels_Template extends CT_Core_Builder {

  /**
	 * Class Constructor.
	 *
	 * @since  1.0.0
	 */
  public function __construct() {
    $this->builder = 'siteorigin-panels';
    parent::__construct();
  }

  /**
	 * Action and Filter hooks.
	 */
	public function init() {

    add_filter( 'siteorigin_panels_layout_attributes', array( $this, 'layout_attributes' ), 99, 3 );

    add_filter( 'siteorigin_panels_before_content', array( $this, 'before_content' ), 99 );
    add_filter( 'siteorigin_panels_after_content', array( $this, 'after_content' ), -99 );

    add_filter( 'siteorigin_panels_before_row', array( $this, 'before_row' ), 99, 3 );
    add_filter( 'siteorigin_panels_after_row', array( $this, 'after_row' ), -99, 3 );

    add_filter( 'siteorigin_panels_row_classes', array( $this, 'row_classes' ), 99, 2 );
    add_filter( 'siteorigin_panels_row_attributes', array( $this, 'row_attributes' ), 99, 2 );
    add_filter( 'siteorigin_panels_row_style_attributes', '__return_empty_array', 99 );
    add_filter( 'siteorigin_panels_row_cell_classes', array( $this, 'row_cell_classes' ), 99, 2 );
    add_filter( 'siteorigin_panels_row_cell_attributes', array( $this, 'row_cell_atrs' ), 99, 2 );
    
    add_filter( 'siteorigin_panels_cell_style_attributes', '__return_empty_array', 999 );
    
    //add_filter( 'siteorigin_panels_widget_classes', array( $this, 'widget_classes' ), 99, 3 );
	}

  /**
	 * Layout wrapper HTML div attribute.
	 *
	 * @since  1.0.0
	 * @return mixed
	 */
  public function layout_attributes( $attrs, $post_id, $post_data ) {
    return array(
      'id'    => 'primary',
      'class' => 'content-area content-' . absint( $post_id ) . implode( ' ', (array)$attrs['class'] )
    );
  }

  /**
	 * Add extra HTML before grid content.
	 *
	 * @since  1.0.0
	 * @return string
	 */
  public function before_content( $content ) {
    $GLOBALS['ct_so_grid'] = 0;
    $content .= '<main id="main" class="site-main" role="main">';
    return $content;
  }

  /**
	 * Add extra HTML after grid content.
	 *
	 * @since  1.0.0
	 * @return string
	 */
  public function after_content( $content ) {
    $content .= '</main>';
    return $content;
  }

  /**
	 * Add extra HTML before row content.
	 *
	 * @since  1.0.0
	 * @return string
	 */
  public function before_row( $content, $panel_data, $grid_attrs ) {
    $GLOBALS['ct_so_cell'] = 0;
    $GLOBALS['ct_so_cell_class'] = '';
    $section_id = !empty( $panel_data['style']['id'] ) ? sanitize_html_class( $panel_data['style']['id'] ) : uniqid( 'section-' );
    $classes = !empty( $panel_data['style']['class'] ) ? ' '.esc_attr( $panel_data['style']['class'] ) : '';
    if ( !empty( $panel_data['style']['background_display'] ) && in_array( $panel_data['style']['background_display'], array( 'parallax', 'parallax-original' ) ) ) {
      $classes .= ' bg-parallax';
    }
    if ( !empty($panel_data['style']['cell_class']) ) {
      $GLOBALS['ct_so_cell_class'] = esc_attr( $panel_data['style']['cell_class'] );
    }

    $style = array();
    $parallax = '';

    if ( !empty( $panel_data['style']['background'] ) )
      $style[] = 'background-color: '.esc_color($panel_data['style']['background']);

    if ( !empty( $panel_data['style']['background_image_attachment'] ) ) {
      $img_url = wp_get_attachment_image_src( $panel_data['style']['background_image_attachment'], 'full' );
      if ( !empty($img_url[0]) ) {
        $style[] = 'background-image: url('.esc_url($img_url[0]).')';
        if ( in_array( $panel_data['style']['background_display'], array( 'parallax', 'parallax-original' ) ) ) {
          wp_enqueue_script('siteorigin-panels-front-styles');
          wp_enqueue_script('siteorigin-parallax');
					$parallax_args = array(
						'backgroundUrl' => $img_url[0],
						'backgroundSize' => array( $img_url[1], $img_url[2] ),
						'backgroundSizing' => $panel_data['style']['background_display'] == 'parallax-original' ? 'original' : 'scaled',
						'limitMotion' => siteorigin_panels_setting( 'parallax-motion' ) ? floatval( siteorigin_panels_setting( 'parallax-motion' ) ) : 'auto',
					);
					$parallax = ' data-siteorigin-parallax=\'' . json_encode( $parallax_args ) . '\'';
          $style[] = 'background-repeat: no-repeat';
        }
        elseif ( !empty( $panel_data['style']['background_display'] ) ) {
          if ( $panel_data['style']['background_display'] == 'center' ) {
          $style[] = 'background-position: center center';
          }
          if ( $panel_data['style']['background_display'] == 'fixed' ) {
          $style[] = 'background-position: fixed';
          }
          elseif ( $panel_data['style']['background_display'] == 'cover' ) {
          $style[] = 'background-size: cover';
          }
          if ( $panel_data['style']['background_display'] != 'tile' ) {
            $style[] = 'background-repeat: no-repeat';
          }
        }
      }
    }

    if ( !empty( $panel_data['style']['padding'] ) )
      $style[] = 'padding: '.esc_attr($panel_data['style']['padding']);

    if ( !empty( $panel_data['style']['bottom_margin'] ) )
      $style[] = 'margin-bottom: '.esc_attr($panel_data['style']['bottom_margin']);

    $style_attr = !empty( $style ) ? ' style="'.join( '; ', $style ).'"' : '';

    $container = '';

    if ( ! empty( $panel_data['style']['container'] ) && $panel_data['style']['container'] != 'none' ) {
      $container_class = ( $panel_data['style']['container'] == 'fluid' ) ? 'container-fluid' : 'container';
      $container = '<div class="'.esc_attr($container_class).'">';
    }

    $content .= '<section id="'.sanitize_title( $section_id ).'" class="row-section'.esc_attr( $classes ).'"'.$style_attr.$parallax.'>'.$container;
    return $content;
  }

  /**
	 * Add extra HTML after row content.
	 *
	 * @since  1.0.0
	 * @return string
	 */
  public function after_row( $content, $panel_data, $grid_attrs ) {
    $GLOBALS['ct_so_grid']++;
    if ( ! empty( $panel_data['style']['container'] ) && $panel_data['style']['container'] != 'none' ) {
      $content .= '</div>';
    }
    $content .= '</section>';
    return $content;
  }

  /**
	 * Row grid class.
	 *
	 * @since  1.0.0
	 * @return mixed
	 */
  public function row_classes( $classes, $row_data ) {
    return array( 'row' );
  }

  /**
	 * Row grid HTML Attributes.
	 *
	 * @since  1.0.0
	 * @return mixed
	 */
  public function row_attributes( $attrs, $row_data ) {

    if ( isset( $attrs['id'] ) )
      unset( $attrs['id'] );

    return $attrs;
  }

  /**
	 * Row grid HTML Attributes.
	 *
	 * @since  1.0.0
	 * @return mixed
	 */
  public function row_cell_classes( $classes, $row_data ) {

    global $ct_so_grid,$ct_so_cell,$ct_so_cell_class;

    $classes = $grid_temp = array();
    foreach ( $row_data['grid_cells'] as $cell => $data ) {
      if ( $data['grid'] === $ct_so_grid ) {
        $grid_temp[] = $data;
      }
    }
    
    $class_ratio = !empty( $grid_temp[ $ct_so_cell ]['weight'] ) ? floatval($grid_temp[ $ct_so_cell ]['weight']) : 1;
    $class_views = !empty( $row_data['grids'][ $ct_so_grid ]['style']['breakpoint'] ) ? esc_attr( $row_data['grids'][ $ct_so_grid ]['style']['breakpoint'] ) : 'md';
    $classes[] = 'col-' . $class_views . '-' . round( $class_ratio * 12 );

    if ( !empty($ct_so_cell_class) ) {
      $classes[] = $ct_so_cell_class;
    }

    return $classes;
  }

  /**
	 * Row cell css class.
	 *
	 * @since  1.0.0
	 * @return mixed
	 */
  public function row_cell_atrs( $attrs, $panel_data ) {

    $GLOBALS['ct_so_cell']++;

    if ( isset( $attrs['id'] ) )
      unset( $attrs['id'] );

    return $attrs;
  }
  
  /**
	 * Widget wrapper css class.
    *
	 * @since  1.0.0
	 * @return mixed
	 */
  public function widget_classes( $classes, $widget, $instance ) {
    return array_diff( $classes, array( 'so-panel' ) );
  }

}
