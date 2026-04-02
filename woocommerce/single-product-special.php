<?php
defined('ABSPATH') || exit;

get_header('shop');

global $product;

$product_id = get_the_ID();
$action_tag = get_field('action_tag', $product_id);

$product_object = wc_get_product( $product_id );
$description = $product_object->get_description();

$price = 0;

// nađi jedan proizvod iz tog taga
$products = wc_get_products([
    'limit' => 1,
    'status' => 'publish',
    'tax_query' => [
        [
            'taxonomy' => 'product_tag',
            'field' => 'term_id',
            'terms' => $action_tag,
        ],
    ],
]);

if (!empty($products)) {
    $price = (float) $products[0]->get_price();
}

$old_total = $price * 3;
$new_total = $price * 2;
$savings = $old_total - $new_total;

$tag = get_term($action_tag, 'product_tag');
?>

<div class="container special-product-layout">

    <?php woocommerce_breadcrumb(); ?>

    <div class="special-header">
        <h1><?php //echo get_the_title();
        echo $tag->name; ?></h1>
        <h2 class="tag-subtitle"><b>Akcija 2+1 gratis</b></h2>
        <?php
        

        if (!is_wp_error($tag) && $tag !== null) {
            ?>
            <h2 class="tag-subtitle">Izaberi svoju kombinaciju i uštedi <b><?php echo wc_price($savings); ?></b></h2>
        <?php }//has tag ?>

        <div class="special-header-price">
            <span>Kupi 3 za samo </span>
            <del class="price-old">
                <?php echo wc_price($old_total); ?>
            </del>

            <span class="price-new">
                <?php echo wc_price($new_total); ?>
            </span>

        </div>

        <?php
        if (has_post_thumbnail($product_id)) {
            echo get_the_post_thumbnail($product_id, 'large');
        } ?>
    </div>

    <div class="special-product-grid">

        <!-- LEFT SIDE -->
        <div class="special-left">

            <form class="special-bundle-form">

                <?php
                $products = wc_get_products([
                    'limit' => -1,
                    'status' => 'publish',
                    'tax_query' => [
                        [
                            'taxonomy' => 'product_tag',
                            'field' => 'term_id',
                            'terms' => $action_tag,
                        ],
                    ],
                ]);
                ?>

                <div class="select-group">
                    <label>Izaberi ukus 1</label>
                    <select name="flavor_1" required>
                        <option value="" disabled selected hidden>Izaberite ukus...</option>
                        <?php foreach ($products as $p):

                            $image = wp_get_attachment_image_url($p->get_image_id(), 'medium');

                            $ukus = $p->get_attribute('ukusi'); // atribut ukusi
                        
                            ?>
                            <option value="<?php echo $p->get_id(); ?>" data-image="<?php echo esc_url($image); ?>"
                                data-name="<?php echo esc_attr($p->get_name()); ?>"
                                data-ukus="<?php echo esc_attr($ukus); ?>">
                                <?php echo $ukus; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="select-group">
                    <label>Izaberi ukus 2</label>
                    <select name="flavor_2" required>
                        <option value="" disabled selected hidden>Izaberite ukus...</option>
                        <?php foreach ($products as $p):

                            $image = wp_get_attachment_image_url($p->get_image_id(), 'medium');

                            $ukus = $p->get_attribute('ukusi'); // atribut ukusi
                        
                            ?>
                            <option value="<?php echo $p->get_id(); ?>" data-image="<?php echo esc_url($image); ?>"
                                data-name="<?php echo esc_attr($p->get_name()); ?>"
                                data-ukus="<?php echo esc_attr($ukus); ?>">
                                <?php echo $ukus; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="select-group">
                    <label>Izaberi ukus 3 <strong>(Besplatan)</strong></label>
                    <select name="flavor_3" required>
                        <option value="" disabled selected hidden>Izaberite ukus...</option>
                        <?php foreach ($products as $p):

                            $image = wp_get_attachment_image_url($p->get_image_id(), 'medium');

                            $ukus = $p->get_attribute('ukusi'); // atribut ukusi
                        
                            ?>
                            <option value="<?php echo $p->get_id(); ?>" data-image="<?php echo esc_url($image); ?>"
                                data-name="<?php echo esc_attr($p->get_name()); ?>"
                                data-ukus="<?php echo esc_attr($ukus); ?>">
                                <?php echo $ukus; ?>
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

        <!-- RIGHT SIDE -->
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

                <div class="bundle-slot free" data-slot="3">
                    <div class="bundle-placeholder">
                        Gratis
                    </div>
                </div>

            </div>



            <div class="bundle-note">
                <p>
                    <strong>Napomena u vezi akcije:</strong><br>

                    Redovna cena jednog proizvoda iznosi <?php echo wc_price($price); ?>.<br>

                    Redovna cena promotivnog paketa (3 komada) iznosi <?php echo wc_price($old_total); ?>.<br>

                    Tokom trajanja akcije paket 2+1 prodaje se po ceni od <?php echo wc_price($new_total); ?>.<br>

                    Na fiskalnom računu cena se raspodeljuje na tri proizvoda:
                    <?php echo wc_price($new_total); ?> ÷ 3 =
                    <?php echo wc_price($new_total / 3); ?> po komadu.
                </p>
            </div>

        </div>

    </div>

</div>


<div class="product-description-wrapper">
    <div class="product-description-content">
        <?php
        /**
         * Prikaži WooCommerce tabove
         */
        woocommerce_output_product_data_tabs();
        ?>
    </div>
</div>




<script>
    document.addEventListener('DOMContentLoaded', function () {

        function updateSlot(select, slotNumber) {

            const option = select.options[select.selectedIndex];

            const img = option.dataset.image;
            const name = option.dataset.name;
            const ukus = option.dataset.ukus;

            const slot = document.querySelector('.bundle-slot[data-slot="' + slotNumber + '"]');

            slot.innerHTML = `
            <div class="bundle-product">
                <img src="${img}" alt="">
                <h4>${ukus}</h4>
            </div>
        `;
        }

        document.querySelector('[name="flavor_1"]').addEventListener('change', function () {
            updateSlot(this, 1);
        });

        document.querySelector('[name="flavor_2"]').addEventListener('change', function () {
            updateSlot(this, 2);
        });

        document.querySelector('[name="flavor_3"]').addEventListener('change', function () {
            updateSlot(this, 3);
        });

    });
</script>

<?php get_footer('shop'); ?>