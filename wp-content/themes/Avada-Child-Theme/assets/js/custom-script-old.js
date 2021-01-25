jQuery(document).ready(function ($) {
    $("#menu-products,.sub-menu").accordion({
        animate: 200,
        heightStyle: "content",
        collapsible: true,
        active: false,
        icons: {"header": "+", "activeHeader": "-"}
    });
//capture the click on the a tag
   $("#menu-products .sub-menu .dis a").click(function() {
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
    //capture the click on the a tag
//   $(".product_custom_fields > a").click(function() {
//      window.location = $(this).attr('href');
//      return false;
//   });
});
