<div class="ui sidebar vertical left inverted menu accordion no-print">
	<img class="ui centered medium image" src="<?php echo base_url()?>assets/images/branding/brand.png" style="margin-top:10px;margin-bottom:15px;">
	<a href="<?php echo base_url() ?>" class="item"><i class="h square icon single-title"></i> Home</a>
	
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
			<a href="<?php echo base_url() ?>index.php/C_unlock_product" class="item item-sub">Unlock Kontrol Harga</a>
			<a href="<?php echo base_url() ?>index.php/C_history_product" class="item item-sub">History Product</a>
		</div>
	</div>
	
	<div class="item">
		<a class="title">
			<i class="chevron down icon"></i><i class="list alternate outline icon"></i> Transaksi Usaha
		</a>
		<div class="content">
			<?php /*<a href="<?php echo base_url() ?>index.php/C_kirim_beli" class="item item-sub">Pengiriman Barang Pembelian</a> */ ?>
			<a href="<?php echo base_url() ?>index.php/C_trans_cabang" class="item item-sub">Transaksi Emas Antar Cabang</a>
			<a href="<?php echo base_url() ?>index.php/C_mutasi_kas" class="item item-sub">Mutasi Kas/Bank (Rupiah)</a>
			<a href="<?php echo base_url() ?>index.php/C_mutasi_mas" class="item item-sub">Mutasi Emas (Gram)</a>
			<a href="<?php echo base_url() ?>index.php/C_titipan_rp" class="item item-sub">Titipan Pelanggan (Rupiah)</a>
			<a href="<?php echo base_url() ?>index.php/C_titipan_gr" class="item item-sub">Titipan Pelanggan (Emas)</a>
			<a href="<?php echo base_url() ?>index.php/C_jumum" class="item item-sub">Jurnal Umum</a>
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
</div>
<div class="ui basic icon top fixed menu no-print">
	<a id="toggle" class="item">
		<i class="sidebar icon"></i>
	</a>
	<div class="right menu" style="font-family:'Segoe UI' !important; font-size:15px">
		<a class="ui browse item"><i class="users icon" style="margin-right:5px;"></i> <?php echo $this->session->userdata('gold_nama_user') ?></a>
		
		<div class="ui flowing popup top left transition hidden">
			<div class="ui link list">
				<a href="<?php echo base_url()?>index.php/C_change_pass" class="item" style="text-align:left;"><i class="key icon"></i> Change Password</a>
					<a href="<?php echo base_url()?>index.php/C_login/logout" class="item" style="text-align:left;"><i class="share icon"></i> Logout</a>
			</div>
		</div>
	</div>
</div>

<script>
	$('.ui.accordion')
		.accordion()
	;
	
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