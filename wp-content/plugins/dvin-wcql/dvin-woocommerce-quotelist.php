<?php
/*
Plugin Name: Woocommerce - Request a Quote
Plugin URI: http://www.limecuda.com/
Description: Woocommerce - Request a Quote Plugin
Version: 2.61
Author: DVin
Author URI: http://www.zippydev.com
Text Domain: dvinwcql
*/

$dvin_qlist_products = array(); //stores the Qlist products

/** define constants **/
define("DVIN_QLIST_PLUGIN_WEBURL", plugin_dir_url( __FILE__ ));
define("DVIN_QLIST_PLUGIN_URL", plugin_dir_path( __FILE__ ));


// address fields
$dvin_wcql_address_fields = array('first_name','last_name','company','email','phone','address_1','address_2','city','state','postcode','country');
$dvin_wcql_address_ship_fields = array('ship_first_name','ship_last_name','ship_company','ship_address_1','ship_address_2','ship_city','ship_state','ship_postcode','ship_country');
$dvin_wcql_address_fields_labels = array('first_name'=>'Billing First name','last_name'=>'Billing Last Name','company'=>'Billing Company','email'=>'Billing Email','phone'=>'Billing Phone','address_1'=> 'Billing Address 1','address_2'=>'Billing Address 2','city'=>'Billing City','state'=>'Billing State','postcode'=>'Billing Postcode','country'=> 'Billing Country','ship_first_name'=>'Shipping First name','ship_last_name'=>'Shipping Last Name','ship_company'=>'Shipping Company','ship_address_1'=> 'Shipping Address 1','ship_address_2'=>'Shipping Address 2','ship_city'=>'Shipping City','ship_state'=>'Shipping State','ship_postcode'=>'Shipping Postcode','ship_country'=> 'Shipping Country');


require_once(DVIN_QLIST_PLUGIN_URL."class-dvin-wcql.php"); //core class
$dvin_wcql_settings = maybe_unserialize(get_option('dvin_wcql_settings')); //get settings


//if AJAX
if(defined('DOING_AJAX') && DOING_AJAX) {

    //do for all cases, we have to set cookies and also load session
    add_action( 'wp_loaded','dvin_wcql_getlist_frm_session',101 ); // After WP and plugins are loaded.
    add_action( 'wp', array( 'Dvin_Wcql', 'set_session_cookies' ), 100 ); // Set session cookies
    Dvin_Wcql::load_ajax_includes(); //add ajax includes

}


