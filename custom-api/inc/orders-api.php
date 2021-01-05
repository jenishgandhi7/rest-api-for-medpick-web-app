<?php
/*====================================== Order API - Start ================================================ */
/**
 * Get Orders listing
 *
 * Function Name: get_orders_assoc
 *
 *
 */

function get_orders_assoc($order)
{
    $order_data = $order->get_data();

    foreach ($order->get_items() as $item_key => $item_values)
    {
        $order_items['product_id'] = $item_values->get_product_id();
    }
    $data = $order_data + $order_items;

    return $data;
}

/*====================================== Order API by id - Start ================================================ */
/**
 * Get Orders listing by id
 *
 * Function Name: wl_order_by_id
 *
 *
 */
function wl_order_by_id($request)
{

    if (validate_authorization_header())
    {

        $order = wc_get_order($request['order_id']);
        $data = get_orders_assoc($order);

        return $data;

    }
    else
    {
        return ['success' => false, 'message' => 'Authorization failed.', ];
    }
}

/*====================================== Order API by id - End ================================================ */

/*====================================== Order API for User - Start ================================================ */
/**
 * Get Orders of User
 *
 * Function Name: wl_user_orders
 *
 *
 */
function wl_user_orders($request)
{

    if (validate_authorization_header())
    {

        $customer_orders = get_posts(array(
            'meta_key' => '_customer_user',
            'meta_value' => $request['user_id'],
            'post_type' => 'shop_order',
            'post_status' => array_keys(wc_get_order_statuses()) ,
            'numberposts' => - 1
        ));

        $data = array_map(function ($order)
        {
            return $order->ID;
        }
        , $customer_orders);
        return $data;

    }
    else
    {
        return ['success' => false, 'message' => 'Authorization failed.', ];
    }
}

/*====================================== Order API for User - End ================================================ */
/*====================================== Order API by vendor  - Start ================================================ */
/**
 * Get Orders listing of vendors
 *
 * Function Name: wl_vendor_orders
 *
 *
 */
function wl_vendor_orders($request)
{
    if (validate_authorization_header())
    {

        $vendor_orders = get_posts(array(
            'post_type' => 'shop_order',
            'author' => $request['vendor_id'],
            'post_status' => array_keys(wc_get_order_statuses()) ,
            'numberposts' => - 1
        ));

        $data = array_map(function ($order)
        {
            return $order->ID;
        }
        , $vendor_orders);
        return $data;

    }
    else
    {
        return ['success' => false, 'message' => 'Authorization failed.', ];
    }
}
/*====================================== Order API by vendor- End ================================================ */

add_action('rest_api_init', function ()
{
    register_rest_route('medpick-api', 'orders/(?P<order_id>\d+)', ['methods' => 'GET', 'callback' => 'wl_order_by_id', ]);

    register_rest_route('medpick-api', 'users/(?P<user_id>\d+)/orders', ['methods' => 'GET', 'callback' => 'wl_user_orders', ]);

    register_rest_route('medpick-api', 'vendors/(?P<vendor_id>\d+)/orders', ['methods' => 'GET', 'callback' => 'wl_vendor_orders', ]);
});
/*====================================== Order API - End ================================================ */

