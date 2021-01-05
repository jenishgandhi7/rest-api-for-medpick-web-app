<?php

/*====================================== Tax listing - Start ================================================ */

/*======================================  GET all taxes listing - Start ================================================ */

/**
 * GET all taxes listing
 *
 * Function Name: get_taxes_list
 *
 *
 */
function get_taxes_list()
{
    $all_tax_rates = [];
    $tax_classes = WC_Tax::get_tax_classes();

    if (!in_array('', $tax_classes)) {
        array_unshift($tax_classes, '');
    }

    foreach ($tax_classes as $tax_class) {
        $taxes = WC_Tax::get_rates_for_tax_class($tax_class);
        $all_tax_rates = array_merge($all_tax_rates, $taxes);
    }
    return $all_tax_rates;
}

function wl_taxes()
{
    if (validate_authorization_header()) {
        return get_taxes_list();
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/*====================================== GET all taxes by id - Start ================================================ */

/**
 * GET all taxes by id
 *
 * Function Name: wl_taxes_by_id
 *
 *
 */

function wl_taxes_by_id($request)
{
    if (validate_authorization_header()) {
        $tax_data = WC_Tax::_get_tax_rate($request['tax_id']);
        return $tax_data;
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

add_action('rest_api_init', function () {
    register_rest_route('medpick-api', 'taxes', [
        'methods' => 'GET',
        'callback' => 'wl_taxes'
    ]);

    register_rest_route('medpick-api', 'taxes/(?P<tax_id>\d+)', [
        'methods' => 'GET',
        'callback' => 'wl_taxes_by_id'
    ]);
});

/*====================================== Tax listing - End ================================================ */
