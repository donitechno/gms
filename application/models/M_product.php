<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_product extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->db= $this->load->database($_COOKIE['cabang'],TRUE);

	}
	
	public function get_posisi_pindah_box($per_tanggal,$filter_box_from,$filter_box_to){
		$sql = "SELECT * FROM gold_pindah_box
				WHERE trans_date > '$per_tanggal'
				ORDER BY trans_date DESC, created_date DESC";
	
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_posisi_detail_pajangan($per_tanggal,$filter_box_from,$filter_box_to,$filter_pindah_box){
		$sql = "SELECT s.*, k.karat_name, c.category_name, f.from_name
				FROM (((gold_product s 
				INNER JOIN gold_karat k on s.id_karat = k.id)
				INNER JOIN gold_product_category c on s.id_category = c.id)
				INNER JOIN gold_product_from f on s.id_from = f.id)
				WHERE (s.id_box BETWEEN $filter_box_from AND $filter_box_to AND s.in_date < '$per_tanggal' AND s.out_date = '0000-00-00 00:00:00') OR (s.in_date < '$per_tanggal' AND s.out_date > '$per_tanggal') OR (s.id IN($filter_pindah_box) AND s.in_date < '$per_tanggal') ORDER BY s.id";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_posisi_detail_pajangan_rk($per_tanggal,$filter_box_from,$filter_box_to,$filter_pindah_box){
		$sql = "SELECT s.id_karat, k.karat_name, COUNT(s.id_karat) as pcs, SUM(product_weight) as berat
				FROM (gold_product s 
				INNER JOIN gold_karat k on s.id_karat = k.id)
				WHERE (s.id_box BETWEEN $filter_box_from AND $filter_box_to AND s.in_date < '$per_tanggal' AND s.out_date = '0000-00-00 00:00:00') 
					  OR (s.in_date < '$per_tanggal' AND s.out_date > '$per_tanggal')
					  OR (s.id IN($filter_pindah_box) AND s.in_date < '$per_tanggal')
				GROUP BY s.id_karat      
				ORDER BY s.id_karat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_trans_number(){
		$sql = "SELECT * FROM gold_product 
				ORDER BY id DESC
				LIMIT 1";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_product_from(){
		$sql = "SELECT * FROM gold_product_from
				WHERE status = 'A'";
	
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_product_from_filter(){
		$sql = "SELECT * FROM gold_product_from";
	
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_product_from_detail($id_from){
		$sql = "SELECT * FROM gold_product_from
				WHERE id='$id_from'";
	
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_stock_in($stock_in_id,$select_karat,$select_box,$select_category,$select_from,$select_from_desc,$nama_barang,$berat_barang,$tgl_stock_in,$created_by){
		$sql = "INSERT INTO gold_stock_in(id,id_karat,id_box,id_category,id_from,id_from_desc,product_name,product_weight,trans_date,created_date,created_by) VALUES ('$stock_in_id','$select_karat','$select_box','$select_category','$select_from','$select_from_desc','$nama_barang','$berat_barang','$tgl_stock_in',CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function insert_product($product_id,$old_id,$select_karat,$select_box,$select_category,$select_from,$select_from_desc,$nama_barang,$berat_barang,$tgl_stock_in,$created_by){
		$sql = "INSERT INTO gold_product(id,id_lama,id_karat,id_box,id_category,id_from,product_from_desc,product_name,product_weight,in_date,created_date,created_by,last_updated) VALUES ('$product_id','$old_id','$select_karat','$select_box','$select_category','$select_from','$select_from_desc','$nama_barang','$berat_barang','$tgl_stock_in',CURRENT_TIMESTAMP(),'$created_by', CURRENT_TIMESTAMP())";
		
		$this->db->query($sql);
	}
	
	public function get_filter_stock_in($from_stock_in,$to_stock_in,$filter_category,$filter_from,$filter_box,$filter_karat){
		$sql = "SELECT s.*, k.karat_name, c.category_name, f.from_name
				FROM gold_stock_in s, gold_karat k, gold_product_category c, gold_product_from f
				WHERE s.id_karat = k.id AND s.id_category = c.id AND s.id_from = f.id AND s.id_category IN ($filter_category) AND s.id_from IN($filter_from) AND s.id_box IN($filter_box) AND s.id_karat IN($filter_karat) AND s.trans_date BETWEEN '$from_stock_in' AND '$to_stock_in'
				ORDER BY s.trans_date, s.created_date";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_product_date_before_old($date_data,$product_id){
		$sql = "SELECT s.*, k.karat_name, c.category_name, f.from_name
				FROM gold_product s, gold_karat k, gold_product_category c, gold_product_from f
				WHERE s.id_karat = k.id AND s.id_category = c.id AND s.id_from = f.id
				AND s.in_date < '$date_data' AND s.status = 'A' AND s.id_lama LIKE '%$product_id%' AND s.id_from != '3'
				ORDER BY s.in_date, s.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_product_date_before($date_data,$product_id){
		$sql = "SELECT s.*, k.karat_name, c.category_name, f.from_name
				FROM gold_product s, gold_karat k, gold_product_category c, gold_product_from f
				WHERE s.id_karat = k.id AND s.id_category = c.id AND s.id_from = f.id
				AND s.in_date < '$date_data' AND s.status = 'A' AND s.id LIKE '%$product_id%' AND s.id_from != '3'
				ORDER BY s.in_date, s.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_pindah_box_after($pindah_box_date,$product_id){
		$sql = "SELECT s.*, p.product_name, p.product_weight,k.karat_name, c.category_name
				FROM gold_pindah_box s, gold_product p, gold_karat k, gold_product_category c
				WHERE s.id_product = p.id AND s.id_karat = k.id AND p.id_category = c.id AND s.trans_date > '$pindah_box_date' AND s.id_product = '$product_id'
				ORDER BY s.trans_date, s.created_date";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_stock_out($stock_out_id,$product_id,$id_karat,$id_box,$so_reason,$tgl_stock_out,$created_by){
		$sql = "INSERT INTO gold_stock_out(id,id_product,id_karat,id_box,so_reason,trans_date,created_date,created_by) VALUES ('$stock_out_id','$product_id','$id_karat','$id_box','$so_reason','$tgl_stock_out',CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function update_product_stock_out($product_id,$tgl_stock_out){
		$sql = "UPDATE gold_product
				SET out_date = '$tgl_stock_out', status = 'O', last_updated = CURRENT_TIMESTAMP()
				WHERE id = '$product_id'";
		
		$this->db->query($sql);
	}
	
	public function get_filter_stock_out($from_stock_out,$to_stock_out,$filter_category,$filter_from,$filter_box,$filter_karat){
		$sql = "SELECT s.*, p.product_name, p.product_weight,k.karat_name, c.category_name, f.from_name
				FROM gold_stock_out s, gold_product p, gold_karat k, gold_product_category c, gold_product_from f
				WHERE s.id_product = p.id AND s.id_karat = k.id AND p.id_category = c.id AND p.id_from = f.id AND p.id_category IN ($filter_category) AND p.id_from IN($filter_from) AND s.id_box IN($filter_box) AND s.id_karat IN($filter_karat) AND s.trans_date BETWEEN '$from_stock_out' AND '$to_stock_out'
				ORDER BY s.trans_date, s.created_date";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_trans_number_pindah_box($product_id){
		$sql = "SELECT * FROM gold_pindah_box 
				WHERE id_product = '$product_id'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by){
		$sql = "INSERT INTO gold_pindah_box(id,id_product,id_karat,id_box_from,id_box_to,trans_date,created_date,created_by) VALUES ('$pindah_box_id','$product_id','$id_karat','$id_box','$id_box_to','$tgl_pindah_box',CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function update_product_pindah_box($product_id,$id_box_to){
		$sql = "UPDATE gold_product
				SET id_box = '$id_box_to', last_updated = CURRENT_TIMESTAMP()
				WHERE id = '$product_id'";
		
		$this->db->query($sql);
	}
	
	public function get_filter_pindah_box($from_pindah_box,$to_pindah_box,$filter_category,$filter_box_from,$filter_box_to,$filter_karat){
		$sql = "SELECT s.*, p.product_name, p.product_weight,k.karat_name, c.category_name
				FROM gold_pindah_box s, gold_product p, gold_karat k, gold_product_category c
				WHERE s.id_product = p.id AND s.id_karat = k.id AND p.id_category = c.id AND p.id_category IN ($filter_category) AND s.id_box_from IN($filter_box_from) AND s.id_box_to IN($filter_box_to) AND s.id_karat IN($filter_karat) AND s.trans_date BETWEEN '$from_pindah_box' AND '$to_pindah_box'
				ORDER BY s.trans_date, s.created_date";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_product_date_before_2($date_data,$product_id,$filter_product){
		$sql = "SELECT s.*, k.karat_name, c.category_name, f.from_name
				FROM gold_product s, gold_karat k, gold_product_category c, gold_product_from f
				WHERE s.id_karat = k.id AND s.id_category = c.id AND s.id_from = f.id
				AND s.in_date < '$date_data' AND s.status = 'A' AND s.id LIKE '%$product_id%' AND s.id NOT IN($filter_product) AND s.id_from != '3'
				ORDER BY s.in_date, s.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_product_date_before_2_old($date_data,$product_id,$filter_product){
		$sql = "SELECT s.*, k.karat_name, c.category_name, f.from_name
				FROM gold_product s, gold_karat k, gold_product_category c, gold_product_from f
				WHERE s.id_karat = k.id AND s.id_category = c.id AND s.id_from = f.id
				AND s.in_date < '$date_data' AND s.status = 'A' AND s.id_lama LIKE '%$product_id%' AND s.id NOT IN($filter_product) AND s.id_from != '3'
				ORDER BY s.in_date, s.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_product_by_id($product_id){
		$sql = "SELECT p.*, k.karat_name
				FROM (gold_product p 
				INNER JOIN gold_karat k on p.id_karat = k.id)
				WHERE p.id LIKE '%$product_id'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_product_date_buy($date_data,$product_id){
		$sql = "SELECT s.*, k.karat_name, c.category_name, f.from_name
				FROM (((gold_product s 
				INNER JOIN gold_karat k on s.id_karat = k.id)
				INNER JOIN gold_product_category c on s.id_category = c.id)
				INNER JOIN gold_product_from f on s.id_from = f.id)
				WHERE s.id LIKE '%$product_id%' AND s.in_date < '$date_data' AND s.out_date < '$date_data' AND s.status = 'S'
				ORDER BY s.in_date, s.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_product_date_buy_2($date_data,$product_id,$filter_product){
		$sql = "SELECT s.*, k.karat_name, c.category_name, f.from_name
				FROM (((gold_product s 
				INNER JOIN gold_karat k on s.id_karat = k.id)
				INNER JOIN gold_product_category c on s.id_category = c.id)
				INNER JOIN gold_product_from f on s.id_from = f.id)
				WHERE s.id LIKE '%$product_id%' AND s.id NOT IN($filter_product) AND s.in_date < '$date_data' AND s.out_date < '$date_data' AND s.status = 'S'
				ORDER BY s.in_date, s.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_product_date_buy_val($date_data,$product_id){
		$sql = "SELECT s.*, k.karat_name, c.category_name, f.from_name
				FROM (((gold_product s 
				INNER JOIN gold_karat k on s.id_karat = k.id)
				INNER JOIN gold_product_category c on s.id_category = c.id)
				INNER JOIN gold_product_from f on s.id_from = f.id)
				WHERE s.id = '$product_id' AND s.in_date < '$date_data' AND s.out_date < '$date_data' AND s.status = 'S'
				ORDER BY s.in_date, s.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_product_date_buy_val_2($date_data,$product_id,$filter_product){
		$sql = "SELECT s.*, k.karat_name, c.category_name, f.from_name
				FROM (((gold_product s 
				INNER JOIN gold_karat k on s.id_karat = k.id)
				INNER JOIN gold_product_category c on s.id_category = c.id)
				INNER JOIN gold_product_from f on s.id_from = f.id)
				WHERE s.id = '$product_id' AND s.id NOT IN($filter_product) AND s.in_date < '$date_data' AND s.out_date < '$date_data' AND s.status = 'S'
				ORDER BY s.in_date, s.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	/*public function get_rekap_stock_in($tgl_transaksi){
		$sql = "SELECT COUNT(s.id_karat) as pcs , SUM(product_weight) as berat , k.karat_name, s.id_karat
				FROM gold_stock_in s, gold_karat k
				WHERE s.id_karat = k.id AND s.trans_date = '$tgl_transaksi'
				GROUP BY s.id_karat
				ORDER BY s.id_karat";

		$query = $this->db->query($sql)->result();
		return $query;
	}*/
	
	public function get_rekap_stock_in($tgl_transaksi){
		$sql = "SELECT COUNT(s.id_karat) as pcs , SUM(product_weight) as berat , k.karat_name, s.id_karat
				FROM (gold_stock_in s
				INNER JOIN gold_karat k on s.id_karat = k.id)
				WHERE s.trans_date = '$tgl_transaksi'
				GROUP BY s.id_karat
				ORDER BY s.id_karat";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_rekap_stock_out($tgl_transaksi){
		$sql = "SELECT COUNT(s.id_karat) as pcs , SUM(p.product_weight) as berat , k.karat_name, s.id_karat
				FROM ((gold_stock_out s
				INNER JOIN gold_karat k on s.id_karat = k.id)
				INNER JOIN gold_product p on s.id_product = p.id)
				WHERE s.trans_date = '$tgl_transaksi'
				GROUP BY s.id_karat
				ORDER BY s.id_karat";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_full_id_product($id_product){
		$sql = "SELECT * FROM gold_product 
				WHERE id LIKE '%$id_product'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$id = $row->id;
			return $id;
		}else{
			$id = 0;
			return $id;
		}
	}
	
	public function get_lock_product($product_id){
		$sql = "SELECT s.*, k.karat_name, c.category_name, f.from_name
				FROM gold_product s, gold_karat k, gold_product_category c, gold_product_from f
				WHERE s.id_karat = k.id AND s.id_category = c.id AND s.id_from = f.id
				AND s.out_date = '0000-00-00 00:00:00' AND s.status = 'A' AND s.lock_status = 'Y' AND s.id LIKE '%$product_id%'
				ORDER BY s.in_date, s.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_lock_product_old($product_id){
		$sql = "SELECT s.*, k.karat_name, c.category_name, f.from_name
				FROM gold_product s, gold_karat k, gold_product_category c, gold_product_from f
				WHERE s.id_karat = k.id AND s.id_category = c.id AND s.id_from = f.id
				AND s.out_date = '0000-00-00 00:00:00' AND s.status = 'A' AND s.lock_status = 'Y' AND s.id_lama LIKE '%$product_id%' AND s.id_lama != 'NULL'
				ORDER BY s.in_date, s.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function unlock_product_control($id_product,$alasan_unlock,$created_by){
		$sql = "UPDATE gold_product
				SET lock_status = 'N', unlock_reason = '$alasan_unlock', unlock_by = '$created_by', unlock_date = CURRENT_TIMESTAMP(), last_updated = CURRENT_TIMESTAMP()
				WHERE id = '$id_product'";
		
		$this->db->query($sql);
	}
	
	public function get_unlock_status($id_product){
		$sql = "SELECT * FROM gold_product 
				WHERE id = '$id_product'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$status = $row->lock_status;
			return $status;
		}else{
			$status = 'Y';
			return $status;
		}
	}
	
	public function get_history_product($product_id){
		$sql = "SELECT s.*, k.karat_name, c.category_name, f.from_name
				FROM gold_product s, gold_karat k, gold_product_category c, gold_product_from f
				WHERE s.id_karat = k.id AND s.id_category = c.id AND s.id_from = f.id
				AND s.id LIKE '%$product_id%'
				ORDER BY s.in_date, s.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_pesanan_per($tgl_transaksi){
		$sql = "SELECT * FROM gold_main_pesanan WHERE trans_date <= '$tgl_transaksi' AND (ambil_date = '0000-00-00 00:00:00' OR ambil_date > '$tgl_transaksi') AND (updated_date = '0000-00-00 00:00:00' OR updated_date > '$tgl_transaksi')";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_tambah_ump($idpesan,$tgl_transaksi,$psnlength){
		$sql = "SELECT * FROM gold_mutasi_rp WHERE idmutasi LIKE 'PI%' AND SUBSTRING(idmutasi,14,$psnlength) = '$idpesan' AND description LIKE '%TAMBAH%' AND transdate > '$tgl_transaksi'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_kurang_ump($idpesan,$tgl_transaksi,$psnlength){
		$sql = "SELECT * FROM gold_mutasi_rp WHERE idmutasi LIKE 'PI%' AND SUBSTRING(idmutasi,14,$psnlength) = '$idpesan' AND description LIKE '%KURANG%' AND transdate > '$tgl_transaksi'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_history_product_old($product_id){
		$sql = "SELECT s.*, k.karat_name, c.category_name, f.from_name
				FROM gold_product s, gold_karat k, gold_product_category c, gold_product_from f
				WHERE s.id_karat = k.id AND s.id_category = c.id AND s.id_from = f.id
				AND s.id_lama LIKE '%$product_id%' AND s.id_lama != 'NULL'
				ORDER BY s.in_date, s.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_outdate_product_by_id($id_product){
		$sql = "SELECT * FROM gold_product 
				WHERE id LIKE '%$id_product'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$out_date = $row->out_date;
			return $out_date;
		}else{
			$out_date = 'UND';
			return $out_date;
		}
	}
	
	public function get_product_pindah($id_box){
		$sql = "SELECT * FROM gold_product
				WHERE id_box = '$id_box' AND out_date = '0000-00-00 00:00:00'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function update_box_temp($id_box_from,$id_box_to){
		$sql = "UPDATE gold_product
				SET id_box = '$id_box_to'
				WHERE id_box = '$id_box_from' AND out_date = '0000-00-00 00:00:00'";
		
		$this->db->query($sql);
	}
}