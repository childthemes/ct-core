<?php
/**
 * CF7BS_Form_Field class
 *
 * @package CF7BS
 * @author Felix Arntz <felix-arntz@leaves-and-love.net>
 * @contributor Rizal Fauzie <fauzie@childthemes.net>
 * @since 1.0.0
 */

class CF7BS_Form_Field extends CF7BS_Component {

	public function display( $echo = false ) {

    $output = '';

    extract( $this->args );

    $type = $this->validate_type( $type );

    $value = $this->validate_value( $value, $type, $options );

    $id = !empty( $id ) ? sanitize_title( $id ) : sanitize_title( $name );

    if ( is_numeric($tabindex) ) {
      $tabindex = intval($tabindex);
    }

    if ( 'hidden' != $type ) {
      if ( ! empty( $label_class ) ) {
        $label_class .= ' ';
      }

      $input_div_class = '';
      $input_class = $class;
      if ( 'horizontal' == $form_layout ) {
        $label_class .= ' control-label';
        $classes = $this->get_column_width_classes( $form_label_width, $form_breakpoint, $grid_columns );
        $label_class .= ' ' . $classes['label'];
        $input_div_class = $classes['input'];
        if ( empty( $label ) ) {
          $input_div_class .= ' ' . $this->get_column_offset_class( $form_label_width, $form_breakpoint, $grid_columns );
        }
      } elseif( 'inline' == $form_layout ) {
        $label_class .= ' sr-only';
        if ( empty( $placeholder ) ) {
          $placeholder = $label;
        }
      } else {
        $label_class .= ' form-control-label';
      }

      if ( ! empty( $wrapper_class ) ) {
        $wrapper_class = ' ' . esc_attr( $wrapper_class );
      }

      $field_column = !empty( $grid_columns ) ? absint( $grid_columns ) : 12;
      $field_breakp = !empty( $form_breakpoint ) ? esc_attr( $form_breakpoint ) : 'md';
      $wrapper_class .= ' col-'.$field_breakp.'-'.$field_column;

      if ( ! empty( $input_class ) ) {
        $input_class .= ' ';
      }
      if ( ! in_array( $type, array( 'radio', 'checkbox' ) ) ) {
        if ( ! in_array( $type, array( 'file', 'range' ) ) ) {
          $input_class .= 'form-control';
        } elseif ( $type == 'file' ) {
          $input_class .= ' custom-file-input';
        }

        if ( is_int( $tabindex ) ) {
          $tabindex = ' tabindex="' . $tabindex . '"';
        } else {
          $tabindex = '';
        }
      } elseif ( in_array( $type, array( 'radio', 'checkbox', 'acceptance' ) ) ) {
        $input_class .= 'custom-control-input';
      }

      if ( 'large' == $size ) {
        $input_class .= ' form-control-lg';
      } elseif ( in_array( $size, array( 'small', 'mini' ) ) ) {
        $input_class .= ' form-control-sm';
      }

      if ( in_array( $status, array( 'success', 'warning', 'danger' ) ) ) {
        $input_class .= ' form-control-' . $status;
      }

      if ( ! empty( $input_class ) ) {
        $input_class = ' class="' . esc_attr( $input_class ) . '"';
      }
      if ( ! empty( $placeholder ) ) {
        $placeholder = ' placeholder="' . esc_attr( $placeholder ) . '"';
      }

      if ( $readonly ) {
        $readonly = ' readonly';
      } else {
        $readonly = '';
      }

      if ( $minlength && $minlength > 0 ) {
        $minlength = ' minlength="' . absint( $minlength ) . '"';
      } else {
        $minlength = '';
      }

      if ( $maxlength && $maxlength > -1 ) {
        $maxlength = ' maxlength="' . absint( $maxlength ) . '"';
      } else {
        $maxlength = '';
      }

      $append = '';

      if ( in_array( $status, array( 'success', 'warning', 'danger' ) ) ) {
        $status = ' has-' . $status;
      } else {
        $status = '';
      }

      if ( 'has-danger' == $status ) {
        $append .= ' aria-invalid="true"';
      } else {
        $append .= ' aria-invalid="false"';
      }

      $label_required = '';
      if ( 'required' == $mode ) {
        $append .= ' aria-required="true" required';
        $label_required = ' ' . cf7bs_get_form_property( 'required_html' );
      } elseif( 'disabled' == $mode ) {
        $append .= ' disabled';
      }

      if ( 'none' != $form_layout ) {
        if ( 'horizontal' == $form_layout ) {
          $output .= '<div class="form-group' . $wrapper_class . $status . '">';
          if ( ! empty( $label ) ) {
            $output .= '<label class="' . esc_attr( $label_class ) . '"' . ( ! empty( $id ) ? ' for="' . esc_attr( $id ) . '"' : '' ) . '>' . esc_html( $label ) . $label_required . '</label>';
          }
          $output .= '<div class="' . esc_attr( $input_div_class ) . '">';
        } elseif( 'inline' == $form_layout ) {
          $output .= '<div class="form-group' . $wrapper_class . $status . '">';
          if ( ! empty( $label ) ) {
            $output .= '<label class="' . esc_attr( $label_class ) . '"' . ( ! empty( $id ) ? ' for="' . esc_attr( $id ) . '"' : '' ) . '>' . esc_html( $label ) . $label_required . '</label>';
          }
        } else {
          $output .= '<div class="form-group' . $wrapper_class . $status . '">';
          if ( ! empty( $label ) ) {
            $rc_group_style = '';
            if ( in_array( $type, array( 'radio', 'checkbox' ) ) ) {
              $rc_group_style = ' style="display:block;"';
            }
            $output .= '<label class="' . esc_attr( $label_class ) . '"' . ( ! empty( $id ) ? ' for="' . esc_attr( $id ) . '"' : '' ) . $rc_group_style . '>' . esc_html( $label ) . $label_required . '</label>';
          }
        }
      }
    }

    switch ( $type ) {
      case 'checkbox':
        if ( count( $options ) <= 1 ) {
          $curval = key( $options );
          $title = $options[ $curval ];
          if ( false === strpos( $title, 'wpcf7-free-text' ) ) {
            $title = esc_html( $title );
          }

          $output .= '<label class="custom-control custom-checkbox"  for="' . esc_attr( $id ) . '">';
          $output .= '<input' . $input_class . ' id="' . esc_attr( $id ) . '" name="' . esc_attr( $name . '[]' ) . '" type="checkbox" value="' . esc_attr( $curval ) . '"' . cf7bs_multiple_checked( $value, $curval, false ) . $tabindex . $append . '>';
          $output .= '<span class="custom-control-indicator"></span>';
          $output .= '<span class="custom-control-description">' . $title . '</span>';
          $output .= '</label>';
        } else {
          if ( 'buttons' == $group_layout ) {
            $button_group = new CF7BS_Button_Group( array(
              'mode'		=> 'checkbox',
              'size'		=> $size,
            ) );
            $output .= $button_group->open( false );
            $counter = 0;
            foreach ( $options as $curval => $title ) {
              $is_checked = cf7bs_multiple_checked( $value, $curval, false );
              $output .= $button_group->insert_button( array(
                'type'		=> $group_type,
                'id'		=> ! empty( $id ) ? $id . ( $counter + 1 ) : '',
                'name'		=> $name . '[]',
                'class'		=> $class,
                'value'		=> $curval,
                'title'		=> $title,
                'append'	=> ( is_int( $tabindex ) ? ' tabindex="' . ( $tabindex + $counter ) . '"' : '' ) . $is_checked . $append,
              ), false );
              $counter++;
            }
            $output .= $button_group->close( false );
          } elseif ( 'inline' == $group_layout && 'inline' != $form_layout ) {
            $counter = 0;
            foreach ( $options as $curval => $title ) {
              if ( false === strpos( $title, 'wpcf7-free-text' ) ) {
                $title = esc_html( $title );
              }
              $output .= '<label class="custom-control custom-checkbox" ' . ( ! empty( $id ) ? ' for="' . esc_attr( $id . ( $counter + 1 ) ) . '"' : '' ) .'>';
              $output .= '<input' . $input_class . ' id="' . esc_attr( $id . ( $counter + 1 ) ) . '" name="' . esc_attr( $name . '[]' ) . '" type="checkbox" value="' . esc_attr( $curval ) . '"' . cf7bs_multiple_checked( $value, $curval, false ) . ( $tabindex >= 0 ? ' tabindex="' . ( $tabindex + $counter ) . '"' : '' ) . $append . '>';
              $output .= '<span class="custom-control-indicator"></span>';
              $output .= '<span class="custom-control-description">' . $title . '</span>';
              $output .= '</label>';
              $counter++;
            }
          } else {
            $counter = 0;
            foreach ( $options as $curval => $title ) {
              if ( false === strpos( $title, 'wpcf7-free-text' ) ) {
                $title = esc_html( $title );
              }
              $output .= '<label class="custom-control custom-checkbox"  for="' . esc_attr( $id . ( $counter + 1 ) ) . '">';
              $output .= '<input' . $input_class . ' id="' . esc_attr( $id . ( $counter + 1 ) ) . '" name="' . esc_attr( $name . '[]' ) . '" type="checkbox" value="' . esc_attr( $curval ) . '"' . cf7bs_multiple_checked( $value, $curval, false ) . ( is_int( $tabindex ) ? ' tabindex="' . ( $tabindex + $counter ) . '"' : '' ) . $append . '>';
              $output .= '<span class="custom-control-indicator"></span>';
              $output .= '<span class="custom-control-description">' . $title . '</span>';
              $output .= '</label>';
              $counter++;
            }
          }
        }
        break;
      case 'select':
        $output .= '<select' . $input_class . ( ! empty( $id ) ? ' id="' . esc_attr( $id ) . '"' : '' ) . ' name="' . esc_attr( $name ) . '"' . $tabindex . $append . '>';
        foreach ( $options as $curval => $title ) {
          $output .= '<option value="' . esc_attr( $curval ) . '"' . cf7bs_selected( $value, $curval, false ) . '>' . esc_html( $title ) . '</option>';
        }
        $output .= '</select>';
        break;
      case 'multiselect':
        $output .= '<select' . $input_class . ( ! empty( $id ) ? ' id="' . esc_attr( $id ) . '"' : '' ) . ' name="' . esc_attr( $name . '[]' ) . '" multiple' . $tabindex . $append . '>';
        foreach ( $options as $curval => $title ) {
          $output .= '<option value="' . esc_attr( $curval ) . '"' . cf7bs_multiple_selected( $value, $curval, false ) . '>' . esc_html( $title ) . '</option>';
        }
        $output .= '</select>';
        break;
      case 'radio':
        if ( count( $options ) <= 1 ) {
          $curval = key( $options );
          $title = $options[ $curval ];
          if ( false === strpos( $title, 'wpcf7-free-text' ) ) {
            $title = esc_html( $title );
          }
          $output .= '<label class="custom-control custom-radio" for="' . esc_attr( $id ) . '">';
          $output .= '<input' . $input_class . ' id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" type="radio" value="' . esc_attr( $curval ) . '"' . cf7bs_checked( $value, $curval, false ) . ( is_int( $tabindex ) ? ' tabindex="' . $tabindex . '"' : '' ) . $append . '>';
          $output .= '<span class="custom-control-indicator"></span>';
          $output .= '<span class="custom-control-description">' . $title . '</span>';
          $output .= '</label>';
        } else {
          if ( 'buttons' == $group_layout ) {
            $button_group = new CF7BS_Button_Group( array(
              'mode'		=> 'radio',
              'size'		=> $size,
            ) );
            $output .= $button_group->open( false );
            $counter = 0;
            foreach ( $options as $curval => $title ) {
              $is_checked = cf7bs_checked( $value, $curval, false );
              $output .= $button_group->insert_button( array(
                'type'		=> $group_type,
                'id'		=> ! empty( $id ) ? $id . ( $counter + 1 ) : '',
                'name'		=> $name,
                'class'		=> $class,
                'value'		=> $curval,
                'title'		=> $title,
                'append'	=> ( is_int( $tabindex ) ? ' tabindex="' . ( $tabindex + $counter ) . '"' : '' ) . $is_checked . $append,
              ), false );
              $counter++;
            }
            $output .= $button_group->close( false );
          } elseif( 'inline' == $group_layout && 'inline' != $form_layout ) {
            $counter = 0;
            foreach ( $options as $curval => $title ) {
              if ( false === strpos( $title, 'wpcf7-free-text' ) ) {
                $title = esc_html( $title );
              }
              $output .= '<label class="custom-control custom-radio" for="' . esc_attr( $id . ( $counter + 1 ) ) . '">';
              $output .= '<input' . $input_class . ' id="' . esc_attr( $id . ( $counter + 1 ) ) . '" name="' . esc_attr( $name ) . '" type="radio" value="' . esc_attr( $curval ) . '"' . cf7bs_checked( $value, $curval, false ) . ( $tabindex >= 0 ? ' tabindex="' . ( $tabindex + $counter ) . '"' : '' ) . $append . '>';
              $output .= '<span class="custom-control-indicator"></span>';
              $output .= '<span class="custom-control-description">' . $title . '</span>';
              $output .= '</label>';
              $counter++;
            }
          } else {
            $counter = 0;
            foreach ( $options as $curval => $title ) {
              if ( false === strpos( $title, 'wpcf7-free-text' ) ) {
                $title = esc_html( $title );
              }
              $output .= '<label class="custom-control custom-radio" for="' . esc_attr( $id . ( $counter + 1 ) ) . '">';
              $output .= '<input' . $input_class . ' id="' . esc_attr( $id . ( $counter + 1 ) ) . '" name="' . esc_attr( $name ) . '" type="radio" value="' . esc_attr( $curval ) . '"' . cf7bs_checked( $value, $curval, false ) . ( is_int( $tabindex ) ? ' tabindex="' . ( $tabindex + $counter ) . '"' : '' ) . $append . '>';
              $output .= '<span class="custom-control-indicator"></span>';
              $output .= '<span class="custom-control-description">' . $title . '</span>';
              $output .= '</label>';
              $counter++;
            }
          }
        }
        break;
      case 'textarea':
        $output .= '<textarea' . $input_class . ( ! empty( $id ) ? ' id="' . esc_attr( $id ) . '"' : '' ) . ' name="' . esc_attr( $name ) . '" rows="' . absint( $rows ) . '"' . $placeholder . $readonly . $minlength . $maxlength . $tabindex . $append . '>';
        $output .= esc_textarea( $value );
        $output .= '</textarea>';
        break;
      case 'file':
        $output .= '<label class="custom-file" for="' . esc_attr( $id ) . '">';
        $output .= '<input' . $input_class . ' id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" type="file"' . $tabindex . $append . '>';
        $output .= '<span class="custom-file-control"></span>';
        $output .= '</label>';
        break;
      case 'hidden':
        $output .= '<input' . ( ! empty( $id ) ? ' id="' . esc_attr( $id ) . '"' : '' ) . ' name="' . esc_attr( $name ) . '" type="hidden" value="' . esc_attr( $value ) . '">';
        break;
      case 'range':
        $value = !empty( $value ) ? $value : ( isset( $options['min'] ) ? absint( $options['min'] ) : 0 );
        $min = isset( $options['min'] ) ? ' data-slider-min="' . absint( $options['min'] ) . '"' : '';
        $max = isset( $options['max'] ) ? ' data-slider-max="' . absint( $options['max'] ) . '"' : '';
        $step = !empty( $options['step'] ) ? ' data-slider-step="' . absint( $options['step'] ) . '"' : ' data-slider-step="1"';
        $groupclass = in_array( $size, array( 'mini', 'small' ) ) ? 'input-group-sm' : ( $size == 'large' ? 'input-group-lg' : '' );
        $append .= ' data-slider-labelledby="range-addon-' . esc_attr( $id ) . '"';
        $append .= ' aria-describedby="range-addon-' . esc_attr( $id ) . '"';

        $output .= '<div class="input-group range-slider '.$groupclass.'">';
        $output .= '<span class="input-group-addon" id="range-addon-' . esc_attr( $id ) . '">'.$value.'</span>';
        $output .= '<div class="form-control"><input' . $input_class . ' id="' . esc_attr( $id ) . '"' . ' name="' . esc_attr( $name ) . '" type="text" value="' . esc_attr( $value ) . '" data-provide="slider" data-slider-value="' . esc_attr( $value ) . '"' . $min . $max . $step . $readonly . $tabindex . $append . '></div>';
        $output .= '</div>';
        break;
      case 'number':
        $min = '';
        if ( isset( $options['min'] ) ) {
          $min = ' min="' . esc_attr( $options['min'] ) . '"';
        }
        $max = '';
        if ( isset( $options['max'] ) ) {
          $max = ' max="' . esc_attr( $options['max'] ) . '"';
        }
        $step = '';
        if ( isset( $options['step'] ) ) {
          $step = ' step="' . esc_attr( $options['step'] ) . '"';
        }
        $output .= '<input' . $input_class . ( ! empty( $id ) ? ' id="' . esc_attr( $id ) . '"' : '' ) . ' name="' . esc_attr( $name ) . '" type="' . esc_attr( $type ) . '" value="' . esc_attr( $value ) . '"' . $placeholder . $min . $max . $step . $readonly . $tabindex . $append . '>';
        break;
      case 'date':
      case 'datetime':
      case 'datetime-local':
      case 'month':
      case 'time':
      case 'week':
        if ( empty( $placeholder ) ) {
          $placeholder = ' placeholder="mm/dd/yyyy"';
        }
        $min = !empty( $options['min'] ) ? esc_attr( $options['min'] ) : '01/01/1900';
        $max = !empty( $options['max'] ) ? esc_attr( $options['max'] ) : '12/31/2030';
        ctcore_log( $options );
        $append .= ' data-provide="datepicker"';
        $append .= ' data-date-today-btn="linked"';
        $append .= ' data-date-format="mm/dd/yyyy"';
        $append .= ' data-date-start-date="' . date( 'm/d/Y', strtotime( $min ) ) . '"';
        $append .= ' data-date-end-date="' . date( 'm/d/Y', strtotime( $max ) ) . '"';

        $output .= '<input' . $input_class . ' id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" type="text" value="' . esc_attr( $value ) . '"' . $placeholder . $readonly . $tabindex . $append . '>';
        break;
      case 'custom':
        if ( ! empty( $name ) ) {
          $output .= $name;
        }
        break;
      default:
        if ( 'static' == $mode ) {
          $output .= '<p class="form-control-static">' . esc_html( $value ) . '</p>';
        } else {
          if ( ! empty( $input_before ) || ! empty( $input_after ) ) {
            $input_group_class = 'input-group';
            if ( false !== strpos( $input_class, ' input-lg') ) {
              $input_class = str_replace( ' input-lg', '', $input_class );
              $input_group_class .= ' input-group-lg';
            } elseif ( false !== strpos( $input_class, ' input-sm') ) {
              $input_class = str_replace( ' input-sm', '', $input_class );
              $input_group_class .= ' input-group-sm';
            }
            $output .= '<div class="' . $input_group_class . '">';
            if ( ! empty( $input_before ) ) {
              $output .= '<span class="' . esc_attr( $input_before_class ) . '">';
              $output .= $input_before;
              $output .= '</span>';
            }
          }

          $output .= '<input' . $input_class . ( ! empty( $id ) ? ' id="' . esc_attr( $id ) . '"' : '' ) . ' name="' . esc_attr( $name ) . '" type="' . esc_attr( $type ) . '" value="' . esc_attr( $value ) . '"' . $placeholder . $readonly . $minlength . $maxlength . $tabindex . $append . '>';

          if ( ! empty( $input_before ) || ! empty( $input_after ) ) {
            if ( ! empty( $input_after ) ) {
              $output .= '<span class="' . esc_attr( $input_after_class ) . '">';
              $output .= $input_after;
              $output .= '</span>';
            }
            $output .= '</div>';
          }
        }
        break;
    }

    if ( 'hidden' != $type && 'none' != $form_layout ) {
      if ( ! empty( $help_text ) && 'inline' != $form_layout ) {
        $output .= '<span class="help-block">' . $help_text . '</span>';
      }

      if ( 'horizontal' == $form_layout ) {
        $output .= '</div>';
        $output .= '</div>';
      } else {
        $output .= '</div>';
      }
    }

    wp_enqueue_style( 'ctcore-cf7bs-style' );
    wp_enqueue_script( 'ctcore-cf7bs-script' );

		if ( $echo ) {
			echo $output;
		}
		return $output;
	}

