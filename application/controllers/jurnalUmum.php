<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class JurnalUmum extends CI_Controller {
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
		$accountumum = $this->mm->get_account_jurnal_umum();
		$data['view'] = '<div class="ui container fluid">
			<div class="ui pointing secondary menu">
				<a class="item active" data-tab="jurnalUmum-first" style="width:50%" onkeyup=entToAction("jurnalUmum")>
					<i class="edit icon"></i> Input Jurnal Umum
				</a>
				<a class="item" data-tab="jurnalUmum-second" style="width:50%" onclick=filterTransaksi("jurnalUmum")>
					<i class="list ol icon"></i> List Jurnal Umum
				</a>
			</div>
			<div class="ui bottom attached tab segment active" data-tab="jurnalUmum-first" onkeyup=entToAction("jurnalUmum")>
				<div class="ui inverted dimmer" id="jurnalUmum-loaderinput">
					<div class="ui large text loader">Loading</div>
				</div>
				<form  class="ui form form-javascript" id="jurnalUmum-form" action="'.base_url().'index.php/jurnalUmum/save" method="post">
				<div class="ui grid">
					<div class="right floated four wide column" style="padding-bottom:0">
						<div class="field">
							<label>Tanggal Mutasi</label>
							<div id="jurnalUmum-wrap_tanggal">
								<input type="text" name="jurnalUmum-dateinput" id="jurnalUmum-dateinput" readonly>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0">
						<div class="ui red message" id="jurnalUmum-wraperror" style="display:none"></div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0" id="wrap_isi_data">
						<table id="jurnalUmum-tableinput" class="ui celled table table-input" cellspacing="0" width="100%" style="margin-top:15px;">
							<thead>
								<tr class="center aligned">
									<th style="width:30px;">No</th>
									<th style="width:250px;">Dari Account</th>
									<th style="width:250px;">Ke Account</th>
									<th>Keterangan</th>
									<th style="width:200px;">Jumlah</th>
								</tr>
							</thead>
							<tbody id="jurnalUmum-pos_body">
								<tr id="jurnalUmum-pos_tr_1">
									<td class="center aligned">1</td>
									<td>
										<select class="form-pos" onkeydown=entToTabInput("jurnalUmum","1","1") name="jurnalUmum-id_account_from_1" id="jurnalUmum-input_1_1">
											<option value="">-- Pilih Account --</option>';
											foreach($accountumum as $k){
											$data['view'] .= '<option value="'.$k->accountnumber.'">'.$k->accountnumber.' - '.$k->accountname.'</option>';
											}
										$data['view'] .= '</select>
									</td>
									<td>
										<select class="form-pos" onkeydown=entToTabInput("jurnalUmum","1","2") name="jurnalUmum-id_account_to_1" id="jurnalUmum-input_1_2">
											<option value="">-- Pilih Account --</option>';
											foreach($accountumum as $k){
											$data['view'] .= '<option value="'.$k->accountnumber.'">'.$k->accountnumber.' - '.$k->accountname.'</option>';
											}
										$data['view'] .= '</select>
									</td>
									<td>
										<input class="form-pos" type="text" name="jurnalUmum-keterangan_1" id="jurnalUmum-input_1_3" onkeydown=entToTabInput("jurnalUmum","1","3") autocomplete="off">
									</td>
									<td>
										<input class="form-pos" type="text" name="jurnalUmum-jumlah_1" id="jurnalUmum-input_1_4" onkeyup=valueToCurrencyRp("jurnalUmum","jurnalUmum-input_1_4","Total") autocomplete="off">
									</td>
								</tr>
							</tbody>
						</table>
						<div class="ui positive right floated labeled icon button" id="jurnalUmum-btn" onclick=saveTransaksi("jurnalUmum")>
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
							<div class="eight wide column ket-bawah right aligned" id="jurnalUmum-total" style="padding-bottom:0;padding-top:0"></div>
						</div>
					</div>
				</div>
				</form>
			</div>
			<div class="ui bottom attached tab segment" data-tab="jurnalUmum-second">
				<div class="ui inverted dimmer" id="jurnalUmum-loaderlist">
					<div class="ui large text loader">Loading</div>
				</div>
				<form class="ui form form-javascript" id="jurnalUmum-form-filter" action="'.base_url().'index.php/jurnalUmum/filter/" method="post">
				<div class="ui grid">
					<div class="eight wide centered column" style="padding-bottom:0">
						<div class="fields">
							<div class="six wide field">
								<label>Tgl Mutasi</label>
								<input type="text" class="form-control input-filter" name="jurnalUmum-filterfromdate" id="jurnalUmum-filterfromdate" readonly>
							</div>
							<div class="two wide field" style="text-align:center;margin-top:30px">
								<label>s.d</label>
							</div>
							<div class="six wide field">
								<label style="visibility:hidden">-</label>
								<input type="text" class="form-control input-filter" name="jurnalUmum-filtertodate" id="jurnalUmum-filtertodate" readonly>
							</div>
							<div class="four wide field">
								<label style="visibility:hidden">-</label>
								<div class="ui fluid icon green button filter-input" id="jurnalUmum-btnfilter" onclick=filterTransaksi("jurnalUmum") title="Filter">
									<i class="filter icon"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" id="jurnalUmum-wrap_filter" style="padding-top:0">
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
		
		$data['view'] = '<tr id="jurnalUmum-pos_tr_'.$row_number.'"><td class="center aligned">'.$row_number.'</td><td><select class="form-pos" onkeydown=entToTabInput("jurnalUmum","jurnalUmum-input_'.$row_number.'_1","Total") name="jurnalUmum-id_account_from_'.$row_number.'" id="jurnalUmum-input_'.$row_number.'_1"><option value="">-- Pilih Account --</option>';
		
		$data_account = $this->mm->get_account_jurnal_umum();
		
		foreach($data_account as $da){
			$accountname = $da->accountname;
			$data['view'] .= '<option value="'.$da->accountnumber.'">'.$da->accountnumber.' - '.$accountname.'</option>';
		}
		
		$data['view'] .= '</select></td><td><select class="form-pos" onkeydown=entToTabInput("jurnalUmum","jurnalUmum-input_'.$row_number.'_2","2") name="jurnalUmum-id_account_to_'.$row_number.'" id="jurnalUmum-input_'.$row_number.'_2"><option value="">-- Pilih Account --</option>';
		
		foreach($data_account as $da){
			$accountname = $da->accountname;
			$data['view'] .= '<option value="'.$da->accountnumber.'">'.$da->accountnumber.' - '.$accountname.'</option>';
		}
		
		$data['view'] .= '<td><input class="form-pos" type="text" name="jurnalUmum-keterangan_'.$row_number.'" id="jurnalUmum-input_'.$row_number.'_3" onkeydown=entToTabInput("jurnalUmum","jurnalUmum-input_'.$row_number.'_3","3") autocomplete="off"></td><td><input class="form-pos" type="text" name="jurnalUmum-jumlah_'.$row_number.'" id="jurnalUmum-input_'.$row_number.'_4" onkeyup=valueToCurrencyRp("jurnalUmum","jurnalUmum-input_'.$row_number.'_4","Total") autocomplete="off"></td></tr>';
								
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function save($row_number){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		$this->validate($row_number);
		
		$tanggal_transaksi = $this->input->post('jurnalUmum-dateinput');
		$tglTrans = $this->date_to_format($tanggal_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		
		$jenis_trans1 = $this->input->post('jurnalUmum-jenis_1');
		$jenis_trans = $this->input->post('jurnalUmum-jenis_2');
		
		$tipe = 'In';
		
		$tgl_code = $tgl_transaksi;
		$codetrans = strtotime($tgl_code);
		//$codetrans = date('ymd',$codetrans).'-'.date('His');
		$codetrans = date('ymd',$codetrans);
		
		$transactioncode = 'JU';
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
			$from_account = $this->input->post('jurnalUmum-id_account_from_'.$i);
			$to_account = $this->input->post('jurnalUmum-id_account_to_'.$i);
			
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
			
			$keterangan = $this->input->post('jurnalUmum-keterangan_'.$i);
			$keterangan = strtoupper($keterangan);
			$jumlah = $this->input->post('jurnalUmum-jumlah_'.$i);
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
		
		for($i = 1; $i <= $row_number; $i++){
			$from_account = $this->input->post('jurnalUmum-id_account_from_'.$i);
			$to_account = $this->input->post('jurnalUmum-id_account_to_'.$i);
			$keterangan = $this->input->post('jurnalUmum-keterangan_'.$i);
			$jumlah = $this->input->post('jurnalUmum-jumlah_'.$i);
			
			if($from_account == '' || $to_account == ''){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Account Harus Diisi!</li>';
				echo json_encode($data);
				exit();
			}
			
			if($from_account == $to_account){
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
			
		$filter_sql = 'idmutasi LIKE "JU%"';
		
		$from_date = $this->input->post('jurnalUmum-filterfromdate');
		$to_date = $this->input->post('jurnalUmum-filtertodate');
		
		$stockFromTime = $this->date_to_format($from_date);
		$stockToTime = $this->date_to_format($to_date);
		
		$from_date = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_date = date("Y-m-d",$stockToTime).' 00:00:00';
		
		$data_filter = $this->mm->get_filter_mkb($from_date,$to_date,$filter_sql);
		
		$data['view'] = '<table id="jurnalUmum-tablefilter" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:50px">No</th><th>Tgl Mutasi</th><th>Dari Account</th><th>Ke Account</th><th>Keterangan</th><th>Jumlah</th><th>Act</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_filter as $d){
			$jenis = '';
			
			$tanggal_tulis = strtotime($d->transdate);
			$tanggal_tulis = date('d-M-y',$tanggal_tulis);
			
			$act = '';
			if($this->session->userdata('gold_admin') == 'Y' || $this->session->userdata('gold_pembukuan') == 'Y' || $this->session->userdata('gold_manager') == 'Y'){	
				$act = '<button type="button" class="ui mini red icon button" onclick=deleteTransMutasi("jurnalUmum","'.$d->idmutasi.'") title="Delete"><i class="ban icon"></i></button>';
			}
			
			$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$tanggal_tulis.'</td><td>'.$d->from_acc_name.'</td><td>'.$d->to_acc_name.'</td><td>'.$d->description.'</td><td class="right aligned">'.number_format($d->value, 0).'</td><td class="center aligned">'.$act.'</td></tr>';
			
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

