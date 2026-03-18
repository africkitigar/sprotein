<?php
//remove sort on shop archive
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);

function set_woocommerce_products_per_page($cols)
{
    return 15; // Set to 3 products per page
}
add_filter('loop_shop_per_page', 'set_woocommerce_products_per_page', 20);

function remove_woocommerce_page_title()
{
    return false;
}
add_filter('woocommerce_show_page_title', 'remove_woocommerce_page_title');





// Set products per page to 24
add_filter('loop_shop_per_page', function($cols) {
    return 24;
}, 20);


/**
 * Move Woo "On sale" badge from gallery to summary and show % discount.
 */
add_action('init', function () {
  // Remove default sale flash from gallery area
  remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);

  // Add our custom badge in summary (before title)
  add_action('woocommerce_single_product_summary', 'my_sale_percentage_badge_in_summary', 4);
});

function my_sale_percentage_badge_in_summary() {
  if ( ! function_exists('wc_get_product') ) return;

  global $product;
  if ( ! $product instanceof WC_Product ) return;

  if ( ! $product->is_on_sale() ) return;

  $percentage = my_get_discount_percentage($product);
  if ( $percentage <= 0 ) return;

  // If variable product, you can show "Up to -xx%"
  $label = $product->is_type('variable')
    ? sprintf('Up to -%d%%', $percentage)
    : sprintf('-%d%%', $percentage);

  echo '<div class="my-onsale-badge" aria-label="Sale badge">'.$label.'</div>';
}

function my_get_discount_percentage( WC_Product $product ): int {
  // Simple / external (with sale)
  if ( $product->is_type('simple') || $product->is_type('external') ) {
    $regular = (float) $product->get_regular_price();
    $sale    = (float) $product->get_sale_price();

    if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
      return (int) round( ( ( $regular - $sale ) / $regular ) * 100 );
    }
    return 0;
  }

  // Variable: find max % across on-sale variations
  if ( $product->is_type('variable') ) {
    $max = 0;

    foreach ( $product->get_children() as $variation_id ) {
      $variation = wc_get_product($variation_id);
      if ( ! $variation || ! $variation->is_on_sale() ) continue;

      $regular = (float) $variation->get_regular_price();
      $sale    = (float) $variation->get_sale_price();

      if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
        $pct = (int) round( ( ( $regular - $sale ) / $regular ) * 100 );
        if ( $pct > $max ) $max = $pct;
      }
    }

    return $max;
  }

  return 0;
}




function display_product_categories_in_loop()
{
    global $product;
    $terms = get_the_terms($product->get_id(), 'product_cat');

    if ($terms && !is_wp_error($terms)) {
        // Sort categories so parent appears first
        usort($terms, function ($a, $b) {
            return ($a->parent === 0) ? -1 : 1;
        });

        echo '<div class="product-categories">';
        foreach ($terms as $term) {
            echo '<a href="' . get_term_link($term) . '">' . esc_html($term->name) . '</a> ';
        }
        echo '</div>';
    }
}

add_action('woocommerce_shop_loop_item_title', 'display_product_categories_in_loop', 15);








add_filter('woocommerce_pagination_args', 'custom_woocommerce_pagination_svg_arrows');

