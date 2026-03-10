<?php
/**
 * HPOS Orders: filter po drzavi + Export delivery CSV dugme
 */

/** 1) Dropdown filter (HPOS wc-orders) */
add_action('woocommerce_order_list_table_restrict_manage_orders', function () {

    if (empty($_GET['page']) || $_GET['page'] !== 'wc-orders') return;

    $selected = isset($_GET['filter_country']) ? sanitize_text_field(wp_unslash($_GET['filter_country'])) : '';

    $countries = [
        ''   => __('Sve države', 'your-textdomain'),
        'RS' => 'Srbija',
        'MK' => 'Severna Makedonija',
        'BA' => 'Bosna i Hercegovina',
        'BG' => 'Bugarska',
        'ME' => 'Crna Gora',
    ];

    echo '<select name="filter_country" id="filter_country" style="max-width:220px;margin-left:6px;">';
    foreach ($countries as $code => $label) {
        printf(
            '<option value="%s"%s>%s</option>',
            esc_attr($code),
            selected($selected, $code, false),
            esc_html($label)
        );
    }
    echo '</select>';
});

/** 2) Primeni filter na HPOS query (billing_country) */
add_filter('woocommerce_order_list_table_prepare_items_query_args', function ($args) {

    if (empty($_GET['page']) || $_GET['page'] !== 'wc-orders') return $args;

    $country = isset($_GET['filter_country']) ? sanitize_text_field(wp_unslash($_GET['filter_country'])) : '';
    if (!$country) return $args;

    // ✅ HPOS-friendly (ne meta_query)
    $args['billing_country'] = $country;
    // Ako želiš shipping umesto billing:
    // $args['shipping_country'] = $country;

    return $args;
}, 10, 1);

/** 3) Dugme "Export delivery CSV" pored filtera */
add_action('woocommerce_order_list_table_restrict_manage_orders', function () {

    if (empty($_GET['page']) || $_GET['page'] !== 'wc-orders') return;
    if (!current_user_can('manage_woocommerce')) return;

    $url = add_query_arg(
        [
            'action' => 'export_delivery_csv',
            '_wpnonce' => wp_create_nonce('export_delivery_csv'),
            // prosledi postojeće filtere (status, search, date...) + naš filter_country
        ],
        admin_url('admin-post.php')
    );

    // Prosledi sve GET parametre sa wc-orders na admin-post (osim page)
    foreach ($_GET as $k => $v) {
        if ($k === 'page') continue;
        $url = add_query_arg($k, is_array($v) ? array_map('sanitize_text_field', $v) : sanitize_text_field(wp_unslash($v)), $url);
    }

    echo '<a class="button button-primary" style="margin-left:8px;" href="' . esc_url($url) . '">Export delivery CSV</a>';
}, 20);

/** 4) Handler koji generiše CSV */
add_action('admin_post_export_delivery_csv', function () {

    if (!current_user_can('manage_woocommerce')) {
        wp_die('No permission');
    }

    check_admin_referer('export_delivery_csv');

    // Sastavi WC_Order_Query args iz filtera (minimalno + naš country)
    $args = [
        'limit'   => -1,
        'orderby' => 'date',
        'order'   => 'DESC',
        'return'  => 'objects',
    ];

    // Status filter (Woo često šalje status kao "status" ili "status[]")
    if (isset($_GET['status'])) {
        $status = wp_unslash($_GET['status']);
        if (is_array($status)) {
            $args['status'] = array_map('sanitize_text_field', $status);
        } else {
            $args['status'] = sanitize_text_field($status);
        }
    }

    // Naš filter po državi
    $country = isset($_GET['filter_country']) ? sanitize_text_field(wp_unslash($_GET['filter_country'])) : '';
    if ($country) {
        $args['billing_country'] = $country; // ili shipping_country
    }

    // (Opcionalno) ako ima search (npr. order ID / customer)
    if (!empty($_GET['s'])) {
        $args['search'] = sanitize_text_field(wp_unslash($_GET['s']));
    }

    $orders = wc_get_orders($args);

    // CSV headers
    $filename = 'delivery-export-' . date('Y-m-d-His') . '.csv';
    nocache_headers();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $out = fopen('php://output', 'w');

    // UTF-8 BOM (da Excel lepo otvori)
    fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Kolone (prilagodi po potrebi)
    fputcsv($out, [
        'Order ID',
        'Date',
        'Name',
        'Phone',
        'Email',
        'Address 1',
        'Address 2',
        'City',
        'Postcode',
        'Country',
        'Shipping method',
        'Items',
        'Customer note',
        'Total',
        'Payment method',
    ]);

    foreach ($orders as $order) {
        /** @var WC_Order $order */

        $items = [];
        foreach ($order->get_items() as $item) {
            $qty = (int) $item->get_quantity();
            $name = $item->get_name();
            $items[] = $name . ' x' . $qty;
        }
        $items_str = implode(' | ', $items);

        // Delivery info: uglavnom shipping adresa; fallback na billing
        $ship_first = $order->get_shipping_first_name();
        $ship_last  = $order->get_shipping_last_name();
        $ship_addr1 = $order->get_shipping_address_1();
        $ship_city  = $order->get_shipping_city();

        $use_shipping = ($ship_first || $ship_last || $ship_addr1 || $ship_city);

        $name = $use_shipping
            ? trim($order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name())
            : trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name());

        $address1 = $use_shipping ? $order->get_shipping_address_1() : $order->get_billing_address_1();
        $address2 = $use_shipping ? $order->get_shipping_address_2() : $order->get_billing_address_2();
        $city     = $use_shipping ? $order->get_shipping_city() : $order->get_billing_city();
        $postcode = $use_shipping ? $order->get_shipping_postcode() : $order->get_billing_postcode();
        $country  = $use_shipping ? $order->get_shipping_country() : $order->get_billing_country();

        fputcsv($out, [
            $order->get_id(),
            $order->get_date_created() ? $order->get_date_created()->date('Y-m-d H:i:s') : '',
            $name,
            $order->get_billing_phone(),
            $order->get_billing_email(),
            $address1,
            $address2,
            $city,
            $postcode,
            $country,
            $order->get_shipping_method(),
            $items_str,
            $order->get_customer_note(),
            $order->get_total(),
            $order->get_payment_method_title(),
        ]);
    }

    fclose($out);
    exit;
});