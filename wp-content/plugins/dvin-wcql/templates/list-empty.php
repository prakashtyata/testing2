<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
wc_print_notices();
?>
<p class="cart-empty"><?php echo apply_filters('dvin_wcql_listempty',__('Your list is currently empty.','dvinwcql')); ?></p>
<p class="return-to-shop"><a class="button wc-backward" href="<?php echo apply_filters( 'dvin_wcql_return_to_shop_url', get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"><?php echo apply_filters('dvin_wcql_return_to_shop_txt',__('Return To Shop','dvinwcql')); ?></a></p>

