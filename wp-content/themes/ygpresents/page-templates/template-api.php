<?php
/**
 * Template Name: Api
 *
 */

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

require_once (ABSPATH.'wp-content/plugins/ygpresent/class/EmailSubscriber.php');

if(strstr($method, 'generateCacheAll')){
  foreach(['artist', 'event', 'tour', 'album', 'blog',' product'] as $type){
    generateCache($type);
  }
  exit;
}


if(strstr($method, 'generateCache')){
  $postType = $_GET['type'];
  generateCache($postType);
  exit;
}


if(function_exists($method)){

  if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $params = json_decode(file_get_contents('php://input'), true);
    $data = $method($params);
    setResponseHeader(200);
    header('Content-type: application/json');
    echo json_encode($data);

  }else{
    // load it from cache
    $cacheFile = ABSPATH . '/wp-cache/' . $method . '.json';
    if(file_exists($cacheFile)){
      setResponseHeader(200);
      header('Content-type: application/json');
      echo file_get_contents($cacheFile);
    }

    else {
      // generate and return
      $data = $method();
      header('Content-type: application/json');
      echo json_encode($data);
    }
  }

}else{
  setResponseHeader(404);
  echo json_encode(null);
}


function generateCache($postType){
  $cacheDir = ABSPATH . '/wp-cache';
  if(!is_dir($cacheDir)) mkdir($cacheDir);

  switch($postType){
    case 'artist':
      file_put_contents($cacheDir . '/getArtists.json', json_encode(getArtists()));

    break;
    case 'event':
      file_put_contents($cacheDir . '/getEvents.json', json_encode(getEvents()));
    break;
    case 'tour':
      file_put_contents($cacheDir . '/getTours.json', json_encode(getTours()));
    break;
    case 'album':
      file_put_contents($cacheDir . '/getMusics.json', json_encode(getMusics()));
    break;
    case 'blog':
      file_put_contents($cacheDir . '/getBlogs.json', json_encode(getBlogs()));
    break;
    case 'product':
      file_put_contents($cacheDir . '/getMusics.json', json_encode(getMusics()));
      file_put_contents($cacheDir . '/getShops.json', json_encode(getShops()));
    break;
    case 'blog-banner':
      file_put_contents($cacheDir . '/getBlogs.json', json_encode(getBlogs()));
    break;

    default:
  }
}


function getProductsInCart(){

  $cart = WC()->instance()->cart;
  $items = $cart->get_cart();

  $cart_data = array();
  $cart_total = 0;

  $product_index = 0;
  $music_index = 0;
  $total_qty = 0;

  foreach($items as $key => $values) {

    $_product = $values['data']->post;
    $product_id = $values['product_id'];

    $is_music = get_post_meta($product_id , '_downloadable', true) == 'yes' ? true : false;

    if($is_music == true){
      $cart_data['music'][$music_index]['cart_id'] = $key;
      $cart_data['music'][$music_index]['product_id'] = $product_id = $values['product_id'];
      $cart_data['music'][$music_index]['variation_id'] = $values['variation_id'];

      $cart_data['music'][$music_index]['product_title'] = $_product->post_title;
      $cart_data['music'][$music_index]['price'] = get_post_meta($product_id , '_price', true);

      $cart_data['music'][$music_index]['quantity'] = $values['quantity'];
      $cart_data['music'][$music_index]['line_total'] = $line_total = $values['line_subtotal'];
      $cart_total += $line_total;

      $albumId = get_field('album', $product_id);
      $thumb = get_field('thumbnail', $albumId[0]);
      $cart_data['music'][$music_index]['thumb'] = $thumb;
      $music_index++;
      $total_qty += $values['quantity'];

    }else{
      $cart_data['product'][$product_index]['cart_id'] = $key;
      $cart_data['product'][$product_index]['product_id'] = $product_id = $values['product_id'];
      $cart_data['product'][$product_index]['variation_id'] = $values['variation_id'];

      $cart_data['product'][$product_index]['product_title'] = $_product->post_title;
      $cart_data['product'][$product_index]['price'] = get_post_meta($product_id , '_price', true);

      $cart_data['product'][$product_index]['quantity'] = $values['quantity'];
      $cart_data['product'][$product_index]['line_total'] = $line_total = $values['line_subtotal'];
      $cart_total += $line_total;

      $thumb = get_field('thumbnail_2x2', $product_id);
      $cart_data['product'][$product_index]['thumb'] = $thumb;
      $product_index++;
      $total_qty += $values['quantity'];
    }
  }
  $cart_data['products_count'] = $total_qty;
  $cart_data['total'] = $cart_total;

  return $cart_data;
}

function addProductsToCart($data){
  //For now, only 1 accepted
  $product_id = $data['product_id'];
  error_log($product_id);
  $variation_id = $data['variation_id'] != null ? $data['variation_id'] : 0;
  error_log($variation_id);
  $qty = $data['qty'];

  WC()->cart->add_to_cart($product_id, $qty , $variation_id);

  return true;
}

function updateProductsInCart($data){

  error_log('start update Product Cart');
  error_log($data['product_id']);
  error_log($data['qty']);

  $cart = WC()->instance()->cart;

  $product_id = $data['product_id'];
  $variation_id = $data['variation_id'];

  $cart_id = $cart->generate_cart_id($product_id, $variation_id);
  $cart_item_id = $cart->find_product_in_cart($cart_id);

  if($cart_item_id){
    $cart->set_quantity($cart_item_id, $data['qty']);
  }

  error_log('update products to cart');

  return true;
}


