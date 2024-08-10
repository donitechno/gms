<!DOCTYPE html>
<html lang="en">

<head>
	<?php $this->load->view('V_link_pos') ?>
</head>
<style>
	.ui.card>.content>.header, .ui.cards>.card>.content>.header, h1, h2, h3, h4, h5, .ui.steps .step .title{
		font-family:"SamsungOne";
	}

	.ui.card .meta, .ui.cards>.card .meta{
		color:#c0392b !important;
		font-weight:bold;
	}
	
	#wrap_filter {
		height: 240px;
		overflow: hidden;
		overflow-y: auto;
		padding:0;
		border:1px solid rgba(34,36,38,.15);
	}
	
	#wrap_step_2{
		margin:0;
		border:1px solid rgba(34,36,38,.15);
	}
	
	#table-pos1{
		border:none;
	}
	
	#table-pos1 th{
		padding:0.2em 0.7em;
		font-size:18px;
		border:none;
		border-bottom:2px solid #c0392b;
		background:none;
	}
	
	#table-pos1 td{
		padding:0.2em 0.7em;
		font-size:19px;
		font-weight:600;
	}
	
	.form-pos{
		padding:5px 8px !important;
		font-size:1.2em !important;
		font-family:"Segoe UI";
	}
	
	#total_jual{
		font-size:2em;
		font-weight:700;
	}
	
	#modal-table td{
		font-size:0.9em;
		padding: 0.1em 0.78571429em;
	}
	
	.pilih{
		padding:.4em 1em !important;
	}
	
	#pos_data_tabel{
		border-radius:0;
		border:none;
		border-bottom:1px solid rgba(34,36,38,.15);
		
	}
	
	#pos_data_tabel th{
		border-radius:0;
		background:#c0392b;
		color:#FFF;
	}
	
	.angka-bayar, .selectize-control.single .selectize-input, .selectize-dropdown.single{
		padding:5px 8px !important;
		font-size:1.2em !important;
		font-family:"Segoe UI";
	}
