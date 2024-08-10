var base_url = window.location.origin+"/gms";
var exeTrans = false;

function addForm(idMenu){
	$.ajax({
		url: base_url+'/index.php/'+idMenu+'/add',
		type: 'post',
		dataType: 'json',
		success: function(response){
			if(response.success == true){
				$("#menuModal").removeClass("large");
				$("#menuModal").addClass("mini");
				$("#menuModal").html(response.view);	
				$('.ui.modal').modal({closable: false}).modal('show');
				
				if(idMenu == 'namaBarang'){
					$('#'+idMenu+'-select-category').selectize({
						onChange: function(value) {
							entToNextID(idMenu+"-btnadd");
						}
					});
				}else if(idMenu == 'stockIn'){
					$('#'+idMenu+'-select-category').selectize({
						onChange: function(value) {
							entToNextID(idMenu+"-btnadd");
						}
					});
				}
				
				notEnter();
			}else{
				alert("Gagal Koneksi ke Server!");
			}
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('Sistem Error, Hubungi Tim IT!');
		}
	})
}

function saveAddEdit(idMenu,flagExe){
	if(exeTrans == false){
		exeTrans = true;
		$("#"+idMenu+"-btnadd").addClass("loading");
		$("#"+idMenu+"-wraperror").attr("style","display:none");
		$("#"+idMenu+"-wraperror").html("");
		
		$.ajax({
			url: $("#"+idMenu+"-addedit").attr("action")+"/"+flagExe,
			type: 'post',
			data: $("#"+idMenu+"-addedit").serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == false){
					if(idMenu == "stockIn"){
						$("#"+idMenu+"-btnadd").removeClass("loading");
						$("#"+idMenu+"-modalwraperror").attr("style","");
						$("#"+idMenu+"-modalwraperror").html(response.inputerror);
						exeTrans = false;
					}else{
						$("#"+idMenu+"-btnadd").removeClass("loading");
						$("#"+idMenu+"-wraperror").attr("style","");
						$("#"+idMenu+"-wraperror").html(response.inputerror);
						exeTrans = false;
					}
					
				}else{
					if(idMenu == "stockIn"){
						$("#menuModal").html(response.message);
						getMasterProduct(idMenu,stockInRowData,"3")
						window.setTimeout(function(){
							$('.ui.modal').modal('hide');
						}, 1000);
						exeTrans = false;
					}else{
						$("#menuModal").html(response.message);
						getContent(idMenu);
						window.setTimeout(function(){
							$('.ui.modal').modal('hide');
						}, 1000);
						exeTrans = false;
					}
					
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('System Error, Hubungi Tim IT!');
			}
		})
	}
}

function editForm(idMenu,idData){
	$.ajax({
		url : base_url+'/index.php/'+idMenu+'/edit/'+idData,
		type: "GET",
		dataType: "JSON",
		success: function(response){
			if(response.success == true){
				$("#menuModal").removeClass("large");
				$("#menuModal").addClass("mini");
				$("#menuModal").html(response.view);	
				$('.ui.modal').modal({closable: false}).modal('show');
				
				if(idMenu == 'namaBarang'){
					$('#'+idMenu+'-select-category').selectize({
						onChange: function(value) {
							entToNextID(idMenu+"-btnadd");
						}
					});
				}
				
				notEnter();
			}else{
				alert("Gagal Koneksi ke Server!");
			}
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('System Error, Hubungi Tim IT!');
		}
	});
}

function changeStatus(idMenu,id,statusVal){
	if(statusVal == 'A'){
		var pesanStatus = 'Aktif';
	}else{
		var pesanStatus = 'Non Aktif';
	}
	
	$("#menuModal").removeClass("large");
	$("#menuModal").addClass("mini");
	
	var view= '<div class="header">Ubah Status</div><div class="content"><p>Anda Ingin Mengubah Status Kelompok Barang Menjadi '+pesanStatus+'?</p></div><div class="actions"><div class="ui negative button">Tidak </div><button id="'+idMenu+'-btnmodalyes" class="ui green right labeled icon button" onclick=exeChangeStatus("'+idMenu+'","'+id+'","'+statusVal+'")>Ya<i class="check circle icon"></i></button></div>';
	
	$("#menuModal").html(view);
	
	$('.ui.modal').modal({closable: false}).modal('show');		
}

function exeChangeStatus(idMenu,id,statusVal){
	if(exeTrans == false){
		exeTrans = true;
		$("#"+idMenu+"-btnmodalyes").addClass("loading");
		
		$.ajax({
			url : base_url+'/index.php/'+idMenu+'/change_status/'+id+'/'+statusVal,
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == false){
					if(idMenu == 'kelompokBarang'){
						$("#"+idMenu+"-btnmodalyes").removeClass("loading");
						exeTrans = false;
					}else if(idMenu == 'boxBarang'){
						$("#menuModal").html(response.message);
						window.setTimeout(function(){
							$('.ui.modal').modal('hide');
						}, 3000);
						exeTrans = false;
					}
				}else{
					$("#menuModal").html(response.message);
					getContent(idMenu);
					window.setTimeout(function(){
						$('.ui.modal').modal('hide');
					}, 1000);
					exeTrans = false;
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('System Error, Hubungi Tim IT!');
			}
		})
	}
}

function filterNamaBarang(idMenu){
	$("#"+idMenu+"-loader").addClass("active");
	
	$.ajax({
		url: $('#'+idMenu+'-form-filter').attr('action'),
		type: 'post',
		data: $('#'+idMenu+'-form-filter').serialize(),
		dataType: 'json',
		success: function(response){
			if(response.success == true){
				$("#"+idMenu+"-wrap").html(response.view);
				$('#'+idMenu+'-table').DataTable();
				$("#"+idMenu+"-loader").removeClass("active");
			}else{
				alert("Gagal Koneksi ke Server!");
			}
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('System Error, Hubungi Tim IT!');
		}
	})
}

function importForm(idMenu){
	$.ajax({
		url : base_url+'/index.php/'+idMenu+'/import/',
		type: "GET",
		dataType: "JSON",
		success: function(response){
			if(response.success == true){
				$("#menuModal").removeClass("large");
				$("#menuModal").addClass("mini");
				$("#menuModal").html(response.view);	
				$('.ui.modal').modal({closable: false}).modal('show');
			}else{
				alert('Gagal Koneksi ke Server!');
			}
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('System Error, Hubungi Tim IT!');
		}
	});
}

function viewDetail(idMenu,id){
	id = id.replace(/\+/g,"plus");
	$.ajax({
		url: base_url+'/index.php/'+idMenu+'/view/'+id,
		type: 'post',
		dataType: 'json',
		success: function(response){
			if(response.success == true){
				$("#menuModal").removeClass("mini");
				$("#menuModal").addClass("large");
				$("#menuModal").html(response.view);
				
				$('#'+idMenu+'-jualtable').DataTable({
					"bPaginate": true,
					"bLengthChange": false,
					"pageLength": 1
				});
				
				$('#'+idMenu+'-belitable').DataTable({
					"bPaginate": true,
					"bLengthChange": false,
					"pageLength": 1
				});
				
				$('.menu .item').tab();
				
				$('.ui.modal').modal({closable: false}).modal('show');
			}else{
				alert("Gagal Koneksi ke Server!");
			}
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('Sistem Error, Hubungi Tim IT!');
		}
	})
}

function notEnter(){
	$('.form-javascript').on('keyup keypress', function(e){
		var keyCode = e.keyCode || e.which;
		if (keyCode === 13) {
			e.preventDefault();
			return false;
		}
	});
}