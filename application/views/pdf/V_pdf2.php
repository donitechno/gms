<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view('V_link') ?>
</head>
<style>
.lap_pdf, .lap_pdf_2, .lap_pdf_3, .lap_pdf_4, .lap_pdf_5, .lap_pdf_6{
	font-family:"samsungone";
}

.lap_pdf td{
	font-size:13px;
	padding: 2px 10px;
}

.lap_pdf_2 td{
	font-size:13px;
	border:1px solid #888;
	padding: 2px 10px;
}

.lap_pdf_3 th{
	font-size:14px;
	border:1px solid #bdc3c7;
	background:#f0f0f0;
	color:#34495e;
	padding: 5px 10px;
}

.lap_pdf_3 td{
	font-size:14px;
	border:1px solid #bdc3c7;
	padding: 5px 10px;
}

.lap_pdf_4 th{
	font-size:14px;
	border:1px solid #bdc3c7;
	background:#f0f0f0;
	color:#34495e;
	padding: 5px 10px;
}

.lap_pdf_4 td{
	font-size:14px;
	border:1px solid #bdc3c7;
	padding: 4px 10px;
}

.lap_pdf_5 td{
	font-size:13px;
	padding: 0px 10px;
}

.th-5{
	border-top:1px dotted #000;
	border-bottom:1px dotted #000;
	text-transform:uppercase;
	font-size:13px;
}

.lap_pdf_6 th{
	font-size:12px;
	padding: 0px 10px;
}

.lap_pdf_6 td{
	font-size:10px;
	padding: 0px 2px;
	vertical-align: text-top;
}

.sup_data{
	font-size:11px;
}

.theader{
	text-align:center;
	font-weight:bold;
	text-transform:uppercase;
	border-bottom:1px dotted #000;
}

.td-bold{
	font-weight:bold;
}

.double-top{
	font-weight:bold;
	border-top:1px dotted #000;
}

.td-total{
	font-weight:bold;
	border-top: 1px dotted !important;
    border-bottom: 1px solid #000 !important;
}

.right-aligned{
	text-align:right;
}

.center-aligned{
	text-align:center;
}

.ket-ttd, .nama-ttd{
	float:left;
	width:33%;
	font-size:14px;
	text-align: center;
}

.ket-ttd2{
	float:left;
	width:13%;
	font-size:12px;
	text-align: right;
	padding-top: 10px;
}

.nama-ttd2{
	float:left;
	width:20.3%;
	font-size:12px;
	text-align: center;
	padding-top: 43px;
}

.nama-ttd span, .nama-ttd2 span{
	border-top: 1px solid #000;
}
</style>
<body>
	<div class="ui container fluid">
		<div class="ui grid"><div class="sixteen wide centered column"><?php echo $view ?></div></div>
	</div>
</div>
</body>
</html>