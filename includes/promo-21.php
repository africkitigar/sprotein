<?php


add_action('wp_ajax_add_bundle_to_cart', 'add_bundle_to_cart');
add_action('wp_ajax_nopriv_add_bundle_to_cart', 'add_bundle_to_cart');


function add_bundle_to_cart()
{

    $ids = $_POST['products'];

    foreach ($ids as $key => $id) {

        if ($key == 2) {
            WC()->cart->add_to_cart($id, 1, 0, [], ['is_free_flavor' => true]);
        } else {
            WC()->cart->add_to_cart($id, 1);
        }
    }

    WC_AJAX::get_refreshed_fragments();
}

add_action('wp_footer', function () {
    if (!is_product())
        return;
    ?>

    <script>
        jQuery(function ($) {

            $('.special-bundle-form:not(.combo-form)').on('submit', function (e) {
                e.preventDefault();

                const products = [
                    $('select[name="flavor_1"]').val(),
                    $('select[name="flavor_2"]').val(),
                    $('select[name="flavor_3"]').val(),
                ];

                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {
                        action: 'add_bundle_to_cart',
                        products: products
                    },
                    /*success: function(response){
    
                        if(response.fragments){
    
                            $.each(response.fragments, function(key, value){
                                $(key).replaceWith(value);
                            });
    
                        }
    
                        $('.bundle-success').fadeIn();
                    }*/
                    /*success: function(response){
     
                         $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);
     
                        // $('.bundle-success').fadeIn();
                        openPopup(products);
                     }*/

                    success: function (response) {

                        if (response.fragments) {
                            $.each(response.fragments, function (key, value) {
                                $(key).replaceWith(value);
                            });
                        }

                        openPopup();
                    }
                });

            });

        let bundlePopupOpened = false;
            function openPopup() {

                if (bundlePopupOpened) return;

                bundlePopupOpened = true;

                const popup = $('.bundle-popup');
                const wrapper = popup.find('.bundle-popup-products');

                let html = '';

                $('.special-bundle-form select').each(function () {

                    const option = this.options[this.selectedIndex];

                    if (!option.value) return;

                    html += `
            <div class="popup-product">
                <img src="${option.dataset.image}" />
                <div>${option.dataset.ukus}</div>
            </div>
        `;
                });

                wrapper.html(html);

                popup.fadeIn();
            }

            // zatvaranje popup-a
            $(document).on('click', '.bundle-popup-overlay, .bundle-popup-close, .continue-shopping', function () {
                $('.bundle-popup').fadeOut();
                location.reload();
            });

        });
    </script>

    <?php
});






add_action('wp_ajax_add_special_product_to_cart', 'add_special_product_to_cart');
add_action('wp_ajax_nopriv_add_special_product_to_cart', 'add_special_product_to_cart');

function add_special_product_to_cart()
{

    $product_id = intval($_POST['product_id']);
    $flavor_1 = sanitize_text_field($_POST['flavor_1']);
    $flavor_2 = sanitize_text_field($_POST['flavor_2']);

    if (!$product_id || !$flavor_1 || !$flavor_2) {
        wp_send_json_error(['message' => 'Missing data']);
    }

    WC()->cart->add_to_cart($product_id, 1, 0, [], [
        'flavor_1' => $flavor_1,
        'flavor_2' => $flavor_2,
        'unique_key' => md5(microtime())
    ]);

    WC_AJAX::get_refreshed_fragments();
}






/**
 * Summary of sp_get_protein_promo_message
 * Show different messages in top header
 */
function sp_get_protein_promo_message()
{

    if (!WC()->cart) {
        return '';
    }

    // tagovi akcija
    $promo_tags = [
        30 => 'Ultra Whey proteina',
        31 => 'Strong Whey proteina',
        75 => 'Kolagena'
    ];

    $tag_counts = [
        30 => 0,
        31 => 0,
        75 => 0
    ];

    // broji proizvode po tagu u korpi
    foreach (WC()->cart->get_cart() as $cart_item) {

        $product_id = $cart_item['product_id'];
        $qty = (int) $cart_item['quantity'];

        foreach ($promo_tags as $tag_id => $label) {

            if (has_term($tag_id, 'product_tag', $product_id)) {
                $tag_counts[$tag_id] += $qty;
            }

        }
    }

    // SUCCESS stanje (ako bilo koji tag ima 3+)
    foreach ($tag_counts as $tag_id => $count) {

        if ($count >= 3) {
            return '
      <svg class="promo-icon promo-icon--check" width="20" height="20" viewBox="0 0 24 24" fill="none">
        <path d="M5 13l4 4L19 7"
              stroke="currentColor" stroke-width="1.8"
              stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <strong>Čestitamo!</strong> Ostvario si <strong>2+1 GRATIS</strong> akciju!
      ';
        }

    }

    // product page logika
    if (is_product()) {

        $product_id = get_queried_object_id();

        foreach ($promo_tags as $tag_id => $label) {

            if (has_term($tag_id, 'product_tag', $product_id)) {

                $count = $tag_counts[$tag_id];

                if ($count === 0) {
                    return "💥 Poruči <strong>2 Olympic  {$label}</strong> i <strong>treći dobijaš GRATIS</strong>!";
                }

                if ($count === 1) {
                    return "+ Poruči još <strong>2 Olympic  {$label}</strong> i ostvari <strong>2+1 GRATIS</strong>!";
                }

                if ($count === 2) {
                    return "🔥 Još <strong>1 {$label}</strong> te deli od <strong>BESPLATNOG trećeg</strong>!";
                }

            }

        }

    }

    // random marketing poruke
    $messages = [
        'Preko 100.000 zadovoljnih korisnika',
        'Rok isporuke 2 radna dana na teritoriji Republike Srbije',
        'Besplatna dostava na teritoriji Republike Srbije za porudžbine preko 3500 RSD'
    ];

    return $messages[array_rand($messages)];
}






