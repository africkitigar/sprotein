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
                    480: { slidesPerView: 1.6 },
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

                    // $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);
                    //$image = wp_get_attachment_image_url($thumbnail_id, 'large');
                    $image_id = get_field('additional_category_image', 'product_cat_' . $term->term_id);

                    if ($image_id) {
                        $image = wp_get_attachment_image_url($image_id, 'large');
                    }

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

            slidesPerView: 1.6,
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











/**
 * [selected_products ids="123,456,789,101"]
 */
add_shortcode('selected_products', function ($atts) {

    $atts = shortcode_atts([
        'ids' => '',
    ], $atts);

    if (!$atts['ids'])
        return '';

    $ids = array_map('intval', explode(',', $atts['ids']));
    $count = count($ids);

    $args = [
        'post_type' => 'product',
        'post__in' => $ids,
        'orderby' => 'post__in',
        'posts_per_page' => -1
    ];

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()):

        // GRID ako ima 1 ili 2 proizvoda
        if ($count <= 2) {

    echo '<div class="selected-products-grid">';

    while ($query->have_posts()) :
        $query->the_post();
        global $product;

        echo '<div class="selected-product-card">';

            echo '<a class="selected-product-image" href="' . get_permalink() . '">';
                echo $product->get_image('medium');
            echo '</a>';

            echo '<h3 class="selected-product-title">';
                echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
            echo '</h3>';

            echo '<div class="selected-product-price">';
                echo $product->get_price_html();
            echo '</div>';

            echo '<a class="button selected-product-btn" href="' . get_permalink() . '">';
                echo 'Pogledaj proizvod';
            echo '</a>';

        echo '</div>';

    endwhile;

    echo '</div>';

} else {

            // SWIPER
            echo '<div class="selected-products-swiper swiper">';
            echo '<div class="swiper-wrapper">';

            while ($query->have_posts()):
                $query->the_post();

                echo '<div class="swiper-slide">';
                wc_get_template_part('content', 'product');
                echo '</div>';

            endwhile;

            echo '</div>';

            echo '<div class="swiper-button-next"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M16.9997 12.0001C16.9957 11.5617 16.819 11.1426 16.5081 10.8335L12.9331 7.25014C12.7769 7.09493 12.5657 7.00781 12.3456 7.00781C12.1254 7.00781 11.9142 7.09493 11.7581 7.25014C11.68 7.32761 11.618 7.41978 11.5757 7.52133C11.5334 7.62288 11.5116 7.7318 11.5116 7.84181C11.5116 7.95182 11.5334 8.06074 11.5757 8.16229C11.618 8.26384 11.68 8.356 11.7581 8.43347L14.4997 11.1668H6.1664C5.94539 11.1668 5.73343 11.2546 5.57715 11.4109C5.42087 11.5672 5.33307 11.7791 5.33307 12.0001C5.33307 12.2212 5.42087 12.4331 5.57715 12.5894C5.73343 12.7457 5.94539 12.8335 6.1664 12.8335H14.4997L11.7581 15.5751C11.6012 15.731 11.5126 15.9427 11.5118 16.1639C11.511 16.385 11.5981 16.5974 11.7539 16.7543C11.9097 16.9112 12.1215 16.9998 12.3426 17.0006C12.5638 17.0014 12.7761 16.9143 12.9331 16.7585L16.5081 13.1751C16.8211 12.864 16.9979 12.4415 16.9997 12.0001Z" fill="#101223"></path>
          </svg></div>';
            echo '<div class="swiper-button-prev"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M17.8334 11.1664H9.50006L12.2417 8.42468C12.3198 8.34721 12.3818 8.25505 12.4241 8.1535C12.4664 8.05195 12.4882 7.94303 12.4882 7.83302C12.4882 7.72301 12.4664 7.61409 12.4241 7.51254C12.3818 7.41099 12.3198 7.31882 12.2417 7.24135C12.0856 7.08614 11.8744 6.99902 11.6542 6.99902C11.4341 6.99902 11.2229 7.08614 11.0667 7.24135L7.49173 10.8247C7.17874 11.1358 7.00192 11.5584 7.00006 11.9997C7.00412 12.4381 7.18076 12.8573 7.49173 13.1664L11.0667 16.7497C11.1444 16.8268 11.2366 16.8879 11.3379 16.9295C11.4392 16.971 11.5477 16.9922 11.6572 16.9918C11.7667 16.9914 11.875 16.9695 11.976 16.9272C12.077 16.885 12.1687 16.8232 12.2459 16.7455C12.323 16.6678 12.3841 16.5757 12.4257 16.4744C12.4672 16.3731 12.4884 16.2646 12.488 16.1551C12.4876 16.0456 12.4657 15.9372 12.4234 15.8362C12.3812 15.7352 12.3194 15.6435 12.2417 15.5664L9.50006 12.833H17.8334C18.0544 12.833 18.2664 12.7452 18.4227 12.5889C18.5789 12.4327 18.6667 12.2207 18.6667 11.9997C18.6667 11.7787 18.5789 11.5667 18.4227 11.4104C18.2664 11.2541 18.0544 11.1664 17.8334 11.1664Z" fill="#101223"></path>
          </svg></div>';

            echo '</div>';

            // JS samo ako ima slider
            ?>
            <script>
                document.addEventListener('DOMContentLoaded', function () {

                    const el = document.querySelector('.selected-products-swiper');

                    if (el) {

                        new Swiper(el, {
                            slidesPerView: 2,
                            spaceBetween: 24,

                            breakpoints: {
                                768: {
                                    slidesPerView: 2
                                }
                            },

                            navigation: {
                                nextEl: '.swiper-button-next',
                                prevEl: '.swiper-button-prev'
                            }
                        });

                    }

                });
            </script>
            <?php
        }

    endif;

    wp_reset_postdata();

    return ob_get_clean();
});