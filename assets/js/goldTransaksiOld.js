var base_url = window.location.origin+"/gms";
var exeInsert = true;
var exeTrans = false;
var exeTambahBaris = false;
var viewRep = '';
var viewGros = '';
var transCabangRowData = 1;
var mutasiKasRowData = 1;
var mutasiGramRowData = 1;
var jurnalUmumRowData = 1;
var maxData = 5;

function entToAction(idMenu){
	if(exeInsert == true){
		if(idMenu == 'transCabang'){
			var x = event.keyCode;
		
			if(x == 45){
				insertRowTransCabang(idMenu);
			}
			
			if(x == 36 && transCabangRowData != 1){
				deleteRowTransCabang(idMenu);
			}
		}else if(idMenu == 'mutasiKas'){
			var x = event.keyCode;
		
			if(x == 45){
				insertRowTransMutasiKas(idMenu);
			}
			
			if(x == 36 && mutasiKasRowData != 1){
				deleteRowTransMutasiKas(idMenu);
			}
		}else if(idMenu == 'mutasiGram'){
			var x = event.keyCode;
		
			if(x == 45){
				insertRowTransMutasiGram(idMenu);
			}
			
			if(x == 36 && mutasiGramRowData != 1){
				deleteRowTransMutasiGram(idMenu);
			}
		}else if(idMenu == 'jurnalUmum'){
			var x = event.keyCode;
		
			if(x == 45){
				insertRowJurnalUmum(idMenu);
			}
			
			if(x == 36 && jurnalUmumRowData != 1){
				deleteRowJurnalUmum(idMenu);
			}
		}
	}
}

function getTableForm(idMenu){
	if(idMenu == "transCabang"){
		account = $("#"+idMenu+"-account_number").val();
		if(account == '17-0002'){
			document.getElementById(idMenu+"-wrap_isi_data").innerHTML = viewRep;
			document.getElementById(idMenu+"-total_taksir").setAttribute("style","display:none");
			document.getElementById(idMenu+"-total_taksirspan").setAttribute("style","display:none");
			transCabangRowData = 1;
		}else if(account == '17-0003' || account == '17-0005'){
			document.getElementById(idMenu+"-wrap_isi_data").innerHTML = viewGros;
			document.getElementById(idMenu+"-total_taksir").setAttribute("style","");
			document.getElementById(idMenu+"-total_taksirspan").setAttribute("style","");
			transCabangRowData = 1;
		}
		
		countTotal(idMenu);
	}
}

function kaliPersentase(idMenu,idRow){
	var karat = $("#"+idMenu+"-input_"+idRow+"_1").val();
	if(karat == 1){
		var persenTase = 100;
	}else if(karat == 3){
		var persenTase = 92;
	}else if(karat == 4){
		var persenTase = 75;
	}else if(karat == 5){
		var persenTase = 70;
	}else{
		var persenTase = 0;
	}
	
	if(persenTase == 0){
		document.getElementById(idMenu+"-input_"+idRow+"_3").value = '0';
	}else{
		jumlahVal = $("#"+idMenu+"-input_"+idRow+"_2").val();
		if(jumlahVal == ''){
			jumlahVal = '0';
		}
		
		jumlahVal = jumlahVal.replace(/[^0-9.]/g, "");
		jumlahVal = parseFloat(jumlahVal);
		
		konversiVal = jumlahVal * persenTase / 100;
		
		konversiVal = konversiVal.toString();
		if (konversiVal.indexOf('.') > -1){
			konversiVal = konversiVal.toString();
			
			var pos = konversiVal.search(/\./g) + 1;
			konversiVal = konversiVal.substr( 0, pos )
			 + konversiVal.slice( pos ).replace(/\./g, '');
			
			var beforeComma = konversiVal.substr(0,konversiVal.indexOf("."));
			var afterComma = konversiVal.substr(konversiVal.indexOf(".") + 1);
			
			beforeComma = parseFloat(beforeComma);
			beforeComma = beforeComma.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(beforeComma.substr(beforeComma.length - 3) == '.00'){
				beforeComma = beforeComma.substring(0, beforeComma.length - 3);
			}
			
			document.getElementById(idMenu+"-input_"+idRow+"_3").value = beforeComma+'.'+afterComma.substring(0, 2);
		}else{
			konversiVal = parseFloat(konversiVal);
			konversiVal = konversiVal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			
			if(konversiVal.substr(konversiVal.length - 3) == '.00'){
				konversiVal = konversiVal.substring(0, konversiVal.length - 3);
			}
			
			document.getElementById(idMenu+"-input_"+idRow+"_3").value = konversiVal;
		}
	}
	
	document.getElementById(idMenu+"-input_"+idRow+"_4").value = persenTase+' %';
	countTotal(idMenu);
}

