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

$dummyData =  [
[
	'name' => 'Won Song',
	'phone' => '2017398833',
	'age' => 30
],
[
	'name' => 'Brian Persico',
	'phone' => '646576232',
	'age' => 25
]
];
 
echo 'API';
echo '<pre>';
//var_dump($wp_query->query);

 print_r($wp_rewrite->rules);
 
 
 
?>
