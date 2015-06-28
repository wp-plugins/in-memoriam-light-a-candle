<?php

// WP_Candle class

final class WP_Candle {

	/* Members */
	public $prayer;
	public $location;

	// Constructor
	public function __construct ( $post_id = null ) {

		if( !is_null( $post_id ) ) {

			$candle = self::get_instance( $post_id );
			$meta = get_post_meta( $candle->ID, '' );

			// Copy WP_Post public members
			foreach( $candle as $key => $value )
				$this->$key = $value;

			// Assign WP_Candle specific members
			$this->prayer = $meta['candle_prayer_name'][0];
			$this->location = $meta['candle_prayer_location_name'][0];

		}

	}

	/**
	 * Render a candle.
	 *
	 * @param string $context
	 * @return string
	 */
	public function render( $context = 'shortcode' ) {

		do_action( 'ct_before_render_candle', $this, $context );

		// Allow plugins/themes to completely filter how a candle is rendered.
		// If this filter returns 1 character or more, it will override the default render process
		$pre_render = apply_filters( 'ct_pre_render_candle', '', $this, $context );

		if ( strlen( $pre_render ) >= 1 ) {
			echo $pre_render;
		}
		else {

			ob_start();
			?>
			<div class="single-candle candle-<?php echo $this->ID; ?>">

				<blockquote>
                
                    <?php echo '<img style="float: left;" src="' . plugins_url( 'candle.jpg', __FILE__ ) . '" width="200" height="200"> '; ?>
                    
                    <h4><?php echo $this->post_title; ?></h4>
                    
                    <?php if( !empty( $this->location ) ): ?>
                    <p><?php echo $this->location; ?></p>
					<?php else: ?>
					<?php endif; ?>
					<i><?php

					if( isset( $this->word_limit ) && $this->word_limit > 0 ) {

						$words = explode( ' ', $this->post_content );
						echo implode( ' ',
							( count( $words ) <= $this->word_limit ? $words : array_slice( $words, 0, $this->word_limit ) )
						) . '... <a href="' . get_permalink( $this->ID ) . '">Read More</a>';

					}
					else echo $this->post_content;

					?></i>

				<?php echo $this->prayer; ?>
                
				</blockquote>

				<br clear="all" />

			</div>

			<?php

			// Allow plugins and themes to filter the default candle render markup
			echo apply_filters( 'ct_render_candle', ob_get_clean(), $this, $context );

		}

		do_action( 'ct_after_render_candle', $this, $context );

	}

	public static function get_instance ( $post_id ) {

		return WP_Post::get_instance( $post_id );

	}

}

?>
