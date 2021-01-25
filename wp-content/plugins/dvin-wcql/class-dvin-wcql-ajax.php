<?php

Class Dvin_Wcql_Ajax {
    /**
	 * Get a refreshed cart fragment
	 */
	public static function get_refreshed_fragments($arr) {
        global $dvin_qlist_products;

        $data = array();
		if($arr['count_refresh'] == "true")
			$data['fragments']['div#dvin-quotelist-count']='<div id="dvin-quotelist-count">'.dvin_get_wcql_count().'<div>';
        // Get mini cart
        if($arr['widget_refresh'] == "true") {
            ob_start();
            wcql_get_mini_qlist();
            $mini_qlist = ob_get_clean();
            $data['fragments']['div#quotelist-widget']='<div id="quotelist-widget">'.$mini_qlist.'</div>';
        }
        return $data;
	}

    public static function widget_refresh() {
        global $dvin_qlist_products;
        $_POST['count_refresh'] = isset($_POST['count_refresh'])?$_POST['count_refresh']:'false';
        $_POST['widget_refresh'] = isset($_POST['widget_refresh'])?$_POST['widget_refresh']:'false';
        $data = self::get_refreshed_fragments(array('count_refresh'=>$_POST['count_refresh'],'widget_refresh'=>$_POST['widget_refresh']));
		$data['status'] = 'success';
		wp_send_json($data);
		exit;
    }
	public static function find_prod_in_qlist() {

		global $dvin_qlist_products;
        $dvin_wcql_obj = new Dvin_Wcql($_POST);//create object
		if($dvin_wcql_obj->isExists($_POST['prod_id'],$_POST['variation_id']))
			echo "true";
		else
			echo "false";
		exit;
	}
    public static function add_to_qlist() {
        global $dvin_qlist_products,$post_id;

		//if form serialized data exists, merge with POST vars
		if(isset($_POST['form_serialize_data'])) {
			parse_str($_POST['form_serialize_data'],$arr);
			$_POST = array_merge($_POST,$arr);

			//check if it is a simple product
			if(!isset($_POST['variable_id']) && isset($_POST['add-to-cart']))
				$_POST['product_id'] = $_POST['add-to-cart'];
		}


        $dvin_wcql_obj = new Dvin_Wcql($_POST);//create object

        //add to quotelist
        list($generated_item_key, $ret_val) = $dvin_wcql_obj->add();

        if($ret_val=="true") {
            $_POST['count_refresh'] = isset($_POST['count_refresh'])?$_POST['count_refresh']:'false';
            $_POST['widget_refresh'] = isset($_POST['widget_refresh'])?$_POST['widget_refresh']:'false';
            $data = self::get_refreshed_fragments(array('count_refresh'=>$_POST['count_refresh'],'widget_refresh'=>$_POST['widget_refresh']));
            $data['status'] = 'success';
            $data['product_id']= $generated_item_key;
            $data['msg'] = __("Successfully added","dvinwcql");
        }elseif($ret_val=="exists") {
            $data = self::get_refreshed_fragments(array('count_refresh'=>"false",'widget_refresh'=>"false"));
            $data['status'] = 'exists';
            $data['product_id']= $generated_item_key;
            $data['msg'] = __("Already Exists. Browse Quotelist.","dvinwcql");
        }elseif(count($dvin_wcql_obj->errors_arr)>0) {
            $data = self::get_refreshed_fragments(array('count_refresh'=>"false",'widget_refresh'=>"false"));
            $data['status'] = 'fail';
            $data['msg'] = $dvin_wcql_obj->errors_arr[0];
        }

        wp_send_json( $data );
        exit;
    }
	public static function remove_from_qlist() {

        global $dvin_qlist_products;

		//remove
		 Dvin_Wcql::remove($_POST['product_id']);
		wc_add_notice(apply_filters('dvin_wcql_listupdated',__('List Updated.','dvinwcql')));
		echo "success";
	}
	public static function remove_from_page() {

        global $dvin_qlist_products;

        //remove
        Dvin_Wcql::remove($_POST['product_id']);
        $_POST['count_refresh'] = isset($_POST['count_refresh'])?$_POST['count_refresh']:'false';
        $_POST['widget_refresh'] = isset($_POST['widget_refresh'])?$_POST['widget_refresh']:'false';
         $data = self::get_refreshed_fragments(array('count_refresh'=>$_POST['count_refresh'],'widget_refresh'=>$_POST['widget_refresh']));

        wp_send_json( $data );
	}

	public static function update_list() {

      global $dvin_qlist_products;

			//update the list
			if(count($_POST['cart'])>0) {
				foreach($_POST['cart'] as $key => $val) {
						$dvin_qlist_products[$key]['quantity']=(int)$val['qty'];
				}
			}

      Dvin_Wcql::set_qlist_to_session($dvin_qlist_products);
			wc_add_notice(apply_filters('dvin_wcql_listupdated',__('List Updated.','dvinwcql')));
	}

	public static function send_request() {
        global $dvin_qlist_products;

	 	$response_arr = array();
		$dvin_wcql_obj = new Dvin_Wcql($_POST);//create object
		if(isset($_POST['form_serialize_data'])) {
			parse_str($_POST['form_serialize_data'],$arr);
      if(is_array($arr))
			 $_POST = array_merge($_POST,$arr);
		}

		//update the list
        if(count($_POST['cart'])>0) {
            foreach($_POST['cart'] as $key => $val) {
                    $dvin_qlist_products[$key]['quantity']=(int)$val['qty'];
            }
        }

        Dvin_Wcql::set_qlist_to_session($dvin_qlist_products);


		//validate the form, send back if errors found
		$errors = self::dvin_qlist_validate_form();
		if(count($errors)>0){
			$response_arr['status']='error';
			$response_arr['msg'] = join($errors,'<br/>');
			wp_send_json($response_arr);
			exit;
		}


		//get settings to use further
		$dvin_wcql_settings = maybe_unserialize(get_option('dvin_wcql_settings'));

		 //create order if enabled
		if(isset($dvin_wcql_settings['create_order']) && $dvin_wcql_settings['create_order'] == 'on')
			$dvin_wcql_obj->order_id = dvin_qlist_create_order($_POST);

        //now prepare and send email
		$return_arr = self::dvin_qlist_sendreq_func($dvin_wcql_obj);
		if($return_arr['status'] == "success" ) {
			if(isset($dvin_wcql_settings['dvin_wcql_redirect_url']) && !empty($dvin_wcql_settings['dvin_wcql_redirect_url'])) {
				$return_arr['status']='redirect';
				$return_arr['redirect_url'] = $dvin_wcql_settings['dvin_wcql_redirect_url'];
				Dvin_Wcql::set_qlist_to_session(array());
				wp_send_json($return_arr);
				exit;
			} else {

				$return_arr['status']='success';
				//clear the products from session
        Dvin_Wcql::set_qlist_to_session(array());
				wp_send_json($return_arr);
				exit;
			}
		}
		wp_send_json($return_arr);
		exit;
	}
	//validates the form
	public static function dvin_qlist_validate_form() {

        global $dvin_qlist_products;

	    $errors = array();

		//validate the form
		if(isset($_POST['req_name']) && trim($_POST['req_name'])==''){
			$errors[] = __("Please enter ",'dvinwcql').__('Name','dvinwcql');
		}
		if(isset($_POST['req_email']) && trim($_POST['req_email'])==''){
			$errors[] = __("Please enter ",'dvinwcql').__('Email','dvinwcql');
		} else if(isset($_POST['req_email'])) {
			$regex = '/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,63})$/';
			// Run the preg_match() function on regex against the email address
			if (!preg_match($regex, $_POST['req_email'])) {
				 $errors[] = "Please enter valid ".__('Email','dvinwcql');
			}
		}
		//apply filters for to do validations for the custom fileds
		$errors = apply_filters('dvin_wcql_custom_fields_validation',$errors);
		//include the validation file if exists
		if(file_exists(TEMPLATEPATH . '/'. Dvin_Wcql::template_path().'form-validation.php')) {
			include(TEMPLATEPATH . '/'. Dvin_Wcql::template_path().'form-validation.php');
		}elseif(file_exists(STYLESHEETPATH . '/'. Dvin_Wcql::template_path().'form-validation.php')) {
			include( STYLESHEETPATH . '/'. Dvin_Wcql::template_path().'form-validation.php');
		}
	    return $errors;
	}

	//sends the email
	public static function dvin_qlist_sendreq_func($dvin_wcql_obj) {

        global $dvin_qlist_products;

		//if no products in the list
		if(!Dvin_Wcql::isempty_quotelist($dvin_qlist_products)) {
				$fragments_data_arr = array();
				//send an email to Admin
				if($dvin_wcql_obj->send_email()) {
					 $dvin_qlist_products = array();
					  $fragments_data_arr = self::get_refreshed_fragments(array('count_refresh'=>$_POST['count_refresh'],'widget_refresh'=>$_POST['widget_refresh']));
					 $data['status'] = 'success';
		             $data['msg'] = __("Email Sent Successfully","dvinwcql");
				}else{
					$data['status'] = 'error';
		      $data['msg'] = __("There was a problem sending your message. Please try again.","dvinwcql");
				}
		}
		else {
				$data['status'] = 'error';
	      $data['msg'] = __("Please select products to send.","dvinwcql");
		}
		return array_merge($data,$fragments_data_arr);
	}
}
?>
