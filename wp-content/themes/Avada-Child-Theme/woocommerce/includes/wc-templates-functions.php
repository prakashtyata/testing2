<?php

    /**
     * WooCommerce Template
     *
     * Functions for the templating system.
     *
     * @author   Mikhalyuk Sergii
     * @category Core
     * @package  WooCommerce/Functions
     * @version  2.5.0
     */
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }

    /**
     * Display product sub categories as thumbnails.
     *
     * @subpackage	Loop
     * @param array $args
     * @return null|boolean
     */
    if (!function_exists('aes_product_subcategories')) {

        function aes_product_subcategories($args = array())
        {

            $parentid = get_queried_object_id();

            $args = array(
                'hide_empty' => FALSE,
                'order' => 'DESC',
                'parent' => $parentid
            );

            $terms = get_terms('product_cat', $args);

            if ($terms) {

                foreach ($terms as $term) {
                    echo '<div class="category title">';
                    echo '<h3>';
//                echo '<a href="' . esc_url(get_term_link($term)) . '" class="' . $term->slug . '">';
                    echo $term->name;
//                echo '</a>';
                    echo '</h3>';
                    echo $term->description;
                    echo '</div>';
                }
            }
        }

    }
    
    if ( ! function_exists( 'woocommerce_template_single_custom_fields' ) ) {

	/**
	 * Output the product custom fields.
	 *
	 * @subpackage	Product
	 */
	function woocommerce_template_single_custom_fields() {
		wc_get_template( 'single-product/custom-fields.php' );
	}
}