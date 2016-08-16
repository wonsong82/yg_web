<?php
/**
 * Template Name: Api
 *
 */

//define('DEBUG', true);
//dd('API');
//dd('<pre>', true);


$args = array(
	'sort_order' => 'asc',
	'sort_column' => 'post_title',
	'hierarchical' => 1,
	'exclude' => '',
	'include' => '',
	'meta_key' => '',
	'meta_value' => '',
	'authors' => '',
	'child_of' => 0,
	'parent' => -1,
	'exclude_tree' => '',
	'number' => '',
	'offset' => 0,
	'post_type' => 'page',
	'post_status' => 'publish'
);

$pages = get_pages($args); 

$requestedUri = $_SERVER['REQUEST_URI'];
$method = substr(trim($requestedUri), 5);


if(function_exists($method)){
    $data = $method();

    setResponseHeader(200);
    header('Content-type: application/json');
    echo json_encode($data);
}else{

    setResponseHeader(404);
    echo json_encode(null);

}


function getProductsInCart(){
    global $woocommerce;
    $items = $woocommerce->cart->get_cart();

    $cart_data = array();

    foreach($items as $item => $values) {
        $_product = $values['data']->post;
        $product_id = $values['product_id'];

        $tn_1x1 = get_field('thumbnail_1x1' , $product_id);
        $product_title = $_product->post_title;
        $price = get_post_meta($product_id , '_price', true);
        $quantity = $values['quantity'];
        $line_total = $values['line_subtotal'];

        $is_music = get_post_meta($product_id , '_downloadable', true);

    }
}

function addProductsToCart($data){

    $product_id = $data['product_id'];
    $variation_id = isset($data['variation_id']) ? $data['variation_id'] : 0;
    $qty = $data['qty'];


    WC()->cart->add_to_cart($product_id, $qty , $variation_id);

    //After Add Items to Cart, Front needs updates cart information.
    return getProductsInCart();
}


// 2th DONE -------------- 8/15/2016
function getArtists(){

    $artist_posts = get_posts([
        'post_type' => 'artist',
        'post_status' => 'publish',
        'posts_per_page' => -1
    ]);

    $artist_data = array();

    foreach($artist_posts as $key => $post){
        $fields = get_fields($post->ID);

        $artist_data[$post->ID]['id'] = $post->ID;
        $artist_data[$post->ID]['name'] = $fields['artist_name'];
        $artist_data[$post->ID]['urlFriendlyName'] = getFriendlyUrl('/artist/', $post);
        $artist_data[$post->ID]['bg'] = $fields['artist_image'];
        $artist_data[$post->ID]['themeColor'] = $fields['theme_color'];
        $artist_data[$post->ID]['textColor'] = $fields['font_color'];
        $artist_data[$post->ID]['facebook_link'] = $fields['facebook_link'];
        $artist_data[$post->ID]['instagram_link'] = $fields['instagram_link'];
    }

    return $artist_data;
}

// 2th DONE -------------- 8/15/2016
function getTours(){

	$tour_posts = get_posts([
		'post_type' => 'tour',
        'post_status' => 'publish',
        'posts_per_page' => -1
	]);

	$tour_data = array();

	foreach($tour_posts as $key => $post){
		$fields = get_fields($post->ID);

        /** Tour Post Data */
        $tour_data[$post->ID]['id'] = $post->ID;
        $tour_data[$post->ID]['url_friendly_name'] = getFriendlyUrl('/tour/',$post);
        $tour_data[$post->ID]['post_title'] = $post->post_title;
        $tour_data[$post->ID]['post_content'] = $post->post_content;
        $tour_data[$post->ID]['post_date'] = convertDateFormat($post->post_date);


        /** Image Data */
        $tour_data[$post->ID]['main_image'] = $fields['main_image'];
        $tour_data[$post->ID]['thumb_1x1'] = $fields['thumbnail'];
        $tour_data[$post->ID]['thumb_3x2'] = $fields['thumbnail_3x2'];
        $tour_data[$post->ID]['thumb_2x1'] = $fields['thumbnail_2x1'];


        /** Tour Main Data */
        $tour_data[$post->ID]['subtitle'] = $fields['subtitle'];
        $tour_data[$post->ID]['start_date'] = convertDateFormat($fields['start_date']);
        $tour_data[$post->ID]['end_date'] = convertDateFormat($fields['end_date']);
        $tour_data[$post->ID]['tour_url'] = $fields['tour_url'];
        $tour_data[$post->ID]['artist_id'] = $fields['artist'][0];

        /** Tour Schedule Data Array */
        $index = 0;
        foreach($fields['tour_schedule'] as $schedule){
            $tour_data[$post->ID]['tour_schedule'][$index]['tour_date'] = convertDateFormat($schedule['tour_date']);
            $tour_data[$post->ID]['tour_schedule'][$index]['location'] = $schedule['location'];
            $tour_data[$post->ID]['tour_schedule'][$index]['event_time'] = $schedule['event_time'];
            $tour_data[$post->ID]['tour_schedule'][$index]['ticket_link'] = $schedule['ticket_link'];
            $tour_data[$post->ID]['tour_schedule'][$index]['ticket_availability'] = $schedule['ticket_availability'];
            $index++;
        }

	}

	return $tour_data;
}


