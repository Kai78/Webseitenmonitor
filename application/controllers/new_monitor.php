<?php
class New_monitor extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->library(array('template'));
		$this->load->model(array('CI_captcha', 'CI_monitored_website', 'CI_menu', 'CI_encrypt'));
		$this->load->helper(array('form', 'url'));
		$this->load->database();
	}

	/**
	* Die Funktion speichert einen neuen Websitemonitor in der Datenbank.
	*/
	function index() {

		if($this->input->post('save_new_monitor')) {
				$url_to_mon = $this->input->post('url_to_monitor');
				$friendly_name = $this->input->post('friendly_name');
				$check_interval = $this->input->post('checkinterval');
				$email_to_alarm = $this->input->post('email_to_alarm');
				$datetime = date("Y-m-d H:i:s");
				
				$input_data = array(
								'url' => $url_to_mon,
								'friendly_name' => $friendly_name, 
								'monitored_since' => $datetime,
								'email_to_alarm' => $email_to_alarm,
								'check_interval' => $check_interval);
				$this->CI_monitored_website->addNewMonitor($input_data);		
		}
		else {
			//Es wurde auf abbrechen geklickt
			redirect(base_url() . '/index.php/member_area');
		}
	}
}