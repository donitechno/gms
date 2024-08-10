<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TitipanGram extends CI_Controller{
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
		$this->load->model('M_kelompok_barang','mc');
	}
	
	public function index(){
		$account = $this->mm->get_coa_titip_gr();
		
		$data['view'] = '<div class="ui container fluid">
			<div class="ui pointing secondary menu">
				<a class="item active" data-tab="titipanGram-first" style="width:50%">
					<i class="edit icon"></i> Input Titipan Pelanggan (Emas)
				</a>
				<a class="item" data-tab="titipanGram-second" style="width:50%" onclick=filterTransaksi("titipanGram")>
					<i class="list ol icon"></i> List Titipan Pelanggan (Emas)
				</a>
			</div>
			<div class="ui bottom attached tab segment active" data-tab="titipanGram-first">
				<div class="ui inverted dimmer" id="titipanGram-loaderinput">
					<div class="ui large text loader">Loading</div>
				</div>
				<form  class="ui form form-javascript" id="titipanGram-form" action="'.base_url().'index.php/titipanGram/save" method="post">
				<div class="ui grid">
					<div class="four wide column" style="padding-bottom:0">
						<div class="field">
							<label style="visibility:hidden">-</label>
							<div class="ui fluid twitter right floated labeled icon button" onclick=addAccountTitipan("titipanGram")>
								<i class="plus icon"></i> Tambah Account
							</div>
						</div>
					</div>
					<div class="right floated four wide column" style="padding-bottom:0">
						<div class="field">
							<label>Tanggal Transaksi</label>
							<div id="titipanGram-wrap_tanggal">
								<input type="text" name="titipanGram-dateinput" id="titipanGram-dateinput" readonly onkeydown=entToNextID("titipanGram-input_1_1") onchange=entToNextID("titipanGram-input_1_1")>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0">
						<div class="ui red message" id="titipanGram-wraperror" style="display:none"></div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0" id="titipanGram-wrap_isi_data">
						<table id="titipanGram-tableinput" class="ui celled table table-input" cellspacing="0" width="100%" style="margin-top:15px;">
							<thead>
								<tr class="center aligned">
									<th style="width:30px;">No</th>
									<th style="width:200px;">Jenis Transaksi</th>
									<th style="width:200px;">Account</th>
									<th>Keterangan</th>
									<th style="width:200px;">Jumlah</th>
								</tr>
							</thead>
							<tbody id="titipanGram-pos_body">
								<tr id="titipanGram-pos_tr_1">
									<td class="center aligned">1</td>
									<td>
										<select class="form-pos" onkeydown=entToTabInput("titipanGram","1","1") name="titipanGram-tipe_trans_1" id="titipanGram-input_1_1" onchange=getKeteranganTitipan("titipanGram","1")>
											<option value="I">Pelanggan Setor Titipan</option>
											<option value="O">Pelanggan Tarik Titipan</option>
										</select>
									</td>
									<td>
										<select class="form-pos" onkeydown=entToTabInput("titipanGram","1","2") name="titipanGram-id_account_1" id="titipanGram-input_1_2" onchange=getKeteranganTitipan("titipanGram","1")>
											<option value="">-- Pilih Account --</option>';
											foreach($account as $a){ 
												$accountname = str_replace('TITIPAN PELANGGAN - ','',$a->accountname);
											
												$data['view'] .= '<option value="'.$a->accountnumber.'">'.$accountname.'</option>';
											}
										$data['view'] .= '</select>
									</td>
									<td>
										<input class="form-pos" onkeydown=entToTabInput("titipanGram","1","3") type="text" name="titipanGram-keterangan_1" id="titipanGram-input_1_3" readonly>
									</td>
									<td>
										<input class="form-pos" type="text" name="titipanGram-jumlah_1" id="titipanGram-input_1_4" onkeyup=valueToCurrency("titipanGram","titipanGram-input_1_4","Total") autocomplete="off">
									</td>
								</tr>
							</tbody>
						</table>
						<div class="ui positive right floated labeled icon button" id="titipanGram-btn" onclick=saveTransaksi("titipanGram")>
							<i class="save icon"></i> Simpan
						</div>
					</div>
					<div class="eight wide column">
						<div class="ui grid" style="padding-top:30px;padding-bottom:15px">
							<div class="four wide column ket-bawah" style="padding-bottom:0;padding-top:0">F5</div>
							<div class="twelve wide column ket-bawah" style="padding-bottom:0;padding-top:0">: REFRESH HALAMAN</div>
						</div>
					</div>
					<div class="eight wide column">
						<div class="ui grid" style="padding-top:30px;padding-bottom:15px">
							<div class="eight wide column ket-bawah right aligned" style="padding-bottom:0;padding-top:0">TOTAL (Gram)</div>
							<div class="eight wide column ket-bawah right aligned" id="titipanGram-total" style="padding-bottom:0;padding-top:0"></div>
						</div>
					</div>
				</div>
				</form>
			</div>
			<div class="ui bottom attached tab segment" data-tab="titipanGram-second">
				<div class="ui inverted dimmer" id="titipanGram-loaderlist">
					<div class="ui large text loader">Loading</div>
				</div>
				<form class="ui form form-javascript" id="titipanGram-form-filter" action="'.base_url().'index.php/titipanGram/filter/" method="post">
				<div class="ui grid">
					<div class="fourteen wide centered column" style="padding-bottom:0">
						<div class="fields">
							<div class="three wide field">
								<label>Tgl Transaksi</label>
								<input type="text" name="titipanGram-filterfromdate" id="titipanGram-filterfromdate" readonly>
							</div>
							<div class="one wide field" style="text-align:center;margin-top:30px">
								<label>s.d</label>
							</div>
							<div class="three wide field">
								<label style="visibility:hidden">-</label>
								<input type="text" name="titipanGram-filtertodate" id="titipanGram-filtertodate" readonly>
							</div>
							<div class="four wide field">
								<label>Account</label>
								<select name="titipanGram-filter_account" id="titipanGram-filter_account">
									<option value="All">-- All --</option>';
									foreach($account as $a){
									$data['view'] .= '<option value="'.$a->accountnumber.'">'.$a->accountname.'</option>';
									}
								$data['view'] .= '</select>
							</div>
							<div class="three wide field">
								<label style="visibility:hidden">-</label>
								<select name="titipanGram-filter_dr" id="titipanGram-filter_dr">
									<option value="D">Detail</option>
									<option value="R">Rekap</option>
								</select>
							</div>
							<div class="two wide field">
								<label style="visibility:hidden">-</label>
								<div class="ui fluid icon green button filter-input" id="titipanGram-btnfilter" onclick=filterTransaksi("titipanGram") title="Filter">
									<i class="filter icon"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" id="titipanGram-wrap_filter" style="padding-top:0">
					</div>
				</div>
				</form>
			</div>
		</div>';
		
		$data["date"] = 3;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function save($row_number){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		$this->validate($row_number);
		
		$tanggal_transaksi = $this->input->post('titipanGram-dateinput');
		$tglTrans = $this->date_to_format($tanggal_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		
		$created_by = $this->session->userdata('gold_nama_user');
		
		for($i = 1; $i <= $row_number; $i++){
			$jenis_trans = $this->input->post('titipanGram-tipe_trans_'.$i);
			
			if($jenis_trans == 'I'){
				$to_account = $this->mm->get_default_account('SRT');
				$from_account = $this->input->post('titipanGram-id_account_'.$i);
				$tipe = 'In';
				$kode = 'I';
			}else{
				$from_account = $this->mm->get_default_account('SRT');
				$to_account = $this->input->post('titipanGram-id_account_'.$i);
				$tipe = 'Out';
				$kode = 'O';
			}
			
			$tgl_code = $tgl_transaksi;
			$codetrans = strtotime($tgl_code);
			$codetrans = date('ymd',$codetrans);
			
			$transactioncode = 'G'.$kode;
			$sitecode = $this->mm->get_site_code();
			
			$totalnumberlength = 3;
			$numberlength = strlen($sitecode);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($a = 1; $a <= $numberspace; $a++){
					$transactioncode .= '0';
				}
			}
			
			$transactioncode .= $sitecode.'-'.$codetrans.'-';
			
			$id_urut = $this->mm->get_mutasi_code_gr($transactioncode);
			$totalnumberlength = 3;
			$numberlength = strlen($id_urut);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($a = 1; $a <= $numberspace; $a++){
					$transactioncode .= '0';
				}
			}
			
			$transactioncode .= $id_urut;
			
			$id_karat = 1;
			$keterangan = $this->input->post('titipanGram-keterangan_'.$i);
			$jumlah = $this->input->post('titipanGram-jumlah_'.$i);
			$jumlah = str_replace(',','',$jumlah);
			
			$this->mm->insert_mutasi_gram($sitecode,$transactioncode,$tipe,$id_karat,$from_account,$to_account,$jumlah,$keterangan,$tgl_transaksi,$created_by);
		}
		
		$this->db->trans_complete();
		
		$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Input Berhasil!</div></div>';
		$data['success'] = true;
		echo json_encode($data);
	}
	
	private function validate($row_number){
		$data = array();
		$data['inputerror'] = '<ul class="list"></ul>';
		$data['success'] = TRUE;
		
		for($i = 1; $i <= $row_number; $i++){
			$id_account = $this->input->post('titipanGram-id_account_'.$i);
			$keterangan = $this->input->post('titipanGram-keterangan_'.$i);
			$jumlah = $this->input->post('titipanGram-jumlah_'.$i);
			
			if($id_account == ''){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Account Harus Diisi!</li>';
				echo json_encode($data);
				exit();
			}
			
			if($keterangan == ''){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Keterangan Harus Diisi!</li>';
				echo json_encode($data);
				exit();
			}
			
			if($jumlah == '' || $jumlah == 0 ){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Jumlah Harus Diisi & Tidak Boleh Bernilai 0!</li>';
				echo json_encode($data);
				exit();
			}
		}
		
		if($data['success'] == FALSE){
			echo json_encode($data);
			exit();
		}
	}
	
	public function filter(){
		$this->db->trans_start();
		
		$from_date = $this->input->post('titipanGram-filterfromdate');
		$to_date = $this->input->post('titipanGram-filtertodate');
		$dari_tanggal = $this->input->post('titipanGram-filterfromdate');
		$sampai_tanggal = $this->input->post('titipanGram-filtertodate');
		
		$stockFromTime = $this->date_to_format($from_date);
		$stockToTime = $this->date_to_format($to_date);
		
		$from_date = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_date = date("Y-m-d",$stockToTime).' 00:00:00';
		$to_date2 = date("Y-m-d",$stockToTime).' 23:59:59';
		
		$filter_account = $this->input->post('titipanGram-filter_account');
		$filter_dr = $this->input->post('titipanGram-filter_dr');
		
		if($filter_account == 'All'){
			$kas_account = $this->mm->get_all_titipan_gr();
		}else{
			$kas_account = $this->mm->get_single_coa_gr($filter_account);
		}
		
		if($filter_dr == 'R'){
			$data['view'] = '<div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/titipanGram/pdf/'.$dari_tanggal.'/'.$sampai_tanggal.'/'.$filter_account.'/'.$filter_dr.'" target=_blank"><i class="file pdf outline icon"></i> Download</a></div><table id="titipanGram-tablefilter" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:30px">No</th><th>Account Name</th><th>Saldo Awal</th><th>Tarik</th><th>Setor</th><th>Saldo Akhir</th></tr></thead><tbody>';
			
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
				$id_karat = 1;
				
				/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
				
				if($id_karat == $ka->idkarat){
					$saldo_awal_kurs[$id_karat] = $this->mm->get_report_gr_beginning_balance($acc_number);
					$saldo_akhir_kurs[$id_karat] = $this->mm->get_report_gr_beginning_balance($acc_number);
				}else{
					$saldo_awal_kurs[$id_karat] = 0;
					$saldo_akhir_kurs[$id_karat] = 0;
				}
				
				/*--------------------------------------------------*/
				
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_bykarat($acc_number,$from_date,$id_karat);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_bykarat($acc_number,$from_date,$id_karat);
				$saldo_akhir_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_bykarat($acc_number,$to_date2,$id_karat);
				$saldo_akhir_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_bykarat($acc_number,$to_date2,$id_karat);
				
				foreach($saldo_awal_d as $mtyd){
					if($acc_group == '1' || $acc_group == '5'){
						$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] + $mtyd->total_mutasi;
					}else{
						$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] - $mtyd->total_mutasi;
					}
				}
				
				foreach($saldo_awal_k as $mtyk){
					if($acc_group == '1' || $acc_group == '5'){
						$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] - $mtyk->total_mutasi;
					}else{
						$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] + $mtyk->total_mutasi;
					}
				}
				
				foreach($saldo_akhir_d as $mttd){
					if($acc_group == '1' || $acc_group == '5'){
						$saldo_akhir_kurs[$mttd->idkarat] = $saldo_akhir_kurs[$mttd->idkarat] + $mttd->total_mutasi;
					}else{
						$saldo_akhir_kurs[$mttd->idkarat] = $saldo_akhir_kurs[$mttd->idkarat] - $mttd->total_mutasi;
					}
				}
			
				foreach($saldo_akhir_k as $mttk){
					if($acc_group == '1' || $acc_group == '5'){
						$saldo_akhir_kurs[$mttk->idkarat] = $saldo_akhir_kurs[$mttk->idkarat] - $mttk->total_mutasi;
					}else{
						$saldo_akhir_kurs[$mttk->idkarat] = $saldo_akhir_kurs[$mttk->idkarat] + $mttk->total_mutasi;
					}
				}	
				
				/*---------------------------------------------------*/
				
				/*---- Menentukan Mutasi Masuk dan Mutasi Keluar ----*/
		
				$mutasi_d = $this->mm->get_mutasi_in_gr($acc_number,$from_date,$to_date);	
				$mutasi_k = $this->mm->get_mutasi_out_gr($acc_number,$from_date,$to_date);
				
				/*---------------------------------------------------*/
				
				/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
				
				$flag_kurs[$id_karat] = FALSE;
				$flag_saldo_awal[$id_karat] = FALSE;
				$flag_mutasi_in[$id_karat] = FALSE;
				$flag_mutasi_out[$id_karat] = FALSE;
				$flag_saldo_akhir[$id_karat] = FALSE;
				
				if($saldo_awal_kurs[$id_karat] != 0){
					$flag_kurs[$id_karat] = TRUE;
					$flag_saldo_awal[$id_karat] = TRUE;
				}
				
				if($saldo_akhir_kurs[$id_karat] != 0){
					$flag_kurs[$id_karat] = TRUE;
					$flag_saldo_akhir[$id_karat] = TRUE;
				}
				
				foreach($mutasi_d as $md){
					if($md->idkarat == $id_karat){
						$flag_kurs[$id_karat] = TRUE;
						$flag_mutasi_in[$id_karat] = TRUE;
						$mutasi_in[$id_karat] = $md->total_mutasi;
					}
				}
				
				foreach($mutasi_k as $mk){
					if($mk->idkarat == $id_karat){
						$flag_kurs[$id_karat] = TRUE;
						$flag_mutasi_out[$id_karat] = TRUE;
						$mutasi_out[$id_karat] = $mk->total_mutasi;
					}
				}
				
				/*----------------------------------------------------*/
				
				/*-------- Menampilkan Data Dalam Tabel Report -------*/
				
				if($flag_kurs[$id_karat] == TRUE){
					$count_data = $count_data + 1;
					
					$data['view'] .= '<tr><td class="center aligned">'.$count_data.'</td>';
					
					if($saldo_awal_kurs[$id_karat] != 0 && $saldo_awal_kurs[$id_karat] != '' && $saldo_awal_kurs[$id_karat] != -0){
						$data['view'] .= '<td>'.$ka->accountname.'</td>';
						if($acc_group == '1' || $acc_group == '5'){
							$data['view'] .= '<td class="right aligned">'.number_format($saldo_awal_kurs[$id_karat], 3).'</td>';
						}else{
							$data['view'] .= '<td class="right aligned">'.number_format($saldo_awal_kurs[$id_karat], 3).'</td>';
						}
						
						if($acc_group == '1' || $acc_group == '5'){
							$sum_bb = $sum_bb + $saldo_awal_kurs[$id_karat];
						}else{
							$sum_bb = $sum_bb - $saldo_awal_kurs[$id_karat];
						}
					}else{
						$data['view'] .= '<td>'.$ka->accountname.'</td><td class="right aligned">-</td>';
					}
					
					if($flag_mutasi_in[$id_karat] == TRUE){
						$data['view'] .= '<td class="right aligned">'.number_format($mutasi_in[$id_karat], 3).'</td>';
						$sum_d = $sum_d + $mutasi_in[$id_karat];
					}else{
						$data['view'] .= '<td class="right aligned">-</td>';
					}
					
					if($flag_mutasi_out[$id_karat] == TRUE){
						$data['view'] .= '<td class="right aligned">'.number_format($mutasi_out[$id_karat], 3).'</td>';
						$sum_k = $sum_k + $mutasi_out[$id_karat];
					}else{
						$data['view'] .= '<td class="right aligned">-</td>';
					}
					
					if($saldo_akhir_kurs[$id_karat] != 0 && $saldo_akhir_kurs[$id_karat] != '' && $saldo_akhir_kurs[$id_karat] != -0){
						if($acc_group == '1' || $acc_group == '5'){
							$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs[$id_karat], 3).'</td>';
						}else{
							$data['view'] .= '<td class="right aligned">'.number_format($saldo_akhir_kurs[$id_karat], 3).'</td>';
						}
						
						if($acc_group == '1' || $acc_group == '5'){
							$sum_eb = $sum_eb + $saldo_akhir_kurs[$id_karat];
						}else{
							$sum_eb = $sum_eb - $saldo_akhir_kurs[$id_karat];
						}
						
					}else{
						$data['view'] .= '<td class="right aligned">-</td>';
					}
					
					$data['view'] .= '</tr>';
				}
			}
			
			if($count_data != 0){
				$data['view'] .= '<tr><td colspan="2"></td><td class="right aligned" style="border-top:2px solid #000;font-weight:bold">'.number_format($sum_bb*-1, 3).'</td><td class="right aligned" style="border-top:2px solid #000;font-weight:bold">'.number_format($sum_d, 3).'</td><td class="right aligned" style="border-top:2px solid #000;font-weight:bold">'.number_format($sum_k, 3).'</td><td class="right aligned" style="border-top:2px solid #000;font-weight:bold">'.number_format($sum_eb*-1, 3).'</td>';
			}
		}else if($filter_dr == 'D'){
			$data['view'] = '<div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/titipanGram/pdf/'.$dari_tanggal.'/'.$sampai_tanggal.'/'.$filter_account.'/'.$filter_dr.'" target=_blank"><i class="file pdf outline icon"></i> Download</a></div><table id="filter_data_tabel" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:50px">No</th><th>Tanggal</th><th>Keterangan</th><th>ID Transaksi</th><th>Tarik</th><th>Setor</th><th>Saldo</th><th>Act</th></tr></thead><tbody>';
			
			foreach($kas_account as $ka){
				$saldo_awal_kurs = array();
				
				$flag_tulis = TRUE;
				$flag_kurs = array();
				$flag_saldo_awal = array();
				$flag_mutasi = array();

				$acc_number = $ka->accountnumber;
				$accountnumber = $ka->accountnumber;
				
				$id_karat = 1;
			
				/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
				
				if($id_karat == $ka->idkarat){
					$saldo_awal_kurs[$id_karat] = $this->mm->get_report_gr_beginning_balance($acc_number);
				}else{
					$saldo_awal_kurs[$id_karat] = 0;
				}
				
				
				/*--------------------------------------------------*/
			
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_bykarat($acc_number,$from_date,$id_karat);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_bykarat($acc_number,$from_date,$id_karat);
				
				foreach($saldo_awal_d as $mtyd){
					if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
						$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] + $mtyd->total_mutasi;
					}else{
						$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] - $mtyd->total_mutasi;
					}
				}
				
				foreach($saldo_awal_k as $mtyk){
					if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
						$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] - $mtyk->total_mutasi;
					}else{
						$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] + $mtyk->total_mutasi;
					}
				}
						
				/*---------------------------------------------------*/
			
				/*---------- Mengambil Data Mutasi Transaksi ----------*/
		
				$mutasi_transaksi = $this->mm->get_report_mutasi_gr_by_karat($id_karat,$from_date,$to_date, $acc_number);
				
				/*---------------------------------------------------*/
			
				/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
				
				$flag_kurs[$id_karat] = FALSE;
				$flag_saldo_awal[$id_karat] = FALSE;
				$flag_mutasi[$id_karat] = FALSE;
				
				if($saldo_awal_kurs[$id_karat] != 0){
					$flag_kurs[$id_karat] = TRUE;
					$flag_saldo_awal[$id_karat] = TRUE;
				}
				
				foreach($mutasi_transaksi as $mk){
					if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
						$flag_kurs[$id_karat] = TRUE;
						$flag_mutasi[$id_karat] = TRUE;
					}
				}
				
				/*----------------------------------------------------*/
			
				/*-------- Menampilkan Data Dalam Tabel Report -------*/
				
				if($flag_kurs[$id_karat] == TRUE){	
					$coa_data = $this->mm->get_accountname_by_accountint($ka->accountnumberint);
					
					$running_balance = $saldo_awal_kurs[$id_karat];
					$total_debet = 0;
					$total_kredit = 0;
					
					if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
						if($saldo_awal_kurs[$id_karat] == 0 || $saldo_awal_kurs[$id_karat] == ''){
							$data['view'] .= '<tr><td class="center aligned" width="50px"></td><td colspan="5">'.$coa_data.'</td><td class="right aligned">-</td><td></td>';
						}else{
							$data['view'] .= '<tr><td class="center aligned" width="50px"></td><td colspan="5">'.$coa_data.'</td><td class="right aligned">'.number_format($saldo_awal_kurs[$id_karat], 3).'</td><td></td>';
						}
					}else{
						$data['view'] .= '<tr><td class="center aligned" width="50px"></td><td colspan="5">'.$coa_data.'</td><td class="right aligned">'.number_format($saldo_awal_kurs[$id_karat], 3).'</td><td></td>';
					}
					
					$count_data = 1;
					foreach($mutasi_transaksi as $mk){
						if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
							$trans_date = strtotime($mk->transdate);
							$trans_date = date('d/m/Y',$trans_date);
							
							if($mk->toaccount == $accountnumber){
								$debet_val = number_format($mk->value, 3);
								$kredit_val = '-';
								
								if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
									$running_balance = $running_balance + $mk->value;
								}else{
									$running_balance = $running_balance - $mk->value;
								}
								
								$total_debet = $total_debet + $mk->value;
							}else{
								$debet_val = '-';
								$kredit_val = number_format($mk->value, 3);
								
								if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
									$running_balance = $running_balance - $mk->value;
								}else{
									$running_balance = $running_balance + $mk->value;
								}
								
								$total_kredit = $total_kredit + $mk->value;
							}
							
							$act = '';
							if($this->session->userdata('gold_admin') == 'Y' || $this->session->userdata('gold_pembukuan') == 'Y' || $this->session->userdata('gold_manager') == 'Y'){
								$act = '<button type="button" class="ui mini red icon button" onclick=deleteTransMutasi("titipanGram","'.$mk->idmutasi.'") title="Delete"><i class="ban icon"></i></button>';
							}
							
							if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
								$data['view'] .= '<tr><td class="center aligned">'.$count_data.'</td><td>'.$trans_date.'</td><td>'.$mk->description.'</td><td>'.$mk->idmutasi.'</td><td class="right aligned">'.$debet_val.'</td><td class="right aligned">'.$kredit_val.'</td><td class="right aligned">'.number_format($running_balance, 3).'</td><td class="center aligned">'.$act.'</td></tr>';
							}else{
								$data['view'] .= '<tr><td class="center aligned">'.$count_data.'</td><td>'.$trans_date.'</td><td>'.$mk->description.'</td><td>'.$mk->idmutasi.'</td><td class="right aligned">'.$debet_val.'</td><td class="right aligned">'.$kredit_val.'</td><td class="right aligned">'.number_format($running_balance, 3).'</td><td class="center aligned">'.$act.'</td></tr>';
							}
							
							$count_data = $count_data + 1;
						}
					}
					
					$data['view'] .= '<tr><td colspan="4"></td><td class="right aligned" style="border-top: 2px solid #000">'.number_format($total_debet, 3).'</td><td class="right aligned" style="border-top: 2px solid #000">'.number_format($total_kredit, 3).'</td><td></td><td></td></tr>';
					$data['view'] .= '<tr><td colspan="8" style="visibility:hidden; color:#FFF">-</td></tr>';
				}
			}
		}
		
		$data['view'] .= '</tbody></table>';
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function get_input_form(){
		$data['view'] = '<i class="close icon"></i><div class="header">Input Account Titipan Emas</div><div class="content"><div class="ui error message" id="titipanGram-errormodal" style="display:none"></div>';
		
		$data['view'] .= '<form class="ui form form-javascript" id="titipanGram-formadd" action="'.base_url().'index.php/titipanGram/save_account" method="post"><div class="field"><label>Nama Pelanggan</label><input type="text" class="form-control" name="titipanGram-account_name" id="titipanGram-input_data_1" onkeydown=entToNextID("titipanGram-input_data_2") autocomplete="off"></div>';
		
		if($this->session->userdata('gold_admin') == 'Y'){
			$data['view'] .= '<div class="field"><label>Saldo Awal</label><input type="text" class="form-control" name="titipanGram-beginning_balance" id="titipanGram-input_data_2" onkeydown=entToNextID("titipanGram-btnsaveadd") onkeyup=valueToCurrency("titipanGram","titipanGram-input_data_2","noTotal") autocomplete="off"></div>';
		}else{
			$data['view'] .= '<input type="hidden" class="form-control" name="titipanGram-beginning_balance" id="titipanGram-input_data_2" onkeydown=entToNextID("titipanGram-btnsaveadd") onkeyup=valueToCurrency("titipanGram","titipanGram-input_data_2","noTotal") autocomplete="off">';
		}
		
		$data['view'] .= '</form></div><div class="actions"><button id="titipanGram-btnsaveadd" class="ui green labeled icon button" onclick=saveAddTitipan("titipanGram")>Simpan	<i class="save icon"></i></button></div>';
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function save_account(){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		$this->validate_add();
		
		$account_name = $this->input->post('titipanGram-account_name');
		$beginning_balance = $this->input->post('titipanGram-beginning_balance');
		$beginning_balance = str_replace(',','',$beginning_balance);
		
		$account_number_int = $this->mm->generate_account_titipan_gr();
		$acc1 = substr($account_number_int, 0, 2);
		$acc2 = substr($account_number_int, -4);
		$account_number = $acc1.'-'.$acc2;
		$account_group = 2;
		$type = 'TTP';
		$idkarat = 1;
		$created_by = $this->session->userdata('gold_nama_user');
		
		$account_awalan = strtoupper($account_name);
		$account_awalan = substr($account_name,0,1);
		
		$id_pelanggan = $this->mm->generate_id_titipan_gr($account_awalan);
		
		$nama_account = 'TITIPAN PELANGGAN - '.$id_pelanggan.' '.strtoupper($account_name);
		$this->mm->insert_coa_gr($account_number,$account_number_int,$nama_account,$account_group,$beginning_balance,$type,$idkarat,$created_by);
		$this->mm->insert_coa_titipan_gr($id_pelanggan,strtoupper($account_name),$created_by);
		
		$this->db->trans_complete();
		
		$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Input Berhasil!</div></div>';
		$data['lokasi'] = base_url().'index.php/C_titipan_gr';
		$data['success'] = true;
		echo json_encode($data);
	}
	
	private function validate_add(){
		$data = array();
		$data['inputerror'] = '<ul class="list"></ul>';
		$data['success'] = TRUE;
		
		$account_name = $this->input->post('titipanGram-account_name');
		$beginning_balance = $this->input->post('titipanGram-beginning_balance');
		$beginning_balance = str_replace(',','',$beginning_balance);
		
		if($account_name == ''){
			$data['inputerror'] = '<li>Nama Pelanggan Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		$cek_nama = 'TITIPAN PELANGGAN - '.$account_name;
		
		if($account_name != ''){
			$cek_nama_account = $this->mm->cek_nama_account_titipan_gr($cek_nama);
			if(count($cek_nama_account) > 0){
				$data['inputerror'] = '<li>Nama Pelanggan Sudah Dipakai, Harap Gunakan Nama Lain!</li>';
				$data['success'] = FALSE;
			}
		}
		
		if($data['success'] == FALSE){
			echo json_encode($data);
			exit();
		}
	}
	
	public function hapus($id){
		if($this->session->userdata('gold_admin') == 'Y' || $this->session->userdata('gold_pembukuan') == 'Y' || $this->session->userdata('gold_manager') == 'Y'){
			date_default_timezone_set("Asia/Jakarta");
			$this->db->trans_start();
			
			$mutasi_data = $this->mm->get_mutasi_gr_by_id($id);
			$deletedby = $this->session->userdata('gold_nama_user');
			
			foreach($mutasi_data as $md){
				$siteid = $md->idsite;
				$idmutasi = $md->idmutasi;
				$tipemutasi = $md->tipemutasi;
				$idkarat = $md->idkarat;
				$fromaccount = $md->fromaccount;
				$toaccount = $md->toaccount;
				$value = $md->value;
				$description = $md->description;
				$transdate = $md->transdate;
				$createddate = $md->createddate;
				$createdby = $md->createdby;
				
				$this->mm->insert_mutasi_gram_deleted($siteid,$idmutasi,$tipemutasi,$idkarat,$fromaccount,$toaccount,$value,$description,$transdate,$createddate,$createdby,$deletedby);
				$this->mm->delete_mutasi_gram($id);
			}
			
			$this->db->trans_complete();
			
			$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Berhasil Hapus Data!</div></div>';
			$data['success'] = true;
			echo json_encode($data);
		}
	}
	
	public function pdf($from_date,$to_date,$filter_account,$filter_dr){
		$from_date = str_replace('%20',' ',$from_date);
		$to_date = str_replace('%20',' ',$to_date);
		
		$dari_tanggal = $from_date;
		$sampai_tanggal = $to_date;
		
		if($dari_tanggal == $sampai_tanggal){
			$tanggal_tulis = $dari_tanggal;
		}else{
			$tanggal_tulis = $dari_tanggal.' s.d '.$sampai_tanggal;
		}
		
		$stockFromTime = $this->date_to_format($from_date);
		$stockToTime = $this->date_to_format($to_date);
		
		$from_date = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_date = date("Y-m-d",$stockToTime).' 00:00:00';
		$to_date2 = date("Y-m-d",$stockToTime).' 23:59:59';
		
		if($filter_account == 'All'){
			$kas_account = $this->mm->get_all_titipan_gr();
			$kas_tulis = 'All Pelanggan';
		}else{
			$kas_account = $this->mm->get_single_coa_gr($filter_account);
			$kas_tulis = $this->mm->get_coa_number_name_gr_2($filter_account);
		}
		
		$site_name = $this->mm->get_site_name();
		
		if($filter_dr == 'R'){
			$data['view'] = '<div class="sixteen wide centered column center aligned" style="font-weight:bold;text-align:center;font-size:13px;font-family:samsungone"><span style="text-align">Laporan Titipan Pelanggan (Gram) Rekap, Cabang '.$site_name.'</span><br><span>Tanggal '.$tanggal_tulis.'</span><br><span>'.$kas_tulis.'</span><br><span style="visibility:hidden">-</span></div><table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:30px" class="th-5">No</th><th class="th-5">Account Name</th><th class="th-5">Saldo Awal</th><th class="th-5">Tarik</th><th class="th-5">Setor</th><th class="th-5">Saldo Akhir</th></tr></thead><tbody>';
			
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
				$id_karat = 1;
				
				/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
				
				if($id_karat == $ka->idkarat){
					$saldo_awal_kurs[$id_karat] = $this->mm->get_report_gr_beginning_balance($acc_number);
					$saldo_akhir_kurs[$id_karat] = $this->mm->get_report_gr_beginning_balance($acc_number);
				}else{
					$saldo_awal_kurs[$id_karat] = 0;
					$saldo_akhir_kurs[$id_karat] = 0;
				}
				
				/*--------------------------------------------------*/
				
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_bykarat($acc_number,$from_date,$id_karat);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_bykarat($acc_number,$from_date,$id_karat);
				$saldo_akhir_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_bykarat($acc_number,$to_date2,$id_karat);
				$saldo_akhir_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_bykarat($acc_number,$to_date2,$id_karat);
				
				foreach($saldo_awal_d as $mtyd){
					if($acc_group == '1' || $acc_group == '5'){
						$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] + $mtyd->total_mutasi;
					}else{
						$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] - $mtyd->total_mutasi;
					}
				}
				
				foreach($saldo_awal_k as $mtyk){
					if($acc_group == '1' || $acc_group == '5'){
						$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] - $mtyk->total_mutasi;
					}else{
						$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] + $mtyk->total_mutasi;
					}
				}
				
				foreach($saldo_akhir_d as $mttd){
					if($acc_group == '1' || $acc_group == '5'){
						$saldo_akhir_kurs[$mttd->idkarat] = $saldo_akhir_kurs[$mttd->idkarat] + $mttd->total_mutasi;
					}else{
						$saldo_akhir_kurs[$mttd->idkarat] = $saldo_akhir_kurs[$mttd->idkarat] - $mttd->total_mutasi;
					}
				}
			
				foreach($saldo_akhir_k as $mttk){
					if($acc_group == '1' || $acc_group == '5'){
						$saldo_akhir_kurs[$mttk->idkarat] = $saldo_akhir_kurs[$mttk->idkarat] - $mttk->total_mutasi;
					}else{
						$saldo_akhir_kurs[$mttk->idkarat] = $saldo_akhir_kurs[$mttk->idkarat] + $mttk->total_mutasi;
					}
				}	
				
				/*---------------------------------------------------*/
				
				/*---- Menentukan Mutasi Masuk dan Mutasi Keluar ----*/
		
				$mutasi_d = $this->mm->get_mutasi_in_gr($acc_number,$from_date,$to_date);	
				$mutasi_k = $this->mm->get_mutasi_out_gr($acc_number,$from_date,$to_date);
				
				/*---------------------------------------------------*/
				
				/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
				
				$flag_kurs[$id_karat] = FALSE;
				$flag_saldo_awal[$id_karat] = FALSE;
				$flag_mutasi_in[$id_karat] = FALSE;
				$flag_mutasi_out[$id_karat] = FALSE;
				$flag_saldo_akhir[$id_karat] = FALSE;
				
				if($saldo_awal_kurs[$id_karat] != 0){
					$flag_kurs[$id_karat] = TRUE;
					$flag_saldo_awal[$id_karat] = TRUE;
				}
				
				if($saldo_akhir_kurs[$id_karat] != 0){
					$flag_kurs[$id_karat] = TRUE;
					$flag_saldo_akhir[$id_karat] = TRUE;
				}
				
				foreach($mutasi_d as $md){
					if($md->idkarat == $id_karat){
						$flag_kurs[$id_karat] = TRUE;
						$flag_mutasi_in[$id_karat] = TRUE;
						$mutasi_in[$id_karat] = $md->total_mutasi;
					}
				}
				
				foreach($mutasi_k as $mk){
					if($mk->idkarat == $id_karat){
						$flag_kurs[$id_karat] = TRUE;
						$flag_mutasi_out[$id_karat] = TRUE;
						$mutasi_out[$id_karat] = $mk->total_mutasi;
					}
				}
				
				/*----------------------------------------------------*/
				
				/*-------- Menampilkan Data Dalam Tabel Report -------*/
				
				if($flag_kurs[$id_karat] == TRUE){
					$count_data = $count_data + 1;
					
					$data['view'] .= '<tr><td class="right-aligned">'.$count_data.'. </td>';
					
					if($saldo_awal_kurs[$id_karat] != 0 && $saldo_awal_kurs[$id_karat] != '' && $saldo_awal_kurs[$id_karat] != -0){
						$data['view'] .= '<td>'.$ka->accountname.'</td>';
						if($acc_group == '1' || $acc_group == '5'){
							$data['view'] .= '<td class="right-aligned">'.number_format($saldo_awal_kurs[$id_karat], 3).'</td>';
						}else{
							$data['view'] .= '<td class="right-aligned">'.number_format($saldo_awal_kurs[$id_karat], 3).'</td>';
						}
						
						if($acc_group == '1' || $acc_group == '5'){
							$sum_bb = $sum_bb + $saldo_awal_kurs[$id_karat];
						}else{
							$sum_bb = $sum_bb - $saldo_awal_kurs[$id_karat];
						}
					}else{
						$data['view'] .= '<td>'.$ka->accountname.'</td><td class="right-aligned">-</td>';
					}
					
					if($flag_mutasi_in[$id_karat] == TRUE){
						$data['view'] .= '<td class="right-aligned">'.number_format($mutasi_in[$id_karat], 3).'</td>';
						$sum_d = $sum_d + $mutasi_in[$id_karat];
					}else{
						$data['view'] .= '<td class="right-aligned">-</td>';
					}
					
					if($flag_mutasi_out[$id_karat] == TRUE){
						$data['view'] .= '<td class="right-aligned">'.number_format($mutasi_out[$id_karat], 3).'</td>';
						$sum_k = $sum_k + $mutasi_out[$id_karat];
					}else{
						$data['view'] .= '<td class="right-aligned">-</td>';
					}
					
					if($saldo_akhir_kurs[$id_karat] != 0 && $saldo_akhir_kurs[$id_karat] != '' && $saldo_akhir_kurs[$id_karat] != -0){
						if($acc_group == '1' || $acc_group == '5'){
							$data['view'] .= '<td class="right-aligned">'.number_format($saldo_akhir_kurs[$id_karat], 3).'</td>';
						}else{
							$data['view'] .= '<td class="right-aligned">'.number_format($saldo_akhir_kurs[$id_karat], 3).'</td>';
						}
						
						if($acc_group == '1' || $acc_group == '5'){
							$sum_eb = $sum_eb + $saldo_akhir_kurs[$id_karat];
						}else{
							$sum_eb = $sum_eb - $saldo_akhir_kurs[$id_karat];
						}
						
					}else{
						$data['view'] .= '<td class="right-aligned">-</td>';
					}
					
					$data['view'] .= '</tr>';
				}
			}
			
			if($count_data != 0){
				$data['view'] .= '<tr><td colspan="2"></td><td class="right-aligned" style="border-top:1px solid #000;font-weight:bold">'.number_format($sum_bb*-1, 3).'</td><td class="right-aligned" style="border-top:1px solid #000;font-weight:bold">'.number_format($sum_d, 3).'</td><td class="right-aligned" style="border-top:1px solid #000;font-weight:bold">'.number_format($sum_k, 3).'</td><td class="right-aligned" style="border-top:1px solid #000;font-weight:bold">'.number_format($sum_eb*-1, 3).'</td>';
			}
		}else if($filter_dr == 'D'){
			$data['view'] = '<div class="sixteen wide centered column center aligned" style="font-weight:bold;text-align:center;font-size:13px;font-family:samsungone"><span style="text-align">Laporan Titipan Pelanggan (Gram) Detail, Cabang '.$site_name.'</span><br><span>Tanggal '.$tanggal_tulis.'</span><br><span>'.$kas_tulis.'</span><br><span style="visibility:hidden">-</span></div><table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:50px" class="th-5">No</th><th class="th-5">Tanggal</th><th class="th-5">Keterangan</th><th class="th-5">ID Transaksi</th><th class="th-5" style="width:80px">Tarik</th><th class="th-5" style="width:80px">Setor</th><th class="th-5" style="width:80px">Saldo</th></tr></thead><tbody>';
			
			foreach($kas_account as $ka){
				$saldo_awal_kurs = array();
				
				$flag_tulis = TRUE;
				$flag_kurs = array();
				$flag_saldo_awal = array();
				$flag_mutasi = array();

				$acc_number = $ka->accountnumber;
				$accountnumber = $ka->accountnumber;
				
				$id_karat = 1;
			
				/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
			
				
				if($id_karat == $ka->idkarat){
					$saldo_awal_kurs[$id_karat] = $this->mm->get_report_gr_beginning_balance($acc_number);
				}else{
					$saldo_awal_kurs[$id_karat] = 0;
				}
				
				
				/*--------------------------------------------------*/
			
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_bykarat($acc_number,$from_date,$id_karat);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_bykarat($acc_number,$from_date,$id_karat);
				
				foreach($saldo_awal_d as $mtyd){
					if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
						$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] + $mtyd->total_mutasi;
					}else{
						$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] - $mtyd->total_mutasi;
					}
				}
				
				foreach($saldo_awal_k as $mtyk){
					if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
						$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] - $mtyk->total_mutasi;
					}else{
						$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] + $mtyk->total_mutasi;
					}
				}
						
				/*---------------------------------------------------*/
			
				/*---------- Mengambil Data Mutasi Transaksi ----------*/
		
				$mutasi_transaksi = $this->mm->get_report_mutasi_gr_by_karat($id_karat,$from_date,$to_date, $acc_number);
				
				/*---------------------------------------------------*/
			
				/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
				
				$flag_kurs[$id_karat] = FALSE;
				$flag_saldo_awal[$id_karat] = FALSE;
				$flag_mutasi[$id_karat] = FALSE;
				
				if($saldo_awal_kurs[$id_karat] != 0){
					$flag_kurs[$id_karat] = TRUE;
					$flag_saldo_awal[$id_karat] = TRUE;
				}
				
				foreach($mutasi_transaksi as $mk){
					if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
						$flag_kurs[$id_karat] = TRUE;
						$flag_mutasi[$id_karat] = TRUE;
					}
				}
				
				/*----------------------------------------------------*/
			
				/*-------- Menampilkan Data Dalam Tabel Report -------*/
				
				if($flag_kurs[$id_karat] == TRUE){	
					$coa_data = $this->mm->get_accountname_by_accountint($ka->accountnumberint);
					
					$running_balance = $saldo_awal_kurs[$id_karat];
					$total_debet = 0;
					$total_kredit = 0;
					
					if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
						if($saldo_awal_kurs[$id_karat] == 0 || $saldo_awal_kurs[$id_karat] == ''){
							$data['view'] .= '<tr><td class="center aligned" width="50px"></td><td colspan="5" style="font-weight:bold">'.$coa_data.'</td><td class="right-aligned">-</td>';
						}else{
							$data['view'] .= '<tr><td class="center aligned" width="50px"></td><td colspan="5" style="font-weight:bold">'.$coa_data.'</td><td class="right-aligned">'.number_format($saldo_awal_kurs[$id_karat], 3).'</td>';
						}
					}else{
						$data['view'] .= '<tr><td class="center aligned" width="50px"></td><td colspan="5" style="font-weight:bold">'.$coa_data.'</td><td class="right-aligned">'.number_format($saldo_awal_kurs[$id_karat], 3).'</td>';
					}
					
					$count_data = 1;
					foreach($mutasi_transaksi as $mk){
						if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
							$trans_date = strtotime($mk->transdate);
							$trans_date = date('d/m/Y',$trans_date);
							
							if($mk->toaccount == $accountnumber){
								$debet_val = number_format($mk->value, 3);
								$kredit_val = '-';
								
								if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
									$running_balance = $running_balance + $mk->value;
								}else{
									$running_balance = $running_balance - $mk->value;
								}
								
								$total_debet = $total_debet + $mk->value;
							}else{
								$debet_val = '-';
								$kredit_val = number_format($mk->value, 3);
								
								if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
									$running_balance = $running_balance - $mk->value;
								}else{
									$running_balance = $running_balance + $mk->value;
								}
								
								$total_kredit = $total_kredit + $mk->value;
							}
							
							if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
								$data['view'] .= '<tr><td class="right-aligned">'.$count_data.'. </td><td>'.$trans_date.'</td><td>'.$mk->description.'</td><td>'.$mk->idmutasi.'</td><td class="right-aligned">'.$debet_val.'</td><td class="right-aligned">'.$kredit_val.'</td><td class="right-aligned">'.number_format($running_balance, 3).'</td></tr>';
							}else{
								$data['view'] .= '<tr><td class="right-aligned">'.$count_data.'. </td><td>'.$trans_date.'</td><td>'.$mk->description.'</td><td>'.$mk->idmutasi.'</td><td class="right-aligned">'.$debet_val.'</td><td class="right-aligned">'.$kredit_val.'</td><td class="right-aligned">'.number_format($running_balance, 3).'</td></tr>';
							}
							
							$count_data = $count_data + 1;
						}
					}
					
					$data['view'] .= '<tr><td colspan="4"></td><td class="right-aligned" style="border-top: 1px dotted #000">'.number_format($total_debet, 3).'</td><td class="right-aligned" style="border-top: 1px dotted #000">'.number_format($total_kredit, 3).'</td><td></td></tr>';
					$data['view'] .= '<tr><td colspan="7" style="visibility:hidden; color:#FFF">-</td></tr>';
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
		
        $pdf->Output("Laporan Titipan Pelanggan Emas.pdf", "I");
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

