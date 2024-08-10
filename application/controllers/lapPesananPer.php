<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LapPesananPer extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->library('M_pdf');
		$this->load->model('M_karat','mk');
		$this->load->model('M_product','mp');
		$this->load->model('M_pos','mt');
		$this->load->model('M_mutasi','mm');
	}
	
	public function index(){
		$data['view'] = '<div class="ui fluid container no-print">
		<form class="ui form form-javascript" id="lapPesananPer-form-filter" action="'.base_url().'index.php/lapPesananPer/filter" method="post">
		<div class="ui grid">
			<div class="ui inverted dimmer" id="lapPesananPer-loaderlist">
				<div class="ui large text loader">Loading</div>
			</div>
			<div class="five wide centered column no-print" style="margin-top:15px">
				<div class="fields">
					<div class="ten wide field">
						<label>Per Tanggal</label>
						<input type="text" name="lapPesananPer-date" id="lapPesananPer-date" readonly>
					</div>
					<div class="six wide field">
						<label style="visibility:hidden">-</label>
						<div class="ui fluid icon green button filter-input" id="lapPesananPer-btnfilter" onclick=filterTransaksi("lapPesananPer") title="Filter">
							<i class="filter icon"></i> Filter
						</div>
					</div>
				</div>
			</div>
			<div class="fifteen wide centered column" id="lapPesananPer-wrap_filter">
			</div>
		</div>
		</form>';
		
		$data["date"] = 1;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function filter(){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		
		$tgl_transaksi =  $this->input->post('lapPesananPer-date');
		$tanggal_transaksi =  $this->input->post('lapPesananPer-date');
		
		$tglTrans = $this->date_to_format($tgl_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		
		$site_name = $this->mm->get_site_name();
		
		$data_filter = $this->mp->get_pesanan_per($tgl_transaksi);
		
		$data['view'] = '<div class="sixteen wide centered column" style="text-align:right"><a class="ui purple button" href="'.base_url().'index.php/lapPesananPer/excel/'.$tanggal_transaksi.'"><i class="file excel icon"></i> Export to Excel</a><a class="ui facebook button" href="'.base_url().'index.php/lapPesananPer/pdf/'.$tanggal_transaksi.'" target="_blank"><i class="paperclip icon"></i> Download</a></div><table class="ui celled table" cellspacing="0" width="100%"><thead><tr><th style="width:50px">No</th><th>ID</th><th>Tgl Pesan</th><th>Customer</th><th>Alamat</th><th>Telepon</th><th>UMP</th></tr></thead><tbody>';
		
		$number = 1;
		$total_ump = 0;
		foreach($data_filter as $d){
			$idpesan = $d->id_pesanan;
			$idpesan = str_replace('PS','',$idpesan);
			
			$psnlength = strlen($idpesan);
			
			$ump_val = $d->ump_val;

			$tambah_ump = $this->mp->get_tambah_ump($idpesan,$tgl_transaksi,$psnlength);
			foreach($tambah_ump as $t){
				$ump_val = $ump_val - $t->value;
			}
			
			$kurang_ump = $this->mp->get_kurang_ump($idpesan,$tgl_transaksi,$psnlength);
			foreach($kurang_ump as $k){
				$ump_val = $ump_val + $k->value;
			}
			
			$tanggal_tulis = strtotime($d->trans_date);
			$tanggal_tulis = date('d-M-y',$tanggal_tulis);
			
			$data['view'] .= '<tr><td class="center aligned">'.$number.'</td><td>'.$d->id_pesanan.'</td><td>'.$tanggal_tulis.'</td><td>'.$d->cust_name.'</td><td>'.$d->cust_address.'</td><td>'.$d->cust_phone.'</td><td class="right aligned">'.number_format($ump_val, 0).'</td></tr>';
			
			$total_ump = $total_ump + $ump_val;
			
			$number = $number + 1;
		}
		
		$data['view'] .= '<tr><td class="right aligned" colspan="6" style="border-top:double #000; font-weight:600">Total</td><td class="right aligned" style="border-top:double #000; font-weight:600">'.number_format($total_ump, 0).'</td></tr>';
		
		$data['view'] .= '</tbody></table>';
		
		$this->db->trans_complete();
		
		$data['success'] = TRUE;
		echo json_encode($data);
	}
	
	public function pdf($tgl_transaksi){
		date_default_timezone_set("Asia/Jakarta");
	
		$tgl_transaksi = str_replace('%20',' ',$tgl_transaksi);
		$tanggal_transaksi =  $tgl_transaksi;
		
		$tglTrans = $this->date_to_format($tgl_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		
		$site_name = $this->mm->get_site_name();
		
		$data_filter = $this->mp->get_pesanan_per($tgl_transaksi);
		
		$data['view'] = '<div class="sixteen wide centered column center aligned" style="font-weight:bold;text-align:center;font-size:13px;font-family:samsungone"><span style="text-align">Laporan Pesanan Pelanggan, Cabang '.$site_name.'</span><br><span>Per Tanggal '.$tanggal_transaksi.'</span><br><span style="visibility:hidden">-</span></div><table class="lap_pdf_6" cellspacing="0" width="100%"><thead><tr><th class="th-5" style="width:50px">No</th><th class="th-5">ID</th><th class="th-5">Tgl Pesan</th><th class="th-5">Customer</th><th class="th-5">Alamat</th><th class="th-5">Telepon</th><th class="th-5">UMP</th></tr></thead><tbody>';
		
		$number = 1;
		$total_ump = 0;
		foreach($data_filter as $d){
			$idpesan = $d->id_pesanan;
			$idpesan = str_replace('PS','',$idpesan);
			
			$psnlength = strlen($idpesan);
			
			$ump_val = $d->ump_val;

			$tambah_ump = $this->mp->get_tambah_ump($idpesan,$tgl_transaksi,$psnlength);
			foreach($tambah_ump as $t){
				$ump_val = $ump_val - $t->value;
			}
			
			$kurang_ump = $this->mp->get_kurang_ump($idpesan,$tgl_transaksi,$psnlength);
			foreach($kurang_ump as $k){
				$ump_val = $ump_val + $k->value;
			}
			
			$tanggal_tulis = strtotime($d->trans_date);
			$tanggal_tulis = date('d-M-y',$tanggal_tulis);
			
			$data['view'] .= '<tr><td class="center-aligned">'.$number.'</td><td>'.$d->id_pesanan.'</td><td>'.$tanggal_tulis.'</td><td>'.$d->cust_name.'</td><td>'.$d->cust_address.'</td><td>'.$d->cust_phone.'</td><td class="right-aligned">'.number_format($ump_val, 0).'</td></tr>';
			
			$total_ump = $total_ump + $ump_val;
			
			$number = $number + 1;
		}
		
		$data['view'] .= '<tr><td class="right-aligned" colspan="6" style="border-top:double #000; font-weight:bold">Total</td><td class="right-aligned" style="border-top:double #000; font-weight:bold">'.number_format($total_ump, 0).'</td></tr>';
		
		$data['view'] .= '</tbody></table>';
		
		$html = $this->load->view("pdf/V_pdf",$data, TRUE);
		$css = base_url().'assets/css/gold.css';
		
		$stylesheet = file_get_contents($css);
		
		$pdf = $this->m_pdf->load();
		$pdf->SetHTMLFooter('<div style="width:100%;text-align:right"><img src="'. base_url().'assets/images/branding/fbn.png" style="width:32px"/></div>');
		$pdf->AddPage('p');
		$pdf->WriteHTML($html);
		
		$pdf->Output("Laporan Pesanan Per Tanggal.pdf", "I");
	}
	
	public function excel($tgl_transaksi){
		date_default_timezone_set("Asia/Jakarta");
	
		$tgl_transaksi = str_replace('%20',' ',$tgl_transaksi);
		$tanggal_transaksi =  $tgl_transaksi;
		
		$tglTrans = $this->date_to_format($tgl_transaksi);
		$tgl_transaksi = date("Y-m-d",$tglTrans).' 00:00:00';
		
		$site_name = $this->mm->get_site_name();
		
		$data_filter = $this->mp->get_pesanan_per($tgl_transaksi);
		
		//$this->load->library('Libexcel');
		PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
		$objPHPExcel = new PHPExcel();
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setTitle($site_name);
		
		$style_font = array(
			'font'  => array(
				'name'  => 'Segoe UI'
			)
		);
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
		$objPHPExcel->getActiveSheet()->getStyle('A1:G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$style_font_header = array(
			'font'  => array(
				'bold'  => true
			)
		);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:G4')->applyFromArray($style_font_header);
		
		$sheet->setCellValue('A1', 'Laporan Pesanan Pelanggan, Cabang '.$site_name);
		$sheet->setCellValue('A2', 'Per Tanggal '.$tanggal_transaksi);
		
		$sheet->setCellValue('A4', 'No');
		$sheet->setCellValue('B4', 'ID Pesanan');
		$sheet->setCellValue('C4', 'Tanggal Pesan');
		$sheet->setCellValue('D4', 'Customer');
		$sheet->setCellValue('E4', 'Alamat');
		$sheet->setCellValue('F4', 'Telepon');
		$sheet->setCellValue('G4', 'UMP');
		
		$objPHPExcel->getActiveSheet()->getStyle("A4:G4")->applyFromArray(array(
			'borders' => array(
				'top' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				),
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		));
		
		$baris = 5;
		
		$number = 1;
		$total_ump = 0;
		foreach($data_filter as $d){
			$idpesan = $d->id_pesanan;
			$idpesan = str_replace('PS','',$idpesan);
			
			$psnlength = strlen($idpesan);
			
			$ump_val = $d->ump_val;

			$tambah_ump = $this->mp->get_tambah_ump($idpesan,$tgl_transaksi,$psnlength);
			foreach($tambah_ump as $t){
				$ump_val = $ump_val - $t->value;
			}
			
			$kurang_ump = $this->mp->get_kurang_ump($idpesan,$tgl_transaksi,$psnlength);
			foreach($kurang_ump as $k){
				$ump_val = $ump_val + $k->value;
			}
			
			$tanggal_tulis = strtotime($d->trans_date);
			$tanggal_tulis = date('d-M-y',$tanggal_tulis);
			
			$sheet->setCellValue('A'.$baris.'', $number);
			$sheet->setCellValue('B'.$baris.'', $d->id_pesanan);
			$sheet->setCellValue('C'.$baris.'', $tanggal_tulis);
			$sheet->setCellValue('D'.$baris.'', $d->cust_name);
			$sheet->setCellValue('E'.$baris.'', $d->cust_address);
			$sheet->setCellValue('F'.$baris.'', $d->cust_phone);
			$sheet->setCellValue('G'.$baris.'', $ump_val);
			
			$total_ump = $total_ump + $ump_val;
			
			$baris = $baris + 1;
			
			$number = $number + 1;
		}
		
		$sheet->setCellValue('F'.$baris.'', 'Total');
		$sheet->setCellValue('G'.$baris.'', $total_ump);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$baris.'')->applyFromArray($style_font);
		$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$baris.'')->getAlignment()->setWrapText(true);
		
		$objPHPExcel->getActiveSheet()->getStyle("F".$baris.":G".$baris)->applyFromArray(array(
			'borders' => array(
				'top' => array(
					'style' => PHPExcel_Style_Border::BORDER_DOUBLE
				)
			)
		));
		
		$objPHPExcel->getActiveSheet()->getStyle('G'.$baris.':G'.$baris.'')->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)');
		
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="EXCEL LAPORAN PESANAN '.$site_name.' PER TANGGAL '.$tanggal_transaksi.'.xlsx"');
		//unduh file
		$objWriter->save("php://output");
	}
	
	/* UBAH FORMAT TANGGAL */
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
