<?php
require('quotelist_top.php');

//include the top file
//$arr_cols = array('remove'=>'','thumbnail'=>'','name'=>'','price'=>'','sku'=>'','qty'=>'','subtotal'=>'');  defined in quotelist_top.php, you can overwrite by using the filter "dvin_qlist_cols"(written in quotelist_top.php)

//below is the table, you can change the layout as you wish
?>
<?php do_action( 'woocommerce_before_cart_table' ); ?>
<div class="woocommerce-page">
<table class="shop_table shop_table_responsive cart" cellspacing="0">
	<thead>
		<tr>
            <?php
                foreach($arr_cols as $colname => $coltitle) { ?>
                <th class="product-<?php echo $colname;?>"><?php _e($coltitle,'dvinwcql');?></th>
            <?php } ?>
		</tr>
	</thead>
	<tbody>

	<?php
        //loop through the list
		foreach ( $data_arr as $cart_item_key => $cart_item ) {
        ?>
				<tr id="<?php echo $cart_item['row_id']?>" class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $qlist[$cart_item_key], $cart_item_key ) ); ?>">
                <?php
                foreach($arr_cols as $colname => $coltitle) { ?>
                    <td class="product-<?php echo $colname;?>" data-title="<?php echo $coltitle;?>"><?php echo $cart_item[$colname];?></td>
                <?php } ?>
				</tr>
        <?php
        }//end of loop

        //add or display rows
        do_action('dvin_wcql_diplay_rows',$arr_cols,$cols_counter,$grand_total_qty,$grand_total_price,$data_arr);
?>
	</tbody>
</table>
<?php require('quotelist_bottom.php'); ?>
</div>
