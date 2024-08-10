<!DOCTYPE html>
<html lang="en">

<head>
   <?php $this->load->view('V_link') ?>
</head>

<body>
	<?php $this->load->view("V_header") ?>
	<div class="pusher">
		<div class="ui container fluid">
			<div class="ui stackable menu" style="border-radius:0">
				<div class="item">
					<div class="ui breadcrumb">
						<div class="section"><i class="book icon"></i> Laporan</div>
						<i class="right angle icon divider"></i>
						<div class="active section">Kartu Piutang Karyawan</div>
					</div>
				</div>
			</div>	
			<form class="ui form form-javascript" id="form_filter" action="<?php echo base_url() ?>index.php/C_kartu_pik/filter/" method="post">
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
								<option value="All">-- All --</option>
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
	
	function getKeterangan(idRow){
		jenisTrans = $("#input_"+idRow+"_1").val();
		innerOptGlobal = document.getElementById("input_"+idRow+"_2");
		innerOpt = innerOptGlobal.getElementsByTagName("option");
		innerOptVal = innerOpt[innerOptGlobal.selectedIndex].innerHTML;
		ketVal = innerOptVal.replace("TITIPAN PELANGGAN - ","")
		
		if(ketVal != '-- Pilih Account --'){
			if(jenisTrans == 'I'){
				descTrans = 'PELANGGAN SETOR TITIPAN RUPIAH - '+ketVal;
			}else{
				descTrans = 'PELANGGAN TARIK TITIPAN RUPIAH - '+ketVal;
			}
			
			document.getElementById("input_"+idRow+"_3").value = descTrans;
		}else{
			document.getElementById("input_"+idRow+"_3").value = "";
		}
	}
	
	function entToHeader(idName){
		var x = event.keyCode;
		
		if(x == 13){
			document.getElementById(idName).focus();
		}
	}
	
	function entToForm(idName){
		document.getElementById(idName).focus();
	}
	
	function entToTab(idRow,idElement){
		var x = event.keyCode;
		idElement = parseFloat(idElement);
		idElement = idElement + 1;
		if(x == 13){
			document.getElementById("input_"+idRow+"_"+idElement+"").focus();
		}
	}
	
	function entToTabModal(idElement){
		var x = event.keyCode;
		if(x == 13){
			document.getElementById("input_data_"+idElement).focus();	
		}	
	}
	
	function valueToCurrency(idRow){
		jumlahVal = $("#input_"+idRow+"_4").val();
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
			
			document.getElementById("input_"+idRow+"_4").value = beforeComma+'.'+afterComma;
			
			countTotal();
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById("input_"+idRow+"_4").value = jumlahVal;
			
			countTotal();
		}
	}
	
	function begBalToCurrency(){
		jumlahVal = $("#input_data_2").val();
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
			
			document.getElementById("input_data_2").value = beforeComma+'.'+afterComma;
		}else{
			jumlahVal = parseFloat(jumlahVal);
			jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
				jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
			}
			
			document.getElementById("input_data_2").value = jumlahVal;
		}
	}
	
	function countTotal(){
		totalGram = 0;
		for (var a = 1; a <= rowData; a++) {
			jumlahVal = $("#input_"+a+"_4").val();
			
			if(jumlahVal == '' || jumlahVal == 'NaN'){
				jumlahVal = "0";
			}
			
			jumlahVal = jumlahVal.replace(/[^0-9.]/g, "");
			jumlahVal = parseFloat(jumlahVal);
			
			totalGram = totalGram + jumlahVal;
		}
		
		totalGram = parseFloat(totalGram);
		totalGram = totalGram.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		
		if(totalGram.substr(totalGram.length - 3) == '.00'){
			totalGram = totalGram.substring(0, totalGram.length - 3);
		}
		
		document.getElementById("total_gram").innerHTML = totalGram;
	}
	
	function resetVal(){
		if(rowData > 1){
			for (var a = 2; a <= rowData; a++) {
				$("#pos_tr_"+a).remove();
			}
		}
				
		rowData = 1;
		
		document.getElementById("input_"+rowData+"_1").value = 'I';
		document.getElementById("input_"+rowData+"_2").value = '';
		document.getElementById("input_"+rowData+"_3").value = '';
		document.getElementById("input_"+rowData+"_4").value = '';
		
		exeTambahBaris = false;
		window.setTimeout(function(){
			document.getElementById("input_"+rowData+"_1").focus();
		}, 500);
		
		countTotal();
	}
	
	function saveTransaksi(){
		if(exeTrans == false){
			exeTrans = true;
			
			document.getElementById("btn-save").className += " loading";
			
			var webUrl = "<?php echo base_url()?>";
			
			$.ajax({
				url: $('#form_transaction').attr('action')+'/'+rowData,
				type: 'post',
				data: $('#form_transaction').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						swal({
							type: "success",
							title: "Berhasil Input Titipan!",
							text: "",
							timer: 2000,
							showConfirmButton: false
						});
						
						exeTrans = false;
						
						document.getElementById("btn-save").classList.remove("loading");
						
						resetVal();
					}else{
						swal({
							html:true,
							type: "error",
							title: "",
							text: response.pesan_error,
							timer: 2000,
							showConfirmButton: false
						});
						
						exeTrans = false;
						
						document.getElementById("btn-save").classList.remove("loading");
						
						document.getElementById("input_"+rowData+"_1").focus();
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
	
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
	
	/*-- MENGELUARKAN FORM ADD --*/
	function addAccount(){
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_titipan_rp/get_input_form',
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					$("#myModal").html(response.view);
					
					$('.ui.modal')
						.modal({
						closable: false
					}).modal('show');
					
					window.setTimeout(function(){
						document.getElementById("input_data_1").focus();
					}, 500);
				}else{
					alert('Gagal Koneksi ke Server!');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('System Error!');
			}
		});
	}
	
	function saveAdd(){
		if(exeTrans == false){
			exeTrans = true;
			
			document.getElementById("input_data_3").className += " loading";
			
			var webUrl = "<?php echo base_url()?>";
			
			$.ajax({
				url: $('#form_add').attr('action'),
				type: 'post',
				data: $('#form_add').serialize(),
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						swal({
							type: "success",
							title: "Berhasil Input Account Baru!",
							text: "",
							showConfirmButton: false
						});
						
						$('#myModal').modal('hide');
						exeTrans = false;
						
						resetVal();
						window.setTimeout(function(){
							window.location= response.lokasi;
						}, 750);
						
						document.getElementById("input_data_3").classList.remove("loading");
					}else{
						exeTrans = false;
						document.getElementById("input_data_3").classList.remove("loading");
						document.getElementById("input_data_1").focus();
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
	
	function deleteTrans(idTrans){
		var webUrl = "<?php echo base_url()?>";
		
		swal({
			title: "",
			text: "Apakah Anda Ingin Menghapus Transaksi Tersebut?",
			type: "info",
			showCancelButton: true,
			closeOnConfirm: false,
			showLoaderOnConfirm: true,
		},
		function(){
			$.ajax({
				url : webUrl+'/index.php/C_titipan_rp/hapus/'+idTrans,
				type: "GET",
				dataType: "JSON",
				success: function(response)
				{
					if(response.success == true){
						swal({
							type: "success",
							title: "Delete Sukses!",
							text: "",
							timer: 1500,
							showConfirmButton: false
						});
					}else{
						swal({
							type: "error",
							title: "Delete Gagal!",
							text: "",
							timer: 1500,
							showConfirmButton: false
						});
					}
					
					filterTrans();
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert('Error get data from ajax');
				}
			});
		});
	}
	
	window.onload = function() {
		filterTrans();
	};
</script>
</html>

