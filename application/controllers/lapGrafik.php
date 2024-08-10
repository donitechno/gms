<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LapGrafik extends CI_Controller {	
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->model('M_login','ml');
		$this->load->model('M_karat','mk');
		$this->load->model('M_kelompok_barang','mc');
		$this->load->model('M_product','mp');
		$this->load->model('M_pos','mt');
		$this->load->model('M_mutasi','mm');
	}
	
	public function index(){
		$data['view'] = '<div class="ui container fluid">
			<form class="ui form form-javascript" id="lapGrafik-form-filter" action="'.base_url().'index.php/lapGrafik/filter/" method="post">
			<div class="ui grid">
				<div class="ui inverted dimmer" id="lapGrafik-loaderlist">
					<div class="ui large text loader">Loading</div>
				</div>
				<div class="thirteen wide centered column" style="padding-bottom:0">
					<div class="fields">
						<div class="four wide field">
							<label>Tgl Transaksi</label>
							<input type="text" name="lapGrafik-datefrom" id="lapGrafik-datefrom" readonly>
						</div>
						<div class="one wide field" style="text-align:center;margin-top:30px">
							<label>s.d</label>
						</div>
						<div class="four wide field">
							<label style="visibility:hidden">-</label>
							<input type="text" name="lapGrafik-dateto" id="lapGrafik-dateto" readonly>
						</div>
						<div class="two wide field">
							<label style="visibility:hidden">-</label>
							<select name="lapGrafik-filter_data" id="lapGrafik-filter_data">
								<option value="P">Pcs</option>
								<option value="I">Gram</option>
							</select>
						</div>
						<div class="three wide field">
							<label style="visibility:hidden">-</label>
							<select name="lapGrafik-filter_data_2" id="lapGrafik-filter_data_2">
								<option value="K">per Kelompok</option>
								<option value="G">Gabungan</option>
							</select>
						</div>
						<div class="two wide field">
							<label style="visibility:hidden">-</label>
							<div class="ui fluid icon green button filter-input" id="lapGrafik-btnfilter" onclick=filterTransaksiGrafik("lapGrafik") title="Filter">
								<i class="filter icon"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="fourteen wide centered column" id="lapGrafik-wrap_filter" style="padding-top:0">
					<canvas id="canvas"></canvas>
				</div>
			</div>
			</form>
		</div>';
		
		$data["date"] = 2;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function filter(){
		$this->db->trans_start();
		
		$from_date = $this->input->post('lapGrafik-datefrom');
		$dari_tanggal = $from_date;
		$to_date = $this->input->post('lapGrafik-dateto');
		$sampai_tanggal = $to_date;
		
		$stockFromTime = $this->date_to_format($from_date);
		$stockToTime = $this->date_to_format($to_date);
		
		$from_date = date("Y-m-d",$stockFromTime).' 00:00:00';
		$to_date = date("Y-m-d",$stockToTime).' 00:00:00';
		$to_date2 = date("Y-m-d",$stockToTime).' 23:59:59';
		
		$filter_data = $this->input->post('lapGrafik-filter_data');
		$filter_data_2 = $this->input->post('lapGrafik-filter_data_2');
		
		if($filter_data ==	'P'){
			$data_karat = $this->mk->get_karat_sdr();
			$labels = array();
			foreach($data_karat as $dk){
				$labels[] = $dk->karat_name;
			}
			//$data['labels'] = $labels;
			
			$data_category = $this->mc->get_all_product_category();
			foreach($data_category as $dc){
				$data['label'] = $dc->category_name;
				$data['backgroundColor'] = $dc->color_rgba;
				$data['borderColor'] = $dc->color_rgba;
				$data['borderWidth'] = 1;
				
				$datachart = array();
				foreach($data_karat as $dk){
					$data_sell = $this->mt->get_chart_sell($dk->id,$dc->id,$from_date,$to_date2);
					if(count($data_sell) == 0){
						$datachart[] = 0;
					}else{
						$datachart[] = $data_sell[0]->total;
					}
				}
				
				$data['data'] = $datachart;
				
				$data_lengkap[] = $data;
			}
			
			$data_tulis['labels'] = $labels;
			$data_flag['rd'] = $filter_data;
			$data_flag['rd2'] = $filter_data_2;
			$data_tulis['datasets'] = $data_lengkap;
			$data_flag['success'] = TRUE;
			$data_flag['hasil'] = $data_tulis;
		}else{
			$data_karat = $this->mk->get_karat_sdr();
			$labels = array();
			foreach($data_karat as $dk){
				$labels[] = $dk->karat_name;
			}
			//$data['labels'] = $labels;
			
			$data_category = $this->mc->get_all_product_category();
			foreach($data_category as $dc){
				$data['label'] = $dc->category_name;
				$data['backgroundColor'] = $dc->color_rgba;
				$data['borderColor'] = $dc->color_rgba;
				$data['borderWidth'] = 1;
				
				$datachart = array();
				foreach($data_karat as $dk){
					$data_sell = $this->mt->get_chart_sell_idr($dk->id,$dc->id,$from_date,$to_date2);
					if(count($data_sell) == 0){
						$datachart[] = 0;
					}else{
						$datachart[] = $data_sell[0]->total;
					}
				}
				
				$data['data'] = $datachart;
				
				$data_lengkap[] = $data;
			}
			
			$data_tulis['labels'] = $labels;
			$data_tulis['datasets'] = $data_lengkap;
			$data_flag['rd'] = $filter_data;
			$data_flag['rd2'] = $filter_data_2;
			$data_flag['success'] = TRUE;
			$data_flag['hasil'] = $data_tulis;
		}
		
		echo json_encode ($data_flag);
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
