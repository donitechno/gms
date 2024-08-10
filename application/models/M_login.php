<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_login extends CI_Model {
	public function __construct(){
		parent::__construct();
	}
	
	public function cek_login($dbname, $username, $pwd){
		
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT * FROM gold_user
				WHERE username='$username' AND status = 'A'";
		
		$query= $condb->query($sql);
		//$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$username = $row->username;
			$password = $row->password_user;
			$salt = $row->salt;
			
			$passencrypt = sha1(md5($pwd) . $salt);
			if($passencrypt == $password){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}
	
	public function get_userdata($username){
		$this->db->where('username',$username);
		return $this->db->get('gold_user')->result();
	}
	
	public function random_digit($length = 8, $special_char = true){
		$digits = '';
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
		
		if($special_char === true)
			$chars .= "!?=/&+,.";
		
		for($i=0; $i<$length; $i++){
			$x = mt_rand(0, strlen($chars) -1);
			$digits .= $chars[$x];
		}
		
		return $digits;
	}
	
	public function random_salt(){
		return $this->random_digit(8, true);
	}
	
	public function encrypt_pass($pwd, $salt){
		return sha1(md5($pwd) . $salt);
	}
}