<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view('V_link') ?>
</head>
<style>
</style>
<body>
	<?php $this->load->view("V_header") ?>
	<div class="pusher">
		<div class="ui container fluid">
			<div class="ui stackable menu" style="border-radius:0">
				<div class="item">
					<div class="ui breadcrumb">
					  <div class="section"><i class="cogs icon"></i> Pengaturan</div>
					  <i class="right chevron icon divider"></i>
					  <div class="active section">Import Pajangan</div>
					</div>
				</div>
			</div>
			<div class="ui grid">
				<div class="fifteen wide centered column">
					<div class="ui grid">
						<div class="ui inverted dimmer" id="filter_loader">
							<div class="ui loader"></div>
						</div>
						<div class="sixteen wide column" id="wrap_filter">
							<form class="ui form form-javascript" id="form_import" action="<?php echo base_url() ?>index.php/C_import_pajangan/save_import" method="post" enctype="multipart/form-data">
							<div class="field">
								<input type="file" class="form-control" name="file_excel" id="file_excel">
							</div>
							<button type="submit" id="btn_save" class="fluid ui green labeled icon button">
								Import	<i class="save icon"></i>
							</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>

