<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_nama_barang extends CI_Model {
	public function __construct(){
		parent::__construct();
	}
	
	public function get_filter_nama_barang($filter_category){
		$sql = "SELECT m.*, c.category_name FROM gold_master_product_name m, gold_product_category c
				WHERE m.id_category = c.id AND m.id_category IN($filter_category)
				ORDER BY m.nama_barang";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_barang_by_id($id){
		$sql = "SELECT * FROM gold_master_product_name
				WHERE id = '$id'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function cek_product_name($product_name,$id_category){
		$sql = "SELECT * FROM gold_master_product_name
				WHERE nama_barang = '$product_name' AND id_category ='$id_category'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_master_product($nama_barang,$select_category,$created_date,$created_by){
		$sql = "INSERT INTO gold_master_product_name(id_category,nama_barang,created_date,created_by) VALUES ('$select_category','$nama_barang','$created_date','$created_by')";
		
		$this->db->query($sql);
	}
	
	public function update_master_product($id_barang, $nama_barang, $id_category){
		$sql = "UPDATE gold_master_product_name
				SET nama_barang = '$nama_barang', id_category = '$id_category'
				WHERE id = '$id_barang'";
		
		$this->db->query($sql);
	}
	
	public function get_master_by_category($id_category){
		$sql = "SELECT * FROM gold_master_product_name
				WHERE id_category = '$id_category'
				ORDER BY nama_barang";

		$query = $this->db->query($sql)->result();
		return $query;
	}
}