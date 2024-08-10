<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");

class C_login extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('M_login','ml');
	}
	
	public function index(){
		if($this->session->userdata('gold_login') == TRUE){
			$this->load->view('V_menu');
		}else{
			$this->load->view('V_login');
		}
	}
	
	public function cek_login(){
		$username = $this->input->post('username');
		$username = strtolower($username);
		$password = $this->input->post('password');
		$kasir_number = 1;
		
		if($this->ml->cek_login($username,$password) == TRUE){
			$data['success'] = true;
			$data['lokasi'] = base_url();
			
			$userdata = $this->ml->get_userdata($username);
			foreach ($userdata as $ud) {
				$nama_user = $ud->nama_user;
				$kasir = $ud->priv_kasir;
				$manager = $ud->priv_manager;
				$pembukuan = $ud->priv_pembukuan;
				$admin = $ud->priv_admin;
				$pp = $ud->picture;
			}
			
			$newsession = array(
				'gold_username' => $username,
				'gold_nama_user' => $nama_user,
				'gold_kasir' => $kasir,
				'gold_manager' => $manager,
				'gold_pembukuan' => $pembukuan,
				'gold_admin' => $admin,
				'gold_pp' => $pp,
				'gold_login' => TRUE
			);
			$this->session->set_userdata($newsession);
			$this->session->sess_expiration = '32140800';
		}else{
			$data['success'] = false;
		}
		echo json_encode($data);
	}
	
	public function logout(){
		$this->session->unset_userdata('gold_username');
		$this->session->unset_userdata('gold_nama_user');
		$this->session->unset_userdata('gold_kasir');
		$this->session->unset_userdata('gold_manager');
		$this->session->unset_userdata('gold_pembukuan');
		$this->session->unset_userdata('gold_admin');
		$this->session->unset_userdata('gold_pp');
		$this->session->unset_userdata('gold_login');
		redirect('../', 'refresh');
	}
}
