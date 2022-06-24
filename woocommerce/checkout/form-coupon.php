<?php
/**
 * Checkout coupon form
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.4
 */

defined( 'ABSPATH' ) || exit;

if ( ! wc_coupons_enabled() ) { // @codingStandardsIgnoreLine.
	return;
}

?>
<div class="woocommerce-form-coupon-toggle">
	<?php wc_print_notice( apply_filters( 'woocommerce_checkout_coupon_message', esc_html__( 'Have a coupon?', 'masterclass' ) . ' <a href="#" class="showcoupon">' . esc_html__( 'Click here to enter your code', 'masterclass' ) . '</a>' ), 'notice' ); ?>
</div>

<form class="checkout_coupon woocommerce-form-coupon" method="post" style="display:none">

	<p><?php esc_html_e( 'If you have a coupon code, please apply it below.', 'masterclass' ); ?></p>

	<div class="input-group-text-button">
		<input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'Coupon code', 'masterclass' ); ?>" id="coupon_code" value="" />
		<button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'masterclass' ); ?>"><?php esc_html_e( 'Apply coupon', 'masterclass' ); ?></button>
	</div>

</form>
