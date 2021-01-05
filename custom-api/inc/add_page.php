<?php

/*====================================== Add Page API- Start ================================================ */
/**
 * Create new page by using rest api
 *
 * Function Name: wl_addPage
 *
 *
 */
function wl_addPage(WP_REST_Request $request)
{
    if (validate_authorization_header()) {
        $arr_request = json_decode($request->get_body(), true);

        $post = array(
            'ID' => $arr_request['ID'], //Are you updating an existing post?
            'menu_order' => $arr_request['menu_order'], //If new post is a page, sets the order should it appear in the tabs.
            'page_template' => $arr_request['page_template'], //Sets the template for the page.
            'comment_status' => $arr_request['comment_status'], // 'closed' means no comments.
            'ping_status' => $arr_request['ping_status'], //Ping status?
            'pinged' => $arr_request['pinged'], //?
            'post_author' => $arr_request['post_author'], //The user ID number of the author.
            'post_category' => $arr_request['post_category'], //Add some categories.
            'post_content' => $arr_request['post_content'], //The full text of the post.
            'post_date' => $arr_request['post_date'], //The time post was made.
            'post_date_gmt' => $arr_request['post_date_gmt'], //The time post was made, in GMT.
            'post_excerpt' => $arr_request['post_excerp'], //For all your post excerpt needs.
            'post_name' => $arr_request['post_name'], // The name (slug) for your post
            'post_parent' => $arr_request['post_parent'], //Sets the parent of the new post.
            'post_password' => $arr_request['post_password'], //password for post?
            'post_status' => $arr_request['post_status'], //Set the status of the new post.
            'post_title' => $arr_request['post_title'], //The title of your post.
            'post_type' => $arr_request['post_type'], //Sometimes you want to post a page.
            'tags_input' => $arr_request['tags_input'], //For tags.
            'to_ping' => $arr_request['to_ping'] //?
        );

        return wp_insert_post($post);
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

add_action('rest_api_init', function () {
    register_rest_route('medpick-api', 'addpage', [
        'methods' => 'POST',
        'callback' => 'wl_addPage'
    ]);
});

/*====================================== Add Page API- End ================================================ */