// 2th DONE -------------- 8/15/2016
function getEvents(){

	$event_posts = get_posts([
		'post_type' => 'event',
        'post_status' => 'publish',
        'posts_per_page' => -1
	]);

	$event_data = array();

	foreach($event_posts as $key => $post){
		$fields = get_fields($post->ID);

        $event_data[$post->ID]['id'] = $post->ID;
        $event_data[$post->ID]['post_title'] = $post->post_title;
        $event_data[$post->ID]['post_content'] = $post->post_content;
        $event_data[$post->ID]['post_date'] = convertDateFormat($post->post_date);
        $event_data[$post->ID]['main_image'] = $fields['main_image'];
        $event_data[$post->ID]['thumb_1x1'] = $fields['thumbnail'];
        $event_data[$post->ID]['thumb_3x2'] = $fields['thumbnail_3x2'];
        $event_data[$post->ID]['excerpt'] = $fields['short_description'];
        $event_data[$post->ID]['url_friendly_name'] = getFriendlyUrl('/event/', $post);
        $event_data[$post->ID]['artist_id'] = $fields['artist'][0];
        $event_data[$post->ID]['related_event'] = $fields['related_event'] ?: [];

	}

	return $event_data;

}


// 2th DONE -------------- 8/15/2016
function getMusics(){


    /** ALBUM DATA */

    $album_posts = get_posts([
        'post_type' => 'album',
        'post_status' => 'publish',
        'posts_per_page' => -1
    ]);

    $album_data = array();

    foreach($album_posts as $post){

        $fields = get_fields($post->ID);

        $album_data['albums'][$post->ID]['id'] = $post->ID;
        $album_data['albums'][$post->ID]['post_title'] = $post->post_title;
        $album_data['albums'][$post->ID]['post_content'] = $post->post_content;
        $album_data['albums'][$post->ID]['post_date'] = convertDateFormat($post->post_date);
        $album_data['albums'][$post->ID]['url_friendly_name'] = getFriendlyUrl('/album/',$post);

        $album_data['albums'][$post->ID]['thumb_1x1'] = $fields['thumbnail'];
        $album_data['albums'][$post->ID]['cover_image'] = $fields['cover_image'];

        $album_data['albums'][$post->ID]['album_url'] = $fields['album_url'];
        $album_data['albums'][$post->ID]['album_release_date'] = convertDateFormat($fields['album_release_date']);

        $album_data['albums'][$post->ID]['artist_id'] = $fields['artist'][0];
        $album_data['albums'][$post->ID]['related_album'] = $fields['related_album'] ?: [];
    }



    /** MUSIC DATA */

    $music_posts = get_posts([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_downloadable',
                'value' => 'yes'
            )
        )
    ]);

    foreach($music_posts as $music_post){

        $music_fields = get_post_meta($music_post->ID);
        $music_custom_fields = get_fields($music_post->ID);

        $album_data['musics'][$music_post->ID]['id'] = $music_post->ID;
        $album_data['musics'][$music_post->ID]['post_title'] = $music_post->post_title;
        $album_data['musics'][$music_post->ID]['post_content'] = $music_post->post_content;
        $album_data['musics'][$music_post->ID]['post_date'] = convertDateFormat($music_post->post_date);


        $album_data['musics'][$music_post->ID]['_regular_price'] = $music_fields['_regular_price'][0];
        $album_data['musics'][$music_post->ID]['_sale_price'] = $music_fields['_sale_price'][0] ?: null;

        $album_data['musics'][$music_post->ID]['album_id'] = $music_custom_fields['album'][0];
        $album_data['musics'][$music_post->ID]['sample_link'] = $music_custom_fields['sample_link'];
        $album_data['musics'][$music_post->ID]['youtube_link'] = $music_custom_fields['youtube_link'];
        $album_data['musics'][$music_post->ID]['product_type'] = $music_custom_fields['music_product_type'];

    }


    /** HOT TRACK DATA */

    $hot_tracks = get_option('sub_hot_track_enable');
    $index = 0;

    foreach($hot_tracks as $key => $value){
        $album_data['hot_tracks'][$index] = $key;
        $index++;
    }

    return $album_data;
}


