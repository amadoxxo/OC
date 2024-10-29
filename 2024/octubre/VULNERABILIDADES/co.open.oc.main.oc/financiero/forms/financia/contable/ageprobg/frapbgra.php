<?php
  namespace openComex;
/**
 * Script para guardar cambio en el Tracking Agendamiento Procesos en Background
 * @author Ricardo Alonso Rincón Vega <ricardo.rincon@opentecnologia.com.co>
 * @package openComex
 * @todo NA
 *
 * Variables:
 */

ini_set('error_reporting', E_ERROR);
ini_set("display_errors","1");

# Librerias
include("../../../../libs/php/utility.php");
include("../../../../libs/php/uticimpo.php");
include("../../../../../config/config.php");

# Cookie fija
$kDf = explode("~", $_COOKIE["kDatosFijos"]);
$kMysqlHost = $kDf[0];
$kMysqlUser = $kDf[1];
$kMysqlPass = $kDf[2];
$kMysqlDb = $kDf[3];
$kUser = $kDf[4];
$kLicencia = $kDf[5];
$swidth = $kDf[6];

# Variable de Control de Error
$nSwitch = 0;

# Variable de Mensaje de Error
$cMsj = "";

# Validaciones descripcion obligatorio
switch ($_COOKIE['kModo']){
  case "CAMBIAESTADO":
    /**
     * Validando Licencia
     */
    $nLic = f_Licencia();
    if ($nLic == 0) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "Error grave de Seguridad otro usuario ingreso con su clave.\n";
    }

    if($_POST['cPbaId'] == ""){
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "El Id de Proceso No puede ser Vacio.\n";
    }else{
      $qIdProc  = "SELECT ";
      $qIdProc .= "doiidxxx, ";
			$qIdProc .= "doisfidx, ";
			$qIdProc .= "admidxxx, ";
      $qIdProc .= "pbaidxxx, ";
      $qIdProc .= "pbatabxx, ";
      $qIdProc .= "regestxx, ";
      $qIdProc .= "regdinix ";
      $qIdProc .= "FROM $cBeta.sysprobg ";
      $qIdProc .= "WHERE ";
      $qIdProc .= "pbaidxxx = \"{$_POST['cPbaId']}\" ";
      $xIdProc  = f_MySql("SELECT", "", $qIdProc, $xConexion01, "");
      $vIdProc  = mysql_fetch_array($xIdProc);
      if(mysql_num_rows($xIdProc) > 0){
      	//Validacion aplica solo SIACO y ADMIN.
        if($cAlfa == "DESIACOSIP" || $cAlfa == "TESIACOSIP" || $cAlfa == "SIACOSIA" || $kUser == "ADMIN"){
          //Valido que el estado del proceso este ACTIVO
          if($vIdProc['regestxx'] == "ACTIVO"){
            # No hace nada
          }else{
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
            $cMsj .= "Solo se permite Inactivar Procesos en Estado [ACTIVO].\n";
          }
        }else{
          if($vIdProc['regestxx'] == "ACTIVO" && $vIdProc['regdinix'] == "0000-00-00 00:00:00"){
            # No hace nada
          }else{
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
            $cMsj .= "Solo se permite Inactivar Procesos en Estado [ACTIVO] y No se esta Ejecutando.\n";
          }
				}
      }else{
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
        $cMsj .= "El Id Proceso [{$_POST['cPbaId']}] No Existe o No se Encuentra.\n";
      }
    }
  break;
  default:
    $nSwitch = 1;
    $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
    $cMsj .= "El Modo de Grabado No Es Correcto.\n";
  break;
}

