<?php
  namespace openComex;
/**
 * Ver Id de Proceso
 * @author Jorge Alberto Ramirez Jimenez <ricardo.rincon@opentecnologia.com.co>
 * @package openComex
 * @todo NA
 *
 * Variables:
 */

# Librerias
include("../../../../libs/php/utility.php");

# Cookie fija
$kDf = explode("~", $_COOKIE["kDatosFijos"]);
$kMysqlHost = $kDf[0];
$kMysqlUser = $kDf[1];
$kMysqlPass = $kDf[2];
$kMysqlDb = $kDf[3];
$kUser = $kDf[4];
$kLicencia = $kDf[5];
$swidth = $kDf[6];

$qSysProBg  = "SELECT * ";
$qSysProBg .= "FROM $cBeta.sysprobg ";
$qSysProBg .= "WHERE ";
$qSysProBg .= "pbaidxxx = \"{$_GET['gIdProc']}\"";
$xSysProBg  = f_MySql("SELECT","",$qSysProBg,$xConexion01,"");
$vSysProBg  = mysql_fetch_array($xSysProBg);
$vSysProBg['tramitex'] = ($vSysProBg['admidxxx'] != "" || $vSysProBg['doiidxxx'] != "" || $vSysProBg['doisfidx'] != "") ? trim($vSysProBg['admidxxx']."-".$vSysProBg['doiidxxx']."-".$vSysProBg['doisfidx'],"-") : "";
$vSysProBg['tiemesmi'] = ($vSysProBg['pbatxixx'] * $vSysProBg['pbacrexx'] <= 60) ? ($vSysProBg['pbatxixx'] * $vSysProBg['pbacrexx'])." SEG" : round(($vSysProBg['pbatxixx'] * $vSysProBg['pbacrexx']) / 60)." MIN";
$nTieEst = (strtotime(date('Y-m-d H:i:s')) - strtotime($vSysProBg['regdinix'])) / ($vSysProBg['pbatxixx'] * $vSysProBg['pbacrexx']);
$vSysProBg['progreso'] = ($vSysProBg['regdinix'] == "" || $vSysProBg['regdinix'] == "0000-00-00 00:00:00") ? "" : (($vSysProBg['regdfinx'] != "0000-00-00 00:00:00") ? "100" : $nTieEst)."&#37;";

