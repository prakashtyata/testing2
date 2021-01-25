<?php
/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function dvin_wcql_add_meta_box() {
	$screens = array( 'product' );
	foreach ( $screens as $screen ) {
		add_meta_box(
			'dvin_wcql_sectionid',
			__( 'Request a Quote Setting', 'dvinwcql' ),
			'dvin_wcql_meta_box_callback',
			$screen
		);
	}
}
add_action( 'add_meta_boxes', 'dvin_wcql_add_meta_box' );
/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function dvin_wcql_meta_box_callback( $post ) {
	global $dvin_wcql_settings;
	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'dvin_wcql_meta_box', 'dvin_wcql_meta_box_nonce' );
	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$rfq_button_set = get_post_meta( $post->ID, '_rfq_button_settings', true );
	$rfq_button_set_show = $rfq_button_set=="show"?"checked":"";
	$rfq_button_set_hide = $rfq_button_set=="hide"?"checked":"";
	$cart_button_set = get_post_meta( $post->ID, '_cart_button_settings', true );
	$cart_button_set_show = $cart_button_set=="show"?"checked":"";
	$cart_button_set_hide = $cart_button_set=="hide"?"checked":"";
	echo '<table cellpadding="10" cellspacing="3"><tr><td>';
	echo '<fieldset class="dvinfieldset"><legend><b>'.__( 'Request a Quote Button', 'dvinwcql' ).'</b></legend>';
	echo '<input type="radio" name="dvin_wcql_rfq_button_settings" value="" checked />'.__( 'Apply Category Settings', 'dvinwcql' ).'<br/>';
	echo '<input type="radio" name="dvin_wcql_rfq_button_settings" value="show" '.$rfq_button_set_show.' />'. __( 'Show Button', 'dvinwcql' ).'<br/>';
	echo '<input type="radio" name="dvin_wcql_rfq_button_settings" value="hide" '.$rfq_button_set_hide.' />'.__( 'Hide Button', 'dvinwcql' ).'<br/>';
	echo '</fieldset></td>';
	if($dvin_wcql_settings['link_position']!='Replace Add To Cart') {
	echo '</fieldset><td>';
		echo '<fieldset><legend><b>'.__( 'Add to Cart Button', 'dvinwcql' ).'</b></legend>';
	echo '<input type="radio" name="dvin_wcql_cart_button_settings" value="" checked  />'.__( 'Apply Category Settings', 'dvinwcql' ).'<br/>';
	echo '<input type="radio" name="dvin_wcql_cart_button_settings" value="show" '.$cart_button_set_show.' />'. __( 'Show Button', 'dvinwcql' ).'<br/>';
	echo '<input type="radio" name="dvin_wcql_cart_button_settings" value="hide" '.$cart_button_set_hide.' />'.__( 'Hide Button', 'dvinwcql' ).'<br/>';

	echo '</fieldset></td>';
	}
	echo '</tr></table>';
}
/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function dvin_wcql_save_meta_box_data( $post_id ) {
	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */
	// Check if our nonce is set.
	if ( ! isset( $_POST['dvin_wcql_meta_box_nonce'] ) ) {
		return;
	}
	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['dvin_wcql_meta_box_nonce'], 'dvin_wcql_meta_box' ) ) {
		return;
	}
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}
	/* OK, it's safe for us to save the data now. */

/*	// Make sure that it is set.
	if ( ! isset( $_POST['dvin_wcql_show_rfq_button'] ) ) {
		return;
	}
*/
	// Sanitize user input.
    $dvin_wcql_rfq_button_settings = $dvin_wcql_cart_button_settings  = '';
    if(isset($_POST['dvin_wcql_rfq_button_settings']))
	   $dvin_wcql_rfq_button_settings = sanitize_text_field( $_POST['dvin_wcql_rfq_button_settings'] );
    if(isset($_POST['dvin_wcql_cart_button_settings']))
	   $dvin_wcql_cart_button_settings = sanitize_text_field( $_POST['dvin_wcql_cart_button_settings'] );

	// Update the meta field in the database.
	update_post_meta( $post_id, '_rfq_button_settings', $dvin_wcql_rfq_button_settings );
	update_post_meta( $post_id, '_cart_button_settings', $dvin_wcql_cart_button_settings );
}
add_action( 'save_post', 'dvin_wcql_save_meta_box_data' );
?>