if(!defined('DOING_AJAX') || !DOING_AJAX) {
	//this array would be used in in determine which hook and priority  to use for which position
	$dvin_wcql_link_positions = array('After Add to Cart' =>array('hook_name'=>'woocommerce_single_product_summary','priority'=>30),
							'After Thumbnail' => array('hook_name'=> 'woocommerce_product_thumbnails','priority'=>40),
							'After Product Summary'=>array('hook_name'=>'woocommerce_single_product_summary','priority'=>40),
							'Replace Add To Cart'=>array('hook_name'=>'woocommerce_after_add_to_cart_button','priority'=>40),
                            'useshortcode'=>array('hook_name'=>'useshortcode','priority'=>40)
							);




	//include required files based on admin or site
	if (is_admin()) { //for admin
		//admin  files
    Dvin_Wcql::load_backend_includes();
		require_once('metabox.php');
		require_once('product_cat_metabox.php');
	}  else { // for site


		add_action( 'wp_enqueue_scripts', 'dvin_qlist_scripts_styles' );
    Dvin_Wcql::load_site_includes();
		$dvin_wcql_obj = new Dvin_Wcql($_REQUEST);//create object
    add_action( 'wp', array( 'Dvin_Wcql', 'set_session_cookies' ), 100 ); // Set session cookies
    add_action( 'wp_loaded','dvin_wcql_initialize' ); // After WP and plugins are loaded.
	//add_filter('is_woocommerce','dvin_is_woocommerce');
	}


	/* initializes the plugin in front end **/
	function dvin_wcql_initialize() {


		global $dvin_wcql_settings,$page,$post,$dvin_qlist_products;


		require_once('templates/add-to-quotelist.php');


		//replace Add to cart is choosen, means hide the cart button
		if(isset($dvin_wcql_settings['link_position']) && $dvin_wcql_settings['link_position']=='Replace Add To Cart') {
			add_action( 'woocommerce_loop_add_to_cart_link', 'dvin_qlist_remove_addtocart_button', 12);


		} else if(isset($dvin_wcql_settings['apply_individual_settings']) && $dvin_wcql_settings['apply_individual_settings']=='on') {
			add_action( 'woocommerce_loop_add_to_cart_link', 'dvin_qlist_actionon_addtocart_button', 12);


		}


		//Display button on woocommerce shop page */
		if(isset($dvin_wcql_settings['show_on_shop']) && $dvin_wcql_settings['show_on_shop']=='on'){
			add_action( 'woocommerce_after_shop_loop_item', 'dvin_add_qlist_button_to_shop', 11);
		}
		if(isset($dvin_wcql_settings['show_price_login']) && $dvin_wcql_settings['show_price_login']=='on'){
			if(!is_user_logged_in()){
				add_filter('woocommerce_get_price_html','dvin_qlist_showlogin_price');
				add_filter('woocommerce_get_variation_price','dvin_qlist_showlogin_price');
			}


		}else if(isset($dvin_wcql_settings['no_price']) && $dvin_wcql_settings['no_price']=='on'){
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
			add_filter('woocommerce_get_price_html','dvin_qlist_noprice');
			add_filter('woocommerce_get_variation_price','dvin_qlist_noprice');
		}else if(isset($dvin_wcql_settings['no_quote_enabled_price']) && $dvin_wcql_settings['no_quote_enabled_price']=='on'){


			add_action( 'woocommerce_after_shop_loop_item_title', 'dvin_qlist_price', 10 );
			add_filter('woocommerce_get_price_html','dvin_qlist_price');
			add_filter('woocommerce_get_variation_price','dvin_qlist_price');
		}
		if(isset($dvin_wcql_settings['use_gravity_forms']) && isset($dvin_wcql_settings['gravity_form_select'])) {
			add_action("gform_pre_submission_".$dvin_wcql_settings['gravity_form_select'], "dvin_wcql_set_post_content", 10, 2);
      add_filter( 'gform_pre_send_email', 'dvin_wcql_gforms_before_email' );
			add_filter("gform_allowable_tags_".$dvin_wcql_settings['gravity_form_select'], "dvin_wcql_allow_basic_tags", 10, 3);
		}
		if(isset($dvin_wcql_settings['use_formidable_forms']) && isset($dvin_wcql_settings['formidable_form_select'])) {
			add_filter('frm_add_entry_meta', 'dvin_wcql_set_formidable_table_content');
	    add_action('frm_after_create_entry', 'dvin_wcql_after_entry_created', 30, 2);
		}
		if(isset($dvin_wcql_settings['no_qty']) && $dvin_wcql_settings['no_qty']=='on'){
			add_action( 'woocommerce_after_shop_loop_item', 'dvin_remove_quantity_box', 13);
		}


    //get list from session
    $dvin_qlist_products = Dvin_Wcql::get_qlist_from_session();


	}
	function dvin_qlist_showlogin_price() {
		return apply_filters('dvin_wcql_login_alt_text','call for prices');
	}


	function dvin_wcql_allow_basic_tags(){
		return true;
	}
	/**
	* function dvin_add_qlist_button_to_shop, adds button to the products in the shop page
	*/
	function dvin_add_qlist_button_to_shop() {
		global $product,$post,$dvin_qlist_products;


		if($product->get_type() == 'simple' || $product->get_type() == 'external') {
			echo do_shortcode('[dvin-wcql-shopbutton product_id='.$product->get_id().']');
		}
	}


	//based on the settings for that post, it would act (which buttons to show/hide)
	function dvin_qlist_actionon_addtocart_button($value) {
		global $post,$dvin_qlist_products;
		return dvin_wcql_act_on_buttons($post->ID,'_cart_button_settings',$value);
	}


	//callback function to remove the Add to Cart Function
	function dvin_qlist_remove_addtocart_button($value) {
		return "";
	}
	//register for activate and deactivate hook
	register_activation_hook( __FILE__, 'dvin_wcql_activate' );
	register_deactivation_hook( __FILE__, 'dvin_wcql_deactivate' );


	//function enques necessary scripts and styles
	function dvin_qlist_scripts_styles() {


		global $dvin_wcql_settings,$dvin_qlist_products;


		wp_enqueue_style( 'dvin-wcql-stylesheet', plugins_url('/css/styles.css', __FILE__));
		wp_enqueue_style( 'dvin-wcql-custom_stylesheet', plugins_url('/css/custom_styles.css', __FILE__));
		wp_enqueue_style( 'dashicons' );


		//include js file
		if(file_exists(TEMPLATEPATH . '/'. Dvin_Wcql::template_path().'js/dvin_wcql.js')) {
			$js_path = get_template_directory_uri() . '/'. Dvin_Wcql::template_path().'js/dvin_wcql.js';
		}elseif(file_exists(STYLESHEETPATH . '/'. Dvin_Wcql::template_path().'js/dvin_wcql.js')) {
			$js_path = get_stylesheet_directory_uri() . '/'. Dvin_Wcql::template_path().'js/dvin_wcql.js';
		}else{
			$js_path = plugins_url('/js/dvin_wcql.js', __FILE__);
		}


	//	if(!defined('DOING_AJAX') || !DOING_AJAX)
		wp_enqueue_script('dvin-wcql-js',$js_path,array('jquery'),'1.0.0', false);


		$dvin_quotelist_refresh_afteradd_url= ''; //initialize with null to avoid undefined notices
		if(isset($dvin_wcql_settings['redirect_to_listpage_afteradd']) && !empty($dvin_wcql_settings['redirect_to_listpage_afteradd']))
			$dvin_quotelist_refresh_afteradd_url=Dvin_Wcql::get_url();

    $disable_button = apply_filters('dvin_wcql_disable_insteadof_hide_button',"true");
		echo "<script>var dvin_quotelist_count ='".dvin_get_wcql_count()."';var dvin_wcql_ajax_url = '".admin_url( 'admin-ajax.php' )."'; var dvin_quotelist_refresh_afteradd_url='".$dvin_quotelist_refresh_afteradd_url."'; var dvin_wcql_addons ='".dvin_wcql_addons()."';";
    echo "var dvin_wcql_disable_insteadof_hide_button = '".$disable_button."'</script>";
	}
	/**
	 * function Dvin_Wcql_activate, called at the action hook "register_activation_hook". Creates required tables and sets default preferences
	 */
	function Dvin_Wcql_activate() {
        global $wpdb; //wpdb var
        //create page if not exists
        if(!get_option('dvin_quotelist_pageid')) {


			//create quotelist page
			$page_data = array(
				'post_status' => 'publish',
				'post_type' => 'page',
				'post_author' => 1,
				'post_name' => 'Quotelist',
				'post_title' => 'Request For a Quote',
				'post_content' => '[dvin-wcql-listing][dvin-wcql-form]',
				'comment_status' => 'closed'
			);
			$quotelistpage_id = wp_insert_post($page_data);
			update_option('dvin_quotelist_pageid', $quotelistpage_id);
         } else {
            //change status to draft from publish
            wp_update_post( array('ID'=>get_option('dvin_quotelist_pageid'),'post_status'=>'publish') );


        }
        //for default settings, if exists, do not create
        if(!get_option('dvin_quotelist_default_settings_set_or_not')) {
            Dvin_Wcql_default_settings();//set default settings (excludes email settings)
            update_option('dvin_quotelist_default_settings_set_or_not','true');
        }
        //default settings for email
        if(!get_option('dvin_wcql_email_subject')) {
            update_option('dvin_wcql_email_subject','Sharing [%req_name%] quotelist');


            //if Dvin_Wcql_email_msg not exists
            if(!get_option('dvin_wcql_email_msg')) {
                update_option('dvin_wcql_email_msg','Hello,
                Following is the quote list of the [%req_name%]:
                [%quotelist%]
                               Comments: [%comments%]
Thanks,
[%req_name%]');
					}//end if
			}//end if
	}//end of function


	/** function Dvin_Wcql_default_settings - sets the default settings for the plugin options **/
	function Dvin_Wcql_default_settings() {
		$settings_arr = array();


    $settings_arr['apply_individual_settings'] = '';
    $settings_arr['link_position'] = 'After Add to Cart';
	$settings_arr['link_bgcolor'] = '#000000';
	$settings_arr['link_fontcolor'] = '#FFFFFF';
	$settings_arr['link_sameas_addtocart_default_colors'] = 'on';
    $settings_arr['link_sameas_addtocart'] = 'on';
    $settings_arr['custom_css'] = '.addquotelistlink{}';
    $settings_arr['show_quotelist_link_always'] = 'on';
    $settings_arr['show_on_shop'] = '';
    $settings_arr['no_price'] = '';
    $settings_arr['no_quote_enabled_price'] = '';
    $settings_arr['show_price_login'] = '';
    $settings_arr['no_qty'] = '';
    $settings_arr['remove_price_col'] = '';
    $settings_arr['use_gravity_forms'] = '';
    $settings_arr['gravity_form_select'] = '';
    $settings_arr['use_formidable_forms'] = '';
    $settings_arr['formidable_form_select'] = '';
    $settings_arr['add_sku_toemail'] = '';
    $settings_arr['add_price_toemail'] = '';
    $settings_arr['dvin_wcql_redirect_url'] = '';
    $settings_arr['redirect_to_listpage_afteradd'] = '';
    $settings_arr['dvin_wcql_quotelist_url'] = '';
    $settings_arr['use_contactform7'] = '';
    $settings_arr['contactform7_form_select'] = '';
    $settings_arr['create_order'] = '';
    $settings_arr['show_sku_col'] = '';

	

	update_option('dvin_wcql_settings',maybe_serialize($settings_arr));


	//update the custom_styles file
	file_put_contents(DVIN_QLIST_PLUGIN_URL."css/custom_styles.css",$settings_arr['custom_css']);

	//set the default values for the email
	update_option('dvin_wcql_admin_email','');
	update_option('dvin_wcql_copy_toreq', '');
	update_option('dvin_wcql_email_tbl_style','');
	update_option('dvin_wcql_email_tbl_hdr_style','');
	update_option('dvin_wcql_email_tbl_row_style','');
	update_option('dvin_wcql_email_tbl_cell_style','');
	update_option('dvin_wcql_admin_postfix_email','');
	}


	/** Adds quotelist button/link to the single product page based on position selection**/
	function dvin_addquotelist_button($shopbutton=false,$product_id='') {


    if(!apply_filters('dvin_wcql_show_button',true))
        return;


		global $post,$product,$dvin_wcql_obj;


    if(!empty($product_id))
        $product = wc_get_product($product_id);


    $output = '';
		//if not a shop button
		if(!$shopbutton) {


			//if product type is variable, get link based on it
			if($product->get_type() !='variable') {
				//set the product ID
				$product_id = $product->get_id();
				$output .=	Dvin_Wcql_UI::get_qlist_link($product_id,$dvin_wcql_obj->get_url(),$product->get_type(),$dvin_wcql_obj->isExists($product_id, ''));
			}else {
					$output .=  Dvin_Wcql_UI::get_qlist_link($product_id,$dvin_wcql_obj->get_url(),$product->get_type(),false);
			}
			//have message popup to display the messages
			$output .=  Dvin_Wcql_UI::get_message_poupup_container(250,190);
		}else {
			$output .=  Dvin_Wcql_UI::get_qlist_shoplink($dvin_wcql_obj->get_url(),'simple',$dvin_wcql_obj->isExists($product_id, ''),$product_id);
		}
		//if shopbutton return output
		if($shopbutton)
			return dvin_wcql_act_on_buttons($post->ID,'_rfq_button_settings',$output);
		else
			echo dvin_wcql_act_on_buttons($post->ID,'_rfq_button_settings',$output);
	}
	/** removes the  Add to Cart button **/
	function dvin_remove_addtocart_button() {


		global $post,$dvin_wcql_settings;
		if(isset($dvin_wcql_settings['link_position'])) {
      $dvin_wcql_singlecartbutton_class = apply_filters('dvin_wcql_singlecartbutton_class','single_add_to_cart_button');
			switch($dvin_wcql_settings['link_position']) {
				case 'Replace Add To Cart':
							echo "<script>jQuery('.".$dvin_wcql_singlecartbutton_class."').remove();</script>";
							break;
				default:
							echo dvin_wcql_act_on_buttons($post->ID,'_cart_button_settings',"","<script>jQuery('.".$dvin_wcql_singlecartbutton_class."').remove();</script>");
						break;
			}
		}
	}


	//removes the quantity selector or box
	function dvin_remove_quantity_box() {
		echo "<script>jQuery('.input-text.qty.text').remove();</script>";
	}


	//displays the quantity input box
	function dvin_qlist_quantity_input( $input_name, $input_value, $args = array(), $product = null, $echo = true ) {
			if ( is_null( $product ) )
				$product = $GLOBALS['product'];
			$product_quantity = woocommerce_quantity_input( array(
											'input_name'  => $input_name,
											'input_value' => $input_value,
											'max_value'   => $product->backorders_allowed() ? '' : $product->get_stock_quantity(),
											'min_value'   => '1'
										), $product, false );
			$output =	apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, '' );
			if ( $echo ) {
				echo $output;
			} else {
				return $output;
			}
		}


		//callback function, would be called when price should not be shown
		function dvin_qlist_noprice() {
			return '';
		}


		//callback funtion when price should be shown
		function dvin_qlist_price($price_html) {
			global $post;
			if(dvin_wcql_act_on_buttons($post->ID,'_rfq_button_settings',"value") !='')
				return '';
			return $price_html;
		}


		//callback function for GForm pre submission, if quotelisttable,[orderinfo] and address fields are replaced with appropriate values
		//last but not least, it empties the quotelist products in the session
		 function dvin_wcql_set_post_content($form){


	        global $dvin_wcql_address_fields,$dvin_wcql_settings,$dvin_wcql_obj;


          $dvin_qlist_products = Dvin_Wcql::get_qlist_from_session();


	         $data = array();
	         $ref_order_field = '';


					$total_price = 0; //this variable is not useful but declaring to just to accept the return value from the get_qlist_table function
				 	$fields = $form['fields'];


             //loop through fields
		 			 foreach($fields as $arr){


                if(strpos($arr['defaultValue'],'quotelisttable') !== FALSE) {
                    $_POST['input_'.$arr['id']] = do_shortcode($arr['defaultValue']);
                }


	            if($arr['defaultValue']=='[orderinfo]')
		 						$ref_order_field = &$_POST['input_'.$arr['id']];


                //check for existense of the array before looping through it
                if(isset($dvin_wcql_settings['address_fields']) && is_array($dvin_wcql_settings['address_fields'])) {
                    $matched_key_arr = array_search($arr['id'],$dvin_wcql_settings['address_fields']);
                    if(!empty($matched_key_arr))
                        $data[$arr['id']] = $_POST['input_'.$arr['id']];
                }
		 				}


	         //create order if enabled
	         if(isset($dvin_wcql_settings['create_order']) && $dvin_wcql_settings['create_order'] == 'on') {
		 	     $dvin_wcql_obj->order_id = dvin_qlist_create_order($data);//craete order
	             if($ref_order_field != '')
	                 $ref_order_field = $dvin_wcql_obj->get_order_info();
	         }
					 //clear the products from session
					Dvin_Wcql::set_qlist_to_session(array());
			}


	  function gf_quotelist($value){
		    return do_shortcode('[dvin-wcql-listing]');
		}


    //quotelisttable and [orderinfo] are replaced with appropriate content
		function dvin_wcql_set_formidable_table_content($new_values) {
			global $dvin_wcql_settings,$dvin_wcql_obj,$dvin_qlist_products;
			$total_price = 0; //this variable is not useful but declaring to just to accept the return value from the get_qlist_table function
            if(strpos($new_values['meta_value'],'quotelisttable') !== FALSE) {
                $new_values['meta_value'] = do_shortcode($new_values['meta_value']);
            }


	        if($new_values['meta_value'] == '[orderinfo]') {
	             //create order if enabled
	            if($dvin_wcql_settings['create_order'] == 'on') {
	                $dvin_wcql_obj->order_id = dvin_qlist_create_order($_POST['item_meta']);//craete order
	                $new_values['meta_value'] = $dvin_wcql_obj->get_order_info();
	            }
	        }


			return $new_values;
		}


  //after entry is created in Formiddable pro, we are clearing the list from the session
  function dvin_wcql_after_entry_created($entry_id, $form_id) {
      Dvin_Wcql::set_qlist_to_session(array());
  }


  //show or hide based on the settings
	function dvin_wcql_act_on_buttons($post_id,$meta_key,$value,$hide="") {


	   global $dvin_wcql_settings;


	   if(isset($dvin_wcql_settings['apply_individual_settings']) && $dvin_wcql_settings['apply_individual_settings'] == 'on') {


 			$setting = get_post_meta( $post_id, $meta_key, true );


				switch($setting) {


					case 'show': return $value;


					case 'hide': return $hide;


					default:


						     $terms = get_the_terms( $post_id, 'product_cat' );




							 if(is_array($terms)) {


                                foreach ($terms as $term) {
								    $product_cat_id = $term->term_id;
                                     $term_meta = get_option( "taxonomy_{$product_cat_id}" );
								     $set = isset($term_meta[ltrim($meta_key,'_')])?$term_meta[ltrim($meta_key,'_')]:'';


                                     if(!empty($set))
									   return $set == 'hide'? $hide:$value;
                                 }//end foreach


							}//end if


				}
		}
		return $value;
	}

	
}

