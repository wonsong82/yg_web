<?php 
class WCST_Order
{
	public function __construct()
	{
		add_action('wp_ajax_wcst_get_order_list', array(&$this, 'ajax_get_order_partial_list'));
		add_action('wp_ajax_wcst_upload_tracking_csv', array(&$this, 'process_csv_upload_ajax'));
	}
	function process_csv_upload_ajax()
	{
		
		$csv_array = explode("<#>", $_POST['csv']);
		$result = $this->process_csv_data_and_update_orders($csv_array);
	
		foreach((array)$result as $message)
				echo $message;
		wp_die();
	}
	private function process_csv_data_and_update_orders($csv_array = null)
	{
		$customerAdded = 0;
		$messages = array(); 
		$order_statuses = wc_get_order_statuses();
		$allowed_email_notification_statuses = array("send_email_new_order,send_email_cancelled_order",
													 "send_email_customer_processing_order",
													 "send_email_customer_completed_order",
													 "send_email_customer_refunded_order",
													 "send_email_customer_invoice" );
		$columns_names = array("order_id",
								"order_status",
								"force_email_notification",
								"dispatch_date",
								"custom_text",
								"tracking_info");
		$colum_index_to_name = array();
		
		$row = 1;
		$updated = 0;
		if($csv_array != null)
		{
			//while (($data = fgetcsv($handle)) !== FALSE) 
			foreach($csv_array as $csv_row)
			{
				//wccm_var_dump($csv_row);
				if(empty($csv_row) || $csv_row == "")
					continue;
				$data = str_getcsv($csv_row);
				$num = count($data);
				$order = array();
				
				for ($c=0; $c < $num; $c++) 
				{						
					if($row == 1)
					{
						foreach( $columns_names as $title)
							if($title == $data[$c])
									$colum_index_to_name[$c] = $title;
					}
					else
					{
						if(isset($colum_index_to_name[$c]))
						{
							$order[$colum_index_to_name[$c]] = $data[$c];
						}
					}
					
				}
				if($order != null)
				{
					//Order id
					$is_order_id_valid =   !isset($order['order_id'] ) || $order['order_id'] == "" || empty($order['order_id'] ) || !is_numeric($order['order_id']) ? false : true; 
					if($is_order_id_valid)
					{
						$order_object = new WC_Order($order['order_id']);
						$is_order_id_valid =  !isset($order_object->post) ? false : $is_order_id_valid;
					}
					if(!$is_order_id_valid)
						array_push( $messages, '<span class="error_message">'.sprintf(__("Order %s: the id is not valid.", 'woocommerce-shipping-tracking'), $order[ 'order_id' ]).'</span><br/>' );
					
					//Order status
					$is_status_valid = !isset($order['order_status'] ) || $order['order_status'] == "" || empty($order['order_status'] ) ? true : false; 
					foreach($order_statuses as $code => $status_name)
						if($order['order_status'] == $code)
							$is_status_valid = true;
					if(!$is_status_valid)	
					{
						//array_push( $messages, new WP_Error('order', sprintf(__("Order %s selected status was not valid.", 'woocommerce-shipping-tracking'), $order[ 'order_id' ])));
						array_push( $messages, '<br/><span class="error_message">'.sprintf(__("Order %s: selected status was not valid. Its status has been left unchanged.", 'woocommerce-shipping-tracking'), $order[ 'order_id' ]).'</span><br/>' );
						//wcst_var_dump($messages);
					}
					
					//Forced email notification
					$is_notification_email_status_valid = !isset($order['force_email_notification'] ) || $order['force_email_notification'] == "" || empty($order['force_email_notification'] ) ? true : false; 
					$is_notification_email_status_valid = in_array( $order['force_email_notification'], $allowed_email_notification_statuses) ? true : $is_notification_email_status_valid;
					
					if(!$is_notification_email_status_valid)	
						array_push( $messages, '<span class="error_message">'.sprintf(__("Order %s: notification email status was not valid. No notification has been sent.", 'woocommerce-shipping-tracking'), $order[ 'order_id' ]).'</span><br/>' );
				
					//Track info
					$tracking_info_strings = explode("|", $order['tracking_info']);
					$company_id_and_tracking_code = array();
					foreach((array)$tracking_info_strings as $tracking_info_string)
					{
						$temp = explode(":", $tracking_info_string);
						array_push($company_id_and_tracking_code, array('company_id' => $temp[0], 'tracking_code' => $temp[1]));
					}
				
					//Custom text
					$custom_texts = explode("|",$order['custom_text']);
					//Dispatch date
					$dispatch_dates = explode("|",$order['dispatch_date']);
					
					if(empty($company_id_and_tracking_code))
						array_push( $messages, '<span class="error_message">'.sprintf(__("Order %s: tacking info is not valid.", 'woocommerce-shipping-tracking'), $order[ 'order_id' ]).'</span><br/>' );
					else
					{
						//Save info 
						global $wcst_email_model;
						$meta_data_array = array();
						$meta_data_array['_wcst_order_trackurl'] = $company_id_and_tracking_code[0]['company_id'];
						$meta_data_array['_wcst_order_trackno'] = $company_id_and_tracking_code[0]['tracking_code'];
						$meta_data_array['_wcst_order_dispatch_date'] = $dispatch_dates[0];
						$meta_data_array['_wcst_custom_text'] = $custom_texts[0];
						
						if(count($company_id_and_tracking_code) > 1 )
						{
							$additiona_company = array();
							$meta_data_array['_wcst_order_additional_shipping'] = array();
							for($i = 1; $i < count($company_id_and_tracking_code); $i++)
							{
								$additiona_company['trackurl'] = isset($company_id_and_tracking_code[$i]['company_id']) ? $company_id_and_tracking_code[$i]['company_id'] : "";
								$additiona_company['trackno'] = isset($company_id_and_tracking_code[$i]['tracking_code']) ? $company_id_and_tracking_code[$i]['tracking_code'] : "";
								$additiona_company['order_dispatch_date'] = isset($dispatch_dates[$i]) ? $dispatch_dates[$i] : "";
								$additiona_company['custom_text'] = isset($custom_texts[$i]) ? $custom_texts[$i] : "";
							}
							array_push($meta_data_array['_wcst_order_additional_shipping'], $additiona_company);
						}
						
						//wcst_var_dump($meta_data_array);
						if($is_order_id_valid)
						{
							$this->save_shippings_info_metas($order['order_id'], $meta_data_array);
							
			
							if($is_status_valid && $order['order_status'] != "" )
							{
								foreach($order_statuses as $code => $status_name)
									if($order['order_status'] == $code)
										$order_object->update_status($order['order_status']);
							}
							if($is_notification_email_status_valid && $order['force_email_notification'] != "")
							{
								//wcst_var_dump($order['force_email_notification']);
								$wcst_email_model->force_status_email_sending($order['force_email_notification'], $order_object);
							} 
						}
					}
				}
				$row++;
			}
			//array_push( $messages, sprintf(__('Updated %d orders!', 'woocommerce-shipping-tracking'),  $updated  ));
			
		}
		//wcst_var_dump($messages);
		return $messages;
	}
	public function get_delivery_and_times($order_id)
	{
		$result =  get_post_meta($order_id, '_wcst_order_delivery_datetimes' , true);
		return isset($result) && $result != "" ? $result : array();
	}
	public function save_delivery_date_and_time($order_id, $date_and_time)
	{
		return update_post_meta($order_id, '_wcst_order_delivery_datetimes',$date_and_time);
	}
	 