function deleteProductsInCart($data){

  $cart = WC()->instance()->cart;

  error_log($data['product_id']);

  $product_id = $data['product_id'];
  $variation_id = $data['variation_id'];

  $cart_id = $cart->generate_cart_id($product_id, $variation_id);
  $cart_item_id = $cart->find_product_in_cart($cart_id);

  if($cart_item_id){
    $cart->set_quantity($cart_item_id, 0);
  }

  error_log('remove products to cart');

  return true;
}

function newsletterSignup($data){

    $instance = new EmailSubscriber();
    $instance->addEmail($data['email']);

    return true;
}


// 2th DONE -------------- 8/15/2016
function getArtists(){
    $artist_posts = get_posts([
        'post_type' => 'artist',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'post_date',
        'order' => 'ASC'
    ]);

    $artist_data = array();
    $artist_order = array();

    foreach($artist_posts as $key => $post){
        $fields = get_fields($post->ID);

        array_push($artist_order, $post->ID);

        $artist_data['artists'][$post->ID]['id'] = $post->ID;
        $artist_data['artists'][$post->ID]['name'] = $fields['artist_name'];
        $artist_data['artists'][$post->ID]['urlFriendlyName'] = getFriendlyUrl('/artist/', $post);
        $artist_data['artists'][$post->ID]['bg'] = $fields['artist_image'];
        $artist_data['artists'][$post->ID]['themeColor'] = $fields['theme_color'];
        $artist_data['artists'][$post->ID]['textColor'] = $fields['font_color'];
        $artist_data['artists'][$post->ID]['twitter_username'] = $fields['twitter_username'];
        $artist_data['artists'][$post->ID]['visibility'] = $fields['visibility'];
    }

    $artist_data['artists_order'] = $artist_order;

    return $artist_data;
}

// 2th DONE -------------- 8/15/2016

function getTours(){

    $tour_posts = get_posts([
		'post_type' => 'tour',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'post_date',
        'order' => 'DESC'
	]);

	$tour_data = array();
    $tour_order = array();

	foreach($tour_posts as $key => $post){
		$fields = get_fields($post->ID);
        $postId = $post->ID;

        if($fields['artist'] == null) continue;

        //for order to appear on list page
        array_push($tour_order, $postId);

        /** Tour Post Data */
        $tour_data['tours'][$postId]['id'] = $post->ID;
        $tour_data['tours'][$postId]['url_friendly_name'] = getFriendlyUrl('/tour/',$post);
        $tour_data['tours'][$postId]['post_title'] = $post->post_title;
        $tour_data['tours'][$postId]['post_content'] = stripTags($post->post_content);
        $tour_data['tours'][$postId]['post_date'] = convertDateFormat($post->post_date);


        /** Image Data */
        $tour_data['tours'][$postId]['main_image'] = $fields['main_image'];
        $tour_data['tours'][$postId]['thumb_1x1'] = $fields['thumbnail'];
        $tour_data['tours'][$postId]['thumb_3x2'] = $fields['thumbnail_3x2'];
        $tour_data['tours'][$postId]['thumb_2x1'] = $fields['thumbnail_2x1'];


        /** Tour Main Data */

        $tour_data['tours'][$postId]['subtitle'] = $fields['subtitle'];
        $tour_data['tours'][$postId]['tour_url'] = $fields['tour_url'];
        $tour_data['tours'][$postId]['artist_id'] = $fields['artist'][0];


        /** Tour Schedule Data Array */
        $index = 0;
        $tour_date_arr = [];

        $begin = '';
        $end = '';

        //** Issue Fix - cuz schedule created with bad order, no calendar has benn generated. re-ordered by date
        asort($fields['tour_schedule']);

        foreach($fields['tour_schedule'] as $schedule){


            $tour_date = convertDateFormat($schedule['tour_date']);
            $tour_data['tours'][$postId]['tour_schedule'][$index]['tour_date'] = $tour_date;


            if($index == 0) $begin = $tour_data['tours'][$postId]['start_date'] = $tour_date;
            else if(count($fields['tour_schedule'])-1 == $index) $end = $tour_data['tours'][$post->ID]['end_date'] = $tour_date;



            $dtTour = new DateTime($tour_date);
            $now = new DateTime();
            $tour_date_arr[$index] = $dtTour->format('m/d');

            $dtTour->modify(' +1 days ');

            //check if tour_date is a past date to the current data. if yes, is_expired to true.


            $tour_data['tours'][$postId]['tour_schedule'][$index]['is_tour_end'] =  $now > $dtTour ? true : false;
            $tour_data['tours'][$postId]['tour_schedule'][$index]['place'] = $schedule['place'];
            $tour_data['tours'][$postId]['tour_schedule'][$index]['location'] = $schedule['location'];
            $tour_data['tours'][$postId]['tour_schedule'][$index]['event_time'] = $schedule['event_time'];
            $tour_data['tours'][$postId]['tour_schedule'][$index]['ticket_link'] = $schedule['ticket_link'];
            $tour_data['tours'][$postId]['tour_schedule'][$index]['ticket_availability'] = $schedule['ticket_availability'];
            $index++;

        }

        //When Tour schedule 1 exist.
        if($end == null) $end = $begin;

        $begin = new DateTime(date("Y-m-d", strtotime('last sunday', strtotime($begin))));
        $end = new DateTime(date("Y-m-d", strtotime('saturday this week', strtotime($end))));
        $end = $end->modify( '+1 day' );

        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod($begin, $interval ,$end);


        $tour_calendar = [];


        foreach($date_range as $date){
          $tour_calendar[] = [
            "date" => $date->format('m/d'),
            "day" => $date->format('D'),
            "available" => in_array($date->format('m/d'), $tour_date_arr)
          ];
        }

        $tour_data['tours'][$postId]['tour_calendar'] = $tour_calendar;
	}

  $tour_data['tours_order'] = $tour_order;

	return $tour_data;
}


