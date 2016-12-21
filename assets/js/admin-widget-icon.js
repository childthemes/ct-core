(function ($) {

	"use strict";

	function iToggle( wie_el, thod, val ) {
		if ( thod == 'show' && val !== null ) {
			$(wie_el).find('#ct-icon-wrap .ct-icon-select').hide();
			$(wie_el).find('#ct-icon-wrap .ct-icon-remove').show();
			$(wie_el).find('#ct-icon-wrap i').attr('class', val+' icon').show();
			$(wie_el).find('p.ct-iconpicker input[type="hidden"]').val(val).trigger('change');
		} else {
			$(wie_el).find('#ct-icon-wrap .ct-icon-select').show();
			$(wie_el).find('#ct-icon-wrap .ct-icon-remove').hide();
			$(wie_el).find('#ct-icon-wrap i').attr('class','').hide();
			$(wie_el).find('p.ct-iconpicker input[type="hidden"]').val('').trigger('change');
		}
	}

	function on_popup_show( cur_widget ) {
		$(document).on('click', '#iconlist .column', function(e){
			e.preventDefault();
			var selected_value = $(this).find('i').attr('class').replace(' icon','');
			$('#iconlist .column').removeClass( 'selected' );
			$(this).addClass( 'selected' );
			$('input[name="iconpicker-search"]').val(selected_value);
		});

		$(document).on('change', '#iconpicker-category', function(){
			var curval = $(this).val();
			$('#iconlist .icon-cat-wrap').hide();
			if ( curval.length ) {
				$('#iconlist').find('.'+curval).show();
			} else {
				$('#iconlist .icon-cat-wrap').show();
			}
		});

		$(document).on('click', 'button#pick-icon', function(e){
			if ( ! $(cur_widget).find('p.ct-iconpicker > input[type="hidden"]').val().length ) {
				iToggle( cur_widget, 'show', $('input[name="iconpicker-search"]').val() );
			}
			if ( wp && wp.customize ) {
				$( '#colorpickerwrap' ).hide();
			}
		});
	}

	function init(widget_el, is_cloned) {

		var _input = $(widget_el).find('p.ct-iconpicker > input[type="hidden"]'),
				_value = _input.val();

		$(widget_el).find('#ct-icon-wrap > .ct-icon-remove').click( function(){
			iToggle( widget_el, 'hide', null );
		});

		$(widget_el).find('#ct-icon-wrap > .ct-icon-select').click( function(){
      
      on_popup_show( widget_el );
      
			if ( wp && wp.customize ) {
				$( '#colorpickerwrap' ).show();
        return;
			}
      
      $( '#colorpickerwrap' ).dialog({
        title: 'Choose an Icon',
        autoOpen: true,
        modal: true,
        height: 400,
        width: 600,
        buttons: [
          {
            text: "Cancel",
            click: function () {
              $(this).dialog( 'close' );
            }
          },
          {
            text: "Select Icon",
            class: 'button-primary',
            click: function () {
              if ( ! $(widget_el).find('p.ct-iconpicker > input[type="hidden"]').val().length ) {
                iToggle( widget_el, 'show', $('input[name="iconpicker-search"]').val() );
              }
              $(this).dialog( 'close' );
            }
          }
        ]
      })
		});
	}

	/**
	 * @param {jQuery.Event} e
	 * @param {jQuery} widget_el
	 */
	function on_form_update(e, widget_el) {
		if ($(widget_el).find('p.ct-iconpicker').length) {
			init(widget_el, 'widget-added' === e.type);
		}
	}

	$(document).ready( function (){
		$('.widget:has(.ct-iconpicker)').each(function (){
			init($(this));
		});
	})
  .on( 'panelsopen', function (e){
    var dialog = e.target;
    if ( $(dialog).has( '.ct-iconpicker' ) ) {
      init( $(dialog) );
    }
  })
  .on( 'widget-added widget-updated', on_form_update );

})(jQuery);
