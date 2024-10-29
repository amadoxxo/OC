<?php
  namespace openComex;
/**
	 * Listado de Ica x Sucursales
	 * @author Hernan Gordillo <opencomex@opencomex.com>
	 * @package openComex
	 */
	include("../../../../libs/php/utility.php");
	//include ("../utility.php");
	//include ("../class.mysql.php");
  /**
	   *  Cookie fija
	   */
	  /*$kDf = explode("~",$_COOKIE["kDatosFijos"]);
	  $kMysqlHost = $kDf[0];
	  $kMysqlUser = $kDf[1];
	  $kMysqlPass = $kDf[2];
	  $kMysqlDb   = $kDf[3];
	  $kUser      = $kDf[4];
	  $kLicencia  = $kDf[5];
	  $swidth     = $kDf[6];
	  $mysql = new c_Mysql();
	  $mysql->cServer = $kMysqlHost;
    $mysql->cUser =   $kMysqlUser;
    $mysql->cPass =   $kMysqlPass;
    $mysql->cDatab =  $kMysqlDb;
    $mysql->f_Conectar();
    $mysql->f_SelecDb();*/

  $vIcaS = trim(strtoupper($vIcaS));
  //$Ica = split('|',$vIcaS);
  $Ica = explode('|',$vIcaS);
  //f_Mensaje(__FILE__,__LINE__,$vIcaS);
   //f_Mensaje(__FILE__,__LINE__,$Ica);
  ?>
