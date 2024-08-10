<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_beli extends CI_Controller {	
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect('C_home_pos');
		}
		
		$this->load->model('M_login','ml');
		$this->load->model('M_karat','mk');
		$this->load->model('M_kelompok_barang','mc');
		$this->load->model('M_product','mp');
		$this->load->model('M_pos','mt');
		$this->load->model('M_mutasi','mm');
		
		$GLOBALS['kasir'] = 1;
	}
	
	public function index(){
		$active_date = $this->mt->get_tanggal_aktif($GLOBALS['kasir']);
		$tanggal_aktif = $active_date[0]->tanggal_aktif;
		$data['tanggal_do'] = $tanggal_aktif;
		$data['tanggal_kini'] = date('Y-m-d').' 00:00:00';
		
		$tanggal_aktif = strtotime($tanggal_aktif);
		$bulan = date('m',$tanggal_aktif);
		
		switch($bulan){
			case "1":
				$aktif_bulan = 'January';
				break;
			case "2":
				$aktif_bulan = 'February';
				break;
			case "3":
				$aktif_bulan = 'March';
				break;
			case "4":
				$aktif_bulan = 'April';
				break;
			case "5":
				$aktif_bulan = 'May';
				break;
			case "6":
				$aktif_bulan = 'June';
				break;
			case "7":
				$aktif_bulan = 'July';
				break;
			case "8":
				$aktif_bulan = 'August';
				break;
			case "9":
				$aktif_bulan = 'September';
				break;
			case "10":
				$aktif_bulan = 'October';
				break;
			case "11":
				$aktif_bulan = 'November';
				break;
			case "12":
				$aktif_bulan = 'December';
				break;
		}
		
		$tanggal = date('d',$tanggal_aktif);
		$tahun = date('Y',$tanggal_aktif);
		
		$data['tanggal_aktif'] = $tanggal.' '.$aktif_bulan.' '.$tahun;
		$data['karat'] = $this->mk->get_karat_srt();
		$data['category'] = $this->mc->get_product_category();
		
		$this->load->view('pos/V_beli',$data);
	}
	
	public function get_product_from($sell_date,$product_id,$row_id){
		$sell_date = str_replace('%20',' ',$sell_date);
		$stockOutDate = $this->date_to_format($sell_date);
		$sell_date = date("Y-m-d",$stockOutDate).' 23:59:59';
		$do_date = date("Y-m-d",$stockOutDate).' 00:00:00';
		
		$product_id = str_replace('_','',$product_id);
		
		if($row_id > 1){
			$filter_product = '';
			if($row_id > 2){
				for($i = 1; $i < $row_id; $i++ ){
					$id_filter = $this->input->post('id_product_'.$i);
					if($i == 1){
						$filter_product .= '"'.$id_filter.'"';
					}else{
						$filter_product .= ',"'.$id_filter.'"';
					}
				}
			}else{
				$id_filter = $this->input->post('id_product_1');
				$filter_product = '"'.$id_filter.'"';
			}
			
			$data_product = $this->mp->get_product_date_buy_2($sell_date,$product_id,$filter_product);
		}else{
			$data_product = $this->mp->get_product_date_buy($sell_date,$product_id);
		}
		
		if(count($data_product) == 1){
			$data['found'] = 'single';
			
			$data['id'] = $data_product[0]->id;
			
			$this->validate_pindah_after($sell_date,$data_product[0]->id);
			
			$data['id_category'] = $data_product[0]->id_category;
			$data['nama_barang'] = $data_product[0]->product_name;
			$data['berat_barang'] = $data_product[0]->product_weight;
			$data['karat_barang'] = $data_product[0]->id_karat;
			
			$data['asal_barang'] = $data_product[0]->from_name;
			$data['ket_asal_barang'] = $data_product[0]->product_from_desc;
			
			$harga_emas = $this->mt->get_do_by_date($do_date);
			if($harga_emas == 0){
				$harga_emas = $this->mt->get_last_do();
			}
			
			$id_karat = $data_product[0]->id_karat;
			$berat_barang = $data_product[0]->product_weight;
			$data_karat = $this->mt->get_harga_struk_by_id($id_karat,$berat_barang);
			
			foreach($data_karat as $k){
				$buy = $harga_emas * $k->min_persen_beli / 100;
			}
			
			$harga_beli = $buy * $data_product[0]->product_weight;
			$harga_beli = $harga_beli / 1000;
			$harga_beli = ceil($harga_beli);
			$harga_beli = $harga_beli * 1000;
			
			$data['harga_beli'] = number_format($harga_beli,0,".",",");
		}else if(count($data_product) > 1){
			$data['found'] = 'not_single';
	
			$data['view'] = '<i class="close icon"></i><div class="header">List Barang</div><div class="content"><table class="ui celled table" id="modal-table" style="width:100%"><thead class="center aligned"><tr><th>No</th><th>ID</th><th>ID Lama</th><th>Nama Barang</th><th>Karat</th><th>Berat</th><th>Act</th></tr></thead><tbody>';
			
			$number = 1;
			foreach($data_product as $d){
				$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$d->id.'</td><td>'.$d->id_lama.'</td><td>'.$d->product_name.'</td><td>'.$d->karat_name.'</td><td class="right aligned">'.number_format($d->product_weight, 2).'</td><td class="center aligned"><button type="button" class="ui linkedin pilih button" onclick=setProduct("'.$d->id.'","'.$row_id.'")><i class="hand point up outline icon"></i> Pilih</button></td>';
				
				$number = $number + 1;
			}
			
			$data['view'] .= '</tbody></table></div>';
		}else if(count($data_product) ==0){
			$data['found'] = 'not_found';
		}
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	private function validate_pindah_after($pindah_box_date,$product_id){
		$cek_pindah_after = $this->mp->get_pindah_box_after($pindah_box_date,$product_id);
		if(count($cek_pindah_after) > 0){
			$data['view'] = '<div class="modal-dialog modal-dialog-centered modal-lg" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">Warning</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body">';
			
			$data['view'] .= '<div class="row"><div class="col-lg-12 col-md-12"><div class="div-warning text-center"><div class="img-warning"><img src="'.base_url().'assets/images/warning.png"></div><div class="span-warning">Andah Sudah Pernah Melakukan Transaksi Pindah Box Melewati Tanggal Yang Anda Pilih. Pilih Tanggal Lain atau Hubungi Tim IT Untuk Penanganan Lebih Lanjut</div></div></div><div class="col-lg-12 col-md-12"><table class="table table-striped table-bordered table-responsive-md" id="modal-table" cellspacing="0" width="99%" style="margin-left:2%"><thead><tr><th>No</th><th>Tgl Pindah Box</th><th>ID</th><th>Dari Box</th><th>Ke Box</th></tr></thead><tbody>';
			
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
				
				$data['view'] .= '<tr><td class="text-center">'.$number.'</td><td>'.$tanggal_pindah_box.'</td><td>'.$c->id.'</td><td>'.$box_number_from.'</td><td>'.$box_number_to.'</td>';
				
				$number = $number + 1;
			}
			
			$data['view'] .= '</tbody></table></div></div></div>';
			$data['view'] .= '<div class="modal-footer"><button type="button" id="mdl-close" class="btn btn-outline-dark" data-dismiss="modal">Close</button></div></div></div>';
			
			$data['success'] = FALSE;
			echo json_encode($data);
			exit();
		}
	}
	
	public function tambah_baris($row_number){
		$this->validate_tambah_baris($row_number);
		$row_number = $row_number + 1;
		
		$data['view'] = '<tr id="pos_tr_'.$row_number.'"><td class="center aligned">'.$row_number.'</td><td><select class="custom-select" name="id_karat_'.$row_number.'" id="input_'.$row_number.'_1" onkeydown=entToTab("'.$row_number.'","1") onchange=getKaratHdn("'.$row_number.'")><option value="">Karat</option>';
		
		$karat = $this->mk->get_karat_srt();
		
		foreach($karat as $k){
			$data['view'] .= '<option value="'.$k->id.'">'.$k->karat_name.'</option>';
		}
		
		$data['view'] .= '</select><input type="hidden" name="id_karat_hdn_'.$row_number.'" id="input_hdn_'.$row_number.'_1" value=""></td><td><input class="form-pos" type="text" onblur=getProduct("'.$row_number.'") onkeydown=entToTab("'.$row_number.'","2") name="id_product_'.$row_number.'" id="input_'.$row_number.'_2" autocomplete="off"></td><td><input class="form-pos" type="text" onkeydown=entToTab("'.$row_number.'","3") name="product_desc_'.$row_number.'" id="input_'.$row_number.'_3" autocomplete="off"></td><td><select class="custom-select" name="id_category_'.$row_number.'" id="input_'.$row_number.'_4" onkeydown=entToTab("'.$row_number.'","4") onchange=getKelompokHdn("'.$row_number.'")><option value="">Kelompok</option>';
		
		$category = $this->mc->get_product_category();
		
		foreach($category as $c){
			$data['view'] .= '<option value="'.$c->id.'">'.$c->category_name.'</option>';
		}
		
		$data['view'] .= '</select><input type="hidden" name="id_category_hdn_'.$row_number.'" id="input_hdn_'.$row_number.'_4" value=""></td><td><input class="form-pos" type="number" onkeydown=entToTab("'.$row_number.'","5") name="product_pcs_'.$row_number.'" id="input_'.$row_number.'_5" autocomplete="off"></td><td><input class="form-pos" type="text" onkeydown=entToTab("'.$row_number.'","6") name="product_weight_'.$row_number.'" onkeyup=weightToCurrency("'.$row_number.'") id="input_'.$row_number.'_6" autocomplete="off"></td><td><input class="form-pos" type="text" onblur=priceToCurrency("'.$row_number.'") onkeyup=countTotal() name="product_price_'.$row_number.'" id="input_'.$row_number.'_7" onkeydown=entToTab("'.$row_number.'","7") autocomplete="off"></td><td><input class="form-pos" type="text" name="product_avg_'.$row_number.'" id="input_'.$row_number.'_8" onkeydown=entToTab("'.$row_number.'","8") readonly></td></tr>';
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function validate_tambah_baris($row_number){
		$data = array();
		$data['inputerror'] = '<i class="close icon"></i><div class="header">Warning</div><div class="content"><div class="ui icon header center aligned"><i class="exclamation triangle red icon"></i>Gagal Insert Data</div><div class="ui negative message" style="text-align:center">';
		$data['inputerror'] .= '<ul class="list"></ul>';
		$data['pesan_error'] = '';
		$data['success'] = TRUE;
		
		$sell_date = $this->input->post('tanggal_aktif');
		$sellDate = $this->date_to_format($sell_date);
		$sell_date = date("Y-m-d",$sellDate).' 23:59:59';
		$do_date = date("Y-m-d",$sellDate).' 00:00:00';
		
		for($i = 1; $i <= $row_number; $i++){
			$id_karat = $this->input->post('id_karat_hdn_'.$i);
			if($id_karat == ''){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Karat Tidak Boleh Kosong!</li>';
				$data['inputerror'] .= '</ul></div></div></div>';
				echo json_encode($data);
				exit();
			}
			
			$product_id = $this->input->post('id_product_'.$i);
			
			if($i > 1){
				$filter_product = '';
				if($i > 2){
					for($a = 1; $a < $i; $a++ ){
						$id_filter = $this->input->post('id_product_'.$a);
						if($a == 1){
							$filter_product .= '"'.$id_filter.'"';
						}else{
							$filter_product .= ',"'.$id_filter.'"';
						}
					}
				}else{
					$id_filter = $this->input->post('id_product_1');
					$filter_product = '"'.$id_filter.'"';
				}
				
				$data_product = $this->mp->get_product_date_buy_val_2($sell_date,$product_id,$filter_product);
			}else{
				$data_product = $this->mp->get_product_date_buy_val($sell_date,$product_id);
			}
			
			if($product_id != '' && count($data_product) > 1){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>ID Product Salah!</li>';
				$data['inputerror'] .= '</ul></div></div></div>';
				echo json_encode($data);
				exit();
			}
			
			$harga_emas = $this->mt->get_do_by_date($do_date);
		
			if($harga_emas == 0){
				$harga_emas = $this->mt->get_last_do();
			}
			
			$keterangan = $this->input->post('product_desc_'.$i);
			if($keterangan == ''){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Keterangan Tidak Boleh Kosong!</li>';
				$data['inputerror'] .= '</ul></div></div></div>';
				echo json_encode($data);
				exit();
			}
			
			$kelompok = $this->input->post('id_category_hdn_'.$i);
			if($kelompok == ''){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Kelompok Tidak Boleh Kosong!</li>';
				$data['inputerror'] .= '</ul></div></div></div>';
				echo json_encode($data);
				exit();
			}
			
			$pcs = $this->input->post('product_pcs_'.$i);
			if($pcs == '' || $pcs == 0 || $pcs < 0){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Jumlah Pcs Tidak Boleh Kosong!</li>';
				$data['inputerror'] .= '</ul></div></div></div>';
				echo json_encode($data);
				exit();
			}
			
			$berat_barang = $this->input->post('product_weight_'.$i);
			$berat_barang = str_replace(',','',$berat_barang);
			if($berat_barang == '' || $berat_barang == 0){
				$data['success'] = FALSE;
				$data['inputerror'] .= '<li>Berat Tidak Boleh Kosong!</li>';
				$data['inputerror'] .= '</ul></div></div></div>';
				echo json_encode($data);
				exit();
			}
			
			$data_karat = $this->mt->get_harga_struk_by_id($id_karat,$berat_barang);
			$harga_emas = $harga_emas + 4000;
			
			foreach($data_karat as $k){
				$min_sell = $harga_emas * $k->min_persen_beli / 100;
				$max_sell = $harga_emas * $k->max_persen_beli / 100;
			}
			
			$min_harga_jual = $min_sell * $berat_barang;
			$min_harga_jual = $min_harga_jual / 1000;
			$min_harga_jual = ceil($min_harga_jual);
			$min_harga_jual = $min_harga_jual * 1000;
			
			$max_harga_jual = $max_sell * $berat_barang;
			$max_harga_jual = $max_harga_jual / 1000;
			$max_harga_jual = ceil($max_harga_jual);
			$max_harga_jual = $max_harga_jual * 1000;
			
			$product_price = $this->input->post('product_price_'.$i);
			$product_price = str_replace('[','',$product_price);
			$product_price = str_replace(',','',$product_price);
			
			if($product_price < $min_harga_jual || $product_price > $max_harga_jual){
				$data['success'] = FALSE;
				$data['inputerror'] .= 'Harga Melewati Batas Min/Max.<br>Harga Min : '.number_format($min_harga_jual,0,".",",").'<br>Harga Max '.number_format($max_harga_jual,0,".",",");
				$data['inputerror'] .= '</ul></div></div></div>';
				echo json_encode($data);
				exit();
			}
		}
		
		if($data['success'] == FALSE){
			echo json_encode($data);
			exit();
		}
	}
	
	public function ke_pembayaran($row_number){
		$this->validate_tambah_baris($row_number);
		
		$total_price = 0;
		
		$kasir = $this->mt->get_user_cs();
		
		$data['view'] = '<div class="eleven wide column"><table class="ui celled table" cellspacing="0" width="100%"><thead><tr style="text-align:center"><th style="width:20px">No</th><th style="width:80px;">Karat</th><th style="width:200px;">Keterangan</th><th style="width:120px;">Kelompok</th><th style="width:80px;">Pcs</th><th style="width:100px;">Berat</th><th>Total Harga</th></tr></thead><tbody>';
		
		for($i = 1; $i <= $row_number; $i++){
			$data['view'] .= '<tr>';
			$data['view'] .= '<td class="center aligned">'.$i.'</td>';
			
			$karat = $this->input->post('id_karat_hdn_'.$i);
			$karat = $this->mk->get_karat_name_by_id($karat);
			$data['view'] .= '<td class="center aligned">'.$karat.'</td>';
			
			$ket = $this->input->post('product_desc_'.$i);
			$data['view'] .= '<td>'.$ket.'</td>';
			
			$kelompok = $this->input->post('id_category_hdn_'.$i);
			$kelompok = $this->mc->get_category_name_by_id($kelompok);
			$data['view'] .= '<td class="center aligned">'.$kelompok.'</td>';
			
			$pcs = $this->input->post('product_pcs_'.$i);
			$data['view'] .= '<td class="right aligned">'.$pcs.'</td>';
			
			$berat = $this->input->post('product_weight_'.$i);
			$data['view'] .= '<td class="right aligned">'.$berat.'</td>';
			
			$product_price = $this->input->post('product_price_'.$i);
			$data['view'] .= '<td class="right aligned">'.$product_price.'</td>';
			$product_price = str_replace('[','',$product_price);
			$product_price = str_replace(',','',$product_price);
			
			$total_price = $total_price + $product_price;
			
			$data['view'] .= '</tr>';
		}
		
		$data['view'] .= '</tbody></table></div>';
		
		$data['view'] .= '<div class="five wide column">';
		$data['view'] .= '<div class="field"><label>Customer Service</label><select id="input_data_1" name="id_cs"><option value="">-- Nama Customer Service --</option>';
		
		foreach($kasir as $k){
			$data['view'] .= '<option value="'.$k->username.'">'.$k->nama_karyawan.'</option>';
		}
										
		$data['view'] .= '</select></div>';
		$data['view'] .= '<div class="field"><label>No. Telp/HP</label><input class="angka-bayar" type="text" name="no_telp" id="input_data_2" value="" onkeydown=entToTabBayar(3) onblur=findCustomer() autocomplete="off"></div><div class="field"><label>Alamat</label><input type="text"  class="angka-bayar" name="alamat_cust" id="input_data_3" value="" onkeydown=entToTabBayar(4) autocomplete="off"></div><div class="field"><label>Nama Pelanggan</label><input type="text" class="angka-bayar" name="nama_cust" id="input_data_4" value="" onkeydown=entToTabBayar(5) autocomplete="off"></div><div class="field"><label>Total Beli</label><input type="text" class="form-control right aligned" style="font-size:26px;font-weight:bold;padding:5px 8px;" name="total_price" id="total_price" value="'.number_format($total_price,0,".",",").'" onkeydown=entToTabBayar(7) readonly></div></div>';
		
		$data['view'] .= '<div class="sixteen wide column right aligned"><div class="ui positive button" onclick=saveTrans("P")><i class="print icon"></i> [F8] Simpan dan Cetak</div><div class="ui primary button" onclick=saveTrans("NP")><i class="save icon"></i> [F2] Simpan Saja</div></div></div><div class="ui grid tall stacked segment" style="width:100%;margin:0"><div class="eight wide column"><div class="ui grid" style="padding-top:15px;padding-bottom:15px"><div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">PgUp</div><div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: STEP SEBELUMNYA</div><div class="four wide column ket-bawah" style="padding-bottom:0;padding-top:0">F5</div><div class="twelve wide column ket-bawah" style="padding-bottom:0;padding-top:0">: REFRESH HALAMAN</div><div class="four wide column ket-bawah" style="padding-bottom:0;padding-top:0">Esc</div><div class="twelve wide column ket-bawah" style="padding-bottom:0;padding-top:0">: HOME</div></div></div><div class="eight wide column"><div class="ui grid" style="padding-top:15px;padding-bottom:15px"><div class="eight wide column ket-bawah right aligned" style="padding-bottom:0;padding-top:0"></div><div class="eight wide column ket-bawah right aligned" id="total_jual" style="padding-bottom:0;padding-top:0"></div></div></div>';
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function find_customer(){
		$data['success'] = FALSE;
		$customer_phone = $this->input->post('no_telp');
		
		if($customer_phone != ''){
			$data_customer = $this->mt->get_customer_by_phone2($customer_phone);
			if(count($data_customer) == 0){
				$data['success'] = FALSE;
			}else{
				$lebar = strlen($customer_phone);
				if($lebar >= 5){
					$data['customer_phone'] = $data_customer[0]->cust_phone;
					$data['customer_name'] = $data_customer[0]->cust_name;
					$data['customer_address'] = $data_customer[0]->cust_address;
					$data['success'] = TRUE;
				}
			}
		}
		
		echo json_encode($data);
	}
	
	public function save_beli($row_number,$print_flag){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		$this->validate_beli($row_number);
		
		$active_date = $this->mt->get_tanggal_aktif($GLOBALS['kasir']);
		$tanggal_aktif = $active_date[0]->tanggal_aktif;
		$codetrans = strtotime($tanggal_aktif);
		$tanggal_filter = date('Y-m-d',$codetrans).' 23:59:59';
		$codetrans = date('ymd',$codetrans);
		
		$transactioncode = 'BR';
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
		$transnumber = $this->mt->get_trans_number_beli($tanggal_aktif,$GLOBALS['kasir']);
		
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
		
		$cust_service = $this->input->post('id_cs');
		$cust_phone = $this->input->post('no_telp');
		$cust_address = $this->input->post('alamat_cust');
		$cust_name = $this->input->post('nama_cust');
		$total_price = $this->input->post('total_price');
		$total_price = str_replace(',','',$total_price);
		$jenis_bayar = $this->mm->get_default_account('KE');
		$account_beli = $this->mm->get_default_account('BL');
		$created_by = $this->session->userdata('gold_username');
		$mutasi_desc = 'PEMBELIAN - '.$transactioncode;
		
		if($cust_phone != ''){
			$found_cust = $this->mt->find_customer($cust_phone);
			if(count($found_cust) == 0){
				$this->mt->save_customer($cust_phone,$cust_address,$cust_name);
			}else{
				$this->mt->update_customer($cust_phone,$cust_address,$cust_name);
			}
		}
		
		$this->mt->insert_main_beli($transactioncode,$GLOBALS['kasir'],$cust_service,$cust_phone,$cust_address,$cust_name,$total_price,$tanggal_aktif,$created_by);
		
		for($i = 1; $i <= $row_number; $i++){
			$product_id = $this->input->post('id_product_'.$i);
			$data_product = $this->mp->get_product_date_buy($tanggal_filter,$product_id);
			if(count($data_product) == 1){
				//$id_lengkap = explode('-',  $data_product[0]->id);
				$product_id = $data_product[0]->id;
			}else{
				$product_id = '';
			}
			
			$id_karat =  $this->input->post('id_karat_hdn_'.$i);
			$product_weight = $this->input->post('product_weight_'.$i);
			$product_weight = str_replace(',','',$product_weight);
			$product_desc = $this->input->post('product_desc_'.$i);
			$product_category = $this->input->post('id_category_hdn_'.$i);
			$product_pcs = $this->input->post('product_pcs_'.$i);
			$product_price = $this->input->post('product_price_'.$i);
			$product_price = str_replace(',','',$product_price);
			
			$this->mt->insert_detail_beli($transactioncode,$GLOBALS['kasir'],$product_id,$id_karat,$product_desc,$product_category,$product_weight,$product_pcs,$product_price,$tanggal_aktif,$created_by);
			
			$account_bl = $this->mm->get_default_account('BL');
			$account_srt = $this->mm->get_default_account('SRT');
			
			$desc = 'PEMBELIAN - NO.REG : '.$transactioncode;
			
			$this->mm->insert_mutasi_gram($sitecode,$transactioncode.'-'.$i,'In',$id_karat,$account_bl,$account_srt,$product_weight,$desc,$tanggal_aktif,$created_by);
		}
		
		$count_mutasi = 1;
		$this->mm->insert_mutasi_rupiah($sitecode,$transactioncode.'-'.$count_mutasi,'In',$jenis_bayar,$account_beli,$total_price,$mutasi_desc,$tanggal_aktif,$created_by);
		
		if($print_flag == 'P'){
			$printer = $this->mm->get_printer($GLOBALS['kasir']);
			$computer_name = $printer[0]->computer_name;
			$printer_name = $printer[0]->printer_name;
			
			//FUNGSI CETAK STRUK TRANSAKSI
			$this->load->library("EscPos.php");
			
			$connector = new Escpos\PrintConnectors\WindowsPrintConnector("smb://".$computer_name."/".$printer_name);
			$printer = new Escpos\Printer($connector);
			
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
			$printer -> text("\n");
			$printer -> text("\n");
			
			$tgl_print = $tanggal.' '.$aktif_bulan.' '.$tahun;
			$jam_print = date('H:i');
			
			$length_print = 80;
			$tgl_length = strlen($tgl_print);
			$jam_length = strlen($jam_print);
			
			$spasi_tgl = $length_print - $tgl_length;
					
			for($a=1; $a <= $spasi_tgl; $a++){
				$printer -> text(" ");
			}
			
			$printer -> text($tgl_print."\n");
			
			$printer -> text("                               ** PEMBELIAN **                             ".$jam_print."\n");
			
			$printer -> text("\n");
			$printer -> text("\n");
			$printer -> text("\n");
			$printer -> text("\n");
			
			$nomor_jual = 1;
			$total_jual = 0;
			for($i = 1; $i <= $row_number; $i++){
				$product_id = $this->input->post('id_product_'.$i);
				$data_product = $this->mp->get_product_date_buy($tanggal_filter,$product_id);
				if(count($data_product) == 1){
					$id_lengkap = explode('-',  $data_product[0]->id);
					$product_id = $id_lengkap[1];
				}else{
					$product_id = '';
				}
				
				$id_karat =  $this->input->post('id_karat_hdn_'.$i);
				$product_weight = $this->input->post('product_weight_'.$i);
				$product_desc = $this->input->post('product_desc_'.$i);
				$product_category = $this->input->post('id_category_hdn_'.$i);
				$product_pcs = $this->input->post('product_pcs_'.$i);
				$product_price = $this->input->post('product_price_'.$i);
				$product_price = str_replace(',','',$product_price);
				
				$id_cetak = $product_id;
				$karat_name = $this->mk->get_karat_name_by_id($id_karat); 
				$product_name = $product_desc;				
				$printer -> text("        ");
				
				if($id_cetak == ''){
					$printer -> text("            ");
				}else{
					$printer -> text($id_cetak);
				}
				
				$printer -> text("   ".substr($karat_name, 0, 3));
				$max_char_prod_name = 19;
				
				$printer -> text("  ".substr($product_name, 0, $max_char_prod_name));
				$jumlah_char = strlen($product_name);
				
				if($jumlah_char < $max_char_prod_name){
					$selisih = $max_char_prod_name - $jumlah_char;
					
					for($b = 1; $b<= $selisih; $b++){
						$printer -> text(" ");
					}
				}
				
				$max_pcs_tulis = 9;
				$pcs_tulis = $product_pcs.' Pcs';
				$pcs_tulis_length = strlen($pcs_tulis);
				
				if($pcs_tulis_length < $max_pcs_tulis){
					$selisih_pcs = $max_pcs_tulis - $pcs_tulis_length;
					
					for($y = 1; $y<= $selisih_pcs; $y++){
						$printer -> text(" ");
					}
				}
				
				$printer -> text($pcs_tulis);
				
				$printer -> text("  ");
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
			
			$max_char_terbilang = 42;
			$length_terbilang = strlen($terbilang);
			
			if($length_terbilang < $max_char_terbilang){
				$printer -> text("             #".$terbilang."#");
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
				
				$printer -> text("             #".$baris_satu);
				$sisa_print = $max_char_terbilang - $length_terbilang;
				
				for($g = 1; $g<= $sisa_print; $g++){
					$printer -> text(" ");
				}
				
				$printer -> text("           ");
				
				$total_length = 13;
				$total_cetak = number_format($total_jual,0,".",",");
				$ttl_length = strlen($total_cetak);
				
				$selisih_total = $total_length - $ttl_length;
				for($h = 1; $h<= $selisih_total; $h++){
					$printer -> text(" ");
				}
				
				$printer -> text(number_format($total_jual,0,".",",")."\n");
				$printer -> text("             ".$baris_dua."#\n");
			}
			
			$printer -> text("\n");
			
			$printer -> text("                                  CUST SERVICE: ".strtoupper($cust_service));
			
			$ket_length = 13;
			$cs_length = strlen($cust_service);
			
			$sisa = $ket_length - $cs_length;
			for($a = 1; $a<= $sisa; $a++){
				$printer -> text(" ");
			}
			
			$enter = 5;
			
			if(strlen($cust_name) > 11){
				$enter = $enter - 1;
			}
			
			$printer -> text("PENJUAL:".strtoupper($cust_name)."\n");
			
			$printer -> text("\n");
			
			if(strlen($cust_address) > 11){
				$enter = $enter - 1;
			}
			
			$printer -> text("                                                                     ".strtoupper($cust_address)."\n");
			$printer -> text("\n");
			$printer -> text("                                                ".strtoupper($created_by));
			
			$cs_length = strlen($created_by);
			
			$sisa = $ket_length - $cs_length;
			for($a = 1; $a<= $sisa; $a++){
				$printer -> text(" ");
			}
			
			if(strlen($cust_phone) > 13){
				$enter = $enter - 1;
			}
			
			$printer -> text("      ".strtoupper($cust_phone)."\n");
			
			for($i = 1;$i<=$enter;$i++){
				$printer -> text("\n");
			}
			
			$printer -> close();
		}
		
		$this->db->trans_complete();
		
		$data['trans_id'] = $transactioncode;
		$data['success'] = true;
		echo json_encode($data);
	}
	
	private function validate_beli($row_number){
		$data = array();
		$data['inputerror'] = '<i class="close icon"></i><div class="header">Warning</div><div class="content"><div class="ui icon header center aligned"><i class="exclamation triangle red icon"></i>Gagal Save Data</div><div class="ui negative message">';
		$data['inputerror'] .= '<ul class="list"></ul>';
		$data['pesan_error'] = '';
		$data['success'] = TRUE;
		
		$id_cs = $this->input->post('id_cs');
		$no_telp = $this->input->post('no_telp');
		$alamat_cust = $this->input->post('alamat_cust');
		$nama_cust = $this->input->post('nama_cust');
		$total_price = $this->input->post('total_price');
		$total_price = str_replace(',','',$total_price);
		
		$price_pcs_total = 0;
		
		for($i = 1; $i <= $row_number; $i++){
			$price_pcs = $this->input->post('product_price_'.$i);
			$price_pcs = str_replace(',','',$price_pcs);
			
			$price_pcs_total = $price_pcs_total + $price_pcs;
		}
		
		if($price_pcs_total != $total_price){
			$data['success'] = FALSE;
			$data['inputerror'] .= '<li>Total Bayar Tidak Sama Dengan Jumlah Total Harga Barang!</li>';
		}
		
		if($id_cs == ''){
			$data['success'] = FALSE;
			$data['inputerror'] .= '<li>Nama Customer Service Harus Diisi!</li>';
		}
		
		if($no_telp == ''){
			$data['success'] = FALSE;
			$data['inputerror'] .= '<li>Nomor Telepon Pelanggan Harus Diisi!</li>';
		}
		
		if($alamat_cust == ''){
			$data['success'] = FALSE;
			$data['inputerror'] .= '<li>Alamat Pelanggan Harus Diisi!</li>';
		}
		
		if($nama_cust == ''){
			$data['success'] = FALSE;
			$data['inputerror'] .= '<li>Nama Pelanggan Harus Diisi!</li>';
		}
		
		if($total_price == '' || $total_price == 0){
			$data['success'] = FALSE;
			$data['inputerror'] .= '<li>Total Tidak Boleh Kosong!</li>';
		}
		
		if($data['success'] == FALSE){
			echo json_encode($data);
			exit();
		}
	}
	
	public function lihat_transaksi(){
		$active_date = $this->mt->get_tanggal_aktif($GLOBALS['kasir']);
		$tanggal_aktif = $active_date[0]->tanggal_aktif;
		
		$data['view'] = '<i class="close icon"></i><div class="header">List Penjualan</div><div class="content">';
		
		$data['view'] .= '<table class="ui celled table" id="modal-table" cellspacing="0" width="100%"><thead><tr><th>No</th><th>ID</th><th>Total</th><th>Act</th></tr></thead><tbody>';
		
		$data_product = $this->mt->get_trans_beli($tanggal_aktif,$GLOBALS['kasir']);
		
		$number = 1;
		foreach($data_product as $d){
			$data['view'] .= '<tr><td class="text-center">'.$number.'</td><td>'.$d->transaction_code.'</td><td class="text-right">'.number_format($d->total_price, 2).'</td><td class="text-center"><button type="button" class="ui linkedin pilih button" onclick=getProductJual("'.$d->id.'")><i class="hand point up outline icon"></i> Pilih</button></td>';
			
			$number = $number + 1;
		}
		
		$data['view'] .= '</tbody></table></div>';
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function get_product_beli($id_main){
		$transactioncode = $this->mt->get_idtrans_beli($id_main);
		
		$data['view'] = '';
		$data_product = $this->mt->get_product_beli($transactioncode);
		
		$number = 0;
		$total = 0;
		foreach($data_product as $dp){
			$number = $number + 1;
			
			$data['view'] .= '<tr id="pos_tr_'.$number.'"><td class="center aligned">'.$number.'</td><td><input class="form-control form-control-sm form-pos" name="id_karat_'.$number.'" id="input_'.$number.'_1" onkeydown=entToTab("'.$number.'","1") value="'.$dp->karat_name.'" readonly></td><td><input class="form-control form-control-sm form-pos" type="text" style="background:#FFF" onkeydown=entToTab("'.$number.'","2") name="id_product_'.$number.'" id="input_'.$number.'_2" value="'.$dp->id_product.'" readonly></td><td><input class="form-control form-control-sm form-pos" type="text" onkeydown=entToTab("'.$number.'","3") name="product_desc_'.$number.'" id="input_'.$number.'_3" value="'.$dp->nama_product.'" readonly></td><td><input class="form-control form-control-sm form-pos" name="id_category_'.$number.'" id="input_'.$number.'_4" onkeydown=entToTab("'.$number.'","4") value="'.$dp->category_name.'" readonly></td><td><input class="form-control form-control-sm form-pos" type="number" style="background:#FFF" onkeydown=entToTab("'.$number.'","5") name="product_pcs_'.$number.'" id="input_'.$number.'_5" value="'.$dp->product_pcs.'" readonly></td><td><input class="form-control form-control-sm form-pos" type="text" style="background:#FFF" onkeydown=entToTab("'.$number.'","6") name="product_weight_'.$number.'" id="input_'.$number.'_6" value="'.number_format($dp->product_weight,3,".",",").'" readonly></td><td><input class="form-control form-control-sm form-pos" type="text" name="product_price_'.$number.'" id="input_'.$number.'_7" onkeydown=entToTab("'.$number.'","7") value="'.number_format($dp->product_price,0,".",",").'" readonly></td><td><input class="form-control form-control-sm form-pos" type="text" name="product_avg_'.$number.'" id="input_'.$number.'_8" style="background:#FFF" value="'.number_format($dp->product_price / $dp->product_weight,3,".",",").'" readonly></td></tr>';
			
			$total = $total + $dp->product_price;
		}
		
		$data['row_number'] = $number;
		$data['total_price'] = number_format($total,0,".",",");
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function print_transaksi($id_main){
		date_default_timezone_set("Asia/Jakarta");
		
		$data_main = $this->mt->get_main_pembelian($id_main);
		$id_detail = $data_main[0]->transaction_code;
		$cust_service = $data_main[0]->cust_service;
		$cust_name = $data_main[0]->cust_name;
		$cust_address = $data_main[0]->cust_address;
		$cust_phone = $data_main[0]->cust_phone;
		$created_by = $data_main[0]->created_by;
		$tanggal_aktif = $data_main[0]->trans_date;
		
		$tanggal_filter = strtotime($tanggal_aktif);
		$tanggal_filter = date('Y-m-d').' 23:59:59';
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
		
		$tgl_print = $tanggal.' '.$aktif_bulan.' '.$tahun;
		$jam_print = date('H:i');
		
		$printer = $this->mm->get_printer($GLOBALS['kasir']);
		$computer_name = $printer[0]->computer_name;
		$printer_name = $printer[0]->printer_name;
		
		//FUNGSI CETAK STRUK TRANSAKSI
		$this->load->library("EscPos.php");
		
		$connector = new Escpos\PrintConnectors\WindowsPrintConnector("smb://".$computer_name."/".$printer_name);
		$printer = new Escpos\Printer($connector);
		
		$printer -> text("\n");
		$printer -> text("\n");
		$printer -> text("\n");
		$printer -> text("\n");
		$printer -> text("\n");
		$printer -> text("\n");
		$printer -> text("\n");
		$printer -> text("\n");
		$printer -> text("\n");
		
		$data_product = $this->mt->get_product_beli($id_detail);
		
		$length_print = 80;
		$tgl_length = strlen($tgl_print);
		$jam_length = strlen($jam_print);
		
		$spasi_tgl = $length_print - $tgl_length;
				
		for($a=1; $a <= $spasi_tgl; $a++){
			$printer -> text(" ");
		}
		
		$printer -> text($tgl_print."\n");
		
		$printer -> text("                          ** PEMBELIAN (REPRINT) **                        ".$jam_print."\n");
		
		$printer -> text("\n");
		$printer -> text("\n");
		$printer -> text("\n");
		$printer -> text("\n");
		
		$nomor_jual = 1;
		$total_jual = 0;
		foreach($data_product as $dp){
			$product_id = $dp->id_product;
			$data_product = $this->mp->get_product_date_buy($tanggal_filter,$product_id);
			if(count($data_product) == 1){
				$id_lengkap = explode('-',  $data_product[0]->id);
				$product_id = $id_lengkap[1];
			}else{
				$product_id = '';
			}
			
			$id_karat =  $dp->id_karat;
			$product_weight = $dp->product_weight;
			$product_desc = $dp->nama_product;
			$product_pcs = $dp->product_pcs;
			$product_price = $dp->product_price;
			$product_price = str_replace(',','',$product_price);
			
			$id_cetak = $product_id;
			$karat_name = $this->mk->get_karat_name_by_id($id_karat); 
			$product_name = $product_desc;				
			$printer -> text("        ");
			
			if($id_cetak == ''){
				$printer -> text("            ");
			}else{
				$printer -> text($id_cetak);
			}
			
			$printer -> text("   ".substr($karat_name, 0, 3));
			$max_char_prod_name = 19;
			
			$printer -> text("  ".substr($product_name, 0, $max_char_prod_name));
			$jumlah_char = strlen($product_name);
			
			if($jumlah_char < $max_char_prod_name){
				$selisih = $max_char_prod_name - $jumlah_char;
				
				for($b = 1; $b<= $selisih; $b++){
					$printer -> text(" ");
				}
			}
			
			$max_pcs_tulis = 9;
			$pcs_tulis = $product_pcs.' Pcs';
			$pcs_tulis_length = strlen($pcs_tulis);
			
			if($pcs_tulis_length < $max_pcs_tulis){
				$selisih_pcs = $max_pcs_tulis - $pcs_tulis_length;
				
				for($y = 1; $y<= $selisih_pcs; $y++){
					$printer -> text(" ");
				}
			}
			
			$printer -> text($pcs_tulis);
			
			$printer -> text("  ");
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
		
		$max_char_terbilang = 42;
		$length_terbilang = strlen($terbilang);
		
		if($length_terbilang < $max_char_terbilang){
			$printer -> text("             #".$terbilang."#");
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
			
			$printer -> text("             #".$baris_satu);
			$sisa_print = $max_char_terbilang - $length_terbilang;
			
			for($g = 1; $g<= $sisa_print; $g++){
				$printer -> text(" ");
			}
			
			$printer -> text("           ");
			
			$total_length = 13;
			$total_cetak = number_format($total_jual,0,".",",");
			$ttl_length = strlen($total_cetak);
			
			$selisih_total = $total_length - $ttl_length;
			for($h = 1; $h<= $selisih_total; $h++){
				$printer -> text(" ");
			}
			
			$printer -> text(number_format($total_jual,0,".",",")."\n");
			$printer -> text("             ".$baris_dua."#\n");
		}
		
		$printer -> text("\n");
		
		$printer -> text("                                  CUST SERVICE: ".strtoupper($cust_service));
			
		$ket_length = 13;
		$cs_length = strlen($cust_service);
		
		$sisa = $ket_length - $cs_length;
		for($a = 1; $a<= $sisa; $a++){
			$printer -> text(" ");
		}
		
		$printer -> text("PENJUAL:".strtoupper($cust_name)."\n");
		
		$printer -> text("\n");
		
		$printer -> text("                                                                     ".strtoupper($cust_address)."\n");
		$printer -> text("\n");
		$printer -> text("                                                ".strtoupper($created_by));
		
		$cs_length = strlen($created_by);
		
		$sisa = $ket_length - $cs_length;
		for($a = 1; $a<= $sisa; $a++){
			$printer -> text(" ");
		}
		
		$printer -> text("      ".strtoupper($cust_phone)."\n");
		
		for($i = 1;$i<=30;$i++){
			$printer -> text("\n");
		}
		
		$printer -> close();
		
		$data['trans_id'] = $id_detail;
		$data['success'] = true;
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
	
	public function kekata($x){
		$x = abs($x);
		$angka = array("", "satu", "dua", "tiga", "empat", "lima",
		"enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$temp = "";
		if ($x <12) {
			$temp = " ". $angka[$x];
		} else if ($x <20) {
			$temp = $this->kekata($x - 10). " belas";
		} else if ($x <100) {
			$temp = $this->kekata($x/10)." puluh". $this->kekata($x % 10);
		} else if ($x <200) {
			$temp = " seratus" . $this->kekata($x - 100);
		} else if ($x <1000) {
			$temp = $this->kekata($x/100) . " ratus" . $this->kekata($x % 100);
		} else if ($x <2000) {
			$temp = " seribu" . $this->kekata($x - 1000);
		} else if ($x <1000000) {
			$temp = $this->kekata($x/1000) . " ribu" . $this->kekata($x % 1000);
		} else if ($x <1000000000) {
			$temp = $this->kekata($x/1000000) . " juta" . $this->kekata($x % 1000000);
		} else if ($x <1000000000000) {
			$temp = $this->kekata($x/1000000000) . " milyar" . $this->kekata(fmod($x,1000000000));
		} else if ($x <1000000000000000) {
			$temp = $this->kekata($x/1000000000000) . " trilyun" . $this->kekata(fmod($x,1000000000000));
		}     
			return $temp;
	}
	
	public function terbilang($x, $style=4) {
		if($x<0) {
			$hasil = "minus ". trim($this->kekata($x));
		} else {
			$hasil = trim($this->kekata($x));
		}     
		switch ($style) {
			case 1:
				$hasil = strtoupper($hasil);
				break;
			case 2:
				$hasil = strtolower($hasil);
				break;
			case 3:
				$hasil = ucwords($hasil);
				break;
			default:
				$hasil = ucfirst($hasil);
				break;
		}     
		return $hasil;
	}
}