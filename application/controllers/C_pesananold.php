<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_pesanan extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->model('M_box','mb');
		$this->load->model('M_karat','mk');
		$this->load->model('M_product','mp');
		$this->load->model('M_mutasi','mm');
		$this->load->model('M_pos','mt');
		$this->load->model('M_kelompok_barang','mc');
		$this->load->model('M_nama_barang','mmp');
		
		$GLOBALS['kasir'] = 1;
	}
	
	public function index(){
		$data['karat'] = $this->mk->get_karat_sdr();
		$data['category'] = $this->mc->get_product_category();
		$this->load->view('mutasi/V_pesanan',$data);
	}
	
	public function get_master_product($id_category,$id_row){
		$master_barang = $this->mmp->get_master_by_category($id_category);
		$data['view'] = '<select name="nama_barang_'.$id_row.'" id="input_'.$id_row.'_3"><option value="">-- Nama Barang --</option>';
		
		foreach($master_barang as $mb){
			$data['view'] .= '<option value="'.$mb->nama_barang.'">'.$mb->nama_barang.'</option>';
		}
		
		$data['view'] .= '</select>';
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function tambah_baris($row_number){
		$row_number = $row_number + 1;
		
		$data['view'] = '<tr id="pos_tr_'.$row_number.'"><td class="center aligned">'.$row_number.'</td><td><select class="form-pos" onkeydown=entToTab("'.$row_number.'","1") name="id_karat_'.$row_number.'" id="input_'.$row_number.'_1"><option value="">-- Karat --</option>';
		
		$karat = $this->mk->get_karat_sdr();
		
		foreach($karat as $k){
			$data['view'] .= '<option value="'.$k->id.'">'.$k->karat_name.'</option>';
		}
		
		$data['view'] .= '</select></td><td><select class="form-pos" name="id_category_'.$row_number.'" id="input_'.$row_number.'_2" onchange=getMasterProduct("'.$row_number.'")><option value="">-- Kelompok --</option>';
		
		$category = $this->mc->get_product_category();
		
		foreach($category as $c){
			$data['view'] .= '<option value="'.$c->id.'">'.$c->category_name.'</option>';
		}
		
		$data['view'] .= '</select></td><td><div id="wrap_nama_barang_'.$row_number.'"><select name="nama_barang_'.$row_number.'" id="input_'.$row_number.'_3"><option value=""></option></select></div></td></tr>';
								
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function get_customer_form(){
		$data_customer = $this->mt->get_customer_pesanan();
		
		$data['view'] = '<i class="close icon"></i><div class="header">List Customer Pesanan</div><div class="content"><table class="ui celled table" id="modal-table" style="width:100%"><thead class="center aligned"><tr><th>No</th><th>Nama Pelanggan</th><th>Alamat</th><th>No. Telp</th><th>Act</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_customer as $d){
			$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$d->cust_name.'</td><td>'.$d->cust_address.'</td><td class="right aligned">'.$d->cust_phone.'</td><td class="center aligned"><button type="button" class="ui linkedin pilih button" onclick=setCustomer("'.$d->cust_phone.'")><i class="hand point up outline icon"></i> Pilih</button></td>';
			
			$number = $number + 1;
		}
		
		$data['view'] .= '</tbody></table></div>';
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function set_customer_form($phone){
		$data_customer = $this->mt->get_customer_pesanan_by_phone($phone);
		
		$data['customer_name'] = $data_customer[0]->cust_name;
		$data['customer_phone'] = $data_customer[0]->cust_phone;
		$data['customer_address'] = $data_customer[0]->cust_address;
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function save($row_number){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		$this->validate($row_number);
		
		$tanggal_transaksi = $this->input->post('tanggal_mutasi');
		$tglTrans = $this->date_to_format($tanggal_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		
		$customer_name = $this->input->post('customer_name');
		$customer_address = $this->input->post('customer_address');
		$customer_phone = $this->input->post('customer_phone');
		$pesanan_code = 'PS';
		$pesanan_number = $this->input->post('pesanan_number');
		
		$id_pesanan = $pesanan_code.''.$pesanan_number;
		
		$ump_val = $this->input->post('ump_val');
		$ump_val = str_replace(',','',$ump_val);
		
		$tipe = 'In';
		$from_account = $this->mm->get_default_account('UMP');
		$to_account = $this->mm->get_default_account('KE');
		
		$tgl_code = $tgl_transaksi;
		$codetrans = strtotime($tgl_code);
		$codetrans = date('ymd',$codetrans).'-'.$pesanan_number;
		
		$transactioncode = 'PI';
		$sitecode = $this->mm->get_site_code();
		
		$totalnumberlength = 3;
		$numberlength = strlen($sitecode);
		$numberspace = $totalnumberlength - $numberlength;
		if($numberspace != 0){
			for ($i = 1; $i <= $numberspace; $i++){
				$transactioncode .= '0';
			}
		}
		
		$transactioncode .= $sitecode.'-'.$codetrans;
		$created_by = $this->session->userdata('gold_nama_user');
		
		$data_customer = $this->mt->get_customer_pesanan_by_phone($customer_phone);
		if(count($data_customer) == 0){
			$this->mt->save_customer_pesanan($customer_phone,$customer_address,$customer_name);
		}
		
		$keterangan = 'Pesanan Pelanggan ID '.$id_pesanan;
		
		$this->mt->input_main_pesanan($id_pesanan,$customer_name,$customer_address,$customer_phone,$ump_val,$tgl_transaksi,$created_by);
		$this->mm->insert_mutasi_rupiah($sitecode,$transactioncode,$tipe,$from_account,$to_account,$ump_val,$keterangan,$tgl_transaksi,$created_by);
		
		for($i = 1; $i <= $row_number; $i++){
			$id_karat = $this->input->post('id_karat_'.$i);
			$id_category = $this->input->post('id_category_'.$i);
			$nama_barang = $this->input->post('nama_barang_'.$i);
			
			$this->mt->input_detail_pesanan($id_pesanan,$id_karat,$id_category,$nama_barang,$tgl_transaksi,$created_by);
		}
		
		$this->db->trans_complete();
		
		$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Input Berhasil!</div></div>';
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function update($id_pesanan,$status,$row_number){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		
		$tanggal_action = $this->input->post('tanggal_action');
		$grosir_use = $this->input->post('saldo_grosir');
		$grosir_use = str_replace(',','',$grosir_use);
		$tglTrans = $this->date_to_format($tanggal_action);
		$tanggal_action = date("Y-m-d",$tglTrans).' 00:00:00';
		
		$data_pesanan = $this->mt->get_pesanan_by_id($id_pesanan);
		
		if($status == 'P'){
			$tanggal_pesan = $data_pesanan[0]->trans_date;
			if($tanggal_pesan > $tanggal_action){
				$data['inputerror'] = '<ul class="list"><li>Tanggal Masuk Box Tidak Boleh Dibawah Tanggal Pesanan!</li></ul>';
				$data['success'] = FALSE;
				echo json_encode($data);
				exit();
			}
			
			$select_from = 3;
			$select_from_desc = 'Pesanan Nomor '.$id_pesanan;
			$box_pesanan = $this->mb->get_box_pesanan();
			
			for($i = 1; $i <= $row_number; $i++){
				$id_detail = $this->input->post('id_detail_'.$i);
				$select_karat = $this->input->post('id_karat_'.$i);
				$select_box = $box_pesanan;
				$select_category = $this->input->post('id_category_'.$i);
				$nama_barang = $this->input->post('nama_barang_'.$i);
				$berat_barang = $this->input->post('product_weight_'.$i);
				$berat_barang = str_replace(',','',$berat_barang);
				
				if($berat_barang == '' || $berat_barang == 0 ){
					$data['inputerror'] = '<ul class="list"><li>Berat Harus Diisi & Tidak Boleh Bernilai 0!</li></ul>';
					$data['success'] = FALSE;
					echo json_encode($data);
					exit();
				}
				
				$sitecode = $this->mm->get_site_code();
			
				$stock_in_id = 'PI-00'.$sitecode.''.date('y').'-';
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
					for ($a = 1; $a <= $numberspace; $a++){
						$stock_in_id .= '0';
						$product_id .= '0';
					}
				}
				
				$stock_in_id .= $numbertrans;
				$product_id .= $numbertrans;
				
				$created_by = $this->session->userdata('gold_nama_user');
				$id_lama = 'NULL';
				
				$this->mp->insert_stock_in($stock_in_id,$select_karat,$select_box,$select_category,$select_from,$select_from_desc,$nama_barang,$berat_barang,$tanggal_action,$created_by);
				$this->mp->insert_product($product_id,$id_lama,$select_karat,$select_box,$select_category,$select_from,$select_from_desc,$nama_barang,$berat_barang,$tanggal_action,$created_by);
				
				$account_srt = $this->mm->get_default_account('SRT');
				$account_pjg = $this->mm->get_default_account('PJG');
				
				$desc = 'STOCK IN - NO.REG : '.$product_id;
				
				$this->mm->insert_mutasi_gram($sitecode,$stock_in_id,'In',$select_karat,$account_srt,$account_pjg,$berat_barang,$desc,$tanggal_action,$created_by);
				
				if($status == 'P'){
					$status_update = 'B';
					$this->mt->update_box_detail_pesanan($id_detail,$product_id,$berat_barang,$status_update,$tanggal_action,$created_by);
				}
			}
			
			$account_srt = $this->mm->get_default_account('SRT');
			$account_grs = $this->mm->get_default_account('SDG');
			
			$desc = 'Pengambilan Saldo Grosir Untuk Pesanan ID '.$id_pesanan;
			$no_pesanan = str_replace('PS','',$id_pesanan);
			
			$codetrans = date('ymd',$tglTrans).'-'.$no_pesanan;
			$transactioncode = 'PG';
			
			$totalnumberlength = 3;
			$numberlength = strlen($sitecode);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$transactioncode .= '0';
				}
			}
			
			$transactioncode .= $sitecode.'-'.$codetrans;
			
			$this->mm->insert_mutasi_gram($sitecode,$transactioncode,'In',1,$account_grs,$account_srt,$grosir_use,$desc,$tanggal_action,$created_by);
			
			if($status == 'P'){
				$status_update = 'B';
				$this->mt->update_box_main_pesanan($id_pesanan,$status_update,$grosir_use,$tanggal_action,$created_by);
			}
		}else if($status == 'B'){
			$tanggal_pesan = $data_pesanan[0]->box_date;
			if($tanggal_pesan > $tanggal_action){
				$data['inputerror'] = '<ul class="list"><li>Tanggal Ambil Tidak Boleh Dibawah Tanggal Masuk Box!</li></ul>';
				$data['success'] = FALSE;
				echo json_encode($data);
				exit();
			}
			
			$harga_emas = $this->mt->get_do_by_date($tanggal_action);
			if($harga_emas == 0){
				$data['inputerror'] = '<ul class="list"><li>Daily Open Tidak Tersedia Untuk Tanggal Yang Anda Pilih!</li></ul>';
				$data['success'] = FALSE;
				echo json_encode($data);
				exit();
			}
			
			$codetrans = date('ymd',$tglTrans);
		
			$transactioncode = 'JP';
			$sitecode = $this->mm->get_site_code();
			
			$totalnumberlength = 3;
			$numberlength = strlen($sitecode);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$transactioncode .= '0';
				}
			}
			
			$transactioncode .= $sitecode.'-'.$GLOBALS['kasir'].'-'.$codetrans.'-';
			$transnumber = $this->mt->get_trans_number_jual($tanggal_action,$GLOBALS['kasir']);
			
			$numbertrans = count($transnumber);
			$numbertrans = $numbertrans + 1;
			
			$totalnumberlength = 5;
			$numberlength = strlen($numbertrans);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$transactioncode .= '0';
				}
			}
			$transactioncode .= $numbertrans;
			
			$data_main_pesanan = $this->mt->get_pesanan_by_id($id_pesanan);
			
			$cust_service = $this->session->userdata('gold_nama_user');
			$cust_phone = $data_main_pesanan[0]->cust_phone;
			$cust_address = $data_main_pesanan[0]->cust_address;
			$cust_name = $data_main_pesanan[0]->cust_name;
			$total_price = $this->input->post('total_modal_hidden');
			$total_price = str_replace(',','',$total_price);
			$bayar_1 = $this->input->post('total_modal_hidden');
			$bayar_1 = str_replace(',','',$bayar_1);
			$bayar_2 = 0;
			$bayar_2 = str_replace(',','',$bayar_2);
			$jenis_bayar_1 = $this->mm->get_default_account('KE');
			$jenis_bayar_2 = $this->mm->get_default_account('KE');
			$account_jual = $this->mm->get_default_account('JL');
			$created_by = $this->session->userdata('gold_nama_user');
			$mutasi_desc = 'PENJUALAN - NO.REG : '.$transactioncode;
			
			for($i = 1; $i <= $row_number; $i++){
				$id_detail = $this->input->post('id_detail_'.$i);
				$product_id = $this->input->post('id_product_'.$i);
				$data_product = $this->mp->get_product_by_id($product_id);
				
				$id_box = $data_product[0]->id_box;
				$id_karat = $data_product[0]->id_karat;
				$product_name = $data_product[0]->product_name;
				$product_weight = $data_product[0]->product_weight;
				$product_desc = $data_product[0]->product_name;
				$product_price = $this->input->post('product_price_'.$i);
				$product_price = str_replace(',','',$product_price);
				
				$data['inputerror'] = '<ul class="list"></ul>';
				$data_karat = $this->mt->get_harga_struk_by_id($id_karat,$product_weight);
			
				foreach($data_karat as $k){
					$min_sell = $harga_emas * $k->min_persen / 100;
					$max_sell = $harga_emas * $k->max_persen / 100;
				}
				
				$min_harga_jual = $min_sell * $data_product[0]->product_weight;
				$min_harga_jual = $min_harga_jual / 1000;
				$min_harga_jual = ceil($min_harga_jual);
				$min_harga_jual = $min_harga_jual * 1000;
				
				$max_harga_jual = $max_sell * $data_product[0]->product_weight;
				$max_harga_jual = $max_harga_jual / 1000;
				$max_harga_jual = ceil($max_harga_jual);
				$max_harga_jual = $max_harga_jual * 1000;
				
				if($product_price < $min_harga_jual || $product_price > $max_harga_jual){
					$data['success'] = FALSE;
					$data['inputerror'] .= '<label>Harga Melewati Batas Min/Max.<br>Harga Min : '.number_format($min_harga_jual,0,".",",").'<br>Harga Max : '.number_format($max_harga_jual,0,".",",").'</label>';
					$data['inputerror'] .= '</ul></div></div></div>';
					echo json_encode($data);
					exit();
				}
				
				$this->mt->insert_detail_jual($transactioncode,$GLOBALS['kasir'],$product_id,$id_box,$id_karat,$product_name,$product_weight,$product_desc,$product_price,$tanggal_action,$created_by);
				
				$this->mt->update_sell_product($transactioncode,$product_id,$tanggal_action);
				
				$account_jl = $this->mm->get_default_account('JL');
				$account_pjg = $this->mm->get_default_account('PJG');
				
				$desc = 'PENJUALAN - NO.REG : '.$transactioncode;
				
				$this->mm->insert_mutasi_gram($sitecode,$transactioncode.'-'.$i,'Out',$id_karat,$account_pjg,$account_jl,$product_weight,$desc,$tanggal_action,$created_by);
				
				$status_update = 'C';
				$this->mt->update_ambil_detail_pesanan($id_detail,$status_update,$product_price,$tanggal_action,$created_by);
			}
			
			if($cust_phone != ''){
				$found_cust = $this->mt->find_customer($cust_phone);
				if(count($found_cust) == 0){
					$this->mt->save_customer($cust_phone,$cust_address,$cust_name);
				}else{
					$this->mt->update_customer($cust_phone,$cust_address,$cust_name);
				}
			}
			
			$this->mt->insert_main_jual($transactioncode,$GLOBALS['kasir'],$cust_service,$cust_phone,$cust_address,$cust_name,$total_price,$bayar_1,$bayar_2,$jenis_bayar_1,$jenis_bayar_2,$tanggal_action,$created_by);
			
			$count_mutasi = 1;
			if($bayar_1 != 0){
				$this->mm->insert_mutasi_rupiah($sitecode,$transactioncode.'-'.$count_mutasi,'In',$account_jual,$jenis_bayar_1,$bayar_1,$mutasi_desc,$tanggal_action,$created_by);
				
				$count_mutasi = $count_mutasi + 1;
			}
			
			if($bayar_2 != 0){
				$this->mm->insert_mutasi_rupiah($sitecode,$transactioncode.'-'.$count_mutasi,'In',$account_jual,$jenis_bayar_2,$bayar_2,$mutasi_desc,$tanggal_action,$created_by);
			}
			
			$status_update = 'C';
			$this->mt->update_ambil_main_pesanan($id_pesanan,$status_update,$grosir_use,$total_price,$tanggal_action,$created_by);
			$data['id_trans'] = $transactioncode;
			
			$no_pesanan = str_replace('PS','',$id_pesanan);
			
			$tgl_pesan = strtotime($tanggal_pesan);
			$tgl_pesan = date('ymd',$tgl_pesan);
			$codetrans = $tgl_pesan.'-'.$no_pesanan;
			$transactioncode = 'PG';
			
			$totalnumberlength = 3;
			$numberlength = strlen($sitecode);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$transactioncode .= '0';
				}
			}
			
			$transactioncode .= $sitecode.'-'.$codetrans;
			
			$this->mm->update_mutasi_gram($transactioncode,$grosir_use);
			
			
			
			
			
			$data_main = $this->mt->get_main_penjualan($transactioncode);
			$id_detail = $data_main[0]->transaction_code;
			$cust_service = $data_main[0]->cust_service;
			$cust_phone = $data_main[0]->cust_phone;
			$cust_address = $data_main[0]->cust_address;
			$cust_name = $data_main[0]->cust_name;
			$created_by = $data_main[0]->created_by;
			$tanggal_aktif = $data_main[0]->trans_date;
			
			$total_price = $data_main[0]->total_price;
			$total_price = str_replace(',','',$total_price);
			$bayar_1 = $data_main[0]->bayar_1;
			$bayar_1 = str_replace(',','',$bayar_1);
			$bayar_2 = $data_main[0]->bayar_2;
			$bayar_2 = str_replace(',','',$bayar_2);
			$jenis_bayar_1 = $data_main[0]->jenis_bayar_1;
			$jenis_bayar_2 = $data_main[0]->jenis_bayar_2;
			
			$data_product_jual = $this->mt->get_product_jual($id_detail);
			
			$this->load->library("EscPos.php");
			
			$connector = new Escpos\PrintConnectors\WindowsPrintConnector("smb://Guest@Nurpida/EpsonLQ-");
			$printer = new Escpos\Printer($connector);
			
			$karat = $this->mt->get_do_formula_struk();
			$harga_emas = $this->mt->get_do_by_date($tanggal_aktif);
			
			if($harga_emas == 0){
				$harga_emas = $this->mt->get_last_do();
			}
			
			$tanggal_aktif = strtotime($tanggal_aktif);
			$bulan = date('m',$tanggal_aktif);
			
			switch($bulan){
				case "1":
					$aktif_bulan = 'Jan';
					break;
				case "2":
					$aktif_bulan = 'Feb';
					break;
				case "3":
					$aktif_bulan = 'Mar';
					break;
				case "4":
					$aktif_bulan = 'Apr';
					break;
				case "5":
					$aktif_bulan = 'May';
					break;
				case "6":
					$aktif_bulan = 'Juni';
					break;
				case "7":
					$aktif_bulan = 'Juli';
					break;
				case "8":
					$aktif_bulan = 'Agust';
					break;
				case "9":
					$aktif_bulan = 'Sept';
					break;
				case "10":
					$aktif_bulan = 'Okt';
					break;
				case "11":
					$aktif_bulan = 'Nov';
					break;
				case "12":
					$aktif_bulan = 'Des';
					break;
			}
			
			$tanggal = date('d',$tanggal_aktif);
			$tahun = date('Y',$tanggal_aktif);
			
			$printer -> text("\n");
			$printer -> text("\n");
			$printer -> text("\n");
			$printer -> text("\n");
			$printer -> text("\n");
			$printer -> text("\n");
			$printer -> text("\n");
			
			$num = 1;
			foreach($karat as $k){
				$sell = $harga_emas * $k->kadar_jual / 100;
				$sell = $sell / 1000;
				$sell = ceil($sell);
				$sell = $sell * 1000;
				$sell = number_format($sell,0,".",",");
				$sell_length = strlen($sell);
				
				$length_print = 80;
				$sell_length = 7 + $sell_length;
				$tgl_print = $tanggal.' '.$aktif_bulan.' '.$tahun;
				$jam_print = date('H:i');
				
				$tgl_length = strlen($tgl_print);
				$jam_length = strlen($jam_print);
				
				if($num == 3){
					$printer -> text("       ".$sell);
					$spasi_tgl = $length_print - $sell_length - $tgl_length;
					
					for($a=1; $a <= $spasi_tgl; $a++){
						$printer -> text(" ");
					}
					
					$printer -> text($tgl_print."\n");
				}else if($num == 4){
					$printer -> text("       ".$sell);
					$spasi_jam = $length_print - $sell_length - $jam_length;
					$spasi_jam = $spasi_jam - 13;
					$spasi_bagi = $spasi_jam / 2;
					$spasi_satu = floor($spasi_bagi);
					$spasi_dua = ceil($spasi_bagi);
					
					for($a=1; $a <= $spasi_satu; $a++){
						$printer -> text(" ");
					}
					
					$printer -> text("** REPRINT **");
					
					for($p=1; $p <= $spasi_dua; $p++){
						$printer -> text(" ");
					}
					
					$printer -> text($jam_print."\n");
				}else{
					$printer -> text("       ".$sell."\n");
				}
				
				$num = $num + 1;
			}
			
			$printer -> text("\n");
			$printer -> text("\n");
			$printer -> text("\n");
			$printer -> text("\n");
			
			$nomor_jual = 1;
			$total_jual = 0;
			foreach($data_product_jual as $dp){
				$product_id = $dp->id_product;
				$id_lengkap = explode('-',  $product_id);
				$product_id = $id_lengkap[1];
				
				$id_cetak = $product_id;
				
				$data_product = $this->mp->get_product_by_id($product_id);
				
				$id_box = $data_product[0]->id_box;
				$karat_name = $data_product[0]->karat_name;
				$product_name = $data_product[0]->product_name;
				$product_weight = $data_product[0]->product_weight;
				$product_price = $dp->product_price;
				$product_price = str_replace(',','',$product_price);
				
				$box_number = '';
				$totalnumberlength = 3;
				$numberlength = strlen($id_box);
				$numberspace = $totalnumberlength - $numberlength;
				if($numberspace != 0){
					for ($a = 1; $a <= $numberspace; $a++){
						$box_number .= '0';
					}
				}
				
				$box_number .= $id_box;
				
				$printer -> text("   ".$box_number);
				$printer -> text("  ".$id_cetak);
				$printer -> text("   ".substr($karat_name, 0, 3));
				$max_char_prod_name = 21;
				
				$printer -> text("  ".substr($product_name, 0, $max_char_prod_name));
				$jumlah_char = strlen($product_name);
				
				if($jumlah_char < $max_char_prod_name){
					$selisih = $max_char_prod_name - $jumlah_char;
					
					for($b = 1; $b<= $selisih; $b++){
						$printer -> text(" ");
					}
				}
				
				$printer -> text("        -  ");
				
				$weight_length = 7;
				$berat_cetak = number_format($product_weight,3,".",",");
				$berat_length = strlen($berat_cetak);
				
				$selisih_berat = $weight_length - $berat_length;
				for($c = 1; $c<= $selisih_berat; $c++){
					$printer -> text(" ");
				}
				
				$printer -> text(number_format($product_weight,3,".",","));
				$printer -> text(" ");
				
				$price_length = 14;
				$harga_cetak = number_format($product_price,0,".",",");
				$harga_length = strlen($harga_cetak);
				
				$selisih_harga = $price_length - $harga_length;
				for($d = 1; $d<= $selisih_harga; $d++){
					$printer -> text(" ");
				}
				
				$printer -> text(number_format($product_price,0,".",",")."\n");
				$printer -> text("\n");
				
				$nomor_jual = $nomor_jual + 1;
				$total_jual = $total_jual + $product_price;
			}
			
			$sisa_baris = 6 - $nomor_jual;
			for($e = 1; $e <= $sisa_baris; $e++){
				$printer -> text("\n");
				$printer -> text("\n");
			}
			
			$printer -> text("\n");
			
			$terbilang = $this->terbilang($total_jual, $style = 3);
			
			$max_char_terbilang = 44;
			$length_terbilang = strlen($terbilang);
			
			if($length_terbilang < $max_char_terbilang){
				$printer -> text("           #".$terbilang."#");
				$sisa_print = $max_char_terbilang - $length_terbilang;
				
				for($f = 1; $f < $sisa_print; $f++){
					$printer -> text(" ");
				}
				
				$printer -> text("           ");
				
				$total_length = 13;
				$total_cetak = number_format($total_jual,0,".",",");
				$ttl_length = strlen($total_cetak);
				
				$selisih_total = $total_length - $ttl_length;
				for($d = 1; $d<= $selisih_total; $d++){
					$printer -> text(" ");
				}
				
				$printer -> text(number_format($total_jual,0,".",",")."\n");
				$printer -> text("\n");
			}else{
				$baris_satu = '';
				$baris_dua = '';
				
				$terbilangArray = explode(' ', $terbilang);
				
				$total_char = 0;
				$pengecekan = 0;
				for($i = 0; $i < count($terbilangArray); $i++){
					$char = $terbilangArray[$i];
					if($pengecekan == 0){
						$cek_panjang = $baris_satu.' '.$char;
						$cek_length = strlen($cek_panjang);
						if($cek_length < $max_char_terbilang){
							$baris_satu .= $char.' ';
						}else{
							$baris_dua .= $char.' ';
							$pengecekan = 1;
						}
					}else{
						$baris_dua .= $char.' ';
					}
				}
				
				$length_terbilang = strlen($baris_satu);
				
				$printer -> text("           #".$baris_satu);
				$sisa_print = $max_char_terbilang - $length_terbilang;
				
				for($g = 1; $g<= $sisa_print; $g++){
					$printer -> text(" ");
				}
				
				$printer -> text("           ");
				
				$total_length = 12;
				$total_cetak = number_format($total_jual,0,".",",");
				$ttl_length = strlen($total_cetak);
				
				$selisih_total = $total_length - $ttl_length;
				for($h = 1; $h<= $selisih_total; $h++){
					$printer -> text(" ");
				}
				
				$printer -> text(number_format($total_jual,0,".",",")."\n");
				$printer -> text("           ".$baris_dua."#\n");
			}
			
			$printer -> text("\n");
			$printer -> text("\n");
			
			$printer -> text("                                              ".strtoupper($cust_service));
			
			$ket_length = 13;
			$cs_length = strlen($cust_service);
			
			$sisa = $ket_length - $cs_length;
			for($a = 1; $a<= $sisa; $a++){
				$printer -> text(" ");
			}
			
			$printer -> text("        ".strtoupper(substr($cust_name, 0, 13))."\n");
			
			$printer -> text("                                                                   ".strtoupper(substr($cust_address, 0, 13))."\n");
			
			$printer -> text("                                              ".strtoupper($created_by));
			
			$cs_length = strlen($created_by);
			
			$sisa = $ket_length - $cs_length;
			for($a = 1; $a<= $sisa; $a++){
				$printer -> text(" ");
			}
			
			$printer -> text("        ".strtoupper(substr($cust_phone, 0, 13))."\n");
			
			$pembayaran = '';
			if($bayar_1 == 0){
				$pembayaran .= $this->mt->get_coa_number_name2($jenis_bayar_2);
			}
			
			if($bayar_2 == 0){
				$pembayaran .= 'TUNAI';
			}
			
			if($bayar_1 != 0 && $bayar_2 != 0){
				$bayar_2 = $this->mt->get_coa_number_name2($jenis_bayar_2);
				$pembayaran .= 'TUNAI DAN '.$bayar_2;
			}
			
			$printer -> text(strtoupper($pembayaran)."\n");
			
			for($i = 1;$i<=40;$i++){
				$printer -> text("\n");
			}
			
			$printer -> close();
		}
		
		$this->db->trans_complete();
		
		if($status == 'B'){
			$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Berhasil Cetak Pesanan!<br>'.$transactioncode.'</div></div>';
		}else{
			$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Berhasil Update Pesanan!</div></div>';
		}
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	private function validate($row_number){
		$data = array();
		$data['inputerror'] = '<ul class="list"></ul>';
		$data['success'] = TRUE;
		
		$customer_name = $this->input->post('customer_name');
		if($customer_name == ''){
			$data['inputerror'] .= '<li>Nama Pelanggan Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		$customer_address = $this->input->post('customer_address');
		if($customer_address == ''){
			$data['inputerror'] .= '<li>Alamat Pelanggan Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		$customer_phone = $this->input->post('customer_phone');
		if($customer_phone == ''){
			$data['inputerror'] .= '<li>Telepon Pelanggan Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		$pesanan_code = 'PS';
		$pesanan_number = $this->input->post('pesanan_number');
		if($pesanan_number == ''){
			$data['inputerror'] .= '<li>Nomor Pesanan Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		$ump_val = $this->input->post('ump_val');
		$ump_val = str_replace(',','',$ump_val);
		
		if($ump_val == '' || $ump_val == 0){
			$data['inputerror'] .= '<li>Uang Muka Pesanan Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		$data_customer = $this->mt->get_customer_pesanan_by_phone($customer_phone);
		
		if(count($data_customer) > 0){
			$nama_cust = $data_customer[0]->cust_name;
			
			if($nama_cust != $customer_name){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Nomor Telepon Sudah Terdaftar Untuk Nama Pelanggan Yang Berbeda!</li>';
				echo json_encode($data);
				exit();
			}
		}
		
		$data_pesanan = $this->mt->get_pesanan_by_id($pesanan_code.''.$pesanan_number);
		if(count($data_pesanan) > 0){
			$data['success'] = FALSE;
			$data['inputerror'] .= '<li>Nomor Pesanan Sudah Terdaftar, Ganti Nomor Pesanan!</li>';
			echo json_encode($data);
			exit();
		}
		
		for($i = 1; $i <= $row_number; $i++){
			$id_karat = $this->input->post('id_karat_'.$i);
			$id_category = $this->input->post('id_category_'.$i);
			$nama_barang = $this->input->post('nama_barang_'.$i);
			
			if($id_karat == ''){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Karat Harus Diisi!</li>';
				echo json_encode($data);
				exit();
			}
			
			if($id_category == ''){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Kelompok Harus Diisi!</li>';
				echo json_encode($data);
				exit();
			}
			
			if($nama_barang == ''){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Nama Barang Harus Diisi!</li>';
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
		
		$filter_status = $this->input->post('filter_status');
		if($filter_status == 'All'){
			$filter_status = '"P","B","C","X"';
		}else{
			$filter_status = '"'.$filter_status.'"';
		}
		
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		
		$stockFromTime = $this->date_to_format($from_date);
		$stockToTime = $this->date_to_format($to_date);
		
		$from_date = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_date = date("Y-m-d",$stockToTime).' 00:00:00';
		
		$data_filter = $this->mt->get_filter_pesanan($from_date,$to_date,$filter_status);
		
		$data['view'] = '<table id="filter_data_tabel" class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:50px">No</th><th>ID</th><th>Tgl Pesan</th><th>Customer</th><th>Alamat</th><th>Telepon</th><th>UMP</th><th>Status</th><th>Act</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_filter as $d){
			$tanggal_tulis = strtotime($d->trans_date);
			$tanggal_tulis = date('d-M-y',$tanggal_tulis);
			
			if($d->status == 'P'){
				$status = 'Dalam Proses';
			}else if($d->status == 'B'){
				$status = 'Masuk Box';
			}else if($d->status == 'C'){
				$status = 'Selesai';
			}else if($d->status == 'X'){
				$status = 'Batal';
			}
			
			$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$d->id_pesanan.'</td><td>'.$tanggal_tulis.'</td><td>'.$d->cust_name.'</td><td>'.$d->cust_address.'</td><td>'.$d->cust_phone.'</td><td class="right aligned">'.number_format($d->ump_val, 0).'</td><td>'.$status.'</td><td class="center aligned"><div id="btn_search" class="ui mini icon facebook button" title="Lihat Detail" onclick=getDetailPesanan("'.$d->id_pesanan.'")><i class="search icon"></i></div></td></tr>';
			
			$number = $number + 1;
		}
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function get_detail_pesanan($id_pesanan){
		$data_main_pesanan = $this->mt->get_pesanan_by_id($id_pesanan);
		$data_detail_pesanan = $this->mt->get_detail_pesanan_by_id($id_pesanan);
		
		$data['row_detail'] = count($data_detail_pesanan);
		
		$data['view'] = '<i class="close icon"></i><div class="header">Pesanan Nomor '.$id_pesanan.'</div><div class="scrolling content"><div class="ui grid"><div class="fifteen wide centered column center aligned">';
		
		foreach($data_main_pesanan as $d){
			$data['status'] = $d->status;
			
			$tgl_pesanan = strtotime($d->trans_date);
			$tgl_pesanan = date('d-m-Y',$tgl_pesanan);
			$tgl_pesanan = $this->date_to_string($tgl_pesanan);
			
			//input pesanan
			$data['view'] .= '<div class="ui ordered steps"><div class="completed step"><div class="content"><div class="title" style="text-align:left">Input Pesanan</div><div class="description" style="text-align:left">'.$tgl_pesanan.'</div></div></div>';
			
			//masuk box
			$tgl_box = $d->box_date;
			if($tgl_box == '0000-00-00 00:00:00'){
				$stt_box = 'active';
				$tgl_box = 'Pesanan Belum Masuk Box';
			}else{
				$stt_box = 'completed';
				$tgl_box = strtotime($d->box_date);
				$tgl_box = date('d-m-Y',$tgl_box);
				$tgl_box = $this->date_to_string($tgl_box);
			}
			
			$data['view'] .= '<div class="'.$stt_box.' step"><div class="content"><div class="title" style="text-align:left">Pesanan Masuk Box</div><div class="description" style="text-align:left">'.$tgl_box.'</div></div></div>';
			
			//ambil pesanan
			$tgl_ambil = $d->ambil_date;
			if($tgl_ambil == '0000-00-00 00:00:00'){
				if($stt_box == 'active'){
					$stt_ambil = '';
				}else{
					$stt_ambil = 'active';
				}
				
				$tgl_ambil = 'Pesanan Belum Diambil';
			}else{
				$stt_ambil = 'completed';
				$tgl_ambil = strtotime($d->ambil_date);
				$tgl_ambil = date('d-m-Y',$tgl_ambil);
				$tgl_ambil = $this->date_to_string($tgl_ambil);
			}
			
			$data['view'] .= '<div class="'.$stt_ambil.' step"><div class="content"><div class="title" style="text-align:left">Pengambilan Pesanan</div><div class="description" style="text-align:left">'.$tgl_ambil.'</div></div></div></div></div></div>';
			
			$id_tulis = str_replace('PS','',$id_pesanan);
			
			if($d->status == 'C'){
				$readonly = 'readonly';
			}else{
				$readonly = '';
			}
			
			//header pesanan
			$data['view'] .= '<form  class="ui form form-javascript" id="form_modal" action="'.base_url().'index.php/C_pesanan/update/'.$id_pesanan.'/'.$d->status.'" method="post"><div class="ui grid"><div class="fifteen wide centered column"><div class="ui grid"><div class="five wide column"><div class="field"><label>Data Pelanggan</label><div class="ui left action input"><div class="ui icon button"><i class="user outline icon"></i></div><input type="text" value="'.$d->cust_name.'" readonly></div></div><div class="field"><div class="ui left action input"><div class="ui icon button"><i class="building outline icon"></i></div><input type="text" value="'.$d->cust_address.'" readonly></div></div><div class="field"><div class="ui left action input"><div class="ui icon button"><i class="phone icon"></i></div><input type="text" value="'.$d->cust_phone.'" readonly></div></div></div><div class="five wide column"><div class="field"><label>Data Pesanan</label><div class="ui left action input"><div class="ui icon button">PS</div><input type="text" value="'.$id_tulis.'" readonly></div></div><div class="field"><div class="ui left action input"><div class="ui icon button"><i class="money bill alternate outline icon"></i></div><input type="text" value="'.number_format($d->ump_val, 0).'" readonly></div></div><div class="field"><div class="ui left action input"><div class="ui icon button" title="Saldo Grosir Terpakai"><i class="database icon"></i></div><input type="text" id="saldo_grosir" name="saldo_grosir" value="'.number_format($d->grosir_use, 2).'" onkeyup=grosirToCurrency() autocomplete="off" '.$readonly.'></div></div></div>';
			
			if($d->status == 'P'){
				$ket_tgl = 'Tanggal Masuk Box';
			}else if($d->status == 'B'){
				$ket_tgl = 'Tanggal Ambil Pesanan';
			}
			
			if($d->status == 'P' || $d->status == 'B'){
				$data['view'] .= '<div class="right floated four wide column"><div class="field"><label>'.$ket_tgl.'</label><input type="text" name="tanggal_action" id="tanggal_action" readonly onkeydown=entToHeader("input_modal_1_1") onchange=entToForm("input_modal_1_1")></div></div></div></div>';
			}
		}
		
		$data['view'] .= '<div class="fifteen wide centered column" style="padding-top:0;padding-bottom:0"><div class="ui red message" id="error_wrap_modal" style="display:none"></div></div><div class="sixteen wide centered column">';
		
		//tabel
		if($d->status == 'P'){
			$data['view'] .= '<table id="modal_data_tabel" class="ui celled table" cellspacing="0" width="100%"><thead><tr class="center aligned"><th style="width:30px;">No</th><th style="width:100px;">Karat</th><th style="width:100px;">Kelompok</th><th>Nama Barang</th><th style="width:100px;">Berat</th></tr></thead><tbody>';
			
			$number = 1;
			
			foreach($data_detail_pesanan as $d){
				$next = $number+1;
				
				$action = '';
				if($number < count($data_detail_pesanan)){
					$action =  'onkeydown=entToTabModal("input_modal_1_'.$next.'")';
				}
				
				$data['view'] .= '<tr><td class="center aligned"><input type="hidden" name="id_detail_'.$number.'" value="'.$d->id.'">'.$number.'</td><td>'.$d->karat_name.'<input type="hidden" name="id_karat_'.$number.'" value="'.$d->id_karat.'"></td><td>'.$d->category_name.'<input type="hidden" name="id_category_'.$number.'" value="'.$d->id_category.'"></td><td>'.$d->nama_pesanan.'<input type="hidden" name="nama_barang_'.$number.'" value="'.$d->nama_pesanan.'"></td><td style="padding:0"><input class="form-pos" type="text" name="product_weight_'.$number.'" id="input_modal_1_'.$number.'" onkeyup=weightToCurrency("input_modal_1_","'.$number.'") '.$action.' value="'.$d->product_weight.'" autocomplete="off"></td></tr>';
				
				$number = $number + 1;
			}
		
			$data['view'] .= '</tbody></table><div class="right floated eight wide column"><div class="ui grid" style="padding-top:15px"><div class="eight wide column ket-bawah right aligned" style="padding-bottom:0;padding-top:0">TOTAL (Gram)</div><div class="eight wide column ket-bawah right aligned" id="total_box" style="padding-bottom:0;padding-top:0">0</div></div></div>';
		}else if($d->status == 'B'){
			$data['view'] .= '<table id="modal_data_tabel" class="ui celled table" cellspacing="0" width="100%"><thead><tr class="center aligned"><th style="width:30px;">No</th><th style="width:100px;">Karat</th><th style="width:100px;">Kelompok</th><th>Nama Barang</th><th style="width:100px;">Berat</th><th style="width:150px;">Harga</th></tr></thead><tbody>';
			
			$number = 1;
			
			foreach($data_detail_pesanan as $d){
				$next = $number+1;
				
				$action = '';
				if($number < count($data_detail_pesanan)){
					$action =  'onkeydown=entToTabModal("input_modal_1_'.$next.'")';
				}
				
				$data['view'] .= '<tr><td class="center aligned"><input type="hidden" name="id_detail_'.$number.'" value="'.$d->id.'"><input type="hidden" name="id_product_'.$number.'" value="'.$d->id_product.'">'.$number.'</td><td>'.$d->karat_name.'<input type="hidden" name="id_karat_'.$number.'" value="'.$d->id_karat.'"></td><td>'.$d->category_name.'<input type="hidden" name="id_category_'.$number.'" value="'.$d->id_category.'"></td><td>'.$d->nama_pesanan.'<input type="hidden" name="nama_barang_'.$number.'" value="'.$d->nama_pesanan.'"></td><td style="padding:0"><input class="form-pos" type="text" name="product_weight_'.$number.'" value="'.$d->product_weight.'" readonly></td><td style="padding:0"><input class="form-pos" type="text" name="product_price_'.$number.'" id="input_modal_1_'.$number.'" onkeyup=jualToCurrency("input_modal_1_","'.$number.'") '.$action.' value="'.$d->harga_jual.'"></td></tr>';
				
				$number = $number + 1;
			}
		
			$data['view'] .= '</tbody></table><div class="ui grid" style="padding-top:15px;padding-bottom:15px">
							<div class="eight wide column ket-bawah right aligned" style="padding-bottom:0;padding-top:0">TOTAL (IDR)</div>
							<div class="eight wide column ket-bawah right aligned" id="total_modal" style="padding-bottom:0;padding-top:0">0.000</div>
							<input type="hidden" name="total_modal_hidden" id="total_modal_hidden" value=""> 
						</div>';
		}else if($d->status == 'C'){
			$data['view'] .= '<table id="modal_data_tabel" class="ui celled table" cellspacing="0" width="100%"><thead><tr class="center aligned"><th style="width:30px;">No</th><th style="width:100px;">Karat</th><th style="width:100px;">Kelompok</th><th>Nama Barang</th><th style="width:100px;">Berat</th><th style="width:150px;">Harga</th></tr></thead><tbody>';
			
			$number = 1;
			
			foreach($data_detail_pesanan as $d){
				$next = $number+1;
				
				$action = '';
				if($number < count($data_detail_pesanan)){
					$action =  'onkeydown=entToTabModal("input_modal_1_'.$next.'")';
				}
				
				$data['view'] .= '<tr><td class="center aligned"><input type="hidden" name="id_detail_'.$number.'" value="'.$d->id.'"><input type="hidden" name="id_product_'.$number.'" value="'.$d->id_product.'">'.$number.'</td><td>'.$d->karat_name.'<input type="hidden" name="id_karat_'.$number.'" value="'.$d->id_karat.'"></td><td>'.$d->category_name.'<input type="hidden" name="id_category_'.$number.'" value="'.$d->id_category.'"></td><td>'.$d->nama_pesanan.'<input type="hidden" name="nama_barang_'.$number.'" value="'.$d->nama_pesanan.'"></td><td class="right aligned">'.number_format($d->product_weight, 2).'</td><td class="right aligned">'.number_format($d->harga_jual, 0).'</td></tr>';
				
				$number = $number + 1;
			}
		
			$data['view'] .= '</tbody></table>';
		}
		
		if($d->status != 'C' && $d->status != 'X'){
		
			$data['view'] .= '</div></div></form></div></div><div class="actions"><div id="btn-save-modal" class="ui primary right labeled icon button" onclick=saveTransModal()>Simpan<i class="save icon"></i></div></div>';
		
		}
		
		$data['success'] = TRUE;
		echo json_encode($data);
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
	
	public function date_to_string($tanggal_mentah){
		$dateArray = explode('-', $tanggal_mentah);
		$reportTanggal = $dateArray[0];
		$reportMonth = $dateArray[1];
		$reportTahun = $dateArray[2];
			
		switch($reportMonth){
			case "1":
				$reportBulan = 'January';
				break;
			case "2":
				$reportBulan = 'February';
				break;
			case "3":
				$reportBulan = 'March';
				break;
			case "4":
				$reportBulan = 'April';
				break;
			case "5":
				$reportBulan = 'May';
				break;
			case "6":
				$reportBulan = 'June';
				break;
			case "7":
				$reportBulan = 'July';
				break;
			case "8":
				$reportBulan = 'August';
				break;
			case "9":
				$reportBulan = 'September';
				break;
			case "10":
				$reportBulan = 'October';
				break;
			case "11":
				$reportBulan = 'November';
				break;
			case "12":
				$reportBulan = 'December';
				break;
		}
			
		$reportTime = $reportTanggal.' '.$reportBulan.' '.$reportTahun;
		return $reportTime;
	}
}

