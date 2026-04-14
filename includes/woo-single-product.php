<?php

// Remove default price location
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);

// Add price below title (priority 5)
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 5);

//remove category in summary
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

// Move short description after the title on single product page
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 6);


// Remove WooCommerce product tabs from default position
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
add_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 1);

/**
 * 4 related products
 */

add_filter('woocommerce_output_related_products_args', 'custom_related_products_args');

function custom_related_products_args($args)
{
    // Set the number of related products to 3
    $args['posts_per_page'] = 4; // Number of related products
    $args['columns'] = 4; // Number of columns (optional, adjust as needed)

    return $args;
}

/** end of | 4 related products **/







/**
 * Enable gutenberg on single product
 */
function enable_gutenberg_for_products($can_edit, $post_type) {
    if ($post_type === 'product') {
        $can_edit = true;
    }
    return $can_edit;
}
add_filter('use_block_editor_for_post_type', 'enable_gutenberg_for_products', 10, 2);

/** end of |  Enable gutenberg on single product **/



/**
 * Change main description tab label to Više
 */
add_filter('woocommerce_product_tabs', 'rename_description_tab', 98);

function rename_description_tab($tabs) {

    if (isset($tabs['description'])) {
        $tabs['description']['title'] = 'Više';
    }

    return $tabs;
}


/**
 * Custom tabs on single product
 */
add_filter('woocommerce_product_tabs', function($tabs){

    unset($tabs['additional_information']);
    unset($tabs['reviews']);

    return $tabs;

}, 98);

add_filter('woocommerce_product_tabs', 'add_custom_acf_product_tabs', 20);

function add_custom_acf_product_tabs($tabs)
{

    global $product;

    if (!$product)
        return $tabs;

    $sastav = get_field('sastav', $product->get_id());
    $deklaracija = get_field('deklaracija', $product->get_id());
    $nacin_pripreme = get_field('nacin_pripreme', $product->get_id());

    /**
     * DESCRIPTION PRIORITY IS 10
     * 
     */

    if (!empty($sastav)) {
        $tabs['sastav_tab'] = array(
            'title' => __('Sastav', 'woocommerce'),
            'priority' => 23,
            'callback' => 'render_sastav_tab_content'
        );
    }

    if (!empty($nacin_pripreme)) {
        $tabs['nacin_pripreme_tab'] = array(
            'title' => __('Upotreba', 'woocommerce'),
            'priority' => 22,
            'callback' => 'render_nacin_pripreme_tab_content'
        );
    }

    if (!empty($nacin_pripreme)) {
        $tabs['deklaracija_tab'] = array(
            'title' => __('Deklaracija', 'woocommerce'),
            'priority' => 24,
            'callback' => 'render_deklaracija_tab_content'
        );
    }

    return $tabs;
}

function render_sastav_tab_content() {
    global $product;

    $product_id = $product->get_id();
    $image_id   = get_field('deklaracija_slika', $product_id);

    echo '<div class="woocommerce-Tabs-panel--sastav">';
    echo '<h5>Sastav</h5>';

    // tekst
    if (get_field('sastav', $product_id)) {
        echo '<div class="sastav-text">';
        the_field('sastav', $product_id);
        echo '</div>';
    }

    // slika (ako postoji)
    if ($image_id) {
        $image_full = wp_get_attachment_image_src($image_id, 'full');
        $image_thumb = wp_get_attachment_image($image_id, 'medium');

        echo '<div class="deklaracija-slika">';
        echo '<a href="' . esc_url($image_full[0]) . '" class="deklaracija-link">';
        echo $image_thumb;
        echo '</a>';
        echo '</div>';
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                document.querySelectorAll('.deklaracija-link').forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();

                        const src = this.getAttribute('href');

                        const overlay = document.createElement('div');
                        overlay.className = 'img-overlay';

                        overlay.innerHTML = `
                            <span class="close">&times;</span>
                            <img src="${src}" alt="">
                        `;

                        document.body.appendChild(overlay);

                        overlay.addEventListener('click', function () {
                            overlay.remove();
                        });
                    });
                });

            });
        </script>
        <?php
    }

    echo '</div>';
}

function render_nacin_pripreme_tab_content()
{
    global $product;
    echo '<div class="woocommerce-Tabs-panel--nacin-pripreme">';
    echo '<h5>Upotreba</h5>';
    the_field('nacin_pripreme', $product->get_id());
    echo '</div>';
}

function render_deklaracija_tab_content()
{
    global $product;
    echo '<div class="woocommerce-Tabs-panel--deklaracija">';
    echo '<h5>Deklaracija</h5>';
    the_field('deklaracija', $product->get_id());
    echo '</div>';
}

