<form id='mapfieldsform' name='mapfieldsform' method='POST'>
	<table class='widefat dvin_table' cellpadding='5' cellspacing='2' width='100%'>
        <tr>
            <td colspan='2' style='text-align:left;'><h1><?php _e('Mappping Fields','dvinwcql')?></h1></td>
        </tr>
        <tr>
            <td style='text-align:left;'><strong><?php _e('Order Field','dvinwcql')?></strong></td>
            <td style='text-align:left;'><strong><?php _e('Form Field','dvinwcql')?></strong></td>
        </tr>
        <?php foreach($address_fields as $field_key => $field_label) { ?>
        <tr>
            <td style='text-align:left;'><?php echo $field_label;?></td>
            <td style='text-align:left;'><input type='text' name='<?php echo $field_key;?>' value="<?php  if(isset($settings_arr['address_fields']["$field_key"])) echo htmlentities($settings_arr['address_fields']["$field_key"], ENT_QUOTES);?>"/></td>
        </tr>
        <?php } ?>
          <tr>
                <td>&nbsp;</td>
                <td>
						<center><input class='button-primary' type="submit" id="updatemapSettings" name="updatemapSettings" value="<?php _e('Map','dvinwcql');?>" /></center>
                </td>
        </tr>
    </table>
</form>
