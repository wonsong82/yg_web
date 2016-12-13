<?php
/*
Plugin Name: Media Uploader
Plugin URI:
Description: 워드프레스 Media Uploader 입니다.
Version: 0.1
Author: MU
Author URI:
*/

/* 미디어 업로더 스크립트 추가 */
function my_media_lib_uploader_enqueue() {
	wp_enqueue_media();
	wp_register_script( 'media-lib-uploader-js', plugins_url( 'media-upload-script.js' , __FILE__ ), array('jquery') );
	wp_enqueue_script( 'media-lib-uploader-js' );
}
add_action('admin_enqueue_scripts', 'my_media_lib_uploader_enqueue');


/* 관리자 화면 > 외모에  이라는 하위 메뉴 추가 */
function my_plugin_menu() {
	add_menu_page('Media Uploader', 'Media Uploader', 'manage_options', 'media-uploder-handle', 'media_uploader_function');
}

/** Step 2 위에서 작성한 기능을 admin_menu 훅에 등록 */
add_action( 'admin_menu', 'my_plugin_menu' );

/** Step 3. Media Uploader 메뉴를 눌렀을 때 나오는 화면의 HTML 작성 */
function media_uploader_function() {
	//must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('이 페이지에 접근할 권한이 없습니다.') );
    }

    // variables for the field and option names 
    $db_field_name = 'wj_popup_url';
    $hidden_field_name = 'mt_submit_hidden';
    $form_field_name = 'wj_popup_url';

    // Read in existing option value from database
    $db_val = get_option( $db_field_name);

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $db_val = $_POST[ $form_field_name ];

        // 폼에서 작성한 내용을 데이터베이스에 저장
        update_option( $db_field_name, $db_val );

        // Put a "settings saved" message on the screen

?>
<div class="updated"><p><strong><?php _e('저장했습니다.', 'menu-test' ); ?></strong></p></div>
<?php

    }

    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>Media Uploader</h2>";

    // settings form
    
    ?>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<img id="prv-img" src="<?php echo $db_val; ?>">

<p>이미지 url
<input type="text" id="image-url" name="<?php echo $form_field_name; ?>" value="<?php echo $db_val; ?>" size="40">
<input id="upload-button" type="button" class="button" value="Upload Image" />
</p><hr />

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="저장" />
</p>

</form>
</div>


<?php
}
?>