<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PosisiPajangan extends CI_Controller {
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
		$box = $this->mb->get_box_aktif();
		$data['view'] = '<div class="ui container fluid">
			<div class="ui grid">
				<div class="fifteen wide centered column">
					<div class="ui grid">
						<div class="ui inverted dimmer" id="posisiPajangan-loaderlist">
							<div class="ui loader"></div>
						</div>
						<div class="sixteen wide centered column no-print">
							<form class="ui form form-javascript" id="posisiPajangan-form-filter" action="'.base_url().'index.php/posisiPajangan/filter" method="post">
							<div class="fields">
								<div class="four wide field">
									<label>Per Tanggal</label>
									<input type="text" name="posisiPajangan-date" id="posisiPajangan-date" readonly>
								</div>
								<div class="two wide field">
									<label>Dari Box</label>
									<select class="fluid dropdown" name="posisiPajangan-filter_box_from" id="posisiPajangan-filter_box_from">';
										foreach($box as $b){
										$data['view'] .= '<option value="'.$b->id.'">BOX '.$b->nama_box.'</option>';
										}
									$data['view'] .= '</select>
								</div>
								<div class="two wide field">
									<label>Sampai Box</label>
									<select class="fluid dropdown" name="posisiPajangan-filter_box_to" id="posisiPajangan-filter_box_to">';
										
									$total = count($box);
									$number = 1;
									foreach($box as $b){ 
										if($total == $number){
											$selected = 'selected';
										}else{
											$selected = '';
										}
										
										$data['view'] .= '<option value="'.$b->id.'" '.$selected.'>BOX '.$b->nama_box.'</option>';
										
										$number = $number+1;
									}
										
									$data['view'] .= '</select>
								</div>
								<div class="three wide field">
									<label style="visibility:hidden">-</label>
									<select class="fluid dropdown" name="posisiPajangan-detail_rekap" id="posisiPajangan-detail_rekap">
										<option value="D">Detail</option>
										<option value="R" selected="selected">Rekap</option>
									</select>
								</div>
								<div class="three wide field">
									<label style="visibility:hidden">-</label>
									<select class="fluid dropdown" name="posisiPajangan-box_karat" id="posisiPajangan-box_karat">
										<option value="B">per Box</option>
										<option value="K" selected="selected">per Karat</option>
										<option value="C">per Kelompok</option>
									</select>
								</div>
								<div class="two wide field">
									<label style="visibility:hidden">-</label>
									<div class="ui fluid icon green button filter-input" id="posisiPajangan-btnfilter" onclick=filterPersediaan("posisiPajangan") title="Filter">
										<i class="filter icon"></i> Filter
									</div>
								</div>
							</div>
							</form>
						</div>
						<div class="sixteen wide column" id="posisiPajangan-wrap_filter"></div>
					</div>
				</div>
			</div>
		</div>';
		
		$data["date"] = 1;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function filter(){
		$this->db->trans_start();
		
		$per_tanggal = $this->input->post('posisiPajangan-date');
		$tanggal_tulis = $this->input->post('posisiPajangan-date');
		$perTanggal = $this->date_to_format($per_tanggal);
		$per_tanggal = date("Y-m-d",$perTanggal).' 23:59:59';
		
		$filter_box_from = $this->input->post('posisiPajangan-filter_box_from');
		$filter_box_to = $this->input->post('posisiPajangan-filter_box_to');
		$detail_rekap = $this->input->post('posisiPajangan-detail_rekap');
		$box_karat = $this->input->post('posisiPajangan-box_karat');
		
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
			$data['view'] = '<div class="ui stackable grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/posisiPajangan/pdf/'.$tanggal_tulis.'/'.$filter_box_from.'/'.$filter_box_to.'/'.$detail_rekap.'/'.$box_karat.'" target=_blank"><i class="paperclip icon"></i> Download</a></div></div><table id="posisiPajangan-tablefilter" class="ui celled table table-pjg" style="width:100%;"><thead><tr><th colspan="9" class="header-lap" style="border-top:none !important;border-bottom:none !important">LAPORAN STOK PAJANGAN '.$dr.' Per '.$bk.'</th></tr><tr><th colspan="9" class="header-lap" style="border-top:none !important;border-bottom:none !important">Per Tanggal '.$tanggal_tulis.', Box '.$filter_box_from.' s/d '.$filter_box_to.', Cabang : '.$sitename.'</th></tr><tr><th style="width:50px">No</th><th>ID</th><th>Nama Barang</th><th>Karat</th><th>Gram</th><th>Tgl Terima</th><th>Kelompok</th><th>Box</th><th>ID Lama</th></tr></thead><tbody>';
			
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
							
							$view_temp .= '<tr><td class="right aligned" style="border:none">'.$number.'</td><td class="center aligned" style="border:none">'.$df->id.'</td><td style="border:none">'.$df->product_name.'</td><td style="border:none">'.$df->karat_name.'</td><td class="right aligned" style="border:none">'.number_format($df->product_weight, 3).'</td><td class="center aligned" style="border:none">'.$tanggal_stock_in.'</td><td style="border:none">'.$df->category_name.'</td><td class="center aligned" style="border:none">'.$box_number.'</td><td class="center aligned" style="border:none">'.$id_lama.'</td></tr>';
							
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
						$data['view'] .= '<tr><td colspan="9" style="border:none">Box '.$box_number.'</td></tr>';
						$data['view'] .= $view_temp;
						$data['view'] .= '<tr><td colspan="3" class="td-total right aligned" style="border:none">Total (pcs)</td><td class="td-total right aligned" style="border:none">'.number_format($number, 0).'</td><td class="td-total right aligned" style="border:none">'.number_format($total_weight, 3).'</td><td class="td-total" style="border:none"></td><td class="td-total" style="border:none"></td><td class="td-total" style="border:none"></td><td class="td-total" style="border:none"></td></tr>';
						$data['view'] .= '<tr><td colspan="9" style="visibility:hidden">-</td></tr>';
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
								
								$view_temp .= '<tr><td class="right aligned" style="border:none">'.$number.'</td><td class="center aligned" style="border:none">'.$df->id.'</td><td style="border:none">'.$df->product_name.'</td><td style="border:none">'.$df->karat_name.'</td><td class="right aligned" style="border:none">'.number_format($df->product_weight, 3).'</td><td class="center aligned" style="border:none">'.$tanggal_stock_in.'</td><td style="border:none">'.$df->category_name.'</td><td class="center aligned" style="border:none">'.$box_number.'</td><td class="center aligned" style="border:none">'.$id_lama.'</td></tr>';
								
								$total_weight = $total_weight + $df->product_weight;
								$count_data = $count_data + 1;
							}
						}
					}
					
					if($count_data > 0){
						$karat_name = $db->karat_name;
						$data['view'] .= '<tr><td colspan="9" style="border:none">'.$karat_name.'</td></tr>';
						$data['view'] .= $view_temp;
						$data['view'] .= '<tr><td colspan="3" class="td-total right aligned" style="border:none">Total (pcs)</td><td class="td-total right aligned" style="border:none">'.number_format($number, 0).'</td><td class="td-total right aligned" style="border:none">'.number_format($total_weight, 3).'</td><td class="td-total" style="border:none"></td><td class="td-total" style="border:none"></td><td class="td-total" style="border:none"></td><td class="td-total" style="border:none"></td></tr>';
						$data['view'] .= '<tr><td colspan="9" style="visibility:hidden">-</td></tr>';
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
									
									$viewdetail .= '<tr><td class="right aligned" style="border:none">'.$number.'</td><td class="center aligned" style="border:none">'.$df->id.'</td><td style="border:none">'.$df->product_name.'</td><td style="border:none">'.$df->karat_name.'</td><td class="right aligned" style="border:none">'.number_format($df->product_weight, 3).'</td><td class="center aligned" style="border:none">'.$tanggal_stock_in.'</td><td style="border:none">'.$df->category_name.'</td><td class="center aligned" style="border:none">'.$box_number.'</td><td class="center aligned" style="border:none">'.$id_lama.'</td></tr>';
									
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
							$viewdetail .= '<tr><td colspan="3" class="td-total right aligned" style="border:none">Total (pcs)</td><td class="td-total right aligned" style="border:none">'.number_format($count_category_pcs, 0).'</td><td class="td-total right aligned" style="border:none">'.number_format($count_category_weight, 3).'</td><td class="td-total" style="border:none"></td><td class="td-total" style="border:none"></td><td class="td-total" style="border:none"></td><td class="td-total" style="border:none"></td></tr>';
							//$viewdetail .= '<tr><td colspan="9" style="visibility:hidden">-</td></tr>';
							
							$view[$dk->id] .= '<tr><td colspan="9" style="border:none">'.$dc->category_name.'</td></tr>';
							$view[$dk->id] .= $viewdetail;
						}
					}
					
					if($count_data_karat > 0){
						$view_temp .= '<tr><td colspan="9" style="border:none">'.$dk->karat_name.'</td></tr>';
						$view_temp .= $view[$dk->id];
						
						$view_temp .= '<tr><td colspan="3" class="td-total" style="border:none"></td><td class="right aligned td-total" style="border:none">'.number_format($count_data_karat, 0).'</td><td class="right aligned td-total" style="border:none">'.number_format($total_weight_karat, 3).'</td><td colspan="4" class="td-total" style="border:none"></td></tr>';
						$view_temp .= '<tr><td colspan="9" style="visibility:hidden">-</td></tr>';
						
						$total_weight = $total_weight + $total_weight_karat;
						$count_data_total = $count_data_total + $count_data_karat;
						
						$data['view'] .= $view_temp;
					}
				}
				
				if($count_data_total != 0){
					$data['view'] .= '<tr><td colspan="3" class="td-total right aligned" style="border:none">TOTAL</td><td class="td-total right aligned" style="border:none">'.number_format($count_data_total, 0).'</td><td class="td-total right aligned" style="border:none">'.number_format($total_weight, 3).'</td><td colspan="4" class="td-total" style="border:none"></td></tr>';
				}
			}
			
			$data['view'] .= '</tbody></table>';
		}else if($detail_rekap == 'R'){
			$data['view'] = '<div class="ui stackable grid"><div class="sixteen wide centered column full-print" style="padding-top:0px;padding-bottom:0px;text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/posisiPajangan/pdf/'.$tanggal_tulis.'/'.$filter_box_from.'/'.$filter_box_to.'/'.$detail_rekap.'/'.$box_karat.'" target=_blank"><i class="paperclip icon"></i> Download</a></div></div><div class="ui stackable grid"><div class="eight wide centered column full-print"><table id="posisiPajangan-tablefilter" class="ui celled table table-pjg" style="width:100%"><thead><tr><th colspan="5" class="header-lap" style="border-top:none !important;border-bottom:none !important">LAPORAN STOK PAJANGAN '.$dr.' Per '.$bk.'</th></tr><tr><th colspan="5" class="header-lap" style="border-top:none !important;border-bottom:none !important">Per Tanggal '.$tanggal_tulis.', Box '.$filter_box_from.' s/d '.$filter_box_to.', Cabang : '.$sitename.'</th></tr><tr><th style="width:50px">No</th><th></th><th>Karat</th><th>Pcs</th><th>Gram</th></tr></thead><tbody>';
			
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
							$view_temp .= '<tr><td colspan="2" style="border:none"></td><td style="border:none">'.$dk->karat_name.'</td><td class="right aligned" style="border:none">'.number_format($count_data_karat, 0).'</td><td class="right aligned" style="border:none">'.number_format($total_weight_karat, 3).'</td></tr>';
							
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
							
						$data['view'] .= '<tr><td class="right aligned" style="border:none">'.$number.'</td><td colspan="4" style="border:none">Box '.$box_number.'</td></tr>';
						$data['view'] .= $view_temp;
						$data['view'] .= '<tr><td colspan="3" class="td-total" style="border:none"></td><td class="right aligned td-total" style="border:none">'.number_format($count_data_box, 0).'</td><td class="right aligned td-total" style="border:none">'.number_format($total_weight, 3).'</td></tr>';
						$data['view'] .= '<tr><td colspan="5" style="visibility:hidden">-</td></tr>';
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
						
						$view_temp .= '<tr><td class="right aligned" style="border:none">'.$number.'</td><td style="border:none"></td><td style="border:none">'.$dk->karat_name.'</td><td class="right aligned" style="border:none">'.number_format($count_data_karat, 0).'</td><td class="right aligned" style="border:none">'.number_format($total_weight_karat, 3).'</td></tr>';
						
						$total_weight = $total_weight + $total_weight_karat;
						$count_data_total = $count_data_total + $count_data_karat;
						
						$data['view'] .= $view_temp;
					}
				}
				
				if($count_data_total != 0){
					$data['view'] .= '<tr><td colspan="3" class="td-total right aligned" style="border:none">TOTAL</td><td class="td-total right aligned" style="border:none">'.number_format($count_data_total, 0).'</td><td class="td-total right aligned" style="border:none">'.number_format($total_weight, 3).'</td></tr>';
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
							$view[$dk->id] .= '<tr><td class="right aligned" style="border:none"></td><td style="border:none"></td><td style="border:none">'.$dc->category_name.'</td><td class="right aligned" style="border:none">'.number_format($count_category_pcs, 0).'</td><td class="right aligned" style="border:none">'.number_format($count_category_weight, 3).'</td></tr>';
						}
					}
					
					if($count_data_karat > 0){
						$number = $number + 1;
						
						$view_temp .= '<tr><td class="right aligned" style="border:none">'.$number.'</td><td colspan="4" style="border:none">'.$dk->karat_name.'</td></tr>';
						$view_temp .= $view[$dk->id];
						
						$view_temp .= '<tr><td colspan="3" class="td-total" style="border:none"></td><td class="right aligned td-total" style="border:none">'.number_format($count_data_karat, 0).'</td><td class="right aligned td-total" style="border:none">'.number_format($total_weight_karat, 3).'</td></tr>';
						$view_temp .= '<tr><td colspan="5" style="visibility:hidden">-</td></tr>';
						
						$total_weight = $total_weight + $total_weight_karat;
						$count_data_total = $count_data_total + $count_data_karat;
						
						$data['view'] .= $view_temp;
					}
				}
				
				if($count_data_total != 0){
					$data['view'] .= '<tr><td colspan="3" class="td-total right aligned" style="border:none">TOTAL</td><td class="td-total right aligned" style="border:none">'.number_format($count_data_total, 0).'</td><td class="td-total right aligned" style="border:none">'.number_format($total_weight, 3).'</td></tr>';
				}
			}
					
			$data['view'] .= '</tbody></table></div></div>';
		}
		
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
		
		//$pdf = $this->m_pdf->load();
		$pdf = new \Mpdf\Mpdf();
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
