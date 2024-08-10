var base_url = window.location.origin+"/gms";
var exeTrans = false;
var stockInRowData = 1;
var stockOutRowData = 1;
var pindahBoxRowData = 1;

function getKetPersediaan(idMenu){
	var ketFromID = $("#"+idMenu+"-from").val();
	
	if(ketFromID != null && ketFromID != ''){
		$.ajax({
			url : base_url+'/index.php/'+idMenu+'/get_ket_from/'+ketFromID,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					if(response.edit == true){
						$("#"+idMenu+"-ket-select").val(response.ketvalue);
						$("#"+idMenu+"-ket-select").removeAttr("readonly");
					}else{
						$("#"+idMenu+"-ket-select").val(response.ketvalue);
						$("#"+idMenu+"-ket-select").attr("readonly","readonly");
					}
				}else{
					alert('Gagal Koneksi ke Server!');
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Sistem Error, Hubungi Tim IT!');
			}
		});
	}
}

function entToTabInput(idMenu,idRow,idElement){
	idElement = parseFloat(idElement);
	idElement = idElement + 1;
	
	if(idMenu == 'stockIn'){
		if(idElement != 5){
			$("#"+idMenu+"-input_"+idRow+"_"+idElement+"-selectized").focus();
		}else{
			$("#"+idMenu+"-input_"+idRow+"_"+idElement+"").focus();
		}
	}else if(idMenu == 'stockOut' || idMenu == 'pindahBox' || idMenu == 'transCabang' || idMenu == 'mutasiKas' || idMenu == 'mutasiGram' || idMenu == 'titipanRp' || idMenu == 'titipanGram' || idMenu == 'jurnalUmum'){
		var x = event.keyCode;
		if(x == 13){
			$("#"+idMenu+"-input_"+idRow+"_"+idElement+"").focus();
		}
	}
}

function getMasterProduct(idMenu,idRow,idUrut){
	var categoryID = $("#"+idMenu+"-input_"+idRow+"_"+idUrut).val();
	
	if(categoryID != ''){
		$.ajax({
			url : base_url+'/index.php/'+idMenu+'/get_master_product/'+categoryID+'/'+idRow,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					$("#"+idMenu+"-wrap_nama_barang_"+idRow).html(response.view);
					idUrut = parseFloat(idUrut);
					idUrut = idUrut + 1;
					$('#'+idMenu+'-input_'+idRow+'_'+idUrut).selectize({
						onChange: function(value) {
							entToTabInput(idMenu,idRow,idUrut);
						}
					});
					$("#"+idMenu+"-input_"+idRow+"_"+idUrut+"-selectized").focus();
				}else{
					alert('Gagal Koneksi ke Server!');
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Sistem Error, Hubungi Tim IT!');
			}
		});
	}
}

function entToInsert(idMenu){
	if(exeTrans == false){
		var x = event.keyCode;
		
		if(idMenu == "stockIn"){
			var rowData = stockInRowData;
		}else if(idMenu == "stockOut"){
			var rowData = stockOutRowData;
		}else if(idMenu == "pindahBox"){
			var rowData = pindahBoxRowData;
		}
		
		if(x == 113){
			saveBaris(idMenu);
		}else if(x == 115){
			$("#"+idMenu+"-input_"+rowData+"_1-selectized").select();
		}
	}
}

