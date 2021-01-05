<?php

/*====================================== USER listing - Start ================================================ */

/*======================================   GET all customer role user - Start ================================================ */

/**
 * GET all customer role user
 *
 * Function Name: wl_users
 *
 *
 */

function get_user_assoc($user)
{
    $data['id'] = $user->ID;
    $data['username'] = $user->user_login;
    $data['user_nick_name'] = $user->user_nicename;
    $data['email'] = $user->user_email;
    $data['first_name'] = $user->first_name;
    $data['last_name'] = $user->last_name;
    $data['url'] = $user->user_url;
    $data['registered_on'] = $user->user_registered;
    $data['user_notification'] = $user->send_user_notification;
    $data['role'] = $user->roles[0];

    return $data;
}

function wl_users()
{
    if (validate_authorization_header()) {
        $args = ['role' => 'customer'];
        $users = get_users($args);

        $data = array_map('get_user_assoc', $users);
        return $data;
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/*====================================== get user listing - End ================================================ */

/*======================================  User by id - Start ================================================ */

/**
 * GET User by id
 *
 * Function Name: wl_user_by_id
 *
 *
 */

function wl_user_by_id($request)
{
    if (validate_authorization_header()) {
        $user = get_user_by('id', $request['user_id']);
        $data = get_user_assoc($user);
        return $data;
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/*======================================  User by id - End ================================================ */

add_action('rest_api_init', function () {
    register_rest_route('medpick-api', 'users', [
        'methods' => 'GET',
        'callback' => 'wl_users'
    ]);

    register_rest_route('medpick-api', 'users/(?P<user_id>\d+)', [
        'methods' => 'GET',
        'callback' => 'wl_user_by_id'
    ]);
});

/*====================================== USER listing - End ================================================ */
