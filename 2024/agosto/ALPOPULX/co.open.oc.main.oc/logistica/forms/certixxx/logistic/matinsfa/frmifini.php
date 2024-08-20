<?php
  /**
   * Tracking Matriz de Isumos Facturables.
   * --- Descripcion: Este programa permite realizar consultas rapidas a la Matriz de Insumos Facturables que se Encuentran en la Base de Datos
   * @author Diego Fernando Cortes Rojas <diego.cortes@openits.co>
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

      function fnVer(xMifId,xRegfcre) {
        var cPathUrl = "frmifnue.php?cMifId="+xMifId+"&cAnio="+xRegfcre.substr(0,4);
        document.cookie = "kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie = "kMenDes=Ver Matriz de Insumos Facturables;path="+"/";
        document.cookie = "kModo=VER;path="+"/";
        parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
        document.location = cPathUrl; // Invoco el menu.
      }

      function fnEditar(xModo) {
        switch (document.forms['frgrm']['vRecords'].value) {
          case "1":
            if (document.forms['frgrm']['oCheck'].checked == true) {
              var mMatriz = document.forms['frgrm']['oCheck'].id.split('~');
              var ruta = "frmifnue.php?cMifId="+mMatriz[0]+"&cAnio="+mMatriz[1].substr(0,4);
              document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
              document.cookie="kMenDes=Editar Matriz de Insumos Facturables;path="+"/";
              document.cookie="kModo="+xModo+";path="+"/";
              parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
              document.location = ruta; // Invoco el menu.
            }
          break;
          default:
            var zSw_Prv = 0;
            for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
              if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
                // Solo Deja Legalizar el Primero Seleccionado
                zSw_Prv = 1;
                var mMatriz = document.forms['frgrm']['oCheck'][i].id.split('~');
                var ruta = "frmifnue.php?cMifId="+mMatriz[0]+"&cAnio="+mMatriz[1].substr(0,4);
                document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                document.cookie="kMenDes=Editar Matriz de Insumos Facturables;path="+"/";
                document.cookie="kModo="+xModo+";path="+"/";
                parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                document.location = ruta; // Invoco el menu.
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

      function fnAdicionarMovimiento(xModo) {
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
                var mMatriz = document.forms['frgrm']['oCheck'].id.split('~');
                if (mMatriz[2] == "ENPROCESO") {
                  var cPathUrl = "frmifamo.php?cMifId="+mMatriz[0]+"&cAnio="+mMatriz[1].substr(0,4);
                  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                  document.cookie="kModo="+xModo+";path="+"/";
                  document.cookie="kMenDes=Adicionar Movimiento;path="+"/";
                  parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                  document.location = cPathUrl; // Invoco el menu.
                } else {
                  alert("El estado de la M.I.F debe ser ENPROCESO.");
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
                  if (mMatriz[2] == "ENPROCESO") {
                    var cPathUrl = "frmifamo.php?cMifId="+mMatriz[0]+"&cAnio="+mMatriz[1].substr(0,4);
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.cookie="kModo="+xModo+";path="+"/";
                    document.cookie="kMenDes=Adicionar Movimiento;path="+"/";
                    parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                    document.location = cPathUrl; // Invoco el menu.
                  } else {
                    alert("El estado de la M.I.F debe ser ENPROCESO.");
                  }
                }
              }
            break;
          }
        } else {
          alert("Solo se permite seleccionar un registro.");
        }
      }

      function fnCargueMasivoMovimiento(xModo) {
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
                var mMatriz = document.forms['frgrm']['oCheck'].id.split('~');
                if (mMatriz[2] == "ENPROCESO") {
                  var cPathUrl = "frmifcar.php?cMifId="+mMatriz[0]+"&cAnio="+mMatriz[1].substr(0,4);
                  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                  document.cookie="kMenDes=Cargue Masivo Movimiento;path="+"/";
                  document.cookie="kModo="+xModo+";path="+"/";
                  parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                  document.location = cPathUrl; // Invoco el menu.
                } else {
                  alert("El estado de la M.I.F debe ser ENPROCESO.");
                }
              }
            break;
            default:
              var zSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
                if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
                  zSw_Prv = 1;
                  var mMatriz = document.forms['frgrm']['oCheck'][i].id.split('~');
                  if (mMatriz[2] == "ENPROCESO") {
                    var cPathUrl = "frmifcar.php?cMifId="+mMatriz[0]+"&cAnio="+mMatriz[1].substr(0,4);
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.cookie="kMenDes=Cargue Masivo Movimiento;path="+"/";
                    document.cookie="kModo="+xModo+";path="+"/";
                    parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                    document.location = cPathUrl; // Invoco el menu.
                  } else {
                    alert("El estado de la M.I.F debe ser ENPROCESO.");
                  }
                }
              }
            break;
          }
        } else {
          alert("Solo se permite seleccionar un registro.");
        }
      }

      function fnCargarReporte(xModo) {
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
                var mMatriz = document.forms['frgrm']['oCheck'].id.split('~');
                if (mMatriz[2] == "ENPROCESO") {
                  var cPathUrl = "frmifrep.php?cMifId="+mMatriz[0]+"&cAnio="+mMatriz[1].substr(0,4);
                  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                  document.cookie="kMenDes=Cargue Reporte;path="+"/";
                  document.cookie="kModo="+xModo+";path="+"/";
                  parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                  document.location = cPathUrl; // Invoco el menu.
                } else {
                  alert("El estado de la M.I.F debe ser ENPROCESO.");
                }
              }
            break;
            default:
              var zSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
                if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
                  zSw_Prv = 1;
                  var mMatriz = document.forms['frgrm']['oCheck'][i].id.split('~');
                  if (mMatriz[2] == "ENPROCESO") {
                    var cPathUrl = "frmifrep.php?cMifId="+mMatriz[0]+"&cAnio="+mMatriz[1].substr(0,4);
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.cookie="kMenDes=Cargue Reporte;path="+"/";
                    document.cookie="kModo="+xModo+";path="+"/";
                    parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                    document.location = cPathUrl; // Invoco el menu.
                  } else {
                    alert("El estado de la M.I.F debe ser ENPROCESO.");
                  }
                }
              }
            break;
          }
        } else {
          alert("Solo se permite seleccionar un registro.");
        }
      }

      function fnImprimir() {
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
                var mMatriz = document.forms['frgrm']['oCheck'].id.split('~');
                parent.fmpro.location = "frmifprn.php?cMifId="+mMatriz[0]+"&cAnio="+mMatriz[1].substr(0,4);
              }
            break;
            default:
              var zSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
                if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
                  zSw_Prv = 1;
                  var mMatriz = document.forms['frgrm']['oCheck'][i].id.split('~');
                  parent.fmpro.location = "frmifprn.php?cMifId="+mMatriz[0]+"&cAnio="+mMatriz[1].substr(0,4);
                }
              }
            break;
          }
        } else {
          alert("Solo se permite seleccionar un registro.");
        }
      }

      function fnDesbloqueo(xModo){
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
                var mMatriz = document.forms['frgrm']['oCheck'].id.split('~');
                var cPathUrl = "frmifdes.php?gMifId="+mMatriz[0]+"&gComPre="+mMatriz[4]+"&gComCsc="+mMatriz[5]+"&gDesde="+mMatriz[6]+"&gHasta="+mMatriz[7]+"&cAnio="+mMatriz[1].substr(0,4);
                document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                document.cookie="kMenDes=Desbloqueo;path="+"/";
                document.cookie="kModo="+xModo+";path="+"/";
                parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                document.location = cPathUrl; // Invoco el menu.
              }
            break;
            default:
              var zSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
                if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
                  zSw_Prv = 1;
                  var mMatriz = document.forms['frgrm']['oCheck'][i].id.split('~');
                  var cPathUrl = "frmifdes.php?gMifId="+mMatriz[0]+"&gComPre="+mMatriz[4]+"&gComCsc="+mMatriz[5]+"&gDesde="+mMatriz[6]+"&gHasta="+mMatriz[7]+"&cAnio="+mMatriz[1].substr(0,4);
                  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                  document.cookie="kMenDes=Desbloqueo;path="+"/";
                  document.cookie="kModo="+xModo+";path="+"/";
                  parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                  document.location = cPathUrl; // Invoco el menu.
                }
              }
            break;
          }
        } else {
          alert("Solo se permite seleccionar un registro.");
        }
      }

      function fnActivarAnular(xModo) {
        var cEstado = xModo == "ACTIVAR" ? "ACTIVO" : "ANULADO";
        var nCheck  = 0
        for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
          if (document.forms['frgrm']['oCheck'][i].checked == true) {
            nCheck++;
          }
        }

        if (nCheck == 1 || document.forms['frgrm']['oCheck'].checked == true) {
          if (document.forms['frgrm']['vRecords'].value!="0"){
            switch (document.forms['frgrm']['vRecords'].value) {
              case "1":
                if (document.forms['frgrm']['oCheck'].checked == true) {
                  var mMatriz = document.forms['frgrm']['oCheck'].id.split('~');
                  if (confirm("Esta seguro de cambiar el estado "+cEstado+" de la M.I.F. ["+mMatriz[4]+"-"+mMatriz[5]+"]?")) {
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.forms['frestado']['cMifId'].value  = mMatriz[0];
                    document.forms['frestado']['dFecCre'].value = mMatriz[1];
                    document.forms['frestado']['cComPre'].value = mMatriz[4];
                    document.forms['frestado']['cComCsc'].value = mMatriz[5];
                    document.cookie="kModo="+xModo+";path="+"/";
                    document.forms['frestado'].submit();
                  }
                }
              break;
              default:
                var zSw_Prv = 0;
                for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
                  if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
                    var mMatriz = document.forms['frgrm']['oCheck'][i].id.split('~');
                    if (confirm("Esta seguro de cambiar el estado "+cEstado+" de la M.I.F. ["+mMatriz[4]+"-"+mMatriz[5]+"]?")) {
                      zSw_Prv = 1;
                      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                      document.forms['frestado']['cMifId'].value  = mMatriz[0];
                      document.forms['frestado']['dFecCre'].value = mMatriz[1];
                      document.forms['frestado']['cComPre'].value = mMatriz[4];
                      document.forms['frestado']['cComCsc'].value = mMatriz[5];
                      document.cookie="kModo="+xModo+";path="+"/";
                      document.forms['frestado'].submit();
                    }
                  }
                }
              break;
            }
          }
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
                var nMifId    = mMatriz[0]; // Id M.I.F
                var dFechaMif = mMatriz[1]; // Fecha de Creacion
                var cRegEst   = mMatriz[2]; // Estado
                if (cRegEst == "ENPROCESO") {
                  var ruta = "frcranue.php?nMifId="+nMifId+"&dFechaMif="+dFechaMif;
                  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                  document.cookie="kMenDes=Cargar Anexos;path="+"/";
                  document.cookie="kModo="+xModo+";path="+"/";
                  parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                  document.location.ruta; // Invoco el menu.
                } else {
                  alert("La M.I.F seleccionada no se encuentra ENPROCESO");
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
                  var nMifId    = mMatriz[0]; // Id M.I.F
                  var dFechaMif = mMatriz[1]; // Fecha de Creacion
                  var cRegEst   = mMatriz[2]; // Estado
                  if (cRegEst == "ENPROCESO") {
                    var ruta = "frcranue.php?nMifId="+nMifId+"&dFechaMif="+dFechaMif;
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.cookie="kMenDes=Cargar Anexos;path="+"/";
                    document.cookie="kModo="+xModo+";path="+"/";
                    parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                    document.location = ruta; // Invoco el menu.
                  } else {
                    alert("La M.I.F seleccionada no se encuentra ENPROCESO");
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
                var nMifId    = mMatriz[0]; // Id M.I.F
                var dFechaMif = mMatriz[1]; // Fecha de Creacion
                var cRegEst   = mMatriz[2]; // Estado
                  if (cRegEst == "ENPROCESO") {
                    var ruta = "frvranue.php?nMifId="+nMifId+"&dFechaMif="+dFechaMif;
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.cookie="kMenDes=Ver Anexos;path="+"/";
                    document.cookie="kModo="+xModo+";path="+"/";
                    parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                    document.location.ruta; // Invoco el menu.
                  } else {
                    alert("La M.I.F seleccionada no se encuentra ENPROCESO");
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
                  var nMifId    = mMatriz[0]; // Id M.I.F
                  var dFechaMif = mMatriz[1]; // Fecha de Creacion
                  var cRegEst   = mMatriz[2]; // Estado
                  if (cRegEst == "ENPROCESO") {
                    var ruta = "frvranue.php?nMifId="+nMifId+"&dFechaMif="+dFechaMif;
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.cookie="kMenDes=Ver Anexos;path="+"/";
                    document.cookie="kModo="+xModo+";path="+"/";
                    parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                    document.location = ruta; // Invoco el menu.
                  } else {
                    alert("La M.I.F seleccionada no se encuentra ENPROCESO");
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
    <form name = "frestado" action = "frmifgra.php" method = "post" target="fmpro">
      <input type = "hidden" name = "cMifId"    value = "">
      <input type = "hidden" name = "dFecCre"   value = "">
      <input type = "hidden" name = "cComPre"   value = "">
      <input type = "hidden" name = "cComCsc"   value = "">
    </form>

    <form name = "frgrm" action = "frmifini.php" method = "post" target="fmwork">
      <input type = "hidden" name = "vRecords"   value = "">
      <input type = "hidden" name = "vLimInf"    value = "<?php echo $vLimInf ?>">
      <input type = "hidden" name = "vSortField" value = "<?php echo $vSortField ?>">
      <input type = "hidden" name = "vSortType"  value = "<?php echo $vSortType ?>">
      <input type = "hidden" name = "vTimes"     value = "<?php echo $vTimes ?>">
      <input type = "hidden" name = "vTimesSave" value = "0">
      <input type = "hidden" name = "vBuscar"    value = "<?php echo $_POST['vBuscar'] ?>">
      <input type = "hidden" name = "cOrderByOrder"  value = "<?php echo $_POST['cOrderByOrder'] ?>" style = "width:1000">

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

        /**INICIO SQL**/
        if ($_POST['cPeriodos'] == "") {
          $_POST['cPeriodos'] == "20";
          $_POST['dDesde'] = substr(date('Y-m-d'),0,8)."01";
          $_POST['dHasta'] = date('Y-m-d');
        }

        // Valida si el año de instalacion del modulo es menor al año actual
        $nAnioDesde = substr($_POST['dDesde'], 0, 4);
        $nAnioDesde = ($nAnioDesde < $vSysStr['logistica_ano_instalacion_modulo']) ? $vSysStr['logistica_ano_instalacion_modulo'] : $nAnioDesde;

        $mMatrInsFac = array();
        for ($iAno = $nAnioDesde; $iAno <= substr($_POST['dHasta'],0,4); $iAno++) { // Recorro desde el anio de inicio hasta el anio de fin de la consulta

          if ($iAno == $nAnioDesde) {
            $qMatrInsFac  = "(SELECT DISTINCT ";
            $qMatrInsFac .= "SQL_CALC_FOUND_ROWS ";
          }else {
            $qMatrInsFac  .= "(SELECT DISTINCT ";
          }

          $qMatrInsFac .= "$cAlfa.lmca$iAno.mifidxxx, ";   // Id MIF 
          $qMatrInsFac .= "$cAlfa.lmca$iAno.comidxxx, ";   // Id del Comprobante 
          $qMatrInsFac .= "$cAlfa.lmca$iAno.comprexx, ";   // Prefijo
          $qMatrInsFac .= "$cAlfa.lmca$iAno.comcscxx, ";   // Consecutivo Uno
          $qMatrInsFac .= "$cAlfa.lmca$iAno.cliidxxx,";    // Id cliente
          $qMatrInsFac .= "$cAlfa.lpar0150.clisapxx, ";    // Codigo SAP
          $qMatrInsFac .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,REPLACE(CONCAT($cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x), \"  \", \" \")) AS clinomxx, "; // Nombre Cliente
          $qMatrInsFac .= "$cAlfa.lmca$iAno.depnumxx, ";   // Numero deposito
          $qMatrInsFac .= "$cAlfa.lmca$iAno.miffdexx, ";   // Fecha desde
          $qMatrInsFac .= "$cAlfa.lmca$iAno.miffhaxx, ";   // Fecha hasta
          $qMatrInsFac .= "$cAlfa.lmca$iAno.miforixx, ";   // Fecha hasta
          $qMatrInsFac .= "$cAlfa.lmca$iAno.regusrxx, ";   // Usuario que creo el registro
          $qMatrInsFac .= "$cAlfa.lmca$iAno.regfcrex, ";   // Fecha de vigencia hasta
          $qMatrInsFac .= "$cAlfa.lmca$iAno.reghcrex, ";   // Hora de creación
          $qMatrInsFac .= "$cAlfa.lmca$iAno.regfmodx, ";   // Fecha de modificación
          $qMatrInsFac .= "$cAlfa.lmca$iAno.reghmodx, ";   // Hora de modificación
          $qMatrInsFac .= "$cAlfa.lmca$iAno.regestxx ";   // Estado
          $qMatrInsFac .= "FROM $cAlfa.lmca$iAno ";

          $qMatrInsFac .= "LEFT JOIN $cAlfa.lpar0150 ON lmca$iAno.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
          $qMatrInsFac .= "WHERE ";
          if ($_POST['vSearch'] != "") {
            if ($_POST['cBusExc'] == "SI") {
              $prefijo     = substr($_POST['vSearch'], 0, 3);
              $consecutivo = substr($_POST['vSearch'], 3);

              $qMatrInsFac .= "$cAlfa.lmca$iAno.comprexx = \"$prefijo\" AND ";
              $qMatrInsFac .= "$cAlfa.lmca$iAno.comcscxx = \"$consecutivo\" AND ";
            } else {
              $qMatrInsFac .= "(";
              $qMatrInsFac .= "CONCAT($cAlfa.lmca$iAno.comprexx ,\"\",$cAlfa.lmca$iAno.comcscxx ) LIKE \"%{$_POST['vSearch']}%\" OR ";
              $qMatrInsFac .= "$cAlfa.lmca$iAno.cliidxxx LIKE \"%{$_POST['vSearch']}%\" OR ";
              $qMatrInsFac .= "$cAlfa.lpar0150.clisapxx LIKE \"%{$_POST['vSearch']}%\" OR ";
              $qMatrInsFac .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,REPLACE(CONCAT($cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x), \"  \", \" \")) LIKE \"%{$_POST['vSearch']}%\" OR ";
              $qMatrInsFac .= "$cAlfa.lmca$iAno.depnumxx LIKE \"%{$_POST['vSearch']}%\" OR ";
              $qMatrInsFac .= "$cAlfa.lmca$iAno.regestxx LIKE \"%{$_POST['vSearch']}%\") AND ";
            }
          }
          $qMatrInsFac .= "$cAlfa.lmca$iAno.regfcrex BETWEEN \"{$_POST['dDesde']}\" AND \"{$_POST['dHasta']}\" ) ";
          /***** FIN SQL *****/

          if ($iAno >= $nAnioDesde && $iAno < substr($_POST['dHasta'],0,4)) {
            $qMatrInsFac .= " UNION ";
          }
        } ## for ($iAno = $nAnioDesde; $iAno <= substr($_POST['dHasta'],0,4); $iAno++) { ##

        // CODIGO NUEVO PARA ORDER BY
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
        $qMatrInsFac .= "$cOrderBy LIMIT $vLimInf,$vLimSup ";
        $xMatrInsFac = f_MySql("SELECT","",$qMatrInsFac,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qMatrInsFac."~".mysql_num_rows($xMatrInsFac));
        // echo $qMatrInsFac."~".mysql_num_rows($xMatrInsFac);

        $xNumRows = mysql_query("SELECT FOUND_ROWS();", $xConexion01);
        $xRNR = mysql_fetch_array($xNumRows);
        $nRNR += $xRNR['FOUND_ROWS()'];

        while ($xRMI = mysql_fetch_array($xMatrInsFac)) {
          $mMatrInsFac[count($mMatrInsFac)] = $xRMI;
        }
      ?>
      <center>
        <table width="95%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td>
              <fieldset>
                <legend>Matriz de Insumos Facturables (<?php echo $nRNR ?>)</legend>
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
                                              document.forms['frgrm'].submit()">&nbsp;&nbsp;&nbsp;
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
                    
                    <!--fin de codigo nuevo-->
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
                              case "ADDMOV": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_create-file_bg.gif" onClick = "javascript:fnAdicionarMovimiento('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "CARGAR": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/page_go.png" onClick = "javascript:fnCargueMasivoMovimiento('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "CARGARREP": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_global-changes_bg1.gif" onClick = "javascript:fnCargarReporte('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "DESBLOQUEO": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/permissions.gif" onClick = "javascript:fnDesbloqueo('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "EDITAR": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_edit.png" onClick = "javascript:fnEditar('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "IMPRIMIR": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_print.png" onClick = "javascript:fnImprimir('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "ACTIVAR": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/ok.gif" onClick = "javascript:fnActivarAnular('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "ANULAR": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_drop.png" onClick = "javascript:fnActivarAnular('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "CARGARANEXOS": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/cargar_anexos.png" onClick = "javascript:fnCargarAnexos('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                              case "VERANEXOS": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/ver_anexos.png" onClick = "javascript:fnVerAnexos('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
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
                        <a href = "javascript:fnOrderBy('onclick','mifidxxx');" title="Ordenar">No. M.I.F</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "mifidxxx">
                        <input type = "hidden" name = "mifidxxx" value = "<?php echo $_POST['mifidxxx'] ?>" id = "mifidxxx">
                        <script language="javascript">fnOrderBy('','mifidxxx')</script>
                      </td>
                      <td class="name" width="12%">
                        <a href = "javascript:fnOrderBy('onclick','cliidxxx');" title="Ordenar">Nit</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "cliidxxx">
                        <input type = "hidden" name = "cliidxxx" value = "<?php echo $_POST['cliidxxx'] ?>" id = "cliidxxx">
                        <script language="javascript">fnOrderBy('','cliidxxx')</script>
                      </td>
                      <td class="name" width="08%">
                        <a href = "javascript:fnOrderBy('onclick','clisapxx');" title="Ordenar">Codigo SAP</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "clisapxx">
                        <input type = "hidden" name = "clisapxx" value = "<?php echo $_POST['clisapxx'] ?>" id = "clisapxx">
                        <script language="javascript">fnOrderBy('','clisapxx')</script>
                      </td>
                      <td class="name" width="19%">
                        <a href = "javascript:fnOrderBy('onclick','clinomxx');" title="Ordenar">Razon Social</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "clinomxx">
                        <input type = "hidden" name = "clinomxx" value = "<?php echo $_POST['clinomxx'] ?>" id = "clinomxx">
                        <script language="javascript">fnOrderBy('','clinomxx')</script>
                      </td>
                      <td class="name" width="19%">
                        <a href = "javascript:fnOrderBy('onclick','depnumxx');" title="Ordenar">Deposito</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "depnumxx">
                        <input type = "hidden" name = "depnumxx" value = "<?php echo $_POST['depnumxx'] ?>" id = "depnumxx">
                        <script language="javascript">fnOrderBy('','depnumxx')</script>
                      </td>
                      <td class="name" width="08%">
                        <a href = "javascript:fnOrderBy('onclick','miffdexx');" title="Ordenar">Desde</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "miffdexx">
                        <input type = "hidden" name = "miffdexx" value = "<?php echo $_POST['miffdexx'] ?>" id = "miffdexx">
                        <script language="javascript">fnOrderBy('','miffdexx')</script>
                      </td>
                      <td class="name" width="08%">
                        <a href = "javascript:fnOrderBy('onclick','miffhaxx');" title="Ordenar">Hasta</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "miffhaxx">
                        <input type = "hidden" name = "miffhaxx" value = "<?php echo $_POST['miffhaxx'] ?>" id = "miffhaxx">
                        <script language="javascript">fnOrderBy('','miffhaxx')</script>
                      </td>
                      <td class="name" width="05%">
                        <a href = "javascript:fnOrderBy('onclick','regfcrex');" title="Ordenar">Creado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfcrex">
                        <input type = "hidden" name = "regfcrex" value = "<?php echo $_POST['regfcrex'] ?>" id = "regfcrex">
                        <script language="javascript">fnOrderBy('','regfcrex')</script>
                      </td>
                      <td class="name" width="06%">
                        <a href = "javascript:fnOrderBy('onclick','regfmodx');" title="Ordenar">Modificado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfmodx">
                        <input type = "hidden" name = "regfmodx" value = "<?php echo $_POST['regfmodx'] ?>" id = "regfmodx">
                        <script language="javascript">fnOrderBy('','regfmodx')</script>
                      </td>
                      <td class="name" width="05%">
                        <a href = "javascript:fnOrderBy('onclick','regestxx');" title="Ordenar">Estado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regestxx">
                        <input type = "hidden" name = "regestxx" value = "<?php echo $_POST['regestxx'] ?>" id = "regestxx">
                        <script language="javascript">fnOrderBy('','regestxx')</script>
                      </td>
                      <td Class='name' width="02%" align="right">
                        <input type="checkbox" name="oCheckAll" onClick = 'javascript:fnMarca()'>
                      </td>
                    </tr>
                      <script languaje="javascript">
                        document.forms['frgrm']['vRecords'].value = "<?php echo count($mMatrInsFac) ?>";
                      </script>

                      <?php
                        for ($i=0;$i<count($mMatrInsFac);$i++) {
                          if ($i < count($mMatrInsFac)) { // Para Controlar el Error
                            $cColor = "{$vSysStr['system_row_impar_color_ini']}";
                            if($y % 2 == 0) {
                              $cColor = "{$vSysStr['system_row_par_color_ini']}";
                            } ?>
                            <tr bgcolor = "<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')"
                              onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
                              <td class="letra7"><a href = javascript:fnVer('<?php echo $mMatrInsFac[$i]['mifidxxx']?>','<?php echo $mMatrInsFac[$i]['regfcrex']?>')>
                                                          <?php echo $mMatrInsFac[$i]['comidxxx']."-".$mMatrInsFac[$i]['comprexx'].$mMatrInsFac[$i]['comcscxx'] ?> </a></td>
                              <td class="letra7"><?php echo $mMatrInsFac[$i]['cliidxxx'] ?></td>
                              <td class="letra7"><?php echo $mMatrInsFac[$i]['clisapxx'] ?></td>
                              <td class="letra7"><?php echo $mMatrInsFac[$i]['clinomxx'] ?></td>
                              <td class="letra7"><?php echo $mMatrInsFac[$i]['depnumxx'] ?></td>
                              <td class="letra7"><?php echo $mMatrInsFac[$i]['miffdexx'] ?></td>
                              <td class="letra7"><?php echo $mMatrInsFac[$i]['miffhaxx'] ?></td>
                              <td class="letra7"><?php echo $mMatrInsFac[$i]['regfcrex'] ?></td>
                              <td class="letra7"><?php echo $mMatrInsFac[$i]['regfmodx'] ?></td>
                              <td class="letra7"><?php echo $mMatrInsFac[$i]['regestxx'] ?></td>
                              <td Class="letra7" align="right">
                                <input type="checkbox" name="oCheck" value = "<?php echo  mysql_num_rows($xMatrInsFac) ?>"
                                id="<?php echo $mMatrInsFac[$i]['mifidxxx'].'~'. //[0]
                                              $mMatrInsFac[$i]['regfcrex'].'~'. //[1]
                                              $mMatrInsFac[$i]['regestxx'].'~'. //[2]
                                              $mMatrInsFac[$i]['miforixx'].'~'. //[3]
                                              $mMatrInsFac[$i]['comprexx'].'~'. //[4]
                                              $mMatrInsFac[$i]['comcscxx'].'~'. //[5]
                                              $mMatrInsFac[$i]['miffdexx'].'~'. //[6]
                                              $mMatrInsFac[$i]['miffhaxx'] //[7]?>"
                                onclick="javascript:document.forms['frgrm']['vRecords'].value='<?php echo count($mMatrInsFac) ?>'">
                              </td>
                            </tr>
                            <?php $y++;
                          }
                        }
                        if(count($mMatrInsFac) == 1){ ?>
                          <script language="javascript">
                            document.forms['frgrm']['oCheck'].checked = true;
                          </script>
                          <?php
                        }?>
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