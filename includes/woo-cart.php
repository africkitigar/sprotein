<?php

add_filter( 'woocommerce_currency_symbol', 'change_rsd_currency_symbol', 10, 2 );

function change_rsd_currency_symbol( $currency_symbol, $currency ) {

    if ( $currency === 'RSD' ) {
        $currency_symbol = 'RSD';
    }

    return $currency_symbol;
}


add_action('woocommerce_after_cart', 'custom_cart_crossells', 5);

function custom_cart_crossells() {

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

        if (has_term([16,17], 'product_cat', $product_id)) {
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
            'field'    => 'term_id',
            'terms'    => [29],
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
                'field'    => 'term_id',
                'terms'    => [24],
                'operator' => 'IN',
            ];
        } else {
            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => [22,24],
                'operator' => 'IN',
            ];
        }

    } else {

        if ($has_22 && $has_24) {

            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => [16,17],
                'operator' => 'IN',
            ];

        } elseif ($has_22) {

            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => [22],
                'operator' => 'NOT IN',
            ];

        } elseif ($has_24) {

            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => [24],
                'operator' => 'NOT IN',
            ];

        } elseif ($has_25) {

            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => [22,23,24],
                'operator' => 'NOT IN',
            ];

        } else {
            return;
        }
    }

    $args = [
        'post_type'      => 'product',
        'posts_per_page' => 4,
        'orderby'        => 'rand',
        'post_status'    => 'publish',
        'tax_query'      => $tax_query,
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

    if (is_admin() && !defined('DOING_AJAX')) return;

    $target_product_id = 619;
    $threshold = 5000;
    $special_price = 199;

    $cart_total = 0;

    // 1. Izračunaj total BEZ tog proizvoda
    foreach ($cart->get_cart() as $cart_item) {

        if ($cart_item['product_id'] == $target_product_id) continue;

        $cart_total += $cart_item['line_total'];
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
    if (!$product) return;

    $in_cart = false;

    foreach (WC()->cart->get_cart() as $item) {
        if ($item['product_id'] == $target_product_id) {
            $in_cart = true;
            break;
        }
    }

    if ($in_cart || $cart_total < $threshold) return;

    ?>
    
    <div class="custom-cart-upsell">

        <?php if ($cart_total >= $threshold): ?>
            <h3>🎉 Specijalna ponuda otključana!</h3>
            <p>Dodaj ovaj proizvod za samo <strong>199 RSD</strong></p>
       
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
                        <strong><?php echo wc_price(199); ?></strong>
                    </div>
                <?php else: ?>
                    <div class="price">
                        <?php echo wc_price($product->get_price()); ?>
                    </div>
                <?php endif; ?>

                <?php if (!$in_cart): ?>
                    <a href="<?php echo esc_url( add_query_arg('add-to-cart', $target_product_id, wc_get_cart_url()) ); ?>" 
                    class="button">
                        Dodaj u korpu
                    </a>
                <?php endif; ?>

            </div>

        </div>

    </div>



    <?php
}, 3);