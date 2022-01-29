<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */
 
if( !defined( 'ABSPATH') ) exit();

class RevSliderUpdate {

	private $plugin_url			= 'https://codecanyon.net/item/slider-revolution-responsive-wordpress-plugin/2751380';
	private $remote_url			= 'check_for_updates.php';
	private $remote_url_info	= 'revslider/revslider.php';
	private $remote_temp_active	= 'temp_activate.php';
	private $plugin_slug		= 'revslider';
	private $plugin_path		= 'revslider/revslider.php';
	private $version;
	private $plugins;
	private $option;
	
	
	public function __construct($version) {
		$this->option = $this->plugin_slug . '_update_info';
		$this->version = $version;
		
	}
	
	public function add_update_checks(){
		
		add_filter('pre_set_site_transient_update_plugins', array(&$this, 'set_update_transient'));
		add_filter('plugins_api', array(&$this, 'set_updates_api_results'), 10, 3);
		
	}
	
	public function set_update_transient($transient) {
	
		$this->_check_updates();

		if(isset($transient) && !isset($transient->response)) {
			$transient->response = array();
		}

		if(!empty($this->data->basic) && is_object($this->data->basic)) {
			if(version_compare($this->version, $this->data->basic->version, '<')) {

				$this->data->basic->new_version = $this->data->basic->version;
				$transient->response[$this->plugin_path] = $this->data->basic;
			}
		}
		
		return $transient;
	}


	public function set_updates_api_results($result, $action, $args) {
	
		$this->_check_updates();

		if(isset($args->slug) && $args->slug == $this->plugin_slug && $action == 'plugin_information') {
			if(is_object($this->data->full) && !empty($this->data->full)) {
				$result = $this->data->full;
			}
		}
		
		return $result;
	}


	protected function _check_updates() {
		
		//reset saved options
		//update_option($this->option, false);
		
		$force_check = false;
		
		if(isset($_GET['checkforupdates']) && $_GET['checkforupdates'] == 'true') $force_check = true;
		
		// Get data
		if(empty($this->data)) {
			$data = get_option($this->option, false);
			$data = $data ? $data : new stdClass;
			
			$this->data = is_object($data) ? $data : maybe_unserialize($data);
		}
		
		$last_check = get_option('revslider-update-check');
		if($last_check == false){ //first time called
			$last_check = time();
			update_option('revslider-update-check', $last_check);
		}
		
		// Save results
		update_option($this->option, $this->data);
	}
}


/**
 * old classname extends new one (old classnames will be obsolete soon)
 * @since: 5.0
 **/
class UniteUpdateClassRev extends RevSliderUpdate {}
?>