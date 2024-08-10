<?php
include($_SERVER['DOCUMENT_ROOT']."/gms/application/libraries/db_backup_library.php");
defined('BASEPATH') OR exit('No direct script access allowed');

class C_mail_selfi extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->library('M_pdf');
		$this->load->model('M_karat','mk');
		$this->load->model('M_product','mp');
		$this->load->model('M_mutasi','mm');
		$this->load->model('M_kelompok_barang','mc');
		$this->load->model('M_pos','mt');
		$this->load->model('M_box','mb');
		$this->load->model('M_bayar','my');
		$this->load->model('M_user','mu');
		
		$GLOBALS['kasir'] = 1;
	}
	
	public function index(){
		$dbBackup = new db_backup;
		$dbBackup->connect("localhost","root","","gold");
		$dbBackup->backup();
		$dbBackup->download();
		$tanggal = date("Ymd");
		$dbBackup->save("D://",$tanggal);
		
		$site_name = $this->mm->get_site_name();
		$tanggal_aktif = date("Y-m-d")." 00:00:00";
		
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
		
		$tanggal_aktif = $tanggal.' '.$aktif_bulan.' '.$tahun;
		
		//$this->lap_kasir_to_pdf($tanggal_aktif);
		//$this->lap_jual_to_pdf($tanggal_aktif,$tanggal_aktif);
		//$this->lap_beli_to_pdf($tanggal_aktif,$tanggal_aktif);
		//$this->lap_bank_to_pdf($tanggal_aktif);
		$this->lap_jual_to_excel($tanggal_aktif);
		//$this->lap_export_data($tanggal_aktif);
		//$this->report_kasir_to_pdf();
		
		$this->load->library('PHPMailerAutoload');
		
		$mail = new PHPMailer();
		
		//$mail->IsSMTP(); // enable SMTP
		//$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
		//$mail->SMTPAuth = true; // authentication enabled
		//$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
		//$mail->Host = "smtp.gmail.com";
		//$mail->Port = 465; // or 587
		//$mail->IsHTML(true);
		//$mail->Username = "it.bandabaru17@gmail.com";
		//$mail->Password = "12345@QWER1";
		//$mail->SetFrom("it.bandabaru17@gmail.com");
		//$mail->Subject = "Test";
		//$mail->Body = "hello";
		//$mail->AddAddress("email@gmail.com");

		$mail->IsSMTP();
		$mail->SMTPSecure   = 'ssl';
		$mail->SMTPAuth   = true;
		$mail->Host       = "bandabaru.com";
		$mail->SMTPDebug = 2;
		$mail->Port       = 465;
		$mail->Username   = "it@bandabaru.com";
		$mail->Password   = "12345@QWER1";
		$mail->SetFrom("it@bandabaru.com","PT Banda Baru Mas"); //set email pengirim
		$mail->Subject = "GMS ".$site_name." Tanggal ".$tanggal_aktif; //subyek email
		$mail->AddAddress("febrian.bandabaru17@gmail.com","Elga Helia"); //tujuan email
		$mail->AddBCC("febrian.bandabaru17@gmail.com","Febrian"); //tujuan email
		
		//$mail->AddAttachment("report/Laporan Kas Bank ".$site_name." Tanggal ".$tanggal_aktif.".pdf");
		//$mail->AddAttachment("report/Laporan Penjualan ".$site_name." Tanggal ".$tanggal_aktif.".pdf");
		//$mail->AddAttachment("report/Laporan Pembelian ".$site_name." Tanggal ".$tanggal_aktif.".pdf");
		//$mail->AddAttachment("report/Laporan Bank ".$site_name." per Tanggal ".$tanggal_aktif.".pdf");
		$mail->AddAttachment("report/LAP EXCEL ".$site_name.".xlsx");
		//$excelFilePath = "report/LAP EXCEL ".$site_name." Tanggal ".$tanggal_transaksi.".xlsx";
		//$mail->AddAttachment("report/Laporan Kasir ".$site_name." Tanggal ".$tanggal_aktif.".pdf");
		
		$mail->IsHTML(true);
		$mail->MsgHTML("Assalamu'alaikum Warohmatullahi Wabarokatuh<br><br>
		Terlampir Transaksi Bank Cabang ".$site_name." per Tanggal ".$tanggal_aktif.".<br><br>
		Best Regards,<br>IT Department :)");
		
		if($mail->Send()){
			//unlink("report/Laporan Kas Bank ".$site_name." Tanggal ".$tanggal_aktif.".pdf");
			//unlink("report/Laporan Penjualan ".$site_name." Tanggal ".$tanggal_aktif.".pdf");
			//unlink("report/Laporan Pembelian ".$site_name." Tanggal ".$tanggal_aktif.".pdf");
			//unlink("report/Laporan Bank ".$site_name." per Tanggal ".$tanggal_aktif.".pdf");
			//unlink("report/LAP EXCEL ".$site_name." Tanggal ".$tanggal_aktif.".xlsx");
			//unlink("report/Laporan Kasir ".$site_name." Tanggal ".$tanggal_aktif.".pdf");
			
			$data['success'] = TRUE;
			echo json_encode($data);
			exit;
		}else{
			//unlink("report/Laporan Kas Bank ".$site_name." Tanggal ".$tanggal_aktif.".pdf");
			//unlink("report/Laporan Penjualan ".$site_name." Tanggal ".$tanggal_aktif.".pdf");
			//unlink("report/Laporan Pembelian ".$site_name." Tanggal ".$tanggal_aktif.".pdf");
			//unlink("report/Laporan Bank ".$site_name." per Tanggal ".$tanggal_aktif.".pdf");
			//unlink("report/LAP EXCEL ".$site_name." Tanggal ".$tanggal_aktif.".xlsx");
			//unlink("report/Laporan Kasir ".$site_name." Tanggal ".$tanggal_aktif.".pdf");
			
			$data['success'] = FALSE;
			echo json_encode($data);
			exit;
		}
	}
	
	public function lap_kasir_to_pdf($tgl_transaksi){
		$tgl_transaksi = str_replace('%20',' ',$tgl_transaksi);
		$tanggal_transaksi =  $tgl_transaksi;
		
		$tglTrans = $this->date_to_format($tgl_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		$site_name = $this->mm->get_site_name();
		
		$kas_account = $this->mm->get_all_kasbank_pos();
			
		$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column center aligned" style="font-weight:bold;text-align:center;font-size:13px;font-family:samsungone"><span style="text-align">Laporan Transaksi Harian Detail</span><br><span>Tanggal '.$tanggal_transaksi.', Cabang '.$site_name.'</span><br><span style="visibility:hidden">-</span></div><div class="sixteen wide centered column">';
		
		$data['view'] .= '<table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr style="text-align:center"><th class="th-5">Description</th><th class="th-5">Voucher Code</th><th class="th-5">Debit</th><th class="th-5">Credit</th><th class="th-5">Balance</th></tr></thead><tbody>';
		
		foreach($kas_account as $ka){
			$acc_number = $ka->accountnumber;
			$accountnumber = $ka->accountnumber;
			
			/*-- Menentukan Beginning Balance --*/
			$saldo_awal = $this->mm->get_report_rp_beginning_balance($acc_number);
			/*----------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_awal_d = $this->mm->get_report_mutasi_rp_yesterday_d($acc_number,$tgl_transaksi);
			$saldo_awal_k = $this->mm->get_report_mutasi_rp_yesterday_k($acc_number,$tgl_transaksi);
			
			foreach($saldo_awal_d as $mtyd){
				$saldo_awal = $saldo_awal + $mtyd->total_mutasi;
			}
			
			foreach($saldo_awal_k as $mtyk){
				$saldo_awal = $saldo_awal - $mtyk->total_mutasi;
			}
			
			/*---------------------------------------------------*/
			
			/*---------- Mengambil Data Mutasi Transaksi ----------*/
	
			$mutasi_transaksi = $this->mm->get_report_mutasi_rp_lap($tgl_transaksi, $tgl_transaksi, $acc_number);
			
			/*---------------------------------------------------*/
			
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs = FALSE;
			$flag_saldo_awal = FALSE;
			$flag_mutasi = FALSE;
			
			if($saldo_awal != 0){
				$flag_saldo_awal = TRUE;
				$flag_kurs = TRUE;
			}
			
			foreach($mutasi_transaksi as $mk){
				if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
					$flag_mutasi = TRUE;
					$flag_kurs = TRUE;
				}
			}
			
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs == TRUE){
				$coa_data = $this->mm->get_coa_number_name($ka->accountnumberint);
				
				$running_balance = $saldo_awal;
				$total_debet = 0;
				$total_kredit = 0;
				
				$sa = number_format($saldo_awal, 0);
				if($sa == 0 || $sa == '' || $sa == -0){
					$data['view'] .= '<tr><td colspan="4" style="font-weight:bold">'.$coa_data.'</td><td class="right-aligned">-</td></tr>';
				}else{
					$data['view'] .= '<tr><td colspan="4" style="font-weight:bold">'.$coa_data.'</td><td class="right-aligned">'.number_format($saldo_awal, 2).'</td></tr>';
				}
									
				foreach($mutasi_transaksi as $mk){
					if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
						$trans_date = strtotime($mk->transdate);
						$trans_date = date('d/m/Y',$trans_date);
						
						$tipe_trans = substr($mk->idmutasi,0,2);
						if($tipe_trans == 'BR' || $tipe_trans == 'JR' || $tipe_trans == 'JP'){
							$idmutasi = substr($mk->idmutasi,0,-2);
						}else{
							$idmutasi = $mk->idmutasi;
						}
						
						if($mk->toaccount == $accountnumber){
							$debet_val = number_format($mk->value, 2);
							$kredit_val = '-';
							
							$running_balance = $running_balance + $mk->value;
							
							$rb = number_format($running_balance, 0);
							if($rb == -0){
								$running_balance = 0;
							}
							
							$total_debet = $total_debet + $mk->value;
						}else{
							$debet_val = '-';
							$kredit_val = number_format($mk->value, 2);
							
							$running_balance = $running_balance - $mk->value;
							
							$rb = number_format($running_balance, 0);
							if($rb == -0){
								$running_balance = 0;
							}
							
							$total_kredit = $total_kredit + $mk->value;
						}
						
						$data['view'] .= '<tr><td>'.$mk->description.'</td><td>'.$idmutasi.'</td><td class="right-aligned">'.$debet_val.'</td><td class="right-aligned">'.$kredit_val.'</td><td class="right-aligned">'.number_format($running_balance, 2).'</td></tr>';
					}
				}
				
				$data['view'] .= '<tr><td colspan="2"></td><td class="right-aligned" style="border-top: 1px solid #000">'.number_format($total_debet, 2).'</td><td class="right-aligned" style="border-top: 1px solid #000">'.number_format($total_kredit, 2).'</td><td></td></tr>';
				$data['view'] .= '<tr><td colspan="7" style="visibility:hidden; color:#FFF">-</td></tr>';
			}
			
			/*----------------------------------------------------*/
		}
		
		$data['view'] .= '</tbody></table></div></div>';
		
		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		$pdfFilePath = "report/Laporan Kas Bank ".$site_name." Tanggal ".$tanggal_transaksi.".pdf";
		
		$pdf = $this->m_pdf->load();
		$pdf->setFooter('{PAGENO} / {nb}');
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right;font-family:samsungone;font-size:11px;">{PAGENO} / {nb}</div>');
		$pdf->AddPage('P');
        $pdf->WriteHTML($html);
		$pdf->setProtection(array('copy','print'), 'bbokeoce', 'bbokeoce');
		$pdf->Output($pdfFilePath, "F");
	}
	
	public function lap_jual_to_pdf($tgl_from,$tgl_to){
		date_default_timezone_set("Asia/Jakarta");
		$tgl_from = str_replace('%20',' ',$tgl_from);
		$tgl_to = str_replace('%20',' ',$tgl_to);
		
		$tanggal_from =  $tgl_from;
		$tglFrom = $this->date_to_format($tgl_from);
		$tgl_from = date("Y-m-d",$tglFrom).' 00:00:00';
		
		$tanggal_to =  $tgl_to;
		$tglTo = $this->date_to_format($tgl_to);
		$tgl_to = date("Y-m-d",$tglTo).' 00:00:00';
		
		$site_name = $this->mm->get_site_name();
		
		$filter_box = '';
		$data_box = $this->mb->get_box_aktif();
		for($i=0; $i<count($data_box); $i++){
			if($i == 0){
				$filter_box .= '"'.$data_box[$i]->id.'"';
			}else{
				$filter_box .= ',"'.$data_box[$i]->id.'"';
			}
		}
		
		$filter_karat = '';
		$data_karat = $this->mk->get_karat_srt();
		for($i=0; $i<count($data_karat); $i++){
			if($i == 0){
				$filter_karat .= '"'.$data_karat[$i]->id.'"';
			}else{
				$filter_karat .= ',"'.$data_karat[$i]->id.'"';
			}
		}
			
		$site_name = $this->mm->get_site_name();
		
		$array_jual = array();
		$dj = $this->mt->get_penjualan_kasir($tgl_from,$tgl_to,$filter_box,$filter_karat);
		
		$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column center aligned" style="text-align:center;font-weight:bold;font-size:13px;font-family:samsungone;"><span>Laporan Penjualan Harian Detail</span><br><span>Tanggal '.$tanggal_from.', Cabang '.$site_name.'</span><br><span style="visibility:hidden">-</span></div><div class="sixteen wide centered column">';
		
		$data['view'] .= '<table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:40px" class="th-5">No</th><th class="th-5" style="width:120px">ID Product</th><th class="th-5">Box</th><th class="th-5" style="width:170px">Keterangan</th><th class="th-5">Karat</th><th class="th-5">Berat</th><th class="th-5" style="width:100px">Harga Jual</th><th class="th-5">Total Jual</th></tr></thead><tbody>';
		
		$length = count($dj);
		$trans_temp = '';
		$total_temp = 0;
		$number = 1;
		$total_pcs_all = 0;
		$total_gram_all = 0;
		$total_jual_all = 0;
		
		for($i = 0;$i < $length; $i++){
			$id_trans = $dj[$i]->transaction_code;
			$act = '';
			
			if($i == 0){
				$trans_date = strtotime($dj[$i]->trans_date);
				$trans_date = date('d-M-Y',$trans_date);
				$trans_temp = $id_trans;
				$cust_service = $this->mt->get_cs_jual_by_id($id_trans);
				$data['view'] .= '<tr><td class="right-aligned">'.$number.'. </td><td class="td-bold" colspan="5">'.$act.' '.$trans_date.' | '.$id_trans.'</td><td colspan="2" style="border-left:none;border-right:none;text-transform:uppercase">Customer Service : '.$cust_service.'</td></tr>';
				
				$number = $number + 1;
				
				$box_number = '';
				$totalnumberlength = 3;
				$numberlength = strlen($dj[$i]->id_box);
				$numberspace = $totalnumberlength - $numberlength;
				if($numberspace != 0){
					for ($a = 1; $a <= $numberspace; $a++){
						$box_number .= '0';
					}
				}
				
				$box_number .= $dj[$i]->id_box;
				
				$data['view'] .= '<tr><td></td><td>'.$dj[$i]->id_product.'</td><td class="center aligned">'.$box_number.'</td><td>'.$dj[$i]->product_desc.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right-aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price/$dj[$i]->product_weight, 2).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
				
				$total_pcs_all = $total_pcs_all + 1;
				$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
				$total_jual_all = $total_jual_all + $dj[$i]->product_price;
				
				$total_temp = $total_temp + $dj[$i]->product_price;
			}else{
				if($dj[$i]->transaction_code == $trans_temp){
					$box_number = '';
					$totalnumberlength = 3;
					$numberlength = strlen($dj[$i]->id_box);
					$numberspace = $totalnumberlength - $numberlength;
					if($numberspace != 0){
						for ($a = 1; $a <= $numberspace; $a++){
							$box_number .= '0';
						}
					}
					
					$box_number .= $dj[$i]->id_box;
					
					$data['view'] .= '<tr><td></td><td>'.$dj[$i]->id_product.'</td><td class="center aligned">'.$box_number.'</td><td>'.$dj[$i]->product_desc.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right-aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price/$dj[$i]->product_weight, 2).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
					
					$total_temp = $total_temp + $dj[$i]->product_price;
					
					$total_pcs_all = $total_pcs_all + 1;
					$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
					$total_jual_all = $total_jual_all + $dj[$i]->product_price;
				}else{
					$data['view'] .= '<tr><td colspan="5"></td><td class="double-top"></td><td class="double-top"></td><td class="double-top right-aligned">'.number_format($total_temp, 2).'</td></tr>';
					
					$total_temp = 0;
					
					$trans_date = strtotime($dj[$i]->trans_date);
					$trans_date = date('d-M-Y',$trans_date);
					$trans_temp = $id_trans;
					$cust_service = $this->mt->get_cs_jual_by_id($id_trans);
					$data['view'] .= '<tr><td class="right-aligned">'.$number.'. </td><td class="td-bold" colspan="5">'.$act.' '.$trans_date.' | '.$id_trans.'</td><td colspan="2" style="border-left:none;border-right:none;text-transform:uppercase">Customer Service : '.$cust_service.'</td></tr>';
					
					$number = $number + 1;
					
					$box_number = '';
					$totalnumberlength = 3;
					$numberlength = strlen($dj[$i]->id_box);
					$numberspace = $totalnumberlength - $numberlength;
					if($numberspace != 0){
						for ($a = 1; $a <= $numberspace; $a++){
							$box_number .= '0';
						}
					}
					
					$box_number .= $dj[$i]->id_box;
					
					$data['view'] .= '<tr><td></td><td>'.$dj[$i]->id_product.'</td><td class="center aligned">'.$box_number.'</td><td>'.$dj[$i]->product_desc.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right-aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price/$dj[$i]->product_weight, 2).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
					
					$total_temp = $total_temp + $dj[$i]->product_price;
					
					$total_pcs_all = $total_pcs_all + 1;
					$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
					$total_jual_all = $total_jual_all + $dj[$i]->product_price;
				}
			}
		}
		
		if($length != 0){
			$data['view'] .= '<tr><td colspan="5"></td><td class="double-top"></td><td class="double-top"></td><td class="double-top right-aligned">'.number_format($total_temp, 2).'</td></tr>';
			
			
			$data['view'] .= '<tr><td colspan="8"><span style="visibility:hidden">-</span></td></tr><tr><td colspan="4"></td><td class="double-top right-aligned">'.$total_pcs_all.' Pcs</td><td class="double-top right-aligned">'.number_format($total_gram_all, 3).'</td><td class="double-top"></td><td class="double-top right-aligned">'.number_format($total_jual_all, 2).'</td></tr>';
		}
		
		$data['view'] .= '</tbody></table></div>';
		
		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		$pdfFilePath = "report/Laporan Penjualan ".$site_name." Tanggal ".$tanggal_from.".pdf";
		
		$pdf = $this->m_pdf->load();
		$pdf->setFooter('{PAGENO} / {nb}');
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right;font-family:samsungone;font-size:11px;">{PAGENO} / {nb}</div>');
		$pdf->AddPage('P');
        $pdf->WriteHTML($html);
		$pdf->setProtection(array('copy','print'), 'bbokeoce', 'bbokeoce');
		$pdf->Output($pdfFilePath, "F");
	}
	
	public function lap_jual_to_excel(){
		$tgl_transaksi = '01 July 2019';
		$tgl_transaksi_dua = '31 July 2019';
		$tanggal_transaksi =  $tgl_transaksi;
		
		$tglTrans = $this->date_to_format($tgl_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		$tgl_transaksi2 = date("Y-m-d",$tglTrans);
		
		$tglTrans2 = $this->date_to_format($tgl_transaksi_dua);
		$tgl_transaksi_dua = date("Y-m-d",$tglTrans2).' 23:59:59';
		
		$before_tgl_transaksi = date('Y-m-d',strtotime($tgl_transaksi2. "-31 days")).' 00:00:00';
		
		$site_name = $this->mm->get_site_name();
		
		$data_karat = $this->mk->get_karat_srt();
		$total_karat = count($data_karat);
		
		$site_name = $this->mm->get_site_name();
		
		$array_jual = array();
		
		$this->load->library('Libexcel');
		$objPHPExcel = new PHPExcel();
		$sheet = $objPHPExcel->getActiveSheet();
		
		$style_font = array(
			'font'  => array(
				'name'  => 'Segoe UI'
			)
		);
		
		$i = 0;
		
		while($i < $total_karat){
			$sheet = $objPHPExcel->createSheet($i);
			$karat_name = $data_karat[$i]->karat_name;
			$karat_id = $data_karat[$i]->id;
			$sheet->setTitle($karat_name);
			
			$data_jual = $this->mt->get_rekap_karat_excel($karat_id,$tgl_transaksi,$tgl_transaksi_dua);
			
			$sheet->getColumnDimension('A')->setWidth(25);
			$sheet->getColumnDimension('B')->setWidth(20);
			$sheet->getColumnDimension('C')->setWidth(20);
			$sheet->getColumnDimension('D')->setWidth(25);
			
			$sheet
				->mergeCells('A1:D1')
				->mergeCells('A2:D2');
			
			$sheet
				->setCellValue('A1','LAPORAN PENJUALAN EMAS')
				->setCellValue('A2','CABANG : '.$site_name)
				->setCellValue('A6','KARAT : ')
				->setCellValue('B6',$karat_name)
				->setCellValue('A8','Tanggal')
				->setCellValue('B8','KETERANGAN')
				->setCellValue('C8','Gram')
				->setCellValue('D8','Rp');
			
			$sheet->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			
			$style_font_header = array(
				'font'  => array(
					'bold'  => true
				)
			);
			
			$sheet->getStyle('A1:D8')->applyFromArray($style_font_header);
			
			$sheet->getStyle("A8:D8")->applyFromArray(array(
				'borders' => array(
					'top' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					),
					'bottom' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			));
			
			$sheet->getStyle('A8:D8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$baris = 10;
			
			foreach($data_jual as $dj){
				$trans_date = strtotime($dj->trans_date);
				$trans_tgl = date('d',$trans_date);
				$trans_bln = date('m',$trans_date);
				$trans_thn = date('Y',$trans_date);
				
				$trans_bln = (int)$trans_bln;
				
				switch($trans_bln){
					case 1:
						$bulankini = 'Januari';
						break;
					case 2:
						$bulankini = 'Februari';
						break;
					case 3:
						$bulankini = 'Maret';
						break;
					case 4:
						$bulankini = 'April';
						break;
					case 5:
						$bulankini = 'May';
						break;
					case 6:
						$bulankini = 'Juni';
						break;
					case 7:
						$bulankini = 'Juli';
						break;
					case 8:
						$bulankini = 'Agustus';
						break;
					case 9:
						$bulankini = 'September';
						break;
					case 10:
						$bulankini = 'Oktober';
						break;
					case 11:
						$bulankini = 'November';
						break;
					case 12:
						$bulankini = 'Desember';
						break;
				}
				
				$sheet
				->setCellValue('A'.$baris, $trans_tgl.' '.$bulankini.' '.$trans_thn)
				->setCellValue('B'.$baris, 'PENJUALAN')
				->setCellValue('C'.$baris, $dj->berat)
				->setCellValue('D'.$baris, $dj->harga);
				
				$baris = $baris + 1;
			}
			
			$sheet->getStyle('C10:C'.$baris)->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"_);_(@_)');
			$sheet->getStyle('D10:D'.$baris)->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
			$sheet->getStyle('A1:D'.$baris.'')->applyFromArray($style_font);
			$sheet->getStyle('A1:D'.$baris.'')->getAlignment()->setWrapText(true);
			
			$i++;
		}
		
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

		//sesuaikan headernya 
		//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		//header("Cache-Control: no-store, no-cache, must-revalidate");
		//header("Cache-Control: post-check=0, pre-check=0", false);
		//header("Pragma: no-cache");
		//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		//header('Content-Disposition: attachment;filename="LAPORAN EXCEL '.$site_name.' per '.$tanggal_transaksi.'.xlsx"');
		$excelFilePath = "report/LAP EXCEL ".$site_name.".xlsx";
		//unduh file
		//$objWriter->save("php://output");
		$objWriter->save($excelFilePath);
	}
	
	public function lap_beli_to_pdf($tgl_from,$tgl_to){
		date_default_timezone_set("Asia/Jakarta");
		$tgl_from = str_replace('%20',' ',$tgl_from);
		$tgl_to = str_replace('%20',' ',$tgl_to);
		
		$tanggal_from =  $tgl_from;
		$tglFrom = $this->date_to_format($tgl_from);
		$tgl_from = date("Y-m-d",$tglFrom).' 00:00:00';
		
		$tanggal_to =  $tgl_to;
		$tglTo = $this->date_to_format($tgl_to);
		$tgl_to = date("Y-m-d",$tglTo).' 00:00:00';
		
		$filter_karat = '';
		$data_karat = $this->mk->get_karat_srt();
		for($i=0; $i<count($data_karat); $i++){
			if($i == 0){
				$filter_karat .= '"'.$data_karat[$i]->id.'"';
			}else{
				$filter_karat .= ',"'.$data_karat[$i]->id.'"';
			}
		}
		
		$site_name = $this->mm->get_site_name();
		
		$array_jual = array();
		$dj = $this->mt->get_pembelian_kasir($tgl_from,$tgl_to,$filter_karat);
		
		$data['view'] = '<div class="ui grid"><div class="sixteen wide centered column center aligned" style="text-align:center;font-weight:bold;font-size:13px;font-family:samsungone;"><span>Laporan Pembelian Harian Detail</span><br><span>Tanggal '.$tanggal_from.', Cabang '.$site_name.'</span><br><span style="visibility:hidden">-</span></div><div class="sixteen wide centered column">';
		
		$data['view'] .= '<table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:40px" class="th-5">No</th><th class="th-5">Kelompok</th><th class="th-5">Keterangan</th><th class="th-5">Karat</th><th class="th-5">Pcs</th><th class="th-5">Berat</th><th class="th-5">Harga Beli</th><th class="th-5">Total Beli</th></tr></thead><tbody>';
		
		$length = count($dj);
		$trans_temp = '';
		$total_pcs_temp = 0;
		$total_gram_temp = 0;
		$total_temp = 0;
		$number = 1;
		$total_pcs_all = 0;
		$total_gram_all = 0;
		$total_jual_all = 0;
		
		for($i = 0;$i < $length; $i++){
			$id_trans = $dj[$i]->transaction_code;
			$act = '';
			
			if($i == 0){
				$trans_date = strtotime($dj[$i]->trans_date);
				$trans_date = date('d-M-Y',$trans_date);
				$trans_temp = $id_trans;
				
				$cust_service = $this->mt->get_cs_beli_by_id($id_trans);
				
				$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td colspan="5">'.$act.' '.$trans_date.' | '.$id_trans.'</td><td colspan="2" style="border-left:none;border-right:none;text-transform:uppercase">Customer Service : '.$cust_service.'</td></tr>';
				
				$number = $number + 1;
				
				$data['view'] .= '<tr><td></td><td>'.$dj[$i]->category_name.'</td><td>'.$dj[$i]->nama_product.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right-aligned">'.$dj[$i]->product_pcs.'</td><td class="right-aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price/$dj[$i]->product_weight, 2).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
				
				$total_pcs_all = $total_pcs_all + $dj[$i]->product_pcs;
				$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
				$total_jual_all = $total_jual_all + $dj[$i]->product_price;
				
				$total_pcs_temp = $total_pcs_temp + $dj[$i]->product_pcs;
				$total_gram_temp = $total_gram_temp + $dj[$i]->product_weight;
				$total_temp = $total_temp + $dj[$i]->product_price;
			}else{
				if($dj[$i]->transaction_code == $trans_temp){
					
					$data['view'] .= '<tr><td></td><td>'.$dj[$i]->category_name.'</td><td>'.$dj[$i]->nama_product.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right-aligned">'.$dj[$i]->product_pcs.'</td><td class="right-aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price/$dj[$i]->product_weight, 2).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
					
					$total_pcs_all = $total_pcs_all + $dj[$i]->product_pcs;
					$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
					$total_jual_all = $total_jual_all + $dj[$i]->product_price;
					
					$total_pcs_temp = $total_pcs_temp + $dj[$i]->product_pcs;
					$total_gram_temp = $total_gram_temp + $dj[$i]->product_weight;
					$total_temp = $total_temp + $dj[$i]->product_price;
				}else{
					$data['view'] .= '<tr><td colspan="4"></td><td class="double-top right-aligned">'.$total_pcs_temp.'</td><td class="double-top right-aligned">'.number_format($total_gram_temp, 3).'</td><td class="double-top"></td><td class="double-top right-aligned">'.number_format($total_temp, 2).'</td></tr>';
					
					$total_temp = 0;
					$total_pcs_temp = 0;
					$total_gram_temp = 0;
					
					$trans_date = strtotime($dj[$i]->trans_date);
					$trans_date = date('d-M-Y',$trans_date);
					$trans_temp = $id_trans;
					
					$cust_service = $this->mt->get_cs_beli_by_id($id_trans);
				
					$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td colspan="5">'.$act.' '.$trans_date.' | '.$id_trans.'</td><td colspan="2" style="border-left:none;border-right:none;text-transform:uppercase">Customer Service : '.$cust_service.'</td></tr>';
					
					$number = $number + 1;
					
					$data['view'] .= '<tr><td></td><td>'.$dj[$i]->category_name.'</td><td>'.$dj[$i]->nama_product.'</td><td>'.$dj[$i]->karat_name.'</td><td class="right-aligned">'.$dj[$i]->product_pcs.'</td><td class="right-aligned">'.number_format($dj[$i]->product_weight, 3).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price/$dj[$i]->product_weight, 2).'</td><td class="right-aligned">'.number_format($dj[$i]->product_price, 2).'</td></tr>';
					
					$total_pcs_all = $total_pcs_all + $dj[$i]->product_pcs;
					$total_gram_all = $total_gram_all + $dj[$i]->product_weight;
					$total_jual_all = $total_jual_all + $dj[$i]->product_price;
					
					$total_pcs_temp = $total_pcs_temp + $dj[$i]->product_pcs;
					$total_gram_temp = $total_gram_temp + $dj[$i]->product_weight;
					$total_temp = $total_temp + $dj[$i]->product_price;
				}
			}
		}
		
		if($length != 0){
			$data['view'] .= '<tr><td colspan="4"></td><td class="double-top right-aligned">'.$total_pcs_temp.'</td><td class="double-top right-aligned">'.number_format($total_gram_temp, 3).'</td><td class="double-top"></td><td class="double-top right-aligned">'.number_format($total_temp, 2).'</td></tr>';
			
			$data['view'] .= '<tr><td colspan="4"></td><td class="double-top right-aligned">'.$total_pcs_all.'</td><td class="double-top right-aligned">'.number_format($total_gram_all, 3).'</td><td class="double-top"></td><td class="double-top right-aligned">'.number_format($total_jual_all, 2).'</td></tr>';
		}
		
		$data['view'] .= '</tbody></table></div>';
		
		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		$pdfFilePath = "report/Laporan Pembelian ".$site_name." Tanggal ".$tanggal_from.".pdf";
		
		$pdf = $this->m_pdf->load();
		$pdf->setFooter('{PAGENO} / {nb}');
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right;font-family:samsungone;font-size:11px;">{PAGENO} / {nb}</div>');
		$pdf->AddPage('P');
        $pdf->WriteHTML($html);
		$pdf->setProtection(array('copy','print'), 'bbokeoce', 'bbokeoce');
		$pdf->Output($pdfFilePath, "F");
	}
	
	public function lap_bank_to_pdf($tgl_transaksi){
		$tgl_transaksi = str_replace('%20',' ',$tgl_transaksi);
		$tanggal_transaksi =  $tgl_transaksi;
		
		$tglTrans = $this->date_to_format($tgl_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		$tgl_transaksi2 = date("Y-m-d",$tglTrans);
		$before_tgl_transaksi = date('Y-m-d',strtotime($tgl_transaksi2. "-10 days")).' 00:00:00';
		$site_name = $this->mm->get_site_name();
		
		$kas_account = $this->mm->get_all_bank_pos();
		
		$data['view'] = '<div class="sixteen wide centered column center aligned" style="font-weight:bold;text-align:center;font-size:13px;font-family:samsungone"><span style="text-align">Ledger Detail Report, Cabang '.$site_name.'</span><br><span>per Tanggal '.$tanggal_transaksi.'</span><br><span style="visibility:hidden">-</span></div><table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th style="width:50px" class="th-5">No</th><th class="th-5">Tanggal</th><th class="th-5">Keterangan</th><th class="th-5">ID Transaksi</th><th class="th-5">Tarik</th><th class="th-5">Setor</th><th class="th-5">Saldo</th></tr></thead><tbody>';
		
		$number = 1;
		
		foreach($kas_account as $ka){
			$saldo_awal_kurs = 0;
			
			$flag_tulis = TRUE;
			$flag_kurs = '';
			$flag_saldo_awal = '';
			$flag_mutasi = '';

			$acc_number = $ka->accountnumber;
			$accountnumber = $ka->accountnumber;
			
			/*-- Menentukan Beginning Balance Tiap-Tiap Account --*/
		
			$saldo_awal_kurs = $this->mm->get_report_rp_beginning_balance($acc_number);
			
			/*--------------------------------------------------*/
		
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_awal_d = $this->mm->get_report_mutasi_transaksi_yesterday_d_rp($acc_number,$before_tgl_transaksi);
			$saldo_awal_k = $this->mm->get_report_mutasi_transaksi_yesterday_k_rp($acc_number,$before_tgl_transaksi);
			
			foreach($saldo_awal_d as $mtyd){
				if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
					$saldo_awal_kurs = $saldo_awal_kurs + $mtyd->total_mutasi;
				}else{
					$saldo_awal_kurs = $saldo_awal_kurs - $mtyd->total_mutasi;
				}
			}
			
			foreach($saldo_awal_k as $mtyk){
				if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
					$saldo_awal_kurs = $saldo_awal_kurs - $mtyk->total_mutasi;
				}else{
					$saldo_awal_kurs = $saldo_awal_kurs + $mtyk->total_mutasi;
				}
			}
					
			/*---------------------------------------------------*/
		
			/*---------- Mengambil Data Mutasi Transaksi ----------*/
	
			$mutasi_transaksi = $this->mm->get_report_mutasi_rp($before_tgl_transaksi,$tgl_transaksi,$acc_number);
			
			/*---------------------------------------------------*/
		
			/*---------- Pengecekan Nilai TRUE or FALSE ----------*/
			
			$flag_kurs = FALSE;
			$flag_saldo_awal = FALSE;
			$flag_mutasi = FALSE;
			
			if($saldo_awal_kurs != 0){
				$flag_kurs = TRUE;
				$flag_saldo_awal = TRUE;
			}
			
			foreach($mutasi_transaksi as $mk){
				if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
					$flag_kurs = TRUE;
					$flag_mutasi = TRUE;
				}
			}
			
			/*----------------------------------------------------*/
		
			/*-------- Menampilkan Data Dalam Tabel Report -------*/
			
			if($flag_kurs == TRUE){
				$coa_data = $this->mm->get_accountname_by_accountint_rp($ka->accountnumberint);
				
				$running_balance = $saldo_awal_kurs;
				$total_debet = 0;
				$total_kredit = 0;
				
				if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
					if($saldo_awal_kurs == 0 || $saldo_awal_kurs == ''){
						$data['view'] .= '<tr><td class="right-aligned" width="50px">'.$number.'. </td><td colspan="5">'.$coa_data.'</td><td class="right-aligned">-</td><td></td>';
					}else{
						$data['view'] .= '<tr><td class="right-aligned" width="50px">'.$number.'. </td><td colspan="5">'.$coa_data.'</td><td class="right-aligned">'.number_format($saldo_awal_kurs, 0).'</td><td></td>';
					}
				}else{
					$data['view'] .= '<tr><td class="right-aligned" width="50px">'.$number.'. </td><td colspan="5">'.$coa_data.'</td><td class="right-aligned">'.number_format($saldo_awal_kurs, 0).'</td><td></td>';
				}
				
				$count_data = 1;
				foreach($mutasi_transaksi as $mk){
					if($mk->fromaccount == $accountnumber || $mk->toaccount == $accountnumber){
						$trans_date = strtotime($mk->transdate);
						$trans_date = date('d/m/Y',$trans_date);
						
						if($mk->toaccount == $accountnumber){
							$debet_val = number_format($mk->value, 0);
							$kredit_val = '-';
							
							if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
								$running_balance = $running_balance + $mk->value;
							}else{
								$running_balance = $running_balance - $mk->value;
							}
							
							$total_debet = $total_debet + $mk->value;
						}else{
							$debet_val = '-';
							$kredit_val = number_format($mk->value, 0);
							
							if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
								$running_balance = $running_balance - $mk->value;
							}else{
								$running_balance = $running_balance + $mk->value;
							}
							
							$total_kredit = $total_kredit + $mk->value;
						}
						
						if($ka->accountgroup == '1' || $ka->accountgroup == '5'){
							$data['view'] .= '<tr><td class="center aligned"></td><td>'.$trans_date.'</td><td>'.$mk->description.'</td><td>'.$mk->idmutasi.'</td><td class="right-aligned">'.$debet_val.'</td><td class="right-aligned">'.$kredit_val.'</td><td class="right-aligned">'.number_format($running_balance, 0).'</td></tr>';
						}else{
							$data['view'] .= '<tr><td class="center aligned"></td><td>'.$trans_date.'</td><td>'.$mk->description.'</td><td>'.$mk->idmutasi.'</td><td class="right-aligned">'.$debet_val.'</td><td class="right-aligned">'.$kredit_val.'</td><td class="right-aligned">'.number_format($running_balance, 0).'</td></tr>';
						}
						
						$count_data = $count_data + 1;
					}
				}
				
				$data['view'] .= '<tr><td colspan="4"></td><td class="right-aligned" style="border-top: 1px solid #000">'.number_format($total_debet, 0).'</td><td class="right-aligned" style="border-top: 1px solid #000">'.number_format($total_kredit, 0).'</td><td></td></tr>';
				$data['view'] .= '<tr><td colspan="7" style="visibility:hidden; color:#FFF">-</td></tr>';
				
				$number = $number + 1;
			}
		}
		
		$data['view'] .= '</tbody></table></div></div>';
		
		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		$pdfFilePath = "report/Laporan Bank ".$site_name." per Tanggal ".$tanggal_transaksi.".pdf";
		
		$pdf = $this->m_pdf->load();
		$pdf->setFooter('{PAGENO} / {nb}');
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right;font-family:samsungone;font-size:11px;">{PAGENO} / {nb}</div>');
		$pdf->AddPage('P');
        $pdf->WriteHTML($html);
		$pdf->setProtection(array('copy','print'), 'bbokeoce', 'bbokeoce');
		$pdf->Output($pdfFilePath, "F");
	}
	
	public function report_kasir_to_pdf(){
		$active_date = $this->mt->get_tanggal_aktif($GLOBALS['kasir']);
		$tanggal_aktif = $active_date[0]->tanggal_aktif;
		$per_tanggal = $active_date[0]->tanggal_aktif;
		$per_tanggal = strtotime($per_tanggal);
		$tgl_from = $active_date[0]->tanggal_aktif;
		$tgl_to = date('Y-m-d',$per_tanggal).' 23:59:59';
		
		$harga_emas = $this->mt->get_do_by_date($tanggal_aktif);
		$tanggal_aktif = strtotime($tanggal_aktif);
		$hari = date('D',$tanggal_aktif);
		$bulan = date('m',$tanggal_aktif);
		
		switch($hari){
			case "Mon":
				$hari_tulis = 'Senin';
				break;
			case "Tue":
				$hari_tulis = 'Selasa';
				break;
			case "Wed":
				$hari_tulis = 'Rabu';
				break;
			case "Thu":
				$hari_tulis = 'Kamis';
				break;
			case "Fri":
				$hari_tulis = 'Jumat';
				break;
			case "Sat":
				$hari_tulis = 'Sabtu';
				break;
			case "Sun":
				$hari_tulis = 'Minggu';
				break;
		}
		
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
		
		$tanggal_aktif = $tanggal.' '.$aktif_bulan.' '.$tahun;
		
		$kas_account = $this->mm->get_single_coa_rp('11-0001');
		foreach($kas_account as $ka){
			$acc_number = $ka->accountnumber;
			$accountnumber = $ka->accountnumber;
			
			/*-- Menentukan Beginning Balance --*/
			$saldo_awal = $this->mm->get_report_rp_beginning_balance($acc_number);
			/*----------------------------------*/
			
			/*----- Menentukan Saldo Awal dan Saldo Akhir ------*/
			
			$saldo_awal_d = $this->mm->get_report_mutasi_rp_yesterday_d($acc_number,$tgl_from);
			$saldo_awal_k = $this->mm->get_report_mutasi_rp_yesterday_k($acc_number,$tgl_from);
			
			foreach($saldo_awal_d as $mtyd){
				$saldo_awal = $saldo_awal + $mtyd->total_mutasi;
			}
			
			foreach($saldo_awal_k as $mtyk){
				$saldo_awal = $saldo_awal - $mtyk->total_mutasi;
			}
		}
		
		$sitename = $this->mm->get_site_name();
		
		$data['view'] = '<div class="ui stackable grid"><div class="sixteen wide centered column"><table class="lap_pdf" cellspacing="0" width="100%"><thead><tr><th colspan="2" style="text-align:left"><img src="'.base_url().'assets/images/branding/brand.png" style="width:240px"></th><th colspan="2" style="text-align:center;font-size:15px;">Laporan Kas Kasir <br>Cabang '.$sitename.'</th><th colspan="2" style="text-align:right;font-size:15px;"><br>'.$hari_tulis.', '.$tanggal_aktif.'</th></tr><tr><th colspan="6"><span style="visibility:hidden">-</span></th></tr><tr><th colspan="6"></th></tr></thead><tbody>';
		
		$data['view'] .= '<tr><td colspan="6" style="text-align:left;border:none;font-weight:bold;border-bottom:1px dotted #000">DATA PENJUALAN</td></tr><tr><td class="theader" style="width:40px;">No</td><td class="theader">Karat</td><td class="theader" style="width:60px;">Pcs</td><td class="theader" style="width:100px;">Gram</td><td class="theader" style="width:140px;">Rata2</td><td class="theader" style="width:160px;">Total Jual</td></tr>';
		
		$number = 1;
		$total_pcs = 0;
		$total_gram = 0;
		$total_jual = 0;
	
		$data_rekap = $this->mt->get_penjualan_rekap_karat($tgl_from,$tgl_to);
		foreach($data_rekap as $dr){
			$data['view'] .= '<tr><td style="text-align:right">'.$number.'.</td><td>'.$dr->karat_name.'</td><td style="text-align:right">'.$dr->pcs.'</td><td style="text-align:right">'.number_format($dr->berat, 2).'</td><td style="text-align:right">'.number_format($dr->harga/$dr->berat, 2).'</td><td style="text-align:right">'.number_format($dr->harga, 0).'</td><tr>';
			
			$total_pcs = $total_pcs + $dr->pcs;
			$total_gram = $total_gram + $dr->berat;
			$total_jual = $total_jual + $dr->harga;
			$number = $number + 1;
		}
		
		if($total_pcs != 0){
			$data['view'] .= '<tr><td colspan="2"></td><td class="double-top" style="text-align:right">'.$total_pcs.'</td><td class="double-top" style="text-align:right">'.number_format($total_gram, 2).'</td><td class="double-top" style="text-align:right"></td><td class="double-top" style="text-align:right">'.number_format($total_jual, 0).'</td><tr>';
		}else{
			$data['view'] .= '<tr><td colspan="6" style="text-align:center">Tidak Ada Penjualan</td><tr>';
		}
		
		$number = 1;
		$total_pcs = 0;
		$total_gram = 0;
		$total_beli = 0;
		
		$data['view'] .= '<tr><td colspan="6"><span style="visibility:hidden">-</span></td></tr><tr><td colspan="6" style="text-align:left;border:none;font-weight:bold;border-bottom:1px dotted #000">DATA PEMBELIAN</td></tr><tr><td class="theader" style="width:40px;">No</td><td class="theader">Karat</td><td class="theader" style="width:60px;">Pcs</td><td class="theader" style="width:100px;">Gram</td><td class="theader" style="width:140px;">Rata2</td><td class="theader" style="width:160px;">Total Beli</td></tr>';
		
		$data_rekap = $this->mt->get_pembelian_rekap_karat($tgl_from,$tgl_to);
		foreach($data_rekap as $dr){
			$data['view'] .= '<tr><td style="text-align:right">'.$number.'.</td><td>'.$dr->karat_name.'</td><td style="text-align:right">'.$dr->pcs.'</td><td style="text-align:right">'.number_format($dr->berat, 2).'</td><td style="text-align:right">'.number_format($dr->harga/$dr->berat, 2).'</td><td style="text-align:right">'.number_format($dr->harga, 0).'</td><tr>';
			
			$total_pcs = $total_pcs + $dr->pcs;
			$total_gram = $total_gram + $dr->berat;
			$total_beli = $total_beli + $dr->harga;
			$number = $number + 1;
		}
		
		if($total_pcs != 0){
			$data['view'] .= '<tr><td colspan="2"></td><td class="double-top" style="text-align:right">'.$total_pcs.'</td><td class="double-top" style="text-align:right">'.number_format($total_gram, 2).'</td><td class="double-top" style="text-align:right"></td><td class="double-top" style="text-align:right">'.number_format($total_beli, 0).'</td><tr>';
		}else{
			$data['view'] .= '<tr><td colspan="6" style="text-align:center">Tidak Ada Pembelian</td><tr>';
		}
		
		$data['view'] .= '<tr><td colspan="6"><span style="visibility:hidden">-</span></td></tr><tr><td colspan="6" style="border-bottom:double #000;"><span style="visibility:hidden">-</span></td></tr>';
		
		$data['view'] .= '</tbody></table>';
		
		$data['view'] .= '<table class="lap_pdf_2" cellspacing="0" width="100%"><thead><tr><td class="theader">Keterangan</td><td class="theader">Debit</td><td class="theader">Credit</td><td class="theader">Balance</td></tr></thead><tbody>';
		
		$data['view'] .= '<tr><td style="font-weight:bold">Saldo Awal</td><td></td><td></td><td style="text-align:right;font-weight:bold">'.number_format($saldo_awal, 0).'</td><tr>';
		
		$trans_bank = $this->mt->get_mutasi_jual_bank_detail($tgl_from);
		
		foreach($trans_bank as $tb){
			$saldo_awal = $saldo_awal - $tb->bayar_2;
			
			$data['view'] .= '<tr><td>Pembayaran Dengan '.$tb->accountname.'</td><td></td><td style="text-align:right">'.number_format($tb->bayar_2, 0).'</td><td style="text-align:right">'.number_format($saldo_awal, 0).'</td><tr>';
		}
		
		$kas_masuk_keluar = $this->mt->get_mutasi_kas_masuk_keluar_detail($tgl_from);
		
		foreach($kas_masuk_keluar as $tb){
			if($tb->fromaccount == '11-0001'){
				$saldo_awal = $saldo_awal - $tb->value;
				$data['view'] .= '<tr><td>'.$tb->description.'</td><td></td><td style="text-align:right">'.number_format($tb->value, 0).'</td><td style="text-align:right">'.number_format($saldo_awal, 0).'</td><tr>';
			}else if($tb->toaccount == '11-0001'){
				$saldo_awal = $saldo_awal + $tb->value;
				$data['view'] .= '<tr><td>'.$tb->description.'</td><td style="text-align:right">'.number_format($tb->value, 0).'</td><td></td><td style="text-align:right">'.number_format($saldo_awal, 0).'</td><tr>';
			}
		}
		
		$saldo_awal = $saldo_awal + $total_jual;
		$data['view'] .= '<tr><td>Penjualan</td><td style="text-align:right">'.number_format($total_jual, 0).'</td><td></td><td style="text-align:right">'.number_format($saldo_awal, 0).'</td><tr>';
		
		$saldo_awal = $saldo_awal - $total_beli;
		$data['view'] .= '<tr><td>Pembelian</td><td></td><td style="text-align:right">'.number_format($total_beli, 0).'</td><td style="text-align:right;font-weight:bold;">'.number_format($saldo_awal, 0).'</td><tr>';
		
		$data['view'] .= '</tbody></table></div></div>';
		
		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
        $css = base_url().'assets/css/gold.css';
        
		$stylesheet = file_get_contents($css);
		
		$pdfFilePath = "report/Laporan Kasir ".$sitename." Tanggal ".$tanggal_aktif.".pdf";
		
		$pdf = $this->m_pdf->load();
		$pdf->setFooter('{PAGENO} / {nb}');
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right;font-family:samsungone;font-size:11px;">{PAGENO} / {nb}</div>');
		$pdf->AddPage('P');
        $pdf->WriteHTML($html);
		$pdf->setProtection(array('copy','print'), 'bbokeoce', 'bbokeoce');
		$pdf->Output($pdfFilePath, "F");
	}
	
	public function lap_excel($tgl_from, $tgl_to){
		$this->db->trans_start();
		
		$this->load->library('Libexcel');
		
		$objPHPExcel = new PHPExcel();
		
		$site_name = $this->mm->get_site_name();
		
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setTitle($site_name);
		
		$data_bayar = $this->my->get_all_bayar_nontunai();
		$sheet->setCellValue('A1', $site_name);
		$sheet->setCellValue('A2', 'Pembayaran');
		
		$start_data_row = 3;
		foreach($data_bayar as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->description);
			$sheet->setCellValue('C'.$start_data_row, $d->account_number);
			$sheet->setCellValue('D'.$start_data_row, $dt->status);
			$sheet->setCellValue('E'.$start_data_row, $dt->created_date);
			$sheet->setCellValue('F'.$start_data_row, $dt->created_by);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'Box');
		$start_data_row = $start_data_row + 1;
		
		$data_box = $this->mb->get_all_box();
		foreach($data_box as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->nama_box);
			$sheet->setCellValue('C'.$start_data_row, $d->pesanan);
			$sheet->setCellValue('D'.$start_data_row, $dt->status);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$data_coa_gr = $this->mm->get_all_coa_gr();
		$data_coa_rp = $this->mm->get_all_coa_rp();
		
		
	}
	
	public function lap_export_data($tgl_transaksi){
		$tgl_transaksi = str_replace('%20',' ',$tgl_transaksi);
		
		$tglTrans = $this->date_to_format($tgl_transaksi);
		$tgl_to = date("Y-m-d",$tglTrans).' 23:59:59';
		$tgl_to2 = date("Y-m-d",$tglTrans);
		$tgl_tulis = date("dMY",$tglTrans);
		$tgl_from = date('Y-m-d',strtotime($tgl_to2. "-7 days")).' 00:00:00';
		
		//membuat objek PHPExcel
		$this->load->library('Libexcel');
		
		$objPHPExcel = new PHPExcel();
		
		$sheet = $objPHPExcel->getActiveSheet();
		
		$site_name = $this->mm->get_site_name();
		
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setTitle($site_name);
		
		$sheet->setCellValue('A1', $site_name);
		$sheet->setCellValue('A2', 'gold_bayar_non_tunai');
		
		$data_non_tunai = $this->my->get_all_bayar_nontunai();
		
		$start_data_row = 3;
		
		foreach($data_non_tunai as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->description);
			$sheet->setCellValue('C'.$start_data_row, $d->account_number);
			$sheet->setCellValue('D'.$start_data_row, $d->status);
			$sheet->setCellValue('E'.$start_data_row, $d->created_date);
			$sheet->setCellValue('F'.$start_data_row, $d->created_by);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_box');
		$start_data_row = $start_data_row + 1;
		
		$data_box  = $this->mb->get_all_box();
		
		foreach($data_box as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->nama_box);
			$sheet->setCellValue('C'.$start_data_row, $d->pesanan);
			$sheet->setCellValue('D'.$start_data_row, $d->status);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_coa_gr');
		$start_data_row = $start_data_row + 1;
		
		$data_coa_gr = $this->mm->get_all_coa_gr();
		
		foreach($data_coa_gr as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->accountnumber);
			$sheet->setCellValue('B'.$start_data_row, $d->accountnumberint);
			$sheet->setCellValue('C'.$start_data_row, $d->accountname);
			$sheet->setCellValue('D'.$start_data_row, $d->accountgroup);
			$sheet->setCellValue('E'.$start_data_row, $d->beginningbalance);
			$sheet->setCellValue('F'.$start_data_row, $d->status);
			$sheet->setCellValue('G'.$start_data_row, $d->type);
			$sheet->setCellValue('H'.$start_data_row, $d->idkarat);
			$sheet->setCellValue('I'.$start_data_row, $d->created_date);
			$sheet->setCellValue('J'.$start_data_row, $d->created_by);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_coa_rp');
		$start_data_row = $start_data_row + 1;
		
		$data_coa_rp = $this->mm->get_all_coa_rp();
		
		foreach($data_coa_rp as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->accountnumber);
			$sheet->setCellValue('B'.$start_data_row, $d->accountnumberint);
			$sheet->setCellValue('C'.$start_data_row, $d->accountname);
			$sheet->setCellValue('D'.$start_data_row, $d->accountgroup);
			$sheet->setCellValue('E'.$start_data_row, $d->beginningbalance);
			$sheet->setCellValue('F'.$start_data_row, $d->status);
			$sheet->setCellValue('G'.$start_data_row, $d->type);
			$sheet->setCellValue('H'.$start_data_row, $d->created_date);
			$sheet->setCellValue('I'.$start_data_row, $d->created_by);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_karyawan');
		$start_data_row = $start_data_row + 1;
		
		$data_karyawan = $this->mu->get_all_karyawan();
		
		foreach($data_karyawan as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->username);
			$sheet->setCellValue('B'.$start_data_row, $d->nama_karyawan);
			$sheet->setCellValue('C'.$start_data_row, $d->kelompok);
			$sheet->setCellValue('D'.$start_data_row, $d->accountnumber);
			$sheet->setCellValue('E'.$start_data_row, $d->status);
			$sheet->setCellValue('F'.$start_data_row, $d->created_date);
			$sheet->setCellValue('G'.$start_data_row, $d->created_by);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_kasir');
		$start_data_row = $start_data_row + 1;
		
		$data_kasir = $this->mm->get_all_kasir();
		
		foreach($data_kasir as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->computer_name);
			$sheet->setCellValue('C'.$start_data_row, $d->printer_name);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_tanggal_aktif');
		$start_data_row = $start_data_row + 1;
		
		$data_tgl_aktif = $this->mm->get_tanggal_aktif();
		
		foreach($data_tgl_aktif as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->id_kasir);
			$sheet->setCellValue('C'.$start_data_row, $d->tanggal_aktif);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_titipan_gr');
		$start_data_row = $start_data_row + 1;
		
		$data_titipan_gr = $this->mm->get_all_titipan_gr2();
		
		foreach($data_titipan_gr as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->nama_pelanggan);
			$sheet->setCellValue('C'.$start_data_row, $d->created_date);
			$sheet->setCellValue('D'.$start_data_row, $d->created_by);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_titipan_rp');
		$start_data_row = $start_data_row + 1;
		
		$data_titipan_rp = $this->mm->get_all_titipan_rp2();
		
		foreach($data_titipan_rp as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->nama_pelanggan);
			$sheet->setCellValue('C'.$start_data_row, $d->created_date);
			$sheet->setCellValue('D'.$start_data_row, $d->created_by);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_user');
		$start_data_row = $start_data_row + 1;
		
		$data_user = $this->mm->get_all_user();
		
		foreach($data_user as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->username);
			$sheet->setCellValue('C'.$start_data_row, $d->nama_user);
			$sheet->setCellValue('D'.$start_data_row, $d->password_user);
			$sheet->setCellValue('E'.$start_data_row, $d->priv_kasir);
			$sheet->setCellValue('F'.$start_data_row, $d->priv_pembukuan);
			$sheet->setCellValue('G'.$start_data_row, $d->priv_manager);
			$sheet->setCellValue('H'.$start_data_row, $d->priv_admin);
			$sheet->setCellValue('I'.$start_data_row, $d->salt);
			$sheet->setCellValue('J'.$start_data_row, $d->picture);
			$sheet->setCellValue('K'.$start_data_row, $d->status);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_dailyopen');
		$start_data_row = $start_data_row + 1;
		
		$data_do = $this->mm->get_dailyopen($tgl_from,$tgl_to);
		
		foreach($data_do as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->do_date);
			$sheet->setCellValue('C'.$start_data_row, $d->harga_emas);
			$sheet->setCellValue('D'.$start_data_row, $d->created_date);
			$sheet->setCellValue('E'.$start_data_row, $d->last_updated);
			$sheet->setCellValue('F'.$start_data_row, $d->created_by);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_product');
		$start_data_row = $start_data_row + 1;
		
		$data_product = $this->mm->get_data_product($tgl_from,$tgl_to);
		
		foreach($data_product as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->id_lama);
			$sheet->setCellValue('C'.$start_data_row, $d->id_karat);
			$sheet->setCellValue('D'.$start_data_row, $d->id_box);
			$sheet->setCellValue('E'.$start_data_row, $d->id_category);
			$sheet->setCellValue('F'.$start_data_row, $d->id_from);
			$sheet->setCellValue('G'.$start_data_row, $d->product_from_desc);
			$sheet->setCellValue('H'.$start_data_row, $d->product_name);
			$sheet->setCellValue('I'.$start_data_row, $d->product_weight);
			$sheet->setCellValue('J'.$start_data_row, $d->in_date);
			$sheet->setCellValue('K'.$start_data_row, $d->out_date);
			$sheet->setCellValue('L'.$start_data_row, $d->id_sell);
			$sheet->setCellValue('M'.$start_data_row, $d->sell_desc);
			$sheet->setCellValue('N'.$start_data_row, $d->status);
			$sheet->setCellValue('O'.$start_data_row, $d->created_date);
			$sheet->setCellValue('P'.$start_data_row, $d->created_by);
			$sheet->setCellValue('Q'.$start_data_row, $d->lock_status);
			$sheet->setCellValue('R'.$start_data_row, $d->unlock_date);
			$sheet->setCellValue('S'.$start_data_row, $d->unlock_by);
			$sheet->setCellValue('T'.$start_data_row, $d->unlock_reason);
			$sheet->setCellValue('U'.$start_data_row, $d->last_updated);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_main_pembelian');
		$start_data_row = $start_data_row + 1;
		
		$data_main_beli = $this->mm->get_main_pembelian($tgl_from,$tgl_to);
		
		foreach($data_main_beli as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->id_kasir);
			$sheet->setCellValue('C'.$start_data_row, $d->transaction_code);
			$sheet->setCellValue('D'.$start_data_row, $d->cust_service);
			$sheet->setCellValue('E'.$start_data_row, $d->cust_phone);
			$sheet->setCellValue('F'.$start_data_row, $d->cust_address);
			$sheet->setCellValue('G'.$start_data_row, $d->cust_name);
			$sheet->setCellValue('H'.$start_data_row, $d->total_price);
			$sheet->setCellValue('I'.$start_data_row, $d->trans_date);
			$sheet->setCellValue('J'.$start_data_row, $d->created_date);
			$sheet->setCellValue('K'.$start_data_row, $d->created_by);
			$sheet->setCellValue('L'.$start_data_row, $d->status);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_detail_pembelian');
		$start_data_row = $start_data_row + 1;
		
		$data_detail_beli = $this->mm->get_detail_pembelian($tgl_from,$tgl_to);
		
		foreach($data_detail_beli as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->transaction_code);
			$sheet->setCellValue('C'.$start_data_row, $d->id_kasir);
			$sheet->setCellValue('D'.$start_data_row, $d->id_product);
			$sheet->setCellValue('E'.$start_data_row, $d->id_karat);
			$sheet->setCellValue('F'.$start_data_row, $d->id_category);
			$sheet->setCellValue('G'.$start_data_row, $d->nama_product);
			$sheet->setCellValue('H'.$start_data_row, $d->product_pcs);
			$sheet->setCellValue('I'.$start_data_row, $d->product_weight);
			$sheet->setCellValue('J'.$start_data_row, $d->product_price);
			$sheet->setCellValue('K'.$start_data_row, $d->trans_date);
			$sheet->setCellValue('L'.$start_data_row, $d->created_date);
			$sheet->setCellValue('M'.$start_data_row, $d->created_by);
			$sheet->setCellValue('N'.$start_data_row, $d->status);
			$sheet->setCellValue('O'.$start_data_row, $d->persentase);
			$sheet->setCellValue('P'.$start_data_row, $d->weight_duaempat);
			$sheet->setCellValue('Q'.$start_data_row, $d->tujuan);
			$sheet->setCellValue('R'.$start_data_row, $d->kirim_date);
			$sheet->setCellValue('S'.$start_data_row, $d->created_kirim_date);
			$sheet->setCellValue('T'.$start_data_row, $d->kirim_by);
			$sheet->setCellValue('U'.$start_data_row, $d->last_update_kirim);
			$sheet->setCellValue('V'.$start_data_row, $d->update_kirim_by);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_main_penjualan');
		$start_data_row = $start_data_row + 1;
		
		$data_main_jual = $this->mm->get_main_penjualan($tgl_from,$tgl_to);
		
		foreach($data_main_jual as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->id_kasir);
			$sheet->setCellValue('C'.$start_data_row, $d->transaction_code);
			$sheet->setCellValue('D'.$start_data_row, $d->cust_service);
			$sheet->setCellValue('E'.$start_data_row, $d->cust_phone);
			$sheet->setCellValue('F'.$start_data_row, $d->cust_address);
			$sheet->setCellValue('G'.$start_data_row, $d->cust_name);
			$sheet->setCellValue('H'.$start_data_row, $d->total_price);
			$sheet->setCellValue('I'.$start_data_row, $d->bayar_1);
			$sheet->setCellValue('J'.$start_data_row, $d->bayar_2);
			$sheet->setCellValue('K'.$start_data_row, $d->jenis_bayar_1);
			$sheet->setCellValue('L'.$start_data_row, $d->jenis_bayar_2);
			$sheet->setCellValue('M'.$start_data_row, $d->trans_date);
			$sheet->setCellValue('N'.$start_data_row, $d->created_date);
			$sheet->setCellValue('O'.$start_data_row, $d->created_by);
			$sheet->setCellValue('P'.$start_data_row, $d->status);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_detail_penjualan');
		$start_data_row = $start_data_row + 1;
		
		$data_detail_jual = $this->mm->get_detail_penjualan($tgl_from,$tgl_to);
		
		foreach($data_detail_jual as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->transaction_code);
			$sheet->setCellValue('C'.$start_data_row, $d->id_kasir);
			$sheet->setCellValue('D'.$start_data_row, $d->id_product);
			$sheet->setCellValue('E'.$start_data_row, $d->id_box);
			$sheet->setCellValue('F'.$start_data_row, $d->id_karat);
			$sheet->setCellValue('G'.$start_data_row, $d->nama_product);
			$sheet->setCellValue('H'.$start_data_row, $d->product_desc);
			$sheet->setCellValue('I'.$start_data_row, $d->product_weight);
			$sheet->setCellValue('J'.$start_data_row, $d->product_price);
			$sheet->setCellValue('K'.$start_data_row, $d->trans_date);
			$sheet->setCellValue('L'.$start_data_row, $d->created_date);
			$sheet->setCellValue('M'.$start_data_row, $d->created_by);
			$sheet->setCellValue('N'.$start_data_row, $d->status);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_main_pesanan');
		$start_data_row = $start_data_row + 1;
		
		$data_main_pesanan = $this->mm->get_main_pesanan($tgl_from,$tgl_to);
		
		foreach($data_main_pesanan as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id_pesanan);
			$sheet->setCellValue('B'.$start_data_row, $d->cust_name);
			$sheet->setCellValue('C'.$start_data_row, $d->cust_address);
			$sheet->setCellValue('D'.$start_data_row, $d->cust_phone);
			$sheet->setCellValue('E'.$start_data_row, $d->ump_val);
			$sheet->setCellValue('F'.$start_data_row, $d->total_trans);
			$sheet->setCellValue('G'.$start_data_row, $d->trans_date);
			$sheet->setCellValue('H'.$start_data_row, $d->created_date);
			$sheet->setCellValue('I'.$start_data_row, $d->created_by);
			$sheet->setCellValue('J'.$start_data_row, $d->box_date);
			$sheet->setCellValue('K'.$start_data_row, $d->box_by);
			$sheet->setCellValue('L'.$start_data_row, $d->box_created_date);
			$sheet->setCellValue('M'.$start_data_row, $d->ambil_date);
			$sheet->setCellValue('N'.$start_data_row, $d->ambil_by);
			$sheet->setCellValue('O'.$start_data_row, $d->ambil_created_date);
			$sheet->setCellValue('P'.$start_data_row, $d->updated_date);
			$sheet->setCellValue('Q'.$start_data_row, $d->updated_by);
			$sheet->setCellValue('R'.$start_data_row, $d->last_updated);
			$sheet->setCellValue('S'.$start_data_row, $d->grosir_use);
			$sheet->setCellValue('T'.$start_data_row, $d->status);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_detail_pesanan');
		$start_data_row = $start_data_row + 1;
		
		$data_detail_pesanan = $this->mm->get_detail_pesanan($tgl_from,$tgl_to);
		
		foreach($data_detail_pesanan as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->id_pesanan);
			$sheet->setCellValue('C'.$start_data_row, $d->id_karat);
			$sheet->setCellValue('D'.$start_data_row, $d->id_category);
			$sheet->setCellValue('E'.$start_data_row, $d->nama_pesanan);
			$sheet->setCellValue('F'.$start_data_row, $d->id_product);
			$sheet->setCellValue('G'.$start_data_row, $d->product_weight);
			$sheet->setCellValue('H'.$start_data_row, $d->harga_jual);
			$sheet->setCellValue('I'.$start_data_row, $d->trans_date);
			$sheet->setCellValue('J'.$start_data_row, $d->created_date);
			$sheet->setCellValue('K'.$start_data_row, $d->created_by);
			$sheet->setCellValue('L'.$start_data_row, $d->box_date);
			$sheet->setCellValue('M'.$start_data_row, $d->box_by);
			$sheet->setCellValue('N'.$start_data_row, $d->box_created_date);
			$sheet->setCellValue('O'.$start_data_row, $d->ambil_date);
			$sheet->setCellValue('P'.$start_data_row, $d->ambil_by);
			$sheet->setCellValue('Q'.$start_data_row, $d->ambil_created_date);
			$sheet->setCellValue('R'.$start_data_row, $d->updated_date);
			$sheet->setCellValue('S'.$start_data_row, $d->updated_by);
			$sheet->setCellValue('T'.$start_data_row, $d->last_updated);
			$sheet->setCellValue('U'.$start_data_row, $d->status);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_master_product_name');
		$start_data_row = $start_data_row + 1;
		
		$data_master_product = $this->mm->get_master_product($tgl_from,$tgl_to);
		
		foreach($data_master_product as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->id_category);
			$sheet->setCellValue('C'.$start_data_row, $d->nama_barang);
			$sheet->setCellValue('D'.$start_data_row, $d->created_date);
			$sheet->setCellValue('E'.$start_data_row, $d->created_by);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_mutasi_gr');
		$start_data_row = $start_data_row + 1;
		
		$data_mutasi_gr = $this->mm->get_mutasi_gr($tgl_from,$tgl_to);
		
		foreach($data_mutasi_gr as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->idsite);
			$sheet->setCellValue('B'.$start_data_row, $d->idmutasi);
			$sheet->setCellValue('C'.$start_data_row, $d->tipemutasi);
			$sheet->setCellValue('D'.$start_data_row, $d->idkarat);
			$sheet->setCellValue('E'.$start_data_row, $d->fromaccount);
			$sheet->setCellValue('F'.$start_data_row, $d->toaccount);
			$sheet->setCellValue('G'.$start_data_row, $d->value);
			$sheet->setCellValue('H'.$start_data_row, $d->description);
			$sheet->setCellValue('I'.$start_data_row, $d->transdate);
			$sheet->setCellValue('J'.$start_data_row, $d->createddate);
			$sheet->setCellValue('K'.$start_data_row, $d->createdby);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_mutasi_gr_hapus');
		$start_data_row = $start_data_row + 1;
		
		$data_mutasi_gr_hapus = $this->mm->get_mutasi_gr_hapus($tgl_from,$tgl_to);
		
		foreach($data_mutasi_gr_hapus as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->idsite);
			$sheet->setCellValue('B'.$start_data_row, $d->idmutasi);
			$sheet->setCellValue('C'.$start_data_row, $d->tipemutasi);
			$sheet->setCellValue('D'.$start_data_row, $d->idkarat);
			$sheet->setCellValue('E'.$start_data_row, $d->fromaccount);
			$sheet->setCellValue('F'.$start_data_row, $d->toaccount);
			$sheet->setCellValue('G'.$start_data_row, $d->value);
			$sheet->setCellValue('H'.$start_data_row, $d->description);
			$sheet->setCellValue('I'.$start_data_row, $d->transdate);
			$sheet->setCellValue('J'.$start_data_row, $d->createddate);
			$sheet->setCellValue('K'.$start_data_row, $d->createdby);
			$sheet->setCellValue('L'.$start_data_row, $d->deleteddate);
			$sheet->setCellValue('M'.$start_data_row, $d->deletedby);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_mutasi_rp');
		$start_data_row = $start_data_row + 1;
		
		$data_mutasi_rp = $this->mm->get_mutasi_rp($tgl_from,$tgl_to);
		
		foreach($data_mutasi_rp as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->idsite);
			$sheet->setCellValue('B'.$start_data_row, $d->idmutasi);
			$sheet->setCellValue('C'.$start_data_row, $d->tipemutasi);
			$sheet->setCellValue('D'.$start_data_row, $d->fromaccount);
			$sheet->setCellValue('E'.$start_data_row, $d->toaccount);
			$sheet->setCellValue('F'.$start_data_row, $d->value);
			$sheet->setCellValue('G'.$start_data_row, $d->description);
			$sheet->setCellValue('H'.$start_data_row, $d->transdate);
			$sheet->setCellValue('I'.$start_data_row, $d->createddate);
			$sheet->setCellValue('J'.$start_data_row, $d->createdby);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_mutasi_rp_hapus');
		$start_data_row = $start_data_row + 1;
		
		$data_mutasi_rp_hapus = $this->mm->get_mutasi_rp_hapus($tgl_from,$tgl_to);
		
		foreach($data_mutasi_rp_hapus as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->idsite);
			$sheet->setCellValue('B'.$start_data_row, $d->idmutasi);
			$sheet->setCellValue('C'.$start_data_row, $d->tipemutasi);
			$sheet->setCellValue('D'.$start_data_row, $d->fromaccount);
			$sheet->setCellValue('E'.$start_data_row, $d->toaccount);
			$sheet->setCellValue('F'.$start_data_row, $d->value);
			$sheet->setCellValue('G'.$start_data_row, $d->description);
			$sheet->setCellValue('H'.$start_data_row, $d->transdate);
			$sheet->setCellValue('I'.$start_data_row, $d->createddate);
			$sheet->setCellValue('J'.$start_data_row, $d->createdby);
			$sheet->setCellValue('K'.$start_data_row, $d->deleteddate);
			$sheet->setCellValue('L'.$start_data_row, $d->deletedby);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_mutasi_pengadaan');
		$start_data_row = $start_data_row + 1;
		
		$data_mutasi_pengadaan = $this->mm->get_mutasi_pengadaan($tgl_from,$tgl_to);
		
		foreach($data_mutasi_pengadaan as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->id_pengiriman);
			$sheet->setCellValue('C'.$start_data_row, $d->from_buy);
			$sheet->setCellValue('D'.$start_data_row, $d->tipe);
			$sheet->setCellValue('E'.$start_data_row, $d->fromaccount);
			$sheet->setCellValue('F'.$start_data_row, $d->toaccount);
			$sheet->setCellValue('G'.$start_data_row, $d->description);
			$sheet->setCellValue('H'.$start_data_row, $d->dua_empat);
			$sheet->setCellValue('I'.$start_data_row, $d->semsanam);
			$sheet->setCellValue('J'.$start_data_row, $d->juhlima);
			$sheet->setCellValue('K'.$start_data_row, $d->juhtus);
			$sheet->setCellValue('L'.$start_data_row, $d->total_konv);
			$sheet->setCellValue('M'.$start_data_row, $d->trans_date);
			$sheet->setCellValue('N'.$start_data_row, $d->created_date);
			$sheet->setCellValue('O'.$start_data_row, $d->created_by);
			$sheet->setCellValue('P'.$start_data_row, $d->last_updated);
			$sheet->setCellValue('Q'.$start_data_row, $d->last_updated_by);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_mutasi_pengadaan_hapus');
		$start_data_row = $start_data_row + 1;
		
		$data_mutasi_pengadaan_hapus = $this->mm->get_mutasi_pengadaan_hapus($tgl_from,$tgl_to);
		
		foreach($data_mutasi_pengadaan_hapus as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->id_pengiriman);
			$sheet->setCellValue('C'.$start_data_row, $d->from_buy);
			$sheet->setCellValue('D'.$start_data_row, $d->tipe);
			$sheet->setCellValue('E'.$start_data_row, $d->fromaccount);
			$sheet->setCellValue('F'.$start_data_row, $d->toaccount);
			$sheet->setCellValue('G'.$start_data_row, $d->description);
			$sheet->setCellValue('H'.$start_data_row, $d->dua_empat);
			$sheet->setCellValue('I'.$start_data_row, $d->semsanam);
			$sheet->setCellValue('J'.$start_data_row, $d->juhlima);
			$sheet->setCellValue('K'.$start_data_row, $d->juhtus);
			$sheet->setCellValue('L'.$start_data_row, $d->total_konv);
			$sheet->setCellValue('M'.$start_data_row, $d->trans_date);
			$sheet->setCellValue('N'.$start_data_row, $d->deleted_date);
			$sheet->setCellValue('O'.$start_data_row, $d->deleted_by);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_mutasi_reparasi');
		$start_data_row = $start_data_row + 1;
		
		$data_mutasi_reparasi = $this->mm->get_mutasi_reparasi($tgl_from,$tgl_to);
		
		foreach($data_mutasi_reparasi as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->id_pengiriman);
			$sheet->setCellValue('C'.$start_data_row, $d->from_buy);
			$sheet->setCellValue('D'.$start_data_row, $d->tipe);
			$sheet->setCellValue('E'.$start_data_row, $d->fromaccount);
			$sheet->setCellValue('F'.$start_data_row, $d->toaccount);
			$sheet->setCellValue('G'.$start_data_row, $d->description);
			$sheet->setCellValue('H'.$start_data_row, $d->dua_empat);
			$sheet->setCellValue('I'.$start_data_row, $d->dua_empat_konv);
			$sheet->setCellValue('J'.$start_data_row, $d->semsanam);
			$sheet->setCellValue('K'.$start_data_row, $d->semsanam_konv);
			$sheet->setCellValue('L'.$start_data_row, $d->juhlima);
			$sheet->setCellValue('M'.$start_data_row, $d->juhlima_konv);
			$sheet->setCellValue('N'.$start_data_row, $d->juhtus);
			$sheet->setCellValue('O'.$start_data_row, $d->juhtus_konv);
			$sheet->setCellValue('P'.$start_data_row, $d->total_konv);
			$sheet->setCellValue('Q'.$start_data_row, $d->trans_date);
			$sheet->setCellValue('R'.$start_data_row, $d->created_date);
			$sheet->setCellValue('S'.$start_data_row, $d->created_by);
			$sheet->setCellValue('T'.$start_data_row, $d->last_updated);
			$sheet->setCellValue('U'.$start_data_row, $d->last_updated_by);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_mutasi_reparasi_hapus');
		$start_data_row = $start_data_row + 1;
		
		$data_mutasi_reparasi_hapus = $this->mm->get_mutasi_reparasi_hapus($tgl_from,$tgl_to);
		
		foreach($data_mutasi_reparasi_hapus as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->id_pengiriman);
			$sheet->setCellValue('C'.$start_data_row, $d->from_buy);
			$sheet->setCellValue('D'.$start_data_row, $d->tipe);
			$sheet->setCellValue('E'.$start_data_row, $d->fromaccount);
			$sheet->setCellValue('F'.$start_data_row, $d->toaccount);
			$sheet->setCellValue('G'.$start_data_row, $d->description);
			$sheet->setCellValue('H'.$start_data_row, $d->dua_empat);
			$sheet->setCellValue('I'.$start_data_row, $d->dua_empat_konv);
			$sheet->setCellValue('J'.$start_data_row, $d->semsanam);
			$sheet->setCellValue('K'.$start_data_row, $d->semsanam_konv);
			$sheet->setCellValue('L'.$start_data_row, $d->juhlima);
			$sheet->setCellValue('M'.$start_data_row, $d->juhlima_konv);
			$sheet->setCellValue('N'.$start_data_row, $d->juhtus);
			$sheet->setCellValue('O'.$start_data_row, $d->juhtus_konv);
			$sheet->setCellValue('P'.$start_data_row, $d->total_konv);
			$sheet->setCellValue('Q'.$start_data_row, $d->trans_date);
			$sheet->setCellValue('R'.$start_data_row, $d->deleted_date);
			$sheet->setCellValue('S'.$start_data_row, $d->deleted_by);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_pindah_box');
		$start_data_row = $start_data_row + 1;
		
		$data_pindah_box = $this->mm->get_pindah_box($tgl_from,$tgl_to);
		
		foreach($data_pindah_box as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->id_product);
			$sheet->setCellValue('C'.$start_data_row, $d->id_karat);
			$sheet->setCellValue('D'.$start_data_row, $d->id_box_from);
			$sheet->setCellValue('E'.$start_data_row, $d->id_box_to);
			$sheet->setCellValue('F'.$start_data_row, $d->trans_date);
			$sheet->setCellValue('G'.$start_data_row, $d->created_date);
			$sheet->setCellValue('H'.$start_data_row, $d->created_by);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_stock_in');
		$start_data_row = $start_data_row + 1;
		
		$data_stock_in = $this->mm->get_stock_in($tgl_from,$tgl_to);
		
		foreach($data_stock_in as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->id_karat);
			$sheet->setCellValue('C'.$start_data_row, $d->id_box);
			$sheet->setCellValue('D'.$start_data_row, $d->id_category);
			$sheet->setCellValue('E'.$start_data_row, $d->id_from);
			$sheet->setCellValue('F'.$start_data_row, $d->id_from_desc);
			$sheet->setCellValue('G'.$start_data_row, $d->product_name);
			$sheet->setCellValue('H'.$start_data_row, $d->product_weight);
			$sheet->setCellValue('I'.$start_data_row, $d->trans_date);
			$sheet->setCellValue('J'.$start_data_row, $d->created_date);
			$sheet->setCellValue('K'.$start_data_row, $d->created_by);
			
			$start_data_row = $start_data_row + 1;
		}
		
		$sheet->setCellValue('A'.$start_data_row, 'gold_stock_out');
		$start_data_row = $start_data_row + 1;
		
		$data_stock_out = $this->mm->get_stock_out($tgl_from,$tgl_to);
		
		foreach($data_stock_out as $d){
			$sheet->setCellValue('A'.$start_data_row, $d->id);
			$sheet->setCellValue('B'.$start_data_row, $d->id_product);
			$sheet->setCellValue('C'.$start_data_row, $d->id_karat);
			$sheet->setCellValue('D'.$start_data_row, $d->id_box);
			$sheet->setCellValue('E'.$start_data_row, $d->so_reason);
			$sheet->setCellValue('F'.$start_data_row, $d->trans_date);
			$sheet->setCellValue('G'.$start_data_row, $d->created_date);
			$sheet->setCellValue('H'.$start_data_row, $d->created_by);
			
			$start_data_row = $start_data_row + 1;
		}
		
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

		//sesuaikan headernya 
		//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		//header("Cache-Control: no-store, no-cache, must-revalidate");
		//header("Cache-Control: post-check=0, pre-check=0", false);
		//header("Pragma: no-cache");
		//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		//header('Content-Disposition: attachment;filename="LAPORAN EXCEL '.$site_name.' per '.$tanggal_transaksi.'.xlsx"');
		$excelFilePath = "report/LAP EXCEL ".$site_name." Tanggal ".$tgl_transaksi.".xlsx";
		//unduh file
		//$objWriter->save("php://output");
		$objWriter->save($excelFilePath);
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