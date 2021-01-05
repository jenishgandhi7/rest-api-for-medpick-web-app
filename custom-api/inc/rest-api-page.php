<?php
/*====================================== Page API - Start ================================================ */
/**
 * Get Page listing
 *
 * Function Name: wl_pages
 *
 *
 */

function wl_pages()
{
    if (validate_authorization_header()) {
        $pages = get_pages();
        $data = [];
        $i = 0;
        foreach ($pages as $page) {
            $data[$i]['id'] = $page->ID;
            $data[$i]['author'] = $page->post_author;
            $data[$i]['date'] = $page->post_date;
            $data[$i]['date_gmt'] = $page->post_date_gmt;
            $data[$i]['content'] = $page->post_content;
            $data[$i]['title'] = $page->post_title;
            $data[$i]['excerpt'] = $page->post_excerpt;
            $data[$i]['status'] = $page->post_status;
            $data[$i]['comment_status'] = $page->comment_status;
            $data[$i]['ping_status'] = $page->ping_status;
            $data[$i]['password'] = $page->post_password;
            $data[$i]['name'] = $page->post_name;
            $data[$i]['to_ping'] = $page->to_ping;
            $data[$i]['pinged'] = $page->pined;
            $data[$i]['post_modified'] = $page->post_modified;
            $data[$i]['content_filtered'] = $page->post_content_filtered;
            $data[$i]['parent'] = $page->post_parent;
            $data[$i]['guid'] = $page->guid;
            $data[$i]['menu_order'] = $page->menu_order;
            $data[$i]['type'] = $page->post_type;
            $data[$i]['mime_type'] = $page->mime_type;
            $data[$i]['comment_count'] = $page->comment_count;
            $data[$i]['filter'] = $page->filter;

            $i++;
        }

        return $data;
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/**
 * get all sub-pages of a parent page by id.
 *
 * Function Name: wl_page
 *
 *
 */

function wl_page($parent_id)
{
    if (validate_authorization_header()) {
        $arg = ['parent' => $parent_id['parent_id']];
        $pages = get_pages($arg);

        $data = [];
        $i = 0;
        foreach ($pages as $page) {
            $data[$i]['id'] = $page->ID;
            $data[$i]['author'] = $page->post_author;
            $data[$i]['date'] = $page->post_date;
            $data[$i]['date_gmt'] = $page->post_date_gmt;
            $data[$i]['content'] = $page->post_content;
            $data[$i]['title'] = $page->post_title;
            $data[$i]['excerpt'] = $page->post_excerpt;
            $data[$i]['status'] = $page->post_status;
            $data[$i]['comment_status'] = $page->comment_status;
            $data[$i]['ping_status'] = $page->ping_status;
            $data[$i]['password'] = $page->post_password;
            $data[$i]['name'] = $page->post_name;
            $data[$i]['to_ping'] = $page->to_ping;
            $data[$i]['pinged'] = $page->pined;
            $data[$i]['post_modified'] = $page->post_modified;
            $data[$i]['content_filtered'] = $page->post_content_filtered;
            $data[$i]['parent'] = $page->post_parent;
            $data[$i]['guid'] = $page->guid;
            $data[$i]['menu_order'] = $page->menu_order;
            $data[$i]['type'] = $page->post_type;
            $data[$i]['mime_type'] = $page->mime_type;
            $data[$i]['comment_count'] = $page->comment_count;
            $data[$i]['filter'] = $page->filter;

            $i++;
        }
        return $data;
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/**
 * get all home page slider image.
 *
 * Function Name: get_slider_image
 *
 *
 */

function get_slider_image($home_page_id)
{
    if (validate_authorization_header()) {
        $arg = ['page_id' => $home_page_id['page_id']];

        $data = [];
        $i = 0;
        $images = get_attached_media('image', $arg['page_id']);

        foreach ($images as $image) {
            $data[$i]['imag_url'] = $image->guid;
            $i++;
        }
        return $data;
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

add_action('rest_api_init', function () {
    register_rest_route('medpick-api', 'pages', [
        'methods' => 'GET',
        'callback' => 'wl_pages'
    ]);

    register_rest_route('medpick-api', 'page/(?P<parent_id>[a-zA-Z0-9-]+)', [
        'methods' => 'GET',
        'callback' => 'wl_page'
    ]);

    register_rest_route('medpick-api', 'addpage', [
        'methods' => 'POST',
        'callback' => 'wl_addPage'
    ]);

    register_rest_route('medpick-api', 'get_image/(?P<page_id>[a-zA-Z0-9-]+)', [
        'methods' => 'GET',
        'callback' => 'get_slider_image'
    ]);
});

/*====================================== Page API - End ================================================ */
