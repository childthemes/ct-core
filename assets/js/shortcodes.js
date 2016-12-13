/**
 * WordPress Shortcode tinyMCE Plugins
 * < Javascript template >
 *
 * @link       http://childthemes.net/
 * @since      1.0.0
 *
 * @package    CT_Core
 * @subpackage CT_Core/assets/js
 */
(function( tinymce ) {
 	tinymce.PluginManager.add( 'mtcore', function (editor, url) {
		var pick_color = function() {
			var colorPickerCallback = editor.settings.color_picker_callback;
			if (colorPickerCallback) {
				return function() {
					var self = this;
					colorPickerCallback.call(
						editor,
						function(value) {
							self.value(value).fire('change');
						},
						self.value()
					);
				};
			}
		};
 		editor.addButton( 'mtcore', {
 			type: 'menubutton',
 			image: url+'/shortcode-icon.png',
 			tooltip: 'Shortcode',
 			menu: [
        {
          text: 'Content',
          menu: []
        },
        {
          text: 'Elements',
          menu: []
        },
        {
          text: 'Boxed',
          menu: []
        },
        {
          text: 'Media',
          menu: []
        },
        {
          text: 'Others',
          menu: []
        }
      ]
 		});
 	});
})( window.tinymce );
