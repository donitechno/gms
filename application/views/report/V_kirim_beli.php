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

.ui.form input:not([type]), .ui.form input[type=date], .ui.form input[type=datetime-local], .ui.form input[type=email], .ui.form input[type=file], .ui.form input[type=number], .ui.form input[type=password], .ui.form input[type=search], .ui.form input[type=tel], .ui.form input[type=text], .ui.form input[type=time], .ui.form input[type=url], .ui.form select, .filter-input, .filter-select, .filter-input{
	font-size:0.9em !important;
}
</style>
<body>
	<?php $this->load->view("V_header_sidebar") ?>
	<div class="pusher">
		<div class="ui fluid container">
			<div class="ui stackable menu" style="border-radius:0">
				<div class="item">
					<div class="ui breadcrumb">
						<div class="section"><i class="list alternate outline icon"></i> Transaksi Usaha</div>
						<i class="right angle icon divider"></i>
						<div class="active section">Pengiriman Barang Pembelian</div>
					</div>
				</div>
			</div>
			<div class="ui pointing secondary menu">
				<a class="item active" data-tab="first" style="width:50%" onclick=loadForm()>
					<i class="pencil alternate icon"></i> Input Pengiriman Pembelian
				</a>
				<a class="item" data-tab="second" style="width:50%" onclick=filterTrans()>
					<i class="list ol icon"></i> List Pengiriman Pembelian
				</a>
			</div>
			<div class="ui bottom attached tab segment active" data-tab="first">
				<div class="ui inverted dimmer" id="loader_form">
					<div class="ui large text loader">Loading</div>
				</div>
				<form  class="ui form form-javascript" id="form_save_new" action="<?php echo base_url() ?>index.php/C_kirim_beli/save_kirim_new" method="post">
				<div class="ui grid">
					<div class="four wide column" style="padding-bottom:0">
						<table class="ui striped table kirimrep">
							<thead>
								<tr>
									<th colspan="2" class="center aligned">Kirim ke Dept Reparasi</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="left aligned">24K</td>
									<td class="right aligned" id="rep_duaempat"></td>
									
								</tr>
								<tr>
									<td class="left aligned">916</td>
									<td class="right aligned" id="rep_semsanam"></td>
									
								</tr>
																<tr>
									<td class="left aligned">750</td>
									<td class="right aligned" id="rep_juhlima"></td>
									
								</tr>
								<tr>
									<td class="left aligned">700</td>
									<td class="right aligned" id="rep_juhtus"></td>
									
								</tr>
							</tbody>
						</table>
					</div>
					<div class="four wide column" style="padding-bottom:0">
						<table class="ui striped table kirimrep">
							<thead>
								<tr>
									<th colspan="2" class="center aligned">Kirim ke Dept Pengadaan</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="left aligned">24K</td>
									<td class="right aligned" id="gros_duaempat"></td>
									
								</tr>
								<tr>
									<td class="left aligned">916</td>
									<td class="right aligned" id="gros_semsanam"></td>
									
								</tr>
																<tr>
									<td class="left aligned">750</td>
									<td class="right aligned" id="gros_juhlima"></td>
									
								</tr>
								<tr>
									<td class="left aligned">700</td>
									<td class="right aligned" id="gros_juhtus"></td>
									
								</tr>
							</tbody>
						</table>
					</div>
					<div class="right floated four wide column" style="padding-bottom:0">
						<div class="field">
							<label>Tanggal Pengiriman</label>
							<div id="wrap_tanggal_pengiriman">
								<input type="text" name="tanggal_pengiriman" id="tanggal_pengiriman" onchange="loadForm()" readonly>
							</div>
						</div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0">
						<div class="ui red message" id="error_wrap" style="display:none"></div>
					</div>
					<div class="sixteen wide column" style="padding-top:0;padding-bottom:0" id="wrap_isi_data">
						<table id="send_data_tabel" class="ui celled table" cellspacing="0" width="100%" style="margin-top:15px;">
							<thead>
								<tr class="center aligned">
									<th>No</th>
									<th>Kelompok</th>
									<th>Keterangan</th>
									<th style="width:80px;">Karat</th>
									<th style="width:80px;">Pcs</th>
									<th style="width:120px;">Berat</th>
									<th style="width:120px;">Total</th>
									<th colspan="3">Tujuan</th>
									<th>Act</th>
								</tr>
							</thead>
							<tbody id="pos_body">
							</tbody>
						</table>
						<div class="ui positive right floated labeled icon button" id="btn-save" onclick="savePengiriman()">
							<i class="save icon"></i> Simpan
						</div>
					</div>
					<div class="eight wide column">
						<div class="ui grid" style="padding-top:30px;padding-bottom:15px">
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
				<div class="ui inverted dimmer" id="loader_filter">
					<div class="ui large text loader">Loading</div>
				</div>
				<form class="ui form form-javascript" id="form_filter" action="<?php echo base_url() ?>index.php/C_kirim_beli/filter/" method="post">
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
							<div class="three wide field" id="wrap_kelompok">
								<label>Kelompok Barang</label>
								<select name="filter_category" id="filter_category">
									<option value="All">-- All --</option>
									<?php foreach($category as $c){ ?>
									<option value="<?php echo $c->id ?>"><?php echo $c->category_name ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="two wide field" id="wrap_karat">
								<label>Karat</label>
								<select name="filter_karat" id="filter_karat">
									<option value="All">-- All --</option>
									<?php foreach($karat as $k){ ?>
									<option value="<?php echo $k->id ?>"><?php echo $k->karat_name ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="three wide field">
								<label>Tujuan</label>
								<select name="filter_to" id="filter_to">
									<option id="tujuan_all" value="All">-- All --</option>
									<option value="R">Reparasi</option>
									<option value="G">Pengadaan</option>
									<option id="tujuan_sendiri" value="S">Sendiri</option>
								</select>
							</div>
							<div class="three wide field">
								<label style="visibility:hidden">-</label>
								<select name="rekap_detail" id="rekap_detail" onchange="detailRekapFilter()">
									<option value="D">Detail</option>
									<option value="R">Rekap</option>
								</select>
							</div>
							<div class="one wide field">
								<label style="visibility:hidden">-</label>
								<div class="ui fluid icon green button filter-input" id="btn_filter" onclick="filterTrans()" title="Filter">
									<i class="filter icon"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="sixteen wide centered column" id="wrap_filter" style="padding-top:0">
					</div>
				</div>
				</form>
			</div>
		</div>
	</div>	
	<div class="ui modal" id="myModal"></div>
</body>

<script>
	var rep = new Array();
	rep[1] = 0;
	rep[3] = 0;
	rep[4] = 0;
	rep[5] = 0;
	
	var gros = new Array();
	gros[1] = 0;
	gros[3] = 0;
	gros[4] = 0;
	gros[5] = 0;
	
	var exeTrans = false;
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
		$( "#tanggal_pengiriman" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		
		$('#tanggal_pengiriman').datepicker('setDate', 'today');
	});
	
	function loadForm(){
		document.getElementById("loader_form").className += " active";
		
		tglKirim = $("#tanggal_pengiriman").val();
		
		if(exeTrans == false){
			exeTrans = true;
			
			var webUrl = "<?php echo base_url()?>";
			
			$.ajax({
				url : webUrl+'/index.php/C_kirim_beli/form_kirim_new/'+tglKirim,
				type: 'post',
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						document.getElementById('pos_body').innerHTML = response.view;
						document.getElementById("loader_form").classList.remove("active");
						countTotal();
						exeTrans = false;
					}else{
						alert('Error Load Form!');
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
	
	function savePengiriman(){
		if(exeTrans == false){
			exeTrans = true;
			
			document.getElementById("btn-save").className += " loading";
			
			var webUrl = "<?php echo base_url()?>";
			
			$.ajax({
				url : webUrl+'/index.php/C_kirim_beli/save_kirim_new/',
				type: 'post',
				data: $('#form_save_new').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						var view = '<div class="header">Berhasil</div><div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i></div><div class="ui positive message" style="text-align:center"><div class="header">Berhasil Input Pengiriman!</div></div></div><div class="actions"><div class="ui positive button">OK</div></div>';
						
						document.getElementById("myModal").className += " mini";
						$("#myModal").html(view);
						$('.ui.modal').modal('show');
						
						exeTrans = false;
						
						document.getElementById("btn-save").classList.remove("loading");
						
						loadForm();
					}else{
						swal({
							html:true,
							type: "error",
							title: "",
							text: response.error_message,
						});
						
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
	
	function countTotal(){
		rep[1] = 0;
		rep[3] = 0;
		rep[4] = 0;
		rep[5] = 0;
		
		gros[1] = 0;
		gros[3] = 0;
		gros[4] = 0;
		gros[5] = 0;
	
		rowData = $("#data_length").val();
		totalGram = 0;
		for (var a = 0; a < rowData; a++) {
			radioValR = document.getElementById("to_kirim_r_"+a).checked;
			radioValG = document.getElementById("to_kirim_g_"+a).checked;
			radioValS = document.getElementById("to_kirim_s_"+a).checked;
			
			if(radioValR == true || radioValG == true || radioValS == true){
				karatVal = $("#product_karat_"+a).val();
				karatVal = parseFloat(karatVal);
				
				jumlahVal = $("#product_weight_"+a).val();
			
				if(jumlahVal == '' || jumlahVal == 'NaN'){
					jumlahVal = "0";
				}
				
				jumlahVal = jumlahVal.replace(/[^0-9.]/g, "");
				jumlahVal = parseFloat(jumlahVal);
				
				if(radioValR == true){
					rep[karatVal] = rep[karatVal] + jumlahVal;
				}
				
				if(radioValG == true){
					gros[karatVal] = gros[karatVal] + jumlahVal;
				}
				
				totalGram = totalGram + jumlahVal;
			}
		}
		
		document.getElementById("rep_duaempat").innerHTML = rep[1].toFixed(3).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		document.getElementById("rep_semsanam").innerHTML = rep[3].toFixed(3).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		document.getElementById("rep_juhlima").innerHTML = rep[4].toFixed(3).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		document.getElementById("rep_juhtus").innerHTML = rep[5].toFixed(3).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		
		document.getElementById("gros_duaempat").innerHTML = gros[1].toFixed(3).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		document.getElementById("gros_semsanam").innerHTML = gros[3].toFixed(3).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		document.getElementById("gros_juhlima").innerHTML = gros[4].toFixed(3).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		document.getElementById("gros_juhtus").innerHTML = gros[5].toFixed(3).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		
		totalGram = parseFloat(totalGram);
		totalGram = totalGram.toFixed(3).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		
		document.getElementById("total_gram").innerHTML = totalGram;
	}
	
	function filterTrans(){
		document.getElementById("btn_filter").className += " loading";
		
		detailRekap = $("#rekap_detail").val();
		
		$.ajax({
			url: $('#form_filter').attr('action'),
			type: 'post',
			data: $('#form_filter').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					
					
					if(detailRekap == 'D'){
						document.getElementById("wrap_filter").classList.remove("eight");
						document.getElementById("wrap_filter").classList.remove("wide");
						document.getElementById("wrap_filter").classList.remove("centered");
						document.getElementById("wrap_filter").classList.remove("column");
						document.getElementById("wrap_filter").className += "sixteen wide centered column";
						document.getElementById("wrap_filter").innerHTML = response.view;
						$(document).ready(function() {
							$('#filter_data_tabel').DataTable({
								"bLengthChange": false
							});
						} );
					}else{
						document.getElementById("wrap_filter").classList.remove("sixteen");
						document.getElementById("wrap_filter").classList.remove("wide");
						document.getElementById("wrap_filter").classList.remove("centered");
						document.getElementById("wrap_filter").classList.remove("column");
						document.getElementById("wrap_filter").className += "eight wide centered column";
						document.getElementById("wrap_filter").innerHTML = response.view;
					}
										
					document.getElementById("btn_filter").classList.remove("loading");
				}else{
					alert('Gagal Filter!');
					document.getElementById("btn_filter").classList.remove("loading");
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
	
	function resetVal(rowNumber){
		document.getElementById("to_kirim_r_"+rowNumber).checked = false;
		document.getElementById("to_kirim_g_"+rowNumber).checked = false;
		document.getElementById("to_kirim_s_"+rowNumber).checked = false;
		
		countTotal();
	}
	
	window.onload = function() {
		window.setTimeout(function(){
			loadForm();
		}, 500);
	};
</script>
</html>

