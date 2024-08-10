<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LapReparasi extends CI_Controller {
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
		$berlian = $this->mm->cek_status_berlian();
		$status = '';
		
		if($berlian = 'Y'){
			$status = 'checked';
		}
		
		$data['view'] = '<div class="ui fluid container no-print">
		<form class="ui form form-javascript" id="lapReparasi-form-filter" action="'.base_url().'index.php/lapReparasi/filter" method="post">
		<div class="ui grid">
			<div class="ui inverted dimmer" id="lapReparasi-loaderlist">
				<div class="ui large text loader">Loading</div>
			</div>
			<div class="five wide centered column no-print" style="margin-top:15px">
				<div class="fields">
					<div class="four wide field">
						<div class="ui left floated compact segment" style="padding:0.5em 1em">
							Berlian
							<div class="ui fitted toggle checkbox">
								<input type="checkbox" name="lapReparasi-berlian" id="lapReparasi-berlian" '.$status.' onchange=filterTransaksi("lapReparasi") value="Y"><label></label>
							</div>
						</div>
					</div>
					<div class="eight wide field">
						<label>Tgl Laporan</label>
						<input type="text" name="lapReparasi-date" id="lapReparasi-date" readonly>
					</div>
					<div class="four wide field">
						<label style="visibility:hidden">-</label>
						<div class="ui fluid icon green button filter-input" id="lapReparasi-btnfilter" onclick=filterTransaksi("lapReparasi") title="Filter">
							<i class="filter icon"></i> Filter
						</div>
					</div>
				</div>
			</div>
			<div class="fifteen wide centered column" id="lapReparasi-wrap_filter">
			</div>
		</div>
		</form>';
		
		$data["date"] = 1;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function filter(){
		date_default_timezone_set("Asia/Jakarta");
		
		$tgl_transaksi =  $this->input->post('lapReparasi-date');
		$tanggal_transaksi =  $this->input->post('lapReparasi-date');
		
		$tglTrans = $this->date_to_format($tgl_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		$tgl_sa = date("Y-m-d",$tglTrans).' 23:59:59';
		
		$tglBesok = date("Y-m-d",$tglTrans);
		$tgl_besok = date('Y-m-d',strtotime($tglBesok. "+1 days"));
		$tgl_sak = $tgl_besok.' 23:59:59';
		$tgl_besok = $tgl_besok.' 00:00:00';
		
		$berlian = $this->input->post('lapReparasi-berlian');
		if($berlian == 'Y'){
			$berlian = 'Y';
		}else{
			$berlian = 'N';
		}
		
		$this->mm->change_berlian_status($berlian);
		
		$site_name = $this->mm->get_site_name();
		$data_karat = $this->mk->get_karat_sdg();
		
		$count_data_karat = count($data_karat);
		$colspan_head = $count_data_karat+1;
		
		$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column" style="text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/lapReparasi/pdf/'.$tanggal_transaksi.'" target="_blank"><i class="paperclip icon"></i> Download</a></div></div><table id="filter_data_tabel " class="ui celled table" cellspacing="0" width="100%"><thead><tr style="text-align:center"><th class="center aligned" rowspan="2">Keterangan</th><th class="center aligned" colspan="'.$colspan_head.'">Kelompok Emas</th><th class="center aligned" colspan="3">Hutang/Piutang Emas Bahan</th></tr><tr style="text-align:center">';
		
		foreach($data_karat as $dk){
			$data['view'] .= '<th class="center aligned">'.$dk->karat_name.'</th>';
		}
		
		$data['view'] .= '<th class="center aligned">Total</th>';
		$data['view'] .= '<th class="center aligned">Di Reparasi</th><th class="center aligned">Di Pengadaan</th><th class="center aligned">Titipan Pelanggan</th>';
		$data['view'] .= '</tr></thead><tbody>';
		
		//                       SALDO AWAL                       //
		$data['view'] .= '<tr><td class="td-bold">Saldo Awal</td>';
		
		//REP TOKO
		$coa_from = 170001;
		$coa_to = 170001;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		$total_fisik = 0;
		foreach($data_karat as $k){
			foreach($kas_account as $ka){
				$saldo_awal_kurs = array();
				$saldo_akhir_kurs = array();
				
				$acc_number = $ka->accountnumber;
				$id_kurs = $k->id;
				
				$type = $ka->type;
				
				$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
				$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
				
				$saldo_awal_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$tgl_transaksi,$id_kurs);
				$saldo_awal_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$tgl_transaksi,$id_kurs);
				
				foreach($saldo_awal_d as $mtyd){
					$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] + $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] - $mtyk->total_mutasi;
				}
				
				$total_fisik = $total_fisik + $saldo_awal_kurs[$id_kurs];
				
				if($saldo_awal_kurs[$id_kurs] == 0){
					$sa_tulis = '-';
				}else{
					$sa_gram = number_format($saldo_awal_kurs[$id_kurs], 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
				}
				
				$data['view'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td>';
			}
		}
		
		if($total_fisik == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_fisik, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td>';
		
		//DEPT REPARASI
		$coa_from = 170002;
		$coa_to = 170002;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		$tabel_name = 'gold_mutasi_reparasi';
		
		foreach($kas_account as $ka){
			$saldo_awal_kurs = array();
			$saldo_akhir_kurs = array();
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			$id_kurs = 1;
			$type = $ka->type;
			
			$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
			$saldo_awal_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$tgl_transaksi);
			$saldo_awal_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$tgl_transaksi);
			
			foreach($saldo_awal_d as $mtyd){
				$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] + $mtyd->total_mutasi;
			}
			
			foreach($saldo_awal_k as $mtyk){
				$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] - $mtyk->total_mutasi;
			}
		}
		
		if($saldo_awal_kurs[$id_kurs] == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($saldo_awal_kurs[$id_kurs], 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td>';
		
		//DEPT PENGADAAN
		$coa_from = 170003;
		$coa_to = 170003;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		$tabel_name = 'gold_mutasi_pengadaan';
		
		foreach($kas_account as $ka){
			$saldo_awal_kurs = array();
			$saldo_akhir_kurs = array();
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			$id_kurs = 1;
			$type = $ka->type;
			
			$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
			$saldo_awal_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$tgl_transaksi);
			$saldo_awal_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$tgl_transaksi);
			
			foreach($saldo_awal_d as $mtyd){
				$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] + $mtyd->total_mutasi;
			}
			
			foreach($saldo_awal_k as $mtyk){
				$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] - $mtyk->total_mutasi;
			}
		}
		
		if($saldo_awal_kurs[$id_kurs] == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($saldo_awal_kurs[$id_kurs], 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td>';
		
		//TITIPAN PELANGGAN
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
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_bykarat($acc_number,$tgl_transaksi,$id_kurs);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_bykarat($acc_number,$tgl_transaksi,$id_kurs);
				
				foreach($saldo_awal_d as $mtyd){
					$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] - $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] + $mtyk->total_mutasi;
				}
				
				$total_titipan = $total_titipan + $saldo_awal_kurs[$id_kurs];				
			}
			
			if($total_titipan == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_titipan, 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
			}
			
			$data['view'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td></tr>';
		}else{
			$data['view'] .= '<td class="right aligned td-bold">0</td></tr>';
		}
		
		
		$total_terima_reptoko = array();
		$data['view'] .= '<tr><td class="td-bold">Penerimaan (+)</td>';
		foreach($data_karat as $k){
			$data['view'] .= '<td></td>';
			$total_terima_reptoko[$k->id] = 0;
		}
		
		$data['view'] .= '<td></td><td></td><td></td><td></td>';
		
		//                       PEMBELIAN                       //
		$data['view'] .= '<tr><td>Pembelian</td>';
		
		$data_rekap = $this->mt->get_pembelian_rekap_karat($tgl_transaksi,$tgl_transaksi);
		$total_beli = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->id_karat){
					
					$sa_gram = number_format($dr->berat, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					
					$total_beli = $total_beli + $dr->berat;
					$total_terima_reptoko[$dr->id_karat] = $total_terima_reptoko[$dr->id_karat] + $dr->berat;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right aligned">-</td>';
			}
		}
		
		if($total_beli == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_beli, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td><td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td>';
		
		//                       STOCK OUT                       //
		$data['view'] .= '<tr><td>Stock Out</td>';
		
		$data_rekap = $this->mp->get_rekap_stock_out($tgl_transaksi);
		$total_so = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->id_karat){
					$sa_gram = number_format($dr->berat, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					$total_so = $total_so + $dr->berat;
					$total_terima_reptoko[$dr->id_karat] = $total_terima_reptoko[$dr->id_karat] + $dr->berat;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right aligned">-</td>';
			}
		}
		
		if($total_so == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_so, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td><td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td>';
		
		//                       TARIK REPARASI                       //
		$coa_from = '17-0002';
		$coa_to = '17-0001';
		
		$data['view'] .= '<tr><td>Penarikan Barang Reparasi</td>';
		
		$data_rekap = $this->mm->get_rekap_lap_rep($coa_from,$coa_to,$tgl_transaksi,$tgl_sa);
		$total_in = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->idkarat){
					$sa_gram = number_format($dr->total, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					$total_in = $total_in + $dr->total;
					$total_terima_reptoko[$dr->idkarat] = $total_terima_reptoko[$dr->idkarat] + $dr->total;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right aligned">-</td>';
			}
		}
		
		if($total_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td><td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td>';
		
		//                       TARIK PENGADAAN                       //
		$coa_from = '17-0003';
		$coa_to = '17-0001';
		
		$data['view'] .= '<tr><td>Penarikan Barang Pengadaan</td>';
		
		$data_rekap = $this->mm->get_rekap_lap_rep($coa_from,$coa_to,$tgl_transaksi,$tgl_sa);
		$total_in = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->idkarat){
					$sa_gram = number_format($dr->total, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					$total_in = $total_in + $dr->total;
					$total_terima_reptoko[$dr->idkarat] = $total_terima_reptoko[$dr->idkarat] + $dr->total;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right aligned">-</td>';
			}
		}
		
		if($total_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td><td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td>';
		
		//                       LAIN LAIN IN                       //
		$coa_to = '17-0001';
		$data['view'] .= '<tr><td>Lain-lain</td>';
		
		$data_rekap = $this->mm->get_rekap_in_lap_rep($coa_to,$tgl_transaksi,$tgl_sa);
		$total_in = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->idkarat){
					$sa_gram = number_format($dr->total, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					$total_in = $total_in + $dr->total;
					$total_terima_reptoko[$dr->idkarat] = $total_terima_reptoko[$dr->idkarat] + $dr->total;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right aligned">-</td>';
			}
		}
		
		if($total_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
		
		//                       REPARASI IN                       //
		$data_rekap = $this->mm->get_rekap_in_reparasi($tgl_transaksi,$tgl_sa);
		$total_reparasi_in = 0;
		foreach($data_rekap as $dr){
			$total_reparasi_in = $total_reparasi_in + $dr->total;
			if($dr->total != NULL && $dr->total != 0){
				$sa_gram = number_format($dr->total, 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
				$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
			}else{
				$data['view'] .= '<td class="right aligned">-</td>';
			}
		}
		
		//                       PENGADAAN IN                       //
		$data_rekap = $this->mm->get_rekap_in_pengadaan($tgl_transaksi,$tgl_sa);
		$total_pengadaan_in = 0;
		foreach($data_rekap as $dr){
			$total_pengadaan_in = $total_pengadaan_in + $dr->total;
			if($dr->total != NULL && $dr->total != 0){
				$sa_gram = number_format($dr->total, 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
				$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
			}else{
				$data['view'] .= '<td class="right aligned">-</td>';
			}
		}
		
		//                       TITIPAN IN                       //
		$data_rekap = $this->mm->get_rekap_in_titipan($tgl_transaksi,$tgl_sa);
		$total_titipan_in = 0;
		foreach($data_rekap as $dr){
			$total_titipan_in = $total_titipan_in + $dr->total;
			if($dr->total != NULL && $dr->total != 0){
				$sa_gram = number_format($dr->total, 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
				$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
			}else{
				$data['view'] .= '<td class="right aligned">-</td>';
			}
		}
		
		//                       TOTAL PENERIMAAN                       //
		$data['view'] .= '<tr><td class="td-bold">Total Penerimaan</td>';
		$total_in = 0;
		foreach($data_karat as $k){
			if($total_terima_reptoko[$k->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_terima_reptoko[$k->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
			}
			
			$total_in = $total_in + $total_terima_reptoko[$k->id];
			
			$data['view'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
		}
		
		if($total_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
		
		if($total_reparasi_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_reparasi_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
		
		if($total_pengadaan_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_pengadaan_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
		
		if($total_titipan_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_titipan_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td></tr>';
		
		$data['view'] .= '<tr><td><span style="visibility:hidden">-</span></td>';
		foreach($data_karat as $dk){
			$data['view'] .= '<td class="right aligned"></td>';
		}
		$data['view'] .= '<td></td><td></td><td></td><td></td></tr>';
		
		//                       PENGELUARAN                       //
		$total_keluar_reptoko = array();
		$data['view'] .= '<tr><td class="td-bold">Pengeluaran (-)</td>';
		foreach($data_karat as $k){
			$data['view'] .= '<td></td>';
			$total_keluar_reptoko[$k->id] = 0;
		}
		
		$data['view'] .= '<td></td><td></td><td></td><td></td>';
		
		//                       STOCK IN                       //
		$data['view'] .= '<tr><td>Ke Pajangan</td>';
		
		$data_rekap = $this->mp->get_rekap_stock_in($tgl_transaksi);
		$total_so = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->id_karat){
					$sa_gram = number_format($dr->berat, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					$total_so = $total_so + $dr->berat;
					$total_keluar_reptoko[$dr->id_karat] = $total_keluar_reptoko[$dr->id_karat] + $dr->berat;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right aligned">-</td>';
			}
		}
		
		if($total_so == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_so, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td><td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td>';
		
		//                       KIRIM REPARASI                       //
		$coa_from = '17-0001';
		$coa_to = '17-0002';
		
		$data['view'] .= '<tr><td>Pengiriman ke Dept. Reparasi</td>';
		
		$data_rekap = $this->mm->get_rekap_lap_rep($coa_from,$coa_to,$tgl_transaksi,$tgl_sa);
		$total_in = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->idkarat){
					$sa_gram = number_format($dr->total, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					$total_in = $total_in + $dr->total;
					$total_keluar_reptoko[$dr->idkarat] = $total_keluar_reptoko[$dr->idkarat] + $dr->total;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right aligned">-</td>';
			}
		}
		
		if($total_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td><td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td>';
		
		//                       KIRIM PENGADAAN                       //
		$coa_from = '17-0001';
		$coa_to = '17-0003';
		
		$data['view'] .= '<tr><td>Kirim ke Dept. Pengadaan</td>';
		
		$data_rekap = $this->mm->get_rekap_lap_rep($coa_from,$coa_to,$tgl_transaksi,$tgl_sa);
		$total_in = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->idkarat){
					$sa_gram = number_format($dr->total, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					$total_in = $total_in + $dr->total;
					$total_keluar_reptoko[$dr->idkarat] = $total_keluar_reptoko[$dr->idkarat] + $dr->total;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right aligned">-</td>';
			}
		}
		
		if($total_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td><td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td>';
		
		//                       LAIN LAIN OUT                       //
		$coa_from = '17-0001';
		$data['view'] .= '<tr><td>Lain-lain</td>';
		
		$data_rekap = $this->mm->get_rekap_out_lap_rep($coa_from,$tgl_transaksi,$tgl_sa);
		$total_in = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->idkarat){
					$sa_gram = number_format($dr->total, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					$total_in = $total_in + $dr->total;
					$total_keluar_reptoko[$dr->idkarat] = $total_keluar_reptoko[$dr->idkarat] + $dr->total;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right aligned">-</td>';
			}
		}
		
		if($total_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
		
		//                       REPARASI OUT                       //
		$data_rekap = $this->mm->get_rekap_out_reparasi($tgl_transaksi,$tgl_sa);
		$total_reparasi_out = 0;
		foreach($data_rekap as $dr){
			$total_reparasi_out = $total_reparasi_out + $dr->total;
			if($dr->total != NULL && $dr->total != 0){
				$sa_gram = number_format($dr->total, 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
				$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
			}else{
				$data['view'] .= '<td class="right aligned">-</td>';
			}
		}
		
		//                       PENGADAAN OUT                       //
		$data_rekap = $this->mm->get_rekap_out_pengadaan($tgl_transaksi,$tgl_sa);
		$total_pengadaan_out = 0;
		foreach($data_rekap as $dr){
			$total_pengadaan_out = $total_pengadaan_out + $dr->total;
			if($dr->total != NULL && $dr->total != 0){
				$sa_gram = number_format($dr->total, 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
				$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
			}else{
				$data['view'] .= '<td class="right aligned">-</td>';
			}
		}
		
		//                       TITIPAN OUT                       //
		$data_rekap = $this->mm->get_rekap_out_titipan($tgl_transaksi,$tgl_sa);
		$total_titipan_out = 0;
		foreach($data_rekap as $dr){
			$total_titipan_out = $total_titipan_out + $dr->total;
			if($dr->total != NULL && $dr->total != 0){
				$sa_gram = number_format($dr->total, 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
				$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
			}else{
				$data['view'] .= '<td class="right aligned">-</td>';
			}
		}
		
		//                       TOTAL PENGELUARAN                       //
		$data['view'] .= '<tr><td class="td-bold">Total Pengeluaran</td>';
		$total_in = 0;
		foreach($data_karat as $k){
			if($total_keluar_reptoko[$k->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_keluar_reptoko[$k->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
			}
			
			$total_in = $total_in + $total_keluar_reptoko[$k->id];
			
			$data['view'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
		}
		
		if($total_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
		
		if($total_reparasi_out == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_reparasi_out, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
		
		if($total_pengadaan_out == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_pengadaan_out, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
		
		if($total_titipan_out == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_titipan_out, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td></tr>';
		
		//                       SALDO AKHIR                       //
		$data['view'] .= '<tr><td class="td-bold">Saldo Akhir</td>';
		
		//REP TOKO
		$coa_from = 170001;
		$coa_to = 170001;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		$total_fisik = 0;
		foreach($data_karat as $k){
			foreach($kas_account as $ka){
				$saldo_awal_kurs = array();
				$saldo_akhir_kurs = array();
				
				$acc_number = $ka->accountnumber;
				$id_kurs = $k->id;
				
				$type = $ka->type;
				
				$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
				$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
				
				$saldo_awal_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$tgl_besok,$id_kurs);
				$saldo_awal_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$tgl_besok,$id_kurs);
				
				foreach($saldo_awal_d as $mtyd){
					$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] + $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] - $mtyk->total_mutasi;
				}
				
				$total_fisik = $total_fisik + $saldo_awal_kurs[$id_kurs];
				
				if($saldo_awal_kurs[$id_kurs] == 0){
					$sa_tulis = '-';
				}else{
					$sa_gram = number_format($saldo_awal_kurs[$id_kurs], 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
				}
				
				$data['view'] .= '<td class="right aligned td-bold double-top">'.$sa_tulis.'</td>';
			}
		}
		
		if($total_fisik == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_fisik, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned td-bold double-top">'.$sa_tulis.'</td>';
		
		//DEPT REPARASI
		$coa_from = 170002;
		$coa_to = 170002;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		$tabel_name = 'gold_mutasi_reparasi';
		
		foreach($kas_account as $ka){
			$saldo_awal_kurs = array();
			$saldo_akhir_kurs = array();
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			$id_kurs = 1;
			$type = $ka->type;
			
			$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
			$saldo_awal_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$tgl_besok);
			$saldo_awal_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$tgl_besok);
			
			foreach($saldo_awal_d as $mtyd){
				$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] + $mtyd->total_mutasi;
			}
			
			foreach($saldo_awal_k as $mtyk){
				$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] - $mtyk->total_mutasi;
			}
		}
		
		if($saldo_awal_kurs[$id_kurs] == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($saldo_awal_kurs[$id_kurs], 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned td-bold double-top">'.$sa_tulis.'</td>';
		
		//DEPT PENGADAAN
		$coa_from = 170003;
		$coa_to = 170003;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		$tabel_name = 'gold_mutasi_pengadaan';
		
		foreach($kas_account as $ka){
			$saldo_awal_kurs = array();
			$saldo_akhir_kurs = array();
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			$id_kurs = 1;
			$type = $ka->type;
			
			$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
			$saldo_awal_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$tgl_besok);
			$saldo_awal_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$tgl_besok);
			
			foreach($saldo_awal_d as $mtyd){
				$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] + $mtyd->total_mutasi;
			}
			
			foreach($saldo_awal_k as $mtyk){
				$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] - $mtyk->total_mutasi;
			}
		}
		
		if($saldo_awal_kurs[$id_kurs] == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($saldo_awal_kurs[$id_kurs], 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right aligned td-bold double-top">'.$sa_tulis.'</td>';
		
		//TITIPAN PELANGGAN
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
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_bykarat($acc_number,$tgl_besok,$id_kurs);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_bykarat($acc_number,$tgl_besok,$id_kurs);
				
				foreach($saldo_awal_d as $mtyd){
					$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] - $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] + $mtyk->total_mutasi;
				}
				
				$total_titipan = $total_titipan + $saldo_awal_kurs[$id_kurs];				
			}
			
			if($total_titipan == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_titipan, 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
			}
			
			$data['view'] .= '<td class="right aligned td-bold double-top">'.$sa_tulis.'</td></tr>';
		}else{
			$data['view'] .= '<td class="right aligned td-bold double-top">0</td></tr>';
		}
		
		$data['view'] .= '<tr><td><span style="visibility:hidden">-</span></td>';
		foreach($data_karat as $dk){
			$data['view'] .= '<td class="right aligned"></td>';
		}
		$data['view'] .= '<td></td><td></td><td></td><td></td></tr>';
		
		/*--- PEMBELIAN ---*/
		
		$data['view'] .= '<tr><td class="td-bold">Data Pembelian</td>';
		foreach($data_karat as $dk){
			$data['view'] .= '<td class="right aligned"></td>';
		}
		$data['view'] .= '<td></td><td></td><td></td><td></td></tr>';
		
		$total_si_rupiah = array();
		$total_si_gram = array();
		$total_si_rupiah_all = 0;
		$total_si_gram_all = 0;
		
		foreach($data_karat as $dk){
			$total_si_rupiah[$dk->id] = 0;
			$total_si_gram[$dk->id] = 0;
		}
		
		$data_si = $this->mt->get_rekap_pembelian($tgl_transaksi);
		
		$data['view'] .= '<tr><td class="align-middle">Pembelian Dalam Rupiah</td>';
		foreach($data_si as $ds){
			$total_si_rupiah[$ds->id_karat] = $ds->total;
			$total_si_gram[$ds->id_karat] = $ds->berat;
			
			$total_si_rupiah_all = $total_si_rupiah_all + $ds->total;
			$total_si_gram_all = $total_si_gram_all + $ds->berat;
		}
		
		foreach($data_karat as $dk){
			if($total_si_rupiah[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_tulis = number_format($total_si_rupiah[$dk->id], 0);
			}
			$data['view'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td>';
		}
		
		if($total_si_rupiah_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_tulis = number_format($total_si_rupiah_all, 0);
		}
		
		$data['view'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td><td></td><td></td><td></td></tr>';
		
		$data['view'] .= '<tr><td class="align-middle">Rata2 per Gram</td>';
		
		foreach($data_karat as $dk){
			if($total_si_gram[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_tulis = number_format($total_si_rupiah[$dk->id] / $total_si_gram[$dk->id], 0);
			}
			
			$data['view'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td>';
		}
		
		if($total_si_gram_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_tulis = number_format($total_si_rupiah_all/$total_si_gram_all, 0);
		}
		
		$data['view'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td><td></td><td></td><td></td></tr>';
		
		$data['view'] .= '</tbody></table>';

		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function pdf($tgl_transaksi){
		date_default_timezone_set("Asia/Jakarta");
		
		$tgl_transaksi = str_replace('%20',' ',$tgl_transaksi);
		
		$tanggal_transaksi =  $tgl_transaksi;
		
		$tglTrans = $this->date_to_format($tgl_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		
		$tanggal_aktif = strtotime($tgl_transaksi);
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
		
		$tgl_sa = date("Y-m-d",$tglTrans).' 23:59:59';
		
		$tglBesok = date("Y-m-d",$tglTrans);
		$tgl_besok = date('Y-m-d',strtotime($tglBesok. "+1 days"));
		$tgl_sak = $tgl_besok.' 23:59:59';
		$tgl_besok = $tgl_besok.' 00:00:00';
		
		$site_name = $this->mm->get_site_name();
		$data_karat = $this->mk->get_karat_sdg();
		
		$count_data_karat = count($data_karat);
		$colspan_head = $count_data_karat+1;
		
		$sitename = $this->mm->get_site_name();
		
		$data['view'] = '<table class="lap_pdf_4" cellspacing="0" width="100%"><tr><td colspan="2" style="text-align:left; border:none"><img src="'.base_url().'assets/images/branding/brand.png" style="width:220px"></td><td colspan="4" style="text-align:center;font-size:14px;border:none;font-weight:bold">Laporan Emas Reparasi Harian <br>Cabang '.$sitename.'</td><td colspan="3" style="text-align:right;font-size:14px;border:none;font-weight:bold"><br>'.$hari_tulis.', '.$tanggal_aktif.'</td></tr><thead><tr style="text-align:center"><th class="center aligned" rowspan="2" style="width:220px">Keterangan</th><th class="center aligned" colspan="'.$colspan_head.'">Kelompok Emas</th><th class="center aligned" colspan="3">Hutang/Piutang Emas Bahan</th></tr><tr style="text-align:center">';
		
		foreach($data_karat as $dk){
			$data['view'] .= '<th class="center aligned" style="width:110px;">'.$dk->karat_name.'</th>';
		}
		
		$data['view'] .= '<th class="center aligned" style="width:110px;">Total</th>';
		$data['view'] .= '<th class="center aligned" style="width:110px;">Reparasi</th><th class="center aligned" style="width:110px;">Pengadaan</th><th class="center aligned" style="width:110px;">Titipan Plgn</th>';
		$data['view'] .= '</tr></thead><tbody>';
		
		//                       SALDO AWAL                       //
		$data['view'] .= '<tr><td class="td-bold">Saldo Awal</td>';
		
		//REP TOKO
		$coa_from = 170001;
		$coa_to = 170001;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		$total_fisik = 0;
		foreach($data_karat as $k){
			foreach($kas_account as $ka){
				$saldo_awal_kurs = array();
				$saldo_akhir_kurs = array();
				
				$acc_number = $ka->accountnumber;
				$id_kurs = $k->id;
				
				$type = $ka->type;
				
				$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
				$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
				
				$saldo_awal_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$tgl_transaksi,$id_kurs);
				$saldo_awal_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$tgl_transaksi,$id_kurs);
				
				foreach($saldo_awal_d as $mtyd){
					$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] + $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] - $mtyk->total_mutasi;
				}
				
				$total_fisik = $total_fisik + $saldo_awal_kurs[$id_kurs];
				
				if($saldo_awal_kurs[$id_kurs] == 0){
					$sa_tulis = '-';
				}else{
					$sa_gram = number_format($saldo_awal_kurs[$id_kurs], 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
				}
				
				$data['view'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td>';
			}
		}
		
		if($total_fisik == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_fisik, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td>';
		
		//DEPT REPARASI
		$coa_from = 170002;
		$coa_to = 170002;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		$tabel_name = 'gold_mutasi_reparasi';
		
		foreach($kas_account as $ka){
			$saldo_awal_kurs = array();
			$saldo_akhir_kurs = array();
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			$id_kurs = 1;
			$type = $ka->type;
			
			$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
			$saldo_awal_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$tgl_transaksi);
			$saldo_awal_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$tgl_transaksi);
			
			foreach($saldo_awal_d as $mtyd){
				$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] + $mtyd->total_mutasi;
			}
			
			foreach($saldo_awal_k as $mtyk){
				$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] - $mtyk->total_mutasi;
			}
		}
		
		if($saldo_awal_kurs[$id_kurs] == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($saldo_awal_kurs[$id_kurs], 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td>';
		
		//DEPT PENGADAAN
		$coa_from = 170003;
		$coa_to = 170003;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		$tabel_name = 'gold_mutasi_pengadaan';
		
		foreach($kas_account as $ka){
			$saldo_awal_kurs = array();
			$saldo_akhir_kurs = array();
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			$id_kurs = 1;
			$type = $ka->type;
			
			$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
			$saldo_awal_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$tgl_transaksi);
			$saldo_awal_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$tgl_transaksi);
			
			foreach($saldo_awal_d as $mtyd){
				$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] + $mtyd->total_mutasi;
			}
			
			foreach($saldo_awal_k as $mtyk){
				$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] - $mtyk->total_mutasi;
			}
		}
		
		if($saldo_awal_kurs[$id_kurs] == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($saldo_awal_kurs[$id_kurs], 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td>';
		
		//TITIPAN PELANGGAN
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
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_bykarat($acc_number,$tgl_transaksi,$id_kurs);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_bykarat($acc_number,$tgl_transaksi,$id_kurs);
				
				foreach($saldo_awal_d as $mtyd){
					$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] - $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] + $mtyk->total_mutasi;
				}
				
				$total_titipan = $total_titipan + $saldo_awal_kurs[$id_kurs];				
			}
			
			if($total_titipan == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_titipan, 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
			}
			
			$data['view'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td></tr>';
		}else{
			$data['view'] .= '<td class="right-aligned td-bold">0</td></tr>';
		}
		
		
		$total_terima_reptoko = array();
		$data['view'] .= '<tr><td class="td-bold">Penerimaan (+)</td>';
		foreach($data_karat as $k){
			$data['view'] .= '<td></td>';
			$total_terima_reptoko[$k->id] = 0;
		}
		
		$data['view'] .= '<td></td><td></td><td></td><td></td>';
		
		//                       PEMBELIAN                       //
		$data['view'] .= '<tr><td><li>Pembelian</li></td>';
		
		$data_rekap = $this->mt->get_pembelian_rekap_karat($tgl_transaksi,$tgl_transaksi);
		$total_beli = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->id_karat){
					
					$sa_gram = number_format($dr->berat, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					
					$total_beli = $total_beli + $dr->berat;
					$total_terima_reptoko[$dr->id_karat] = $total_terima_reptoko[$dr->id_karat] + $dr->berat;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right-aligned">-</td>';
			}
		}
		
		if($total_beli == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_beli, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td><td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td>';
		
		//                       STOCK OUT                       //
		$data['view'] .= '<tr><td><li>Stock Out</li></td>';
		
		$data_rekap = $this->mp->get_rekap_stock_out($tgl_transaksi);
		$total_so = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->id_karat){
					$sa_gram = number_format($dr->berat, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					$total_so = $total_so + $dr->berat;
					$total_terima_reptoko[$dr->id_karat] = $total_terima_reptoko[$dr->id_karat] + $dr->berat;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right-aligned">-</td>';
			}
		}
		
		if($total_so == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_so, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td><td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td>';
		
		//                       TARIK REPARASI                       //
		$coa_from = '17-0002';
		$coa_to = '17-0001';
		
		$data['view'] .= '<tr><td><li>Penarikan Barang Reparasi</li></td>';
		
		$data_rekap = $this->mm->get_rekap_lap_rep($coa_from,$coa_to,$tgl_transaksi,$tgl_sa);
		$total_in = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->idkarat){
					$sa_gram = number_format($dr->total, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					$total_in = $total_in + $dr->total;
					$total_terima_reptoko[$dr->idkarat] = $total_terima_reptoko[$dr->idkarat] + $dr->total;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right-aligned">-</td>';
			}
		}
		
		if($total_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td><td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td>';
		
		//                       TARIK PENGADAAN                       //
		$coa_from = '17-0003';
		$coa_to = '17-0001';
		
		$data['view'] .= '<tr><td><li>Penarikan Barang Pengadaan</li></td>';
		
		$data_rekap = $this->mm->get_rekap_lap_rep($coa_from,$coa_to,$tgl_transaksi,$tgl_sa);
		$total_in = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->idkarat){
					$sa_gram = number_format($dr->total, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					$total_in = $total_in + $dr->total;
					$total_terima_reptoko[$dr->idkarat] = $total_terima_reptoko[$dr->idkarat] + $dr->total;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right-aligned">-</td>';
			}
		}
		
		if($total_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td><td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td>';
		
		//                       LAIN LAIN IN                       //
		$coa_to = '17-0001';
		$data['view'] .= '<tr><td><li>Lain-lain</li></td>';
		
		$data_rekap = $this->mm->get_rekap_in_lap_rep($coa_to,$tgl_transaksi,$tgl_sa);
		$total_in = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->idkarat){
					$sa_gram = number_format($dr->total, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					$total_in = $total_in + $dr->total;
					$total_terima_reptoko[$dr->idkarat] = $total_terima_reptoko[$dr->idkarat] + $dr->total;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right-aligned">-</td>';
			}
		}
		
		if($total_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
		
		//                       REPARASI IN                       //
		$data_rekap = $this->mm->get_rekap_in_reparasi($tgl_transaksi,$tgl_sa);
		$total_reparasi_in = 0;
		foreach($data_rekap as $dr){
			$total_reparasi_in = $total_reparasi_in + $dr->total;
			if($dr->total != NULL && $dr->total != 0){
				$sa_gram = number_format($dr->total, 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
				$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
			}else{
				$data['view'] .= '<td class="right-aligned">-</td>';
			}
		}
		
		//                       PENGADAAN IN                       //
		$data_rekap = $this->mm->get_rekap_in_pengadaan($tgl_transaksi,$tgl_sa);
		$total_pengadaan_in = 0;
		foreach($data_rekap as $dr){
			$total_pengadaan_in = $total_pengadaan_in + $dr->total;
			if($dr->total != NULL && $dr->total != 0){
				$sa_gram = number_format($dr->total, 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
				$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
			}else{
				$data['view'] .= '<td class="right-aligned">-</td>';
			}
		}
		
		//                       TITIPAN IN                       //
		$data_rekap = $this->mm->get_rekap_in_titipan($tgl_transaksi,$tgl_sa);
		$total_titipan_in = 0;
		foreach($data_rekap as $dr){
			$total_titipan_in = $total_titipan_in + $dr->total;
			if($dr->total != NULL && $dr->total != 0){
				$sa_gram = number_format($dr->total, 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
				$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
			}else{
				$data['view'] .= '<td class="right-aligned">-</td>';
			}
		}
		
		//                       TOTAL PENERIMAAN                       //
		$data['view'] .= '<tr><td class="td-bold">Total Penerimaan</td>';
		$total_in = 0;
		foreach($data_karat as $k){
			if($total_terima_reptoko[$k->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_terima_reptoko[$k->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
			}
			
			$total_in = $total_in + $total_terima_reptoko[$k->id];
			
			$data['view'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
		}
		
		if($total_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
		
		if($total_reparasi_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_reparasi_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
		
		if($total_pengadaan_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_pengadaan_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
		
		if($total_titipan_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_titipan_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td></tr>';
		
		$data['view'] .= '<tr><td><span style="visibility:hidden">-</span></td>';
		foreach($data_karat as $dk){
			$data['view'] .= '<td class="right-aligned"></td>';
		}
		$data['view'] .= '<td></td><td></td><td></td><td></td></tr>';
		
		//                       PENGELUARAN                       //
		$total_keluar_reptoko = array();
		$data['view'] .= '<tr><td class="td-bold">Pengeluaran (-)</td>';
		foreach($data_karat as $k){
			$data['view'] .= '<td></td>';
			$total_keluar_reptoko[$k->id] = 0;
		}
		
		$data['view'] .= '<td></td><td></td><td></td><td></td>';
		
		//                       STOCK IN                       //
		$data['view'] .= '<tr><td><li>Ke Pajangan</li></td>';
		
		$data_rekap = $this->mp->get_rekap_stock_in($tgl_transaksi);
		$total_so = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->id_karat){
					$sa_gram = number_format($dr->berat, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					$total_so = $total_so + $dr->berat;
					$total_keluar_reptoko[$dr->id_karat] = $total_keluar_reptoko[$dr->id_karat] + $dr->berat;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right-aligned">-</td>';
			}
		}
		
		if($total_so == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_so, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td><td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td>';
		
		//                       KIRIM REPARASI                       //
		$coa_from = '17-0001';
		$coa_to = '17-0002';
		
		$data['view'] .= '<tr><td><li>Pengiriman ke Dept. Reparasi</li></td>';
		
		$data_rekap = $this->mm->get_rekap_lap_rep($coa_from,$coa_to,$tgl_transaksi,$tgl_sa);
		$total_in = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->idkarat){
					$sa_gram = number_format($dr->total, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					$total_in = $total_in + $dr->total;
					$total_keluar_reptoko[$dr->idkarat] = $total_keluar_reptoko[$dr->idkarat] + $dr->total;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right-aligned">-</td>';
			}
		}
		
		if($total_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td><td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td>';
		
		//                       KIRIM PENGADAAN                       //
		$coa_from = '17-0001';
		$coa_to = '17-0003';
		
		$data['view'] .= '<tr><td><li>Kirim ke Dept. Pengadaan</li></td>';
		
		$data_rekap = $this->mm->get_rekap_lap_rep($coa_from,$coa_to,$tgl_transaksi,$tgl_sa);
		$total_in = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->idkarat){
					$sa_gram = number_format($dr->total, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					$total_in = $total_in + $dr->total;
					$total_keluar_reptoko[$dr->idkarat] = $total_keluar_reptoko[$dr->idkarat] + $dr->total;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right-aligned">-</td>';
			}
		}
		
		if($total_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td><td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td>';
		
		//                       LAIN LAIN OUT                       //
		$coa_from = '17-0001';
		$data['view'] .= '<tr><td><li>Lain-lain</li></td>';
		
		$data_rekap = $this->mm->get_rekap_out_lap_rep($coa_from,$tgl_transaksi,$tgl_sa);
		$total_in = 0;
		foreach($data_karat as $k){
			$flag = FALSE;
			foreach($data_rekap as $dr){
				if($k->id == $dr->idkarat){
					$sa_gram = number_format($dr->total, 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
					
					$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					$total_in = $total_in + $dr->total;
					$total_keluar_reptoko[$dr->idkarat] = $total_keluar_reptoko[$dr->idkarat] + $dr->total;
					$flag = TRUE;
				}
			}
			
			if($flag == FALSE){
				$data['view'] .= '<td class="right-aligned">-</td>';
			}
		}
		
		if($total_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
		
		//                       REPARASI OUT                       //
		$data_rekap = $this->mm->get_rekap_out_reparasi($tgl_transaksi,$tgl_sa);
		$total_reparasi_out = 0;
		foreach($data_rekap as $dr){
			$total_reparasi_out = $total_reparasi_out + $dr->total;
			if($dr->total != NULL && $dr->total != 0){
				$sa_gram = number_format($dr->total, 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
				$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
			}else{
				$data['view'] .= '<td class="right-aligned">-</td>';
			}
		}
		
		//                       PENGADAAN OUT                       //
		$data_rekap = $this->mm->get_rekap_out_pengadaan($tgl_transaksi,$tgl_sa);
		$total_pengadaan_out = 0;
		foreach($data_rekap as $dr){
			$total_pengadaan_out = $total_pengadaan_out + $dr->total;
			if($dr->total != NULL && $dr->total != 0){
				$sa_gram = number_format($dr->total, 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
				$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
			}else{
				$data['view'] .= '<td class="right-aligned">-</td>';
			}
		}
		
		//                       TITIPAN OUT                       //
		$data_rekap = $this->mm->get_rekap_out_titipan($tgl_transaksi,$tgl_sa);
		$total_titipan_out = 0;
		foreach($data_rekap as $dr){
			$total_titipan_out = $total_titipan_out + $dr->total;
			if($dr->total != NULL && $dr->total != 0){
				$sa_gram = number_format($dr->total, 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
				$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
			}else{
				$data['view'] .= '<td class="right-aligned">-</td>';
			}
		}
		
		//                       TOTAL PENGELUARAN                       //
		$data['view'] .= '<tr><td class="td-bold">Total Pengeluaran</td>';
		$total_in = 0;
		foreach($data_karat as $k){
			if($total_keluar_reptoko[$k->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_keluar_reptoko[$k->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
			}
			
			$total_in = $total_in + $total_keluar_reptoko[$k->id];
			
			$data['view'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
		}
		
		if($total_in == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_in, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
		
		if($total_reparasi_out == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_reparasi_out, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
		
		if($total_pengadaan_out == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_pengadaan_out, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
		
		if($total_titipan_out == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_titipan_out, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td></tr>';
		
		//                       SALDO AKHIR                       //
		$data['view'] .= '<tr><td class="td-bold">Saldo Akhir</td>';
		
		//REP TOKO
		$coa_from = 170001;
		$coa_to = 170001;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		$total_fisik = 0;
		foreach($data_karat as $k){
			foreach($kas_account as $ka){
				$saldo_awal_kurs = array();
				$saldo_akhir_kurs = array();
				
				$acc_number = $ka->accountnumber;
				$id_kurs = $k->id;
				
				$type = $ka->type;
				
				$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
				$saldo_akhir_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
				
				$saldo_awal_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$tgl_besok,$id_kurs);
				$saldo_awal_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$tgl_besok,$id_kurs);
				
				foreach($saldo_awal_d as $mtyd){
					$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] + $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] - $mtyk->total_mutasi;
				}
				
				$total_fisik = $total_fisik + $saldo_awal_kurs[$id_kurs];
				
				if($saldo_awal_kurs[$id_kurs] == 0){
					$sa_tulis = '-';
				}else{
					$sa_gram = number_format($saldo_awal_kurs[$id_kurs], 3);
					$sa_gram_bulat = substr($sa_gram, 0, -4);
					$sa_gram_koma = substr($sa_gram, -3);
					$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
				}
				
				$data['view'] .= '<td class="right-aligned td-bold double-top" style="border-top:double #000">'.$sa_tulis.'</td>';
			}
		}
		
		if($total_fisik == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_fisik, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned td-bold double-top" style="border-top:double #000">'.$sa_tulis.'</td>';
		
		//DEPT REPARASI
		$coa_from = 170002;
		$coa_to = 170002;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		$tabel_name = 'gold_mutasi_reparasi';
		
		foreach($kas_account as $ka){
			$saldo_awal_kurs = array();
			$saldo_akhir_kurs = array();
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			$id_kurs = 1;
			$type = $ka->type;
			
			$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
			$saldo_awal_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$tgl_besok);
			$saldo_awal_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$tgl_besok);
			
			foreach($saldo_awal_d as $mtyd){
				$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] + $mtyd->total_mutasi;
			}
			
			foreach($saldo_awal_k as $mtyk){
				$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] - $mtyk->total_mutasi;
			}
		}
		
		if($saldo_awal_kurs[$id_kurs] == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($saldo_awal_kurs[$id_kurs], 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned td-bold double-top" style="border-top:double #000">'.$sa_tulis.'</td>';
		
		//DEPT PENGADAAN
		$coa_from = 170003;
		$coa_to = 170003;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		$tabel_name = 'gold_mutasi_pengadaan';
		
		foreach($kas_account as $ka){
			$saldo_awal_kurs = array();
			$saldo_akhir_kurs = array();
			
			$acc_number = $ka->accountnumber;
			$acc_group = $ka->accountgroup;
			$id_kurs = 1;
			$type = $ka->type;
			
			$saldo_awal_kurs[$id_kurs] = $this->mm->get_report_gr_beginning_balance_2($id_kurs,$type);
			$saldo_awal_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$tgl_besok);
			$saldo_awal_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$tgl_besok);
			
			foreach($saldo_awal_d as $mtyd){
				$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] + $mtyd->total_mutasi;
			}
			
			foreach($saldo_awal_k as $mtyk){
				$saldo_awal_kurs[$id_kurs] = $saldo_awal_kurs[$id_kurs] - $mtyk->total_mutasi;
			}
		}
		
		if($saldo_awal_kurs[$id_kurs] == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($saldo_awal_kurs[$id_kurs], 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		$data['view'] .= '<td class="right-aligned td-bold double-top" style="border-top:double #000">'.$sa_tulis.'</td>';
		
		//TITIPAN PELANGGAN
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
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_bykarat($acc_number,$tgl_besok,$id_kurs);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_bykarat($acc_number,$tgl_besok,$id_kurs);
				
				foreach($saldo_awal_d as $mtyd){
					$saldo_awal_kurs[$mtyd->idkarat] = $saldo_awal_kurs[$mtyd->idkarat] - $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$saldo_awal_kurs[$mtyk->idkarat] = $saldo_awal_kurs[$mtyk->idkarat] + $mtyk->total_mutasi;
				}
				
				$total_titipan = $total_titipan + $saldo_awal_kurs[$id_kurs];				
			}
			
			if($total_titipan == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_titipan, 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
			}
			
			$data['view'] .= '<td class="right-aligned td-bold double-top" style="border-top:double #000">'.$sa_tulis.'</td></tr>';
		}else{
			$data['view'] .= '<td class="right-aligned td-bold double-top" style="border-top:double #000">0</td></tr>';
		}
		
		$data['view'] .= '<tr><td><span style="visibility:hidden">-</span></td>';
		foreach($data_karat as $dk){
			$data['view'] .= '<td class="right-aligned"></td>';
		}
		$data['view'] .= '<td></td><td></td><td></td><td></td></tr>';
		
		/*--- PEMBELIAN ---*/
		
		$data['view'] .= '<tr><td class="td-bold">Data Pembelian</td>';
		foreach($data_karat as $dk){
			$data['view'] .= '<td class="right-aligned"></td>';
		}
		$data['view'] .= '<td></td><td></td><td></td><td></td></tr>';
		
		$total_si_rupiah = array();
		$total_si_gram = array();
		$total_si_rupiah_all = 0;
		$total_si_gram_all = 0;
		
		foreach($data_karat as $dk){
			$total_si_rupiah[$dk->id] = 0;
			$total_si_gram[$dk->id] = 0;
		}
		
		$data_si = $this->mt->get_rekap_pembelian($tgl_transaksi);
		
		$data['view'] .= '<tr><td class="align-middle">Pembelian Dalam Rupiah</td>';
		foreach($data_si as $ds){
			$total_si_rupiah[$ds->id_karat] = $ds->total;
			$total_si_gram[$ds->id_karat] = $ds->berat;
			
			$total_si_rupiah_all = $total_si_rupiah_all + $ds->total;
			$total_si_gram_all = $total_si_gram_all + $ds->berat;
		}
		
		foreach($data_karat as $dk){
			if($total_si_rupiah[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_tulis = number_format($total_si_rupiah[$dk->id], 0);
			}
			$data['view'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td>';
		}
		
		if($total_si_rupiah_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_tulis = number_format($total_si_rupiah_all, 0);
		}
		
		$data['view'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td><td></td><td></td><td></td></tr>';
		
		$data['view'] .= '<tr><td class="align-middle">Rata2 per Gram</td>';
		
		foreach($data_karat as $dk){
			if($total_si_gram[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_tulis = number_format($total_si_rupiah[$dk->id] / $total_si_gram[$dk->id], 0);
			}
			
			$data['view'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td>';
		}
		
		if($total_si_gram_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_tulis = number_format($total_si_rupiah_all/$total_si_gram_all, 0);
		}
		
		$data['view'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td><td></td><td></td><td></td></tr>';
		
		$data['view'] .= '</tbody></table>';
		
		$data['view'] .= '<div class="ket-ttd2">Diperiksa Oleh : </div><div class="nama-ttd2"><span>Staff Akuntansi</span></div><div class="ket-ttd2">Disetujui Oleh : </div><div class="nama-ttd2"><span>Manager Cabang</span></div><div class="ket-ttd2">Diketahui Oleh : </div><div class="nama-ttd2"><span>Manager Pembina</span></div>';

		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		$pdf = $this->m_pdf->load();
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right"><img src="'. base_url().'assets/images/branding/fbn.png" style="width:32px"/></div>');
        $pdf->AddPage('L');
        $pdf->WriteHTML($html);
		
        $pdf->Output("Laporan Saldo Reparasi Harian.pdf", "I");
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