</style>
<body onkeyup=entToBayar()>
	<?php $this->load->view('V_header_pos') ?>
	<form id="form_transaction" class="ui form" action="<?php echo base_url() ?>index.php/C_jual/save_jual" method="post">
	<div class="ui container">
		<div class="ui active inverted dimmer" id="form-loader" style="display:none">
			<div class="ui medium text loader">Loading</div>
		</div>
		<div class="ui grid">
			<div class="sixteen wide centered column bg-form" style="margin-top:15px;margin-bottom:15px;">
				<div class="ui grid">
					<div class="four wide column">
						<div class="ui card">
							<div class="content" style="padding-bottom:0">
								<img class="right floated mini ui image" src="<?php echo base_url() ?>assets/pp/<?php echo $this->session->userdata("gold_pp") ?>">
								<div class="header"><?php echo $this->session->userdata("gold_nama_user") ?></div>
								<div class="meta">Kasir Emas</div>
							</div>
							<div class="content">
								<div class="fields" style="margin:0 -.5em">
									<div class="sixteen wide field">
										<label style="color:#c0392b">
											<i class="calendar alternate outline icon"></i> Tanggal Aktif
										</label>
										<input class="form-pos center aligned" name="tanggal_aktif" id="tanggal_aktif" style="margin-top:5px; background:#c0392b; color:#FFF" type="text" value="<?php echo $tanggal_aktif?>" readonly>
										
									</div>
								</div>
							</div>						
						</div>
					</div>
					<div class="eight wide column">
						<div class="ui piled segment center aligned" style="margin-bottom:1em">
							<h3>PENJUALAN EMAS RETAIL</h3>
						</div>
						<div class="ui grid" id="step_one">
							<div class="sixteen wide centered column center aligned">
								<div class="ui tiny ordered steps">
									<div class="active step" id="step_satu">
										<div class="content">
											<div class="title" style="text-align:left">Input Data</div>
											<div class="description" style="text-align:left">Masukkan Data Penjualan</div>
										</div>
									</div>
									<div class="step"  id="step_dua">
										<div class="content">
											<div class="title" style="text-align:left">Pembayaran</div>
											<div class="description" style="text-align:left">Masukkan Data Pembayaran</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="four wide column">
						<table id="table-pos1" class="ui striped table">
							<thead>
								<tr>
									<th colspan="2" class="center aligned">Harga Jual Emas</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($karat as $k){
									$sell = $harga_emas * $k->kadar_jual / 100;
									$sell = $sell / 1000;
									$sell = ceil($sell);
									$sell = $sell * 1000;
								?>
								<tr>
									<td class="center aligned"><?php echo $k->karat_name ?></td>
									<td class="right aligned"><?php echo number_format($sell,0,".",",") ?></td>
									
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="ui grid" id="wrap_step_1" style="margin-top:0">
			<div class="sixteen wide centered column" id="wrap_filter">
				<table id="pos_data_tabel" class="ui celled table" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th style="width:20px">No</th>
							<th>ID Barang</th>
							<th style="width:70px;">Box</th>
							<th style="width:220px;">Nama Barang</th>
							<th style="width:220px;">Keterangan</th>
							<th style="width:80px;">Karat</th>
							<th style="width:100px;">Berat</th>
							<th>Harga</th>
						</tr>
					</thead>
					<tbody id="pos_body">
						<tr id="pos_tr_1">
							<td class="center aligned">1</td>
							<td>
								<input class="form-pos" type="text" onblur=getProduct("1") onkeydown=entToTab("1","1") name="id_product_1" id="input_1_1" autocomplete="off">
							</td>
							<td>
								<input class="form-pos" type="text" onkeydown=entToTab("1","2") name="id_box_1" id="input_1_2" readonly>
							</td>
							<td>
								<input class="form-pos" type="text" onkeydown=entToTab("1","3") name="product_name_1" id="input_1_3" readonly>
							</td>
							<td>
								<input class="form-pos" type="text" onkeydown=entToTab("1","4") name="product_desc_1" id="input_1_4" autocomplete="off">
							</td>
							<td>
								<input class="form-pos" type="text" onkeydown=entToTab("1","5") name="id_karat_1" id="input_1_5" readonly>
							</td>
							<td>
								<input class="form-pos" type="text" style="background:#FFF"  onkeydown=entToTab("1","6") name="product_weight_1" id="input_1_6" readonly>
							</td>
							<td>
								<input class="form-pos" type="text" onblur=priceToCurrency("1") onkeyup=countTotal() onkeydown=entToTab("1","7") name="product_price_1" id="input_1_7" autocomplete="off">
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="ui grid tall stacked segment" style="margin:0">
				<div class="eight wide column">
					<div class="ui grid" style="padding-top:15px;padding-bottom:15px">
						<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">INSERT</div>
						<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: TAMBAH BARIS</div>
						<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">HOME</div>
						<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: HAPUS BARIS</div>
						<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">PgDn</div>
						<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: STEP SELANJUTNYA</div>
						<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">F4</div>
						<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: LIHAT TRANSAKSI</div>
						<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">F5</div>
						<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: REFRESH HALAMAN</div>
						<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">Esc</div>
						<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: HOME</div>
					</div>
				</div>
				<div class="eight wide column">
					<div class="ui grid" style="padding-top:15px;padding-bottom:15px">
						<div class="eight wide column ket-bawah right aligned" style="padding-bottom:0;padding-top:0">TOTAL (Gram)</div>
						<div class="eight wide column ket-bawah right aligned" id="total_jual" style="padding-bottom:0;padding-top:0"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="ui grid" id="wrap_step_2" onkeyup=entToSave()>
			
		</div>
	</div>
	<div class="ui modal" id="myModal"></div>
	</form>
</body>

<script>
	var exeInsert = true;
	var exeTrans = false;
	var exeTambahBaris = false;
	var exePembayaran = false;
	var rowData = 1;
	var tanggalKini = '<?php echo $tanggal_kini ?>';
	var tanggalDO = '<?php echo $tanggal_do ?>';
	var maxData = 5;
	
	function entToBayar(){
		var x = event.keyCode;
		if(exeInsert == true){
			if(x == 45){
				tambahBaris();
			}
			
			if(x == 36 && rowData != 1){
				kurangBaris();
			}
			
			if(x == 34){
				kePembayaran();
			}
			
			if(x == 115){
				lihatTransaksi();
			}
		}else{
			if(x == 33){
				keInputJual();
			}
		}
		
		if(x == 27){
			var webUrl = "<?php echo base_url()?>index.php/C_home_pos";
			window.location= webUrl;
		}
	}
	
	function entToSave(){
		var x = event.keyCode;
		
		if(x == 119){
			saveTrans('P');
		}
		
		if(x == 113){
			saveTrans('NP');
		}
		
		if(x == 27){
			closeModal();
		}
	}
	
	function entToTab(idRow,idElement){
		var x = event.keyCode;
		idElement = parseFloat(idElement);
		if(x == 13){
			idElement = idElement + 1;
			if(idElement != 8){
				if(idElement != 7){
					document.getElementById("input_"+idRow+"_"+idElement+"").focus();
				}else{
					document.getElementById("input_"+idRow+"_"+idElement+"").select();
				}
			}
		}else if(x == 37){
			idElement = idElement - 1;
			if(idElement != 0){
				if(idElement != 7){
					document.getElementById("input_"+idRow+"_"+idElement+"").focus();
				}else{
					document.getElementById("input_"+idRow+"_"+idElement+"").select();
				}
			}
		}
	}
	
	function entToTabBayar(idRow){
		var x = event.keyCode;
		if(x == 13){
			if(idRow == 7){
				document.getElementById("input_data_"+idRow).focus();
			}else{
				document.getElementById("input_data_"+idRow).select();
			}
			
			/*if(idRow == 3){
				findCustomer();
			}*/
		}
	}
	
	function getProduct(idRow){
		var webUrl = "<?php echo base_url()?>";
		var sellDate = $("#tanggal_aktif").val();
		var productID = $("#input_"+idRow+"_1").val();
		if(productID == '' || productID == null){
			productID = '_';
		}
		
		if(productID != '_' && productID != ''){
			$.ajax({
				url : webUrl+'/index.php/C_jual/get_product_from/'+sellDate+'/'+productID+'/'+idRow,
				type: 'post',
				data: $('#form_transaction').serialize(),
				dataType: "JSON",
				success: function(response){
					if(response.success == true){
						if(response.found == 'single'){
							document.getElementById('input_'+idRow+'_1').value=response.id;
							document.getElementById('input_'+idRow+'_2').value=response.box_barang;
							document.getElementById('input_'+idRow+'_3').value=response.nama_barang;
							document.getElementById('input_'+idRow+'_4').value=response.nama_barang;
							document.getElementById('input_'+idRow+'_5').value=response.karat_barang;
							document.getElementById('input_'+idRow+'_6').value=response.berat_barang;
							document.getElementById('input_'+idRow+'_7').value=response.harga_jual;
							
							document.getElementById('input_'+idRow+'_2').select();
							countTotal();
						}else{
							document.getElementById("myModal").classList.remove("mini");
							$("#myModal").html(response.view);
							
							$('#modal-table').DataTable({
								"bPaginate": true,
								"bLengthChange": false,
								"bInfo": false
							});
							
							$('.ui.modal')
							.modal({
							closable: false
							}).modal('show');
						}
					}else{
						window.setTimeout(function(){
							document.getElementById("myModal").classList.remove("mini");
							$("#myModal").html(response.view);
							
							$('#modal-table').DataTable({
								"bPaginate": false,
								"bInfo": false,
								"searching": false
							});
							
							$('.ui.modal')
							.modal({
							
							}).modal('show');
							
							//resetVal();
						}, 500);
					}
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert('System Error!');
				}
			});
		}
	}
	
	function setProduct(productID,idRow){
		$('#myModal').modal('hide');
		
		var webUrl = "<?php echo base_url()?>";
		var sellDate = $("#tanggal_aktif").val();
		
		$.ajax({
			url : webUrl+'/index.php/C_jual/get_product_from/'+sellDate+'/'+productID+'/'+idRow,
			type: 'post',
			data: $('#form_transaction').serialize(),
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					if(response.found == 'single'){
						document.getElementById('input_'+idRow+'_1').value=response.id;
						document.getElementById('input_'+idRow+'_2').value=response.box_barang;
						document.getElementById('input_'+idRow+'_3').value=response.nama_barang;
						document.getElementById('input_'+idRow+'_4').value=response.nama_barang;
						document.getElementById('input_'+idRow+'_5').value=response.karat_barang;
						document.getElementById('input_'+idRow+'_6').value=response.berat_barang;
						document.getElementById('input_'+idRow+'_7').value=response.harga_jual;
						
						document.getElementById('input_'+idRow+'_2').select();
						countTotal();
					}else{
						document.getElementById("myModal").classList.remove("mini");
						$("#myModal").html(response.view);
						
						$('#modal-table').DataTable({
							"bPaginate": true,
							"bLengthChange": false,
							"bInfo": false
						});
						
						$('.ui.modal')
						.modal({
						closable: false
						}).modal('show');
					}
				}else{
					window.setTimeout(function(){
						document.getElementById("myModal").classList.remove("mini");
						$("#myModal").html(response.view);
						
						$('#modal-table').DataTable({
							"bPaginate": false,
							"bInfo": false,
							"searching": false
						});
						
						$('.ui.modal')
						.modal({
						
						}).modal('show');
						
						//resetVal();
					}, 500);
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('System Error!');
			}
		});
	}
	
	function priceToCurrency(idRow){
		var x = event.keyCode;
		
		
		jumlahVal = $("#input_"+idRow+"_7").val();
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
			
			document.getElementById("input_"+idRow+"_7").value = beforeComma;
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById("input_"+idRow+"_7").value = jumlahVal;
		}
		
		countTotal();
		
		
		/*
		if(x == 219){
			tambahBaris();
		}
		
		if(x == 221 && rowData != 1){
			kurangBaris();
		}
		
		if(x == 34){
			kePembayaran();
		}
		*/
		
	}
	
	function tambahBaris(){
		if(exeTambahBaris == false){
			exeTambahBaris = true;
			
			if(rowData == maxData){
				document.getElementById("myModal").className += " mini";
			
				var view = '<i class="close icon"></i><div class="header">Warning</div><div class="content"><div class="ui icon header center aligned"><i class="exclamation triangle red icon"></i>Maksimal Hanya '+maxData+' Data</div></div></div>';
				
				$("#myModal").html(view);
				
				$('.ui.modal').modal('show');
				
				exeTambahBaris = false;
			}else{
				var webUrl = "<?php echo base_url()?>";
				
				$.ajax({
					url : webUrl+'/index.php/C_jual/tambah_baris/'+rowData,
					type: 'post',
					data: $('#form_transaction').serialize(),
					dataType: 'json',
					success: function(response){
						if(response.success == true){
							$("#pos_body").append(response.view);
							document.getElementById("input_"+rowData+"_1").setAttribute('readonly','readonly');
							document.getElementById("input_"+rowData+"_4").setAttribute('readonly','readonly');
							//document.getElementById("input_"+rowData+"_7").setAttribute('readonly','readonly');
							document.getElementById("input_"+rowData+"_1").removeAttribute('onblur');
							//document.getElementById("input_"+rowData+"_7").removeAttribute('onkeyup');
							rowData = rowData + 1;
							document.getElementById("input_"+rowData+"_1").focus();
							exeTambahBaris = false;
							
							countTotal();
						}else{
							document.getElementById("myModal").className += " mini";
							$("#myModal").html(response.inputerror);
							$('.ui.modal').modal('show');
							
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
			document.getElementById("input_"+rowData+"_1").removeAttribute('readonly');
			document.getElementById("input_"+rowData+"_4").removeAttribute('readonly');
			document.getElementById("input_"+rowData+"_7").removeAttribute('readonly');
			document.getElementById("input_"+rowData+"_1").setAttribute('onblur','getProduct("'+rowData+'")');
			document.getElementById("input_"+rowData+"_7").setAttribute('onblur','priceToCurrency("'+rowData+'")');
			document.getElementById("input_"+rowData+"_7").setAttribute('onkeyup','countTotal()');
			document.getElementById("input_"+rowData+"_1").focus();
			exeTambahBaris = false;
			
			countTotal();
		}
	}
	
	function countTotal(){
		totalJual = 0;
		for (var a = 1; a <= rowData; a++) {
			jumlahVal = $("#input_"+a+"_7").val();
			
			if(jumlahVal == '' || jumlahVal == 'NaN'){
				jumlahVal = "0";
			}
			
			jumlahVal = jumlahVal.replace(/[^0-9.]/g, "");
			jumlahVal = parseFloat(jumlahVal);
			
			totalJual = totalJual + jumlahVal;
		}
		
		totalJual = parseFloat(totalJual);
		totalJual = totalJual.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		
		if(totalJual.substr(totalJual.length - 3) == '.00'){
			totalJual = totalJual.substring(0, totalJual.length - 3);
		}
		
		document.getElementById("total_jual").innerHTML = totalJual;
	}
	
	function kePembayaran(){
		if(exePembayaran == false){
			exeInsert = false;
			exePembayaran = true;
			
			document.getElementById("form-loader").setAttribute('style','');
			var webUrl = "<?php echo base_url()?>";
			
			$.ajax({
				url : webUrl+'/index.php/C_jual/ke_pembayaran/'+rowData,
				type: 'post',
				data: $('#form_transaction').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						document.getElementById("wrap_step_1").setAttribute('style','display:none');
						$("#wrap_step_2").html(response.view);
						document.getElementById("wrap_step_2").setAttribute('style','');
						
						$('#input_data_1').selectize({
							onChange: function(value) {
								entToTabBayar(2);
							}
						});
							
						window.setTimeout(function(){
							document.getElementById("input_data_1-selectized").focus();
						}, 500);
						
						document.getElementById("step_satu").className += " completed";
						document.getElementById("step_satu").classList.remove("active");
						document.getElementById("step_dua").className += " active";
						
						document.getElementById("form-loader").setAttribute('style','display:none');
					}else{
						document.getElementById("myModal").className += " mini";
						$("#myModal").html(response.inputerror);
						$('.ui.modal').modal('show');
						
						document.getElementById("form-loader").setAttribute('style','display:none');
						
						exeInsert = true;
						exePembayaran = false;
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
	
	function keInputJual(){
		document.getElementById("form-loader").setAttribute('style','');
		document.getElementById("wrap_step_1").setAttribute('style','');
		document.getElementById("wrap_step_2").setAttribute('style','display:none');
		
		document.getElementById("step_satu").className += " active";
		document.getElementById("step_satu").classList.remove("completed");
		document.getElementById("step_dua").classList.remove("active");
		
		document.getElementById("form-loader").setAttribute('style','display:none');
		
		exeInsert = true;
		exePembayaran = false
		
		window.setTimeout(function(){
			//document.getElementById("input_"+rowData+"_1").focus();
			document.getElementById("tombol_modal").focus();
		}, 500);
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
			
			document.getElementById(idBayar).value = beforeComma+'.'+afterComma;
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById(idBayar).value = jumlahVal;
		}
		
		countBayarDua();
	}
	
	function countBayarDua(){
		totalBelanja = $("#total_price").val();
		if(totalBelanja == ''){
			totalBelanja = '0';
		}
		
		totalBelanja = totalBelanja.replace(/[^0-9.]/g, "");
		totalBelanja = parseFloat(totalBelanja);
		
		jumlahBayarSatu = $("#input_data_5").val();
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
		
		document.getElementById("input_data_6").value = jumlahBayarDua;
	}
	
	function findCustomer(){
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_jual/find_customer/',
			type: 'post',
			data: $('#form_transaction').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					document.getElementById('input_data_2').value=response.customer_phone;
					document.getElementById('input_data_3').value=response.customer_address;
					document.getElementById('input_data_4').value=response.customer_name;
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
	
	function closeModal(){
		$('#myModal').modal('hide');
		exePembayaran = false;
		exeInsert = true;
		document.getElementById("input_"+rowData+"_1").focus();
	}
	
	function saveTrans(printFlag){
		if(exeTrans == false){
			exeTrans = true;
			document.getElementById("form-loader").setAttribute('style','');
			
			$.ajax({
				url: $('#form_transaction').attr('action')+'/'+rowData+'/'+printFlag,
				type: 'post',
				data: $('#form_transaction').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						exeTrans = false;
						exePembayaran = false;
						
						var view = '<div class="header">Berhasil</div><div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>'+response.id_trans+'</div><div class="ui positive message" style="text-align:center"><div class="header">Berhasil Input Penjualan</div><button id="tombol_modal" class="ui positive button" style="margin-top:15px" onclick=tutupModal()>OK</button></div></div></div>';
						
						document.getElementById("myModal").className += " mini";
						$("#myModal").html(view);
						$('.ui.modal').modal('show');
						
						document.getElementById("form-loader").setAttribute('style','display:none');
						document.getElementById("tombol_modal").focus();
						
						window.setTimeout(function(){
							//keInputJual();
							resetVal();
						}, 1000);
						
					}else{
						document.getElementById("myModal").className += " mini";
						$("#myModal").html(response.inputerror);
						$('.ui.modal').modal('show');
						
						document.getElementById("form-loader").setAttribute('style','display:none');
						
						exeTrans = false;
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
	
	function resetVal(){
		//$('.ui.modal').modal('hide');
		
		if(rowData > 1){
			for (var a = 2; a <= rowData; a++) {
				$("#pos_tr_"+a).remove();
			}
		}
				
		rowData = 1;
		document.getElementById("input_"+rowData+"_1").removeAttribute('readonly');
		document.getElementById("input_"+rowData+"_4").removeAttribute('readonly');
		document.getElementById("input_"+rowData+"_7").removeAttribute('readonly');
		document.getElementById("input_"+rowData+"_1").setAttribute('onblur','getProduct("'+rowData+'")');
		document.getElementById("input_"+rowData+"_7").setAttribute('onblur','priceToCurrency("'+rowData+'")');
		document.getElementById("input_"+rowData+"_7").setAttribute('onkeyup','countTotal()');
		//document.getElementById("input_"+rowData+"_1").focus();
		document.getElementById("tombol_modal").focus();
		
		document.getElementById("input_"+rowData+"_1").value = '';
		document.getElementById("input_"+rowData+"_2").value = '';
		document.getElementById("input_"+rowData+"_3").value = '';
		document.getElementById("input_"+rowData+"_4").value = '';
		document.getElementById("input_"+rowData+"_5").value = '';
		document.getElementById("input_"+rowData+"_6").value = '';
		document.getElementById("input_"+rowData+"_7").value = '';
		exeTambahBaris = false;
		exeInsert = true;
		
		keInputJual();
		
		window.setTimeout(function(){
			cekTanggal();
		}, 1000);
	}
	
	function tutupModal(){
		$('.ui.modal').modal('hide');
		window.setTimeout(function(){
			document.getElementById("input_"+rowData+"_1").focus();
		}, 700);
	}
	
	function cekTanggal(){
		if(tanggalKini != tanggalDO){
			document.getElementById("myModal").className += " mini";
			
			var view = '<i class="close icon"></i><div class="header">Warning</div><div class="content"><div class="ui icon header center aligned"><i class="exclamation triangle red icon"></i>Transaksi Berbeda Tanggal</div><div class="ui negative message" style="text-align:center"><div class="header">Tanggal Daily Open Berbeda Dengan Tanggal Hari Ini</div></div></div></div>';
			
			$("#myModal").html(view);
			
			$('.ui.modal').modal('show');
		}
		
		countTotal();
	}
	
	function lihatTransaksi(){
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_jual/lihat_transaksi/',
			type: 'post',
			data: $('#form_transaction').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					document.getElementById("myModal").classList.remove("mini");
					$("#myModal").html(response.view);
						$('#myModal').modal({backdrop: 'static', keyboard: false});
						$('#myModal').modal('show');
				}else{
					alert('Error Lihat Transaksi!');
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
	
	function getProductJual(idMain){
		$('#myModal').modal('hide');
		
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_jual/get_product_jual/'+idMain,
			type: 'post',
			data: $('#form_transaction').serialize(),
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					$("#pos_body").html(response.view);
					$("#total_jual").html(response.total_price);
					exeTrans = true;
					exePembayaran = true;
					exeTambahBaris = true;
					
					rowData = response.row_number;
					document.getElementById("input_"+rowData+"_1").focus();
					
					document.getElementById("myModal").className += " mini";
					
					var view= '<div class="header">Print Ulang Penjualan</div><div class="content"><p>Anda Ingin Mencetak Ulang Surat Penjualan?</p></div><div class="actions"><div class="ui negative button">Tidak </div><button id="btn_confirm" class="ui green right labeled icon button" onclick=printTrans("'+idMain+'")>Ya<i class="checkmark icon"></i></button></div>';
					
					document.getElementById("myModal").innerHTML = view;
		
					$('.ui.modal')
						.modal({
						closable: false
					}).modal('show');
				}else{
					alert('error');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('System Error!');
			}
		});
	}
	
	function printTrans(idMain){
		document.getElementById("btn_confirm").className += " loading";
		
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_jual/print_transaksi/'+idMain,
			type: 'post',
			data: $('#form_transaction').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					exeTrans = false;
					exePembayaran = false;
					
					var view = '<div class="header">Berhasil</div><div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>'+response.trans_id+'</div><div class="ui positive message" style="text-align:center"><div class="header">Berhasil Cetak Ulang Penjualan</div><button id="tombol_modal" class="ui positive button" onclick=refreshPage() style="margin-top:15px">OK</button></div></div></div>';
					
					document.getElementById("myModal").className += " mini";
					$("#myModal").html(view);
					$('.ui.modal').modal('show');
				}else{
					alert('Error Cetak Transaksi!');
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
	
	function refreshPage(){
		var webUrl = "<?php echo base_url()?>";
		window.location= webUrl+'index.php/C_jual';
	}
	
	window.onload = function() {
		window.setTimeout(function(){
			document.getElementById("input_1_1").focus();
			cekTanggal();
		}, 300);
	};
</script>
</html>

