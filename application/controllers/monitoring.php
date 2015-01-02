<?php

class monitoring extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model(array('CI_monitored_website', 'CI_menu'));
		$this->load->library(array('curl', 'email', 'template'));
		$this->load->helper(array('html','form', 'url'));
	
	}
	
	/**
	* Die Funktion schreibt an die Empfänger welche mit dem Parameter $emailReceiver angegeben wurden ein Alarmierungsemail. 
	* Das E-Mail beinhaltet die URL (Parameter $url) und den HTTP Returncode (Parameter $httpCode)
	* @param $url Die URL der Website welche im E-Mail Betreff angezeigt wird.
	* @param $httpCode Der HTTP Returncode welcher in der E-Mail Nachricht angezeigt wird. 
	* @param $emailReceiver Die E-Mail Empfänger an denen die Alarmierungsemail gesendet werden soll. Dieser Parameter muss als Array mitgegeben werden.
	*/
	private function _writeEmail($url, $httpCode, $emailReceiver) {
		/*
		$config = Array(
	    'protocol'  => 'smtp',
	    'smtp_host' => 'ssl://smtp.googlemail.com',
	    'smtp_port' => '465',
	    'charset'   => 'iso-8859-1'
		);
		*/
		echo "Starting email prep";
		//$this->load->library('email', $config);
		//$this->email->set_newline("/r/n");
		$this->email->from( 'tschimail78@gmail.com', 'WebsiteMonitor' );
		$emailAdresses = "";
		foreach ($emailReceiver as $email) {
			$emailAdresses = $email['email']; # . ";" . $emailAdresses;
		}
		print "Die E-Mail Adressen sind: " . $emailAdresses . "</br>";
		$this->email->to( trim($emailAdresses) );
		$this->email->subject( $url . ' ist nicht erreichbar' );
		$this->email->message("Website " . $url . " ist nicht erreichbar. Der Statuscode ist " . $httpCode . ". Für weitere Informationen zu http Statuscodes siehe http://de.wikipedia.org/wiki/HTTP-Statuscode");
		//echo("Starting email send");
		if ($this->email->send()) {
			echo("Email wurde gesendet");
		}
		else {
			show_error($this->email->print_debugger()); 
			echo("Email wurde nicht gesendet");
		}
		
	}

	/**
	* Die Funktion check prüft ob die Website einen Fehlerstatuscode grösser als 400 zurückliefert.
	* Wenn ein Fehlerstatuscode grösser als 400 zurückgeliefert wird und bei der vorherigen Prüfung nicht auch schon ein 
	* Fehlerstatuscode vorlag dann wird die Methode writeEmail aufgerufen. 
	* Wird ein Parameter mitgegeben wird nur die Website geprüft welcher der id des Parameterwertes entspricht.
	* @param $id Die id der Webseite welche geprüft werden soll.
	*/
	function check($id=FALSE) {

		if ($id) {
			$allWebsites = $this->CI_monitored_website->getWebsiteWithId($id);		
		}
		else {
			$allWebsites = $this->CI_monitored_website->get_all(TRUE);
		}


		foreach ($allWebsites as $website) {

			//wenn die Website nicht pausiert ist
			if ($website['is_paused'] == 1 OR $id > 0) {
				echo $website['url'];				
				//wenn die url mit http ist
				if (preg_match("~(https)://~", $website['url'])) {
    				$this->curl->create($website['url']);
    				$this->curl->ssl(FALSE);
					$this->curl->simple_get($website['url']);	
				}
				else {	
					$this->curl->simple_get($website['url']);	
				}
				$returnCode = $this->curl->info['http_code'];
				$totalTime = $this->curl->info['total_time'];
				echo "The reponse Time is " . $totalTime . ".    ";
				$this->CI_monitored_website->updateLastCheck($website['id']);
				$this->CI_monitored_website->addNewResponseTime($website['id'], $totalTime);
				$this->CI_monitored_website->deleteOldResponseTimes();
				echo "Statuscode der Webseite " . $website['url'] . " ist " . $returnCode . ". Der letzte HTTP-Code war " . $website['http_code'] . "</br>";
				if ($website['http_code'] != $returnCode ) { 
					echo "Die Webseite " . $website['url'] . " hatte zuletzt einen anderen Code nämlich " . $website['http_code'] . "</br>";
					//Falls der ermittelte Returncode >= 400 oder 0 ist und der zuletzt http_code Grösse 0 und kleiner 400 war (also Seite aufrufbar)
					//oder wenn der ermitteltee Returncode >= 400 oder 0 ist und der  ermittelte HTTP-Code von der differenz zum letzten HTTP-Code Grösser als 100 ist
					if (($returnCode >= 400 or $returnCode == 0) AND (
					($website['http_code'] < 400 AND $website['http_code'] > 0) OR 
					(abs($returnCode - $website['http_code']) > 100))) {
						//echo $website['url'] . " ist nicht erreichbar. Email wird gesendet.";
						$allEmails = $this->CI_monitored_website->getEmailAdresses($website['id']);
						$this->_writeEmail($website['url'], $returnCode, $allEmails);
					}
					$this->CI_monitored_website->updateStatusCode($website['id'], $returnCode)	; #AUSKOMMENTIEREN!!!!!!!!!!
				}
			}
		}
	}
		
	/**
	* Die Funktion pausiert eine Website von der Überwachung so dass nicht mehr
	* alarmiert wird, wenn die Website einen Fehlerstatuscode grösser oder gleich 400 zurückliefert.
	* @param $id Die id der Website welche pausiert werden soll
	*/
	function pause($id) {
		$this->CI_monitored_website->updateIsPaused($id);
		redirect(base_url() . 'index.php/member_area');
	}
	
	/**
	* Die Funktion löscht eine Website von der Überwachung. Wenn eine Website gelöscht ist wird sie nicht mehr im 
	* im Memberbereich im Navigationsbereich angezeigt.
	* @param $id Die id der Website welche gelöscht werden soll. 
	*/
	function delete($id) {
		$this->CI_monitored_website->updateIsDeleted($id);
		redirect(base_url() . '/index.php/member_area');		
	}
	
	/**
	* Mit der Funktion kann eine bereits registrierte Website bearbeitet werden.
	* @param $id Die id der Website welche bearbeitet werden soll.
	*/
	function edit($id) {
				   	
	}
		
	/**
	* Mit der Funktion kann eine Website manuell überprüft werden ob sie erreichbar ist.
	* @param $id Die id der Website welche geprüft werden soll.
	*/	
	function check_now($id) {
		$allWebsites = $this->CI_monitored_website->getWebsiteWithId($id);
		foreach ($allWebsites as $website) {
			if (preg_match("~(https)://~", $website['url'])) {
				$this->curl->create($website['url']);
				$this->curl->ssl(FALSE);
				$this->curl->simple_get($website['url']);	
			}
			else {	
				$this->curl->simple_get($website['url']);	
			}
			$returnCode = $this->curl->info['http_code'];
			$totalTime = $this->curl->info['total_time'];
			$this->CI_monitored_website->updateLastCheck($website['id']);
			$this->CI_monitored_website->addNewResponseTime($website['id'], $totalTime);
			$this->CI_monitored_website->deleteOldResponseTimes();
			if ($returnCode >= 400 OR $returnCode === 0) {
				echo "Die Webseite ist nicht erreichbar. Der Statuscode ist " . $returnCode;
			}
			else {
				echo "Die Website ist erreichbar. Der Statuscode ist " . $returnCode;
			}
		}	
	}
	
	/**
	* Mit der Funktion werden Details zur Webseite angezeigt wie seit wann die Webseite überwacht ist,
	* wann die letzte Downzeit war, und wie lange die Upzeit ist. Weiter werden damit die Antwortzeiten der 
	* letzten 3 Monate angezeigt.
	* @param  $id Die id der Webseite von der die Statistikdaten angezeigt werden sollen. 
	*/
	function show_details($id) {
		$allWebsites = $this->CI_monitored_website->getWebsiteWithId($id);
		$avgResponseTime = $this->CI_monitored_website->getAverageResponseTime($id);
		$uptime = $this->CI_monitored_website->getUptime($id);
		foreach ($allWebsites as $website) {
			$data['url'] = $website['url'];
			$data['title'] = 'Statistik von ' . $website['friendly_name'] ;
			$data['menu_top'] = $this->CI_menu->menu_top();
			$lastDown = "";
			$avgResponseTimeItem = "";
			$uptimeItem = "";
			foreach ($avgResponseTime as $avgResponseTimeItem2) {
				$avgResponseTimeItem = $avgResponseTimeItem2['average'];
			}
			foreach ($uptime as $uptimeItem2) {
				$uptimeItem = $uptimeItem2['uptime'];
			}
			if (is_null($website['last_down'])) {
				$lastDown = "Keine Downzeit bisher";
			}
			else {
				$lastDown = $website['last_down'];
			}
			$data['statistic'] = array('Überwacht seit' => $website['monitored_since'], 
									   'Letzte Downzeit' => $lastDown, 
									   'Durchschnittliche Antwortzeit' => $avgResponseTimeItem . " Sekunden", 
									   'Uptime' => $uptimeItem);
			$data['response_times'] = $this->CI_monitored_website->getResponeTimes($id);
			$this->template->load('default', '_show_detail', $data);
		}
	}
}