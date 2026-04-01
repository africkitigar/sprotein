<?php
defined('ABSPATH') || exit;

get_header('shop');

global $product;

$product_id = get_the_ID();

// ACF tagovi
$ukus1_tag = get_field('ukus1', $product_id);
$ukus2_tag = get_field('ukus2', $product_id);

// uzmi proizvode iz ta dva taga
$products_1 = wc_get_products([
    'limit' => -1,
    'status' => 'publish',
    'tax_query' => [
        [
            'taxonomy' => 'product_tag',
            'field' => 'term_id',
            'terms' => $ukus1_tag,
        ],
    ],
]);

$products_2 = wc_get_products([
    'limit' => -1,
    'status' => 'publish',
    'tax_query' => [
        [
            'taxonomy' => 'product_tag',
            'field' => 'term_id',
            'terms' => $ukus2_tag,
        ],
    ],
]);

/*
$price1 = !empty($products_1) ? (float)$products_1[0]->get_price() : 0;
$price2 = !empty($products_2) ? (float)$products_2[0]->get_price() : 0;

$total_price = $price1 + $price2;*/

// regularna cena
$regular_price = (float) $product->get_regular_price();

// sale cena (ako postoji)
$sale_price = $product->get_sale_price();

if ($sale_price) {
    $price_html = '
        <del class="price-old">' . wc_price($regular_price) . '</del>
        <span class="price-new">' . wc_price($sale_price) . '</span>';
} else {
    $price_html = '
        <span class="price-new">' . wc_price($regular_price) . '</span>';
}
?>

<div class="container special-product-layout special-combo">

    <?php woocommerce_breadcrumb(); ?>

    <div class="special-header">
        <h1><?php the_title(); ?></h1>

        <div class="special-header-price">
            <span>Akcijska kombo cena: </span>
            <span class="price-new-wrap">
                 <?php echo $price_html; ?>
            </span>
        </div>

        <?php
        if (has_post_thumbnail($product_id)) {
            echo get_the_post_thumbnail($product_id, 'large');
        }
        ?>
    </div>

    <div class="special-product-grid">

        <!-- LEFT -->
        <div class="special-left">




            <form class="cart special-bundle-form combo-form" method="post" enctype="multipart/form-data">

    <input type="hidden" name="add-to-cart" value="<?php echo $product_id; ?>">

    <!-- UKUS 1 -->
    <div class="select-group">
        <label>Izaberi proizvod 1</label>
        <select name="flavor_1" required>
            <option value="" disabled selected hidden>Izaberite ukus...</option>

            <?php foreach ($products_1 as $p):
                $image = wp_get_attachment_image_url($p->get_image_id(), 'medium');
                $ukus = $p->get_attribute('ukusi');
            ?>
                <option value="<?php echo $p->get_id(); ?>"
                    data-image="<?php echo esc_url($image); ?>"
                    data-ukus="<?php echo esc_attr($ukus); ?>">
                    <?php echo $ukus; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- UKUS 2 -->
    <div class="select-group">
        <label>Izaberi proizvod 2</label>
        <select name="flavor_2" required>
            <option value="" disabled selected hidden>Izaberite ukus...</option>

            <?php foreach ($products_2 as $p):
                $image = wp_get_attachment_image_url($p->get_image_id(), 'medium');
                $ukus = $p->get_attribute('ukusi');
            ?>
                <option value="<?php echo $p->get_id(); ?>"
                    data-image="<?php echo esc_url($image); ?>"
                    data-ukus="<?php echo esc_attr($ukus); ?>">
                    <?php echo $ukus; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="single_add_to_cart_button button alt">
        Dodaj u korpu
    </button>

</form>

        </div>

        <!-- RIGHT -->
        <div class="special-right">

            <div class="bundle-preview">

                <div class="bundle-slot" data-slot="1">
                    <div class="bundle-placeholder">
                        Izaberi ukus
                    </div>
                </div>

                <div class="bundle-slot" data-slot="2">
                    <div class="bundle-placeholder">
                        Izaberi ukus
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

<!-- TABOVI -->
<div class="product-description-wrapper">
    <div class="product-description-content">
        <?php woocommerce_output_product_data_tabs(); ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    function updateSlot(select, slotNumber) {

        const option = select.options[select.selectedIndex];

        const img = option.dataset.image;
        const ukus = option.dataset.ukus;

        const slot = document.querySelector('.bundle-slot[data-slot="' + slotNumber + '"]');

        slot.innerHTML = `
            <div class="bundle-product">
                <img src="${img}" alt="">
                <h4>${ukus}</h4>
            </div>
        `;
    }

    const flavor1 = document.querySelector('[name="flavor_1"]');
    const flavor2 = document.querySelector('[name="flavor_2"]');

    flavor1.addEventListener('change', function () {
        updateSlot(this, 1);

        // blokiraj isti izbor u drugom selectu
        const val = this.value;

        document.querySelectorAll('[name="flavor_2"] option').forEach(opt => {
            opt.disabled = (opt.value === val);
        });
    });

    flavor2.addEventListener('change', function () {
        updateSlot(this, 2);
    });

});
</script>

<?php get_footer('shop'); ?>