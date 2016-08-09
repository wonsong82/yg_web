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
    }


    function get_main_contents() {

        echo "<h1>" . 'Main Contents List' . "</h1>";
        echo "<h4>" . 'Please select promotional items that need to appear on Main Promotional Page' . "</h4>";

        foreach($this->options as $option){

            $page_type = explode('_' , $option)[0];
            $post_type = explode('_' , $option)[1];

            if($page_type != 'main')
                break;

            $query_arg = array(
                'post_type' => $post_type,
                'post_status' => 'publish',
                'posts_per_page' => -1,
            );

            if($option == $this->main_product){
                $query_arg = array_merge($query_arg , array(
                    'meta_query' => array(
                        array(
                            'key' => '_downloadable',
                            'value' => 'no'
                        )
                    )
                ));
            }

            $posts = get_posts($query_arg);
            $this->setting_form($option.'-group', $option.'_enable' , $option.'_order', $posts);
        }
    }

    function get_hot_track() {

        echo "<h1>" . 'Hot Track List' . "</h1>";
        echo "<h4>" . 'Please select music items that need to appear on Hot Track List' . "</h4>";

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

        $this->setting_form($this->hot_track.'-group', $this->hot_track.'_enable' , $this->hot_track.'_order', $posts);

    }

    function get_hot_blog(){

        echo "<h1>" . 'Hot Blog List' . "</h1>";
        echo "<h4>" . 'Please select Blog that need to appear on Hot Blog List' . "</h4>";

        $posts = get_posts([
            'post_type' => 'blog',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ]);

        $this->setting_form($this->hot_blog.'-group', $this->hot_blog.'_enable' , $this->hot_blog.'_order', $posts);
    }

    function no_post_message($ret){

    }

    function setting_form($option_group, $enable, $order, $posts){

        if(count($posts) == 0 ) return false;

        $enable_val = get_option($enable);
        $order_val = get_option($order);
        ?>
        <form method="post" action="options.php">
            <?php settings_fields($option_group); ?>
            <table class="table-style-two" width="40%">
                <?php
                foreach ($posts as $key => $post) {
                    $id = $post->ID;
                    ?>
                    <?php if($key == 0){?>
                        <tr><th colspan="4"><?= strtoupper($post->post_type) ?></th></tr>
                    <?php } ?>
                    <tr>
                        <td width="5%"><input name="<?=$enable?>[<?=$id?>]" type="checkbox"
                                <?=isset($enable_val[$id]) == '1' ? 'checked' : '' ?> value="<?= $post->post_type ?>"/></td>
                        <td width="5%"><input name="<?=$order?>[<?=$id?>]" type="number" value="<?=$order_val[$id]?>"</td>
                        <td width="30%"><?=$post->post_title?></td>
                        <td><?=$post->post_content?></td>
                    </tr>
                <?php } ?>
            </table>
            <?php submit_button(); ?>
        </form>
        <?php
    }
}


$promotion_settings = new PromotionSettings();