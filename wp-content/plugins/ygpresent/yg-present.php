<?php
/*
Plugin Name: YG Present
Plugin URI: yg-present.com
Version: 1.0.0
Author: Zeter Lee
Description: YG Present
 */

$main_contents = 'main_contents';
$hot_track = 'hot_track';
$hot_blog = 'hot_blog';


$yg_option_fields = array(
    $main_contents,
    $hot_track,
    $hot_blog
);

// Hook for adding admin menus
add_action('admin_init', 'yg_admin_init');
add_action('admin_menu', 'yg_admin_menu');



function yg_admin_init() {
    global $yg_option_fields;

    foreach($yg_option_fields as $key => $field){
        register_setting($field, $field);
    }
}

// action function for above hook
function yg_admin_menu() {

    // Add a new top-level menu (Promotional Contents Manager):
    add_menu_page('Promotion', 'Promotion', 'administrator', 'contents-manager', 'contents_manager' );

    // Add a submenu to the Promotional Contents Manager Menu
    add_submenu_page('contents-manager', 'Main Contents', 'Main Contents', 'administrator', 'main-contents', 'get_main_contents');
    add_submenu_page('contents-manager', 'Hot Track', 'Hot Track', 'administrator', 'hot-track', 'get_hot_track');
    add_submenu_page('contents-manager', 'Hot Blog', 'Hot Blog', 'administrator', 'hot-blog', 'get_hot_blog');

}

function contents_manager() {
    echo "<h2>" . 'Promotional Contents Manager' . "</h2>";
}


function get_main_contents() {

    echo "<h1>" . 'Main Contents List' . "</h1>";
    echo "<h4>" . 'Please select promotional items that need to appear on Main Promotional Page' . "</h4>";

    global $main_contents;

    $combined_posts = get_posts([
        'post_type' => array(
           'product','tour','event','album'
        ),
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ]);

    $main_contents_option = get_option($main_contents);
    option_form($main_contents_option, $combined_posts, $main_contents);
}


function get_hot_track() {

    echo "<h1>" . 'Hot Track List' . "</h1>";
    echo "<h4>" . 'Please select music items that need to appear on Hot Track List' . "</h4>";

    global $hot_track;

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
    $hot_track_option = get_option($hot_track);
    option_form($hot_track_option, $music_posts, $hot_track);
}

function get_hot_blog(){

    echo "<h1>" . 'Hot Blog List' . "</h1>";
    echo "<h4>" . 'Please select Blog that need to appear on Hot Blog List' . "</h4>";

    global $hot_blog;

    $blog_posts = get_posts([
        'post_type' => 'blog',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ]);
    $hot_track_option = get_option($hot_blog);
    option_form($hot_track_option, $blog_posts, $hot_blog);
}

function option_form($option, $posts, $tag){?>

    <form method="post" action="options.php">
        <?php settings_fields( $tag ); ?>

        <table class="form-table">
            <tr valign="top">
                <td scope="row" colspan="2">
                    <?php foreach($posts as $post){

                        /**  
                         * Main Promotion 일 경우,
                         * Downloadable Music 은 해당 list 에 포함 시키 지 않는다.
                         * Album Item 은 Album Post Type 에서 가져 온다.
                         */

                        if($tag == 'main_contents'){
                            if($post->post_type == 'product'){
                                $post_type_val = get_post_meta($post->ID, '_downloadable');

                                if($post_type_val[0] == 'yes'){
                                    continue;
                                }
                            }
                        }
                        
                        $title = $post->post_title;
                        $id = $post->ID;
                        $post_type = strtoupper($post->post_type);
                        ?>

                        <input name="<?php echo $tag ?>[<?php echo $id?>]" type="checkbox"
                            <?=isset($option[$id]) == '1' ? 'checked' : '' ?>
                            value="<?= $post->post_type ?>"
                        />

                        <lable for=<?php echo $tag?>><?php echo $title . ' [' . $post_type . ']' ?></lable><br>
                    <?php } ?>
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
    <?php
}