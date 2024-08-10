<!DOCTYPE html>
<html lang="en">
<head>
	<?php $this->load->view('V_link'); ?>
</head>
<body>
	<?php $this->load->view('V_header'); ?>
	<div class="pusher">
		<div class="ui container fluid">
			<div class="ui stackable menu" style="border-radius:0">
				<div class="item">
					<div class="ui breadcrumb">
					  <div class="section"><i class="users icon"></i> User</div>
					  <i class="right chevron icon divider"></i>
					  <div class="active section">Change Password</div>
					</div>
				</div>
			</div>
			<div class="ui grid">
				<div class="fifteen wide centered column">
					<form id="form_filter" action="<?php echo base_url() ?>index.php/C_change_pass/change_password" method="post">
					<div class="ui grid">
						<div class="ten wide centered column">
							<div class="ui error message" id="error_modal" style="display:none"></div>
							<div class="ui form">
								<?php foreach($user as $u){ ?>
								<div class="field">
									<label>Username</label>
									<input type="text" name="username" id="username" value="<?php echo $u->username ?>" readonly>
								</div>
								<div class="field">
									<label>Old Password</label>
									<input type="password" name="old_pass" id="old_pass" value="" placeholder="Ketikkan Password Lama">
								</div>
								<div class="field">
									<label>New Password</label>
									<input type="password" name="new_pass" id="new_pass" value="" placeholder="Ketikkan Password Baru">
								</div>
								<div class="field">
									<label>Repeat New Password</label>
									<input type="password" name="rep_new_pass" id="rep_new_pass" value="" placeholder="Ulangi Ketik Password Baru">
								</div>
								<?php } ?>
							</div>
							<div class="ui positive right floated labeled icon button" id="btn_save" onclick="saveEdit()" style="margin-top:15px">
								<i class="magic icon"></i> Update
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

	function saveEdit(){
		if(exeTrans == false){
			exeTrans = true;
			document.getElementById("btn_save").className += " loading";
			document.getElementById("error_modal").setAttribute('style','display:none');
			document.getElementById("error_modal").innerHTML = "";
			
			$.ajax({
				url: $('#form_filter').attr('action'),
				type: 'post',
				data: $('#form_filter').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == false){
						document.getElementById("error_modal").innerHTML = response.inputerror;
						document.getElementById("error_modal").setAttribute('style','');
						document.getElementById("btn_save").classList.remove("loading");
						exeTrans = false;
					}else{
						document.getElementById("wrap_modal").innerHTML = response.message;
						$('.ui.modal').modal('show');
						window.setTimeout(function(){
							window.location= response.lokasi;
						}, 2000);
						exeTrans = false;
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('System Error, Hubungi Tim IT!');
				}
			})
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
		
	};
</script>
</html>