//To get the post_id of the sending post I used the following code in my functions.php:
function dvin_wcql_mycf7_before_send_mail($WPCF7_ContactForm) {

	global $dvin_wcql_settings,$dvin_wcql_obj,$dvin_qlist_products;

	 // do some replacements in the cf7 email body
	  $mail  = $WPCF7_ContactForm->prop('mail');
	 //find the strings of quotelist, if it is there get the shortcode
			 preg_match_all('/(\[quotelist.*)/',$mail['body'],$matches);
	 if(count($matches)>0 && isset($matches[0][0])) {

		if(!isset($dvin_qlist_products) || empty($dvin_qlist_products)) {
			$dvin_qlist_products = Dvin_Wcql::get_qlist_from_session();
		}

		//prepare and send email
		$submission = WPCF7_Submission::get_instance();
		$data = $submission->get_posted_data();

		//create order if enabled
		if($dvin_wcql_settings['create_order'] == 'on')
			$dvin_wcql_obj->order_id = dvin_qlist_create_order($data);//craete order

		// do some replacements in the cf7 email body
		$mail  = $WPCF7_ContactForm->prop('mail');

		//find the strings of quotelist, if it is there get the shortcode
		preg_match_all('/(\[quotelist.*)/',$mail['body'],$matches);

		if(count($matches)>0 && isset($matches[0][0])) {
			list($total_price,$table) = unserialize(do_shortcode($matches[0][0]));


			// Find/replace the "[quotelist]" and "[orderinfo]"tag as defined in your CF7 email body
			// and add changes name
			$mail['body'] = str_replace(array($matches[0][0],'[orderinfo]'), array($table,$dvin_wcql_obj->get_order_info()), $mail['body']);
			 // Save the email body
			$WPCF7_ContactForm->set_properties(array(
				"mail" => $mail
			));

		}

		$mail2 = $WPCF7_ContactForm->prop( 'mail_2' ); //get CF7's mail_2 object
		//handle cc mail
		if($mail2['active']) { //if Checkbox checked
			// Find/replace the "[quotelist]" tag as defined in your CF7 email body
			// and add changes name
			//find the strings of quotelist, if it is there get the shortcode
			preg_match_all('/(\[quotelist.*)/',$mail2['body'],$matches);
			if(count($matches)>0 && isset($matches[0][0])) {
				list($total_price,$table) = unserialize(do_shortcode($matches[0][0]));
				 $mail2['body'] = str_replace(array($matches[0][0],'[orderinfo]'), array($table,$dvin_wcql_obj->get_order_info()), $mail2['body']);
				$WPCF7_ContactForm->set_properties( array( 'mail_2' => $mail2 ) );
			}
		}

	//clear products from session
	Dvin_Wcql::set_qlist_to_session(array());
	
	// return current cf7 instance
	return $WPCF7_ContactForm;
   }
 }

