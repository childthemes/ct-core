<?php
/**
 * WordPress Widget Loader Class
 *
 * @link       http://childthemes.net/
 * @since      1.0.0
 *
 * @package    CT_Core
 * @subpackage CT_Core/includes
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 *  CT_Core_Widget - ChildThemes Core Widget Framework
 *
 * @author  ChildThemes
 * @since 	1.0.0
 */
class CT_Core_Widgets {

  /**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

  /**
   * Instance of this class.
   *
   * @since    1.0.0
   *
   * @var      object
   */
  protected static $instance = null;

  /**
   * Active var of this class.
   *
   * @since    1.0.0
   *
   * @var      object
   */
  protected $active = false;

  /**
	 * Get relative path to load shortcode directory.
	 *
	 * @var string
	 */
	protected $rel_path = 'includes/widgets';

	/**
   * $cache_time
   * transient exiration time
	 *
   * @var int
   */
  public $cache_time = 43200; // 12 hours in seconds

  /**
	 * Holds registered widgets.
	 *
	 * @var array
	 */
	public $widgets = array();

  /**
   * Fire it up
   *
   * @since  1.0.0
   */
  public function __construct() {
    $this->version = CT_VERSION;
		if ( false !== ( $opts = get_option( 'ctcore_features' ) ) && in_array( 'widgets', $opts ) ) {
	    $this->active = true;
	    $this->init();
		}
  }

  /**
   * Hook init.
   *
   * @since     1.0.0
   * @access   private
   */
  private function init() {
    add_action( 'after_setup_theme', array( $this, 'register_widgets' ), 16 );
    add_action( 'widgets_init', array( $this, 'load_widgets' ), -10 );
		add_action( 'admin_footer', array( $this, 'iconpicker_content' ), 20 );
		add_filter( 'widget_display_callback', array( $this, '_cache_widget_output' ), 10, 3 );
		add_filter( 'widget_update_callback', array( $this, '_delete_cache_widget' ), 10, 4 );
  }

  /**
	 * Include all widget files.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
  public function register_widgets() {

    // class prefix is current parent theme folder name
    $class_prefix = get_template();

    // get relative path widgets inside theme
    $rel_path = apply_filters( 'ctcore_widgets_path', $this->rel_path, self::$instance );
    $rel_path = trailingslashit( $rel_path );

    // list all widget class files
    $source_dir = array(
      CT_PATH . $rel_path,
      CT_THEME . $rel_path
    );
    if ( is_child_theme() ) {
      $source_dir[] = CT_CHILD . $rel_path;
    }
    $source_dir = apply_filters( 'ctcore_widgets_sources', $source_dir, self::$instance );

    if ( ! is_array( $source_dir ) || empty( $source_dir ) ) {
      return;
    }
    $source_dir = array_reverse( $source_dir );

    // Add all widget file
    foreach ( $source_dir as $source ) {
      $source = trailingslashit($source);
      $dir = glob($source.'*.php', GLOB_NOSORT);
      if ( ! is_array( $dir ) || empty( $dir ) ) continue;
      foreach ( $dir as $file ) {
        $widget_base  = basename( $file, '.php' );
        $widget_base  = str_replace( 'class-', '', $widget_base );
        $widget_class = file_to_classname( $file, $class_prefix, 'Widget' );
				if( file_exists( $file ) && ! class_exists( $widget_class ) ) {
          include_once( $file );
          if ( ! in_array( $widget_class, $this->widgets ) )
            $this->widgets[ $widget_base ] = $widget_class;
				}
			}
    }

  }

  /**
	 * Register widget area.
	 *
	 * @link     https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
	 * @since    1.0.0
	 * @access   public
	 */
  public function load_widgets() {

    if ( empty( $this->widgets ) || !is_array( $this->widgets ) ) {
      return;
    }

    foreach ($this->widgets as $basename => $widget_class) {
      if ( class_exists( $widget_class ) )
        register_widget( $widget_class );
    }
  }

	/**
   * Add icon picker lightbox content to admin area
   *
   * @since     1.0.0
   */
	public function iconpicker_content() {
    $screen = get_current_screen();
    if ( in_array( $screen->id, array( 'post', 'page', 'widgets', 'customize' ) ) ) {
      include_once CT_INC . 'libraries/ctcore-fontawesome.php';
      include_once CT_INC . 'libraries/ctcore-admin-icon.php';
    }
	}

