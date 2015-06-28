<?php
/*
Shortcode handlers
*/

// Handler for "candle" shortcode.
// This shortcode is used to output a single candle
function shortcode_candle ( $atts ) {

	if( !isset( $atts['id'] ) )
		return;

	$args = array(

		'post_type' => 'candle',
		'posts_per_page' => 1

	);

	if( isset( $atts['category'] ) ) {

		$category = $atts['category'];

		if( is_numeric( $category ) )
		$category = get_term_by( 'id', $category, 'candle_category' )->slug;

		$args['candle_category'] = $category;

	}

	if( is_numeric( $atts['id'] ) )
		$args['include'] = $atts['id'];
	elseif( $atts['id'] == 'random' )
		$args['orderby'] = 'rand';

	if( $candles = get_posts( $args ) ) {

		ob_start();

		$candle = new WP_Candle( array_pop( $candles )->ID );
		$candle->word_limit = ( isset( $atts['word_limit'] ) && is_numeric( $atts['word_limit'] ) ? $atts['word_limit'] : -1 );
		$candle->render();

		$output = ob_get_contents();
		ob_end_clean();

		if( isset( $atts['cycle'] ) && $atts['cycle'] ) {

			$output = '<script type="text/javascript" src="' . plugins_url( 'assets/js/ajax.js', dirname( __FILE__ ) ) . '"></script>' .
								sprintf( '<script type="text/javascript">jQuery(document).ready( function() { cycleCandle(%s, "%s"); });</script>', $candle->ID, admin_url( 'admin-ajax.php' ) ) .
								$output;

		}

		return $output;

	}

}
add_shortcode( 'candle', 'shortcode_candle' );

// Handler for "candles" shortcode.
// This shortcode is used to output multiple candles from a candle_category term
function shortcode_candles ( $atts ) {

	$args = array(

		'posts_per_page' => isset( $atts['per_page'] ) ? $atts['per_page'] : 2,
		'paged' => get_query_var( 'paged' ),
		'order' => isset( $atts['order'] ) ? $atts['order'] : 'DESC',
		'orderby' => isset( $atts['orderby'] ) ? $atts['orderby'] : 'date',
		'post_type' => 'candle'

	);

	$output = '';

	if( isset( $atts['category'] ) ) {

		$ids 	= explode( ',', $atts['category'] );
		$slugs = array();

		foreach( $ids as $id ) {
			if( $term = get_term_by( 'id', $id, 'candle_category' ) ) {
				$slugs[] = $term->slug;
			}
		}

		$args['candle_category'] = implode( ',', $slugs );

	}

	if( query_posts( $args ) ) {

		if( have_posts() ) {

			ob_start();

			echo '<div class="candle-category">';

			while( have_posts( ) ) {

				the_post();

				$candle = new WP_Candle( get_the_ID() );
				$candle->word_limit = ( isset( $atts['word_limit'] ) && is_numeric( $atts['word_limit'] ) ? $atts['word_limit'] : -1 );
				$candle->render();

			}

			if( function_exists( 'wp_paginate' ) ) {

				wp_paginate();

			}
			else {

				global $wp_query;

				$big = 9999999;

				echo paginate_links( array(
					'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format' => '?paged=%#%',
					'current' => max( 1, get_query_var('paged') ),
					'total' => $wp_query->max_num_pages
				) );

			}

			echo '</div>';

			$output = ob_get_contents();
			ob_end_clean();

		}

		wp_reset_query();

		return $output;

	}

}
add_shortcode( 'candles', 'shortcode_candles' );

