<?php

class BillplzOpenExchangeRate
{

    const URL = 'http://openexchangerates.org/api/latest.json';

    public function __construct($app_id)
    {
        $this->app_id = $app_id;
    }

    public function getRates()
    {
        if (false === ($rates = get_transient('woo_amount_converter_for_billplz'))) {
            $base = get_woocommerce_currency();
            $query = http_build_query($this->getQueryParams($base));
            $rates = wp_remote_retrieve_body(wp_safe_remote_get(self::URL . "?{$query}"));
            $check_rates = json_decode($rates);

            // Check for error
            if (is_wp_error($rates) || !empty($check_rates->error) || empty($rates)) {
                // Do nothing
            } else {
                set_transient('woo_amount_converter_for_billplz', $rates, HOUR_IN_SECONDS * 12);
            }
        }
        return $rates;
    }

    private function getQueryParams($base = "USD")
    {
        return array(
            'base' => $base,
            'symbols' => 'MYR',
            'app_id' => $this->app_id,
        );
    }
}