add_action( 'wpcf7_before_send_mail', 'dvin_wcql_mycf7_before_send_mail' );



function wcql_get_mini_qlist() {
    global $dvin_qlist_products;//clear the products from session
    $qlist = array();
    if(isset($dvin_qlist_products) && count($dvin_qlist_products)>0) {
        $qlist_count[0]['cnt'] = count($dvin_qlist_products);
        $qlist = $dvin_qlist_products;
    }
    $dvin_wcql_settings = maybe_unserialize(get_option('dvin_wcql_settings'));


    if(file_exists(TEMPLATEPATH . '/'. Dvin_Wcql::template_path().'templates/mini-cart.php')) {
        include(TEMPLATEPATH . '/'. Dvin_Wcql::template_path().'templates/mini-cart.php');
    }elseif(file_exists(STYLESHEETPATH . '/'. Dvin_Wcql::template_path().'templates/mini-cart.php')) {
        include(STYLESHEETPATH . '/'. Dvin_Wcql::template_path().'templates/mini-cart.php');
    }else{
        require('templates/mini-cart.php');
    }
}
/**
* Get the product row price per item.
*
* @param WC_Product $_product
* @return string formatted price
*/
function get_quotelist_product_price( $_product ) {


    //based on tax inclusion or exclusion, display the price
if ( get_option('woocommerce_tax_display_cart') == 'incl' )
    $product_price =  wc_get_price_including_tax( $_product);
else
    $product_price = wc_get_price_excluding_tax( $_product);


    return (float) $product_price;
}
/**
* function dvin_get_wcql_count, rteurn the number of products in quotelist
*@return integer number of products in quotelist
*/
function dvin_get_wcql_count() {
    global $dvin_qlist_products;
    $var_count  = 0;
    if(isset($dvin_qlist_products) && is_array($dvin_qlist_products)) {
        foreach($dvin_qlist_products as $key=>$value)
            if($key == '') {
                unset($dvin_qlist_products[$key]);
                 Dvin_Wcql::set_qlist_to_session($dvin_qlist_products);
            } else {
                //if defined and handle even arrays in case of grouped products
                if(isset( $dvin_qlist_products[$key]['quantity'])) {
                    if(is_array($dvin_qlist_products[$key]['quantity']))
                        foreach($dvin_qlist_products[$key]['quantity'] as $qty)
                            $var_count += $qty;
                    else
                        $var_count += $dvin_qlist_products[$key]['quantity'];
                }
            }
        return $var_count;
    }
}
//called by deactivate hook, do thye necessary stuff
function Dvin_Wcql_deactivate() {
    //change status to publish from draft
    wp_update_post( array('ID'=>get_option('dvin_quotelist_pageid'),'post_status'=>'draft') );
}




