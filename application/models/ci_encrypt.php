<?php
class CI_encrypt extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	* Die Funktion erzeugt einen Zufallsstring
	* @param $length Die Länge des Zufallsstring
	* @param $specialCharacters Ob Spezialzeichen im Zufallsstring vorkommen sollen.
	* @return Der generierte Zufallsstring
	*/
	function genRndDgt($length = 8, $specialCharacters = true) {
		$digits = '';
		$chars = "abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789";
		if($specialCharacters === true)
			$chars .= "!?=/&+,.";
		for($i = 0; $i < $length; $i++) {
			$x = mt_rand(0, strlen($chars) -1);
			$digits .= $chars{$x};
		}
		return $digits;
	}

	/**
	* Die Funktion ruft die Funktion genRndDgt auf gibt den generierten Zufallsstring zurück.
	* @return Der generierte Zufallsstring
	*/
	function genRndSalt() {
		return $this->genRndDgt(8, true);
	}

	/**
	* Die Funktion verschlüssel das Passwort mit dem sha1 und md5 Algorithmus.
	* @param  $pwd Das Passwort zum verschlüsseln
	* @param  $salt Der String zum verschlüsseln des Passwort
	* @return Das verschlüsselte Passwort
	*/
	function encryptUserPwd($pwd, $salt) {
		return sha1(md5($pwd) . $salt);
	}

}
