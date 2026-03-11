<?php


add_action('wp_ajax_add_bundle_to_cart', 'add_bundle_to_cart');
add_action('wp_ajax_nopriv_add_bundle_to_cart', 'add_bundle_to_cart');

function add_bundle_to_cart() {

    $ids = $_POST['products'];

    foreach ($ids as $key => $id) {

        if ($key == 2) {
            WC()->cart->add_to_cart($id, 1, 0, [], ['is_free_flavor' => true]);
        } else {
            WC()->cart->add_to_cart($id, 1);
        }
    }

    WC_AJAX::get_refreshed_fragments(); 
}

add_action('wp_footer', function() {
if (!is_product()) return;
?>

<script>
jQuery(function($){

    $('.special-bundle-form').on('submit', function(e){
        e.preventDefault();

        const products = [
            $('select[name="flavor_1"]').val(),
            $('select[name="flavor_2"]').val(),
            $('select[name="flavor_3"]').val(),
        ];

        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {
                action: 'add_bundle_to_cart',
                products: products
            },
            /*success: function(response){

                if(response.fragments){

                    $.each(response.fragments, function(key, value){
                        $(key).replaceWith(value);
                    });

                }

                $('.bundle-success').fadeIn();
            }*/
           success: function(response){

                $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);

                $('.bundle-success').fadeIn();
            }
        });

    });

});
</script>

<?php
});