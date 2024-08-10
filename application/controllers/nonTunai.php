<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NonTunai extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('gold_login') != TRUE){
			redirect();
		}
		
		$this->load->model('M_bayar','mb');
	}
	
	public function index(){
		$data['view'] = '<div class="ui container fluid">
			<div class="ui grid"><div class="fifteen wide centered column right aligned" style="padding-bottom:0px"><button class="ui linkedin button btn-head" onclick=addForm("nonTunai")><i class="add icon"></i> Tambah Data</button></div></div>
			<div class="ui grid">
				<div class="fifteen wide centered column">
					<div class="ui grid">
						<div class="ui inverted dimmer" id="nonTunai-loader">
							<div class="ui loader"></div>
						</div>
						<div class="sixteen wide column" id="nonTunai-wrap">';
						
						$data_bayar = $this->mb->get_all_bayar_nontunai();
			
		$data['view'] .= '<table id="nonTunai-table" class="ui celled table" style="width:100%"><thead><tr><th style="width:30px">No</th><th>Metode Pembayaran</th><th>Account Number</th><th style="width:100px">Action</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_bayar as $d){
			$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.$d->description.'</td><td>'.$d->account_number.'</td><td style="padding: 0;text-align: center;"><button class="ui tiny icon google plus button" onclick=editForm("nonTunai","'.$d->account_number.'") title="Edit"><i class="edit icon"></i></button>';
			
			if($d->status == 'A'){
				$data['view'] .= '<button class="ui tiny icon positive button" onclick=changeStatus("nonTunai","'.$d->id.'","NA") title="Aktif"><i class="adjust icon"></i></button>';
			}else{
				$data['view'] .= '<button class="ui tiny icon negative button" onclick=changeStatus("nonTunai","'.$d->id.'","A") title="Tidak Aktif"><i class="adjust icon"></i></button>';
			}
			
			$data['view'] .= '</td></tr>';

			$number = $number + 1;
		}
		
		$data['view'] .= '</tbody></table>';
						
						$data['view'] .= '</div>
					</div>
				</div>
			</div>
		</div>';
		
		$data["date"] = 0;
		$data["success"] = true;
		echo json_encode($data);
	}
	
	public function get_all_pembayaran(){
		$this->db->trans_start();
		
		$data_bayar = $this->mb->get_all_bayar_nontunai();
			
		$data['view'] = '<table id="filter_data_tabel" class="ui celled table" style="width:100%"><thead><tr><th style="width:30px">No</th><th>Metode Pembayaran</th><th>Account Number</th><th style="width:60px">Action</th></tr></thead><tbody>';
		
		$number = 1;
		foreach($data_bayar as $d){
			$data['view'] .= '<tr><td class="right aligned">'.$number.'</td><td>'.$d->description.'</td><td>'.$d->account_number.'</td><td style="padding: 0;text-align: center;"><button class="ui tiny icon google plus button" onclick=editForm("'.$d->account_number.'") title="Edit"><i class="edit icon"></i></button>';
			
			if($d->status == 'A'){
				$data['view'] .= '<button class="ui tiny icon positive button" onclick=changeStatus("'.$d->id.'","NA") title="Aktif"><i class="adjust icon"></i></button>';
			}else{
				$data['view'] .= '<button class="ui tiny icon negative button" onclick=changeStatus("'.$d->id.'","A") title="Tidak Aktif"><i class="adjust icon"></i></button>';
			}
			
			$data['view'] .= '</td></tr>';

			$number = $number + 1;
		}
		
		$data['view'] .= '</tbody></table>';
		
		$this->db->trans_complete();
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function add(){
		$data['view'] = '<i class="close icon"></i><div class="header">Tambah Pembayaran Non Tunai</div><div class="content"><div class="ui error message" id="nonTunai-wraperror" style="display:none"></div>';
		
		$data['view'] .= '<form class="ui form form-javascript" id="nonTunai-addedit" action="'.base_url().'index.php/nonTunai/save_addedit" method="post"><div class="field"><input type="text" id="nonTunai-bank" name="nonTunai-bank" placeholder="Nama Metode Pembayaran" style="text-transform:uppercase" autocomplete="off" onkeyup=entToNextID("nonTunai-bb")></div><div class="field"><label>Saldo Awal</label><input type="text" id="nonTunai-bb" name="nonTunai-bb" placeholder="Saldo Awal Pembayaran" autocomplete="off" onkeyup=valueToCurrency("nonTunai","nonTunai-bb","noTotal") onkeydown=entToNextID("nonTunai-btnadd")></div></form></div><div class="actions"><button id="nonTunai-btnadd" class="ui green labeled icon button" onclick=saveAddEdit("nonTunai","Input")>Simpan	<i class="save icon"></i></button></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function save_addedit($flag){
		date_default_timezone_set("Asia/Jakarta");
		
		$this->db->trans_start();
		
		$this->validate($flag);
		
		$cara_bayar = $this->input->post('nonTunai-bank');
		$cara_bayar = strtoupper($cara_bayar);
		$saldo_awal = $this->input->post('nonTunai-bb');
		$saldo_awal = str_replace(',','',$saldo_awal);
		
		if($saldo_awal == '' || $saldo_awal == 'NaN'){
			$saldo_awal = 0;
		}
		
		$created_date = date("Y-m-d").' 00:00:00';
		$created_by = $this->session->userdata('gold_nama_user');
		
		if($flag == 'Input'){
			$account_number_int = $this->mb->generate_account_bank();
			$acc1 = substr($account_number_int, 0, 2);
			$acc2 = substr($account_number_int, -4);
			$account_number = $acc1.'-'.$acc2;
			$account_group = 1;
			$type = 'BA';
			$this->mb->insert_bayar_nontunai($cara_bayar,$account_number,$created_date,$created_by);
			$this->mb->insert_coa($account_number,$account_number_int,$cara_bayar,$account_group,$saldo_awal,$type,$created_by);
		}else if($flag == 'Update'){
			$id_bayar_form = $this->input->post('nonTunai-id');
			$this->mb->update_coa_bayar_nontunai($id_bayar_form,$cara_bayar,$saldo_awal);
			$this->mb->update_bayar_nontunai($id_bayar_form,$cara_bayar);
		}
		
		$this->db->trans_complete();
		
		if($flag == 'Input'){
			$pesan = 'Input';
		}else{
			$pesan = 'Update';
		}
		
		$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>'.$pesan.' Berhasil!</div></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	private function validate($flag){
		$data = array();
		$data['inputerror'] = '<ul class="list"></ul>';
		$data['success'] = TRUE;
		
		$cara_bayar = $this->input->post('nonTunai-bank');
		$cara_bayar = strtoupper($cara_bayar);
		
		if($cara_bayar == ''){
			$data['inputerror'] .= '<li>Metode Pembayaran Harus Diisi!</li>';
			$data['success'] = FALSE;
		}
		
		if($flag == 'Input'){
			$id = $this->mb->get_bayar_id_by_name($cara_bayar);
			if($id != 0){
				$data['inputerror'] .= '<li>Metode Pembayaran Sudah Ada!</li>';
				$data['success'] = FALSE;
			}
		}
		
		if($flag == 'Update'){
			$id_bayar_form = $this->input->post('nonTunai-id');
			$id_bayar = $this->mb->get_bayar_id_by_name($cara_bayar);
			if($id_bayar != 0 && $id_bayar != $id_bayar_form){
				$data['inputerror'] .= '<li>Metode Pembayaran Sudah Ada!</li>';
				$data['success'] = FALSE;
			}
		}
		
		if($data['success'] == FALSE){
			echo json_encode($data);
			exit();
		}
	}
	
	public function edit($id){
		$data_category = $this->mb->get_bayar_by_id($id);
		
		$data['view'] = '<i class="close icon"></i><div class="header">Edit Metode Pembayaran</div><div class="content"><div class="ui error message" id="nonTunai-wraperror" style="display:none"></div>';
		
		$data['view'] .= '<form class="ui form form-javascript" id="nonTunai-addedit" action="'.base_url().'index.php/nonTunai/save_addedit" method="post"><div class="field"><input type="hidden" name="nonTunai-id" value="'.$data_category[0]->accountnumber.'"><input type="text" id="nonTunai-bank" name="nonTunai-bank" placeholder="Nama Metode Pembayaran" style="text-transform:uppercase" autocomplete="off" onkeyup=entToNextID("nonTunai-bb") value="'.$data_category[0]->accountname.'"></div><div class="field"><label>Saldo Awal</label><input type="text" id="nonTunai-bb" name="nonTunai-bb" placeholder="Saldo Awal Pembayaran" autocomplete="off" onkeyup=valueToCurrency("nonTunai","nonTunai-bb","noTotal") onkeydown=entToNextID("nonTunai-btnadd") value="'.number_format($data_category[0]->beginningbalance, 0).'"></div></form></div></div><div class="actions"><button id="nonTunai-btnadd" class="ui green labeled icon button" onclick=saveAddEdit("nonTunai","Update")>Update	<i class="magic icon"></i></button></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
	
	public function change_status($id_bayar = 0,$status_bayar = 0){
		$this->db->trans_start();
		
		$this->mb->change_bayar_status($id_bayar,$status_bayar);
		
		$this->db->trans_complete();
		
		$data['message'] = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Update Berhasil!</div></div>';
		
		$data['success'] = true;
		echo json_encode($data);
	}
}
