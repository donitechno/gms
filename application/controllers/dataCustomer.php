<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DataCustomer extends CI_Controller {	
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->model('M_login','ml');
		$this->load->model('M_karat','mk');
		$this->load->model('M_kelompok_barang','mc');
		$this->load->model('M_product','mp');
		$this->load->model('M_pos','mt');
		$this->load->model('M_mutasi','mm');
	}
	
	public function index(){
		$data['view'] = '<div class="ui fluid container no-print">
		<form class="ui form form-javascript" id="dataCustomer-form-filter" action="'.base_url().'index.php/dataCustomer/filter" method="post">
		<div class="ui grid">
			<div class="ui inverted dimmer" id="dataCustomer-loaderlist">
				<div class="ui large text loader">Loading</div>
			</div>
			<div class="ten wide centered column no-print" style="margin-top:15px">
				<div class="fields">
					<div class="six wide field">
						<label>Kata Kunci</label>
						<input type="text" name="dataCustomer-keyword" id="dataCustomer-keyword" autocomplete=off>
					</div>
					<div class="six wide field">
						<label>Cari Berdasarkan</label>
						<select name="dataCustomer-filter_based" id="dataCustomer-filter_based">
							<option value="P">Nomor Telepon</option>
							<option value="A">Alamat Customer</option>
							<option value="N">Nama Customer</option>
						</select>
					</div>
					<div class="four wide field">
						<label style="visibility:hidden">-</label>
						<div class="ui fluid icon green button filter-input" id="dataCustomer-btnfilter" onclick=filterTransaksi("dataCustomer") title="Filter">
							<i class="filter icon"></i> Filter
						</div>
					</div>
				</div>
			</div>
			<div class="fifteen wide centered column" id="dataCustomer-wrap_filter">
			</div>
		</div>
		</form>';
		
		$data["date"] = 0;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function index2(){
		$data['view'] = '<div class="ui grid"><div class="fifteen wide centered column"><div class="ui grid"><div class="ui inverted dimmer" id="dataCustomer-loader"><div class="ui loader"></div></div><div class="ten wide centered column" id="dataCustomer-wrap">';
		
		$data_customer = $this->mt->get_all_customer();
		
		$data['view'] .= '<table id="dataCustomer-table" class="ui celled table" style="width:100%"><thead><tr><th style="width:40px">No</th><th>No Telepon</th><th>Nama Customer</th><th>Alamat Customer</th><th style="width:80px">Action</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_customer as $d){
			$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.$d->cust_phone.'</td><td>'.$d->cust_name.'</td><td>'.$d->cust_address.'</td><td style="padding: 0;text-align: center;"><button class="ui tiny icon positive button" onclick=viewDetail("dataCustomer","'.$d->cust_phone.'")><i class="eye icon"></i></button>';
			
			$data['view'] .= '</td></tr>';
			
			$number = $number + 1;
		}
		
		$data['view'] .= '</tbody></table>';
		
		$data['view'] .= '</div></div></div></div></div>';
		
		$data["date"] = 0;
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function filter(){
		$this->db->trans_start();
		
		$keyword = $this->input->post('dataCustomer-keyword');
		$filter_by = $this->input->post('dataCustomer-filter_based');
		
		if($filter_by == 'P'){
			$column = 'cust_phone';
		}else if($filter_by == 'A'){
			$column = 'cust_address';
		}else if($filter_by == 'N'){
			$column = 'cust_name';
		}
		
		$data_customer = $this->mt->get_customer_filter($keyword,$column);
		
		$data['view'] = '<table id="dataCustomer-tablefilter" class="ui celled table" style="width:100%"><thead><tr><th style="width:40px">No</th><th>No Telepon</th><th>Nama Customer</th><th>Alamat Customer</th><th style="width:80px">Action</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_customer as $d){
			$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.$d->cust_phone.'</td><td>'.$d->cust_name.'</td><td>'.$d->cust_address.'</td><td style="padding: 0;text-align: center;"><div class="ui tiny icon positive button" onclick=viewDetail("dataCustomer","'.$d->cust_phone.'")><i class="eye icon"></i></div>';
			
			$data['view'] .= '</td></tr>';
			
			$number = $number + 1;
		}
		
		$data['view'] .= '</tbody></table>';
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function get_all_customer(){
		$this->db->trans_start();
		
		$data_customer = $this->mt->get_all_customer();
		
		$data['view'] = '<table id="filter_data_tabel" class="ui celled table" style="width:100%"><thead><tr><th style="width:40px">No</th><th>No Telepon</th><th>Nama Customer</th><th>Alamat Customer</th><th style="width:80px">Action</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_customer as $d){
			$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.$d->cust_phone.'</td><td>'.$d->cust_name.'</td><td>'.$d->cust_address.'</td><td style="padding: 0;text-align: center;"><button class="ui tiny icon positive button" onclick=viewDetail("'.$d->cust_phone.'")><i class="eye icon"></i></button>';
			
			$data['view'] .= '</td></tr>';
			
			$number = $number + 1;
		}
		
		$data['view'] .= '</tbody></table>';
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function view($phone){
		$phone = str_replace('plus','+',$phone);
		$data_trans = $this->mt->get_main_penjualan_by_phone($phone);
		
		$data['view'] = '<i class="close icon"></i><div class="header">List Transaksi '.$phone.'</div><div class="content">';
		
		$data['view'] .= '<div class="ui pointing secondary menu"><a class="item active" data-tab="first" style="width:50%"><i class="list ol icon"></i> Penjualan</a><a class="item" data-tab="second" style="width:50%"><i class="list ol icon"></i> Pembelian</a></div>';
		
		$data['view'] .= '<div class="ui bottom attached tab segment active" data-tab="first">';
		
		$data['view'] .= '<table class="ui celled table" id="dataCustomer-jualtable" style="width:100%"><thead class="center aligned"><tr><th>Data Penjualan Customer '.$phone.'</th></tr></thead><tbody>';
		
		foreach($data_trans as $d){
			$tanggal_tulis = strtotime($d->trans_date);
			$tanggal_tulis = date('d-M-y',$tanggal_tulis);
			
			$data['view'] .= '<tr><td><table class="ui celled table" style="border:none">';
			$data['view'] .= '<tr style="display:none"><td>'.$d->trans_date.'</td></tr>';
			$data['view'] .= '<tr><td style="padding: 0px 5px;border:none;">Tanggal</td><td colspan="2" style="padding: 0px 5px;border:none;"> : '.$tanggal_tulis.'</td><td class="right aligned" style="padding: 0px 5px;border:none;">Kasir : </td><td colspan="2" style="padding: 0px 5px;border:none;">'.strtoupper($d->created_by).'</td></tr><tr><td style="padding: 0px 5px;border:none;">ID Penjualan</td><td colspan="2" style="padding: 0px 5px;border:none;"> : '.$d->transaction_code.'</td><td class="right aligned" style="padding: 0px 5px;border:none;">CS : </td><td style="padding: 0px 5px;border:none;">'.strtoupper($d->cust_service).'</td></tr>';
			
			$data['view'] .= '<tr class="td-head-cust"><td class="td-head-cust">ID Barang</td><td class="td-head-cust">Nama Barang</td><td class="td-head-cust">Box</td><td class="td-head-cust">Karat</td><td class="td-head-cust">Berat</td><td class="td-head-cust">Harga</td></tr>';
			
			$data_product = $this->mt->get_product_jual($d->transaction_code);
			$number = 0;
			$total = 0;
			foreach($data_product as $dp){
				$number = $number + 1;
				$box_number = '';
				$totalnumberlength = 3;
				$numberlength = strlen($dp->id_box);
				$numberspace = $totalnumberlength - $numberlength;
				if($numberspace != 0){
					for ($i = 1; $i <= $numberspace; $i++){
						$box_number .= '0';
					}
				}
				
				$box_number .= $dp->id_box;
				$id_lengkap = explode('-', $dp->id_product);
				$id_tulis = $id_lengkap[1];
				
				$data['view'] .= '<tr><td class="center aligned" style="padding: 5px 5px;border:none;">'.$id_tulis.'</td><td style="padding: 5px 5px;border:none;">'.$dp->nama_product.'</td><td class="center aligned" style="padding: 5px 5px;border:none;">'.$box_number.'</td><td class="center aligned" style="padding: 5px 5px;border:none;">'.$dp->karat_name.'</td><td class="right aligned" style="padding: 5px 5px;border:none;">'.number_format($dp->product_weight,3,".",",").'</td><td class="right aligned" style="padding: 5px 5px;border:none;">'.number_format($dp->product_price,0,".",",").'</td></tr>';
				
				$total = $total + $dp->product_price;
			}
			
			$data['view'] .= '<tr><td class="td-head-cust" colspan="5" style="text-align:right !important">Total</td><td class="td-head-cust" style="text-align:right !important">'.number_format($total,0,".",",").'</td></tr>';
			
			$data['view'] .= '</table></td></tr>';
		}
		
		$data['view'] .= '</tbody></table></div>';
		
		$data_trans = $this->mt->get_main_pembelian_by_phone($phone);
		
		$data['view'] .= '<div class="ui bottom attached tab segment" data-tab="second">';
		
		$data['view'] .= '<table class="ui celled table" id="dataCustomer-belitable" style="width:100%"><thead class="center aligned"><tr><th>Data Pembelian Customer '.$phone.'</th></tr></thead><tbody>';
		
		foreach($data_trans as $d){
			$tanggal_tulis = strtotime($d->trans_date);
			$tanggal_tulis = date('d-M-y',$tanggal_tulis);
			
			$data['view'] .= '<tr><td><table class="ui celled table" style="border:none">';
			$data['view'] .= '<tr><td style="padding: 0px 5px;border:none;">Tanggal</td><td colspan="2" style="padding: 0px 5px;border:none;"> : '.$tanggal_tulis.'</td><td class="right aligned" style="padding: 0px 5px;border:none;">Kasir : </td><td colspan="2" style="padding: 0px 5px;border:none;">'.strtoupper($d->created_by).'</td></tr><tr><td style="padding: 0px 5px;border:none;">ID Penjualan</td><td colspan="2" style="padding: 0px 5px;border:none;"> : '.$d->transaction_code.'</td><td class="right aligned" style="padding: 0px 5px;border:none;">CS : </td><td style="padding: 0px 5px;border:none;">'.strtoupper($d->cust_service).'</td></tr>';
			
			$data['view'] .= '<tr class="td-head-cust"><td class="td-head-cust">Kelompok Barang</td><td class="td-head-cust">Nama Barang</td><td class="td-head-cust">Pcs</td><td class="td-head-cust">Karat</td><td class="td-head-cust">Berat</td><td class="td-head-cust">Total Harga</td></tr>';
			
			$data_product = $this->mt->get_product_beli($d->transaction_code);
			$number = 0;
			$total = 0;
			foreach($data_product as $dp){
				$number = $number + 1;
				
				$data['view'] .= '<tr><td class="center aligned" style="padding: 5px 5px;border:none;">'.$dp->category_name.'</td><td style="padding: 5px 5px;border:none;">'.$dp->nama_product.'</td><td class="right aligned" style="padding: 5px 5px;border:none;">'.$dp->product_pcs.'</td><td class="center aligned" style="padding: 5px 5px;border:none;">'.$dp->karat_name.'</td><td class="right aligned" style="padding: 5px 5px;border:none;">'.number_format($dp->product_weight,3,".",",").'</td><td class="right aligned" style="padding: 5px 5px;border:none;">'.number_format($dp->product_price,0,".",",").'</td></tr>';
				
				$total = $total + $dp->product_price;
			}
			
			$data['view'] .= '<tr><td class="td-head-cust" colspan="5" style="text-align:right !important">Total</td><td class="td-head-cust" style="text-align:right !important">'.number_format($total,0,".",",").'</td></tr>';
			
			$data['view'] .= '</table></td></tr>';
		}
		
		$data['view'] .= '</tbody></table></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function test_array(){
		$test = array();
		$test[] = 'satu';
		$test[] = 'dua';
		
		echo json_encode($test);
	}
}