	/**
	 * get_widget_key
	 *
	 * Simple function to generate a unique id for the widget transient
	 * based on the widget's id_base and instance
	 *
	 * @param  array $b widget id_base
	 * @param  array $i widget instance
	 * @return string md5 hash
	 */
	private function get_widget_key( $b, $i ){
		return 'CT_' . md5( serialize( array( $b, $i ) ) );
	}

	/**
   * _cache_widget_output
	 *
   * @param array     $instance 	The current widget instance's settings.
   * @param WP_Widget $widget     The current widget instance.
   * @param array     $args     	An array of default widget arguments.
	 *
   * @return mixed array|boolean	$instance
   */
  public function _cache_widget_output( $instance, $widget, $args ) {
    if ( false === $instance )
      return $instance;

		//check if wordpress in development mode
		if ( defined( 'WP_DEBUG' ) && false !== WP_DEBUG )
			return $instance;

		//check if widget is inside wp customizer preview
		if ( $widget->is_preview() )
			return $instance;

    //simple timer to clock the widget rendring
    $timer_start = microtime(true);

    //create a uniqe transient ID for this widget instance
    $transient_name = $this->get_widget_key( $widget->id_base, $instance );

    //get the "cached version of the widget"
    if ( false === ( $cached_widget = get_transient( $transient_name ) ) ) {
      // It wasn't there, so render the widget and save it as a transient
      // start a buffer to capture the widget output
      ob_start();
      //this renders the widget
      $widget->widget( $args, $instance );
      //get rendered widget from buffer
      $cached_widget = ob_get_clean();
      //save/cache the widget output as a transient
      set_transient( $transient_name, $cached_widget, $this->cache_time );
    }

    //output the widget
    echo $cached_widget;
    //output rendering time as an html comment
    echo '<!-- Widget cached in '.number_format( microtime(true) - $timer_start, 5 ).' seconds -->';

    //after the widget was rendered and printed we return false to short-circuit the normal display of the widget
    return false;
  }

	/**
   * _delete_cache_widget
	 *
   * @param array     $instance 			The current widget instance's settings.
	 * @param array     $new_instance 	The new widget instance's settings.
	 * @param array     $old_instance 	The old widget instance's settings.
   * @param WP_Widget $widget     		The current widget instance.
	 *
   * @return array		$instance
   */
	public function _delete_cache_widget( $instance, $new_instance, $old_instance, $widget ) {
		$transient_name = $this->get_widget_key( $widget->id_base, $instance );
		// cleanup transient
		delete_transient( $transient_name );
		return $instance;
	}

  /**
   * Return single property of registered widget.
   *
   * @since     1.0.0
   *
   * @return    mixed
   */
  public function get_widget( $widget_id ) {
    if ( array_key_exists( $widget_id, $this->widgets ) ) {
      return $this->widgets[ $widget_id ];
    }
  }

  /**
   * Return an instance of this class.
   *
   * @since     1.0.0
   *
   * @return    object    A single instance of this class.
   */
  public static function get_instance() {

    // If the single instance hasn't been set, set it now.
    if ( null == self::$instance ) {
      self::$instance = new self;
    }

    return self::$instance;
  }

  /**
   * Check if this feature activated.
   *
   * @since     1.0.0
   *
   * @return    boolean
   */
  public function is_active() {
    return $this->active;
  }

}

 /**
  *  CT_Widget - Child Themes Core Widget Factory.
  *
  * @author  ChildThemes
  * @since 	1.0.0
  */
class CT_Widget extends WP_Widget {

	public $widget_description;
	public $widget_id;
	public $widget_name;
  public $widget_class;
  public $panels_groups;
  public $panels_icon;
	public $settings;
	public $control_args;
	public $customizer_support = true;
	public $customize_selective_refresh = true;