// 2th DONE -------------- 8/15/2016
function getBlogs(){

    $blog_posts = get_posts([
        'post_type' => 'blog',
        'post_status' => 'publish',
        'posts_per_page' => -1
    ]);

    $blog_data = array();

    foreach($blog_posts as $key => $post){

        $fields = get_fields($post->ID);

        $blog_data['posts'][$post->ID]['id'] = $post->ID;
        $blog_data['posts'][$post->ID]['post_title'] = $post->post_title;
        $blog_data['posts'][$post->ID]['url_friendly_name'] = getFriendlyUrl('/blog/',$post);
        $blog_data['posts'][$post->ID]['excerpt'] = $post->post_excerpt;
        $blog_data['posts'][$post->ID]['post_content'] = $post->post_content;
        $blog_data['posts'][$post->ID]['post_date'] = convertDateFormat($post->post_date);
        $blog_data['posts'][$post->ID]['related_blog'] = $fields['related_blog'] ?: [];
        $blog_data['posts'][$post->ID]['main_image'] = $fields['main_image'];
        $blog_data['posts'][$post->ID]['thumb_2x1'] = $fields['thumbnail_2x1'];
        $blog_data['posts'][$post->ID]['thumb_3x2'] = $fields['thumbnail_3x2'];
    }


    $hot_blogs = get_option('sub_hot_blog_enable');
    $index = 0;

    foreach($hot_blogs as $key => $value){
        $blog_data['hot_posts'][$index] = $key;
        $index++;
    }

    return $blog_data;
}


// 2th DONE -------------- 8/15/2016
function getShops(){

    $shop_posts = get_posts([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_downloadable',
                'value' => 'no'
            )
        )
    ]);

    $shop_data = array();

    foreach($shop_posts as $key => $post){

        $fields = get_post_meta($post->ID);
        $custom_fields = get_fields($post->ID);

        $plan = wc_get_product($post->ID);

        $terms = get_the_terms($post->ID, 'product_cat');

        $terms_data = array();
        foreach($terms as $term_key => $term_value){
            $terms_data[$term_key] = $term_value->term_id;
        }


        $shop_data['products'][$post->ID]['id'] = $post->ID;
        $shop_data['products'][$post->ID]['post_title'] = $post->post_title;
        $shop_data['products'][$post->ID]['post_content'] = $post->post_content;
        $shop_data['products'][$post->ID]['post_date'] = convertDateFormat($post->post_date);
        $shop_data['products'][$post->ID]['url_friendly_name'] = getFriendlyUrl('/product/', $post);

        $shop_data['products'][$post->ID]['product_type'] = $plan->product_type;
        $shop_data['products'][$post->ID]['cat_IDs'] = $terms_data;

        $shop_data['products'][$post->ID]['thumb_1x1'] = $custom_fields['thumbnail_2x2'];
        $shop_data['products'][$post->ID]['thumb_2x1'] = $custom_fields['thumbnail_2x1'];
        $shop_data['products'][$post->ID]['thumb_1x2'] = $custom_fields['thumbnail_1x2'];

        $images_ids = $plan->get_gallery_attachment_ids();

        $index = 0;
        foreach( $images_ids as $image_id){
            $shop_data['products'][$post->ID]['image'][$index] = wp_get_attachment_url($image_id);
            $index++;
        }


        /** Custom Field Not in WooCommerce */

        $shop_data['products'][$post->ID]['artist_id'] = $custom_fields['artist'][0];
        $shop_data['products'][$post->ID]['related'] = count($plan->get_cross_sells()) > 0 ? $plan->get_cross_sells() : [];


        if($plan->product_type == 'variable'){
            $variations = $plan->get_available_variations();
            $default = $plan->get_variation_default_attributes();

            $default_att = array();
            foreach($default as $key_att => $item){
                $default_att['attribute_'.$key_att] = $item;
            }

            foreach($variations as $k => $variation){
                $shop_data['products'][$post->ID]['variation'][$k]['variation_id'] = $variation['variation_id'];
                $shop_data['products'][$post->ID]['variation'][$k]['display_regular_price'] = $variation['display_regular_price'];
                $shop_data['products'][$post->ID]['variation'][$k]['display_price'] = $variation['display_price'];
                $shop_data['products'][$post->ID]['variation'][$k]['sku'] = $variation['sku'];



                $attributes = $variation['attributes'];

                $att_index = 0;
                $is_default = true;
                foreach($attributes as $att_key => $attribute){
                    $shop_data['products'][$post->ID]['variation'][$k]['attribute'][$att_index]['key'] = $att_key;
                    $shop_data['products'][$post->ID]['variation'][$k]['attribute'][$att_index]['value'] = $attribute;

                    if($default_att[$att_key] != $attribute){
                        $is_default = false;
                    }

                    $att_index++;
                }

                $shop_data['products'][$post->ID]['variation'][$k]['id_default'] = $is_default;
            }
        }else{
            $shop_data['products'][$post->ID]['_regular_price'] = $fields['_regular_price'][0];
            $shop_data['products'][$post->ID]['_sale_price'] = $fields['_sale_price'][0];
            $shop_data['products'][$post->ID]['_sku'] = $fields['_sku'][0];
        }

    }


    $categories =  get_categories([
        'taxonomy'     => 'product_cat',
        'orderby'      => 'id',
        'show_count'   => 0,
        'pad_counts'   => 0,
        'hierarchical' => 1,
        'title_li'     => '',
        'hide_empty'   => 0
    ]);

    foreach($categories as $key => $category){
        $shop_data['categories'][$category->cat_ID]['cat_ID'] = $category->cat_ID;
        $shop_data['categories'][$category->cat_ID]['name'] = $category->name;
    }


    return $shop_data;

}

