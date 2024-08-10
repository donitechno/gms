<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_mutasi extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->db= $this->load->database($_COOKIE['cabang'],TRUE);
		$this->db_pusat = $this->load->database('default',TRUE);
	}

	public function find_all_goldsite(){
		$sql = "SELECT * FROM gold_site ORDER BY sitedesc ASC ";
		return $this->db->query($sql)->result();

	}

	public function find_all_goldsite_pusat(){
		
		$sql = "SELECT * FROM gold_site ORDER BY sitedesc ASC ";
		return $this->db_pusat->query($sql)->result();

	}
	
	public function get_site_code(){
		$sql = "SELECT * FROM gold_site_aktif
				";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$site_code = $row->id_site;
			return $site_code;
		}else{
			$site_code = 0;
			return 0;
		}
	}
	
	public function get_site_name(){
		$sql = "SELECT g.*, s.sitedesc 
				FROM gold_site_aktif g, gold_site s
				WHERE g.id_site = s.id
				";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			return $row->sitedesc;
		}else{
			return 'Undefined';
		}
	}
	
	public function get_default_account($initial){
		$sql = "SELECT * FROM gold_defaultaccount 
				WHERE initial='$initial'
				";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$accountnumber = $row->accountnumber;
			return $accountnumber;
		}else{
			$accountnumber = '00-0000';
			return $accountnumber;
		}
	}
	
	public function insert_mutasi_gram($sitecode,$idmutasi,$tipe,$karat,$fromaccount,$toaccount,$value,$desc,$transdate,$createdby){
		$sql = "INSERT INTO gold_mutasi_gr(idsite,idmutasi,tipemutasi,idkarat,fromaccount,toaccount,value,description,transdate,createdby,createddate) VALUES ('$sitecode','$idmutasi','$tipe',$karat,'$fromaccount','$toaccount','$value','$desc','$transdate','$createdby',CURRENT_TIMESTAMP())";
		
		$this->db->query($sql);
	}
	
	public function insert_mutasi_rupiah($sitecode,$idmutasi,$tipe,$fromaccount,$toaccount,$value,$desc,$transdate,$createdby){
		$sql = "INSERT INTO gold_mutasi_rp(idsite,idmutasi,tipemutasi,fromaccount,toaccount,value,description,transdate,createdby,createddate) VALUES ('$sitecode','$idmutasi','$tipe','$fromaccount','$toaccount','$value','$desc','$transdate','$createdby',CURRENT_TIMESTAMP())";
		
		$this->db->query($sql);
	}
	
	public function get_all_kasbank(){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE type IN('KK','BA','PIDP') AND status = 'A'
				ORDER BY accountnumberint";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_all_kasbank_pos(){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE type IN('KK','BA') AND status = 'A'
				ORDER BY accountnumberint";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_all_bank_pos(){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE type IN('BA') AND status = 'A'
				ORDER BY accountnumberint";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_single_coa_rp($jenis_bayar){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE accountnumber = '$jenis_bayar'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_rp_beginning_balance($id_coa){
		$sql = "SELECT beginningbalance FROM gold_coa_rp 
				WHERE accountnumber='$id_coa'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0)
		{
			$row = $query->row(); 

			$beginningbalance = $row->beginningbalance;
			return $beginningbalance;
		}else{
			$beginningbalance = 0;
			return $beginningbalance;
		}
	}
	
	public function get_report_mutasi_rp_yesterday_d($id_coa,$tgl_transaksi){
		$sql = "SELECT SUM(value) as total_mutasi
				FROM gold_mutasi_rp
				WHERE transdate < '$tgl_transaksi' AND toaccount = '$id_coa'
				GROUP BY toaccount";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_mutasi_rp_yesterday_k($id_coa,$tgl_transaksi){
		$sql = "SELECT SUM(value) as total_mutasi
				FROM gold_mutasi_rp
				WHERE transdate < '$tgl_transaksi' AND fromaccount = '$id_coa'
				GROUP BY fromaccount";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_mutasi_rp_lap($report_date_from,$report_date_to, $acc_number){
		$sql = "SELECT m.*, f.accountname AS from_acc_name, f.type AS type_from, t.accountname AS to_acc_name, t.type AS type_to
		FROM ((gold_mutasi_rp m
		INNER JOIN gold_coa_rp f on m.fromaccount = f.accountnumber)
		INNER JOIN gold_coa_rp t on m.toaccount = t.accountnumber)
		WHERE m.transdate BETWEEN '$report_date_from' AND '$report_date_to' AND (m.fromaccount = '$acc_number' OR m.toaccount = '$acc_number')
		ORDER BY m.idmutasi, m.transdate, m.createddate";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_coa_number_name($coa_int){
		$sql = "SELECT * FROM gold_coa_rp 
				WHERE accountnumberint = '$coa_int'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$account = $row->accountnumber.' | '.$row->accountname;
			return $account;
		}else{
			$account = 'NOT FOUND';
			return $account;
		}
	}
	
	public function get_coa_number_name_2($coa_int){
		$sql = "SELECT * FROM gold_coa_rp 
				WHERE accountnumber = '$coa_int'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$account = $row->accountname;
			return $account;
		}else{
			$account = 'NOT FOUND';
			return $account;
		}
	}
	
	public function get_coa_number_name_gr_2($coa_int){
		$sql = "SELECT * FROM gold_coa_gr
				WHERE accountnumber = '$coa_int'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$account = $row->accountname;
			return $account;
		}else{
			$account = 'NOT FOUND';
			return $account;
		}
	}
	
	public function get_report_mutasi_rp_jual($report_date_from,$report_date_to, $acc_number){
		$sql = "SELECT m.*, f.accountname AS from_acc_name, f.type AS type_from, t.accountname AS to_acc_name, t.type AS type_to
		FROM ((gold_mutasi_rp m
		INNER JOIN gold_coa_rp f on m.fromaccount = f.accountnumber)
		INNER JOIN gold_coa_rp t on m.toaccount = t.accountnumber)
		WHERE m.transdate BETWEEN '$report_date_from' and '$report_date_to' AND (m.idmutasi LIKE 'JR%' OR m.idmutasi LIKE 'JP%') AND (m.fromaccount = '$acc_number' OR m.toaccount = '$acc_number')
		ORDER BY m.transdate, m.createddate";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_mutasi_rp_bycode($code, $report_date_from,$report_date_to, $acc_number){
		$sql = "SELECT m.*, f.accountname AS from_acc_name, f.type AS type_from, t.accountname AS to_acc_name, t.type AS type_to
		FROM ((gold_mutasi_rp m
		INNER JOIN gold_coa_rp f on m.fromaccount = f.accountnumber)
		INNER JOIN gold_coa_rp t on m.toaccount = t.accountnumber)
		WHERE m.transdate BETWEEN '$report_date_from' and '$report_date_to' AND m.idmutasi LIKE '$code%' AND (m.fromaccount = '$acc_number' OR m.toaccount = '$acc_number')
		ORDER BY m.transdate, m.createddate";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_mutasi_rp_rekap_lap($report_date_from,$report_date_to, $acc_number){
		$sql = "SELECT m.*, f.accountname AS from_acc_name, f.type AS type_from, t.accountname AS to_acc_name, t.type AS type_to
		FROM ((gold_mutasi_rp m
		INNER JOIN gold_coa_rp f on m.fromaccount = f.accountnumber)
		INNER JOIN gold_coa_rp t on m.toaccount = t.accountnumber)
		WHERE m.transdate BETWEEN '$report_date_from' and '$report_date_to' AND (m.idmutasi NOT LIKE 'BR%' AND m.idmutasi NOT LIKE 'JR%' AND m.idmutasi NOT LIKE 'JP%') AND (m.fromaccount = '$acc_number' OR m.toaccount = '$acc_number')
		ORDER BY m.transdate, m.createddate";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function update_mutasi_gram($idmutasi,$value){
		$sql = "UPDATE gold_mutasi_gr
				SET value='$value'
				WHERE idmutasi='$idmutasi'";
		
		$this->db->query($sql);
	}
	
	public function get_repgros_account(){
		$sql = "SELECT * FROM gold_coa_gr
				WHERE type IN('SDR','SDG','TDG') AND status = 'A' AND idkarat = '1'
				ORDER BY accountnumberint";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_code_gr($kode_awal){
		$sql = "SELECT * FROM gold_mutasi_gr
				WHERE idmutasi LIKE '$kode_awal%'
				ORDER BY idmutasi DESC
				LIMIT 1";
		
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 
		
			$idmutasi = $row->idmutasi;
			$urutArray = explode('-', $idmutasi);
			$urut1 = $urutArray[2];
			$urut1 = (int)$urut1;
			//$urut1 = $urut1 + 1;
		}else{
			$urut1 = 0;
		}
		
		$sql2 = "SELECT * FROM gold_mutasi_gr_hapus
				WHERE idmutasi LIKE '$kode_awal%'
				ORDER BY idmutasi DESC
				LIMIT 1";
		
		$query2 = $this->db->query($sql2);
		if($query2->num_rows() > 0){
			$row2 = $query2->row(); 
		
			$idmutasi2 = $row2->idmutasi;
			$urutArray2 = explode('-', $idmutasi2);
			$urut2 = $urutArray2[2];
			$urut2 = (int)$urut2;
		}else{
			$urut2 = 0;
		}
		
		if($urut2 == $urut1){
			return $urut1 + 1;
		}else if($urut1 > $urut2){
			return $urut1 + 1;
		}else if($urut2 > $urut1){
			return $urut2 + 1;
		}
	}
	
	public function insert_transaksi_cabang($transactioncode,$dept,$tipe,$keterangan,$id_karat,$real_gram,$konv_gram,$persentase,$tgl_transaksi,$created_by){
		$sql = "INSERT INTO gold_detail_trans_cabang(transaction_code,cabang,tipe,description,id_karat,berat_real,berat_konversi,persentase,trans_date,created_by,created_date) VALUES ('$transactioncode','$dept','$tipe','$keterangan',$id_karat,'$real_gram','$konv_gram','$persentase','$tgl_transaksi','$created_by',CURRENT_TIMESTAMP())";
		
		$this->db->query($sql);
	}
	
	public function get_filter_tac($from_date,$to_date,$filter_cabang,$filter_jenis,$filter_karat){
		$sql = "SELECT m.*, k.karat_name
		FROM gold_detail_trans_cabang m, gold_karat k
		WHERE m.id_karat = k.id AND m.trans_date BETWEEN '$from_date' and '$to_date' AND m.cabang IN($filter_cabang) AND m.tipe IN($filter_jenis) AND m.id_karat IN($filter_karat)
		ORDER BY m.trans_date, m.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_filter_rep($from_date,$to_date,$filter_jenis){
		$sql = "SELECT * FROM gold_mutasi_reparasi
		WHERE trans_date BETWEEN '$from_date' and '$to_date' AND tipe IN($filter_jenis) AND from_buy = 'N'
		ORDER BY trans_date, created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_filter_gros($from_date,$to_date,$filter_jenis){
		$sql = "SELECT * FROM gold_mutasi_pengadaan
		WHERE trans_date BETWEEN '$from_date' and '$to_date' AND tipe IN($filter_jenis) AND from_buy = 'N'
		ORDER BY trans_date, created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_filter_tip_gros($from_date,$to_date,$filter_jenis){
		$sql = "SELECT * FROM gold_mutasi_titip_pengadaan
		WHERE trans_date BETWEEN '$from_date' and '$to_date' AND tipe IN($filter_jenis) AND from_buy = 'N'
		ORDER BY trans_date, created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_detail_tac($id_trans,$id_dept){
		$sql = "SELECT m.*, k.karat_name FROM gold_detail_trans_cabang m, gold_karat k
		WHERE m.id_karat = k.id AND m.transaction_code = '$id_trans'
		ORDER BY m.trans_date, m.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_pengadaan_byid($id_trans){
		$sql = "SELECT * FROM gold_mutasi_pengadaan
		WHERE id_pengiriman = '$id_trans'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_titip_pengadaan_byid($id_trans){
		$sql = "SELECT * FROM gold_mutasi_titip_pengadaan
		WHERE id_pengiriman = '$id_trans'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_reparasi_byid($id_trans){
		$sql = "SELECT * FROM gold_mutasi_reparasi
		WHERE id_pengiriman = '$id_trans'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function delete_trans_cabang($id_trans){
		$sql = "DELETE FROM gold_detail_trans_cabang
		WHERE transaction_code = '$id_trans'";
		
		$this->db->query($sql);
	}
	
	public function get_account_umum(){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE type IN('KK','BA','PIDP','MKI','PL','BY','PI','HU','PST') AND status = 'A'
				ORDER BY accountnumberint";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_account_jurnal_umum(){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE type IN('KK','BA','PIDP','MKI','PL','BY','PI','PIK','UMP','TTP','HU','PST') AND status = 'A'
				ORDER BY accountnumberint";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_account_karyawan(){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE type IN('PIK') AND status = 'A'
				ORDER BY accountnumberint";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_account_kas(){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE type IN('KK') AND status = 'A'
				ORDER BY accountnumberint";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_account_lain(){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE type IN('PIDP','MKI','PL','BY','KK','BA','UMP','PI','HU') AND status = 'A'
				ORDER BY accountnumberint";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_code_rp($kode_awal){
		$sql = "SELECT * FROM gold_mutasi_rp
				WHERE idmutasi LIKE '$kode_awal%'
				ORDER BY idmutasi DESC
				LIMIT 1";
		
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 
		
			$idmutasi = $row->idmutasi;
			$urutArray = explode('-', $idmutasi);
			$urut1 = $urutArray[2];
			$urut1 = (int)$urut1;
			//$urut1 = $urut1 + 1;
		}else{
			$urut1 = 0;
		}
		
		$sql2 = "SELECT * FROM gold_mutasi_rp_hapus
				WHERE idmutasi LIKE '$kode_awal%'
				ORDER BY idmutasi DESC
				LIMIT 1";
		
		$query2 = $this->db->query($sql2);
		if($query2->num_rows() > 0){
			$row2 = $query2->row(); 
		
			$idmutasi2 = $row2->idmutasi;
			$urutArray2 = explode('-', $idmutasi2);
			$urut2 = $urutArray2[2];
			$urut2 = (int)$urut2;
		}else{
			$urut2 = 0;
		}
		
		if($urut2 == $urut1){
			return $urut1 + 1;
		}else if($urut1 > $urut2){
			return $urut1 + 1;
		}else if($urut2 > $urut1){
			return $urut2 + 1;
		}
	}
	
	public function get_filter_mkb($from_date,$to_date,$filter_sql){
		$sql = "SELECT m.*, f.accountname AS from_acc_name, t.accountname AS to_acc_name
		FROM ((gold_mutasi_rp m
		INNER JOIN gold_coa_rp f on m.fromaccount = f.accountnumber)
		INNER JOIN gold_coa_rp t on m.toaccount = t.accountnumber)
		WHERE m.transdate BETWEEN '$from_date' and '$to_date' AND ($filter_sql)
		ORDER BY m.transdate, m.createddate";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_rp_by_id($id){
		$sql = "SELECT * FROM gold_mutasi_rp
				WHERE idmutasi = '$id'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_mutasi_rupiah_deleted($sitecode,$idmutasi,$tipe,$fromaccount,$toaccount,$value,$desc,$transdate,$createddate,$createdby,$deletedby){
		$sql = "INSERT INTO gold_mutasi_rp_hapus(idsite,idmutasi,tipemutasi,fromaccount,toaccount,value,description,transdate,createddate,createdby,deleteddate,deletedby) VALUES ('$sitecode','$idmutasi','$tipe','$fromaccount','$toaccount','$value','$desc','$transdate','$createddate','$createdby',CURRENT_TIMESTAMP(),'$deletedby')";
		
		$this->db->query($sql);
	}
	
	public function delete_mutasi_rupiah($idmutasi){
		$sql = "DELETE FROM gold_mutasi_rp
				WHERE idmutasi = '$idmutasi'";
		
		$this->db->query($sql);
	}
	
	public function get_coa_mas_header(){
		$sql = "SELECT * FROM gold_coa_gr
				WHERE accountnumberint LIKE '17%' AND status = 'A' AND type = 'SRT'
				ORDER BY accountnumberint";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_coa_mas_content(){
		$sql = "SELECT * FROM gold_coa_gr
				WHERE status = 'A' AND type NOT IN('SRT','SDR','SDG','PJG','JL','BL','TTP','TDG')
				ORDER BY accountnumberint";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_filter_mutasi_mas($from_date,$to_date,$filter_jenis,$filter_karat){
		$sql = "SELECT m.*, f.accountname AS from_acc_name, t.accountname AS to_acc_name, k.karat_name
		FROM ((gold_karat k, gold_mutasi_gr m
		INNER JOIN gold_coa_gr f on m.fromaccount = f.accountnumber)
		INNER JOIN gold_coa_gr t on m.toaccount = t.accountnumber)
		WHERE m.idkarat = k.id AND m.transdate BETWEEN '$from_date' and '$to_date' AND m.tipemutasi IN($filter_jenis) AND m.idkarat IN($filter_karat) AND ( m.idmutasi LIKE 'MI%' OR m.idmutasi LIKE 'MO%')
		ORDER BY m.transdate, m.createddate";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_gr_by_id($id){
		$sql = "SELECT * FROM gold_mutasi_gr
				WHERE idmutasi = '$id'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_gr_like_id($id){
		$sql = "SELECT * FROM gold_mutasi_gr
				WHERE idmutasi LIKE '$id%'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function insert_mutasi_gram_deleted($sitecode,$idmutasi,$tipe,$idkarat,$fromaccount,$toaccount,$value,$desc,$transdate,$createddate,$createdby,$deletedby){
		$sql = "INSERT INTO gold_mutasi_gr_hapus(idsite,idmutasi,tipemutasi,idkarat,fromaccount,toaccount,value,description,transdate,createddate,createdby,deleteddate,deletedby) VALUES ('$sitecode','$idmutasi','$tipe','$idkarat','$fromaccount','$toaccount','$value','$desc','$transdate','$createddate','$createdby',CURRENT_TIMESTAMP(),'$deletedby')";
		
		$this->db->query($sql);
	}
	
	public function delete_mutasi_gram($idmutasi){
		$sql = "DELETE FROM gold_mutasi_gr
				WHERE idmutasi = '$idmutasi'";
		
		$this->db->query($sql);
	}
	
	public function get_coa_titip_rp(){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE type = 'TTP' AND status = 'A'
				ORDER BY accountname";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_all_titipan_rp(){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE status = 'A' AND type = 'TTP'
				ORDER BY accountname";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_mutasi_transaksi_yesterday_d_rp($id_coa,$yesterday_date){
		$sql = "SELECT SUM(value) as total_mutasi
				FROM gold_mutasi_rp
				WHERE transdate < '$yesterday_date' AND toaccount = '$id_coa'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_mutasi_transaksi_yesterday_k_rp($id_coa,$yesterday_date){
		$sql = "SELECT SUM(value) as total_mutasi
				FROM gold_mutasi_rp
				WHERE transdate < '$yesterday_date' AND fromaccount = '$id_coa'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_in_rp($id_coa,$report_date_from,$report_date_to){
		$sql = "SELECT SUM(value) as total_mutasi
				FROM gold_mutasi_rp
				WHERE transdate BETWEEN '$report_date_from' AND '$report_date_to' AND toaccount = '$id_coa'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_out_rp($id_coa,$report_date_from,$report_date_to){
		$sql = "SELECT SUM(value) as total_mutasi
				FROM gold_mutasi_rp
				WHERE transdate BETWEEN '$report_date_from' AND '$report_date_to' AND fromaccount = '$id_coa'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_mutasi_rp($report_date_from,$report_date_to, $acc_number){
		$sql = "SELECT m.*, f.accountname AS from_acc_name, f.type AS type_from, t.accountname AS to_acc_name, t.type AS type_to
		FROM ((gold_mutasi_rp m
		INNER JOIN gold_coa_rp f on m.fromaccount = f.accountnumber)
		INNER JOIN gold_coa_rp t on m.toaccount = t.accountnumber)
		WHERE m.transdate BETWEEN '$report_date_from' AND '$report_date_to' AND (m.fromaccount = '$acc_number' OR m.toaccount = '$acc_number')
		ORDER BY m.transdate, m.createddate";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_accountname_by_accountint_rp($accountnumber){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE accountnumberint = '$accountnumber'
				LIMIT 1";

		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 

			return $row->accountname;
		}else{
			return 'UNDEFINED';
		}
	}
	
	public function cek_nama_account_titipan_rp($accountname){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE accountname = '$accountname'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function generate_account_titipan_rp(){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE type = 'TTP'
				ORDER BY accountnumberint DESC
				LIMIT 1";
		
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 

			return $row->accountnumberint + 1;
		}else{
			$id = 230001;
			return $id;
		}
	}
	
	public function insert_coa_rp($account_number,$account_number_int,$account_name,$account_group,$beginning_balance,$type,$created_by){
		$sql = "INSERT INTO gold_coa_rp(accountnumber,accountnumberint,accountname,accountgroup,beginningbalance,status,type,created_date,created_by) VALUES ('$account_number','$account_number_int','$account_name','$account_group','$beginning_balance','A','$type',CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function get_all_titipan_gr(){
		$sql = "SELECT * FROM gold_coa_gr
				WHERE status = 'A' AND type = 'TTP'
				ORDER BY accountname";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_single_coa_gr($id){
		$sql = "SELECT * FROM gold_coa_gr
				WHERE accountnumber = '$id'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_mutasi_transaksi_yesterday_d_bykarat($id_coa,$yesterday_date,$id_kurs){
		$sql = "SELECT SUM(value) as total_mutasi, idkarat
				FROM gold_mutasi_gr
				WHERE idkarat = $id_kurs AND toaccount = '$id_coa' AND transdate < '$yesterday_date' 
				GROUP BY idkarat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_mutasi_transaksi_yesterday_k_bykarat($id_coa,$yesterday_date,$id_kurs){
		$sql = "SELECT SUM(value) as total_mutasi, idkarat
				FROM gold_mutasi_gr
				WHERE idkarat = $id_kurs AND fromaccount = '$id_coa' AND transdate < '$yesterday_date' 
				GROUP BY idkarat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_in_gr($id_coa,$report_date_from,$report_date_to){
		$sql = "SELECT SUM(value) as total_mutasi, idkarat
				FROM gold_mutasi_gr
				WHERE transdate BETWEEN '$report_date_from' AND '$report_date_to' AND toaccount = '$id_coa'
				GROUP BY idkarat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_out_gr($id_coa,$report_date_from,$report_date_to){
		$sql = "SELECT SUM(value) as total_mutasi, idkarat
				FROM gold_mutasi_gr
				WHERE transdate BETWEEN '$report_date_from' AND '$report_date_to' AND fromaccount = '$id_coa'
				GROUP BY idkarat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_coa_titip_gr(){
		$sql = "SELECT * FROM gold_coa_gr
				WHERE type = 'TTP' AND status = 'A'
				ORDER BY accountname";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_mutasi_gr_by_karat($id_karat,$report_date_from,$report_date_to,$acc_number){
		$sql = "SELECT m.*, c.karat_name, f.accountname AS from_acc_name, f.type AS type_from, t.accountname AS to_acc_name, t.type AS type_to
		FROM ((gold_karat c, gold_mutasi_gr m
		INNER JOIN gold_coa_gr f on m.fromaccount = f.accountnumber)
		INNER JOIN gold_coa_gr t on m.toaccount = t.accountnumber)
		WHERE m.idkarat = c.id AND m.transdate BETWEEN '$report_date_from' and '$report_date_to' AND m.idkarat = '$id_karat' AND (m.fromaccount = '$acc_number' OR m.toaccount = '$acc_number')
		ORDER BY m.transdate, m.createddate";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_accountname_by_accountint($accountnumber){
		$sql = "SELECT * FROM gold_coa_gr
				WHERE accountnumberint = '$accountnumber'
				LIMIT 1";

		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 

			return $row->accountname;
		}else{
			return 'UNDEFINED';
		}
	}
	
	public function generate_account_titipan_gr(){
		$sql = "SELECT * FROM gold_coa_gr
				WHERE type = 'TTP'
				ORDER BY accountnumberint DESC
				LIMIT 1";
		
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 

			return $row->accountnumberint + 1;
		}else{
			$id = 220001;
			return $id;
		}
	}
	
	public function cek_nama_account_titipan_gr($accountname){
		$sql = "SELECT * FROM gold_coa_gr
				WHERE accountname = '$accountname'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_gr_beginning_balance($id_coa){
		$sql = "SELECT beginningbalance FROM gold_coa_gr
				WHERE accountnumber='$id_coa'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0)
		{
			$row = $query->row(); 

			$beginningbalance = $row->beginningbalance;
			return $beginningbalance;
		}else{
			$beginningbalance = 0;
			return $beginningbalance;
		}
	}
	
	public function get_report_gr_beginning_balance_2($id_karat, $type){
		$sql = "SELECT beginningbalance FROM gold_coa_gr 
				WHERE type='$type' AND idkarat = '$id_karat'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0)
		{
			$row = $query->row(); 

			$beginningbalance = $row->beginningbalance;
			return $beginningbalance;
		}else{
			$beginningbalance = 0;
			return $beginningbalance;
		}
	}
	
	public function insert_coa_gr($account_number,$account_number_int,$account_name,$account_group,$beginning_balance,$type,$idkarat,$created_by){
		$sql = "INSERT INTO gold_coa_gr(accountnumber,accountnumberint,accountname,accountgroup,beginningbalance,status,type,idkarat,created_date,created_by) VALUES ('$account_number','$account_number_int','$account_name','$account_group','$beginning_balance','A','$type','$idkarat',CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function get_mas_coa(){
		$sql = "SELECT * FROM gold_coa_gr
				WHERE accountnumberint LIKE '17%' AND status = 'A'
				ORDER BY accountnumberint";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_coa_gr_by_range($coa_from, $coa_to){
		$sql = "SELECT * FROM gold_coa_gr
				WHERE accountnumberint BETWEEN $coa_from AND $coa_to
				ORDER BY accountnumberint";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_mutasi_repgros_d($tabel_name,$id_coa,$yesterday_date){
		$sql = "SELECT SUM(total_konv) as total_mutasi
				FROM $tabel_name
				WHERE trans_date < '$yesterday_date' AND toaccount = '$id_coa'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_mutasi_repgros_k($tabel_name,$id_coa,$yesterday_date){
		$sql = "SELECT SUM(total_konv) as total_mutasi
				FROM $tabel_name
				WHERE trans_date < '$yesterday_date' AND fromaccount = '$id_coa'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_repgros_in($tabel_name,$id_coa,$report_date_from,$report_date_to){
		$sql = "SELECT SUM(total_konv) as total_mutasi
				FROM $tabel_name
				WHERE trans_date BETWEEN '$report_date_from' AND '$report_date_to' AND toaccount = '$id_coa' AND total_konv != 0";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_repgros_out($tabel_name,$id_coa,$report_date_from,$report_date_to){
		$sql = "SELECT SUM(total_konv) as total_mutasi
				FROM $tabel_name
				WHERE trans_date BETWEEN '$report_date_from' AND '$report_date_to' AND fromaccount = '$id_coa' AND total_konv != 0";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_mutasi_gr_yesterday_d_bykurs($id_coa,$yesterday_date,$id_kurs){
		$sql = "SELECT SUM(value) as total_mutasi, idkarat
				FROM gold_mutasi_gr
				WHERE idkarat = $id_kurs AND toaccount = '$id_coa' AND transdate < '$yesterday_date'
				GROUP BY idkarat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_mutasi_gr_yesterday_k_bykurs($id_coa,$yesterday_date,$id_kurs){
		$sql = "SELECT SUM(value) as total_mutasi, idkarat
				FROM gold_mutasi_gr
				WHERE idkarat = $id_kurs AND fromaccount = '$id_coa' AND transdate < '$yesterday_date' 
				GROUP BY idkarat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_gr_in($id_coa,$report_date_from,$report_date_to){
		$sql = "SELECT SUM(value) as total_mutasi, idkarat
				FROM gold_mutasi_gr
				WHERE transdate BETWEEN '$report_date_from' AND '$report_date_to' AND toaccount = '$id_coa' AND value != 0
				GROUP BY idkarat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_gr_out($id_coa,$report_date_from,$report_date_to){
		$sql = "SELECT SUM(value) as total_mutasi, idkarat
				FROM gold_mutasi_gr
				WHERE transdate BETWEEN '$report_date_from' AND '$report_date_to' AND fromaccount = '$id_coa' AND value != 0
				GROUP BY idkarat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_mutasi_gr_by_kurs($id_kurs,$report_date_from,$report_date_to){
		$sql = "SELECT m.*, c.karat_name, f.accountname AS from_acc_name, f.type AS type_from, t.accountname AS to_acc_name, t.type AS type_to
		FROM ((gold_karat c, gold_mutasi_gr m
		INNER JOIN gold_coa_gr f on m.fromaccount = f.accountnumber)
		INNER JOIN gold_coa_gr t on m.toaccount = t.accountnumber)
		WHERE m.idkarat = c.id AND m.transdate BETWEEN '$report_date_from' and '$report_date_to' AND m.idkarat = '$id_kurs' AND m.value != 0
		ORDER BY m.transdate, m.createddate";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_report_mutasi_repgros($tabel_name,$report_date_from,$report_date_to){
		$sql = "SELECT m.*, f.accountname AS from_acc_name, f.type AS type_from, t.accountname AS to_acc_name, t.type AS type_to
		FROM (($tabel_name m
		INNER JOIN gold_coa_gr f on m.fromaccount = f.accountnumber)
		INNER JOIN gold_coa_gr t on m.toaccount = t.accountnumber)
		WHERE m.trans_date BETWEEN '$report_date_from' and '$report_date_to' AND m.total_konv != 0
		ORDER BY m.trans_date, m.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_rekap_lap_rep($coa_from,$coa_to,$from_date,$to_date){
		$sql = "SELECT SUM(m.value) as total, m.idkarat, k.karat_name
		FROM (gold_mutasi_gr m
		INNER JOIN gold_karat k on m.idkarat = k.id)
		WHERE m.fromaccount = '$coa_from' AND m.toaccount = '$coa_to' AND m.transdate BETWEEN '$from_date' and '$to_date'
		GROUP BY m.idkarat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_rekap_lap_gros($coa_from,$coa_to,$coa_from2,$coa_to2,$from_date,$to_date){
		$sql = "SELECT SUM(m.value) as total, m.idkarat, k.karat_name
		FROM (gold_mutasi_gr m
		INNER JOIN gold_karat k on m.idkarat = k.id)
		WHERE ((m.fromaccount = '$coa_from' AND m.toaccount = '$coa_to') OR (m.fromaccount = '$coa_from2' AND m.toaccount = '$coa_to2')) AND m.transdate BETWEEN '$from_date' AND '$to_date'
		GROUP BY m.idkarat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_rekap_in_lap_rep($coa_to,$from_date,$to_date){
		$sql = "SELECT SUM(m.value) as total, m.idkarat, k.karat_name
		FROM (gold_mutasi_gr m
		INNER JOIN gold_karat k on m.idkarat = k.id)
		WHERE m.transdate BETWEEN '$from_date' and '$to_date' AND m.toaccount = '$coa_to' AND (fromaccount IN('31-0001','41-0004','41-0005') OR fromaccount LIKE '22-%')
		GROUP BY m.idkarat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_rekap_in_reparasi($from_date,$to_date){
		$sql = "SELECT SUM(total_konv) as total
		FROM gold_mutasi_reparasi
		WHERE trans_date BETWEEN '$from_date' and '$to_date' AND toaccount = '17-0002'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_rekap_in_pengadaan($from_date,$to_date){
		$sql = "SELECT SUM(total_konv) as total
		FROM gold_mutasi_pengadaan
		WHERE trans_date BETWEEN '$from_date' and '$to_date' AND toaccount = '17-0003'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_rekap_in_titip_pengadaan($from_date,$to_date){
		$sql = "SELECT SUM(total_konv) as total
		FROM gold_mutasi_titip_pengadaan
		WHERE trans_date BETWEEN '$from_date' and '$to_date' AND toaccount = '17-0005'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_rekap_in_titipan($from_date,$to_date){
		$sql = "SELECT SUM(value) as total
		FROM gold_mutasi_gr
		WHERE transdate BETWEEN '$from_date' and '$to_date' AND fromaccount LIKE '22-%'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_rekap_out_lap_rep($coa_from,$from_date,$to_date){
		$sql = "SELECT SUM(m.value) as total, m.idkarat, k.karat_name
		FROM (gold_mutasi_gr m
		INNER JOIN gold_karat k on m.idkarat = k.id)
		WHERE m.transdate BETWEEN '$from_date' and '$to_date' AND m.fromaccount = '$coa_from' AND (toaccount IN('31-0001','41-0004','41-0005') OR toaccount LIKE '22-%')
		GROUP BY m.idkarat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_rekap_out_reparasi($from_date,$to_date){
		$sql = "SELECT SUM(total_konv) as total
		FROM gold_mutasi_reparasi
		WHERE trans_date BETWEEN '$from_date' and '$to_date' AND fromaccount = '17-0002'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_rekap_out_pengadaan($from_date,$to_date){
		$sql = "SELECT SUM(total_konv) as total
		FROM gold_mutasi_pengadaan
		WHERE trans_date BETWEEN '$from_date' and '$to_date' AND fromaccount = '17-0003'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_rekap_out_titip_pengadaan($from_date,$to_date){
		$sql = "SELECT SUM(total_konv) as total
		FROM gold_mutasi_titip_pengadaan
		WHERE trans_date BETWEEN '$from_date' and '$to_date' AND fromaccount = '17-0005'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_rekap_out_titipan($from_date,$to_date){
		$sql = "SELECT SUM(value) as total
		FROM gold_mutasi_gr
		WHERE transdate BETWEEN '$from_date' and '$to_date' AND toaccount LIKE '22-%'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_coa_rp_by_range($coa_from, $coa_to){
		$sql = "SELECT * FROM gold_coa_rp
				WHERE accountnumberint BETWEEN '$coa_from' AND '$coa_to'
				ORDER BY accountnumberint";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_rp_like_id($id){
		$sql = "SELECT * FROM gold_mutasi_rp
				WHERE idmutasi LIKE '$id%'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function delete_mutasi_rupiah_like($idmutasi){
		$sql = "DELETE FROM gold_mutasi_rp
				WHERE idmutasi LIKE '$idmutasi%'";
		
		$this->db->query($sql);
	}
	
	public function delete_mutasi_rupiah_like2($idmutasi){
		$sql = "DELETE FROM gold_mutasi_rp
				WHERE idmutasi LIKE '%$idmutasi'";
		
		$this->db->query($sql);
	}
	
	public function delete_mutasi_gram_like($idmutasi){
		$sql = "DELETE FROM gold_mutasi_gr
				WHERE idmutasi LIKE '$idmutasi%'";
		
		$this->db->query($sql);
	}
	
	public function get_periode_aktif($tanggal_aktif){
		$sql = "SELECT * FROM gold_periode
				WHERE from_date <= '$tanggal_aktif' AND to_date >= '$tanggal_aktif'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_setor_biaya($fromaccount,$toaccount,$from_date,$to_date){
		$sql = "SELECT SUM(value) as total FROM gold_mutasi_rp
				WHERE fromaccount = '$fromaccount' AND toaccount = '$toaccount' AND transdate BETWEEN '$from_date' AND '$to_date'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_setor_biaya_gr($fromaccount,$toaccount,$idkarat,$from_date,$to_date){
		$sql = "SELECT SUM(m.value) as total, k.kali_laporan 
				FROM gold_mutasi_gr m, gold_karat k
				WHERE m.idkarat = k.id AND m.fromaccount = '$fromaccount' AND m.toaccount = '$toaccount' AND m.idkarat = '$idkarat' AND m.transdate BETWEEN '$from_date' AND '$to_date'
				GROUP BY idkarat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function generate_id_titipan_gr($account_awalan){
		$sql = "SELECT * FROM gold_titipan_gr
				WHERE id LIKE '$account_awalan%'
				ORDER BY id DESC
				LIMIT 1";
		
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 
			$id_lengkap = $row->id;
			$id_angka = str_replace($account_awalan,'',$id_lengkap);
			$id_angka = intval($id_angka);
			
			$id_pelanggan = $account_awalan;
			
			$id_angka = $id_angka + 1;
			$totalnumberlength = 4;
			$numberlength = strlen($id_angka);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$id_pelanggan .= '0';
				}
			}
			
			$id_pelanggan .= $id_angka;
			
			return $id_pelanggan;
		}else{
			return $account_awalan.'0001';
		}
	}
	
	public function insert_coa_titipan_gr($id_pelanggan,$account_name,$created_by){
		$sql = "INSERT INTO gold_titipan_gr(id,nama_pelanggan,created_date,created_by) VALUES ('$id_pelanggan','$account_name',CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function generate_id_titipan_rp($account_awalan){
		$sql = "SELECT * FROM gold_titipan_rp
				WHERE id LIKE '$account_awalan%'
				ORDER BY id DESC
				LIMIT 1";
		
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row(); 
			$id_lengkap = $row->id;
			$id_angka = str_replace($account_awalan,'',$id_lengkap);
			$id_angka = intval($id_angka);
			
			$id_pelanggan = $account_awalan;
			
			$id_angka = $id_angka + 1;
			$totalnumberlength = 4;
			$numberlength = strlen($id_angka);
			$numberspace = $totalnumberlength - $numberlength;
			if($numberspace != 0){
				for ($i = 1; $i <= $numberspace; $i++){
					$id_pelanggan .= '0';
				}
			}
			
			$id_pelanggan .= $id_angka;
			
			return $id_pelanggan;
		}else{
			return $account_awalan.'0001';
		}
	}
	
	public function insert_coa_titipan_rp($id_pelanggan,$account_name,$created_by){
		$sql = "INSERT INTO gold_titipan_rp(id,nama_pelanggan,created_date,created_by) VALUES ('$id_pelanggan','$account_name',CURRENT_TIMESTAMP(),'$created_by')";
		
		$this->db->query($sql);
	}
	
	public function get_printer($id){
		$sql = "SELECT * FROM gold_kasir WHERE id = '$id'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_all_kasir(){
		$sql = "SELECT * FROM gold_kasir";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function generate_pesanan_code($idtrans){
		$sql = "SELECT * FROM gold_mutasi_rp WHERE idmutasi LIKE '$idtrans%'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function update_ump_pesanan($id_pesanan,$ump_main){
		$sql = "UPDATE gold_main_pesanan
				SET ump_val = '$ump_main', last_updated = CURRENT_TIMESTAMP()
				WHERE id_pesanan = '$id_pesanan'";
		
		$this->db->query($sql);
	}
	
	public function get_total_jual_bank($account, $tanggal){
		$sql = "SELECT SUM(bayar_2) as total 
				FROM gold_main_penjualan WHERE jenis_bayar_2 = '$account' AND trans_date = '$tanggal'";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row(); 

			$value = $row->total;
			return $value;
		}else{
			$value = 0;
			return $value;
		}
	}
	
	public function update_pesan_batal_pesanan($keterangan){
		$keterangan_update = $keterangan.' (BATAL)';
		
		$sql = "UPDATE gold_mutasi_rp
				SET description='$keterangan_update'
				WHERE description='$keterangan'";
		
		$this->db->query($sql);
	}
	
	public function get_best_customer($from_date, $to_date){
		$sql = "SELECT cust_service, COUNT(cust_service) as total
				FROM `gold_main_penjualan`
				WHERE transaction_code LIKE 'JR%' AND status = 'A' AND trans_date BETWEEN '$from_date' AND '$to_date'
				GROUP BY cust_service
				ORDER BY COUNT(cust_service) DESC";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_count_jual_cs($from_date, $to_date){
		$sql = "SELECT transaction_code, cust_service
				FROM `gold_main_penjualan`
				WHERE transaction_code LIKE 'JR%' AND status = 'A' AND trans_date BETWEEN '$from_date' AND '$to_date'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_count_jual_detail_cs($from_date, $to_date){
		$sql = "SELECT transaction_code, COUNT(transaction_code) AS total
				FROM `gold_detail_penjualan`
				WHERE transaction_code LIKE 'JR%' AND status = 'A' AND trans_date BETWEEN '$from_date' AND '$to_date'
				GROUP BY transaction_code
				ORDER BY transaction_code";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function cek_status_berlian(){
		$sql = "SELECT sdg FROM gold_karat WHERE id = 6";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$row = $query->row();

			$value = $row->sdg;
			return $value;
		}else{
			$value = 'N';
			return $value;
		}
	}
	
	public function change_berlian_status($berlian){
		$sql = "UPDATE gold_karat
				SET sdg='$berlian'
				WHERE id=6";
		
		$this->db->query($sql);
	}
	
	public function get_tanggal_aktif(){
		$sql = "SELECT * FROM gold_tanggal_aktif";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_all_coa_gr(){
		$sql = "SELECT * FROM gold_coa_gr";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_all_coa_rp(){
		$sql = "SELECT * FROM gold_coa_rp";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_all_titipan_gr2(){
		$sql = "SELECT * FROM gold_titipan_gr";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_all_titipan_rp2(){
		$sql = "SELECT * FROM gold_titipan_gr";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_all_user(){
		$sql = "SELECT * FROM gold_user";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_dailyopen($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_dailyopen
				WHERE created_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_main_pembelian($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_main_pembelian
				WHERE created_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_detail_pembelian($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_detail_pembelian
				WHERE created_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_main_penjualan($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_main_penjualan
				WHERE created_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_detail_penjualan($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_detail_penjualan
				WHERE created_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_main_pesanan($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_main_pesanan
				WHERE last_updated BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_detail_pesanan($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_detail_pesanan
				WHERE last_updated BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_master_product($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_master_product_name
				WHERE created_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_gr($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_mutasi_gr
				WHERE createddate BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_gr_hapus($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_mutasi_gr_hapus
				WHERE deleteddate BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_rp($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_mutasi_rp
				WHERE createddate BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_rp_hapus($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_mutasi_rp_hapus
				WHERE deleteddate BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_pengadaan($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_mutasi_pengadaan
				WHERE created_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_pengadaan_hapus($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_mutasi_pengadaan_hapus
				WHERE deleted_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_reparasi($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_mutasi_reparasi
				WHERE created_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_reparasi_hapus($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_mutasi_reparasi_hapus
				WHERE deleted_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_titip_pgd($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_mutasi_titip_pengadaan
				WHERE created_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_mutasi_titip_pgd_hapus($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_mutasi_titip_pengadaan_hapus
				WHERE deleted_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_pindah_box($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_pindah_box
				WHERE created_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_stock_in($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_stock_in
				WHERE created_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_stock_out($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_stock_out
				WHERE created_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_data_product($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_product
				WHERE last_updated BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_periode($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_periode";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_detail_trans_cabang($tgl_from,$tgl_to){
		$sql = "SELECT * FROM gold_detail_trans_cabang WHERE trans_date BETWEEN '$tgl_from' AND '$tgl_to'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_best_customer2($from_date, $to_date){
		$sql = "SELECT cust_service, COUNT(cust_service) as total
				FROM `gold_main_pembelian`
				WHERE transaction_code LIKE 'BR%' AND status = 'A' AND trans_date BETWEEN '$from_date' AND '$to_date'
				GROUP BY cust_service
				ORDER BY COUNT(cust_service) DESC";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_count_beli_cs($from_date, $to_date){
		$sql = "SELECT transaction_code, cust_service
				FROM `gold_main_pembelian`
				WHERE transaction_code LIKE 'BR%' AND status = 'A' AND trans_date BETWEEN '$from_date' AND '$to_date'";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_count_beli_detail_cs($from_date, $to_date){
		$sql = "SELECT transaction_code, SUM(product_pcs) AS total
				FROM `gold_detail_pembelian`
				WHERE transaction_code LIKE 'BR%' AND status = 'A' AND trans_date BETWEEN '$from_date' AND '$to_date'
				GROUP BY transaction_code
				ORDER BY transaction_code";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_cs(){
		$sql = "SELECT username
				FROM `gold_karyawan`";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_bb_laprep(){
		$sql = "SELECT accountnumber,accountnumberint,beginningbalance,type,idkarat FROM `gold_coa_gr`
				WHERE accountnumberint BETWEEN 170001 AND 180003 AND status = 'A' AND type IN('SRT','SDR','SDG','TDG')
				ORDER BY idkarat";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}
	
	public function get_product_all($filter_category,$filter_box,$filter_karat){
		$sql = "SELECT p.*, k.karat_name, c.category_name, b.nama_box
				FROM gold_product p, gold_karat k, gold_product_category c, gold_box b
				WHERE p.id_karat = k.id AND p.id_category = c.id AND p.id_box = b.id AND p.id_category IN ($filter_category) AND p.id_box IN ($filter_box) AND p.id_karat IN ($filter_karat)
				ORDER BY p.in_date, p.created_date";

		$query = $this->db->query($sql)->result();
		return $query;
	}

	public function get_product_filter($filter_category,$filter_box,$filter_karat,$filter_status){
		$sql = "SELECT p.*, k.karat_name, c.category_name, b.nama_box
				FROM gold_product p, gold_karat k, gold_product_category c, gold_box b
				WHERE p.id_karat = k.id AND p.id_category = c.id AND p.id_box = b.id AND p.id_category IN ($filter_category) AND p.id_box IN ($filter_box) AND p.id_karat IN ($filter_karat) AND p.status = '$filter_status'
				ORDER BY p.in_date, p.created_date";
		
		$query = $this->db->query($sql)->result();
		return $query;
	}

	public function get_so_reason($id){
		$sql = "SELECT so_reason
				FROM gold_stock_out
				WHERE id_product = '$id'";

		$query = $this->db->query($sql)->result();
		return $query;
	}
}