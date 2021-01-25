<?php

 global $woocommerce,$dvin_wcql_obj,$qlist,$style;

//if logged in, get current user id
if ( is_user_logged_in() )
    $current_user = get_user_by( 'id', get_current_user_id() );

    if(!isset($dvin_wcql_email_tbl_hdr_style))
     $dvin_wcql_email_tbl_hdr_style='';
?>
<div id="formtable">
    <p>
        <div id='dvin_messages' align="left" style="color:red">&nbsp;</div>
		<table>
		  <tr>
              <td colspan="5">
                  <div id="req_error_msgs"></div>
                  <table cellpadding="0" cellspacing="0">
                      <tr style="<?php echo $dvin_wcql_email_tbl_hdr_style;?>">
                          <td><font color="red">*</font><?php _e('Name','dvinwcql')?>:</td>
                          <td><input type="text" name="req_name" id="req_name" value="<?php if(isset($_POST['req_name'])) { echo htmlentities($_POST['req_name']);} else { if ( isset( $current_user ) ) echo $current_user->first_name.' '.$current_user->last_name;}?>"/></td>
                      </tr>
                      <tr>
                          <td><font color="red">*</font><?php _e('Email','dvinwcql')?>:</td>
                          <td><input type="text" name="req_email" id="req_email" value="<?php if(isset($_POST['req_email'])) { echo htmlentities($_POST['req_email']);} else { if ( isset( $current_user ) ) echo $current_user->user_email; }?>"/></td>
                      </tr>
                      <tr>
                          <td><?php _e('Additional details/comments','dvinwcql')?>:</td>
                          <td><textarea cols="40" rows="5" name="req_details" id="req_details"><?php if(isset($_POST['req_details'])) { echo $_POST['req_details'];}?></textarea></td>
                      </tr>
                      <tr>
                          <td>&nbsp;</td>
                          <td align="right" width="75%"><button type="button" class="button alt" onClick="call_ajax_submitform_to_admin('<?php echo DVIN_QLIST_PLUGIN_WEBURL.'dvin-wcql-ajax.php';?>')" <?php echo $style;?>> <?php echo apply_filters('dvin_wcql_req_a_quote_text',__('Request a Quote','dvinwcql'));?></button><img style="display: none;border:0; width:16px; height:16px;" src="<?php echo DVIN_QLIST_PLUGIN_WEBURL;?>images/ajax-loader.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="Working...">
                          </td>
                      </tr>
                  </table>
              </td>
            </tr>
    </table>
    </p>
</div>
