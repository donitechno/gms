<div id="sidebar_menu" class="ui left fixed vertical inverted menu left-menu accordion no-print" style="overflow-y:auto">
	<img class="ui centered medium image" src="<?php echo base_url()?>assets/images/branding/brand.png" style="margin-top:10px;margin-bottom:15px;">
	<a href="<?php echo base_url() ?>" class="item"><i class="h square icon single-title"></i> Home</a>
	<?php /*
	<div class="item">
		<a class="title">
			<i class="chevron down icon"></i><i class="suitcase icon"></i> Master Data
		</a>
		<div class="content">
			<a href="<?php echo base_url() ?>index.php/C_kelompok_barang" class="item item-sub">Kelompok Barang Pajangan</a>
			<a href="<?php echo base_url() ?>index.php/C_nama_barang" class="item item-sub">Nama Barang Pajangan</a>
			<a href="<?php echo base_url() ?>index.php/C_box" class="item item-sub">Box Kotak / Barang</a>
			<a href="<?php echo base_url() ?>index.php/C_bayar_nontunai" class="item item-sub">Pembayaran Non Tunai</a>
			<a href="<?php echo base_url() ?>index.php/C_customer" class="item item-sub">Data Customer</a>
		</div>
	</div>
	
	<div class="item">
		<a class="title">
			<i class="chevron down icon"></i><i class="hdd outline icon"></i> Kontrol Barang Pajangan
		</a>
		<div class="content">
			<a href="<?php echo base_url() ?>index.php/C_stock_in" class="item item-sub">Stock In</a>
			<a href="<?php echo base_url() ?>index.php/C_stock_out" class="item item-sub">Stock Out</a>
			<a href="<?php echo base_url() ?>index.php/C_pindah_box" class="item item-sub">Pindah Box</a>
			<a href="<?php echo base_url() ?>index.php/C_posisi_pajangan" class="item item-sub">Posisi Stock Pajangan</a>
		</div>
	</div>
	
	<div class="item">
		<a class="title">
			<i class="chevron down icon"></i><i class="list alternate outline icon"></i> Transaksi Usaha
		</a>
		<div class="content">
			<a href="<?php echo base_url() ?>index.php/C_trans_cabang" class="item item-sub">Transaksi Emas Antar Cabang</a>
			<a href="<?php echo base_url() ?>index.php/C_mutasi_kas" class="item item-sub">Mutasi Kas/Bank (Rupiah)</a>
			<a href="<?php echo base_url() ?>index.php/C_mutasi_mas" class="item item-sub">Mutasi Emas (Gram)</a>
			<a href="<?php echo base_url() ?>index.php/C_titipan_rp" class="item item-sub">Titipan Pelanggan (Rupiah)</a>
			<a href="<?php echo base_url() ?>index.php/C_titipan_gr" class="item item-sub">Titipan Pelanggan (Emas)</a>
			<a href="<?php echo base_url() ?>index.php/C_pesanan" class="item item-sub">Pesanan Pelanggan</a>
		</div>
	</div>
	
	<div class="item">
		<a class="title">
			<i class="chevron down icon"></i><i class="book icon"></i> Laporan
		</a>
		<div class="content">
			<a href="<?php echo base_url() ?>index.php/C_lap_pajangan" class="item item-sub">Laporan Emas Pajangan</a>
			<a href="<?php echo base_url() ?>index.php/C_lap_reparasi" class="item item-sub">Laporan Reparasi Harian</a>
			<a href="<?php echo base_url() ?>index.php/C_lap_saldo_mas" class="item item-sub">Laporan Saldo Emas (Gram)</a>
			<a href="<?php echo base_url() ?>index.php/C_kartu_pik" class="item item-sub">Kartu Piutang Karyawan</a>
			<a href="<?php echo base_url() ?>index.php/C_lain_rp" class="item item-sub">Laporan Account Lain (Rupiah)</a>
			<a href="<?php echo base_url() ?>index.php/C_lain_gr" class="item item-sub">Laporan Account Lain (Gram)</a>
			<a href="<?php echo base_url() ?>index.php/C_sell_chart" class="item item-sub">Laporan Grafik Penjualan</a>
			<a href="<?php echo base_url() ?>index.php/C_lap_tahunan" target="_blank" class="item item-sub">Laporan Tahunan</a>
		</div>
	</div>
	
	<?php if($this->session->userdata('gold_admin') == 'Y'){ ?>
	<div class="item">
		<a class="title">
			<i class="chevron down icon"></i><i class="cogs icon"></i> Pengaturan
		</a>
		<div class="content">
			<a href="<?php echo base_url() ?>index.php/C_user" class="item item-sub">Data User</a>
			<a href="<?php echo base_url() ?>index.php/C_setting_harga" class="item item-sub">Setting Harga Jual Beli</a>
			<a href="<?php echo base_url() ?>index.php/C_import_pajangan" class="item item-sub">Import Pajangan</a>
		</div>
	</div>
	<?php } ?>
	*/ ?>
