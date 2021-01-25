<?php

//determine whether to show proce column or not based on settings.
$remove_price_col = Dvin_Wcql::show_price_column_email($atts);
$arr_email_cols = array('thumbnail'=>'&nbsp;','name'=>'Product','price'=>'Price','sku'=>'SKU','quantity'=>'Quantity','subtotal'=>'Total'); //col and title in header

if($remove_price_col) {
    unset($arr_email_cols['price']);
    unset($arr_email_cols['subtotal']);
}

if(isset($dvin_wcql_settings['no_qty'] ) && $dvin_wcql_settings['no_qty'] == 'on')
    unset($arr_email_cols['qty']);

if($dvin_wcql_settings['add_sku_toemail'] !=  'on' && !isset($atts['add_sku_toemail']))
    unset($arr_email_cols['sku']);


//get the final list of columns to be part of quotelist
 $arr_email_cols = apply_filters('dvin_qlist_emailcols',$arr_email_cols);
$emailcols_counter = count($arr_email_cols); //it calculates the columns

//get the final list of columns to be part of quotelist
 $arr_email_rows = array('dvin_wcql_grand_total_row');
 $arr_email_rows = apply_filters('dvin_qlist_email_rows',$arr_email_rows);

//loop through and include row actions
if(is_array($arr_email_rows) && count($arr_email_rows)>0) {
    $priority_index=0;
    foreach($arr_email_rows as $func_name){
        $priority_index++;
        add_action( 'dvin_wcql_email_diplay_rows',$func_name,$priority_index,5);
    }
}

$grand_total_price = $grand_total_qty = 0; //grand total and quantity

$data_arr = Dvin_Wcql::getQuotelistData($qlist,$arr_email_cols,$emailcols_counter,$grand_total_price,$grand_total_qty,true);

//get the final list of data for the columns to be part of quotelist
$data_arr = apply_filters('dvin_qlist_email_cols_data',$data_arr);

?>
