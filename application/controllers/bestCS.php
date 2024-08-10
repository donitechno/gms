<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BestCS extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->library('M_pdf');
		$this->load->model('M_karat','mk');
		$this->load->model('M_product','mp');
		$this->load->model('M_pos','mt');
		$this->load->model('M_mutasi','mm');
	}
	
	public function index(){
		$data['view'] = '<div class="ui fluid container no-print">
		<form class="ui form form-javascript" id="bestCS-form-filter" action="'.base_url().'index.php/bestCS/filter" method="post">
		<div class="ui grid">
			<div class="ui inverted dimmer" id="bestCS-loaderlist">
				<div class="ui large text loader">Loading</div>
			</div>
			<div class="eight wide centered column no-print" style="margin-top:15px">
				<div class="fields">
					<div class="five wide field">
						<input type="text" name="bestCS-datefrom" id="bestCS-datefrom" readonly>
					</div>
					<div class="two wide field" style="text-align:center;margin-top:7px">
						<label>s.d</label>
					</div>
					<div class="five wide field">
						<input type="text" name="bestCS-dateto" id="bestCS-dateto" readonly>
					</div>
					<div class="four wide field">
						<div class="ui fluid icon green button filter-input" id="bestCS-btnfilter" onclick=filterTransaksi("bestCS") title="Filter">
							<i class="filter icon"></i> Filter
						</div>
					</div>
				</div>
			</div>
			<div class="fifteen wide centered column" id="bestCS-wrap_filter">
			</div>
		</div>
		</form>';
		
		$data["date"] = 2;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function filter(){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		
		$site_name = $this->mm->get_site_name();
		
		$tgl_transaksi_from =  $this->input->post('bestCS-datefrom');
		$tanggal_transaksi_from =  $this->input->post('bestCS-datefrom');
		$tgl_transaksi_to =  $this->input->post('bestCS-dateto');
		$tanggal_transaksi_to =  $this->input->post('bestCS-dateto');
		$tglTransFrom = $this->date_to_format($tgl_transaksi_from);
		$tglTransTo = $this->date_to_format($tgl_transaksi_to);
		$tgl_from = date("Y-m-d",$tglTransFrom).' 00:00:00';
		$tgl_to = date("Y-m-d",$tglTransTo).' 23:59:59';
		
		$data_cs = $this->mm->get_cs();
		$data_best = $this->mm->get_best_customer($tgl_from,$tgl_to);
		$data_best2 = $this->mm->get_best_customer2($tgl_from,$tgl_to);
		$data_main_jual = $this->mm->get_count_jual_cs($tgl_from,$tgl_to);
		$data_detail_jual = $this->mm->get_count_jual_detail_cs($tgl_from,$tgl_to);
		$data_main_beli = $this->mm->get_count_beli_cs($tgl_from,$tgl_to);
		$data_detail_beli = $this->mm->get_count_beli_detail_cs($tgl_from,$tgl_to);
		
		$customer = array();
		$customer2 = array();
		foreach($data_cs as $db){
			$customer[$db->username] = 0;
			$customer2[$db->username] = 0;
		}
		
		foreach($data_main_jual as $dm){
			$customer[$dm->cust_service] = $customer[$dm->cust_service] + 1;
			
			foreach($data_detail_jual as $dd){
				if($dd->transaction_code == $dm->transaction_code){
					$customer[$dm->cust_service] = $customer[$dm->cust_service] + $dd->total - 1;
				}
			}
		}
		
		foreach($data_main_beli as $dm){
			$customer2[$dm->cust_service] = $customer2[$dm->cust_service] + 1;
			
			foreach($data_detail_beli as $dd){
				if($dd->transaction_code == $dm->transaction_code){
					$customer2[$dm->cust_service] = $customer2[$dm->cust_service] + $dd->total - 1;
				}
			}
		}
		
		$data['view'] = '<div class="ui stackable grid"><div class="eight wide centered column full-print" style="padding-top:0px;padding-bottom:10px;text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/bestCS/pdf/'.$tanggal_transaksi_from.'/'.$tanggal_transaksi_to.'" target=_blank"><i class="paperclip icon"></i> Download</a></div></div><div class="ui stackable grid"><div class="eight wide centered column full-print"><table id="bestCS-filtertabel" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th>Customer Service</th><th style="width:80px">Penjualan</th><th style="width:80px">Pembelian</th><th style="width:80px">Total</th></tr></thead><tbody>';
		
		$number = 1;
		$total_jual = 0;
		$total_beli = 0;
		$total_pcs = 0;
		
		foreach($data_cs as $dr){
			if($customer[$dr->username] > 0 || $customer2[$dr->username] > 0){
				//$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.strtoupper($dr->username).'</td><td class="right aligned">'.$customer[$dr->username].'</td><td class="right aligned">'.$customer2[$dr->username].'</td><td class="right aligned">'.$customer[$dr->username] + $customer2[$dr->username].'</td><tr>';
				
				$ttl = $customer[$dr->username] + $customer2[$dr->username];
				
				$data['view'] .= '<tr><td>'.strtoupper($dr->username).'</td><td class="right aligned">'.$customer[$dr->username].'</td><td class="right aligned">'.$customer2[$dr->username].'</td><td class="right aligned">'.$ttl.'</td></tr>';
				
				$total_jual = $total_jual + $customer[$dr->username];
				$total_beli = $total_beli + $customer2[$dr->username];
				$total_pcs = $total_pcs + $customer[$dr->username] + $customer2[$dr->username];
				$number = $number + 1;
			}
		}
		
		if($total_pcs != 0){
			$data['view'] .= '</tbody><tfoot><tr><th>Total</th><th class="double-top right aligned">'.$total_jual.'</th><th class="double-top right aligned">'.$total_beli.'</th><th class="double-top right aligned">'.$total_pcs.'</th></tr></tfoot></table></div></div>';
		}else{
			$data['view'] .= '</tbody></table></div></div>';
		}
		
		//$data['view'] .= '</tbody></table></div></div>';
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function pdf($tgl_transaksi_from,$tgl_transaksi_to){
		date_default_timezone_set("Asia/Jakarta");
		
		$tgl_transaksi_from = str_replace('%20',' ',$tgl_transaksi_from);
		$tanggal_transaksi_from =  $tgl_transaksi_from;
		$tgl_transaksi_to =  str_replace('%20',' ',$tgl_transaksi_to);
		$tanggal_transaksi_to = $tgl_transaksi_to;
		$tglTransFrom = $this->date_to_format($tgl_transaksi_from);
		$tglTransTo = $this->date_to_format($tgl_transaksi_to);
		$tgl_from = date("Y-m-d",$tglTransFrom).' 00:00:00';
		$tgl_to = date("Y-m-d",$tglTransTo).' 23:59:59';
		
		if($tanggal_transaksi_from == $tanggal_transaksi_to){
			$tanggal_tulis = $tanggal_transaksi_from;
		}else{
			$tanggal_tulis = $tanggal_transaksi_from.' s.d '.$tanggal_transaksi_to;
		}
		
		$sitename = $this->mm->get_site_name();
		
		$data['view'] = '<div class="sixteen wide centered column center aligned" style="font-weight:bold;text-align:center;font-size:13px;font-family:samsungone"><span style="text-align">Best Customer Service, Cabang '.$sitename.'</span><br><span>Tanggal '.$tanggal_tulis.'</span><br><span style="visibility:hidden">-</span></div><table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:30px" class="th-5">No</th><th class="th-5">Customer Service</th><th class="th-5">Penjualan</th><th class="th-5">Pembelian</th><th class="th-5">Total</th></tr></thead><tbody>';
		
		$data_cs = $this->mm->get_cs();
		$data_best = $this->mm->get_best_customer($tgl_from,$tgl_to);
		$data_best2 = $this->mm->get_best_customer2($tgl_from,$tgl_to);
		$data_main_jual = $this->mm->get_count_jual_cs($tgl_from,$tgl_to);
		$data_detail_jual = $this->mm->get_count_jual_detail_cs($tgl_from,$tgl_to);
		$data_main_beli = $this->mm->get_count_beli_cs($tgl_from,$tgl_to);
		$data_detail_beli = $this->mm->get_count_beli_detail_cs($tgl_from,$tgl_to);
		
		$customer = array();
		$customer2 = array();
		foreach($data_cs as $db){
			$customer[$db->username] = 0;
			$customer2[$db->username] = 0;
		}
		
		foreach($data_main_jual as $dm){
			$customer[$dm->cust_service] = $customer[$dm->cust_service] + 1;
			
			foreach($data_detail_jual as $dd){
				if($dd->transaction_code == $dm->transaction_code){
					$customer[$dm->cust_service] = $customer[$dm->cust_service] + $dd->total - 1;
				}
			}
		}
		
		foreach($data_main_beli as $dm){
			$customer2[$dm->cust_service] = $customer2[$dm->cust_service] + 1;
			
			foreach($data_detail_beli as $dd){
				if($dd->transaction_code == $dm->transaction_code){
					$customer2[$dm->cust_service] = $customer2[$dm->cust_service] + $dd->total - 1;
				}
			}
		}
		
		$number = 1;
		$total_jual = 0;
		$total_beli = 0;
		$total_pcs = 0;
		
		foreach($data_cs as $dr){
			if($customer[$dr->username] > 0 || $customer2[$dr->username] > 0){
				//$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.strtoupper($dr->username).'</td><td class="right aligned">'.$customer[$dr->username].'</td><td class="right aligned">'.$customer2[$dr->username].'</td><td class="right aligned">'.$customer[$dr->username] + $customer2[$dr->username].'</td><tr>';
				
				$ttl = $customer[$dr->username] + $customer2[$dr->username];
				
				$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.strtoupper($dr->username).'</td><td class="right aligned">'.$customer[$dr->username].'</td><td class="right aligned">'.$customer2[$dr->username].'</td><td class="right aligned">'.$ttl.'</td></tr>';
				
				$total_jual = $total_jual + $customer[$dr->username];
				$total_beli = $total_beli + $customer2[$dr->username];
				$total_pcs = $total_pcs + $customer[$dr->username] + $customer2[$dr->username];
				$number = $number + 1;
			}
		}
		
		if($total_pcs != 0){
			$data['view'] .= '<tr><td></td><td>Total</td><td class="double-top right aligned">'.$total_jual.'</td><td class="double-top right aligned">'.$total_beli.'</td><td class="double-top right aligned">'.$total_pcs.'</td></tr>';
		}else{
			$data['view'] .= '<tr><td class="center aligned" colspan="5">Tidak Ada Penjualan</td><tr>';
		}
		
		$data['view'] .= '</tbody></table></div></div>';
		
		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		//$pdf = $this->m_pdf->load();
		$pdf = new \Mpdf\Mpdf();
        $pdf->AddPage('P');
        $pdf->WriteHTML($html);
		
        $pdf->Output("Best Customer Service.pdf", "I");
	}
	
	public function pdf2($tgl_transaksi){
		date_default_timezone_set("Asia/Jakarta");
		
		$tgl_transaksi = str_replace('%20',' ',$tgl_transaksi);
		$tanggal_transaksi =  $tgl_transaksi;
		$tglTrans = $this->date_to_format($tgl_transaksi);
		$tgl_from = date("Y-m-d",$tglTrans).' 00:00:00';
		$tanggal_aktif = date("Y-m-d",$tglTrans).' 00:00:00';
		$tgl_to = date("Y-m-d",$tglTrans).' 23:59:59';
		
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
			$data['view'] .= '<tr><td style="text-align:right">'.$number.'.</td><td>'.$dr->karat_name.'</td><td style="text-align:right">'.$dr->pcs.'</td><td style="text-align:right">'.number_format($dr->berat, 2).'</td><td style="text-align:right">'.number_format($dr->harga/$dr->berat, 2).'</td><td style="text-align:right">'.number_format($dr->harga, 0).'</td><tr>';
			
			$total_pcs = $total_pcs + $dr->pcs;
			$total_gram = $total_gram + $dr->berat;
			$total_beli = $total_beli + $dr->harga;
			$number = $number + 1;
		}
		
		if($total_pcs != 0){
			$data['view'] .= '<tr><td colspan="2"></td><td class="double-top" style="text-align:right">'.$total_pcs.'</td><td class="double-top" style="text-align:right">'.number_format($total_gram, 2).'</td><td class="double-top" style="text-align:right"></td><td class="double-top" style="text-align:right">'.number_format($total_beli, 0).'</td><tr>';
		}else{
			$data['view'] .= '<tr><td colspan="6" style="text-align:center">Tidak Ada Pembelian</td><tr>';
		}
		
		$data['view'] .= '<tr><td colspan="6"><span style="visibility:hidden">-</span></td></tr><tr><td colspan="6" style="border-bottom:double #000;"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '</tbody></table>';
		
		$data['view'] .= '<table class="lap_pdf_2" cellspacing="0" width="100%"><thead><tr><td class="theader" colspan="2">Keterangan</td><td class="theader">Debit</td><td class="theader">Credit</td><td class="theader">Balance</td></tr></thead><tbody>';
		
		$data['view'] .= '<tr><td style="font-weight:bold" colspan="2">Saldo Awal</td><td></td><td></td><td style="text-align:right;font-weight:bold">'.number_format($saldo_awal, 0).'</td><tr>';
		
		$kas_masuk_keluar = $this->mt->get_mutasi_kas_masuk_keluar_detail($tgl_from);
		
		foreach($kas_masuk_keluar as $tb){
			if($tb->fromaccount == '11-0001'){
				$saldo_awal = $saldo_awal - $tb->value;
				$data['view'] .= '<tr><td colspan="2">'.$tb->description.'</td><td></td><td style="text-align:right">'.number_format($tb->value, 0).'</td><td style="text-align:right">'.number_format($saldo_awal, 0).'</td><tr>';
			}else if($tb->toaccount == '11-0001'){
				$saldo_awal = $saldo_awal + $tb->value;
				$data['view'] .= '<tr><td colspan="2">'.$tb->description.'</td><td style="text-align:right">'.number_format($tb->value, 0).'</td><td></td><td style="text-align:right">'.number_format($saldo_awal, 0).'</td><tr>';
			}
		}
		
		$saldo_awal = $saldo_awal + $total_jual;
		
		$data['view'] .= '<tr><td colspan="2">Penjualan</td><td style="text-align:right">'.number_format($total_jual, 0).'</td><td></td><td style="text-align:right">'.number_format($saldo_awal, 0).'</td><tr>';
		
		$kas_account = $this->mm->get_all_bank_pos();
		
		$jual_bank = array();
		$flag_jual_bank = FALSE;
		$number_bank = 1;
		$last_bank = 0;
 		foreach($kas_account as $ka){
			$accountnumber = $ka->accountnumber;
			$jual_bank[$number_bank] = 0;
			$total_jual_bank = $this->mm->get_total_jual_bank($accountnumber,$tgl_from);
			$jual_bank[$number_bank] = $jual_bank[$number_bank] + $total_jual_bank;
			
			if($jual_bank[$number_bank] > 0){
				$flag_jual_bank = TRUE;
				$last_bank = $number_bank;
			}
			
			$number_bank = $number_bank + 1;
		}
		
		$saldo_awal = $saldo_awal - $total_beli;
		
		if($flag_jual_bank == TRUE){
			$data['view'] .= '<tr><td colspan="2">Pembelian</td><td></td><td style="text-align:right">'.number_format($total_beli, 0).'</td><td style="text-align:right;">'.number_format($saldo_awal, 0).'</td><tr>';
		}else{
			$data['view'] .= '<tr><td colspan="2">Pembelian</td><td></td><td style="text-align:right">'.number_format($total_beli, 0).'</td><td style="text-align:right;font-weight:bold;">'.number_format($saldo_awal, 0).'</td><tr>';
		}
		
		$number_bank = 1;
		foreach($kas_account as $ka){
			if($jual_bank[$number_bank] != 0){
				$saldo_awal = $saldo_awal - $jual_bank[$number_bank];
				
				if($last_bank == $number_bank){
					$data['view'] .= '<tr><td colspan="2">'.$ka->accountname.'</td><td></td><td style="text-align:right">'.number_format($jual_bank[$number_bank], 0).'</td><td style="text-align:right;font-weight:bold;">'.number_format($saldo_awal, 0).'</td><tr>';
				}else{
					$data['view'] .= '<tr><td colspan="2">'.$ka->accountname.'</td><td></td><td style="text-align:right">'.number_format($jual_bank[$number_bank], 0).'</td><td style="text-align:right;">'.number_format($saldo_awal, 0).'</td><tr>';
				}
			}
			
			$number_bank = $number_bank + 1;
		}
		
		$data['view'] .= '<tr><td colspan="5" style="border:none"><span style="visibility:hidden">-</span></td></tr>';
		$data['view'] .= '<tr><td colspan="5" style="border:none"><span style="visibility:hidden">-</span></td></tr>';
		$data['view'] .= '<tr><td colspan="5" style="border:none"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '<tr><td class="theader">Visa</td><td class="theader">Saldo Awal</td><td class="theader">Debit</td><td class="theader">Credit</td><td class="theader">Saldo Akhir</td></tr>';
		
		$kas_account = $this->mm->get_all_bank_pos();
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
	
			$mutasi_transaksi = $this->mm->get_report_mutasi_rp_lap($tgl_from, $tgl_from, $acc_number);
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs = FALSE;
			$flag_saldo_awal = FALSE;
			$flag_mutasi = FALSE;
			
			if($saldo_awal != 0){
				$flag_saldo_awal = TRUE;
				$flag_kurs = TRUE;
			}
			
			foreach($mutasi_transaksi as $mk){
				if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
					$flag_mutasi = TRUE;
					$flag_kurs = TRUE;
				}
			}
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs == TRUE){
				$coa_data = $this->mm->get_coa_number_name_2($ka->accountnumber);
				
				$running_balance = $saldo_awal;
				$total_debet = 0;
				$total_kredit = 0;
				
				//$data['view'] .= '<tr><td style="font-weight:bold">'.$coa_data.'</td><td></td><td></td><td style="text-align:right;font-weight:bold">'.number_format($saldo_awal, 0).'</td><tr>';
				
				$data['view'] .= '<tr><td>'.$coa_data.'</td><td style="text-align:right">'.number_format($saldo_awal, 0).'</td>';
				
				//<td></td><td></td><td></td><tr>';
				
				$number = 0;
				$total_debit = 0;
				$total_credit = 0;
				$total = count($mutasi_transaksi);
				
				foreach($mutasi_transaksi as $mk){
					if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
						if($mk->toaccount == $accountnumber){
							$saldo_awal = $saldo_awal + $mk->value;
							$total_debit = $total_debit + $mk->value;
							/*$number = $number + 1;
							if($number == $total){
								$data['view'] .= '<tr><td>'.$mk->description.'</td><td style="text-align:right">'.number_format($mk->value, 0).'</td><td></td><td style="text-align:right;font-weight:bold">'.number_format($saldo_awal, 0).'</td><tr>';
							}else{
								$data['view'] .= '<tr><td>'.$mk->description.'</td><td style="text-align:right">'.number_format($mk->value, 0).'</td><td></td><td style="text-align:right">'.number_format($saldo_awal, 0).'</td><tr>';
							}*/
						}else{
							$saldo_awal = $saldo_awal - $mk->value;
							$total_credit = $total_credit + $mk->value;
							/*$number = $number + 1;
							if($number == $total){
								$data['view'] .= '<tr><td>'.$mk->description.'</td><td></td><td style="text-align:right">'.number_format($mk->value, 0).'</td><td style="text-align:right;font-weight:bold">'.number_format($saldo_awal, 0).'</td><tr>';
							}else{
								$data['view'] .= '<tr><td>'.$mk->description.'</td><td></td><td style="text-align:right">'.number_format($mk->value, 0).'</td><td style="text-align:right">'.number_format($saldo_awal, 0).'</td><tr>';
							}*/
						}
					}
				}
				
				$data['view'] .= '<td style="text-align:right;">'.number_format($total_debit, 0).'</td><td style="text-align:right;">'.number_format($total_credit, 0).'</td><td style="text-align:right;">'.number_format($saldo_awal, 0).'</td>';
				
				//$data['view'] .= '<tr><td colspan="4" style="border:none"><span style="visibility:hidden">-</span></td></tr>';
				//$data['view'] .= '<tr><td colspan="4" style="border:none"></td></tr>';
			}
			
			/*----------------------------------------------------*/
		}
		
		$data['view'] .= '</tbody></table></div></div>';
		
		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		$pdf = $this->m_pdf->load();
        $pdf->AddPage('P');
        $pdf->WriteHTML($html);
		
		$pdf->Output("Laporan Kasir.pdf", "I");
	}
	
	/* UBAH FORMAT TANGGAL */
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
