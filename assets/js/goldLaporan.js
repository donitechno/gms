function getKaratLaporan(idMenu){
	accountNumber = $("#"+idMenu+"-accountnumber").val();
	
	if(accountNumber == '170002' || accountNumber == '170003' || accountNumber == '170005'){
		var x = document.getElementsByClassName("class-karat");
				
		for (var a = 0; a < x.length; a++){
			x[a].setAttribute('style','display:none');
		}
		
		document.getElementById(idMenu+"-idkarat").value = '1';
	}else{
		var x = document.getElementsByClassName("class-karat");
		
		for (var a = 0; a < x.length; a++){
			x[a].setAttribute('style','');
		}
		
		document.getElementById(idMenu+"-idkarat").value = 'All';
	}
}

function getDetailRekapJual(){
	detailRekap = $("#lapKasir-jual-detail_rekap").val();
	if(detailRekap == 'D'){
		document.getElementById("lapKasir-wrap_rekap_jual").setAttribute('style','visibility:hidden');
		document.getElementById("lapKasir-wrap_karat_jual").setAttribute('style','');
		document.getElementById("lapKasir-wrap_box_jual").setAttribute('style','');
	}else if(detailRekap == 'R'){
		document.getElementById("lapKasir-wrap_rekap_jual").setAttribute('style','');
		document.getElementById("lapKasir-wrap_karat_jual").setAttribute('style','visibility:hidden');
		document.getElementById("lapKasir-wrap_box_jual").setAttribute('style','visibility:hidden');
	}
}

function getDetailRekapBeli(){
	detailRekap = $("#lapKasir-beli-detail_rekap").val();
	if(detailRekap == 'D'){
		document.getElementById("lapKasir-wrap_rekap_beli").setAttribute('style','visibility:hidden');
		document.getElementById("lapKasir-wrap_karat_beli").setAttribute('style','');
	}else if(detailRekap == 'R'){
		document.getElementById("lapKasir-wrap_rekap_beli").setAttribute('style','');
		document.getElementById("lapKasir-wrap_karat_beli").setAttribute('style','visibility:hidden');
	}
}

function filterLapKasir(idMenu){
	document.getElementById(idMenu+"-wrap_lap").innerHTML = '';
	document.getElementById(idMenu+"-k-btnfilter").className += " loading";
	
	$.ajax({
		url: $('#'+idMenu+'-form-lap').attr('action'),
		type: 'post',
		data: $('#'+idMenu+'-form-lap').serialize(),
		dataType: 'json',
		success: function(response){
			if(response.success == true){
				document.getElementById(idMenu+"-wrap_lap").innerHTML = response.view;
				exeTrans = false;
				document.getElementById(idMenu+"-k-btnfilter").classList.remove("loading");
			}else{
				alert('filter gagal');
				exeTrans = false;
				document.getElementById(idMenu+"-k-btnfilter").classList.remove("loading");
			}
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('Error get data from ajax');
		}
	})
}

function filterJualKasir(idMenu){
	getDetailRekapJual();
	document.getElementById(idMenu+"-wrap_jual").innerHTML = '';
	document.getElementById(idMenu+"-btnfilterjual").className += " loading";
	
	$.ajax({
		url: $('#'+idMenu+'-form-jual').attr('action'),
		type: 'post',
		data: $('#'+idMenu+'-form-jual').serialize(),
		dataType: 'json',
		success: function(response){
			if(response.success == true){
				document.getElementById(idMenu+"-wrap_jual").innerHTML = response.view;
				exeTrans = false;
				document.getElementById(idMenu+"-btnfilterjual").classList.remove("loading");
			}else{
				alert('filter gagal');
				exeTrans = false;
				document.getElementById(idMenu+"-btnfilterjual").classList.remove("loading");
			}
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('Error get data from ajax');
		}
	})
}

