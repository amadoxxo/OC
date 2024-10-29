<?php
  namespace openComex;
  /**
   * Graba Comprobantes.
   * --- Descripcion: Permite Guardar en la tabla Comprobantes un nuevo registro.
   * @author Diego Fernando Cortes Rojas <diego.cortes@openits.co>
   * @package opencomex
   * @version 001
   */
  include("../../../../../financiero/libs/php/utility.php");

  $nSwitch = 0; // Switch para Vericar la Validacion de Datos
  $cMsj    = "\n";

  switch ($_COOKIE['kModo']) {
    case "NUEVO":
    case "EDITAR":
      if ($_COOKIE['kModo'] == "NUEVO") {
        // Validando Id del Comprobante
        if ($_POST['cComId'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Id del Comprobante no puede ser vacio, \n";
        }

        // Validando Codigo del Comprobante
        if ($_POST['cComCod'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Codigo del Comprobante no puede ser vacio, \n";
        }

        $qComDat  = "SELECT * ";
        $qComDat .= "FROM $cAlfa.lpar0117 ";
        $qComDat .= "WHERE ";
        $qComDat .= "$cAlfa.lpar0117.comidxxx = \"{$_POST['cComId']}\" AND ";
        $qComDat .= "$cAlfa.lpar0117.comcodxx = \"{$_POST['cComCod']}\" LIMIT 0,1 ";
        $xComDat  = f_MySql("SELECT","",$qComDat,$xConexion01,"");
        $nFilCom  = mysql_num_rows($xComDat);
        if($nFilCom > 0){
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Codigo de Comprobante {$_POST['cComCod']} ya existe, \n";
        }
      } elseif ($_COOKIE['kModo'] == "EDITAR") {
        $cControlCsc = "x";
        if ($_POST['vChMensu'] != "") {
          $cControlCsc = $_POST['vChMensu'];
        } elseif ($_POST['vChAnual'] != "") {
          $cControlCsc = $_POST['vChAnual'];
        } elseif ($_POST['vChIndef'] != "") {
          $cControlCsc = $_POST['vChIndef'];
        }

        // Valida si cambia el tipo de consecutivo o el contol de consecutivo
        if ($_POST['rBtCt'] != $_POST['cComTcoEdit'] || ($_POST['rBtCt'] == "AUTOMATICO" && $_POST['cComCcoEdit'] != $cControlCsc)) {
          // Valida si el comprobante ya tiene movimiento
          $cRespuesta = fnExisteMovimientoComprobante($_POST['cComIdEdit'], $_POST['cComCod']);

          if ($cRespuesta == "SI") {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Comprobante ya Cuenta con Movimiento, No se Puede Editar, \n";
          }
        }
      }

      // Validando Descripcion Comprobantes
      if ($_POST['cComDes'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Descripcion Comprobantes no puede ser vacio, \n";
      }

      // Validando campos despues de que se seleccione el id 
      if ($_POST['cComId'] == "M" || $_POST['cComId'] == "C") {
        // Validando que se seleccione un tipo despues de haber seleccionado un id
        if ($_POST['rBtCt'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Debe seleccionar un tipo de consecutivo, \n";
        } else {
          if ($_POST['rBtCt'] == "AUTOMATICO") {
            // Validando que se seleccione un periodo
            if ($_POST['vChMensu'] == "" && $_POST['vChAnual'] == "" && $_POST['vChIndef'] == "") {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "Debe seleccionar un Periodo, \n";
            }
          }
        }
        // Validando Prefijo del Consecutivo
        if ($_POST['cComPre'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Prefijo de Comprobante no puede ser vacio, \n";
        }
        
        // Validando Prefijo que sea alfanumerico
        if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['cComPre'])) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Prefijo de Comprobante debe ser alfanumerico, \n";
        }

        if ($_COOKIE['kModo'] == "NUEVO") {
          // Validando que el Prefijo exista
          $qPrefijo  = "SELECT comprexx, comidxxx ";
          $qPrefijo .= "FROM $cAlfa.lpar0117 ";
          $qPrefijo .= "WHERE ";
          $qPrefijo .= "$cAlfa.lpar0117.comidxxx = \"{$_POST['cComId']}\" AND ";
          $qPrefijo .= "$cAlfa.lpar0117.comprexx = \"{$_POST['cComPre']}\" LIMIT 0,1 ";
          $xPrefijo  = f_MySql("SELECT","",$qPrefijo,$xConexion01,"");
          $nFilCom  = mysql_num_rows($xPrefijo);
          if($nFilCom > 0){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Prefijo del Comprobante [{$_POST['cComPre']}] ya existe, \n";
          }
        }

        // Validando el Año
        if (!preg_match('/^[0-9]+$/', $_POST['cPerAno'])) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El A&ntilde;o debe ser numerico, \n";
        }

        // Validando el Año
        if ($_POST['cPerAno'] == '') {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El A&ntilde;o no puede ser vacio, \n";
        }
      }
      
      /* Inicio de Validaciones Parametrizacion de Consecutivos */
      // Validando el Consecutivo que sea tipo numerico
      if (!preg_match('/^[0-9]+$/', $_POST['cCscInicial'])) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Consecutivo Inicial debe ser numerico, \n";
      }

      $ConIni = "";
      $ConTip = "";
      switch ($_POST['rBtCt']) {
        case "MANUAL":
          // Validando el consecutivo sea igual a 1 en el tipo de consecutivo MANUAL
          $ConIni = 1 ;
        break;
        case "AUTOMATICO":
          if ($_POST['vChMensu'] != "") {
            if ($_POST['cCscInicial'] == "") {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Numero Inicial para el Consecutivo Automatico Mensual no puede ser vacio, \n";
            } else {
              $ConTip = $_POST['vChMensu'];
              $ConIni = $_POST['cCscInicial'];
            }
          }

          if ($_POST['vChAnual'] != "") {
            if ($_POST['cCscInicial'] == "") {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Numero Inicial para el Consecutivo Automatico Anual no puede ser vacio, \n";
            } else {
              $ConTip = $_POST['vChAnual'];
              $ConIni = $_POST['cCscInicial'];
            }
          }

          if ($_POST['vChIndef'] != "") {
            if ($_POST['cCscInicial'] == "") {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Numero Inicial para el Consecutivo Automatico Indefinido no puede ser vacio, \n";
            } else {
              $ConTip = $_POST['vChIndef'];
              $ConIni = $_POST['cCscInicial'];
            }
          }
        break;
        default:
          $ConIni = 1;
          $ConTip = "";
        break;
      }
    break;
    case "CAMBIAESTADO":
      //No hace nada
    break;
    case "BORRAR":
      // Valida si el comprobante ya tiene movimiento
      $cRespuesta = fnExisteMovimientoComprobante($_POST['cComId'], $_POST['cComCod']);

      if ($cRespuesta == "SI") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Comprobante ya Cuenta con Movimiento, No se Puede Borrar, \n";
      }
    break;
    default:
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Modo de Grabado Vacio.\n";
    break;
  }
  // Fin de la Validacion

  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "NUEVO":
        /**
         * Insertando en la Tabla lpar0117.
         */
        $qInsert	= array(array('NAME'=> 'comidxxx','VALUE' => trim(strtoupper($_POST['cComId']))           ,'CHECK' => 'SI'),     //Id del comprobante  
                          array('NAME' => 'comcodxx','VALUE' => trim(strtoupper($_POST['cComCod']))         ,'CHECK' => 'SI'),     //Codigo del comprobante  
                          array('NAME' => 'comdesxx','VALUE' => trim(strtoupper($_POST['cComDes']))         ,'CHECK' => 'SI'),     //Descripcion del comprobante  
                          array('NAME' => 'comtcoxx','VALUE' => trim(strtoupper($_POST['rBtCt']))           ,'CHECK' => 'SI'),     //Tipo de consecutivo  
                          array('NAME' => 'comccoxx','VALUE' => trim(strtoupper($ConTip))       						,'CHECK' => 'NO'),     //Control de consecutivo  
                          array('NAME' => 'comprexx','VALUE' => trim(strtoupper($_POST['cComPre']))       	,'CHECK' => 'SI'),     //Prefijo de consecutivo  
                          array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))        ,'CHECK' => 'SI'),     //Usuario que creo el registro  
                          array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')												        ,'CHECK' => 'SI'),     //Fecha de creacion  
                          array('NAME' => 'reghcrex','VALUE' => date('H:i:s')		                            ,'CHECK' => 'SI'),     //Hora de creacion  
                          array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')												        ,'CHECK' => 'SI'),     //Fecha de modificacion  
                          array('NAME' => 'reghmodx','VALUE' => date('H:i:s')		                            ,'CHECK' => 'SI'),     //Hora de modificacion  
                          array('NAME' => 'regestxx','VALUE' => trim(strtoupper($_POST['cEstado']))         ,'CHECK' => 'SI'));    //Estado  

        if (f_MySql("INSERT","lpar0117",$qInsert,$xConexion01,$cAlfa)) {

          // SE CREA EL PRIMER PERIODO PARA ESTE COMPROBANTE
          $qInsertPerCom = array(array('NAME'=>'comidxxx','VALUE' => trim(strtoupper($_POST['cComId']))		    ,'CHECK' => 'SI'),   //Id del comprobante   
                                array('NAME' => 'comcodxx','VALUE' => trim(strtoupper($_POST['cComCod']))     ,'CHECK' => 'SI'),   //Codigo del comprobante  
                                array('NAME' => 'peranoxx','VALUE' => trim(strtoupper(date('Y')))  						,'CHECK' => 'SI'),   //Año del periodo
                                array('NAME' => 'permesxx','VALUE' => trim(strtoupper(date('m')))			        ,'CHECK' => 'SI'),   //Mes del periodo
                                array('NAME' => 'comcscxx','VALUE' => trim($ConIni)                      			,'CHECK' => 'SI'),   //Consecutivo Uno
                                array('NAME' => 'comcsc2x','VALUE' => trim(strtoupper('1'))                   ,'CHECK' => 'SI'),   //Consecutivo Dos
                                array('NAME' => 'combanxx','VALUE' => trim(strtoupper('0'))                  	,'CHECK' => 'SI'),   //Ultimo periodo aperturado
                                array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))    ,'CHECK' => 'SI'),   //Usuario que Creo el registro
                                array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')												    ,'CHECK' => 'SI'),   //Fecha de creacion
                                array('NAME' => 'reghcrex','VALUE' => date('H:i:s')		                        ,'CHECK' => 'SI'),   //Hora de creacion
                                array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')												    ,'CHECK' => 'SI'),   //Fecha de modificacion
                                array('NAME' => 'reghmodx','VALUE' => date('H:i:s')		                        ,'CHECK' => 'SI'),   //Hora de modificacion
                                array('NAME' => 'regestxx','VALUE' => trim(strtoupper('ABIERTO'))             ,'CHECK' => 'SI'));  //Estado

            if (!f_MySql("INSERT","lpar0122",$qInsertPerCom,$xConexion01,$cAlfa)) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "Error No se Pudo Insertar el Periodo.\n";
            }
        } else {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error Guardando Datos de la Comprobantes.\n";
        }
      break;
      case "EDITAR":
        // echo "<pre>";
        // print_r($_POST);
        $qUpdate = array(array('NAME' => 'comdesxx','VALUE' => trim(strtoupper($_POST['cComDes']))   ,'CHECK' => 'SI'),     //Descripcion del comprobante  
                         array('NAME' => 'comtcoxx','VALUE' => trim(strtoupper($_POST['rBtCt']))     ,'CHECK' => 'SI'),     //Tipo de consecutivo
                         array('NAME' => 'comccoxx','VALUE' => trim(strtoupper($ConTip))             ,'CHECK' => 'NO'),     //Control de consecutivo  
                         array('NAME' => 'comprexx','VALUE' => trim(strtoupper($_POST['cComPre']))   ,'CHECK' => 'SI'),     //Prefijo de consecutivo  
                         array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))  ,'CHECK' => 'SI'),
                         array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                         ,'CHECK' => 'SI'),
                         array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                         ,'CHECK' => 'SI'),
                         array('NAME' => 'comidxxx','VALUE' => trim(strtoupper($_POST['cComIdEdit'])),'CHECK' => 'WH'),
                         array('NAME' => 'comcodxx','VALUE' => trim(strtoupper($_POST['cComCod']))   ,'CHECK' => 'WH'));

        if (!f_MySql("UPDATE","lpar0117",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al Actualizar Datos.\n";
        }
      break;
      case "CAMBIAESTADO":
        if($_POST['cCliEst']=="ACTIVO"){
          $cEstado="INACTIVO";
        }
        if($_POST['cCliEst']=="INACTIVO"){
          $cEstado="ACTIVO";
        }

        $zInsertCab	= array(array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))  ,'CHECK'=>'SI'),      //Usuario que edito el Registro   
                            array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')												  ,'CHECK'=>'SI'),      //Fecha de Modificacion del Registro  
                            array('NAME' => 'reghmodx','VALUE' => date('H:i:s')		                      ,'CHECK'=>'SI'),      //Hora de Modificacion del Registro  
                            array('NAME' => 'regestxx','VALUE' => $cEstado                              ,'CHECK'=>'SI'),      //Estado del Registro  
                            array('NAME' => 'comidxxx','VALUE' => trim(strtoupper($_POST['cComId']))    ,'CHECK'=>'WH'),      //Id del comprobante
                            array('NAME' => 'comcodxx','VALUE' => trim(strtoupper($_POST['cComCod']))   ,'CHECK'=>'WH'));     //Código del comprobante

        if (!f_MySql("UPDATE","lpar0117",$zInsertCab,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al Actualizar el Registro.\n";
        }
      break;
      case "BORRAR":
        $qDelete = array(array('NAME' => 'comidxxx','VALUE' => trim(strtoupper($_POST['cComId']))           ,'CHECK'=>'WH'),    //Id del comprobante
                         array('NAME' => 'comcodxx','VALUE' => trim(strtoupper($_POST['cComCod']))          ,'CHECK'=>'WH'));   //Código del comprobante
        if (f_MySql("DELETE","lpar0117",$qDelete,$xConexion01,$cAlfa)) {
          $qDeleteConse = array(array('NAME' => 'comidxxx','VALUE'  => trim(strtoupper($_POST['cComId']))   ,'CHECK'=>'WH'),    
                                array('NAME' => 'comcodxx','VALUE' => trim(strtoupper($_POST['cComCod']))   ,'CHECK'=>'WH'));
          if (!f_MySql("DELETE","lpar0122",$qDeleteConse,$xConexion01,$cAlfa)) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error al Eliminar el Registro.\n";
          }
        } else {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al Eliminar el Registro.\n";
        }
      break;
    }
  }

  if ($nSwitch == 0) {
    if($_COOKIE['kModo'] == "NUEVO"){
      f_Mensaje(__FILE__,__LINE__,"El Registro se Cargo con Exito.");
    } elseif ($_COOKIE['kModo'] == "EDITAR") {
      f_Mensaje(__FILE__,__LINE__,"El Registro se Actualizo con Exito.");
    } elseif ($_COOKIE['kModo'] == "CAMBIAESTADO") {
      f_Mensaje(__FILE__,__LINE__,"El Registro Cambio de Estado con Exito.");
    } elseif ($_COOKIE['kModo'] == "BORRAR") {
      f_Mensaje(__FILE__,__LINE__,"Se Elimino el Registro con Exito.");
    }
    ?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
    <script languaje = "javascript">
      parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
      document.forms['frgrm'].submit()
    </script>
  <?php } else {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
  }

  /**
   * Permite validar si el comprobante a editar o eliminar ya cuenta con movimiento en las tablas principales.
   */
  function fnExisteMovimientoComprobante($cComId, $cComCod) {
    global $cAlfa; global $xConexion01; global $vSysStr;

    // Valida si el año de instalacion del modulo es menor al año actual, para consultar sobre los dos ultimos años
    $cAnioAnt = ((date('Y') - 1) < $vSysStr['logistica_ano_instalacion_modulo']) ? $vSysStr['logistica_ano_instalacion_modulo'] : date('Y') - 1;
    
    $cExiste = "NO";
    // Valida si el comprobante a eliminar ya tiene movimiento contable
    switch ($cComId) {
      case 'M':
        // Recorre los dos ultimos años
        for ($cAnio=$cAnioAnt;$cAnio<=date('Y');$cAnio++) {
          // Consulta la tabla de cabecera de la MIF
          $qMifCab  = "SELECT ";
          $qMifCab .= "$cAlfa.lmca$cAnio.mifidxxx ";
          $qMifCab .= "FROM $cAlfa.lmca$cAnio ";
          $qMifCab .= "WHERE ";
          $qMifCab .= "$cAlfa.lmca$cAnio.comidxxx = \"$cComId\" AND ";
          $qMifCab .= "$cAlfa.lmca$cAnio.comcodxx = \"$cComCod\" ";
          $xMifCab  = f_MySql("SELECT","",$qMifCab,$xConexion01,"");
          if (mysql_num_rows($xMifCab) > 0) {
            $cExiste = "SI";
          } 
        }
      break;
      case 'C':
        // Recorre los dos ultimos años
        for ($cAnio=$cAnioAnt;$cAnio<=date('Y');$cAnio++) {
          // Consulta la tabla de cabecera de la Certificacion
          $qCertifiCab  = "SELECT ";
          $qCertifiCab .= "$cAlfa.lcca$cAnio.ceridxxx ";
          $qCertifiCab .= "FROM $cAlfa.lcca$cAnio ";
          $qCertifiCab .= "WHERE ";
          $qCertifiCab .= "$cAlfa.lcca$cAnio.comidxxx = \"$cComId\" AND ";
          $qCertifiCab .= "$cAlfa.lcca$cAnio.comcodxx = \"$cComCod\" ";
          $xCertifiCab  = f_MySql("SELECT","",$qCertifiCab,$xConexion01,"");
          if (mysql_num_rows($xCertifiCab) > 0) {
            $cExiste = "SI";
          } 
        }
      break;
      default:
        // No hace nada
      break;
    }

    return $cExiste;
  }
?>