/*
add_action('woocommerce_before_calculate_totals', function ($cart) {

    if (is_admin() && !defined('DOING_AJAX')) return;

    $target_tags = [30, 31, 75];

    $eligible_items = [];
    $total_qty = 0;

    // 1. Pronađi sve proizvode u akciji
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {

        $product_id = $cart_item['product_id'];

        if (has_term($target_tags, 'product_tag', $product_id)) {
            $eligible_items[$cart_item_key] = $cart_item;
            $total_qty += $cart_item['quantity'];
        }
    }

    if ($total_qty < 3) return;

    // 2. Koliko komada dobija popust (3,6,9...)
    $discounted_qty = floor($total_qty / 3) * 3;

    $processed = 0;

    // 3. Resetuj cene prvo (bitno!)
    foreach ($cart->get_cart() as $cart_item) {
        $product = $cart_item['data'];
        $product->set_price($product->get_regular_price() ?: $product->get_price());
    }

    // 4. Primeni popust samo na prvih N komada
    foreach ($eligible_items as $cart_item_key => $cart_item) {

        if ($processed >= $discounted_qty) break;

        $product = $cart_item['data'];
        $qty = $cart_item['quantity'];

        $remaining = $discounted_qty - $processed;
        $apply_qty = min($qty, $remaining);

        $price = (float) $product->get_price();

        $discounted_price = $price * 0.6667;

        // ako je ceo item u akciji
        if ($apply_qty === $qty) {
            $product->set_price($discounted_price);
        } else {
            // ako je deo → izračunaj prosečnu cenu (hack bez splitovanja)
            $full_qty = $qty - $apply_qty;

            $total =
                ($apply_qty * $discounted_price) +
                ($full_qty * $price);

            $avg_price = $total / $qty;

            $product->set_price($avg_price);
        }

        $processed += $apply_qty;
    }

}, 20);
*/

/**
 * Handle 2+1 discount in cart
 */
/*add_action('woocommerce_cart_calculate_fees', function ($cart) {

    if (is_admin() && !defined('DOING_AJAX'))
        return;

    $target_tags = [30, 31, 75, 83];

    $total_qty = 0;
    $eligible_items = [];

    // 1. Pronađi proizvode
    foreach ($cart->get_cart() as $cart_item) {

        $product_id = $cart_item['product_id'];

        if (has_term($target_tags, 'product_tag', $product_id)) {
            $eligible_items[] = $cart_item;
            $total_qty += $cart_item['quantity'];
        }
    }

    if ($total_qty < 3)
        return;

    // 2. Koliko komada ulazi u akciju
    $discounted_qty = floor($total_qty / 3) * 3;

    $processed = 0;
    $total_savings = 0;

    // 3. Izračunaj uštedu
    foreach ($eligible_items as $cart_item) {

        if ($processed >= $discounted_qty)
            break;

        $product = $cart_item['data'];
        $qty = $cart_item['quantity'];

        $remaining = $discounted_qty - $processed;
        $apply_qty = min($qty, $remaining);

        $price = (float) $product->get_price();

        // 33.33% ušteda
        $saving_per_item = $price * 0.3333;

        $total_savings += $saving_per_item * $apply_qty;

        $processed += $apply_qty;
    }

    if ($total_savings > 0) {
        $sets = floor($total_qty / 3);
        $cart->add_fee("Popust (2+1 akcija)", -$total_savings);
    }

});
*/


