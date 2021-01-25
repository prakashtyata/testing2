<?php
require('email_quotelist_template_top.php');

//include the top file
//$arr_cols = array('remove'=>'','thumbnail'=>'','name'=>'','price'=>'','sku'=>'','qty'=>'','subtotal'=>'');  defined in quotelist_top.php, you can overwrite by using the filter "dvin_qlist_cols"(written in quotelist_top.php)

//below is the table, you can change the layout as you wish
?>
<table cellspacing="1" cellpadding="1"  style="<?php echo $dvin_wcql_email_tbl_style; ?>">
	<thead>
		<tr style="<?php echo $dvin_wcql_email_tbl_hdr_style;?>">
            <?php
                foreach($arr_email_cols as $colname => $coltitle) { ?>
                <td><?php _e($coltitle,'dvinwcql');?></td>
            <?php } ?>
		</tr>
	</thead>
	<tbody>
	<?php
        //loop through the list
		foreach ( $data_arr as $cart_item_key => $cart_item ) {
        ?>
				<tr style="<?php echo $dvin_wcql_email_tbl_row_style;?>">
                <?php
                foreach($arr_email_cols as $colname => $coltitle) { ?>
                    <td style="<?php echo $dvin_wcql_email_tbl_cell_style;?>"><?php echo $cart_item[$colname];?></td>
                <?php } ?></tr>

        <?php
        }//end of loop

        //add or display rows
        do_action('dvin_wcql_email_diplay_rows',$arr_email_cols,$emailcols_counter,$grand_total_qty,$grand_total_price,$data_arr);
?>
	</tbody>
</table>
<?php require('email_quotelist_template_bottom.php'); ?>
