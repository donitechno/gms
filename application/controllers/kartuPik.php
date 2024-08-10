<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KartuPik extends CI_Controller {
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
		$account = $this->mm->get_account_karyawan();
		$data['view'] = '<div class="ui container fluid">
			<form class="ui form form-javascript" id="kartuPik-form-filter" action="'.base_url().'index.php/kartuPik/filter/" method="post">
			<div class="ui grid">
				<div class="ui inverted dimmer" id="kartuPik-loaderlist">
					<div class="ui large text loader">Loading</div>
				</div>
				<div class="fifteen wide centered column" style="padding-bottom:0">
					<div class="fields">
						<div class="three wide field">
							<label>Tgl Transaksi</label>
							<input type="text" name="kartuPik-datefrom" id="kartuPik-datefrom" readonly>
						</div>
						<div class="one wide field" style="text-align:center;margin-top:30px">
							<label>s.d</label>
						</div>
						<div class="three wide field">
							<label style="visibility:hidden">-</label>
							<input type="text" name="kartuPik-dateto" id="kartuPik-dateto" readonly>
						</div>
						<div class="five wide field">
							<label>Account</label>
							<select name="kartuPik-filter_account" id="kartuPik-filter_account">
								<option value="All">-- All --</option>';
								foreach($account as $a){
								$data['view'] .= '<option value="'.$a->accountnumber.'">'.$a->accountname.'</option>';
								}
							$data['view'] .= '</select>
						</div>
						<div class="three wide field">
							<label style="visibility:hidden">-</label>
							<select name="kartuPik-filter_dr" id="kartuPik-filter_dr">
								<option value="D">Detail</option>
								<option value="R">Rekap</option>
							</select>
						</div>
						<div class="one wide field">
							<label style="visibility:hidden">-</label>
							<div class="ui fluid icon green button filter-input" id="kartuPik-btnfilter" onclick=filterTransaksi("kartuPik") title="Filter">
								<i class="filter icon"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="fifteen wide centered column" id="kartuPik-wrap_filter" style="padding-top:0">
				</div>
			</div>
			</form>
		</div>';
		
		$data["date"] = 2;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function filter(){
		$this->db->trans_start();
		
		$from_date = $this->input->post('kartuPik-datefrom');
		$dari_tanggal = $from_date;
		$to_date = $this->input->post('kartuPik-dateto');
		$sampai_tanggal = $to_date;
		
		$stockFromTime = $this->date_to_format($from_date);
		$stockToTime = $this->date_to_format($to_date);
		
		$from_date = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_date = date("Y-m-d",$stockToTime).' 00:00:00';
		$to_date2 = date("Y-m-d",$stockToTime).' 23:59:59';
		
		$filter_account = $this->input->post('kartuPik-filter_account');
		$filter_dr = $this->input->post('kartuPik-filter_dr');
		
		if($filter_account == 'All'){
			$kas_account = $this->mm->get_account_karyawan();
		}else{
			$kas_account = $this->mm->get_single_coa_rp($filter_account);
		}
		
		if($filter_dr == 'R'){
			$data['view'] = '<div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui purple button" href="'.base_url().'index.php/kartuPik/excel/'.$dari_tanggal.'/'.$sampai_tanggal.'/'.$filter_account.'/'.$filter_dr.'"><i class="file excel icon"></i> Export to Excel</a><a class="ui facebook button" href="'.base_url().'index.php/kartuPik/pdf/'.$dari_tanggal.'/'.$sampai_tanggal.'/'.$filter_account.'/'.$filter_dr.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><table class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:30px">No</th><th>Account Name</th><th>Saldo Awal</th><th>Debit</th><th>Credit</th><th>Saldo Akhir</th></tr></thead><tbody>';
			
			$count_data = 0;
			
			$flag_tulis = TRUE;
			$flag_kurs = '';
			$flag_saldo_awal = '';
			$flag_mutasi_in = '';
			$flag_mutasi_out = '';
			$flag_saldo_akhir = '';
			
			$mutasi_in = 0;
			$mutasi_out = 0;
			
			$sum_bb = 0;
			$sum_d = 0;
			$sum_k = 0;
			$sum_eb = 0;
			
			foreach($kas_account as $ka){
				$saldo_awal_kurs = 0;
				$saldo_akhir_kurs = 0;
				
				$acc_number = $ka->accountnumber;
				$acc_group = $ka->accountgroup;
				
				/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
				
				$saldo_awal_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
				$saldo_akhir_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
				
				/*--------------------------------------------------*/
				
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$from_date);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$from_date);
				$saldo_akhir_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$to_date2);
				$saldo_akhir_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$to_date2);
				
				foreach($saldo_awal_d as $mtyd){
					if($acc_group == '1' || $acc_group == '5'){
						$saldo_awal_kurs = $saldo_awal_kurs + $mtyd->total_mutasi;
					}else{
						$saldo_awal_kurs = $saldo_awal_kurs - $mtyd->total_mutasi;
					}
				}
				
				foreach($saldo_awal_k as $mtyk){
					if($acc_group == '1' || $acc_group == '5'){
						$saldo_awal_kurs = $saldo_awal_kurs - $mtyk->total_mutasi;
					}else{
						$saldo_awal_kurs = $saldo_awal_kurs + $mtyk->total_mutasi;
					}
				}
				
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
				
				/*---- Menentukan Mutasi Masuk dan Mutasi Keluar ----*/
		
				$mutasi_d = $this->mm->get_mutasi_in_rp($acc_number,$from_date,$to_date);	
				$mutasi_k = $this->mm->get_mutasi_out_rp($acc_number,$from_date,$to_date);
				
				/*---------------------------------------------------*/
				
				/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
				
				$flag_kurs = FALSE;
				$flag_saldo_awal = FALSE;
				$flag_mutasi_in = FALSE;
				$flag_mutasi_out = FALSE;
				$flag_saldo_akhir = FALSE;
				
				if($saldo_awal_kurs != 0){
					$flag_kurs = TRUE;
					$flag_saldo_awal = TRUE;
				}
				
				if($saldo_akhir_kurs != 0){
					$flag_kurs = TRUE;
					$flag_saldo_akhir = TRUE;
				}
				
				if(count($mutasi_d > 0)){
					if($mutasi_d[0]->total_mutasi != NULL){
						foreach($mutasi_d as $md){
							$flag_kurs = TRUE;
							$flag_mutasi_in = TRUE;
							$mutasi_in = $md->total_mutasi;
						}
					}
				}
				
				if(count($mutasi_k > 0)){
					if($mutasi_k[0]->total_mutasi != NULL){
						foreach($mutasi_k as $mk){
							$flag_kurs = TRUE;
							$flag_mutasi_out = TRUE;
							$mutasi_out = $mk->total_mutasi;
						}
					}
				}
				
				
				
				/*----------------------------------------------------*/
				
				/*-------- Menampilkan Data Dalam Tabel Report -------*/
				
				if($flag_kurs == TRUE){
					$count_data = $count_data + 1;
					
					$data['view'] .= '<tr><td class="center aligned">'.$count_data.'</td>';
					
					if($saldo_awal_kurs != 0 && $saldo_awal_kurs != '' && $saldo_awal_kurs != -0){
						$data['view'] .= '<td>'.$ka->accountname.'</td>';
						if($acc_group == '1' || $acc_group == '5'){
							$data['view'] .= '<td class="right aligned">'.number_format($saldo_awal_kurs, 0).'</td>';
						}else{
							$data['view'] .= '<td class="right aligned">'.number_format($saldo_awal_kurs, 0).'</td>';
						}
						
						if($acc_group == '1' || $acc_group == '5'){
							$sum_bb = $sum_bb + $saldo_awal_kurs;
						}else{
							$sum_bb = $sum_bb - $saldo_awal_kurs;
						}
					}else{
						$data['view'] .= '<td>'.$ka->accountname.'</td><td class="right aligned">-</td>';
					}
					
					if($flag_mutasi_in == TRUE){
						$data['view'] .= '<td class="right aligned">'.number_format($mutasi_in, 0).'</td>';
						$sum_d = $sum_d + $mutasi_in;
					}else{
						$data['view'] .= '<td class="right aligned">-</td>';
					}
					
					if($flag_mutasi_out == TRUE){
						$data['view'] .= '<td class="right aligned">'.number_format($mutasi_out, 0).'</td>';
						$sum_k = $sum_k + $mutasi_out;
					}else{
						$data['view'] .= '<td class="right aligned">-</td>';
					}
					
					if($saldo_akhir_kurs != 0 && $saldo_akhir_kurs != '' && $saldo_akhir_kurs != -0){
						if($acc_group == '1' || $acc_group == '5'){
							$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td>';
						}else{
							$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs, 0).'</td>';
						}
						
						if($acc_group == '1' || $acc_group == '5'){
							$sum_eb = $sum_eb + $saldo_akhir_kurs;
						}else{
							$sum_eb = $sum_eb - $saldo_akhir_kurs;
						}
						
					}else{
						$data['view'] .= '<td class="right aligned">-</td>';
					}
					
					$data['view'] .= '</tr>';
				}
			}
			
			$data['view'] .= '<tr><td colspan="2" style="border-top:double #000; font-weight:600"></td><td class="right aligned" style="border-top:double #000; font-weight:600">'.number_format($sum_bb, 0).'</td><td class="right aligned" style="border-top:double #000; font-weight:600">'.number_format($sum_d, 0).'</td><td class="right aligned" style="border-top:double #000; font-weight:600">'.number_format($sum_k, 0).'</td><td class="right aligned" style="border-top:double #000; font-weight:600">'.number_format($sum_eb, 0).'</td></tr>';
		}else if($filter_dr == 'D'){
			$data['view'] = '<div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui purple button" href="'.base_url().'index.php/kartuPik/excel/'.$dari_tanggal.'/'.$sampai_tanggal.'/'.$filter_account.'/'.$filter_dr.'"><i class="file excel icon"></i> Export to Excel</a><a class="ui facebook button" href="'.base_url().'index.php/kartuPik/pdf/'.$dari_tanggal.'/'.$sampai_tanggal.'/'.$filter_account.'/'.$filter_dr.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><table class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:50px">No</th><th>Tanggal</th><th>Keterangan</th><th>ID Transaksi</th><th>Tarik</th><th>Setor</th><th>Saldo</th></tr></thead><tbody>';
			
			$number = 1;
			
			foreach($kas_account as $ka){
				$saldo_awal_kurs = 0;
				
				$flag_tulis = TRUE;
				$flag_kurs = '';
				$flag_saldo_awal = '';
				$flag_mutasi = '';

				$acc_number = $ka->accountnumber;
				$accountnumber = $ka->accountnumber;
				
				/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
				$saldo_awal_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
				
				/*--------------------------------------------------*/
			
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$from_date);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$from_date);
				
				foreach($saldo_awal_d as $mtyd){
					if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
						$saldo_awal_kurs = $saldo_awal_kurs + $mtyd->total_mutasi;
					}else{
						$saldo_awal_kurs = $saldo_awal_kurs - $mtyd->total_mutasi;
					}
				}
				
				foreach($saldo_awal_k as $mtyk){
					if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
						$saldo_awal_kurs = $saldo_awal_kurs - $mtyk->total_mutasi;
					}else{
						$saldo_awal_kurs = $saldo_awal_kurs + $mtyk->total_mutasi;
					}
				}
						
				/*---------------------------------------------------*/
			
				/*---------- Mengambil Data Mutasi Transaksi ----------*/
		
				$mutasi_transaksi = $this->mm->get_report_mutasi_rp($from_date,$to_date,$acc_number);
				
				/*---------------------------------------------------*/
			
				/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
				
				$flag_kurs = FALSE;
				$flag_saldo_awal = FALSE;
				$flag_mutasi = FALSE;
				
				if($saldo_awal_kurs != 0){
					$flag_kurs = TRUE;
					$flag_saldo_awal = TRUE;
				}
				
				foreach($mutasi_transaksi as $mk){
					if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
						$flag_kurs = TRUE;
						$flag_mutasi = TRUE;
					}
				}
				
				/*----------------------------------------------------*/
			
				/*-------- Menampilkan Data Dalam Tabel Report -------*/
				
				if($flag_kurs == TRUE){
					$coa_data = $this->mm->get_accountname_by_accountint_rp($ka->accountnumberint);
					
					$running_balance = $saldo_awal_kurs;
					$total_debet = 0;
					$total_kredit = 0;
					
					if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
						if($saldo_awal_kurs == 0 || $saldo_awal_kurs == ''){
							$data['view'] .= '<tr><td class="center aligned" width="50px">'.$number.'</td><td colspan="5">'.$coa_data.'</td><td class="right aligned">-</td>';
						}else{
							$data['view'] .= '<tr><td class="center aligned" width="50px">'.$number.'</td><td colspan="5">'.$coa_data.'</td><td class="right aligned">'.number_format($saldo_awal_kurs, 0).'</td>';
						}
					}else{
						$data['view'] .= '<tr><td class="center aligned" width="50px">'.$number.'</td><td colspan="5">'.$coa_data.'</td><td class="right aligned">'.number_format($saldo_awal_kurs, 0).'</td>';
					}
					
					$count_data = 1;
					foreach($mutasi_transaksi as $mk){
						if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
							$trans_date = strtotime($mk->transdate);
							$trans_date = date('d/m/Y',$trans_date);
							
							if($mk->toaccount == $accountnumber){
								$debet_val = number_format($mk->value, 0);
								$kredit_val = '-';
								
								if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
									$running_balance = $running_balance + $mk->value;
								}else{
									$running_balance = $running_balance - $mk->value;
								}
								
								$total_debet = $total_debet + $mk->value;
							}else{
								$debet_val = '-';
								$kredit_val = number_format($mk->value, 0);
								
								if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
									$running_balance = $running_balance - $mk->value;
								}else{
									$running_balance = $running_balance + $mk->value;
								}
								
								$total_kredit = $total_kredit + $mk->value;
							}
							
							if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
								$data['view'] .= '<tr><td class="center aligned"></td><td>'.$trans_date.'</td><td>'.$mk->description.'</td><td>'.$mk->idmutasi.'</td><td class="right aligned">'.$debet_val.'</td><td class="right aligned">'.$kredit_val.'</td><td class="right aligned">'.number_format($running_balance, 0).'</td></tr>';
							}else{
								$data['view'] .= '<tr><td class="center aligned"></td><td>'.$trans_date.'</td><td>'.$mk->description.'</td><td>'.$mk->idmutasi.'</td><td class="right aligned">'.$debet_val.'</td><td class="right aligned">'.$kredit_val.'</td><td class="right aligned">'.number_format($running_balance, 0).'</td></tr>';
							}
							
							$count_data = $count_data + 1;
						}
					}
					
					$data['view'] .= '<tr><td colspan="4"></td><td class="right aligned" style="border-top: 2px solid #000">'.number_format($total_debet, 0).'</td><td class="right aligned" style="border-top: 2px solid #000">'.number_format($total_kredit, 0).'</td><td></td></tr>';
					$data['view'] .= '<tr><td colspan="7" style="visibility:hidden; color:#FFF">-</td></tr>';
					
					$number = $number + 1;
				}
			}
		}
		
		$data['view'] .= '</tbody></table>';
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function pdf($from_date,$to_date,$filter_account,$filter_dr){
		$from_date = str_replace('%20',' ',$from_date);
		$to_date = str_replace('%20',' ',$to_date);
		
		$dari_tanggal = $from_date;
		$sampai_tanggal = $to_date;
		
		$stockFromTime = $this->date_to_format($from_date);
		$stockToTime = $this->date_to_format($to_date);
		
		$from_date = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_date = date("Y-m-d",$stockToTime).' 00:00:00';
		$to_date2 = date("Y-m-d",$stockToTime).' 23:59:59';
		
		if($filter_account == 'All'){
			$kas_account = $this->mm->get_account_karyawan();
			$kas_tulis = 'All Karyawan';
		}else{
			$kas_account = $this->mm->get_single_coa_rp($filter_account);
			$kas_tulis = $this->mm->get_coa_number_name_2($filter_account);
		}
		
		$site_name = $this->mm->get_site_name();
		
		if($filter_dr == 'R'){
			$data['view'] = '<div class="sixteen wide centered column center aligned" style="font-weight:bold;text-align:center;font-size:13px;font-family:samsungone"><span style="text-align">Kartu Piutang Karyawan Rekap, Cabang '.$site_name.'</span><br><span>Tanggal '.$dari_tanggal.' s.d '.$sampai_tanggal.'</span><br><span>'.$kas_tulis.'</span><br><span style="visibility:hidden">-</span></div><table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:30px" class="th-5">No</th><th class="th-5">Account Name</th><th class="th-5">Saldo Awal</th><th class="th-5">Debit</th><th class="th-5">Credit</th><th class="th-5">Saldo Akhir</th></tr></thead><tbody>';
			
			$count_data = 0;
			
			$flag_tulis = TRUE;
			$flag_kurs = '';
			$flag_saldo_awal = '';
			$flag_mutasi_in = '';
			$flag_mutasi_out = '';
			$flag_saldo_akhir = '';
			
			$mutasi_in = 0;
			$mutasi_out = 0;
			
			$sum_bb = 0;
			$sum_d = 0;
			$sum_k = 0;
			$sum_eb = 0;
			
			foreach($kas_account as $ka){
				$saldo_awal_kurs = 0;
				$saldo_akhir_kurs = 0;
				
				$acc_number = $ka->accountnumber;
				$acc_group = $ka->accountgroup;
				
				/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
				
				$saldo_awal_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
				$saldo_akhir_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
				
				/*--------------------------------------------------*/
				
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$from_date);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$from_date);
				$saldo_akhir_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$to_date2);
				$saldo_akhir_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$to_date2);
				
				foreach($saldo_awal_d as $mtyd){
					if($acc_group == '1' || $acc_group == '5'){
						$saldo_awal_kurs = $saldo_awal_kurs + $mtyd->total_mutasi;
					}else{
						$saldo_awal_kurs = $saldo_awal_kurs - $mtyd->total_mutasi;
					}
				}
				
				foreach($saldo_awal_k as $mtyk){
					if($acc_group == '1' || $acc_group == '5'){
						$saldo_awal_kurs = $saldo_awal_kurs - $mtyk->total_mutasi;
					}else{
						$saldo_awal_kurs = $saldo_awal_kurs + $mtyk->total_mutasi;
					}
				}
				
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
				
				/*---- Menentukan Mutasi Masuk dan Mutasi Keluar ----*/
		
				$mutasi_d = $this->mm->get_mutasi_in_rp($acc_number,$from_date,$to_date);	
				$mutasi_k = $this->mm->get_mutasi_out_rp($acc_number,$from_date,$to_date);
				
				/*---------------------------------------------------*/
				
				/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
				
				$flag_kurs = FALSE;
				$flag_saldo_awal = FALSE;
				$flag_mutasi_in = FALSE;
				$flag_mutasi_out = FALSE;
				$flag_saldo_akhir = FALSE;
				
				if($saldo_awal_kurs != 0){
					$flag_kurs = TRUE;
					$flag_saldo_awal = TRUE;
				}
				
				if($saldo_akhir_kurs != 0){
					$flag_kurs = TRUE;
					$flag_saldo_akhir = TRUE;
				}
				
				if(count($mutasi_d > 0)){
					if($mutasi_d[0]->total_mutasi != NULL){
						foreach($mutasi_d as $md){
							$flag_kurs = TRUE;
							$flag_mutasi_in = TRUE;
							$mutasi_in = $md->total_mutasi;
						}
					}
				}
				
				if(count($mutasi_k > 0)){
					if($mutasi_k[0]->total_mutasi != NULL){
						foreach($mutasi_k as $mk){
							$flag_kurs = TRUE;
							$flag_mutasi_out = TRUE;
							$mutasi_out = $mk->total_mutasi;
						}
					}
				}
				
				
				
				/*----------------------------------------------------*/
				
				/*-------- Menampilkan Data Dalam Tabel Report -------*/
				
				if($flag_kurs == TRUE){
					$count_data = $count_data + 1;
					
					$data['view'] .= '<tr><td class="right-aligned">'.$count_data.'. </td>';
					
					if($saldo_awal_kurs != 0 && $saldo_awal_kurs != '' && $saldo_awal_kurs != -0){
						$data['view'] .= '<td>'.$ka->accountname.'</td>';
						if($acc_group == '1' || $acc_group == '5'){
							$data['view'] .= '<td class="right-aligned">'.number_format($saldo_awal_kurs, 0).'</td>';
						}else{
							$data['view'] .= '<td class="right-aligned">'.number_format($saldo_awal_kurs, 0).'</td>';
						}
						
						if($acc_group == '1' || $acc_group == '5'){
							$sum_bb = $sum_bb + $saldo_awal_kurs;
						}else{
							$sum_bb = $sum_bb - $saldo_awal_kurs;
						}
					}else{
						$data['view'] .= '<td>'.$ka->accountname.'</td><td class="right-aligned">-</td>';
					}
					
					if($flag_mutasi_in == TRUE){
						$data['view'] .= '<td class="right-aligned">'.number_format($mutasi_in, 0).'</td>';
						$sum_d = $sum_d + $mutasi_in;
					}else{
						$data['view'] .= '<td class="right-aligned">-</td>';
					}
					
					if($flag_mutasi_out == TRUE){
						$data['view'] .= '<td class="right-aligned">'.number_format($mutasi_out, 0).'</td>';
						$sum_k = $sum_k + $mutasi_out;
					}else{
						$data['view'] .= '<td class="right-aligned">-</td>';
					}
					
					if($saldo_akhir_kurs != 0 && $saldo_akhir_kurs != '' && $saldo_akhir_kurs != -0){
						if($acc_group == '1' || $acc_group == '5'){
							$data['view'] .= '<td class="right-aligned">'.number_format($saldo_akhir_kurs, 0).'</td>';
						}else{
							$data['view'] .= '<td class="right-aligned">'.number_format($saldo_akhir_kurs, 0).'</td>';
						}
						
						if($acc_group == '1' || $acc_group == '5'){
							$sum_eb = $sum_eb + $saldo_akhir_kurs;
						}else{
							$sum_eb = $sum_eb - $saldo_akhir_kurs;
						}
						
					}else{
						$data['view'] .= '<td class="right-aligned">-</td>';
					}
					
					$data['view'] .= '</tr>';
				}
			}
			
			$data['view'] .= '<tr><td colspan="2" style="border-top:double #000; font-weight:600"></td><td class="right-aligned" style="border-top:double #000; font-weight:600">'.number_format($sum_bb, 0).'</td><td class="right-aligned" style="border-top:double #000; font-weight:600">'.number_format($sum_d, 0).'</td><td class="right-aligned" style="border-top:double #000; font-weight:600">'.number_format($sum_k, 0).'</td><td class="right-aligned" style="border-top:double #000; font-weight:600">'.number_format($sum_eb, 0).'</td></tr>';
		}else if($filter_dr == 'D'){
			$data['view'] = '<div class="sixteen wide centered column center aligned" style="font-weight:bold;text-align:center;font-size:13px;font-family:samsungone"><span style="text-align">Kartu Piutang Karyawan Detail, Cabang '.$site_name.'</span><br><span>Tanggal '.$dari_tanggal.' s.d '.$sampai_tanggal.'</span><br><span>'.$kas_tulis.'</span><br><span style="visibility:hidden">-</span></div><table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:50px" class="th-5">No</th><th class="th-5">Tanggal</th><th class="th-5">Keterangan</th><th class="th-5">ID Transaksi</th><th class="th-5">Tarik</th><th class="th-5">Setor</th><th class="th-5">Saldo</th></tr></thead><tbody>';
			
			$number = 1;
			
			foreach($kas_account as $ka){
				$saldo_awal_kurs = 0;
				
				$flag_tulis = TRUE;
				$flag_kurs = '';
				$flag_saldo_awal = '';
				$flag_mutasi = '';

				$acc_number = $ka->accountnumber;
				$accountnumber = $ka->accountnumber;
				
				/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
				$saldo_awal_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
				
				/*--------------------------------------------------*/
			
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$from_date);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$from_date);
				
				foreach($saldo_awal_d as $mtyd){
					if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
						$saldo_awal_kurs = $saldo_awal_kurs + $mtyd->total_mutasi;
					}else{
						$saldo_awal_kurs = $saldo_awal_kurs - $mtyd->total_mutasi;
					}
				}
				
				foreach($saldo_awal_k as $mtyk){
					if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
						$saldo_awal_kurs = $saldo_awal_kurs - $mtyk->total_mutasi;
					}else{
						$saldo_awal_kurs = $saldo_awal_kurs + $mtyk->total_mutasi;
					}
				}
						
				/*---------------------------------------------------*/
			
				/*---------- Mengambil Data Mutasi Transaksi ----------*/
		
				$mutasi_transaksi = $this->mm->get_report_mutasi_rp($from_date,$to_date,$acc_number);
				
				/*---------------------------------------------------*/
			
				/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
				
				$flag_kurs = FALSE;
				$flag_saldo_awal = FALSE;
				$flag_mutasi = FALSE;
				
				if($saldo_awal_kurs != 0){
					$flag_kurs = TRUE;
					$flag_saldo_awal = TRUE;
				}
				
				foreach($mutasi_transaksi as $mk){
					if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
						$flag_kurs = TRUE;
						$flag_mutasi = TRUE;
					}
				}
				
				/*----------------------------------------------------*/
			
				/*-------- Menampilkan Data Dalam Tabel Report -------*/
				
				if($flag_kurs == TRUE){
					$coa_data = $this->mm->get_accountname_by_accountint_rp($ka->accountnumberint);
					
					$running_balance = $saldo_awal_kurs;
					$total_debet = 0;
					$total_kredit = 0;
					
					if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
						if($saldo_awal_kurs == 0 || $saldo_awal_kurs == ''){
							$data['view'] .= '<tr><td class="right-aligned" width="50px">'.$number.'. </td><td colspan="5">'.$coa_data.'</td><td class="right-aligned">-</td><td></td>';
						}else{
							$data['view'] .= '<tr><td class="right-aligned" width="50px">'.$number.'. </td><td colspan="5">'.$coa_data.'</td><td class="right-aligned">'.number_format($saldo_awal_kurs, 0).'</td><td></td>';
						}
					}else{
						$data['view'] .= '<tr><td class="right-aligned" width="50px">'.$number.'. </td><td colspan="5">'.$coa_data.'</td><td class="right-aligned">'.number_format($saldo_awal_kurs, 0).'</td><td></td>';
					}
					
					$count_data = 1;
					foreach($mutasi_transaksi as $mk){
						if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
							$trans_date = strtotime($mk->transdate);
							$trans_date = date('d/m/Y',$trans_date);
							
							if($mk->toaccount == $accountnumber){
								$debet_val = number_format($mk->value, 0);
								$kredit_val = '-';
								
								if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
									$running_balance = $running_balance + $mk->value;
								}else{
									$running_balance = $running_balance - $mk->value;
								}
								
								$total_debet = $total_debet + $mk->value;
							}else{
								$debet_val = '-';
								$kredit_val = number_format($mk->value, 0);
								
								if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
									$running_balance = $running_balance - $mk->value;
								}else{
									$running_balance = $running_balance + $mk->value;
								}
								
								$total_kredit = $total_kredit + $mk->value;
							}
							
							if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
								$data['view'] .= '<tr><td class="center aligned"></td><td>'.$trans_date.'</td><td>'.$mk->description.'</td><td>'.$mk->idmutasi.'</td><td class="right-aligned">'.$debet_val.'</td><td class="right-aligned">'.$kredit_val.'</td><td class="right-aligned">'.number_format($running_balance, 0).'</td></tr>';
							}else{
								$data['view'] .= '<tr><td class="center aligned"></td><td>'.$trans_date.'</td><td>'.$mk->description.'</td><td>'.$mk->idmutasi.'</td><td class="right-aligned">'.$debet_val.'</td><td class="right-aligned">'.$kredit_val.'</td><td class="right-aligned">'.number_format($running_balance, 0).'</td></tr>';
							}
							
							$count_data = $count_data + 1;
						}
					}
					
					$data['view'] .= '<tr><td colspan="4"></td><td class="right-aligned" style="border-top: 1px solid #000">'.number_format($total_debet, 0).'</td><td class="right-aligned" style="border-top: 1px solid #000">'.number_format($total_kredit, 0).'</td><td></td></tr>';
					$data['view'] .= '<tr><td colspan="7" style="visibility:hidden; color:#FFF">-</td></tr>';
					
					$number = $number + 1;
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
		
        $pdf->Output("Laporan Kartu PIK.pdf", "I");
	}
	
	public function excel($from_date,$to_date,$filter_account,$filter_dr){
		$from_date = str_replace('%20',' ',$from_date);
		$to_date = str_replace('%20',' ',$to_date);
		
		$dari_tanggal = $from_date;
		$sampai_tanggal = $to_date;
		
		$stockFromTime = $this->date_to_format($from_date);
		$stockToTime = $this->date_to_format($to_date);
		
		$from_date = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_date = date("Y-m-d",$stockToTime).' 00:00:00';
		$to_date2 = date("Y-m-d",$stockToTime).' 23:59:59';
		
		if($filter_account == 'All'){
			$kas_account = $this->mm->get_account_karyawan();
			$kas_tulis = 'All Karyawan';
		}else{
			$kas_account = $this->mm->get_single_coa_rp($filter_account);
			$kas_tulis = $this->mm->get_coa_number_name_2($filter_account);
		}
		
		$site_name = $this->mm->get_site_name();
		
		$this->load->library('Libexcel');
		$objPHPExcel = new PHPExcel();
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setTitle($site_name);
		
		$style_font = array(
			'font'  => array(
				'name'  => 'Segoe UI'
			)
		);
		
		if($filter_dr == 'R'){
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(55);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			
			$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:F3');
			$objPHPExcel->getActiveSheet()->getStyle('A1:F5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$style_font_header = array(
				'font'  => array(
					'bold'  => true
				)
			);
			
			$objPHPExcel->getActiveSheet()->getStyle('A1:F5')->applyFromArray($style_font_header);
			
			$sheet->setCellValue('A1', 'Kartu Piutang Karyawan Rekap, Cabang '.$site_name);
			$sheet->setCellValue('A2', 'Tanggal '.$dari_tanggal.' S/D '.$sampai_tanggal);
			$sheet->setCellValue('A3', $kas_tulis);
			
			$sheet->setCellValue('A5', 'No');
			$sheet->setCellValue('B5', 'Account Name');
			$sheet->setCellValue('C5', 'Saldo Awal');
			$sheet->setCellValue('D5', 'Debit');
			$sheet->setCellValue('E5', 'Credit');
			$sheet->setCellValue('F5', 'Saldo Akhir');
			
			$objPHPExcel->getActiveSheet()->getStyle("A5:F5")->applyFromArray(array(
				'borders' => array(
					'top' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					),
					'bottom' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			));
			
			$baris = 6;
			
			$count_data = 0;
			
			$flag_tulis = TRUE;
			$flag_kurs = '';
			$flag_saldo_awal = '';
			$flag_mutasi_in = '';
			$flag_mutasi_out = '';
			$flag_saldo_akhir = '';
			
			$mutasi_in = 0;
			$mutasi_out = 0;
			
			$sum_bb = 0;
			$sum_d = 0;
			$sum_k = 0;
			$sum_eb = 0;
			
			foreach($kas_account as $ka){
				$saldo_awal_kurs = 0;
				$saldo_akhir_kurs = 0;
				
				$acc_number = $ka->accountnumber;
				$acc_group = $ka->accountgroup;
				
				/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
				
				$saldo_awal_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
				$saldo_akhir_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
				
				/*--------------------------------------------------*/
				
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$from_date);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$from_date);
				$saldo_akhir_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$to_date2);
				$saldo_akhir_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$to_date2);
				
				foreach($saldo_awal_d as $mtyd){
					if($acc_group == '1' || $acc_group == '5'){
						$saldo_awal_kurs = $saldo_awal_kurs + $mtyd->total_mutasi;
					}else{
						$saldo_awal_kurs = $saldo_awal_kurs - $mtyd->total_mutasi;
					}
				}
				
				foreach($saldo_awal_k as $mtyk){
					if($acc_group == '1' || $acc_group == '5'){
						$saldo_awal_kurs = $saldo_awal_kurs - $mtyk->total_mutasi;
					}else{
						$saldo_awal_kurs = $saldo_awal_kurs + $mtyk->total_mutasi;
					}
				}
				
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
				
				/*---- Menentukan Mutasi Masuk dan Mutasi Keluar ----*/
		
				$mutasi_d = $this->mm->get_mutasi_in_rp($acc_number,$from_date,$to_date);	
				$mutasi_k = $this->mm->get_mutasi_out_rp($acc_number,$from_date,$to_date);
				
				/*---------------------------------------------------*/
				
				/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
				
				$flag_kurs = FALSE;
				$flag_saldo_awal = FALSE;
				$flag_mutasi_in = FALSE;
				$flag_mutasi_out = FALSE;
				$flag_saldo_akhir = FALSE;
				
				if($saldo_awal_kurs != 0){
					$flag_kurs = TRUE;
					$flag_saldo_awal = TRUE;
				}
				
				if($saldo_akhir_kurs != 0){
					$flag_kurs = TRUE;
					$flag_saldo_akhir = TRUE;
				}
				
				if(count($mutasi_d > 0)){
					if($mutasi_d[0]->total_mutasi != NULL){
						foreach($mutasi_d as $md){
							$flag_kurs = TRUE;
							$flag_mutasi_in = TRUE;
							$mutasi_in = $md->total_mutasi;
						}
					}
				}
				
				if(count($mutasi_k > 0)){
					if($mutasi_k[0]->total_mutasi != NULL){
						foreach($mutasi_k as $mk){
							$flag_kurs = TRUE;
							$flag_mutasi_out = TRUE;
							$mutasi_out = $mk->total_mutasi;
						}
					}
				}
				
				
				
				/*----------------------------------------------------*/
				
				/*-------- Menampilkan Data Dalam Tabel Report -------*/
				
				if($flag_kurs == TRUE){
					$count_data = $count_data + 1;
					
					$sheet->setCellValue('A'.$baris.'', $count_data);
					$sheet->setCellValue('B'.$baris.'', $ka->accountname);
					$sheet->setCellValue('C'.$baris.'', $saldo_awal_kurs);
					
					if($acc_group == '1' || $acc_group == '5'){
						$sum_bb = $sum_bb + $saldo_awal_kurs;
					}else{
						$sum_bb = $sum_bb - $saldo_awal_kurs;
					}
					
					if($flag_mutasi_in != TRUE){
						$mutasi_in = 0;	
					}
					
					$sum_d = $sum_d + $mutasi_in;
					
					$sheet->setCellValue('D'.$baris.'', $mutasi_in);
					
					if($flag_mutasi_out != TRUE){
						$mutasi_out = 0;
					}
					
					$sum_k = $sum_k + $mutasi_out;
					
					$sheet->setCellValue('E'.$baris.'', $mutasi_out);
					$sheet->setCellValue('F'.$baris.'', $saldo_akhir_kurs);
					
					if($acc_group == '1' || $acc_group == '5'){
						$sum_eb = $sum_eb + $saldo_akhir_kurs;
					}else{
						$sum_eb = $sum_eb - $saldo_akhir_kurs;
					}
					
					$objPHPExcel->getActiveSheet()->getStyle('C'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
					
					$baris = $baris + 1;
				}
			}
			
			$sheet->setCellValue('C'.$baris.'', $sum_bb);
			$sheet->setCellValue('D'.$baris.'', $sum_d);
			$sheet->setCellValue('E'.$baris.'', $sum_k);
			$sheet->setCellValue('F'.$baris.'', $sum_eb);
			
			$objPHPExcel->getActiveSheet()->getStyle("C".$baris.":F".$baris)->applyFromArray(array(
				'borders' => array(
					'top' => array(
						'style' => PHPExcel_Style_Border::BORDER_DOUBLE
					)
				)
			));
			$objPHPExcel->getActiveSheet()->getStyle('C'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
			
			$baris = $baris + 1;
			
			$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$baris.'')->applyFromArray($style_font);
			$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$baris.'')->getAlignment()->setWrapText(true);
		}else if($filter_dr == 'D'){
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(55);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
			
			$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
			$objPHPExcel->getActiveSheet()->getStyle('A1:G5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$style_font_header = array(
				'font'  => array(
					'bold'  => true
				)
			);
			
			$objPHPExcel->getActiveSheet()->getStyle('A1:G5')->applyFromArray($style_font_header);
			
			$sheet->setCellValue('A1', 'Kartu Piutang Karyawan Detail, Cabang '.$site_name);
			$sheet->setCellValue('A2', 'Tanggal '.$dari_tanggal.' S/D '.$sampai_tanggal);
			$sheet->setCellValue('A3', $kas_tulis);
			
			$sheet->setCellValue('A5', 'No');
			$sheet->setCellValue('B5', 'Tanggal');
			$sheet->setCellValue('C5', 'Keterangan');
			$sheet->setCellValue('D5', 'ID Transaksi');
			$sheet->setCellValue('E5', 'Tarik');
			$sheet->setCellValue('F5', 'Setor');
			$sheet->setCellValue('G5', 'Saldo');
			
			$objPHPExcel->getActiveSheet()->getStyle("A5:G5")->applyFromArray(array(
				'borders' => array(
					'top' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					),
					'bottom' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			));
			
			$baris = 6;
			
			$number = 1;
			
			foreach($kas_account as $ka){
				$saldo_awal_kurs = 0;
				
				$flag_tulis = TRUE;
				$flag_kurs = '';
				$flag_saldo_awal = '';
				$flag_mutasi = '';

				$acc_number = $ka->accountnumber;
				$accountnumber = $ka->accountnumber;
				
				/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
				$saldo_awal_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
				
				/*--------------------------------------------------*/
			
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$from_date);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$from_date);
				
				foreach($saldo_awal_d as $mtyd){
					if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
						$saldo_awal_kurs = $saldo_awal_kurs + $mtyd->total_mutasi;
					}else{
						$saldo_awal_kurs = $saldo_awal_kurs - $mtyd->total_mutasi;
					}
				}
				
				foreach($saldo_awal_k as $mtyk){
					if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
						$saldo_awal_kurs = $saldo_awal_kurs - $mtyk->total_mutasi;
					}else{
						$saldo_awal_kurs = $saldo_awal_kurs + $mtyk->total_mutasi;
					}
				}
						
				/*---------------------------------------------------*/
			
				/*---------- Mengambil Data Mutasi Transaksi ----------*/
		
				$mutasi_transaksi = $this->mm->get_report_mutasi_rp($from_date,$to_date,$acc_number);
				
				/*---------------------------------------------------*/
			
				/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
				
				$flag_kurs = FALSE;
				$flag_saldo_awal = FALSE;
				$flag_mutasi = FALSE;
				
				if($saldo_awal_kurs != 0){
					$flag_kurs = TRUE;
					$flag_saldo_awal = TRUE;
				}
				
				foreach($mutasi_transaksi as $mk){
					if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
						$flag_kurs = TRUE;
						$flag_mutasi = TRUE;
					}
				}
				
				/*----------------------------------------------------*/
			
				/*-------- Menampilkan Data Dalam Tabel Report -------*/
				
				if($flag_kurs == TRUE){
					$coa_data = $this->mm->get_accountname_by_accountint_rp($ka->accountnumberint);
					
					$running_balance = $saldo_awal_kurs;
					$total_debet = 0;
					$total_kredit = 0;
					
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$baris.':F'.$baris);
					$sheet->setCellValue('A'.$baris.'', $number);
					$sheet->setCellValue('B'.$baris.'', $coa_data);
					$sheet->setCellValue('G'.$baris.'', $saldo_awal_kurs);
					$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
					
					$baris = $baris + 1;
					
					$count_data = 1;
					foreach($mutasi_transaksi as $mk){
						if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
							$trans_date = strtotime($mk->transdate);
							$trans_date = date('d/m/Y',$trans_date);
							
							if($mk->toaccount == $accountnumber){
								$debet_val = $mk->value;
								$kredit_val = 0;
								
								if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
									$running_balance = $running_balance + $mk->value;
								}else{
									$running_balance = $running_balance - $mk->value;
								}
								
								$total_debet = $total_debet + $mk->value;
							}else{
								$debet_val = 0;
								$kredit_val = $mk->value;
								
								if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
									$running_balance = $running_balance - $mk->value;
								}else{
									$running_balance = $running_balance + $mk->value;
								}
								
								$total_kredit = $total_kredit + $mk->value;
							}
							
							$sheet->setCellValue('B'.$baris.'', $trans_date);
							$sheet->setCellValue('C'.$baris.'', $mk->description);
							$sheet->setCellValue('D'.$baris.'', $mk->idmutasi);
							$sheet->setCellValue('E'.$baris.'', $debet_val);
							$sheet->setCellValue('F'.$baris.'', $kredit_val);
							$sheet->setCellValue('G'.$baris.'', $running_balance);
							
							$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
							
							$count_data = $count_data + 1;
							$baris = $baris + 1;
						}
					}
					
					$sheet->setCellValue('E'.$baris.'', $total_debet);
					$sheet->setCellValue('F'.$baris.'', $total_kredit);
					
					$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle("E".$baris.":F".$baris)->applyFromArray(array(
						'borders' => array(
							'top' => array(
								'style' => PHPExcel_Style_Border::BORDER_DOUBLE
							)
						)
					));
					
					$number = $number + 1;
					$baris = $baris + 2;
				}
			}
			
			$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$baris.'')->applyFromArray($style_font);
			$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$baris.'')->getAlignment()->setWrapText(true);
		}
		
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="EXCEL KARTU PIUTANG KARYAWAN '.$site_name.' TANGGAL '.$dari_tanggal.' SD '.$sampai_tanggal.'.xlsx"');
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
