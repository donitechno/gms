<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PindahBox extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->model('M_login','ml');
		$this->load->model('M_karat','mk');
		$this->load->model('M_box','mb');
		$this->load->model('M_kelompok_barang','mc');
		$this->load->model('M_product','mp');
		$this->load->model('M_nama_barang','mmp');
	}
	
	public function index(){
		$karat = $this->mk->get_karat_srt();
		$box = $this->mb->get_box_aktif();
		$category = $this->mc->get_product_category();
		$from = $this->mp->get_product_from();
		
		$data['view'] = '<div class="ui container fluid">
			<div class="ui pointing secondary menu">
				<a class="item active" data-tab="pindahBox-first" style="width:50%" onkeyup=entToInsert("pindahBox")>
					<i class="edit icon"></i> Input Pindah Box
				</a>
				<a class="item" data-tab="pindahBox-second" style="width:50%" onclick=filterPersediaan("pindahBox")>
					<i class="list ol icon"></i> List Pindah Box
				</a>
			</div>
			<div class="ui bottom attached tab segment active" data-tab="pindahBox-first" onkeyup=entToInsert("pindahBox")>
				<div class="ui inverted dimmer" id="pindahBox-loaderinput">
					<div class="ui large text loader">Loading</div>
				</div>
				<form class="ui form form-javascript" id="pindahBox-form" action="'.base_url().'index.php/pindahBox/insert" method="post">
				<div class="ui grid">
					<div class="right floated four wide column" style="padding-bottom:0">
						<div class="fields">
							<div class="sixteen wide field">
							  <label>Tanggal Pindah Box</label>
								<div id="pindahBox-wrapdate">
									<input type="text" name="pindahBox-dateinput" id="pindahBox-dateinput" readonly onkeydown=entToNextID("pindahBox-input_1_1") onchange=entToNextID("pindahBox-input_1_1")>
								</div>
							  </div>
						</div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0">
						<div class="ui red message" id="pindahBox-wraperror" style="display:none"></div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0">
						<table id="pindahBox-tableinput" class="ui celled table table-input" cellspacing="0" width="100%" style="margin-top:15px;">
							<thead>
								<tr class="center aligned">
									<th>No</th>
									<th>ID Barang</th>
									<th>Kelompok</th>
									<th style="width:280px;">Nama Barang</th>
									<th style="width:80px;">Karat</th>
									<th style="width:120px;">Berat</th>
									<th style="width:80px;">Dari Box</th>
									<th style="width:80px;">Ke Box</th>
									<th>X</th>
								</tr>
							</thead>
							<tbody id="pindahBox-pos_body">
								<tr id="pindahBox-pos_tr_1">
									<td class="center aligned">1</td>
									<td>
										<input class="form-pos" type="text" name="pindahBox-id_1" id="pindahBox-input_1_1" onkeydown=entToTabInput("pindahBox","1","1") onblur=getProductForm("pindahBox","1","F","0") autocomplete="off" placeholder="Masukkan ID Barang" autofocus="on">
									</td>
									<td>
										<input class="form-pos" type="text" name="pindahBox-category_1" id="pindahBox-input_1_2" onkeydown=entToTabInput("pindahBox","1","2") readonly>
									</td>
									<td>
										<input class="form-pos" type="text" name="pindahBox-nama_barang_1" id="pindahBox-input_1_3" onkeydown=entToTabInput("pindahBox","1","3") readonly>
									</td>
									<td>
										<input class="form-pos" type="text" name="pindahBox-karat_1" id="pindahBox-input_1_4" onkeydown=entToTabInput("pindahBox","1","4") readonly>
									</td>
									<td>
										<input class="form-pos right aligned" type="text" name="pindahBox-berat_1" id="pindahBox-input_1_5" onkeydown=entToTabInput("pindahBox","1","5") readonly>
									</td>
									<td>
										<input class="form-pos center aligned" type="text" name="pindahBox-box_1" id="pindahBox-input_1_6" onkeydown=entToTabInput("pindahBox","1","6") readonly>
									</td>
									<td id="pindahBox-wrap_ke_box_1">
										<input class="form-pos" type="text" name="pindahBox-box_to_1" id="pindahBox-input_1_7" onkeydown=entToTabInput("pindahBox","1","7") readonly>
									</td>
									<td class="center aligned" id="pindahBox-input_1_8">
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
							<div class="eight wide column ket-bawah right aligned" id="pindahBox-total" style="padding-bottom:0;padding-top:0">0</div>
						</div>
					</div>
				</div>
				</form>
			</div>
			<div class="ui bottom attached tab segment" data-tab="pindahBox-second">
				<div class="ui inverted dimmer" id="pindahBox-loaderlist">
					<div class="ui large text loader">Loading</div>
				</div>
				<form class="ui form form-javascript" id="pindahBox-form-filter" action="'.base_url().'index.php/pindahBox/filter" method="post">
				<div class="ui grid">
					<div class="sixteen wide column" style="padding-bottom:0">
						<div class="fields">
							<div class="two wide field">
								<label>Kelompok Barang</label>
								<select name="pindahBox-filter_category" id="pindahBox-filter_category">
									<option value="All">-- All --</option>';
									
									foreach($category as $c){
									$data['view'] .= '<option value="'.$c->id.'">'.$c->category_name.'</option>';
									}
								$data['view'] .= '</select>
							</div>
							<div class="two wide field">
								<label>Box Asal</label>
								<select name="pindahBox-filter_box_from" id="pindahBox-filter_box_from">
									<option value="All">-- All --</option>';
									foreach($box as $b){
									$data['view'] .= '<option value="'.$b->id.'">BOX '.$b->nama_box.'</option>';
									}
								$data['view'] .= '</select>
							</div>
							<div class="two wide field">
								<label>Box Tujuan</label>
								<select name="pindahBox-filter_box_to" id="pindahBox-filter_box_to">
									<option value="All">-- All --</option>';
									foreach($box as $b){
									$data['view'] .= '<option value="'.$b->id.'">BOX '.$b->nama_box.'</option>';
									}
								$data['view'] .= '</select>
							</div>
							<div class="two wide field">
								<label>Karat</label>
								<select name="pindahBox-filter_karat" id="pindahBox-filter_karat">
									<option value="All">-- All --</option>';
									foreach($karat as $k){
									$data['view'] .= '<option value="'.$k->id.'">'.$k->karat_name.'</option>';
									}
								$data['view'] .= '</select>
							</div>
							<div class="three wide field">
								<label>Tgl Pindah Box</label>
								<input type="text" name="pindahBox-filterfromdate" id="pindahBox-filterfromdate" readonly>
							</div>
							<div class="one wide field" style="text-align:center;margin-top:30px">
								<label>s.d</label>
							</div>
							<div class="three wide field">
								<label style="visibility:hidden">-</label>
								<input type="text" name="pindahBox-filtertodate" id="pindahBox-filtertodate" readonly>
							</div>
							<div class="one wide field">
								<label style="visibility:hidden">-</label>
								<div class="ui fluid icon green button filter-input" id="pindahBox-btnfilter" onclick=filterPersediaan("pindahBox") title="Filter">
									<i class="filter icon"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" id="pindahBox-wrap_filter" style="padding-top:0"></div>
					<div class="six wide column">
						<div class="ui grid" style="padding-bottom:40px">
							<div class="eight wide column ket-bawah left aligned" style="padding-bottom:0;padding-top:0">TOTAL (Pcs)</div>
							<div class="eight wide column ket-bawah right aligned" id="pindahBox-filter-pcs" style="padding-bottom:0;padding-top:0">0 Pcs</div>
						</div>
					</div>
					<div class="four wide column">
					</div>
					<div class="six wide column">
						<div class="ui grid" style="padding-bottom:40px">
							
							<div class="eight wide column ket-bawah right aligned" style="padding-bottom:0;padding-top:0">TOTAL (Gram)</div>
							<div class="eight wide column ket-bawah right aligned" id="pindahBox-filter-gram" style="padding-bottom:0;padding-top:0">0.000</div>
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
	
	public function get_product_from($pindah_box_date,$product_id,$id_row){
		$pindah_box_date = str_replace('%20',' ',$pindah_box_date);
		$pindahBoxDate = $this->date_to_format($pindah_box_date);
		$pindah_box_date = date("Y-m-d",$pindahBoxDate).' 23:59:59';
		
		$product_id = str_replace('_','',$product_id);
		
		$cek_old = substr($product_id, 0, 4);
		$cek_old = strtoupper($cek_old);
		
		if($cek_old == 'LAMA'){
			$cek_val = strtoupper($product_id);
			$cek_lama = str_replace('LAMA','',$cek_val);
			$data_pindah_box = $this->mp->get_product_date_before_old($pindah_box_date,$cek_lama);
		}else{
			$data_pindah_box = $this->mp->get_product_date_before($pindah_box_date,$product_id);
		}
		
		if(count($data_pindah_box) == 1){
			$data['found'] = 'single';
			
			$data['id'] = $data_pindah_box[0]->id;
			
			$this->validate_pindah_after($pindah_box_date,$data_pindah_box[0]->id);
			
			$data['kelompok_barang'] = $data_pindah_box[0]->category_name;
			$data['nama_barang'] = $data_pindah_box[0]->product_name;
			$data['berat_barang'] = $data_pindah_box[0]->product_weight;
			$data['karat_barang'] = $data_pindah_box[0]->karat_name;
			
			$box_number = '';
			$totalnumberlength = 3;
			$numberlength = strlen($data_pindah_box[0]->id_box);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$box_number .= '0';
				}
			}
			
			$box_number .= $data_pindah_box[0]->id_box;
			
			$data['box_barang'] = $box_number;
			
			$data_box_to = $this->mb->get_box_to($data_pindah_box[0]->id_box);
			$data['view'] = '<select name="pindahBox-box_to_'.$id_row.'" id="pindahBox-input_'.$id_row.'_7"><option value=""></option>';
		
			foreach($data_box_to as $db){
				$data['view'] .= '<option value="'.$db->id.'">'.$db->nama_box.'</option>';
			}
			
			$data['view'] .= '</select>';
		}else{
			$data['found'] = 'not_single';
	
			$data['view'] = '<i class="close icon"></i><div class="header">List Barang</div><div class="content"><table class="ui celled table table-modal" id="pindahBox-tablemodal" style="width:100%"><thead class="center aligned"><tr><th>No</th><th>ID</th><th>ID Lama</th><th>Nama Barang</th><th>Karat</th><th>Berat</th><th>Act</th></tr></thead><tbody>';
			
			$number = 1;
			foreach($data_pindah_box as $d){
				$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$d->id.'</td><td>'.$d->id_lama.'</td><td>'.$d->product_name.'</td><td>'.$d->karat_name.'</td><td class="right aligned">'.number_format($d->product_weight, 2).'</td><td class="center aligned"><button type="button" class="ui linkedin pilih button" onclick=getProductForm("pindahBox","0","M","'.$d->id.'")><i class="hand point up outline icon"></i> Pilih</button></td>';
				
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
		
		$pindah_box_date = $this->input->post('pindahBox-dateinput');
		$tanggal_pindah_box = $this->input->post('pindahBox-dateinput');
		$pindah_box_date = str_replace('%20',' ',$pindah_box_date);
		$pindahBoxDate = $this->date_to_format($pindah_box_date);
		$pindah_box_date = date("Y-m-d",$pindahBoxDate).' 23:59:59';
		$tgl_pindah_box = date("Y-m-d",$pindahBoxDate).' 00:00:00';
		$product_id = $this->input->post('pindahBox-id_'.$id_row);
		$id_box_to = $this->input->post('pindahBox-box_to_'.$id_row);
		
		$this->validate_pindah_after($pindah_box_date,$product_id);
		
		$data_pindah_box = $this->mp->get_product_date_before($pindah_box_date,$product_id);
		
		$id_karat = $data_pindah_box[0]->id_karat;
		$id_box = $data_pindah_box[0]->id_box;
		
		$pindah_box_id = 'PB-'.$product_id.'-';
		
		$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
		$numbertrans = count($transnumber);
		$numbertrans = $numbertrans + 1;
		$totalnumberlength = 3;
		$numberlength = strlen($numbertrans);
		$numberspace = $totalnumberlength - $numberlength;
		if($numberspace != 0){
			for ($i = 1; $i <= $numberspace; $i++){
				$pindah_box_id .= '0';
			}
		}
		$pindah_box_id .= $numbertrans;
		
		$created_by = $this->session->userdata('gold_username');
		
		$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
		$this->mp->update_product_pindah_box($product_id,$id_box_to);
		
		$box_number = '';
		$totalnumberlength = 3;
		$numberlength = strlen($id_box_to);
		$numberspace = $totalnumberlength - $numberlength;
		if($numberspace != 0){
			for ($i = 1; $i <= $numberspace; $i++){
				$box_number .= '0';
			}
		}
		
		$box_number .= $id_box_to;
		
		$data['box_ke'] = '<input type="text" class="center aligned form-pos" name="pindahBox-box_to_'.$id_row.'" id="pindahBox-input_'.$id_row.'_7" value="'.$box_number.'" readonly>';
		
		$id_row = $id_row + 1;
		
		$data['tanggal_pindah_box'] = '<input type="text" name="pindahBox-dateinput" id="pindahBox-dateinput" style="background:#FFF" value="'.$tanggal_pindah_box.'" readonly onkeydown=entToNextID("pindahBox-input_1_1")>';
		
		$data['button_messsage'] = '<div class="ui tiny icon green button"><i class="check circle icon"></i></div>';
		
		$data['view'] = '<tr id="pindahBox-pos_tr_'.$id_row.'"><td class="center aligned">'.$id_row.'</td><td><input class="form-pos" type="text" name="pindahBox-id_'.$id_row.'" id="pindahBox-input_'.$id_row.'_1" onkeydown=entToTabInput("pindahBox","'.$id_row.'","1") onblur=getProductForm("pindahBox","'.$id_row.'","F","0") autocomplete="off" placeholder="Masukkan ID Barang"></td><td><input class="form-pos" type="text" name="pindahBox-category_'.$id_row.'" id="pindahBox-input_'.$id_row.'_2" onkeydown=entToTabInput("pindahBox","'.$id_row.'","2") readonly></td><td><input class="form-pos" type="text" name="pindahBox-nama_barang_'.$id_row.'" id="pindahBox-input_'.$id_row.'_3" onkeydown=entToTabInput("pindahBox","'.$id_row.'","3") readonly></td><td><input class="form-pos" type="text" name="pindahBox-karat_'.$id_row.'" id="pindahBox-input_'.$id_row.'_4" onkeydown=entToTabInput("pindahBox","'.$id_row.'","4") readonly></td><td><input class="form-pos right aligned" type="text" name="pindahBox-berat_'.$id_row.'" id="pindahBox-input_'.$id_row.'_5" onkeydown=entToTabInput("pindahBox","'.$id_row.'","5") readonly></td><td><input class="center aligned form-pos" type="text" name="pindahBox-box_'.$id_row.'" id="pindahBox-input_'.$id_row.'_6" onkeydown=entToTabInput("pindahBox","'.$id_row.'","6") readonly></td><td id="pindahBox-wrap_ke_box_'.$id_row.'"><input class="form-pos" type="text" name="pindahBox-box_to_'.$id_row.'" id="pindahBox-input_'.$id_row.'_7" onkeydown=entToTabInput("pindahBox","'.$id_row.'","7") readonly></td><td class="center aligned" id="pindahBox-input_'.$id_row.'_8"><div class="ui tiny icon google plus button"><i class="ban icon"></i></div></td></tr>';
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	private function validate($id_row){
		$data = array();
		$data['inputerror'] = '<ul class="list"></ul>';
		$data['success'] = TRUE;
		
		$data['error_message'] = '';
		
		$pindah_box_date = $this->input->post('pindahBox-dateinput');
		$pindah_box_date = str_replace('%20',' ',$pindah_box_date);
		$stockOutDate = $this->date_to_format($pindah_box_date);
		$pindah_box_date = date("Y-m-d",$stockOutDate).' 23:59:59';
		
		$product_id = $this->input->post('pindahBox-id_'.$id_row);
		$id_box_to = $this->input->post('pindahBox-box_to_'.$id_row);
		$data_pindah_box = $this->mp->get_product_date_before($pindah_box_date,$product_id);
		
		if($product_id == ''){
			$data['inputerror'] .= '<li>ID Barang Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if(count($data_pindah_box) == 0){
			$data['inputerror'] .= '<li>ID Barang Tidak Ditemukan!</li>';
			$data['success'] = FALSE;
		}
		
		if(count($data_pindah_box) > 1){
			$data['inputerror'] .= '<li>ID Barang Salah!</li>';
			$data['success'] = FALSE;
		}
		
		if($id_box_to == ''){
			$data['inputerror'] .= '<li>Box Tujuan Harus Diisi!</li>';
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
		
		$filter_category = $this->input->post('pindahBox-filter_category');
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
		
		$filter_box_from = $this->input->post('pindahBox-filter_box_from');
		if($filter_box_from == 'All'){
			$filter_box_from = '';
			$data_box = $this->mb->get_box_aktif();
			for($i=0; $i<count($data_box); $i++){
				if($i == 0){
					$filter_box_from .= '"'.$data_box[$i]->id.'"';
				}else{
					$filter_box_from .= ',"'.$data_box[$i]->id.'"';
				}
			}
		}else{
			$filter_box_from = '"'.$filter_box_from.'"';
		}
		
		$filter_box_to = $this->input->post('pindahBox-filter_box_to');
		if($filter_box_to == 'All'){
			$filter_box_to = '';
			$data_box = $this->mb->get_box_aktif();
			for($i=0; $i<count($data_box); $i++){
				if($i == 0){
					$filter_box_to .= '"'.$data_box[$i]->id.'"';
				}else{
					$filter_box_to .= ',"'.$data_box[$i]->id.'"';
				}
			}
		}else{
			$filter_box_to = '"'.$filter_box_to.'"';
		}
		
		$filter_karat = $this->input->post('pindahBox-filter_karat');
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
		
		$from_pindah_box = $this->input->post('pindahBox-filterfromdate');
		$to_pindah_box = $this->input->post('pindahBox-filtertodate');
		
		$stockFromTime = $this->date_to_format($from_pindah_box);
		$stockToTime = $this->date_to_format($to_pindah_box);
		
		$from_pindah_box = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_pindah_box = date("Y-m-d",$stockToTime).' 23:59:59';
		
		$data_pindah_box = $this->mp->get_filter_pindah_box($from_pindah_box,$to_pindah_box,$filter_category,$filter_box_from,$filter_box_to,$filter_karat);
		
		$data['view'] = '<table id="pindahBox-tablefilter" class="ui celled table" style="width:100%"><thead><tr><th style="width:50px">No</th><th>ID Pindah Box</th><th>Nama Barang</th><th>Karat</th><th>Dari Box</th><th>Ke Box</th><th>Berat</th><th>Tgl Pindah</th></tr></thead><tbody>';
		
		$number = 1;
		$total_pcs = 0;
		$total_gram = 0;
		foreach($data_pindah_box as $d){
			$tanggal_pindah_box = strtotime($d->trans_date);
			$tanggal_pindah_box = date('d-M-y',$tanggal_pindah_box);
			
			$box_number_from = '';
			$totalnumberlength = 3;
			$numberlength = strlen($d->id_box_from);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$box_number_from .= '0';
				}
			}
			
			$box_number_from .= $d->id_box_from;
			
			$box_number_to = '';
			$totalnumberlength = 3;
			$numberlength = strlen($d->id_box_to);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$box_number_to .= '0';
				}
			}
			
			$box_number_to .= $d->id_box_to;
			
			$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$d->id.'</td><td>'.$d->product_name.'</td><td>'.$d->karat_name.'</td><td class="center aligned">'.$box_number_from.'</td><td class="center aligned">'.$box_number_to.'</td><td class="right aligned">'.number_format($d->product_weight, 2).'</td><td>'.$tanggal_pindah_box.'</td></tr>';
			
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
				
				$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$tanggal_pindah_box.'</td><td>'.$c->id.'</td><td class="center aligned">'.$box_number_from.'</td><td class="center aligned">'.$box_number_to.'</td>';
				
				$number = $number + 1;
			}
			
			$data['view'] .= '</tbody></table></div></div>';
			
			$data['val_filter'] = 'Y';
			$data['success'] = FALSE;
			echo json_encode($data);
			exit();
		}
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
	
	public function pindah_masal(){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		
		$tgl_pindah_box = date("Y-m-d").' 00:00:00';
		$box_penampung = '31';
		
		$this->mp->update_box_temp('2',$box_penampung);
		
		//6 KE 2
		$id_box_from = '6';
		$id_box_to = '2';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//8 KE 6
		$id_box_from = '8';
		$id_box_to = '6';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//15 KE 8
		$id_box_from = '15';
		$id_box_to = '8';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//27 KE 15
		$id_box_from = '27';
		$id_box_to = '15';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//10 KE 3
		$id_box_from = '10';
		$id_box_to = '3';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//14 KE 10
		$id_box_from = '14';
		$id_box_to = '10';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//16 KE 14
		$id_box_from = '16';
		$id_box_to = '14';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//31=2 KE 16
		$id_box_from = '31';
		$id_box_to = '16';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = '2';
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//5 KE 4
		$id_box_from = '5';
		$id_box_to = '4';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//9 KE 5
		$id_box_from = '9';
		$id_box_to = '5';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//13 KE 9
		$id_box_from = '13';
		$id_box_to = '9';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//12 KE 13
		$id_box_from = '12';
		$id_box_to = '13';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//19 KE 12
		$id_box_from = '19';
		$id_box_to = '12';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//22 KE 19
		$id_box_from = '22';
		$id_box_to = '19';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//28 KE 22
		$id_box_from = '28';
		$id_box_to = '22';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//20 KE 11
		$id_box_from = '20';
		$id_box_to = '11';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//23 KE 20
		$id_box_from = '23';
		$id_box_to = '20';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//24 KE 23
		$id_box_from = '24';
		$id_box_to = '23';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//21 KE 18
		$id_box_from = '21';
		$id_box_to = '18';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		//26 KE 21
		$id_box_from = '26';
		$id_box_to = '21';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function pindah_lp(){
		$this->db->trans_start();
		
		$tgl_pindah_box = date("Y-m-d").' 00:00:00';
		
		$id_box_from = '19';
		$id_box_to = '27';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		$id_box_from = '21';
		$id_box_to = '19';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		$id_box_from = '20';
		$id_box_to = '21';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		$id_box_from = '27';
		$id_box_to = '20';
		
		$data_product = $this->mp->get_product_pindah($id_box_from);
		
		foreach($data_product as $dp){
			$id_karat = $dp->id_karat;
			$id_box = $dp->id_box;
			$product_id = $dp->id;
			
			$pindah_box_id = 'PB-'.$product_id.'-';
			
			$transnumber = $this->mp->get_trans_number_pindah_box($product_id);
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			$totalnumberlength = 3;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$pindah_box_id .= '0';
				}
			}
			
			$pindah_box_id .= $numbertrans;
			
			$created_by = $this->session->userdata('gold_username');
			
			$this->mp->insert_pindah_box($pindah_box_id,$product_id,$id_karat,$id_box,$id_box_to,$tgl_pindah_box,$created_by);
			$this->mp->update_product_pindah_box($product_id,$id_box_to);
		}
		
		$this->db->trans_complete();
	}
}
