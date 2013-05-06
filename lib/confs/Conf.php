<?php
class Conf {

	var $smtphost;
	var $dbhost;
	var $dbport;
	var $dbname;
	var $dbuser;
	var $version;

	function Conf() {

		$this->dbhost	= 'localhost';
		$this->dbport 	= '3306';
		if(defined('ENVIRNOMENT') && ENVIRNOMENT == 'test'){
		$this->dbname    = 'test_orangehrm_3.0.1';		
		}else {
		$this->dbname    = 'orangehrm_3.0.1';
		}
		$this->dbuser    = 'orangehrm-3.0.1';
		$this->dbpass	= 'orangehrm-3.0.1';
		$this->version = '3.0.1';

		$this->emailConfiguration = dirname(__FILE__).'/mailConf.php';
		$this->errorLog =  realpath(dirname(__FILE__).'/../logs/').'/';
	}
}
?>