<?php
	/*-------------------------
	Autor: Obed Alvarado
	Web: obedalvarado.pw
	Mail: info@obedalvarado.pw
	---------------------------*/
	session_start();
	if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
        header("location: login.php");
		exit;
        }
	if (!file_exists ('config/db.php')){
		header("location: install/paso1.php");
		exit;
	}	
	$active_inicio="";
	$active_cotizaciones="active";
	$active_productos="";
	$active_fabricantes="";
	$active_usuarios="";
	$active_clientes="";
	$active_empresa="";	
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cotizador - Panel de Control</title>

<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/datepicker3.css" rel="stylesheet">
<link href="css/styles.css" rel="stylesheet">
<link rel="icon" href="img/cart_icon.png">
<!--Icons-->
<script src="js/lumino.glyphs.js"></script>

<!--[if lt IE 9]>
<script src="js/html5shiv.js"></script>
<script src="js/respond.min.js"></script>
<![endif]-->

</head>

<body>
	<?php 
	include("navbar.php");
	include("sidebar.php");
	include("modal/buscar_productos.php");
	?>
	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="#"><svg class="glyph stroked bag"><use xlink:href="#stroked-bag"></use></svg></a></li>
				<li class="active">Cotizaciones</li>
			</ol>
			
		</div><!--/.row-->
		
		<div class="row">
			
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4><i class='glyphicon glyphicon-edit'></i> Nueva Cotización</h4>
					</div>
					<div class="panel-body">
					
						<form class="form-horizontal" role="form" id="datos_cotizacion">
							<div class="form-group row">
							  <label for="nombre_cliente" class="col-md-2 control-label">Selecciona el cliente:</label>
							  <div class="col-md-3">
								  <input type="text" class="form-control input-sm" id="nombre_cliente" placeholder="Ingresa el nombre del cliente" required>
								  <input id="id_cliente" type='hidden'>	
							 </div>
							  <label for="atencion" class="col-md-1 control-label">Atención:</label>
								<div class="col-md-2">
									<input type="text" class="form-control input-sm" id="atencion" placeholder="" readonly>
								</div>
								
								<label for="tel1" class="col-md-1 control-label">Teléfono:</label>
								<div class="col-md-2">
									<input type="text" class="form-control input-sm" id="tel1" placeholder="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="empresa" class="col-md-2 control-label">Empresa:</label>
								<div class="col-md-3">
									<input type="text" class="form-control input-sm" id="empresa" placeholder="" readonly>
								</div>
								<label for="tel2" class="col-md-1 control-label">Teléfono:</label>
								<div class="col-md-2">
									<input type="text" class="form-control input-sm" id="tel2" placeholder="" readonly>
								</div>
								<label for="email" class="col-md-1 control-label">Email:</label>
								<div class="col-md-3">
									<input type="email" class="form-control input-sm" id="email" placeholder="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="condiciones" class="col-md-2 control-label">Condiciones de pago:</label>
								<div class="col-md-3">
									<select class="form-control input-sm" id="condiciones" required>
										<option value="">Selecciona condiciones de pago</option>
										<option value='Contado' selected>Contado</option>
										<option value='Crédito 30 días'>Crédito 30 días</option>
										<option value='Crédito 45 días'>Crédito 45 días</option>
										<option value='Crédito 60 días'>Crédito 60 días</option>
										<option value='Crédito 90 días'>Crédito 90 días</option>
									</select>
								</div>
								<label for="validez" class="col-md-1 control-label">Validez:</label>
								<div class="col-md-2">
									<select class="form-control input-sm" id="validez" required>
										<option value="">Selecciona validez de oferta</option>
										<option value='5 días' >5 días</option>
										<option value='10 días'>10 días</option>
										<option value='15 días' selected>15 días</option>
										<option value='30 días'>30 días</option>
										<option value='60 días'>60 días</option>
									</select>
								</div>
								<label for="entrega" class="col-md-1 control-label">Tiempo:</label>
								<div class="col-md-3">
									<input type="text" class="form-control input-sm" id="entrega" placeholder="Tiempo de entrega" value="Inmediato">
								</div>
							</div>
							
							<div class="form-group row">
								<label for="condiciones" class="col-md-2 control-label">Nota:</label>
								<div class="col-md-6">
									<input type="text" class="form-control input-sm" id="notas" placeholder="Nota"maxlength='255' >
								</div>
								
								
							</div>
							<hr>
							
							<div class="col-md-12">
								<div class="pull-right">
									<button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal">
									 <span class="glyphicon glyphicon-plus"></span> Agregar productos
									</button>
									<button type="submit" class="btn btn-default">
									  <span class="glyphicon glyphicon-print"></span> Imprimir
									</button>
								</div>	
							</div>
						</form>	
						<div id="resultados" class='col-md-12' style="margin-top:10px"></div><!-- Carga los datos ajax -->
					</div>
				</div>
			</div>
		</div><!-- /.row -->
	</div>	<!--/.main-->
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/VentanaCentrada.js"></script>
	<script type="text/javascript" src="js/nueva_cotizacion.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	</body>
</html>
