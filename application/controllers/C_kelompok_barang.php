<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_kelompok_barang extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->model('M_kelompok_barang','mk');
	}
	
	public function index(){
		$this->load->view('master/V_kelompok_barang');
	}
	
	public function get_all_category(){
		$this->db->trans_start();
		
		$data_category = $this->mk->get_all_product_category();
		
		$data['view'] = '<table id="filter_data_tabel" class="ui celled table" style="width:100%"><thead><tr><th style="width:30px">No</th><th>Kelompok Barang</th><th style="width:80px">Action</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_category as $d){
			$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.$d->category_name.'</td><td style="padding: 0;text-align: center;"><button class="ui tiny icon google plus button" onclick=editForm("'.$d->id.'") title="Edit"><i class="edit icon"></i></button>';
			
			if($d->status == 'A'){
				$data['view'] .= '<button class="ui tiny icon positive button" onclick=changeStatus("'.$d->id.'","NA") title="Aktif"><i class="adjust icon"></i></button>';
			}else{
				$data['view'] .= '<button class="ui tiny icon negative button" onclick=changeStatus("'.$d->id.'","A") title="Tidak Aktif"><i class="adjust icon"></i></button>';
			}
			
			$data['view'] .= '</td></tr>';
			
			$number = $number + 1;
		}
		
		$data['view'] .= '</tbody></table>';
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function get_category_form(){
		$data['view'] = '<i class="close icon"></i><div class="header">Tambah Kelompok Barang</div><div class="content"><div class="ui error message" id="error_modal" style="display:none"></div>';
		
		$data['view'] .= '<form class="ui form form-javascript" id="form_add" action="'.base_url().'index.php/C_kelompok_barang/save_update" method="post"><div class="field"><input type="text" id="kelompok_barang" name="kelompok_barang" placeholder="Masukkan Nama Kelompok Barang" style="text-transform:uppercase" autocomplete="off" onkeyup=entToNextID("btn_save")></div></form></div><div class="actions"><button id="btn_save" class="ui green labeled icon button" onclick=saveForm()>Simpan	<i class="save icon"></i></button></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function get_category_data($id){
		$data_category = $this->mk->get_category_by_id($id);
		
		$data['nama_kelompok'] = $data_category[0]->category_name;
		
		$data['view'] = '<i class="close icon"></i><div class="header">Edit Nama Barang</div><div class="content"><div class="ui error message" id="error_modal" style="display:none"></div>';
		
		$data['view'] .= '<form class="ui form form-javascript" id="form_edit" action="'.base_url().'index.php/C_kelompok_barang/save_update" method="post"><div class="field"><input type="hidden" name="id" value="'.$data_category[0]->id.'"><input type="text" id="kelompok_barang" name="kelompok_barang" placeholder="Masukkan Kelompok Barang" style="text-transform:uppercase" autocomplete="off" onkeyup=entToNextID("btn_save") value="'.$data_category[0]->category_name.'"></div><div class="field"></form></div></div><div class="actions"><button id="btn_save" class="ui green labeled icon button" onclick=saveEditForm()>Update	<i class="magic icon"></i></button></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function save_update($flag){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		
		$this->validate($flag);
		
		$data['inputerror'] = array();
		$data['success'] = TRUE;
		
		$kelompok_barang = $this->input->post('kelompok_barang');
		$kelompok_barang = strtoupper($kelompok_barang);
		
		$created_date = date("Y-m-d").' 00:00:00';
		$created_by = $this->session->userdata('gold_nama_user');
		
		if($flag == 'input'){
			$this->mk->insert_category_product($kelompok_barang);
		}else if($flag == 'edit'){
			$id = $this->input->post('id');
			$this->mk->update_category_product($id, $kelompok_barang);
		}
		
		if($flag == 'input'){
			$pesan = 'Input';
		}else{
			$pesan = 'Update';
		}
		
		$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>'.$pesan.' Berhasil!</div></div>';
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	private function validate($flag){
		$data = array();
		$data['inputerror'] = '<ul class="list"></ul>';
		$data['success'] = TRUE;
		
		$kelompok_barang = $this->input->post('kelompok_barang');
		$kelompok_barang = strtoupper($kelompok_barang);
		
		if($kelompok_barang == ''){
			$data['inputerror'] .= '<li>Nama Kelompok Barang Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($flag == 'input'){
			$id_barang = $this->mk->get_category_id_by_name($kelompok_barang);
			if($id_barang != 0){
				$data['inputerror'] .= '<li>Nama Kelompok Barang Sudah Ada!</li>';
				$data['success'] = FALSE;
			}
		}else if($flag == 'edit'){
			$id = $this->input->post('id');
			$id_barang = $this->mk->get_category_id_by_name($kelompok_barang);
			if($id_barang != 0 && $id_barang != $id){
				$data['inputerror'] .= '<li>Nama Kelompok Barang Sudah Ada!</li>';
				$data['success'] = FALSE;
			}
		}
		
		if($data['success'] == FALSE){
			echo json_encode($data);
			exit();
		}
	}
	
	public function change_status($id_category = 0,$status_category = 0){
		$this->db->trans_start();
		
		$this->mk->change_category_status($id_category,$status_category);
		
		$this->db->trans_complete();
		
		$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Update Berhasil!</div></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
}
