<?php
class CI_monitored_website extends CI_Model {
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->database();
		$this->load->helper('url');
		$this->load->model(array('CI_auth'));
	}
	
	/**
	* Die Funktion gibt alle Datensätze von der Tabelle monitored_website zurück bei denen die Spalte is_deleted = 0 ist
	* @param $onlyThoseToCheck Bei True sollen nur diese Webseiten zurück gegeben werden bei denen der Prüfungsintervall nach ist
	* @return Das Resultat der Abfrage als Array.
	*/
	function  get_all($onlyThoseToCheck = FALSE) {
		if ($onlyThoseToCheck == FALSE) {
			$check_query = "SELECT * FROM monitored_website WHERE is_deleted = 0";
		}
		else {
			$check_query = "SELECT * FROM monitored_website WHERE ((TIME_TO_SEC((TIMEDIFF(NOW(), last_check))) > check_interval) OR last_check IS NULL) AND is_deleted = 0 ;";
		} 
		$query = $this->db->query($check_query);
		return $query->result_array();	
	}
	
	/**
	* Die Funktion gibt den Datensatz in der Tabelle monitored_website zurück welche der id in der Spalte id entspricht 
	* @param $id Die ID der Webseite welche zurückgegeben werden soll.
	* @return Das Resultat der Abfrage als Array
	*/
	function getWebsiteWithId($id) {
		$check_query = "SELECT * FROM monitored_website WHERE is_deleted = 0 AND id = " . $id . ";";	
		$query = $this->db->query($check_query);
		return $query->result_array();	
	}
	
	/**
	* Löscht respone Zeiten aus der Tabelle reponse_times die älter als 6 Monate sind.
	*/
	function deleteOldResponseTimes() {
		$query = "DELETE FROM response_times WHERE 
		          PERIOD_DIFF(DATE_FORMAT(NOW(), '%Y%m'), DATE_FORMAT(data_of_response, '%Y%m')) >  6;";
		$this->db->query($query);   
	}
	
	/**
	* Die Funktion gibt alle Antwortzeiten der Webseite zurück.
	* @param $fromWebsiteWithId Die id der Webseite von welcher die Antwortzeiten zurück gegeben werden soll.
	* @return Das Resultat der Abfrage als Array
	*/
	function getResponeTimes($fromWebsiteWithId) {
		$check_query = "SELECT time_of_response, data_of_response FROM response_times WHERE mw_id = " . $fromWebsiteWithId . 
							   " AND PERIOD_DIFF(DATE_FORMAT(NOW(), '%Y%m'), DATE_FORMAT(data_of_response, '%Y%m')) < 4;";	
		$query = $this->db->query($check_query);
		return $query->result_array();	
	}
	
	/**
	* Die Funktion gibt die Uptime der Website zurück
	* @param $fromWebsiteWithId Die id der Website von welcher die Uptime zurück gegeben werden soll.
	* @return Das Resultat der Abfrage als Array
	*/
	function getUptime($fromWebsiteWithId) {
		$check_query = "SELECT IF(last_down IS NOT NULL, 
							CONCAT(FLOOR(HOUR(TIMEDIFF(last_down, monitored_since))/24), \" Tag(e) \", 
					               MOD(HOUR(TIMEDIFF(last_down, monitored_since)), 24), \" Stunde(n) \",
					               MINUTE(TIMEDIFF(last_down, monitored_since)), \" Minute(n)\"),
							CONCAT(FLOOR(HOUR(TIMEDIFF(NOW(), monitored_since))/24), \" Tag(e) \", 
			                       MOD(HOUR(TIMEDIFF(NOW(), monitored_since)), 24), \" Stunde(n) \",
			                       MINUTE(TIMEDIFF(NOW(), monitored_since)), \" Minute(n)\")) AS uptime 
			                FROM monitored_website WHERE id = $fromWebsiteWithId";
		$query = $this->db->query($check_query);
		return $query->result_array();	                
	}
	
	/**
	* Die Funktion gibt die Durchschnittszeit einer Webseite zurück.
	* @param $fromWebsiteWithId Die id der Webseite von welcher die Durchschnittszeit zurück gegeben werden soll.
	* @return Das Resultat der Abfrage als Array
	*/
	function getAverageResponseTime($fromWebsiteWithId) {
		$check_query = "SELECT AVG(time_of_response) AS average FROM response_times WHERE mw_id = " . $fromWebsiteWithId . " GROUP BY mw_id;";	
		$query = $this->db->query($check_query);
		return $query->result_array();		
	}
	
	/**
	* Die Funktion gibt alle E-Mail Adressen zurück, die für die Webseite registriert wurden.
	* @param  $fromWebsiteWithId Die id der Webseite von denen die E-Mail Adressen zurück gegeben werden sollen. 
	* @return Das Resultat der Abfrage als Array
	*/
	function getEmailAdresses($fromWebsiteWithId) {
		$check_query = "SELECT email FROM email_to_alarm eta INNER JOIN monitored_website mw ON mw.id = eta.mw_id WHERE mw.id = " . $fromWebsiteWithId . ";";
		$query = $this->db->query($check_query);
		return $query->result_array();	
	}
	
	
	/**
	* Die Funktion fügt einen neuen Datensatz in die Tabelle monitored_website ein. 
	* @param $newMonitorArray Das Array mit dem Inhalt zum Speichern in der DB.
	*/
	function addNewMonitor($newMonitorArray) {

		$this->db->query("INSERT INTO monitored_website (url, friendly_name, monitored_since, check_interval) VALUES (" 
						. "\"" . $newMonitorArray['url'] . "\", " 
						. "\"" . $newMonitorArray['friendly_name'] . "\", " 
						. "\"" . $newMonitorArray['monitored_since'] . "\", " 
						. $newMonitorArray['check_interval'] . ");");
		$checkQuery = $this->db->query("SELECT id FROM monitored_website WHERE url = \"" . $newMonitorArray['url'] . "\";");
		$id = $checkQuery->row()->id;
		$this->db->query("INSERT INTO email_to_alarm (email, mw_id) VALUES (\"" . $newMonitorArray['email_to_alarm'] . "\", " . $id . ");");
		redirect(base_url() . '/index.php/member_area');
	}
	
	/**
	* Die Funktion fügt einen neuen Datensatz in die Tabelle response_times ein. 
	* @param $id Die id der Website in der Tabelle monitored_website (hier als FK)
	* @param $responseTime Die Antwortzeit der Webseite.
	*/
	function addNewResponseTime($id, $responseTime) {
		$datetime = date("Y-m-d H:i:s");
		$this->db->query("INSERT INTO response_times (time_of_response, mw_id, data_of_response) VALUES (\"" . $responseTime . "\"," . $id . ", \"" . $datetime . "\");");
		
	}
	
	/**
	* Die Funktion aktualisiert den Statuscode der Website mit der id in der Tabelle monitored_website
	* @param $id Die id der Webseite bei welcher der Statuscode aktualisiert werden soll.
	* @param $code Der zu aktualisierende Statuscode
	*/
	function updateStatusCode($id, $code) {
		$check_query = $this->db->query("UPDATE monitored_website SET http_code = $code WHERE id = $id");
	}
	
	/**
	* Die Funktion setzt die Spalte is_deleted der Webseite mit der id die als Parameter übergeben wurde auf True.
	* @param $id Die id der Webseite bei welcher der Status is_deleted auf True gesetzt werden soll.
	*/
	function updateIsDeleted($id) {	
		$this->db->query("UPDATE monitored_website SET is_deleted = 1 WHERE id = $id");		
	}
	
	/**
	* Die Funktion aktualiert das Datum und die Zeit der letzten Prüfung der Webseite mit der id.
	* @param $id Die id der Webseite bei welcher das Datum und die Zeit aktualisiert werden sollen. 
	*/
	function updateLastCheck($id) {
		$datetime = date("Y-m-d H:i:s");
		$this->db->query("UPDATE monitored_website SET last_check = \"" . $datetime . "\" WHERE id = " . $id . ";");
	}
	
	/**
	* Die Funktion setzt die Spalte is_paused der Website mit der id die als Parameter übergeben wurde auf True oder False 
	* je nach dem welchen Wert sie gerade besitzt.
	* @param $id Die id der Webseite bei welcher der Status is_paused auf True oder False gesetzt werden soll.
	*/
	function updateIsPaused($id) {
		$check_query = $this->db->query("SELECT is_paused FROM `monitored_website` WHERE `id`= " . $id . ";");
		$row = $check_query->row()->is_paused;
		if ($row == 0) {
			$this->db->query("UPDATE monitored_website SET is_paused = 1 WHERE id = " . $id . ";");
		}
		else {
			$this->db->query("UPDATE monitored_website SET is_paused = 0 WHERE id = " . $id . ";");
		}
	}
}