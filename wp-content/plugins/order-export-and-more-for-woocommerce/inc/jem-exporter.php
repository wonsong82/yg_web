<?php
class JEM_export_lite {


	private $objects = array();
	private $my_errors = "";
	private $settings;
	
	public function __construct(){	

		//Globals
		global $jem_export_globals;
		
		add_action( 'admin_menu', array( $this, 'add_to_menu' ), 99 );

		
		//handles the post form for the labels
		add_action('admin_post_update_labels', array( &$this, 'update_labels'));
		add_action('admin_enqueue_scripts', array( &$this, 'load_scripts'));
		add_action('admin_post_export_data', array( &$this, 'export_data'));
		
		//handles the form post for the SETTINGS
		add_action('admin_post_save_settings', array( &$this, 'save_settings'));
		
		//load up all the entity classes
		$entities = $jem_export_globals['entities'];
		
		//$entities[] = "Product";
		//$entities[] = "Order";
		
		foreach($entities as $entity){
			//create the object
			$ent = new $entity;
			//stick it in an array
			$this->objects[$ent->id] = $ent;
		}
		
		//get the settings
		$this->get_settings();
		
		//create the error object
		$this->my_errors = new WP_Error();
	}
	
	
	/**
	 * Load up the stuff we need!
	 */
	public function load_scripts(){
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery-ui-datepicker');
		
	}
	
	/**
	 * Gets the settings, sets defaults etc
	 */
	public function get_settings(){
		$this->settings = get_option(JEM_EXP_DOMAIN);
		
		
		//ok lets check they are set, if not lets set them to defaults
		//we use this array, makes it very easy to add new ones
		$defaults= array(
				"filename"		=> "woo-export.csv",
				"date_format" 	=> "Y/m/d",
				"encoding" 	=> "UTF-8",
				"delimiter"		=> ","
				
		);
		
		foreach($defaults as $key => $value){
			if(empty($this->settings[$key])){
				$this->settings[$key] = $value;
			}	
		}
	}
	/**
	 * This puts us on the woo menu
	 */
	public function add_to_menu() {


		
		$this->page = add_submenu_page(
			'woocommerce',
			__( 'WooCommerce Order Export and More', JEM_EXP_DOMAIN ),
			__( 'Order Export +', JEM_EXP_DOMAIN ),
			'manage_woocommerce',
			'JEM_EXPORT_MENU',
			array( $this, 'render_settings' )
		);
	}


