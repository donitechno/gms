<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransCabang extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->model('M_karat','mk');
		$this->load->model('M_product','mp');
		$this->load->model('M_mutasi','mm');
		$this->load->model('M_pos','mt');
		$this->load->model('M_kelompok_barang','mc');
	}
	
	public function index(){
		$repgros = $this->mm->get_repgros_account();
		$karat = $this->mk->get_karat_sdr();
		
		$data['view'] = '<div class="ui container fluid">
			<div class="ui pointing secondary menu">
				<a class="item active" data-tab="transCabang-first" style="width:50%" onkeyup=entToAction("transCabang")>
					<i class="edit icon"></i> Input Mutasi Emas Antar Cabang
				</a>
				<a class="item" data-tab="transCabang-second" style="width:50%" onclick=filterTransaksi("transCabang")>
					<i class="list ol icon"></i> List Mutasi Emas Antar Cabang
				</a>
			</div>
			<div class="ui bottom attached tab segment active" data-tab="transCabang-first" onkeyup=entToAction("transCabang")>
				<div class="ui inverted dimmer" id="transCabang-loaderinput">
					<div class="ui large text loader">Loading</div>
				</div>
				<form  class="ui form form-javascript" id="transCabang-form" action="'.base_url().'index.php/transCabang/save" method="post">
				<div class="ui grid">
					<div class="sixteen wide column" style="padding-bottom:0">
						<div class="fields">
							<div class="four wide field">
								<label>Account Cabang</label>
								<div id="transCabang-account_number_wrap">
									<select name="transCabang-account_number" id="transCabang-account_number" onkeydown=entToNextID("transCabang-jenis_trans") onchange=getTableForm("transCabang")>';
										foreach($repgros as $k){
										$data['view'] .= '<option value="'.$k->accountnumber.'">'.$k->accountname.'</option>';
										}
									$data['view'] .= '</select>
								</div>
							</div>
							<div class="four wide field">
								<label>Jenis Transaksi</label>
								<select class="custom-select select-filter" name="transCabang-jenis_trans" id="transCabang-jenis_trans" onkeydown=entToNextID("transCabang-keterangan")>
									<option value="O">Pengiriman ke Cabang</option>
									<option value="I">Penerimaan dari Cabang</option>
								</select>
							</div>
							<div class="four wide field">
								<label>Keterangan</label>
								<input type="text" name="transCabang-keterangan" id="transCabang-keterangan" onkeydown=entToNextID("transCabang-dateinput") autocomplete="off">
							</div>
							<div class="four wide field">
								<label>Tanggal Mutasi</label>
								<input type="text" name="transCabang-dateinput" id="transCabang-dateinput" readonly onkeydown=entToNextID("transCabang-input_1_1") onchange=entToNextID("transCabang-input_1_1")>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0">
						<div class="ui red message" id="transCabang-wraperror" style="display:none"></div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0" id="transCabang-wrap_isi_data">
						
					</div>
					<div class="eight wide column">
						<div class="ui grid" style="padding-top:30px;padding-bottom:15px">
							<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">F5</div>
							<div class="twelve wide column ket-bawah" style="padding-bottom:0;padding-top:0">: REFRESH HALAMAN</div>
							<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">Insert</div>
							<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: TAMBAH BARIS</div>
							<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">Home</div>
							<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: KURANG BARIS</div>
						</div>
					</div>
					<div class="eight wide column">
						<div class="ui grid" style="padding-top:30px;padding-bottom:15px">
							<div class="eight wide column ket-bawah right aligned" style="padding-bottom:0;padding-top:0">TOTAL KONVERSI (Gram)</div>
							<div class="eight wide column ket-bawah right aligned" id="transCabang-total" style="padding-bottom:0;padding-top:0"></div>
							<div class="eight wide column ket-bawah right aligned" style="padding-bottom:0;padding-top:20px" id="transCabang-total_taksirspan">TOTAL TAKSIRAN (Gram)</div>
							<div class="eight wide column ket-bawah right aligned" id="transCabang-total_taksir" style="padding-bottom:0;padding-top:20px">0</div>
						</div>
					</div>
				</div>
				</form>
			</div>
			<div class="ui bottom attached tab segment" data-tab="transCabang-second">
				<div class="ui inverted dimmer" id="transCabang-loaderlist">
					<div class="ui large text loader">Loading</div>
				</div>
				<form class="ui form form-javascript" id="transCabang-form-filter" action="'.base_url().'index.php/transCabang/filter/" method="post">
				<div class="ui grid">
					<div class="sixteen wide centered column" style="padding-bottom:0">
						<div class="fields">
							<div class="three wide field">
								<label>Tgl Mutasi</label>
								<input type="text" class="form-control input-filter" name="transCabang-filterfromdate" id="transCabang-filterfromdate" readonly>
							</div>
							<div class="one wide field" style="text-align:center;margin-top:30px">
								<label>s.d</label>
							</div>
							<div class="three wide field">
								<label style="visibility:hidden">-</label>
								<input type="text" class="form-control input-filter" name="transCabang-filtertodate" id="transCabang-filtertodate" readonly>
							</div>
							<div class="five wide field">
								<label>Account Cabang</label>
								<select class="custom-select select-filter" name="transCabang-filter_cabang" id="transCabang-filter_cabang">
									<option value="R">DEPARTEMEN REPARASI</option>
									<option value="G">DEPARTEMEN PENGADAAN</option>
									<option value="T">TITIPAN DI PENGADAAN</option>
								</select>
							</div>
							<div class="five wide field">
								<label>Jenis Transaksi</label>
								<select class="custom-select select-filter" name="transCabang-filter_jenis" id="transCabang-filter_jenis">
									<option value="All">-- All Transaksi --</option>
									<option value="In">Pengiriman ke Cabang</option>
									<option value="Out">Penerimaan dari Cabang</option>
								</select>
							</div>
							<div class="two wide field">
								<label style="visibility:hidden">-</label>
								<div class="ui fluid icon green button filter-input" id="transCabang-btnfilter" onclick=filterTransaksi("transCabang") title="Filter">
									<i class="filter icon"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" id="transCabang-wrap_filter" style="padding-top:0">
					</div>
				</div>
				</form>
			</div>
		</div>';
		
		$data['viewRep'] = '<table id="transCabang-pos_data_tabel" class="ui celled table table-input" cellspacing="0" width="100%" style="margin-top:15px;"><thead><tr class="center aligned"><th style="width:30px;">No</th><th style="width:100px;">Karat</th><th style="width:100px">Gram Real</th><th style="width:100px">Gram Konversi 24K</th><th style="width:100px;">Persentase</th></tr></thead><tbody id="transCabang-pos_body"><tr id="transCabang-pos_tr_1"><td class="center aligned">1</td><td><select class="form-pos" onkeydown=entToTabInput("transCabang","1","1") onchange=kaliPersentase("transCabang","1") name="transCabang-id_karat_1" id="transCabang-input_1_1"><option value="">-- Pilih Karat --</option>';
			
		foreach($karat as $k){
		$data['viewRep'] .= '<option value="'.$k->id.'">'.$k->karat_name.'</option>';
		}
	
		$data['viewRep'] .= '</select></td><td><input class="form-pos" type="text" name="transCabang-real_gram_1" id="transCabang-input_1_2" onkeydown=entToTabInput("transCabang","1","2") onkeyup=valueToCurrency("transCabang","transCabang-input_1_2","Total") autocomplete="off"></td><td><input class="form-pos" type="text" name="transCabang-konv_gram_1" id="transCabang-input_1_3" onkeydown=entToTabInput("transCabang","1","3") onkeyup=valueToCurrency("transCabang","transCabang-input_1_3","Total") autocomplete="off"></td><td><input class="form-pos" type="text" name="transCabang-persentase_1" id="transCabang-input_1_4" readonly></td></tr></tbody></table><div class="ui positive right floated labeled icon button" id="transCabang-btn" onclick=saveTransaksi("transCabang")><i class="save icon"></i> Simpan</div>';
		
		$data['viewGros'] = '<table id="transCabang-pos_data_tabel" class="ui celled table table-input" cellspacing="0" width="100%" style="margin-top:15px;"><thead><tr class="center aligned"><th style="width:30px;">No</th><th style="width:100px;">Karat</th><th style="width:100px">Gram Real</th><th style="width:100px">Gram Konversi 24K</th></tr></thead><tbody id="transCabang-pos_body"><tr id="transCabang-pos_tr_1"><td class="center aligned">1</td><td><select class="form-pos" onkeydown=entToTabInput("transCabang","1","1") name="transCabang-id_karat_1" id="transCabang-input_1_1"><option value="">-- Pilih Karat --</option>';
	
		foreach($karat as $k){
		$data['viewGros'] .= '<option value="'.$k->id.'">'.$k->karat_name.'</option>';
		}
		
		$data['viewGros'] .= '</select></td><td><input class="form-pos" type="text" name="transCabang-real_gram_1" id="transCabang-input_1_2" onkeyup=valueToCurrency("transCabang","transCabang-input_1_2","Total") autocomplete="off"></td><td class="kosong-bg"><input class="form-pos form-taksir" type="text" name="transCabang-taksir_1"  id="transCabang-input_1_3" style="width:50%" onkeyup=valueToCurrencyCabang("transCabang","transCabang-input_1_3","transCabang-input_1_4","transCabang-input_1_2")><input class="form-pos form-taksir" type="text" name="transCabang-taksirpersen_1"  id="transCabang-input_1_4" style="width:50%" readonly></td></tr></tbody><tfoot><th></th><th></th><th style="font-weight:bold;text-align:right">Total Konversi</th><th style="padding:0px !important;"><input class="form-pos" type="text" name="transCabang-total_konversi" id="transCabang-input_1_9" onkeyup=valueToCurrency("transCabang","transCabang-input_1_9","Total") autocomplete="off"></th></tfoot></table><div class="ui positive right floated labeled icon button" id="transCabang-btn" onclick=saveTransaksi("transCabang")><i class="save icon"></i> Simpan</div>';
		
		$data["date"] = 3;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function tambah_baris($id_dept,$row_number){
		$row_number = $row_number + 1;
		
		if($id_dept == 'R'){
			$data['view'] = '<tr id="transCabang-pos_tr_'.$row_number.'"><td class="center aligned">'.$row_number.'</td><td><select class="form-pos" onkeydown=entToTabInput("transCabang","'.$row_number.'","1") onchange=kaliPersentase("transCabang","'.$row_number.'") name="transCabang-id_karat_'.$row_number.'" id="transCabang-input_'.$row_number.'_1"><option value="">-- Pilih Karat --</option>';
		
			$karat = $this->mk->get_karat_sdr();
			
			foreach($karat as $k){
				$data['view'] .= '<option value="'.$k->id.'">'.$k->karat_name.'</option>';
			}
			
			$data['view'] .= '</select></td><td><input class="form-pos" type="text" name="transCabang-real_gram_'.$row_number.'" id="transCabang-input_'.$row_number.'_2" onkeydown=entToTabInput("transCabang","'.$row_number.'","2") onkeyup=valueToCurrency("transCabang","transCabang-input_'.$row_number.'_2","Total") autocomplete="off"></td><td><input class="form-pos" type="text" name="transCabang-konv_gram_'.$row_number.'" id="transCabang-input_'.$row_number.'_3" onkeydown=entToTabInput("transCabang","'.$row_number.'","3") onkeyup=valueToCurrency("transCabang","transCabang-input_'.$row_number.'_3","Total") autocomplete="off"></td><td><input class="form-pos" type="text" name="transCabang-persentase_'.$row_number.'" id="transCabang-input_'.$row_number.'_4" readonly></td></tr>';
		}else if($id_dept == 'P' || $id_dept == 'T'){
			$data['view'] = '<tr id="transCabang-pos_tr_'.$row_number.'"><td class="center aligned">'.$row_number.'</td><td><select class="form-pos" onkeydown=entToTabInput("transCabang","'.$row_number.'","1") name="transCabang-id_karat_'.$row_number.'" id="transCabang-input_'.$row_number.'_1"><option value="">-- Pilih Karat --</option>';
			
			$karat = $this->mk->get_karat_sdr();
			
			foreach($karat as $k){
				$data['view'] .= '<option value="'.$k->id.'">'.$k->karat_name.'</option>';
			}
			
			$data['view'] .= '</select></td><td><input class="form-pos" type="text" name="transCabang-real_gram_'.$row_number.'" id="transCabang-input_'.$row_number.'_2" onkeyup=valueToCurrency("transCabang","transCabang-input_'.$row_number.'_2","Total") autocomplete="off"></td><td class="kosong-bg"><input class="form-pos form-taksir" type="text" name="transCabang-taksir_'.$row_number.'"  id="transCabang-input_'.$row_number.'_3" style="width:50%" onkeyup=valueToCurrencyCabang("transCabang","transCabang-input_'.$row_number.'_3","transCabang-input_'.$row_number.'_4","transCabang-input_'.$row_number.'_2")><input class="form-pos form-taksir" type="text" name="transCabang-taksirpersen_'.$row_number.'"  id="transCabang-input_'.$row_number.'_4" style="width:50%" readonly></td></tr>';
		}
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function save($row_number){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		$this->validate($row_number);
		
		$tanggal_transaksi = $this->input->post('transCabang-dateinput');
		$tglTrans = $this->date_to_format($tanggal_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		
		$keterangan = $this->input->post('transCabang-keterangan');
		$account_number = $this->input->post('transCabang-account_number');
		if($account_number == '17-0002'){
			$dept = 'R';
			$iddept = 'X';
		}else if($account_number == '17-0003'){
			$dept = 'G';
			$iddept = 'Y';
		}else if($account_number == '17-0005'){
			$dept = 'T';
			$iddept = 'Z';
		}
		
		$jenis_trans = $this->input->post('transCabang-jenis_trans');
		
		if($jenis_trans == 'I'){
			$from_account = $account_number;
			$to_account = $this->mm->get_default_account('SRT');
			$tipe = 'In';
			$code = 'I';
		}else{
			$from_account = $this->mm->get_default_account('SRT');
			$to_account = $account_number;
			$tipe = 'Out';
			$code = 'O';
		}
		
		$tgl_code = $tgl_transaksi;
		$codetrans = strtotime($tgl_code);
		$codetrans = date('ymd',$codetrans);
		
		$transactioncode = $iddept.''.$code;
		$sitecode = $this->mm->get_site_code();
		
		$totalnumberlength = 3;
		$numberlength = strlen($sitecode);
		$numberspace = $totalnumberlength - $numberlength;
		if($numberspace != 0){
			for ($i = 1; $i <= $numberspace; $i++){
				$transactioncode .= '0';
			}
		}
		
		$transactioncode .= $sitecode.'-'.$codetrans.'-';
		$created_by = $this->session->userdata('gold_nama_user');
		
		$id_urut = $this->mm->get_mutasi_code_gr($transactioncode);
		$id_trans = $transactioncode;
		$totalnumberlength = 3;
		$numberlength = strlen($id_urut);
		$numberspace = $totalnumberlength - $numberlength;
		if($numberspace != 0){
			for ($a = 1; $a <= $numberspace; $a++){
				$id_trans .= '0';
			}
		}
		
		$id_trans .= $id_urut;
		
		$karat = $this->mk->get_karat_sdr();
		$real_val = array();
		$konv_val = array();
		
		foreach($karat as $k){
			$real_val[$k->id] = 0;
			$konv_val[$k->id] = 0;
		}
		
		for($i = 1; $i <= $row_number; $i++){
			$id_karat = $this->input->post('transCabang-id_karat_'.$i);
			$real_gram = $this->input->post('transCabang-real_gram_'.$i);
			$real_gram = str_replace(',','',$real_gram);
			$real_gram = (float) $real_gram;
			
			if($dept == 'R'){
				$konv_gram = $this->input->post('transCabang-konv_gram_'.$i);
				$konv_gram = str_replace(',','',$konv_gram);
				$konv_gram = (float) $konv_gram;
			}else if($dept == 'G'){
				$konv_gram = 0;
			}else if($dept == 'T'){
				$konv_gram = 0;
			}
			
			$real_val[$id_karat] = $real_val[$id_karat] + $real_gram;
			$konv_val[$id_karat] = $konv_val[$id_karat] + $konv_gram;
			
			
			if($dept == 'R'){
				$persentase = $this->input->post('transCabang-persentase_'.$i);
				$persentase = str_replace('%','',$persentase);
			}else if($dept == 'G'){
				$persentase = 0;
			}else if($dept == 'T'){
				$persentase = 0;
			}
			
			$this->mm->insert_transaksi_cabang($id_trans,$dept,$tipe,$keterangan,$id_karat,$real_gram,$konv_gram,$persentase,$tgl_transaksi,$created_by);
			$this->mm->insert_mutasi_gram($sitecode,$id_trans.'-'.$i,$tipe,$id_karat,$from_account,$to_account,$real_gram,$keterangan,$tgl_transaksi,$created_by);
		}
		
		if($tipe == 'In'){
			$tipe = 'Out';
		}else{
			$tipe = 'In';
		}
		
		$from_buy = 'N';
		
		if($dept == 'R'){
			$dua_empat = 1;
			$semsanam = 3;
			$juhlima = 4;
			$juhtus = 5;
			
			$dua_empat_real = $real_val[$dua_empat];
			$semsanam_real = $real_val[$semsanam];
			$juhlima_real = $real_val[$juhlima];
			$juhtus_real = $real_val[$juhtus];
			
			$dua_empat_konv = $konv_val[$dua_empat];
			$semsanam_konv = $konv_val[$semsanam];
			$juhlima_konv = $konv_val[$juhlima];
			$juhtus_konv = $konv_val[$juhtus];
			
			$total_konv_sdr = $dua_empat_konv + $semsanam_konv + $juhlima_konv + $juhtus_konv;
			
			$this->mt->input_main_reparasi($id_trans,$from_buy,$from_account,$to_account,$tipe,$keterangan,$dua_empat_real,$dua_empat_konv,$semsanam_real,$semsanam_konv,$juhlima_real,$juhlima_konv,$juhtus_real,$juhtus_konv,$total_konv_sdr,$tgl_transaksi,$created_by);
		}else if($dept == 'G'){
			$dua_empat = 1;
			$semsanam = 3;
			$juhlima = 4;
			$juhtus = 5;
			
			$dua_empat_real = $real_val[$dua_empat];
			$semsanam_real = $real_val[$semsanam];
			$juhlima_real = $real_val[$juhlima];
			$juhtus_real = $real_val[$juhtus];
			
			$total_konversi = $this->input->post('transCabang-total_konversi');
			$total_konversi = str_replace(',','',$total_konversi);
			$total_konversi = (float) $total_konversi;
			
			$this->mt->input_main_pengadaan($id_trans,$from_buy,$from_account,$to_account,$tipe,$keterangan,$dua_empat_real,$semsanam_real,$juhlima_real,$juhtus_real,$total_konversi,$tgl_transaksi,$created_by);
		}else if($dept == 'T'){
			$dua_empat = 1;
			$semsanam = 3;
			$juhlima = 4;
			$juhtus = 5;
			
			$dua_empat_real = $real_val[$dua_empat];
			$semsanam_real = $real_val[$semsanam];
			$juhlima_real = $real_val[$juhlima];
			$juhtus_real = $real_val[$juhtus];
			
			$total_konversi = $this->input->post('transCabang-total_konversi');
			$total_konversi = str_replace(',','',$total_konversi);
			$total_konversi = (float) $total_konversi;
			
			$this->mt->input_main_titip_pengadaan($id_trans,$from_buy,$from_account,$to_account,$tipe,$keterangan,$dua_empat_real,$semsanam_real,$juhlima_real,$juhtus_real,$total_konversi,$tgl_transaksi,$created_by);
		}
		
		$this->db->trans_complete();
		
		$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Input Berhasil!</div></div>';
		$data['success'] = true;
		echo json_encode($data);
	}
	
	private function validate($row_number){
		$data = array();
		$data['inputerror'] = '<ul class="list"></ul>';
		$data['success'] = TRUE;
		
		$keterangan = $this->input->post('transCabang-keterangan');
		if($keterangan == ''){
			$data['success'] = FALSE;
			$data['inputerror'] .= '<li>Keterangan Harus Diisi!</li>';
			echo json_encode($data);
			exit();
		}
		
		$account_number = $this->input->post('transCabang-account_number');
		if($account_number == '17-0002'){
			for($i = 1; $i <= $row_number; $i++){
				$id_karat = $this->input->post('transCabang-id_karat_'.$i);
				$real_gram = $this->input->post('transCabang-real_gram_'.$i);
				$real_gram = str_replace(',','',$real_gram);
				$real_gram = (float) $real_gram;
				
				$konv_gram = $this->input->post('transCabang-konv_gram_'.$i);
				$konv_gram = str_replace(',','',$konv_gram);
				$konv_gram = (float) $konv_gram;
				
				if($id_karat == ''){
					$data['success'] = FALSE;
					$data['inputerror'] .= '<li>Karat Harus Diisi!</li>';
					echo json_encode($data);
					exit();
				}
				
				if($real_gram == '' || $real_gram == 0){
					$data['success'] = FALSE;
					$data['inputerror'] .= '<li>Berat Real Harus Diisi & Tidak Boleh Bernilai 0!</li>';
					echo json_encode($data);
					exit();
				}
				
				if($konv_gram == '' || $konv_gram == 0 ){
					$data['success'] = FALSE;
					$data['inputerror'] .= '<li>Berat Konversi Harus Diisi & Tidak Boleh Bernilai 0!</li>';
					echo json_encode($data);
					exit();
				}
			}
		}else if($account_number == '17-0003' || $account_number == '17-0005'){
			for($i = 1; $i <= $row_number; $i++){
				$id_karat = $this->input->post('transCabang-id_karat_'.$i);
				$real_gram = $this->input->post('transCabang-real_gram_'.$i);
				$real_gram = str_replace(',','',$real_gram);
				$real_gram = (float) $real_gram;
				
				if($id_karat == ''){
					$data['success'] = FALSE;
					$data['inputerror'] .= '<li>Karat Harus Diisi!</li>';
					echo json_encode($data);
					exit();
				}
				
				if($real_gram == '' || $real_gram == 0){
					$data['success'] = FALSE;
					$data['inputerror'] .= '<li>Berat Real Harus Diisi & Tidak Boleh Bernilai 0!</li>';
					echo json_encode($data);
					exit();
				}
			}
			
			$total_konversi = $this->input->post('transCabang-total_konversi');
			$total_konversi = str_replace(',','',$total_konversi);
			$total_konversi = (float) $total_konversi;
			
			if($total_konversi == '' || $total_konversi == 0){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Total Konversi Harus Diisi & Tidak Boleh Bernilai 0!</li>';
				echo json_encode($data);
				exit();
			}
		}
		
		if($data['success'] == FALSE){
			echo json_encode($data);
			exit();
		}
	}
	
	public function filter(){
		$this->db->trans_start();
		
		$filter_jenis = $this->input->post('transCabang-filter_jenis');
		if($filter_jenis == 'All'){
			$filter_jenis = '"Out","In"';
		}else{
			$filter_jenis = '"'.$filter_jenis.'"';
		}
		
		$filter_cabang = $this->input->post('transCabang-filter_cabang');
		
		$from_date = $this->input->post('transCabang-filterfromdate');
		$to_date = $this->input->post('transCabang-filtertodate');
		
		$stockFromTime = $this->date_to_format($from_date);
		$stockToTime = $this->date_to_format($to_date);
		
		$from_date = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_date = date("Y-m-d",$stockToTime).' 00:00:00';
		
		if($filter_cabang == 'R'){
			$data_filter = $this->mm->get_filter_rep($from_date,$to_date,$filter_jenis);
		}else if($filter_cabang == 'G'){
			$data_filter = $this->mm->get_filter_gros($from_date,$to_date,$filter_jenis);
		}else if($filter_cabang == 'T'){
			$data_filter = $this->mm->get_filter_tip_gros($from_date,$to_date,$filter_jenis);
		}
		
		$data['view'] = '<table id="transCabang-tablefilter" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:50px">No</th><th>Tgl Trans</th><th>ID Pengiriman</th><th>Jenis</th><th>Keterangan</th><th>Total Konversi 24K</th><th></th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_filter as $d){
			if($d->tipe == 'In'){
				$jenis = 'Pengiriman';
			}else if($d->tipe == 'Out'){
				$jenis = 'Penerimaan';
			}
			
			$tanggal_tulis = strtotime($d->trans_date);
			$tanggal_tulis = date('d-M-y',$tanggal_tulis);
			
			$act = '<button type="button" class="ui mini primary icon button" onclick=viewTransCabang("transCabang","'.$d->id_pengiriman.'","'.$filter_cabang.'") title="Lihat Detail"><i class="eye icon"></i></button>';
			
			if($this->session->userdata('gold_admin') == 'Y' || $this->session->userdata('gold_pembukuan') == 'Y' || $this->session->userdata('gold_manager') == 'Y'){
				$act .= '<button type="button" class="ui mini red icon button" onclick=deleteTransCabang("transCabang","'.$d->id_pengiriman.'","'.$filter_cabang.'") title="Delete"><i class="ban icon"></i></button>';
			}
			
			$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$tanggal_tulis.'</td><td>'.$d->id_pengiriman.'</td><td>'.$jenis.'</td><td>'.$d->description.'</td><td class="right aligned">'.number_format($d->total_konv, 3).'</td><td class="center aligned">'.$act.'</td></tr>';
			
			$number = $number + 1;
		}
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function detail($id_trans,$id_dept){
		$this->db->trans_start();
		
		$data_detail = $this->mm->get_detail_tac($id_trans,$id_dept);
		
		if($id_dept == 'R'){
			$data['view'] = '<i class="close icon"></i><div class="header">'.$id_trans.'</div><div class="content"><table class="ui celled table" id="modal-table" style="width:100%"><thead class="center aligned"><tr><th>No</th><th>Karat</th><th>Gram Real</th><th>Gram Konversi 24</th><th>Persentase</th></tr></thead><tbody>';
		}else if($id_dept == 'G'){
			$data['view'] = '<i class="close icon"></i><div class="header">'.$id_trans.'</div><div class="content"><table class="ui celled table" id="modal-table" style="width:100%"><thead class="center aligned"><tr><th>No</th><th>Karat</th><th>Gram Real</th><th>Gram Konversi 24</th></tr></thead><tbody>';
		}else if($id_dept == 'T'){
			$data['view'] = '<i class="close icon"></i><div class="header">'.$id_trans.'</div><div class="content"><table class="ui celled table" id="modal-table" style="width:100%"><thead class="center aligned"><tr><th>No</th><th>Karat</th><th>Gram Real</th><th>Gram Konversi 24</th></tr></thead><tbody>';
		}
		
		$number = 1;
		$total_konversi = 0;
		if($id_dept =='G'){
			$total_konv = $this->mm->get_mutasi_pengadaan_byid($id_trans);
			$total_konversi = $total_konv[0]->total_konv;
		}else if($id_dept =='T'){
			$total_konv = $this->mm->get_mutasi_titip_pengadaan_byid($id_trans);
			$total_konversi = $total_konv[0]->total_konv;
		}
		
		foreach($data_detail as $d){
			if($id_dept == 'R'){
				$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$d->karat_name.'</td><td class="right aligned">'.number_format($d->berat_real, 3).'</td><td class="right aligned">'.number_format($d->berat_konversi, 3).'</td><td class="right aligned">'.number_format($d->persentase, 3).'</td></tr>';
				
				$number = $number + 1;
				$total_konversi = $total_konversi + $d->berat_konversi;
			}else if($id_dept == 'G' || $id_dept == 'T'){
				$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$d->karat_name.'</td><td class="right aligned">'.number_format($d->berat_real, 3).'</td><td class="kosong-bg"></td></tr>';
				
				$number = $number + 1;
			}
		}
		
		if($id_dept == 'R'){
			$data['view'] .= '</tbody><tfoot><tr><th></th><th></th><th style="font-weight:bold;text-align:right">Total Konversi</th><th class="right aligned" style="font-weight:bold;">'.number_format($total_konversi, 3).'</th><th></th></tr></tfoot></table></div>';
		}else if($id_dept == 'G' || $id_dept == 'T'){
			$data['view'] .= '</tbody><tfoot><tr><th></th><th></th><th style="font-weight:bold;text-align:right">Total Konversi</th><th class="right aligned" style="font-weight:bold;background:#FFF !important">'.number_format($total_konversi, 3).'</th></tr></tfoot></table></div>';
		}
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function hapus($id,$id_dept){
		if($this->session->userdata('gold_admin') == 'Y' || $this->session->userdata('gold_pembukuan') == 'Y' || $this->session->userdata('gold_manager') == 'Y'){
			date_default_timezone_set("Asia/Jakarta");
			$this->db->trans_start();
			
			$mutasi_data = $this->mm->get_mutasi_gr_like_id($id);
			$deletedby = $this->session->userdata('gold_username');
			
			foreach($mutasi_data as $md){
				$siteid = $md->idsite;
				$idmutasi = $md->idmutasi;
				$tipemutasi = $md->tipemutasi;
				$idkarat = $md->idkarat;
				$fromaccount = $md->fromaccount;
				$toaccount = $md->toaccount;
				$value = $md->value;
				$description = $md->description;
				$transdate = $md->transdate;
				$createddate = $md->createddate;
				$createdby = $md->createdby;
				
				$this->mm->insert_mutasi_gram_deleted($siteid,$idmutasi,$tipemutasi,$idkarat,$fromaccount,$toaccount,$value,$description,$transdate,$createddate,$createdby,$deletedby);
				$this->mm->delete_mutasi_gram($idmutasi);
			}
			
			if($id_dept == 'R'){
				$data_mutasi = $this->mm->get_mutasi_reparasi_byid($id);
				foreach($data_mutasi as $d){
					$id_pengiriman = $d->id_pengiriman;
					$from_buy = $d->from_buy;
					$tipe = $d->tipe;
					$fromaccount = $d->fromaccount;
					$toaccount = $d->toaccount;
					$description = $description;
					$dua_empat = $d->dua_empat;
					$dua_empat_konv = $d->dua_empat_konv;
					$semsanam = $d->semsanam;
					$semsanam_konv = $d->semsanam_konv;
					$juhlima = $d->juhlima;
					$juhlima_konv = $d->juhlima_konv;
					$juhtus = $d->juhtus;
					$juhtus_konv = $d->juhtus_konv;
					$total_konv = $d->total_konv;
					$trans_date = $d->trans_date;
				}
				
				$this->mt->hapus_main_reparasi($id_pengiriman);
				$this->mt->input_main_reparasi_hapus($id_pengiriman,$from_buy,$fromaccount,$toaccount,$tipe,$description,$dua_empat,$dua_empat_konv,$semsanam,$semsanam_konv,$juhlima,$juhlima_konv,$juhtus,$juhtus_konv,$total_konv,$trans_date,$deletedby);
			}else if($id_dept == 'G'){
				$data_mutasi = $this->mm->get_mutasi_pengadaan_byid($id);
				foreach($data_mutasi as $d){
					$id_pengiriman = $d->id_pengiriman;
					$from_buy = $d->from_buy;
					$tipe = $d->tipe;
					$fromaccount = $d->fromaccount;
					$toaccount = $d->toaccount;
					$description = $description;
					$dua_empat = $d->dua_empat;
					$semsanam = $d->semsanam;
					$juhlima = $d->juhlima;
					$juhtus = $d->juhtus;
					$total_konv = $d->total_konv;
					$trans_date = $d->trans_date;
				}
				
				$this->mt->hapus_main_pengadaan($id_pengiriman);
				$this->mt->input_main_pengadaan_hapus($id_pengiriman,$from_buy,$fromaccount,$toaccount,$tipe,$description,$dua_empat,$semsanam,$juhlima,$juhtus,$total_konv,$trans_date,$deletedby);
			}else if($id_dept == 'T'){
				$data_mutasi = $this->mm->get_mutasi_titip_pengadaan_byid($id);
				foreach($data_mutasi as $d){
					$id_pengiriman = $d->id_pengiriman;
					$from_buy = $d->from_buy;
					$tipe = $d->tipe;
					$fromaccount = $d->fromaccount;
					$toaccount = $d->toaccount;
					$description = $description;
					$dua_empat = $d->dua_empat;
					$semsanam = $d->semsanam;
					$juhlima = $d->juhlima;
					$juhtus = $d->juhtus;
					$total_konv = $d->total_konv;
					$trans_date = $d->trans_date;
				}
				
				$this->mt->hapus_main_titip_pengadaan($id_pengiriman);
				$this->mt->input_main_titip_pengadaan_hapus($id_pengiriman,$from_buy,$fromaccount,$toaccount,$tipe,$description,$dua_empat,$semsanam,$juhlima,$juhtus,$total_konv,$trans_date,$deletedby);
			}
			
			$this->mm->delete_trans_cabang($id);
			$this->db->trans_complete();
			
			$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Berhasil Hapus Data!</div></div>';
			
			$data['success'] = true;
			echo json_encode($data);
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
	
	public function date_to_format_2($tanggal_mentah){
		$dateArray = explode('%20', $tanggal_mentah);
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

