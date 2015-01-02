<?php
class CI_captcha extends CI_Model {
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	* Die Funktion erstellt ein Captcha und schreibt es in die DB.
	* @return Das erstellte captcha 
	*/
	function make_captcha()
	{
		$this->load->helper('captcha');
		$vals = array(
			'img_path' => './/images/captcha/', 
			'img_url' => base_url() . 'images/captcha/', 
			'img_width' => 120, 
			'img_height' => 50, 
			'font_path'     => '../system/fonts/texb.ttf',
			'expiration' => 3600 ,);
		//Erstellen des Captcha
		$cap = create_captcha( $vals );
		//Captcha in DB schreiben
		if ( $cap ) {
			$data = array(
				'captcha_id' =>'',
				'captcha_time' => $cap['time'],
				'ip_address' => $this->input->ip_address(),
				'word' => $cap['word'] , );
			$query = $this -> db -> insert_string( 'captcha', $data );
			$this->db->query( $query );
		} else {
			return "Captcha not work" ;
		}
		return $cap['image'];
		}
	
	/**
	* Die Funktion löscht alte Captcha aus der DB und prüft ob es von der IP-Adresse, mit diesem Captcha schon eines gibt. 
	* Falls es eines gibt wird true zurück gegeben anderenfalls false.
	* @return
	*/
	function check_captcha()
	{
		// Lösche alte Daten ( 2 Stunden alt)
		$expiration = time()-3600 ;
		$sql = " DELETE FROM captcha WHERE captcha_time < ? ";
		$binds = array($expiration);
		$query = $this->db->query($sql, $binds);
		$sql = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
		$binds = array($_POST['captcha'], $this->input->ip_address(), $expiration);
		$query = $this->db->query($sql, $binds);
		$row = $query->row();
		
		if ( $row->count > 0 )
		{
			return true;
		}
		return false;
	}

}

?>
