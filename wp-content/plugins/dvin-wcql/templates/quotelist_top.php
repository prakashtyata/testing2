<?php
/**
 * Quotelist Page
 */
/*******************START ---- DO NOT TOUCH THIS CODE***********************/
 global $woocommerce,$dvin_wcql_settings, $current_user,$dvin_wcql_obj;
?>
<div id='dvin_message' align="left" style="display:none;color:red;">&nbsp;</div>
<div id="dvin_wcql_success_msg" style="display:none">
	<?php  echo apply_filters('dvin_wcql_ack_text',__('Sent email to Admin for the quote.','dvinwcql')); ?>
</div>
<?php if(wc_notice_count()>0) { ?>
<div id="dvin_wcql_details">
<?php 	wc_print_notices(); ?>
</div>
<?php
}
if(isset($_REQUEST['qlist_process']))
    return;
?>
<!--<div id="content">-->
<?php
//if using standard form then include the form tag
if(Dvin_Wcql::usingStandardForm()) {
?>
<form method="post"  class="qlist" action="">
<?php  } ?>
	<input type='hidden' name='qlist_process' value='true'/>
	<input type='hidden' name='action' value='send_request'/>
<?php /*************END ---- DO NOT TOUCH THIS CODE***********************/ ?>
<!-- STARTS LAYOUT --->
<?php

//determine whether to show proce column or not based on settings.
$remove_price_col = Dvin_Wcql::show_price_column();
$arr_cols = array('remove'=>'&nbsp;','thumbnail'=>'&nbsp;','name'=>'Product','price'=>'Price','sku'=>'SKU','quantity'=>'Quantity','subtotal'=>'Total'); //col and title in header

//to make it compatible with POEdit and othe translation plugins
$prod_str = __('Product','dvinwcql');
$price_str = __('Price','dvinwcql');
$qty_str = __('Quantity','dvinwcql');
$total_str = __('Total','dvinwcql');
$total_str = __('SKU','dvinwcql');

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

//get the final list of columns to be part of quotelist
 $arr_rows = array('dvin_wcql_grand_total_row','dvin_wcql_updatebutton_row');
// $arr_rows = array('dvin_wcql_grand_total_row');
 $arr_rows = apply_filters('dvin_qlist_rows',$arr_rows);
//loop through and include row actions
if(is_array($arr_rows) && count($arr_rows)>0) {
    $priority_index=0;
    foreach($arr_rows as $func_name){
        $priority_index++;
        add_action ( 'dvin_wcql_diplay_rows',$func_name,$priority_index,5);
    }
}
$grand_total_price = $grand_total_qty = 0; //grand total and quantity

$data_arr = Dvin_Wcql::getQuotelistData($qlist,$arr_cols,$cols_counter,$grand_total_price,$grand_total_qty);


//get the final list of data for the columns to be part of quotelist
$data_arr = apply_filters('dvin_qlist_cols_data',$data_arr);
?>