//craetes order
function dvin_qlist_create_order($data) {


global $dvin_qlist_products,$dvin_wcql_settings;


 if(class_exists('Product_Addon_Cart')) {
     $addon_cart_obj = new Product_Addon_Cart(); //create object for addons
 }
$address = array(); //stores the billing and shipping info for creating order
$billing_address = dvin_wcql_map_address($data,'');
$shipping_address = dvin_wcql_map_address($data,'ship_');
$order = wc_create_order();
foreach($dvin_qlist_products as $key => $arr) {

    if(!isset($arr['variation_id']) && !isset($arr['product_id']))
        continue;


    //simple or variable
    if(isset($arr['variation_id']) && !empty($arr['variation_id'])) {
        $product_obj = wc_get_product($arr['variation_id']);
        if(isset($arr['price']))
            $product_obj->set_price($arr['price']);
        $item_id = $order->add_product($product_obj,$arr['quantity'],array('variation'=>$arr['variation']));
    } else{
       $product_obj = wc_get_product($arr['product_id']);
        $item_id = $order->add_product( $product_obj, $arr['quantity'] ); //(get_product with id and next is for quantity)
    }


    // Allow plugins to add order item meta
    if(isset($addon_cart_obj) && $addon_cart_obj instanceof Product_Addon_Cart) {
        //to avoid/prevent notices
        $arr['data']=array();
        $addon_cart_obj->order_item_meta($item_id, $arr);
    };
}//end of foreach


$order->set_address( $billing_address, 'billing' );
$order->set_address( $shipping_address, 'shipping' );
$order->calculate_totals();
//update the customer data
update_post_meta( $order->get_id(), '_customer_user',	absint( get_current_user_id()) );
//update the order status to pending
$order = new WC_Order($order->get_id());
$order->update_status('pending');
return $order->get_id();
}


