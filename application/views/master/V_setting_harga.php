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

#btn-save{
	margin-top:10px;
}
</style>
<body>
	<?php $this->load->view("V_header_sidebar") ?>
	<div class="pusher">
		<div class="ui container fluid">
			<div class="ui stackable menu" style="border-radius:0">
				<div class="item">
					<div class="ui breadcrumb">
					  <div class="section"><i class="cogs icon"></i> Pengaturan</div>
					  <i class="right chevron icon divider"></i>
					  <div class="active section">Setting Harga Jual Beli</div>
					</div>
				</div>
			</div>
			<div class="ui grid">
				<div class="fifteen wide centered column">
					<div class="ui grid">
						<div class="ui inverted dimmer" id="filter_loader">
							<div class="ui loader"></div>
						</div>
						<div class="sixteen wide column" id="wrap_filter">
							<form id="form_transaction" action="<?php echo base_url() ?>index.php/C_setting_harga/save_setting_harga" method="post">
							<table id="pos_data_tabel" class="ui celled table" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th>No</th>
										<th>Karat</th>
										<th>Dari Berat (gr)</th>
										<th>Sampai Berat (gr)</th>
										<th>Min Jual (%)</th>
										<th>Max Jual (%)</th>
										<th>Min Beli (%)</th>
										<th>Max Beli (%)</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									$number = 1;
									foreach($setting as $s){
									?>
									<tr>
										<td class="center aligned"><?php echo $number ?></td>
										<td class="center aligned"><?php echo $s->karat_name ?></td>
										<td><input class="form-control form-control-sm form-pos" type="text" onkeyup=valToCurrency("<?php echo $s->id ?>","1") name="from_<?php echo $s->id ?>" id="input_<?php echo $s->id ?>_1" value="<?php echo number_format($s->dari_berat,3,".",",") ?>"></td>
										<td><input class="form-control form-control-sm form-pos" type="text" onkeyup=valToCurrency("<?php echo $s->id ?>","2") name="to_<?php echo $s->id ?>" id="input_<?php echo $s->id ?>_2" value="<?php echo number_format($s->sampai_berat,3,".",",") ?>"></td>
										<td><input class="form-control form-control-sm form-pos" type="text" onkeyup=valToCurrency("<?php echo $s->id ?>","3") name="min_<?php echo $s->id ?>" id="input_<?php echo $s->id ?>_3" value="<?php echo number_format($s->min_persen,3,".",",") ?>"></td>
										<td><input class="form-control form-control-sm form-pos" type="text" onkeyup=valToCurrency("<?php echo $s->id ?>","4") name="max_<?php echo $s->id ?>" id="input_<?php echo $s->id ?>_4" value="<?php echo number_format($s->max_persen,3,".",",") ?>"></td>
										<td><input class="form-control form-control-sm form-pos" type="text" onkeyup=valToCurrency("<?php echo $s->id ?>","5") name="min_beli_<?php echo $s->id ?>" id="input_<?php echo $s->id ?>_5" value="<?php echo number_format($s->min_persen_beli,3,".",",") ?>"></td>
										<td><input class="form-control form-control-sm form-pos" type="text" onkeyup=valToCurrency("<?php echo $s->id ?>","6") name="max_beli_<?php echo $s->id ?>" id="input_<?php echo $s->id ?>_6" value="<?php echo number_format($s->max_persen_beli,3,".",",") ?>"></td>
									</tr>	
									<?php 
										$number = $number + 1;
									} ?>
								</tbody>
							</table>
							</form>
							<div class="ui positive right floated labeled icon button" id="btn-save" onclick="saveTrans()">
								<i class="save icon"></i> Simpan
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="ui modal mini" id="wrap_modal"></div>
</body>
<script>
	var exeTrans = false;
	
	function valToCurrency(idRow,idColumn){
		var x = event.keyCode;
		
		jumlahVal = $("#input_"+idRow+"_"+idColumn).val();
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
			
			document.getElementById("input_"+idRow+"_"+idColumn).value = beforeComma+'.'+afterComma;
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById("input_"+idRow+"_"+idColumn).value = jumlahVal;
		}
	}
	
	function saveTrans(){
		document.getElementById("btn-save").className += " loading";
		
		$.ajax({
			url: $('#form_transaction').attr('action'),
			type: 'post',
			data: $('#form_transaction').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					document.getElementById("wrap_modal").innerHTML = response.message;
					$('.ui.modal').modal('show');
					window.setTimeout(function(){
						window.location= response.lokasi;
					}, 3000);
					
					document.getElementById("btn-save").classList.remove("loading");
				}else{
					alert('Gagal Simpan Data!');
					window.location= response.lokasi;
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
</script>
</html>