/**
 * end of - Custom tabs on single product
 */








/**
 * Custom content below add to cart in summary
 */
add_action('woocommerce_after_add_to_cart_form', function () {
    ?>
    <div class="product-delivery-info">

        <div class="delivery-item">
            <span class="delivery-icon">
                <!-- Truck -->
                <svg class="delivery-svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M3 7H15V17H3V7Z" stroke="currentColor" stroke-width="1.5" />
                    <path d="M15 11H19L22 14V17H15V11Z" stroke="currentColor" stroke-width="1.5" />
                    <circle cx="7" cy="17" r="2" stroke="currentColor" stroke-width="1.5" />
                    <circle cx="17" cy="17" r="2" stroke="currentColor" stroke-width="1.5" />
                </svg>
            </span>
            <span>Besplatna dostava na teritoriji Republike Srbije za porudžbine preko <strong>3500 RSD</strong></span>
        </div>

        <div class="delivery-item">
            <span class="delivery-icon">
                <!-- Clock -->
                <svg class="delivery-svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5" />
                    <path d="M12 7V12L15 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
            </span>
            <span>Rok za dostavu paketa na teritoriji Republike Srbije <strong>2 radna dana</strong></span>
        </div>

        <?php 
            global $product;
            
            if($product->is_on_sale()):

            $start = date_i18n('d. m.', strtotime('first day of this month'));
            $end = date_i18n('d. m. Y.', strtotime('last day of this month'));
        ?>
        <div class="delivery-item sale-validity">
            <span class="delivery-icon">
                <!-- Clock -->
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="5" x2="5" y2="19"/>
                    <circle cx="6.5" cy="6.5" r="2.5"/>
                    <circle cx="17.5" cy="17.5" r="2.5"/>
                </svg>
            </span>
            <span><?php  echo 'Ova akcija važi od ' . esc_html($start) . ' do ' . esc_html($end); ?></span>
        </div>
        <?php endif; //product is on sale ?>

    </div>
    <?php
});

/**
 * end of - Custom content below add to cart in summary
 */





/**
 * Show variable price with min value
 */
add_filter('woocommerce_variable_price_html', 'custom_variable_price_if_price_range', 10, 2);

function custom_variable_price_if_price_range($price, $product) {
    if ($product->is_type('variable')) {
        $variation_prices = $product->get_variation_prices(true); // true = including tax

        $prices = $variation_prices['price']; // array of variation prices
        $unique_prices = array_unique($prices);

        if (count($unique_prices) > 1) {
            $min_price = min($unique_prices);
            $formatted_price = wc_price($min_price);
            return 'ab ' . $formatted_price;
        }
    }

    return $price; // fallback to default price output
}

/**
 * end of - Show variable price with min value
 */








/**
 * Custom upsell products on single product
 */

add_action(
  'woocommerce_after_single_product_summary',
  'custom_category_based_upsells',
  2
);

function custom_category_based_upsells() {
  if (!is_product()) {
    return;
  }

  global $product;

  if (!$product) {
    return;
  }

  $product_id = $product->get_id();

  // Ako proizvod NIJE u kategoriji 16 ili 17 → ništa
  if (!has_term([16, 17], 'product_cat', $product_id)) {
    return;
  }

  $args = [
    'post_type'      => 'product',
    'posts_per_page' => 4,
    'orderby'        => 'rand',
    'post_status'    => 'publish',
    'post__not_in'   => [$product_id],
    'tax_query'      => [
      'relation' => 'AND',

      // Mora biti u kategoriji 22 ili 24
      [
        'taxonomy' => 'product_cat',
        'field'    => 'term_id',
        'terms'    => [22, 24],
        'operator' => 'IN',
      ],

      // Ne sme imati tag 29
      [
        'taxonomy' => 'product_tag',
        'field'    => 'term_id',
        'terms'    => [29],
        'operator' => 'NOT IN',
      ],
    ],
  ];

  $upsell_query = new WP_Query($args);

  if (!$upsell_query->have_posts()) {
    return;
  }

  echo '<section class="custom-upsells upsell-products">';
  echo '<h3 class="custom-upsells__title">Dodaj kreatin za maksimalne rezultate</h3>';
  echo '<ul class="custom-upsells__grid products columns-4">';

  while ($upsell_query->have_posts()) {
    $upsell_query->the_post();
    wc_get_template_part('content', 'product');
  }

  echo '</ul>';
  echo '</section>';

  wp_reset_postdata();
}
/** end of | Custom upsell products on single product **/



