<?php

/*
Plugin Name: BFW Total Converter
Description: Convert the base amount to MYR for Billplz for WooCommerce
Version: 1.0.0
Author: wzul
Author URI: https://github.com/wzul/billplz-woo-convert-currency
License: GPLv2 or later
 */

class BillplzWooConvertCurrency
{

    const CURRENCY_CONVERSION_CHARGES = 1;

    public function __construct()
    {
        add_filter('bfw_filter_order_data', array($this, 'convert_total_value'));
        add_filter('bfw_supported_currencies', array($this, 'apply_base_currency'));
    }

    public function convert_total_value($order_data)
    {
        $this->get_current_conversion();
        $order_data['total'] = $order_data['total'] * $this->get_current_conversion() * self::CURRENCY_CONVERSION_CHARGES;
        return $order_data;
    }

    public function apply_base_currency($currency)
    {
        $currency[] = get_woocommerce_currency();
        return $currency;
    }

    public function get_current_conversion()
    {
        if (false === ($rates = get_transient('woo_amount_converter_for_billplz'))) {
            $app_id = 'e65018798d4a4585a8e2c41359cc7f3c';
            $base = get_woocommerce_currency();
            $rates = wp_remote_retrieve_body(wp_safe_remote_get("http://openexchangerates.org/api/latest.json?base=$base&symbols=MYR&app_id=$app_id"));
            $check_rates = json_decode($rates);

            // Check for error
            if (is_wp_error($rates) || !empty($check_rates->error) || empty($rates)) {

                if (401 == $check_rates->status) {
                    add_action('admin_notices', array($this, 'admin_notice_wrong_key'));
                }

            } else {
                set_transient('woo_amount_converter_for_billplz', $rates, HOUR_IN_SECONDS * 12);
            }
        }

        $rates = json_decode($rates);

        if ($rates && !empty($rates->base) && !empty($rates->rates)) {
            return $rates->rates->MYR;
        }
        return 4;
    }

    public function admin_notice_wrong_key()
    {
        ?>
            <div class="error">
                <p>WooCommerce amount converter for Billplz: Incorrect key!</p>
            </div>
        <?php
}

}

new BillplzWooConvertCurrency();
