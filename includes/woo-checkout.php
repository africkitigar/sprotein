<?php
/***
 * Custom Post fee on checkout 
 */

add_action( 'woocommerce_cart_calculate_fees', 'add_cod_fee', 30, 1 );
function add_cod_fee( $cart ) {

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    if ( ! is_checkout() )
        return;

    $chosen_payment_method = WC()->session->get( 'chosen_payment_method' );

    if ( $chosen_payment_method !== 'cod' ) return;

    $percentage = 0.01;
    $min_fee    = 50;

    // 🔹 OSNOVA (bez fee-ova)
    $base_total = 
        (float) $cart->get_cart_contents_total() + 
        (float) $cart->get_shipping_total() + 
        (float) $cart->get_taxes_total();

    // 🔥 RUČNO izračunaj tvoj 2+1 popust opet
    $target_tags = [30, 31, 75];
    $total_qty = 0;
    $eligible_items = [];

    foreach ($cart->get_cart() as $cart_item) {
        $product_id = $cart_item['product_id'];

        if (has_term($target_tags, 'product_tag', $product_id)) {
            $eligible_items[] = $cart_item;
            $total_qty += $cart_item['quantity'];
        }
    }

    $total_savings = 0;

    if ($total_qty >= 3) {

        $discounted_qty = floor($total_qty / 3) * 3;
        $processed = 0;

        foreach ($eligible_items as $cart_item) {

            if ($processed >= $discounted_qty) break;

            $product = $cart_item['data'];
            $qty = $cart_item['quantity'];

            $remaining = $discounted_qty - $processed;
            $apply_qty = min($qty, $remaining);

            $price = (float) $product->get_price();
            $saving_per_item = $price * 0.3333;

            $total_savings += $saving_per_item * $apply_qty;
            $processed += $apply_qty;
        }
    }

    // 🔥 FINAL TOTAL (sa popustom)
    $final_total = $base_total - $total_savings;

    $fee = $final_total * $percentage;

    if ($fee < $min_fee) {
        $fee = $min_fee;
    }

    $cart->add_fee(
        __( 'Naknada pošte za pouzeće', 'woocommerce' ),
        $fee,
        false
    );
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


add_filter('woocommerce_shipping_package_name', function ($name, $i, $package) {
    return 'Dostava';
}, 10, 3);



add_filter('woocommerce_checkout_fields', function($fields){

    if (isset($fields['order']['order_comments'])) {
        $fields['order']['order_comments']['label'] = 'Napomena u vezi isporuke';
        $fields['order']['order_comments']['placeholder'] = 'Unesite napomenu za dostavu';
    }

    return $fields;
});



add_filter('woocommerce_checkout_fields', function($fields){

    // Checkbox
    $fields['order']['delivery_post'] = [
        'type'     => 'checkbox',
        'label'    => 'Isporuka u pošti',
        'required' => false,
        'class'    => ['form-row-wide'],
        'priority' => 25,
    ];

    // Adresa u pošti (skriveno po defaultu)
    $fields['order']['post_address'] = [
        'type'        => 'text',
        'label'       => 'Adresa u pošti',
        'placeholder' => 'Unesite adresu pošte...',
        'required'    => false,
        'class'       => ['form-row-wide', 'hidden-post-field'],
        'priority'    => 26,
    ];

    return $fields;
});


add_action('woocommerce_admin_order_data_after_billing_address', function($order){

    $delivery_post = $order->get_meta('Isporuka u pošti');
    $post_address  = $order->get_meta('Adresa u pošti');

    if ($delivery_post === 'Da') {

        echo '<p><strong>Isporuka:</strong> Pošta</p>';

        if (!empty($post_address)) {
            echo '<p><strong>Adresa pošte:</strong> ' . esc_html($post_address) . '</p>';
        }
    }

});

add_action('woocommerce_after_checkout_form', function(){
?>
<div id="post-delivery-note" style="display:none; margin-top:10px;">
    <small>
        <strong>Napomena*</strong> Za preuzimanje paketa u pošti biće Vam potrebna lična karta, 
        te je važno da ime i prezime koje ste naveli bude isto kao u ličnoj karti.
    </small>
</div>
<?php
});

add_action('wp_footer', function(){
    if (!is_checkout()) return;
?>
<script>
jQuery(function($){

    function togglePostFields(){

        if($('#delivery_post').is(':checked')){
            $('.hidden-post-field').show();
            $('#post-delivery-note').show();
        } else {
            $('.hidden-post-field').hide();
            $('#post-delivery-note').hide();
        }
    }

    // init
    togglePostFields();

    // change
    $(document).on('change', '#delivery_post', function(){
        togglePostFields();
    });

});
</script>
<?php
});


add_action('woocommerce_checkout_create_order', function($order, $data){

    if (isset($_POST['delivery_post'])) {
        $order->update_meta_data('Isporuka u pošti', 'Da');
    }

    if (!empty($_POST['post_address'])) {
        $order->update_meta_data('Adresa u pošti', sanitize_text_field($_POST['post_address']));
    }

}, 10, 2);

add_action('woocommerce_email_order_meta', function($order, $sent_to_admin, $plain_text, $email){

    // samo za processing email kupcu
    if ($email->id !== 'customer_processing_order') return;

    $delivery_post = $order->get_meta('Isporuka u pošti');
    $post_address  = $order->get_meta('Adresa u pošti');

    if ($delivery_post === 'Da') {

        if ($plain_text) {

            echo "\n---\n";
            echo "Isporuka: Pošta\n";
            echo "Adresa pošte: " . $post_address . "\n";

        } else {

            echo '<h2 style="margin-top:20px;">Detalji isporuke</h2>';
            echo '<p><strong>Isporuka:</strong> Pošta</p>';

            if (!empty($post_address)) {
                echo '<p><strong>Adresa pošte:</strong> ' . esc_html($post_address) . '</p>';
            }
        }
    }

}, 20, 4);









add_action('wp_footer', function () {

    if (!is_wc_endpoint_url('order-pay')) {
        return;
    }
    ?>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const details = document.querySelector('.order_details');
        if (!details) return;

        // Ako već postoji poruka – ne dodaj opet
        if (document.querySelector('.intesa-redirect-notice')) return;

        const notice = document.createElement('div');
        notice.className = 'intesa-redirect-notice';
        notice.innerHTML = `
            <div class="intesa-loader"></div>
            <p>
                Preusmeravamo vas na siguran portal banke Intesa radi završetka plaćanja.
                Molimo sačekajte...
            </p>
        `;

        details.insertAdjacentElement('afterend', notice);

    });
    </script>

    <?php
});



