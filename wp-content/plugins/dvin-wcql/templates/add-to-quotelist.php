<?php

/** Add the quotelist button/link to the single product page based on the selction of the position **/
global $dvin_wcql_settings,$dvin_wcql_link_positions;
//apply the filter
$dvin_wcql_link_positions = apply_filters( 'dvin_wcql_custom_link_positions' , $dvin_wcql_link_positions );
/** determine the position, if not shortcode and replace Add to Cart Button**/
if($dvin_wcql_link_positions[$dvin_wcql_settings['link_position']]['hook_name'] == "woocommerce_after_add_to_cart_button"){
    add_filter('woocommerce_is_purchasable','dvin_makeitpurchaseable');
	add_action($dvin_wcql_link_positions[$dvin_wcql_settings['link_position']]['hook_name'], 'dvin_remove_addtocart_button',1);
	add_action($dvin_wcql_link_positions[$dvin_wcql_settings['link_position']]['hook_name'], 'dvin_addquotelist_button',2);
}else if($dvin_wcql_link_positions[$dvin_wcql_settings['link_position']]['hook_name'] != "useshortcode") {
		add_action($dvin_wcql_link_positions[$dvin_wcql_settings['link_position']]['hook_name'], 'dvin_remove_addtocart_button',40);
	add_action($dvin_wcql_link_positions[$dvin_wcql_settings['link_position']]['hook_name'], 'dvin_addquotelist_button',$dvin_wcql_link_positions[$dvin_wcql_settings['link_position']]['priority']);
}
?>
