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

.td-bold{
	font-weight:600;
}

.double-top{
	border-top:double #34495e !important;
}

.ui.table td{
	font-size: 1em !important;
    padding: 0.3em 0.78571429em;
}

.ui.table td{
	font-size: 1em !important;
    padding: 0.3em 0.78571429em;
}

.ui.table thead th{
	font-size: 1em !important;
    padding: 0.5em 0.78571429em;
}

.no-padding{
	padding-top:0px !important;
	padding-bottom:0px !important;
}
</style>
<body>
	<?php $this->load->view("V_header_sidebar") ?>
	<div class="pusher">
		<div class="ui fluid container no-print">
			<div class="ui stackable menu" style="border-radius:0">
				<div class="item">
					<div class="ui breadcrumb">
						<div class="section"><i class="book icon"></i> Laporan</div>
						<i class="right chevron icon divider"></i>
						<div class="active section">Laporan Tahunan</div>
					</div>
				</div>
			</div>
		</div>
		<form class="ui form form-javascript" id="form_transaction" action="<?php echo base_url() ?>index.php/C_lap_tahunan_2/lap_to_pdf" method="post">
		<div class="ui grid">
			<div class="ui inverted dimmer" id="loader_form">
				<div class="ui large text loader">Loading</div>
			</div>
			<div class="five wide centered column no-print" style="margin-top:15px">
				<div class="fields">
					<div class="ten wide field">
						<label>Tgl Laporan</label>
						<input type="text" name="tgl_transaksi" id="tgl_transaksi" readonly>
					</div>
					<div class="six wide field">
						<label style="visibility:hidden">-</label>
						<div class="ui fluid icon green button filter-input" id="btn_filter" onclick="filterTrans()" title="Filter">
							<i class="filter icon"></i> Filter
						</div>
					</div>
				</div>
			</div>
			<div class="fifteen wide centered column" id="wrap_filter">
				<div class="ui grid">
					<div class="sixteen wide column" id="wrap_download">
					</div>
					<div class="sixteen wide column no-padding">
						<div class="ui grid">
							<div class="eight wide column" id="wrap_filter_kiri"></div>
							<div class="eight wide column" id="wrap_filter_kanan"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		</form>
	</div>
	<div class="ui modal mini" id="myModal"></div>
</body>

<script>
	var exeTrans = false;
	var flagTrans = 'input';
	
	$(function(){
		$( "#tgl_transaksi" ).datepicker({
			dateFormat: 'dd MM yy'
		});
		
		$('#tgl_transaksi').datepicker('setDate', 'today');
	});
	
	function filterTrans(){
		var webUrl = "<?php echo base_url()?>";
		var dateReport = $("#tgl_transaksi").val();
		
		var alamat = webUrl+'index.php/C_lap_tahunan_2/lap_to_pdf/'+dateReport;
		window.open(alamat,"_blank");
	}
</script>
</html>

