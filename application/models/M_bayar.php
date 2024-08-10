<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_bayar extends CI_Model {
	public function __construct(){
		parent::__construct();
	}
	
	public function get_all_bayar_nontunai(){
		$sql = "SELECT * FROM gold_bayar_non_tunai";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function generate_account_bank(){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE type = 'BA'
				ORDER BY accountnumberint DESC
				LIMIT 1";
		
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 

			return $row->accountnumberint + 1;
		}else{
			$id = 130001;
			return $id;
		}
	}
	
	public function insert_bayar_nontunai($cara_bayar,$account_number,$created_date,$created_by){
		$sql = "INSERT INTO gold_bayar_non_tunai(description,account_number,status,created_date,created_by) VALUES ('$cara_bayar','$account_number','A','$created_date','$created_by')";
		
		$this->db->query($sql);
	}
	
	public function insert_coa($account_number,$account_number_int,$account_name,$account_group,$beginning_balance,$type,$created_by){
		$sql = "INSERT INTO gold_coa_rp(accountnumber,accountnumberint,accountname,accountgroup,beginningbalance,status,type,created_by) VALUES ('$account_number','$account_number_int','$account_name','$account_group','$beginning_balance','A','$type','$created_by')";
		
		$this->db->query($sql);
	}
	
	public function update_bayar_nontunai($id_bayar,$cara_bayar){
		$sql = "UPDATE gold_bayar_non_tunai
				SET description = '$cara_bayar'
				WHERE account_number = '$id_bayar'";
		
		$this->db->query($sql);
	}
	
	public function update_coa_bayar_nontunai($account_bayar,$cara_bayar,$saldo_awal){
		$sql = "UPDATE gold_coa_rp
				SET accountname = '$cara_bayar', beginningbalance = '$saldo_awal'
				WHERE accountnumber = '$account_bayar'";
		
		$this->db->query($sql);
	}
	
	public function get_bayar_id_by_name($name){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE accountname='$name'";
		
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 
			
			return $row->accountnumber;
		}else{
			$id = 0;
			return $id;
		}
	}
	
	public function get_bayar_by_id($id){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE accountnumber='$id'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function change_bayar_status($id_bayar,$status_bayar){
		$sql = "UPDATE gold_bayar_non_tunai
				SET status = '$status_bayar'
				WHERE id = '$id_bayar'";
		
		$this->db->query($sql);
	}
}