<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_lap_pajangan extends CI_Controller {
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
		$this->load->view('report/V_lap_pajangan');
	}
	
	public function filter_lap(){
		date_default_timezone_set("Asia/Jakarta");
		
		$filter_box_from = 1;
		$filter_box_to = 999;
		$array_box = array();
		
		$tgl_transaksi =  $this->input->post('tgl_transaksi');
		$tanggal_transaksi =  $this->input->post('tgl_transaksi');
		
		$tglTrans = $this->date_to_format($tgl_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		$tgl_sa = date("Y-m-d",$tglTrans).' 23:59:59';
		
		$tglKemarin = date("Y-m-d",$tglTrans);
		$tgl_kemarin = date('Y-m-d',strtotime($tglKemarin. "-1 days"));
		$tgl_kemarin = $tgl_kemarin.' 23:59:59';
		
		$site_name = $this->mm->get_site_name();
		
		$data_karat = $this->mk->get_karat_srt();
		
		$filter_product_pindah_box = '';
				
		$pindah_box = $this->mp->get_posisi_pindah_box($tgl_kemarin,$filter_box_from,$filter_box_to);
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
		
		$data_filter = $this->mp->get_posisi_detail_pajangan($tgl_kemarin,$filter_box_from,$filter_box_to,$filter_product_pindah_box);
		
		foreach($data_filter as $d){
			$array_box[$d->id] = $d->id_box;
		}
		
		foreach($pindah_box as $p){
			$array_box[$p->id_product] = $p->id_box_from;
		}
		
		$count_data_karat = count($data_karat);
		$colspan_head = $count_data_karat * 2;
		$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column" style="text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/C_lap_pajangan/lap_to_pdf/'.$tanggal_transaksi.'" target="_blank"><i class="paperclip icon"></i> Download</a></div></div><table id="report_data_lap " class="ui celled table" cellspacing="0" width="100%"><thead><tr style="text-align:center"><th class="center aligned" rowspan="3">Keterangan</th><th class="center aligned" colspan="'.$colspan_head.'">Kelompok Emas</th><th class="center aligned" rowspan="2" colspan="2">Total</th></tr><tr style="text-align:center">';
		
		foreach($data_karat as $dk){
			$data['view'] .= '<th class="center aligned" colspan="2">'.$dk->karat_name.'</th>';
		}
		
		$data['view'] .= '</tr><tr style="text-align:center">';
		
		foreach($data_karat as $dk){
			$data['view'] .= '<th class="center aligned">Gram</th><th class="center aligned">Potong</th>';
		}
		
		$data['view'] .= '<th class="center aligned">Gram</th><th class="center aligned">Potong</th>';
		$data['view'] .= '</tr>';
		$data['view'] .= '</thead><tbody>';
		
		$saldo_awal_gram = array();
		$saldo_awal_potong = array();
		
		$number = 0;
		$total_weight = 0;
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
			
			$saldo_awal_gram[$dk->id] = $total_weight_karat;
			$saldo_awal_potong[$dk->id] = $count_data_karat;
		}
		
		$total_sa_gram = 0;
		$total_sa_potong = 0;
		
		/*-- SALDO AWAL -- */
		$data['view'] .= '<tr><td class="td-bold">Saldo Awal</td>';
		foreach($data_karat as $dk){
			if($saldo_awal_gram[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($saldo_awal_gram[$dk->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
			}
			
			if($saldo_awal_potong[$dk->id] == 0){
				$sa_pt_tulis = '-';
			}else{
				$sa_pt_tulis = number_format($saldo_awal_potong[$dk->id], 0);
			}
			
			$data['view'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td><td class="right aligned td-bold">'.$sa_pt_tulis.'</td>';
			
			$total_sa_gram = $total_sa_gram + $saldo_awal_gram[$dk->id];
			$total_sa_potong = $total_sa_potong + $saldo_awal_potong[$dk->id];
		}
		
		if($total_sa_gram == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_sa_gram, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		if($total_sa_potong == 0){
			$sa_pt_tulis = '-';
		}else{
			$sa_pt_tulis = number_format($total_sa_potong, 0);
		}
		
		$data['view'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td><td class="right aligned td-bold">'.$sa_pt_tulis.'</td></tr>';
		/*------------------*/
		
		/*--- PENERIMAAN ---*/
		
		$data['view'] .= '<tr><td class="td-bold">Penerimaan</td>';
		foreach($data_karat as $dk){
			$data['view'] .= '<td class="right aligned"></td><td class="right aligned"></td>';
		}
		$data['view'] .= '<td class="right aligned"></td><td class="right aligned"></td></tr>';
		
		$total_si_gram = array();
		$total_si_pt = array();
		
		$total_si_gram_all = 0;
		$total_si_pt_all = 0;
		
		$total_trim_gr = array();
		$total_trim_pt = array();
		
		foreach($data_karat as $dk){
			$total_si_gram[$dk->id] = 0;
			$total_si_pt[$dk->id] = 0;
			
			$total_trim_gr[$dk->id] = 0;
			$total_trim_pt[$dk->id] = 0;
		}
		
		$data_si = $this->mp->get_rekap_stock_in($tgl_transaksi);
		
		$data['view'] .= '<tr><td class="align-middle"><li>Stock In</li></td>';
		foreach($data_si as $ds){
			$total_si_gram[$ds->id_karat] = $ds->berat;
			$total_si_pt[$ds->id_karat] = $ds->pcs;
			
			$total_trim_gr[$ds->id_karat] = $ds->berat;
			$total_trim_pt[$ds->id_karat] = $ds->pcs;
			
			$total_si_gram_all = $total_si_gram_all + $ds->berat;
			$total_si_pt_all = $total_si_pt_all + $ds->pcs;
		}
		
		foreach($data_karat as $dk){
			if($total_si_gram[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_si_gram[$dk->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
			}
			
			if($total_si_pt[$dk->id] == 0){
				$sa_pt_tulis = '-';
			}else{
				$sa_pt_tulis = number_format($total_si_pt[$dk->id], 0);
			}
			
			$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td><td class="right aligned">'.$sa_pt_tulis.'</td>';
		}
		
		if($total_si_gram_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_si_gram_all, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		if($total_si_pt_all == 0){
			$sa_pt_tulis = '-';
		}else{
			$sa_pt_tulis = number_format($total_si_pt_all, 0);
		}
		
		$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td><td class="right aligned">'.$sa_pt_tulis.'</td></tr>';
		
		$total_si_gram_all = 0;
		$total_si_pt_all = 0;
		
		$data['view'] .= '<tr><td class="td-bold">Total Penerimaan</td>';
		
		foreach($data_karat as $dk){
			$total_si_gram_all = $total_si_gram_all + $total_trim_gr[$dk->id];
			$total_si_pt_all = $total_si_pt_all + $total_trim_pt[$dk->id];
			
			if($total_trim_gr[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_trim_gr[$dk->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
			}
			
			if($total_trim_pt[$dk->id] == 0){
				$sa_pt_tulis = '-';
			}else{
				$sa_pt_tulis = number_format($total_trim_pt[$dk->id], 0);
			}
			
			$data['view'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td><td class="right aligned double-top td-bold">'.$sa_pt_tulis.'</td>';
		}
		
		if($total_si_gram_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_si_gram_all, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		if($total_si_pt_all == 0){
			$sa_pt_tulis = '-';
		}else{
			$sa_pt_tulis = number_format($total_si_pt_all, 0);
		}
		
		$data['view'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td><td class="right aligned double-top td-bold">'.$sa_pt_tulis.'</td></tr>';
		
		
		/*------------------*/
		
		$data['view'] .= '<tr><td class="td-bold"><span style="visibility:hidden">-</span></td>';
		foreach($data_karat as $dk){
			$data['view'] .= '<td class="right aligned"></td><td class="right aligned"></td>';
		}
		$data['view'] .= '<td class="right aligned"></td><td class="right aligned"></td></tr>';
		
		/*--- PENGELUARAN ---*/
		
		$data['view'] .= '<tr><td class="td-bold">Pengeluaran</td>';
		foreach($data_karat as $dk){
			$data['view'] .= '<td class="right aligned"></td><td class="right aligned"></td>';
		}
		$data['view'] .= '<td class="right aligned"></td><td class="right aligned"></td></tr>';
		
		$total_si_gram = array();
		$total_si_pt = array();
		
		$total_si_gram_all = 0;
		$total_si_pt_all = 0;
		
		$total_pengeluaran_gr = array();
		$total_pengeluaran_pt = array();
		
		foreach($data_karat as $dk){
			$total_si_gram[$dk->id] = 0;
			$total_si_pt[$dk->id] = 0;
			
			$total_pengeluaran_gr[$dk->id] = 0;
			$total_pengeluaran_pt[$dk->id] = 0;
		}
		
		$data_si = $this->mt->get_rekap_penjualan($tgl_transaksi);
		
		$data['view'] .= '<tr><td class="align-middle"><li>Penjualan</li></td>';
		foreach($data_si as $ds){
			$total_si_gram[$ds->id_karat] = $ds->berat;
			$total_si_pt[$ds->id_karat] = $ds->pcs;
			
			$total_si_gram_all = $total_si_gram_all + $ds->berat;
			$total_si_pt_all = $total_si_pt_all + $ds->pcs;
			
			$total_pengeluaran_gr[$ds->id_karat] = $total_pengeluaran_gr[$ds->id_karat] + $ds->berat;
			$total_pengeluaran_pt[$ds->id_karat] = $total_pengeluaran_pt[$ds->id_karat] + $ds->pcs;
		}
		
		foreach($data_karat as $dk){
			if($total_si_gram[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_si_gram[$dk->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
			}
			
			if($total_si_pt[$dk->id] == 0){
				$sa_pt_tulis = '-';
			}else{
				$sa_pt_tulis = number_format($total_si_pt[$dk->id], 0);
			}
			
			$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td><td class="right aligned">'.$sa_pt_tulis.'</td>';
		}
		
		if($total_si_gram_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_si_gram_all, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		if($total_si_pt_all == 0){
			$sa_pt_tulis = '-';
		}else{
			$sa_pt_tulis = number_format($total_si_pt_all, 0);
		}
		
		$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td><td class="right aligned">'.$sa_pt_tulis.'</td></tr>';
		
		$total_si_gram = array();
		$total_si_pt = array();
		
		$total_si_gram_all = 0;
		$total_si_pt_all = 0;
		
		foreach($data_karat as $dk){
			$total_si_gram[$dk->id] = 0;
			$total_si_pt[$dk->id] = 0;
		}
		
		$data_si = $this->mp->get_rekap_stock_out($tgl_transaksi);
		
		$data['view'] .= '<tr><td class="align-middle"><li>Stock Out</li></td>';
		foreach($data_si as $ds){
			$total_si_gram[$ds->id_karat] = $ds->berat;
			$total_si_pt[$ds->id_karat] = $ds->pcs;
			
			$total_si_gram_all = $total_si_gram_all + $ds->berat;
			$total_si_pt_all = $total_si_pt_all + $ds->pcs;
			
			$total_pengeluaran_gr[$ds->id_karat] = $total_pengeluaran_gr[$ds->id_karat] + $ds->berat;
			$total_pengeluaran_pt[$ds->id_karat] = $total_pengeluaran_pt[$ds->id_karat] + $ds->pcs;
		}
		
		foreach($data_karat as $dk){
			if($total_si_gram[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_si_gram[$dk->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
			}
			
			if($total_si_pt[$dk->id] == 0){
				$sa_pt_tulis = '-';
			}else{
				$sa_pt_tulis = number_format($total_si_pt[$dk->id], 0);
			}
			
			$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td><td class="right aligned">'.$sa_pt_tulis.'</td>';
		}
		
		if($total_si_gram_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_si_gram_all, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		if($total_si_pt_all == 0){
			$sa_pt_tulis = '-';
		}else{
			$sa_pt_tulis = number_format($total_si_pt_all, 0);
		}
		
		$data['view'] .= '<td class="right aligned">'.$sa_tulis.'</td><td class="right aligned">'.$sa_pt_tulis.'</td></tr>';
		
		$total_si_gram_all = 0;
		$total_si_pt_all = 0;
		
		$data['view'] .= '<tr><td class="td-bold">Total Pengeluaran</td>';
		
		foreach($data_karat as $dk){
			$total_si_gram_all = $total_si_gram_all + $total_pengeluaran_gr[$dk->id];
			$total_si_pt_all = $total_si_pt_all + $total_pengeluaran_pt[$dk->id];
			
			if($total_pengeluaran_gr[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_pengeluaran_gr[$dk->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
			}
			
			if($total_pengeluaran_pt[$dk->id] == 0){
				$sa_pt_tulis = '-';
			}else{
				$sa_pt_tulis = number_format($total_pengeluaran_pt[$dk->id], 0);
			}
			
			$data['view'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td><td class="right aligned double-top td-bold">'.$sa_pt_tulis.'</td>';
		}
		
		if($total_si_gram_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_si_gram_all, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		if($total_si_pt_all == 0){
			$sa_pt_tulis = '-';
		}else{
			$sa_pt_tulis = number_format($total_si_pt_all, 0);
		}
		
		$data['view'] .= '<td class="right aligned double-top td-bold">'.$sa_tulis.'</td><td class="right aligned double-top td-bold">'.$sa_pt_tulis.'</td></tr>';
		
		$data['view'] .= '<tr><td class="td-bold"><span style="visibility:hidden">-</span></td>';
		foreach($data_karat as $dk){
			$data['view'] .= '<td class="right aligned"></td><td class="right aligned"></td>';
		}
		$data['view'] .= '<td class="right aligned"></td><td class="right aligned"></td></tr>';
		
		/*-- SALDO AKHIR --*/
		$filter_product_pindah_box = '';
				
		$pindah_box = $this->mp->get_posisi_pindah_box($tgl_sa,$filter_box_from,$filter_box_to);
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
		
		$data_filter = $this->mp->get_posisi_detail_pajangan($tgl_sa,$filter_box_from,$filter_box_to,$filter_product_pindah_box);
		
		foreach($data_filter as $d){
			$array_box[$d->id] = $d->id_box;
		}
		
		foreach($pindah_box as $p){
			$array_box[$p->id_product] = $p->id_box_from;
		}
		
		$saldo_awal_gram = array();
		$saldo_awal_potong = array();
		
		$number = 0;
		$total_weight = 0;
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
			
			$saldo_awal_gram[$dk->id] = $total_weight_karat;
			$saldo_awal_potong[$dk->id] = $count_data_karat;
		}
		
		$total_sa_gram = 0;
		$total_sa_potong = 0;
		
		/*-- SALDO AKHIR -- */
		$data['view'] .= '<tr><td class="td-bold">Saldo Akhir</td>';
		foreach($data_karat as $dk){
			if($saldo_awal_gram[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($saldo_awal_gram[$dk->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
			}
			
			if($saldo_awal_potong[$dk->id] == 0){
				$sa_pt_tulis = '-';
			}else{
				$sa_pt_tulis = number_format($saldo_awal_potong[$dk->id], 0);
			}
			
			$data['view'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td><td class="right aligned td-bold">'.$sa_pt_tulis.'</td>';
			
			$total_sa_gram = $total_sa_gram + $saldo_awal_gram[$dk->id];
			$total_sa_potong = $total_sa_potong + $saldo_awal_potong[$dk->id];
		}
		
		if($total_sa_gram == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_sa_gram, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup> '.$sa_gram_koma.'</sup>';
		}
		
		if($total_sa_potong == 0){
			$sa_pt_tulis = '-';
		}else{
			$sa_pt_tulis = number_format($total_sa_potong, 0);
		}
		
		$data['view'] .= '<td class="right aligned td-bold">'.$sa_tulis.'</td><td class="right aligned td-bold">'.$sa_pt_tulis.'</td></tr>';
		/*------------------*/
		
		$data['view'] .= '<tr><td class="double-top td-bold"><span style="visibility:hidden">-</span></td>';
		foreach($data_karat as $dk){
			$data['view'] .= '<td class="right aligned double-top td-bold"></td><td class="right aligned double-top td-bold"></td>';
		}
		$data['view'] .= '<td class="right aligned double-top td-bold"></td><td class="right aligned double-top td-bold"></td></tr>';
		
		/*--- PENJUALAN ---*/
		
		$data['view'] .= '<tr><td class="td-bold">Data Penjualan</td>';
		foreach($data_karat as $dk){
			$data['view'] .= '<td class="right aligned"></td><td class="right aligned"></td>';
		}
		$data['view'] .= '<td class="right aligned"></td><td class="right aligned"></td></tr>';
		
		$total_si_rupiah = array();
		$total_si_gram = array();
		$total_si_rupiah_all = 0;
		$total_si_gram_all = 0;
		
		foreach($data_karat as $dk){
			$total_si_rupiah[$dk->id] = 0;
			$total_si_gram[$dk->id] = 0;
		}
		
		$data_si = $this->mt->get_rekap_penjualan($tgl_transaksi);
		
		$data['view'] .= '<tr><td class="align-middle"><li>Penjualan Dalam Rupiah</li></td>';
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
			$data['view'] .= '<td class="right aligned td-bold" colspan="2">'.$sa_tulis.'</td>';
		}
		
		if($total_si_rupiah_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_tulis = number_format($total_si_rupiah_all, 0);
		}
		
		$data['view'] .= '<td class="right aligned td-bold" colspan="2">'.$sa_tulis.'</td></tr>';
		
		$data['view'] .= '<tr><td class="align-middle"><li>Rata2 per Gram</li></td>';
		
		foreach($data_karat as $dk){
			if($total_si_gram[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_tulis = number_format($total_si_rupiah[$dk->id] / $total_si_gram[$dk->id], 0);
			}
			
			$data['view'] .= '<td class="right aligned td-bold" colspan="2">'.$sa_tulis.'</td>';
		}
		
		if($total_si_gram_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_tulis = number_format($total_si_rupiah_all/$total_si_gram_all, 0);
		}
		
		$data['view'] .= '<td class="right aligned td-bold" colspan="2">'.$sa_tulis.'</td></tr>';
			
		$data['view'] .= '</tbody></table>';
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function lap_to_pdf($tgl_transaksi){
		date_default_timezone_set("Asia/Jakarta");
		
		$filter_box_from = 1;
		$filter_box_to = 999;
		$array_box = array();
		
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
		
		$tglKemarin = date("Y-m-d",$tglTrans);
		$tgl_kemarin = date('Y-m-d',strtotime($tglKemarin. "-1 days"));
		$tgl_kemarin = $tgl_kemarin.' 23:59:59';
		
		$site_name = $this->mm->get_site_name();
		
		$data_karat = $this->mk->get_karat_srt();
		
		$filter_product_pindah_box = '';
				
		$pindah_box = $this->mp->get_posisi_pindah_box($tgl_kemarin,$filter_box_from,$filter_box_to);
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
		
		$data_filter = $this->mp->get_posisi_detail_pajangan($tgl_kemarin,$filter_box_from,$filter_box_to,$filter_product_pindah_box);
		
		foreach($data_filter as $d){
			$array_box[$d->id] = $d->id_box;
		}
		
		foreach($pindah_box as $p){
			$array_box[$p->id_product] = $p->id_box_from;
		}
		
		$count_data_karat = count($data_karat);
		$colspan_head = $count_data_karat * 2;
		$colspan = 3 + (count($data_karat) * 2);
		
		$sitename = $this->mm->get_site_name();
		
		$data['view'] = '<table class="lap_pdf_3" cellspacing="0" width="100%"><thead><tr><td colspan="3" style="text-align:left; border:none"><img src="'.base_url().'assets/images/branding/brand.png" style="width:280px"></td><td colspan="6" style="text-align:center;font-size:16px;border:none;font-weight:bold">Laporan Emas Pajangan Harian <br>Cabang '.$sitename.'</td><td colspan="4" style="text-align:right;font-size:16px;border:none;font-weight:bold"><br>'.$hari_tulis.', '.$tanggal_aktif.'</td></tr><tr style="text-align:center"><th rowspan="3" style="width:190px">Keterangan</th><th colspan="'.$colspan_head.'">Kelompok Emas</th><th colspan="2" rowspan="2">Total</th></tr><tr style="text-align:center">';
		
		foreach($data_karat as $dk){
			$data['view'] .= '<th class="center aligned" colspan="2">'.$dk->karat_name.'</th>';
		}
		
		$data['view'] .= '</tr><tr style="text-align:center">';
		
		foreach($data_karat as $dk){
			$data['view'] .= '<th style="width:90px;">Gram</th><th style="width:60px;">Potong</th>';
		}
		
		$data['view'] .= '<th style="width:90px;">Gram</th><th style="width:60px;">Potong</th>';
		$data['view'] .= '</tr>';
		$data['view'] .= '</thead><tbody>';
		
		$saldo_awal_gram = array();
		$saldo_awal_potong = array();
		
		$number = 0;
		$total_weight = 0;
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
			
			$saldo_awal_gram[$dk->id] = $total_weight_karat;
			$saldo_awal_potong[$dk->id] = $count_data_karat;
		}
		
		$total_sa_gram = 0;
		$total_sa_potong = 0;
		
		/*-- SALDO AWAL -- */
		$data['view'] .= '<tr><td class="td-bold">Saldo Awal</td>';
		foreach($data_karat as $dk){
			if($saldo_awal_gram[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($saldo_awal_gram[$dk->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
			}
			
			if($saldo_awal_potong[$dk->id] == 0){
				$sa_pt_tulis = '-';
			}else{
				$sa_pt_tulis = number_format($saldo_awal_potong[$dk->id], 0);
			}
			
			$data['view'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td><td class="right-aligned td-bold">'.$sa_pt_tulis.'</td>';
			
			$total_sa_gram = $total_sa_gram + $saldo_awal_gram[$dk->id];
			$total_sa_potong = $total_sa_potong + $saldo_awal_potong[$dk->id];
		}
		
		if($total_sa_gram == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_sa_gram, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		if($total_sa_potong == 0){
			$sa_pt_tulis = '-';
		}else{
			$sa_pt_tulis = number_format($total_sa_potong, 0);
		}
		
		$data['view'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td><td class="right-aligned td-bold">'.$sa_pt_tulis.'</td></tr>';
		/*------------------*/
		
		/*--- PENERIMAAN ---*/
		
		$data['view'] .= '<tr><td colspan="'.$colspan.'" class="td-bold">Penerimaan</td></tr>';
		
		$total_si_gram = array();
		$total_si_pt = array();
		
		$total_si_gram_all = 0;
		$total_si_pt_all = 0;
		
		$total_trim_gr = array();
		$total_trim_pt = array();
		
		foreach($data_karat as $dk){
			$total_si_gram[$dk->id] = 0;
			$total_si_pt[$dk->id] = 0;
			
			$total_trim_gr[$dk->id] = 0;
			$total_trim_pt[$dk->id] = 0;
		}
		
		$data_si = $this->mp->get_rekap_stock_in($tgl_transaksi);
		
		$data['view'] .= '<tr><td class="align-middle"><li>Stock In</li></td>';
		foreach($data_si as $ds){
			$total_si_gram[$ds->id_karat] = $ds->berat;
			$total_si_pt[$ds->id_karat] = $ds->pcs;
			
			$total_trim_gr[$ds->id_karat] = $ds->berat;
			$total_trim_pt[$ds->id_karat] = $ds->pcs;
			
			$total_si_gram_all = $total_si_gram_all + $ds->berat;
			$total_si_pt_all = $total_si_pt_all + $ds->pcs;
		}
		
		foreach($data_karat as $dk){
			if($total_si_gram[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_si_gram[$dk->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
			}
			
			if($total_si_pt[$dk->id] == 0){
				$sa_pt_tulis = '-';
			}else{
				$sa_pt_tulis = number_format($total_si_pt[$dk->id], 0);
			}
			
			$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td><td class="right-aligned">'.$sa_pt_tulis.'</td>';
		}
		
		if($total_si_gram_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_si_gram_all, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		if($total_si_pt_all == 0){
			$sa_pt_tulis = '-';
		}else{
			$sa_pt_tulis = number_format($total_si_pt_all, 0);
		}
		
		$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td><td class="right-aligned">'.$sa_pt_tulis.'</td></tr>';
		
		$total_si_gram_all = 0;
		$total_si_pt_all = 0;
		
		$data['view'] .= '<tr><td class="td-bold">Total Penerimaan</td>';
		
		foreach($data_karat as $dk){
			$total_si_gram_all = $total_si_gram_all + $total_trim_gr[$dk->id];
			$total_si_pt_all = $total_si_pt_all + $total_trim_pt[$dk->id];
			
			if($total_trim_gr[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_trim_gr[$dk->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
			}
			
			if($total_trim_pt[$dk->id] == 0){
				$sa_pt_tulis = '-';
			}else{
				$sa_pt_tulis = number_format($total_trim_pt[$dk->id], 0);
			}
			
			$data['view'] .= '<td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_tulis.'</td><td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_pt_tulis.'</td>';
		}
		
		if($total_si_gram_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_si_gram_all, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		if($total_si_pt_all == 0){
			$sa_pt_tulis = '-';
		}else{
			$sa_pt_tulis = number_format($total_si_pt_all, 0);
		}
		
		$data['view'] .= '<td class="right-aligned td-bold" style="border-top:double #000">'.$sa_tulis.'</td><td class="right-aligned double-top td-bold" style="border-top:double #000">'.$sa_pt_tulis.'</td></tr>';
		
		
		/*------------------*/
		
		$data['view'] .= '<tr><td colspan="'.$colspan.'" class="td-bold"><span style="visibility:hidden">-</span></td>';
		
		/*--- PENGELUARAN ---*/
		
		$data['view'] .= '<tr><td colspan="'.$colspan.'" class="td-bold">Pengeluaran</td>';
		
		$total_si_gram = array();
		$total_si_pt = array();
		
		$total_si_gram_all = 0;
		$total_si_pt_all = 0;
		
		$total_pengeluaran_gr = array();
		$total_pengeluaran_pt = array();
		
		foreach($data_karat as $dk){
			$total_si_gram[$dk->id] = 0;
			$total_si_pt[$dk->id] = 0;
			
			$total_pengeluaran_gr[$dk->id] = 0;
			$total_pengeluaran_pt[$dk->id] = 0;
		}
		
		$data_si = $this->mt->get_rekap_penjualan($tgl_transaksi);
		
		$data['view'] .= '<tr><td class="align-middle"><li>Penjualan</li></td>';
		foreach($data_si as $ds){
			$total_si_gram[$ds->id_karat] = $ds->berat;
			$total_si_pt[$ds->id_karat] = $ds->pcs;
			
			$total_si_gram_all = $total_si_gram_all + $ds->berat;
			$total_si_pt_all = $total_si_pt_all + $ds->pcs;
			
			$total_pengeluaran_gr[$ds->id_karat] = $total_pengeluaran_gr[$ds->id_karat] + $ds->berat;
			$total_pengeluaran_pt[$ds->id_karat] = $total_pengeluaran_pt[$ds->id_karat] + $ds->pcs;
		}
		
		foreach($data_karat as $dk){
			if($total_si_gram[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_si_gram[$dk->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
			}
			
			if($total_si_pt[$dk->id] == 0){
				$sa_pt_tulis = '-';
			}else{
				$sa_pt_tulis = number_format($total_si_pt[$dk->id], 0);
			}
			
			$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td><td class="right-aligned">'.$sa_pt_tulis.'</td>';
		}
		
		if($total_si_gram_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_si_gram_all, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		if($total_si_pt_all == 0){
			$sa_pt_tulis = '-';
		}else{
			$sa_pt_tulis = number_format($total_si_pt_all, 0);
		}
		
		$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td><td class="right-aligned">'.$sa_pt_tulis.'</td></tr>';
		
		$total_si_gram = array();
		$total_si_pt = array();
		
		$total_si_gram_all = 0;
		$total_si_pt_all = 0;
		
		foreach($data_karat as $dk){
			$total_si_gram[$dk->id] = 0;
			$total_si_pt[$dk->id] = 0;
		}
		
		$data_si = $this->mp->get_rekap_stock_out($tgl_transaksi);
		
		$data['view'] .= '<tr><td class="align-middle"><li>Stock Out</li></td>';
		foreach($data_si as $ds){
			$total_si_gram[$ds->id_karat] = $ds->berat;
			$total_si_pt[$ds->id_karat] = $ds->pcs;
			
			$total_si_gram_all = $total_si_gram_all + $ds->berat;
			$total_si_pt_all = $total_si_pt_all + $ds->pcs;
			
			$total_pengeluaran_gr[$ds->id_karat] = $total_pengeluaran_gr[$ds->id_karat] + $ds->berat;
			$total_pengeluaran_pt[$ds->id_karat] = $total_pengeluaran_pt[$ds->id_karat] + $ds->pcs;
		}
		
		foreach($data_karat as $dk){
			if($total_si_gram[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_si_gram[$dk->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
			}
			
			if($total_si_pt[$dk->id] == 0){
				$sa_pt_tulis = '-';
			}else{
				$sa_pt_tulis = number_format($total_si_pt[$dk->id], 0);
			}
			
			$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td><td class="right-aligned">'.$sa_pt_tulis.'</td>';
		}
		
		if($total_si_gram_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_si_gram_all, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		if($total_si_pt_all == 0){
			$sa_pt_tulis = '-';
		}else{
			$sa_pt_tulis = number_format($total_si_pt_all, 0);
		}
		
		$data['view'] .= '<td class="right-aligned">'.$sa_tulis.'</td><td class="right-aligned">'.$sa_pt_tulis.'</td></tr>';
		
		$total_si_gram_all = 0;
		$total_si_pt_all = 0;
		
		$data['view'] .= '<tr><td class="td-bold">Total Pengeluaran</td>';
		
		foreach($data_karat as $dk){
			$total_si_gram_all = $total_si_gram_all + $total_pengeluaran_gr[$dk->id];
			$total_si_pt_all = $total_si_pt_all + $total_pengeluaran_pt[$dk->id];
			
			if($total_pengeluaran_gr[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($total_pengeluaran_gr[$dk->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
			}
			
			if($total_pengeluaran_pt[$dk->id] == 0){
				$sa_pt_tulis = '-';
			}else{
				$sa_pt_tulis = number_format($total_pengeluaran_pt[$dk->id], 0);
			}
			
			$data['view'] .= '<td class="right-aligned td-bold" style="border-top:double #000">'.$sa_tulis.'</td><td class="right-aligned td-bold" style="border-top:double #000">'.$sa_pt_tulis.'</td>';
		}
		
		if($total_si_gram_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_si_gram_all, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		if($total_si_pt_all == 0){
			$sa_pt_tulis = '-';
		}else{
			$sa_pt_tulis = number_format($total_si_pt_all, 0);
		}
		
		$data['view'] .= '<td class="right-aligned td-bold" style="border-top:double #000">'.$sa_tulis.'</td><td class="right-aligned td-bold" style="border-top:double #000">'.$sa_pt_tulis.'</td></tr>';
		
		$data['view'] .= '<tr><td colspan="'.$colspan.'" class="td-bold"><span style="visibility:hidden">-</span></td>';
		
		/*-- SALDO AKHIR --*/
		$filter_product_pindah_box = '';
				
		$pindah_box = $this->mp->get_posisi_pindah_box($tgl_sa,$filter_box_from,$filter_box_to);
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
		
		$data_filter = $this->mp->get_posisi_detail_pajangan($tgl_sa,$filter_box_from,$filter_box_to,$filter_product_pindah_box);
		
		foreach($data_filter as $d){
			$array_box[$d->id] = $d->id_box;
		}
		
		foreach($pindah_box as $p){
			$array_box[$p->id_product] = $p->id_box_from;
		}
		
		$saldo_awal_gram = array();
		$saldo_awal_potong = array();
		
		$number = 0;
		$total_weight = 0;
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
			
			$saldo_awal_gram[$dk->id] = $total_weight_karat;
			$saldo_awal_potong[$dk->id] = $count_data_karat;
		}
		
		$total_sa_gram = 0;
		$total_sa_potong = 0;
		
		/*-- SALDO AKHIR -- */
		$data['view'] .= '<tr><td class="td-bold">Saldo Akhir</td>';
		foreach($data_karat as $dk){
			if($saldo_awal_gram[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_gram = number_format($saldo_awal_gram[$dk->id], 3);
				$sa_gram_bulat = substr($sa_gram, 0, -4);
				$sa_gram_koma = substr($sa_gram, -3);
				$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
			}
			
			if($saldo_awal_potong[$dk->id] == 0){
				$sa_pt_tulis = '-';
			}else{
				$sa_pt_tulis = number_format($saldo_awal_potong[$dk->id], 0);
			}
			
			$data['view'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td><td class="right-aligned td-bold">'.$sa_pt_tulis.'</td>';
			
			$total_sa_gram = $total_sa_gram + $saldo_awal_gram[$dk->id];
			$total_sa_potong = $total_sa_potong + $saldo_awal_potong[$dk->id];
		}
		
		if($total_sa_gram == 0){
			$sa_tulis = '-';
		}else{
			$sa_gram = number_format($total_sa_gram, 3);
			$sa_gram_bulat = substr($sa_gram, 0, -4);
			$sa_gram_koma = substr($sa_gram, -3);
			$sa_tulis = $sa_gram_bulat.'<sup class="sup_data"> '.$sa_gram_koma.'</sup>';
		}
		
		if($total_sa_potong == 0){
			$sa_pt_tulis = '-';
		}else{
			$sa_pt_tulis = number_format($total_sa_potong, 0);
		}
		
		$data['view'] .= '<td class="right-aligned td-bold">'.$sa_tulis.'</td><td class="right-aligned td-bold">'.$sa_pt_tulis.'</td></tr>';
		/*------------------*/
		
		$data['view'] .= '<tr><td colspan="'.$colspan.'" class="td-bold" style="border-top:double #000"><span style="visibility:hidden">-</span></td>';
		
		/*--- PENJUALAN ---*/
		
		$data['view'] .= '<tr><td colspan="'.$colspan.'" class="td-bold">Data Penjualan</td>';
		
		$total_si_rupiah = array();
		$total_si_gram = array();
		$total_si_rupiah_all = 0;
		$total_si_gram_all = 0;
		
		foreach($data_karat as $dk){
			$total_si_rupiah[$dk->id] = 0;
			$total_si_gram[$dk->id] = 0;
		}
		
		$data_si = $this->mt->get_rekap_penjualan($tgl_transaksi);
		
		$data['view'] .= '<tr><td class="align-middle"><li>Penjualan Dalam Rupiah</li></td>';
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
			$data['view'] .= '<td class="right-aligned td-bold" colspan="2">'.$sa_tulis.'</td>';
		}
		
		if($total_si_rupiah_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_tulis = number_format($total_si_rupiah_all, 0);
		}
		
		$data['view'] .= '<td class="right-aligned td-bold" colspan="2">'.$sa_tulis.'</td></tr>';
		
		$data['view'] .= '<tr><td class="align-middle"><li>Rata2 per Gram</li></td>';
		
		foreach($data_karat as $dk){
			if($total_si_gram[$dk->id] == 0){
				$sa_tulis = '-';
			}else{
				$sa_tulis = number_format($total_si_rupiah[$dk->id] / $total_si_gram[$dk->id], 2);
			}
			
			$data['view'] .= '<td class="right-aligned td-bold" colspan="2">'.$sa_tulis.'</td>';
		}
		
		if($total_si_gram_all == 0){
			$sa_tulis = '-';
		}else{
			$sa_tulis = number_format($total_si_rupiah_all/$total_si_gram_all, 2);
		}
		
		$data['view'] .= '<td class="right-aligned td-bold" colspan="2">'.$sa_tulis.'</td></tr>';
		
		$data['view'] .= '</tbody></table><br>';
		
		$data['view'] .= '<div class="ket-ttd">Dibuat Oleh</div><div class="ket-ttd">Diperiksa Oleh</div><div class="ket-ttd">Disetujui Oleh</div><br><br><br><div class="nama-ttd"><span>Pembukuan Cabang</span></div><div class="nama-ttd"><span>Staff Accounting</span></div><div class="nama-ttd"><span>Manager Cabang</span></div>';

		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		$pdf = $this->m_pdf->load();
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right"><img src="'. base_url().'assets/images/branding/fbn.png" style="width:32px"/></div>');
        $pdf->AddPage('L');
        $pdf->WriteHTML($html);
		
        $pdf->Output("Laporan Pajangan Harian.pdf", "I");
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
