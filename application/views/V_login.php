<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="icon" href="<?php echo base_url() ?>assets/images/branding/pembukuan.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo base_url() ?>assets/images/branding/pembukuan.ico" type="image/ico" />
	<title>PT Banda Baru Mas</title>

	<!-- SCRIPTS -->
	<!-- JQuery -->
	<script src="<?php echo base_url() ?>assets/js/jquery-3.2.1.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/jquery-ui.js"></script>
	<!-- Semantic UI JS -->
	<script src="<?php echo base_url() ?>assets/js/semantic.js"></script>
	
	<?php echo link_tag('assets/css/gold.css'); ?>
	<!-- JQuery UI -->
	<?php echo link_tag('assets/css/jquery-ui.css'); ?>
	<?php echo link_tag('assets/css/jquery-ui.min.css'); ?>
	<!-- Semantic UI -->
	<?php echo link_tag('assets/css/semantic.min.css'); ?>
</head>

<style>
<?php $random = rand(1,8); ?>
body{
	margin:0 auto;
	background: #FFF url(/gms/assets/images/login_background<?php echo $random; ?>.jpg) no-repeat center top;
	-webkit-background-size: cover;
	-moz-background-size: cover;
	background-size: cover;
}

body > .grid{
      height: 100%;
}
.column{
  max-width: 450px;
}

.ui.form input:not([type]), .ui.form input[type=date], .ui.form input[type=datetime-local], .ui.form input[type=email], .ui.form input[type=file], .ui.form input[type=number], .ui.form input[type=password], .ui.form input[type=search], .ui.form input[type=tel], .ui.form input[type=text], .ui.form input[type=time], .ui.form input[type=url]{
	border:none;
	border-bottom:2px solid red;
	border-radius:0px;
	background:none !important;
}

.ui.form input[type=text]:focus, .ui.form input[type=password]:focus{
	border:none;
	border-bottom:2px solid red;
	border-radius:0px;
	background:none !important;
}

.login{
	width:40%;
	font-family:"Segoe UI" !important;
	background: #FBC02D !important;
    background: -webkit-linear-gradient(to right, #D32F2F, #FBC02D) !important;
    background: linear-gradient(to right, #D32F2F, #FBC02D) !important;
    border: none !important;
}
</style>

<body>
	<div class="ui middle aligned center aligned grid">
		<div class="column">
			<form id="form_login" action="<?php echo base_url()?>index.php/C_login/cek_login" method="post" class="ui large form">
			<div id="wrap_login" class="ui raised segment" style="padding:3em;background:rgba(250,250,250,0.93);">
				<img class="ui centered medium image" src="<?php echo base_url()?>assets/images/branding/brand.png" style="margin-top:10px;margin-bottom:15px;">
				<div class="ui red message" id="error_wrap" style="display:none"></div>
				<div class="field">
					<div class="ui left icon input large">
						<i class="red users icon" style="opacity:1"></i>
						<input type="text" id="username" name="username" placeholder="Username" style="font-family:'Segoe UI'" autocomplete="off" onkeyup=entToSubmit("username") autofocus>
					</div>
				</div>
				<div class="field" style="margin-bottom:35px">
					<div class="ui left icon input large">
						<i class="red lock icon" style="opacity:1"></i>
						<input type="password" id="password" name="password" placeholder="Password" style="font-family:'Segoe UI'" autocomplete="off" onkeyup=entToSubmit("password")>
					</div>
				</div>
				<div class="field" style="margin-bottom:35px">
					<div class="ui left icon input large" >
						<i class="red users icon" style="opacity:1;"></i>
						<select name="cabang" id="cabang" class="form-control custom-select" style="padding-left:35px;">
								
									<?php
											foreach($gold_site as $s){
												?>
													<option value="<?php echo $s->sitecode ?>"><?php echo $s->sitedesc ?></option>
									<?php				
										}
									?>
								</select>
					</div>
				</div>
				<button type="button" class="ui login primary big animated button" id="btn-submit" onclick="submitLogin()">
					<div class="hidden content">Login</div>
					<div class="visible content">
						<i class="plane icon"></i>
					</div>
				</button>
			</div>
			</form>
		</div>
	</div>
	<?php //$this->load->view('V_footer'); ?>
</body>
<script>
	function entToSubmit(varSub){
		var x = event.keyCode;
		if(x == 13){
			if(varSub == 'username'){
				document.getElementById("password").focus();
			}else{
				submitLogin();
			}
		}
	}
	
	function submitLogin(){
		document.getElementById("error_wrap").setAttribute('style','display:none');
		document.getElementById("error_wrap").innerHTML = '';
		document.getElementById("btn-submit").className += " loading";
		
		$.ajax({
			url: $("#form_login").attr('action'),
			type: 'post',
			data: $("#form_login").serialize(),
			dataType: 'json',
			success: function(response){
				if(response.success == true){
					window.location= response.lokasi;
				}else{
					document.getElementById("error_wrap").innerHTML = "Wrong Username and Password!";
					document.getElementById("error_wrap").setAttribute('style','');
					document.getElementById("username").focus();
					
					document.getElementById("btn-submit").classList.remove("loading");
				}
			}
		})
	}
</script>
</html>