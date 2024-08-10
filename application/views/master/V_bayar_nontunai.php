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
</style>
<body>
	<?php $this->load->view("V_header_sidebar") ?>
	<div class="pusher">
		<div class="ui container fluid">
			<div class="ui stackable menu" style="border-radius:0">
				<div class="item">
					<div class="ui breadcrumb">
					  <div class="section"><i class="suitcase icon"></i> Master Data</div>
					  <i class="right chevron icon divider"></i>
					  <div class="active section">Pembayaran Non Tunai</div>
					</div>
				</div>
				<div class="right menu">
					<button class="ui linkedin button btn-head" onclick=addForm()>
						<i class="add icon"></i> Tambah Data
					</button>
				</div>
			</div>
			<div class="ui grid">
				<div class="fifteen wide centered column">
					<div class="ui grid">
						<div class="ui inverted dimmer" id="filter_loader">
							<div class="ui loader"></div>
						</div>
						<div class="sixteen wide column" id="wrap_filter"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="ui modal mini" id="wrap_modal">
		
	</div>
</body>
<script>
	var exeTrans = false;
	
	function valToCurrency(idName){
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
			
			document.getElementById(idName).value = beforeComma;
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById(idName).value = jumlahVal;
		}
	}
	
	function notEnter(){
		$('.form-javascript').on('keyup keypress', function(e){
			var keyCode = e.keyCode || e.which;
			if (keyCode === 13) {
				e.preventDefault();
				return false;
			}
		});
	}
	
	function entToNextID(nextID){
		var x = event.keyCode;
		if(x == 13){
			document.getElementById(nextID).select();
		}
	}
	
	function filterTrans(){
		document.getElementById("filter_loader").className += " active";
		
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_bayar_nontunai/get_all_pembayaran/',
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					document.getElementById("wrap_filter").innerHTML = response.view;
					
					$('#filter_data_tabel').DataTable({
						//"sPaginationType": "full_numbers"
					});
				
					document.getElementById("filter_loader").classList.remove("active");
				}else{
					alert("Gagal Filter Data!");
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('System Error, Hubungi Tim IT!');
			}
		})
	}
	
	function addForm(){
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_bayar_nontunai/get_product_form',
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					document.getElementById("wrap_modal").innerHTML = response.view;
					
					$('.ui.modal')
						.modal({
						closable: false
					}).modal('show');
					
					notEnter();
					
					$('#select_category').selectize({
						onChange: function(value) {
							entToNextID("btn_save");
						}
					});
				}else{
					alert('Gagal Koneksi ke Server!');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('System Error, Hubungi Tim IT!');
			}
		});
	}
	
	function saveForm(){
		if(exeTrans == false){
			exeTrans = true;
			document.getElementById("btn_save").className += " loading";
			document.getElementById("error_modal").setAttribute('style','display:none');
			document.getElementById("error_modal").innerHTML = "";
			
			$.ajax({
				url: $('#form_add').attr('action')+'/input',
				type: 'post',
				data: $('#form_add').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == false){
						document.getElementById("error_modal").innerHTML = response.inputerror;
						document.getElementById("error_modal").setAttribute('style','');
						document.getElementById("btn_save").classList.remove("loading");
						exeTrans = false;
					}else{
						document.getElementById("wrap_modal").innerHTML = response.message;
						filterTrans();
						window.setTimeout(function(){
							$('.ui.modal').modal('hide');
						}, 1000);
						exeTrans = false;
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('System Error, Hubungi Tim IT!');
				}
			})
		}
	}
	
	function editForm(idData){
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_bayar_nontunai/get_bayar_data/'+idData,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					document.getElementById("wrap_modal").innerHTML = response.view;
					
					$('.ui.modal')
						.modal({
						closable: false
					}).modal('show');
					
					notEnter();
					
					$('#select_category').selectize({
						onChange: function(value) {
							entToNextID("btn_save");
						}
					});
				}else{
					alert('Gagal Koneksi ke Server!');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('System Error, Hubungi Tim IT!');
			}
		});
	}
	
	function saveEditForm(){
		if(exeTrans == false){
			exeTrans = true;
			document.getElementById("btn_save").className += " loading";
			document.getElementById("error_modal").setAttribute('style','display:none');
			document.getElementById("error_modal").innerHTML = "";
			
			$.ajax({
				url: $('#form_edit').attr('action')+'/edit',
				type: 'post',
				data: $('#form_edit').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == false){
						document.getElementById("error_modal").innerHTML = response.inputerror;
						document.getElementById("error_modal").setAttribute('style','');
						document.getElementById("btn_save").classList.remove("loading");
						exeTrans = false;
					}else{
						document.getElementById("wrap_modal").innerHTML = response.message;
						filterTrans();
						window.setTimeout(function(){
							$('.ui.modal').modal('hide');
						}, 1000);
						exeTrans = false;
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('System Error, Hubungi Tim IT!');
				}
			})
		}
	}
	
	function changeStatus(id,status){
		if(status == 'A'){
			var pesanStatus = 'Aktif';
		}else{
			var pesanStatus = 'Non Aktif';
		}
		var webUrl = "<?php echo base_url()?>";
		var view= '<div class="header">Ubah Status</div><div class="content"><p>Anda Ingin Mengubah Status Kelompok Barang Menjadi '+pesanStatus+'?</p></div><div class="actions"><div class="ui negative button">Tidak </div><button id="btn_confirm" class="ui green right labeled icon button" onclick=exeChangeStatus("'+id+'","'+status+'")>Ya<i class="check circle icon"></i></button></div>';
		
		document.getElementById("wrap_modal").innerHTML = view;
		
		$('.ui.modal')
			.modal({
			closable: false
		}).modal('show');		
	}
	
	function exeChangeStatus(id,status){
		if(exeTrans == false){
			exeTrans = true;
			document.getElementById("btn_confirm").className += " loading";
			var webUrl = "<?php echo base_url()?>";
		
			$.ajax({
				url : webUrl+'/index.php/C_bayar_nontunai/change_status/'+id+'/'+status,
				type: "GET",
				dataType: "JSON",
				success: function(response){
					if(response.success == false){
						document.getElementById("btn_confirm").classList.remove("loading");
						exeTrans = false;
					}else{
						document.getElementById("wrap_modal").innerHTML = response.message;
						filterTrans();
						window.setTimeout(function(){
							$('.ui.modal').modal('hide');
						}, 1000);
						exeTrans = false;
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('System Error, Hubungi Tim IT!');
				}
			})
		}
	}
	
	window.onload = function() {
		window.setTimeout(function(){
			filterTrans();
		}, 300);
	};
</script>
</html>

