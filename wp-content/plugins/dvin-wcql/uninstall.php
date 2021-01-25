<?php
// If uninstall not called from WordPress exit
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
 exit ();
global $wpdb;require_once('dvin-woocommerce-quotelist.php');	// Delete option from options table
delete_option( 'dvin_wcql_settings');
delete_option( 'dvin_wcql_email_subject');
delete_option( 'dvin_wcql_email_msg');
delete_option( 'dvin_wcql_admin_email');
delete_option('dvin_wcql_admin_postfix_email');
delete_option('dvin_wcql_email_tbl_style');
delete_option('dvin_wcql_email_tbl_hdr_style');
delete_option('dvin_wcql_email_tbl_row_style');
delete_option('dvin_wcql_email_tbl_cell_style');
delete_option('dvin_quotelist_default_settings_set_or_not');
wp_delete_post(get_option('dvin_quotelist_pageid'),true);
delete_option( 'dvin_quotelist_pageid');//delete pages created for this plugin
?>
