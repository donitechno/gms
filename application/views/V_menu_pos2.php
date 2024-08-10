<!DOCTYPE html>
<html lang="en">

<head>
   <?php $this->load->view('V_link_pos'); ?>
</head>
<style>
	.image{
		margin:5px;
	}
</style>
<body onkeyup=entToAction()>
	<?php $this->load->view('V_header_menupos'); ?>
	<div class="ui grid">
		<div class="sixteen wide centered column" style="margin-top:50px;text-align:center;border-bottom:double #e74c3c">
			<img class="ui centered medium image" src="<?php echo base_url() ?>assets/images/branding/brand.png">
		</div>
		<div class="sixteen wide centered column" style="text-align:center;padding-bottom:5px">
			<a class="ui medium image" href="<?php echo base_url()?>index.php/C_jual">
				<img src="<?php echo base_url() ?>assets/images/jual.jpg" alt="Penjualan">
			</a>
			<a class="ui medium image" href="<?php echo base_url()?>index.php/C_beli">
				<img src="<?php echo base_url() ?>assets/images/beli.jpg" alt="Pembelian">
			</a>
			<a class="ui medium image" href="<?php echo base_url()?>index.php/C_dailyopen">
				<img src="<?php echo base_url() ?>assets/images/dopen.jpg" alt="Daily Open">
			</a>
		</div>
		<div class="sixteen wide centered column" style="text-align:center;padding-top:0">
			<a class="ui medium image" href="<?php echo base_url()?>index.php/C_lap_kasir">
				<img src="<?php echo base_url() ?>assets/images/laporan.jpg" alt="Laporan Kasir">
			</a>
			<a class="ui medium image" href="<?php echo base_url() ?>index.php/C_pesanan_pos" style="text-align:center;padding-top:0">
				<img src="<?php echo base_url() ?>assets/images/pesanan.jpg" alt="Laporan Kasir">
			</a>
			<a class="ui medium image" href="#" style="visibility:hidden">
				<img src="<?php echo base_url() ?>assets/images/laporan.jpg" alt="Laporan Kasir">
			</a>
		</div>
	</div>
	<div class="ui modal mini" id="wrap_modal"></div>
</body>
</html>
<script>
	function entToAction(){
		var x = event.keyCode;
		
		if(x == 81){
			var webUrl = "<?php echo base_url()?>index.php/C_jual";
			window.location= webUrl;
		}else if(x == 87){
			var webUrl = "<?php echo base_url()?>index.php/C_beli";
			window.location= webUrl;
		}else if(x == 69){
			var webUrl = "<?php echo base_url()?>index.php/C_dailyopen";
			window.location= webUrl;
		}else if(x == 65){
			var webUrl = "<?php echo base_url()?>index.php/C_lap_kasir";
			window.location= webUrl;
		}else if(x == 83){
			var webUrl = "<?php echo base_url()?>index.php/C_pesanan_pos";
			window.location= webUrl;
		}
	}
	
	function sendEmail(){
		document.getElementById("btn_send").className += " loading";
		
		var message = '<div class="content"><div class="ui icon header center aligned"><i class="check circle outline green icon"></i>Berhasil Backup Database!</div></div>';
		
		var webUrl = "<?php echo base_url()?>";
		
		$.ajax({
			url : webUrl+'/index.php/C_mail',
			type: "GET",
			dataType: "JSON",
			success: function(response){
				if(response.success == true){
					document.getElementById("wrap_modal").innerHTML = message;
						$('.ui.modal').modal('show');
						window.setTimeout(function(){
							$('.ui.modal').modal('hide');
						}, 4000);
						
						exeTrans = false;
						
					document.getElementById("btn_send").classList.remove("loading");
				}else{
					document.getElementById("wrap_modal").innerHTML = response.message;
						$('.ui.modal').modal('show');
						window.setTimeout(function(){
							$('.ui.modal').modal('hide');
						}, 4000);
						
						exeTrans = false;
						
					document.getElementById("btn_send").classList.remove("loading");
				}
			},
			error: function (jqXHR, textStatus, errorThrown){
				document.getElementById("wrap_modal").innerHTML = message;
					$('.ui.modal').modal('show');
					window.setTimeout(function(){
						$('.ui.modal').modal('hide');
					}, 4000);
					
					exeTrans = false;
					
				document.getElementById("btn_send").classList.remove("loading");
			}
		});
	}
</script>