if(mysql_num_rows($xSysProBg) > 0){
  ?>
  <html>
    <head>
      <title>Ver Resultado</title>
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
      <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
      <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
      <script language="javascript">
        function fnDescargar(xArchivo,xTipInt){
          parent.fmpro2.location = "frapbdoc.php?cRuta="+xArchivo+"&cTipInt="+xTipInt;
        }
      </script>
    </head>
    <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
    <center>
      <form name = "frgrm" action = "frapbver.php" method = "post">
        <!-- Proceso en Background -->
        <table border ="0" cellpadding="0" cellspacing="0" width="600">
          <tr>
            <td>
              <fieldset >
                <legend><b>Proceso en Background</b></legend>
                <center>
                  <table border = "0" cellpadding = "0" cellspacing = "0" width="580">
                    <tr>
                      <td bgcolor="#CEE3F6" style="padding: 2px"><b><?php echo $vSysProBg['pbatinde'] ?>&nbsp;[ID: <?php echo $vSysProBg['pbaidxxx'] ?>]</b></td>
                    </tr>
                    <?php if($vSysProBg['tramitex'] != ""){ ?>
                      <tr>
                        <td style="padding: 2px"><b>Tramite:</b><br><?php echo $vSysProBg['tramitex'] ?></td>
                      </tr>
                    <?php } ?>
                    <?php if($vSysProBg['cliidxxx'] != ""){ ?>
                      <tr>
                        <td style="padding: 2px"><b>Importador:</b><br><?php echo $vSysProBg['clinomxx']." [{$vSysProBg['cliidxxx']}]" ?></td>
                      </tr>
                    <?php } ?>
                    <?php if($vSysProBg['pbaopcxx'] != ""){ ?>
                      <tr>
                        <td style="padding: 2px"><b>Opciones de Agrupamiento o Filtros:</b><br><?php echo str_replace("~", "<br>", $vSysProBg['pbaopcxx']) ?></td>
                      </tr>
                    <?php } ?>
                    <tr>
                      <td style="padding: 2px"><b>Usuario que agendo el proceso:</b><br><?php echo "[".$vSysProBg['regusrxx']."] ".$vSysProBg['regunomx'] ?></td>
                    </tr>
                    <tr>
                      <td style="padding: 2px"><b>Fecha y Hora Creaci&oacute;n del Proceso:</b><br><?php echo $vSysProBg['regdcrex'] ?></td>
                    </tr>
                    <?php if($vSysProBg['pbacrexx'] != ""){ ?>
                      <tr>
                        <td style="padding: 2px"><b>Cantidad de Registros a Procesar:</b><br><?php echo $vSysProBg['pbacrexx'] ?></td>
                      </tr>
                    <?php } ?>
                    <?php if($vSysProBg['tiemesmi'] != ""){ ?>
                      <tr>
                        <td style="padding: 2px"><b>Tiempo Estimado de Ejecuci&oacute;n:</b><br><?php echo $vSysProBg['tiemesmi'] ?></td>
                      </tr>
                    <?php } ?>
                    <?php if($vSysProBg['progreso'] != ""){ ?>
                      <tr>
                        <td style="padding: 2px"><b>Progreso:</b><br><?php echo $vSysProBg['progreso'] ?></td>
                      </tr>
                    <?php } ?>
                    <?php if($vSysProBg['regdinix'] != "0000-00-00 00:00:00"){ ?>
                      <tr>
                        <td style="padding: 2px"><b>Fecha y Hora Inicio Ejecuci&oacute;n:</b><br><?php echo ($vSysProBg['regdinix'] != "" && $vSysProBg['regdinix'] != "0000-00-00 00:00:00") ? $vSysProBg['regdinix'] : "" ?></td>
                      </tr>
                    <?php } ?>
                    <?php if($vSysProBg['regdfinx'] != "0000-00-00 00:00:00"){ ?>
                      <tr>
                        <td style="padding: 2px"><b>Fecha y Hora Finalizacion Ejecuci&oacute;n:</b><br><?php echo ($vSysProBg['regdfinx'] != "" && $vSysProBg['regdfinx'] != "0000-00-00 00:00:00") ? $vSysProBg['regdfinx'] : "" ?></td>
                      </tr>
                    <?php } ?>
                    <?php if($vSysProBg['pbarespr'] != ""){ ?>
                      <tr>
                        <td style="padding: 2px"><b>Resultado Ejecuci&oacute;n:</b><br><?php echo ($vSysProBg['pbarespr'] == "FALLIDO") ? "<font color=\"red\">".$vSysProBg['pbarespr']."</font>" : $vSysProBg['pbarespr'] ?></td>
                      </tr>
                    <?php } ?>
                    <tr>
                      <td style="padding: 2px"><b>Estado:</b><br><?php echo $vSysProBg['regestxx'] ?></td>
                    </tr>
                    <?php if($vSysProBg['pbaerrxx'] != ""){ ?>
                      <tr>
                        <td style="padding: 2px"><b>Mensajes de Error/Alerta:</b><br><?php echo $vSysProBg['pbaerrxx'] ?></td>
                      </tr>
                    <?php } ?>
                    <?php if($vSysProBg['pbaexcxx'] != ""){ ?>
                      <tr>
                        <td style="padding: 2px"><b>Archivos Generados:</b><br>
                          <?php $vArchivos = explode("~", trim($vSysProBg['pbaexcxx'],"~"));
                            $cTipInt = "";
                            for($nA=0; $nA<count($vArchivos); $nA++) {
                              if($vSysProBg['pbatinxx'] == "ESTADOCUENTATRAMITES"){
                                $cRuta = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . "propios/" . $cAlfa . "/estado_cuenta" . "/" . $vArchivos[$nA];
                                $cTipInt = "ESTADOCUENTATRAMITES";
                              }else{
                                $cRuta = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$vArchivos[$nA];
                              }
                              if (file_exists($cRuta)) { ?>
                                <a href = "javascript:fnDescargar('<?php echo $vArchivos[$nA] ?>','<?php echo $cTipInt ?>')"><?php echo $vArchivos[$nA] ?></a><br>
                              <?php } else {
                                echo $vArchivos[$nA]."<br>";
                              }
                            }?>
                        </td>
                      </tr>
                    <?php } ?>
                  </table>
                </center>
              </fieldset>
              <center>
                <table border="0" cellpadding="0" cellspacing="0" width="600">
                  <tr height="21">
                    <td width="418" height="21"></td>
                    <td width="75" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand"
                        onClick = "javascript:parent.window.close()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
                    </td>
                  </tr>
                </table>
              </center>
            </td>
          </tr>
        </table>
        <!-- fin tabla para historico -->
      </form>
    </center>
    </body>
  </html>
  <?php
}else{
  f_Mensaje(__FILE__,__LINE__,"El Id de Proceso [{$_GET['gIdProc']}] No se Encuentra No Existe");
  ?>
  <script>
    window.close();
  </script>
  <?php
}
?>
