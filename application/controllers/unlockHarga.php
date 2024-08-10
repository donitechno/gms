<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UnlockHarga extends CI_Controller {
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
		$data['view'] = '<div class="ui container fluid">
			<div class="ui grid">
				<div class="fifteen wide centered column">
					<div class="ui grid">
						<div class="ui inverted dimmer" id="unlockHarga-loaderlist">
							<div class="ui loader"></div>
						</div>
						<div class="sixteen wide centered column no-print">
							<form class="ui form form-javascript" id="unlockHarga-form" action="'.base_url().'index.php/unlockHarga/exe" method="post">
							<div class="ui grid">
								<div class="eight wide centered column no-print">
									<div class="fields">
										<div class="twelve wide field">
											<label>ID Pajangan</label>
											<input type="text" name="unlockHarga-id_product_atas" id="unlockHarga-id_product_atas" autofocus="on" autocomplete="off">
										</div>
										<div class="four wide field">
											<label style="visibility:hidden">-</label>
											<div class="ui fluid icon green button filter-input" id="unlockHarga-btnfilter" onclick=getProductForm("unlockHarga","1","F","0") title="Cari">
												<i class="filter icon"></i> Cari
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="ui grid">
								<div class="sixteen wide column"><div class="ui error message" id="unlockHarga-wraperror" style=""></div></div>
								<div class="sixteen wide column" id="unlockHarga-wrap_filter"></div>
							<div class="ui grid">
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>';
		
		$data["date"] = 0;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function get_product_from($date_input,$product_id,$id_row){
		$product_id = str_replace('_','',$product_id);
		
		$cek_old = substr($product_id, 0, 4);
		$cek_old = strtoupper($cek_old);
		
		if($cek_old == 'LAMA'){
			$cek_val = strtoupper($product_id);
			$cek_lama = str_replace('LAMA','',$cek_val);
			$data_product = $this->mp->get_lock_product_old($cek_lama);
		}else{
			$data_product = $this->mp->get_lock_product($product_id);
		}
		
		if(count($data_product) == 1){
			$data['found'] = 'single';
			
			$id_lengkap = explode('-', $data_product[0]->id);
			$id = $id_lengkap[1];
			
			$data['kelompok_barang'] = $data_product[0]->category_name;
			$nama_barang = $data_product[0]->product_name;
			$berat_barang = $data_product[0]->product_weight;
			$karat_barang = $data_product[0]->karat_name;
			
			$box_number = '';
			$totalnumberlength = 3;
			$numberlength = strlen($data_product[0]->id_box);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$box_number .= '0';
				}
			}
			
			$box_number .= $data_product[0]->id_box;
			
			$box_barang = $box_number;
			
			$data['view'] = '<table id="unlockHarga-tableinput" class="ui celled table table-input" cellspacing="0" width="100%"><thead><tr><th>ID Barang</th><th style="width:70px;">Box</th><th style="width:220px;">Nama Barang</th><th style="width:80px;">Karat</th><th style="width:100px;">Berat</th><th>Alasan Buka Kunci</th></tr></thead><tbody id="pos_body"><tr id="pos_tr_1"><td style="text-align:center">'.$id.'</td><td style="text-align:center">'.$box_barang.'</td><td style="text-align:center">'.$nama_barang.'</td><td style="text-align:center">'.$karat_barang.'</td><td style="text-align:center">'.$berat_barang.'</td><td><input class="form-pos" type="text" name="unlockHarga-alasan_unlock" id="unlockHarga-alasan_unlock"><input type="hidden" name="unlockHarga-id_product" value="'.$data_product[0]->id.'"></td></tr></tbody></table><div class="ui positive right floated labeled icon button" id="unlockHarga-btnexe" onclick=exePersediaan("unlockHarga")><i class="save icon"></i> Unlock</div>';
		}else{
			$data['found'] = 'not_single';
			
			$data['view'] = '<i class="close icon"></i><div class="header">List Barang</div><div class="content"><table class="ui celled table table-modal" id="unlockHarga-tablemodal" style="width:100%"><thead class="center aligned"><tr><th>No</th><th>ID</th><th>ID Lama</th><th>Nama Barang</th><th>Karat</th><th>Berat</th><th>Act</th></tr></thead><tbody>';
			
			$number = 1;
			foreach($data_product as $d){
				$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$d->id.'</td><td>'.$d->id_lama.'</td><td>'.$d->product_name.'</td><td>'.$d->karat_name.'</td><td class="right aligned">'.number_format($d->product_weight, 2).'</td><td class="center aligned"><button type="button" class="ui linkedin pilih button" onclick=getProductForm("unlockHarga","1","M","'.$d->id.'")><i class="hand point up outline icon"></i> Pilih</button></td>';
				
				$number = $number + 1;
			}
			
			$data['view'] .= '</tbody></table></div>';
		}
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function exe(){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		
		$data = array();
		$data['inputerror'] = '<ul class="list"></ul>';
		$data['success'] = TRUE;
		
		$alasan_unlock = $this->input->post('unlockHarga-alasan_unlock');
		
		if($alasan_unlock == ''){
			$data['inputerror'] .= '<li>Alasan Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($data['success'] == FALSE){
			echo json_encode($data);
			exit();
		}
		
		$id_product = $this->input->post('unlockHarga-id_product');
		$created_by = $this->session->userdata('gold_nama_user');
		
		$this->mp->unlock_product_control($id_product,$alasan_unlock,$created_by);
		
		$this->db->trans_complete();
		
		$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Berhasil Buka Kontrol!</div></div>';
		
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
