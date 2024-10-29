<?php
namespace openComex;
include("../../../../libs/php/utility.php");
?>
<!--
	/**
	 * Ventana Auxiliar para traer DOS validos segun consulta.
	 * --- Descripcion: Permite por Metodo valid (es decir si trae un numero valido en la consulta DO's) o por metodo
	 * Window(si lo que viene no es valido, y debe generar una ventana con datos para escoger.
	 * @author Pedro Leon Burbano Suarez <pedrob@repremundo.com.co>
	 * @version 001
	 */ -->
<?php if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Documentos de Comercio Exterior</title>
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
	   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
	  </head>
	  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>

	  <center>
			<table border ="0" cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td>
						<fieldset>
			   			<legend>Documentos de Comercio Exterior</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<input type = "hidden" name = "vIteration" value = "<?php echo $gIteration ?>">
	  						<input type = "hidden" name = "vMatriz"    value = "">
	  						<?php
	  							switch ($gWhat) {
	  								case "VALID":
		  									/**
		  									 * Consulto en la tabla 121 los DO's, validos que en su campo DOCFORMS sean diferentes a NO.
		  									 */
		  									$zSqlUsr  = "SELECT * ";
				  							$zSqlUsr .= "FROM $cAlfa.SIAI0003 ";
				  							$zSqlUsr .= "WHERE USRIDXXX = \"{$_COOKIE['kUsrId']}\" AND ";
			  							  $zSqlUsr .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
			  							  $zCrsUsr = mysql_query($zSqlUsr,$xConexion01);
			  							  $zRUsr = mysql_fetch_array($zCrsUsr);
			  							//  if($zRUsr['USRIDXXX']==$zRUsr['USRID2XX']){
			  							    $zSqlDoi  = "SELECT * ";
				  							  $zSqlDoi .= "FROM $cAlfa.sys00121 ";
				  							  $zSqlDoi .= "WHERE docidxxx = \"$gDocNro\" AND ";
				  							  $zSqlDoi .= "regusrxx = \"{$_COOKIE['kUsrId']}\" AND ";
				  							  //$zSqlDoi .= "sucidxxx = \"{$zRUsr['sucidxxx']}\" AND ";
			  							    $zSqlDoi .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
			  							 /* }else{
			  							    $zSqlUsr2  = "SELECT * ";
				  							  $zSqlUsr2 .= "FROM $cAlfa.SYS00001 ";
				  							  $zSqlUsr2 .= "WHERE USRIDXXX = \"{$zRUsr['USRID2XX']}\" AND ";
			  							    $zSqlUsr2 .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
			  							    $zCrsUsr2 = mysql_query($zSqlUsr2,$xConexion01);
			  							    $zRUsr2 = mysql_fetch_array($zCrsUsr2);
			  							    if($zRUsr2['USRIDXXX']==$zRUsr2['USRID2XX']){

			  							      if($zRUsr2['USRIDXXX']!=""){
	  		  							      $zSqlDoi  = "SELECT * ";
	  			  							    $zSqlDoi .= "FROM $cAlfa.GRM00121 ";
	  			  							    $zSqlDoi .= "WHERE docidxxx = \"$gDocNro\" AND ";
	  			  							    $zSqlDoi .= "USRIDXXX = \"{$zRUsr2['USRIDXXX']}\" AND ";
	  		  							      $zSqlDoi .= "REGESTXX = \"ACTIVO\"    LIMIT 0,1";
			  							      }

			  							    }
			  							  }*/

	  									$zCrsDoi = mysql_query($zSqlDoi,$xConexion01);
	  									if (mysql_num_rows($zCrsDoi) > 0) {
												$zMtzDo = mysql_fetch_array($zCrsDoi);
												/**
												 * Como solo me trae un dato, saco el nombre del cliente la sucursal y el Tipo de Do, para se
												 * insertados en los otros campos de texto del formulario que ayudan a tener mayor claridad sobre
												 * la informaci�n del DO.
												 */
												  $zSqlNomCli  = "SELECT * ";
			  								  $zSqlNomCli .= "FROM $cAlfa.SIAI0150 ";
			  								  $zSqlNomCli .= "WHERE CLIIDXXX  = \"{$zMtzDo['cliidxxx']}\" AND ";
			  								  $zSqlNomCli .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
			  								  $zCrsNomCli = mysql_query($zSqlNomCli,$xConexion01);
			  								  $zNomCli = mysql_fetch_array($zCrsNomCli); ?>

	  										  <script languaje = "javascript">
	  											  parent.fmwork.document.forms['frgrm']['cSucId'].value = "<?php echo $zMtzDo['sucidxxx']?>";
	  											  parent.fmwork.document.forms['frgrm']['cDocId'].value = "<?php echo $zMtzDo['docidxxx']?>";
	  											  parent.fmwork.document.forms['frgrm']['cDocSuf'].value= "<?php echo $zMtzDo['docsufxx']?>";
	  											  parent.fmwork.document.forms['frgrm']['cDocTip'].value= "<?php echo $zMtzDo['doctipxx']?>";
	  											  parent.fmwork.document.forms['frgrm']['cCliId'].value = "<?php echo $zNomCli['CLIIDXXX']?>";
	  											  parent.fmwork.document.forms['frgrm']['cCliNom'].value= "<?php echo $zNomCli['CLINOMXX']?>";
	  										  </script>
	  							<?php } else { ?>
	  										 <script languaje = "javascript">
	     	    							 parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
													 window.close();
												 </script>
	  							<?php }
	  								break;
	  								case "WINDOW":
		  									/**
		  									 * Consulto en la tabla 121 los DO's, validos que en su campo DOCFORMS sean diferentes a NO.
		  									 */
	  										$zSqlUsr  = "SELECT * ";
				  							$zSqlUsr .= "FROM $cAlfa.SIAI0003 ";
				  							$zSqlUsr .= "WHERE USRIDXXX = \"{$_COOKIE['kUsrId']}\" AND ";
			  							  $zSqlUsr .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
			  							  $zCrsUsr = mysql_query($zSqlUsr,$xConexion01);
			  							  $zRUsr = mysql_fetch_array($zCrsUsr);
			  							 // if($zRUsr['USRIDXXX']==$zRUsr['USRID2XX']){

		  									  $zSqlDoi  = "SELECT * ";
				  							  $zSqlDoi .= "FROM $cAlfa.sys00121 ";
				  							  $zSqlDoi .= "WHERE docidxxx LIKE \"%$gDocNro%\" AND ";
				  							  $zSqlDoi .= "regusrxx = \"{$_COOKIE['kUsrId']}\" AND ";
			  							    //$zSqlDoi .= "sucidxxx = \"{$zRUsr['sucidxxx']}\" AND ";
			  							    $zSqlDoi .= "regestxx = \"ACTIVO\"  ";
				  							  $zCrsDoi = mysql_query($zSqlDoi,$xConexion01);
			  							 /* }else{

			  							    $zSqlUsr2  = "SELECT * ";
				  							  $zSqlUsr2 .= "FROM $cAlfa.SYS00001 ";
				  							  $zSqlUsr2 .= "WHERE USRIDXXX = \"{$zRUsr['USRID2XX']}\" AND ";
			  							    $zSqlUsr2 .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
			  							    $zCrsUsr2 = mysql_query($zSqlUsr2,$xConexion01);
			  							    $zRUsr2 = mysql_fetch_array($zCrsUsr2);
			  							    if($zRUsr2['USRIDXXX']==$zRUsr2['USRID2XX']){

			  							       if($zRUsr2['USRIDXXX']!=""){
	  		  							       $zSqlDoi  = "SELECT * ";
	  			  							     $zSqlDoi .= "FROM $cAlfa.GRM00121 ";
	  			  							     $zSqlDoi .= "WHERE docidxxx LIKE \"%$gDocNro%\" AND ";
	  			  							     $zSqlDoi .= "USRIDXXX = \"{$zRUsr2['USRIDXXX']}\" AND ";
	  		  							       $zSqlDoi .= "REGESTXX = \"ACTIVO\"  ";
	  			  							     $zCrsDoi = mysql_query($zSqlDoi,$xConexion01);
			  							       }
			  							    }
			  							  }
				  							*/
	  									if (mysql_num_rows($zCrsDoi) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "300">
														<tr>
															<td widht = "050" Class = "name"><center>Suc.</center></td>
															<td widht = "050" Class = "name"><center>Tipo</center></td>
															<td widht = "200" Class = "name"><center>Documento</center></td>
														</tr>
														<?php while ($zRDoc = mysql_fetch_array($zCrsDoi)) {

															$zSqlNomCli  = "SELECT * ";
		  												$zSqlNomCli .= "FROM $cAlfa.SIAI0150 ";
		  												$zSqlNomCli .= "WHERE CLIIDXXX  = \"{$zRDoc['cliidxxx']}\" AND ";
		  												$zSqlNomCli .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
		  												$zCrsNomCli = mysql_query($zSqlNomCli,$xConexion01);
		  												$zNomCli = mysql_fetch_array($zCrsNomCli);
														?>
															<tr>
																<td width = "050" class= "name"><?php echo $zRDoc['sucidxxx'] ?></td>
																<td width = "020" class= "name"><?php echo $zRDoc['doctipxx'] ?></td>
																<td width = "020" class= "name">
																	<a href = "javascript:window.opener.document.forms['frgrm']['cSucId'].value ='<?php echo $zRDoc['sucidxxx'] ?>';
														                   window.opener.document.forms['frgrm']['cDocId'].value='<?php echo $zRDoc['docidxxx'] ?>';
														                   window.opener.document.forms['frgrm']['cDocSuf'].value='<?php echo $zRDoc['docsufxx'] ?>';
	  																		       window.opener.document.forms['frgrm']['cDocTip'].value='<?php echo $zRDoc['doctipxx'] ?>';
	  																		       window.opener.document.forms['frgrm']['cCliId'].value='<?php echo $zNomCli['CLIIDXXX'] ?>';
	  																		       window.opener.document.forms['frgrm']['cCliNom'].value='<?php echo $zNomCli['CLINOMXX'] ?>';
	  																		       window.close()"><?php echo $zRDoc['docidxxx'] ?></a></td>
																</td>
															</tr>
														<?php } ?>
													</table>
												</center>
	  									<?php	} else {
	  										wMenssage(__FILE__,__LINE__,"No se Encontraron Registros");
	  									}
	  								break;
	  							}
	  						?>
	  					</form>
	  				</fieldset>
	  			</td>
	  		</tr>
	  	</table>
	  </center>
	</body>
</html>
<?php
} else {
	wMenssage(__FILE__,__LINE__,"No se Recibieron Parametros Completos");
} ?>