<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MutasiGram extends CI_Controller{
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->model('M_karat','mk');
		$this->load->model('M_product','mp');
		$this->load->model('M_pos','mt');
		$this->load->model('M_mutasi','mm');
		$this->load->model('M_kelompok_barang','mc');
	}
	
	public function index(){
		$account = $this->mm->get_coa_mas_header();
		$account2 = $this->mm->get_coa_mas_content();
		$karat = $this->mk->get_karat_srt();
		
		$data['view'] = '<div class="ui container fluid">
			<div class="ui pointing secondary menu">
				<a class="item active" data-tab="mutasiGram-first" style="width:50%" onkeyup=entToAction("mutasiGram")>
					<i class="edit icon"></i> Input Mutasi Emas (Gram)
				</a>
				<a class="item" data-tab="mutasiGram-second" style="width:50%" onclick=filterTransaksi("mutasiGram")>
					<i class="list ol icon"></i> List Mutasi Emas (Gram)
				</a>
			</div>
			<div class="ui bottom attached tab segment active" data-tab="mutasiGram-first" onkeyup=entToAction("mutasiGram")>
				<div class="ui inverted dimmer" id="mutasiGram-loaderinput">
					<div class="ui large text loader">Loading</div>
				</div>
				<form  class="ui form form-javascript" id="mutasiGram-form" action="'.base_url().'index.php/mutasiGram/save" method="post">
				<div class="ui grid">
					<div class="four wide column" style="padding-bottom:0">
						<div class="field">
							<label>Jenis Transaksi</label>
							<select class="custom-select select-filter" name="mutasiGram-jenis_trans" id="mutasiGram-jenis_trans" onkeydown=entToNextID("mutasiGram-account_number")>
								<option value="I">Emas Masuk</option>
								<option value="O">Emas Keluar</option>
							</select>
						</div>
					</div>
					<div class="four wide column" style="padding-bottom:0">
						<div class="field">
							<label>Account</label>
							<div id="mutasiGram-account_number_wrap">
								<select class="custom-select select-filter" name="mutasiGram-account_number" id="mutasiGram-account_number" onkeydown=entToNextID("mutasiGram-dateinput")>';
									
									foreach($account as $k){
									$data['view'] .= '<option value="'.$k->accountnumber.'">'.$k->accountnumber.' - '.$k->accountname.'</option>';
									}
								$data['view'] .= '</select>
							</div>
						</div>
					</div>
					<div class="right floated four wide column" style="padding-bottom:0">
						<div class="field">
							<label>Tanggal Mutasi</label>
							<div id="mutasiGram-wrap_tanggal_stock_in">
								<input type="text" name="mutasiGram-dateinput" id="mutasiGram-dateinput" readonly onkeydown=entToNextID("mutasiGram-input_1_1") onchange=entToNextID("mutasiGram-input_1_1")>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0">
						<div class="ui red message" id="mutasiGram-wraperror" style="display:none"></div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0" id="mutasiGram-wrap_isi_data">
						<table id="mutasiGram-pos_data_tabel" class="ui celled table table-input" cellspacing="0" width="100%" style="margin-top:15px;">
							<thead>
								<tr class="center aligned">
									<th style="width:30px;">No</th>
									<th style="width:250px;">Account</th>
									<th style="width:100px;">Karat</th>
									<th>Keterangan</th>
									<th style="width:200px;">Jumlah</th>
								</tr>
							</thead>
							<tbody id="mutasiGram-pos_body">
								<tr id="mutasiGram-pos_tr_1">
									<td class="center aligned">1</td>
									<td>
										<select class="form-pos" onkeydown=entToTabInput("mutasiGram","1","1") name="mutasiGram-id_account_1" id="mutasiGram-input_1_1">
											<option value="">-- Pilih Account --</option>';
											foreach($account2 as $k){
											$data['view'] .= '<option value="'.$k->accountnumber.'">'.$k->accountnumber.' - '.$k->accountname.'</option>';
											}
										$data['view'] .= '</select>
									</td>
									<td>
										<select class="form-pos" onkeydown=entToTabInput("mutasiGram","1","2") name="mutasiGram-id_karat_1" id="mutasiGram-input_1_2">
											<option value="">-- Karat --</option>';
											foreach($karat as $k){
											$data['view'] .= '<option value="'.$k->id.'">'.$k->karat_name.'</option>';
											}
										$data['view'] .= '</select>
									</td>
									<td>
										<input class="form-pos" onkeydown=entToTabInput("mutasiGram","1","3") type="text" name="mutasiGram-keterangan_1" id="mutasiGram-input_1_3">
									</td>
									<td>
										<input class="form-pos" type="text" name="mutasiGram-jumlah_1" id="mutasiGram-input_1_4" onkeyup=valueToCurrency("mutasiGram","mutasiGram-input_1_4","Total")>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="ui positive right floated labeled icon button" id="mutasiGram-btn" onclick=saveTransaksi("mutasiGram")>
							<i class="save icon"></i> Simpan
						</div>
					</div>
					<div class="eight wide column">
						<div class="ui grid" style="padding-top:30px;padding-bottom:15px">
							<div class="four wide column ket-bawah" style="padding-bottom:0;padding-top:0">F5</div>
							<div class="twelve wide column ket-bawah" style="padding-bottom:0;padding-top:0">: REFRESH HALAMAN</div>
							<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">Insert</div>
							<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: TAMBAH BARIS</div>
							<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">Home</div>
							<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: KURANG BARIS</div>
						</div>
					</div>
					<div class="eight wide column">
						<div class="ui grid" style="padding-top:30px;padding-bottom:15px">
							<div class="eight wide column ket-bawah right aligned" style="padding-bottom:0;padding-top:0">TOTAL (IDR)</div>
							<div class="eight wide column ket-bawah right aligned" id="mutasiGram-total" style="padding-bottom:0;padding-top:0"></div>
						</div>
					</div>
				</div>
				</form>
			</div>
			<div class="ui bottom attached tab segment" data-tab="mutasiGram-second">
				<div class="ui inverted dimmer" id="mutasiGram-loaderlist">
					<div class="ui large text loader">Loading</div>
				</div>
				<form class="ui form form-javascript" id="mutasiGram-form-filter" action="'.base_url().'index.php/mutasiGram/filter/" method="post">
				<div class="ui grid">
					<div class="fourteen wide centered column" style="padding-bottom:0">
						<div class="fields">
							<div class="three wide field">
								<label>Tgl Mutasi</label>
								<input type="text" name="mutasiGram-filterfromdate" id="mutasiGram-filterfromdate" readonly>
							</div>
							<div class="one wide field" style="text-align:center;margin-top:30px">
								<label>s.d</label>
							</div>
							<div class="three wide field">
								<label style="visibility:hidden">-</label>
								<input type="text" name="mutasiGram-filtertodate" id="mutasiGram-filtertodate" readonly>
							</div>
							<div class="three wide field">
								<label>Jenis Transaksi</label>
								<select name="mutasiGram-filter_jenis" id="mutasiGram-filter_jenis">
									<option value="All">-- All --</option>
									<option value="In">Emas Masuk</option>
									<option value="Out">Emas Keluar</option>
								</select>
							</div>
							<div class="four wide field">
								<label>Karat</label>
								<select name="mutasiGram-filter_karat" id="mutasiGram-filter_karat">
									<option value="All">-- All --</option>';
									foreach($karat as $k){
									$data['view'] .= '<option value="'.$k->id.'">'.$k->karat_name.'</option>';
									}
								$data['view'] .= '</select>
							</div>
							<div class="two wide field">
								<label style="visibility:hidden">-</label>
								<div class="ui fluid icon green button filter-input" id="mutasiGram-btnfilter" onclick=filterTransaksi("mutasiGram") title="Filter">
									<i class="filter icon"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" id="mutasiGram-wrap_filter" style="padding-top:0">
					</div>
				</div>
				</form>
			</div>
		</div>';
		
		$data["date"] = 3;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function tambah_baris($row_number){
		$row_number = $row_number + 1;
		
		$data['view'] = '<tr id="mutasiGram-pos_tr_'.$row_number.'"><td class="center aligned">'.$row_number.'</td><td><select class="form-control form-control-sm form-pos" onkeydown=entToTabInput("mutasiGram","'.$row_number.'","1") name="mutasiGram-id_account_'.$row_number.'" id="mutasiGram-input_'.$row_number.'_1")><option value="">-- Pilih Account --</option>';
		
		$data_account = $this->mm->get_coa_mas_content();
		
		foreach($data_account as $da){
			$data['view'] .= '<option value="'.$da->accountnumber.'">'.$da->accountnumber.' - '.$da->accountname.'</option>';
		}
		
		$data['view'] .= '</select></td><td><select class="form-control form-control-sm form-pos" onkeydown=entToTabInput("mutasiGram","'.$row_number.'","2") name="mutasiGram-id_karat_'.$row_number.'" id="mutasiGram-input_'.$row_number.'_2"><option value="">-- Karat --</option>';
		
		$data_karat = $this->mk->get_karat_sdr();
		
		foreach($data_karat as $da){
			$data['view'] .= '<option value="'.$da->id.'">'.$da->karat_name.'</option>';
		}
		
		$data['view'] .= '</select></td><td><input class="form-control form-control-sm form-pos" onkeydown=entToTabInput("mutasiGram","'.$row_number.'","3") type="text" name="mutasiGram-keterangan_'.$row_number.'" id="mutasiGram-input_'.$row_number.'_3"></td><td><input class="form-control form-control-sm form-pos" type="text" name="mutasiGram-jumlah_'.$row_number.'" id="mutasiGram-input_'.$row_number.'_4" onkeyup=valueToCurrency("mutasiGram","mutasiGram-input_'.$row_number.'_4","Total")></td></tr>';
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function save($row_number){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		$this->validate($row_number);
		
		$tanggal_transaksi = $this->input->post('mutasiGram-dateinput');
		$tglTrans = $this->date_to_format($tanggal_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		
		$jenis_trans = $this->input->post('mutasiGram-jenis_trans');
		
		if($jenis_trans == 'I'){
			$to_account = $this->input->post('mutasiGram-account_number');
			$tipe = 'In';
			$kode = 'I';
		}else{
			$from_account = $this->input->post('mutasiGram-account_number');
			$tipe = 'Out';
			$kode = 'O';
		}
		
		$tgl_code = $tgl_transaksi;
		$codetrans = strtotime($tgl_code);
		$codetrans = date('ymd',$codetrans);
		
		$transactioncode = 'M'.$kode;
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
		
		for($i = 1; $i <= $row_number; $i++){
			if($jenis_trans == 'I'){
				$from_account = $this->input->post('mutasiGram-id_account_'.$i);
			}else{
				$to_account = $this->input->post('mutasiGram-id_account_'.$i);
			}
			
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
			
			$id_karat = $this->input->post('mutasiGram-id_karat_'.$i);
			$keterangan = $this->input->post('mutasiGram-keterangan_'.$i);
			$jumlah = $this->input->post('mutasiGram-jumlah_'.$i);
			$jumlah = str_replace(',','',$jumlah);
			
			$this->mm->insert_mutasi_gram($sitecode,$id_trans,$tipe,$id_karat,$from_account,$to_account,$jumlah,$keterangan,$tgl_transaksi,$created_by);
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
		
		for($i = 1; $i <= $row_number; $i++){
			$id_account = $this->input->post('mutasiGram-id_account_'.$i);
			$id_karat = $this->input->post('mutasiGram-id_karat_'.$i);
			$keterangan = $this->input->post('mutasiGram-keterangan_'.$i);
			$jumlah = $this->input->post('mutasiGram-jumlah_'.$i);
			
			if($id_account == ''){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Account Harus Diisi!</li>';
				echo json_encode($data);
				exit();
			}
			
			if($id_karat == ''){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Karat Harus Diisi!</li>';
				echo json_encode($data);
				exit();
			}
			
			if($keterangan == ''){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Keterangan Harus Diisi!</li>';
				echo json_encode($data);
				exit();
			}
			
			if($jumlah == '' || $jumlah == 0 ){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Jumlah Harus Diisi & Tidak Boleh Bernilai 0!</li>';
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
		
		$filter_jenis = $this->input->post('mutasiGram-filter_jenis');
		if($filter_jenis == 'All'){
			$filter_jenis = '"Out","In"';
		}else{
			$filter_jenis = '"'.$filter_jenis.'"';
		}
		
		$filter_karat = $this->input->post('mutasiGram-filter_karat');
		if($filter_karat == 'All'){
			$filter_karat = '';
			$data_karat = $this->mk->get_karat_sdr();
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
		
		$from_date = $this->input->post('mutasiGram-filterfromdate');
		$to_date = $this->input->post('mutasiGram-filtertodate');
		
		$stockFromTime = $this->date_to_format($from_date);
		$stockToTime = $this->date_to_format($to_date);
		
		$from_date = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_date = date("Y-m-d",$stockToTime).' 00:00:00';
		
		$data_filter = $this->mm->get_filter_mutasi_mas($from_date,$to_date,$filter_jenis,$filter_karat);
		
		$data['view'] = '<table id="mutasiGram-tablefilter" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:50px">No</th><th>Tgl Mutasi</th><th>Jenis Transaksi</th><th>Dari Account</th><th>Ke Account</th><th>Karat</th><th>Keterangan</th><th>Jumlah</th><th>Act</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_filter as $d){
			$jenis = '';
			
			if($d->tipemutasi == 'In'){
				$jenis = 'Emas Masuk';
			}else if($d->tipemutasi == 'Out'){
				$jenis = 'Emas Keluar';
			}
			
			$tanggal_tulis = strtotime($d->transdate);
			$tanggal_tulis = date('d-M-y',$tanggal_tulis);
			
			$act = '';
			if($this->session->userdata('gold_admin') == 'Y' || $this->session->userdata('gold_pembukuan') == 'Y' || $this->session->userdata('gold_manager') == 'Y'){
				$act = '<button type="button" class="ui mini red icon button" onclick=deleteTransMutasi("mutasiGram","'.$d->idmutasi.'") title="Delete"><i class="ban icon"></i></button>';
			}
			
			$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$tanggal_tulis.'</td><td>'.$jenis.'</td><td>'.$d->from_acc_name.'</td><td>'.$d->to_acc_name.'</td><td>'.$d->karat_name.'</td><td>'.$d->description.'</td><td class="right aligned">'.number_format($d->value, 3).'</td><td class="center aligned">'.$act.'</td></tr>';
			
			$number = $number + 1;
		}
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function hapus($id){
		if($this->session->userdata('gold_admin') == 'Y' || $this->session->userdata('gold_pembukuan') == 'Y' || $this->session->userdata('gold_manager') == 'Y'){
			date_default_timezone_set("Asia/Jakarta");
			$this->db->trans_start();
			
			$mutasi_data = $this->mm->get_mutasi_gr_by_id($id);
			$deletedby = $this->session->userdata('gold_nama_user');
			
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
				$this->mm->delete_mutasi_gram($id);
			}
			
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
}

