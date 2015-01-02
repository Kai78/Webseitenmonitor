<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {
	 
	function __construct()
	{
		parent::__construct();
		$this->load->library(array('template'));
		$this->load->model(array('CI_menu'));
		$this->load->helper(array('url'));
		$this->load->database();
	}

	/**
	* Die Funktion prüft ob ein Benutzer eingeloggt ist und begrüsst ihn entsprechend mit seinem Benutzernamen. 
	* Falls nicht wird er gebeten sich einzuloggen
	*/
	public function index()
	{
		$data['title'] = "Startseite HF-ICT Monitor";
		$subData['logged_message'] = "Bitte loggen Sie sich ein.";
		$data['menu_top'] = $this->CI_menu->menu_top();
		if($this->CI_auth->check_logged()=== true)
			$subData['logged_message'] =  "Sie sind eingeloggt als " . $this->CI_auth->logged_user();
		$data['body'] = $this->load->view('_welcome_message', $subData, true);
		$this->template->load('default',NULL,$data);;
		
	}
}
