<?php
/**
 * Tracking  Facturacion .
 * Este programa permite realizar consultas de las Facturas que se Encuentran en la Base de Datos.
 * @author
 * @package openComex
 *
 * Johana Arboleda Ramos 2016-01-21 16:20
 * Se incluyo en las funciones que imprimen reportes o realizan transmisiones un proceso intermedio
 * para validar que la factura este bien guardada antes de transmitirla o imprimir el reporte
 * para esto se llama el archivo frfacval.php y si todo esta bien se ejecuta la accion solicitada
 * Este archivo recibe como paramtro el tipo que indica que funcion es la que debe ejecutar
 */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  include("../../../../libs/php/utility.php");
  include("../../../../../config/config.php");
  include("../../../../libs/php/uticones.php");
  include("../../../../libs/php/uticoval.php");

  $kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlDb = $kDf[3];

  ##Proceso oculto para verificar que las facturas anuladas o borradas desmarcaron todos los pagos
  $ObjVal = new cValidaComprobantes();
  $mRetorna = $ObjVal->fnValidaFacturaAnulada();

  $cPerAno = date('Y');

  // Busco el centro de costo del usuario.
  $qUsrSuc = "SELECT SUCIDXXX FROM $cAlfa.SIAI0003 WHERE USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1";
  $xUsrSuc = f_MySql("SELECT","",$qUsrSuc,$xConexion01,"");
  $vUsrSuc = mysql_fetch_array($xUsrSuc);
  $qCcoId = "SELECT ccoidxxx FROM $cAlfa.fpar0008 WHERE sucidxxx = \"{$vUsrSuc['SUCIDXXX']}\" LIMIT 0,1";
  $xCcoId = f_MySql("SELECT","",$qCcoId,$xConexion01,"");
  $mCcoId = mysql_fetch_array($xCcoId); $gUsrCco = $mCcoId['ccoidxxx'];

  /* Busco en la 05 que Tiene Permiso el Usuario*/
  $qUsrMen  = "SELECT * ";
  $qUsrMen .= "FROM $cAlfa.sys00005 ";
  $qUsrMen .= "WHERE ";
  $qUsrMen .= "sys00005.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
  $qUsrMen .= "sys00005.proidxxx = \"{$_COOKIE['kProId']}\" AND ";
  $qUsrMen .= "sys00005.menimgon != \"\" ";
  if (f_InList($kDf[3], "TEALPOPULP", "TEALPOPULX", "ALPOPULX") && $vSysStr['alpopular_activar_seven_facturacion'] != "SI") {
    $qUsrMen .= "AND ";
    $qUsrMen .= "menidxxx != \"1\" AND ";
    $qUsrMen .= "menidxxx != \"75\" ";
  }
  $qUsrMen .= "ORDER BY sys00005.menordxx";
  $xUsrMen = f_MySql("SELECT","",$qUsrMen,$xConexion01,"");