// 2th DONE -------------- 8/15/2016
function getEvents(){

	$event_posts = get_posts([
		'post_type' => 'event',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'post_date',
        'order' => 'DESC'
	]);

	$event_data = array();
    $event_order = array();




	foreach($event_posts as $key => $post){

		$fields = get_fields($post->ID);
        $postId = $post->ID;


        //have no idea, how come it's possible saving post without putting a required field...
        //Maybe, selected artist has been deleted? I have no idea
        //To avoid this critical issue, we don't include post without artist.

        if($fields['artist'] == null) continue;

        array_push($event_order, $postId);


        $event_data['events'][$postId]['id'] = $post->ID;
        $event_data['events'][$postId]['post_title'] = $post->post_title;
        $event_data['events'][$postId]['post_content'] = nl2br($post->post_content);
        $event_data['events'][$postId]['post_date'] = convertDateFormat($post->post_date);
        $event_data['events'][$postId]['main_image'] = $fields['main_image'];
        $event_data['events'][$postId]['thumb_1x1'] = $fields['thumbnail'];
        $event_data['events'][$postId]['thumb_3x2'] = $fields['thumbnail_3x2'];
        $event_data['events'][$postId]['excerpt'] = $fields['short_description'];
        $event_data['events'][$postId]['url_friendly_name'] = getFriendlyUrl('/event/', $post);

        $event_data['events'][$postId]['artist_id'] = $fields['artist'];
        $event_data['events'][$postId]['related_event'] = $fields['related_event'] ?: [];
	}

	$event_data['event_order'] = $event_order;

	return $event_data;

}

// 2th DONE -------------- 8/15/2016
function getMusics(){

    /** ALBUM DATA */

    $album_posts = get_posts([
        'post_type' => 'album',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'post_date',
        'order' => 'DESC'
    ]);

    $album_data = array();
    $album_order = array();

    foreach($album_posts as $post){
        $fields = get_fields($post->ID);

        if($fields['artist'] == null) continue;

        array_push($album_order, $post->ID);
    }

    $album_data['albums_order'] = $album_order;

    foreach($album_posts as $post){

        $fields = get_fields($post->ID);
        $postId = $post->ID;


        //If no Artist found, delete ID from albums_order
        //then skip
        if($fields['artist'] == null) continue;
//        if($fields['artist'] == null){
//            if(($key = array_search($post->ID, $album_order)) !== false){
//                unset($album_order[$key]);
//
//            }
//            continue;
//        }


        $album_data['albums'][$postId]['id'] = $post->ID;
        $album_data['albums'][$postId]['post_title'] = $post->post_title;
        $album_data['albums'][$postId]['post_content'] = stripTags($post->post_content);
        $album_data['albums'][$postId]['post_date'] = convertDateFormat($post->post_date);
        $album_data['albums'][$postId]['url_friendly_name'] = getFriendlyUrl('/album/',$post);

        $album_data['albums'][$postId]['thumb_1x1'] = $fields['thumbnail'];
        $album_data['albums'][$postId]['cover_image'] = $fields['cover_image'];

        $album_data['albums'][$postId]['album_url'] = $fields['album_url'];
        $album_data['albums'][$postId]['album_release_date'] = convertDateFormat($fields['album_release_date']);

        $album_data['albums'][$postId]['artist_id'] = $fields['artist'][0];

        $related_ablum_array = array();


        //In case of related_ablum deleted or drafted
        if(is_array($fields['related_album'])){
            foreach($fields['related_album'] as $related_ablum){
                if(in_array($related_ablum, $album_order)){
                    array_push($related_ablum_array, $related_ablum);
                }
            }
        }


        $album_data['albums'][$postId]['related_album'] = $related_ablum_array ?: [];
        $album_data['albums'][$postId]['individual_name'] = isset($fields['individual_name']) ? $fields['individual_name'] : '';
    }

    /** MUSIC DATA */

    $music_posts = get_posts([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'post_date',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => '_downloadable',
                'value' => 'yes'
            )
        )
    ]);

    $music_order = array();

    foreach($music_posts as $music_post){

        $music_fields = get_post_meta($music_post->ID);
        $music_custom_fields = get_fields($music_post->ID);

        //if no album id exist, skip
        if($music_custom_fields['album'] == null) continue;

        //if album that this music belongs to does not exist, then skip
        if(in_array($music_custom_fields['album'][0], $album_data['albums_order']) == false) continue;

        array_push($music_order, $music_post->ID);

        $album_data['musics'][$music_post->ID]['id'] = $music_post->ID;
        $album_data['musics'][$music_post->ID]['post_title'] = $music_post->post_title;
        $album_data['musics'][$music_post->ID]['post_content'] = stripTags($music_post->post_content);
        $album_data['musics'][$music_post->ID]['post_date'] = convertDateFormat($music_post->post_date);


        $album_data['musics'][$music_post->ID]['_regular_price'] = $music_fields['_regular_price'][0] ?: null;
        $album_data['musics'][$music_post->ID]['_sale_price'] = $music_fields['_sale_price'][0] ?: null;


        $album_data['musics'][$music_post->ID]['album_id'] = $music_custom_fields['album'][0];


        $file = $music_fields['_downloadable_files'][0];
        $unserialized = unserialize($file);


        $fileLink = null;
        $sample_link = null;
        if(count($unserialized) > 0){
            foreach($unserialized as $item){
                $fileLink = $item['file'];
            }

            $sample_link = str_replace('.mp3', '-sample.mp3', str_replace('/woocommerce_uploads', '', $fileLink));
        }

        if($music_custom_fields['music_product_type'] == 'album') $sample_link = null;


        $album_data['musics'][$music_post->ID]['sample_link'] = $sample_link;

        $album_data['musics'][$music_post->ID]['youtube_link'] = $music_custom_fields['youtube_link'];
        $album_data['musics'][$music_post->ID]['product_type'] = $music_custom_fields['music_product_type'];

    }

    $album_data['musics_order'] = $music_order;

    /** HOT TRACK DATA */

    $hot_tracks = get_option('sub_hot_track_enable');
    $hot_tracks_order = array_filter(get_option('sub_hot_track_order'));

    asort($hot_tracks_order);

    $index = 0;


    //Need to update later if there's time
    //if no hot_track_order then, skip
    if(count($hot_tracks_order) > 0 && $hot_tracks_order != null) {
        foreach ($hot_tracks_order as $key => $value) {
            
            //if key from hot_tracks_order doesn't exist on hot_tracks, then skip
            //Meaning Even though there is order inputted, it will skip if no checkbox selected.
            if (key_exists($key, $hot_tracks)) {

                //if this hot track id is not in music_order, then skip
                if(in_array($key, $music_order)){
                    $album_data['hotTracks'][$index][] = $key;
                }
                $index++;
            }
        }
    }else{
        $album_data['hotTracks'] = [];
    }
    
    return $album_data;
}


