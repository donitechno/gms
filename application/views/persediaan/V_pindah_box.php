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
					  <div class="active section">Pindah Box</div>
					</div>
				</div>
			</div>
			<div class="ui pointing secondary menu">
				<a class="item active" data-tab="first" style="width:50%" onclick=resetVal()>
					<i class="edit icon"></i> Input Pindah Box
				</a>
				<a class="item" data-tab="second" style="width:50%" onclick=filterTrans()>
					<i class="list ol icon"></i> List Pindah Box
				</a>
			</div>
			<div class="ui bottom attached tab segment active" data-tab="first">
				<div class="ui inverted dimmer" id="loader_form">
					<div class="ui large text loader">Loading</div>
				</div>
				<form class="ui form form-javascript" id="form_transaction" action="<?php echo base_url() ?>index.php/C_pindah_box/save_baris" method="post">
				<div class="ui grid">
					<div class="right floated four wide column" style="padding-bottom:0">
						<div class="fields">
							<div class="sixteen wide field">
							  <label>Tanggal Pindah Box</label>
								<div id="wrap_tanggal_pindah_box">
									<input type="text" name="tanggal_pindah_box" id="tanggal_pindah_box" readonly onkeydown=entToHeader("input_1_1") onchange=entToForm("input_1_1")>
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
									<th>Kelompok</th>
									<th style="width:280px;">Nama Barang</th>
									<th style="width:80px;">Karat</th>
									<th style="width:120px;">Berat</th>
									<th style="width:80px;">Dari Box</th>
									<th style="width:80px;">Ke Box</th>
									<th>X</th>
								</tr>
							</thead>
							<tbody id="pos_body">
								<tr id="pos_tr_1">
									<td class="center aligned">1</td>
									<td>
										<input class="form-pos" type="text" name="id_1" id="input_1_1" onkeydown=entToTab("1","1") onblur=getProductForm("1") autocomplete="off" placeholder="Masukkan ID Barang" autofocus="on">
									</td>
									<td>
										<input class="form-pos" type="text" name="category_1" id="input_1_2" onkeydown=entToTab("1","2") readonly>
									</td>
									<td>
										<input class="form-pos" type="text" name="nama_barang_1" id="input_1_3" onkeydown=entToTab("1","3") readonly>
									</td>
									<td>
										<input class="form-pos" type="text" name="karat_1" id="input_1_4" onkeydown=entToTab("1","4") readonly>
									</td>
									<td>
										<input class="form-pos right aligned" type="text" name="berat_1" id="input_1_5" onkeydown=entToTab("1","5") readonly>
									</td>
									<td>
										<input class="form-pos center aligned" type="text" name="box_1" id="input_1_6" onkeydown=entToTab("1","6") readonly>
									</td>
									<td id="wrap_ke_box_1">
										<input class="form-pos" type="text" name="box_to_1" id="input_1_7" onkeydown=entToTab("1","7") readonly>
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
				</div>
				</form>
			</div>
			<div class="ui bottom attached tab segment" data-tab="second">
				<div class="ui inverted dimmer" id="loader_form">
					<div class="ui large text loader">Loading</div>
				</div>
				<form class="ui form form-javascript" id="form_filter" action="<?php echo base_url() ?>index.php/C_pindah_box/filter_pindah_box" method="post">
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
								<label>Box Asal</label>
								<select name="filter_box_from" id="filter_box_from">
									<option value="All">-- All --</option>
									<?php foreach($box as $b){ ?>
									<option value="<?php echo $b->id ?>">BOX <?php echo $b->nama_box ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="two wide field">
								<label>Box Tujuan</label>
								<select name="filter_box_to" id="filter_box_to">
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
								<label>Tgl Pindah Box</label>
								<input type="text" name="from_pindah_box" id="from_pindah_box" readonly>
							</div>
							<div class="one wide field" style="text-align:center;margin-top:30px">
								<label>s.d</label>
							</div>
							<div class="three wide field">
								<label style="visibility:hidden">-</label>
								<input type="text" name="to_pindah_box" id="to_pindah_box" readonly>
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
	
	$( function() {
		$( "#tanggal_pindah_box" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		
		$('#tanggal_pindah_box').datepicker('setDate', 'today');
	} );
	
	$( function() {
		$( "#from_pindah_box" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		$('#from_pindah_box').datepicker('setDate', 'today');
		
		$( "#to_pindah_box" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		$('#to_pindah_box').datepicker('setDate', 'today');
	} );
	
	$( function() {
		var dateFormat = "dd MM yy",
		from = $( "#from_pindah_box" )
		
		.datepicker({
			changeMonth: true,
			numberOfMonths: 1
		})
		.on( "change", function() {
		  to.datepicker( "option", "minDate", getDate( this ) );
		}),
		to = $( "#to_pindah_box" ).datepicker({
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
	
	document.getElementById("input_1_1").focus();
	
	function resetVal(){
		exeTrans = false;
		window.setTimeout(function(){
			document.getElementById("input_"+rowData+"_1").focus();
		}, 500);
	}
	
	function entToInsert(){
		if(exeTrans == false){
			var x = event.keyCode;
			
			if(x == 45){
				saveBaris();
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
	
	function getProductForm(idRow){
		var webUrl = "<?php echo base_url()?>";
		var pindahBoxDate = $("#tanggal_pindah_box").val();
		var productID = $("#input_"+idRow+"_1").val();
		
		if(productID != '' && productID != null){
			$.ajax({
				url : webUrl+'/index.php/C_pindah_box/get_product_from/'+pindahBoxDate+'/'+productID+'/'+idRow,
				type: "GET",
				dataType: "JSON",
				success: function(response){
					if(response.success == true){
						if(response.found == 'single'){
							document.getElementById('input_'+idRow+'_1').value=response.id;
							document.getElementById('input_'+idRow+'_2').value=response.kelompok_barang;
							document.getElementById('input_'+idRow+'_3').value=response.nama_barang;
							document.getElementById('input_'+idRow+'_4').value=response.karat_barang;
							document.getElementById('input_'+idRow+'_5').value=response.berat_barang;
							document.getElementById('input_'+idRow+'_6').value=response.box_barang;
							document.getElementById('wrap_ke_box_'+idRow).innerHTML=response.view;
							
							$('#input_'+idRow+'_7').selectize();
							
							window.setTimeout(function(){
								document.getElementById('input_'+idRow+'_7-selectized').focus();
							}, 500);
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
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert('System Error, Hubungi Tim IT!');
				}
			});
		}
	}
	
	function setProduct(productID){
		$('#myModal').modal('hide');
		var idRow = rowData;
		
		var webUrl = "<?php echo base_url()?>";
		var pindahBoxDate = $("#tanggal_pindah_box").val();
		
		$.ajax({
			url : webUrl+'/index.php/C_pindah_box/get_product_from/'+pindahBoxDate+'/'+productID+'/'+idRow,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
						if(response.found == 'single'){
							document.getElementById('input_'+idRow+'_1').value=response.id;
							document.getElementById('input_'+idRow+'_2').value=response.kelompok_barang;
							document.getElementById('input_'+idRow+'_3').value=response.nama_barang;
							document.getElementById('input_'+idRow+'_4').value=response.karat_barang;
							document.getElementById('input_'+idRow+'_5').value=response.berat_barang;
							document.getElementById('input_'+idRow+'_6').value=response.box_barang;
							document.getElementById('wrap_ke_box_'+idRow).innerHTML=response.view;
							
							$('#input_'+idRow+'_7').selectize();
							
							window.setTimeout(function(){
								document.getElementById('input_'+idRow+'_7-selectized').focus();
							}, 500);
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
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('System Error, Hubungi Tim IT!');
			}
		});
	}
	
	function saveBaris(){
		if(exeTrans == false){
			exeTrans = true;
			
			document.getElementById("loader_form").className += " active";
			
			var webUrl = "<?php echo base_url()?>";
		
			$.ajax({
				url : webUrl+'/index.php/C_pindah_box/save_baris/'+rowData,
				type: 'post',
				data: $('#form_transaction').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						document.getElementById("error_wrap").innerHTML = '';
						document.getElementById("error_wrap").setAttribute('style','display:none');
						document.getElementById("loader_form").classList.remove("active");
						
						$("#pos_body").append(response.view);
						
						document.getElementById("wrap_tanggal_pindah_box").innerHTML = response.tanggal_pindah_box;
						document.getElementById("wrap_ke_box_"+rowData).innerHTML = response.box_ke;
						document.getElementById("input_"+rowData+"_8").innerHTML = response.button_messsage;
						document.getElementById("input_"+rowData+"_1").setAttribute('readonly','readonly');
						$("#input_"+rowData+"_1").removeAttr("onblur");
						
						rowData = rowData + 1;
						document.getElementById("input_"+rowData+"_1").focus();
						exeTrans = false;
					}else{
						if(response.val_filter == 'N'){
							document.getElementById("error_wrap").innerHTML = response.inputerror;
							document.getElementById("error_wrap").setAttribute('style','');
							document.getElementById("loader_form").classList.remove("active");
							
						}else if(response.val_filter == 'Y'){
							window.setTimeout(function(){
								$("#myModal").html(response.view);
								
								$('#modal-table').DataTable({
									"bPaginate": false,
									"bInfo": false,
									"searching": false
								});
								
								$('.ui.modal').modal({
									closable: false
								}).modal('show');
							}, 500);
						}
						exeTrans = false;
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('System Error, Hubungi Tim IT!');
				}
			})
		}
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
					alert('filter gagal');
					exeTrans = false;
					document.getElementById("btn_filter").classList.remove("loading");
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('System Error, Hubungi Tim IT!');
			}
		})
	}
</script>
</html>

