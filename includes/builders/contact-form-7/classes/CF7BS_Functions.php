<?php
/**
 * Utility functions
 *
 * @package CF7BS
 * @author Felix Arntz <felix-arntz@leaves-and-love.net>
 * @since 1.0.0
 */

$current_form_id = 0;
$current_form_properties = array();

function cf7bs_get_form_property( $property, $form_id = 0, $field_tag = null ) {
	global $current_form_id, $current_form_properties;

	// Allow overriding some form properties by individual fields.
	if ( $field_tag ) {
		if ( in_array( $property, array_keys( cf7bs_get_default_form_properties( true ) ) ) ) {
			$ret = $field_tag->get_option( $property, '[-0-9a-zA-Z_]+', true );
			if ( $ret ) {
				// special case: skip the `$size` property if it is numeric (default CF7 way)
				if ( 'size' != $property || ! is_numeric( $ret ) ) {
					return $ret;
				}
			}
		}
	}

	$current_form = $original_form = null;

	if ( ! $form_id ) {
		$form_id = cf7bs_get_current_form_id();
		if ( ! $form_id ) {
			return false;
		}
		$current_form = WPCF7_ContactForm::get_current();
	} else {
		$current_form = WPCF7_ContactForm::get_instance( $form_id );
		$original_form = WPCF7_ContactForm::get_current();
		if ( is_a( $current_form, 'WPCF7_ContactForm' ) && is_callable( array( $current_form, 'id' ) ) && is_a( $original_form, 'WPCF7_ContactForm' ) && is_callable( array( $original_form, 'id' ) ) ) {
			if ( $original_form->id() === $current_form->id() ) {
				$original_form = null;
			}
		}
	}

	if ( $current_form_id != $form_id ) {
		$current_form_id = $form_id;

		$properties = cf7bs_get_default_form_properties();
		if ( is_a( $current_form, 'WPCF7_ContactForm' ) && is_callable( array( $current_form, 'additional_setting' ) ) ) {
			foreach ( $properties as $key => &$value ) {
				$setting = $current_form->additional_setting( $key );
				if ( isset( $setting[0] ) ) {
					$value = $setting[0];
				}
			}
			unset( $key );
			unset( $value );
		}
		$current_form_properties = apply_filters( 'cf7bs_form_' . $form_id . '_properties', $properties );
	}

	if ( null !== $original_form ) {
		if ( is_a( $original_form, 'WPCF7_ContactForm' ) && is_callable( array( $original_form, 'id' ) ) ) {
			WPCF7_ContactForm::get_instance( $original_form->id() );
		}
	}

	if ( isset( $current_form_properties[ $property ] ) ) {
		return $current_form_properties[ $property ];
	}
	return false;
}

function cf7bs_get_default_form_properties( $only_overrideables = false ) {
	$properties = array(
		'layout'		=> 'default', // 'default', 'inline', 'horizontal', 'none'
		'size'			=> 'default', // 'default', 'small', 'large'
		'group_layout'	=> 'default', // 'default', 'inline', 'buttons'
		'group_type'	=> 'default', // 'default', 'primary', 'success', 'info', 'warning', 'danger' (only if group_layout=buttons)
		'grid_columns'	=> 12,
		'label_width'	=> 3, // integer between 1 and 'grid_columns' minus 1
		'breakpoint'	=> 'sm', // xs, sm, md, lg
	);

	if ( ! $only_overrideables ) {
		$properties = array_merge( $properties, array(
			'submit_size'	=> '', // 'default', 'small', 'large' or leave empty to use value of 'size'
			'submit_type'	=> 'primary', // 'default', 'primary', 'success', 'info', 'warning', 'danger'
			'required_html'	=> '<span class="required">*</span>',
		) );
	}

	return apply_filters( 'cf7bs_default_form_properties', $properties, $only_overrideables );
}