function getPromotions(){

    $promotions = get_option('main_contents');

    $promotions_data = array();
    $index = 0;

    foreach($promotions as $key => $value){
        $promotions_data[$index]['id'] = $key;
        $promotions_data[$index]['post_type'] = $value;

        $index++;
    }

    return $promotions_data;
}


function convertDateFormat($date){

    return date("m/d/Y", strtotime($date));

}

function getFriendlyUrl($type, $post){

    $permalink = get_permalink($post);
    return str_replace($type, '', parse_url($permalink)['path']);
}


function dd($dumpData, $echo = false){
	if(DEBUG) {
		if($echo) echo $dumpData;
		else var_dump($dumpData);
	}
}

function setResponseHeader($code){
	$code = (int)$code;
	switch ($code) {
		case 100: $text = 'Continue'; break;
		case 101: $text = 'Switching Protocols'; break;
		case 200: $text = 'OK'; break;
		case 201: $text = 'Created'; break;
		case 202: $text = 'Accepted'; break;
		case 203: $text = 'Non-Authoritative Information'; break;
		case 204: $text = 'No Content'; break;
		case 205: $text = 'Reset Content'; break;
		case 206: $text = 'Partial Content'; break;
		case 300: $text = 'Multiple Choices'; break;
		case 301: $text = 'Moved Permanently'; break;
		case 302: $text = 'Moved Temporarily'; break;
		case 303: $text = 'See Other'; break;
		case 304: $text = 'Not Modified'; break;
		case 305: $text = 'Use Proxy'; break;
		case 400: $text = 'Bad Request'; break;
		case 401: $text = 'Unauthorized'; break;
		case 402: $text = 'Payment Required'; break;
		case 403: $text = 'Forbidden'; break;
		case 404: $text = 'Not Found'; break;
		case 405: $text = 'Method Not Allowed'; break;
		case 406: $text = 'Not Acceptable'; break;
		case 407: $text = 'Proxy Authentication Required'; break;
		case 408: $text = 'Request Time-out'; break;
		case 409: $text = 'Conflict'; break;
		case 410: $text = 'Gone'; break;
		case 411: $text = 'Length Required'; break;
		case 412: $text = 'Precondition Failed'; break;
		case 413: $text = 'Request Entity Too Large'; break;
		case 414: $text = 'Request-URI Too Large'; break;
		case 415: $text = 'Unsupported Media Type'; break;
		case 500: $text = 'Internal Server Error'; break;
		case 501: $text = 'Not Implemented'; break;
		case 502: $text = 'Bad Gateway'; break;
		case 503: $text = 'Service Unavailable'; break;
		case 504: $text = 'Gateway Time-out'; break;
		case 505: $text = 'HTTP Version not supported'; break;
		default:
			exit('Unknown http status code "' . htmlentities($code) . '"');
			break;
	}

	$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
	header("{$protocol} {$code} {$text}");
	$GLOBALS['http_response_code'] = $code;
}


