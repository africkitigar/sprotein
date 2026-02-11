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


function custom_woocommerce_result_count()
{
    global $wp_query;

    if ($wp_query->found_posts > 0) {
        echo '<p class="custom-result-count">' . sprintf(
            _n('Zeige %d Produkt', 'Zeige %d Produkte', $wp_query->found_posts, 'woocommerce'),
            $wp_query->found_posts
        ) . '</p>';
    }
}



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




// Move short description after the title on single product page
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 6);

//remove category in summary
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);





// Remove WooCommerce product tabs from default position
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);

add_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 1);

// Add the tabs after product summary with accordion structure
/*add_action('woocommerce_after_single_product_summary', 'custom_woocommerce_output_product_tabs', 1);

function custom_woocommerce_output_product_tabs()
{
    global $product;

    // Get the product tabs
    $tabs = apply_filters('woocommerce_product_tabs', array());

    // Get all product attributes
    $attributes = $product->get_attributes();

    if (!empty($tabs)  || !empty( $attributes )) {
        echo '<div class="woocommerce-accordion-tabs">';


        foreach ($tabs as $key => $tab) {
            // Output the tab title with the "accordion-title" class
            echo '<div class="accordion"><div class="accordion-title" data-tab-key="' . esc_attr($key) . '">';
            echo esc_html($tab['title']);
            echo '</div>';

            // Output the tab content with the "accordion-content" class
            echo '<div class="accordion-content" id="content-' . esc_attr($key) . '">';
            if (isset($tab['callback']) && is_callable($tab['callback'])) {
                // Capture and display the content from the callback
                call_user_func($tab['callback'], $key, $tab);
            }
            echo '</div></div>';
        }//endoforeach 

        ?>


        <?php

        echo '</div>'; // End of woocommerce-accordion-tabs
    }
}
*/

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



add_filter('woocommerce_output_related_products_args', 'custom_related_products_args');

function custom_related_products_args($args)
{
    // Set the number of related products to 3
    $args['posts_per_page'] = 4; // Number of related products
    $args['columns'] = 4; // Number of columns (optional, adjust as needed)

    return $args;
}









add_action('woocommerce_single_product_summary', 'custom_sku_below_title', 5);
function custom_sku_below_title()
{
    global $product;
    if ($product->get_sku() && !$product->is_type('variable')) {
        echo '<p class="custom-sku"><b>Artikelcode:</b> ' . $product->get_sku() . '</p>';
    }

    if ($product->is_type('variable')) {
        echo '<div class="woocommerce-variation-sku custom-sku">';
        echo '<span class="label"><strong>' . esc_html__('Artikelcode:', 'woocommerce') . '</strong></span> ';
        echo '<span class="value">' . esc_html($product->get_sku()) . '</span>';
        echo '</div>';
        
        wc_enqueue_js("
            jQuery(function($) {
                $('form.variations_form').on('show_variation', function(event, variation) {
                    $('.woocommerce-variation-sku .value').text(variation.sku);
                }).on('hide_variation', function() {
                    $('.woocommerce-variation-sku .value').text('" . esc_js($product->get_sku()) . "');
                });
            });
        ");
    }
}


// Remove default price location
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);

// Add price below title (priority 5)
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 5);









add_filter('woocommerce_get_breadcrumb', function ($crumbs) {
    if (!empty($crumbs)) {
        // Get the last breadcrumb
        $last_index = count($crumbs) - 1;

        // Add a class to the last breadcrumb
        $crumbs[$last_index][0] = ' &nbsp; &nbsp; ' . esc_html($crumbs[$last_index][0]);
    }

    return $crumbs;
}, 10, 1);









// Change the "View Cart" button URL to the checkout page after adding to cart
add_filter('woocommerce_add_to_cart_fragments', function($fragments){

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









/**
 * Summary of custom_featured_products_slider
 * [featured_products_slider] - shortcode for featured products slider
 */
function custom_featured_products_slider() {
    if (!function_exists('wc_get_products')) return '';

    ob_start();

 

    // Query WooCommerce Featured Products
    $args = array(
        'limit' => 10, // Adjust the number of products
        'status' => 'publish',
        'featured' => true,
    );
    $products = wc_get_products($args);

    if (!$products) {
        return '<p>No featured products found.</p>';
    }
    ?>

    <div class="custom-swiper-container featured-products-slider">
        <div class="swiperFeatured">
            <div class="swiper-wrapper">
                <?php foreach ($products as $product) : 
                    // Setup global product post for WooCommerce templates
                    $post_object = get_post($product->get_id());
                    if (!$post_object) continue;

                    global $post, $product;
                    $post = $post_object;
                    $product = wc_get_product($post->ID);
                    setup_postdata($post);
                ?>
                    <div class="swiper-slide">
                        <?php wc_get_template_part('content', 'product'); ?>
                    </div>
                <?php endforeach; wp_reset_postdata(); ?>
            </div>
           
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Swiper('.swiperFeatured', {
                slidesPerView: 1.4,
                spaceBetween: 20,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                speed:600,
                autoplay: {
                    delay: 6000, // Auto-slide every 3 seconds
                    disableOnInteraction: false, // Keep autoplay running even after user interaction
                },
                loop:true,
                grabCursor: true, // Enables drag cursor
                freeMode: true, // Allows smooth dragging
                breakpoints: {
                    1600: { slidesPerView: 5.5 },
                    1024: { slidesPerView: 3.7 },
                    480: { slidesPerView: 2.8 },
                }
            });
        });
    </script>

    <?php
    return ob_get_clean();
}

