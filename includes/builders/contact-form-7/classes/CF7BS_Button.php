<?php
/**
 * CF7BS_Button class
 *
 * @package CF7BS
 * @author Felix Arntz <felix-arntz@leaves-and-love.net>
 * @contributor Rizal Fauzie <fauzie@childthemes.net>
 * @since 1.0.0
 */

class CF7BS_Button extends CF7BS_Component {
	public function display( $echo = false ) {

    $output = '';

    extract( $this->args );

    $type = $this->validate_type( $type );

    if ( is_numeric($tabindex) ) {
      $tabindex = intval($tabindex);
    }

    if ( ! empty( $class ) ) {
      $class .= ' ';
    }

    if ( $outline ) {
      $class .= 'btn btn-outline-' . $type;
    } else {
      $class .= 'btn btn-' . $type;
    }

    $sizes = array(
      'mini'		=> 'xs',
      'small'		=> 'sm',
      'large'		=> 'lg',
    );
    if ( isset( $sizes[ $size ] ) ) {
      $class .= ' btn-' . $sizes[ $size ];
    }

    $for = '';
    if ( ! empty( $id ) ) {
      $for = ' for="' . esc_attr( $id ) . '"';
      $id = ' id="' . esc_attr( $id ) . '"';
    }

    if ( ! empty( $name ) ) {
      $name = ' name="' . esc_attr( $name ) . '"';
    }

    if ( 'checkbox' == $mode ) {
      if ( false === strpos( $title, 'wpcf7-free-text' ) ) {
        $title = esc_html( $title );
      }
      $output .= '<label' . $for . ' class="' . esc_attr( $class ) . '"><input' . $id . $name . ' type="checkbox" value="' . esc_attr( $value ) . '"' . $append . '>' . $title . '</label>';
    } elseif ( 'radio' == $mode ) {
      if ( false === strpos( $title, 'wpcf7-free-text' ) ) {
        $title = esc_html( $title );
      }
      $output .= '<label' . $for . ' class="' . esc_attr( $class ) . '"><input' . $id . $name . ' type="radio" value="' . esc_attr( $value ) . '"' . $append . '>' . $title . '</label>';
    } else {
      if ( 'none' != $form_layout ) {

        if ( !empty( $icon ) ) {
          $i_class = is_array( $icon ) ? implode( ' ', $icon ) : $icon;
          $icon = '<i class="' . esc_attr( $i_class ) . '"></i>&nbsp;';
        }

        $wrapper_class = array( 'form-submit' );

        if ( $align && 'inline' != $form_layout ) {
          $wrapper_class[] = 'text-xs-' . $align;
        }

        if ( 'horizontal' == $form_layout ) {
          $wrapper_class[] = $this->get_column_class( $form_label_width, $form_breakpoint, $grid_columns );
        } else {
          $field_column = !empty( $grid_columns ) ? absint( $grid_columns ) : 12;
          $field_breakp = !empty( $form_breakpoint ) ? esc_attr( $form_breakpoint ) : 'md';
          $wrapper_class[] = 'col-'.$field_breakp.'-'.$field_column;
        }

        $wrapper_class = implode( ' ', $wrapper_class );

        if ( is_int( $tabindex ) ) {
          $tabindex = ' tabindex="' . $tabindex . '"';
        } else {
          $tabindex = '';
        }
        $output .= '<div class="form-group ' . $wrapper_class . '">';
      }

      $output .= '<button class="' . esc_attr( $class ) . '"' . $id . $name . ' type="submit" value="1"' . $tabindex . '><i class="fa fa-refresh fa-spin fa-fw"></i>' . $icon . esc_attr( $title ) . '</button>';

      if ( 'none' != $form_layout ) {
        $output .= '</div>';
      }
    }

		if ( $echo ) {
			echo $output;
		}
		return $output;
	}

	protected function validate_args( $args, $exclude = array() ) {
		$exclude[] = 'tabindex';
		$exclude[] = 'align';
		$args = parent::validate_args( $args, $exclude );

		if ( is_string( $args['align'] ) ) {
			$args['align'] = strtolower( $args['align'] );
		}

		if ( ! in_array( $args['align'], array( 'left', 'center', 'right' ) ) ) {
			$args['align'] = false;
		}

		// type whitelist check is made later in the display() function to allow different types to use in a filter

		return $args;
	}

	protected function get_defaults() {
		$defaults = array(
			'type'					=> 'default',
			'size'					=> 'default', // default, large, small, mini
			'mode'					=> 'submit', // checkbox, radio, submit
			'id'					=> '',
			'class'					=> '',
			'icon'					=> array(),
			'title'					=> 'Button Title',
			'name'					=> '',
			'append'				=> '', // for checkbox/radio only
			'value'					=> '', // for checkbox/radio only
			'tabindex'				=> false,
			'align'					  => false,
			'outline'					=> false,
			'grid_columns'			=> 12,
			'form_layout'			=> 'default', // default, inline, horizontal, none
			'form_label_width'		=> 2,
			'form_breakpoint'		=> 'sm',
		);
		return $defaults;
	}

	private function validate_type( $type ) {
		$whitelist = array(
			'default',
			'primary',
			'info',
			'success',
			'warning',
			'danger',
			'link',
		);

		$type = strtolower( $type );
		if ( ! in_array( $type, $whitelist ) ) {
			$type = 'default';
		}
		return $type;
	}

	private function get_column_class( $label_column_width = 2, $breakpoint = 'sm', $grid_columns = 12 ) {
		if ( $label_column_width > $grid_columns - 1 || $label_column_width < 1 ) {
			$label_column_width = 2;
		}
		if ( ! in_array( $breakpoint, array( 'xs', 'sm', 'md', 'lg' ) ) ) {
			$breakpoint = 'sm';
		}
		return 'col-' . $breakpoint . '-' . ( $grid_columns - $label_column_width ) . ' col-' . $breakpoint . '-offset-' . $label_column_width;
	}
}
