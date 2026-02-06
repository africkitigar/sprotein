<?php
$message = sp_get_protein_promo_message();

if (!$message) {
  return;
}

$allowed = [
  'svg' => [
    'class' => true,
    'width' => true,
    'height' => true,
    'viewBox' => true,
    'fill' => true,
    'xmlns' => true,
  ],
  'path' => [
    'd' => true,
    'stroke' => true,
    'stroke-width' => true,
    'stroke-linecap' => true,
    'stroke-linejoin' => true,
    'fill' => true,
  ],
  'strong' => [],
  'span' => ['class' => true],
];
?>

<div class="header-promo header-promo--protein promos">
  <div class="header-promo__inner">
    <?php echo wp_kses($message, $allowed); ?>
  </div>
</div>