// 2th DONE -------------- 8/15/2016
function getBlogs(){

    $blog_posts = get_posts([
        'post_type' => 'blog',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'post_date',
        'order' => 'DESC'
    ]);

    $blog_data = array();
    $blog_order = array();

    foreach($blog_posts as $key => $post){

        $fields = get_fields($post->ID);
        $postId = $post->ID;

        //for order to appear on list page
        array_push($blog_order, $post->ID);

        $blog_data['posts'][$postId]['id'] = $post->ID;
        $blog_data['posts'][$postId]['post_title'] = $post->post_title;
        $blog_data['posts'][$postId]['url_friendly_name'] = getFriendlyUrl('/blog/',$post);
        $blog_data['posts'][$postId]['excerpt'] = $post->post_excerpt;
        $blog_data['posts'][$postId]['post_content'] = stripTags($post->post_content);
        $blog_data['posts'][$postId]['post_date'] = convertDateFormat($post->post_date);
        $blog_data['posts'][$postId]['related_blog'] = $fields['related_blog'] ?: [];
        $blog_data['posts'][$postId]['main_image'] = $fields['main_image'];
        $blog_data['posts'][$postId]['thumb_2x1'] = $fields['thumbnail_2x1'];
        $blog_data['posts'][$postId]['thumb_3x2'] = $fields['thumbnail_3x2'];
    }

    $blog_data['posts_order'] = $blog_order;

    $hot_blogs = get_option('sub_hot_blog_enable');
    $index = 0;

    if(count($hot_blogs) > 0 && $hot_blogs != null){
        foreach($hot_blogs as $key => $value){
            $blog_data['hot_posts'][$index] = $key;
            $index++;
        }
    }else{
        $blog_data['hot_posts'] = [];
    }

    $blog_banner_posts = get_posts([
        'post_type' => 'blog-banner',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'post_date'
    ]);

    foreach($blog_banner_posts as $key => $post){
        $postId = $post->ID;
        $fields = get_fields($postId);

        $blog_data['banner'][$key]['thumbnail'] = $fields['thumbnail'];
        $blog_data['banner'][$key]['target_url'] = $fields['target_url'];
    }



    return $blog_data;
}