add_shortcode('featured_products_slider', 'custom_featured_products_slider');





function enable_gutenberg_for_products($can_edit, $post_type) {
    if ($post_type === 'product') {
        $can_edit = true;
    }
    return $can_edit;
}
add_filter('use_block_editor_for_post_type', 'enable_gutenberg_for_products', 10, 2);











/**
 * Completely remove WooCommerce upsell products
 */
/*
function remove_woocommerce_upsells() {
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
}
add_action( 'woocommerce_after_single_product_summary', 'remove_woocommerce_upsells', 1 );

*/




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





add_action('woocommerce_after_add_to_cart_form', function () {
  ?>
  <div class="product-delivery-info">

    <div class="delivery-item">
      <span class="delivery-icon">
        <!-- Truck -->
        <svg class="delivery-svg" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M3 7H15V17H3V7Z" stroke="currentColor" stroke-width="1.5"/>
          <path d="M15 11H19L22 14V17H15V11Z" stroke="currentColor" stroke-width="1.5"/>
          <circle cx="7" cy="17" r="2" stroke="currentColor" stroke-width="1.5"/>
          <circle cx="17" cy="17" r="2" stroke="currentColor" stroke-width="1.5"/>
        </svg>
      </span>
      <span>Besplatna dostava preko <strong>3500 RSD</strong></span>
    </div>

    <div class="delivery-item">
      <span class="delivery-icon">
        <!-- Clock -->
        <svg class="delivery-svg" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
          <path d="M12 7V12L15 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
      </span>
      <span>Rok za dostavu paketa <strong>2 radna dana</strong></span>
    </div>

  </div>
  <?php
});





