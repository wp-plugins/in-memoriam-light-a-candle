<?php

// Core class for plugin functionality

final class Plugify_Light_Candles {

	public function __construct () {

		// Register actions
		add_action( 'init', array( __CLASS__, 'init' ) );
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
		add_action( 'widgets_init', array( __CLASS__, 'widgets_init' ) );
		add_action( 'wp_insert_post', array( __CLASS__, 'insert_candle' ), 10, 1 );
		add_action( 'manage_posts_custom_column', array( __CLASS__, 'candle_column' ), 10, 2 );

		// Filters for candle post type columns
		add_filter( 'manage_edit-candle_columns', array( __CLASS__, 'candle_columns' ), 5 );
		add_filter( 'manage_edit-candle_sortable_columns', array( __CLASS__, 'candle_sortable_columns' ), 5 );

		// Filter for candle category taxonomy columns
		add_filter( 'manage_edit-candle_category_columns', array( __CLASS__, 'candle_taxonomy_columns' ) );
		add_filter( 'manage_candle_category_custom_column', array( __CLASS__, 'candle_taxonomy_column' ), 10, 3 );

		// AJAX hooks
		add_action( 'wp_ajax_get_random_candle', array( __CLASS__, 'ajax_get_random_candle' ) );
		add_action( 'wp_ajax_nopriv_get_random_candle', array( __CLASS__, 'ajax_get_random_candle' ) );

		// Load textdomain
		$this->load_textdomain();
		
		// Install tasks
		register_activation_hook( trailingslashit( dirname( __FILE__ ) ) . 'in-memoriam-light-a-candle.php', array( &$this, 'install' ) );

	}

	public static function install () {

		// Store timestamp of when activation occured
		if( !get_option( 'ct_activated' ) ) {
			update_option( 'ct_activated', time() );
		}

	}