function custom_woocommerce_pagination_svg_arrows($args)
{
    // Replace next arrow with SVG
    $args['next_text'] = '<span class="custom-next-arrow">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M14.8334 10.2333L11.0084 6.40834C10.8523 6.25313 10.6411 6.16602 10.4209 6.16602C10.2008 6.16602 9.98955 6.25313 9.83341 6.40834C9.75531 6.48581 9.69331 6.57798 9.651 6.67953C9.6087 6.78108 9.58691 6.89 9.58691 7.00001C9.58691 7.11002 9.6087 7.21894 9.651 7.32049C9.69331 7.42204 9.75531 7.51421 9.83341 7.59168L13.6667 11.4083C13.7449 11.4858 13.8068 11.578 13.8492 11.6795C13.8915 11.7811 13.9132 11.89 13.9132 12C13.9132 12.11 13.8915 12.2189 13.8492 12.3205C13.8068 12.422 13.7449 12.5142 13.6667 12.5917L9.83341 16.4083C9.67649 16.5642 9.5879 16.7759 9.58712 16.9971C9.58633 17.2182 9.67343 17.4306 9.82925 17.5875C9.98506 17.7444 10.1968 17.833 10.418 17.8338C10.6391 17.8346 10.8515 17.7475 11.0084 17.5917L14.8334 13.7667C15.3016 13.2979 15.5645 12.6625 15.5645 12C15.5645 11.3375 15.3016 10.7021 14.8334 10.2333Z" fill="black"/>
</svg>
    </span>';

    // Replace previous arrow with SVG
    $args['prev_text'] = '<span class="custom-prev-arrow">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M10.8329 12.5917C10.7548 12.5142 10.6928 12.422 10.6505 12.3205C10.6082 12.2189 10.5864 12.11 10.5864 12C10.5864 11.89 10.6082 11.7811 10.6505 11.6795C10.6928 11.578 10.7548 11.4858 10.8329 11.4083L14.6579 7.59168C14.736 7.51421 14.798 7.42204 14.8403 7.32049C14.8826 7.21894 14.9044 7.11002 14.9044 7.00001C14.9044 6.89 14.8826 6.78108 14.8403 6.67953C14.798 6.57798 14.736 6.48581 14.6579 6.40834C14.5018 6.25313 14.2905 6.16602 14.0704 6.16602C13.8502 6.16602 13.639 6.25313 13.4829 6.40834L9.65789 10.2333C9.18972 10.7021 8.92676 11.3375 8.92676 12C8.92676 12.6625 9.18972 13.2979 9.65789 13.7667L13.4829 17.5917C13.6381 17.7456 13.8476 17.8324 14.0662 17.8333C14.1759 17.834 14.2846 17.813 14.3861 17.7715C14.4877 17.73 14.58 17.6689 14.6579 17.5917C14.736 17.5142 14.798 17.422 14.8403 17.3205C14.8826 17.2189 14.9044 17.11 14.9044 17C14.9044 16.89 14.8826 16.7811 14.8403 16.6795C14.798 16.578 14.736 16.4858 14.6579 16.4083L10.8329 12.5917Z" fill="#000"/>
</svg>
    </span>';

    return $args;
}















remove_action(
  'woocommerce_after_single_product_summary',
  'woocommerce_upsell_display',
  15
);


add_action('woocommerce_after_single_product_summary', 'custom_upsells_product_tab_content', 2);
function custom_upsells_product_tab_content() {
    global $product;

    $upsells = $product->get_upsell_ids();

    if ( ! empty( $upsells ) ) {
        // Temporarily override global $posts to show upsells
        $args = array(
            'post_type'      => 'product',
            'post__in'       => $upsells,
            'posts_per_page' => -1,
            'orderby'        => 'post__in'
        );

        $upsell_loop = new WP_Query( $args );

        if ( $upsell_loop->have_posts() ) {
            echo '<div class="upsell-products">';
            echo '<h3>Upsells</h3>';
            woocommerce_product_loop_start();
            while ( $upsell_loop->have_posts() ) {
                $upsell_loop->the_post();
                wc_get_template_part( 'content', 'product' );
            }
            woocommerce_product_loop_end();
            echo '</div>';
        }

        wp_reset_postdata();
    }
}



/**
 * Remove "Description" Heading Title @ WooCommerce Single Product Tabs
 */
add_filter('woocommerce_product_description_heading', '__return_null');
add_filter('woocommerce_product_additional_information_heading', '__return_null');
add_filter('woocommerce_reviews_title', '__return_null');



// Hook to modify the onsale badge and display percentage discount for variable products
add_filter('woocommerce_sale_flash', 'custom_woocommerce_variable_sale_percentage', 10, 3);

