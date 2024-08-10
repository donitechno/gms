<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\IOFactory;

class C_import_pajangan extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE || $this->session->userdata('gold_admin') != 'Y'){
			redirect();
		}
		
		$this->load->model('M_product','mp');
		$this->load->model('M_karat','mk');
		ini_set('memory_limit', '512M');
	}
	
	public function index(){
		$this->load->view('persediaan/V_import_product');
	}
	
	/*-- EXECUTE IMPORT DARI EXCEL --*/
	public function save_import(){
		//$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		
		$this->db->trans_start();
		
		$fileName = $_FILES['file_excel']['name'];
		$fileName = str_replace(' ','_',$fileName);
        $config['upload_path'] = './import';
        $config['file_name'] = $fileName;
        $config['allowed_types'] = 'xls|xlsx|csv';
        $config['max_size'] = 9999999;
         
        $this->load->library('upload');
        $this->upload->initialize($config);
		
		$file_name = './import/'.$fileName;

		if (file_exists($file_name)) {
			unlink($file_name);
		} else {
			
		}
		
		if(!$this->upload->do_upload('file_excel')){
			$data['img'] = base_url().'assets/images/error.png';
			$data['location'] = base_url();
			$data['color'] = "#e74c3c";
			$data['pesan'] = "Gagal Upload File!";
			$this->load->view("import/V_notif",$data);
		}else{

		}
		
		$inputFileName = './import/'.$fileName;
         
        try{
			$inputFileType = IOFactory::identify($inputFileName);
			$objReader = IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFileName);
		}catch(Exception $e){
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}
		
		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
		
		$in_date = date("Y-m-d",strtotime("-1 days")).' 00:00:00';
		$created_by = $this->session->userdata('nama_user');
		
		for($row = 1; $row <= $highestRow; $row++){
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
			
			$sales_date = $rowData[0][13];
			if($sales_date == 'NULL'){
				$id = $rowData[0][1];
				$id_lama = $rowData[0][31];
				$karatname = $rowData[0][5];
				$karatname = str_replace(' ','',$karatname);
				
				$id_karat = $this->mk->get_karat_id_by_name($karatname);
				$id_box = $rowData[0][17];
				$id_category = $rowData[0][6];
				if($id_category == '6'){
					$id_category = 2;
				}
				$id_from = '4';
				$from_desc = 'MIGRASI';
				$product_name = $rowData[0][3];
				$product_weight = $rowData[0][8];
				
				//echo $id.'<br>';
				
				$this->mp->insert_product($id,$id_lama,$id_karat,$id_box,$id_category,$id_from,$from_desc,$product_name,$product_weight,$in_date,$created_by);
			}
		}
		
		$this->db->trans_complete();
		unlink('./import/'.$fileName);
		$data['tipe'] = 'positive';
		$data['icon'] = 'thumbs up outline';
		$data['location'] = base_url().'index.php/C_posisi_pajangan';
		$data['pesan'] = "Berhasil Import Data!";
		$this->load->view("import/V_notif",$data);
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
