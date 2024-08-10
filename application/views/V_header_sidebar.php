<div class="ui sidebar vertical left inverted menu accordion no-print">
	<img class="ui centered medium image" src="<?php echo base_url()?>assets/images/branding/brand.png" style="margin-top:10px;margin-bottom:15px;">
	<a href="<?php echo base_url() ?>" class="item"><i class="h square icon single-title"></i> Home</a>
	
	<div class="item">
		<a class="title">
			<i class="chevron down icon"></i><i class="suitcase icon"></i> Master Data
		</a>
		<div class="content">
			<a class="item item-sub" id="kelompokBarang">Kelompok Barang Pajangan</a>
			<a class="item item-sub" id="namaBarang">Nama Barang Pajangan</a>
			<a class="item item-sub" id="boxBarang">Box Kotak / Barang</a>
			<a class="item item-sub" id="nonTunai">Pembayaran Non Tunai</a>
			<a class="item item-sub" id="dataCustomer">Data Customer</a>
		</div>
	</div>
	
	<div class="item">
		<a class="title">
			<i class="chevron down icon"></i><i class="hdd outline icon"></i> Kontrol Barang Pajangan
		</a>
		<div class="content">
			<a class="item item-sub" id="stockIn">Stock In</a>
			<a class="item item-sub" id="stockOut">Stock Out</a>
			<a class="item item-sub" id="pindahBox">Pindah Box</a>
			<a class="item item-sub" id="posisiPajangan">Posisi Stock Pajangan</a>
			<a class="item item-sub" id="unlockHarga">Unlock Kontrol Harga</a>
			<a class="item item-sub" id="historyProduct">History Product</a>
			<a class="item item-sub" id="dataProduct">Data Product</a>
		</div>
	</div>
	
	<div class="item">
		<a class="title">
			<i class="chevron down icon"></i><i class="list alternate outline icon"></i> Transaksi Usaha
		</a>
		<div class="content">
			<?php /*<a href="<?php echo base_url() ?>index.php/C_kirim_beli" class="item item-sub">Pengiriman Barang Pembelian</a> */ ?>
			<a class="item item-sub" id="transCabang">Transaksi Emas Antar Cabang</a>
			<a class="item item-sub" id="mutasiKas">Mutasi Kas/Bank (Rupiah)</a>
			<a class="item item-sub" id="mutasiGram">Mutasi Emas (Gram)</a>
			<a class="item item-sub" id="titipanRp">Titipan Pelanggan (Rupiah)</a>
			<a class="item item-sub" id="titipanGram">Titipan Pelanggan (Emas)</a>
			<a class="item item-sub" id="jurnalUmum">Jurnal Umum</a>
					</div>
	</div>
	
	<div class="item">
		<a class="title">
			<i class="chevron down icon"></i><i class="book icon"></i> Laporan
		</a>
		<div class="content">
			<a class="item item-sub" id="lapKasir">Laporan Jual Beli</a>
			<a class="item item-sub" id="lapPajangan">Laporan Emas Pajangan</a>
			<a class="item item-sub" id="lapReparasi">Laporan Reparasi Harian</a>
			<a class="item item-sub" id="lapSaldoGr">Laporan Saldo Emas (Gram)</a>
			<a class="item item-sub" id="kartuPik">Kartu Piutang Karyawan</a>
			<a class="item item-sub" id="lapAccountRp">Laporan Account Lain (Rupiah)</a>
			<a class="item item-sub" id="lapAccountGr">Laporan Account Lain (Gram)</a>
			<a class="item item-sub" id="lapGrafik">Laporan Grafik Penjualan</a>
			<a class="item item-sub" id="bestCS">Best Customer Service</a>
			<a class="item item-sub" id="lapPesananPer">Laporan Pesanan Per Tanggal</a>
			<a class="item item-sub" id="lapKasirHarian">Laporan Kasir Harian</a>
			<a class="item item-sub" id="lapNeracaHarian">Laporan Neraca Harian</a>
			<a class="item item-sub" id="lapTahunan">Laporan Tahunan</a>
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
	    <a class="ui item"> <strong><?php echo $_COOKIE['cabang'] ?></strong></a>
		<a class="ui browse item"><i class="users icon" style="margin-right:5px;"></i> <?php echo $this->session->userdata('gold_nama_user') ?></a>
		
		<div class="ui flowing popup top left transition hidden">
			<div class="ui link list">
				<a href="<?php echo base_url()?>index.php/C_change_pass" class="item" style="text-align:left;"><i class="key icon"></i> Change Password</a>
					<a href="<?php echo base_url()?>index.php/C_login/logout" class="item" style="text-align:left;"><i class="share icon"></i> Logout</a>
					<a href="<?php echo base_url()?>index.php/C_sync" class="item" style="text-align:left;"><i class="share icon"></i> Sync Database</a>
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