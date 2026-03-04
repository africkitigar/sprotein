<?php
/**
 * [top_picks parent="123"]
 */

add_shortcode('top_picks', function ($atts) {

    if (!class_exists('WooCommerce'))
        return;

    $atts = shortcode_atts([
        'parent' => 0
    ], $atts);

    $parent_id = intval($atts['parent']);

    if (!$parent_id)
        return;

    $terms = get_terms([
        'taxonomy' => 'product_cat',
        'parent' => $parent_id,
        'hide_empty' => true,
        'orderby' => 'menu_order',
        'order' => 'ASC'
    ]);

    if (!$terms)
        return;

    ob_start();
    ?>

    <div class="top-picks-section" data-parent="<?php echo esc_attr($parent_id); ?>">

        <div class="top-picks-left">

            <ul class="top-picks-cats">

                <?php foreach ($terms as $i => $term): ?>

                    <li class="<?php echo $i == 0 ? 'active' : ''; ?>">

                        <button class="top-picks-cat" data-cat="<?php echo esc_attr($term->term_id); ?>">
                            <?php echo esc_html($term->name); ?>
                        </button>

                    </li>

                <?php endforeach; ?>

            </ul>

        </div>


        <div class="top-picks-right">

            <div class="top-picks-products">

                <div class="skeleton-grid">
                    <?php for ($i = 0; $i < 6; $i++): ?>
                        <div class="skeleton-card"></div>
                    <?php endfor; ?>
                </div>

                <?php
                echo top_picks_products($terms[0]->term_id);
                ?>

            </div>

        </div>

    </div>

    <?php

    return ob_get_clean();
});

function top_picks_products($cat_id)
{

    $cache_key = 'top_picks_' . $cat_id;

    $cached = get_transient($cache_key);

    if ($cached !== false) {
        return $cached;
    }

    $args = [
        'post_type' => 'product',
        'posts_per_page' => 6,
        'post_status' => 'publish',
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'tax_query' => [
            'relation' => 'AND',

            [
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $cat_id
            ],

            [
                'taxonomy' => 'product_tag',
                'field' => 'term_id',
                'terms' => [29],
                'operator' => 'NOT IN'
            ]

        ]
    ];

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) {

        echo '<ul class="products-grid products columns-4">';

        while ($query->have_posts()) {
            $query->the_post();

            wc_get_template_part('content', 'product');

        }

        echo '</ul>';

    }

    wp_reset_postdata();

    $html = ob_get_clean();

    // cache 6 sati
    set_transient($cache_key, $html, 6 * HOUR_IN_SECONDS);

    return $html;
}


add_action('wp_ajax_top_picks_filter', 'top_picks_filter');
add_action('wp_ajax_nopriv_top_picks_filter', 'top_picks_filter');

add_action('save_post_product', function () {

    global $wpdb;

    $wpdb->query(
        "DELETE FROM $wpdb->options 
        WHERE option_name LIKE '_transient_top_picks_%'
        OR option_name LIKE '_transient_timeout_top_picks_%'"
    );

});

function top_picks_filter()
{

    $cat = intval($_POST['cat']);

    echo top_picks_products($cat);

    wp_die();
}




/**
 * end of [top_picks parent="123"]
 */





/**
 * Summary of custom_featured_products_slider
 * [featured_products_slider] - shortcode for featured products slider
 */
function custom_featured_products_slider()
{
    if (!function_exists('wc_get_products'))
        return '';

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
                <?php foreach ($products as $product):
                    // Setup global product post for WooCommerce templates
                    $post_object = get_post($product->get_id());
                    if (!$post_object)
                        continue;

                    global $post, $product;
                    $post = $post_object;
                    $product = wc_get_product($post->ID);
                    setup_postdata($post);
                    ?>
                    <div class="swiper-slide">
                        <ul class="products columns-4">
                        <?php wc_get_template_part('content', 'product'); ?>
                        </ul>
                    </div>
                <?php endforeach;
                wp_reset_postdata(); ?>
            </div>

        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Swiper('.swiperFeatured', {
                slidesPerView: 1.4,
                spaceBetween: 20,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                speed: 600,
                autoplay: {
                    delay: 6000, // Auto-slide every 3 seconds
                    disableOnInteraction: false, // Keep autoplay running even after user interaction
                },
                loop: true,
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














/**
 * [popular_categories]
 * [popular_categories ids="12,15,18"]
 */

add_shortcode('popular_categories', function ($atts) {

    $atts = shortcode_atts([
        'ids' => ''
    ], $atts);

    if ($atts['ids']) {

        $ids = array_map('intval', explode(',', $atts['ids']));

        $terms = get_terms([
            'taxonomy' => 'product_cat',
            'include' => $ids,
            'hide_empty' => true,
            'orderby' => 'include'
        ]);

    } else {

        $terms = get_terms([
            'taxonomy' => 'product_cat',
            'parent' => 0,
            'hide_empty' => true,
            'orderby' => 'menu_order'
        ]);

    }

    if (!$terms)
        return;

    ob_start();
    ?>

    <div class="popular-categories">

        <div class="popular-categories-slider swiper">

            <div class="swiper-wrapper">

                <?php foreach ($terms as $term):

                    $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);
                    $image = wp_get_attachment_image_url($thumbnail_id, 'large');

                    $link = get_term_link($term);
                    ?>

                    <div class="swiper-slide">

                        <a class="category-card" href="<?php echo esc_url($link); ?>">

                            <?php if ($image): ?>
                                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($term->name); ?>">
                            <?php endif; ?>

                            <span class="cat-title">
                                <?php echo esc_html($term->name); ?>
                            </span>

                        </a>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

    </div>

    <script>
        new Swiper('.popular-categories-slider', {

            slidesPerView: 2.5,
            spaceBetween: 24,

            breakpoints: {

                640: {
                    slidesPerView: 3
                },

                1024: {
                    slidesPerView: 3.5
                },

                1280: {
                    slidesPerView: 4
                }

            }

        });
    </script>

    <?php

    return ob_get_clean();

});