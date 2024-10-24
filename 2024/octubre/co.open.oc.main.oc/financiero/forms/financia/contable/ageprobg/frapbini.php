<?php
  namespace openComex;
/**
 * Ini de Integracion BOSCH
 * @author Ricardo Alonso Rincón Vega <ricardo.rincon@opentecnologia.com.co>
 * @package openComex
 * @todo NA
 *
 * Variables:
 * @var array   $vArchivo     Datos del OC_ROBOT1
 * @var mixed   $cServer      Servidor de la Conexion OC_ROBOT1
 * @var mixed   $cUser        Usuario de la Conexion OC_ROBOT1
 * @var mixed   $cPass        Password de la Conexion OC_ROBOT1
 * @var mixed   $cBase        Base de Datos de la Conexion OC_ROBOT1
 * @var mixed   $cTabla       Tabla de la Conexion OC_ROBOT1
 * @var array   $kDf          Cookies Conexion OpenComex
 * @var mixed   $kMysqlHost   Servidor de la Conexion OpenComex
 * @var mixed   $kMysqlUser   Usuario de la Conexion OpenComex
 * @var mixed   $kMysqlPass   Password de la Conexion OpenComex
 * @var mixed   $kMysqlDb     Base de Datos de la Conexion OpenComex
 * @var mixed   $kUser        Usuario de la Conexion OpenComex
 * @var mixed   $kLicencia    Licencia de la Conexion OpenComex
 * @var mixed   $swidth       xxx
 * @var string  $qSqlMen      Consulta Permiso Usuarios sys00006
 * @var mixed   $xSqlMen      Cursor Resultado de la Consulta $qSqlMen
 * @var string  $qSqlPer      Consulta Botones de Acceso sys00006
 * @var mixed   $xSqlPer      Cursor Resultado de la Consulta $qSqlPer
 * @var array   $vRow         Vector Permiso Usuarios
 * @var string  $cCeldas      xxx
 * @var int     $nDiferencia  xxx
 * @var int     $vLimInf      Limite Inferior de la Lista
 * @var int     $vLimSup      Limite Superior de la Lista
 * @var int     $vPaginas     Numero de Paginas
 * @var string  $qSqlRec100   Consulta en la rec00100
 * @var mixed   $xSqlRec100   Cursor Resultado de la Consulta $qSqlRec100
 * @var array   $vRSqlRec100  Vector de los Datos de Reconocimiento
 * @var array   $mMatrizTmp   xxx
 * @var array   $mMatrizTra   xxx
 * @var array   $vRPer        Vector de Botones de Acceso
 * @var int     $nColor       xxx
 */


# Librerias
include("../../../../libs/php/utility.php");
include("../../../../config/config.php");

# Extraigo Datos del OC_ROBOT1
$vArchivo = explode("~",OC_ROBOT1);
$cServer = $vArchivo[1];
$cUser   = $vArchivo[4];
$cPass   = $vArchivo[5];
$cBase   = $vArchivo[2];
$cTabla  = $vArchivo[3];
# Cookie Fija
$kDf = explode("~",$_COOKIE["kDatosFijos"]);
$kMysqlHost = $kDf[0];
$kMysqlUser = $kDf[1];
$kMysqlPass = $kDf[2];
$kMysqlDb   = $kDf[3];
$kUser      = $kDf[4];
$kLicencia  = $kDf[5];
$swidth     = $kDf[6];

