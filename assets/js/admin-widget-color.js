(function ($) {

	"use strict";

	function init($widget_el, is_cloned) {
		var $elem = $widget_el.find('input.wp-color-picker-widget');
		// Clean up from the clone which lost all events and data
		if ( is_cloned ) {
			$widget_el.find('a.wp-color-result').remove();
		}

		$elem.wpColorPicker({
			change: _.throttle( function () { $(this).trigger('change'); }, 1000, {leading: false} )
		});
	}

	/**
	 * @param {jQuery.Event} e
	 * @param {jQuery} widget_el
	 */
	function on_form_update(e, $widget_el) {
		if ($widget_el.find('input.wp-color-picker-widget').length) {
			init($widget_el, 'widget-added' === e.type);
		}
	}
  
	$(document).ready( function (){
		$('.widget:has(.wp-color-picker-widget)').each(function (){
			init($(this));
		});
	})
  .on( 'panelsopen', function (e){
    var dialog = e.target;
    if ( $(dialog).has( 'input.wp-color-picker-widget' ) ) {
      init( $(dialog) );
    }
  })
  .on( 'widget-added widget-updated', on_form_update );

})(jQuery);
