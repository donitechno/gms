<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_stock_out extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->model('M_login','ml');
		$this->load->model('M_karat','mk');
		$this->load->model('M_box','mb');
		$this->load->model('M_kelompok_barang','mc');
		$this->load->model('M_product','mp');
		$this->load->model('M_nama_barang','mmp');
		$this->load->model('M_mutasi','mm');
	}
	
	public function index(){
		$data['karat'] = $this->mk->get_karat_srt();
		$data['box'] = $this->mb->get_box_aktif();
		$data['category'] = $this->mc->get_product_category();
		$data['from'] = $this->mp->get_product_from();
		$this->load->view('persediaan/V_stock_out',$data);
	}
	
	public function get_product_from($stock_out_date,$product_id){
		$stock_out_date = str_replace('%20',' ',$stock_out_date);
		$stockOutDate = $this->date_to_format($stock_out_date);
		$stock_out_date = date("Y-m-d",$stockOutDate).' 23:59:59';
		
		$product_id = str_replace('_','',$product_id);
		
		$cek_old = substr($product_id, 0, 4);
		$cek_old = strtoupper($cek_old);
		
		if($cek_old == 'LAMA'){
			$cek_val = strtoupper($product_id);
			$cek_lama = str_replace('LAMA','',$cek_val);
			$data_stock_out = $this->mp->get_product_date_before_old($stock_out_date,$cek_lama);
		}else{
			$data_stock_out = $this->mp->get_product_date_before($stock_out_date,$product_id);
		}
		
		if(count($data_stock_out) == 1){
			$data['found'] = 'single';
			
			$data['id'] = $data_stock_out[0]->id;
			
			$this->validate_pindah_after($stock_out_date,$data_stock_out[0]->id);
			
			$data['kelompok_barang'] = $data_stock_out[0]->category_name;
			$data['nama_barang'] = $data_stock_out[0]->product_name;
			$data['berat_barang'] = $data_stock_out[0]->product_weight;
			$data['karat_barang'] = $data_stock_out[0]->karat_name;
			$data['asal_barang'] = $data_stock_out[0]->from_name;
			
			$box_number = '';
			$totalnumberlength = 3;
			$numberlength = strlen($data_stock_out[0]->id_box);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$box_number .= '0';
				}
			}
			
			$box_number .= $data_stock_out[0]->id_box;
			
			$data['box_barang'] = $box_number;
			$data['asal_barang'] = $data_stock_out[0]->from_name;
			$data['ket_asal_barang'] = $data_stock_out[0]->product_from_desc;
		}else{
			$data['found'] = 'not_single';
			
			$data['view'] = '<i class="close icon"></i><div class="header">List Barang</div><div class="content"><table class="ui celled table" id="modal-table" style="width:100%"><thead class="center aligned"><tr><th>No</th><th>ID</th><th>ID Lama</th><th>Nama Barang</th><th>Karat</th><th>Berat</th><th>Act</th></tr></thead><tbody>';
			
			$number = 1;
			foreach($data_stock_out as $d){
				$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$d->id.'</td><td>'.$d->id_lama.'</td><td>'.$d->product_name.'</td><td>'.$d->karat_name.'</td><td class="right aligned">'.number_format($d->product_weight, 2).'</td><td class="center aligned"><button type="button" class="ui linkedin pilih button" onclick=setProduct("'.$d->id.'")><i class="hand point up outline icon"></i> Pilih</button></td>';
				
				$number = $number + 1;
			}
			
			$data['view'] .= '</tbody></table></div>';
		}
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function save_baris($id_row){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		$this->validate($id_row);
		
		$stock_out_date = $this->input->post('tanggal_stock_out');
		$tanggal_stock_out = $this->input->post('tanggal_stock_out');
		$stock_out_date = str_replace('%20',' ',$stock_out_date);
		$stockOutDate = $this->date_to_format($stock_out_date);
		$stock_out_date = date("Y-m-d",$stockOutDate).' 23:59:59';
		$tgl_stock_out = date("Y-m-d",$stockOutDate).' 00:00:00';
		
		$so_reason = $this->input->post('alasan_stock_out');
		$product_id = $this->input->post('id_'.$id_row);
		
		$this->validate_pindah_after($stock_out_date,$product_id);
		
		$data_stock_out = $this->mp->get_product_date_before($stock_out_date,$product_id);
		
		$id_karat = $data_stock_out[0]->id_karat;
		$id_box = $data_stock_out[0]->id_box;
		$berat_barang = $data_stock_out[0]->product_weight;
		
		$stock_out_id = 'SO-'.$product_id;
		$created_by = $this->session->userdata('gold_nama_user');
		
		$this->mp->insert_stock_out($stock_out_id,$product_id,$id_karat,$id_box,$so_reason,$tgl_stock_out,$created_by);
		$this->mp->update_product_stock_out($product_id,$tgl_stock_out);
		
		$sitecode = $this->mm->get_site_code();
		$account_srt = $this->mm->get_default_account('SRT');
		$account_pjg = $this->mm->get_default_account('PJG');
		
		$desc = 'STOCK OUT - NO.REG : '.$product_id;
		
		$this->mm->insert_mutasi_gram($sitecode,$stock_out_id,'Out',$id_karat,$account_pjg,$account_srt,$berat_barang,$desc,$tgl_stock_out,$created_by);
		
		$id_row = $id_row + 1;
		
		$data['tanggal_stock_out'] = '<input type="text" name="tanggal_stock_out" id="tanggal_stock_out" value="'.$tanggal_stock_out.'" readonly onkeydown=entToHeader("input_1_1")>';
		
		$data['button_messsage'] = '<div class="ui tiny icon green button"><i class="check circle icon"></i></div>';
		
		$data['view'] = '<tr id="pos_tr_'.$id_row.'"><td class="center aligned">'.$id_row.'</td><td><input class="form-pos" type="text" name="id_'.$id_row.'" id="input_'.$id_row.'_1" onkeydown=entToTab("'.$id_row.'","1") onblur=getProductForm("'.$id_row.'") autocomplete="off" placeholder="Masukkan ID Barang"></td><td><input class="form-pos" type="text" name="asal_'.$id_row.'" id="input_'.$id_row.'_2" onkeydown=entToTab("'.$id_row.'","2") readonly></td><td><input class="form-pos" type="text" name="category_'.$id_row.'" id="input_'.$id_row.'_3" onkeydown=entToTab("'.$id_row.'","3") readonly></td><td><input class="form-pos" type="text" name="nama_barang_'.$id_row.'" id="input_'.$id_row.'_4" onkeydown=entToTab("'.$id_row.'","4") readonly></td><td><input class="form-pos" type="text" name="karat_'.$id_row.'" id="input_'.$id_row.'_5" onkeydown=entToTab("'.$id_row.'","5") readonly></td><td><input class="center aligned form-pos" type="text" name="box_'.$id_row.'" id="input_'.$id_row.'_6" onkeydown=entToTab("'.$id_row.'","6") readonly></td><td><input class="form-pos right aligned" type="text" name="berat_'.$id_row.'" id="input_'.$id_row.'_7" onkeydown=entToTab("'.$id_row.'","7") readonly></td><td class="center aligned" id="input_'.$id_row.'_8"><div class="ui tiny icon google plus button"><i class="ban icon"></i></div></td></tr>';
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	private function validate($id_row){
		$data = array();
		$data['inputerror'] = '<ul class="list"></ul>';
		$data['success'] = TRUE;
		
		$stock_out_date = $this->input->post('tanggal_stock_out');
		if($stock_out_date == ''){
			$data['inputerror'] .= '<li>Tanggal Stock Out Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		$stock_out_date = str_replace('%20',' ',$stock_out_date);
		$stockOutDate = $this->date_to_format($stock_out_date);
		$stock_out_date = date("Y-m-d",$stockOutDate).' 23:59:59';
		
		$so_reason = $this->input->post('alasan_stock_out');
		if($so_reason == ''){
			$data['inputerror'] .= '<li>Alasan Stock Out Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		$product_id = $this->input->post('id_'.$id_row);
		$data_stock_out = $this->mp->get_product_date_before($stock_out_date,$product_id);
		
		if($product_id == ''){
			$data['inputerror'] .= '<li>ID Barang Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if(count($data_stock_out) == 0){
			$data['inputerror'] .= '<li>ID Barang Tidak Ditemukan!</li>';
			$data['success'] = FALSE;
		}
		
		if(count($data_stock_out) > 1){
			$data['inputerror'] .= '<li>ID Barang Salah!</li>';
			$data['success'] = FALSE;
		}
		
		if($data['success'] == FALSE){
			$data['val_filter'] = 'N';
			echo json_encode($data);
			exit();
		}
	}
	
	public function filter_stock_out(){
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
			$data_from = $this->mp->get_product_from();
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
		
		$from_stock_out = $this->input->post('from_stock_out');
		$to_stock_out = $this->input->post('to_stock_out');
		
		$stockFromTime = $this->date_to_format($from_stock_out);
		$stockToTime = $this->date_to_format($to_stock_out);
		
		$from_stock_out = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_stock_out = date("Y-m-d",$stockToTime).' 23:59:59';
		
		$data_stock_out = $this->mp->get_filter_stock_out($from_stock_out,$to_stock_out,$filter_category,$filter_from,$filter_box,$filter_karat);
		
		$data['view'] = '<table id="filter_data_tabel" class="ui celled table" style="width:100%"><thead><tr><th style="width:50px">No</th><th>ID Stock Out</th><th>Nama Barang</th><th>Karat</th><th>Box</th><th>Berat</th><th>Tgl Keluar</th><th>Alasan</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_stock_out as $d){
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
			
			$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$d->id.'</td><td>'.$d->product_name.'</td><td>'.$d->karat_name.'</td><td class="center aligned">'.$box_number.'</td><td class="right aligned">'.number_format($d->product_weight, 2).'</td><td>'.$tanggal_stock_in.'</td><td>'.$d->so_reason.'</td></tr>';
			
			$number = $number + 1;
		}
		
		$data['view'] .= '</tbody></table>';
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	private function validate_pindah_after($pindah_box_date,$product_id){
		$cek_pindah_after = $this->mp->get_pindah_box_after($pindah_box_date,$product_id);
		if(count($cek_pindah_after) > 0){
			$data['view'] = '<i class="close icon"></i><div class="header">Warning</div><div class="content">';
			
			$data['view'] .= '<div class="ui icon header center aligned"><i class="exclamation triangle red icon"></i>Barang Tidak Dapat di Stock Out</div><div class="ui negative message" style="text-align:center"><div class="header">Andah Sudah Pernah Melakukan Transaksi Pindah Box Melewati Tanggal Yang Anda Pilih. Pilih Tanggal Lain atau Hubungi Tim IT Untuk Penanganan Lebih Lanjut</div></div>';
			
			$data['view'] .= '<table class="ui celled table" id="modal-table" style="width:100%;"><thead><tr><th>No</th><th>Tgl Pindah Box</th><th>ID</th><th>Dari Box</th><th>Ke Box</th></tr></thead><tbody>';
			
			$number = 1;
			foreach($cek_pindah_after as $c){
				$tanggal_pindah_box = strtotime($c->trans_date);
				$tanggal_pindah_box = date('d-M-y',$tanggal_pindah_box);
				
				$box_number_from = '';
				$totalnumberlength = 3;
				$numberlength = strlen($c->id_box_from);
				$numberspace = $totalnumberlength - $numberlength;
				if($numberspace != 0){
					for ($i = 1; $i <= $numberspace; $i++){
						$box_number_from .= '0';
					}
				}
				
				$box_number_from .= $c->id_box_from;
				
				$box_number_to = '';
				$totalnumberlength = 3;
				$numberlength = strlen($c->id_box_to);
				$numberspace = $totalnumberlength - $numberlength;
				if($numberspace != 0){
					for ($i = 1; $i <= $numberspace; $i++){
						$box_number_to .= '0';
					}
				}
				
				$box_number_to .= $c->id_box_to;
				
				$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$tanggal_pindah_box.'</td><td>'.$c->id.'</td><td>'.$box_number_from.'</td><td>'.$box_number_to.'</td>';
				
				$number = $number + 1;
			}
			
			$data['view'] .= '</tbody></table></div></div>';
			
			$data['val_filter'] = 'Y';
			$data['success'] = FALSE;
			echo json_encode($data);
			exit();
		}
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
