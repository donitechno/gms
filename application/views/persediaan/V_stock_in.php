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
		padding:0;
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
					  <div class="active section">Stock In</div>
					</div>
				</div>
			</div>
			<div class="ui pointing secondary menu">
				<a class="item active" data-tab="first" style="width:50%" onclick=inputForm()>
					<i class="edit icon"></i> Input Data Stock In
				</a>
				<a class="item" data-tab="second" style="width:50%" onclick=filterTrans()>
					<i class="list ol icon"></i> List Data Stock In
				</a>
			</div>
			<div class="ui bottom attached tab segment active" data-tab="first">
				<div class="ui inverted dimmer" id="loader_form">
					<div class="ui large text loader">Loading</div>
				</div>
				<form class="ui form form-javascript" id="form_transaction" action="<?php echo base_url() ?>index.php/C_stock_in/save_baris" method="post">
				<div class="ui grid">
					<div class="left floated four wide column" style="padding-bottom:0">
						<div class="field">
							<label>ID Barang</label>
							<input type="text" id="id_urut" name="id_urut" value="<?php echo $id_urutan ?>" readonly>
						</div>
					</div>
					<div class="right floated ten wide column" style="padding-bottom:0">
						<div class="fields">
							<div class="four wide field">
								<label>Asal Barang</label>
								<div id="wrap_select_from">
									<select name="select_from" id="select_from" onchange="getKetFrom()" onkeydown=entToHeader("ket_select_from")>
										<option value="">-- Asal Barang --</option>
										<?php foreach($from as $f){ ?>
										<option value="<?php echo $f->id ?>"><?php echo $f->from_name ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="six wide field">
								<label>Keterangan</label>
								<input type="text" name="ket_select_from" id="ket_select_from" readonly onkeydown=entToHeader("tanggal_stock_in")>
							</div>
							<div class="six wide field">
								<label>Tanggal Stock In</label>
								<div id="wrap_tanggal_stock_in">
									<input type="text" name="tanggal_stock_in" id="tanggal_stock_in" readonly onkeydown=entToHeader("input_1_1-selectized") onchange=entToForm("input_1_1-selectized")>
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
									<th style="width:50px;">No</th>
									<th style="width:80px;">Karat</th>
									<th style="width:80px;">Box</th>
									<th>Kelompok</th>
									<th style="width:280px;">Nama Barang</th>
									<th style="width:120px;">Berat</th>
									<th>ID Barang</th>
									<th>X</th>
								</tr>
							</thead>
							<tbody id="pos_body">
								<tr id="pos_tr_1">
									<td class="center aligned">1</td>
									<td>
										<div id="wrap_id_karat_1">
											<select class="select-form" onchange=entToTabInput("1","1") name="id_karat_1" id="input_1_1">
												<option value=""></option>
												<?php foreach($karat as $k){ ?>
												<option value="<?php echo $k->id ?>"><?php echo $k->karat_name ?></option>
												<?php } ?>
											</select>
										</div>
									</td>
									<td>
										<div id="wrap_id_box_1">
											<select class="select-form" onchange=entToTabInput("1","2") name="id_box_1" id="input_1_2">
												<option value=""></option>
												<?php foreach($box as $b){ ?>
												<option value="<?php echo $b->id ?>"><?php echo $b->nama_box ?></option>
												<?php } ?>
											</select>
										</div>
									</td>
									<td>
										<div id="wrap_id_category_1">
											<select class="select-form" name="id_category_1" id="input_1_3" onchange=getMasterProduct("1")>
												<option value=""></option>
												<?php foreach($category as $c){ ?>
												<option value="<?php echo $c->id ?>"><?php echo $c->category_name ?></option>
												<?php } ?>
											</select>
										</div>
									</td>
									<td>
										<div id="wrap_nama_barang_1">
											<select class="select-form" class="" name="nama_barang_1" id="input_1_4">
												<option value=""></option>
											</select>
										</div>
									</td>
									<td>
										<input class="form-pos" type="text" name="berat_1" id="input_1_5" onkeyup=beratToCurrency("1") autocomplete="off">
									</td>
									<td id="input_1_6"></td>
									<td class="center aligned" id="input_1_7">
										<div class="ui tiny icon google plus button"><i class="ban icon"></i></div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="eight wide column">
						<div class="ui grid" style="padding-top:30px;padding-bottom:15px">
							<div class="four wide column ket-bawah" style="padding-bottom:5px;padding-top:0">F2</div>
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
				<form class="ui form form-javascript" id="form_filter" action="<?php echo base_url() ?>index.php/C_stock_in/filter_stock_in" method="post">
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
								<label>Tgl Stock In</label>
								<input type="text" name="from_stock_in" id="from_stock_in" readonly>
							</div>
							<div class="one wide field" style="text-align:center;margin-top:30px">
								<label>s.d</label>
							</div>
							<div class="three wide field">
								<label style="visibility:hidden">-</label>
								<input type="text" name="to_stock_in" id="to_stock_in" readonly>
							</div>
							<div class="one wide field">
								<label style="visibility:hidden">-</label>
								<div class="ui fluid icon green button filter-input" id="btn_filter" onclick="filterTrans()" title="Filter">
									<i class="filter icon"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" id="wrap_filter" style="padding-top:0">
					</div>
					<div class="six wide column">
						<div class="ui grid" style="padding-bottom:40px">
							<div class="eight wide column ket-bawah left aligned" style="padding-bottom:0;padding-top:0">TOTAL (Pcs)</div>
							<div class="eight wide column ket-bawah right aligned" id="total_list_pcs" style="padding-bottom:0;padding-top:0">0 Pcs</div>
						</div>
					</div>
					<div class="four wide column">
					</div>
					<div class="six wide column">
						<div class="ui grid" style="padding-bottom:40px">
							
							<div class="eight wide column ket-bawah right aligned" style="padding-bottom:0;padding-top:0">TOTAL (Gram)</div>
							<div class="eight wide column ket-bawah right aligned" id="total_list_gram" style="padding-bottom:0;padding-top:0">0.000</div>
						</div>
					</div>
				</div>
				</form>
			</div>
		</div>
	</div>
	<div class="ui modal mini" id="wrap_modal"></div>
</body>
<script>
	var exeTrans = false;
	var rowData = 1;
	
	$('select.dropdown').dropdown();
	$('.menu .item').tab();
	
	document.getElementById("select_from").focus();
	$('#input_1_1').selectize();
	$('#input_1_2').selectize();
	$('#input_1_3').selectize();
	$('#input_1_4').selectize();
	
	$('.form-javascript').on('keyup keypress', function(e){
			var keyCode = e.keyCode || e.which;
			if(keyCode === 13){
				e.preventDefault();
				return false;
			}
		});
	
	$(function(){
		$( "#tanggal_stock_in" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		
		$('#tanggal_stock_in').datepicker('setDate', 'today');
	});
	
	$( function() {
		$( "#from_stock_in" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		$('#from_stock_in').datepicker('setDate', 'today');
		
		$( "#to_stock_in" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		$('#to_stock_in').datepicker('setDate', 'today');
	} );
	
	$( function(){
		var dateFormat = "dd MM yy",
		from = $( "#from_stock_in" )
		
		.datepicker({
			changeMonth: true,
			numberOfMonths: 1
		})
		.on( "change", function(){
		  to.datepicker( "option", "minDate", getDate( this ) );
		}),
		to = $( "#to_stock_in" ).datepicker({
			changeMonth: true,
			numberOfMonths: 3
		})
		.on( "change", function(){
			from.datepicker( "option", "maxDate", getDate( this ) );
		});

		function getDate( element ){
			var date;
			try {
				date = $.datepicker.parseDate( dateFormat, element.value );
			}catch( error ){
				date = null;
			}

			return date;
		}
	});
	
	function entToInsert(){
		if(exeTrans == false){
			var x = event.keyCode;
			
			if(x == 113){
				saveBaris();
			}else if(x == 115){
				document.getElementById('input_'+rowData+'_1-selectized').focus();
			}
		}
	}
	
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
			if(idElement != 5){
				document.getElementById("input_"+idRow+"_"+idElement+"-selectized").focus();
			}else{
				document.getElementById("input_"+idRow+"_"+idElement+"").focus();
			}
		}
	}
	
	function entToTabInput(idRow,idElement){
		idElement = parseFloat(idElement);
		idElement = idElement + 1;
		if(idElement != 5){
			document.getElementById("input_"+idRow+"_"+idElement+"-selectized").focus();
		}else{
			document.getElementById("input_"+idRow+"_"+idElement+"").focus();
		}
	}
	
	function getKetFrom(){
		var webUrl = "<?php echo base_url()?>";
		var ketFromID = $("#select_from").val();
		
		if(ketFromID != null && ketFromID != ''){
			$.ajax({
				url : webUrl+'/index.php/C_stock_in/get_ket_from/'+ketFromID,
				type: "GET",
				dataType: "JSON",
				success: function(response){
					if(response.success == true){
						if(response.edit == true){
							document.getElementById('ket_select_from').value = response.ketvalue;
							$("#ket_select_from").removeAttr("readonly");
						}else{
							document.getElementById('ket_select_from').value = response.ketvalue;
							$("#ket_select_from").attr("readonly","readonly");
						}
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
	
	function getMasterProduct(idRow){
		var webUrl = "<?php echo base_url()?>";
		var categoryID = $("#input_"+idRow+"_3").val();
		
		if(categoryID != ''){
			$.ajax({
				url : webUrl+'/index.php/C_stock_in/get_master_product/'+categoryID+'/'+idRow,
				type: "GET",
				dataType: "JSON",
				success: function(response){
					if(response.success == true){
						document.getElementById('wrap_nama_barang_'+idRow).innerHTML = response.view;
						$('#input_'+idRow+'_4').selectize({
							onChange: function(value) {
								entToTabInput(idRow,4);
							}
						});
								
						document.getElementById('input_'+idRow+'_4-selectized').focus();
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
	
	function beratToCurrency(idRow){
		jumlahVal = $("#input_"+idRow+"_5").val();
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
			
			document.getElementById("input_"+idRow+"_5").value = beforeComma+'.'+afterComma.substring(0,2);
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById("input_"+idRow+"_5").value = jumlahVal;
		}
		
		countTotal();
	}
	
	function saveBaris(){
		if(exeTrans == false){
			exeTrans = true;
			
			document.getElementById("loader_form").className += " active";
			
			var webUrl = "<?php echo base_url()?>";
			
			$.ajax({
				url : webUrl+'/index.php/C_stock_in/save_baris/'+rowData,
				type: 'post',
				data: $('#form_transaction').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						$("#pos_body").append(response.view);
						document.getElementById("ket_select_from").setAttribute('readonly','readonly');
						document.getElementById("wrap_id_karat_"+rowData).innerHTML = response.select_karat;
						document.getElementById("wrap_id_karat_"+rowData).setAttribute('style','padding: 0.3em 0.78571429em;');
						document.getElementById("wrap_id_box_"+rowData).innerHTML = response.select_box;
						document.getElementById("wrap_id_box_"+rowData).setAttribute('style','padding: 0.3em 0.78571429em;');
						document.getElementById("wrap_id_category_"+rowData).innerHTML = response.select_category;
						document.getElementById("wrap_id_category_"+rowData).setAttribute('style','padding: 0.3em 0.78571429em;');
						document.getElementById("input_"+rowData+"_5").setAttribute('readonly','readonly');
						document.getElementById("input_"+rowData+"_6").innerHTML = response.product_id;
						document.getElementById("input_"+rowData+"_6").setAttribute('style','padding: 0.3em 0.78571429em;');
						document.getElementById("input_"+rowData+"_7").innerHTML = response.button_messsage;
						document.getElementById("wrap_select_from").innerHTML = response.select_from;
						document.getElementById("wrap_tanggal_stock_in").innerHTML = response.tanggal_stock_in;
						document.getElementById("wrap_nama_barang_"+rowData).innerHTML = response.nama_barang;
						document.getElementById("wrap_nama_barang_"+rowData).setAttribute('style','padding: 0.3em 0.78571429em;');
						document.getElementById("error_wrap").setAttribute('style','display:none');
						
						rowData = rowData + 1;
						
						$('#input_'+rowData+'_1').selectize();
						$('#input_'+rowData+'_2').selectize();
						$('#input_'+rowData+'_3').selectize();
						$('#input_'+rowData+'_4').selectize();
						
						window.setTimeout(function(){
							document.getElementById("input_"+rowData+"_5").focus();
						}, 1000);
						
						document.getElementById("id_urut").value = response.id_urutan;
						exeTrans = false;
						
						document.getElementById("loader_form").classList.remove("active");
						countTotal();
					}else{
						document.getElementById("error_wrap").innerHTML = response.inputerror;
						document.getElementById("error_wrap").setAttribute('style','');
						document.getElementById("loader_form").classList.remove("active");
						exeTrans = false;
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
	
	function countTotal(){
		totalGram = 0;
		for (var a = 1; a <= rowData; a++) {
			jumlahVal = $("#input_"+a+"_5").val();
			
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
					
					document.getElementById("total_list_pcs").innerHTML = response.total_pcs+" Pcs";
					document.getElementById("total_list_gram").innerHTML = response.total_gram;
					exeTrans = false;
					document.getElementById("btn_filter").classList.remove("loading");
				}else{
					alert('filter gagal');
					exeTrans = false;
					document.getElementById("btn_filter").classList.remove("loading");
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
	
	function inputForm(){
		window.setTimeout(function(){
			document.getElementById("input_"+rowData+"_1-selectized").focus();
		}, 1000);
	}
	
	window.onload = function() {
		window.setTimeout(function(){
			countTotal();
		}, 300);
	};
</script>
</html>

