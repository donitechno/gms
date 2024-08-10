<!DOCTYPE html>
<html lang="en">

<head>
   <?php $this->load->view('V_link') ?>
</head>
<style>
	.pusher{
		padding-top:43px;
		margin-left: 0px !important;
	}
	
	.ui.menu{
		margin-bottom:0 !important;
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
						<div class="active section">Mutasi Kas/Bank</div>
					</div>
				</div>
			</div>
			<div class="ui pointing secondary menu">
				<a class="item active" data-tab="first" style="width:50%" onclick=resetVal()>
					<i class="edit icon"></i> Input Mutasi Kas/Bank
				</a>
				<a class="item" data-tab="second" style="width:50%" onclick=filterTrans()>
					<i class="list ol icon"></i> List Mutasi Kas/Bank
				</a>
			</div>
			<div class="ui bottom attached tab segment active" data-tab="first">
				<div class="ui inverted dimmer" id="loader_form">
					<div class="ui large text loader">Loading</div>
				</div>
				<form  class="ui form form-javascript" id="form_transaction" action="<?php echo base_url() ?>index.php/C_mutasi_kas/save" method="post">
				<div class="ui grid">
					<div class="four wide column" style="padding-bottom:0">
						<div class="field">
							<label>Jenis Transaksi</label>
							<div id="wrap_jenis_1">
								<select name="jenis_1" id="jenis_1" onchange=getAccountTrans()>
									<option value="U">Transaksi Umum</option>
									<option value="K">Piutang Karyawan</option>
								</select>
							</div>
							<div id="wrap_jenis_2">
								<select name="jenis_2" id="jenis_2" onchange=getAccountTrans()>
									<option value="I">Penerimaan Kas/Bank</option>
									<option value="O">Pengeluaran Kas/Bank</option>
								</select>
							</div>
						</div>
					</div>
					<div class="four wide column" style="padding-bottom:0">
						<div class="field">
							<label>Account Kas/Bank</label>
							<div id="account_number_wrap">
								<select name="account_number" id="account_number">
									<?php foreach($kasbank as $k){ ?>
									<option value="<?php echo $k->accountnumber ?>"><?php echo $k->accountnumber ?> - <?php echo $k->accountname ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<div class="right floated four wide column" style="padding-bottom:0">
						<div class="field">
							<label>Tanggal Mutasi</label>
							<div id="wrap_tanggal_stock_in">
								<input type="text" name="tanggal_mutasi" id="tanggal_mutasi" readonly>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0">
						<div class="ui red message" id="error_wrap" style="display:none"></div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0" id="wrap_isi_data">
						<table id="pos_data_tabel" class="ui celled table" cellspacing="0" width="100%" style="margin-top:15px;">
							<thead>
								<tr class="center aligned">
									<th style="width:30px;">No</th>
									<th style="width:250px;">Account</th>
									<th>Keterangan</th>
									<th style="width:200px;">Jumlah</th>
								</tr>
							</thead>
							<tbody id="pos_body">
								<tr id="pos_tr_1">
									<td class="center aligned">1</td>
									<td>
										<select class="form-pos" onkeydown=entToTab("1","1") name="id_account_1" id="input_1_1" onchange=getKeterangan("1")>
											<option value="">-- Pilih Account --</option>
											<?php foreach($accountumum as $k){ ?>
											<option value="<?php echo $k->accountnumber ?>"><?php echo $k->accountnumber ?> - <?php echo $k->accountname ?></option>
											<?php } ?>
										</select>
									</td>
									<td>
										<input class="form-pos" type="text" name="keterangan_1" id="input_1_2" onkeydown=entToTab("1","2") autocomplete="off">
									</td>
									<td>
										<input class="form-pos" type="text" name="jumlah_1" id="input_1_3" onkeyup=valueToCurrency("1") autocomplete="off">
									</td>
								</tr>
							</tbody>
						</table>
						<div class="ui positive right floated labeled icon button" id="btn-save" onclick="saveTransaksi()">
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
				<form class="ui form form-javascript" id="form_filter" action="<?php echo base_url() ?>index.php/C_mutasi_kas/filter/" method="post">
				<div class="ui grid">
					<div class="fourteen wide centered column" style="padding-bottom:0">
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
							<div class="three wide field">
								<label>Jenis Transaksi</label>
								<select class="custom-select select-filter" name="filter_jenis_1" id="filter_jenis_1" onchange=getAccountTrans()>
									<option value="All">-- All --</option>
									<option value="U">Transaksi Umum</option>
									<option value="K">Piutang Karyawan</option>
								</select>
							</div>
							<div class="four wide field">
								<label style="visibility:hidden">-</label>
								<select class="custom-select select-filter" name="filter_jenis_2" id="filter_jenis_2" onchange=getAccountTrans()>
									<option value="All">-- All --</option>
									<option value="I">Penerimaan Kas/Bank</option>
									<option value="O">Pengeluaran Kas/Bank</option>
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
	<div class="ui modal mini" id="wrap_modal"></div>
</body>

<script>
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
	
	function getKeterangan(idRow){
		jenisTrans = $("#jenis_1").val();
		jenisTrans2 = $("#jenis_2").val();
		if(jenisTrans == 'K'){
			innerOptGlobal = document.getElementById("input_"+idRow+"_1");
			innerOpt = innerOptGlobal.getElementsByTagName("option");
			innerOptVal = innerOpt[innerOptGlobal.selectedIndex].innerHTML;
			
			arrayInner = innerOptVal.split("-");
			
			if(jenisTrans2 == 'I'){
				descTrans = 'PENGEMBALIAN PINJAMAN KARYAWAN -'+arrayInner[2];
			}else{
				descTrans = 'PINJAMAN KARYAWAN - '+arrayInner[2];
			}
			
			document.getElementById("input_"+idRow+"_2").value = descTrans;
		}
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
	
	function tambahBaris(){
		if(exeTambahBaris == false){
			exeTambahBaris = true;
			
			if(rowData == maxData){
				var view = '<i class="close icon"></i><div class="header">Warning</div><div class="content"><div class="ui icon header center aligned"><i class="exclamation triangle red icon"></i>Maksimal Hanya '+maxData+' Data</div></div></div>';
				
				$("#wrap_modal").html(view);
				
				$('.ui.modal').modal('show');
				
				exeTambahBaris = false;
			}else{
				jenisTrans = $("#jenis_1").val();
				var webUrl = "<?php echo base_url()?>";
				
				$.ajax({
					url : webUrl+'/index.php/C_mutasi_kas/tambah_baris/'+rowData+'/'+jenisTrans,
					type: 'post',
					dataType: 'json',
					success: function(response){
						if(response.success == true){
							$("#pos_body").append(response.view);
							
							rowData = rowData + 1;
							document.getElementById("input_"+rowData+"_1").focus();
							exeTambahBaris = false;
							
							for (var a = 1; a <= rowData; a++){
								if(jenisTrans == 'K'){
									document.getElementById("input_"+a+"_2").setAttribute('readonly','readonly');
								}else{
									document.getElementById("input_"+a+"_2").removeAttribute('readonly');
								}
							}
							
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
	
	function getAccountTrans(){
		jenisTrans = $("#jenis_1").val();
		
		if(exeTrans == false){
			exeTrans = true;
			
			var webUrl = "<?php echo base_url()?>";
			
			$.ajax({
				url : webUrl+'/index.php/C_mutasi_kas/get_account_data/'+jenisTrans,
				type: 'post',
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						var x = document.getElementsByClassName("invalid-tooltip");
					
						for (var a = 1; a <= rowData; a++){
							document.getElementById('input_'+a+'_1').innerHTML = response.account;
							document.getElementById('input_'+a+'_2').value = '';
							document.getElementById('input_'+a+'_3').value = '0';
							if(jenisTrans == 'K'){
								document.getElementById("input_"+a+"_2").setAttribute('readonly','readonly');
							}else{
								document.getElementById("input_"+a+"_2").removeAttribute('readonly');
							}
						}
						
						document.getElementById('account_number').innerHTML = response.header;
						
						countTotal();
						
						exeTrans = false;
					}else{
						var x = document.getElementsByClassName("invalid-tooltip");
					
						for (var a = 0; a < x.length; a++){
							x[a].setAttribute('style','display:none');
						}
						
						for (var i = 0; i < response.inputerror.length; i++) {
							document.getElementById(response.inputerror[i]).setAttribute('style','display:inherit');
						}
						
						swal({
							html:true,
							type: "error",
							title: "",
							text: response.error_message,
						});
						
						exeTrans = false;
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
	
	function loadForm(){
		tglKirim = $("#tanggal_mutasi").val();
		
		if(exeTrans == false){
			exeTrans = true;
			
			var webUrl = "<?php echo base_url()?>";
			
			$.ajax({
				url : webUrl+'/index.php/C_kirim_beli/form_kirim_new/'+tglKirim,
				type: 'post',
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						var x = document.getElementsByClassName("invalid-tooltip");
					
						for (var a = 0; a < x.length; a++){
							x[a].setAttribute('style','display:none');
						}
						
						document.getElementById('pos_body').innerHTML = response.view;
						
						/*$("#pos_body").append(response.view);
						document.getElementById("ket_select_from").setAttribute('readonly','readonly');
						document.getElementById("input_"+rowData+"_6").value = response.product_id;
						document.getElementById("input_"+rowData+"_7").innerHTML = response.button_messsage;
						document.getElementById("wrap_select_from").innerHTML = response.select_from;
						document.getElementById("wrap_tanggal_stock_in").innerHTML = response.tanggal_stock_in;
						document.getElementById("wrap_nama_barang_"+rowData).innerHTML = response.nama_barang;
						
						document.getElementById("input_"+rowData+"_1").setAttribute('disabled','true');
						document.getElementById("input_"+rowData+"_2").setAttribute('disabled','true');
						document.getElementById("input_"+rowData+"_3").setAttribute('disabled','true');
						document.getElementById("input_"+rowData+"_5").setAttribute('readonly','readonly');
						
						rowData = rowData + 1;
						document.getElementById("input_"+rowData+"_1").focus();
						$('#input_'+rowData+'_4').selectize();
						exeTrans = false;
						*/
						
						countTotal();
						
						
						exeTrans = false;
					}else{
						var x = document.getElementsByClassName("invalid-tooltip");
					
						for (var a = 0; a < x.length; a++){
							x[a].setAttribute('style','display:none');
						}
						
						for (var i = 0; i < response.inputerror.length; i++) {
							document.getElementById(response.inputerror[i]).setAttribute('style','display:inherit');
						}
						
						swal({
							html:true,
							type: "error",
							title: "",
							text: response.error_message,
						});
						
						exeTrans = false;
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
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
		
		document.getElementById("input_"+rowData+"_1").value = '';
		document.getElementById("input_"+rowData+"_2").value = '';
		document.getElementById("input_"+rowData+"_3").value = '';
		
		exeTambahBaris = false;
		exeInsert = true;
		
		window.setTimeout(function(){
			document.getElementById("input_"+rowData+"_1").focus();
		}, 500);
		
		getAccountTrans();
	}
	
	function inputPengirimanBaru(tglTrans){
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_kirim_beli/form_kirim_new/'+tglTrans,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					document.getElementById("wrap_filter").innerHTML = response.view;				
				}else{
					alert('error get laporan');
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
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
					alert('filter gagal');
					document.getElementById("btn_filter").classList.remove("loading");
					exeTrans = false;
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
	
	function detailRekapFilter(){
		detailRekap = $("#rekap_detail").val();
		if(detailRekap == 'R'){
			document.getElementById("wrap_kelompok").setAttribute('style','visibility:hidden');
			document.getElementById("wrap_karat").setAttribute('style','visibility:hidden');
			document.getElementById("tujuan_all").setAttribute('style','display:none');
			document.getElementById("tujuan_sendiri").setAttribute('style','display:none');
			
			tujuanVal = $("#filter_to").val();
			if(tujuanVal == 'All' || tujuanVal == 'S'){
				document.getElementById("filter_to").value = 'R';
			}
		}else if(detailRekap == 'D'){
			document.getElementById("wrap_kelompok").setAttribute('style','');
			document.getElementById("wrap_karat").setAttribute('style','');
			document.getElementById("tujuan_all").setAttribute('style','');
			document.getElementById("tujuan_sendiri").setAttribute('style','');
		}
	}
	
	function valueToCurrency(idRow){
		jumlahVal = $("#input_"+idRow+"_3").val();
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
			
			document.getElementById("input_"+idRow+"_3").value = beforeComma;
			
			countTotal();
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById("input_"+idRow+"_3").value = jumlahVal;
			
			countTotal();
		}
	}
	
	function countTotal(){
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
		
		if(totalGram.substr(totalGram.length - 3) == '.00'){
			totalGram = totalGram.substring(0, totalGram.length - 3);
		}
		
		document.getElementById("total_gram").innerHTML = totalGram;
	}
	
	function deleteTrans(idTrans){
		var view= '<div class="header">Hapus Transaksi</div><div class="content"><p>Anda Ingin Menghapus Data Tersebut?</p></div><div class="actions"><div class="ui negative button">Tidak </div><button id="btn_confirm" class="ui green right labeled icon button" onclick=exeDeleteTrans("'+idTrans+'")>Ya<i class="check circle icon"></i></button></div>';
		
		document.getElementById("wrap_modal").innerHTML = view;
		
		$('.ui.modal').modal({closable: false}).modal('show');
	}
	
	function exeDeleteTrans(idTrans){
		var webUrl = "<?php echo base_url()?>";
		document.getElementById("btn_confirm").className += " loading";
		
		$.ajax({
			url : webUrl+'/index.php/C_mutasi_kas/hapus/'+idTrans,
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
			document.getElementById("input_"+rowData+"_1").focus();
			countTotal();
		}, 300);
	};
</script>
</html>