function custom_woocommerce_variable_sale_percentage($html, $post, $product)
{
    // Check if the product is on sale
    if ($product->is_type('variable')) {
        // Get available variations
        $available_variations = $product->get_available_variations();
        $regular_price = $sale_price = null;

        // Loop through the variations
        foreach ($available_variations as $variation) {
            $variation_obj = new WC_Product_Variation($variation['variation_id']);
            $variation_regular_price = $variation_obj->get_regular_price();
            $variation_sale_price = $variation_obj->get_sale_price();

            // Find the first variation with a sale price
            if ($variation_sale_price && $variation_regular_price > 0) {
                $regular_price = $variation_regular_price;
                $sale_price = $variation_sale_price;
                break;  // Break out of the loop once the first sale price is found
            }
        }

        // If a variation with sale price is found, calculate the percentage discount
        if ($regular_price && $sale_price) {
            $discount_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);

            // Return custom sale flash with percentage
            $html = '<span class="onsale"> ' . $discount_percentage . ' %</span>';
        }
    } elseif ($product->is_on_sale()) {
        // For simple products
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();

        if ($regular_price > 0) {
            $discount_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
            $html = '<span class="onsale">' . $discount_percentage . ' %</span>';
        }
    }

    return $html;
}




























// Change the "View Cart" button URL to the checkout page after adding to cart
/*add_filter('woocommerce_add_to_cart_fragments', function($fragments){

    ob_start();
    ?>

    <a class="cart-contents"
       href="<?php echo wc_get_cart_url(); ?>"
       title="Idi na plaćanje">

        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
            <path d="M13 27C13 27.3956 12.8827 27.7822 12.6629 28.1111C12.4432 28.44 12.1308 28.6964 11.7654 28.8478C11.3999 28.9991 10.9978 29.0387 10.6098 28.9616C10.2219 28.8844 9.86549 28.6939 9.58579 28.4142C9.30608 28.1345 9.1156 27.7781 9.03843 27.3902C8.96126 27.0022 9.00087 26.6001 9.15224 26.2346C9.30362 25.8692 9.55996 25.5568 9.88886 25.3371C10.2178 25.1173 10.6044 25 11 25C11.5304 25 12.0391 25.2107 12.4142 25.5858C12.7893 25.9609 13 26.4696 13 27ZM24 25C23.6044 25 23.2178 25.1173 22.8889 25.3371C22.56 25.5568 22.3036 25.8692 22.1522 26.2346C22.0009 26.6001 21.9613 27.0022 22.0384 27.3902C22.1156 27.7781 22.3061 28.1345 22.5858 28.4142C22.8655 28.6939 23.2219 28.8844 23.6098 28.9616C23.9978 29.0387 24.3999 28.9991 24.7654 28.8478C25.1308 28.6964 25.4432 28.44 25.6629 28.1111C25.8827 27.7822 26 27.3956 26 27C26 26.4696 25.7893 25.9609 25.4142 25.5858C25.0391 25.2107 24.5304 25 24 25Z"
                  fill="#000"/>
        </svg>

        <span class="cart-count">
            <?php echo WC()->cart->get_cart_contents_count(); ?>
        </span>

    </a>

    <?php

    $fragments['a.cart-contents'] = ob_get_clean();

    return $fragments;
});*/
add_filter('woocommerce_add_to_cart_fragments', function($fragments){

    ob_start();
    ?>
    <span class="cart-count cart-contents">
        <?php echo WC()->cart->get_cart_contents_count(); ?>
    </span>
    <?php

    $fragments['.cart-count'] = ob_get_clean();

    return $fragments;

});



function change_add_to_cart_button_text($text) {
    if (WC()->cart->get_cart_contents_count() > 0) {
        return __('Nastavite na plaćanje', 'woocommerce');
    }
    return $text; // Default 'Add to Cart' text
}









add_filter('wc_add_to_cart_message_html', function ($message, $products) {
    $checkout_url = wc_get_checkout_url(); // Get checkout page URL

    // Replace the cart link with the checkout link
    $message = preg_replace('/<a.*?class="button wc-forward".*?>.*?<\/a>/', '<a href="' . esc_url($checkout_url) . '" class="button wc-forward">Idi na plaćanje</a>', $message);

    return $message;
}, 10, 2);








