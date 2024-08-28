<?php
  /**
   * Tracking de Pedido.
   * --- Descripcion: Este programa permite litsar y consultar los registros de pedido que se encuentran en la Base de Datos
   * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @package opencomex
   * @version 001
   */
  include("../../../../../financiero/libs/php/utility.php");

  $kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlDb = $kDf[3];

  /* Busco en la 05 que Tiene Permiso el Usuario*/
  $qUsrMen  = "SELECT * ";
  $qUsrMen .= "FROM $cAlfa.sys00039 ";
  $qUsrMen .= "WHERE ";
  $qUsrMen .= "sys00039.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
  $qUsrMen .= "sys00039.proidxxx = \"{$_COOKIE['kProId']}\" AND ";
  $qUsrMen .= "sys00039.menimgon != \"\" ";
  $qUsrMen .= "ORDER BY sys00039.menordxx";
  $xUsrMen  = f_MySql("SELECT","",$qUsrMen,$xConexion01,"");
?>
<html>
  <head>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    <script language = "javascript">

      function fnVer(xPedId,xRegfcre) {
        var cPathUrl = "frpednue.php?cPedId="+xPedId+"&cAnio="+xRegfcre.substr(0,4);
        document.cookie = "kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie = "kMenDes=Ver Pedido;path="+"/";
        document.cookie = "kModo=VER;path="+"/";
        parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
        document.location = cPathUrl; // Invoco el menu.
      }

      function fnEditar(xModo) {
        switch (document.forms['frgrm']['vRecords'].value) {
          case "1":
            if (document.forms['frgrm']['oCheck'].checked == true) {
              var mComDat = document.forms['frgrm']['oCheck'].id.split('~');

              if (mComDat[6] == "PROVISIONAL" || mComDat[6] == "RECHAZADO") {
                var ruta = "frpednue.php?cPedId="+mComDat[0]+"&cAnio="+mComDat[1].substr(0,4);
                document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                document.cookie="kMenDes=Editar Pedido;path="+"/";
                document.cookie="kModo="+xModo+";path="+"/";
                parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                document.location = ruta; // Invoco el menu.
              } else {
                alert("El Pedido ["+mComDat[2]+"-"+mComDat[3]+"-"+mComDat[4]+"-"+mComDat[5]+"] No se encuentra en estado [PROVISIONAL] o [RECHAZADO] por tal motivo no se puede Editar,\nVerifique.");
              }
            }
          break;
          default:
            var zSw_Prv = 0;
            for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
              if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
                // Solo Deja Legalizar el Primero Seleccionado
                zSw_Prv = 1;
                var mComDat = document.forms['frgrm']['oCheck'][i].id.split('~');

                if (mComDat[6] == "PROVISIONAL" || mComDat[6] == "RECHAZADO") {
                  var ruta = "frpednue.php?cPedId="+mComDat[0]+"&cAnio="+mComDat[1].substr(0,4);
                  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                  document.cookie="kMenDes=Editar Pedido;path="+"/";
                  document.cookie="kModo="+xModo+";path="+"/";
                  parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                  document.location = ruta; // Invoco el menu.
                } else {
                  alert("El Pedido ["+mComDat[2]+"-"+mComDat[3]+"-"+mComDat[4]+"-"+mComDat[5]+"] No se encuentra en estado [PROVISIONAL] o [RECHAZADO] por tal motivo no se puede Editar,\nVerifique.");
                }
              }
            }
          break;
        }
      }

      function fnLink(xModId,xProId,xMenId,xForm,xOpcion,xMenDes) {
        document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie="kMenDes="+xMenDes+";path="+"/";
        document.cookie="kModo="+xOpcion+";path="+"/";
        parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
        document.location = xForm; // Invoco el menu.
      }

      function fnMarca() {
        if (document.forms['frgrm']['oCheckAll'].checked == true){
          if (document.forms['frgrm']['vRecords'].value == 1){
            document.forms['frgrm']['oCheck'].checked = true;
          } else {
            if (document.forms['frgrm']['vRecords'].value > 1){
              for (i = 0; i < document.forms['frgrm']['oCheck'].length; i++){
                document.forms['frgrm']['oCheck'][i].checked = true;
              }
            }
          }
        } else {
          if (document.forms['frgrm']['vRecords'].value == 1){
            document.forms['frgrm']['oCheck'].checked = false;
          } else {
            if (document.forms['frgrm']['vRecords'].value > 1){
              for (i = 0; i < document.forms['frgrm']['oCheck'].length; i++){
                document.forms['frgrm']['oCheck'][i].checked = false;
              }
            }
          }
        }
      }

      function fnOrderBy(xEvento, xCampo) {
        if (document.forms['frgrm'][xCampo].value != '') {
          var vSwitch = document.forms['frgrm'][xCampo].value.split(' ');
          var cSwitch = vSwitch[1];
        } else {
          var cSwitch = '';
        }

        if (xEvento == 'onclick') {
          switch (cSwitch) {
            case '':
              document.forms['frgrm'][xCampo].value = document.forms['frgrm'][xCampo].id + ' ASC,';
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_Logistic ?>/s_asc.png';
              if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
                document.forms['frgrm']['cOrderByOrder'].value += xCampo + "~";
              }
            break;
            case 'ASC,':
              document.forms['frgrm'][xCampo].value = document.forms['frgrm'][xCampo].id + ' DESC,';
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_Logistic ?>/s_desc.png';
              if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
                document.forms['frgrm']['cOrderByOrder'].value += xCampo + "~";
              }
            break;
            case 'DESC,':
              document.forms['frgrm'][xCampo].value = '';
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png';
              if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) >= 0) {
                document.forms['frgrm']['cOrderByOrder'].value = document.forms['frgrm']['cOrderByOrder'].value.replace(xCampo, "");
              }
            break;
          }

          document.forms['frgrm']['vSearch'].value=document.forms['frgrm']['vSearch'].value.toUpperCase();
          document.forms['frgrm']['vLimInf'].value='00';
          document.forms['frgrm']['vLimSup'].value='30';
          document.forms['frgrm']['vPaginas'].value='1';
          document.forms['frgrm'].submit();
        } else {
          switch (cSwitch) {
            case '':
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png';
            break;
            case 'ASC,':
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_Logistic ?>/s_asc.png';
            break;
            case 'DESC,':
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_Logistic ?>/s_desc.png';
            break;
          }
        }
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
        document.forms['frgrm'].action = 'frpedini.php';
        cWindow.focus();
      }

      function fnEnviarConsultaInducida(xDatos){
        document.forms['frgrm']['cPeriodos'].value     = xDatos['cPeriodos'];
        document.forms['frgrm']['dDesde'].value        = xDatos['dDesde'];
        document.forms['frgrm']['dHasta'].value        = xDatos['dHasta'];
        document.forms['frgrm']['cOfvSap'].value       = xDatos['cOfvSap'];
        document.forms['frgrm']['cUsrId'].value        = xDatos['cUsrId'];
        document.forms['frgrm']['cEstado'].value       = xDatos['cEstado'];
        document.forms['frgrm']['cConsecutivo'].value  = xDatos['cConsecutivo'];
        document.forms['frgrm']['cCerId'].value        = xDatos['cCerId'];
        document.forms['frgrm']['cCliId'].value        = xDatos['cCliId'];
        document.forms['frgrm']['cDepNum'].value       = xDatos['cDepNum'];
        document.forms['frgrm'].submit();
      }

      function fnImprimir() {
        var nCheck = 0;
        for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
          if (document.forms['frgrm']['oCheck'][i].checked == true) {
            nCheck++;
          }
        }

        if (nCheck == 1 || document.forms['frgrm']['oCheck'].checked == true) {
          var cRuta = 'frpedprn.php';

          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oCheck'].checked == true) {
                var mMatriz = document.forms['frgrm']['oCheck'].id.split('~');
                var cPedId  = mMatriz[0];
                var dComFec = mMatriz[1];
                var prints  = cPedId+'~'+dComFec;

                document.cookie="kIniAnt=frpedini.php;path="+"/";
                document.cookie="kMenDes=Imprimir Pedido;path="+"/";
                document.cookie="kModo=IMPRIMIR;path="+"/";
                parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                parent.fmwork.location = cRuta+"?prints="+prints; // Invoco el menu.
              }
            break;
            default:
              var zSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
                if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
                  zSw_Prv = 1;
                  var mMatriz = document.forms['frgrm']['oCheck'][i].id.split('~');
                  var cPedId  = mMatriz[0];
                  var dComFec = mMatriz[1];
                  var prints  = cPedId+'~'+dComFec;

                  document.cookie="kIniAnt=frpedini.php;path="+"/";
                  document.cookie="kMenDes=Imprimir Pedido;path="+"/";
                  document.cookie="kModo=IMPRIMIR;path="+"/";
                  parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                  parent.fmwork.location = cRuta+"?prints="+prints; // Invoco el menu.
                }
              }
            break;
          }
        } else {
          alert("Solo se permite seleccionar un registro.");
        }
      }

      function fnAnular(xModo) {
        var nCheck = 0
        for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
          if (document.forms['frgrm']['oCheck'][i].checked == true) {
            nCheck++;
          }
        }

        if (nCheck == 1 || document.forms['frgrm']['oCheck'].checked == true) {
          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oCheck'].checked == true) {
                var mComDat = document.forms['frgrm']['oCheck'].id.split("~");
                var nWidth  = 400;
                var nHeight = 200;

                if (mComDat[6] == "PROVISIONAL" || mComDat[6] == "RECHAZADO") {
                  if (confirm("Esta seguro que desea Anular el Pedido ["+mComDat[2]+"-"+mComDat[3]+"-"+mComDat[4]+"-"+mComDat[5]+"]?")) {
                    var cPathUrl = "frpedobs.php?gComId="+mComDat[2]+
                                                "&gComCod="+mComDat[3]+
                                                "&gComCsc="+mComDat[4]+
                                                "&gComCsc2="+mComDat[5]+
                                                "&gRegFCre="+mComDat[1]+
                                                "&gRegEst="+mComDat[6]+
                                                "&gComPre="+mComDat[7];
                    var nX       = screen.width;
                    var nY       = screen.height;
                    var nNx      = (nX-nWidth)/2;
                    var nNy      = (nY-nHeight)/2;
                    var cWinOpt  = "width="+nWidth+",scrollbars=1,height="+nHeight+",left="+nNx+",top="+nNy;

                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.cookie="kModo="+xModo+";path="+"/";
                    cWindow = window.open(cPathUrl,'cWinCam',cWinOpt);
                    cWindow.focus();
                  }
                } else {
                  alert("El Pedido ["+mComDat[2]+"-"+mComDat[3]+"-"+mComDat[4]+"-"+mComDat[5]+"] No se encuentra en estado [PROVISIONAL] o [RECHAZADO] por tal motivo no se puede Anular,\nVerifique.");
                }
              }
            break;
            default:
              var nSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
                if (document.forms['frgrm']['oCheck'][i].checked == true && nSw_Prv == 0) {
                  var mComDat = document.forms['frgrm']['oCheck'][i].id.split("~");
                  var nWidth  = 400;
                  var nHeight = 200;

                  if (mComDat[6] == "PROVISIONAL" || mComDat[6] == "RECHAZADO") {
                    if (confirm("Esta seguro que desea Anular el Pedido ["+mComDat[2]+"-"+mComDat[3]+"-"+mComDat[4]+"-"+mComDat[5]+"]?")) {
                      nSw_Prv = 1;
                      var cPathUrl = "frpedobs.php?gComId="+mComDat[2]+
                                                  "&gComCod="+mComDat[3]+
                                                  "&gComCsc="+mComDat[4]+
                                                  "&gComCsc2="+mComDat[5]+
                                                  "&gRegFCre="+mComDat[1]+
                                                  "&gRegEst="+mComDat[6]+
                                                  "&gComPre="+mComDat[7];
                      var nX       = screen.width;
                      var nY       = screen.height;
                      var nNx      = (nX-nWidth)/2;
                      var nNy      = (nY-nHeight)/2;
                      var cWinOpt  = "width="+nWidth+",scrollbars=1,height="+nHeight+",left="+nNx+",top="+nNy;

                      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                      document.cookie="kModo="+xModo+";path="+"/";
                      cWindow = window.open(cPathUrl,'cWinCam',cWinOpt);
                      cWindow.focus();
                    }
                  } else {
                    alert("El Pedido ["+mComDat[2]+"-"+mComDat[3]+"-"+mComDat[4]+"-"+mComDat[5]+"] No se encuentra [PROVISIONAL] o [RECHAZADO] por tal motivo no se puede Anular,\nVerifique.");
                  }
                }
              }
            break;
          }
        } else {
          alert("Solo se permite seleccionar un registro.");
        }
      }

      function fnTransmitirSAP(xModo) {
        var nCheck = 0
        for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
          if (document.forms['frgrm']['oCheck'][i].checked == true) {
            nCheck++;
          }
        }

        if (nCheck == 1 || document.forms['frgrm']['oCheck'].checked == true) {
          alert("En desarrollo...");
        } else {
          alert("Solo se permite seleccionar un registro.");
        }
      }

      function fnCargarAnexos(xModo) {
        var nCheck  = 0
        for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
          if (document.forms['frgrm']['oCheck'][i].checked == true) {
            nCheck++;
          }
        }

        if (nCheck == 1 || document.forms['frgrm']['oCheck'].checked == true) {
          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oCheck'].checked == true) {
                var mMatriz = document.forms['frgrm']['oCheck'].id.split('~');
                var nPedId    = mMatriz[0]; // Id Pedido
                var dFechaPed = mMatriz[1]; // Fecha de Creacion
                var cRegEst   = mMatriz[6]; // Estado
                if (cRegEst == "PROVISIONAL") {
                  var ruta = "../matinsfa/frcranue.php?nPedId="+nPedId+"&dFechaPed="+dFechaPed+"&cOrigen=PEDIDO";
                  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                  document.cookie="kMenDes=Cargar Anexos;path="+"/";
                  document.cookie="kModo="+xModo+";path="+"/";
                  parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                  document.location.ruta; // Invoco el menu.
                } else {
                  alert("El Pedido seleccionado no se encuentra PROVISIONAL");
                }
              }
            break;
            default:
              var zSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
                if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
                  // Solo Deja Legalizar el Primero Seleccionado
                  zSw_Prv = 1;
                  var mMatriz = document.forms['frgrm']['oCheck'][i].id.split('~');
                  var nPedId    = mMatriz[0]; // Id Pedido
                  var dFechaPed = mMatriz[1]; // Fecha de Creacion
                  var cRegEst   = mMatriz[6]; // Estado
                  if (cRegEst == "PROVISIONAL") {
                    var ruta = "../matinsfa/frcranue.php?nPedId="+nPedId+"&dFechaPed="+dFechaPed+"&cOrigen=PEDIDO";
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.cookie="kMenDes=Cargar Anexos;path="+"/";
                    document.cookie="kModo="+xModo+";path="+"/";
                    parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                    document.location = ruta; // Invoco el menu.
                  } else {
                    alert("El Pedido seleccionado no se encuentra PROVISIONAL");
                  }
                }
              }
            break;
          }
        } else {
          alert("Solo se permite seleccionar un registro.");
        }
      }

      function fnVerAnexos(xModo) {
        var nCheck  = 0
        for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
          if (document.forms['frgrm']['oCheck'][i].checked == true) {
            nCheck++;
          }
        }

        if (nCheck == 1 || document.forms['frgrm']['oCheck'].checked == true) {
          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oCheck'].checked == true) {
                var mMatriz = document.forms['frgrm']['oCheck'].id.split('~');
                var nPedId    = mMatriz[0]; // Id Pedido
                var dFechaPed = mMatriz[1]; // Fecha de Creacion
                var cRegEst   = mMatriz[6]; // Estado
                  if (cRegEst == "PROVISIONAL") {
                    var ruta = "../matinsfa/frvranue.php?nPedId="+nPedId+"&dFechaPed="+dFechaPed+"&cOrigen=PEDIDO";
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.cookie="kMenDes=Ver Anexos;path="+"/";
                    document.cookie="kModo="+xModo+";path="+"/";
                    parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                    document.location.ruta; // Invoco el menu.
                  } else {
                    alert("El Pedido seleccionado no se encuentra PROVISIONAL");
                  }
                }
            break;
            default:
              var zSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
                if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
                  // Solo Deja Legalizar el Primero Seleccionado
                  zSw_Prv = 1;
                  var mMatriz = document.forms['frgrm']['oCheck'][i].id.split('~');
                  var nPedId    = mMatriz[0]; // Id Pedido
                  var dFechaPed = mMatriz[1]; // Fecha de Creacion
                  var cRegEst   = mMatriz[6]; // Estado
                  if (cRegEst == "PROVISIONAL") {
                    var ruta = "../matinsfa/frvranue.php?nPedId="+nPedId+"&dFechaPed="+dFechaPed+"&cOrigen=PEDIDO";
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.cookie="kMenDes=Ver Anexos;path="+"/";
                    document.cookie="kModo="+xModo+";path="+"/";
                    parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                    document.location = ruta; // Invoco el menu.
                  } else {
                    alert("El Pedido seleccionado no se encuentra PROVISIONAL");
                  }
                }
              }
            break;
          }
        } else {
          alert("Solo se permite seleccionar un registro.");
        }
      }

      function fnDevolucion(xModo) {
        var nCheck = 0
        for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
          if (document.forms['frgrm']['oCheck'][i].checked == true) {
            nCheck++;
          }
        }

        if (nCheck == 1 || document.forms['frgrm']['oCheck'].checked == true) {
          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oCheck'].checked == true) {
                var mComDat = document.forms['frgrm']['oCheck'].id.split("~");
                var nWidth  = 400;
                var nHeight = 200;

                if (mComDat[6] == "PROVISIONAL") {
                  if (confirm("Esta seguro que desea realizar la Devolucion del Pedido ["+mComDat[2]+"-"+mComDat[3]+"-"+mComDat[4]+"-"+mComDat[5]+"]?")) {
                    var cPathUrl = "frpedobs.php?gComId="+mComDat[2]+
                                                "&gComCod="+mComDat[3]+
                                                "&gComCsc="+mComDat[4]+
                                                "&gComCsc2="+mComDat[5]+
                                                "&gRegFCre="+mComDat[1]+
                                                "&gRegEst="+mComDat[6]+
                                                "&gComPre="+mComDat[7];
                    var nX       = screen.width;
                    var nY       = screen.height;
                    var nNx      = (nX-nWidth)/2;
                    var nNy      = (nY-nHeight)/2;
                    var cWinOpt  = "width="+nWidth+",scrollbars=1,height="+nHeight+",left="+nNx+",top="+nNy;

                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.cookie="kModo="+xModo+";path="+"/";
                    cWindow = window.open(cPathUrl,'cWinCam',cWinOpt);
                    cWindow.focus();
                  }
                } else {
                  alert("El Pedido ["+mComDat[2]+"-"+mComDat[3]+"-"+mComDat[4]+"-"+mComDat[5]+"] No se encuentra en estado [PROVISIONAL] por tal motivo no se puede realizar el proceso de Devolucion,\nVerifique.");
                }
              }
            break;
            default:
              var nSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
                if (document.forms['frgrm']['oCheck'][i].checked == true && nSw_Prv == 0) {
                  var mComDat = document.forms['frgrm']['oCheck'][i].id.split("~");
                  var nWidth  = 400;
                  var nHeight = 200;

                  if (mComDat[6] == "PROVISIONAL") {
                    if (confirm("Esta seguro que desea realizar la Devolucion del Pedido ["+mComDat[2]+"-"+mComDat[3]+"-"+mComDat[4]+"-"+mComDat[5]+"]?")) {
                      nSw_Prv = 1;
                      var cPathUrl = "frpedobs.php?gComId="+mComDat[2]+
                                                  "&gComCod="+mComDat[3]+
                                                  "&gComCsc="+mComDat[4]+
                                                  "&gComCsc2="+mComDat[5]+
                                                  "&gRegFCre="+mComDat[1]+
                                                  "&gRegEst="+mComDat[6]+
                                                  "&gComPre="+mComDat[7];
                      var nX       = screen.width;
                      var nY       = screen.height;
                      var nNx      = (nX-nWidth)/2;
                      var nNy      = (nY-nHeight)/2;
                      var cWinOpt  = "width="+nWidth+",scrollbars=1,height="+nHeight+",left="+nNx+",top="+nNy;

                      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                      document.cookie="kModo="+xModo+";path="+"/";
                      cWindow = window.open(cPathUrl,'cWinCam',cWinOpt);
                      cWindow.focus();
                    }
                  } else {
                    alert("El Pedido ["+mComDat[2]+"-"+mComDat[3]+"-"+mComDat[4]+"-"+mComDat[5]+"] No se encuentra [PROVISIONAL] por tal motivo no se puede realizar el proceso de Devolucion,\nVerifique.");
                  }
                }
              }
            break;
          }
        } else {
          alert("Solo se permite seleccionar un registro.");
        }
      }
    
    </script>
  </head>
  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
    <form name = "frestado" action = "frpedgra.php" method = "post" target="fmpro">
      <input type = "hidden" name = "cPedId"       value = "">
      <input type = "hidden" name = "gComId"       value = "">
      <input type = "hidden" name = "gComCod"      value = "">
      <input type = "hidden" name = "gComPre"      value = "">
      <input type = "hidden" name = "gComCsc"      value = "">
      <input type = "hidden" name = "gComCsc2"     value = "">
      <input type = "hidden" name = "cAnio"        value = "">
      <input type = "hidden" name = "gRegEst"      value = "">
      <input type = "hidden" name = "gObservacion" id="gObservacion">
    </form>

    <form name = "frgrm" action = "frpedini.php" method = "post" target="fmwork">
      <input type = "hidden" name = "vRecords"   value = "">
      <input type = "hidden" name = "vLimInf"    value = "<?php echo $vLimInf ?>">
      <input type = "hidden" name = "vSortField" value = "<?php echo $vSortField ?>">
      <input type = "hidden" name = "vSortType"  value = "<?php echo $vSortType ?>">
      <input type = "hidden" name = "vTimes"     value = "<?php echo $vTimes ?>">
      <input type = "hidden" name = "vBuscar"    value = "<?php echo $_POST['vBuscar'] ?>">
      <input type = "hidden" name = "cOrderByOrder"  value = "<?php echo $_POST['cOrderByOrder'] ?>" style = "width:1000">
      <!--Campos ocultos de la consulta inducida-->
      <input type = "hidden" name = "cConsecutivo"  value = "<?php echo $cConsecutivo ?>">
      <input type = "hidden" name = "cCerId"        value = "<?php echo $cCerId ?>">
      <input type = "hidden" name = "cCliId"        value = "<?php echo $cCliId ?>">
      <input type = "hidden" name = "cDepNum"       value = "<?php echo $cDepNum ?>">

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
                        // Empiezo a Leer la sys00039
                        while($mUsrMen = mysql_fetch_array($xUsrMen)) {
                          if($y == 0 || $y % 5 == 0) {
                            if ($y == 0) {?>
                              <tr>
                            <?php } else { ?>
                              </tr><tr>
                            <?php }
                          }
                          // Busco de la sys00039 en la sys00040
                          $qUsrPer  = "SELECT * ";
                          $qUsrPer .= "FROM $cAlfa.sys00040 ";
                          $qUsrPer .= "WHERE ";
                          $qUsrPer .= "usridxxx = \"{$_COOKIE['kUsrId']}\" AND ";
                          $qUsrPer .= "modidxxx = \"{$mUsrMen['modidxxx']}\"  AND ";
                          $qUsrPer .= "proidxxx = \"{$mUsrMen['proidxxx']}\"  AND ";
                          $qUsrPer .= "menidxxx = \"{$mUsrMen['menidxxx']}\"  LIMIT 0,1";
                          $xUsrPer = f_MySql("SELECT","",$qUsrPer,$xConexion01,"");
                          if (mysql_num_rows($xUsrPer) > 0) { ?>
                            <td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/<?php echo $mUsrMen['menimgon'] ?>" style = "cursor:pointer" onClick ="javascript:fnLink('<?php echo $mUsrMen['modidxxx'] ?>','<?php echo $mUsrMen['proidxxx'] ?>','<?php echo $mUsrMen['menidxxx'] ?>','<?php echo $mUsrMen['menformx']?>','<?php echo $mUsrMen['menopcxx']?>','<?php echo $mUsrMen['mendesxx']?>')"><br>
                            <a href = "javascript:fnLink('<?php echo $mUsrMen['modidxxx'] ?>','<?php echo $mUsrMen['proidxxx'] ?>','<?php echo $mUsrMen['menidxxx'] ?>','<?php echo $mUsrMen['menformx']?>','<?php echo $mUsrMen['menopcxx']?>','<?php echo $mUsrMen['mendesxx']?>')"
                              style="color:<?php echo $vSysStr['system_link_menu_color'] ?>"><?php echo $mUsrMen['mendesxx'] ?></a></center></td>
                          <?php } else { ?>
                            <td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/<?php echo $mUsrMen['menimgof']?>"><br>
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
        } elseif ($vLimInf == "") {
          $vLimInf = "00";
        }

        if (substr_count($vLimInf,"-") > 0) {
          $vLimInf = "00";
        }

        if ($vPaginas == "") {
          $vPaginas = "1";
        }

        // Si viene vacio el $cOfvSap lo cargo con la Cookie de la Oficina de Venta
        // Si no hago el SELECT con el Oficina de Venta que me entrega el combo del INI
        if (empty($cOfvSap)) {
          $cOfvSap  = "";
        } else {
          // Si el $cOfvSap viene cargado del combo con "ALL" es porque Debo mostrar todos las Oficina de Venta
          // Si no dejo la Oficina de Venta que viene cargada
          if ($cOfvSap == "ALL") {
            $cOfvSap = "";
          }
        }

        /**INICIO SQL**/
        if ($_POST['cPeriodos'] == "") {
          $_POST['cPeriodos'] == "20";
          $_POST['dDesde'] = substr(date('Y-m-d'),0,8)."01";
          $_POST['dHasta'] = date('Y-m-d');
        }

        $nAnioDesde = substr($_POST['dDesde'], 0, 4);
        $nAnioDesde = ($nAnioDesde < $vSysStr['logistica_ano_instalacion_modulo']) ? $vSysStr['logistica_ano_instalacion_modulo'] : $nAnioDesde;

        $mPedido = array();
        for ($iAno = $nAnioDesde; $iAno <= substr($_POST['dHasta'],0,4); $iAno++) { // Recorro desde el anio de inicio hasta el anio de fin de la consulta

          if ($iAno == $nAnioDesde) {
            $qPedido  = "(SELECT DISTINCT ";
            $qPedido .= "SQL_CALC_FOUND_ROWS ";
          }else {
            $qPedido  .= "(SELECT DISTINCT ";
          }

          $qPedido .= "$cAlfa.lpca$iAno.pedidxxx, ";   // Id Pedido
          $qPedido .= "$cAlfa.lpca$iAno.comidxxx, ";   // Id del Comprobante
          $qPedido .= "$cAlfa.lpca$iAno.comcodxx, ";   // Codigo del Comprobante
          $qPedido .= "$cAlfa.lpca$iAno.comprexx, ";   // Prefijo
          $qPedido .= "$cAlfa.lpca$iAno.comcscxx, ";   // Consecutivo Uno
          $qPedido .= "$cAlfa.lpca$iAno.comcsc2x, ";   // Consecutivo Dos
          $qPedido .= "$cAlfa.lpca$iAno.comfecxx, ";   // Fecha Pedido
          $qPedido .= "$cAlfa.lpca$iAno.cliidxxx,";    // Id cliente
          $qPedido .= "$cAlfa.lpca$iAno.pedtipxx,";    // Tipo de Pedido
          $qPedido .= "$cAlfa.lpca$iAno.pedmemde,";    // Campo Memo Depositos 
          $qPedido .= "$cAlfa.lpca$iAno.pedvlrxx,";    // Valor del Pedido
          $qPedido .= "$cAlfa.lpca$iAno.regessap,";    // Estado del Pedido en SAP
          $qPedido .= "$cAlfa.lpar0150.clisapxx, ";    // Codigo SAP
          $qPedido .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,REPLACE(CONCAT($cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x), \"  \", \" \")) AS clinomxx, "; // Nombre Cliente
          $qPedido .= "$cAlfa.lpca$iAno.regusrxx, ";   // Usuario que creo el registro
          $qPedido .= "$cAlfa.lpca$iAno.regfcrex, ";   // Fecha de creación
          $qPedido .= "$cAlfa.lpca$iAno.reghcrex, ";   // Hora de creación
          $qPedido .= "$cAlfa.lpca$iAno.regfmodx, ";   // Fecha de modificación
          $qPedido .= "$cAlfa.lpca$iAno.reghmodx, ";   // Hora de modificación
          $qPedido .= "$cAlfa.lpca$iAno.regestxx ";   // Estado
          $qPedido .= "FROM $cAlfa.lpca$iAno ";
          $qPedido .= "LEFT JOIN $cAlfa.lpar0150 ON lpca$iAno.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
          $qPedido .= "WHERE ";
          if ($_POST['vSearch'] != "") {
            if ($_POST['cBusExc'] == "SI") {
              $prefijo     = substr($_POST['vSearch'], 0, 3);
              $consecutivo = substr($_POST['vSearch'], 3);

              $qPedido .= "$cAlfa.lpca$iAno.comprexx = \"$prefijo\" AND ";
              $qPedido .= "$cAlfa.lpca$iAno.comcscxx = \"$consecutivo\" AND ";
            } else {
              $qPedido .= "(";
              $qPedido .= "CONCAT($cAlfa.lpca$iAno.comprexx ,\"\",$cAlfa.lpca$iAno.comcscxx ) LIKE \"%{$_POST['vSearch']}%\" OR ";
              $qPedido .= "$cAlfa.lpca$iAno.cliidxxx LIKE \"%{$_POST['vSearch']}%\" OR ";
              $qPedido .= "$cAlfa.lpar0150.clisapxx LIKE \"%{$_POST['vSearch']}%\" OR ";
              $qPedido .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,REPLACE(CONCAT($cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x), \"  \", \" \")) LIKE \"%{$_POST['vSearch']}%\" OR ";
              $qPedido .= "$cAlfa.lpca$iAno.regestxx LIKE \"%{$_POST['vSearch']}%\") AND ";
            }
          }

          // Campos de la Consulta inducida
          // Buscando por la Certificacion
          if ($_POST['cCerId'] != "") {
            $qPedido .= "$cAlfa.lpca$iAno.pedmemod LIKE \"%{$_POST['cCerId']}%\" AND ";
          }
          // Buscando por consecutivo exacto o contenido
          if ($_POST['cConsecutivo'] != "") {
            $qPedido .= "$cAlfa.lpca$iAno.comcscxx = \"{$_POST['cConsecutivo']}\" AND ";
          }
          // Buscando por Cliente
          if ($_POST['cCliId'] != "") {
            $qPedido .= "$cAlfa.lpca$iAno.cliidxxx = \"{$_POST['cCliId']}\" AND ";
          }
          // Buscando por Deposito
          if ($_POST['cDepNum'] != "") {
            $qPedido .= "$cAlfa.lpca$iAno.pedmemde LIKE \"%{$_POST['cDepNum']}%\" AND ";
          }
          // Fin Campos de la Consulta inducida

          // Consulta por la oficina de venta
          if ($cOfvSap != "") {
            $qPedido .= "$cAlfa.lpca$iAno.ofvsapxx = \"$cOfvSap\" AND ";
          }
          // Consulta por el usuario
          if ($cUsrId != "" && $cUsrId != "ALL") {
            $qPedido .= "$cAlfa.lpca$iAno.regusrxx = \"$cUsrId\" AND ";
          }
          // Consulta por el estado
          if ($cEstado != "") {
            $qPedido .= "$cAlfa.lpca$iAno.regestxx = \"$cEstado\" AND ";
          }
          $qPedido .= "$cAlfa.lpca$iAno.regfcrex BETWEEN \"{$_POST['dDesde']}\" AND \"{$_POST['dHasta']}\" ) ";
          /***** FIN SQL *****/

          if ($iAno >= $nAnioDesde && $iAno < substr($_POST['dHasta'],0,4)) {
            $qPedido .= " UNION ";
          }
        } ## for ($iAno=$nAnioDesde;$iAno<=substr($_POST['dHasta'],0,4);$iAno++) { ##

        //// CODIGO NUEVO PARA ORDER BY
        $cOrderBy = "";
        $vOrderByOrder = explode("~", $_POST['cOrderByOrder']);
        for ($z = 0; $z < count($vOrderByOrder); $z++) {
          if ($vOrderByOrder[$z] != "") {
            if ($_POST[$vOrderByOrder[$z]] != "") {
              $cOrderBy .= $_POST[$vOrderByOrder[$z]];
            }
          }
        }
        if (strlen($cOrderBy) > 0) {
          $cOrderBy = substr($cOrderBy, 0, strlen($cOrderBy) - 1);
          $cOrderBy = "ORDER BY " . $cOrderBy;
        } else {
          $cOrderBy = "ORDER BY regfmodx DESC ";
        }
        //// FIN CODIGO NUEVO PARA ORDER BY
        $qPedido .= "$cOrderBy LIMIT $vLimInf,$vLimSup ";
        $xPedido  = f_MySql("SELECT","",$qPedido,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qPedido."~".mysql_num_rows($xPedido));
        // echo $qPedido."~".mysql_num_rows($xPedido);

        $xNumRows = mysql_query("SELECT FOUND_ROWS();", $xConexion01);
        $xRNR = mysql_fetch_array($xNumRows);
        $nRNR += $xRNR['FOUND_ROWS()'];

        $mMatrizUsr = array();
        $mExisteUsr = array();
        while ($xRPE = mysql_fetch_array($xPedido)) {
          if (in_array($xRPE['regusrxx'], $mExisteUsr)) {
            $xRPE['usrnomxx'] = $mExisteUsr[$xRPE['regusrxx']]['clinomxx'];
          } else {
            // Busco la informacion del usuario autenticado
            $qUsrNom  = "SELECT USRIDXXX, USRNOMXX, REGESTXX ";
            $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
            $qUsrNom .= "WHERE ";
            $qUsrNom .= "USRIDXXX = \"{$xRPE['regusrxx']}\"";
            $xUsrNom = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
            if (mysql_num_rows($xUsrNom) > 0) {
              $vUsrNom = mysql_fetch_array($xUsrNom);
              $nInd_mMatrizUsr = count($mMatrizUsr);
              $mMatrizUsr[$nInd_mMatrizUsr]['usridxxx'] = $vUsrNom['USRIDXXX'];
              $mMatrizUsr[$nInd_mMatrizUsr]['usrnomxx'] = $vUsrNom['USRNOMXX'];
              $mMatrizUsr[$nInd_mMatrizUsr]['regestxx'] = $vUsrNom['REGESTXX'];
              
              $mExisteUsr[$xRPE['USRIDXXX']][] = $vCliente;
              $xRPE['usrnomxx'] = $vUsrNom['USRNOMXX'];
            }
          }

          // Obtiene la identificacion de los depositos
          $vDeposito  = explode("~", $xRPE['pedmemde']);

          $iAno = substr($xRPE['regfcrex'], 0, 4);
          // Obtiene las fecha inicial y final de la primera certificacion del detalle
          $qDetPedido  = "SELECT ";
          $qDetPedido .= "$cAlfa.lpde$iAno.peddidxx, ";
          $qDetPedido .= "$cAlfa.lpde$iAno.cerfdexx, ";
          $qDetPedido .= "$cAlfa.lpde$iAno.cerfhaxx, ";
          $qDetPedido .= "$cAlfa.lpde$iAno.pedfdexx, ";
          $qDetPedido .= "$cAlfa.lpde$iAno.pedfhaxx ";
          $qDetPedido .= "FROM $cAlfa.lpde$iAno ";
          $qDetPedido .= "WHERE ";
          $qDetPedido .= "lpde$iAno.pedidxxx = \"{$xRPE['pedidxxx']}\" ";
          $qDetPedido .= "ORDER BY $cAlfa.lpde$iAno.peddidxx ASC LIMIT 0,1 ";
          $xDetPedido  = f_MySql("SELECT","",$qDetPedido,$xConexion01,"");
          $vDetPedido  = array();
          if (mysql_num_rows($xDetPedido) > 0) {
            $vDetPedido = mysql_fetch_array($xDetPedido);
          }

          $xRPE['depnumxx'] = implode(", ", $vDeposito);
          $xRPE['fechinix'] = ($xRPE['pedtipxx'] == "AUTOMATICA") ? $vDetPedido['cerfdexx'] : $vDetPedido['pedfdexx'];
          $xRPE['fechhast'] = ($xRPE['pedtipxx'] == "AUTOMATICA") ? $vDetPedido['cerfhaxx'] : $vDetPedido['pedfhaxx'];
          $mPedido[count($mPedido)] = $xRPE;
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
                        <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_search.png" style = "cursor:pointer" title="Buscar"
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
                        <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_show-all_bg.gif" style = "cursor:hand" title="Mostrar Todo"
                          onClick ="javascript:document.forms['frgrm']['vSearch'].value='';
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
                                              document.forms['frgrm']['cOfvSap'].value='';
                                              document.forms['frgrm']['cUsrId'].value='';
                                              document.forms['frgrm']['cEstado'].value='';
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
                          onfocus = "javascript:document.forms['frgrm']['vPaginas'].value='1'"
                          onblur = "javascript:f_FixFloat(this);
                                                document.forms['frgrm']['vLimInf'].value='00';
                                                document.forms['frgrm'].submit()">
                      </td>
                      <td class="name" width="05%" align="center">
                        <?php if (ceil($nRNR/$vLimSup) > 1) { ?>
                          <?php if ($vPaginas == "1") { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_nextpage.png"    style = "cursor:pointer" title="Pagina Siguiente"
                              onClick = "javascript:document.frgrm.vPaginas.value++;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_lastpage.png"    style = "cursor:pointer" title="Ultima Pagina"
                              onClick = "javascript:document.frgrm.vPaginas.value='<?php echo ceil($nRNR/$vLimSup) ?>';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                          <?php } ?>
                          <?php if ($vPaginas > "1" && $vPaginas < ceil($nRNR/$vLimSup)) { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
                              onClick = "javascript:document.frgrm.vPaginas.value='1';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
                              onClick = "javascript:document.frgrm.vPaginas.value--;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente"
                              onClick = "javascript:document.frgrm.vPaginas.value++;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina"
                              onClick = "javascript:document.frgrm.vPaginas.value='<?php echo ceil($nRNR/$vLimSup) ?>';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                          <?php } ?>
                          <?php if ($vPaginas == ceil($nRNR/$vLimSup)) { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
                              onClick = "javascript:document.frgrm.vPaginas.value='1';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
                              onClick = "javascript:document.frgrm.vPaginas.value--;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_nextpage.png" style = "cursor:pointer" title="Pagina Siguiente">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_lastpage.png" style = "cursor:pointer" title="Ultima Pagina">
                          <?php } ?>
                        <?php } else { ?>
                          <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
                          <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
                          <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente">
                          <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina">
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
                        <select Class = "letrase" name = "cOfvSap" value = "<?php echo $cOfvSap ?>" style = "width:99%">
                          <option value = "ALL" selected>OFICINA DE VENTAS</option>
                          <?php
                            $qOfiVenta  = "SELECT ";
                            $qOfiVenta .= "orvsapxx, ";
                            $qOfiVenta .= "ofvsapxx, ";
                            $qOfiVenta .= "ofvdesxx ";
                            $qOfiVenta .= "FROM $cAlfa.lpar0002 ";
                            $qOfiVenta .= "WHERE ";
                            $qOfiVenta .= "regestxx = \"ACTIVO\" ORDER BY ofvdesxx";
                            $xOfiVenta = f_MySql("SELECT","",$qOfiVenta,$xConexion01,"");
                            if (mysql_num_rows($xOfiVenta) > 0) {
                              while ($xROV = mysql_fetch_array($xOfiVenta)) {
                                if ($xROV['ofvsapxx'] == $cOfvSap) { ?>
                                  <option value = "<?php echo $xROV['ofvsapxx']?>" selected><?php echo $xROV['ofvdesxx'] ?></option>
                                <?php } else { ?>
                                  <option value = "<?php echo $xROV['ofvsapxx']?>"><?php echo $xROV['ofvdesxx'] ?></option>
                                <?php }
                              }
                            }
                          ?>
                        </select>
                      </td>

                      <td class="name" width="13%" align="left">
                        <select Class = "letrase" name = "cUsrId" value = "<?php echo $cUsrId ?>" style = "width:99%" >
                          <option value = "ALL" selected>USUARIOS</option>
                          <?php
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
                        <select Class = "letrase" name = "cEstado" value = "<?php echo $cEstado ?>" style = "width:99%" >
                          <option value = "">ESTADO</option>
                          <option value = "PENDIENTE">PENDIENTE</option>
                          <option value = "ACTIVO">ACTIVO</option>
                          <option value = "FACTURADO">FACTURADO</option>
                          <option value = "ANULADO">ANULADO</option>
                          <option value = "RECHAZADO">RECHAZADO</option>
                          <option value = "NOTA_CREDITO">NOTA CREDITO</option>
                        </select>
                        <script language='javascript'>
                          document.forms['frgrm']['cEstado'].value = "<?php echo $cEstado ?>";
                        </script>
                      </td>

                      <td Class="name" align="right">&nbsp;
                        <?php
                          /***** Botones de Acceso Rapido *****/
                          $qBotAcc  = "SELECT * ";
                          $qBotAcc .= "FROM $cAlfa.sys00039,$cAlfa.sys00040 ";
                          $qBotAcc .= "WHERE ";
                          $qBotAcc .= "sys00040.usridxxx = \"{$_COOKIE['kUsrId']}\" AND ";
                          $qBotAcc .= "sys00040.modidxxx = sys00039.modidxxx        AND ";
                          $qBotAcc .= "sys00040.proidxxx = sys00039.proidxxx        AND ";
                          $qBotAcc .= "sys00040.menidxxx = sys00039.menidxxx        AND ";
                          $qBotAcc .= "sys00040.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
                          $qBotAcc .= "sys00040.proidxxx = \"{$_COOKIE['kProId']}\" ";
                          $qBotAcc .= "ORDER BY sys00039.menordxx";

                          $xBotAcc  = f_MySql("SELECT","",$qBotAcc,$xConexion01,"");
                          // f_Mensaje(__FILE__, __LINE__, $qBotAcc."~".mysql_num_rows($xBotAcc));
                          while ($mBotAcc = mysql_fetch_array($xBotAcc)) {
                            switch ($mBotAcc['menopcxx']) {
                              case "EDITAR": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_edit.png" onClick = "javascript:fnEditar('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "IMPRIMIR": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_print.png" onClick = "javascript:fnImprimir('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "ANULAR": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_drop.png" onClick = "javascript:fnAnular('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "TRANSMITIRSAP": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/transmitir_sap.png" onClick = "javascript:fnTransmitirSAP('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "CARGARANEXOS": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/cargar_anexos.png" onClick = "javascript:fnCargarAnexos('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "VERANEXOS": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/ver_anexos.png" onClick = "javascript:fnVerAnexos('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "DEVOLUCION": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/devolucion.png" onClick = "javascript:fnDevolucion('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
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
                      <td class="name" width="08%">
                        <a href = "javascript:fnOrderBy('onclick','pedidxxx');" title="Ordenar">No. Pedido</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "pedidxxx">
                        <input type = "hidden" name = "pedidxxx" value = "<?php echo $_POST['pedidxxx'] ?>" id = "pedidxxx">
                        <script language="javascript">fnOrderBy('','pedidxxx')</script>
                      </td>
                      <td class="name" width="09%">
                        <a href = "javascript:fnOrderBy('onclick','comcscxx');" title="Ordenar">Consecutivo SAP</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "comcscxx">
                        <input type = "hidden" name = "comcscxx" value = "<?php echo $_POST['comcscxx'] ?>" id = "comcscxx">
                        <script language="javascript">fnOrderBy('','comcscxx')</script>
                      </td>
                      <td class="name" width="08%">
                        <a href = "javascript:fnOrderBy('onclick','comfecxx');" title="Ordenar">Fecha Pedido</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "comfecxx">
                        <input type = "hidden" name = "comfecxx" value = "<?php echo $_POST['comfecxx'] ?>" id = "comfecxx">
                        <script language="javascript">fnOrderBy('','comfecxx')</script>
                      </td>
                      <td class="name" width="06%">
                        <a href = "javascript:fnOrderBy('onclick','cliidxxx');" title="Ordenar">Nit</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "cliidxxx">
                        <input type = "hidden" name = "cliidxxx" value = "<?php echo $_POST['cliidxxx'] ?>" id = "cliidxxx">
                        <script language="javascript">fnOrderBy('','cliidxxx')</script>
                      </td>
                      <td class="name" width="15%">
                        <a href = "javascript:fnOrderBy('onclick','clinomxx');" title="Ordenar">Cliente</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "clinomxx">
                        <input type = "hidden" name = "clinomxx" value = "<?php echo $_POST['clinomxx'] ?>" id = "clinomxx">
                        <script language="javascript">fnOrderBy('','clinomxx')</script>
                      </td>
                      <td class="name" width="09%">
                        <a href = "" title="Ordenar">Deposito</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "depnumxx">
                        <input type = "hidden" name = "depnumxx" value = "<?php echo $_POST['depnumxx'] ?>" id = "depnumxx">
                        <script language="javascript">fnOrderBy('','depnumxx')</script>
                      </td>
                      <td class="name" width="07%">
                        <a href = "" title="Ordenar">Fecha Desde</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "fechinix">
                        <input type = "hidden" name = "fechinix" value = "<?php echo $_POST['fechinix'] ?>" id = "fechinix">
                        <script language="javascript">fnOrderBy('','fechinix')</script>
                      </td>
                      <td class="name" width="07%">
                        <a href = "" title="Ordenar">Fecha Hasta</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "fechhast">
                        <input type = "hidden" name = "fechhast" value = "<?php echo $_POST['fechhast'] ?>" id = "fechhast">
                        <script language="javascript">fnOrderBy('','fechhast')</script>
                      </td>
                      <td class="name" width="05%">
                        <a href = "javascript:fnOrderBy('onclick','pedvlrxx');" title="Ordenar">Valor</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "pedvlrxx">
                        <input type = "hidden" name = "pedvlrxx" value = "<?php echo $_POST['pedvlrxx'] ?>" id = "pedvlrxx">
                        <script language="javascript">fnOrderBy('','pedvlrxx')</script>
                      </td>
                      <td class="name" width="12%">
                        <a href = "" title="Ordenar">Creado Por</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "usrnomxx">
                        <input type = "hidden" name = "usrnomxx" value = "<?php echo $_POST['usrnomxx'] ?>" id = "usrnomxx">
                        <script language="javascript">fnOrderBy('','usrnomxx')</script>
                      </td>
                      <td class="name" width="07%">
                        <a href = "javascript:fnOrderBy('onclick','regessap');" title="Ordenar">Estado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regessap">
                        <input type = "hidden" name = "regessap" value = "<?php echo $_POST['regessap'] ?>" id = "regessap">
                        <script language="javascript">fnOrderBy('','regessap')</script>
                      </td>
                      <td class="name" width="08%">
                        <a href = "javascript:fnOrderBy('onclick','regestxx');" title="Ordenar">Estado Dos</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regestxx">
                        <input type = "hidden" name = "regestxx" value = "<?php echo $_POST['regestxx'] ?>" id = "regestxx">
                        <script language="javascript">fnOrderBy('','regestxx')</script>
                      </td>
                      <td Class='name' width="02%" align="right">
                        <input type="checkbox" name="oCheckAll" onClick = 'javascript:fnMarca()'>
                      </td>
                    </tr>
                    <script languaje="javascript">
                      document.forms['frgrm']['vRecords'].value = "<?php echo count($mPedido) ?>";
                    </script>

                    <?php
                      for ($i=0;$i<count($mPedido);$i++) {
                        if ($i < count($mPedido)) { // Para Controlar el Error
                          $cColor = "{$vSysStr['system_row_impar_color_ini']}";
                          if($y % 2 == 0) {
                            $cColor = "{$vSysStr['system_row_par_color_ini']}";
                          } ?>
                          <tr bgcolor = "<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')"
                            onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
                            <td class="letra7"><a href = javascript:fnVer('<?php echo $mPedido[$i]['pedidxxx']?>','<?php echo $mPedido[$i]['regfcrex']?>')>
                                                        <?php echo $mPedido[$i]['comidxxx']."-".$mPedido[$i]['comprexx'].$mPedido[$i]['comcscxx'] ?> </a></td>
                            <td class="letra7"><?php echo $mPedido[$i]['comcscxx'] ?></td>
                            <td class="letra7"><?php echo $mPedido[$i]['comfecxx'] ?></td>
                            <td class="letra7"><?php echo $mPedido[$i]['cliidxxx'] ?></td>
                            <td class="letra7"><?php echo $mPedido[$i]['clinomxx'] ?></td>
                            <td class="letra7"><?php echo $mPedido[$i]['depnumxx'] ?></td>
                            <td class="letra7"><?php echo $mPedido[$i]['fechinix'] ?></td>
                            <td class="letra7"><?php echo $mPedido[$i]['fechhast'] ?></td>
                            <td class="letra7"><?php echo number_format($mPedido[$i]['pedvlrxx'], 2, '.', ',') ?></td>
                            <td class="letra7"><?php echo $mPedido[$i]['usrnomxx'] ?></td>
                            <td class="letra7"><?php echo str_replace("_", " ", $mPedido[$i]['regessap']) ?></td>
                            <td class="letra7"><?php echo str_replace("_", " ", $mPedido[$i]['regestxx']) ?></td>
                            <td Class="letra7" align="right">
                              <input type="checkbox" name="oCheck" value = "<?php echo mysql_num_rows($xPedido) ?>"
                              id="<?php echo $mPedido[$i]['pedidxxx'].'~'. //[0]
                                             $mPedido[$i]['regfcrex'].'~'. //[1]
                                             $mPedido[$i]['comidxxx'].'~'. //[2]
                                             $mPedido[$i]['comcodxx'].'~'. //[3]
                                             $mPedido[$i]['comcscxx'].'~'. //[4]
                                             $mPedido[$i]['comcsc2x'].'~'. //[5]
                                             $mPedido[$i]['regestxx'].'~'. //[6]
                                             $mPedido[$i]['comprexx'] //[7] ?>"
                              onclick="javascript:document.forms['frgrm']['vRecords'].value='<?php echo count($mPedido) ?>'">
                            </td>
                          </tr>
                          <?php $y++;
                        }
                      }

                      if(count($mPedido) == 1){ ?>
                        <script language="javascript">
                          document.forms['frgrm']['oCheck'].checked = true;
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