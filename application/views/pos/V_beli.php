<!DOCTYPE html>
<html lang="en">

<head>
   <?php $this->load->view('V_link_pos') ?>
</head>
<style>
	.ui.card>.content>.header, .ui.cards>.card>.content>.header, h1, h2, h3, h4, h5, .ui.steps .step .title, .ui.header, .ui.message .header{
		font-family:"SamsungOne";
	}

	.ui.card .meta, .ui.cards>.card .meta{
		color:#2980b9 !important;
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
		border:none;
		border-bottom:2px solid #2980b9;
		background:none;
	}
	
	#table-pos1 td{
		padding:0.2em 0.7em;
		font-weight:600;
	}
	
	#pos_data_tabel th{
		background:#2980b9 !important;
	}
	
	.form-pos, .custom-select{
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
	<form id="form_transaction" class="ui form" action="<?php echo base_url() ?>index.php/C_beli/save_beli" method="post">
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
								<div class="fields" style="margin: 0 -.5em;">
									<div class="sixteen wide field">
										<label style="color:#2980b9">
											<i class="calendar alternate outline icon"></i> Tanggal Aktif
										</label>
										<input class="form-pos center aligned" name="tanggal_aktif" id="tanggal_aktif" style="margin-top:5px; background:#2980b9; color:#FFF" type="text" value="<?php echo $tanggal_aktif?>" readonly>
										
									</div>
								</div>
							</div>						
						</div>
					</div>
					<div class="eight wide column">
						<div class="ui piled segment center aligned" style="margin-bottom:1em">
							<h3>PEMBELIAN EMAS RETAIL</h3>
						</div>
						
					</div>
					<div class="four wide column">
						<div class="ui grid" id="step_one">
							<div class="sixteen wide centered column center aligned">
								<div class="ui tiny ordered vertical steps">
									<div class="active step" id="step_satu">
										<div class="content">
											<div class="title" style="text-align:left">Input Data</div>
											<div class="description" style="text-align:left">Masukkan Data Pembelian</div>
										</div>
									</div>
									<div class="step"  id="step_dua">
										<div class="content">
											<div class="title" style="text-align:left">Verifikasi Pembelian</div>
											<div class="description" style="text-align:left">Periksa Kembali data Pembelian</div>
										</div>
									</div>
								</div>
							</div>
						</div>
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
							<th style="width:80px;">Karat</th>
							<th>Kode Pajangan</th>
							<th style="width:200px;">Keterangan</th>
							<th style="width:120px;">Kelompok</th>
							<th style="width:80px;">Pcs</th>
							<th style="width:100px;">Berat</th>
							<th>Total Harga</th>
							<th>Harga Rata2</th>
						</tr>
					</thead>
					<tbody id="pos_body">
						<tr id="pos_tr_1">
							<td class="center aligned">1</td>
							<td>
								<select class="custom-select" name="id_karat_1" id="input_1_1" onkeydown=entToTab("1","1") onchange=getKaratHdn("1")>
									<option value="">Karat</option>
									<?php foreach($karat as $k){ ?>
									<option value="<?php echo $k->id ?>"><?php echo $k->karat_name ?></option>
									<?php } ?>
								</select>
								<input type="hidden" name="id_karat_hdn_1" id="input_hdn_1_1" value="">
							</td>
							<td>
								<input class="form-pos" type="text" onblur=getProduct("1") onkeydown=entToTab("1","2") name="id_product_1" id="input_1_2" autocomplete="off">
							</td>
							<td>
								<input class="form-pos" type="text" onkeydown=entToTab("1","3") name="product_desc_1" id="input_1_3" autocomplete="off">
							</td>
							<td>
								<select class="custom-select" name="id_category_1" id="input_1_4" onkeydown=entToTab("1","4") onchange=getKelompokHdn("1")>
									<option value="">Kelompok</option>
									<?php foreach($category as $c){ ?>
									<option value="<?php echo $c->id ?>"><?php echo $c->category_name ?></option>
									<?php } ?>
								</select>
								<input type="hidden" name="id_category_hdn_1" id="input_hdn_1_4" value="">
							</td>
							<td>
								<input class="form-pos" type="number" onkeydown=entToTab("1","5") name="product_pcs_1" id="input_1_5" autocomplete="off">
							</td>
							<td>
								<input class="form-pos" type="text" onkeydown=entToTab("1","6") name="product_weight_1" onkeyup=weightToCurrency("1") id="input_1_6" autocomplete="off">
							</td>
							<td>
								<input class="form-pos" type="text" onblur=priceToCurrency("1") onkeyup=countTotal() name="product_price_1" id="input_1_7" onkeydown=entToTab("1","7") autocomplete="off">
							</td>
							<td>
								<input class="form-pos" type="text" name="product_avg_1" id="input_1_8" onkeydown=entToTab("1","8") readonly>
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
						<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">F8</div>
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
	
	function entToTab(idRow,idElement){
		var x = event.keyCode;
		idElement = parseFloat(idElement);
		if(x == 13){
			idElement = idElement + 1;
			if(idElement != 9){
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
	
	function getKaratHdn(idRow){
		var idKarat = $("#input_"+idRow+"_1").val();
		document.getElementById('input_hdn_'+idRow+'_1').value=idKarat;
	}
	
	function getKelompokHdn(idRow){
		var idKarat = $("#input_"+idRow+"_4").val();
		document.getElementById('input_hdn_'+idRow+'_4').value=idKarat;
	}
	
	function getProduct(idRow){
		var webUrl = "<?php echo base_url()?>";
		var buyDate = $("#tanggal_aktif").val();
		var productID = $("#input_"+idRow+"_2").val();
		if(productID == '' || productID == null){
			productID = '_';
		}
		
		if(productID != '_' && productID != ''){
			$.ajax({
				url : webUrl+'/index.php/C_beli/get_product_from/'+buyDate+'/'+productID+'/'+idRow,
				type: 'post',
				data: $('#form_transaction').serialize(),
				dataType: "JSON",
				success: function(response){
					if(response.success == true){
						if(response.found == 'single'){
							document.getElementById('input_'+idRow+'_1').value=response.karat_barang;
							document.getElementById('input_hdn_'+idRow+'_1').value=response.karat_barang;
							document.getElementById('input_'+idRow+'_2').value=response.id;
							document.getElementById('input_'+idRow+'_3').value=response.nama_barang;
							document.getElementById('input_'+idRow+'_4').value=response.id_category;
							document.getElementById('input_'+idRow+'_5').value='1';
							document.getElementById('input_'+idRow+'_6').value=response.berat_barang;
							document.getElementById('input_'+idRow+'_7').value=response.harga_beli;
							
							//document.getElementById("input_"+idRow+"_3").setAttribute('readonly','readonly');
							document.getElementById("input_"+idRow+"_5").setAttribute('readonly','readonly');
							//document.getElementById("input_"+idRow+"_6").setAttribute('readonly','readonly');
							document.getElementById("input_"+idRow+"_3").setAttribute('onkeydown','entToTab("'+idRow+'","4")');
							
							document.getElementById("input_"+idRow+"_1").setAttribute('disabled','true');
							document.getElementById("input_"+idRow+"_4").setAttribute('disabled','true');
							
							document.getElementById('input_'+idRow+'_3').select();
							getKaratHdn(idRow);
							getKelompokHdn(idRow);
							
							countRata(idRow);
							countTotal();
						}else if(response.found == 'not_single'){
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
						}else if(response.found == 'not_found'){
							document.getElementById("input_"+idRow+"_3").removeAttribute('readonly');
							document.getElementById("input_"+idRow+"_5").removeAttribute('readonly');
							document.getElementById("input_"+idRow+"_6").removeAttribute('readonly');
							
							document.getElementById("input_"+idRow+"_1").removeAttribute('disabled');
							document.getElementById("input_"+idRow+"_4").removeAttribute('disabled');
							document.getElementById("input_"+idRow+"_3").setAttribute('onkeydown','entToTab("'+idRow+'","3")');
							
							getKaratHdn(idRow);
							getKelompokHdn(idRow);
							
							document.getElementById('input_'+idRow+'_3').value='Data Tidak Ditemukan';
						}
					}else{
						window.setTimeout(function(){
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
						}, 500);
					}
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert('System Error!');
				}
			});
		}else{
			document.getElementById("input_"+idRow+"_3").removeAttribute('readonly');
			document.getElementById("input_"+idRow+"_5").removeAttribute('readonly');
			document.getElementById("input_"+idRow+"_6").removeAttribute('readonly');
			
			document.getElementById("input_"+idRow+"_1").removeAttribute('disabled');
			document.getElementById("input_"+idRow+"_4").removeAttribute('disabled');
			document.getElementById("input_"+idRow+"_3").setAttribute('onkeydown','entToTab("'+idRow+'","3")');
			
			getKaratHdn(idRow);
			getKelompokHdn(idRow);
			
			//document.getElementById('input_'+idRow+'_3').value='Data Tidak Ditemukan';
		}
	}
	
	function setProduct(productID,idRow){
		$('#myModal').modal('hide');
		
		var webUrl = "<?php echo base_url()?>";
		var buyDate = $("#tanggal_aktif").val();
		
		$.ajax({
			url : webUrl+'/index.php/C_beli/get_product_from/'+buyDate+'/'+productID+'/'+idRow,
			type: 'post',
			data: $('#form_transaction').serialize(),
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					if(response.found == 'single'){
						document.getElementById('input_'+idRow+'_1').value=response.karat_barang;
						document.getElementById('input_hdn_'+idRow+'_1').value=response.karat_barang;
						document.getElementById('input_'+idRow+'_2').value=response.id;
						document.getElementById('input_'+idRow+'_3').value=response.nama_barang;
						document.getElementById('input_'+idRow+'_4').value=response.id_category;
						document.getElementById('input_'+idRow+'_5').value='1';
						document.getElementById('input_'+idRow+'_6').value=response.berat_barang;
						document.getElementById('input_'+idRow+'_7').value=response.harga_beli;
						
						//document.getElementById("input_"+idRow+"_3").setAttribute('readonly','readonly');
						document.getElementById("input_"+idRow+"_5").setAttribute('readonly','readonly');
						//document.getElementById("input_"+idRow+"_6").setAttribute('readonly','readonly');
						document.getElementById("input_"+idRow+"_3").setAttribute('onkeydown','entToTab("'+idRow+'","4")');
						
						document.getElementById("input_"+idRow+"_1").setAttribute('disabled','true');
						document.getElementById("input_"+idRow+"_4").setAttribute('disabled','true');
						
						document.getElementById('input_'+idRow+'_3').select();
						getKaratHdn(idRow);
						getKelompokHdn(idRow);
						
						countRata(idRow);
						countTotal();
					}else if(response.found == 'not_single'){
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
					}else if(response.found == 'not_found'){
						document.getElementById("input_"+idRow+"_3").removeAttribute('readonly');
						document.getElementById("input_"+idRow+"_5").removeAttribute('readonly');
						document.getElementById("input_"+idRow+"_6").removeAttribute('readonly');
						document.getElementById("input_"+idRow+"_3").setAttribute('onkeydown','entToTab("'+idRow+'","3")');
						
						document.getElementById("input_"+idRow+"_1").removeAttribute('disabled');
						document.getElementById("input_"+idRow+"_4").removeAttribute('disabled');
						
						getKaratHdn(idRow);
						getKelompokHdn(idRow);
						
						document.getElementById('input_'+idRow+'_3').value='Data Tidak Ditemukan';
					}
				}else{
					window.setTimeout(function(){
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
					}, 500);
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('System Error!');
			}
		});
	}
	
	function countRata(idRow){
		totalHarga = $("#input_"+idRow+"_7").val();
		beratBarang = $("#input_"+idRow+"_6").val();
		
		if(totalHarga == '' || totalHarga == 'NaN'){
			totalHarga = "0";
		}
		
		totalHarga = totalHarga.replace(/[^0-9.]/g, "");
		totalHarga = parseFloat(totalHarga);
		
		if(beratBarang == '' || beratBarang == 'NaN'){
			beratBarang = "0";
		}
		
		beratBarang = beratBarang.replace(/[^0-9.]/g, "");
		beratBarang = parseFloat(beratBarang);
		
		rataHarga = totalHarga / beratBarang;
		
		rataHarga = parseFloat(rataHarga);
		rataHarga = rataHarga.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		rataHarga = rataHarga.substring(0, rataHarga.length - 3);
		
		document.getElementById("input_"+idRow+"_8").value = rataHarga;
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
		
		document.getElementById("total_jual").innerHTML = totalJual;
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
		
		countRata(idRow)
		countTotal();
	}
	
	function weightToCurrency(idRow){
		var x = event.keyCode;
		
		jumlahVal = $("#input_"+idRow+"_6").val();
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
			
			document.getElementById("input_"+idRow+"_6").value = beforeComma+'.'+afterComma.substring(0,2);
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById("input_"+idRow+"_6").value = jumlahVal;
		}
		
		countTotal();
	}
	
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
			
			if(x == 119){
				lihatTransaksi();
			}
		}else{
			if(x == 33){
				keInputBeli();
			}
		}
		
		if(x == 27){
			var webUrl = "<?php echo base_url()?>index.php/C_home_pos";
			window.location= webUrl;
		}
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
					url : webUrl+'/index.php/c_beli/tambah_baris/'+rowData,
					type: 'post',
					data: $('#form_transaction').serialize(),
					dataType: 'json',
					success: function(response){
						if(response.success == true){
							$("#pos_body").append(response.view);
							document.getElementById("input_"+rowData+"_1").setAttribute('disabled','true');
							document.getElementById("input_"+rowData+"_2").setAttribute('readonly','readonly');
							document.getElementById("input_"+rowData+"_3").setAttribute('readonly','readonly');
							document.getElementById("input_"+rowData+"_4").setAttribute('disabled','true');
							document.getElementById("input_"+rowData+"_5").setAttribute('readonly','readonly');
							document.getElementById("input_"+rowData+"_6").setAttribute('readonly','readonly');
							document.getElementById("input_"+rowData+"_7").setAttribute('readonly','readonly');
							
							document.getElementById("input_"+rowData+"_2").removeAttribute('onblur');
							
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
			
			document.getElementById("input_"+rowData+"_1").removeAttribute('disabled');
			document.getElementById("input_"+rowData+"_2").removeAttribute('readonly');
			document.getElementById("input_"+rowData+"_3").removeAttribute('readonly');
			document.getElementById("input_"+rowData+"_4").removeAttribute('disabled');
			document.getElementById("input_"+rowData+"_5").removeAttribute('readonly');
			document.getElementById("input_"+rowData+"_6").removeAttribute('readonly');
			document.getElementById("input_"+rowData+"_7").removeAttribute('readonly');
			
			document.getElementById("input_"+rowData+"_2").setAttribute('onblur','getProduct("'+rowData+'")');
			document.getElementById("input_"+rowData+"_1").focus();
			exeTambahBaris = false;
			
			countTotal();
		}
	}
	
	function kePembayaran(){
		if(exePembayaran == false){
			exeInsert = false;
			exePembayaran = true;
			
			document.getElementById("form-loader").setAttribute('style','');
			var webUrl = "<?php echo base_url()?>";
			
			$.ajax({
				url : webUrl+'/index.php/C_beli/ke_pembayaran/'+rowData,
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
	
	function entToTabBayar(idRow){
		var x = event.keyCode;
		if(x == 13){
			if(idRow == 5){
				document.getElementById("total_price").focus();
			}else{
				document.getElementById("input_data_"+idRow).select();
			}
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
	}
	
	function closeModal(){
		exeInsert = true;
		exePembayaran = false;
		document.getElementById("input_"+rowData+"_2").focus();
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
						exePembayaran = false;
						
						var view = '<div class="header">Berhasil</div><div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>'+response.trans_id+'</div><div class="ui positive message" style="text-align:center"><div class="header">Berhasil Input Pembelian</div><button id="tombol_modal" class="ui positive button" onclick=tutupModal() style="margin-top:15px">OK</button></div></div></div>';
						
						document.getElementById("myModal").className += " mini";
						$("#myModal").html(view);
						$('.ui.modal').modal('show');
						
						document.getElementById("form-loader").setAttribute('style','display:none');
						//document.getElementById("tombol_modal").focus();
						
						window.setTimeout(function(){
							exeTrans = false;
							resetVal();
						}, 1500);
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
		
		document.getElementById("input_"+rowData+"_1").removeAttribute('disabled');
		document.getElementById("input_"+rowData+"_2").removeAttribute('readonly');
		document.getElementById("input_"+rowData+"_3").removeAttribute('readonly');
		document.getElementById("input_"+rowData+"_4").removeAttribute('disabled');
		document.getElementById("input_"+rowData+"_5").removeAttribute('readonly');
		document.getElementById("input_"+rowData+"_6").removeAttribute('readonly');
		document.getElementById("input_"+rowData+"_7").removeAttribute('readonly');
		
		document.getElementById("input_"+rowData+"_2").setAttribute('onblur','getProduct("'+rowData+'")');
		
		document.getElementById("input_"+rowData+"_1").value = '';
		document.getElementById("input_"+rowData+"_2").value = '';
		document.getElementById("input_"+rowData+"_3").value = '';
		document.getElementById("input_"+rowData+"_4").value = '';
		document.getElementById("input_"+rowData+"_5").value = '';
		document.getElementById("input_"+rowData+"_6").value = '';
		document.getElementById("input_"+rowData+"_7").value = '';
		document.getElementById("input_"+rowData+"_8").value = '';
		
		exeTambahBaris = false;
		exeInsert = true;
		
		keInputBeli();
		
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
	
	function lihatTransaksi(){
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_beli/lihat_transaksi/',
			type: 'post',
			data: $('#form_transaction').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					document.getElementById("myModal").classList.remove("mini");
					$("#myModal").html(response.view);
						$('#myModal').modal({backdrop: 'static', keyboard: false});
						$('#myModal').modal('show');
						
						window.setTimeout(function(){
							document.getElementById("mdl-close").focus();
						}, 500);
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
			url : webUrl+'/index.php/C_beli/get_product_beli/'+idMain,
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
					
					var view= '<div class="header">Print Ulang Pembelian</div><div class="content"><p>Anda Ingin Mencetak Ulang Surat Pembelian?</p></div><div class="actions"><div class="ui negative button">Tidak </div><button id="btn_confirm" class="ui green right labeled icon button" onclick=printTrans("'+idMain+'")>Ya<i class="checkmark icon"></i></button></div>';
					
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
	
	function keInputBeli(){
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
			document.getElementById("tombol_modal").focus();
			//document.getElementById("input_"+rowData+"_1").focus();
		}, 500);
	}
	
	function cekTanggal(){
		if(tanggalKini != tanggalDO){
			document.getElementById("myModal").className += " mini";
			
			var view = '<i class="close icon"></i><div class="header">Warning</div><div class="content"><div class="ui icon header center aligned"><i class="exclamation triangle red icon"></i>Transaksi Berbeda Tanggal</div><div class="ui negative message" style="text-align:center"><div class="header">Tanggal Daily Open Berbeda Dengan Tanggal Hari Ini</div></div></div></div>';
			
			$("#myModal").html(view);
			
			$('.ui.modal').modal('show');
		}
		
		//document.getElementById("input_"+rowData+"_1").focus();
		countTotal();
	}
	
	function printTrans(idMain){
		document.getElementById("btn_confirm").className += " loading";
		
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_beli/print_transaksi/'+idMain,
			type: 'post',
			data: $('#form_transaction').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					exeTrans = false;
					exePembayaran = false;
					
					var view = '<div class="header">Berhasil</div><div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>'+response.trans_id+'</div><div class="ui positive message" style="text-align:center"><div class="header">Berhasil Cetak Ulang Pembelian</div><button id="tombol_modal" class="ui positive button" onclick=refreshPage() style="margin-top:15px">OK</button></div></div></div>';
					
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
		window.location= webUrl+'index.php/C_beli';
	}
	
	function findCustomer(){
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_beli/find_customer/',
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
	
	window.onload = function() {
		window.setTimeout(function(){
			document.getElementById("input_1_1").focus();
			cekTanggal();
		}, 300);
	};
</script>
</html>

