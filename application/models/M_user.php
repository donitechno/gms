<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_user extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->db= $this->load->database($_COOKIE['cabang'],TRUE);

	}
	
	public function get_all_karyawan(){
		$sql = "SELECT * FROM gold_karyawan";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function cek_username($username){
		$sql = "SELECT * FROM gold_karyawan
				WHERE username = '$username'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function cek_username_2($username){
		$sql = "SELECT * FROM gold_user
				WHERE username = '$username'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function random_digit($length = 8, $special_char = true){
		$digits = '';
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
		
		if($special_char === true)
			$chars .= "!?=/&+,.";
		
		for($i=0; $i<$length; $i++){
			$x = mt_rand(0, strlen($chars) -1);
			$digits .= $chars{$x};
		}
		
		return $digits;
	}
	
	public function random_salt(){
		return $this->random_digit(8, true);
	}
	
	public function encrypt_pass($pwd, $salt){
		return sha1(md5($pwd) . $salt);
	}
	
	public function generate_account_karyawan(){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE type = 'PIK'
				ORDER BY accountnumberint DESC
				LIMIT 1";
		
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 

			return $row->accountnumberint + 1;
		}else{
			$id = 140001;
			return $id;
		}
	}
	
	public function insert_coa($account_number,$account_number_int,$account_name,$account_group,$beginning_balance,$status,$type,$created_by){
		$sql = "INSERT INTO gold_coa_rp(accountnumber,accountnumberint,accountname,accountgroup,beginningbalance,status,type,created_by) VALUES ('$account_number','$account_number_int','$account_name','$account_group','$beginning_balance','$status','$type','$created_by')";
		
		$this->db->query($sql);
	}
	
	public function get_piutang_kary_account($username){
		$sql = "SELECT * FROM gold_karyawan WHERE username = '$username'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_karyawan($username,$nama_karyawan,$kelompok_karyawan,$account_number,$random_salt,$encrypt_pass,$created_date,$created_by){
		$sql = "INSERT INTO gold_karyawan(username,nama_karyawan,kelompok,accountnumber,status,created_date,created_by) VALUES ('$username','$nama_karyawan','$kelompok_karyawan','$account_number','A','$created_date','$created_by')";
		
		$this->db->query($sql);
		
		$kasir = 'N';
		$manager = 'N';
		$pembukuan = 'N';
		$superadmin = 'N';
		
		if($kelompok_karyawan == 'KARYAWAN'){
			$kasir = 'Y';
		}else if($kelompok_karyawan == 'MANAGER/WAKIL'){
			$manager = 'Y';
		}else if($kelompok_karyawan == 'PEMBUKUAN'){
			$pembukuan = 'Y';
		}else if($kelompok_karyawan == 'SUPERADMIN'){
			$kasir = 'Y';
			$manager = 'Y';
			$pembukuan = 'Y';
			$superadmin = 'Y';
		}
		
		$sql = "INSERT INTO gold_user(username,nama_user,password_user,priv_kasir,priv_pembukuan,priv_manager,priv_admin,salt,picture,status) VALUES ('$username','$nama_karyawan','$encrypt_pass','$kasir','$pembukuan','$manager','$superadmin','$random_salt','user.jpg','A')";
		
		$this->db->query($sql);
	}
	
	public function change_karyawan_status($id,$status){
		$sql = "UPDATE gold_karyawan
				SET status = '$status'
				WHERE username = '$id'";
		
		$this->db->query($sql);
		
		$sql = "UPDATE gold_user
				SET status = '$status'
				WHERE username = '$id'";
		
		$this->db->query($sql);
	}
	
	public function update_piutang_kary_status($id_piutang,$status){
		$sql = "UPDATE gold_coa_rp
				SET status = '$status'
				WHERE accountnumber = '$id_piutang'";
		
		$this->db->query($sql);
	}
	
	public function update_piutang_kary_status_2($id_piutang,$account_name,$status){
		$sql = "UPDATE gold_coa_rp
				SET status = '$status', accountname = '$account_name'
				WHERE accountnumber = '$id_piutang'";
		
		$this->db->query($sql);
	}
	
	public function update_karyawan($username,$nama_karyawan,$kelompok_karyawan){
		$sql = "UPDATE gold_karyawan
				SET nama_karyawan = '$nama_karyawan', kelompok = '$kelompok_karyawan'
				WHERE username = '$username'";
		
		$this->db->query($sql);
		
		$kasir = 'N';
		$manager = 'N';
		$pembukuan = 'N';
		$superadmin = 'N';
		
		if($kelompok_karyawan == 'KARYAWAN'){
			$kasir = 'Y';
		}else if($kelompok_karyawan == 'MANAGER/WAKIL'){
			$manager = 'Y';
		}else if($kelompok_karyawan == 'PEMBUKUAN'){
			$pembukuan = 'Y';
		}else if($kelompok_karyawan == 'SUPERADMIN'){
			$kasir = 'Y';
			$manager = 'Y';
			$pembukuan = 'Y';
			$superadmin = 'Y';
		}
		
		$sql = "UPDATE gold_user
				SET nama_user = '$nama_karyawan', priv_kasir = '$kasir', priv_pembukuan = '$pembukuan', priv_manager = '$manager', priv_admin = '$superadmin'
				WHERE username = '$username'";
		
		$this->db->query($sql);
	}
	
	public function update_karyawan_password($username,$random_salt,$encrypt_pass){
		$sql = "UPDATE gold_user
				SET password_user = '$encrypt_pass', salt = '$random_salt'
				WHERE username = '$username'";
		
		$this->db->query($sql);
	}
	
	/*
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	*/
}