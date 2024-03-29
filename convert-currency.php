<?php

/*
Plugin Name: BFW Total Converter
Description: Convert the base amount to MYR for Billplz for WooCommerce
Version: 1.1.0
Author: wzul
Author URI: https://github.com/wzul/billplz-woo-convert-currency
License: MIT License
 */

class BillplzWooConvertCurrency
{
    /**
     * 1: OpenExchangeRates.org
     * 2: BNM API
     */
    const CURRENCY_PROVIDER = 2;

    const APP_ID = 'e65018798d4a4585a8e2c41359cc7f3c';
    const DEFAULT_CONVERSION_RATE = 4;
    const CURRENCY_CONVERSION_CHARGES = 1;

    public function __construct()
    {
        add_filter('bfw_filter_order_data', array($this, 'convert_total_value'));
        add_filter('bfw_supported_currencies', array($this, 'apply_base_currency'));
    }

    public function convert_total_value($order_data)
    {
        $conversion_rate = $this->get_current_conversion(self::DEFAULT_CONVERSION_RATE);
        $order_data['total'] = $order_data['total'] * $conversion_rate * self::CURRENCY_CONVERSION_CHARGES;
        return $order_data;
    }

    public function apply_base_currency($currency)
    {
        $currency[] = get_woocommerce_currency();
        return $currency;
    }

    private function get_currency_provider()
    {
        switch (self::CURRENCY_CONVERSION_CHARGES) {
            case 1:
                include 'include/CurrencyOpenExchangeRate.php';
                return new BillplzOpenExchangeRate(self::APP_ID);
            case 2:
                include 'include/CurrencyBNMAPI.php';
                return new BillplzBNMAPI();
            default:
                include 'include/CurrencyBNMAPI.php';
                return new BillplzBNMAPI();
        }
    }

    public function get_current_conversion($default_conversion_rate)
    {

        $currency_provider = $this->get_currency_provider();
        $rates = $currency_provider->getRates();

        $rates = json_decode($rates);

        if ($rates && !empty($rates->base) && !empty($rates->rates)) {
            return $rates->rates->MYR;
        }
        return $default_conversion_rate;
    }

}

new BillplzWooConvertCurrency();