function cf7bs_add_get_parameter() {
	$args = func_get_args();
	if ( is_array( $args[0] ) ) {
		if ( count( $args ) < 2 || $args[1] === false ) {
			$uri = $_SERVER['REQUEST_URI'];
		} else {
			$uri = $args[1];
		}
		foreach ( $args[0] as $key => &$value ) {
			$value = cf7bs_parameter_encode( $value );
		}
		return add_query_arg( $args[0], $uri );
	} else {
		if ( count( $args ) < 3 || $args[2] === false ) {
			$uri = $_SERVER['REQUEST_URI'];
		} else {
			$uri = $args[2];
		}
		if ( count( $args ) < 2 ) {
			return $uri;
		}
		return add_query_arg( $args[0], cf7bs_parameter_encode( $args[1] ), $uri );
	}
	return '';
}

function cf7bs_get_current_form_id() {
	if ( is_callable( array( 'WPCF7_ContactForm', 'get_current' ) ) ) {
		$current_form = WPCF7_ContactForm::get_current();
		if ( is_a( $current_form, 'WPCF7_ContactForm' ) && is_callable( array( $current_form, 'id' ) ) ) {
			return $current_form->id();
		}
	}

	return false;
}

function cf7bs_selected( $selected, $current = true, $echo = true ) {
	$result = '';

	if ( $selected == $current ) {
		$result = ' selected';
		if ( ! wpcf7_support_html5() ) {
			$result .= '="selected"';
		}
	}

	if ( $echo ) {
		echo $result;
	}

	return $result;
}

function cf7bs_multiple_selected( $selected, $current = true, $echo = true ) {
	$result = '';

	if ( is_array( $selected ) ) {
		if ( in_array( $current, $selected ) ) {
			$result = ' selected';
			if ( ! wpcf7_support_html5() ) {
				$result .= '="selected"';
			}
		}
	}

	if ( $echo ) {
		echo $result;
	}

	return $result;
}

function cf7bs_checked( $checked, $current = true, $echo = true ) {
	$result = '';

	if ( $checked == $current ) {
		$result = ' checked';
		if ( ! wpcf7_support_html5() ) {
			$result .= '="checked"';
		}
	}

	if ( $echo ) {
		echo $result;
	}

	return $result;
}

function cf7bs_multiple_checked( $checked, $current = true, $echo = true ) {
	$result = '';

	if ( is_array( $checked ) ) {
		if ( in_array( $current, $checked ) ) {
			$result = ' checked';
			if ( ! wpcf7_support_html5() ) {
				$result .= '="checked"';
			}
		}
	}

	if ( $echo ) {
		echo $result;
	}

	return $result;
}

function cf7bs_form_class_attr( $class = '' ) {

	$layout = cf7bs_get_form_property( 'layout' );

	if ( in_array( $layout, array( 'horizontal', 'inline' ) ) ) {
		if ( ! empty( $class ) ) {
			$class .= ' ';
		}
		$class .= 'form-' . $layout;
	}
  $class .= ' clearfix';

	return $class;
}
add_filter( 'wpcf7_form_class_attr', 'cf7bs_form_class_attr' );

function cf7bs_form_novalidate( $novalidate ) {
	if ( wpcf7_support_html5() ) {
		return ' novalidate';
	}
	return '';
}
add_filter( 'wpcf7_form_novalidate', 'cf7bs_form_novalidate' );

function cf7bs_form_response_output( $output, $class, $content, $form_obj ) {
	$type = 'warning';

	if ( false !== strpos( $class, 'wpcf7-display-none' ) ) {
		$type = '';
	} else {
		if ( false !== strpos( $class, 'wpcf7-mail-sent-ng' ) ) {
			$type = 'danger';
		} elseif ( false !== strpos( $class, 'wpcf7-mail-sent-ok' ) ) {
			$type = 'success';
		} else {
			$type = 'warning';
		}
	}

	$alert = new CF7BS_Alert( array(
		'type'			=> $type,
		'class'			=> $class,
		'dismissible'	=> false,
	) );

	return $alert->open( false ) . esc_html( $content ) . $alert->close( false );
}
add_filter( 'wpcf7_form_response_output', 'cf7bs_form_response_output', 10, 4 );