	/**
	 * This renders the main page for the plugin - all the front-end fun happens here!!
	 */
	public function render_settings(){



		//get the main tab
		if( isset($_REQUEST['tab'])){
				
			//so get the tab from the url...
			$tab = $_REQUEST['tab'];
		} else {
			//no tab default to export
			$tab = "export";
		}
		
		//get the sub-tab
		if( isset($_REQUEST['sub-tab'])){
				
			//so get the tab from the url...
			$subTab = $_REQUEST['sub-tab'];
		} else {
			//no sub-tab default to fields
			$subTab = "fields";
		}
		
		//are we editing an entity? if not default to Product
		if( isset($_REQUEST['entity'])){
				
			$entity = $_REQUEST['entity'];
		} else {
			//default
			$entity = "Product";
		}		
		
		//set the active tabs to blank
		$export_active = "";
		$settings_active = "";
		$meta_active ="";
		
		
		//get the tab data for this tab
		
		$content = "";
		switch($tab) {
			case 'settings':
				$content = $this->generate_settings_tab($subTab);
				$settings_active = "nav-tab-active";
				break;

			case 'meta':
				$content = $this->generate_meta_tab($subTab);
				$meta_active = "nav-tab-active";
				break;

			//default to export
			default:
				$content = $this->generate_export_tab($subTab);
				$export_active = 'nav-tab-active';
				break;
					
		
		}
		
		
		//The basic html for our page
		
		
		//check if we have a message

		
		$html = '<div class="wrap">
					<h2>WooCommerce Order Export and More</h2>' . $this->print_admin_messages() . '

							
				<!--  begin email -->

<!-- Begin MailChimp Signup Form -->
<link href="//cdn-images.mailchimp.com/embedcode/classic-081711.css" rel="stylesheet" type="text/css">
<style type="text/css">
	#mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
	/* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
	   We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
	   #optin {
	background: #dde2ec;
	border: 2px solid #1c3b7e;
	/* padding: 20px 15px; */
	text-align: center;
	width: 800px;
}
	#optin input {
		background: #fff;
		border: 1px solid #ccc;
		font-size: 15px;
		margin-bottom: 10px;
		padding: 8px 10px;
		border-radius: 3px;
		-moz-border-radius: 3px;
		-webkit-border-radius: 3px;
		box-shadow: 0 2px 2px #ddd;
		-moz-box-shadow: 0 2px 2px #ddd;
		-webkit-box-shadow: 0 2px 2px #ddd
	}
		#optin input.name { background: #fff url("' . JEM_EXP_URL . '/images/name.png") no-repeat 10px center; padding-left: 35px }
		#optin input.myemail { background: #fff url("' . JEM_EXP_URL .'images/email.png") no-repeat 10px center; padding-left: 35px }
		#optin input[type="submit"] {
			background: #217b30 url("' . JEM_EXP_URL. '/images/green.png") repeat-x top;
			border: 1px solid #137725;
			color: #fff;
			cursor: pointer;
			font-size: 14px;
			font-weight: bold;
			padding: 2px 0;
			text-shadow: -1px -1px #1c5d28;
			width: 120px;
			height: 38px;
		}
			#optin input[type="submit"]:hover { color: #c6ffd1 }
		.optin-header{
			font-size: 24px;
			color: #ffffff;
			background-color: #1c3b7e;
			padding: 20px 15px;
		}
		#jem-submit-results{
			padding: 10px 0px;
			font-size: 24px;
		}
</style>
<div id="optin">
<form action="//jem-products.us12.list-manage.com/subscribe/post?u=6d531bf4acbb9df72cd2e718d&amp;id=e70736aa58" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
    <div id="mc_embed_signup_scroll">
	<div class="optin-header">Upgrade to Pro - get a 20% Discount Coupon</div>
<div class="mc-field-group" style="padding: 20px 15px;; text-align: left;">
	<input type="text" value="Enter your email" size="30" name="EMAIL" class="myemail" id="mce-EMAIL" onfocus="if(this.value==this.defaultValue)this.value=\'\';" onblur="if(this.value==\'\')this.value=this.defaultValue;"
	>
	<input type="text" value="Enter your name" size="30" name="FNAME" class="name" id="mce-FNAME" onfocus="if(this.value==this.defaultValue)this.value=\'\';" onblur="if(this.value==\'\')this.value=this.defaultValue;"
	>
<input type="submit" value="Get Discount" name="subscribe" id="" class="button">			
	</div>
	<div id="mce-responses" class="clear">
		<div class="response" id="mce-error-response" style="display:none"></div>
		<div class="response" id="mce-success-response" style="display:none"></div>
	</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_6d531bf4acbb9df72cd2e718d_de987ac678" tabindex="-1" value=""></div>
    <div class="clear"><img src="' . JEM_EXP_URL . '/images/lock.png">We respect your privacy and will never sell or rent your details</div>
    </div>
