<?php


/*
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

*/






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













add_filter('woocommerce_checkout_fields', function($fields) {

    // Billing state
    unset($fields['billing']['billing_state']);

    // Shipping state
    unset($fields['shipping']['shipping_state']);

    return $fields;
});










