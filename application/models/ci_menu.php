<?php
class CI_menu extends CI_Model {
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->database();
		$this->load->helper('url');
		$this->load->model(array('CI_auth'));
	}
	
	/**
	* Die Funktion erstellt das Menü anhand des Arrayinhaltes 
	* @param $array_menu Das Array mit den einzelnen Menüpunkten
	* @param $separator Der Seperator zwischen den Menüpunkten.
	* @return Die View _links mit dem Menü
	*/
	function create_menu($array_menu, $separator ='|'){
		$data = array(
				'menu' => $array_menu,
				'separator' => $separator
		);
		return $this->load->view('_links',$data, true);
	}

	/**
	* Die Funktion prüft ob der Benutzer eingeloggt ist und erstellt je nach dem ein Menü mit anderen Elementen.
	* Bei eingeloggtem Benutzer sind dies die Menüpunkte Home, Mein Account und Logout.
	* Beim nicht eingeloggten Benutzer sind dies die Menüpunkte Home, Registrieren und Login
	* @return Die View _links mit dem Menü.
	*/
	function menu_top() {
		$menu_common = array(
				'Home' => base_url()
		);
		$menu_unlogged = array(
				'Registrierung' => base_url() .'index.php/register/',
				'Login' => base_url() . 'index.php/login/'
		);
		$menu_logged = array(
				'Mein Account' => base_url() .'index.php/member_area/',
				'Logout' => base_url() .'index.php/login/logout/'
		);

		$menu = array_merge($menu_common,($this->CI_auth->check_logged() == true)?$menu_logged:$menu_unlogged);
		return $this->create_menu($menu);
	}
}