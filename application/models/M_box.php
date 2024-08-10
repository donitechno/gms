<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_box extends CI_Model {
	public function __construct(){
		parent::__construct();
	}
	
	public function get_all_box(){
		$sql = "SELECT * FROM gold_box";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_box_by_range($filter_box_from,$filter_box_to){
		$sql = "SELECT * FROM gold_box
				WHERE status = 'A' AND id BETWEEN '$filter_box_from' AND '$filter_box_to'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function change_box_status($id_box,$status_box){
		$sql = "UPDATE gold_box
				SET status = '$status_box'
				WHERE id = '$id_box'";
		
		$this->db->query($sql);
	}
	
	public function get_box_aktif(){
		$sql = "SELECT * FROM gold_box
				WHERE status = 'A'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_box_to($id_box){
		$sql = "SELECT * FROM gold_box
				WHERE status = 'A' AND id <> '$id_box'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_box_pesanan(){
		$sql = "SELECT * FROM gold_box
				WHERE pesanan = 'Y'";

		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 
			return $row->id;
		}else{
			return 0;
		}
	}
}