<?php
/*====================================== Post API - Start ================================================ */

/*====================================== Get ALL Post list - Start ================================================ */

/**
 * GET all Posts list
 *
 * Function Name: wl_posts
 *
 *
 */

function wl_posts()
{
    if (validate_authorization_header()) {
        $args = [
            'numberposts' => 99999,
            'post_type' => 'post'
        ];

        $posts = get_posts($args);

        $data = [];
        $i = 0;

        foreach ($posts as $post) {
            $author_id = get_post_field('post_author', $post->ID);

            $term_list = wp_get_post_terms($post->ID, 'category', array(
                'fields' => 'ids'
            ));
            $attachment_id = get_post_thumbnail_id($post->ID);
            $tag_terms = get_the_terms($post->ID, 'post_tag');
            $related = ci_get_related_post($post->ID, -1);

            $data[$i]['id'] = $post->ID;
            $data[$i]['title'] = $post->post_title;
            $data[$i]['permalink'] = get_permalink($post->ID);

            $data[$i]['content'] = $post->post_content;
            $data[$i]['excerpt'] = get_the_excerpt($post->ID);
            $data[$i]['slug'] = $post->post_name;
            $data[$i]['categories'] = get_the_category($post->ID);
            $data[$i]['tag'] = $tag_terms;
            $data[$i]['status'] = $post->post_status;
            $data[$i]['ping'] = $post->ping_status;
            $data[$i]['comment_status'] = $post->comment_status;
            $data[$i]['type'] = $post->post_type;
            $data[$i]['related_post'] = $related;
            $data[$i]['post_author'] = $post->post_author;
            $data[$i]['featured_image'][
                'thumbnail'
            ] = get_the_post_thumbnail_url($post->ID, 'thumbnail');
            $data[$i]['featured_image']['medium'] = get_the_post_thumbnail_url(
                $post->ID,
                'medium'
            );
            $data[$i]['featured_image']['large'] = get_the_post_thumbnail_url(
                $post->ID,
                'large'
            );

            $data[$i]['attachment_image'] = [
                'img_sizes' => wp_get_attachment_image_sizes($attachment_id),
                'img_src' => wp_get_attachment_image_src(
                    $attachment_id,
                    'full'
                ),
                'img_srcset' => wp_get_attachment_image_srcset($attachment_id)
            ];
            $data[$i]['date'] = $post->post_date;
            $data[$i]['date_gmt'] = $post->post_date_gmt;
            $data[$i]['post_modified'] = $post->post_modified;

            $data[$i]['meta'] = [
                'author_id' => $author_id,
                'author_name' => get_the_author_meta('display_name', $author_id)
            ];

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

/*======================================  Get ALL Post list - End ================================================ */

/*====================================== Get Post by slug - Start ================================================ */

/**
 *  GET Posts by slug name
 *
 * Function Name: wl_post
 *
 *
 */
function wl_post($slug)
{
    if (validate_authorization_header()) {
        $args = [
            'name' => $slug['slug'],
            'post_type' => 'post'
        ];

        $post = get_posts($args);

        $author_id = get_post_field('post_author', $post[0]->ID);

        $term_list = wp_get_post_terms($post[0]->ID, 'category', array(
            'fields' => 'ids'
        ));
        $attachment_id = get_post_thumbnail_id($post[0]->ID);

        $related = ci_get_related_post($post[0]->ID, -1);

        $data['id'] = $post[0]->ID;
        $data['title'] = $post[0]->post_title;
        $data['content'] = $post[0]->post_content;
        $data['excerpt'] = get_the_excerpt($post[0]->ID);
        $data['slug'] = $post[0]->post_name;
        $data['status'] = $post[0]->post_status;
        $data['ping'] = $post[0]->ping_status;
        $data['comment_status'] = $post[0]->comment_status;
        $data['type'] = $post[0]->post_type;
        $data['category'] = $term_list;
        $data['related'] = $related;
        $data['post_author'] = $post[0]->post_author;
        $data['featured_image']['thumbnail'] = get_the_post_thumbnail_url(
            $post[0]->ID,
            'thumbnail'
        );
        $data['featured_image']['medium'] = get_the_post_thumbnail_url(
            $post[0]->ID,
            'medium'
        );
        $data['featured_image']['large'] = get_the_post_thumbnail_url(
            $post[0]->ID,
            'large'
        );

        $data['attachment_image'] = [
            'img_sizes' => wp_get_attachment_image_sizes($attachment_id),
            'img_src' => wp_get_attachment_image_src($attachment_id, 'full'),
            'img_srcset' => wp_get_attachment_image_srcset($attachment_id)
        ];

        $data['date'] = $post[0]->post_date;
        $data['date_gmt'] = $post[0]->post_date_gmt;
        $data['post_modified'] = $post[0]->post_modified;
        $data['categories'] = get_the_category($post[0]->ID);
        $data['meta'] = [
            'author_id' => $author_id,
            'author_name' => get_the_author_meta('display_name', $author_id)
        ];

        return $data;
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/*====================================== Get Post by slug - End ================================================ */

/*====================================== Get Post by ID - Start ================================================ */

/**
 * GET Posts by post id
 *
 * Function Name: get_latest_posts_by_id
 *
 *
 */

function get_latest_posts_by_id($post_id)
{
    if (validate_authorization_header()) {
        $post = get_post($post_id['post_id'], array('post_type' => 'post'));

        $author_id = get_post_field('post_author', $post->ID);

        $term_list = wp_get_post_terms($post->ID, 'category', array(
            'fields' => 'ids'
        ));
        $attachment_id = get_post_thumbnail_id($post->ID);
        $tag_terms = get_the_terms($post->ID, 'post_tag');

        $related = ci_get_related_post($post->ID, -1);

        $data['id'] = $post->ID;
        $data['title'] = $post->post_title;
        $data['content'] = $post->post_content;
        $data['excerpt'] = get_the_excerpt($post->ID);
        $data['slug'] = $post->post_name;
        $data['type'] = $post->post_type;
        $data['status'] = $post->post_status;
        $data['ping'] = $post->ping_status;
        $data['comment_status'] = $post->comment_status;
        $data['categories'] = get_the_category($post->ID);
        $data['tag'] = $tag_terms;
        $data['related'] = $related;
        $data['post_author'] = $post->post_author;
        $data['featured_image']['thumbnail'] = get_the_post_thumbnail_url(
            $post->ID,
            'thumbnail'
        );
        $data['featured_image']['medium'] = get_the_post_thumbnail_url(
            $post->ID,
            'medium'
        );
        $data['featured_image']['large'] = get_the_post_thumbnail_url(
            $post->ID,
            'large'
        );

        $data['attachment_image'] = [
            'img_sizes' => wp_get_attachment_image_sizes($attachment_id),
            'img_src' => wp_get_attachment_image_src($attachment_id, 'full'),
            'img_srcset' => wp_get_attachment_image_srcset($attachment_id)
        ];

        $data['date'] = $post->post_date;
        $data['date_gmt'] = $post->post_date_gmt;
        $data['post_modified'] = $post->post_modified;

        $data['meta'] = [
            'author_id' => $author_id,
            'author_name' => get_the_author_meta('display_name', $author_id)
        ];

        //display comments
        $data['comments'] = get_comments(array('post_id' => $post->ID));

        $i++;

        return $data;
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/*====================================== Get Post by ID - End ================================================ */

/*====================================== Get Related Posts - Start ================================================ */

/**
 * Function for Get Related Posts of that post
 *
 * Function Name: ci_get_related_post
 *
 *
 */

function ci_get_related_post($post_id, $related_count, $args = array())
{
    $terms = get_the_terms($post_id, 'category');

    if (empty($terms)) {
        $terms = array();
    }

    $term_list = wp_list_pluck($terms, 'slug');

    $related_args = array(
        'post_type' => 'post',
        'posts_per_page' => $related_count,
        'post_status' => 'publish',
        'post__not_in' => array($post_id),
        'orderby' => 'rand',
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => $term_list
            )
        )
    );

    $related_post = new WP_Query($related_args);
    $related = [];
    while ($related_post->have_posts()):
        $related_post->the_post();
        $new_related = [];
        $new_related['id'] = get_the_ID();
        $new_related['slug'] = get_post_field('post_name', get_the_ID());
        $related[] = $new_related;
    endwhile;

    return $related;
}

/*====================================== Get Related Posts- End ================================================ */

/*====================================== Post Filters - Start ================================================ */

/**
 * Function for Post listing by before date
 *
 * Function Name: filter_Post_By_Date_Before
 *
 *
 */

function filter_Post_By_Date_Before($posts, $request)
{
    $params = $request['date'];
    $date = $params;
    $data = [];
    if (!isset($date)) {
        return $data;
    }
    $i = 0;

    foreach ($posts as $post) {
        if (get_post_timestamp($post['id']) < strtotime($date)) {
            $data[$i] = $post;

            $i++;
        }
    }

    return $data;
}

/**
 * Function for Post listing by  after date
 *
 * Function Name: filter_Post_By_Date_After
 *
 *
 */

function filter_Post_By_Date_After($posts, $request)
{
    $params = $request['date'];
    $date = $params;
    $data = [];
    if (!isset($date)) {
        return $data;
    }
    $i = 0;

    foreach ($posts as $post) {
        if (get_post_timestamp($post['id']) > strtotime($date)) {
            $data[$i] = $post;

            $i++;
        }
    }
    return $data;
}

add_filter('filter_post_by_date_before', 'filter_Post_By_Date_Before', 10, 2);

add_filter('filter_post_by_date_after', 'filter_Post_By_Date_After', 10, 2);

function get_filter_posts_by_date_before($request)
{
    if (validate_authorization_header()) {
        return apply_filters(
            'filter_post_by_date_before',
            wl_posts(),
            $request
        );
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

function get_filter_posts_by_date_after($request)
{
    if (validate_authorization_header()) {
        return apply_filters('filter_post_by_date_after', wl_posts(), $request);
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/**
 * Function for Post listing by tag
 *
 * Function Name: filter_Post_By_Tag
 *
 *
 */

function filter_Post_By_Tag($posts, $request)
{
    $params = $request['tag'];
    $data = [];
    $i = 0;

    foreach ($posts as $post) {
        $tags = $post['tag'];
        if (is_array($tags) && !empty($tags)) {
            foreach ($tags as $tag) {
                if ($tag->name == $params) {
                    $data[$i] = $post;
                    $i++;
                    break;
                }
            }
        }
    }

    return $data;
}

add_filter('filter_post_by_tag', 'filter_Post_By_Tag', 10, 2);

function get_filter_posts_by_tag($request)
{
    if (validate_authorization_header()) {
        return apply_filters('filter_post_by_tag', wl_posts(), $request);
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/**
 * Function for Post listing by @category
 *
 * Function Name: filter_Post_By_Category
 *
 *
 */

function filter_Post_By_Category($posts, $request)
{
    $params = $request['category'];
    $data = [];
    $i = 0;

    foreach ($posts as $post) {
        $categories = $post['categories'];
        if (is_array($categories) && !empty($categories)) {
            foreach ($categories as $category) {
                if ($category->name == $params) {
                    $data[$i] = $post;
                    $i++;
                    break;
                }
            }
        }
    }

    return $data;
}

add_filter('filter_post_by_category', 'filter_Post_By_Category', 10, 2);

function get_filter_posts_by_category($request)
{
    if (validate_authorization_header()) {
        return apply_filters('filter_post_by_category', wl_posts(), $request);
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/**
 * Add function for Posts
 */
add_action('rest_api_init', function () {
    register_rest_route('medpick-api', 'posts', [
        'methods' => 'GET',
        'callback' => 'wl_posts'
    ]);

    register_rest_route('medpick-api', 'posts/(?P<post_id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_latest_posts_by_id'
    ));

    register_rest_route('medpick-api', 'posts/(?P<slug>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'wl_post'
    ));

    register_rest_route('medpick-api', 'posts_before/(?P<date>[a-zA-Z0-9-]+)', [
        'methods' => 'GET',
        'callback' => 'get_filter_posts_by_date_before'
    ]);

    register_rest_route('medpick-api', 'posts_after/(?P<date>[a-zA-Z0-9-]+)', [
        'methods' => 'GET',
        'callback' => 'get_filter_posts_by_date_after'
    ]);

    register_rest_route('medpick-api', 'posts_tag/(?P<tag>[a-zA-Z0-9-]+)', [
        'methods' => 'GET',
        'callback' => 'get_filter_posts_by_tag'
    ]);

    register_rest_route(
        'medpick-api',
        'posts_category/(?P<category>[a-zA-Z0-9-]+)',
        [
            'methods' => 'GET',
            'callback' => 'get_filter_posts_by_category'
        ]
    );
});

/*====================================== Post API - End ================================================ */