add_filter('woocommerce_page_title', function($title){

    if (is_search()) {
        global $wp_query;

        $count = $wp_query->found_posts;
        $title = 'Pronađeno ' . $count . ' proizvoda za: ' . get_search_query();
    }

    return $title;

});






/**
 * Summary of add_woocommerce_category_accordion
 * Make accordions in sidebar filters
 */
function add_woocommerce_category_accordion() {
    // Check if this is a product category page
    if (is_product_category() && strpos($_SERVER['REQUEST_URI'], '?') === false ) {
        ?>
        <script>
jQuery(document).ready(function($) {
    // Wait for WooCommerce filters to be fully rendered
    function initAccordion() {
        const $filters = $('.woo-filters .wp-block-woocommerce-attribute-filter');
        
        // Check if filters exist and are fully rendered
        if ($filters.length > 0 && $filters.find('ul').length > 0) {
            // Initialize accordion
            $filters.parent().parent().parent().addClass('initialized');
            
            // Hide all except first
            $('.initialized').not(':first').find('.wp-block-woocommerce-attribute-filter').slideUp(0);
            
            // Add active class to first title
            $('.woo-filters .initialized .wc-blocks-filter-wrapper:not([hidden]):first > h3.wp-block-heading').addClass('active');

            $('.woo-filters .initialized .wc-blocks-filter-wrapper:not([hidden]):first > h3.wp-block-heading').next().show();
            
            // Set up click handlers
            $('.woo-filters .wc-blocks-filter-wrapper > h3.wp-block-heading').on('click', function() {
                $(this).toggleClass('active');
                $(this).next('.wp-block-woocommerce-attribute-filter').stop(true, true).slideToggle(300);
            });
        } else {
            // Try again in 100ms if filters aren't ready
            setTimeout(initAccordion, 100);
        }
    }
    
    // Start checking for filters
    initAccordion();
    
    // Additional fallback in case of dynamic loading
    $(document).on('updated_checkout updated_cart_totals updated_wc_div', initAccordion);
});
        </script>

        <?php
    } elseif( is_product_category() && strpos($_SERVER['REQUEST_URI'], '?') != false ){ ?>
        <script>
        jQuery(document).ready(function($) {

    function initAccordion() {
        const $filters = $('.woo-filters .wp-block-woocommerce-attribute-filter');
        
        // Check if filters exist and are fully rendered
        if ($filters.length > 0 && $filters.find('ul').length > 0) {
            // Initialize accordion
            $filters.parent().parent().parent().addClass('initialized');

            $('.wp-block-woocommerce-attribute-filter').each(function() {
        const $filterBlock = $(this);
        const $prevH3 = $filterBlock.prev('h3');
        
        if ($filterBlock.find('input[type="checkbox"]:checked').length > 0) {
            $filterBlock.show();
            $prevH3.addClass('active');
        } else {
            $filterBlock.hide();
            $prevH3.removeClass('active');
        }
    });

    // Handle changes
    $(document).on('change', '.wp-block-woocommerce-attribute-filter input[type="checkbox"]', function() {
        const $filterBlock = $(this).closest('.wp-block-woocommerce-attribute-filter');
        const $prevH3 = $filterBlock.prev('h3');
        
        if ($(this).is(':checked')) {
            $filterBlock.slideDown();
            $prevH3.addClass('active');
        } else {
            if ($filterBlock.find('input[type="checkbox"]:checked').length === 0) {
                $filterBlock.slideUp();
                $prevH3.removeClass('active');
            }
        }
    });
            
            // Hide all except first
          //  $('.initialized').not(':first').find('.wp-block-woocommerce-attribute-filter').slideUp(0);
            
            // Add active class to first title
          //  $('.woo-filters .initialized .wc-blocks-filter-wrapper:not([hidden]):first > h3.wp-block-heading').addClass('active');

       //     $('.woo-filters .initialized .wc-blocks-filter-wrapper:not([hidden]):first > h3.wp-block-heading').next().show();
            
            // Set up click handlers
            $('.woo-filters .wc-blocks-filter-wrapper > h3.wp-block-heading').on('click', function() {
                $(this).toggleClass('active');
                $(this).next('.wp-block-woocommerce-attribute-filter').stop(true, true).slideToggle(300);
            });
        } else {
            // Try again in 100ms if filters aren't ready
            setTimeout(initAccordion, 100);
        }
    }
    
    // Start checking for filters
    initAccordion();
    
    // Additional fallback in case of dynamic loading
    $(document).on('updated_checkout updated_cart_totals updated_wc_div', initAccordion);


    
});</script>
   <?php }//endif
}
add_action('wp_footer', 'add_woocommerce_category_accordion');






