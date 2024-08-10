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
					  <div class="active section">Box Kotak / Barang</div>
					</div>
				</div>
			</div>
			<div class="ui grid">
				<div class="ten wide centered column">
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

	function filterTrans(){
		document.getElementById("filter_loader").className += " active";
		
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_box/get_all_box/',
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
					alert("Gagal Filter!");
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('System Error, Hubungi Tim IT!');
			}
		})
	}
	
	function changeStatus(id,status){
		if(status == 'A'){
			var pesanStatus = 'Aktif';
		}else{
			var pesanStatus = 'Non Aktif';
		}
		var webUrl = "<?php echo base_url()?>";
		var view= '<div class="header">Ubah Status</div><div class="content"><p>Anda Ingin Mengubah Status Box Menjadi '+pesanStatus+'?</p></div><div class="actions"><div class="ui negative button">Tidak </div><button id="btn_confirm" class="ui green right labeled icon button" onclick=exeChangeStatus("'+id+'","'+status+'")>Ya<i class="check circle icon"></i></button></div>';
		
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
				url : webUrl+'/index.php/C_box/change_status/'+id+'/'+status,
				type: "GET",
				dataType: "JSON",
				success: function(response){
					if(response.success == false){
						document.getElementById("wrap_modal").innerHTML = response.message;
						filterTrans();
						window.setTimeout(function(){
							$('.ui.modal').modal('hide');
						}, 3000);
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

