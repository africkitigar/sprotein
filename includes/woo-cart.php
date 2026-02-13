<?php

add_filter( 'woocommerce_currency_symbol', 'change_rsd_currency_symbol', 10, 2 );

function change_rsd_currency_symbol( $currency_symbol, $currency ) {

    if ( $currency === 'RSD' ) {
        $currency_symbol = 'RSD';
    }

    return $currency_symbol;
}