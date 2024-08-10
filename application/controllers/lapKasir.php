<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LapKasir extends CI_Controller {

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
		$acc_ke = $this->mm->get_default_account('KE');
		$bayar = $this->mt->get_bayar_nt();
		$karat = $this->mk->get_karat_srt();
		$box = $this->mb->get_box_aktif();
		
		$data['view'] = '<div class="ui fluid container">
		<div class="ui pointing secondary menu">
			<a class="item active" data-tab="lapKasir-first" style="width:33%" onclick=filterLapKasir("lapKasir")>
				<i class="columns icon"></i> Laporan Kas Kasir
			</a>
			<a class="item" data-tab="lapKasir-second" style="width:33%" onclick=filterJualKasir("lapKasir")>
				<i class="tags icon"></i> Laporan Penjualan
			</a>
			<a class="item" data-tab="lapKasir-third" style="width:34%" onclick=filterBeliKasir("lapKasir")>
				<i class="shopping cart icon"></i> Laporan Pembelian
			</a>
		</div>
		<div class="ui bottom attached tab segment active" data-tab="lapKasir-first">
			<form class="ui form" id="lapKasir-form-lap" action="'.base_url().'index.php/lapKasir/filter_kas" method="post">
			<div class="ui grid">
				<div class="ten wide centered column" style="padding-bottom:0">
					<div class="fields">
						<div class="five wide field">
							<label>Tanggal Transaksi</label>
							<input type="text" name="lapKasir-datekas" id="lapKasir-datekas" readonly>
						</div>
						<div class="five wide field">
							<label>Jenis Pembayaran</label>
							<select name="lapKasir-k-jenis_bayar" id="lapKasir-k-jenis_bayar">
								<option value="">Semua</option>
								<option value="'.$acc_ke.'">TUNAI</option>';
								foreach($bayar as $b){
								$data['view'] .= '<option value="'.$b->account_number.'">'.$b->description.'</option>';
								}
							$data['view'] .= '</select>
						</div>
						<div class="three wide field">
							<label style="visibility:hidden">Detail/Rekap</label>
							<select name="lapKasir-k-detail_rekap" id="lapKasir-k-detail_rekap">
								<option value="D">Detail</option>
								<option value="R">Rekap</option>
							</select>
						</div>
						<div class="two wide field">
							<label style="visibility:hidden">Filter</label>
							<div class="ui fluid icon green button filter-input" id="lapKasir-k-btnfilter" onclick=filterLapKasir("lapKasir") title="Filter">
								<i class="filter icon"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="twelve wide centered column" id="lapKasir-wrap_lap">
				</div>
			</div>
			</form>
		</div>
		<div class="ui bottom attached tab segment" data-tab="lapKasir-second">
			<form class="ui form" id="lapKasir-form-jual" action="'.base_url().'index.php/lapKasir/filter_jual" method="post">
			<div class="ui grid">
				<div class="ten wide centered column" style="padding-bottom:0">
					<div class="fields">
						<div class="five wide field">
							<input type="text" name="lapKasir-jual-fromdate" id="lapKasir-jual-fromdate" readonly>
						</div>
						<div class="two wide field" style="text-align:center;margin-top:7px">
							<label>s.d</label>
						</div>
						<div class="five wide field">
							<input type="text" name="lapKasir-jual-todate" id="lapKasir-jual-todate" readonly>
						</div>
						<div class="four wide field">
							<select name="lapKasir-jual-detail_rekap" id="lapKasir-jual-detail_rekap" onchange=getDetailRekapJual()>
								<option value="D">Detail</option>
								<option value="R">Rekap</option>
							</select>
						</div>
					</div>
					<div class="fields">
						<div class="four wide field" id="lapKasir-wrap_box_jual">
							<select name="lapKasir-filter_box_jual" id="lapKasir-filter_box_jual">
								<option value="All">-- Seluruh Box --</option>';
								foreach($box as $b){
								$data['view'] .= '<option value="'.$b->id.'">BOX '.$b->nama_box.'</option>';
								}
							$data['view'] .= '</select>
						</div>
						<div class="four wide field" id="lapKasir-wrap_karat_jual">
							<select name="lapKasir-filter_karat_jual" id="lapKasir-filter_karat_jual">
								<option value="All">-- Seluruh Karat --</option>';
								foreach($karat as $k){
								$data['view'] .= '<option value="'.$k->id.'">'.$k->karat_name.'</option>';
								}
							$data['view'] .= '</select>
						</div>
						<div class="four wide field" id="lapKasir-wrap_rekap_jual">
							<select name="lapKasir-filter_rekap_jual" id="lapKasir-filter_rekap_jual">
								<option value="All">Semua</option>
								<option value="K">Per Karat</option>
								<option value="G">Per Kelompok</option>
							</select>
						</div>
						<div class="four wide field">
							<div class="ui fluid icon green button filter-input" id="lapKasir-btnfilterjual" onclick=filterJualKasir("lapKasir") title="Filter">
								<i class="filter icon"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="twelve wide centered column" id="lapKasir-wrap_jual">
				</div>
			</div>
			</form>
		</div>
		<div class="ui bottom attached tab segment" data-tab="lapKasir-third">
			<form class="ui form" id="lapKasir-form-beli" action="'.base_url().'index.php/lapKasir/filter_beli" method="post">
			<div class="ui grid">
				<div class="ten wide centered column" style="padding-bottom:0">
					<div class="fields">
						<div class="five wide field">
							<input type="text" name="lapKasir-beli-fromdate" id="lapKasir-beli-fromdate" readonly>
						</div>
						<div class="two wide field" style="text-align:center;margin-top:7px">
							<label>s.d</label>
						</div>
						<div class="five wide field">
							<input type="text" name="lapKasir-beli-todate" id="lapKasir-beli-todate" readonly>
						</div>
						<div class="four wide field">
							<select name="lapKasir-beli-detail_rekap" id="lapKasir-beli-detail_rekap" onchange=getDetailRekapBeli()>
								<option value="D">Detail</option>
								<option value="R">Rekap</option>
							</select>
						</div>
					</div>
					<div class="fields">
						<div class="six wide field" id="lapKasir-wrap_karat_beli">
							<select name="lapKasir-filter_karat_beli" id="lapKasir-filter_karat_beli">
								<option value="All">-- Seluruh Karat --</option>';
								foreach($karat as $k){
								$data['view'] .= '<option value="'.$k->id.'">'.$k->karat_name.'</option>';
								}
							$data['view'] .= '</select>
						</div>
						<div class="six wide field" id="lapKasir-wrap_rekap_beli">
							<select name="lapKasir-filter_rekap_beli" id="lapKasir-filter_rekap_beli">
								<option value="All">Semua</option>
								<option value="K">Per Karat</option>
								<option value="G">Per Kelompok</option>
							</select>
						</div>
						<div class="four wide field">
							<div class="ui fluid icon green button filter-input" id="lapKasir-btnfilterbeli" onclick=filterBeliKasir("lapKasir") title="Filter">
								<i class="filter icon"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="twelve wide centered column" id="lapKasir-wrap_beli">
				</div>
			</div>
			</form>
		</div>';
		
		$data["date"] = 4;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function filter_kas(){
		date_default_timezone_set("Asia/Jakarta");
		
		$tgl_transaksi =  $this->input->post('lapKasir-datekas');
		$tanggal_transaksi =  $this->input->post('lapKasir-datekas');
		$jenis_bayar =  $this->input->post('lapKasir-k-jenis_bayar');
		$detail_rekap =  $this->input->post('lapKasir-k-detail_rekap');
		
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
			
			$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui purple button" href="'.base_url().'index.php/lapKasir/lap_kasir_to_excel/'.$tanggal_transaksi.'/'.$jenis_bayar_link.'/'.$detail_rekap.'"><i class="file excel icon"></i> Export to Excel</a><a class="ui facebook button" href="'.base_url().'index.php/lapKasir/lap_kasir_to_pdf/'.$tanggal_transaksi.'/'.$jenis_bayar_link.'/'.$detail_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Transaksi Harian Detail</span><br><span>Tanggal '.$tanggal_transaksi.', Cabang '.$site_name.'</span></div><div class="sixteen wide centered column">';
			
			$data['view'] .= '<table class="ui celled table" cellspacing="0" width="100%"><thead><tr style="text-align:center"><th>Description</th><th>Voucher Code</th><th>Debit</th><th>Credit</th><th>Balance</th></tr></thead><tbody>';
			
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
			
			$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui purple button" href="'.base_url().'index.php/lapKasir/lap_kasir_to_excel/'.$tanggal_transaksi.'/'.$jenis_bayar_link.'/'.$detail_rekap.'"><i class="file excel icon"></i> Export to Excel</a><a class="ui facebook button" href="'.base_url().'index.php/lapKasir/lap_kasir_to_pdf/'.$tanggal_transaksi.'/'.$jenis_bayar_link.'/'.$detail_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Transaksi Harian Rekap</span><br><span>Tanggal '.$tanggal_transaksi.', Cabang '.$site_name.'</span><br></div><div class="sixteen wide centered column">';
			
			$data['view'] .= '<table class="ui celled table" class="table-report-detail" cellspacing="0" width="100%"><thead><tr style="text-align:center"><th>Description</th><th>Debit</th><th>Credit</th><th>Balance</th></tr></thead><tbody>';
			
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
		
		$tgl_from =  $this->input->post('lapKasir-jual-fromdate');
		$tanggal_from =  $this->input->post('lapKasir-jual-fromdate');
		$tglFrom = $this->date_to_format($tgl_from);
		$tgl_from = date("Y-m-d",$tglFrom).' 00:00:00';
		
		$tgl_to =  $this->input->post('lapKasir-jual-todate');
		$tanggal_to =  $this->input->post('lapKasir-jual-todate');
		$tglTo = $this->date_to_format($tgl_to);
		$tgl_to = date("Y-m-d",$tglTo).' 00:00:00';
		
		$detail_rekap = $this->input->post('lapKasir-jual-detail_rekap');
		
		$filter_box = $this->input->post('lapKasir-filter_box_jual');
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
		
		$filter_karat = $this->input->post('lapKasir-filter_karat_jual');
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
		
		$filter_rekap = $this->input->post('lapKasir-filter_rekap_jual');
		$site_name = $this->mm->get_site_name();
		
		if($detail_rekap == 'D'){
			$array_jual = array();
			$dj = $this->mt->get_penjualan_kasir($tgl_from,$tgl_to,$filter_box,$filter_karat);
			
			$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui blue button" href="'.base_url().'index.php/lapKasir/lap_jual_to_excel_tax/'.$tanggal_from.'/'.$tanggal_to.'"><i class="file excel icon"></i> Tax</a><a class="ui purple button" href="'.base_url().'index.php/lapKasir/lap_jual_to_excel/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$box_link.'/'.$karat_link.'/'.$filter_rekap.'"><i class="file excel icon"></i> Export to Excel</a><a class="ui facebook button" href="'.base_url().'index.php/lapKasir/lap_jual_to_pdf/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$box_link.'/'.$karat_link.'/'.$filter_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Penjualan Harian Detail</span><br><span>Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name.'</span><br></div><div class="sixteen wide centered column">';
			
			$data['view'] .= '<table class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:40px">No</th><th>ID Product</th><th>Box</th><th>Keterangan</th><th>Karat</th><th>Berat</th><th>Harga Jual</th><th>Total Jual</th></tr></thead><tbody>';
			
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
				
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui blue button" href="'.base_url().'index.php/lapKasir/lap_jual_to_excel_tax/'.$tanggal_from.'/'.$tanggal_to.'"><i class="file excel icon"></i> Tax</a><a class="ui purple button" href="'.base_url().'index.php/lapKasir/lap_jual_to_excel/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$box_link.'/'.$karat_link.'/'.$filter_rekap.'"><i class="file excel icon"></i> Export to Excel</a><a class="ui facebook button" href="'.base_url().'index.php/lapKasir/lap_jual_to_pdf/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$box_link.'/'.$karat_link.'/'.$filter_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Penjualan Harian Rekap / Karat</span><br><span>Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name.'</span><br></div><div class="sixteen wide centered column">';
				
				$data['view'] .= '<table class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:40px">No</th><th>Karat</th><th style="width:40px">Pcs</th><th style="width:80px">Gram</th><th style="width:120px">Rata2</th><th style="width:140px">Total Jual</th></tr></thead><tbody>';
			
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
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui blue button" href="'.base_url().'index.php/lapKasir/lap_jual_to_excel_tax/'.$tanggal_from.'/'.$tanggal_to.'"><i class="file excel icon"></i> Tax</a><a class="ui purple button" href="'.base_url().'index.php/lapKasir/lap_jual_to_excel/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$box_link.'/'.$karat_link.'/'.$filter_rekap.'"><i class="file excel icon"></i> Export to Excel</a><a class="ui facebook button" href="'.base_url().'index.php/lapKasir/lap_jual_to_pdf/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$box_link.'/'.$karat_link.'/'.$filter_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Penjualan Harian Rekap</span><br><span>Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name.'</span><br></div><div class="sixteen wide centered column">';
				
				$data['view'] .= '<div class="offset-lg-2 col-lg-8 offset-md-2 col-md-8">';
				
				$data['view'] .= '<table class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:40px">No</th><th>Tanggal</th><th>ID Transaksi</th><th>Nama Customer</th><th>Alamat Customer</th><th>Telepon Customer</th><th>Jumlah</th></tr></thead><tbody>';
				
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
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui blue button" href="'.base_url().'index.php/lapKasir/lap_jual_to_excel_tax/'.$tanggal_from.'/'.$tanggal_to.'"><i class="file excel icon"></i> Tax</a><a class="ui purple button" href="'.base_url().'index.php/lapKasir/lap_jual_to_excel/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$box_link.'/'.$karat_link.'/'.$filter_rekap.'"><i class="file excel icon"></i> Export to Excel</a><a class="ui facebook button" href="'.base_url().'index.php/lapKasir/lap_jual_to_pdf/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$box_link.'/'.$karat_link.'/'.$filter_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Penjualan Harian Rekap / Kelompok</span><br><span>Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name.'</span></div><div class="sixteen wide centered column">';
				
				$data['view'] .= '<table class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:40px">No</th><th>Karat</th><th style="width:40px">Pcs</th><th style="width:80px">Gram</th><th style="width:120px">Rata2</th><th style="width:140px">Total Jual</th></tr></thead><tbody>';
				
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
		
		$tgl_from =  $this->input->post('lapKasir-beli-fromdate');
		$tanggal_from =  $this->input->post('lapKasir-beli-fromdate');
		$tglFrom = $this->date_to_format($tgl_from);
		$tgl_from = date("Y-m-d",$tglFrom).' 00:00:00';
		
		$tgl_to =  $this->input->post('lapKasir-beli-todate');
		$tanggal_to =  $this->input->post('lapKasir-beli-todate');
		$tglTo = $this->date_to_format($tgl_to);
		$tgl_to = date("Y-m-d",$tglTo).' 00:00:00';
		
		$detail_rekap = $this->input->post('lapKasir-beli-detail_rekap');
		
		$filter_karat = $this->input->post('lapKasir-filter_karat_beli');
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
		
		$filter_rekap = $this->input->post('lapKasir-filter_rekap_beli');
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
			
			$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui blue button" href="'.base_url().'index.php/lapKasir/lap_beli_to_excel_tax/'.$tanggal_from.'/'.$tanggal_to.'"><i class="file excel icon"></i> Tax</a><a class="ui purple button" href="'.base_url().'index.php/lapKasir/lap_beli_to_excel/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$karat_link.'/'.$filter_rekap.'"><i class="file excel icon"></i> Export to Excel</a><a class="ui facebook button" href="'.base_url().'index.php/lapKasir/lap_beli_to_pdf/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$karat_link.'/'.$filter_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Pembelian Harian Detail</span><br><span>Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name.'</span></div><div class="sixteen wide centered column">';
			
			$data['view'] .= '<table class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:40px">No</th><th>Kelompok</th><th>Keterangan</th><th>Karat</th><th>Pcs</th><th>Berat</th><th>Harga Beli</th><th>Total Beli</th></tr></thead><tbody>';
			
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
				
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui blue button" href="'.base_url().'index.php/lapKasir/lap_beli_to_excel_tax/'.$tanggal_from.'/'.$tanggal_to.'"><i class="file excel icon"></i> Tax</a><a class="ui purple button" href="'.base_url().'index.php/lapKasir/lap_beli_to_excel/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$karat_link.'/'.$filter_rekap.'"><i class="file excel icon"></i> Export to Excel</a><a class="ui facebook button" href="'.base_url().'index.php/lapKasir/lap_beli_to_pdf/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$karat_link.'/'.$filter_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Pembelian Harian Rekap / Karat</span><br><span>Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name.'</span></div><div class="sixteen wide centered column">';
				
				$data['view'] .= '<div class="offset-lg-2 col-lg-8 offset-md-2 col-md-8">';
				
				$data['view'] .= '<table class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:40px">No</th><th>Karat</th><th style="width:40px">Pcs</th><th style="width:80px">Gram</th><th style="width:120px">Rata2</th><th style="width:140px">Total Beli</th></tr></thead><tbody>';
			
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
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui blue button" href="'.base_url().'index.php/lapKasir/lap_beli_to_excel_tax/'.$tanggal_from.'/'.$tanggal_to.'"><i class="file excel icon"></i> Tax</a><a class="ui purple button" href="'.base_url().'index.php/lapKasir/lap_beli_to_excel/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$karat_link.'/'.$filter_rekap.'"><i class="file excel icon"></i> Export to Excel</a><a class="ui facebook button" href="'.base_url().'index.php/lapKasir/lap_beli_to_pdf/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$karat_link.'/'.$filter_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Pembelian Harian Rekap</span><br><span>Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name.'</span></div><div class="sixteen wide centered column">';
				
				$data['view'] .= '<div class="offset-lg-2 col-lg-8 offset-md-2 col-md-8">';
				
				$data['view'] .= '<table class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:40px">No</th><th>Tanggal</th><th>ID Transaksi</th><th>Nama Customer</th><th>Alamat Customer</th><th>Telepon Customer</th><th>Jumlah</th></tr></thead><tbody>';
				
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
				$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui blue button" href="'.base_url().'index.php/lapKasir/lap_beli_to_excel_tax/'.$tanggal_from.'/'.$tanggal_to.'"><i class="file excel icon"></i> Tax</a><a class="ui purple button" href="'.base_url().'index.php/lapKasir/lap_beli_to_excel/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$karat_link.'/'.$filter_rekap.'"><i class="file excel icon"></i> Export to Excel</a><a class="ui facebook button" href="'.base_url().'index.php/lapKasir/lap_beli_to_pdf/'.$tanggal_from.'/'.$tanggal_to.'/'.$detail_rekap.'/'.$karat_link.'/'.$filter_rekap.'" target=_blank"><i class="paperclip icon"></i> Download</a></div><div class="sixteen wide centered column center aligned" style="font-weight:600"><span>Laporan Pembelian Harian Rekap / Karat</span><br><span>Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name.'</span><br><br></div><div class="sixteen wide centered column">';
				
				$data['view'] .= '<div class="offset-lg-2 col-lg-8 offset-md-2 col-md-8">';
				
				$data['view'] .= '<table class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:40px">No</th><th>Karat</th><th style="width:40px">Pcs</th><th style="width:80px">Gram</th><th style="width:120px">Rata2</th><th style="width:140px">Total Beli</th></tr></thead><tbody>';
				
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
		
		//$pdf = $this->m_pdf->load();
		$pdf = new \Mpdf\Mpdf();
		$pdf->setFooter('{PAGENO} / {nb}');
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right;font-family:samsungone;font-size:11px;">{PAGENO} / {nb}</div>');
		$pdf->AddPage('P');
        $pdf->WriteHTML($html);
		
        $pdf->Output("Laporan Kas Kasir.pdf", "I");
	}
	
	public function lap_kasir_to_excel($tgl_transaksi,$jenis_bayar,$detail_rekap){
		if($jenis_bayar == 'All'){
			$jenis_bayar = '';
		}
		$tgl_transaksi = str_replace('%20',' ',$tgl_transaksi);
		$tanggal_transaksi =  $tgl_transaksi;
		
		$tglTrans = $this->date_to_format($tgl_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		$site_name = $this->mm->get_site_name();
		
		//$this->load->library('Libexcel');
		$objPHPExcel = new Spreadsheet();
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setTitle($site_name);
		
		$style_font = array(
			'font'  => array(
				'name'  => 'Segoe UI'
			)
		);
		
		if($detail_rekap == 'D'){
			if($jenis_bayar == ''){
				$kas_account = $this->mm->get_all_kasbank_pos();
				$kas_ket = 'Seluruh Account';
			}else{
				$kas_account = $this->mm->get_single_coa_rp($jenis_bayar);
				$kas_ket = $this->mm->get_coa_number_name_2($jenis_bayar);
			}
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(45);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(29);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(17);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(19);
			
			$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:E3');
			$objPHPExcel->getActiveSheet()->getStyle('A1:E4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			
			$style_font_header = array(
				'font'  => array(
					'bold'  => true
				)
			);
			
			$objPHPExcel->getActiveSheet()->getStyle('A1:E4')->applyFromArray($style_font_header);
			
			$sheet->setCellValue('A1', 'Laporan Transaksi Harian Detail');
			$sheet->setCellValue('A2', 'Tanggal '.$tanggal_transaksi.', '.$kas_ket.', Cabang '.$site_name);
			
			$sheet->setCellValue('A4', 'Description');
			$sheet->setCellValue('B4', 'Voucher Code');
			$sheet->setCellValue('C4', 'Debit');
			$sheet->setCellValue('D4', 'Credit');
			$sheet->setCellValue('E4', 'Balance');
			
			$objPHPExcel->getActiveSheet()->getStyle("A4:E4")->applyFromArray(array(
				'borders' => array(
					'top' => array(
						'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
					),
					'bottom' => array(
						'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
					)
				)
			));
			
			$baris = 5;
			
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
					
					$objPHPExcel->getActiveSheet()->mergeCells('A'.$baris.':D'.$baris);
					
					$sheet->setCellValue('A'.$baris.'', $coa_data);
					$sheet->setCellValue('E'.$baris.'', $saldo_awal);
					
					$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':E'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('A'.$baris)->applyFromArray($style_font_header);
					
					$baris = $baris + 1;
					
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
								$debet_val = $mk->value;
								$kredit_val = 0;
								
								$running_balance = $running_balance + $mk->value;
								
								$rb = number_format($running_balance, 0);
								if($rb == -0){
									$running_balance = 0;
								}
								
								$total_debet = $total_debet + $mk->value;
							}else{
								$debet_val = 0;
								$kredit_val = $mk->value;
								
								$running_balance = $running_balance - $mk->value;
								
								$rb = number_format($running_balance, 0);
								if($rb == -0){
									$running_balance = 0;
								}
								
								$total_kredit = $total_kredit + $mk->value;
							}
							
							$sheet->setCellValue('A'.$baris.'', $mk->description);
							$sheet->setCellValue('B'.$baris.'', $idmutasi);
							$sheet->setCellValue('C'.$baris.'', $debet_val);
							$sheet->setCellValue('D'.$baris.'', $kredit_val);
							$sheet->setCellValue('E'.$baris.'', $running_balance);
							
							$objPHPExcel->getActiveSheet()->getStyle('C'.$baris.':E'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
							
							$baris = $baris + 1;
						}
					}
					
					$sheet->setCellValue('C'.$baris.'', $total_debet);
					$sheet->setCellValue('D'.$baris.'', $total_kredit);
					
					$objPHPExcel->getActiveSheet()->getStyle('C'.$baris.':D'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
					
					$objPHPExcel->getActiveSheet()->getStyle("C".$baris.":D".$baris)->applyFromArray(array(
						'borders' => array(
							'top' => array(
								'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
							)
						)
					));
					
					$baris = $baris + 2;
				}
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:E'.$baris.'')->applyFromArray($style_font);
				$objPHPExcel->getActiveSheet()->getStyle('A1:E'.$baris.'')->getAlignment()->setWrapText(true);
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
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(19);
			
			$objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
			$objPHPExcel->getActiveSheet()->getStyle('A1:D4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			
			$style_font_header = array(
				'font'  => array(
					'bold'  => true
				)
			);
			
			$objPHPExcel->getActiveSheet()->getStyle('A1:D4')->applyFromArray($style_font_header);
			
			$sheet->setCellValue('A1', 'Laporan Transaksi Harian Rekap');
			$sheet->setCellValue('A2', 'Tanggal '.$tanggal_transaksi.', '.$kas_ket.', Cabang '.$site_name);
			
			$sheet->setCellValue('A4', 'Description');
			$sheet->setCellValue('B4', 'Debit');
			$sheet->setCellValue('C4', 'Credit');
			$sheet->setCellValue('D4', 'Balance');
			
			$objPHPExcel->getActiveSheet()->getStyle("A4:D4")->applyFromArray(array(
				'borders' => array(
					'top' => array(
						'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
					),
					'bottom' => array(
						'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
					)
				)
			));
			
			$baris = 5;
			
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
					
					$objPHPExcel->getActiveSheet()->mergeCells('A'.$baris.':C'.$baris);
					$sheet->setCellValue('A'.$baris.'', $coa_data);
					$sheet->setCellValue('D'.$baris.'', $saldo_awal);
					
					$objPHPExcel->getActiveSheet()->getStyle('D'.$baris.':D'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('A'.$baris)->applyFromArray($style_font_header);
					
					$baris = $baris + 1;
					
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
						$sheet->setCellValue('A'.$baris.'', 'PENJUALAN');
						$sheet->setCellValue('B'.$baris.'', $total_debet_ind);
						$sheet->setCellValue('C'.$baris.'', $total_kredit_ind);
						$sheet->setCellValue('D'.$baris.'', $running_balance);
						
						$objPHPExcel->getActiveSheet()->getStyle('B'.$baris.':D'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
						
						$baris = $baris + 1;
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
						$sheet->setCellValue('A'.$baris.'', 'PENERIMAAN / PENGELUARAN KAS');
						$sheet->setCellValue('B'.$baris.'', $total_debet_ind);
						$sheet->setCellValue('C'.$baris.'', $total_kredit_ind);
						$sheet->setCellValue('D'.$baris.'', $running_balance);
						
						$objPHPExcel->getActiveSheet()->getStyle('B'.$baris.':D'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
						
						$baris = $baris + 1;
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
						$sheet->setCellValue('A'.$baris.'', 'PEMBELIAN');
						$sheet->setCellValue('B'.$baris.'', $total_debet_ind);
						$sheet->setCellValue('C'.$baris.'', $total_kredit_ind);
						$sheet->setCellValue('D'.$baris.'', $running_balance);
						
						$objPHPExcel->getActiveSheet()->getStyle('B'.$baris.':D'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
						
						$baris = $baris + 1;
					}
					//---
					
					$sheet->setCellValue('B'.$baris.'', $total_debet);
					$sheet->setCellValue('C'.$baris.'', $total_kredit);
					
					$objPHPExcel->getActiveSheet()->getStyle('B'.$baris.':C'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
					
					$objPHPExcel->getActiveSheet()->getStyle('B'.$baris.':C'.$baris)->applyFromArray(array(
						'borders' => array(
							'top' => array(
								'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
							)
						)
					));
					
					$baris = $baris + 2;
				}
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:D'.$baris.'')->applyFromArray($style_font);
				$objPHPExcel->getActiveSheet()->getStyle('A1:D'.$baris.'')->getAlignment()->setWrapText(true);
				
				/*----------------------------------------------------*/
			}
		}
		
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');

		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="EXCEL TRANSAKSI HARIAN '.$site_name.' TANGGAL '.$tanggal_transaksi.'.xlsx"');
		//unduh file
		$objWriter->save("php://output");
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
				
				$data['view'] .= '<table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:40px" class="th-5">No</th><th class="th-5">Karat</th><th style="width:40px" class="th-5">Pcs</th><th style="width:80px" class="th-5">Gram</th><th style="width:120px" class="th-5">Rata2</th><th style="width:140px" class="th-5">Total Jual</th></tr></thead><tbody>';
				
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
		
		//$pdf = $this->m_pdf->load();
		$pdf = new \Mpdf\Mpdf();
		$pdf->setFooter('{PAGENO} / {nb}');
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right;font-family:samsungone;font-size:11px;">{PAGENO} / {nb}</div>');
		$pdf->AddPage('P');
        $pdf->WriteHTML($html);
		
        $pdf->Output("Laporan Penjualan.pdf", "I");
	}
	
	public function lap_jual_to_excel($tgl_from,$tgl_to,$detail_rekap,$filter_box,$filter_karat,$filter_rekap){
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
		
		//$this->load->library('Libexcel');
		$objPHPExcel = new Spreadsheet();
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setTitle($site_name);
		
		$style_font = array(
			'font'  => array(
				'name'  => 'Segoe UI'
			)
		);
		
		if($detail_rekap == 'D'){
			$array_jual = array();
			$dj = $this->mt->get_penjualan_kasir($tgl_from,$tgl_to,$filter_box,$filter_karat);
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(16);
			
			$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');
			$objPHPExcel->getActiveSheet()->getStyle('A1:H5')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			
			$style_font_header = array(
				'font'  => array(
					'bold'  => true
				)
			);
			
			$objPHPExcel->getActiveSheet()->getStyle('A1:H5')->applyFromArray($style_font_header);
			
			$sheet->setCellValue('A1', 'Laporan Penjualan Harian Detail');
			$sheet->setCellValue('A2', 'Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name);
			$sheet->setCellValue('A3', $box_tulis.', '.$karat_tulis);
			
			$sheet->setCellValue('A5', 'No');
			$sheet->setCellValue('B5', 'ID Product');
			$sheet->setCellValue('C5', 'Box');
			$sheet->setCellValue('D5', 'Keterangan');
			$sheet->setCellValue('E5', 'Karat');
			$sheet->setCellValue('F5', 'Berat');
			$sheet->setCellValue('G5', 'Harga Jual');
			$sheet->setCellValue('H5', 'Total Jual');
			
			$objPHPExcel->getActiveSheet()->getStyle("A5:H5")->applyFromArray(array(
				'borders' => array(
					'top' => array(
						'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
					),
					'bottom' => array(
						'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
					)
				)
			));
			
			$baris = 6;
			
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
					
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$baris.':E'.$baris);
					$objPHPExcel->getActiveSheet()->mergeCells('F'.$baris.':H'.$baris);
					
					$sheet->setCellValue('A'.$baris.'', $number);
					$sheet->setCellValue('B'.$baris.'', $trans_date.' | '.$id_trans);
					$sheet->setCellValue('F'.$baris.'', 'Customer Service : '.strtoupper($cust_service));
					
					$objPHPExcel->getActiveSheet()->getStyle('B'.$baris.':B'.$baris)->applyFromArray($style_font_header);
					
					$number = $number + 1;
					$baris = $baris + 1;
					
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
					
					$sheet->setCellValue('B'.$baris.'', $dj[$i]->id_product);
					$sheet->setCellValue('C'.$baris.'', $box_number);
					$sheet->setCellValue('D'.$baris.'', $dj[$i]->product_desc);
					$sheet->setCellValue('E'.$baris.'', $dj[$i]->karat_name);
					$sheet->setCellValue('F'.$baris.'', $dj[$i]->product_weight);
					$sheet->setCellValue('G'.$baris.'', $dj[$i]->product_price/$dj[$i]->product_weight);
					$sheet->setCellValue('H'.$baris.'', $dj[$i]->product_price);
					
					$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('H'.$baris.':H'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('C'.$baris.':C'.$baris.'')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':E'.$baris.'')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
					
					$baris = $baris + 1;
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
						
						$sheet->setCellValue('B'.$baris.'', $dj[$i]->id_product);
						$sheet->setCellValue('C'.$baris.'', $box_number);
						$sheet->setCellValue('D'.$baris.'', $dj[$i]->product_desc);
						$sheet->setCellValue('E'.$baris.'', $dj[$i]->karat_name);
						$sheet->setCellValue('F'.$baris.'', $dj[$i]->product_weight);
						$sheet->setCellValue('G'.$baris.'', $dj[$i]->product_price/$dj[$i]->product_weight);
						$sheet->setCellValue('H'.$baris.'', $dj[$i]->product_price);
						
						$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('H'.$baris.':H'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('C'.$baris.':C'.$baris.'')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':E'.$baris.'')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
						
						$total_temp = $total_temp + $dj[$i]->product_price;
						
						$baris = $baris + 1;
						$total_pcs_all = $total_pcs_all + 1;
						$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
						$total_jual_all = $total_jual_all + $dj[$i]->product_price;
					}else{
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$baris.':E'.$baris);
						$objPHPExcel->getActiveSheet()->mergeCells('F'.$baris.':H'.$baris);
						
						$sheet->setCellValue('F'.$baris.'', $total_temp);
						
						$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.'')->applyFromArray($style_font_header);
						$objPHPExcel->getActiveSheet()->getStyle("F".$baris.":H".$baris)->applyFromArray(array(
							'borders' => array(
								'top' => array(
									'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
								)
							)
						));
						
						$total_temp = 0;
						
						$baris = $baris + 1;
						$trans_date = strtotime($dj[$i]->trans_date);
						$trans_date = date('d-M-Y',$trans_date);
						$trans_temp = $id_trans;
						$cust_service = $this->mt->get_cs_jual_by_id($id_trans);
						
						$objPHPExcel->getActiveSheet()->mergeCells('B'.$baris.':E'.$baris);
						$objPHPExcel->getActiveSheet()->mergeCells('F'.$baris.':H'.$baris);
						
						$sheet->setCellValue('A'.$baris.'', $number);
						$sheet->setCellValue('B'.$baris.'', $trans_date.' | '.$id_trans);
						$sheet->setCellValue('F'.$baris.'', 'Customer Service : '.strtoupper($cust_service));
						
						$objPHPExcel->getActiveSheet()->getStyle('B'.$baris.':B'.$baris)->applyFromArray($style_font_header);
						
						$baris = $baris + 1;
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
						
						$sheet->setCellValue('B'.$baris.'', $dj[$i]->id_product);
						$sheet->setCellValue('C'.$baris.'', $box_number);
						$sheet->setCellValue('D'.$baris.'', $dj[$i]->product_desc);
						$sheet->setCellValue('E'.$baris.'', $dj[$i]->karat_name);
						$sheet->setCellValue('F'.$baris.'', $dj[$i]->product_weight);
						$sheet->setCellValue('G'.$baris.'', $dj[$i]->product_price/$dj[$i]->product_weight);
						$sheet->setCellValue('H'.$baris.'', $dj[$i]->product_price);
						
						$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('H'.$baris.':H'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('C'.$baris.':C'.$baris.'')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':E'.$baris.'')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
						
						$total_temp = $total_temp + $dj[$i]->product_price;
						
						$baris = $baris + 1;
						$total_pcs_all = $total_pcs_all + 1;
						$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
						$total_jual_all = $total_jual_all + $dj[$i]->product_price;
					}
				}
			}
			
			if($length != 0){
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$baris.':E'.$baris);
				$objPHPExcel->getActiveSheet()->mergeCells('F'.$baris.':H'.$baris);
						
				$sheet->setCellValue('F'.$baris.'', $total_temp);
				
				$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
				$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.'')->applyFromArray($style_font_header);
				$objPHPExcel->getActiveSheet()->getStyle("F".$baris.":H".$baris)->applyFromArray(array(
					'borders' => array(
						'top' => array(
							'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
						)
					)
				));
				
				$baris = $baris + 2;
				
				$sheet->setCellValue('E'.$baris.'', $total_pcs_all.' Pcs');
				$sheet->setCellValue('F'.$baris.'', $total_gram_all);
				$sheet->setCellValue('H'.$baris.'', $total_jual_all);
				
				$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
				$objPHPExcel->getActiveSheet()->getStyle('H'.$baris.':H'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
				$objPHPExcel->getActiveSheet()->getStyle('H'.$baris.'')->applyFromArray($style_font_header);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':E'.$baris.'')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle("E".$baris.":H".$baris)->applyFromArray(array(
					'borders' => array(
						'top' => array(
							'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
						)
					)
				));
			}
			
			$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$baris.'')->applyFromArray($style_font);
			$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$baris.'')->getAlignment()->setWrapText(true);
		}else if($detail_rekap == 'R'){
			if($filter_rekap == 'K'){
				$number = 1;
				$total_pcs = 0;
				$total_gram = 0;
				$total_jual = 0;
				
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(14);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
				
				$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
				$objPHPExcel->getActiveSheet()->mergeCells('A3:F3');
				$objPHPExcel->getActiveSheet()->getStyle('A1:F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$style_font_header = array(
					'font'  => array(
						'bold'  => true
					)
				);
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:F4')->applyFromArray($style_font_header);
				
				$sheet->setCellValue('A1', 'Laporan Penjualan Harian Rekap / Karat');
				$sheet->setCellValue('A2', 'Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name);
				
				$sheet->setCellValue('A4', 'No');
				$sheet->setCellValue('B4', 'Karat');
				$sheet->setCellValue('C4', 'Pcs');
				$sheet->setCellValue('D4', 'Gram');
				$sheet->setCellValue('E4', 'Rata2');
				$sheet->setCellValue('F4', 'Total Jual');
				
				$objPHPExcel->getActiveSheet()->getStyle("A4:F4")->applyFromArray(array(
					'borders' => array(
						'top' => array(
							'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						),
						'bottom' => array(
							'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						)
					)
				));
				
				$baris = 5;
				
				$data_rekap = $this->mt->get_penjualan_rekap_karat($tgl_from,$tgl_to);
				foreach($data_rekap as $dr){
					$sheet->setCellValue('A'.$baris.'', $number);
					$sheet->setCellValue('B'.$baris.'', $dr->karat_name);
					$sheet->setCellValue('C'.$baris.'', $dr->pcs);
					$sheet->setCellValue('D'.$baris.'', $dr->berat);
					$sheet->setCellValue('E'.$baris.'', $dr->harga/$dr->berat);
					$sheet->setCellValue('F'.$baris.'', $dr->harga);
					
					$objPHPExcel->getActiveSheet()->getStyle('D'.$baris.':D'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':E'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('B'.$baris.':B'.$baris.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$baris = $baris + 1;
					
					$total_pcs = $total_pcs + $dr->pcs;
					$total_gram = $total_gram + $dr->berat;
					$total_jual = $total_jual + $dr->harga;
					$number = $number + 1;
				}
				
				if($total_pcs != 0){
					$sheet->setCellValue('C'.$baris.'', $total_pcs);
					$sheet->setCellValue('D'.$baris.'', $total_gram);
					$sheet->setCellValue('F'.$baris.'', $total_jual);
					
					$objPHPExcel->getActiveSheet()->getStyle('D'.$baris.':D'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle("C".$baris.":F".$baris)->applyFromArray(array(
						'borders' => array(
							'top' => array(
								'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
							)
						)
					));
					$objPHPExcel->getActiveSheet()->getStyle('C'.$baris.':F'.$baris.'')->applyFromArray($style_font_header);
					$baris = $baris + 1;
				}
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$baris.'')->applyFromArray($style_font);
				$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$baris.'')->getAlignment()->setWrapText(true);
			}else if($filter_rekap == 'All'){
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(28);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(28);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(28);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(17);
				
				$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
				$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
				$objPHPExcel->getActiveSheet()->getStyle('A1:G4')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				
				$style_font_header = array(
					'font'  => array(
						'bold'  => true
					)
				);
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:G4')->applyFromArray($style_font_header);
				
				$sheet->setCellValue('A1', 'Laporan Penjualan Harian Rekap');
				$sheet->setCellValue('A2', 'Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name);
				
				$sheet->setCellValue('A4', 'No');
				$sheet->setCellValue('B4', 'Tanggal');
				$sheet->setCellValue('C4', 'ID Transaksi');
				$sheet->setCellValue('D4', 'Nama Customer');
				$sheet->setCellValue('E4', 'Alamat Customer');
				$sheet->setCellValue('F4', 'Telepon Customer');
				$sheet->setCellValue('G4', 'Jumlah');
				
				$objPHPExcel->getActiveSheet()->getStyle("A4:G4")->applyFromArray(array(
					'borders' => array(
						'top' => array(
							'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						),
						'bottom' => array(
							'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						)
					)
				));
				
				$baris = 5;
				
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
						
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$baris.':G'.$baris.'');
						$sheet->setCellValue('A'.$baris.'', $coa_data);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$baris.':A'.$baris.'')->applyFromArray($style_font_header);
						$baris = $baris + 1;
						
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
									
									$sheet->setCellValue('A'.$baris.'', $number);
									$sheet->setCellValue('B'.$baris.'', $trans_date);
									$sheet->setCellValue('C'.$baris.'', $idmutasi);
									$sheet->setCellValue('D'.$baris.'', $customer_name);
									$sheet->setCellValue('E'.$baris.'', $customer_address);
									$sheet->setCellValue('F'.$baris.'', $customer_phone);
									$sheet->setCellValue('G'.$baris.'', $mk->value);
									
									$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
									
									$baris = $baris + 1;
									
									$total_debet = $total_debet + $mk->value;
									$running_balance = $running_balance + $mk->value;
									$number = $number + 1;
								}
							}
						}
						
						if($total_debet != 0){
							$objPHPExcel->getActiveSheet()->mergeCells('A'.$baris.':F'.$baris.'');
							$sheet->setCellValue('G'.$baris.'', $total_debet);
							$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
							$objPHPExcel->getActiveSheet()->getStyle("G".$baris.":G".$baris)->applyFromArray(array(
								'borders' => array(
									'top' => array(
										'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
									)
								)
							));
							$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->applyFromArray($style_font_header);
							$baris = $baris + 1;
						}
						//---
					}
					
					/*----------------------------------------------------*/
				}
				
				if($running_balance != 0){
					$baris = $baris + 1;
					$sheet->setCellValue('G'.$baris.'', $running_balance);
					$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle("G".$baris.":G".$baris)->applyFromArray(array(
						'borders' => array(
							'top' => array(
								'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
							)
						)
					));
					$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->applyFromArray($style_font_header);
				}
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$baris.'')->applyFromArray($style_font);
				$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$baris.'')->getAlignment()->setWrapText(true);
			}else if($filter_rekap == 'G'){
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(14);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
				
				$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
				$objPHPExcel->getActiveSheet()->mergeCells('A3:F3');
				$objPHPExcel->getActiveSheet()->getStyle('A1:F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$style_font_header = array(
					'font'  => array(
						'bold'  => true
					)
				);
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:F4')->applyFromArray($style_font_header);
				
				$sheet->setCellValue('A1', 'Laporan Penjualan Harian Rekap / Kelompok');
				$sheet->setCellValue('A2', 'Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name);
				
				$sheet->setCellValue('A4', 'No');
				$sheet->setCellValue('B4', 'Karat');
				$sheet->setCellValue('C4', 'Pcs');
				$sheet->setCellValue('D4', 'Gram');
				$sheet->setCellValue('E4', 'Rata2');
				$sheet->setCellValue('F4', 'Total Jual');
				
				$objPHPExcel->getActiveSheet()->getStyle("A4:F4")->applyFromArray(array(
					'borders' => array(
						'top' => array(
							'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						),
						'bottom' => array(
							'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						)
					)
				));
				
				$baris = 5;
				
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
						$sheet->setCellValue('A'.$baris.'', $number);
						$sheet->setCellValue('B'.$baris.'', $dk->karat_name);
						
						$objPHPExcel->getActiveSheet()->getStyle('B'.$baris.':B'.$baris.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->mergeCells('C'.$baris.':F'.$baris.'');
						
						$objPHPExcel->getActiveSheet()->getStyle('A'.$baris.':B'.$baris.'')->applyFromArray($style_font_header);
						
						$baris = $baris + 1;
						
						foreach($data_rekap as $dr){
							$sheet->setCellValue('B'.$baris.'', $dr->category_name);
							$sheet->setCellValue('C'.$baris.'', $dr->pcs);
							$sheet->setCellValue('D'.$baris.'', $dr->berat);
							$sheet->setCellValue('E'.$baris.'', $dr->harga/$dr->berat);
							$sheet->setCellValue('F'.$baris.'', $dr->harga);
							
							$objPHPExcel->getActiveSheet()->getStyle('D'.$baris.':D'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
							$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':E'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');
							$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
							$objPHPExcel->getActiveSheet()->getStyle('B'.$baris.':B'.$baris.'')->applyFromArray($style_font_header);
							
							$baris = $baris + 1;
							
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
						$sheet->setCellValue('C'.$baris.'', $total_pcs);
						$sheet->setCellValue('D'.$baris.'', $total_gram);
						$sheet->setCellValue('F'.$baris.'', $total_jual);
						
						$objPHPExcel->getActiveSheet()->getStyle('D'.$baris.':D'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
						
						$objPHPExcel->getActiveSheet()->getStyle("C".$baris.":F".$baris)->applyFromArray(array(
							'borders' => array(
								'top' => array(
									'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
								)
							)
						));
						$objPHPExcel->getActiveSheet()->getStyle('C'.$baris.':F'.$baris.'')->applyFromArray($style_font_header);
						
						$baris = $baris + 1;
					}
				}
				
				if($total_pcs_all != 0){
					$baris = $baris + 1;
					
					$sheet->setCellValue('C'.$baris.'', $total_pcs_all);
					$sheet->setCellValue('D'.$baris.'', $total_gram_all);
					$sheet->setCellValue('F'.$baris.'', $total_jual_all);
					
					$objPHPExcel->getActiveSheet()->getStyle('D'.$baris.':D'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
					
					$objPHPExcel->getActiveSheet()->getStyle("C".$baris.":F".$baris)->applyFromArray(array(
						'borders' => array(
							'top' => array(
								'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
							)
						)
					));
					$objPHPExcel->getActiveSheet()->getStyle('C'.$baris.':F'.$baris.'')->applyFromArray($style_font_header);
				}
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$baris.'')->applyFromArray($style_font);
				$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$baris.'')->getAlignment()->setWrapText(true);
			}
		}
		
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');

		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="EXCEL PENJUALAN '.$site_name.' TANGGAL '.$tanggal_from.' SD '.$tanggal_to.'.xlsx"');
		//unduh file
		$objWriter->save("php://output");
	}
	
	public function lap_jual_to_excel_tax($tgl_from,$tgl_to){
		date_default_timezone_set("Asia/Jakarta");
		$tgl_from = str_replace('%20',' ',$tgl_from);
		$tgl_to = str_replace('%20',' ',$tgl_to);
		
		$tanggal_from =  $tgl_from;
		$tglFrom = $this->date_to_format($tgl_from);
		$tgl_from = date("Y-m-d",$tglFrom).' 00:00:00';
		
		$tanggal_to =  $tgl_to;
		$tglTo = $this->date_to_format($tgl_to);
		$tgl_to = date("Y-m-d",$tglTo).' 23:59:59';
		
		$site_name = $this->mm->get_site_name();
		
		$data_karat = $this->mk->get_karat_srt();
		$total_karat = count($data_karat);
		
		$array_jual = array();
		
		//$this->load->library('Libexcel');
		$objPHPExcel = new Spreadsheet();
		$sheet = $objPHPExcel->getActiveSheet();
		
		$style_font = array(
			'font'  => array(
				'name'  => 'Segoe UI'
			)
		);
		
		$i = 0;
		
		while($i < $total_karat){
			$sheet = $objPHPExcel->createSheet($i);
			$karat_name = $data_karat[$i]->karat_name;
			$karat_id = $data_karat[$i]->id;
			$sheet->setTitle($karat_name);
			
			$data_jual = $this->mt->get_rekap_karat_excel($karat_id,$tgl_from,$tgl_to);
			
			$sheet->getColumnDimension('A')->setWidth(25);
			$sheet->getColumnDimension('B')->setWidth(20);
			$sheet->getColumnDimension('C')->setWidth(20);
			$sheet->getColumnDimension('D')->setWidth(25);
			
			$sheet
				->mergeCells('A1:D1')
				->mergeCells('A2:D2');
			
			$sheet
				->setCellValue('A1','LAPORAN PENJUALAN EMAS')
				->setCellValue('A2','CABANG : '.$site_name)
				->setCellValue('A6','KARAT : ')
				->setCellValue('B6',$karat_name)
				->setCellValue('A8','Tanggal')
				->setCellValue('B8','KETERANGAN')
				->setCellValue('C8','Gram')
				->setCellValue('D8','Rp');
			
			$sheet->getStyle('B6')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			
			$style_font_header = array(
				'font'  => array(
					'bold'  => true
				)
			);
			
			$sheet->getStyle('A1:D8')->applyFromArray($style_font_header);
			
			$sheet->getStyle("A8:D8")->applyFromArray(array(
				'borders' => array(
					'top' => array(
						'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
					),
					'bottom' => array(
						'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
					)
				)
			));
			
			$sheet->getStyle('A8:D8')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			
			$baris = 10;
			
			foreach($data_jual as $dj){
				$trans_date = strtotime($dj->trans_date);
				$trans_tgl = date('d',$trans_date);
				$trans_bln = date('m',$trans_date);
				$trans_thn = date('Y',$trans_date);
				
				$trans_bln = (int)$trans_bln;
				
				switch($trans_bln){
					case 1:
						$bulankini = 'Januari';
						break;
					case 2:
						$bulankini = 'Februari';
						break;
					case 3:
						$bulankini = 'Maret';
						break;
					case 4:
						$bulankini = 'April';
						break;
					case 5:
						$bulankini = 'May';
						break;
					case 6:
						$bulankini = 'Juni';
						break;
					case 7:
						$bulankini = 'Juli';
						break;
					case 8:
						$bulankini = 'Agustus';
						break;
					case 9:
						$bulankini = 'September';
						break;
					case 10:
						$bulankini = 'Oktober';
						break;
					case 11:
						$bulankini = 'November';
						break;
					case 12:
						$bulankini = 'Desember';
						break;
				}
				
				$sheet
				->setCellValue('A'.$baris, $trans_tgl.' '.$bulankini.' '.$trans_thn)
				->setCellValue('B'.$baris, 'PENJUALAN')
				->setCellValue('C'.$baris, $dj->berat)
				->setCellValue('D'.$baris, $dj->harga);
				
				$baris = $baris + 1;
			}
			
			$sheet->getStyle('C10:C'.$baris)->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
			$sheet->getStyle('D10:D'.$baris)->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
			$sheet->getStyle('A1:D'.$baris.'')->applyFromArray($style_font);
			$sheet->getStyle('A1:D'.$baris.'')->getAlignment()->setWrapText(true);
			
			$i++;
		}
		
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');

		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="EXCEL PENJUALAN TAX '.$site_name.' TANGGAL '.$tanggal_from.' SD '.$tanggal_to.'.xlsx"');
		//unduh file
		$objWriter->save("php://output");
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
		
		//$pdf = $this->m_pdf->load();
		$pdf = new \Mpdf\Mpdf();
		$pdf->setFooter('{PAGENO} / {nb}');
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right;font-family:samsungone;font-size:11px;">{PAGENO} / {nb}</div>');
		$pdf->AddPage('P');
        $pdf->WriteHTML($html);
		
        $pdf->Output("Laporan Pembelian.pdf", "I");
	}
	
	public function lap_beli_to_excel($tgl_from,$tgl_to,$detail_rekap,$filter_karat,$filter_rekap){
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
		
		//$this->load->library('Libexcel');
		$objPHPExcel = new Spreadsheet();
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setTitle($site_name);
		
		$style_font = array(
			'font'  => array(
				'name'  => 'Segoe UI'
			)
		);
		
		if($detail_rekap == 'D'){
			$array_jual = array();
			$dj = $this->mt->get_pembelian_kasir($tgl_from,$tgl_to,$filter_karat);
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(16);
			
			$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');
			$objPHPExcel->getActiveSheet()->getStyle('A1:H5')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			
			$style_font_header = array(
				'font'  => array(
					'bold'  => true
				)
			);
			
			$objPHPExcel->getActiveSheet()->getStyle('A1:H5')->applyFromArray($style_font_header);
			
			$sheet->setCellValue('A1', 'Laporan Pembelian Harian Detail');
			$sheet->setCellValue('A2', 'Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name);
			$sheet->setCellValue('A3', $karat_tulis);
			
			$sheet->setCellValue('A5', 'No');
			$sheet->setCellValue('B5', 'Kelompok');
			$sheet->setCellValue('C5', 'Keterangan');
			$sheet->setCellValue('D5', 'Karat');
			$sheet->setCellValue('E5', 'Pcs');
			$sheet->setCellValue('F5', 'Berat');
			$sheet->setCellValue('G5', 'Harga Beli');
			$sheet->setCellValue('H5', 'Total Beli');
			
			$objPHPExcel->getActiveSheet()->getStyle("A5:H5")->applyFromArray(array(
				'borders' => array(
					'top' => array(
						'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
					),
					'bottom' => array(
						'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
					)
				)
			));
			
			$baris = 6;
			
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
					
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$baris.':E'.$baris);
					$objPHPExcel->getActiveSheet()->mergeCells('F'.$baris.':H'.$baris);
					
					$sheet->setCellValue('A'.$baris.'', $number);
					$sheet->setCellValue('B'.$baris.'', $trans_date.' | '.$id_trans);
					$sheet->setCellValue('F'.$baris.'', 'Customer Service : '.strtoupper($cust_service));
					
					$objPHPExcel->getActiveSheet()->getStyle('B'.$baris.':B'.$baris)->applyFromArray($style_font_header);
					
					$baris = $baris + 1;
					$number = $number + 1;
					
					$sheet->setCellValue('B'.$baris.'', $dj[$i]->category_name);
					$sheet->setCellValue('C'.$baris.'', $dj[$i]->nama_product);
					$sheet->setCellValue('D'.$baris.'', $dj[$i]->karat_name);
					$sheet->setCellValue('E'.$baris.'', $dj[$i]->product_pcs);
					$sheet->setCellValue('F'.$baris.'', $dj[$i]->product_weight);
					$sheet->setCellValue('G'.$baris.'', $dj[$i]->product_price/$dj[$i]->product_weight);
					$sheet->setCellValue('H'.$baris.'', $dj[$i]->product_price);
					
					$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('H'.$baris.':H'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('D'.$baris.':D'.$baris.'')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
					
					$baris = $baris + 1;
					
					$total_pcs_all = $total_pcs_all + $dj[$i]->product_pcs;
					$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
					$total_jual_all = $total_jual_all + $dj[$i]->product_price;
					
					$total_pcs_temp = $total_pcs_temp + $dj[$i]->product_pcs;
					$total_gram_temp = $total_gram_temp + $dj[$i]->product_weight;
					$total_temp = $total_temp + $dj[$i]->product_price;
				}else{
					if($dj[$i]->transaction_code == $trans_temp){
						
						$sheet->setCellValue('B'.$baris.'', $dj[$i]->category_name);
						$sheet->setCellValue('C'.$baris.'', $dj[$i]->nama_product);
						$sheet->setCellValue('D'.$baris.'', $dj[$i]->karat_name);
						$sheet->setCellValue('E'.$baris.'', $dj[$i]->product_pcs);
						$sheet->setCellValue('F'.$baris.'', $dj[$i]->product_weight);
						$sheet->setCellValue('G'.$baris.'', $dj[$i]->product_price/$dj[$i]->product_weight);
						$sheet->setCellValue('H'.$baris.'', $dj[$i]->product_price);
						
						$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('H'.$baris.':H'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('D'.$baris.':D'.$baris.'')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
						
						$baris = $baris + 1;
						
						$total_pcs_all = $total_pcs_all + $dj[$i]->product_pcs;
						$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
						$total_jual_all = $total_jual_all + $dj[$i]->product_price;
						
						$total_pcs_temp = $total_pcs_temp + $dj[$i]->product_pcs;
						$total_gram_temp = $total_gram_temp + $dj[$i]->product_weight;
						$total_temp = $total_temp + $dj[$i]->product_price;
					}else{
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$baris.':D'.$baris);
						
						$sheet->setCellValue('E'.$baris.'', $total_pcs_temp);
						$sheet->setCellValue('F'.$baris.'', $total_gram_temp);
						$sheet->setCellValue('H'.$baris.'', $total_temp);
						
						$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':E'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('H'.$baris.':H'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.'')->applyFromArray($style_font_header);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.'')->applyFromArray($style_font_header);
						$objPHPExcel->getActiveSheet()->getStyle('H'.$baris.'')->applyFromArray($style_font_header);
						$objPHPExcel->getActiveSheet()->getStyle("E".$baris.":H".$baris)->applyFromArray(array(
							'borders' => array(
								'top' => array(
									'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
								)
							)
						));
						
						$baris = $baris + 1;
						
						$total_temp = 0;
						$total_pcs_temp = 0;
						$total_gram_temp = 0;
						
						$trans_date = strtotime($dj[$i]->trans_date);
						$trans_date = date('d-M-Y',$trans_date);
						$trans_temp = $id_trans;
						
						$cust_service = $this->mt->get_cs_beli_by_id($id_trans);
					
						$objPHPExcel->getActiveSheet()->mergeCells('B'.$baris.':E'.$baris);
						$objPHPExcel->getActiveSheet()->mergeCells('F'.$baris.':H'.$baris);
						
						$sheet->setCellValue('A'.$baris.'', $number);
						$sheet->setCellValue('B'.$baris.'', $trans_date.' | '.$id_trans);
						$sheet->setCellValue('F'.$baris.'', 'Customer Service : '.strtoupper($cust_service));
						
						$objPHPExcel->getActiveSheet()->getStyle('B'.$baris.':B'.$baris)->applyFromArray($style_font_header);
						
						$baris = $baris + 1;
						$number = $number + 1;
						
						$sheet->setCellValue('B'.$baris.'', $dj[$i]->category_name);
						$sheet->setCellValue('C'.$baris.'', $dj[$i]->nama_product);
						$sheet->setCellValue('D'.$baris.'', $dj[$i]->karat_name);
						$sheet->setCellValue('E'.$baris.'', $dj[$i]->product_pcs);
						$sheet->setCellValue('F'.$baris.'', $dj[$i]->product_weight);
						$sheet->setCellValue('G'.$baris.'', $dj[$i]->product_price/$dj[$i]->product_weight);
						$sheet->setCellValue('H'.$baris.'', $dj[$i]->product_price);
						
						$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('H'.$baris.':H'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('D'.$baris.':D'.$baris.'')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
						
						$baris = $baris + 1;
						
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
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$baris.':D'.$baris);
				
				$sheet->setCellValue('E'.$baris.'', $total_pcs_temp);
				$sheet->setCellValue('F'.$baris.'', $total_gram_temp);
				$sheet->setCellValue('H'.$baris.'', $total_temp);
				
				$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':E'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
				$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
				$objPHPExcel->getActiveSheet()->getStyle('H'.$baris.':H'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
				$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.'')->applyFromArray($style_font_header);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.'')->applyFromArray($style_font_header);
				$objPHPExcel->getActiveSheet()->getStyle('H'.$baris.'')->applyFromArray($style_font_header);
				$objPHPExcel->getActiveSheet()->getStyle("E".$baris.":H".$baris)->applyFromArray(array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_DOUBLE
						)
					)
				));
				
				$baris = $baris + 2;
				
				$sheet->setCellValue('E'.$baris.'', $total_pcs_all);
				$sheet->setCellValue('F'.$baris.'', $total_gram_all);
				$sheet->setCellValue('H'.$baris.'', $total_jual_all);
				
				$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':E'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
				$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
				$objPHPExcel->getActiveSheet()->getStyle('H'.$baris.':H'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
				$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.'')->applyFromArray($style_font_header);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.'')->applyFromArray($style_font_header);
				$objPHPExcel->getActiveSheet()->getStyle('H'.$baris.'')->applyFromArray($style_font_header);
				
				$objPHPExcel->getActiveSheet()->getStyle("E".$baris.":H".$baris)->applyFromArray(array(
					'borders' => array(
						'top' => array(
							'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
						)
					)
				));
			}
			
			$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$baris.'')->applyFromArray($style_font);
			$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$baris.'')->getAlignment()->setWrapText(true);
		}else if($detail_rekap == 'R'){
			if($filter_rekap == 'K'){
				$number = 1;
				$total_pcs = 0;
				$total_gram = 0;
				$total_jual = 0;
				
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(14);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
				
				$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
				$objPHPExcel->getActiveSheet()->mergeCells('A3:F3');
				$objPHPExcel->getActiveSheet()->getStyle('A1:F4')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				
				$style_font_header = array(
					'font'  => array(
						'bold'  => true
					)
				);
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:F4')->applyFromArray($style_font_header);
				
				$sheet->setCellValue('A1', 'Laporan Pembelian Harian Rekap / Karat');
				$sheet->setCellValue('A2', 'Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name);
				
				$sheet->setCellValue('A4', 'No');
				$sheet->setCellValue('B4', 'Karat');
				$sheet->setCellValue('C4', 'Pcs');
				$sheet->setCellValue('D4', 'Gram');
				$sheet->setCellValue('E4', 'Rata2');
				$sheet->setCellValue('F4', 'Total Beli');
				
				$objPHPExcel->getActiveSheet()->getStyle("A4:F4")->applyFromArray(array(
					'borders' => array(
						'top' => array(
							'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						),
						'bottom' => array(
							'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						)
					)
				));
				
				$baris = 5;
			
				$data_rekap = $this->mt->get_pembelian_rekap_karat($tgl_from,$tgl_to);
				foreach($data_rekap as $dr){
					$sheet->setCellValue('A'.$baris.'', $number);
					$sheet->setCellValue('B'.$baris.'', $dr->karat_name);
					$sheet->setCellValue('C'.$baris.'', $dr->pcs);
					$sheet->setCellValue('D'.$baris.'', $dr->berat);
					$sheet->setCellValue('E'.$baris.'', $dr->harga/$dr->berat);
					$sheet->setCellValue('F'.$baris.'', $dr->harga);
					
					$objPHPExcel->getActiveSheet()->getStyle('D'.$baris.':D'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':E'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('B'.$baris.':B'.$baris.'')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
					
					$baris = $baris + 1;
					
					$total_pcs = $total_pcs + $dr->pcs;
					$total_gram = $total_gram + $dr->berat;
					$total_jual = $total_jual + $dr->harga;
					$number = $number + 1;
				}
				
				if($total_pcs != 0){
					$sheet->setCellValue('C'.$baris.'', $total_pcs);
					$sheet->setCellValue('D'.$baris.'', $total_gram);
					$sheet->setCellValue('F'.$baris.'', $total_jual);
					
					$objPHPExcel->getActiveSheet()->getStyle('D'.$baris.':D'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle("C".$baris.":F".$baris)->applyFromArray(array(
						'borders' => array(
							'top' => array(
								'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
							)
						)
					));
					$objPHPExcel->getActiveSheet()->getStyle('C'.$baris.':F'.$baris.'')->applyFromArray($style_font_header);
					$baris = $baris + 1;
				}
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$baris.'')->applyFromArray($style_font);
				$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$baris.'')->getAlignment()->setWrapText(true);
			}else if($filter_rekap == 'All'){
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(28);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(28);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(28);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(17);
				
				$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
				$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
				$objPHPExcel->getActiveSheet()->getStyle('A1:G4')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				
				$style_font_header = array(
					'font'  => array(
						'bold'  => true
					)
				);
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:G4')->applyFromArray($style_font_header);
				
				$sheet->setCellValue('A1', 'Laporan Pembelian Harian Rekap');
				$sheet->setCellValue('A2', 'Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name);
				
				$sheet->setCellValue('A4', 'No');
				$sheet->setCellValue('B4', 'Tanggal');
				$sheet->setCellValue('C4', 'ID Transaksi');
				$sheet->setCellValue('D4', 'Nama Customer');
				$sheet->setCellValue('E4', 'Alamat Customer');
				$sheet->setCellValue('F4', 'Telepon Customer');
				$sheet->setCellValue('G4', 'Jumlah');
				
				$objPHPExcel->getActiveSheet()->getStyle("A4:G4")->applyFromArray(array(
					'borders' => array(
						'top' => array(
							'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						),
						'bottom' => array(
							'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						)
					)
				));
				
				$baris = 5;
				
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
						
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$baris.':G'.$baris.'');
						$sheet->setCellValue('A'.$baris.'', $coa_data);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$baris.':A'.$baris.'')->applyFromArray($style_font_header);
						$baris = $baris + 1;
						
						foreach($mutasi_transaksi_beli as $mk){
							if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
								if($mk->fromaccount == $accountnumber){
									$trans_date = strtotime($mk->transdate);
									$trans_date = date('d/m/Y',$trans_date);
									
									$idmutasi = substr($mk->idmutasi,0,20);
									$customer_name = $this->mt->get_customer_name2($idmutasi);
									$customer_address = $this->mt->get_customer_address2($idmutasi);
									$customer_phone = $this->mt->get_customer_phone2($idmutasi);
									
									$sheet->setCellValue('A'.$baris.'', $number);
									$sheet->setCellValue('B'.$baris.'', $trans_date);
									$sheet->setCellValue('C'.$baris.'', $idmutasi);
									$sheet->setCellValue('D'.$baris.'', $customer_name);
									$sheet->setCellValue('E'.$baris.'', $customer_address);
									$sheet->setCellValue('F'.$baris.'', $customer_phone);
									$sheet->setCellValue('G'.$baris.'', $mk->value);
									
									$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
									
									$baris = $baris + 1;
									
									$total_debet = $total_debet + $mk->value;
									$running_balance = $running_balance + $mk->value;
									$number = $number + 1;
								}
							}
						}
						
						if($total_debet != 0){
							$objPHPExcel->getActiveSheet()->mergeCells('A'.$baris.':F'.$baris.'');
							$sheet->setCellValue('G'.$baris.'', $total_debet);
							$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
							$objPHPExcel->getActiveSheet()->getStyle("G".$baris.":G".$baris)->applyFromArray(array(
								'borders' => array(
									'top' => array(
										'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
									)
								)
							));
							$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->applyFromArray($style_font_header);
							$baris = $baris + 1;
						}
						//---
					}
					
					/*----------------------------------------------------*/
				}
				
				if($running_balance != 0){
					$baris = $baris + 1;
					$sheet->setCellValue('G'.$baris.'', $running_balance);
					$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle("G".$baris.":G".$baris)->applyFromArray(array(
						'borders' => array(
							'top' => array(
								'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
							)
						)
					));
					$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->applyFromArray($style_font_header);
				}
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$baris.'')->applyFromArray($style_font);
				$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$baris.'')->getAlignment()->setWrapText(true);
			}else if($filter_rekap == 'G'){
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(14);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
				
				$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
				$objPHPExcel->getActiveSheet()->mergeCells('A3:F3');
				$objPHPExcel->getActiveSheet()->getStyle('A1:F4')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				
				$style_font_header = array(
					'font'  => array(
						'bold'  => true
					)
				);
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:F4')->applyFromArray($style_font_header);
				
				$sheet->setCellValue('A1', 'Laporan Pembelian Harian Rekap / Kelompok');
				$sheet->setCellValue('A2', 'Tanggal '.$tanggal_from.' S/D '.$tanggal_to.', Cabang '.$site_name);
				
				$sheet->setCellValue('A4', 'No');
				$sheet->setCellValue('B4', 'Karat');
				$sheet->setCellValue('C4', 'Pcs');
				$sheet->setCellValue('D4', 'Gram');
				$sheet->setCellValue('E4', 'Rata2');
				$sheet->setCellValue('F4', 'Total Jual');
				
				$objPHPExcel->getActiveSheet()->getStyle("A4:F4")->applyFromArray(array(
					'borders' => array(
						'top' => array(
							'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						),
						'bottom' => array(
							'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
						)
					)
				));
				
				$baris = 5;
				
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
						$sheet->setCellValue('A'.$baris.'', $number);
						$sheet->setCellValue('B'.$baris.'', $dk->karat_name);
						
						$objPHPExcel->getActiveSheet()->getStyle('B'.$baris.':B'.$baris.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->mergeCells('C'.$baris.':F'.$baris.'');
						
						$objPHPExcel->getActiveSheet()->getStyle('A'.$baris.':B'.$baris.'')->applyFromArray($style_font_header);
						
						$baris = $baris + 1;
						
						foreach($data_rekap as $dr){
							$sheet->setCellValue('B'.$baris.'', $dr->category_name);
							$sheet->setCellValue('C'.$baris.'', $dr->pcs);
							$sheet->setCellValue('D'.$baris.'', $dr->berat);
							$sheet->setCellValue('E'.$baris.'', $dr->harga/$dr->berat);
							$sheet->setCellValue('F'.$baris.'', $dr->harga);
							
							$objPHPExcel->getActiveSheet()->getStyle('D'.$baris.':D'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
							$objPHPExcel->getActiveSheet()->getStyle('E'.$baris.':E'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');
							$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
							$objPHPExcel->getActiveSheet()->getStyle('B'.$baris.':B'.$baris.'')->applyFromArray($style_font_header);
							
							$baris = $baris + 1;
							
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
						$sheet->setCellValue('C'.$baris.'', $total_pcs);
						$sheet->setCellValue('D'.$baris.'', $total_gram);
						$sheet->setCellValue('F'.$baris.'', $total_jual);
						
						$objPHPExcel->getActiveSheet()->getStyle('D'.$baris.':D'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
						$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
						
						$objPHPExcel->getActiveSheet()->getStyle("C".$baris.":F".$baris)->applyFromArray(array(
							'borders' => array(
								'top' => array(
									'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
								)
							)
						));
						$objPHPExcel->getActiveSheet()->getStyle('C'.$baris.':F'.$baris.'')->applyFromArray($style_font_header);
						
						$baris = $baris + 1;
					}
				}
				
				if($total_pcs_all != 0){
					$baris = $baris + 1;
					
					$sheet->setCellValue('C'.$baris.'', $total_pcs_all);
					$sheet->setCellValue('D'.$baris.'', $total_gram_all);
					$sheet->setCellValue('F'.$baris.'', $total_jual_all);
					
					$objPHPExcel->getActiveSheet()->getStyle('D'.$baris.':D'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
					$objPHPExcel->getActiveSheet()->getStyle('F'.$baris.':F'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
					
					$objPHPExcel->getActiveSheet()->getStyle("C".$baris.":F".$baris)->applyFromArray(array(
						'borders' => array(
							'top' => array(
								'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
							)
						)
					));
					$objPHPExcel->getActiveSheet()->getStyle('C'.$baris.':F'.$baris.'')->applyFromArray($style_font_header);
				}
				
				$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$baris.'')->applyFromArray($style_font);
				$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$baris.'')->getAlignment()->setWrapText(true);
			}
		}
		
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');

		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="EXCEL PEMBELIAN '.$site_name.' TANGGAL '.$tanggal_from.' SD '.$tanggal_to.'.xlsx"');
		//unduh file
		$objWriter->save("php://output");
	}
	
	public function lap_beli_to_excel_tax($tgl_from,$tgl_to){
		date_default_timezone_set("Asia/Jakarta");
		$tgl_from = str_replace('%20',' ',$tgl_from);
		$tgl_to = str_replace('%20',' ',$tgl_to);
		
		$tanggal_from =  $tgl_from;
		$tglFrom = $this->date_to_format($tgl_from);
		$tgl_from = date("Y-m-d",$tglFrom).' 00:00:00';
		
		$tanggal_to =  $tgl_to;
		$tglTo = $this->date_to_format($tgl_to);
		$tgl_to = date("Y-m-d",$tglTo).' 23:59:59';
		
		$site_name = $this->mm->get_site_name();
		
		$data_karat = $this->mk->get_karat_srt();
		$total_karat = count($data_karat);
		
		$array_jual = array();
		
		//$this->load->library('Libexcel');
		$objPHPExcel = new Spreadsheet();
		$sheet = $objPHPExcel->getActiveSheet();
		
		$style_font = array(
			'font'  => array(
				'name'  => 'Segoe UI'
			)
		);
		
		$i = 0;
		
		while($i < $total_karat){
			$sheet = $objPHPExcel->createSheet($i);
			$karat_name = $data_karat[$i]->karat_name;
			$karat_id = $data_karat[$i]->id;
			$sheet->setTitle($karat_name);
			
			$data_jual = $this->mt->get_rekap_karat_excel_beli($karat_id,$tgl_from,$tgl_to);
			
			$sheet->getColumnDimension('A')->setWidth(25);
			$sheet->getColumnDimension('B')->setWidth(20);
			$sheet->getColumnDimension('C')->setWidth(20);
			$sheet->getColumnDimension('D')->setWidth(25);
			
			$sheet
				->mergeCells('A1:D1')
				->mergeCells('A2:D2');
			
			$sheet
				->setCellValue('A1','LAPORAN PEMBELIAN EMAS')
				->setCellValue('A2','CABANG : '.$site_name)
				->setCellValue('A6','KARAT : ')
				->setCellValue('B6',$karat_name)
				->setCellValue('A8','Tanggal')
				->setCellValue('B8','KETERANGAN')
				->setCellValue('C8','Gram')
				->setCellValue('D8','Rp');
			
			$sheet->getStyle('B6')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			
			$style_font_header = array(
				'font'  => array(
					'bold'  => true
				)
			);
			
			$sheet->getStyle('A1:D8')->applyFromArray($style_font_header);
			
			$sheet->getStyle("A8:D8")->applyFromArray(array(
				'borders' => array(
					'top' => array(
						'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
					),
					'bottom' => array(
						'style' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
					)
				)
			));
			
			$sheet->getStyle('A8:D8')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			
			$baris = 10;
			
			foreach($data_jual as $dj){
				$trans_date = strtotime($dj->trans_date);
				$trans_tgl = date('d',$trans_date);
				$trans_bln = date('m',$trans_date);
				$trans_thn = date('Y',$trans_date);
				
				$trans_bln = (int)$trans_bln;
				
				switch($trans_bln){
					case 1:
						$bulankini = 'Januari';
						break;
					case 2:
						$bulankini = 'Februari';
						break;
					case 3:
						$bulankini = 'Maret';
						break;
					case 4:
						$bulankini = 'April';
						break;
					case 5:
						$bulankini = 'May';
						break;
					case 6:
						$bulankini = 'Juni';
						break;
					case 7:
						$bulankini = 'Juli';
						break;
					case 8:
						$bulankini = 'Agustus';
						break;
					case 9:
						$bulankini = 'September';
						break;
					case 10:
						$bulankini = 'Oktober';
						break;
					case 11:
						$bulankini = 'November';
						break;
					case 12:
						$bulankini = 'Desember';
						break;
				}
				
				$sheet
				->setCellValue('A'.$baris, $trans_tgl.' '.$bulankini.' '.$trans_thn)
				->setCellValue('B'.$baris, 'PEMBELIAN')
				->setCellValue('C'.$baris, $dj->berat)
				->setCellValue('D'.$baris, $dj->harga);
				
				$baris = $baris + 1;
			}
			
			$sheet->getStyle('C10:C'.$baris)->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
			$sheet->getStyle('D10:D'.$baris)->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
			$sheet->getStyle('A1:D'.$baris.'')->applyFromArray($style_font);
			$sheet->getStyle('A1:D'.$baris.'')->getAlignment()->setWrapText(true);
			
			$i++;
		}
		
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');

		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="EXCEL PEMBELIAN TAX '.$site_name.' TANGGAL '.$tanggal_from.' SD '.$tanggal_to.'.xlsx"');
		//unduh file
		$objWriter->save("php://output");
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
