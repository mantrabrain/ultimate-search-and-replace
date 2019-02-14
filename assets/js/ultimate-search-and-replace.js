(function( $ ) {
	'use strict';

	/**
	 * Initializes our event handlers.
	 */
	function usar_init() {
		usar_search_replace();
		usar_update_sliders();
	}

	/**
	 * Recursive function for performing batch operations.
	 */
	function usar_process_step( action, step, page, data ) {

		$.ajax({
			type: 'POST',
			url: usar_object_vars.endpoint + action,
			data: {
				usar_ajax_nonce : usar_object_vars.ajax_nonce,
				action: action,
				usar_step: step,
				usar_page: page,
				usar_data: data
			},
			dataType: 'json',
			success: function( response ) {

				// Maybe display more details.
				if ( typeof response.message != 'undefined' ) {
					$('.usar-description').remove();
					$('.usar-progress-wrap').append( '<p class="description usar-description">' + response.message + '</p>' );
				}

				if ( 'done' == response.step ) {

					usar_update_progress_bar( '100%' );

					// Maybe run another action.
					if ( typeof response.next_action != 'undefined' ) {
						usar_update_progress_bar( '0%', 0 );
						usar_process_step( response.next_action, 0, 0, response.usar_data );
					} else {
						$('.usar-processing-wrap').remove();
						$('.usar-disabled').removeClass('usar-disabled button-disabled' );
						window.location = response.url;
					}

				} else {
					usar_update_progress_bar( response.percentage );
					usar_process_step( action, response.step, response.page, response.usar_data );
				}

			}
		}).fail(function (response) {
			$('.usar-processing-wrap').remove();
			$('.usar-disabled').removeClass('usar-disabled button-disabled' );
			$('#usar-error-wrap').html( '<div class="error"><p>' + usar_object_vars.unknown + '</p></div>' );
			if ( window.console && window.console.log ) {
				console.log(response);
			}
		});

	}

	/**
	 * Initializes a search/replace.
	 */
	function usar_search_replace() {

		var search_replace_submit = $( '#usar-submit' );
		var usar_error_wrap = $( '#usar-error-wrap' );
		search_replace_submit.click( function( e ) {

			e.preventDefault();

			if ( ! search_replace_submit.hasClass( 'button-disabled' ) ) {

				if ( ! $( '#search_for' ).val() ) {
					usar_error_wrap.html( '<div class="error"><p>' + usar_object_vars.no_search + '</p></div>' );
				} else if ( ! $( '#usar-table-select' ).val() ) {
					usar_error_wrap.html( '<div class="error"><p>' + usar_object_vars.no_tables + '</p></div>' );
				} else {
					var str 	= $( '.usar-action-form' ).serialize();
					var data 	= str.replace(/%5C/g, "#USAR_BACKSLASH#" );

					usar_error_wrap.html('');
					search_replace_submit.addClass( 'usar-disabled button-disabled' );
					$( '#usar-submit-wrap' ).append('<div class="usar-processing-wrap"><div class="spinner is-active usar-spinner"></div><div class="usar-progress-wrap"><div class="usar-progress"></div></div></div>');
					$('.usar-progress-wrap').append( '<p class="description usar-description">' + usar_object_vars.processing + '</p>' );
					usar_process_step( 'process_search_replace', 0, 0, data );
				}

			}

		});

	}

	/**
	 * Updates the progress bar for AJAX bulk actions.
	 */
	function usar_update_progress_bar( percentage, speed ) {
		if ( typeof speed == 'undefined' ) {
			speed = 150;
		}
		$( '.usar-progress' ).animate({
			width: percentage
		}, speed );
	}

	/**
	 * Updates the "Max Page Size" slider.
	 */
	function usar_update_sliders( percentage ) {
		$('#usar-page-size-slider').slider({
			value: usar_object_vars.page_size,
			range: "min",
			min: 1000,
			max: 50000,
			step: 1000,
			slide: function( event, ui ) {
				$('#usar-page-size-value').text( ui.value );
				$('#usar_page_size').val( ui.value );
			}
		});
	}

	usar_init();

})( jQuery );
