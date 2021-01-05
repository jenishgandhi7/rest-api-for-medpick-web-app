<?php

/*====================================== Product Categories API - Start ================================================ */

/*====================================== Get ALL Product categories list - Start ================================================ */

/**
 * GET all Product categories list
 *
 * Function Name: get_Product_categories
 *
 *
 */

function get_Product_categories()
{
    if (validate_authorization_header()) {
        $defaults = array('numberposts' => 99999, 'taxonomy' => 'product_cat');
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
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/*====================================== Get ALL Product categories list - End ================================================ */

/*====================================== GET all Products listing realted to categories by id - Start ================================================ */

/**
 * GET all Products listing realted to categories by id
 *
 * Function Name: get_category_products_listing
 *
 *
 */
function get_category_products_listing($term_id)
{
    if (validate_authorization_header()) {
        $all_ids = get_posts(array(
            'post_type' => 'product',
            'numberposts' => -1,
            'post_status' => 'publish',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $term_id['term_id'],
                    'operator' => 'IN'
                )
            )
        ));

        $data = [];
        $i = 0;

        foreach ($all_ids as $product) {
            $author_id = get_post_field('post_author', $product->ID);
            $term_list = wp_get_post_terms($product->ID, 'product_cat', array(
                'fields' => 'ids'
            ));
            $terms = get_the_terms($product->ID, 'product_cat');
            $tag_terms = get_the_terms($product->ID, 'product_tag');

            $related = ci_get_related_product($product->ID, -1);
            $tag_terms = get_the_terms($product->ID, 'product_tag');
            $attachment_id = get_post_thumbnail_id($product->ID);

            //vendor attachment
            $vendor_profile = get_the_author_meta(
                'wcfmmp_profile_settings',
                $author_id
            );
            $attachment = get_post($vendor_profile['gravatar']);

            $data[$i]['id'] = $product->ID;
            $data[$i]['title'] = $product->post_title;
            $data[$i]['content'] = $product->post_content;
            $data[$i]['excerpt'] = get_the_excerpt($product->ID);
            $data[$i]['slug'] = $product->post_name;
            $data[$i]['status'] = $product->post_status;
            $data[$i]['ping'] = $product->ping_status;
            $data[$i]['comment_status'] = $product->comment_status;
            $data[$i]['categories'] = $terms;
            $data[$i]['tag'] = $tag_terms;
            $data[$i]['date'] = $product->post_date;
            $data[$i]['post_date_gmt'] = $product->post_date_gmt;
            $data[$i]['post_modified'] = $product->post_modified;
            $data[$i]['post_modified_gmt'] = $product->post_modified_gmt;
            $data[$i]['related_product'] = $related;
            $data[$i]['featured_image'][
                'thumbnail'
            ] = get_the_post_thumbnail_url($product->ID, 'thumbnail');
            $data[$i]['featured_image']['medium'] = get_the_post_thumbnail_url(
                $product->ID,
                'medium'
            );
            $data[$i]['featured_image']['large'] = get_the_post_thumbnail_url(
                $product->ID,
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

            $related_gallery = ci_get_related_gallery($product->ID, -1);
            $data[$i]['product_gallery_image'] = $related_gallery;

            $data[$i]['status'] = $product->post_status;
            $data[$i]['price'] = $product->_price;
            $data[$i]['regular_price'] = $product->_regular_price;
            $data[$i]['sale_price'] = $product->_sale_price;
            $data[$i]['user_role_price'] = get_post_meta(
                $product->ID,
                'product_role_based_price',
                true
            );
            $data[$i]['sku'] = $product->_sku;
            $data[$i]['stock_status'] = $product->_stock_status;
            $data[$i]['manage_stock'] = $product->_manage_stock;
            $data[$i]['min_quantity'] = $product->min_quantity;
            $data[$i]['max_quantity'] = $product->max_quantity;
            $data[$i]['stock quantity'] = $product->_stock;
            $data[$i]['low_stock_amount'] = $product->_low_stock_amount;
            $data[$i]['sold_individually'] = $product->_sold_individually;
            $data[$i]['backorders'] = $product->_backorders;
            $data[$i]['virtual'] = $product->_virtual;
            $data[$i]['downloadable'] = $product->_downloadable;
            $data[$i]['download_limit'] = $product->_download_limit;
            $data[$i]['download_expirty'] = $product->_download_expiry;
            $data[$i]['weight'] = $product->_weight;
            $data[$i]['dimensions']['length'] = $product->_length;
            $data[$i]['dimensions']['width'] = $product->_width;
            $data[$i]['dimensions']['height'] = $product->_height;
            $data[$i]['shipping_class'] = $product->product_shipping_class;
            $data[$i]['upsell_ids'] = $product->_upsell_ids;
            $data[$i]['crosssell_ids'] = $product->_crosssell_ids;
            $data[$i]['purchase_note'] = $product->_purchase_note;
            $data[$i]['menu_order'] = $product->menu_order;
            $data[$i]['comment_status'] = $product->comment_status;
            $data[$i]['date'] = get_the_date('', $product->ID);
            $data[$i]['product_approved_notification'] =
                $product->_wcfm_product_approved_notified;

            $data[$i]['vendor_profile'] = [
                'author_id' => $author_id,
                'author_name' => get_the_author_meta(
                    'display_name',
                    $author_id
                ),
                'vendor_store_name' => get_the_author_meta(
                    'wcfmmp_store_name',
                    $author_id
                ),
                'vendor_store_id' => get_the_author_meta(
                    '_wcfmmp_profile_id',
                    $author_id
                ),
                'vendor_email_id' => get_the_author_meta(
                    '_wcfm_email_verified_for',
                    $author_id
                ),
                'vendor_store_slug' => get_the_author_meta(
                    'user_nicename',
                    $author_id
                ),
                'vendor_store_description' => get_the_author_meta(
                    '_store_description',
                    $author_id
                ),
                'vendor_store_logo_url' => $attachment->guid,
                'vendor_store_total_review_count' => get_the_author_meta(
                    '_wcfmmp_total_review_count',
                    $author_id
                ),
                'vendor_store_total_review_rating' => get_the_author_meta(
                    '_wcfmmp_total_review_rating',
                    $author_id
                ),
                'vendor_store_avg_review_rating' => get_the_author_meta(
                    '_wcfmmp_avg_review_rating',
                    $author_id
                ),
                'vendor_store_category_review_rating' => get_the_author_meta(
                    '_wcfmmp_category_review_rating',
                    $author_id
                )
            ];

            //review couts and ratting
            $data[$i]['reviews_count'] = get_post_meta(
                $product->ID,
                '_wc_review_count',
                true
            );
            $data[$i]['reviews_avg_rating'] = get_post_meta(
                $product->ID,
                '_wc_average_rating',
                true
            );
            $data[$i]['rating_count'] = get_post_meta(
                $product->ID,
                '_wc_rating_count',
                true
            );

            //display comments
            $data[$i]['comments'] = get_comments(array(
                'post_id' => $product->ID
            ));
            $data[$i]['commission'] = get_post_meta(
                $product->ID,
                '_wp_attached_file',
                true
            );
            $data[$i]['product_views'] = get_post_meta(
                $product->ID,
                '_wcfm_product_views',
                true
            );
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

/*====================================== GET all Products listing realted to categories by id - End ================================================ */

/*======================================  GET Related image gallery of product- End ================================================ */

/**
 * GET Related image gallery of product
 * Function name = ci_get_related_gallery
 */
function ci_get_related_gallery($post_id, $related_count, $args = array())
{
    $related_gallery_id = array_filter(
        explode(',', get_post_meta($post_id, '_product_image_gallery', true))
    );
    $related = [];
    for ($i = 0; $i < count($related_gallery_id); $i++) {
        $new_related = [];
        $new_related['id'] = $related_gallery_id[$i];
        $new_related['img_src'] = wp_get_attachment_image_sizes(
            $related_gallery_id[$i],
            'full'
        );
        $new_related['img_sizes'] = wp_get_attachment_image_src(
            $related_gallery_id[$i],
            'full'
        );
        $new_related['img_srcset'] = wp_get_attachment_image_srcset(
            $related_gallery_id[$i],
            'full'
        );
        $related[] = $new_related;
    }

    return $related;
}

/**
 * ADD Function of for Product categories
 */

add_action('rest_api_init', function () {
    register_rest_route('medpick-api', 'product_categories', array(
        'methods' => 'GET',
        'callback' => 'get_Product_categories'
    ));

    register_rest_route(
        'medpick-api',
        'product_categories/(?P<term_id>\d+)',
        array(
            'methods' => 'GET',
            'callback' => 'get_category_products_listing'
        )
    );
});

/*====================================== Product Categories API - End ================================================ */
