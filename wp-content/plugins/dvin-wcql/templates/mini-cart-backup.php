<?php
do_action( 'woocommerce_before_mini_quotelist' );
?>
<div class="woocommerce">
<ul class="cart_list product_list_widget">
	<?php if ( sizeof($qlist)> 0) :
			foreach ($qlist as $key => $values) {
					if($key =='') continue;


            //check for validity of the entry/product
			if(!isset($values['product_id']) && !isset($values['variation_id']))
				continue;
			//initialize to avoid notices
			if(!isset($values['variation_id']))
						$values['variation_id']=0;
			if (isset($values['product_id']) && isset($values['variation_id'])) {
				if(isset($values['product_id']))
					$values['prod_id'] = $values['ID'] = $values['product_id'];
				if(!empty($values['variation_id']))
					$values['ID'] = $values['variation_id'];
			} else{
				$values['prod_id'] = $values['ID'] = $values['product_id'];
				$values['ID'] =  $values['product_id'];
			}
			$_product = !empty($values['variation_id'])?get_product($values['variation_id'],array('parent_id'=>$values['product_id'])): get_product($values['prod_id']);
                if ( $_product && $_product->exists()) {
					$product_name  = apply_filters( 'woocommerce_cart_item_name', $_product->get_title());
					$thumbnail     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image());


                    //get price before showing the addon data as proce could change
                    if(!isset($values['price']))
                        $values['price'] = get_quotelist_product_price( $_product );
                    $temp_arr = array();
                    if(class_exists('Product_Addon_Cart')) {
 $addon_cart_obj = new Product_Addon_Cart();
                    $temp_arr = dvin_wcql_get_add_on_list($addon_cart_obj,$values['price'], $_product, $values);
                    }

                    $addons_str = wc_get_formatted_variation($temp_arr,false);
					$price_in_html = apply_filters( 'woocommerce_cart_product_price', wc_price( $values['price'] ), $_product );
					$price_in_html = apply_filters( 'woocommerce_cart_item_price',$price_in_html,'','');
					?>
					<li>
					<?php if ( ! $_product->is_visible() ) { ?>
						<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . $product_name; ?>
					<?php } else { ?>
						<a href="<?php echo esc_url( get_permalink(apply_filters('woocommerce_in_cart_product', $values['prod_id']))) ?>">
							<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . $product_name; ?>
                        </a>
					<?php } ?>
						<?php 	if(!empty($values['variation_id'])):
									echo wc_get_formatted_variation(unserialize($values['variation_data']),false);
								endif; ?>
						<?php

                                echo $addons_str;//addon info
								if(isset($dvin_wcql_settings['no_price'] ) && $dvin_wcql_settings['no_price'] != 'on') {
echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $values['quantity'], $price_in_html ) . '</span>'); ?>
					<?php
					}a
				}
		}
	?>
	</li>
	<p class="buttons">

		<a href="<?php echo Dvin_Wcql::get_url(); ?>" class="button wc-forward minicart-button"><?php echo apply_filters('dvin_wcql_viewquotelist',__('View Quote List','dvinwcql')); ?></a>
	</p>
	<?php else : ?>
		<li class="empty"><?php _e( 'No products in the list', 'dvinwcql' ); ?></li>
	<?php endif; ?>
</ul><!-- end product list -->
</div>
