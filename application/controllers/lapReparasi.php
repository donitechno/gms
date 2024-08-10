<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LapReparasi extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		//$this->load->library('M_pdf');
		$this->load->model('M_karat','mk');
		$this->load->model('M_product','mp');
		$this->load->model('M_pos','mt');
		$this->load->model('M_mutasi','mm');
	}
	
	public function index(){
		$berlian = $this->mm->cek_status_berlian();
		$status = '';
		
		if($berlian == 'Y'){
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
		
		$data['margin'] = '';
		$data['saldo_awal'] = '';
		
		$data['pembelian'] = '';
		$data['stock_out'] = '';
		$data['tarik_reparasi'] = '';
		$data['tarik_pengadaan'] = '';
		$data['tarik_lain'] = '';
		$data['total_tarik'] = '';
		
		$data['stock_in'] = '';
		$data['kirim_reparasi'] = '';
		$data['kirim_pengadaan'] = '';
		$data['kirim_lain'] = '';
		$data['total_kirim'] = '';
		
		$data['saldo_akhir'] = '';
		
		$data['beli_rupiah'] = '';
		$data['beli_rata'] = '';
		
		$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column" style="text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/lapReparasi/pdf/'.$tanggal_transaksi.'" target="_blank"><i class="paperclip icon"></i> Download</a></div></div><table id="filter_data_tabel " class="ui celled table" cellspacing="0" width="100%"><thead><tr style="text-align:center"><th class="center aligned" rowspan="2">Keterangan</th><th class="center aligned" colspan="'.$colspan_head.'">Kelompok Emas</th><th class="center aligned" colspan="4">Hutang/Piutang Emas Bahan</th></tr><tr style="text-align:center">';
		
		$saldo_awal_rt = array();
		$pembelian_rt = array();
		$stockout_rt = array();
		$tarik_reparasi_rt = array();
		$tarik_pengadaan_rt = array();
		$tarik_lain_rt = array();
		$terima_all_rt = array();
		
		$stockin_rt = array();
		$kirim_reparasi_rt = array();
		$kirim_pengadaan_rt = array();
		$kirim_lain_rt = array();
		$kirim_all_rt = array();
		
		$saldo_akhir_rt = array();
		
		$pembelianrupiah_rt = array();
		$pembelianrupiah2_rt = array();
		
		$saldo_awal_dr = 0;
		$saldo_awal_dg = 0;
		$saldo_awal_tg = 0;
		$saldo_awal_tp = 0;
		
		$total_saldo_awal_rt = 0;
		$total_pembelian_rt = 0;
		$total_stockout_rt = 0;
		$total_tarikreparasi_rt = 0;
		$total_tarikpengadaan_rt = 0;
		$total_tariklain_rt = 0;
		$total_terima_rt = 0;
		
		$total_stockin_rt = 0;
		$total_kirimreparasi_rt = 0;
		$total_kirimpengadaan_rt = 0;
		$total_kirimlain_rt = 0;
		$total_kirim_rt = 0;
		
		$total_saldo_akhir_rt = 0;
		
		$total_pembelianrupiah_rt = 0;
		
		$saldo_akhir_dr = 0;
		$saldo_akhir_dg = 0;
		$saldo_akhir_tg = 0;
		$saldo_akhir_tp = 0;
		
		foreach($data_karat as $dk){
			$data['view'] .= '<th class="center aligned">'.$dk->karat_name.'</th>';
			$data['margin'] .= '<td></td>';
			$saldo_awal_rt[$dk->id] = 0;
			$pembelian_rt[$dk->id] = 0;
			$stockout_rt[$dk->id] = 0;
			$tarik_reparasi_rt[$dk->id] = 0;
			$tarik_pengadaan_rt[$dk->id] = 0;
			$tarik_lain_rt[$dk->id] = 0;
			$terima_all_rt[$dk->id] = 0;
			$stockin_rt[$dk->id] = 0;
			$kirim_reparasi_rt[$dk->id] = 0;
			$kirim_pengadaan_rt[$dk->id] = 0;
			$kirim_lain_rt[$dk->id] = 0;
			$kirim_all_rt[$dk->id] = 0;
			$saldo_akhir_rt[$dk->id] = 0;
			$pembelianrupiah_rt[$dk->id] = 0;
			$pembelianrupiah2_rt[$dk->id] = 0;
		}
		
		$data_bb_laprep = $this->mm->get_bb_laprep();
		$data_pembelian = $this->mt->get_pembelian_rekap_karat($tgl_transaksi,$tgl_transaksi);
		$data_stockout = $this->mp->get_rekap_stock_out($tgl_transaksi);
		
		$coa_from = '17-0002';
		$coa_to = '17-0001';
		
		$data_in_reparasi = $this->mm->get_rekap_lap_rep($coa_from,$coa_to,$tgl_transaksi,$tgl_sa);
		
		$coa_from = '17-0003';
		$coa_to = '17-0001';
		
		$coa_from2 = '17-0005';
		$coa_to2 = '17-0001';
		
		$data_in_pengadaan = $this->mm->get_rekap_lap_gros($coa_from,$coa_to,$coa_from2,$coa_to2,$tgl_transaksi,$tgl_sa);
		
		$coa_to = '17-0001';
		$data_in_lain1 = $this->mm->get_rekap_in_lap_rep($coa_to,$tgl_transaksi,$tgl_sa);
		$data_in_lain2 = $this->mm->get_rekap_in_reparasi($tgl_transaksi,$tgl_sa);
		$data_in_lain3 = $this->mm->get_rekap_in_pengadaan($tgl_transaksi,$tgl_sa);
		$data_in_lain4 = $this->mm->get_rekap_in_titip_pengadaan($tgl_transaksi,$tgl_sa);
		$data_in_lain5 = $this->mm->get_rekap_in_titipan($tgl_transaksi,$tgl_sa);
		
		$data_stockin = $this->mp->get_rekap_stock_in($tgl_transaksi);
		
		$coa_from = '17-0001';
		$coa_to = '17-0002';
		
		$data_out_reparasi = $this->mm->get_rekap_lap_rep($coa_from,$coa_to,$tgl_transaksi,$tgl_sa);
		
		$coa_from = '17-0001';
		$coa_to = '17-0003';
		
		$coa_from2 = '17-0001';
		$coa_to2 = '17-0005';
		
		$data_out_pengadaan = $this->mm->get_rekap_lap_gros($coa_from,$coa_to,$coa_from2,$coa_to2,$tgl_transaksi,$tgl_sa);
		
		$coa_from = '17-0001';
		$data_out_lain1 = $this->mm->get_rekap_out_lap_rep($coa_from,$tgl_transaksi,$tgl_sa);
		$data_out_lain2 = $this->mm->get_rekap_out_reparasi($tgl_transaksi,$tgl_sa);
		$data_out_lain3 = $this->mm->get_rekap_out_pengadaan($tgl_transaksi,$tgl_sa);
		$data_out_lain4 = $this->mm->get_rekap_out_titip_pengadaan($tgl_transaksi,$tgl_sa);
		$data_out_lain5 = $this->mm->get_rekap_out_titipan($tgl_transaksi,$tgl_sa);
		
		foreach($data_pembelian as $d){
			$pembelian_rt[$d->id_karat] = $d->berat;
			$total_pembelian_rt = $total_pembelian_rt + $d->berat;
			
			$terima_all_rt[$d->id_karat] = $terima_all_rt[$d->id_karat] + $d->berat;
			$total_terima_rt = $total_terima_rt + $d->berat;
			
			$saldo_akhir_rt[$d->id_karat] = $saldo_akhir_rt[$d->id_karat] + $d->berat;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt + $d->berat;
			
			$pembelianrupiah_rt[$d->id_karat] = $d->harga;
			$total_pembelianrupiah_rt = $total_pembelianrupiah_rt + $d->harga;
			
			$pembelianrupiah2_rt[$d->id_karat] = $d->harga / $d->berat;
		}
		
		foreach($data_stockout as $d){
			$stockout_rt[$d->id_karat] = $d->berat;
			$total_stockout_rt = $total_stockout_rt + $d->berat;
			
			$terima_all_rt[$d->id_karat] = $terima_all_rt[$d->id_karat] + $d->berat;
			$total_terima_rt = $total_terima_rt + $d->berat;
			
			$saldo_akhir_rt[$d->id_karat] = $saldo_akhir_rt[$d->id_karat] + $d->berat;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt + $d->berat;
		}
		
		foreach($data_in_reparasi as $d){
			$tarik_reparasi_rt[$d->idkarat] = $d->total;
			$total_tarikreparasi_rt = $total_tarikreparasi_rt + $d->total;
			
			$terima_all_rt[$d->idkarat] = $terima_all_rt[$d->idkarat] + $d->total;
			$total_terima_rt = $total_terima_rt + $d->total;
			
			$saldo_akhir_rt[$d->idkarat] = $saldo_akhir_rt[$d->idkarat] + $d->total;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt + $d->total;
		}
		
		foreach($data_in_pengadaan as $d){
			$tarik_pengadaan_rt[$d->idkarat] = $d->total;
			$total_tarikpengadaan_rt = $total_tarikpengadaan_rt + $d->total;
			
			$terima_all_rt[$d->idkarat] = $terima_all_rt[$d->idkarat] + $d->total;
			$total_terima_rt = $total_terima_rt + $d->total;
			
			$saldo_akhir_rt[$d->idkarat] = $saldo_akhir_rt[$d->idkarat] + $d->total;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt + $d->total;
		}
		
		foreach($data_in_lain1 as $d){
			$tarik_lain_rt[$d->idkarat] = $d->total;
			$total_tariklain_rt = $total_tariklain_rt + $d->total;
			
			$terima_all_rt[$d->idkarat] = $terima_all_rt[$d->idkarat] + $d->total;
			$total_terima_rt = $total_terima_rt + $d->total;
			
			$saldo_akhir_rt[$d->idkarat] = $saldo_akhir_rt[$d->idkarat] + $d->total;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt + $d->total;
		}
		
		//
		
		foreach($data_stockin as $d){
			$stockin_rt[$d->id_karat] = $d->berat;
			$total_stockin_rt = $total_stockin_rt + $d->berat;
			
			$kirim_all_rt[$d->id_karat] = $kirim_all_rt[$d->id_karat] + $d->berat;
			$total_kirim_rt = $total_kirim_rt + $d->berat;
			
			$saldo_akhir_rt[$d->id_karat] = $saldo_akhir_rt[$d->id_karat] - $d->berat;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt - $d->berat;
		}
		
		foreach($data_out_reparasi as $d){
			$kirim_reparasi_rt[$d->idkarat] = $d->total;
			$total_kirimreparasi_rt = $total_kirimreparasi_rt + $d->total;
			
			$kirim_all_rt[$d->idkarat] = $kirim_all_rt[$d->idkarat] + $d->total;
			$total_kirim_rt = $total_kirim_rt + $d->total;
			
			$saldo_akhir_rt[$d->idkarat] = $saldo_akhir_rt[$d->idkarat] - $d->total;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt - $d->total;
		}
		
		foreach($data_out_pengadaan as $d){
			$kirim_pengadaan_rt[$d->idkarat] = $d->total;
			$total_kirimpengadaan_rt = $total_kirimpengadaan_rt + $d->total;
			
			$kirim_all_rt[$d->idkarat] = $kirim_all_rt[$d->idkarat] + $d->total;
			$total_kirim_rt = $total_kirim_rt + $d->total;
			
			$saldo_akhir_rt[$d->idkarat] = $saldo_akhir_rt[$d->idkarat] - $d->total;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt - $d->total;
		}
		
		foreach($data_out_lain1 as $d){
			$kirim_lain_rt[$d->idkarat] = $d->total;
			$total_kirimlain_rt = $total_kirimlain_rt + $d->total;
			
			$kirim_all_rt[$d->idkarat] = $kirim_all_rt[$d->idkarat] + $d->total;
			$total_kirim_rt = $total_kirim_rt + $d->total;
			
			$saldo_akhir_rt[$d->idkarat] = $saldo_akhir_rt[$d->idkarat] - $d->total;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt - $d->total;
		}
		
		foreach($data_bb_laprep as $d){
			if($d->type == 'SRT'){
				$saldo_awal_rt[$d->idkarat] = $saldo_awal_rt[$d->idkarat] + $d->beginningbalance;
			}else if($d->type == 'SDR'){
				$saldo_awal_dr = $saldo_awal_dr + $d->beginningbalance;
			}else if($d->type == 'SDG'){
				$saldo_awal_dg = $saldo_awal_dg + $d->beginningbalance;
			}else if($d->type == 'TDG'){
				$saldo_awal_tg = $saldo_awal_tg + $d->beginningbalance;
			}
		}
		
		foreach($data_bb_laprep as $d){
			if($d->accountnumberint < 179999){
				if($d->type == 'SRT'){
					$acc_number = $d->accountnumber;
					foreach($data_karat as $dk){
						$id_kurs = $dk->id;
						
						$saldo_awal_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$tgl_transaksi,$id_kurs);
						$saldo_awal_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$tgl_transaksi,$id_kurs);
						
						foreach($saldo_awal_d as $mtyd){
							$saldo_awal_rt[$id_kurs] = $saldo_awal_rt[$id_kurs] + $mtyd->total_mutasi;
						}
						
						foreach($saldo_awal_k as $mtyk){
							$saldo_awal_rt[$id_kurs] = $saldo_awal_rt[$id_kurs] - $mtyk->total_mutasi;
						}
						
						$total_saldo_awal_rt = $total_saldo_awal_rt + $saldo_awal_rt[$id_kurs];
						
						//SALDO AWAL
						$saldo_awal = $saldo_awal_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['saldo_awal'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td>';
						
						//SALDO AKHIR
						$saldo_akhir_rt[$id_kurs] = $saldo_akhir_rt[$id_kurs] + $saldo_awal_rt[$id_kurs];
						$total_saldo_akhir_rt = $total_saldo_akhir_rt + $saldo_awal_rt[$id_kurs];
						
						$saldo_awal = $saldo_akhir_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['saldo_akhir'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
						
						//PEMBELIAN
						$saldo_awal = $pembelian_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['pembelian'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
						
						//STOCKOUT
						$saldo_awal = $stockout_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['stock_out'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
						
						//TARIK REPARASI
						$saldo_awal = $tarik_reparasi_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['tarik_reparasi'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
						
						//TARIK PENGADAAN
						$saldo_awal = $tarik_pengadaan_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['tarik_pengadaan'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
						
						//TARIK LAIN
						$saldo_awal = $tarik_lain_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['tarik_lain'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
						
						//TOTAL TERIMA
						$saldo_awal = $terima_all_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['total_tarik'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
						
						//
						
						//STOCKIN
						$saldo_awal = $stockin_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['stock_in'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
						
						//KIRIM REPARASI
						$saldo_awal = $kirim_reparasi_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['kirim_reparasi'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
						
						//KIRIM PENGADAAN
						$saldo_awal = $kirim_pengadaan_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['kirim_pengadaan'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
						
						//KIRIM LAIN
						$saldo_awal = $kirim_lain_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['kirim_lain'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
						
						//TOTAL KIRIM
						$saldo_awal = $kirim_all_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['total_kirim'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
						
						//PEMBELIAN RUPIAH
						$saldo_awal = $pembelianrupiah_rt[$id_kurs];
						if(number_format($saldo_awal,0) == '0'){
							$sa_tulis = '-';
						}else{
							$sa_tulis = number_format($saldo_awal,0);
						}
						$data['beli_rupiah'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td>';
						
						//PEMBELIAN RUPIAH RATA2
						$saldo_awal = $pembelianrupiah2_rt[$id_kurs];
						if(number_format($saldo_awal,0) == '0'){
							$sa_tulis = '-';
						}else{
							$sa_tulis = number_format($saldo_awal,0);
						}
						$data['beli_rata'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td>';
					}
					
					//SALDO AWAL
					$saldo_awal = $total_saldo_awal_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['saldo_awal'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td>';
					
					//SALDO AKHIR
					$saldo_awal = $total_saldo_akhir_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['saldo_akhir'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
					
					//PEMBELIAN
					$saldo_awal = $total_pembelian_rt;
					$sa_tulis = $this->weight_format($saldo_awal);
					$data['pembelian'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					$data['pembelian'] .= '<td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td></tr>';
					
					//STOCKOUT
					$saldo_awal = $total_stockout_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['stock_out'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					$data['stock_out'] .= '<td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td></tr>';
					
					//TARIK REPARASI
					$saldo_awal = $total_tarikreparasi_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['tarik_reparasi'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					$data['tarik_reparasi'] .= '<td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td></tr>';
					
					//TARIK PENGADAAN
					$saldo_awal = $total_tarikpengadaan_rt;
					$sa_tulis = $this->weight_format($saldo_awal);
					$data['tarik_pengadaan'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					$data['tarik_pengadaan'] .= '<td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td></tr>';
					
					//TARIK LAIN
					$saldo_awal = $total_tariklain_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['tarik_lain'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					
					//TOTAL TERIMA
					$saldo_awal = $total_terima_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['total_tarik'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
					
					//
					
					//STOCKIN
					$saldo_awal = $total_stockin_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['stock_in'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					$data['stock_in'] .= '<td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td></tr>';
					
					//KIRIM REPARASI
					$saldo_awal = $total_kirimreparasi_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['kirim_reparasi'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					$data['kirim_reparasi'] .= '<td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td></tr>';
					
					//KIRIM PENGADAAN
					$saldo_awal = $total_kirimpengadaan_rt;
					$sa_tulis = $this->weight_format($saldo_awal);
					$data['kirim_pengadaan'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					$data['kirim_pengadaan'] .= '<td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td><td class="right aligned">-</td></tr>';
					
					//KIRIM LAIN
					$saldo_awal = $total_kirimlain_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['kirim_lain'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
					
					//TOTAL KIRIM
					$saldo_awal = $total_kirim_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['total_kirim'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
					
					//PEMBELIAN RUPIAH
					$saldo_awal = $total_pembelianrupiah_rt;
					if(number_format($saldo_awal,0) == '0'){
						$sa_tulis = '-';
					}else{
						$sa_tulis = number_format($saldo_awal,0);
					}
					$data['beli_rupiah'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td>';
					$data['beli_rupiah'] .= '<td></td><td></td><td></td><td></td></tr>';
					
					//PEMBELIAN RUPIAH RATA2
					if($total_pembelianrupiah_rt == 0){
						$saldo_awal = 0;
					}else{
						$saldo_awal = $total_pembelianrupiah_rt / $total_pembelian_rt;
					}
					
					if(number_format($saldo_awal,0) == '0'){
						$sa_tulis = '-';
					}else{
						$sa_tulis = number_format($saldo_awal,0);
					}
					$data['beli_rata'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td>';
					$data['beli_rata'] .= '<td></td><td></td><td></td><td></td></tr>';
				}else{
					$acc_number = $d->accountnumber;
					$id_kurs = 1;
					
					if($d->type == 'SDR'){
						$tabel_name = 'gold_mutasi_reparasi';
					}else if($d->type == 'SDG'){
						$tabel_name = 'gold_mutasi_pengadaan';
					}else if($d->type == 'TDG'){
						$tabel_name = 'gold_mutasi_titip_pengadaan';
					}
					
					$saldo_awal_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$tgl_transaksi);
					$saldo_awal_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$tgl_transaksi);
					
					foreach($saldo_awal_d as $mtyd){
						if($d->type == 'SDR'){
							$saldo_awal_dr = $saldo_awal_dr + $mtyd->total_mutasi;
						}else if($d->type == 'SDG'){
							$saldo_awal_dg = $saldo_awal_dg + $mtyd->total_mutasi;
						}else if($d->type == 'TDG'){
							$saldo_awal_tg = $saldo_awal_tg + $mtyd->total_mutasi;
						}
					}
					
					foreach($saldo_awal_k as $mtyk){
						if($d->type == 'SDR'){
							$saldo_awal_dr = $saldo_awal_dr - $mtyk->total_mutasi;
						}else if($d->type == 'SDG'){
							$saldo_awal_dg = $saldo_awal_dg - $mtyk->total_mutasi;
						}else if($d->type == 'TDG'){
							$saldo_awal_tg = $saldo_awal_tg - $mtyk->total_mutasi;
						}
					}
					
					if($d->type == 'SDR'){
						$saldo_awal = $saldo_awal_dr;
						$saldo_akhir_dr = $saldo_akhir_dr + $saldo_awal;
					}else if($d->type == 'SDG'){
						$saldo_awal = $saldo_awal_dg;
						$saldo_akhir_dg = $saldo_akhir_dg + $saldo_awal;
					}else if($d->type == 'TDG'){
						$saldo_awal = $saldo_awal_tg;
						$saldo_akhir_tg = $saldo_akhir_tg + $saldo_awal;
					}
					
					$sa_tulis = $this->weight_format($saldo_awal);
					$sa_tulis2 = $this->weight_format($saldo_awal);
					$data['saldo_awal'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td>';
					
					if($d->type == 'SDR'){
						$saldo_awal = $data_in_lain2[0]->total;
						$saldo_awal = (double)$saldo_awal;
						$saldo_akhir_dr = $saldo_akhir_dr + $saldo_awal;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['tarik_lain'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
						$data['total_tarik'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
						
						$saldo_awal = $data_out_lain2[0]->total;
						$saldo_awal = (double)$saldo_awal;
						$saldo_akhir_dr = $saldo_akhir_dr - $saldo_awal;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['kirim_lain'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
						$data['total_kirim'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
						
						$saldo_awal = $saldo_akhir_dr;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['saldo_akhir'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
					}else if($d->type == 'SDG'){
						$saldo_awal = $data_in_lain3[0]->total;
						$saldo_awal = (double)$saldo_awal;
						$saldo_akhir_dg = $saldo_akhir_dg + $saldo_awal;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['tarik_lain'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
						$data['total_tarik'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
						
						$saldo_awal = $data_out_lain3[0]->total;
						$saldo_awal = (double)$saldo_awal;
						$saldo_akhir_dg = $saldo_akhir_dg - $saldo_awal;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['kirim_lain'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
						$data['total_kirim'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
						
						$saldo_awal = $saldo_akhir_dg;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['saldo_akhir'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
					}else if($d->type == 'TDG'){
						$saldo_awal = $data_in_lain4[0]->total;
						$saldo_awal = (double)$saldo_awal;
						$saldo_akhir_tg = $saldo_akhir_tg + $saldo_awal;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['tarik_lain'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
						$data['total_tarik'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
						
						$saldo_awal = $data_out_lain4[0]->total;
						$saldo_awal = (double)$saldo_awal;
						$saldo_akhir_tg = $saldo_akhir_tg - $saldo_awal;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['kirim_lain'] .= '<td class="right aligned">'.$sa_tulis.'</td>';
						$data['total_kirim'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
						
						$saldo_awal = $saldo_akhir_tg;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['saldo_akhir'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td>';
					}
				}
			}
		}
		
		$saldo_awal = $data_in_lain5[0]->total;
		$saldo_awal = (double)$saldo_awal;
		$saldo_akhir_tp = $saldo_akhir_tp + $saldo_awal;
		$sa_tulis = $this->weight_format($saldo_awal);
		$data['tarik_lain'] .= '<td class="right aligned">'.$sa_tulis.'</td></tr>';
		$data['total_tarik'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td></tr>';
		
		$saldo_awal = $data_out_lain5[0]->total;
		$saldo_awal = (double)$saldo_awal;
		$saldo_akhir_tp = $saldo_akhir_tp - $saldo_awal;
		$sa_tulis = $this->weight_format($saldo_awal);
		$data['kirim_lain'] .= '<td class="right aligned">'.$sa_tulis.'</td></tr>';
		$data['total_kirim'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td></tr>';
		
		$coa_from = 220001;
		$coa_to = 229999;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		if(count($kas_account) > 0){
			foreach($kas_account as $ka){
				$acc_number = $ka->accountnumber;
				$id_kurs = 1;
				
				$sa = $this->mm->get_report_gr_beginning_balance($acc_number);
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_bykarat($acc_number,$tgl_transaksi,$id_kurs);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_bykarat($acc_number,$tgl_transaksi,$id_kurs);
				
				foreach($saldo_awal_d as $mtyd){
					$sa = $sa - $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$sa = $sa + $mtyk->total_mutasi;
				}	

				$saldo_awal_tp = $saldo_awal_tp + $sa;			
			}
			
			$saldo_akhir_tp = $saldo_akhir_tp + $saldo_awal_tp;
			$sa_tulis = $this->weight_format($saldo_awal_tp);
			$data['saldo_awal'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td></tr>';
		}else{
			$data['saldo_awal'] .= '<td class="right aligned td-bold">-</td></tr>';
		}
		
		$sa_tulis = $this->weight_format($saldo_akhir_tp);
		$data['saldo_akhir'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td></tr>';
		
		$data['margin'] .= '<td></td><td></td><td></td><td></td><td></td>';
		$data['view'] .= '<th class="center aligned">Total</th>';
		$data['view'] .= '<th class="center aligned">Di Reparasi</th><th class="center aligned">Saldo Pgdn</th><th class="center aligned">Titipan di Pgdn</th><th class="center aligned">Titipan Pelanggan</th>';
		$data['view'] .= '</tr></thead><tbody>';
		
		//                       SALDO AWAL                       //
		$data['view'] .= '<tr><td class="td-bold">Saldo Awal</td>';
		$data['view'] .= $data['saldo_awal'];
		
		$data['view'] .= '<tr><td class="td-bold">Penerimaan (+)</td>';
		$data['view'] .= $data['margin'];
		
		//                       PEMBELIAN                       //
		$data['view'] .= '<tr><td>Pembelian</td>';
		$data['view'] .= $data['pembelian'];
		
		//                       STOCK OUT                       //
		$data['view'] .= '<tr><td>Stock Out</td>';
		$data['view'] .= $data['stock_out'];
		
		//                       TARIK REPARASI                       //
		$data['view'] .= '<tr><td>Penarikan Barang Reparasi</td>';
		$data['view'] .= $data['tarik_reparasi'];
		
		//                       TARIK PENGADAAN                       //
		$data['view'] .= '<tr><td>Penarikan Barang Pengadaan</td>';
		$data['view'] .= $data['tarik_pengadaan'];
		
		//                       LAIN LAIN IN                       //
		$data['view'] .= '<tr><td>Lain-lain</td>';
		$data['view'] .= $data['tarik_lain'];
		
		//                       TOTAL PENERIMAAN                       //
		$data['view'] .= '<tr><td class="td-bold">Total Penerimaan</td>';
		$data['view'] .= $data['total_tarik'];
		
		$data['view'] .= '<tr><td><span style="visibility:hidden">-</span></td>';
		$data['view'] .= $data['margin'];
		
		//                       PENGELUARAN                       //
		$data['view'] .= '<tr><td class="td-bold">Pengeluaran (-)</td>';
		$data['view'] .= $data['margin'];
		
		//                       STOCK IN                       //
		$data['view'] .= '<tr><td>Ke Pajangan</td>';
		$data['view'] .= $data['stock_in'];
		
		//                       KIRIM REPARASI                       //
		$data['view'] .= '<tr><td>Pengiriman ke Dept. Reparasi</td>';
		$data['view'] .= $data['kirim_reparasi'];
		
		//                       KIRIM PENGADAAN                       //
		$data['view'] .= '<tr><td>Kirim ke Dept. Pengadaan</td>';
		$data['view'] .= $data['kirim_pengadaan'];
		
		//                       LAIN LAIN OUT                       //
		$data['view'] .= '<tr><td>Lain-lain</td>';
		$data['view'] .= $data['kirim_lain'];
		
		//                       TOTAL PENGELUARAN                       //
		$data['view'] .= '<tr><td class="td-bold">Total Pengeluaran</td>';
		$data['view'] .= $data['total_kirim'];
		
		//                       SALDO AKHIR                       //
		$data['view'] .= '<tr><td class="td-bold">Saldo Akhir</td>';
		$data['view'] .= $data['saldo_akhir'];
		
		$data['view'] .= '<tr><td><span style="visibility:hidden">-</span></td>';
		$data['view'] .= $data['margin'];
		
		/*--- PEMBELIAN ---*/
		
		$data['view'] .= '<tr><td class="td-bold">Data Pembelian</td>';
		$data['view'] .= $data['margin'];
		
		$data['view'] .= '<tr><td class="align-middle">Pembelian Dalam Rupiah</td>';
		$data['view'] .= $data['beli_rupiah'];
		
		$data['view'] .= '<tr><td class="align-middle">Rata2 per Gram</td>';
		$data['view'] .= $data['beli_rata'];
		
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
		$colspan_head2 = $colspan_head+1;
		
		$sitename = $this->mm->get_site_name();
		
		$data['margin'] = '';
		$data['saldo_awal'] = '';
		
		$data['pembelian'] = '';
		$data['stock_out'] = '';
		$data['tarik_reparasi'] = '';
		$data['tarik_pengadaan'] = '';
		$data['tarik_lain'] = '';
		$data['total_tarik'] = '';
		
		$data['stock_in'] = '';
		$data['kirim_reparasi'] = '';
		$data['kirim_pengadaan'] = '';
		$data['kirim_lain'] = '';
		$data['total_kirim'] = '';
		
		$data['saldo_akhir'] = '';
		
		$data['beli_rupiah'] = '';
		$data['beli_rata'] = '';
		
		$data['view'] = '<table class="lap_pdf_4" cellspacing="0" width="100%"><tr><td colspan="2" style="text-align:left; border:none"><img src="'.base_url().'assets/images/branding/brand.png" style="width:220px"></td><td colspan="'.$colspan_head2.'" style="text-align:center;font-size:15.5px;border:none;font-weight:bold">Laporan Emas Reparasi Harian <br>Cabang '.$sitename.'</td><td colspan="3" style="text-align:right;font-size:15.5px;border:none;font-weight:bold"><br>'.$hari_tulis.', '.$tanggal_aktif.'</td></tr><thead><tr style="text-align:center"><th class="center aligned" rowspan="2" style="width:220px">Keterangan</th><th class="center aligned" colspan="'.$colspan_head.'">Kelompok Emas</th><th class="center aligned" colspan="4">Hutang/Piutang Emas Bahan</th></tr><tr style="text-align:center">';
		
		$saldo_awal_rt = array();
		$pembelian_rt = array();
		$stockout_rt = array();
		$tarik_reparasi_rt = array();
		$tarik_pengadaan_rt = array();
		$tarik_lain_rt = array();
		$terima_all_rt = array();
		
		$stockin_rt = array();
		$kirim_reparasi_rt = array();
		$kirim_pengadaan_rt = array();
		$kirim_lain_rt = array();
		$kirim_all_rt = array();
		
		$saldo_akhir_rt = array();
		
		$pembelianrupiah_rt = array();
		$pembelianrupiah2_rt = array();
		
		$saldo_awal_dr = 0;
		$saldo_awal_dg = 0;
		$saldo_awal_tg = 0;
		$saldo_awal_tp = 0;
		
		$total_saldo_awal_rt = 0;
		$total_pembelian_rt = 0;
		$total_stockout_rt = 0;
		$total_tarikreparasi_rt = 0;
		$total_tarikpengadaan_rt = 0;
		$total_tariklain_rt = 0;
		$total_terima_rt = 0;
		
		$total_stockin_rt = 0;
		$total_kirimreparasi_rt = 0;
		$total_kirimpengadaan_rt = 0;
		$total_kirimlain_rt = 0;
		$total_kirim_rt = 0;
		
		$total_saldo_akhir_rt = 0;
		
		$total_pembelianrupiah_rt = 0;
		
		$saldo_akhir_dr = 0;
		$saldo_akhir_dg = 0;
		$saldo_akhir_tg = 0;
		$saldo_akhir_tp = 0;
		
		foreach($data_karat as $dk){
			$data['view'] .= '<th class="center aligned" style="width:110px;">'.$dk->karat_name.'</th>';
			$data['margin'] .= '<td></td>';
			$saldo_awal_rt[$dk->id] = 0;
			$pembelian_rt[$dk->id] = 0;
			$stockout_rt[$dk->id] = 0;
			$tarik_reparasi_rt[$dk->id] = 0;
			$tarik_pengadaan_rt[$dk->id] = 0;
			$tarik_lain_rt[$dk->id] = 0;
			$terima_all_rt[$dk->id] = 0;
			$stockin_rt[$dk->id] = 0;
			$kirim_reparasi_rt[$dk->id] = 0;
			$kirim_pengadaan_rt[$dk->id] = 0;
			$kirim_lain_rt[$dk->id] = 0;
			$kirim_all_rt[$dk->id] = 0;
			$saldo_akhir_rt[$dk->id] = 0;
			$pembelianrupiah_rt[$dk->id] = 0;
			$pembelianrupiah2_rt[$dk->id] = 0;
		}
		
		$data_bb_laprep = $this->mm->get_bb_laprep();
		$data_pembelian = $this->mt->get_pembelian_rekap_karat($tgl_transaksi,$tgl_transaksi);
		$data_stockout = $this->mp->get_rekap_stock_out($tgl_transaksi);
		
		$coa_from = '17-0002';
		$coa_to = '17-0001';
		
		$data_in_reparasi = $this->mm->get_rekap_lap_rep($coa_from,$coa_to,$tgl_transaksi,$tgl_sa);
		
		$coa_from = '17-0003';
		$coa_to = '17-0001';
		
		$coa_from2 = '17-0005';
		$coa_to2 = '17-0001';
		
		$data_in_pengadaan = $this->mm->get_rekap_lap_gros($coa_from,$coa_to,$coa_from2,$coa_to2,$tgl_transaksi,$tgl_sa);
		
		$coa_to = '17-0001';
		$data_in_lain1 = $this->mm->get_rekap_in_lap_rep($coa_to,$tgl_transaksi,$tgl_sa);
		$data_in_lain2 = $this->mm->get_rekap_in_reparasi($tgl_transaksi,$tgl_sa);
		$data_in_lain3 = $this->mm->get_rekap_in_pengadaan($tgl_transaksi,$tgl_sa);
		$data_in_lain4 = $this->mm->get_rekap_in_titip_pengadaan($tgl_transaksi,$tgl_sa);
		$data_in_lain5 = $this->mm->get_rekap_in_titipan($tgl_transaksi,$tgl_sa);
		
		$data_stockin = $this->mp->get_rekap_stock_in($tgl_transaksi);
		
		$coa_from = '17-0001';
		$coa_to = '17-0002';
		
		$data_out_reparasi = $this->mm->get_rekap_lap_rep($coa_from,$coa_to,$tgl_transaksi,$tgl_sa);
		
		$coa_from = '17-0001';
		$coa_to = '17-0003';
		
		$data_out_pengadaan = $this->mm->get_rekap_lap_rep($coa_from,$coa_to,$tgl_transaksi,$tgl_sa);
		
		$coa_from = '17-0001';
		$data_out_lain1 = $this->mm->get_rekap_out_lap_rep($coa_from,$tgl_transaksi,$tgl_sa);
		$data_out_lain2 = $this->mm->get_rekap_out_reparasi($tgl_transaksi,$tgl_sa);
		$data_out_lain3 = $this->mm->get_rekap_out_pengadaan($tgl_transaksi,$tgl_sa);
		$data_out_lain4 = $this->mm->get_rekap_out_titip_pengadaan($tgl_transaksi,$tgl_sa);
		$data_out_lain5 = $this->mm->get_rekap_out_titipan($tgl_transaksi,$tgl_sa);
		
		foreach($data_pembelian as $d){
			$pembelian_rt[$d->id_karat] = $d->berat;
			$total_pembelian_rt = $total_pembelian_rt + $d->berat;
			
			$terima_all_rt[$d->id_karat] = $terima_all_rt[$d->id_karat] + $d->berat;
			$total_terima_rt = $total_terima_rt + $d->berat;
			
			$saldo_akhir_rt[$d->id_karat] = $saldo_akhir_rt[$d->id_karat] + $d->berat;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt + $d->berat;
			
			$pembelianrupiah_rt[$d->id_karat] = $d->harga;
			$total_pembelianrupiah_rt = $total_pembelianrupiah_rt + $d->harga;
			
			$pembelianrupiah2_rt[$d->id_karat] = $d->harga / $d->berat;
		}
		
		foreach($data_stockout as $d){
			$stockout_rt[$d->id_karat] = $d->berat;
			$total_stockout_rt = $total_stockout_rt + $d->berat;
			
			$terima_all_rt[$d->id_karat] = $terima_all_rt[$d->id_karat] + $d->berat;
			$total_terima_rt = $total_terima_rt + $d->berat;
			
			$saldo_akhir_rt[$d->id_karat] = $saldo_akhir_rt[$d->id_karat] + $d->berat;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt + $d->berat;
		}
		
		foreach($data_in_reparasi as $d){
			$tarik_reparasi_rt[$d->idkarat] = $d->total;
			$total_tarikreparasi_rt = $total_tarikreparasi_rt + $d->total;
			
			$terima_all_rt[$d->idkarat] = $terima_all_rt[$d->idkarat] + $d->total;
			$total_terima_rt = $total_terima_rt + $d->total;
			
			$saldo_akhir_rt[$d->idkarat] = $saldo_akhir_rt[$d->idkarat] + $d->total;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt + $d->total;
		}
		
		foreach($data_in_pengadaan as $d){
			$tarik_pengadaan_rt[$d->idkarat] = $d->total;
			$total_tarikpengadaan_rt = $total_tarikpengadaan_rt + $d->total;
			
			$terima_all_rt[$d->idkarat] = $terima_all_rt[$d->idkarat] + $d->total;
			$total_terima_rt = $total_terima_rt + $d->total;
			
			$saldo_akhir_rt[$d->idkarat] = $saldo_akhir_rt[$d->idkarat] + $d->total;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt + $d->total;
		}
		
		foreach($data_in_lain1 as $d){
			$tarik_lain_rt[$d->idkarat] = $d->total;
			$total_tariklain_rt = $total_tariklain_rt + $d->total;
			
			$terima_all_rt[$d->idkarat] = $terima_all_rt[$d->idkarat] + $d->total;
			$total_terima_rt = $total_terima_rt + $d->total;
			
			$saldo_akhir_rt[$d->idkarat] = $saldo_akhir_rt[$d->idkarat] + $d->total;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt + $d->total;
		}
		
		//
		
		foreach($data_stockin as $d){
			$stockin_rt[$d->id_karat] = $d->berat;
			$total_stockin_rt = $total_stockin_rt + $d->berat;
			
			$kirim_all_rt[$d->id_karat] = $kirim_all_rt[$d->id_karat] + $d->berat;
			$total_kirim_rt = $total_kirim_rt + $d->berat;
			
			$saldo_akhir_rt[$d->id_karat] = $saldo_akhir_rt[$d->id_karat] - $d->berat;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt - $d->berat;
		}
		
		foreach($data_out_reparasi as $d){
			$kirim_reparasi_rt[$d->idkarat] = $d->total;
			$total_kirimreparasi_rt = $total_kirimreparasi_rt + $d->total;
			
			$kirim_all_rt[$d->idkarat] = $kirim_all_rt[$d->idkarat] + $d->total;
			$total_kirim_rt = $total_kirim_rt + $d->total;
			
			$saldo_akhir_rt[$d->idkarat] = $saldo_akhir_rt[$d->idkarat] - $d->total;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt - $d->total;
		}
		
		foreach($data_out_pengadaan as $d){
			$kirim_pengadaan_rt[$d->idkarat] = $d->total;
			$total_kirimpengadaan_rt = $total_kirimpengadaan_rt + $d->total;
			
			$kirim_all_rt[$d->idkarat] = $kirim_all_rt[$d->idkarat] + $d->total;
			$total_kirim_rt = $total_kirim_rt + $d->total;
			
			$saldo_akhir_rt[$d->idkarat] = $saldo_akhir_rt[$d->idkarat] - $d->total;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt - $d->total;
		}
		
		foreach($data_out_lain1 as $d){
			$kirim_lain_rt[$d->idkarat] = $d->total;
			$total_kirimlain_rt = $total_kirimlain_rt + $d->total;
			
			$kirim_all_rt[$d->idkarat] = $kirim_all_rt[$d->idkarat] + $d->total;
			$total_kirim_rt = $total_kirim_rt + $d->total;
			
			$saldo_akhir_rt[$d->idkarat] = $saldo_akhir_rt[$d->idkarat] - $d->total;
			$total_saldo_akhir_rt = $total_saldo_akhir_rt - $d->total;
		}
		
		foreach($data_bb_laprep as $d){
			if($d->type == 'SRT'){
				$saldo_awal_rt[$d->idkarat] = $saldo_awal_rt[$d->idkarat] + $d->beginningbalance;
			}else if($d->type == 'SDR'){
				$saldo_awal_dr = $saldo_awal_dr + $d->beginningbalance;
			}else if($d->type == 'SDG'){
				$saldo_awal_dg = $saldo_awal_dg + $d->beginningbalance;
			}else if($d->type == 'TDG'){
				$saldo_awal_tg = $saldo_awal_tg + $d->beginningbalance;
			}
		}
		
		foreach($data_bb_laprep as $d){
			if($d->accountnumberint < 179999){
				if($d->type == 'SRT'){
					$acc_number = $d->accountnumber;
					foreach($data_karat as $dk){
						$id_kurs = $dk->id;
						
						$saldo_awal_d = $this->mm->get_report_mutasi_gr_yesterday_d_bykurs($acc_number,$tgl_transaksi,$id_kurs);
						$saldo_awal_k = $this->mm->get_report_mutasi_gr_yesterday_k_bykurs($acc_number,$tgl_transaksi,$id_kurs);
						
						foreach($saldo_awal_d as $mtyd){
							$saldo_awal_rt[$id_kurs] = $saldo_awal_rt[$id_kurs] + $mtyd->total_mutasi;
						}
						
						foreach($saldo_awal_k as $mtyk){
							$saldo_awal_rt[$id_kurs] = $saldo_awal_rt[$id_kurs] - $mtyk->total_mutasi;
						}
						
						$total_saldo_awal_rt = $total_saldo_awal_rt + $saldo_awal_rt[$id_kurs];
						
						//SALDO AWAL
						$saldo_awal = $saldo_awal_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['saldo_awal'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td>';
						
						//SALDO AKHIR
						$saldo_akhir_rt[$id_kurs] = $saldo_akhir_rt[$id_kurs] + $saldo_awal_rt[$id_kurs];
						$total_saldo_akhir_rt = $total_saldo_akhir_rt + $saldo_awal_rt[$id_kurs];
						
						$saldo_awal = $saldo_akhir_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['saldo_akhir'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
						
						//PEMBELIAN
						$saldo_awal = $pembelian_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['pembelian'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
						
						//STOCKOUT
						$saldo_awal = $stockout_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['stock_out'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
						
						//TARIK REPARASI
						$saldo_awal = $tarik_reparasi_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['tarik_reparasi'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
						
						//TARIK PENGADAAN
						$saldo_awal = $tarik_pengadaan_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['tarik_pengadaan'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
						
						//TARIK LAIN
						$saldo_awal = $tarik_lain_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['tarik_lain'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
						
						//TOTAL TERIMA
						$saldo_awal = $terima_all_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['total_tarik'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
						
						//
						
						//STOCKIN
						$saldo_awal = $stockin_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['stock_in'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
						
						//KIRIM REPARASI
						$saldo_awal = $kirim_reparasi_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['kirim_reparasi'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
						
						//KIRIM PENGADAAN
						$saldo_awal = $kirim_pengadaan_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['kirim_pengadaan'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
						
						//KIRIM LAIN
						$saldo_awal = $kirim_lain_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['kirim_lain'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
						
						//TOTAL KIRIM
						$saldo_awal = $kirim_all_rt[$id_kurs];
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['total_kirim'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
						
						//PEMBELIAN RUPIAH
						$saldo_awal = $pembelianrupiah_rt[$id_kurs];
						if(number_format($saldo_awal,0) == '0'){
							$sa_tulis = '-';
						}else{
							$sa_tulis = number_format($saldo_awal,0);
						}
						$data['beli_rupiah'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td>';
						
						//PEMBELIAN RUPIAH RATA2
						$saldo_awal = $pembelianrupiah2_rt[$id_kurs];
						if(number_format($saldo_awal,0) == '0'){
							$sa_tulis = '-';
						}else{
							$sa_tulis = number_format($saldo_awal,0);
						}
						$data['beli_rata'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td>';
					}
					
					//SALDO AWAL
					$saldo_awal = $total_saldo_awal_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['saldo_awal'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td>';
					
					//SALDO AKHIR
					$saldo_awal = $total_saldo_akhir_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['saldo_akhir'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
					
					//PEMBELIAN
					$saldo_awal = $total_pembelian_rt;
					$sa_tulis = $this->weight_format($saldo_awal);
					$data['pembelian'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					$data['pembelian'] .= '<td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td></tr>';
					
					//STOCKOUT
					$saldo_awal = $total_stockout_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['stock_out'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					$data['stock_out'] .= '<td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td></tr>';
					
					//TARIK REPARASI
					$saldo_awal = $total_tarikreparasi_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['tarik_reparasi'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					$data['tarik_reparasi'] .= '<td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td></tr>';
					
					//TARIK PENGADAAN
					$saldo_awal = $total_tarikpengadaan_rt;
					$sa_tulis = $this->weight_format($saldo_awal);
					$data['tarik_pengadaan'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					$data['tarik_pengadaan'] .= '<td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td></tr>';
					
					//TARIK LAIN
					$saldo_awal = $total_tariklain_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['tarik_lain'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					
					//TOTAL TERIMA
					$saldo_awal = $total_terima_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['total_tarik'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
					
					//
					
					//STOCKIN
					$saldo_awal = $total_stockin_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['stock_in'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					$data['stock_in'] .= '<td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td></tr>';
					
					//KIRIM REPARASI
					$saldo_awal = $total_kirimreparasi_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['kirim_reparasi'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					$data['kirim_reparasi'] .= '<td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td></tr>';
					
					//KIRIM PENGADAAN
					$saldo_awal = $total_kirimpengadaan_rt;
					$sa_tulis = $this->weight_format($saldo_awal);
					$data['kirim_pengadaan'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					$data['kirim_pengadaan'] .= '<td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td><td class="right-aligned">-</td></tr>';
					
					//KIRIM LAIN
					$saldo_awal = $total_kirimlain_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['kirim_lain'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
					
					//TOTAL KIRIM
					$saldo_awal = $total_kirim_rt;
					$sa_tulis = $this->weight_format($saldo_awal);	
					$data['total_kirim'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
					
					//PEMBELIAN RUPIAH
					$saldo_awal = $total_pembelianrupiah_rt;
					if(number_format($saldo_awal,0) == '0'){
						$sa_tulis = '-';
					}else{
						$sa_tulis = number_format($saldo_awal,0);
					}
					$data['beli_rupiah'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td>';
					$data['beli_rupiah'] .= '<td></td><td></td><td></td><td></td></tr>';
					
					//PEMBELIAN RUPIAH RATA2
					if($total_pembelianrupiah_rt == 0){
						$saldo_awal = 0;
					}else{
						$saldo_awal = $total_pembelianrupiah_rt / $total_pembelian_rt;
					}
					
					if(number_format($saldo_awal,0) == '0'){
						$sa_tulis = '-';
					}else{
						$sa_tulis = number_format($saldo_awal,0);
					}
					$data['beli_rata'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td>';
					$data['beli_rata'] .= '<td></td><td></td><td></td><td></td></tr>';
				}else{
					$acc_number = $d->accountnumber;
					$id_kurs = 1;
					
					if($d->type == 'SDR'){
						$tabel_name = 'gold_mutasi_reparasi';
					}else if($d->type == 'SDG'){
						$tabel_name = 'gold_mutasi_pengadaan';
					}else if($d->type == 'TDG'){
						$tabel_name = 'gold_mutasi_titip_pengadaan';
					}
					
					$saldo_awal_d = $this->mm->get_report_mutasi_repgros_d($tabel_name,$acc_number,$tgl_transaksi);
					$saldo_awal_k = $this->mm->get_report_mutasi_repgros_k($tabel_name,$acc_number,$tgl_transaksi);
					
					foreach($saldo_awal_d as $mtyd){
						if($d->type == 'SDR'){
							$saldo_awal_dr = $saldo_awal_dr + $mtyd->total_mutasi;
						}else if($d->type == 'SDG'){
							$saldo_awal_dg = $saldo_awal_dg + $mtyd->total_mutasi;
						}else if($d->type == 'TDG'){
							$saldo_awal_tg = $saldo_awal_tg + $mtyd->total_mutasi;
						}
					}
					
					foreach($saldo_awal_k as $mtyk){
						if($d->type == 'SDR'){
							$saldo_awal_dr = $saldo_awal_dr - $mtyk->total_mutasi;
						}else if($d->type == 'SDG'){
							$saldo_awal_dg = $saldo_awal_dg - $mtyk->total_mutasi;
						}else if($d->type == 'TDG'){
							$saldo_awal_tg = $saldo_awal_tg - $mtyk->total_mutasi;
						}
					}
					
					if($d->type == 'SDR'){
						$saldo_awal = $saldo_awal_dr;
						$saldo_akhir_dr = $saldo_akhir_dr + $saldo_awal;
					}else if($d->type == 'SDG'){
						$saldo_awal = $saldo_awal_dg;
						$saldo_akhir_dg = $saldo_akhir_dg + $saldo_awal;
					}else if($d->type == 'TDG'){
						$saldo_awal = $saldo_awal_tg;
						$saldo_akhir_tg = $saldo_akhir_tg + $saldo_awal;
					}
					
					$sa_tulis = $this->weight_format($saldo_awal);
					$sa_tulis2 = $this->weight_format($saldo_awal);
					$data['saldo_awal'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td>';
					
					if($d->type == 'SDR'){
						$saldo_awal = $data_in_lain2[0]->total;
						$saldo_awal = (double)$saldo_awal;
						$saldo_akhir_dr = $saldo_akhir_dr + $saldo_awal;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['tarik_lain'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
						$data['total_tarik'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
						
						$saldo_awal = $data_out_lain2[0]->total;
						$saldo_awal = (double)$saldo_awal;
						$saldo_akhir_dr = $saldo_akhir_dr - $saldo_awal;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['kirim_lain'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
						$data['total_kirim'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
						
						$saldo_awal = $saldo_akhir_dr;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['saldo_akhir'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
					}else if($d->type == 'SDG'){
						$saldo_awal = $data_in_lain3[0]->total;
						$saldo_awal = (double)$saldo_awal;
						$saldo_akhir_dg = $saldo_akhir_dg + $saldo_awal;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['tarik_lain'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
						$data['total_tarik'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
						
						$saldo_awal = $data_out_lain3[0]->total;
						$saldo_awal = (double)$saldo_awal;
						$saldo_akhir_dg = $saldo_akhir_dg - $saldo_awal;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['kirim_lain'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
						$data['total_kirim'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
						
						$saldo_awal = $saldo_akhir_dg;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['saldo_akhir'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
					}else if($d->type == 'TDG'){
						$saldo_awal = $data_in_lain4[0]->total;
						$saldo_awal = (double)$saldo_awal;
						$saldo_akhir_tg = $saldo_akhir_tg + $saldo_awal;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['tarik_lain'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
						$data['total_tarik'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
						
						$saldo_awal = $data_out_lain4[0]->total;
						$saldo_awal = (double)$saldo_awal;
						$saldo_akhir_tg = $saldo_akhir_tg - $saldo_awal;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['kirim_lain'] .= '<td class="right-aligned">'.$sa_tulis.'</td>';
						$data['total_kirim'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
						
						$saldo_awal = $saldo_akhir_tg;
						$sa_tulis = $this->weight_format($saldo_awal);
						$data['saldo_akhir'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td>';
					}
				}
			}
		}
		
		$saldo_awal = $data_in_lain5[0]->total;
		$saldo_awal = (double)$saldo_awal;
		$saldo_akhir_tp = $saldo_akhir_tp + $saldo_awal;
		$sa_tulis = $this->weight_format($saldo_awal);
		$data['tarik_lain'] .= '<td class="right-aligned">'.$sa_tulis.'</td></tr>';
		$data['total_tarik'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td></tr>';
		
		$saldo_awal = $data_out_lain5[0]->total;
		$saldo_awal = (double)$saldo_awal;
		$saldo_akhir_tp = $saldo_akhir_tp - $saldo_awal;
		$sa_tulis = $this->weight_format($saldo_awal);
		$data['kirim_lain'] .= '<td class="right-aligned">'.$sa_tulis.'</td></tr>';
		$data['total_kirim'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td></tr>';
		
		$coa_from = 220001;
		$coa_to = 229999;
		$kas_account = $this->mm->get_coa_gr_by_range($coa_from, $coa_to);
		
		if(count($kas_account) > 0){
			foreach($kas_account as $ka){
				$acc_number = $ka->accountnumber;
				$id_kurs = 1;
				
				$sa = $this->mm->get_report_gr_beginning_balance($acc_number);
				
				$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_bykarat($acc_number,$tgl_transaksi,$id_kurs);
				$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_bykarat($acc_number,$tgl_transaksi,$id_kurs);
				
				foreach($saldo_awal_d as $mtyd){
					$sa = $sa - $mtyd->total_mutasi;
				}
				
				foreach($saldo_awal_k as $mtyk){
					$sa = $sa + $mtyk->total_mutasi;
				}	

				$saldo_awal_tp = $saldo_awal_tp + $sa;			
			}
			
			$saldo_akhir_tp = $saldo_akhir_tp + $saldo_awal_tp;
			$sa_tulis = $this->weight_format($saldo_awal_tp);
			$data['saldo_awal'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td></tr>';
		}else{
			$data['saldo_awal'] .= '<td class="right-aligned td-bold">-</td></tr>';
		}
		
		$sa_tulis = $this->weight_format($saldo_akhir_tp);
		$data['saldo_akhir'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td></tr>';
		
		$data['margin'] .= '<td></td><td></td><td></td><td></td><td></td>';
		
		$data['view'] .= '<th class="center aligned" style="width:110px;">Total</th>';
		$data['view'] .= '<th class="center aligned" style="width:110px;">Reparasi</th><th class="center aligned" style="width:110px;">Saldo Pgdn</th><th class="center aligned" style="width:110px;">Titipan di Pgdn</th><th class="center aligned" style="width:110px;">Titipan Plgn</th>';
		$data['view'] .= '</tr></thead><tbody>';
		
		//                       SALDO AWAL                       //
		$data['view'] .= '<tr><td class="td-bold">Saldo Awal</td>';
		$data['view'] .= $data['saldo_awal'];
		
		$data['view'] .= '<tr><td class="td-bold">Penerimaan (+)</td>';
		$data['view'] .= $data['margin'];
		$data['view'] .= '<ul>';
		//                       PEMBELIAN                       //
		$data['view'] .= '<tr><td><li>Pembelian</li></td>';
		$data['view'] .= $data['pembelian'];
		
		//                       STOCK OUT                       //
		$data['view'] .= '<tr><td><li>Stock Out</li></td>';
		$data['view'] .= $data['stock_out'];
		
		//                       TARIK REPARASI                       //
		$data['view'] .= '<tr><td><li>Penarikan Barang Reparasi</li></td>';
		$data['view'] .= $data['tarik_reparasi'];
		
		//                       TARIK PENGADAAN                       //
		$data['view'] .= '<tr><td><li>Penarikan Barang Pengadaan</li></td>';
		$data['view'] .= $data['tarik_pengadaan'];
		
		//                       LAIN LAIN IN                       //
		$data['view'] .= '<tr><td><li>Lain-lain</li></td>';
		$data['view'] .= $data['tarik_lain'];
		
		//                       TOTAL PENERIMAAN                       //
		$data['view'] .= '<tr><td class="td-bold">Total Penerimaan</td>';
		$data['view'] .= $data['total_tarik'];
		
		//                       PENGELUARAN                       //
		$data['view'] .= '<tr><td class="td-bold">Pengeluaran (-)</td>';
		$data['view'] .= $data['margin'];
		
		//                       STOCK IN                       //
		$data['view'] .= '<tr><td><li>Ke Pajangan</li></td>';
		$data['view'] .= $data['stock_in'];
		
		//                       KIRIM REPARASI                       //
		$data['view'] .= '<tr><td><li>Pengiriman ke Dept. Reparasi</li></td>';
		$data['view'] .= $data['kirim_reparasi'];
		
		//                       KIRIM PENGADAAN                       //
		$data['view'] .= '<tr><td><li>Kirim ke Dept. Pengadaan</li></td>';
		$data['view'] .= $data['kirim_pengadaan'];
		
		//                       LAIN LAIN OUT                       //
		$data['view'] .= '<tr><td><li>Lain-lain</li></td>';
		$data['view'] .= $data['kirim_lain'];
		
		//                       TOTAL PENGELUARAN                       //
		$data['view'] .= '<tr><td class="td-bold">Total Pengeluaran</td>';
		$data['view'] .= $data['total_kirim'];
		
		//                       SALDO AKHIR                       //
		$data['view'] .= '<tr><td class="td-bold">Saldo Akhir</td>';
		$data['view'] .= $data['saldo_akhir'];
		
		$data['view'] .= '<tr><td><span style="visibility:hidden">-</span></td>';
		$data['view'] .= $data['margin'];
		
		/*--- PEMBELIAN ---*/
		
		$data['view'] .= '<tr><td class="td-bold">Data Pembelian</td>';
		$data['view'] .= $data['margin'];
		
		$data['view'] .= '<tr><td class="align-middle">Pembelian Dalam Rupiah</td>';
		$data['view'] .= $data['beli_rupiah'];
		
		$data['view'] .= '<tr><td class="align-middle">Rata2 per Gram</td>';
		$data['view'] .= $data['beli_rata'];
		$data['view'] .= '</ul>';
		$data['view'] .= '</tbody></table>';
		
		$data['view'] .= '<div class="ket-ttd2">Diperiksa Oleh : </div><div class="nama-ttd2"><span>Staff Akuntansi</span></div><div class="ket-ttd2">Disetujui Oleh : </div><div class="nama-ttd2"><span>Manager Cabang</span></div><div class="ket-ttd2">Diketahui Oleh : </div><div class="nama-ttd2"><span>Manager Pembina</span></div>';
		
		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		$pdf = new \Mpdf\Mpdf();
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right"><img src="'. base_url().'assets/images/branding/fbn.png" style="width:32px"/></div>');
        $pdf->AddPage('L');
        $pdf->WriteHTML($html);
		
        $pdf->Output("Laporan Saldo Reparasi Harian.pdf", "I");
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

	public function weight_format($number){
		$saldo_awal = (double)$number;
						
		if(number_format($saldo_awal,3) == '0.000'){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($saldo_awal, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		return $sa_tulis;
	}
}