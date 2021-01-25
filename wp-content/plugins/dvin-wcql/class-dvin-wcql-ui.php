<?php

/**
 * Dvin_Wcql_UI class, Handles UI Quotelist
 */
class Dvin_Wcql_UI {
 	/**
  * function get_message_poupup_container, builds the messagepopup container
	* @static
  */
	public static function get_message_poupup_container($left=250,$top=190) {
		//return '<script>jQuery("body").prepend(\'<div id="dvin_messagecontainer"><div id="dvin-message-popup" class="dvin-message-popup" style="display: none; "><div class="dvin-message" id="dvin-message"></div></div></div>\');</script>';
	}
/**
  * function get_qlist_link, builds the quotelist button/link to place it in single product page
	* @static
	* @return string HTML of quotelist button/link
  */
	public static function get_qlist_link($product_id,$url,$prod_type,$prod_exists=false) {
		global $dvin_wcql_settings,$product;

        $label 	= apply_filters('add_to_quotelist_text', __('Add to Quote',"dvinwcql"));
		$qlistlink = '';
		$qlistlink ='<div class="addquotelistlink">';
		 $qlistlink .= '<div class="quotelistadd_prodpage" ';
		//if($prod_type =='variable' || $prod_exists) {
    if($prod_exists) {
			$qlistlink .=' style="display:none;"';
		}

        //button wrap
        $qlistlink .= '><span class="dvin_wcql_btn_wrap">';

		//determine product ID value
		if($prod_type =='variable' ) {
			$product_id = 0;
		}
		//if Quotelist link appear as add to cart as appears in theme
		if($dvin_wcql_settings['link_sameas_addtocart'] == 'on') {
			$style=""; //defines or stores the style
			/** if not default colors **/
			if($dvin_wcql_settings['link_sameas_addtocart_default_colors'] != 'on') {
				//handle background color
				if($dvin_wcql_settings['link_bgcolor']!=''){
					$style.='background:'.$dvin_wcql_settings['link_bgcolor'].';';
				}
				//handle font color
				if($dvin_wcql_settings['link_fontcolor']!=''){
					$style .=' color:'.$dvin_wcql_settings['link_fontcolor'].';';
				}
				$style = $style!=''?' style="'.$style.'"':'';
			}//end if
            $qlistlink .= self::get_button_link($style,$label,true,'button');
		} else { //display as normal link
			$qlistlink .=self::get_button_link('',$label,true, 'link');
		}

        //close button wrap
        $qlistlink .= '</span>';

//common code to both instances
$addtoquote_removequote_str = "";
		$qlistlink .='<span class="dvin_wcql_btn_ajaxico_wrap"><img style="display: none;border:0; width:16px; height:16px;" src="'.DVIN_QLIST_PLUGIN_WEBURL.'images/ajax-loader.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="Working..."/></span></div><div class="quotelistaddedbrowse_prodpage" style="display:none;">';

		$addtoquote_removequote_str .='<a class="quotelist-added-icon"  href="'.$url.'">'.apply_filters('dvin_prodaddedtoquote_text',__('Product Added to Quote','dvinwcql')).'</a><br/>';

        if(!dvin_wcql_addons())
            $addtoquote_removequote_str .='<a class="quotelist-remove-icon removefromprodpage" rel="nofollow" data-product_id="'.$product->get_id().'" href="javascript:void(0)" >'.apply_filters('dvin_prodremovefromquote_text',__('Remove from Quote',"dvinwcql")).'</a>';
        $qlistlink .=apply_filters('dvin_addtoquote_removequote_text',$addtoquote_removequote_str).'</div>';

		$display_style = $prod_exists?'block;':'none;';
		//exists
		$qlistlink .='<div class="quotelistexistsbrowse_prodpage" style="display:'.$display_style.'">';
		$addtoquote_removequote_str='<a class="quotelist-added-icon"  href="'.$url.'">'.apply_filters('dvin_prodaddedtoquote_text',__('Product Added to Quote','dvinwcql')).'</a><br/>';

         if(!dvin_wcql_addons())
            $addtoquote_removequote_str .='<a class="quotelist-remove-icon removefromprodpage" rel="nofollow" data-product_id="'.$product->get_id().'" href="javascript:void(0)">'.apply_filters('dvin_prodremovefromquote_text',__('Remove from Quote',"dvinwcql")).'</a>';
        $qlistlink .=apply_filters('dvin_addtoquote_removequote_exists_text',$addtoquote_removequote_str).'</div>';

		$qlistlink .='<div style="clear:both"></div><div class="quotelistaddresponse"></div></div>';
		//clear
		$qlistlink .='<div class="clear"></div>';
		return $qlistlink;
	}
	/**
  	* function get_qlist_shoplink, builds the quotelist button/link to place it in single product page
	* @static
	* @return string HTML of quotelist button/link 
  	*/
	public static function get_qlist_shoplink($url,$prod_type,$prod_exists=false, $prod_id='') {
		global $dvin_wcql_settings,$product;

		//define the product object
		$product = wc_get_product($prod_id);
		//print_r($product);

        $style= ''; //stores the styles
		$label 	= apply_filters('add_to_quotelist_text', __('Add to Quote',"dvinwcql"));
		$qlistlink ='<div id="'.$prod_id.'" class="addquotelistlink">';
		 $qlistlink .= '<div class="quotelistadd" ';
		if($prod_type !='simple' || $prod_exists) {
			$qlistlink .=' style="display:none;"';
		}
		//if Quotelist link appear as add to cart as appears in theme
		if($dvin_wcql_settings['link_sameas_addtocart'] == 'on') {
			/** if not default colors **/
			if($dvin_wcql_settings['link_sameas_addtocart_default_colors'] != 'on') {
				//handle background color
				if($dvin_wcql_settings['link_bgcolor']!=''){
					$style.='background:'.$dvin_wcql_settings['link_bgcolor'].';';
				}
				//handle font color
				if($dvin_wcql_settings['link_fontcolor']!=''){
					$style .=' color:'.$dvin_wcql_settings['link_fontcolor'].';';
				}
			}//end if
			$style = $style!=''?' style="'.$style.'"':'';
			$qlistlink .='><span class="dvin_wcql_shopbtn_wrap">'.self::get_button_link($style,$label,false).'</span>';
		} else {
        	$qlistlink .='><span class="dvin_wcql_shopbtn_wrap">'.self::get_button_link($style,$label,false,'link').'</span>';
		}

		//common code to both instances
		$qlistlink .='<span class="dvin_wcql_shopbtn_ajaxico_wrap"><img style="display: none;border:0; width:16px; height:16px;" src="'.DVIN_QLIST_PLUGIN_WEBURL.'images/ajax-loader.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="Working..."/></span></div><div class="quotelistaddedbrowse" style="display:none;">';
		$qlistlink .='<a href="'.$url.'">'.apply_filters('in_quotelist_text',__('In Quotelist','dvinwcql')).'</a></div>';
		$display_style = $prod_exists?'block;':'none;';
		//exists
		$qlistlink .='<div class="quotelistexistsbrowse" style="display:'.$display_style.';">';
		$qlistlink .='<a href="'.$url.'">'.apply_filters('in_quotelist_text',__('In Quotelist','dvinwcql')).'</a></div>';
		$qlistlink .='<div style="clear:both"></div><div class="quotelistaddresponse"></div></div>';
		//clear
		$qlistlink .='<div class="clear"></div>';
		return $qlistlink;
	}

	
    public static function get_button_link($style,$label,$prod_page=false,$link_type='button'){

		global $product,$prod_id;
		
		//if $product_id is defined
		//if(!empty($product_id))
		//	$product = wc_get_product($product_id);

            //if product page
            if($prod_page && $product->get_type() == 'variable')
                $prodpagestyle_str = "addquotelistbutton_prodpage disabled"; //events bind to this
            else if($prod_page)
                $prodpagestyle_str = "addquotelistbutton_prodpage"; //events bind to this
            else
                $prodpagestyle_str = "addquotelistbutton";

            //if button
            if($link_type =='button') {
                $str = '<button rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" type="button" class="button alt %s product_type_%s" '.$style.'>%s</button>';
            }else {//normal link
                 $str = '<a href="javascript:void(0);" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button %s product_type_%s" '.$style.'>%s</a>';
			}

			return sprintf($str, esc_attr( $product->get_id() ),
			
            esc_attr( $product->get_sku() ),
            esc_attr( isset( $quantity ) ? $quantity : 1 ),
            $prodpagestyle_str,
            esc_attr( $product->get_type() ),
             $label);
    }
 }//end of class
 ?>