function saveBaris(idMenu){
	if(exeTrans == false){
		exeTrans = true;
		
		if(idMenu == "stockIn"){
			var rowData = stockInRowData;
		}else if(idMenu == "stockOut"){
			var rowData = stockOutRowData;
		}else if(idMenu == "pindahBox"){
			var rowData = pindahBoxRowData;
		}
		
		$("#"+idMenu+"-loaderinput").addClass("active");
		
		$.ajax({
			url : base_url+'/index.php/'+idMenu+'/insert/'+rowData,
			type: 'post',
			data: $('#'+idMenu+'-form').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					if(idMenu == "stockIn"){
						$("#"+idMenu+"-pos_body").append(response.view);
						document.getElementById(idMenu+"-ket-select").setAttribute('readonly','readonly');
						document.getElementById(idMenu+"-wrap_id_karat_"+stockInRowData).innerHTML = response.select_karat;
						document.getElementById(idMenu+"-wrap_id_karat_"+stockInRowData).setAttribute('style','padding: 0.3em 0.78571429em;');
						document.getElementById(idMenu+"-wrap_id_box_"+stockInRowData).innerHTML = response.select_box;
						document.getElementById(idMenu+"-wrap_id_box_"+stockInRowData).setAttribute('style','padding: 0.3em 0.78571429em;');
						document.getElementById(idMenu+"-wrap_id_category_"+stockInRowData).innerHTML = response.select_category;
						document.getElementById(idMenu+"-wrap_id_category_"+stockInRowData).setAttribute('style','padding: 0.3em 0.78571429em;');
						document.getElementById(idMenu+"-input_"+stockInRowData+"_5").setAttribute('readonly','readonly');
						document.getElementById(idMenu+"-input_"+stockInRowData+"_6").innerHTML = response.product_id;
						document.getElementById(idMenu+"-input_"+stockInRowData+"_6").setAttribute('style','padding: 0.3em 0.78571429em;');
						document.getElementById(idMenu+"-input_"+stockInRowData+"_7").innerHTML = response.button_messsage;
						document.getElementById(idMenu+"-wrap-select-from").innerHTML = response.select_from;
						document.getElementById(idMenu+"-wrap-tanggal-input").innerHTML = response.tanggal_stock_in;
						document.getElementById(idMenu+"-wrap_nama_barang_"+stockInRowData).innerHTML = response.nama_barang;
						document.getElementById(idMenu+"-wrap_nama_barang_"+stockInRowData).setAttribute('style','padding: 0.3em 0.78571429em;');
						document.getElementById(idMenu+"-wraperror").setAttribute('style','display:none');
						
						stockInRowData = stockInRowData + 1;
						
						$('#'+idMenu+'-input_'+stockInRowData+'_1').selectize();
						$('#'+idMenu+'-input_'+stockInRowData+'_2').selectize();
						$('#'+idMenu+'-input_'+stockInRowData+'_3').selectize();
						$('#'+idMenu+'-input_'+stockInRowData+'_4').selectize();
						
						window.setTimeout(function(){
							document.getElementById(idMenu+"-input_"+stockInRowData+"_5").focus();
						}, 1000);
						
						document.getElementById(idMenu+"-idurut").value = response.id_urutan;
						exeTrans = false;
						
						document.getElementById(idMenu+"-loaderinput").classList.remove("active");
						countTotal(idMenu);
					}else if(idMenu == "stockOut"){
						$("#"+idMenu+"-pos_body").append(response.view);
						
						document.getElementById(idMenu+"-reason").setAttribute('readonly','readonly');
						document.getElementById(idMenu+"-wrapdate").innerHTML = response.tanggal_stock_out;
						document.getElementById(idMenu+"-input_"+stockOutRowData+"_8").innerHTML = response.button_messsage;
						document.getElementById(idMenu+"-input_"+stockOutRowData+"_1").setAttribute('readonly','readonly');
						$("#"+idMenu+"-input_"+stockOutRowData+"_1").removeAttr("onblur");
						
						stockOutRowData = stockOutRowData + 1;
						
						document.getElementById(idMenu+"-wraperror").setAttribute('style','display:none');
						document.getElementById(idMenu+"-loaderinput").classList.remove("active");
						document.getElementById(idMenu+"-input_"+stockOutRowData+"_1").focus();
						
						exeTrans = false;
						countTotal(idMenu);
					}else if(idMenu == "pindahBox"){
						document.getElementById(idMenu+"-wraperror").setAttribute('style','display:none');
						document.getElementById(idMenu+"-loaderinput").classList.remove("active");
						
						$("#"+idMenu+"-pos_body").append(response.view);
						
						document.getElementById(idMenu+"-wrapdate").innerHTML = response.tanggal_pindah_box;
						document.getElementById(idMenu+"-wrap_ke_box_"+pindahBoxRowData).innerHTML = response.box_ke;
						document.getElementById(idMenu+"-input_"+pindahBoxRowData+"_8").innerHTML = response.button_messsage;
						document.getElementById(idMenu+"-input_"+pindahBoxRowData+"_1").setAttribute('readonly','readonly');
						$("#"+idMenu+"-input_"+pindahBoxRowData+"_1").removeAttr("onblur");
						
						pindahBoxRowData = pindahBoxRowData + 1;
						document.getElementById(idMenu+"-input_"+pindahBoxRowData+"_1").focus();
						exeTrans = false;
						countTotal(idMenu);
					}
				}else{
					document.getElementById(idMenu+"-wraperror").innerHTML = response.inputerror;
					document.getElementById(idMenu+"-wraperror").setAttribute('style','');
					document.getElementById(idMenu+"-loaderinput").classList.remove("active");
					exeTrans = false;
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
}

function countTotal(idMenu){
	var account = '';
	var totalAll = 0;
	
	if(idMenu == "stockIn"){
		var idHitung = 5;
		var idRow = stockInRowData;
	}else if(idMenu == "pindahBox"){
		var idHitung = 5;
		var idRow = pindahBoxRowData;
	}else if(idMenu == "stockOut"){
		var idHitung = 7;
		var idRow = stockOutRowData;
	}else if(idMenu == 'transCabang'){
		var idHitung = 3;
		var idRow = transCabangRowData;
		account = $("#transCabang-account_number").val();
	}else if(idMenu == 'mutasiKas'){
		var idHitung = 3;
		var idRow = mutasiKasRowData;
	}else if(idMenu == 'mutasiGram'){
		var idHitung = 4;
		var idRow = mutasiGramRowData;
	}else if(idMenu == 'titipanRp' || idMenu == 'titipanGram'){
		var idHitung = 4;
		var idRow = 1;
	}else if(idMenu == 'jurnalUmum'){
		var idHitung = 4;
		var idRow = jurnalUmumRowData;
	}
	
	if(idMenu == 'transCabang' && account == '17-0003'){
		var totalInd = $("#"+idMenu+"-input_1_9").val();
		totalInd = totalInd.replace(/,/g, "");
		totalInd = parseFloat(totalInd);
		
		totalAll = totalAll + totalInd;
	}else if(idMenu == 'transCabang' && account == '17-0005'){
		var totalInd = $("#"+idMenu+"-input_1_9").val();
		totalInd = totalInd.replace(/,/g, "");
		totalInd = parseFloat(totalInd);
		
		totalAll = totalAll + totalInd;
	}else{
		for (var i = 1; i <= idRow; i++){
			var totalInd = $("#"+idMenu+"-input_"+i+"_"+idHitung).val();
			totalInd = totalInd.replace(/,/g, "");
			
			if(totalInd == ''){
				totalInd = '0';
			}
			
			totalInd = parseFloat(totalInd);
			
			totalAll = totalAll + totalInd;
		}
	}
	
	if(totalAll == 'N' || totalAll == 'NaN'){
		totalAll = 0;
	}
	
	totalAll = totalAll.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
	if(totalAll.substr(totalAll.length - 3) == '.00'){
		totalAll = totalAll.substring(0, totalAll.length - 3);
	}
	
	document.getElementById(idMenu+'-total').innerHTML = totalAll;
}

function countTotalTaksir(){
	var idRow = transCabangRowData;
	var idHitung = 4;
	var totalAll = 0;
	
	for (var i = 1; i <= idRow; i++){
		var totalInd = $("#transCabang-input_"+i+"_"+idHitung).val();
		totalInd = totalInd.replace(/,/g, "");
		
		if(totalInd == ''){
			totalInd = '0';
		}
		
		totalInd = parseFloat(totalInd);
		
		totalAll = totalAll + totalInd;
	}
	
	document.getElementById('transCabang-total_taksir').innerHTML = totalAll;
}

function filterPersediaan(idMenu){
	exeTrans = true;
	document.getElementById(idMenu+"-loaderlist").className += " active";
	document.getElementById(idMenu+"-btnfilter").className += " loading";
	
	$.ajax({
		url: $('#'+idMenu+'-form-filter').attr('action'),
		type: 'post',
		data: $('#'+idMenu+'-form-filter').serialize(),
		dataType: 'json',
		success: function(response){
			if(response.success == true){
				document.getElementById(idMenu+"-wrap_filter").innerHTML = response.view;
				
				if(idMenu == "stockIn" || idMenu == "stockOut" || idMenu == "pindahBox"){
					$(document).ready(function() {
						$('#'+idMenu+'-tablefilter').DataTable({
							"bLengthChange": false
						});
					} );
				}
				
				if(idMenu == "stockIn" || idMenu == "stockOut" || idMenu == "pindahBox"){
					document.getElementById(idMenu+"-filter-pcs").innerHTML = response.total_pcs+" Pcs";
					document.getElementById(idMenu+"-filter-gram").innerHTML = response.total_gram;
				}
				
				exeTrans = false;
				document.getElementById(idMenu+"-loaderlist").classList.remove("active");
				document.getElementById(idMenu+"-btnfilter").classList.remove("loading");
			}else{
				alert('filter gagal');
				exeTrans = false;
				document.getElementById(idMenu+"-loaderlist").classList.remove("active");
				document.getElementById(idMenu+"-btnfilter").classList.remove("loading");
			}
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('Error get data from ajax');
		}
	})
}

function getProductForm(idMenu,idRow,flagForm,productID){
	if(idMenu == "unlockHarga"){
		var inputDate = "0";
	}else{
		var inputDate = $("#"+idMenu+"-dateinput").val();
	}
	
	if(flagForm == "F"){
		if(idMenu == "unlockHarga" || idMenu == "historyProduct"){
			var productID = $("#"+idMenu+"-id_product_atas").val();
		}else{
			var productID = $("#"+idMenu+"-input_"+idRow+"_1").val();
		}
	}
	
	if(flagForm == "M"){
		if(idMenu == "stockOut"){
			idRow = stockOutRowData;
		}else if(idMenu == "pindahBox"){
			idRow = pindahBoxRowData;
		}else{
			idRow = "0";
		}
	}
	
	if(productID != '' && productID != null){
		$.ajax({
			url : base_url+'/index.php/'+idMenu+'/get_product_from/'+inputDate+'/'+productID+'/'+idRow,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					if(response.found == 'single'){
						if(idMenu == 'stockOut'){
							document.getElementById(idMenu+'-input_'+idRow+'_1').value=response.id;
							document.getElementById(idMenu+'-input_'+idRow+'_2').value=response.asal_barang;
							document.getElementById(idMenu+'-input_'+idRow+'_3').value=response.kelompok_barang;
							document.getElementById(idMenu+'-input_'+idRow+'_4').value=response.nama_barang;
							document.getElementById(idMenu+'-input_'+idRow+'_5').value=response.karat_barang;
							document.getElementById(idMenu+'-input_'+idRow+'_6').value=response.box_barang;
							document.getElementById(idMenu+'-input_'+idRow+'_7').value=response.berat_barang;
							countTotal(idMenu);
						}else if(idMenu == 'pindahBox'){
							document.getElementById(idMenu+'-input_'+idRow+'_1').value=response.id;
							document.getElementById(idMenu+'-input_'+idRow+'_2').value=response.kelompok_barang;
							document.getElementById(idMenu+'-input_'+idRow+'_3').value=response.nama_barang;
							document.getElementById(idMenu+'-input_'+idRow+'_4').value=response.karat_barang;
							document.getElementById(idMenu+'-input_'+idRow+'_5').value=response.berat_barang;
							document.getElementById(idMenu+'-input_'+idRow+'_6').value=response.box_barang;
							document.getElementById(idMenu+'-wrap_ke_box_'+idRow).innerHTML=response.view;
							
							$('#'+idMenu+'-input_'+idRow+'_7').selectize({
								onChange: function(value) {
									entToNextID(idMenu+"-input_"+idRow+"_6");
								}
							});
							
							window.setTimeout(function(){
								document.getElementById(idMenu+'-input_'+idRow+'_7-selectized').focus();
							}, 500);
							
							countTotal(idMenu);
						}else if(idMenu == 'unlockHarga'){
							$("#"+idMenu+"-wrap_filter").html(response.view);
							document.getElementById(idMenu+'-alasan_unlock').focus();
						}else if(idMenu == 'historyProduct'){
							$("#"+idMenu+"-wrap_filter").html(response.view);
						}
						
						if(flagForm == "M"){
							$('.ui.modal').modal('hide');
						}
					}else{
						$("#menuModal").removeClass("mini");
						$("#menuModal").addClass("large");
						$("#menuModal").html(response.view);
						
						$(document).ready(function() {
							$('#'+idMenu+'-tablemodal').DataTable({
								"bLengthChange": false
							});
						} );
						
						$('.ui.modal').modal({closable: false}).modal('show');
					}
				}else{
					window.setTimeout(function(){
						$("#menuModal").removeClass("mini");
						$("#menuModal").addClass("large");
						$("#menuModal").html(response.view);
						$('.ui.modal').modal('show');
					}, 500);
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('System Error, Hubungi Tim IT!');
			}
		});
	}
}

function exePersediaan(idMenu){
	exeTrans = true;
	$("#"+idMenu+"-wraperror").html("");
	document.getElementById(idMenu+"-wraperror").setAttribute('style','display:none');
	document.getElementById(idMenu+"-btnexe").className += " loading";
	
	$.ajax({
		url: $('#'+idMenu+'-form').attr('action'),
		type: 'post',
		data: $('#'+idMenu+'-form').serialize(),
		dataType: 'json',
		success: function(response){
			if(response.success == true){
				document.getElementById(idMenu+"-wraperror").setAttribute('style','display:none');
				$("#menuModal").removeClass("large");
				$("#menuModal").addClass("mini");
				document.getElementById("menuModal").innerHTML = response.message;
				$('.ui.modal').modal({}).modal('show');
				window.setTimeout(function(){
					$('.ui.modal').modal('hide');
				}, 3000);
				exeTrans = false;
				document.getElementById(idMenu+"-btnexe").classList.remove("loading");
			}else{
				$("#"+idMenu+"-wraperror").html(response.inputerror);
				document.getElementById(idMenu+"-wraperror").setAttribute('style','display:block');
				exeTrans = false;
				document.getElementById(idMenu+"-btnexe").classList.remove("loading");
			}
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('Error get data from ajax');
		}
	})
}
	