<?php
defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product || ! $product->is_visible() ) {
    return;
}

$product_id  = $product->get_id();
$permalink   = get_permalink( $product_id );
$title       = get_the_title( $product_id );
$short_desc  = $product->get_short_description();
$image_url   = get_the_post_thumbnail_url( $product_id, 'large' );
?>

<li <?php wc_product_class( 'product product-special-banner', $product ); ?>>

    <a href="<?php echo esc_url( $permalink ); ?>" class="special-banner-link">

        <div class="special-banner-inner"
             style="background-image: url('<?php echo esc_url( $image_url ); ?>');">

            <div class="special-banner-overlay"></div>

            <div class="special-banner-content">

                <h2 class="special-banner-title">
                    <?php 
                        $title = explode('–', $title)[0];
                        echo esc_html(trim($title));
                    ?>
                    <span>Akcija 2+1 gratis</span>
                </h2>

                <?php if ( $short_desc ) : ?>
                    <div class="special-banner-description">
                        <?php echo wp_kses_post( wpautop( $short_desc ) ); ?>
                    </div>
                <?php endif; ?>

                <span class="special-banner-cta btn">
                    <?php _e( 'Iskoristi akciju', 'woocommerce' ); ?>
                </span>

            </div>

        </div>

    </a>

</li>