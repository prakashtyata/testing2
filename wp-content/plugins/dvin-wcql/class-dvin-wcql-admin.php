<?php

/**
 * Dvin_Wcql_Admin class, Handles the Admin Panel functionality of Quotelist
 */
class Dvin_Wcql_Admin {
/**
  * function init
  * @static
  */
	static function init() {

		//process the form, if submitted for update
		if(isset($_POST['wcqlupdateSettings'])) {
			$settings_arr = array();

			//get all settings
			$settings_arr = maybe_unserialize(get_option('dvin_wcql_settings'));
			$settings_arr['apply_individual_settings'] = isset($_POST['apply_individual_settings'])?'on':'';
			$settings_arr['link_position'] = $_POST['link_position'];
			$settings_arr['link_bgcolor'] = $_POST['link_bgcolor'];
			$settings_arr['link_fontcolor'] = $_POST['link_fontcolor'];
			$settings_arr['link_sameas_addtocart_default_colors'] = isset($_POST['link_sameas_addtocart_default_colors'])?'on':'';
			$settings_arr['link_sameas_addtocart'] = isset($_POST['link_sameas_addtocart'])?'on':'';
			$settings_arr['custom_css'] = $_POST['custom_css'];
			$settings_arr['show_quotelist_link_always'] = isset($_POST['show_quotelist_link_always'])?'on':'';
			$settings_arr['show_on_shop'] = isset($_POST['show_on_shop'])?'on':'';
			$settings_arr['no_price'] = isset($_POST['no_price'])?'on':'';
			$settings_arr['no_quote_enabled_price'] = isset($_POST['no_quote_enabled_price'])?'on':'';
			$settings_arr['show_price_login'] = isset($_POST['show_price_login'])?'on':'';
			$settings_arr['no_qty'] = isset($_POST['no_qty'])?'on':'';
			$settings_arr['remove_price_col'] = isset($_POST['remove_price_col'])?'on':'';
			$settings_arr['use_gravity_forms'] = isset($_POST['use_gravity_forms'])?'on':'';
			$settings_arr['gravity_form_select'] = $_POST['gravity_form_select'];
			$settings_arr['use_formidable_forms'] = isset($_POST['use_formidable_forms'])?'on':'';
			$settings_arr['formidable_form_select'] = $_POST['formidable_form_select'];
			$settings_arr['add_sku_toemail'] = isset($_POST['add_sku_toemail'])?'on':'';
      $settings_arr['add_price_toemail'] = isset($_POST['add_price_toemail'])?'on':'';
			$settings_arr['dvin_wcql_redirect_url'] = $_POST['dvin_wcql_redirect_url'];
			$settings_arr['redirect_to_listpage_afteradd'] = isset($_POST['redirect_to_listpage_afteradd'])?'on':'';
			$settings_arr['dvin_wcql_quotelist_url'] = $_POST['dvin_wcql_quotelist_url'];
			$settings_arr['use_contactform7'] = isset($_POST['use_contactform7'])?'on':'';
			$settings_arr['contactform7_form_select'] = $_POST['contactform7_form_select'];
            $settings_arr['create_order'] = isset($_POST['create_order'])?'on':'';
            $settings_arr['show_sku_col'] = isset($_POST['show_sku_col'])?'on':'';


			//update settings in database
			update_option('dvin_wcql_settings',maybe_serialize($settings_arr));

			//update the custom_styles file
			file_put_contents(DVIN_QLIST_PLUGIN_URL."css/custom_styles.css",$_POST['custom_css']);

			header('Location: ?page=dvinqlistmainpage&updated=true');
			exit;
		}
		//process the form, if submitted for update
		if(isset($_POST['updateEmailSettings'])) {
			if(get_magic_quotes_gpc()) {
				$_POST['dvin_wcql_email_subject'] = stripslashes($_POST['dvin_wcql_email_subject']);
				$_POST['dvin_wcql_email_msg'] =  stripslashes($_POST['dvin_wcql_email_msg']);
			}
			//save the settings
			update_option('dvin_wcql_email_subject',$_POST['dvin_wcql_email_subject']);
			update_option('dvin_wcql_email_msg',$_POST['dvin_wcql_email_msg']);
			update_option('dvin_wcql_admin_email',$_POST['dvin_wcql_admin_email']);
			update_option('dvin_wcql_copy_toreq', isset($_POST['dvin_wcql_copy_toreq'])?'on':'');
			update_option('dvin_wcql_email_tbl_style',$_POST['dvin_wcql_email_tbl_style']);
			update_option('dvin_wcql_email_tbl_hdr_style',$_POST['dvin_wcql_email_tbl_hdr_style']);
			update_option('dvin_wcql_email_tbl_row_style',$_POST['dvin_wcql_email_tbl_row_style']);
			update_option('dvin_wcql_email_tbl_cell_style',$_POST['dvin_wcql_email_tbl_cell_style']);
			update_option('dvin_wcql_admin_postfix_email',$_POST['dvin_wcql_admin_postfix_email']);


			header('Location: ?page=dvinqlistmainpage&updated=true');
			exit;
		}//end if

        //process the form

            if(isset($_POST['updatemapSettings'])) {
               unset($_POST['updatemapSettings']);

            //get all settings
			$settings_arr = maybe_unserialize(get_option('dvin_wcql_settings'));
            foreach($_POST as $key => $val) {
                $settings_arr['address_fields']["$key"] = $val;
            }

            update_option('dvin_wcql_settings',maybe_serialize($settings_arr));
            $dvin_wcql_settings = maybe_unserialize(get_option('dvin_wcql_settings'));
			header('Location: ?page=dvinqlistmainpage&updated=true');
			exit;
		}//end if

		//action to reset to default settings
		if(isset($_POST['resettodefaultsettings'])) {
			Dvin_Wcql_default_settings();
			header('Location: ?page=dvinqlistmainpage&updated=true');
			exit;
		}
	}
/**
  * function add_menu, adds menu items to the admin
  * @static
  */
	static function add_menu() {
		//create submenu items
		$dvinquotelist_page = add_submenu_page("woocommerce", __('Request a Quote','dvinwcql'), __('Request a Quote','dvinwcql'), "manage_options", "dvinqlistmainpage",array("dvin_wcql_Admin","dvin_quotelist_mainpage"));
		
		/* Using registered $page handle to hook script and style load */
		add_action("admin_print_scripts-{$dvinquotelist_page}",array("dvin_wcql_Admin", "dvin_admin_scripts"));
		add_action("admin_print_styles-{$dvinquotelist_page}",array("dvin_wcql_Admin", "dvin_admin_styles"));
	}
	/**
	* function dvin_quotelist_mainpage, manages the admin mainpage
	* @static
	*/
	static function dvin_quotelist_mainpage() {

		//get plugin data
		$dvin_wcql_plugin_data    = get_plugin_data( dirname(__FILE__).'/dvin-woocommerce-quotelist.php');
		//now include the template
		include('templates/admin_template.php');
	}
	/**
	* function dvin_settings_page, manages settings
	* @static
	*/
	static function dvin_settings_page() {
		//retrieve from db
		$settings_arr = maybe_unserialize(get_option('dvin_wcql_settings'));
		extract($settings_arr);
		//now include the template
		include('templates/settings_template.php');
	}
	/**
	* function dvin_emailsettings_page, manages email settings
	* @static
	*/
	static function dvin_emailsettings_page() {
		//retrieve from db
		$settings_arr = maybe_unserialize(get_option('dvin_wcql_settings'));
		extract($settings_arr);
		$dvin_wcql_email_subject = get_option('dvin_wcql_email_subject');
		$dvin_wcql_email_msg = get_option('dvin_wcql_email_msg');
		$dvin_wcql_admin_email = get_option('dvin_wcql_admin_email');
		$dvin_wcql_copy_toreq = get_option('dvin_wcql_copy_toreq');
		$dvin_wcql_email_tbl_style = get_option('dvin_wcql_email_tbl_style');
		$dvin_wcql_email_tbl_hdr_style = get_option('dvin_wcql_email_tbl_hdr_style');
		$dvin_wcql_email_tbl_row_style = get_option('dvin_wcql_email_tbl_row_style');
		$dvin_wcql_email_tbl_cell_style = get_option('dvin_wcql_email_tbl_cell_style');
		$dvin_wcql_admin_postfix_email = get_option('dvin_wcql_admin_postfix_email');

		//now include the template
		include('templates/email_settings_template.php');
	}

