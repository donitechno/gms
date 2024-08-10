<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_setting_harga extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE || $this->session->userdata('gold_admin') != 'Y'){
			redirect();
		}
		
		$this->load->model('M_login','ml');
		$this->load->model('M_karat','mk');
	}
	
	public function index(){
		$data['setting'] = $this->mk->get_setting_harga();
		$this->load->view('master/V_setting_harga',$data);
	}
	
	public function save_setting_harga(){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		
		$setting = $this->mk->get_setting_harga();
		
		$data['success'] = TRUE;
		
		foreach($setting as $s){
			$from_weight = $this->input->post('from_'.$s->id);
			$from_weight = str_replace(',','',$from_weight);
			$from_weight = ceil($from_weight);
			
			$to_weight = $this->input->post('to_'.$s->id);
			$to_weight = str_replace(',','',$to_weight);
			$to_weight = ceil($to_weight);
			
			$min_percent = $this->input->post('min_'.$s->id);
			$min_percent = str_replace(',','',$min_percent);
			$min_percent = ceil($min_percent);
			
			$max_percent = $this->input->post('max_'.$s->id);
			$max_percent = str_replace(',','',$max_percent);
			$max_percent = ceil($max_percent);
			
			$min_percent_beli = $this->input->post('min_beli_'.$s->id);
			$min_percent_beli = str_replace(',','',$min_percent_beli);
			$min_percent_beli = ceil($min_percent_beli);
			
			$max_percent_beli = $this->input->post('max_beli_'.$s->id);
			$max_percent_beli = str_replace(',','',$max_percent_beli);
			$max_percent_beli = ceil($max_percent_beli);
			
			if($from_weight == 0 || $from_weight == 'NAN' || $to_weight == 0 || $to_weight == 'NAN' || $min_percent == 0 || $min_percent == '' || $min_percent == 'NAN' || $max_percent == 0 || $max_percent == '' || $max_percent == 'NAN' || $min_percent_beli == 0 || $min_percent_beli == '' || $min_percent_beli == 'NAN' || $max_percent_beli == 0 || $max_percent_beli == '' || $max_percent_beli == 'NAN'){
				$data['success'] = FALSE;
			}
		}
		
		if($data['success'] == FALSE){
			$data['lokasi'] = base_url().'index.php/C_setting_harga';
			echo json_encode($data);
			exit();
		}
		
		foreach($setting as $s){
			$id_setting = $s->id;
			
			$from_weight = $this->input->post('from_'.$s->id);
			$from_weight = str_replace(',','',$from_weight);
			
			$to_weight = $this->input->post('to_'.$s->id);
			$to_weight = str_replace(',','',$to_weight);
			
			$min_percent = $this->input->post('min_'.$s->id);
			$min_percent = str_replace(',','',$min_percent);
			
			$max_percent = $this->input->post('max_'.$s->id);
			$max_percent = str_replace(',','',$max_percent);
			
			$min_percent_beli = $this->input->post('min_beli_'.$s->id);
			$min_percent_beli = str_replace(',','',$min_percent_beli);
			
			$max_percent_beli = $this->input->post('max_beli_'.$s->id);
			$max_percent_beli = str_replace(',','',$max_percent_beli);
			
			$this->mk->update_setting_harga($id_setting, $from_weight, $to_weight, $min_percent, $max_percent, $min_percent_beli, $max_percent_beli);
		}
		
		$this->db->trans_complete();
		
		$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Update Berhasil!</div></div>';
		$data['lokasi'] = base_url().'index.php/C_setting_harga';
		$data['success'] = true;
		echo json_encode($data);
	}
	
	private function validate_nama_product($flag){
		$data = array();
		$data['inputerror'] = array();
		$data['success'] = TRUE;
		
		$nama_barang = $this->input->post('nama_barang');
		$nama_barang = strtoupper($nama_barang);
		
		if($flag == 'input'){
			$select_category = $this->input->post('select_category');
		}else if($flag == 'edit'){
			$select_category = $this->input->post('id_category');
		}
	
		if($nama_barang == ''){
			$data['inputerror'][] = 'nama_barang_val';
			$data['success'] = FALSE;
		}
		
		if($select_category == ''){
			$data['inputerror'][] = 'select_category_val';
			$data['success'] = FALSE;
		}
		
		if($flag == 'input'){
			$data_nama_barang = $this->mmp->cek_nama_barang($nama_barang);
			if(count($data_nama_barang) > 0){
				$data['inputerror'][] = 'nama_barang_val';
				$data['success'] = FALSE;
			}
		}
		
		if($data['success'] == FALSE){
			echo json_encode($data);
			exit();
		}
	}
	
	private function validate_edit_product(){
		$data = array();
		$data['inputerror'] = array();
		$data['success'] = TRUE;
		
		$nama_barang = $this->input->post('nama_barang');
		$nama_barang = strtoupper($nama_barang);
		$select_category = $this->input->post('id_category');
		$data_nama_barang = $this->mmp->cek_nama_barang($nama_barang);
		
		if($nama_barang == ''){
			$data['inputerror'][] = 'nama_barang_val';
			$data['success'] = FALSE;
		}
		
		if($select_category == ''){
			$data['inputerror'][] = 'select_category_val';
			$data['success'] = FALSE;
		}
		
		if(count($data_nama_barang) > 0){
			$data['inputerror'][] = 'nama_barang_val';
			$data['success'] = FALSE;
		}
		
		if($data['success'] == FALSE){
			echo json_encode($data);
			exit();
		}
	}
	
	public function filter_nama_product(){
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
		
		$data_nama_barang = $this->mmp->get_filter_nama_barang($filter_category);
		
		$data['view'] = '<table id="filter_data_tabel" class="table table-striped table-bordered table-responsive-md" cellspacing="0" width="99%" style="margin-left:2%"><thead><tr><th style="width:50px">No</th><th>Nama Barang</th><th>Kelompok Barang</th><th>Action</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_nama_barang as $d){
			$data['view'] .= '<tr><td class="text-center">'.$number.'</td><td>'.$d->nama_barang.'</td><td>'.$d->category_name.'</td><td><button type="button" class="btn btn-info btn-sm" onclick=editTrans("'.$d->id.'") title="Edit"><i class="fa fa-magic fa-fw"></i></button></td></tr>';
			
			$number = $number + 1;
		}
		
		$data['view'] .= '</tbody></table>';
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function get_product_data($id){
		$data_product = $this->mmp->get_barang_by_id($id);
		
		$data['nama_barang'] = $data_product[0]->nama_barang;
		$data['id_category'] = $data_product[0]->id_category;
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	/* Ubah Format Tanggal */
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
