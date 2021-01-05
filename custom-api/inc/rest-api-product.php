<?php
/*====================================== Product API - Start ================================================ */

/*====================================== Get ALL Products list - Start ================================================ */

/**
 * GET all Products list
 *
 * Function Name: wl_product_list
 *
 *
 */

function wl_product_list()
{
    if (validate_authorization_header()) {
        $args = [
            'numberposts' => 99999,
            'post_type' => 'product'
        ];

        $products = get_posts($args);

        $data = [];
        $i = 0;

        foreach ($products as $product) {
            $term_list = wp_get_post_terms($product->ID, 'product_cat', array(
                'fields' => 'ids'
            ));
            $terms = get_the_terms($product->ID, 'product_cat');
            $author_id = get_post_field('post_author', $product->ID);
            $related = ci_get_related_product($product->ID, -1);
            $tag_terms = get_the_terms($product->ID, 'product_tag');

            $data[$i]['id'] = $product->ID;
            $data[$i]['title'] = $product->post_title;
            $data[$i]['content'] = $product->post_content;
            $data[$i]['excerpt'] = get_the_excerpt($product->ID);
            $data[$i]['slug'] = $product->post_name;
            $data[$i]['type'] = $product->post_type;
            $data[$i]['status'] = $product->post_status;
            $data[$i]['ping'] = $product->ping_status;
            $data[$i]['comment_status'] = $product->comment_status;
            //		$data[$i]['category'] = $term_list;
            $data[$i]['related_product'] = $related;
            $data[$i]['categories'] = $terms;
            $data[$i]['tag'] = $tag_terms;
            $data[$i]['date'] = $product->post_date;
            $data[$i]['date_gmt'] = $product->post_date_gmt;
            $data[$i]['post_modified'] = $product->post_modified;

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
            $attachment_id = get_post_thumbnail_id($product->ID);
            $data[$i]['attachment_image'] = [
                'img_sizes' => wp_get_attachment_image_sizes($attachment_id),
                'img_src' => wp_get_attachment_image_src(
                    $attachment_id,
                    'full'
                ),
                'img_srcset' => wp_get_attachment_image_srcset($attachment_id)
            ];
            $data[$i]['status'] = $product->post_status;
            $data[$i]['price'] = $product->_price;
            $data[$i]['regular_price'] = $product->_regular_price;
            $data[$i]['sale_price'] = $product->_sale_price;
            $data[$i]['sku'] = $product->_sku;
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
/*======================================  Get ALL Products list  - End ================================================ */

/*====================================== Get Product by slug - Start ================================================ */

/**
 * GET Product by slug
 *
 * Function Name: wl_product_detail
 *
 *
 */

function wl_product_detail($slug)
{
    if (validate_authorization_header()) {
        $args = [
            'name' => $slug['slug'],
            'post_type' => 'product'
        ];

        $product = get_posts($args);
        $author_id = get_post_field('post_author', $product[0]->ID);
        $term_list = wp_get_post_terms($product[0]->ID, 'product_cat', array(
            'fields' => 'ids'
        ));
        $terms = get_the_terms($product[0]->ID, 'product_cat');
        $attachment_id = get_post_thumbnail_id($product[0]->ID);
        $related = ci_get_related_product($product[0]->ID, -1);

        //vendor attachment
        $vendor_profile = get_the_author_meta(
            'wcfmmp_profile_settings',
            $author_id
        );
        $attachment = get_post($vendor_profile['gravatar']);

        $data['id'] = $product[0]->ID;
        $data['title'] = $product[0]->post_title;
        $data['content'] = $product[0]->post_content;
        $data['excerpt'] = get_the_excerpt($product[0]->ID);
        $data['slug'] = $product[0]->post_name;
        $data['category'] = $term_list;
        $data['categories'] = $terms;
        $data['date'] = $product[0]->post_date;
        $data['post_date_gmt'] = $product[0]->post_date_gmt;
        $data['post_modified'] = $product[0]->post_modified;
        $data['post_modified_gmt'] = $product[0]->post_modified_gmt;
        $data['related'] = $related;

        $data['featured_image']['thumbnail'] = get_the_post_thumbnail_url(
            $product[0]->ID,
            'thumbnail'
        );
        $data['featured_image']['medium'] = get_the_post_thumbnail_url(
            $product[0]->ID,
            'medium'
        );
        $data['featured_image']['large'] = get_the_post_thumbnail_url(
            $product[0]->ID,
            'large'
        );

        $data['attachment_image'] = [
            'img_sizes' => wp_get_attachment_image_sizes($attachment_id),
            'img_src' => wp_get_attachment_image_src($attachment_id, 'full'),
            'img_srcset' => wp_get_attachment_image_srcset($attachment_id)
        ];

        $data['featured_image']['thumbnail'] = get_the_post_thumbnail_url(
            $product[0]->ID,
            'thumbnail'
        );
        $data['status'] = $product[0]->post_status;
        $data['price'] = $product[0]->_price;
        $data['regular_price'] = $product[0]->_regular_price;
        $data['sale_price'] = $product[0]->_sale_price;
        $data['sku'] = $product[0]->_sku;
        $data['stock_status'] = $product[0]->_stock_status;
        $data['manage_stock'] = $product[0]->_manage_stock;
        $data['sold_individually'] = $product[0]->_sold_individually;
        $data['weight'] = $product[0]->_weight;
        $data['dimensions']['length'] = $product[0]->product_length;
        $data['dimensions']['width'] = $product[0]->product_width;
        $data['dimensions']['height'] = $product[0]->product_height;
        $data['shipping_class'] = $product[0]->product_shipping_class;
        $data['upsell_ids'] = $product[0]->upsell_ids;
        $data['crosssell_ids'] = $product[0]->crosssell_ids;
        $data['purchase_note'] = $product[0]->_purchase_note;
        $data['menu_order'] = $product[0]->menu_order;
        $data['comment_status'] = $product[0]->comment_status;

        $data['type'] = $product[0]->product_type;
        $data['featured'] = $product[0]->_featured;
        $data['featured'] = $product[0]->product_featured;
        $data['catalog_visibility'] = $product[0]->product_catalog_visibility;
        $data['short_description'] = $product[0]->product_description;

        $data['vendor_profile'] = [
            'author_id' => $author_id,
            'author_name' => get_the_author_meta('display_name', $author_id),
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

        $related_gallery_id = array_filter(
            explode(
                ',',
                get_post_meta($product->ID, '_product_image_gallery', true)
            )
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

        $data['product_gallery_image'] = $related;
        $data['user_role_price'] = get_post_meta(
            $product->ID,
            'product_role_based_price',
            true
        );

        $data['reviews count'] = get_post_meta(
            $product->ID,
            '_wc_review_count',
            true
        );
        $data['reviews count'] = get_post_meta(
            $product->ID,
            '_wc_average_rating',
            true
        );
        $data['rating_count'] = get_post_meta(
            $product->ID,
            '_wc_rating_count',
            true
        );
        //display comments
        $data['comments'] = get_comments(array('post_id' => $product->ID));

        $data['commission'] = get_post_meta(
            $product->ID,
            '_wcfmmp_commission',
            true
        );
        $data['product_views'] = get_post_meta(
            $product->ID,
            '_wcfm_product_views',
            true
        );

        $i++;

        return $data;
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/*======================================  Get Product by slug   - End ================================================ */

/*====================================== Get Product by id - Start ================================================ */

/**
 * GET Product by id
 *
 * Function Name: wl_product_detail_by_id
 *
 *
 */

function wl_product_detail_by_id($post_id)
{
    if (validate_authorization_header()) {
        //comma separated list of ids

        $product = get_post($post_id['post_id'], array(
            'post_type' => 'product'
        ));
        $author_id = get_post_field('post_author', $product->ID);
        $term_list = wp_get_post_terms($product->ID, 'product_cat', array(
            'fields' => 'ids'
        ));
        $terms = get_the_terms($product->ID, 'product_cat');
        $tag_terms = get_the_terms($product->ID, 'product_tag');
        $attachment_id = get_post_thumbnail_id($product->ID);
        $related = ci_get_related_product($product->ID, -1);

        //vendor attachment
        $vendor_profile = get_the_author_meta(
            'wcfmmp_profile_settings',
            $author_id
        );
        $attachment = get_post($vendor_profile['gravatar']);

        $data['id'] = $product->ID;
        $data['title'] = $product->post_title;
        $data['content'] = $product->post_content;
        $data['excerpt'] = get_the_excerpt($product->ID);
        $data['slug'] = $product->post_name;
        $data['status'] = $product->post_status;
        $data['ping'] = $product->ping_status;
        $data['comment_status'] = $product->comment_status;
        $data['categories'] = $terms;
        $data['tag'] = $tag_terms;
        $data['date'] = $product->post_date;
        $data['post_date_gmt'] = $product->post_date_gmt;
        $data['post_modified'] = $product->post_modified;
        $data['post_modified_gmt'] = $product->post_modified_gmt;
        $data['related_product'] = $related;
        $data['featured_image']['thumbnail'] = get_the_post_thumbnail_url(
            $product->ID,
            'thumbnail'
        );
        $data['featured_image']['medium'] = get_the_post_thumbnail_url(
            $product->ID,
            'medium'
        );
        $data['featured_image']['large'] = get_the_post_thumbnail_url(
            $product->ID,
            'large'
        );
        $data['attachment_image'] = [
            'img_sizes' => wp_get_attachment_image_sizes($attachment_id),
            'img_src' => wp_get_attachment_image_src($attachment_id, 'full'),
            'img_srcset' => wp_get_attachment_image_srcset($attachment_id)
        ];

        $related_gallery = ci_get_related_gallery($product->ID, -1);
        $data[$i]['product_gallery_image'] = $related_gallery;

        $data['status'] = $product->post_status;
        $data['price'] = $product->_price;
        $data['regular_price'] = $product->_regular_price;
        $data['sale_price'] = $product->_sale_price;
        $data['user_role_price'] = get_post_meta(
            $product->ID,
            'product_role_based_price',
            true
        );
        $data['sku'] = $product->_sku;
        $data['stock_status'] = $product->_stock_status;
        $data['manage_stock'] = $product->_manage_stock;
        $data['min_quantity'] = $product->min_quantity;
        $data['max_quantity'] = $product->max_quantity;
        $data['stock quantity'] = $product->_stock;
        $data['low_stock_amount'] = $product->_low_stock_amount;
        $data['sold_individually'] = $product->_sold_individually;
        $data['backorders'] = $product->_backorders;
        $data['virtual'] = $product->_virtual;
        $data['downloadable'] = $product->_downloadable;
        $data['download_limit'] = $product->_download_limit;
        $data['download_expirty'] = $product->_download_expiry;
        $data['weight'] = $product->_weight;
        $data['dimensions']['length'] = $product->_length;
        $data['dimensions']['width'] = $product->_width;
        $data['dimensions']['height'] = $product->_height;
        $data['shipping_class'] = $product->product_shipping_class;
        $data['upsell_ids'] = $product->_upsell_ids;
        $data['crosssell_ids'] = $product->_crosssell_ids;
        $data['purchase_note'] = $product->_purchase_note;
        $data['menu_order'] = $product->menu_order;
        $data['comment_status'] = $product->comment_status;
        $data['date'] = get_the_date('', $product->ID);
        $data['catalog_visibility'] = $product->product_catalog_visibility;
        $data['short_description'] = $product->product_description;
        $data['product_approved_notification'] =
            $product->_wcfm_product_approved_notified;

        $data['vendor_profile'] = [
            'author_id' => $author_id,
            'author_name' => get_the_author_meta('display_name', $author_id),
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

        $data['reviews count'] = get_post_meta(
            $product->ID,
            '_wc_review_count',
            true
        );
        $data['reviews count'] = get_post_meta(
            $product->ID,
            '_wc_average_rating',
            true
        );
        $data['rating_count'] = get_post_meta(
            $product->ID,
            '_wc_rating_count',
            true
        );
        //display comments
        $data['comments'] = get_comments(array('post_id' => $product->ID));
        $data['commission'] = get_post_meta(
            $product->ID,
            '_wcfmmp_commission',
            true
        );
        $data['product_views'] = get_post_meta(
            $product->ID,
            '_wcfm_product_views',
            true
        );

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

/*======================================  Get Product by slug   - End ================================================ */

/*====================================== GET Related Product of product - Start ================================================ */

/**
 * GET Related Product of product
 *
 * Function Name: ci_get_related_product
 *
 *
 */

function ci_get_related_product($post_id, $related_count, $args = array())
{
    $terms = get_the_terms($post_id, 'product_cat');

    if (empty($terms)) {
        $terms = array();
    }

    $term_list = wp_list_pluck($terms, 'slug');

    $related_args = array(
        'post_type' => 'product',
        'posts_per_page' => $related_count,
        'post_status' => 'publish',
        'post__not_in' => array($post_id),
        'orderby' => 'rand',
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $term_list
            )
        )
    );

    $related_products = new WP_Query($related_args);
    $related = [];
    while ($related_products->have_posts()):
        $related_products->the_post();
        $new_related = [];
        $new_related['id'] = get_the_ID();
        $new_related['slug'] = get_post_field('post_name', get_the_ID());
        $related[] = $new_related;
    endwhile;

    return $related;
}

/*====================================== GET Related Product of product - End ================================================ */

/*======================================  Filter for Products  - Start ================================================ */

/**
 * get Filter products by @category
 *
 * Function Name: filter_Product_By_Category
 *
 *
 */

function filter_Product_By_Category($products, $request)
{
    $params = $request['category'];
    $data = [];
    $i = 0;

    foreach ($products as $product) {
        $categories = $product['categories'];
        if (is_array($categories) && !empty($categories)) {
            foreach ($categories as $category) {
                if ($category->slug == $params) {
                    $data[$i] = $product;
                    $i++;
                    break;
                }
            }
        }
    }

    return $data;
}

add_filter('filter_product_by_category', 'filter_Product_By_Category', 10, 2);

function get_filter_products_by_category($request)
{
    if (validate_authorization_header()) {
        return apply_filters(
            'filter_product_by_category',
            wl_product_list(),
            $request
        );
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/**
 * get Filter products by @tag
 *
 * Function Name: filter_Product_By_Tag
 *
 *
 */

function filter_Product_By_Tag($products, $request)
{
    $params = $request['tag'];
    $data = [];
    $i = 0;

    foreach ($products as $product) {
        $tags = $product['tag'];
        if (is_array($tags) && !empty($tags)) {
            foreach ($tags as $tag) {
                if ($tag->name == $params) {
                    $data[$i] = $product;
                    $i++;
                    break;
                }
            }
        }
    }

    return $data;
}

add_filter('filter_product_by_tag', 'filter_Product_By_Tag', 10, 2);

function get_filter_products_by_tag($request)
{
    if (validate_authorization_header()) {
        return apply_filters(
            'filter_product_by_tag',
            wl_product_list(),
            $request
        );
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/**
 * get Filter products by date before
 *
 * Function Name: filter_Product_By_Date_Before
 *
 *
 */

function filter_Product_By_Date_Before($products, $request)
{
    $params = $request->get_params();
    $date = $params['date'];

    $data = [];
    if (!isset($date)) {
        return $data;
    }
    $i = 0;

    foreach ($products as $product) {
        if (get_post_timestamp($product['id']) < strtotime($date)) {
            $data[$i] = $product;
            $i++;
        }
    }
    if ($data == []) {
        return "no product after this time";
    }

    return $data;
}

add_filter(
    'filter_product_by_date_before',
    'filter_Product_By_Date_Before',
    10,
    2
);

function get_filter_products_by_date_before($request)
{
    if (validate_authorization_header()) {
        return apply_filters(
            'filter_product_by_date_before',
            wl_product_list(),
            $request
        );
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/**
 * get Filter products by date after
 *
 * Function Name: filter_Product_By_Date_After
 *
 *
 */

function filter_Product_By_Date_After($products, $request)
{
    $params = $request->get_params();
    $date = $params['date'];

    $data = [];
    if (!isset($date)) {
        return $data;
    }
    $i = 0;

    foreach ($products as $product) {
        if (get_post_timestamp($product['id']) > strtotime($date)) {
            $data[$i] = $product;
            $i++;
        }
    }
    if ($data == []) {
        return "no product after this time";
    }
    return $data;
}

add_filter(
    'filter_product_by_date_after',
    'filter_Product_By_Date_After',
    10,
    2
);

function get_filter_products_by_date_after($request)
{
    if (validate_authorization_header()) {
        return apply_filters(
            'filter_product_by_date_after',
            wl_product_list(),
            $request
        );
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/**
 * get Filter products by OnSale
 *
 * Function Name: filter_Product_By_OnSale
 *
 *
 */

function filter_Product_By_OnSale($products)
{
    $data = [];
    $i = 0;

    foreach ($products as $product) {
        $price = $product['price'];
        $sale_price = $product['sale_price'];
        if ($price == $sale_price) {
            $data[$i] = $product;
            $i++;
        }
    }
    return $data;
}

add_filter('filter_product_by_onsale', 'filter_Product_By_onSale', 10, 2);

function get_filter_products_by_onsale($request)
{
    if (validate_authorization_header()) {
        return apply_filters(
            'filter_product_by_onsale',
            wl_product_list(),
            $request
        );
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

/*======================================  Filter for Products  - End ================================================ */

/**
 * Custom Endpoint Add action
 */
add_action('rest_api_init', function () {
    register_rest_route('medpick-api', 'product', [
        'methods' => 'GET',
        'callback' => 'wl_product_list'
    ]);

    register_rest_route('medpick-api', 'product/(?P<post_id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'wl_product_detail_by_id'
    ));

    register_rest_route('medpick-api', 'product/(?P<slug>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'wl_product_detail'
    ));

    register_rest_route(
        'medpick-api',
        'products_category/(?P<category>[a-zA-Z0-9-]+)',
        [
            'methods' => 'GET',
            'callback' => 'get_filter_products_by_category'
        ]
    );

    register_rest_route('medpick-api', 'products_tag/(?P<tag>[a-zA-Z0-9-]+)', [
        'methods' => 'GET',
        'callback' => 'get_filter_products_by_tag'
    ]);

    register_rest_route(
        'medpick-api',
        'products_before/(?P<date>[a-zA-Z0-9-]+)',
        [
            'methods' => 'GET',
            'callback' => 'get_filter_products_by_date_before'
        ]
    );

    register_rest_route(
        'medpick-api',
        'products_after/(?P<date>[a-zA-Z0-9-]+)',
        [
            'methods' => 'GET',
            'callback' => 'get_filter_products_by_date_after'
        ]
    );

    register_rest_route('medpick-api', 'products_onsale', [
        'methods' => 'GET',
        'callback' => 'get_filter_products_by_onsale'
    ]);
});
/*====================================== Product API - End ================================================ */
