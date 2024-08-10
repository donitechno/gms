<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php $this->load->view('V_link'); ?>
</head>
<body>
<div class="ui container" style="margin-top:30px;">
	<div class="ui grid">
		<div class="eight wide centered column center aligned">
			<div class="ui <?php echo $tipe ?> message">
				<div class="ui icon header">
					<i class="<?php echo $icon ?> icon"></i>
					<?php echo $pesan ?>
				</div>
				<a class="ui twitter button center aligned" style="margin-top:15px;" href="<?php echo $location ?>">
					<i class="undo alternate icon"></i>
					Kembali ke Menu Sebelumnya
				</a>
			</div>
		</div>
	</div>
</div>
</body>
</html>