//maps the adddress fields
function dvin_wcql_map_address($post_vars,$prefix) {


global $dvin_wcql_settings,$dvin_wcql_address_fields,$dvin_wcql_address_ship_fields;


//$address_fields = apply_filters('dvin_customize_address_fields',$dvin_wcql_address_fields);
if($prefix=='ship_')
    $address_fields = $dvin_wcql_address_ship_fields;
else
    $address_fields = $dvin_wcql_address_fields;


$len = strlen($prefix);


$address = array(); //initialize with array
foreach($address_fields as $field) {


    if(isset($dvin_wcql_settings['address_fields'][$field])) {
        if($len >0) {
            if(isset($post_vars[$dvin_wcql_settings['address_fields'][$field]]))
                $address[substr($field,$len)] = $post_vars[$dvin_wcql_settings['address_fields'][$field]];
        } else {
            if(isset($post_vars[$dvin_wcql_settings['address_fields'][$field]]))
                $address[$field] = $post_vars[$dvin_wcql_settings['address_fields'][$field]];
        }
    }
}
return $address;


}


//prepares the table to be displayed in the email
function get_qlist_table($atts) {


     $dvin_wcql_settings = maybe_unserialize(get_option('dvin_wcql_settings')); //get settings
     $qlist = $dvin_qlist_products = Dvin_Wcql::get_qlist_from_session();//get list from session




    //get the all the style values
    $dvin_wcql_email_tbl_style = get_option('dvin_wcql_email_tbl_style');
    $dvin_wcql_email_tbl_hdr_style = get_option('dvin_wcql_email_tbl_hdr_style');
    $dvin_wcql_email_tbl_row_style = get_option('dvin_wcql_email_tbl_row_style');
    $dvin_wcql_email_tbl_cell_style = get_option('dvin_wcql_email_tbl_cell_style');


    ob_start();
    //include the template file
    if(file_exists(TEMPLATEPATH . '/'. Dvin_Wcql::template_path().'templates/email_quotelist_template.php')) {
        include(TEMPLATEPATH . '/'. Dvin_Wcql::template_path().'templates/email_quotelist_template.php');
    }elseif(file_exists(STYLESHEETPATH . '/'. Dvin_Wcql::template_path().'templates/email_quotelist_template.php')) 		{
        include(STYLESHEETPATH . '/'. Dvin_Wcql::template_path().'templates/email_quotelist_template.php');
    }else{
        require('templates/email_quotelist_template.php');
    }


    $quote_list = preg_replace("/[\n\r]/","",ob_get_contents());
    ob_end_clean();
    return array($grand_total_price,$quote_list);
}


