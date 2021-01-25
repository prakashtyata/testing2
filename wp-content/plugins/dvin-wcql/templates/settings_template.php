<?php
global $dvin_wcql_link_positions;
?>
 <form id='settingsform' name='settingsform' method='POST'>
 <input type='hidden' name='action' id='action' value='preview_update'> <!--used for ajax call action -->
	<input type='hidden' name='plugin_weburl' id='plugin_weburl' value='<?php echo DVIN_QLIST_PLUGIN_WEBURL?>'>
		<table class='widefat dvin_table' cellpadding='5' cellspacing='2' width='100%'>
				<thead>
				<tr>
					<td><?php _e('Custom QuoteList URL','dvinwcql');?>:</td>
					<td><textarea id="dvin_wcql_quotelist_url" name="dvin_wcql_quotelist_url" rows="5" cols="60"><?php if(isset($dvin_wcql_quotelist_url)) echo $dvin_wcql_quotelist_url;?></textarea><br/>
<?php _e('You can provide custom QuoteList URL Page instead of using default provided by the plugin','dvinwcql')?></td>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td><input type='checkbox' name='show_on_shop'/></td>
					<td><?php _e('Show "Add to Quotelist" on Products list page','dvinwcql');?></td>
				</tr>
				<tr>
					<td><input type='checkbox' name='apply_individual_settings'/></td>
					<td><?php _e('Apply show/hide "Add to Quotelist" and "Add to Cart" buttons setting set at individual/category level','dvinwcql');?></td>
				</tr>
				<tr>
					<td><?php _e('Position','dvinwcql');?>:</td>
					<td><select name="link_position" id="link_position"  style="height:30px;">
					<?php
						foreach($dvin_wcql_link_positions as $key => $val) {?>
								<option value='<?php echo $key?>'><?php _e($key,'dvinwcql');?></option>
					<?php } ?>
								<option value='useshortcode'><?php _e('Use Shortcode','dvinwcql');?></option>
							</select>
					<br/><?php _e('If you want to position quotelist link on the single page --> Select position of "Add to Quotelist" link/button on single product page','dvinwcql');?></td>
				</tr>
				<tr>
					<td><?php _e('Redirect URL','dvinwcql');?>:</td>
					<td><textarea id="dvin_wcql_redirect_url" name="dvin_wcql_redirect_url" rows="5" cols="60"><?php if(isset($dvin_wcql_redirect_url)) echo $dvin_wcql_redirect_url;?></textarea><br/>
