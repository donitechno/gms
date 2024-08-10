<!DOCTYPE html>
<html lang="en">

<head>
   <?php $this->load->view('V_link') ?>
   <script src="<?php echo base_url() ?>assets/js/Chart.bundle.js"></script>
   <script src="<?php echo base_url() ?>assets/js/utils.js"></script>
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
						<div class="section"><i class="book icon"></i> Laporan</div>
						<i class="right chevron icon divider"></i>
						<div class="active section">Laporan Grafik Penjualan</div>
					</div>
				</div>
			</div>	
			<form class="ui form form-javascript" id="form_filter" action="<?php echo base_url() ?>index.php/C_sell_chart/filter/" method="post">
			<div class="ui grid">
				<div class="ui inverted dimmer" id="loader_form">
					<div class="ui large text loader">Loading</div>
				</div>
				<div class="thirteen wide centered column" style="padding-bottom:0">
					<div class="fields">
						<div class="four wide field">
							<label>Tgl Transaksi</label>
							<input type="text" name="from_date" id="from_date" readonly>
						</div>
						<div class="one wide field" style="text-align:center;margin-top:30px">
							<label>s.d</label>
						</div>
						<div class="four wide field">
							<label style="visibility:hidden">-</label>
							<input type="text" name="to_date" id="to_date" readonly>
						</div>
						<div class="two wide field">
							<label style="visibility:hidden">-</label>
							<select name="filter_data" id="filter_data">
								<option value="P">Pcs</option>
								<option value="I">Gram</option>
							</select>
						</div>
						<div class="three wide field">
							<label style="visibility:hidden">-</label>
							<select name="filter_data_2" id="filter_data_2">
								<option value="K">per Kelompok</option>
								<option value="G">Gabungan</option>
							</select>
						</div>
						<div class="two wide field">
							<label style="visibility:hidden">-</label>
							<div class="ui fluid icon green button filter-input" id="btn_filter" onclick="filterTrans()" title="Filter">
								<i class="filter icon"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="fourteen wide centered column" id="wrap_filter" style="padding-top:0">
					<canvas id="canvas"></canvas>
				</div>
			</div>
			</form>
		</div>
	</div>
	
	
</body>

<script>
	var exeTrans = false;
	var exeTambahBaris = false;
	var maxData = 5;
	var rowData = 1;
	
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
		$( "#tanggal_mutasi" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		
		$('#tanggal_mutasi').datepicker('setDate', 'today');
	});
	
	function filterTrans(){
		exeTrans = true;
		document.getElementById("btn_filter").className += " loading";
		document.getElementById('wrap_filter').innerHTML = '<canvas id="canvas"></canvas>';
		
		detailRekap = $("#rekap_detail").val();
		
		$.ajax({
			url: $('#form_filter').attr('action'),
			type: 'post',
			data: $('#form_filter').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					window.setTimeout(function(){
						if(response.rd2 == 'K'){
							var barChartData = response.hasil;
							if(response.rd == 'P'){
								var jarak = 1;
							}else{
								var jarak = 0;
							}
							var ctx = document.getElementById('canvas').getContext('2d');
							window.myBar = new Chart(ctx, {
								type: 'bar',
								data: barChartData,
								options: {
									scales: {
										yAxes: [{
											ticks: {
												stepSize: jarak,
												beginAtZero: true,
												callback: function(value, index, values) {
													var angka1 = value.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
													var angkaTulis = angka1.substring(0, angka1.length - 3);
													return angkaTulis;
												}
											}
										}]
									},
									responsive: true,
									legend: {
										position: 'top',
									},
									title: {
										display: false,
										text: ''
									}
								}
							});
						}else{
							var barChartData = response.hasil;
							if(response.rd == 'P'){
								var jarak = 1;
							}else{
								var jarak = 0;
							}
							var ctx = document.getElementById('canvas').getContext('2d');
							window.myBar = new Chart(ctx, {
								type: 'bar',
								data: barChartData,
								options: {
									title: {
										display: true,
										text: 'Chart.js Bar Chart - Stacked'
									},
									tooltips: {
										mode: 'index',
										intersect: false
									},
									responsive: true,
									scales: {
										xAxes: [{
											stacked: true,
										}],
										yAxes: [{
											stacked: true,
											ticks: {
												stepSize: jarak,
												beginAtZero: true,
												callback: function(value, index, values) {
													var angka1 = value.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
													var angkaTulis = angka1.substring(0, angka1.length - 3);
													return angkaTulis;
												}
											}
										}]
									},
									legend: {
										position: 'top',
									},
									title: {
										display: false,
										text: ''
									}
								}
							});
						}
						
						document.getElementById("btn_filter").classList.remove("loading");
						exeTrans = false;
					}, 0);
				}else{
					alert('filter gagal');
					document.getElementById("btn_filter").classList.remove("loading");
					exeTrans = false;
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
	
	/*
	var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		var color = Chart.helpers.color;
		var barChartData = {
			labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
			datasets: [{
				"label": 'Dataset 1',
				backgroundColor: "rgba(255,255,255,0)",
				borderColor: window.chartColors.red,
				borderWidth: 1,
				data: [
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor()
				]
			}, {
				label: 'Dataset 2',
				backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
				borderColor: window.chartColors.blue,
				borderWidth: 1,
				data: [
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor()
				]
			}]

		};

		window.onload = function() {
			var ctx = document.getElementById('canvas').getContext('2d');
			window.myBar = new Chart(ctx, {
				type: 'bar',
				data: barChartData,
				options: {
					responsive: true,
					legend: {
						position: 'top',
					},
					title: {
						display: true,
						text: 'Chart.js Bar Chart'
					}
				}
			});

		};*/
</script>
</html>