//callback for 'woocommerce_is_purchasable' filter
function dvin_makeitpurchaseable($purchaseable) {
    return true;
}


//retrieves the products from session
function dvin_wcql_getlist_frm_session() {
    global $dvin_qlist_products;


     //get list from session
    $dvin_qlist_products = Dvin_Wcql::get_qlist_from_session();
}


//deprecated function - which was using in the mini-cart.php
function dvin_wcql_get_add_on_list($addon_cart_obj,$price, $product_obj, $values) {


    $temp_arr = array();
    if($addon_cart_obj instanceof Product_Addon_Cart) {
        $values['data']=array();
        $arr = wc_get_formatted_cart_item_data( array(), $values );


       foreach($arr as $ar) {
            $temp_arr[$ar['name']]=isset($ar['value'])?$ar['value']:'';
        }
    }
    return $temp_arr;
}


//callback function for gform_pre_send_email filter/action
function dvin_wcql_gforms_before_email($email) {
    $email['message'] = html_entity_decode($email['message']);
    $email['subject'] = html_entity_decode($email['subject']);
    return $email;
}




//adds grand total row to the table
function dvin_wcql_grand_total_row($arr_cols,$cols_counter,$grand_total_qty,$grand_total_price,$data_arr) {


      //grand total price
        if($grand_total_price>=0 && isset($arr_cols['subtotal'])) {
            ?>
            <tr>
                <td colspan="<?php echo $cols_counter-2;?>" align="right" class="dvingrandtotal">
                    <div  style="float:right;">
                        <?php  echo apply_filters('grand_total_text',__('GRAND TOTAL','dvinwcql')); ?>
                    </div>
                </td>
                <td align="left" valign="center" style="<?php echo apply_filters('dvin_wcql_grandqtynumcell_style','');?>" class="grandqtynumcell">&nbsp;<?php echo $grand_total_qty; ?></td>
                <td align="right" style="<?php echo apply_filters('dvin_wcql_grandtotalpricecell_style','');?>" class="grandtotalpricecell" data-title="<?php  echo apply_filters('grand_total_text',__('GRAND TOTAL','dvinwcql')); ?>"><?php echo wc_price($grand_total_price); ?></td>
            </tr>
            <?php
            }//end of grand total
}


