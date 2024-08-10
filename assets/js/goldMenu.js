var base_url = window.location.origin+"/gms";
var arrayMenu = ["","","","",""];

//Menu Transaksi
$("#kelompokBarang").click(function(){
	var idMenu = "kelompokBarang";
	var namaTab = 'Kelompok Barang';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#namaBarang").click(function(){
	var idMenu = "namaBarang";
	var namaTab = 'Nama Barang Pajangan';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#boxBarang").click(function(){
	var idMenu = "boxBarang";
	var namaTab = 'Box Kotak / Barang';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#nonTunai").click(function(){
	var idMenu = "nonTunai";
	var namaTab = 'Pembayaran Non Tunai';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#dataCustomer").click(function(){
	var idMenu = "dataCustomer";
	var namaTab = 'Data Customer';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

//Menu Pajangan
$("#stockIn").click(function(){
	var idMenu = "stockIn";
	var namaTab = 'Stock In';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
		stockInRowData = 1;
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#stockOut").click(function(){
	var idMenu = "stockOut";
	var namaTab = 'Stock Out';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
		stockOutRowData = 1;
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#pindahBox").click(function(){
	var idMenu = "pindahBox";
	var namaTab = 'Pindah Box';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
		pindahBoxRowData = 1;
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#posisiPajangan").click(function(){
	var idMenu = "posisiPajangan";
	var namaTab = 'Posisi Pajangan';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#unlockHarga").click(function(){
	var idMenu = "unlockHarga";
	var namaTab = 'Unlock Harga';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#historyProduct").click(function(){
	var idMenu = "historyProduct";
	var namaTab = 'History Product';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#dataProduct").click(function(){
	var idMenu = "dataProduct";
	var namaTab = 'Data Product';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

//Menu Transaksi
$("#transCabang").click(function(){
	var idMenu = "transCabang";
	var namaTab = 'Trans Antarcabang';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
		transCabangRowData = 1;
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#mutasiKas").click(function(){
	var idMenu = "mutasiKas";
	var namaTab = 'Mutasi Kas/Bank';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
		mutasiGramRowData = 1;
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#mutasiGram").click(function(){
	var idMenu = "mutasiGram";
	var namaTab = 'Mutasi Emas';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
		mutasiGramRowData = 1;
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#titipanRp").click(function(){
	var idMenu = "titipanRp";
	var namaTab = 'Titipan Rp';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#titipanGram").click(function(){
	var idMenu = "titipanGram";
	var namaTab = 'Titipan Gram';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#jurnalUmum").click(function(){
	var idMenu = "jurnalUmum";
	var namaTab = 'Jurnal Umum';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
		jurnalUmumRowData = 1
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

//Menu Laporan
$("#lapKasir").click(function(){
	var idMenu = "lapKasir";
	var namaTab = 'Lap Jual Beli';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#lapPajangan").click(function(){
	var idMenu = "lapPajangan";
	var namaTab = 'Laporan Pajangan';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#lapReparasi").click(function(){
	var idMenu = "lapReparasi";
	var namaTab = 'Laporan Reparasi';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#lapSaldoGr").click(function(){
	var idMenu = "lapSaldoGr";
	var namaTab = 'Laporan Saldo Gr';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#kartuPik").click(function(){
	var idMenu = "kartuPik";
	var namaTab = 'Lap Piutang Kry';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#lapAccountRp").click(function(){
	var idMenu = "lapAccountRp";
	var namaTab = 'Lap Account Rp';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#lapAccountGr").click(function(){
	var idMenu = "lapAccountGr";
	var namaTab = 'Lap Account Gram';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#lapGrafik").click(function(){
	var idMenu = "lapGrafik";
	var namaTab = 'Grafik Penjualan';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#bestCS").click(function(){
	var idMenu = "bestCS";
	var namaTab = 'Best CS';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#lapPesananPer").click(function(){
	var idMenu = "lapPesananPer";
	var namaTab = 'Pesanan Per Tgl';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#lapKasirHarian").click(function(){
	var idMenu = "lapKasirHarian";
	var namaTab = 'Lap Kasir';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#lapNeracaHarian").click(function(){
	var idMenu = "lapNeracaHarian";
	var namaTab = 'Lap Neraca';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

$("#lapTahunan").click(function(){
	var idMenu = "lapTahunan";
	var namaTab = 'Lap Tahunan';
	var exeMenu = exeMenuTrans(idMenu,namaTab);
	
	if(exeMenu == "success1" || exeMenu == "success2"){
		getContent(idMenu);
	}
	
	$('.menu .item').tab();
	$('.ui.sidebar').sidebar('hide');
});

function exeMenuTrans(idMenu,namaTab){
	var flagTampilMenu = cekTampilMenu(idMenu);
	if(flagTampilMenu == true){
		
		var flagKosongMenu = false;
		for(i=0;i<arrayMenu.length;i++){
			if(arrayMenu[i] == ""){
				flagKosongMenu = true;
			}
		}
		
		if(flagKosongMenu == true){
			isiTab = '<a id="'+idMenu+'-tab" class="item" data-tab="'+idMenu+'"><span style="width:90%">'+namaTab+'</span><span class="close-tab" style="width:10%;cursor:pointer;text-align:center" onclick=closeTab("'+idMenu+'")>x</span></a>';
			isiContent = '<div id="'+idMenu+'-content" class="ui bottom attached tab segment" data-tab="'+idMenu+'"><div class="menunggu" style="margin-top:80px;"><div class="loading-bar"></div><div class="loading-bar"></div><div class="loading-bar"></div><div class="loading-bar"></div></div></div>';
			
			$("#menuTab").append(isiTab);
			$("#menuContent").append(isiContent);
			
			for(i=0;i<arrayMenu.length;i++){
				if(arrayMenu[i] == ""){
					arrayMenu[i] = idMenu;
					
					for(i=0;i<arrayMenu.length;i++){
						if(arrayMenu[i] != ""){
							var idName = arrayMenu[i];
							var idTab = "#"+idName+"-tab";
							var idContent = "#"+idName+"-content";
							
							$(idTab).removeClass("active");
							$(idContent).removeClass("active");
						}
					}
					
					for(i=0;i<arrayMenu.length;i++){
						if(arrayMenu[i] == idMenu){
							var idName = arrayMenu[i];
							var idTab = "#"+idName+"-tab";
							var idContent = "#"+idName+"-content";
							
							$(idTab).addClass("active");
							$(idContent).addClass("active");
						}
					}
					return "success1";
				}
			}
		}else{
			isiTab = '<a id="'+idMenu+'-tab" class="item" data-tab="'+idMenu+'"><span style="width:90%">'+namaTab+'</span><span class="close-tab" style="width:10%;cursor:pointer;text-align:center" onclick=closeTab("'+idMenu+'")>x</span></a>';
			isiContent = '<div id="'+idMenu+'-content" class="ui bottom attached tab segment" data-tab="'+idMenu+'"><div class="menunggu" style="margin-top:80px;"><div class="loading-bar"></div><div class="loading-bar"></div><div class="loading-bar"></div><div class="loading-bar"></div></div></div>';
			
			var idName = arrayMenu[0];
			var idNav = "#"+idName+"-tab";
			var idContent = "#"+idName+"-content";
			
			$(idNav).remove();
			$(idContent).remove();
			
			var arrayTemp = ["","","","",""];
			for(i=0;i<arrayMenu.length;i++){
				if(i != arrayMenu.length-1){
					arrayTemp[i] = arrayMenu[i+1];
				}
			}
			
			arrayTemp[arrayMenu.length-1] = idMenu;
			arrayMenu = arrayTemp;
			
			$("#menuTab").append(isiTab);
			$("#menuContent").append(isiContent);
			
			for(i=0;i<arrayMenu.length;i++){
				if(arrayMenu[i] != ""){
					var idName = arrayMenu[i];
					var idTab = "#"+idName+"-tab";
					var idContent = "#"+idName+"-content";
					
					$(idTab).removeClass("active");
					$(idContent).removeClass("active");
				}
			}
			
			for(i=0;i<arrayMenu.length;i++){
				if(arrayMenu[i] == idMenu){
					var idName = arrayMenu[i];
					var idTab = "#"+idName+"-tab";
					var idContent = "#"+idName+"-content";
					
					$(idTab).addClass("active");
					$(idContent).addClass("active");
				}
			}
			
			return "success2";
		}
	}else{
		for(i=0;i<arrayMenu.length;i++){
			if(arrayMenu[i] != ""){
				var idName = arrayMenu[i];
				var idTab = "#"+idName+"-tab";
				var idContent = "#"+idName+"-content";
				
				$(idTab).removeClass("active");
				$(idContent).removeClass("active");
			}
		}
		
		for(i=0;i<arrayMenu.length;i++){
			if(arrayMenu[i] == idMenu){
				var idName = arrayMenu[i];
				var idTab = "#"+idName+"-tab";
				var idContent = "#"+idName+"-content";
				
				$(idTab).addClass("active");
				$(idContent).addClass("active");
			}
		}
		
		return "success3";
	}
}

function cekTampilMenu(idMenu){
	var hasilMenu = true;
	for(i=0;i<arrayMenu.length;i++){
		if(arrayMenu[i] == idMenu){
			hasilMenu = false;
			return hasilMenu;
		}
	}
	
	return hasilMenu;
}

function closeTab(idMenu){
	var idName = idMenu;
	var idNav = "#"+idName+"-tab";
	var idContent = "#"+idName+"-content";
	
	$(idNav).remove();
	$(idContent).remove();
	
	var arrayTemp = ["","","","",""];
	for(i=0;i<arrayTemp.length;i++){
		var berhenti = false;
		for(j=0;j<arrayMenu.length;j++){
			if(berhenti == false){
				if(arrayMenu[j] != "" && arrayMenu[j] != idMenu){
					arrayTemp[i] = arrayMenu[j];
					arrayMenu[j] = "";
					berhenti = true;
				}
			}
		}
	}
	
	arrayMenu = arrayTemp;
}

function getContent(idMenu){
	$.ajax({
		url: base_url+'/index.php/'+idMenu,
		
		type: 'post',
		data: $(this).serialize(),
		dataType: 'json',
		success: function(response){
			if(response.success == true){
				$("#"+idMenu+"-content").html(response.view);
				if(response.date == 1){
					$( function() {
						$("#"+idMenu+"-date").datepicker({
							dateFormat: 'dd MM yy'
						});
						$("#"+idMenu+"-date").datepicker('setDate', 'today');
					} );
				}else if(response.date == 2){
					$("#"+idMenu+"-datefrom").datepicker({
						dateFormat: 'dd MM yy'
					});
					$("#"+idMenu+"-datefrom").datepicker('setDate', 'today');
					
					$("#"+idMenu+"-dateto").datepicker({
						dateFormat: 'dd MM yy'
					});
					$("#"+idMenu+"-dateto").datepicker('setDate', 'today');
					
					$( function() {
						var dateFormat = "dd MM yy",
						from = $("#"+idMenu+"-datefrom")
						
						.datepicker({
							changeMonth: true,
							numberOfMonths: 1
						})
						.on( "change", function() {
						  to.datepicker( "option", "minDate", getDate( this ) );
						}),
						to = $("#"+idMenu+"-dateto").datepicker({
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
				}else if(response.date == 3){
					$( function(){
						$("#"+idMenu+"-dateinput").datepicker({
							dateFormat: 'dd MM yy'
						});
						$("#"+idMenu+"-dateinput").datepicker('setDate', 'today');
					});
					
					$("#"+idMenu+"-filterfromdate").datepicker({
						dateFormat: 'dd MM yy'
					});
					$("#"+idMenu+"-filterfromdate").datepicker('setDate', 'today');
					
					$("#"+idMenu+"-filtertodate").datepicker({
						dateFormat: 'dd MM yy'
					});
					$("#"+idMenu+"-filtertodate").datepicker('setDate', 'today');
					
					$( function() {
						var dateFormat = "dd MM yy",
						from = $("#"+idMenu+"-filterfromdate")
						
						.datepicker({
							changeMonth: true,
							numberOfMonths: 1
						})
						.on( "change", function() {
						  to.datepicker( "option", "minDate", getDate( this ) );
						}),
						to = $("#"+idMenu+"-filtertodate").datepicker({
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
				}else if(response.date == 4){
					//KAS
					$( function() {
						$("#"+idMenu+"-datekas").datepicker({
							dateFormat: 'dd MM yy'
						});
						$("#"+idMenu+"-datekas").datepicker('setDate', 'today');
					} );
					
					//JUAL
					$("#"+idMenu+"-jual-fromdate").datepicker({
						dateFormat: 'dd MM yy'
					});
					$("#"+idMenu+"-jual-fromdate").datepicker('setDate', 'today');
					
					$("#"+idMenu+"-jual-todate").datepicker({
						dateFormat: 'dd MM yy'
					});
					$("#"+idMenu+"-jual-todate").datepicker('setDate', 'today');
					
					$( function() {
						var dateFormat = "dd MM yy",
						from = $("#"+idMenu+"-jual-fromdate")
						
						.datepicker({
							changeMonth: true,
							numberOfMonths: 1
						})
						.on( "change", function() {
						  to.datepicker( "option", "minDate", getDate( this ) );
						}),
						to = $("#"+idMenu+"-jual-todate").datepicker({
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
					
					//BELI
					$("#"+idMenu+"-beli-fromdate").datepicker({
						dateFormat: 'dd MM yy'
					});
					$("#"+idMenu+"-beli-fromdate").datepicker('setDate', 'today');
					
					$("#"+idMenu+"-beli-todate").datepicker({
						dateFormat: 'dd MM yy'
					});
					$("#"+idMenu+"-beli-todate").datepicker('setDate', 'today');
					
					$( function() {
						var dateFormat = "dd MM yy",
						from = $("#"+idMenu+"-beli-fromdate")
						
						.datepicker({
							changeMonth: true,
							numberOfMonths: 1
						})
						.on( "change", function() {
						  to.datepicker( "option", "minDate", getDate( this ) );
						}),
						to = $("#"+idMenu+"-beli-todate").datepicker({
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
				}
				
				if(idMenu == "namaBarang"){
					filterNamaBarang(idMenu);
					$('#'+idMenu+'-category').dropdown();
				}else if(idMenu == 'boxBarang' || idMenu == 'dataCustomer'){
					$('#'+idMenu+'-table').DataTable({});
				}else if(idMenu == 'stockIn'){
					$('.menu .item').tab();
					document.getElementById("stockIn-from").focus();
					$('#'+idMenu+'-input_1_1').selectize();
					$('#'+idMenu+'-input_1_2').selectize();
					$('#'+idMenu+'-input_1_3').selectize();
					$('#'+idMenu+'-input_1_4').selectize();
				}else if(idMenu == 'stockOut'){
					$('.menu .item').tab();
				}else if(idMenu == 'pindahBox'){
					$('.menu .item').tab();
					document.getElementById("pindahBox-input_1_1").focus();
				}else if(idMenu == 'unlockHarga' || idMenu == 'historyProduct'){
					notEnter();
				}else if(idMenu == 'transCabang'){
					transCabangRowData = 1;
					$('.menu .item').tab();
					document.getElementById("transCabang-account_number").focus();
					viewRep = response.viewRep;
					viewGros = response.viewGros;
					getTableForm(idMenu);
				}else if(idMenu == 'mutasiKas'){
					mutasiKasRowData = 1;
					document.getElementById("mutasiKas-jenis_1").focus();
					$('.menu .item').tab();
				}else if(idMenu == 'mutasiGram'){
					mutasiGramRowData = 1;
					document.getElementById("mutasiGram-jenis_trans").focus();
					$('.menu .item').tab();
				}else if(idMenu == 'titipanRp'){
					document.getElementById("titipanRp-input_1_1").focus();
					$('.menu .item').tab();
				}else if(idMenu == 'titipanGram'){
					document.getElementById("titipanGram-input_1_1").focus();
					$('.menu .item').tab();
				}else if(idMenu == 'titipanGram'){
					document.getElementById("titipanGram-input_1_1").focus();
					$('.menu .item').tab();
				}else if(idMenu == 'jurnalUmum'){
					jurnalUmumRowData = 1;
					document.getElementById("jurnalUmum-input_1_1").focus();
					$('.menu .item').tab();
				}else if(idMenu == 'lapKasir'){
					window.setTimeout(function(){
						filterLapKasir(idMenu);
					}, 1000);
					$('.menu .item').tab();
				}
			}else{
				alert('Gagal Koneksi ke Sistem!');
			}
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('Sistem Error, Hubungi Tim IT!');
		}
	})
}

function entToNextID(nextID){
	var x = event.keyCode;
	if(x == 13){
		document.getElementById(nextID).focus();
	}
}

function valueToCurrency(idMenu,idForm,flagTotal){
	jumlahVal = $("#"+idForm).val();
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
		
		document.getElementById(idForm).value = beforeComma+'.'+afterComma.substring(0,2);
	}else{
		jumlahVal = parseFloat(jumlahVal);
		jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		
		if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
			jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
		}
		
		document.getElementById(idForm).value = jumlahVal;
	}
	
	if(flagTotal == "Total"){
		countTotal(idMenu);
	}
	
	if(idMenu == 'transCabang'){
		var account = $("#"+idMenu+"-account_number").val();
		
		if(account == '17-0002'){
			jumlahVal = "#"+idForm;
			var idRow = jumlahVal.split("_");
			idCol = idRow[2];
			idRow = idRow[1];
			if(idCol == '2'){
				kaliPersentase(idMenu,idRow);
			}else if(idCol == '3'){
				countPersentase(idMenu,idRow);
			}
		}
	}
}

function valueToCurrencyRp(idMenu,idForm,flagTotal){
	jumlahVal = $("#"+idForm).val();
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
		
		document.getElementById(idForm).value = beforeComma;
		
	}else{
		jumlahVal = parseFloat(jumlahVal);
		jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		
		if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
			jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
		}
		
		document.getElementById(idForm).value = jumlahVal;
		
		if(flagTotal == 'Total'){
			countTotal(idMenu);
		}
	}
}

function valueToCurrencyCabang(idMenu,idForm,idFormTaksir,idFormReal){
	jumlahVal = $("#"+idForm).val();
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
		
		document.getElementById(idForm).value = beforeComma+'.'+afterComma.substring(0,2);
	}else{
		jumlahVal = parseFloat(jumlahVal);
		jumlahVal = jumlahVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		
		if(jumlahVal.substr(jumlahVal.length - 3) == '.00'){
			jumlahVal = jumlahVal.substring(0, jumlahVal.length - 3);
		}
		
		document.getElementById(idForm).value = jumlahVal;
	}
	
	countTaksir(idForm,idFormTaksir,idFormReal);
	/*if(flagTotal == "Total"){
		countTotalTaksir(idMenu);
	}*/
}

function countTaksir(idForm,idFormTaksir,idFormReal){
	var totalPersen = $("#"+idForm).val();
		if(totalPersen == ''){
			totalPersen = 0;
		}
		totalPersen = totalPersen.replace(/,/g, "");
		totalPersen = parseFloat(totalPersen);
	
	var totalReal = $("#"+idFormReal).val();
		if(totalReal == ''){
			totalReal = 0;
		}
		totalReal = totalReal.replace(/,/g, "");
		totalReal = parseFloat(totalReal);
		
	var totalTaksir = totalReal * totalPersen / 100;
	
	totalTaksir = totalTaksir.toString();
	totalTaksir = totalTaksir.replace(/[^0-9.]/g, "");
	
	if (totalTaksir.indexOf('.') > -1){
		totalTaksir = totalTaksir.toString();
		
		var pos = totalTaksir.search(/\./g) + 1;
		totalTaksir = totalTaksir.substr( 0, pos )
		 + totalTaksir.slice( pos ).replace(/\./g, '');
		
		var beforeComma = totalTaksir.substr(0,totalTaksir.indexOf("."));
		var afterComma = totalTaksir.substr(totalTaksir.indexOf(".") + 1);
		
		beforeComma = parseFloat(beforeComma);
		beforeComma = beforeComma.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		
		if(beforeComma.substr(beforeComma.length - 3) == '.00'){
			beforeComma = beforeComma.substring(0, beforeComma.length - 3);
		}
		
		document.getElementById(idFormTaksir).value = beforeComma+'.'+afterComma.substring(0,2);
	}else{
		totalTaksir = parseFloat(totalTaksir);
		totalTaksir = totalTaksir.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		
		if(totalTaksir.substr(totalTaksir.length - 3) == '.00'){
			totalTaksir = totalTaksir.substring(0, totalTaksir.length - 3);
		}
		
		document.getElementById(idFormTaksir).value = totalTaksir;
	}
	
	countTotalTaksir();
}

