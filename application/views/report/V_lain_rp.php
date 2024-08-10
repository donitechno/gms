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
						<div class="section"><i class="book icon"></i> Laporan</div>
						<i class="right chevron icon divider"></i>
						<div class="active section">Laporan Account Lain (Rupiah)</div>
					</div>
				</div>
			</div>	
			<form class="ui form form-javascript" id="form_filter" action="<?php echo base_url() ?>index.php/C_lain_rp/filter/" method="post">
			<div class="ui grid">
				<div class="ui inverted dimmer" id="loader_form">
					<div class="ui large text loader">Loading</div>
				</div>
				<div class="fifteen wide centered column" style="padding-bottom:0">
					<div class="fields">
						<div class="three wide field">
							<label>Tgl Transaksi</label>
							<input type="text" name="from_date" id="from_date" readonly>
						</div>
						<div class="one wide field" style="text-align:center;margin-top:30px">
							<label>s.d</label>
						</div>
						<div class="three wide field">
							<label style="visibility:hidden">-</label>
							<input type="text" name="to_date" id="to_date" readonly>
						</div>
						<div class="five wide field">
							<label>Account</label>
							<select name="filter_account" id="filter_account">
								<?php foreach($account as $a){ ?>
								<option value="<?php echo $a->accountnumber ?>"><?php echo $a->accountname ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="three wide field">
							<label style="visibility:hidden">-</label>
							<select name="filter_dr" id="filter_dr">
								<option value="D">Detail</option>
								<option value="R">Rekap</option>
							</select>
						</div>
						<div class="one wide field">
							<label style="visibility:hidden">-</label>
							<div class="ui fluid icon green button filter-input" id="btn_filter" onclick="filterTrans()" title="Filter">
								<i class="filter icon"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="fifteen wide centered column" id="wrap_filter" style="padding-top:0">
				</div>
			</div>
			</form>
		</div>
	</div>
	
	<div class="ui modal mini" id="myModal"></div>
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
		
		detailRekap = $("#rekap_detail").val();
		
		$.ajax({
			url: $('#form_filter').attr('action'),
			type: 'post',
			data: $('#form_filter').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					document.getElementById("wrap_filter").innerHTML = response.view;
					document.getElementById("btn_filter").classList.remove("loading");
					exeTrans = false;
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
</script>
</html>

