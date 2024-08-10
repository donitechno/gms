<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_sync extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->db= $this->load->database($_COOKIE['cabang'],TRUE);

	}
	
	public function get_dbsite(){
		$sql = "SELECT * FROM gold_site_aktif";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_all_bayar_nontunai(){
		$sql = "SELECT * FROM gold_bayar_non_tunai";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_bayar_nontunai($dbname,$id,$description,$account_number,$status,$created_date,$created_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_bayar_non_tunai(id,description,account_number,status,created_date,created_by) VALUES('$id','$description','$account_number','$status','$created_date','$created_by')
		ON DUPLICATE KEY UPDATE description = '$description', account_number = '$account_number', status = '$status', created_date = '$created_date', created_by = '$created_by'";
		
		$condb->query($sql);
	}
	
	public function get_all_box(){
		$sql = "SELECT * FROM gold_box";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_box($dbname,$id,$nama_box,$pesanan,$status){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_box(id,nama_box,pesanan,status) VALUES('$id','$nama_box','$pesanan','$status')
		ON DUPLICATE KEY UPDATE nama_box = '$nama_box', pesanan = '$pesanan', status = '$status'";
		
		$condb->query($sql);
	}
	
	public function get_all_coa_gr(){
		$sql = "SELECT * FROM gold_coa_gr";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_coa_gr($dbname,$accountnumber,$accountnumberint,$accountname,$accountgroup,$beginningbalance,$status,$type,$idkarat,$created_date,$created_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_coa_gr(accountnumber,accountnumberint,accountname,accountgroup,beginningbalance,status,type,idkarat,created_date,created_by) VALUES('$accountnumber','$accountnumberint','$accountname','$accountgroup','$beginningbalance','$status','$type','$idkarat','$created_date','$created_by')
		ON DUPLICATE KEY UPDATE accountnumberint = '$accountnumberint', accountname = '$accountname', accountgroup = '$accountgroup', beginningbalance = '$beginningbalance', status = '$status', type = '$type', idkarat = '$idkarat', created_date = '$created_date', created_by = '$created_by'";
		
		$condb->query($sql);
	}
	
	public function get_all_coa_rp(){
		$sql = "SELECT * FROM gold_coa_rp";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_coa_rp($dbname,$accountnumber,$accountnumberint,$accountname,$accountgroup,$beginningbalance,$status,$type,$created_date,$created_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_coa_rp(accountnumber,accountnumberint,accountname,accountgroup,beginningbalance,status,type,created_date,created_by) VALUES('$accountnumber','$accountnumberint','$accountname','$accountgroup','$beginningbalance','$status','$type','$created_date','$created_by')
		ON DUPLICATE KEY UPDATE accountnumberint = '$accountnumberint', accountname = '$accountname', accountgroup = '$accountgroup', beginningbalance = '$beginningbalance', status = '$status', type = '$type', created_date = '$created_date', created_by = '$created_by'";
		
		$condb->query($sql);
	}
	
	public function get_all_karyawan(){
		$sql = "SELECT * FROM gold_karyawan";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_karyawan($dbname,$username,$nama_karyawan,$kelompok,$accountnumber,$status,$created_date,$created_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_karyawan(username,nama_karyawan,kelompok,accountnumber,status,created_date,created_by) VALUES('$username','$nama_karyawan','$kelompok','$accountnumber','$status','$created_date','$created_by')
		ON DUPLICATE KEY UPDATE nama_karyawan = '$nama_karyawan', kelompok = '$kelompok', accountnumber = '$accountnumber', status = '$status', created_date = '$created_date', created_by = '$created_by'";
		
		$condb->query($sql);
	}
	
	public function get_all_kasir(){
		$sql = "SELECT * FROM gold_kasir";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_kasir($dbname,$id,$computer_name,$printer_name){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_kasir(id,computer_name,printer_name) VALUES('$id','$computer_name','$printer_name')
		ON DUPLICATE KEY UPDATE computer_name = '$computer_name', printer_name = '$printer_name'";
		
		$condb->query($sql);
	}
	
	public function get_tanggal_aktif(){
		$sql = "SELECT * FROM gold_tanggal_aktif";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_tanggal_aktif($dbname,$id,$id_kasir,$tanggal_aktif){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_tanggal_aktif(id,id_kasir,tanggal_aktif) VALUES('$id','$id_kasir','$tanggal_aktif')
		ON DUPLICATE KEY UPDATE id_kasir = '$id_kasir', tanggal_aktif = '$tanggal_aktif'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_titipan_gr($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT created_date FROM gold_titipan_gr
				ORDER BY created_date DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_all_titipan_gr($last_id){
		$sql = "SELECT * FROM gold_titipan_gr
		WHERE created_date >= '$last_id'
		ORDER BY created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	
	public function insert_titipan_gr($dbname,$id,$nama_pelanggan,$created_date,$created_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_titipan_gr(id,nama_pelanggan,created_date,created_by) VALUES('$id','$nama_pelanggan','$created_date','$created_by')
		ON DUPLICATE KEY UPDATE nama_pelanggan = '$nama_pelanggan', created_date = '$created_date', created_by = '$created_by'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_titipan_rp($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT created_date FROM gold_titipan_rp
				ORDER BY created_date DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_all_titipan_rp($last_id){
		$sql = "SELECT * FROM gold_titipan_rp
		WHERE created_date >= '$last_id'
		ORDER BY created_date";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_titipan_rp($dbname,$id,$nama_pelanggan,$created_date,$created_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_titipan_rp(id,nama_pelanggan,created_date,created_by) VALUES('$id','$nama_pelanggan','$created_date','$created_by')
		ON DUPLICATE KEY UPDATE nama_pelanggan = '$nama_pelanggan', created_date = '$created_date', created_by = '$created_by'";
		
		$condb->query($sql);
	}
	
	public function get_all_user(){
		$sql = "SELECT * FROM gold_user";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_user($dbname,$id,$username,$nama_user,$password_user,$priv_kasir,$priv_pembukuan,$priv_manager,$priv_admin,$salt,$picture,$status){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_user(id,username,nama_user,password_user,priv_kasir,priv_pembukuan,priv_manager,priv_admin,salt,picture,status) VALUES('$id','$username','$nama_user','$password_user','$priv_kasir','$priv_pembukuan','$priv_manager','$priv_admin','$salt','$picture','$status')
		ON DUPLICATE KEY UPDATE username = '$username', nama_user = '$nama_user', password_user = '$password_user', priv_kasir = '$priv_kasir', priv_pembukuan = '$priv_pembukuan', priv_manager = '$priv_manager', priv_admin = '$priv_admin', salt = '$salt', picture = '$picture', status = '$status'";
		
		$condb->query($sql);
	}
	
	public function get_dailyopen($from_date,$to_date){
		$sql = "SELECT * FROM gold_dailyopen
				WHERE last_updated BETWEEN '$from_date' AND '$to_date'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_dailyopen($dbname,$id,$do_date,$harga_emas,$created_date,$last_updated,$created_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_dailyopen(id,do_date,harga_emas,created_date,last_updated,created_by) VALUES('$id','$do_date','$harga_emas','$created_date','$last_updated','$created_by')
		ON DUPLICATE KEY UPDATE do_date = '$do_date', harga_emas = '$harga_emas', created_date = '$created_date', last_updated = '$last_updated', created_by = '$created_by'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_product_update($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT last_updated FROM gold_product
				ORDER BY last_updated DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_data_product($last_id){
		$sql = "SELECT * FROM gold_product
				WHERE last_updated >= '$last_id'
				ORDER BY last_updated";
				
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_product($dbname,$id,$id_lama,$id_karat,$id_box,$id_category,$id_from,$product_from_desc,$product_name,$product_weight,$in_date,$out_date,$id_sell,$sell_desc,$status,$created_date,$created_by,$lock_status,$unlock_date,$unlock_by,$unlock_reason,$last_updated){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_product(id,id_lama,id_karat,id_box,id_category,id_from,product_from_desc,product_name,product_weight,in_date,out_date,id_sell,sell_desc,status,created_date,created_by,lock_status,unlock_date,unlock_by,unlock_reason,last_updated) VALUES('$id','$id_lama','$id_karat','$id_box','$id_category','$id_from','$product_from_desc','$product_name','$product_weight','$in_date','$out_date','$id_sell','$sell_desc','$status','$created_date','$created_by','$lock_status','$unlock_date','$unlock_by','$unlock_reason','$last_updated')
		ON DUPLICATE KEY UPDATE id_lama = '$id_lama', id_karat = '$id_karat', id_box = '$id_box', id_category = '$id_category', id_from = '$id_from', product_from_desc = '$product_from_desc', product_name = '$product_name', product_weight = '$product_weight', in_date = '$in_date', out_date = '$out_date', id_sell = '$id_sell', sell_desc = '$sell_desc', status = '$status', created_date = '$created_date', created_by = '$created_by', lock_status = '$lock_status', unlock_date = '$unlock_date', unlock_by = '$unlock_by', unlock_reason = '$unlock_reason', last_updated = '$last_updated'";
		
		$condb->query($sql);
	}
	
	public function insert_customer($dbname,$cust_phone,$cust_address,$cust_name){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_customer(cust_phone,cust_address,cust_name) VALUES('$cust_phone','$cust_address','$cust_name')
		ON DUPLICATE KEY UPDATE cust_address = '$cust_address', cust_name = '$cust_name'";
		
		$condb->query($sql);
	}
	
	public function insert_customer_pesanan($dbname,$cust_phone,$cust_address,$cust_name){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_customer_pesanan(cust_phone,cust_address,cust_name) VALUES('$cust_phone','$cust_address','$cust_name')
		ON DUPLICATE KEY UPDATE cust_address = '$cust_address', cust_name = '$cust_name'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_main_beli($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT id FROM gold_main_pembelian
				ORDER BY id DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_main_pembelian($last_id){
		$sql = "SELECT * FROM gold_main_pembelian
				WHERE id >= $last_id
				ORDER BY id";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_main_pembelian($dbname,$id,$id_kasir,$transaction_code,$cust_service,$cust_phone,$cust_address,$cust_name,$total_price,$trans_date,$created_date,$created_by,$status){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_main_pembelian(id,id_kasir,transaction_code,cust_service,cust_phone,cust_address,cust_name,total_price,trans_date,created_date,created_by,status) VALUES('$id','$id_kasir','$transaction_code','$cust_service','$cust_phone','$cust_address','$cust_name','$total_price','$trans_date','$created_date','$created_by','$status')
		ON DUPLICATE KEY UPDATE id_kasir = '$id_kasir', transaction_code = '$transaction_code', cust_service = '$cust_service', cust_phone = '$cust_phone', cust_address = '$cust_address', cust_name = '$cust_name', total_price = '$total_price', trans_date = '$trans_date', created_date = '$created_date', created_by = '$created_by', status = '$status'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_detail_beli($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT id FROM gold_detail_pembelian
				ORDER BY id DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_detail_pembelian($last_id){
		$sql = "SELECT * FROM gold_detail_pembelian
				WHERE id >= $last_id
				ORDER BY id";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_detail_pembelian($dbname,$id,$transaction_code,$id_kasir,$id_product,$id_karat,$id_category,$nama_product,$product_pcs,$product_weight,$product_price,$trans_date,$created_date,$created_by,$status,$persentase,$weight_duaempat,$tujuan,$kirim_date,$created_kirim_date,$kirim_by,$last_update_kirim,$update_kirim_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_detail_pembelian(id,transaction_code,id_kasir,id_product,id_karat,id_category,nama_product,product_pcs,product_weight,product_price,trans_date,created_date,created_by,status,persentase,weight_duaempat,tujuan,kirim_date,created_kirim_date,kirim_by,last_update_kirim,update_kirim_by) VALUES('$id','$transaction_code','$id_kasir','$id_product','$id_karat','$id_category','$nama_product','$product_pcs','$product_weight','$product_price','$trans_date','$created_date','$created_by','$status','$persentase','$weight_duaempat','$tujuan','$kirim_date','$created_kirim_date','$kirim_by','$last_update_kirim','$update_kirim_by')
		ON DUPLICATE KEY UPDATE transaction_code = '$transaction_code', id_kasir = '$id_kasir', id_product = '$id_product', id_karat = '$id_karat', id_category = '$id_category', nama_product = '$nama_product', product_pcs = '$product_pcs', product_weight = '$product_weight', product_price = '$product_price', trans_date = '$trans_date', created_date = '$created_date', created_by = '$created_by', status = '$status', persentase = '$persentase', weight_duaempat = '$weight_duaempat', tujuan = '$tujuan', kirim_date = '$kirim_date', created_kirim_date = '$created_kirim_date', kirim_by = '$kirim_by', last_update_kirim = '$last_update_kirim', update_kirim_by = '$update_kirim_by'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_main_jual($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT id FROM gold_main_penjualan
				ORDER BY id DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_main_penjualan($last_id){
		$sql = "SELECT * FROM gold_main_penjualan
				WHERE id >= $last_id
				ORDER BY id";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_main_penjualan($dbname,$id,$id_kasir,$transaction_code,$cust_service,$cust_phone,$cust_address,$cust_name,$total_price,$bayar_1,$bayar_2,$jenis_bayar_1,$jenis_bayar_2,$trans_date,$created_date,$created_by,$status){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_main_penjualan(id,id_kasir,transaction_code,cust_service,cust_phone,cust_address,cust_name,total_price,bayar_1,bayar_2,jenis_bayar_1,jenis_bayar_2,trans_date,created_date,created_by,status) VALUES('$id','$id_kasir','$transaction_code','$cust_service','$cust_phone','$cust_address','$cust_name','$total_price','$bayar_1','$bayar_2','$jenis_bayar_1','$jenis_bayar_2','$trans_date','$created_date','$created_by','$status')
		ON DUPLICATE KEY UPDATE id_kasir = '$id_kasir', transaction_code = '$transaction_code', cust_service = '$cust_service', cust_phone = '$cust_phone', cust_address = '$cust_address', cust_name = '$cust_name', total_price = '$total_price', bayar_1 = '$bayar_1', bayar_2 = '$bayar_2', jenis_bayar_1 = '$jenis_bayar_1', jenis_bayar_2 = '$jenis_bayar_2', trans_date = '$trans_date', created_date = '$created_date', created_by = '$created_by', status = '$status'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_detail_jual($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT id FROM gold_detail_penjualan
				ORDER BY id DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_detail_penjualan($last_id){
		$sql = "SELECT * FROM gold_detail_penjualan
				WHERE id >= $last_id
				ORDER BY id";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_detail_penjualan($dbname,$id,$transaction_code,$id_kasir,$id_product,$id_box,$id_karat,$nama_product,$product_desc,$product_weight,$product_price,$trans_date,$created_date,$created_by,$status){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_detail_penjualan(id,transaction_code,id_kasir,id_product,id_box,id_karat,nama_product,product_desc,product_weight,product_price,trans_date,created_date,created_by,status) VALUES('$id','$transaction_code','$id_kasir','$id_product','$id_box','$id_karat','$nama_product','$product_desc','$product_weight','$product_price','$trans_date','$created_date','$created_by','$status')
		ON DUPLICATE KEY UPDATE transaction_code = '$transaction_code', id_kasir = '$id_kasir', id_product = '$id_product', id_box = '$id_box', id_karat = '$id_karat', nama_product = '$nama_product', product_desc = '$product_desc', product_weight = '$product_weight', product_price = '$product_price', trans_date = '$trans_date', created_date = '$created_date', created_by = '$created_by', status = '$status'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_main_pesanan($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT last_updated FROM gold_main_pesanan
				ORDER BY last_updated DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_main_pesanan($last_id){
		$sql = "SELECT * FROM gold_main_pesanan
				WHERE last_updated >= '$last_id'
				ORDER BY last_updated";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_main_pesanan($dbname,$id_pesanan,$cust_name,$cust_address,$cust_phone,$ump_val,$total_trans,$trans_date,$created_date,$created_by,$box_date,$box_by,$box_created_date,$ambil_date,$ambil_by,$ambil_created_date,$updated_date,$updated_by,$last_updated,$grosir_use,$status){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_main_pesanan(id_pesanan,cust_name,cust_address,cust_phone,ump_val,total_trans,trans_date,created_date,created_by,box_date,box_by,box_created_date,ambil_date,ambil_by,ambil_created_date,updated_date,updated_by,last_updated,grosir_use,status) VALUES('$id_pesanan','$cust_name','$cust_address','$cust_phone','$ump_val','$total_trans','$trans_date','$created_date','$created_by','$box_date','$box_by','$box_created_date','$ambil_date','$ambil_by','$ambil_created_date','$updated_date','$updated_by','$last_updated','$grosir_use','$status')
		ON DUPLICATE KEY UPDATE cust_name = '$cust_name', cust_address = '$cust_address', cust_phone = '$cust_phone', ump_val = '$ump_val', total_trans = '$total_trans', trans_date = '$trans_date', created_date = '$created_date', created_by = '$created_by', box_date = '$box_date', box_by = '$box_by', box_created_date = '$box_created_date', ambil_date = '$ambil_date', ambil_by = '$ambil_by', ambil_created_date = '$ambil_created_date', updated_date = '$updated_date', updated_by = '$updated_by', last_updated = '$last_updated', grosir_use = '$grosir_use', status = '$status'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_detail_pesanan($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT last_updated FROM gold_detail_pesanan
				ORDER BY last_updated DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_detail_pesanan($last_id){
		$sql = "SELECT * FROM gold_detail_pesanan
				WHERE last_updated >= '$last_id'
				ORDER BY last_updated";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_detail_pesanan($dbname,$id,$id_pesanan,$id_karat,$id_category,$nama_pesanan,$id_product,$product_weight,$harga_jual,$trans_date,$created_date,$created_by,$box_date,$box_by,$box_created_date,$ambil_date,$ambil_by,$ambil_created_date,$updated_date,$updated_by,$last_updated,$status){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_detail_pesanan(id,id_pesanan,id_karat,id_category,nama_pesanan,id_product,product_weight,harga_jual,trans_date,created_date,created_by,box_date,box_by,box_created_date,ambil_date,ambil_by,ambil_created_date,updated_date,updated_by,last_updated,status) VALUES('$id','$id_pesanan','$id_karat','$id_category','$nama_pesanan','$id_product','$product_weight','$harga_jual','$trans_date','$created_date','$created_by','$box_date','$box_by','$box_created_date','$ambil_date','$ambil_by','$ambil_created_date','$updated_date','$updated_by','$last_updated','$status')
		ON DUPLICATE KEY UPDATE id_pesanan = '$id_pesanan', id_karat = '$id_karat', id_category = '$id_category', nama_pesanan = '$nama_pesanan', id_product = '$id_product', product_weight = '$product_weight', harga_jual = '$harga_jual', trans_date = '$trans_date', created_date = '$created_date', created_by = '$created_by', box_date = '$box_date', box_by = '$box_by', box_created_date = '$box_created_date', ambil_date = '$ambil_date', ambil_by = '$ambil_by', ambil_created_date = '$ambil_created_date', updated_date = '$updated_date', updated_by = '$updated_by', last_updated = '$last_updated', status = '$status'";
		
		$condb->query($sql);
	}
	
	public function get_master_product($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_master_product_name
				WHERE created_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_master_product_name($dbname,$id,$id_category,$nama_barang,$created_date,$created_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_master_product_name(id,id_category,nama_barang,created_date,created_by) VALUES('$id','$id_category','$nama_barang','$created_date','$created_by')
		ON DUPLICATE KEY UPDATE id_category = '$id_category', nama_barang = '$nama_barang', created_date = '$created_date', created_by = '$created_by'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_mutasi_gr($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT createddate FROM gold_mutasi_gr
				ORDER BY createddate DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_gr($last_id){
		$sql = "SELECT * FROM gold_mutasi_gr
				WHERE createddate >= '$last_id'
				ORDER BY createddate";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_mutasi_gr($dbname,$idsite,$idmutasi,$tipemutasi,$idkarat,$fromaccount,$toaccount,$value,$description,$transdate,$createddate,$createdby){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_mutasi_gr(idsite,idmutasi,tipemutasi,idkarat,fromaccount,toaccount,value,description,transdate,createddate,createdby) VALUES('$idsite','$idmutasi','$tipemutasi','$idkarat','$fromaccount','$toaccount','$value','$description','$transdate','$createddate','$createdby')
		ON DUPLICATE KEY UPDATE idsite = '$idsite', tipemutasi = '$tipemutasi', idkarat = '$idkarat', fromaccount = '$fromaccount', toaccount = '$toaccount', value = '$value', description = '$description', transdate = '$transdate', createddate = '$createddate', createdby = '$createdby'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_mutasi_gr_hapus($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT deleteddate FROM gold_mutasi_gr_hapus
				ORDER BY deleteddate DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_gr_hapus($last_id){
		$sql = "SELECT * FROM gold_mutasi_gr_hapus
				WHERE deleteddate >= '$last_id'
				ORDER BY deleteddate";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_mutasi_gr_hapus($dbname,$idsite,$idmutasi,$tipemutasi,$idkarat,$fromaccount,$toaccount,$value,$description,$transdate,$createddate,$createdby,$deleteddate,$deletedby){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_mutasi_gr_hapus(idsite,idmutasi,tipemutasi,idkarat,fromaccount,toaccount,value,description,transdate,createddate,createdby,deleteddate,deletedby) VALUES('$idsite','$idmutasi','$tipemutasi','$idkarat','$fromaccount','$toaccount','$value','$description','$transdate','$createddate','$createdby','$deleteddate','$deletedby')
		ON DUPLICATE KEY UPDATE idsite = '$idsite', tipemutasi = '$tipemutasi', idkarat = '$idkarat', fromaccount = '$fromaccount', toaccount = '$toaccount', value = '$value', description = '$description', transdate = '$transdate', createddate = '$createddate', createdby = '$createdby', deleteddate = '$deleteddate', deletedby = '$deletedby'";
		
		$condb->query($sql);
		
		$sql2 = "DELETE FROM gold_mutasi_gr WHERE idmutasi = '$idmutasi'";
		
		$condb->query($sql2);
		
		$type_trans = substr($idmutasi, 0, 2);
		if($type_trans == 'BR'){
			$idtrans = substr($idmutasi, 0, -2);
			
			$sql3 = "UPDATE gold_main_pembelian SET status = 'X'
					 WHERE transaction_code = '$idtrans'";
		
			$condb->query($sql3);
			
			$sql4 = "UPDATE gold_detail_pembelian SET status = 'X'
					 WHERE transaction_code = '$idtrans'";
		
			$condb->query($sql4);
		}else if($type_trans == 'JR' || $type_trans == 'JP'){
			$idtrans = substr($idmutasi, 0, -2);
			
			$sql3 = "UPDATE gold_main_penjualan SET status = 'X'
					 WHERE transaction_code = '$idtrans'";
		
			$condb->query($sql3);
			
			$sql4 = "UPDATE gold_detail_penjualan SET status = 'X'
					 WHERE transaction_code = '$idtrans'";
		
			$condb->query($sql4);
		}
		
	}
	
	public function get_last_id_mutasi_rp($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT createddate FROM gold_mutasi_rp
				ORDER BY createddate DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_rp($last_id){
		$sql = "SELECT * FROM gold_mutasi_rp
				WHERE createddate >= '$last_id'
				ORDER BY createddate";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_mutasi_rp($dbname,$idsite,$idmutasi,$tipemutasi,$fromaccount,$toaccount,$value,$description,$transdate,$createddate,$createdby){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_mutasi_rp(idsite,idmutasi,tipemutasi,fromaccount,toaccount,value,description,transdate,createddate,createdby) VALUES('$idsite','$idmutasi','$tipemutasi','$fromaccount','$toaccount','$value','$description','$transdate','$createddate','$createdby')
		ON DUPLICATE KEY UPDATE idsite = '$idsite', tipemutasi = '$tipemutasi', fromaccount = '$fromaccount', toaccount = '$toaccount', value = '$value', description = '$description', transdate = '$transdate', createddate = '$createddate', createdby = '$createdby'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_mutasi_rp_hapus($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT deleteddate FROM gold_mutasi_rp_hapus
				ORDER BY deleteddate DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_rp_hapus($last_id){
		$sql = "SELECT * FROM gold_mutasi_rp_hapus
				WHERE deleteddate >= '$last_id'
				ORDER BY deleteddate";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_mutasi_rp_hapus($dbname,$idsite,$idmutasi,$tipemutasi,$fromaccount,$toaccount,$value,$description,$transdate,$createddate,$createdby,$deleteddate,$deletedby){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_mutasi_rp_hapus(idsite,idmutasi,tipemutasi,fromaccount,toaccount,value,description,transdate,createddate,createdby,deleteddate,deletedby) VALUES('$idsite','$idmutasi','$tipemutasi','$fromaccount','$toaccount','$value','$description','$transdate','$createddate','$createdby','$deleteddate','$deletedby')
		ON DUPLICATE KEY UPDATE idsite = '$idsite', tipemutasi = '$tipemutasi', fromaccount = '$fromaccount', toaccount = '$toaccount', value = '$value', description = '$description', transdate = '$transdate', createddate = '$createddate', createdby = '$createdby', deleteddate = '$deleteddate', deletedby = '$deletedby'";
		
		$condb->query($sql);
		
		$sql2 = "DELETE FROM gold_mutasi_rp WHERE idmutasi = '$idmutasi'";
		
		$condb->query($sql2);
		
		$type_trans = substr($idmutasi, 0, 2);
		if($type_trans == 'BR'){
			$idtrans = substr($idmutasi, 0, -2);
			
			$sql3 = "UPDATE gold_main_pembelian SET status = 'X'
					 WHERE transaction_code = '$idtrans'";
		
			$condb->query($sql3);
			
			$sql4 = "UPDATE gold_detail_pembelian SET status = 'X'
					 WHERE transaction_code = '$idtrans'";
		
			$condb->query($sql4);
		}else if($type_trans == 'JR' || $type_trans == 'JP'){
			$idtrans = substr($idmutasi, 0, -2);
			
			$sql3 = "UPDATE gold_main_penjualan SET status = 'X'
					 WHERE transaction_code = '$idtrans'";
		
			$condb->query($sql3);
			
			$sql4 = "UPDATE gold_detail_penjualan SET status = 'X'
					 WHERE transaction_code = '$idtrans'";
		
			$condb->query($sql4);
		}
	}
	
	public function get_last_id_mutasi_pengadaan($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT id FROM gold_mutasi_pengadaan
				ORDER BY id DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_pengadaan($last_id){
		$sql = "SELECT * FROM gold_mutasi_pengadaan
				WHERE id >= '$last_id'
				ORDER BY id";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_mutasi_pengadaan($dbname,$id,$id_pengiriman,$from_buy,$tipe,$fromaccount,$toaccount,$description,$dua_empat,$semsanam,$juhlima,$juhtus,$total_konv,$trans_date,$created_date,$created_by,$last_updated,$last_updated_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_mutasi_pengadaan(id,id_pengiriman,from_buy,tipe,fromaccount,toaccount,description,dua_empat,semsanam,juhlima,juhtus,total_konv,trans_date,created_date,created_by,last_updated,last_updated_by) VALUES('$id','$id_pengiriman','$from_buy','$tipe','$fromaccount','$toaccount','$description','$dua_empat','$semsanam','$juhlima','$juhtus','$total_konv','$trans_date','$created_date','$created_by','$last_updated','$last_updated_by')
		ON DUPLICATE KEY UPDATE id_pengiriman = '$id_pengiriman', from_buy = '$from_buy', tipe = '$tipe', fromaccount = '$fromaccount', toaccount = '$toaccount', description = '$description', dua_empat = '$dua_empat', semsanam = '$semsanam', juhlima = '$juhlima', juhtus = '$juhtus', total_konv = '$total_konv', trans_date = '$trans_date', created_date = '$created_date', created_by = '$created_by', last_updated = '$last_updated', last_updated_by = '$last_updated_by'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_mutasi_pengadaan_hapus($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT id FROM gold_mutasi_pengadaan_hapus
				ORDER BY id DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_pengadaan_hapus($last_id){
		$sql = "SELECT * FROM gold_mutasi_pengadaan_hapus
				WHERE id >= '$last_id'
				ORDER BY id";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_mutasi_pengadaan_hapus($dbname,$id,$id_pengiriman,$from_buy,$tipe,$fromaccount,$toaccount,$description,$dua_empat,$semsanam,$juhlima,$juhtus,$total_konv,$trans_date,$deleted_date,$deleted_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_mutasi_pengadaan_hapus(id,id_pengiriman,from_buy,tipe,fromaccount,toaccount,description,dua_empat,semsanam,juhlima,juhtus,total_konv,trans_date,deleted_date,deleted_by) VALUES('$id','$id_pengiriman','$from_buy','$tipe','$fromaccount','$toaccount','$description','$dua_empat','$semsanam','$juhlima','$juhtus','$total_konv','$trans_date','$deleted_date','$deleted_by')
		ON DUPLICATE KEY UPDATE id_pengiriman = '$id_pengiriman', from_buy = '$from_buy', tipe = '$tipe', fromaccount = '$fromaccount', toaccount = '$toaccount', description = '$description', dua_empat = '$dua_empat', semsanam = '$semsanam', juhlima = '$juhlima', juhtus = '$juhtus', total_konv = '$total_konv', trans_date = '$trans_date', deleted_date = '$deleted_date', deleted_by = '$deleted_by'";
		
		$condb->query($sql);
		
		$sql2 = "DELETE FROM gold_mutasi_pengadaan WHERE id_pengiriman = '$id_pengiriman'";
		
		$condb->query($sql2);
	}
	
	public function get_last_id_mutasi_titip_pgd($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT id FROM gold_mutasi_titip_pengadaan
				ORDER BY id DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_titip_pgd($last_id){
		$sql = "SELECT * FROM gold_mutasi_titip_pengadaan
				WHERE id >= '$last_id'
				ORDER BY id";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_mutasi_titip_pengadaan($dbname,$id,$id_pengiriman,$from_buy,$tipe,$fromaccount,$toaccount,$description,$dua_empat,$semsanam,$juhlima,$juhtus,$total_konv,$trans_date,$created_date,$created_by,$last_updated,$last_updated_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_mutasi_titip_pengadaan(id,id_pengiriman,from_buy,tipe,fromaccount,toaccount,description,dua_empat,semsanam,juhlima,juhtus,total_konv,trans_date,created_date,created_by,last_updated,last_updated_by) VALUES('$id','$id_pengiriman','$from_buy','$tipe','$fromaccount','$toaccount','$description','$dua_empat','$semsanam','$juhlima','$juhtus','$total_konv','$trans_date','$created_date','$created_by','$last_updated','$last_updated_by')
		ON DUPLICATE KEY UPDATE id_pengiriman = '$id_pengiriman', from_buy = '$from_buy', tipe = '$tipe', fromaccount = '$fromaccount', toaccount = '$toaccount', description = '$description', dua_empat = '$dua_empat', semsanam = '$semsanam', juhlima = '$juhlima', juhtus = '$juhtus', total_konv = '$total_konv', trans_date = '$trans_date', created_date = '$created_date', created_by = '$created_by', last_updated = '$last_updated', last_updated_by = '$last_updated_by'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_mutasi_titip_pgd_hapus($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT id FROM gold_mutasi_titip_pengadaan_hapus
				ORDER BY id DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_titip_pgd_hapus($last_id){
		$sql = "SELECT * FROM gold_mutasi_titip_pengadaan_hapus
				WHERE id >= '$last_id'
				ORDER BY id";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_mutasi_titip_pengadaan_hapus($dbname,$id,$id_pengiriman,$from_buy,$tipe,$fromaccount,$toaccount,$description,$dua_empat,$semsanam,$juhlima,$juhtus,$total_konv,$trans_date,$deleted_date,$deleted_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_mutasi_titip_pengadaan_hapus(id,id_pengiriman,from_buy,tipe,fromaccount,toaccount,description,dua_empat,semsanam,juhlima,juhtus,total_konv,trans_date,deleted_date,deleted_by) VALUES('$id','$id_pengiriman','$from_buy','$tipe','$fromaccount','$toaccount','$description','$dua_empat','$semsanam','$juhlima','$juhtus','$total_konv','$trans_date','$deleted_date','$deleted_by')
		ON DUPLICATE KEY UPDATE id_pengiriman = '$id_pengiriman', from_buy = '$from_buy', tipe = '$tipe', fromaccount = '$fromaccount', toaccount = '$toaccount', description = '$description', dua_empat = '$dua_empat', semsanam = '$semsanam', juhlima = '$juhlima', juhtus = '$juhtus', total_konv = '$total_konv', trans_date = '$trans_date', deleted_date = '$deleted_date', deleted_by = '$deleted_by'";
		
		$condb->query($sql);
		
		$sql2 = "DELETE FROM gold_mutasi_pengadaan WHERE id_pengiriman = '$id_pengiriman'";
		
		$condb->query($sql2);
	}
	
	public function get_last_id_mutasi_reparasi($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT id FROM gold_mutasi_reparasi
				ORDER BY id DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_reparasi($last_id){
		$sql = "SELECT * FROM gold_mutasi_reparasi
				WHERE id >= '$last_id'
				ORDER BY id";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_mutasi_reparasi($dbname,$id,$id_pengiriman,$from_buy,$tipe,$fromaccount,$toaccount,$description,$dua_empat,$dua_empat_konv,$semsanam,$semsanam_konv,$juhlima,$juhlima_konv,$juhtus,$juhtus_konv,$total_konv,$trans_date,$created_date,$created_by,$last_updated,$last_updated_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_mutasi_reparasi(id,id_pengiriman,from_buy,tipe,fromaccount,toaccount,description,dua_empat,dua_empat_konv,semsanam,semsanam_konv,juhlima,juhlima_konv,juhtus,juhtus_konv,total_konv,trans_date,created_date,created_by,last_updated,last_updated_by) VALUES('$id','$id_pengiriman','$from_buy','$tipe','$fromaccount','$toaccount','$description','$dua_empat','$dua_empat_konv','$semsanam','$semsanam_konv','$juhlima','$juhlima_konv','$juhtus','$juhtus_konv','$total_konv','$trans_date','$created_date','$created_by','$last_updated','$last_updated_by')
		ON DUPLICATE KEY UPDATE id_pengiriman = '$id_pengiriman', from_buy = '$from_buy', tipe = '$tipe', fromaccount = '$fromaccount', toaccount = '$toaccount', description = '$description', dua_empat = '$dua_empat', dua_empat_konv = '$dua_empat_konv', semsanam = '$semsanam', semsanam_konv = '$semsanam_konv', juhlima = '$juhlima', juhlima_konv = '$juhlima_konv', juhtus = '$juhtus', juhtus_konv = '$juhtus_konv', total_konv = '$total_konv', trans_date = '$trans_date', created_date = '$created_date', created_by = '$created_by', last_updated = '$last_updated', last_updated_by = '$last_updated_by'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_mutasi_reparasi_hapus($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT id FROM gold_mutasi_reparasi_hapus
				ORDER BY id DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_reparasi_hapus($last_id){
		$sql = "SELECT * FROM gold_mutasi_reparasi_hapus
				WHERE id >= '$last_id'
				ORDER BY id";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_mutasi_reparasi_hapus($dbname,$id,$id_pengiriman,$from_buy,$tipe,$fromaccount,$toaccount,$description,$dua_empat,$dua_empat_konv,$semsanam,$semsanam_konv,$juhlima,$juhlima_konv,$juhtus,$juhtus_konv,$total_konv,$trans_date,$deleted_date,$deleted_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_mutasi_reparasi_hapus(id,id_pengiriman,from_buy,tipe,fromaccount,toaccount,description,dua_empat,dua_empat_konv,semsanam,semsanam_konv,juhlima,juhlima_konv,juhtus,juhtus_konv,total_konv,trans_date,deleted_date,deleted_by) VALUES('$id','$id_pengiriman','$from_buy','$tipe','$fromaccount','$toaccount','$description','$dua_empat','$dua_empat_konv','$semsanam','$semsanam_konv','$juhlima','$juhlima_konv','$juhtus','$juhtus_konv','$total_konv','$trans_date','$deleted_date','$deleted_by')
		ON DUPLICATE KEY UPDATE id_pengiriman = '$id_pengiriman', from_buy = '$from_buy', tipe = '$tipe', fromaccount = '$fromaccount', toaccount = '$toaccount', description = '$description', dua_empat = '$dua_empat', dua_empat_konv = '$dua_empat_konv', semsanam = '$semsanam', semsanam_konv = '$semsanam_konv', juhlima = '$juhlima', juhlima_konv = '$juhlima_konv', juhtus = '$juhtus', juhtus_konv = '$juhtus_konv', total_konv = '$total_konv', trans_date = '$trans_date', deleted_date = '$deleted_date', deleted_by = '$deleted_by'";
		
		$condb->query($sql);
		
		$sql2 = "DELETE FROM gold_mutasi_reparasi WHERE id_pengiriman = '$id_pengiriman'";
		
		$condb->query($sql2);
	}
	
	public function get_last_id_pindah_box($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT created_date FROM gold_pindah_box
				ORDER BY created_date DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_pindah_box($last_id){
		$sql = "SELECT * FROM gold_pindah_box
				WHERE created_date >= '$last_id'
				ORDER BY created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_pindah_box($dbname,$id,$id_product,$id_karat,$id_box_from,$id_box_to,$trans_date,$created_date,$created_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_pindah_box(id,id_product,id_karat,id_box_from,id_box_to,trans_date,created_date,created_by) VALUES('$id','$id_product','$id_karat','$id_box_from','$id_box_to','$trans_date','$created_date','$created_by')
		ON DUPLICATE KEY UPDATE id_product = '$id_product', id_karat = '$id_karat', id_box_from = '$id_box_from', id_box_to = '$id_box_to', trans_date = '$trans_date', created_date = '$created_date', created_by = '$created_by'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_stock_in($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT created_date FROM gold_stock_in
				ORDER BY created_date DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_stock_in($last_id){
		$sql = "SELECT * FROM gold_stock_in
				WHERE created_date >= '$last_id'
				ORDER BY created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_stock_in($dbname,$id,$id_karat,$id_box,$id_category,$id_from,$id_from_desc,$product_name,$product_weight,$trans_date,$created_date,$created_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_stock_in(id,id_karat,id_box,id_category,id_from,id_from_desc,product_name,product_weight,trans_date,created_date,created_by) VALUES('$id','$id_karat','$id_box','$id_category','$id_from','$id_from_desc','$product_name','$product_weight','$trans_date','$created_date','$created_by')
		ON DUPLICATE KEY UPDATE id_karat = '$id_karat', id_box = '$id_box', id_category = '$id_category', id_from = '$id_from', id_from_desc = '$id_from_desc', product_name = '$product_name', product_weight = '$product_weight', trans_date = '$trans_date', created_date = '$created_date', created_by = '$created_by'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_stock_out($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT created_date FROM gold_stock_out
				ORDER BY created_date DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_stock_out($last_id){
		$sql = "SELECT * FROM gold_stock_out
				WHERE created_date >= '$last_id'
				ORDER BY created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_stock_out($dbname,$id,$id_product,$id_karat,$id_box,$so_reason,$trans_date,$created_date,$created_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_stock_out(id,id_product,id_karat,id_box,so_reason,trans_date,created_date,created_by) VALUES('$id','$id_product','$id_karat','$id_box','$so_reason','$trans_date','$created_date','$created_by')
		ON DUPLICATE KEY UPDATE id_product = '$id_product', id_karat = '$id_karat', id_box = '$id_box', so_reason = '$so_reason', trans_date = '$trans_date', created_date = '$created_date', created_by = '$created_by'";
		
		$condb->query($sql);
	}
	
	public function get_periode(){
		$sql = "SELECT * FROM gold_periode";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_periode($dbname,$id,$from_date,$to_date,$map,$created_date,$created_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_periode(id,from_date,to_date,map,created_date,created_by) VALUES('$id','$from_date','$to_date','$map','$created_date','$created_by')
		ON DUPLICATE KEY UPDATE from_date = '$from_date', to_date = '$to_date', map = '$map', created_date = '$created_date', created_by = '$created_by'";
		
		$condb->query($sql);
	}
	
	public function get_last_id_detail_trans_cabang($dbname){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "SELECT id FROM gold_detail_trans_cabang
				ORDER BY id DESC LIMIT 1";
		
		$query = $condb->query($sql)->result();
		return $query;
	}
	
	public function get_detail_trans_cabang($last_id){
		$sql = "SELECT * FROM gold_detail_trans_cabang WHERE id >= '$last_id'
				ORDER BY id";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_detail_trans_cabang($dbname,$id,$transaction_code,$cabang,$tipe,$description,$id_karat,$berat_real,$berat_konversi,$persentase,$trans_date,$created_date,$created_by){
		$condb = $this->load->database($dbname, TRUE);
		
		$sql = "INSERT INTO gold_detail_trans_cabang(id,transaction_code,cabang,tipe,description,id_karat,berat_real,berat_konversi,persentase,trans_date,created_date,created_by) VALUES('$id','$transaction_code','$cabang','$tipe','$description','$id_karat','$berat_real','$berat_konversi','$persentase','$trans_date','$created_date','$created_by')
		ON DUPLICATE KEY UPDATE transaction_code = '$transaction_code', cabang = '$cabang', tipe = '$tipe', description = '$description', id_karat = '$id_karat', berat_real = '$berat_real', berat_konversi = '$berat_konversi', persentase = '$persentase', trans_date = '$trans_date', created_date = '$created_date', created_by = '$created_by'";
		
		$condb->query($sql);
	}
}