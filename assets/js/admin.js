
jQuery( function( $ ) {

	var WC_La_Poste_Tracking_items = {
	
		// init Class
		init: function() {
			$( '#la-poste-tracking-for-woocommerce' )
				.on( 'click', 'a.delete-tracking', this.delete_tracking )
				.on( 'click', 'button.button-show-form', this.show_form )
				.on( 'click', 'button.button-save-form', this.save_form );
		},
	
		// When a user enters a new tracking item
		save_form: function () {

			if ( !$( 'input#tracking_number' ).val() ) {
				return false;
			}

			$( '#la-poste-tracking-form' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			} );
			
			var data = {
				action:                   'WC_La_Poste_Tracking_save_form',
				order_id:                 woocommerce_admin_meta_boxes.post_id,
				tracking_number:          $( 'input#tracking_number' ).val(),
				date_shipped:             $( 'input#date_shipped' ).val(),
				security:                 $( '#WC_La_Poste_Tracking_create_nonce' ).val()
			};
			

			$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {
				$( '#la-poste-tracking-form' ).unblock();
				if ( response != '-1' ) {
					$( '#la-poste-tracking-form' ).hide();
					$( '#la-poste-tracking-for-woocommerce #tracking-items' ).append( response );
					$( '#la-poste-tracking-for-woocommerce button.button-show-form' ).show();
					$( 'input#tracking_number' ).val( '' );
					$( 'input#date_shipped' ).val( '' );
				}
			});

			return false;
		},
		
		// Show the new tracking item form
		show_form: function () {
			$( '#la-poste-tracking-form' ).show();
			$( '#la-poste-tracking-for-woocommerce button.button-show-form' ).hide();
		},
		
		// Delete a tracking item
		delete_tracking: function() {
	
			var tracking_id = $( this ).attr( 'rel' );
			
			$( '#tracking-item-' + tracking_id ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
	
			var data = {
				action:      'WC_La_Poste_Tracking_delete_item',
				order_id:    woocommerce_admin_meta_boxes.post_id,
				tracking_id: tracking_id,
				security:    $( '#WC_La_Poste_Tracking_delete_nonce' ).val()
			};
	
			$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {
				$( '#tracking-item-' + tracking_id ).unblock();
				if ( response != '-1' ) {
					$( '#tracking-item-' + tracking_id ).remove();
				}
			});
	
			return false;
		}
	}
	
	WC_La_Poste_Tracking_items.init();

} );
