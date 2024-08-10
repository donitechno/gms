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
	<?php $this->load->view('V_header_sidebar'); ?>
	<div class="pusher">
		<div class="ui container fluid">
			<div class="ui stackable menu" style="border-radius:0">
				<div class="item">
					<div class="ui breadcrumb">
					  <div class="section"><i class="suitcase icon"></i> Master Data</div>
					  <i class="right chevron icon divider"></i>
					  <div class="active section">Nama Barang Pajangan</div>
					</div>
				</div>
				<div class="right menu">
					<button class="ui linkedin button btn-head" onclick=addForm()>
						<i class="add icon"></i> Tambah Data
					</button>
					<button class="ui green button btn-head" onclick=getImportForm()>
						<i class="folder icon"></i> Import Dari Excel
					</button>
				</div>
			</div>
			<div class="ui grid">
				<div class="fifteen wide centered column">
					<form id="form_filter" action="<?php echo base_url() ?>index.php/C_nama_barang/filter_nama_barang" method="post" onchange="filterTrans()">
					<div class="ui grid">
						<div class="ten wide centered column">
							<div class="ui form">
								<div class="field">
									<select class="ui fluid dropdown" name="filter_category" id="filter_category" onchange="filterTrans()">
										<option value="All">-- Lihat Seluruh Kelompok Barang --</option>
										<?php foreach($category as $c){ ?>
										<option value="<?php echo $c->id ?>"><?php echo $c->category_name ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
					</div>
					</form>
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

	$('select.dropdown').dropdown();
	
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
			document.getElementById(nextID).focus();
		}
	}
	
	function filterTrans(){
		document.getElementById("filter_loader").className += " active";
		
		$.ajax({
			url: $('#form_filter').attr('action'),
			type: 'post',
			data: $('#form_filter').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					document.getElementById("wrap_filter").innerHTML = response.view;
					
					$('#filter_data_tabel').DataTable();
				
					document.getElementById("filter_loader").classList.remove("active");
				}else{
					alert("Gagal Filter Stock In!");
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
			url : webUrl+'/index.php/C_nama_barang/get_product_form',
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
			url : webUrl+'/index.php/C_nama_barang/get_product_data/'+idData,
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
	
	function getImportForm(){
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_nama_barang/get_import_form/',
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					document.getElementById("wrap_modal").innerHTML = response.view;
					
					$('.ui.modal')
						.modal({
						closable: false
					}).modal('show');
				}else{
					alert('Gagal Koneksi ke Server!');
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('System Error, Hubungi Tim IT!');
			}
		});
	}
	
	window.onload = function() {
		window.setTimeout(function(){
			filterTrans();
		}, 300);
	};
</script>
</html>