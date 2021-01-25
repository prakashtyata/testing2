<?php
/**
 * WooCommerce Template Hooks
 *
 * Action/filter hooks used for WooCommerce functions/templates.
 *
 * @author 	Mikhalyuk Sergii
 * @category 	Core
 * @package 	Avada-Child-Theme/WooCommerce/Templates
 * @version            1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Product Summary Box.
 * 
 * @see woocommerce_template_single_custom_fields() 
 */
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_custom_fields', 35 );