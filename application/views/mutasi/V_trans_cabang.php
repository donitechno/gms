<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view('V_link'); ?>
</head>
<style>
	.pusher{
		padding-top:43px;
		margin-left: 0px !important;
	}
	
	.ui.menu{
		margin-bottom:0 !important;
	}
	
	.form-total{
		padding: 8px !important;
		border:none !important;
	}
	
	.kosong-bg{
		background:#f9fafb;
	}
</style>
<body onkeyup="entToAction()">
	<?php $this->load->view("V_header_sidebar") ?>
	
	<div class="pusher">
		<div class="ui container fluid">
			<div class="ui stackable menu" style="border-radius:0">
				<div class="item">
					<div class="ui breadcrumb">
						<div class="section"><i class="list alternate outline icon"></i> Transaksi Usaha</div>
						<i class="right chevron icon divider"></i>
						<div class="active section">Transaksi Emas Antar Cabang</div>
					</div>
				</div>
			</div>
			<div class="ui pointing secondary menu">
				<a class="item active" data-tab="first" style="width:50%" onclick=resetVal()>
					<i class="edit icon"></i> Input Mutasi Emas Antar Cabang
				</a>
				<a class="item" data-tab="second" style="width:50%" onclick=filterTrans()>
					<i class="list ol icon"></i> List Mutasi Emas Antar Cabang
				</a>
			</div>
			<div class="ui bottom attached tab segment active" data-tab="first">
				<div class="ui inverted dimmer" id="loader_form">
					<div class="ui large text loader">Loading</div>
				</div>
				<form  class="ui form form-javascript" id="form_transaction" action="<?php echo base_url() ?>index.php/C_trans_cabang/save" method="post">
				<div class="ui grid">
					<div class="sixteen wide column" style="padding-bottom:0">
						<div class="fields">
							<div class="four wide field">
								<label>Account Cabang</label>
								<div id="account_number_wrap">
									<select name="account_number" id="account_number" onkeydown=entToHeader("jenis_trans") onchange=getTableForm()>
										<?php foreach($repgros as $k){ ?>
										<option value="<?php echo $k->accountnumber ?>"><?php echo $k->accountname ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="four wide field">
								<label>Jenis Transaksi</label>
								<select class="custom-select select-filter" name="jenis_trans" id="jenis_trans" onkeydown=entToHeader("keterangan")>
									<option value="O">Pengiriman ke Cabang</option>
									<option value="I">Penerimaan dari Cabang</option>
								</select>
							</div>
							<div class="four wide field">
								<label>Keterangan</label>
								<input type="text" name="keterangan" id="keterangan" onkeydown=entToHeader("tanggal_mutasi") autocomplete="off">
							</div>
							<div class="four wide field">
								<label>Tanggal Mutasi</label>
								<input type="text" name="tanggal_mutasi" id="tanggal_mutasi" readonly onkeydown=entToHeader("input_1_1") onchange=entToForm("input_1_1")>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0">
						<div class="ui red message" id="error_wrap" style="display:none"></div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0" id="wrap_isi_data">
						
					</div>
					<div class="eight wide column">
						<div class="ui grid" style="padding-top:30px;padding-bottom:15px">
							<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">F5</div>
							<div class="twelve wide column ket-bawah" style="padding-bottom:0;padding-top:0">: REFRESH HALAMAN</div>
							<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">Insert</div>
							<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: TAMBAH BARIS</div>
							<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">Home</div>
							<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: KURANG BARIS</div>
						</div>
					</div>
					<div class="eight wide column">
						<div class="ui grid" style="padding-top:30px;padding-bottom:15px">
							<div class="eight wide column ket-bawah right aligned" style="padding-bottom:0;padding-top:0">TOTAL KONVERSI (Gram)</div>
							<div class="eight wide column ket-bawah right aligned" id="total_gram" style="padding-bottom:0;padding-top:0"></div>
						</div>
					</div>
				</div>
				</form>
			</div>
			<div class="ui bottom attached tab segment" data-tab="second">
				<div class="ui inverted dimmer" id="loader_form">
					<div class="ui large text loader">Loading</div>
				</div>
				<form class="ui form form-javascript" id="form_filter" action="<?php echo base_url() ?>index.php/C_trans_cabang/filter/" method="post">
				<div class="ui grid">
					<div class="sixteen wide centered column" style="padding-bottom:0">
						<div class="fields">
							<div class="three wide field">
								<label>Tgl Mutasi</label>
								<input type="text" class="form-control input-filter" name="from_date" id="from_date" readonly>
							</div>
							<div class="one wide field" style="text-align:center;margin-top:30px">
								<label>s.d</label>
							</div>
							<div class="three wide field">
								<label style="visibility:hidden">-</label>
								<input type="text" class="form-control input-filter" name="to_date" id="to_date" readonly>
							</div>
							<div class="five wide field">
								<label>Account Cabang</label>
								<select class="custom-select select-filter" name="filter_cabang" id="filter_cabang" onkeydown=entToHeader("jenis_trans")>
									<option value="R">DEPARTEMEN REPARASI</option>
									<option value="G">DEPARTEMEN PENGADAAN</option>
								</select>
							</div>
							<div class="five wide field">
								<label>Jenis Transaksi</label>
								<select class="custom-select select-filter" name="filter_jenis" id="filter_jenis">
									<option value="All">-- All Transaksi --</option>
									<option value="Out">Pengiriman ke Cabang</option>
									<option value="In">Penerimaan dari Cabang</option>
								</select>
							</div>
							<div class="two wide field">
								<label style="visibility:hidden">-</label>
								<div class="ui fluid icon green button filter-input" id="btn_filter" onclick="filterTrans()" title="Filter">
									<i class="filter icon"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" id="wrap_filter" style="padding-top:0">
					</div>
				</div>
				</form>
			</div>
		</div>
	</div>
	
	<div class="ui modal" id="wrap_modal"></div>
</body>

<script>
	var viewRep = '<table id="pos_data_tabel" class="ui celled table" cellspacing="0" width="100%" style="margin-top:15px;"><thead><tr class="center aligned"><th style="width:30px;">No</th><th style="width:100px;">Karat</th><th style="width:100px">Gram Real</th><th style="width:100px">Gram Konversi 24K</th><th style="width:100px;">Persentase</th></tr></thead><tbody id="pos_body"><tr id="pos_tr_1"><td class="center aligned">1</td><td><select class="form-pos" onkeydown=entToTab("1","1") onchange=kaliPersentase("1") name="id_karat_1" id="input_1_1"><option value="">-- Pilih Karat --</option>';
			
	<?php foreach($karat as $k){ ?>
	viewRep +=	'<option value="<?php echo $k->id ?>"><?php echo $k->karat_name ?></option>';
	<?php } ?>
	
	viewRep +=	'</select></td><td><input class="form-pos" type="text" name="real_gram_1" id="input_1_2" onkeydown=entToTab("1","2") onkeyup=valueToCurrency("1","2") autocomplete="off"></td><td><input class="form-pos" type="text" name="konv_gram_1" id="input_1_3" onkeydown=entToTab("1","3") onkeyup=valueToCurrency("1","3") autocomplete="off"></td><td><input class="form-pos" type="text" name="persentase_1" id="input_1_4" readonly></td></tr></tbody></table><div class="ui positive right floated labeled icon button" id="btn-save" onclick="saveTransaksi()"><i class="save icon"></i> Simpan</div>';
	
	var viewGros = '<table id="pos_data_tabel" class="ui celled table" cellspacing="0" width="100%" style="margin-top:15px;"><thead><tr class="center aligned"><th style="width:30px;">No</th><th style="width:100px;">Karat</th><th style="width:100px">Gram Real</th><th style="width:100px">Gram Konversi 24K</th></tr></thead><tbody id="pos_body"><tr id="pos_tr_1"><td class="center aligned">1</td><td><select class="form-pos" onkeydown=entToTab("1","1") name="id_karat_1" id="input_1_1"><option value="">-- Pilih Karat --</option>';
	
	<?php foreach($karat as $k){ ?>
	viewGros += '<option value="<?php echo $k->id ?>"><?php echo $k->karat_name ?></option>';
	<?php } ?>
	
	viewGros += '</select></td><td><input class="form-pos" type="text" name="real_gram_1" id="input_1_2" onkeyup=valueToCurrency("1","2") autocomplete="off"></td><td class="kosong-bg"></td></tr></tbody><tfoot><th></th><th></th><th style="font-weight:bold;text-align:right">Total Konversi</th><th style="padding:0px !important;"><input class="form-total" type="text" name="total_konversi" id="input_1_9" onkeyup=valueToCurrency("1","9") autocomplete="off" style="font-size:1.2em !important"></th></tfoot></table><div class="ui positive right floated labeled icon button" id="btn-save" onclick="saveTransaksi()"><i class="save icon"></i> Simpan</div>';
	
	var exeInsert = true;
	var exeTrans = false;
	var exeTambahBaris = false;
	var maxData = 5;
	var rowData = 1;
	var flagTrans = 'input';
	
	$('select.dropdown').dropdown();
	$('.menu .item').tab();
	
	$( function() {
		$( "#from_date" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		$('#from_date').datepicker('setDate', 'today');
		
		$( "#to_date" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		$('#to_date').datepicker('setDate', 'today');
	} );
	
	$( function() {
		var dateFormat = "dd MM yy",
		from = $( "#from_date" )
		
		.datepicker({
			changeMonth: true,
			numberOfMonths: 1
		})
		.on( "change", function() {
		  to.datepicker( "option", "minDate", getDate( this ) );
		}),
		to = $( "#to_date" ).datepicker({
			changeMonth: true,
			numberOfMonths: 3
		})
		.on( "change", function() {
			from.datepicker( "option", "maxDate", getDate( this ) );
		});

		function getDate( element ) {
			var date;
			try {
				date = $.datepicker.parseDate( dateFormat, element.value );
			} catch( error ) {
				date = null;
			}

			return date;
		}
	});
	
	$(function(){
		$( "#tanggal_mutasi" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		
		$('#tanggal_mutasi').datepicker('setDate', 'today');
	});
	
	function entToHeader(idName){
		var x = event.keyCode;
		
		if(x == 13){
			document.getElementById(idName).focus();
		}
	}
	
	function entToForm(idName){
		document.getElementById(idName).focus();
	}
	
	function entToTab(idRow,idElement){
		var x = event.keyCode;
		idElement = parseFloat(idElement);
		idElement = idElement + 1;
		if(x == 13){
			document.getElementById("input_"+idRow+"_"+idElement+"").focus();
		}
	}
	
	function entToAction(){
		if(exeInsert == true){
			var x = event.keyCode;
			
			if(x == 45){
				tambahBaris();
			}
			
			if(x == 36 && rowData != 1){
				kurangBaris();
			}
		}
	}
	
	function valueToCurrency(idRow,idCol){
		jumlahVal = $("#input_"+idRow+"_"+idCol).val();
		if(jumlahVal == ''){
			jumlahVal = '0';
		}
		
		jumlahVal = jumlahVal.replace(/[^0-9.]/g, "");
		
		if (jumlahVal.indexOf('.') > -1){
			jumlahVal = jumlahVal.toString();
			
			var pos = jumlahVal.search(/\./g) + 1;
			jumlahVal = jumlahVal.substr( 0, pos )
             + jumlahVal.slice( pos ).replace(/\./g, '');
			
			var beforeComma = jumlahVal.substr(0,jumlahVal.indexOf("."));
			var afterComma = jumlahVal.substr(jumlahVal.indexOf(".") + 1);
			
			beforeComma = parseFloat(beforeComma);
			beforeComma = beforeComma.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(beforeComma.substr(beforeComma.length - 3) == '.00'){
				beforeComma = beforeComma.substring(0, beforeComma.length - 3);
			}
			
			document.getElementById("input_"+idRow+"_"+idCol).value = beforeComma+'.'+afterComma.substring(0, 2);
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById("input_"+idRow+"_"+idCol).value = jumlahVal;
		}
		
		var account = $("#account_number").val();
		if(account == '17-0002'){
			countTotal();
			if(idCol == 2){
				kaliPersentase(idRow);
			}else if(idCol == 3){
				countPersentase(idRow);
			}
		}else if(account == '17-0003'){
			countTotal();
		}
	}
	
	function getTableForm(){
		account = $("#account_number").val();
		if(account == '17-0002'){
			document.getElementById("wrap_isi_data").innerHTML = viewRep;
			rowData = 1;
		}else if(account == '17-0003'){
			document.getElementById("wrap_isi_data").innerHTML = viewGros;
			rowData = 1;
		}
		
		countTotal();
	}
	
	function countTotal(){
		var account = $("#account_number").val();
		if(account == '17-0002'){
			totalGram = 0;
			for (var a = 1; a <= rowData; a++) {
				jumlahVal = $("#input_"+a+"_3").val();
				
				if(jumlahVal == '' || jumlahVal == 'NaN'){
					jumlahVal = "0";
				}
				
				jumlahVal = jumlahVal.replace(/[^0-9.]/g, "");
				jumlahVal = parseFloat(jumlahVal);
				
				totalGram = totalGram + jumlahVal;
			}
			
			totalGram = parseFloat(totalGram);
			totalGram = totalGram.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			document.getElementById("total_gram").innerHTML = totalGram;
		}else if(account == '17-0003'){
			jumlahVal = $("#input_1_9").val();
			
			if(jumlahVal == '' || jumlahVal == 'NaN'){
				jumlahVal = "0";
			}
			
			jumlahVal = jumlahVal.replace(/[^0-9.]/g, "");
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			document.getElementById("total_gram").innerHTML = jumlahVal;
		}
		
		
	}
	
	function countPersentase(idRow){
		gramReal = $("#input_"+idRow+"_2").val();
		if(gramReal == '' || gramReal == 'NaN'){
			gramReal = "0";
		}
		
		gramReal = gramReal.replace(/[^0-9.]/g, "");
		gramReal = parseFloat(gramReal);
		
		gram24 = $("#input_"+idRow+"_3").val();
		if(gram24 == '' || gram24 == 'NaN'){
			gram24 = "0";
		}
		
		gram24 = gram24.replace(/[^0-9.]/g, "");
		gram24 = parseFloat(gram24);
		
		if(gramReal == 0 || gramReal == '' || gramReal == null){
			valPersentase = 0;
		}else{
			valPersentase = gram24 / gramReal * 100;
		}
		
		valPersentase = Math.round(valPersentase);
		/*valPersentase = valPersentase.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
		if(valPersentase.substr(valPersentase.length - 3) == '.00'){
			valPersentase = valPersentase.substring(0, valPersentase.length - 3);
		}*/
		
		document.getElementById("input_"+idRow+"_4").value = valPersentase+' %';
	}
	
	function kaliPersentase(idRow){
		var karat = $("#input_"+idRow+"_1").val();
		if(karat == 1){
			var persenTase = 100;
		}else if(karat == 3){
			var persenTase = 92;
		}else if(karat == 4){
			var persenTase = 75;
		}else if(karat == 5){
			var persenTase = 70;
		}else{
			var persenTase = 0;
		}
		
		if(persenTase == 0){
			document.getElementById("input_"+idRow+"_3").value = '0';
		}else{
			jumlahVal = $("#input_"+idRow+"_2").val();
			if(jumlahVal == ''){
				jumlahVal = '0';
			}
			
			jumlahVal = jumlahVal.replace(/[^0-9.]/g, "");
			jumlahVal = parseFloat(jumlahVal);
			
			konversiVal = jumlahVal * persenTase / 100;
			
			konversiVal = konversiVal.toString();
			if (konversiVal.indexOf('.') > -1){
				konversiVal = konversiVal.toString();
				
				var pos = konversiVal.search(/\./g) + 1;
				konversiVal = konversiVal.substr( 0, pos )
				 + konversiVal.slice( pos ).replace(/\./g, '');
				
				var beforeComma = konversiVal.substr(0,konversiVal.indexOf("."));
				var afterComma = konversiVal.substr(konversiVal.indexOf(".") + 1);
				
				beforeComma = parseFloat(beforeComma);
				beforeComma = beforeComma.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
				
				if(beforeComma.substr(beforeComma.length - 3) == '.00'){
					beforeComma = beforeComma.substring(0, beforeComma.length - 3);
				}
				
				document.getElementById("input_"+idRow+"_3").value = beforeComma+'.'+afterComma.substring(0, 2);
			}else{
				konversiVal = parseFloat(konversiVal);
				konversiVal = konversiVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
				
				if(konversiVal.substr(konversiVal.length - 3) == '.00'){
					konversiVal = konversiVal.substring(0, konversiVal.length - 3);
				}
				
				document.getElementById("input_"+idRow+"_3").value = konversiVal;
			}
		}
		
		document.getElementById("input_"+idRow+"_4").value = persenTase+' %';
		countTotal();
	}
	
	function tambahBaris(){
		if(exeTambahBaris == false){
			exeTambahBaris = true;
			
			account = $("#account_number").val();
			if(account == '17-0002'){
				var deptID = 'R';
			}else if(account == '17-0003'){
				var deptID = 'P';
			}
			
			if(rowData == maxData){
				swal({
					html:true,
					type: "warning",
					title: "Maksimal Hanya "+maxData+" Item!",
					text: "",
					timer: 2000,
					showConfirmButton: false
				});
				
				exeTambahBaris = false;
			}else{
				var webUrl = "<?php echo base_url()?>";
				
				$.ajax({
					url : webUrl+'/index.php/C_trans_cabang/tambah_baris/'+deptID+'/'+rowData,
					type: 'post',
					dataType: 'json',
					success: function(response){
						if(response.success == true){
							$("#pos_body").append(response.view);
							
							rowData = rowData + 1;
							document.getElementById("input_"+rowData+"_1").focus();
							exeTambahBaris = false;
							
							countTotal();
						}else{
							swal({
								html:true,
								type: "error",
								title: "",
								text: response.pesan_error,
							});
							
							exeTambahBaris = false;
						}
					},
					error: function (jqXHR, textStatus, errorThrown){
						alert('Error get data from ajax');
					}
				})
			}
		}
	}
	
	function kurangBaris(){
		if(exeTambahBaris == false){
			exeTambahBaris = true;
			
			$("#pos_tr_"+rowData).remove();
			rowData = rowData - 1;
			exeTambahBaris = false;
			document.getElementById("input_"+rowData+"_1").focus();
			
			countTotal();
		}
	}
	
	function saveTransaksi(){
		if(exeTrans == false){
			exeTrans = true;
			
			document.getElementById("error_wrap").setAttribute('style','display:none');
			document.getElementById("btn-save").className += " loading";
			
			var webUrl = "<?php echo base_url()?>";
			
			$.ajax({
				url: $('#form_transaction').attr('action')+'/'+rowData,
				type: 'post',
				data: $('#form_transaction').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						document.getElementById("wrap_modal").innerHTML = response.message;
						$('.ui.modal').modal('show');
						window.setTimeout(function(){
							$('.ui.modal').modal('hide');
						}, 2000);
						exeTrans = false;
						
						document.getElementById("btn-save").classList.remove("loading");
						
						resetVal();
					}else{
						document.getElementById("error_wrap").innerHTML = response.inputerror;
						document.getElementById("error_wrap").setAttribute('style','');
						
						exeTrans = false;
						document.getElementById("btn-save").classList.remove("loading");
						
						document.getElementById("input_"+rowData+"_1").focus();
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
	
	function resetVal(){
		if(rowData > 1){
			for (var a = 2; a <= rowData; a++) {
				$("#pos_tr_"+a).remove();
			}
		}
				
		rowData = 1;
		
		document.getElementById("keterangan").value = '';
		
		document.getElementById("input_"+rowData+"_1").value = '';
		document.getElementById("input_"+rowData+"_2").value = '';
		
		account = $("#account_number").val();
		if(account == '17-0002'){
			document.getElementById("input_"+rowData+"_3").value = '';
			document.getElementById("input_"+rowData+"_4").value = '';
		}else if(account == '17-0003'){
			document.getElementById("input_1_9").value = '';
		}
		
		document.getElementById("error_wrap").setAttribute('style','display:none');
		
		exeTambahBaris = false;
		exeInsert = true;
		window.setTimeout(function(){
			document.getElementById("input_"+rowData+"_1").focus();
		}, 500);
	}
	
	function filterTrans(){
		exeTrans = true;
		document.getElementById("btn_filter").className += " loading";
		
		$.ajax({
			url: $('#form_filter').attr('action'),
			type: 'post',
			data: $('#form_filter').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					document.getElementById("wrap_filter").innerHTML = response.view;
					
					$(document).ready(function() {
						$('#filter_data_tabel').DataTable({
							"bLengthChange": false
						});
					} );
										
					document.getElementById("btn_filter").classList.remove("loading");
					exeTrans = false;
				}else{
					alert('Gagal Filter Data!');
					document.getElementById("btn_filter").classList.remove("loading");
					exeTrans = false;
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
	
	function viewTrans(transID,deptID){
		if(exeTrans == false){
			exeTrans = true;
			document.getElementById("wrap_modal").classList.remove("mini");
			
			var webUrl = "<?php echo base_url()?>";
			
			$.ajax({
				url : webUrl+'/index.php/C_trans_cabang/detail_trans/'+transID+'/'+deptID,
				type: 'post',
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						document.getElementById("wrap_modal").innerHTML = response.view;
						$('.ui.modal').modal('show');
						
						exeTrans = false;
					}else{
						alert('Gagal Mengambil Data!');
						exeTrans = false;
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
	
	function deleteTrans(idTrans,idDept){
		var view= '<div class="header">Hapus Transaksi</div><div class="content"><p>Anda Ingin Menghapus Data Tersebut?</p></div><div class="actions"><div class="ui negative button">Tidak </div><button id="btn_confirm" class="ui green right labeled icon button" onclick=exeDeleteTrans("'+idTrans+'","'+idDept+'")>Ya<i class="check circle icon"></i></button></div>';
		
		document.getElementById("wrap_modal").innerHTML = view;
		document.getElementById("wrap_modal").className += " mini";
		
		$('.ui.modal').modal('show');
	}
	
	function exeDeleteTrans(idTrans,idDept){
		var webUrl = "<?php echo base_url()?>";
		document.getElementById("btn_confirm").className += " loading";
		
		$.ajax({
			url : webUrl+'/index.php/C_trans_cabang/hapus/'+idTrans+'/'+idDept,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					document.getElementById("wrap_modal").innerHTML = response.message;
					filterTrans();
					window.setTimeout(function(){
						$('.ui.modal').modal('hide');
					}, 2000);
				}else{
					alert('Gagal Hapus!')
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('Error get data from ajax');
			}
		});
	}
	
	window.onload = function() {
		window.setTimeout(function(){
			document.getElementById("account_number").focus();
			getTableForm();
		}, 300);
	};
</script>
</html>

