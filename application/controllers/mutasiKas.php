<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MutasiKas extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->model('M_karat','mk');
		$this->load->model('M_product','mp');
		$this->load->model('M_mutasi','mm');
		$this->load->model('M_kelompok_barang','mc');
	}
	
	public function index(){
		$kasbank = $this->mm->get_all_kasbank();
		$accountumum = $this->mm->get_account_umum();
		
		$data['view'] = '<div class="ui container fluid">
			<div class="ui pointing secondary menu">
				<a class="item active" data-tab="mutasiKas-first" style="width:50%" onkeyup=entToAction("mutasiKas")>
					<i class="edit icon"></i> Input Mutasi Kas/Bank
				</a>
				<a class="item" data-tab="mutasiKas-second" style="width:50%" onclick=filterTransaksi("mutasiKas")>
					<i class="list ol icon"></i> List Mutasi Kas/Bank
				</a>
			</div>
			<div class="ui bottom attached tab segment active" data-tab="mutasiKas-first" onkeyup=entToAction("mutasiKas")>
				<div class="ui inverted dimmer" id="mutasiKas-loaderinput">
					<div class="ui large text loader">Loading</div>
				</div>
				<form  class="ui form form-javascript" id="mutasiKas-form" action="'.base_url().'index.php/mutasiKas/save" method="post">
				<div class="ui grid">
					<div class="four wide column" style="padding-bottom:0">
						<div class="field">
							<label>Jenis Transaksi</label>
							<div id="mutasiKas-wrap_jenis_1">
								<select name="mutasiKas-jenis_1" id="mutasiKas-jenis_1" onchange=getAccountTrans("mutasiKas")>
									<option value="U">Transaksi Umum</option>
									<option value="K">Piutang Karyawan</option>
								</select>
							</div>
							<div id="mutasiKas-wrap_jenis_2">
								<select name="mutasiKas-jenis_2" id="mutasiKas-jenis_2" onchange=getAccountTrans("mutasiKas")>
									<option value="I">Penerimaan Kas/Bank</option>
									<option value="O">Pengeluaran Kas/Bank</option>
								</select>
							</div>
						</div>
					</div>
					<div class="four wide column" style="padding-bottom:0">
						<div class="field">
							<label>Account Kas/Bank</label>
							<div id="mutasiKas-account_number_wrap">
								<select name="mutasiKas-account_number" id="mutasiKas-account_number">';
									foreach($kasbank as $k){
									$data['view'] .= '<option value="'.$k->accountnumber.'">'.$k->accountnumber.' - '.$k->accountname.'</option>';
									}
								$data['view'] .= '</select>
							</div>
						</div>
					</div>
					<div class="right floated four wide column" style="padding-bottom:0">
						<div class="field">
							<label>Tanggal Mutasi</label>
							<div id="mutasiKas-wrap_tanggal_stock_in">
								<input type="text" name="mutasiKas-dateinput" id="mutasiKas-dateinput" readonly>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0">
						<div class="ui red message" id="mutasiKas-wraperror" style="display:none"></div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0" id="mutasiKas-wrap_isi_data">
						<table id="mutasiKas-tableinput" class="ui celled table table-input" cellspacing="0" width="100%" style="margin-top:15px;">
							<thead>
								<tr class="center aligned">
									<th style="width:30px;">No</th>
									<th style="width:250px;">Account</th>
									<th>Keterangan</th>
									<th style="width:200px;">Jumlah</th>
								</tr>
							</thead>
							<tbody id="mutasiKas-pos_body">
								<tr id="mutasiKas-pos_tr_1">
									<td class="center aligned">1</td>
									<td>
										<select class="form-pos" onkeydown=entToTabInput("mutasiKas","1","1") name="mutasiKas-id_account_1" id="mutasiKas-input_1_1" onchange=getKeteranganMutasi("mutasiKas","1")>
											<option value="">-- Pilih Account --</option>';
											foreach($accountumum as $k){
											$data['view'] .= '<option value="'.$k->accountnumber.'">'.$k->accountnumber.' - '.$k->accountname.'</option>';
											}
										$data['view'] .= '</select>
									</td>
									<td>
										<input class="form-pos" type="text" name="mutasiKas-keterangan_1" id="mutasiKas-input_1_2" onkeydown=entToTabInput("mutasiKas","1","2") autocomplete="off">
									</td>
									<td>
										<input class="form-pos" type="text" name="mutasiKas-jumlah_1" id="mutasiKas-input_1_3" onkeyup=valueToCurrencyRp("mutasiKas","mutasiKas-input_1_3","Total") autocomplete="off">
									</td>
								</tr>
							</tbody>
						</table>
						<div class="ui positive right floated labeled icon button" id="mutasiKas-btn" onclick=saveTransaksi("mutasiKas")>
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
							<div class="eight wide column ket-bawah right aligned" id="mutasiKas-total" style="padding-bottom:0;padding-top:0">0</div>
						</div>
					</div>
				</div>
				</form>
			</div>
			<div class="ui bottom attached tab segment" data-tab="mutasiKas-second">
				<div class="ui inverted dimmer" id="mutasiKas-loaderlist">
					<div class="ui large text loader">Loading</div>
				</div>
				<form class="ui form form-javascript" id="mutasiKas-form-filter" action="'.base_url().'index.php/mutasiKas/filter/" method="post">
				<div class="ui grid">
					<div class="fourteen wide centered column" style="padding-bottom:0">
						<div class="fields">
							<div class="three wide field">
								<label>Tgl Mutasi</label>
								<input type="text" class="form-control input-filter" name="mutasiKas-filterfromdate" id="mutasiKas-filterfromdate" readonly>
							</div>
							<div class="one wide field" style="text-align:center;margin-top:30px">
								<label>s.d</label>
							</div>
							<div class="three wide field">
								<label style="visibility:hidden">-</label>
								<input type="text" class="form-control input-filter" name="mutasiKas-filtertodate" id="mutasiKas-filtertodate" readonly>
							</div>
							<div class="three wide field">
								<label>Jenis Transaksi</label>
								<select class="custom-select select-filter" name="mutasiKas-filter_jenis_1" id="mutasiKas-filter_jenis_1">
									<option value="All">-- All --</option>
									<option value="U">Transaksi Umum</option>
									<option value="K">Piutang Karyawan</option>
								</select>
							</div>
							<div class="four wide field">
								<label style="visibility:hidden">-</label>
								<select class="custom-select select-filter" name="mutasiKas-filter_jenis_2" id="mutasiKas-filter_jenis_2">
									<option value="All">-- All --</option>
									<option value="I">Penerimaan Kas/Bank</option>
									<option value="O">Pengeluaran Kas/Bank</option>
								</select>
							</div>
							<div class="two wide field">
								<label style="visibility:hidden">-</label>
								<div class="ui fluid icon green button filter-input" id="mutasiKas-btnfilter" onclick=filterTransaksi("mutasiKas") title="Filter">
									<i class="filter icon"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" id="mutasiKas-wrap_filter" style="padding-top:0"></div>
				</div>
				</form>
			</div>
		</div>';
		
		$data["date"] = 3;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function get_account_data($jenis_trans = 0){
		$data['account'] = '<option value="">-- Pilih Account --</option>';
		if($jenis_trans == 'U'){
			$data_account = $this->mm->get_account_umum();
			$header_account = $this->mm->get_all_kasbank();
		}else if($jenis_trans == 'K'){
			$data_account = $this->mm->get_account_karyawan();
			$header_account = $this->mm->get_account_kas();
		}
		
		foreach($data_account as $da){
			$accountname = $da->accountname;
			$accountname = str_replace('PIUTANG KARYAWAN - ','',$accountname);
			$data['account'] .= '<option value="'.$da->accountnumber.'">'.$da->accountnumber.' - '.$accountname.'</option>';
		}
		
		$data['header'] = '';
		foreach($header_account as $ha){
			$data['header'] .= '<option value="'.$ha->accountnumber.'">'.$ha->accountnumber.' - '.$ha->accountname.'</option>';
		}
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function tambah_baris($row_number,$jenis_trans){
		$row_number = $row_number + 1;
		
		$data['view'] = '<tr id="mutasiKas-pos_tr_'.$row_number.'"><td class="center aligned">'.$row_number.'</td><td><select class="form-pos" onkeydown=entToTabInput("mutasiKas","'.$row_number.'","1") name="mutasiKas-id_account_'.$row_number.'" id="mutasiKas-input_'.$row_number.'_1"  onchange=getKeteranganMutasi("mutasiKas","'.$row_number.'")><option value="">-- Pilih Account --</option>';
		
		if($jenis_trans == 'U'){
			$data_account = $this->mm->get_account_umum();
		}else if($jenis_trans == 'K'){
			$data_account = $this->mm->get_account_karyawan();
		}
		
		foreach($data_account as $da){
			$accountname = $da->accountname;
			$accountname = str_replace('PIUTANG KARYAWAN - ','',$accountname);
			$data['view'] .= '<option value="'.$da->accountnumber.'">'.$da->accountnumber.' - '.$accountname.'</option>';
		}
		
		$data['view'] .= '</select></td><td><input class="form-pos" type="text" name="mutasiKas-keterangan_'.$row_number.'" id="mutasiKas-input_'.$row_number.'_2" onkeydown=entToTabInput("mutasiKas","'.$row_number.'","2") autocomplete="off"></td><td><input class="form-pos" type="text" name="mutasiKas-jumlah_'.$row_number.'" id="mutasiKas-input_'.$row_number.'_3" onkeyup=valueToCurrencyRp("mutasiKas","mutasiKas-input_'.$row_number.'_3","Total") autocomplete="off"></td></tr>';
								
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function save($row_number){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		$this->validate($row_number);
		
		$tanggal_transaksi = $this->input->post('mutasiKas-dateinput');
		$tglTrans = $this->date_to_format($tanggal_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		
		$jenis_trans1 = $this->input->post('mutasiKas-jenis_1');
		$jenis_trans = $this->input->post('mutasiKas-jenis_2');
		
		if($jenis_trans == 'I'){
			$to_account = $this->input->post('mutasiKas-account_number');
			$tipe = 'In';
			$kode = 'I';
		}else{
			$from_account = $this->input->post('mutasiKas-account_number');
			$tipe = 'Out';
			$kode = 'O';
		}
		
		$tgl_code = $tgl_transaksi;
		$codetrans = strtotime($tgl_code);
		//$codetrans = date('ymd',$codetrans).'-'.date('His');
		$codetrans = date('ymd',$codetrans);
		
		$transactioncode = $jenis_trans1.''.$kode;
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
				$from_account = $this->input->post('mutasiKas-id_account_'.$i);
			}else{
				$to_account = $this->input->post('mutasiKas-id_account_'.$i);
			}
			
			$id_urut = $this->mm->get_mutasi_code_rp($transactioncode);
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
			
			$keterangan = $this->input->post('mutasiKas-keterangan_'.$i);
			$keterangan = strtoupper($keterangan);
			$jumlah = $this->input->post('mutasiKas-jumlah_'.$i);
			$jumlah = str_replace(',','',$jumlah);
			
			$this->mm->insert_mutasi_rupiah($sitecode,$id_trans,$tipe,$from_account,$to_account,$jumlah,$keterangan,$tgl_transaksi,$created_by);
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
		
		$head_account = $this->input->post('mutasiKas-account_number');
		
		for($i = 1; $i <= $row_number; $i++){
			$id_account = $this->input->post('mutasiKas-id_account_'.$i);
			$keterangan = $this->input->post('mutasiKas-keterangan_'.$i);
			$jumlah = $this->input->post('mutasiKas-jumlah_'.$i);
			
			if($id_account == ''){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Account Harus Diisi!</li>';
				echo json_encode($data);
				exit();
			}
			
			if($id_account == $head_account){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Account Sumber dan Tujuan Tidak Boleh Sama!</li>';
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
		
		$filter_jenis_1 = $this->input->post('mutasiKas-filter_jenis_1');
		$filter_jenis_2 = $this->input->post('mutasiKas-filter_jenis_2');
			
		if($filter_jenis_1 == 'All' && $filter_jenis_2 == 'All'){
			$filter_sql = 'idmutasi LIKE "UI%" OR idmutasi LIKE "UO%" OR idmutasi LIKE "KI%" OR idmutasi LIKE "KO%"';
		}else if($filter_jenis_1 == 'All' && $filter_jenis_2 != 'All'){
			$filter_sql = 'idmutasi LIKE "U'.$filter_jenis_2.'%" OR idmutasi LIKE "K'.$filter_jenis_2.'%"';
		}else if($filter_jenis_1 != 'All' && $filter_jenis_2 == 'All'){
			$filter_sql = 'idmutasi LIKE "'.$filter_jenis_1.'I%" OR idmutasi LIKE "'.$filter_jenis_2.'O%"';
		}else{
			$filter_sql = 'idmutasi LIKE "'.$filter_jenis_1.''.$filter_jenis_2.'%"';
		}
		
		$from_date = $this->input->post('mutasiKas-filterfromdate');
		$to_date = $this->input->post('mutasiKas-filtertodate');
		
		$stockFromTime = $this->date_to_format($from_date);
		$stockToTime = $this->date_to_format($to_date);
		
		$from_date = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_date = date("Y-m-d",$stockToTime).' 00:00:00';
		
		$data_filter = $this->mm->get_filter_mkb($from_date,$to_date,$filter_sql);
		
		$data['view'] = '<table id="mutasiKas-tablefilter" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:50px">No</th><th>Tgl Mutasi</th><th>Jenis Transaksi</th><th>Dari Account</th><th>Ke Account</th><th>Keterangan</th><th>Jumlah</th><th>Act</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_filter as $d){
			$jenis = '';
			
			if($d->tipemutasi == 'In'){
				$jenis = 'Penerimaan Kas/Bank';
			}else if($d->tipemutasi == 'Out'){
				$jenis = 'Pengeluaran Kas/Bank';
			}
			
			$tanggal_tulis = strtotime($d->transdate);
			$tanggal_tulis = date('d-M-y',$tanggal_tulis);
			
			$act = '';
			if($this->session->userdata('gold_admin') == 'Y' || $this->session->userdata('gold_pembukuan') == 'Y' || $this->session->userdata('gold_manager') == 'Y'){	
				$act = '<button type="button" class="ui mini red icon button" onclick=deleteTransMutasi("mutasiKas","'.$d->idmutasi.'") title="Delete"><i class="ban icon"></i></button>';
			}
			
			$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$tanggal_tulis.'</td><td>'.$jenis.'</td><td>'.$d->from_acc_name.'</td><td>'.$d->to_acc_name.'</td><td>'.$d->description.'</td><td class="right aligned">'.number_format($d->value, 0).'</td><td class="center aligned">'.$act.'</td></tr>';
			
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
			
			$mutasi_data = $this->mm->get_mutasi_rp_by_id($id);
			$deletedby = $this->session->userdata('gold_nama_user');
			
			foreach($mutasi_data as $md){
				$siteid = $md->idsite;
				$idmutasi = $md->idmutasi;
				$tipemutasi = $md->tipemutasi;
				$fromaccount = $md->fromaccount;
				$toaccount = $md->toaccount;
				$value = $md->value;
				$description = $md->description;
				$transdate = $md->transdate;
				$createddate = $md->createddate;
				$createdby = $md->createdby;
				
				$this->mm->insert_mutasi_rupiah_deleted($siteid,$idmutasi,$tipemutasi,$fromaccount,$toaccount,$value,$description,$transdate,$createddate,$createdby,$deletedby);
				$this->mm->delete_mutasi_rupiah($id);
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

