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











function sp_get_protein_promo_message() {

  if (!WC()->cart) {
    return '';
  }

  // tagovi akcija
  $promo_tags = [
    30 => 'Ultra Whey proteina',
    31 => 'Strong Whey proteina',
    75 => 'Kolagena'
  ];

  $tag_counts = [
    30 => 0,
    31 => 0,
    75 => 0
  ];

  // broji proizvode po tagu u korpi
  foreach (WC()->cart->get_cart() as $cart_item) {

    $product_id = $cart_item['product_id'];
    $qty = (int) $cart_item['quantity'];

    foreach ($promo_tags as $tag_id => $label) {

      if (has_term($tag_id, 'product_tag', $product_id)) {
        $tag_counts[$tag_id] += $qty;
      }

    }
  }

  // SUCCESS stanje (ako bilo koji tag ima 3+)
  foreach ($tag_counts as $tag_id => $count) {

    if ($count >= 3) {
      return '
      <svg class="promo-icon promo-icon--check" width="20" height="20" viewBox="0 0 24 24" fill="none">
        <path d="M5 13l4 4L19 7"
              stroke="currentColor" stroke-width="1.8"
              stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <strong>Čestitamo!</strong> Ostvario si <strong>2+1 GRATIS</strong> akciju!
      ';
    }

  }

  // product page logika
  if (is_product()) {

    $product_id = get_queried_object_id();

    foreach ($promo_tags as $tag_id => $label) {

      if (has_term($tag_id, 'product_tag', $product_id)) {

        $count = $tag_counts[$tag_id];

        if ($count === 0) {
          return "💥 Poruči <strong>2 Olympic  {$label}</strong> i <strong>treći dobijaš GRATIS</strong>!";
        }

        if ($count === 1) {
          return "+ Poruči još <strong>2 Olympic  {$label}</strong> i ostvari <strong>2+1 GRATIS</strong>!";
        }

        if ($count === 2) {
          return "🔥 Još <strong>1 {$label}</strong> te deli od <strong>BESPLATNOG trećeg</strong>!";
        }

      }

    }

  }

  // random marketing poruke
  $messages = [
    'Preko 100.000 zadovoljnih korisnika',
    'Rok isporuke 2 radna dana na teritoriji Republike Srbije',
    'Besplatna dostava na teritoriji Republike Srbije za porudžbine preko 3500 RSD'
  ];

  return $messages[array_rand($messages)];
}