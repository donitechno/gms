<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StockOut extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->library('M_pdf');
		$this->load->model('M_login','ml');
		$this->load->model('M_karat','mk');
		$this->load->model('M_box','mb');
		$this->load->model('M_kelompok_barang','mc');
		$this->load->model('M_product','mp');
		$this->load->model('M_nama_barang','mmp');
		$this->load->model('M_mutasi','mm');
	}
	
	public function index(){
		$karat = $this->mk->get_karat_srt();
		$box = $this->mb->get_box_aktif();
		$category = $this->mc->get_product_category();
		$from = $this->mp->get_product_from();
		$from_filter = $this->mp->get_product_from_filter();
		
		$data['view'] = '<div class="ui container fluid">
			<div class="ui pointing secondary menu">
				<a class="item active" data-tab="stockOut-first" style="width:50%" onkeyup=entToInsert("stockOut")>
					<i class="edit icon"></i> Input Data Stock Out
				</a>
				<a class="item" data-tab="stockOut-second" style="width:50%" onclick=filterPersediaan("stockOut")>
					<i class="list ol icon"></i> List Data Stock Out
				</a>
			</div>
			<div class="ui bottom attached tab segment active" data-tab="stockOut-first" onkeyup=entToInsert("stockOut")>
				<div class="ui inverted dimmer" id="stockOut-loaderinput">
					<div class="ui large text loader">Loading</div>
				</div>
				<form class="ui form form-javascript" id="stockOut-form" action="'.base_url().'index.php/stockOut/insert" method="post">
				<div class="ui grid">
					<div class="right floated ten wide column" style="padding-bottom:0">
						<div class="fields">
							<div class="ten wide field">
								<label>Alasan Stock Out</label>
								<input type="text" name="stockOut-reason" id="stockOut-reason" onkeydown=entToNextID("stockOut-dateinput") autofocus="on" autocomplete="off">
							</div>
							<div class="six wide field">
							  <label>Tanggal Stock Out</label>
								<div id="stockOut-wrapdate">
									<input type="text" name="stockOut-dateinput" id="stockOut-dateinput" readonly onkeydown=entToNextID("stockOut-input_1_1") onchange=entToNextID("stockOut-input_1_1")>
								</div>
							  </div>
						</div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0">
						<div class="ui red message" id="stockOut-wraperror" style="display:none"></div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0">
						<table id="stockOut-tableinput" class="ui celled table table-input" cellspacing="0" width="100%" style="margin-top:15px;">
							<thead>
								<tr class="center aligned">
									<th>No</th>
									<th>ID Barang</th>
									<th>Asal</th>
									<th>Kelompok</th>
									<th style="width:260px;">Nama Barang</th>
									<th style="width:80px;">Karat</th>
									<th style="width:80px;">Box</th>
									<th style="width:80px;">Berat</th>
									<th>X</th>
								</tr>
							</thead>
							<tbody id="stockOut-pos_body">
								<tr id="stockOut-pos_tr_1">
									<td class="center aligned">1</td>
									<td>
										<input type="text" class="form-pos" name="stockOut-id_1" id="stockOut-input_1_1" onkeydown=entToTabInput("stockOut","1","1") onblur=getProductForm("stockOut","1","F","0") autocomplete="off" placeholder="Masukkan ID Barang">
									</td>
									<td>
										<input class="form-pos" type="text" name="stockOut-asal_1" id="stockOut-input_1_2" onkeydown=entToTabInput("stockOut","1","2") readonly>
									</td>
									<td>
										<input class="form-pos" type="text" name="stockOut-category_1" id="stockOut-input_1_3" onkeydown=entToTabInput("stockOut","1","3") readonly>
									</td>
									<td>
										<input class="form-pos" type="text" name="stockOut-nama_barang_1" id="stockOut-input_1_4" onkeydown=entToTabInput("stockOut","1","4") readonly>
									</td>
									<td>
										<input class="form-pos" type="text" name="stockOut-karat_1" id="stockOut-input_1_5" onkeydown=entToTabInput("stockOut","1","5") readonly>
									</td>
									<td>
										<input class="center aligned form-pos" type="text" name="stockOut-box_1" id="stockOut-input_1_6" onkeydown=entToTabInput("stockOut","1","6") readonly>
									</td>
									<td>
										<input class="form-pos right aligned" type="text" name="stockOut-berat_1" id="stockOut-input_1_7" onkeydown=entToTabInput("stockOut","1","7") readonly >
									</td>
									<td class="center aligned" id="stockOut-input_1_8">
										<div class="ui tiny icon google plus button"><i class="ban icon"></i></div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="eight wide column">
						<div class="ui grid" style="padding-top:30px;padding-bottom:15px">
							<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">F2</div>
							<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: SIMPAN DATA</div>
							<div class="four wide column ket-bawah" style="padding-bottom:0;padding-top:0">F5</div>
							<div class="twelve wide column ket-bawah" style="padding-bottom:0;padding-top:0">: REFRESH HALAMAN</div>
						</div>
					</div>
					<div class="eight wide column">
						<div class="ui grid" style="padding-top:30px;padding-bottom:15px">
							<div class="eight wide column ket-bawah right aligned" style="padding-bottom:0;padding-top:0">TOTAL (Gram)</div>
							<div class="eight wide column ket-bawah right aligned" id="stockOut-total" style="padding-bottom:0;padding-top:0">0</div>
						</div>
					</div>
				</div>
				</form>
			</div>
			<div class="ui bottom attached tab segment" data-tab="stockOut-second">
				<div class="ui inverted dimmer" id="stockOut-loaderlist">
					<div class="ui large text loader">Loading</div>
				</div>
				<form class="ui form form-javascript" id="stockOut-form-filter" action="'.base_url().'index.php/stockOut/filter" method="post">
				<div class="ui grid">
					<div class="sixteen wide column" style="padding-bottom:0">
						<div class="fields">
							<div class="two wide field">
								<label>Kelompok Barang</label>
								<select name="stockOut-filter_category" id="stockOut-filter_category">
									<option value="All">-- All --</option>';
									foreach($category as $c){
									$data['view'] .= '<option value="'.$c->id.'">'.$c->category_name.'</option>';
									}
								$data['view'] .= '</select>
							</div>
							<div class="two wide field">
								<label>Asal Barang</label>
								<select name="stockOut-filter_from" id="stockOut-filter_from">
									<option value="All">-- All --</option>';
									foreach($from_filter as $f){
									$data['view'] .= '<option value="'.$f->id.'">'.$f->from_name.'</option>';
									}
								$data['view'] .= '</select>
							</div>
							<div class="two wide field">
								<label>Box</label>
								<select name="stockOut-filter_box" id="stockOut-filter_box">
									<option value="All">-- All --</option>';
									foreach($box as $b){
									$data['view'] .= '<option value="'.$b->id.'">BOX '.$b->nama_box.'</option>';
									}
								$data['view'] .= '</select>
							</div>
							<div class="two wide field">
								<label>Karat</label>
								<select name="stockOut-filter_karat" id="stockOut-filter_karat">
									<option value="All">-- All --</option>';
									foreach($karat as $k){
									$data['view'] .= '<option value="'.$k->id.'">'.$k->karat_name.'</option>';
									}
								$data['view'] .= '</select>
							</div>
							<div class="three wide field">
								<label>Tgl Stock Out</label>
								<input type="text" name="stockOut-filterfromdate" id="stockOut-filterfromdate" readonly>
							</div>
							<div class="one wide field" style="text-align:center;margin-top:30px">
								<label>s.d</label>
							</div>
							<div class="three wide field">
								<label style="visibility:hidden">Tgl Stock Out</label>
								<input type="text" name="stockOut-filtertodate" id="stockOut-filtertodate" readonly>
							</div>
							<div class="one wide field">
								<label style="visibility:hidden">-</label>
								<div class="ui fluid icon green button filter-input" id="stockOut-btnfilter" onclick=filterPersediaan("stockOut") title="Filter">
									<i class="filter icon"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" id="stockOut-wrap_filter" style="padding-top:0"></div>
					<div class="six wide column">
						<div class="ui grid" style="padding-bottom:40px">
							<div class="eight wide column ket-bawah left aligned" style="padding-bottom:0;padding-top:0">TOTAL (Pcs)</div>
							<div class="eight wide column ket-bawah right aligned" id="stockOut-filter-pcs" style="padding-bottom:0;padding-top:0">0 Pcs</div>
						</div>
					</div>
					<div class="four wide column">
					</div>
					<div class="six wide column">
						<div class="ui grid" style="padding-bottom:40px">
							
							<div class="eight wide column ket-bawah right aligned" style="padding-bottom:0;padding-top:0">TOTAL (Gram)</div>
							<div class="eight wide column ket-bawah right aligned" id="stockOut-filter-gram" style="padding-bottom:0;padding-top:0">0.000</div>
						</div>
					</div>
				</div>
				</form>
			</div>	
		</div>';
		
		$data["date"] = 3;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function get_product_from($stock_out_date,$product_id,$id_row){
		$stock_out_date = str_replace('%20',' ',$stock_out_date);
		$stockOutDate = $this->date_to_format($stock_out_date);
		$stock_out_date = date("Y-m-d",$stockOutDate).' 23:59:59';
		
		$product_id = str_replace('_','',$product_id);
		
		$cek_old = substr($product_id, 0, 4);
		$cek_old = strtoupper($cek_old);
		
		if($cek_old == 'LAMA'){
			$cek_val = strtoupper($product_id);
			$cek_lama = str_replace('LAMA','',$cek_val);
			$data_stock_out = $this->mp->get_product_date_before_old($stock_out_date,$cek_lama);
		}else{
			$data_stock_out = $this->mp->get_product_date_before($stock_out_date,$product_id);
		}
		
		if(count($data_stock_out) == 1){
			$data['found'] = 'single';
			
			$data['id'] = $data_stock_out[0]->id;
			
			$this->validate_pindah_after($stock_out_date,$data_stock_out[0]->id);
			
			$data['kelompok_barang'] = $data_stock_out[0]->category_name;
			$data['nama_barang'] = $data_stock_out[0]->product_name;
			$data['berat_barang'] = $data_stock_out[0]->product_weight;
			$data['karat_barang'] = $data_stock_out[0]->karat_name;
			$data['asal_barang'] = $data_stock_out[0]->from_name;
			
			$box_number = '';
			$totalnumberlength = 3;
			$numberlength = strlen($data_stock_out[0]->id_box);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$box_number .= '0';
				}
			}
			
			$box_number .= $data_stock_out[0]->id_box;
			
			$data['box_barang'] = $box_number;
			$data['asal_barang'] = $data_stock_out[0]->from_name;
			$data['ket_asal_barang'] = $data_stock_out[0]->product_from_desc;
		}else{
			$data['found'] = 'not_single';
			
			$data['view'] = '<i class="close icon"></i><div class="header">List Barang</div><div class="content"><table class="ui celled table table-modal" id="stockOut-tablemodal" style="width:100%"><thead class="center aligned"><tr><th>No</th><th>ID</th><th>ID Lama</th><th>Nama Barang</th><th>Karat</th><th>Berat</th><th>Act</th></tr></thead><tbody>';
			
			$number = 1;
			foreach($data_stock_out as $d){
				$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$d->id.'</td><td>'.$d->id_lama.'</td><td>'.$d->product_name.'</td><td>'.$d->karat_name.'</td><td class="right aligned">'.number_format($d->product_weight, 2).'</td><td class="center aligned"><button type="button" class="ui linkedin pilih button" onclick=getProductForm("stockOut","0","M","'.$d->id.'")><i class="hand point up outline icon"></i> Pilih</button></td>';
				
				$number = $number + 1;
			}
			
			$data['view'] .= '</tbody></table></div>';
		}
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function insert($id_row){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		$this->validate($id_row);
		
		$stock_out_date = $this->input->post('stockOut-dateinput');
		$tanggal_stock_out = $this->input->post('stockOut-dateinput');
		$stock_out_date = str_replace('%20',' ',$stock_out_date);
		$stockOutDate = $this->date_to_format($stock_out_date);
		$stock_out_date = date("Y-m-d",$stockOutDate).' 23:59:59';
		$tgl_stock_out = date("Y-m-d",$stockOutDate).' 00:00:00';
		
		$so_reason = $this->input->post('stockOut-reason');
		$product_id = $this->input->post('stockOut-id_'.$id_row);
		
		$this->validate_pindah_after($stock_out_date,$product_id);
		
		$data_stock_out = $this->mp->get_product_date_before($stock_out_date,$product_id);
		
		$id_karat = $data_stock_out[0]->id_karat;
		$id_box = $data_stock_out[0]->id_box;
		$berat_barang = $data_stock_out[0]->product_weight;
		
		$stock_out_id = 'SO-'.$product_id;
		$created_by = $this->session->userdata('gold_nama_user');
		
		$this->mp->insert_stock_out($stock_out_id,$product_id,$id_karat,$id_box,$so_reason,$tgl_stock_out,$created_by);
		$this->mp->update_product_stock_out($product_id,$tgl_stock_out);
		
		$sitecode = $this->mm->get_site_code();
		$account_srt = $this->mm->get_default_account('SRT');
		$account_pjg = $this->mm->get_default_account('PJG');
		
		$desc = 'STOCK OUT - NO.REG : '.$product_id;
		
		$this->mm->insert_mutasi_gram($sitecode,$stock_out_id,'Out',$id_karat,$account_pjg,$account_srt,$berat_barang,$desc,$tgl_stock_out,$created_by);
		
		$id_row = $id_row + 1;
		
		$data['tanggal_stock_out'] = '<input type="text" name="stockOut-dateinput" id="stockOut-dateinput" value="'.$tanggal_stock_out.'" readonly onkeydown=entToNextID("stockOut-input_1_1")>';
		
		$data['button_messsage'] = '<div class="ui tiny icon green button"><i class="check circle icon"></i></div>';
		
		$data['view'] = '<tr id="stockOut-pos_tr_'.$id_row.'"><td class="center aligned">'.$id_row.'</td><td><input class="form-pos" type="text" name="stockOut-id_'.$id_row.'" id="stockOut-input_'.$id_row.'_1" onkeydown=entToTabInput("stockOut","'.$id_row.'","1") onblur=getProductForm("stockOut","'.$id_row.'","F","0") autocomplete="off" placeholder="Masukkan ID Barang"></td><td><input class="form-pos" type="text" name="stockOut-asal_'.$id_row.'" id="stockOut-input_'.$id_row.'_2" onkeydown=entToTabInput("stockOut","'.$id_row.'","2") readonly></td><td><input class="form-pos" type="text" name="stockOut-category_'.$id_row.'" id="stockOut-input_'.$id_row.'_3" onkeydown=entToTabInput("stockOut","'.$id_row.'","3") readonly></td><td><input class="form-pos" type="text" name="stockOut-nama_barang_'.$id_row.'" id="stockOut-input_'.$id_row.'_4" onkeydown=entToTabInput("stockOut","'.$id_row.'","4") readonly></td><td><input class="form-pos" type="text" name="stockOut-karat_'.$id_row.'" id="stockOut-input_'.$id_row.'_5" onkeydown=entToTabInput("stockOut","'.$id_row.'","5") readonly></td><td><input class="center aligned form-pos" type="text" name="stockOut-box_'.$id_row.'" id="stockOut-input_'.$id_row.'_6" onkeydown=entToTabInput("stockOut","'.$id_row.'","6") readonly></td><td><input class="form-pos right aligned" type="text" name="stockOut-berat_'.$id_row.'" id="stockOut-input_'.$id_row.'_7" onkeydown=entToTabInput("stockOut","'.$id_row.'","7") readonly></td><td class="center aligned" id="stockOut-input_'.$id_row.'_8"><div class="ui tiny icon google plus button"><i class="ban icon"></i></div></td></tr>';
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	private function validate($id_row){
		$data = array();
		$data['inputerror'] = '<ul class="list"></ul>';
		$data['success'] = TRUE;
		
		$stock_out_date = $this->input->post('stockOut-dateinput');
		if($stock_out_date == ''){
			$data['inputerror'] .= '<li>Tanggal Stock Out Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		$stock_out_date = str_replace('%20',' ',$stock_out_date);
		$stockOutDate = $this->date_to_format($stock_out_date);
		$stock_out_date = date("Y-m-d",$stockOutDate).' 23:59:59';
		
		$so_reason = $this->input->post('stockOut-reason');
		if($so_reason == ''){
			$data['inputerror'] .= '<li>Alasan Stock Out Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		$product_id = $this->input->post('stockOut-id_'.$id_row);
		$data_stock_out = $this->mp->get_product_date_before($stock_out_date,$product_id);
		
		if($product_id == ''){
			$data['inputerror'] .= '<li>ID Barang Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if(count($data_stock_out) == 0){
			$data['inputerror'] .= '<li>ID Barang Tidak Ditemukan!</li>';
			$data['success'] = FALSE;
		}
		
		if(count($data_stock_out) > 1){
			$data['inputerror'] .= '<li>ID Barang Salah!</li>';
			$data['success'] = FALSE;
		}
		
		if($data['success'] == FALSE){
			$data['val_filter'] = 'N';
			echo json_encode($data);
			exit();
		}
	}
	
	public function filter(){
		$this->db->trans_start();
		
		$filter_category = $this->input->post('stockOut-filter_category');
		$filter_category2 = $this->input->post('stockOut-filter_category');
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
		
		$filter_from = $this->input->post('stockOut-filter_from');
		$filter_from2 = $this->input->post('stockOut-filter_from');
		if($filter_from == 'All'){
			$filter_from = '';
			$data_from = $this->mp->get_product_from_filter();
			for($i=0; $i<count($data_from); $i++){
				if($i == 0){
					$filter_from .= '"'.$data_from[$i]->id.'"';
				}else{
					$filter_from .= ',"'.$data_from[$i]->id.'"';
				}
			}
		}else{
			$filter_from = '"'.$filter_from.'"';
		}
		
		$filter_box = $this->input->post('stockOut-filter_box');
		$filter_box2 = $this->input->post('stockOut-filter_box');
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
		
		$filter_karat = $this->input->post('stockOut-filter_karat');
		$filter_karat2 = $this->input->post('stockOut-filter_karat');
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
		
		$from_stock_out = $this->input->post('stockOut-filterfromdate');
		$from_date = $this->input->post('stockOut-filterfromdate');
		$to_stock_out = $this->input->post('stockOut-filtertodate');
		$to_date = $this->input->post('stockOut-filtertodate');
		
		$stockFromTime = $this->date_to_format($from_stock_out);
		$stockToTime = $this->date_to_format($to_stock_out);
		
		$from_stock_out = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_stock_out = date("Y-m-d",$stockToTime).' 23:59:59';
		
		$data_stock_out = $this->mp->get_filter_stock_out($from_stock_out,$to_stock_out,$filter_category,$filter_from,$filter_box,$filter_karat);
		
		$data['view'] = '<div class="sixteen wide centered column full-print" style="padding-top:25px;padding-bottom:5px;text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/stockOut/pdf/'.$from_date.'/'.$to_date.'/'.$filter_category2.'/'.$filter_from2.'/'.$filter_box2.'/'.$filter_karat2.'" target=_blank"><i class="file pdf outline icon"></i> Download</a></div><table id="stockOut-tablefilter" class="ui celled table" style="width:100%"><thead><tr><th style="width:50px">No</th><th>ID Stock Out</th><th>Nama Barang</th><th>Karat</th><th>Box</th><th>Berat</th><th>Tgl Keluar</th><th>Alasan</th></tr></thead><tbody>';
		
		$number = 1;
		$total_pcs = 0;
		$total_gram = 0;
		foreach($data_stock_out as $d){
			$tanggal_stock_out = strtotime($d->trans_date);
			$tanggal_stock_out = date('d-M-y',$tanggal_stock_out);
			
			$box_number = '';
			$totalnumberlength = 3;
			$numberlength = strlen($d->id_box);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$box_number .= '0';
				}
			}
			
			$box_number .= $d->id_box;
			
			$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$d->id.'</td><td>'.$d->product_name.'</td><td>'.$d->karat_name.'</td><td class="center aligned">'.$box_number.'</td><td class="right aligned">'.number_format($d->product_weight, 2).'</td><td>'.$tanggal_stock_out.'</td><td>'.$d->so_reason.'</td></tr>';
			
			$total_pcs = $total_pcs + 1;
			$total_gram = $total_gram + $d->product_weight;
			$number = $number + 1;
		}
		
		$data['total_pcs'] = $total_pcs;
		$data['total_gram'] = number_format($total_gram, 3);
		$data['view'] .= '</tbody></table>';
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	private function validate_pindah_after($pindah_box_date,$product_id){
		$cek_pindah_after = $this->mp->get_pindah_box_after($pindah_box_date,$product_id);
		if(count($cek_pindah_after) > 0){
			$data['view'] = '<i class="close icon"></i><div class="header">Warning</div><div class="content">';
			
			$data['view'] .= '<div class="ui icon header center aligned"><i class="exclamation triangle red icon"></i>Barang Tidak Dapat di Stock Out</div><div class="ui negative message" style="text-align:center"><div class="header">Andah Sudah Pernah Melakukan Transaksi Pindah Box Melewati Tanggal Yang Anda Pilih. Pilih Tanggal Lain atau Hubungi Tim IT Untuk Penanganan Lebih Lanjut</div></div>';
			
			$data['view'] .= '<table class="ui celled table" id="modal-table" style="width:100%;"><thead><tr><th>No</th><th>Tgl Pindah Box</th><th>ID</th><th>Dari Box</th><th>Ke Box</th></tr></thead><tbody>';
			
			$number = 1;
			foreach($cek_pindah_after as $c){
				$tanggal_pindah_box = strtotime($c->trans_date);
				$tanggal_pindah_box = date('d-M-y',$tanggal_pindah_box);
				
				$box_number_from = '';
				$totalnumberlength = 3;
				$numberlength = strlen($c->id_box_from);
				$numberspace = $totalnumberlength - $numberlength;
				if($numberspace != 0){
					for ($i = 1; $i <= $numberspace; $i++){
						$box_number_from .= '0';
					}
				}
				
				$box_number_from .= $c->id_box_from;
				
				$box_number_to = '';
				$totalnumberlength = 3;
				$numberlength = strlen($c->id_box_to);
				$numberspace = $totalnumberlength - $numberlength;
				if($numberspace != 0){
					for ($i = 1; $i <= $numberspace; $i++){
						$box_number_to .= '0';
					}
				}
				
				$box_number_to .= $c->id_box_to;
				
				$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$tanggal_pindah_box.'</td><td>'.$c->id.'</td><td>'.$box_number_from.'</td><td>'.$box_number_to.'</td>';
				
				$number = $number + 1;
			}
			
			$data['view'] .= '</tbody></table></div></div>';
			
			$data['val_filter'] = 'Y';
			$data['success'] = FALSE;
			echo json_encode($data);
			exit();
		}
	}
	
	public function pdf($from_stock_out,$to_stock_out,$filter_category,$filter_from,$filter_box,$filter_karat){
		$this->db->trans_start();
		
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
			
			$tulis_category = 'All Kelompok';
		}else{
			$data_category = $this->mc->get_category_by_id($filter_category);
			$tulis_category = $data_category[0]->category_name;
			$filter_category = '"'.$filter_category.'"';
		}
		
		if($filter_from == 'All'){
			$filter_from = '';
			$data_from = $this->mp->get_product_from_filter();
			for($i=0; $i<count($data_from); $i++){
				if($i == 0){
					$filter_from .= '"'.$data_from[$i]->id.'"';
				}else{
					$filter_from .= ',"'.$data_from[$i]->id.'"';
				}
			}
			
			$tulis_from = 'All Sumber';
		}else{
			$from_data = $this->mp->get_product_from_detail($filter_from);
			$tulis_from = $from_data[0]->from_name;
			
			$filter_from = '"'.$filter_from.'"';
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
			
			$tulis_box = 'All Box';
		}else{
			$tulis_box = 'Box '.$filter_box;
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
			
			$tulis_karat = 'All Karat';
		}else{
			$tulis_karat = $this->mk->get_karat_name_by_id($filter_karat);
			$tulis_karat = 'Karat '.$tulis_karat;
			
			$filter_karat = '"'.$filter_karat.'"';
		}
		
		$from_stock_out = str_replace('%20',' ',$from_stock_out);
		$to_stock_out = str_replace('%20',' ',$to_stock_out);
		
		$from_date = $from_stock_out;
		$to_date = $to_stock_out;
		
		if($from_date == $to_date){
			$tanggal_tulis = $from_date;
		}else{
			$tanggal_tulis = $from_date.' S/D '.$to_date;
		}
		
		$stockFromTime = $this->date_to_format($from_stock_out);
		$stockToTime = $this->date_to_format($to_stock_out);
		
		$from_stock_out = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_stock_out = date("Y-m-d",$stockToTime).' 23:59:59';
		
		$data_stock_out = $this->mp->get_filter_stock_out($from_stock_out,$to_stock_out,$filter_category,$filter_from,$filter_box,$filter_karat);
		
		$site_name = $this->mm->get_site_name();
		
		$data['view'] = '<div class="sixteen wide centered column center aligned" style="font-weight:bold;text-align:center;font-size:13px;font-family:samsungone"><span style="text-align">Laporan Stock Out, Cabang '.$site_name.'</span><br><span>Tanggal '.$tanggal_tulis.'</span><br><span>'.$tulis_category.', '.$tulis_from.', '.$tulis_box.', '.$tulis_karat.'</span><br><span style="visibility:hidden">-</span></div><table class="lap_pdf_6" style="width:100%"><thead><tr><th style="width:40px" class="th-5">No</th><th class="th-5">ID Stock Out</th><th class="th-5">Nama Barang</th><th class="th-5">Karat</th><th class="th-5">Box</th><th class="th-5">Berat</th><th class="th-5">Tgl Keluar</th><th class="th-5">Alasan</th></tr></thead><tbody>';
		
		$number = 1;
		$total_pcs = 0;
		$total_gram = 0;
		foreach($data_stock_out as $d){
			$tanggal_stock_out = strtotime($d->trans_date);
			$tanggal_stock_out = date('d-M-y',$tanggal_stock_out);
			
			$box_number = '';
			$totalnumberlength = 3;
			$numberlength = strlen($d->id_box);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$box_number .= '0';
				}
			}
			
			$box_number .= $d->id_box;
			
			$data['view'] .= '<tr><td class="text-center">'.$number.'</td><td>'.$d->id.'</td><td>'.$d->product_name.'</td><td style="text-align:center">'.$d->karat_name.'</td><td style="text-align:center">'.$box_number.'</td><td style="text-align:right">'.number_format($d->product_weight, 3).'</td><td style="text-align:center">'.$tanggal_stock_out.'</td><td>'.$d->so_reason.'</td></tr>';
			
			$total_pcs = $total_pcs + 1;
			$total_gram = $total_gram + $d->product_weight;
			$number = $number + 1;
		}
		
		$data['total_pcs'] = $total_pcs;
		$data['total_gram'] = number_format($total_gram, 3);
		$data['view'] .= '<tr><td colspan="4" style="text-align:right;border-top:1px solid #000;border-bottom:1px solid #000;font-weight:bold">TOTAL</td><td colspan="1" style="border-top:1px solid #000;border-bottom:1px solid #000;font-weight:bold;text-align:center">'.number_format($total_pcs,0).' PCS</td><td style="text-align:right;border-top:1px solid #000;border-bottom:1px solid #000;font-weight:bold">'.number_format($total_gram,3).'</td><td colspan="2" style="text-align:right;border-top:1px solid #000;border-bottom:1px solid #000;font-weight:bold"></td>';
		$data['view'] .= '</tbody></table>';
		
		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		//$pdf = $this->m_pdf->load();
		$pdf = new \Mpdf\Mpdf();
		$pdf->setFooter('{PAGENO} / {nb}');
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right;font-family:samsungone;font-size:11px;">{PAGENO} / {nb}</div>');
		$pdf->AddPage('P');
        $pdf->WriteHTML($html);
		
        $pdf->Output("Laporan Stock In.pdf", "I");
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
