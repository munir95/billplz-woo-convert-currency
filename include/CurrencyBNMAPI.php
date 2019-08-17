<?php

class BillplzBNMAPI
{
    const URL = 'https://api.bnm.gov.my/public/exchange-rate';

    public function getRates()
    {
        // BNM don't accept User-Agent. Thus, it must be set to null.
        $header = array(
            'Accept' => 'application/vnd.BNM.API.v1+json',
            'User-Agent' => null,
        );

        if (false === ($json_rates = get_transient('woo_amount_converter_for_billplz'))) {
            $base = get_woocommerce_currency();
            $query = http_build_query($this->getQueryParams());
            $rates = wp_remote_retrieve_body(wp_safe_remote_get(self::URL . "?{$query}", array(
                'headers' => $header,
            )));

            $array_return = json_decode($rates, true);

            foreach ($array_return['data'] as $value) {
                if (in_array($base, $value)) {
                    $display = $value;
                    break;
                }
            }

            $check_rates = array(
                'base' => $display['currency_code'],
                'rates' => array(
                    'MYR' => $display['rate']['selling_rate'] / $display['unit'],
                ),
            );

            $json_rates = json_encode($check_rates);

            // Check for error
            if (is_wp_error($rates) || empty($rates)) {
                // Do nothing
            } else {
                set_transient('woo_amount_converter_for_billplz', $json_rates, HOUR_IN_SECONDS * 12);
            }
        }
        return $json_rates;
    }

    private function getQueryParams()
    {
        return array(
            'session' => "0900",
        );
    }
}