    static function dvin_fields_mapping_page() {

        global $dvin_wcql_address_fields_labels;

        $settings_arr = maybe_unserialize(get_option('dvin_wcql_settings'));

        $address_fields = apply_filters('dvin_customize_address_fields',$dvin_wcql_address_fields_labels);

        //now include the template
		include('templates/mapfields_settings_template.php');
    }
	/**
     * function dvin_admin_styles, enques styles
	 * @static
     */
	static function dvin_admin_styles() {
		wp_enqueue_style('jquery');
		wp_enqueue_style('jquery-ui-core');
		wp_enqueue_style('jquery-ui-tabs');
		wp_enqueue_style( 'dvin-wcql-ui-css', plugins_url('/css/ui-lightness/jquery-ui-1.8.23.custom.css', __FILE__));//remove, if not req
         wp_enqueue_style('farbtastic'); //remove, if not req
		 wp_enqueue_style( 'dvin-wcql-admin-css', plugins_url('/css/admin.css', __FILE__));//remove, if not req

    }

	/**
	 * function dvin_admin_scripts, enques scripts
	 * @static
  	*/
	static function dvin_admin_scripts() {

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('farbtastic'); //remove, if not req
        wp_enqueue_script('div-wcql-adminjs',plugins_url('/js/admin.js', __FILE__),array('jquery','jquery-ui-core','jquery-ui-tabs'),'','');

	}
}
//add actions
add_action("admin_init", array("dvin_wcql_Admin", "init"));
add_action("admin_menu", array("dvin_wcql_Admin", "add_menu"));
?>
