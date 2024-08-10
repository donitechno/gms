<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DataProduct extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->library('M_pdf');
		$this->load->model('M_login','ml');
		$this->load->model('M_box','mb');
		$this->load->model('M_product','mp');
		$this->load->model('M_karat','mk');
		$this->load->model('M_mutasi','mm');
		$this->load->model('M_kelompok_barang','mc');
		ini_set('memory_limit', '5012M');
	}
	
	public function index(){
		$data_category = $this->mc->get_all_product_category();
		$box = $this->mb->get_box_aktif();
		$karat = $this->mk->get_karat_srt();
		
		$data['view'] = '<div class="ui container fluid">
			<div class="ui grid">
				<div class="fifteen wide centered column">
					<div class="ui grid">
						<div class="ui inverted dimmer" id="dataProduct-loaderlist">
							<div class="ui loader"></div>
						</div>
						<div class="eight wide centered column no-print">
							<form class="ui form form-javascript" id="dataProduct-form-filter" action="'.base_url().'index.php/dataProduct/filter" method="post">
							<div class="fields">
								<div class="four wide field">
									<label>Kategori</label>
									<select class="fluid dropdown" name="dataProduct-category" id="dataProduct-category">
										<option value="All">-- All Kategori --</option>';
										foreach($data_category as $c){
											$data['view'] .= '<option value="'.$c->id.'">'.$c->category_name.'</option>';
										}
									$data['view'] .= '</select>
								</div>
								<div class="three wide field">
									<label>Box</label>
									<select class="fluid dropdown" name="dataProduct-box" id="dataProduct-box">
										<option value="All">-- All Box --</option>';
										foreach($box as $b){
											$data['view'] .= '<option value="'.$b->id.'">BOX '.$b->nama_box.'</option>';
										}
									$data['view'] .= '</select>
								</div>
								<div class="three wide field">
									<label>Karat</label>
									<select class="fluid dropdown" name="dataProduct-karat" id="dataProduct-karat">
										<option value="All">-- All Karat --</option>';
										foreach($karat as $k){
											$data['view'] .= '<option value="'.$k->id.'">'.$k->karat_name.'</option>';
										}
									$data['view'] .= '</select>
								</div>
								<div class="four wide field">
									<label>Status</label>
									<select class="fluid dropdown" name="dataProduct-status" id="dataProduct-status">
										<option value="A">Belum Terjual</option>
										<option value="S">Sudah Terjual</option>
										<option value="O">Lain-lain</option>
									</select>
								</div>
								<div class="two wide field">
									<label style="visibility:hidden">-</label>
									<div class="ui fluid icon green button filter-input" id="dataProduct-btnfilter" onclick=filterTransaksi("dataProduct") title="Filter">
										<i class="filter icon"></i> Filter
									</div>
								</div>
							</div>
							</form>
						</div>
						<div class="sixteen wide column" id="dataProduct-wrap_filter"></div>
					</div>
				</div>
			</div>
		</div>';
		
		$data["date"] = 0;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function filter(){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		
		$site_name = $this->mm->get_site_name();
		
		$filter_category =  $this->input->post('dataProduct-category');
		$filter_status =  $this->input->post('dataProduct-status');
		$filter_box = $this->input->post('dataProduct-box');
		$filter_karat = $this->input->post('dataProduct-karat');
		
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
		
		if($filter_status == 'All'){
			$data_filter = $this->mm->get_product_all($filter_category,$filter_box,$filter_karat);
		}else{
			$data_filter = $this->mm->get_product_filter($filter_category,$filter_box,$filter_karat,$filter_status);
		}
		
		$data['view'] = '<div class="ui stackable grid"><div class="fourteen wide centered column full-print"><table id="dataProduct-filtertabel" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th>No</th><th>ID Barang</th><th>Kelompok</th><th>Nama Barang</th><th>Karat</th><th>Berat</th><th>Box</th><th>Tgl Masuk</th><th>Tgl Keluar</th><th>Status</th></tr></thead><tbody>';
		
		$number = 1;
		
		foreach($data_filter as $d){
			$in_date = strtotime($d->in_date);
			$in_date = date('d M Y',$in_date);
			
			$out_date = strtotime($d->out_date);
			$out_date = date('d M Y',$out_date);
			if($out_date == '01 Jan 1970'){
				$out_date = '-';
			}
			
			if($d->status == 'A'){
				$status = 'Belum Terjual';
			}else if($d->status == 'S'){
				$status = 'Terjual | '.$d->id_sell;
			}else if($d->status == 'O'){
				$so_reason = $this->mm->get_so_reason($d->id);
				$status = 'Stock Out - '.$so_reason[0]->so_reason;
			}
			
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
			
			$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.$d->id.'</td><td class="center aligned">'.$d->category_name.'</td><td>'.$d->product_name.'</td><td class="center aligned">'.$d->karat_name.'</td><td class="right aligned">'.number_format($d->product_weight,3).'</td><td class="center aligned">'.$box_number.'</td><td class="center aligned"><span style="display:none">'.$d->in_date.'</span>'.$in_date.'</td><td class="center aligned"><span style="display:none">'.$d->in_date.'</span>'.$out_date.'</td><td>'.$status.'</td></tr>';
			
			$number = $number + 1;
		}
		
		//$data['view'] .= '</tbody></table></div></div>';
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function pdf($per_tanggal,$filter_box_from,$filter_box_to,$detail_rekap,$box_karat){
		$per_tanggal = str_replace('%20',' ',$per_tanggal);
		$tanggal_tulis = $per_tanggal;
		$perTanggal = $this->date_to_format($per_tanggal);
		$per_tanggal = date("Y-m-d",$perTanggal).' 23:59:59';
		
		if($detail_rekap == 'R'){
			$dr = 'Rekap';
		}else{
			$dr = 'Detail';
		}
		
		if($box_karat == 'B'){
			$bk = 'Box';
		}else if($box_karat == 'K'){
			$bk = 'Karat';
		}else{
			$bk = 'Kelompok';
		}
		
		$sitename = $this->mm->get_site_name();
		
		$array_box = array();
		
		if($detail_rekap == 'D'){
			$data['view'] = '<table class="lap_pdf_7" style="width:100%;" cellspacing="0"><thead><tr><th colspan="9" class="header-lap">LAPORAN STOK PAJANGAN '.$dr.' Per '.$bk.'</th></tr><tr><th colspan="9" class="header-lap">Per Tanggal '.$tanggal_tulis.', Box '.$filter_box_from.' s/d '.$filter_box_to.', Cabang : '.$sitename.'</th></tr><tr><th style="width:50px" class="th-5">No</th><th style="width:120px" class="th-5">ID</th><th class="th-5">Nama Barang</th><th style="width:50px" class="th-5">Karat</th><th class="th-5">Gram</th><th class="th-5">Tgl Terima</th><th class="th-5">Kelompok</th><th class="th-5">Box</th><th class="th-5">ID Lama</th></tr></thead><tbody>';
			
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
			
			if($box_karat == 'B'){			
				$data_box = $this->mb->get_box_by_range($filter_box_from,$filter_box_to);
				
				foreach($data_box as $db){
					$view_temp = '';
					$number = 0;
					$count_data = 0;
					$total_weight = 0;
					$id_box = $db->id;
					foreach($data_filter as $df){
						if($array_box[$df->id] == $id_box){
							$number = $number + 1;
							
							$tanggal_stock_in = strtotime($df->in_date);
							$tanggal_stock_in = date('d-M-y',$tanggal_stock_in);
							
							$box_number = '';
							$totalnumberlength = 3;
							$numberlength = strlen($array_box[$df->id]);
							$numberspace = $totalnumberlength - $numberlength;
							if($numberspace != 0){
								for ($i = 1; $i <= $numberspace; $i++){
									$box_number .= '0';
								}
							}
							
							$box_number .= $array_box[$df->id];
							
							if($df->id_lama == 'NULL'){
								$id_lama = '';
							}else{
								$id_lama = $df->id_lama;
							}
							
							$view_temp .= '<tr><td class="right-aligned">'.$number.'</td><td class="center aligned">'.$df->id.'</td><td>'.substr($df->product_name,0,20).'</td><td style="text-align:center">'.$df->karat_name.'</td><td class="right-aligned">'.number_format($df->product_weight, 3).'</td><td style="text-align:center">'.$tanggal_stock_in.'</td><td style="text-align:center">'.$df->category_name.'</td><td style="text-align:center">'.$box_number.'</td><td>'.$id_lama.'</td></tr>';
							
							$total_weight = $total_weight + $df->product_weight;
							$count_data = $count_data + 1;
						}
					}
					
					if($count_data > 0){
						$box_number = '';
						$totalnumberlength = 3;
						$numberlength = strlen($id_box);
						$numberspace = $totalnumberlength - $numberlength;
						if($numberspace != 0){
							for ($i = 1; $i <= $numberspace; $i++){
								$box_number .= '0';
							}
						}
						
						$box_number .= $id_box;
						$data['view'] .= '<tr><td colspan="9">Box '.$box_number.'</td></tr>';
						$data['view'] .= $view_temp;
						$data['view'] .= '<tr><td colspan="3" class="td-total right-aligned">Total (pcs)</td><td class="td-total right-aligned">'.number_format($number, 0).'</td><td class="td-total right-aligned">'.number_format($total_weight, 3).'</td><td class="td-total"></td><td class="td-total"></td><td class="td-total"></td><td class="td-total"></td></tr>';
						$data['view'] .= '<tr><td colspan="9"><span style="visibility:hidden">-</span></td></tr>';
					}
				}
			}else if($box_karat == 'K'){
				$data_karat = $this->mk->get_karat_srt();
				
				foreach($data_karat as $db){
					$view_temp = '';
					$number = 0;
					$count_data = 0;
					$total_weight = 0;
					foreach($data_filter as $df){
						if($db->id == $df->id_karat){
							if($array_box[$df->id] >= $filter_box_from && $array_box[$df->id] <= $filter_box_to){
								$number = $number + 1;
								
								$tanggal_stock_in = strtotime($df->in_date);
								$tanggal_stock_in = date('d-M-y',$tanggal_stock_in);
								
								$box_number = '';
								$totalnumberlength = 3;
								$numberlength = strlen($array_box[$df->id]);
								$numberspace = $totalnumberlength - $numberlength;
								if($numberspace != 0){
									for ($i = 1; $i <= $numberspace; $i++){
										$box_number .= '0';
									}
								}
								
								$box_number .= $array_box[$df->id];
								
								if($df->id_lama == 'NULL'){
									$id_lama = '';
								}else{
									$id_lama = $df->id_lama;
								}
								
								$view_temp .= '<tr><td class="right-aligned">'.$number.'</td><td class="center aligned">'.$df->id.'</td><td>'.substr($df->product_name,0,20).'</td><td style="text-align:center">'.$df->karat_name.'</td><td class="right-aligned">'.number_format($df->product_weight, 3).'</td><td style="text-align:center">'.$tanggal_stock_in.'</td><td style="text-align:center">'.$df->category_name.'</td><td style="text-align:center">'.$box_number.'</td><td>'.$id_lama.'</td></tr>';
								
								$total_weight = $total_weight + $df->product_weight;
								$count_data = $count_data + 1;
							}
						}
					}
					
					if($count_data > 0){
						$karat_name = $db->karat_name;
						$data['view'] .= '<tr><td colspan="8">'.$karat_name.'</td></tr>';
						$data['view'] .= $view_temp;
						$data['view'] .= '<tr><td colspan="3" class="td-total right-aligned">Total (pcs)</td><td class="td-total right-aligned">'.number_format($number, 0).'</td><td class="td-total right-aligned">'.number_format($total_weight, 3).'</td><td class="td-total"></td><td class="td-total"></td><td class="td-total"></td><td class="td-total"></td></tr>';
						$data['view'] .= '<tr><td colspan="9"><span style="visibility:hidden">-</span></td></tr>';
					}
				}
			}else if($box_karat == 'C'){
				$data_karat = $this->mk->get_karat_srt();
				$total_weight = 0;
				$count_data_total = 0;
				
				$data_category = $this->mc->get_all_product_category();
				
				$view = array();
				foreach($data_karat as $dk){
					$view_temp = '';
					$count_data_karat = 0;
					$total_weight_karat = 0;
					$view[$dk->id] = '';
					
					foreach($data_category as $dc){
						$viewdetail = '';
						$count_category_pcs = 0;
						$count_category_weight = 0;
						$number = 0;
						
						foreach($data_filter as $df){
							if(($df->id_karat == $dk->id) && ($df->id_category == $dc->id)){
								if($array_box[$df->id] >= $filter_box_from && $array_box[$df->id] <= $filter_box_to){
									$number = $number + 1;
								
									$tanggal_stock_in = strtotime($df->in_date);
									$tanggal_stock_in = date('d-M-y',$tanggal_stock_in);
									
									$box_number = '';
									$totalnumberlength = 3;
									$numberlength = strlen($array_box[$df->id]);
									$numberspace = $totalnumberlength - $numberlength;
									if($numberspace != 0){
										for ($i = 1; $i <= $numberspace; $i++){
											$box_number .= '0';
										}
									}
									
									$box_number .= $array_box[$df->id];
									
									if($df->id_lama == 'NULL'){
										$id_lama = '';
									}else{
										$id_lama = $df->id_lama;
									}
									
									$viewdetail .= '<tr><td class="right-aligned">'.$number.'</td><td class="center-aligned">'.$df->id.'</td><td>'.$df->product_name.'</td><td>'.$df->karat_name.'</td><td class="right-aligned">'.number_format($df->product_weight, 3).'</td><td class="center-aligned">'.$tanggal_stock_in.'</td><td>'.$df->category_name.'</td><td class="center-aligned">'.$box_number.'</td><td class="center-aligned">'.$id_lama.'</td></tr>';
									
									//$total_weight = $total_weight + $df->product_weight;
									//$count_data_total = $count_data_total + 1;
									
									$count_category_pcs = $count_category_pcs + 1;
									$count_category_weight = $count_category_weight + $df->product_weight;
									$count_data_karat = $count_data_karat + 1;
									$total_weight_karat = $total_weight_karat + $df->product_weight;
								}
							}
						}
						
						if($count_category_pcs != 0){
							$viewdetail .= '<tr><td colspan="3" class="td-total right-aligned">Total (pcs)</td><td class="td-total right-aligned">'.number_format($count_category_pcs, 0).'</td><td class="td-total right-aligned">'.number_format($count_category_weight, 3).'</td><td class="td-total"></td><td class="td-total"></td><td class="td-total"></td><td class="td-total"></td></tr>';
							//$viewdetail .= '<tr><td colspan="9" style="visibility:hidden">-</td></tr>';
							
							$view[$dk->id] .= '<tr><td colspan="9">'.$dc->category_name.'</td></tr>';
							$view[$dk->id] .= $viewdetail;
						}
					}
					
					if($count_data_karat > 0){
						$view_temp .= '<tr><td colspan="9">'.$dk->karat_name.'</td></tr>';
						$view_temp .= $view[$dk->id];
						
						$view_temp .= '<tr><td colspan="3" class="td-total"></td><td class="right-aligned td-total">'.number_format($count_data_karat, 0).'</td><td class="right-aligned td-total">'.number_format($total_weight_karat, 3).'</td><td colspan="4" class="td-total"></td></tr>';
						$view_temp .= '<tr><td colspan="9"><span style="visibility:hidden">-</span></td></tr>';
						
						$total_weight = $total_weight + $total_weight_karat;
						$count_data_total = $count_data_total + $count_data_karat;
						
						$data['view'] .= $view_temp;
					}
				}
				
				if($count_data_total != 0){
					$data['view'] .= '<tr><td colspan="3" class="td-total right-aligned">TOTAL</td><td class="td-total right-aligned">'.number_format($count_data_total, 0).'</td><td class="td-total right-aligned">'.number_format($total_weight, 3).'</td><td colspan="4" class="td-total"></td></tr>';
				}
			}
			
			$data['view'] .= '</tbody></table>';
		}else if($detail_rekap == 'R'){
			$data['view'] = '<table class="lap_pdf_5" cellspacing="0" style="width:100%"><thead><tr><th colspan="5" class="header-lap">LAPORAN STOK PAJANGAN '.$dr.' Per '.$bk.'</th></tr><tr><th colspan="5" class="header-lap">Per Tanggal '.$tanggal_tulis.', Box '.$filter_box_from.' s/d '.$filter_box_to.', Cabang : '.$sitename.'</th></tr><tr><th style="width:50px" class="th-5">No</th><th class="th-5"></th><th class="th-5">Karat</th><th class="th-5">Pcs</th><th class="th-5">Gram</th></tr></thead><tbody>';
			
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
			
			if($box_karat == 'B'){			
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
							$view_temp .= '<tr><td colspan="2"></td><td style="text-align:center">'.$dk->karat_name.'</td><td class="right-aligned">'.number_format($count_data_karat, 0).'</td><td class="right-aligned">'.number_format($total_weight_karat, 3).'</td></tr>';
							
							$total_weight = $total_weight + $total_weight_karat;
							$count_data_box = $count_data_box + $count_data_karat;
						}
					}
					
					if($view_temp != ''){
						$number = $number + 1;
						
						$box_number = '';
						$totalnumberlength = 3;
						$numberlength = strlen($id_box);
						$numberspace = $totalnumberlength - $numberlength;
						if($numberspace != 0){
							for ($i = 1; $i <= $numberspace; $i++){
								$box_number .= '0';
							}
						}
						
						$box_number .= $id_box;
							
						$data['view'] .= '<tr><td class="right-aligned">'.$number.'</td><td colspan="4">Box '.$box_number.'</td></tr>';
						$data['view'] .= $view_temp;
						$data['view'] .= '<tr><td colspan="3" class="td-total"></td><td class="right-aligned td-total">'.number_format($count_data_box, 0).'</td><td class="right-aligned td-total">'.number_format($total_weight, 3).'</td></tr>';
						$data['view'] .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
					}
				}
			}else if($box_karat == 'K'){
				$data_karat = $this->mk->get_karat_srt();
				$number = 0;
				$total_weight = 0;
				$count_data_total = 0;
				
				foreach($data_karat as $dk){
					$view_temp = '';
					$count_data_karat = 0;
					$total_weight_karat = 0;
					foreach($data_filter as $df){
						if($df->id_karat == $dk->id){
							if($array_box[$df->id] >= $filter_box_from && $array_box[$df->id] <= $filter_box_to){
								$count_data_karat = $count_data_karat + 1;
								$total_weight_karat = $total_weight_karat + $df->product_weight;
							}
							
						}
					}
					
					if($count_data_karat > 0){
						$number = $number + 1;
						
						$view_temp .= '<tr><td class="right-aligned">'.$number.'</td><td></td><td>'.$dk->karat_name.'</td><td class="right-aligned">'.number_format($count_data_karat, 0).'</td><td class="right-aligned">'.number_format($total_weight_karat, 3).'</td></tr>';
						
						$total_weight = $total_weight + $total_weight_karat;
						$count_data_total = $count_data_total + $count_data_karat;
						
						$data['view'] .= $view_temp;
					}
				}
				
				if($count_data_total != 0){
					$data['view'] .= '<tr><td colspan="3" class="td-total right-aligned">TOTAL</td><td class="td-total right-aligned">'.number_format($count_data_total, 0).'</td><td class="td-total right-aligned">'.number_format($total_weight, 3).'</td></tr>';
				}
			}else if($box_karat == 'C'){
				$data_karat = $this->mk->get_karat_srt();
				$number = 0;
				$total_weight = 0;
				$count_data_total = 0;
				
				$data_category = $this->mc->get_all_product_category();
				
				$view = array();
				foreach($data_karat as $dk){
					$view_temp = '';
					$count_data_karat = 0;
					$total_weight_karat = 0;
					$view[$dk->id] = '';
					
					foreach($data_category as $dc){
						$count_category_pcs = 0;
						$count_category_weight = 0;
						
						foreach($data_filter as $df){
							if(($df->id_karat == $dk->id) && ($df->id_category == $dc->id)){
								if($array_box[$df->id] >= $filter_box_from && $array_box[$df->id] <= $filter_box_to){
									$count_category_pcs = $count_category_pcs + 1;
									$count_category_weight = $count_category_weight + $df->product_weight;
									$count_data_karat = $count_data_karat + 1;
									$total_weight_karat = $total_weight_karat + $df->product_weight;
								}
							}
						}
						
						if($count_category_pcs != 0){
							$view[$dk->id] .= '<tr><td class="right-aligned"></td><td></td><td>'.$dc->category_name.'</td><td class="right-aligned">'.number_format($count_category_pcs, 0).'</td><td class="right-aligned">'.number_format($count_category_weight, 3).'</td></tr>';
						}
					}
					
					if($count_data_karat > 0){
						$number = $number + 1;
						
						$view_temp .= '<tr><td class="right-aligned">'.$number.'</td><td colspan="4">'.$dk->karat_name.'</td></tr>';
						$view_temp .= $view[$dk->id];
						
						$view_temp .= '<tr><td colspan="3" class="td-total"></td><td class="right-aligned td-total">'.number_format($count_data_karat, 0).'</td><td class="right-aligned td-total">'.number_format($total_weight_karat, 3).'</td></tr>';
						$view_temp .= '<tr><td colspan="5"><span style="visibility:hidden">-</span></td></tr>';
						
						$total_weight = $total_weight + $total_weight_karat;
						$count_data_total = $count_data_total + $count_data_karat;
						
						$data['view'] .= $view_temp;
					}
				}
				
				if($count_data_total != 0){
					$data['view'] .= '<tr><td colspan="3" class="td-total right-aligned">TOTAL</td><td class="td-total right-aligned">'.number_format($count_data_total, 0).'</td><td class="td-total right-aligned">'.number_format($total_weight, 3).'</td></tr>';
				}
			}
					
			$data['view'] .= '</tbody></table>';
		}
		
		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		$pdf = $this->m_pdf->load();
		$pdf->setFooter('{PAGENO} / {nb}');
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right;font-family:samsungone;font-size:11px;">{PAGENO} / {nb}</div>');
		$pdf->AddPage('P');
        $pdf->WriteHTML($html);
		
        $pdf->Output("Laporan Posisi Pajangan.pdf", "I");
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
