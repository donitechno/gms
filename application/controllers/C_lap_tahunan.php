<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_lap_tahunan extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->library('M_pdf');
		$this->load->model('M_login','ml');
		$this->load->model('M_mutasi','mm');
		$this->load->model('M_pos','mt');
		$this->load->model('M_product','mp');
		$this->load->model('M_karat','mk');
		
		$GLOBALS['kasir'] = 1;
	}
	
	public function index(){
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
		
		$data['header'] = '<table class="lap_pdf_neraca" cellspacing="0" width="100%"><thead><tr><th style="text-align:left;width:25%"><img src="'.base_url().'assets/images/branding/brand.png" style="width:200px"></th><th style="text-align:center;font-size:12px;text-transform:uppercase;width:50%">Laporan Laba/Rugi <br>Cabang '.$sitename.'</th><th style="text-align:right;font-size:12px;width:25%">'.$hari_tulis.', '.$tanggal_tulis.'<br>Harga Emas : '.number_format($harga_emas, 0).'</th></tr><tr><th colspan="6"><span style="visibility:hidden">-</span></th></tr><tr><th colspan="6"></th></tr></thead><tbody></tbody></table>';
		
		$data['view'] = '<div class="ui stackable grid"><div class="sixteen wide centered column full-print"><table class="lap_pdf_neraca"  cellspacing="0" style="width:100%"><thead><tr><th class="th-border" style="width:30px">No</th><th class="th-border">Karat</th><th class="th-border">Pcs</th><th class="th-border">Gram</th><th class="th-border">24K</th></tr></thead><tbody><tr><td colspan="5" style="text-align:left;text-transform:uppercase;font-weight:bold">Emas Pajangan</td></tr>';
		
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
		
		
		$data['view'] .= '<tr><td colspan="5" style="text-transform:uppercase;font-weight:bold;border-top:1px solid #000;">Reparasi Toko</td></tr>';
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
			
			$number_titipan = 1;
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
				
				if($saldo_awal_kurs[$id_kurs] != 0 && $saldo_awal_kurs[$id_kurs] != NULL){
					$namaaccount = str_replace('TITIPAN PELANGGAN - ','',$ka->accountname); 
					
					$data['view'] .= '<tr><td class="right-aligned">'.$number_titipan.'</td><td colspan = "3">'.$namaaccount.'</td><td class="right-aligned">'.number_format($saldo_awal_kurs[$id_kurs], 3).'</td></tr>';
					
					$number_titipan = $number_titipan + 1;
				}
				
				$total_titipan = $total_titipan + $saldo_awal_kurs[$id_kurs];				
			}
			
			$data['view'] .= '<tr><td class="right-aligned th-border-bottom td-total" colspan="2">TOTAL</td><td class="th-border-bottom td-total"></td><td class="th-border-bottom td-total"></td><td class="right-aligned th-border-bottom td-total">'.number_format($total_titipan, 3).'</td></tr>';
			
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
		
		$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '<tr><td colspan="5" class="th-border-top">PERSEDIAAN EMAS</td></tr>';
		
		$data['view'] .= '<tr><td class="right-aligned"></td><td>Emas Pajangan</td><td class="right-aligned">'.number_format($total_pajangan24, 3).'</td><td class="right-aligned">'.number_format($total_pajangan24 * $harga_emas, 0).'</td><td class="right-aligned">'.number_format($total_pajangan24, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td class="right-aligned"></td><td>Reparasi Toko</td><td class="right-aligned">'.number_format($total_reptoko24, 3).'</td><td class="right-aligned">'.number_format($total_reptoko24 * $harga_emas, 0).'</td><td class="right-aligned">'.number_format($total_reptoko24, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td class="right-aligned"></td><td>Dept. Reparasi</td><td class="right-aligned">'.number_format($total_deprep24, 3).'</td><td class="right-aligned">'.number_format($total_deprep24 * $harga_emas, 0).'</td><td class="right-aligned">'.number_format($total_deprep24, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td class="right-aligned"></td><td>Dept. Pengadaan</td><td class="right-aligned">'.number_format($total_depgrs24, 3).'</td><td class="right-aligned">'.number_format($total_depgrs24 * $harga_emas, 0).'</td><td class="right-aligned">'.number_format($total_depgrs24, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td class="right-aligned"></td><td>Titipan Pelanggan</td><td class="right-aligned">('.number_format($total_titipan24, 3).')</td><td class="right-aligned">('.number_format($total_titipan24 * $harga_emas, 0).')</td><td class="right-aligned">('.number_format($total_titipan24, 3).')</td></tr>';
		
		$data['view'] .= '<tr><td colspan="2" class="td-total">TOTAL PERSEDIAAN EMAS</td><td class="td-total"></td><td class="td-total right-aligned">'.number_format($total24 * $harga_emas, 0).'</td><td class="td-total right-aligned">'.number_format($total24, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '<tr><td colspan="5" class="th-border-top">Piutang Usaha</td></tr>';
		
		$coa_from = 140001;
		$coa_to = 169999;
		
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
		
		
		$data['view'] .= '<tr><td colspan="5" class="th-border-top">DP BIAYA</td></tr>';
		
		$pidp = $this->mm->get_default_account('PIDP');
		$kas = $this->mm->get_default_account('KE');
		
		$periode = $this->mm->get_periode_aktif($tanggal_aktif);
		$from_date = $periode[0]->from_date;
		$to_date = $per_tanggal;
		$map = $periode[0]->map;
		
		$setor_dp = $this->mm->get_setor_biaya($kas,$pidp,$from_date,$to_date);
		
		if($setor_dp[0]->total == NULL || $setor_dp[0]->total == ''){
			$total_setor = 0;
		}else{
			$total_setor = $setor_dp[0]->total;
		}
		
		$total_setor_rp = $total_setor;
		$total_setor_gr = $total_setor / $harga_emas;
		
		$data['view'] .= '<tr><td></td><td>Setoran Periode Ini</td><td class="right-aligned">'.number_format($total_setor_rp, 0).'</td><td class="right-aligned">'.number_format($total_setor, 0).'</td><td class="right-aligned">'.number_format($total_setor_gr, 3).'</td>';
		
		$byo = $this->mm->get_default_account('BYO');
		
		$setor_op = $this->mm->get_setor_biaya($pidp,$byo,$from_date,$to_date);
		
		if($setor_op[0]->total == NULL || $setor_op[0]->total == ''){
			$total_op = 0;
		}else{
			$total_op = $setor_op[0]->total;
		}
		
		$total_op_rp = $total_op;
		$total_op_gr = $total_op / $harga_emas;
		
		$data['view'] .= '<tr><td></td><td>Biaya Operasional</td><td class="right-aligned">('.number_format($total_op_rp, 0).')</td><td class="right-aligned">('.number_format($total_op, 0).')</td><td class="right-aligned">('.number_format($total_op_gr, 3).')</td>';
		
		$byg = $this->mm->get_default_account('BYG');
		
		$setor_gr = $this->mm->get_setor_biaya($pidp,$byg,$from_date,$to_date);
		
		if($setor_gr[0]->total == NULL || $setor_gr[0]->total == ''){
			$total_gr = 0;
		}else{
			$total_gr = $setor_gr[0]->total;
		}
		
		$total_gr_rp = $total_gr;
		$total_gr_gr = $total_gr / $harga_emas;
		
		$data['view'] .= '<tr><td></td><td>Biaya Bersama Group</td><td class="right-aligned">('.number_format($total_gr_rp, 0).')</td><td class="right-aligned">('.number_format($total_gr, 0).')</td><td class="right-aligned">('.number_format($total_gr_gr, 3).')</td>';
		
		$dp_biaya_rp = $total_setor_rp - $total_op_rp - $total_gr_rp;
		$dp_biaya_gr = $total_setor_gr - $total_op_gr - $total_gr_gr;
		
		$data['view'] .= '<tr><td colspan="2" class="th-border-bottom td-total">LEBIH/KURANG DP BIAYA</td><td class="th-border-bottom td-total"></td><td class="th-border-bottom td-total right-aligned">'.number_format($dp_biaya_rp, 0).'</td><td class="th-border-bottom td-total right-aligned">'.number_format($dp_biaya_gr, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		$total_aktiva_rp = ($total24*$harga_emas) + $total_kasbank_rp + $total_piutang_rp + $dp_biaya_rp;
		$total_aktiva_gr = $total24 + $total_kasbank_gr + $total_piutang_gr + $dp_biaya_gr;
		
		$data['view'] .= '<tr><td colspan="2" class="th-border">TOTAL AKTIVA</td><td class="th-border"></td><td class="th-border right-aligned">'.number_format($total_aktiva_rp, 0).'</td><td class="th-border right-aligned">'.number_format($total_aktiva_gr, 3).'</td></tr>';
		
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
				
				$namaaccount = str_replace('TITIPAN PELANGGAN - ','TP - ',$ka->accountname);
				
				$data['view'] .= '<tr><td class="right-aligned">'.$count_data.'</td><td>'.$namaaccount.'</td>';
				
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
		
		$data['view'] .= '<tr><td colspan="5" class="th-border-top">Modal Operasional</td></tr>';
		
		$map_gr = $map;
		$map_rp = $map * $harga_emas;
	
		$data['view'] .= '<tr><td></td><td>Modal Awal Periode</td><td class="right-aligned">'.number_format($map_gr, 3).'</td><td class="right-aligned">'.number_format($map_rp, 0).'</td><td class="right-aligned">'.number_format($map_gr, 3).'</td>';
		
		$mdrp = $this->mm->get_default_account('MKRP');
		
		$tambah_rp = $this->mm->get_setor_biaya($mdrp,$kas,$from_date,$to_date);
		$kurang_rp = $this->mm->get_setor_biaya($kas,$mdrp,$from_date,$to_date);
		
		if($tambah_rp[0]->total == NULL || $tambah_rp[0]->total == ''){
			$total_tambah_rp = 0;
		}else{
			$total_tambah_rp = $tambah_rp[0]->total;
		}
		
		if($kurang_rp[0]->total == NULL || $kurang_rp[0]->total == ''){
			$total_kurang_rp = 0;
		}else{
			$total_kurang_rp = $kurang_rp[0]->total;
		}
		
		$total_tk_modal_rp = $total_tambah_rp - $total_kurang_rp;
		
		$data['view'] .= '<tr><td></td><td>+- Modal (Rp)</td><td class="right-aligned">'.number_format($total_tk_modal_rp, 0).'</td><td class="right-aligned">'.number_format($total_tk_modal_rp, 0).'</td><td class="right-aligned">'.number_format($total_tk_modal_rp / $harga_emas, 3).'</td>';
		
		$mdgr = $this->mm->get_default_account('MKRP');
		$srt = $this->mm->get_default_account('SRT');
		
		$total_tambah_gr = 0;
		$total_kurang_gr = 0;
		
		foreach($data_karat as $k){
			$tambah_gr = $this->mm->get_setor_biaya_gr($mdgr,$srt,$k->id,$from_date,$to_date);
			$kurang_gr = $this->mm->get_setor_biaya_gr($srt,$mdgr,$k->id,$from_date,$to_date);

			if(count($tambah_gr) == 0){
				
			}else{
				$total_tambah_gr = $total_tambah_gr + ($tambah_gr[0]->total * $tambah_gr[0]->kali_laporan);
			}

			if(count($kurang_gr) == 0){
				
			}else{
				$total_kurang_gr = $total_kurang_gr + ($kurang_gr[0]->total * $kurang_gr[0]->kali_laporan);
			}
		}
		
		$total_tk_modal_gr = $total_tambah_gr - $total_kurang_gr;
		
		$data['view'] .= '<tr><td></td><td>+- Modal (Gram)</td><td class="right-aligned">'.number_format($total_tk_modal_gr, 3).'</td><td class="right-aligned">'.number_format($total_tk_modal_gr * $harga_emas, 0).'</td><td class="right-aligned">'.number_format($total_tk_modal_gr, 3).'</td>';
		
		$mat_rp = ($map * $harga_emas) + $total_tk_modal_rp + ($total_tk_modal_gr * $harga_emas);
		$mat_gr = $map + ($total_tk_modal_rp / $harga_emas) + $total_tk_modal_gr;
		
		$data['view'] .= '<tr><td colspan="2" class="th-border-bottom td-total">Modal Kerja Awal Tahun</td><td class="th-border-bottom td-total"></td><td class="th-border-bottom right-aligned td-total">'.number_format($mat_rp, 0).'</td><td class="th-border-bottom right-aligned td-total">'.number_format($mat_gr, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		$laba_bef_zakat_rp = $total_aktiva_rp - $total_hutang_rp - $mat_rp;
		$laba_bef_zakat_gr = $total_aktiva_gr - $total_hutang_gr - $mat_gr;
		
		$data['view'] .= '<tr><td colspan="2" class="th-border-bottom td-total">Laba/Rugi Tahun Berjalan</td><td class="th-border-bottom td-total"></td><td class="th-border-bottom right-aligned td-total">'.number_format($laba_bef_zakat_rp, 0).'</td><td class="th-border-bottom right-aligned td-total">'.number_format($laba_bef_zakat_gr, 3).'</td></tr>';
		
		
		
		
		
		
		
		$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '<tr><td colspan="5" class="th-border-top">Menghitung Zakat</td></tr>';
		
		$data['view'] .= '<tr><td></td><td>Total Aktiva</td><td class="right-aligned"></td><td class="right-aligned">'.number_format($total_aktiva_rp, 0).'</td><td class="right-aligned">'.number_format($total_aktiva_gr, 3).'</td>';
		
		$data['view'] .= '<tr><td></td><td>(-) Total Hutang</td><td class="right-aligned"></td><td class="right-aligned">('.number_format($total_hutang_rp, 0).')</td><td class="right-aligned">('.number_format($total_hutang_gr, 3).')</td>';
		
		if($dp_biaya_rp < 0){
			$dp_biaya_rp_zakat = $dp_biaya_rp;
			$dp_biaya_gr_zakat = $dp_biaya_gr;
		}else{
			$dp_biaya_rp_zakat = $dp_biaya_rp * -1;
			$dp_biaya_gr_zakat = $dp_biaya_gr * -1;
		}
		
		$data['view'] .= '<tr><td></td><td>(-) Sisa DP Biaya</td><td class="right-aligned"></td><td class="right-aligned">('.number_format($dp_biaya_rp_zakat * -1, 0).')</td><td class="right-aligned">'.number_format($dp_biaya_gr_zakat * -1, 3).'</td>';
		
		$dp_zakat_rp = $total_aktiva_rp - $total_hutang_rp + $dp_biaya_rp_zakat;
		$dp_zakat_gr = $total_aktiva_gr - $total_hutang_gr + $dp_biaya_gr_zakat;
		
		$data['view'] .= '<tr><td colspan="2">Dasar Pengenaan Zakat</td><td></td><td class="right-aligned">'.number_format($dp_zakat_rp, 0).'</td><td class="right-aligned">'.number_format($dp_zakat_gr, 3).'</td></tr>';
		
		if($dp_zakat_rp > 0){
			$zakat_rp = $dp_zakat_rp * 2.5/100;
			$zakat_gr = $dp_zakat_gr * 2.5/100;
		}else{
			$zakat_rp = 0;
			$zakat_gr = 0;
		}
		
		$data['view'] .= '<tr><td colspan="2" class="th-border-bottom td-total">Zakat 2.5%</td><td class="th-border-bottom td-total"></td><td class="th-border-bottom right-aligned td-total">'.number_format($zakat_rp, 0).'</td><td class="th-border-bottom right-aligned td-total">'.number_format($zakat_gr, 3).'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '<tr><td colspan="2" class="th-border-bottom td-total">Sisa Laba/Rugi</td><td class="th-border-bottom td-total"></td><td class="th-border-bottom right-aligned td-total">'.number_format($laba_bef_zakat_rp - $zakat_rp, 0).'</td><td class="th-border-bottom right-aligned td-total">'.number_format($laba_bef_zakat_gr - $zakat_gr, 3).'</td></tr>';
		
		
		//$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
		
		//$data['view'] .= '<tr><td colspan="2" class="th-border">LABA/RUGI</td><td class="th-border"></td><td class="th-border right-aligned">'.number_format($modal_kerja_rp - $modal_kerja_rp_kemarin, 0).'</td><td class="th-border right-aligned">'.number_format($modal_kerja_gr - $modal_kerja_gr_kemarin, 3).'</td></tr>';
		
		$data['view'] .= '</tbody></table></div></div>';
		
		$data['viewkiri'] = $data['view'];
		
		#$html = $this->load->view("pdf/V_pdf_neraca",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		$this->load->view("pdf/V_pdf_neraca",$data);
		#$pdf = $this->m_pdf->load();
        #$pdf->AddPage('P');
        #$pdf->WriteHTML($html);
		
        #$pdf->Output("Laporan Tahunan.pdf", "I");
	}
	
}