function cf7bs_validation_error( $output, $name, $form_obj ) {
	$alert = new CF7BS_Alert( array(
		'type'			=> 'warning',
		'class'			=> 'wpcf7-not-valid-tip',
		'dismissible'	=> true,
	) );
	$output = str_replace( '<span role="alert" class="wpcf7-not-valid-tip">', $alert->open( false ), $output );
	$output = str_replace( '</span>', $alert->close( false ), $output );
	return $output;
}
add_filter( 'wpcf7_validation_error', 'cf7bs_validation_error', 10, 3 );

function cf7bs_ajax_json_echo( $items, $result ) {
	if ( isset( $items['invalids'] ) ) {
		foreach ( $items['invalids'] as &$invalid ) {
			$invalid['into'] = str_replace( 'span.wpcf7-form-control-wrap', 'div.form-group', $invalid['into'] );
		}
	}
	return $items;
}
add_filter( 'wpcf7_ajax_json_echo', 'cf7bs_ajax_json_echo', 10, 2 );

function cf7bs_default_template( $template, $prop = 'form' ) {
	if ( 'form' == $prop ) {
		$template = cf7bs_default_form_template();
	}
	return $template;
}
add_filter( 'wpcf7_default_template', 'cf7bs_default_template', 10, 2 );

function cf7bs_default_form_template() {
	return '<div class="row">' . "\n"
    . '[text* your-name grid_columns:4]Your Name[/text*]' . "\n"
		. '[email* your-email grid_columns:4]Your Email[/email*]' . "\n"
		. '[text your-subject grid_columns:4]Subject[/text]' . "\n"
    . '</div>' . "\n"
    . '<div class="row">' . "\n"
		. '[textarea your-message grid_columns:12]Your Message[/textarea]' . "\n"
		. '[submit form-submit align:center grid_columns:12 icon:fa icon:fa-check "Send"]' . "\n"
    . '</div>' . "\n";
}

function cf7bs_parameter_encode( $item ) {
	$encoded = '';
	if ( is_object( $item ) ) {
		return '';
	} elseif ( is_array( $item ) ) {
		$encoded = cf7bs_array_encode( $item );
	} else {
		$encoded = $item;
	}
	return rawurlencode( $encoded );
}

function cf7bs_array_encode( $values ) {
	if ( ! is_array( $values ) ) {
		return '';
	}
	$encoded = '';
	foreach ( $values as $value ) {
		if ( ! empty( $encoded ) ) {
			$encoded .= '---';
		}
		$encoded .= $value;
	}
	return $encoded;
}

function cf7bs_array_decode( $values ) {
	if ( ! is_string( $values ) ) {
		return array();
	}
	$decoded = explode( '---', $values );
	return $decoded;
}

function cf7bs_editor_panel_additional_settings( $post ) {
	if ( ! function_exists( 'wpcf7_editor_panel_additional_settings' ) ) {
		return;
	}

	ob_start();
	wpcf7_editor_panel_additional_settings( $post );
	$output = ob_get_clean();

	$output = str_replace( 'http://contactform7.com/additional-settings/', 'https://wordpress.org/plugins/bootstrap-for-contact-form-7/other_notes/', $output );

	echo $output;
}

function cf7bs_editor_panels( $panels ) {
	if ( ! isset( $panels['additional-settings-panel'] ) ) {
		return $panels;
	}

	$panels['additional-settings-panel']['callback'] = 'cf7bs_editor_panel_additional_settings';

	return $panels;
}
add_filter( 'wpcf7_editor_panels', 'cf7bs_editor_panels' );
