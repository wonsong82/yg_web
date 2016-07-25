<?php
/**
 * Template Name: Api
 *
 */

define('DEBUG', false);

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

dd('API');
dd('<pre>', true);


echo '<pre>';
$requestedUri = $_SERVER['REQUEST_URI'];

$method = substr(trim($requestedUri), 5);

function getArtists(){
  echo 'function';
}

if(function_exists($method))
  $method();

exit;


// ############################################################################################################################  All Tours


//foreach($posts as $post){
//
//	$fields = get_fields();
//
//	$tn_3x1 = $fields['thumbnail_image_3x1'];
//	$tn_3x2 = $fields['thumbnail_image_3x2'];
//	$tn = $fields['thumbnail_image'];
//	$mainImage = $fields['main_image'];
//
//	$subtitle = $fields['subtitle'];
//	$start_date = $fields['start_date'];
//	$end_date = $fields['end_date'];
//	$tour_url = $fields['tour_url'];
//	$guest = $fields['guest'];
//
//
//	$artist = $fields['artist'];
//
//	//array
//	$tour_schedule = $fields['tour_schedule'];
//
//	var_dump($subtitle, $start_date , $end_date , $tour_url , $guest , $artist[0]->post_title);
//}






//global $woocommerce;
//
//$items = $woocommerce->cart->get_cart();
//
//echo '<pre>';
//
//foreach($items as $item){
//    var_dump($item);
//}

echo '<pre>';


$cart = getProductsInCart();

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

$cart_add = addProductsToCart();

function addProductsToCart(){

    WC()->cart->add_to_cart(134, 1);

    //After Add Items to Cart, Front needs updates cart information.
    return getProductsInCart();
}





$artist_data = getAllArtists();

dd('<h2>' . 'Artists' . '</h2>', true);
dd($artist_data);


$tour_data = getAllTours();

dd('<h2>' . 'Tours' . '</h2>', true);
dd($tour_data);


$event_data = getAllEvents();

dd('<h2>' . 'Events' . '</h2>',true);
dd($event_data);


$album_data = getAllAlbums();

dd('<h2>' . 'Albums' . '</h2>',true);
dd($album_data);


$blog_data = getAllBlog();





$shop_data = getAllShops();

//setResponseHeader(200);
//header('Content-type: application/json');
//echo json_encode($data);







function getAllArtists(){
    $artist_posts = get_posts([
        'post_type' => 'artist'
    ]);

    $artist_data = array();

    foreach($artist_posts as $key => $post){
        $fields = get_fields($post->ID);

        $artist_data[$key]['id'] = $post->ID;
        $artist_data[$key]['post_title'] = $post->post_title;
        $artist_data[$key]['post_content'] = $post->post_content;
        $artist_data[$key]['post_date'] = $post->post_date;

        $artist_data[$key]['artist_name'] = $fields['artist_name'];
        $artist_data[$key]['artist_image'] = $fields['artist_image'];
        $artist_data[$key]['theme_color'] = $fields['theme_color'];
        $artist_data[$key]['font_color'] = $fields['font_color'];

        $artist_data[$key]['facebook_link'] = $fields['facebook_link'];
        $artist_data[$key]['instagram_link'] = $fields['instagram_link'];
    }

    return $artist_data;
}

function getAllTours(){

	$tour_posts = get_posts([
		'post_type' => 'tour'
	]);

	$tour_data = array();

	foreach($tour_posts as $key => $post){
		$fields = get_fields($post->ID);

		$tour_data[$key]['id'] = $post->ID;
		$tour_data[$key]['post_title'] = $post->post_title;
		$tour_data[$key]['post_content'] = $post->post_content;
		$tour_data[$key]['post_date'] = $post->post_date;


		/** Image Data */
		$tour_data[$key]['main_image'] = $fields['main_image'];
		$tour_data[$key]['thumbnail_image'] = $fields['thumbnail_image'];
		$tour_data[$key]['thumbnail_image_3x1'] = $fields['thumbnail_image_3x1'];
		$tour_data[$key]['thumbnail_image_3x2'] = $fields['thumbnail_image_3x2'];



		$tour_data[$key]['subtitle'] = $fields['subtitle'];
		$tour_data[$key]['start_date'] = $fields['start_date'];
		$tour_data[$key]['end_date'] = $fields['end_date'];
		$tour_data[$key]['tour_url'] = $fields['tour_url'];
		$tour_data[$key]['guest'] = $fields['guest'];
		$tour_data[$key]['artist'] = $fields['artist'][0];

		/** Tour Schedule - Array*/

		$tour_data[$key]['tour_schedule'] = $fields['tour_schedule'];

	}

	return $tour_data;
}


