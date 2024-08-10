<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");

class C_login extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->library('M_pdf');
		$this->load->model('M_login','ml');
		$this->load->model('M_mutasi','mm');
		$this->load->model('M_pos','mt');
		$this->load->model('M_product','mp');
		$this->load->model('M_karat','mk');
		
		$GLOBALS['kasir'] = 1;
	}
	
	public function index(){
		if($this->session->userdata('gold_login') == TRUE){
			$active_date = $this->mt->get_tanggal_aktif($GLOBALS['kasir']);
			$tanggal_aktif = $active_date[0]->tanggal_aktif;
			$data['tanggal_do'] = $tanggal_aktif;
			
			$harga_emas = $this->mt->get_do_by_date($tanggal_aktif);
			
			if($harga_emas == 0){
				$harga_emas = $this->mt->get_last_do();
			}
			
			$data['harga_emas'] = $harga_emas;
			
			$tanggal_aktif = strtotime($tanggal_aktif);
			$bulan = date('m',$tanggal_aktif);
			
			switch($bulan){
				case "1":
					$aktif_bulan = 'January';
					break;
				case "2":
					$aktif_bulan = 'February';
					break;
				case "3":
					$aktif_bulan = 'March';
					break;
				case "4":
					$aktif_bulan = 'April';
					break;
				case "5":
					$aktif_bulan = 'May';
					break;
				case "6":
					$aktif_bulan = 'June';
					break;
				case "7":
					$aktif_bulan = 'July';
					break;
				case "8":
					$aktif_bulan = 'August';
					break;
				case "9":
					$aktif_bulan = 'September';
					break;
				case "10":
					$aktif_bulan = 'October';
					break;
				case "11":
					$aktif_bulan = 'November';
					break;
				case "12":
					$aktif_bulan = 'December';
					break;
			}
			
			$tanggal = date('d',$tanggal_aktif);
			$tahun = date('Y',$tanggal_aktif);
			
			$data['tanggal_aktif'] = $tanggal.' '.$aktif_bulan.' '.$tahun;
			$data['sitename'] = $this->mm->get_site_name();
			$this->load->view('V_menu',$data);
		}else{
			$this->load->view('V_login');
		}
	}
	
	public function cek_login(){
		$username = $this->input->post('username');
		$username = strtolower($username);
		$password = $this->input->post('password');
		
		if($this->ml->cek_login($username,$password) == TRUE){
			$data['success'] = true;
			$data['lokasi'] = base_url();
			
			$userdata = $this->ml->get_userdata($username);
			foreach ($userdata as $ud) {
				$nama_user = $ud->nama_user;
				$kasir = $ud->priv_kasir;
				$manager = $ud->priv_manager;
				$pembukuan = $ud->priv_pembukuan;
				$admin = $ud->priv_admin;
				$pp = $ud->picture;
			}
			
			$newsession = array(
				'gold_username' => $username,
				'gold_nama_user' => $nama_user,
				'gold_kasir' => $kasir,
				'gold_manager' => $manager,
				'gold_pembukuan' => $pembukuan,
				'gold_admin' => $admin,
				'gold_pp' => $pp,
				'gold_login' => TRUE
			);
			$this->session->set_userdata($newsession);
			$this->session->sess_expiration = '32140800';
		}else{
			$data['success'] = false;
		}
		echo json_encode($data);
	}
	
	public function cek_login_pos(){
		$username = $this->input->post('username');
		$username = strtolower($username);
		$password = $this->input->post('password');
		
		if($this->ml->cek_login($username,$password) == TRUE){
			$data['success'] = true;
			$data['lokasi'] = base_url().'index.php/C_home_pos';
			
			$userdata = $this->ml->get_userdata($username);
			foreach ($userdata as $ud) {
				$nama_user = $ud->nama_user;
				$kasir = $ud->priv_kasir;
				$manager = $ud->priv_manager;
				$pembukuan = $ud->priv_pembukuan;
				$admin = $ud->priv_admin;
				$pp = $ud->picture;
			}
			
			$newsession = array(
				'gold_username' => $username,
				'gold_nama_user' => $nama_user,
				'gold_kasir' => $kasir,
				'gold_manager' => $manager,
				'gold_pembukuan' => $pembukuan,
				'gold_admin' => $admin,
				'gold_pp' => $pp,
				'gold_login' => TRUE
			);
			$this->session->set_userdata($newsession);
			$this->session->sess_expiration = '32140800';
		}else{
			$data['success'] = false;
		}
		echo json_encode($data);
	}
	
	public function generate_report(){
		$active_date = $this->mt->get_tanggal_aktif($GLOBALS['kasir']);
		$per_tanggal = $active_date[0]->tanggal_aktif;
		$per_tanggal = strtotime($per_tanggal);
		$per_tanggal = date('Y-m-d',$per_tanggal).' 23:59:59';
		$tanggal_aktif = $active_date[0]->tanggal_aktif;
		
		$filter_box_from = 1;
		$filter_box_to = 999;
		$detail_rekap = 'R';
		$box_karat = 'K';
		
		if($detail_rekap == 'R'){
			$dr = 'Rekap';
		}else{
			$dr = 'Detail';
		}
		
		if($box_karat == 'B'){
			$bk = 'Box';
		}else{
			$bk = 'Karat';
		}
		
		$array_box = array();
		
		$data['view'] = '<div class="ui stackable grid"><div class="sixteen wide centered column full-print"><table id="filter_data_report" class="ui celled table" style="width:100%"><thead style="text-align:center"><tr><th colspan="5" style="text-align:left;">Emas Pajangan</th></tr><tr><th style="width:50px">No</th><th>Karat</th><th>Pcs</th><th>Gram</th><th>24K</th></tr></thead><tbody>';
		
		$filter_product_pindah_box = '';
			
		$pindah_box = $this->mp->get_posisi_pindah_box($per_tanggal,$filter_box_from,$filter_box_to);
		if(count($pindah_box) == 0){
			$filter_product_pindah_box .= '""';
		}else{
			for($i=0; $i<count($pindah_box); $i++){
				if($i == 0){
					$filter_product_pindah_box .= '"'.$pindah_box[$i]->id_product.'"';
				}else{
					$filter_product_pindah_box .= ',"'.$pindah_box[$i]->id_product.'"';
				}
			}
		}
		
		$data_filter = $this->mp->get_posisi_detail_pajangan($per_tanggal,$filter_box_from,$filter_box_to,$filter_product_pindah_box);
		
		foreach($data_filter as $d){
			$array_box[$d->id] = $d->id_box;
		}
		
		foreach($pindah_box as $p){
			$array_box[$p->id_product] = $p->id_box_from;
		}
		
		$data_karat = $this->mk->get_karat_srt();
		$number = 0;
		$total_weight = 0;
		$total_konversi = 0;
		$count_data_total = 0;
		
		foreach($data_karat as $dk){
			$view_temp = '';
			$count_data_karat = 0;
			$total_weight_karat = 0;
			foreach($data_filter as $df){
				if($df->id_karat == $dk->id){
					if($array_box[$df->id] >= $filter_box_from && $array_box[$df->id] <= $filter_box_to){
						$count_data_karat = $count_data_karat + 1;
						$total_weight_karat = $total_weight_karat + $df->product_weight;
					}
					
				}
			}
			
			if($count_data_karat > 0){
				$number = $number + 1;

				$view_temp .= '<tr><td class="right aligned">'.$number.'</td><td>'.$dk->karat_name.'</td><td class="right aligned">'.number_format($count_data_karat, 0).'</td><td class="right aligned">'.number_format($total_weight_karat, 3).'</td><td class="right aligned">'.number_format($total_weight_karat * $dk->kali_laporan, 3).'</td></tr>';
				
				$total_weight = $total_weight + $total_weight_karat;
				$total_konversi = $total_konversi + ($total_weight_karat * $dk->kali_laporan);
				$count_data_total = $count_data_total + $count_data_karat;
				
				$data['view'] .= $view_temp;
			}
		}
		
		$total_pajangan24 = $total_konversi;
		$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL</td><td class="td-total right aligned">'.number_format($count_data_total, 0).'</td><td class="td-total right aligned">'.number_format($total_weight, 3).'</td><td class="td-total right aligned">'.number_format($total_konversi, 3).'</td></tr>';
		
		
		$data['view'] .= '<tr><td colspan="5" class="theader">Reparasi Toko</td></tr>';
		$coa_from = 170001;
		$coa_to = 170001;
		
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		$count_data = 0;
		$total_gram = 0;
		$total_konversi = 0;
		foreach($data_karat as $k){
			$flag_kurs = array();
			$flag_saldo_akhir = array();
			
			$sum_eb = 0;
			
			foreach($kas_account as $ka){
				$saldo_akhir_kurs = array();
				$acc_number = $ka->accountnumber;
				$acc_group = $ka->accountgroup;
				$id_kurs = $k->id;
				$type = $ka->type;
				
				/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
				
				$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
				
				/*--------------------------------------------------*/
				
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_akhir_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$per_tanggal,$id_kurs);
				$saldo_akhir_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$per_tanggal,$id_kurs);
				
				foreach($saldo_akhir_d as $mttd){
					$saldo_akhir_kurs[$mttd->idkarat] = $saldo_akhir_kurs[$mttd->idkarat] + $mttd->total_mutasi;
				}
			
				foreach($saldo_akhir_k as $mttk){
					$saldo_akhir_kurs[$mttk->idkarat] = $saldo_akhir_kurs[$mttk->idkarat] - $mttk->total_mutasi;
				}	
				
				/*---------------------------------------------------*/
				
				/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
				
				$flag_kurs[$k->id] = FALSE;
				$flag_saldo_akhir[$k->id] = FALSE;
				
				if($saldo_akhir_kurs[$k->id] != 0){
					$flag_kurs[$k->id] = TRUE;
					$flag_saldo_akhir[$k->id] = TRUE;
				}
				
				/*-------- Menampilkan Data Dalam Tabel Report -------*/
				
				if($flag_kurs[$k->id] == TRUE){
					$count_data = $count_data + 1;
					$total_gram = $total_gram + $saldo_akhir_kurs[$k->id];
					$total_konversi = $total_konversi + ($saldo_akhir_kurs[$k->id]*$k->kali_laporan);
					
					$data['view'] .= '<tr><td class="right aligned">'.$count_data.'</td><td>'.$k->karat_name.'</td><td></td>';
					$sak = $saldo_akhir_kurs[$k->id];
					$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs[$k->id], 3).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs[$k->id]*$k->kali_laporan, 3).'</td>';
					$data['view'] .= '</tr>';
				}
			}
		}
		
		$total_reptoko24 = $total_konversi;
		$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL</td><td class="td-total"></td><td class="td-total right aligned">'.number_format($total_gram, 3).'</td><td class="td-total right aligned">'.number_format($total_konversi, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="5" class="theader">Departemen Reparasi</td></tr>';
		$coa_from = 170002;
		$coa_to = 170002;
		
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		if($coa_from == '170002'){
			$tabel_name = 'gold_mutasi_reparasi';
		}else if($coa_from == '170003'){
			$tabel_name = 'gold_mutasi_pengadaan';
		}
		
		$count_data = 0;
		foreach($kas_account as $ka){
			$flag_kurs = array();
			$flag_saldo_akhir = array();
			$saldo_akhir_kurs = array();
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			$id_kurs = 1;
			$type = $ka->type;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] + $mttd->total_mutasi;
			}
			
			foreach($saldo_akhir_k as $mttk){
				$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] - $mttk->total_mutasi;
			}
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs[$id_kurs] = FALSE;
			$flag_saldo_akhir[$id_kurs] = FALSE;
			
			if($saldo_akhir_kurs[$id_kurs] != 0){
				$flag_kurs[$id_kurs] = TRUE;
				$flag_saldo_akhir[$id_kurs] = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs[$id_kurs] == TRUE){
				$count_data = $count_data + 1;
				$data['view'] .= '<tr><td class="right aligned td-total" colspan="2">TOTAL</td><td class="td-total"></td><td class="td-total"></td>';
				$data['view'] .= '<td class="right aligned td-total">'.number_format($saldo_akhir_kurs[$id_kurs], 3).'</td>';
				$data['view'] .= '</tr>';
			}
			
			$total_deprep24 = $saldo_akhir_kurs[$id_kurs];
		}
		
		$data['view'] .= '<tr><td colspan="5" class="theader">Departemen Pengadaan</td></tr>';
		$coa_from = 170003;
		$coa_to = 170003;
		
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		if($coa_from == '170002'){
			$tabel_name = 'gold_mutasi_reparasi';
		}else if($coa_from == '170003'){
			$tabel_name = 'gold_mutasi_pengadaan';
		}
		
		$count_data = 0;
		foreach($kas_account as $ka){
			$flag_kurs = array();
			$flag_saldo_akhir = array();
			$saldo_akhir_kurs = array();
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			$id_kurs = 1;
			$type = $ka->type;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] + $mttd->total_mutasi;
			}
			
			foreach($saldo_akhir_k as $mttk){
				$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] - $mttk->total_mutasi;
			}
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs[$id_kurs] = FALSE;
			$flag_saldo_akhir[$id_kurs] = FALSE;
			
			if($saldo_akhir_kurs[$id_kurs] != 0){
				$flag_kurs[$id_kurs] = TRUE;
				$flag_saldo_akhir[$id_kurs] = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs[$id_kurs] == TRUE){
				$count_data = $count_data + 1;
				$data['view'] .= '<tr><td class="right aligned td-total" colspan="2">TOTAL</td><td class="td-total"></td><td class="td-total"></td>';
				$data['view'] .= '<td class="right aligned td-total">'.number_format($saldo_akhir_kurs[$id_kurs], 3).'</td>';
				$data['view'] .= '</tr>';
			}
			
			$total_depgrs24 = $saldo_akhir_kurs[$id_kurs];
		}
		
		$data['view'] .= '<tr><td colspan="5" class="theader">Titipan Pelanggan</td></tr>';
		
		$coa_from = 220001;
		$coa_to = 229999;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		$total_fisik = 0;
		$total_titipan24 = 0;
		if(count($kas_account) > 0){
			$total_titipan = 0;
			
			foreach($kas_account as $ka){
				$saldo_awal_kurs = array();
				
				$acc_number = $ka->accountnumber;
				$id_kurs = 1;
				
				$type = $ka->type;
				
				$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance($acc_number);
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_bykarat($acc_number,$per_tanggal,$id_kurs);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_bykarat($acc_number,$per_tanggal,$id_kurs);
				
				foreach($saldo_awal_d as $mtyd){
					$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] - $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] + $mtyk->total_mutasi;
				}
				
				$total_titipan = $total_titipan + $saldo_awal_kurs[$id_kurs];				
			}
			
			$data['view'] .= '<td class="right aligned td-total" colspan="2">TOTAL KONVERSI 24K</td><td class="td-total"></td><td class="td-total"></td><td class="right aligned td-total">'.number_format($total_titipan, 3).'</td></tr>';
			
			$total_titipan24 = $total_titipan;
		}
		
		$total24 = $total_pajangan24 + $total_reptoko24 + $total_deprep24 + $total_depgrs24 - $total_titipan24;
		$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '<td class="right aligned td-total" colspan="2">TOTAL EMAS</td><td class="td-total"></td><td class="td-total"></td><td class="right aligned td-total">'.number_format($total24, 3).'</td></tr>';
		
		$data['view'] .= '</tbody></table></div></div>';
		
		$data['viewkanan'] = $data['view'];
		
		$data['view'] = '<div class="ui stackable grid"><div class="sixteen wide centered column full-print"><table id="filter_data_report_2" class="ui celled table" style="width:100%"><thead style="text-align:center"><tr><th style="width:50px">No</th><th>Account</th><th>Nilai</th><th>Rupiah</th><th>24K</th></tr><tr><th colspan="5" style="text-align:left !important">Kas dan Bank</th></tr></thead><tbody>';
		
		$harga_emas = $this->mt->get_do_by_date($tanggal_aktif);
		$coa_from = 110001;
		$coa_to = 139999;
		
		$kas_account = $this->mm->get_coa_rp_by_range($coa_from, $coa_to);
		
		$count_data = 0;
		
		$flag_tulis = TRUE;
		$flag_kurs = '';
		$flag_saldo_akhir = '';
		
		$total_kasbank_rp = 0;
		$total_kasbank_gr = 0;
		foreach($kas_account as $ka){
			$saldo_akhir_kurs = 0;
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttd->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttd->total_mutasi;
				}
			}
		
			foreach($saldo_akhir_k as $mttk){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttk->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttk->total_mutasi;
				}
			}	
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs = FALSE;
			$flag_saldo_akhir = FALSE;
			
			if($saldo_akhir_kurs != 0){
				$flag_kurs = TRUE;
				$flag_saldo_akhir = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs == TRUE){
				$count_data = $count_data + 1;
				
				$data['view'] .= '<tr><td class="right aligned">'.$count_data.'</td><td>'.$ka->accountname.'</td>';
				
				if($saldo_akhir_kurs != 0 && $saldo_akhir_kurs != '' && $saldo_akhir_kurs != -0){
					
					$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs/$harga_emas, 3).'</td>';
				}else{
					$data['view'] .= '<td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td>';
				}
				
				$data['view'] .= '</tr>';
			}
			
			$total_kasbank_rp = $total_kasbank_rp + $saldo_akhir_kurs;
			$total_kasbank_gr = $total_kasbank_gr + ($saldo_akhir_kurs/$harga_emas);
		}
		
		$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL KAS & BANK</td><td class="td-total"></td><td class="td-total right aligned">'.number_format($total_kasbank_rp, 0).'</td><td class="td-total right aligned">'.number_format($total_kasbank_gr, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="5" class="theader">Piutang Usaha</td></tr>';
		
		$coa_from = 140001;
		$coa_to = 179999;
		
		$kas_account = $this->mm->get_coa_rp_by_range($coa_from, $coa_to);
		
		$count_data = 0;
		
		$flag_tulis = TRUE;
		$flag_kurs = '';
		$flag_saldo_akhir = '';
		
		$total_piutang_rp = 0;
		$total_piutang_gr = 0;
		foreach($kas_account as $ka){
			$saldo_akhir_kurs = 0;
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttd->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttd->total_mutasi;
				}
			}
		
			foreach($saldo_akhir_k as $mttk){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttk->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttk->total_mutasi;
				}
			}	
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs = FALSE;
			$flag_saldo_akhir = FALSE;
			
			if($saldo_akhir_kurs != 0){
				$flag_kurs = TRUE;
				$flag_saldo_akhir = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs == TRUE){
				$count_data = $count_data + 1;
				
				//$data['view'] .= '<tr><td class="right aligned">'.$count_data.'</td><td>'.$ka->accountname.'</td>';
				
				if($saldo_akhir_kurs != 0 && $saldo_akhir_kurs != '' && $saldo_akhir_kurs != -0){
					
					//$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs/$harga_emas, 2).'</td>';
				}else{
					//$data['view'] .= '<td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td>';
				}
				
				//$data['view'] .= '</tr>';
				$total_piutang_rp = $total_piutang_rp + $saldo_akhir_kurs;
				$total_piutang_gr = $total_piutang_gr + ($saldo_akhir_kurs/$harga_emas);
			}
		}
		
		$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL PIUTANG</td><td class="td-total"></td><td class="td-total right aligned">'.number_format($total_piutang_rp, 0).'</td><td class="td-total right aligned">'.number_format($total_piutang_gr, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL HARTA</td><td class="td-total"></td><td class="td-total right aligned">'.number_format(($total24*$harga_emas) + $total_kasbank_rp + $total_piutang_rp, 0).'</td><td class="td-total right aligned">'.number_format($total24 + $total_kasbank_gr + $total_piutang_gr, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="5" class="td-total"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '<tr><td colspan="5" class="theader">Hutang/Kewajiban</td></tr>';
		
		$coa_from = 210001;
		$coa_to = 239999;
		
		$kas_account = $this->mm->get_coa_rp_by_range($coa_from, $coa_to);
		
		$count_data = 0;
		
		$flag_tulis = TRUE;
		$flag_kurs = '';
		$flag_saldo_akhir = '';
		
		$total_hutang_rp = 0;
		$total_hutang_gr = 0;
		foreach($kas_account as $ka){
			$saldo_akhir_kurs = 0;
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttd->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttd->total_mutasi;
				}
			}
		
			foreach($saldo_akhir_k as $mttk){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttk->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttk->total_mutasi;
				}
			}	
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs = FALSE;
			$flag_saldo_akhir = FALSE;
			
			if($saldo_akhir_kurs != 0){
				$flag_kurs = TRUE;
				$flag_saldo_akhir = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs == TRUE){
				$count_data = $count_data + 1;
				
				//$data['view'] .= '<tr><td class="right aligned">'.$count_data.'</td><td>'.$ka->accountname.'</td>';
				
				if($saldo_akhir_kurs != 0 && $saldo_akhir_kurs != '' && $saldo_akhir_kurs != -0){
					
					//$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs/$harga_emas, 2).'</td>';
				}else{
					//$data['view'] .= '<td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td>';
				}
				
				//$data['view'] .= '</tr>';
				
				$total_hutang_rp = $total_hutang_rp + $saldo_akhir_kurs;
				$total_hutang_gr = $total_hutang_gr + ($saldo_akhir_kurs/$harga_emas);
			}
		}
		
		$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL HUTANG</td><td class="td-total"></td><td class="td-total right aligned">'.number_format($total_hutang_rp, 0).'</td><td class="td-total right aligned">'.number_format($total_hutang_gr, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		$modal_kerja_rp = ($total24*$harga_emas) + $total_kasbank_rp + $total_piutang_rp - $total_hutang_rp;
		$modal_kerja_gr = $total24 + $total_kasbank_gr + $total_piutang_gr - $total_hutang_gr;
		
		$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">MODAL KERJA</td><td class="td-total"></td><td class="td-total right aligned">'.number_format($modal_kerja_rp, 0).'</td><td class="td-total right aligned">'.number_format($modal_kerja_gr, 3).'</td></tr>';
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		$active_date = $this->mt->get_tanggal_aktif($GLOBALS['kasir']);
		$per_tanggal = $active_date[0]->tanggal_aktif;
		$tanggal_aktif = date('Y-m-d',strtotime($per_tanggal. "-1 days")).' 00:00:00';
		$per_tanggal = date('Y-m-d',strtotime($per_tanggal. "-1 days")).' 23:59:59';
		
		$filter_box_from = 1;
		$filter_box_to = 999;
		$detail_rekap = 'R';
		$box_karat = 'K';
		
		if($detail_rekap == 'R'){
			$dr = 'Rekap';
		}else{
			$dr = 'Detail';
		}
		
		if($box_karat == 'B'){
			$bk = 'Box';
		}else{
			$bk = 'Karat';
		}
		
		$array_box = array();
		
		//$data['view'] = '<div class="ui stackable grid"><div class="sixteen wide centered column full-print"><table id="filter_data_report" class="ui celled table" style="width:100%"><thead style="text-align:center"><tr><th colspan="5" style="text-align:left;">Emas Pajangan</th></tr><tr><th style="width:50px">No</th><th>Karat</th><th>Pcs</th><th>Gram</th><th>24K</th></tr></thead><tbody>';
		
		$filter_product_pindah_box = '';
			
		$pindah_box = $this->mp->get_posisi_pindah_box($per_tanggal,$filter_box_from,$filter_box_to);
		if(count($pindah_box) == 0){
			$filter_product_pindah_box .= '""';
		}else{
			for($i=0; $i<count($pindah_box); $i++){
				if($i == 0){
					$filter_product_pindah_box .= '"'.$pindah_box[$i]->id_product.'"';
				}else{
					$filter_product_pindah_box .= ',"'.$pindah_box[$i]->id_product.'"';
				}
			}
		}
		
		$data_filter = $this->mp->get_posisi_detail_pajangan($per_tanggal,$filter_box_from,$filter_box_to,$filter_product_pindah_box);
		
		foreach($data_filter as $d){
			$array_box[$d->id] = $d->id_box;
		}
		
		foreach($pindah_box as $p){
			$array_box[$p->id_product] = $p->id_box_from;
		}
		
		$data_karat = $this->mk->get_karat_srt();
		$number = 0;
		$total_weight = 0;
		$total_konversi = 0;
		$count_data_total = 0;
		
		foreach($data_karat as $dk){
			$view_temp = '';
			$count_data_karat = 0;
			$total_weight_karat = 0;
			foreach($data_filter as $df){
				if($df->id_karat == $dk->id){
					if($array_box[$df->id] >= $filter_box_from && $array_box[$df->id] <= $filter_box_to){
						$count_data_karat = $count_data_karat + 1;
						$total_weight_karat = $total_weight_karat + $df->product_weight;
					}
					
				}
			}
			
			if($count_data_karat > 0){
				$number = $number + 1;

				//$view_temp .= '<tr><td class="right aligned">'.$number.'</td><td>'.$dk->karat_name.'</td><td class="right aligned">'.number_format($count_data_karat, 0).'</td><td class="right aligned">'.number_format($total_weight_karat, 3).'</td><td class="right aligned">'.number_format($total_weight_karat * $dk->kali_laporan, 3).'</td></tr>';
				
				$total_weight = $total_weight + $total_weight_karat;
				$total_konversi = $total_konversi + ($total_weight_karat * $dk->kali_laporan);
				$count_data_total = $count_data_total + $count_data_karat;
				
				//$data['view'] .= $view_temp;
			}
		}
		
		$total_pajangan24 = $total_konversi;
		//$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL</td><td class="td-total right aligned">'.number_format($count_data_total, 0).'</td><td class="td-total right aligned">'.number_format($total_weight, 3).'</td><td class="td-total right aligned">'.number_format($total_konversi, 3).'</td></tr>';
		
		
		//$data['view'] .= '<tr><td colspan="5" class="theader">Reparasi Toko</td></tr>';
		$coa_from = 170001;
		$coa_to = 170001;
		
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		$count_data = 0;
		$total_gram = 0;
		$total_konversi = 0;
		foreach($data_karat as $k){
			$flag_kurs = array();
			$flag_saldo_akhir = array();
			
			$sum_eb = 0;
			
			foreach($kas_account as $ka){
				$saldo_akhir_kurs = array();
				$acc_number = $ka->accountnumber;
				$acc_group = $ka->accountgroup;
				$id_kurs = $k->id;
				$type = $ka->type;
				
				/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
				
				$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
				
				/*--------------------------------------------------*/
				
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_akhir_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$per_tanggal,$id_kurs);
				$saldo_akhir_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$per_tanggal,$id_kurs);
				
				foreach($saldo_akhir_d as $mttd){
					$saldo_akhir_kurs[$mttd->idkarat] = $saldo_akhir_kurs[$mttd->idkarat] + $mttd->total_mutasi;
				}
			
				foreach($saldo_akhir_k as $mttk){
					$saldo_akhir_kurs[$mttk->idkarat] = $saldo_akhir_kurs[$mttk->idkarat] - $mttk->total_mutasi;
				}	
				
				/*---------------------------------------------------*/
				
				/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
				
				$flag_kurs[$k->id] = FALSE;
				$flag_saldo_akhir[$k->id] = FALSE;
				
				if($saldo_akhir_kurs[$k->id] != 0){
					$flag_kurs[$k->id] = TRUE;
					$flag_saldo_akhir[$k->id] = TRUE;
				}
				
				/*-------- Menampilkan Data Dalam Tabel Report -------*/
				
				if($flag_kurs[$k->id] == TRUE){
					$count_data = $count_data + 1;
					$total_gram = $total_gram + $saldo_akhir_kurs[$k->id];
					$total_konversi = $total_konversi + ($saldo_akhir_kurs[$k->id]*$k->kali_laporan);
					
					//$data['view'] .= '<tr><td class="right aligned">'.$count_data.'</td><td>'.$k->karat_name.'</td><td></td>';
					$sak = $saldo_akhir_kurs[$k->id];
					//$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs[$k->id], 3).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs[$k->id]*$k->kali_laporan, 3).'</td>';
					//$data['view'] .= '</tr>';
				}
			}
		}
		
		$total_reptoko24 = $total_konversi;
		//$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL</td><td class="td-total"></td><td class="td-total right aligned">'.number_format($total_gram, 3).'</td><td class="td-total right aligned">'.number_format($total_konversi, 3).'</td></tr>';
		
		//$data['view'] .= '<tr><td colspan="5" class="theader">Departemen Reparasi</td></tr>';
		$coa_from = 170002;
		$coa_to = 170002;
		
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		if($coa_from == '170002'){
			$tabel_name = 'gold_mutasi_reparasi';
		}else if($coa_from == '170003'){
			$tabel_name = 'gold_mutasi_pengadaan';
		}
		
		$count_data = 0;
		foreach($kas_account as $ka){
			$flag_kurs = array();
			$flag_saldo_akhir = array();
			$saldo_akhir_kurs = array();
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			$id_kurs = 1;
			$type = $ka->type;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] + $mttd->total_mutasi;
			}
			
			foreach($saldo_akhir_k as $mttk){
				$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] - $mttk->total_mutasi;
			}
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs[$id_kurs] = FALSE;
			$flag_saldo_akhir[$id_kurs] = FALSE;
			
			if($saldo_akhir_kurs[$id_kurs] != 0){
				$flag_kurs[$id_kurs] = TRUE;
				$flag_saldo_akhir[$id_kurs] = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs[$id_kurs] == TRUE){
				$count_data = $count_data + 1;
				//$data['view'] .= '<tr><td class="right aligned td-total" colspan="2">TOTAL</td><td class="td-total"></td><td class="td-total"></td>';
				//$data['view'] .= '<td class="right aligned td-total">'.number_format($saldo_akhir_kurs[$id_kurs], 3).'</td>';
				//$data['view'] .= '</tr>';
			}
			
			$total_deprep24 = $saldo_akhir_kurs[$id_kurs];
		}
		
		//$data['view'] .= '<tr><td colspan="5" class="theader">Departemen Pengadaan</td></tr>';
		$coa_from = 170003;
		$coa_to = 170003;
		
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		if($coa_from == '170002'){
			$tabel_name = 'gold_mutasi_reparasi';
		}else if($coa_from == '170003'){
			$tabel_name = 'gold_mutasi_pengadaan';
		}
		
		$count_data = 0;
		foreach($kas_account as $ka){
			$flag_kurs = array();
			$flag_saldo_akhir = array();
			$saldo_akhir_kurs = array();
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			$id_kurs = 1;
			$type = $ka->type;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] + $mttd->total_mutasi;
			}
			
			foreach($saldo_akhir_k as $mttk){
				$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] - $mttk->total_mutasi;
			}
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs[$id_kurs] = FALSE;
			$flag_saldo_akhir[$id_kurs] = FALSE;
			
			if($saldo_akhir_kurs[$id_kurs] != 0){
				$flag_kurs[$id_kurs] = TRUE;
				$flag_saldo_akhir[$id_kurs] = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs[$id_kurs] == TRUE){
				$count_data = $count_data + 1;
				//$data['view'] .= '<tr><td class="right aligned td-total" colspan="2">TOTAL</td><td class="td-total"></td><td class="td-total"></td>';
				//$data['view'] .= '<td class="right aligned td-total">'.number_format($saldo_akhir_kurs[$id_kurs], 3).'</td>';
				//$data['view'] .= '</tr>';
			}
			
			$total_depgrs24 = $saldo_akhir_kurs[$id_kurs];
		}
		
		//$data['view'] .= '<tr><td colspan="5" class="theader">Titipan Pelanggan</td></tr>';
		
		$coa_from = 220001;
		$coa_to = 229999;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		$total_fisik = 0;
		
		if(count($kas_account) > 0){
			$total_titipan = 0;
			
			foreach($kas_account as $ka){
				$saldo_awal_kurs = array();
				
				$acc_number = $ka->accountnumber;
				$id_kurs = 1;
				
				$type = $ka->type;
				
				$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance($acc_number);
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_bykarat($acc_number,$per_tanggal,$id_kurs);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_bykarat($acc_number,$per_tanggal,$id_kurs);
				
				foreach($saldo_awal_d as $mtyd){
					$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] - $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] + $mtyk->total_mutasi;
				}
				
				$total_titipan = $total_titipan + $saldo_awal_kurs[$id_kurs];				
			}
			
			//$data['view'] .= '<td class="right aligned td-total" colspan="2">TOTAL KONVERSI 24K</td><td class="td-total"></td><td class="td-total"></td><td class="right aligned td-total">'.number_format($total_titipan, 3).'</td></tr>';
			
			$total_titipan24 = $total_titipan;
		}
		
		$total24 = $total_pajangan24 + $total_reptoko24 + $total_deprep24 + $total_depgrs24 - $total_titipan24;
		//$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		//$data['view'] .= '<td class="right aligned td-total" colspan="2">TOTAL EMAS</td><td class="td-total"></td><td class="td-total"></td><td class="right aligned td-total">'.number_format($total24, 3).'</td></tr>';
		
		//$data['view'] .= '</tbody></table></div></div>';
		
		//$data['viewkanan'] = $data['view'];
		
		//$data['view'] = '<div class="ui stackable grid"><div class="sixteen wide centered column full-print"><table id="filter_data_report_2" class="ui celled table" style="width:100%"><thead style="text-align:center"><tr><th colspan="5">Kas dan Bank</th></tr><tr><th style="width:50px">No</th><th>Account</th><th>Nilai</th><th>Rupiah</th><th>24K</th></tr></thead><tbody>';
		
		//echo $tanggal_aktif.'<br>';
		$harga_emas = $this->mt->get_do_by_date($tanggal_aktif);
		//echo $harga_emas.'<br>';
		$coa_from = 110001;
		$coa_to = 139999;
		
		$kas_account = $this->mm->get_coa_rp_by_range($coa_from, $coa_to);
		
		$count_data = 0;
		
		$flag_tulis = TRUE;
		$flag_kurs = '';
		$flag_saldo_akhir = '';
		
		$total_kasbank_rp = 0;
		$total_kasbank_gr = 0;
		foreach($kas_account as $ka){
			$saldo_akhir_kurs = 0;
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttd->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttd->total_mutasi;
				}
			}
		
			foreach($saldo_akhir_k as $mttk){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttk->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttk->total_mutasi;
				}
			}	
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs = FALSE;
			$flag_saldo_akhir = FALSE;
			
			if($saldo_akhir_kurs != 0){
				$flag_kurs = TRUE;
				$flag_saldo_akhir = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs == TRUE){
				$count_data = $count_data + 1;
				
				//$data['view'] .= '<tr><td class="right aligned">'.$count_data.'</td><td>'.$ka->accountname.'</td>';
				
				if($saldo_akhir_kurs != 0 && $saldo_akhir_kurs != '' && $saldo_akhir_kurs != -0){
					
					//$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs/$harga_emas, 3).'</td>';
				}else{
					//$data['view'] .= '<td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td>';
				}
				
				//$data['view'] .= '</tr>';
			}
			
			$total_kasbank_rp = $total_kasbank_rp + $saldo_akhir_kurs;
			//echo $harga_emas;
			$total_kasbank_gr = $total_kasbank_gr + ($saldo_akhir_kurs/$harga_emas);
		}
		
		//$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL KAS & BANK</td><td class="td-total"></td><td class="td-total right aligned">'.number_format($total_kasbank_rp, 0).'</td><td class="td-total right aligned">'.number_format($total_kasbank_gr, 3).'</td></tr>';
		
		//$data['view'] .= '<tr><td colspan="5" class="theader">Piutang Usaha</td></tr>';
		
		$coa_from = 140001;
		$coa_to = 179999;
		
		$kas_account = $this->mm->get_coa_rp_by_range($coa_from, $coa_to);
		
		$count_data = 0;
		
		$flag_tulis = TRUE;
		$flag_kurs = '';
		$flag_saldo_akhir = '';
		
		$total_piutang_rp = 0;
		$total_piutang_gr = 0;
		foreach($kas_account as $ka){
			$saldo_akhir_kurs = 0;
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttd->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttd->total_mutasi;
				}
			}
		
			foreach($saldo_akhir_k as $mttk){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttk->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttk->total_mutasi;
				}
			}	
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs = FALSE;
			$flag_saldo_akhir = FALSE;
			
			if($saldo_akhir_kurs != 0){
				$flag_kurs = TRUE;
				$flag_saldo_akhir = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs == TRUE){
				$count_data = $count_data + 1;
				
				//$data['view'] .= '<tr><td class="right aligned">'.$count_data.'</td><td>'.$ka->accountname.'</td>';
				
				if($saldo_akhir_kurs != 0 && $saldo_akhir_kurs != '' && $saldo_akhir_kurs != -0){
					
					//$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs/$harga_emas, 2).'</td>';
				}else{
					//$data['view'] .= '<td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td>';
				}
				
				//$data['view'] .= '</tr>';
				$total_piutang_rp = $total_piutang_rp + $saldo_akhir_kurs;
				$total_piutang_gr = $total_piutang_gr + ($saldo_akhir_kurs/$harga_emas);
			}
		}
		
		//$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL PIUTANG</td><td class="td-total"></td><td class="td-total right aligned">'.number_format($total_piutang_rp, 0).'</td><td class="td-total right aligned">'.number_format($total_piutang_gr, 3).'</td></tr>';
		
		//$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		//$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL HARTA</td><td class="td-total"></td><td class="td-total right aligned">'.number_format(($total24*$harga_emas) + $total_kasbank_rp + $total_piutang_rp, 0).'</td><td class="td-total right aligned">'.number_format($total24 + $total_kasbank_gr + $total_piutang_gr, 3).'</td></tr>';
		
		//$data['view'] .= '<tr><td colspan="5" class="td-total"><span style="visibility:hidden">-</span></td></tr>';
		
		//$data['view'] .= '<tr><td colspan="5" class="theader">Hutang/Kewajiban</td></tr>';
		
		$coa_from = 210001;
		$coa_to = 239999;
		
		$kas_account = $this->mm->get_coa_rp_by_range($coa_from, $coa_to);
		
		$count_data = 0;
		
		$flag_tulis = TRUE;
		$flag_kurs = '';
		$flag_saldo_akhir = '';
		
		$total_hutang_rp = 0;
		$total_hutang_gr = 0;
		foreach($kas_account as $ka){
			$saldo_akhir_kurs = 0;
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttd->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttd->total_mutasi;
				}
			}
		
			foreach($saldo_akhir_k as $mttk){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttk->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttk->total_mutasi;
				}
			}	
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs = FALSE;
			$flag_saldo_akhir = FALSE;
			
			if($saldo_akhir_kurs != 0){
				$flag_kurs = TRUE;
				$flag_saldo_akhir = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs == TRUE){
				$count_data = $count_data + 1;
				
				//$data['view'] .= '<tr><td class="right aligned">'.$count_data.'</td><td>'.$ka->accountname.'</td>';
				
				if($saldo_akhir_kurs != 0 && $saldo_akhir_kurs != '' && $saldo_akhir_kurs != -0){
					
					//$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs/$harga_emas, 2).'</td>';
				}else{
					//$data['view'] .= '<td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td>';
				}
				
				//$data['view'] .= '</tr>';
				
				$total_hutang_rp = $total_hutang_rp + $saldo_akhir_kurs;
				$total_hutang_gr = $total_hutang_gr + ($saldo_akhir_kurs/$harga_emas);
			}
		}
		
		//$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL HUTANG</td><td class="td-total"></td><td class="td-total right aligned">'.number_format($total_hutang_rp, 0).'</td><td class="td-total right aligned">'.number_format($total_hutang_gr, 3).'</td></tr>';
		
		//$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		//$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">MODAL KERJA</td><td class="td-total"></td><td class="td-total right aligned">'.number_format(($total24*$harga_emas) + $total_kasbank_rp + $total_piutang_rp - $total_hutang_rp, 0).'</td><td class="td-total right aligned">'.number_format($total24 + $total_kasbank_gr + $total_piutang_gr - $total_hutang_gr, 3).'</td></tr>';
		
		$modal_kerja_rp_kemarin = ($total24*$harga_emas) + $total_kasbank_rp + $total_piutang_rp - $total_hutang_rp;
		$modal_kerja_gr_kemarin = $total24 + $total_kasbank_gr + $total_piutang_gr - $total_hutang_gr;
		
		$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">MODAL KERJA KEMARIN</td><td class="td-total"></td><td class="td-total right aligned"></td><td class="td-total right aligned">'.number_format($modal_kerja_gr_kemarin, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">LABA/RUGI</td><td class="td-total"></td><td class="td-total right aligned"></td><td class="td-total right aligned">'.number_format($modal_kerja_gr - $modal_kerja_gr_kemarin, 3).'</td></tr>';
		
		$data['view'] .= '</tbody></table></div></div>';
		
		$data['viewkiri'] = $data['view'];
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function report_to_pdf(){
		$active_date = $this->mt->get_tanggal_aktif($GLOBALS['kasir']);
		$per_tanggal = $active_date[0]->tanggal_aktif;
		$per_tanggal = strtotime($per_tanggal);
		$per_tanggal = date('Y-m-d',$per_tanggal).' 23:59:59';
		$tanggal_aktif = $active_date[0]->tanggal_aktif;
		
		$tanggal_tulis = strtotime($tanggal_aktif);
		$bulan = date('m',$tanggal_tulis);
		
		switch($bulan){
			case "1":
				$aktif_bulan = 'Januari';
				break;
			case "2":
				$aktif_bulan = 'Februari';
				break;
			case "3":
				$aktif_bulan = 'Maret';
				break;
			case "4":
				$aktif_bulan = 'April';
				break;
			case "5":
				$aktif_bulan = 'Mei';
				break;
			case "6":
				$aktif_bulan = 'Juni';
				break;
			case "7":
				$aktif_bulan = 'Juli';
				break;
			case "8":
				$aktif_bulan = 'Agustus';
				break;
			case "9":
				$aktif_bulan = 'September';
				break;
			case "10":
				$aktif_bulan = 'Oktober';
				break;
			case "11":
				$aktif_bulan = 'November';
				break;
			case "12":
				$aktif_bulan = 'Desember';
				break;
		}
		
		$tanggal = date('d',$tanggal_tulis);
		$tahun = date('Y',$tanggal_tulis);
		
		$hari = date('D',$tanggal_tulis);
		$tanggal_tulis = $tanggal.' '.$aktif_bulan.' '.$tahun;
		
		switch($hari){
			case "Mon":
				$hari_tulis = 'Senin';
				break;
			case "Tue":
				$hari_tulis = 'Selasa';
				break;
			case "Wed":
				$hari_tulis = 'Rabu';
				break;
			case "Thu":
				$hari_tulis = 'Kamis';
				break;
			case "Fri":
				$hari_tulis = 'Jumat';
				break;
			case "Sat":
				$hari_tulis = 'Sabtu';
				break;
			case "Sun":
				$hari_tulis = 'Minggu';
				break;
		}
		
		$filter_box_from = 1;
		$filter_box_to = 999;
		$detail_rekap = 'R';
		$box_karat = 'K';
		
		if($detail_rekap == 'R'){
			$dr = 'Rekap';
		}else{
			$dr = 'Detail';
		}
		
		if($box_karat == 'B'){
			$bk = 'Box';
		}else{
			$bk = 'Karat';
		}
		
		$sitename = $this->mm->get_site_name();
		$harga_emas = $this->mt->get_do_by_date($tanggal_aktif);
		
		$array_box = array();
		
		$data['header'] = '<table class="lap_pdf_neraca" cellspacing="0" width="100%"><thead><tr><th style="text-align:left;width:25%"><img src="'.base_url().'assets/images/branding/brand.png" style="width:200px"></th><th style="text-align:center;font-size:12px;text-transform:uppercase;width:50%">Laporan Neraca Likuiditas <br>Cabang '.$sitename.'</th><th style="text-align:right;font-size:12px;width:25%">'.$hari_tulis.', '.$tanggal_tulis.'<br>Harga Emas : '.number_format($harga_emas, 0).'</th></tr><tr><th colspan="6"><span style="visibility:hidden">-</span></th></tr><tr><th colspan="6"></th></tr></thead><tbody></tbody></table>';
		
		$data['view'] = '<div class="ui stackable grid"><div class="sixteen wide centered column full-print"><table class="lap_pdf_neraca"  cellspacing="0" style="width:100%"><thead><tr><th colspan="5" style="text-align:left;text-transform:uppercase;">Emas Pajangan</th></tr><tr><th class="th-border" style="width:30px">No</th><th class="th-border">Karat</th><th class="th-border">Pcs</th><th class="th-border">Gram</th><th class="th-border">24K</th></tr></thead><tbody>';
		
		$filter_product_pindah_box = '';
			
		$pindah_box = $this->mp->get_posisi_pindah_box($per_tanggal,$filter_box_from,$filter_box_to);
		if(count($pindah_box) == 0){
			$filter_product_pindah_box .= '""';
		}else{
			for($i=0; $i<count($pindah_box); $i++){
				if($i == 0){
					$filter_product_pindah_box .= '"'.$pindah_box[$i]->id_product.'"';
				}else{
					$filter_product_pindah_box .= ',"'.$pindah_box[$i]->id_product.'"';
				}
			}
		}
		
		$data_filter = $this->mp->get_posisi_detail_pajangan($per_tanggal,$filter_box_from,$filter_box_to,$filter_product_pindah_box);
		
		foreach($data_filter as $d){
			$array_box[$d->id] = $d->id_box;
		}
		
		foreach($pindah_box as $p){
			$array_box[$p->id_product] = $p->id_box_from;
		}
		
		$data_karat = $this->mk->get_karat_srt();
		$number = 0;
		$total_weight = 0;
		$total_konversi = 0;
		$count_data_total = 0;
		
		foreach($data_karat as $dk){
			$view_temp = '';
			$count_data_karat = 0;
			$total_weight_karat = 0;
			foreach($data_filter as $df){
				if($df->id_karat == $dk->id){
					if($array_box[$df->id] >= $filter_box_from && $array_box[$df->id] <= $filter_box_to){
						$count_data_karat = $count_data_karat + 1;
						$total_weight_karat = $total_weight_karat + $df->product_weight;
					}
					
				}
			}
			
			if($count_data_karat > 0){
				$number = $number + 1;

				$view_temp .= '<tr><td class="right-aligned">'.$number.'</td><td>'.$dk->karat_name.'</td><td class="right-aligned">'.number_format($count_data_karat, 0).'</td><td class="right-aligned">'.number_format($total_weight_karat, 3).'</td><td class="right-aligned">'.number_format($total_weight_karat * $dk->kali_laporan, 3).'</td></tr>';
				
				$total_weight = $total_weight + $total_weight_karat;
				$total_konversi = $total_konversi + ($total_weight_karat * $dk->kali_laporan);
				$count_data_total = $count_data_total + $count_data_karat;
				
				$data['view'] .= $view_temp;
			}
		}
		
		$total_pajangan24 = $total_konversi;
		$data['view'] .= '<tr><td colspan="2" class="td-total right-aligned">TOTAL</td><td class="td-total right-aligned">'.number_format($count_data_total, 0).'</td><td class="td-total right-aligned">'.number_format($total_weight, 3).'</td><td class="td-total right-aligned">'.number_format($total_konversi, 3).'</td></tr>';
		
		
		$data['view'] .= '<tr><td colspan="5" class="th-border">Reparasi Toko</td></tr>';
		$coa_from = 170001;
		$coa_to = 170001;
		
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		$count_data = 0;
		$total_gram = 0;
		$total_konversi = 0;
		foreach($data_karat as $k){
			$flag_kurs = array();
			$flag_saldo_akhir = array();
			
			$sum_eb = 0;
			
			foreach($kas_account as $ka){
				$saldo_akhir_kurs = array();
				$acc_number = $ka->accountnumber;
				$acc_group = $ka->accountgroup;
				$id_kurs = $k->id;
				$type = $ka->type;
				
				/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
				
				$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
				
				/*--------------------------------------------------*/
				
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_akhir_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$per_tanggal,$id_kurs);
				$saldo_akhir_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$per_tanggal,$id_kurs);
				
				foreach($saldo_akhir_d as $mttd){
					$saldo_akhir_kurs[$mttd->idkarat] = $saldo_akhir_kurs[$mttd->idkarat] + $mttd->total_mutasi;
				}
			
				foreach($saldo_akhir_k as $mttk){
					$saldo_akhir_kurs[$mttk->idkarat] = $saldo_akhir_kurs[$mttk->idkarat] - $mttk->total_mutasi;
				}	
				
				/*---------------------------------------------------*/
				
				/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
				
				$flag_kurs[$k->id] = FALSE;
				$flag_saldo_akhir[$k->id] = FALSE;
				
				if($saldo_akhir_kurs[$k->id] != 0){
					$flag_kurs[$k->id] = TRUE;
					$flag_saldo_akhir[$k->id] = TRUE;
				}
				
				/*-------- Menampilkan Data Dalam Tabel Report -------*/
				
				if($flag_kurs[$k->id] == TRUE){
					$count_data = $count_data + 1;
					$total_gram = $total_gram + $saldo_akhir_kurs[$k->id];
					$total_konversi = $total_konversi + ($saldo_akhir_kurs[$k->id]*$k->kali_laporan);
					
					$data['view'] .= '<tr><td class="right-aligned">'.$count_data.'</td><td>'.$k->karat_name.'</td><td></td>';
					$sak = $saldo_akhir_kurs[$k->id];
					$data['view'] .= '<td class="right-aligned">'.number_format($saldo_akhir_kurs[$k->id], 3).'</td><td class="right-aligned">'.number_format($saldo_akhir_kurs[$k->id]*$k->kali_laporan, 3).'</td>';
					$data['view'] .= '</tr>';
				}
			}
		}
		
		$total_reptoko24 = $total_konversi;
		$data['view'] .= '<tr><td colspan="2" class="td-total right-aligned">TOTAL</td><td class="td-total"></td><td class="td-total right-aligned">'.number_format($total_gram, 3).'</td><td class="td-total right-aligned">'.number_format($total_konversi, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="5" class="th-border-top">Departemen Reparasi</td></tr>';
		$coa_from = 170002;
		$coa_to = 170002;
		
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		if($coa_from == '170002'){
			$tabel_name = 'gold_mutasi_reparasi';
		}else if($coa_from == '170003'){
			$tabel_name = 'gold_mutasi_pengadaan';
		}
		
		$count_data = 0;
		foreach($kas_account as $ka){
			$flag_kurs = array();
			$flag_saldo_akhir = array();
			$saldo_akhir_kurs = array();
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			$id_kurs = 1;
			$type = $ka->type;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] + $mttd->total_mutasi;
			}
			
			foreach($saldo_akhir_k as $mttk){
				$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] - $mttk->total_mutasi;
			}
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs[$id_kurs] = FALSE;
			$flag_saldo_akhir[$id_kurs] = FALSE;
			
			if($saldo_akhir_kurs[$id_kurs] != 0){
				$flag_kurs[$id_kurs] = TRUE;
				$flag_saldo_akhir[$id_kurs] = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			$count_data = $count_data + 1;
			$data['view'] .= '<tr><td class="right-aligned th-border-bottom" colspan="2">TOTAL</td><td class="th-border-bottom"></td><td class="th-border-bottom"></td>';
			$data['view'] .= '<td class="right-aligned th-border-bottom">'.number_format($saldo_akhir_kurs[$id_kurs], 3).'</td>';
			$data['view'] .= '</tr>';
			
			$total_deprep24 = $saldo_akhir_kurs[$id_kurs];
		}
		
		$data['view'] .= '<tr><td colspan="5" class="th-no-border">Departemen Pengadaan</td></tr>';
		$coa_from = 170003;
		$coa_to = 170003;
		
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		if($coa_from == '170002'){
			$tabel_name = 'gold_mutasi_reparasi';
		}else if($coa_from == '170003'){
			$tabel_name = 'gold_mutasi_pengadaan';
		}
		
		$count_data = 0;
		foreach($kas_account as $ka){
			$flag_kurs = array();
			$flag_saldo_akhir = array();
			$saldo_akhir_kurs = array();
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			$id_kurs = 1;
			$type = $ka->type;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] + $mttd->total_mutasi;
			}
			
			foreach($saldo_akhir_k as $mttk){
				$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] - $mttk->total_mutasi;
			}
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs[$id_kurs] = FALSE;
			$flag_saldo_akhir[$id_kurs] = FALSE;
			
			if($saldo_akhir_kurs[$id_kurs] != 0){
				$flag_kurs[$id_kurs] = TRUE;
				$flag_saldo_akhir[$id_kurs] = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/

			$count_data = $count_data + 1;
			$data['view'] .= '<tr><td class="right-aligned th-border-bottom" colspan="2">TOTAL</td><td class="th-border-bottom"></td><td class="th-border-bottom"></td>';
			$data['view'] .= '<td class="right-aligned th-border-bottom">'.number_format($saldo_akhir_kurs[$id_kurs], 3).'</td>';
			$data['view'] .= '</tr>';

			$total_depgrs24 = $saldo_akhir_kurs[$id_kurs];
		}
		
		$data['view'] .= '<tr><td colspan="5" class="th-no-border">Titipan Pelanggan</td></tr>';
		
		$coa_from = 220001;
		$coa_to = 229999;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		$total_fisik = 0;
		$total_titipan24 = 0;
		
		if(count($kas_account) > 0){
			$total_titipan = 0;
			
			foreach($kas_account as $ka){
				$saldo_awal_kurs = array();
				
				$acc_number = $ka->accountnumber;
				$id_kurs = 1;
				
				$type = $ka->type;
				
				$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance($acc_number);
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_bykarat($acc_number,$per_tanggal,$id_kurs);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_bykarat($acc_number,$per_tanggal,$id_kurs);
				
				foreach($saldo_awal_d as $mtyd){
					$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] - $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] + $mtyk->total_mutasi;
				}
				
				$total_titipan = $total_titipan + $saldo_awal_kurs[$id_kurs];				
			}
			
			$data['view'] .= '<tr><td class="right-aligned th-border-bottom" colspan="2">TOTAL</td><td class="th-border-bottom"></td><td class="th-border-bottom"></td><td class="right-aligned th-border-bottom">'.number_format($total_titipan, 3).'</td></tr>';
			
			$total_titipan24 = $total_titipan;
		}
		
		$total24 = $total_pajangan24 + $total_reptoko24 + $total_deprep24 + $total_depgrs24 - $total_titipan24;
		$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '<tr><td class="right-aligned th-border" colspan="2">TOTAL EMAS</td><td class="th-border"></td><td class="th-border"></td><td class="right-aligned th-border">'.number_format($total24, 3).'</td></tr>';
		
		$data['view'] .= '</tbody></table></div></div>';
		
		$data['viewkanan'] = $data['view'];
		
		$data['view'] = '<div class="ui stackable grid"><div class="sixteen wide centered column full-print"><table  class="lap_pdf_neraca" cellspacing="0" style="width:100%"><thead style="text-align:center"><tr><th style="width:30" class="th-border">No</th><th class="th-border">Account</th><th class="th-border">Nilai</th><th class="th-border">Rupiah</th><th class="th-border">24K</th></tr><tr><th colspan="5" style="font-weight:bold;text-align:left;text-transform:uppercase">Kas dan Bank</th></tr></thead><tbody>';
		
		$coa_from = 110001;
		$coa_to = 139999;
		
		$kas_account = $this->mm->get_coa_rp_by_range($coa_from, $coa_to);
		
		$count_data = 0;
		
		$flag_tulis = TRUE;
		$flag_kurs = '';
		$flag_saldo_akhir = '';
		
		$total_kasbank_rp = 0;
		$total_kasbank_gr = 0;
		foreach($kas_account as $ka){
			$saldo_akhir_kurs = 0;
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttd->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttd->total_mutasi;
				}
			}
		
			foreach($saldo_akhir_k as $mttk){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttk->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttk->total_mutasi;
				}
			}	
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs = FALSE;
			$flag_saldo_akhir = FALSE;
			
			if($saldo_akhir_kurs != 0){
				$flag_kurs = TRUE;
				$flag_saldo_akhir = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs == TRUE){
				$count_data = $count_data + 1;
				
				$data['view'] .= '<tr><td class="right-aligned">'.$count_data.'</td><td>'.$ka->accountname.'</td>';
				
				if($saldo_akhir_kurs != 0 && $saldo_akhir_kurs != '' && $saldo_akhir_kurs != -0){
					
					$data['view'] .= '<td class="right-aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right-aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right-aligned">'.number_format($saldo_akhir_kurs/$harga_emas, 3).'</td>';
				}else{
					$data['view'] .= '<td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td>';
				}
				
				$data['view'] .= '</tr>';
			}
			
			$total_kasbank_rp = $total_kasbank_rp + $saldo_akhir_kurs;
			$total_kasbank_gr = $total_kasbank_gr + ($saldo_akhir_kurs/$harga_emas);
		}
		
		$data['view'] .= '<tr><td colspan="2" class="td-total">TOTAL KAS & BANK</td><td class="td-total"></td><td class="td-total right-aligned">'.number_format($total_kasbank_rp, 0).'</td><td class="td-total right-aligned">'.number_format($total_kasbank_gr, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="5" class="th-border-top">Piutang Usaha</td></tr>';
		
		$coa_from = 140001;
		$coa_to = 179999;
		
		$kas_account = $this->mm->get_coa_rp_by_range($coa_from, $coa_to);
		
		$count_data = 0;
		
		$flag_tulis = TRUE;
		$flag_kurs = '';
		$flag_saldo_akhir = '';
		
		$total_piutang_rp = 0;
		$total_piutang_gr = 0;
		foreach($kas_account as $ka){
			$saldo_akhir_kurs = 0;
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttd->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttd->total_mutasi;
				}
			}
		
			foreach($saldo_akhir_k as $mttk){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttk->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttk->total_mutasi;
				}
			}	
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs = FALSE;
			$flag_saldo_akhir = FALSE;
			
			if($saldo_akhir_kurs != 0){
				$flag_kurs = TRUE;
				$flag_saldo_akhir = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs == TRUE){
				$count_data = $count_data + 1;
				
				$data['view'] .= '<tr><td class="right-aligned">'.$count_data.'</td><td>'.$ka->accountname.'</td>';
				
				if($saldo_akhir_kurs != 0 && $saldo_akhir_kurs != '' && $saldo_akhir_kurs != -0){
					
					$data['view'] .= '<td class="right-aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right-aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right-aligned">'.number_format($saldo_akhir_kurs/$harga_emas, 2).'</td>';
				}else{
					$data['view'] .= '<td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td>';
				}
				
				$data['view'] .= '</tr>';
				$total_piutang_rp = $total_piutang_rp + $saldo_akhir_kurs;
				$total_piutang_gr = $total_piutang_gr + ($saldo_akhir_kurs/$harga_emas);
			}
		}
		
		$data['view'] .= '<tr><td colspan="2" class="th-border-bottom td-total">TOTAL PIUTANG USAHA</td><td class="th-border-bottom td-total"></td><td class="th-border-bottom td-total right-aligned">'.number_format($total_piutang_rp, 0).'</td><td class="th-border-bottom td-total right-aligned">'.number_format($total_piutang_gr, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '<tr><td colspan="2" class="th-border">HARTA</td><td class="th-border"></td><td class="th-border right-aligned">'.number_format(($total24*$harga_emas) + $total_kasbank_rp + $total_piutang_rp, 0).'</td><td class="th-border right-aligned">'.number_format($total24 + $total_kasbank_gr + $total_piutang_gr, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '<tr><td colspan="5" class="th-border-top">Hutang/Kewajiban</td></tr>';
		
		$coa_from = 210001;
		$coa_to = 239999;
		
		$kas_account = $this->mm->get_coa_rp_by_range($coa_from, $coa_to);
		
		$count_data = 0;
		
		$flag_tulis = TRUE;
		$flag_kurs = '';
		$flag_saldo_akhir = '';
		
		$total_hutang_rp = 0;
		$total_hutang_gr = 0;
		foreach($kas_account as $ka){
			$saldo_akhir_kurs = 0;
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttd->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttd->total_mutasi;
				}
			}
		
			foreach($saldo_akhir_k as $mttk){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttk->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttk->total_mutasi;
				}
			}	
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs = FALSE;
			$flag_saldo_akhir = FALSE;
			
			if($saldo_akhir_kurs != 0){
				$flag_kurs = TRUE;
				$flag_saldo_akhir = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs == TRUE){
				$count_data = $count_data + 1;
				
				$data['view'] .= '<tr><td class="right-aligned">'.$count_data.'</td><td>'.$ka->accountname.'</td>';
				
				if($saldo_akhir_kurs != 0 && $saldo_akhir_kurs != '' && $saldo_akhir_kurs != -0){
					
					$data['view'] .= '<td class="right-aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right-aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right-aligned">'.number_format($saldo_akhir_kurs/$harga_emas, 2).'</td>';
				}else{
					$data['view'] .= '<td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td>';
				}
				
				$data['view'] .= '</tr>';
				
				$total_hutang_rp = $total_hutang_rp + $saldo_akhir_kurs;
				$total_hutang_gr = $total_hutang_gr + ($saldo_akhir_kurs/$harga_emas);
			}
		}
		
		$data['view'] .= '<tr><td colspan="2" class="th-border-bottom td-total">TOTAL HUTANG/KEWAJIBAN</td><td class="th-border-bottom td-total"></td><td class="th-border-bottom right-aligned td-total">'.number_format($total_hutang_rp, 0).'</td><td class="th-border-bottom right-aligned td-total">'.number_format($total_hutang_gr, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		$modal_kerja_rp = ($total24*$harga_emas) + $total_kasbank_rp + $total_piutang_rp - $total_hutang_rp;
		$modal_kerja_gr = $total24 + $total_kasbank_gr + $total_piutang_gr - $total_hutang_gr;
		
		$data['view'] .= '<tr><td colspan="2" class="th-border">MODAL KERJA</td><td class="th-border"></td><td class="th-border right-aligned">'.number_format(($total24*$harga_emas) + $total_kasbank_rp + $total_piutang_rp - $total_hutang_rp, 0).'</td><td class="th-border right-aligned">'.number_format($total24 + $total_kasbank_gr + $total_piutang_gr - $total_hutang_gr, 3).'</td></tr>';
		
		$active_date = $this->mt->get_tanggal_aktif($GLOBALS['kasir']);
		$per_tanggal = $active_date[0]->tanggal_aktif;
		$tanggal_aktif = date('Y-m-d',strtotime($per_tanggal. "-1 days")).' 00:00:00';
		$per_tanggal = date('Y-m-d',strtotime($per_tanggal. "-1 days")).' 23:59:59';
		
		$filter_box_from = 1;
		$filter_box_to = 999;
		$detail_rekap = 'R';
		$box_karat = 'K';
		
		if($detail_rekap == 'R'){
			$dr = 'Rekap';
		}else{
			$dr = 'Detail';
		}
		
		if($box_karat == 'B'){
			$bk = 'Box';
		}else{
			$bk = 'Karat';
		}
		
		$array_box = array();
		
		//$data['view'] = '<div class="ui stackable grid"><div class="sixteen wide centered column full-print"><table id="filter_data_report" class="ui celled table" style="width:100%"><thead style="text-align:center"><tr><th colspan="5" style="text-align:left;">Emas Pajangan</th></tr><tr><th style="width:50px">No</th><th>Karat</th><th>Pcs</th><th>Gram</th><th>24K</th></tr></thead><tbody>';
		
		$filter_product_pindah_box = '';
			
		$pindah_box = $this->mp->get_posisi_pindah_box($per_tanggal,$filter_box_from,$filter_box_to);
		if(count($pindah_box) == 0){
			$filter_product_pindah_box .= '""';
		}else{
			for($i=0; $i<count($pindah_box); $i++){
				if($i == 0){
					$filter_product_pindah_box .= '"'.$pindah_box[$i]->id_product.'"';
				}else{
					$filter_product_pindah_box .= ',"'.$pindah_box[$i]->id_product.'"';
				}
			}
		}
		
		$data_filter = $this->mp->get_posisi_detail_pajangan($per_tanggal,$filter_box_from,$filter_box_to,$filter_product_pindah_box);
		
		foreach($data_filter as $d){
			$array_box[$d->id] = $d->id_box;
		}
		
		foreach($pindah_box as $p){
			$array_box[$p->id_product] = $p->id_box_from;
		}
		
		$data_karat = $this->mk->get_karat_srt();
		$number = 0;
		$total_weight = 0;
		$total_konversi = 0;
		$count_data_total = 0;
		
		foreach($data_karat as $dk){
			$view_temp = '';
			$count_data_karat = 0;
			$total_weight_karat = 0;
			foreach($data_filter as $df){
				if($df->id_karat == $dk->id){
					if($array_box[$df->id] >= $filter_box_from && $array_box[$df->id] <= $filter_box_to){
						$count_data_karat = $count_data_karat + 1;
						$total_weight_karat = $total_weight_karat + $df->product_weight;
					}
					
				}
			}
			
			if($count_data_karat > 0){
				$number = $number + 1;

				//$view_temp .= '<tr><td class="right aligned">'.$number.'</td><td>'.$dk->karat_name.'</td><td class="right aligned">'.number_format($count_data_karat, 0).'</td><td class="right aligned">'.number_format($total_weight_karat, 3).'</td><td class="right aligned">'.number_format($total_weight_karat * $dk->kali_laporan, 3).'</td></tr>';
				
				$total_weight = $total_weight + $total_weight_karat;
				$total_konversi = $total_konversi + ($total_weight_karat * $dk->kali_laporan);
				$count_data_total = $count_data_total + $count_data_karat;
				
				//$data['view'] .= $view_temp;
			}
		}
		
		$total_pajangan24 = $total_konversi;
		//$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL</td><td class="td-total right aligned">'.number_format($count_data_total, 0).'</td><td class="td-total right aligned">'.number_format($total_weight, 3).'</td><td class="td-total right aligned">'.number_format($total_konversi, 3).'</td></tr>';
		
		
		//$data['view'] .= '<tr><td colspan="5" class="theader">Reparasi Toko</td></tr>';
		$coa_from = 170001;
		$coa_to = 170001;
		
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		$count_data = 0;
		$total_gram = 0;
		$total_konversi = 0;
		foreach($data_karat as $k){
			$flag_kurs = array();
			$flag_saldo_akhir = array();
			
			$sum_eb = 0;
			
			foreach($kas_account as $ka){
				$saldo_akhir_kurs = array();
				$acc_number = $ka->accountnumber;
				$acc_group = $ka->accountgroup;
				$id_kurs = $k->id;
				$type = $ka->type;
				
				/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
				
				$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
				
				/*--------------------------------------------------*/
				
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_akhir_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$per_tanggal,$id_kurs);
				$saldo_akhir_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$per_tanggal,$id_kurs);
				
				foreach($saldo_akhir_d as $mttd){
					$saldo_akhir_kurs[$mttd->idkarat] = $saldo_akhir_kurs[$mttd->idkarat] + $mttd->total_mutasi;
				}
			
				foreach($saldo_akhir_k as $mttk){
					$saldo_akhir_kurs[$mttk->idkarat] = $saldo_akhir_kurs[$mttk->idkarat] - $mttk->total_mutasi;
				}	
				
				/*---------------------------------------------------*/
				
				/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
				
				$flag_kurs[$k->id] = FALSE;
				$flag_saldo_akhir[$k->id] = FALSE;
				
				if($saldo_akhir_kurs[$k->id] != 0){
					$flag_kurs[$k->id] = TRUE;
					$flag_saldo_akhir[$k->id] = TRUE;
				}
				
				/*-------- Menampilkan Data Dalam Tabel Report -------*/
				
				if($flag_kurs[$k->id] == TRUE){
					$count_data = $count_data + 1;
					$total_gram = $total_gram + $saldo_akhir_kurs[$k->id];
					$total_konversi = $total_konversi + ($saldo_akhir_kurs[$k->id]*$k->kali_laporan);
					
					//$data['view'] .= '<tr><td class="right aligned">'.$count_data.'</td><td>'.$k->karat_name.'</td><td></td>';
					$sak = $saldo_akhir_kurs[$k->id];
					//$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs[$k->id], 3).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs[$k->id]*$k->kali_laporan, 3).'</td>';
					//$data['view'] .= '</tr>';
				}
			}
		}
		
		$total_reptoko24 = $total_konversi;
		//$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL</td><td class="td-total"></td><td class="td-total right aligned">'.number_format($total_gram, 3).'</td><td class="td-total right aligned">'.number_format($total_konversi, 3).'</td></tr>';
		
		//$data['view'] .= '<tr><td colspan="5" class="theader">Departemen Reparasi</td></tr>';
		$coa_from = 170002;
		$coa_to = 170002;
		
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		if($coa_from == '170002'){
			$tabel_name = 'gold_mutasi_reparasi';
		}else if($coa_from == '170003'){
			$tabel_name = 'gold_mutasi_pengadaan';
		}
		
		$count_data = 0;
		foreach($kas_account as $ka){
			$flag_kurs = array();
			$flag_saldo_akhir = array();
			$saldo_akhir_kurs = array();
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			$id_kurs = 1;
			$type = $ka->type;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] + $mttd->total_mutasi;
			}
			
			foreach($saldo_akhir_k as $mttk){
				$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] - $mttk->total_mutasi;
			}
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs[$id_kurs] = FALSE;
			$flag_saldo_akhir[$id_kurs] = FALSE;
			
			if($saldo_akhir_kurs[$id_kurs] != 0){
				$flag_kurs[$id_kurs] = TRUE;
				$flag_saldo_akhir[$id_kurs] = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs[$id_kurs] == TRUE){
				$count_data = $count_data + 1;
				//$data['view'] .= '<tr><td class="right aligned td-total" colspan="2">TOTAL</td><td class="td-total"></td><td class="td-total"></td>';
				//$data['view'] .= '<td class="right aligned td-total">'.number_format($saldo_akhir_kurs[$id_kurs], 3).'</td>';
				//$data['view'] .= '</tr>';
			}
			
			$total_deprep24 = $saldo_akhir_kurs[$id_kurs];
		}
		
		//$data['view'] .= '<tr><td colspan="5" class="theader">Departemen Pengadaan</td></tr>';
		$coa_from = 170003;
		$coa_to = 170003;
		
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		if($coa_from == '170002'){
			$tabel_name = 'gold_mutasi_reparasi';
		}else if($coa_from == '170003'){
			$tabel_name = 'gold_mutasi_pengadaan';
		}
		
		$count_data = 0;
		foreach($kas_account as $ka){
			$flag_kurs = array();
			$flag_saldo_akhir = array();
			$saldo_akhir_kurs = array();
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			$id_kurs = 1;
			$type = $ka->type;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] + $mttd->total_mutasi;
			}
			
			foreach($saldo_akhir_k as $mttk){
				$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] - $mttk->total_mutasi;
			}
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs[$id_kurs] = FALSE;
			$flag_saldo_akhir[$id_kurs] = FALSE;
			
			if($saldo_akhir_kurs[$id_kurs] != 0){
				$flag_kurs[$id_kurs] = TRUE;
				$flag_saldo_akhir[$id_kurs] = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs[$id_kurs] == TRUE){
				$count_data = $count_data + 1;
				//$data['view'] .= '<tr><td class="right aligned td-total" colspan="2">TOTAL</td><td class="td-total"></td><td class="td-total"></td>';
				//$data['view'] .= '<td class="right aligned td-total">'.number_format($saldo_akhir_kurs[$id_kurs], 3).'</td>';
				//$data['view'] .= '</tr>';
			}
			
			$total_depgrs24 = $saldo_akhir_kurs[$id_kurs];
		}
		
		//$data['view'] .= '<tr><td colspan="5" class="theader">Titipan Pelanggan</td></tr>';
		
		$coa_from = 220001;
		$coa_to = 229999;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		$total_fisik = 0;
		
		if(count($kas_account) > 0){
			$total_titipan = 0;
			
			foreach($kas_account as $ka){
				$saldo_awal_kurs = array();
				
				$acc_number = $ka->accountnumber;
				$id_kurs = 1;
				
				$type = $ka->type;
				
				$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance($acc_number);
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_bykarat($acc_number,$per_tanggal,$id_kurs);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_bykarat($acc_number,$per_tanggal,$id_kurs);
				
				foreach($saldo_awal_d as $mtyd){
					$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] - $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] + $mtyk->total_mutasi;
				}
				
				$total_titipan = $total_titipan + $saldo_awal_kurs[$id_kurs];				
			}
			
			//$data['view'] .= '<td class="right aligned td-total" colspan="2">TOTAL KONVERSI 24K</td><td class="td-total"></td><td class="td-total"></td><td class="right aligned td-total">'.number_format($total_titipan, 3).'</td></tr>';
			
			$total_titipan24 = $total_titipan;
		}
		
		$total24 = $total_pajangan24 + $total_reptoko24 + $total_deprep24 + $total_depgrs24 - $total_titipan24;
		//$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		//$data['view'] .= '<td class="right aligned td-total" colspan="2">TOTAL EMAS</td><td class="td-total"></td><td class="td-total"></td><td class="right aligned td-total">'.number_format($total24, 3).'</td></tr>';
		
		//$data['view'] .= '</tbody></table></div></div>';
		
		//$data['viewkanan'] = $data['view'];
		
		//$data['view'] = '<div class="ui stackable grid"><div class="sixteen wide centered column full-print"><table id="filter_data_report_2" class="ui celled table" style="width:100%"><thead style="text-align:center"><tr><th colspan="5">Kas dan Bank</th></tr><tr><th style="width:50px">No</th><th>Account</th><th>Nilai</th><th>Rupiah</th><th>24K</th></tr></thead><tbody>';
		
		//echo $tanggal_aktif.'<br>';
		$harga_emas = $this->mt->get_do_by_date($tanggal_aktif);
		//echo $harga_emas.'<br>';
		$coa_from = 110001;
		$coa_to = 139999;
		
		$kas_account = $this->mm->get_coa_rp_by_range($coa_from, $coa_to);
		
		$count_data = 0;
		
		$flag_tulis = TRUE;
		$flag_kurs = '';
		$flag_saldo_akhir = '';
		
		$total_kasbank_rp = 0;
		$total_kasbank_gr = 0;
		foreach($kas_account as $ka){
			$saldo_akhir_kurs = 0;
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttd->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttd->total_mutasi;
				}
			}
		
			foreach($saldo_akhir_k as $mttk){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttk->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttk->total_mutasi;
				}
			}	
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs = FALSE;
			$flag_saldo_akhir = FALSE;
			
			if($saldo_akhir_kurs != 0){
				$flag_kurs = TRUE;
				$flag_saldo_akhir = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs == TRUE){
				$count_data = $count_data + 1;
				
				//$data['view'] .= '<tr><td class="right aligned">'.$count_data.'</td><td>'.$ka->accountname.'</td>';
				
				if($saldo_akhir_kurs != 0 && $saldo_akhir_kurs != '' && $saldo_akhir_kurs != -0){
					
					//$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs/$harga_emas, 3).'</td>';
				}else{
					//$data['view'] .= '<td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td>';
				}
				
				//$data['view'] .= '</tr>';
			}
			
			$total_kasbank_rp = $total_kasbank_rp + $saldo_akhir_kurs;
			//echo $harga_emas;
			$total_kasbank_gr = $total_kasbank_gr + ($saldo_akhir_kurs/$harga_emas);
		}
		
		//$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL KAS & BANK</td><td class="td-total"></td><td class="td-total right aligned">'.number_format($total_kasbank_rp, 0).'</td><td class="td-total right aligned">'.number_format($total_kasbank_gr, 3).'</td></tr>';
		
		//$data['view'] .= '<tr><td colspan="5" class="theader">Piutang Usaha</td></tr>';
		
		$coa_from = 140001;
		$coa_to = 179999;
		
		$kas_account = $this->mm->get_coa_rp_by_range($coa_from, $coa_to);
		
		$count_data = 0;
		
		$flag_tulis = TRUE;
		$flag_kurs = '';
		$flag_saldo_akhir = '';
		
		$total_piutang_rp = 0;
		$total_piutang_gr = 0;
		foreach($kas_account as $ka){
			$saldo_akhir_kurs = 0;
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttd->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttd->total_mutasi;
				}
			}
		
			foreach($saldo_akhir_k as $mttk){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttk->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttk->total_mutasi;
				}
			}	
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs = FALSE;
			$flag_saldo_akhir = FALSE;
			
			if($saldo_akhir_kurs != 0){
				$flag_kurs = TRUE;
				$flag_saldo_akhir = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs == TRUE){
				$count_data = $count_data + 1;
				
				//$data['view'] .= '<tr><td class="right aligned">'.$count_data.'</td><td>'.$ka->accountname.'</td>';
				
				if($saldo_akhir_kurs != 0 && $saldo_akhir_kurs != '' && $saldo_akhir_kurs != -0){
					
					//$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs/$harga_emas, 2).'</td>';
				}else{
					//$data['view'] .= '<td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td>';
				}
				
				//$data['view'] .= '</tr>';
				$total_piutang_rp = $total_piutang_rp + $saldo_akhir_kurs;
				$total_piutang_gr = $total_piutang_gr + ($saldo_akhir_kurs/$harga_emas);
			}
		}
		
		//$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL PIUTANG</td><td class="td-total"></td><td class="td-total right aligned">'.number_format($total_piutang_rp, 0).'</td><td class="td-total right aligned">'.number_format($total_piutang_gr, 3).'</td></tr>';
		
		//$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		//$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL HARTA</td><td class="td-total"></td><td class="td-total right aligned">'.number_format(($total24*$harga_emas) + $total_kasbank_rp + $total_piutang_rp, 0).'</td><td class="td-total right aligned">'.number_format($total24 + $total_kasbank_gr + $total_piutang_gr, 3).'</td></tr>';
		
		//$data['view'] .= '<tr><td colspan="5" class="td-total"><span style="visibility:hidden">-</span></td></tr>';
		
		//$data['view'] .= '<tr><td colspan="5" class="theader">Hutang/Kewajiban</td></tr>';
		
		$coa_from = 210001;
		$coa_to = 239999;
		
		$kas_account = $this->mm->get_coa_rp_by_range($coa_from, $coa_to);
		
		$count_data = 0;
		
		$flag_tulis = TRUE;
		$flag_kurs = '';
		$flag_saldo_akhir = '';
		
		$total_hutang_rp = 0;
		$total_hutang_gr = 0;
		foreach($kas_account as $ka){
			$saldo_akhir_kurs = 0;
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
			$saldo_akhir_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
			
			/*--------------------------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_akhir_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$per_tanggal);
			$saldo_akhir_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$per_tanggal);
			
			foreach($saldo_akhir_d as $mttd){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttd->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttd->total_mutasi;
				}
			}
		
			foreach($saldo_akhir_k as $mttk){
				if($acc_group == '1' || $acc_group == '5'){
					$saldo_akhir_kurs = $saldo_akhir_kurs - $mttk->total_mutasi;
				}else{
					$saldo_akhir_kurs = $saldo_akhir_kurs + $mttk->total_mutasi;
				}
			}	
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs = FALSE;
			$flag_saldo_akhir = FALSE;
			
			if($saldo_akhir_kurs != 0){
				$flag_kurs = TRUE;
				$flag_saldo_akhir = TRUE;
			}
			
			/*----------------------------------------------------*/
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs == TRUE){
				$count_data = $count_data + 1;
				
				//$data['view'] .= '<tr><td class="right aligned">'.$count_data.'</td><td>'.$ka->accountname.'</td>';
				
				if($saldo_akhir_kurs != 0 && $saldo_akhir_kurs != '' && $saldo_akhir_kurs != -0){
					
					//$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td><td class="right aligned">'.number_format($saldo_akhir_kurs/$harga_emas, 2).'</td>';
				}else{
					//$data['view'] .= '<td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td>';
				}
				
				//$data['view'] .= '</tr>';
				
				$total_hutang_rp = $total_hutang_rp + $saldo_akhir_kurs;
				$total_hutang_gr = $total_hutang_gr + ($saldo_akhir_kurs/$harga_emas);
			}
		}
		
		//$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">TOTAL HUTANG</td><td class="td-total"></td><td class="td-total right aligned">'.number_format($total_hutang_rp, 0).'</td><td class="td-total right aligned">'.number_format($total_hutang_gr, 3).'</td></tr>';
		
		//$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		//$data['view'] .= '<tr><td colspan="2" class="td-total right aligned">MODAL KERJA</td><td class="td-total"></td><td class="td-total right aligned">'.number_format(($total24*$harga_emas) + $total_kasbank_rp + $total_piutang_rp - $total_hutang_rp, 0).'</td><td class="td-total right aligned">'.number_format($total24 + $total_kasbank_gr + $total_piutang_gr - $total_hutang_gr, 3).'</td></tr>';
		
		$modal_kerja_rp_kemarin = ($total24*$harga_emas) + $total_kasbank_rp + $total_piutang_rp - $total_hutang_rp;
		$modal_kerja_gr_kemarin = $total24 + $total_kasbank_gr + $total_piutang_gr - $total_hutang_gr;
		
		$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '<tr><td colspan="2" class="th-border">MODAL KERJA KEMARIN</td><td class="th-border"></td><td class="th-border right-aligned"></td><td class="th-border right-aligned">'.number_format($modal_kerja_gr_kemarin, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '<tr><td colspan="2" class="th-border">LABA/RUGI</td><td class="th-border"></td><td class="th-border right-aligned"></td><td class="th-border right-aligned">'.number_format($modal_kerja_gr - $modal_kerja_gr_kemarin, 3).'</td></tr>';
		
		$data['view'] .= '</tbody></table></div></div>';
		
		$data['viewkiri'] = $data['view'];
		
		$html = $this->load->view("pdf/V_pdf_neraca",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		$pdf = $this->m_pdf->load();
        $pdf->AddPage('P');
        $pdf->WriteHTML($html);
		
        $pdf->Output("Laporan Neraca Likuiditas.pdf", "I");
	}
	
	public function generate_report_kasir(){
		$this->db->trans_start();
		
		$active_date = $this->mt->get_tanggal_aktif($GLOBALS['kasir']);
		$per_tanggal = $active_date[0]->tanggal_aktif;
		$per_tanggal = strtotime($per_tanggal);
		$tgl_from = $active_date[0]->tanggal_aktif;
		$tgl_to = date('Y-m-d',$per_tanggal).' 23:59:59';
		
		$running_balance = 0;
		
		$kas_account = $this->mm->get_single_coa_rp('11-0001');
		foreach($kas_account as $ka){
			$acc_number = $ka->accountnumber;
			$accountnumber = $ka->accountnumber;
			
			/*-- Menentukan Beginning Balance --*/
			$saldo_awal = $this->mm->get_report_rp_beginning_balance($acc_number);
			/*----------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_awal_d = $this->mm->get_report_mutasi_rp_yesterday_d($acc_number,$tgl_from);
			$saldo_awal_k = $this->mm->get_report_mutasi_rp_yesterday_k($acc_number,$tgl_from);
			
			foreach($saldo_awal_d as $mtyd){
				$saldo_awal = $saldo_awal + $mtyd->total_mutasi;
			}
			
			foreach($saldo_awal_k as $mtyk){
				$saldo_awal = $saldo_awal - $mtyk->total_mutasi;
			}
			
			/*---------------------------------------------------*/
			
			/*---------- Mengambil Data Mutasi Transaksi ----------*/
	
			$mutasi_transaksi_jual = $this->mm->get_report_mutasi_rp_jual($tgl_from, $tgl_from, $acc_number);
			$mutasi_transaksi = $this->mm->get_report_mutasi_rp_rekap_lap($tgl_from, $tgl_from, $acc_number);
			$mutasi_transaksi_beli = $this->mm->get_report_mutasi_rp_bycode('BR', $tgl_from, $tgl_from, $acc_number);
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs = FALSE;
			$flag_saldo_awal = FALSE;
			$flag_mutasi = FALSE;
			
			if($saldo_awal != 0){
				$flag_saldo_awal = TRUE;
				$flag_kurs = TRUE;
			}
			
			foreach($mutasi_transaksi_jual as $mk){
				if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
					$flag_mutasi = TRUE;
					$flag_kurs = TRUE;
				}
			}
			
			foreach($mutasi_transaksi as $mk){
				if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
					$flag_mutasi = TRUE;
					$flag_kurs = TRUE;
				}
			}
			
			foreach($mutasi_transaksi_beli as $mk){
				if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
					$flag_mutasi = TRUE;
					$flag_kurs = TRUE;
				}
			}
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs == TRUE){
				$coa_data = $this->mm->get_coa_number_name($ka->accountnumberint);
				
				$running_balance = $saldo_awal;
				$total_debet = 0;
				$total_kredit = 0;
							
				//PENJUALAN
				$total_debet_ind = 0;
				$total_kredit_ind = 0;
				
				foreach($mutasi_transaksi_jual as $mk){
					if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
						if($mk->toaccount == $accountnumber){
							$total_debet_ind = $total_debet_ind + $mk->value;
						}else{
							$total_kredit_ind = $total_kredit_ind + $mk->value;
						}
					}
				}
				
				$running_balance = $running_balance + $total_debet_ind - $total_kredit_ind;
				$total_debet = $total_debet + $total_debet_ind;
				$total_kredit = $total_kredit + $total_kredit_ind;
				
				//---
				
				//KASIN/KASOUT
				$total_debet_ind = 0;
				$total_kredit_ind = 0;
					
				foreach($mutasi_transaksi as $mk){
					if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
						if($mk->toaccount == $accountnumber){
							$total_debet_ind = $total_debet_ind + $mk->value;
						}else{
							$total_kredit_ind = $total_kredit_ind + $mk->value;
						}
					}
				}
				
				$running_balance = $running_balance + $total_debet_ind - $total_kredit_ind;
				$total_debet = $total_debet + $total_debet_ind;
				$total_kredit = $total_kredit + $total_kredit_ind;
				
				//---
				
				//PEMBELIAN
				$total_debet_ind = 0;
				$total_kredit_ind = 0;
				
				foreach($mutasi_transaksi_beli as $mk){
					if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
						if($mk->toaccount == $accountnumber){
							$total_debet_ind = $total_debet_ind + $mk->value;
						}else{
							$total_kredit_ind = $total_kredit_ind + $mk->value;
						}
					}
				}
				
				$running_balance = $running_balance + $total_debet_ind - $total_kredit_ind;
				$total_debet = $total_debet + $total_debet_ind;
				$total_kredit = $total_kredit + $total_kredit_ind;
				
				//---
			}
			
			/*----------------------------------------------------*/
		}
		
		
		
		$data['view'] = '<div class="ui stackable grid"><div class="sixteen wide centered column full-print"><table id="filter_data_report" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th colspan="5">Saldo Awal</th><th style="text-align:right !important">'.number_format($saldo_awal, 2).'</th></tr><tr><th colspan="6"><span style="visibility:hidden">-</span></th></tr><tr><th colspan="6">Data Penjualan</th></tr><tr><th style="width:40px">No</th><th>Karat</th><th style="width:40px">Pcs</th><th style="width:80px">Gram</th><th style="width:120px">Rata2</th><th style="width:140px">Total Jual</th></tr></thead><tbody>';
		
		$number = 1;
		$total_pcs = 0;
		$total_gram = 0;
		$total_jual = 0;
	
		$data_rekap = $this->mt->get_penjualan_rekap_karat($tgl_from,$tgl_to);
		foreach($data_rekap as $dr){
			$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.$dr->karat_name.'</td><td class="right aligned">'.$dr->pcs.'</td><td class="right aligned">'.number_format($dr->berat, 2).'</td><td class="right aligned">'.number_format($dr->harga/$dr->berat, 2).'</td><td class="right aligned">'.number_format($dr->harga, 2).'</td><tr>';
			
			$total_pcs = $total_pcs + $dr->pcs;
			$total_gram = $total_gram + $dr->berat;
			$total_jual = $total_jual + $dr->harga;
			$number = $number + 1;
		}
		
		if($total_pcs != 0){
			$data['view'] .= '<tr><td colspan="2"></td><td class="double-top right aligned">'.$total_pcs.'</td><td class="double-top right aligned">'.number_format($total_gram, 2).'</td><td class="double-top right aligned"></td><td class="double-top td-bold right aligned">'.number_format($total_jual, 2).'</td><tr>';
		}else{
			$data['view'] .= '<tr><td class="center aligned" colspan="6">Tidak Ada Penjualan</td><tr>';
		}
		
		$number = 1;
		$total_pcs = 0;
		$total_gram = 0;
		$total_jual = 0;
		
		$data['view'] .= '<tr><td colspan="6" style="border-left:none;border-right:none"><span style="visibility:hidden">-</span></td></tr><tr><td class="theader" colspan="6">Data Pembelian</th></tr><tr><td class="theader" style="width:40px;">No</td><td class="theader">Karat</td><td class="theader" style="width:40px;">Pcs</td><td class="theader" style="width:80px;">Gram</td><td class="theader" style="width:120px;">Rata2</td><td class="theader" style="width:140px;">Total Beli</td></tr>';
		
		$data_rekap = $this->mt->get_pembelian_rekap_karat($tgl_from,$tgl_to);
		foreach($data_rekap as $dr){
			$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.$dr->karat_name.'</td><td class="right aligned">'.$dr->pcs.'</td><td class="right aligned">'.number_format($dr->berat, 2).'</td><td class="right aligned">'.number_format($dr->harga/$dr->berat, 2).'</td><td class="right aligned">'.number_format($dr->harga, 2).'</td><tr>';
			
			$total_pcs = $total_pcs + $dr->pcs;
			$total_gram = $total_gram + $dr->berat;
			$total_jual = $total_jual + $dr->harga;
			$number = $number + 1;
		}
		
		if($total_pcs != 0){
			$data['view'] .= '<tr><td colspan="2"></td><td class="double-top right aligned">'.$total_pcs.'</td><td class="double-top right aligned">'.number_format($total_gram, 2).'</td><td class="double-top right aligned"></td><td class="double-top td-bold right aligned">'.number_format($total_jual, 2).'</td><tr>';
		}else{
			$data['view'] .= '<tr><td class="center aligned" colspan="6">Tidak Ada Pembelian</td><tr>';
		}
		
		$trans_bank = $this->mt->get_mutasi_jual_bank($tgl_from);
		
		$data['view'] .= '<tr><td colspan="6" style="border-left:none;border-right:none"><span style="visibility:hidden">-</span></td></tr><tr><td class="td-bold" colspan="5">Pembayaran Menggunakan Kartu Bank</td><td class="right aligned">'.number_format($trans_bank, 2).'</td></tr>';
		
		$kas_keluar = $this->mt->get_mutasi_kas_keluar($tgl_from);
		
		$data['view'] .= '<tr><td class="td-bold" colspan="5">Kas Keluar</td><td class="right aligned">'.number_format($kas_keluar, 2).'</td></tr>';
		
		$kas_masuk = $this->mt->get_mutasi_kas_masuk($tgl_from);
		
		$data['view'] .= '<tr><td class="td-bold" colspan="5">Kas Masuk</td><td class="right aligned">'.number_format($kas_masuk, 2).'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="6" style="border-left:none;border-right:none"><span style="visibility:hidden">-</span></td></tr><tr><td class="td-bold" colspan="5">Saldo Akhir</td><td class="right aligned td-bold">'.number_format($running_balance, 2).'</td></tr>';
		
		$data['view'] .= '</tbody></table></div></div>';
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function report_kasir_to_pdf(){
		$active_date = $this->mt->get_tanggal_aktif($GLOBALS['kasir']);
		$tanggal_aktif = $active_date[0]->tanggal_aktif;
		$per_tanggal = $active_date[0]->tanggal_aktif;
		$per_tanggal = strtotime($per_tanggal);
		$tgl_from = $active_date[0]->tanggal_aktif;
		$tgl_to = date('Y-m-d',$per_tanggal).' 23:59:59';
		
		$harga_emas = $this->mt->get_do_by_date($tanggal_aktif);
		$tanggal_aktif = strtotime($tanggal_aktif);
		$hari = date('D',$tanggal_aktif);
		$bulan = date('m',$tanggal_aktif);
		
		switch($hari){
			case "Mon":
				$hari_tulis = 'Senin';
				break;
			case "Tue":
				$hari_tulis = 'Selasa';
				break;
			case "Wed":
				$hari_tulis = 'Rabu';
				break;
			case "Thu":
				$hari_tulis = 'Kamis';
				break;
			case "Fri":
				$hari_tulis = 'Jumat';
				break;
			case "Sat":
				$hari_tulis = 'Sabtu';
				break;
			case "Sun":
				$hari_tulis = 'Minggu';
				break;
		}
		
		switch($bulan){
			case "1":
				$aktif_bulan = 'Januari';
				break;
			case "2":
				$aktif_bulan = 'Februari';
				break;
			case "3":
				$aktif_bulan = 'Maret';
				break;
			case "4":
				$aktif_bulan = 'April';
				break;
			case "5":
				$aktif_bulan = 'Mei';
				break;
			case "6":
				$aktif_bulan = 'Juni';
				break;
			case "7":
				$aktif_bulan = 'Juli';
				break;
			case "8":
				$aktif_bulan = 'Agustus';
				break;
			case "9":
				$aktif_bulan = 'September';
				break;
			case "10":
				$aktif_bulan = 'Oktober';
				break;
			case "11":
				$aktif_bulan = 'November';
				break;
			case "12":
				$aktif_bulan = 'Desember';
				break;
		}
		
		$tanggal = date('d',$tanggal_aktif);
		$tahun = date('Y',$tanggal_aktif);
		
		$tanggal_aktif = $tanggal.' '.$aktif_bulan.' '.$tahun;
		
		$kas_account = $this->mm->get_single_coa_rp('11-0001');
		foreach($kas_account as $ka){
			$acc_number = $ka->accountnumber;
			$accountnumber = $ka->accountnumber;
			
			/*-- Menentukan Beginning Balance --*/
			$saldo_awal = $this->mm->get_report_rp_beginning_balance($acc_number);
			/*----------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_awal_d = $this->mm->get_report_mutasi_rp_yesterday_d($acc_number,$tgl_from);
			$saldo_awal_k = $this->mm->get_report_mutasi_rp_yesterday_k($acc_number,$tgl_from);
			
			foreach($saldo_awal_d as $mtyd){
				$saldo_awal = $saldo_awal + $mtyd->total_mutasi;
			}
			
			foreach($saldo_awal_k as $mtyk){
				$saldo_awal = $saldo_awal - $mtyk->total_mutasi;
			}
		}
		
		$sitename = $this->mm->get_site_name();
		
		$data['view'] = '<div class="ui stackable grid"><div class="sixteen wide centered column"><table class="lap_pdf" cellspacing="0" width="100%"><thead><tr><th colspan="2" style="text-align:left"><img src="'.base_url().'assets/images/branding/brand.png" style="width:240px"></th><th colspan="2" style="text-align:center;font-size:15px;">Laporan Kas Kasir <br>Cabang '.$sitename.'</th><th colspan="2" style="text-align:right;font-size:15px;"><br>'.$hari_tulis.', '.$tanggal_aktif.'</th></tr><tr><th colspan="6"><span style="visibility:hidden">-</span></th></tr><tr><th colspan="6"></th></tr></thead><tbody>';
		
		$data['view'] .= '<tr><td colspan="6" style="text-align:left;border:none;font-weight:bold;border-bottom:1px dotted #000">DATA PENJUALAN</td></tr><tr><td class="theader" style="width:40px;">No</td><td class="theader">Karat</td><td class="theader" style="width:60px;">Pcs</td><td class="theader" style="width:100px;">Gram</td><td class="theader" style="width:140px;">Rata2</td><td class="theader" style="width:160px;">Total Jual</td></tr>';
		
		$number = 1;
		$total_pcs = 0;
		$total_gram = 0;
		$total_jual = 0;
	
		$data_rekap = $this->mt->get_penjualan_rekap_karat($tgl_from,$tgl_to);
		foreach($data_rekap as $dr){
			$data['view'] .= '<tr><td style="text-align:right">'.$number.'.</td><td>'.$dr->karat_name.'</td><td style="text-align:right">'.$dr->pcs.'</td><td style="text-align:right">'.number_format($dr->berat, 2).'</td><td style="text-align:right">'.number_format($dr->harga/$dr->berat, 2).'</td><td style="text-align:right">'.number_format($dr->harga, 0).'</td><tr>';
			
			$total_pcs = $total_pcs + $dr->pcs;
			$total_gram = $total_gram + $dr->berat;
			$total_jual = $total_jual + $dr->harga;
			$number = $number + 1;
		}
		
		if($total_pcs != 0){
			$data['view'] .= '<tr><td colspan="2"></td><td class="double-top" style="text-align:right">'.$total_pcs.'</td><td class="double-top" style="text-align:right">'.number_format($total_gram, 2).'</td><td class="double-top" style="text-align:right"></td><td class="double-top" style="text-align:right">'.number_format($total_jual, 0).'</td><tr>';
		}else{
			$data['view'] .= '<tr><td colspan="6" style="text-align:center">Tidak Ada Penjualan</td><tr>';
		}
		
		$number = 1;
		$total_pcs = 0;
		$total_gram = 0;
		$total_beli = 0;
		
		$data['view'] .= '<tr><td colspan="6"><span style="visibility:hidden">-</span></td></tr><tr><td colspan="6" style="text-align:left;border:none;font-weight:bold;border-bottom:1px dotted #000">DATA PEMBELIAN</td></tr><tr><td class="theader" style="width:40px;">No</td><td class="theader">Karat</td><td class="theader" style="width:60px;">Pcs</td><td class="theader" style="width:100px;">Gram</td><td class="theader" style="width:140px;">Rata2</td><td class="theader" style="width:160px;">Total Beli</td></tr>';
		
		$data_rekap = $this->mt->get_pembelian_rekap_karat($tgl_from,$tgl_to);
		foreach($data_rekap as $dr){
			$data['view'] .= '<tr><td style="text-align:right">'.$number.'.</td><td>'.$dr->karat_name.'</td><td style="text-align:right">'.$dr->pcs.'</td><td style="text-align:right">'.number_format($dr->berat, 2).'</td><td style="text-align:right">'.number_format($dr->harga/$dr->berat, 2).'</td><td style="text-align:right">'.number_format($dr->harga, 2).'</td><tr>';
			
			$total_pcs = $total_pcs + $dr->pcs;
			$total_gram = $total_gram + $dr->berat;
			$total_beli = $total_beli + $dr->harga;
			$number = $number + 1;
		}
		
		if($total_pcs != 0){
			$data['view'] .= '<tr><td colspan="2"></td><td class="double-top" style="text-align:right">'.$total_pcs.'</td><td class="double-top" style="text-align:right">'.number_format($total_gram, 2).'</td><td class="double-top" style="text-align:right"></td><td class="double-top" style="text-align:right">'.number_format($total_beli, 2).'</td><tr>';
		}else{
			$data['view'] .= '<tr><td colspan="6" style="text-align:center">Tidak Ada Pembelian</td><tr>';
		}
		
		$data['view'] .= '<tr><td colspan="6"><span style="visibility:hidden">-</span></td></tr><tr><td colspan="6" style="border-bottom:double #000;"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '</tbody></table>';
		
		$data['view'] .= '<table class="lap_pdf_2" cellspacing="0" width="100%"><thead><tr><td class="theader">Keterangan</td><td class="theader">Debit</td><td class="theader">Credit</td><td class="theader">Balance</td></tr></thead><tbody>';
		
		$data['view'] .= '<tr><td style="font-weight:bold">Saldo Awal</td><td></td><td></td><td style="text-align:right;font-weight:bold">'.number_format($saldo_awal, 0).'</td><tr>';
		
		$trans_bank = $this->mt->get_mutasi_jual_bank_detail($tgl_from);
		
		foreach($trans_bank as $tb){
			$saldo_awal = $saldo_awal - $tb->bayar_2;
			
			$data['view'] .= '<tr><td>Pembayaran Dengan '.$tb->accountname.'</td><td></td><td style="text-align:right">'.number_format($tb->bayar_2, 0).'</td><td style="text-align:right">'.number_format($saldo_awal, 0).'</td><tr>';
		}
		
		$kas_masuk_keluar = $this->mt->get_mutasi_kas_masuk_keluar_detail($tgl_from);
		
		foreach($kas_masuk_keluar as $tb){
			if($tb->fromaccount == '11-0001'){
				$saldo_awal = $saldo_awal - $tb->value;
				$data['view'] .= '<tr><td>'.$tb->description.'</td><td></td><td style="text-align:right">'.number_format($tb->value, 0).'</td><td style="text-align:right">'.number_format($saldo_awal, 0).'</td><tr>';
			}else if($tb->toaccount == '11-0001'){
				$saldo_awal = $saldo_awal + $tb->value;
				$data['view'] .= '<tr><td>'.$tb->description.'</td><td style="text-align:right">'.number_format($tb->value, 0).'</td><td></td><td style="text-align:right">'.number_format($saldo_awal, 0).'</td><tr>';
			}
		}
		
		$saldo_awal = $saldo_awal + $total_jual;
		$data['view'] .= '<tr><td>Penjualan</td><td style="text-align:right">'.number_format($total_jual, 0).'</td><td></td><td style="text-align:right">'.number_format($saldo_awal, 0).'</td><tr>';
		
		$saldo_awal = $saldo_awal - $total_beli;
		$data['view'] .= '<tr><td>Pembelian</td><td></td><td style="text-align:right">'.number_format($total_beli, 0).'</td><td style="text-align:right;font-weight:bold;">'.number_format($saldo_awal, 0).'</td><tr>';
		
		$data['view'] .= '</tbody></table></div></div>';
		
		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		$pdf = $this->m_pdf->load();
        $pdf->AddPage('P');
        $pdf->WriteHTML($html);
		
        $pdf->Output("Laporan Kasir.pdf", "I");
	}
	
	public function login_pos(){
		if($this->session->userdata('gold_login') == TRUE){
			redirect('../index.php/C_home_pos');
		}else{
			$this->load->view('V_login_pos');
		}
	}
	
	public function logout(){
		$this->session->unset_userdata('gold_username');
		$this->session->unset_userdata('gold_nama_user');
		$this->session->unset_userdata('gold_kasir');
		$this->session->unset_userdata('gold_manager');
		$this->session->unset_userdata('gold_pembukuan');
		$this->session->unset_userdata('gold_admin');
		$this->session->unset_userdata('gold_pp');
		$this->session->unset_userdata('gold_login');
		redirect('../', 'refresh');
	}
	
	public function logout_pos(){
		$this->session->unset_userdata('gold_username');
		$this->session->unset_userdata('gold_nama_user');
		$this->session->unset_userdata('gold_kasir');
		$this->session->unset_userdata('gold_manager');
		$this->session->unset_userdata('gold_pembukuan');
		$this->session->unset_userdata('gold_admin');
		$this->session->unset_userdata('gold_pp');
		$this->session->unset_userdata('gold_login');
		redirect('../index.php/C_login/login_pos', 'refresh');
	}
}
