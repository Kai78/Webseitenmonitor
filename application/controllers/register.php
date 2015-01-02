<?php
class Register extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->library(array('form_validation', 'template'));
		$this->load->model(array('CI_captcha', 'CI_menu', 'CI_encrypt'));
		$this->load->helper(array('form', 'url'));
		$this->load->database();
	}


	/**
	* Die Funktion prüft ob der Benutzer bereits eingeloggt ist und falls nicht wird die Registrierungsseite angezeigt. Ansonsten der Memberbereich.
	* Bei Übertragung des Registrierungsformular werden die eingegeben Daten auf Gültigkeit überprüft. Falls die Angaben ungültig sind wird der Benutzer
	* darauf hingewiesen die entsprechenden Angaben zu korrigieren.
	*/
	function index(){
		if($this->CI_auth->check_logged()=== true)
			redirect(base_url() . '/index.php/member_area/');
			

		$data['title'] = 'Registrierung';
		$data['menu_top'] = $this->CI_menu->menu_top();
		$sub_data['captcha_return'] ='';
		$sub_data['cap_img'] = $this ->CI_captcha->make_captcha();
		if($this->input->post('submit')) {
			$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('username', 'User name', 'trim|required|alpha_dash|min_length[3]|max_length[20]|xss_clean');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[3]|max_length[20]|matches[passconf]|xss_clean');
			$this->form_validation->set_rules('passconf', 'Confirm Password', 'trim|required|min_length[3]|max_length[20]|xss_clean');
			$this->form_validation->set_rules('email', 'Email',  'trim|required|min_length[3]|max_length[30]|valid_email');
			$this->form_validation->set_rules('captcha', 'Captcha', 'required');
			if ($this->form_validation->run() == FALSE){
				$data['body']  = $this->load->view('_join_form', $sub_data, true);
			}
			else{
				if($this->CI_captcha->check_captcha()==TRUE){
					$name = $this->input->post('name');
					$username = $this->input->post('username');
					$password = $this->input->post('password');
					$email = $this->input->post('email');
					$check_query = "SELECT * FROM `users` WHERE `username`='$username' OR `email`='$email'";
					$query = $this->db->query($check_query);
					if ($query->num_rows() > 0){
						$sub_data['captcha_return'] = 'Der eingegebene Benutzername oder die E-Mail Adresse existieren bereits. Bitte wählen Sie andere Angaben.<br/>';
						$data['body']  = $this->load->view('_join_form', $sub_data, true);
					}
					else{
						$rand_salt = $this->CI_encrypt->genRndSalt();
						$encrypt_pass = $this->CI_encrypt->encryptUserPwd( $this->input->post('password'),$rand_salt);
						$input_data = array(
								'name' => $name,
								'username' => $username,
								'email' => $email,
								'password' => $encrypt_pass,
								'salt' => $rand_salt
						);
						if($this->db->insert('users', $input_data)){
							$data['body']  = "Registrierung erfolgreich. Bitte loggen Sie sich ein.<br/>";
						}
						else
							$data['body']  = "Fehler bei der Abfrage.";
					}
				}
				else{
					$sub_data['captcha_return'] = "Die eingegebenen Zeichen stimmen nicht mit dem Sicherheitscode überein. Bitte versuchen Sie es erneut. <br/>";
					$data['body']  = $this->load->view('_join_form', $sub_data, true);
				}
			}

		}
		else{
			$data['body']  = $this->load->view('_join_form', $sub_data, true);
		}
		$this->template->load('default',NULL,$data);
	}
}
?>