function getAllEvents(){

	$event_posts = get_posts([
		'post_type' => 'event'
	]);

	$event_data = array();

	foreach($event_posts as $key => $post){
		$fields = get_fields($post->ID);

		$tour_data[$key]['id'] = $post->ID;
		$tour_data[$key]['post_title'] = $post->post_title;
		$tour_data[$key]['post_content'] = $post->post_content;
		$tour_data[$key]['post_date'] = $post->post_date;

		$event_data[$key]['main_image'] = $fields['main_image'];
		$event_data[$key]['thumbnail_2x2'] = $fields['thumbnail_2x2'];

		$event_data[$key]['subtitle'] = $fields['subtitle'];
		$event_data[$key]['short_description'] = $fields['short_description'];

		$event_data[$key]['artist_id'] = $fields['artist'][0];

	}

	return $event_data;

}

function getAllAlbums(){

    $album_posts = get_posts([
        'post_type' => 'album'
    ]);

    $album_data = array();

    foreach($album_posts as $key => $post){

        $fields = get_fields($post->ID);

        $album_data[$key]['id'] = $post->ID;
        $album_data[$key]['post_title'] = $post->post_title;
        $album_data[$key]['post_content'] = $post->post_content;
        $album_data[$key]['post_date'] = $post->post_date;

        $album_data[$key]['subtitle'] = $fields['subtitle'];
        $album_data[$key]['cover_image'] = $fields['cover_image'];
        $album_data[$key]['album_url'] = $fields['album_url'];
        $album_data[$key]['album_release_date'] = $fields['album_release_date'];
        $album_data[$key]['artist_id'] = $fields['artist'][0];
    }

    return $album_data;
}

function getAllBlog(){

    echo '<pre>';

    $blog_posts = get_posts([
        'post_type' => 'blog'
    ]);

    $blog_data = array();

    foreach($blog_posts as $key => $post){

        $fields = get_fields($post->ID);

        $blog_data[$key]['id'] = $post->ID;
        $blog_data[$key]['post_title'] = $post->post_title;
        $blog_data[$key]['post_content'] = $post->post_content;
        $blog_data[$key]['post_date'] = $post->post_date;


        $blog_data[$key]['related_blog'] = $fields['related_blog'];
        $blog_data[$key]['main_image'] = $fields['main_image'];

    }

    return $blog_data;
}



