# Billplz for WooCommerce Convert Currency

This plugin will help you to convert your home currency value to MYR on the fly.

## Description

If you are using WooCommerce and stuck with a currency other than MYR. Probably you are not able to change your home currency to MYR just because of Billplz. 

Setting home currency to USD + integrating using Billplz will result in the amount to be paid inaccurately. E.g.: $100 total amount, but Billplz charge RM100.

## Changing of Base Currency

Changing Base Currency on WooCommerce will require 12 hour to refresh due to transient timeout time.

## Plugin behavior

This plugin will do 4 things:

1. Add base currency to Billplz for WooCommerce accepted currency.
2. Get latest conversion rate based on base currency.
3. Convert total amount to MYR before creating a bill.
4. Auto fallback to default conversion rate if failed.
