/*! light candles ajax scripts */

jQuery( function($) {

	// Rotate through random candles
	cycleCandle = function( attach_to, ajax_source, context, word_limit ) {

		setInterval( function() {

			var candle = $('.single-candle.candle-' + attach_to);

			if (typeof context === 'undefined') {
				context = 'shortcode';
			}

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: ajax_source,
				data: {
					action: 'get_random_candle', 
					context: context, 
					word_limit: word_limit
				},
				success: function(result) {

					$(result.data.markup).insertBefore(candle);

					candle.remove();
					
					attach_to = result.data.candle_id;
				}
			});

		}, 6000 );

	}

});