$cTexto = "";
if($nSwitch == 0){
  switch ($_COOKIE['kModo']) {
    case "CAMBIAESTADO":
			//Opción Eliminar Procesos en Background. Aplica solo SIACO y ADMIN.
			if($cAlfa == "DESIACOSIP" || $cAlfa == "TESIACOSIP" || $cAlfa == "SIACOSIA" || $kUser == "ADMIN"){
				$nEncontro = 0;
				if(($vIdProc['doiidxxx'] != "" && $vIdProc['doisfidx'] != "" && $vIdProc['admidxxx'] != "") ||
          ($vIdProc['doiidxxx'] != "" && $vIdProc['doisfidx'] == "" && $vIdProc['admidxxx'] == "")){

          if($vIdProc['doiidxxx'] != "" && $vIdProc['doisfidx'] != "" && $vIdProc['admidxxx'] != ""){
            $cTexto = "Tramite";
  					//Valido que existan DO en la SIAI0202
            $qRegDoi  = "SELECT DOIIDXXX ";
            $qRegDoi .= "FROM $cAlfa.SIAI0202 ";
            $qRegDoi .= "WHERE ";
            $qRegDoi .= "DOIIDXXX = \"{$vIdProc['doiidxxx']}\" AND ";
            $qRegDoi .= "DOISFIDX = \"{$vIdProc['doisfidx']}\" AND ";
            $qRegDoi .= "ADMIDXXX = \"{$vIdProc['admidxxx']}\" LIMIT 0,1 ";
            $xRegDoi  = f_MySql("SELECT", "", $qRegDoi, $xConexion01, "");
            if(mysql_num_rows($xRegDoi) == 1){
              $nEncontro = 1;
            }

  					//Valido que existan DO en la SIAI0203
            $qSubDoi  = "SELECT DOIIDXXX ";
            $qSubDoi .= "FROM $cAlfa.SIAI0203 ";
            $qSubDoi .= "WHERE ";
            $qSubDoi .= "DOIIDXXX = \"{$vIdProc['doiidxxx']}\" AND ";
            $qSubDoi .= "DOISFIDX = \"{$vIdProc['doisfidx']}\" AND ";
            $qSubDoi .= "ADMIDXXX = \"{$vIdProc['admidxxx']}\" LIMIT 0,1";
            $xSubDoi  = f_MySql("SELECT", "", $qSubDoi, $xConexion01, "");
            if(mysql_num_rows($xSubDoi) == 1){
              $nEncontro = 1;
            }

            //Valido que existan DO en la SIAI0204
            $qFacDoi  = "SELECT DOIIDXXX ";
            $qFacDoi .= "FROM $cAlfa.SIAI0204 ";
            $qFacDoi .= "WHERE ";
            $qFacDoi .= "DOIIDXXX = \"{$vIdProc['doiidxxx']}\" AND ";
            $qFacDoi .= "DOISFIDX = \"{$vIdProc['doisfidx']}\" AND ";
            $qFacDoi .= "ADMIDXXX = \"{$vIdProc['admidxxx']}\" LIMIT 0,1";
            $xFacDoi  = f_MySql("SELECT", "", $qFacDoi, $xConexion01, "");
            if(mysql_num_rows($xFacDoi) == 1){
              $nEncontro = 1;
            }

  					//Valido que existan DO en la SIAI0205
            $qIteDoi  = "SELECT DOIIDXXX ";
            $qIteDoi .= "FROM $cAlfa.SIAI0205 ";
            $qIteDoi .= "WHERE ";
            $qIteDoi .= "DOIIDXXX = \"{$vIdProc['doiidxxx']}\" AND ";
            $qIteDoi .= "DOISFIDX = \"{$vIdProc['doisfidx']}\" AND ";
            $qIteDoi .= "ADMIDXXX = \"{$vIdProc['admidxxx']}\" LIMIT 0,1";
            $xIteDoi  = f_MySql("SELECT", "", $qIteDoi, $xConexion01, "");
            if(mysql_num_rows($xIteDoi) == 1){
              $nEncontro = 1;
            }
          }

          //Registro Vuce
          if($vIdProc['doiidxxx'] != "" && $vIdProc['doisfidx'] == "" && $vIdProc['admidxxx'] == ""){
            $cTexto = "Registro";
            /**
             * Buscando si el Registro ya tiene datos migrados
             */
            $qSubpartidas  = "SELECT REGIDXXX ";
            $qSubpartidas .= "FROM $cAlfa.RIM00151 ";
            $qSubpartidas .= "WHERE ";
            $qSubpartidas .= "REGIDXXX = \"{$vIdProc['doiidxxx']}\" LIMIT 0,1 ";
            $xSubpartidas   = f_MySql("SELECT", "", $qSubpartidas, $xConexion01, "");
            if(mysql_num_rows($xSubpartidas) == 1){
              $nEncontro = 1;
            }

            /**
             * Buscando si hay items
             */
            $qItems  = "SELECT REGIDXXX ";
            $qItems .= "FROM $cAlfa.RIM00152 ";
            $qItems .= "WHERE ";
            $qItems .= "REGIDXXX = \"{$vIdProc['doiidxxx']}\" LIMIT 0,1 ";
            $xItems   = f_MySql("SELECT", "", $qItems, $xConexion01, "");
            if(mysql_num_rows($xItems) == 1){
              $nEncontro = 1;
            }
          }

					if($nEncontro == 1 && $vIdProc['regdinix'] != "0000-00-00 00:00:00"){
						$dRegDIni = date_create($vIdProc['regdinix']); 	//Fecha y Hora Inicio Ejecucion
						$dFecAct = date_create(date('Y-m-d H:i:s'));		//Fecha Actual
						$dHorDif = date_diff($dFecAct, $dRegDIni); 			//Diferencia de Horas
						//Valido que el proceso lleve mas de 2 horas de Ejecutado
						if($dHorDif->h >= 2){
							$mTablasTemporales = explode("~", $vIdProc['pbatabxx']);
							for($i=0;$i<count($mTablasTemporales);$i++){
								if($mTablasTemporales[$i] != ""){
                  $mReturnBorrarTabla = fnBorrarTablasProcesoBackground($mTablasTemporales[$i]);
                  if($mReturnBorrarTabla[0] == "false"){
                    $nSwitch = 1;
                    for($nR=1;$nR<count($mReturnBorrarTabla);$nR++){
                      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                      $cMsj .= $mReturnBorrarTabla[$nR]."\n";
                    }
                  }
								}
							}
							if($nSwitch == 0){
                $mCampos  = array(array('NAME'=>'regestxx', 'VALUE'=>("INACTIVO")															,'CHECK'=>'SI'),
                                  array('NAME'=>'pbaidxxx', 'VALUE'=>(trim(strtoupper($_POST['cPbaId'])))   		,'CHECK'=>'WH'));
                if(f_MySql("UPDATE", "sysprobg", $mCampos, $xConexion01, $cBeta)){
                  $nSwitch = 0;
                  $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                  $cMsj .= "Se Anulo el Registro con Exito";
                }else{
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                  $cMsj .= "Error al Anular el Proceso en Background.";
                }
							}
						}else{
							$nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
              $cMsj .= "No se Permite Inactivar Proceso en Background por ser un $cTexto Parcial. ";
						}
					}else{
						$mTablasTemporales = explode("~", $vIdProc['pbatabxx']);
						for($i=0;$i<count($mTablasTemporales);$i++){
							if($mTablasTemporales[$i] != ""){
								$mReturnBorrarTabla = fnBorrarTablasProcesoBackground($mTablasTemporales[$i]);
                if($mReturnBorrarTabla[0] == "false"){
                  $nSwitch = 1;
                  for($nR=1;$nR<count($mReturnBorrarTabla);$nR++){
                    $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                    $cMsj .= $mReturnBorrarTabla[$nR]."\n";
                  }
                }
							}
						}

            if($nSwitch == 0){
              $mCampos  = array(array('NAME'=>'regestxx', 'VALUE'=>("INACTIVO")															,'CHECK'=>'SI'),
                                array('NAME'=>'pbaidxxx', 'VALUE'=>(trim(strtoupper($_POST['cPbaId'])))   		,'CHECK'=>'WH'));
              if(f_MySql("UPDATE", "sysprobg", $mCampos, $xConexion01, $cBeta)){
                $nSwitch = 0;
                $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                $cMsj .= "Se Anulo el Registro con Exito";
              }else{
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                $cMsj .= "Error al Anular el Registro";
              }
            }
					}
				}else{
					$mTablasTemporales = explode("~", $vIdProc['pbatabxx']);
					for($i=0;$i<count($mTablasTemporales);$i++){
						if($mTablasTemporales[$i] != ""){
							$mReturnBorrarTabla = fnBorrarTablasProcesoBackground($mTablasTemporales[$i]);
              if($mReturnBorrarTabla[0] == "false"){
                $nSwitch = 1;
                for($nR=1;$nR<count($mReturnBorrarTabla);$nR++){
                  $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                  $cMsj .= $mReturnBorrarTabla[$nR]."\n";
                }
              }
						}
					}

          if($nSwitch == 0){
            $mCampos  = array(array('NAME'=>'regestxx', 'VALUE'=>("INACTIVO")															,'CHECK'=>'SI'),
                              array('NAME'=>'pbaidxxx', 'VALUE'=>(trim(strtoupper($_POST['cPbaId'])))   		,'CHECK'=>'WH'));
            if(f_MySql("UPDATE", "sysprobg", $mCampos, $xConexion01, $cBeta)){
              $nSwitch = 0;
              $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
              $cMsj .= "Se Anulo el Registro con Exito";
            }else{
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
              $cMsj .= "Error al Anular el Registro";
            }
          }
        }
			}else{
        $mCampos  = array(array('NAME'=>'regestxx', 'VALUE'=>(trim(strtoupper($vIdProc['regestxx'])) == "ACTIVO" ? "INACTIVO" : "ACTIVO"), 'CHECK'=>'SI'),
                          array('NAME'=>'pbaidxxx', 'VALUE'=>(trim(strtoupper($_POST['cPbaId'])))                                        , 'CHECK'=>'WH'));
        if(f_MySql("UPDATE", "sysprobg", $mCampos, $xConexion01, $cBeta)){
          $nSwitch = 0;
          $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
          $cMsj .= "Se ".($vIdProc['regestxx'] == "ACTIVO" ? "Anulo" : "Activo")." el Registro con Exito";
        }else{
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
          $cMsj .= "Error al Anular el Registro";
        }
			}
    break;
  }
}

