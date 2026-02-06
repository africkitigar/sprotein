<?php
/**
 * Admin new order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/admin-new-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails\HTML
 * @version 9.8.0
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php
/*
echo $email_improvements_enabled ? '<div class="email-introduction">' : '';

$text = __( 'You’ve received the following order from %s:', 'woocommerce' );
if ( $email_improvements_enabled ) {

	$text = __( 'You’ve received a new order from %s:', 'woocommerce' );
}
*/?>
<?php

echo $email_improvements_enabled ? '<div class="email-introduction">' : '';

$billing_name    = $order->get_formatted_billing_full_name();
$billing_company = $order->get_billing_company();

// Ako postoji company → dodaj ga u string
if ( ! empty( $billing_company ) ) {
    $from_text = sprintf( '%s von %s', $billing_name, $billing_company );
} else {
    $from_text = $billing_name;
}

// Default poruka
if ( ! $email_improvements_enabled ) {
    /* translators: %s: Customer billing full name + company */
    $text = sprintf( __( 'You’ve received the following order from %s:', 'woocommerce' ), $from_text );

} else {
    /* translators: %s: Customer billing full name + company */
    $text = sprintf( __( 'You’ve received a new order from %s:', 'woocommerce' ), $from_text );
}

echo $text;

?>


<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo $email_improvements_enabled ? '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td class="email-additional-content">' : '';
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
	echo $email_improvements_enabled ? '</td></tr></table>' : '';
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
