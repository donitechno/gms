<div class="ui basic icon top fixed menu no-print">
	<div class="right menu" style="font-family:'Segoe UI' !important; font-size:15px">
		<button class="ui linkedin button" id="btn_send" style="border-radius:0px;margin-right:0px;" onclick=sendEmail()>
			<i class="database icon"></i> Backup Database
		</button>
		<a class="ui browse item"><i class="users icon" style="margin-right:5px;"></i> <?php echo $this->session->userdata('gold_nama_user') ?></a>
		
		<div class="ui flowing popup top left transition hidden">
			<div class="ui link list">
				<a href="<?php echo base_url()?>index.php/C_login/logout_pos" class="item"><i class="share icon"></i> Logout</a>
			</div>
		</div>
	</div>
</div>

<script>
	$('#toggle').click(function(){
		$('.ui.sidebar').sidebar('toggle');
	});
	
	$('.menu .browse')
		.popup({
			inline     : true,
			hoverable  : true,
			position   : 'bottom left',
			delay: {
				show: 300,
				hide: 800
			}
		})
	;
</script>