if ($nSwitch == 0) {
  f_Mensaje(__FILE__, __LINE__, $cMsj);
  ?>
  <form name = "frgrm" action = "frapbini.php" method = "post" target = "fmwork"></form>
  <script languaje = "javascript">
    document.forms['frgrm'].submit();
  </script>
  <?php
}else{
  f_Mensaje(__FILE__, __LINE__, $cMsj."Verifique.");
}

function fnBorrarTablasProcesoBackground($pTabla) {
  global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;

  /**
   * Variable para saber si hay o no errores de validacion.
   *
   * @var number
   */
  $nSwitch = 0;

  /**
   * Matriz para Retornar Valores
   */
  $mReturn = array();

  /**
   * Reservando Primera Posición para retorna true o false
   */
  $mReturn[0] = "";

  /**
   * Instanciando Metodo para hacer conexion
   */
  $objConectar = new cEstructuras();

  /**
   * Validando que la tabla no sea vacia
   */
  if($pTabla == ""){
    $nSwitch = 1;
    $mReturn[count($mReturn)] = "La Tabla para Eliminar, no puede ser Vacia.";
  }

  /**
   * Llamando Metodo que hace conexion
   */
  $mReturnConexionTM = $objConectar->fnConectarDB();
  if($mReturnConexionTM[0] == "true"){
    $xConexionTM = $mReturnConexionTM[1];
  }else{
    $nSwitch = 1;
    for($nR=1;$nR<count($mReturnConexionTM);$nR++){
      $mReturn[count($mReturn)] = $mReturnConexionTM[$nR];
    }
  }

  /**
   * Eliminando Tabla Temporal
   */
  if($nSwitch == 0){
    $qDrop  = "DROP TABLE IF EXISTS $cAlfa.$pTabla ";
    $xDrop  = mysql_query($qDrop,$xConexionTM);

    if(!$xDrop){
      $nSwitch = 1;
      $mReturn[count($mReturn)] = "Error al Eliminar Tabla[{$pTabla}].";
    }
    mysql_free_result($xDrop);
  }

  if($nSwitch == 0){
    $mReturn[0] = "true";
  }else{
    $mReturn[0] = "false";
  }
  return $mReturn;
}##function fnBorrarTablasProcesoBackground() {##
