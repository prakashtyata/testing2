<?php
  /**
     * Single Product Custom Fields
     *     
     * @author 	Mikhalyuk Sergii
     * @package 	Avada-Child-Theme/WooCommerce/Templates
     * @version        1.0.0
     */
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }

    global $post, $product;
?>
<div class="product_custom_fields">


        <?php if(get_field('intended_use')): ?>
                        <h3> <?php _e('Intended Use','woocommerce'); ?> </h3>
                        <div><?php echo get_field('intended_use'); ?></div>
        <?php  endif; ?>
        
        <?php if(get_field('documents')): ?>
                        <h3 > <?php _e('Details','woocommerce'); ?> </h3>
                        <div><?php echo get_field('documents'); ?></div>
        <?php  endif; ?>
        
        
                     <h3 >  </h3>
                        <div></div>
        <?php if(get_field('ordering_information')): ?>
                        <h3 > <?php _e('Ordering Information','woocommerce'); ?> </h3>
                        <div><?php echo get_field('ordering_information'); ?></div>
        <?php  endif; ?>
      
        

       
</div>
