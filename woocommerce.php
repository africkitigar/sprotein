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
            $search = get_search_query();

            if (is_product_category()) {
                $term = get_queried_object();
                echo '<h1 class="shop-title">' . esc_html($term->name) . '</h1>';
            } elseif (is_product_tag()) {
                $term = get_queried_object();
                echo '<h1 class="shop-title">' . esc_html($term->name) . '</h1>';
            } elseif (is_shop() & !$search) {
                echo '<h1 class="shop-title">Prodavnica</h1>';
            } 
            
            ?>

            <h1 class="shop-title">
            <?php

            

            if ($search) {
                echo 'Rezultati pretrage za: ' . esc_html($search);
            } 

            ?>
            </h1>

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

                <div class="mobile-grid-toggle">
                    <button class="grid-btn active" data-grid="1">
                        
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="3" width="18" height="7"></rect>
                            <rect x="3" y="14" width="18" height="7"></rect>
                        </svg>
                    </button>

                    <button class="grid-btn" data-grid="2">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                        </svg>
                    </button>
                </div>

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

<script>
document.addEventListener('DOMContentLoaded', function(){

    const buttons = document.querySelectorAll('.grid-btn');
    const products = document.querySelector('.listed-products');

    if(!products) return;

    // 🔹 1. učitaj prethodni izbor
    const saved = localStorage.getItem('productGrid');

    if(saved === "2"){
        products.classList.add('grid-2');
        document.querySelector('[data-grid="2"]')?.classList.add('active');
        document.querySelector('[data-grid="1"]')?.classList.remove('active');
    }

    // 🔹 2. klik na dugme
    buttons.forEach(btn => {

        btn.addEventListener('click', function(){

            buttons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const grid = this.dataset.grid;

            if(grid === "2"){
                products.classList.add('grid-2');
            }else{
                products.classList.remove('grid-2');
            }

            // 🔹 sačuvaj izbor
            localStorage.setItem('productGrid', grid);

        });

    });

});   
</script>