<?php _e('If you want to redirect, mention Redirect URL <strong>after submitting form</strong>, othewise acknowledgement text would be displayed.This is useful for conversions tracking.<b>This works only for Pluglin provided form NOT for third Party forms.','dvinwcql')?></td>
				</tr>
				<thead>
				<tr>
					<td colspan='2' style='text-align:left;'><h3><?php _e('Add to quotelist link settings','dvinwcql')?></h3></td>
				</tr>
				</thead>
				<tr>
					<td><input type='checkbox' name='link_sameas_addtocart'/></td>
					<td><?php _e('Show "Add to Quotelist" as a button instead of a link','dvinwcql')?></td>
				</tr>
					<tr>
					<td><input type='checkbox' name='link_sameas_addtocart_default_colors'/></td>
					<td><?php _e('Default to theme colors (or leave unchecked and select custom colors below)','dvinwcql')?></td>
				</tr>
				<tr>
					<td><?php _e('Change Background Color','dvinwcql');?>:</td>
					<td><input type="text" id="link_bgcolor" name="link_bgcolor" value="<?php echo $link_bgcolor;?>"  maxlength="7" />
					<div   id="link_bgcolorpicker"></div><br/>
					<?php _e('"Default to theme colors" must be unchecked','dvinwcql');?>
					</td>
				</tr>
				<tr>
					<td><?php _e('Change Font Color','dvinwcql');?>:</td>
					<td><input type="text" id="link_fontcolor" name="link_fontcolor" value="<?php echo $link_fontcolor;?>"  maxlength="7" />
					<div   id="link_fontcolorpicker"></div><br/>
					<?php _e('"Default to theme colors" must be unchecked','dvinwcql');?>
					</td>
				</tr>
				<tr>
					<td><input type='checkbox' name='redirect_to_listpage_afteradd'/></td>
					<td><?php _e('Redirect to Quote List Page after adding product to the list','dvinwcql')?></td>
				</tr>
				<tr>
					<td colspan='2' style='text-align:left;'><h3><?php _e('Other Settings','dvinwcql')?></h3></td>
				</tr>
				<tr>
					<td><input type='checkbox' name='no_price'/></td>
					<td><?php _e('Hide Price from Shop and Product Pages','dvinwcql');?></td>
				</tr>
				<tr>
					<td><input type='checkbox' name='no_quote_enabled_price'/></td>
					<td><?php _e('Hide Price from Shop and Product Pages when  "Add to Quotelist" button enabled','dvinwcql');?></td>
				</tr>
				<tr>
					<td><input type='checkbox' name='remove_price_col'/></td>
					<td><?php _e('Remove/Hide Price column from Request For Quote Page','dvinwcql')?></td>
				</tr>
				<tr>
					<td><input type='checkbox' name='show_price_login'/></td>
					<td><?php _e('Show Price when user logs in','dvinwcql');?></td>
				</tr>
				<tr>
					<td><input type='checkbox' name='no_qty'/></td>
					<td><?php _e('Remove/Hide Quantity from Product Pages and Request For Quote Page. It also hides the "UpdateList" button','dvinwcql')?></td>
				</tr>
                <tr>
					<td><input type='checkbox' name='show_sku_col'/></td>
					<td><?php _e('Show SKU column in Request For Quote Page','dvinwcql')?></td>
				</tr>
                <tr>
					<td><input type='checkbox' name='create_order'/></td>
					<td><?php _e('Creates Order (require Woocommerce 2.2+). Ensure that you mapped the fields(please find the tab)','dvinwcql')?></td>
				</tr>
                <tr>
					<td colspan='2' style='text-align:left;'><h3><?php _e('Column settings for Email','dvinwcql')?></h3></td>
				</tr>
                <tr>
					<td><input type='checkbox' name='add_sku_toemail'/></td>
					<td><?php _e('Add SKU column to email','dvinwcql')?></td>
				</tr>
                <tr>
					<td><input type='checkbox' name='add_price_toemail'/></td>
					<td><?php _e('Add Price column to email','dvinwcql')?></td>
				</tr>
				<tr>
					<td colspan='2' style='text-align:left;'><h3><?php _e('Use Third Party Forms and Email','dvinwcql')?></h3></td>
				</tr>
				<tr>
					<td colspan='2' style='text-align:left;'><span style="color:red;"><?php _e('Note:Form and Email setiings has to be  handled from Third Party Pluign itself.','dvinwcql')?></span></td>
				</tr>
				<tr>
					<td><input type='checkbox' name='use_gravity_forms'/></td>
					<td><?php _e('Use Gravity Forms','dvinwcql')?> - <select name='gravity_form_select' id='gravity_form_select'>
						<option value=''>Select Form</option>
						<?php
						if (class_exists('RGFormsModel')) {
							$forms = RGFormsModel::get_forms( null, 'title' );
						} else {
							$forms = array();
						}
						foreach($forms as $form) {?>
								<option value='<?php echo $form->id?>'><?php echo $form->title;?></option>
					<?php } ?>
					</select></td>
				</tr>
					<tr>
					<td><input type='checkbox' name='use_formidable_forms'/></td>
					<td><?php _e('Use Formidable Forms','dvinwcql')?> - <select name='formidable_form_select' id='formidable_form_select'>
						<option value=''>Select Form</option>
						<?php
						if (class_exists('FrmForm')) {
							$frm_form = new FrmForm();
					        $forms = $frm_form->getAll("is_template=0 AND status = 'published'", ' ORDER BY name');
						} else {
							$forms = array();
						}
						foreach($forms as $form) {?>
								<option value='<?php echo $form->id?>'><?php echo $form->name;?></option>
					<?php } ?>
					</select></td>
				</tr>
				<tr>
					<td><input type='checkbox' name='use_contactform7'/></td>
					<td><?php _e('Use ContactForm7','dvinwcql')?> - <select name='contactform7_form_select' id='contactform7_form_select'>
						<option value=''>Select Form</option>
						<?php
						if (class_exists('WPCF7_ContactForm')) {
							$forms = WPCF7_ContactForm::find();
						} else {
							$forms = array();
						}
						foreach($forms as $form) {
                        ?>
								<option value='<?php echo $form->id();?>'><?php echo $form->title();?></option>
					<?php } ?>
					</select></td>
				</tr>
				<!--<tr>
					<td colspan='2' style='text-align:left;'>
					<ul>
						<li>
						1)<?php _e('Please remove [dvin-wcql-listing] shortcode in "Request For Quote" Page.','dvinwcql')?>
						</li>
						<li>
						2)<?php _e('Must add [dvin-wcql-listing] shortcode as one of the HTML elements in Gravity Form where you want to show the listing.','dvinwcql')?>
						</li>
						<li>
						3)<?php _e('Must add hidden field name of "quotelisttable" with value of  "[quotelisttable]" shortcode as one of the HTML elements in Gravity Form.','dvinwcql')?>
						</li>
					</ul>
					</td>
				</tr>-->
                <tr>
					<td colspan='2' style='text-align:left;'>
					<ul>
                        <li> Please refer to <a href="<?php echo DVIN_QLIST_PLUGIN_WEBURL ?>documentation" target="_blank" style="color:blue"><strong><u>documentation</u></strong></a> for integrating the third party plugins as stated above.</li></ul>
					</td>
				</tr>
				<thead>
				<tr>
					<td colspan='2' style='text-align:left;'><h3><?php _e('Custom CSS','dvinwcql')?></h3></td>
				</tr>
				<tr>
					<td colspan='2' style='text-align:left;'><textarea cols="100%" rows="10" name="custom_css"><?php echo $custom_css;?></textarea><br/>
					<?php _e('Use addquotelistlink class to style the button/link e.g. for alignment, float:left, float:right etc.','dvinwcql');?>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<center><input class='button button-primary' type="submit" id="wcqlupdateSettings" name="wcqlupdateSettings" value="Update" />&nbsp;<input class='button button-secondary' type="submit" id="resettodefaultsettings" name="resettodefaultsettings" value="<?php _e('Reset to Default Settings','dvinwcql');?>" /></center>
					</td>
				</tr>
				</thead>
				<tbody>
		</table>


		<div id="lefttinycontainer">
			<div id="div_preview"></div>
		</div>