	public function ajax_get_order_partial_list()
	{
		$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
		$orders = $this->get_order_list($order_id );
		 echo json_encode( $orders);
		 wp_die();
	}
	private function get_order_list($search_string = null)
	{
		global $wpdb;
		 $query_string = "SELECT orders.ID as order_id, orders.post_date as order_date, orders.post_status as order_status
							 FROM {$wpdb->posts} AS orders
							 WHERE orders.post_type = 'shop_order' ";
		if($search_string)
				$query_string .=  " AND ( orders.ID LIKE '%{$search_string}%' OR  orders.post_date LIKE '%{$search_string}%' OR orders.post_status LIKE '%{$search_string}%')";
		
		$query_string .=  " GROUP BY orders.ID ORDER BY orders.post_date DESC";
		return $wpdb->get_results($query_string );
	}
	public function is_email_tracking_info_embedding_disabled($order_id)
	{
		$result = get_post_meta( $order_id, '_wcst_order_disable_email', true);
		//wcst_var_dump($result);
		return isset($result) && $result == 'disable_email_embedding' ? true : false;
	}
	public function save_shippings_info_metas($post_id, $data_to_save)
	{
		global $wcst_shipping_company_model;
		$order = new WC_Order($post_id);
		/* $post_code = stripslashes( $data_to_save['_billing_postcode']);
		if(isset( $data_to_save['_shipping_postcode']) && !empty( $data_to_save['_shipping_postcode']))
			$post_code = stripslashes( $data_to_save['_shipping_postcode']); */
		$post_code = isset($order->shipping_postcode) && $order->shipping_postcode != "" ? $order->shipping_postcode : $order->billing_postcode;
		
		$info = WCST_shipping_companies_url::get_company_url(stripslashes( $data_to_save['_wcst_order_trackurl'] ), stripslashes( $data_to_save['_wcst_order_trackno'] ), $post_code );
		add_post_meta( $post_id, '_order_key', uniqid('order_') );
		update_post_meta( $post_id, '_wcst_order_trackno', stripslashes( $data_to_save['_wcst_order_trackno'] ));
		update_post_meta( $post_id, '_wcst_order_dispatch_date', stripslashes( $data_to_save['_wcst_order_dispatch_date'] ));
		update_post_meta( $post_id, '_wcst_custom_text', stripslashes( $data_to_save['_wcst_custom_text'] ));
		update_post_meta( $post_id, '_wcst_order_trackname', stripslashes( $wcst_shipping_company_model->get_company_name_by_id($data_to_save['_wcst_order_trackurl']) ));
		update_post_meta( $post_id, '_wcst_order_trackurl', stripslashes( $data_to_save['_wcst_order_trackurl'] ));
		update_post_meta( $post_id, '_wcst_order_track_http_url', stripslashes( $info['urltrack'] ));
		update_post_meta( $post_id, '_wcst_order_disable_email', $data_to_save['_wcst_order_disable_email']);
		
		//additional
		if(!isset($data_to_save['_wcst_order_additional_shipping']))
		{
			delete_post_meta( $post_id, '_wcst_additional_companies' );
			return;
		}
		
		$addtional_companies_counter = 0;
		$additiona_companies = array();
		foreach($data_to_save['_wcst_order_additional_shipping'] as $additional_company)
		{
			$temp = array();
			$info = WCST_shipping_companies_url::get_company_url(stripslashes( $additional_company['trackurl'] ), stripslashes( $additional_company['trackno'] ), $post_code );
			$temp['_wcst_order_trackno'] = $additional_company['trackno'] ;
			$temp['_wcst_custom_text'] = $additional_company['custom_text'] ;
			$temp['_wcst_order_dispatch_date'] = $additional_company['order_dispatch_date'] ;
			$temp['_wcst_order_trackname'] = stripslashes( $wcst_shipping_company_model->get_company_name_by_id($additional_company['trackurl']) );
			$temp['_wcst_order_trackurl'] = stripslashes( $additional_company['trackurl']);
			$temp['_wcst_order_track_http_url'] = stripslashes( $info['urltrack']);
			array_push($additiona_companies, $temp);
		}
		update_post_meta( $post_id, '_wcst_additional_companies', $additiona_companies );
	}
}
?>