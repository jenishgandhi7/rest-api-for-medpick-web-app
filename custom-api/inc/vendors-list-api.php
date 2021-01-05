<?php

/*====================================== Vendor listing - Start ================================================ */

/*======================================   GET all Vendors list - Start ================================================ */

/**
 * GET all Vendor list
 *
 * Function Name: get_vendor_assoc
 *
 *
 */
function get_vendor_assoc($vendor)
{
    $vendor_profile = get_the_author_meta(
        'wcfmmp_profile_settings',
        $vendor->ID
    );
    $attachment = get_post($vendor_profile['gravatar']);

    $data['id'] = $vendor->ID;
    $data['username'] = $vendor->user_login;
    $data['email'] = $vendor->user_email;
    $data['first_name'] = $vendor->first_name;
    $data['last_name'] = $vendor->last_name;
    $data['author_name'] = get_the_author_meta('display_name', $vendor->ID);
    $data['vendor_store_name'] = get_the_author_meta(
        'wcfmmp_store_name',
        $vendor->ID
    );
    $data['vendor_store_id'] = get_the_author_meta(
        '_wcfmmp_profile_id',
        $vendor->ID
    );
    $data['vendor_email_id'] = get_the_author_meta(
        '_wcfm_email_verified_for',
        $vendor->ID
    );
    $data['vendor_store_slug'] = get_the_author_meta(
        'user_nicename',
        $vendor->ID
    );
    $data['vendor_store_description'] = get_the_author_meta(
        '_store_description',
        $vendor->ID
    );
    $data['vendor_store_logo_url'] = $attachment->guid;

    return $data;
}

function wl_vendors()
{
    if (validate_authorization_header()) {
        $args = [
            'role' => 'wcfm_vendor',
            'orderby' => 'user_nicename',
            'order' => 'ASC'
        ];
        $vendors = get_users($args);
        $data = array_map('get_vendor_assoc', $vendors);

        return $data;
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/*======================================   GET all Vendors list - End ================================================ */

/*====================================== Vendor by id - Start ================================================ */

/**
 * GET Vendor by id
 *
 * Function Name: get_vendor_detail_assoc
 *
 *
 */
function get_vendor_detail_assoc($user_id)
{
    $vendor_profile = get_the_author_meta('wcfmmp_profile_settings', $user_id);
    $attachment_logo = get_post($vendor_profile['gravatar']);
    $attachment_banner = get_post($vendor_profile['banner']);
    $attachment_mobile_banner = get_post($vendor_profile['mobile_banner']);
    $attachment_list_banner = get_post($vendor_profile['list_banner']);

    $data[$user_id]['vendor_store_detail'] = get_the_author_meta(
        'wcfmmp_profile_settings',
        $user_id
    );
    $data[$user_id]['img_url']['vendor_store_logo_url'] =
        $attachment_logo->guid;
    $data[$user_id]['img_url']['vendor_store_banner_url'] =
        $attachment_banner->guid;
    $data[$user_id]['img_url']['vendor_store_mobile_banner_url'] =
        $attachment_mobile_banner->guid;
    $data[$user_id]['img_url']['vendor_store_list_banner_url'] =
        $attachment_list_banner->guid;

    return $data;
}

function wl_vendor_by_id($request)
{
    if (validate_authorization_header()) {
        $user_id = $request['user_id'];
        $data = get_vendor_detail_assoc($user_id);

        return $data;
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

function wl_vendor_products($request)
{
    if (validate_authorization_header()) {
        $args = [
            'post_type' => 'product',
            'author' => $request['user_id'],
            'orderby' => 'post_date',
            'order' => 'DSC'
        ];

        $vendor_products = get_posts($args);
        return $vendor_products;
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/*====================================== Vendor by id - End ================================================ */

add_action('rest_api_init', function () {
    register_rest_route('medpick-api', 'vendors', [
        'methods' => 'GET',
        'callback' => 'wl_vendors'
    ]);

    register_rest_route('medpick-api', 'vendors/(?P<user_id>\d+)', [
        'methods' => 'GET',
        'callback' => 'wl_vendor_by_id'
    ]);

    register_rest_route('medpick-api', 'vendors/(?P<user_id>\d+/products)', [
        'methods' => 'GET',
        'callback' => 'wl_vendor_products'
    ]);
});

/*====================================== Vendor listing - End	 ================================================ */