function countPersentase(idMenu,idRow){
	gramReal = $("#"+idMenu+"-input_"+idRow+"_2").val();
	
	if(gramReal == '' || gramReal == 'NaN'){
		gramReal = "0";
	}
	
	gramReal = gramReal.replace(/[^0-9.]/g, "");
	gramReal = parseFloat(gramReal);
	
	gram24 = $("#"+idMenu+"-input_"+idRow+"_3").val();
	if(gram24 == '' || gram24 == 'NaN'){
		gram24 = "0";
	}
	
	gram24 = gram24.replace(/[^0-9.]/g, "");
	gram24 = parseFloat(gram24);
	
	if(gramReal == 0 || gramReal == '' || gramReal == null){
		valPersentase = 0;
	}else{
		valPersentase = gram24 / gramReal * 100;
	}
	
	valPersentase = Math.round(valPersentase);
	
	document.getElementById(idMenu+"-input_"+idRow+"_4").value = valPersentase+' %';
}

function insertRowTransCabang(idMenu){
	if(exeTambahBaris == false){
		exeTambahBaris = true;
		
		var account = $("#"+idMenu+"-account_number").val();
		if(account == '17-0002'){
			var deptID = 'R';
		}else if(account == '17-0003'){
			var deptID = 'P';
		}else if(account == '17-0005'){
			var deptID = 'T';
		}
		
		if(transCabangRowData == maxData){
			$("#menuModal").removeClass("large");
			$("#menuModal").addClass("mini");
			
			var view = '<i class="close icon"></i><div class="header">Warning</div><div class="content"><div class="ui icon header center aligned"><i class="exclamation triangle red icon"></i>Maksimal Hanya '+maxData+' Data</div></div></div>';
			
			$("#menuModal").html(view);
			
			$('.ui.modal').modal('show');
			
			exeTambahBaris = false;
		}else{
			$.ajax({
				url : base_url+'/index.php/'+idMenu+'/tambah_baris/'+deptID+'/'+transCabangRowData,
				type: 'post',
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						$("#"+idMenu+"-pos_body").append(response.view);
						
						transCabangRowData = transCabangRowData + 1;
						document.getElementById(idMenu+"-input_"+transCabangRowData+"_1").focus();
						exeTambahBaris = false;
						
						countTotal(idMenu);
					}else{
						swal({
							html:true,
							type: "error",
							title: "",
							text: response.pesan_error,
						});
						
						exeTambahBaris = false;
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
}

function deleteRowTransCabang(idMenu){
	if(exeTambahBaris == false){
		exeTambahBaris = true;
		
		$("#"+idMenu+"-pos_tr_"+transCabangRowData).remove();
		transCabangRowData = transCabangRowData - 1;
		exeTambahBaris = false;
		document.getElementById(idMenu+"-input_"+transCabangRowData+"_1").focus();
		
		countTotal(idMenu);
	}
}

function saveTransaksi(idMenu){
	if(exeTrans == false){
		exeTrans = true;
		
		if(idMenu == 'transCabang'){
			rowData = transCabangRowData
		}else if(idMenu == 'mutasiKas'){
			rowData = mutasiKasRowData;
		}else if(idMenu == 'mutasiGram'){
			rowData = mutasiGramRowData;
		}else if(idMenu == 'titipanRp'){
			rowData = 1;
		}else if(idMenu == 'titipanGram'){
			rowData = 1;
		}else if(idMenu == 'jurnalUmum'){
			rowData = jurnalUmumRowData;
		}
		
		document.getElementById(idMenu+"-wraperror").setAttribute('style','display:none');
		document.getElementById(idMenu+"-btn").className += " loading";
		
		$.ajax({
			url: $('#'+idMenu+'-form').attr('action')+'/'+rowData,
			type: 'post',
			data: $('#'+idMenu+'-form').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					$("#menuModal").removeClass("large");
					$("#menuModal").addClass("mini");
					
					document.getElementById("menuModal").innerHTML = response.message;
					$('.ui.modal').modal('show');
					window.setTimeout(function(){
						$('.ui.modal').modal('hide');
					}, 2000);
					exeTrans = false;
					
					document.getElementById(idMenu+"-btn").classList.remove("loading");
					
					if(idMenu == 'transCabang'){
						getTableForm(idMenu);
					}else{
						getContent(idMenu);
					}
				}else{
					document.getElementById(idMenu+"-wraperror").innerHTML = response.inputerror;
					document.getElementById(idMenu+"-wraperror").setAttribute('style','');
					
					exeTrans = false;
					document.getElementById(idMenu+"-btn").classList.remove("loading");
					
					document.getElementById(idMenu+"-input_"+rowData+"_1").focus();
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
}

function filterTransaksi(idMenu){
	exeTrans = true;
	document.getElementById(idMenu+"-btnfilter").className += " loading";
	
	if(idMenu == 'lapTahunan'){
		var dateReport = $("#"+idMenu+"-date").val();
		
		var alamat = base_url+'/index.php/'+idMenu+'/pdf/'+dateReport;
		window.open(alamat,"_blank");
		
		document.getElementById(idMenu+"-btnfilter").classList.remove("loading");
		exeTrans = false;
	}else{
		$.ajax({
			url: $('#'+idMenu+'-form-filter').attr('action'),
			type: 'post',
			data: $('#'+idMenu+'-form-filter').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					document.getElementById(idMenu+"-wrap_filter").innerHTML = response.view;
					
					if(idMenu != "titipanRp" && idMenu != "titipanGram"){
						$(document).ready(function() {
							$('#'+idMenu+'-tablefilter').DataTable({
								"bLengthChange": false
							});
						} );
					}
					
					if(idMenu == 'bestCS'){
						$('#bestCS-filtertabel').DataTable({
							"order": [[ 3, "desc" ]]
						});
					}
					
					document.getElementById(idMenu+"-btnfilter").classList.remove("loading");
					exeTrans = false;
				}else{
					alert('Gagal Filter Data!');
					document.getElementById(idMenu+"-btnfilter").classList.remove("loading");
					exeTrans = false;
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
}

function viewTransCabang(idMenu,transID,deptID){
	if(exeTrans == false){
		exeTrans = true;
		$("#menuModal").removeClass("mini");
		$("#menuModal").addClass("large");
		
		$.ajax({
			url : base_url+'/index.php/'+idMenu+'/detail/'+transID+'/'+deptID,
			type: 'post',
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					document.getElementById("menuModal").innerHTML = response.view;
					$('.ui.modal').modal('show');
					
					exeTrans = false;
				}else{
					alert('Gagal Mengambil Data!');
					exeTrans = false;
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
}

function deleteTransCabang(idMenu,idTrans,idDept){
	$("#menuModal").removeClass("large");
	$("#menuModal").addClass("mini");
	
	var view= '<div class="header">Hapus Transaksi</div><div class="content"><p>Anda Ingin Menghapus Data Tersebut?</p></div><div class="actions"><div class="ui negative button">Tidak </div><button id="'+idMenu+'-btnconfirm" class="ui green right labeled icon button" onclick=exeDeleteTransCabang("'+idMenu+'","'+idTrans+'","'+idDept+'")>Ya<i class="check circle icon"></i></button></div>';
	
	document.getElementById("menuModal").innerHTML = view;
	document.getElementById("menuModal").className += " mini";
	
	$('.ui.modal').modal('show');
}

function exeDeleteTransCabang(idMenu,idTrans,idDept){
	document.getElementById(idMenu+"-btnconfirm").className += " loading";
	
	$.ajax({
		url : base_url+'/index.php/'+idMenu+'/hapus/'+idTrans+'/'+idDept,
		type: "GET",
		dataType: "JSON",
		success: function(response){
			if(response.success == true){
				document.getElementById("menuModal").innerHTML = response.message;
				filterTransaksi(idMenu);
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

function getAccountTrans(idMenu){
	jenisTrans = $("#"+idMenu+"-jenis_1").val();
	
	if(exeTrans == false){
		exeTrans = true;
		
		$.ajax({
			url : base_url+'/index.php/'+idMenu+'/get_account_data/'+jenisTrans,
			type: 'post',
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					var x = document.getElementsByClassName("invalid-tooltip");
					
					for (var a = 1; a <= mutasiKasRowData; a++){
						document.getElementById(idMenu+'-input_'+a+'_1').innerHTML = response.account;
						document.getElementById(idMenu+'-input_'+a+'_2').value = '';
						document.getElementById(idMenu+'-input_'+a+'_3').value = '0';
						if(jenisTrans == 'K'){
							document.getElementById(idMenu+"-input_"+a+"_2").setAttribute('readonly','readonly');
						}else{
							document.getElementById(idMenu+"-input_"+a+"_2").removeAttribute('readonly');
						}
					}
					
					document.getElementById(idMenu+'-account_number').innerHTML = response.header;
					
					countTotal(idMenu);
					
					exeTrans = false;
				}else{
					var x = document.getElementsByClassName("invalid-tooltip");
				
					for (var a = 0; a < x.length; a++){
						x[a].setAttribute('style','display:none');
					}
					
					for (var i = 0; i < response.inputerror.length; i++) {
						document.getElementById(response.inputerror[i]).setAttribute('style','display:inherit');
					}
					
					swal({
						html:true,
						type: "error",
						title: "",
						text: response.error_message,
					});
					
					exeTrans = false;
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
}

function getKeteranganMutasi(idMenu,idRow){
	jenisTrans = $("#"+idMenu+"-jenis_1").val();
	jenisTrans2 = $("#"+idMenu+"-jenis_2").val();
	if(jenisTrans == 'K'){
		innerOptGlobal = document.getElementById(idMenu+"-input_"+idRow+"_1");
		innerOpt = innerOptGlobal.getElementsByTagName("option");
		innerOptVal = innerOpt[innerOptGlobal.selectedIndex].innerHTML;
		
		arrayInner = innerOptVal.split("-");
		
		if(jenisTrans2 == 'I'){
			descTrans = 'PENGEMBALIAN PINJAMAN KARYAWAN -'+arrayInner[2];
		}else{
			descTrans = 'PINJAMAN KARYAWAN - '+arrayInner[2];
		}
		
		document.getElementById(idMenu+"-input_"+idRow+"_2").value = descTrans;
	}
}

function insertRowTransMutasiKas(idMenu){
	if(exeTambahBaris == false){
		exeTambahBaris = true;
		
		if(mutasiKasRowData == maxData){
			$("#menuModal").removeClass("large");
			$("#menuModal").addClass("mini");
			
			var view = '<i class="close icon"></i><div class="header">Warning</div><div class="content"><div class="ui icon header center aligned"><i class="exclamation triangle red icon"></i>Maksimal Hanya '+maxData+' Data</div></div></div>';
			
			$("#menuModal").html(view);
			
			$('.ui.modal').modal('show');
			
			exeTambahBaris = false;
		}else{
			jenisTrans = $("#"+idMenu+"-jenis_1").val();
			
			$.ajax({
				url : base_url+'/index.php/'+idMenu+'/tambah_baris/'+mutasiKasRowData+'/'+jenisTrans,
				type: 'post',
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						$("#"+idMenu+"-pos_body").append(response.view);
						
						mutasiKasRowData = mutasiKasRowData + 1;
						document.getElementById(idMenu+"-input_"+mutasiKasRowData+"_1").focus();
						exeTambahBaris = false;
						
						for (var a = 1; a <= mutasiKasRowData; a++){
							if(jenisTrans == 'K'){
								document.getElementById(idMenu+"-input_"+a+"_2").setAttribute('readonly','readonly');
							}else{
								document.getElementById(idMenu+"-input_"+a+"_2").removeAttribute('readonly');
							}
						}
						
						countTotal(idMenu);
					}else{
						swal({
							html:true,
							type: "error",
							title: "",
							text: response.pesan_error,
						});
						
						exeTambahBaris = false;
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
}

function deleteRowTransMutasiKas(idMenu){
	if(exeTambahBaris == false){
		exeTambahBaris = true;
		$("#"+idMenu+"-pos_tr_"+mutasiKasRowData).remove();
		mutasiKasRowData = mutasiKasRowData - 1;
		exeTambahBaris = false;
		document.getElementById(idMenu+"-input_"+mutasiKasRowData+"_1").focus();
		
		countTotal(idMenu);
	}
}

function deleteTransMutasi(idMenu,idTrans){
	$("#menuModal").removeClass("large");
	$("#menuModal").addClass("mini");
	
	var view= '<div class="header">Hapus Transaksi</div><div class="content"><p>Anda Ingin Menghapus Data Tersebut?</p></div><div class="actions"><div class="ui negative button">Tidak </div><button id="'+idMenu+'-btnconfirm" class="ui green right labeled icon button" onclick=exeDeleteTransMutasi("'+idMenu+'","'+idTrans+'")>Ya<i class="check circle icon"></i></button></div>';
	
	document.getElementById("menuModal").innerHTML = view;
	
	$('.ui.modal').modal({closable: false}).modal('show');
}

function exeDeleteTransMutasi(idMenu,idTrans){
	document.getElementById(idMenu+"-btnconfirm").className += " loading";
	
	$.ajax({
		url : base_url+'/index.php/'+idMenu+'/hapus/'+idTrans,
		type: "GET",
		dataType: "JSON",
		success: function(response){
			if(response.success == true){
				$("#menuModal").removeClass("large");
				$("#menuModal").addClass("mini");
			
				document.getElementById("menuModal").innerHTML = response.message;
				filterTransaksi(idMenu);
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

function insertRowTransMutasiGram(idMenu){
	if(exeTambahBaris == false){
		exeTambahBaris = true;
		
		if(mutasiGramRowData == maxData){
			$("#menuModal").removeClass("large");
			$("#menuModal").addClass("mini");
			
			var view = '<i class="close icon"></i><div class="header">Warning</div><div class="content"><div class="ui icon header center aligned"><i class="exclamation triangle red icon"></i>Maksimal Hanya '+maxData+' Data</div></div></div>';
			
			$("#menuModal").html(view);
			
			$('.ui.modal').modal('show');
			
			exeTambahBaris = false;
		}else{
			$.ajax({
				url : base_url+'/index.php/'+idMenu+'/tambah_baris/'+mutasiGramRowData,
				type: 'post',
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						$("#"+idMenu+"-pos_body").append(response.view);
						
						mutasiGramRowData = mutasiGramRowData + 1;
						document.getElementById(idMenu+"-input_"+mutasiGramRowData+"_1").focus();
						exeTambahBaris = false;
						
						countTotal(idMenu);
					}else{
						swal({
							html:true,
							type: "error",
							title: "",
							text: response.pesan_error,
						});
						
						exeTambahBaris = false;
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
}

function deleteRowTransMutasiGram(idMenu){
	if(exeTambahBaris == false){
		exeTambahBaris = true;
		$("#"+idMenu+"-pos_tr_"+mutasiGramRowData).remove();
		mutasiGramRowData = mutasiGramRowData - 1;
		exeTambahBaris = false;
		document.getElementById(idMenu+"-input_"+mutasiGramRowData+"_1").focus();
		
		countTotal(idMenu);
	}
}

function getKeteranganTitipan(idMenu,idRow){
	jenisTrans = $("#"+idMenu+"-input_"+idRow+"_1").val();
	innerOptGlobal = document.getElementById(idMenu+"-input_"+idRow+"_2");
	innerOpt = innerOptGlobal.getElementsByTagName("option");
	innerOptVal = innerOpt[innerOptGlobal.selectedIndex].innerHTML;
	ketVal = innerOptVal.replace("TITIPAN PELANGGAN - ","")
	
	if(ketVal != '-- Pilih Account --'){
		if(jenisTrans == 'I'){
			descTrans = 'PELANGGAN SETOR TITIPAN - '+ketVal;
		}else{
			descTrans = 'PELANGGAN TARIK TITIPAN - '+ketVal;
		}
		
		document.getElementById(idMenu+"-input_"+idRow+"_3").value = descTrans;
	}else{
		document.getElementById(idMenu+"-input_"+idRow+"_3").value = "";
	}
}

function addAccountTitipan(idMenu){
	$.ajax({
		url : base_url+'/index.php/'+idMenu+'/get_input_form',
		type: "GET",
		dataType: "JSON",
		success: function(response){
			if(response.success == true){
				$("#menuModal").removeClass("large");
				$("#menuModal").addClass("mini");
				
				$("#menuModal").html(response.view);
				
				$('.ui.modal')
					.modal({
					closable: false
				}).modal('show');
				
				window.setTimeout(function(){
					document.getElementById(idMenu+"-input_data_1").focus();
				}, 500);
				
				notEnter();
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

function saveAddTitipan(idMenu){
	if(exeTrans == false){
		exeTrans = true;
		
		document.getElementById(idMenu+"-errormodal").setAttribute('style','display:none');
		document.getElementById(idMenu+"-btnsaveadd").className += " loading";
		
		$.ajax({
			url: $('#'+idMenu+'-formadd').attr('action'),
			type: 'post',
			data: $('#'+idMenu+'-formadd').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					$("#menuModal").removeClass("large");
					$("#menuModal").addClass("mini");
					
					document.getElementById("menuModal").innerHTML = response.message;
					$('.ui.modal').modal('show');
					
					exeTrans = false;
					
					//document.getElementById(idMenu+"-btnsaveadd").classList.remove("loading");
					
					window.setTimeout(function(){
						$('.ui.modal').modal('hide');
					}, 2000);
					
					getContent(idMenu);
				}else{
					document.getElementById(idMenu+"-errormodal").innerHTML = response.inputerror;
					document.getElementById(idMenu+"-errormodal").setAttribute('style','');
					
					exeTrans = false;
					document.getElementById(idMenu+"-btnsaveadd").classList.remove("loading");
					document.getElementById(idMenu+"-input_data_1").focus();
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Error get data from ajax');
			}
		})
	}
}

function insertRowJurnalUmum(idMenu){
	if(exeTambahBaris == false){
		exeTambahBaris = true;
		
		if(jurnalUmumRowData == maxData){
			$("#menuModal").removeClass("large");
			$("#menuModal").addClass("mini");
			
			var view = '<i class="close icon"></i><div class="header">Warning</div><div class="content"><div class="ui icon header center aligned"><i class="exclamation triangle red icon"></i>Maksimal Hanya '+maxData+' Data</div></div></div>';
			
			$("#menuModal").html(view);
			
			$('.ui.modal').modal('show');
			
			exeTambahBaris = false;
		}else{
			$.ajax({
				url : base_url+'/index.php/'+idMenu+'/tambah_baris/'+jurnalUmumRowData,
				type: 'post',
				dataType: 'json',
				success: function(response){
					if(response.success == true){
						$("#"+idMenu+"-pos_body").append(response.view);
						
						jurnalUmumRowData = jurnalUmumRowData + 1;
						document.getElementById(idMenu+"-input_"+jurnalUmumRowData+"_1").focus();
						exeTambahBaris = false;
						
						countTotal(idMenu);
					}else{
						swal({
							html:true,
							type: "error",
							title: "",
							text: response.pesan_error,
						});
						
						exeTambahBaris = false;
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					alert('Error get data from ajax');
				}
			})
		}
	}
}

function deleteRowJurnalUmum(idMenu){
	if(exeTambahBaris == false){
		exeTambahBaris = true;
		$("#"+idMenu+"-pos_tr_"+jurnalUmumRowData).remove();
		jurnalUmumRowData = jurnalUmumRowData - 1;
		exeTambahBaris = false;
		document.getElementById(idMenu+"-input_"+jurnalUmumRowData+"_1").focus();
		
		countTotal(idMenu);
	}
}