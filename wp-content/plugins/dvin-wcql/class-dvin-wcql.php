<?php
/**
 * Dvin_Wcql class, Handles the core Quotelist functionality
 */
 class Dvin_Wcql {
	public $errors_arr; //stores the errors
	public $details_arr; //stores details array
	public $messages_arr; //stores details array
    public $order_id;
	/**
	* Constructor initializes the variables with respective values
	*@access public
	*/
	public function __construct($details_arr) {
		$this->errors_arr = array();
		$this->details_arr = $details_arr;
        $order_id = 0;
	}
	/**
   * function isExistsWithGenratedKey, checks for existence of the product
	 * @access  public
	 * @boolean
   */
	public function isExistsWithGenratedKey($cart_key) {
    global $wpdb,$dvin_qlist_products;
    //if using addons, no need to check for existence of the product in the list
    if(dvin_wcql_addons())
        return false;
    //if array of products
    if(is_array($dvin_qlist_products))
        if(array_key_exists( $cart_key, $dvin_qlist_products))
            return true;
      return false;
	}
	/**
   * function remove, removes product from quotelist based on its ID
	 * @access  public
	 * @boolean
   */
	public static function remove($id) {
	   global $dvin_qlist_products;

     foreach ( $dvin_qlist_products as $cart_item_key => $cart_item ) {
        $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
        $var_id   = $cart_item['variation_id'];
        if($product_id == $id || $var_id == $id) {
          unset($dvin_qlist_products[$cart_item_key]);

          Dvin_Wcql::set_qlist_to_session($dvin_qlist_products);
          return true;
        }
      }
      unset($dvin_qlist_products[$id]);
      Dvin_Wcql::set_qlist_to_session($dvin_qlist_products);

      return true;

	}
     /**
   * function isExists, checks for existence of the product
	 * @access  public
	 * @boolean
   */
	public function isExists($prod_id,$variation_id=0) {
    	global $wpdb,$dvin_qlist_products;
    //if using addons, no need to check for existence of the product in the list
    if(dvin_wcql_addons())
        return false;
    if(is_array($dvin_qlist_products)) {
      $arr_products = DVin_Wcql::get_product_ids($dvin_qlist_products);
      if($variation_id>0) {
          if(array_search($variation_id,$arr_products)!==FALSE)
              return true;
          return false;
      } else {
          if(array_search($prod_id,$arr_products)!==FALSE)
              return true;
          return false;
      }
    }
  }
	/**
	* function get_url, Builds the quotelist page URL
	* @access  public
	* @return string URL
  */
	public static function get_url() {
		global $dvin_wcql_settings,$dvin_qlist_products;
    // Generate the random number to cheat the local Browser cache
    //$nocache_str =rand(1,100000);
		if(isset($dvin_wcql_settings['dvin_wcql_quotelist_url']) && !empty($dvin_wcql_settings['dvin_wcql_quotelist_url']))
			return  apply_filters('dvin_wcql_quotelist_url', $dvin_wcql_settings['dvin_wcql_quotelist_url']);
		return  apply_filters('dvin_wcql_quotelist_url', get_permalink(get_option('dvin_quotelist_pageid')));
	}
	/**
	* function get_remove_url, Builds the generic URL which handles removal of product from quotelist
	* @access  public
	* @string URL
  */
	public static function get_remove_url($id) {
		return add_query_arg('action','remove_from_quotelist',add_query_arg('qlist_item_id', $id,DVIN_QLIST_PLUGIN_WEBURL."dvin-wcql-ajax.php"));
	}
/**
	* function add, Adds product to quotelist
	* @access  public
	* @return string "error" or "true"  , "error" indicates, error occurred
  */
	public function add() {
        //global variables
		global $wpdb,$woocommerce,$dvin_qlist_products;
        //get the list from session
        $dvin_qlist_products = self::get_qlist_from_session();
        //set default values
        $variation_id= $product_id = 0;
        $variation = array();
        $quantity = 1;
        $cart_item_data = array();
        //create object of the WC_cart
        $cart_obj = new WC_Cart();

        extract($this->details_arr); //now extract which would override the default values set
        try {
                $product_id   = absint( $product_id );
				$variation_id = absint( $variation_id );
                //if it is variation
                if($variation_id > 0){
                    //get variation data and price
                    list($price,$variation)=$this->variation_data_price($product_id,$variation_id);
                }
				// Ensure we don't add a variation to the cart directly by variation ID
				if ( 'product_variation' == get_post_type( $product_id ) ) {
					$variation_id = $product_id;
					$product_id   = wp_get_post_parent_id( $variation_id );
				}

				// Get the product
				$product_data = wc_get_product( $variation_id ? $variation_id : $product_id );

                // Load cart item data - may be added by other plugins
				$cart_item_data = (array) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id );

				// Generate a ID based on product ID, variation ID, variation data, and other cart item data
				$item_key        = $cart_obj->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );
                // check for existence,  product ID, variation ID, variation data, and other cart item data
			if($this->isExistsWithGenratedKey( $item_key, '')) {
				return array($item_key,"exists");
			}
            // Add item after merging with $cart_item_data - hook to allow plugins to modify cart item
					$dvin_qlist_products[$item_key] = apply_filters( 'woocommerce_add_cart_item', array_merge( $cart_item_data, array(
						'product_id'	=> $product_id,
						'variation_id'	=> $variation_id,
						'variation' 	=> $variation,
						'quantity' 		=> $quantity,
						'data'			=> $product_data
					) ), $item_key );
            Dvin_Wcql::set_qlist_to_session($dvin_qlist_products);
            return array($item_key,"true");
        } catch ( Exception $e ) {
				if ( $e->getMessage() ) {
					wc_add_notice( $e->getMessage(), 'error' );
				}
				return array('',"false");
			}
    }
	/**
	* function send_email, Sends Email
	* @access  public static
	* @return boolean
  */
	public function send_email() {
        global $dvin_qlist_products,$dvin_wcql_settings;
		$overall_tot_price = 0;
		$email = get_option('dvin_wcql_admin_email');
		$postfix_email = get_option('dvin_wcql_admin_postfix_email');
		$subject = get_option('dvin_wcql_email_subject');
		$message = get_option('dvin_wcql_email_msg');
		$cc = get_option('dvin_wcql_copy_toreq');
		list($overall_tot_price,$quote_list) = get_qlist_table(array());
		 $overall_tot_price_str = isset($overall_tot_price)?apply_filters('woocommerce_cart_item_price_html', woocommerce_price( $overall_tot_price)):'';
		$needle_arr = array('[%req_name%]','[%req_email%]','[%quotelist%]','[%total_price%]','[%comments%]');
		$replace_with_arr = array(ucwords($_POST['req_name']),$_POST['req_email'],$quote_list,$overall_tot_price_str,$_POST['req_details']);
		//apply filters  to capture custom fileds names in [%filed_name%]
		$needle_arr = apply_filters('dvin_wcql_custom_fields_needles',$needle_arr);
		//apply filters for
		$replace_with_arr = apply_filters('dvin_wcql_custom_fields_replacements',$replace_with_arr);
		//include the validation file if exists
		if(file_exists(TEMPLATEPATH . '/'. Dvin_Wcql::template_path().'custom-field-arr.php')) {
			include(TEMPLATEPATH . '/'. Dvin_Wcql::template_path().'custom-field-arr.php');
		}elseif(file_exists(STYLESHEETPATH . '/'. Dvin_Wcql::template_path().'custom-field-arr.php')) {
			include( STYLESHEETPATH . '/'. Dvin_Wcql::template_path().'custom-field-arr.php');
		}
		$subject = str_replace($needle_arr,$replace_with_arr,$subject);
		$message = str_replace($needle_arr,$replace_with_arr,$message);
        $message ='<html><body><style>table, th, td{border: 1px solid black;}</style>'.$message.'</body></html>';
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		// Additional headers
		$from_admin_email = !empty($postfix_email)? $postfix_email : $_POST['req_name'].' <'.$_POST['req_email'].'>';
		$to_admin_headers = $headers.'From: '.$from_admin_email."\r\n";
		$to_admin_headers = $to_admin_headers.'Reply-to: '.$_POST['req_name'].'<'.$_POST['req_email'].">\r\n";
		//check for whether to send copy to customer
		if($cc == "on"){
			$to_customer_headers = $headers.'From: '.$email." \r\n";
		}
        //get order info. in case of admin email
        $admin_message = str_replace('[%orderinfo%]', $this->get_order_info(),$message);
        //replace with nothing in case of client
        $message = str_replace('[%orderinfo%]','',$message);
		//send the email
		if(wp_mail($email, $subject, nl2br($admin_message),$to_admin_headers)) {
			if($to_customer_headers!=''){
				if(wp_mail($_POST['req_email'], $subject, nl2br($message),$to_customer_headers)){
					Dvin_Wcql::set_qlist_to_session(array());
					return true;
				}
			} else {
        Dvin_Wcql::set_qlist_to_session(array());
				return true;
			}
		}
		return false;
	}
	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public static function template_path() {
		return apply_filters( 'DVIN_QLIST_TEMPLATE_PATH', 'dvin-wcql/');
	}
     public function get_order_info() {
         global $dvin_qlist_products;
         //for admin message, replace the order info with right info otherwise space
        $order_info = apply_filters('dvin_wcql_order_info_str',__('Order Links(%s):<a href="%s">View</a>&nbsp;|&nbsp;<a href="%s">Edit</a>&nbsp;|&nbsp;<a href="%s">CheckOut</a>','dvinwcql'));
        $order_obj = new WC_Order($this->order_id);
        $order_edit_url = admin_url( 'post.php?post=' . absint( $this->order_id ) . '&action=edit');
        //$order_obj->get_view_order_url();
        return sprintf($order_info,$this->order_id,$order_obj->get_view_order_url(),$order_edit_url,$order_obj->get_checkout_payment_url());
     }
     //load all ajax actions
     public static function load_ajax_actions() {
        add_action( 'wp_ajax_wcql_widget_refresh',array('Dvin_Wcql_Ajax','widget_refresh'));
        add_action( 'wp_ajax_nopriv_wcql_widget_refresh',array('Dvin_Wcql_Ajax','widget_refresh'));
        add_action( 'wp_ajax_add_to_qlist',array('Dvin_Wcql_Ajax','add_to_qlist'));
        add_action( 'wp_ajax_nopriv_add_to_qlist',array('Dvin_Wcql_Ajax','add_to_qlist'));
        add_action( 'wp_ajax_remove_from_qlist',array('Dvin_Wcql_Ajax','remove_from_qlist'));
        add_action( 'wp_ajax_nopriv_remove_from_qlist',array('Dvin_Wcql_Ajax','remove_from_qlist'));
        add_action( 'wp_ajax_find_prod_in_qlist',array('Dvin_Wcql_Ajax','find_prod_in_qlist'));
        add_action( 'wp_ajax_nopriv_find_prod_in_qlist',array('Dvin_Wcql_Ajax','find_prod_in_qlist'));
        add_action( 'wp_ajax_add_to_qlist_from_prodpage',array('Dvin_Wcql_Ajax','add_to_qlist'));
        add_action( 'wp_ajax_nopriv_add_to_qlist_from_prodpage',array('Dvin_Wcql_Ajax','add_to_qlist'));
        add_action( 'wp_ajax_remove_from_page',array('Dvin_Wcql_Ajax','remove_from_page'));
        add_action( 'wp_ajax_nopriv_remove_from_page',array('Dvin_Wcql_Ajax','remove_from_page'));
        add_action( 'wp_ajax_update_list',array('Dvin_Wcql_Ajax','update_list'));
        add_action( 'wp_ajax_nopriv_update_list',array('Dvin_Wcql_Ajax','update_list'));
        add_action( 'wp_ajax_send_request',array('Dvin_Wcql_Ajax','send_request'));
        add_action( 'wp_ajax_nopriv_send_request',array('Dvin_Wcql_Ajax','send_request'));
     }
     //localisation
     public static function add_localisation() {
        //define constants if not
        if(!defined('TEMPLATEPATH_QLIST'))
            define('TEMPLATEPATH_QLIST',get_template_directory());
        if(!defined('STYLESHEETPATH_QLIST'))
            define('STYLESHEETPATH_QLIST',get_stylesheet_directory());
        //include the language file
        if(file_exists(TEMPLATEPATH_QLIST . '/'. Dvin_Wcql::template_path().'languages/')) {
            $langdir_path = TEMPLATEPATH_QLIST . '/'. Dvin_Wcql::template_path().'languages/';
        }elseif(file_exists(STYLESHEETPATH_QLIST . '/'. Dvin_Wcql::template_path().'languages/')) {
            $langdir_path = STYLESHEETPATH_QLIST . '/'. Dvin_Wcql::template_path().'languages/';
        }else{
            $langdir_path =  dirname( plugin_basename( __FILE__ ) ). '/languages/';
        }
        load_plugin_textdomain( 'dvinwcql', false, $langdir_path );
     }
     //ajax includes
     public static function load_ajax_includes() {
       self::add_localisation(); // add languages files
	   self::load_ajax_actions(); //ajax actions
       require_once(DVIN_QLIST_PLUGIN_URL."class-dvin-wcql-ajax.php"); //core class
     }
     //frontend site includes
     public static function load_site_includes() {
         require_once(DVIN_QLIST_PLUGIN_URL."class-dvin-wcql-ui.php");//ui related
         require_once("class-dvin-wcql-shortcodes.php"); //shortcodes inclusion
         self::add_localisation(); // add languages files
     }
      //backend includes
     public static function load_backend_includes() {
         self::add_localisation(); // add languages files
         include_once(DVIN_QLIST_PLUGIN_URL."class-dvin-wcql-admin.php");
     }
     //sets session cookies
    public static function set_session_cookies() {
        do_action( 'woocommerce_set_cart_cookies', true );
    }
    //get the qlist from the session
     public static function get_qlist_from_session(){
            if(function_exists('WC') && is_object(WC()->session)) {
              return  WC()->session->get('dvin_qlist_products');
            } else {
                WC()->initialize_session();
                return WC()->session->get('dvin_qlist_products');
            }
			return;
     }
     //set the qlist to the session
     public static function set_qlist_to_session($qlist)
     {
        if(function_exists('WC') && is_object(WC()->session)) {
            WC()->session->set('dvin_qlist_products',$qlist);
          } else {
              WC()->initialize_session();
              WC()->session->set('dvin_qlist_products',$qlist);
          }
     }
     //get the array with product or variable SplDoublyLinkedList
     public static function get_product_ids(&$qlist) {
       //unset if any already exists
       $products_list=array();
       //loop through and set the list
       foreach($qlist as $val){
            if(!empty($val['variation_id']))
               $products_list[]= $val['variation_id'];
            else
               $products_list[]= $val['product_id'];
        }
       if(isset($products_list) && is_array($products_list))
           $products_list = array_unique($products_list);
        return $products_list;
     }
     //to check for empty product List
     public static function isempty_quotelist(&$qlist) {
       if(is_array($qlist) && count($qlist)>0)
          return false;
        return true;
     }
     public function variation_data_price($product_id,$variation_id) {
         $attributes = (array) maybe_unserialize(get_post_meta($product_id, '_product_attributes', true));
				$variations = array();
				$all_variations_set = true;
				foreach ($attributes as $attribute) :
					if ( !$attribute['is_variation'] ) continue;
					$taxonomy = 'attribute_' . sanitize_title($attribute['name']);
					if (!empty($this->details_arr[$taxonomy])) :
						// Get value from post data
						$value = esc_attr(stripslashes($this->details_arr[$taxonomy]));
						// Use name so it looks nicer in the cart widget/order page etc - instead of a sanitized string
						$variations[esc_attr($taxonomy)] = $value;
					else :
						$all_variations_set = false;
					endif;
				endforeach;
				if ($all_variations_set && $variation_id > 0) {
                    //return array
                    return array(get_quotelist_product_price( wc_get_product($variation_id)),$variations);
                }
                return array();
     }
     //determine whether using standard form or not.
     public static function usingStandardForm() {
         global $dvin_wcql_settings;
        if((!isset($dvin_wcql_settings['use_gravity_forms']) || $dvin_wcql_settings['use_gravity_forms']!='on') && (!isset($dvin_wcql_settings['use_formidable_forms']) || $dvin_wcql_settings['use_formidable_forms']!='on') && (!isset($dvin_wcql_settings['use_contactform7']) || $dvin_wcql_settings['use_contactform7']!='on'))
            return true;
         return false;
     }
     //determine whether to show proce column or not based on settings.
     public static function show_price_column() {
         global $dvin_wcql_settings;
         if((isset($dvin_wcql_settings['remove_price_col'] ) && $dvin_wcql_settings['remove_price_col'] == 'on') || ((isset($dvin_wcql_settings['show_price_login'] ) && $dvin_wcql_settings['show_price_login'] == 'on') && !is_user_logged_in()))
          return true;
        return false;
     }
     //get the data of quotelist to prepare
     public static function getQuotelistData(&$qlist,&$arr_cols,$cols_counter,&$grand_total_price=0,&$grand_total_qty=0,$for_email=false) {
         //data array
         $data_arr = array();
		 
		 if(function_exists('WC') && !is_object(WC()->cart)) {
			 WC()->initialize_cart();
		 }
		 
         if(is_array($qlist) && count($qlist) == 0) 
            return array();

         //loop through the list
		foreach ( $qlist as $cart_item_key => $cart_item ) {

			$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
            //$prod_permalink = $_product->get_permalink( $cart_item );
            $prod_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->get_permalink( $cart_item ), $cart_item, $cart_item_key );

            //if product exists and quantity greater than zero
			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0) {
                 $data_arr[$cart_item_key]['row_id']=$cart_item_key.'_dvin_rowid';
                 $data_arr[$cart_item_key]['remove']='<a href="javascript:void(0)" title="'.__('Remove this item','dvinwcql').'" class="remove removeproductqlist" data-product_id="'.$cart_item_key.'">'.apply_filters( 'dvin_remove_text','&times;').'</a>';
                 $data_arr[$cart_item_key]['thumbnail']=sprintf( '<a href="%s">%s</a>', esc_url( $prod_permalink ), apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key ));
                 //$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                 $image_size = wc_get_image_size( 'shop_thumbnail' );
                $thumbnail = apply_filters( 'woocommerce_order_item_thumbnail', '<div style="margin-bottom: 5px"><img src="' . ( $_product->get_image_id() ? current( wp_get_attachment_image_src( $_product->get_image_id(), 'thumbnail' ) ) : wc_placeholder_img_src() ) . '" alt="' . esc_attr__( 'Product image', 'woocommerce' ) . '" height="' . esc_attr( $image_size['height'] ) . '" width="' . esc_attr( $image_size['width'] ) . '" style="vertical-align:middle; margin-' . ( is_rtl() ? 'left' : 'right' ) . ': 10px;" /></div>', $cart_item );
                 $data_arr[$cart_item_key]['thumbnail']=sprintf( '<a href="%s">%s</a>', esc_url( $prod_permalink ), $thumbnail );
                                  
                 $item_name = apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s </a> %s', esc_url( $prod_permalink ), $_product->get_name(), wc_get_formatted_cart_item_data( $cart_item ) ), $cart_item, $cart_item_key );
                $data_arr[$cart_item_key]['name'] = apply_filters('dvin_woocommerce_quote_item_name',$item_name);
                $data_arr[$cart_item_key]['product-link']=esc_url( $prod_permalink);
                //if price to be shown
                if(isset($arr_cols['price']))
                    $data_arr[$cart_item_key]['price'] = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                //sku
                if(isset($arr_cols['sku']))
                    $data_arr[$cart_item_key]['sku'] = $_product->get_sku();
                //qty
                if(isset($arr_cols['quantity'])){
                    if(!$for_email) {
                        $product_quantity = woocommerce_quantity_input( array(
                                        'input_name'  => "cart[{$cart_item_key}][qty]",
                                        'input_value' => $cart_item['quantity'],
                                        'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
                                        'min_value'   => '0'
                                    ), $_product, false );
                        $data_arr[$cart_item_key]['quantity'] = apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
                    } else {
                        $data_arr[$cart_item_key]['quantity'] = $cart_item['quantity'];
                    }
                    //if defined and handle even arrays in case of grouped products
                    if(is_array($cart_item['quantity']))
                        foreach($cart_item['quantity'] as $qty)
                            $grand_total_qty += $qty;
                    else
                         $grand_total_qty += $cart_item['quantity'];
                    }
                //subtotal
                if(isset($arr_cols['subtotal'])) {
                    $data_arr[$cart_item_key]['subtotal'] = $subtotal = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );

        $pattern_str = "/[^\d".trim(get_option('woocommerce_price_decimal_sep'))."]/";
		    $subtotal_decoded_str = preg_replace( $pattern_str, '', html_entity_decode($subtotal));
        if(trim(get_option('woocommerce_price_decimal_sep'))==',')
          $grand_total_price += floatval(str_replace(',', '.', str_replace('.', '', $subtotal_decoded_str)));
        else
          $grand_total_price +=floatval($subtotal_decoded_str);
                }
			}//endif product exists and quantity greater than zero
        }//end of foreach
        return $data_arr; //return the array
     }//end of function
      //determine whether to show price column or not in email based on settings.
     public static function show_price_column_email($atts) {
         global $dvin_wcql_settings,$atts;
         if((isset($dvin_wcql_settings['remove_price_col'] ) && $dvin_wcql_settings['remove_price_col'] == 'on') || ((isset($dvin_wcql_settings['show_price_login'] ) && $dvin_wcql_settings['show_price_login'] == 'on') && !is_user_logged_in()) || (isset($atts['show_price']) && $atts['show_price']))
          return true;
        return false;
     }
 }//end of class
?>