function filterBeliKasir(idMenu){
	getDetailRekapBeli();
	document.getElementById(idMenu+"-wrap_beli").innerHTML = '';
	document.getElementById(idMenu+"-btnfilterbeli").className += " loading";
	
	$.ajax({
		url: $('#'+idMenu+'-form-beli').attr('action'),
		type: 'post',
		data: $('#'+idMenu+'-form-beli').serialize(),
		dataType: 'json',
		success: function(response){
			if(response.success == true){
				document.getElementById(idMenu+"-wrap_beli").innerHTML = response.view;
				exeTrans = false;
				document.getElementById(idMenu+"-btnfilterbeli").classList.remove("loading");
			}else{
				alert('filter gagal');
				exeTrans = false;
				document.getElementById(idMenu+"-btnfilterbeli").classList.remove("loading");
			}
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('Error get data from ajax');
		}
	})
}

function deleteTransJual(idTrans){
		$("#menuModal").removeClass("large");
		$("#menuModal").addClass("mini");
			
		var view= '<div class="header">Hapus Transaksi</div><div class="content"><p>Anda Ingin Menghapus Data Tersebut?</p></div><div class="actions"><div class="ui negative button">Tidak </div><button id="btn_confirm" class="ui green right labeled icon button" onclick=exeDeleteTransJual("'+idTrans+'")>Ya<i class="check circle icon"></i></button></div>';
		
		document.getElementById("menuModal").innerHTML = view;
		
		$('.ui.modal').modal({closable: false}).modal('show');
	}
	
function exeDeleteTransJual(idTrans){
	document.getElementById("btn_confirm").className += " loading";
	
	$.ajax({
		url : base_url+'/index.php/lapKasir/hapus_jual/'+idTrans,
		type: "GET",
		dataType: "JSON",
		success: function(response){
			if(response.success == true){
				$("#menuModal").removeClass("large");
				$("#menuModal").addClass("mini");
			
				document.getElementById("menuModal").innerHTML = response.message;
				filterJualKasir("lapKasir");
				window.setTimeout(function(){
					$('.ui.modal').modal('hide');
				}, 2000);
			}else{
				alert('Gagal Hapus!')
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error get data from ajax');
		}
	});
}

function deleteTransBeli(idTrans){
	$("#menuModal").removeClass("large");
	$("#menuModal").addClass("mini");
	
	var view= '<div class="header">Hapus Transaksi</div><div class="content"><p>Anda Ingin Menghapus Data Tersebut?</p></div><div class="actions"><div class="ui negative button">Tidak </div><button id="btn_confirm" class="ui green right labeled icon button" onclick=exeDeleteTransBeli("'+idTrans+'")>Ya<i class="check circle icon"></i></button></div>';
	
	document.getElementById("menuModal").innerHTML = view;
	
	$('.ui.modal').modal({closable: false}).modal('show');
}

function exeDeleteTransBeli(idTrans){
	document.getElementById("btn_confirm").className += " loading";
	
	$.ajax({
		url : base_url+'/index.php/lapKasir/hapus_beli/'+idTrans,
		type: "GET",
		dataType: "JSON",
		success: function(response){
			if(response.success == true){
				$("#menuModal").removeClass("large");
				$("#menuModal").addClass("mini");
				
				document.getElementById("menuModal").innerHTML = response.message;
				filterBeliKasir("lapKasir");
				window.setTimeout(function(){
					$('.ui.modal').modal('hide');
				}, 2000);
			}else{
				alert('Gagal Hapus!')
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error get data from ajax');
		}
	});
}

function filterTransaksiGrafik(idMenu){
	exeTrans = true;
	document.getElementById(idMenu+"-btnfilter").className += " loading";
	document.getElementById(idMenu+'-wrap_filter').innerHTML = '<canvas id="canvas"></canvas>';
	
	$.ajax({
		url: $('#'+idMenu+'-form-filter').attr('action'),
		type: 'post',
		data: $('#'+idMenu+'-form-filter').serialize(),
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
					
					document.getElementById(idMenu+"-btnfilter").classList.remove("loading");
					exeTrans = false;
				}, 0);
			}else{
				alert('filter gagal');
				document.getElementById(idMenu+"-btnfilter").classList.remove("loading");
				exeTrans = false;
			}
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('Error get data from ajax');
		}
	})
}
