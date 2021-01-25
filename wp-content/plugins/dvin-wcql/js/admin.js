jQuery(document).ready(function(){

	//tabs
	jQuery( "#dvinqlisttabs" ).tabs({collapsible: true});

	//for addtoqlist color
	jQuery('#link_bgcolorpicker').hide();
	jQuery('#link_bgcolorpicker').farbtastic('#link_bgcolor');
	jQuery('#link_bgcolor').click(function() {
	jQuery('#link_bgcolorpicker').fadeIn(500);
	});
	//for addtoqlist fontcolor
	jQuery('#link_fontcolorpicker').hide();
	jQuery('#link_fontcolorpicker').farbtastic('#link_fontcolor');
	jQuery('#link_fontcolor').click(function() {
	jQuery('#link_fontcolorpicker').fadeIn(500);
	});
});
jQuery(document).mousedown(function() {
        jQuery('#link_bgcolorpicker').each(function() {
	    var display = jQuery(this).css('display');
            if ( display == 'block' )
                jQuery(this).fadeOut();
        });

	  jQuery('#link_fontcolorpicker').each(function() {
	    var display = jQuery(this).css('display');
            if ( display == 'block' )
                jQuery(this).fadeOut();
        });
});
