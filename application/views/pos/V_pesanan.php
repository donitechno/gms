<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view('V_link_pos'); ?>
</head>
<style>	
	.ui.menu{
		margin-bottom:0 !important;
	}
	
	.selectize-input{
		border:0px;
		-webkit-box-shadow:0 0px 0 rgba(0,0,0,0), inset 0 0px 0 rgba(255,255,255,0) !important;
		box-shadow:0 0px 0 rgba(0,0,0,0), inset 0 0px 0 rgba(255,255,255,0) !important;
	}
	
	#modal-table td{
		font-size:0.9em;
		padding: 0.1em 0.78571429em;
	}
	
	.pilih{
		padding:.4em 1em !important;
	}
	
	#modal_data_tabel td{
		padding:0 0.5em;
	}
	
	.ui.table thead th, .ui.table tbody td {
		padding: 0.3em 0.78571429em;
	}
	
</style>
<body onkeyup="entToAction()">
	<div class="ui container fluid">
		<div class="ui pointing secondary menu">
			<a class="item active" data-tab="first" style="width:25%" onclick=resetVal()>
				<i class="edit icon"></i> Input Pesanan
			</a>
			<a class="item" data-tab="second" style="width:25%" onclick=masukBox()>
				<i class="list hdd outline icon"></i> Pesanan Masuk Box
			</a>
			<a class="item" data-tab="third" style="width:25%" onclick=ambilPesanan()>
				<i class="tags icon"></i> Ambil Pesanan
			</a>
			<a class="item" data-tab="fourth" style="width:50%" onclick=filterTrans()>
				<i class="list ol icon"></i> List Pesanan
			</a>
		</div>
		<div class="ui bottom attached tab segment active" data-tab="first">
			<div class="ui inverted dimmer" id="loader_form">
				<div class="ui large text loader">Loading</div>
			</div>
			<form  class="ui form form-javascript" id="form_transaction" action="<?php echo base_url() ?>index.php/C_pesanan_pos/save" method="post">
			<div class="ui grid">
				<div class="five wide column" style="padding-bottom:0">
					<div class="field">
						<label>Data Pelanggan</label>
						<div class="ui action input">
							<input type="text" name="customer_name" id="customer_name" onkeyup=entToHeader("customer_address") placeholder="Nama Pelanggan">
							<div id="btn_search" class="ui icon positive button" title="Cari" onclick="getCustomerForm()">
								<i class="eye icon"></i>
							</div>
						</div>
					</div>
					<div class="field">
						<input type="text" name="customer_address" id="customer_address" onkeyup=entToHeader("customer_phone") placeholder="Alamat Pelanggan">
					</div>
					<div class="field">
						<input type="text" name="customer_phone" id="customer_phone" onkeyup=entToHeader("pesanan_number") placeholder="No. Telp Pelanggan">
					</div>
				</div>
				<div class="five wide column" style="padding-bottom:0">
					<div class="field">
						<label>Nomor Pesanan</label>
						<div class="ui labeled input">
							<div class="ui label">PS</div>
							<input type="number" name="pesanan_number" id="pesanan_number" onkeyup=entToHeader("ump_val")>
						</div>
					</div>
					<div class="field">
						<input type="text" name="ump_val" id="ump_val" onkeyup=valueToCurrency("ump_val") onkeydown=entToHeader("input_1_1") placeholder="Uang Muka Pesanan (IDR)">
					</div>
				</div>
				<div class="right floated four wide column" style="padding-bottom:0">
					<div class="field">
						<label>Tanggal Mutasi</label>
						<div id="wrap_tanggal_stock_in">
							<input type="text" name="tanggal_mutasi" id="tanggal_mutasi" readonly onkeydown=entToHeader("input_1_1") onchange=entToForm("input_1_1")>
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
								<th style="width:150px;">Karat</th>
								<th style="width:200px;">Kelompok</th>
								<th>Nama Barang</th>
							</tr>
						</thead>
						<tbody id="pos_body">
							<tr id="pos_tr_1">
								<td class="center aligned">1</td>
								<td>
									<select class="form-pos" onkeydown=entToTab("1","1") name="id_karat_1" id="input_1_1">
										<option value="">-- Karat --</option>
										<?php foreach($karat as $k){ ?>
										<option value="<?php echo $k->id ?>"><?php echo $k->karat_name ?></option>
										<?php } ?>
									</select>
								</td>
								<td>
									<select class="form-pos" name="id_category_1" id="input_1_2" onchange=getMasterProduct("1")>
										<option value="">-- Kelompok --</option>
										<?php foreach($category as $c){ ?>
										<option value="<?php echo $c->id ?>"><?php echo $c->category_name ?></option>
										<?php } ?>
									</select>
								</td>
								<td>
									<div id="wrap_nama_barang_1">
										<select name="nama_barang_1" id="input_1_3">
											<option value=""></option>
										</select>
									</div>
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
						<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">F5</div>
						<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: REFRESH HALAMAN</div>
						<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">Insert</div>
						<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: TAMBAH BARIS</div>
						<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">Home</div>
						<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: KURANG BARIS</div>
					</div>
				</div>
				<div class="eight wide column">
					<div class="ui grid" style="padding-top:30px;padding-bottom:15px">
						<div class="eight wide column ket-bawah right aligned" style="padding-bottom:0;padding-top:0">TOTAL (IDR)</div>
						<div class="eight wide column ket-bawah right aligned" id="total_gram" style="padding-bottom:0;padding-top:0">0</div>
					</div>
				</div>
			</div>
			</form>
		</div>
		<div class="ui bottom attached tab segment" data-tab="second">
			<div class="ui inverted dimmer" id="box-loader_form">
				<div class="ui large text loader">Loading</div>
			</div>
			<form class="ui form form-javascript" id="form_box" action="<?php echo base_url() ?>index.php/C_pesanan_pos/box/" method="post">
			<div class="ui grid">
				<div class="sixteen wide column" id="box-wrap_filter" style="padding-top:0">
				</div>
			</div>
			</form>
		</div>
		<div class="ui bottom attached tab segment" data-tab="third">
			<div class="ui inverted dimmer" id="ambil-loader_form">
				<div class="ui large text loader">Loading</div>
			</div>
			<form class="ui form form-javascript" id="form_ambil" action="<?php echo base_url() ?>index.php/C_pesanan_pos/ambil/" method="post">
			<div class="ui grid">
				<div class="sixteen wide column" id="ambil-wrap_filter" style="padding-top:0">
				</div>
			</div>
			</form>
		</div>
		<div class="ui bottom attached tab segment" data-tab="fourth">
			<div class="ui inverted dimmer" id="loader_form">
				<div class="ui large text loader">Loading</div>
			</div>
			<form class="ui form form-javascript" id="form_filter" action="<?php echo base_url() ?>index.php/C_pesanan_pos/filter/" method="post">
			<div class="ui grid">
				<div class="fourteen wide centered column" style="padding-bottom:0">
					<div class="fields">
						<div class="four wide field">
							<label>Tgl Mutasi</label>
							<input type="text" name="from_date" id="from_date" value="01 January <?php echo date('Y') ?>" readonly>
						</div>
						<div class="one wide field" style="text-align:center;margin-top:30px">
							<label>s.d</label>
						</div>
						<div class="four wide field">
							<label style="visibility:hidden">-</label>
							<input type="text" name="to_date" id="to_date" readonly>
						</div>
						<div class="four wide field">
							<label>Jenis Transaksi</label>
							<select name="filter_status" id="filter_status">
								<option value="All">-- All --</option>
								<option value="P">Dalam Proses</option>
								<option value="B">Masuk Box</option>
								<option value="C">Selesai</option>
								<option value="X">Batal</option>
							</select>
						</div>
						<div class="three wide field">
							<label style="visibility:hidden">-</label>
							<div class="ui fluid icon green button filter-input" id="btn_filter" onclick="filterTrans()" title="Filter">
								<i class="filter icon"></i> Filter
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
	<div class="ui modal mini" id="myModal"></div>
