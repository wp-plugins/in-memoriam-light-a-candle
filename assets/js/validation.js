/* ! candle submission form validation */

jQuery( function() {

	jQuery('#submit-candle').bind( 'click', function( event ) {
		
		jQuery('#add-candle input[required="required"], #add-candle textarea[required="required"]').each( function() {
		
			if( jQuery(this).val() == '' ) {
			
				alert( 'Please ensure all require fields are filled out' );
				
				event.preventDefault();
				return false;
			
			}
		
		});

	});

});