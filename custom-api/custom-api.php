<?php
/**
 * Plugin Name: Custom API for Madpick
 * Plugin URI: Http://Madpick.in/
 * Description: REST-API for Mobile aaplication
 * Version: 1.0
 * Requires at least: 5.2
 * Author: UMASS Boston
 * License: GPL2 or later
 */

define('MP_DIR_PATH', plugin_dir_path(__FILE__));

/*====================================== Plugin Authorization - Start ================================================ */
/**
 *  Validate Authorization header for an API calls.
 *
 * Function Name: customrestapi_deactivate
 *
 *
 */
function validate_authorization_header()
{
    $headers = apache_request_headers();
    $options = get_option('custom_plugin_options');
    $consumer_key = $options['consumer_key'];
    $consumer_secret = $options['consumer_secret'];

    if (empty($headers['Authorization'])) {
        if (empty($consumer_key) && empty($consumer_secret)) {
            return true;
        }
    } else {
        if (isset($headers['Authorization'])) {
            if (!empty($consumer_key) && !empty($consumer_secret)) {
                $wc_header =
                    'Basic ' .
                    base64_encode(
                        esc_attr($consumer_key) .
                            ':' .
                            esc_attr($consumer_secret)
                    );
                if ($headers['Authorization'] == $wc_header) {
                    return true;
                }
            }
        }
    }
    return false;
}
/*====================================== Plugin Authorization - End ================================================ */

/*====================================== Plugin Activate - Start ================================================ */
/**
 *  Deactivate Plugin : When deactivate plugin then meta value deleted in database.
 *
 * Function Name: customrestapi_deactivate
 *
 *
 */
register_activation_hook(__FILE__, 'customrestapi_activate');
function customrestapi_activate()
{
    delete_option('custom_add_settings_page');
}

/*====================================== Plugin Activate - End ================================================ */

/*====================================== Plugin deactivate - Start ================================================ */
/**
 *  Deactivate Plugin : When deactivate plugin then meta value deleted in database.
 *
 * Function Name: customrestapi_deactivate
 *
 *
 */
register_deactivation_hook(__FILE__, 'customrestapi_deactivate');
function customrestapi_deactivate()
{
    delete_option('custom_add_settings_page');
}
/*====================================== Plugin deactivate - End ================================================ */

/**
 * insert file name of api's
 *
 * @return
 */

require_once MP_DIR_PATH . 'admin/settings.php';

require_once MP_DIR_PATH . 'inc/rest-api-post.php';

require_once MP_DIR_PATH . 'inc/rest-api-product.php';

require_once MP_DIR_PATH . 'inc/rest-api-post-categories.php';

require_once MP_DIR_PATH . 'inc/rest-api-product-categories.php';

require_once MP_DIR_PATH . 'inc/rest-api-page.php';

require_once MP_DIR_PATH . 'inc/users-list-api.php';

require_once MP_DIR_PATH . 'inc/vendors-list-api.php';

require_once MP_DIR_PATH . 'inc/rest-api-login-regsiter.php';

require_once MP_DIR_PATH . 'inc/checkout-api.php';

require_once MP_DIR_PATH . 'inc/rest-api-payment-method.php';

require_once MP_DIR_PATH . 'inc/orders-api.php';

require_once MP_DIR_PATH . 'inc/checkout-api.php';

require_once MP_DIR_PATH . 'inc/rest-api-payment-method.php';

require_once MP_DIR_PATH . 'inc/rest-api-login-regsiter.php';

require_once MP_DIR_PATH . 'inc/tax-rates-api.php';

require_once MP_DIR_PATH . 'inc/add_page.php';
require_once MP_DIR_PATH . 'inc/add_product.php';
