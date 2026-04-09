<?php

add_filter('woocommerce_currency_symbol', 'change_rsd_currency_symbol', 10, 2);

function change_rsd_currency_symbol($currency_symbol, $currency)
{

    if ($currency === 'RSD') {
        $currency_symbol = 'RSD';
    }

    return $currency_symbol;
}


add_action('woocommerce_after_cart', 'custom_cart_crossells', 5);

function custom_cart_crossells()
{

    if (WC()->cart->is_empty()) {
        return;
    }

    $has_16_17 = false;
    $has_22 = false;
    $has_24 = false;
    $has_25 = false;

    // Provera proizvoda u korpi
    foreach (WC()->cart->get_cart() as $cart_item) {
        $product_id = $cart_item['product_id'];

        if (has_term([16, 17], 'product_cat', $product_id)) {
            $has_16_17 = true;
        }

        if (has_term(22, 'product_cat', $product_id)) {
            $has_22 = true;
        }

        if (has_term(24, 'product_cat', $product_id)) {
            $has_24 = true;
        }

        if (has_term(25, 'product_cat', $product_id)) {
            $has_25 = true;
        }
    }

    $tax_query = [
        'relation' => 'AND',

        // UVEK ignorisemo tag 29
        [
            'taxonomy' => 'product_tag',
            'field' => 'term_id',
            'terms' => [29],
            'operator' => 'NOT IN',
        ]
    ];

    // ==================================
    // GLAVNA LOGIKA
    // ==================================

    if ($has_16_17) {

        if ($has_24) {
            return; // Ako ima 24 → ništa
        }

        if ($has_22) {
            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => [23, 24],
                'operator' => 'IN',
            ];
        } else {
            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => [22, 24],
                'operator' => 'IN',
            ];
        }

    } else {

        if ($has_22 && $has_24) {

            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => [16, 17, 23],
                'operator' => 'IN',
            ];

        } elseif ($has_22) {

            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => [17, 23],
                'operator' => 'NOT IN',
            ];

        } elseif ($has_24) {

            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => [17, 23, 25],
                'operator' => 'NOT IN',
            ];

        } elseif ($has_25) {

            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => [22, 23, 24],
                'operator' => 'NOT IN',
            ];

        } else {
            return;
        }
    }

    $args = [
        'post_type' => 'product',
        'posts_per_page' => 4,
        'orderby' => 'rand',
        'post_status' => 'publish',
        'tax_query' => $tax_query,
    ];

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return;
    }

    echo '<section class="custom-cart-crossells">';
    echo '<h3>Naša preporuka</h3>';
    echo '<ul class="products columns-4">';

    while ($query->have_posts()) {
        $query->the_post();
        wc_get_template_part('content', 'product');
    }

    echo '</ul>';
    echo '</section>';

    wp_reset_postdata();
}













add_action('woocommerce_before_calculate_totals', function ($cart) {

    if (is_admin() && !defined('DOING_AJAX'))
        return;

    $target_product_id = 619;
    $threshold = 5000;
    $special_price = 899;

    $cart_total = 0;

    // 1. Izračunaj total BEZ tog proizvoda
    foreach ($cart->get_cart() as $cart_item) {

        if ($cart_item['product_id'] == $target_product_id)
            continue;

        // $cart_total += $cart_item['line_total'];
        $cart_total += $cart_item['line_total'] ?? 0;
    }

    // 2. Ako je >= 5000 → promeni cenu
    if ($cart_total >= $threshold) {

        foreach ($cart->get_cart() as $cart_item) {

            if ($cart_item['product_id'] == $target_product_id) {

                $cart_item['data']->set_price($special_price);
            }
        }

    } else {

        // 3. Vrati normalnu cenu ako padne ispod 5000
        foreach ($cart->get_cart() as $cart_item) {

            if ($cart_item['product_id'] == $target_product_id) {

                $product = wc_get_product($target_product_id);
                $cart_item['data']->set_price($product->get_regular_price());
            }
        }
    }

}, 20);




add_action('woocommerce_after_cart', function () {

    $target_product_id = 619;
    $threshold = 5000;

    $cart_total = WC()->cart->get_subtotal();

    $product = wc_get_product($target_product_id);
    if (!$product)
        return;

    $in_cart = false;

    foreach (WC()->cart->get_cart() as $item) {
        if ($item['product_id'] == $target_product_id) {
            $in_cart = true;
            break;
        }
    }

    if ($in_cart || $cart_total < $threshold)
        return;

    ?>

    <div class="custom-cart-upsell">

        <?php if ($cart_total >= $threshold): ?>
            <h3>🎉 Specijalna ponuda otključana!</h3>
            <p>Dodaj ovaj proizvod za samo <strong>899 RSD</strong></p>

        <?php endif; ?>

        <div class="upsell-product">

            <div class="upsell-image">
                <?php echo $product->get_image('medium'); ?>
            </div>

            <div class="upsell-info">
                <h4><?php echo $product->get_name(); ?></h4>

                <?php if ($product->get_short_description()): ?>
                    <div class="upsell-short-desc">
                        <?php echo wp_kses_post($product->get_short_description()); ?>
                    </div>
                <?php endif; ?>

                <?php if ($cart_total >= $threshold): ?>
                    <div class="price">
                        <del><?php echo wc_price($product->get_regular_price()); ?></del>
                        <strong><?php echo wc_price(899); ?></strong>
                    </div>
                <?php else: ?>
                    <div class="price">
                        <?php echo wc_price($product->get_price()); ?>
                    </div>
                <?php endif; ?>

                <?php if (!$in_cart): ?>
                    <a href="<?php echo esc_url(add_query_arg('add-to-cart', $target_product_id, wc_get_cart_url())); ?>"
                        class="button">
                        Dodaj u korpu
                    </a>
                <?php endif; ?>

            </div>

        </div>

    </div>



    <?php
}, 3);






