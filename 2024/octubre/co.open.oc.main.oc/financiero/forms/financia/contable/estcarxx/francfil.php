<?php
  namespace openComex;
/**
	* 
	* Descripcion: Formulario para Ver Filtros .
	* @author Julio Lopez<julio.lopez@open-eb.co>
	* @package openComex
	*/
	include("../../../../libs/php/utility.php");

  $dHoy = date('Y-m-d');

  $qSysProbg  = "SELECT * ";
  $qSysProbg .= "FROM $cBeta.sysprobg ";
  $qSysProbg .= "WHERE ";
  $qSysProbg .= "pbaidxxx =\"$gPbaId\" AND ";
  $qSysProbg .= "DATE(regdcrex) =\"$dHoy\" AND ";
  $qSysProbg .= "regusrxx = \"$kUser\" AND ";
  $qSysProbg .= "pbadbxxx = \"$cAlfa\" AND ";
  $qSysProbg .= "pbamodxx = \"FACTURACION\" AND ";
  $qSysProbg .= "pbatinxx = \"ANALISISDECARTERA\" ";
  $qSysProbg .= "ORDER BY regdcrex DESC";
  $xSysProbg = f_MySql("SELECT", "", $qSysProbg, $xConexion01, "");

  $mArcProBg = array();

  while ($xRB = mysql_fetch_array($xSysProbg)) {
    $nInd_mArcProBg = count($mArcProBg);
    $mPost = f_Explode_Array($xRB['pbapostx'], "|", "~");
    for ($nP = 0; $nP < count($mPost); $nP++) {
      if ($mPost[$nP][0] != "") {
        $mArcProBg[$nInd_mArcProBg][$mPost[$nP][0]] = $mPost[$nP][1];
      }
    }    
  }

  /**
	 *  Cookie fija
	 */
	$kDf = explode("~",$_COOKIE["kDatosFijos"]);
	$kMysqlHost = $kDf[0];
	$kMysqlUser = $kDf[1];
	$kMysqlPass = $kDf[2];
	$kMysqlDb   = $kDf[3];
	$kUser      = $kDf[4];
	$kLicencia  = $kDf[5];
	$swidth     = $kDf[6];
?>
<html>
	<head>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/estilo.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/custom.css'>
		<script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/utility.js'></script>
		<script language = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory_New ?>/date_picker.js"></script>
		<script language="javascript">
			function fnRetorna(){
				window.close();
			}
  	</script>
	</head>
	<body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="360">
				<tr>
					<td>
				  	<fieldset>
					  	<legend><b>Filtros Condiciones del Reporte</b></legend><br>
                <center>
                  <table border = "0" cellpadding = "0" cellspacing = "0" width = "360">
                    <?php echo f_Columnas(18,20); ?>
                    <tr>
									    <td Class = "name" colspan = "18">
                        <table width= class="table-grid" style="border-collapse: collapse;" cellpadding="2" cellspacing="0">
                          <thead>
                            <tr bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>">
                              <th style="text-align:center;border: 1px solid #000000;" Class = "clase08" width = "180">
                                Filtro
                              </th>
                              <th style="text-align:center;border: 1px solid #000000;" Class = "clase08" width = "180">
                                Valor
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            if($gCarEda == "SI"){ ?>
                              <tr>
                                <td style="text-align:left;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;">
                                  Rango Uno
                                </td>
                                <td style="text-align:center;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;">
                                  <?php echo $mArcProBg[0]['nComVlr01'] ?>
                                </td>
                              </tr>
                              <tr>
                                <td style="text-align:left;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;">
                                  Rango Dos
                                </td>
                                <td style="text-align:center;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;">
                                  <?php echo $mArcProBg[0]['nComVlr02'] ?>
                                </td>
                              </tr>
                              <tr>
                                <td style="text-align:left;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;">
                                  Rango Tres
                                </td>
                                <td style="text-align:center;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;">
                                  <?php echo $mArcProBg[0]['nComVlr03'] ?>
                                </td>
                              </tr>
                              <tr>
                                <td style="text-align:left;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;">
                                  Rango Cuatro
                                </td>
                                <td style="text-align:center;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;">
                                  <?php echo $mArcProBg[0]['nComVlr04'] ?>
                                </td>
                              </tr>
                            <?php
                            } ?>
                            <tr>
                              <td style="text-align:left;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;">
                                Rango de Cuentas
                              </td>
                              <td style="text-align:center;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;">
                                <?php echo ($mArcProBg[0]['cPucIni'] != "" ? $mArcProBg[0]['cPucIni']." {$mArcProBg[0]['cOpe01']} ".$mArcProBg[0]['cPucFin'] : "") ?>
                              </td>
                            </tr>                            
                            <tr>
                              <td style="text-align:left;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;">
                                Rango de Nits
                              </td>
                              <td style="text-align:center;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;">
                                <?php echo ($mArcProBg[0]['nNitIni'] != "" ? $mArcProBg[0]['nNitIni']." {$mArcProBg[0]['cOpe02']} ".$mArcProBg[0]['nNitFin'] : "")?>
                              </td>
                            </tr>
                            <tr>
                              <td style="text-align:left;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;">
                                Rango Centro Costos
                              </td>
                              <td style="text-align:center;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;">
                                <?php echo ($mArcProBg[0]['nCcoIni'] != "" ? $mArcProBg[0]['nCcoIni']." {$mArcProBg[0]['cOpe03']} ".$mArcProBg[0]['nCcoFin'] : "") ?>
                              </td>
                            </tr>
                            <tr>
                              <td style="text-align:left;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;">
                                Fecha de Corte
                              </td>
                              <td style="text-align:center;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;">
                                <?php echo $mArcProBg[0]['dHasta'] ?>
                              </td>
                            </tr>
                          </tbody>
                        </table>
									    </td>
								    </tr>
                  </table>
                </center>
							</form>
						</fieldset>
            <center>
              <table border="0" cellpadding="0" cellspacing="0" width="360">
                <tr height="21">
                  <td width="269" height="21"></td>
                  <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand"
                      onClick = "javascript:fnRetorna();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
                  </td>
                </tr>
              </table>
            </center>
					</td>
				</tr>
			</table>
		</center>		
	</body>
</html>