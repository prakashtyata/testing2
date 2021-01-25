<?php

// Add term page
function dvin_rfq_taxonomy_add_meta_field() {

    global $dvin_wcql_settings;

    // this will add the custom meta field to the add new term page
		echo '<tr class="form-field"><th scope="row"><label for="description">'.__( 'Request a Quote Settings', 'dvinwcql' ).'</label></th><td><table cellpadding="10" cellspacing="3"><tr><td>';
	echo '<fieldset class="dvinfieldset"><legend><b>'.__( 'Request a Quote Button', 'dvinwcql' ).'</b></legend>';
    echo '<input type="radio" name="term_meta[rfq_button_settings]" value="" checked />'.__( 'Apply Parent Category Settings', 'dvinwcql' ).'<br/>';
	echo '<input type="radio" name="term_meta[rfq_button_settings]" value="show" />'. __( 'Show Button', 'dvinwcql' ).'<br/>';
	echo '<input type="radio" name="term_meta[rfq_button_settings]" value="hide" />'.__( 'Hide Button', 'dvinwcql' ).'<br/>';
	echo '</fieldset></td>';
	if($dvin_wcql_settings['link_position']!='Replace Add To Cart') {
	echo '</fieldset><td>';
		echo '<fieldset><legend><b>'.__( 'Add to Cart Button', 'dvinwcql' ).'</b></legend>';
        echo '<input type="radio" name="term_meta[rfq_button_settings]" value="" checked />'.__( 'Apply Parent Category Settings', 'dvinwcql' ).'<br/>';
	echo '<input type="radio" name="term_meta[cart_button_settings]" value="show"  />'. __( 'Show Button', 'dvinwcql' ).'<br/>';
	echo '<input type="radio" name="term_meta[cart_button_settings]" value="hide" />'.__( 'Hide Button', 'dvinwcql' ).'<br/>';

	echo '</fieldset></td>';
	}
	echo '</tr></table></td></tr>';
}
add_action( 'product_cat_add_form_fields', 'dvin_rfq_taxonomy_add_meta_field', 10, 2 );
// Edit term page
function dvin_rfq_taxonomy_edit_meta_field($term) {

	global $dvin_wcql_settings;
	// put the term ID into a variable
	$t_id = (int)$term->term_id;

	// retrieve the existing value(s) for this meta field. This returns an array
	$term_meta = get_option( "taxonomy_$t_id" );
	$rfq_button_set = $term_meta['rfq_button_settings'];
	$rfq_button_set_show = $rfq_button_set=="show"?"checked":"";
	$rfq_button_set_hide = $rfq_button_set=="hide"?"checked":"";
	$cart_button_set = $term_meta['cart_button_settings'];
	$cart_button_set_show = $cart_button_set=="show"?"checked":"";
	$cart_button_set_hide = $cart_button_set=="hide"?"checked":"";
	echo '<tr class="form-field"><th scope="row"><label for="description">'.__( 'Request a Quote Settings', 'dvinwcql' ).'</label></th><td><table cellpadding="10" cellspacing="3"><tr><td>';
	echo '<fieldset class="dvinfieldset"><legend><b>'.__( 'Request a Quote Button', 'dvinwcql' ).'</b></legend>';
    echo '<input type="radio" name="term_meta[rfq_button_settings]" value="" checked />'.__( 'Apply Parent Category Settings', 'dvinwcql' ).'<br/>';
	echo '<input type="radio" name="term_meta[rfq_button_settings]" value="show" '.$rfq_button_set_show.' />'. __( 'Show Button', 'dvinwcql' ).'<br/>';
	echo '<input type="radio" name="term_meta[rfq_button_settings]" value="hide" '.$rfq_button_set_hide.' />'.__( 'Hide Button', 'dvinwcql' ).'<br/>';
	echo '</fieldset></td>';
	if($dvin_wcql_settings['link_position']!='Replace Add To Cart') {
	echo '</fieldset><td>';
		echo '<fieldset><legend><b>'.__( 'Add to Cart Button', 'dvinwcql' ).'</b></legend>';
        echo '<input type="radio" name="term_meta[cart_button_settings]" value="" checked />'.__( 'Apply Parent Category Settings', 'dvinwcql' ).'<br/>';
	echo '<input type="radio" name="term_meta[cart_button_settings]" value="show" '.$cart_button_set_show.' />'. __( 'Show Button', 'dvinwcql' ).'<br/>';
	echo '<input type="radio" name="term_meta[cart_button_settings]" value="hide" '.$cart_button_set_hide.' />'.__( 'Hide Button', 'dvinwcql' ).'<br/>';

	echo '</fieldset></td>';
	}
	echo '</tr></table></td></tr>';
}
add_action( 'product_cat_edit_form_fields', 'dvin_rfq_taxonomy_edit_meta_field', 10, 2 );
// Save extra taxonomy fields callback function.
function dvin_rfq_save_taxonomy_custom_meta( $term_id ) {

	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = $_POST['term_meta'][$key];
			}
		}
		// Save the option array.
		update_option( "taxonomy_$t_id", $term_meta );
	}
}
add_action( 'edited_product_cat', 'dvin_rfq_save_taxonomy_custom_meta', 10, 2 );
add_action( 'create_product_cat', 'dvin_rfq_save_taxonomy_custom_meta', 10, 2 );
?>
