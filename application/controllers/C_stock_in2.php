<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_stock_in extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->model('M_karat','mk');
		$this->load->model('M_box','mb');
		$this->load->model('M_kelompok_barang','mc');
		$this->load->model('M_product','mp');
		$this->load->model('M_nama_barang','mmp');
		$this->load->model('M_mutasi','mm');
	}
	
	public function index(){
		$sitecode = $this->mm->get_site_code();
		
		$product_id = '00'.$sitecode.''.date('y').'-';
		
		$transnumber = $this->mp->get_trans_number();
		$countnumber = count($transnumber);
		
		if($countnumber == 0){
			$numbertrans = 0;
		}else{
			$numArray = explode('-', $transnumber[0]->id);
			$numbertrans = $numArray[1];
			$numbertrans = (int)$numbertrans;
		}
		
		$numbertrans = $numbertrans + 1;
		$totalnumberlength = 10;
		$numberlength = strlen($numbertrans);
		$numberspace = $totalnumberlength - $numberlength;
		if($numberspace != 0){
			for ($i = 1; $i <= $numberspace; $i++){
				$product_id .= '0';
			}
		}
		
		$product_id .= $numbertrans;
		
		$data['id_urutan'] = $product_id;
		$data['karat'] = $this->mk->get_karat_srt();
		$data['box'] = $this->mb->get_box_aktif();
		$data['category'] = $this->mc->get_product_category();
		$data['from'] = $this->mp->get_product_from();
		$data['from_filter'] = $this->mp->get_product_from_filter();
		$this->load->view('persediaan/V_stock_in',$data);
	}
	
	public function get_ket_from($id_from){
		$from_data = $this->mp->get_product_from_detail($id_from);
		if($from_data[0]->from_name == 'LAIN-LAIN'){
			$data['edit'] = TRUE;
			$data['ketvalue'] = $from_data[0]->from_desc;
		}else{
			$data['edit'] = FALSE;
			$data['ketvalue'] = $from_data[0]->from_desc;
		}
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function get_master_product($id_category,$id_row){
		$master_barang = $this->mmp->get_master_by_category($id_category);
		$data['view'] = '<select name="nama_barang_'.$id_row.'" id="input_'.$id_row.'_4"><option value="">-- Nama Barang --</option>';
		
		foreach($master_barang as $mb){
			$data['view'] .= '<option value="'.$mb->nama_barang.'">'.$mb->nama_barang.'</option>';
		}
		
		$data['view'] .= '</select>';
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function save_baris($id_row){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		$this->validate($id_row);
		
		$tgl_stock_in = $this->input->post('tanggal_stock_in');
		$tanggal_stock_in = $this->input->post('tanggal_stock_in');
		$select_from = $this->input->post('select_from');
		$select_from_desc = $this->input->post('ket_select_from');
		
		$select_karat = $this->input->post('id_karat_'.$id_row);
		$select_box = $this->input->post('id_box_'.$id_row);
		$select_category = $this->input->post('id_category_'.$id_row);
		$nama_barang = $this->input->post('nama_barang_'.$id_row);
		$berat_barang = $this->input->post('berat_'.$id_row);
		$berat_barang = str_replace(',','',$berat_barang);
		
		$dateArray = explode(' ', $tgl_stock_in);
		$transTanggal = $dateArray[0];
		$transMonth = $dateArray[1];
		$transTahun = $dateArray[2];
			
		switch($transMonth){
			case "January":
				$transBulan = '1';
				break;
			case "February":
				$transBulan = '2';
				break;
			case "March":
				$transBulan = '3';
				break;
			case "April":
				$transBulan = '4';
				break;
			case "May":
				$transBulan = '5';
				break;
			case "June":
				$transBulan = '6';
				break;
			case "July":
				$transBulan = '7';
				break;
			case "August":
				$transBulan = '8';
				break;
			case "September":
				$transBulan = '9';
				break;
			case "October":
				$transBulan = '10';
				break;
			case "November":
				$transBulan = '11';
				break;
			case "December":
				$transBulan = '12';
				break;
		}
			
		$reportTime = strtotime($transBulan.'/'.$transTanggal.'/'.$transTahun);
		$tgl_stock_in = date("Y-m-d",$reportTime).' 00:00:00';
		
		$sitecode = $this->mm->get_site_code();
		
		$stock_in_id = 'SI-00'.$sitecode.''.date('y').'-';
		$product_id = '00'.$sitecode.''.date('y').'-';
		
		$transnumber = $this->mp->get_trans_number();
		$countnumber = count($transnumber);
		
		if($countnumber == 0){
			$numbertrans = 0;
		}else{
			$numArray = explode('-', $transnumber[0]->id);
			$numbertrans = $numArray[1];
			$numbertrans = (int)$numbertrans;
		}
		
		$numbertrans = $numbertrans + 1;
		$totalnumberlength = 10;
		$numberlength = strlen($numbertrans);
		$numberspace = $totalnumberlength - $numberlength;
		if($numberspace != 0){
			for ($i = 1; $i <= $numberspace; $i++){
				$stock_in_id .= '0';
				$product_id .= '0';
			}
		}
		
		$stock_in_id .= $numbertrans;
		$product_id .= $numbertrans;
		
		$created_by = $this->session->userdata('gold_nama_user');
		$id_lama = 'NULL';
		
		$this->mp->insert_stock_in($stock_in_id,$select_karat,$select_box,$select_category,$select_from,$select_from_desc,$nama_barang,$berat_barang,$tgl_stock_in,$created_by);
		$this->mp->insert_product($product_id,$id_lama,$select_karat,$select_box,$select_category,$select_from,$select_from_desc,$nama_barang,$berat_barang,$tgl_stock_in,$created_by);
		
		$account_srt = $this->mm->get_default_account('SRT');
		$account_pjg = $this->mm->get_default_account('PJG');
		
		$desc = 'STOCK IN - NO.REG : '.$product_id;
		
		$this->mm->insert_mutasi_gram($sitecode,$stock_in_id,'In',$select_karat,$account_srt,$account_pjg,$berat_barang,$desc,$tgl_stock_in,$created_by);
		
		$this->db->trans_complete();
		
		$karat = $this->mk->get_karat_srt();
		$box = $this->mb->get_box_aktif();
		$category = $this->mc->get_product_category();
		$from_name = $this->mp->get_product_from_detail($select_from);
		foreach($from_name as $fn){
			$select_from_name = $fn->from_name;
		}
		
		$data['select_from'] = '<input type="text" name="select_from_val" id="select_from_val" value="'.$select_from_name.'" readonly onkeydown=entToHeader("input_1_1")><input type="hidden" name="select_from" id="select_from" value="'.$select_from.'">';
		
		$data['tanggal_stock_in'] = '<input type="text" name="tanggal_stock_in" id="tanggal_stock_in" value="'.$tanggal_stock_in.'" readonly>';
		
		$data['nama_barang'] = $nama_barang;
		
		$data['product_id'] = $product_id;
		$data['button_messsage'] = '<div class="ui tiny icon green button"><i class="check circle icon"></i></div>';
		
		$id_row = $id_row + 1;
		$data['view'] = '<tr id="pos_tr_'.$id_row.'"><td class="center aligned">'.$id_row.'</td><td><div id="wrap_id_karat_'.$id_row.'"><select onchange=entToTabInput("'.$id_row.'","1") name="id_karat_'.$id_row.'" id="input_'.$id_row.'_1"><option value=""></option>';
		
		foreach($karat as $k){
			$selected = '';
			if($k->id == $select_karat){
				$selected = 'selected';
			}
			$data['view'] .= '<option value="'.$k->id.'" '.$selected.'>'.$k->karat_name.'</option>';
		}
		
		$data['view'] .= '</select></div></td><td><div id="wrap_id_box_'.$id_row.'"><select onchange=entToTabInput("'.$id_row.'","2") name="id_box_'.$id_row.'" id="input_'.$id_row.'_2"><option value=""></option>';
		
		foreach($box as $b){
			$selected = '';
			if($b->id == $select_box){
				$selected = 'selected';
			}
			$data['view'] .= '<option value="'.$b->id.'" '.$selected.'>'.$b->nama_box.'</option>';
		}
																						
		$data['view'] .= '</select></div></td><td><div id="wrap_id_category_'.$id_row.'"><select name="id_category_'.$id_row.'" id="input_'.$id_row.'_3" onchange=getMasterProduct("'.$id_row.'")><option value=""></option>';
		
		foreach($category as $c){
			$selected = '';
			if($c->id == $select_category){
				$selected = 'selected';
			}
			$data['view'] .= '<option value="'.$c->id.'" '.$selected.'>'.$c->category_name.'</option>';
		}
		
		//$data['view'] .= '</select></div></td><td><div id="wrap_nama_barang_'.$id_row.'"><select name="nama_barang_'.$id_row.'" id="input_'.$id_row.'_4"><option value=""></option></select></div></td>';
		
		$data['view'] .= '</select></div></td><td><div id="wrap_nama_barang_'.$id_row.'"><select name="nama_barang_'.$id_row.'" id="input_'.$id_row.'_4">';
		
		$master_barang = $this->mmp->get_master_by_category($select_category);
		$data['view'] .= '<option value="">-- Nama Barang --</option>';
		
		foreach($master_barang as $mb){
			$selected = '';
			if($mb->nama_barang == $nama_barang){
				$selected = 'selected';
			}
			$data['view'] .= '<option value="'.$mb->nama_barang.'" '.$selected.'>'.$mb->nama_barang.'</option>';
		}
		
		$data['view'] .= '</select></div></td>';
		
		$data['view'] .= '<td><input class="form-pos" type="text" name="berat_'.$id_row.'" id="input_'.$id_row.'_5" onkeyup=beratToCurrency("'.$id_row.'") autocomplete="off"></td><td id="input_'.$id_row.'_6"></td><td class="center aligned" id="input_'.$id_row.'_7"><div class="ui tiny icon google plus button"><i class="ban icon"></i></div></td></tr>';
		
		$product_id = '00'.$sitecode.''.date('y').'-';
		
		$transnumber = $this->mp->get_trans_number();
		$countnumber = count($transnumber);
		
		if($countnumber == 0){
			$numbertrans = 0;
		}else{
			$numArray = explode('-', $transnumber[0]->id);
			$numbertrans = $numArray[1];
			$numbertrans = (int)$numbertrans;
		}
		
		$numbertrans = $numbertrans + 1;
		$totalnumberlength = 10;
		$numberlength = strlen($numbertrans);
		$numberspace = $totalnumberlength - $numberlength;
		if($numberspace != 0){
			for ($i = 1; $i <= $numberspace; $i++){
				$product_id .= '0';
			}
		}
		
		$product_id .= $numbertrans;
		
		$box_number = '';
		$totalnumberlength = 3;
		$numberlength = strlen($select_box);
		$numberspace = $totalnumberlength - $numberlength;
		if($numberspace != 0){
			for ($i = 1; $i <= $numberspace; $i++){
				$box_number .= '0';
			}
		}
		
		$box_number .= $select_box;
		$data_cat = $this->mc->get_category_by_id($select_category);
		
		$data['select_box'] = $box_number;
		$data['select_karat'] = $this->mk->get_karat_name_by_id($select_karat);
		$data['select_category'] = $data_cat[0]->category_name;
		
		$data['id_urutan'] = $product_id;
		$data['success'] = true;
		echo json_encode($data);
	}
	
	private function validate($id_row){
		$data = array();
		$data['inputerror'] = '<ul class="list"></ul>';
		$data['success'] = TRUE;
		$data['error_message'] = 'Data Belum Lengkap!<br>';
		
		$tanggal_stock_in = $this->input->post('tanggal_stock_in');
		$select_from = $this->input->post('select_from');
		$select_from_desc = $this->input->post('ket_select_from');
		
		if($tanggal_stock_in == ''){
			$data['inputerror'] .= '<li>Tanggal Stock In Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($select_from == ''){
			$data['inputerror'] .= '<li>Asal Barang Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($select_from_desc == ''){
			$data['inputerror'] .= '<li>Keterangan Asal Barang Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		$select_karat = $this->input->post('id_karat_'.$id_row);
		$select_box = $this->input->post('id_box_'.$id_row);
		$select_category = $this->input->post('id_category_'.$id_row);
		$nama_barang = $this->input->post('nama_barang_'.$id_row);
		$berat_barang = $this->input->post('berat_'.$id_row);
		
		if($select_karat == ''){
			$data['inputerror'] .= '<li>Karat Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($select_box == ''){
			$data['inputerror'] .= '<li>Box Harus Diisi!</li>';
			$data['success'] = FALSE;
		}

		if($select_category == ''){
			$data['inputerror'] .= '<li>Kelompok Barang Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($nama_barang == ''){
			$data['inputerror'] .= '<li>Nama Barang Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($berat_barang == '' || $berat_barang == 'NaN' || $berat_barang == 0){
			$data['inputerror'] .= '<li>Berat Barang Tidak Boleh Bernilai 0 dan Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($data['success'] == FALSE){
			echo json_encode($data);
			exit();
		}
	}
	
	public function filter_stock_in(){
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
		
		$filter_from = $this->input->post('filter_from');
		if($filter_from == 'All'){
			$filter_from = '';
			$data_from = $this->mp->get_product_from_filter();
			for($i=0; $i<count($data_from); $i++){
				if($i == 0){
					$filter_from .= '"'.$data_from[$i]->id.'"';
				}else{
					$filter_from .= ',"'.$data_from[$i]->id.'"';
				}
			}
		}else{
			$filter_from = '"'.$filter_from.'"';
		}
		
		$filter_box = $this->input->post('filter_box');
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
		
		$from_stock_in = $this->input->post('from_stock_in');
		$to_stock_in = $this->input->post('to_stock_in');
		
		$stockFromTime = $this->date_to_format($from_stock_in);
		$stockToTime = $this->date_to_format($to_stock_in);
		
		$from_stock_in = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_stock_in = date("Y-m-d",$stockToTime).' 23:59:59';
		
		$data_stock_in = $this->mp->get_filter_stock_in($from_stock_in,$to_stock_in,$filter_category,$filter_from,$filter_box,$filter_karat);
		
		$data['view'] = '<table id="filter_data_tabel" class="ui celled table" style="width:100%"><thead><tr><th style="width:40px">No</th><th>ID Stock In</th><th>Nama Barang</th><th>Kelompok</th><th>Asal</th><th>Krt</th><th>Box</th><th>Berat</th><th>Tgl Masuk</th></tr></thead><tbody>';
		
		$number = 1;
		$total_pcs = 0;
		$total_gram = 0;
		foreach($data_stock_in as $d){
			$tanggal_stock_in = strtotime($d->trans_date);
			$tanggal_stock_in = date('d-M-y',$tanggal_stock_in);
			
			$box_number = '';
			$totalnumberlength = 3;
			$numberlength = strlen($d->id_box);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$box_number .= '0';
				}
			}
			
			$box_number .= $d->id_box;
			
			$data['view'] .= '<tr><td class="text-center">'.$number.'</td><td>'.$d->id.'</td><td>'.$d->product_name.'</td><td>'.$d->category_name.'</td><td>'.$d->from_name.'</td><td>'.$d->karat_name.'</td><td class="text-center">'.$box_number.'</td><td class="text-right">'.number_format($d->product_weight, 3).'</td><td>'.$tanggal_stock_in.'</td></tr>';
			
			$total_pcs = $total_pcs + 1;
			$total_gram = $total_gram + $d->product_weight;
			$number = $number + 1;
		}
		
		$data['total_pcs'] = $total_pcs;
		$data['total_gram'] = number_format($total_gram, 3);
		$data['view'] .= '</tbody></table>';
		
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
}