	protected function validate_args( $args, $exclude = array() ) {
		$exclude[] = 'value';
		$exclude[] = 'maxlength';
		$exclude[] = 'tabindex';
		$args = parent::validate_args( $args, $exclude );

		// type whitelist check is made later in the display() function to allow different types to use in a filter

		return $args;
	}

	protected function get_defaults() {
		$defaults = array(
			'name'					=> '',
			'id'					=> '',
			'class'					=> '',
			'type'					=> 'text',
			'value'					=> '', // for multiselect and multiple checkbox an array, for singular checkboxes and all others a string
			'placeholder'			=> '',
			'label'					=> '',
			'options'				=> array(), // for select, multiselect, checkbox and radio: value => title; for number, range and all datetime-related fields: min, max, step
			'rows'					=> 4,
			'help_text'				=> '',
			'size'					=> 'default', // default, large, small, mini
			'grid_columns'			=> 12,
			'form_layout'			=> 'default', // default, inline, horizontal, none
			'form_label_width'		=> 2,
			'form_breakpoint'		=> 'xs',
			'mode'					=> 'default', // default, required, static, disabled
			'status'				=> 'default', // default, success, warning, error
			'readonly'				=> false,
			'minlength'				=> false,
			'maxlength'				=> false,
			'tabindex'				=> false,
			'group_layout'			=> 'default', // default, inline, buttons
			'group_type'			=> 'default', // only if group_layout==buttons
			'wrapper_class'			=> '',
			'label_class'           => '',
			'input_before'			=> '',
			'input_after'			=> '',
			'input_before_class'	=> 'input-group-addon',
			'input_after_class'		=> 'input-group-addon',
		);
		return apply_filters( 'cf7bs_bootstrap_form_field_defaults', $defaults );
	}

