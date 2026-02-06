<?php get_header(); ?>
<div class="container woocommerce-content">



    <?php if (is_product_category() || is_shop() || is_product_tag()): ?>
        <div class="shop-header">

        <?php
if ( is_product_category() ) {

    $term = get_queried_object();

    if ( $term && ! is_wp_error( $term ) ) {

        $thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );

        if ( $thumbnail_id ) {
            echo wp_get_attachment_image(
                $thumbnail_id,
                'full',
                false,
                [
                    'class' => 'product-category-image',
                    'alt'   => esc_attr( $term->name ),
                ]
            );
        }
    }
}
?>

            <?php
            if (is_product_category()) {
                $term = get_queried_object();
                echo '<h1 class="shop-title">' . esc_html($term->name) . '</h1>';
            } elseif (is_product_tag()) {
                $term = get_queried_object();
                echo '<h1 class="shop-title">' . esc_html($term->name) . '</h1>';
            } elseif (is_shop()) {
                echo '<h1 class="shop-title">Prodavnica</h1>';
            }
            ?>

            <div class="shop-header-meta">
                <?php
                if (!function_exists('woocommerce_result_count')) {
                    function woocommerce_result_count()
                    {
                        if (!wc_get_loop_prop('is_paginated') || !woocommerce_products_will_display()) {
                            return;
                        }
                        $args = array(
                            'total' => wc_get_loop_prop('total'),
                            'per_page' => wc_get_loop_prop('per_page'),
                            'current' => wc_get_loop_prop('current_page'),
                        );
                        wc_get_template('loop/result-count.php', $args);
                    }
                }

                // Place the result count where you want
                woocommerce_result_count(); ?>

            </div>
        </div>
        <div class="shop-container">
            <div class="listed-products">

                <?php woocommerce_content(); ?>
            </div>
        </div>


    <?php else: ?>
        <?php $args = array(
            'delimiter' => ' / ',
        );
        woocommerce_breadcrumb($args);
        ?>
        <?php woocommerce_content(); ?>
    <?php endif; ?>

</div>
<?php get_footer(); ?>