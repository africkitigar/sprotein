<?php
remove_action('woocommerce_checkout_order_review', 'woocommerce_order_review', 10);
add_action('woocommerce_after_checkout_form', 'woocommerce_order_review', 100);


add_action('woocommerce_before_checkout_form', 'add_cart_to_checkout_before_customer_details');

function add_cart_to_checkout_before_customer_details() {
    echo do_shortcode('[woocommerce_cart]');
}



function add_checkout_js_script() {
    if( is_checkout() ):
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Detach the table with class 'shop_table woocommerce-checkout-review-order-table'
            var checkoutReviewOrderTable = $('.shop_table.woocommerce-checkout-review-order-table').detach();

            // Select the coupon form (woo coupon form toggle)
            var couponToggle = $('.woocommerce-form-coupon-toggle');
            var couponForm = $('.checkout_coupon');

            // Create a new div with id 'checkoutsidebar'
            var checkoutSidebar = $('<div id="checkoutsidebar"></div>');

            // Append the coupon form to the new checkout sidebar
            checkoutSidebar.prepend(couponToggle);
            checkoutSidebar.prepend(couponForm);


            // Insert the checkout sidebar (which now includes the coupon form) before the review order table
            checkoutSidebar.append(checkoutReviewOrderTable);

            // Insert the new checkoutsidebar after the div with data-shortcode="checkout"
            $('div[data-shortcode="checkout"]').after(checkoutSidebar);

            jQuery(function($) {
    // Update cart when quantity changes
    $(document).on('change', '.woocommerce-cart-form input.qty', function() {
        $('button[name="update_cart"]').trigger('click');
    });

    // Reinitialize quantity buttons after cart update
    $(document.body).on('updated_wc_div', function() {
        addPlusMinusButtons(); // Re-add buttons after cart updates
    });

    // Function to add plus/minus buttons
    function addPlusMinusButtons() {
        $('.woocommerce-cart-form .quantity').each(function() {
            if (!$(this).find('.qty-button').length) {
                $(this).prepend('<button type="button" class="qty-button minus">−</button>');
                $(this).append('<button type="button" class="qty-button plus">+</button>');
            }
        });
    }

    // Handle plus/minus button clicks
    $(document).on('click', '.qty-button.plus', function() {
        var $input = $(this).siblings('input.qty');
        $input.val(parseInt($input.val()) + 1).trigger('change');
    });

    $(document).on('click', '.qty-button.minus', function() {
        var $input = $(this).siblings('input.qty');
        if ($input.val() > 1) {
            $input.val(parseInt($input.val()) - 1).trigger('change');
        }
    });

    // Initialize buttons on page load
    addPlusMinusButtons();
});



            });//ready
    </script>
    <?php
    endif; //checkout
}
add_action('wp_footer', 'add_checkout_js_script');








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



function default_billing_details(){

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();



        // Get user billing details
        $company    = get_user_meta($user_id, 'billing_company', true);
        $first_name = get_user_meta($user_id, 'billing_first_name', true);
        $last_name  = get_user_meta($user_id, 'billing_last_name', true);
        $address_1  = get_user_meta($user_id, 'billing_address_1', true);
        $postcode   = get_user_meta($user_id, 'billing_postcode', true);
        $city       = get_user_meta($user_id, 'billing_city', true);

        // Output the details
        echo '<div class="custom-billing-info">';
        echo '<h3>Rechnungsadresse</h3>';
        if($company){
            echo '<p>' . esc_html($company) . '</p>';
        }
        echo '<p>' . esc_html($first_name) . ' ' . esc_html($last_name) . '</p>';
        echo '<p>' . esc_html($address_1) . '</p>';
        echo '<p>' . esc_html($postcode) . ', ' . esc_html($city) .'</p>';
        echo '</div>';
    }
}
add_action('woocommerce_before_checkout_billing_form', 'default_billing_details');


add_filter( 'woocommerce_checkout_fields', function( $fields ) {
    unset( $fields['billing']['billing_state'] );
    unset( $fields['shipping']['shipping_state'] );

    return $fields;
});



add_action( 'woocommerce_review_order_before_submit', function() {
    echo '<p class="totalvalue"><strong>Gesamtsumme: </strong><span>' . WC()->cart->get_total() . '</span></p>';
});





// Display "Kommission" field before the shipping form on the checkout page
add_action( 'woocommerce_before_checkout_billing_form', 'display_custom_kommission_field', 1 );
function display_custom_kommission_field() {
    // Define the field parameters
    $field_args = array(
        'type'        => 'text',
        'label'       => __( 'Kommission', 'your-text-domain' ),
        'placeholder' => __( '', 'your-text-domain' ),
        'required'    => false,
        'class'       => array( 'form-row-wide' ),
        'clear'       => true,
    );

    // Output the field using WooCommerce's form field helper
    woocommerce_form_field( 'billing_kommission', $field_args, WC()->checkout->get_value( 'billing_komission' ) );
}





// Save the "Kommission" field value in order meta
add_action( 'woocommerce_checkout_update_order_meta', function( $order_id ) {
    if ( ! empty( $_POST['billing_kommission'] ) ) {
        $order = wc_get_order( $order_id );
        $order->update_meta_data( 'billing_kommission', sanitize_text_field( $_POST['billing_kommission'] ) );
        $order->save();
    }
});



// Display "Kommission" field in the WooCommerce admin order details
add_action( 'woocommerce_admin_order_data_after_billing_address', function( $order ) {
    $kommission = $order->get_meta( 'billing_kommission' );
    if ( ! empty( $kommission ) ) {
        echo '<p><strong>' . __( 'Kommission', 'your-text-domain' ) . ':</strong> ' . esc_html( $kommission ) . '</p>';
    }
});

add_action( 'woocommerce_order_details_after_order_table', function( $order ) {
    if ( is_user_logged_in() ) {
        $kommission = $order->get_meta( 'billing_kommission' );
        if ( ! empty( $kommission ) ) {
            echo '<p><strong>' . __( 'Kommission', 'your-text-domain' ) . ':</strong> ' . esc_html( $kommission ) . '</p>';
        }
    }
});










add_action('woocommerce_cart_calculate_fees', function() {
    if (is_admin() && !defined('DOING_AJAX')) return;

    // Get the current user
    $user_id = get_current_user_id();
    if (!$user_id) return;

    // Get the ACF discount field
    $discount_percentage = get_field('discount', 'user_' . $user_id);

    if ($discount_percentage && is_numeric($discount_percentage) && $discount_percentage > 0) {
        $cart = WC()->cart;
        $discount_amount = ($cart->subtotal_ex_tax * $discount_percentage) / 100;

        // Apply the discount as a negative fee (before tax) with percentage in label
        $cart->add_fee(sprintf(__('Ihr Rabatt (%.2f%%)', 'your-textdomain'), $discount_percentage), -$discount_amount, false);
    }
});








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



/** Custom text for accept terms and conditions **/
add_filter( 'woocommerce_get_terms_and_conditions_checkbox_text', 'custom_german_terms_text' );
function custom_german_terms_text( $text ) {
    $terms_url = home_url('/allgemeine-geschaeftsbedingungen/');
    $privacy_url = home_url('/datenschutzrichtlinie/');

    return sprintf(
        __( 'Ich habe die <a href="%s" target="_blank">AGB</a> und <a href="%s" target="_blank">Datenschutzerklärung</a> gelesen und stimme ihnen zu', 'woocommerce' ),
        esc_url( $terms_url ),
        esc_url( $privacy_url )
    );
}