function getAllShops(){

    echo '<pre>';

    $shop_posts = get_posts([
        'post_type' => 'product',
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

        $shop_data[$key]['id'] = $post->ID;
        $shop_data[$key]['post_title'] = $post->post_title;
        $shop_data[$key]['post_content'] = $post->post_content;
        $shop_data[$key]['post_date'] = $post->post_date;


        /** Custom Field Not in WooCommerce */

        $shop_data[$key]['thumbnail_1x1'] = $fields['thumbnail_1x1'][0];
        $shop_data[$key]['thumbnail_1x2'] = $fields['thumbnail_1x2'][0];
        $shop_data[$key]['thumbnail_2x1'] = $fields['thumbnail_2x1'][0];
        $shop_data[$key]['thumbnail_2x2'] = $fields['thumbnail_2x2'][0];
        $shop_data[$key]['artist_id'] = $fields['artist'][0];



        /** Custom Field Not in WooCommerce */
        $shop_data[$key]['_regular_price'] = $fields['_regular_price'][0];
        $shop_data[$key]['_sale_price'] = $fields['_sale_price'][0];
        $shop_data[$key]['_sku'] = $fields['_sku'][0];

    }

    return $shop_data;

}

function getAllMusic(){

}




// ############################################################################################################################ All Physical Goods

//$shop_query = array(
//	'post_type'		=> 'product',
//	'meta_query'		=> array(
//		array(
//			'key' => '_downloadable',
//			'value' => 'no'
//		)
//	)
//);
//
//$shop_posts = get_posts($shop_query);
//
//foreach($shop_posts as $post){
//
//	echo '<h3>' . 'CURRENT SHOP POST' . '</h3>';
//	var_dump($post);
//
//	$fields = get_fields($post->ID);
//
//	echo '<h3>' . 'FIELDS OF CURRENT SHOP POST' . '</h3>';
//	var_dump($fields);
//}



// ############################################################################################################################ Artist Page

//$artist_id = 129;
//
//$artist = get_post($artist_id);
//
//echo '<h3>' . 'current ARTIST' . '</h3>';
//var_dump($artist);
//
//$artist_fields = get_fields($artist_id);
//echo '<h3>' . 'current ARTIST fields' . '</h3>';
//var_dump($artist_fields);
//
//
///**  ############################ TOUR QUERY ########################### */
//
//$tour_query = array(
//	'post_type'		=> 'tour',
//	'meta_query'		=> array(
//		array(
//			'key' => 'artist',
//			'value' => '"' . $artist_id . '"',
//			'compare' => 'LIKE'
//		)
//	)
//);
//
//$tour_posts = get_posts($tour_query);
//
//echo '<h3>' . 'All Tour Posts related to current ARTIST' . '</h3>';
//var_dump($tour_posts);
//
//foreach($tour_posts as $tour_post){
//
//	$tour_fields = get_fields($tour_post->ID);
//
//	echo '<h3>' . 'All Fields related to current TOUR' . '</h3>';
//	var_dump($tour_fields);
//}
//
//
///**  ############################ EVENT QUERY ########################### */
//
//$event_query = array(
//	'post_type'		=> 'event',
//	'meta_query'		=> array(
//		array(
//			'key' => 'artist',
//			'value' => '"' . $artist_id . '"',
//			'compare' => 'LIKE'
//		)
//	)
//);
//
//echo '<h3>' . 'All EVENT Posts related to current ARTIST' . '</h3>';
//$event_posts = get_posts($event_query);
//var_dump($event_posts);
//
//foreach($event_posts as $event_post){
//	$event_fields = get_fields($event_post->ID);
//
//	echo '<h3>' . 'All Fields related to current EVENT' . '</h3>';
//	var_dump($event_fields);
//}
//
//
//
///**  ############################ ALBUM QUERY ########################### */
//
//$album_query = array(
//	'post_type'		=> 'album',
//	'meta_query'		=> array(
//		array(
//			'key' => 'artist',
//			'value' => '"' . $artist_id . '"',
//			'compare' => 'LIKE'
//		)
//	)
//);
//
//echo '<h3>' . 'All ALBUM Posts related to current ARTIST' . '</h3>';
//$album_posts = get_posts($album_query);
//var_dump($album_posts);
//
//foreach($album_posts as $album_post){
//	$album_fields = get_fields($album_post->ID);
//
//	echo '<h3>' . 'All Fields related to current Album' . '</h3>';
//	var_dump($album_fields);
//
//	/**  ############################ SONG QUERY that belongs to current Album ########################### */
//
//	$music_query = array(
//		'post_type'		=> 'product',
//		'meta_query'		=> array(
//			array(
//				'key' => 'album',
//				'value' => '"' . $album_post->ID . '"',
//				'compare' => 'LIKE'
//			)
//		)
//	);
//
//	$music_posts = get_posts($music_query);
//
//	echo '<h3>' . 'All MUSIC related to current Album' . '</h3>';
//
//	var_dump($music_posts);
//
//	foreach($music_posts as $music_post){
//
//		$music_fields = get_fields($music_post->ID);
//		echo '<h3>' . 'All MUSIC FIELDS related to current MUSIC(Product)' . '</h3>';
//
//		var_dump($music_fields);
//	}
//
//}





function dd($dumpData, $echo = false){
	if(DEBUG) {
		if($echo) echo $dumpData;
		else var_dump($dumpData);
	}
}

$data = [
	'name' => 'Won Song',
	'id' => 10
];



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

//setResponseHeader(200);
//header('Content-type: application/json');
//echo json_encode($data);
