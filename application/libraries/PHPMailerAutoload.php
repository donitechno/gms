<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 

class PHPMailerAutoload {
	public function __construct() {
        require_once('PHPMailer/PHPMailerAutoload.php');
    }
	
    public function PHPMailerAutoload() {

        require_once('PHPMailer/PHPMailerAutoload.php');

    }
}

new PHPMailerAutoload();
//PHPMailerAutoload::PHPMailerAutoloadLibrary();