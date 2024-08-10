<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view('V_link_pos') ?>
</head>
<style>
	.td-bold{
		font-weight:600;
	}
	
	.dash-top{
		border-top:1px dashed #000 !important;
	}
	
	.double-top{
		border-top:double #000 !important;
	}
</style>
<body onkeyup=entToHome()>
	<div class="ui fluid container">
		<div class="ui pointing secondary menu">
			<a class="item active" data-tab="first" style="width:33%" onclick=filterLap()>
				<i class="columns icon"></i> Laporan Kas Kasir
			</a>
			<a class="item" data-tab="second" style="width:33%" onclick=filterJual()>
				<i class="tags icon"></i> Laporan Penjualan
			</a>
			<a class="item" data-tab="third" style="width:34%" onclick=filterBeli()>
				<i class="shopping cart icon"></i> Laporan Pembelian
			</a>
		</div>
		<div class="ui bottom attached tab segment active" data-tab="first">
			<form class="ui form" id="form_kas" action="<?php echo base_url() ?>index.php/C_lap_kasir/filter_kas" method="post">
			<div class="ui grid">
				<div class="ten wide centered column" style="padding-bottom:0">
					<div class="fields">
						<div class="five wide field">
							<label>Tanggal Transaksi</label>
							<input type="text" name="tgl_transaksi" id="tgl_transaksi" readonly>
						</div>
						<div class="five wide field">
							<label>Jenis Pembayaran</label>
							<select name="jenis_bayar" id="jenis_bayar">
								<option value="">Semua</option>
								<option value="<?php echo $acc_ke ?>">TUNAI</option>
								<?php foreach($bayar as $b){ ?>
								<option value="<?php echo $b->account_number ?>"><?php echo $b->description ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="three wide field">
							<label style="visibility:hidden">Detail/Rekap</label>
							<select name="detail_rekap" id="detail_rekap">
								<option value="D">Detail</option>
								<option value="R">Rekap</option>
							</select>
						</div>
						<div class="two wide field">
							<label style="visibility:hidden">Filter</label>
							<div class="ui fluid icon green button filter-input" id="btn_filter_lap" onclick="filterLap()" title="Filter">
								<i class="filter icon"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="twelve wide centered column" id="wrap_lap">
				</div>
			</div>
			</form>
		</div>
		<div class="ui bottom attached tab segment" data-tab="second">
			<form class="ui form" id="form_jual" action="<?php echo base_url() ?>index.php/C_lap_kasir/filter_jual" method="post">
			<div class="ui grid">
				<div class="ten wide centered column" style="padding-bottom:0">
					<div class="fields">
						<div class="five wide field">
							<input type="text" name="from_filter_jual" id="from_filter_jual" readonly>
						</div>
						<div class="two wide field" style="text-align:center;margin-top:7px">
							<label>s.d</label>
						</div>
						<div class="five wide field">
							<input type="text" name="to_filter_jual" id="to_filter_jual" readonly>
						</div>
						<div class="four wide field">
							<select name="detail_rekap_jual" id="detail_rekap_jual" onchange=getDetailRekapJual()>
								<option value="D">Detail</option>
								<option value="R">Rekap</option>
							</select>
						</div>
					</div>
					<div class="fields">
						<div class="four wide field" id="wrap_box_jual">
							<select name="filter_box_jual" id="filter_box_jual">
								<option value="All">-- Seluruh Box --</option>
								<?php foreach($box as $b){ ?>
								<option value="<?php echo $b->id ?>">BOX <?php echo $b->nama_box ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="four wide field" id="wrap_karat_jual">
							<select name="filter_karat_jual" id="filter_karat_jual">
								<option value="All">-- Seluruh Karat --</option>
								<?php foreach($karat as $k){ ?>
								<option value="<?php echo $k->id ?>"><?php echo $k->karat_name ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="four wide field" id="wrap_rekap_jual">
							<select name="filter_rekap_jual" id="filter_rekap_jual">
								<option value="All">Semua</option>
								<option value="K">Per Karat</option>
								<option value="G">Per Kelompok</option>
							</select>
						</div>
						<div class="four wide field">
							<div class="ui fluid icon green button filter-input" id="btn_filter_jual" onclick="filterJual()" title="Filter">
								<i class="filter icon"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="twelve wide centered column" id="wrap_jual">
				</div>
			</div>
			</form>
		</div>
		<div class="ui bottom attached tab segment" data-tab="third">
			<form class="ui form" id="form_beli" action="<?php echo base_url() ?>index.php/C_lap_kasir/filter_beli" method="post">
			<div class="ui grid">
				<div class="ten wide centered column" style="padding-bottom:0">
					<div class="fields">
						<div class="five wide field">
							<input type="text" name="from_filter_beli" id="from_filter_beli" readonly>
						</div>
						<div class="two wide field" style="text-align:center;margin-top:7px">
							<label>s.d</label>
						</div>
						<div class="five wide field">
							<input type="text" name="to_filter_beli" id="to_filter_beli" readonly>
						</div>
						<div class="four wide field">
							<select name="detail_rekap_beli" id="detail_rekap_beli" onchange=getDetailRekapBeli()>
								<option value="D">Detail</option>
								<option value="R">Rekap</option>
							</select>
						</div>
					</div>
					<div class="fields">
						<div class="six wide field" id="wrap_karat_beli">
							<select name="filter_karat_beli" id="filter_karat_beli">
								<option value="All">-- Seluruh Karat --</option>
								<?php foreach($karat as $k){ ?>
								<option value="<?php echo $k->id ?>"><?php echo $k->karat_name ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="six wide field" id="wrap_rekap_beli">
							<select name="filter_rekap_beli" id="filter_rekap_beli">
								<option value="All">Semua</option>
								<option value="K">Per Karat</option>
								<option value="G">Per Kelompok</option>
							</select>
						</div>
						<div class="four wide field">
							<div class="ui fluid icon green button filter-input" id="btn_filter_beli" onclick="filterBeli()" title="Filter">
								<i class="filter icon"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="twelve wide centered column" id="wrap_beli">
				</div>
			</div>
			</form>
		</div>
	</div>
	<div class="ui modal mini" id="wrap_modal"></div>
</body>


<script>
	var exeTrans = false;
	var flagTrans = 'input';
	
	$('.menu .item').tab();
	
	$(function(){
		$( "#tgl_transaksi" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		
		$('#tgl_transaksi').datepicker('setDate', 'today');
	});
	
	$( function() {
		$( "#from_filter_jual" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		$('#from_filter_jual').datepicker('setDate', 'today');
		
		$( "#to_filter_jual" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		$('#to_filter_jual').datepicker('setDate', 'today');
	} );
	
	$( function() {
		var dateFormat = "dd MM yy",
		from = $( "#from_filter_jual" )
		
		.datepicker({
			changeMonth: true,
			numberOfMonths: 1
		})
		.on( "change", function() {
		  to.datepicker( "option", "minDate", getDate( this ) );
		}),
		to = $( "#to_filter_jual" ).datepicker({
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
	
	$( function() {
		$( "#from_filter_beli" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		$('#from_filter_beli').datepicker('setDate', 'today');
		
		$( "#to_filter_beli" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		$('#to_filter_beli').datepicker('setDate', 'today');
	} );
	
	$( function() {
		var dateFormat = "dd MM yy",
		from = $( "#from_filter_beli" )
		
		.datepicker({
			changeMonth: true,
			numberOfMonths: 1
		})
		.on( "change", function() {
		  to.datepicker( "option", "minDate", getDate( this ) );
		}),
		to = $( "#to_filter_beli" ).datepicker({
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
	
	function entToHome(){
		var x = event.keyCode;
		
		if(x == 27){
			var webUrl = "<?php echo base_url()?>index.php/C_home_pos";
			window.location= webUrl;
		}
	}
	
	function getDetailRekapJual(){
		detailRekap = $("#detail_rekap_jual").val();
		if(detailRekap == 'D'){
			document.getElementById("wrap_rekap_jual").setAttribute('style','visibility:hidden');
			document.getElementById("wrap_karat_jual").setAttribute('style','');
			document.getElementById("wrap_box_jual").setAttribute('style','');
		}else if(detailRekap == 'R'){
			document.getElementById("wrap_rekap_jual").setAttribute('style','');
			document.getElementById("wrap_karat_jual").setAttribute('style','visibility:hidden');
			document.getElementById("wrap_box_jual").setAttribute('style','visibility:hidden');
		}
	}
	
	function getDetailRekapBeli(){
		detailRekap = $("#detail_rekap_beli").val();
		if(detailRekap == 'D'){
			document.getElementById("wrap_rekap_beli").setAttribute('style','visibility:hidden');
			document.getElementById("wrap_karat_beli").setAttribute('style','');
		}else if(detailRekap == 'R'){
			document.getElementById("wrap_rekap_beli").setAttribute('style','');
			document.getElementById("wrap_karat_beli").setAttribute('style','visibility:hidden');
		}
	}
	
	function filterLap(){
		document.getElementById("wrap_lap").innerHTML = '';
		document.getElementById("btn_filter_lap").className += " loading";
		
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url: $('#form_kas').attr('action'),
			type: 'post',
			data: $('#form_kas').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					document.getElementById("wrap_lap").innerHTML = response.view;
					exeTrans = false;
					document.getElementById("btn_filter_lap").classList.remove("loading");
				}else{
					alert('filter gagal');
					exeTrans = false;
					document.getElementById("btn_filter_lap").classList.remove("loading");
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
	
	function filterJual(){
		document.getElementById("wrap_jual").innerHTML = '';
		document.getElementById("btn_filter_jual").className += " loading";
		
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url: $('#form_jual').attr('action'),
			type: 'post',
			data: $('#form_jual').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					document.getElementById("wrap_jual").innerHTML = response.view;
					exeTrans = false;
					document.getElementById("btn_filter_jual").classList.remove("loading");
				}else{
					alert('filter gagal');
					exeTrans = false;
					document.getElementById("btn_filter_jual").classList.remove("loading");
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
	
	function deleteTransJual(idTrans){
		var view= '<div class="header">Hapus Transaksi</div><div class="content"><p>Anda Ingin Menghapus Data Tersebut?</p></div><div class="actions"><div class="ui negative button">Tidak </div><button id="btn_confirm" class="ui green right labeled icon button" onclick=exeDeleteTransJual("'+idTrans+'")>Ya<i class="check circle icon"></i></button></div>';
		
		document.getElementById("wrap_modal").innerHTML = view;
		
		$('.ui.modal').modal({closable: false}).modal('show');
	}
	
	function exeDeleteTransJual(idTrans){
		var webUrl = "<?php echo base_url()?>";
		document.getElementById("btn_confirm").className += " loading";
		
		$.ajax({
			url : webUrl+'/index.php/C_lap_kasir/hapus_jual/'+idTrans,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					document.getElementById("wrap_modal").innerHTML = response.message;
					filterJual();
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
	
	function filterBeli(){
		document.getElementById("wrap_beli").innerHTML = '';
		document.getElementById("btn_filter_beli").className += " loading";
		
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url: $('#form_beli').attr('action'),
			type: 'post',
			data: $('#form_beli').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					document.getElementById("wrap_beli").innerHTML = response.view;
					exeTrans = false;
					document.getElementById("btn_filter_beli").classList.remove("loading");
				}else{
					alert('filter gagal');
					exeTrans = false;
					document.getElementById("btn_filter_beli").classList.remove("loading");
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
	
	function deleteTransBeli(idTrans){
		var view= '<div class="header">Hapus Transaksi</div><div class="content"><p>Anda Ingin Menghapus Data Tersebut?</p></div><div class="actions"><div class="ui negative button">Tidak </div><button id="btn_confirm" class="ui green right labeled icon button" onclick=exeDeleteTransBeli("'+idTrans+'")>Ya<i class="check circle icon"></i></button></div>';
		
		document.getElementById("wrap_modal").innerHTML = view;
		
		$('.ui.modal').modal({closable: false}).modal('show');
	}
	
	function exeDeleteTransBeli(idTrans){
		var webUrl = "<?php echo base_url()?>";
		document.getElementById("btn_confirm").className += " loading";
		
		$.ajax({
			url : webUrl+'/index.php/C_lap_kasir/hapus_beli/'+idTrans,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					document.getElementById("wrap_modal").innerHTML = response.message;
					filterBeli();
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
			getDetailRekapJual();
			getDetailRekapBeli();
			filterLap();
		}, 300);
	};
</script>
</html>

