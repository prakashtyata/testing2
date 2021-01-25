<?php

/**
 * Dvin_Wcql_Shortcodes class, Handles the Listing shortcodes
 */
class Dvin_Wcql_Shortcodes {

	/**
     * function listing, retrieves quotelist products
	 * @param $atts Array - an associative array of attributes (preferences)
	 * @param $content  String -  the enclosed content
	 * @param $code String -  the shortcode name
	 * @code
	 * @static
     */
	static function listing($atts, $content = null, $code = "") {
		global $dvin_wcql_settings;
        $dvin_qlist_products = WC()->session->get('dvin_qlist_products');

		ob_start();
		echo '<div class="woocommerce woocommerce-cart">';
		$qlist = array();
		if((isset($dvin_qlist_products) && count($dvin_qlist_products)>0)) {
			$qlist_count[0]['cnt'] = count($dvin_qlist_products);
			$qlist = $dvin_qlist_products;
		
			//include the template file
			if(file_exists(TEMPLATEPATH . '/'. Dvin_Wcql::template_path().'templates/quotelist.php')) {
				include(TEMPLATEPATH . '/'. Dvin_Wcql::template_path().'templates/quotelist.php');
			}elseif(file_exists(STYLESHEETPATH . '/'. Dvin_Wcql::template_path().'templates/quotelist.php')) {
				include(STYLESHEETPATH . '/'. Dvin_Wcql::template_path().'templates/quotelist.php');
			}else{
				require('templates/quotelist.php');
			}
		} else {

			//Filter for empty Quote list
			$empty_redirect_url = apply_filters('dvin_qlist_empty_redirect_url','');
			if(!empty($empty_redirect_url)) {
				ob_end_clean();
				?>
				<script>
					window.location.href= '<?php echo $empty_redirect_url?>';
				</script>
				<?php
				exit;
			}
           

			//include the template file
			if(file_exists(TEMPLATEPATH . '/'. Dvin_Wcql::template_path().'templates/list-empty.php')) {
				include(TEMPLATEPATH . '/'. Dvin_Wcql::template_path().'templates/list-empty.php');
			}elseif(file_exists(STYLESHEETPATH . '/'. Dvin_Wcql::template_path().'templates/list-empty.php')) {
				include(STYLESHEETPATH . '/'. Dvin_Wcql::template_path().'templates/list-empty.php');
			}else{
				require('templates/list-empty.php');
			}
		}
		echo '</div>';
		 return ob_get_clean();
	}

	/**
     * function Form, displays the form
	 * @param $atts Array - an associative array of attributes (preferences)
	 * @param $content  String -  the enclosed content
	 * @param $code String -  the shortcode name
	 * @code
	 * @static
     */
	static function form($atts, $content = null, $code = "") {

        $dvin_wcql_settings = maybe_unserialize(get_option('dvin_wcql_settings')); //get settings
        $dvin_qlist_products = Dvin_Wcql::get_qlist_from_session();//get list from session
        $always_display_form=apply_filters('dvin_wcql_always_display_form',false); //always display form or not

        //if using third party plugin
        if(self::isUsingThirdPartyPlugin()) {

            //if no products
            if(Dvin_Wcql::isempty_quotelist($dvin_qlist_products)) {
								//display form whether products in the list or not
                if($always_display_form) {
                    return self::showForm();
                } else {
                          ob_start();
		                 echo '<div class="woocommerce">';
                    	//include the template file
                        if(file_exists(TEMPLATEPATH . '/'. Dvin_Wcql::template_path().'templates/list-empty.php')) {
                            include(TEMPLATEPATH . '/'. Dvin_Wcql::template_path().'templates/list-empty.php');
                        }elseif(file_exists(STYLESHEETPATH . '/'. Dvin_Wcql::template_path().'templates/list-empty.php')) {
                            include(STYLESHEETPATH . '/'. Dvin_Wcql::template_path().'templates/list-empty.php');
                        }else{
                            require('templates/list-empty.php');
                        }
                        echo '</div>';
		              return ob_get_clean();
                }
            } else {

                return self::showForm();
            }


        } else { //if no third party

            //if no products
            if(Dvin_Wcql::isempty_quotelist($dvin_qlist_products)) {
                 //determine whether to show form or not
                 if(!$always_display_form){
                        return;
                 } else {
                     return self::showForm();
                 }
            } else {
                     return self::showForm();
            }
        }

	}

	/**
     * function button, displays quotelist button on shop page
	 * @param $atts Array - an associative array of attributes (preferences)
	 * @param $content  String -  the enclosed content
	 * @param $code String -  the shortcode name
	 * @codess
	 * @static
     */
 static function shopbutton($atts, $content = null, $code = "") {
		global $dvin_wcql_settings,$dvin_qlist_products,$product;
		

        //if product_id attribute is not defined, get it from product object
        if(!isset($atts['product_id']))
			$atts['product_id'] = $product->get_id();
			
		ob_start();
		echo '<div class="woocommerce">';
	 	echo dvin_addquotelist_button(true,$atts['product_id']);
		echo '</div>';
 		return ob_get_clean();
	}

