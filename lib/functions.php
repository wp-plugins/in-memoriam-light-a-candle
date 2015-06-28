<?php
/*
Helper functions for In Memoriam (Light a Candle)
*/

function the_prayer () {

	global $post;
	
	return get_post_meta( $post->ID, 'candle_prayer_name', true );

}

function get_the_prayer ( $candle_id ) {

	return get_post_meta( $candle_id, 'candle_prayer_name', true );

}

function the_location () {

	global $post;
	
	return get_post_meta( $post->ID, 'candle_prayer_location', true );

}

function get_the_location ( $candle_id ) {

	return get_post_meta( $candle_id, 'candle_prayer_location', true );

}

?>