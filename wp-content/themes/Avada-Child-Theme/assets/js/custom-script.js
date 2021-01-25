function getCook(cookiename) {
  // Get name followed by anything except a semicolon
  var cookiestring=RegExp(""+cookiename+"[^;]+").exec(document.cookie);
  // Return everything after the equal sign
  return unescape(!!cookiestring ? cookiestring.toString().replace(/^[^=]+./,"") : "");
}
var menuID = getCook('menu');

jQuery(document).ready(function ($) {

  $("#menu-products,.sub-menu").accordion({
    animate: 200,
    heightStyle: "content",
    active: false,
    collapsible: true,
    icons: {"header": "+", "activeHeader": "-"}
  });


  /**
  * Open accordion to active page
  */

  //Grab the current page
  //var currentPage = window.location.href;
  var currentPage = window.location.pathname;
  var testLink = $('a[href="'+currentPage+'"]').first();
  
  if (!testLink.length || testLink=="undefined") {
      var currentPage = window.location.href;
  }
 

  //Grab active link, parents & menu element
  var menu = $('#menu-products');
  var activeLink = $('a[href="'+currentPage+'"]').first();
  var activeLinkParent = activeLink.closest('li');
  var activeLinkMenu = activeLink.closest('ul');

  //Check if it's a sub menu
  if ( activeLinkMenu.parents('ul').hasClass('sub-menu') ) {
    //Open menu element
    var index = activeLinkMenu.parents('ul').parent('li').index();
    menu.accordion( "option", "active", index );

    //Open sub menu too
    subMenuIndex = activeLinkMenu.parent('li').index();
    activeLinkMenu.parent('li').parent('ul').accordion( "option", "active", subMenuIndex );
  } else if ( activeLinkMenu.parents('ul').hasClass('menu') ) {
    //Open menu element
    index = activeLinkMenu.parent('li').index();
    menu.accordion( "option", "active", index );
  }


  /**
  * Some other functions
  */
  //capture the click on the a tag
   //$("#menu-products .sub-menu .dis a").click(function() {
   $(".dis a").click(function() { //Fixes the link when its anywhere other than a sub menu
      window.location = $(this).attr('href');
      return false;
   });

   $(".product_custom_fields").accordion({
        animate: 200,
        active: 3,
        heightStyle: "content",
        header: 'h3'
//        icons: {"header": "+", "activeHeader": "-"}
    });

});