	private function validate_type( $type ) {
		$whitelist = array(
			'text',
			'password',
			'datetime',
			'datetime-local',
			'date',
			'month',
			'time',
			'week',
			'number',
			'range',
			'email',
			'url',
			'search',
			'tel',
			'color',
			'textarea',
			'file',
			'hidden',
			'select',
			'multiselect',
			'checkbox',
			'radio',
			'custom',
		);

		$type = strtolower( $type );
		if ( ! in_array( $type, $whitelist ) ) {
			$type = 'text';
		}
		return $type;
	}

	private function validate_value( $value, $type, $options = array() ) {
		if ( 'multiselect' == $type || 'checkbox' == $type && is_array( $options ) && count( $options ) > 1 ) {
			$value = (array) $value;
		} else {
			if ( is_array( $value ) ) {
				if ( count( $value ) > 0 ) {
					reset( $value );
					$value = $value[ key( $value ) ];
				} else {
					$value = '';
				}
			}
			$value = (string) $value;
		}
		return $value;
	}

	private function get_column_width_classes( $label_column_width = 2, $breakpoint = 'sm', $grid_columns = 12 ) {
		if ( $label_column_width > $grid_columns - 1 || $label_column_width < 1 ) {
			$label_column_width = 2;
		}
		if ( ! in_array( $breakpoint, array( 'xs', 'sm', 'md', 'lg' ) ) ) {
			$breakpoint = 'sm';
		}
		return array(
			'label'		=> 'col-' . $breakpoint . '-' . $label_column_width,
			'input'		=> 'col-' . $breakpoint . '-' . ( $grid_columns - $label_column_width ),
		);
	}

	private function get_column_offset_class( $label_column_width = 2, $breakpoint = 'sm', $grid_columns = 12 ) {
		if ( $label_column_width > $grid_columns - 1 || $label_column_width < 1 ) {
			$label_column_width = 2;
		}
		if ( ! in_array( $breakpoint, array( 'xs', 'sm', 'md', 'lg' ) ) ) {
			$breakpoint = 'sm';
		}
		return 'col-' . $breakpoint . '-offset-' . $label_column_width;
	}
}
