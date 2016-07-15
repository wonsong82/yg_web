<?php
/*
Process:
1. This plugin adds custom routes on top of the routes already defined in wordpress
2. User types the uri that is defined in the custom routes.
3. Wordpress directs the route to designated "Page"
4. "Page" loads the "Template" file assigned to the page.

How TO USE (With example usage with SPA):
  0. Before start, activate the plugin, obviously.
  1. First, make template files being used for custom routes.
    In this case, we are making two template files:
      template-front.php : routed from defined uris to this template, then SPA will handle routing on client side
      template-api.php : template file for api, usually outputs RESTFUL API format
  2. Then, create pages in Pages section in Admin Panel
    In this case, we are adding following pages:
      Front : this will be used for all the client routes for SPA. select Front as template
      API : this will be used for all the api requests. select Api as template
      Home : this will be used for root route. select Front as template
  3. Then, add routes into this file.
    Look before for example.
      uri_pattern: this resembles the pattern being used for htaccess rewrite rule, using regular expression
      match_params: matches from uri_pattern(with parenthesis) gets saved into query_vars with names defined here
      page_post_id: the post_id of the Page you created in step 2. you can find post_id by from url, look for post= parameter
  4. Finally, activate them.
    Go to  Settings->Permalinks and change the permalink setting to "Post name" and click "Save Changes"
    Note that every time when the routes are modified, YOU MUST come back here and hit "Save Changes" to affect new changes.
    Go to Settings->Reading and set Home ad front page display
*/
return [

  // For any api calls. Template file receives class & method params via $wp_query->query
  [
    'uri_pattern'   => '^api/(.+?)/(.+?)/?$',
    'match_params'  => ['class', 'method'],
    'page_post_id'  => 103,
  ],

  // For regular SPA routes
  [
    'uri_pattern'   => '^(blog)/?$',
    'match_params'  => ['page'],
    'page_post_id'  => 101
  ],
  [
    'uri_pattern'   => '^(event)/?$',
    'match_params'  => ['page'],
    'page_post_id'  => 101
  ],
  [
    'uri_pattern'   => '^(tour)/?$',
    'match_params'  => ['page'],
    'page_post_id'  => 101
  ],
  [
    'uri_pattern'   => '^(shop)/?$',
    'match_params'  => ['page'],
    'page_post_id'  => 101
  ],
  [
    'uri_pattern'   => '^(music)/?$',
    'match_params'  => ['page'],
    'page_post_id'  => 101
  ],
  [
    'uri_pattern'   => '^(artist)/(.+?)/?$',
    'match_params'  => ['page', 'artist_name'],
    'page_post_id'  => 101
  ]
];


