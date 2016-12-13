<?php

/**
 * Created by PhpStorm.
 * User: Zeter
 * Date: 11/10/2016
 * Time: 10:29 PM
 */
class Banner
{

    const VERSION = '1.0.0';


    public $slug = 'banner';


    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_banner_menu_page'));
        add_action('admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }


    public function enqueue_admin_scripts(){
        wp_enqueue_media();
        $src = plugins_url( 'assets/js/banner.js', __FILE__ );
        wp_enqueue_script($this->slug.'-function', $src , array() , self::VERSION);
    }

    public function add_banner_menu_page(){

        add_menu_page('Banner', 'Banner', 'edit_users', 'banner-manager', array($this, 'get_banner'));
    }


    public function get_banner(){
        $type = isset($_GET['banner_type']) ? $_GET['banner_type'] : 'main-tile';

        $option = get_option('main-top-banner');


        $this->set_top_navi(strtoupper($type));

        $this->view(strtoupper($type));

    }


    function set_top_navi($type){
        $url = admin_url().'admin.php?page=banner-manager';
        ?>

        <div class="top-navi">
            <ul>
                <li class="<?= $type == 'MAIN-TILE' ? 'active' : '' ?>"><a href="<?=$url?>&banner_type=main-tile">MAIN TILE</a></li>
                <li class="<?= $type == 'BLOG-SIDE' ? 'active' : '' ?>"><a href="<?=$url?>&banner_type=blog-side">BLOG SIDE</a></li>
            </ul>
        </div>
        <div style="clear:both">
        </div>
        <?php
    }


    public function view($type){?>
        <div style="width: 60%">
            <form name="form" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ; ?>">
                <h1><?=$type?></h1>
                <button class="btn-submit banner">APPLY BANNER</button>

                <table class="widefat fixed" cellpadding="10">
                    <thead>
                    <tr>
                        <th id="cb" class="manage-column column-cb check-column" scope="col"></th>
                        <th width="15%" id="thumb" class="manage-column column-thumb"><strong>THUMB</strong></th>
                        <th width="40%" id="columnname" class="manage-column column-columnname" scope="col"><strong>IMAGE PATH</strong></th>
                        <th width="40%" id="columnname" class="manage-column column-columnname" scope="col"><strong>TARGET LINK</strong></th>
                    </tr>
                    </thead>
                    <tbody>
                        <th class="check-column" scope="row">
                            <input type="checkbox" />
                        </th>
                        <td class="thumb"><img class="thumb-image banner" id='thumb-image' src="<?= $image = plugins_url('assets/images/placeholder.png', __FILE__);?>" ></td>
                        <td>
                            <div class="image-path-container">
                                <div>
                                    <input style="width: 90%" type="text" id="image-url" name="image-url" value="">
                                </div>
                                <div>
                                    <span class="instruction-text">* Recommended Size : 1920 x 1080 </span>
                                </div>
                                <div style="margin-top: 25px;">
                                    <input class="btn-image-upload" id="btn-image-upload" type="button" class="button" value="Upload Image" />
                                    <span class="size-info"></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <input style="width: 90%" type="text" id="banner-link" name="banner-link" value="">
                            </div>

                            <div>
                                <span class="instruction-text">* Please input full URL of target page </span>
                            </div>
                        </td>

                    </tbody>
                </table>
                <button class="btn-submit promotion" style="margin-top:6px">APPLY BANNER</button>
            </form>
        </div>
        <?php
    }
}


//$banner = new Banner();