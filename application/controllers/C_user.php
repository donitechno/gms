<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class C_user extends CI_Controller {
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
		
		$this->load->model('M_user','mk');
	}
	
	public function index(){
		$this->load->view('master/V_user');
	}
	
	/*-- MENAMPILKAN DATA KELOMPOK PRODUK --*/
	public function get_all_karyawan(){
		$this->db->trans_start();
		
		$data_karyawan = $this->mk->get_all_karyawan();
			
		$data['view'] = '<table id="filter_data_tabel" class="ui celled table" width="100%"><thead><tr><th style="width:30px">No</th><th>Username</th><th>Nama Karyawan</th><th>Kelompok</th><th style="width:80px">Action</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_karyawan as $d){
			$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.$d->username.'</td><td>'.$d->nama_karyawan.'</td><td>'.$d->kelompok.'</td><td><button class="ui tiny icon google plus button" onclick=editForm("'.$d->username.'") title="Edit"><i class="edit icon"></i></button>';
			
			if($d->status == 'A'){
				$data['view'] .= '<button class="ui tiny icon positive button" onclick=changeStatus("'.$d->username.'","NA") title="Aktif"><i class="power off icon"></i></button>';
			}else{
				$data['view'] .= '<button class="ui tiny icon negative button" onclick=changeStatus("'.$d->username.'","A") title="Tidak Aktif"><i class="power off icon"></i></button>';
			}
			
			$data['view'] .= '</td></tr>';

			$number = $number + 1;
		}
		
		$data['view'] .= '</tbody></table>';
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	/*-- MENGUBAH STATUS MENJADI AKTIF / NON AKTIF --*/
	public function change_status($id = 0,$status = 0){
		$this->db->trans_start();
		
		$this->mk->change_karyawan_status($id,$status);
		
		$data_karyawan = $this->mk->get_piutang_kary_account($id);
		$id_piutang = $data_karyawan[0]->accountnumber;
		$this->mk->update_piutang_kary_status($id_piutang,$status);
		
		$this->db->trans_complete();
		
		$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Update Berhasil!</div></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function get_input_form(){
		$data['view'] = '<i class="close icon"></i><div class="header">Tambah User</div><div class="content"><div class="ui error message" id="error_modal" style="display:none"></div>';
		
		$data['view'] .= '<form class="ui form form-javascript" id="form_add" action="'.base_url().'index.php/C_user/save_karyawan" method="post">';
		
		$data['view'] .= '<div class="field"><label>Username</label><input type="text" id="input_data_1" name="username" autocomplete="off" onkeyup=entToTab("2")></div>';
		
		$data['view'] .= '<div class="field"><label>Nama Karyawan</label><input type="text" id="input_data_2" name="nama_karyawan" autocomplete="off" onkeyup=entToTab("3")></div>';
		
		$data['view'] .= '<div class="field"><label>Kelompok</label><select id="input_data_3" name="kelompok_karyawan" onkeyup=entToTab("4")><option value="KARYAWAN">KARYAWAN</option><option value="MANAGER/WAKIL">MANAGER/WAKIL</option><option value="PEMBUKUAN">PEMBUKUAN</option><option value="SUPERADMIN">SUPERADMIN</option></select></div>';
		
		$data['view'] .= '<div class="field"><label>Password</label><input type="password" id="input_data_4" name="password" autocomplete="off" onkeyup=entToTab("5")></div>';
		
		
		$data['view'] .= '<div class="field"><label>Ulangi Password</label><input type="password" id="input_data_5" name="rep_password" autocomplete="off" onkeyup=entToTab("6")></div></form></div><div class="actions"><button id="btn_save" class="ui green labeled icon button" onclick=saveForm()>Simpan	<i class="save icon"></i></button></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function get_edit_form($username){
		$data_user = $this->mk->cek_username($username);
		
		$kasir = '';
		$manager = '';
		$pembukuan = '';
		$superadmin = '';
		
		if($data_user[0]->kelompok == 'KARYAwAN'){
			$kasir = 'selected';
		}else if($data_user[0]->kelompok == 'MANAGER/WAKIL'){
			$manager = 'selected';
		}else if($data_user[0]->kelompok == 'PEMBUKUAN'){
			$pembukuan = 'selected';
		}else if($data_user[0]->kelompok == 'SUPERADMIN'){
			$superadmin = 'selected';
		}
		
		$data['view'] = '<i class="close icon"></i><div class="header">Edit User</div><div class="content"><div class="ui error message" id="error_modal" style="display:none"></div>';
		
		$data['view'] .= '<form class="ui form form-javascript" id="form_edit" action="'.base_url().'index.php/C_user/edit_karyawan" method="post">';
		
		$data['view'] .= '<div class="field"><label>Username</label><input type="text" id="input_data_1" name="username" value="'.$data_user[0]->username.'" onkeyup=entToTab("2") readonly="readonly"></div>';
		
		$data['view'] .= '<div class="field"><label>Nama Karyawan</label><input type="text" id="input_data_2" name="nama_karyawan" value="'.$data_user[0]->nama_karyawan.'"autocomplete="off" onkeyup=entToTab("3")></div>';
		
		$data['view'] .= '<div class="field"><label>Kelompok</label><select id="input_data_3" name="kelompok_karyawan" onkeyup=entToTab("4")><option value="KARYAWAN" '.$kasir.'>KARYAWAN</option><option value="MANAGER/WAKIL" '.$manager.'>MANAGER/WAKIL</option><option value="PEMBUKUAN" '.$pembukuan.'>PEMBUKUAN</option><option value="SUPERADMIN" '.$superadmin.'>SUPERADMIN</option></select></div>';
		
		$data['view'] .= '<div class="field"><label>Password (Kosongkan Jika Tidak ingin Diubah)</label><input type="password" id="input_data_4" name="password" autocomplete="off" onkeyup=entToTab("5")></div>';
		
		
		$data['view'] .= '<div class="field"><label>Ulangi Password (Kosongkan Jika Tidak ingin Diubah)</label><input type="password" id="input_data_5" name="rep_password" autocomplete="off" onkeyup=entToTab("7")></div></form></div><div class="actions"><button id="btn_save" class="ui green labeled icon button" onclick=saveEdit()>Update	<i class="save icon"></i></button></div>';
		
		
		
		
		
		/*
		
		$data['view'] = '<div class="modal-dialog modal-sm" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">Input Data Karyawan</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body">';
		
		
		
		$data['view'] .= '<form id="form_transaction" action="'.base_url().'index.php/C_karyawan/edit_karyawan" method="post"><div class="row" style="padding-top:15px;"><div class="col-lg-12 col-md-12"><div class="col-lg-12 col-md-12 col-no-pad"><div class="form-group"><label for="input_data_1">Username</label><input type="text" class="form-control" name="username" id="input_data_1" onkeydown="entToTab(2)" autocomplete="off" value="'.$data_user[0]->username.'" readonly></div><div id="username_val" class="invalid-tooltip"></div></div><div class="col-lg-12 col-md-12 col-no-pad"><div class="form-group"><label for="input_data_2">Nama Karyawan</label><input type="text" class="form-control" name="nama_karyawan" id="input_data_2" onkeydown = "entToTab(3)" value="'.$data_user[0]->nama_karyawan.'"></div><div id="nama_karyawan_val" class="invalid-tooltip"></div></div><div class="col-lg-12 col-md-12 col-no-pad"><div class="form-group"><label for="input_data_3">Kelompok</label><select id="input_data_3" name="kelompok_karyawan" class="custom-select" onkeydown = "entToTab(4)"><option value="KARYAWAN" '.$kasir.'>KARYAWAN</option><option value="MANAGER/WAKIL" '.$manager.'>MANAGER/WAKIL</option><option value="PEMBUKUAN" '.$pembukuan.'>PEMBUKUAN</option><option value="SUPERADMIN" '.$superadmin.'>SUPERADMIN</option></select></div><div id="kelompok_karyawan_val" class="invalid-tooltip"></div></div><div class="col-lg-12 col-md-12 col-no-pad"><div class="form-group"><label for="input_data_4">Password (Kosongkan Jika Tidak ingin Merubah Password)</label><input type="password" class="form-control" name="password" id="input_data_4" onkeydown = "entToTab(5)"></div><div id="password_val" class="invalid-tooltip"></div></div><div class="col-lg-12 col-md-12 col-no-pad"><div class="form-group"><label for="input_data_5">Ulangi Password (Kosongkan Jika Tidak ingin Merubah Password)</label><input type="password" class="form-control" name="rep_password" id="input_data_5" onkeydown = "entToTab(6)"></div><div id="rep_password_val" class="invalid-tooltip"></div></div><div class="col-lg-12 col-md-12 col-no-pad"><button id="input_data_6" type="button" class="btn btn-success btn-full" onclick="saveEdit()"><i class="fa fa-send-o fa-fw"></i> Simpan</button><div id="exe_loading_modal" class="lgn-loading-div text-center" style="display:none"><i class="fa fa-refresh fa-pulse fa-2x fa-fw"></i><span class="sr-only">Loading...</span></div></div></div></div></div></form>';
		
		$data['view'] .= '<div class="modal-footer"><button type="button" id="mdl-close" class="btn btn-outline-dark" data-dismiss="modal">Close</button></div></div></div>';
		*/
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	/*-- SAVE/EDIT KELOMPOK PRODUCT --*/
	public function save_karyawan(){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		
		$this->validate();
		
		$username = $this->input->post('username');
		$username = strtolower($username);
		$nama_karyawan = $this->input->post('nama_karyawan');
		$kelompok_karyawan = $this->input->post('kelompok_karyawan');
		$password = $this->input->post('password');
		
		$created_date = date("Y-m-d").' 00:00:00';
		$created_by = $this->session->userdata('nama_user');
		
		$random_salt = $this->mk->random_salt();
		$encrypt_pass = $this->mk->encrypt_pass($password, $random_salt);
		
		$account_number_int = $this->mk->generate_account_karyawan();
		$acc1 = substr($account_number_int, 0, 2);
		$acc2 = substr($account_number_int, -4);
		$account_number = $acc1.'-'.$acc2;
		$account_group = 1;
		$beginning_balance = 0;
		$type = 'PIK';
		
		if($kelompok_karyawan == 'PEMBUKUAN' || $kelompok_karyawan == 'SUPERADMIN'){
			$status = 'NA';
		}else{
			$status = 'A';
		}
		
		$nama_account = 'PIUTANG KARYAWAN - '.strtoupper($nama_karyawan);
		$this->mk->insert_karyawan($username,$nama_karyawan,$kelompok_karyawan,$account_number,$random_salt,$encrypt_pass,$created_date,$created_by);
		$this->mk->insert_coa($account_number,$account_number_int,$nama_account,$account_group,$beginning_balance,$status,$type,$created_by);
		
		$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Input Berhasil!</div></div>';
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	/*-- VALIDASI NAMA PRODUCT --*/
	private function validate(){
		$data = array();
		$data['inputerror'] = '<ul class="list"></ul>';
		$data['success'] = TRUE;
		
		$username = $this->input->post('username');
		$username = strtolower($username);
		
		$nama_karyawan = $this->input->post('nama_karyawan');
		$kelompok_karyawan = $this->input->post('kelompok_karyawan');
		
		$password = $this->input->post('password');
		$rep_password = $this->input->post('rep_password');
		
		if($username == ''){
			$data['inputerror'] .= '<li>Username Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($nama_karyawan == ''){
			$data['inputerror'] .= '<li>Nama Karyawan Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($password == ''){
			$data['inputerror'] .= '<li>Password Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($rep_password == ''){
			$data['inputerror'] .= '<li>Repeat Password Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($password != '' && $rep_password != ''){
			if($password != $rep_password){
				$data['inputerror'] .= '<li>Password dan Repeat Password Tidak Sama!</li>';
				$data['success'] = FALSE;
			}
		}
		
		if($username != ''){
			$cek_username = $this->mk->cek_username($username);
			if(count($cek_username) > 0){
				$data['inputerror'] .= '<li>Username Sudah Dipakai, Harap Gunakan Username Lain!</li>';
				$data['success'] = FALSE;
			}
		}
		
		if($data['success'] == FALSE){
			echo json_encode($data);
			exit();
		}
	}
	
	/*-- SAVE/EDIT KELOMPOK PRODUCT --*/
	public function edit_karyawan(){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		
		$this->validate_edit();
		
		$username = $this->input->post('username');
		$username = strtolower($username);
		$nama_karyawan = $this->input->post('nama_karyawan');
		$kelompok_karyawan = $this->input->post('kelompok_karyawan');
		$password = $this->input->post('password');
		$rep_password = $this->input->post('rep_password');
		
		$this->mk->update_karyawan($username,$nama_karyawan,$kelompok_karyawan);
		$data_karyawan = $this->mk->get_piutang_kary_account($username);
		$id_piutang = $data_karyawan[0]->accountnumber;
		$account_name = 'PIUTANG KARYAWAN - '.strtoupper($nama_karyawan);
		
		if($kelompok_karyawan == 'PEMBUKUAN' || $kelompok_karyawan == 'SUPERADMIN'){
			$status = 'NA';
		}else{
			$status = 'A';
		}
		
		$this->mk->update_piutang_kary_status_2($id_piutang,$account_name,$status);
		
		if($password != '' && $rep_password != ''){
			$random_salt = $this->mk->random_salt();
			$encrypt_pass = $this->mk->encrypt_pass($password, $random_salt);
			$this->mk->update_karyawan_password($username,$random_salt,$encrypt_pass);
		}
		
		$this->db->trans_complete();
		
		$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Update Berhasil!</div></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	/*-- VALIDASI NAMA PRODUCT --*/
	private function validate_edit(){
		$data = array();
		$data['inputerror'] = '<ul class="list"></ul>';
		$data['success'] = TRUE;
		
		$nama_karyawan = $this->input->post('nama_karyawan');
		
		$password = $this->input->post('password');
		$rep_password = $this->input->post('rep_password');
		
		if($nama_karyawan == ''){
			$data['inputerror'] .= '<li>Nama Karyawan Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($password != '' && $rep_password != ''){
			if($password != $rep_password){
				$data['inputerror'] .= '<li>Password dan Repeat Password Tidak Sama!</li>';
				$data['success'] = FALSE;
			}
		}
		
		if($data['success'] == FALSE){
			echo json_encode($data);
			exit();
		}
	}
}
