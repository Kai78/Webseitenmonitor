<?php
class CI_auth extends CI_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->database();
		$this->load->helper('url');
		$this->load->model(array('CI_encrypt'));
	}

	/**
	* Die Funktion prüft ob Benutzername und Passwort beim Login angegegeben wurden. Wenn dies der Fall ist wird geprüft ob der angegebene Benutzer 
	* in der Datenbank gespeichert ist und ob das angegebene Passwort übereinstimmt.
	* @param $login_array_input 
	* 
	* @return Wenn der Login erfolgreich ist wird true zurück gegeben ansonsten false.
	*/
	function process_login($login_array_input = NULL){
		if(!isset($login_array_input) OR count($login_array_input) != 2)
			return false;
		$username = $login_array_input[0];
		$password = $login_array_input[1];
		// Prüfe anhand der Daten in der DB ob angegebener Benutzer bereits existiert.
		$query = $this->db->query("SELECT * FROM `users` WHERE `username`= '".$username."' LIMIT 1");
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$user_id = $row->id;
			$username = $row->username;
			$user_pass = $row->password;
			$user_salt = $row->salt;
				if($this->CI_encrypt->encryptUserPwd( $password,$user_salt) === $user_pass){
					$loggedIDAndUsername = array('logged_user' => $user_id, 'logged_username' => $username);
					$this->session->set_userdata($loggedIDAndUsername);
					return true;
				}
			return false;
		}
		return false;
	}

	/**
	* Die Funktion prüft anhand der Session ob der Benutzer eingeloggt ist.
	* @return Gibt True zurück wenn der Benutzer eingeloggt ist, ansonsten false.
	*/
	function check_logged(){
		return ($this->session->userdata('logged_user'))?TRUE:FALSE;
	}

	/**
	* Die Funktion gibt die id des eingeloggten Benutzer zurück.
	* @return Die id des eingeloggten Benutzer.
	*/
	function logged_id(){
		return ($this->check_logged())?$this->session->userdata('logged_user'):'';
	}

	/**
	* Die Funktion gibt den Benutzername des eingeloggten Benutzer zurück.
	* @return Der Benutzername des eingeloggten Benutzer
	*/
	function logged_user() {
		return ($this->check_logged())?$this->session->userdata('logged_username'):'';	
		
	}
}

