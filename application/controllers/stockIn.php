<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StockIn extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->library('M_pdf');
		$this->load->model('M_karat','mk');
		$this->load->model('M_box','mb');
		$this->load->model('M_kelompok_barang','mc');
		$this->load->model('M_product','mp');
		$this->load->model('M_nama_barang','mmp');
		$this->load->model('M_mutasi','mm');
	}
	
	public function index(){
		$sitecode = $this->mm->get_site_code();
		
		$product_id = '00'.$sitecode.''.date('y').'-';
		
		$transnumber = $this->mp->get_trans_number();
		$countnumber = count($transnumber);
		
		if($countnumber == 0){
			$numbertrans = 0;
		}else{
			$numArray = explode('-', $transnumber[0]->id);
			$numbertrans = $numArray[1];
			$numbertrans = (int)$numbertrans;
		}
		
		$numbertrans = $numbertrans + 1;
		$totalnumberlength = 10;
		$numberlength = strlen($numbertrans);
		$numberspace = $totalnumberlength - $numberlength;
		if($numberspace != 0){
			for ($i = 1; $i <= $numberspace; $i++){
				$product_id .= '0';
			}
		}
		
		$product_id .= $numbertrans;
		
		$id_urutan = $product_id;
		$karat = $this->mk->get_karat_srt();
		$box = $this->mb->get_box_aktif();
		$category = $this->mc->get_product_category();
		$from = $this->mp->get_product_from();
		$from_filter = $this->mp->get_product_from_filter();
		
		$data['view'] = '<div class="ui container fluid">
			<div class="ui pointing secondary menu">
				<a class="item active" data-tab="stockIn-first" style="width:50%" onkeyup=entToInsert("stockIn")>
					<i class="edit icon"></i> Input Data Stock In
				</a>
				<a class="item" data-tab="stockIn-second" style="width:50%" onclick=filterPersediaan("stockIn")>
					<i class="list ol icon"></i> List Data Stock In
				</a>
			</div>
			<div class="ui bottom attached tab segment active" data-tab="stockIn-first" onkeyup=entToInsert("stockIn")>
				<div class="ui inverted dimmer" id="stockIn-loaderinput">
					<div class="ui large text loader">Loading</div>
				</div>
				<form class="ui form form-javascript" id="stockIn-form" action="'.base_url().'index.php/stockIn/insert" method="post">
				<div class="ui grid">
					<div class="left floated four wide column" style="padding-bottom:0">
						<div class="field">
							<label>ID Barang</label>
							<input type="text" id="stockIn-idurut" name="stockIn-idurut" value="'.$id_urutan.'" readonly>
						</div>
					</div>
					<div class="left floated two wide column" style="padding-left:0;padding-right:0;padding-bottom:0">
						<div class="field">
							<label style="visibility:hidden">ID Barang</label>
							<div class="ui labeled icon button" onclick=addForm("stockIn")><i class="add icon"></i>Nama Barang</div>
						</div>
					</div>
					<div class="right floated ten wide column" style="padding-bottom:0">
						<div class="fields">
							<div class="four wide field">
								<label>Asal Barang</label>
								<div id="stockIn-wrap-select-from">
									<select name="stockIn-from" id="stockIn-from" onchange=getKetPersediaan("stockIn") onkeydown=entToNextID("stockIn-ket-select")>
										<option value="">-- Asal Barang --</option>';
										
										foreach($from as $f){
										$data['view'] .= '<option value="'.$f->id.'">'.$f->from_name.'</option>';
										}
									$data['view'] .= '</select>
								</div>
							</div>
							<div class="six wide field">
								<label>Keterangan</label>
								<input type="text" name="stockIn-ket-select" id="stockIn-ket-select" readonly onkeydown=entToNextID("stockIn-dateinput")>
							</div>
							<div class="six wide field">
								<label>Tanggal Stock In</label>
								<div id="stockIn-wrap-tanggal-input">
									<input type="text" name="stockIn-dateinput" id="stockIn-dateinput" readonly onkeydown=entToNextID("stockIn-input_1_1-selectized") onchange=entToNextID("stockIn-input_1_1-selectized")>
								</div>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0">
						<div class="ui red message" id="stockIn-wraperror" style="display:none"></div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0">
						<table id="stockIn-tableinput" class="ui celled table table-input" cellspacing="0" width="100%" style="margin-top:15px;">
							<thead>
								<tr class="center aligned">
									<th style="width:50px;">No</th>
									<th style="width:80px;">Karat</th>
									<th style="width:80px;">Box</th>
									<th>Kelompok</th>
									<th style="width:280px;">Nama Barang</th>
									<th style="width:120px;">Berat</th>
									<th>ID Barang</th>
									<th>X</th>
								</tr>
							</thead>
							<tbody id="stockIn-pos_body">
								<tr id="stockIn-pos_tr_1">
									<td class="center aligned">1</td>
									<td>
										<div id="stockIn-wrap_id_karat_1">
											<select class="select-form" onchange=entToTabInput("stockIn","1","1") name="stockIn-id_karat_1" id="stockIn-input_1_1">
												<option value=""></option>';
												foreach($karat as $k){
												$data['view'] .= '<option value="'.$k->id.'">'.$k->karat_name.'</option>';
												}
											$data['view'] .= '</select>
										</div>
									</td>
									<td>
										<div id="stockIn-wrap_id_box_1">
											<select class="select-form" onchange=entToTabInput("stockIn","1","2") name="stockIn-id_box_1" id="stockIn-input_1_2">
												<option value=""></option>';
												foreach($box as $b){
													$data['view'] .= '<option value="'.$b->id.'">'.$b->nama_box.'</option>';
												}
											$data['view'] .= '</select>
										</div>
									</td>
									<td>
										<div id="stockIn-wrap_id_category_1">
											<select class="select-form" name="stockIn-id_category_1" id="stockIn-input_1_3" onchange=getMasterProduct("stockIn","1","3")>
												<option value=""></option>';
												foreach($category as $c){
													$data['view'] .= '<option value="'.$c->id.'">'.$c->category_name.'</option>';
												}
											$data['view'] .= '</select>
										</div>
									</td>
									<td>
										<div id="stockIn-wrap_nama_barang_1">
											<select class="select-form" class="" name="stockIn-nama_barang_1" id="stockIn-input_1_4">
												<option value=""></option>
											</select>
										</div>
									</td>
									<td>
										<input class="form-pos" type="text" name="stockIn-berat_1" id="stockIn-input_1_5" onkeyup=valueToCurrency("stockIn","stockIn-input_1_5","Total") autocomplete="off">
									</td>
									<td id="stockIn-input_1_6"></td>
									<td class="center aligned" id="stockIn-input_1_7">
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
							<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">F4</div>
							<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: KEMBALI KE KARAT</div>
							<div class="four wide column ket-bawah" style="padding-bottom:0;padding-top:0">F5</div>
							<div class="twelve wide column ket-bawah" style="padding-bottom:0;padding-top:0">: REFRESH HALAMAN</div>
						</div>
					</div>
					<div class="eight wide column">
						<div class="ui grid" style="padding-top:30px;padding-bottom:15px">
							<div class="eight wide column ket-bawah right aligned" style="padding-bottom:0;padding-top:0">TOTAL (Gram)</div>
							<div class="eight wide column ket-bawah right aligned" id="stockIn-total" style="padding-bottom:0;padding-top:0">0</div>
						</div>
					</div>
				</div>
				</form>
			</div>
			<div class="ui bottom attached tab segment" data-tab="stockIn-second">
				<div class="ui inverted dimmer" id="stockIn-loaderlist">
					<div class="ui large text loader">Loading</div>
				</div>
				<form class="ui form form-javascript" id="stockIn-form-filter" action="'.base_url().'index.php/stockIn/filter" method="post">
				<div class="ui grid">
					<div class="sixteen wide column" style="padding-bottom:0">
						<div class="fields">
							<div class="two wide field">
								<label>Kelompok Barang</label>
								<select name="stockIn-filter_category" id="stockIn-filter_category">
									<option value="All">-- All --</option>';
									foreach($category as $c){
									$data['view'] .= '<option value="'.$c->id.'">'.$c->category_name.'</option>';
									}
								$data['view'] .= '</select>
							</div>
							<div class="two wide field">
								<label>Asal Barang</label>
								<select name="stockIn-filter_from" id="stockIn-filter_from">
									<option value="All">-- All --</option>';
									foreach($from_filter as $f){
									$data['view'] .= '<option value="'.$f->id.'">'.$f->from_name.'</option>';
									}
								$data['view'] .= '</select>
							</div>
							<div class="two wide field">
								<label>Box</label>
								<select name="stockIn-filter_box" id="stockIn-filter_box">
									<option value="All">-- All --</option>';
									foreach($box as $b){
									$data['view'] .= '<option value="'.$b->id.'">BOX '.$b->nama_box.'</option>';
									}
								$data['view'] .= '</select>
							</div>
							<div class="two wide field">
								<label>Karat</label>
								<select name="stockIn-filter_karat" id="stockIn-filter_karat">
									<option value="All">-- All --</option>';
									foreach($karat as $k){
									$data['view'] .= '<option value="'.$k->id.'">'.$k->karat_name.'</option>';
									}
								$data['view'] .= '</select>
							</div>
							<div class="three wide field">
								<label>Tgl Stock In</label>
								<input type="text" name="stockIn-filterfromdate" id="stockIn-filterfromdate" readonly>
							</div>
							<div class="one wide field" style="text-align:center;margin-top:30px">
								<label>s.d</label>
							</div>
							<div class="three wide field">
								<label style="visibility:hidden">-</label>
								<input type="text" name="stockIn-filtertodate" id="stockIn-filtertodate" readonly>
							</div>
							<div class="one wide field">
								<label style="visibility:hidden">-</label>
								<div class="ui fluid icon green button filter-input" id="stockIn-btnfilter" onclick=filterPersediaan("stockIn") title="Filter">
									<i class="filter icon"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" id="stockIn-wrap_filter" style="padding-top:0"></div>
					<div class="six wide column">
						<div class="ui grid" style="padding-bottom:40px">
							<div class="eight wide column ket-bawah left aligned" style="padding-bottom:0;padding-top:0">TOTAL (Pcs)</div>
							<div class="eight wide column ket-bawah right aligned" id="stockIn-filter-pcs" style="padding-bottom:0;padding-top:0">0 Pcs</div>
						</div>
					</div>
					<div class="four wide column">
					</div>
					<div class="six wide column">
						<div class="ui grid" style="padding-bottom:40px">
							
							<div class="eight wide column ket-bawah right aligned" style="padding-bottom:0;padding-top:0">TOTAL (Gram)</div>
							<div class="eight wide column ket-bawah right aligned" id="stockIn-filter-gram" style="padding-bottom:0;padding-top:0">0.000</div>
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
	
	public function add(){
		$category = $this->mc->get_product_category();
		
		$data['view'] = '<i class="close icon"></i><div class="header">Tambah Nama Barang</div><div class="content"><div class="ui error message" id="stockIn-modalwraperror" style="display:none"></div>';
		
		$data['view'] .= '<form class="ui form form-javascript" id="stockIn-addedit" action="'.base_url().'index.php/stockIn/save_addedit" method="post"><div class="field"><input type="text" id="stockIn-name" name="stockIn-name" placeholder="Masukkan Nama Barang" style="text-transform:uppercase" autocomplete="off" onkeyup=entToNextID("stockIn-select-category-selectized")></div><div class="field"><select id="stockIn-select-category" name="stockIn-select-category"><option value="">-- Pilih Kelompok Barang --</option>';
		
		foreach($category as $c){
			$data['view'] .= '<option value="'.$c->id.'">'.$c->category_name.'</option>';
		}
		
		$data['view'] .= '</select></div></form></div><div class="actions"><button id="stockIn-btnadd" class="ui green labeled icon button" onclick=saveAddEdit("stockIn","Input")>Simpan	<i class="save icon"></i></button></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function save_addedit($flag = 0){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		
		$this->validate_modal($flag);
		
		$nama_barang = $this->input->post('stockIn-name');
		$nama_barang = strtoupper($nama_barang);
		$select_category = $this->input->post('stockIn-select-category');
		$created_date = date("Y-m-d").' 00:00:00';
		$created_by = $this->session->userdata('gold_nama_user');
		
		if($flag == 'Input'){
			$this->mmp->insert_master_product($nama_barang,$select_category,$created_date,$created_by);
		}else if($flag == 'Update'){
			$id = $this->input->post('stockIn-id');
			$this->mmp->update_master_product($id, $nama_barang, $select_category);
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
	
	public function get_ket_from($id_from){
		$from_data = $this->mp->get_product_from_detail($id_from);
		if($from_data[0]->from_name == 'LAIN-LAIN'){
			$data['edit'] = TRUE;
			$data['ketvalue'] = $from_data[0]->from_desc;
		}else{
			$data['edit'] = FALSE;
			$data['ketvalue'] = $from_data[0]->from_desc;
		}
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function get_master_product($id_category,$id_row){
		$master_barang = $this->mmp->get_master_by_category($id_category);
		$data['view'] = '<select name="stockIn-nama_barang_'.$id_row.'" id="stockIn-input_'.$id_row.'_4"><option value="">-- Nama Barang --</option>';
		
		foreach($master_barang as $mb){
			$data['view'] .= '<option value="'.$mb->nama_barang.'">'.$mb->nama_barang.'</option>';
		}
		
		$data['view'] .= '</select>';
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function insert($id_row){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		$this->validate($id_row);
		
		$tgl_stock_in = $this->input->post('stockIn-dateinput');
		$tanggal_stock_in = $this->input->post('stockIn-dateinput');
		$select_from = $this->input->post('stockIn-from');
		$select_from_desc = $this->input->post('stockIn-ket-select');
		
		$select_karat = $this->input->post('stockIn-id_karat_'.$id_row);
		$select_box = $this->input->post('stockIn-id_box_'.$id_row);
		$select_category = $this->input->post('stockIn-id_category_'.$id_row);
		$nama_barang = $this->input->post('stockIn-nama_barang_'.$id_row);
		$berat_barang = $this->input->post('stockIn-berat_'.$id_row);
		$berat_barang = str_replace(',','',$berat_barang);
		
		$dateArray = explode(' ', $tgl_stock_in);
		$transTanggal = $dateArray[0];
		$transMonth = $dateArray[1];
		$transTahun = $dateArray[2];
			
		switch($transMonth){
			case "January":
				$transBulan = '1';
				break;
			case "February":
				$transBulan = '2';
				break;
			case "March":
				$transBulan = '3';
				break;
			case "April":
				$transBulan = '4';
				break;
			case "May":
				$transBulan = '5';
				break;
			case "June":
				$transBulan = '6';
				break;
			case "July":
				$transBulan = '7';
				break;
			case "August":
				$transBulan = '8';
				break;
			case "September":
				$transBulan = '9';
				break;
			case "October":
				$transBulan = '10';
				break;
			case "November":
				$transBulan = '11';
				break;
			case "December":
				$transBulan = '12';
				break;
		}
			
		$reportTime = strtotime($transBulan.'/'.$transTanggal.'/'.$transTahun);
		$tgl_stock_in = date("Y-m-d",$reportTime).' 00:00:00';
		
		$sitecode = $this->mm->get_site_code();
		
		$stock_in_id = 'SI-00'.$sitecode.''.date('y').'-';
		$product_id = '00'.$sitecode.''.date('y').'-';
		
		$transnumber = $this->mp->get_trans_number();
		$countnumber = count($transnumber);
		
		if($countnumber == 0){
			$numbertrans = 0;
		}else{
			$numArray = explode('-', $transnumber[0]->id);
			$numbertrans = $numArray[1];
			$numbertrans = (int)$numbertrans;
		}
		
		$numbertrans = $numbertrans + 1;
		$totalnumberlength = 10;
		$numberlength = strlen($numbertrans);
		$numberspace = $totalnumberlength - $numberlength;
		if($numberspace != 0){
			for ($i = 1; $i <= $numberspace; $i++){
				$stock_in_id .= '0';
				$product_id .= '0';
			}
		}
		
		$stock_in_id .= $numbertrans;
		$product_id .= $numbertrans;
		
		$created_by = $this->session->userdata('gold_nama_user');
		$id_lama = 'NULL';
		
		$this->mp->insert_stock_in($stock_in_id,$select_karat,$select_box,$select_category,$select_from,$select_from_desc,$nama_barang,$berat_barang,$tgl_stock_in,$created_by);
		$this->mp->insert_product($product_id,$id_lama,$select_karat,$select_box,$select_category,$select_from,$select_from_desc,$nama_barang,$berat_barang,$tgl_stock_in,$created_by);
		
		$account_srt = $this->mm->get_default_account('SRT');
		$account_pjg = $this->mm->get_default_account('PJG');
		
		$desc = 'STOCK IN - NO.REG : '.$product_id;
		
		$this->mm->insert_mutasi_gram($sitecode,$stock_in_id,'In',$select_karat,$account_srt,$account_pjg,$berat_barang,$desc,$tgl_stock_in,$created_by);
		
		$this->db->trans_complete();
		
		$karat = $this->mk->get_karat_srt();
		$box = $this->mb->get_box_aktif();
		$category = $this->mc->get_product_category();
		$from_name = $this->mp->get_product_from_detail($select_from);
		foreach($from_name as $fn){
			$select_from_name = $fn->from_name;
		}
		
		$data['select_from'] = '<input type="text" name="select_from_val" id="select_from_val" value="'.$select_from_name.'" readonly onkeydown=entToHeader("input_1_1")><input type="hidden" name="stockIn-from" id="stockIn-from" value="'.$select_from.'">';
		
		$data['tanggal_stock_in'] = '<input type="text" name="stockIn-dateinput" id="stockIn-dateinput" value="'.$tanggal_stock_in.'" readonly>';
		
		$data['nama_barang'] = $nama_barang;
		
		$data['product_id'] = $product_id;
		$data['button_messsage'] = '<div class="ui tiny icon green button"><i class="check circle icon"></i></div>';
		
		$id_row = $id_row + 1;
		$data['view'] = '<tr id="stockIn-pos_tr_'.$id_row.'"><td class="center aligned">'.$id_row.'</td><td><div id="stockIn-wrap_id_karat_'.$id_row.'"><select onchange=entToTabInput("stockIn","'.$id_row.'","1") name="stockIn-id_karat_'.$id_row.'" id="stockIn-input_'.$id_row.'_1"><option value=""></option>';
		
		foreach($karat as $k){
			$selected = '';
			if($k->id == $select_karat){
				$selected = 'selected';
			}
			$data['view'] .= '<option value="'.$k->id.'" '.$selected.'>'.$k->karat_name.'</option>';
		}
		
		$data['view'] .= '</select></div></td><td><div id="stockIn-wrap_id_box_'.$id_row.'"><select onchange=entToTabInput("stockIn","'.$id_row.'","2") name="stockIn-id_box_'.$id_row.'" id="stockIn-input_'.$id_row.'_2"><option value=""></option>';
		
		foreach($box as $b){
			$selected = '';
			if($b->id == $select_box){
				$selected = 'selected';
			}
			$data['view'] .= '<option value="'.$b->id.'" '.$selected.'>'.$b->nama_box.'</option>';
		}
																						
		$data['view'] .= '</select></div></td><td><div id="stockIn-wrap_id_category_'.$id_row.'"><select name="stockIn-id_category_'.$id_row.'" id="stockIn-input_'.$id_row.'_3" onchange=getMasterProduct("stockIn","'.$id_row.'","3")><option value=""></option>';
		
		foreach($category as $c){
			$selected = '';
			if($c->id == $select_category){
				$selected = 'selected';
			}
			$data['view'] .= '<option value="'.$c->id.'" '.$selected.'>'.$c->category_name.'</option>';
		}
		
		
		$data['view'] .= '</select></div></td><td><div id="stockIn-wrap_nama_barang_'.$id_row.'"><select name="stockIn-nama_barang_'.$id_row.'" id="stockIn-input_'.$id_row.'_4">';
		
		$master_barang = $this->mmp->get_master_by_category($select_category);
		$data['view'] .= '<option value="">-- Nama Barang --</option>';
		
		foreach($master_barang as $mb){
			$selected = '';
			if($mb->nama_barang == $nama_barang){
				$selected = 'selected';
			}
			$data['view'] .= '<option value="'.$mb->nama_barang.'" '.$selected.'>'.$mb->nama_barang.'</option>';
		}
		
		$data['view'] .= '</select></div></td>';
		
		$data['view'] .= '<td><input class="form-pos" type="text" name="stockIn-berat_'.$id_row.'" id="stockIn-input_'.$id_row.'_5" onkeyup=valueToCurrency("stockIn","stockIn-input_'.$id_row.'_5","Total") autocomplete="off" value=""></td><td id="stockIn-input_'.$id_row.'_6"></td><td class="center aligned" id="stockIn-input_'.$id_row.'_7"><div class="ui tiny icon google plus button"><i class="ban icon"></i></div></td></tr>';
		
		$product_id = '00'.$sitecode.''.date('y').'-';
		
		$transnumber = $this->mp->get_trans_number();
		$countnumber = count($transnumber);
		
		if($countnumber == 0){
			$numbertrans = 0;
		}else{
			$numArray = explode('-', $transnumber[0]->id);
			$numbertrans = $numArray[1];
			$numbertrans = (int)$numbertrans;
		}
		
		$numbertrans = $numbertrans + 1;
		$totalnumberlength = 10;
		$numberlength = strlen($numbertrans);
		$numberspace = $totalnumberlength - $numberlength;
		if($numberspace != 0){
			for ($i = 1; $i <= $numberspace; $i++){
				$product_id .= '0';
			}
		}
		
		$product_id .= $numbertrans;
		
		$box_number = '';
		$totalnumberlength = 3;
		$numberlength = strlen($select_box);
		$numberspace = $totalnumberlength - $numberlength;
		if($numberspace != 0){
			for ($i = 1; $i <= $numberspace; $i++){
				$box_number .= '0';
			}
		}
		
		$box_number .= $select_box;
		$data_cat = $this->mc->get_category_by_id($select_category);
		
		$data['select_box'] = $box_number;
		$data['select_karat'] = $this->mk->get_karat_name_by_id($select_karat);
		$data['select_category'] = $data_cat[0]->category_name;
		
		$data['id_urutan'] = $product_id;
		$data['success'] = true;
		echo json_encode($data);
	}
	
	private function validate($id_row){
		$data = array();
		$data['inputerror'] = '<ul class="list"></ul>';
		$data['success'] = TRUE;
		$data['error_message'] = 'Data Belum Lengkap!<br>';
		
		$tanggal_stock_in = $this->input->post('stockIn-dateinput');
		$select_from = $this->input->post('stockIn-from');
		$select_from_desc = $this->input->post('stockIn-ket-select');
		
		if($tanggal_stock_in == ''){
			$data['inputerror'] .= '<li>Tanggal Stock In Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($select_from == ''){
			$data['inputerror'] .= '<li>Asal Barang Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($select_from_desc == ''){
			$data['inputerror'] .= '<li>Keterangan Asal Barang Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		$select_karat = $this->input->post('stockIn-id_karat_'.$id_row);
		$select_box = $this->input->post('stockIn-id_box_'.$id_row);
		$select_category = $this->input->post('stockIn-id_category_'.$id_row);
		$nama_barang = $this->input->post('stockIn-nama_barang_'.$id_row);
		$berat_barang = $this->input->post('stockIn-berat_'.$id_row);
		
		if($select_karat == ''){
			$data['inputerror'] .= '<li>Karat Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($select_box == ''){
			$data['inputerror'] .= '<li>Box Harus Diisi!</li>';
			$data['success'] = FALSE;
		}

		if($select_category == ''){
			$data['inputerror'] .= '<li>Kelompok Barang Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($nama_barang == ''){
			$data['inputerror'] .= '<li>Nama Barang Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($berat_barang == '' || $berat_barang == 'NaN' || $berat_barang == 0){
			$data['inputerror'] .= '<li>Berat Barang Tidak Boleh Bernilai 0 dan Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($data['success'] == FALSE){
			echo json_encode($data);
			exit();
		}
	}
	
	private function validate_modal($flag){
		$data = array();
		$data['inputerror'] = '<ul class="list"></ul>';
		$data['success'] = TRUE;
		
		$nama_barang = $this->input->post('stockIn-name');
		$nama_barang = strtoupper($nama_barang);
		
		$select_category = $this->input->post('stockIn-select-category');
		
		if($nama_barang == ''){
			$data['inputerror'] .= '<li>Nama Barang Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($select_category == ''){
			$data['inputerror'] .= '<li>Kelompok Barang Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($flag == 'Input'){
			$data_nama_barang = $this->mmp->cek_product_name($nama_barang,$select_category);
			if(count($data_nama_barang) > 0){
				$data['inputerror'] .= '<li>Nama Barang Sudah Ada!</li>';
				$data['success'] = FALSE;
			}
		}else{
			$id = $this->input->post('stockIn-id');
			
			$data_nama_barang = $this->mmp->cek_product_name($nama_barang,$select_category);
			if(count($data_nama_barang) > 0){
				$data_product = $this->mmp->get_barang_by_id($id);
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
	
	public function filter(){
		$this->db->trans_start();
		
		$filter_category = $this->input->post('stockIn-filter_category');
		$filter_category2 = $this->input->post('stockIn-filter_category');
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
		
		$filter_from = $this->input->post('stockIn-filter_from');
		$filter_from2 = $this->input->post('stockIn-filter_from');
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
		
		$filter_box = $this->input->post('stockIn-filter_box');
		$filter_box2 = $this->input->post('stockIn-filter_box');
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
		
		$filter_karat = $this->input->post('stockIn-filter_karat');
		$filter_karat2 = $this->input->post('stockIn-filter_karat');
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
		
		$from_stock_in = $this->input->post('stockIn-filterfromdate');
		$from_date = $this->input->post('stockIn-filterfromdate');
		$to_stock_in = $this->input->post('stockIn-filtertodate');
		$to_date = $this->input->post('stockIn-filtertodate');
		
		$stockFromTime = $this->date_to_format($from_stock_in);
		$stockToTime = $this->date_to_format($to_stock_in);
		
		$from_stock_in = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_stock_in = date("Y-m-d",$stockToTime).' 23:59:59';
		
		$data_stock_in = $this->mp->get_filter_stock_in($from_stock_in,$to_stock_in,$filter_category,$filter_from,$filter_box,$filter_karat);
		
		$data['view'] = '<div class="sixteen wide centered column full-print" style="padding-top:25px;padding-bottom:5px;text-align:right"><a class="ui facebook button" href="'.base_url().'index.php/stockIn/pdf/'.$from_date.'/'.$to_date.'/'.$filter_category2.'/'.$filter_from2.'/'.$filter_box2.'/'.$filter_karat2.'" target=_blank"><i class="file pdf outline icon"></i> Download</a></div><table id="stockIn-tablefilter" class="ui celled table" style="width:100%"><thead><tr><th style="width:40px">No</th><th>ID Stock In</th><th>Nama Barang</th><th>Kelompok</th><th>Asal</th><th>Krt</th><th>Box</th><th>Berat</th><th>Tgl Masuk</th></tr></thead><tbody>';
		
		$number = 1;
		$total_pcs = 0;
		$total_gram = 0;
		foreach($data_stock_in as $d){
			$tanggal_stock_in = strtotime($d->trans_date);
			$tanggal_stock_in = date('d-M-y',$tanggal_stock_in);
			
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
			
			$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$d->id.'</td><td>'.$d->product_name.'</td><td>'.$d->category_name.'</td><td>'.$d->from_name.'</td><td>'.$d->karat_name.'</td><td class="center aligned">'.$box_number.'</td><td class="right aligned">'.number_format($d->product_weight, 3).'</td><td>'.$tanggal_stock_in.'</td></tr>';
			
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
	
	public function pdf($from_stock_in,$to_stock_in,$filter_category,$filter_from,$filter_box,$filter_karat){
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
		
		$from_stock_in = str_replace('%20',' ',$from_stock_in);
		$to_stock_in = str_replace('%20',' ',$to_stock_in);
		
		$from_date = $from_stock_in;
		$to_date = $to_stock_in;
		
		if($from_date == $to_date){
			$tanggal_tulis = $from_date;
		}else{
			$tanggal_tulis = $from_date.' S/D '.$to_date;
		}
		
		$stockFromTime = $this->date_to_format($from_stock_in);
		$stockToTime = $this->date_to_format($to_stock_in);
		
		$from_stock_in = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_stock_in = date("Y-m-d",$stockToTime).' 23:59:59';
		
		$data_stock_in = $this->mp->get_filter_stock_in($from_stock_in,$to_stock_in,$filter_category,$filter_from,$filter_box,$filter_karat);
		
		$site_name = $this->mm->get_site_name();
		
		$data['view'] = '<div class="sixteen wide centered column center aligned" style="font-weight:bold;text-align:center;font-size:13px;font-family:samsungone"><span style="text-align">Laporan Stock In, Cabang '.$site_name.'</span><br><span>Tanggal '.$tanggal_tulis.'</span><br><span>'.$tulis_category.', '.$tulis_from.', '.$tulis_box.', '.$tulis_karat.'</span><br><span style="visibility:hidden">-</span></div><table class="lap_pdf_6" style="width:100%"><thead><tr><th style="width:40px" class="th-5">No</th><th class="th-5">ID Stock In</th><th class="th-5">Nama Barang</th><th class="th-5">Kelompok</th><th class="th-5">Asal</th><th class="th-5">Krt</th><th class="th-5">Box</th><th class="th-5">Berat</th><th class="th-5">Tgl Masuk</th></tr></thead><tbody>';
		
		$number = 1;
		$total_pcs = 0;
		$total_gram = 0;
		foreach($data_stock_in as $d){
			$tanggal_stock_in = strtotime($d->trans_date);
			$tanggal_stock_in = date('d-M-y',$tanggal_stock_in);
			
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
			
			$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$d->id.'</td><td>'.$d->product_name.'</td><td>'.$d->category_name.'</td><td>'.$d->from_name.'</td><td>'.$d->karat_name.'</td><td class="center aligned">'.$box_number.'</td><td class="right aligned" style="text-align:right">'.number_format($d->product_weight, 3).'</td><td style="text-align:center">'.$tanggal_stock_in.'</td></tr>';
			
			$total_pcs = $total_pcs + 1;
			$total_gram = $total_gram + $d->product_weight;
			$number = $number + 1;
		}
		
		$data['total_pcs'] = $total_pcs;
		$data['total_gram'] = number_format($total_gram, 3);
		$data['view'] .= '<tr><td colspan="5" style="text-align:right;border-top:1px solid #000;border-bottom:1px solid #000;font-weight:bold">TOTAL</td><td colspan="2" style="border-top:1px solid #000;border-bottom:1px solid #000;font-weight:bold">'.number_format($total_pcs,0).' PCS</td><td style="text-align:right;border-top:1px solid #000;border-bottom:1px solid #000;font-weight:bold">'.number_format($total_gram,3).'</td><td style="text-align:right;border-top:1px solid #000;border-bottom:1px solid #000;font-weight:bold"></td>';
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
