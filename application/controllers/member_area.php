<?php
class Member_area extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->library(array('session', 'template'));
		$this->load->model(array('CI_auth', 'CI_menu', 'CI_monitored_website'));
		$this->load->helper(array('html','url'));
	}

	/**
	* Die Funktion überprüft ob der Benutzer eingeloggt ist. Falls nicht wird der Benutzer an die Startseite weitergeleitet. 
	* Wenn der Benutzer eingeloggt ist, wird ein geändertes Topmenü geladen wo der eingeloggte Benutzer die Möglichkeit hat sich auszuloggen
	* und die Startseite zu laden wo er mit einer Begrüssung empfangen wird.  
	* Weiter wird die linke Navigation geladen wo er die registrierten Webseiten aufgelistet sind.  
	*/
	function index(){
		if($this->CI_auth->check_logged()===FALSE)
			redirect(base_url());
		else{
			$data['title'] = 'Memberbereich';
			$data['menu_top'] = $this->CI_menu->menu_top();
			$data['monitored_url'] = $this->CI_monitored_website->get_all(FALSE);
			$this->template->load('default', '_monitoring_navigation', $data);
		}
	}
}