?>
<html>
  <head>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    <script language="javascript">

      function f_Ver(xComId,xComCod,xComCsc,xComCsc2,xRegFCre) {
        var cPathUrl = "frfacnue.php?gComId="+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gRegFCre='+xRegFCre;
        document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie="kMenDes=Ver Factura;path="+"/";
        document.cookie="kModo=VER;path="+"/";
        parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
        document.location = cPathUrl; // Invoco el menu.
      }

      function f_Borrar(xModo) {
        switch (document.forms['frgrm']['vRecords'].value) {
          case "1":
            if (document.forms['frgrm']['oChkCom'].checked == true) {
              var mComDat = document.forms['frgrm']['oChkCom'].id.split("~");
              if('<?php echo $kDf[3] ?>' == 'ALPOPULX' || '<?php echo $kDf[3] ?>' == 'TEALPOPULX' || '<?php echo $kDf[3] ?>' == 'DEALPOPULP' ||
                 '<?php echo $kDf[3] ?>' == 'ALMACAFE' || '<?php echo $kDf[3] ?>' == 'TEALMACAFE' || '<?php echo $kDf[3] ?>' == 'DEALMACAFE' ||
                 '<?php echo $kDf[3] ?>' == 'ALMAVIVA' || '<?php echo $kDf[3] ?>' == 'TEALMAVIVA' || 
                 '<?php echo $kDf[3] ?>' == 'DSVSASXX' || '<?php echo $kDf[3] ?>' == 'TEDSVSASXX' || '<?php echo $kDf[3] ?>' == 'DEDSVSASXX') {
                //No hace nada, deja el estado que tiene el registro
              } else {
                mComDat[8] = "PENDIENTE";
              }

              if (mComDat[5] == "PROVISIONAL") {
                mComDat[9] = "ABIERTO";
              }

              //Para alpopular solo se permite borrar el documento si el estado es:
              //[TRANS-XML-PROVISIONAL]
              var nPermiteBorrar = 0;
              if('<?php echo $kDf[3] ?>' == 'ALPOPULX' || '<?php echo $kDf[3] ?>' == 'DEALPOPULX' || '<?php echo $kDf[3] ?>' == 'TEALPOPULP') {
                //Estado Dos Seven TRANS-XML
                //Estado Dos SAP PENDIENTE
                if ((mComDat[8] == "TRANS-XML" || mComDat[8] == "PENDIENTE") && mComDat[5] == "PROVISIONAL") {
                  nPermiteBorrar = 1;
                }
              } else {
                if ((mComDat[5] == "ACTIVO" || mComDat[5] == "PROVISIONAL") && mComDat[8] == "PENDIENTE" && mComDat[7] == "") {
                  nPermiteBorrar = 1;
                }
              }

              if (mComDat[9] == "ABIERTO") {
                if (nPermiteBorrar == 1) {
                  if (confirm("Esta Seguro de Borrar el Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
                    var cPathUrl = "frfacnue.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4];
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.cookie="kMenDes=Borrar Comprobante;path="+"/";
                    document.cookie="kModo="+xModo+";path="+"/";
                    parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
                    document.location = cPathUrl; // Invoco el menu.
                  }
                } else {
                  switch ("<?php echo $kDf[3] ?>") {
                    case "ALPOPULX":
                    case "DEALPOPULX":
                    case "TEALPOPULP":
                      if (mComDat[8] == "TRANS-XML") {
                        alert("El Sistema Permite Borrar Facturas Unicamente en Estados [TRANS-XML - PROVISIONAL].");
                      } else {
                        alert("El Sistema Permite Borrar Facturas Unicamente en Estados [ACTIVO - PROVISIONAL].");
                      }                      
                    break;
                    case "ALMAVIVA":
                    case "TEALMAVIVA":
                      alert("El Sistema Permite Borrar Facturas Unicamente en Estados [ACTIVO o PROVISIONAL] y [PENDIENTE], y con Indicador de Paso de Factura en Estado [NULO], Verifique.");
                    break;
                    case "ALMACAFE":
                    case "TEALMACAFE":
                    case "DEALMACAFE":
                      alert("El Sistema Permite Borrar Facturas Unicamente en Estados [ACTIVO] y [PENDIENTE], Verifique.");
                    break;
                    case "DSVSASXX":
                    case "TEDSVSASXX":
                    case "DEDSVSASXX":
                      alert("El Sistema Permite Borrar Facturas Unicamente en Estado [PENDIENTE - PROVISIONAL], Verifique.");
                    break;
                    default:
                      alert("El Sistema Permite Borrar Facturas Unicamente en Estado [ACTIVO o PROVISIONAL], Verifique.");
                    break;
                  }
                }
              } else {
                alert("El Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] Pertenece a un Periodo que no esta [ABIERTO] por tal Motivo no se Puede Borrar, Verifique.");
              }
            }
          break;
          default:
            var nSw_Prv = 0;
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
              if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0) {
                // Solo Deja Legalizar el Primero Seleccionado
                nSw_Prv = 1;
                var mComDat = document.forms['frgrm']['oChkCom'][i].id.split("~");
                if('<?php echo $kDf[3] ?>' == 'ALPOPULX' || '<?php echo $kDf[3] ?>' == 'TEALPOPULX' || '<?php echo $kDf[3] ?>' == 'DEALPOPULP' ||
                   '<?php echo $kDf[3] ?>' == 'ALMACAFE' || '<?php echo $kDf[3] ?>' == 'TEALMACAFE' || '<?php echo $kDf[3] ?>' == 'DEALMACAFE' ||
                   '<?php echo $kDf[3] ?>' == 'ALMAVIVA' || '<?php echo $kDf[3] ?>' == 'TEALMAVIVA' || 
                   '<?php echo $kDf[3] ?>' == 'DSVSASXX' || '<?php echo $kDf[3] ?>' == 'TEDSVSASXX' || '<?php echo $kDf[3] ?>' == 'DEDSVSASXX') {
                  //No hace nada, deja el estado que tiene el registro
                } else {
                  mComDat[8] = "PENDIENTE";
                }

                if (mComDat[5] == "PROVISIONAL") {
                  mComDat[9] = "ABIERTO";
                }

                //Para alpopular solo se permite borrar el documento si el estado es:
                //[TRANS-XML-PROVISIONAL]
                var nPermiteBorrar = 0;
                if('<?php echo $kDf[3] ?>' == 'ALPOPULX'   || '<?php echo $kDf[3] ?>' == 'DEALPOPULX' || '<?php echo $kDf[3] ?>' == 'TEALPOPULP') {
                  //Estado Dos Seven TRANS-XML
                  //Estado Dos SAP PENDIENTE
                  if ((mComDat[8] == "TRANS-XML" || mComDat[8] == "PENDIENTE") && mComDat[5] == "PROVISIONAL") {
                    nPermiteBorrar = 1;
                  }
                } else {
                  if ((mComDat[5] == "ACTIVO" || mComDat[5] == "PROVISIONAL") && mComDat[8] == "PENDIENTE" && mComDat[7] == "") {
                    nPermiteBorrar = 1;
                  }
                }

                if (mComDat[9] == "ABIERTO") {
                  if (nPermiteBorrar == 1) {
                    if (confirm("Esta Seguro de Borrar el Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
                      var cPathUrl = "frfacnue.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4];
                      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                      document.cookie="kMenDes=Borrar Comprobante;path="+"/";
                      document.cookie="kModo="+xModo+";path="+"/";
                      parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
                      document.location = cPathUrl; // Invoco el menu.
                    }
                  } else {
                    switch ("<?php echo $kDf[3] ?>") {
                      case "ALPOPULX":
                      case "DEALPOPULX":
                      case "TEALPOPULP":
                        if (mComDat[8] == "TRANS-XML") {
                          alert("El Sistema Permite Borrar Facturas Unicamente en Estados [TRANS-XML - PROVISIONAL].");
                        } else {
                          alert("El Sistema Permite Borrar Facturas Unicamente en Estados [ACTIVO - PROVISIONAL].");
                        } 
                      break;
                      case "ALMAVIVA":
                      case "TEALMAVIVA":
                        alert("El Sistema Permite Borrar Facturas Unicamente en Estados [ACTIVO o PROVISIONAL] y [PENDIENTE], y con Indicador de Paso de Factura en Estado [NULO], Verifique.");
                      break;
                      case "ALMACAFE":
                      case "TEALMACAFE":
                      case "DEALMACAFE":
                        alert("El Sistema Permite Borrar Facturas Unicamente en Estados [ACTIVO] y [PENDIENTE], Verifique.");
                      break;
                      case "DSVSASXX":
                      case "TEDSVSASXX":
                      case "DEDSVSASXX":
                        alert("El Sistema Permite Borrar Facturas Unicamente en Estado [PENDIENTE - PROVISIONAL], Verifique.");
                      break;
                      default:
                        alert("El Sistema Permite Borrar Facturas Unicamente en Estado [ACTIVO o PROVISIONAL], Verifique.");
                      break;
                    }
                  }
                } else {
                  alert("El Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] Pertenece a un Periodo que no esta [ABIERTO] por tal Motivo no se Puede Borrar, Verifique.");
                }
              }
            }
          break;
        }
      }

      function f_Cambia_Estado(xModo) {
        if (document.forms['frgrm']['vRecords'].value != "0"){
          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oChkCom'].checked == true) {
                var mComDat = document.forms['frgrm']['oChkCom'].id.split("~");
                if('<?php echo $cAlfa ?>' == 'ALPOPULX' || '<?php echo $cAlfa ?>' == 'TEALPOPULP' || '<?php echo $cAlfa ?>' == 'DEALPOPULX' ||
                   '<?php echo $cAlfa ?>' == 'ALMAVIVA' || '<?php echo $cAlfa ?>' == 'TEALMAVIVA'){
                  mComDat[9] = "ABIERTO";
                }

                //Para ALPOPULAR y ALMAVIVA se llaman archivos para anulacion de factura
                //Diferentes a la de las demas agencias cuando el arcivo ya esta CONTABILIZADO
                //Si la factura esta en estado PROVISIONAL se anula el comprobante como se hace con las demas agencias
                switch ('<?php echo $cAlfa ?>') {
                  case "ALPOPULX":
                  case "TEALPOPULP":
                    //Si la factura es tipo PEDIDOSAP se va por proceso normal, no se crea nota credito
                    //Se envia estado ACTIVO, CONTABILIZADO y periodo ABIERTO, las validaciones de si se puede o inactivar
                    //se aplican en el graba
                    if (mComDat[11] == "PEDIDOSAP") {
                      var cRutaAnular = "franufac.php";
                      var nWidth      = 400;
                      var nHeight     = 200;

                      mComDat[9] = "ABIERTO";
                    } else {
                      var cRutaAnular = "franffrm.php";
                      var nWidth      = 560;
                      var nHeight     = 250;
                    }
                  break;
                  case "ALMAVIVA":
                  case "TEALMAVIVA":
                    //Si la factura es tipo PEDIDOSAP se va por proceso normal, no se crea nota credito
                    //Se envia estado ACTIVO, CONTABILIZADO y periodo ABIERTO, las validaciones de si se puede o inactivar
                    //se aplican en el graba
                    if (mComDat[11] == "PEDIDOSAP") {
                      var cRutaAnular = "franufac.php";
                      var nWidth      = 400;
                      var nHeight     = 200;

                      mComDat[5] = "ACTIVO";
                      mComDat[8] = "CONTABILIZADO";
                      mComDat[9] = "ABIERTO";
                    } else {
                      var cRutaAnular = "franunue.php";
                      var nWidth      = 400;
                      var nHeight     = 170;
                    }
                  break;
                  default:
                    var cRutaAnular = "franufac.php"; //Archivo invocado para las demas agencias
                    var nWidth      = 400;
                    var nHeight     = 200;
                  break;
                }

                //Los comprobantes en estado PROVISIONAL se pueden anular si ninguna restriccion
                if (mComDat[5] == "PROVISIONAL") {
                  if('<?php echo $cAlfa ?>' == 'ALPOPULX' || '<?php echo $cAlfa ?>' == 'TEALPOPULP' || '<?php echo $cAlfa ?>' == 'DEALPOPULX' ||
                     '<?php echo $cAlfa ?>' == 'ALMAVIVA' || '<?php echo $cAlfa ?>' == 'TEALMAVIVA'){
                    cRutaAnular = "franufac.php"; //Archivo invocado para las demas agencias
                  }
                  mComDat[5] = "ACTIVO";
                  mComDat[8] = "CONTABILIZADO";
                  mComDat[9] = "ABIERTO";
                }

                //Para alpoular se implemento la opcion de al anular una factura se cree la nota credito automaticamente
                //para esto la pregunta se personalizo
                var cPregunta = "Esta Seguro de Cambiar el Estado del Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?";
                var cError    = "Solo se Pueden Cambiar de Estado Comprobantes en Estado [ACTIVO] y [CONTABILIZADO], Verifique.";
                if(('<?php echo $cAlfa ?>' == 'ALPOPULX' || '<?php echo $cAlfa ?>' == 'TEALPOPULP') && mComDat[11] != "PEDIDOSAP") {
                  if (mComDat[5] == "ACTIVO") {
                    cPregunta = "Esta Seguro de Generar la Nota Credito de Anulacion del Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?";
                    cError    = "Solo se Pueden Generar la Nota Credito de Anulacion a Comprobantes en Estado [ACTIVO] y [CONTABILIZADO], Verifique.";
                  }
                }

                //Para las facturas de alpopular realizadas por Pedido SAP:
                //Solo se permite anular aquellas que su estado dos sea ACTIVO, es decir que no tiene registrado ningun evento
                if(('<?php echo $cAlfa ?>' == 'ALPOPULX' || '<?php echo $cAlfa ?>' == 'TEALPOPULP') && mComDat[11] == "PEDIDOSAP") {
                  if (mComDat[8] == "ACTIVO" || mComDat[8] == "FACTURADO") {
                    mComDat[5] = "ACTIVO";
                    mComDat[8] = "CONTABILIZADO";
                    mComDat[9] = "ABIERTO";
                  } else {
                    cError    = "Solo se Pueden Cambiar de Estado Comprobantes en Estado [ACTIVO-ACTIVO] o [FACTURADO-ACTIVO], Verifique.";
                  }
                }

                if (mComDat[9] == "ABIERTO") {
                  if (mComDat[5] == "ACTIVO" && mComDat[8] == "CONTABILIZADO") {
                    if (confirm(cPregunta)) {
                      if('<?php echo $cAlfa ?>' == 'ALPOPULX' || '<?php echo $cAlfa ?>' == 'TEALPOPULP' || '<?php echo $cAlfa ?>' == 'DEALPOPULX' ||
                         '<?php echo $cAlfa ?>' == 'ALMAVIVA' || '<?php echo $cAlfa ?>' == 'TEALMAVIVA'){
                        var cPathUrl = cRutaAnular+"?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5]+"&gModo="+xModo+"&gTipVen=SI";
                      } else {
                        var cPathUrl = cRutaAnular+"?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5];
                      }
                      var nX    = screen.width;
                      var nY    = screen.height;
                      var nNx      = (nX-nWidth)/2;
                      var nNy      = (nY-nHeight)/2;
                      var cWinOpt  = "width="+nWidth+",scrollbars=1,height="+nHeight+",left="+nNx+",top="+nNy;
                      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                      document.cookie="kModo="+xModo+";path="+"/";
                      cWindow = window.open(cPathUrl,'cWinCam',cWinOpt);
                      cWindow.focus();
                    }
                  } else {
                    alert(cError);
                  }
                } else {
                  alert("El Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] Pertenece a un Periodo que no esta [ABIERTO] por tal Motivo no se Puede Anular, Verifique.");
                }
              }
            break;
            default:
              var nSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
                if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0) {
                  var mComDat = document.forms['frgrm']['oChkCom'][i].id.split("~");
                  if('<?php echo $cAlfa ?>' == 'ALPOPULX' || '<?php echo $cAlfa ?>' == 'TEALPOPULP'  || '<?php echo $cAlfa ?>' == 'DEALPOPULP' ||
                     '<?php echo $cAlfa ?>' == 'ALMAVIVA' || '<?php echo $cAlfa ?>' == 'TEALMAVIVA'){
                    mComDat[9] = "ABIERTO";
                  }

                  //Para ALPOPULAR y ALMAVIVA se llaman archivos para anulacion de factura
                  //Diferentes a la de las demas agencias cuando el arcivo ya esta CONTABILIZADO
                  //Si la factura esta en estado PROVISIONAL se anula el comprobante como se hace con las demas agencias
                  switch ('<?php echo $cAlfa ?>') {
                    case "ALPOPULX":
                    case "TEALPOPULP":
                      //Si la factura es tipo PEDIDOSAP se va por proceso normal, no se crea nota credito
                      //Se envia estado ACTIVO, CONTABILIZADO y periodo ABIERTO, las validaciones de si se puede o inactivar
                      //se aplican en el graba
                      if (mComDat[11] == "PEDIDOSAP") {
                        var cRutaAnular = "franufac.php";
                        var nWidth      = 400;
                        var nHeight     = 200;

                        mComDat[9] = "ABIERTO";
                      } else {
                        var cRutaAnular = "franffrm.php";
                        var nWidth      = 560;
                        var nHeight     = 250;
                      }
                    break;
                    case "ALMAVIVA":
                    case "TEALMAVIVA":
                      //Si la factura es tipo PEDIDOSAP se va por proceso normal, no se crea nota credito
                      //Se envia estado ACTIVO, CONTABILIZADO y periodo ABIERTO, las validaciones de si se puede o inactivar
                      //se aplican en el graba
                      if (mComDat[11] == "PEDIDOSAP") {
                        var cRutaAnular = "franufac.php";
                        var nWidth      = 400;
                        var nHeight     = 200;

                        mComDat[5] = "ACTIVO";
                        mComDat[8] = "CONTABILIZADO";
                        mComDat[9] = "ABIERTO";
                      } else {
                        var cRutaAnular = "franunue.php";
                        var nWidth      = 400;
                        var nHeight     = 170;
                      }                      
                    break;
                    default:
                      var cRutaAnular = "franufac.php"; //Archivo invocado para las demas agencias
                      var nWidth      = 400;
                      var nHeight     = 200;
                    break;
                  }

                  //Los comprobantes en estado PROVISIONAL se pueden anular si ninguna restriccion
                  if (mComDat[5] == "PROVISIONAL") {
                    if('<?php echo $cAlfa ?>' == 'ALPOPULX' || '<?php echo $cAlfa ?>' == 'TEALPOPULP' || '<?php echo $cAlfa ?>' == 'DEALPOPULX' ||
                       '<?php echo $cAlfa ?>' == 'ALMAVIVA' || '<?php echo $cAlfa ?>' == 'TEALMAVIVA'){
                      cRutaAnular = "franufac.php"; //Archivo invocado para las demas agencias
                    }
                    mComDat[5] = "ACTIVO";
                    mComDat[8] = "CONTABILIZADO";
                    mComDat[9] = "ABIERTO";
                  }

                  //Para alpoular se implemento la opcion de al anular una factura se cree la nota credito automaticamente
                  //para esto la pregunta se personalizo
                  var cPregunta = "Esta Seguro de Cambiar el Estado del Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?";
                  var cError    = "Solo se Pueden Cambiar de Estado Comprobantes en Estado [ACTIVO] y [CONTABILIZADO], Verifique.";
                  if(('<?php echo $cAlfa ?>' == 'ALPOPULX' || '<?php echo $cAlfa ?>' == 'TEALPOPULP') && mComDat[11] != "PEDIDOSAP") {
                    if (mComDat[5] == "ACTIVO") {
                      cPregunta = "Esta Seguro de Generar la Nota Credito de Anulacion del Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?";
                      cError    = "Solo se Pueden Generar la Nota Credito de Anulacion a Comprobantes en Estado [ACTIVO] y [CONTABILIZADO], Verifique.";
                    }
                  }

                  //Para las facturas de alpopular realizadas por Pedido SAP:
                  //Solo se permite anular aquellas que su estado dos sea ACTIVO, es decir que no tiene registrado ningun evento
                  if(('<?php echo $cAlfa ?>' == 'ALPOPULX' || '<?php echo $cAlfa ?>' == 'TEALPOPULP') && mComDat[11] == "PEDIDOSAP") {
                    if (mComDat[8] == "ACTIVO" || mComDat[8] == "FACTURADO") {
                      mComDat[5] = "ACTIVO";
                      mComDat[8] = "CONTABILIZADO";
                      mComDat[9] = "ABIERTO";
                    } else {
                      cError    = "Solo se Pueden Cambiar de Estado Comprobantes en Estado [ACTIVO-ACTIVO] o [FACTURADO-ACTIVO], Verifique.";
                    }
                  }

                  if (mComDat[9] == "ABIERTO") {
                    if (mComDat[5] == "ACTIVO" && mComDat[8] == "CONTABILIZADO") {
                      if (confirm(cPregunta)) {
                        nSw_Prv = 1;
                        if('<?php echo $cAlfa ?>' == 'ALPOPULX' || '<?php echo $cAlfa ?>' == 'TEALPOPULP' || '<?php echo $cAlfa ?>' == 'DEALPOPULX' ||
                           '<?php echo $cAlfa ?>' == 'ALMAVIVA' || '<?php echo $cAlfa ?>' == 'TEALMAVIVA'){
                          var cPathUrl = cRutaAnular+"?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5]+"&gModo="+xModo+"&gTipVen=SI";
                        } else {
                          var cPathUrl = cRutaAnular+"?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5];
                        }
                        var nX    = screen.width;
                        var nY    = screen.height;
                        var nNx      = (nX-nWidth)/2;
                        var nNy      = (nY-nHeight)/2;
                        var cWinOpt  = "width="+nWidth+",scrollbars=1,height="+nHeight+",left="+nNx+",top="+nNy;
                        document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                        document.cookie="kModo="+xModo+";path="+"/";
                        cWindow = window.open(cPathUrl,'cWinCam',cWinOpt);
                        cWindow.focus();
                      }
                    } else {
                      alert(cError);
                    }
                  } else {
                    alert("El Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] Pertenece a un Periodo que no esta [ABIERTO] por tal Motivo no se Puede Anular, Verifique.");
                  }
                }
              }
            break;
          }
        }
      }

      function f_Activar_Factura(xModo) {
        if (document.forms['frgrm']['vRecords'].value != "0"){
          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oChkCom'].checked == true) {
                var mComDat = document.forms['frgrm']['oChkCom'].id.split("~");

                if (mComDat[9] == "ABIERTO") {
                  if (mComDat[5] == "INACTIVO" && mComDat[8] == "CONTABILIZADO") {
                    if (confirm("Esta Seguro de Cambiar el Estado del Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
                      var cPathUrl = "franufac.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5];
                      var nX    = screen.width;
                      var nY    = screen.height;
                      var nNx      = (nX-400)/2;
                      var nNy      = (nY-170)/2;
                      var cWinOpt  = "width=400,scrollbars=1,height=170,left="+nNx+",top="+nNy;
                      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                      document.cookie="kModo="+xModo+";path="+"/";
                      cWindow = window.open(cPathUrl,'cWinCam',cWinOpt);
                      cWindow.focus();
                    }
                  } else {
                    alert("Solo se Pueden Cambiar de Estado Comprobantes en Estado [INACTIVO] y [CONTABILIZADO], Verifique.");
                  }
                } else {
                  alert("El Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] Pertenece a un Periodo que no esta [ABIERTO] por tal Motivo no se Puede Anular, Verifique.");
                }
              }
            break;
            default:
              var nSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
                if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0) {
                  var mComDat = document.forms['frgrm']['oChkCom'][i].id.split("~");

                  if (mComDat[9] == "ABIERTO") {
                    if (mComDat[5] == "INACTIVO" && mComDat[8] == "CONTABILIZADO") {
                      if (confirm("Esta Seguro de Cambiar el Estado del Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
                        nSw_Prv = 1;
                        var cPathUrl = "franufac.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5];
                        var nX    = screen.width;
                        var nY    = screen.height;
                        var nNx      = (nX-400)/2;
                        var nNy      = (nY-170)/2;
                        var cWinOpt  = "width=400,scrollbars=1,height=170,left="+nNx+",top="+nNy;
                        document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                        document.cookie="kModo="+xModo+";path="+"/";
                        cWindow = window.open(cPathUrl,'cWinCam',cWinOpt);
                        cWindow.focus();
                      }
                    } else {
                      alert("Solo se Pueden Cambiar de Estado Comprobantes en Estado [INACTIVO] y [CONTABILIZADO], Verifique.");
                    }
                  } else {
                    alert("El Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] Pertenece a un Periodo que no esta [ABIERTO] por tal Motivo no se Puede Anular, Verifique.");
                  }
                }
              }
            break;
          }
        }
      }

      function f_Contabilizar_Factura(xModo) {

        if('<?php echo $cAlfa ?>' == 'ALPOPULX' || '<?php echo $cAlfa ?>' == 'TEALPOPULP' || '<?php echo $cAlfa ?>' == 'DEALPOPULX'){
          var cRuta = 'frfacwsa.php';
        } else {
          var cRuta = 'frfacwal.php';
        }

        if (document.forms['frgrm']['vRecords'].value != "0"){
          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oChkCom'].checked == true) {
                var mComDat = document.forms['frgrm']['oChkCom'].id.split("~");
                if (mComDat[5] == "ACTIVO") {
                  if (confirm("Esta Seguro de Contabilizar el Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
                    cRuta = "frfacval.php?gTipo=CONTABILIZARALPO&gNomArc="+cRuta+"&gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5]+"&gComIpf="+mComDat[7];
                    parent.fmpro.location = cRuta; // Invoco el menu.
                  }
                } else {
                  alert("Solo se Pueden Contabilizar Comprobantes en Estado [ACTIVO], Verifique.");
                }
              }
            break;
            default:
              var nSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
                if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0) {
                  var mComDat = document.forms['frgrm']['oChkCom'][i].id.split("~");
                  if (mComDat[5] == "ACTIVO") {
                    if (confirm("Esta Seguro de Contabilizar el Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
                      nSw_Prv = 1;
                      cRuta = "frfacval.php?gTipo=CONTABILIZARALPO&gNomArc="+cRuta+"&gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5]+"&gComIpf="+mComDat[7];
                      parent.fmpro.location = cRuta; // Invoco el menu.
                    }
                  } else {
                    alert("Solo se Pueden Contabilizar Comprobantes en Estado [ACTIVO], Verifique.");
                  }
                }
              }
            break;
          }
        }
      }

      function fnWsPedidoSAP(xModo) {

        if('<?php echo $cAlfa ?>' == 'ALPOPULX' || '<?php echo $cAlfa ?>' == 'TEALPOPULP' || '<?php echo $cAlfa ?>' == 'DEALPOPULX'){
          var cRuta = 'frfacwsp.php';
        } else {
          var cRuta = 'frfacwap.php';
        }

        var cEstVal = ("<?php echo $vSysStr['system_activar_integracion_sap_almaviva'] ?>" == "SI") ? "PROVISIONAL" : "ACTIVO";

        if (document.forms['frgrm']['vRecords'].value != "0"){
          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oChkCom'].checked == true) {
                var mComDat = document.forms['frgrm']['oChkCom'].id.split("~");
                if (mComDat[5] == cEstVal) {
                  if (confirm("Esta Seguro de Enviar Pedido del Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
                    cRuta = "frfacval.php?gTipo=PEDIDOSAP&gNomArc="+cRuta+"&gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5]+"&gComIpf="+mComDat[7];
                    parent.fmpro.location = cRuta; // Invoco el menu.
                  }
                } else {
                  alert("Solo se Pueden Enviar los Pedidos a SAP de Comprobantes en Estado ["+cEstVal+"], Verifique.");
                }
              }
            break;
            default:
              var nSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
                if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0) {
                  var mComDat = document.forms['frgrm']['oChkCom'][i].id.split("~");
                  if (mComDat[5] == cEstVal) {
                    if (confirm("Esta Seguro de Enviar Pedido del Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
                      nSw_Prv = 1;
                      cRuta = "frfacval.php?gTipo=PEDIDOSAP&gNomArc="+cRuta+"&gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5]+"&gComIpf="+mComDat[7];
                      parent.fmpro.location = cRuta; // Invoco el menu.
                    }
                  } else {
                    alert("Solo se Pueden Enviar los Pedidos a SAP de Comprobantes en Estado ["+cEstVal+"], Verifique.");
                  }
                }
              }
            break;
          }
        }
      }

      function fnEnviarXMLaPT(xModo) {

        var cRuta = 'frfaxmla.php';

        if (document.forms['frgrm']['vRecords'].value != "0"){
          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oChkCom'].checked == true) {
                var mComDat = document.forms['frgrm']['oChkCom'].id.split("~");

                if ((mComDat[5] == "ACTIVO" && mComDat[8] == "TRANS-XML")|| (mComDat[5] == "ACTIVO" && mComDat[8] == "TRANS-ERROR")) {
                  if (confirm("Esta Seguro de Transmitir XML a PT del Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"] ?")) {
                    cRuta = "frfacval.php?gTipo=TRANSMITIRXMLPT&gNomArc="+cRuta+"&gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5]+"&gComIpf="+mComDat[7];
                    parent.fmpro.location = cRuta; // Invoco el menu.
                  }
                } else {
                  alert("Solo se Pueden Transmitir XML a PT de Comprobantes en Estado [TRANS-XML-ACTIVO] o [TRANS-ERROR-ACTIVO], Verifique.");
                }
              }
            break;
            default:
              var nSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
                if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0) {
                  var mComDat = document.forms['frgrm']['oChkCom'][i].id.split("~");
                  if ((mComDat[5] == "ACTIVO" && mComDat[8] == "TRANS-XML")|| (mComDat[5] == "ACTIVO" && mComDat[8] == "TRANS-ERROR")) {
                    if (confirm("Esta Seguro de Transmitir XML a PT del Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"] ?")) {
                      nSw_Prv = 1;
                      cRuta = "frfacval.php?gTipo=TRANSMITIRXMLPT&gNomArc="+cRuta+"&gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5]+"&gComIpf="+mComDat[7];
                      parent.fmpro.location = cRuta; // Invoco el menu.
                    }
                  } else {
                    alert("Solo se Pueden Transmitir XML a PT de Comprobantes en Estado [TRANS-XML-ACTIVO] o [TRANS-ERROR-ACTIVO], Verifique.");
                  }
                }
              }
            break;
          }
        }
      }

      function fnDescargarXML(xModo) {
        if (document.forms['frgrm']['vRecords'].value != "0"){
          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oChkCom'].checked == true) {
                var mComDat = document.forms['frgrm']['oChkCom'].id.split("~");
                cRuta = "frdesxml.php?gTipo=DESCARGARXML&gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5]+"&gComIpf="+mComDat[7];
                parent.fmpro.location = cRuta; // Invoco el menu.
              }
            break;
            default:
              var nSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
                if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0) {
                  var mComDat = document.forms['frgrm']['oChkCom'][i].id.split("~");
                  nSw_Prv = 1;
                  cRuta = "frdesxml.php?gTipo=DESCARGARXML&gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5]+"&gComIpf="+mComDat[7];
                  parent.fmpro.location = cRuta; // Invoco el menu.
                }
              }
            break;
          }
        }
      }

      function fnEnviarTXTaPT(xModo) {

        var cRuta = 'frfatxta.php';

        if (document.forms['frgrm']['vRecords'].value != "0"){
          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oChkCom'].checked == true) {
                var mComDat = document.forms['frgrm']['oChkCom'].id.split("~");
                if (mComDat[5] == "ACTIVO") {
                  if (confirm("Esta Seguro de Transmitir TXT a PT del Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"] ?")) {
                    cRuta = "frfacval.php?gTipo=TRANSMITIRTXTPT&gNomArc="+cRuta+"&gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5]+"&gComIpf="+mComDat[7];
                    parent.fmpro.location = cRuta; // Invoco el menu.
                  }
                } else {
                  alert("Solo se Pueden Transmitir TXT a PT de Comprobantes en Estado [ACTIVO], Verifique.");
                }
              }
            break;
            default:
              var nSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
                if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0) {
                  var mComDat = document.forms['frgrm']['oChkCom'][i].id.split("~");
                  if (mComDat[5] == "ACTIVO") {
                    if (confirm("Esta Seguro de Transmitir XML a PT del Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"] ?")) {
                      nSw_Prv = 1;
                      cRuta = "frfacval.php?gTipo=TRANSMITIRTXTPT&gNomArc="+cRuta+"&gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5]+"&gComIpf="+mComDat[7];
                      parent.fmpro.location = cRuta; // Invoco el menu.
                    }
                  } else {
                    alert("Solo se Pueden Transmitir TXT a PT de Comprobantes en Estado [ACTIVO], Verifique.");
                  }
                }
              }
            break;
          }
        }
      }

      function fnDescargarTXT(xModo) {
        if (document.forms['frgrm']['vRecords'].value != "0"){
          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oChkCom'].checked == true) {
                var mComDat = document.forms['frgrm']['oChkCom'].id.split("~");
                cRuta = "frdestxt.php?gTipo=DESCARGARTXT&gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5]+"&gComIpf="+mComDat[7];
                parent.fmpro.location = cRuta; // Invoco el menu.
              }
            break;
            default:
              var nSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
                if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0) {
                  var mComDat = document.forms['frgrm']['oChkCom'][i].id.split("~");
                  nSw_Prv = 1;
                  cRuta = "frdestxt.php?gTipo=DESCARGARTXT&gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5]+"&gComIpf="+mComDat[7];
                  parent.fmpro.location = cRuta; // Invoco el menu.
                }
              }
            break;
          }
        }
      }

      /*** Contablizar en SAP ***/
      function fnContabilizarSAP(xModo) {

        var cRuta = 'frfacwss.php';

        if (document.forms['frgrm']['vRecords'].value != "0"){
          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oChkCom'].checked == true) {
                var mComDat = document.forms['frgrm']['oChkCom'].id.split("~");
                if ( mComDat[5] == "ACTIVO" && mComDat[8] == "PENDIENTE") {
                  if (confirm("Esta Seguro de Contabilizar en SAP el Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
                    cRuta = "frfacval.php?gTipo=CONTABILIZARSAP&gNomArc="+cRuta+"&gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5]+"&gComIpf="+mComDat[7];
                    parent.fmpro.location = cRuta; // Invoco el menu.
                  }
                } else {
                  alert("Solo se Pueden Contabilizar Comprobantes en Estado [ACTIVO] y [PENDIENTE], Verifique.");
                }
              }
            break;
            default:
              var nSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
                if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0) {
                  nSw_Prv = 1;
                  var mComDat = document.forms['frgrm']['oChkCom'][i].id.split("~");
                  if ( mComDat[5] == "ACTIVO" && mComDat[8] == "PENDIENTE") {
                    if (confirm("Esta Seguro de Contabilizar en SAP el Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
                      cRuta = "frfacval.php?gTipo=CONTABILIZARSAP&gNomArc="+cRuta+"&gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5]+"&gComIpf="+mComDat[7];
                      parent.fmpro.location = cRuta; // Invoco el menu.
                    }
                  } else {
                    alert("Solo se Pueden Contabilizar Comprobantes en Estado [ACTIVO] y [PENDIENTE], Verifique.");
                  }
                }
              }
            break;
          }
        }
      }

      function f_Imprimir() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        var nSwitch = 0;
        /**
         * Archivo del PDF por cada base de datos
         * Alpopular no se inicializa porque tiene una validacion particular que se hace mas abajo
         */
        var cRuta = '';
        switch('<?php echo $kMysqlDb ?>'){
          case 'UPSXXXXX': case 'TEUPSXXXXX': case 'DEUPSXXXXX': cRuta = 'frupsprn.php';    break;
          case 'GRUPOGLA': case 'TEGRUPOGLA': case 'DEGRUPOGLA': cRuta = 'frglaprn.php';    break;
          case 'ADUACARX': case 'TEADUACARX': case 'DEADUACARX': cRuta = 'frfacprn.php';    break;
          case 'INTERLOG': case 'TEINTERLOG': case 'DEINTERLOG': cRuta = 'frfaiprn.php';    break;
          case 'ETRANSPT': case 'TEETRANSPT': case 'DEETRANSPT': cRuta = 'frdliprn.php';    break;
          case 'COLMASXX': case 'TECOLMASXX': case 'DECOLMASXX': cRuta = 'frcolprn.php';    break;
          case 'ADUANAMI': case 'TEADUANAMI': case 'DEADUANAMI': cRuta = 'fraduprn.php';    break;
          case 'INTERLO2': case 'TEINTERLO2': case 'DEINTERLO2': cRuta = 'frintprn.php';    break;
          case 'ADUANERA': case 'TEADUANERA': case 'DEADUANERA': cRuta = 'fradnprn.php';    break;
          case 'SIACOSIA': case 'DESIACOSIP': case 'TESIACOSIP': cRuta = 'frsiaprn1.php';   break;
          case 'MIRCANAX': case 'TEMIRCANAX': case 'DEMIRCANAX': cRuta = 'frmirprn.php';    break;
          case 'ADUANAMO': case 'TEADUANAMO': case 'DEADUANAMO': cRuta = 'framoprn.php';    break;
          case 'SUPPORTX': case 'TESUPPORTX': case 'DESUPPORTX': cRuta = 'frsupprn.php';    break;
          case 'ALPOPULX': case 'TEALPOPULP': case 'DEALPOPULX': cRuta = '';                break;
          case 'LIDERESX': case 'TELIDERESX': case 'DELIDERESX': cRuta = 'frlidprn.php';    break;
          // case 'ACODEXXX': case 'TEACODEXXX': case 'DEACODEXXX': cRuta = 'fracdprn.php';    break;
          case 'ACODEXXX': case 'TEACODEXXX': case 'DEACODEXXX': cRuta = 'fracnprn.php';    break;
          case 'LOGISTSA': case 'TELOGISTSA': case 'DELOGISTSA': cRuta = 'frloiprn.php';    break;
          case 'LOGINCAR': case 'TELOGINCAR': case 'DELOGINCAR': cRuta = 'frlogprn.php';    break;
          case 'TRLXXXXX': case 'TETRLXXXXX': case 'DETRLXXXXX': cRuta = 'frbmaprn.php';    break;
          case 'ADIMPEXX': case 'TEADIMPEXX': case 'DEADIMPEXX': cRuta = 'fradiprn.php';    break;
          case 'ROLDANLO': case 'TEROLDANLO': case 'DEROLDANLO': cRuta = 'frrolprn.php';    break;
          case 'ALMAVIVA': case 'TEALMAVIVA': case 'DEALMAVIVA': cRuta = 'fralmprn.php';    break;
          case 'CASTANOX': case 'TECASTANOX': case 'DECASTANOX': cRuta = 'frcasprn.php';    break;
          case 'ALMACAFE': case 'TEALMACAFE': case 'DEALMACAFE': cRuta = 'fralcprn.php';    break;
          case 'CARGOADU': case 'TECARGOADU': case 'DECARGOADU': cRuta = 'frcadprn.php';    break;
          case 'GRUPOALC': case 'TEGRUPOALC': case 'DEGRUPOALC': cRuta = 'frgalprn.php';    break;
          case 'GRUMALCO': case 'TEGRUMALCO': case 'DEGRUMALCO': cRuta = 'frmalprn.php';    break;
          case 'ANDINOSX': case 'TEANDINOSX': case 'DEANDINOSX': cRuta = 'frandprn.php';    break;
          case 'AAINTERX': case 'TEAAINTERX': case 'DEAAINTERX': cRuta = 'frainprn.php';    break;
          case 'AALOPEZX': case 'TEAALOPEZX': case 'DEAALOPEZX': cRuta = 'frlopprn.php';    break;
          case 'ADUAMARX': case 'TEADUAMARX': case 'DEADUAMARX': cRuta = 'frmarprn.php';    break;
          case 'OPENEBCO': case 'TEOPENEBCO': case 'DEOPENEBCO': cRuta = 'fropeprn.php';    break;
          case 'SOLUCION': case 'TESOLUCION': case 'DESOLUCION': cRuta = 'frsolprn.php';    break;
          case 'FENIXSAS': case 'TEFENIXSAS': case 'DEFENIXSAS': cRuta = 'frfenprn.php';    break;
          case 'INTERLAC': case 'TEINTERLAC': case 'DEINTERLAC': cRuta = 'frterprn.php';    break;
          case 'COLVANXX': case 'TECOLVANXX': case 'DECOLVANXX': cRuta = 'frcovprn.php';    break;
          case 'DHLEXPRE': case 'TEDHLEXPRE': case 'DEDHLEXPRE': cRuta = 'frdhlprn.php';    break;
					case 'KARGORUX': case 'TEKARGORUX': case 'DEKARGORUX': cRuta = 'frkarprn.php';    break;
					case 'PROSERCO': case 'TEPROSERCO': case 'DEPROSERCO': cRuta = 'frproprn.php';    break;
					case 'MANATIAL': case 'TEMANATIAL': case 'DEMANATIAL': cRuta = 'frmanprn.php';    break;
          case 'DSVSASXX': case 'TEDSVSASXX': case 'DEDSVSASXX': cRuta = 'frdsvprn.php';    break;
          case 'FEDEXEXP': case 'DEFEDEXEXP': case 'TEFEDEXEXP': cRuta = 'frfedprn.php';    break;
          case 'EXPORCOM': case 'DEEXPORCOM': case 'TEEXPORCOM': cRuta = 'frexpprn.php';    break;
          case 'HAYDEARX': case 'DEHAYDEARX': case 'TEHAYDEARX': cRuta = 'frhayprn.php';    break;
          case 'ALADUANA': case 'TEALADUANA': case 'DEALADUANA':
            if (confirm("Imprimir PDF con formato estandar")) {
              if (confirm("Imprimir Factura con Retenciones?")) {
                cRuta = 'fralaprn.php&gRetenciones=SI';
              } else {
                cRuta = 'fralaprn.php&gRetenciones=NO';
              }
            }else{
              if (confirm("Imprimir Factura con Retenciones?")) {
                cRuta = 'fralapr2.php&gRetenciones=SI';
              } else {
                cRuta = 'fralapr2.php&gRetenciones=NO';
              }
            }
          break;
          default: cRuta = 'frfacprn.php'; break;
        }

        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
            var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId  = docun[0];
            var cComCod = docun[1];
            var cComCsc = docun[2];
            var cComCsc2= docun[3];
            var dRegFCre= docun[4];
            var cTerId  = docun[6];
            var cEstado = docun[8];
            var dComFec = docun[10];
            var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';

            switch('<?php echo $kMysqlDb ?>'){
              case 'ALPOPULX': case 'TEALPOPULP': case 'DEALPOPULX':
                if(cEstado == 'CONTABILIZADO'){
                  if(cTerId == 'NORMAL'){
                    cRuta = 'frfalprn.php';
                  } else {
                    cRuta = 'frdptprn.php';
                  }
                } else {
                  nSwitch = 1;
                  alert("Usted no puede imprimir la Factura hasta que no haya sido CONTABILIZADA en Seven");
                }
              break;
              default: /*No hace nada porque ya inicializo el archivo arriba*/ break;
            }
          }
        } else {
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints = '|';
            var nRegSel = 0;
            var nRegPen = 0;  //Registros pendiente aplica para ALPOPULAR
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
              if (document.forms['frgrm']['oChkCom'][i].checked == true){
                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];
                var cTerId  = docun[6];
                var cEstado = docun[8];
                var dComFec = docun[10];
                nRegSel++;

                if(cEstado == 'CONTABILIZADO'){
                  prints += cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
                } else {
                  nRegPen++;
                  switch('<?php echo $kMysqlDb ?>'){
                    case 'ALPOPULX': case 'TEALPOPULP': case 'DEALPOPULX':
                      nSwitch = 1;
                      alert('No puede Imprimir la Factura '+cComId+'-'+cComCod+'-'+cComCsc+ ' hasta que no haya sido CONTABILIZADA en Seven');
                    break;
                    default:
                      prints += cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
                    break;
                  }
                }
              }
            }

            if(nRegPen > 0) {
              cEstado = (nRegSel == nRegPen) ? 'PENDIENTE' : 'CONTABILIZADO';
            } else {
              cEstado = 'CONTABILIZADO';
            }

            switch('<?php echo $kMysqlDb ?>'){
              case 'ALPOPULX': case 'TEALPOPULP': case 'DEALPOPULX':
                if(cEstado == 'CONTABILIZADO'){
                  if(cTerId == 'NORMAL'){
                    cRuta = 'frfalprn.php';
                  } else {
                    cRuta = 'frdptprn.php';
                  }
                }
              break;
              default: /*No hace nada porque ya inicializo el archivo arriba*/ break;
            }
          }
        }
        cRuta = 'frfacval.php?gTipo=IMPRIMIR&gMenDes=Imprimir%20Factura&gModo=IMPRIMIR&gNomArc='+cRuta+'&prints='+prints;
        parent.fmpro.location = cRuta; // Invoco el menu.
      }

      function f_Imprimir2() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        var nSwitch = 0;
        var cRuta = 'frglapr2.php';

        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
            var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId  = docun[0];
            var cComCod = docun[1];
            var cComCsc = docun[2];
            var cComCsc2= docun[3];
            var dRegFCre= docun[4];
            var cTerId  = docun[6];
            var cEstado = docun[8];
            var dComFec = docun[10];
            var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
          }
        } else {
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints = '|';
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
              if (document.forms['frgrm']['oChkCom'][i].checked == true){
                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];
                var cTerId  = docun[6];
                var cEstado = docun[8];
                var dComFec = docun[10];
                var zX      = screen.width;
                var zY      = screen.height;
                var alto    = zY-80;
                var ancho   = zX-100;
                var zNx     = (zX-ancho)/2;
                var zNy     = (zY-alto)/2;
                var zWinPro = 'width='+ancho+',scrollbars=1,height='+alto+',left='+zNx+',top=0';
                prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
              }
            }
          }
        }
        cRuta = 'frfacval.php?gTipo=IMPRIMIR2&gMenDes=Imprimir%20Factura%20Formato%20Anterior&gModo=IMPRIMIR2&gNomArc='+cRuta+'&prints='+prints;
        parent.fmpro.location = cRuta; // Invoco el menu.
      }

      function f_Anexo_Factura() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion

        var cRuta = 'frataprn.php';

        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
            var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId  = docun[0];
            var cComCod = docun[1];
            var cComCsc = docun[2];
            var cComCsc2= docun[3];
            var dRegFCre= docun[4];
            var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';
          }
        }else{
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints = '|';
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
              //if (document.frgrm.vCheck[i].checked == true){
              if (document.forms['frgrm']['oChkCom'][i].checked == true){
                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];
                prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';
              }
            }
          }
        }
        cRuta = 'frfacval.php?gTipo=ANEXOFACTURA&gMenDes=Imprimir%20Anexo&gModo=ANEXOFACTURA&gNomArc='+cRuta+'&prints='+prints;
        parent.fmpro.location = cRuta; // Invoco el menu.
      }

      function f_Anexo_Factura_Excel() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion

        var cRuta = 'frataexc.php';

        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
            var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId  = docun[0];
            var cComCod = docun[1];
            var cComCsc = docun[2];
            var cComCsc2= docun[3];
            var dRegFCre= docun[4];
            var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';
          }
        }else{
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints = '|';
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
              //if (document.frgrm.vCheck[i].checked == true){
              if (document.forms['frgrm']['oChkCom'][i].checked == true){
                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];
                prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';
              }
            }
          }
        }
        cRuta = 'frfacval.php?gTipo=ANEXOFACTURAXLS&gNomArc='+cRuta+'&prints='+prints;
        parent.fmpro.location = cRuta; // Invoco el menu.
      }

      function fnReporteFacturaExcel() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion

        var nSel = 0;

        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
            var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId  = docun[0];
            var cComCod = docun[1];
            var cComCsc = docun[2];
            var cComCsc2= docun[3];
            var dRegFCre= docun[4]; //Es la misma fecha del comprobante
            var cTerId  = docun[6];
            var cEstado = docun[8];
            var dComFec = docun[10];
            var prints = cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec;

            nSel = 1;
          }
        } else {
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints  = '';
            var nSw_Prv = 0;
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
              if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0){
                nSw_Prv = 1;
                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];  //Es la misma fecha del comprobante
                var cTerId  = docun[6];
                var cEstado = docun[8];
                var dComFec = docun[10];
                prints += cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec;
                nSel = 1;
              }
            }
          }
        }

        if (nSel == 1) {
          cRuta = 'frrfaexc.php?prints='+prints;
          parent.fmpro.location = cRuta; // Invoco el menu.
        } else {
          alert("Debe Seleccionar una Factura.");
        }

      }

      function f_Prefactura() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion

        var cRuta = '';

        var kMysqlDb = '<?php echo $kMysqlDb ?>';
        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
            var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId  = docun[0];
            var cComCod = docun[1];
            var cComCsc = docun[2];
            var cComCsc2= docun[3];
            var dRegFCre= docun[4];
            var cTerId  = docun[6];
            var cEstado = docun[8];
            var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';
            switch('<?php echo $kMysqlDb ?>'){
              case 'ALPOPULX':
              case 'TEALPOPULP':
                if(cTerId == 'NORMAL'){
                  cRuta = 'frprefac.php';
                } else {
                  cRuta = 'frpredpt.php';
                }
              break;
              default:
                if(cTerId == 'NORMAL'){
                  cRuta = 'frprefac.php';
                }else{
                  cRuta = 'frpredpt.php';
                }
              break;
            }
          }
        } else {
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints = '|';
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
              if (document.forms['frgrm']['oChkCom'][i].checked == true){
                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];
                var cTerId  = docun[6];
                var cEstado = docun[8];
                prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';
              }
            }
            switch('<?php echo $kMysqlDb ?>'){
              case 'ALPOPULX':
              case 'TEALPOPULP':
                if(cTerId == 'NORMAL'){
                  cRuta = 'frprefac.php';
                } else {
                  cRuta = 'frpredpt.php';
                }
              break;
              default:
                if(cTerId == 'NORMAL'){
                  cRuta = 'frprefac.php';
                } else {
                  cRuta = 'frpredpt.php';
                }
              break;
            }
          }
        }
        cRuta = 'frfacval.php?gTipo=IMPRIMIR&gMenDes=Imprimir%20Prefactura&gModo=IMPRIMIR&gNomArc='+cRuta+'&prints='+prints;
        parent.fmpro.location = cRuta; // Invoco el menu.
      }

      function f_Certificado_PCC(xOpcion) { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        var cRuta = 'frcerprn.php';

        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {

            var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId  = docun[0];
            var cComCod = docun[1];
            var cComCsc = docun[2];
            var cComCsc2= docun[3];
            var dRegFCre= docun[4];
            var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';
          }
        }else{
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints = '|';
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
              if (document.forms['frgrm']['oChkCom'][i].checked == true){

                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];
                prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';
              }
            }
          }
        }

        var gFirma = "NO";
        if ('<?php echo $cAlfa ?>' == 'DEALMAVIVA' || '<?php echo $cAlfa ?>' == 'TEALMAVIVA' || '<?php echo $cAlfa ?>' == 'ALMAVIVA') {
          if (confirm("Imprimir Firma Revisor Fiscal?")) {
            gFirma = 'SI';
          } else {
            gFirma = 'NO';
          }
        }

        cRuta = 'frfacval.php?gTipo='+xOpcion+'&gMenDes=Imprimir%20Certificado&gModo='+xOpcion+'&gNomArc='+cRuta+'&prints='+prints+'&gFirma='+gFirma;
        parent.fmpro.location = cRuta; // Invoco el menu.
      }

      function f_Xml_Ups() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion

        var cRuta = 'frxmlups.php';
        var nSw_Prv = 0;

        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
            var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId  = docun[0];
            var cComCod = docun[1];
            var cComCsc = docun[2];
            var cComCsc2= docun[3];
            var dRegFCre= docun[4];
            var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';
          }
        }else{
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints = '|';
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
              if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0){
                nSw_Prv = 1;
                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];
                prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';
              }
            }
          }
        }
        cRuta = 'frfacval.php?gTipo=TRANSMITIRUPS&gNomArc='+cRuta+'&prints='+prints;
        parent.fmpro.location = cRuta; // Invoco el menu.
      }

      //Se comenta opcion porque se desarrollo una tarea automatica para realizar este proceso
      /*function f_Contabilizar_Ups() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        var nSw_Prv = 0;
        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
            nSw_Prv = 1;
            var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId  = docun[0];
            var cComCod = docun[1];
            var cComCsc = docun[2];
            var cComCsc2= docun[3];
            var dRegFCre= docun[4];
          }
        }else{
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
              if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0){
                nSw_Prv = 1;
                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];
              }
            }
          }
        }
        if (nSw_Prv == 1) {
          var zX      = screen.width;
          var zY      = screen.height;
          var ancho   = 600;
          var alto    = 400;
          var zNx     = (zX-ancho)/2;
          var zNy     = (zY-alto)/2;
          var zWinPro = 'width='+ancho+',scrollbars=1,height='+alto+',left='+zNx+',top='+zNy;
          var cRuta = "frfacups.php?gComId="+cComId+
                      "&gComCod=" +cComCod +
                      "&gComCsc=" +cComCsc +
                      "&gComCsc2="+cComCsc2+
                      "&gRegFCre="+dRegFCre;
          zWin = window.open(cRuta,"cWinConta",zWinPro);
        } else {
          alert("Debe Seleccionar una Factura. Verifique.");
        }
      }*/

      function f_Legalizar_Provisionales(xOpcion) { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion

        var cRuta = 'frfacpro.php';

        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
            if (confirm("Esta Seguro Legalizar la Factura Provisional Seleccionada?")) {
              var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
              if (docun[5] == "PROVISIONAL") {
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];
                var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';
                cRuta = 'frfacval.php?gTipo='+xOpcion+'&gNomArc='+cRuta+'&prints='+prints;
                parent.fmpro.location = cRuta; // Invoco el menu.
              } else {
                alert('Solo Puede Legalizar Prefacturas en estado [PROVISIONAL], Verifique.');
              }
            }
          }
        }else{
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            if (confirm("Esta Seguro Legalizar la Factura(s) Provisional(es) Seleccionada(s)?")) {
              var prints = '|';
              var nEncontro = 0;
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
                if (document.forms['frgrm']['oChkCom'][i].checked == true){
                  var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                  if (docun[5] == "PROVISIONAL") {
                    var cComId  = docun[0];
                    var cComCod = docun[1];
                    var cComCsc = docun[2];
                    var cComCsc2= docun[3];
                    var dRegFCre= docun[4];
                    prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';
                  } else {
                    nEncontro = 1;
                  }
                }
              }

              if (nEncontro == 0) {
                cRuta = 'frfacval.php?gTipo='+xOpcion+'&gNomArc='+cRuta+'&prints='+prints;
                parent.fmpro.location = cRuta; // Invoco el menu.
              } else {
                alert('Solo Puede Legalizar Prefacturas en estado [PROVISIONAL], Verifique.');
              }
            }
          }
        }
      }

      function f_Fecha_Entrega() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        var zX  = screen.width;
        var zY  = screen.height;
        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
          //  if (confirm("Esta Seguro Legalizar la Factura Provisional Seleccionada?")) {
              var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
              var cComId  = docun[0];
              var cComCod = docun[1];
              var cComCsc = docun[2];
              var cComCsc2= docun[3];
              var dRegFCre= docun[4];
              var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';

              var zNx     = (zX-450)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=450,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var cRuta = 'frfeccli.php?prints='+prints;
              zWindow2    = window.open(cRuta,'zWindow2',zWinPro);
              zWindow2.focus();
            //}
          }
        }else{
          var zX  = screen.width;
          var zY  = screen.height;
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints = '|';
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
              if (document.forms['frgrm']['oChkCom'][i].checked == true){
                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];
                prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';
              }
            }

            var zNx     = (zX-450)/2;
            var zNy     = (zY-250)/2;
            var zWinPro = 'width=450,scrollbars=1,height=250,left='+zNx+',top='+zNy;
            var cRuta = 'frfeccli.php?prints='+prints;
            zWindow2    = window.open(cRuta,'zWindow2',zWinPro);
            zWindow2.focus();
          }
        }
      }

      function fnAsignarDisconformidad() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        var zX  = screen.width;
        var zY  = screen.height;
        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
            var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId  = docun[0];
            var cComCod = docun[1];
            var cComCsc = docun[2];
            var cComCsc2= docun[3];
            var dRegFCre= docun[4];
            var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';

            var zNx     = (zX-500)/2;
            var zNy     = (zY-250)/2;
            var zWinPro = 'width=500,scrollbars=1,height=250,left='+zNx+',top='+zNy;
            var cRuta = 'frdisfrm.php?prints='+prints;
            zWindow2    = window.open(cRuta,'zWindow2',zWinPro);
            zWindow2.focus();
          }
        }else{
          var zX  = screen.width;
          var zY  = screen.height;
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints = '|';
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
              if (document.forms['frgrm']['oChkCom'][i].checked == true){
                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];
                prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';
              }
            }
            var zNx     = (zX-500)/2;
            var zNy     = (zY-250)/2;
            var zWinPro = 'width=500,scrollbars=1,height=250,left='+zNx+',top='+zNy;
            var cRuta = 'frdisfrm.php?prints='+prints;
            zWindow2    = window.open(cRuta,'zWindow2',zWinPro);
            zWindow2.focus();
          }
        }
      }

      function f_Envia_Correo() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        var nSwitch = 0;
        var cRuta   = 'frsiaprn.php';

        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
            var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId  = docun[0];
            var cComCod = docun[1];
            var cComCsc = docun[2];
            var cComCsc2= docun[3];
            var dRegFCre= docun[4];
            var cTerId  = docun[6];
            var cEstado = docun[8];
            var dComFec = docun[10];
            var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
            cRuta = 'frfacval.php?gTipo=CORREO&gNomArc='+cRuta+'&prints='+prints;
            parent.fmpro.location = cRuta; // Invoco el menu.
          }
        } else {
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints = '|';
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
              if (document.forms['frgrm']['oChkCom'][i].checked == true){
                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];
                var cTerId  = docun[6];
                var cEstado = docun[8];
                var dComFec = docun[10];
                var zX      = screen.width;
                var zY      = screen.height;
                var alto    = zY-80;
                var ancho   = zX-100;
                var zNx     = (zX-ancho)/2;
                var zNy     = (zY-alto)/2;
                var zWinPro = 'width='+ancho+',scrollbars=1,height='+alto+',left='+zNx+',top=0';
                prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
                cRuta = 'frfacval.php?gTipo=CORREO&gNomArc='+cRuta+'&prints='+prints;
                parent.fmpro.location = cRuta; // Invoco el menu.
              }
            }
          }
        }
      }

      function fnEnviarCertificadoXCorreoMalco() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        var nSwitch = 0;
        var cRuta   = 'frmalcma.php';

        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
            var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId  = docun[0];
            var cComCod = docun[1];
            var cComCsc = docun[2];
            var cComCsc2= docun[3];
            var dRegFCre= docun[4];
            var cTerId  = docun[6];
            var cEstado = docun[8];
            var dComFec = docun[10];
            var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
            cRuta = 'frfacval.php?gTipo=CORREO&gNomArc='+cRuta+'&prints='+prints;
            parent.fmpro.location = cRuta; // Invoco el menu.
          }
        } else {
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints = '|';
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
              if (document.forms['frgrm']['oChkCom'][i].checked == true){
                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];
                var cTerId  = docun[6];
                var cEstado = docun[8];
                var dComFec = docun[10];
                prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
                cRuta = 'frfacval.php?gTipo=CORREO&gNomArc='+cRuta+'&prints='+prints;
                parent.fmpro.location = cRuta; // Invoco el menu.
              }
            }
          }
        }
      }

      function f_Ver_Factura() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        var nSwitch = 0;
        /**
         * Archivo del PDF por cada base de datos
         * Alpopular no se se le habilita la funcionalidad del ver facutura
         */
        var cRuta = '';
        switch('<?php echo $kMysqlDb ?>'){
          case 'UPSXXXXX': case 'TEUPSXXXXX': case 'DEUPSXXXXX': cRuta = 'frupsprn.php';    break;
          case 'GRUPOGLA': case 'TEGRUPOGLA': case 'DEGRUPOGLA': cRuta = 'frglapr2.php';    break;
          case 'ADUACARX': case 'TEADUACARX': case 'DEADUACARX': cRuta = 'frfacprn.php';    break;
          case 'INTERLOG': case 'TEINTERLOG': case 'DEINTERLOG': cRuta = 'frfaiprn.php';    break;
          case 'ETRANSPT': case 'TEETRANSPT': case 'DEETRANSPT': cRuta = 'frdliprn.php';    break;
          case 'COLMASXX': case 'TECOLMASXX': case 'DECOLMASXX': cRuta = 'frcolprn.php';    break;
          case 'ADUANAMI': case 'TEADUANAMI': case 'DEADUANAMI': cRuta = 'fraduprn.php';    break;
          case 'INTERLO2': case 'TEINTERLO2': case 'DEINTERLO2': cRuta = 'frintprn.php';    break;
          case 'ADUANERA': case 'TEADUANERA': case 'DEADUANERA': cRuta = 'fradnprn.php';    break;
          case 'SIACOSIA': case 'DESIACOSIP': case 'TESIACOSIP': cRuta = 'frsiaprn.php';    break;
          case 'MIRCANAX': case 'TEMIRCANAX': case 'DEMIRCANAX': cRuta = 'frmirprn.php';    break;
          case 'ADUANAMO': case 'TEADUANAMO': case 'DEADUANAMO': cRuta = 'framoprn.php';    break;
          case 'SUPPORTX': case 'TESUPPORTX': case 'DESUPPORTX': cRuta = 'frsupprn.php';    break;
          case 'ALPOPULX': case 'TEALPOPULP': case 'DEALPOPULX': cRuta = '';                break;
          case 'LIDERESX': case 'TELIDERESX': case 'DELIDERESX': cRuta = 'frlidprn.php';    break;
          // case 'ACODEXXX': case 'TEACODEXXX': case 'DEACODEXXX': cRuta = 'fracdprn.php';    break;
          case 'ACODEXXX': case 'TEACODEXXX': case 'DEACODEXXX': cRuta = 'fracnprn.php';    break;
          case 'LOGISTSA': case 'TELOGISTSA': case 'DELOGISTSA': cRuta = 'frloiprn.php';    break;
          case 'LOGINCAR': case 'TELOGINCAR': case 'DELOGINCAR': cRuta = 'frlogprn.php';    break;
          case 'TRLXXXXX': case 'TETRLXXXXX': case 'DETRLXXXXX': cRuta = 'frbmaprn.php';    break;
          case 'ADIMPEXX': case 'TEADIMPEXX': case 'DEADIMPEXX': cRuta = 'fradiprn.php';    break;
          case 'ROLDANLO': case 'TEROLDANLO': case 'DEROLDANLO': cRuta = 'frrolprn.php';    break;
          case 'ALMAVIVA': case 'TEALMAVIVA': case 'DEALMAVIVA': cRuta = 'fralmprn.php';    break;
          case 'CASTANOX': case 'TECASTANOX': case 'DECASTANOX': cRuta = 'frcasprn.php';    break;
          case 'ALMACAFE': case 'TEALMACAFE': case 'DEALMACAFE': cRuta = 'fralcprn.php';    break;
          case 'CARGOADU': case 'TECARGOADU': case 'DECARGOADU': cRuta = 'frcadprn.php';    break;
          case 'GRUPOALC': case 'TEGRUPOALC': case 'DEGRUPOALC': cRuta = 'frgalprn.php';    break;
          case 'GRUMALCO': case 'TEGRUMALCO': case 'DEGRUMALCO': cRuta = 'frmalprn.php';    break;
          case 'ANDINOSX': case 'TEANDINOSX': case 'DEANDINOSX': cRuta = 'frandprn.php';    break;
          case 'AAINTERX': case 'TEAAINTERX': case 'DEAAINTERX': cRuta = 'frainprn.php';    break;
          case 'AALOPEZX': case 'TEAALOPEZX': case 'DEAALOPEZX': cRuta = 'frlopprn.php';    break;
          case 'ADUAMARX': case 'TEADUAMARX': case 'DEADUAMARX': cRuta = 'frmarprn.php';    break;
          case 'OPENEBCO': case 'TEOPENEBCO': case 'DEOPENEBCO': cRuta = 'fropeprn.php';    break;
          case 'SOLUCION': case 'TESOLUCION': case 'DESOLUCION': cRuta = 'frsolprn.php';    break;
          case 'FENIXSAS': case 'TEFENIXSAS': case 'DEFENIXSAS': cRuta = 'frfenprn.php';    break;
          case 'INTERLAC': case 'TEINTERLAC': case 'DEINTERLAC': cRuta = 'frterprn.php';    break;
          case 'COLVANXX': case 'TECOLVANXX': case 'DECOLVANXX': cRuta = 'frcovprn.php';    break;
          case 'DHLEXPRE': case 'TEDHLEXPRE': case 'DEDHLEXPRE': cRuta = 'frdhlprn.php';    break;
					case 'KARGORUX': case 'TEKARGORUX': case 'DEKARGORUX': cRuta = 'frkarprn.php';    break;
					case 'PROSERCO': case 'TEPROSERCO': case 'DEPROSERCO': cRuta = 'frproprn.php';    break;
					case 'MANATIAL': case 'TEMANATIAL': case 'DEMANATIAL': cRuta = 'frmanprn.php';    break;
					case 'DSVSASXX': case 'TEDSVSASXX': case 'DEDSVSASXX': cRuta = 'frdsvprn.php';    break;
          case 'FEDEXEXP': case 'DEFEDEXEXP': case 'TEFEDEXEXP': cRuta = 'frfedprn.php';    break;
          case 'EXPORCOM': case 'DEEXPORCOM': case 'TEEXPORCOM': cRuta = 'frexpprn.php';    break; 
          case 'ALADUANA': case 'TEALADUANA': case 'DEALADUANA':
            if (confirm("Imprimir PDF con formato estandar")) {
              if (confirm("Imprimir Factura con Retenciones?")) {
                cRuta = 'fralaprn.php&gRetenciones=SI';
              } else {
                cRuta = 'fralaprn.php&gRetenciones=NO';
              }
            }else{
              if (confirm("Imprimir Factura con Retenciones?")) {
                cRuta = 'fralapr2.php&gRetenciones=SI';
              } else {
                cRuta = 'fralapr2.php&gRetenciones=NO';
              }
            }
          break;
          default: cRuta = 'frfacprn.php'; break;
        }

        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
            var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId  = docun[0];
            var cComCod = docun[1];
            var cComCsc = docun[2];
            var cComCsc2= docun[3];
            var dRegFCre= docun[4]; //Es la misma fecha del comprobante
            var cTerId  = docun[6];
            var cEstado = docun[8];
            var dComFec = docun[10];
            var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
          }
        } else {
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints = '|';
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
              if (document.forms['frgrm']['oChkCom'][i].checked == true){
                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];  //Es la misma fecha del comprobante
                var cTerId  = docun[6];
                var cEstado = docun[8];
                var dComFec = docun[10];
                prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
              }
            }
          }
        }

        if (cRuta != "") {
          cRuta = 'frfacval.php?gTipo=VERFACTURA&gMenDes=Ver%20Factura&gModo=VERFACTURA&gNomArc='+cRuta+'&prints='+prints;
          parent.fmpro.location = cRuta; // Invoco el menu.
        }
      }

      function fnReporteDiscriminadoGecolsa(xOpcion) { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        
        cRuta = 'frrdgprn.php';
        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
            var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId  = docun[0];
            var cComCod = docun[1];
            var cComCsc = docun[2];
            var cComCsc2= docun[3];
            var dRegFCre= docun[4]; //Es la misma fecha del comprobante
            var cTerId  = docun[6];
            var cEstado = docun[8];
            var dComFec = docun[10];
            var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
          }
        } else {
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints = '|';
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
              if (document.forms['frgrm']['oChkCom'][i].checked == true){
                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];  //Es la misma fecha del comprobante
                var cTerId  = docun[6];
                var cEstado = docun[8];
                var dComFec = docun[10];
                prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
              }
            }
          }
        }

        if (cRuta != "") {
          cRuta = 'frfacval.php?gTipo='+xOpcion+'&gMenDes=Imprimir%20Reporte%20Discriminado%20Gecolsa&gNomArc='+cRuta+'&prints='+prints;
          parent.fmpro.location = cRuta; // Invoco el menu.
        }
      }

      function f_Reporte_Lg(){
        var nSel = 0;

        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
            var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId  = docun[0];
            var cComCod = docun[1];
            var cComCsc = docun[2];
            var cComCsc2= docun[3];
            var dRegFCre= docun[4]; //Es la misma fecha del comprobante
            var cTerId  = docun[6];
            var cEstado = docun[8];
            var dComFec = docun[10];
            var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
            nSel = 1;
          }
        } else {
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints  = '';
            var nSw_Prv = 0;
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
              if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0){
                nSw_Prv = 1;
                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];  //Es la misma fecha del comprobante
                var cTerId  = docun[6];
                var cEstado = docun[8];
                var dComFec = docun[10];
                prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
                nSel = 1;
              }
            }
          }
        }

        if (nSel == 1) {
          var nX      = screen.width;
          var nY      = screen.height;
          var nNx     = (nX-500)/2;
          var nNy     = (nY-250)/2;
          var zWinPro = 'width=500,scrollbars=1,height=250,left='+nNx+',top='+nNy;
          cRuta = 'frfacrlg.php?prints='+prints;
          zWindow = window.open(cRuta,'zWindow',zWinPro);
          zWindow.focus();
        } else {
          alert("Debe Seleccionar una Factura.");
        }
      }

      function f_Certificado_PCC2(xOpcion) { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        var cRuta = 'frcalprn.php';

        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {

            var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId  = docun[0];
            var cComCod = docun[1];
            var cComCsc = docun[2];
            var cComCsc2= docun[3];
            var dRegFCre= docun[4];
            var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';
          }
        }else{
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints = '|';
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
              if (document.forms['frgrm']['oChkCom'][i].checked == true){

                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];
                prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';
              }
            }
          }
        }

        var gFirma = "NO";
        if (confirm("Imprimir en PDF con Formato Estandar?")) {
          gFirma = 'SI';
        } else {
          gFirma = 'NO';
        }
        cRuta = 'frfacval.php?gTipo='+xOpcion+'&gMenDes=Imprimir%20Certificado&gModo='+xOpcion+'&gNomArc='+cRuta+'&prints='+prints+'&gFirma='+gFirma;
        parent.fmpro.location = cRuta; // Invoco el menu.
      }

      function f_Belcorp(){ //Proyecto Belcorp
       var cCad = "";
       if(document.forms['frgrm']['vRecords'].value == 1){
         if(document.forms['frgrm']['oChkCom'].checked == true) {
           var cDocFac   = document.forms['frgrm']['oChkCom'].id.split('~');
           var cComId   = cDocFac[0];
           var cComCod  = cDocFac[1];
           var cComCsc  = cDocFac[2];
           var cComFec  = cDocFac[4];

           cCad += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComFec+'|';
         }
          var cPeriodos = document.forms['frgrm']['cPeriodos'].value;
          var cSearch2 = document.forms['frgrm']['vSearch'].value;
          var cDesde = document.forms['frgrm']['dDesde'].value;
          var cHasta = document.forms['frgrm']['dHasta'].value;
          var cPaginas = document.forms['frgrm']['vPaginas'].value;
          var cRuta = "frcvsadn.php?gSearch2="+cSearch2+'&gPeriodos='+cPeriodos+'&gDesde='+cDesde+'&gHasta='+cHasta+'&gPaginas='+cPaginas;
          document.forms['frgrm']['cCadFac'].value  = cCad;
          document.forms['frgrm'].target='fmpro';
          document.forms['frgrm'].action=cRuta;
          document.forms['frgrm'].submit();
          document.forms.frgrm.target='fmwork';
          document.forms.frgrm.action='frfacini.php';
        }else{
          if(document.forms['frgrm']['vRecords'].value > 1){
            for(i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
              if(document.forms['frgrm']['oChkCom'][i].checked == true) {
                var cDocFac   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId   = cDocFac[0];
                var cComCod  = cDocFac[1];
                var cComCsc  = cDocFac[2];
                var cComFec  = cDocFac[4];

                cCad += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComFec+'|';
              }
            }
          }
          var cRuta = 'frcvsadn.php';
          document.forms['frgrm']['cCadFac'].value  = cCad;
          document.forms['frgrm'].target='fmpro';
          document.forms['frgrm'].action=cRuta;
          document.forms['frgrm'].submit();
          document.forms.frgrm.target='fmwork';
          document.forms.frgrm.action='frfacini.php';
        }
      }

      function fnCertificadoMandato(xOpcion) {
        if(fnValidarSeleccion()){

          var cRuta = 'frcmaprn.php';

          if (document.forms['frgrm']['vRecords'].value == 1){
            if (document.forms['frgrm']['oChkCom'].checked == true) {

              var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
              var cComId  = docun[0];
              var cComCod = docun[1];
              var cComCsc = docun[2];
              var cComCsc2= docun[3];
              var dRegFCre= docun[4]; //Es la misma fecha del comprobante
              var cTerId  = docun[6];
              var cEstado = docun[8];
              var dComFec = docun[10];
              var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
            }
          }else{
            if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
              var prints = '|';
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
                if (document.forms['frgrm']['oChkCom'][i].checked == true){

                  var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                  var cComId  = docun[0];
                  var cComCod = docun[1];
                  var cComCsc = docun[2];
                  var cComCsc2= docun[3];
                  var dRegFCre= docun[4];  //Es la misma fecha del comprobante
                  var cTerId  = docun[6];
                  var cEstado = docun[8];
                  var dComFec = docun[10];
                  prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
                }
              }
            }
          }

          cRuta = 'frfacval.php?gTipo='+xOpcion+'&gMenDes=Certificado%20Mandato&gModo='+xOpcion+'&gNomArc='+cRuta+'&prints='+prints;
          parent.fmpro.location = cRuta; // Invoco el menu.
        }
      }

      function fnValidarSeleccion() {
				var nSeleccion = 0;
				var nSwitch = 0;
			 	if (document.forms['frgrm']['vRecords'].value == 1){
					if (document.forms['frgrm']['oChkCom'].checked == true) {
				 		nSeleccion = 1;
					}
			 	} else {
				 	if (document.forms['frgrm']['vRecords'].value > 1){
					 	for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
							if (document.forms['frgrm']['oChkCom'][i].checked == true) {
								nSeleccion = 1;
							}
					 	}
				 	}
			 	}
				if (nSeleccion == 0) {
					alert("Debe Seleccionar Una Factura.");
					nSwitch = 1;
				}

				if(nSwitch == 0){
					return true;
				}else{
					return false;
				}
		 	}

      /************************ FUNCION PROPIAS DEL INI ***********************/
      function f_Link(xModId,xProId,xMenId,xForm,xOpcion,xMenDes) {
        document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie="kMenDes="+xMenDes+";path="+"/";
        document.cookie="kModo="+xOpcion+";path="+"/";
        parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
        document.location = xForm; // Invoco el menu.
      }

      function f_Marca() {
        if (document.forms['frgrm']['oChkComAll'].checked == true){
          if (document.forms['frgrm']['vRecords'].value == 1){
            document.forms['frgrm']['oChkCom'].checked=true;
          } else {
            if (document.forms['frgrm']['vRecords'].value > 1){
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
                document.forms['frgrm']['oChkCom'][i].checked = true;
              }
            }
          }
        } else {
          if (document.forms['frgrm']['vRecords'].value == 1){
            document.forms['frgrm']['oChkCom'].checked=false;
          } else {
            if (document.forms['frgrm']['vRecords'].value > 1){
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
                document.forms['frgrm']['oChkCom'][i].checked = false;
              }
            }
          }
        }
      }

      function fnConsolidarPrefacturas(cModo){
        var cComprobantes = "";
        if (document.forms['frgrm']['vRecords'].value > 0){
          if (document.forms['frgrm']['vRecords'].value == 1){
            if(document.forms['frgrm']['oChkCom'].checked == true){
              cComprobantes = document.forms['frgrm']['oChkCom'].id + "|";
            }
          }else{
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
              if(document.forms['frgrm']['oChkCom'][i].checked == true){
                cComprobantes += document.forms['frgrm']['oChkCom'][i].id + "|";
              }
            }
          }
        }
        document.forms['frconsol']['gComprobantes'].value = cComprobantes;
        if(document.forms['frconsol']['gComprobantes'].value != ""){
          document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
          document.cookie="kMenDes=Consolidar Prefacturas;path="+"/";
          document.cookie="kModo="+cModo+";path="+"/";
          parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
          document.forms['frconsol'].target = "fmwork";
          document.forms['frconsol'].submit();
        }
      }

      function fnHistoricoNotaCausal() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        var zX  = screen.width;
        var zY  = screen.height;
        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
          //  if (confirm("Esta Seguro Legalizar la Factura Provisional Seleccionada?")) {
              var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
              var cComId  = docun[0];
              var cComCod = docun[1];
              var cComCsc = docun[2];
              var cComCsc2= docun[3];
              var dRegFCre= docun[4];
              var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';

              var zNx     = (zX-480)/2;
              var zNy     = (zY-300)/2;
              var zWinPro = 'width=480,scrollbars=1,height=300,left='+zNx+',top='+zNy;
              var cRuta = 'frcauobs.php?prints='+prints;
              zWindow2    = window.open(cRuta,'zWindow2',zWinPro);
              zWindow2.focus();
            //}
          }
        }else{
          var zX  = screen.width;
          var zY  = screen.height;
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints = '|';
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
              if (document.forms['frgrm']['oChkCom'][i].checked == true){
                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];
                prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'|';
              }
            }

            var zNx     = (zX-480)/2;
            var zNy     = (zY-300)/2;
            var zWinPro = 'width=480,scrollbars=1,height=300,left='+zNx+',top='+zNy;
            var cRuta = 'frcauobs.php?prints='+prints;
            zWindow2    = window.open(cRuta,'zWindow2',zWinPro);
            zWindow2.focus();
          }
        }
      }

      /************************ FUNCION PARA GUARDAR EL ORDEN DEL ORDER BY DEL SQL ***********************/
      function f_Order_By(xEvento,xCampo) {
        //alert(document.forms['frgrm'][xCampo].value);
        if (document.forms['frgrm'][xCampo].value != '') {
          var vSwitch = document.forms['frgrm'][xCampo].value.split(' ');
          var cSwitch = vSwitch[1];
        } else {
          var cSwitch = '';
        }
        //alert(cSwitch);
        if (xEvento == 'onclick') {
          switch (cSwitch) {
            case '':
              document.forms['frgrm'][xCampo].value = document.forms['frgrm'][xCampo].id+' ASC,';
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
              if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
                document.forms['frgrm']['cOrderByOrder'].value += xCampo+"~";
              }
            break;
            case 'ASC,':
              document.forms['frgrm'][xCampo].value = document.forms['frgrm'][xCampo].id+' DESC,';
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
              if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
                document.forms['frgrm']['cOrderByOrder'].value += xCampo+"~";
              }
            break;
            case 'DESC,':
              document.forms['frgrm'][xCampo].value = '';
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/spacer.png';
              if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) >= 0) {
                document.forms['frgrm']['cOrderByOrder'].value = document.forms['frgrm']['cOrderByOrder'].value.replace(xCampo,"");
              }
            break;
          }
        } else {
          switch (cSwitch) {
            case '':
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/spacer.png';
            break;
            case 'ASC,':
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
            break;
            case 'DESC,':
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
            break;
          }
        }
      }

      function fnTransmitirOpenPCE(xModo) {
        if(document.forms['frgrm']['vRecords'].value == 1){
          if(document.forms['frgrm']['oChkCom'].checked == true) {
            var mComDat   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId   = mComDat[0];
            var cComCod  = mComDat[1];
            var cComCsc  = mComDat[2];
            var cComCsc2 = mComDat[3];
            var dRegFCre = mComDat[4];
            if (confirm("Esta Seguro de Transmitir a openETL el Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
              document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
              document.cookie="kModo="+xModo+";path="+"/";
              document.forms['frtraop']['cComId'].value   = cComId;
              document.forms['frtraop']['cComCod'].value  = cComCod;
              document.forms['frtraop']['cComCsc'].value  = cComCsc;
              document.forms['frtraop']['cComCsc2'].value = cComCsc2;
              document.forms['frtraop']['dRegFCre'].value = dRegFCre;
              document.forms['frtraop'].action= "frreopce.php";
              document.forms['frtraop'].submit();
            }
          }else{
            alert("Para esta opcion debe seleccionar un comprobante");
          }
        }else{
          if(document.forms['frgrm']['vRecords'].value > 1){
            var nActivos = 0;
            var elemento = null;
            for(i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
              if(document.forms['frgrm']['oChkCom'][i].checked == true ) {
                nActivos++;
                elemento = document.forms['frgrm']['oChkCom'][i];
              }
              if (nActivos > 1){
                break;
              }
            }
            if (nActivos == 1){
              var mComDat  = elemento.id.split('~');
              var cComId   = mComDat[0];
              var cComCod  = mComDat[1];
              var cComCsc  = mComDat[2];
              var cComCsc2 = mComDat[3];
              var dRegFCre = mComDat[4];
              if (confirm("Esta Seguro de Transmitir a openETL el Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
                document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                document.cookie="kModo="+xModo+";path="+"/";
                document.forms['frtraop']['cComId'].value   = cComId;
                document.forms['frtraop']['cComCod'].value  = cComCod;
                document.forms['frtraop']['cComCsc'].value  = cComCsc;
                document.forms['frtraop']['cComCsc2'].value = cComCsc2;
                document.forms['frtraop']['dRegFCre'].value = dRegFCre;
                document.forms['frtraop'].action= "frreopce.php";
                document.forms['frtraop'].submit();
              }
            }
            else if (nActivos == 0){
              alert("Para esta opcion debe seleccionar un comprobante");
            }
            else{
              alert("Para esta opcion solo se permite seleccionar un comprobante")
            }
          }
        }
      }

      function fnConsultarETL(xModo) {
       if(document.forms['frgrm']['vRecords'].value == 1){
         if(document.forms['frgrm']['oChkCom'].checked == true) {
            var mComDat   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId   = mComDat[0];
            var cComCod  = mComDat[1];
            var cComCsc  = mComDat[2];
            var cComCsc2 = mComDat[3];
            var dRegFCre = mComDat[4];
            document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'], (strrpos($_SERVER['PHP_SELF'], "/") + 1), strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
            document.cookie="kModo="+xModo+";path="+"/";
            document.forms['frtraop']['cComId'].value   = cComId;
            document.forms['frtraop']['cComCod'].value  = cComCod;
            document.forms['frtraop']['cComCsc'].value  = cComCsc;
            document.forms['frtraop']['cComCsc2'].value = cComCsc2;
            document.forms['frtraop']['dRegFCre'].value = dRegFCre;
            document.forms['frtraop'].action= "frreopce.php";
            document.forms['frtraop'].submit()
         }else{
            alert("Para esta opcion debe seleccionar un comprobante");
         }
        }else{
          if(document.forms['frgrm']['vRecords'].value > 1){
            var nActivos = 0;
            var elemento = null;
            for(i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
              if(document.forms['frgrm']['oChkCom'][i].checked == true ) {
                nActivos++;
                elemento = document.forms['frgrm']['oChkCom'][i];
              }
              if (nActivos > 1){
                break;
              }
            }
            if (nActivos == 1){
              var mComDat  = elemento.id.split('~');
              var cComId   = mComDat[0];
              var cComCod  = mComDat[1];
              var cComCsc  = mComDat[2];
              var cComCsc2 = mComDat[3];
              var dRegFCre = mComDat[4];
              document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'], (strrpos($_SERVER['PHP_SELF'], "/") + 1), strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
              document.cookie="kModo="+xModo+";path="+"/";
              document.forms['frtraop']['cComId'].value   = cComId;
              document.forms['frtraop']['cComCod'].value  = cComCod;
              document.forms['frtraop']['cComCsc'].value  = cComCsc;
              document.forms['frtraop']['cComCsc2'].value = cComCsc2;
              document.forms['frtraop']['dRegFCre'].value = dRegFCre;
              document.forms['frtraop'].action= "frreopce.php";
              document.forms['frtraop'].submit()
            }
            else if (nActivos == 0){
              alert("Para esta opcion debe seleccionar un comprobante");
            }
            else{
              alert("Para esta opcion solo se permite seleccionar un comprobante")
            }
          }
        }
      }

      function fnNoEnviarOpenPCE(xModo) {
       if(document.forms['frgrm']['vRecords'].value == 1){
         if(document.forms['frgrm']['oChkCom'].checked == true) {
            var mComDat   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId   = mComDat[0];
            var cComCod  = mComDat[1];
            var cComCsc  = mComDat[2];
            var cComCsc2 = mComDat[3];
            var dRegFCre = mComDat[4];
            document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'], (strrpos($_SERVER['PHP_SELF'], "/") + 1), strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
            document.cookie="kModo="+xModo+";path="+"/";
            document.forms['frtraop']['cComId'].value   = cComId;
            document.forms['frtraop']['cComCod'].value  = cComCod;
            document.forms['frtraop']['cComCsc'].value  = cComCsc;
            document.forms['frtraop']['cComCsc2'].value = cComCsc2;
            document.forms['frtraop']['dRegFCre'].value = dRegFCre;
            document.forms['frtraop'].action= "frreopce.php";
            document.forms['frtraop'].submit()
         }else{
            alert("Para esta opcion debe seleccionar un comprobante");
         }
        }else{
          if(document.forms['frgrm']['vRecords'].value > 1){
            var nActivos = 0;
            var elemento = null;
            for(i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
              if(document.forms['frgrm']['oChkCom'][i].checked == true ) {
                nActivos++;
                elemento = document.forms['frgrm']['oChkCom'][i];
              }
              if (nActivos > 1){
                break;
              }
            }
            if (nActivos == 1){
              var mComDat  = elemento.id.split('~');
              var cComId   = mComDat[0];
              var cComCod  = mComDat[1];
              var cComCsc  = mComDat[2];
              var cComCsc2 = mComDat[3];
              var dRegFCre = mComDat[4];
              document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'], (strrpos($_SERVER['PHP_SELF'], "/") + 1), strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
              document.cookie="kModo="+xModo+";path="+"/";
              document.forms['frtraop']['cComId'].value   = cComId;
              document.forms['frtraop']['cComCod'].value  = cComCod;
              document.forms['frtraop']['cComCsc'].value  = cComCsc;
              document.forms['frtraop']['cComCsc2'].value = cComCsc2;
              document.forms['frtraop']['dRegFCre'].value = dRegFCre;
              document.forms['frtraop'].action= "frreopce.php";
              document.forms['frtraop'].submit()
            }
            else if (nActivos == 0){
              alert("Para esta opcion debe seleccionar un comprobante");
            }
            else{
              alert("Para esta opcion solo se permite seleccionar un comprobante")
            }
          }
        }
      }

      function fnModificarObservacionPrefactura(xModo) {
        if (document.forms['frgrm']['vRecords'].value != "0"){
          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oChkCom'].checked == true) {
                var mComDat = document.forms['frgrm']['oChkCom'].id.split("~");

                if (mComDat[5] == "PROVISIONAL") {
                  if (confirm("Esta Seguro de Modificar la Observacion del Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
                    var cPathUrl = "frobsnue.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5];
                    var nX    = screen.width;
                    var nY    = screen.height;
                    var nNx      = (nX-450)/2;
                    var nNy      = (nY-210)/2;
                    var cWinOpt  = "width=450,scrollbars=1,height=210,left="+nNx+",top="+nNy;
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.cookie="kModo="+xModo+";path="+"/";
                    cWindow = window.open(cPathUrl,'cWinCam',cWinOpt);
                    cWindow.focus();
                  }
                } else {
                  alert("Solo se Puede Modificar la Observacion de Comprobantes en Estado [PROVISIONAL], Verifique.");
                }
              }else{
                alert("Para Esta Opcion Debe Seleccionar un Comprobante");
              }
            break;
            default:
              var nSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
                if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0) {
                  var mComDat = document.forms['frgrm']['oChkCom'][i].id.split("~");
                  nSw_Prv = 1;
                  if (mComDat[5] == "PROVISIONAL") {
                    if (confirm("Esta Seguro de Modificar la Observacion del Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
                      var cPathUrl = "frobsnue.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5];
                      var nX    = screen.width;
                      var nY    = screen.height;
                      var nNx      = (nX-450)/2;
                      var nNy      = (nY-210)/2;
                      var cWinOpt  = "width=450,scrollbars=1,height=210,left="+nNx+",top="+nNy;
                      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                      document.cookie="kModo="+xModo+";path="+"/";
                      cWindow = window.open(cPathUrl,'cWinCam',cWinOpt);
                      cWindow.focus();
                    }
                  } else {
                    alert("Solo se Puede Modificar la Observacion de Comprobantes en Estado [PROVISIONAL], Verifique.");
                  }
                }
              }

              if(nSw_Prv == 0){
                alert("Para Esta Opcion Debe Seleccionar un Comprobante");
              }
            break;
          }
        }
      }

      function fnModificarOrdenPrefactura(xModo) {
        if (document.forms['frgrm']['vRecords'].value != "0"){
          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oChkCom'].checked == true) {
                var mComDat = document.forms['frgrm']['oChkCom'].id.split("~");

                if (mComDat[5] == "PROVISIONAL") {
                  if (confirm("Esta Seguro de Modificar la Orden de Compra del Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
                    var cPathUrl = "frordnue.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5];
                    var nX    = screen.width;
                    var nY    = screen.height;
                    var nNx      = (nX-450)/2;
                    var nNy      = (nY-210)/2;
                    var cWinOpt  = "width=450,scrollbars=1,height=210,left="+nNx+",top="+nNy;
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.cookie="kModo="+xModo+";path="+"/";
                    cWindow = window.open(cPathUrl,'cWinCam',cWinOpt);
                    cWindow.focus();
                  }
                } else {
                  alert("Solo se Puede Modificar la Orden de Compra de Comprobantes en Estado [PROVISIONAL], Verifique.");
                }
              }else{
                alert("Para Esta Opcion Debe Seleccionar un Comprobante");
              }
            break;
            default:
              var nSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
                if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0) {
                  var mComDat = document.forms['frgrm']['oChkCom'][i].id.split("~");
                  nSw_Prv = 1;
                  if (mComDat[5] == "PROVISIONAL") {
                    if (confirm("Esta Seguro de Modificar la Orden de Compra del Comprobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
                      var cPathUrl = "frordnue.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gRegFCre="+mComDat[4]+"&gRegEst="+mComDat[5];
                      var nX    = screen.width;
                      var nY    = screen.height;
                      var nNx      = (nX-450)/2;
                      var nNy      = (nY-210)/2;
                      var cWinOpt  = "width=450,scrollbars=1,height=210,left="+nNx+",top="+nNy;
                      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                      document.cookie="kModo="+xModo+";path="+"/";
                      cWindow = window.open(cPathUrl,'cWinCam',cWinOpt);
                      cWindow.focus();
                    }
                  } else {
                    alert("Solo se Puede Modificar la Orden de Compra de Comprobantes en Estado [PROVISIONAL], Verifique.");
                  }
                }
              }

              if(nSw_Prv == 0){
                alert("Para Esta Opcion Debe Seleccionar un Comprobante");
              }
            break;
          }
        }
      }

      function f_Estado_Sap(xComId,xComCod,xComCsc,xComCsc2,xRegFcre) {
        var nWidth  = 500;
        var nHeight = 420;
        var nLeftPosition = (screen.width) ? (screen.width-nWidth)/2 : 0;
        var nTopPosition  = (screen.height) ? (screen.height-nHeight)/2 : 0;
        var cSettings ='height='+nHeight+',width='+nWidth+',top='+nTopPosition+',left='+nLeftPosition+',scrollbars=YES,resizable'
        var cRuta = 'frestsap.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gRegFcre='+xRegFcre;
        zWin = window.open(cRuta,'zWinXml',cSettings);
        zWin.focus();
      }

      function fnEnviarConsultaInducida(xDatos){
        document.forms['frgrm']['cPeriodos'].value     = xDatos['cPeriodos'];
			  document.forms['frgrm']['dDesde'].value        = xDatos['dDesde'];
			  document.forms['frgrm']['dHasta'].value        = xDatos['dHasta'];
			  document.forms['frgrm']['cCcoId'].value        = xDatos['cCcoId'];
			  document.forms['frgrm']['cUsrId'].value        = xDatos['cUsrId'];
			  document.forms['frgrm']['cEstadoDian'].value   = xDatos['cEstadoDian'];
        document.forms['frgrm']['cConsecutivo'].value  = xDatos['cConsecutivo'];
        document.forms['frgrm']['cDo'].value           = xDatos['cDo'];
        document.forms['frgrm']['cTerId'].value        = xDatos['cTerId'];
        document.forms['frgrm']['cTerId2'].value       = xDatos['cTerId2'];
        document.forms['frgrm'].submit();
      }

      function fnConsultaInducida(){
        var nWidth  = 520;
        var nHeight = 400;
				var nX      = screen.width;
        var nY      = screen.height;
        var nNx     = (nX-nWidth)/2;
        var nNy     = (nY-nHeight)/2;
        var cWinOpt = "width="+nWidth+",scrollbars=1,height="+nHeight+",left="+nNx+",top="+nNy;
        cWindow = window.open('', 'cConInd', cWinOpt);
        document.forms['frgrm'].action = 'frframex.php';
        document.forms['frgrm'].target = 'cConInd';
        document.forms['frgrm'].submit();
        document.forms['frgrm'].target = 'fmwork';
        document.forms['frgrm'].action = 'frfacini.php';
        cWindow.focus();
      }

      if("<?php echo $_POST['cConsultaInducida']?>" == "SI"){
        if("<?php echo $_POST['cPeriodos']?>" != "99"){
          parent.fmpro.location='<?php echo $cSystem_Libs_Php_Directory ?>/utilfepe.php?gTipo='+"<?php echo $_POST['cPeriodos']?>"+'&gForm='+'frgrm'+'&gFecIni='+'dDesde'+'&gFecFin='+'dHasta';
        }
      }

      function fnEnviarReporteXCorreoHP() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        var nSel = 0;

        if (document.forms['frgrm']['vRecords'].value == 1){
          if (document.forms['frgrm']['oChkCom'].checked == true) {
            var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
            var cComId  = docun[0];
            var cComCod = docun[1];
            var cComCsc = docun[2];
            var cComCsc2= docun[3];
            var dRegFCre= docun[4]; //Es la misma fecha del comprobante
            var cTerId  = docun[6];
            var cEstado = docun[8];
            var dComFec = docun[10];
            var prints = '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
            nSel = 1;
          }
        } else {
          if (document.forms['frgrm']['vRecords'].value > 1){ //varios registros para imprimir //
            var prints  = '';
            var nSw_Prv = 0;
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
              if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0){
                nSw_Prv = 1;
                var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
                var cComId  = docun[0];
                var cComCod = docun[1];
                var cComCsc = docun[2];
                var cComCsc2= docun[3];
                var dRegFCre= docun[4];  //Es la misma fecha del comprobante
                var cTerId  = docun[6];
                var cEstado = docun[8];
                var dComFec = docun[10];
                prints += '|'+cComId+'~'+cComCod+'~'+cComCsc+'~'+cComCsc2+'~'+dRegFCre+'~'+dComFec+'|';
                nSel = 1;
              }
            }
          }
        }

        if (nSel == 1) {
          cRuta = 'frfacrhp.php?prints='+prints;
          parent.fmpro.location = cRuta; // Invoco el menu.
        } else {
          alert("Debe Seleccionar una Factura.");
        }
      }
    </script>
  </head>
  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">

    <form name = "frestado" action = "frfacgra.php" method = "post" target="fmpro">
      <input type = "hidden" name = "cEstado"    value = "">
      <input type = "hidden" name = "cComId"     value = "">
      <input type = "hidden" name = "cComCod"    value = "">
      <input type = "hidden" name = "cComCsc"    value = "">
      <input type = "hidden" name = "cComCsc2"   value = "">
    </form>

    <form name = "franula" action = "frfacgra.php" method = "post" target="fmpro">
      <input type="hidden" name="gComId"   value="">
      <input type="hidden" name="gComCod"  value="">
      <input type="hidden" name="gComCsc"  value="">
      <input type="hidden" name="gComCsc2" value="">
      <input type="hidden" name="gRegFCre" value="">
      <input type="hidden" name="gRegEst"  value="">
      <input type="hidden" name="cCncId" id="cCncId">
      <textarea name="gObsObs" id="gObsObs"></textarea>
      <script language="javascript">
      document.getElementById("gObsObs").style.display="none";
      </script>
    </form>

    <form name = "frtraop" action = "frreopce.php" method = "post" target="fmpro">
      <input type="hidden" name="cComId"   value="">
      <input type="hidden" name="cComCod"  value="">
      <input type="hidden" name="cComCsc"  value="">
      <input type="hidden" name="cComCsc2" value="">
      <input type="hidden" name="dRegFCre" value="">
    </form>

    <form name = "frconsol" action = "frfaccpn.php" method = "post" target="fmwork">
      <textarea name="gComprobantes" id="gComprobantes" style="display:none;"></textarea>
    </form>

    <form name = "frgrm" action = "frfacini.php" method = "post" target="fmwork">
      <input type = "hidden" name = "vRecords"   value = "">
      <input type = "hidden" name = "vLimInf"    value = "<?php echo $vLimInf ?>">
      <input type = "hidden" name = "vSortField" value = "<?php echo $vSortField ?>">
      <input type = "hidden" name = "vSortType"  value = "<?php echo $vSortType ?>">
      <input type = "hidden" name = "vTimes"     value = "<?php echo $vTimes ?>">
      <input type = "hidden" name = "vTimesSave" value = "0">
      <input type = 'hidden'  name = "cCadFac"    value=''>
      <input type = "hidden" name = "vBuscar"    value = "<?php echo $_POST['vBuscar'] ?>">
      <input type = "hidden" name = "cOrderByOrder"  value = "<?php echo $_POST['cOrderByOrder'] ?>" style = "width:1000">
      <!--Campos ocultos de la consulta inducida-->
      <input type = "hidden" name = "cEstadoDian"   value = "<?php echo $cEstadoDian ?>">
      <input type = "hidden" name = "cConsecutivo"  value = "<?php echo $cConsecutivo ?>">
      <input type = "hidden" name = "cDo"           value = "<?php echo $cDo ?>">
      <input type = "hidden" name = "cTerId"        value = "<?php echo $cTerId ?>">
      <input type = "hidden" name = "cTerId2"       value = "<?php echo $cTerId2 ?>">

      <!-- Inicia Nivel de Procesos -->
      <?php if (mysql_num_rows($xUsrMen) > 0) { ?>
        <center>
          <table width="95%" cellspacing="0" cellpadding="0" border="0">
            <tr>
              <td>
                <fieldset>
                  <legend>Proceso <?php echo $_COOKIE['kProDes'] ?></legend>
                  <center>
                    <table cellspacing="0" width="100%">
                      <?php
                        $y = 0;
                        /* Empiezo a Leer la sys00005 */
                        while($mUsrMen = mysql_fetch_array($xUsrMen)) {
                          if($y == 0 || $y % 5 == 0) {
                            if ($y == 0) {?>
                            <tr>
                            <?php } else { ?>
                            </tr><tr>
                            <?php }
                          }
                          /* Busco de la sys00005 en la sys00006 */
                          $qUsrPer  = "SELECT * ";
                          $qUsrPer .= "FROM $cAlfa.sys00006 ";
                          $qUsrPer .= "WHERE ";
                          $qUsrPer .= "usridxxx = \"{$_COOKIE['kUsrId']}\" AND ";
                          $qUsrPer .= "modidxxx = \"{$mUsrMen['modidxxx']}\"  AND ";
                          $qUsrPer .= "proidxxx = \"{$mUsrMen['proidxxx']}\"  AND ";
                          $qUsrPer .= "menidxxx = \"{$mUsrMen['menidxxx']}\"  LIMIT 0,1";
                          $xUsrPer = f_MySql("SELECT","",$qUsrPer,$xConexion01,"");
                          if (mysql_num_rows($xUsrPer) > 0) { ?>
                            <td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory ?>/<?php echo $mUsrMen['menimgon'] ?>" style = "cursor:pointer" onClick ="javascript:f_Link('<?php echo $mUsrMen['modidxxx'] ?>','<?php echo $mUsrMen['proidxxx'] ?>','<?php echo $mUsrMen['menidxxx'] ?>','<?php echo $mUsrMen['menformx']?>','<?php echo $mUsrMen['menopcxx']?>','<?php echo $mUsrMen['mendesxx']?>')"><br>
                            <a href = "javascript:f_Link('<?php echo $mUsrMen['modidxxx'] ?>','<?php echo $mUsrMen['proidxxx'] ?>','<?php echo $mUsrMen['menidxxx'] ?>','<?php echo $mUsrMen['menformx']?>','<?php echo $mUsrMen['menopcxx']?>','<?php echo $mUsrMen['mendesxx']?>')"
                              style="color:<?php echo $vSysStr['system_link_menu_color'] ?>"><?php echo $mUsrMen['mendesxx'] ?></a></center></td>
                          <?php } else { ?>
                            <td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory ?>/<?php echo $mUsrMen['menimgof']?>"><br>
                            <?php echo $mUsrMen['mendesxx'] ?></center></td>
                          <?php }
                          $y++;
                        }
                        $celdas = "";
                        $nf = intval($y/5);
                        $resto = $y-$nf;
                        $restan = 5-$resto;
                        if ($restan > 0) {
                          for ($i=0;$i<$restan;$i++) {
                            $celdas.="<td width='20%'></td>";
                          }
                          echo $celdas;
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

        if ($vLimInf == "" && $vLimSup == "") {
          $vLimInf = "00";
          $vLimSup = $vSysStr['system_rows_page_ini'];
        }elseif ($vLimInf == "") {
          $vLimInf = "00";
        }

        if (substr_count($vLimInf,"-") > 0) {
          $vLimInf = "00";
        }

        if ($vPaginas == "") {
          $vPaginas = "1";
        }

        // Si Viene Vacio el $cCcoId lo Cargo con la Cookie del Centro de Costo
        // Si no Hago el SELECT con el Centro de Costo que me Entrega el Combo del INI
        if (empty($cCcoId)) {
          $cCcoId  = "";
        } else {
          // Si el $cCcoId Viene Cargado del Combo con "ALL" es porque Debo Mostrar Todos los Centros de Costos
          // Si no Dejo la Sucursal que Viene Cargada
          if ($cCcoId == "ALL") {
            $cCcoId = "";
          }
        }

        /**
         * Si Viene Vacio el $cUsrId lo Cargo con la Cookie del Usuario
         * Si no Hago el SELECT con el Usuario que me Entrega el Combo de INI
         */
        if ($cUsrId == "") {
          $cUsrId = ($_COOKIE['kUsrId'] == "ADMIN" || $cUsrInt == "SI") ? "ALL":$_COOKIE['kUsrId'];
        }

        // Sql para Buscar Periodos Abiertos
        $qPeriodos  = "SELECT CONCAT(peranoxx,permesxx) AS periodo ";
        $qPeriodos .= "FROM $cAlfa.fpar0122 ";
        $qPeriodos .= "WHERE ";
        $qPeriodos .= "comidxxx = \"F\"  AND ";
        $qPeriodos .= "regestxx = \"ABIERTO\" ";
        $qPeriodos .= "GROUP BY peranoxx,permesxx";
        $xPeriodos  = f_MySql("SELECT","",$qPeriodos,$xConexion01,"");
        $zPeriodos = "";
        if (mysql_num_rows($xPeriodos) > 0) {
          while ($xRP = mysql_fetch_array($xPeriodos)) {
            $zPeriodos .= "\"".$xRP['periodo']."\"".",";
          }
          $zPeriodos = substr($zPeriodos,0,(strlen($zPeriodos)-1));
        }
        // Fin de Sql para Buscar Periodos Abiertos

        /**INICIO SQL**/
        if ($_POST['cPeriodos'] == "") {
          $_POST['cPeriodos'] == "20";
          $_POST['dDesde'] = substr(date('Y-m-d'),0,8)."01";
          $_POST['dHasta'] = date('Y-m-d');
        }


        if ($_POST['vSearch'] != "") {
          /**
           * Buscando los id que corresponden a las busquedas de los lefjoin
           */
          $qUsrNom  = "SELECT ";
          $qUsrNom .= "USRIDXXX ";
          $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
          $qUsrNom .= "WHERE IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") LIKE \"%{$_POST['vSearch']}%\" ";
          $xUsrNom = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
          $cUsrSearch = "";
          while ($xRUN = mysql_fetch_array($xUsrNom)) {
            $cUsrSearch .= "\"{$xRUN['USRIDXXX']}\",";
          }
          $cUsrSearch = substr($cUsrSearch,0,strlen($cUsrSearch)-1);

          $qCliNom  = "SELECT ";
          $qCliNom .= "CLIIDXXX ";
          $qCliNom .= "FROM $cAlfa.SIAI0150 ";
          $qCliNom .= "WHERE IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF((TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) != \"\",(TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))),\"SIN NOMBRE\")) LIKE \"%{$_POST['vSearch']}%\" ";
          $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
          $cCliIdSearch = "";
          while ($xRCN = mysql_fetch_array($xCliNom)) {
            $cCliIdSearch .= "\"{$xRCN['CLIIDXXX']}\",";
          }
          $cCliIdSearch = substr($cCliIdSearch,0,strlen($cCliIdSearch)-1);

        }

        $mCabMov = array();
        for ($iAno=substr($_POST['dDesde'],0,4);$iAno<=substr($_POST['dHasta'],0,4);$iAno++) { // Recorro desde el a�o de inicio hasta e a�o de fin de la consulta

          if ($iAno == substr($_POST['dDesde'],0,4)) {
            $qCabMov  = "(SELECT DISTINCT ";
            $qCabMov .= "SQL_CALC_FOUND_ROWS ";
          }else {
            $qCabMov  .= "(SELECT DISTINCT ";
          }
          $qCabMov .= "$cAlfa.fcoc$iAno.comidxxx AS comidxxx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.comcodxx AS comcodxx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.comcscxx AS comcscxx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.comcsc2x AS comcsc2x,";
          $qCabMov .= "$cAlfa.fcoc$iAno.comfecxx AS comfecxx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.comperxx AS comperxx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.ccoidxxx AS ccoidxxx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.terid2xx AS terid2xx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.reghcrex AS reghcrex,";
          $qCabMov .= "($cAlfa.fcoc$iAno.comvlrxx + $cAlfa.fcoc$iAno.comvlrnf) AS comvlrxx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.comipfxx AS comipfxx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.comealpo AS comealpo,";
          $qCabMov .= "$cAlfa.fcoc$iAno.regestxx AS regestxx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.comfpxxx AS comfpxxx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.comobs2x AS comobs2x,";          
          $qCabMov .= "$cAlfa.fcoc$iAno.teridxxx AS teridxxx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.terid2xx AS terid2xx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.comprnxx AS comprnxx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.regusrxx AS regusrxx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.comfexml AS comfexml,";
          $qCabMov .= "$cAlfa.fcoc$iAno.compcevx AS compcevx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.compceen AS compceen,";
          $qCabMov .= "$cAlfa.fcoc$iAno.compcesn AS compcesn,";
          $qCabMov .= "$cAlfa.fcoc$iAno.compcees AS compcees,";
          $qCabMov .= "$cAlfa.fcoc$iAno.compcere AS compcere,";
          $qCabMov .= "$cAlfa.fcoc$iAno.disidxxx AS disidxxx ";
          if (substr_count($cOrderByOrder,"USRNOMXX") > 0) {
            $qCabMov .= ", IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
          }
          if (substr_count($cOrderByOrder,"CLINOMXX") > 0) {
            $qCabMov .= ", IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
          }
          if (substr_count($cOrderByOrder,"PRONOMXX") > 0) {
            $qCabMov .= ", IF($cAlfa.A.CLINOMXX != \"\",$cAlfa.A.CLINOMXX,CONCAT($cAlfa.A.CLINOM1X,\" \",$cAlfa.A.CLINOM2X,\" \",$cAlfa.A.CLIAPE1X,\" \",$cAlfa.A.CLIAPE2X)) AS PRONOMXX ";
          }
          $qCabMov .= "FROM $cAlfa.fcoc$iAno ";
          if (substr_count($cOrderByOrder,"USRNOMXX") > 0) {
            $qCabMov .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.fcoc$iAno.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
          }
          if (substr_count($cOrderByOrder,"CLINOMXX") > 0) {
            $qCabMov .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$iAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
          }
          if (substr_count($cOrderByOrder,"PRONOMXX") > 0) {
            $qCabMov .= "LEFT JOIN $cAlfa.SIAI0150 AS A ON $cAlfa.fcoc$iAno.terid2xx = $cAlfa.A.CLIIDXXX ";
          }

          $qCabMov .= "WHERE $cAlfa.fcoc$iAno.comidxxx = \"F\" AND ";
          $qCabMov .= "$cAlfa.fcoc$iAno.regestxx IN (\"ACTIVO\",\"INACTIVO\",\"PROVISIONAL\") AND ";
          //Buscando por consecutivo exacto o contenido
          if ($_POST['cConsecutivo'] != "") {
            $qCabMov .= "$cAlfa.fcoc$iAno.comcscxx = \"{$_POST['cConsecutivo']}\" AND ";
          }
          //Buscando por DO exacto o contenido
          if ($_POST['cDo'] != "") {
            $qCabMov .= "$cAlfa.fcoc$iAno.comfpxxx LIKE \"%~{$_POST['cDo']}~%\" AND ";
          }
          //Buscando por Cliente
          if ($_POST['cTerId'] != "") {
            $qCabMov .= "$cAlfa.fcoc$iAno.teridxxx = \"{$_POST['cTerId']}\" AND ";
          }
          //Buscando por facturar a
          if ($_POST['cTerId2'] != "") {
            $qCabMov .= "$cAlfa.fcoc$iAno.terid2xx = \"{$_POST['cTerId2']}\" AND ";
          }
          //Buscando por estado DIAN
          if ($_POST['cEstadoDian'] != "") {
            switch($_POST['cEstadoDian']){
              case "REGISTRADO-VP":
                $qCabMov .= "($cAlfa.fcoc$iAno.compcevx = \"VP\" AND $cAlfa.fcoc$iAno.compceen != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcees = \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcesn = \"EXITOSO\") AND ";
              break;
              case "REGISTRADO-2242":
                $qCabMov .= "($cAlfa.fcoc$iAno.compcevx = \"2242\" AND $cAlfa.fcoc$iAno.compceen != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcees = \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcesn = \"\") AND ";
              break;
              case "APROBADO":
                $qCabMov .= "($cAlfa.fcoc$iAno.compcevx = \"VP\" AND $cAlfa.fcoc$iAno.compceen != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcees != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcere = \"APROBADO\") AND ";
              break;
              case "EXITOSO":
                $qCabMov .= "($cAlfa.fcoc$iAno.compcevx = \"2242\" AND $cAlfa.fcoc$iAno.compceen != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcees != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcere = \"EXITOSO\") AND ";
              break;
              case "RECIBIDO":
                $qCabMov .= "($cAlfa.fcoc$iAno.compcevx = \"2242\" AND $cAlfa.fcoc$iAno.compceen != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcees != \"0000-00-00 00:00:00\" AND ($cAlfa.fcoc$iAno.compcere = \"RECIBIDA\" OR $cAlfa.fcoc$iAno.compcere = \"VALIDACION\")) AND ";
              break;
              case "APROBADO_NOTIFICACION":
                $qCabMov .= "($cAlfa.fcoc$iAno.compcevx = \"VP\" AND $cAlfa.fcoc$iAno.compceen != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcees != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcere = \"APROBADO_NOTIFICACION\") AND ";
              break;
              case "FALLIDO-VP":
                $qCabMov .= "(($cAlfa.fcoc$iAno.compcevx = \"VP\" AND $cAlfa.fcoc$iAno.compceen != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcees = \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcesn = \"FALLIDO\") OR ";
                $qCabMov .= "($cAlfa.fcoc$iAno.compcevx = \"VP\" AND $cAlfa.fcoc$iAno.compceen != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcees != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcesn = \"FALLIDO\") OR ";
                $qCabMov .= "($cAlfa.fcoc$iAno.compcevx = \"VP\" AND $cAlfa.fcoc$iAno.compceen != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcees != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcere = \"FALLIDO\")) AND ";
              break;
              case "FALLIDO-2242":
                $qCabMov .= "(($cAlfa.fcoc$iAno.compcevx = \"2242\" AND $cAlfa.fcoc$iAno.compceen != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcesn = \"FALLIDO\" AND $cAlfa.fcoc$iAno.compcees = \"0000-00-00 00:00:00\") OR ";
                $qCabMov .= "($cAlfa.fcoc$iAno.compcevx = \"2242\" AND $cAlfa.fcoc$iAno.compceen != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcees != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcere = \"FALLIDO\")) AND ";
              break;
              break;
              case "NOENVIAR-VP":
                $qCabMov .= "(($cAlfa.fcoc$iAno.compcevx = \"VP\" AND $cAlfa.fcoc$iAno.compceen != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcees != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcesn = \"NOENVIAR\") OR ";
                $qCabMov .= "($cAlfa.fcoc$iAno.compceen != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcees != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcesn = \"NOENVIAR\")) AND ";
              break;
              case "NOENVIAR-2242":
                $qCabMov .= "(($cAlfa.fcoc$iAno.compcevx = \"2242\" AND $cAlfa.fcoc$iAno.compceen != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcees != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcesn = \"NOENVIAR\") OR ";
                $qCabMov .= "($cAlfa.fcoc$iAno.compceen != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcees != \"0000-00-00 00:00:00\" AND $cAlfa.fcoc$iAno.compcesn = \"NOENVIAR\")) AND ";
              break;
            }
          }
          if ($_POST['vSearch'] != "") {
            if ($_POST['cBusExc'] == "SI") {
              $qCabMov .= "$cAlfa.fcoc$iAno.comcscxx = \"{$_POST['vSearch']}\" AND ";
            }
            $qCabMov .= "(";
            $qCabMov .= "$cAlfa.fcoc$iAno.comcodxx LIKE \"%{$_POST['vSearch']}%\" OR ";
            $qCabMov .= "$cAlfa.fcoc$iAno.comcscxx LIKE \"%{$_POST['vSearch']}%\" OR ";
            $qCabMov .= "$cAlfa.fcoc$iAno.comfecxx LIKE \"%{$_POST['vSearch']}%\" OR ";
            $qCabMov .= "$cAlfa.fcoc$iAno.reghcrex LIKE \"%{$_POST['vSearch']}%\" OR ";
            $qCabMov .= "($cAlfa.fcoc$iAno.comvlrxx + $cAlfa.fcoc$iAno.comvlrnf) LIKE \"%{$_POST['vSearch']}%\" OR ";
            $qCabMov .= "$cAlfa.fcoc$iAno.comipfxx LIKE \"%{$_POST['vSearch']}%\" OR ";
            $qCabMov .= "$cAlfa.fcoc$iAno.comealpo LIKE \"%{$_POST['vSearch']}%\" OR ";
            $qCabMov .= "$cAlfa.fcoc$iAno.comfpxxx LIKE \"%~{$_POST['vSearch']}~%\" OR ";
            if ($cUsrSearch != "") {
              $qCabMov .= "$cAlfa.fcoc$iAno.regusrxx IN ($cUsrSearch) OR ";
            }
            if ($cCliIdSearch != "") {
              $qCabMov .= "$cAlfa.fcoc$iAno.teridxxx IN ($cCliIdSearch) OR ";
              $qCabMov .= "$cAlfa.fcoc$iAno.terid2xx IN ($cCliIdSearch) OR ";
            }
            $qCabMov .= "$cAlfa.fcoc$iAno.regestxx LIKE \"%{$_POST['vSearch']}%\") AND ";
          }

          if ($cUsrId != "" && $cUsrId != "ALL") {
            $qCabMov .= "$cAlfa.fcoc$iAno.regusrxx = \"$cUsrId\" AND ";
          }
          if ($cCcoId != "") {
            $qCabMov .= "$cAlfa.fcoc$iAno.ccoidxxx LIKE \"%$cCcoId%\" AND ";
          }
          $qCabMov .= "$cAlfa.fcoc$iAno.comfecxx BETWEEN \"{$_POST['dDesde']}\" AND \"{$_POST['dHasta']}\" ) ";
          /***** FIN SQL *****/

          if ($iAno >= substr($_POST['dDesde'],0,4) && $iAno < substr($_POST['dHasta'],0,4)) {
            $qCabMov .= " UNION ";
          }
        } ## for ($iAno=substr($_POST['dDesde'],0,4);$iAno<=substr($_POST['dHasta'],0,4);$iAno++) { ##

        //// CODIGO NUEVO PARA ORDER BY
        $cOrderBy = "";
        $vOrderByOrder = explode("~",$cOrderByOrder);
        for ($z=0;$z<count($vOrderByOrder);$z++) {
          if ($vOrderByOrder[$z] != "") {
            if (substr_count($_POST[$vOrderByOrder[$z]], "comidxxx") > 0) {
              //Ordena por comidxxx, comcodxx, comcscxx, comcsc2x
              $cOrdComId = str_replace("comidxxx", "CONCAT(comidxxx,\"-\",comcodxx,\"-\",comcscxx,\"-\",comcsc2x)", $_POST[$vOrderByOrder[$z]]);
              $cOrderBy .= $cOrdComId;
            } else {
              $cOrderBy .= $_POST[$vOrderByOrder[$z]];
            }
          }
        }
        if (strlen($cOrderBy)>0) {
          $cOrderBy = substr($cOrderBy,0,strlen($cOrderBy)-1);
          $cOrderBy = "ORDER BY ".$cOrderBy;
        } else {
          //Ordenamiento por Consecutivo 1,Consecutivo 2 o Fecha de Modificado
          if($cOrderTramite != ""){
            $cOrderBy = "ORDER BY ".$cOrderTramite. " DESC ";
          }else{
            $cOrderBy = "ORDER BY comfecxx DESC,reghcrex  DESC";
          }
        }
        //// FIN CODIGO NUEVO PARA ORDER BY
        $qCabMov .= "$cOrderBy LIMIT $vLimInf,$vLimSup ";
        $xCabMov = f_MySql("SELECT","",$qCabMov,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qCabMov."~".mysql_num_rows($xCabMov));
        // echo $qCabMov."~".mysql_num_rows($xCabMov);

        $xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
        $xRNR = mysql_fetch_array($xNumRows);
        $nRNR += $xRNR['FOUND_ROWS()'];

        while ($xRCC = mysql_fetch_array($xCabMov)) {
          //Busando Nombre del usuario
          if (substr_count($cOrderByOrder,"USRNOMXX") == 0) {
            $qUsrNom  = "SELECT ";
            $qUsrNom .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
            $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
            $qUsrNom .= "WHERE $cAlfa.SIAI0003.USRIDXXX = \"{$xRCC['regusrxx']}\" LIMIT 0,1 ";
            $xUsrNom = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
            if (mysql_num_rows($xUsrNom) > 0) {
              $xRUN = mysql_fetch_array($xUsrNom);
              $xRCC['USRNOMXX'] = $xRUN['USRNOMXX'];
            } else {
              $xRCC['USRNOMXX'] = "USUARIO SIN NOMBRE";
            }
          }
          //Buscando nombre del cliente
          if (substr_count($cOrderByOrder,"CLINOMXX") == 0) {
            $qCliNom  = "SELECT ";
            $qCliNom .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF((TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) != \"\",(TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))),\"SIN NOMBRE\")) AS CLINOMXX ";
            $qCliNom .= "FROM $cAlfa.SIAI0150 ";
            $qCliNom .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xRCC['teridxxx']}\" LIMIT 0,1 ";
            $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
            if (mysql_num_rows($xCliNom) > 0) {
              $xRCN = mysql_fetch_array($xCliNom);
              $xRCC['CLINOMXX'] = $xRCN['CLINOMXX'];
            } else {
              $xRCC['CLINOMXX'] = "SIN NOMBRE";
            }
          }
          //Buscando nombre del proveedor
          if (substr_count($cOrderByOrder,"PRONOMXX") == 0) {
            $qProNom  = "SELECT ";
            $qProNom .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF((TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) != \"\",(TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))),\"SIN NOMBRE\")) AS CLINOMXX ";
            $qProNom .= "FROM $cAlfa.SIAI0150 ";
            $qProNom .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xRCC['terid2xx']}\" LIMIT 0,1 ";
            $xProNom = f_MySql("SELECT","",$qProNom,$xConexion01,"");
            if (mysql_num_rows($xProNom) > 0) {
              $xRPN = mysql_fetch_array($xProNom);
              $xRCC['PRONOMXX'] = $xRPN['CLINOMXX'];
            } else {
              $xRCC['PRONOMXX'] = "SIN NOMBRE";
            }
          }

          //Buscando estado del periodo contable
          $qPerEst  = "SELECT $cAlfa.fpar0122.regestxx ";
          $qPerEst .= "FROM $cAlfa.fpar0122 ";
          $qPerEst .= "WHERE ";
          $qPerEst .= "$cAlfa.fpar0122.comidxxx = \"{$xRCC['comidxxx']}\" AND ";
          $qPerEst .= "$cAlfa.fpar0122.comcodxx = \"{$xRCC['comcodxx']}\" AND ";
          $qPerEst .= "$cAlfa.fpar0122.peranoxx = \"".substr($xRCC['comperxx'],0,4)."\" AND ";
          $qPerEst .= "$cAlfa.fpar0122.permesxx = \"".substr($xRCC['comperxx'],4,2)."\" LIMIT 0,1 ";
          $xPerEst = f_MySql("SELECT","",$qPerEst,$xConexion01,"");
          if (mysql_num_rows($xPerEst) > 0) {
            $xRPE = mysql_fetch_array($xPerEst);
            $xRCC['perestxx'] = ($xRPE['regestxx'] != "") ? $xRPE['regestxx'] : "CERRADO";
          } else {
            $xRCC['perestxx'] = "CERRADO";
          }
          $mCabMov[count($mCabMov)] = $xRCC;
        }
      ?>
      <center>
        <table width="95%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td>
              <fieldset>
                <legend>Registros Seleccionados (<?php echo $nRNR ?>)</legend>
                <center>
                  <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                      <td class="clase08" width="14%" align="left">
                        <input type="text" class="letra" name = "vSearch" maxlength="20" value = "<?php echo $vSearch ?>" style= "width:80"
                          onblur="javascript:this.value=this.value.toUpperCase();
                                             document.frgrm.vLimInf.value='00'; ">
                        <input type="checkbox" name="cBusExc" value ="NO" onclick="javascript:if(this.checked == true) { this.value = 'SI'} else { this.value = 'NO'}">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:pointer" title="Buscar"
                          onClick = "javascript:document.forms['frgrm']['vBuscar'].value = 'ON';
                                                document.frgrm.vSearch.value=document.frgrm.vSearch.value.toUpperCase();
                                                if ((document.forms['frgrm']['dHasta'].value < document.forms['frgrm']['dDesde'].value) ||
                                                  document.forms['frgrm']['dDesde'].value == '' || document.forms['frgrm']['dHasta'].value == '') {
                                                  alert('El Sistema no Puede Hacer la Busqueda por Error en las Fechas del Periodo a Buscar, Verifique.');
                                                } else {
                                                  if (document.forms['frgrm']['vPaginas'].id == 'ON') {
                                                    document.forms['frgrm']['vPaginas'].id = 'OFF'
                                                  } else {
                                                    document.forms['frgrm']['vPaginas'].value='1';
                                                  };
                                                  document.forms['frgrm']['vLimInf'].value='00';
                                                  document.forms['frgrm'].submit();
                                                }">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:hand" title="Mostrar Todo"
                          onClick ="javascript:document.forms['frgrm']['cCcoId'].value='<?php echo $cCcoId ?>';
                                               document.forms['frgrm']['cUsrId'].value='<?php echo $_COOKIE['kUsrId'] ?>';
                                               document.forms['frgrm']['vSearch'].value='';
                                               document.forms['frgrm']['vLimInf'].value='00';
                                               document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
                                               document.forms['frgrm']['vPaginas'].value='1';
                                               document.forms['frgrm']['vSortField'].value='';
                                               document.forms['frgrm']['vSortType'].value='';
                                               document.forms['frgrm']['vTimes'].value='';
                                               document.forms['frgrm']['dDesde'].value='<?php echo substr(date('Y-m-d'),0,8)."01";  ?>';
                                               document.forms['frgrm']['dHasta'].value='<?php echo date('Y-m-d');  ?>';
                                               document.forms['frgrm']['vBuscar'].value='';
                                               document.forms['frgrm']['cPeriodos'].value='20';
                                               document.forms['frgrm']['cOrderByOrder'].value='';
                                               document.forms['frgrm']['cCcoId'].value='';
                                               document.forms['frgrm']['cUsrId'].value='';
                                               document.forms['frgrm']['cOrderTramite'].value='';
                                               document.forms['frgrm']['cEstadoDian'].value='';
                                               document.forms['frgrm']['cConsecutivo'].value='';
                                               document.forms['frgrm']['cDo'].value='';
                                               document.forms['frgrm']['cTerId'].value='';
                                               document.forms['frgrm']['cTerId2'].value='';
                                               document.forms['frgrm'].submit()">&nbsp;&nbsp;&nbsp;
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/cert_ca_cert_on.gif" style = "cursor:pointer" title="Consulta inducida"
                          onClick = "javascript:fnConsultaInducida()">
												<script language = "javascript">
													if ("<?php echo $_POST['cBusExc'] ?>" == "SI") {
														document.forms['frgrm']['cBusExc'].value   = "SI";
														document.forms['frgrm']['cBusExc'].checked = true;
													} else {
														document.forms['frgrm']['cBusExc'].value   = "NO";
														document.forms['frgrm']['cBusExc'].checked = false;
													}
												</script>
                      </td>
                      <td class="name" width="03%" align="left">Filas&nbsp;
                        <input type="text" class="letra" name = "vLimSup" value = "<?php echo $vLimSup ?>" style="width:30;text-align:right"
                          onblur = "javascript:uFixFloat(this);
                                               document.frgrm.vLimInf.value='00'; ">
                      </td>
                      <td class="name" width="05%" align="center">
                        <?php if (ceil($nRNR/$vLimSup) > 1) { ?>
                          <?php if ($vPaginas == "1") { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"    style = "cursor:pointer" title="Pagina Siguiente"
                              onClick = "javascript:document.frgrm.vPaginas.value++;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"    style = "cursor:pointer" title="Ultima Pagina"
                              onClick = "javascript:document.frgrm.vPaginas.value='<?php echo ceil($nRNR/$vLimSup) ?>';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                          <?php } ?>
                          <?php if ($vPaginas > "1" && $vPaginas < ceil($nRNR/$vLimSup)) { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
                              onClick = "javascript:document.frgrm.vPaginas.value='1';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
                              onClick = "javascript:document.frgrm.vPaginas.value--;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente"
                              onClick = "javascript:document.frgrm.vPaginas.value++;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina"
                              onClick = "javascript:document.frgrm.vPaginas.value='<?php echo ceil($nRNR/$vLimSup) ?>';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                          <?php } ?>
                          <?php if ($vPaginas == ceil($nRNR/$vLimSup)) { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
                              onClick = "javascript:document.frgrm.vPaginas.value='1';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
                              onClick = "javascript:document.frgrm.vPaginas.value--;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_nextpage.png" style = "cursor:pointer" title="Pagina Siguiente">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_lastpage.png" style = "cursor:pointer" title="Ultima Pagina">
                          <?php } ?>
                        <?php } else { ?>
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina">
                        <?php } ?>
                      </td>
                      <td class="name" width="09%" align="center">Pag&nbsp;
                        <select Class = "letrase" name = "vPaginas" value = "<?php echo $vPaginas ?>" style = "width:60%"
                          onchange="javascript:this.id = 'ON'; // Cambio 18, Incluir este Codigo.
                                               document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(this.value-1));
                                               document.frgrm.submit();">
                          <?php for ($i=0;$i<ceil($nRNR/$vLimSup);$i++) {
                            if ($i+1 == $vPaginas) { ?>
                              <option value = "<?php echo $i+1 ?>" selected><?php echo $i+1 ?></option>
                            <?php } else { ?>
                              <option value = "<?php echo $i+1 ?>"><?php echo $i+1 ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </td>
                      <td class="name" width="12%" align="center" >
                        <select class="letrase" size="1" name="cPeriodos" style = "width:100%" value = "<?php echo $_POST['cPeriodos'] ?>"
                          onChange = "javascript:
                                      parent.fmpro.location='<?php echo $cSystem_Libs_Php_Directory ?>/utilfepe.php?gTipo='+this.value+'&gForm='+'frgrm'+'&gFecIni='+'dDesde'+'&gFecFin='+'dHasta';
                                      if (document.forms['frgrm']['cPeriodos'].value == '99') {
                                        document.forms['frgrm']['dDesde'].readOnly = false;
                                        document.forms['frgrm']['dHasta'].readOnly = false;
                                        document.forms['frgrm']['vLimInf'].value = '00';
                                      } else {
                                        document.forms['frgrm']['dDesde'].readOnly = true;
                                        document.forms['frgrm']['dHasta'].readOnly = true;
                                        document.forms['frgrm']['vLimInf'].value = '00';
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
                      <td class="name" width="06%" align="center">
                        <input type = "text" Class = "letra" style = "width:90%;text-align:center" name = "dDesde" value = "<?php
                        if($_POST['dDesde']=="" && $_POST['cPeriodos'] == ""){
                          echo substr(date('Y-m-d'),0,8)."01";
                        } else{
                          echo $_POST['dDesde'];
                        } ?>"
                          onblur="javascript:document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));" readonly>
                      </td>
                      <td class="name" width="06%" align="center">
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
                      <td class="name" width="10%" align="center">
                        <select Class = "letrase" name = "cCcoId" value = "<?php echo $cCcoId ?>" style = "width:99%">
                          <option value = "ALL" selected>SUCURSALES</option>
                          <?php
                          //  if (empty($vSearch)) {
                              $qSucDat  = "SELECT sucidxxx,ccoidxxx,sucdesxx FROM $cAlfa.fpar0008 WHERE ";
                              $qSucDat .= "regestxx = \"ACTIVO\" ORDER BY sucdesxx";
                              $xSucDat = f_MySql("SELECT","",$qSucDat,$xConexion01,"");
                              if (mysql_num_rows($xSucDat) > 0) {
                                while ($xRSD = mysql_fetch_array($xSucDat)) {
                                  if ($xRSD['ccoidxxx'] == $cCcoId) { ?>
                                    <option value = "<?php echo $xRSD['ccoidxxx']?>" selected><?php echo $xRSD['sucdesxx'] ?></option>
                                  <?php } else { ?>
                                    <option value = "<?php echo $xRSD['ccoidxxx']?>"><?php echo $xRSD['sucdesxx'] ?></option>
                                  <?php }
                                }
                              } else {
                                //f_Mensaje(__FILE__,__LINE__,"No se Encontraron Sucursales");
                              }
                            //}
                          ?>
                        </select>
                      </td>
                    <td class="name" width="13%" align="left">
                      <select Class = "letrase" name = "cUsrId" value = "<?php echo $cUsrId ?>" style = "width:99%" >
                        <option value = "ALL" selected>USUARIOS</option>
                        <?php
                          if (($_COOKIE["kUsrId"] == 'ADMIN' || $cUsrInt == "SI") || ($cAlfa != 'DEOPENWORK' && $cAlfa != 'OPENWORK' && $cAlfa != 'TEOPENWORK')) {
                            $qUsrNom  = "SELECT USRIDXXX,USRNOMXX,USRPROXX,REGESTXX FROM $cAlfa.SIAI0003 WHERE USRIDXXX != \"ADMIN\" AND USRINTXX != \"SI\" AND USRPROXX LIKE \"%103%\" ";
                          } else {
                            $qUsrNom  = "SELECT USRIDXXX,USRNOMXX,USRPROXX,REGESTXX FROM $cAlfa.SIAI0003 WHERE USRIDXXX = \"{$_COOKIE["kUsrId"]}\" AND USRPROXX LIKE \"%103%\" ";?>
                            <script language="javascript">
                              document.forms['frgrm']['cUsrId'].remove(0);
                            </script>
                            <?php
                          }
                          $xUsrNom = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
                          while ($xRUN = mysql_fetch_array($xUsrNom)) {
                            $mPerEsp = array();
                            $mPerEsp = explode("|",$xRUN['USRPROXX']);
                            for($j=0; $j<count($mPerEsp); $j++) {
                              $mAuxPer = array();
                              $mAuxPer = explode("~",$mPerEsp[$j]);
                              if($mAuxPer[1] == "103") {
                                $mMatrizUsr[$i]['usridxxx'] = $xRUN['USRIDXXX'];
                                $mMatrizUsr[$i]['usrnomxx'] = $xRUN['USRNOMXX'];
                                $mMatrizUsr[$i]['regestxx'] = $xRUN['REGESTXX'];
                                $j = count($mPerEsp);
                                $i++;
                              }
                            }
                          }
                          $mMatrizUsr = f_Sort_Array_By_Field($mMatrizUsr,"usrnomxx","ASC_AZ");

                          for ($i=0;$i<count($mMatrizUsr);$i++) {
                            if($mMatrizUsr[$i]['regestxx'] == "INACTIVO"){
                              $cColor = "#FF0000";
                            }else{
                              $cColor = "#000000";
                            }
                            if ($mMatrizUsr[$i]['usridxxx'] == $cUsrId && $cUsrId != "ADMIN" && $cUsrInt != "SI") { ?>
                              <option value = "<?php echo $mMatrizUsr[$i]['usridxxx']?>" style="color:<?php echo $cColor ?>" selected><?php echo $mMatrizUsr[$i]['usrnomxx'] ?></option>
                            <?php } else { ?>
                              <option value = "<?php echo $mMatrizUsr[$i]['usridxxx']?>" style="color:<?php echo $cColor ?>"><?php echo $mMatrizUsr[$i]['usrnomxx'] ?></option>
                            <?php }
                          }
                        ?>
                      </select>
                    </td>
                    <td class="name" width="10%" align="left">
                      <select Class = "letrase" name = "cOrderTramite" value = "<?php echo $cOrderTramite ?>" style = "width:99%" >
                        <option value = "" >ORDENAR POR</option>
                        <option value = "comcscxx">CONSECUTIVO 1</option>
                        <option value = "comcsc2x">CONSECUTIVO 2</option>
                        <option value = "comfecxx">FECHA COMPROBANTE</option>
                      </select>
                      <script language='javascript'>
                        document.forms['frgrm']['cOrderTramite'].value = "<?php echo $cOrderTramite ?>";
                      </script>
                    </td>
                    
                    <!--fin de codigo nuevo-->
                      <td Class="name" align="right">&nbsp;
                        <?php
                          /***** Botones de Acceso Rapido *****/
                          $qBotAcc  = "SELECT * ";
                          $qBotAcc .= "FROM $cAlfa.sys00005,$cAlfa.sys00006 ";
                          $qBotAcc .= "WHERE ";
                          $qBotAcc .= "sys00006.usridxxx = \"{$_COOKIE['kUsrId']}\" AND ";
                          $qBotAcc .= "sys00006.modidxxx = sys00005.modidxxx        AND ";
                          $qBotAcc .= "sys00006.proidxxx = sys00005.proidxxx        AND ";
                          $qBotAcc .= "sys00006.menidxxx = sys00005.menidxxx        AND ";
                          $qBotAcc .= "sys00006.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
                          $qBotAcc .= "sys00006.proidxxx = \"{$_COOKIE['kProId']}\" ";
                          $qBotAcc .= "ORDER BY sys00005.menordxx";

                          $xBotAcc  = f_MySql("SELECT","",$qBotAcc,$xConexion01,"");
                          // f_Mensaje(__FILE__, __LINE__, $qBotAcc."~".mysql_num_rows($xBotAcc));
                          while ($mBotAcc = mysql_fetch_array($xBotAcc)) {
                            switch ($mBotAcc['menopcxx']) {
                              case "BORRAR": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_drop.png" onClick = "javascript:f_Borrar('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "FECHAS": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_calendar.png" onClick = "javascript:f_Fecha_Entrega()" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "IMPRIMIR": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_print.png" onClick = "javascript:f_Imprimir()" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "IMPRIMIR2":
                                if (f_InList($kDf[3],"DEGRUPOGLA","TEGRUPOGLA","GRUPOGLA")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_print.png" onClick = "javascript:f_Imprimir2()" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                              case "CAMBIAESTADO": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory ?>/failed.jpg" onClick = "javascript:f_Cambia_Estado('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "ANEXOFACTURA": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory ?>/file_text.gif" onClick = "javascript:f_Anexo_Factura('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "ANEXOEXCFAC": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory ?>/excel_icon.gif" onClick = "javascript:f_Anexo_Factura_Excel('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "PREFACTURA": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_print.png" onClick = "javascript:f_Prefactura('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "BELCORP":
                                if (f_InList($kDf[3],"DEDESARROL","DEADUANERA","ADUANERA","TEADUANERA","DEADUANERP")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/excel_icon.gif" onClick = "javascript:f_Belcorp()" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                              case "CERTIFICADO":
                                if (!f_InList($kDf[3],"TEALMAVIVA","DEALMAVIVA","ALMAVIVA")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_create-file_bg.gif" onClick = "javascript:f_Certificado_PCC('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                              case "CERTIFICADOINT":
                              if (!f_InList($kDf[3],"TEALMAVIVA","DEALMAVIVA","ALMAVIVA")) { ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" onClick = "javascript:f_Certificado_PCC('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php }
                              break;
                              case "ALPOPULARWS":
                                if (f_InList($kDf[3],"TEALPOPULP","ALPOPULX","ALMAVIVA","TEALMAVIVA")) {
                                  if ($vSysStr['alpopular_activar_seven_facturacion'] == "SI" || f_InList($kDf[3],"ALMAVIVA","TEALMAVIVA")) { ?>
                                    <img src = "<?php echo $cPlesk_Skin_Directory ?>/page_go.png" onClick = "javascript:f_Contabilizar_Factura('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                  <?php }
                                }
                              break;
                              case "HISTORICONOTCAU":
                                if (f_InList($kDf[3],"TEALPOPULP","TEALPOPULX","ALPOPULX")) {
                                  if ($vSysStr['alpopular_activar_seven_facturacion'] == "SI") { ?>
                                    <img src = "<?php echo $cPlesk_Skin_Directory ?>/edit_text.gif" onClick = "javascript:fnHistoricoNotaCausal()" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                  <?php }
                                }
                              break;
                              case "UPSXLM":
                                if (f_InList($kDf[3],"DEUPSXXXXX","TEUPSXXXXP","TEUPSXXXXX","UPSXXXXX")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/page_go.png" onClick = "javascript:f_Xml_Ups()" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                              case "TRANSMITIRXML":
                                if (f_InList($kDf[3],"DEALPOPULX","TEALPOPULX","TEALPOPULP","ALPOPULX")) { 
                                  if ($vSysStr['alpopular_activar_seven_facturacion'] == "SI") { ?>
                                    <img src = "<?php echo $cPlesk_Skin_Directory ?>/page_go.png" onClick = "javascript:fnEnviarXMLaPT('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                  <?php }
                                }
                              break;
                              case "DESCARGARXML":
                                if (f_InList($kDf[3],"DEALPOPULX","TEALPOPULX","TEALPOPULP","ALPOPULX")) {
                                  if ($vSysStr['alpopular_activar_seven_facturacion'] == "SI") { ?>
                                    <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_xml.png" onClick = "javascript:fnDescargarXML('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                  <?php }
                                }
                              break;
                              case "TRANSMITIRTXT":
                                if (f_InList($kDf[3],"ALMAVIVA","TEALMAVIVA","DEALMAVIVA")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/upload_file01.png" onClick = "javascript:fnEnviarTXTaPT('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php 
                                }
                              break;
                              case "DESCARGARTXT":
                                if (f_InList($kDf[3],"ALMAVIVA","TEALMAVIVA","DEALMAVIVA")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/txt_file01.png" onClick = "javascript:fnDescargarTXT('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                              //Se comenta opcion porque se desarrollo una tarea automatica para realizar este proceso
                              /*case "CONTABILIZAR":
                                if (f_InList($kDf[3],"DEUPSXXXXX","TEUPSXXXXP","TEUPSXXXXX","UPSXXXXX")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/wysiwyg.gif" onClick = "javascript:f_Contabilizar_Ups()" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;*/
                              case "LEGALIZAR": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory ?>/ok.gif" onClick = "javascript:f_Legalizar_Provisionales('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "ENVIARCORREO":
                                if (f_InList($kDf[3],"SIACOSIA","TESIACOSIP","DESIACOSIP","DEDESARROL","TEPRUEBASX")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/bmail.png" onClick = "javascript:f_Envia_Correo()" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                              case "VERFACTURA":
                                  if (!f_InList($kDf[3],"TEALPOPULP","TEALPOPULX","DEALPOPULX","ALPOPULX")) { ?>
                                    <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show_bg.gif" onClick = "javascript:f_Ver_Factura()" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                  <?php }
                              break;
                              case "REPORTELG":
                                if (f_InList($kDf[3],"SIACOSIA","TESIACOSIP","DESIACOSIP","DEDESARROL","TEPRUEBASX")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/excel_icon.gif" onClick = "javascript:f_Reporte_Lg()" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                              case "ACTIVAR":
                                if (!f_InList($kDf[3],"TEALPOPULP","TEALPOPULX","DEALPOPULX","ALPOPULX","ALMAVIVA","TEALMAVIVA")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_cambest.gif" onClick = "javascript:f_Activar_Factura('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                              case "ALMACAFEWS":
                                if (f_InList($kDf[3],"TEALMACAFE","DEALMACAFE","ALMACAFE")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/page_go.png" onClick = "javascript:fnContabilizarSAP('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                              case "CONSOLIDARPREFACTURA":
                                if (f_InList($kDf[3],"TEALMAVIVA","DEALMAVIVA","ALMAVIVA")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/ok.gif" onClick = "javascript:fnConsolidarPrefacturas('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                              case "ENVIARCORREOALM":
                                if (f_InList($kDf[3],"TEALMAVIVA","DEALMAVIVA","ALMAVIVA")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/cert_ca_cert_on.gif" onClick = "javascript:fnEnviarFacturaXCorreo('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                              case "CERTIFICADOMANDATO":
                                if (f_InList($kDf[3],"TEALMAVIVA","DEALMAVIVA","ALMAVIVA","DEDESARROL")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_create-file_bg.gif" onClick = "javascript:fnCertificadoMandato('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                              case "ANEXOFACTURA2":
                              if(f_InList($kMysqlDb,"DEDESARROL", "ALADUANA", "TEALADUANA", "DEALADUANA" )) { ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_create-file_bg.gif" onClick = "javascript:f_Certificado_PCC2('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php }
                              break;
                              case "ENVIARCORREOMAL":
                                if (f_InList($kDf[3],"TEGRUMALCO","DEGRUMALCO","DEDESARROL","GRUMALCO")) {?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/page_go.png" onClick = "javascript:fnEnviarCertificadoXCorreoMalco('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                              case "REPORTEFACTURA":
                              if (f_InList($kDf[3],"DEDESARROL","TEALADUANA","DEALADUANA","ALADUANA")) {?>
                                <img src = "<?php echo $cPlesk_Skin_Directory ?>/excel_icon.gif" onClick = "javascript:fnReporteFacturaExcel('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php }
                              break;
                              case "TRANSMITIROPENPCE":
                                if ($vSysStr['system_activar_openpce'] == "SI" || $vSysStr['system_activar_openetl'] == "SI") {
                                  if ($vSysStr['system_activar_openetl'] == "SI") {
                                    $mBotAcc['mendesxx'] = "Transmitir a OpenETL";
                                  }
                                  ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_on-off_bg.gif" onClick = "javascript:fnTransmitirOpenPCE('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php }
                              break;
                              case "CONSULTAROPENETL":
                                if ($vSysStr['system_activar_openetl'] == "SI") { ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory ?>/etl_consultar.png" onClick = "javascript:fnConsultarETL('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php }
                              break;
                              case "NOENVIAROPENPCE":
                                if ($vSysStr['system_activar_openpce'] == "SI" || $vSysStr['system_activar_openetl'] == "SI") { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_warning_bg.png" onClick = "javascript:fnNoEnviarOpenPCE('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                              case "EDITAROBSERVACION":?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_global-changes_bg1.gif" onClick = "javascript:fnModificarObservacionPrefactura('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php
                              break;
                              case "EDITARORDEN":
                                if (f_InList($kDf[3],"TEOPENEBCO","DEOPENEBCO","OPENEBCO")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/page_go.png" onClick = "javascript:fnModificarOrdenPrefactura('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              case "WSPEDIDOSAP":
                                if (f_InList($kDf[3],"TEALPOPULP","ALPOPULX","ALMAVIVA","TEALMAVIVA")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/page_go.png" onClick = "javascript:fnWsPedidoSAP('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                              case "DESCARGARPDFETL":
                                if ($vSysStr['system_activar_openetl'] == "SI") { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_global-changes_bg.gif" onClick = "javascript:fnConsultarETL('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                              case "ENVIARCORREOHP":
                                if (f_InList($kDf[3],"SIACOSIA","TESIACOSIP","DESIACOSIP","DEDESARROL","TEPRUEBASX")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/page_go.png" onClick = "javascript:fnEnviarReporteXCorreoHP()" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                              case "DISCONFORMIDAD":
                                if (f_InList($kDf[3],"GRUMALCO","TEGRUMALCO","DEGRUMALCO")) { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_create-file_bg.gif" onClick = "javascript:fnAsignarDisconformidad()" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                <?php }
                              break;
                            }
                          }
                          /***** Fin Botones de Acceso Rapido *****/
                        ?>
                      </td>
                    </tr>
                  </table>
                </center>
                <hr></hr>
                <center>
                  <table cellspacing="0" width="100%">
                    <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
                      <?php
                        /** Si la variable del sistema system_activar_openpce se encuentra activa,
                          * mostrar una imagen que indique el estado de transmisión a la DIAN
                        */
                        if ($vSysStr['system_activar_openetl'] == "SI" || $vSysStr['system_activar_openpce'] == "SI" || f_InList($kDf[3],"ALMAVIVA","TEALMAVIVA","DEALMAVIVA")){ ?>
                          <td class="name" width="02%">
                            <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "">
                          </td>
                          <td class="name" width="06%">
                        <?php } else { ?>
                          <td class="name" width="08%">
                        <?php } ?>
                        <a href = "javascript:f_Order_By('onclick','comidxxx');" title="Ordenar">Comprobante</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "comidxxx">
                        <input type = "hidden" name = "comidxxx" value = "<?php echo $_POST['comidxxx'] ?>" id = "comidxxx">
                        <script language="javascript">f_Order_By('','comidxxx')</script>
                      </td>
                      <td class="name" width="06%">
                        <a href = "javascript:f_Order_By('onclick','comfpxxx');" title="Ordenar">Do</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "comfpxxx">
                        <input type = "hidden" name = "comfpxxx" value = "<?php echo $_POST['comfpxxx'] ?>" id = "comfpxxx">
                        <script language="javascript">f_Order_By('','comfpxxx')</script>
                      </td>
                      <td class="name" width="08%">
                        <a href = "javascript:f_Order_By('onclick','comfecxx');" title="Ordenar">Fecha</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "comfecxx">
                        <input type = "hidden" name = "comfecxx" value = "<?php echo $_POST['comfecxx'] ?>" id = "comfecxx">
                        <script language="javascript">f_Order_By('','comfecxx')</script>
                      </td>
                      <td class="name" width="06%">
                        <a href = "javascript:f_Order_By('onclick','reghcrex');" title="Ordenar">Hora</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "reghcrex">
                        <input type = "hidden" name = "reghcrex" value = "<?php echo $_POST['reghcrex'] ?>" id = "reghcrex">
                        <script language="javascript">f_Order_By('','reghcrex')</script>
                      </td>
                      <td class="name" width="06%">
                        <a href = "javascript:f_Order_By('onclick','comperxx');" title="Ordenar">Peri&oacute;do</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "comperxx">
                        <input type = "hidden" name = "comperxx" value = "<?php echo $_POST['comperxx'] ?>" id = "comperxx">
                        <script language="javascript">f_Order_By('','comperxx')</script>
                      </td>
                      <td class="name" width="08%">
                        <a href = "javascript:f_Order_By('onclick','ccoidxxx');" title="Ordenar">Centro Costo</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "ccoidxxx">
                        <input type = "hidden" name = "ccoidxxx" value = "<?php echo $_POST['ccoidxxx'] ?>" id = "ccoidxxx">
                        <script language="javascript">f_Order_By('','ccoidxxx')</script>
                      </td>
                      <td class="name" width="13%">
                        <a href = "javascript:f_Order_By('onclick','CLINOMXX');" title="Ordenar">Cliente</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "CLINOMXX">
                        <input type = "hidden" name = "CLINOMXX" value = "<?php echo $_POST['CLINOMXX'] ?>" id = "CLINOMXX">
                        <script language="javascript">f_Order_By('','CLINOMXX')</script>
                      </td>
                      <td class="name" width="13%">
                        <a href = "javascript:f_Order_By('onclick','PRONOMXX');" title="Ordenar">Facturar a</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "PRONOMXX">
                        <input type = "hidden" name = "PRONOMXX" value = "<?php echo $_POST['PRONOMXX'] ?>" id = "PRONOMXX">
                        <script language="javascript">f_Order_By('','PRONOMXX')</script>
                      </td>
                      <td class="name" width="12%">
                        <a href = "javascript:f_Order_By('onclick','USRNOMXX');" title="Ordenar">Usuario</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "USRNOMXX">
                        <input type = "hidden" name = "USRNOMXX" value = "<?php echo $_POST['USRNOMXX'] ?>" id = "USRNOMXX">
                        <script language="javascript">f_Order_By('','USRNOMXX')</script>
                      </td>
                      <td class="name" width="05%">
                        <a href = "javascript:f_Order_By('onclick','comvlrxx');" title="Ordenar">Valor</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "comvlrxx">
                        <input type = "hidden" name = "comvlrxx" value = "<?php echo $_POST['comvlrxx'] ?>" id = "ABS(comvlrxx)">
                        <script language="javascript">f_Order_By('','comvlrxx')</script>
                      </td>
                      <td class="name" width="10%">
                        <a href = "javascript:f_Order_By('onclick','comipfxx');" title="Ordenar">Estado Dos</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "comipfxx">
                        <input type = "hidden" name = "comipfxx" value = "<?php echo $_POST['comipfxx'] ?>" id = "comipfxx">
                        <script language="javascript">f_Order_By('','comipfxx')</script>
                      </td>
                      <td class="name" width="05%">
                        <a href = "javascript:f_Order_By('onclick','regestxx');" title="Ordenar">Estado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regestxx">
                        <input type = "hidden" name = "regestxx" value = "<?php echo $_POST['regestxx'] ?>" id = "regestxx">
                        <script language="javascript">f_Order_By('','regestxx')</script>
                      </td>
                      <td Class='name' width="02%" align="right">
                        <input type="checkbox" name="oChkComAll" onClick = 'javascript:f_Marca()'>
                      </td>
                    </tr>
                      <script languaje="javascript">
                        document.forms['frgrm']['vRecords'].value = "<?php echo count($mCabMov) ?>";
                      </script>

                      <?php
                        $y = 0; $nCan2242 = 0;
                        for ($i=0;$i<count($mCabMov);$i++) {
                          /**
                           * Buscando el primer DO
                           */
                          $mDo = f_Explode_Array($mCabMov[$i]['comfpxxx'],"|","~");
                          $mCabMov[$i]['doidxxxx'] = $mDo[0][15]."-".$mDo[0][2]."-".$mDo[0][3];

                          // Trampara para marcar las facturas como contabilizadas para cumplir con el modelo de ALPOPULAR para poder ANULAR una FACTURA
                          if (!f_InList($kDf[3],"TEALPOPULP","ALPOPULX","DEALPOPULX","TEUPSXXXXP","TEUPSXXXXX","UPSXXXXX","ALMACAFE","DEALMACAFE","TEALMACAFE","ALMAVIVA","TEALMAVIVA","DSVSASXX","TEDSVSASXX")) { $mCabMov[$i]['comealpo'] = "CONTABILIZADO"; }

                          if(f_InList($kDf[3],"TEUPSXXXXP","TEUPSXXXXX","UPSXXXXX")) {
                            //Si la factura es provisional el comealpo es pendiente
                            if (!($mCabMov[$i]['regestxx'] == "PROVISIONAL" || substr($mCabMov[$i]['comcscxx'], 0, 1) == "P" || substr($mCabMov[$i]['comcscxx'], 0, 1) == "T")) {
                              $mCabMov[$i]['comealpo'] = "CONTABILIZADO";
                            }
                          }
                          // Fin de Trampara para marcar las facturas

                          //Para alpopular se debe enviar si el proceso de facturación es normal o si fue por pedido sap
                          $cTipoFac = "";
                          if (f_InList($kDf[3], "DEALPOPULP", "TEALPOPULP", "ALPOPULX","ALMAVIVA","TEALMAVIVA")) {
                            $vComObs2 = explode("~",$mCabMov[$i]['comobs2x']);
                            $cTipoFac = $vComObs2[8];  //PEDIDOSAP - Indicador de factura por pedido de ALMAVIVA/ALPOPULAR
                          }

                          /***** Busco en la Tabla de Condiciones Comerciales de Alpopular el Esquema de Impresion *****/
                          $qCccDat  = "SELECT cccimpxx ";
                          $qCccDat .= "FROM $cAlfa.fpar0151 ";
                          $qCccDat .= "WHERE ";
                          $qCccDat .= "$cAlfa.fpar0151.cliidxxx = \"{$mCabMov[$i]['terid2xx']}\" ";
                          $xCccDat  = f_MySql("SELECT","",$qCccDat,$xConexion01,"");
                          $nFilCcc  = mysql_num_rows($xCccDat);
                          if ($nFilCcc > 0) {
                            $vCccDat  = mysql_fetch_array($xCccDat);
                          }
                          $mCabMov[$i]['teridixx'] = $vCccDat['cccimpxx'];
                          //f_Mensaje(__FILE__,__LINE__,$qCccDat);
                        /***** Fin busqueda de Condiciones Comerciales *****/

                        if ($mCabMov[$i]['compcevx'] == "2242") {
                          $nCan2242++;
                        }

                        if ($y <= count($mCabMov)) { // Para Controlar el Error
                          $zColor = "{$vSysStr['system_row_impar_color_ini']}";
                          if($y % 2 == 0) {
                            $zColor = "{$vSysStr['system_row_par_color_ini']}";
                          }

                          if ($mCabMov[$i]['comprnxx'] != "IMPRESO") {
                              $zColor = "#F6CECE";
                          }
                          ?>
                          <!--<tr bgcolor = "<?php echo $zColor ?>">-->
                          <tr id="<?php echo $mCabMov[$i]['comidxxx'].'-'.$mCabMov[$i]['comcodxx'].'-'.$mCabMov[$i]['comcscxx'].'-'.$mCabMov[$i]['comcsc2x'] ?>" bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')"
                            onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
                            <?php
                            /** Si la variable del sistema system_activar_openetl o system_activar_openpce se ecnuentra activa,
                             * mostrar una imagen que indique el estado de transmisión a la DIAN
                             */
                            if ($vSysStr['system_activar_openetl'] == "SI" || $vSysStr['system_activar_openpce'] == "SI") { ?>
                              <td class="letra7" align="center">
                                <?php
                                  if ($mCabMov[$i]['compcevx'] == "VP") {
                                    if ($mCabMov[$i]['compceen'] == "0000-00-00 00:00:00"){
                                      //VACIO
                                    }
                                    if ($mCabMov[$i]['compceen'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcees'] == "0000-00-00 00:00:00" && $mCabMov[$i]['compcesn'] == "EXITOSO"){
                                      echo "<img src=\"$cPlesk_Skin_Directory/etl_enviado.png\" border=\"0\" align=\"center\">";
                                    }
                                    if ($mCabMov[$i]['compceen'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcees'] == "0000-00-00 00:00:00" && $mCabMov[$i]['compcesn'] == "FALLIDO"){
                                      echo "<img src=\"$cPlesk_Skin_Directory/etl_error.png\" border=\"0\" align=\"center\">";
                                    }
                                    if ($mCabMov[$i]['compceen'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcees'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcesn'] == "FALLIDO" ){
                                      echo "<img src=\"$cPlesk_Skin_Directory/etl_error.png\" border=\"0\" align=\"center\">";
                                    }
                                    if ($mCabMov[$i]['compceen'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcees'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcere'] == "APROBADO" ){
                                      echo "<img src=\"$cPlesk_Skin_Directory/etl_aprobado.png\" border=\"0\" align=\"center\">";
                                    }
                                    if ($mCabMov[$i]['compceen'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcees'] != "0000-00-00 00:00:00" && ($mCabMov[$i]['compcere'] == "APROBADO_NOTIFICACION") ){
                                      echo "<img src=\"$cPlesk_Skin_Directory/etl_aprobado_notificacion.png\" border=\"0\" align=\"center\">";
                                    }
                                    if ($mCabMov[$i]['compceen'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcees'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcere'] == "FALLIDO" ){
                                      echo "<img src=\"$cPlesk_Skin_Directory/etl_error.png\" border=\"0\" align=\"center\">";
                                    }
                                    if ($mCabMov[$i]['compceen'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcees'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcesn'] == "NOENVIAR") {
                                      echo "<img src=\"$cPlesk_Skin_Directory/etl_no_enviar.png\" width=\"10px\" border=\"0\" align=\"center\">";
                                    }
                                  }

                                  if ($mCabMov[$i]['compcevx'] == "2242") {
                                    if ($mCabMov[$i]['compceen'] == "0000-00-00 00:00:00"){
                                      //VACIO
                                    }
                                    if ($mCabMov[$i]['compceen'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcesn'] == "FALLIDO" && $mCabMov[$i]['compcees'] == "0000-00-00 00:00:00"){
                                      echo "<img src=\"$cPlesk_Skin_Directory/pce_error.jpg\" border=\"0\" align=\"center\">";
                                    }elseif ($mCabMov[$i]['compceen'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcees'] == "0000-00-00 00:00:00"){
                                      echo "<img src=\"$cPlesk_Skin_Directory/pce_enviado.jpg\" border=\"0\" align=\"center\">";
                                    }
                                    if ($mCabMov[$i]['compceen'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcees'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcere'] == "EXITOSO" ){
                                      echo "<img src=\"$cPlesk_Skin_Directory/pce_exitoso.jpg\" border=\"0\" align=\"center\">";
                                    }
                                    if ($mCabMov[$i]['compceen'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcees'] != "0000-00-00 00:00:00" && ($mCabMov[$i]['compcere'] == "RECIBIDA" || $mCabMov[$i]['compcere'] == "VALIDACION") ){
                                      echo "<img src=\"$cPlesk_Skin_Directory/pce_recibido_validacion.jpg\" border=\"0\" align=\"center\">";
                                    }
                                    if ($mCabMov[$i]['compceen'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcees'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcere'] == "FALLIDO" ){
                                      echo "<img src=\"$cPlesk_Skin_Directory/pce_error.jpg\" border=\"0\" align=\"center\">";
                                    }
                                    if ($mCabMov[$i]['compceen'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcees'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcesn'] == "NOENVIAR") {
                                      echo "<img src=\"$cPlesk_Skin_Directory/pce_no_enviar.png\" width=\"10px\" border=\"0\" align=\"center\">";
                                    }
                                  }

                                  if ($mCabMov[$i]['compcevx'] == "") {
                                    if ($mCabMov[$i]['compceen'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcees'] != "0000-00-00 00:00:00" && $mCabMov[$i]['compcesn'] == "NOENVIAR") {
                                      echo "<img src=\"$cPlesk_Skin_Directory/etl_no_enviar.png\" width=\"10px\" border=\"0\" align=\"center\">";
                                    }
                                  }
                                ?>
                              </td>
                            <?php } elseif (f_InList($kDf[3],"ALMAVIVA","TEALMAVIVA","DEALMAVIVA")) {
                              //Para almaviva se debe mostrar el icono verde que indica que ya se transmitio el TXT al SFTP del PT
                              ?>
                              <td class="letra7" align="center">
                                <?php
                                if ($mCabMov[$i]['comfexml'] != "0000-00-00 00:00:00") {
                                  echo "<img src=\"$cPlesk_Skin_Directory/pce_exitoso.jpg\" border=\"0\" align=\"center\">";
                                }
                                ?>
                              </td>
                            <?php } ?>
                            <td class="letra7"><a href = javascript:f_Ver('<?php echo $mCabMov[$i]['comidxxx']?>','<?php echo $mCabMov[$i]['comcodxx']?>','<?php echo $mCabMov[$i]['comcscxx']?>','<?php echo $mCabMov[$i]['comcsc2x']?>','<?php echo $mCabMov[$i]['comfecxx']?>')>
                                                        <?php echo $mCabMov[$i]['comidxxx'].'-'.$mCabMov[$i]['comcodxx'].'-'.$mCabMov[$i]['comcscxx'] ?> </a></td>
                            <td class="letra7"><?php echo $mCabMov[$i]['doidxxxx'] ?></td>
                            <td class="letra7"><?php echo $mCabMov[$i]['comfecxx'] ?></td>
                            <td class="letra7"><?php echo $mCabMov[$i]['reghcrex'] ?></td>
                            <td class="letra7"><?php echo $mCabMov[$i]['comperxx'] ?></td>
                            <td class="letra7"><?php echo $mCabMov[$i]['ccoidxxx'] ?></td>
                            <td class="letra7"><?php echo substr($mCabMov[$i]['CLINOMXX'],0,28) ?></td>
                            <td class="letra7"><?php echo substr($mCabMov[$i]['PRONOMXX'],0,28) ?></td>
                            <td class="letra7"><?php echo substr($mCabMov[$i]['USRNOMXX'],0,28) ?></td>
                            <td class="letra7" align="right"><?php echo number_format($mCabMov[$i]['comvlrxx']) ?>&nbsp;</td>
                            <?php if ((f_InList($kDf[3],"TEALPOPULP","ALPOPULX") || $vSysStr['system_activar_integracion_sap_almaviva'] == "SI") && $cTipoFac == "PEDIDOSAP" && f_InList($mCabMov[$i]['comealpo'],"FACTURADO","RECHAZADO","NOTA_CREDITO")) { ?>
                              <td class="letra7">&nbsp;<a href="javascript:f_Estado_Sap('<?php echo $mCabMov[$i]['comidxxx']?>','<?php echo $mCabMov[$i]['comcodxx']?>','<?php echo $mCabMov[$i]['comcscxx']?>','<?php echo $mCabMov[$i]['comcsc2x']?>','<?php echo $mCabMov[$i]['comfecxx']?>')"><?php echo $mCabMov[$i]['comealpo']?></a></td>
                            <?php } else { ?>
                              <td class="letra7">&nbsp;<?php echo $mCabMov[$i]['comealpo'] ?></td>
                            <?php } ?>
                            <?php if($mCabMov[$i]['disidxxx'] != "" && f_InList($kDf[3], "GRUMALCO","DEGRUMALCO","TEGRUMALCO")){ ?>
                              <td class="letra7 tooltip" style="color: #FF0000" title="<?php echo $mCabMov[$i]['disidxxx'] ?>"><?php echo $mCabMov[$i]['regestxx'] ?></td>
                            <?php } else { ?>
                              <td class="letra7"><?php echo $mCabMov[$i]['regestxx'] ?></td>
                            <?php } ?>
                            <td Class="letra7" align="right">
                              <input type="checkbox" name="oChkCom" value = "<?php echo mysql_num_rows($xCabMov) ?>"
                              id="<?php echo $mCabMov[$i]['comidxxx'].'~'. //[0]
                                             $mCabMov[$i]['comcodxx'].'~'. //[1]
                                             $mCabMov[$i]['comcscxx'].'~'. //[2]
                                             $mCabMov[$i]['comcsc2x'].'~'. //[3]
                                             $mCabMov[$i]['comfecxx'].'~'. //[4]
                                             $mCabMov[$i]['regestxx'].'~'. //[5]
                                             $mCabMov[$i]['teridixx'].'~'. //[6]
                                             $mCabMov[$i]['comipfxx'].'~'. //[7]
                                             $mCabMov[$i]['comealpo'].'~'. //[8]
                                             $mCabMov[$i]['perestxx'].'~'. //[9]
                                             $mCabMov[$i]['comfecxx'].'~'. //[10]
                                             $cTipoFac  //[11] ?>"
                              onclick="javascript:document.forms['frgrm']['vRecords'].value='<?php echo count($mCabMov) ?>'">
                            </td>
                          </tr>
                          <?php $y++;
                        }
                      }
                      ?>
                  </table>
                  </center>
                  
                  <?php if ($nCan2242 > 0 || $vSysStr['system_activar_openetl'] == "SI") { ?>
                  <br>
                  <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                    <?php if ($nCan2242 > 0) {
                      echo "<td width=\"170px\"><b>Integraci&oacute;n openPCE (2242):&nbsp;&nbsp;</b></td>";
                      echo "<td class=\"name\" style=\"vertical-align: middle\"><img src=\"$cPlesk_Skin_Directory/pce_enviado.jpg\">&nbsp;Registrado</td>";
                      echo "<td class=\"name\" style=\"vertical-align: middle\"><img src=\"$cPlesk_Skin_Directory/pce_recibido_validacion.jpg\">&nbsp;Recibido/Validaci&oacute;n</td>";
                      echo "<td class=\"name\" style=\"vertical-align: middle\"><img src=\"$cPlesk_Skin_Directory/pce_exitoso.jpg\">&nbsp;Exitoso</td>";
                      echo "<td class=\"name\" style=\"vertical-align: middle\"><img src=\"$cPlesk_Skin_Directory/pce_error.jpg\">&nbsp;Con Errores</td>";
                      echo "<td class=\"name\" style=\"vertical-align: middle\"><img src=\"$cPlesk_Skin_Directory/pce_no_enviar.png\" width=\"10px\">&nbsp;No Enviado</td>";
                      echo "<td class=\"name\" style=\"vertical-align: middle\">&nbsp;</td>";
                    }
                    if ($vSysStr['system_activar_openetl'] == "SI"){
                      echo "<td width=\"160px\"><b>Integraci&oacute;n openETL (VP):&nbsp;&nbsp;</b></td>";
                      echo "<td class=\"name\" style=\"vertical-align: middle\"><img src=\"$cPlesk_Skin_Directory/etl_enviado.png\">&nbsp;Registrado</td>";
                      echo "<td class=\"name\" style=\"vertical-align: middle\"><img src=\"$cPlesk_Skin_Directory/etl_aprobado.png\">&nbsp;Aprobado</td>";
                      echo "<td class=\"name\" style=\"vertical-align: middle\"><img src=\"$cPlesk_Skin_Directory/etl_aprobado_notificacion.png\">&nbsp;Aprobado con Notificaci&oacute;n</td>";
                      echo "<td class=\"name\" style=\"vertical-align: middle\"><img src=\"$cPlesk_Skin_Directory/etl_error.png\">&nbsp;Con Errores</td>";
                      echo "<td class=\"name\" style=\"vertical-align: middle\"><img src=\"$cPlesk_Skin_Directory/etl_no_enviar.png\" width=\"10px\">&nbsp;No Enviado</td>";
                    } 
                    
                    if ($nCan2242==0 || $vSysStr['system_activar_openetl'] != "SI") {
                      echo "<td width=\"50%\">&nbsp;</td>";
                    } ?>
                    </tr>
                  </table>
                  <br>
                <?php }

                  //Para almaviva se debe mostrar el icono verde que indica que ya se transmitio el TXT al SFTP del PT
                  if (f_InList($kDf[3], "ALMAVIVA", "TEALMAVIVA", "DEALMAVIVA")) {
                    echo "<br><span class=\"name\" style=\"vertical-align: middle\"><img src=\"$cPlesk_Skin_Directory/pce_exitoso.jpg\">&nbsp;&nbsp;TXT enviado al PT<br></span>";
                  }
                  ?>
              </fieldset>
            </td>
          </tr>
        </table>
      </center>
    </form>
  </body>
</html>
