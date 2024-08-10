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
	
	.selectize-input{
		border:0px;
		-webkit-box-shadow:0 0px 0 rgba(0,0,0,0), inset 0 0px 0 rgba(255,255,255,0) !important;
		box-shadow:0 0px 0 rgba(0,0,0,0), inset 0 0px 0 rgba(255,255,255,0) !important;
	}
	
	#pos_data_tabel td{
		padding:0 !important;
	}
	
	#modal-table td{
		font-size:0.9em;
		padding: 0.1em 0.78571429em;
	}
	
	.pilih{
		padding:.4em 1em !important;
	}
</style>
<body onkeyup="entToInsert()">
	<?php $this->load->view("V_header_sidebar") ?>
	<div class="pusher">
		<div class="ui container fluid">
			<div class="ui stackable menu" style="border-radius:0">
				<div class="item">
					<div class="ui breadcrumb">
					  <div class="section"><i class="hdd outline icon"></i> Kontrol Barang Pajangan</div>
					  <i class="right chevron icon divider"></i>
					  <div class="active section">Stock Out</div>
					</div>
				</div>
			</div>
			<div class="ui pointing secondary menu">
				<a class="item active" data-tab="first" style="width:50%">
					<i class="edit icon"></i> Input Data Stock Out
				</a>
				<a class="item" data-tab="second" style="width:50%" onclick=filterTrans()>
					<i class="list ol icon"></i> List Data Stock Out
				</a>
			</div>
			<div class="ui bottom attached tab segment active" data-tab="first">
				<div class="ui inverted dimmer" id="loader_form">
					<div class="ui large text loader">Loading</div>
				</div>
				<form class="ui form form-javascript" id="form_transaction" action="<?php echo base_url() ?>index.php/C_stock_out/save_stock_out" method="post">
				<div class="ui grid">
					<div class="right floated ten wide column" style="padding-bottom:0">
						<div class="fields">
							<div class="ten wide field">
								<label>Alasan Stock Out</label>
								<input type="text" name="alasan_stock_out" id="alasan_stock_out" onkeydown=entToHeader("tanggal_stock_out") autofocus="on" autocomplete="off">
							</div>
							<div class="six wide field">
							  <label>Tanggal Stock Out</label>
								<div id="wrap_tanggal_stock_out">
									<input type="text" name="tanggal_stock_out" id="tanggal_stock_out" readonly onkeydown=entToHeader("input_1_1") onchange=entToForm("input_1_1")>
								</div>
							  </div>
						</div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0">
						<div class="ui red message" id="error_wrap" style="display:none"></div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0">
						<table id="pos_data_tabel" class="ui celled table" cellspacing="0" width="100%" style="margin-top:15px;">
							<thead>
								<tr class="center aligned">
									<th>No</th>
									<th>ID Barang</th>
									<th>Asal</th>
									<th>Kelompok</th>
									<th style="width:260px;">Nama Barang</th>
									<th style="width:80px;">Karat</th>
									<th style="width:80px;">Box</th>
									<th style="width:80px;">Berat</th>
									<th>X</th>
								</tr>
							</thead>
							<tbody id="pos_body">
								<tr id="pos_tr_1">
									<td class="center aligned">1</td>
									<td>
										<input type="text" class="form-pos" name="id_1" id="input_1_1" onkeydown=entToTab("1","1") onblur=getProductForm("1") autocomplete="off" placeholder="Masukkan ID Barang">
									</td>
									<td>
										<input class="form-pos" type="text" name="asal_1" id="input_1_2" onkeydown=entToTab("1","2") readonly>
									</td>
									<td>
										<input class="form-pos" type="text" name="category_1" id="input_1_3" onkeydown=entToTab("1","3") readonly>
									</td>
									<td>
										<input class="form-pos" type="text" name="nama_barang_1" id="input_1_4" onkeydown=entToTab("1","4") readonly>
									</td>
									<td>
										<input class="form-pos" type="text" name="karat_1" id="input_1_5" onkeydown=entToTab("1","5") readonly>
									</td>
									<td>
										<input class="center aligned form-pos" type="text" name="box_1" id="input_1_6" onkeydown=entToTab("1","6") readonly>
									</td>
									<td>
										<input class="form-pos right aligned" type="text" name="berat_1" id="input_1_7" onkeydown=entToTab("1","7") readonly >
									</td>
									<td class="center aligned" id="input_1_8">
										<div class="ui tiny icon google plus button"><i class="ban icon"></i></div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="eight wide column">
						<div class="ui grid" style="padding-top:30px;padding-bottom:15px">
							<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">INSERT</div>
							<div class="twelve wide column ket-bawah" style="padding-bottom:5px;padding-top:0">: SIMPAN DATA</div>
							<div class="four wide column ket-bawah" style="padding-bottom:0;padding-top:0">F5</div>
							<div class="twelve wide column ket-bawah" style="padding-bottom:0;padding-top:0">: REFRESH HALAMAN</div>
						</div>
					</div>
					<div class="eight wide column">
						<div class="ui grid" style="padding-top:30px;padding-bottom:15px">
							<div class="eight wide column ket-bawah right aligned" style="padding-bottom:0;padding-top:0">TOTAL (Gram)</div>
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
				<form class="ui form form-javascript" id="form_filter" action="<?php echo base_url() ?>index.php/C_stock_out/filter_stock_out" method="post">
				<div class="ui grid">
					<div class="sixteen wide column" style="padding-bottom:0">
						<div class="fields">
							<div class="two wide field">
								<label>Kelompok Barang</label>
								<select name="filter_category" id="filter_category">
									<option value="All">-- All --</option>
									<?php foreach($category as $c){ ?>
									<option value="<?php echo $c->id ?>"><?php echo $c->category_name ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="two wide field">
								<label>Asal Barang</label>
								<select name="filter_from" id="filter_from">
									<option value="All">-- All --</option>
									<?php foreach($from_filter as $f){ ?>
									<option value="<?php echo $f->id ?>"><?php echo $f->from_name ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="two wide field">
								<label>Box</label>
								<select name="filter_box" id="filter_box">
									<option value="All">-- All --</option>
									<?php foreach($box as $b){ ?>
									<option value="<?php echo $b->id ?>">BOX <?php echo $b->nama_box ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="two wide field">
								<label>Karat</label>
								<select name="filter_karat" id="filter_karat">
									<option value="All">-- All --</option>
									<?php foreach($karat as $k){ ?>
									<option value="<?php echo $k->id ?>"><?php echo $k->karat_name ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="three wide field">
								<label>Tgl Stock Out</label>
								<input type="text" name="from_stock_out" id="from_stock_out" readonly>
							</div>
							<div class="one wide field" style="text-align:center;margin-top:30px">
								<label>s.d</label>
							</div>
							<div class="three wide field">
								<label style="visibility:hidden">Tgl Stock Out</label>
								<input type="text" name="to_stock_out" id="to_stock_out" readonly>
							</div>
							<div class="one wide field">
								<label style="visibility:hidden">-</label>
								<div class="ui fluid icon green button filter-input" id="btn_filter" onclick="filterTrans()" title="Filter">
									<i class="filter icon"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" id="wrap_filter" style="padding-top:0"></div>
				</div>
				</form>
			</div>	
		</div>
	</div>
	<div class="ui modal" id="myModal"></div>
</body>
<script>
	var exeTrans = false;
	var rowData = 1;
	
	$('select.dropdown').dropdown();
	$('.menu .item').tab();
	
	$('.form-javascript').on('keyup keypress', function(e){
		var keyCode = e.keyCode || e.which;
		if (keyCode === 13) {
			e.preventDefault();
			return false;
		}
	});
	
	$(function(){
		$( "#tanggal_stock_out" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		
		$('#tanggal_stock_out').datepicker('setDate', 'today');
	});
	
	$( function() {
		$( "#from_stock_out" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		$('#from_stock_out').datepicker('setDate', 'today');
		
		$( "#to_stock_out" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		$('#to_stock_out').datepicker('setDate', 'today');
	} );
	
	$( function() {
		var dateFormat = "dd MM yy",
		from = $( "#from_stock_out" )
		
		.datepicker({
			changeMonth: true,
			numberOfMonths: 1
		})
		.on( "change", function() {
		  to.datepicker( "option", "minDate", getDate( this ) );
		}),
		to = $( "#to_stock_out" ).datepicker({
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
	
	function entToHeader(idName){
		var x = event.keyCode;
		
		if(x == 13){
			if(idName == 'ket_select_from'){
				ketSelect = $("#ket_select_from").val();
				if(ketSelect != ''){
					document.getElementById(idName).focus();
				}
			}else{
				document.getElementById(idName).focus();
			}
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
	
	function entToInsert(){
		if(exeTrans == false){
			var x = event.keyCode;
			
			if(x == 45){
				saveBaris();
			}
		}
	}
	
	function saveBaris(){
		if(exeTrans == false){
			exeTrans = true;
			
			document.getElementById("loader_form").className += " active";
			
			var webUrl = "<?php echo base_url()?>";
			
			$.ajax({
				url : webUrl+'/index.php/C_stock_out/save_baris/'+rowData,
				type: 'post',
				data: $('#form_transaction').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						$("#pos_body").append(response.view);
						exeTrans = false;
						
						document.getElementById("alasan_stock_out").setAttribute('readonly','readonly');
						document.getElementById("wrap_tanggal_stock_out").innerHTML = response.tanggal_stock_out;
						document.getElementById("input_"+rowData+"_8").innerHTML = response.button_messsage;
						document.getElementById("input_"+rowData+"_1").setAttribute('readonly','readonly');
						$("#input_"+rowData+"_1").removeAttr("onblur");
						
						rowData = rowData + 1;
						
						document.getElementById("error_wrap").setAttribute('style','display:none');
						document.getElementById("loader_form").classList.remove("active");
						document.getElementById("input_"+rowData+"_1").focus();
						countTotal();
					}else{
						document.getElementById("error_wrap").innerHTML = response.inputerror;
						document.getElementById("error_wrap").setAttribute('style','');
						document.getElementById("loader_form").classList.remove("active");
						exeTrans = false;
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('System Error, Hubungi Tim IT!');
				}
			})
		}
	}
	
	function countTotal(){
		totalGram = 0;
		for (var a = 1; a <= rowData; a++) {
			jumlahVal = $("#input_"+a+"_7").val();
			
			if(jumlahVal == '' || jumlahVal == 'NaN'){
				jumlahVal = "0";
			}
			
			jumlahVal = jumlahVal.replace(/[^0-9.]/g, "");
			jumlahVal = parseFloat(jumlahVal);
			
			totalGram = totalGram + jumlahVal;
		}
		
		totalGram = parseFloat(totalGram);
		totalGram = totalGram.toFixed(3).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		
		document.getElementById("total_gram").innerHTML = totalGram;
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
					
					exeTrans = false;
					document.getElementById("btn_filter").classList.remove("loading");
				}else{
					swal({
						type: "error",
						title: "Gagal Filter Data Stock In!",
						text: "",
						timer: 2000,
						showConfirmButton: false
					});
					
					document.getElementById("loading_filter").setAttribute('style','display:none');
					document.getElementById("button_filter").setAttribute('style','');
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('System Error, Hubungi Tim IT!');
			}
		})
	}
	
	function getProductForm(idRow){
		var webUrl = "<?php echo base_url()?>";
		var stockOutDate = $("#tanggal_stock_out").val();
		var productID = $("#input_"+idRow+"_1").val();
		
		if(productID != '' && productID != null){
			$.ajax({
				url : webUrl+'/index.php/C_stock_out/get_product_from/'+stockOutDate+'/'+productID,
				type: "GET",
				dataType: "JSON",
				success: function(response){
					if(response.success == true){
						if(response.found == 'single'){
							document.getElementById('input_'+idRow+'_1').value=response.id;
							document.getElementById('input_'+idRow+'_2').value=response.asal_barang;
							document.getElementById('input_'+idRow+'_3').value=response.kelompok_barang;
							document.getElementById('input_'+idRow+'_4').value=response.nama_barang;
							document.getElementById('input_'+idRow+'_5').value=response.karat_barang;
							document.getElementById('input_'+idRow+'_6').value=response.box_barang;
							document.getElementById('input_'+idRow+'_7').value=response.berat_barang;
							
							countTotal();
						}else{
							$("#myModal").html(response.view);
							
							$(document).ready(function() {
								$('#modal-table').DataTable({
									"bLengthChange": false
								});
							} );
							
							$('.ui.modal').modal({closable: false}).modal('show');
						}
					}else{
						window.setTimeout(function(){
							$("#myModal").html(response.view);
							
							$('.ui.modal').modal('show');
							
						}, 500);
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('System Error, Hubungi Tim IT!');
				}
			});
		}
	}
	
	function setProduct(productID){
		$('#myModal').modal('hide');
		
		var webUrl = "<?php echo base_url()?>";
		var stockOutDate = $("#tanggal_stock_out").val();
		
		$.ajax({
			url : webUrl+'/index.php/C_stock_out/get_product_from/'+stockOutDate+'/'+productID,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					if(response.found == 'single'){
						document.getElementById('input_'+rowData+'_1').value=response.id;
						document.getElementById('input_'+rowData+'_2').value=response.asal_barang;
						document.getElementById('input_'+rowData+'_3').value=response.kelompok_barang;
						document.getElementById('input_'+rowData+'_4').value=response.nama_barang;
						document.getElementById('input_'+rowData+'_5').value=response.karat_barang;
						document.getElementById('input_'+rowData+'_6').value=response.box_barang;
						document.getElementById('input_'+rowData+'_7').value=response.berat_barang;
						
						document.getElementById('input_'+rowData+'_2').focus();
						countTotal();
					}else{
						$("#myModal").html(response.view);
						
						$('#modal-table').DataTable({
							"bInfo": false
						});
						
						$('.ui.modal').modal({closable: false}).modal('show');
					}
				}else{
					window.setTimeout(function(){
						$("#myModal").html(response.view);
							
						$('.ui.modal').modal('show');
					}, 500);
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('System Error, Hubungi Tim IT!');
			}
		});
	}
	
	window.onload = function() {
		window.setTimeout(function(){
			countTotal();
		}, 300);
	};
</script>
</html>