add_action('woocommerce_before_calculate_totals', 'custom_2plus1_discount', 20, 1);
function custom_2plus1_discount($cart) {

    if (is_admin() && !defined('DOING_AJAX')) return;

    $target_tags = [30, 31, 75, 83, 78 ];

    $eligible_items = [];
    $total_qty = 0;

    // 1. Reset cena (da ne ostane iz prethodnog refresh-a)
    foreach ($cart->get_cart() as $cart_item) {
        if (isset($cart_item['original_price'])) {
            $cart_item['data']->set_price($cart_item['original_price']);
        }
    }

    // 2. Pronađi proizvode u akciji
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {

        $product_id = $cart_item['product_id'];

        if (has_term($target_tags, 'product_tag', $product_id)) {

            // sačuvaj original cenu ako nije već
            if (!isset($cart_item['original_price'])) {
                $cart->cart_contents[$cart_item_key]['original_price'] = (float) $cart_item['data']->get_price();
            }

            $eligible_items[$cart_item_key] = $cart_item;
            $total_qty += $cart_item['quantity'];
        }
    }

    if ($total_qty < 3) return;

    // 3. Koliko komada ulazi u akciju
    $discounted_qty = floor($total_qty / 3) * 3;

    $processed = 0;

    // 4. Primeni popust po artiklu
    foreach ($eligible_items as $cart_item_key => $cart_item) {

        if ($processed >= $discounted_qty) break;

        $product = $cart_item['data'];
        $qty = $cart_item['quantity'];

        $remaining = $discounted_qty - $processed;
        $apply_qty = min($qty, $remaining);

        $original_price = isset($cart_item['original_price'])
    ? $cart_item['original_price']
    : (float) $cart_item['data']->get_regular_price();

        // 33.33% popust
        $discounted_price = $original_price * 0.666666666;

        // Ako cela količina ulazi u akciju
        if ($apply_qty == $qty) {
            $product->set_price($discounted_price);
        } else {
            // SPLIT item (deo sa popustom, deo bez)
            // Ovo je tricky deo — Woo ne deli automatski item
            // workaround: primeni prosečnu cenu

            $full_price_part = ($qty - $apply_qty) * $original_price;
            $discounted_part = $apply_qty * $discounted_price;

            $avg_price = ($full_price_part + $discounted_part) / $qty;

            $product->set_price($avg_price);
        }

        $processed += $apply_qty;
    }
}


add_filter('woocommerce_cart_item_price', 'custom_cart_item_price_html', 10, 3);
function custom_cart_item_price_html($price_html, $cart_item, $cart_item_key) {

    if (!isset($cart_item['original_price'])) {
        return $price_html;
    }

    $original_price = (float) $cart_item['original_price'];
    $current_price  = (float) $cart_item['data']->get_price();

    // Ako nema popusta, ne diraj
    if ($current_price >= $original_price) {
        return $price_html;
    }

    // Formatiraj cene
    $original = wc_price($original_price);
    $current  = wc_price($current_price);

    return '<del>' . $original . '</del> <ins>' . $current . '</ins>';
}


function get_2plus1_total_savings() {

    if (!WC()->cart) return 0;

    $total_savings = 0;

    foreach (WC()->cart->get_cart() as $cart_item) {

        if (!isset($cart_item['original_price'])) continue;

        $original_price = (float) $cart_item['original_price'];
        $current_price  = (float) $cart_item['data']->get_price();
        $qty            = $cart_item['quantity'];

        if ($current_price < $original_price) {
            $total_savings += ($original_price - $current_price) * $qty;
        }
    }

    return $total_savings;
}

add_action('woocommerce_cart_totals_before_order_total', function() {

    $savings = get_2plus1_total_savings();

    if ($savings <= 0) return;

    echo '<tr class="order-discount-2plus1">
        <th>Ušteda (2+1 akcija)</th>
        <td data-title="Ušteda">-' . wc_price($savings) . '</td>
    </tr>';
});


add_action('woocommerce_review_order_before_order_total', function() {

    $savings = get_2plus1_total_savings();

    if ($savings <= 0) return;

    echo '<tr class="order-discount-2plus1">
        <th>Ušteda (2+1 akcija)</th>
        <td>-' . wc_price($savings) . '</td>
    </tr>';
});



add_filter('woocommerce_add_cart_item_data', function ($cart_item_data, $product_id) {

    if (isset($_POST['flavor_1'])) {
        $cart_item_data['flavor_1'] = sanitize_text_field($_POST['flavor_1']);
    }

    if (isset($_POST['flavor_2'])) {
        $cart_item_data['flavor_2'] = sanitize_text_field($_POST['flavor_2']);
    }

    // da ne merge-uje iste proizvode
    $cart_item_data['unique_key'] = md5(microtime());

    return $cart_item_data;

}, 10, 2);

add_filter('woocommerce_get_item_data', function ($item_data, $cart_item) {

    if (!empty($cart_item['flavor_1'])) {
        $product = wc_get_product($cart_item['flavor_1']);
        $item_data[] = [
            'key' => 'Kombo proizvod 1',
            'value' => $product ? $product->get_name() : '',
        ];
    }

    if (!empty($cart_item['flavor_2'])) {
        $product = wc_get_product($cart_item['flavor_2']);
        $item_data[] = [
            'key' => 'Kombo proizvod 2',
            'value' => $product ? $product->get_name() : '',
        ];
    }

    return $item_data;

}, 10, 2);


add_action('woocommerce_checkout_create_order_line_item', function ($item, $cart_item_key, $values) {

    if (!empty($values['flavor_1'])) {
        $product = wc_get_product($values['flavor_1']);
        $item->add_meta_data('Kombo proizvod 1', $product ? $product->get_name() : '');
    }

    if (!empty($values['flavor_2'])) {
        $product = wc_get_product($values['flavor_2']);
        $item->add_meta_data('Kombo proizvod 2', $product ? $product->get_name() : '');
    }

}, 10, 3);