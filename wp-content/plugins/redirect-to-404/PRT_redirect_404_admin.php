<div class="wrap red_404">
    <?php    echo "<h2>" . __( 'Redirect 404 Settings', 'PRT_redirect_404' ) . "</h2>";  ?>
    
    <?php 
		if (current_user_can( 'manage_options' ) ) {
			if(get_option('PRT_redirect_404_pageUrl', null) == null)
				{
					 update_option('PRT_redirect_404_pageUrl', home_url());
				}
			
			if(isset($_POST['Submit'])){
				
				check_admin_referer('update-setting');
				
				update_option('PRT_redirect_404_pageUrl', esc_url($_POST['PRT_redirect_404_pageUrl']));
				update_option('PRT_redirect_404_status', sanitize_text_field($_POST['PRT_redirect_404_status']));
			} 
		}
	 ?>
     
    <form name="PRT_redirect_404_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <?php wp_nonce_field('update-setting'); ?>
        <div class="row">
		<div class="col-sm-3"><?php _e("Enable Redirection 404: " ); ?></div>
        <div class="col-sm-4">
        <select name="PRT_redirect_404_status" class="form-control">
        	<option value="1" <?php if(get_option('PRT_redirect_404_status') ==1){ echo "selected"; } ?> >Yes </option>
            <option value="0" <?php if(get_option('PRT_redirect_404_status') ==0){ echo "selected"; } ?>> No </option>
        </select>
        </div>
        </div>

        <div class="row">
		<div class="col-sm-3"><?php _e(" All 404 Page Redirect to: " ); ?></div>
        <div class="col-sm-4">
        <input type="text" class="form-control" name="PRT_redirect_404_pageUrl" value="<?php echo get_option('PRT_redirect_404_pageUrl'); ?>" >
        </div>
        </div>
         
     
        <div class="row">
		<div class="col-sm-3"></div>
        <div class="col-sm-4">
        <input type="submit" class="btn btn-info" name="Submit" value="<?php _e('Update Options', 'PRT_redirect_404' ) ?>" />
        </div>
        </div>
    </form>
    
    
    
</div>