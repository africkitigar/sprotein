<?php
defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

global $product;

$product_id = get_the_ID();
$action_tag = get_field( 'action_tag', $product_id );

?>

<div class="container special-product-layout">

    <?php woocommerce_breadcrumb(); ?>

    <div class="special-product-grid">

        <!-- LEFT SIDE -->
        <div class="special-left">

            <h3>Izaberi ukus</h3>

            <?php
            $args = [
                'post_type' => 'product',
                'posts_per_page' => -1,
                'tax_query' => [
                    [
                        'taxonomy' => 'product_tag',
                        'field'    => 'term_id',
                        'terms'    => $action_tag,
                    ],
                ],
            ];

            $flavors = new WP_Query($args);

            if ( $flavors->have_posts() ) :
                while ( $flavors->have_posts() ) : $flavors->the_post();
                    global $product;
                    ?>

                    <a href="<?php the_permalink(); ?>" class="special-related-item">

                        <div class="related-thumb">
                            <?php echo $product->get_image( 'thumbnail' ); ?>
                        </div>

                        <div class="related-info">
                            <h4><?php the_title(); ?></h4>
                            <span class="price"><?php echo $product->get_price_html(); ?></span>
                        </div>

                    </a>

                <?php endwhile;
                wp_reset_postdata();
            endif;
            ?>

        </div>

        <!-- RIGHT SIDE -->
        <div class="special-right">

            <form class="special-bundle-form">

                <?php
                $products = wc_get_products([
                    'limit' => -1,
                    'status' => 'publish',
                    'tax_query' => [
                        [
                            'taxonomy' => 'product_tag',
                            'field'    => 'term_id',
                            'terms'    => $action_tag,
                        ],
                    ],
                ]);
                ?>

                <div class="select-group">
                    <label>Izaberi ukus 1</label>
                    <select name="flavor_1" required>
                        <?php foreach ($products as $p) : ?>
                            <option value="<?php echo $p->get_id(); ?>">
                                <?php echo $p->get_name(); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="select-group">
                    <label>Izaberi ukus 2</label>
                    <select name="flavor_2" required>
                        <?php foreach ($products as $p) : ?>
                            <option value="<?php echo $p->get_id(); ?>">
                                <?php echo $p->get_name(); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="select-group">
                    <label>Izaberi ukus 3 <strong>(Besplatan)</strong></label>
                    <select name="flavor_3" required>
                        <?php foreach ($products as $p) : ?>
                            <option value="<?php echo $p->get_id(); ?>">
                                <?php echo $p->get_name(); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="button alt">
                    Dodaj u korpu
                </button>

                <div class="bundle-success" style="display:none;">
                    Proizvodi su dodati u korpu!
                </div>

            </form>

        </div>

    </div>

</div>

<?php get_footer( 'shop' ); ?>