</div>
<div class="container no-print utama" style="margin-left:260px;">
	<div id="menu_utama" class="ui basic icon top fixed menu utama" style="border-radius:0; left:auto; padding-right:260px">
		<a id="toggle" class="item" onclick=geserKiri()>
			<i id="panah_sidebar" class="chevron left icon"></i>
		</a>
		<div class="right menu" style="font-size:15px">
			<a class="ui browse item"><i class="users icon" style="margin-right:5px;"></i> <?php echo $this->session->userdata('gold_nama_user') ?></a>
			
			<div class="ui flowing popup top left transition hidden">
				<div class="ui link list">
					<a href="<?php echo base_url()?>index.php/C_change_pass" class="item" style="text-align:left;"><i class="key icon"></i> Change Password</a>
					<a href="<?php echo base_url()?>index.php/C_login/logout" class="item" style="text-align:left;"><i class="share icon"></i> Logout</a>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$('.ui.accordion').accordion();
	
	$('.menu .browse').popup({
		inline     : true,
		hoverable  : true,
		position   : 'bottom left',
		delay: {
			show: 300,
			hide: 800
		}
	});
	
	$('body').css('padding-top', $('#top-menu').height());
	
	function geserKiri(){
		document.getElementById("sidebar_menu").setAttribute('style','display:none');
		document.getElementById("menu_utama").setAttribute('style','border-radius:0');
		var list, index;
		list = document.getElementsByClassName("pusher");
		for (index = 0; index < list.length; ++index) {
			list[index].setAttribute('style','padding-top:43px;margin-left:0px');
		}
		
		document.getElementById("panah_sidebar").classList.remove("chevron");
		//document.getElementById("panah_sidebar").classList.remove("double");
		document.getElementById("panah_sidebar").classList.remove("left");
		document.getElementById("panah_sidebar").classList.remove("icon");
		document.getElementById("panah_sidebar").className += "chevron right icon";
		document.getElementById("toggle").setAttribute('onclick','geserKanan()');
	}
	
	function geserKanan(){
		document.getElementById("sidebar_menu").setAttribute('style','');
		document.getElementById("menu_utama").setAttribute('style','border-radius: 0;left: auto;padding-right: 260px;');
		var list, index;
		list = document.getElementsByClassName("pusher");
		for (index = 0; index < list.length; ++index) {
			list[index].setAttribute('style','padding-top:43px;margin-left:260px');
		}
		
		document.getElementById("panah_sidebar").classList.remove("chevron");
		//document.getElementById("panah_sidebar").classList.remove("double");
		document.getElementById("panah_sidebar").classList.remove("right");
		document.getElementById("panah_sidebar").classList.remove("icon");
		document.getElementById("panah_sidebar").className += "chevron left icon";
		document.getElementById("toggle").setAttribute('onclick','geserKiri()');
	}
</script>