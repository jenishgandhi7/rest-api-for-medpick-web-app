<?php
/*====================================== Checkout API - Start ================================================ */
/**
 * Checkout the order using Checkout api
 *
 * Function Name: checkout
 *
 *
 */

function checkout(WP_REST_Request $request)
{
    if (validate_authorization_header()) {
        $arr_request = json_decode($request->get_body(), true);
        $email = $arr_request['email'];

        $userid = $arr_request['userid'];
        if (empty($userid)) {
            return "please enter an user id";
        }
        global $wpdb;
        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $wpdb->users WHERE ID = %d ",
                $userid
            )
        );
        if ($count == 0) {
            return array(
                "success" => "false",
                "message" =>
                    "invalid user id，please input correct user id and try it again"
            );
        }
        $products = $arr_request['products'];

        $b_first_name = $arr_request['billing_first_name'];
        if (empty($b_first_name)) {
            return "please enter billing first name";
        }
        $b_last_name = $arr_request['billing_last_name'];

        if (empty($b_last_name)) {
            return "please enter billing last name";
        }
        $b_address1 = $arr_request['billing_street_address'];
        if (empty($b_address1)) {
            return "please enter billing street address";
        }
        $b_city = $arr_request['billing_city'];
        if (empty($b_city)) {
            return "please enter billing city";
        }

        $b_state = $arr_request['billing_state'];
        if (empty($b_state)) {
            return "please enter billing state";
        }

        $b_postcode = $arr_request['billing_postcode'];
        if (empty($b_postcode)) {
            return "please enter billing postcode";
        }
        $b_country = $arr_request['billing_country'];
        if (empty($b_country)) {
            return "please enter billing country";
        }

        $s_first_name = $arr_request['shipping_first_name'];
        if (empty($s_first_name)) {
            return "please enter shipping first name";
        }
        $s_last_name = $arr_request['shipping_last_name'];

        if (empty($s_last_name)) {
            return "please enter shipping last name";
        }
        $s_address1 = $arr_request['shipping_street_address'];
        if (empty($s_address1)) {
            return "please enter shipping street address";
        }
        $s_city = $arr_request['shipping_city'];
        if (empty($s_city)) {
            return "please enter shipping city";
        }

        $s_state = $arr_request['shipping_state'];
        if (empty($s_state)) {
            return "please enter shipping state";
        }

        $s_postcode = $arr_request['shipping_postcode'];
        if (empty($s_postcode)) {
            return "please enter shipping postcode";
        }
        $s_country = $arr_request['shipping_country'];
        if (empty($s_country)) {
            return "please enter shipping country";
        }

        $billing_address = array(
            'first_name' => $arr_request['billing_first_name'],
            'last_name' => $arr_request['billing_last_name'],
            'company' => $arr_request['billing_company'],
            'email' => $email,
            'phone' => $arr_request['billing_phone'],
            'address_1' => $arr_request['billing_street_address'],
            'address_2' => $arr_request['billing_address_2'],
            'city' => $arr_request['billing_city'],
            'state' => $arr_request['billing_state'],
            'postcode' => $arr_request['billing_postcode'],
            'country' => $arr_request['billing_country']
        );

        $shipping_address = array(
            'first_name' => $arr_request['shipping_first_name'],
            'last_name' => $arr_request['shipping_last_name'],
            'company' => $arr_request['shipping_company'],
            'email' => $email,
            'phone' => $arr_request['shipping_phone'],
            'address_1' => $arr_request['shipping_street_address'],
            'address_2' => $arr_request['shipping_address_2'],
            'city' => $arr_request['shipping_city'],
            'state' => $arr_request['shipping_state'],
            'postcode' => $arr_request['shipping_postcode'],
            'country' => $arr_request['shipping_country']
        );

        $order = wc_create_order(array(
            'customer_id' => $userid
        ));
        foreach ($products as $product) {
            $order->add_product(
                get_product($product['productid']),
                $product['quantity']
            );
        }
        $order->set_address($billing_address, 'billing');
        $order->set_address($shipping_address, 'shipping');
        $order_id = trim(str_replace('#', '', $order->get_order_number()));
        $discount_code = $arr_request['coupon'];
        if (!empty($discount_code)) {
            $order->apply_coupon($discount_code);
        }
        $order->calculate_totals();
        add_post_meta(
            $order_id,
            '_payment_method',
            $arr_request['payment_method']
        );
        if ($order->get_total() != 0 && $order->user_id != null) {
	 $response["success"] = 1;
        $response["userid"] = $userid ;
        $response["message"] = "Checkout Order successfully made.. Your oder id is  $order_id ";
		$json_terms = json_encode($response);
            echo $json_terms  ;
exit();
        } else {
            return "error happen";
        }
    } else {
        return ['success' => false, 'message' => 'Authorization failed.'];
    }
}
/*====================================== Checkout API - END ================================================ */

/*====================================== CART API - Start ================================================ */
/**
 *  Create Cart and gernate total with tax and shipping using Cart api
 *
 * Function Name: addCart
 *
 *
 */

function addCart(WP_REST_Request $request)
{
    if (validate_authorization_header()) {
        $arr_request = json_decode($request->get_body(), true);
        $data = [];
        $products = $arr_request['products'];
        if (is_null(WC()->cart)) {
            wc_load_cart();
        }
        //WC()->cart->empty_cart();
        WC()->cart->get_cart();
        foreach ($products as $product) {
            WC()->cart->add_to_cart(
                $product['productid'],
                $product['quantity']
            );
        }

        $carts = WC()->cart->get_cart();
        $i = 0;
        $total = 0;
        $total_tax = 0;

        foreach ($carts as $cart) {
            $data['product' . $i]['product_id'] = $cart['product_id'];
            $data['product' . $i]['price'] = get_post_meta(
                $cart['product_id'],
                '_price',
                true
            );
            $data['product' . $i]['quantity'] = $cart['quantity'];

            $total += $cart['line_total'] + $cart['line_tax'];
            $total_tax += $cart['line_tax'];
            $i++;
        }
        $data['total_tax'] = $total_tax;
        $data['total_with_tax'] = $total;

        $shipping_packages = WC()->cart->get_shipping_packages();

        // Get the WC_Shipping_Zones instance object for the first package
        $shipping_zone = wc_get_shipping_zone(reset($shipping_packages));

        $zone_id = $shipping_zone->get_id(); // Get the zone ID

        $shipping_zone = new WC_Shipping_Zone($zone_id);

        // Get all shipping method values for the shipping zone
        $shipping_methods = $shipping_zone->get_shipping_methods(
            true,
            'values'
        );

        $zone_method = $shipping_zone->get_shipping_methods();
        $zone_data = $shipping_zone->get_id();

        foreach ($shipping_methods as $method) {
            $data['shipping_method'] = $method->id;
            $data['shipping_cost'] = $method->cost;
            $data['total_amount'] = $total + $method->cost;
        }

        return $data;
    } else {
        return ['success' => false, 'message' => 'Authorization failed.'];
    }
}

add_action('rest_api_init', function () {
    register_rest_route('medpick-api', 'checkout', [
        'methods' => 'POST',
        'callback' => 'checkout'
    ]);

    register_rest_route('medpick-api', 'addcart', [
        'methods' => 'POST',
        'callback' => 'addCart'
    ]);
});
/*====================================== Cart API - End ================================================ */