    	/**
     * function button, displays quotelist button on shop page
	 * @param $atts Array - an associative array of attributes (preferences)
	 * @param $content  String -  the enclosed content
	 * @param $code String -  the shortcode name
	 * @codess
	 * @static
     */
 static function button($atts, $content = null, $code = "") {
        global $dvin_wcql_settings,$dvin_qlist_products,$product;

        //if product_id attribute is not defined, get it from product object
        if(!isset($atts['product_id']))
            $atts['product_id'] = $product->get_id();
		ob_start();
		echo '<div class="woocommerce">';
	 	echo dvin_addquotelist_button(false,$atts['product_id']);
		echo '</div>';
 		return ob_get_clean();
	}
    /**
     * function quotelist for the emails
	 * @param $atts Array - an associative array of attributes (preferences)
	 * @param $content  String -  the enclosed content
	 * @param $code String -  the shortcode name
	 * @code
	 * @static
     */
	static function quotelist($atts, $content = null, $code = "") {
        global $dvin_wcql_settings;
        $dvin_qlist_products = Dvin_Wcql::get_qlist_from_session();
        return serialize(get_qlist_table($atts));
	}

       /**
     * function quotelisttable for the emails
	 * @param $atts Array - an associative array of attributes (preferences)
	 * @param $content  String -  the enclosed content
	 * @param $code String -  the shortcode name
	 * @code
	 * @static
     */
	static function quotelisttable($atts, $content = null, $code = "") {
        global $dvin_wcql_settings,$dvin_qlist_products;
         list(,$table) = get_qlist_table($atts);
         return $table;
	}

    //determine whether using third party plugin EXCEpT ContactForm7 because it follows same config as normal form
    private static function isUsingThirdPartyPlugin() {
        global $dvin_wcql_settings;

        if(isset($dvin_wcql_settings['use_gravity_forms']) && $dvin_wcql_settings['use_gravity_forms'] == 'on' ||
           isset($dvin_wcql_settings['use_formidable_forms']) && $dvin_wcql_settings['use_formidable_forms'] == 'on'
          )
            return true;
        else
            return false;
    }

    //show the form
    private static function showForm() {
        global $dvin_wcql_settings;

        //show the selected form, you can modify dynamically with the filters
        if(isset($dvin_wcql_settings['use_gravity_forms']) && $dvin_wcql_settings['use_gravity_forms'] == 'on') {
            $ajax_gf=apply_filters('dvin_wcql_GF_ajax',"false");
            $gf_form = apply_filters('dvin_wcql_GF_form',$dvin_wcql_settings['gravity_form_select']);
			return do_shortcode('[gravityform id="'.$gf_form.'" title=false description=false ajax='.$ajax_gf.']');
		} else if(isset($dvin_wcql_settings['use_formidable_forms']) && $dvin_wcql_settings['use_formidable_forms'] == 'on') {
            $formidable_form = apply_filters('dvin_wcql_formidable_form',$dvin_wcql_settings['formidable_form_select']);
			return do_shortcode('[formidable id="'.$formidable_form.'"]');
		}else if(isset($dvin_wcql_settings['use_contactform7']) && $dvin_wcql_settings['use_contactform7'] == 'on') {
            $contactform7_form = apply_filters('dvin_wcql_contactform7_form',$dvin_wcql_settings['contactform7_form_select']);
			return do_shortcode('[contact-form-7 id="'.$contactform7_form.'"]');
		}else {

            ob_start();

            //point to the default form
            $dvin_wcql_form = apply_filters('dvin_wcql_form',TEMPLATEPATH . '/'. Dvin_Wcql::template_path().'templates/form.php');


			if(file_exists($dvin_wcql_form)) {
				include($dvin_wcql_form);
			}elseif(file_exists(STYLESHEETPATH . '/'. Dvin_Wcql::template_path().'templates/form.php')) {
				include(STYLESHEETPATH . '/'. Dvin_Wcql::template_path().'templates/form.php');
			}else{
				echo '<div class="woocommerce">';
				include('templates/form.php');
				echo '</div>';
			}
			return ob_get_clean();
		}
    }

}
add_shortcode("dvin-wcql-listing", array("dvin_wcql_Shortcodes", "listing"));
add_shortcode("dvin-wcql-form", array("dvin_wcql_Shortcodes", "form"));
add_shortcode("dvin-wcql-shopbutton", array("dvin_wcql_Shortcodes", "shopbutton"));
add_shortcode("dvin-wcql-button", array("dvin_wcql_Shortcodes", "button"));
add_shortcode("quotelist", array("dvin_wcql_Shortcodes", "quotelist"));
add_shortcode("quotelisttable", array("dvin_wcql_Shortcodes", "quotelisttable"));
