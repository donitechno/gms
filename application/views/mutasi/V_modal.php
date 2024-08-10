<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view('V_link'); ?>
</head>
<style>
	.ui.menu{
		margin-bottom:0 !important;
	}
	
	.ui.form input:not([type]), .ui.form input[type=date], .ui.form input[type=datetime-local], .ui.form input[type=email], .ui.form input[type=file], .ui.form input[type=number], .ui.form input[type=password], .ui.form input[type=search], .ui.form input[type=tel], .ui.form input[type=text], .ui.form input[type=time], .ui.form input[type=url], .ui.form select, .filter-input, .filter-select, .filter-input{
		font-size:0.9em !important;
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
</style>
<body onkeyup="entToAction()">
	<?php $this->load->view("V_header") ?>
	<div class="pusher">
		<div class="ui container">
			<div class="ui grid">
				<div class="fifteen wide centered column center aligned" style="margin-top:15px">
					<div class="ui ordered steps">
						<div class="completed step">
							<div class="content">
								<div class="title" style="text-align:left">Input Pesanan</div>
								<div class="description" style="text-align:left">17 Juli 2018</div>
							</div>
						</div>
						<div class="completed step">
							<div class="content">
								<div class="title" style="text-align:left">Pesanan Masuk Box</div>
								<div class="description" style="text-align:left">20 Juli 2018</div>
							</div>
						</div>
						<div class="active step">
							<div class="content">
								<div class="title" style="text-align:left">Pesanan Diambil</div>
								<div class="description" style="text-align:left">22 Juli 2018</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<form  class="ui form form-javascript" id="form_transaction" action="<?php echo base_url() ?>index.php/C_pesanan/save" method="post">
			<div class="ui grid">
				<div class="fifteen wide centered column" style="margin-top:15px;">
					<div class="ui grid">
						<div class="four wide column">
							<div class="field">
								<label>Data Pelanggan</label>
								<div class="ui left action input">
									<div class="ui icon button">
										<i class="user outline icon"></i>
									</div>
									<input type="text" value="">
								</div>
							</div>
							<div class="field">
								<div class="ui left action input">
									<div class="ui icon button">
										<i class="building outline icon"></i>
									</div>
									<input type="text" value="">
								</div>
							</div>
							<div class="field">
								<div class="ui left action input">
									<div class="ui icon button">
										<i class="phone icon"></i>
									</div>
									<input type="text" value="">
								</div>
							</div>
						</div>
						<div class="four wide column">
							<div class="field">
								<label>Data Pesanan</label>
								<div class="ui left action input">
									<div class="ui icon button">
										PS
									</div>
									<input type="text" value="">
								</div>
							</div>
							<div class="field">
								<div class="ui left action input">
									<div class="ui icon button">
										<i class="money bill alternate outline icon"></i>
									</div>
									<input type="text" value="">
								</div>
							</div>
						</div>
						<div class="right floated four wide column">
							<div class="field">
								<label>Tanggal Masuk Box</label>
								<input type="text" name="tanggal_mutasi" id="tanggal_mutasi" readonly onkeydown=entToHeader("input_1_1") onchange=entToForm("input_1_1")>
							</div>
						</div>
					</div>
				</div>
				<div class="fifteen wide centered column">
				Tabel
				</div>
			</div>
			</form>
		</div>
	</div>
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
		var x = event.keyCode;
		
		if(x == 45){
			tambahBaris();
		}
		
		if(x == 36 && rowData != 1){
			kurangBaris();
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
			
			document.getElementById(idName).value = beforeComma+'.'+afterComma;
			document.getElementById("total_gram").innerHTML = beforeComma+'.'+afterComma;
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
				url : webUrl+'/index.php/C_pesanan/get_master_product/'+categoryID+'/'+idRow,
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
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert('System Error!');
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
					url : webUrl+'/index.php/C_pesanan/tambah_baris/'+rowData,
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
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_pesanan/get_customer_form/',
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
			url : webUrl+'/index.php/C_pesanan/set_customer_form/'+custPhone,
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
			
			document.getElementById("btn-save").className += " loading";
			
			var webUrl = "<?php echo base_url()?>";
			
			$.ajax({
				url: $('#form_transaction').attr('action')+'/'+rowData,
				type: 'post',
				data: $('#form_transaction').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						swal({
							type: "success",
							title: "Berhasil Input Pesanan!",
							text: "",
						});
						
						exeTrans = false;
						
						document.getElementById("btn-save").classList.remove("loading");
						
						resetVal();
					}else{
						if(response.pesan_error != ''){
							swal({
								html:true,
								type: "error",
								title: "",
								text: response.pesan_error,
							});
						}
						
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
	
	function getDetailPesanan(idPesanan){
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_pesanan/get_detail_pesanan/'+idPesanan,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					$("#myModal").html(response.view);
					$('#myModal').modal({backdrop: 'static', keyboard: false});
					
					$('#myModal').modal('show');
					
					rowDetail = response.row_detail;
					
					window.setTimeout(function(){
						document.getElementById("product_weight_1").focus();
					}, 500);
					
					countTotalDetail();
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
	
	function weightToCurrency(rowWeight){
		jumlahVal = $("#product_weight_"+rowWeight).val();
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
			
			document.getElementById("product_weight_"+rowWeight).value = beforeComma+'.'+afterComma;
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById("product_weight_"+rowWeight).value = jumlahVal;
		}
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
			
			document.getElementById("saldo_grosir").value = beforeComma+'.'+afterComma;
			countTotalDetail();
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById("saldo_grosir").value = jumlahVal;
			countTotalDetail();
		}
	}
	
	function jualToCurrency(rowWeight){
		jumlahVal = $("#product_price_"+rowWeight).val();
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
			
			document.getElementById("product_price_"+rowWeight).value = beforeComma+'.'+afterComma;
			countTotalDetail();
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById("product_price_"+rowWeight).value = jumlahVal;
			countTotalDetail();
		}
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
	
	window.onload = function() {
		
	};
</script>
</html>

