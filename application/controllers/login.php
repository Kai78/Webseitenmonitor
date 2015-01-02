<?php
class Login extends CI_Controller {
	function __construct()
	{
		parent::__construct();

		$this->load->library(array('session', 'form_validation', 'template'));
		$this->load->model(array('CI_auth', 'CI_menu'));
		$this->load->helper(array('html','form', 'url'));
	}

	/**
	* Die Funktion überprüft ob Benutzername und Kennwort eingegeben wurden und ob sie den Mindestkriterien genügen wie Mindest- 
	* und Maximalanzahl Zeichen. Weiter überprüft die Funktion ob der angegebene Benutzername und das angegebene Kennwort mit einem in 
	* der Datenbank gespeicherten Benutzername resp. Kennwort übereinstimmt.
	*/
	function index(){
		if($this->CI_auth->check_logged()=== true)
			redirect(base_url() . 'index.php/member_area/');
		$data['login_failed'] = '';
		$data['title'] = 'Login';
		$data['menu_top'] = $this->CI_menu->menu_top();
		if($this->input->post('submit_login')) {
			$this->form_validation->set_rules('username', 'username', 'trim|required|min_length[3]|max_length[20]|xss_clean');
			$this->form_validation->set_rules('password', 'password', 'trim|required|min_length[5]|max_length[35]|xss_clean');
			$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');

			if ($this->form_validation->run() == FALSE){
				$this->template->load('default', '_login_form', $data);				
			}
			else{
				$login_array = array($this->input->post('username'), $this->input->post('password'));
				if($this->CI_auth->process_login($login_array))
				{
					redirect(base_url() . 'index.php/member_area/' );
				}
				else {
					$data['login_failed'] = "Ungültiger Benutzername oder Passwort";
					$this->template->load('default', '_login_form', $data);
				}
			}
		}
		else{
			$this->template->load('default', '_login_form', $data);
		}
	}
	
	/**
	* Loggt den Benutzer aus. Der Benutzer wird nach dem Logout auf die Loginseite geleitet.
	* Die Funktion hat keinen Rückgabewert.
	*/
	function logout(){
		$this->session->sess_destroy();
		redirect(base_url() . 'index.php/login/' );
	}
}
?>