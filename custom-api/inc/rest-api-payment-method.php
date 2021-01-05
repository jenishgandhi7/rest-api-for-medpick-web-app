<?php
/*====================================== Payment Methods API - Start ================================================ */
/**
 * Get Payment Method used for Checkout
 *
 * Function Name: get_payment_methods
 *
 *
 */

function get_payment_methods()
{
    if (validate_authorization_header()) {
        $gateways = WC()->payment_gateways->get_available_payment_gateways();
        $enabled_gateways = [];

        if ($gateways) {
            foreach ($gateways as $gateway) {
                if ($gateway->enabled == 'yes') {
                    $enabled_gateways[] = $gateway;
                }
            }
        }

        $data = [];
        $i = 0;
        foreach ($enabled_gateways as $enabled_gateway) {
            $data[$i]['method_name'] = $enabled_gateway->title;
            $data[$i]['description'] = $enabled_gateway->description;
            $data[$i]['method_description'] =
                $enabled_gateway->method_description;
            $data[$i]['instructions'] = $enabled_gateway->instructionss;
            $data[$i]['supports'] = $enabled_gateway->supports;
            $i++;
        }
        return $data;
    } else {
        return ['success' => false, 'message' => 'Authorization failed.'];
    }
}

add_action('rest_api_init', function () {
    register_rest_route('medpick-api', 'payment', [
        'methods' => 'GET',
        'callback' => 'get_payment_methods'
    ]);
});
/*====================================== Payment Methods API - End ================================================ */
