<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_lap_kasir extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		//$this->load->library('M_pdf');
		$this->load->model('M_product','mp');
		$this->load->model('M_pos','mt');
		$this->load->model('M_mutasi','mm');
		$this->load->model('M_karat','mk');
		$this->load->model('M_box','mb');
	}
	
	public function index(){
		$data['acc_ke'] = $this->mm->get_default_account('KE');
		$data['bayar'] = $this->mt->get_bayar_nt();
		$data['karat'] = $this->mk->get_karat_srt();
		$data['box'] = $this->mb->get_box_aktif();
		$this->load->view('pos/V_laporan_kasir',$data);
	}
	
	public function filter_kas(){
		date_default_timezone_set("Asia/Jakarta");
		
		$tgl_transaksi =  $this->input->post('tgl_transaksi');
		$tanggal_transaksi =  $this->input->post('tgl_transaksi');
		$jenis_bayar =  $this->input->post('jenis_bayar');
		$detail_rekap =  $this->input->post('detail_rekap');
		
		$tglTrans = $this->date_to_format($tgl_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		$site_name = $this->mm->get_site_name();
		
		if($jenis_bayar == ''){
			$jenis_bayar_link = 'All';
		}else{
			$jenis_bayar_link = $jenis_bayar;
		}
		
		if($detail_rekap == 'D'){
			if($jenis_bayar == ''){
				$kas_account = $this->mm->get_all_kasbank_pos();
			}else{
				$kas_account = $this->mm->get_single_coa_rp($jenis_bayar);
			}
			
			$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/C_lap_kasir/lap_kasir_to_pdf/'.$tanggal_transaksi.'/'.$jenis_bayar_link.'/'.$detail_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Transaksi Harian Detail</span><br><span>Tanggal '.$tanggal_transaksi.', Cabang '.$site_name.'</span></div><div class="sixteen wide centered column">';
			
			$data['view'] .= '<table id="report_kas" class="ui celled table" cellspacing="0" width="100%"><thead><tr style="text-align:center"><th>Description</th><th>Voucher Code</th><th>Debit</th><th>Credit</th><th>Balance</th></tr></thead><tbody>';
			
			foreach($kas_account as $ka){
				$acc_number = $ka->accountnumber;
				$accountnumber = $ka->accountnumber;
				
				/*-- Menentukan Beginning Balance --*/
				$saldo_awal = $this->mm->get_report_rp_beginning_balance($acc_number);
				/*----------------------------------*/
				
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_awal_d = $this->mm->get_report_mutasi_rp_yesterday_d($acc_number,$tgl_transaksi);
				$saldo_awal_k = $this->mm->get_report_mutasi_rp_yesterday_k($acc_number,$tgl_transaksi);
				
				foreach($saldo_awal_d as $mtyd){
					$saldo_awal = $saldo_awal + $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$saldo_awal = $saldo_awal - $mtyk->total_mutasi;
				}
				
				/*---------------------------------------------------*/
				
				/*---------- Mengambil Data Mutasi Transaksi ----------*/
		
				$mutasi_transaksi = $this->mm->get_report_mutasi_rp_lap($tgl_transaksi, $tgl_transaksi, $acc_number);
				
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
					$coa_data = $this->mm->get_coa_number_name($ka->accountnumberint);
					
					$running_balance = $saldo_awal;
					$total_debet = 0;
					$total_kredit = 0;
					
					$sa = number_format($saldo_awal, 0);
					if($sa == 0 || $sa == '' || $sa == -0){
						$data['view'] .= '<tr><td colspan="4" style="font-weight:600">'.$coa_data.'</td><td class="right aligned">-</td></tr>';
					}else{
						$data['view'] .= '<tr><td colspan="4" style="font-weight:600">'.$coa_data.'</td><td class="right aligned">'.number_format($saldo_awal, 2).'</td></tr>';
					}
										
					foreach($mutasi_transaksi as $mk){
						if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
							$trans_date = strtotime($mk->transdate);
							$trans_date = date('d/m/Y',$trans_date);
							
							$tipe_trans = substr($mk->idmutasi,0,2);
							if($tipe_trans == 'BR' || $tipe_trans == 'JR' || $tipe_trans == 'JP'){
								$idmutasi = substr($mk->idmutasi,0,-2);
							}else{
								$idmutasi = $mk->idmutasi;
							}
							
							if($mk->toaccount == $accountnumber){
								$debet_val = number_format($mk->value, 2);
								$kredit_val = '-';
								
								$running_balance = $running_balance + $mk->value;
								
								$rb = number_format($running_balance, 0);
								if($rb == -0){
									$running_balance = 0;
								}
								
								$total_debet = $total_debet + $mk->value;
							}else{
								$debet_val = '-';
								$kredit_val = number_format($mk->value, 2);
								
								$running_balance = $running_balance - $mk->value;
								
								$rb = number_format($running_balance, 0);
								if($rb == -0){
									$running_balance = 0;
								}
								
								$total_kredit = $total_kredit + $mk->value;
							}
							
							$data['view'] .= '<tr><td>'.$mk->description.'</td><td>'.$idmutasi.'</td><td class="right aligned">'.$debet_val.'</td><td class="right aligned">'.$kredit_val.'</td><td class="right aligned">'.number_format($running_balance, 2).'</td></tr>';
						}
					}
					
					$data['view'] .= '<tr><td colspan="2"></td><td class="right aligned" style="border-top: 1px solid #000">'.number_format($total_debet, 2).'</td><td class="right aligned" style="border-top: 1px solid #000">'.number_format($total_kredit, 2).'</td><td></td></tr>';
					$data['view'] .= '<tr><td colspan="7" style="visibility:hidden; color:#FFF">-</td></tr>';
				}
				
				/*----------------------------------------------------*/
			}
		}else if($detail_rekap == 'R'){
			if($jenis_bayar == ''){
				$kas_account = $this->mm->get_all_kasbank_pos();
			}else{
				$kas_account = $this->mm->get_single_coa_rp($jenis_bayar);
			}
			
			$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/C_lap_kasir/lap_kasir_to_pdf/'.$tanggal_transaksi.'/'.$jenis_bayar_link.'/'.$detail_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Transaksi Harian Rekap</span><br><span>Tanggal '.$tanggal_transaksi.', Cabang '.$site_name.'</span><br></div><div class="sixteen wide centered column">';
			
			$data['view'] .= '<table class="ui celled table" id="report_kas" class="table-report-detail" cellspacing="0" width="100%"><thead><tr style="text-align:center"><th>Description</th><th>Debit</th><th>Credit</th><th>Balance</th></tr></thead><tbody>';
			
			foreach($kas_account as $ka){
				$acc_number = $ka->accountnumber;
				$accountnumber = $ka->accountnumber;
				
				/*-- Menentukan Beginning Balance --*/
				$saldo_awal = $this->mm->get_report_rp_beginning_balance($acc_number);
				/*----------------------------------*/
				
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_awal_d = $this->mm->get_report_mutasi_rp_yesterday_d($acc_number,$tgl_transaksi);
				$saldo_awal_k = $this->mm->get_report_mutasi_rp_yesterday_k($acc_number,$tgl_transaksi);
				
				foreach($saldo_awal_d as $mtyd){
					$saldo_awal = $saldo_awal + $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$saldo_awal = $saldo_awal - $mtyk->total_mutasi;
				}
				
				/*---------------------------------------------------*/
				
				/*---------- Mengambil Data Mutasi Transaksi ----------*/
		
				$mutasi_transaksi_jual = $this->mm->get_report_mutasi_rp_jual($tgl_transaksi, $tgl_transaksi, $acc_number);
				$mutasi_transaksi = $this->mm->get_report_mutasi_rp_rekap_lap($tgl_transaksi, $tgl_transaksi, $acc_number);
				$mutasi_transaksi_beli = $this->mm->get_report_mutasi_rp_bycode('BR', $tgl_transaksi, $tgl_transaksi, $acc_number);
				
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
					
					$sa = number_format($saldo_awal, 0);
					if($sa == 0 || $sa == '' || $sa == -0){
						$data['view'] .= '<tr><td colspan="3" style="font-weight:600">'.$coa_data.'</td><td class="right aligned">-</td></tr>';
					}else{
						$data['view'] .= '<tr><td colspan="3" style="font-weight:600">'.$coa_data.'</td><td class="right aligned">'.number_format($saldo_awal, 2).'</td></tr>';
					}
					
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
					
					if($total_debet_ind != 0 || $total_kredit_ind != 0){
						$data['view'] .= '<tr><td>PENJUALAN</td><td class="right aligned">'.number_format($total_debet_ind, 2).'</td><td class="right aligned">'.number_format($total_kredit_ind, 2).'</td><td class="right aligned">'.number_format($running_balance, 2).'</td><tr>';
					}
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
					
					if($total_debet_ind != 0 || $total_kredit_ind != 0){
						$data['view'] .= '<tr><td>PENERIMAAN / PENGELUARAN KAS </td><td class="right aligned">'.number_format($total_debet_ind, 2).'</td><td class="right aligned">'.number_format($total_kredit_ind, 2).'</td><td class="right aligned">'.number_format($running_balance, 2).'</td></tr>';
					}
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
					
					if($total_debet_ind != 0 || $total_kredit_ind != 0){
						$data['view'] .= '<tr><td>PEMBELIAN</td><td class="right aligned">'.number_format($total_debet_ind, 2).'</td><td class="right aligned">'.number_format($total_kredit_ind, 2).'</td><td class="right aligned">'.number_format($running_balance, 2).'</td></tr>';
					}
					//---
					
					$data['view'] .= '<tr><td></td><td class="right aligned" style="border-top: 1px solid #000">'.number_format($total_debet, 2).'</td><td class="right aligned" style="border-top: 1px solid #000">'.number_format($total_kredit, 2).'</td><td></td></tr>';
					$data['view'] .= '<tr><td colspan="7" style="visibility:hidden; color:#FFF">-</td></tr>';
				}
				
				/*----------------------------------------------------*/
			}
		}
		
		$data['view'] .= '</tbody></table></div></div>';
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function filter_jual(){
		date_default_timezone_set("Asia/Jakarta");
		
		$tgl_from =  $this->input->post('from_filter_jual');
		$tanggal_from =  $this->input->post('from_filter_jual');
		$tglFrom = $this->date_to_format($tgl_from);
		$tgl_from = date("Y-m-d",$tglFrom).' 00:00:00';
		
		$tgl_to =  $this->input->post('to_filter_jual');
		$tanggal_to =  $this->input->post('to_filter_jual');
		$tglTo = $this->date_to_format($tgl_to);
		$tgl_to = date("Y-m-d",$tglTo).' 00:00:00';
		
		$detail_rekap = $this->input->post('detail_rekap_jual');
		
		$filter_box = $this->input->post('filter_box_jual');
		$box_link = $filter_box;
		if($filter_box == 'All'){
			$filter_box = '';
			$data_box = $this->mb->get_box_aktif();
			for($i=0; $i<count($data_box); $i++){
				if($i == 0){
					$filter_box .= '"'.$data_box[$i]->id.'"';
				}else{
					$filter_box .= ',"'.$data_box[$i]->id.'"';
				}
			}
		}else{
			$filter_box = '"'.$filter_box.'"';
		}
		
		$filter_karat = $this->input->post('filter_karat_jual');
		$karat_link = $filter_karat;
		if($filter_karat == 'All'){
			$filter_karat = '';
			$data_karat = $this->mk->get_karat_srt();
			for($i=0; $i<count($data_karat); $i++){
				if($i == 0){
					$filter_karat .= '"'.$data_karat[$i]->id.'"';
				}else{
					$filter_karat .= ',"'.$data_karat[$i]->id.'"';
				}
			}
		}else{
			$filter_karat = '"'.$filter_karat.'"';
		}
		
		$filter_rekap = $this->input->post('filter_rekap_jual');
		$site_name = $this->mm->get_site_name();
		
		if($detail_rekap == 'D'){
			$array_jual = array();
			$dj = $this->mt->get_penjualan_kasir($tgl_from,$tgl_to,$filter_box,$filter_karat);
			
			$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/C_lap_kasir/lap_jual_to_pdf/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$box_link.'/'.$karat_link.'/'.$filter_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Penjualan Harian Detail</span><br><span>Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name.'</span><br></div><div class="sixteen wide centered column">';
			
			$data['view'] .= '<table id="report_jual" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:40px">No</th><th>ID Product</th><th>Box</th><th>Keterangan</th><th>Karat</th><th>Berat</th><th>Harga Jual</th><th>Total Jual</th></tr></thead><tbody>';
			
			$length = count($dj);
			$trans_temp = '';
			$total_temp = 0;
			$number = 1;
			$total_pcs_all = 0;
			$total_gram_all = 0;
			$total_jual_all = 0;
			
			for($i = 0;$i < $length; $i++){
				$id_trans = $dj[$i]->transaction_code;
				$act = '';
				if($this->session->userdata('gold_admin') == 'Y'){
					if(substr($id_trans,0,2) == 'JR'){
						$act = '<button type="button" class="ui mini icon negative button" onclick=deleteTransJual("'.$id_trans.'") title="Hapus"><i class="ban icon"></i></button>';
					}
				}
				
				if($i == 0){
					$trans_date = strtotime($dj[$i]->trans_date);
					$trans_date = date('d-M-Y',$trans_date);
					$trans_temp = $id_trans;
					$cust_service = $this->mt->get_cs_jual_by_id($id_trans);
					$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td class="td-bold" colspan="4">'.$act.' '.$trans_date.' | '.$id_trans.'</td><td colspan="2" style="border-left:none;border-right:none;">Customer Service : </td><td style="text-transform:uppercase; border-left:none">'.$cust_service.'</td></tr>';
					
					$number = $number + 1;
					
					$box_number = '';
					$totalnumberlength = 3;
					$numberlength = strlen($dj[$i]->id_box);
					$numberspace = $totalnumberlength - $numberlength;
					if($numberspace != 0){
						for ($a = 1; $a <= $numberspace; $a++){
							$box_number .= '0';
						}
					}
					
					$box_number .= $dj[$i]->id_box;
					
					$data['view'] .= '<tr><td></td><td>'.$dj[$i]->id_product.'</td><td class="center aligned">'.$box_number.'</td><td>'.$dj[$i]->product_desc.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right aligned">'.number_format($dj[$i]->product_price/$dj[$i]->product_weight, 2).'</td><td class="right aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
					
					$total_pcs_all = $total_pcs_all + 1;
					$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
					$total_jual_all = $total_jual_all + $dj[$i]->product_price;
					
					$total_temp = $total_temp + $dj[$i]->product_price;
				}else{
					if($dj[$i]->transaction_code == $trans_temp){
						$box_number = '';
						$totalnumberlength = 3;
						$numberlength = strlen($dj[$i]->id_box);
						$numberspace = $totalnumberlength - $numberlength;
						if($numberspace != 0){
							for ($a = 1; $a <= $numberspace; $a++){
								$box_number .= '0';
							}
						}
						
						$box_number .= $dj[$i]->id_box;
						
						$data['view'] .= '<tr><td></td><td>'.$dj[$i]->id_product.'</td><td class="center aligned">'.$box_number.'</td><td>'.$dj[$i]->product_desc.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right aligned">'.number_format($dj[$i]->product_price/$dj[$i]->product_weight, 2).'</td><td class="right aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
						
						$total_temp = $total_temp + $dj[$i]->product_price;
						
						$total_pcs_all = $total_pcs_all + 1;
						$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
						$total_jual_all = $total_jual_all + $dj[$i]->product_price;
					}else{
						$data['view'] .= '<tr><td colspan="5"></td><td class="dash-top"></td><td class="dash-top"></td><td class="dash-top right aligned">'.number_format($total_temp, 2).'</td></tr>';
						
						$total_temp = 0;
						
						$trans_date = strtotime($dj[$i]->trans_date);
						$trans_date = date('d-M-Y',$trans_date);
						$trans_temp = $id_trans;
						$cust_service = $this->mt->get_cs_jual_by_id($id_trans);
						$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td class="td-bold" colspan="4">'.$act.' '.$trans_date.' | '.$id_trans.'</td><td colspan="2" style="border-left:none;border-right:none;">Customer Service : </td><td style="text-transform:uppercase; border-left:none">'.$cust_service.'</td></tr>';
						//$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td class="td-bold" colspan="7">'.$act.' '.$trans_date.' | '.$id_trans.'</td></tr>';
						
						$number = $number + 1;
						
						$box_number = '';
						$totalnumberlength = 3;
						$numberlength = strlen($dj[$i]->id_box);
						$numberspace = $totalnumberlength - $numberlength;
						if($numberspace != 0){
							for ($a = 1; $a <= $numberspace; $a++){
								$box_number .= '0';
							}
						}
						
						$box_number .= $dj[$i]->id_box;
						
						$data['view'] .= '<tr><td></td><td>'.$dj[$i]->id_product.'</td><td class="center aligned">'.$box_number.'</td><td>'.$dj[$i]->product_desc.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right aligned">'.number_format($dj[$i]->product_price/$dj[$i]->product_weight, 2).'</td><td class="right aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
						
						$total_temp = $total_temp + $dj[$i]->product_price;
						
						$total_pcs_all = $total_pcs_all + 1;
						$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
						$total_jual_all = $total_jual_all + $dj[$i]->product_price;
					}
				}
			}
			
			if($length != 0){
				$data['view'] .= '<tr><td colspan="5"></td><td class="dash-top"></td><td class="dash-top"></td><td class="dash-top right aligned">'.number_format($total_temp, 2).'</td></tr>';
				
				
				$data['view'] .= '<tr><td colspan="4"></td><td class="double-top right aligned">'.$total_pcs_all.' Pcs</td><td class="double-top right aligned">'.number_format($total_gram_all, 3).'</td><td class="double-top"></td><td class="double-top right aligned">'.number_format($total_jual_all, 2).'</td></tr>';
			}
		}else if($detail_rekap == 'R'){
			if($filter_rekap == 'K'){
				$number = 1;
				$total_pcs = 0;
				$total_gram = 0;
				$total_jual = 0;
				
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/C_lap_kasir/lap_jual_to_pdf/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$box_link.'/'.$karat_link.'/'.$filter_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Penjualan Harian Rekap / Karat</span><br><span>Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name.'</span><br></div><div class="sixteen wide centered column">';
				
				$data['view'] .= '<table id="report_jual" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:40px">No</th><th>Karat</th><th style="width:40px">Pcs</th><th style="width:80px">Gram</th><th style="width:120px">Rata2</th><th style="width:140px">Total Jual</th></tr></thead><tbody>';
			
				$data_rekap = $this->mt->get_penjualan_rekap_karat($tgl_from,$tgl_to);
				foreach($data_rekap as $dr){
					$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.$dr->karat_name.'</td><td class="right aligned">'.$dr->pcs.'</td><td class="right aligned">'.number_format($dr->berat, 2).'</td><td class="right aligned">'.number_format($dr->harga/$dr->berat, 2).'</td><td class="right aligned">'.number_format($dr->harga, 2).'</td><tr>';
					
					$total_pcs = $total_pcs + $dr->pcs;
					$total_gram = $total_gram + $dr->berat;
					$total_jual = $total_jual + $dr->harga;
					$number = $number + 1;
				}
				
				if($total_pcs != 0){
					$data['view'] .= '<tr><td colspan="2"></td><td class="double-top right aligned">'.$total_pcs.'</td><td class="double-top right aligned">'.number_format($total_gram, 2).'</td><td class="double-top right aligned"></td><td class="double-top right aligned">'.number_format($total_jual, 2).'</td><tr>';
				}
			}else if($filter_rekap == 'All'){
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/C_lap_kasir/lap_jual_to_pdf/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$box_link.'/'.$karat_link.'/'.$filter_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Penjualan Harian Rekap</span><br><span>Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name.'</span><br></div><div class="sixteen wide centered column">';
				
				$data['view'] .= '<div class="offset-lg-2 col-lg-8 offset-md-2 col-md-8">';
				
				$data['view'] .= '<table id="report_jual" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:40px">No</th><th>Tanggal</th><th>ID Transaksi</th><th>Nama Customer</th><th>Alamat Customer</th><th>Telepon Customer</th><th>Jumlah</th></tr></thead><tbody>';
				
				$kas_account = $this->mm->get_all_kasbank();
				$number = 1;
				$running_balance = 0;
				foreach($kas_account as $ka){
					$acc_number = $ka->accountnumber;
					$accountnumber = $ka->accountnumber;
					
					/*---------- Mengambil Data Mutasi Transaksi ----------*/
			
					$mutasi_transaksi_jual = $this->mm->get_report_mutasi_rp_jual($tgl_from, $tgl_to, $acc_number);
					
					/*---------------------------------------------------*/
					
					/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
					
					$flag_kurs = FALSE;
					$flag_mutasi = FALSE;
					
					foreach($mutasi_transaksi_jual as $mk){
						if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
							$flag_mutasi = TRUE;
							$flag_kurs = TRUE;
						}
					}
					
					/*-------- Menampilkan Data Dalam Tabel Report -------*/
					
					if($flag_kurs == TRUE){
						$coa_data = $this->mm->get_coa_number_name($ka->accountnumberint);
						
						$total_debet = 0;
						
						$data['view'] .= '<tr><td colspan="7">'.$coa_data.'</td></tr>';
						
						//PENJUALAN
						
						foreach($mutasi_transaksi_jual as $mk){
							if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
								if($mk->toaccount == $accountnumber){
									$trans_date = strtotime($mk->transdate);
									$trans_date = date('d/m/Y',$trans_date);
									
									$idmutasi = substr($mk->idmutasi,0,20);
									$customer_name = $this->mt->get_customer_name($idmutasi);
									$customer_address = $this->mt->get_customer_address($idmutasi);
									$customer_phone = $this->mt->get_customer_phone($idmutasi);
									
									$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.$trans_date.'</td><td>'.$idmutasi.'</td><td>'.$customer_name.'</td><td>'.$customer_address.'</td><td>'.$customer_phone.'</td><td class="right aligned">'.number_format($mk->value, 2).'</td><tr>';
									$total_debet = $total_debet + $mk->value;
									$running_balance = $running_balance + $mk->value;
									$number = $number + 1;
								}
							}
						}
						
						if($total_debet != 0){
							$data['view'] .= '<tr><td colspan="6"></td><td class="dash-top right aligned">'.number_format($total_debet, 2).'</td><tr>';
						}
						//---
					}
					
					/*----------------------------------------------------*/
				}
				
				if($running_balance != 0){
					$data['view'] .= '<tr><td colspan="6"></td><td class="right aligned"><span style="visibility:hidden">-</span></td><tr>';
					$data['view'] .= '<tr><td colspan="6"></td><td class="double-top right aligned">'.number_format($running_balance, 2).'</td><tr>';
				}
			}else if($filter_rekap == 'G'){
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/C_lap_kasir/lap_jual_to_pdf/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$box_link.'/'.$karat_link.'/'.$filter_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Penjualan Harian Rekap / Kelompok</span><br><span>Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name.'</span></div><div class="sixteen wide centered column">';
				
				$data['view'] .= '<table id="report_jual" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:40px">No</th><th>Karat</th><th style="width:40px">Pcs</th><th style="width:80px">Gram</th><th style="width:120px">Rata2</th><th style="width:140px">Total Beli</th></tr></thead><tbody>';
				
				$data_karat = $this->mk->get_karat_srt();
				
				$number = 1;
				$total_pcs_all = 0;
				$total_gram_all = 0;
				$total_jual_all = 0;
				
				foreach($data_karat as $dk){
					$id_karat = $dk->id;
					
					$total_pcs = 0;
					$total_gram = 0;
					$total_jual = 0;
					
					$data_rekap = $this->mt->get_penjualan_rekap_by_category($tgl_from,$tgl_to,$id_karat);
					if(count($data_rekap) != 0){
						$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td class="td-bold">'.$dk->karat_name.'</td><td colspan="4"></td><tr>';
						
						foreach($data_rekap as $dr){
							$data['view'] .= '<tr><td></td><td class="td-bold">'.$dr->category_name.'</td><td class="right aligned">'.$dr->pcs.'</td><td class="right aligned">'.number_format($dr->berat, 2).'</td><td class="right aligned">'.number_format($dr->harga/$dr->berat, 2).'</td><td class="right aligned">'.number_format($dr->harga, 2).'</td><tr>';
							
							$total_pcs = $total_pcs + $dr->pcs;
							$total_gram = $total_gram + $dr->berat;
							$total_jual = $total_jual + $dr->harga;
							
							$total_pcs_all = $total_pcs_all + $dr->pcs;
							$total_gram_all = $total_gram_all + $dr->berat;
							$total_jual_all = $total_jual_all + $dr->harga;
						}
						
						$number = $number + 1;
					}
					
					
					if($total_pcs != 0){
						$data['view'] .= '<tr><td colspan="2"></td><td class="dash-top right aligned">'.$total_pcs.'</td><td class="dash-top right aligned">'.number_format($total_gram, 2).'</td><td class="dash-top right aligned"></td><td class="dash-top right aligned">'.number_format($total_jual, 2).'</td><tr>';
					}
				}
				
				if($total_pcs_all != 0){
					$data['view'] .= '<tr><td colspan="2"></td><td class="double-top right aligned">'.$total_pcs_all.'</td><td class="double-top right aligned">'.number_format($total_gram_all, 2).'</td><td class="double-top right aligned"></td><td class="double-top right aligned">'.number_format($total_jual_all, 2).'</td><tr>';
				}
			}
		}
		
		$data['view'] .= '</tbody></table></div>';
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function hapus_jual($id){
		if($this->session->userdata('gold_admin') == 'Y'){
			date_default_timezone_set("Asia/Jakarta");
			$this->db->trans_start();
			
			$this->mt->hapus_main_detail_jual($id);
			$detail_jual = $this->mt->get_product_jual($id);
			
			foreach($detail_jual as $d){
				$id_product = $d->id_product;
				$this->mt->reset_product_jual($id_product);
			}
			
			$idmutasi = $id.'-';
			
			$mutasi_data = $this->mm->get_mutasi_rp_like_id($idmutasi);
			$deletedby = $this->session->userdata('gold_nama_user');
			
			foreach($mutasi_data as $md){
				$siteid = $md->idsite;
				$idmutasi = $md->idmutasi;
				$tipemutasi = $md->tipemutasi;
				$fromaccount = $md->fromaccount;
				$toaccount = $md->toaccount;
				$value = $md->value;
				$description = $md->description;
				$transdate = $md->transdate;
				$createddate = $md->createddate;
				$createdby = $md->createdby;
				
				$this->mm->insert_mutasi_rupiah_deleted($siteid,$idmutasi,$tipemutasi,$fromaccount,$toaccount,$value,$description,$transdate,$createddate,$createdby,$deletedby);
				$this->mm->delete_mutasi_rupiah_like($id);
			}

			$mutasi_data = $this->mm->get_mutasi_gr_like_id($idmutasi);
			
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
				$this->mm->delete_mutasi_gram_like($id);
			}
			
			$this->db->trans_complete();
			
			$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Berhasil Hapus Data!</div></div>';
			$data['success'] = true;
			echo json_encode($data);
		}
	}
	
	public function filter_beli(){
		date_default_timezone_set("Asia/Jakarta");
		
		$tgl_from =  $this->input->post('from_filter_beli');
		$tanggal_from =  $this->input->post('from_filter_beli');
		$tglFrom = $this->date_to_format($tgl_from);
		$tgl_from = date("Y-m-d",$tglFrom).' 00:00:00';
		
		$tgl_to =  $this->input->post('to_filter_beli');
		$tanggal_to =  $this->input->post('to_filter_beli');
		$tglTo = $this->date_to_format($tgl_to);
		$tgl_to = date("Y-m-d",$tglTo).' 00:00:00';
		
		$detail_rekap = $this->input->post('detail_rekap_beli');
		
		$filter_karat = $this->input->post('filter_karat_beli');
		if($filter_karat == 'All'){
			$filter_karat = '';
			$data_karat = $this->mk->get_karat_srt();
			for($i=0; $i<count($data_karat); $i++){
				if($i == 0){
					$filter_karat .= '"'.$data_karat[$i]->id.'"';
				}else{
					$filter_karat .= ',"'.$data_karat[$i]->id.'"';
				}
			}
			
			$karat_link = 'All';
		}else{
			$karat_link = $filter_karat;
			$filter_karat = '"'.$filter_karat.'"';
		}
		
		$filter_rekap = $this->input->post('filter_rekap_beli');
		$site_name = $this->mm->get_site_name();
		
		if($detail_rekap == 'D'){
			$array_jual = array();
			$array_do = array();
			$array_pr = array();
			
			$do = $this->mt->get_do_range($tgl_from,$tgl_to);
			$pr = $this->mt->get_pr_range();
			
			foreach($do as $a){
				$array_do[$a->do_date] = $a->harga_emas;
			}
			
			foreach($pr as $p){
				if($p->id_karat == 1){
					$array_pr[$p->id_karat] = 100.25;
				}else{
					$array_pr[$p->id_karat] = $p->kadar_beli_std;
				}
			}
			
			$dj = $this->mt->get_pembelian_kasir($tgl_from,$tgl_to,$filter_karat);
			
			$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/C_lap_kasir/lap_beli_to_pdf/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$karat_link.'/'.$filter_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Pembelian Harian Detail</span><br><span>Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name.'</span></div><div class="sixteen wide centered column">';
			
			$data['view'] .= '<table id="report_beli" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:40px">No</th><th>Kelompok</th><th>Keterangan</th><th>Karat</th><th>Pcs</th><th>Berat</th><th>Harga Beli</th><th>Total Beli</th></tr></thead><tbody>';
			
			$length = count($dj);
			$trans_temp = '';
			$total_pcs_temp = 0;
			$total_gram_temp = 0;
			$total_temp = 0;
			$number = 1;
			$total_pcs_all = 0;
			$total_gram_all = 0;
			$total_jual_all = 0;
			
			for($i = 0;$i < $length; $i++){
				$warna = '';
				$id_trans = $dj[$i]->transaction_code;
				$act = '';
				if($this->session->userdata('gold_admin') == 'Y'){
					$act = '<button type="button" class="ui mini icon negative button" onclick=deleteTransBeli("'.$id_trans.'") title="Hapus"><i class="ban icon"></i></button>';
				}
				
				if($i == 0){
					$trans_date = strtotime($dj[$i]->trans_date);
					$trans_date = date('d-M-Y',$trans_date);
					$trans_temp = $id_trans;
					
					$cust_service = $this->mt->get_cs_beli_by_id($id_trans);
					
					$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td colspan="4">'.$act.' '.$trans_date.' | '.$id_trans.'</td><td colspan="2" style="border-left:none;border-right:none">Customer Service : </td><td style="text-transform:uppercase;border-left:none">'.$cust_service.'</td></tr>';
					
					$number = $number + 1;
					
					$harga_emas = $array_do[$dj[$i]->trans_date];
					$id_karat = $dj[$i]->id_karat;
					
					if($id_karat == 1){
						$kali = $array_pr[$id_karat] / 100;
						$batas = $harga_emas * $kali;
					}else{
						$kali = ($array_pr[$id_karat] + 5) / 100;
						$batas = $harga_emas * $kali;
					}
					
					$ratarata = $dj[$i]->product_price/$dj[$i]->product_weight;
					if($ratarata > $batas){
						$warna = 'style="font-weight:bold;"';
					}
					
					$data['view'] .= '<tr><td></td><td>'.$dj[$i]->category_name.'</td><td>'.$dj[$i]->nama_product.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right aligned">'.$dj[$i]->product_pcs.'</td><td class="right aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right aligned" '.$warna.'>'.number_format($ratarata, 2).'</td><td class="right aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
					
					$total_pcs_all = $total_pcs_all + $dj[$i]->product_pcs;
					$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
					$total_jual_all = $total_jual_all + $dj[$i]->product_price;
					
					$total_pcs_temp = $total_pcs_temp + $dj[$i]->product_pcs;
					$total_gram_temp = $total_gram_temp + $dj[$i]->product_weight;
					$total_temp = $total_temp + $dj[$i]->product_price;
				}else{
					if($dj[$i]->transaction_code == $trans_temp){
						
						$harga_emas = $array_do[$dj[$i]->trans_date];
						$id_karat = $dj[$i]->id_karat;
						
						if($id_karat == 1){
							$kali = $array_pr[$id_karat] / 100;
							$batas = $harga_emas * $kali;
						}else{
							$kali = ($array_pr[$id_karat] + 5) / 100;
							$batas = $harga_emas * $kali;
						}
						
						$ratarata = $dj[$i]->product_price/$dj[$i]->product_weight;
						if($ratarata > $batas){
							$warna = 'style="font-weight:bold;"';
						}
						
						$data['view'] .= '<tr><td></td><td>'.$dj[$i]->category_name.'</td><td>'.$dj[$i]->nama_product.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right aligned">'.$dj[$i]->product_pcs.'</td><td class="right aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right aligned" '.$warna.'>'.number_format($ratarata, 2).'</td><td class="right aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
						
						$total_pcs_all = $total_pcs_all + $dj[$i]->product_pcs;
						$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
						$total_jual_all = $total_jual_all + $dj[$i]->product_price;
						
						$total_pcs_temp = $total_pcs_temp + $dj[$i]->product_pcs;
						$total_gram_temp = $total_gram_temp + $dj[$i]->product_weight;
						$total_temp = $total_temp + $dj[$i]->product_price;
					}else{
						$data['view'] .= '<tr><td colspan="4"></td><td class="dash-top right aligned">'.$total_pcs_temp.'</td><td class="dash-top right aligned">'.number_format($total_gram_temp, 3).'</td><td class="dash-top"></td><td class="dash-top right aligned">'.number_format($total_temp, 2).'</td></tr>';
						
						$total_temp = 0;
						$total_pcs_temp = 0;
						$total_gram_temp = 0;
						
						$trans_date = strtotime($dj[$i]->trans_date);
						$trans_date = date('d-M-Y',$trans_date);
						$trans_temp = $id_trans;
						
						$harga_emas = $array_do[$dj[$i]->trans_date];
						$id_karat = $dj[$i]->id_karat;
						
						if($id_karat == 1){
							$kali = $array_pr[$id_karat] / 100;
							$batas = $harga_emas * $kali;
						}else{
							$kali = ($array_pr[$id_karat] + 5) / 100;
							$batas = $harga_emas * $kali;
						}
						
						$ratarata = $dj[$i]->product_price/$dj[$i]->product_weight;
						if($ratarata > $batas){
							$warna = 'style="font-weight:bold;"';
						}
						
						$cust_service = $this->mt->get_cs_beli_by_id($id_trans);
					
						$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td colspan="4">'.$act.' '.$trans_date.' | '.$id_trans.'</td><td colspan="2" style="border-left:none;border-right:none">Customer Service : </td><td style="text-transform:uppercase;border-left:none">'.$cust_service.'</td></tr>';
						
						$number = $number + 1;
						
						$data['view'] .= '<tr><td></td><td>'.$dj[$i]->category_name.'</td><td>'.$dj[$i]->nama_product.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right aligned">'.$dj[$i]->product_pcs.'</td><td class="right aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right aligned" '.$warna.'>'.number_format($ratarata, 2).'</td><td class="right aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
						
						$total_pcs_all = $total_pcs_all + $dj[$i]->product_pcs;
						$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
						$total_jual_all = $total_jual_all + $dj[$i]->product_price;
						
						$total_pcs_temp = $total_pcs_temp + $dj[$i]->product_pcs;
						$total_gram_temp = $total_gram_temp + $dj[$i]->product_weight;
						$total_temp = $total_temp + $dj[$i]->product_price;
					}
				}
			}
			
			if($length != 0){
				$data['view'] .= '<tr><td colspan="4"></td><td class="dash-top right aligned">'.$total_pcs_temp.'</td><td class="dash-top right aligned">'.number_format($total_gram_temp, 3).'</td><td class="dash-top"></td><td class="dash-top right aligned">'.number_format($total_temp, 2).'</td></tr>';
				
				$data['view'] .= '<tr><td colspan="4"></td><td class="double-top right aligned">'.$total_pcs_all.'</td><td class="double-top right aligned">'.number_format($total_gram_all, 3).'</td><td class="double-top"></td><td class="double-top right aligned">'.number_format($total_jual_all, 2).'</td></tr>';
			}
		}else if($detail_rekap == 'R'){
			if($filter_rekap == 'K'){
				$number = 1;
				$total_pcs = 0;
				$total_gram = 0;
				$total_jual = 0;
				
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/C_lap_kasir/lap_beli_to_pdf/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$karat_link.'/'.$filter_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Pembelian Harian Rekap / Karat</span><br><span>Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name.'</span></div><div class="sixteen wide centered column">';
				
				$data['view'] .= '<div class="offset-lg-2 col-lg-8 offset-md-2 col-md-8">';
				
				$data['view'] .= '<table id="report_beli" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:40px">No</th><th>Karat</th><th style="width:40px">Pcs</th><th style="width:80px">Gram</th><th style="width:120px">Rata2</th><th style="width:140px">Total Beli</th></tr></thead><tbody>';
			
				$data_rekap = $this->mt->get_pembelian_rekap_karat($tgl_from,$tgl_to);
				foreach($data_rekap as $dr){
					$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.$dr->karat_name.'</td><td class="right aligned">'.$dr->pcs.'</td><td class="right aligned">'.number_format($dr->berat, 2).'</td><td class="right aligned">'.number_format($dr->harga/$dr->berat, 2).'</td><td class="right aligned">'.number_format($dr->harga, 2).'</td><tr>';
					
					$total_pcs = $total_pcs + $dr->pcs;
					$total_gram = $total_gram + $dr->berat;
					$total_jual = $total_jual + $dr->harga;
					$number = $number + 1;
				}
				
				if($total_pcs != 0){
					$data['view'] .= '<tr><td colspan="2"></td><td class="double-top right aligned">'.$total_pcs.'</td><td class="double-top right aligned">'.number_format($total_gram, 2).'</td><td class="double-top right aligned"></td><td class="double-top right aligned">'.number_format($total_jual, 2).'</td><tr>';
				}
			}else if($filter_rekap == 'All'){
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/C_lap_kasir/lap_beli_to_pdf/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$karat_link.'/'.$filter_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Pembelian Harian Rekap</span><br><span>Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name.'</span></div><div class="sixteen wide centered column">';
				
				$data['view'] .= '<div class="offset-lg-2 col-lg-8 offset-md-2 col-md-8">';
				
				$data['view'] .= '<table id="report_beli" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:40px">No</th><th>Tanggal</th><th>ID Transaksi</th><th>Nama Customer</th><th>Alamat Customer</th><th>Telepon Customer</th><th>Jumlah</th></tr></thead><tbody>';
				
				$kas_account = $this->mm->get_all_kasbank();
				$number = 1;
				$running_balance = 0;
				foreach($kas_account as $ka){
					$acc_number = $ka->accountnumber;
					$accountnumber = $ka->accountnumber;
					
					/*---------- Mengambil Data Mutasi Transaksi ----------*/
			
					$mutasi_transaksi_beli = $this->mm->get_report_mutasi_rp_bycode('BR', $tgl_from, $tgl_to, $acc_number);
					
					/*---------------------------------------------------*/
					
					/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
					
					$flag_kurs = FALSE;
					$flag_mutasi = FALSE;
					
					foreach($mutasi_transaksi_beli as $mk){
						if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
							$flag_mutasi = TRUE;
							$flag_kurs = TRUE;
						}
					}
					
					/*-------- Menampilkan Data Dalam Tabel Report -------*/
					
					if($flag_kurs == TRUE){
						$coa_data = $this->mm->get_coa_number_name($ka->accountnumberint);
						
						$total_debet = 0;
						
						$data['view'] .= '<tr><td colspan="7">'.$coa_data.'</td></tr>';
						
						foreach($mutasi_transaksi_beli as $mk){
							if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
								if($mk->fromaccount == $accountnumber){
									$trans_date = strtotime($mk->transdate);
									$trans_date = date('d/m/Y',$trans_date);
									
									$idmutasi = substr($mk->idmutasi,0,20);
									$customer_name = $this->mt->get_customer_name2($idmutasi);
									$customer_address = $this->mt->get_customer_address2($idmutasi);
									$customer_phone = $this->mt->get_customer_phone2($idmutasi);
									
									$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.$trans_date.'</td><td>'.$idmutasi.'</td><td>'.$customer_name.'</td><td>'.$customer_address.'</td><td>'.$customer_phone.'</td><td class="right aligned">'.number_format($mk->value, 2).'</td><tr>';
									$total_debet = $total_debet + $mk->value;
									$running_balance = $running_balance + $mk->value;
									$number = $number + 1;
								}
							}
						}
						
						if($total_debet != 0){
							$data['view'] .= '<tr><td colspan="6"></td><td class="dash-top right aligned">'.number_format($total_debet, 2).'</td><tr>';
						}
						//---
					}
					
					/*----------------------------------------------------*/
				}
				
				if($running_balance != 0){
					$data['view'] .= '<tr><td colspan="6"></td><td class="right aligned"><span style="visibility:hidden">-</span></td><tr>';
					$data['view'] .= '<tr><td colspan="6"></td><td class="double-top right aligned">'.number_format($running_balance, 2).'</td><tr>';
				}
			}else if($filter_rekap == 'G'){
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/C_lap_kasir/lap_beli_to_pdf/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$karat_link.'/'.$filter_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Pembelian Harian Rekap / Karat</span><br><span>Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name.'</span><br><br></div><div class="sixteen wide centered column">';
				
				$data['view'] .= '<div class="offset-lg-2 col-lg-8 offset-md-2 col-md-8">';
				
				$data['view'] .= '<table id="report_beli" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:40px">No</th><th>Karat</th><th style="width:40px">Pcs</th><th style="width:80px">Gram</th><th style="width:120px">Rata2</th><th style="width:140px">Total Beli</th></tr></thead><tbody>';
				
				$data_karat = $this->mk->get_karat_srt();
				
				$number = 1;
				$total_pcs_all = 0;
				$total_gram_all = 0;
				$total_jual_all = 0;
				
				foreach($data_karat as $dk){
					$id_karat = $dk->id;
					
					$total_pcs = 0;
					$total_gram = 0;
					$total_jual = 0;
					
					$data_rekap = $this->mt->get_pembelian_rekap_by_category($tgl_from,$tgl_to,$id_karat);
					if(count($data_rekap) != 0){
						$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.$dk->karat_name.'</td><td colspan="4"></td><tr>';
						
						foreach($data_rekap as $dr){
							$data['view'] .= '<tr><td></td><td>'.$dr->category_name.'</td><td class="right aligned">'.$dr->pcs.'</td><td class="right aligned">'.number_format($dr->berat, 2).'</td><td class="right aligned">'.number_format($dr->harga/$dr->berat, 2).'</td><td class="right aligned">'.number_format($dr->harga, 2).'</td><tr>';
							
							$total_pcs = $total_pcs + $dr->pcs;
							$total_gram = $total_gram + $dr->berat;
							$total_jual = $total_jual + $dr->harga;
							
							$total_pcs_all = $total_pcs_all + $dr->pcs;
							$total_gram_all = $total_gram_all + $dr->berat;
							$total_jual_all = $total_jual_all + $dr->harga;
						}
						
						$number = $number + 1;
					}
					
					
					if($total_pcs != 0){
						$data['view'] .= '<tr><td colspan="2"></td><td class="dash-top right aligned">'.$total_pcs.'</td><td class="dash-top right aligned">'.number_format($total_gram, 2).'</td><td class="dash-top right aligned"></td><td class="dash-top right aligned">'.number_format($total_jual, 2).'</td><tr>';
					}
				}
				
				if($total_pcs_all != 0){
					$data['view'] .= '<tr><td colspan="2"></td><td class="double-top right aligned">'.$total_pcs_all.'</td><td class="double-top right aligned">'.number_format($total_gram_all, 2).'</td><td class="double-top right aligned"></td><td class="double-top right aligned">'.number_format($total_jual_all, 2).'</td><tr>';
				}
			}
		}
		
		$data['view'] .= '</tbody></table></div>';
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function hapus_beli($id){
		if($this->session->userdata('gold_admin') == 'Y'){
			date_default_timezone_set("Asia/Jakarta");
			$this->db->trans_start();
			
			$this->mt->hapus_main_detail_beli($id);
			
			$idmutasi = $id.'-';
			
			$mutasi_data = $this->mm->get_mutasi_rp_like_id($idmutasi);
			$deletedby = $this->session->userdata('gold_nama_user');
			$created_by = $this->session->userdata('gold_nama_user');
			
			foreach($mutasi_data as $md){
				$siteid = $md->idsite;
				$idmutasi = $md->idmutasi;
				$tipemutasi = $md->tipemutasi;
				$fromaccount = $md->fromaccount;
				$toaccount = $md->toaccount;
				$value = $md->value;
				$description = $md->description;
				$transdate = $md->transdate;
				$createddate = $md->createddate;
				$createdby = $md->createdby;
				
				$this->mm->insert_mutasi_rupiah_deleted($siteid,$idmutasi,$tipemutasi,$fromaccount,$toaccount,$value,$description,$transdate,$createddate,$createdby,$deletedby);
				$this->mm->delete_mutasi_rupiah_like($id);
			}

			$mutasi_data = $this->mm->get_mutasi_gr_like_id($idmutasi);
			
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
				$this->mm->delete_mutasi_gram_like($id);
			}
			
			$this->db->trans_complete();
			
			$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Berhasil Hapus Data!</div></div>';
			$data['success'] = true;
			echo json_encode($data);
		}
	}
	
	public function lap_kasir_to_pdf($tgl_transaksi,$jenis_bayar,$detail_rekap){
		if($jenis_bayar == 'All'){
			$jenis_bayar = '';
		}
		$tgl_transaksi = str_replace('%20',' ',$tgl_transaksi);
		$tanggal_transaksi =  $tgl_transaksi;
		
		$tglTrans = $this->date_to_format($tgl_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		$site_name = $this->mm->get_site_name();
		
		if($detail_rekap == 'D'){
			if($jenis_bayar == ''){
				$kas_account = $this->mm->get_all_kasbank_pos();
				$kas_ket = 'Seluruh Account';
			}else{
				$kas_account = $this->mm->get_single_coa_rp($jenis_bayar);
				$kas_ket = $this->mm->get_coa_number_name_2($jenis_bayar);
			}
			
			$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column center aligned" style="font-weight:bold;text-align:center;font-size:13px;font-family:samsungone"><span style="text-align">Laporan Transaksi Harian Detail</span><br><span>Tanggal '.$tanggal_transaksi.', '.$kas_ket.', Cabang '.$site_name.'</span><br><span style="visibility:hidden">-</span></div><div class="sixteen wide centered column">';
			
			$data['view'] .= '<table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr style="text-align:center"><th class="th-5">Description</th><th class="th-5">Voucher Code</th><th class="th-5">Debit</th><th class="th-5">Credit</th><th class="th-5">Balance</th></tr></thead><tbody>';
			
			foreach($kas_account as $ka){
				$acc_number = $ka->accountnumber;
				$accountnumber = $ka->accountnumber;
				
				/*-- Menentukan Beginning Balance --*/
				$saldo_awal = $this->mm->get_report_rp_beginning_balance($acc_number);
				/*----------------------------------*/
				
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_awal_d = $this->mm->get_report_mutasi_rp_yesterday_d($acc_number,$tgl_transaksi);
				$saldo_awal_k = $this->mm->get_report_mutasi_rp_yesterday_k($acc_number,$tgl_transaksi);
				
				foreach($saldo_awal_d as $mtyd){
					$saldo_awal = $saldo_awal + $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$saldo_awal = $saldo_awal - $mtyk->total_mutasi;
				}
				
				/*---------------------------------------------------*/
				
				/*---------- Mengambil Data Mutasi Transaksi ----------*/
		
				$mutasi_transaksi = $this->mm->get_report_mutasi_rp_lap($tgl_transaksi, $tgl_transaksi, $acc_number);
				
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
					$coa_data = $this->mm->get_coa_number_name($ka->accountnumberint);
					
					$running_balance = $saldo_awal;
					$total_debet = 0;
					$total_kredit = 0;
					
					$sa = number_format($saldo_awal, 0);
					if($sa == 0 || $sa == '' || $sa == -0){
						$data['view'] .= '<tr><td colspan="4" style="font-weight:bold">'.$coa_data.'</td><td class="right-aligned">-</td></tr>';
					}else{
						$data['view'] .= '<tr><td colspan="4" style="font-weight:bold">'.$coa_data.'</td><td class="right-aligned">'.number_format($saldo_awal, 2).'</td></tr>';
					}
										
					foreach($mutasi_transaksi as $mk){
						if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
							$trans_date = strtotime($mk->transdate);
							$trans_date = date('d/m/Y',$trans_date);
							
							$tipe_trans = substr($mk->idmutasi,0,2);
							if($tipe_trans == 'BR' || $tipe_trans == 'JR' || $tipe_trans == 'JP'){
								$idmutasi = substr($mk->idmutasi,0,-2);
							}else{
								$idmutasi = $mk->idmutasi;
							}
							
							if($mk->toaccount == $accountnumber){
								$debet_val = number_format($mk->value, 2);
								$kredit_val = '-';
								
								$running_balance = $running_balance + $mk->value;
								
								$rb = number_format($running_balance, 0);
								if($rb == -0){
									$running_balance = 0;
								}
								
								$total_debet = $total_debet + $mk->value;
							}else{
								$debet_val = '-';
								$kredit_val = number_format($mk->value, 2);
								
								$running_balance = $running_balance - $mk->value;
								
								$rb = number_format($running_balance, 0);
								if($rb == -0){
									$running_balance = 0;
								}
								
								$total_kredit = $total_kredit + $mk->value;
							}
							
							$data['view'] .= '<tr><td>'.$mk->description.'</td><td>'.$idmutasi.'</td><td class="right-aligned">'.$debet_val.'</td><td class="right-aligned">'.$kredit_val.'</td><td class="right-aligned">'.number_format($running_balance, 2).'</td></tr>';
						}
					}
					
					$data['view'] .= '<tr><td colspan="2"></td><td class="right-aligned" style="border-top: 1px solid #000">'.number_format($total_debet, 2).'</td><td class="right-aligned" style="border-top: 1px solid #000">'.number_format($total_kredit, 2).'</td><td></td></tr>';
					$data['view'] .= '<tr><td colspan="7" style="visibility:hidden; color:#FFF">-</td></tr>';
				}
				
				/*----------------------------------------------------*/
			}
		}else if($detail_rekap == 'R'){
			if($jenis_bayar == ''){
				$kas_account = $this->mm->get_all_kasbank_pos();
				$kas_ket = 'Seluruh Account';
			}else{
				$kas_account = $this->mm->get_single_coa_rp($jenis_bayar);
				$kas_ket = $this->mm->get_coa_number_name_2($jenis_bayar);
			}
			
			$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column center aligned" style="font-weight:bold;text-align:center;font-size:13px;font-family:samsungone"><span style="text-align">Laporan Transaksi Harian Rekap</span><br><span>Tanggal '.$tanggal_transaksi.', '.$kas_ket.', Cabang '.$site_name.'</span><br><span style="visibility:hidden">-</span></div><div class="sixteen wide centered column">';
			
			$data['view'] .= '<table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr style="text-align:center"><th class="th-5">Description</th><th class="th-5">Debit</th><th class="th-5">Credit</th><th class="th-5">Balance</th></tr></thead><tbody>';
			
			foreach($kas_account as $ka){
				$acc_number = $ka->accountnumber;
				$accountnumber = $ka->accountnumber;
				
				/*-- Menentukan Beginning Balance --*/
				$saldo_awal = $this->mm->get_report_rp_beginning_balance($acc_number);
				/*----------------------------------*/
				
				/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
				
				$saldo_awal_d = $this->mm->get_report_mutasi_rp_yesterday_d($acc_number,$tgl_transaksi);
				$saldo_awal_k = $this->mm->get_report_mutasi_rp_yesterday_k($acc_number,$tgl_transaksi);
				
				foreach($saldo_awal_d as $mtyd){
					$saldo_awal = $saldo_awal + $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$saldo_awal = $saldo_awal - $mtyk->total_mutasi;
				}
				
				/*---------------------------------------------------*/
				
				/*---------- Mengambil Data Mutasi Transaksi ----------*/
		
				$mutasi_transaksi_jual = $this->mm->get_report_mutasi_rp_jual($tgl_transaksi, $tgl_transaksi, $acc_number);
				$mutasi_transaksi = $this->mm->get_report_mutasi_rp_rekap_lap($tgl_transaksi, $tgl_transaksi, $acc_number);
				$mutasi_transaksi_beli = $this->mm->get_report_mutasi_rp_bycode('BR', $tgl_transaksi, $tgl_transaksi, $acc_number);
				
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
					
					$sa = number_format($saldo_awal, 0);
					if($sa == 0 || $sa == '' || $sa == -0){
						$data['view'] .= '<tr><td colspan="3" style="font-weight:bold">'.$coa_data.'</td><td class="right-aligned">-</td></tr>';
					}else{
						$data['view'] .= '<tr><td colspan="3" style="font-weight:bold">'.$coa_data.'</td><td class="right-aligned">'.number_format($saldo_awal, 2).'</td></tr>';
					}
					
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
					
					if($total_debet_ind != 0 || $total_kredit_ind != 0){
						$data['view'] .= '<tr><td>PENJUALAN</td><td class="right-aligned">'.number_format($total_debet_ind, 2).'</td><td class="right-aligned">'.number_format($total_kredit_ind, 2).'</td><td class="right-aligned">'.number_format($running_balance, 2).'</td><tr>';
					}
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
					
					if($total_debet_ind != 0 || $total_kredit_ind != 0){
						$data['view'] .= '<tr><td>PENERIMAAN / PENGELUARAN KAS </td><td class="right-aligned">'.number_format($total_debet_ind, 2).'</td><td class="right-aligned">'.number_format($total_kredit_ind, 2).'</td><td class="right-aligned">'.number_format($running_balance, 2).'</td></tr>';
					}
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
					
					if($total_debet_ind != 0 || $total_kredit_ind != 0){
						$data['view'] .= '<tr><td>PEMBELIAN</td><td class="right-aligned">'.number_format($total_debet_ind, 2).'</td><td class="right-aligned">'.number_format($total_kredit_ind, 2).'</td><td class="right-aligned">'.number_format($running_balance, 2).'</td></tr>';
					}
					//---
					
					$data['view'] .= '<tr><td></td><td class="right-aligned" style="border-top: 1px solid #000">'.number_format($total_debet, 2).'</td><td class="right-aligned" style="border-top: 1px solid #000">'.number_format($total_kredit, 2).'</td><td></td></tr>';
					$data['view'] .= '<tr><td colspan="7" style="visibility:hidden; color:#FFF">-</td></tr>';
				}
				
				/*----------------------------------------------------*/
			}
		}
		
		$data['view'] .= '</tbody></table></div></div>';
		
		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		$pdf = new \Mpdf\Mpdf();
		$pdf->setFooter('{PAGENO} / {nb}');
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right;font-family:samsungone;font-size:11px;">{PAGENO} / {nb}</div>');
		$pdf->AddPage('P');
        $pdf->WriteHTML($html);
		
        $pdf->Output("Laporan Kas Kasir.pdf", "I");
	}
	
	public function lap_jual_to_pdf($tgl_from,$tgl_to,$detail_rekap,$filter_box,$filter_karat,$filter_rekap){
		date_default_timezone_set("Asia/Jakarta");
		$tgl_from = str_replace('%20',' ',$tgl_from);
		$tgl_to = str_replace('%20',' ',$tgl_to);
		
		$tanggal_from =  $tgl_from;
		$tglFrom = $this->date_to_format($tgl_from);
		$tgl_from = date("Y-m-d",$tglFrom).' 00:00:00';
		
		$tanggal_to =  $tgl_to;
		$tglTo = $this->date_to_format($tgl_to);
		$tgl_to = date("Y-m-d",$tglTo).' 00:00:00';
		
		if($filter_box == 'All'){
			$filter_box = '';
			$data_box = $this->mb->get_box_aktif();
			for($i=0; $i<count($data_box); $i++){
				if($i == 0){
					$filter_box .= '"'.$data_box[$i]->id.'"';
				}else{
					$filter_box .= ',"'.$data_box[$i]->id.'"';
				}
			}
			
			$box_tulis = 'All Box';
		}else{
			$box_tulis = 'Box '.$filter_box;
			$filter_box = '"'.$filter_box.'"';
		}
		
		if($filter_karat == 'All'){
			$filter_karat = '';
			$data_karat = $this->mk->get_karat_srt();
			for($i=0; $i<count($data_karat); $i++){
				if($i == 0){
					$filter_karat .= '"'.$data_karat[$i]->id.'"';
				}else{
					$filter_karat .= ',"'.$data_karat[$i]->id.'"';
				}
			}
			
			$karat_tulis = 'All Karat';
		}else{
			$karat_tulis = $this->mk->get_karat_name_by_id($filter_karat);
			$karat_tulis = 'Karat '.$karat_tulis;
			$filter_karat = '"'.$filter_karat.'"';
		}
		
		$site_name = $this->mm->get_site_name();
		
		if($detail_rekap == 'D'){
			$array_jual = array();
			$dj = $this->mt->get_penjualan_kasir($tgl_from,$tgl_to,$filter_box,$filter_karat);
			
			$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column center aligned" style="text-align:center;font-weight:bold;font-size:13px;font-family:samsungone;"><span>Laporan Penjualan Harian Detail</span><br><span>Tanggal '.$tanggal_from.' s/d '.$tanggal_to.', Cabang '.$site_name.'</span><br><span>'.$box_tulis.', '.$karat_tulis.'</span><br><span style="visibility:hidden">-</span></div><div class="sixteen wide centered column">';
			
			$data['view'] .= '<table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:40px" class="th-5">No</th><th class="th-5" style="width:120px">ID Product</th><th class="th-5">Box</th><th class="th-5" style="width:170px">Keterangan</th><th class="th-5">Karat</th><th class="th-5">Berat</th><th class="th-5" style="width:100px">Harga Jual</th><th class="th-5">Total Jual</th></tr></thead><tbody>';
			
			$length = count($dj);
			$trans_temp = '';
			$total_temp = 0;
			$number = 1;
			$total_pcs_all = 0;
			$total_gram_all = 0;
			$total_jual_all = 0;
			
			for($i = 0;$i < $length; $i++){
				$id_trans = $dj[$i]->transaction_code;
				$act = '';
				
				if($i == 0){
					$trans_date = strtotime($dj[$i]->trans_date);
					$trans_date = date('d-M-Y',$trans_date);
					$trans_temp = $id_trans;
					$cust_service = $this->mt->get_cs_jual_by_id($id_trans);
					$data['view'] .= '<tr><td class="right-aligned">'.$number.'. </td><td class="td-bold" colspan="5">'.$act.' '.$trans_date.' | '.$id_trans.'</td><td colspan="2" style="border-left:none;border-right:none;text-transform:uppercase">Customer Service : '.$cust_service.'</td></tr>';
					
					$number = $number + 1;
					
					$box_number = '';
					$totalnumberlength = 3;
					$numberlength = strlen($dj[$i]->id_box);
					$numberspace = $totalnumberlength - $numberlength;
					if($numberspace != 0){
						for ($a = 1; $a <= $numberspace; $a++){
							$box_number .= '0';
						}
					}
					
					$box_number .= $dj[$i]->id_box;
					
					$data['view'] .= '<tr><td></td><td>'.$dj[$i]->id_product.'</td><td class="center aligned">'.$box_number.'</td><td>'.$dj[$i]->product_desc.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right-aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price/$dj[$i]->product_weight, 2).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
					
					$total_pcs_all = $total_pcs_all + 1;
					$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
					$total_jual_all = $total_jual_all + $dj[$i]->product_price;
					
					$total_temp = $total_temp + $dj[$i]->product_price;
				}else{
					if($dj[$i]->transaction_code == $trans_temp){
						$box_number = '';
						$totalnumberlength = 3;
						$numberlength = strlen($dj[$i]->id_box);
						$numberspace = $totalnumberlength - $numberlength;
						if($numberspace != 0){
							for ($a = 1; $a <= $numberspace; $a++){
								$box_number .= '0';
							}
						}
						
						$box_number .= $dj[$i]->id_box;
						
						$data['view'] .= '<tr><td></td><td>'.$dj[$i]->id_product.'</td><td class="center aligned">'.$box_number.'</td><td>'.$dj[$i]->product_desc.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right-aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price/$dj[$i]->product_weight, 2).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
						
						$total_temp = $total_temp + $dj[$i]->product_price;
						
						$total_pcs_all = $total_pcs_all + 1;
						$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
						$total_jual_all = $total_jual_all + $dj[$i]->product_price;
					}else{
						$data['view'] .= '<tr><td colspan="5"></td><td class="double-top"></td><td class="double-top"></td><td class="double-top right-aligned">'.number_format($total_temp, 2).'</td></tr>';
						
						$total_temp = 0;
						
						$trans_date = strtotime($dj[$i]->trans_date);
						$trans_date = date('d-M-Y',$trans_date);
						$trans_temp = $id_trans;
						$cust_service = $this->mt->get_cs_jual_by_id($id_trans);
						$data['view'] .= '<tr><td class="right-aligned">'.$number.'. </td><td class="td-bold" colspan="5">'.$act.' '.$trans_date.' | '.$id_trans.'</td><td colspan="2" style="border-left:none;border-right:none;text-transform:uppercase">Customer Service : '.$cust_service.'</td></tr>';
						
						$number = $number + 1;
						
						$box_number = '';
						$totalnumberlength = 3;
						$numberlength = strlen($dj[$i]->id_box);
						$numberspace = $totalnumberlength - $numberlength;
						if($numberspace != 0){
							for ($a = 1; $a <= $numberspace; $a++){
								$box_number .= '0';
							}
						}
						
						$box_number .= $dj[$i]->id_box;
						
						$data['view'] .= '<tr><td></td><td>'.$dj[$i]->id_product.'</td><td class="center aligned">'.$box_number.'</td><td>'.$dj[$i]->product_desc.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right-aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price/$dj[$i]->product_weight, 2).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
						
						$total_temp = $total_temp + $dj[$i]->product_price;
						
						$total_pcs_all = $total_pcs_all + 1;
						$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
						$total_jual_all = $total_jual_all + $dj[$i]->product_price;
					}
				}
			}
			
			if($length != 0){
				$data['view'] .= '<tr><td colspan="5"></td><td class="double-top"></td><td class="double-top"></td><td class="double-top right-aligned">'.number_format($total_temp, 2).'</td></tr>';
				
				
				$data['view'] .= '<tr><td colspan="8"><span style="visibility:hidden">-</span></td></tr><tr><td colspan="4"></td><td class="double-top right-aligned">'.$total_pcs_all.' Pcs</td><td class="double-top right-aligned">'.number_format($total_gram_all, 3).'</td><td class="double-top"></td><td class="double-top right-aligned">'.number_format($total_jual_all, 2).'</td></tr>';
			}
		}else if($detail_rekap == 'R'){
			if($filter_rekap == 'K'){
				$number = 1;
				$total_pcs = 0;
				$total_gram = 0;
				$total_jual = 0;
				
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column center aligned" style="text-align:center;font-weight:bold;font-size:13px;font-family:samsungone;"><span>Laporan Penjualan Harian Rekap / Karat</span><br><span>Tanggal '.$tanggal_from.' s/d '.$tanggal_to.', Cabang '.$site_name.'</span><br><span style="visibility:hidden">-</span></div><div class="sixteen wide centered column">';
				
				$data['view'] .= '<table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:40px" class="th-5">No</th><th class="th-5">Karat</th><th style="width:40px" class="th-5">Pcs</th><th style="width:80px" class="th-5">Gram</th><th style="width:120px" class="th-5">Rata2</th><th style="width:140px" class="th-5">Total Jual</th></tr></thead><tbody>';
			
				$data_rekap = $this->mt->get_penjualan_rekap_karat($tgl_from,$tgl_to);
				foreach($data_rekap as $dr){
					$data['view'] .= '<tr><td class="right-aligned">'.$number.'. </td><td>'.$dr->karat_name.'</td><td class="right-aligned">'.$dr->pcs.'</td><td class="right-aligned">'.number_format($dr->berat, 2).'</td><td class="right-aligned">'.number_format($dr->harga/$dr->berat, 2).'</td><td class="right-aligned">'.number_format($dr->harga, 2).'</td><tr>';
					
					$total_pcs = $total_pcs + $dr->pcs;
					$total_gram = $total_gram + $dr->berat;
					$total_jual = $total_jual + $dr->harga;
					$number = $number + 1;
				}
				
				if($total_pcs != 0){
					$data['view'] .= '<tr><td colspan="2"></td><td class="double-top right-aligned">'.$total_pcs.'</td><td class="double-top right-aligned">'.number_format($total_gram, 2).'</td><td class="double-top right-aligned"></td><td class="double-top right-aligned">'.number_format($total_jual, 2).'</td><tr>';
				}
			}else if($filter_rekap == 'All'){
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column center aligned" style="text-align:center;font-weight:bold;font-size:13px;font-family:samsungone;"><span>Laporan Penjualan Harian Rekap</span><br><span>Tanggal '.$tanggal_from.' s/d '.$tanggal_to.', Cabang '.$site_name.'</span><br><span style="visibility:hidden">-</span></div><div class="sixteen wide centered column">';
				
				$data['view'] .= '<div class="offset-lg-2 col-lg-8 offset-md-2 col-md-8">';
				
				$data['view'] .= '<table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:40px" class="th-5">No</th><th class="th-5">Tanggal</th><th class="th-5">ID Transaksi</th><th class="th-5">Nama Customer</th><th class="th-5">Alamat Customer</th><th class="th-5">Telepon Customer</th><th class="th-5">Jumlah</th></tr></thead><tbody>';
				
				$kas_account = $this->mm->get_all_kasbank();
				$number = 1;
				$running_balance = 0;
				foreach($kas_account as $ka){
					$acc_number = $ka->accountnumber;
					$accountnumber = $ka->accountnumber;
					
					/*---------- Mengambil Data Mutasi Transaksi ----------*/
			
					$mutasi_transaksi_jual = $this->mm->get_report_mutasi_rp_jual($tgl_from, $tgl_to, $acc_number);
					
					/*---------------------------------------------------*/
					
					/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
					
					$flag_kurs = FALSE;
					$flag_mutasi = FALSE;
					
					foreach($mutasi_transaksi_jual as $mk){
						if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
							$flag_mutasi = TRUE;
							$flag_kurs = TRUE;
						}
					}
					
					/*-------- Menampilkan Data Dalam Tabel Report -------*/
					
					if($flag_kurs == TRUE){
						$coa_data = $this->mm->get_coa_number_name($ka->accountnumberint);
						
						$total_debet = 0;
						
						$data['view'] .= '<tr><td colspan="7" style="font-weight:bold">'.$coa_data.'</td></tr>';
						
						//PENJUALAN
						
						foreach($mutasi_transaksi_jual as $mk){
							if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
								if($mk->toaccount == $accountnumber){
									$trans_date = strtotime($mk->transdate);
									$trans_date = date('d/m/Y',$trans_date);
									
									$idmutasi = substr($mk->idmutasi,0,20);
									$customer_name = $this->mt->get_customer_name($idmutasi);
									$customer_address = $this->mt->get_customer_address($idmutasi);
									$customer_phone = $this->mt->get_customer_phone($idmutasi);
									
									$data['view'] .= '<tr><td class="right-aligned">'.$number.'. </td><td>'.$trans_date.'</td><td>'.$idmutasi.'</td><td>'.$customer_name.'</td><td>'.$customer_address.'</td><td>'.$customer_phone.'</td><td class="right-aligned">'.number_format($mk->value, 2).'</td><tr>';
									$total_debet = $total_debet + $mk->value;
									$running_balance = $running_balance + $mk->value;
									$number = $number + 1;
								}
							}
						}
						
						if($total_debet != 0){
							$data['view'] .= '<tr><td colspan="6"></td><td class="double-top right-aligned">'.number_format($total_debet, 2).'</td><tr>';
						}
						//---
					}
					
					/*----------------------------------------------------*/
				}
				
				if($running_balance != 0){
					$data['view'] .= '<tr><td colspan="6"></td><td class="right-aligned"><span style="visibility:hidden">-</span></td><tr>';
					$data['view'] .= '<tr><td colspan="6"></td><td class="double-top right-aligned">'.number_format($running_balance, 2).'</td><tr>';
				}
			}else if($filter_rekap == 'G'){
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column center aligned" style="text-align:center;font-weight:bold;font-size:13px;font-family:samsungone;"><span>Laporan Penjualan Harian Rekap / Kelompok</span><br><span>Tanggal '.$tanggal_from.' s/d '.$tanggal_to.', Cabang '.$site_name.'</span><br><span style="visibility:hidden">-</span></div><div class="sixteen wide centered column">';
				
				$data['view'] .= '<table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:40px" class="th-5">No</th><th class="th-5">Karat</th><th style="width:40px" class="th-5">Pcs</th><th style="width:80px" class="th-5">Gram</th><th style="width:120px" class="th-5">Rata2</th><th style="width:140px" class="th-5">Total Beli</th></tr></thead><tbody>';
				
				$data_karat = $this->mk->get_karat_srt();
				
				$number = 1;
				$total_pcs_all = 0;
				$total_gram_all = 0;
				$total_jual_all = 0;
				
				foreach($data_karat as $dk){
					$id_karat = $dk->id;
					
					$total_pcs = 0;
					$total_gram = 0;
					$total_jual = 0;
					
					$data_rekap = $this->mt->get_penjualan_rekap_by_category($tgl_from,$tgl_to,$id_karat);
					if(count($data_rekap) != 0){
						$data['view'] .= '<tr><td class="right-aligned">'.$number.'. </td><td class="td-bold">'.$dk->karat_name.'</td><td colspan="4"></td><tr>';
						
						foreach($data_rekap as $dr){
							$data['view'] .= '<tr><td></td><td class="td-bold">'.$dr->category_name.'</td><td class="right-aligned">'.$dr->pcs.'</td><td class="right-aligned">'.number_format($dr->berat, 2).'</td><td class="right-aligned">'.number_format($dr->harga/$dr->berat, 2).'</td><td class="right-aligned">'.number_format($dr->harga, 2).'</td><tr>';
							
							$total_pcs = $total_pcs + $dr->pcs;
							$total_gram = $total_gram + $dr->berat;
							$total_jual = $total_jual + $dr->harga;
							
							$total_pcs_all = $total_pcs_all + $dr->pcs;
							$total_gram_all = $total_gram_all + $dr->berat;
							$total_jual_all = $total_jual_all + $dr->harga;
						}
						
						$number = $number + 1;
					}
					
					
					if($total_pcs != 0){
						$data['view'] .= '<tr><td colspan="2"></td><td class="double-top right-aligned">'.$total_pcs.'</td><td class="double-top right-aligned">'.number_format($total_gram, 2).'</td><td class="double-top right-aligned"></td><td class="double-top right-aligned">'.number_format($total_jual, 2).'</td><tr>';
					}
				}
				
				if($total_pcs_all != 0){
					$data['view'] .= '<tr><td colspan="6"><span style="visibility:hidden">-</span></td></tr><tr><td colspan="2"></td><td class="double-top right-aligned">'.$total_pcs_all.'</td><td class="double-top right-aligned">'.number_format($total_gram_all, 2).'</td><td class="double-top right-aligned"></td><td class="double-top right-aligned">'.number_format($total_jual_all, 2).'</td></tr>';
				}
			}
		}
		
		$data['view'] .= '</tbody></table></div>';
		
		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		$pdf = new \Mpdf\Mpdf();
		$pdf->setFooter('{PAGENO} / {nb}');
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right;font-family:samsungone;font-size:11px;">{PAGENO} / {nb}</div>');
		$pdf->AddPage('P');
        $pdf->WriteHTML($html);
		
        $pdf->Output("Laporan Penjualan.pdf", "I");
	}
	
	public function lap_beli_to_pdf($tgl_from,$tgl_to,$detail_rekap,$filter_karat,$filter_rekap){
		date_default_timezone_set("Asia/Jakarta");
		$tgl_from = str_replace('%20',' ',$tgl_from);
		$tgl_to = str_replace('%20',' ',$tgl_to);
		
		$tanggal_from =  $tgl_from;
		$tglFrom = $this->date_to_format($tgl_from);
		$tgl_from = date("Y-m-d",$tglFrom).' 00:00:00';
		
		$tanggal_to =  $tgl_to;
		$tglTo = $this->date_to_format($tgl_to);
		$tgl_to = date("Y-m-d",$tglTo).' 00:00:00';
		
		if($filter_karat == 'All'){
			$filter_karat = '';
			$data_karat = $this->mk->get_karat_srt();
			for($i=0; $i<count($data_karat); $i++){
				if($i == 0){
					$filter_karat .= '"'.$data_karat[$i]->id.'"';
				}else{
					$filter_karat .= ',"'.$data_karat[$i]->id.'"';
				}
			}
			
			$karat_tulis = 'All Karat';
		}else{
			$karat_tulis = $this->mk->get_karat_name_by_id($filter_karat);
			$karat_tulis = 'Karat '.$karat_tulis;
			$filter_karat = '"'.$filter_karat.'"';
		}
		
		$site_name = $this->mm->get_site_name();
		
		if($detail_rekap == 'D'){
			$array_jual = array();
			$dj = $this->mt->get_pembelian_kasir($tgl_from,$tgl_to,$filter_karat);
			
			$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column center aligned" style="text-align:center;font-weight:bold;font-size:13px;font-family:samsungone;"><span>Laporan Pembelian Harian Detail</span><br><span>Tanggal '.$tanggal_from.' s/d '.$tanggal_to.', Cabang '.$site_name.'</span><br><span>'.$karat_tulis.'</span><br><span style="visibility:hidden">-</span></div><div class="sixteen wide centered column">';
			
			$data['view'] .= '<table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:40px" class="th-5">No</th><th class="th-5">Kelompok</th><th class="th-5">Keterangan</th><th class="th-5">Karat</th><th class="th-5">Pcs</th><th class="th-5">Berat</th><th class="th-5">Harga Beli</th><th class="th-5">Total Beli</th></tr></thead><tbody>';
			
			$length = count($dj);
			$trans_temp = '';
			$total_pcs_temp = 0;
			$total_gram_temp = 0;
			$total_temp = 0;
			$number = 1;
			$total_pcs_all = 0;
			$total_gram_all = 0;
			$total_jual_all = 0;
			
			for($i = 0;$i < $length; $i++){
				$id_trans = $dj[$i]->transaction_code;
				$act = '';
				
				if($i == 0){
					$trans_date = strtotime($dj[$i]->trans_date);
					$trans_date = date('d-M-Y',$trans_date);
					$trans_temp = $id_trans;
					
					$cust_service = $this->mt->get_cs_beli_by_id($id_trans);
					
					$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td colspan="5">'.$act.' '.$trans_date.' | '.$id_trans.'</td><td colspan="2" style="border-left:none;border-right:none;text-transform:uppercase">Customer Service : '.$cust_service.'</td></tr>';
					
					$number = $number + 1;
					
					$data['view'] .= '<tr><td></td><td>'.$dj[$i]->category_name.'</td><td>'.$dj[$i]->nama_product.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right-aligned">'.$dj[$i]->product_pcs.'</td><td class="right-aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price/$dj[$i]->product_weight, 2).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
					
					$total_pcs_all = $total_pcs_all + $dj[$i]->product_pcs;
					$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
					$total_jual_all = $total_jual_all + $dj[$i]->product_price;
					
					$total_pcs_temp = $total_pcs_temp + $dj[$i]->product_pcs;
					$total_gram_temp = $total_gram_temp + $dj[$i]->product_weight;
					$total_temp = $total_temp + $dj[$i]->product_price;
				}else{
					if($dj[$i]->transaction_code == $trans_temp){
						
						$data['view'] .= '<tr><td></td><td>'.$dj[$i]->category_name.'</td><td>'.$dj[$i]->nama_product.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right-aligned">'.$dj[$i]->product_pcs.'</td><td class="right-aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price/$dj[$i]->product_weight, 2).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
						
						$total_pcs_all = $total_pcs_all + $dj[$i]->product_pcs;
						$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
						$total_jual_all = $total_jual_all + $dj[$i]->product_price;
						
						$total_pcs_temp = $total_pcs_temp + $dj[$i]->product_pcs;
						$total_gram_temp = $total_gram_temp + $dj[$i]->product_weight;
						$total_temp = $total_temp + $dj[$i]->product_price;
					}else{
						$data['view'] .= '<tr><td colspan="4"></td><td class="double-top right-aligned">'.$total_pcs_temp.'</td><td class="double-top right-aligned">'.number_format($total_gram_temp, 3).'</td><td class="double-top"></td><td class="double-top right-aligned">'.number_format($total_temp, 2).'</td></tr>';
						
						$total_temp = 0;
						$total_pcs_temp = 0;
						$total_gram_temp = 0;
						
						$trans_date = strtotime($dj[$i]->trans_date);
						$trans_date = date('d-M-Y',$trans_date);
						$trans_temp = $id_trans;
						
						$cust_service = $this->mt->get_cs_beli_by_id($id_trans);
					
						$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td colspan="5">'.$act.' '.$trans_date.' | '.$id_trans.'</td><td colspan="2" style="border-left:none;border-right:none;text-transform:uppercase">Customer Service : '.$cust_service.'</td></tr>';
						
						$number = $number + 1;
						
						$data['view'] .= '<tr><td></td><td>'.$dj[$i]->category_name.'</td><td>'.$dj[$i]->nama_product.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right-aligned">'.$dj[$i]->product_pcs.'</td><td class="right-aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price/$dj[$i]->product_weight, 2).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
						
						$total_pcs_all = $total_pcs_all + $dj[$i]->product_pcs;
						$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
						$total_jual_all = $total_jual_all + $dj[$i]->product_price;
						
						$total_pcs_temp = $total_pcs_temp + $dj[$i]->product_pcs;
						$total_gram_temp = $total_gram_temp + $dj[$i]->product_weight;
						$total_temp = $total_temp + $dj[$i]->product_price;
					}
				}
			}
			
			if($length != 0){
				$data['view'] .= '<tr><td colspan="4"></td><td class="double-top right-aligned">'.$total_pcs_temp.'</td><td class="double-top right-aligned">'.number_format($total_gram_temp, 3).'</td><td class="double-top"></td><td class="double-top right-aligned">'.number_format($total_temp, 2).'</td></tr>';
				
				$data['view'] .= '<tr><td colspan="4"></td><td class="double-top right-aligned">'.$total_pcs_all.'</td><td class="double-top right-aligned">'.number_format($total_gram_all, 3).'</td><td class="double-top"></td><td class="double-top right-aligned">'.number_format($total_jual_all, 2).'</td></tr>';
			}
		}else if($detail_rekap == 'R'){
			if($filter_rekap == 'K'){
				$number = 1;
				$total_pcs = 0;
				$total_gram = 0;
				$total_jual = 0;
				
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column center aligned" style="text-align:center;font-weight:bold;font-size:13px;font-family:samsungone;"><span>Laporan Pembelian Harian Rekap / Karat</span><br><span>Tanggal '.$tanggal_from.' s/d '.$tanggal_to.', Cabang '.$site_name.'</span><br><span style="visibility:hidden">-</span></div>';
				
				$data['view'] .= '<div class="offset-lg-2 col-lg-8 offset-md-2 col-md-8">';
				
				$data['view'] .= '<table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:40px" class="th-5">No</th><th class="th-5">Karat</th><th style="width:40px" class="th-5">Pcs</th><th style="width:80px" class="th-5">Gram</th><th style="width:120px" class="th-5">Rata2</th><th style="width:140px" class="th-5">Total Beli</th></tr></thead><tbody>';
			
				$data_rekap = $this->mt->get_pembelian_rekap_karat($tgl_from,$tgl_to);
				foreach($data_rekap as $dr){
					$data['view'] .= '<tr><td class="right-aligned">'.$number.'. </td><td>'.$dr->karat_name.'</td><td class="right-aligned">'.$dr->pcs.'</td><td class="right-aligned">'.number_format($dr->berat, 2).'</td><td class="right-aligned">'.number_format($dr->harga/$dr->berat, 2).'</td><td class="right-aligned">'.number_format($dr->harga, 2).'</td><tr>';
					
					$total_pcs = $total_pcs + $dr->pcs;
					$total_gram = $total_gram + $dr->berat;
					$total_jual = $total_jual + $dr->harga;
					$number = $number + 1;
				}
				
				if($total_pcs != 0){
					$data['view'] .= '<tr><td colspan="2"></td><td class="double-top right-aligned">'.$total_pcs.'</td><td class="double-top right-aligned">'.number_format($total_gram, 2).'</td><td class="double-top right-aligned"></td><td class="double-top right-aligned">'.number_format($total_jual, 2).'</td><tr>';
				}
			}else if($filter_rekap == 'All'){
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column center aligned" style="text-align:center;font-weight:bold;font-size:13px;font-family:samsungone;"><span>Laporan Pembelian Harian Rekap</span><br><span>Tanggal '.$tanggal_from.' s/d '.$tanggal_to.', Cabang '.$site_name.'</span><br><span style="visibility:hidden">-</span></div>';
				
				$data['view'] .= '<table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:40px" class="th-5">No</th><th class="th-5">Tanggal</th><th class="th-5">ID Transaksi</th><th class="th-5">Nama Customer</th><th class="th-5">Alamat Customer</th><th class="th-5">Telepon Customer</th><th class="th-5">Jumlah</th></tr></thead><tbody>';
				
				$kas_account = $this->mm->get_all_kasbank();
				$number = 1;
				$running_balance = 0;
				foreach($kas_account as $ka){
					$acc_number = $ka->accountnumber;
					$accountnumber = $ka->accountnumber;
					
					/*---------- Mengambil Data Mutasi Transaksi ----------*/
			
					$mutasi_transaksi_beli = $this->mm->get_report_mutasi_rp_bycode('BR', $tgl_from, $tgl_to, $acc_number);
					
					/*---------------------------------------------------*/
					
					/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
					
					$flag_kurs = FALSE;
					$flag_mutasi = FALSE;
					
					foreach($mutasi_transaksi_beli as $mk){
						if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
							$flag_mutasi = TRUE;
							$flag_kurs = TRUE;
						}
					}
					
					/*-------- Menampilkan Data Dalam Tabel Report -------*/
					
					if($flag_kurs == TRUE){
						$coa_data = $this->mm->get_coa_number_name($ka->accountnumberint);
						
						$total_debet = 0;
						
						$data['view'] .= '<tr><td colspan="7" style="font-weight:bold">'.$coa_data.'</td></tr>';
						
						foreach($mutasi_transaksi_beli as $mk){
							if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
								if($mk->fromaccount == $accountnumber){
									$trans_date = strtotime($mk->transdate);
									$trans_date = date('d/m/Y',$trans_date);
									
									$idmutasi = substr($mk->idmutasi,0,20);
									$customer_name = $this->mt->get_customer_name2($idmutasi);
									$customer_address = $this->mt->get_customer_address2($idmutasi);
									$customer_phone = $this->mt->get_customer_phone2($idmutasi);
									
									$data['view'] .= '<tr><td class="right-aligned">'.$number.'. </td><td>'.$trans_date.'</td><td>'.$idmutasi.'</td><td>'.$customer_name.'</td><td>'.$customer_address.'</td><td>'.$customer_phone.'</td><td class="right-aligned">'.number_format($mk->value, 2).'</td></tr>';
									$total_debet = $total_debet + $mk->value;
									$running_balance = $running_balance + $mk->value;
									$number = $number + 1;
								}
							}
						}
						
						if($total_debet != 0){
							$data['view'] .= '<tr><td colspan="6"></td><td class="double-top right-aligned">'.number_format($total_debet, 2).'</td><tr>';
						}
						//---
					}
					
					/*----------------------------------------------------*/
				}
				
				if($running_balance != 0){
					$data['view'] .= '<tr><td colspan="6"></td><td class="right-aligned"><span style="visibility:hidden">-</span></td><tr>';
					$data['view'] .= '<tr><td colspan="6"></td><td class="double-top right-aligned">'.number_format($running_balance, 2).'</td><tr>';
				}
			}else if($filter_rekap == 'G'){
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column center aligned" style="text-align:center;font-weight:bold;font-size:13px;font-family:samsungone;"><span>Laporan Pembelian Harian Rekap / Kelompok</span><br><span>Tanggal '.$tanggal_from.' s/d '.$tanggal_to.', Cabang '.$site_name.'</span><br><span style="visibility:hidden">-</span></div>';
				
				$data['view'] .= '<table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:40px" class="th-5">No</th><th class="th-5">Karat</th><th style="width:40px" class="th-5">Pcs</th><th style="width:80px" class="th-5">Gram</th><th style="width:120px" class="th-5">Rata2</th><th style="width:140px" class="th-5">Total Beli</th></tr></thead><tbody>';
				
				$data_karat = $this->mk->get_karat_srt();
				
				$number = 1;
				$total_pcs_all = 0;
				$total_gram_all = 0;
				$total_jual_all = 0;
				
				foreach($data_karat as $dk){
					$id_karat = $dk->id;
					
					$total_pcs = 0;
					$total_gram = 0;
					$total_jual = 0;
					
					$data_rekap = $this->mt->get_pembelian_rekap_by_category($tgl_from,$tgl_to,$id_karat);
					if(count($data_rekap) != 0){
						$data['view'] .= '<tr><td class="right-aligned">'.$number.'. </td><td>'.$dk->karat_name.'</td><td colspan="4"></td><tr>';
						
						foreach($data_rekap as $dr){
							$data['view'] .= '<tr><td></td><td>'.$dr->category_name.'</td><td class="right-aligned">'.$dr->pcs.'</td><td class="right-aligned">'.number_format($dr->berat, 2).'</td><td class="right-aligned">'.number_format($dr->harga/$dr->berat, 2).'</td><td class="right-aligned">'.number_format($dr->harga, 2).'</td><tr>';
							
							$total_pcs = $total_pcs + $dr->pcs;
							$total_gram = $total_gram + $dr->berat;
							$total_jual = $total_jual + $dr->harga;
							
							$total_pcs_all = $total_pcs_all + $dr->pcs;
							$total_gram_all = $total_gram_all + $dr->berat;
							$total_jual_all = $total_jual_all + $dr->harga;
						}
						
						$number = $number + 1;
					}
					
					
					if($total_pcs != 0){
						$data['view'] .= '<tr><td colspan="2"></td><td class="double-top right-aligned">'.$total_pcs.'</td><td class="double-top right-aligned">'.number_format($total_gram, 2).'</td><td class="double-top right-aligned"></td><td class="double-top right-aligned">'.number_format($total_jual, 2).'</td><tr>';
					}
				}
				
				if($total_pcs_all != 0){
					$data['view'] .= '<tr><td colspan="6"><span style="visibility:hidden">-</span></td></tr><tr><td colspan="2"></td><td class="double-top right-aligned">'.$total_pcs_all.'</td><td class="double-top right-aligned">'.number_format($total_gram_all, 2).'</td><td class="double-top right-aligned"></td><td class="double-top right-aligned">'.number_format($total_jual_all, 2).'</td><tr>';
				}
			}
		}
		
		$data['view'] .= '</tbody></table></div>';
		
		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		$pdf = new \Mpdf\Mpdf();
		$pdf->setFooter('{PAGENO} / {nb}');
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right;font-family:samsungone;font-size:11px;">{PAGENO} / {nb}</div>');
		$pdf->AddPage('P');
        $pdf->WriteHTML($html);
		
        $pdf->Output("Laporan Pembelian.pdf", "I");
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
