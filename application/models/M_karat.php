<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_karat extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->db= $this->load->database($_COOKIE['cabang'],TRUE);

	}
	
	public function get_karat_srt(){
		$sql = "SELECT * FROM gold_karat
				WHERE srt = 'Y'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_karat_name_by_id($id){
		$sql = "SELECT * FROM gold_karat
				WHERE id='$id'";
		
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 

			return $row->karat_name;
		}else{
			$id = 'UNDEFINED';
			return $id;
		}
	}
	
	public function get_do_formula(){
		$sql = "SELECT * FROM gold_do_formula
				ORDER BY id";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_karat_sdr(){
		$sql = "SELECT * FROM gold_karat
				WHERE sdr = 'Y'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_karat_sdg(){
		$sql = "SELECT * FROM gold_karat
				WHERE sdg = 'Y'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_karat_by_id($id){
		$sql = "SELECT * FROM gold_karat
				WHERE id='$id'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_setting_harga(){
		$sql = "SELECT s.*, k.karat_name FROM gold_setting_harga s, gold_karat k
				WHERE s.id_karat = k.id
				ORDER BY id";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function update_setting_harga($id_setting, $from_weight, $to_weight, $min_percent, $max_percent, $min_percent_beli, $max_percent_beli){
		$sql = "UPDATE gold_setting_harga
				SET dari_berat = '$from_weight', sampai_berat = '$to_weight', min_persen = '$min_percent', max_persen = '$max_percent', min_persen_beli = '$min_percent_beli', max_persen_beli = '$max_percent_beli', last_updated = CURRENT_TIMESTAMP()
				WHERE id = '$id_setting'";
				
		$this->db->query($sql);
	}
	
	public function get_karat_id_by_name($karatname){
		$sql = "SELECT * FROM gold_karat
				WHERE karat_name='$karatname'";
		
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 

			return $row->id;
		}else{
			$id = $karatname;
			return $id;
		}
	}
}