</body>

<script>
	var exeTrans = false;
	var exeTambahBaris = false;
	var maxData = 5;
	var rowData = 1;
	var flagTrans = 'input';
	var rowDetail = 1;
	
	$('select.dropdown').dropdown();
	$('.menu .item').tab();
	
	document.getElementById("customer_name").focus();
	$('#input_1_3').selectize();
	
	$( function() {
		$( "#from_date" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		//$('#from_date').datepicker('setDate', 'today');
		
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
	
	function notEnter(){
		$('.form-javascript').on('keyup keypress', function(e){
			var keyCode = e.keyCode || e.which;
			if (keyCode === 13) {
				e.preventDefault();
				return false;
			}
		});
	}
	
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
	
	function entToTabModal(idElement){
		var x = event.keyCode;
		if(x == 13){
			document.getElementById(idElement).select();
		}
	}
	
	function entToTabModalEdit(idRow,idElement){
		var x = event.keyCode;
		idElement = parseFloat(idElement);
		idElement = idElement + 1;
		if(x == 13){
			if(idElement == 3){
				document.getElementById("input_modal_"+idRow+"_"+idElement+"-selectized").focus();
			}else{
				document.getElementById("input_modal_"+idRow+"_"+idElement+"").focus();
			}
		}
	}
	
	function entToAction(){
		var x = event.keyCode;
		
		if(x == 45){
			tambahBaris();
		}
		
		if(x == 36 && rowData != 1){
			kurangBaris();
		}
		
		if(x == 27){
			var webUrl = "<?php echo base_url()?>index.php/C_home_pos";
			window.location= webUrl;
		}
	}
	
	function valueToCurrency(idName){
		jumlahVal = $("#"+idName).val();
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
			
			document.getElementById(idName).value = beforeComma;
			document.getElementById("total_gram").innerHTML = beforeComma;
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById(idName).value = jumlahVal;
			document.getElementById("total_gram").innerHTML = jumlahVal;
		}
	}
	
	function getMasterProduct(idRow){
		var webUrl = "<?php echo base_url()?>";
		var categoryID = $("#input_"+idRow+"_2").val();
		
		if(categoryID != ''){
			$.ajax({
				url : webUrl+'/index.php/C_pesanan_pos/get_master_product/'+categoryID+'/'+idRow,
				type: "GET",
				dataType: "JSON",
				success: function(response){
					if(response.success == true){
						document.getElementById('wrap_nama_barang_'+idRow).innerHTML = response.view;
						$('#input_'+idRow+'_3').selectize({
							onChange: function(value) {
								entToTab(idRow,1);
							}
						});
								
						document.getElementById('input_'+idRow+'_3-selectized').focus();
					}else{
						alert('Gagal Koneksi ke Server!');
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('System Error!');
					alert(categoryID);
					alert(idRow);
				}
			});
		}
	}
	
	function getMasterProductModal(idRow){
		var webUrl = "<?php echo base_url()?>";
		var categoryID = $("#input_modal_"+idRow+"_2").val();
		
		if(categoryID != ''){
			$.ajax({
				url : webUrl+'/index.php/C_pesanan_pos/get_master_product_modal/'+categoryID+'/'+idRow,
				type: "GET",
				dataType: "JSON",
				success: function(response){
					if(response.success == true){
						document.getElementById('wrap_nama_barang_modal_'+idRow).innerHTML = response.view;
						$('#input_modal_'+idRow+'_3').selectize({
							/*onChange: function(value) {
								entToTabModalEdit(idRow,1);
							}*/
						});
								
						document.getElementById('input_modal_'+idRow+'_3-selectized').focus();
					}else{
						alert('Gagal Koneksi ke Server!');
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('System Error!');
					alert(categoryID);
					alert(idRow);
				}
			});
		}
	}
	
	function tambahBaris(){
		if(exeTambahBaris == false){
			exeTambahBaris = true;
			
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
				jenisTrans = $("#jenis_1").val();
				var webUrl = "<?php echo base_url()?>";
				
				$.ajax({
					url : webUrl+'/index.php/C_pesanan_pos/tambah_baris/'+rowData,
					type: 'post',
					dataType: 'json',
					success: function(response){
						if(response.success == true){
							$("#pos_body").append(response.view);
							
							rowData = rowData + 1;
							$('#input_'+rowData+'_3').selectize();
							document.getElementById("input_"+rowData+"_1").focus();
							exeTambahBaris = false;
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
	
	function getCustomerForm(){
		document.getElementById("myModal").classList.remove("mini");
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'index.php/C_pesanan_pos/get_customer_form/',
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					$("#myModal").html(response.view);
					
					$('#modal-table').DataTable({
						"bPaginate": true,
						"bLengthChange": false,
						"bInfo": false
					});
					
					$('.ui.modal')
						.modal({
						
						}).modal('show');
					
					
				}else{
					alert('System Error!');
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('System Error!');
			}
		});
	}
	
	function setCustomer(custPhone){
		$('#myModal').modal('hide');
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_pesanan_pos/set_customer_form/'+custPhone,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					document.getElementById('customer_name').value=response.customer_name;
					document.getElementById('customer_address').value=response.customer_address;
					document.getElementById('customer_phone').value=response.customer_phone;
					
					document.getElementById("customer_name").setAttribute('readonly','readonly');
					document.getElementById("customer_address").setAttribute('readonly','readonly');
					document.getElementById("customer_phone").setAttribute('readonly','readonly');
					
					document.getElementById("pesanan_number").focus();
					document.getElementById("btn_search").setAttribute('onclick','clearCustomer()');
					document.getElementById("btn_search").innerHTML = '<i class="trash alternate icon"></i>';
				}else{
					alert('System Error!');
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('System Error!');
			}
		});
	}
	
	function clearCustomer(){
		document.getElementById('customer_name').value='';
		document.getElementById('customer_address').value='';
		document.getElementById('customer_phone').value='';
		
		document.getElementById("customer_name").removeAttribute('readonly');
		document.getElementById("customer_address").removeAttribute('readonly');
		document.getElementById("customer_phone").removeAttribute('readonly');
		
		document.getElementById("customer_name").focus();
		document.getElementById("btn_search").setAttribute('onclick','getCustomerForm()');
		document.getElementById("btn_search").innerHTML = '<i class="search icon"></i>';
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
						document.getElementById("myModal").className += " mini";
						document.getElementById("myModal").innerHTML = response.message;
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
		document.getElementById('customer_name').value='';
		document.getElementById('customer_address').value='';
		document.getElementById('customer_phone').value='';
		document.getElementById('pesanan_number').value='';
		document.getElementById('ump_val').value='';
		
		document.getElementById("customer_name").removeAttribute('readonly');
		document.getElementById("customer_address").removeAttribute('readonly');
		document.getElementById("customer_phone").removeAttribute('readonly');
		
		document.getElementById("customer_name").focus();
		document.getElementById("btn_search").setAttribute('onclick','getCustomerForm()');
		document.getElementById("btn_search").innerHTML = '<i class="search icon"></i>';
		
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
		document.getElementById("input_"+rowData+"_1").focus();
	}
	
	function filterTrans(){
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
				}else{
					swal({
						type: "error",
						title: "Gagal Filter Data Stock In!",
						text: "",
						timer: 2000,
						showConfirmButton: false
					});
					
					document.getElementById("btn_filter").classList.remove("loading");
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
	
	function masukBox(){	
		$.ajax({
			url: $('#form_box').attr('action'),
			type: 'post',
			data: $('#form_box').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					document.getElementById("box-wrap_filter").innerHTML = response.view;
					
					$(document).ready(function() {
						$('#box-filter_data_tabel').DataTable({
							"bLengthChange": false
						});
					} );
				}else{
					swal({
						type: "error",
						title: "Gagal Filter Data!",
						text: "",
						timer: 2000,
						showConfirmButton: false
					});
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
	
	function ambilPesanan(){	
		$.ajax({
			url: $('#form_ambil').attr('action'),
			type: 'post',
			data: $('#form_ambil').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					document.getElementById("ambil-wrap_filter").innerHTML = response.view;
					
					$(document).ready(function() {
						$('#ambil-filter_data_tabel').DataTable({
							"bLengthChange": false
						});
					} );
				}else{
					swal({
						type: "error",
						title: "Gagal Filter Data!",
						text: "",
						timer: 2000,
						showConfirmButton: false
					});
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
	
	function getDetailPesanan(idPesanan){
		document.getElementById("myModal").classList.remove("mini");
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_pesanan_pos/get_detail_pesanan/'+idPesanan,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					$("#myModal").html(response.view);
					
					$('.ui.modal')
					.modal({
					closable: false
					}).modal('show');
					
					rowDetail = response.row_detail;
					
					if(response.status == 'P' || response.status == 'B'){
						$(function(){
							$( "#tanggal_action" ).datepicker({
								dateFormat: 'dd MM yy'
							});
							
							$('#tanggal_action').datepicker('setDate', 'today');
						});
					}
					
					window.setTimeout(function(){
						document.getElementById("input_modal_1_1").select();
					}, 1000);
					
					//countTotalDetail();
				}else{
					alert('Gagal Koneksi ke Server!');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('System Error!');
			}
		});
	}
	
	function getDetailPesananBox(idPesanan){
		document.getElementById("myModal").classList.remove("mini");
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_pesanan_pos/get_detail_pesanan_box/'+idPesanan,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					$("#myModal").html(response.view);
					
					$('.ui.modal')
					.modal({
					closable: false
					}).modal('show');
					
					rowDetail = response.row_detail;
					
					if(response.status == 'P' || response.status == 'B'){
						$(function(){
							$( "#tanggal_action" ).datepicker({
								dateFormat: 'dd MM yy'
							});
							
							$('#tanggal_action').datepicker('setDate', 'today');
						});
					}
					
					window.setTimeout(function(){
						document.getElementById("input_modal_1_1").select();
					}, 1000);
					
					//countTotalDetail();
				}else{
					alert('Gagal Koneksi ke Server!');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('System Error!');
			}
		});
	}
	
	function getDetailPesananAmbil(idPesanan){
		document.getElementById("myModal").classList.remove("mini");
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_pesanan_pos/get_detail_pesanan_ambil/'+idPesanan,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					$("#myModal").html(response.view);
					
					$('.ui.modal')
					.modal({
					closable: false
					}).modal('show');
					
					rowDetail = response.row_detail;
					
					if(response.status == 'P' || response.status == 'B'){
						$(function(){
							$( "#tanggal_action" ).datepicker({
								dateFormat: 'dd MM yy'
							});
							
							$('#tanggal_action').datepicker('setDate', 'today');
						});
					}
					
					window.setTimeout(function(){
						document.getElementById("input_modal_1_1").select();
					}, 1000);
					
					//countTotalDetail();
				}else{
					alert('Gagal Koneksi ke Server!');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('System Error!');
			}
		});
	}
	
	function getEditPesanan(idPesanan){
		document.getElementById("myModal").classList.remove("mini");
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_pesanan_pos/get_edit_pesanan/'+idPesanan,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					$("#myModal").html(response.view);
					
					$('.ui.modal')
					.modal({
					closable: false
					}).modal('show');
					
					rowDetail = response.row_detail;
					
					if(response.status == 'P' || response.status == 'B'){
						$(function(){
							$( "#tanggal_action" ).datepicker({
								dateFormat: 'dd MM yy'
							});
							
							$('#tanggal_action').datepicker('setDate', 'today');
						});
						
						for (var a = 1; a <= rowDetail; a++) {
							$('#input_modal_'+a+'_3').selectize({});
						}
					}
					
					/*window.setTimeout(function(){
						document.getElementById("input_modal_1_1").focus();
					}, 1000);*/
					
					//countTotalDetail();
				}else{
					alert('Gagal Koneksi ke Server!');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('System Error!');
			}
		});
	}
	
	function getTambahUMP(idPesanan){
		var webUrl = "<?php echo base_url()?>";
		document.getElementById("myModal").className += " mini";
		
		$.ajax({
			url : webUrl+'/index.php/C_pesanan_pos/get_tambah_ump/'+idPesanan,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					document.getElementById("myModal").innerHTML = response.view;
					
					$(function(){
						$( "#ump_date" ).datepicker({
							dateFormat: 'dd MM yy'
						});
						
						$('#ump_date').datepicker('setDate', 'today');
					});
					
					$('.ui.modal')
						.modal({
						closable: false
					}).modal('show');
					
					notEnter();
				}else{
					alert('Gagal Koneksi ke Server!');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('System Error, Hubungi Tim IT!');
			}
		});
	}
	
	function getKurangUMP(idPesanan){
		var webUrl = "<?php echo base_url()?>";
		document.getElementById("myModal").className += " mini";
		
		$.ajax({
			url : webUrl+'/index.php/C_pesanan_pos/get_kurang_ump/'+idPesanan,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					document.getElementById("myModal").innerHTML = response.view;
					
					$(function(){
						$( "#ump_date" ).datepicker({
							dateFormat: 'dd MM yy'
						});
						
						$('#ump_date').datepicker('setDate', 'today');
					});
					
					$('.ui.modal')
						.modal({
						closable: false
					}).modal('show');
					
					notEnter();
				}else{
					alert('Gagal Koneksi ke Server!');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('System Error, Hubungi Tim IT!');
			}
		});
	}
	
	function weightToCurrency(idName,rowWeight){
		jumlahVal = $("#"+idName+""+rowWeight).val();
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
			
			document.getElementById(idName+""+rowWeight).value = beforeComma+'.'+afterComma.substring(0,2);
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById(idName+""+rowWeight).value = jumlahVal;
		}
		
		countTotalGram();
	}
	
	function grosirToCurrency(){
		jumlahVal = $("#saldo_grosir").val();
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
			
			document.getElementById("saldo_grosir").value = beforeComma+'.'+afterComma.substring(0,2);
			//countTotalDetail();
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById("saldo_grosir").value = jumlahVal;
			//countTotalDetail();
		}
	}
	
	function jualToCurrency(idName,rowWeight){
		jumlahVal = $("#"+idName+""+rowWeight).val();
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
			
			document.getElementById(idName+""+rowWeight).value = beforeComma;
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById(idName+""+rowWeight).value = jumlahVal;
		}
		
		countTotalModal();
	}
	
	function umpToCurrency(){
		jumlahVal = $("#ump_tambah_kurang").val();
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
			
			document.getElementById("ump_tambah_kurang").value = beforeComma+'.'+afterComma.substring(0,2);
			//countTotalDetail();
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById("ump_tambah_kurang").value = jumlahVal;
			//countTotalDetail();
		}
	}
	
	function countTotalModal(){
		totalGram = 0;
		for (var a = 1; a <= rowDetail; a++) {
			jumlahVal = $("#input_modal_1_"+a).val();
			
			if(jumlahVal == '' || jumlahVal == 'NaN'){
				jumlahVal = "0";
			}
			
			jumlahVal = jumlahVal.replace(/[^0-9.]/g, "");
			jumlahVal = parseFloat(jumlahVal);
			
			totalGram = totalGram + jumlahVal;
		}
		
		totalGram = parseFloat(totalGram);
		totalGram = totalGram.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		totalGram = totalGram.substring(0, totalGram.length - 3);
		
		document.getElementById("total_modal").innerHTML = totalGram;
		document.getElementById("total_modal_hidden").value = totalGram;
		document.getElementById("ambil-input_data_5").value = totalGram;
		
		countBayarDua();
	}
	
	function countTotalDetail(){
		totalGram = 0;
		for (var a = 1; a <= rowDetail; a++) {
			jumlahVal = $("#product_price_"+a).val();
			
			if(jumlahVal == '' || jumlahVal == 'NaN'){
				jumlahVal = "0";
			}
			
			jumlahVal = jumlahVal.replace(/[^0-9.]/g, "");
			jumlahVal = parseFloat(jumlahVal);
			
			totalGram = totalGram + jumlahVal;
		}
		
		totalGram = parseFloat(totalGram);
		totalGram = totalGram.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		
		document.getElementById("total_modal").innerHTML = totalGram;
	}
	
	function countTotalGram(){
		totalGram = 0;
		for (var a = 1; a <= rowDetail; a++) {
			jumlahVal = $("#input_modal_1_"+a).val();
			
			if(jumlahVal == '' || jumlahVal == 'NaN'){
				jumlahVal = "0";
			}
			
			jumlahVal = jumlahVal.replace(/[^0-9.]/g, "");
			jumlahVal = parseFloat(jumlahVal);
			
			totalGram = totalGram + jumlahVal;
		}
		
		totalGram = parseFloat(totalGram);
		totalGram = totalGram.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		
		document.getElementById("total_box").innerHTML = totalGram;
	}
	
	function saveTransModal(){
		if(exeTrans == false){
			exeTrans = true;
			
			document.getElementById("error_wrap_modal").setAttribute('style','display:none');
			document.getElementById("btn-save-modal").className += " loading";
			
			var webUrl = "<?php echo base_url()?>";
			
			$.ajax({
				url: $('#form_modal').attr('action')+'/'+rowDetail,
				type: 'post',
				data: $('#form_modal').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						document.getElementById("myModal").innerHTML = "";
						document.getElementById("myModal").className += " mini";
						document.getElementById("myModal").innerHTML = response.message;
						$('.ui.modal').modal('show');
						window.setTimeout(function(){
							$('.ui.modal').modal('hide');
							filterTrans();
						}, 2000);
						
						exeTrans = false;
					}else{
						document.getElementById("error_wrap_modal").innerHTML = response.inputerror;
						document.getElementById("error_wrap_modal").setAttribute('style','');
						
						exeTrans = false;
						document.getElementById("btn-save-modal").classList.remove("loading");
						filterTrans();
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
	
	function saveTransModalBox(){
		if(exeTrans == false){
			exeTrans = true;
			
			document.getElementById("error_wrap_modal").setAttribute('style','display:none');
			document.getElementById("btn-save-modal").className += " loading";
			
			var webUrl = "<?php echo base_url()?>";
			
			$.ajax({
				url: $('#form_modal').attr('action')+'/'+rowDetail,
				type: 'post',
				data: $('#form_modal').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						//document.getElementById("myModal").innerHTML = "";
						//document.getElementById("myModal").className += " mini";
						//document.getElementById("myModal").innerHTML = response.message;
						//$('.ui.modal').modal('show');
						/*window.setTimeout(function(){
							$('.ui.modal').modal('hide');
							masukBox();
						}, 2000);*/
						
						for(var a=0;a<response.box.length;a++){
							var aidi = a+1;
							$("#box_"+aidi).append(response.box[a]);
						}
						
						masukBox();
						$("#error_wrap_modal").removeClass('red');
						$("#error_wrap_modal").addClass('green');
						$("#error_wrap_modal").html('Pesanan Berhasil Masuk Box');
						document.getElementById("error_wrap_modal").setAttribute('style','');
						document.getElementById("btn-save-modal").setAttribute('style','display:none');
						exeTrans = false;
					}else{
						document.getElementById("error_wrap_modal").innerHTML = response.inputerror;
						document.getElementById("error_wrap_modal").setAttribute('style','');
						
						exeTrans = false;
						document.getElementById("btn-save-modal").classList.remove("loading");
						//filterTrans();
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
	
	function saveTransModalAmbil(){
		if(exeTrans == false){
			exeTrans = true;
			
			document.getElementById("error_wrap_modal").setAttribute('style','display:none');
			document.getElementById("btn-save-modal").className += " loading";
			
			var webUrl = "<?php echo base_url()?>";
			
			$.ajax({
				url: $('#form_modal').attr('action')+'/'+rowDetail,
				type: 'post',
				data: $('#form_modal').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						document.getElementById("myModal").innerHTML = "";
						document.getElementById("myModal").className += " mini";
						document.getElementById("myModal").innerHTML = response.message;
						$('.ui.modal').modal('show');
						window.setTimeout(function(){
							$('.ui.modal').modal('hide');
							ambilPesanan();
						}, 2000);
						
						exeTrans = false;
					}else{
						document.getElementById("error_wrap_modal").innerHTML = response.inputerror;
						document.getElementById("error_wrap_modal").setAttribute('style','');
						
						exeTrans = false;
						document.getElementById("btn-save-modal").classList.remove("loading");
						ambilPesanan();
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
	
	function saveTrans(printFlag){
		if(exeTrans == false){
			exeTrans = true;
			document.getElementById("btn-print").setAttribute('style','display:none');
			document.getElementById("btn-save").setAttribute('style','display:none');
			document.getElementById("exe_loading").setAttribute('style','');
			
			$.ajax({
				url: $('#form_modal').attr('action')+'/'+rowDetail+'/'+printFlag,
				type: 'post',
				data: $('#form_modal').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						exeTrans = false;
						exePembayaran = false;
						$('#myModal').modal('hide');

						swal({
							title: "Sukses Input Penjualan Pesanan!",
							text: response.id_trans,
							type: "success",
						});
						
						filterTrans();
					}else{
						swal({
							html:true,
							type: "error",
							title: "",
							text: response.pesan_error,
							timer: 2000,
							showConfirmButton: false
						});
						
						exeTrans = false;
						document.getElementById("btn-print").setAttribute('style','');
						document.getElementById("btn-save").setAttribute('style','');
						document.getElementById("exe_loading").setAttribute('style','display:none');
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
	
	function saveEditTrans(){
		if(exeTrans == false){
			exeTrans = true;
			
			document.getElementById("error_wrap_modal").setAttribute('style','display:none');
			document.getElementById("btn-save-modal").className += " loading";
			
			var webUrl = "<?php echo base_url()?>";
			
			$.ajax({
				url: $('#form_modal').attr('action')+'/'+rowDetail,
				type: 'post',
				data: $('#form_modal').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						document.getElementById("myModal").innerHTML = "";
						document.getElementById("myModal").className += " mini";
						document.getElementById("myModal").innerHTML = response.message;
						$('.ui.modal').modal('show');
						window.setTimeout(function(){
							$('.ui.modal').modal('hide');
							filterTrans();
						}, 2000);
						
						exeTrans = false;
					}else{
						document.getElementById("error_wrap_modal").innerHTML = response.inputerror;
						document.getElementById("error_wrap_modal").setAttribute('style','');
						
						exeTrans = false;
						document.getElementById("btn-save-modal").classList.remove("loading");
						filterTrans();
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
	
	function saveTambahKurangUMP(){
		if(exeTrans == false){
			exeTrans = true;
			
			document.getElementById("error_modal").setAttribute('style','display:none');
			document.getElementById("btn_save_plus_min").className += " loading";
			
			$.ajax({
				url: $('#form_tambah_kurang').attr('action'),
				type: 'post',
				data: $('#form_tambah_kurang').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						document.getElementById("myModal").innerHTML = "";
						document.getElementById("myModal").className += " mini";
						document.getElementById("myModal").innerHTML = response.message;
						$('.ui.modal').modal('show');
						window.setTimeout(function(){
							$('.ui.modal').modal('hide');
							filterTrans();
						}, 2000);
						
						exeTrans = false;
					}else{
						document.getElementById("error_modal").innerHTML = response.inputerror;
						document.getElementById("error_modal").setAttribute('style','');
						
						exeTrans = false;
						document.getElementById("btn_save_plus_min").classList.remove("loading");
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
	
	function batalPesanan(idPesanan){
		var view= '<div class="header">Hapus Transaksi</div><div class="content"><p>Anda Ingin Membatalkan Pesanan Tersebut?</p></div><div class="actions"><div class="ui negative button">Tidak </div><button id="btn_confirm" class="ui green right labeled icon button" onclick=exeBatalPesanan("'+idPesanan+'")>Ya<i class="check circle icon"></i></button></div>';
		
		document.getElementById("myModal").innerHTML = view;
		
		$('.ui.modal').modal({closable: false}).modal('show');
	}
	
	function stepBackPesanan(idPesanan){
		var view= '<div class="header">Mundur Satu Step</div><div class="content"><p>Anda Ingin Memundurkan Satu Step Pesanan Tersebut?</p></div><div class="actions"><div class="ui negative button">Tidak </div><button id="btn_confirm" class="ui green right labeled icon button" onclick=exeStepBackPesanan("'+idPesanan+'")>Ya<i class="check circle icon"></i></button></div>';
		
		document.getElementById("myModal").innerHTML = view;
		
		$('.ui.modal').modal({closable: false}).modal('show');
	}
	
	function exeBatalPesanan(idPesanan){
		var webUrl = "<?php echo base_url()?>";
		document.getElementById("btn_confirm").className += " loading";
		
		$.ajax({
			url : webUrl+'/index.php/C_pesanan_pos/batal_pesanan/'+idPesanan,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					document.getElementById("myModal").innerHTML = response.message;
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
	
	function exeStepBackPesanan(idPesanan){
		var webUrl = "<?php echo base_url()?>";
		document.getElementById("btn_confirm").className += " loading";
		
		$.ajax({
			url : webUrl+'/index.php/C_pesanan_pos/stepback_pesanan/'+idPesanan,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					document.getElementById("myModal").innerHTML = response.message;
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
	
	function bayarToCurrency(idBayar){
		var x = event.keyCode;
		
		jumlahVal = $("#"+idBayar).val();
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
			
			document.getElementById(idBayar).value = beforeComma;
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			
			document.getElementById(idBayar).value = jumlahVal;
		}
		
		countBayarDua();
	}
	
	function countBayarDua(){
		totalBelanja = $("#total_modal").html();
		if(totalBelanja == ''){
			totalBelanja = '0';
		}
		
		totalBelanja = totalBelanja.replace(/[^0-9.]/g, "");
		totalBelanja = parseFloat(totalBelanja);
		
		jumlahBayarSatu = $("#ambil-input_data_5").val();
		if(jumlahBayarSatu == ''){
			jumlahBayarSatu = '0';
		}
		
		jumlahBayarSatu = jumlahBayarSatu.replace(/[^0-9.]/g, "");
		jumlahBayarSatu = parseFloat(jumlahBayarSatu);
		
		jumlahBayarDua = totalBelanja - jumlahBayarSatu;
		jumlahBayarDua = parseFloat(jumlahBayarDua);
		jumlahBayarDua = jumlahBayarDua.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		
		if(jumlahBayarDua.substr(jumlahBayarDua.length - 3) == '.00'){
			jumlahBayarDua = jumlahBayarDua.substring(0, jumlahBayarDua.length - 3);
		}
		
		document.getElementById("ambil-input_data_6").value = jumlahBayarDua;
	}
	
	window.onload = function() {
		
	};
</script>
</html>