	/**
	 * Constructor
	 *
	 * @since		1.0.0
	 */
	public function __construct() {
		$widget_args = array(
			'classname'          => !empty( $this->widget_class ) ? $this->widget_class : $this->widget_id,
			'description'        => !empty( $this->widget_description ) ? $this->widget_description : '',
			'customizer_support' => $this->customizer_support,
			'customize_selective_refresh' => $this->customize_selective_refresh
		);
    
    if ( !empty( $this->panels_groups ) ) {
      $widget_args['panels_groups'] = !is_array( $this->panels_groups ) ? array( esc_attr( $this->panels_groups ) ) : esc_attr( $this->panels_groups );
    }
    
    if ( !empty( $this->panels_icon ) ) {
      $widget_args['panels_icon'] = esc_attr( $this->panels_icon );
    }

		// Enqueue style if widget is active (appears in a sidebar) or if in Customizer preview.
		if ( is_active_widget( false, false, $this->widget_id ) || is_customize_preview() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		parent::__construct( $this->widget_id, $this->widget_name, $widget_args, $this->control_args );
	}

	/**
	 * Enqueue script/style for widget.
	 *
	 * @since		1.0.0
	 */
	public function enqueue_scripts() {}

	/**
	 * Get widget id from before_widget args.
	 *
	 * @since		1.0.0
	 *
   * @return string widget_id
	 */
	public function getID() {
		return $this->widget_id;
	}

	/**
	 * Parse default value for fields
   *
	 * @since		1.0.0
   *
   * @return array instance
	 */
	public function parse_defaults( $instance ) {
    $defaults = array();
		$fields = $this->settings;
    foreach ( $fields as $key => $field ) {
      if ( !isset( $instance[ $key ] ) ) {
        $defaults[ $key ] = isset( $field['std'] ) ? $field['std'] : '';
      } else {
        $defaults[ $key ] = $instance[ $key ];
      }
    }
    return (object)$defaults;
	}

	/**
	 * Convert array to HTML attributes
	 *
	 * @since		1.0.0
	 *
   * @return string
	 */
	private function set_attr( $arr ) {
		$attributes = '';
		if ( is_array( $arr ) && !empty( $arr ) ) {
			foreach ($arr as $name => $value) {
				$attributes .= ' '.$name.'="'.$value.'"';
			}
		}
		echo $attributes;
	}

	/**
	 * update function.
	 *
	 * @since 	1.0.0
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		if ( ! $this->settings )
			return $instance;

		foreach ( $this->settings as $key => $setting ) {
			switch ( $setting[ 'type' ] ) {
				case 'description' :
					$instance[ $key ] = '';
				break;
				case 'textarea' :
					if ( current_user_can( 'unfiltered_html' ) )
						$instance[ $key ] = $new_instance[ $key ];
					else
						$instance[ $key ] = wp_kses_data( $new_instance[ $key ] );
				break;
				case 'multicheck' :
					//$instance[ $key ] = maybe_serialize( $new_instance[ $key ] );
						$instance[ $key ] = is_array( $new_instance[ $key ] ) ? array_filter( $new_instance[ $key ], 'esc_attr' ) :  array( esc_attr($new_instance[ $key ]) );
				break;
				case 'checkbox' :
					$instance[ $key ] = isset( $new_instance[ $key ] );
				break;
				case 'text' :
				case 'select' :
          $instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
        break;
				case 'number' :
					$instance[ $key ] = absint( $new_instance[ $key ] );
				break;
				case 'colorpicker' :
					$instance[ $key ] = esc_color( $new_instance[ $key ] );
				break;
				case 'iconpicker' :
					$instance[ $key ] = esc_attr( $new_instance[ $key ] );
				break;
				default :
					$instance[ $key ] = apply_filters( 'ctcore_widget_update_type_' . $setting[ 'type' ], $new_instance[ $key ], $key, $setting );
				break;
			}
		}

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @since 	1.0.0
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {

		if ( ! $this->settings )
			return;

		echo '<div class="widget-factory-options">';

		foreach ( $this->settings as $key => $setting ) {

			$atts = array();
			$field_id = esc_attr( $this->get_field_id( $key ) );
			$field_name = esc_attr( $this->get_field_name( $key ) );
			$value = isset( $instance[ $key ] ) ? $instance[ $key ] : ( isset($setting[ 'std' ]) ? $setting[ 'std' ] : '' );

			$atts[ 'id' ] 		= $field_id;
			$atts[ 'name' ] 	= $field_name;
			$atts[ 'value' ] 	= is_array( $value ) ? $value : esc_attr( $value );

			if ( isset( $setting[ 'holder' ] ) ) {
				$atts[ 'placeholder' ] = $setting[ 'holder' ];
			}

			if ( isset( $setting['atts'] ) && is_array( $setting['atts'] ) && !empty( $setting['atts'] ) ) {
        foreach ($setting['atts'] as $attr => $attrval) {
        	$atts[ $attr ] = $attrval;
        }
      }

			switch ( $setting[ 'type' ] ) {
				case 'description' :
					?>
					<p class="description"><?php echo $value; ?></p>
					<?php
				break;
				case 'text' :
					?>
					<p>
						<label for="<?php echo $field_id; ?>"><?php echo $setting[ 'label' ]; ?></label>
						<input class="widefat" type="text"<?php $this->set_attr( $atts ); ?> />
					</p>
					<?php
				break;
        case 'radio' :
          $css_inline = ( isset($setting['inline']) && false !== $setting['inline'] ) ? ' class="radio-inline"' : '';
					?>
          <p style="margin-bottom:0"><?php echo $setting[ 'label' ]; ?></p>
					<p<?php echo $css_inline; ?> style="margin-top:0.3em">
            <?php foreach ( $setting['options'] as $id => $label ) :
							$atts[ 'id' ] = esc_attr( $field_id.'-'.$id );
							$atts[ 'value' ] = esc_attr( $id );
						?>
            <label for="<?php echo esc_attr( $field_id.'-'.$id ); ?>">
              <input type="radio"<?php $this->set_attr( $atts ); ?> <?php checked( esc_attr( $id ), $value ); ?> />
              <?php echo $label; ?><br />
            </label>
            <?php endforeach; ?>
					</p>
					<?php
				break;
				case 'checkbox' :
					$atts[ 'value' ] = '1';
					?>
					<p>
						<label for="<?php echo $field_id; ?>">
							<input type="checkbox"<?php $this->set_attr( $atts ); ?> <?php checked( '1', $value ); ?>/>
							<?php echo $setting[ 'label' ]; ?>
						</label>
					</p>
					<?php
				break;
				case 'multicheck' :
					//$value = maybe_unserialize( $value );

					if ( ! is_array( $value ) )
						$value = array();
					?>
					<p style="margin-bottom:0"><?php echo $setting[ 'label' ]; ?></p>
					<p style="margin-top:0.3em">
						<?php foreach ( $setting[ 'options' ] as $id => $label ) :
							$atts[ 'id' ] 				= esc_attr( $field_id.'-'.$id );
							$atts[ 'name' ] 			= $field_name . '[]';
							$atts[ 'value' ] 			= esc_attr( $id );
							if ( in_array( $id, $value ) ) {
								$atts[ 'checked' ] 	= 'checked';
							}
						?>
						<label for="<?php echo esc_attr( $field_id.'-'.$id ); ?>">
							<input type="checkbox"<?php $this->set_attr( $atts ); ?> />
							<?php echo esc_html( $label ); ?>
						</label><br />
						<?php unset( $atts[ 'checked' ] ); ?>
						<?php endforeach; ?>
					</p>
					<?php
				break;
				case 'select' :
          $empty_option = ( isset($setting['empty']) && false !== $setting['empty'] ) ? '<option>-- none --</option>' : '';
					unset( $atts['value'] );
					?>
					<p>
						<label for="<?php echo $field_id; ?>"><?php echo $setting[ 'label' ]; ?></label>
						<select class="widefat"<?php $this->set_attr( $atts ); ?>>
              <?php echo $empty_option; ?>
							<?php foreach ( $setting[ 'options' ] as $key => $label ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $value ); ?>><?php echo esc_attr( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<?php
				break;
				case 'term-select' :
					unset( $atts['value'] );
					if ( ! empty( $setting[ 'taxonomy' ] ) && taxonomy_exists( $setting[ 'taxonomy' ] ) ) :
						$term_field = isset( $setting['field'] ) ? esc_attr($setting['field']) : 'term_id';
						$tax_name		= get_taxonomy( $setting[ 'taxonomy' ] )->label;
						$options 		= array();
						$terms 	 		= get_terms( array(
							'taxonomy'		=> $setting[ 'taxonomy' ],
							'hide_empty'	=> false
						) );
						if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
							foreach ( $terms as $term ) {
								$options[ $term->$term_field ] = $term->name;
							}
						}
						$option_all = ( isset( $setting['option_all'] ) && false === $setting['option_all'] ) ? false : true;
					?>
					<p>
						<label for="<?php echo $field_id; ?>"><?php echo $setting[ 'label' ]; ?></label>
						<select class="widefat"<?php $this->set_attr( $atts ); ?>>
							<?php if ( $option_all ) : ?>
							<option value="all" <?php selected( 'all', $value ); ?>>
								<?php printf( esc_attr__( 'All %s', 'ctcore' ), $tax_name ); ?>
							</option>
							<?php endif; ?>
							<?php foreach ( $options as $key => $label ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $value ); ?>><?php echo esc_attr( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<?php
					endif;
				break;
				case 'number' :
					$atts[ 'value' ] = absint( $value );
					?>
					<p>
						<label for="<?php echo $field_id; ?>"><?php echo $setting[ 'label' ]; ?></label>
						<input class="widefat" type="number"<?php $this->set_attr( $atts ); ?> />
					</p>
					<?php
				break;
				case 'textarea' :
					unset( $atts['value'] );
					$atts[ 'rows' ] = isset( $setting[ 'rows' ] ) ? $setting[ 'rows' ] : 3;
					?>
					<p>
						<label for="<?php echo $field_id; ?>"><?php echo $setting[ 'label' ]; ?></label>
						<textarea class="widefat"<?php $this->set_attr( $atts ); ?>><?php echo esc_html( $value ); ?></textarea>
					</p>
					<?php
				break;
				case 'colorpicker' :
					wp_enqueue_script( 'wp-color-picker' );
					wp_enqueue_style( 'wp-color-picker' );
					wp_enqueue_script(
						CT_SLUG.'-widget-color',
						ctcore_js( 'admin-widget-color' ),
						array( 'jquery', 'underscore', 'wp-color-picker' ),
						CT_VERSION,
						true
					);
					$atts[ 'class' ] = 'widefat wp-color-picker-widget';
					$atts[ 'data-default-color' ] = $value;
					?>
						<p style="margin-bottom: 0;">
							<label for="<?php echo $field_id; ?>"><?php echo $setting[ 'label' ]; ?></label>
						</p>
						<input type="text"<?php $this->set_attr( $atts ); ?> />
						<p></p>
					<?php
				break;
				case 'iconpicker' :
					//add_thickbox();
          //wp_enqueue_style( 'wp-jquery-ui-dialog' );
					wp_enqueue_style(
						'font-awesome.min.css',
						ctcore_css( 'font-awesome' ),
						array( 'wp-jquery-ui-dialog' ),
						CT_VERSION
					);
					wp_enqueue_script(
						CT_SLUG.'-widget-icon',
						ctcore_js( 'admin-widget-icon' ),
						array( 'jquery', 'jquery-ui-dialog' ),
						CT_VERSION,
						true
					);
					?>
						<p class="ct-iconpicker">
							<label for="<?php echo $field_id; ?>"><?php echo $setting[ 'label' ]; ?> </label>
							<input type="hidden"<?php $this->set_attr( $atts ); ?> />
							<span id="ct-icon-wrap" class="ct-icon-wrap">
								<i class="<?php echo $value; ?> icon"<?php echo !empty($value)?'':' style="display:none;"'; ?>></i>
								<button type="button" class="ct-icon-remove button button-small default"<?php echo !empty($value)?'':' style="display:none;"'; ?>>X</button>
								<button type="button" class="ct-icon-select button button-small default"<?php echo !empty($value)?' style="display:none;"':''; ?>><?php esc_attr_e('Select Icon','ctcore'); ?></button>
							</span>
						</p>
					<?php
				break;
				case 'image' :
					wp_enqueue_media();
					wp_enqueue_script(
						CT_SLUG.'-widget-image',
						ctcore_js( 'admin-widget-image' ),
						array( 'jquery' ),
						CT_VERSION,
						true
					);
					if ( empty( $atts['placeholder'] ) ) {
						 $atts['placeholder'] = 'http://';
					}
				?>
					<p style="margin-bottom: 0;">
						<label for="<?php echo $field_id; ?>"><?php echo $setting[ 'label' ]; ?></label>
					</p>
					<p style="margin-top: 3px;">
						<a href="#" class="button-secondary <?php echo $field_id; ?>-add"><?php _e( 'Choose Image', 'ctcore' ); ?></a>
					</p>
					<p>
						<input type="text" class="widefat"<?php $this->set_attr( $atts ); ?> />
					</p>
					<script>
						jQuery(document).ready(function($){
							var <?php echo $key; ?> = new cImageWidget.MediaManager({
								target: '<?php echo $field_id; ?>',
							});
						});
					</script>
				<?php
				break;
				default :
					do_action( 'ctcore_widget_type_' . $setting[ 'type' ], $this, $key, $setting, $instance );
				break;
			} // switch type
		} // endforeach settings type

		echo '</div>'; //ctcore-widget-options
	}

}
