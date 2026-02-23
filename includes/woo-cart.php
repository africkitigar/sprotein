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
    echo '<h3>Možda će vam se svideti</h3>';
    echo '<ul class="products columns-4">';

    while ($query->have_posts()) {
        $query->the_post();
        wc_get_template_part('content', 'product');
    }

    echo '</ul>';
    echo '</section>';

    wp_reset_postdata();
}