// 2th DONE -------------- 8/15/2016
function getShops(){

    $shop_posts = get_posts([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'post_date',
        'order' => 'DESC',
        'meta_query' => array(
            array(
                'key' => '_downloadable',
                'value' => 'no'
            )
        )
    ]);

    $shop_data = array();
    $product_order = array();
    foreach($shop_posts as $key => $post){

        $fields = get_post_meta($post->ID);
        $custom_fields = get_fields($post->ID);



        array_push($product_order, $post->ID);

        $plan = wc_get_product($post->ID);

        $terms = get_the_terms($post->ID, 'product_cat');

        $terms_data = array();

        if($terms){
          foreach($terms as $term_key => $term_value){
              $terms_data[$term_key] = $term_value->term_id;
          }
        }

        $shop_data['products'][$post->ID]['id'] = $post->ID;
        $shop_data['products'][$post->ID]['post_title'] = $post->post_title;
        $shop_data['products'][$post->ID]['post_content'] = stripTags($post->post_content);
        $shop_data['products'][$post->ID]['post_date'] = convertDateFormat($post->post_date);
        $shop_data['products'][$post->ID]['url_friendly_name'] = getFriendlyUrl('/product/', $post);

        $shop_data['products'][$post->ID]['product_type'] = $plan->product_type;
        $shop_data['products'][$post->ID]['cat_IDs'] = $terms_data;

        $shop_data['products'][$post->ID]['thumb_1x1'] = $custom_fields['thumbnail_2x2'];
        $shop_data['products'][$post->ID]['thumb_2x1'] = $custom_fields['thumbnail_2x1'];
        $shop_data['products'][$post->ID]['thumb_1x2'] = $custom_fields['thumbnail_1x2'];

        $images_ids = $plan->get_gallery_attachment_ids();

        $index = 0;
        $shop_data['products'][$post->ID]['images'] = [];

        foreach( $images_ids as $image_id){
            $shop_data['products'][$post->ID]['images'][$index] = wp_get_attachment_url($image_id);
            $index++;
        }

        /** Custom Field Not in WooCommerce */

        $shop_data['products'][$post->ID]['artist_id'] = is_array($custom_fields['artist_product']) ? $custom_fields['artist_product'][0] : '';
        $shop_data['products'][$post->ID]['related'] = count($plan->get_cross_sells()) > 0 ? $plan->get_cross_sells() : [];


        if($plan->product_type == 'variable'){

          //Get All Attributes this product used along with sort ordering
          $all_attributes = $plan->get_attributes();

          $att_temp = array();
          $attr_order_temp = array();

            foreach($all_attributes as $attr){

            $terms = get_the_terms($post->ID, $attr['name']);

              $attr_order = array_map('trim', explode(',', $plan->get_attribute($attr['name'])));


              foreach($terms as $term){
                  $key = array_search($term->name, $attr_order);
                  $attr_order_temp['attribute_'.$attr['name']][$key] = $term->slug;
                  $att_temp['attribute_'.$attr['name']][$term->slug] = $term->name;
              }

              ksort($attr_order_temp['attribute_'.$attr['name']]);

          }

          $shop_data['products'][$post->ID]['attributes'] = $att_temp;
          $shop_data['products'][$post->ID]['attributes_order'] = $attr_order_temp;



          $variations = $plan->get_available_variations();
          $default = $plan->get_variation_default_attributes();


          $default_att = array();
          foreach($default as $key_att => $item){
            $default_att['attribute_'.$key_att] = $item;
          }

          $isOutofStock = true;

          foreach($variations as $k => $variation){
            $shop_data['products'][$post->ID]['variation'][$k]['variation_id'] = $variation['variation_id'];

              //Stock Management
              $stock_status = get_post_meta($variation['variation_id'], '_stock_status')[0];
              $shop_data['products'][$post->ID]['variation'][$k]['_stock_status'] = $stock_status;
              if($stock_status == 'instock') $isOutofStock = false;


              //regular price
              $shop_data['products'][$post->ID]['variation'][$k]['display_regular_price'] = $variation['display_regular_price'];


              $curTime = time();
              $v_salePrice = get_post_meta($variation['variation_id'], '_sale_price')[0];

              //sale Price 존재 여부 파악
              if($v_salePrice == null){
                  //sale Price exist
              }else{
                  //sale Price not exist
                  //sale 기간 파악

                  //variation 내에는 세일 관련 기간을 파악 할 수 없음. variation ID 로 등록 된 Post 의 meta data 호출 하여 from, to 정보 읽어 옴
                  $v_sale_price_dates_from = get_post_meta($variation['variation_id'], '_sale_price_dates_from')[0];
                  $v_sale_price_dates_to = get_post_meta($variation['variation_id'], '_sale_price_dates_to')[0];


                  if($v_sale_price_dates_from == null && $v_sale_price_dates_to == null){
                      //기간이 존재 하지 않을 시에는 sale price 적용
                  }else{
                      if($v_sale_price_dates_from < $curTime && $curTime < $v_sale_price_dates_to){
                          // sale 기간에 포함 됨
                      }else{
                          // sale 기간에 포함 되지 않음
                          // sale price 를 null 로 set
                          $v_salePrice = '';
                      }
                  }

              }
              
              //sale price
            $shop_data['products'][$post->ID]['variation'][$k]['display_price'] = $v_salePrice;



            $shop_data['products'][$post->ID]['variation'][$k]['sku'] = $variation['sku'];
            $shop_data['products'][$post->ID]['variation'][$k]['image'] = $variation['image_src'];

            $attributes = $variation['attributes'];


            $att_index = 0;
            $is_default = true;
            foreach($attributes as $att_key => $attribute){
              $shop_data['products'][$post->ID]['variation'][$k]['attribute'][$att_index]['key'] = $att_key;
              $shop_data['products'][$post->ID]['variation'][$k]['attribute'][$att_index]['value'] = $attribute;

              if(!isset($default_att[$att_key])){
                $is_default = false;
              } else if($default_att[$att_key] != $attribute){
                $is_default = false;
              }

              $att_index++;
            }

            $shop_data['products'][$post->ID]['variation'][$k]['id_default'] = $is_default;
          }

          $shop_data['products'][$post->ID]['_stock_status'] = $isOutofStock ==  true ? 'outofstock' : 'instock';

        }else{

            //Stock Management
            $shop_data['products'][$post->ID]['_stock_status'] = $fields['_stock_status'][0];

            //Price Management
            $shop_data['products'][$post->ID]['_regular_price'] = $fields['_regular_price'][0];

            $curTime = time();
            $salePrice = $fields['_sale_price'][0];



            //sale Price 존재 여부 파악
            if($salePrice == null){
                //sale Price not exist
            }else{
                //sale Price exist
                $_sale_price_dates_from = $fields['_sale_price_dates_from'][0];
                $_sale_price_dates_to = $fields['_sale_price_dates_to'][0];

                //기간 존재 유무 파악 우선.
                if($_sale_price_dates_from == null && $_sale_price_dates_to == null){
                    //기간이 존재 하지 않을 시에는 sale price 적용
                }else{
                    //sale 기간 파악
                    if($_sale_price_dates_from < $curTime && $curTime < $_sale_price_dates_to){
                        // sale 기간에 포함 됨. sale price 적용
                    }else{
                        // sale 기간에 포함 되지 않음
                        // sale price 를 null 로 set
                        $salePrice = '';
                    }
                }
            }



          $shop_data['products'][$post->ID]['_sale_price'] = $salePrice;
          $shop_data['products'][$post->ID]['_sku'] = $fields['_sku'][0];
        }
    }

    $shop_data['products_order'] = $product_order;


    $categories =  get_categories([
        'taxonomy'     => 'product_cat',
        'orderby'      => 'id',
        'show_count'   => 0,
        'pad_counts'   => 0,
        'hierarchical' => 1,
        'title_li'     => '',
        'hide_empty'   => 0
    ]);

    if(count($categories) > 0 && $categories != null){
        foreach($categories as $key => $category){
            $shop_data['categories'][$category->cat_ID]['cat_ID'] = $category->cat_ID;
            $shop_data['categories'][$category->cat_ID]['name'] = $category->name;
        }
    }else{
        $shop_data['categories'] = [];
    }

    return $shop_data;

}


