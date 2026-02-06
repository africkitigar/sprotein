<?php
/**
 * Customer new account email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-new-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 6.0.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer's billing first name */ ?>
<p><?php printf( esc_html__( 'Hallo %s,', 'woocommerce' ), esc_html( $billing_first_name ) ); ?></p>

<p><?php esc_html_e( 'Ihr Konto wurde bestätigt und erstellt. Sie können sich hier anmelden:', 'woocommerce' ); ?> 
<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Anmelden', 'woocommerce' ); ?></a>.</p>

<p><?php esc_html_e( 'Ihr temporäres Passwort lautet "worldoflights2025". Bitte ändern Sie es nach der Anmeldung.', 'woocommerce' ); ?></p>


<?php
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );
