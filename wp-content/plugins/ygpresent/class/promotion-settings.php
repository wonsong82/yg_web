<?php
/*
Plugin Name: YG Present
Plugin URI: yg-present.com
Version: 1.0.0
Author: Zeter Lee
Description: YG Present
 */

class PromotionSettings
{
    const VERSION = '1.0.0';


    public $main_product = 'main_product';


    public $main_album = 'main_album';


    public $main_tour = 'main_tour';


    public $main_event = 'main_event';


    public $hot_track = 'sub_hot_track';


    public $hot_blog = 'sub_hot_blog';


    public $options = array();


    public $slug = 'promotion-settings';


    public function __construct()
    {
        $this->options = array(
            $this->main_product,
            $this->main_album,
            $this->main_tour,
            $this->main_event,
            $this->hot_track,
            $this->hot_blog
        );

        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));


        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }

    function add_plugin_page() {

        // Add a new top-level menu (Promotional Contents Manager):
        add_menu_page('Promotion', 'Promotion', 'edit_users', 'contents-manager', array($this, 'get_main_contents'));


        // Add a sub-menu to the Promotional Contents Manager Menu
        add_submenu_page('contents-manager', 'Hot Track', 'Hot Track', 'edit_users', 'hot-track', array($this, 'get_hot_track'));
        add_submenu_page('contents-manager', 'Hot Blog', 'Hot Blog', 'edit_users', 'hot-blog', array($this, 'get_hot_blog'));

    }

    function page_init() {
        foreach($this->options as $option){
            $this->reg_setting($option);
        }
    }

    public function reg_setting($option){
        register_setting($option.'-group', $option.'_enable');
        register_setting($option.'-group', $option.'_order');
    }

    public function enqueue_admin_styles(){
        $src = plugins_url( 'assets/css/table.css', __FILE__ );

        wp_enqueue_style( $this->slug .'-table', $src , array(), self::VERSION );
    }

    public function enqueue_admin_scripts(){
        //@todo JavaScript Register if needed
        $src = plugins_url( 'assets/js/promotion-setting.js', __FILE__ );

        wp_enqueue_script($this->slug.'-function', $src , array() , self::VERSION);

    }


    function get_main_contents() {

        $type = isset($_GET['content_type']) ? $_GET['content_type'] : 'product';

        $query_arg = array(
            'post_type' => $type,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );


        $this->save_data('main_' . $type);

        switch ($type){
            case 'product' :
                $query_arg = array_merge($query_arg , array(
                    'meta_query' => array(
                        array(
                            'key' => '_downloadable',
                            'value' => 'no'
                        )
                    )
                ));

                break;
            default :
                break;
        }


        $posts = get_posts($query_arg);

        $option_enable = get_option('main_' . $type . '_enable');
        $option_order = get_option('main_' . $type . '_order');


        $this->set_top_navi(strtoupper($type));


        $this->setting_form(strtoupper($type), $posts, $option_enable, $option_order, 3);

    }

    function get_hot_track() {

        $type = 'HOT TRACK';

        if($_POST)
            $this->save_data($this->hot_track);

        $option_enable = get_option($this->hot_track.'_enable');
        $option_order = get_option($this->hot_track.'_order');

        $posts = get_posts([
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_downloadable',
                    'value' => 'yes'
                ),
                array(
                    'key' => 'music_product_type',
                    'value' => 'music'
                )
            )
        ]);

        $this->setting_form($type, $posts, $option_enable, $option_order);

    }

    function get_hot_blog(){
        $type = 'HOT BLOG';

        if($_POST)
            $this->save_data($this->hot_blog);


        $option_enable = get_option($this->hot_blog.'_enable');
        $option_order = get_option($this->hot_blog.'_order');


        $posts = get_posts([
            'post_type' => 'blog',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'post_date',
            'order' => 'DESC'
        ]);

        $this->setting_form($type, $posts, $option_enable, $option_order);
    }


    function save_data($option){

        if(isset($_POST['enable']) && isset($_POST['order'])){
            update_option( $option.'_enable', $_POST['enable'] );
            update_option( $option.'_order' , $_POST['order']);
        }

    }

    function no_post_message($ret){

    }



    function set_top_navi($type){
        $url = admin_url().'admin.php?page=contents-manager';
        ?>



        <div class="top-navi">
            <ul>
                <li class="<?= $type == 'PRODUCT' ? 'active' : '' ?>"><a href="<?=$url?>&content_type=product">PRODUCT</a></li>
                <li class="<?= $type == 'TOUR' ? 'active' : '' ?>"><a href="<?=$url?>&content_type=tour">TOUR</a></li>
                <li class="<?= $type == 'EVENT' ? 'active' : '' ?>"><a href="<?=$url?>&content_type=event">EVENT</a></li>
                <li class="<?= $type == 'ALBUM' ? 'active' : '' ?>"><a href="<?=$url?>&content_type=album">ALBUM</a></li>
            </ul>
        </div>
        <div style="clear:both">

        </div>
        <?php
    }

    function setting_form($type, $posts, $option_enable, $option_order, $requiredCnt = -1){
        ?>

        <div class="container">

            <form id="form" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ; ?>">
                <h1><?=$type?></h1>
                <button class="btn-submit">APPLY PROMOTION</button>
                <input type="hidden" id="required_cnt" value="<?=$requiredCnt?>">
                <input type="hidden" id="curCheckCnt" value="<?=count($option_enable)?>">

                <table class="widefat fixed" cellpadding="10">
                    <thead>
                        <tr>
                            <th id="cb" class="manage-column column-cb check-column" scope="col"></th>
                            <th width="10%" id="thumb" class="manage-column column-thumb">THUMB</th>
                            <th width="10%" id="columnname" class="manage-column column-columnname" scope="col">POST ID</th>
                            <th id="columnname" class="manage-column column-columnname" scope="col">TITLE</th>
                            <th width="15%" id="columnname" class="manage-column column-columnname" scope="col">DATE</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($posts as $key => $post){
                            $postId = $post->ID;

                            $trClass = $key % 2 == 0 ? 'alternate' : '';

                            $title = strlen($post->post_title) > 115 ? substr($post->post_title, 0 , 115).'...' : $post->post_title ;

                            $image = $this->getImage($type, $post);
                        ?>
                            <tr class="<?=$trClass?>">
                                <th class="check-column" scope="row">
                                    <input type="checkbox" id="enable_<?=$postId?>" name="enable[<?=$postId?>]"
                                        <?=isset($option_enable[$postId]) == true ? 'checked' : '' ?>
                                        value="<?= $post->post_type ?>"
                                    />
                                </th>

                                <td class="thumb"><img class="thumb-image" src="<?=$image?>" alt=""></td>


                                <td>
                                    <div><?=$postId?></div>
                                    <div style="margin-top: 10px">
                                        <input name="order[<?=$postId?>]" id="order_<?=$postId?>" type="number"
                                                <?=isset($option_enable[$postId]) == true ? '' : 'disabled'?>
                                               value="<?=isset($option_enable[$postId]) == true ? $option_order[$postId] : '' ?>"
                                        />
                                    </div>
                                </td>
                                <td>
                                    <div><?=$title?></div>
                                    <div style="margin-top: 10px">
                                        <a href="/wp-admin/post.php?post=<?=$postId?>&action=edit">Edit Contents</a>
                                    </div>
                                </td>
                                <td class="text-align__center">2016/09/14</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <button class="btn-submit" style="margin-top:6px"> APPLY PROMOTION</button>

            </form>
        </div>
<?php

    }

    function getImage($type, $post){

        $imageUrl = '';
        $noImage = plugins_url( 'assets/images/placeholder.png', __FILE__ );

        switch($type){
            case 'HOT TRACK':
                $albumId = get_field('album', $post->ID);
                if(is_array($albumId)){
                    $imageUrl = get_field('thumbnail', $albumId[0]);
                }

                break;

            case 'HOT BLOG':
                $imageUrl = get_field('main_image', $post->ID);
                break;


            case 'PRODUCT':
                $imageUrl = get_field('thumbnail_2x2', $post->ID);
                break;

            case 'EVENT':
                $imageUrl = get_field('main_image', $post->ID);
                break;

            case 'TOUR':
                $imageUrl = get_field('main_image', $post->ID);
                break;

            case 'ALBUM':
                $imageUrl = get_field('thumbnail', $post->ID);
                break;

            default:
                break;

        }
        return $imageUrl ?: $noImage;
    }
}


$promotion_settings = new PromotionSettings();