# Busco en la sys00005 si el Usuario tiene Permiso
$qSqlMen  = "SELECT * ";
$qSqlMen .= "FROM $cAlfa.sys00005 ";
$qSqlMen .= "WHERE ";
$qSqlMen .= "modidxxx = \"{$_COOKIE["kModId"]}\" AND ";
$qSqlMen .= "proidxxx = \"{$_COOKIE["kProId"]}\" AND ";
$qSqlMen .= "menimgon <> '' ";
$qSqlMen .= "ORDER BY menordxx";
$xSqlMen  = f_MySql("SELECT","",$qSqlMen,$xConexion01,"");
?>
<html>
	<head>
  	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/estilo.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/general.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/layout.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/custom.css'>
   	<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/date_picker.js'></script>
   	<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/utility.js'></script>
   	<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/ajax.js'></script>
   	<!-- Funciones JavaScript -->
   	<script language="javascript">
		/**
     * JavaScript:fnVerProceso
     * @param mixed   xIpProc
     */
		function fnVerProceso(xIdProc){
      w=650;
      h=500;
      LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
      TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
      settings ='height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars=YES,resizable'
      var ruta = 'frapbmep.php?gIdProc='+xIdProc;
      zWin = window.open(ruta,'zTraId',settings);
      zWin.focus();
    }
    /**
     * JavaScript:fnCambEst
     * @param mixed   xModo
     */
		function fnCambEst(xModo) {
			switch (document.forms['frgrm']['vRecords'].value) {
				case "1":
					if (document.forms['frgrm']['oCheck'].checked == true) {
						var zMatriz = document.forms['frgrm']['oCheck'].id.split('~');
						if(zMatriz[1] == "ACTIVO" || zMatriz[1] == "INACTIVO"){
						  if (zMatriz[1] == "ACTIVO") {
                var cTitulo = "Inactivar";
                var cEstado = "INACTIVO";
              } else {
                var cTitulo = "Activar";
                var cEstado = "ACTIVO";
              }
              var xMensaje  = "Esta Seguro de ["+cTitulo+"] El Id del Proceso ["+zMatriz[0]+"]?";
              if (confirm(xMensaje)) {
                f_CreaCookie('kModo',xModo);
                document.forms['franula']['cRegEst'].value   = cEstado;
                document.forms['franula']['cPbaId'].value    = zMatriz[0];
                document.forms['franula'].submit();
              }
						}else{
						  alert("Para cambiar el Estado del Registro debe estar en Estado [ACTIVO] o [INACTIVO].\nVerifique");
						}
					}
				break;
				default:
					var zSw_Prv = 0;
					for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
						if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
							// Solo Permite INACTIVAR el Primero Seleccionado
							zSw_Prv = 1;
							var zMatriz = document.forms['frgrm']['oCheck'][i].id.split('~');
							if(zMatriz[1] == "ACTIVO" || zMatriz[1] == "INACTIVO"){
							  if (zMatriz[1] == "ACTIVO") {
                  var cTitulo = "Inactivar";
                  var cEstado = "INACTIVO";
                } else {
                  var cTitulo = "Activar";
                  var cEstado = "ACTIVO";
                }
                var xMensaje  = "Esta Seguro de ["+cTitulo+"] El Id del Proceso ["+zMatriz[0]+"]?";
                if (confirm(xMensaje)) {
                  f_CreaCookie('kModo',xModo);
                  document.forms['franula']['cRegEst'].value   = cEstado;
                  document.forms['franula']['cPbaId'].value    = zMatriz[0];
                  document.forms['franula'].submit();
                }
							}else{
							  alert("Para cambiar el Estado del Registro debe estar en Estado [ACTIVO] o [INACTIVO].\nVerifique");
							}
						}
					}
				break;
			}
    }
    /**
     * JavaScript:fnNavega
     * @param mixed   xModulo
     */
    function fnNavega(xModulo){
     	parent.fmnav.location='../../nivel2.php';
      document.location = '../../central1.php';
   	}
    /**
     * JavaScript:fnLink
     * @param mixed   xModId
     * @param mixed   xProId
     * @param mixed   xMenId
     * @param mixed   xForm
     * @param mixed   xOpcion
     * @param mixed   xMenDes
     */
    function fnLink(xModId,xProId,xMenId,xForm,xOpcion,xMenDes){
    	document.cookie="kIniAnt=reconocimiento/tarearec/<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
      document.cookie="kMenDes="+xMenDes+";path="+"/";
      document.cookie="kModo="+xOpcion+";path="+"/";
      parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_New ?>/nivel4.php";
      document.location = xForm; // Invoco el menu.
    }
    /**
     * JavaScript:fnMarca
     */
    function fnMarca() {
    	if (document.forms['frgrm']['oCheckAll'].checked == true){
     	  if (document.frgrm.vRecords.value == 1){
     	  	document.forms['frgrm']['oCheck'].checked=true;
     	  } else {
	     		if (document.frgrm.vRecords.value > 1){
		      	for (i=0;i<document.forms['frgrm']['oCheck'].length;i++){
   	 	      	document.forms['frgrm']['oCheck'][i].checked = true;
		      	}
		      }
     	  }
     	} else {
	     	if (document.frgrm.vRecords.value == 1){
     	  	document.forms['frgrm']['oCheck'].checked=false;
     	  } else {
     	  	if (document.frgrm.vRecords.value > 1){
			      for (i=0;i<document.forms['frgrm']['oCheck'].length;i++){
			      	document.forms['frgrm']['oCheck'][i].checked = false;
			      }
     	  	}
 	     	}
	    }
	 	}
    /**
     * JavaScript:fnButtonsAscDes
     * @param mixed   xEvent
     * @param mixed   xSortField
     * @param mixed   xSortType
     * @var   string  zSortType
     */
	  function fnButtonsAscDes(xEvent,xSortField,xSortType) {
	   	var zSortType = "";
	   	switch (document.forms['frgrm']['vSortType'].value) {
	   		case "ASC_NUM":
	   			zSortType = "DESC_NUM";
	   		break;
	   		case "DESC_NUM":
	   			zSortType = "ASC_NUM";
	   		break;
	   		case "ASC_AZ":
	   			zSortType = "DESC_AZ";
	   		break;
	   		case "DESC_AZ":
	   			zSortType = "ASC_AZ";
	  		break;
	   	}
	   	switch (xEvent) {
	   		case "onclick":
					if (document.getElementById(xSortField).id != document.forms['frgrm']['vSortField'].value) {
						document.forms['frgrm']['vSortField'].value=xSortField;
						document.forms['frgrm']['vSortType'].value=xSortType;
						document.forms['frgrm'].submit();
					} else {
						document.forms['frgrm'].submit();
					}
	   		break;
	  		case "onmouseover":
					if(document.forms['frgrm']['vSortField'].value == xSortField) {
						if (document.forms['frgrm']['vSortType'].value == 'ASC_NUM' || document.forms['frgrm']['vSortType'].value == 'ASC_AZ') {
							document.getElementById(xSortField).src = '<?php echo $cPlesk_Skin_Directory_New ?>/s_desc.png';
							document.forms['frgrm']['vSortField'].value = xSortField;
							document.forms['frgrm']['vSortType'].value = zSortType;
						} else {
							document.getElementById(xSortField).src = '<?php echo $cPlesk_Skin_Directory_New ?>/s_asc.png';
							document.forms['frgrm']['vSortField'].value = xSortField;
							document.forms['frgrm']['vSortType'].value = zSortType;
						}
					} else {
						document.getElementById(xSortField).src = '<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png';
						}
	  		break;
	   		case "onmouseout":
					if(document.forms['frgrm']['vSortField'].value == xSortField) {
						if (document.forms['frgrm']['vSortType'].value == 'ASC_NUM' || document.forms['frgrm']['vSortType'].value == 'ASC_AZ') {
							document.getElementById(xSortField).src='<?php echo $cPlesk_Skin_Directory_New ?>/s_desc.png';
							document.forms['frgrm']['vSortField'].value = xSortField;
							document.forms['frgrm']['vSortType'].value = zSortType;
						} else {
							document.getElementById(xSortField).src='<?php echo $cPlesk_Skin_Directory_New ?>/s_asc.png';
							document.forms['frgrm']['vSortField'].value = xSortField;
							document.forms['frgrm']['vSortType'].value = zSortType;
						}
					} else {
						document.getElementById(xSortField).src='<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png';
						}
	  		break;
	    	}
    	  if(document.forms['frgrm']['vSortField'].value == xSortField) {
      		if (document.forms['frgrm']['vSortType'].value == 'ASC_NUM' || document.forms['frgrm']['vSortType'].value == 'ASC_AZ') {
      	  	document.getElementById(xSortField).src = '<?php echo $cPlesk_Skin_Directory_New ?>/s_asc.png';
      	    document.getElementById(xSortField).title = 'Ascendente';
      	  } else {
      	    document.getElementById(xSortField).src = '<?php echo $cPlesk_Skin_Directory_New ?>/s_desc.png';
      	    document.getElementById(xSortField).title = 'Descendente';
      	  }
      	}
    	}
  	</script>
  </head>
  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
		<form name = "franula" action = "frapbgra.php" method = "post" target="fmpro">
			<input type = "hidden" name = "cRegEst"   value = "">
			<input type = "hidden" name = "cPbaId"    value = "">
		</form>
		<form name = "frgrm" action="frapbini.php" method="post" target="fmwork">
   		<input type = "hidden" name = "vRecords"   value = "">
   		<input type = "hidden" name = "vLimInf"    value = "<?php echo $_POST['vLimInf'] ?>">
   		<input type = "hidden" name = "vSortField" value = "<?php echo $_POST['vSortField'] ?>">
   		<input type = "hidden" name = "vSortType"  value = "<?php echo $_POST['vSortType'] ?>">
   		<input type = "hidden" name = "vTimes"     value = "<?php echo $_POST['vTimes'] ?>">
   		<input type = "hidden" name = "vTimesSave" value = "0">
      <input type = "hidden" name = "vBuscar"    value = "<?php echo $_POST['vBuscar'] ?>">
      <input type = "hidden" name = "cOrderByOrder"  value = "<?php echo $_POST['cOrderByOrder'] ?>" style = "width:1000">
   		<!-- Inicia Nivel de Procesos -->
   		<?php if (mysql_num_rows($xSqlMen) > 0) { ?>
   		  <center>
 	 				<table width="95%" cellspacing="0" cellpadding="0" border="0">
	  				<tr>
  						<td>
				    		<fieldset>
  	  		    		<legend>Procesos <?php echo $_COOKIE['kProDes'] ?></legend>
 	 			  	  		<center>
	       	  				<table cellspacing="0" width="100%">
	        	  	  		<?php
     			    		   		$y = 0;
     			    		   		/* Empiezo a Leer */
												while($vRow = mysql_fetch_array($xSqlMen)) {
													if($y == 0 || $y % 5 == 0) {
				  	      					if ($y == 0) {?>
											  	  <tr>
													  <?php } else { ?>
												    </tr><tr>
												    <?php }
												  }
												  /* Consulto en las sys00006 */
												  $qSqlPer  = "SELECT * ";
												  $qSqlPer .= "FROM $cAlfa.sys00006 ";
												  $qSqlPer .= "WHERE ";
												  $qSqlPer .= "usridxxx = \"{$kUser}\" AND ";
												  $qSqlPer .= "modidxxx = \"{$vRow['modidxxx']}\"  AND ";
												  $qSqlPer .= "proidxxx = \"{$vRow['proidxxx']}\"  AND ";
												  $qSqlPer .= "menidxxx = \"{$vRow['menidxxx']}\"  LIMIT 0,1";
												  $xSqlPer  = f_MySql("SELECT","",$qSqlPer,$xConexion01,"");
												  if (mysql_num_rows($xSqlPer) > 0) { ?>
													  <td Class="clase08" width="20%">
													    <center>
													       <img src = "<?php echo $cPlesk_Skin_Directory_New ?>/<?php echo $vRow['menimgon'] ?>" style = "cursor:hand"
												           onClick ="javascript:fnLink('<?php echo $vRow['modidxxx'] ?>','<?php echo $vRow['proidxxx'] ?>','<?php echo $vRow['menidxxx'] ?>','<?php echo $vRow['menformx']?>','<?php echo $vRow['menopcxx']?>','<?php echo $vRow['mendesxx']?>')"><br>
				                         <a href = "javascript:fnLink('<?php echo $vRow['modidxxx'] ?>','<?php echo $vRow['proidxxx'] ?>','<?php echo $vRow['menidxxx'] ?>','<?php echo $vRow['menformx']?>','<?php echo $vRow['menopcxx']?>','<?php echo $vRow['mendesxx']?>')"
															     style="color:#000000"><?php echo $vRow['mendesxx'] ?>
				                        </a>
				                      </center>
				                    </td>
													<?php	} else { ?>
														<td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory_New ?>/<?php echo $vRow['menimgof']?>"><br>
   			    		          	<?php echo $vRow['mendesxx'] ?></center></td>
													<?php }
													$y++;
												}
												$cCeldas = "";
					        	  	$nDiferencia = 5-($y-(intval($y/5)));
					          	  if ($nDiferencia > 0) {
		    			        		for ($i=0;$i<$nDiferencia;$i++) {
		        			      		$cCeldas.="<td width='20%'></td>";
				      	      		}
						    	        echo $cCeldas;
					  	    	    } ?>
   		      		  			</tr>
     		        		</table>
      		      	</center>
 		    		  	</fieldset>
         	  	</td>
          	</tr>
      		</table>
 	      </center>
 	    <?php } ?>
 	    <!-- Fin Nivel de Procesos -->
      <?php
      if(empty($vLimInf) && empty($vLimSup)){
				$vLimInf = "00";
        $vLimSup = "30";
			}

			if(empty($vPaginas)){
      	$vPaginas = "1";
			}

      if($_POST['cPeriodos'] == ""){
        $_POST['cPeriodos'] = "20";
        $_POST['dDesde'] = substr(date('Y-m-d'),0,8)."01";
        $_POST['dHasta'] = date('Y-m-d');
      }

      if($_POST['cRegEst'] == ""){
        $_POST['cRegEst'] = "ALL";
      }

      if ($_POST['cRegUsr'] == "") {
        $_POST['cRegUsr'] = $kUser;
      }

      $y=0;
      $qSysProbg  = "SELECT * ";
      $qSysProbg .= "FROM $cBeta.sysprobg ";
      $qSysProbg .= "WHERE ";
      if($_POST['cPeriodos'] != ""){
        $qSysProbg .= "DATE(regdcrex) BETWEEN \"{$_POST['dDesde']}\" AND \"{$_POST['dHasta']}\" AND ";
      }
      if($_POST['cPbaTin'] != ""){
        $qSysProbg .= "pbatinxx = \"{$_POST['cPbaTin']}\" AND ";
      }
      if($_POST['cRegEst'] != "" && $_POST['cRegEst'] != "ALL"){
        $qSysProbg .= "regestxx = \"{$_POST['cRegEst']}\" AND ";
      }
      if($_POST['cRegUsr'] != "" && $_POST['cRegUsr'] != "ALL"){
        $qSysProbg .= "regusrxx = \"{$_POST['cRegUsr']}\" AND ";
      }
      $qSysProbg .= "pbadbxxx = \"$cAlfa\" AND ";
			$qSysProbg .= "pbamodxx = \"FACTURACION\" ";
      $qSysProbg .= "ORDER BY regdcrex DESC";
      $xSysProbg  = f_MySql("SELECT","",$qSysProbg,$xConexion01,"");

			# Cargo la Matriz con los ROWS del Cursor
			$i=0;
			while ($xRB = mysql_fetch_array($xSysProbg)){
			  $mMatrizTmp[$i] = $xRB;
        $mMatrizTmp[$i]['tramitex'] = ($xRB['admidxxx'] != "" || $xRB['doiidxxx'] != "" || $xRB['doisfidx'] != "") ? trim($xRB['admidxxx']."-".$xRB['doiidxxx']."-".$xRB['doisfidx'],"-") : "";
        $mMatrizTmp[$i]['tiemesmi'] = ($xRB['pbatxixx'] * $xRB['pbacrexx'] <= 60) ? ($xRB['pbatxixx'] * $xRB['pbacrexx'])." SEG" : round(($xRB['pbatxixx'] * $xRB['pbacrexx']) / 60)." MIN";

        if($xRB['regestxx'] != "INACTIVO"){
          $nTieEst = round(((strtotime(date('Y-m-d H:i:s')) - strtotime($xRB['regdinix'])) / ($xRB['pbatxixx'] * $xRB['pbacrexx'])),2)."&#37";
        }else{
          $nTieEst = "";
        }
        $mMatrizTmp[$i]['progreso'] = ($xRB['regdinix'] == "" || $xRB['regdinix'] == "0000-00-00 00:00:00") ? "" : (($xRB['regdfinx'] != "0000-00-00 00:00:00") ? "100" : $nTieEst);
				$i++;
			}

      # Fin de Cargo la Matriz con los ROWS del Cursor
      # Recorro la Matriz para Traer Datos Externos

      # Si el $vSearch Viene Cargado Busco el Patron en Todos los Campos de la Matriz
			if (!empty($_POST['vSearch'])) {
				$mMatrizTra = array();
				for ($i=0,$j=0;$i<count($mMatrizTmp);$i++) {
					$zArray = array_values($mMatrizTmp[$i]);
					for ($k=0;$k<count($zArray);$k++) {
						if (substr_count($zArray[$k],strtoupper($vSearch)) > 0) {
							$k = count($zArray)+1;
							$mMatrizTra[$j] = $mMatrizTmp[$i];
							$j++;
						}
					}
				}
			} else {
				$mMatrizTra = $mMatrizTmp;
			}
			# Fin de Buscar Patron en la Matriz

			if (!empty($_POST['vSortField']) && !empty($_POST['vSortType'])) {
			  $mMatrizTra = wSortArrayByField($mMatrizTra,$vSortField,$vSortType);
			}
      # Fin de Recorro la Matriz para Traer Datos Externos

			?>
      <center>
       	<table width="95%" cellspacing="0" cellpadding="0" border="0">
         	<tr>
	       	  <td>
				      <fieldset>
   			        <legend>Registros en la Consulta (<?php echo count($mMatrizTra)?>)</legend>
     	         	<center>
       	       		<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      		<tr>
           	        	<td class="clase08" width="12%">
            	        	<input type="text" class="letra" name = "vSearch" maxlength="20" value = "<?php echo $vSearch ?>" style= "width:80"
            	        		onblur="javascript:this.value=this.value.toUpperCase();
																						 document.forms['frgrm']['vLimInf'].value='00';
								      											 document.forms['frgrm']['vLimSup'].value='30';
								      											 document.forms['frgrm']['vPaginas'].value='1';
								      											 document.forms['frgrm'].submit()">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_search.png" style = "cursor:hand" title="Buscar"
								      		onClick = "javascript:document.forms['frgrm']['vSearch'].value=document.forms['frgrm']['vSearch'].value.toUpperCase();
								      											 		document.forms['frgrm']['vLimInf'].value='00';
								      												  document.forms['frgrm']['vLimSup'].value='30';
								      												  document.forms['frgrm']['vPaginas'].value='1';
								      												  document.forms['frgrm'].submit()">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/btn_show-all_bg.gif" style = "cursor:hand" title="Mostrar Todo"
								      		onClick ="javascript:document.forms['frgrm']['vSearch'].value='';
								      												 document.forms['frgrm']['vLimInf'].value='00';
								      												 document.forms['frgrm']['vLimSup'].value='30';
								      												 document.forms['frgrm']['vPaginas'].value='1';
								      												 document.forms['frgrm']['vSortField'].value='';
								      												 document.forms['frgrm']['vSortType'].value='';
								      												 document.forms['frgrm']['vTimes'].value='';
								      												 document.forms['frgrm']['cPeriodos'].value='20';
								      												 document.forms['frgrm']['cRegUsr'].value='<?php echo $kUser ?>';
								      												 document.forms['frgrm']['cPbaTin'].value='';
								      												 document.forms['frgrm']['cRegEst'].value='ALL';
								      												 document.forms['frgrm'].submit()">
   	              	  </td>
       	       				<td class="name" width="07%" align="left">Filas&nbsp;
       	       					<input type="text" class="letra" name = "vLimSup" value = "<?php echo $vLimSup ?>" style="width:30;text-align:right"
								      		onfocus = "javascript:document.forms['frgrm']['vPaginas'].value='1'"
       	       						onblur = "javascript:uFixFloat(this);
								      												 document.forms['frgrm']['vLimInf'].value='00';
								      												 document.forms['frgrm'].submit()">
       	       				</td>
       	       				<td class="name" width="07%">
       	       					<?php if (ceil(count($mMatrizTra)/$vLimSup) > 1) { ?>
       	       						<?php if ($vPaginas == "1") { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/bd_firstpage.png" style = "cursor:hand" title="Primera Pagina">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/bd_prevpage.png"  style = "cursor:hand" title="Pagina Anterior">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_nextpage.png"  	style = "cursor:hand" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value++;
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_lastpage.png"  	style = "cursor:hand" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value='<?php echo ceil(count($mMatrizTra)/$vLimSup) ?>';
								      				    						 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
       	       						<?php } ?>
       	       						<?php if ($vPaginas > "1" && $vPaginas < ceil(count($mMatrizTra)/$vLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_firstpage.png" style = "cursor:hand" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value='1';
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_prevpage.png"  style = "cursor:hand" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value--;
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_nextpage.png"  style = "cursor:hand" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value++;
								      												 			 document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_lastpage.png"  style = "cursor:hand" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value='<?php echo ceil(count($mMatrizTra)/$vLimSup) ?>';
								      				    						 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
	       	       					<?php } ?>
       	       						<?php if ($vPaginas == ceil(count($mMatrizTra)/$vLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_firstpage.png" style = "cursor:hand" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value='1';
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_prevpage.png"  style = "cursor:hand" title="Pagina Anterior"
       	       								onClick = "javascript:document.frgrm.vPaginas.value--;
								      												 			 document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
								      												 			document.frgrm.submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/bd_nextpage.png" style = "cursor:hand" title="Pagina Siguiente">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/bd_lastpage.png" style = "cursor:hand" title="Ultima Pagina">
	       	       					<?php } ?>
	       	       				<?php } else { ?>
	       	       					<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/bd_firstpage.png" style = "cursor:hand" title="Primera Pagina">
	       	       					<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/bd_prevpage.png"  style = "cursor:hand" title="Pagina Anterior">
	       	       					<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/bd_nextpage.png"  style = "cursor:hand" title="Pagina Siguiente">
	       	       					<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/bd_lastpage.png"  style = "cursor:hand" title="Ultima Pagina">
	       	       				<?php } ?>
       	       				</td>
       	       				<td class="name" width="07%" align="left">Pag&nbsp;
												<select Class = "letrase" name = "vPaginas" value = "<?php echo $vPaginas ?>" style = "width:60%"
       	       						onchange="javascript:document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(this.value-1));
       	       						                     document.forms['frgrm'].submit()">
													<?php for ($i=0;$i<ceil(count($mMatrizTra)/$vLimSup);$i++) {
														if ($i+1 == $vPaginas) { ?>
															<option value = "<?php echo $i+1 ?>" selected><?php echo $i+1 ?></option>
														<?php } else { ?>
															<option value = "<?php echo $i+1 ?>"><?php echo $i+1 ?></option>
														<?php } ?>
													<?php } ?>
												</select>
       	       				</td>
       	       				<td class="name" width="14%" align="center" >
                        <select class="letrase" size="1" name="cPeriodos" style = "width:100%" value = "<?php echo $_POST['cPeriodos'] ?>"
                          onChange = "javascript:
                                      parent.fmpro.location='<?php echo $cSystem_Libs_Php_Directory ?>/utilfepe.php?gTipo='+this.value+'&gForm='+'frgrm'+'&gFecIni='+'dDesde'+'&gFecFin='+'dHasta';
                                      if (document.forms['frgrm']['cPeriodos'].value == '99') {
                                        document.forms['frgrm']['dDesde'].readOnly = false;
                                        document.forms['frgrm']['dHasta'].readOnly = false;
                                      } else {
                                        document.forms['frgrm']['dDesde'].readOnly = true;
                                        document.forms['frgrm']['dHasta'].readOnly = true;
                                      }">
                           <option value = "10">Hoy</option>
                           <option value = "15">Esta Semana</option>
                           <option value = "20">Este Mes</option>
                           <option value = "25">Este A&ntilde;o</option>
                           <option value = "30">Ayer</option>
                           <option value = "35">Semana Pasada</option>
                           <option value = "40">Semana Pasada Hasta Hoy</option>
                           <option value = "45">Mes Pasado</option>
                           <option value = "50">Mes Pasado Hasta Hoy</option>
                           <option value = "55">Ultimos Tres Meses</option>
                           <option value = "60">Ultimos Seis Meses</option>
                           <option value = "65">Ultimo A&ntilde;o</option>
                           <option value = "99">Periodo Especifico</option>
                        </select>
                        <script language = "javascript">
                          if ("<?php echo $_POST['cPeriodos'] ?>" == "") {
                            document.forms['frgrm']['cPeriodos'].value = "20";
                          } else {
                            document.forms['frgrm']['cPeriodos'].value = "<?php echo $_POST['cPeriodos'] ?>";
                          }
                        </script>
                      </td>
       	       				<td class="name" width="07%" align="center">
                        <input type = "text" Class = "letra" style = "width:90%;text-align:center" name = "dDesde" value = "<?php
                        if($_POST['dDesde']=="" && $_POST['cPeriodos'] == ""){
                          echo substr(date('Y-m-d'),0,8)."01";
                        } else{
                          echo $_POST['dDesde'];
                        } ?>"
                          onblur="javascript:document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));" readonly>
                      </td>
                      <td class="name" width="07%" align="center">
                        <input type = "text" Class = "letra" style = "width:90%;text-align:center" name = "dHasta" value = "<?php
                          if($_POST['dHasta']=="" && $_POST['cPeriodos'] == ""){
                            echo date('Y-m-d');
                          } else{
                            echo $_POST['dHasta'];
                          }  ?>"
                          onblur = "javascript:document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1)); " readonly>
                      </td>
                      <script language = "javascript">
                        if (document.forms['frgrm']['cPeriodos'].value == "99") {
                          document.forms['frgrm']['dDesde'].readOnly = false;
                          document.forms['frgrm']['dHasta'].readOnly = false;
                        } else {
                          document.forms['frgrm']['dDesde'].readOnly = true;
                          document.forms['frgrm']['dHasta'].readOnly = true;
                        }
                      </script>
                      <td class="name" width="12%" align="center">
                        <select Class = "letrase" name = "cRegUsr" style = "width:99%">
                          <option value = "ALL" selected>USUARIOS</option>
                          <?php
                          $qUsrInt  = "SELECT DISTINCT regusrxx,regunomx ";
                          $qUsrInt .= "FROM $cBeta.sysprobg ";
                          $qUsrInt .= "WHERE pbadbxxx = \"$cAlfa\" ";
                          $xUsrInt = f_MySql("SELECT","",$qUsrInt,$xConexion01,"");
                          while ($xRUI = mysql_fetch_array($xUsrInt)) {
                            ?>
                            <option value = "<?php echo $xRUI['regusrxx']?>"><?php echo $xRUI['regunomx'] ?></option>
                           <?php
                          }
                          ?>
                        </select>
                        <script language = "javascript">
                          if ("<?php echo $_POST['cRegUsr'] ?>" == "") {
                            document.forms['frgrm']['cRegUsr'].value = "";
                          } else {
                            document.forms['frgrm']['cRegUsr'].value = "<?php echo $_POST['cRegUsr'] ?>";
                          }
                        </script>
                      </td>
                      <td class="name" width="12%" align="center">
                        <select Class = "letrase" name = "cPbaTin" style = "width:99%">
                          <option value = "" selected>INTERFACES</option>
                          <?php
                          $qTipoInt  = "SELECT DISTINCT pbatinxx,pbatinde ";
                          $qTipoInt .= "FROM $cBeta.sysprobg ";
                          $qTipoInt .= "WHERE pbadbxxx = \"$cAlfa\" AND ";
													$qTipoInt .= "pbamodxx LIKE \"%FACTURACION%\" ";
                          $xTipoInt = f_MySql("SELECT","",$qTipoInt,$xConexion01,"");
                          while ($xRTI = mysql_fetch_array($xTipoInt)) {
                            ?>
                            <option value = "<?php echo $xRTI['pbatinxx']?>"><?php echo $xRTI['pbatinde'] ?></option>
                           <?php
                          }
                          ?>
                        </select>
                        <script language = "javascript">
                          if ("<?php echo $_POST['cPbaTin'] ?>" == "") {
                            document.forms['frgrm']['cPbaTin'].value = "";
                          } else {
                            document.forms['frgrm']['cPbaTin'].value = "<?php echo $_POST['cPbaTin'] ?>";
                          }
                        </script>
                      </td>
       	       				<td class="name" width="10%" align="center">
                        <select Class = "letrase" name = "cRegEst" style = "width:99%">
                          <option value = "ALL">TODOS</option>
                          <option value = "ACTIVO" selected>ACTIVO</option>
                          <option value = "PROCESADO">PROCESADO</option>
                        </select>
                        <script language = "javascript">
                          if ("<?php echo $_POST['cRegEst'] ?>" == "") {
                            document.forms['frgrm']['cRegEst'].value = "";
                          } else {
                            document.forms['frgrm']['cRegEst'].value = "<?php echo $_POST['cRegEst'] ?>";
                          }
                        </script>
                      </td>

             	        <td Class="name" width="4%" align="right">
                        <?php
                        # Botones de Acceso Rapido
                        $qSqlPer  = "SELECT sys00005.menopcxx,sys00005.mendesxx,sys00006.modidxxx ";
                        $qSqlPer .= "FROM $cAlfa.sys00005,$cAlfa.sys00006 ";
                        $qSqlPer .= "WHERE ";
                        $qSqlPer .= "sys00006.usridxxx = \"{$kUser}\"             AND ";
                        $qSqlPer .= "sys00006.modidxxx = sys00005.modidxxx        AND ";
                        $qSqlPer .= "sys00006.proidxxx = sys00005.proidxxx        AND ";
                        $qSqlPer .= "sys00006.menidxxx = sys00005.menidxxx        AND ";
                        $qSqlPer .= "sys00006.modidxxx = \"{$_COOKIE["kModId"]}\" AND ";
                        $qSqlPer .= "sys00006.proidxxx = \"{$_COOKIE["kProId"]}\" ";
                        $qSqlPer .= "ORDER BY sys00005.menordxx";
                        $xSqlPer  = f_MySql("SELECT","",$qSqlPer,$xConexion01,"");
                        while ($vRPer = mysql_fetch_array($xSqlPer)) {
                          switch ($vRPer['menopcxx']) {
                            case "CAMBIAESTADO": ?>
                              <img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_drop.png" onClick = "javascript:fnCambEst('<?php echo $vRPer['menopcxx'] ?>')"
                                style = "cursor:hand" title="<?php echo ucwords(strtolower($vRPer['mendesxx'])) ?>">
                            <?php break;
                          }
                        }
                        # Fin de los Botones de Acceso Rapido
                        ?>
                      </td>
	       	         	</tr>
 	     	         	</table>
 	   	         	</center>
   	         		<hr></hr>
     	       		<center>
       	     			<table cellspacing="0" cellpadding="0" border="0" width="100%">
         	         	<tr bgcolor = '#D6DFF7'>
           	         	<td class="name" width="7%">
           	         		<a href = "javascript:fnButtonsAscDes('onclick','pbaidxxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:fnButtonsAscDes('onmouseover','pbaidxxx','')"
       	       					onmouseout="javascript:fnButtonsAscDes('onmouseout','pbaidxxx','')">Id Proceso</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png" border="0" width="11" height="9" title = "" id = "pbaidxxx">
           	         		<script language="javascript">fnButtonsAscDes('','pbaidxxx','');</script>
           	         	</td>
           	         	<!-- RDSP -->
           	         	<td class="name" width="24%">
           	         		<a href = "javascript:fnButtonsAscDes('onclick','pbatinde','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:fnButtonsAscDes('onmouseover','pbatinde','')"
       	       					onmouseout="javascript:fnButtonsAscDes('onmouseout','pbatinde','')">Interface</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png" border="0" width="11" height="9" title = "" id = "pbatinde">
           	         		<script language="javascript">fnButtonsAscDes('','pbatinde','');</script>
           	         	</td>
                      <td class="name" width="15%">
                        <a href = "javascript:fnButtonsAscDes('onclick','tramitex','ASC_AZ')" title="Ordenar"
                        onmouseover="javascript:fnButtonsAscDes('onmouseover','tramitex','')"
                        onmouseout="javascript:fnButtonsAscDes('onmouseout','tramitex','')">Tr&aacute;mite</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png" border="0" width="11" height="9" title = "" id = "tramitex">
                        <script language="javascript">fnButtonsAscDes('','tramitex','');</script>
                      </td>
                      <td class="name" width="18%">
                        <a href = "javascript:fnButtonsAscDes('onclick','regunomx','ASC_AZ')" title="Ordenar"
                        onmouseover="javascript:fnButtonsAscDes('onmouseover','regunomx','')"
                        onmouseout="javascript:fnButtonsAscDes('onmouseout','regunomx','')">Usuario</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regunomx">
                        <script language="javascript">fnButtonsAscDes('','regunomx','');</script>
                      </td>
                      <td class="name" width="10%">
                        <a href = "javascript:fnButtonsAscDes('onclick','tiempoes','ASC_AZ')" title="Ordenar"
                        onmouseover="javascript:fnButtonsAscDes('onmouseover','tiempoes','')"
                        onmouseout="javascript:fnButtonsAscDes('onmouseout','tiempoes','')">Tiempo Estimado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png" border="0" width="11" height="9" title = "" id = "'tiempoes'">
                        <script language="javascript">fnButtonsAscDes('','tiempoes','');</script>
                      </td>
                      <td class="name" width="8%">
                        <a href = "javascript:fnButtonsAscDes('onclick','progreso','ASC_AZ')" title="Ordenar"
                        onmouseover="javascript:fnButtonsAscDes('onmouseover','progreso','')"
                        onmouseout="javascript:fnButtonsAscDes('onmouseout','progreso','')">Progreso</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png" border="0" width="11" height="9" title = "" id = "progreso">
                        <script language="javascript">fnButtonsAscDes('','progreso','');</script>
                      </td>
                      <td class="name" width="8%">
                        <a href = "javascript:fnButtonsAscDes('onclick','pbarespr','ASC_AZ')" title="Ordenar"
                        onmouseover="javascript:fnButtonsAscDes('onmouseover','pbarespr','')"
                        onmouseout="javascript:fnButtonsAscDes('onmouseout','pbarespr','')">Resultado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png" border="0" width="11" height="9" title = "" id = "pbarespr">
                        <script language="javascript">fnButtonsAscDes('','pbarespr','');</script>
                      </td>
                      <td class="name" width="8%">
                        <a href = "javascript:fnButtonsAscDes('onclick','regestxx','ASC_AZ')" title="Ordenar"
                        onmouseover="javascript:fnButtonsAscDes('onmouseover','regestxx','')"
                        onmouseout="javascript:fnButtonsAscDes('onmouseout','regestxx','')">Estado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regestxx">
                        <script language="javascript">fnButtonsAscDes('','regestxx','');</script>
                      </td>
                 	    <td Class='name' width="2%" align="right">
                 	    	<input type="checkbox" name="oCheckAll" onClick = 'javascript:fnMarca()'>
                 	    </td>
                 		</tr>
								      <script languaje="javascript">
												document.frgrm.vRecords.value = "<?php echo count($mMatrizTra) ?>";
											</script>
 	                    <?php
 	                    for ($i=intval($vLimInf);$i<intval($vLimInf+$vLimSup);$i++) {
 	                    	if ($i < count($mMatrizTra)) { // Para Controlar el Error
	 	                    	$nColor = "#FFFFFF";
	                   	    if($y % 2 == 0) {
	                   	    	$nColor = "#E5E5E5";
													} ?>
													<!-- <tr bgcolor = "<?php echo $nColor ?>"> -->
													<tr bgcolor = "<?php echo $nColor ?>" onmouseover="javascript:uRowColor(this,'#CCFFCC')"
													  onmouseout="javascript:uRowColor(this,'<?php echo $nColor ?>')">
	          	              <td class="letra8">
	          	                <a href = javascript:fnVerProceso('<?php echo $mMatrizTra[$i]['pbaidxxx'] ?>')><?php echo $mMatrizTra[$i]['pbaidxxx'] ?></a>
	          	              </td>
	          	              <td class="letra8"><?php echo substr($mMatrizTra[$i]['pbatinde'],0,41) ?></td>
                            <td class="letra8"><?php echo $mMatrizTra[$i]['tramitex'] ?></td>
	          	              <td class="letra8"><?php echo substr($mMatrizTra[$i]['regunomx'],0,28) ?></td>
                            <td class="letra8"><?php echo $mMatrizTra[$i]['tiemesmi'] ?></td>
	          	              <td class="letra8"><?php echo $mMatrizTra[$i]['progreso'] ?></td>
	          	              <td class="letra8"><?php echo ($mMatrizTra[$i]['pbarespr'] == "FALLIDO") ? "<font color=\"red\">".$mMatrizTra[$i]['pbarespr']."</font>" : $mMatrizTra[$i]['pbarespr'] ?></td>
	          	              <td class="letra8"><?php echo $mMatrizTra[$i]['regestxx'] ?></td>
	            	            <td class="letra8" align="right"><input type="checkbox" name="oCheck"
													    value = "<?php echo count($mMatrizTra) ?>"
	                   	    		id="<?php echo $mMatrizTra[$i]['pbaidxxx']."~".$mMatrizTra[$i]['regestxx'] ?>"
	                   	    		onclick="javascript:document.forms['frgrm']['vRecords'].value='<?php echo count($mMatrizTra) ?>'" style="height:15">
	            	            </td>
	              	        </tr>
	                	    	<?php $y++;
 	                    	}
 	                    }
 	                    if(count($mMatrizTra) == 1){ ?>
 	                       <script language="javascript">
 	                         document.forms['frgrm'].oCheck.checked = true;
 	                       </script>
 	                       <?php
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
	</body>
</html>
