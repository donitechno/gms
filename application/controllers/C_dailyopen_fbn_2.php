<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_dailyopen extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect('C_home_pos');
		}
		
		$this->load->model('M_login','ml');
		$this->load->model('M_karat','mk');
		$this->load->model('M_pos','mt');
		$this->load->model('M_mutasi','mm');
		
		$GLOBALS['kasir'] = 1;
	}
	
	public function index(){	
		$active_date = $this->mt->get_tanggal_aktif($GLOBALS['kasir']);
		$tanggal_aktif = $active_date[0]->tanggal_aktif;
		$tanggal_query = $active_date[0]->tanggal_aktif;
		
		$harga_emas = $this->mt->get_do_by_date($tanggal_aktif);
		
		if($harga_emas == 0){
			$harga_emas = $this->mt->get_last_do();
		}
		
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
		
		$acc_number = '11-0001';
		$saldo_awal = $this->mm->get_report_rp_beginning_balance($acc_number);
		
		$saldo_awal_d = $this->mm->get_report_mutasi_rp_yesterday_d($acc_number,$tanggal_query);
		$saldo_awal_k = $this->mm->get_report_mutasi_rp_yesterday_k($acc_number,$tanggal_query);
		
		foreach($saldo_awal_d as $mtyd){
			$saldo_awal = $saldo_awal + $mtyd->total_mutasi;
		}
		
		foreach($saldo_awal_k as $mtyk){
			$saldo_awal = $saldo_awal - $mtyk->total_mutasi;
		}
		
		$data['tanggal_aktif'] = $tanggal.' '.$aktif_bulan.' '.$tahun;
		$data['harga_emas'] = $harga_emas;
		$data['saldo_awal'] = $saldo_awal;
		$data['karat'] = $this->mk->get_do_formula();
		
		$this->load->view('pos/V_dailyopen',$data);
	}
	
	public function get_do_bydate($do_date){
		$do_date = str_replace('%20',' ',$do_date);
		$doDate = $this->date_to_format($do_date);
		$do_date = date("Y-m-d",$doDate).' 00:00:00';
		
		$harga_emas = $this->mt->get_do_by_date($do_date);
		if($harga_emas == 0){
			$harga_emas = $this->mt->get_last_do();
		}
		
		$karat = $this->mk->get_do_formula();
		
		$data['view'] = '<table class="ui celled table" cellspacing="0" width="100%" style="margin-top:15px;"><thead><tr style="text-align:center"><th rowspan="2">Karat</th><th colspan="2">Harga Jual</th><th colspan="2">Harga Beli</th></tr><tr style="text-align:center"><th style="border-left:1px solid rgba(34,36,38,.1);">Persen</th><th>Harga</th><th>Persen</th><th>Harga</th></tr></thead><tbody>';
		
		$data['harga_emas'] = number_format($harga_emas,0,".",",");
		
		$harga_emas = $harga_emas + 4000;
		foreach($karat as $k){
			$sell = $harga_emas * $k->kadar_jual / 100;
			$buy = $harga_emas * $k->kadar_beli_bgs / 100;
			
			$sell = $sell / 1000;
			$buy = $buy / 1000;
			
			$sell = ceil($sell);
			$buy = floor($buy);
			
			$sell = $sell * 1000;
			$buy = $buy * 1000;
		
			$data['view'] .= '<tr><td>'.$k->description.'</td><td id="kj_'.$k->id.'">'.$k->kadar_jual.'</td><td id="j_'.$k->id.'">'.number_format($sell,0,".",",").'</td><td id="kb_'.$k->id.'">'.$k->kadar_beli_bgs.'</td><td id="b_'.$k->id.'">'.number_format($buy,0,".",",").'</td></tr>';
		}
		
		$data['view'] .= '</tbody></table>';
		
		
		
		$acc_number = '11-0001';
		$saldo_awal = $this->mm->get_report_rp_beginning_balance($acc_number);
		
		$saldo_awal_d = $this->mm->get_report_mutasi_rp_yesterday_d($acc_number,$do_date);
		$saldo_awal_k = $this->mm->get_report_mutasi_rp_yesterday_k($acc_number,$do_date);
		
		foreach($saldo_awal_d as $mtyd){
			$saldo_awal = $saldo_awal + $mtyd->total_mutasi;
		}
		
		foreach($saldo_awal_k as $mtyk){
			$saldo_awal = $saldo_awal - $mtyk->total_mutasi;
		}
		
		$data['saldo_awal'] = number_format($saldo_awal,0);
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function save_daily_open(){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		
		$do_date = $this->input->post('tanggal_aktif');
		$doDate = $this->date_to_format($do_date);
		$do_date = date("Y-m-d",$doDate).' 00:00:00';
		
		$harga_emas = $this->input->post('harga_emas');
		$harga_emas = str_replace(',','',$harga_emas);
		$created_by = $this->session->userdata('gold_nama_user');
		
		$cek_do = $this->mt->cek_do_by_date($do_date);
		if($cek_do == 'Y'){
			$this->mt->update_do($do_date,$harga_emas);
		}else if($cek_do == 'N'){
			$this->mt->insert_do($do_date,$harga_emas,$created_by);
		}
		
		$this->mt->update_tanggal_aktif($do_date,$GLOBALS['kasir']);
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function konversi_harga($harga_emas){
		$harga_emas = $harga_emas + 4000;
		$karat = $this->mk->get_do_formula();
		
		foreach($karat as $k){
			$sell = $harga_emas * $k->kadar_jual / 100;
			$buy = $harga_emas * $k->kadar_beli_bgs / 100;
			
			$sell = $sell / 1000;
			$buy = $buy / 1000;
			
			$sell = ceil($sell);
			$buy = floor($buy);
			
			$sell = $sell * 1000;
			$buy = $buy * 1000;
		
			$data['sell'][] = number_format($sell,0,".",",");
			$data['buy'][] = number_format($buy,0,".",",");
		}
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	/* Ubah Format Tanggal */
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