//adds update button row to the table
function dvin_wcql_updatebutton_row($arr_cols,$cols_counter,$grand_total_qty,$grand_total_price,$data_arr) {


    global $dvin_wcql_settings;


    //if no qty, hide the button
    if(isset($dvin_wcql_settings['no_qty'] ) && $dvin_wcql_settings['no_qty'] != 'on') {
     ?>
      <tr>
            <td colspan="<?php echo $cols_counter;?>" class="dvinupdatelist" align="right">
            <?php
                echo '<div style="float:right;"><button type="button" class="button alt wcqlupdatelistbtn" onClick="javascript:ajax_req_update_quote();">'.apply_filters('update_list_text', __('Update List',"dvinwcql")).'</button></div></div>';
            ?>
            </td>
    </tr>
    <?php
    }
}


//function to determine, whether we are using any AddOns or not
function dvin_wcql_addons(){
	//we are checking for standard Woocommerce Add Ons and TM extra product options, for any other you ahve to set the filter to set
    if(function_exists('get_product_addons') || class_exists('TM_Extra_Product_Options'))
        return apply_filters('dvin_wcql_addons',true);
}

add_filter('dvin_wcql_quotelist_bottom_html','dvin_wcql_quotelist_bottom_html_func');
//funcion
function dvin_wcql_quotelist_bottom_html_func() {
	global $dvin_qlist_products;
	if(count($dvin_qlist_products)>0)
	return '<p class="return-to-shop"><a class="button wc-backward" href="'.apply_filters( 'dvin_wcql_return_to_shop_url', get_permalink( wc_get_page_id( 'shop' ) ) ).'">'.apply_filters('dvin_wcql_return_to_shop_txt',__('Return To Shop','dvinwcql')).'</a></p>';
}
?>