// Handler for "candle-submission-form" shortcode
// This shortcode outputs a form which visitors can use to submit a candle
function shortcode_candle_submission ( $atts ) {

	ob_start();

	if( isset( $_POST['candle-postback'] ) && wp_verify_nonce( $_POST['candle_nonce'], 'add-candle' ) ):

		// Require WordPress core functions we require for file upload
		if( !function_exists( 'media_handle_upload' ) ) {

			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );

		}

		// Build post array object
		$candle = array(

			'ID' => NULL,
			'post_content' => apply_filters( 'the_content', esc_textarea( $_POST['candle_description'] ) ),
			'post_name' => '',
			'post_type' => 'candle',
			'post_status' => 'draft',
			'post_title' => $_POST['candle_title']

		);

		// Only process captcha if it is not disabled by a filter
		$captcha = true;

		if( !apply_filters( 'ct_disable_captcha', false ) ) {

			// Ensure CAPTCHA passed
			require_once( trailingslashit( dirname( __FILE__ ) ) . 'recaptchalib.php' );

			$captcha = recaptcha_check_answer(

				'6LeCFegSAAAAACCQCRacwYkqX37CSKWtrowfkP9K',
				$_SERVER['REMOTE_ADDR'],
				isset( $_POST['recaptcha_challenge_field'] ) ? $_POST['recaptcha_challenge_field'] : '',
				isset( $_POST['recaptcha_response_field'] ) ? $_POST['recaptcha_response_field'] : ''

			)->is_valid;

		}

		// Insert new candle, if successful, update meta data
		if( ( $post_id = wp_insert_post( $candle, false ) ) && $captcha ) {

			// Cache candle post we just inserted
			$candle = get_post( $post_id );

			update_post_meta( $post_id, 'candle_prayer_name', sanitize_text_field( $_POST['candle_prayer_name'] ) );
			update_post_meta( $post_id, 'candle_prayer_location_name', sanitize_text_field( $_POST['candle_prayer_location_name'] ) );

			// If a category has been selected, update the object term
			if( '' != $_POST['candle_category_group'] )
				wp_set_object_terms( $post_id, $_POST['candle_category_group'], 'candle_category' );

			// Send email notification to admin
			if( apply_filters( 'ct_send_new_candle_notification', true ) ) {

				$email = apply_filters( 'new_candle_notification_email', get_option( 'admin_email' ) );

				// Start output buffering and grab contents of email
				ob_start();

				include( trailingslashit( dirname( __FILE__) ) . 'templates/email-new-candle.php' );

				$html = ob_get_contents();
				ob_end_clean();

				// Prepare headers and send email

				$headers = array(
					'From: ' . sprintf( '%s <%s>', get_option( 'blogname' ), $email ),
					'Content-type: text/html',
					'Reply-to: ' . $email
				);

				if( apply_filters( 'new_candle_notification', true ) )
				wp_mail( $email, 'New Candle | ' . get_option( 'blogname' ), $html, $headers );

			}

			echo sprintf( '<p>%s</p>', apply_filters( 'new_candle_confirmation_message', __( 'We successfully received your candle. If approved, it will appear on our website. Thank you!', 'in-memoriam-light-a-candle' ) ) );

		}
		else {
			echo sprintf( '<p class="error">%s</p>', apply_filters( 'new_candle_failure_message', __( 'Sorry, but there was a problem with submitting your candle. Please ensure all required fields have been supplied and that you entered the CAPTCHA code correctly.', 'in-memoriam-light-a-candle' ) ) );
		}

	else:
	?>

	<script src="<?php echo plugins_url( 'assets/js/validation.js', dirname( __FILE__ ) ); ?>"></script>
	<script type="text/javascript">
		var RecaptchaOptions = {
			theme: '<?php echo apply_filters( 'candle_submission_captcha_theme', 'clean' ); ?>'
		}
	</script>

	<form id="add-candle" enctype="multipart/form-data" name="add-candle" method="POST" action="<?php the_permalink(); ?>">

		<label for="candle_title"><?php _e( 'Praying For:' ,'in-memoriam-light-a-candle' ); ?> </label><br />
		<input type="text" maxlength="35" size="35" name="candle_title" required="required"/><br /><br />
        
        <label for="candle_prayer_location_name"><?php _e( 'Location (optional):', 'in-memoriam-light-a-candle' ); ?></label><br />
		<input type="text" maxlength="35" size="35" name="candle_prayer_location_name" id="candle_prayer_location_name" /><br /><br />

		<label for="candle_description"><?php _e( 'Prayer Intention:', 'in-memoriam-light-a-candle' ); ?></label><br />
		<textarea  maxlength="350" name="candle_description" rows="10" cols="100%" required="required"></textarea><br /><br />

		<label for="candle_category_group"><?php _e( 'Category (optional):', 'in-memoriam-light-a-candle' ); ?></label><br />
		<select name="candle_category_group">

			<option value=""><?php _e( 'None', 'in-memoriam-light-a-candle' ); ?></option>

			<?php if( $terms = get_terms( 'candle_category', array( 'hide_empty' => false ) ) ): ?>

				<?php foreach( $terms as $term ): ?>
				<option value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></option>
				<?php endforeach; ?>

			<?php endif; ?>

		</select><br /><br />

		<label for="candle_prayer_name"><?php _e( 'Your Name:', 'in-memoriam-light-a-candle' ); ?></label><br />
		<input type="text" maxlength="35" size="35" name="candle_prayer_name" id="candle_prayer_name" required="required"/><br /><br />

		<!-- hidden postback test field and nonce -->
		<input type="hidden" name="candle-postback" value="true" />
		<input type="hidden" name="candle_nonce" value="<?php echo wp_create_nonce( 'add-candle' ); ?>" />

		<?php

		if( !apply_filters( 'ct_disable_captcha', false ) ) {

			// Output captcha field if it is not disabled
			require_once( trailingslashit( dirname( __FILE__ ) ) . 'recaptchalib.php' );
			echo recaptcha_get_html('6LeCFegSAAAAANwMXR-_3SOkC6POym9IVa2HgWCO');

		}

		?>

		<input type="submit" id="submit-candle" value="<?php _e( 'Submit Candle', 'in-memoriam-light-a-candle' ); ?>" />

	</form>

	<?php

	endif;

	$content = ob_get_contents();
	ob_end_clean();

	return $content;

}
add_shortcode( 'candle-submission-form', 'shortcode_candle_submission' );

?>