/**
 * Category bottom description (ACF field)
 */
add_action('woocommerce_after_shop_loop', 'wc_category_bottom_description', 20);
function wc_category_bottom_description() {

    if (!is_product_category()) {
        return;
    }

    $term = get_queried_object();

    if (!$term || empty($term->term_id)) {
        return;
    }

    $bottom_description = get_field('bottom_description', 'product_cat_' . $term->term_id);

    if ($bottom_description) {
        echo '<div class="category-bottom-description">';
        echo wp_kses_post($bottom_description);
        echo '</div>';
    }
}









/*

function sp_get_protein_promo_message() {

  if (!WC()->cart) {
    return '';
  }

  $protein_count = 0;

  foreach (WC()->cart->get_cart() as $cart_item) {
    $cart_product_id = $cart_item['product_id'];

    if (has_term(17, 'product_cat', $cart_product_id)) {
      $protein_count += (int) $cart_item['quantity'];
    }
  }

  // ✅ SUCCESS STATE → vidi se SVUDA
  if ($protein_count >= 3) {
    return '
    <svg class="promo-icon promo-icon--check" width="20" height="20" viewBox="0 0 24 24" fill="none">
      <path d="M5 13l4 4L19 7"
            stroke="currentColor" stroke-width="1.8"
            stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
     <strong>Čestitamo!</strong> Ostvario si <strong>2+1 GRATIS</strong> akciju!
    ';
  }

  // ❗ sve ispod važi SAMO na product page
  if (!is_product()) {
    return '';
  }

  $product_id = get_queried_object_id();

  if (!$product_id || !has_term(17, 'product_cat', $product_id)) {
    return '';
  }

  // ostala logika (0 / 1 / 2 komada)
  if (WC()->cart->is_empty() || $protein_count === 0) {
    return '💥 Kupi <strong>2 proteina</strong> i <strong>treći dobijaš GRATIS</strong>!';
  }

  if ($protein_count === 1) {
    return '➕ Dodaj još <strong>2 proteina</strong> i ostvari <strong>2+1 GRATIS</strong>!';
  }

  if ($protein_count === 2) {
    return '🔥 Još <strong>1 protein</strong> te deli od <strong>BESPLATNOG trećeg</strong>!';
  }

  return '';
}
*/

/*
add_action('woocommerce_cart_calculate_fees', 'sp_apply_protein_2plus1_discount');

function sp_apply_protein_2plus1_discount(WC_Cart $cart) {

  if (is_admin() && !defined('DOING_AJAX')) {
    return;
  }

  $protein_items = [];
  $protein_qty   = 0;

  foreach ($cart->get_cart() as $cart_item) {
    $product_id = $cart_item['product_id'];

    if (has_term(17, 'product_cat', $product_id)) {
      $protein_qty += $cart_item['quantity'];

      for ($i = 0; $i < $cart_item['quantity']; $i++) {
        $protein_items[] = $cart_item['data']->get_price();
      }
    }
  }

  // nema uslova
  if ($protein_qty < 3) {
    return;
  }

  // treći (najjeftiniji) je gratis
  sort($protein_items);
  $free_price = (float) $protein_items[0];

  if ($free_price <= 0) {
    return;
  }

  $cart->add_fee(
    __('Protein 2+1 GRATIS', 'superprotein'),
    -$free_price,
    false
  );
}
*/






