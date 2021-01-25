jQuery( function( $ ) {

	//when variation_id changes, take appropriate action on quotelist button (show/hide)
	$('[name|="variation_id"]').change(function() {

		if($('[name|="variation_id"]').val()!='') {

			//find whether it is already exists in quotelist
			var data = {action: 'find_prod_in_qlist'};
			data['prod_id'] = $('[name|="product_id"]').val();
			data['variation_id'] = $('[name|="variation_id"]').val();

			$.post( dvin_wcql_ajax_url, data, function( response ) {

				if(response == "true") {

					$('.quotelistadd_prodpage').css('display','none');
					$('.quotelistexistsbrowse_prodpage').css('display','block');
					$('.quotelistaddedbrowse_prodpage').css('display','none');

				} else {

					$('.quotelistadd_prodpage').css('display','block');
					$('.quotelistexistsbrowse_prodpage').css('display','none');
					$('.quotelistaddedbrowse_prodpage').css('display','none');

				}

			});

			$('.quotelistaddresponse').html('');

			if(dvin_wcql_disable_insteadof_hide_button == "true")
				$('.addquotelistbutton_prodpage').removeClass( 'disabled' ).addClass( 'enabled' );
		} else {
			$('.quotelistadd_prodpage').css('display','block');
			$('.quotelistexistsbrowse_prodpage').css('display','none');
			$('.quotelistaddedbrowse_prodpage').css('display','none');

			if(dvin_wcql_disable_insteadof_hide_button == "true")
				$('.addquotelistbutton_prodpage').removeClass( 'enabled' ).addClass( 'disabled' );
			else
				$('.quotelistadd_prodpage').css('display','none');
				$('.quotelistexistsbrowse_prodpage').fadeOut();
		}

	});
	//Ajax remove from qlist
	$( document ).on( 'click', '.removeproductqlist', function(e) {
		var $thisbutton = $( this );
		if ( ! $thisbutton.attr( 'data-product_id' ) )
			return true;
        var data = {action: 'remove_from_qlist'};
        $.each( $thisbutton.data(), function( key, value ) {
            data[key] = value;
        });
        // Trigger event
        $( 'body' ).trigger( 'remove_from_qlist', [ $thisbutton, data ] );
        // Ajax action
        $.post( dvin_wcql_ajax_url, data, function( response ) {
            if ( ! response )
                return;
            $( this ).closest('tr').remove();
            location.reload(true);

	});
});
	// Ajax add to qlist from shop
	$( document ).on( 'click', '.addquotelistbutton_prodpage', function(e) {

			var $this = $( this );

			if ( $this.is('.disabled') ) {
				event.preventDefault();
				return false;
			}

        var data = {action: 'add_to_qlist_from_prodpage'};

        // AJAX add to cart request
		var $thisbutton = $( this );

         //check for the data attribute
        if ( ! $thisbutton.attr( 'data-product_id' ) )
         return true;

        //get the data into the array
        $.each( $thisbutton.data(), function( key, value ) {
        data[key] = value;
        });

        if($('form.cart').length){
            if($thisbutton.closest('form.cart').serialize()!='') {
                data['form_serialize_data']=$thisbutton.closest('form.cart').serialize();
            } else if($thisbutton.closest('.addquotelistlink').prev("form.cart").serialize()!='') {
    data['form_serialize_data']=$thisbutton.closest('.addquotelistlink').prev("form.cart").serialize();
            } else {
                data['form_serialize_data']=$('form.cart').serialize();
            }
        }

		//check if element(widget DIV) exists and no redirect...need refresh
		if(dvin_quotelist_refresh_afteradd_url !='') {
			data['widget_refresh'] = "false";
			data['count_refresh'] = "false";
		} else {
			if($('div#quotelist-widget').length >0)
				data['widget_refresh'] = "true";
			else
				data['widget_refresh'] = "false";
			if($('div#dvin-quotelist-count').length >0)
				data['count_refresh'] = "true";
			else
				data['count_refresh'] = "false";
		}
 		// Trigger event
		$( 'body' ).trigger( 'adding_to_qlist', [ $thisbutton, data ] );
		$thisbutton.parent().parent().parent().find('.ajax-loading-img').fadeIn();
		// Ajax action
		$.post( dvin_wcql_ajax_url, data, function( response ) {
			if ( !response )
				return;
			var loading = $('.ajax-loading-img');
			loading.fadeOut();
            fragments = response.fragments;
            // Replace fragments
            if ( fragments ) {
               $.each( fragments, function( key, value ) {
                        $( key ).replaceWith( value );
              });
            }

			//if refresh, no need to hide or display message
			if(dvin_quotelist_refresh_afteradd_url !='') {
				window.location = dvin_quotelist_refresh_afteradd_url;
			} else {

                $thisbutton.parent().parent().parent().children('.removefromprodpage').css('display','none');                //
                $thisbutton.parent().parent().parent().children('.quotelistaddedbrowse_prodpage').css('display','block');

                if(dvin_wcql_addons == '1') {
                    $('.quotelistaddedbrowse_prodpage').fadeOut(4000);
                } else {
                        $thisbutton.parent().parent().parent().children('.quotelistadd_prodpage').css('display','none');
                        $('a.removefromprodpage').attr("data-product_id",response.product_id);
                }
				return true;
			}
		});
		return false;
	});
	// Ajax add to qlist from shop
	$( document ).on( 'click', '.addquotelistbutton', function(e) {
		// AJAX add to cart request
		var $thisbutton = $( this );
		if ( $thisbutton.is( '.product_type_simple' ) || $thisbutton.is( '.product_type_external' )) {
            if ( ! $thisbutton.attr( 'data-product_id' ) )
				return true;
			var data = {action: 'add_to_qlist'};
			$.each( $thisbutton.data(), function( key, value ) {
				data[key] = value;
        	});
			//check if element(widget DIV) exists and no redirect...need refresh
			if(dvin_quotelist_refresh_afteradd_url !='') {
				data['widget_refresh'] = "false";
				data['count_refresh'] = "false";
			} else {
				if($('div#quotelist-widget').length >0)
					data['widget_refresh'] = "true";
				if($('div#dvin-quotelist-count').length >0)
					data['count_refresh'] = "true";
			}
            // Trigger event
			$( 'body' ).trigger( 'adding_to_qlist', [ $thisbutton, data ] );
			$('#'+$thisbutton.attr( 'data-product_id')).find('.ajax-loading-img').fadeIn();
			// Ajax action
			$.post( dvin_wcql_ajax_url, data, function( response ) {
				if ( ! response )
					return;

				var loading = $('#'+$thisbutton.attr( 'data-product_id')).children('.ajax-loading-img');
					loading.fadeOut();

				//if refresh, no need to hide or display message
				if(dvin_quotelist_refresh_afteradd_url =='') {

                    fragments = response.fragments;
                    // Replace fragments
                    if ( fragments ) {
                        $.each( fragments, function( key, value ) {
                            $( key ).replaceWith( value );
                        });
                    }

					$('#'+data['product_id']).children('.quotelistadd').css('display','none');
					$('#'+data['product_id']).children('.quotelistaddedbrowse').css('display','block');
				} else {
					window.location = dvin_quotelist_refresh_afteradd_url;
					return;
				}
			});
			return false;
		}
		return true;
	});
	// Ajax remove product from product page
	$( document ).on( 'click', '.removefromprodpage', function(e) {
		var data = {action: 'remove_from_page'};

        // AJAX add to cart request
		var $thisbutton = $( this );

        //check for the data attribute
        if ( ! $thisbutton.attr( 'data-product_id' ) )
         return true;

				 //get variation data
				 if($('[name|="variation_id"]').length && $('[name|="variation_id"]').val() >0) {
				 	var_id = $('[name|="variation_id"]').val();
				 	$thisbutton.attr( 'data-product_id', var_id);
				 }

        //get the data into the array
        $.each( $thisbutton.data(), function( key, value ) {
        data[key] = value;
        });



        if($('div#quotelist-widget').length >0)
            data['widget_refresh'] = "true";
        else
            data['widget_refresh'] = "false";
        if($('div#dvin-quotelist-count').length >0)
            data['count_refresh'] = "true";
        else
            data['count_refresh'] = "false";

 		// Trigger event
		$( 'body' ).trigger( 'remove_from_qlist', [ $thisbutton, data ] );
		// Ajax action
		$.post( dvin_wcql_ajax_url, data, function( response ) {
			if ( ! response )
				return;
                fragments = response.fragments;
                // Replace fragments
                if ( fragments ) {
                    $.each( fragments, function( key, value ) {
                        $( key ).replaceWith( value );
                    });
            }

			$thisbutton.parent().parent().children('.quotelistadd_prodpage').css('display','block');
			$thisbutton.parent().parent().children('.quotelistaddedbrowse_prodpage').css('display','none');
			$thisbutton.parent().parent().children('.quotelistexistsbrowse_prodpage').css('display','none');

		});
		return false;
	});

    //if list has some products
	if($('#dvin-quotelist-count').length>0) {
		//if DIV for count exists, update it
		$('#dvin-quotelist-count').html(dvin_quotelist_count);
	}
	if($('div#quotelist-widget').length>0) {
		refresh_quotelist_widget();
	}

});

