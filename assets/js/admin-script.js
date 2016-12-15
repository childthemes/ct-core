(function ($) {

	"use strict";

  var $exportarea = $( '#setting_export_options.format-settings textarea.textarea' ).prop( 'readonly', true );

  $exportarea.on( 'focus click', function (){
    $(this).select();
  });

})(jQuery);