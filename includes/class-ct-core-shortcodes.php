<?php
/**
 * WordPress Shortcode Loader Class
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
 *  CT_Core_Shortcode - ChildThemes Core
 *
 * @author  Child Themes
 * @since 	1.0.0
 */
class CT_Core_Shortcodes {

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

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
  protected $rel_path = 'includes/shortcodes';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Holds shortcodes loaded in.
	 *
	 * @var array
	 */
	public $shortcodes = array();

	/**
	 * Holds shortcodes TinyMCE Menu.
	 *
	 * @var array
	 */
	public $menus = array();

	/**
	 * Fire it up
	 *
	 * @since  1.0.0
	 */
	public function __construct() {

    $this->version = CT_VERSION;
		if ( false !== ( $opts = get_option( 'ctcore_features' ) ) && in_array( 'shortcodes', $opts ) ) {
	    $this->active = true;
			add_action( 'after_setup_theme', array( $this, 'register_shortcodes' ), 15 );
			add_action( 'admin_head', array( $this, 'register_mce_button' ), 1 );
			add_filter( 'the_content',  array( $this, 'fix_wpautop_content' ), 1 );
		}
	}

	/**
	 * Add plugin button TinyMCE to WP Editor
	 *
	 * @since    1.0.0
	 */
	public function register_mce_button() {
		if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
	    return;
	  }
	  // Check if WYSIWYG is enabled
	  if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_action( 'before_wp_tiny_mce', array( $this, 'generate_script' ), 999 );
	    add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );
	    add_filter( 'mce_buttons', array( $this, 'mce_buttons' ) );
	  }
	}

	/**
	 * Add TinyMCE Button
	 *
	 * @since    1.0.0
	 */
	public function mce_external_plugins( $plugin_array ) {
		$upload = wp_upload_dir();
		if ( file_exists( trailingslashit($upload['basedir']) . 'ct-core/shortcodes.js' ) ) {
			$plugin_array['ctcore'] = trailingslashit($upload['baseurl']) . 'ct-core/shortcodes.js';
		}
	  return $plugin_array;
	}

	/**
	 * Register new button in the editor
	 *
	 * @since    1.0.0
	 */
	public function mce_buttons( $buttons ) {
	  $buttons[] = 'ctcore';
	  return $buttons;
	}

	/**
	 * Add script variable for shortcode buttons.
	 *
	 * @since    1.0.0
	 */
	public function generate_script() {

		$theme = get_template();
		$upload = wp_upload_dir();

		if ( !$theme || $upload['error'] ) {
			return;
		}

    $menus = $this->menus;
    $cache = 'CT_'.md5( serialize( array($menus, $theme) ) );
    $files = trailingslashit($upload['basedir']) . 'ct-core/shortcodes.js';

		if ( ! file_exists( $files ) ) {
      $this->clear_cache();
      delete_transient( $cache );
    }

    if ( false === ( $mce = get_transient( $cache ) ) ) {
      $mce = '(function( tinymce ) {';
      $mce .= 'tinymce.create("tinymce.plugins.ctcore", {';
      $mce .= 'init : function(editor, url) {';
        $mce .= 'var pick_color=function(){var c=editor.settings.color_picker_callback;return c?function(){';
        $mce .= 'var i=this;c.call(editor,function(c){i.value(c).fire("change")},i.value())}:void 0};';
				$mce .= 'var comboOnSelect=function(t){void 0===t.target.val||""===t.target.value()?';
				$mce .= 't.target.val=t.target.value():t.target.val+=","+t.target.value(),t.target.value(t.target.val)};';
        $mce .= 'editor.addButton( "ctcore", {';
        $mce .= 'type: "menubutton",';
        $mce .= 'image: "'.CT_ASSETS.'js/shortcode-icon.png",';
        $mce .= 'tooltip: "Shortcodes",';
        $mce .= 'menu: [';
        foreach ($menus as $menu) {
          if ( empty( $menu['menu'] ) ) {
            continue;
          }
          $mce .= '{';
            $mce .= 'text: "'.$menu['text'].'",';
						$mce .= 'icon: "'.$menu['icon'].'",';
            $mce .= 'menu: [';
            foreach ($menu['menu'] as $submenu) {
              $mce .= '{';
                $mce .= 'text: "'.$submenu['text'].'",';
                $mce .= 'onclick: function (e) {';
                  $mce .= $this->generate_dialog( $submenu );
                $mce .= '}';
                $mce .= '';
              $mce .= '},';
            }
            $mce .= ']';
          $mce .= '},';
        }
        $mce .= ']'; //menu
        $mce .= '});'; //addButton
      $mce .= '},'; // init
      $mce .= 'createControl : function(n, cm) { return null; },';
      $mce .= '});'; // tinymce.create
      $mce .= 'tinymce.PluginManager.add( "ctcore", tinymce.plugins.ctcore );';
      $mce .= '})( window.tinymce );'; // end function
      $data = file_put_contents( $files, $mce );
      set_transient( $cache, $mce, 365 * DAY_IN_SECONDS );
    }
		return;
	}

	/**
	 * Generate onClick action for shortcode menu
	 *
	 * @since    1.0.0
	 */
  private function generate_dialog( $menuitem ) {

		if ( empty( $menuitem['fields']['body'] ) ) {
			$js = 'e.stopPropagation();';
			$js .= 'var content = editor.selection.getContent();';
			$js .= 'editor.insertContent( \'['.$menuitem['slug'].'\' + ';
			$js .= $menuitem['enclose'] ? '\']\'+content+\'[/'.$menuitem['slug'].']\'' : '\' /]\'';
			$js .= ' );';
			return $js;
		}

		$results = array();
    $js = 'editor.windowManager.open({';
		$js .= 'title: "'.$menuitem['fields']['title'].'",';
		$js .= 'body: [';
		foreach ($menuitem['fields']['body'] as $field) {
			$js .= '{';
			if ( $field['type'] == 'container' ) {
				$field['label'] = (isset($field['label'])&&!empty($field['label'])) ? $field['label'] : '   ';
				$js .= 'type: "container",';
				$js .= 'label: "'.$field['label'].'",';
				$js .= 'html: "'.$field['html'].'",';
			}
			elseif ( isset( $field['name'] ) && !empty( $field['name'] ) ) {
				$field['name'] = sanitize_title( $field['name'] );
				$field_value = isset($field['value']) ? 'value: "'.$field['value'].'",' : '';
				$field_value .= isset($field['placeholder']) ? 'placeholder: "'.$field['placeholder'].'",' : '';
				$field_tips = isset($field['tooltip']) ? 'tooltip: "'.$field['tooltip'].'",' : '';
				$field_styling = isset($field['classes']) ? 'classes: "'.$field['classes'].'",' : '';
				$field_styling .= isset($field['border']) ? 'border: "'.$field['border'].'",' : '';
				$field_styling .= isset($field['margin']) ? 'margin: "'.$field['margin'].'",' : '';
				$field_styling .= isset($field['padding']) ? 'padding: "'.$field['padding'].'",' : '';
				$field_styling .= isset($field['minHeight']) ? 'minHeight: "'.$field['minHeight'].'",' : '';
				$field_styling .= isset($field['minWidth']) ? 'minWidth: "'.$field['minWidth'].'",' : '';
				$results[ $field['name'] ] = 'e.data.'.$field['name'];
				switch ($field['type']) {
				case 'listbox':
					$js .= 'type: "listbox",';
					$js .= 'name: "'.$field['name'].'",';
					$js .= 'label: "'.$field['label'].'",';
					$js .= $field_value;
					$js .= $field_tips;
					$js .= $field_styling;
					$js .= 'values: '.json_encode($field['values']).',';
					$js .= isset($field['onselect']) ? 'onselect: function (e){'.trim($field['onselect']).'},' : '';
					break;
				case 'combobox':
					$js .= 'type: "combobox",';
					$js .= 'name: "'.$field['name'].'",';
					$js .= 'label: "'.$field['label'].'",';
					$js .= $field_value;
					$js .= $field_tips;
					$js .= $field_styling;
					$js .= 'values: '.json_encode($field['values']).',';
					$js .= "onselect: function (e) {";
          $js .= "if ( this.val === undefined || this.value() == '' ) {";
        	$js .= "this.val = this.value();";
          $js .= "} else { this.val += ','+this.value(); }";
          $js .= "this.value( this.val ); },";
					break;
				case 'textbox':
					$js .= 'type: "textbox",';
					$js .= 'name: "'.$field['name'].'",';
					$js .= 'label: "'.$field['label'].'",';
					$js .= isset( $field['multiline'] ) ? 'multiline: '.($field['multiline']?'true':'false').',' : '';
					$js .= $field_tips;
					$js .= $field_value;
					$js .= $field_styling;
					break;
				case 'radio':
				case 'checkbox':
					$js .= 'type: "checkbox",';
					$js .= 'name: "'.$field['name'].'",';
					$js .= 'label: "'.$field['label'].'",';
					$js .= 'text: "'.$field['text'].'",';
					$js .= $field_tips;
					$js .= $field_styling;
					$js .= isset( $field['checked'] ) ? 'checked: '.($field['checked']?'true':'false').',' : '';
					break;
				case 'colorbox':
					$js .= 'type: "colorbox",';
					$js .= 'name: "'.$field['name'].'",';
					$js .= 'label: "'.$field['label'].'",';
					$js .= $field_tips;
					$js .= $field_value;
					$js .= $field_styling;
					$js .= 'onaction: pick_color(),';
					break;
				}
			}
			$js .= '},';
		} // endforeach
		$js .= '],'; // body
		$js .= 'onsubmit: function( e ) {';
			$js .= 'var atts = "", content = editor.selection.getContent();';
			foreach ($results as $key => $value) {
				$js .= 'if ('.$value.' === true) {';
				$js .= ' '.$value.' = "1"; ';
				$js .= '}';
				$js .= 'atts += ('.$value.'.length > 0 ) ? \' '.$key.'="\'+'.$value.'+\'"\' : "";';
			}
			$js .= 'editor.insertContent( \'['.$menuitem['slug'].'\' + ';
			$js .= 'atts + ';
			$js .= $menuitem['enclose'] ? '\']\'+content+\'[/'.$menuitem['slug'].']\'' : '\' /]\'';
			$js .= ' );';
		$js .= '}'; //onsubmit
    $js .= '});'; //windowManager

    return $js;
  }

	/**
	 * Fix autowp WordPress on our shortcode
	 *
	 * @since    1.0.0
	 */
	public function fix_wpautop_content( $content ) {

		if ( !is_array( $this->shortcodes ) ) {
			return $content;
		}

		// array of custom shortcodes requiring the fix
		$block = join( "|", array_keys( $this->shortcodes ) );

		// opening tag
		$rep = preg_replace( "/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content );
		// closing tag
		$rep = preg_replace( "/(<p>)?\[\/($block)](<\/p>|<br \/>)?/", "[/$2]", $rep );

		return $rep;
	}

	/**
	 * Get all shortcodes
	 *
	 * @since 	1.0.0
	 */
	public function register_shortcodes() {

		/**
		 * Change theme relative path with this filter
		 */
		$rel_path = apply_filters( 'ctcore_shortcode_path', $this->rel_path, self::$instance );

		/**
		 * Modify default main menu dropdown for shortcode
		 */
		$this->menus = apply_filters( 'ctcore_shortcode_menu',
		array(
			'content' => array(
				'text' => esc_attr__( 'Content', 'ctcore' ),
				'icon' => 'feedback',
				'menu' => array()
			),
			'element' => array(
				'text' => esc_attr__( 'Elements', 'ctcore' ),
				'icon' => 'networking',
				'menu' => array()
			),
			'boxed' 	=> array(
				'text' => esc_attr__( 'Boxed', 'ctcore' ),
				'icon' => 'editor-table',
				'menu' => array()
			),
			'media' 	=> array(
				'text' => esc_attr__( 'Media', 'ctcore' ),
				'icon' => 'admin-media',
				'menu' => array()
			),
			'other' 	=> array(
				'text' => esc_attr__( 'Others', 'ctcore' ),
				'icon' => 'portfolio',
				'menu' => array()
			),
		) );

		//Check Child Theme Shortcodes
		if ( is_child_theme() ) {
			if ( is_dir( CT_CHILD . $rel_path ) ) {
				$this->load_shortcodes( CT_CHILD . $rel_path );
			}
		}

		//Check Theme Shortcode Overrides;
		if ( is_dir( CT_THEME . $rel_path ) ) {
			$this->load_shortcodes( CT_THEME . $rel_path );
		}

		/**
		 * Theme override before plugin shortcde
		 * You can replace our predefined shortcode
		 * or remove all with:
		 * add_filter( 'ctcore_shortcode_directory', '__return_empty_array' );
		 *
		 */
		$dirs = apply_filters( 'ctcore_shortcode_directory',
			array( 'ctcore' => CT_INC . 'shortcodes' ),
			$this->shortcodes
		);

		/**
		 * Load Directories
		 * Fix/absolute folder path
		 */
		if ( !empty( $dirs ) && is_array( $dirs ) ) {
			foreach ($dirs as $key => $directory) {
				if ( is_dir( $directory ) ) {
					$prefix = is_string( $key ) ? $key : '';
					$this->load_shortcodes( $directory, $prefix );
				}
			}
		}
	}

	/**
	 * Load shortcodes
	 *
	 * @since 	1.0.0
	 */
	public function load_shortcodes( $directory, $prefix = '' ) {

		$directory = trailingslashit( $directory );

		if ( is_dir( $directory ) ) {
			$shortcode_files = glob( $directory.'*.php', GLOB_NOSORT );
			if ( is_array( $shortcode_files ) && count( $shortcode_files ) > 0 ) {
				foreach ( $shortcode_files as $shortcode_file ) {
					$slug = empty($prefix) ? get_template() : $prefix;
					$sc_match = str_replace( $slug.'-', '', basename($shortcode_file, '.php'), $matched );
					if ( $matched > 0 ) {
						$sc_base = str_replace( '-', '_', sanitize_title( $sc_match ) );
						if ( !array_key_exists( $sc_base , $this->shortcodes ) ) {
							$options_file = $directory.'options/'.$sc_base.'.php';
              $sc_data = get_file_data( $shortcode_file, array(
      					'name' => 'Name', 'desc' => 'Desc', 'close' => 'Ends', 'menu' => 'Menu'
      				) );
							$sc_data['slug'] = $sc_base;
							$sc_data['file'] = $shortcode_file;
							$sc_data['close'] = filter_var( $sc_data['close'], FILTER_VALIDATE_BOOLEAN );
							$sc_data['fields'] = array();
							if ( is_file( $options_file ) && file_exists( $options_file ) ) {
								$sc_data['fields'] = include_once( $options_file );
							}
							$sc_data = apply_filters( 'ctcore_shortcode_data', $sc_data, $sc_base );
							if ( isset( $sc_data['fields'] ) && is_array( $sc_data['fields'] ) ) {
								$sc_data['fields'] = $this->set_fields( $sc_data );
							}
							if ( !shortcode_exists( $sc_base ) && !empty($sc_data['name']) ) {
								$this->shortcodes[$sc_base] = $sc_data;
								$sc_render = new CT_Core_Shortcode( $sc_base );
							}
						} // shortcode_exists
					} // if $is_shortcode
				} // endforeach
			} // if files found
		} // if $directory
	}

	/**
	 * Set shortcode field options.
	 *
	 * @since     1.0.0
	 */
	private function set_fields( $data ) {

		$attr = array();
		$menu = $this->menus;

		if ( empty($data) || !is_array($data['fields']) ) {
			return $attr;
		}

		$fields = $data['fields'];

		$fields_head = array(
			'type'		=> 'container',
			'label'		=> esc_html__( 'Description', 'ctcore' ),
			'html'		=> $data['desc']
		);
		array_unshift( $fields, $fields_head );

		foreach ($menu as $key => $value) {
			if ( $data['menu'] == $key ) {
				$menu[ $key ]['menu'][] = array(
					'text'	  => $data['name'],
          'slug'    => $data['slug'],
          'enclose' => $data['close'],
					'fields'  => array(
						'title' => sprintf( __('%s Attributes','ctcore'), $data['name'] ),
						'body'  => $fields
					)
				);
			}
		}
    $this->menus = $menu;
		return $fields;
	}

	/**
	 * Delete cache shortcode field attributes.
	 *
	 * @since     1.0.0
	 */
	public function clear_cache() {
		$upload = wp_upload_dir();
    if ( empty( $this->menus ) || $upload['error'] )
      return false;

		$sc_temp = file_get_contents( CT_PATH.'assets/js/shortcodes.js' );
		$sc_real = file_put_contents( trailingslashit($upload['basedir']).'ct-core/shortcodes.js', $sc_temp );
		return true;
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
	 * Set this feature active.
	 *
	 * @since     1.0.0
	 */
	public function is_active() {
		return $this->active;
	}

}

class CT_Core_Shortcode extends CT_Core_Shortcodes {

	//Shortcode name
	public $shortcode_base = '';

	//Shortcode atts
	public $shortcode_atts = '';

	//Shortcode template file
	public $shortcode_template = false;

	public function __construct( $settings ) {

		$this->shortcode_base = $settings;

		add_shortcode( $this->shortcode_base,  array( $this, 'render' ) );
	}

	/**
	 * Output shortcode content
	 *
	 * @param array   $atts
	 * @param string  $content
	 * @return string
	 */
	public function render( $atts, $content ) {
		$this->shortcode_atts = $atts;

		//$content = ($content) ? $this->fix_shortcode_content($content) : '';

		$this->get_shortcode_template();

		if ( $this->shortcode_template ) {
			$output = '';
			ob_start();
			include $this->shortcode_template;
			$output .= ob_get_contents();
			ob_end_clean();

			return apply_filters( 'shortcode_content', $output, $this->shortcode_base, $this->shortcode_atts, $content );
		}
	}

	/**
	 * Cleanup shortcode content if has shortcode from wp autop and auto br tags.
	 *
	 * @since 1.0.0
	 */
	private function fix_shortcode_content($content) {
		$array = array (
			'<p>[' => '[',
			'<br />[' => '[',
			'<br>[' => '[',
			']</p>' => ']',
			']<br />' => ']',
			']<br>' => ']',
			'<p></p>' => ''
		);
		$content = strtr($content, $array);
		return str_replace("\r\n", '', $content);
	}

	/**
	 * Map shortcode child attributes
	 *
	 * @since		1.0.0
	 */
	private function attribute_map($str, $att = null) {
	  $res = array();
	  $reg = get_shortcode_regex();
	  preg_match_all('~'.$reg.'~',$str, $matches);
	  foreach($matches[2] as $key => $name) {
	    $parsed = shortcode_parse_atts($matches[3][$key]);
	    $parsed = is_array($parsed) ? $parsed : array();

	    if(array_key_exists($name, $res)) {
	      $arr = array();
	      if(is_array($res[$name])) {
	        $arr = $res[$name];
	      } else {
	        $arr[] = $res[$name];
	      }

	      $arr[] = array_key_exists($att, $parsed) ? $parsed[$att] : $parsed;
	      $res[$name] = $arr;

	    } else {
	      $res[$name] = array_key_exists($att, $parsed) ? $parsed[$att] : $parsed;
	    }
	  }
	  return $res;
	}

	/**
	 * Sets shortcode template
	 *
	 * @since		1.0.0
	 */
	private function get_shortcode_template() {

		if ( array_key_exists( $this->shortcode_base, parent::get_instance()->shortcodes ) && is_file( parent::get_instance()->shortcodes[$this->shortcode_base]['file'] ) ) {
			$this->shortcode_template = parent::get_instance()->shortcodes[$this->shortcode_base]['file'];
		}else {
			$this->shortcode_template = false;
		}

	}

}