<html>
  <title>Ica x Sucursales </title>
	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		</script>
		<script language="javascript">
  		function f_Navega(cPai,cDep,cCiu,acc){
  		  var lcla = window.opener.document.forms['frgrm'].cCliIcas.value.length;
        if (acc == 1) {
          if (lcla > 0) {
            window.opener.document.forms['frgrm'].cCliIcas.value = window.opener.document.forms['frgrm'].cCliIcas.value+'|'+cPai+'~'+cDep+'~'+cCiu;
          } else {
  					window.opener.document.forms['frgrm'].cCliIcas.value = cPai+'~'+cDep+'~'+cCiu;
          }
        } else {
          if (lcla > 0) {
            window.opener.document.forms['frgrm'].cCliIcas.value = window.opener.document.forms['frgrm'].cCliIcas.value.replace('|'+cPai+'~'+cDep+'~'+cCiu,'');
            window.opener.document.forms['frgrm'].cCliIcas.value = window.opener.document.forms['frgrm'].cCliIcas.value.replace(cPai+'~'+cSuc+'~'+cCco,'');
          }
        }
				window.close();
			}

			function f_Guardar(nfin){
  		  var ccadena = '';
  		  for (i=0;i<nfin;i++) {
  		    if (document.frgrm["ch"+i].checked == true) {
  		      if (ccadena.length == 0) {
  		        ccadena += '|'+document.frgrm["ch"+i].id+'|';
  		      } else {
  		        ccadena += document.frgrm["ch"+i].id+'|';
  		      }
  		    }
  		  }
  		  window.opener.document.forms['frgrm'].cCliIcas.value = ccadena;
				window.close();
			}

  	</script>
  </head>

	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
	<form name = 'frgrm'>
	 <center>
  		<table border ="0" cellpadding="0" cellspacing="0" width="95%">
				<tr>
					<td>
				  	<fieldset>
					  	<legend>Listado Ica x Sucursales</legend>
					  	  <font color="#FF0000">** Registros Asignados</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					  	  <font color="#0000FF">** Registros candidatos a asignar</font>
						 	  <center>
									<table border = "1" cellpadding = "0" cellspacing = "0" width="100%">
									<tr bgcolor = '#D6DFF7'>
											<td Class = "name" width = "10%"><center>Id</center></td>
											<td Class = "name" width = "25%"><center>Ciudad</center></td>
											<td Class = "name" width = "11%"><center>Sucursal</center></td>
											<td Class = "name" width = "11%"><center>Centro Costo</center></td>
											<td Class = "name" width = "16%"><center>Tarifa ICA</center></td>
											<td Class = "name" width = "20%"><center>Cuenta PUC</center></td>
											<td Class = "name" width = "7%"><center></center></td>
										</tr>
										<?php

										    $qDatIca  = "SELECT * ";
										    $qDatIca .= "FROM $cAlfa.SIAI0055 ";
										    $qDatIca .= "WHERE PAIIDXXX = \"CO\" ";
										    $qDatIca .= "ORDER BY CIUIDXXX ";
                        $xDatIca  = f_MySql("SELECT","",$qDatIca,$xConexion01,"");

                        $vIndi = 0;
                        $y = 0;
										    while ($xDI = mysql_fetch_array($xDatIca)){
										    $y++;
										      $PaiId  = trim($xDI['PAIIDXXX']);
										      $DepId  = trim($xDI['DEPIDXXX']);
										      $CiuId  = trim($xDI['CIUIDXXX']);
 					                $CiuDes = trim ($xDI['CIUDESXX']);
 					                $zColor = "{$vSysStr['system_row_impar_color_ini']}";
 					                if($y % 2 == 0) {
    												 $zColor = "{$vSysStr['system_row_par_color_ini']}";
    											}

 					                if (in_array($PaiId."~".$DepId."~".$CiuId,$Ica,true)) {
 					                  ?>
 					                  <tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
                              <td Class = "letra8" width = "10%"><font color="#FF0000"><?php echo $CiuId ?></font></td>
                              <td Class = "letra8" width = "25%"><?php echo $CiuDes ?></td>
                              <td Class = "letra8" width = "11%"><?php echo $xDI['SUCIDXXX'] ?></td>
                              <td Class = "letra8" width = "11%"><?php echo $xDI['CCOIDXXX'] ?></td>
                              <td Class = "letra8" width = "16%" style="text-align:right"><?php echo $xDI['CIUICAXX'] ?></td>
    										      <td Class = "letra8" width = "20%"><?php echo $xDI['PUCIDXXX'] ?></td>
    										      <td Class = "letra8" width = "7%"><input type = 'checkbox' name = 'ch<?php echo $vIndi ?>' id="<?php echo $PaiId."~".$DepId."~".$CiuId ?>" checked></td>
    										    </tr>
  										      <?php
                					} else {
                						$onch = "javascript:f_Navega('$PaiId',$DepId','$CiuId',1)";
                						$zColor = "{$vSysStr['system_row_impar_color_ini']}";
   					                if($y % 2 == 0) {
      												 $zColor = "{$vSysStr['system_row_par_color_ini']}";
      											}

                						?>
                						<tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
      										   <td Class = "letra8"  width = "10%"><font color="#0000FF"><?php echo $CiuId ?></font></td>
                              <td Class = "letra8" width = "25%"><?php echo $CiuDes ?></td>
                              <td Class = "letra8" width = "11%"><?php echo $xDI['SUCIDXXX'] ?></td>
                              <td Class = "letra8" width = "11%"><?php echo $xDI['CCOIDXXX'] ?></td>
                              <td Class = "letra8" width = "16%" style="text-align:right"><?php echo $xDI['CIUICAXX'] ?></td>
    										      <td Class = "letra8" width = "20%"><?php echo $xDI['PUCIDXXX'] ?></td>
    										      <td Class = "letra8" width = "7%"><input type = 'checkbox' name = 'ch<?php echo $vIndi ?>' id="<?php echo $PaiId."~".$DepId."~".$CiuId ?>"></td>
      											</tr>
                					  <?php
                					}
                					$vIndi++;
                			     if($_COOKIE['kModo'] == "VER"){
                			     ?>
                			     <script language="javascript">
                			       document.getElementById('<?php echo $PaiId."~".$DepId."~".$CiuId ?>').disabled  = true;
                			     </script>
                			     <?php
                			     }
										    }
										     ?>
										</table>
								</center>
			 	    </fieldset>
					</td>
				</tr>
		 	</table>
		  </center>
		</form>
		<center>
			<table border="0" cellpadding="0" cellspacing="0" width="92%">
				<tr height="21">
				<?php
				  if($_COOKIE['kModo'] != "VER"){
				?>
				 	<td width="218" height="21"></td>
					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer"
						onClick = "javascript:f_Guardar(<?php echo $vIndi ?>)">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar
					</td>
					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer"
						onClick = "javascript:window.close()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
					</td>
					<?php }else{
					?>
					<td width="309" height="21"></td>
					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer"
						onClick = "javascript:window.close()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
					</td>
					<?php

					}
					?>

				</tr>
			</table>
		</center>
	</body>
</html>