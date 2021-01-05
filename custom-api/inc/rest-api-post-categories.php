<?php
/*====================================== Post categories API - Start ================================================ */

/*====================================== Get ALL Post categories list - Start ================================================ */

/**
 * GET all Post categories list
 *
 * Function Name: get_Post_categories
 *
 *
 */

function get_Post_categories()
{
    if (validate_authorization_header()) {
        $defaults = array(
            'taxonomy' => 'category'
        );
        $args = wp_parse_args($args, $defaults);

        $args['taxonomy'] = apply_filters(
            'get_categories_taxonomy',
            $args['taxonomy'],
            $args
        );

        // Back compat.
        if (isset($args['type']) && 'link' === $args['type']) {
            _deprecated_argument(
                __FUNCTION__,
                '3.0.0',
                sprintf(
                    /* translators: 1: "type => link", 2: "taxonomy => link_category" */
                    __('%1$s is deprecated. Use %2$s instead.'),
                    '<code>type => link</code>',
                    '<code>taxonomy => link_category</code>'
                )
            );
            $args['taxonomy'] = 'link_category';
        }
        $posts = get_terms($args);

        $data = [];
        $i = 0;

        foreach ($posts as $post) {
            $data[$i]['id'] = $post->term_id;
            $data[$i]['name'] = $post->name;
            $data[$i]['count'] = $post->count;
            $data[$i]['parent'] = $post->parent;
            $data[$i]['slug'] = $post->slug;
            $data[$i]['description'] = $post->description;
            $i++;
        }

        return $data;
    } else {
        return ['success' => false, 'message' => 'Authorization failed.'];
    }
}

/*====================================== Get ALL Post categories list  - End ================================================ */

/*====================================== GET all Posts listing realted to post categories - Start ================================================ */

/**
 * GET all Posts listing realted to post categories
 *
 * Function Name: get_category_posts_listing_by_id
 *
 *
 */

function get_category_posts_listing_by_id($term_id)
{
    if (validate_authorization_header()) {
        $all_ids = get_posts(array(
            'post_type' => 'post',
            'numberposts' => -1,
            'post_status' => 'publish',
            'tax_query' => array(
                array(
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => $term_id['term_id'],
                    'operator' => 'IN'
                )
            )
        ));

        $data = [];
        $i = 0;

        foreach ($all_ids as $post) {
            $author_id = get_post_field('post_author', $post->ID);
            $tag_terms = get_the_terms($product->ID, 'product_tag');
            $term_list = wp_get_post_terms($post->ID, 'category', array(
                'fields' => 'ids'
            ));
            $attachment_id = get_post_thumbnail_id($post->ID);

            $related = ci_get_related_post($post->ID, -1);

            $data[$i]['id'] = $post->ID;
            $data[$i]['title'] = $post->post_title;
            $data[$i]['content'] = $post->post_content;
            $data[$i]['excerpt'] = get_the_excerpt($post->ID);
            $data[$i]['slug'] = $post->post_name;
            $data[$i]['tag'] = $tag_terms;
            $data[$i]['status'] = $post->post_status;
            $data[$i]['ping'] = $post->ping_status;
            $data[$i]['comment_status'] = $post->comment_status;
            $data[$i]['type'] = $post->post_type;
            $data[$i]['category'] = $term_list;
            $data[$i]['related'] = $related;
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

            $data[$i]['categories'] = get_the_category($post->ID);
            $data[$i]['meta'] = [
                'author_id' => $author_id,
                'author_name' => get_the_author_meta('display_name', $author_id)
            ];

            $i++;
        }

        return $data;
    } else {
        return ['success' => false, 'message' => 'Authorization failed.'];
    }
}

/*====================================== GET all Posts listing realted to post categories - End ================================================ */

/**
 * ADD Function of for Post categories
 */

add_action('rest_api_init', function () {
    register_rest_route('medpick-api', 'post_categories', array(
        'methods' => 'GET',
        'callback' => 'get_Post_categories'
    ));

    register_rest_route(
        'medpick-api',
        'post_categories/(?P<term_id>\d+)',
        array(
            'methods' => 'GET',
            'callback' => 'get_category_posts_listing_by_id'
        )
    );
});

/*====================================== Post categories API - End ================================================ */
