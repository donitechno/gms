<!DOCTYPE html>
<html lang="en">
<head>
   <?php $this->load->view('V_link') ?>
</head>
<style>
.pusher{
	padding-top:55px;
	margin-left: 0px !important;
}

.td-bold{
	font-weight:600;
}

.dash-top{
	border-top:1px dashed #000 !important;
}

.double-top{
	border-top:double #000 !important;
}

.ui.table thead th, .ui.table tbody td{
	padding: 0.3em 0.78571429em;
	//font-size:0.9em;
}

.td-total{
	font-weight:600;
	border-top:double #34495e !important;
}

.theader{
	text-align:left !important;
	font-weight:600;
	background:#f9fafb;
}
</style>
<body>
	<?php $this->load->view('V_header_sidebar'); ?>
	<div class="pusher">
		<div class="ui container fluid">
			<div class="ui top attached tabular menu" id="menuTab">
			</div>
			<div id="menuContent">
			</div>
		</div>
	</div>
	<div class="ui modal mini" id="menuModal"></div>
</body>
</html>
<?php $this->load->view('V_link_js') ?>
<script>
	$('.menu .item')
	  .tab()
	;
</script>