<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_kirim_beli extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->model('M_karat','mk');
		$this->load->model('M_product','mp');
		$this->load->model('M_pos','mt');
		$this->load->model('M_kelompok_barang','mc');
		$this->load->model('M_mutasi','mm');
	}
	
	public function index(){
		$data['karat'] = $this->mk->get_karat_sdr();
		$data['category'] = $this->mc->get_product_category();
		$this->load->view('report/V_kirim_beli',$data);
	}
	
	public function form_kirim_new($tgl_transaksi){
		date_default_timezone_set("Asia/Jakarta");
		
		$tanggal_transaksi = str_replace('%20',' ',$tgl_transaksi);
		
		$tglTrans = $this->date_to_format_2($tgl_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		$tgl_sa = date("Y-m-d",$tglTrans).' 23:59:59';
				
		$array_jual = array();
		$dj = $this->mt->get_beli_kirim($tgl_transaksi);
		
		$length = count($dj);
		$trans_temp = '';
		$total_pcs_temp = 0;
		$total_gram_temp = 0;
		$total_temp = 0;
		$number = 1;
		$total_pcs_all = 0;
		$total_gram_all = 0;
		$total_jual_all = 0;
		
		$data['view'] = '<input type="hidden" id="data_length" name="data_length" value="'.$length.'">';
		
		for($i = 0;$i < $length; $i++){
			$id_trans = $dj[$i]->transaction_code;
			$tujuan = $dj[$i]->tujuan;
			
			$reparasi = '';
			$grosir = '';
			$sendiri = '';
			
			if($tujuan == 'R'){
				$reparasi = 'checked';
			}else if($tujuan == 'G'){
				$grosir = 'checked';
			}else if($tujuan == 'S'){
				$sendiri = 'checked';
			}
			
			if($i == 0){
				$trans_date = strtotime($dj[$i]->trans_date);
				$trans_date = date('d-M-Y',$trans_date);
				$trans_temp = $id_trans;
				$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$trans_date.'</td><td colspan="9">'.$id_trans.'</td></tr>';
				
				$number = $number + 1;
				
				$data['view'] .= '<tr><td></td><td>'.$dj[$i]->category_name.'</td><td>'.$dj[$i]->nama_product.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right aligned">'.$dj[$i]->product_pcs.'</td><td class="right aligned">'.number_format($dj[$i]->product_weight, 3).'<input type="hidden" value="'.$dj[$i]->product_weight.'" id="product_weight_'.$i.'"><input type="hidden" value="'.$dj[$i]->id_karat.'" id="product_karat_'.$i.'"></td><td class="right aligned">'.number_format($dj[$i]->product_price, 2).'</td><td class="center aligned"><div class="ui toggle checkbox"><input type="radio" name="to_kirim_'.$i.'" id="to_kirim_r_'.$i.'" value="R" '.$reparasi.' onclick="if(this.checked){countTotal()}"><label>Reparasi</label></div></td><td class="center aligned"><div class="ui toggle checkbox"><input type="radio" name="to_kirim_'.$i.'" id="to_kirim_g_'.$i.'" value="G" '.$grosir.' onclick="if(this.checked){countTotal()}"><label>Pengadaan</label></div></td><td class="center aligned"><div class="ui toggle checkbox"><input type="radio" name="to_kirim_'.$i.'" id="to_kirim_s_'.$i.'" value="S" '.$sendiri.' onclick="if(this.checked){countTotal()}"><label>Sendiri</label></div><input type="hidden" name="id_beli_'.$i.'" id="id_beli_'.$i.'" value="'.$dj[$i]->id.'"></td><td class="center aligned"><div class="ui circular tiny icon twitter button" onclick=resetVal("'.$i.'") title="Reset"><i class="sync alternate icon"></i></div></td></tr>';
				
				$total_pcs_all = $total_pcs_all + $dj[$i]->product_pcs;
				$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
				$total_jual_all = $total_jual_all + $dj[$i]->product_price;
				
				$total_pcs_temp = $total_pcs_temp + $dj[$i]->product_pcs;
				$total_gram_temp = $total_gram_temp + $dj[$i]->product_weight;
				$total_temp = $total_temp + $dj[$i]->product_price;
			}else{
				if($dj[$i]->transaction_code == $trans_temp){
					
					$data['view'] .= '<tr><td></td><td>'.$dj[$i]->category_name.'</td><td>'.$dj[$i]->nama_product.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right aligned">'.$dj[$i]->product_pcs.'</td><td class="right aligned">'.number_format($dj[$i]->product_weight, 3).'<input type="hidden" value="'.$dj[$i]->product_weight.'" id="product_weight_'.$i.'"><input type="hidden" value="'.$dj[$i]->id_karat.'" id="product_karat_'.$i.'"></td><td class="right aligned">'.number_format($dj[$i]->product_price, 2).'</td><td class="center aligned"><div class="ui toggle checkbox"><input type="radio" name="to_kirim_'.$i.'" id="to_kirim_r_'.$i.'" value="R" '.$reparasi.' onclick="if(this.checked){countTotal()}"><label>Reparasi</label></div></td><td class="center aligned"><div class="ui toggle checkbox"><input type="radio" name="to_kirim_'.$i.'" id="to_kirim_g_'.$i.'" value="G" '.$grosir.' onclick="if(this.checked){countTotal()}"><label>Pengadaan</label></div></td><td class="center aligned"><div class="ui toggle checkbox"><input type="radio" name="to_kirim_'.$i.'" id="to_kirim_s_'.$i.'" value="S" '.$sendiri.' onclick="if(this.checked){countTotal()}"><label>Sendiri</label></div><input type="hidden" name="id_beli_'.$i.'" id="id_beli_'.$i.'" value="'.$dj[$i]->id.'"></td><td class="center aligned"><div class="ui circular tiny icon twitter button" onclick=resetVal("'.$i.'") title="Reset"><i class="sync alternate icon"></i></div></td></tr>';
					
					$total_pcs_all = $total_pcs_all + $dj[$i]->product_pcs;
					$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
					$total_jual_all = $total_jual_all + $dj[$i]->product_price;
					
					$total_pcs_temp = $total_pcs_temp + $dj[$i]->product_pcs;
					$total_gram_temp = $total_gram_temp + $dj[$i]->product_weight;
					$total_temp = $total_temp + $dj[$i]->product_price;
				}else{
					$data['view'] .= '<tr><td colspan="11"><span style="visibility:hidden">-</span></td></tr>';
					
					$total_temp = 0;
					$total_pcs_temp = 0;
					$total_gram_temp = 0;
					
					$trans_date = strtotime($dj[$i]->trans_date);
					$trans_date = date('d-M-Y',$trans_date);
					$trans_temp = $id_trans;
					$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$trans_date.'</td><td colspan="9">'.$id_trans.'</td></tr>';
					
					$number = $number + 1;
					
					$data['view'] .= '<tr><td></td><td>'.$dj[$i]->category_name.'</td><td>'.$dj[$i]->nama_product.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right aligned">'.$dj[$i]->product_pcs.'</td><td class="right aligned">'.number_format($dj[$i]->product_weight, 3).'<input type="hidden" value="'.$dj[$i]->product_weight.'" id="product_weight_'.$i.'"><input type="hidden" value="'.$dj[$i]->id_karat.'" id="product_karat_'.$i.'"></td><td class="right aligned">'.number_format($dj[$i]->product_price, 2).'</td><td class="center aligned"><div class="ui toggle checkbox"><input type="radio" name="to_kirim_'.$i.'" id="to_kirim_r_'.$i.'" value="R" '.$reparasi.' onclick="if(this.checked){countTotal()}"><label>Reparasi</label></div></td><td class="center aligned"><div class="ui toggle checkbox"><input type="radio" name="to_kirim_'.$i.'" id="to_kirim_g_'.$i.'" value="G" '.$grosir.' onclick="if(this.checked){countTotal()}"><label>Pengadaan</label></div></td><td class="center aligned"><div class="ui toggle checkbox"><input type="radio" name="to_kirim_'.$i.'" id="to_kirim_s_'.$i.'" value="S" '.$sendiri.' onclick="if(this.checked){countTotal()}"><label>Sendiri</label></div><input type="hidden" name="id_beli_'.$i.'" id="id_beli_'.$i.'" value="'.$dj[$i]->id.'"></td><td class="center aligned"><div class="ui circular tiny icon twitter button" onclick=resetVal("'.$i.'") title="Reset"><i class="sync alternate icon"></i></div></td></tr>';
					
					$total_pcs_all = $total_pcs_all + $dj[$i]->product_pcs;
					$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
					$total_jual_all = $total_jual_all + $dj[$i]->product_price;
					
					$total_pcs_temp = $total_pcs_temp + $dj[$i]->product_pcs;
					$total_gram_temp = $total_gram_temp + $dj[$i]->product_weight;
					$total_temp = $total_temp + $dj[$i]->product_price;
				}
			}
		}
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function save_kirim_new(){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		
		$length = $this->input->post('data_length');
		$tgl_transaksi = $this->input->post('tanggal_pengiriman');
		
		$tglTrans = $this->date_to_format($tgl_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		
		$created_by = $this->session->userdata('nama_user');
		
		$data_karat = $this->mk->get_karat_sdr();
		
		$karat_real_sdr = array();
		$karat_konv_sdr = array();
		$persen_karat_sdr = array();
		$karat_real_sdg = array();
		
		foreach($data_karat as $dk){
			$karat_real_sdr[$dk->id] = 0;
			$karat_konv_sdr[$dk->id] = 0;
			$persen_karat_sdr[$dk->id] = 0;
			$karat_real_sdg[$dk->id] = 0;
		}
		
		for($i = 0;$i < $length; $i++){
			$id = $this->input->post('id_beli_'.$i);
			$tujuan = $this->input->post('to_kirim_'.$i);
			
			if($tujuan != ''){
				$data_beli = $this->mt->get_detail_beli_by_id($id);
				if($tujuan == 'R'){
					foreach($data_beli as $db){
						$karat_beli = $db->id_karat;
						$berat_beli = $db->product_weight;
					}
					
					$karat_real_sdr[$karat_beli] = $karat_real_sdr[$karat_beli] + $berat_beli;
					
					$persen_reparasi = $this->mt->get_reparasi_to_persen($karat_beli);
					$dua_empat = $berat_beli * $persen_reparasi / 100;
					
					$karat_konv_sdr[$karat_beli] = $karat_konv_sdr[$karat_beli] + $dua_empat;
					$this->mt->input_kirim_pembelian($id,$tujuan,$tgl_transaksi,$persen_reparasi,$dua_empat,$created_by);
				}else if($tujuan == 'G'){
					foreach($data_beli as $db){
						$karat_beli = $db->id_karat;
						$berat_beli = $db->product_weight;
					}
					
					$karat_real_sdg[$karat_beli] = $karat_real_sdg[$karat_beli] + $berat_beli;
					$persen_reparasi = '';
					$dua_empat = '';
					
					$this->mt->input_kirim_pembelian($id,$tujuan,$tgl_transaksi,$persen_reparasi,$dua_empat,$created_by);
				}else if($tujuan == 'S'){
					foreach($data_beli as $db){
						$karat_beli = $db->id_karat;
						$berat_beli = $db->product_weight;
					}
					
					$persen_reparasi = '';
					$dua_empat = '';
					
					$this->mt->input_kirim_pembelian($id,$tujuan,$tgl_transaksi,$persen_reparasi,$dua_empat,$created_by);
				}
			}else{
				$this->mt->input_kirim_pembelian($id,'','0000-00-00 00:00:00','0','0',$created_by);
			}
		}
		
		$dua_empat = 1;
		$semsanam = 3;
		$juhlima = 4;
		$juhtus = 5;
		
		$dua_empat_real = $karat_real_sdr[$dua_empat];
		$semsanam_real = $karat_real_sdr[$semsanam];
		$juhlima_real = $karat_real_sdr[$juhlima];
		$juhtus_real = $karat_real_sdr[$juhtus];
		
		$dua_empat_konv = $karat_konv_sdr[$dua_empat];
		$semsanam_konv = $karat_konv_sdr[$semsanam];
		$juhlima_konv = $karat_konv_sdr[$juhlima];
		$juhtus_konv = $karat_konv_sdr[$juhtus];
		
		$dua_empat_sdg = $karat_real_sdg[$dua_empat];
		$semsanam_sdg = $karat_real_sdg[$semsanam];
		$juhlima_sdg = $karat_real_sdg[$juhlima];
		$juhtus_sdg = $karat_real_sdg[$juhtus];
		
		$data_kirim_sdr = $this->mt->get_kirim_sdr($tgl_transaksi);
		
		$codetrans = strtotime($tgl_transaksi);
		$tgl_kirim = date('d M Y',$codetrans);
		$codetrans = date('ymd',$codetrans);
		
		$transactioncode = 'TR';
		$transactioncode2 = 'TG';
		$sitecode = $this->mm->get_site_code();
		
		$totalnumberlength = 3;
		$numberlength = strlen($sitecode);
		$numberspace = $totalnumberlength - $numberlength;
		if($numberspace != 0){
			for ($i = 1; $i <= $numberspace; $i++){
				$transactioncode .= '0';
				$transactioncode2 .= '0';
			}
		}
		
		$transactioncode .= $sitecode.'-'.$codetrans;
		$transactioncode2 .= $sitecode.'-'.$codetrans;
		
		$tipe = 'In';
		
		$total_konv_sdr = $dua_empat_konv + $semsanam_konv + $juhlima_konv + $juhtus_konv;
		
		$account_srt = $this->mm->get_default_account('SRT');
		$account_sdr = $this->mm->get_default_account('SDR');
		$account_sdg = $this->mm->get_default_account('SDG');
		
		$from_buy = 'Y';
		
		if(count($data_kirim_sdr) == 0){
			$this->mt->input_main_reparasi($transactioncode,$from_buy,$account_srt,$account_sdr,$tipe,'Konversi 24K ke Reparasi Tgl '.$tgl_kirim,$dua_empat_real,$dua_empat_konv,$semsanam_real,$semsanam_konv,$juhlima_real,$juhlima_konv,$juhtus_real,$juhtus_konv,$total_konv_sdr,$tgl_transaksi,$created_by);
			
			$this->mm->insert_mutasi_gram($sitecode,$transactioncode.'-1','Out','1',$account_srt,$account_sdr,$dua_empat_real,'24K ke Reparasi Tgl '.$tgl_kirim,$tgl_transaksi,$created_by);
			$this->mm->insert_mutasi_gram($sitecode,$transactioncode.'-3','Out','3',$account_srt,$account_sdr,$semsanam_real,'916 ke Reparasi Tgl '.$tgl_kirim,$tgl_transaksi,$created_by);
			$this->mm->insert_mutasi_gram($sitecode,$transactioncode.'-4','Out','4',$account_srt,$account_sdr,$juhlima_real,'750 ke Reparasi Tgl '.$tgl_kirim,$tgl_transaksi,$created_by);
			$this->mm->insert_mutasi_gram($sitecode,$transactioncode.'-5','Out','5',$account_srt,$account_sdr,$juhtus_real,'700 ke Reparasi Tgl '.$tgl_kirim,$tgl_transaksi,$created_by);
		}else{
			$this->mt->update_main_reparasi($dua_empat_real,$dua_empat_konv,$semsanam_real,$semsanam_konv,$juhlima_real,$juhlima_konv,$juhtus_real,$juhtus_konv,$total_konv_sdr,$tgl_transaksi,$created_by);
			
			$this->mm->update_mutasi_gram($transactioncode.'-1',$dua_empat_real);
			$this->mm->update_mutasi_gram($transactioncode.'-3',$semsanam_real);
			$this->mm->update_mutasi_gram($transactioncode.'-4',$juhlima_real);
			$this->mm->update_mutasi_gram($transactioncode.'-5',$juhtus_real);
		}
		
		$data_kirim_sdg = $this->mt->get_kirim_sdg($tgl_transaksi);
		$total_konv_sdg = 0;
		if(count($data_kirim_sdg) == 0){
			$this->mt->input_main_pengadaan($transactioncode2,$from_buy,$account_srt,$account_sdg,$tipe,'Konversi 24K ke Pengadaan Tgl '.$tgl_kirim,$dua_empat_sdg,$semsanam_sdg,$juhlima_sdg,$juhtus_sdg,$total_konv_sdg,$tgl_transaksi,$created_by);
			
			$this->mm->insert_mutasi_gram($sitecode,$transactioncode2.'-1','Out','1',$account_srt,$account_sdg,$dua_empat_sdg,'24K ke Pengadaan Tgl '.$tgl_kirim,$tgl_transaksi,$created_by);
			$this->mm->insert_mutasi_gram($sitecode,$transactioncode2.'-3','Out','3',$account_srt,$account_sdg,$semsanam_sdg,'916 ke Pengadaan Tgl '.$tgl_kirim,$tgl_transaksi,$created_by);
			$this->mm->insert_mutasi_gram($sitecode,$transactioncode2.'-4','Out','4',$account_srt,$account_sdg,$juhlima_sdg,'750 ke Pengadaan Tgl '.$tgl_kirim,$tgl_transaksi,$created_by);
			$this->mm->insert_mutasi_gram($sitecode,$transactioncode2.'-5','Out','5',$account_srt,$account_sdg,$juhtus_sdg,'700 ke Pengadaan Tgl '.$tgl_kirim,$tgl_transaksi,$created_by);
		}else{
			$this->mt->update_main_pengadaan($dua_empat_sdg,$semsanam_sdg,$juhlima_sdg,$juhtus_sdg,$tgl_transaksi,$created_by);
			
			$this->mm->update_mutasi_gram($transactioncode2.'-1',$dua_empat_sdg);
			$this->mm->update_mutasi_gram($transactioncode2.'-3',$semsanam_sdg);
			$this->mm->update_mutasi_gram($transactioncode2.'-4',$juhlima_sdg);
			$this->mm->update_mutasi_gram($transactioncode2.'-5',$juhtus_sdg);
		}
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function filter(){
		$this->db->trans_start();
		
		$filter_category = $this->input->post('filter_category');
		if($filter_category == 'All'){
			$filter_category = '';
			$data_category = $this->mc->get_product_category();
			for($i=0; $i<count($data_category); $i++){
				if($i == 0){
					$filter_category .= '"'.$data_category[$i]->id.'"';
				}else{
					$filter_category .= ',"'.$data_category[$i]->id.'"';
				}
			}
		}else{
			$filter_category = '"'.$filter_category.'"';
		}
		
		$filter_to = $this->input->post('filter_to');
		if($filter_to == 'All'){
			$filter_to = '"R","G","S"';
		}else{
			$filter_to = '"'.$filter_to.'"';
		}
		
		$filter_karat = $this->input->post('filter_karat');
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
		
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		
		$stockFromTime = $this->date_to_format($from_date);
		$stockToTime = $this->date_to_format($to_date);
		
		$from_date = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_date = date("Y-m-d",$stockToTime).' 00:00:00';
		
		$rekap_detail = $this->input->post('rekap_detail');
		
		if($rekap_detail == 'D'){
			$data_filter = $this->mt->get_filter_kirim_beli($from_date,$to_date,$filter_category,$filter_to,$filter_karat);
		
			$data['view'] = '<table id="filter_data_tabel" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:50px">No</th><th>Tgl Kirim</th><th>ID Pembelian</th><th>Kelompok</th><th>Keterangan</th><th>Karat</th><th>Pcs</th><th>Berat Real</th><th>Tujuan</th></tr></thead><tbody>';
			
			$number = 1;
			$total_berat = 0;
			$total_pcs = 0;
			foreach($data_filter as $d){
				$tujuan = '';
				
				if($d->tujuan == 'R'){
					$tujuan = 'Reparasi';
				}else if($d->tujuan == 'G'){
					$tujuan = 'Pengadaan';
				}else if($d->tujuan == 'S'){
					$tujuan = 'Sendiri';
				}
				
				$tanggal_tulis = strtotime($d->kirim_date);
				$tanggal_tulis = date('d-M-y',$tanggal_tulis);
				
				$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$tanggal_tulis.'</td><td>'.$d->transaction_code.'</td><td>'.$d->category_name.'</td><td>'.$d->nama_product.'</td><td class="center aligned">'.$d->karat_name.'</td><td class="right aligned">'.$d->product_pcs.'</td><td class="right aligned">'.number_format($d->product_weight, 3).'</td><td>'.$tujuan.'</td></tr>';
				
				$number = $number + 1;
				$total_berat = $total_berat + $d->product_weight;
				$total_pcs = $total_pcs + $d->product_pcs;
			}
			
			$data['view'] .= '</tbody>';
			$data['view'] .= '<tfoot><tr><th colspan="6" class="right aligned">Total</td><th class="right aligned" style="text-align:right">'.$total_pcs.'</td><th class="right aligned" style="text-align:right">'.number_format($total_berat, 3).'</td><th></td></tr></tfoot>';
			$data['view'] .= '</table>';
		}else if($rekap_detail == 'R'){
			$filter_category = '';
			$data_category = $this->mc->get_product_category();
			for($i=0; $i<count($data_category); $i++){
				if($i == 0){
					$filter_category .= '"'.$data_category[$i]->id.'"';
				}else{
					$filter_category .= ',"'.$data_category[$i]->id.'"';
				}
			}
			
			$filter_karat = '';
			$data_karat = $this->mk->get_karat_srt();
			for($i=0; $i<count($data_karat); $i++){
				if($i == 0){
					$filter_karat .= '"'.$data_karat[$i]->id.'"';
				}else{
					$filter_karat .= ',"'.$data_karat[$i]->id.'"';
				}
			}
			
			$filter_to = $this->input->post('filter_to');
			
			if($filter_to == 'R'){
				$filter_to = "'".$filter_to."'";
				
				$data['view'] = '<table id="filter_data_tabel" class="ui celled table" cellspacing="0"><thead><tr><th style="width:50px">No</th><th>Karat</th><th>Jenis</th><th>Unit</th><th>Berat (Gr)</th></tr></thead><tbody>';
				
				$data_karat = $this->mk->get_karat_sdr();
				$array_kalung_unit = array();
				$array_gelang_unit = array();
				$array_cincin_unit = array();
				$array_anting_unit = array();
				$array_lain_unit = array();
				
				$array_kalung_berat = array();
				$array_gelang_berat = array();
				$array_cincin_berat = array();
				$array_anting_berat = array();
				$array_lain_berat = array();
				
				$kategori = array("KALUNG", "GELANG", "CINCIN", "ANTING");
				
				foreach($data_karat as $dk){
					$array_kalung_unit[$dk->id] = 0;
					$array_gelang_unit[$dk->id] = 0;
					$array_cincin_unit[$dk->id] = 0;
					$array_anting_unit[$dk->id] = 0;
					$array_lain_unit[$dk->id] = 0;
					
					$array_kalung_berat[$dk->id] = 0;
					$array_gelang_berat[$dk->id] = 0;
					$array_cincin_berat[$dk->id] = 0;
					$array_anting_berat[$dk->id] = 0;
					$array_lain_berat[$dk->id] = 0;
				}
				
				$data_filter = $this->mt->get_filter_kirim_beli($from_date,$to_date,$filter_category,$filter_to,$filter_karat);
				
				foreach($data_filter as $d){
					if(in_array($d->category_name, $kategori)){
						if($d->category_name == 'KALUNG'){
							$array_kalung_unit[$d->id_karat] = $array_kalung_unit[$d->id_karat] + $d->product_pcs;
							$array_kalung_berat[$d->id_karat] = $array_kalung_berat[$d->id_karat] + $d->product_weight;
						}else if($d->category_name == 'GELANG'){
							$array_gelang_unit[$d->id_karat] = $array_gelang_unit[$d->id_karat] + $d->product_pcs;
							$array_gelang_berat[$d->id_karat] = $array_gelang_berat[$d->id_karat] + $d->product_weight;
						}else if($d->category_name == 'CINCIN'){
							$array_cincin_unit[$d->id_karat] = $array_cincin_unit[$d->id_karat] + $d->product_pcs;
							$array_cincin_berat[$d->id_karat] = $array_cincin_berat[$d->id_karat] + $d->product_weight;
						}else if($d->category_name == 'ANTING'){
							$array_anting_unit[$d->id_karat] = $array_anting_unit[$d->id_karat] + $d->product_pcs;
							$array_anting_berat[$d->id_karat] = $array_anting_berat[$d->id_karat] + $d->product_weight;
						}
					}else{
						$array_lain_unit[$d->id_karat] = $array_lain_unit[$d->id_karat] + $d->product_pcs;
						$array_lain_berat[$d->id_karat] = $array_lain_berat[$d->id_karat] + $d->product_weight;
					}
				}
				
				$number = 1;
				$baris = count($kategori) + 1;
				foreach($data_karat as $dk){
					$data['view'] .= '<tr><td rowspan="'.$baris.'" class="center aligned align-top">'.$number.'</td><td rowspan="'.$baris.'" class="center aligned align-top">'.$dk->karat_name.'</td>';
					
					$total_unit = $array_kalung_unit[$dk->id] + $array_gelang_unit[$dk->id] + $array_cincin_unit[$dk->id] + $array_anting_unit[$dk->id] + $array_lain_unit[$dk->id];
					
					$total_berat = $array_kalung_berat[$dk->id] + $array_gelang_berat[$dk->id] + $array_cincin_berat[$dk->id] + $array_anting_berat[$dk->id] + $array_lain_berat[$dk->id];
					
					if($array_kalung_unit[$dk->id] == 0){
						$unit_kalung = '';
					}else{
						$unit_kalung = $array_kalung_unit[$dk->id];
					}
					
					if($array_gelang_unit[$dk->id] == 0){
						$unit_gelang = '';
					}else{
						$unit_gelang = $array_gelang_unit[$dk->id];
					}
					
					if($array_cincin_unit[$dk->id] == 0){
						$unit_cincin = '';
					}else{
						$unit_cincin = $array_cincin_unit[$dk->id];
					}
					
					if($array_anting_unit[$dk->id] == 0){
						$unit_anting = '';
					}else{
						$unit_anting = $array_anting_unit[$dk->id];
					}
					
					if($array_lain_unit[$dk->id] == 0){
						$unit_lain = '';
					}else{
						$unit_lain = $array_lain_unit[$dk->id];
					}
					
					if($array_kalung_berat[$dk->id] == 0){
						$berat_kalung = '';
					}else{
						$berat_kalung = number_format($array_kalung_berat[$dk->id], 3);
					}
					
					if($array_gelang_berat[$dk->id] == 0){
						$berat_gelang = '';
					}else{
						$berat_gelang = number_format($array_gelang_berat[$dk->id], 3);
					}
					
					if($array_cincin_berat[$dk->id] == 0){
						$berat_cincin = '';
					}else{
						$berat_cincin = number_format($array_cincin_berat[$dk->id], 3);
					}
					
					if($array_anting_berat[$dk->id] == 0){
						$berat_anting = '';
					}else{
						$berat_anting = number_format($array_anting_berat[$dk->id], 3);
					}
					
					if($array_lain_berat[$dk->id] == 0){
						$berat_lain = '';
					}else{
						$berat_lain = number_format($array_lain_berat[$dk->id], 3);
					}
					
					$data['view'] .= '<td>KALUNG</td><td class="right aligned">'.$unit_kalung.'</td><td class="right aligned">'.$berat_kalung.'</td></tr>';
					
					$data['view'] .= '<tr><td>GELANG</td><td class="right aligned">'.$unit_gelang.'</td><td class="right aligned">'.$berat_gelang.'</td></tr>';
					
					$data['view'] .= '<tr><td>CINCIN</td><td class="right aligned">'.$unit_cincin.'</td><td class="right aligned">'.$berat_cincin.'</td></tr>';
					
					$data['view'] .= '<tr><td>ANTING</td><td class="right aligned">'.$unit_anting.'</td><td class="right aligned">'.$berat_anting.'</td></tr>';
					
					$data['view'] .= '<tr><td>LAIN-LAIN</td><td class="right aligned">'.$unit_lain.'</td><td class="right aligned">'.$berat_lain.'</td></tr>';
					
					$data['view'] .= '<tr><td class="center aligned"></td><td colspan="2" class="right aligned td-bold">TOTAL EMAS '.$dk->karat_name.'</td><td class="right aligned td-bold">'.$total_unit.'</td><td class="right aligned td-bold">'.number_format($total_berat, 3).'</td></tr>';
					
					$number = $number + 1;
				}
				
				$data['view'] .= '</tbody></table>';
			}else if($filter_to == 'G'){
				$filter_to = "'".$filter_to."'";
				
				$data['view'] = '<table id="filter_data_tabel" class="ui celled table" cellspacing="0"><thead><tr><th style="width:50px">No</th><th>Karat</th><th>Jenis</th><th>Unit</th><th>Berat (Gr)</th><th>Harga Beli</th></tr></thead><tbody>';
				
				$data_karat = $this->mk->get_karat_sdr();
				$array_bahan_unit = array();
				$array_kalung_unit = array();
				$array_gelang_unit = array();
				$array_cincin_unit = array();
				$array_anting_unit = array();
				$array_lain_unit = array();
				
				$array_bahan_berat = array();
				$array_kalung_berat = array();
				$array_gelang_berat = array();
				$array_cincin_berat = array();
				$array_anting_berat = array();
				$array_lain_berat = array();
				
				$array_bahan_harga = array();
				$array_kalung_harga = array();
				$array_gelang_harga = array();
				$array_cincin_harga = array();
				$array_anting_harga = array();
				$array_lain_harga = array();
				
				$kategori = array("BAHAN","KALUNG", "GELANG", "CINCIN", "ANTING");
				
				foreach($data_karat as $dk){
					$array_bahan_unit[$dk->id] = 0;
					$array_kalung_unit[$dk->id] = 0;
					$array_gelang_unit[$dk->id] = 0;
					$array_cincin_unit[$dk->id] = 0;
					$array_anting_unit[$dk->id] = 0;
					$array_lain_unit[$dk->id] = 0;
					
					$array_bahan_berat[$dk->id] = 0;
					$array_kalung_berat[$dk->id] = 0;
					$array_gelang_berat[$dk->id] = 0;
					$array_cincin_berat[$dk->id] = 0;
					$array_anting_berat[$dk->id] = 0;
					$array_lain_berat[$dk->id] = 0;
					
					$array_bahan_harga[$dk->id] = 0;
					$array_kalung_harga[$dk->id] = 0;
					$array_gelang_harga[$dk->id] = 0;
					$array_cincin_harga[$dk->id] = 0;
					$array_anting_harga[$dk->id] = 0;
					$array_lain_harga[$dk->id] = 0;
				}
				
				$data_filter = $this->mt->get_filter_kirim_beli($from_date,$to_date,$filter_category,$filter_to,$filter_karat);
				
				foreach($data_filter as $d){
					if(in_array($d->category_name, $kategori)){
						if($d->category_name == 'BAHAN'){
							$array_bahan_unit[$d->id_karat] = $array_bahan_unit[$d->id_karat] + $d->product_pcs;
							$array_bahan_berat[$d->id_karat] = $array_bahan_berat[$d->id_karat] + $d->product_weight;
							$array_bahan_harga[$d->id_karat] = $array_bahan_harga[$d->id_karat] + $d->product_price;
						}else if($d->category_name == 'KALUNG'){
							$array_kalung_unit[$d->id_karat] = $array_kalung_unit[$d->id_karat] + $d->product_pcs;
							$array_kalung_berat[$d->id_karat] = $array_kalung_berat[$d->id_karat] + $d->product_weight;
							$array_kalung_harga[$d->id_karat] = $array_kalung_harga[$d->id_karat] + $d->product_price;
						}else if($d->category_name == 'GELANG'){
							$array_gelang_unit[$d->id_karat] = $array_gelang_unit[$d->id_karat] + $d->product_pcs;
							$array_gelang_berat[$d->id_karat] = $array_gelang_berat[$d->id_karat] + $d->product_weight;
							$array_gelang_harga[$d->id_karat] = $array_gelang_harga[$d->id_karat] + $d->product_price;
						}else if($d->category_name == 'CINCIN'){
							$array_cincin_unit[$d->id_karat] = $array_cincin_unit[$d->id_karat] + $d->product_pcs;
							$array_cincin_berat[$d->id_karat] = $array_cincin_berat[$d->id_karat] + $d->product_weight;
							$array_cincin_harga[$d->id_karat] = $array_cincin_harga[$d->id_karat] + $d->product_price;
						}else if($d->category_name == 'ANTING'){
							$array_anting_unit[$d->id_karat] = $array_anting_unit[$d->id_karat] + $d->product_pcs;
							$array_anting_berat[$d->id_karat] = $array_anting_berat[$d->id_karat] + $d->product_weight;
							$array_anting_harga[$d->id_karat] = $array_anting_harga[$d->id_karat] + $d->product_price;
						}
					}else{
						$array_lain_unit[$d->id_karat] = $array_lain_unit[$d->id_karat] + $d->product_pcs;
						$array_lain_berat[$d->id_karat] = $array_lain_berat[$d->id_karat] + $d->product_weight;
						$array_lain_harga[$d->id_karat] = $array_lain_harga[$d->id_karat] + $d->product_weight;
					}
				}
				
				$number = 1;
				$baris = count($kategori) + 1;
				foreach($data_karat as $dk){
					$data['view'] .= '<tr><td rowspan="'.$baris.'" class="center aligned align-top">'.$number.'</td><td rowspan="'.$baris.'" class="center aligned align-top">'.$dk->karat_name.'</td>';
					
					$total_unit = $array_bahan_unit[$dk->id] + $array_kalung_unit[$dk->id] + $array_gelang_unit[$dk->id] + $array_cincin_unit[$dk->id] + $array_anting_unit[$dk->id] + $array_lain_unit[$dk->id];
					
					$total_berat = $array_bahan_berat[$dk->id] + $array_kalung_berat[$dk->id] + $array_gelang_berat[$dk->id] + $array_cincin_berat[$dk->id] + $array_anting_berat[$dk->id] + $array_lain_berat[$dk->id];
					
					$total_harga = $array_bahan_harga[$dk->id] + $array_kalung_harga[$dk->id] + $array_gelang_harga[$dk->id] + $array_cincin_harga[$dk->id] + $array_anting_harga[$dk->id] + $array_lain_harga[$dk->id];
					
					if($array_bahan_unit[$dk->id] == 0){
						$unit_bahan = '';
					}else{
						$unit_bahan = $array_bahan_unit[$dk->id];
					}
					
					if($array_kalung_unit[$dk->id] == 0){
						$unit_kalung = '';
					}else{
						$unit_kalung = $array_kalung_unit[$dk->id];
					}
					
					if($array_gelang_unit[$dk->id] == 0){
						$unit_gelang = '';
					}else{
						$unit_gelang = $array_gelang_unit[$dk->id];
					}
					
					if($array_cincin_unit[$dk->id] == 0){
						$unit_cincin = '';
					}else{
						$unit_cincin = $array_cincin_unit[$dk->id];
					}
					
					if($array_anting_unit[$dk->id] == 0){
						$unit_anting = '';
					}else{
						$unit_anting = $array_anting_unit[$dk->id];
					}
					
					if($array_lain_unit[$dk->id] == 0){
						$unit_lain = '';
					}else{
						$unit_lain = $array_lain_unit[$dk->id];
					}
					
					if($array_bahan_berat[$dk->id] == 0){
						$berat_bahan = '';
					}else{
						$berat_bahan = number_format($array_bahan_berat[$dk->id], 3);
					}
					
					if($array_kalung_berat[$dk->id] == 0){
						$berat_kalung = '';
					}else{
						$berat_kalung = number_format($array_kalung_berat[$dk->id], 3);
					}
					
					if($array_gelang_berat[$dk->id] == 0){
						$berat_gelang = '';
					}else{
						$berat_gelang = number_format($array_gelang_berat[$dk->id], 3);
					}
					
					if($array_cincin_berat[$dk->id] == 0){
						$berat_cincin = '';
					}else{
						$berat_cincin = number_format($array_cincin_berat[$dk->id], 3);
					}
					
					if($array_anting_berat[$dk->id] == 0){
						$berat_anting = '';
					}else{
						$berat_anting = number_format($array_anting_berat[$dk->id], 3);
					}
					
					if($array_lain_berat[$dk->id] == 0){
						$berat_lain = '';
					}else{
						$berat_lain = number_format($array_lain_berat[$dk->id], 3);
					}
					
					if($array_bahan_harga[$dk->id] == 0){
						$harga_bahan = '';
					}else{
						$harga_bahan = number_format($array_bahan_harga[$dk->id], 0);
					}
					
					if($array_kalung_harga[$dk->id] == 0){
						$harga_kalung = '';
					}else{
						$harga_kalung = number_format($array_kalung_harga[$dk->id], 0);
					}
					
					if($array_gelang_harga[$dk->id] == 0){
						$harga_gelang = '';
					}else{
						$harga_gelang = number_format($array_gelang_harga[$dk->id], 0);
					}
					
					if($array_cincin_harga[$dk->id] == 0){
						$harga_cincin = '';
					}else{
						$harga_cincin = number_format($array_cincin_harga[$dk->id], 0);
					}
					
					if($array_anting_harga[$dk->id] == 0){
						$harga_anting = '';
					}else{
						$harga_anting = number_format($array_anting_harga[$dk->id], 0);
					}
					
					if($array_lain_harga[$dk->id] == 0){
						$harga_lain = '';
					}else{
						$harga_lain = number_format($array_lain_harga[$dk->id], 0);
					}
					
					$data['view'] .= '<td>BAHAN</td><td class="right aligned">'.$unit_bahan.'</td><td class="right aligned">'.$berat_bahan.'</td><td class="right aligned">'.$harga_bahan.'</td></tr>';
					
					$data['view'] .= '<td>KALUNG</td><td class="right aligned">'.$unit_kalung.'</td><td class="right aligned">'.$berat_kalung.'</td><td class="right aligned">'.$harga_kalung.'</td></tr>';
					
					$data['view'] .= '<tr><td>GELANG</td><td class="right aligned">'.$unit_gelang.'</td><td class="right aligned">'.$berat_gelang.'</td><td class="right aligned">'.$harga_gelang.'</td></tr>';
					
					$data['view'] .= '<tr><td>CINCIN</td><td class="right aligned">'.$unit_cincin.'</td><td class="right aligned">'.$berat_cincin.'</td><td class="right aligned">'.$harga_cincin.'</td></tr>';
					
					$data['view'] .= '<tr><td>ANTING</td><td class="right aligned">'.$unit_anting.'</td><td class="right aligned">'.$berat_anting.'</td><td class="right aligned">'.$harga_anting.'</td></tr>';
					
					$data['view'] .= '<tr><td>LAIN-LAIN</td><td class="right aligned">'.$unit_lain.'</td><td class="right aligned">'.$berat_lain.'</td><td class="right aligned">'.$harga_lain.'</td></tr>';
					
					$data['view'] .= '<tr><td class="center aligned"></td><td colspan="2" class="right aligned td-bold">TOTAL EMAS '.$dk->karat_name.'</td><td class="right aligned td-bold">'.$total_unit.'</td><td class="right aligned td-bold">'.number_format($total_berat, 3).'</td><td class="right aligned td-bold">'.number_format($total_harga, 0).'</td></tr>';
					
					$number = $number + 1;
				}
				
				$data['view'] .= '</tbody></table>';
			}
			
		}
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
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
	
	public function date_to_format_2($tanggal_mentah){
		$dateArray = explode('%20', $tanggal_mentah);
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
