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
					  <div class="active section">Posisi Stock Pajangan</div>
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
							<form class="ui form form-javascript" id="form_filter" action="<?php echo base_url() ?>index.php/C_posisi_pajangan/filter" method="post">
							<div class="fields">
								<div class="four wide field">
									<label>Per Tanggal</label>
									<input type="text" name="per_tanggal" id="per_tanggal" readonly>
								</div>
								<div class="two wide field">
									<label>Dari Box</label>
									<select class="fluid dropdown" name="filter_box_from" id="filter_box_from">
										<?php foreach($box as $b){ ?>
										<option value="<?php echo $b->id ?>">BOX <?php echo $b->nama_box ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="two wide field">
									<label>Sampai Box</label>
									<select class="fluid dropdown" name="filter_box_to" id="filter_box_to">
										<?php 
											$total = count($box);
											$number = 1;
											foreach($box as $b){ 
												if($total == $number){
													$selected = 'selected';
												}else{
													$selected = '';
												}
										?>
										
										<option value="<?php echo $b->id ?>" <?php echo $selected; ?>>BOX <?php echo $b->nama_box ?></option>
										
										<?php 
												$number = $number+1;
											
											} 
										?>
									</select>
								</div>
								<div class="three wide field">
									<label style="visibility:hidden">-</label>
									<select class="fluid dropdown" name="detail_rekap" id="detail_rekap">
										<option value="D">Detail</option>
										<option value="R" selected="selected">Rekap</option>
									</select>
								</div>
								<div class="three wide field">
									<label style="visibility:hidden">-</label>
									<select class="fluid dropdown" name="box_karat" id="box_karat">
										<option value="B">per Box</option>
										<option value="K" selected="selected">per Karat</option>
										<option value="C">per Kelompok</option>
									</select>
								</div>
								<div class="two wide field">
									<label style="visibility:hidden">-</label>
									<div class="ui fluid icon green button filter-input" id="btn_filter" onclick="filterTrans()" title="Filter">
										<i class="filter icon"></i> Filter
									</div>
								</div>
							</div>
							</form>
						</div>
						<div class="sixteen wide column" id="wrap_filter"></div>
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
				alert('Error get data from ajax');
			}
		})
	}
	
	window.onload = function() {
		window.setTimeout(function(){
			filterTrans();
		}, 300);
	};
</script>
</html>

