<?php
/***
 * Custom Post fee on checkout 
 */
add_action( 'woocommerce_cart_calculate_fees', 'add_cod_fee', 20, 1 );
function add_cod_fee( $cart ) {

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    if ( ! is_checkout() )
        return;

    // Get chosen payment method
    $chosen_payment_method = WC()->session->get( 'chosen_payment_method' );

    if ( $chosen_payment_method === 'cod' ) {

        $fee = 50; // amount

        $cart->add_fee(
            __( 'Naknada pošte za pouzeće', 'woocommerce' ),
            $fee,
            false // not taxable
        );
    }
}

add_action( 'wp_footer', function() {
    if ( ! is_checkout() ) return;
    ?>
    <script>
    jQuery(function($){
        $('form.checkout').on('change', 'input[name="payment_method"]', function(){
            $('body').trigger('update_checkout');
        });
    });
    </script>
    <?php
});
/** end of | Custom Post fee on checkout  **/




/**
 * Hide other shipping methods when free shipping is available
 */
add_filter( 'woocommerce_package_rates', 'hide_other_shipping_when_free_available', 100 );

function hide_other_shipping_when_free_available( $rates ) {

    $free = [];

    foreach ( $rates as $rate_id => $rate ) {

        if ( $rate->method_id === 'free_shipping' ) {
            $free[ $rate_id ] = $rate;
        }
    }

    // Ako postoji free shipping → vrati samo njega
    if ( ! empty( $free ) ) {
        return $free;
    }

    return $rates;
}

/** end of | Hide other shipping methods when free shipping is available  **/












function update_cart_quantity_ajax() {
    if (!isset($_POST['cart_key']) || !isset($_POST['quantity'])) {
        wp_send_json_error(['message' => __('Invalid data.', 'woocommerce')]);
    }

    $cart_key = sanitize_text_field($_POST['cart_key']);
    $quantity = (int) $_POST['quantity'];

    if ($quantity < 1) {
        $quantity = 1; // Ensure quantity is at least 1
    }

    WC()->cart->set_quantity($cart_key, $quantity);
    WC()->cart->calculate_totals();

    ob_start();
    woocommerce_cart_totals();
    $cart_totals = ob_get_clean();

    ob_start();
    woocommerce_cart_form();
    $cart_html = ob_get_clean();

    wp_send_json_success([
        'fragments' => [
            'cart_html'    => $cart_html,
            'cart_totals'  => $cart_totals,
        ]
    ]);
}
add_action('wp_ajax_update_cart_quantity', 'update_cart_quantity_ajax');
add_action('wp_ajax_nopriv_update_cart_quantity', 'update_cart_quantity_ajax');









/*
add_action('wp_loaded', 'redirect_to_shop_when_cart_empty');
function redirect_to_shop_when_cart_empty() {
    // Only proceed if we're on the checkout page and it's an AJAX call for removing an item
    if (is_checkout() && isset($_POST['action']) && $_POST['action'] === 'woocommerce_remove_cart_item') {
        // Check if cart is now empty after removal
        if (WC()->cart->is_empty()) {
            wp_send_json(array(
                'redirect' => wc_get_page_permalink('shop')
            ));
        }
    }
}

// For non-AJAX requests (fallback)
add_action('template_redirect', 'redirect_empty_cart_to_shop');
function redirect_empty_cart_to_shop() {
    if (is_checkout() && WC()->cart->is_empty() && !is_wc_endpoint_url('order-received')) {
        wp_safe_redirect(wc_get_page_permalink('shop'));
        exit;
    }
}


*/










add_filter('woocommerce_checkout_fields', function($fields) {

    // Billing state
    unset($fields['billing']['billing_state']);

    // Shipping state
    unset($fields['shipping']['shipping_state']);

    return $fields;
});




/*
add_action('woocommerce_cart_calculate_fees', function($cart) {

    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    if (!is_checkout()) return;

    // Uzimamo billing country sa checkouta
    $billing_country = WC()->customer->get_billing_country();

    // Ako nije Srbija (RS)
    if ($billing_country && $billing_country !== 'RS') {

        $cart->add_fee(
            'Troškovi isporuke u inostranstvo',
            800,
            false // false = nije oporezivo
        );
    }

});
*/


/**
 * Remove COD if selected country is not Serbia on checkout
 */
add_filter('woocommerce_available_payment_gateways', function($gateways) {

    if (is_admin()) {
        return $gateways;
    }

    $country = WC()->customer->get_billing_country();

    // Ako NIJE Srbija → ukloni COD
    if ($country && $country !== 'RS') {
        unset($gateways['cod']);
    }

    return $gateways;

});
/**
 * Remove default shipping methods when country is not Serbia
 */
/*add_filter('woocommerce_package_rates', function($rates, $package) {

    $country = WC()->customer->get_shipping_country();

    if ($country && $country !== 'RS') {
        return []; // uklanja sve shipping metode
    }

    return $rates;

}, 20, 2);*/



add_action('woocommerce_review_order_after_order_total', function() {

    $country = WC()->customer->get_shipping_country();

    if (!$country || $country === 'RS') {
        return;
    }

    $exchange_rate = 117.4;

    // Ukupan iznos sa svim fee-ovima
    $total_rsd = WC()->cart->get_total('edit'); 
    $total_rsd = floatval($total_rsd);

    if (!$total_rsd) return;

    $total_eur = $total_rsd / $exchange_rate;
    $total_eur = number_format($total_eur, 2, ',', '.');

    echo '<tr class="international-eur-total">
            <th>Ukupno u evrima</th>
            <td><strong>≈ ' . $total_eur . ' €</strong><br></td>
          </tr>';

});