add_action('wp_footer', function () {

    if (!is_cart())
        return;

    $target_categories = [17, 22, 23, 24, 25];
    $present_categories = [];

    foreach (WC()->cart->get_cart() as $cart_item) {
        $product_id = $cart_item['product_id'];

        foreach ($target_categories as $cat_id) {
            if (has_term($cat_id, 'product_cat', $product_id)) {
                $present_categories[] = $cat_id;
            }
        }
    }

    $present_categories = array_unique($present_categories);

    // kategorije koje NEMA u korpi
    $missing_categories = array_diff($target_categories, $present_categories);

    // uzmi po 1 proizvod iz svake missing kategorije
    $upsell_products = [];

    foreach ($missing_categories as $cat_id) {

        $products = wc_get_products([
            'limit' => 1,
            'status' => 'publish',
            'tax_query' => [
                'relation' => 'AND',
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $cat_id,
                ],
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => [79],
                    'operator' => 'NOT IN',
                ],
            ],
        ]);

        if (!empty($products)) {
            $p = $products[0];

            $upsell_products[] = [
                'id' => $p->get_id(),
                'name' => $p->get_name(),
                'image' => wp_get_attachment_image_url($p->get_image_id(), 'medium'),
                'price' => wc_price($p->get_price()),
            ];
        }
    }

    ?>
    <script>
        window.upsellProducts = <?php echo json_encode($upsell_products); ?>;
        window.shouldShowUpsell = <?php echo empty($missing_categories) ? 'false' : 'true'; ?>;
    </script>

    <div class="checkout-upsell-popup" style="display:none;">
        <div class="overlay"></div>

        <div class="content">
            <h3>Preporučujemo uz tvoju kupovinu</h3>

            <div class="upsell-products"></div>

            <div class="actions">
                <button class="continue-checkout button alt">
                    Nastavite na plaćanje
                </button>
            </div>
        </div>
    </div>

    <script>
        jQuery(function ($) {

            let proceedToCheckout = false;

            $('.checkout-button').on('click', function (e) {

                if (proceedToCheckout) return;

                if (!window.shouldShowUpsell) return;

                e.preventDefault();

                renderUpsellProducts();
                $('.checkout-upsell-popup').fadeIn();

            });

            function renderUpsellProducts() {

                let html = '';

                window.upsellProducts.forEach(product => {

                    html += `
                <div class="upsell-item">
                    <img src="${product.image}" />
                    <div>
                    <h4>${product.name}</h4>
                    <span>${product.price}</span>
                    </div>

                    <button class="add-upsell button" data-id="${product.id}">
                        Dodaj
                    </button>
                </div>
            `;
                });

                $('.upsell-products').html(html);
            }

            // ADD TO CART (AJAX)
$(document).on('click', '.add-upsell', function () {

    const btn = $(this);

    if (btn.hasClass('added')) return;

    const id = btn.data('id');

    // ✅ DEFINIŠI item OVDE (bitno!)
    const item = btn.closest('.upsell-item');

    btn.text('Dodajem...').prop('disabled', true);

    $.ajax({
        type: 'POST',
        url: wc_add_to_cart_params.wc_ajax_url.replace('%%endpoint%%', 'add_to_cart'),
        data: {
            product_id: id,
            quantity: 1
        },
        success: function () {

            // ✔ vizuelni feedback
            btn.text('Dodato ✔').addClass('added');

            // ✔ refresh checkout + mini cart
            $('body').trigger('update_checkout');
            $('body').trigger('wc_fragment_refresh');

            // ✔ update cart page (KLJUČNO)
            if ($('body').hasClass('woocommerce-cart')) {
                $('button[name="update_cart"]').prop('disabled', false).trigger('click');
            }
        },
        complete: function () {

            // ✔ ukloni item iz popup-a
            setTimeout(function () {

                item.fadeOut(300, function () {
                    item.remove();

                    // ✔ ako nema više → redirect
                    if ($('.upsell-item:visible').length === 0) {

                        const checkoutUrl = $('.checkout-button').attr('href');

                        window.location.href = checkoutUrl;
                    }
                });

            }, 800);
        }
    });

});

            // CONTINUE
            $('.continue-checkout').on('click', function () {

                proceedToCheckout = true;

                $(document.body).trigger('wc_fragment_refresh'); // mini cart + fragments
                $(document.body).trigger('update_checkout');

                window.location.href = $('.checkout-button').attr('href');

                // trigger refresh
                $(document.body).trigger('wc_fragment_refresh');


            });

            // CLOSE overlay
            $('.checkout-upsell-popup .overlay').on('click', function () {
                $('.checkout-upsell-popup').fadeOut();
                location.reload();
            });

        });
    </script>
    <?php
});



add_action('wp_footer', function(){
    if (!is_checkout()) return;

    // proveri prethodnu stranicu
    $referrer = wp_get_referer();

    if (!$referrer || strpos($referrer, wc_get_cart_url()) === false) {
        return;
    }
?>
<script>
jQuery(function($){

    $(window).on('load', function(){

        setTimeout(function(){
            $('body').trigger('update_checkout');
        }, 200);

        setTimeout(function(){
            $('body').trigger('update_checkout');
        }, 600);

    });

});
</script>
<?php
});