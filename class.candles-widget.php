<?php

// WP_Candle class

final class Candles_Widget extends WP_Widget {

	function __construct () {

		parent::__construct( false, 'Candle Widget' );

	}

	function widget ( $args, $instance ) {

		extract( $args );

		// Get candles to display
		$query_args = array(

			'post_type' => 'candle',
			'numberposts' => 1

		);

		if( is_numeric( $instance['candle_id'] ) )
			$query_args['include'] = $instance['candle_id'];
		else {

			if( is_array( $instance['candle_random_category'] ) )
			$query_args['candle_category'] = implode( ',', array_keys( $instance['candle_random_category'] ) );

			$query_args['orderby'] = 'rand';
		}

		$candles = get_posts( $query_args );

		// Widget title
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		echo $before_widget;

		if ( strlen( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		if ( $candles ) {

			foreach( $candles as $candle ) {
				$candle = new WP_Candle( $candle->ID );
				$candle->word_limit = isset( $instance['candle_word_limit'] ) ? $instance['candle_word_limit'] : 0;
			}

			$candle->render(true);
		}

		echo $after_widget;
	}

	function update ( $new_instance, $old_instance ) {

		$instance = $old_instance;

		if( !empty( $new_instance['candle_id'] ) )
			$instance['candle_id'] = $new_instance['candle_id'];

		if( !empty( $new_instance['title'] ) )
			$instance['title'] = $new_instance['title'];

		$instance['candle_word_limit'] = isset( $new_instance['candle_word_limit'] ) ? $new_instance['candle_word_limit'] : 0;
		$instance['candle_random_category'] = $new_instance['candle_random_category'];


		return $instance;

	}

	function form ( $instance ) {

		$defaults = array(

			'title' => '',
			'candle_id' => 'random',
			'candle_word_limit' => NULL,
			'candle_random_category' => ''

		);

		$instance = wp_parse_args( (array)$instance, $defaults );

		?>

		<p>
			<label for="candles_title"><?php _e( 'Title', 'in-memoriam-light-a-candle' ); ?>:</label>
			<input type="text" id="candles_title" name="<?php echo $this->get_field_name( 'title' ) ?>" style="width: 100%;" value="<?php echo $instance['title'] ?>" />
		</p>

		<p>
			<label for="candle_id"><?php _e( 'Select a Candle to display', 'in-memoriam-light-a-candle' ); ?></label>
			<select class="candle_widget_select" name="<?php echo $this->get_field_name( 'candle_id' ); ?>" style="width:100%;">
				<option value="random"><?php _e( 'Random', 'in-memoriam-light-a-candle' ); ?></option>

				<?php if( $candles = get_posts( array( 'post_type' => 'candle', 'numberposts' => -1 ) ) ) :?>

				<?php foreach( $candles as $candle ): ?>
				<option value="<?php echo esc_attr( $candle->ID ); ?>"<?php echo ( $instance['candle_id'] == $candle->ID ? ' selected="selected"' : NULL ); ?>><?php echo $candle->post_title; ?></option>
				<?php endforeach; ?>

				<?php endif; ?>
			</select>
		</p>

		<p>
			<label for="candle_word_limit"><?php _e( 'Word limit (optional)', 'in-memoriam-light-a-candle' ); ?></label>
			<input type="Text" name="<?php echo $this->get_field_name( 'candle_word_limit' ); ?>" style="width:100%;" value="<?php echo $instance['candle_word_limit']; ?>" />
		</p>

		<div class="candle_random_category">

			<p><?php _e( 'If random, get from specific category (optional)', 'in-memoriam-light-a-candle' ); ?></p>

			<p>
				<?php if( $categories = get_terms( 'candle_category', array( 'hide_empty' => false ) ) ) foreach( $categories as $category ): $id = uniqid(); ?>
				<input id="project-category-<?php echo $category->slug . '-' . $id; ?>" class="checkbox" type="checkbox" name="<?php echo $this->get_field_name( 'candle_random_category' ); ?>[<?php echo $category->slug; ?>]"<?php echo ( ( is_array( $instance['candle_random_category'] ) && array_key_exists( $category->slug, $instance['candle_random_category'] ) ) ? ' checked="checked"' :  NULL ); ?>></input><label for="project-category-<?php echo $category->slug . '-' . $id; ?>"><?php echo $category->name; ?></label><br />
				<?php endforeach; ?>
			</p>

		</div>

		<br />

		<?php

	}

}

?>
