<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KelompokBarang extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->model('M_kelompok_barang','mk');
	}
	
	public function index(){
		$data['view'] = '<div class="ui container fluid"><div class="ui grid"><div class="fifteen wide centered column right aligned" style="padding-bottom:0px"><button class="ui linkedin button btn-head" onclick=addForm("kelompokBarang")><i class="add icon"></i> Tambah Data</button></div></div><div class="ui grid"><div class="fifteen wide centered column"><div class="ui grid"><div class="sixteen wide column" id="kelompokBarang-wrap">';
		
		$data_category = $this->mk->get_all_product_category();
		
		$data['view'] .= '<table id="filter_data_tabel" class="ui celled table" style="width:100%"><thead><tr><th style="width:30px">No</th><th>Kelompok Barang</th><th style="width:80px">Action</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_category as $d){
			$data['view'] .= '<tr><td class="text-right">'.$number.'</td><td>'.$d->category_name.'</td><td style="padding: 0;text-align: center;"><button class="ui tiny icon google plus button" onclick=editForm("kelompokBarang","'.$d->id.'") title="Edit"><i class="edit icon"></i></button>';
			
			if($d->status == 'A'){
				$data['view'] .= '<button class="ui tiny icon positive button" onclick=changeStatus("kelompokBarang","'.$d->id.'","NA") title="Aktif"><i class="adjust icon"></i></button>';
			}else{
				$data['view'] .= '<button class="ui tiny icon negative button" onclick=changeStatus("kelompokBarang","'.$d->id.'","A") title="Tidak Aktif"><i class="adjust icon"></i></button>';
			}
			
			$data['view'] .= '</td></tr>';
			
			$number = $number + 1;
		}
		
		$data['view'] .= '</tbody></table>';
		
		$data['view'] .= '</div></div></div></div></div>';
		
		$data["date"] = 0;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function add(){
		$data['view'] = '<i class="close icon"></i><div class="header">Tambah Kelompok Barang</div><div class="content"><div class="ui error message" id="kelompokBarang-wraperror" style="display:none"></div>';
		
		$data['view'] .= '<form class="ui form form-javascript" id="kelompokBarang-addedit" action="'.base_url().'index.php/kelompokBarang/save_addedit" method="post"><div class="field"><input type="text" id="kelompokBarang-name" name="kelompokBarang-name" placeholder="Masukkan Nama Kelompok Barang" style="text-transform:uppercase" autocomplete="off" onkeyup=entToNextID("kelompokBarang-btnadd")></div></form></div><div class="actions"><button id="kelompokBarang-btnadd" class="ui green labeled icon button" onclick=saveAddEdit("kelompokBarang","Input")>Simpan	<i class="save icon"></i></button></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function edit($id){
		$data_category = $this->mk->get_category_by_id($id);
		
		$data['nama_kelompok'] = $data_category[0]->category_name;
		
		$data['view'] = '<i class="close icon"></i><div class="header">Edit Nama Barang</div><div class="content"><div class="ui error message" id="kelompokBarang-wraperror" style="display:none"></div>';
		
		$data['view'] .= '<form class="ui form form-javascript" id="kelompokBarang-addedit" action="'.base_url().'index.php/kelompokBarang/save_addedit" method="post"><div class="field"><input type="hidden" name="kelompokBarang-id" value="'.$data_category[0]->id.'"><input type="text" id="kelompokBarang-name" name="kelompokBarang-name" placeholder="Masukkan Kelompok Barang" style="text-transform:uppercase" autocomplete="off" onkeyup=entToNextID("kelompokBarang-btnadd") value="'.$data_category[0]->category_name.'"></div><div class="field"></form></div></div><div class="actions"><button id="kelompokBarang-btnadd" class="ui green labeled icon button" onclick=saveAddEdit("kelompokBarang","Update")>Update	<i class="magic icon"></i></button></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function save_addedit($flag){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		
		$this->validate($flag);
		
		$data['inputerror'] = array();
		$data['success'] = TRUE;
		
		$kelompok_barang = $this->input->post('kelompokBarang-name');
		$kelompok_barang = strtoupper($kelompok_barang);
		
		$created_date = date("Y-m-d").' 00:00:00';
		$created_by = $this->session->userdata('gold_nama_user');
		
		if($flag == 'Input'){
			$this->mk->insert_category_product($kelompok_barang);
		}else if($flag == 'Update'){
			$id = $this->input->post('kelompokBarang-id');
			$this->mk->update_category_product($id, $kelompok_barang);
		}
		
		if($flag == 'Input'){
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
		
		$kelompok_barang = $this->input->post('kelompokBarang-name');
		$kelompok_barang = strtoupper($kelompok_barang);
		
		if($kelompok_barang == ''){
			$data['inputerror'] .= '<li>Nama Kelompok Barang Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($flag == 'Input'){
			$id_barang = $this->mk->get_category_id_by_name($kelompok_barang);
			if($id_barang != 0){
				$data['inputerror'] .= '<li>Nama Kelompok Barang Sudah Ada!</li>';
				$data['success'] = FALSE;
			}
		}else if($flag == 'Update'){
			$id = $this->input->post('kelompokBarang-id');
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