function test(){

    $postId = 536;

    $post = get_post($postId);
    $plan = wc_get_product($postId);

    $all_attributes = $plan->get_attributes();

    foreach($all_attributes as $attr){
        $terms = get_the_terms($postId, $attr['name']);

        $attr_order = array_map('trim', explode(',', $plan->get_attribute($attr['name'])));
        $new_attr_order = array();

        foreach($terms as $term){
            $key = array_search($term->name, $attr_order);
            $new_attr_order[$key] = $term->slug;

            $att_temp['attribute_'.$attr['name']][$term->slug] = $term->name;
        }

        ksort($new_attr_order);


    }



    return $new_attr_order;
}

function getPromotions(){


  $main_product = 'main_product';
  $main_album = 'main_album';
  $main_tour = 'main_tour';
  $main_event = 'main_event';

  $options = [$main_product, $main_album, $main_tour, $main_event];

  foreach($options as $option){
    $post_type = explode('_' , $option)[1];

    $enables_items = get_option($option.'_enable');
    $order_items = get_option($option.'_order');

    $promotion_data[$post_type] = array();

    if($enables_items){
      foreach($enables_items as $post_id => $post_type){
        $order = $order_items[$post_id];
        $promotion_data[$post_type][$order]['id'] = $post_id;
        $promotion_data[$post_type][$order]['order'] = $order;
      }
    }

    /** sort by order */
    ksort($promotion_data[$post_type]);

    /** remove if count > 3 per each Post Type */
    if(count($promotion_data[$post_type]) > 3){
      $index = 1;
      foreach($promotion_data[$post_type] as $key => $item){
        if($index > 3){
          unset($promotion_data[$post_type][$key]);
        }
        $index++;
      }
    }
  }

    $main_banner_posts = get_posts([
        'post_type' => 'main-banner',
        'post_status' => 'publish',
        'posts_per_page' => 3,
        'orderby' => 'post_date'
    ]);

    $main_banner_data = array();


    foreach($main_banner_posts as $key => $post){
        $postId = $post->ID;
        $fields = get_fields($postId);

        $promotion_data['banner'][$key]['thumbnail_2x1'] = $fields['thumbnail_2x1'];
        $promotion_data['banner'][$key]['thumbnail_3x2'] = $fields['thumbnail_3x2'];
        $promotion_data['banner'][$key]['thumbnail'] = $fields['thumbnail'];
        $promotion_data['banner'][$key]['target_url'] = $fields['target_url'];
    }


  return $promotion_data;
}


function getFacebookFeeds($fb_page_id){
    $access_token="1584941118480725|iTQQ_kX7d3kwwKODmTHv7Dz50dY";

    $url = "https://graph.facebook.com/".$fb_page_id."/posts?fields=id,full_picture,from,message,message_tags,story,story_tags,link,source,name,caption,description,type,status_type,object_id,created_time&access_token=". $access_token. "&limit=6&locale=en_US";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $json = curl_exec($ch);
    curl_close($ch);

    $data = (array) json_decode($json);
    return $data;

}

