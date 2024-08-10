<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_pos extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->db= $this->load->database($_COOKIE['cabang'],TRUE);

	}
	
	public function get_tanggal_aktif($id_kasir){
		$sql = "SELECT * FROM gold_tanggal_aktif
				WHERE id_kasir = '$id_kasir'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_do_by_date($tanggal_aktif){
		$sql = "SELECT * FROM gold_dailyopen
				WHERE do_date = '$tanggal_aktif'";

		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$harga_emas = $row->harga_emas;
			return $harga_emas;
		}else{
			$harga_emas = 0;
			return $harga_emas;
		}
	}
	
	public function get_last_do(){
		$sql = "SELECT * FROM gold_dailyopen
				ORDER BY do_date DESC
				LIMIT 1";

		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$harga_emas = $row->harga_emas;
			return $harga_emas;
		}else{
			$harga_emas = 0;
			return $harga_emas;
		}
	}
	
	public function cek_do_by_date($tanggal_aktif){
		$sql = "SELECT * FROM gold_dailyopen
				WHERE do_date = '$tanggal_aktif'";

		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$flag = 'Y';
			return $flag;
		}else{
			$flag = 'N';
			return $flag;
		}
	}
	
	public function insert_do($do_date,$harga_emas,$created_by){
		$sql = "INSERT INTO gold_dailyopen(do_date,harga_emas,created_date,last_updated,created_by) VALUES('$do_date','$harga_emas',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function update_do($do_date,$harga_emas){
		$sql = "UPDATE gold_dailyopen
				SET harga_emas = '$harga_emas', last_updated = CURRENT_TIMESTAMP()
				WHERE do_date = '$do_date'";
		
		$this->db->query($sql);
	}
	
	public function update_tanggal_aktif($do_date,$id_kasir){
		$sql = "UPDATE gold_tanggal_aktif
				SET tanggal_aktif = '$do_date'
				WHERE id_kasir = '$id_kasir'";
		
		$this->db->query($sql);
	}
	
	public function get_do_formula_struk(){
		$sql = "SELECT d.*, k.karat_name FROM gold_do_formula d, gold_karat k
				WHERE d.id_karat = k.id AND d.tampil_struk = 'Y'
				ORDER BY id";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_harga_struk_by_id($id_karat,$berat_barang){
		$sql = "SELECT * FROM gold_setting_harga
				WHERE id_karat = '$id_karat' AND dari_berat <= '$berat_barang' AND sampai_berat >= '$berat_barang'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_bayar_nt(){
		$sql = "SELECT * FROM gold_bayar_non_tunai
				WHERE status = 'A'
				ORDER BY description";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_user_cs(){
		$sql = "SELECT * FROM gold_karyawan
				WHERE kelompok IN ('KARYAWAN','MANAGER/WAKIL') AND status = 'A'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_customer_by_phone($customer_phone){
		$sql = "SELECT * FROM gold_customer
				WHERE cust_phone = '$customer_phone'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_customer_by_phone2($customer_phone){
		$sql = "SELECT * FROM gold_customer
				WHERE cust_phone LIKE '%$customer_phone%'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_trans_number_jual($tanggal_aktif,$no_kasir){
		$sql = "SELECT * FROM gold_main_penjualan
				WHERE id_kasir=$no_kasir AND trans_date='$tanggal_aktif'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function find_customer($id){
		$sql = "SELECT * FROM gold_customer WHERE cust_phone = '$id'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function save_customer($tlp_cust,$alamat_cust,$nama_cust){
		$sql = "INSERT INTO gold_customer(cust_phone,cust_address,cust_name) VALUES ('$tlp_cust','$alamat_cust','$nama_cust')";
		
		$this->db->query($sql);
	}
	
	public function update_customer($tlp_cust,$alamat_cust,$nama_cust){
		$sql = "UPDATE gold_customer
				SET cust_name='$nama_cust', cust_address='$alamat_cust'
				WHERE cust_phone='$tlp_cust'";
		
		$this->db->query($sql);
	}
	
	public function insert_main_jual($transactioncode,$id_kasir,$cust_service,$cust_phone,$cust_address,$cust_name,$total_price,$bayar_1,$bayar_2,$jenis_bayar_1,$jenis_bayar_2,$tanggal_aktif,$created_by){
		$sql = "INSERT INTO gold_main_penjualan(transaction_code,id_kasir,cust_service,cust_phone,cust_address,cust_name,total_price,bayar_1,bayar_2,jenis_bayar_1,jenis_bayar_2,trans_date,created_date,created_by) VALUES('$transactioncode',$id_kasir,'$cust_service','$cust_phone','$cust_address','$cust_name',$total_price,$bayar_1,$bayar_2,'$jenis_bayar_1','$jenis_bayar_2','$tanggal_aktif',CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function insert_detail_jual($transactioncode,$id_kasir,$product_id,$id_box,$id_karat,$product_name,$product_weight,$product_desc,$product_price,$tanggal_aktif,$created_by){
		$sql = "INSERT INTO gold_detail_penjualan(transaction_code,id_kasir,id_product,id_box,id_karat,nama_product,product_desc,product_weight,product_price,trans_date,created_date,created_by) VALUES('$transactioncode',$id_kasir,'$product_id',$id_box,$id_karat,'$product_name','$product_desc','$product_weight',$product_price,'$tanggal_aktif',CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function update_sell_product($transactioncode,$product_id,$tanggal_aktif){
		$sql = "UPDATE gold_product
				SET out_date = '$tanggal_aktif', id_sell = '$transactioncode', status = 'S', last_updated = CURRENT_TIMESTAMP()
				WHERE id = '$product_id'";
		
		$this->db->query($sql);
	}
	
	public function get_trans_jual($tanggal_aktif,$no_kasir){
		$sql = "SELECT * FROM gold_main_penjualan
				WHERE id_kasir=$no_kasir AND trans_date = '$tanggal_aktif' AND status = 'A'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_idtrans_jual($id_main){
		$sql = "SELECT * FROM gold_main_penjualan 
				WHERE id='$id_main'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$transactioncode = $row->transaction_code;
			return $transactioncode;
		}else{
			$transactioncode = 0;
			return $transactioncode;
		}
	}
	
	public function get_product_jual($transactioncode){
		$sql = "SELECT s.*, k.karat_name
				FROM gold_detail_penjualan s, gold_karat k
				WHERE s.id_karat = k.id AND s.transaction_code = '$transactioncode'
				ORDER BY s.id, s.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_trans_number_beli($tanggal_aktif,$no_kasir){
		$sql = "SELECT * FROM gold_main_pembelian
				WHERE id_kasir=$no_kasir AND trans_date='$tanggal_aktif'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_main_beli($transactioncode,$id_kasir,$cust_service,$cust_phone,$cust_address,$cust_name,$total_price,$tanggal_aktif,$created_by){
		$sql = "INSERT INTO gold_main_pembelian(transaction_code,id_kasir,cust_service,cust_phone,cust_address,cust_name,total_price,trans_date,created_date,created_by) VALUES('$transactioncode','$id_kasir','$cust_service','$cust_phone','$cust_address','$cust_name','$total_price','$tanggal_aktif',CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function insert_detail_beli($transactioncode,$id_kasir,$product_id,$id_karat,$product_name,$product_category,$product_weight,$product_pcs,$product_price,$tanggal_aktif,$created_by){
		$sql = "INSERT INTO gold_detail_pembelian(transaction_code,id_kasir,id_product,id_karat,nama_product,product_pcs,product_weight,id_category,product_price,trans_date,created_date,created_by) VALUES('$transactioncode','$id_kasir','$product_id','$id_karat','$product_name','$product_pcs','$product_weight',$product_category,'$product_price','$tanggal_aktif',CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function get_trans_beli($tanggal_aktif,$no_kasir){
		$sql = "SELECT * FROM gold_main_pembelian
				WHERE id_kasir='$no_kasir' AND trans_date = '$tanggal_aktif' AND status = 'A'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_idtrans_beli($id_main){
		$sql = "SELECT * FROM gold_main_pembelian 
				WHERE id='$id_main'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$transactioncode = $row->transaction_code;
			return $transactioncode;
		}else{
			$transactioncode = 0;
			return $transactioncode;
		}
	}
	
	public function get_product_beli($transactioncode){
		$sql = "SELECT s.*, k.karat_name, c.category_name
				FROM gold_detail_pembelian s, gold_karat k, gold_product_category c
				WHERE s.id_karat = k.id AND s.id_category = c.id AND s.transaction_code = '$transactioncode'
				ORDER BY s.id, s.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_penjualan_kasir($tgl_from,$tgl_to,$filter_box,$filter_karat){
		$sql = "SELECT j.*, k.karat_name
				FROM gold_detail_penjualan j, gold_karat k
				WHERE j.id_karat = k.id AND trans_date BETWEEN '$tgl_from' AND '$tgl_to' AND j.id_box IN($filter_box) AND j.id_karat IN($filter_karat) AND j.status = 'A'
				ORDER BY j.trans_date, j.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_penjualan_rekap_karat($tgl_from,$tgl_to){
		$sql = "SELECT k.karat_name, COUNT(j.id_karat) AS pcs, SUM(product_weight) AS berat, SUM(product_price) as harga
				FROM gold_detail_penjualan j, gold_karat k
				WHERE j.id_karat = k.id AND trans_date BETWEEN '$tgl_from' AND '$tgl_to' AND j.status = 'A'
				GROUP BY j.id_karat
				ORDER BY j.id_karat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_customer_name($idmutasi){
		$sql = "SELECT * FROM gold_main_penjualan
				WHERE transaction_code = '$idmutasi'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			return $row->cust_name;
		}else{
			return 'Undefined';
		}
	}
	
	public function get_customer_address($idmutasi){
		$sql = "SELECT * FROM gold_main_penjualan
				WHERE transaction_code = '$idmutasi'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			return $row->cust_address;
		}else{
			return 'Undefined';
		}
	}
	
	public function get_customer_phone($idmutasi){
		$sql = "SELECT * FROM gold_main_penjualan
				WHERE transaction_code = '$idmutasi'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			return $row->cust_phone;
		}else{
			return 'Undefined';
		}
	}
	
	public function get_customer_name2($idmutasi){
		$sql = "SELECT * FROM gold_main_pembelian
				WHERE transaction_code = '$idmutasi'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			return $row->cust_name;
		}else{
			return 'Undefined';
		}
	}
	
	public function get_customer_address2($idmutasi){
		$sql = "SELECT * FROM gold_main_pembelian
				WHERE transaction_code = '$idmutasi'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			return $row->cust_address;
		}else{
			return 'Undefined';
		}
	}
	
	public function get_customer_phone2($idmutasi){
		$sql = "SELECT * FROM gold_main_pembelian
				WHERE transaction_code = '$idmutasi'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			return $row->cust_phone;
		}else{
			return 'Undefined';
		}
	}
	
	public function get_penjualan_rekap_by_category($tgl_from,$tgl_to,$id_karat){
		$sql = "SELECT k.category_name, COUNT(p.id_category) AS pcs, SUM(j.product_weight) AS berat, SUM(j.product_price) as harga
				FROM gold_detail_penjualan j,gold_product p, gold_product_category k
				WHERE j.id_product = p.id AND p.id_category = k.id AND j.trans_date BETWEEN '$tgl_from' AND '$tgl_to' AND j.id_karat = '$id_karat' AND j.status = 'A'
				GROUP BY p.id_category
				ORDER BY p.id_category";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_pembelian_kasir($tgl_from,$tgl_to,$filter_karat){
		$sql = "SELECT j.*, k.karat_name, c.category_name
				FROM gold_detail_pembelian j, gold_karat k, gold_product_category c
				WHERE j.id_karat = k.id AND j.id_category = c.id AND trans_date BETWEEN '$tgl_from' AND '$tgl_to' AND j.id_karat IN($filter_karat) AND j.status = 'A'
				ORDER BY j.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_pembelian_rekap_karat($tgl_from,$tgl_to){
		$sql = "SELECT j.id_karat, k.karat_name, SUM(j.product_pcs) AS pcs, SUM(product_weight) AS berat, SUM(product_price) as harga
				FROM (gold_detail_pembelian j
				INNER JOIN gold_karat k on j.id_karat = k.id)
				WHERE j.trans_date BETWEEN '$tgl_from' AND '$tgl_to' AND j.status = 'A'
				GROUP BY j.id_karat
				ORDER BY j.id_karat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_pembelian_rekap_by_category($tgl_from,$tgl_to,$id_karat){
		$sql = "SELECT k.category_name, SUM(j.product_pcs) AS pcs, SUM(product_weight) AS berat, SUM(product_price) as harga
				FROM gold_detail_pembelian j, gold_product_category k
				WHERE j.id_category = k.id AND trans_date BETWEEN '$tgl_from' AND '$tgl_to' AND j.id_karat = '$id_karat' AND j.status = 'A'
				GROUP BY j.id_category
				ORDER BY j.id_category";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_beli_kirim($tgl_transaksi){
		$sql = "SELECT s.*, k.karat_name, c.category_name
				FROM gold_detail_pembelian s, gold_karat k, gold_product_category c
				WHERE (s.id_karat = k.id AND s.id_category = c.id AND s.status = 'A' AND s.kirim_date = '0000-00-00 00:00:00' AND s.trans_date <= '$tgl_transaksi' AND s.status = 'A') OR (s.id_karat = k.id AND s.id_category = c.id AND s.status = 'A' AND s.kirim_date = '$tgl_transaksi' AND s.trans_date <= '$tgl_transaksi' AND s.status = 'A')
				ORDER BY s.trans_date, s.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_detail_beli_by_id($id){
		$sql = "SELECT * FROM gold_detail_pembelian
				WHERE id = '$id'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_reparasi_to_persen($karat_beli){
		$sql = "SELECT * FROM gold_karat
				WHERE id = '$karat_beli'";
	
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 
			return $row->to_reparasi;
		}else{
			return 0;
		}
	}
	
	public function input_kirim_pembelian($id,$tujuan,$tgl_transaksi,$persen_reparasi,$dua_empat,$created_by){
		$sql = "UPDATE gold_detail_pembelian
				SET kirim_date = '$tgl_transaksi', tujuan = '$tujuan', persentase = '$persen_reparasi', weight_duaempat = '$dua_empat', created_kirim_date = CURRENT_TIMESTAMP(), last_update_kirim = CURRENT_TIMESTAMP(), kirim_by = '$created_by', update_kirim_by = '$created_by'
				WHERE id = '$id'";
		
		$this->db->query($sql);
	}
	
	public function get_kirim_sdr($tgl_transaksi){
		$sql = "SELECT *
				FROM gold_mutasi_reparasi
				WHERE from_buy = 'Y' AND trans_date = '$tgl_transaksi'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function input_main_reparasi($transactioncode,$from_buy,$fromaccount,$toaccount,$tipe,$desc,$dua_empat_real,$dua_empat_konv,$semsanam_real,$semsanam_konv,$juhlima_real,$juhlima_konv,$juhtus_real,$juhtus_konv,$total_konv_sdr,$tgl_transaksi,$created_by){
		$sql = "INSERT INTO gold_mutasi_reparasi(id_pengiriman,from_buy,fromaccount,toaccount,tipe,description,dua_empat,dua_empat_konv,semsanam,semsanam_konv,juhlima,juhlima_konv,juhtus,juhtus_konv,total_konv,trans_date,created_date,created_by,last_updated,last_updated_by) VALUES('$transactioncode','$from_buy','$fromaccount','$toaccount','$tipe','$desc','$dua_empat_real','$dua_empat_konv','$semsanam_real','$semsanam_konv','$juhlima_real','$juhlima_konv','$juhtus_real','$juhtus_konv','$total_konv_sdr','$tgl_transaksi',CURRENT_TIMESTAMP(),'$created_by',CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function input_main_reparasi_hapus($transactioncode,$from_buy,$fromaccount,$toaccount,$tipe,$desc,$dua_empat_real,$dua_empat_konv,$semsanam_real,$semsanam_konv,$juhlima_real,$juhlima_konv,$juhtus_real,$juhtus_konv,$total_konv_sdr,$tgl_transaksi,$created_by){
		$sql = "INSERT INTO gold_mutasi_reparasi_hapus(id_pengiriman,from_buy,fromaccount,toaccount,tipe,description,dua_empat,dua_empat_konv,semsanam,semsanam_konv,juhlima,juhlima_konv,juhtus,juhtus_konv,total_konv,trans_date,deleted_date,deleted_by) VALUES('$transactioncode','$from_buy','$fromaccount','$toaccount','$tipe','$desc','$dua_empat_real','$dua_empat_konv','$semsanam_real','$semsanam_konv','$juhlima_real','$juhlima_konv','$juhtus_real','$juhtus_konv','$total_konv_sdr','$tgl_transaksi',CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function hapus_main_reparasi($transactioncode){
		$sql = "DELETE FROM gold_mutasi_reparasi
				WHERE id_pengiriman = '$transactioncode'";
		
		$this->db->query($sql);
	}
	
	public function update_main_reparasi($dua_empat_real,$dua_empat_konv,$semsanam_real,$semsanam_konv,$juhlima_real,$juhlima_konv,$juhtus_real,$juhtus_konv,$total_konv_sdr,$tgl_transaksi,$created_by){
		$sql = "UPDATE gold_mutasi_reparasi
				SET dua_empat = '$dua_empat_real',dua_empat_konv = '$dua_empat_konv',semsanam = '$semsanam_real',semsanam_konv = '$semsanam_konv', juhlima = '$juhlima_real',juhlima_konv = '$juhlima_konv',juhtus = '$juhtus_real',juhtus_konv = '$juhtus_konv', total_konv = '$total_konv_sdr', last_updated = CURRENT_TIMESTAMP(), last_updated_by = '$created_by'
				WHERE trans_date = '$tgl_transaksi' AND from_buy = 'Y'";
		
		$this->db->query($sql);
	}
	
	public function get_kirim_sdg($tgl_transaksi){
		$sql = "SELECT *
				FROM gold_mutasi_pengadaan
				WHERE from_buy = 'Y' AND trans_date = '$tgl_transaksi'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function input_main_pengadaan($transactioncode,$from_buy,$fromaccount,$toaccount,$tipe,$desc,$dua_empat_sdg,$semsanam_sdg,$juhlima_sdg,$juhtus_sdg,$total_konv,$tgl_transaksi,$created_by){
		$sql = "INSERT INTO gold_mutasi_pengadaan(id_pengiriman,from_buy,fromaccount,toaccount,tipe,description,dua_empat,semsanam,juhlima,juhtus,total_konv,trans_date,created_date,created_by,last_updated,last_updated_by) VALUES('$transactioncode','$from_buy','$fromaccount','$toaccount','$tipe','$desc','$dua_empat_sdg','$semsanam_sdg','$juhlima_sdg','$juhtus_sdg','$total_konv','$tgl_transaksi',CURRENT_TIMESTAMP(),'$created_by',CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function input_main_titip_pengadaan($transactioncode,$from_buy,$fromaccount,$toaccount,$tipe,$desc,$dua_empat_sdg,$semsanam_sdg,$juhlima_sdg,$juhtus_sdg,$total_konv,$tgl_transaksi,$created_by){
		$sql = "INSERT INTO gold_mutasi_titip_pengadaan(id_pengiriman,from_buy,fromaccount,toaccount,tipe,description,dua_empat,semsanam,juhlima,juhtus,total_konv,trans_date,created_date,created_by,last_updated,last_updated_by) VALUES('$transactioncode','$from_buy','$fromaccount','$toaccount','$tipe','$desc','$dua_empat_sdg','$semsanam_sdg','$juhlima_sdg','$juhtus_sdg','$total_konv','$tgl_transaksi',CURRENT_TIMESTAMP(),'$created_by',CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function input_main_pengadaan_hapus($transactioncode,$from_buy,$fromaccount,$toaccount,$tipe,$desc,$dua_empat_sdg,$semsanam_sdg,$juhlima_sdg,$juhtus_sdg,$total_konv,$tgl_transaksi,$created_by){
		$sql = "INSERT INTO gold_mutasi_pengadaan_hapus(id_pengiriman,from_buy,fromaccount,toaccount,tipe,description,dua_empat,semsanam,juhlima,juhtus,total_konv,trans_date,deleted_date,deleted_by) VALUES('$transactioncode','$from_buy','$fromaccount','$toaccount','$tipe','$desc','$dua_empat_sdg','$semsanam_sdg','$juhlima_sdg','$juhtus_sdg','$total_konv','$tgl_transaksi',CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function input_main_titip_pengadaan_hapus($transactioncode,$from_buy,$fromaccount,$toaccount,$tipe,$desc,$dua_empat_sdg,$semsanam_sdg,$juhlima_sdg,$juhtus_sdg,$total_konv,$tgl_transaksi,$created_by){
		$sql = "INSERT INTO gold_mutasi_titip_pengadaan_hapus(id_pengiriman,from_buy,fromaccount,toaccount,tipe,description,dua_empat,semsanam,juhlima,juhtus,total_konv,trans_date,deleted_date,deleted_by) VALUES('$transactioncode','$from_buy','$fromaccount','$toaccount','$tipe','$desc','$dua_empat_sdg','$semsanam_sdg','$juhlima_sdg','$juhtus_sdg','$total_konv','$tgl_transaksi',CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function hapus_main_pengadaan($transactioncode){
		$sql = "DELETE FROM gold_mutasi_pengadaan
				WHERE id_pengiriman = '$transactioncode'";
		
		$this->db->query($sql);
	}
	
	public function hapus_main_titip_pengadaan($transactioncode){
		$sql = "DELETE FROM gold_mutasi_titip_pengadaan
				WHERE id_pengiriman = '$transactioncode'";
		
		$this->db->query($sql);
	}
	
	public function update_main_pengadaan($dua_empat_sdg,$semsanam_sdg,$juhlima_sdg,$juhtus_sdg,$tgl_transaksi,$created_by){
		$sql = "UPDATE gold_mutasi_pengadaan
				SET dua_empat = '$dua_empat_sdg',semsanam = '$semsanam_sdg', juhlima = '$juhlima_sdg',juhtus = '$juhtus_sdg', last_updated = CURRENT_TIMESTAMP(), last_updated_by = '$created_by'
				WHERE trans_date = '$tgl_transaksi' AND from_buy = 'Y'";
		
		$this->db->query($sql);
	}
	
	public function get_filter_kirim_beli($from_date,$to_date,$filter_category,$filter_to,$filter_karat){
		$sql = "SELECT s.*, k.karat_name, c.category_name
				FROM gold_detail_pembelian s, gold_karat k, gold_product_category c
				WHERE s.id_karat = k.id AND s.id_category = c.id AND s.id_category IN ($filter_category) AND s.tujuan IN($filter_to) AND s.id_karat IN($filter_karat) AND s.kirim_date BETWEEN '$from_date' AND '$to_date' AND s.status = 'A'
				ORDER BY s.kirim_date, s.created_date";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_customer_pesanan(){
		$sql = "SELECT * FROM gold_customer_pesanan";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_customer_pesanan_by_phone($phone){
		$sql = "SELECT * FROM gold_customer_pesanan
				WHERE cust_phone = '$phone'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function save_customer_pesanan($tlp_cust,$alamat_cust,$nama_cust){
		$sql = "INSERT INTO gold_customer_pesanan(cust_phone,cust_address,cust_name) VALUES ('$tlp_cust','$alamat_cust','$nama_cust')";
		
		$this->db->query($sql);
	}
	
	public function input_main_pesanan($id_pesanan,$customer_name,$customer_address,$customer_phone,$ump_val,$tgl_transaksi,$created_by){
		$sql = "INSERT INTO gold_main_pesanan(id_pesanan,cust_name,cust_address,cust_phone,ump_val,trans_date,created_date,created_by,status,last_updated) VALUES('$id_pesanan','$customer_name','$customer_address','$customer_phone','$ump_val','$tgl_transaksi',CURRENT_TIMESTAMP(),'$created_by','P',CURRENT_TIMESTAMP())";
		
		$this->db->query($sql);
	}
	
	public function input_main_pesanan_bbcu($id_pesanan,$customer_name,$customer_address,$customer_phone,$ump_val,$tgl_transaksi,$created_by){
		$sql = "INSERT INTO gold_main_pesanan_bbcu(id_pesanan,cust_name,cust_address,cust_phone,ump_val,trans_date,created_date,created_by,status,last_updated) VALUES('$id_pesanan','$customer_name','$customer_address','$customer_phone','$ump_val','$tgl_transaksi',CURRENT_TIMESTAMP(),'$created_by','P',CURRENT_TIMESTAMP())";
		
		$this->db->query($sql);
	}
	
	public function input_detail_pesanan($id_pesanan,$id_karat,$id_category,$nama_barang,$tgl_transaksi,$created_by){
		$sql = "INSERT INTO gold_detail_pesanan(id_pesanan,id_karat,id_category,nama_pesanan,trans_date,created_date,created_by,status,last_updated) VALUES('$id_pesanan','$id_karat','$id_category','$nama_barang','$tgl_transaksi',CURRENT_TIMESTAMP(),'$created_by','P',CURRENT_TIMESTAMP())";
		
		$this->db->query($sql);
	}
	
	public function get_pesanan_by_id($id_pesanan){
		$sql = "SELECT * FROM gold_main_pesanan
				WHERE id_pesanan = '$id_pesanan'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_pesanan_by_id_bbcu($id_pesanan){
		$sql = "SELECT * FROM gold_main_pesanan_bbcu
				WHERE id_pesanan = '$id_pesanan'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_filter_pesanan($from_date,$to_date,$filter_status){
		$sql = "SELECT *FROM gold_main_pesanan
		WHERE trans_date BETWEEN '$from_date' and '$to_date' AND status IN($filter_status)
		ORDER BY trans_date DESC, created_date DESC";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_filter_pesanan_bbcu($from_date,$to_date,$filter_status){
		$sql = "SELECT *FROM gold_main_pesanan_bbcu
		WHERE trans_date BETWEEN '$from_date' and '$to_date' AND status IN($filter_status)
		ORDER BY trans_date DESC, created_date DESC";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function update_ump_pesanan_bbcu($id_pesanan,$ump_main){
		$sql = "UPDATE gold_main_pesanan_bbcu
				SET ump_val = '$ump_main', last_updated = CURRENT_TIMESTAMP()
				WHERE id_pesanan = '$id_pesanan'";
		
		$this->db->query($sql);
	}
	
	public function get_pesanan_per($tgl_transaksi){
		$sql = "SELECT * FROM gold_main_pesanan WHERE trans_date <= '$tgl_transaksi' AND (ambil_date = '0000-00-00 00:00:00' OR ambil_date > '$tgl_transaksi') AND (updated_date = '0000-00-00 00:00:00' OR updated_date > '$tgl_transaksi')";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_filter_pesanan_box(){
		$sql = "SELECT *FROM gold_main_pesanan
		WHERE status = 'P'
		ORDER BY trans_date DESC, created_date DESC";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_filter_pesanan_ambil(){
		$sql = "SELECT *FROM gold_main_pesanan
		WHERE status = 'B'
		ORDER BY trans_date DESC, created_date DESC";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_filter_pesanan_ambil_bbcu(){
		$sql = "SELECT *FROM gold_main_pesanan_bbcu
		WHERE status = 'P'
		ORDER BY trans_date DESC, created_date DESC";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_detail_pesanan_by_id($id_pesanan){
		$sql = "SELECT m.*, k.karat_name,  c.category_name
				FROM gold_detail_pesanan m, gold_karat k, gold_product_category c
				WHERE m.id_karat = k.id AND m.id_category = c.id AND id_pesanan = '$id_pesanan'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function update_box_detail_pesanan($id_detail,$product_id,$berat_barang,$status,$tanggal_action,$created_by){
		$sql = "UPDATE gold_detail_pesanan
				SET id_product = '$product_id', product_weight = '$berat_barang',status = '$status', box_date = '$tanggal_action',box_by = '$created_by', box_created_date = CURRENT_TIMESTAMP(), last_updated = CURRENT_TIMESTAMP()
				WHERE id = '$id_detail'";
		
		$this->db->query($sql);
	}
	
	public function update_box_main_pesanan($id_pesanan,$status,$grosir_use,$tanggal_action,$created_by){
		$sql = "UPDATE gold_main_pesanan
				SET status = '$status', grosir_use = '$grosir_use', box_date = '$tanggal_action',box_by = '$created_by', box_created_date = CURRENT_TIMESTAMP(), last_updated = CURRENT_TIMESTAMP()
				WHERE id_pesanan = '$id_pesanan'";
		
		$this->db->query($sql);
	}
	
	public function update_ambil_detail_pesanan($id_detail,$status,$product_price,$tanggal_action,$created_by){
		$sql = "UPDATE gold_detail_pesanan
				SET harga_jual = '$product_price',status = '$status', ambil_date = '$tanggal_action',ambil_by = '$created_by', ambil_created_date = CURRENT_TIMESTAMP(), last_updated = CURRENT_TIMESTAMP()
				WHERE id = '$id_detail'";
		
		$this->db->query($sql);
	}
	
	public function update_ambil_main_pesanan($id_pesanan,$status,$grosir_use,$total_price,$tanggal_action,$created_by){
		$sql = "UPDATE gold_main_pesanan
				SET status = '$status',grosir_use = '$grosir_use', ambil_date = '$tanggal_action',ambil_by = '$created_by', ambil_created_date = CURRENT_TIMESTAMP(), total_trans = '$total_price', last_updated = CURRENT_TIMESTAMP()
				WHERE id_pesanan = '$id_pesanan'";
		
		$this->db->query($sql);
	}
	
	public function update_ambil_main_pesanan_bbcu($id_pesanan,$status,$grosir_use,$total_price,$tanggal_action,$created_by){
		$sql = "UPDATE gold_main_pesanan_bbcu
				SET status = '$status',grosir_use = '$grosir_use', ambil_date = '$tanggal_action',ambil_by = '$created_by', ambil_created_date = CURRENT_TIMESTAMP(), total_trans = '$total_price', last_updated = CURRENT_TIMESTAMP()
				WHERE id_pesanan = '$id_pesanan'";
		
		$this->db->query($sql);
	}
	
	public function get_rekap_penjualan($tgl_transaksi){
		$sql = "SELECT COUNT(s.id_karat) as pcs , SUM(product_weight) as berat , SUM(product_price) as total, k.karat_name, s.id_karat
				FROM (gold_detail_penjualan s
				INNER JOIN gold_karat k on s.id_karat = k.id)
				WHERE s.trans_date = '$tgl_transaksi' AND s.status = 'A'
				GROUP BY s.id_karat
				ORDER BY s.id_karat";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_rekap_pembelian($tgl_transaksi){
		$sql = "SELECT COUNT(s.id_karat) as pcs , SUM(product_weight) as berat , SUM(product_price) as total, k.karat_name, s.id_karat
				FROM gold_detail_pembelian s, gold_karat k
				WHERE s.id_karat = k.id AND s.trans_date = '$tgl_transaksi' AND s.status = 'A'
				GROUP BY s.id_karat
				ORDER BY s.id_karat";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function hapus_main_detail_jual($id){
		$sql = "UPDATE gold_main_penjualan
				SET status = 'X'
				WHERE transaction_code = '$id'";
		
		$this->db->query($sql);
		
		$sql2 = "UPDATE gold_detail_penjualan
				SET status = 'X'
				WHERE transaction_code = '$id'";
		
		$this->db->query($sql2);
	}
	
	public function reset_product_jual($id_product){
		$sql = "UPDATE gold_product
				SET status = 'A', id_sell = '', out_date = '0000-00-00 00:00:00', last_updated = CURRENT_TIMESTAMP()
				WHERE id = '$id_product'";
		
		$this->db->query($sql);
	}
	
	public function hapus_main_detail_beli($id){
		$sql = "UPDATE gold_main_pembelian
				SET status = 'X'
				WHERE transaction_code = '$id'";
		
		$this->db->query($sql);
		
		$sql2 = "UPDATE gold_detail_pembelian
				SET status = 'X'
				WHERE transaction_code = '$id'";
		
		$this->db->query($sql2);
	}
	
	public function get_mutasi_jual_bank($tgl_trans){
		$sql = "SELECT SUM(bayar_2) AS total FROM gold_main_penjualan
				WHERE status = 'A' AND trans_date = '$tgl_trans'";

		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 
			return $row->total;;
		}else{
			return 0;
		}
	}
	
	public function get_mutasi_jual_bank_detail($tgl_trans){
		$sql = "SELECT j.bayar_2,c.accountname
				FROM gold_main_penjualan j, gold_coa_rp c
				WHERE j.jenis_bayar_2 = c.accountnumber AND j.status = 'A' AND j.bayar_2 <> 0 AND j.trans_date = '$tgl_trans'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_jual_tunai_detail($tgl_trans){
		$sql = "SELECT SUM(bayar_1) as value
				FROM gold_main_penjualan
				WHERE status = 'A' AND bayar_1 <> 0 AND trans_date = '$tgl_trans'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_kas_keluar($tgl_trans){
		$sql = "SELECT SUM(value) AS total FROM gold_mutasi_rp
				WHERE transdate = '$tgl_trans' AND fromaccount = '11-0001' AND (idmutasi LIKE 'KI%' OR idmutasi LIKE 'KO%' OR idmutasi LIKE 'UI%' OR idmutasi LIKE 'UO%' OR idmutasi LIKE 'PI%' OR idmutasi LIKE 'PO%' OR idmutasi LIKE 'RI%' OR idmutasi LIKE 'RO%' OR idmutasi LIKE 'PX%')";

		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 
			return $row->total;;
		}else{
			return 0;
		}
	}
	
	public function get_mutasi_kas_masuk($tgl_trans){
		$sql = "SELECT SUM(value) AS total FROM gold_mutasi_rp
				WHERE transdate = '$tgl_trans' AND toaccount = '11-0001' AND (idmutasi LIKE 'KI%' OR idmutasi LIKE 'KO%' OR idmutasi LIKE 'UI%' OR idmutasi LIKE 'UO%' OR idmutasi LIKE 'PI%' OR idmutasi LIKE 'PO%' OR idmutasi LIKE 'RI%' OR idmutasi LIKE 'RO%')";

		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 
			return $row->total;;
		}else{
			return 0;
		}
	}
	
	public function get_mutasi_kas_masuk_keluar_detail($tgl_trans){
		$sql = "SELECT m.fromaccount, m.toaccount, m.description, m.value FROM gold_mutasi_rp m
				WHERE transdate = '$tgl_trans' AND (fromaccount = '11-0001' OR toaccount = '11-0001') AND (idmutasi LIKE 'KI%' OR idmutasi LIKE 'KO%' OR idmutasi LIKE 'UI%' OR idmutasi LIKE 'UO%' OR idmutasi LIKE 'PI%' OR idmutasi LIKE 'PO%' OR idmutasi LIKE 'RI%' OR idmutasi LIKE 'RO%' OR idmutasi LIKE 'PX%' OR idmutasi LIKE 'JU%')";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_coa_number_name2($id_coa){
		$sql = "SELECT * FROM gold_coa_rp 
				WHERE accountnumber = '$id_coa'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$account = $row->accountname;
			return $account;
		}else{
			$account = 'NOT FOUND';
			return $account;
		}
	}
	
	public function get_main_pembelian($id){
		$sql = "SELECT * FROM gold_main_pembelian
				WHERE id = '$id'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_main_penjualan($id){
		$sql = "SELECT * FROM gold_main_penjualan
				WHERE id = '$id'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_main_penjualan_2($id){
		$sql = "SELECT * FROM gold_main_penjualan
				WHERE transaction_code = '$id'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_all_customer(){
		$sql = "SELECT * FROM gold_customer";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_customer_filter($keyword,$column){
		$sql = "SELECT * FROM gold_customer WHERE ".$column." LIKE '%".$keyword."%'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_main_penjualan_by_phone($phone){
		$sql = "SELECT * FROM gold_main_penjualan
				WHERE cust_phone = '$phone'
				ORDER BY trans_date DESC";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_main_pembelian_by_phone($phone){
		$sql = "SELECT * FROM gold_main_pembelian
				WHERE cust_phone = '$phone'
				ORDER BY trans_date DESC";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_chart_sell($id_karat,$id_category,$from_date,$to_date){
		$sql = "SELECT COUNT(j.id_karat) as total
				FROM gold_detail_penjualan j, gold_product p
				WHERE j.id_product = p.id AND j.id_karat = '$id_karat' AND p.id_category = '$id_category' AND trans_date BETWEEN '$from_date' AND '$to_date' 
				GROUP BY j.id_karat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_chart_sell_idr($id_karat,$id_category,$from_date,$to_date){
		$sql = "SELECT SUM(j.product_weight) as total
				FROM gold_detail_penjualan j, gold_product p
				WHERE j.id_product = p.id AND j.id_karat = '$id_karat' AND p.id_category = '$id_category' AND trans_date BETWEEN '$from_date' AND '$to_date' 
				GROUP BY j.id_karat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function update_batal_pesanan($id_pesanan,$tgl_transaksi,$created_by){
		$sql = "UPDATE gold_main_pesanan
				SET status = 'X', updated_date = '$tgl_transaksi', updated_by = '$created_by', last_updated = CURRENT_TIMESTAMP()
				WHERE id_pesanan = '$id_pesanan'";
		
		$this->db->query($sql);
		
		$sql2 = "UPDATE gold_detail_pesanan
				SET status = 'X', updated_date = '$tgl_transaksi', updated_by = '$created_by', last_updated = CURRENT_TIMESTAMP()
				WHERE id_pesanan = '$id_pesanan'";
		
		$this->db->query($sql2);
	}
	
	public function update_stepback_pesanan_box($id_pesanan,$status){
		$sql = "UPDATE gold_main_pesanan
				SET status = '$status', box_date = '000-00-00 00:00:00', box_created_date = '000-00-00 00:00:00', box_by = '', last_updated = CURRENT_TIMESTAMP()
				WHERE id_pesanan = '$id_pesanan'";
		
		$this->db->query($sql);
		
		$sql2 = "UPDATE gold_detail_pesanan
				SET status = '$status', box_date = '000-00-00 00:00:00', box_created_date = '000-00-00 00:00:00', box_by = '', product_weight = '0', id_product = '', last_updated = CURRENT_TIMESTAMP()
				WHERE id_pesanan = '$id_pesanan'";
		
		$this->db->query($sql2);
	}
	
	public function update_stepback_pesanan_ambil($id_pesanan,$status){
		$sql = "UPDATE gold_main_pesanan
				SET status = '$status', ambil_date = '000-00-00 00:00:00', ambil_created_date = '000-00-00 00:00:00', ambil_by = '', last_updated = CURRENT_TIMESTAMP()
				WHERE id_pesanan = '$id_pesanan'";
		
		$this->db->query($sql);
		
		$sql2 = "UPDATE gold_detail_pesanan
				SET status = '$status', ambil_date = '000-00-00 00:00:00', ambil_created_date = '000-00-00 00:00:00', ambil_by = '', harga_jual = '0', last_updated = CURRENT_TIMESTAMP()
				WHERE id_pesanan = '$id_pesanan'";
		
		$this->db->query($sql2);
	}
	
	public function get_id_penjualan_product($id_product){
		$sql = "SELECT transaction_code FROM gold_detail_penjualan 
				WHERE id_product = '$id_product' AND status != 'X'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$id = $row->transaction_code;
			return $id;
		}else{
			$id = 'NOT FOUND';
			return $id;
		}
	}
	
	public function update_process_detail_pesanan($id,$id_karat,$id_category,$nama_barang,$created_by){
		$sql = "UPDATE gold_detail_pesanan
				SET id_karat = '$id_karat', id_category = '$id_category', nama_pesanan = '$nama_barang', updated_by = '$created_by', updated_date = CURRENT_TIMESTAMP()
				WHERE id = '$id'";
		
		$this->db->query($sql);
	}
	
	public function update_edit_box_detail_pesanan($id,$id_product,$id_karat,$id_category,$nama_barang,$berat_barang,$created_by){
		$sql = "UPDATE gold_detail_pesanan
				SET id_karat = '$id_karat', id_category = '$id_category', nama_pesanan = '$nama_barang', product_weight = '$berat_barang', updated_by = '$created_by', updated_date = CURRENT_TIMESTAMP()
				WHERE id = '$id'";
		
		$this->db->query($sql);
	}
	
	public function update_stock_in_box_detail_pesanan($id_product,$id_karat,$id_category,$nama_barang,$berat_barang){
		$id_stock_in = 'PI-'.$id_product;
		$sql = "UPDATE gold_stock_in
				SET id_karat = '$id_karat', id_category = '$id_category', product_name = '$nama_barang', product_weight = '$berat_barang'
				WHERE id = '$id_stock_in'";
		
		$this->db->query($sql);
		
		$sql2 = "UPDATE gold_mutasi_gr
				SET idkarat = '$id_karat', value = '$berat_barang'
				WHERE idmutasi = '$id_stock_in'";
		
		$this->db->query($sql2);
	}
	
	public function update_edit_harga_detail_pesanan($id,$harga_barang,$created_by){
		$sql = "UPDATE gold_detail_pesanan
				SET harga_jual = '$harga_barang', updated_by = '$created_by', updated_date = CURRENT_TIMESTAMP()
				WHERE id = '$id'";
		
		$this->db->query($sql);
	}
	
	public function update_edit_harga_detail_penjualan($id_product,$harga_barang){
		$sql = "UPDATE gold_detail_penjualan
				SET product_price = '$harga_barang'
				WHERE id_product = '$id_product'";
		
		$this->db->query($sql);
	}
	
	public function update_harga_main_pesanan($id_pesanan,$total_trans){
		$sql = "UPDATE gold_main_pesanan
				SET total_trans = '$total_trans'
				WHERE id_pesanan = '$id_pesanan'";
		
		$this->db->query($sql);
	}
	
	public function update_harga_main_penjualan($id_penjualan,$total_trans){
		$sql = "UPDATE gold_main_penjualan
				SET total_price = '$total_trans', bayar_1 = '$total_trans'
				WHERE transaction_code = '$id_penjualan'";
		
		$this->db->query($sql);
		
		$sql2 = "UPDATE gold_mutasi_rp
				SET value = '$total_trans'
				WHERE idmutasi LIKE '$id_penjualan%'";
		
		$this->db->query($sql2);
	}
	
	public function get_cs_jual_by_id($id_trans){
		$sql = "SELECT cust_service FROM gold_main_penjualan 
				WHERE transaction_code = '$id_trans'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$id = $row->cust_service;
			return $id;
		}else{
			$id = 'NOT FOUND';
			return $id;
		}
	}
	
	public function get_cs_beli_by_id($id_trans){
		$sql = "SELECT cust_service FROM gold_main_pembelian 
				WHERE transaction_code = '$id_trans'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$id = $row->cust_service;
			return $id;
		}else{
			$id = 'NOT FOUND';
			return $id;
		}
	}
	
	public function get_rekap_karat_excel($id_karat,$from_date,$to_date){
		$sql = "SELECT trans_date, id_karat, SUM(product_weight) AS berat, SUM(product_price) AS harga
				FROM `gold_detail_penjualan` WHERE status = 'A' AND trans_date BETWEEN '$from_date' AND '$to_date' AND id_karat = '$id_karat'
				GROUP BY id_karat,trans_date
				ORDER BY trans_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_rekap_karat_excel_beli($id_karat,$from_date,$to_date){
		$sql = "SELECT trans_date, id_karat, SUM(product_weight) AS berat, SUM(product_price) AS harga
				FROM `gold_detail_pembelian` WHERE status = 'A' AND trans_date BETWEEN '$from_date' AND '$to_date' AND id_karat = '$id_karat'
				GROUP BY id_karat,trans_date
				ORDER BY trans_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_do_range($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_dailyopen
				WHERE do_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}

	public function get_pr_range(){
		$sql = "SELECT * FROM gold_do_formula";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
}