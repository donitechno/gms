<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view('V_link') ?>
</head>
<style>
.lap_pdf_neraca{
	font-family:"samsungone";
}

.lap_pdf_neraca th{
	font-size:10px;
	padding: 2px 10px;
	text-align:center;
}

.lap_pdf_neraca td{
	font-size:10px;
	padding: 2px 10px;
	vertical-align:top;
}

.th-border{
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	font-weight:bold;
	text-transform:uppercase;
}

.th-border-top{
	border-top:1px solid #000;
	font-weight:bold;
	text-transform:uppercase;
}

.th-border-bottom{
	border-bottom:1px solid #000;
	font-weight:bold;
	text-transform:uppercase;
}

.th-no-border{
	font-weight:bold;
	text-transform:uppercase;
}

.td-total{
	border-top:1px dotted #000;
	font-weight:bold;
}

.right-aligned{
	text-align:right;
}

</style>
<body>
	<div class="ui container fluid">
		<div class="ui grid" style="float:left">
			<div class="sixteen wide centered column" style="width:100%;float:left"><?php echo $header ?></div>
			<div class="eight wide centered column" style="width:58%;float:left"><?php echo $viewkiri ?></div>
			<div class="eight wide centered column" style="width:38%;float:left;margin-left:4%"><?php echo $viewkanan ?></div>
		</div>
	</div>
</div>
</body>
</html>