function getInstagramPhotos(){

  //Instagram API
  //https://api.instagram.com/v1/users/'USER_ID_GOES_HERE'/media/recent?access_token='YOU_ACCESS_TOKEN'

  //use this instagram access token generator http://instagram.pixelunion.net/
  $access_token="14387739.1677ed0.2335d2923cad4d18b161b1bbd6def862";

  //use this to get user id https://smashballoon.com/instagram-feed/find-instagram-user-id
  $user_id = '14387739';

  $photo_count=9;

  $json_link="https://api.instagram.com/v1/users/$user_id/media/recent/?";
  $json_link.="access_token={$access_token}&count={$photo_count}";


  $json = file_get_contents($json_link);
  $obj = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);

  return $obj;
}




//refer to https://www.codeofaninja.com/2015/08/display-twitter-feed-on-website.html
//refer to http://stackoverflow.com/questions/12684765/twitter-api-returns-error-215-bad-authentication-data

function getSocialFeeds(){

  $artist_posts = get_posts([
    'post_type' => 'artist',
    'post_status' => 'publish',
    'posts_per_page' => -1
  ]);

  $new_feed_data = [];

    // Twitter Instagram
  if(false){
      foreach($artist_posts as $key => $post){

          $tweeterUsername = get_field('twitter_username', $post->ID);
          $instaUserName = get_field('instagram_username', $post->ID);

          $tweets = $tweeterUsername != null ?  getTweets($tweeterUsername, 3) : [];
          $instaFeeds = $instaUserName != null ? getInsta($instaUserName, 2) : [];


          $feed_data = [];

          if($tweets && count($tweets) > 0){
              foreach($tweets as $tweet){
                  $time_created_at = strtotime($tweet->created_at);

                  $feed_data[$time_created_at]['type'] = 'tweeter';
                  $feed_data[$time_created_at]['artist_id'] = $post->ID;
                  $feed_data[$time_created_at]['url'] = 'https://twitter.com/'.$tweeterUsername;
                  $feed_data[$time_created_at]['username'] = $tweeterUsername;
                  $feed_data[$time_created_at]['text'] = $tweet->text;
                  $feed_data[$time_created_at]['created_at'] = convertDateFormat($tweet->created_at);
                  $feed_data[$time_created_at]['image'] = '';
                  $feed_data[$time_created_at]['profile_image'] = $tweet->user->profile_image_url;

              }
          }


          if($instaFeeds && count($instaFeeds) > 0){
              foreach($instaFeeds as $feed){
                  $time_created_at = $feed->created_time;

                  $feed_data[$time_created_at]['type'] = 'instagram';
                  $feed_data[$time_created_at]['artist_id'] = $post->ID;
                  $feed_data[$time_created_at]['url'] = $feed->link;
                  $feed_data[$time_created_at]['username'] = $instaUserName;
                  $feed_data[$time_created_at]['text'] = '';
                  $feed_data[$time_created_at]['created_at'] = date('Y-m-d', $feed->created_time);
                  $feed_data[$time_created_at]['image'] = $feed->images->thumbnail->url;
                  $feed_data[$time_created_at]['profile_image'] = '';
              }
          }

          krsort($feed_data);

          //After krsort, rename key value
          $index = 0;
          foreach($feed_data as $key => $item){
              $new_feed_data[$post->ID][$index] = $item;
              $index++;
          }
      }
  }

  // Facebook
  else{
      foreach($artist_posts as $key => $post){
          $fbUsername = get_field('facebook_username', $post->ID);
          $fbFeeds = $fbUsername != null ? getFacebookFeeds($fbUsername) : [];

          if($fbFeeds && count($fbFeeds) > 0) {
              $index = 0;
              foreach($fbFeeds['data'] as $feed){
                  if($index == 5) break;

                  $new_feed_data[$post->ID][$index]['type'] = 'facebook';
                  $new_feed_data[$post->ID][$index]['artist_id'] = $post->ID;
                  $new_feed_data[$post->ID][$index]['username'] = $fbUsername;
                  $new_feed_data[$post->ID][$index]['text'] = isset($feed->message) ? $feed->message : $feed->description;
                  $new_feed_data[$post->ID][$index]['image'] = isset($feed->full_picture) ? $feed->full_picture : null;
                  $new_feed_data[$post->ID][$index]['url'] = isset($feed->link) ? $feed->link : null;
                  $new_feed_data[$post->ID][$index]['created_at'] = isset($feed->created_time) ? convertDateFormat($feed->created_time) : null;

                  $index++;
              }
          }
      }
  }


  return $new_feed_data;
}

function getInsta($username, $count){

  $url =  'https://www.instagram.com/'.$username.'/media/';

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
  $json = curl_exec($ch);
  curl_close($ch);

  $data = (array) json_decode($json);
  $data = $data['items'];

  //API call 로 최대 호출 가능 수 20.
  if($count > 20) $count = 20;

  foreach($data as $key => $item){
   if($key > $count-1){
     unset($data[$key]);
   }
  }
  return $data;
}

