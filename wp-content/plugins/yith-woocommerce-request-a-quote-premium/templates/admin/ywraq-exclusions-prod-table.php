<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Admin View: Exclusions List Table
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$list_query_args = array(
	'page' => $_GET['page'],
	'tab'  => $_GET['tab']
);

$list_url = add_query_arg( $list_query_args, admin_url( 'admin.php' ) );

?>
<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br /></div>
	<h2><?php _e( 'Product exclusion list', 'yith-woocommerce-request-a-quote' ); ?></h2>

	<?php if ( ! empty( $notice ) ) : ?>
		<div id="notice" class="error below-h2"><p><?php echo $notice; ?></p></div>
	<?php endif;

	if ( ! empty( $message ) ) : ?>
		<div id="message" class="updated below-h2"><p><?php echo $message; ?></p></div>
	<?php endif;

	?>
	<form id="yith-add-exclusion-prod" method="POST">
		<input type="hidden" name="_nonce" value="<?php echo wp_create_nonce( 'yith_ywraq_add_exclusions_prod' ); ?>" />
		<label for="add_products"><?php _e( 'Select products to exclude', 'yith-woocommerce-request-a-quote' ); ?></label>
		<?php yit_add_select2_fields( array(
			'style' 			=> 'width: 300px;display: inline-block;',
			'class'				=> 'wc-product-search',
			'id'				=> 'add_products',
			'name'				=> 'add_products',
			'data-placeholder' 	=> __( 'Search product...', 'yith-woocommerce-request-a-quote' ),
			'data-multiple'		=> true,
			'data-action'		=> 'yith_ywraq_search_products',
		) );
		?>
		<input type="submit" value="<?php _e( 'Exclude', 'yith-woocommerce-request-a-quote' ); ?>" id="add" class="button" name="add">
	</form>

	<?php $table->display(); ?>

</div>