</form>
</div>
				
				<!--  end email -->

							
							
							
							
				
					<div id="jem-content">
					<h2 class="nav-tab-wrapper">
						<a data-tab-id="export" class="nav-tab ' . $export_active . '" href="admin.php?page=JEM_EXPORT_MENU&amp;tab=export-data"">Export Data</a>
						<a data-tab-id="setting" class="nav-tab ' . $settings_active . '" href="admin.php?page=JEM_EXPORT_MENU&amp;tab=settings">Settings</a>
						<a data-tab-id="meta" class="nav-tab ' . $meta_active . '" href="admin.php?page=JEM_EXPORT_MENU&amp;tab=meta">Meta</a>
					</h2>
					</div>
			
				</div>';
		
		
		//add on the content for this specific tab and voila we are off to the races...
		$html = $html .  $content;
		echo $html;
		
		
		//now add in the jscript to select the approriate entity, tab & sub-tab & entity
		$html =  '
			<div class="hidden" style="display: none;" id="current-tab">' . $tab . '</div>
			<div class="hidden" style="display: none;" id="current-sub-tab">' . $subTab . '</div>
			<div class="hidden" style="display: none;" id="current-entity">' . $entity . '</div>
					<script>
				tab = "' . $tab . '";
				subTab = "' . $subTab . '";
			</script>
		';
		
		echo $html;
	}
	
	/**
	 * This generates the screen for the export tab
	 */
	function generate_export_tab($subTab){

		//the wrapper
		$ret = '<div id="jem-export-post" class="wrap">';
		
		//First create the list of entities you can export....
		
		$ret .= $this->generate_entity_list();
				
				
				
		//create the vertical tabs
		$ret .= '
<div id="export-tabs" class="postbox">

	<div class="jem-vert-header">
		<h3 class="hndle" id="entity-type-title">Title goes Here</h3>
	</div>

  <div class="vert-tabs-container">
				

	<div class="jemex-panel-wrap">			
		<ul class="jemex-vert-tabs">
		  <li><a href="#field-tab" id="vert-field-tab" class="dashicons-before dashicons-media-spreadsheet">Fields</a></li>
		  <li><a href="#filters-tab" id="vert-filter-tab"  class="dashicons-before dashicons-filter">Filters</a></li>
		  <li><a href="#labels-tab" id="vert-label-tab" class="dashicons-before dashicons-tag">Labels</a></li>
		  <li><a href="#scheduled-tab" id="vert-scheduled-tab" class="dashicons-before dashicons-clock">Scheduled</a></li>
								</ul>
		<div id="field-tab" class="jemex-panel">
		<div class="jemex-inner-panel">
		' . $this->generate_fields_tab() . '
		</div>
		</div>
		<div id="filters-tab" class="jemex-panel">
		<div class="jemex-inner-panel">
		' . $this->generate_filters_tab() . '
		</div>
		</div>
				<div id="labels-tab" class="jemex-panel">
		<div class="jemex-inner-panel">
				' . $this->generate_labels_tab() . '
		</div>
		</div>
		<div id="scheduled-tab" class="jemex-panel">
		<div class="jemex-inner-panel">
				' . $this->generate_scheduled_tab() . '
		</div>
		</div>						
	</div>
			
  </div>				

			</div>
</div> <!-- end wrap -->
				';
		
		return $ret;
	}
	

	/**
	 * This generates the screen for the settings - HORIZONTAL tabs
	 */
	function generate_settings_tab($subTab){
		
		
		//Trying out output buffering
		ob_start();
				
		include_once('templates/tab-settings.php');
		
		$html = ob_get_clean();
		
		
		return $html;

		
	}
	/**
	 * This generates the screen for the META - HORIZONTAL tabs
	 */
	function generate_meta_tab($subTab){


		//Trying out output buffering
		ob_start();

		include_once('templates/tab-meta.php');

		$html = ob_get_clean();


		return $html;


	}

	/**
	 * This generates the fields screen - VERTICAL tab
	 */
	function generate_fields_tab(){
		//Globals
		global $jem_export_globals;
		//we create a set of divs for each entity
		
		$html = "<p class='instructions'>" . __('Select the fields you would like to export.', JEM_EXP_DOMAIN) ."</p>";
		$html .= '<a href="javascript:void(0);" id="export-select-all">select all</a>  |';
		$html .= '<a href="javascript:void(0);" id="export-select-none">select none</a>';
		$html .= '<form method="post" id="postform"  action="' . admin_url( "admin-post.php" ) . '?tab=export&sub-tab=fields">';
		
		foreach($this->objects as $object){
			$html .= '<div class="export-fields" id="' . $object->id . '-div" style="display: none;">';
			$html .= '<table><tbody>';
			
			//now loop thru the entities fields
			
			$checkbox_name = 'name="' . $object->id . '_fields[';
			
			foreach ($object->fields as $field){
				if(isset($field['disabled'])){
					$disabled = " disabled='disabled' ";
					$msg = "<td><a href='http://jem-products.com/woocommerce-export-orders-pro-plugin/?utm_source=wordpress&utm_medium=plugin&utm_campaign=wordpress' target='_blank'>Available in the PRO version</a></td>";
				} else {
					$disabled='';
					$msg = "";
				}
				$html .= '<tr><td><input type="checkbox" ' . $checkbox_name . $field['name'] . ']"' . $disabled . '>' . $field['placeholder'] . '</input></td>';
				$html .= $msg;
				$html .= '</tr>';
			}
			
			$html .= '</tbody></table></div>';
			
		}
		
		//now add the submit button
		$html .= '
			<p class="submit">
				<input type="hidden" name="action" value="export_data">
		        <input type="hidden" name="_wp_http_referer" value="' . urlencode( $_SERVER['REQUEST_URI'] ) . '">
				<input type="hidden" id="entity-to-export" name="entity-to-export" value="export_data">
				<input type="submit" class="button-primary"  id="submit-export" value="Export ' . $object->id .'">
			</p>
				
		';
		
		
		return $html;
		
		
	}
	
	
	/**
	 * This generates the LABELS screen - VERTICAL tab
	 */
	function generate_labels_tab(){
		//we create a set of divs for each entity
	
		$html = '<form method="post" id="postform" action="' . admin_url( "admin-post.php" ) . '">';
		
		foreach($this->objects as $object){
			$html .= '<div class="export-labels" id="' . $object->id . '-labels-div" style="display: block;">';
			$html .= '<table><tbody>';

			
			//lets get the options for these labels
			$labels = get_option( JEM_EXP_DOMAIN . '_' . $object->id . '_labels');
			
			//now loop thru the entities fields
				
			$labelbox_name = 'name="' . $object->id . '_labels[';
				
			foreach ($object->fields as $field){

				//do we have a custum label for this one?
				$val = ( isset($labels[ $field['name'] ] ) ) ? $labels[ $field['name'] ] : '';
				
				$html .= '<tr><th><label>' . $field['name'] . '</label><td><input type="text" size="50"' . $labelbox_name . $field['name'] . ']" placeholder="' . $field['placeholder'] . '" value="' . $val .'"></td></tr>';
			}
				
			$html .= '</tbody></table></div>';
				
		}

		$html .= '<input type="hidden" name="entity-being-edited" id="entity-being-edited" value="">';
		$html .= ' <input type="hidden" name="_wp_http_referer" value="' . urlencode( $_SERVER['REQUEST_URI'] ) . '">';
		$html .= '  <input type="hidden" name="action" value="update_labels"> <input type="hidden" name="data" value="update_labels">';
		
		$html .= '<p class="submit"><input type="submit" value="Save Changes " class="button-primary"></p>';
		
		return $html;
	
	
	}
	
	
	/**
	 * This generates the FILTERS for the VERTICAL tab
	 */
	function generate_filters_tab(){
		$html = "";

		//we generate a div for each entity

		foreach($this->objects as $object){
			$html .= '<div class="export-filters" id="' . $object->id . '-filters-div" style="display: block;">';
			
			
// 			$html .= '
// 				<div class="filter-dates">
// 					<label>
// 				 	' . __('From Date', JEM_EXP_DOMAIN) . '
// 				 	</label>
// 				 	<input id="order-filter-start-date"  class="jemexp-datepicker">
// 				</div>
// 			';
			

			$html .= $object->generate_filters();
				
			$html .= '</div>';
			
		}
		

		//now add the submit button
		$html .= '
			<p class="submit">
				<input type="submit" class="button-primary"  id="submit2-export" value="Export ' . $object->id .'">
			</p>';
		
		//we close the form from that is obened in labels
		$html .= '</form>';
		
		return $html;
	}
	/**
	 * This generates the SCHEDULED screen - VERTICAL tab
	 */
	function generate_scheduled_tab(){
	
		//we create a set of divs for each entity
	
		$html = '';
	
		foreach($this->objects as $object){
				
			$html .= '<div class="jemex-scheduled export-scheduled" id="' . $object->id . '-scheduled-div" style="display: block;">';
	
			$html .= "<h2>Scheduled Exports</h2>";
			$html .= "<p><a href='http://jem-products.com/woocommerce-export-orders-pro-plugin/?utm_source=wordpress&utm_medium=plugin&utm_campaign=wordpress' target='_blank'>This feature is available in the PRO version</a></p>";
			$html .= '</div>';
	
			}
	
	
			return $html;
			
		}
	
	
	/**
	 * This generates the box with the entity list
	 */
	function generate_entity_list(){

		global $jem_export_globals;
		
		//list of active entities
		$active = $jem_export_globals['active'];
		
		//first lets build the table of entities
		
		//loop thru the entities & build the table rows
 		$html = "";
		foreach($this->objects as $object){
			$id = $object->id;
			
			if(isset($active[$id])){
				$msg = "";
				
			} else {
				$msg = "<a href='http://jem-products.com/woocommerce-export-orders-pro-plugin/?utm_source=wordpress&utm_medium=plugin&utm_campaign=wordpress' target='_blank'>This data is available in the PRO version</a>";
			}
			
			$html .= '<tr><td width="150px"><input type="radio" id="' . $id . '" value="' . $id . '" name="datatype">';
			$html .= '<label for="' . $id .'">' . $id . '</label></td>';
			$html .= '<td>' . $msg . '</td>';
			$html .= '</tr>';
		}
		
		
		$table = '<table><tbody>' . $html . '</tbody></table>';
		
		$html = '
<div id="export-type" class="postbox">
	<h3 class="hndle">Export Type</h3>
	<div class="inside">
		<p class="instructions">' . __('Select the data type you would like to export.', JEM_EXP_DOMAIN) . '</p>' . $table . '
	</div>
				
</div>				
		';
		
		
		return $html;
		
	}


	/**
	 * This handles the updates to the labels
	 * It is called automagically from the form post
	 */
	function update_labels(){
		
		//lets update any of the labels!
		//first get the entity we are edting
		$ent = ( isset($_POST['entity-being-edited']) ) ? $_POST['entity-being-edited'] : '';

		if( $ent === '' ){
			//no entity being edited
			wp_redirect( urldecode($_POST['_wp_http_referer']));
		}
		
	
		//the name of the labels
		$nm = $ent . "_labels";
		$labels = ( isset( $_POST[$nm] ) ) ? array_filter( $_POST[$nm] ) : array();
		
		//And update we go
		update_option( JEM_EXP_DOMAIN . '_' . $ent . '_labels', $labels);
		
		//save the location on the page into the url
		
		$url = add_query_arg(array('tab'=> 'export', 'sub-tab'=>'labels', 'entity'=>$ent), urldecode($_POST['_wp_http_referer']));
		
		
		wp_redirect( $url );
	}
	
	/**
	 * This handles the form post from the settings tab
	 * Called automagically from the admin_post action
	 */
	function save_settings(){
		//ok lets take each field, sanitize it and off we go!
		
		$settings = array();
		$settings['filename'] = sanitize_text_field( $_POST['jemex_export_filename'] );
		
		$settings['encoding'] = sanitize_text_field( $_POST['jemex_encoding'] );
		
		$settings['date_format'] = sanitize_text_field( $_POST['jemex_date_format'] );
		
		$settings['delimiter'] = sanitize_text_field( $_POST['jemex_field_delimiter'] );
		
		//save them
		update_option(JEM_EXP_DOMAIN, $settings);
		
		//set the transient
		$this->save_admin_messages(__('Settings Saved.', JEM_EXP_DOMAIN), 'updated');
		
		//now just goback to settings!
		wp_redirect( urldecode($_POST['_wp_http_referer']));
		return;
		
		
	}
	
	/**
	 * This handles the export of the data
	 * * gets called automagically by the submit of the form
	 */
	function export_data(){
		

		//load settings
		$this->get_settings();
		
		
		$output_fileName = $this->settings['filename'];

		//first get the entity we are exporting
		$ent = ( isset($_POST['entity-to-export']) ) ? $_POST['entity-to-export'] : '';
		
		if( $ent === '' ){
			//no entity being edited
			wp_redirect( urldecode($_POST['_wp_http_referer']));
			return;
		}
		

		
		//if no object redirects
		if(!isset($this->objects[$ent])){

			//hmmmmm no entity exists - something screwey happened!
			wp_redirect( urldecode($_POST['_wp_http_referer']));
			return;
		}

		//get the entity
		$obj = $this->objects[$ent];
		
		//lets get the field list to display and put it in the entity object
		$temp = $ent . "_fields";
		if(isset($_POST[$temp])){
			$fieldsToExport = $_POST[$temp];
		} else {
			//No fields to export so display an error message and return
			
			$this->save_admin_messages(__('You have not selected any fields to export', JEM_EXP_DOMAIN), 'error');
				
			wp_redirect( urldecode($_POST['_wp_http_referer']));
			return;
		}

		$obj->fieldsToExport = $fieldsToExport;
		
		//load the user settings into the object
		$obj->settings = $this->settings;
		
		//lets get the appropriate filters for this entity
		$ret = $obj->extract_filters($_POST);
		
		//did we get an error?
		if($ret != ''){
			$this->save_admin_messages( $ret, 'error');
			
			wp_redirect( urldecode($_POST['_wp_http_referer']));
			return;
				
		}

		//create the file name - this is the name stored on our server
		$dir = wp_upload_dir();
		$fileName = $dir['basedir'] . '/JEM_csv_export.csv';
		$file = fopen( $fileName, 'w+');
		
		
		//ok we have an object - lets execute the darn query!
		$ret = $obj->run_query($file);
		
		if($ret === false){
			$this->save_admin_messages(__('No records were found - please modify the filters and try again', JEM_EXP_DOMAIN), 'error');
				
			wp_redirect( urldecode($_POST['_wp_http_referer']));
			return;
				
			
		}
		fclose($file);
		
		//now download the CSV file...
		
		if( file_exists( $fileName ) ){
		
			$file = fopen( $fileName, 'r' );
			$contents = fread($file, filesize($fileName));
			fclose($file);
		
			//delete the file
			unlink($fileName);
		
			//funky headers!
			//TODO - put this in a function - need to work out how to handle non-western characters etc
			//http://www.andrew-kirkpatrick.com/2013/08/output-csv-straight-to-browser-using-php/ with some mods
			header("Expires: 0");
			header("Pragma: no-cache");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=$output_fileName.csv");
		
			//now write it out
			$file = @fopen( 'php://output', 'w' );
			fwrite( $file, $contents );
			fclose($file);
		}
		
		
	}
	

	/**
	 * Saves admion messages of a specific type, currently 'updated' or 'error'
	 * @param unknown $message
	 * @param unknown $type
	 */
	function save_admin_messages($message, $type='updated'){
		//add it to the trasnient queue
		
		$html = '
			<div id="message" class="' . $type . '">
			<p>' . $message . '</p>
			</div>
		';
		
		set_transient(JEM_EXP_DOMAIN . '_messages', $html, MINUTE_IN_SECONDS);
	}
	
	
	/**
	 * Prints any admin messages
	 */
	function print_admin_messages(){
		$html = get_transient(JEM_EXP_DOMAIN . '_messages');
		if($html != false){
			delete_transient(JEM_EXP_DOMAIN . '_messages');
			echo $html;
		}
	}
}
?>