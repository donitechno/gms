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
	
	.ui.form input:not([type]), .ui.form input[type=date], .ui.form input[type=datetime-local], .ui.form input[type=email], .ui.form input[type=file], .ui.form input[type=number], .ui.form input[type=password], .ui.form input[type=search], .ui.form input[type=tel], .ui.form input[type=text], .ui.form input[type=time], .ui.form input[type=url], .ui.form select, .filter-input, .filter-select, .filter-input{
		font-size:1em !important;
	}
	
	#filter_data_report{
		border:none;
	}
	
	#filter_data_report th{
		padding: 0.3em 0.78571429em;
		text-align:center;
		border:none;
		border-top:1px solid #000;
		border-bottom:1px solid #000;
		background:#FFF;
	}
	
	#filter_data_report td{
		font-size:1em;
		padding: 0.2em 0.78571429em;
		border:none;
	}
	
	.header-lap{
		border-top:0px !important;
		border-bottom:0px !important;
	}
	
	.td-total{
		border-top:double !important;
		border-bottom:1px solid #000 !important;
	}
	
	#modal-table td{
		font-size:0.9em;
		padding: 0.1em 0.78571429em;
	}
	
	.pilih{
		padding:.4em 1em !important;
	}
</style>
<body>
	<?php $this->load->view("V_header_sidebar") ?>
	<div class="pusher utama">
		<div class="ui container fluid">
			<div class="ui stackable menu no-print" style="border-radius:0">
				<div class="item">
					<div class="ui breadcrumb">
						<div class="section"><i class="hdd outline icon"></i> Kontrol Barang Pajangan</div>
						<i class="right chevron icon divider"></i>
						<div class="active section">Unlock Kontrol Harga</div>
					</div>
				</div>
			</div>
			<div class="ui grid">
				<div class="fifteen wide centered column">
					<div class="ui grid">
						<div class="ui inverted dimmer" id="filter_loader">
							<div class="ui loader"></div>
						</div>
						<div class="sixteen wide centered column no-print">
							<form class="ui form form-javascript" id="form_transaction" action="<?php echo base_url() ?>index.php/C_unlock_product/execute" method="post">
							<div class="ui grid">
								<div class="eight wide centered column no-print">
									<div class="fields">
										<div class="twelve wide field">
											<label>ID Pajangan</label>
											<input type="text" name="id_product" id="id_product" autofocus="on">
										</div>
										<div class="four wide field">
											<label style="visibility:hidden">-</label>
											<div class="ui fluid icon green button filter-input" id="btn_filter" onclick="cariTrans()" title="Filter">
												<i class="filter icon"></i> Cari
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="ui grid">
								<div class="sixteen wide column"><div class="ui error message" id="error_modal" style=""></div></div>
								<div class="sixteen wide column" id="wrap_filter"></div>
							<div class="ui grid">
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="ui modal" id="myModal"></div>
</body>
<script>
	var exeTrans = false;
	
	$('select.dropdown').dropdown();
	$('.menu .item').tab();
	
	$( function() {
		$( "#per_tanggal" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		$('#per_tanggal').datepicker('setDate', 'today');
	} );
	
	$('.form-javascript').on('keyup keypress', function(e){
		var keyCode = e.keyCode || e.which;
		if (keyCode === 13) {
			e.preventDefault();
			return false;
		}
	});
	
	function cariTrans(){
		var webUrl = "<?php echo base_url()?>";
		var productID = $("#id_product").val();
		if(productID == '' || productID == null){
			productID = '_';
		}
		
		if(productID != '_' && productID != ''){
			$.ajax({
				url : webUrl+'/index.php/C_unlock_product/get_product_from/'+productID,
				type: 'post',
				data: $('#form_transaction').serialize(),
				dataType: "JSON",
				success: function(response){
					if(response.success == true){
						if(response.found == 'single'){
							$("#wrap_filter").html(response.view);
							window.setTimeout(function(){
								document.getElementById("alasan_unlock").select();
							}, 500);
						}else{
							document.getElementById("myModal").classList.remove("mini");
							$("#myModal").html(response.view);
							
							$('#modal-table').DataTable({
								"bPaginate": true,
								"bLengthChange": false,
								"bInfo": false
							});
							
							$('.ui.modal')
							.modal({
							closable: false
							}).modal('show');
						}
					}else{
						window.setTimeout(function(){
							document.getElementById("myModal").classList.remove("mini");
							$("#myModal").html(response.view);
							
							$('#modal-table').DataTable({
								"bPaginate": false,
								"bInfo": false,
								"searching": false
							});
							
							$('.ui.modal')
							.modal({
							
							}).modal('show');
							
							//resetVal();
						}, 500);
					}
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert('System Error!');
				}
			});
		}
	}
	
	function setProduct(productID){
		var webUrl = "<?php echo base_url()?>";
		if(productID == '' || productID == null){
			productID = '_';
		}
		
		if(productID != '_' && productID != ''){
			$.ajax({
				url : webUrl+'/index.php/C_unlock_product/get_product_from/'+productID,
				type: 'post',
				data: $('#form_transaction').serialize(),
				dataType: "JSON",
				success: function(response){
					if(response.success == true){
						if(response.found == 'single'){
							$("#wrap_filter").html(response.view);
							window.setTimeout(function(){
								document.getElementById("alasan_unlock").select();
							}, 500);
							$('.ui.modal')
							.modal({
							closable: false
							}).modal('hide');
						}else{
							document.getElementById("myModal").classList.remove("mini");
							$("#myModal").html(response.view);
							
							$('#modal-table').DataTable({
								"bPaginate": true,
								"bLengthChange": false,
								"bInfo": false
							});
							
							$('.ui.modal')
							.modal({
							closable: false
							}).modal('show');
						}
					}else{
						window.setTimeout(function(){
							document.getElementById("myModal").classList.remove("mini");
							$("#myModal").html(response.view);
							
							$('#modal-table').DataTable({
								"bPaginate": false,
								"bInfo": false,
								"searching": false
							});
							
							$('.ui.modal')
							.modal({
							
							}).modal('show');
							
							//resetVal();
						}, 500);
					}
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert('System Error!');
				}
			});
		}
	}
	
	function executeTrans(){
		exeTrans = true;
		$("#error_modal").html("");
		document.getElementById("error_modal").setAttribute('style','display:none');
		document.getElementById("btn-save").className += " loading";
		
		$.ajax({
			url: $('#form_transaction').attr('action'),
			type: 'post',
			data: $('#form_transaction').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					document.getElementById("error_modal").setAttribute('style','display:none');
					document.getElementById("myModal").innerHTML = response.message;
					$('.ui.modal').modal({}).modal('show');
					window.setTimeout(function(){
						$('.ui.modal').modal('hide');
					}, 3000);
					exeTrans = false;
					document.getElementById("btn-save").classList.remove("loading");
				}else{
					$("#error_modal").html(response.inputerror);
					document.getElementById("error_modal").setAttribute('style','display:block');
					exeTrans = false;
					document.getElementById("btn-save").classList.remove("loading");
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
	
	window.onload = function() {
		
	};
</script>
</html>