add_filter( 'template_include', 'load_custom_single_for_tag_29', 99 );

function load_custom_single_for_tag_29( $template ) {

    if ( is_product() ) {

        $product_id = get_queried_object_id();

        if ( has_term( 29, 'product_tag', $product_id ) ) {

            $custom = locate_template( 'woocommerce/single-product-special.php' );

            if ( $custom ) {
                return $custom;
            }
        }
    }

    return $template;
}



add_filter( 'wc_get_template_part', 'load_custom_loop_template_for_tag_29', 10, 3 );

function load_custom_loop_template_for_tag_29( $template, $slug, $name ) {

    if ( $slug === 'content' && $name === 'product' ) {

        global $product;

        if ( $product && has_term( 29, 'product_tag', $product->get_id() ) ) {

            $custom = locate_template( 'woocommerce/content-product-special.php' );

            if ( $custom ) {
                return $custom;
            }
        }
    }

    return $template;
}


add_filter( 'woocommerce_related_products', 'exclude_tag_29_from_related', 10, 3 );

function exclude_tag_29_from_related( $related_posts, $product_id, $args ) {

    foreach ( $related_posts as $key => $related_id ) {

        if ( has_term( 29, 'product_tag', $related_id ) ) {
            unset( $related_posts[$key] );
        }
    }

    return $related_posts;
}


add_filter( 'woocommerce_upsell_display_args', function( $args ) {
    return $args;
});

add_filter( 'woocommerce_upsell_ids', 'exclude_tag_29_from_upsells', 10, 2 );

function exclude_tag_29_from_upsells( $upsells, $product_id ) {

    foreach ( $upsells as $key => $upsell_id ) {

        if ( has_term( 29, 'product_tag', $upsell_id ) ) {
            unset( $upsells[$key] );
        }
    }

    return $upsells;
}

add_filter( 'woocommerce_cross_sells', 'exclude_tag_29_from_cross_sells' );

function exclude_tag_29_from_cross_sells( $cross_sells ) {

    foreach ( $cross_sells as $key => $id ) {

        if ( has_term( 29, 'product_tag', $id ) ) {
            unset( $cross_sells[$key] );
        }
    }

    return $cross_sells;
}


/**
 * 2+1 Akcija – 3 dropdowna na akcijskom proizvodu + dodavanje u korpu (bez redirecta)
 * Slug akcijskog proizvoda: akcija-2-1-gratis
 * Kategorija proteina: proteini
 */


/*
add_action( 'woocommerce_before_calculate_totals', function( $cart ) {

    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {

        if ( isset( $cart_item['is_free_flavor'] ) ) {
            $cart_item['data']->set_price( 0 );
        }
    }

});*/








/**
 * Custom related heading regarding the tag of the product
 */
add_filter('woocommerce_product_related_products_heading', function($heading){

    if (!is_product()) {
        return $heading;
    }

    global $product;

    $tags = wp_get_post_terms($product->get_id(), 'product_tag', ['fields'=>'ids']);

    if (in_array(30, $tags)) {
        return 'Ostali Ultra Whey ukusi';
    }

    if (in_array(31, $tags)) {
        return 'Ostali Strong Whey ukusi';
    }

    return $heading;

});
/**
 * Custom query for related products if they are in specific tags
 */
add_filter('woocommerce_related_products', function($related_ids, $product_id, $args){

    $target_tags = [30,31];

    $tags = wp_get_post_terms($product_id,'product_tag',['fields'=>'ids']);

    $matched = array_intersect($tags,$target_tags);

    if(!$matched){
        return $related_ids;
    }

    $query = new WP_Query([
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post__not_in' => [$product_id],
        'tax_query' => [
            [
                'taxonomy' => 'product_tag',
                'field' => 'term_id',
                'terms' => $matched
            ]
        ]
    ]);

    if($query->have_posts()){

        $ids = [];

        foreach($query->posts as $p){
            $ids[] = $p->ID;
        }

        return $ids;
    }

    return $related_ids;

},10,3);