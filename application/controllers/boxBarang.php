<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class boxBarang extends CI_Controller {	
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->model('M_box','mb');
		$this->load->model('M_product','mp');
		$this->load->model('M_karat','mk');
	}
	
	public function index(){
		$data['view'] = '<div class="ui container fluid">
			<div class="ui grid">
				<div class="ten wide centered column">
					<div class="ui grid">
						<div class="ui inverted dimmer" id="boxBarang-loader">
							<div class="ui loader"></div>
						</div>
						<div class="sixteen wide column" id="boxBarang-wrap">';
		
		$data_box = $this->mb->get_all_box();
		
		$data['view'] .= '<table id="boxBarang-table" class="ui celled table" style="width:100%"><thead><tr><th>Box</th><th style="width:80px">Action</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_box as $d){
			$box_number = '';
			$totalnumberlength = 3;
			$numberlength = strlen($d->nama_box);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$box_number .= '0';
				}
			}
			
			$box_number .= $d->nama_box;
			
			$data['view'] .= '<tr><td>'.$box_number.'</td><td style="padding: 0;text-align: center;">';
			
			if($d->status == 'A'){
				$data['view'] .= '<button class="ui tiny icon positive button" onclick=changeStatus("boxBarang","'.$d->id.'","NA") title="Aktif"><i class="adjust icon"></i></button>';
			}else{
				$data['view'] .= '<button class="ui tiny icon negative button" onclick=changeStatus("boxBarang","'.$d->id.'","A") title="Tidak Aktif"><i class="adjust icon"></i></button>';
			}
			
			$data['view'] .= '</td></tr>';
			
			$number = $number + 1;
		}
		
		$data['view'] .= '</tbody></table>';
		
		$data['view'] .= '</div>
					</div>
				</div>
			</div>
		</div>';
		
		$data["date"] = 0;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function get_all_box(){
		$this->db->trans_start();
		
		$data_box = $this->mb->get_all_box();
		
		$data['view'] = '<table id="filter_data_tabel" class="ui celled table" style="width:100%"><thead><tr><th>Box</th><th style="width:80px">Action</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_box as $d){
			$box_number = '';
			$totalnumberlength = 3;
			$numberlength = strlen($d->nama_box);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$box_number .= '0';
				}
			}
			
			$box_number .= $d->nama_box;
			
			$data['view'] .= '<tr><td>'.$box_number.'</td><td style="padding: 0;text-align: center;">';
			
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
	
	public function change_status($id_box = 0,$status_box = 0){
		$this->db->trans_start();
		
		$per_tanggal = date("Y-m-d").' 23:59:59';
		
		$filter_box_from = $id_box;
		$filter_box_to = $id_box;
		
		$detail_rekap = 'R';
		$box_karat = 'B';
		
		$array_box = array();
		$filter_product_pindah_box = '';
				
		$pindah_box = $this->mp->get_posisi_pindah_box($per_tanggal,$filter_box_from,$filter_box_to);
		if(count($pindah_box) == 0){
			$filter_product_pindah_box .= '""';
		}else{
			for($i=0; $i<count($pindah_box); $i++){
				if($i == 0){
					$filter_product_pindah_box .= '"'.$pindah_box[$i]->id_product.'"';
				}else{
					$filter_product_pindah_box .= ',"'.$pindah_box[$i]->id_product.'"';
				}
			}
		}
		
		$data_filter = $this->mp->get_posisi_detail_pajangan($per_tanggal,$filter_box_from,$filter_box_to,$filter_product_pindah_box);
		
		foreach($data_filter as $d){
			$array_box[$d->id] = $d->id_box;
		}
		
		foreach($pindah_box as $p){
			$array_box[$p->id_product] = $p->id_box_from;
		}
				
		$data_box = $this->mb->get_box_by_range($filter_box_from,$filter_box_to);
		$data_karat = $this->mk->get_karat_srt();
		$number = 0;
		
		foreach($data_box as $db){
			$view_temp = '';
			$count_data_box = 0;
			$total_weight = 0;
			$id_box = $db->id;
			
			foreach($data_karat as $dk){
				$count_data_karat = 0;
				$total_weight_karat = 0;
				foreach($data_filter as $df){
					if($array_box[$df->id] == $id_box && $df->id_karat == $dk->id){
						$count_data_karat = $count_data_karat + 1;
						$total_weight_karat = $total_weight_karat + $df->product_weight;
					}
				}
				
				if($count_data_karat > 0){
					$view_temp .= 'Y';
					
					$total_weight = $total_weight + $total_weight_karat;
					$count_data_box = $count_data_box + $count_data_karat;
				}
			}
			
			if($view_temp != ''){
				$number = $number + 1;
			}
		}
		
		if($number > 0){
			$data['success'] = false;
			$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="times red icon"></i>Box Tidak Kosong, Tidak Dapat Di Non Aktifkan!</div></div>';
		}else{
			$data['success'] = true;
			$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Status Berhasil Diubah!</div></div>';
			$this->mb->change_box_status($id_box,$status_box);
		}
		
		$this->db->trans_complete();
		echo json_encode($data);
	}
}