function ajax_req_update_quote() {
    serialized_data = jQuery( "input[name^='cart']" ).serialize()+'&action=update_list';
    jQuery.ajax({
     type: 'POST',
      url: dvin_wcql_ajax_url,
      data: serialized_data,
      success: function( response ) {
        location.reload(true);
        }
    });
}
//handles ajax call post form
function call_ajax_submitform_to_admin(url) {

    var data = {action: 'send_request'};
    data['form_serialize_data']=jQuery('.qlist').serialize();

    if(data['form_serialize_data']=='')
        return;
    else
       jQuery('.ajax-loading-img').fadeIn();

        if(jQuery('div#quotelist-widget').length >0)
            data['widget_refresh'] = "true";
        else
            data['widget_refresh'] = "false";
        if(jQuery('div#dvin-quotelist-count').length >0)
            data['count_refresh'] = "true";
        else
            data['count_refresh'] = "false";
    // Ajax action
    jQuery.post( dvin_wcql_ajax_url, data, function( response ) {
        if ( ! response )
            return;
        var msg = jQuery('#dvin-message-popup');
        var loading = jQuery('.ajax-loading-img');
        loading.fadeOut();
        if(response.status == "success") {
            fragments = response.fragments;
            // Replace fragments
            if ( fragments ) {
                jQuery.each( fragments, function( key, value ) {
                    jQuery( key ).replaceWith( value );
                });
            }
            jQuery('.shop_table').remove();
            jQuery('#formtable').remove();
            jQuery('#dvin_wcql_details').css('display','none');
            jQuery('#dvin_wcql_success_msg').css('display','block');
            jQuery('#dvin_messages').html('&nbsp;');
            jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
            jQuery('#dvin_message').html('');
        } else if(response.status == "redirect"){
            window.location = response.redirect_url;
            return;
        } else if(response.status == "error") {
            jQuery('#dvin_messages').html(response.msg);
        }
        msg.fadeIn();
        window.setTimeout(function(){
           msg.fadeOut();
        }, 2000);
    });
}
function refresh_quotelist_widget() {

    var data = {action: 'wcql_widget_refresh'};
    data['widget_refresh'] = "true";
    data['count_refresh'] = "true";
    // Ajax action
    jQuery.post( dvin_wcql_ajax_url, data, function( response ) {
        if ( ! response )
            return;
            if(response.status == "success") {
                fragments = response.fragments;
                // Replace fragments
                if ( fragments ) {
                    jQuery.each( fragments, function( key, value ) {
                        jQuery( key ).replaceWith( value );
                    });
                }
            }
    });
}
