(function ($) {

	"use strict";

	function iToggle( wie_el, thod, val ) {
		if ( thod == 'show' && val !== null ) {
			$(wie_el).find('#mt-icon-wrap .mt-icon-select').hide();
			$(wie_el).find('#mt-icon-wrap .mt-icon-remove').show();
			$(wie_el).find('#mt-icon-wrap i').attr('class', val+' icon').show();
			$(wie_el).find('p.mt-iconpicker input[type="hidden"]').val(val).trigger('change');
		} else {
			$(wie_el).find('#mt-icon-wrap .mt-icon-select').show();
			$(wie_el).find('#mt-icon-wrap .mt-icon-remove').hide();
			$(wie_el).find('#mt-icon-wrap i').attr('class','').hide();
			$(wie_el).find('p.mt-iconpicker input[type="hidden"]').val('').trigger('change');
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
			if ( ! $(cur_widget).find('p.mt-iconpicker > input[type="hidden"]').val().length ) {
				iToggle( cur_widget, 'show', $('input[name="iconpicker-search"]').val() );
			}
			if ( wp && wp.customize ) {
				$( '#colorpickerwrap' ).hide();
			} else {
				tb_remove();
			}
		});
	}

	function init(widget_el, is_cloned) {

		var _input = $(widget_el).find('p.mt-iconpicker > input[type="hidden"]'),
				_value = _input.val();

		$(widget_el).find('#mt-icon-wrap > .mt-icon-remove').click( function(){
			iToggle( widget_el, 'hide', null );
		});

		$(widget_el).find('#mt-icon-wrap > .mt-icon-select').click( function(){
			if ( wp && wp.customize ) {
				$( '#colorpickerwrap' ).show();
			} else {
				tb_show("Choose Icon", "#TB_inline?height=400&amp;width=600&amp;inlineId=colorpickerwrap");
			}
			on_popup_show( widget_el );
		});
	}

	/**
	 * @param {jQuery.Event} e
	 * @param {jQuery} widget_el
	 */
	function on_form_update(e, widget_el) {
		if ($(widget_el).find('p.mt-iconpicker').length) {
			init(widget_el, 'widget-added' === e.type);
		}
	}

	$(document).on('widget-updated', on_form_update);
	$(document).on('widget-added', on_form_update);

	$(document).ready( function (){
		$('.widget:has(.mt-iconpicker)').each(function (){
			init($(this));
		});
	});

})(jQuery);
