<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LapSaldoGr extends CI_Controller{

	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		//$this->load->library('M_pdf');
		$this->load->model('M_karat','mk');
		$this->load->model('M_product','mp');
		$this->load->model('M_pos','mt');
		$this->load->model('M_kelompok_barang','mc');
		$this->load->model('M_mutasi','mm');
	}
	
	public function index(){
		$account = $this->mm->get_mas_coa();
		$karat = $this->mk->get_karat_srt();
		
		$data['view'] = '<div class="ui container fluid">
			<form class="ui form form-javascript" id="lapSaldoGr-form-filter" action="'.base_url().'index.php/lapSaldoGr/filter" method="post">
			<div class="ui grid">
				<div class="ui inverted dimmer" id="lapSaldoGr-loaderlist">
					<div class="ui large text loader">Loading</div>
				</div>
				<div class="fifteen wide centered column" style="margin-top:15px">
					<div class="fields">
						<div class="three wide field">
							<label>Tgl Mutasi</label>
							<input type="text" name="lapSaldoGr-datefrom" id="lapSaldoGr-datefrom" readonly>
						</div>
						<div class="one wide field" style="text-align:center;margin-top:30px">
							<label>s.d</label>
						</div>
						<div class="three wide field">
							<label style="visibility:hidden">-</label>
							<input type="text" name="lapSaldoGr-dateto" id="lapSaldoGr-dateto" readonly>
						</div>
						<div class="five wide field">
							<label>Account</label>
							<select name="lapSaldoGr-accountnumber" id="lapSaldoGr-accountnumber" onchange=getKaratLaporan("lapSaldoGr")>';
								foreach($account as $a) {
								$data['view'] .= '<option value="'.$a->accountnumberint.'">'.$a->accountname.'</option>';
								}
							$data['view'] .= '</select>
						</div>
						<div class="five wide field">
							<label>Karat</label>
							<select name="lapSaldoGr-idkarat" id="lapSaldoGr-idkarat">
								<option value="All" class="class-karat">-- All --</option>';
								foreach($karat as $k){
									$ket_karat = '';
									if($k->id != 1){
										$ket_karat = 'class="class-karat"';
									}
									$data['view'] .= '<option value="'.$k->id.'"'.$ket_karat.'>'.$k->karat_name.'</option>';
								}
							$data['view'] .= '</select>
						</div>
						<div class="three wide field">
							<label style="visibility:hidden">-</label>
							<select name="lapSaldoGr-detail_rekap" id="lapSaldoGr-detail_rekap">
								<option value="D">Detail</option>
								<option value="R">Rekap</option>
							</select>
						</div>
						<div class="one wide field">
							<label style="visibility:hidden">-</label>
							<div class="ui fluid icon green button filter-input" id="lapSaldoGr-btnfilter" onclick=filterTransaksi("lapSaldoGr") title="Filter">
								<i class="filter icon"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="fifteen wide centered column" id="lapSaldoGr-wrap_filter" style="padding-top:0">
				</div>
			</div>
			</form>
		</div>';
		
		$data["date"] = 2;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function filter(){
		date_default_timezone_set("Asia/Jakarta");
		
		$from_date = $this->input->post('lapSaldoGr-datefrom');
		$dari_tanggal = $this->input->post('lapSaldoGr-datefrom');
		$to_date = $this->input->post('lapSaldoGr-dateto');
		$sampai_tanggal = $this->input->post('lapSaldoGr-dateto');
		
		$stockFromTime = $this->date_to_format($from_date);
		$stockToTime = $this->date_to_format($to_date);
		
		$from_date = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_date = date("Y-m-d",$stockToTime).' 23:59:59';
		
		if($dari_tanggal == $sampai_tanggal){
			$tanggal_tulis = $dari_tanggal;
		}else{
			$tanggal_tulis = $dari_tanggal.' s.d '.$sampai_tanggal;
		}
		
		$accountnumber = $this->input->post('lapSaldoGr-accountnumber');
		$accountname = $this->mm->get_accountname_by_accountint($accountnumber);
		
		$detail_rekap = $this->input->post('lapSaldoGr-detail_rekap');
		
		$site_name = $this->mm->get_site_name();
		
		$idkarat = $this->input->post('lapSaldoGr-idkarat');
		if($idkarat == 'All'){
			$karat = $this->mk->get_karat_srt();
		}else{
			$karat = $this->mk->get_karat_by_id($idkarat);
		}
		
		$coa_from = $accountnumber;
		$coa_to = $accountnumber;
		
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		$jenis_karat = array("170002", "170003");
		
		if($coa_from == '170002'){
			$tabel_name = 'gold_mutasi_reparasi';
		}else if($coa_from == '170003'){
			$tabel_name = 'gold_mutasi_pengadaan';
		}
		
		$data['view'] = '';
		
		if($detail_rekap == 'R'){
			if(in_array($coa_from, $jenis_karat)){
				$data['view'] .= '<div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui purple button" href="'.base_url().'index.php/lapSaldoGr/excel/'.$dari_tanggal.'/'.$sampai_tanggal.'/'.$accountnumber.'/'.$idkarat.'/'.$detail_rekap.'"><i class="file excel icon"></i> Export to Excel</a><a class="ui facebook button" href="'.base_url().'index.php/lapSaldoGr/pdf/'.$dari_tanggal.'/'.$sampai_tanggal.'/'.$accountnumber.'/'.$idkarat.'/'.$detail_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><table class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:100px">Karat</th><th>Account Name</th><th>Beginning Balance</th><th>Debit</th><th>Credit</th><th>Ending Balance</th></tr></thead><tbody>';
				
				foreach($karat as $k){
					$count_data = 0;
					
					$flag_tulis = TRUE;
					$flag_kurs = array();
					$flag_saldo_awal = array();
					$flag_mutasi_in = array();
					$flag_mutasi_out = array();
					$flag_saldo_akhir = array();
					
					$mutasi_in = array();
					$mutasi_out = array();
					
					$sum_bb = 0;
					$sum_d = 0;
					$sum_k = 0;
					$sum_eb = 0;
					
					foreach($kas_account as $ka){
						$saldo_awal_kurs = array();
						$saldo_akhir_kurs = array();
						
						$acc_number = $ka->accountnumber;
						$acc_group = $ka->accountgroup;
						$id_kurs = $k->id;
						$type = $ka->type;
						
						/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
						
						$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						
						/*--------------------------------------------------*/
						
						/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
						
						$saldo_awal_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$from_date);
						$saldo_awal_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$from_date);
						$saldo_akhir_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$to_date);
						$saldo_akhir_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$to_date);
						
						foreach($saldo_awal_d as $mtyd){
							$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] + $mtyd->total_mutasi;
						}
						
						foreach($saldo_awal_k as $mtyk){
							$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] - $mtyk->total_mutasi;
						}
						
						foreach($saldo_akhir_d as $mttd){
							$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] + $mttd->total_mutasi;
						}
					
						foreach($saldo_akhir_k as $mttk){
							$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] - $mttk->total_mutasi;
						}
						
						/*---------------------------------------------------*/
						
						/*---- Menentukan Mutasi Masuk dan Mutasi Keluar ----*/
				
						$mutasi_d = $this->mm->get_mutasi_repgros_in($tabel_name,$acc_number,$from_date,$to_date);	
						$mutasi_k = $this->mm->get_mutasi_repgros_out($tabel_name,$acc_number,$from_date,$to_date);
						
						/*---------------------------------------------------*/
						
						/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
						
						$flag_kurs[$k->id] = FALSE;
						$flag_saldo_awal[$k->id] = FALSE;
						$flag_mutasi_in[$k->id] = FALSE;
						$flag_mutasi_out[$k->id] = FALSE;
						$flag_saldo_akhir[$k->id] = FALSE;
						
						if($saldo_awal_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_awal[$k->id] = TRUE;
						}
						
						if($saldo_akhir_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_akhir[$k->id] = TRUE;
						}
						
						foreach($mutasi_d as $md){
							$flag_kurs[$k->id] = TRUE;
							$flag_mutasi_in[$k->id] = TRUE;
							$mutasi_in[$k->id] = $md->total_mutasi;
						}
						
						foreach($mutasi_k as $mk){
							$flag_kurs[$k->id] = TRUE;
							$flag_mutasi_out[$k->id] = TRUE;
							$mutasi_out[$k->id] = $mk->total_mutasi;
						}
						
						/*----------------------------------------------------*/
						
						/*-------- Menampilkan Data Dalam Tabel Report -------*/
						
						if($flag_kurs[$k->id] == TRUE){
							$count_data = $count_data + 1;
							
							if($flag_tulis == TRUE){
								$data['view'] .= '<tr><td class="center aligned">'.$k->karat_name.'</td>';
								$flag_tulis = FALSE;
							}else{
								$data['view'] .= '<tr><td class="center aligned"></td>';
							}
							
							$sa = $saldo_awal_kurs[$k->id];
							if($sa != 0 && $sa != '' && $sa != -0){
								$data['view'] .= '<td>'.$ka->accountname.'</td>';
								$data['view'] .= '<td class="right aligned">'.number_format($saldo_awal_kurs[$k->id], 3).'</td>';
								
								$sum_bb = $sum_bb + $saldo_awal_kurs[$k->id];
							}else{
								$data['view'] .= '<td>'.$ka->accountname.'</td><td class="right aligned">-</td>';
							}
							
							if($flag_mutasi_in[$k->id] == TRUE){
								$data['view'] .= '<td class="right aligned">'.number_format($mutasi_in[$k->id], 3).'</td>';
								$sum_d = $sum_d + $mutasi_in[$k->id];
							}else{
								$data['view'] .= '<td class="right aligned">-</td>';
							}
							
							if($flag_mutasi_out[$k->id] == TRUE){
								$data['view'] .= '<td class="right aligned">'.number_format($mutasi_out[$k->id], 3).'</td>';
								$sum_k = $sum_k + $mutasi_out[$k->id];
							}else{
								$data['view'] .= '<td class="right aligned">-</td>';
							}
							
							$sak = $saldo_akhir_kurs[$k->id];
							if($sak != 0 && $sak != '' && $sak != -0){
								$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs[$k->id], 3).'</td>';
								
								$sum_eb = $sum_eb + $saldo_akhir_kurs[$k->id];
							}else{
								$data['view'] .= '<td class="right aligned">-</td>';
							}
							
							$data['view'] .= '</tr>';
						}
					}
					
					if($coa_from != $coa_to){
						if($count_data >= 1){
							$data['view'] .= '<tr><td colspan="2" style="border-top:2px dashed #000;border-bottom:2px dashed #000">Total</td><td class="right aligned" style="border-top:2px dashed #000;border-bottom:2px dashed #000">'.number_format($sum_bb, 3).'</td><td class="right aligned" style="border-top:2px dashed #000;border-bottom:2px dashed #000">'.number_format($sum_d, 3).'</td><td class="right aligned" style="border-top:2px dashed #000;border-bottom:2px dashed #000">'.number_format($sum_k, 3).'</td><td class="right aligned" style="border-top:2px dashed #000;border-bottom:2px dashed #000">'.number_format($sum_eb, 3).'</td></tr>';
							$data['view'] .= '<tr><td colspan="6" style="border-bottom:2px dashed #000"><span style="visibility:hidden">-</span></td><tr>';
						}
					}
				}
			}else{
				$data['view'] .= '<div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui purple button" href="'.base_url().'index.php/lapSaldoGr/excel/'.$dari_tanggal.'/'.$sampai_tanggal.'/'.$accountnumber.'/'.$idkarat.'/'.$detail_rekap.'"><i class="file excel icon"></i> Export to Excel</a><a class="ui facebook button" href="'.base_url().'index.php/lapSaldoGr/pdf/'.$dari_tanggal.'/'.$sampai_tanggal.'/'.$accountnumber.'/'.$idkarat.'/'.$detail_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><table class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:100px">Karat</th><th>Account Name</th><th>Beginning Balance</th><th>Debit</th><th>Credit</th><th>Ending Balance</th></tr></thead><tbody>';
			
				foreach($karat as $k){
					$count_data = 0;
					
					$flag_tulis = TRUE;
					$flag_kurs = array();
					$flag_saldo_awal = array();
					$flag_mutasi_in = array();
					$flag_mutasi_out = array();
					$flag_saldo_akhir = array();
					
					$mutasi_in = array();
					$mutasi_out = array();
					
					$sum_bb = 0;
					$sum_d = 0;
					$sum_k = 0;
					$sum_eb = 0;
					
					foreach($kas_account as $ka){
						$saldo_awal_kurs = array();
						$saldo_akhir_kurs = array();
						
						$acc_number = $ka->accountnumber;
						$acc_group = $ka->accountgroup;
						$id_kurs = $k->id;
						$type = $ka->type;
						
						/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
						
						$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						
						/*--------------------------------------------------*/
						
						/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
						
						$saldo_awal_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$from_date,$id_kurs);
						$saldo_awal_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$from_date,$id_kurs);
						$saldo_akhir_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$to_date,$id_kurs);
						$saldo_akhir_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$to_date,$id_kurs);
						
						foreach($saldo_awal_d as $mtyd){
							$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] + $mtyd->total_mutasi;
						}
						
						foreach($saldo_awal_k as $mtyk){
							$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] - $mtyk->total_mutasi;
						}
						
						foreach($saldo_akhir_d as $mttd){
							$saldo_akhir_kurs[$mttd->idkarat] = $saldo_akhir_kurs[$mttd->idkarat] + $mttd->total_mutasi;
						}
					
						foreach($saldo_akhir_k as $mttk){
							$saldo_akhir_kurs[$mttk->idkarat] = $saldo_akhir_kurs[$mttk->idkarat] - $mttk->total_mutasi;
						}	
						
						/*---------------------------------------------------*/
						
						/*---- Menentukan Mutasi Masuk dan Mutasi Keluar ----*/
				
						$mutasi_d = $this->mm->get_mutasi_gr_in($acc_number,$from_date,$to_date);	
						$mutasi_k = $this->mm->get_mutasi_gr_out($acc_number,$from_date,$to_date);
						
						/*---------------------------------------------------*/
						
						/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
						
						$flag_kurs[$k->id] = FALSE;
						$flag_saldo_awal[$k->id] = FALSE;
						$flag_mutasi_in[$k->id] = FALSE;
						$flag_mutasi_out[$k->id] = FALSE;
						$flag_saldo_akhir[$k->id] = FALSE;
						
						if($saldo_awal_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_awal[$k->id] = TRUE;
						}
						
						if($saldo_akhir_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_akhir[$k->id] = TRUE;
						}
						
						foreach($mutasi_d as $md){
							if($md->idkarat == $k->id){
								$flag_kurs[$k->id] = TRUE;
								$flag_mutasi_in[$k->id] = TRUE;
								$mutasi_in[$k->id] = $md->total_mutasi;
							}
						}
						
						foreach($mutasi_k as $mk){
							if($mk->idkarat == $k->id){
								$flag_kurs[$k->id] = TRUE;
								$flag_mutasi_out[$k->id] = TRUE;
								$mutasi_out[$k->id] = $mk->total_mutasi;
							}
						}
						
						/*----------------------------------------------------*/
						
						/*-------- Menampilkan Data Dalam Tabel Report -------*/
						
						if($flag_kurs[$k->id] == TRUE){
							$count_data = $count_data + 1;
							
							if($flag_tulis == TRUE){
								$data['view'] .= '<tr><td class="center aligned">'.$k->karat_name.'</td>';
								$flag_tulis = FALSE;
							}else{
								$data['view'] .= '<tr><td class="center aligned"></td>';
							}
							
							$sa = $saldo_awal_kurs[$k->id];
							if($sa != 0 && $sa != '' && $sa != -0){
								$data['view'] .= '<td>'.$ka->accountname.'</td>';
								$data['view'] .= '<td class="right aligned">'.number_format($saldo_awal_kurs[$k->id], 3).'</td>';
								
								$sum_bb = $sum_bb + $saldo_awal_kurs[$k->id];
							}else{
								$data['view'] .= '<td>'.$ka->accountname.'</td><td class="right aligned">-</td>';
							}
							
							if($flag_mutasi_in[$k->id] == TRUE){
								$data['view'] .= '<td class="right aligned">'.number_format($mutasi_in[$k->id], 3).'</td>';
								$sum_d = $sum_d + $mutasi_in[$k->id];
							}else{
								$data['view'] .= '<td class="right aligned">-</td>';
							}
							
							if($flag_mutasi_out[$k->id] == TRUE){
								$data['view'] .= '<td class="right aligned">'.number_format($mutasi_out[$k->id], 3).'</td>';
								$sum_k = $sum_k + $mutasi_out[$k->id];
							}else{
								$data['view'] .= '<td class="right aligned">-</td>';
							}
							
							$sak = $saldo_akhir_kurs[$k->id];
							if($sak != 0 && $sak != '' && $sak != -0){
								$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs[$k->id], 3).'</td>';
								
								$sum_eb = $sum_eb + $saldo_akhir_kurs[$k->id];
							}else{
								$data['view'] .= '<td class="right aligned">-</td>';
							}
							
							$data['view'] .= '</tr>';
						}
					}
					
					if($coa_from != $coa_to){
						if($count_data >= 1){
							$data['view'] .= '<tr><td colspan="2" style="border-top:2px dashed #000;border-bottom:2px dashed #000">Total</td><td class="right aligned" style="border-top:2px dashed #000;border-bottom:2px dashed #000">'.number_format($sum_bb, 3).'</td><td class="right aligned" style="border-top:2px dashed #000;border-bottom:2px dashed #000">'.number_format($sum_d, 3).'</td><td class="right aligned" style="border-top:2px dashed #000;border-bottom:2px dashed #000">'.number_format($sum_k, 3).'</td><td class="right aligned" style="border-top:2px dashed #000;border-bottom:2px dashed #000">'.number_format($sum_eb, 3).'</td></tr>';
							$data['view'] .= '<tr><td colspan="6" style="border-bottom:2px dashed #000"><span style="visibility:hidden">-</span></td><tr>';
						}
					}
				}
			}
		}else{
			if (in_array($coa_from, $jenis_karat)){
				$data['view'] .= '<div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui purple button" href="'.base_url().'index.php/lapSaldoGr/excel/'.$dari_tanggal.'/'.$sampai_tanggal.'/'.$accountnumber.'/'.$idkarat.'/'.$detail_rekap.'"><i class="file excel icon"></i> Export to Excel</a><a class="ui facebook button" href="'.base_url().'index.php/lapSaldoGr/pdf/'.$dari_tanggal.'/'.$sampai_tanggal.'/'.$accountnumber.'/'.$idkarat.'/'.$detail_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><table class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:50px">Karat</th><th>Date</th><th>Description</th><th>Voucher Code</th><th>Debit</th><th>Credit</th><th>Balance</th></tr></thead><tbody>';
			
				foreach($kas_account as $ka){
					foreach($karat as $k){
						$saldo_awal_kurs = array();
						
						$flag_tulis = TRUE;
						$flag_kurs = array();
						$flag_saldo_awal = array();
						$flag_mutasi = array();

						$acc_number = $ka->accountnumber;
						$acc_group = $ka->accountgroup;
						$id_kurs = $k->id;
						$type = $ka->type;
						
						/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
						
						$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						
						/*--------------------------------------------------*/
					
						/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
						$saldo_awal_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$from_date);
						$saldo_awal_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$from_date);
						
						foreach($saldo_awal_d as $mtyd){
							$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] + $mtyd->total_mutasi;
						}
						
						foreach($saldo_awal_k as $mtyk){
							$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] - $mtyk->total_mutasi;
						}
								
						/*---------------------------------------------------*/
					
						/*---------- Mengambil Data Mutasi Transaksi ----------*/
						
						$mutasi_transaksi = $this->mm->get_report_mutasi_repgros($tabel_name,$from_date,$to_date);
						
						/*---------------------------------------------------*/
					
						/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
						
						$flag_kurs[$k->id] = FALSE;
						$flag_saldo_awal[$k->id] = FALSE;
						$flag_mutasi[$k->id] = FALSE;
						
						if($saldo_awal_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_awal[$k->id] = TRUE;
						}
						
						foreach($mutasi_transaksi as $mk){
							$flag_kurs[$k->id] = TRUE;
							$flag_mutasi[$k->id] = TRUE;
						}
						
						/*----------------------------------------------------*/
					
						/*-------- Menampilkan Data Dalam Tabel Report -------*/
						
						if($flag_kurs[$k->id] == TRUE){
							$coa_data = $this->mm->get_accountname_by_accountint($ka->accountnumberint);
							
							$running_balance = $saldo_awal_kurs[$k->id];
							$total_debet = 0;
							$total_kredit = 0;
							
							$sa = $saldo_awal_kurs[$k->id];
							if($sa == 0 || $sa == '' || $sa == -0){
								$data['view'] .= '<tr><td class="center aligned" width="50px">'.$k->karat_name.'</td><td colspan="5">'.$coa_data.'</td><td class="right aligned">-</td>';
							}else{
								$data['view'] .= '<tr><td class="center aligned" width="50px">'.$k->karat_name.'</td><td colspan="5">'.$coa_data.'</td><td class="right aligned">'.number_format($saldo_awal_kurs[$k->id], 3).'</td>';
							}						
							
							foreach($mutasi_transaksi as $mk){								
								$trans_date = strtotime($mk->trans_date);
								$trans_date = date('d/m/Y',$trans_date);
								
								if($mk->toaccount == $acc_number){
									$debet_val = number_format($mk->total_konv, 3);
									$kredit_val = '-';
									
									$running_balance = $running_balance + $mk->total_konv;
									
									$total_debet = $total_debet + $mk->total_konv;
								}else{
									$debet_val = '-';
									$kredit_val = number_format($mk->total_konv, 3);
									
									$running_balance = $running_balance - $mk->total_konv;
									
									$total_kredit = $total_kredit + $mk->total_konv;
								}
								
								$data['view'] .= '<tr><td></td><td>'.$trans_date.'</td><td>'.$mk->description.'</td><td>'.$mk->id_pengiriman.'</td><td class="right aligned">'.$debet_val.'</td><td class="right aligned">'.$kredit_val.'</td><td class="right aligned">'.number_format($running_balance, 3).'</td></tr>';
							}
							
							$data['view'] .= '<tr><td colspan="4"></td><td class="right aligned" style="border-top: 2px dashed #000">'.number_format($total_debet, 3).'</td><td class="right aligned" style="border-top: 2px dashed #000">'.number_format($total_kredit, 3).'</td><td></td></tr>';
							$data['view'] .= '<tr><td colspan="7" style="visibility:hidden; color:#FFF">-</td></tr>';
						}
					}
				}
			}else{
				$data['view'] .= '<div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui purple button" href="'.base_url().'index.php/lapSaldoGr/excel/'.$dari_tanggal.'/'.$sampai_tanggal.'/'.$accountnumber.'/'.$idkarat.'/'.$detail_rekap.'"><i class="file excel icon"></i> Export to Excel</a><a class="ui facebook button" href="'.base_url().'index.php/lapSaldoGr/pdf/'.$dari_tanggal.'/'.$sampai_tanggal.'/'.$accountnumber.'/'.$idkarat.'/'.$detail_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><table class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:50px">Karat</th><th>Date</th><th>Description</th><th>Voucher Code</th><th>Debit</th><th>Credit</th><th>Balance</th></tr></thead><tbody>';
			
				foreach($kas_account as $ka){
					foreach($karat as $k){
						$saldo_awal_kurs = array();
						
						$flag_tulis = TRUE;
						$flag_kurs = array();
						$flag_saldo_awal = array();
						$flag_mutasi = array();

						$acc_number = $ka->accountnumber;
						$acc_group = $ka->accountgroup;
						$id_kurs = $k->id;
						$type = $ka->type;
						
						/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
						
						$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						
						/*--------------------------------------------------*/
					
						/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
						
						$saldo_awal_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$from_date,$id_kurs);
						$saldo_awal_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$from_date,$id_kurs);
						
						foreach($saldo_awal_d as $mtyd){
							$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] + $mtyd->total_mutasi;
						}
						
						foreach($saldo_awal_k as $mtyk){
							$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] - $mtyk->total_mutasi;
						}
								
						/*---------------------------------------------------*/
					
						/*---------- Mengambil Data Mutasi Transaksi ----------*/
						
						$mutasi_transaksi = $this->mm->get_report_mutasi_gr_by_kurs($id_kurs,$from_date,$to_date);
						
						/*---------------------------------------------------*/
					
						/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
						
						$flag_kurs[$k->id] = FALSE;
						$flag_saldo_awal[$k->id] = FALSE;
						$flag_mutasi[$k->id] = FALSE;
						
						if($saldo_awal_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_awal[$k->id] = TRUE;
						}
						
						foreach($mutasi_transaksi as $mk){
							if($mk->fromaccount == $acc_number || $mk->toaccount == $acc_number){
								$flag_kurs[$k->id] = TRUE;
								$flag_mutasi[$k->id] = TRUE;
							}
						}
						
						/*----------------------------------------------------*/
					
						/*-------- Menampilkan Data Dalam Tabel Report -------*/
						
						if($flag_kurs[$k->id] == TRUE){
							$coa_data = $this->mm->get_accountname_by_accountint($ka->accountnumberint);
							
							$running_balance = $saldo_awal_kurs[$k->id];
							$total_debet = 0;
							$total_kredit = 0;
							
							$sa = $saldo_awal_kurs[$k->id];
							if($sa == 0 || $sa == '' || $sa == -0){
								$data['view'] .= '<tr><td class="center aligned" width="50px">'.$k->karat_name.'</td><td colspan="5">'.$coa_data.'</td><td class="right aligned">-</td>';
							}else{
								$data['view'] .= '<tr><td class="center aligned" width="50px">'.$k->karat_name.'</td><td colspan="5">'.$coa_data.'</td><td class="right aligned">'.number_format($saldo_awal_kurs[$k->id], 3).'</td>';
							}						
							
							foreach($mutasi_transaksi as $mk){
								if($mk->fromaccount == $acc_number || $mk->toaccount == $acc_number){
									$trans_date = strtotime($mk->transdate);
									$trans_date = date('d/m/Y',$trans_date);
									
									if($mk->toaccount == $acc_number){
										$debet_val = number_format($mk->value, 3);
										$kredit_val = '-';
										
										$running_balance = $running_balance + $mk->value;
										
										$total_debet = $total_debet + $mk->value;
									}else{
										$debet_val = '-';
										$kredit_val = number_format($mk->value, 3);
										
										$running_balance = $running_balance - $mk->value;
										
										$total_kredit = $total_kredit + $mk->value;
									}
									
									$data['view'] .= '<tr><td></td><td>'.$trans_date.'</td><td>'.$mk->description.'</td><td>'.$mk->idmutasi.'</td><td class="right aligned">'.$debet_val.'</td><td class="right aligned">'.$kredit_val.'</td><td class="right aligned">'.number_format($running_balance, 3).'</td></tr>';
								}
							}
							
							$data['view'] .= '<tr><td colspan="4"></td><td class="right aligned" style="border-top: 2px dashed #000">'.number_format($total_debet, 3).'</td><td class="right aligned" style="border-top: 2px dashed #000">'.number_format($total_kredit, 3).'</td><td></td></tr>';
							$data['view'] .= '<tr><td colspan="7" style="visibility:hidden; color:#FFF">-</td></tr>';
						}
					}
				}
			}
		}
		
		$data['view'] .= '</tbody></table>';
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function pdf($from_date,$to_date,$accountnumber,$idkarat,$detail_rekap){
		date_default_timezone_set("Asia/Jakarta");
		$from_date = str_replace('%20',' ',$from_date);
		$to_date = str_replace('%20',' ',$to_date);
		
		$dari_tanggal = $from_date;
		$sampai_tanggal = $to_date;
		
		$stockFromTime = $this->date_to_format($from_date);
		$stockToTime = $this->date_to_format($to_date);
		
		$from_date = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_date = date("Y-m-d",$stockToTime).' 23:59:59';
		
		if($dari_tanggal == $sampai_tanggal){
			$tanggal_tulis = $dari_tanggal;
		}else{
			$tanggal_tulis = $dari_tanggal.' s.d '.$sampai_tanggal;
		}
		
		$accountname = $this->mm->get_accountname_by_accountint($accountnumber);
		$site_name = $this->mm->get_site_name();
		
		if($idkarat == 'All'){
			$karat = $this->mk->get_karat_srt();
			$karat_tulis = 'All Karat';
		}else{
			$karat = $this->mk->get_karat_by_id($idkarat);
			$karat_tulis = $this->mk->get_karat_name_by_id($idkarat);
			$karat_tulis = $karat_tulis;
		}
		
		$coa_from = $accountnumber;
		$coa_to = $accountnumber;
		
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		$jenis_karat = array("170002", "170003");
		
		if($coa_from == '170002'){
			$tabel_name = 'gold_mutasi_reparasi';
		}else if($coa_from == '170003'){
			$tabel_name = 'gold_mutasi_pengadaan';
		}
		
		$site_name = $this->mm->get_site_name();
		
		$data['view'] = '';
		
		if($detail_rekap == 'R'){
			if(in_array($coa_from, $jenis_karat)){
				$data['view'] .= '<div class="sixteen wide centered column center aligned" style="font-weight:bold;text-align:center;font-size:13px;font-family:samsungone"><span style="text-align">Laporan Saldo Emas Rekap, Cabang '.$site_name.'</span><br><span>Tanggal '.$tanggal_tulis.'</span><br><span>'.$accountname.', '.$karat_tulis.'</span><br><span style="visibility:hidden">-</span></div><table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:100px" class="th-5">Karat</th><th class="th-5">Account Name</th><th class="th-5">Beginning Balance</th><th class="th-5">Debit</th><th class="th-5">Credit</th><th class="th-5">Ending Balance</th></tr></thead><tbody>';
				
				foreach($karat as $k){
					$count_data = 0;
					
					$flag_tulis = TRUE;
					$flag_kurs = array();
					$flag_saldo_awal = array();
					$flag_mutasi_in = array();
					$flag_mutasi_out = array();
					$flag_saldo_akhir = array();
					
					$mutasi_in = array();
					$mutasi_out = array();
					
					$sum_bb = 0;
					$sum_d = 0;
					$sum_k = 0;
					$sum_eb = 0;
					
					foreach($kas_account as $ka){
						$saldo_awal_kurs = array();
						$saldo_akhir_kurs = array();
						
						$acc_number = $ka->accountnumber;
						$acc_group = $ka->accountgroup;
						$id_kurs = $k->id;
						$type = $ka->type;
						
						/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
						
						$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						
						/*--------------------------------------------------*/
						
						/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
						
						$saldo_awal_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$from_date);
						$saldo_awal_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$from_date);
						$saldo_akhir_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$to_date);
						$saldo_akhir_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$to_date);
						
						foreach($saldo_awal_d as $mtyd){
							$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] + $mtyd->total_mutasi;
						}
						
						foreach($saldo_awal_k as $mtyk){
							$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] - $mtyk->total_mutasi;
						}
						
						foreach($saldo_akhir_d as $mttd){
							$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] + $mttd->total_mutasi;
						}
					
						foreach($saldo_akhir_k as $mttk){
							$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] - $mttk->total_mutasi;
						}
						
						/*---------------------------------------------------*/
						
						/*---- Menentukan Mutasi Masuk dan Mutasi Keluar ----*/
				
						$mutasi_d = $this->mm->get_mutasi_repgros_in($tabel_name,$acc_number,$from_date,$to_date);	
						$mutasi_k = $this->mm->get_mutasi_repgros_out($tabel_name,$acc_number,$from_date,$to_date);
						
						/*---------------------------------------------------*/
						
						/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
						
						$flag_kurs[$k->id] = FALSE;
						$flag_saldo_awal[$k->id] = FALSE;
						$flag_mutasi_in[$k->id] = FALSE;
						$flag_mutasi_out[$k->id] = FALSE;
						$flag_saldo_akhir[$k->id] = FALSE;
						
						if($saldo_awal_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_awal[$k->id] = TRUE;
						}
						
						if($saldo_akhir_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_akhir[$k->id] = TRUE;
						}
						
						foreach($mutasi_d as $md){
							$flag_kurs[$k->id] = TRUE;
							$flag_mutasi_in[$k->id] = TRUE;
							$mutasi_in[$k->id] = $md->total_mutasi;
						}
						
						foreach($mutasi_k as $mk){
							$flag_kurs[$k->id] = TRUE;
							$flag_mutasi_out[$k->id] = TRUE;
							$mutasi_out[$k->id] = $mk->total_mutasi;
						}
						
						/*----------------------------------------------------*/
						
						/*-------- Menampilkan Data Dalam Tabel Report -------*/
						
						if($flag_kurs[$k->id] == TRUE){
							$count_data = $count_data + 1;
							
							if($flag_tulis == TRUE){
								$data['view'] .= '<tr><td class="center aligned">'.$k->karat_name.'</td>';
								$flag_tulis = FALSE;
							}else{
								$data['view'] .= '<tr><td class="center aligned"></td>';
							}
							
							$sa = $saldo_awal_kurs[$k->id];
							if($sa != 0 && $sa != '' && $sa != -0){
								$data['view'] .= '<td>'.$ka->accountname.'</td>';
								$data['view'] .= '<td class="right-aligned">'.number_format($saldo_awal_kurs[$k->id], 3).'</td>';
								
								$sum_bb = $sum_bb + $saldo_awal_kurs[$k->id];
							}else{
								$data['view'] .= '<td>'.$ka->accountname.'</td><td class="right-aligned">-</td>';
							}
							
							if($flag_mutasi_in[$k->id] == TRUE){
								$data['view'] .= '<td class="right-aligned">'.number_format($mutasi_in[$k->id], 3).'</td>';
								$sum_d = $sum_d + $mutasi_in[$k->id];
							}else{
								$data['view'] .= '<td class="right-aligned">-</td>';
							}
							
							if($flag_mutasi_out[$k->id] == TRUE){
								$data['view'] .= '<td class="right-aligned">'.number_format($mutasi_out[$k->id], 3).'</td>';
								$sum_k = $sum_k + $mutasi_out[$k->id];
							}else{
								$data['view'] .= '<td class="right-aligned">-</td>';
							}
							
							$sak = $saldo_akhir_kurs[$k->id];
							if($sak != 0 && $sak != '' && $sak != -0){
								$data['view'] .= '<td class="right-aligned">'.number_format($saldo_akhir_kurs[$k->id], 3).'</td>';
								
								$sum_eb = $sum_eb + $saldo_akhir_kurs[$k->id];
							}else{
								$data['view'] .= '<td class="right-aligned">-</td>';
							}
							
							$data['view'] .= '</tr>';
						}
					}
					
					if($coa_from != $coa_to){
						if($count_data >= 1){
							$data['view'] .= '<tr><td colspan="2" style="border-top:1px dotted #000;border-bottom:1px dotted #000">Total</td><td class="right-aligned" style="border-top:1px dotted #000;border-bottom:1px dotted #000">'.number_format($sum_bb, 3).'</td><td class="right-aligned" style="border-top:1px dotted #000;border-bottom:1px dotted #000">'.number_format($sum_d, 3).'</td><td class="right-aligned" style="border-top:1px dotted #000;border-bottom:1px dotted #000">'.number_format($sum_k, 3).'</td><td class="right-aligned" style="border-top:1px dotted #000;border-bottom:1px dotted #000">'.number_format($sum_eb, 3).'</td></tr>';
							$data['view'] .= '<tr><td colspan="6" style="border-bottom:1px dotted #000"><span style="visibility:hidden">-</span></td><tr>';
						}
					}
				}
			}else{
				$data['view'] .= '<div class="sixteen wide centered column center aligned" style="font-weight:bold;text-align:center;font-size:13px;font-family:samsungone"><span style="text-align">Laporan Saldo Emas Rekap, Cabang '.$site_name.'</span><br><span>Tanggal '.$tanggal_tulis.'</span><br><span>'.$accountname.', '.$karat_tulis.'</span><br><span style="visibility:hidden">-</span></div><table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:100px" class="th-5">Karat</th><th class="th-5">Account Name</th><th class="th-5">Beginning Balance</th><th class="th-5">Debit</th><th class="th-5">Credit</th><th class="th-5">Ending Balance</th></tr></thead><tbody>';
			
				foreach($karat as $k){
					$count_data = 0;
					
					$flag_tulis = TRUE;
					$flag_kurs = array();
					$flag_saldo_awal = array();
					$flag_mutasi_in = array();
					$flag_mutasi_out = array();
					$flag_saldo_akhir = array();
					
					$mutasi_in = array();
					$mutasi_out = array();
					
					$sum_bb = 0;
					$sum_d = 0;
					$sum_k = 0;
					$sum_eb = 0;
					
					foreach($kas_account as $ka){
						$saldo_awal_kurs = array();
						$saldo_akhir_kurs = array();
						
						$acc_number = $ka->accountnumber;
						$acc_group = $ka->accountgroup;
						$id_kurs = $k->id;
						$type = $ka->type;
						
						/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
						
						$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						
						/*--------------------------------------------------*/
						
						/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
						
						$saldo_awal_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$from_date,$id_kurs);
						$saldo_awal_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$from_date,$id_kurs);
						$saldo_akhir_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$to_date,$id_kurs);
						$saldo_akhir_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$to_date,$id_kurs);
						
						foreach($saldo_awal_d as $mtyd){
							$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] + $mtyd->total_mutasi;
						}
						
						foreach($saldo_awal_k as $mtyk){
							$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] - $mtyk->total_mutasi;
						}
						
						foreach($saldo_akhir_d as $mttd){
							$saldo_akhir_kurs[$mttd->idkarat] = $saldo_akhir_kurs[$mttd->idkarat] + $mttd->total_mutasi;
						}
					
						foreach($saldo_akhir_k as $mttk){
							$saldo_akhir_kurs[$mttk->idkarat] = $saldo_akhir_kurs[$mttk->idkarat] - $mttk->total_mutasi;
						}	
						
						/*---------------------------------------------------*/
						
						/*---- Menentukan Mutasi Masuk dan Mutasi Keluar ----*/
				
						$mutasi_d = $this->mm->get_mutasi_gr_in($acc_number,$from_date,$to_date);	
						$mutasi_k = $this->mm->get_mutasi_gr_out($acc_number,$from_date,$to_date);
						
						/*---------------------------------------------------*/
						
						/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
						
						$flag_kurs[$k->id] = FALSE;
						$flag_saldo_awal[$k->id] = FALSE;
						$flag_mutasi_in[$k->id] = FALSE;
						$flag_mutasi_out[$k->id] = FALSE;
						$flag_saldo_akhir[$k->id] = FALSE;
						
						if($saldo_awal_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_awal[$k->id] = TRUE;
						}
						
						if($saldo_akhir_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_akhir[$k->id] = TRUE;
						}
						
						foreach($mutasi_d as $md){
							if($md->idkarat == $k->id){
								$flag_kurs[$k->id] = TRUE;
								$flag_mutasi_in[$k->id] = TRUE;
								$mutasi_in[$k->id] = $md->total_mutasi;
							}
						}
						
						foreach($mutasi_k as $mk){
							if($mk->idkarat == $k->id){
								$flag_kurs[$k->id] = TRUE;
								$flag_mutasi_out[$k->id] = TRUE;
								$mutasi_out[$k->id] = $mk->total_mutasi;
							}
						}
						
						/*----------------------------------------------------*/
						
						/*-------- Menampilkan Data Dalam Tabel Report -------*/
						
						if($flag_kurs[$k->id] == TRUE){
							$count_data = $count_data + 1;
							
							if($flag_tulis == TRUE){
								$data['view'] .= '<tr><td class="center aligned">'.$k->karat_name.'</td>';
								$flag_tulis = FALSE;
							}else{
								$data['view'] .= '<tr><td class="center aligned"></td>';
							}
							
							$sa = $saldo_awal_kurs[$k->id];
							if($sa != 0 && $sa != '' && $sa != -0){
								$data['view'] .= '<td>'.$ka->accountname.'</td>';
								$data['view'] .= '<td class="right-aligned">'.number_format($saldo_awal_kurs[$k->id], 3).'</td>';
								
								$sum_bb = $sum_bb + $saldo_awal_kurs[$k->id];
							}else{
								$data['view'] .= '<td>'.$ka->accountname.'</td><td class="right-aligned">-</td>';
							}
							
							if($flag_mutasi_in[$k->id] == TRUE){
								$data['view'] .= '<td class="right-aligned">'.number_format($mutasi_in[$k->id], 3).'</td>';
								$sum_d = $sum_d + $mutasi_in[$k->id];
							}else{
								$data['view'] .= '<td class="right-aligned">-</td>';
							}
							
							if($flag_mutasi_out[$k->id] == TRUE){
								$data['view'] .= '<td class="right-aligned">'.number_format($mutasi_out[$k->id], 3).'</td>';
								$sum_k = $sum_k + $mutasi_out[$k->id];
							}else{
								$data['view'] .= '<td class="right-aligned">-</td>';
							}
							
							$sak = $saldo_akhir_kurs[$k->id];
							if($sak != 0 && $sak != '' && $sak != -0){
								$data['view'] .= '<td class="right-aligned">'.number_format($saldo_akhir_kurs[$k->id], 3).'</td>';
								
								$sum_eb = $sum_eb + $saldo_akhir_kurs[$k->id];
							}else{
								$data['view'] .= '<td class="right-aligned">-</td>';
							}
							
							$data['view'] .= '</tr>';
						}
					}
					
					if($coa_from != $coa_to){
						if($count_data >= 1){
							$data['view'] .= '<tr><td colspan="2" style="border-top:1px dotted #000;border-bottom:1px dotted #000">Total</td><td class="right-aligned" style="border-top:1px dotted #000;border-bottom:1px dotted #000">'.number_format($sum_bb, 3).'</td><td class="right-aligned" style="border-top:1px dotted #000;border-bottom:1px dotted #000">'.number_format($sum_d, 3).'</td><td class="right-aligned" style="border-top:1px dotted #000;border-bottom:1px dotted #000">'.number_format($sum_k, 3).'</td><td class="right-aligned" style="border-top:1px dotted #000;border-bottom:1px dotted #000">'.number_format($sum_eb, 3).'</td></tr>';
							$data['view'] .= '<tr><td colspan="6" style="border-bottom:1px dotted #000"><span style="visibility:hidden">-</span></td><tr>';
						}
					}
				}
			}
		}else{
			if (in_array($coa_from, $jenis_karat)){
				$data['view'] .= '<div class="sixteen wide centered column center aligned" style="font-weight:bold;text-align:center;font-size:13px;font-family:samsungone"><span style="text-align">Laporan Saldo Emas Detail, Cabang '.$site_name.'</span><br><span>Tanggal '.$tanggal_tulis.'</span><br><span>'.$accountname.', '.$karat_tulis.'</span><br><span style="visibility:hidden">-</span></div><table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:50px" class="th-5">Karat</th><th class="th-5">Date</th><th class="th-5">Description</th><th class="th-5">Voucher Code</th><th class="th-5">Debit</th><th class="th-5">Credit</th><th class="th-5">Balance</th></tr></thead><tbody>';
			
				foreach($kas_account as $ka){
					foreach($karat as $k){
						$saldo_awal_kurs = array();
						
						$flag_tulis = TRUE;
						$flag_kurs = array();
						$flag_saldo_awal = array();
						$flag_mutasi = array();

						$acc_number = $ka->accountnumber;
						$acc_group = $ka->accountgroup;
						$id_kurs = $k->id;
						$type = $ka->type;
						
						/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
						
						$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						
						/*--------------------------------------------------*/
					
						/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
						$saldo_awal_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$from_date);
						$saldo_awal_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$from_date);
						
						foreach($saldo_awal_d as $mtyd){
							$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] + $mtyd->total_mutasi;
						}
						
						foreach($saldo_awal_k as $mtyk){
							$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] - $mtyk->total_mutasi;
						}
								
						/*---------------------------------------------------*/
					
						/*---------- Mengambil Data Mutasi Transaksi ----------*/
						
						$mutasi_transaksi = $this->mm->get_report_mutasi_repgros($tabel_name,$from_date,$to_date);
						
						/*---------------------------------------------------*/
					
						/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
						
						$flag_kurs[$k->id] = FALSE;
						$flag_saldo_awal[$k->id] = FALSE;
						$flag_mutasi[$k->id] = FALSE;
						
						if($saldo_awal_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_awal[$k->id] = TRUE;
						}
						
						foreach($mutasi_transaksi as $mk){
							$flag_kurs[$k->id] = TRUE;
							$flag_mutasi[$k->id] = TRUE;
						}
						
						/*----------------------------------------------------*/
					
						/*-------- Menampilkan Data Dalam Tabel Report -------*/
						
						if($flag_kurs[$k->id] == TRUE){
							$coa_data = $this->mm->get_accountname_by_accountint($ka->accountnumberint);
							
							$running_balance = $saldo_awal_kurs[$k->id];
							$total_debet = 0;
							$total_kredit = 0;
							
							$sa = $saldo_awal_kurs[$k->id];
							if($sa == 0 || $sa == '' || $sa == -0){
								$data['view'] .= '<tr><td class="center aligned" width="50px">'.$k->karat_name.'</td><td colspan="5">'.$coa_data.'</td><td class="right-aligned">-</td>';
							}else{
								$data['view'] .= '<tr><td class="center aligned" width="50px">'.$k->karat_name.'</td><td colspan="5">'.$coa_data.'</td><td class="right-aligned">'.number_format($saldo_awal_kurs[$k->id], 3).'</td>';
							}						
							
							foreach($mutasi_transaksi as $mk){								
								$trans_date = strtotime($mk->trans_date);
								$trans_date = date('d/m/Y',$trans_date);
								
								if($mk->toaccount == $acc_number){
									$debet_val = number_format($mk->total_konv, 3);
									$kredit_val = '-';
									
									$running_balance = $running_balance + $mk->total_konv;
									
									$total_debet = $total_debet + $mk->total_konv;
								}else{
									$debet_val = '-';
									$kredit_val = number_format($mk->total_konv, 3);
									
									$running_balance = $running_balance - $mk->total_konv;
									
									$total_kredit = $total_kredit + $mk->total_konv;
								}
								
								$data['view'] .= '<tr><td></td><td style="padding:0px 5px;">'.$trans_date.'</td><td>'.$mk->description.'</td><td>'.$mk->id_pengiriman.'</td><td class="right-aligned">'.$debet_val.'</td><td class="right-aligned">'.$kredit_val.'</td><td class="right-aligned">'.number_format($running_balance, 3).'</td></tr>';
							}
							
							$data['view'] .= '<tr><td colspan="4"></td><td class="right-aligned" style="border-top: 1px dotted #000">'.number_format($total_debet, 3).'</td><td class="right-aligned" style="border-top: 1px dotted #000">'.number_format($total_kredit, 3).'</td><td></td></tr>';
							$data['view'] .= '<tr><td colspan="7" style="visibility:hidden; color:#FFF">-</td></tr>';
						}
					}
				}
			}else{
				$data['view'] .= '<div class="sixteen wide centered column center aligned" style="font-weight:bold;text-align:center;font-size:13px;font-family:samsungone"><span style="text-align">Laporan Saldo Emas Detail, Cabang '.$site_name.'</span><br><span>Tanggal '.$tanggal_tulis.'</span><br><span>'.$accountname.', '.$karat_tulis.'</span><br><span style="visibility:hidden">-</span></div><table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:50px" class="th-5">Karat</th><th class="th-5">Date</th><th class="th-5">Description</th><th class="th-5">Voucher Code</th><th class="th-5">Debit</th><th class="th-5">Credit</th><th class="th-5">Balance</th></tr></thead><tbody>';
			
				foreach($kas_account as $ka){
					foreach($karat as $k){
						$saldo_awal_kurs = array();
						
						$flag_tulis = TRUE;
						$flag_kurs = array();
						$flag_saldo_awal = array();
						$flag_mutasi = array();

						$acc_number = $ka->accountnumber;
						$acc_group = $ka->accountgroup;
						$id_kurs = $k->id;
						$type = $ka->type;
						
						/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
						
						$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						
						/*--------------------------------------------------*/
					
						/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
						
						$saldo_awal_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$from_date,$id_kurs);
						$saldo_awal_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$from_date,$id_kurs);
						
						foreach($saldo_awal_d as $mtyd){
							$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] + $mtyd->total_mutasi;
						}
						
						foreach($saldo_awal_k as $mtyk){
							$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] - $mtyk->total_mutasi;
						}
								
						/*---------------------------------------------------*/
					
						/*---------- Mengambil Data Mutasi Transaksi ----------*/
						
						$mutasi_transaksi = $this->mm->get_report_mutasi_gr_by_kurs($id_kurs,$from_date,$to_date);
						
						/*---------------------------------------------------*/
					
						/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
						
						$flag_kurs[$k->id] = FALSE;
						$flag_saldo_awal[$k->id] = FALSE;
						$flag_mutasi[$k->id] = FALSE;
						
						if($saldo_awal_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_awal[$k->id] = TRUE;
						}
						
						foreach($mutasi_transaksi as $mk){
							if($mk->fromaccount == $acc_number || $mk->toaccount == $acc_number){
								$flag_kurs[$k->id] = TRUE;
								$flag_mutasi[$k->id] = TRUE;
							}
						}
						
						/*----------------------------------------------------*/
					
						/*-------- Menampilkan Data Dalam Tabel Report -------*/
						
						if($flag_kurs[$k->id] == TRUE){
							$coa_data = $this->mm->get_accountname_by_accountint($ka->accountnumberint);
							
							$running_balance = $saldo_awal_kurs[$k->id];
							$total_debet = 0;
							$total_kredit = 0;
							
							$sa = $saldo_awal_kurs[$k->id];
							if($sa == 0 || $sa == '' || $sa == -0){
								$data['view'] .= '<tr><td class="center aligned" width="50px">'.$k->karat_name.'</td><td colspan="5">'.$coa_data.'</td><td class="right-aligned">-</td>';
							}else{
								$data['view'] .= '<tr><td class="center aligned" width="50px">'.$k->karat_name.'</td><td colspan="5">'.$coa_data.'</td><td class="right-aligned">'.number_format($saldo_awal_kurs[$k->id], 3).'</td>';
							}						
							
							foreach($mutasi_transaksi as $mk){
								if($mk->fromaccount == $acc_number || $mk->toaccount == $acc_number){
									$trans_date = strtotime($mk->transdate);
									$trans_date = date('d/m/Y',$trans_date);
									
									if($mk->toaccount == $acc_number){
										$debet_val = number_format($mk->value, 3);
										$kredit_val = '-';
										
										$running_balance = $running_balance + $mk->value;
										
										$total_debet = $total_debet + $mk->value;
									}else{
										$debet_val = '-';
										$kredit_val = number_format($mk->value, 3);
										
										$running_balance = $running_balance - $mk->value;
										
										$total_kredit = $total_kredit + $mk->value;
									}
									
									$data['view'] .= '<tr><td></td><td style="padding:0px 5px;">'.$trans_date.'</td><td>'.$mk->description.'</td><td>'.$mk->idmutasi.'</td><td class="right-aligned">'.$debet_val.'</td><td class="right-aligned">'.$kredit_val.'</td><td class="right-aligned">'.number_format($running_balance, 3).'</td></tr>';
								}
							}
							
							$data['view'] .= '<tr><td colspan="4"></td><td class="right-aligned" style="border-top: 1px dotted #000">'.number_format($total_debet, 3).'</td><td class="right-aligned" style="border-top: 1px dotted #000">'.number_format($total_kredit, 3).'</td><td></td></tr>';
							$data['view'] .= '<tr><td colspan="7" style="visibility:hidden; color:#FFF">-</td></tr>';
						}
					}
				}
			}
		}
		
		$data['view'] .= '</tbody></table>';
		
		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		//$pdf = $this->m_pdf->load();
		$pdf = new \Mpdf\Mpdf();
		$pdf->setFooter('{PAGENO} / {nb}');
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right;font-family:samsungone;font-size:11px;">{PAGENO} / {nb}</div>');
		$pdf->AddPage('P');
        $pdf->WriteHTML($html);
		
        $pdf->Output("Laporan Saldo Emas.pdf", "I");
	}
	
	public function excel($from_date,$to_date,$accountnumber,$idkarat,$detail_rekap){
		date_default_timezone_set("Asia/Jakarta");
		$from_date = str_replace('%20',' ',$from_date);
		$to_date = str_replace('%20',' ',$to_date);
		
		$dari_tanggal = $from_date;
		$sampai_tanggal = $to_date;
		
		$stockFromTime = $this->date_to_format($from_date);
		$stockToTime = $this->date_to_format($to_date);
		
		$from_date = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_date = date("Y-m-d",$stockToTime).' 23:59:59';
		
		if($dari_tanggal == $sampai_tanggal){
			$tanggal_tulis = $dari_tanggal;
		}else{
			$tanggal_tulis = $dari_tanggal.' s.d '.$sampai_tanggal;
		}
		
		$accountname = $this->mm->get_accountname_by_accountint($accountnumber);
		$site_name = $this->mm->get_site_name();
		
		if($idkarat == 'All'){
			$karat = $this->mk->get_karat_srt();
			$karat_tulis = 'All Karat';
		}else{
			$karat = $this->mk->get_karat_by_id($idkarat);
			$karat_tulis = $this->mk->get_karat_name_by_id($idkarat);
			$karat_tulis = $karat_tulis;
		}
		
		$coa_from = $accountnumber;
		$coa_to = $accountnumber;
		
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		$jenis_karat = array("170002", "170003");
		
		if($coa_from == '170002'){
			$tabel_name = 'gold_mutasi_reparasi';
		}else if($coa_from == '170003'){
			$tabel_name = 'gold_mutasi_pengadaan';
		}
		
		$site_name = $this->mm->get_site_name();
		
		$data['view'] = '';
		
		//$this->load->library('Libexcel');
		$objPHPExcel = new Spreadsheet();
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setTitle($site_name);
		
		$style_font = array(
			'font'  => array(
				'name'  => 'Segoe UI'
			)
		);
		
		if($detail_rekap == 'R'){
			if(in_array($coa_from, $jenis_karat)){
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(16);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
				
				$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
				$objPHPExcel->getActiveSheet()->mergeCells('A3:F3');
				$objPHPExcel->getActiveSheet()->getStyle('A1:F5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				
				$style_font_header = array(
					'font'  => array(
						'bold'  => true
					)
				);
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:F5')->applyFromArray($style_font_header);
				
				$sheet->setCellValue('A1', 'Laporan Saldo Emas Rekap, Cabang '.$site_name);
				$sheet->setCellValue('A2', 'Tanggal '.$tanggal_tulis);
				$sheet->setCellValue('A3', $accountname.', '.$karat_tulis);
				
				$sheet->setCellValue('A5', 'Karat');
				$sheet->setCellValue('B5', 'Account Name');
				$sheet->setCellValue('C5', 'Beginning Balance');
				$sheet->setCellValue('D5', 'Debit');
				$sheet->setCellValue('E5', 'Credit');
				$sheet->setCellValue('F5', 'Ending Balance');
				
				$objPHPExcel->getActiveSheet()->getStyle("A5:F5")->applyFromArray(array(
					'borders' => array(
						'top' => array(
							'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						),
						'bottom' => array(
							'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						)
					)
				));
				
				$baris = 6;
				
				foreach($karat as $k){
					$count_data = 0;
					
					$flag_tulis = TRUE;
					$flag_kurs = array();
					$flag_saldo_awal = array();
					$flag_mutasi_in = array();
					$flag_mutasi_out = array();
					$flag_saldo_akhir = array();
					
					$mutasi_in = array();
					$mutasi_out = array();
					
					$sum_bb = 0;
					$sum_d = 0;
					$sum_k = 0;
					$sum_eb = 0;
					
					foreach($kas_account as $ka){
						$saldo_awal_kurs = array();
						$saldo_akhir_kurs = array();
						
						$acc_number = $ka->accountnumber;
						$acc_group = $ka->accountgroup;
						$id_kurs = $k->id;
						$type = $ka->type;
						
						/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
						
						$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						
						/*--------------------------------------------------*/
						
						/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
						
						$saldo_awal_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$from_date);
						$saldo_awal_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$from_date);
						$saldo_akhir_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$to_date);
						$saldo_akhir_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$to_date);
						
						foreach($saldo_awal_d as $mtyd){
							$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] + $mtyd->total_mutasi;
						}
						
						foreach($saldo_awal_k as $mtyk){
							$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] - $mtyk->total_mutasi;
						}
						
						foreach($saldo_akhir_d as $mttd){
							$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] + $mttd->total_mutasi;
						}
					
						foreach($saldo_akhir_k as $mttk){
							$saldo_akhir_kurs[$id_kurs] = $saldo_akhir_kurs[$id_kurs] - $mttk->total_mutasi;
						}
						
						/*---------------------------------------------------*/
						
						/*---- Menentukan Mutasi Masuk dan Mutasi Keluar ----*/
				
						$mutasi_d = $this->mm->get_mutasi_repgros_in($tabel_name,$acc_number,$from_date,$to_date);	
						$mutasi_k = $this->mm->get_mutasi_repgros_out($tabel_name,$acc_number,$from_date,$to_date);
						
						/*---------------------------------------------------*/
						
						/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
						
						$flag_kurs[$k->id] = FALSE;
						$flag_saldo_awal[$k->id] = FALSE;
						$flag_mutasi_in[$k->id] = FALSE;
						$flag_mutasi_out[$k->id] = FALSE;
						$flag_saldo_akhir[$k->id] = FALSE;
						
						if($saldo_awal_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_awal[$k->id] = TRUE;
						}
						
						if($saldo_akhir_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_akhir[$k->id] = TRUE;
						}
						
						foreach($mutasi_d as $md){
							$flag_kurs[$k->id] = TRUE;
							$flag_mutasi_in[$k->id] = TRUE;
							$mutasi_in[$k->id] = $md->total_mutasi;
						}
						
						foreach($mutasi_k as $mk){
							$flag_kurs[$k->id] = TRUE;
							$flag_mutasi_out[$k->id] = TRUE;
							$mutasi_out[$k->id] = $mk->total_mutasi;
						}
						
						/*----------------------------------------------------*/
						
						/*-------- Menampilkan Data Dalam Tabel Report -------*/
						
						if($flag_kurs[$k->id] == TRUE){
							$count_data = $count_data + 1;
							
							$sheet->setCellValue('A'.$baris.'', $k->karat_name);
							$objPHPExcel->getActiveSheet()->getStyle('A'.$baris.':A'.$baris.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							
							$sheet->setCellValue('B'.$baris.'', $ka->accountname);
							$sheet->setCellValue('C'.$baris.'', $saldo_awal_kurs[$k->id]);
							if($flag_mutasi_in[$k->id] != TRUE){
								$mutasi_in[$k->id] = 0;
							}
							$sheet->setCellValue('D'.$baris.'', $mutasi_in[$k->id]);
							if($flag_mutasi_out[$k->id] != TRUE){
								$mutasi_out[$k->id] = 0;
							}
							$sheet->setCellValue('E'.$baris.'', $mutasi_out[$k->id]);
							$sheet->setCellValue('F'.$baris.'', $saldo_akhir_kurs[$k->id]);
							
							$sum_bb = $sum_bb + $saldo_awal_kurs[$k->id];
							$sum_d = $sum_d + $mutasi_in[$k->id];
							$sum_k = $sum_k + $mutasi_out[$k->id];
							$sum_eb = $sum_eb + $saldo_akhir_kurs[$k->id];
							
							$objPHPExcel->getActiveSheet()->getStyle('C'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
							
							$baris = $baris + 1;
						}
					}
				}
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$baris.'')->applyFromArray($style_font);
				$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$baris.'')->getAlignment()->setWrapText(true);
			}else{
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(16);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
				
				$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
				$objPHPExcel->getActiveSheet()->mergeCells('A3:F3');
				$objPHPExcel->getActiveSheet()->getStyle('A1:F5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				
				$style_font_header = array(
					'font'  => array(
						'bold'  => true
					)
				);
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:F5')->applyFromArray($style_font_header);
				
				$sheet->setCellValue('A1', 'Laporan Saldo Emas Rekap, Cabang '.$site_name);
				$sheet->setCellValue('A2', 'Tanggal '.$tanggal_tulis);
				$sheet->setCellValue('A3', $accountname.', '.$karat_tulis);
				
				$sheet->setCellValue('A5', 'Karat');
				$sheet->setCellValue('B5', 'Account Name');
				$sheet->setCellValue('C5', 'Beginning Balance');
				$sheet->setCellValue('D5', 'Debit');
				$sheet->setCellValue('E5', 'Credit');
				$sheet->setCellValue('F5', 'Ending Balance');
				
				$objPHPExcel->getActiveSheet()->getStyle("A5:F5")->applyFromArray(array(
					'borders' => array(
						'top' => array(
							'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						),
						'bottom' => array(
							'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						)
					)
				));
				
				$baris = 6;
			
				foreach($karat as $k){
					$count_data = 0;
					
					$flag_tulis = TRUE;
					$flag_kurs = array();
					$flag_saldo_awal = array();
					$flag_mutasi_in = array();
					$flag_mutasi_out = array();
					$flag_saldo_akhir = array();
					
					$mutasi_in = array();
					$mutasi_out = array();
					
					$sum_bb = 0;
					$sum_d = 0;
					$sum_k = 0;
					$sum_eb = 0;
					
					foreach($kas_account as $ka){
						$saldo_awal_kurs = array();
						$saldo_akhir_kurs = array();
						
						$acc_number = $ka->accountnumber;
						$acc_group = $ka->accountgroup;
						$id_kurs = $k->id;
						$type = $ka->type;
						
						/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
						
						$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						
						/*--------------------------------------------------*/
						
						/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
						
						$saldo_awal_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$from_date,$id_kurs);
						$saldo_awal_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$from_date,$id_kurs);
						$saldo_akhir_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$to_date,$id_kurs);
						$saldo_akhir_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$to_date,$id_kurs);
						
						foreach($saldo_awal_d as $mtyd){
							$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] + $mtyd->total_mutasi;
						}
						
						foreach($saldo_awal_k as $mtyk){
							$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] - $mtyk->total_mutasi;
						}
						
						foreach($saldo_akhir_d as $mttd){
							$saldo_akhir_kurs[$mttd->idkarat] = $saldo_akhir_kurs[$mttd->idkarat] + $mttd->total_mutasi;
						}
					
						foreach($saldo_akhir_k as $mttk){
							$saldo_akhir_kurs[$mttk->idkarat] = $saldo_akhir_kurs[$mttk->idkarat] - $mttk->total_mutasi;
						}	
						
						/*---------------------------------------------------*/
						
						/*---- Menentukan Mutasi Masuk dan Mutasi Keluar ----*/
				
						$mutasi_d = $this->mm->get_mutasi_gr_in($acc_number,$from_date,$to_date);	
						$mutasi_k = $this->mm->get_mutasi_gr_out($acc_number,$from_date,$to_date);
						
						/*---------------------------------------------------*/
						
						/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
						
						$flag_kurs[$k->id] = FALSE;
						$flag_saldo_awal[$k->id] = FALSE;
						$flag_mutasi_in[$k->id] = FALSE;
						$flag_mutasi_out[$k->id] = FALSE;
						$flag_saldo_akhir[$k->id] = FALSE;
						
						if($saldo_awal_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_awal[$k->id] = TRUE;
						}
						
						if($saldo_akhir_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_akhir[$k->id] = TRUE;
						}
						
						foreach($mutasi_d as $md){
							if($md->idkarat == $k->id){
								$flag_kurs[$k->id] = TRUE;
								$flag_mutasi_in[$k->id] = TRUE;
								$mutasi_in[$k->id] = $md->total_mutasi;
							}
						}
						
						foreach($mutasi_k as $mk){
							if($mk->idkarat == $k->id){
								$flag_kurs[$k->id] = TRUE;
								$flag_mutasi_out[$k->id] = TRUE;
								$mutasi_out[$k->id] = $mk->total_mutasi;
							}
						}
						
						/*----------------------------------------------------*/
						
						/*-------- Menampilkan Data Dalam Tabel Report -------*/
						
						if($flag_kurs[$k->id] == TRUE){
							$count_data = $count_data + 1;
							
							$sheet->setCellValue('A'.$baris.'', $k->karat_name);
							$objPHPExcel->getActiveSheet()->getStyle('A'.$baris.':A'.$baris.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							
							$sheet->setCellValue('B'.$baris.'', $ka->accountname);
							$sheet->setCellValue('C'.$baris.'', $saldo_awal_kurs[$k->id]);
							if($flag_mutasi_in[$k->id] != TRUE){
								$mutasi_in[$k->id] = 0;
							}
							$sheet->setCellValue('D'.$baris.'', $mutasi_in[$k->id]);
							if($flag_mutasi_out[$k->id] != TRUE){
								$mutasi_out[$k->id] = 0;
							}
							$sheet->setCellValue('E'.$baris.'', $mutasi_out[$k->id]);
							$sheet->setCellValue('F'.$baris.'', $saldo_akhir_kurs[$k->id]);
							
							$sum_bb = $sum_bb + $saldo_awal_kurs[$k->id];
							$sum_d = $sum_d + $mutasi_in[$k->id];
							$sum_k = $sum_k + $mutasi_out[$k->id];
							$sum_eb = $sum_eb + $saldo_akhir_kurs[$k->id];
							
							$objPHPExcel->getActiveSheet()->getStyle('C'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
							
							$baris = $baris + 1;
						}
					}
				}
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$baris.'')->applyFromArray($style_font);
				$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$baris.'')->getAlignment()->setWrapText(true);
			}
		}else{
			if (in_array($coa_from, $jenis_karat)){
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(14);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(55);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(16);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
				
				$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
				$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
				$objPHPExcel->getActiveSheet()->mergeCells('A3:G4');
				$objPHPExcel->getActiveSheet()->getStyle('A1:G5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				
				$style_font_header = array(
					'font'  => array(
						'bold'  => true
					)
				);
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:G5')->applyFromArray($style_font_header);
				
				$sheet->setCellValue('A1', 'Laporan Saldo Emas Detail, Cabang '.$site_name);
				$sheet->setCellValue('A2', 'Tanggal '.$tanggal_tulis);
				$sheet->setCellValue('A3', $accountname.', '.$karat_tulis);
				
				$sheet->setCellValue('A5', 'Karat');
				$sheet->setCellValue('B5', 'Date');
				$sheet->setCellValue('C5', 'Description');
				$sheet->setCellValue('D5', 'Voucher Code');
				$sheet->setCellValue('E5', 'Debit');
				$sheet->setCellValue('F5', 'Credit');
				$sheet->setCellValue('G5', 'Balance');
				
				$objPHPExcel->getActiveSheet()->getStyle("A5:G5")->applyFromArray(array(
					'borders' => array(
						'top' => array(
							'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						),
						'bottom' => array(
							'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						)
					)
				));
				
				$baris = 6;
			
				foreach($kas_account as $ka){
					foreach($karat as $k){
						$saldo_awal_kurs = array();
						
						$flag_tulis = TRUE;
						$flag_kurs = array();
						$flag_saldo_awal = array();
						$flag_mutasi = array();

						$acc_number = $ka->accountnumber;
						$acc_group = $ka->accountgroup;
						$id_kurs = $k->id;
						$type = $ka->type;
						
						/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
						
						$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						
						/*--------------------------------------------------*/
					
						/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
						$saldo_awal_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$from_date);
						$saldo_awal_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$from_date);
						
						foreach($saldo_awal_d as $mtyd){
							$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] + $mtyd->total_mutasi;
						}
						
						foreach($saldo_awal_k as $mtyk){
							$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] - $mtyk->total_mutasi;
						}
								
						/*---------------------------------------------------*/
					
						/*---------- Mengambil Data Mutasi Transaksi ----------*/
						
						$mutasi_transaksi = $this->mm->get_report_mutasi_repgros($tabel_name,$from_date,$to_date);
						
						/*---------------------------------------------------*/
					
						/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
						
						$flag_kurs[$k->id] = FALSE;
						$flag_saldo_awal[$k->id] = FALSE;
						$flag_mutasi[$k->id] = FALSE;
						
						if($saldo_awal_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_awal[$k->id] = TRUE;
						}
						
						foreach($mutasi_transaksi as $mk){
							$flag_kurs[$k->id] = TRUE;
							$flag_mutasi[$k->id] = TRUE;
						}
						
						/*----------------------------------------------------*/
					
						/*-------- Menampilkan Data Dalam Tabel Report -------*/
						
						if($flag_kurs[$k->id] == TRUE){
							$coa_data = $this->mm->get_accountname_by_accountint($ka->accountnumberint);
							
							$running_balance = $saldo_awal_kurs[$k->id];
							$total_debet = 0;
							$total_kredit = 0;					
							
							$objPHPExcel->getActiveSheet()->mergeCells('B'.$baris.':F'.$baris);
							
							$sheet->setCellValue('A'.$baris.'', $k->karat_name);
							$sheet->setCellValue('B'.$baris.'', $coa_data);
							$sheet->setCellValue('G'.$baris.'', $saldo_awal_kurs[$k->id]);
							
							$objPHPExcel->getActiveSheet()->getStyle('A'.$baris.':A'.$baris.'')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
							$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
							
							$baris = $baris + 1;
							
							foreach($mutasi_transaksi as $mk){								
								$trans_date = strtotime($mk->trans_date);
								$trans_date = date('d/m/Y',$trans_date);
								
								if($mk->toaccount == $acc_number){
									$debet_val = $mk->total_konv;
									$kredit_val = 0;
									
									$running_balance = $running_balance + $mk->total_konv;
									
									$total_debet = $total_debet + $mk->total_konv;
								}else{
									$debet_val = 0;
									$kredit_val = $mk->total_konv;
									
									$running_balance = $running_balance - $mk->total_konv;
									
									$total_kredit = $total_kredit + $mk->total_konv;
								}
								
								$sheet->setCellValue('B'.$baris.'', $trans_date);
								$sheet->setCellValue('C'.$baris.'', $mk->description);
								$sheet->setCellValue('D'.$baris.'', $mk->id_pengiriman);
								$sheet->setCellValue('E'.$baris.'', $debet_val);
								$sheet->setCellValue('F'.$baris.'', $kredit_val);
								$sheet->setCellValue('G'.$baris.'', $running_balance);
								
								$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
								
								$baris = $baris + 1;
							}
							
							$sheet->setCellValue('E'.$baris.'', $total_debet);
							$sheet->setCellValue('F'.$baris.'', $total_kredit);
							
							$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
							$objPHPExcel->getActiveSheet()->getStyle("E".$baris.":F".$baris)->applyFromArray(array(
								'borders' => array(
									'top' => array(
										'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
									)
								)
							));
							
							$baris = $baris + 2;
						}
					}
					
					$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$baris.'')->applyFromArray($style_font);
					$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$baris.'')->getAlignment()->setWrapText(true);
				}
			}else{
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(14);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(55);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(16);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
				
				$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
				$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
				$objPHPExcel->getActiveSheet()->mergeCells('A3:G4');
				$objPHPExcel->getActiveSheet()->getStyle('A1:G5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				
				$style_font_header = array(
					'font'  => array(
						'bold'  => true
					)
				);
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:G5')->applyFromArray($style_font_header);
				
				$sheet->setCellValue('A1', 'Laporan Saldo Emas Detail, Cabang '.$site_name);
				$sheet->setCellValue('A2', 'Tanggal '.$tanggal_tulis);
				$sheet->setCellValue('A3', $accountname.', '.$karat_tulis);
				
				$sheet->setCellValue('A5', 'Karat');
				$sheet->setCellValue('B5', 'Date');
				$sheet->setCellValue('C5', 'Description');
				$sheet->setCellValue('D5', 'Voucher Code');
				$sheet->setCellValue('E5', 'Debit');
				$sheet->setCellValue('F5', 'Credit');
				$sheet->setCellValue('G5', 'Balance');
				
				$objPHPExcel->getActiveSheet()->getStyle("A5:G5")->applyFromArray(array(
					'borders' => array(
						'top' => array(
							'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						),
						'bottom' => array(
							'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						)
					)
				));
				
				$baris = 6;
			
				foreach($kas_account as $ka){
					foreach($karat as $k){
						$saldo_awal_kurs = array();
						
						$flag_tulis = TRUE;
						$flag_kurs = array();
						$flag_saldo_awal = array();
						$flag_mutasi = array();

						$acc_number = $ka->accountnumber;
						$acc_group = $ka->accountgroup;
						$id_kurs = $k->id;
						$type = $ka->type;
						
						/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
						
						$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
						
						/*--------------------------------------------------*/
					
						/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
						
						$saldo_awal_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$from_date,$id_kurs);
						$saldo_awal_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$from_date,$id_kurs);
						
						foreach($saldo_awal_d as $mtyd){
							$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] + $mtyd->total_mutasi;
						}
						
						foreach($saldo_awal_k as $mtyk){
							$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] - $mtyk->total_mutasi;
						}
								
						/*---------------------------------------------------*/
					
						/*---------- Mengambil Data Mutasi Transaksi ----------*/
						
						$mutasi_transaksi = $this->mm->get_report_mutasi_gr_by_kurs($id_kurs,$from_date,$to_date);
						
						/*---------------------------------------------------*/
					
						/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
						
						$flag_kurs[$k->id] = FALSE;
						$flag_saldo_awal[$k->id] = FALSE;
						$flag_mutasi[$k->id] = FALSE;
						
						if($saldo_awal_kurs[$k->id] != 0){
							$flag_kurs[$k->id] = TRUE;
							$flag_saldo_awal[$k->id] = TRUE;
						}
						
						foreach($mutasi_transaksi as $mk){
							if($mk->fromaccount == $acc_number || $mk->toaccount == $acc_number){
								$flag_kurs[$k->id] = TRUE;
								$flag_mutasi[$k->id] = TRUE;
							}
						}
						
						/*----------------------------------------------------*/
					
						/*-------- Menampilkan Data Dalam Tabel Report -------*/
						
						if($flag_kurs[$k->id] == TRUE){
							$coa_data = $this->mm->get_accountname_by_accountint($ka->accountnumberint);
							
							$running_balance = $saldo_awal_kurs[$k->id];
							$total_debet = 0;
							$total_kredit = 0;
							
							$objPHPExcel->getActiveSheet()->mergeCells('B'.$baris.':F'.$baris);
							
							$sheet->setCellValue('A'.$baris.'', $k->karat_name);
							$sheet->setCellValue('B'.$baris.'', $coa_data);
							$sheet->setCellValue('G'.$baris.'', $saldo_awal_kurs[$k->id]);
							
							$objPHPExcel->getActiveSheet()->getStyle('A'.$baris.':A'.$baris.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
							
							$baris = $baris + 1;				
							
							foreach($mutasi_transaksi as $mk){
								if($mk->fromaccount == $acc_number || $mk->toaccount == $acc_number){
									$trans_date = strtotime($mk->transdate);
									$trans_date = date('d/m/Y',$trans_date);
									
									if($mk->toaccount == $acc_number){
										$debet_val = $mk->value;
										$kredit_val = 0;
										
										$running_balance = $running_balance + $mk->value;
										
										$total_debet = $total_debet + $mk->value;
									}else{
										$debet_val = 0;
										$kredit_val = $mk->value;
										
										$running_balance = $running_balance - $mk->value;
										
										$total_kredit = $total_kredit + $mk->value;
									}
									
									$sheet->setCellValue('B'.$baris.'', $trans_date);
									$sheet->setCellValue('C'.$baris.'', $mk->description);
									$sheet->setCellValue('D'.$baris.'', $mk->idmutasi);
									$sheet->setCellValue('E'.$baris.'', $debet_val);
									$sheet->setCellValue('F'.$baris.'', $kredit_val);
									$sheet->setCellValue('G'.$baris.'', $running_balance);
									
									$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
									
									$baris = $baris + 1;
								}
							}
							
							$sheet->setCellValue('E'.$baris.'', $total_debet);
							$sheet->setCellValue('F'.$baris.'', $total_kredit);
							
							$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
							$objPHPExcel->getActiveSheet()->getStyle("E".$baris.":F".$baris)->applyFromArray(array(
								'borders' => array(
									'top' => array(
										'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
									)
								)
							));
							
							$baris = $baris + 2;
						}
					}
				}
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$baris.'')->applyFromArray($style_font);
				$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$baris.'')->getAlignment()->setWrapText(true);
			}
		}
		
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Excel2007');

		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="EXCEL LAPORAN SALDO GRAM '.$site_name.' TANGGAL '.$tanggal_tulis.'.xlsx"');
		//unduh file
		$objWriter->save("php://output");
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

