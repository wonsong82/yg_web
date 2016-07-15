<?php
	global $music_store_settings;
	
	if( !defined( 'MS_H_URL' ) ) { echo 'Direct access not allowed.';  exit; }	
	function make_seed() {
		list($usec, $sec) = explode(' ', microtime());
		return (float) $sec + ((float) $usec * 100000);
	} 
	
	mt_srand(make_seed());
	$randval = mt_rand(1,999999);
	$purchase_id = md5($randval.uniqid('', true));
	
	if( preg_match( '/^(http(s)?:\/\/[^\/\n]*)/i', MS_H_URL, $matches ) && strpos( $_SERVER['HTTP_REFERER'], $matches[ 0 ] ) ) $host = $_SERVER['HTTP_REFERER'];
	if(empty($host))
		$host = MS_H_URL;
		
	if(isset($_POST['ms_product_id']) && isset($_POST['ms_product_type'])){
		$obj = new MSSong($_POST['ms_product_id']);
		
		$ms_paypal_email = $music_store_settings[ 'ms_paypal_email' ];
		
		if(isset($obj->ID) && $ms_paypal_email){ // Check object existence and saler email
			$currency = $music_store_settings[ 'ms_paypal_currency' ];
			$language = $music_store_settings[ 'ms_paypal_language' ];
			
			$cost = $obj->price;
			if($cost > 0){ // Check for a valid cost
			
				$baseurl = MS_H_URL.'?ms-action=ipn';
				
                $returnurl = $GLOBALS['music_store']->_ms_create_pages( 'ms-download-page', 'Download Page' );
                $returnurl .= ( ( strpos( $returnurl, '?' ) === false ) ? '?' : '&' ).'ms-action=download';

				$code = '<form action="https://www.'.( ( $music_store_settings[ 'ms_paypal_sandbox' ] ) ? 'sandbox.' : '' ).'paypal.com/cgi-bin/webscr" name="ppform'.$randval.'" method="post">'.
				'<input type="hidden" name="business" value="'.esc_attr($ms_paypal_email).'" />'.
				'<input type="hidden" name="item_name" value="'.esc_attr($obj->post_title).'" />'.
				'<input type="hidden" name="item_number" value="Item Number '.esc_attr($obj->ID).'" />'.
				'<input type="hidden" name="amount" value="'.esc_attr($cost).'" />'.
				'<input type="hidden" name="currency_code" value="'.esc_attr($currency).'" />'.
				'<input type="hidden" name="lc" value="'.esc_attr($language).'" />'.
				''.
				'<input type="hidden" name="return" value="'.$returnurl.'&purchase_id='.$purchase_id.'" />'.
				'<input type="hidden" name="cancel_return" value="'.$host.'" />'.
				'<input type="hidden" name="notify_url" value="'.$baseurl.'&id='.$obj->ID.'&purchase_id='.$purchase_id.'&rtn_act=purchased_product_music_store" />'.
				''.
				'<input type="hidden" name="cmd" value="_xclick" />'.
				'<input type="hidden" name="page_style" value="Primary" />'.
				'<input type="hidden" name="no_shipping" value="1" />'.
				'<input type="hidden" name="no_note" value="1" />'.
				'<input type="hidden" name="bn" value="NetFactorSL_SI_Custom" />'.
				'<input type="hidden" name="ipn_test" value="1" />'.
				'</form>'.
				'<script type="text/javascript">document.ppform'.$randval.'.submit();'.'</script>';
				echo $code;
				exit;
			} // End if cost
		} // End if saler and object
	} // End if parameters
	
	header('location: '.$host);
?>