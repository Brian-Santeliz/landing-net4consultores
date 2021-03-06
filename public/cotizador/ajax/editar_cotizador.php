<?php
	/*-------------------------
	Autor: Obed Alvarado
	Web: obedalvarado.pw
	Mail: info@obedalvarado.pw
	---------------------------*/
session_start();
$numero_cotizacion=intval($_SESSION['numero_cotizacion']);//Datos de SESSION
if (isset($_POST['id'])){$id=$_POST['id'];}
if (isset($_POST['cantidad'])){$cantidad=$_POST['cantidad'];}
if (isset($_POST['precio_venta'])){$precio_venta=$_POST['precio_venta'];}
if (isset($_POST['descuento'])){$descuento=$_POST['descuento'];}

	/* Connect To Database*/
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
$update_iva=false;	
if (!empty($id) and !empty($cantidad) and !empty($precio_venta))
{
$insert_tmp=mysqli_query($con, "INSERT INTO detail_estimate (numero_cotizacion,id_producto, cantidad,descuento, precio_venta) VALUES ('$numero_cotizacion','$id','$cantidad','$descuento','$precio_venta')");
$update_iva=true;
}
if (isset($_GET['id']))//codigo elimina un elemento de la DB
{
$id_tmp=intval($_GET['id']);	
$delete=mysqli_query($con, "DELETE FROM detail_estimate WHERE id_detalle_cotizacion='".$id_tmp."'");
$update_iva=true;
}
if (isset($_GET['action'])){//Actualizacion de los datos
	$campo=intval($_GET['campo']);
	if ($campo==1){
		$valor=intval($_GET['valor']);
		$sentencia_sql="id_cliente='$valor'";
	} else if ($campo==2){
		$valor=mysqli_real_escape_string($con,(strip_tags($_REQUEST['valor'], ENT_QUOTES)));
		$sentencia_sql="condiciones='$valor'";
	} else if ($campo==3){
		$valor=mysqli_real_escape_string($con,(strip_tags($_REQUEST['valor'], ENT_QUOTES)));
		$sentencia_sql="validez='$valor'";
	} else if ($campo==4){
		$valor=mysqli_real_escape_string($con,(strip_tags($_REQUEST['valor'], ENT_QUOTES)));
		$sentencia_sql="entrega='$valor'";
	} else if ($campo==5){
		$valor=mysqli_real_escape_string($con,(strip_tags($_REQUEST['valor'], ENT_QUOTES)));
		$sentencia_sql="notas='$valor'";
	} else if ($campo==6){
		$valor=mysqli_real_escape_string($con,(strip_tags($_REQUEST['valor'], ENT_QUOTES)));
		$sentencia_sql="status='$valor'";
	} else {
		$sentencia_sql="";
	}
	$update=mysqli_query($con, "update estimates set $sentencia_sql where numero_cotizacion='$numero_cotizacion'");
}
/*Datos de la empresa*/
	$sql_empresa=mysqli_query($con,"SELECT * FROM empresa where id_empresa=1");
	$rw_empresa=mysqli_fetch_array($sql_empresa);
	$iva=$rw_empresa["iva"];
	$moneda=$rw_empresa["moneda"];
/*Fin datos empresa*/

?>
<table class="table">
<tr>
	<th>CODIGO</th>
	<th>CANT.</th>
	<th>DESCRIPCION</th>
	<th><span class="pull-right">PRECIO UNIT.</span></th>
	<th><span class="pull-right">DESCUENTO</span></th>
	<th><span class="pull-right">PRECIO TOTAL</span></th>
	<th></th>
