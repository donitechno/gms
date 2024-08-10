<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_kelompok_barang extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->db= $this->load->database($_COOKIE['cabang'],TRUE);

	}
	
	public function get_all_product_category(){
		$sql = "SELECT * FROM gold_product_category";
	
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_product_category(){
		$sql = "SELECT * FROM gold_product_category
				WHERE status = 'A'
				ORDER BY category_name";
	
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_category_by_id($id_category){
		$sql = "SELECT * FROM gold_product_category
				WHERE id='$id_category'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_category_id_by_name($product_category){
		$sql = "SELECT * FROM gold_product_category
				WHERE category_name='$product_category'";
		
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 

			return $row->id;
		}else{
			$id = 0;
			return $id;
		}
	}
	
	public function insert_category_product($kelompok_barang){
		$sql = "INSERT INTO gold_product_category(category_name,status) VALUES ('$kelompok_barang','A')";
		
		$this->db->query($sql);
	}
	
	public function update_category_product($id_category, $kelompok_barang){
		$sql = "UPDATE gold_product_category
				SET category_name = '$kelompok_barang'
				WHERE id = '$id_category'";
		
		$this->db->query($sql);
	}
	
	public function change_category_status($id_category, $status_category){
		$sql = "UPDATE gold_product_category
				SET status = '$status_category'
				WHERE id = '$id_category'";
		
		$this->db->query($sql);
	}
	
	public function get_category_name_by_id($id){
		$sql = "SELECT * FROM gold_product_category
				WHERE id='$id'";
		
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 

			return $row->category_name;
		}else{
			$id = 0;
			return $id;
		}
	}
}