	/**
	* Load language files
	*
	* @since 1.5.1
	*
	* @return void
	*/
	public function load_textdomain() {

		// Set filter for plugin's languages directory
		$lang_dir = plugin_dir_path( __FILE__ ) . 'languages/';

		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale', get_locale(), 'in-memoriam-light-a-candle' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'in-memoriam-light-a-candle', $locale );

		// Setup paths to current locale file
		$mofile_local = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/in-memoriam-light-a-candle/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			load_textdomain( 'in-memoriam-light-a-candle', $mofile_global );
		}
		elseif ( file_exists( $mofile_local ) ) {
			load_textdomain( 'in-memoriam-light-a-candle', $mofile_local );
		}
		else {
			// Load the default language files
			load_plugin_textdomain( 'in-memoriam-light-a-candle', false, $lang_dir );
		}

	}

	public static function init () {

		/*≈=====≈=====≈=====≈=====≈=====≈=====≈=====
		Candle Post Type
		≈=====≈=====≈=====≈=====≈=====≈=====≈=====*/
		// Setup core dependencies
		$post_type_labels = array(
			'name' => __( 'Candles', 'in-memoriam-light-a-candle' ),
			'singular_name' => __( 'Candle', 'in-memoriam-light-a-candle' ),
			'add_new' => __( 'Add New', 'in-memoriam-light-a-candle' ),
			'add_new_item' => __( 'Add New Candle', 'in-memoriam-light-a-candle' ),
			'edit_item' => __( 'Edit Candle', 'in-memoriam-light-a-candle' ),
			'new_item' => __( 'New Candle', 'in-memoriam-light-a-candle' ),
			'view_item' => __( 'View Candle', 'in-memoriam-light-a-candle' ),
			'search_items' => __( 'Search Candles', 'in-memoriam-light-a-candle' ),
			'not_found' =>  __( 'No candle found', 'in-memoriam-light-a-candle' ),
			'not_found_in_trash' => __( 'No candle found in the trash', 'in-memoriam-light-a-candle' ),
			'parent_item_colon' => ''
		);

		// Register the post type
		$args = array(
				 'labels' => $post_type_labels,
				 'singular_label' => __( 'Candle', 'in-memoriam-light-a-candle' ),
				 'public' => true,
				 'show_ui' => true,
				 '_builtin' => false,
				 '_edit_link' => 'post.php?post=%d',
				 'capability_type' => 'post',
				 'hierarchical' => false,
				 'rewrite' => array( 'slug' => 'candle' ),
				 'query_var' => 'candle',
				 'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
				 'menu_position' => 5
			);
					register_post_type( 'candle', $args );
					flush_rewrite_rules();

		/*≈=====≈=====≈=====≈=====≈=====≈=====≈=====
		Candle Taxonomy
		≈=====≈=====≈=====≈=====≈=====≈=====≈=====*/
		// Register and configure Candle Category taxonomy
		$taxonomy_labels = array(
			'name' => __( 'Candle Categories', 'in-memoriam-light-a-candle' ),
			'singular_name' => __( 'Candle Category', 'in-memoriam-light-a-candle' ),
			'search_items' =>  __( 'Search Candle Categories', 'in-memoriam-light-a-candle' ),
			'all_items' => __( 'All Candle Categories', 'in-memoriam-light-a-candle' ),
			'parent_item' => __( 'Parent Candle Categories', 'in-memoriam-light-a-candle' ),
			'parent_item_colon' => __( 'Parent Candle Category', 'in-memoriam-light-a-candle' ),
			'edit_item' => __( 'Edit Candle Category', 'in-memoriam-light-a-candle' ),
			'update_item' => __( 'Update Candle Category', 'in-memoriam-light-a-candle' ),
			'add_new_item' => __( 'Add New Candle Category', 'in-memoriam-light-a-candle' ),
			'new_item_name' => __( 'New Candle Category', 'in-memoriam-light-a-candle' ),
			'menu_name' => __( 'Categories', 'in-memoriam-light-a-candle' )
	  );

		register_taxonomy( 'candle_category', 'candle', array(
				'hierarchical' => true,
				'labels' => $taxonomy_labels,
				'show_ui' => true,
				'query_var' => true,
				'rewrite' => array( 'slug' => 'candles' )
			)
		);

		// Ensure jQuery is enqueued
		wp_enqueue_script( 'jquery' );

		// Enqueue admin scripts
		if( is_admin() )
			wp_enqueue_script( 'ct_scripts', plugins_url( 'assets/js/scripts.js', __FILE__ ), array( 'jquery' ) );

	}

	public static function admin_init () {

		// Add metabox for candle meta
		add_meta_box( 'candle-details', 'Prayer Details', array( __CLASS__, 'candle_metabox' ), 'candle', 'normal', 'core' );

	}

	public static function admin_notices () {

		// Display donation prompt if CT has been installed for more than two weeks
		$installed = get_option( 'ct_activated' );

		if( time() >= ( $installed + ( 86400 * 14 ) ) && !get_option( 'ct_prompted' ) ) {
			echo '<div id="message" class="updated"><p>' . __( 'Loving In Memoriam (Light a Candle)? Help support development by <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BTBXYQ5JMC89J" target="_blank">buying us a coffee</a>, or leave a <a href="http://wordpress.org/support/view/plugin-reviews/in-memoriam-light-a-candle?filter=5" target="_blank">rating for us!</a> You\'ll never see this again, don\'t worry.', 'in-memoriam-light-a-candle' ) . '</p></div>';
			update_option( 'ct_prompted', 'yes' );
		}

	}

	public static function widgets_init () {

		register_widget( 'Candles_Widget' );

	}

	public static function candle_columns ( $columns ) {

		unset( $columns['date'] );
		$columns['candle_prayer_name'] = __( 'Name', 'in-memoriam-light-a-candle' );
		$columns['candle_prayer_location_name'] = __( 'Location', 'in-memoriam-light-a-candle' );
		$columns['candle_category'] = __( 'Category', 'in-memoriam-light-a-candle' );
		$columns['candle_shortcode'] = __( 'Shortcode', 'in-memoriam-light-a-candle' );

		$columns['date'] = __( 'Date', 'in-memoriam-light-a-candle' );

		return $columns;

	}

	public static function candle_sortable_columns ( $columns ) {

		$columns['candle_prayer_name'] = 'candle_prayer';
		$columns['candle_prayer_location_name'] = 'candle_prayer_location_name';

		return $columns;

	}

	public static function candle_column ( $column, $post_id ) {

		global $post;

		if( $post->post_type != 'candle' )
			return;

		switch( $column ) {

			case 'candle_category':

				$list = get_the_term_list( $post->ID, 'candle_category', null, ', ', null );
				echo $list == '' ? '<em>N/A</em>' : $list;

				break;

			case 'candle_shortcode':
				echo sprintf( '[candle id="%s"]', $post->ID );
				break;

			case 'candle_thumbnail':

				if( has_post_thumbnail( $post->ID ) )
					echo '<img src="' . plugins_url( 'candle.jpg', __FILE__ ) . '" width="200" height="200" align="left"> ';
				else
					echo '<img src="' . plugins_url( 'candle.jpg', __FILE__ ) . '" width="200" height="200" align="left"> ';

				break;

			default:

				$value = get_post_meta( $post->ID, $column, true );
				echo $value == '' ? '<em>N/A</em>' : $value;

		}

	}

	public static function candle_taxonomy_columns ( $columns ) {

		return array(

			'cb' => '<input type="checkbox" />"',
			'name' => __( 'Name', 'in-memoriam-light-a-candle' ),
			'shortcode' => __( 'Shortcode', 'in-memoriam-light-a-candle' ),
			'slug' => __( 'Slug', 'in-memoriam-light-a-candle' ),
			'posts' => __( 'Candles', 'in-memoriam-light-a-candle' )

		);

	}

	public static function candle_taxonomy_column ( $out, $column_name, $id ) {

		if( $column_name == 'shortcode' )
			return sprintf( '[candles category="%s"]', $id );

	}

	public static function candle_metabox ( $post ) {

		global $post;

		// Display Prayer Details form
		?>

		<table class="candle-prayer-details">

			<tr>
				<td valign="middle" align="left" width="125"><label for="candle_prayer_name"><?php _e( 'Prayer Name', 'in-memoriam-light-a-candle' ); ?></label></td>
				<td valign="middle" align="left" width="150"><input type="text" name="candle_prayer_name" value="<?php echo esc_attr( get_post_meta( $post->ID, 'candle_prayer_name', true ) ); ?>" />
				<td valign="middle" align="left"><em><?php _e( 'The name of the prayer lighting this candle', 'in-memoriam-light-a-candle' ); ?></em></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><label for="candle_prayer_location_name"><?php _e( 'Location Name', 'in-memoriam-light-a-candle' ); ?></label></td>
				<td valign="middle" align="left"><input type="text" name="candle_prayer_location_name" value="<?php echo esc_attr( get_post_meta( $post->ID, 'candle_prayer_location_name', true ) ); ?>" />
				<td valign="middle" align="left"><em><?php _e( 'Location, if any', 'in-memoriam-light-a-candle' ); ?></em></td>
			</tr>

		</table>

		<?php

	}

	public static function ajax_get_random_candle () {

		$candle = get_posts( array(

			'post_type' => 'candle',
			'posts_per_page' => 1,
			'orderby' => 'rand'

		) );

		if( $candle ) {

			$candle = new WP_Candle( $candle[0]->ID );
			$candle->word_limit = isset( $_POST['word_limit'] ) ? $_POST['word_limit'] : -1;

			ob_start();

			$candle->render();
			$markup = ob_get_contents();

			ob_end_clean();

			wp_send_json_success( array( 'markup' => $markup, 'candle_id' => $candle->ID ) );

		}
		else {
			wp_send_json_error();
		}

	}

	public static function insert_candle ( $post_id ) {

		global $post;

		if( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( isset( $_GET['action'] ) && $_GET['action'] == 'trash' ) )
			return;

		if( @$post->post_type != 'candle' )
			return;

		foreach( $_POST as $key => $value )
			if( strpos( $key, 'candle_' ) === 0 )
				update_post_meta( $post_id, $key, sanitize_text_field( $value ) );

	}

}

?>