add_action(
  'woocommerce_product_additional_information',
  function ($product) {

    if (!is_product()) {
      return;
    }

    $product_id = $product->get_id();

    $text  = get_field('additional_tab_title_text', $product_id);
    $image = get_field('additional_tab_image', $product_id);

    // ako nema ništa — ne radimo ništa
    if (!$text && !$image) {
      return;
    }
    ?>
    <div class="wc-additional-custom-content">

      <?php if ($text): ?>
        <div class="wc-additional-custom-text">
          <?php echo wp_kses_post($text); ?>
        </div>
      <?php endif; ?>

      <?php if ($image && isset($image['url'])): ?>
        <div class="wc-additional-custom-image">
          <img
            src="<?php echo esc_url($image['url']); ?>"
            alt="<?php echo esc_attr($image['alt'] ?? ''); ?>"
            loading="lazy"
          />
        </div>
      <?php endif; ?>

    </div>
    <?php
  }
);







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

  // ako proizvod NIJE u kategoriji 16 → ništa
  if (!has_term(16, 'product_cat', $product->get_id())) {
    return;
  }

  $args = [
    'post_type'      => 'product',
    'posts_per_page' => 4,
    'orderby'        => 'rand',
    'post_status'    => 'publish',
    'post__not_in'   => [$product->get_id()],
    'tax_query'      => [
      [
        'taxonomy' => 'product_cat',
        'field'    => 'term_id',
        'terms'    => [22],
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








/*
function sp_get_protein_promo_message() {
  if (!is_product()) {
    return '';
  }

  $product_id = get_queried_object_id();

  if (!$product_id) {
    return '';
  }

  // učitaj WC product objekat BEZ oslanjanja na global $product
  $product = wc_get_product($product_id);

  if (!$product) {
    return '';
  }

  // radi samo za proizvode iz kategorije 17
  if (!has_term(17, 'product_cat', $product_id)) {
    return '';
  }

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

  // 1️⃣ prazna korpa ILI nema proteina
  if (WC()->cart->is_empty() || $protein_count === 0) {
   return '
<svg class="promo-icon promo-icon--pulse" width="20" height="20" viewBox="0 0 24 24" fill="none">
  <path d="M8 2h8v2h-1v2l2 3v11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V9l2-3V4H8V2Z"
        stroke="currentColor" stroke-width="1.5"/>
</svg>
Kupi <strong>2 proteina</strong> i <strong>treći dobijaš GRATIS</strong>!
';

  }

  // 2️⃣ ima 1 protein
  if ($protein_count === 1) {
    return '
<svg class="promo-icon promo-icon--bounce" width="20" height="20" viewBox="0 0 24 24" fill="none">
  <path d="M12 5v14M5 12h14"
        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
</svg>

Dodaj još <strong>2 proteina</strong> i ostvari <strong>2+1 GRATIS</strong>!
';

  }

  // 3️⃣ ima 2 proteina
  if ($protein_count === 2) {
    return '<svg class="promo-icon promo-icon--bounce" width="20" height="20" viewBox="0 0 24 24" fill="none">
  <path d="M12 5v14M5 12h14"
        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
</svg> Još <strong>1 protein</strong> te deli od <strong>BESPLATNOG trećeg</strong>!';
  }

  // 4️⃣ ima 3 ili više
  if ($protein_count >= 3) {
return '
<svg class="promo-icon promo-icon--check" width="20" height="20" viewBox="0 0 24 24" fill="none">
  <path d="M5 13l4 4L19 7"
        stroke="currentColor" stroke-width="1.8"
        stroke-linecap="round" stroke-linejoin="round"/>
</svg>

<strong>Bravo!</strong> Treći protein ti je <strong>BESPLATAN</strong>
';


  }

  return '';
}
*/
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









/**
 * 2+1 Akcija – 3 dropdowna na akcijskom proizvodu + dodavanje u korpu (bez redirecta)
 * Slug akcijskog proizvoda: akcija-2-1-gratis
 * Kategorija proteina: proteini
 */

/**
 * 1) Forma na single productu (akcijski proizvod)
 */
add_action('woocommerce_single_product_summary', 'akcija_21_forma', 25);
function akcija_21_forma() {
    global $product;

    if (!$product || $product->get_slug() !== 'akcija-2-1-gratis') return;

    $q = new WP_Query([
        'post_type' => 'product',
        'posts_per_page' => 6,
        'tax_query' => [
            [
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => 'proteini',
            ]
        ]
    ]);

    if (!$q->have_posts()) return;

    $options = [];

    while ($q->have_posts()) {
        $q->the_post();
        $options[] = [
            'id' => get_the_ID(),
            'title' => get_the_title()
        ];
    }

    wp_reset_postdata();
    ?>

    <div class="akcija-21-wrap">
        <h3>Akcija 2+1 gratis</h3>

        <div class="akcija-21-form">

            <?php for ($i=1; $i<=3; $i++): ?>
                <select class="akcija-select">
                    <option value="">Izaberi ukus (<?php echo $i; ?>)</option>
                    <?php foreach ($options as $opt): ?>
                        <option value="<?php echo $opt['id']; ?>">
                            <?php echo esc_html($opt['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endfor; ?>

            <button type="button" class="button alt akcija-dodaj">
                Dodaj u korpu
            </button>

        </div>
    </div>

    <?php
}






add_action('wp_footer', function() {

    if (!is_product()) return;

    ?>

<script>
jQuery(function($){

    $('.akcija-dodaj').on('click', function(){

        let ids = [];
        let valid = true;

        $('.akcija-select').each(function(){
            let val = $(this).val();
            if(!val){
                valid = false;
            } else {
                ids.push(val);
            }
        });

        if(!valid || ids.length !== 3){
            alert('Morate izabrati 3 proizvoda.');
            return;
        }

        let i = 0;

        /*function addNext() {

            if (i >= ids.length) {

                $(document.body).trigger('wc_fragment_refresh');
                $(document.body).trigger('added_to_cart');

                // ukloni stari notice ako postoji
                $('.akcija-success').remove();

                // ubaci novi notice ispod breadcrumb-a
                $('.woocommerce-breadcrumb').after(
                    '<div class="akcija-success" style="background:#d4edda;color:#155724;padding:12px 16px;margin:15px 0;border-radius:4px;">' +
                    '✔ Uspešno ste dodali 3 proizvoda u korpu.' +
                    '</div>'
                );

                return;
            }


            $.ajax({
                type: 'POST',
                url: wc_add_to_cart_params.wc_ajax_url
                        .replace('%%endpoint%%', 'add_to_cart'),
                data: {
                    product_id: ids[i],
                    quantity: 1
                },
                success: function() {
                    i++;
                    addNext(); // pozovi sledeći
                }
            });

        }
*/
        function addNext() {

    if (i >= ids.length) {
        return;
    }

    $.ajax({
        type: 'POST',
        url: wc_add_to_cart_params.wc_ajax_url
                .replace('%%endpoint%%', 'add_to_cart'),
        data: {
            product_id: ids[i],
            quantity: 1
        },
        success: function(response) {

            if (response.fragments) {
                $.each(response.fragments, function(key, value) {
                    $(key).replaceWith(value);
                });
            }

            i++;
            addNext();
        }
    });
}


        addNext();

    });

});
</script>

<?php
});
