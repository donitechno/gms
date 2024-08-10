<?php
include($_SERVER['DOCUMENT_ROOT']."/gms/application/libraries/db_backup_library.php");
defined('BASEPATH') OR exit('No direct script access allowed');

class C_sync extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->model('M_sync','ms');
		
		$GLOBALS['kasir'] = 1;
	}
	
	public function index(){
		$dbBackup = new db_backup;
		$dbBackup->connect("localhost","root","","gold");
		$dbBackup->backup();
		//$dbBackup->download();
		$tanggal = date("Ymd");
		$dbBackup->save("D://",$tanggal);
		
		$tgl_to = date("Y-m-d").' 23:59:59';
		$tgl_to2 = date("Y-m-d");
		$tgl_from = date('Y-m-d',strtotime($tgl_to2. "-4 days")).' 00:00:00';
		$dbsites = $this->ms->get_dbsite();
		
		$dbsite = $dbsites[0]->dbname;
		
		$data_non_tunai = $this->ms->get_all_bayar_nontunai();
		foreach($data_non_tunai as $d){
			$this->ms->insert_bayar_nontunai($dbsite,$d->id,$d->description,$d->account_number,$d->status,$d->created_date,$d->created_by);
		}
		
		$data_box  = $this->ms->get_all_box();
		foreach($data_box as $d){
			$this->ms->insert_box($dbsite,$d->id,$d->nama_box,$d->pesanan,$d->status);
		}
		
		$data_coa_gr = $this->ms->get_all_coa_gr();
		foreach($data_coa_gr as $d){
			$this->ms->insert_coa_gr($dbsite,$d->accountnumber,$d->accountnumberint,$d->accountname,$d->accountgroup,$d->beginningbalance,$d->status,$d->type,$d->idkarat,$d->created_date,$d->created_by);
		}
		
		$data_coa_rp = $this->ms->get_all_coa_rp();
		foreach($data_coa_rp as $d){
			$this->ms->insert_coa_rp($dbsite,$d->accountnumber,$d->accountnumberint,$d->accountname,$d->accountgroup,$d->beginningbalance,$d->status,$d->type,$d->created_date,$d->created_by);
		}
		
		$data_karyawan = $this->ms->get_all_karyawan();
		foreach($data_karyawan as $d){
			$this->ms->insert_karyawan($dbsite,$d->username,$d->nama_karyawan,$d->kelompok,$d->accountnumber,$d->status,$d->created_date,$d->created_by);
		}
		
		$data_kasir = $this->ms->get_all_kasir();
		foreach($data_kasir as $d){
			$this->ms->insert_kasir($dbsite,$d->id,$d->computer_name,$d->printer_name);
		}
		
		$data_tgl_aktif = $this->ms->get_tanggal_aktif();
		foreach($data_tgl_aktif as $d){
			$this->ms->insert_tanggal_aktif($dbsite,$d->id,$d->id_kasir,$d->tanggal_aktif);
		}
		
		$data_titipan_gr = $this->ms->get_all_titipan_gr();
		foreach($data_titipan_gr as $d){
			$this->ms->insert_titipan_gr($dbsite,$d->id,$d->nama_pelanggan,$d->created_date,$d->created_by);
		}
		
		$data_titipan_rp = $this->ms->get_all_titipan_rp();
		foreach($data_titipan_rp as $d){
			$this->ms->insert_titipan_rp($dbsite,$d->id,$d->nama_pelanggan,$d->created_date,$d->created_by);
		}
		
		$data_user = $this->ms->get_all_user();
		foreach($data_user as $d){
			$this->ms->insert_user($dbsite,$d->id,$d->username,$d->nama_user,$d->password_user,$d->priv_kasir,$d->priv_pembukuan,$d->priv_manager,$d->priv_admin,$d->salt,$d->picture,$d->status);
		}
		
		$data_do = $this->ms->get_dailyopen($tgl_from,$tgl_to);
		foreach($data_do as $d){
			$this->ms->insert_dailyopen($dbsite,$d->id,$d->do_date,$d->harga_emas,$d->created_date,$d->last_updated,$d->created_by);
		}
		
		$data_product = $this->ms->get_data_product($tgl_from,$tgl_to);
		foreach($data_product as $d){
			$this->ms->insert_product($dbsite,$d->id,$d->id_lama,$d->id_karat,$d->id_box,$d->id_category,$d->id_from,$d->product_from_desc,$d->product_name,$d->product_weight,$d->in_date,$d->out_date,$d->id_sell,$d->sell_desc,$d->status,$d->created_date,$d->created_by,$d->lock_status,$d->unlock_date,$d->unlock_by,$d->unlock_reason,$d->last_updated);
		}
		
		$data_main_beli = $this->ms->get_main_pembelian($tgl_from,$tgl_to);
		foreach($data_main_beli as $d){
			$this->ms->insert_main_pembelian($dbsite,$d->id,$d->id_kasir,$d->transaction_code,$d->cust_service,$d->cust_phone,$d->cust_address,$d->cust_name,$d->total_price,$d->trans_date,$d->created_date,$d->created_by,$d->status);
			
			$this->ms->insert_customer($dbsite,$d->cust_phone,$d->cust_address,$d->cust_name);
		}
		
		$data_detail_beli = $this->ms->get_detail_pembelian($tgl_from,$tgl_to);
		foreach($data_detail_beli as $d){
			$this->ms->insert_detail_pembelian($dbsite,$d->id,$d->transaction_code,$d->id_kasir,$d->id_product,$d->id_karat,$d->id_category,$d->nama_product,$d->product_pcs,$d->product_weight,$d->product_price,$d->trans_date,$d->created_date,$d->created_by,$d->status,$d->persentase,$d->weight_duaempat,$d->tujuan,$d->kirim_date,$d->created_kirim_date,$d->kirim_by,$d->last_update_kirim,$d->update_kirim_by);
		}
		
		$data_main_jual = $this->ms->get_main_penjualan($tgl_from,$tgl_to);
		foreach($data_main_jual as $d){
			$this->ms->insert_main_penjualan($dbsite,$d->id,$d->id_kasir,$d->transaction_code,$d->cust_service,$d->cust_phone,$d->cust_address,$d->cust_name,$d->total_price,$d->bayar_1,$d->bayar_2,$d->jenis_bayar_1,$d->jenis_bayar_2,$d->trans_date,$d->created_date,$d->created_by,$d->status);
			
			$this->ms->insert_customer($dbsite,$d->cust_phone,$d->cust_address,$d->cust_name);
		}
		
		$data_detail_jual = $this->ms->get_detail_penjualan($tgl_from,$tgl_to);
		foreach($data_detail_jual as $d){
			$this->ms->insert_detail_penjualan($dbsite,$d->id,$d->transaction_code,$d->id_kasir,$d->id_product,$d->id_box,$d->id_karat,$d->nama_product,$d->product_desc,$d->product_weight,$d->product_price,$d->trans_date,$d->created_date,$d->created_by,$d->status);
		}
		
		$data_main_pesanan = $this->ms->get_main_pesanan($tgl_from,$tgl_to);
		foreach($data_main_pesanan as $d){
			$this->ms->insert_main_pesanan($dbsite,$d->id_pesanan,$d->cust_name,$d->cust_address,$d->cust_phone,$d->ump_val,$d->total_trans,$d->trans_date,$d->created_date,$d->created_by,$d->box_date,$d->box_by,$d->box_created_date,$d->ambil_date,$d->ambil_by,$d->ambil_created_date,$d->updated_date,$d->updated_by,$d->last_updated,$d->grosir_use,$d->status);
		}
		
		$data_detail_pesanan = $this->ms->get_detail_pesanan($tgl_from,$tgl_to);
		foreach($data_detail_pesanan as $d){
			$this->ms->insert_detail_pesanan($dbsite,$d->id,$d->id_pesanan,$d->id_karat,$d->id_category,$d->nama_pesanan,$d->id_product,$d->product_weight,$d->harga_jual,$d->trans_date,$d->created_date,$d->created_by,$d->box_date,$d->box_by,$d->box_created_date,$d->ambil_date,$d->ambil_by,$d->ambil_created_date,$d->updated_date,$d->updated_by,$d->last_updated,$d->status);
		}
		
		$data_master_product = $this->ms->get_master_product($tgl_from,$tgl_to);
		foreach($data_master_product as $d){
			$this->ms->insert_master_product_name($dbsite,$d->id,$d->id_category,$d->nama_barang,$d->created_date,$d->created_by);
		}
		
		$data_mutasi_gr = $this->ms->get_mutasi_gr($tgl_from,$tgl_to);
		foreach($data_mutasi_gr as $d){
			$this->ms->insert_mutasi_gr($dbsite,$d->idsite,$d->idmutasi,$d->tipemutasi,$d->idkarat,$d->fromaccount,$d->toaccount,$d->value,$d->description,$d->transdate,$d->createddate,$d->createdby);
		}
		
		$data_mutasi_gr_hapus = $this->ms->get_mutasi_gr_hapus($tgl_from,$tgl_to);
		foreach($data_mutasi_gr_hapus as $d){
			$this->ms->insert_mutasi_gr_hapus($dbsite,$d->idsite,$d->idmutasi,$d->tipemutasi,$d->idkarat,$d->fromaccount,$d->toaccount,$d->value,$d->description,$d->transdate,$d->createddate,$d->createdby,$d->deleteddate,$d->deletedby);
		}
		
		$data_mutasi_rp = $this->ms->get_mutasi_rp($tgl_from,$tgl_to);
		foreach($data_mutasi_rp as $d){
			$this->ms->insert_mutasi_rp($dbsite,$d->idsite,$d->idmutasi,$d->tipemutasi,$d->fromaccount,$d->toaccount,$d->value,$d->description,$d->transdate,$d->createddate,$d->createdby);
		}
		
		$data_mutasi_rp_hapus = $this->ms->get_mutasi_rp_hapus($tgl_from,$tgl_to);
		foreach($data_mutasi_rp_hapus as $d){
			$this->ms->insert_mutasi_rp_hapus($dbsite,$d->idsite,$d->idmutasi,$d->tipemutasi,$d->fromaccount,$d->toaccount,$d->value,$d->description,$d->transdate,$d->createddate,$d->createdby,$d->deleteddate,$d->deletedby);
		}
		
		$data_mutasi_pengadaan = $this->ms->get_mutasi_pengadaan($tgl_from,$tgl_to);
		foreach($data_mutasi_pengadaan as $d){
			$this->ms->insert_mutasi_pengadaan($dbsite,$d->id,$d->id_pengiriman,$d->from_buy,$d->tipe,$d->fromaccount,$d->toaccount,$d->description,$d->dua_empat,$d->semsanam,$d->juhlima,$d->juhtus,$d->total_konv,$d->trans_date,$d->created_date,$d->created_by,$d->last_updated,$d->last_updated_by);
		}
		
		$data_mutasi_pengadaan_hapus = $this->ms->get_mutasi_pengadaan_hapus($tgl_from,$tgl_to);
		foreach($data_mutasi_pengadaan_hapus as $d){
			$this->ms->insert_mutasi_pengadaan_hapus($dbsite,$d->id,$d->id_pengiriman,$d->from_buy,$d->tipe,$d->fromaccount,$d->toaccount,$d->description,$d->dua_empat,$d->semsanam,$d->juhlima,$d->juhtus,$d->total_konv,$d->trans_date,$d->deleted_date,$d->deleted_by);
		}
		
		$data_mutasi_reparasi = $this->ms->get_mutasi_reparasi($tgl_from,$tgl_to);
		foreach($data_mutasi_reparasi as $d){
			$this->ms->insert_mutasi_reparasi($dbsite,$d->id,$d->id_pengiriman,$d->from_buy,$d->tipe,$d->fromaccount,$d->toaccount,$d->description,$d->dua_empat,$d->dua_empat_konv,$d->semsanam,$d->semsanam_konv,$d->juhlima,$d->juhlima_konv,$d->juhtus,$d->juhtus_konv,$d->total_konv,$d->trans_date,$d->created_date,$d->created_by,$d->last_updated,$d->last_updated_by);
		}
		
		$data_mutasi_reparasi_hapus = $this->ms->get_mutasi_reparasi_hapus($tgl_from,$tgl_to);
		foreach($data_mutasi_reparasi_hapus as $d){
			$this->ms->insert_mutasi_reparasi_hapus($dbsite,$d->id,$d->id_pengiriman,$d->from_buy,$d->tipe,$d->fromaccount,$d->toaccount,$d->description,$d->dua_empat,$d->dua_empat_konv,$d->semsanam,$d->semsanam_konv,$d->juhlima,$d->juhlima_konv,$d->juhtus,$d->juhtus_konv,$d->total_konv,$d->trans_date,$d->deleted_date,$d->deleted_by);
		}
		
		$data_mutasi_titip_pgd = $this->ms->get_mutasi_titip_pgd($tgl_from,$tgl_to);
		foreach($data_mutasi_titip_pgd as $d){
			$this->ms->insert_mutasi_titip_pengadaan($dbsite,$d->id,$d->id_pengiriman,$d->from_buy,$d->tipe,$d->fromaccount,$d->toaccount,$d->description,$d->dua_empat,$d->semsanam,$d->juhlima,$d->juhtus,$d->total_konv,$d->trans_date,$d->created_date,$d->created_by,$d->last_updated,$d->last_updated_by);
		}
		
		$data_mutasi_titip_pgd_hapus = $this->ms->get_mutasi_titip_pgd_hapus($tgl_from,$tgl_to);
		foreach($data_mutasi_titip_pgd_hapus as $d){
			$this->ms->insert_mutasi_titip_pengadaan_hapus($dbsite,$d->id,$d->id_pengiriman,$d->from_buy,$d->tipe,$d->fromaccount,$d->toaccount,$d->description,$d->dua_empat,$d->semsanam,$d->juhlima,$d->juhtus,$d->total_konv,$d->trans_date,$d->deleted_date,$d->deleted_by);
		}
		
		$data_pindah_box = $this->ms->get_pindah_box($tgl_from,$tgl_to);
		foreach($data_pindah_box as $d){
			$this->ms->insert_pindah_box($dbsite,$d->id,$d->id_product,$d->id_karat,$d->id_box_from,$d->id_box_to,$d->trans_date,$d->created_date,$d->created_by);
		}
		
		$data_stock_in = $this->ms->get_stock_in($tgl_from,$tgl_to);
		foreach($data_stock_in as $d){
			$this->ms->insert_stock_in($dbsite,$d->id,$d->id_karat,$d->id_box,$d->id_category,$d->id_from,$d->id_from_desc,$d->product_name,$d->product_weight,$d->trans_date,$d->created_date,$d->created_by);
		}
		
		$data_stock_out = $this->ms->get_stock_out($tgl_from,$tgl_to);
		foreach($data_stock_out as $d){
			$this->ms->insert_stock_out($dbsite,$d->id,$d->id_product,$d->id_karat,$d->id_box,$d->so_reason,$d->trans_date,$d->created_date,$d->created_by);
		}
		
		$data_periode = $this->ms->get_periode();
		foreach($data_periode as $d){
			$this->ms->insert_periode($dbsite,$d->id,$d->from_date,$d->to_date,$d->map,$d->created_date,$d->created_by);
		}
		
		$data_trans_cabang = $this->ms->get_detail_trans_cabang($tgl_from,$tgl_to);
		foreach($data_trans_cabang as $d){
			$this->ms->insert_detail_trans_cabang($dbsite,$d->id,$d->transaction_code,$d->cabang,$d->tipe,$d->description,$d->id_karat,$d->berat_real,$d->berat_konversi,$d->persentase,$d->trans_date,$d->created_date,$d->created_by);
		}
		
		$this->load->view('welcome_message');
	}
	
	public function lap_export_data($tgl_transaksi){
		$tgl_to = date("Y-m-d").' 23:59:59';
		$tgl_to2 = date("Y-m-d");
		$tgl_from = date('Y-m-d',strtotime($tgl_to2. "-7 days")).' 00:00:00';
		$dbsite = $this->ms->get_dbsite();
		
		$data_non_tunai = $this->ms->get_all_bayar_nontunai();
		foreach($data_non_tunai as $d){
			$this->ms->insert_bayar_nontunai($dbsite,$d->id,$d->description,$d->account_number,$d->status,$d->created_date,$d->created_by);
		}
		
		$data_box  = $this->ms->get_all_box();
		foreach($data_box as $d){
			$this->ms->insert_box($dbsite,$d->nama_box,$d->pesanan,$d->status);
		}
		
		$data_coa_gr = $this->ms->get_all_coa_gr();
		foreach($data_coa_gr as $d){
			$this->ms->insert_coa_gr($dbsite,$d->accountnumber,$d->accountnumberint,$d->accountname,$d->accountgroup,$d->beginningbalance,$d->status,$d->type,$d->idkarat,$d->created_date,$d->created_by);
		}
		
		$data_coa_rp = $this->ms->get_all_coa_rp();
		foreach($data_coa_rp as $d){
			$this->ms->insert_coa_rp($dbsite,$d->accountnumber,$d->accountnumberint,$d->accountname,$d->accountgroup,$d->beginningbalance,$d->status,$d->type,$d->created_date,$d->created_by);
		}
		
		$data_karyawan = $this->ms->get_all_karyawan();
		foreach($data_karyawan as $d){
			$this->ms->insert_karyawan($dbsite,$d->username,$d->nama_karyawan,$d->kelompok,$d->accountnumber,$d->status,$d->created_date,$d->created_by);
		}
		
		$data_kasir = $this->ms->get_all_kasir();
		foreach($data_kasir as $d){
			$this->ms->insert_kasir($dbsite,$d->id,$d->computer_name,$d->printer_name);
		}
		
		$data_tgl_aktif = $this->ms->get_tanggal_aktif();
		foreach($data_tgl_aktif as $d){
			$this->ms->insert_tanggal_aktif($dbsite,$d->id,$d->id_kasir,$d->tanggal_aktif);
		}
		
		/*
		$data_titipan_gr = $this->ms->get_all_titipan_gr2();
		$data_titipan_rp = $this->ms->get_all_titipan_rp2();
		$data_user = $this->ms->get_all_user();
		$data_do = $this->ms->get_dailyopen($tgl_from,$tgl_to);
		$data_product = $this->ms->get_data_product($tgl_from,$tgl_to);
		$data_main_beli = $this->ms->get_main_pembelian($tgl_from,$tgl_to);
		$data_detail_beli = $this->ms->get_detail_pembelian($tgl_from,$tgl_to);
		$data_main_jual = $this->ms->get_main_penjualan($tgl_from,$tgl_to);
		$data_detail_jual = $this->ms->get_detail_penjualan($tgl_from,$tgl_to);
		$data_main_pesanan = $this->ms->get_main_pesanan($tgl_from,$tgl_to);
		$data_detail_pesanan = $this->ms->get_detail_pesanan($tgl_from,$tgl_to);
		$data_master_product = $this->ms->get_master_product($tgl_from,$tgl_to);
		$data_mutasi_gr = $this->ms->get_mutasi_gr($tgl_from,$tgl_to);
		$data_mutasi_gr_hapus = $this->ms->get_mutasi_gr_hapus($tgl_from,$tgl_to);
		$data_mutasi_rp = $this->ms->get_mutasi_rp($tgl_from,$tgl_to);
		$data_mutasi_rp_hapus = $this->ms->get_mutasi_rp_hapus($tgl_from,$tgl_to);
		$data_mutasi_pengadaan = $this->ms->get_mutasi_pengadaan($tgl_from,$tgl_to);
		$data_mutasi_pengadaan_hapus = $this->ms->get_mutasi_pengadaan_hapus($tgl_from,$tgl_to);
		$data_mutasi_reparasi = $this->ms->get_mutasi_reparasi($tgl_from,$tgl_to);
		$data_mutasi_reparasi_hapus = $this->ms->get_mutasi_reparasi_hapus($tgl_from,$tgl_to);
		$data_mutasi_titip_pgd = $this->ms->get_mutasi_titip_pgd($tgl_from,$tgl_to);
		$data_mutasi_titip_pgd_hapus = $this->ms->get_mutasi_titip_pgd_hapus($tgl_from,$tgl_to);
		$data_pindah_box = $this->ms->get_pindah_box($tgl_from,$tgl_to);
		$data_stock_in = $this->ms->get_stock_in($tgl_from,$tgl_to);
		$data_stock_out = $this->ms->get_stock_out($tgl_from,$tgl_to);
		$data_periode = $this->ms->get_periode($tgl_from,$tgl_to);
		$data_trans_cabang = $this->ms->get_detail_trans_cabang($tgl_from,$tgl_to);
		*/
	}
	
	public function date_to_format($tanggal_mentah){
		$dateArray = explode(' ', $tanggal_mentah);
		$reportTanggal = $dateArray[0];
		$reportMonth = $dateArray[1];
		$reportTahun = $dateArray[2];
			
		switch($reportMonth){
			case "January":
				$reportBulan = '1';
				break;
			case "February":
				$reportBulan = '2';
				break;
			case "March":
				$reportBulan = '3';
				break;
			case "April":
				$reportBulan = '4';
				break;
			case "May":
				$reportBulan = '5';
				break;
			case "June":
				$reportBulan = '6';
				break;
			case "July":
				$reportBulan = '7';
				break;
			case "August":
				$reportBulan = '8';
				break;
			case "September":
				$reportBulan = '9';
				break;
			case "October":
				$reportBulan = '10';
				break;
			case "November":
				$reportBulan = '11';
				break;
			case "December":
				$reportBulan = '12';
				break;
		}
			
		$reportTime = strtotime($reportBulan.'/'.$reportTanggal.'/'.$reportTahun);
		return $reportTime;
	}
}