function getTweets($username, $count){

  $token = '152670167-XodIHmDXLgBFs0aY3nMk4lVntEx9b98Gr6IPoe2n';
  $token_secret = '0BUrdprSyrP8EKeLdSFxpCjo3OXUuBC4MzhXjIJDxoFJL';
  $consumer_key = 'SvmPDVspbcyPJVs2Okhmw47iu';
  $consumer_secret = 'TYTEIBmJ9uGkWluWW7IelDgOadZl1nOndifMOKvaM13jV0YjW3';

  $host = 'api.twitter.com';
  $method = 'GET';
  $path = '/1.1/statuses/user_timeline.json'; // api call path

  $query = array( // query parameters
    'screen_name' => $username,
    'count' => $count+3
  );

  $oauth = array(
    'oauth_consumer_key' => $consumer_key,
    'oauth_token' => $token,
    'oauth_nonce' => (string)mt_rand(), // a stronger nonce is recommended
    'oauth_timestamp' => time(),
    'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_version' => '1.0'
  );

  $oauth = array_map("rawurlencode", $oauth); // must be encoded before sorting
  $query = array_map("rawurlencode", $query);

  $arr = array_merge($oauth, $query); // combine the values THEN sort

  asort($arr); // secondary sort (value)
  ksort($arr); // primary sort (key)

  // http_build_query automatically encodes, but our parameters
  // are already encoded, and must be by this point, so we undo
  // the encoding step
  $querystring = urldecode(http_build_query($arr, '', '&'));

  $url = "https://$host$path";

  // mash everything together for the text to hash
  $base_string = $method."&".rawurlencode($url)."&".rawurlencode($querystring);

  // same with the key
  $key = rawurlencode($consumer_secret)."&".rawurlencode($token_secret);

  // generate the hash
  $signature = rawurlencode(base64_encode(hash_hmac('sha1', $base_string, $key, true)));

  // this time we're using a normal GET query, and we're only encoding the query params
  // (without the oauth params)
  $url .= "?".http_build_query($query);
  $url=str_replace("&amp;","&",$url); //Patch by @Frewuill

  $oauth['oauth_signature'] = $signature; // don't want to abandon all that work!
  ksort($oauth); // probably not necessary, but twitter's demo does it

//  also not necessary, but twitter's demo does this too
//  function add_quotes($str) { return '"'.$str.'"'; }
//  $oauth = array_map("add_quotes", $oauth);

  // this is the full value of the Authorization line
  $auth = "OAuth " . urldecode(http_build_query($oauth, '', ', '));

  // if you're doing post, you need to skip the GET building above
  // and instead supply query parameters to CURLOPT_POSTFIELDS
  $options = array( CURLOPT_HTTPHEADER => array("Authorization: $auth"),
    //CURLOPT_POSTFIELDS => $postfields,
    CURLOPT_HEADER => false,
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false);

  // do our business
  $feed = curl_init();
  curl_setopt_array($feed, $options);
  $json = curl_exec($feed);
  curl_close($feed);

  $data = json_decode($json);


  foreach($data as $key => $item){
    if($key > $count-1){
      unset($data[$key]);
    }
  }


  return $data;
}


function reserializePath(){

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

    foreach($music_posts as $post){

        $before_url = 'yg.testroo.com';
        $after_url  = 'ygpresents.com';


        //Array Return from serialized string
        $music_field = get_post_meta($post->ID, '_downloadable_files');

        $new_array = array();

        $is_update_required = false;

        if(count($music_field[0]) > 0 ){
            foreach($music_field as $field){
                foreach($field as $y => $item){
                    $new_array[$y]['name'] = $item['name'];

                    //Only when OLD address exist, it tries to update
                    if(strpos($item['file'], $before_url)){
                        $new_array[$y]['file'] = str_replace($before_url, $after_url, $item['file']) ;
                        $is_update_required = true;
                    }
                }
            }

            if($is_update_required){
                //Serialized data will be stored with array parameter
                update_post_meta($post->ID, '_downloadable_files', $new_array);
            }
        }
    }
}




function convertDateFormat($date){
  return date("Y-m-d", strtotime($date));
}

function getFriendlyUrl($type, $post){

    $permalink = get_permalink($post);
    return str_replace($type, '', parse_url($permalink)['path']);
}

function stripTags($string){

  return strip_tags($string);
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











/** Issue Tracker to find issues */

function musicProductsWithNoAlbumsSelect(){
    $music_posts = get_posts([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'post_date',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => '_downloadable',
                'value' => 'yes'
            )
        )
    ]);
    $ret = array();

    foreach($music_posts as $music_post){
        $music_custom_fields = get_fields($music_post->ID);

        if($music_custom_fields['album'] == null) {
            array_push($ret, $music_post->ID);
        }
    }
    return $ret;
}


function musicProductsWithNoDownloadableFile(){
    $music_posts = get_posts([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'post_date',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => '_downloadable',
                'value' => 'yes'
            )
        )
    ]);

    $ret = array();

    foreach($music_posts as $music_post){
        $music_field = get_post_meta($music_post->ID, '_downloadable_files');
        if(count($music_field[0]) == 0){
            array_push($ret, $music_post->ID);
        }

    }
    return $ret;
}


function getTotalCountOfProducts(){
    $music_posts = get_posts([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'post_date',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => '_downloadable',
                'value' => 'yes'
            )
        )
    ]);

    $ret['music_count'] = count($music_posts);

    $shop_posts = get_posts([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'post_date',
        'order' => 'DESC',
        'meta_query' => array(
            array(
                'key' => '_downloadable',
                'value' => 'no'
            )
        )
    ]);

    $ret['shop_count'] = count($shop_posts);

    return $ret;
}





/** SET VALUE */

function setVirtualToYesForMusic(){

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

    foreach($music_posts as $post){
        update_post_meta($post->ID, '_virtual', 'yes');
    }

    return ['success'];

}



