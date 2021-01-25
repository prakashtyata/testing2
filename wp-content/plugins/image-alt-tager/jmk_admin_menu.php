<?php
global $wpdb;
if ($wpdb){
$table = $wpdb->prefix."jmk_alt_tags";
	
	if($_POST['jmk_hidden'] == 'Y') {
		//Form data sent
		if (isset($_POST['Randomize'])) {
			$randomize = "Yes";
			$check = "checked";
		}else{
			$randomize = "No";
			$check = "";
		}
		
		if (isset($_POST['Content'])) {
			$ContentBox = "Yes";
			$check1 = "checked";
		}else{
			$ContentBox = "No";
			$check1 = "";
		}
		
		if (isset($_POST['Widgets'])) {
			$WidgetBox = "Yes";
			$check2 = "checked";
		}else{
			$WidgetBox = "No";
			$check2 = "";
		}
		
		$jmktags = $_POST['jmk_tags'];
			$wpdb->replace($table, array(
			"id" => "1",
			"jmk_randomize" => $randomize,
			"jmk_content" => $ContentBox,
			"jmk_widgets" => $WidgetBox,
			"jmk_tags" => $jmktags
		));
	?>

	<div class="updated"><p><strong><?php _e('Settings saved.' ); ?></strong></p></div>

	<?php

	}else{
		$id = 1;
		$result = $wpdb->get_row("SELECT * FROM $table WHERE ID = " . $id);
		if($result){
			foreach($result as $detail => $item){
				if($detail === 'jmk_tags'){
					$jmktags = $item;
				}
				if($detail === 'jmk_randomize'){
					$randomize = $item;
				}
				if($detail === 'jmk_content'){
					$ContentBox = $item;
				}
				if($detail === 'jmk_widgets'){
					$WidgetBox = $item;
				}
			}
			if($ContentBox == "Yes"){
				$check1 = "checked";
			}else{
				$check1 = "";
			}			

			if($WidgetBox == "Yes"){
				$check2 = "checked";
			}else{
				$check2 = "";
			}
			
			if($randomize == "Yes"){
				$check = "checked";
			}else{
				$check = "";
			}
		}
	}

?>

<div class="wrap">
    <?php    echo "<h2 style='border-bottom:1px solid #777;padding-bottom:17px;'>" . __( 'IMAGE ALT TAGER', 'jmk_trdom' ) . "</h2>"; ?>
    <p style="font-style:italic;">Follow the example bellow, and start adding the tags! After you press update, all of your images on website will have one of the ALT tags you specified.</br>
	Currently plugin will change image tags in your "content" and "widgets", will be implementing header soon and other options. :)
	</p>
	<div style="width:100%;">
		<form name="jmk_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
			<fieldset>
				<input type="hidden" name="jmk_hidden" value="Y">
				<?php    echo "<h4>" . __( 'ADD ALT TAGS IN A BOX BELOW', 'jmk_trdom' ) . "</h4>"; ?>
				<p><?php _e(' ex: "SEO service Manchester, Bespoke stationery Winchester, Plumbing services New Hampshire..."');?>
				</br><textarea style="width:600px; height:300px;" class="jmk_alt_tags" name="jmk_tags" ><?php echo $jmktags;?></textarea></p>
			</fieldset>
			<fieldset>
				<?php    echo "<h4>" . __( 'Select section/sections where you want your ALT tags changed', 'jmk_trdom' ) . "</h4>"; ?>
				<input style="margin-left: 4px;margin-top: 2px;" type="checkbox" name="Header" value="0" disabled><label for="header">Header</label> |
				<input style="margin-left: 10px;margin-top: 2px;" type="checkbox" name="Content" value="<?php echo $ContentBox; ?>" <?php echo $check1; ?>><label for="content">Content</label> |
				<input style="margin-left: 10px;margin-top: 2px;" type="checkbox" name="Widgets" value="<?php echo $WidgetBox; ?>" <?php echo $check2; ?>><label for="widgets">Widgets (currently only works with text widgets)</label>        
			</fieldset>
			<fieldset>
				<?php    echo "<h4 style='margin-top:23px;'>" . __( 'Randomize ALT tag placement', 'jmk_trdom' ) . "</h4>"; ?>
				<p style="margin-top:-19px;"><?php _e('Instead of adding/changing tags in sequence each of your ALT tag will have a randomized tag from your list"');?></p>
				<input style="margin-left:4px;margin-top:2px;" type="checkbox" name="Randomize" value="<?php echo $randomize;?>" <?php echo $check;?>><label for="randomize">Randomize (will be improving this)</label>
			</fieldset>
			<p class="submit">
				<input type="submit" name="Submit" value="<?php _e('Update Tags', 'jmk_trdom' ) ?>" />
			</p>
		</form>
	</div>
	<p style="border-top:1px solid #777;">This plugin was created by Justas Piliukaitis, if you noticed any errors or have any suggestions/requests please contact me <a href="mailto:j.piliukaitis@gmail.com">j.piliukaitis@gmail.com</a>.</p>
	<p style="margin-bottom: -2px;margin-top: 24px;">If you find this plugin useful, and you have money to spear, I will be very thankful for your donation. Thank you for using my plugin! :)</p>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
	<input type="hidden" name="cmd" value="_donations">
	<input type="hidden" name="business" value="j.piliukaitis@gmail.com">
	<input type="hidden" name="lc" value="LT">
	<input type="hidden" name="item_name" value="Img Alt Tager">
	<input type="hidden" name="no_note" value="0">
	<input type="hidden" name="currency_code" value="EUR">
	<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_SM.gif:NonHostedGuest">
	<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>
</div>

<?php
}else{
	echo '<p>No Wordpress Database($wpdb) Detected</p>';
}
?>