add_filter('body_class', function($classes) {

    if (is_order_received_page() && isset($_GET['plgresp'])) {

        if ($_GET['plgresp'] === 'no3d') {
            $classes[] = 'failed-transaction';
        }

    }

    return $classes;
});


add_action('template_redirect', function () {

    if (!is_order_received_page()) {
        return;
    }

    if (!isset($_GET['key'])) {
        return;
    }

    $order_id = wc_get_order_id_by_order_key(sanitize_text_field($_GET['key']));
    if (!$order_id) {
        return;
    }

    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }

    if (isset($_REQUEST['TransId'])) {

        $order->update_meta_data('_intesa_transaction_id', sanitize_text_field($_REQUEST['TransId']));
        $order->update_meta_data('_intesa_authcode', sanitize_text_field($_REQUEST['AuthCode'] ?? ''));
        $order->update_meta_data('_intesa_transaction_date', sanitize_text_field($_REQUEST['EXTRA_TRXDATE'] ?? ''));

        $order->save(); // HPOS compatible
    }

});



add_action('woocommerce_email_after_order_table', function($order, $sent_to_admin, $plain_text, $email){

    // samo processing email kupcu
    if ($email->id !== 'customer_processing_order') {
        return;
    }

    // samo Intesa kartično plaćanje
    if ($order->get_payment_method() !== 'npintesa') {
        return;
    }

    $transaction_id = $order->get_meta('_intesa_transaction_id');
    $authcode       = $order->get_meta('_intesa_authcode');
    $trx_date       = $order->get_meta('_intesa_transaction_date');

    if (!$transaction_id) {
        return;
    }

    if ($plain_text) {

        echo "\nPodaci o kartičnoj transakciji\n";
        echo "Transaction ID: $transaction_id\n";
        echo "Authcode: $authcode\n";
        echo "Vreme transakcije: $trx_date\n";

    } else {

        echo '<h2>Podaci o kartičnoj transakciji</h2>';
        echo '<p><strong>Transaction ID:</strong> '.esc_html($transaction_id).'</p>';
        echo '<p><strong>Authcode:</strong> '.esc_html($authcode).'</p>';
        echo '<p><strong>Vreme transakcije:</strong> '.esc_html($trx_date).'</p>';

    }

}, 20, 4);



add_action('template_redirect', function () {

    // Radi samo na order-received stranici
    if (!is_order_received_page()) return;

    // Proveri parametar iz Intesa redirecta
    if (!isset($_GET['plgresp']) || $_GET['plgresp'] !== 'no3d') return;

    // Uzmi order ID iz URL-a
    $order_id = absint(get_query_var('order-received'));
    if (!$order_id) return;

    $order = wc_get_order($order_id);
    if (!$order) return;

    // Ako već nije failed ili completed
    if (!$order->has_status(['failed', 'processing', 'completed'])) {

        $order->update_status('failed', 'Intesa redirect: no3d (failed payment)');

        // Opcionalno: smanji zalihe ako nisu već
        wc_maybe_reduce_stock_levels($order_id);
    }

});