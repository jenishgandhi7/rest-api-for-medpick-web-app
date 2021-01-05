<?php

/*====================================== Add Product API- Start ================================================ */
/**
 * Create new page by using rest api
 *
 * Function Name: wl_addProduct
 *
 *
 */
function wl_addProduct(WP_REST_Request $request)
{
    if (validate_authorization_header()) {
        $arr_request = json_decode($request->get_body(), true);
        $post = array(
            'ID' => $arr_request['ID'], //Are you updating an existing product?
			'post_title' => $arr_request['post_title'], //The title of your Product.
			'post_name' => $arr_request['post_name'], // The name for your product
            'post_content' => $arr_request['post_content'], //Sets the template for the page.
            'post_excerpt' => $arr_request['post_excerpt'], // 'closed' means no comments.
            'post_status' => $arr_request['post_status'], //Ping status?
            'pinged' => $arr_request['pinged'], //?
            'post_author' => $arr_request['post_author'], //The user ID number of the author.
            'post_date' => $arr_request['post_date'], //The time post was made.
            'post_date_gmt' => $arr_request['post_date_gmt'], //The time product was made, in GMT.
            'post_parent' => $arr_request['post_parent'], //Sets the parent of the new product.
            'post_status' => $arr_request['post_status'], //Set the status of the new product.
            'post_type' => $arr_request['post_type'], //Sometimes you want to product.
			'comment_status' => $arr_request['comment_status'], //Sometimes you want to product a product.

			'meta_input' => array(
			 '_thumbnail_id' => $arr_request['thumbnail_id'], //Sometimes you want to post a product.
			 '_product_image_gallery' => $arr_request['product_image_gallery'], //Sometimes you want to post a page.
			'max_quantity' => $arr_request['max_quantity'],
            'min_quantity' => $arr_request['min_quantity'],

			'_price' => $arr_request['price'],
			'_regular_price' => $arr_request['regular_price'],
			'_sale_price' => $arr_request['sale_price'],
			'_sku' => $arr_request['sku'],
			'_stock_status' => $arr_request['stock_status'],
			'product_role_based_price_Hospital' => $arr_request['price_Hospital'],
			'product_role_based_price_Dealer' => $arr_request['price_Dealer'],
			'product_role_based_price_administrator' => $arr_request['price_Administrator'],
			'product_role_based_price_customer' => $arr_request['price_Customer'],
            '_manage_stock' => $arr_request['manage_stock'],
            '_stock' => $arr_request['stock'],
			'_low_stock_amount' => $arr_request['low_stock_amount'],
			'_weight' => $arr_request['weight'], 
			'_length' => $arr_request['product_length'], 
			'_width' => $arr_request['product_width'],
			'_height' => $arr_request['product_height'],
			'product_shipping_class' => $arr_request['product_shipping_class'],
			'_purchase_note' => $arr_request['purchase_note'],
			'_wcfmmp_profile_id' => $arr_request['wcfmmp_profile_id'],
			'menu_order' => $arr_request['menu_order']
			)
			 
        );
		

		$pid = wp_insert_post($post);
		
		if ( ! empty( $arr_request['post_category'] ) )
        wp_set_post_terms($pid, $arr_request['post_category'], 'product_cat' );
		

		if ( ! empty( $arr_request['tags'] ) )
        wp_set_post_terms($pid, $arr_request['tags'], 'product_tag' );
		
		$args = [
            'ID' => $pid,
            'post_author' => $arr_request['vendor_id']
        ];
		
	    wp_update_post( $args, true );

        return get_post($pid);
    } else {
        return [
            'success' => false,
            'message' => 'Authorization failed.'
        ];
    }
}

add_action('rest_api_init', function () {
    register_rest_route('medpick-api', 'createProduct', [
        'methods' => 'POST',
        'callback' => 'wl_addProduct'
    ]);
});

/*====================================== Add Product API- End ================================================ */
