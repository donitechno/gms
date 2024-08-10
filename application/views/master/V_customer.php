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

.td-head-cust{
	text-align: center !important;
	font-weight:600;
    border-top: 1px solid #bdc3c7 !important;
    border-bottom: 1px solid #bdc3c7;
    border-left: none !important;
    border-right: none !important;
    padding: 5px 5px 5px 5px !important;
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
					  <div class="active section">Data Customer</div>
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
	<div class="ui modal" id="wrap_modal">
		
	</div>
	<canvas id="canvas"></canvas>
</body>
<script>
	var exeTrans = false;
	
	$('.menu .item').tab();
	
	function filterTrans(){
		document.getElementById("filter_loader").className += " active";
		
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_customer/get_all_customer/',
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
	
	function viewDetail(phoneNumber){
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_customer/get_customer_trans/'+phoneNumber,
			type: 'post',
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					$("#wrap_modal").html(response.view);
					
					$('#modal-table').DataTable({
						"bPaginate": true,
						"bLengthChange": false,
						"pageLength": 1
					});
					
					$('#modal-table2').DataTable({
						"bPaginate": true,
						"bLengthChange": false,
						"pageLength": 1
					});
					
					$('.ui.modal')
					.modal({
						closable: false
					}).modal('show');
					
					$('.menu .item').tab();
				}else{
					alert('Gagal Terhubung ke System');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('System Error!');
			}
		});
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
			label: 'Dataset 1',
			backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
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

	};
	*/
</script>
</html>

