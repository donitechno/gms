<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NamaBarang extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->model('M_kelompok_barang','mk');
		$this->load->model('M_nama_barang','mn');
	}
	
	public function index(){
		$category = $this->mk->get_product_category();
		$data['view'] = '<div class="ui container fluid"><div class="ui grid"><div class="fifteen wide centered column right aligned" style="padding-bottom:0px"><button class="ui linkedin button btn-head" onclick=addForm("namaBarang")><i class="add icon"></i> Tambah Data</button><button class="ui green button btn-head" onclick=importForm("namaBarang")>
						<i class="folder icon"></i> Import Dari Excel
					</button></div></div>
			<div class="ui grid">
				<div class="fifteen wide centered column">
					<form id="namaBarang-form-filter" action="'.base_url().'index.php/namaBarang/filter" method="post">
					<div class="ui grid">
						<div class="ten wide centered column">
							<div class="ui form">
								<div class="field">
									<select class="ui fluid dropdown" name="namaBarang-category" id="namaBarang-category" onchange=filterNamaBarang("namaBarang")>
										<option value="All">-- Lihat Seluruh Kelompok Barang --</option>';
										foreach($category as $c){
										$data['view'] .= '<option value="'.$c->id.'">'.$c->category_name.'</option>';
										}
									$data['view'] .= '</select>
								</div>
							</div>
						</div>
					</div>
					</form>
					<div class="ui grid">
						<div class="ui inverted dimmer" id="namaBarang-loader">
							<div class="ui loader"></div>
						</div>
						<div class="sixteen wide column" id="namaBarang-wrap"></div>
					</div>
				</div>
			</div>
		</div>';
		
		$data["date"] = 0;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function filter(){
		$this->db->trans_start();
		
		$filter_category = $this->input->post('namaBarang-category');
		if($filter_category == 'All'){
			$filter_category = '';
			$data_category = $this->mk->get_product_category();
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
		
		$data_nama_barang = $this->mn->get_filter_nama_barang($filter_category);
		
		$data['view'] = '<table id="namaBarang-table" class="ui celled table" style="width:100%"><thead><tr><th style="width:30px">No</th><th>Nama Barang</th><th style="width:120px">Kelompok Barang</th><th style="width:50px">Action</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_nama_barang as $d){
			$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.$d->nama_barang.'</td><td>'.$d->category_name.'</td><td style="padding: 0;text-align: center;"><button class="ui tiny icon google plus button" onclick=editForm("namaBarang","'.$d->id.'") title="Edit"><i class="edit icon"></i></button></td></tr>';
			
			$number = $number + 1;
		}
		
		$data['view'] .= '</tbody></table>';
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}

	public function add(){
		$category = $this->mk->get_product_category();
		
		$data['view'] = '<i class="close icon"></i><div class="header">Tambah Nama Barang</div><div class="content"><div class="ui error message" id="namaBarang-wraperror" style="display:none"></div>';
		
		$data['view'] .= '<form class="ui form form-javascript" id="namaBarang-addedit" action="'.base_url().'index.php/namaBarang/save_addedit" method="post"><div class="field"><input type="text" id="namaBarang-name" name="namaBarang-name" placeholder="Masukkan Nama Barang" style="text-transform:uppercase" autocomplete="off" onkeyup=entToNextID("namaBarang-select-category-selectized")></div><div class="field"><select id="namaBarang-select-category" name="namaBarang-select-category"><option value="">-- Pilih Kelompok Barang --</option>';
		
		foreach($category as $c){
			$data['view'] .= '<option value="'.$c->id.'">'.$c->category_name.'</option>';
		}
		
		$data['view'] .= '</select></div></form></div><div class="actions"><button id="namaBarang-btnadd" class="ui green labeled icon button" onclick=saveAddEdit("namaBarang","Input")>Simpan	<i class="save icon"></i></button></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function edit($id){
		$category = $this->mk->get_product_category();
		$data_product = $this->mn->get_barang_by_id($id);
		
		$data['view'] = '<i class="close icon"></i><div class="header">Edit Nama Barang</div><div class="content"><div class="ui error message" id="namaBarang-wraperror" style="display:none"></div>';
		
		$data['view'] .= '<form class="ui form form-javascript" id="namaBarang-addedit" action="'.base_url().'index.php/namaBarang/save_addedit" method="post"><div class="field"><input type="hidden" name="namaBarang-id" value="'.$data_product[0]->id.'"><input type="text" id="namaBarang-name" name="namaBarang-name" placeholder="Masukkan Nama Barang" style="text-transform:uppercase" autocomplete="off" onkeyup=entToNextID("namaBarang-select-category-selectized") value="'.$data_product[0]->nama_barang.'"></div><div class="field"><select id="namaBarang-select-category" name="namaBarang-select-category"><option value="">-- Pilih Kelompok Barang --</option>';
		
		foreach($category as $c){
			$selected = '';
			if($c->id == $data_product[0]->id_category){
				$selected = 'selected';
			}
			
			$data['view'] .= '<option value="'.$c->id.'" '.$selected.'>'.$c->category_name.'</option>';
		}
		
		$data['view'] .= '</select></div></form></div><div class="actions"><button id="namaBarang-btnadd" class="ui green labeled icon button" onclick=saveAddEdit("namaBarang","Update")>Update	<i class="magic icon"></i></button></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function save_addedit($flag = 0){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		
		$this->validate($flag);
		
		$nama_barang = $this->input->post('namaBarang-name');
		$nama_barang = strtoupper($nama_barang);
		$select_category = $this->input->post('namaBarang-select-category');
		$created_date = date("Y-m-d").' 00:00:00';
		$created_by = $this->session->userdata('gold_nama_user');
		
		if($flag == 'Input'){
			$this->mn->insert_master_product($nama_barang,$select_category,$created_date,$created_by);
		}else if($flag == 'Update'){
			$id = $this->input->post('namaBarang-id');
			$this->mn->update_master_product($id, $nama_barang, $select_category);
		}
		
		$this->db->trans_complete();
		
		if($flag == 'Input'){
			$pesan = 'Input';
		}else{
			$pesan = 'Update';
		}
		
		$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>'.$pesan.' Berhasil!</div></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	private function validate($flag){
		$data = array();
		$data['inputerror'] = '<ul class="list"></ul>';
		$data['success'] = TRUE;
		
		$nama_barang = $this->input->post('namaBarang-name');
		$nama_barang = strtoupper($nama_barang);
		
		$select_category = $this->input->post('namaBarang-select-category');
		
		if($nama_barang == ''){
			$data['inputerror'] .= '<li>Nama Barang Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($select_category == ''){
			$data['inputerror'] .= '<li>Kelompok Barang Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($flag == 'Input'){
			$data_nama_barang = $this->mn->cek_product_name($nama_barang,$select_category);
			if(count($data_nama_barang) > 0){
				$data['inputerror'] .= '<li>Nama Barang Sudah Ada!</li>';
				$data['success'] = FALSE;
			}
		}else{
			$id = $this->input->post('namaBarang-id');
			
			$data_nama_barang = $this->mn->cek_product_name($nama_barang,$select_category);
			if(count($data_nama_barang) > 0){
				$data_product = $this->mn->get_barang_by_id($id);
				$nama_exs = $data_product[0]->nama_barang;
				$category_exs = $data_product[0]->id_category;
				
				if($nama_barang == $nama_exs && $select_category == $category_exs){
					
				}else{
					$data['inputerror'] .= '<li>Nama Barang Sudah Ada!</li>';
					$data['success'] = FALSE;
				}
			}
		}
		
		if($data['success'] == FALSE){
			echo json_encode($data);
			exit();
		}
	}
	
	public function import(){
		$data['view'] = '<i class="close icon"></i><div class="header">Import Nama Barang</div><div class="content"><div class="ui error message" id="namaBarang-wraperror" style="display:none"></div>';
		
		$data['view'] .= '<form class="ui form form-javascript" id="namaBarang-formimport" action="'.base_url().'index.php/namaBarang/save_import" method="post" enctype="multipart/form-data"><div class="field"><input type="file" class="form-control" name="namaBarang-file" id="namaBarang-file"></div>';
		
		$data['view'] .= '<button type="submit" id="namaBarang-btnimport" class="fluid ui green labeled icon button"> Import	<i class="download icon"></i></button></div></form></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function save_import(){
		//$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		
		$this->db->trans_start();
		
		$fileName = $_FILES['namaBarang-file']['name'];
		$fileName = str_replace(' ','_',$fileName);
        $config['upload_path'] = './import';
        $config['file_name'] = $fileName;
        $config['allowed_types'] = 'xls|xlsx|csv';
        $config['max_size'] = 10000;
         
        $this->load->library('upload');
        $this->upload->initialize($config);
		
		$file_name = './import/'.$fileName;

		if (file_exists($file_name)) {
			unlink($file_name);
		} else {
			
		}
		
		if(!$this->upload->do_upload('namaBarang-file')){
			$data['img'] = base_url().'assets/images/error.png';
			$data['location'] = base_url();
			$data['color'] = "#e74c3c";
			$data['pesan'] = "Gagal Upload File!";
			$this->load->view("import/V_notif",$data);
		}else{

		}
		
		$inputFileName = './import/'.$fileName;
         
        try{
			$inputFileType = IOFactory::identify($inputFileName);
			$objReader = PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFileName);
		}catch(Exception $e){
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}
		
		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
		
		$created_date = date("Y-m-d").' 00:00:00';
		$created_by = $this->session->userdata('gold_nama_user');
		
		for($row = 1; $row <= $highestRow; $row++){
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
			$product_name = $rowData[0][2];
			$product_category = $rowData[0][4];
			$product_category = str_replace(' ','',$product_category);
			
			$id_category = $this->mk->get_category_id_by_name($product_category);
			if($id_category != 0){
				$data_product_name = $this->mn->cek_product_name($product_name,$id_category);
				if(count($data_product_name) == 0){
					$this->mn->insert_master_product($product_name,$id_category,$created_date,$created_by);
				}
			}
		}
		
		$this->db->trans_complete();
		
		unlink('./import/'.$fileName);
		$data['tipe'] = 'positive';
		$data['icon'] = 'thumbs up outline';
		$data['location'] = base_url();
		$data['pesan'] = "Berhasil Import Data!";
		$this->load->view("import/V_notif",$data);
	}
}
