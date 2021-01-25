<?php

global $dvin_wcql_link_positions;
?>
<div class="dvininterface">
	<!-- #dvin_header -->
	<div id="dvin_header">
		<div class="logo">
			<img src="<?php echo DVIN_QLIST_PLUGIN_WEBURL.'images/admin_logo.png';?>" alt="Dvin" />
		</div>
		<div class="panelinfo">
			<span class="themename"><?php echo $dvin_wcql_plugin_data['Name']; ?> Plugin</span>
			<span class="themename"><?php _e('Plugin Version','dvinwcql');?> : <?php  echo $dvin_wcql_plugin_data['Version']; ?></span>
	<div>
				<span><a href="http://limecuda.ticksy.com/articles/" target="_blank">Frequently Asked Questions</a>&nbsp;|&nbsp;<a href="<?php echo DVIN_QLIST_PLUGIN_WEBURL ?>documentation" target="_blank">Documentation</a>&nbsp;|&nbsp;<a href="http://limecuda.ticksy.com/" target="_blank">Raise support ticket</a>&nbsp;|&nbsp;<a href="<?php echo DVIN_QLIST_PLUGIN_WEBURL ?>change_log.txt" target="_blank">Changes</a></span>
			</div>
		</div>

	</div>
	<div id="dvin_content_wrap">
	 <?php
	 //if updated successfully , display the message
	 if(isset($_GET['updated']) && $_GET['updated']=='true'){
		echo "<span class='status'><h3><font color='green'>"._e('Updated Successfully','dvinwcql')."</font></h3></span>";
	 }
	?>
		<div id="dvinqlisttabs">
			<ul>
				<li><a href="#dvinqlisttabs-1"><?php echo __('Settings','dvinwcql');?></a></li>
				<li><a href="#dvinqlisttabs-2"><?php echo __('Email','dvinwcql');?></a></li>
                <li><a href="#dvinqlisttabs-3"><?php echo __('Form to Order Map Fields','dvinwcql');?></a></li>
				<!--<li><a href="#dvinqlisttabs-4"><?php echo __('Entries','dvinwcql');?></a></li>-->
			</ul>
			<div id="dvinqlisttabs-1">
				<?php Dvin_Wcql_Admin::dvin_settings_page(); ?>
			</div>
			<div id="dvinqlisttabs-2">
				<?php Dvin_Wcql_Admin::dvin_emailsettings_page(); ?>
			</div>
            <div id="dvinqlisttabs-3">
				<?php Dvin_Wcql_Admin::dvin_fields_mapping_page(); ?>
			</div>
		</div>
	</div>
</div>
