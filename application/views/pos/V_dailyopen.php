<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view('V_link_pos'); ?>
</head>
<style>
.simpan{
	width: 40%;
    font-family: "Segoe UI" !important;
    background: #FBC02D !important;
    background: -webkit-linear-gradient(to top, #D32F2F, #FBC02D) !important;
    background: linear-gradient(to top, #D32F2F, #FBC02D) !important;
    border: none !important;
}

.table th, .table td{
	padding:5px !important;
}
</style>
<body onkeyup=entToHome()>
	<?php $this->load->view('V_header_pos') ?>
	<div class="ui container">
		<form class="ui form" id="form_transaction" action="<?php echo base_url() ?>index.php/C_dailyopen/save_daily_open" method="post">
		<div class="ui grid">
			<div class="eight wide centered column bg-div" style="margin-top:30px;">
				<div class="ui grid">
					<div class="nine wide column">
						<div class="field">
							<label>Tanggal Aktif</label>
							<input type="text" name="tanggal_aktif" id="tanggal_aktif" value="<?php echo $tanggal_aktif?>" onchange="getHargaEmas()" readonly>
						</div>
						<div class="field">
							<label>Harga Emas Kadar 100%</label>
							<input type="text" name="harga_emas" id="harga_emas" value="<?php echo number_format($harga_emas,0) ?>" onkeyup=hargaToCurrency("harga_emas") autocomplete="off">
						</div>
						<div class="ui grid">
							<div class="sixteen wide centered column center aligned">
								<button type="submit" class="ui google plus button" id="btn-submit">
									<i class="save icon"></i> Simpan
								</button>
							</div>
						</div>
					</div>
					<div class="seven wide column">
						<div class="field">
							<label>Saldo Awal</label>
							<input type="text" name="saldo_awal" id="saldo_awal" value="<?php echo number_format($saldo_awal,0) ?>" readonly>
						</div>
					</div>
				</div>
			</div>
		</div>
		</form>
		<div class="ui grid">
			<div class="twelve wide centered column" id="wrap_filter">
				<table class="ui celled table" cellspacing="0" width="100%" style="margin-top:15px;">
					<thead>
						<tr style="text-align:center">
							<th rowspan="2">Karat</th>
							<th colspan="2">Harga Jual</th>
							<th colspan="2">Harga Beli</th>
						</tr>
						<tr style="text-align:center">
							<th style="border-left:1px solid rgba(34,36,38,.1);">Persen</th>
							<th>Harga</th>
							<th>Persen</th>
							<th>Harga</th>
						</tr>
					</thead>
					<tbody>
						<?php
							
							foreach($karat as $k){
							$sell = $harga_emas * $k->kadar_jual / 100;
							$buy = $harga_emas * $k->kadar_beli_bgs / 100;
							
							$sell = $sell / 1000;
							$buy = $buy / 1000;
							
							$sell = ceil($sell);
							$buy = floor($buy);
							
							$sell = $sell * 1000;
							$buy = $buy * 1000;
							
							
						?>
						<tr>
							<td><?php echo $k->description ?></td>
							<td id="kj_<?php echo $k->id ?>"><?php echo $k->kadar_jual ?></td>
							<td class="text-right" id="j_<?php echo $k->id ?>"><?php echo number_format($sell,0,".",",") ?></td>
							<td id="kb_<?php echo $k->id ?>"><?php echo $k->kadar_beli_bgs ?></td>
							<td class="text-right" id="b_<?php echo $k->id ?>"><?php echo number_format($buy,0,".",",") ?></td>
						</tr>	
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="ui modal" id="myModal"></div>
</body>

<script>
	var exeTrans = false;
	
	$(function(){
		$( "#tanggal_aktif" ).datepicker({
			dateFormat: 'dd MM yy'
		});
	});
	
	function entToHome(){
		var x = event.keyCode;
		
		if(x == 27){
			var webUrl = "<?php echo base_url()?>index.php/C_home_pos";
			window.location= webUrl;
		}
	}
	
	function hargaToCurrency(idForm){
		var x = event.keyCode;

		jumlahVal = $("#"+idForm).val();
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
			
			document.getElementById(idForm).value = beforeComma+'.'+afterComma;
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById(idForm).value = jumlahVal;
		}
		
		if(jumlahVal.length >= 6){
			konversiHarga();
		}
	}
	
	function getHargaEmas(){
		var webUrl = "<?php echo base_url()?>";
		
		document.getElementById("btn-submit").className += " loading";
		document.getElementById("wrap_filter").innerHTML = '';
		
		var tglAktif = $("#tanggal_aktif").val();
		
		$.ajax({
			url : webUrl+'index.php/C_dailyopen/get_do_bydate/'+tglAktif,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					document.getElementById("wrap_filter").innerHTML = response.view;
					document.getElementById("harga_emas").value = response.harga_emas;
					document.getElementById("saldo_awal").value = response.saldo_awal;
					
					document.getElementById("btn-submit").classList.remove("loading");
				}else{
					swal({
						type: "error",
						title: "Gagal Filter Data!",
						text: "",
						timer: 2000,
						showConfirmButton: false
					});
					
					document.getElementById("btn-submit").classList.remove("loading");
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error System!');
			}
		});
	}
	
	function konversiHarga(){
		var hargaEmas = $("#harga_emas").val();
		var hargaEmas = hargaEmas.replace(/,/g , "");
		
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'index.php/C_dailyopen/konversi_harga/'+hargaEmas,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					for (var a = 0; a < response.buy.length; a++) {
						var b = a+1
						document.getElementById("j_"+b).innerHTML = response.sell[a];
						document.getElementById("b_"+b).innerHTML = response.buy[a];
					}
				}else{
					swal({
						type: "error",
						title: "Gagal Konversi Harga!",
						text: "",
						timer: 2000,
						showConfirmButton: false
					});
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error System!');
			}
		});
	}
	
	$('#form_transaction').submit(function(e){
		e.preventDefault();
		
		hargaEmas = $("#harga_emas").val();
		
		if(exeTrans == false){
			exeTrans = true;
			document.getElementById("btn-submit").className += " loading";
			
			if(hargaEmas == '' || hargaEmas == null || hargaEmas == '0'){
				var view = '<div class="header">Warning</div><div class="content"><div class="ui icon header center aligned"><i class="check info orange icon"></i></div><div class="ui orange message" style="text-align:center"><div class="header">Harga Tidak Boleh Kosong!</div></div><div class="actions" style="text-align:center"><div class="ui negative icon button">OK</div></div>';
							
				document.getElementById("myModal").className += " mini";
				$("#myModal").html(view);
				$('.ui.modal').modal('show');
				
				exeTrans = false;
				document.getElementById("btn-submit").classList.remove("loading");
			}else{
				$.ajax({
					url: $(this).attr('action'),
					type: 'post',
					data: $(this).serialize(),
					dataType: 'json',
					success: function(response){
						if(response.success == true){
							var view = '<div class="header">Berhasil</div><div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i></div><div class="ui positive message" style="text-align:center"><div class="header">Sukses Daily Open!</div></div><div class="actions" style="text-align:center"><div class="ui positive icon button">OK</div></div>';
							
							document.getElementById("myModal").className += " mini";
							$("#myModal").html(view);
							$('.ui.modal').modal('show');
							
							exeTrans = false;
							document.getElementById("btn-submit").classList.remove("loading");
						}else{
							swal({
								type: "error",
								title: "Gagal Input Data!",
								text: "",
								timer: 2000,
								showConfirmButton: false
							});
							
							exeTrans = false;
							document.getElementById("save_button").setAttribute('style','');
							document.getElementById("exe_loading").setAttribute('style','display:none');
						}
					},
					error: function (jqXHR, textStatus, errorThrown){
						alert('Error get data from ajax');
					}
				})
			}
		}
	});
	
</script>
</html>