</form>
<!-- script to select the appropriate values in dropdown boxes -->
<script type='text/javascript'>
jQuery("#link_position").val('<?php echo $link_position;?>');
jQuery("#gravity_form_select").val('<?php echo $gravity_form_select;?>');
jQuery("#formidable_form_select").val('<?php echo $formidable_form_select;?>');
jQuery("#contactform7_form_select").val('<?php echo $contactform7_form_select;?>');
<?php
if( isset($link_sameas_addtocart) && $link_sameas_addtocart == "on") {?>
jQuery('input[name=link_sameas_addtocart]').attr('checked', true);
<?php }
if( isset($link_sameas_addtocart_default_colors) && $link_sameas_addtocart_default_colors == "on") {?>
jQuery('input[name=link_sameas_addtocart_default_colors]').attr('checked', true);
<?php }
if(isset($show_on_shop) && $show_on_shop == "on") {?>
jQuery('input[name=show_on_shop]').attr('checked', true);
<?php }
if(isset($remove_price_col) && $remove_price_col == "on") {?>
jQuery('input[name=remove_price_col]').attr('checked', true);
<?php }
if(isset($no_price) && $no_price == "on") {?>
jQuery('input[name=no_price]').attr('checked', true);
<?php }
 if(isset($show_price_login) && $show_price_login == "on") {?>
jQuery('input[name=show_price_login]').attr('checked', true);
<?php }
if(isset($use_gravity_forms) && $use_gravity_forms == "on") {?>
jQuery('input[name=use_gravity_forms]').attr('checked', true);
<?php }
if(isset($use_contactform7) && $use_contactform7 == "on") {?>
jQuery('input[name=use_contactform7]').attr('checked', true);
<?php }
if(isset($no_qty) && $no_qty == "on") {?>
jQuery('input[name=no_qty]').attr('checked', true);
<?php } if(isset($use_formidable_forms) && $use_formidable_forms == "on") {?>
jQuery('input[name=use_formidable_forms]').attr('checked', true);
<?php }
if(isset($add_sku_toemail) && $add_sku_toemail == "on") {?>
jQuery('input[name=add_sku_toemail]').attr('checked', true);
<?php } if(isset($add_price_toemail) && $add_price_toemail == "on") {?>
jQuery('input[name=add_price_toemail]').attr('checked', true);
<?php } if(isset($apply_individual_settings) && $apply_individual_settings == "on") {?>
jQuery('input[name=apply_individual_settings]').attr('checked', true);
<?php }
if(isset($no_quote_enabled_price) && $no_quote_enabled_price == "on") {?>
jQuery('input[name=no_quote_enabled_price]').attr('checked', true);
<?php } if(isset($redirect_to_listpage_afteradd) && $redirect_to_listpage_afteradd == "on") {?>
jQuery('input[name=redirect_to_listpage_afteradd]').attr('checked', true);
<?php } if(isset($create_order) && $create_order == "on") {?>
jQuery('input[name=create_order]').attr('checked', true);
<?php } if(isset($show_sku_col) && $show_sku_col == "on") {?>
jQuery('input[name=show_sku_col]').attr('checked', true);
<?php } ?>
</script>
