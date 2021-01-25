<?php

//determine whether to show proce column or not based on settings.
$remove_price_col = Dvin_Wcql::show_price_column();
$arr_cols = array('remove'=>'&nbsp;','thumbnail'=>'&nbsp;','name'=>'Product','price'=>'Price','sku'=>'SKU','quantity'=>'Quantity','subtotal'=>'Total'); //col and title in header

if($remove_price_col) {
    unset($arr_cols['price']);
    unset($arr_cols['subtotal']);
    
    
}

                                        
if(isset($dvin_wcql_settings['no_qty'] ) && $dvin_wcql_settings['no_qty'] == 'on')
    unset($arr_cols['quantity']);
                                        
if($dvin_wcql_settings['show_sku_col'] !=  'on')
    unset($arr_cols['sku']);

     
//get the final list of columns to be part of quotelist
 $arr_cols = apply_filters('dvin_qlist_cols',$arr_cols);
$cols_counter = count($arr_cols); //it calculates the columns   
    
$data_arr = Dvin_Wcql::getQuotelistData($qlist,$arr_cols,$cols_counter,$grand_total_price,$grand_total_qty);

//get the final list of data for the columns to be part of quotelist
$data_arr = apply_filters('dvin_qlist_cols_data',$data_arr);

?>
<div class="woocommerce">
<ul class="cart_list product_list_widget">
<?php
if ( sizeof($data_arr)> 0) :
    foreach( $data_arr as $cart_item_key => $cart_item ) {
    ?>
          <li>
            <a href="<?php echo $cart_item['product-link']; ?>">
                <?php echo str_replace( array( 'http:', 'https:' ), '', $cart_item['thumbnail'] ) . $cart_item['name']; ?>
            </a>
                      <?php
        if(isset($dvin_wcql_settings['no_price'] ) && $dvin_wcql_settings['no_price'] != 'on') {
    echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $qlist[$cart_item_key]['quantity'], $cart_item['price'] ) . '</span>');
    }
    ?>
          </li>
    <?php } ?>
    <p class="buttons">

		<a href="<?php echo Dvin_Wcql::get_url(); ?>" class="button wc-forward minicart-button"><?php echo apply_filters('dvin_wcql_viewquotelist',__('View Quote List','dvinwcql')); ?></a>
	</p>
    <?php else : ?>
		<li class="empty"><?php _e( 'No products in the list', 'dvinwcql' ); ?></li>
	<?php endif; ?>
    </ul>
</div>