</tr>
<?php
	$sumador_descuento=0;
	$sumador_total=0;
	$sql=mysqli_query($con, "select * from products, detail_estimate, estimates where products.id_producto=detail_estimate.id_producto and estimates.numero_cotizacion=detail_estimate.numero_cotizacion and estimates.numero_cotizacion='".$numero_cotizacion."'");
	while ($row=mysqli_fetch_array($sql))
	{
	$id_tmp=$row["id_detalle_cotizacion"];
	$codigo_producto=$row['codigo_producto'];
	$cantidad=$row['cantidad'];
	$porcentaje=$row['descuento'] / 100;
	$nombre_producto=$row['nombre_producto'];
	$id_marca_producto=$row['id_marca_producto'];
	if (!empty($id_marca_producto))
	{
	$sql_marca=mysqli_query($con, "select nombre_marca from manufacturers where id_marca='$id_marca_producto'");
	$rw_marca=mysqli_fetch_array($sql_marca);
	$nombre_marca=$rw_marca['nombre_marca'];
	$marca_producto=" ".strtoupper($nombre_marca);
	}
	else {$marca_producto='';}
	$precio_venta=$row['precio_venta'];
	$precio_venta_f=number_format($precio_venta,2);//Formateo variables
	$precio_venta_r=str_replace(",","",$precio_venta_f);//Reemplazo las comas
	$precio_total=$precio_venta_r*$cantidad;
	$precio_total_f=number_format($precio_total,2);//Precio total formateado
	$precio_total_r=str_replace(",","",$precio_total_f);//Reemplazo las comas
	$total_descuento=$precio_total_r*$porcentaje;//Total descuento
	$total_descuento=number_format($total_descuento,2,'.','');//Formateo de numeros sin separador de miles
	$sumador_descuento+=$total_descuento;
	$sumador_total+=$precio_total_r;//Sumador
	
		?>
		<tr>
			<td><?php echo $codigo_producto;?></td>
			<td><?php echo $cantidad;?></td>
			<td><?php echo $nombre_producto.$marca_producto;?></td>
			<td><span class="pull-right"><?php echo $precio_venta_f;?></span></td>
			<td><span class="pull-right"><?php echo number_format($total_descuento,2);?></span></td>
			<td><span class="pull-right"><?php echo $precio_total_f;?></span></td>
			<td ><span class="pull-right"><a href="#" onclick="eliminar('<?php echo $id_tmp ?>')"><i class="glyphicon glyphicon-trash"></i></a></span></td>
		</tr>		
		<?php
		$validez=$row['validez'];
		$condiciones=$row['condiciones'];
		$entrega=$row['entrega'];
		$total_iva=$row['total_iva'];
		
	}
	$total_parcial=number_format($sumador_total,2,'.','');
	$sumador_descuento=number_format($sumador_descuento,2,'.','');
	$total_neto=$total_parcial-$sumador_descuento;
	$total_neto=number_format($total_neto,2,'.','');
	if ($update_iva){
		$total_iva=($total_neto*$iva) / 100;
		$total_iva=number_format($total_iva,2,'.','');
		$update=mysqli_query($con,"update estimates set total_neto='$total_neto', total_iva='$total_iva' where numero_cotizacion='$numero_cotizacion'");
	} else {
		$total_iva=number_format($total_iva,2,'.','');
		$iva=($total_iva/$total_neto) * 100;
		$iva=ceil($iva);
	}
	$total_cotizacion=$total_neto+$total_iva;
	
	
?>
<tr>
	<td colspan=5><span class="pull-right">PARCIAL <?php echo $moneda;?></span></td>
	<td><span class="pull-right"><? echo number_format($total_parcial,2);?></span></td>
	<td></td>
</tr>
<tr>
	<td colspan=5><span class="pull-right">DESCUENTO <?php echo $moneda;?></span></td>
	<td><span class="pull-right"><? echo number_format($sumador_descuento,2);?></span></td>
	<td></td>
</tr>
<tr>
	<td colspan=5><span class="pull-right">NETO <?php echo $moneda;?></span></td>
	<td><span class="pull-right"><? echo number_format($total_neto,2);?></span></td>
	<td></td>
</tr>
<tr>
	<td colspan=5><span class="pull-right">IVA <?php echo "$iva% $moneda";?></span></td>
	<td><span class="pull-right"><? echo number_format($total_iva,2);?></span></td>
	<td></td>
</tr>
<tr>
	<td colspan=5><span class="pull-right">TOTAL <?php echo $moneda;?></span></td>
	<td><span class="pull-right"><? echo number_format($total_cotizacion,2);?></span></td>
	<td></td>
</tr>
</table>
