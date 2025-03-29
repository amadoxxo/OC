<?php
/**
 * Reporte General Importaciones X Item
 * @author cesar Mu�oz <opencomex@opencomex.com>
 * @package opencomex
 */
include("../../libs/php/utility.php");
/**
 *  Cookie fija
 */
$kDf = explode("~", $_COOKIE["kDatosFijos"]);
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
    <title>Consulta General X Item</title>
    <script language = 'javascript' src = '../../programs/otvalida.js'></script>
    <script language = 'javascript' src = '../../programs/date_picker2.js'></script>
    <LINK rel = 'stylesheet' href = '../../programs/estilo.css'>
    <LINK rel = 'stylesheet' href = '../../programs/general.css'>
    <LINK rel = 'stylesheet' href = '../../programs/layout.css'>
    <LINK rel = 'stylesheet' href = '../../programs/custom.css'>
    <script language = 'javascript' src = '../../programs/utility.js'></script>
    <script language = 'javascript' src = '../../programs/ajax.js'></script>
    <script language="javascript">
      <?php
      /**
       * JavaScript:fnCargarImpoGla
       */
      ?>
      function fnCargarImpoGla(){
        switch("<?php echo $cAlfa ?>"){
          case "GRUPOGLA":
          case "TEGRUPOGLA":
          case "DEGRUPOGLA":
            if(document.frnav['vdoiid'].value != "" && document.frnav['vdoisfid'].value != ""){
              document.frimpodo['cDoiId'].value = document.frnav['vdoiid'].value;
              document.frimpodo['cDoiSfId'].value = document.frnav['vdoisfid'].value;
              document.frimpodo.submit();
            }
          break;
        }
      }

      function f_VarSession(cNombre, cValor) {
        parent.fmpro.location = "../creasession.php?cNombre=" + cNombre + "&cValor=" + cValor;
      }

      function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        parent.fmwork.location = 'consulta.php';
        parent.fmnav.location = '../nivel3.php';
      }

      function f_Despeja() {
        self.location = "fr00003.php?gUser=<?php echo $kUser ?>";
      }

      function enwindow() {
        msg = window.open('fr00003f.php', 'mywin', 'width=700,height=500,scrollbars=no,toolbar=no,location=no,directories=no,status=no,menubar=no');
        msg.focus();
      }

      /**
       * Funcion para los diferentes ambientes de solo DHLEXPRE, TEDHLEXPRE, DEDHLEXPRE para la opcion Ejecutivo de Cuenta.
       */
      function fnLinkEjecutivoCuenta(xLink, xSwitch, xIteration){
        var zX = screen.width;
        var zY = screen.height;

        if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
          var zRuta = "frdex150.php?gWhat=" + xSwitch +
                  "&gFunction=" + xLink +
                  "&gCliId=" + document.frnav[xLink].value.toUpperCase();
          parent.fmpro.location = zRuta;
        } else if(xSwitch == "WINDOW") {
          var zNx = (zX - 600) / 2;
          var zNy = (zY - 550) / 2;

          var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
          var zRuta = "frdex150.php?gWhat=WINDOW&gFunction=" + xLink +
                  "&gCliId=" + document.frnav[xLink].value.toUpperCase();

          if (xIteration == -1) {
            zRuta = "frdex150.php?gWhat=WINDOW&gFunction=" + xLink + "&gCliId=";
          }

          zWindow = window.open(zRuta, "zWindow", zWinPro);
          zWindow.focus();
        } else if(document.frnav[xLink].value.length == 0) {
          // Limpiando Campos de Ejecutivo de cuenta.
          document.frnav['vusrid4'].value = "";
          document.frnav['vusrnom4'].value = "";
        }
      }

      function uLinks(xLink, xSwitch, xIteration, xRes) {
        var zX = screen.width;
        var zY = screen.height;
        var cProceso = "105";
        var cSubproceso = "";
        switch (xLink) {
          case "vsccid":
            if(xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vsccdes'].value = '';
            }
            if (xSwitch == "VALID" && document.frnav[xLink].value.length > 0) {
              var zRuta = "frfpa120.php?gWhat=VALID&gFunction=" + xLink + "&gSccId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frfpa120.php?gWhat=WINDOW&gFunction=" + xLink + "&gSccId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frfpa120.php?gWhat=WINDOW&gFunction=" + xLink + "&gSccId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              } else {
                if (xSwitch == "EXACT") {
                  var zRuta = "frfpa120.php?gWhat=EXACT&gFunction=" + xLink + "&gSccId=" + document.frnav[xLink].value.toUpperCase() + "";
                  parent.fmpro.location = zRuta;
                }
              }
            }
          break;
          case "vcliid":
          case "vcliid2":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              switch (xLink) {
                case "vcliid":
                  document.frnav['vclinom'].value = '';
                  break;
                case "vcliid2":
                  document.frnav['vclinom2'].value = '';
                  break;
              }
            }
            if (xSwitch == "VALID" && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi150.php?gWhat=VALID&gFunction=" + xLink + "&gCliId=" + document.frnav[xLink].value.toUpperCase() + "&cCliIna=SI";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi150.php?gWhat=WINDOW&gFunction=" + xLink + "&gCliId=" + document.frnav[xLink].value.toUpperCase() + "&cCliIna=SI";
                if (xIteration == -1) {
                  zRuta = "frdoi150.php?gWhat=WINDOW&gFunction=" + xLink + "&cCliIna=SI&gCliId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              } else {
                if (xSwitch == "EXACT") {
                  var zRuta = "frdoi150.php?gWhat=EXACT&gFunction=" + xLink + "&gCliId=" + document.frnav[xLink].value.toUpperCase() + "&cCliIna=SI";
                  parent.fmpro.location = zRuta;
                }
              }
            }
            break;
          case "vusrid1":
          case "vusrid2":
          case "vusrid3":
          case "vusrid4":
          case "vusrid5":
          case "vusrid6":
            var cDirCue = "";
            var nSwicht = 0;
            var cMsj = "";

            if (nSwicht == 0) {
              if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
                switch (xLink) {
                  case "vusrid1":
                    document.frnav['vusrnom1'].value = '';
                    break;
                  case "vusrid2":
                    document.frnav['vusrnom2'].value = '';
                    break;
                  case "vusrid3":
                    document.frnav['vusrnom3'].value = '';
                    break;
                  case "vusrid4":
                    document.frnav['vusrnom4'].value = '';
                    break;
                  case "vusrid5":
                    document.frnav['vusrnom5'].value = '';
                    break;
                  case "vusrid6":
                    document.frnav['vusrnom6'].value = '';
                    break;
                }
              }
              switch (xLink) {
                case "vusrid4": // ejecutivo de cuenta
                  cSubproceso = "103";
                  break;
                case "vusrid5": // analista arancel
                  cSubproceso = "104";
                  break;
                case "vusrid6":// Analista de Registro
                  cSubproceso = "105";
                  break;
              }
              if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
                var zRuta = "frdoi003.php?gWhat=" + xSwitch +
                        "&gFunction=" + xLink +
                        "&gUsrId=" + document.frnav[xLink].value.toUpperCase() +
                        "&gUsrId2=" + cDirCue +
                        "&gProceso=" + cProceso +
                        "&gSubProceso=" + cSubproceso;
                parent.fmpro.location = zRuta;
              } else {
                if (xSwitch == "WINDOW") {
                  var zNx = (zX - 600) / 2;
                  var zNy = (zY - 550) / 2;
                  var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                  var zRuta = "frdoi003.php?gWhat=WINDOW&gFunction=" + xLink +
                          "&gUsrId=" + document.frnav[xLink].value.toUpperCase() +
                          "&gUsrId2=" + cDirCue +
                          "&gProceso=" + cProceso +
                          "&gSubProceso=" + cSubproceso;
                  if (xIteration == -1) {
                    zRuta = "frdoi003.php?gWhat=WINDOW&gFunction=" + xLink +
                            "&gUsrId=" +
                            "&gUsrId2=" + cDirCue +
                            "&gProceso=" + cProceso +
                            "&gSubProceso=" + cSubproceso;
                  }
                  zWindow = window.open(zRuta, "zWindow", zWinPro);
                  zWindow.focus();
                }
              }
            } else {
              alert(cMsj + " Verifique.\n");
            }
            break;
					case "cCosDHLId":
						if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
							document.frnav['cCosDHLDes'].value = '';
						}
						if (xSwitch == "VALID" && document.frnav[xLink].value.length > 0) {
							var zRuta = "frdoi005.php?gWhat=VALID&gFunction="+xLink+"&gCliId=false&gCosDHLId="+document.frnav[xLink].value.toUpperCase();
							parent.fmpro.location = zRuta;
						} else {
							if (xSwitch == "WINDOW") {
								var zNx			= (zX-600)/2;
								var zNy			= (zY-550)/2;
								var zWinPro = 'width=600,scrollbars=1,height=550,left='+zNx+',top='+zNy;
								var zRuta		= "frdoi005.php?gWhat=WINDOW&gFunction="+xLink+"&gCliId=false&gCosDHLId&gCosDHLId="+document.frnav[xLink].value.toUpperCase();
								if (xIteration == -1){
									zRuta = "frdoi005.php?gWhat=WINDOW&gFunction="+xLink+"&gCliId=false&gCosDHLId&gCosDHLId=";
								}
								zWindow = window.open(zRuta,"zWindow",zWinPro);
								zWindow.focus();
							} else {
								if (xSwitch == "EXACT") {
									var zRuta = "frdoi005.php?gWhat=EXACT&gFunction="+xLink+"&gCliId=false&gCosDHLId&gCosDHLId="+document.frnav[xLink].value.toUpperCase();
									parent.fmpro.location = zRuta;
								}
							}
						}
						break;
					case "cDivDHLId":
						if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
							document.frnav['cDivDHLDes'].value = '';
						}
						if (xSwitch == "VALID" && document.frnav[xLink].value.length > 0) {
							var zRuta = "frdoi006.php?gWhat=VALID&gFunction="+xLink+"&gCliId=false&gDivDHLId="+document.frnav[xLink].value.toUpperCase();
							parent.fmpro.location = zRuta;
						} else {
							if (xSwitch == "WINDOW") {
								var zNx			= (zX-600)/2;
								var zNy			= (zY-550)/2;
								var zWinPro = 'width=600,scrollbars=1,height=550,left='+zNx+',top='+zNy;
								var zRuta		= "frdoi006.php?gWhat=WINDOW&gFunction="+xLink+"&gCliId=false&gDivDHLId&gDivDHLId="+document.frnav[xLink].value.toUpperCase();
								if (xIteration == -1){
									zRuta = "frdoi006.php?gWhat=WINDOW&gFunction="+xLink+"&gCliId=false&gDivDHLId&gDivDHLId=";
								}
								zWindow = window.open(zRuta,"zWindow",zWinPro);
								zWindow.focus();
							} else {
								if (xSwitch == "EXACT") {
									var zRuta = "frdoi006.php?gWhat=EXACT&gFunction="+xLink+"&gCliId=false&gDivDHLId&gDivDHLId="+document.frnav[xLink].value.toUpperCase();
									parent.fmpro.location = zRuta;
								}
							}
						}
						break;
          case "vauxid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vauxdes'].value = '';
            }

            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi141.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gAuxId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi141.php?gWhat=WINDOW&gFunction=" + xLink + "&gAuxId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi141.php?gWhat=WINDOW&gFunction=" + xLink + "&gAuxId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vtdeid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vtdedes'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi127.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gTdeId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi127.php?gWhat=WINDOW&gFunction=" + xLink + "&gTdeId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi127.php?gWhat=WINDOW&gFunction=" + xLink + "&gTdeId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vlinid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vlindes'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi119.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gLinId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi119.php?gWhat=WINDOW&gFunction=" + xLink + "&gLinId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi119.php?gWhat=WINDOW&gFunction=" + xLink + "&gLinId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vpieid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vpienom'].value = '';
              document.frnav['vpiepai'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi125.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gPieId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi125.php?gWhat=WINDOW&gFunction=" + xLink + "&gPieId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi125.php?gWhat=WINDOW&gFunction=" + xLink + "&gPieId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vdaaid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vdaades'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi110.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gDaaId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi110.php?gWhat=WINDOW&gFunction=" + xLink + "&gDaaId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi110.php?gWhat=WINDOW&gFunction=" + xLink + "&gDaaId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vpaiid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vpaides'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi052.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gPaiId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi052.php?gWhat=WINDOW&gFunction=" + xLink + "&gPaiId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi052.php?gWhat=WINDOW&gFunction=" + xLink + "&gPaiId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vpaibanid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vpaibandes'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi052.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gPaiId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi052.php?gWhat=WINDOW&gFunction=" + xLink + "&gPaiId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi052.php?gWhat=WINDOW&gFunction=" + xLink + "&gPaiId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vpaiid3":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vpaides3'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi052.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gPaiId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi052.php?gWhat=WINDOW&gFunction=" + xLink + "&gPaiId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi052.php?gWhat=WINDOW&gFunction=" + xLink + "&gPaiId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vsubpaiid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vsubpaides'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi052.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gPaiId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi052.php?gWhat=WINDOW&gFunction=" + xLink + "&gPaiId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi052.php?gWhat=WINDOW&gFunction=" + xLink + "&gPaiId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vpaiite":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vpaiitedes'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi052.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gPaiId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi052.php?gWhat=WINDOW&gFunction=" + xLink + "&gPaiId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi052.php?gWhat=WINDOW&gFunction=" + xLink + "&gPaiId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vtraid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vtrades'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi133.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gTraId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi133.php?gWhat=WINDOW&gFunction=" + xLink + "&gTraId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi133.php?gWhat=WINDOW&gFunction=" + xLink + "&gTraId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vmonid":
          case "vmonidsg":
          case "vfmonid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              switch (xLink) {
                case "vmonid":
                  document.frnav['vmondes'].value = '';
                  break;
                case "vmonidsg":
                  document.frnav['vmondessg'].value = '';
                  break;
                case "vfmonid":
                  document.frnav['vfmondes'].value = '';
                  break;
              }
            }

            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi111.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gMonId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi111.php?gWhat=WINDOW&gFunction=" + xLink + "&gMonId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi111.php?gWhat=WINDOW&gFunction=" + xLink + "&gMonId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vodiid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vodides'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi103.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gOdiId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi103.php?gWhat=WINDOW&gFunction=" + xLink + "&gOdiId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi103.php?gWhat=WINDOW&gFunction=" + xLink + "&gOdiId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vmtrid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vmtrdes'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi120.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gMtrId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi120.php?gWhat=WINDOW&gFunction=" + xLink + "&gMtrId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi120.php?gWhat=WINDOW&gFunction=" + xLink + "&gMtrId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vdepid":
          case "vdepid2":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              switch (xLink) {
                case "vdepid":
                  document.frnav['vdepdes'].value = '';
                  break;
                case "vdepid2":
                  document.frnav['vdepdes2'].value = '';
                  break;
              }
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi054.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gDepId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi054.php?gWhat=WINDOW&gFunction=" + xLink + "&gDepId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi054.php?gWhat=WINDOW&gFunction=" + xLink + "&gDepId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vciuid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vciudes'].value = '';
            }
            var gDep = document.frnav.vdepid.value;
            if (gDep.length == 0) {
              alert('Debe seleccionar Departamento');
              document.frnav.vciuid.value = '';
              document.frnav.vciudes.value = '';
            } else {
              if (xSwitch == "VALID" && document.frnav[xLink].value.length > 0) {
                var zRuta = "frdoi055.php?gWhat=VALID&gDep=" + gDep + "&gFunction=" + xLink + "&gCiuId=" + document.frnav[xLink].value.toUpperCase() + "";
                parent.fmpro.location = zRuta;
              } else {
                if (xSwitch == "WINDOW") {
                  var zNx = (zX - 600) / 2;
                  var zNy = (zY - 550) / 2;
                  var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                  var zRuta = "frdoi055.php?gWhat=WINDOW&gDep=" + gDep + "&gFunction=" + xLink + "&gCiuId=" + document.frnav[xLink].value.toUpperCase() + "";
                  if (xIteration == -1) {
                    zRuta = "frdoi055.php?gWhat=WINDOW&gDep=" + gDep + "&gFunction=" + xLink + "&gCiuId=";
                  }
                  zWindow = window.open(zRuta, "zWindow", zWinPro);
                  zWindow.focus();
                } else {
                  if (xSwitch == "EXACT") {
                    var zRuta = "frdoi055.php?gWhat=EXACT&gDep=" + gDep + "&gFunction=" + xLink + "&gCiuId=" + document.frnav[xLink].value.toUpperCase() + "";
                    parent.fmpro.location = zRuta;
                  }
                }
              }
            }
            break;
          case "vfpiid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vfpides'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi115.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gFpiId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi115.php?gWhat=WINDOW&gFunction=" + xLink + "&gFpiId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi115.php?gWhat=WINDOW&gFunction=" + xLink + "&gFpiId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vtimid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vtimdes'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi131.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gTimId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi131.php?gWhat=WINDOW&gFunction=" + xLink + "&gTimId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi131.php?gWhat=WINDOW&gFunction=" + xLink + "&gTimId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "varcid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['varcdes'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi104.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gArcId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi104.php?gWhat=WINDOW&gFunction=" + xLink + "&gArcId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi104.php?gWhat=WINDOW&gFunction=" + xLink + "&gArcId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vmodid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vmoddes'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi121.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gModId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi121.php?gWhat=WINDOW&gFunction=" + xLink + "&gModId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi121.php?gWhat=WINDOW&gFunction=" + xLink + "&gModId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vaceid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vacedes'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi102.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gAceId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi102.php?gWhat=WINDOW&gFunction=" + xLink + "&gAceId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi102.php?gWhat=WINDOW&gFunction=" + xLink + "&gAceId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vcetid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vcetdes'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi107.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gCetId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi107.php?gWhat=WINDOW&gFunction=" + xLink + "&gCetId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi107.php?gWhat=WINDOW&gFunction=" + xLink + "&gCetId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vtemid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vtemdes'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi112.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gTemId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi112.php?gWhat=WINDOW&gFunction=" + xLink + "&gTemId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi112.php?gWhat=WINDOW&gFunction=" + xLink + "&gTemId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vumcid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vumcdes'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi056.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gUmcId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi056.php?gWhat=WINDOW&gFunction=" + xLink + "&gUmcId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi056.php?gWhat=WINDOW&gFunction=" + xLink + "&gUmcId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vtriid":
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vtrides'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              var zRuta = "frdoi132.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gTriId=" + document.frnav[xLink].value.toUpperCase() + "";
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 600) / 2;
                var zNy = (zY - 550) / 2;
                var zWinPro = 'width=600,scrollbars=1,height=550,left=' + zNx + ',top=' + zNy;
                var zRuta = "frdoi132.php?gWhat=WINDOW&gFunction=" + xLink + "&gTriId=" + document.frnav[xLink].value.toUpperCase() + "";
                if (xIteration == -1) {
                  zRuta = "frdoi132.php?gWhat=WINDOW&gFunction=" + xLink + "&gTriId=";
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
          case "vproid":
            var nProTipo = 0;
            //  alert(document.forms['frnav']['rapro'].value);
            if (document.getElementById("xProdOld").checked == true) {
              nProTipo = 1;
            }
            if (document.getElementById("xProdNew").checked == true) {
              nProTipo = 2;
            }
            if (xSwitch == "VALID" && document.frnav[xLink].value == '') {
              document.frnav['vprodes'].value = '';
            }
            if ((xSwitch == "VALID" || xSwitch == "EXACT") && document.frnav[xLink].value.length > 0) {
              if (nProTipo == 1) {
                var zRuta = "frdo0124.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gProId=" + document.frnav[xLink].value.toUpperCase() + "&uncampo=uno" + "";
              } else {
                var zRuta = "frdoi190.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gProId=" + document.frnav[xLink].value.toUpperCase() + "&uncampo=uno" + "";
              }
              parent.fmpro.location = zRuta;
            } else {
              if (xSwitch == "WINDOW") {
                var zNx = (zX - 800) / 2;
                var zNy = (zY - 600) / 2;
                var zWinPro = 'width=800,scrollbars=1,height=600,left=' + zNx + ',top=' + zNy;
                if (nProTipo == 1) {
                  var zRuta = "frdo0124.php?gWhat=WINDOW&gFunction=" + xLink + "&gProId=" + document.frnav[xLink].value.toUpperCase() + "&uncampo=uno" + "";
                } else {
                  var zRuta = "frdoi190.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gProId=" + document.frnav[xLink].value.toUpperCase() + "&uncampo=uno" + "";
                }
                if (xIteration == -1) {
                  if (nProTipo == 1) {
                    zRuta = "frdo0124.php?gWhat=WINDOW&gFunction=" + xLink + "&gProId=";
                  } else {
                    var zRuta = "frdoi190.php?gWhat=" + xSwitch + "&gFunction=" + xLink + "&gProId=" + document.frnav[xLink].value.toUpperCase() + "&uncampo=uno" + "";
                  }
                }
                zWindow = window.open(zRuta, "zWindow", zWinPro);
                zWindow.focus();
              }
            }
            break;
        }
      }

      function fnMostrarAnios(xModoCon){ 
        document.forms['frnav']['vAnio'].value = "";
        switch(xModoCon){
          case "HISTORICO":
            document.getElementById('trAnios').style.display = '';
          break; 
          default: 
            document.getElementById('trAnios').style.display = 'none';
          break;
        }
      }
    </script>
    <script languaje = 'javascript'>
      function chDate(fld) {
				var reg = /[.+"'*?^${}()|[\]\\a-zA-Z\u00C0-\u017F]+/gi
				var val = fld.value;
				var ok = 1;
				if (val.length > 0){
					if(val != "" && reg.test(val)){
						alert('Formato de Fecha debe ser aaaa-mm-dd');
						fld.value = '';
						fld.focus();
						ok = 0;
					}
					if (ok == 1 && (val.length < 10 || val.length > 10)){
						alert('Formato de Fecha debe ser aaaa-mm-dd');
						fld.value = '';
						fld.focus();
						ok = 0;
					}
					if(val.substr(4,1) == '-' && val.substr(7,1) == '-' && ok == 1){
						var anio = val.substr(0,4);
						var mes  = val.substr(5,2);
						var dia  = val.substr(8,2);
						if (mes.substr(0,1) == '0'){
							mes = mes.substr(1,1);
						}
						if (dia.substr(0,1) == '0'){
							dia = dia.substr(1,1);
						}

						if ((val != "") && (anio < 1999)) {
							alert('El A\u00F1o debe ser mayor a 1999');
							fld.value = '';
							fld.focus();
						}
						if ((val != "") && (mes < 1 || mes > 12)) {
							alert('El mes debe ser mayor a 0 o menor a 13');
							fld.value = '';
							fld.focus();
						}
						if (dia > 31){
							alert('El dia debe ser menor a 32');
							fld.value = '';
							fld.focus();
						}
						var aniobi = 28;
						if(anio % 4 ==  0){
							aniobi = 29;
						}
						if (mes == 4 || mes == 6 || mes == 9 || mes == 11){
							if (dia < 1 || dia > 30){
								alert('El dia debe ser menor a 31, dia queda en 30');
								fld.value = val.substr(0,8)+'30';
							}
						}
						if (mes == 1 || mes == 3 || mes == 5 || mes == 7 || mes == 8 || mes == 10 || mes == 12){
							if (dia < 1 || dia > 32){
								alert('El dia debe ser menor a 32');
								fld.value = '';
								fld.focus();
							}
						}
						if(mes == 2 && aniobi == 28 && dia > 28 ){
							alert('El dia debe ser menor a 29');
							fld.value = '';
							fld.focus();
						}
						if(mes == 2 && aniobi == 29 && dia > 29){
							alert('El dia debe ser menor a 30');
							fld.value = '';
							fld.focus();
						}
						if ((val != "") && (mes == 2 && dia < 1)) {
							alert('El dia debe ser mayor a 0');
							fld.value = '';
							fld.focus();
						}
					}else{
						if(val.length > 0){
							alert('Fecha erronea, Verifique');
						}
						fld.value = '';
						fld.focus();
					}
				}
			}
    </script>
    <script language="javascript">
      function f_Plantilla() {
        document.frnav.sql.value = '';
        var cCadPl = "|";
        //check exactos
        var cNoinc = '~gUser~super~vobjetos~sql~valtdav~valudav~valudavitem~valtdavitem~';
        //campos con nombres que no son hacen parte del estandar
        var cCh = '~chnoc~chmac~chtip~chcla~chmod~chref~chotc~chnte~chpr2~chpr3~chnem~chnca~chcon~chdes~chdsf~chdin~chdfi~chser~chudav~valtdav~valudav~valudavitem~valtdavitem~';
        var bIsop = 0;
        var nOp = 0;

        for (x = 0; x < document.frnav.elements.length; x++) {
          if (document.frnav.elements[x].name) {
            var cNom = document.frnav.elements[x].name;
            if (cNom.substring(0, 2) == "ch") {
              var sNumCh = cNom.substring(2, cNom.length);
              //validacion que ch termine en un numero
              if (/^([0-9])*$/.test(sNumCh)) {
                //Solamente se guardan plantillas diligenciadas
                if (document.frnav[cNom].checked == true) {
                  //nombre del input hidden equivalente al chx
                  var cNomInput = document.forms['frnav']['chv' + sNumCh].value;
                  //campo memo
                  cCadPl += cNomInput + "~checked~true|";
                  //nombre del input hidden equivalente al txx
                  var cTexto = document.forms['frnav']['cht' + sNumCh].value;
                  //valor del input txx
                  var cOrden = document.forms['frnav']['tx' + sNumCh].value;
                  cCadPl += cTexto + "~value~" + cOrden + "|";
                }
              } else {
                if (cCh.indexOf(cNom) > 0) {
                  if (document.forms['frnav'][cNom].checked == true) {
                    cCadPl += cNom + "~checked~true|";
                  }
                }
              }
            } else if (cNom != "undefined" && cNom.substring(0, 3) != "chv" && cNom.substring(0, 3) != "cht" && cNom.substring(0, 2) != "tx" && cNom.substring(0, 2) != "ch" && cNom.substring(0, 3) != "sql" && cNoinc.indexOf(cNom) < 0) {

              //Solamente se guardan campos diligenciados
              var valor = document.forms['frnav'][cNom].value;
              if (valor != "") {
                cCadPl += cNom + "~value~" + valor + "|";
              }
            } else {
              if (cCh.indexOf(cNom) > 0) {
                if (document.forms['frnav'][cNom].checked == true) {
                  cCadPl += cNom + "~checked~true|";
                }
              }
            }
          }
        }

        document.frnav.sql.value = cCadPl;
        var x = screen.width;
        var y = screen.height;
        var nx = (x - 450) / 2;
        var ny = (y - 250) / 2;
        var str = 'width=450,scrollbars=1,height=250,left=' + nx + ',top=' + ny;
        var rut = "fr147200n.php?gUser=<? echo $kUser ?>&tipnav=1&cForm=fr00003";
        msg = window.open(rut, 'myw', str);
        msg.focus();
      }

      function f_CargaPlantilla() {
        var cContenido = document.frnav.sql.value;
        var aContenid = cContenido.split("|");
        for (x = 0; x < aContenid.length; x++) {
          if (aContenid[x].length > 0) {
            document.aContenid[x];
          }
        }
      }

      function f_Buscar() {
        var x = screen.width;
        var y = screen.height;
        var nx = (x - 650) / 2;
        var ny = (y - 450) / 2;
        var str = 'width=650,height=450,scrollbars=1,left=' + nx + ',top=' + ny;
        var rut = 'fr147200.php?gUser=<?php $kUser ?>&cForm=fr00003';
        msg = window.open(rut, 'mywb', str);
        msg.focus();
      }

      function f_Marca(tipo) {
        var vobj = 1 * (document.frnav.vobjetos.value);
        document.frnav.gMayor.value = 1;
        for (n = 1; n <= vobj; n++) {
          if (tipo == 1) {
            document.frnav["ch" + n].checked = true;
            f_Ordena(n);
          } else {
            document.frnav["ch" + n].checked = false;
            document.frnav["tx" + n].value = '';
          }
        }
      }

      function fnSelPro057(xSel){
        var cSel = document.frnav[xSel].value;
        if(cSel == "" || cSel == "NO"){
          document.frnav[xSel].value = "SI";
        }else{
          document.frnav[xSel].value = "NO";
        }
      }

      function f_Ordena(xCh) {
        var vobj = 1 * (document.frnav.vobjetos.value);
        var opt = 1;


        //RDSP
        //alert ("99:98 vobj,xCh..."+vobj+","+xCh);
        //alert("document.frnav[ch + xCh].checked..."+document.frnav["ch" + xCh].checked);

        if (document.frnav["ch" + xCh].checked == true) {
          var xText = "tx" + xCh;
          var xVal = 1 * document.frnav.gMayor.value;
          document.frnav[xText].value = document.frnav.gMayor.value;
          document.frnav.gMayor.value = eval(xVal + 1);
        } else {
          var vDel = 1 * (document.frnav["tx" + xCh].value);
          for (n = 1; n <= vobj; n++) {
            if (document.frnav["ch" + n].checked == true) {
              var xText = "tx" + n;
              var xVal = 1 * (document.frnav[xText].value);
              if (xVal > vDel) {
                document.frnav[xText].value = eval(xVal - 1);
              }
            }
          }
          var xText2 = "tx" + xCh;
          document.frnav[xText2].value = "";
          var my = 1;
          var vc = 1;
          var nContador = 0;
          for (n = 1; n <= vobj; n++) {
            if (document.frnav["ch" + n].checked == true) {
              nContador++;
              vc = 1 * (document.frnav["tx" + n].value);
              if (vc > my) {
                my = vc;
              }
            }
          }
          if (my >= 1) {
            my++;
          }
          if (nContador == 0) {
            my = 1;
          }
          document.frnav.gMayor.value = my;
        }
      }
    </script>
    <script language = "JavaScript" src = '../../programs/dockit.js'></script>
    <style type="text/css">
      .dockclass{
        position:absolute;
      }
    </style>
  </head>
  <body topmargin=0 leftmargin=0  marginwidth=0 marginheight=0 style = 'margin-right : 0' link = '#0000FF' vlink = '#0000FF' alink = '#0000FF'>
    <div id="dockcontent0" class="dockclass" style="height:25px; width:91px">
      <table border = '0' cellpadding = '0' cellspacing = '0' style = 'width:91'>
        <tr>
          <td width="91" height="200"></td>
        </tr>
        <tr>
          <td width="91" height="2"></td>
        </tr>
        <tr>
          <td width="91" height="21" background="../../graphics/btn_ok_bg.gif" onmousedown='javascript:f_ArmaSql(2)' style = 'cursor:hand'>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Consultar
          </td>
        </tr>
        <tr>
          <td width="91" height="21"background="../../graphics/btn_ok_bg.gif" onmousedown='javascript:f_Plantilla()' style = 'cursor:hand'>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Plantilla
          </td>
        </tr>
        <tr>
          <td width="91" height="21" background="../../graphics/btn_ok_bg.gif" onmousedown='javascript:f_Buscar()' style = 'cursor:hand'>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Buscar
          </td>
        </tr>
        <tr>
          <td width="91" height="21" background="../../graphics/btn_ok_bg.gif" onmousedown='javascript:f_Despeja()' style = 'cursor:hand'>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Despejar
          </td>
        </tr>
        <tr>
          <td width="91" height="21" background="../../graphics/btn_ok_bg.gif" style="cursor:hand"
              onClick = "javascript:f_Marca(1)">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Marca Todo
          </td>
        </tr>
        <tr>
          <td width="91" height="21" background="../../graphics/btn_cancel_bg.gif" style="cursor:hand"
              onClick = "javascript:f_Marca(2)">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Desmarca
          </td>
       	</tr>
        <tr>
          <td width="91" height="21" background="../../graphics/btn_cancel_bg.gif" style="cursor:hand"
              onClick = "javascript:f_Retorna()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
          </td>
        </tr>
        <tr>
          <td width="91" height="2"></td>
        </tr>
      </table>
    </div>
    <?php

    $act      = "ACTIVO";
    $fec      = f_Fecha();
    $usr2     = '';
    $super    = '';
    $flcta    = 0;
    $director = '';
    $cliente  = '';
    $vcli     = '';
    $vclin    = '';
    $sqlusr2  = "SELECT USRID2XX,USRSUPXX FROM $kMysqlDb.SIAI0003 WHERE USRIDXXX ='$kUser' LIMIT 0,1";
    $resusr2  = f_MySql("SELECT","",$sqlusr2,$xConexion01,"");
    $nre      = mysql_num_rows($resusr2);
    if ($nre > 0) {
      $rusr2  = mysql_fetch_array($resusr2);
      $usr2   = trim($rusr2['USRID2XX']);
      $super  = trim($rusr2['USRSUPXX']);
      $sqlcta = "SELECT USRIDXXX,USRID2XX,USRNOMXX FROM $kMysqlDb.SIAI0003 WHERE USRIDXXX ='$usr2' LIMIT 0,1";
      $rescta = f_MySql("SELECT","",$sqlcta,$xConexion01,"");
      $rcta   = mysql_fetch_array($rescta);
      $director = $rcta['USRNOMXX'];

      if ($rcta['USRIDXXX'] == $rcta['USRID2XX']) {
        $flcta = 1;
      }
    }
    if ($flcta == 1) {
      $sql150 = "SELECT CLIIDXXX,CLINOMXX FROM $kMysqlDb.SIAI0150 WHERE CLIIDXXX = '$kUser' LIMIT 0,1";
      $res150 = f_MySql("SELECT","",$sql150,$xConexion01,"");
      $fil150 = mysql_num_rows($res150);
      if ($fil150 > 0) {
        //$cliente = 'X';
        //$super = '';
        $r150 = mysql_fetch_array($res150);
        //$vcli = $r150['CLIIDXXX'];
        //$vclin = $r150['CLINOMXX'];
        /* <script languaje = 'javascript'>
          document.frnav.vcliid.value =".'"'.$vcli.'";';
          document.frnav.vclinom.value =".'"'.$vclin.'";';
          </script> */
      }
    }

    $sq009 = "SELECT SIAI0150.CLINOMXX,";
    $sq009 .= "SIAI0009.CLIIDXXX";
    $sq009 .= " FROM $kMysqlDb.SIAI0150,$kMysqlDb.SIAI0009";
    $sq009 .= " WHERE SIAI0009.USRIDXXX = \"$kUser\"";
    $sq009 .= " AND SIAI0150.CLIIDXXX = SIAI0009.CLIIDXXX LIMIT 0,1";

    $cCli = "";
    $cClo = "";
    $zR009 = f_MySql("SELECT","",$sq009,$xConexion01,"");
    while ($z009 = mysql_fetch_array($zR009)) {
      $cClo = $z009["CLINOMXX"];
      $cCli = $z009["CLIIDXXX"];
      $cliente = "X";
    }

    ##CONSULTAR CUENTA TIPO CLIENTE ##
    $sql = "SELECT * ";
    $sql .= "FROM $kMysqlDb.SIAI0003 ";
    $sql .= "WHERE ";
    $sql .= "USRIDXXX = \"$kUser\" AND ";
    $sql .= "REGESTXX = \"ACTIVO\" ";
    $zCrsUsr = f_MySql("SELECT","",$sql,$xConexion01,"");
    $zRow = mysql_fetch_array($zCrsUsr);
    $TipCli = $zRow['USRTIPXX'];

    if ($TipCli == "CLIENTE") {
      $excli = explode("~", $zRow['USRCLIXX']);
      $Cliente = "AND SIAI0206.CLIIDXXX IN ( ";

      for ($i = 0; $i < count($excli); $i++) {
        $Cadena .= "$excli[$i],";
      }
      $Cliente .= substr($Cadena, 0, (strlen($Cadena)) - 1);
      $Cliente .= " ) ";
    }
    ##FIN CONSULTAR CUENTA TIPO CLIENTE ##
    ?>
    <script language = "javascript">
      function unid(tip) {
        switch (tip) {
          case 0:
            var x = screen.width;
            var y = screen.height;
            var nx = (x - 450) / 2;
            var ny = (y - 450) / 2;
            var str = 'width=450,scrollbars=1,height=450,left=' + nx + ',top=' + ny;
            var fecha = document.frnav.vtcafec1.value;
            var rut = 'frdoitas3.php?valor=1&fecha=' + fecha;
            msg = window.open(rut, 'myw', str);
            msg.focus();
            break;
        }
      }
    </script>
    <?php
    $icsc = 1;
    if ($super == 'X' || $cliente == 'X' || $flcta == 1) {
      $aOdi = array();
      $sqodi = "SELECT ODIIDXXX,ODIDESXX FROM $kMysqlDb.SIAI0103 ORDER BY ODIIDXXX";
      $resodi = f_MySql("SELECT","",$sqodi,$xConexion01,"");
      $iod = 0;
      while ($rodi = mysql_fetch_array($resodi)) {
        $aOdi[$iod]["ODIIDXXX"] = $rodi["ODIIDXXX"];
        $aOdi[$iod]["ODIDESXX"] = $rodi["ODIDESXX"];
        $iod++;
      }
      ?>
      <form name = 'frimpodo' action = "frconimp.php" target ="fmpro">
        <input type='hidden' name= 'cDoiId' value = '<?php echo $_POST['cDoiId']?>'>
        <input type='hidden' name= 'cDoiSfId' value = '<?php echo $_POST['cDoiSfId']?>'>
      </form>
      <form name = 'frnav' action = 'fr00003f.php' method = 'post' target = 'mywin' onSubmit = 'javascript:enwindow()'>
        <input type='hidden' name= 'gUser' value = '<?php echo $kUser ?>'>
        <input type='hidden' name= 'super' value = '<?php echo $super ?>'>
        <input type='hidden' name= 'gMayor' value = 1>
        <center>
          <table width = '720'  border = 0 cellpadding = 0 cellspacing = 0>
            <tr>
              <td>
                <fieldset>
                  <legend>Consulta General Importaciones X Item</legend>
                  <center>
                    <table width = '720'  border = 0 cellpadding = 0 cellspacing = 0>
                      <?php echo f_Columnas(36, 20); ?>
                      <?php
                      $vSysApPa = explode('~', $vSysStr['sys_aplica_particionamiento']);
                      if($vSysStr['sys_aplica_particionamiento'] ==! "" AND $vSysApPa[0] == "SI") {
                        ?>
                        <tr>
                          <td Class = 'letra7' colspan="12">Generar consulta sobre</td>
                          <td Class = 'letra7' colspan="16">Actual<input type="radio" name="cModoCon" value="ACTUAL" checked onchange="fnMostrarAnios(this.value)"/> </td>
                          <td Class = 'letra7' colspan="8">Historico <input type="radio" name="cModoCon" value="HISTORICO" onchange="fnMostrarAnios(this.value)"/> </td>
                        </tr>
                        <tr id="trAnios" style="display:none;">
                          <td Class = 'letra7' colspan="12">A&ntilde;o</td>
                          <td Class = 'letra7' colspan = "8">
                            <select name="vAnio" Class = 'letrase' style = 'width:160;height:19'>
                              <option value=""></option>
                              <?php
                                for($i = $vSysApPa[1]; $i <= $vSysApPa[2]; $i++){ ?>
                                  <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                  <?php
                                }
                              ?>
                            </select>
                          </td>
                        </tr>
                      <?php }else{ ?>
                        <input type="hidden" name="cModoCon">
                        <?php
                      } ?> 
                      <tr>
                        <td Class = 'letra7' colspan = '12'>Sucursal</td>
                        <?php
                        $sqladm = "SELECT LINIDXXX,LINDESXX FROM $kMysqlDb.SIAI0119 WHERE LINCSCXX > 0 ORDER BY LINDESXX";
                        $resadm = f_MySql("SELECT","",$sqladm,$xConexion01,"");
                        $filadm = mysql_fetch_array($resadm);
                        ?>
                        <td Class = 'letra7' colspan = '8'>
                          <select name = 'vadmid' Class = 'letrase' style = 'width:160;height:19'>
                            <option value =''></option>
                            <?php
                            while ($row = mysql_fetch_array($resadm)) {
                              $vl = $row['LINIDXXX'];
                              $ds = $row['LINDESXX'];
                              ?>
                              <option value = '<?php echo $vl ?>'><?php echo $ds ?></option>
                            <?php }
                            ?>
                          </select>
                        </td>
                        <td colspan = '8'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vadmid.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0205.ADMIDXXX AS SUCURSAL" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvadmid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvadmid'>
                          <input type = 'hidden' name ='txvadmid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvadmid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'>#DO/Imp</td>
                        <td Class = 'letra7' colspan = '8'><input type = 'text' Class = 'letra' name = 'vdoiid' style = 'width:160'
                            onblur= 'javascript:this.value = this.value.toUpperCase();
                                                fnCargarImpoGla();'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' Class = 'letra' name = 'vdoisfid' style = 'width:40'
                          onblur= 'javascript:fnCargarImpoGla()'></td>
                        <td colspan = '6'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vdoiid.value = '';
                            document.frnav.vdoisfid.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0205.DOIIDXXX AS DO_IMP,SIAI0205.DOISFIDX AS DO_SUF,SIAI0205.ITEIDXXX AS ITEM" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvdoiid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvdoiid'>
                          <input type = 'hidden' name ='txvdoiid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvdoiid'>
                        </td>
                      </tr>
                      <?php
                      switch($kMysqlDb) {
                        case "ROLDANLO":
                        case "TEROLDANLO":
                        case "DEROLDANLO":
                        case "DEDESARROL":
                          ?>
                          <tr>
                            <?php $icsc++; ?>
                            <td class="name" colspan="12"><a href="javascript:uLinks('vsccid','WINDOW',-1)">Oficina Operadora / Subcentro de Costo</a>
                            </td>
                            <td class="name" colspan="4">
                              <input type="text" class="letra" name="vsccid" style="width: 80px;"
                                onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                      uLinks('vsccid', 'VALID');
                                                      this.style.background = '#FFFFFF'"
                                                      onFocus="javascript:this.style.background = '#00FFFF'">
                                <input type="hidden" class="letra" name="vccoid" style="width: 80px;" >
                            </td>
                            <td class="name" colspan="12"><a href="#"></a>
                              <input type="text" class="letra" name="vsccdes" style="width: 240px;" readonly>
                            </td>
                            <td class="letra7" colspan="4"><img src="../../graphics/clear.bmp" onMousedown= "javascript:document.frnav.vccoid.value = '';
                                                                                                                        document.frnav.vsccid.value = '';
                                                                                                                        document.frnav.vsccdes.value = ''"></td>
                            <td class="letra7" colspan="2"><input type="checkbox" name="ch<?php echo $icsc ?>" id="SIAI0200.SCCIDXXX AS CODIGO_OFICINA_OPERADORA, '' AS OFICINA_OPERADORA" onclick="javascript:f_Ordena(<?php echo $icsc ?>)"></td>
                            <td class="letra7" colspan="2"><input type="text" class="letra" name="tx<?php echo $icsc ?>" style="width: 40px;text-align: right;" readonly></td>
                            <td>
                              <input type="hidden" name="chvsccid" value="ch<?php echo $icsc ?>">
                              <input type="hidden" name="chv<?php echo $icsc ?>" value="chvsccid">
                              <input type="hidden" name="txvsccid" value="tx<?php echo $icsc ?>">
                              <input type="hidden" name="cht<?php echo $icsc ?>" value="txvsccid">
                            </td>
                          </tr>
                          <?php
                        break;
                      }
                      ?>

                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'># Pedido DO</td>
                        <td Class = 'letra7' colspan = '20'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0200.DOIPEDXX AS PEDIDO_DO" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvdoipeddo' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvdoipeddo'>
                          <input type = 'hidden' name ='txvdoipeddo' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvdoipeddo'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'># Pedido</td>
                        <td Class = 'letra7' colspan = '10'><input type = 'text' Class = 'letra' name = 'vdoiped' style = 'width:200' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '6'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vdoiped.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0205.DOIPEDXX AS PEDIDO" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvdoiped' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvdoiped'>
                          <input type = 'hidden' name ='txvdoiped' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvdoiped'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'># Orden de Compra</td>
                        <td Class = 'letra7' colspan = '10'><input type = 'text' Class = 'letra' name = 'vorcid' style = 'width:200' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '6'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vorcid.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0205.ORCIDXXX AS ORDEN_COMPRA" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvorcid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvorcid'>
                          <input type = 'hidden' name ='txvorcid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvorcid'>
                        </td>
                      </tr>
                      <?php
                      switch($kMysqlDb) {
                        case "SIACOSIA":
                        case "TESIACOSIP":
                        case "DESIACOSIP": ?>
                          <tr>
                            <?php $icsc++; ?>
                            <td Class = 'letra7' colspan = '12'># Item_2</td>
                            <td Class = 'letra7' colspan = '10'><input type = 'text' Class = 'letra' name = 'viteid2' style = 'width:200' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                            <td Class = 'letra7' colspan = '6'></td>
                            <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.viteid2.value = '';"></td>
                            <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0205.ITEID2XX AS ITEM_2" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                            <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                            <td>
                              <input type = 'hidden' name ='chviteid2' value = 'ch<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chviteid2'>
                              <input type = 'hidden' name ='txviteid2' value = 'tx<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txviteid2'>
                            </td>
                          </tr>
                        <?php
                        break;
                      } ?>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'><a href='javascript:show_calendar("frnav.vregfec1")'>Periodo Del</a></td>
                        <td Class = 'letra7' colspan = '5'><input type = 'text' Class = 'letra' name = 'vregfec1' style = 'width:100' onBlur = 'chDate(this)'></td>
                        <td Class = 'letra7' colspan = '6'><center><a href='javascript:show_calendar("frnav.vregfec2")'>Al</a></center></td>
                      <td Class = 'letra7' colspan = '5'><input type = 'text' Class = 'letra' name = 'vregfec2'  style = 'width:100' onBlur = 'chDate(this)'></td>
                      <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vregfec1.value = '';
                          document.frnav.vregfec2.value = '';"></td>
                      <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0205.REGFECXX AS CREADO" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvregfec1' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvregfec1'>
                        <input type = 'hidden' name ='txvregfec1' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvregfec1'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'>#Factura</td>
                        <td Class = 'letra7' colspan = '6'><input type = 'text' Class = 'letra' name = 'vfacid' style = 'width:120' onKeyUp = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '10'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vfacid.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0205.FACIDXXX AS FACTURA,SIAI0205.ADMIDXXX AS SUCIDXXX,SIAI0205.DOIIDXXX AS DOCIDXXX,SIAI0205.DOISFIDX AS DOSSUFXX" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvfacid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvfacid'>
                          <input type = 'hidden' name ='txvfacid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvfacid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'>Codigo SAP</td>
                        <td Class = 'letra7' colspan = '6'><input type = 'text' Class = 'letra' name = 'vfacsap' style = 'width:120' onKeyUp = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '10'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vfacid.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0204.FACSAPXX AS CODIGOSAP" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvfacsap' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvfacsap'>
                          <input type = 'hidden' name ='txvfacsap' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvfacsap'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vusrid2','WINDOW',-1)">Director/Declarante</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vusrid2" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vusrid2', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vusrnom2" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vusrid2.value = '';
                            document.frnav.vusrnom2.value = ''"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.DIRECTOR, SIAI0206.USRID2XX AS CC_DECLARANTE,SIAI0206.USRNOMXX AS DECLARANTE" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvusrid2' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvusrid2'>
                          <input type = 'hidden' name ='txvusrid2' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvusrid2'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vusrid3','WINDOW',-1)">Comparte Con</a>
                        </td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vusrid3" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vusrid3', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vusrnom3" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vusrid3.value = '';
                            document.frnav.vusrnom3.value = ''"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>'  id="SIAI0200.USRID3XX AS COMPARTE_CON"  onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvusrid3' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvusrid3'>
                          <input type = 'hidden' name ='txvusrid3' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvusrid3'>
                        </td>
                      </tr>
                      <!-- Ejecutivo de Cuenta -->
                      <tr>
                        <?php $icsc++; ?>
                        <?php
                        switch ($kMysqlDb) {
                          case 'DHLEXPRE':
                          case 'TEDHLEXPRE':
                          case 'DEDHLEXPRE': ?>
                            <td Class = 'name' colspan = '12'><a href = "javascript:fnLinkEjecutivoCuenta('vusrid4','WINDOW',-1)">Ejecutivo de Cuenta</a>
                            </td>
                            <td Class = 'name' colspan = '4'>
                              <input type = "text" Class = "letra" name = "vusrid4" style = "width:80"
                                    onBlur = "javascript:this.value = this.value.toUpperCase();
                                        fnLinkEjecutivoCuenta('vusrid4', 'VALID');
                                        this.style.background = '#FFFFFF'"
                                    onFocus="javascript:this.style.background = '#00FFFF'">
                            </td> 
                            <?php
                          break; 
                          default: ?>
                            <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vusrid4','WINDOW',-1)">Ejecutivo de Cuenta</a>
                            </td>
                            <td Class = 'name' colspan = '4'>
                              <input type = "text" Class = "letra" name = "vusrid4" style = "width:80"
                                onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                      uLinks('vusrid4', 'VALID');
                                                      this.style.background = '#FFFFFF'"
                                onFocus="javascript:this.style.background = '#00FFFF'">
                            </td>
                            <?php
                          break;
                        }
                        ?>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vusrnom4" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vusrid4.value = '';
                            document.frnav.vusrnom4.value = ''"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>'  id="SIAI0200.USRID4XX AS EJECUTIVO_CUENTA"  onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvusrid4' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvusrid4'>
                          <input type = 'hidden' name ='txvusrid4' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvusrid4'>
                        </td>
                      </tr>
                      <!-- Fin Ejecutivo de Cuenta -->
                      <!-- Analista Arancel -->
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vusrid5','WINDOW',-1)">Analista Arancel</a>
                        </td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vusrid5" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vusrid5', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vusrnom5" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vusrid5.value = '';
                            document.frnav.vusrnom5.value = ''"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>'  id="SIAI0200.USRID5XX AS ANALISTA_ARANCEL"  onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvusrid5' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvusrid5'>
                          <input type = 'hidden' name ='txvusrid5' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvusrid5'>
                        </td>
                      </tr>
                      <!-- Fin Analista Arancel -->
                      <!-- Analista Registro -->
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vusrid6','WINDOW',-1)">Analista Registro</a>
                        </td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vusrid6" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vusrid6', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vusrnom6" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vusrid6.value = '';
                            document.frnav.vusrnom6.value = ''"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>'  id="SIAI0200.USRID6XX AS ANALISTA_REGISTRO"  onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvusrid6' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvusrid6'>
                          <input type = 'hidden' name ='txvusrid6' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvusrid6'>
                        </td>
                      </tr>
                      <!-- Fin Analista Registro -->
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vusrid1','WINDOW',-1)">Digitador</a>
                        </td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vusrid1" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vusrid1', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vusrnom1" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vusrid1.value = '';
                            document.frnav.vusrnom1.value = ''"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0200.USRIDXXX AS DIGITADOR"onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvusrid1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvusrid1'>
                          <input type = 'hidden' name ='txvusrid1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvusrid1'>
                        </td>
                      </tr>
											<?php
											switch($kMysqlDb) {
												case "DHLXXXXX":
												case "TEDHLXXXXX":
												case "DEDHLXXXXX":
												case "DEDESARROL":
													?>
													<tr>
														<?php $icsc++; ?>
														<td class="name" colspan="12"><a href="javascript:uLinks('cCosDHLId','WINDOW',-1)">Centro de Costos DHL</a>
														</td>
														<td class="name" colspan="4">
															<input type="text" class="letra" name="cCosDHLId" style="width: 80px;"
																onBlur ="javascript:this.value = this.value.toUpperCase();
                                                    uLinks('cCosDHLId', 'VALID');
                                                    this.style.background = '#FFFFFF'"
                                onFocus="javascript:this.style.background = '#00FFFF'">
														</td>
														<td class="name" colspan="12"><a href="#"></a>
															<input type="text" class="letra" name="cCosDHLDes" style="width: 240px;" readonly>
														</td>
                            <td class="letra7" colspan="4"><img src="../../graphics/clear.bmp" onMousedown= "javascript:document.frnav.cCosDHLId.value = '';
                                                                                                                        document.frnav.cCosDHLDes.value = ''"></td>
                            <td class="letra7" colspan="2"><input type="checkbox" name="ch<?php echo $icsc ?>" id="SIAI0200.DOICCOXX AS CODIGO_CENTRO_COSTOS_DHL" onclick="javascript:f_Ordena(<?php echo $icsc ?>)"></td>
														<td class="letra7" colspan="2"><input type="text" class="letra" name="tx<?php echo $icsc ?>" style="width: 40px;text-align: right;" readonly></td>
														<td>
															<input type="hidden" name="chcCosDHLId" value="ch<?php echo $icsc ?>">
															<input type="hidden" name="chv<?php echo $icsc ?>" value="chcCosDHLId">
															<input type="hidden" name="txcCosDHLId" value="tx<?php echo $icsc ?>">
															<input type="hidden" name="cht<?php echo $icsc ?>" value="txcCosDHLId">
														</td>
													</tr>
													<tr>
														<?php $icsc++; ?>
														<td class="name" colspan="12"><a href="javascript:uLinks('cDivDHLId','WINDOW',-1)">Divisi&oacute;n DHL</a>
														</td>
														<td class="name" colspan="4">
															<input type="text" class="letra" name="cDivDHLId" style="width: 80px;"
																onBlur ="javascript:this.value = this.value.toUpperCase();
                                                    uLinks('cDivDHLId', 'VALID');
                                                    this.style.background = '#FFFFFF'"
                                onFocus="javascript:this.style.background = '#00FFFF'">
														</td>
														<td class="name" colspan="12"><a href="#"></a>
															<input type="text" class="letra" name="cDivDHLDes" style="width: 240px;" readonly>
														</td>
                            <td class="letra7" colspan="4"><img src="../../graphics/clear.bmp" onMousedown ="javascript:document.frnav.cDivDHLId.value = '';
                                                                                                                        document.frnav.cDivDHLDes.value = ''"></td>
                            <td class="letra7" colspan="2"><input type="checkbox" name="ch<?php echo $icsc ?>" id="SIAI0200.DOIDIVXX AS CODIGO_DIVISION_DHL" onclick="javascript:f_Ordena(<?php echo $icsc ?>)"></td>
                            <td class="letra7" colspan="2"><input type="text" class="letra" name="tx<?php echo $icsc ?>" style="width: 40px;text-align: right;" readonly></td>
                            <td>
                              <input type="hidden" name="chcDivDHLId" value="ch<?php echo $icsc ?>">
                              <input type="hidden" name="chv<?php echo $icsc ?>" value="chcDivDHLId">
                              <input type="hidden" name="txcDivDHLId" value="tx<?php echo $icsc ?>">
                              <input type="hidden" name="cht<?php echo $icsc ?>" value="txcDivDHLId">
                            </td>
                          </tr>
													<?php
												break;
											}
											?>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vauxid','WINDOW',-1)">Auxiliar / Tramitador</a>
                        </td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vauxid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vauxid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vauxdes" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown  = "javascript:document.frnav.vauxid.value = '';
                                                                                                                            document.frnav.vauxid.value = ''"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0200.AUXIDXXX AS AUXILIAR_TRAMITADOR"onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvauxid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvauxid'>
                          <input type = 'hidden' name ='txvauxid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvauxid'>
                        </td>
                      </tr>
                      <tr>
                        <td Class = 'letra7' colspan = '12'>Seleccione Tipo Producto
                        </td>
                      <!--<td Class = 'name' colspan = '24'>
                      Nombre<input type="radio" name="rapro" value="cIteNoc">
                      Marca<input type="radio" name="rapro" value="cIteMac">
                      Tipo<input type="radio" name="rapro" value="cIteTip">
                      Clase<input type="radio" name="rapro" value="cIteCla">
                      Modelo<input type="radio" name="rapro" value="cIteMod">
                      Referencia<input type="radio" name="rapro" value="cIteRef">
                      Otras<input type="radio" name="rapro" value="cIteOtc">
                      Compl.<input type="radio" name="rapro" value="cProDes" checked>
                        Productos Antiguos<input type="radio" name="rapro" value="cProIda" checked>
                        Productos Res.025<input type="radio" name="rapro" value="cProIdn">
                            </td>!-->
                        <td Class = 'name' colspan = '24'>
                          Productos Antiguos<input type="radio" name="rapro" value="1" id="xProdOld" checked>
                          Productos Res.057<input type="radio" name="rapro" value="2" id="xProdNew">
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vproid','WINDOW',-1)">Cod. Producto</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vproid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vproid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vprodes" style = "width:240" readonly>
                        </td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vproid'].value = '';
                            document.forms['frnav']['vprodes'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.PROIDXXX AS PRODUCTO" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvproid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvproid'>
                          <input type = 'hidden' name ='txvproid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvproid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Nombre Comercial Contiene</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vitenoc' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                      <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chnoc' ></td>
                      <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitenoc'].value = '';
                          document.forms['frnav']['chnoc'].checked = false;"></td>
                      <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITENOCXX AS NOM_COMERCIAL" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvitenoc' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvitenoc'>
                        <input type = 'hidden' name ='txvitenoc' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitenoc'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Marca Comercial Contiene</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vitemac' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                      <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chmac' ></td>
                      <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitemac'].value = '';
                          document.forms['frnav']['chmac'].checked = false;"></td>
                      <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITEMACXX AS MARCA_COMERCIAL" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvitemac' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvitemac'>
                        <input type = 'hidden' name ='txvitemac' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitemac'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Tipo Contiene</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vitetip' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                      <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chtip' ></td>
                      <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitetip'].value = '';
                          document.forms['frnav']['chtip'].checked = false;"></td>
                      <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITETIPXX AS TIPO" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvitetip' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvitetip'>
                        <input type = 'hidden' name ='txvitetip' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitetip'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Clase Contiene</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vitecla' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                      <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chcla' ></td>
                      <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitecla'].value = '';
                          document.forms['frnav']['chcla'].checked = false;"></td>
                      <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITECLAXX AS CLASE" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvitecla' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvitecla'>
                        <input type = 'hidden' name ='txvitecla' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitecla'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Modelo Contiene</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vitemod' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                      <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chmod' ></td>
                      <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitemod'].value = '';
                          document.forms['frnav']['chmod'].checked = false;"></td>
                      <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITEMODXX AS MODELO" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvitemod' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvitemod'>
                        <input type = 'hidden' name ='txvitemod' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitemod'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Referencia Contiene</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'viteref' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                      <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chref' ></td>
                      <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['viteref'].value = '';
                          document.forms['frnav']['chref'].checked = false;"></td>
                      <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITEREFXX AS REFERENCIA" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chviteref' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chviteref'>
                        <input type = 'hidden' name ='txviteref' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txviteref'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Otras Caracteristicas Contiene</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'viteotc' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                      <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chotc' ></td>
                      <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['viteotc'].value = '';
                          document.forms['frnav']['chotc'].checked = false;"></td>
                      <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITEOTCXX AS OTRAS_CARACTERISTICAS" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chviteotc' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chviteotc'>
                        <input type = 'hidden' name ='txviteotc' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txviteotc'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Nombre T&eacute;cnico</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vitente' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                        <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chnte' ></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitente'].value = '';
                            document.forms['frnav']['chnte'].checked = false;"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITENTEXX AS NOMBRE_TECNICO" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvitente' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvitente'>
                          <input type = 'hidden' name ='txvitente' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitente'>
                        </td>
                      </tr>
                      <?php
                      $cBaseDatos = (strlen($cAlfa) == 10) ? strtolower(substr($cAlfa, 2)) : strtolower($cAlfa);
                      if( $vSysStr[$cBaseDatos.'_habilitar_edicion_codigo_dos_y_tres_en_items'] == "SI" ){
                        ?>
                        <tr>
                          <?php $icsc++; ?>
                          <td Class = 'name' colspan = '12'>Cod. Producto Dos</td>
                          <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vproid2' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                          <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                          <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chpr2' ></td>
                          <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vproid2'].value = '';
                              document.forms['frnav']['chpr2'].checked = false;"></td>
                          <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.PROID2XX AS COD_PRODUCTO_DOS" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                          <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                          <td>
                            <input type = 'hidden' name ='chvproid2' value = 'ch<?php echo $icsc ?>'>
                            <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvproid2'>
                            <input type = 'hidden' name ='txvproid2' value = 'tx<?php echo $icsc ?>'>
                            <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvproid2'>
                          </td>
                        </tr>
                        <tr>
                          <?php $icsc++; ?>
                          <td Class = 'name' colspan = '12'>Cod. Producto Tres</td>
                          <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vproid3' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                          <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                          <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chpr3' ></td>
                          <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vproid3'].value = '';
                              document.forms['frnav']['chpr3'].checked = false;"></td>
                          <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.PROID3XX AS COD_PRODUCTO_TRES" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                          <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                          <td>
                            <input type = 'hidden' name ='chvproid3' value = 'ch<?php echo $icsc ?>'>
                            <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvproid3'>
                            <input type = 'hidden' name ='txvproid3' value = 'tx<?php echo $icsc ?>'>
                            <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvproid3'>
                          </td>
                        </tr>
                        <?php
                      }else{
                        ?>
                        <tr>
                          <?php $icsc++; ?>
                          <td Class = 'name' colspan = '12'>Cod. Producto Dos</td>
                          <td Class = 'name' colspan = '20'><input type = 'hidden' name ='vproid2' id='vproid2'></td>
                          <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="'' AS COD_PRODUCTO_DOS,SIAI0205.PROIDXXX AS CODPRID2,SIAI0205.CLIIDXXX AS CODPR2CL" onclick = "javascript:f_Ordena(<?php echo $icsc ?>);fnSelPro057('vproid2')"></td>
                          <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                          <td>
                            <input type = 'hidden' name ='chvproid2' value = 'ch<?php echo $icsc ?>'>
                            <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvproid2'>
                            <input type = 'hidden' name ='txvproid2' value = 'tx<?php echo $icsc ?>'>
                            <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvproid2'>
                          </td>
                        </tr>
                        <tr>
                          <?php $icsc++; ?>
                          <td Class = 'name' colspan = '12'>Cod. Producto Tres</td>
                          <td Class = 'name' colspan = '20'><input type = 'hidden' name ='vproid3' id='vproid3'></td>
                          <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="'' AS COD_PRODUCTO_TRES,SIAI0205.PROIDXXX AS CODPRID3,SIAI0205.CLIIDXXX AS CODPR3CL" onclick = "javascript:f_Ordena(<?php echo $icsc ?>);fnSelPro057('vproid3')"></td>
                          <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                          <td>
                            <input type = 'hidden' name ='chvproid3' value = 'ch<?php echo $icsc ?>'>
                            <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvproid3'>
                            <input type = 'hidden' name ='txvproid3' value = 'tx<?php echo $icsc ?>'>
                            <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvproid3'>
                          </td>
                        </tr>
                        <?php
                      }?>
                      <?php
                      switch($kMysqlDb){
                        case "CEVAXXXX":
                        case "TECEVAXXXX":
                        case "DECEVAXXXX":
                          ?>
                          <tr>
                            <?php $icsc++; ?>
                            <td Class = 'name' colspan = '12'>N&uacute;mero Contenedor</td>
                            <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vitecon' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                            <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                            <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chcon' ></td>
                            <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitecon'].value = '';
                                document.forms['frnav']['chcon'].checked = false;"></td>
                            <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITECONXX AS NUMERO_CONTENEDOR" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                            <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                            <td>
                              <input type = 'hidden' name ='chvitecon' value = 'ch<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvitecon'>
                              <input type = 'hidden' name = 'txvitecon' value ='tx<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitecon'>
                            </td>
                          </tr>
                        <?php
                        break;
                      }
                      ?>
                      <?php
                      switch($kMysqlDb) {
                        case "SIACOSIA":
                        case "TESIACOSIP":
                        case "DESIACOSIP":
                          ?>
                          <tr>
                            <?php $icsc++; ?>
                            <td Class = 'name' colspan = '12'>N&uacute;mero Embarque</td>
                            <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vitenem' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                            <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                            <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chnem' ></td>
                            <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitenem'].value = '';
                                document.forms['frnav']['chnem'].checked = false;"></td>
                            <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITENEMXX AS NUMERO_EMBARQUE" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                            <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                            <td>
                              <input type = 'hidden' name ='chvitenem' value = 'ch<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvitenem'>
                              <input type = 'hidden' name = 'txvitenem' value ='tx<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitenem'>
                            </td>
                          </tr>
                            <?php $icsc++; ?>
                            <td Class = 'name' colspan = '12'>N&uacute;mero Carpeta</td>
                            <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vitenca' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                            <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                            <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chnca' ></td>
                            <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitenca'].value = '';
                                document.forms['frnav']['chnca'].checked = false;"></td>
                            <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITENCAXX AS NUMERO_CARPETA" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                            <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                            <td>
                              <input type = 'hidden' name ='chvitenca' value = 'ch<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvitenca'>
                              <input type = 'hidden' name = 'txvitenca' value ='tx<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitenca'>
                            </td>
                          </tr>
                          <tr>
                            <?php $icsc++; ?>
                            <td Class = 'name' colspan = '12'>N&uacute;mero Contenedor</td>
                            <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vitecon' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                            <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                            <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chcon' ></td>
                            <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitecon'].value = '';
                                document.forms['frnav']['chcon'].checked = false;"></td>
                            <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITECONXX AS NUMERO_CONTENEDOR" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                            <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                            <td>
                              <input type = 'hidden' name ='chvitecon' value = 'ch<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvitecon'>
                              <input type = 'hidden' name = 'txvitecon' value ='tx<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitecon'>
                            </td>
                          </tr>
                          <tr>
                            <?php $icsc++; ?>
                            <td Class = 'name' colspan = '12'>Delivery Note</td>
                            <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vitedelno' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                            <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                            <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chdelno' ></td>
                            <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitedelno'].value = '';
                                document.forms['frnav']['chdelno'].checked = false;"></td>
                            <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITEDELNO AS DELIVERY_NOTE" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                            <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                            <td>
                              <input type = 'hidden' name ='chitedelno' value = 'ch<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chitedelno'>
                              <input type = 'hidden' name = 'txvitedelno' value ='tx<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitedelno'>
                            </td>
                          </tr>
                          <?php
                        break;
                      }
                      ?>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Descripci&oacute;n Complementaria Contiene</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vitedes' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                      <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chdes' ></td>
                      <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitedes'].value = '';
                          document.forms['frnav']['chdes'].checked = false;"></td>
                      <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITEDESXX AS COMPLEMENTARIA" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvitedes' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvitedes'>
                        <input type = 'hidden' name ='txvitedes' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitedes'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Descripci&oacute;n Segun Factura</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vprodesfa' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                      <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chdsf' ></td>
                      <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vprodesfa'].value = '';
                          document.forms['frnav']['chdsf'].checked = false;"></td>
                      <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.PRODESFA AS DESCRIPCION_SEGUN_FACTURA" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvprodesfa' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvprodesfa'>
                        <input type = 'hidden' name ='txvprodesfa' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvprodesfa'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Descripci&oacute;n Inicial</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vitedesin' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                      <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chdin' ></td>
                      <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitedesin'].value = '';
                          document.forms['frnav']['chdin'].checked = false;"></td>
                      <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITEDESIN AS DESCRIPCION_INICIAL" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvitedesin' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvitedesin'>
                        <input type = 'hidden' name ='txvitedesin' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitedesin'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Descripci&oacute;n Final</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'videsfin' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                      <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chdfi' ></td>
                      <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['videsfin'].value = '';
                          document.forms['frnav']['chdfi'].checked = false;"></td>
                      <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITEDESFI AS DESCRIPCION_FINAL" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvidesfin' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvidesfin'>
                        <input type = 'hidden' name ='txvidesfin' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvidesfin'>
                      </td>
                      </tr>
                      <tr>
                        <?php
                        $icsc++;
                        //$cSqlSer = "IF(SIAI0205.ITENUEXX != 'SI',SIAI0205.ITESERXX,(SELECT GROUP_CONCAT(SIAI0221.SERIDXXX) FROM SIAI0221 WHERE SIAI0221.DOIIDXXX = SIAI0205.DOIIDXXX AND SIAI0221.DOISFIDX = SIAI0205.DOISFIDX AND SIAI0221.ADMIDXXX = SIAI0205.ADMIDXXX AND SIAI0221.ITEIDXXX = SIAI0205.ITEIDXXX AND SIAI0221.SERIDXXX != '' GROUP BY SIAI0221.DOIIDXXX, SIAI0221.DOISFIDX, SIAI0221.ADMIDXXX,SIAI0221.ITEIDXXX ORDER BY ABS(SIAI0221.SERSECXX))) AS SERIALES";
                        ?>
                        <td Class = 'name' colspan = '12'>Seriales Contienen</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'viteser' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                      <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chser' ></td>
                      <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['viteser'].value = '';
                          document.forms['frnav']['chser'].checked = false;"></td>
                      <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITESERXX AS SERIALES,SIAI0205.ITENUEXX AS ITENUEVO,SIAI0205.ITEIDXXX AS ITEMXXXX,SIAI0205.ADMIDXXX AS SUCIDXXX,SIAI0205.DOIIDXXX AS DOCIDXXX,SIAI0205.DOISFIDX AS DOSSUFXX,SIAI0205.SUBID2XX AS SUBDECXX" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chviteser' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chviteser'>
                        <input type = 'hidden' name ='txviteser' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txviteser'>
                      </td>
                      </tr>
                      <tr>
                        <?php
                        $icsc++;
                        ?>
                        <td Class = 'name' colspan = '12'>Seriales Adicionales</td>
                        <td Class = 'name' colspan = '16'><input type = 'text' Class = 'letra' name = 'viteseradi' style = 'width:320' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['viteseradi'].value = '';
                            document.forms['frnav']['chseradi'].checked = false;"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = '"SERADI" AS SERIALES_ADICIONALES,SIAI0205.ITENUEXX AS ITENUEVO,SIAI0205.ITEIDXXX AS ITEMXXXX,SIAI0205.ADMIDXXX AS SUCIDXXX,SIAI0205.DOIIDXXX AS DOCIDXXX,SIAI0205.DOISFIDX AS DOSSUFXX' onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chviteseradi' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chviteseradi'>
                          <input type = 'hidden' name ='txviteseradi' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txviteseradi'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Peso Bruto</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vlimpbr1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vlimpbr2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vlimpbr1'].value = '';
                            document.forms['frnav']['vlimpbr2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.LIMPBRXX AS PESO_BR" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlimpbr1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimpbr1'>
                          <input type = 'hidden' name ='txvlimpbr1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimpbr1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Peso Neto</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vlimpne1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vlimpne2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vlimpne1'].value = '';
                            document.forms['frnav']['vlimpne2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.LIMPNEXX AS PESO_NT" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlimpne1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimpne1'>
                          <input type = 'hidden' name ='txvlimpne1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimpne1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Cantidad Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vlimcan1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vlimcan2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vlimcan1'].value = '';
                            document.forms['frnav']['vlimcan2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITECANXX AS CANTIDAD" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlimcan1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimcan1'>
                          <input type = 'hidden' name ='txvlimcan1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimcan1'>
                        </td>
                      </tr> <?php
                      switch($kMysqlDb) {
                        case "ALMAVIVA":
                        case "TEALMAVIVA":
                        case "DEALMAVIVA": ?>
                          <tr>
                            <?php $icsc++; ?>
                            <td Class = 'name' colspan = '12'>Bultos DIM</td>
                            <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vLimBul1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                            <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vLimBul2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                            <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vLimBul1'].value = '';
                                document.forms['frnav']['vLimBul2'].value = '';"></td>
                            <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.LIMFOBXX AS BULTOS_DIM,SIAI0205.ADMIDXXX AS SUCIDXXX,SIAI0205.DOIIDXXX AS DOCIDXXX,SIAI0205.DOISFIDX AS DOSSUFXX,SIAI0205.SUBID2XX AS SUBDECXX" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                            <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                            <td>
                              <input type = 'hidden' name ='chvLimBul1' value = 'ch<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvLimBul1'>
                              <input type = 'hidden' name ='txvLimBul1' value = 'tx<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvLimBul1'>
                            </td>
                          </tr>
                          <?php
                        break;
                      }?>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Valor Unitario</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vitevun1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vitevun2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitevun1'].value = '';
                            document.forms['frnav']['vitevun2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITEVLRXX/SIAI0205.ITECANXX AS VALOR_UNITARIO" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvitevun1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvitevun1'>
                          <input type = 'hidden' name ='txvitevun1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitevun1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Cantidad DAV Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vlimcandv1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vlimcandv2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vlimcandv1'].value = '';
                            document.forms['frnav']['vlimcandv2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITECANDV AS CANTIDAD_DAV" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlimcandv1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimcandv1'>
                          <input type = 'hidden' name ='txvlimcandv1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimcandv1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Unidad DAV Contiene</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vumciddav' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                      <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'chudav' ></td>
                      <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vumciddav'].value = '';
                          document.forms['frnav']['chudav'].checked = false;"></td>
                      <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.UMCIDDAV AS UNIDAD_DAV" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvumciddav' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvumciddav'>
                        <input type = 'hidden' name ='txvumciddav' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvumciddav'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Valor Unitario DAV Contiene</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vitefodav' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                      <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'valudav' ></td>
                      <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitefodav'].value = '';
                          document.forms['frnav']['valudav'].checked = false;"></td>
                      <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITECANDV AS CANTIDAV,SIAI0205.LIMFOBXX AS VALOR_UNI_DAV" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvitefodav' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvitefodav'>
                        <input type = 'hidden' name ='txvitefodav' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitefodav'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Valor Total DAV Contiene</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vitetodav' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                      <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'valtdav' ></td>
                      <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitetodav'].value = '';
                          document.forms['frnav']['valtdav'].checked = false;"></td>
                      <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.LIMFOBXX AS VALOR_TOTAL_DAV" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvitetodav' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvitetodav'>
                        <input type = 'hidden' name ='txvitetodav' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitetodav'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Valor Unitario DAV Item</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vitefodavitem' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                      <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'valudavitem' ></td>
                      <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitefodavitem'].value = '';
                          document.forms['frnav']['valudavitem'].checked = false;"></td>
                      <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "(SIAI0205.ITEVLRXX/SIAI0205.ITECANDV) AS VALOR_UNI_DAV_ITEM" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvitefodavitem' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvitefodavitem'>
                        <input type = 'hidden' name ='txvitefodavitem' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitefodavitem'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Valor Total DAV Item</td>
                        <td Class = 'name' colspan = '12'><input type = 'text' Class = 'letra' name = 'vitetodavitem' style = 'width:240' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '3'><center>Exacta</center></td>
                      <td Class = 'name' colspan = '1'><input type = 'checkbox' name = 'valtdavitem' ></td>
                      <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vitetodavitem'].value = '';
                          document.forms['frnav']['valtdavitem'].checked = false;"></td>
                      <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.ITEVLRXX AS VALOR_TOTAL_DAV_ITEM" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvitetodavitem' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvitetodavitem'>
                        <input type = 'hidden' name ='txvitetodavitem' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvitetodavitem'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Aplica Regalias Por</td>
                        <td Class = 'letra7' colspan = '16'>
                          <select name = 'viteaplre' class="letrase" style="width:320">
                              <option value = ''></option>
                              <option value ='NA'>NO APLICA</option>
                              <option value ='PORCENTAJE'>PORCENTAJE VALOR ADUANA ITEM</option>
                              <option value = 'PORVALITEM'>PORCENTAJE VALOR ITEM</option>
                              <option value ='VALOR'>VALOR</option>
                          </select>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.viteaplre.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0205.ITEAPLRE AS APLICA_REGALIAS_POR, SIAI0205.ITEREVLR AS VALOR_REGALIAS, SIAI0205.LIMREVLR AS VALOR_LIQUIDADO_REGALIAS" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chviteaplre' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chviteaplre'>
                          <input type = 'hidden' name ='txviteaplre' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txviteaplre'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Fob (+/-) Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vfacfob1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vfacfob2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vfacfob1'].value = '';
                            document.forms['frnav']['vfacfob2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0204.FACFOBXX AS FAC_FOB, SIAI0205.ITEVLRXX AS ITEM_FOB , SIAI0205.ADMIDXXX AS SUCIDXXX,SIAI0205.DOIIDXXX AS DOCIDXXX,SIAI0205.DOISFIDX AS DOSSUFXX,SIAI0205.ITEIDXXX AS ITEIDFAC, SIAI0205.FACIDXXX AS DOCFACID " onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvfacfob1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvfacfob1'>
                          <input type = 'hidden' name ='txvfacfob1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvfacfob1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Fob Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vlimvlr1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vlimvlr2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vlimvlr1'].value = '';
                            document.forms['frnav']['vlimvlr2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.LIMFOBXX AS FOB,SIAI0205.ITEIDXXX AS ITEMXXXX,SIAI0206.LIMVLRXX AS FOBDECXX,SIAI0205.ADMIDXXX AS SUCIDXXX,SIAI0205.DOIIDXXX AS DOCIDXXX,SIAI0205.DOISFIDX AS DOSSUFXX,SIAI0205.SUBID2XX AS SUBDECXX" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlimvlr1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimvlr1'>
                          <input type = 'hidden' name ='txvlimvlr1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimvlr1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Flete Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vlimfle1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vlimfle2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vlimfle1'].value = '';
                            document.forms['frnav']['vlimfle2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.LIMFLEXX AS FLETE,SIAI0205.ITEIDXXX AS ITEMXXXX,SIAI0206.LIMFLEXX AS FLEDECXX,SIAI0205.ADMIDXXX AS SUCIDXXX,SIAI0205.DOIIDXXX AS DOCIDXXX,SIAI0205.DOISFIDX AS DOSSUFXX,SIAI0205.SUBID2XX AS SUBDECXX" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlimfle1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimfle1'>
                          <input type = 'hidden' name ='txvlimfle1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimfle1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Seguro Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vlimseg1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vlimseg2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vlimseg1'].value = '';
                            document.forms['frnav']['vlimseg2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.LIMSEGXX AS SEGURO,SIAI0205.ITEIDXXX AS ITEMXXXX,SIAI0206.LIMSEGXX AS SEGDECXX,SIAI0205.ADMIDXXX AS SUCIDXXX,SIAI0205.DOIIDXXX AS DOCIDXXX,SIAI0205.DOISFIDX AS DOSSUFXX,SIAI0205.SUBID2XX AS SUBDECXX" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlimseg1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimseg1'>
                          <input type = 'hidden' name ='txvlimseg1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimseg1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Conexo Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vlimcon1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vlimcon2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vlimcon1'].value = '';
                            document.forms['frnav']['vlimcon2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.LIMCONXX AS CONEXOS" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlimcon1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimcon1'>
                          <input type = 'hidden' name ='txvlimcon1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimcon1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Varios Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vlimvar1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vlimvar2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vlimvar1'].value = '';
                            document.forms['frnav']['vlimvar2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.LIMVARXX AS VARIOS" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlimvar1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimvar1'>
                          <input type = 'hidden' name ='txvlimvar1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimvar1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Otros Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vlimotr1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vlimotr2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vlimotr1'].value = '';
                            document.forms['frnav']['vlimotr2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.LIMVARXX AS OTROS" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlimotr1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimotr1'>
                          <input type = 'hidden' name ='txvlimotr1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimotr1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Sumatoria Fletes, Seg y Otr</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vseflot1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vseflot2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vseflot1'].value = '';
                            document.forms['frnav']['vseflot2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.LIMVARXX+SIAI0205.LIMCONXX AS SUMA_FL_SG_OT,SIAI0205.SUBID2XX AS SUBDECXX,SIAI0205.ITEIDXXX AS ITEMXXXX,SIAI0205.LIMFLEXX AS FLEITXXX,SIAI0206.LIMFLEXX AS FLEDECXX,SIAI0205.LIMSEGXX AS SEGITXXX,SIAI0206.LIMSEGXX AS SEGDECXX,SIAI0205.ADMIDXXX AS SUCIDXXX,SIAI0205.DOIIDXXX AS DOCIDXXX,SIAI0205.DOISFIDX AS DOSSUFXX" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvseflot1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvseflot1'>
                          <input type = 'hidden' name ='txvseflot1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvseflot1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Valor CIF COP(Fob, Fte y Seg)</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vcifcop1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vcifcop2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vcifcop1'].value = '';
                            document.forms['frnav']['vcifcop2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "(SIAI0205.LIMFLEXX+SIAI0205.LIMSEGXX+SIAI0205.LIMFOBXX+SIAI0205.LIMVARXX+SIAI0205.LIMCONXX)*SIAI0206.DGETRMXX AS CIF_COP,SIAI0205.ITEIDXXX AS ITEMXXXX,SIAI0206.LIMCIFXX AS CIFDECXX,SIAI0205.ADMIDXXX AS SUCIDXXX,SIAI0205.DOIIDXXX AS DOCIDXXX,SIAI0205.DOISFIDX AS DOSSUFXX,SIAI0205.SUBID2XX AS SUBDECXX,SIAI0206.DGETRMXX AS TASADECX" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvcifcop1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvcifcop1'>
                          <input type = 'hidden' name ='txvcifcop1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvcifcop1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Ajuste Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vlimaju1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vlimaju2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vlimaju1'].value = '';
                            document.forms['frnav']['vlimaju2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.LIMAJUXX AS AJUSTES" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlimaju1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimaju1'>
                          <input type = 'hidden' name ='txvlimaju1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimaju1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Valor en Aduana Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vlimnet1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vlimnet2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vlimnet1'].value = '';
                            document.forms['frnav']['vlimnet2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0205.LIMFOBXX+SIAI0205.LIMFLEXX+SIAI0205.LIMSEGXX+SIAI0205.LIMCONXX+SIAI0205.LIMVARXX+SIAI0205.LIMAJUXX-SIAI0205.ITEDEDXX AS VALOR_ADUANA" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlimnet1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimnet1'>
                          <input type = 'hidden' name ='txvlimnet1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimnet1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Base Arancel Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vlimcif1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vlimcif2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vlimcif1'].value = '';
                            document.forms['frnav']['vlimcif2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "((SIAI0205.LIMFOBXX+SIAI0205.LIMFLEXX+SIAI0205.LIMSEGXX+SIAI0205.LIMCONXX+SIAI0205.LIMVARXX+SIAI0205.LIMAJUXX)-SIAI0205.ITEDEDXX)*SIAI0206.DGETRMXX AS BASE_ARANCEL, SIAI0205.ITEIDXXX AS ITEMXXXX,SIAI0206.LIMCIFXX AS ARANCEL_COP,SIAI0206.DGETRMXX AS TASA,SIAI0205.ADMIDXXX AS SUCIDXXX,SIAI0205.DOIIDXXX AS DOCIDXXX,SIAI0205.DOISFIDX AS DOSSUFXX,SIAI0205.SUBID2XX AS SUBDECXX " onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlimcif1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimcif1'>
                          <input type = 'hidden' name ='txvlimcif1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimcif1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Total Liqui. Pesos Arancel Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vlimgra11' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vlimgra12' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vlimgra11'].value = '';
                            document.forms['frnav']['vlimgra12'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "ROUND(((((SIAI0205.LIMFOBXX+SIAI0205.LIMFLEXX+SIAI0205.LIMSEGXX+SIAI0205.LIMCONXX+SIAI0205.LIMVARXX+SIAI0205.LIMAJUXX)-SIAI0205.ITEDEDXX)*SIAI0206.DGETRMXX)*SIAI0206.ARCPORXX/100)) AS LIQ_ARANCEL_PESOS,SIAI0205.ITEIDXXX AS ITEMXXXX,SIAI0206.LIMGRAXX AS ARANCEL_LIQ,SIAI0206.DGETRMXX AS TASA,SIAI0206.ARCPORXX AS POR_ARANCEL, SIAI0205.ADMIDXXX AS SUCIDXXX,SIAI0205.DOIIDXXX AS DOCIDXXX,SIAI0205.DOISFIDX AS DOSSUFXX,SIAI0205.SUBID2XX AS SUBDECXX" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlimgra11' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimgra11'>
                          <input type = 'hidden' name ='txvlimgra11' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimgra11'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Base I.V.A Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vlimiva1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vlimiva2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vlimiva1'].value = '';
                            document.forms['frnav']['vlimiva2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "(((SIAI0205.LIMFOBXX+SIAI0205.LIMFLEXX+SIAI0205.LIMSEGXX+SIAI0205.LIMCONXX+SIAI0205.LIMVARXX+SIAI0205.LIMAJUXX)-SIAI0205.ITEDEDXX)*SIAI0206.DGETRMXX)+((((SIAI0205.LIMFOBXX+SIAI0205.LIMFLEXX+SIAI0205.LIMSEGXX+SIAI0205.LIMCONXX+SIAI0205.LIMVARXX+SIAI0205.LIMAJUXX)-SIAI0205.ITEDEDXX)*SIAI0206.DGETRMXX)*SIAI0206.ARCPORXX/100) AS BASE_IVA,SIAI0205.ITEIDXXX AS ITEMXXXX,SIAI0206.LIMIVAXX AS IVA_BASE, SIAI0206.ARCPORXX AS POR_IVA, SIAI0205.ADMIDXXX AS SUCIDXXX,SIAI0205.DOIIDXXX AS DOCIDXXX,SIAI0205.DOISFIDX AS DOSSUFXX,SIAI0205.SUBID2XX AS SUBDECXX " onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlimiva1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimiva1'>
                          <input type = 'hidden' name ='txvlimiva1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimiva1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Total Liqui. Pesos IVA Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vlimsubt11' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vlimsubt12' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vlimsubt11'].value = '';
                            document.forms['frnav']['vlimsubt12'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "ROUND((((((SIAI0205.LIMFOBXX+SIAI0205.LIMFLEXX+SIAI0205.LIMSEGXX+SIAI0205.LIMCONXX+SIAI0205.LIMVARXX+SIAI0205.LIMAJUXX)-SIAI0205.ITEDEDXX)*SIAI0206.DGETRMXX)+((((SIAI0205.LIMFOBXX+SIAI0205.LIMFLEXX+SIAI0205.LIMSEGXX+SIAI0205.LIMCONXX+SIAI0205.LIMVARXX+SIAI0205.LIMAJUXX)-SIAI0205.ITEDEDXX)*SIAI0206.DGETRMXX)*SIAI0206.ARCPORXX/100))*SIAI0206.ARCIVAXX/100)) AS LIQ_IVA_PESOS,SIAI0205.ITEIDXXX AS ITEMXXXX,SIAI0206.LIMSUBTX AS TOT_LIQ_IVA,SIAI0206.ARCIVAXX PORCE_IVA, SIAI0205.ADMIDXXX AS SUCIDXXX,SIAI0205.DOIIDXXX AS DOCIDXXX,SIAI0205.DOISFIDX AS DOSSUFXX,SIAI0205.SUBID2XX " onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlimsubt11' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimsubt11'>
                          <input type = 'hidden' name ='txvlimsubt11' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimsubt11'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Arancel  Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'varcpor1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'varcpor2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['varcpor1'].value = '';
                            document.forms['frnav']['varcpor2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0206.ARCPORXX AS ARANCEL" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvarcpor1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvarcpor1'>
                          <input type = 'hidden' name ='txvarcpor1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvarcpor1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>I.V.A.  Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'varciva1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'varciva2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['varciva1'].value = '';
                            document.forms['frnav']['varciva2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0206.ARCIVAXX AS IVA" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvarciva1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvarciva1'>
                          <input type = 'hidden' name ='txvarciva1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvarciva1'>
                        </td>
                      </tr>
                      <tr>
                      <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Rescate Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'varcres1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'varcres2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['varcres1'].value = '';document.forms['frnav']['varcres2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "((((SIAI0205.LIMFOBXX+SIAI0205.LIMFLEXX+SIAI0205.LIMSEGXX+SIAI0205.LIMCONXX+SIAI0205.LIMVARXX+SIAI0205.LIMAJUXX-SIAI0205.ITEDEDXX)*(ROUND(SIAI0206.SUBRESPO,2)))/100) * SIAI0206.DGETRMXX) AS RESCATE, SIAI0205.ITEIDXXX AS ITEMXXXX, (((SIAI0206.LIMNETXX*SIAI0206.SUBRESPO)/100) * SIAI0206.DGETRMXX) AS ADUANA, SIAI0205.ADMIDXXX AS SUCIDXXX, SIAI0205.DOIIDXXX AS DOCIDXXX, SIAI0205.DOISFIDX AS DOSSUFXX, SIAI0205.SUBID2XX AS SUBDECXX" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvarcres1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvarcres1'>
                          <input type = 'hidden' name ='txvarcres1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvarcres1'>
                        </td>
                      </tr>
                      </tr>
                        <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>% Rescate</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'varcpre1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'varcpre2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['varcpre1'].value = '';document.forms['frnav']['varcpre2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "ROUND(SIAI0206.SUBRESPO,2) AS PORCENTAJE_RESCATE" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvarcpre1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvarcpre1'>
                          <input type = 'hidden' name ='txvarcpre1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvarcpre1'>
                        </td>
                      </tr>
                      <tr>
                        <td><input type="hidden" name = 'Cliente' value = '<?php echo $Cliente ?>'></td>
                        <td><input type="hidden" name = 'TipCli' value = '<?php echo $TipCli ?>'></td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <?php if ($cliente == 'X') { ?>
                          <td Class = 'letra7' colspan = '12'><a href = '#' id="idcli">Importador</a></td>
                          <td Class = 'letra7' colspan = '4'><input type = 'text' Class = 'letra' name = 'vcliid' value = '<?php echo $vcli ?>' style = 'width:80' readonly></td>
                          <td Class = 'letra7' colspan = '12'><input type = 'text' Class = 'letra' name = 'vclinom' value = '<?php echo $vclin ?>'  style = 'width:240' readonly></td>
                          <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:alert('No Permitido');"></td>
                          <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.CLIIDXXX AS NIT,SIAI0206.CLINOMXX AS IMPORTADOR" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                          <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                          <td>
                            <input type = 'hidden' name ='chvcliid' value = 'ch<?php echo $icsc ?>'>
                            <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvcliid'>
                            <input type = 'hidden' name ='txvcliid' value = 'tx<?php echo $icsc ?>'>
                            <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvcliid'>
                          </td>
                        <?php } else { ?>
                          <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vcliid','WINDOW',-1)">Importador</a></td>
                          <td Class = 'name' colspan = '4'>
                            <input type = "text" Class = "letra" name = "vcliid" style = "width:80"
                              onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                    uLinks('vcliid', 'VALID');
                                                    this.style.background = '#FFFFFF'"
                              onFocus="javascript:this.style.background = '#00FFFF'">
                          </td>
                          <td Class = 'name' colspan = '12'><a href="#"></a>
                            <input type = "text" Class = "letra" name = "vclinom" style = "width:240" readonly>
                          </td>
                          <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vcliid.value = '';
                              document.frnav.vclinom.value = '';"></td>
                          <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.CLIIDXXX AS NIT,SIAI0206.CLINOMXX AS IMPORTADOR" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                          <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                          <td>
                            <input type = 'hidden' name ='chvcliid' value = 'ch<?php echo $icsc ?>'>
                            <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvcliid'>
                            <input type = 'hidden' name ='txvcliid' value = 'tx<?php echo $icsc ?>'>
                            <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvcliid'>
                          </td>
                        <?php }
                        ?>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vtdeid','WINDOW',-1)">Tipo Declaraci&oacute;n</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vtdeid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vtdeid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vtdedes" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vtdeid.value = '';
                            document.frnav.vtdedes.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.TDEIDXXX AS COD_TIPO_DECL,0 AS TIPO_DECL" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvtdeid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvtdeid'>
                          <input type = 'hidden' name ='txvtdeid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvtdeid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>No. Declaraci&oacute;n Anterior</td>
                        <td Class = 'letra7' colspan = '4'><input type = 'text' Class = 'letra' name = 'vlimstkan' style = 'width:80' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '1'><a href='javascript:show_calendar("frnav.vlimfstka1")'>Del</a></td>
                        <td Class = 'letra7' colspan = '3'><input type = 'text' Class = 'letra' name = 'vlimfstka1' style = 'width:60' onBlur = 'javascript:chDate(this)'></td>
                        <td Class = 'letra7' colspan = '1'><center><a href='javascript:show_calendar("frnav.vlimfstka2")'>Al</a></center></td>
                      <td Class = 'letra7' colspan = '3'><input type = 'text' Class = 'letra' name = 'vlimfstka2'  style = 'width:60' onBlur = 'javascript:chDate(this)'></td>
                      <td Class = 'letra7' colspan = '4'>
                        <select name = 'vodiid3' class="letrase" style="width:80">
                          <option></option>
                          <?php for ($od = 0; $od < count($aOdi); $od++) { ?>
                            <option value="<?php echo $aOdi[$od]['ODIIDXXX'] ?>"><?php echo $aOdi[$od]['ODIDESXX'] ?></option>
                          <?php }
                          ?>
                        </select>
                      </td>
                      <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vlimfstka1.value = '';
                          document.frnav.vlimfstka2.value = '';
                          document.frnav.vlimstkan.value = '';
                          document.frnav.vodiid3.value = '';"></td>
                      <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.LIMSTKAN AS DEC_ANTERIOR,SIAI0206.LIMFSTKA AS FECHA_DEC_ANT" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvlimstkan' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimstkan'>
                        <input type = 'hidden' name ='txvlimstkan' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimstkan'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>No. Exportaci&oacute;n Anterior</td>
                        <td Class = 'letra7' colspan = '4'><input type = 'text' Class = 'letra' name = 'vlimexp' style = 'width:80' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '1'><a href='javascript:show_calendar("frnav.vlimfexp1")'>Del</a></td>
                        <td Class = 'letra7' colspan = '3'><input type = 'text' Class = 'letra' name = 'vlimfexp1' style = 'width:60' onBlur = 'javascript:chDate(this)'></td>
                        <td Class = 'letra7' colspan = '1'><center><a href='javascript:show_calendar("frnav.vlimfexp2")'>Al</a></center></td>
                      <td Class = 'letra7' colspan = '3'><input type = 'text' Class = 'letra' name = 'vlimfexp2'  style = 'width:60' onBlur = 'javascript:chDate(this)'></td>
                      <td Class = 'letra7' colspan = '4'>
                        <select name = 'vodiid4' class="letrase" style="width:80">
                          <option></option>
                          <?php for ($od = 0; $od < count($aOdi); $od++) { ?>
                            <option value="<?php echo $aOdi[$od]['ODIIDXXX'] ?>"><?php echo $aOdi[$od]['ODIDESXX'] ?></option>
                          <?php }
                          ?>
                        </select>
                      </td>
                      <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vlimfexp1.value = '';
                          document.frnav.vlimfexp2.value = '';
                          document.frnav.vlimexp.value = '';
                          document.frnav.vodiid4.value = '';"></td>
                      <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.LIMEXPXX AS EXPORTACION,SIAI0206.LIMFEXPX AS FECHA_EXPORTACION" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvlimexp' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimexp'>
                        <input type = 'hidden' name ='txvlimexp' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimexp'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vlinid','WINDOW',-1)">Lugar de Ingreso</a>
                        </td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vlinid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vlinid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vlindes" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vlinid.value = '';
                            document.frnav.vlindes.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.LINIDXXX AS LUGAR_INGR" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlinid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlinid'>
                          <input type = 'hidden' name ='txvlinid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlinid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vdaaid','WINDOW',-1)">Dep&oacute;sito</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vdaaid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vdaaid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vdaades" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vdaaid.value = '';
                            document.frnav.vdaades.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.DAAIDXXX AS DEPOSITO" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvdaaid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvdaaid'>
                          <input type = 'hidden' name ='txvdaaid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvdaaid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'>Manifiesto</td>
                        <td Class = 'letra7' colspan = '4'><input type = 'text' Class = 'letra' name = 'vdgemc' style = 'width:80' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '1'><a href='javascript:show_calendar("frnav.vdgefmc1")'>Del</a></td>
                        <td Class = 'letra7' colspan = '5'><input type = 'text' Class = 'letra' name = 'vdgefmc1' style = 'width:100' onBlur = 'javascript:chDate(this)'></td>
                        <td Class = 'letra7' colspan = '1'><center><a href='javascript:show_calendar("frnav.vdgefmc2")'>Al</a></center></td>
                      <td Class = 'letra7' colspan = '5'><input type = 'text' Class = 'letra' name = 'vdgefmc2'  style = 'width:100' onBlur = 'javascript:chDate(this)'></td>
                      <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vdgemc.value = '';
                          document.frnav.vdgefmc1.value = '';
                          document.frnav.vdgefmc2.value = '';"></td>
                      <?php
                      switch($cAlfa){
                        case 'GRUMALCO':
                        case 'DEGRUMALCO':
                        case 'TEGRUMALCO': ?>
                          <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.DGEMCXXX AS MANIFIESTO,SIAI0206.DGEFMCXX AS FECHA_MANIF,SIAI0200.DGEHMCXX AS HORA_MANIF,SIAI0200.DGEMCXXX AS MANIFIESTO_GENERALES,SIAI0200.DGEFMCXX AS FECHA_MANIF_GENERALES" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                          <?php
                        break;
                        default: ?>
                          <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.DGEMCXXX AS MANIFIESTO,SIAI0206.DGEFMCXX AS FECHA_MANIF,SIAI0200.DGEHMCXX AS HORA_MANIF" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                          <?php
                        break;
                      }?>
                      <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvdgemc' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvdgemc'>
                        <input type = 'hidden' name ='txvdgemc' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvdgemc'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'>Documento de Transporte</td>
                        <td Class = 'letra7' colspan = '4'><input type = 'text' Class = 'letra' name = 'vdgedt' style = 'width:80'></td>
                        <td Class = 'letra7' colspan = '1'><a href='javascript:show_calendar("frnav.vdgefdt1")'>Del</a></td>
                        <td Class = 'letra7' colspan = '5'><input type = 'text' Class = 'letra' name = 'vdgefdt1' style = 'width:100' onBlur = 'javascript:chDate(this)'></td>
                        <td Class = 'letra7' colspan = '1'><center><a href='javascript:show_calendar("frnav.vdgefdt2")'>Al</a></center></td>
                      <td Class = 'letra7' colspan = '5'><input type = 'text' Class = 'letra' name = 'vdgefdt2'  style = 'width:100' onBlur = 'javascript:chDate(this)'></td>
                      <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vdgedt.value = '';
                          document.frnav.vdgefdt1.value = '';
                          document.frnav.vdgefdt2.value = '';"></td>
                      <?php
                      switch($cAlfa){
                        case 'GRUMALCO':
                        case 'DEGRUMALCO':
                        case 'TEGRUMALCO': ?>
                          <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.DGEDTXXX AS GUIA,SIAI0206.DGEFDTXX AS FECHA_GUIA,SIAI0200.DGEDTXXX AS GUIA_GENERALES,SIAI0200.DGEFDTXX AS FECHA_GUIA_GENERALES" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                          <?php
                        break;
                        case 'CORALVIS':
                        case 'DECORALVIS':
                        case 'TECORALVIS': ?>
                          <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.DGEDTXXX AS DOCUMENTO_TRANSPORTE,SIAI0206.DGEFDTXX AS FECHA_DOCUMENTO_TRANSPORTE" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                          <?php
                        break;
                        default:?>
                          <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.DGEDTXXX AS GUIA,SIAI0206.DGEFDTXX AS FECHA_GUIA" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                          <?php
                        break;
                      }?>
                      <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvdgedt' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvdgedt'>
                        <input type = 'hidden' name ='txvdgedt' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvdgedt'>
                      </td>
                      </tr>
                      <?php
                      switch ($kMysqlDb) {
                        case "CEVAXXXX":
                        case "DECEVAXXXX":
                        case "TECEVAXXXX":
                        case "DEDESARROL":
                        case "TEPRUEBASX": ?>
                          <tr>
                            <?php $icsc++; ?>
                            <td Class = 'letra7' colspan = '12'>L&iacute;nea de Producto</td>
                            <td Class = 'letra7' colspan = '20'></td>
                            <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="'' AS CODIGO_DE_LINEA,'' AS LINEA_DE_PRODUCTO,SIAI0205.DOIIDXXX AS LPRDOIID,SIAI0205.DOISFIDX AS LPRDOISF,SIAI0205.ADMIDXXX AS LPRADMID" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                            <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                            <td>
                              <input type = 'hidden' name ='chvlprid3' value = 'ch<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlprid3'>
                              <input type = 'hidden' name ='txvlprid3' value = 'tx<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlprid3'>
                            </td>
                          </tr><?php
                        break;
                      }
                      ?>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vpieid','WINDOW',-1)">Proveedor</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vpieid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vpieid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '11'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vpienom" style = "width:220" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '1'><input type = 'text' Class = 'letra' name = 'vpiepai' style = 'width:20' readonly></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vpieid.value = '';
                            document.frnav.vpienom.value = '';
                            document.frnav.vpiepai.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.PIENOMXX AS PROVEDEDOR" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvpieid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvpieid'>
                          <input type = 'hidden' name ='txvpieid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvpieid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'>Ciudad Proveedor</td>
                        <td Class = 'letra7' colspan = '16'><input type = 'text' Class = 'letra' name = 'vpieciu' style = 'width:320' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vpieciu.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.PIECIUXX AS CIUDAD_PROVEDEDOR" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvpieciu' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvpieciu'>
                          <input type = 'hidden' name ='txvpieciu' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvpieciu'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'>Direccion Proveedor</td>
                        <td Class = 'letra7' colspan = '16'><input type = 'text' Class = 'letra' name = 'vpiedir' style = 'width:320' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vpiedir.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.PIEDIRXX AS DIRECCION_PROVEDEDOR" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvpiedir' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvpiedir'>
                          <input type = 'hidden' name ='txvpiedir' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvpiedir'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'>E-Mail Proveedor</td>
                        <td Class = 'letra7' colspan = '16'><input type = 'text' Class = 'letra' name = 'vpieema' style = 'width:320' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vpieema.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.PIEEMAXX AS E_MAIL_PROVEDEDOR" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvpieema' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvpieema'>
                          <input type = 'hidden' name ='txvpieema' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvpieema'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vpaiid','WINDOW',-1)">Pa&iacute;s Procedencia</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vpaiid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vpaiid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vpaides" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vpaiid.value = '';
                            document.frnav.vpaides.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.PAIIDXXX AS PAIS_PROCE" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvpaiid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvpaiid'>
                          <input type = 'hidden' name ='txvpaiid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvpaiid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vtraid','WINDOW',-1)">Empresa Transportadora</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vtraid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vtraid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vtrades" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vtraid.value = '';
                            document.frnav.vtrades.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.TRAIDXXX AS TRANSPORTADOR" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvtraid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvtraid'>
                          <input type = 'hidden' name ='txvtraid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvtraid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vpaibanid','WINDOW',-1)">Bandera</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vpaibanid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vpaibanid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vpaibandes" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vpaibanid.value = '';
                            document.frnav.vpaibandes.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.BANIDXXX AS BANDERA" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvpaibanid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvpaibanid'>
                          <input type = 'hidden' name ='txvpaibanid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvpaibanid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vmonid','WINDOW',-1)">Moneda de Negociaci&oacute;n</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vmonid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vmonid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vmondes" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vmonid.value = '';
                            document.frnav.vmondes.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.MONIDXXX AS MONEDA_NEG" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvmonid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvmonid'>
                          <input type = 'hidden' name ='txvmonid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvmonid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vmonidsg','WINDOW',-1)">Moneda de Seguro</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vmonidsg" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vmonidsg', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vmondessg" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vmonidsg.value = '';
                            document.frnav.vmondessg.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.MONIDSGX AS MONEDA_SEG" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvmonidsg' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvmonidsg'>
                          <input type = 'hidden' name ='txvmonidsg' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvmonidsg'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vodiid','WINDOW',-1)">Oficina Dian</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vodiid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vodiid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vodides" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vodiid.value = '';
                            document.frnav.vodides.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.ODIIDXXX AS OFICINA" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvodiid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvodiid'>
                          <input type = 'hidden' name ='txvodiid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvodiid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vmtrid','WINDOW',-1)">Medio de Transporte</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vmtrid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vmtrid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vmtrdes" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vmtrid.value = '';
                            document.frnav.vmtrdes.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.MTRIDXXX AS MEDIO_TRANS" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvmtrid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvmtrid'>
                          <input type = 'hidden' name ='txvmtrid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvmtrid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vdepid','WINDOW',-1)">Departamento Destino</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vdepid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vdepid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vdepdes" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vdepid.value = '';
                            document.frnav.vdepdes.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.DEPIDXXX AS DEPTO" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvdepid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvdepid'>
                          <input type = 'hidden' name ='txvdepid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvdepid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'><a href = 'javascript:unid(0)'>Tasa Cambio</a></td>
                        <td Class = 'letra7' colspan = '6'><input type = 'text' Class = 'letra' name = 'vtcafec1' style = 'width:120' readonly></td>
                        <td Class = 'letra7' colspan = '6'><input type = 'text' Class = 'letra' name = 'vtcat1' style = 'width:120' readonly></td>
                        <td Class = 'letra7' colspan = '4'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vtcafec1.value = '';
                            document.frnav.vtcat1.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.DGETRMXX AS TASA" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvtcafec1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvtcafec1'>
                          <input type = 'hidden' name ='txvtcafec1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvtcafec1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vfpiid','WINDOW',-1)">Forma de Pago</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vfpiid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vfpiid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vfpides" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vfpiid.value = '';
                            document.frnav.vfpides.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.FPIIDXXX AS FORMA_PAG" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvfpiid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvfpiid'>
                          <input type = 'hidden' name ='txvfpiid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvfpiid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vtimid','WINDOW',-1)">Tipo Importaci&oacute;n</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vtimid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vtimid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vtimdes" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vtimid.value = '';
                            document.frnav.vtimdes.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.TIMIDXXX AS TIPO_IMPORTACION" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvtimid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvtimid'>
                          <input type = 'hidden' name ='txvtimid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvtimid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vpaiid3','WINDOW',-1)">Pa&iacute;s de Compra</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vpaiid3" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vpaiid3', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vpaides3" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vpaiid3.value = '';
                            document.frnav.vpaides3.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.SUBPAII2 AS PAIS_COMPRA" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvpaiid3' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvpaiid3'>
                          <input type = 'hidden' name ='txvpaiid3' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvpaiid3'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('varcid','WINDOW',-1)">Subpartida</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "varcid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('varcid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'letra7' colspan = '12'><input type = 'text' Class = 'letra' name = 'varcdes' style = 'width:240' readonly></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.varcid.value = '';
                            document.frnav.varcdes.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.ARCIDXXX AS SUBPARTIDA" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvarcid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvarcid'>
                          <input type = 'hidden' name ='txvarcid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvarcid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'><a href = 'javascript:unid(18)'>Codigo Complementario</a></td>
                        <td Class = 'letra7' colspan = '3'><input type = 'text' Class = 'letra' name = 'varccom' style = 'width:60' maxlength="4"></td>
                        <td Class = 'letra7' colspan = '13'>&nbsp</td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.varccom.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.ARCCOMXX AS COMPLEMENTARIO" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvarccom' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvarccom'>
                          <input type = 'hidden' name ='txvarccom' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvarccom'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'><a href = 'javascript:unid(18)'>Codigo Suplementario</a></td>
                        <td Class = 'letra7' colspan = '3'><input type = 'text' Class = 'letra' name = 'varcsup' style = 'width:60' maxlength="4"></td>
                        <td Class = 'letra7' colspan = '13'>&nbsp</td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.varcsup.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.ARCSUPXX AS SUPLEMENTARIO" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvarcsup' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvarcsup'>
                          <input type = 'hidden' name ='txvarcsup' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvarcsup'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vmodid','WINDOW',-1)">Modalidad</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vmodid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vmodid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'letra7' colspan = '12'><input type = 'text' Class = 'letra' name = 'vmoddes' style = 'width:240' readonly></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vmodid.value = '';
                            document.frnav.vmoddes.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.MODIDXXX AS MODALIDAD" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvmodid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvmodid'>
                          <input type = 'hidden' name ='txvmodid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvmodid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Cuotas Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vsubcuo1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vsubcuo2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vsubcuo1'].value = '';
                            document.forms['frnav']['vsubcuo2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0206.SUBCUOXX AS CUOTAS" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvsubcuo1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvsubcuo1'>
                          <input type = 'hidden' name ='txvsubcuo1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvsubcuo1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Valor Cuota (USD) Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vsubcuovl1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vsubcuovl2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vsubcuovl1'].value = '';
                            document.forms['frnav']['vsubcuovl2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0206.SUBCUOVL AS VALOR_CUOTA" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvsubcuovl1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvsubcuovl1'>
                          <input type = 'hidden' name ='txvsubcuovl1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvsubcuovl1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Periodicidad Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vsubper1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vsubper2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vsubper1'].value = '';
                            document.forms['frnav']['vsubper2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0206.SUBPERXX AS PERIODICIDAD" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvsubper1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvsubper1'>
                          <input type = 'hidden' name ='txvsubper1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvsubper1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vsubpaiid','WINDOW',-1)">Pa&iacute;s de Origen</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vsubpaiid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vsubpaiid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vsubpaides" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vsubpaiid.value = '';
                            document.frnav.vsubpaides.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.SUBPAIID AS PAIS_ORIGEN" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvsubpaiid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvsubpaiid'>
                          <input type = 'hidden' name ='txvsubpaiid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvsubpaiid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vpaiite','WINDOW',-1)">Pa&iacute;s de Origen Item</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vpaiite" style = "width:80"
                                 onBlur = "javascript:this.value = this.value.toUpperCase();
                                     uLinks('vpaiite', 'VALID');
                                     this.style.background = '#FFFFFF'"
                                 onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vpaiitedes" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vpaiite.value = '';
                            document.frnav.vpaiitedes.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0205.ITEPAIID AS PAIS_ITEM" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvpaiite' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvpaiite'>
                          <input type = 'hidden' name ='txvpaiite' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvpaiite'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vaceid','WINDOW',-1)">Acuerdo</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vaceid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vaceid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vacedes" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vaceid.value = '';
                            document.frnav.vacedes.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.ACEIDXXX AS ACUERDO" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvaceid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvaceid'>
                          <input type = 'hidden' name ='txvaceid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvaceid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>No. Aceptacion</td>
                        <td Class = 'letra7' colspan = '4'><input type = 'text' Class = 'letra' name = 'vlimace' style = 'width:80' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '1'><a href='javascript:show_calendar("frnav.vlimface1")'>Del</a></td>
                        <td Class = 'letra7' colspan = '5'><input type = 'text' Class = 'letra' name = 'vlimface1' style = 'width:100' onBlur = 'javascript:chDate(this)'></td>
                        <td Class = 'letra7' colspan = '1'><center><a href='javascript:show_calendar("frnav.vlimface2")'>Al</a></center></td>
                      <td Class = 'letra7' colspan = '5'><input type = 'text' Class = 'letra' name = 'vlimface2'  style = 'width:100' onBlur = 'javascript:chDate(this)'></td>
                      <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vlimface1.value = '';
                          document.frnav.vlimface2.value = '';
                          document.frnav.vlimace.value = '';"></td>
                          <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.LIMACEXX AS ACEPTACION, SIAI0206.LIMACEXX AS NUMERO_FORMULARIO, SIAI0206.LIMFACEX AS FECHA_ACEP" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvlimace' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimace'>
                        <input type = 'hidden' name ='txvlimace' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimace'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>No. Autoadhesivo</td>
                        <td Class = 'letra7' colspan = '4'><input type = 'text' Class = 'letra' name = 'vlimstk' style = 'width:80' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '1'><a href='javascript:show_calendar("frnav.vlimfstk1")'>Del</a></td>
                        <td Class = 'letra7' colspan = '5'><input type = 'text' Class = 'letra' name = 'vlimfstk1' style = 'width:100' onBlur = 'javascript:chDate(this)'></td>
                        <td Class = 'letra7' colspan = '1'><center><a href='javascript:show_calendar("frnav.vlimfstk2")'>Al</a></center></td>
                      <td Class = 'letra7' colspan = '5'><input type = 'text' Class = 'letra' name = 'vlimfstk2'  style = 'width:100' onBlur = 'javascript:chDate(this)'></td>
                      <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vlimfstk1.value = '';
                          document.frnav.vlimfstk2.value = '';
                          document.frnav.vlimstk.value = '';"></td>
                      <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.LIMSTKXX AS AUTOADHESIVO,SIAI0206.LIMFSTKX AS FECHA_AUTOAD" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvlimstk' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimstk'>
                        <input type = 'hidden' name ='txvlimstk' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimstk'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>No. Levante</td>
                        <td Class = 'letra7' colspan = '4'><input type = 'text' Class = 'letra' name = 'vlimlev' style = 'width:80' onBlur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'name' colspan = '1'><a href='javascript:show_calendar("frnav.vlimflev1")'>Del</a></td>
                        <td Class = 'letra7' colspan = '5'><input type = 'text' Class = 'letra' name = 'vlimflev1' style = 'width:100' onBlur = 'javascript:chDate(this)'></td>
                        <td Class = 'letra7' colspan = '1'><center><a href='javascript:show_calendar("frnav.vlimflev2")'>Al</a></center></td>
                      <td Class = 'letra7' colspan = '5'><input type = 'text' Class = 'letra' name = 'vlimflev2'  style = 'width:100' onBlur = 'javascript:chDate(this)'></td>
                      <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vlimflev1.value = '';
                          document.frnav.vlimflev2.value = '';
                          document.frnav.vlimlev.value = '';"></td>
                      <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.LIMLEVXX AS LEVANTE,SIAI0206.LIMFLEVX AS FECHA_LEV" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvlimlev' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimlev'>
                        <input type = 'hidden' name ='txvlimlev' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimlev'>
                      </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vtemid','WINDOW',-1)">Tipo de Embalaje</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vtemid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vtemid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vtemdes" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vtemid.value = '';
                            document.frnav.vtemdes.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.TEMIDXXX AS TIPO_EMB" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvtemid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvtemid'>
                          <input type = 'hidden' name ='txvtemid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvtemid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vumcid','WINDOW',-1)">Unidad Comercial</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vumcid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vumcid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vumcdes" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vumcid.value = '';
                            document.frnav.vumcdes.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.UMCIDXXX AS UMC" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvumcid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvumcid'>
                          <input type = 'hidden' name ='txvumcid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvumcid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'><a href = "javascript:uLinks('vtriid','WINDOW',-1)">Cod. Registro / Licencia</a></td>
                        <td Class = 'name' colspan = '4'>
                          <input type = "text" Class = "letra" name = "vtriid" style = "width:80"
                            onBlur  = "javascript:this.value = this.value.toUpperCase();
                                                  uLinks('vtriid', 'VALID');
                                                  this.style.background = '#FFFFFF'"
                            onFocus="javascript:this.style.background = '#00FFFF'">
                        </td>
                        <td Class = 'name' colspan = '12'><a href="#"></a>
                          <input type = "text" Class = "letra" name = "vtrides" style = "width:240" readonly>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vtriid.value = '';
                            document.frnav.vtrides.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.TRIIDXXX AS TIPO_REG" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvtriid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvtriid'>
                          <input type = 'hidden' name ='txvtriid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvtriid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'>Registro No.</td>
                        <td Class = 'letra7' colspan = '6'><input type = 'text' Class = 'letra' name = 'vrimid' style = 'width:120' onblur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '10'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vrimid.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.RIMIDXXX AS REGISTRO" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvrimid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvrimid'>
                          <input type = 'hidden' name ='txvrimid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvrimid'>
                        </td>
                      </tr>
                      <?php
                      switch($kMysqlDb) {
                        case "SIACOSIA":
                        case "TESIACOSIP":
                        case "DESIACOSIP": ?>
                          <tr>
                            <?php $icsc++; ?>
                            <td Class = 'letra7' colspan = '12'>Registro Item (<span style="color:red;">Aplica para Siemens</span>)</td>
                            <td Class = 'letra7' colspan = '6'><input type = 'text' Class = 'letra' name = 'vrimid2' style = 'width:120' onblur = 'javascript:this.value = this.value.toUpperCase()'></td>
                            <td Class = 'letra7' colspan = '10'></td>
                            <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vrimid2.value = '';"></td>
                            <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0205.RIMIDXXX AS REGISTRO_ITEM" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                            <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                            <td>
                              <input type = 'hidden' name ='chvrimid2' value = 'ch<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvrimid2'>
                              <input type = 'hidden' name ='txvrimid2' value = 'tx<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvrimid2'>
                            </td>
                          </tr>
                        <?php
                        break;
                      } ?>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>Oficina Incomex</td>
                        <td Class = 'letra7' colspan = '16'>
                          <select name = 'voinid' class="letrase" style="width:320">
                            <option></option>
                            <?php
                            $sq116 = "SELECT * FROM $kMysqlDb.SIAI0116 ORDER BY OINIDXXX";
                            $res116  = f_MySql("SELECT","",$sq116,$xConexion01,"");
                            while ($r116 = mysql_fetch_array($res116)) {
                              ?>
                              <option value="<?php echo $r116['OINIDXXX'] ?>"><?php echo $r116['OINDESXX'] ?></option>
                            <?php }
                            ?>
                          </select>
                        </td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.voinid.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.OINIDXXX AS OF_INCOMEX" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvoinid' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvoinid'>
                          <input type = 'hidden' name ='txvoinid' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvoinid'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'name' colspan = '12'>A&ntilde;o  Entre</td>
                        <td Class = 'name' colspan = '8'><input type = 'text' Class = 'letra' name = 'vrimano1' style = 'width:160' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '8'> Y <input type = 'text' Class = 'letra' name = 'vrimano2' style = 'width:140' onBlur = 'javascript:uFixFloat(this)'></td>
                        <td Class = 'name' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.forms['frnav']['vrimano1'].value = '';
                            document.forms['frnav']['vrimano2'].value = '';"></td>
                        <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id = "SIAI0206.RIMANOXX AS ANIO" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvrimano1' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvrimano1'>
                          <input type = 'hidden' name ='txvrimano1' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvrimano1'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'>Programa No.</td>
                        <td Class = 'letra7' colspan = '6'><input type = 'text' Class = 'letra' name = 'vrimpv' style = 'width:120' onblur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '10'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vrimpv.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.RIMPVXXX AS PROGRAMA" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvrimpv' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvrimpv'>
                          <input type = 'hidden' name ='txvrimpv' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvrimpv'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'>Cod Interno Producto</td>
                        <td Class = 'letra7' colspan = '6'><input type = 'text' Class = 'letra' name = 'vsubpvpro' style = 'width:120' onblur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '10'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vsubpvpro.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.SUBPVPRO AS COD_INT_PRODUCTO" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvsubpvpro' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvsubpvpro'>
                          <input type = 'hidden' name ='txvsubpvpro' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvsubpvpro'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'>#Recibo Oficial de Pago</td>
                        <td Class = 'letra7' colspan = '6'><input type = 'text' Class = 'letra' name = 'vlimrpan' style = 'width:120' onblur = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '10'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vlimrpan.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.LIMRPANX AS RECIBO_PAGO_ANT" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlimrpan' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimrpan'>
                          <input type = 'hidden' name ='txvlimrpan' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimrpan'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'><a href='javascript:show_calendar("frnav.vlimfpan1")'>Fecha Pago Anterior Del</a></td>
                        <td Class = 'letra7' colspan = '6'><input type = 'text' Class = 'letra' name = 'vlimfpan1' style = 'width:120' onBlur = 'chDate(this)'></td>
                        <td Class = 'letra7' colspan = '4'><center><a href='javascript:show_calendar("frnav.vlimfpan2")'>Al</a></center></td>
                      <td Class = 'letra7' colspan = '6'><input type = 'text' Class = 'letra' name = 'vlimfpan2'  style = 'width:120' onBlur = 'chDate(this)'></td>
                      <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vlimfpan1.value = '';
                          document.frnav.vlimfpan2.value = '';"></td>
                      <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0206.LIMFPANX AS FECHA_PAGO_ANT" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                      <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                      <td>
                        <input type = 'hidden' name ='chvlimfpan1' value = 'ch<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlimfpan1'>
                        <input type = 'hidden' name ='txvlimfpan1' value = 'tx<?php echo $icsc ?>'>
                        <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlimfpan1'>
                      </td>
                      </tr>
                      <tr>
                        <?php
                        $icsc++;
                        ?>
                        <td Class = 'letra7' colspan = '12'>Lote</td>
                        <td Class = 'letra7' colspan = '6'><input type = 'text' Class = 'letra' name = 'vlote' style = 'width:120' onKeyUp = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '10'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vlote.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id='"L" AS LOTES,SIAI0205.ITEIDXXX AS ITELOTXX,SIAI0205.ADMIDXXX AS SUCIDLOT,SIAI0205.DOIIDXXX AS DOCIDLOT,SIAI0205.DOISFIDX AS DOSSUFLO, "CL" AS CANTIDAD_LOTE,SIAI0205.ITEIDXXX AS ITELOTXX,SIAI0205.ADMIDXXX AS SUCIDLOT,SIAI0205.DOIIDXXX AS DOCIDLOT,SIAI0205.DOISFIDX AS DOSSUFLO' onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvlote' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvlote'>
                          <input type = 'hidden' name ='txvlote' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvlote'>
                        </td>
                      </tr>

                      <?php
                        if ($kMysqlDb != "DEGRUPOGLA" && $kMysqlDb != "TEGRUPOGLA" && $kMysqlDb != "GRUPOGLA"){
                      ?>
                      <tr id='fila_GLA'>
                        <?php
                        $icsc++;
                        ?>
                        <td Class = 'letra7' colspan = '12'>Fecha Elaboracion Lote</td>
                        <td Class = 'letra7' colspan = '6'><input type = 'text' Class = 'letra' name = 'velaLote' style = 'width:120' onKeyUp = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '10'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.velaLote.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id='"LA" AS LOTES_FECHA_ELABORACION,SIAI0205.ITEIDXXX AS ITELOTXX,SIAI0205.ADMIDXXX AS SUCIDXXX,SIAI0205.DOIIDXXX AS DOCIDXXX,SIAI0205.DOISFIDX AS DOSSUFXX' onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                          <input type = 'hidden' name ='chvelaLote' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvelaLote'>
                          <input type = 'hidden' name ='txvelaLote' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvelaLote'>
                        </td>
                      </tr>
                      <?php
                        }
                      ?>

                      <tr>
                        <?php
                        $icsc++;
                        ?>
                        <td Class = 'letra7' colspan = '12'>Fecha Vencimiento Lote</td>
                        <td Class = 'letra7' colspan = '6'><input type = 'text' Class = 'letra' name = 'vfecLote' style = 'width:120' onKeyUp = 'javascript:this.value = this.value.toUpperCase()'></td>
                        <td Class = 'letra7' colspan = '10'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.vfecLote.value = '';"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id='"LV" AS LOTES_FECHA_VENCIMIENTO,SIAI0205.ITEIDXXX AS ITELOTXX,SIAI0205.ADMIDXXX AS SUCIDXXX,SIAI0205.DOIIDXXX AS DOCIDXXX,SIAI0205.DOISFIDX AS DOSSUFXX' onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chvfecLote' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvfecLote'>
                          <input type = 'hidden' name ='txvfecLote' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvfecLote'>
                        </td>
                      </tr>
                      <tr>
                        <?php $icsc++; ?>
                        <td Class = 'letra7' colspan = '12'>Estado</td>
                        <td Class = 'letra7' colspan = '5'>
                          <select name = 'cRegEst' Class = 'letrase' style = 'width:100;height:18'>
                            <option value = ''></option>
                            <option value = 'TODOS'>TODOS</option>
                            <option value = 'PROVISIONAL'>PROVISIONAL</option>
                            <option value = 'ACTIVO'>ACTIVO</option>
                            <option value = 'INACTIVO'>INACTIVO</option>
                          </select>
                        </td>
                        <td Class = 'letra7' colspan = '6'></td>
                        <td Class = 'letra7' colspan = '5'></td>
                        <td Class = 'letra7' colspan = '4'><img src = '../../graphics/clear.bmp' onMousedown = "javascript:document.frnav.cRegEst.value = ''"></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0205.REGESTXX AS ESTADO" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                        <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                        <td>
                          <input type = 'hidden' name ='chcRegEst' value = 'ch<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chcRegEst'>
                          <input type = 'hidden' name ='txcRegEst' value = 'tx<?php echo $icsc ?>'>
                          <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txcRegEst'>
                        </td>
                      </tr>
                      <?php
                      switch ($kMysqlDb) {
                        case "SIACOSIA":
                        case "TESIACOSIP":
                        case "DESIACOSIP": ?>
                          <tr>
                            <?php $icsc++; ?>
                            <td Class = 'name' colspan = '12'>Vlr Moneda Distinta DAV</td>
                            <td Class = 'name' colspan = '20'><input type = 'hidden' name ='cFacVlr' id='cFacVlr'></td>
                            <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0204.FACVLRXX AS VALOR_MONEDA_DAV" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                            <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                            <td>
                              <input type = 'hidden' name ='chcFacVlr' value = 'ch<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chcFacVlr'>
                              <input type = 'hidden' name ='txcFacVlr' value = 'tx<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txcFacVlr'>
                            </td>
                          </tr>
                          <tr>
                            <?php $icsc++; ?>
                            <td Class = 'name' colspan = '12'>Precio Neto Factura USD DAV</td>
                            <td Class = 'name' colspan = '20'><input type = 'hidden' name ='cFacVlrX' id='cFacVlrX'></td>
                            <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0204.FACVLRXX AS PRECIO_FACTURA_DAV" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                            <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                            <td>
                              <input type = 'hidden' name ='chcFacVlrX' value = 'ch<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chcFacVlrX'>
                              <input type = 'hidden' name ='txcFacVlrX' value = 'tx<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txcFacVlrX'>
                            </td>
                          </tr>
                          <tr>
                            <?php $icsc++; ?>
                            <td Class = 'name' colspan = '12'>Pagos Indi, Desc u Otros USD DAV</td>
                            <td Class = 'name' colspan = '20'><input type = 'hidden' name ='cFacInd' id='cFacInd'></td>
                            <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="SIAI0204.FACINDXX AS PAGOS_INDI_DES_RETRO_DAV" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                            <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                            <td>
                              <input type = 'hidden' name ='chcFacInd' value = 'ch<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chcFacInd'>
                              <input type = 'hidden' name ='txcFacInd' value = 'tx<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txcFacInd'>
                            </td>
                          </tr>
                          <tr>
                            <?php $icsc++; ?>
                            <td Class = 'name' colspan = '12'>Precio Pagado DAV</td>
                            <td Class = 'name' colspan = '20'><input type = 'hidden' name ='cFacVlrUs' id='cFacVlrUs'></td>
                            <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="'' AS PRECIO_PAGADO_PAGAR_DAV, SIAI0205.DOIIDXXX AS ITEDOIID,SIAI0205.DOISFIDX AS ITEDOISF,SIAI0205.ADMIDXXX AS ITEADMID, SIAI0205.SUBIDXXX AS ITESUBID" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                            <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                            <td>
                              <input type = 'hidden' name ='chcFacVlrUs' value = 'ch<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chcFacVlrUs'>
                              <input type = 'hidden' name ='txcFacVlrUs' value = 'tx<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txcFacVlrUs'>
                            </td>
                          </tr>
                          <tr>
                            <?php $icsc++; ?>
                            <td Class = 'name' colspan = '12'>Total Adiciones DAV</td>
                            <td Class = 'name' colspan = '20'><input type = 'hidden' name ='cTotAdDav' id='cTotAdDav'></td>
                            <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="'' AS TOTAL_ADICIONES_DAV, SIAI0205.DOIIDXXX AS ITEDOIID,SIAI0205.DOISFIDX AS ITEDOISF,SIAI0205.ADMIDXXX AS ITEADMID, SIAI0205.SUBIDXXX AS ITESUBID" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                            <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                            <td>
                              <input type = 'hidden' name ='chcTotAdDav' value = 'ch<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chcTotAdDav'>
                              <input type = 'hidden' name ='txcTotAdDav' value = 'tx<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txcTotAdDav'>
                            </td>
                          </tr>
                          <tr>
                            <?php $icsc++; ?>
                            <td Class = 'name' colspan = '12'>Gastos Entrega Post</td>
                            <td Class = 'name' colspan = '20'><input type = 'hidden' name ='cFacPosUs' id='cFacPosUs'></td>
                            <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="'' AS GASTOS_ENTREGA_POS_IMPO_DAV, SIAI0205.DOIIDXXX AS ITEDOIID,SIAI0205.DOISFIDX AS ITEDOISF,SIAI0205.ADMIDXXX AS ITEADMID, SIAI0205.SUBIDXXX AS ITESUBID" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                            <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                            <td>
                              <input type = 'hidden' name ='chcFacPosUs' value = 'ch<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chcFacPosUs'>
                              <input type = 'hidden' name ='txcFacPosUs' value = 'tx<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txcFacPosUs'>
                            </td>
                          </tr><?php
                        break;
                      }
                      ?>
                      <?php
                      switch ($kMysqlDb) {
                        case "CEVAXXXX":
                        case "DECEVAXXXX":
                        case "TECEVAXXXX":
                        case "DEDESARROL":
                        case "TEPRUEBASX": ?>
                          <tr>
                            <?php $icsc++; ?>
                            <td Class = 'letra7' colspan = '12'>Metros Cubicos CBM</td>
                            <td Class = 'letra7' colspan = '20'></td>
                            <td Class = 'letra7' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="'' AS METROS_CUBICOS_CBM,SIAI0205.DOIIDXXX AS MTCDOIID,SIAI0205.DOISFIDX AS MTCDOISF,SIAI0205.ADMIDXXX AS MTCADMID" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                            <td Class = 'letra7' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                            <td>
                              <input type = 'hidden' name ='chvdoicbm' value = 'ch<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chvdoicbm'>
                              <input type = 'hidden' name ='txvdoicbm' value = 'tx<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txvdoicbm'>
                            </td>
                          </tr><?php
                        break;
                      }
                      ?>
                      <?php
                      switch($kMysqlDb){
                        case "SIACOSIA":
                        case "TESIACOSIP":
                        case "DESIACOSIP": ?>
                          <tr>
                            <?php $icsc++; ?>
                            <td Class = 'name' colspan = '12'>Tipo Bien</td>
                            <td Class = 'name' colspan = '20'><input type = 'hidden' name ='cTdbId' id='cTdbId'></td>
                            <td Class = 'name' colspan = '2'><input type = 'checkbox' name = 'ch<?php echo $icsc ?>' id="'' AS CODIGO_TIPO_BIEN,'' AS DESCRIPCION_TIPO_BIEN,SIAI0205.PROIDXXX AS CODPROID,SIAI0205.CLIIDXXX AS CODPIDCL" onclick = 'javascript:f_Ordena(<?php echo $icsc ?>)'></td>
                            <td Class = 'name' colspan = '2'><input type = 'text' class="letra" name = 'tx<?php echo $icsc ?>' value = '' style = 'width:40;text-align:right' readonly></td>
                            <td>
                              <input type = 'hidden' name ='chcTdbId' value = 'ch<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'chv<?php echo $icsc ?>' value ='chcTdbId'>
                              <input type = 'hidden' name ='txcTdbId' value = 'tx<?php echo $icsc ?>'>
                              <input type = 'hidden' name = 'cht<?php echo $icsc ?>' value ='txcTdbId'>
                            </td>
                          </tr><?php
                        break;
                      }
                      ?>
                    </table>
                  </center>
                </fieldset>
              </td>
            </tr>
          </table>
        </center>
        <?php
        $sup = 0;
        $cli = 0;
        if ($super == 'X') {
          $sup = 1;
        }
        if ($cliente == 'X') {
          $cli = 1;
        }
        if ($kUser == 'ADMIN') {
          ?>
          <center><textarea name = 'sql' class = 'letrata' style = 'width:720;height:120'></textarea></center>
          <center><textarea name = 'sql2' class = 'letrata' style = 'width:720;height:120'></textarea></center>
        <?php } else { ?>
          <center><textarea name = 'sql' class = 'letrata' style = 'width:720;height:1;color:#FFFFFF;border-width:0' readonly></textarea></center>
          <center><textarea name = 'sql2' class = 'letrata' style = 'width:720;height:1;color:#FFFFFF;border-width:0' readonly></textarea></center>
          <?php } ?>
        <input type = 'hidden' name = 'vobjetos' value ='<?php echo $icsc ?>'>
      </form>
      <script language = 'javascript'>
        function sortNumber(a, b) {
          return (a - b);
        }

        function f_ArmaSql(tipo) {
          var cMsj = "";
          var opt = 1;
          var sig = 1;
          var arord = '';
          var arini = 0;
          var nSwitch = 0;
          var cadok = "";
          var cWhere = "";
          var cWhere205 = "";
          var cFrom = "";
          var vobj = 1 * (document.frnav.vobjetos.value);
          var cSelect = "SELECT ";
          var cSelect2 = "SELECT DISTINCT ";
          for (j = 1; j <= vobj; j++) {
            if (document.frnav['ch' + j].checked == true) {
              if (document.frnav['tx' + j].value > 0) {
                var aok = cadok.split("~");
                var ok = 1;
                for (n = 0; n < aok.length; n++) {
                  if (aok[n] == document.frnav['tx' + j].value) {
                    ok = 0;
                    sig = 0;
                    break;
                  }
                }
                if (ok == 1 && document.frnav['tx' + j].value <= vobj) {
                  arord += (document.frnav['tx' + j].value + "~");
                  cadok += ("~" + document.frnav['tx' + j].value);
                } else {
                  alert('El numero de orden ' + document.frnav['tx' + j].value + ' Esta repetido o es mayor a 41, verifique');
                }
              }
            }
          }

          var afinal = arord.split("~");
          afinal.sort(sortNumber);
          var okotr = 0;
          var okotr2 = 0;
          var okotr3 = 0;
          var trp = 0;
          for (j = 0; j < afinal.length; j++) {
            if (afinal[j].length > 0) {
              var t = afinal[j];
              var vlf = '';
              for (n = 1; n <= vobj; n++) {
                if (document.frnav['tx' + n].value == t) {
                  vlf = document.frnav['ch' + n].id;

                  if (vlf.substring(0, 8) == "SIAI0206" || vlf.indexOf("SIAI0206") != -1 ) {
                    okotr = 1;
                  }
                  if (vlf.substring(0, 8) == "SIAI0200" || vlf.indexOf("SIAI0200") != -1 ) {
                    okotr2 = 1;
                  }
                  //Cambio ticket 9375; Mostrar fecha manifiesto.
                  if (vlf.substring(73, 65) == "SIAI0200") {
                      okotr2 = 1;
                  }
                  <?php
                  switch($kMysqlDb){
                    case "ROLDANLO":
                    case "DEROLDANLO":
                    case "TEROLDANLO":
                    case "DEDESARROL":
                    ?>
                    if (document.frnav.vsccid.value.length > 0) {
                      okotr2 = 1;
                    }
                    <?php
                    break;
                  }
                  ?>
                  if (vlf.substring(0, 8) == "SIAI0204" || vlf.indexOf("SIAI0204") != -1 ) {
                    okotr3 = 1;
                  }
                  if (vlf.substring(0, 4) == "0 AS") {
                    cSelect += vlf + ",";
                    cSelect2 += vlf + ",";
                  } else {
                    cSelect += vlf + ",";
                  }
                }
              }
            }
          }

          if (cSelect.length > 7) {
            cSelect = cSelect.substring(0, (cSelect.length - 1));
          }

          if (cSelect2.length > 7) {
            cSelect2 = cSelect2.substring(0, (cSelect2.length - 1));
          }

          cWhere = "";
          cWhere205 = "";

          if (document.frnav.vdoiid.value.length > 0) {
            cWhere205 += " AND SIAI0205.DOIIDXXX = '" + document.frnav.vdoiid.value + "'";
          }

          if (document.frnav.vdoisfid.value.length > 0) {
            cWhere205 += " AND SIAI0205.DOISFIDX = '" + document.frnav.vdoisfid.value + "'";
          }

          if (document.frnav.vadmid.value.length > 0) {
            cWhere205 += " AND SIAI0205.ADMIDXXX = '" + document.frnav.vadmid.value + "'";
          }

          if (document.frnav.vusrid1.value.length > 0) {
            cWhere205 += " AND SIAI0200.USRIDXXX = '" + document.frnav.vusrid1.value + "'";
          }

          <?php
          switch($kMysqlDb){
            case "ROLDANLO":
            case "DEROLDANLO":
            case "TEROLDANLO":
            case "DEDESARROL":
            ?>
            if (document.frnav.vsccid.value.length > 0) {
              cWhere205 += " AND SIAI0200.SCCIDXXX = '" + document.frnav.vsccid.value + "'";
            }
            <?php
            break;
          }
          ?>

          <?php
					switch($kMysqlDb){
						case "DHLXXXXX":
						case "DEDHLXXXXX":
						case "TEDHLXXXXX":
						?>
						if (document.frnav.cCosDHLId.value.length > 0) {
							cWhere205 += " AND SIAI0200.DOICCOXX = '" + document.frnav.cCosDHLId.value + "'";
						}

						if (document.frnav.cDivDHLId.value.length > 0) {
							cWhere205 += " AND SIAI0200.DOIDIVXX = '" + document.frnav.cDivDHLId.value + "'";
						}
						<?php
						break;
					}
					?>


          if (document.frnav.vusrid2.value.length > 0) {
            cWhere205 += " AND SIAI0206.DIRECTOR = '" + document.frnav.vusrid2.value + "'";
          }

          if (document.frnav.vusrid3.value.length > 0) {
            cWhere205 += " AND SIAI0200.USRID3XX = '" + document.frnav.vusrid3.value + "'";
          }

          if (document.frnav.vusrid4.value.length > 0) {
            cWhere205 += " AND SIAI0200.USRID4XX = '" + document.frnav.vusrid4.value + "'";
          }

          if (document.frnav.vusrid5.value.length > 0) {
            cWhere205 += " AND SIAI0200.USRID5XX = '" + document.frnav.vusrid5.value + "'";
          }

          if (document.frnav.vusrid6.value.length > 0) {
            cWhere205 += " AND SIAI0200.USRID6XX = '" + document.frnav.vusrid6.value + "'";
          }

          if (document.frnav.vauxid.value.length > 0) {
            cWhere206 += " AND SIAI0200.AUXIDXXX LIKE '%" + document.frnav.vauxid.value + "%'";
          }

          if (document.frnav.vregfec1.value.length > 0 && document.frnav.vregfec2.value.length > 0) {
            cWhere205 += " AND SIAI0205.REGFECXX BETWEEN '" + document.frnav.vregfec1.value + "' AND '" + document.frnav.vregfec2.value + "'";
          }

          if (document.frnav.vdoiped.value.length > 0) {
            cWhere205 += " AND SIAI0205.DOIPEDXX = '" + document.frnav.vdoiped.value + "'";
          }

          if (document.frnav.vorcid.value.length > 0) {
            cWhere205 += " AND SIAI0205.ORCIDXXX = '" + document.frnav.vorcid.value + "'";
          }

          <?php
          switch($kMysqlDb){
            case "SIACOSIA":
            case "TESIACOSIP":
            case "DESIACOSIP": ?>
              if (document.frnav.vrimid2.value.length > 0) {
                cWhere205 += " AND SIAI0205.ITEID2XX = '" + document.frnav.viteid2.value + "'";
              }
            <?php
            break;
          } ?>

          if (document.frnav.vproid.value.length > 0) {
            cWhere205 += " AND SIAI0205.PROIDXXX = '" + document.frnav.vproid.value + "'";
          }

          if (document.frnav.vitenoc.value.length > 0) {
            if (document.frnav.chnoc.checked == true) {
              cWhere205 += " AND SIAI0205.ITENOCXX = '" + document.frnav.vitenoc.value + "'";
            } else {
              cWhere205 += " AND SIAI0205.ITENOCXX LIKE '%" + document.frnav.vitenoc.value + "%'";
            }
          }

          if (document.frnav.vitemac.value.length > 0) {
            if (document.frnav.chmac.checked == true) {
              cWhere205 += " AND SIAI0205.ITEMACXX = '" + document.frnav.vitemac.value + "'";
            } else {
              cWhere205 += " AND SIAI0205.ITEMACXX LIKE '%" + document.frnav.vitemac.value + "%'";
            }
          }

          if (document.frnav.vitetip.value.length > 0) {
            if (document.frnav.chtip.checked == true) {
              cWhere205 += " AND SIAI0205.ITETIPXX = '" + document.frnav.vitetip.value + "'";
            } else {
              cWhere205 += " AND SIAI0205.ITETIPXX LIKE '%" + document.frnav.vitetip.value + "%'";
            }
          }

          if (document.frnav.vitecla.value.length > 0) {
            if (document.frnav.chcla.checked == true) {
              cWhere205 += " AND SIAI0205.ITECLAXX = '" + document.frnav.vitecla.value + "'";
            } else {
              cWhere205 += " AND SIAI0205.ITECLAXX LIKE '%" + document.frnav.vitecla.value + "%'";
            }
          }

          if (document.frnav.vitemod.value.length > 0) {
            if (document.frnav.chmod.checked == true) {
              cWhere205 += " AND SIAI0205.ITEMODXX = '" + document.frnav.vitenoc.value + "'";
            } else {
              cWhere205 += " AND SIAI0205.ITEMODXX LIKE '%" + document.frnav.vitemod.value + "%'";
            }
          }

          if (document.frnav.viteref.value.length > 0) {
            if (document.frnav.chref.checked == true) {
              cWhere205 += " AND SIAI0205.ITEREFXX = '" + document.frnav.viteref.value + "'";
            } else {
              cWhere205 += " AND SIAI0205.ITEREFXX LIKE '%" + document.frnav.viteref.value + "%'";
            }
          }

          if (document.frnav.viteotc.value.length > 0) {
            if (document.frnav.chotc.checked == true) {
              cWhere205 += " AND SIAI0205.ITEOTCXX = '" + document.frnav.viteotc.value + "'";
            } else {
              cWhere205 += " AND SIAI0205.ITEOTCXX LIKE '%" + document.frnav.viteotc.value + "%'";
            }
          }
          if (document.frnav.vitente.value.length > 0) {
            if (document.frnav.chnte.checked == true) {
              cWhere205 += " AND SIAI0205.ITENTEXX = '" + document.frnav.vitente.value + "'";
            } else {
              cWhere205 += " AND SIAI0205.ITENTEXX LIKE '%" + document.frnav.vitente.value + "%'";
            }
          }

          if (document.frnav.vitedes.value.length > 0) {
            if (document.frnav.chdes.checked == true) {
              cWhere205 += " AND SIAI0205.ITEDESXX = '" + document.frnav.vitedes.value + "'";
            } else {
              cWhere205 += " AND SIAI0205.ITEDESXX LIKE '%" + document.frnav.vitedes.value + "%'";
            }
          }

          if (document.frnav.vprodesfa.value.length > 0) {
            if (document.frnav.chdsf.checked == true) {
              cWhere205 += " AND SIAI0205.PRODESFA = '" + document.frnav.vprodesfa.value + "'";
            } else {
              cWhere205 += " AND SIAI0205.PRODESFA LIKE '%" + document.frnav.vprodesfa.value + "%'";
            }
          }

          if (document.frnav.vitedesin.value.length > 0) {
            if (document.frnav.chdin.checked == true) {
              cWhere205 += " AND SIAI0205.ITEDESIN = '" + document.frnav.vitedesin.value + "'";
            } else {
              cWhere205 += " AND SIAI0205.ITEDESIN LIKE '%" + document.frnav.vitedesin.value + "%'";
            }
          }

          if (document.frnav.videsfin.value.length > 0) {
            if (document.frnav.chdfi.checked == true) {
              cWhere205 += " AND SIAI0205.ITEDESFI = '" + document.frnav.videsfin.value + "'";
            } else {
              cWhere205 += " AND SIAI0205.ITEDESFI LIKE '%" + document.frnav.videsfin.value + "%'";
            }
          }

          if (document.frnav.vumciddav.value.length > 0) {
            if (document.frnav.chudav.checked == true) {
              cWhere205 += " AND SIAI0205.UMCIDDAV = '" + document.frnav.vumciddav.value + "'";
            } else {
              cWhere205 += " AND SIAI0205.UMCIDDAV LIKE '%" + document.frnav.vumciddav.value + "%'";
            }
          }
          //Valor unitario DAV
          if (document.frnav.vitefodav.value.length > 0) {
            if (document.frnav.valudav.checked == true) {
              cWhere205 += " AND (SIAI0205.LIMFOBXX/SIAI0205.ITECANDV) = '" + document.frnav.vitefodav.value + "'";
            } else {
              cWhere205 += " AND (SIAI0205.LIMFOBXX/SIAI0205.ITECANDV) LIKE '%" + document.frnav.vitefodav.value + "%'";
            }
          }

          //Valor DAV Total
          if (document.frnav.vitetodav.value.length > 0) {
            if (document.frnav.valtdav.checked == true) {
              cWhere205 += " AND SIAI0205.LIMFOBXX = '" + document.frnav.vitetodav.value + "'";
            } else {
              cWhere205 += " AND SIAI0205.LIMFOBXX LIKE '%" + document.frnav.vitetodav.value + "%'";
            }
          }

          //Valor unitario DAV Item
          if (document.frnav.vitefodavitem.value.length > 0) {
            if (document.frnav.valudavitem.checked == true) {
              cWhere205 += " AND (SIAI0205.ITEVLRXX/SIAI0205.ITECANDV) = '" + document.frnav.vitefodavitem.value + "'";
            } else {
              cWhere205 += " AND (SIAI0205.ITEVLRXX/SIAI0205.ITECANDV) LIKE '%" + document.frnav.vitefodavitem.value + "%'";
            }
          }

          //Valor DAV Total Item
          if (document.frnav.vitetodavitem.value.length > 0) {
            if (document.frnav.valtdavitem.checked == true) {
              cWhere205 += " AND SIAI0205.ITEVLRXX = '" + document.frnav.vitetodavitem.value + "'";
            } else {
              cWhere205 += " AND SIAI0205.ITEVLRXX LIKE '%" + document.frnav.vitetodavitem.value + "%'";
            }
          }

          if (document.frnav.vfacid.value.length > 0) {
            cWhere205 += " AND SIAI0205.FACIDXXX LIKE '%" + document.frnav.vfacid.value + "%'";
          }

          if (document.frnav.vpaiite.value.length > 0) {
            cWhere205 += " AND SIAI0205.ITEPAIID LIKE '%" + document.frnav.vpaiite.value + "%'";
          }

          if (document.frnav.vfacsap.value.length > 0) {
              cWhere205 += " AND SIAI0204.FACSAPXX LIKE '%" + document.frnav.vfacsap.value + "%'";
          }

          if (document.frnav.vlimcan1.value >= 0 && document.frnav.vlimcan2.value > 0) {
            cWhere205 += " AND SIAI0205.ITECANXX BETWEEN " + document.frnav.vlimcan1.value + " AND " + document.frnav.vlimcan2.value;
          }

          <?php
          switch($kMysqlDb){
            case "ALMAVIVA":
            case "TEALMAVIVA":
            case "DEALMAVIVA": ?>
              if (document.frnav.vLimBul1.value >= 0 && document.frnav.vLimBul2.value > 0) {
                cWhere205 += " AND SIAI0205.LIMFOBXX BETWEEN " + document.frnav.vLimBul1.value + " AND " + document.frnav.vLimBul2.value;
              }
            <?php
            break;
          } ?>

          if (document.frnav.vlimcandv1.value >= 0 && document.frnav.vlimcandv2.value > 0) {
            cWhere205 += " AND SIAI0205.ITECANDV BETWEEN " + document.frnav.vlimcandv1.value + " AND " + document.frnav.vlimcandv2.value;
          }

          if (document.frnav.vitevun1.value >= 0 && document.frnav.vitevun2.value > 0) {
            cWhere205 += " AND SIAI0205.ITEVLRXX/SIAI0205.ITECANXX BETWEEN " + document.frnav.vitevun1.value + " AND " + document.frnav.vitevun2.value;
          }

          if (document.frnav.vlimvlr1.value >= 0 && document.frnav.vlimvlr2.value > 0) {
            cWhere205 += " AND SIAI0205.LIMFOBXX BETWEEN " + document.frnav.vlimvlr1.value + " AND " + document.frnav.vlimvlr2.value;
          }

          if (document.frnav.vlimfle1.value >= 0 && document.frnav.vlimfle2.value > 0) {
            cWhere205 += " AND SIAI0205.LIMFLEXX BETWEEN " + document.frnav.vlimfle1.value + " AND " + document.frnav.vlimfle2.value;
          }

          if (document.frnav.vlimseg1.value >= 0 && document.frnav.vlimseg2.value > 0) {
            cWhere205 += " AND SIAI0205.LIMSEGXX BETWEEN " + document.frnav.vlimseg1.value + " AND " + document.frnav.vlimseg2.value;
          }

          if (document.frnav.vlimotr1.value >= 0 && document.frnav.vlimotr2.value > 0) {
            cWhere205 += " AND SIAI0205.LIMVARXX+SIAI0205.LIMCONXX BETWEEN " + document.frnav.vlimotr1.value + " AND " + document.frnav.vlimotr2.value;
          }

          if (document.frnav.vseflot1.value >= 0 && document.frnav.vseflot2.value > 0) {
            cWhere205 += " AND (SIAI0205.LIMFLEXX+SIAI0205.LIMSEGXX+SIAI0205.LIMVARXX+SIAI0205.LIMCONXX) BETWEEN " + document.frnav.vseflot1.value + " AND " + document.frnav.vseflot2.value;
          }

          if (document.frnav.vlimaju1.value >= 0 && document.frnav.vlimaju2.value > 0) {
            cWhere205 += " AND SIAI0205.LIMAJUXX BETWEEN " + document.frnav.vlimaju1.value + " AND " + document.frnav.vlimaju2.value;
          }

          if (document.frnav.vlimnet1.value >= 0 && document.frnav.vlimnet2.value > 0) {
            cWhere205 += " AND SIAI0205.LIMFOBXX+SIAI0205.LIMFLEXX+SIAI0205.LIMSEGXX+SIAI0205.LIMCONXX+SIAI0205.LIMVARXX-SIAI0205.ITEDEDXX BETWEEN " + document.frnav.vlimnet1.value + " AND " + document.frnav.vlimnet2.value;
          }

          if (document.frnav.vcliid.value.length > 0) {
            cWhere205 += " AND SIAI0205.CLIIDXXX = '" + document.frnav.vcliid.value + "'";
          }

          if (document.frnav.TipCli.value == 'CLIENTE') {
            okotr = 1;
            cWhere205 += " " + document.frnav.Cliente.value + " ";
          }

          if (document.frnav.vdgemc.value.length > 0) {
            cWhere205 += " AND SIAI0206.DGEMCXXX = '" + document.frnav.vdgemc.value + "'";
          }

          if (document.frnav.vdgefmc1.value.length > 0 && document.frnav.vdgefmc2.value.length > 0) {
            cWhere205 += " AND SIAI0206.DGEFMCXX BETWEEN '" + document.frnav.vdgefmc1.value + "' AND '" + document.frnav.vdgefmc2.value + "'";
          }

          if (document.frnav.vdgedt.value.length > 0) {
            cWhere205 += " AND SIAI0206.DGEDTXXX = '" + document.frnav.vdgedt.value + "'";
          }

          if (document.frnav.vdgefdt1.value.length > 0 && document.frnav.vdgefdt2.value.length > 0) {
            cWhere205 += " AND SIAI0206.DGEFDTXX BETWEEN '" + document.frnav.vdgefdt1.value + "' AND '" + document.frnav.vdgefdt2.value + "'";
          }

          if (document.frnav.vlimstkan.value.length > 0) {
            cWhere205 += " AND SIAI0206.LIMSTKANXX = '" + document.frnav.vlimstkan.value + "'";
          }

          if (document.frnav.vlimfstka1.value.length > 0 && document.frnav.vlimfstka2.value.length > 0) {
            cWhere205 += " AND SIAI0206.LIMFSTKA BETWEEN '" + document.frnav.vlimfstka1.value + "' AND '" + document.frnav.vlimfstka2.value + "'";
          }

          if (document.frnav.vodiid3.value.length > 0) {
            cWhere205 += " AND SIAI0206.ODIID3XX = '" + document.frnav.vodiid3.value + "'";
          }

          if (document.frnav.vlimexp.value.length > 0) {
            cWhere205 += " AND SIAI0206.LIMEXPXX = '" + document.frnav.vlimexp.value + "'";
          }

          if (document.frnav.vlimfexp1.value.length > 0 && document.frnav.vlimfexp2.value.length > 0) {
            cWhere205 += " AND SIAI0206.LIMFEXPX BETWEEN '" + document.frnav.vlimfexp1.value + "' AND '" + document.frnav.vlimfexp2.value + "'";
          }

          if (document.frnav.vodiid3.value.length > 0) {
            cWhere205 += " AND SIAI0206.ODIID3XX = '" + document.frnav.vodiid3.value + "'";
          }

          if (document.frnav.vpaiid.value.length > 0) {
            cWhere205 += " AND SIAI0206.PAIIDXXX = '" + document.frnav.vpaiid.value + "'";
          }

          if (document.frnav.vtraid.value.length > 0) {
            cWhere205 += " AND SIAI0206.TRAIDXXX = '" + document.frnav.vtraid.value + "'";
          }

          if (document.frnav.vpaibanid.value.length > 0) {
            cWhere205 += " AND SIAI0206.BANIDXXX = '" + document.frnav.vpaibanid.value + "'";
          }

          if (document.frnav.vodiid.value.length > 0) {
            cWhere205 += " AND SIAI0206.MONIDXXX = '" + document.frnav.vodiid.value + "'";
          }

          if (document.frnav.vmonidsg.value.length > 0) {
            cWhere205 += " AND SIAI0206.MONIDSGX = '" + document.frnav.vmonidsg.value + "'";
          }

          if (document.frnav.vtdeid.value.length > 0) {
            cWhere205 += " AND SIAI0206.TDEIDXXX = '" + document.frnav.vtdeid.value + "'";
          }

          if (document.frnav.vdaaid.value.length > 0) {
            cWhere205 += " AND SIAI0206.DAAIDXXX = '" + document.frnav.vdaaid.value + "'";
          }

          if (document.frnav.vodiid.value.length > 0) {
            cWhere205 += " AND SIAI0206.ODIIDXXX = '" + document.frnav.vodiid.value + "'";
          }

          if (document.frnav.vmtrid.value.length > 0) {
            cWhere205 += " AND SIAI0206.MTRIDXXX = '" + document.frnav.vmtrid.value + "'";
          }

          if (document.frnav.vlinid.value.length > 0) {
            cWhere205 += " AND SIAI0206.LINIDXXX = '" + document.frnav.vlinid.value + "'";
          }

          if (document.frnav.vdepid.value.length > 0) {
            cWhere205 += " AND SIAI0206.DEPIDXXX = '" + document.frnav.vdepid.value + "'";
          }

          if (document.frnav.vtcat1.value.length > 0) {
            cWhere205 += " AND SIAI0206.DGETRMXX = " + document.frnav.vtcat1.value;
          }

          if (document.frnav.vfpiid.value.length > 0) {
            cWhere205 += " AND SIAI0206.FPIIDXXX = '" + document.frnav.vfpiid.value + "'";
          }

          if (document.frnav.vpieid.value.length > 0) {
            cWhere205 += " AND SIAI0206.PIEIDXXX = '" + document.frnav.vpieid.value + "'";
          }

          if (document.frnav.vpieciu.value.length > 0) {
            cWhere205 += " AND SIAI0206.PIECIUXX LIKE '%" + document.frnav.vpieciu.value + "%'";
          }

          if (document.frnav.vpiedir.value.length > 0) {
            cWhere205 += " AND SIAI0206.PIEDIRXX LIKE '%" + document.frnav.vpiedir.value + "%'";
          }

          if (document.frnav.vpieema.value.length > 0) {
            cWhere205 += " AND SIAI0206.PIEEMAXX LIKE '%" + document.frnav.vpieema.value + "%'";
          }

          if (document.frnav.vmodid.value.length > 0) {
            cWhere205 += " AND SIAI0206.MODIDXXX = '" + document.frnav.vmodid.value + "'";
          }

          if (document.frnav.varcid.value.length > 0) {
            cWhere205 += " AND SIAI0206.ARCIDXXX = '" + document.frnav.varcid.value + "'";
          }

          if (document.frnav.varccom.value.length > 0) {
            cWhere205 += " AND SIAI0206.ARCCOMXX = '" + document.frnav.varccom.value + "'";
          }

          if (document.frnav.varcsup.value.length > 0) {
            cWhere205 += " AND SIAI0206.ARCSUPXX = '" + document.frnav.varcsup.value + "'";
          }

          if (document.frnav.vaceid.value.length > 0) {
            cWhere205 += " AND SIAI0206.ACEIDXXX = '" + document.frnav.vaceid.value + "'";
          }

          if (document.frnav.vsubcuo1.value.length > 0 && document.frnav.vsubcuo2.value.length > 0) {
            cWhere205 += " AND SIAI0206.SUBCUOXX BETWEEN " + document.frnav.vsubcuo1.value + " AND " + document.frnav.vsubcuo2.value;
          }

          if (document.frnav.vsubcuovl1.value.length > 0 && document.frnav.vsubcuovl2.value.length > 0) {
            cWhere205 += " AND SIAI0206.SUBCUOVL BETWEEN " + document.frnav.vsubcuovl1.value + " AND " + document.frnav.vsubcuovl2.value;
          }

          if (document.frnav.vsubper1.value.length > 0 && document.frnav.vsubper2.value.length > 0) {
            cWhere205 += " AND SIAI0206.SUBPERXX BETWEEN " + document.frnav.vsubper1.value + " AND " + document.frnav.vsubper2.value;
          }

          if (document.frnav.vtimid.value.length > 0) {
            cWhere205 += " AND SIAI0206.TIMIDXXX ='" + document.frnav.vtimid.value + "'";
          }

          if (document.frnav.vpaiid3.value.length > 0) {
            cWhere205 += " AND SIAI0206.SUBPAII2 ='" + document.frnav.vpaiid3.value + "'";
          }

          if (document.frnav.vlimpbr1.value >= 0 && document.frnav.vlimpbr2.value > 0) {
            cWhere205 += " AND SIAI0206.LIMPBRXX BETWEEN " + document.frnav.vlimpbr1.value + " AND " + document.frnav.vlimpbr2.value;
          }

          if (document.frnav.vlimpne1.value >= 0 && document.frnav.vlimpne2.value > 0) {
            cWhere205 += " AND SIAI0206.LIMPNEXX BETWEEN " + document.frnav.vlimpne1.value + " AND " + document.frnav.vlimpne2.value;
          }

          if (document.frnav.vtemid.value.length > 0) {
            cWhere205 += " AND SIAI0206.TEMIDXXX ='" + document.frnav.vtemid.value + "'";
          }

          if (document.frnav.vumcid.value.length > 0) {
            cWhere205 += " AND SIAI0206.UMCIDXXX ='" + document.frnav.vumcid.value + "'";
          }

          if (document.frnav.vtriid.value.length > 0) {
            cWhere205 += " AND SIAI0206.TRIIDXXX = '" + document.frnav.vtriid.value + "'";
          }

          if (document.frnav.vrimid.value.length > 0) {
            cWhere205 += " AND SIAI0206.RIMIDXXX = '" + document.frnav.vrimid.value + "'";
          }

          <?php
          switch($kMysqlDb){
            case "SIACOSIA":
            case "TESIACOSIP":
            case "DESIACOSIP": ?>
              if (document.frnav.vrimid2.value.length > 0) {
                cWhere205 += " AND SIAI0205.RIMIDXXX = '" + document.frnav.vrimid2.value + "'";
              }
            <?php
            break;
          } ?>

          if (document.frnav.voinid.value.length > 0) {
            cWhere205 += " AND SIAI0206.OINIDXXX = '" + document.frnav.voinid.value + "'";
          }

          if (document.frnav.viteaplre.value.length > 0) {
            cWhere205 += " AND SIAI0205.ITEAPLRE = '" + document.frnav.viteaplre.value + "'";
          }

          if (document.frnav.vrimano1.value >= 0 && document.frnav.vrimano2.value > 0) {
            cWhere205 += " AND ABS(SIAI0206.RIMANOXX) BETWEEN " + document.frnav.vrimano1.value + " AND " + document.frnav.vrimano2.value;
          }

          if (document.frnav.vrimpv.value.length > 0) {
            cWhere205 += " AND SIAI0206.RIMPVXXX = '" + document.frnav.vrimpv.value + "'";
          }

          if (document.frnav.vsubpvpro.value.length > 0) {
            cWhere205 += " AND SIAI0206.SUBPVPRO = '" + document.frnav.vsubpvpro.value + "'";
          }

          if (document.frnav.varcpor1.value >= 0 && document.frnav.varcpor2.value > 0) {
            cWhere205 += " AND SIAI0206.ARCPORXX BETWEEN " + document.frnav.varcpor1.value + " AND " + document.frnav.varcpor2.value;
          }

          if (document.frnav.varciva1.value >= 0 && document.frnav.varciva2.value > 0) {
            cWhere205 += " AND SIAI0206.ARCIVAXX BETWEEN " + document.frnav.varciva1.value + " AND " + document.frnav.varciva2.value;
          }

          if (document.frnav.varcres1.value >= 0 && document.frnav.varcres2.value > 0) {
            cWhere205 += " AND SIAI0206.SUBRESTL BETWEEN " + document.frnav.varcres1.value + " AND " + document.frnav.varcres2.value;
          }

          if (document.frnav.varcpre1.value >= 0 && document.frnav.varcpre2.value > 0) {
            cWhere205 += " AND SIAI0206.SUBRESPO BETWEEN " + document.frnav.varcpre1.value + " AND " + document.frnav.varcpre2.value;
          }

          if (document.frnav.vlimrpan.value.length > 0) {
            cWhere205 += " AND SIAI0206.LIMRPANX = '" + document.frnav.vlimrpan.value + "'";
          }

          if (document.frnav.vlimfpan1.value.length > 0 && document.frnav.vlimfpan2.value.length > 0) {
            cWhere205 += " AND SIAI0206.LIMFPANX BETWEEN '" + document.frnav.vlimfpan1.value + "' AND '" + document.frnav.vlimfpan2.value + "'";
          }

          if (document.frnav.vlimace.value.length > 0) {
            cWhere205 += " AND SIAI0206.LIMACEXX = '" + document.frnav.vlimace.value + "'";
          }

          if (document.frnav.vlimstk.value.length > 0) {
            cWhere205 += " AND SIAI0206.LIMSTKXX = '" + document.frnav.vlimstk.value + "'";
          }

          if (document.frnav.vlimlev.value.length > 0) {
            cWhere205 += " AND SIAI0206.LIMLEVXX = '" + document.frnav.vlimlev.value + "'";
          }

          if (document.frnav.vlimflev1.value.length > 0 && document.frnav.vlimflev2.value.length > 0) {
            cWhere205 += " AND SIAI0206.LIMFLEVX BETWEEN '" + document.frnav.vlimflev1.value + "' AND '" + document.frnav.vlimflev2.value + "'";
          }

          if (document.frnav.vlimface1.value.length > 0 && document.frnav.vlimface2.value.length > 0) {
            cWhere205 += " AND SIAI0206.LIMFACEX BETWEEN '" + document.frnav.vlimface1.value + "' AND '" + document.frnav.vlimface2.value + "'";
          }

          if (document.frnav.vlimfstk1.value.length > 0 && document.frnav.vlimfstk2.value.length > 0) {
            cWhere205 += " AND SIAI0206.LIMFSTKX BETWEEN '" + document.frnav.vlimfstk1.value + "' AND '" + document.frnav.vlimfstk2.value + "'";
          }

          if (document.frnav.vsubpaiid.value.length > 0) {
            cWhere205 += " AND SIAI0206.SUBPAIID ='" + document.frnav.vsubpaiid.value + "'";
          }

          <?php $cBaseDatos = (strlen($kMysqlDb) == 10) ? strtolower(substr($kMysqlDb, 2)) : strtolower($kMysqlDb);?>
          if( "<?php echo $vSysStr[$cBaseDatos.'_habilitar_edicion_codigo_dos_y_tres_en_items']?>" == "SI" ){
            if (document.frnav.vproid2.value.length > 0) {
              if (document.frnav.chpr2.checked == true) {
                cWhere205 += " AND SIAI0205.PROID2XX = '" + document.frnav.vproid2.value + "'";
              } else {
                cWhere205 += " AND SIAI0205.PROID2XX LIKE '%" + document.frnav.vproid2.value + "%'";
              }
            }

            if (document.frnav.vproid3.value.length > 0) {
              if (document.frnav.chpr3.checked == true) {
                cWhere205 += " AND SIAI0205.PROID3XX = '" + document.frnav.vproid3.value + "'";
              } else {
                cWhere205 += " AND SIAI0205.PROID3XX LIKE '%" + document.frnav.vproid3.value + "%'";
              }
            }
          }else{
            if(document.frnav.vproid2.value == "SI" || document.frnav.vproid3.value == "SI"){
               // alert(document.forms['frnav']['rapro'].value);
              if (document.getElementById("xProdNew").checked == false) {
                nSwitch = 1;
                cMsj = "Para Incluir Codigo de Productos Dos y Tres, Debe Seleccionar Productos Res. 057";
              }
            }
          }

          <?php
          switch($kMysqlDb){
            case "SIACOSIA":
            case "TESIACOSIP":
            case "DESIACOSIP":
              ?>
              if (document.frnav.vitenem.value.length > 0) {
                if (document.frnav.chnem.checked == true) {
                  cWhere205 += " AND SIAI0205.ITENEMXX = '" + document.frnav.vitenem.value + "'";
                } else {
                  cWhere205 += " AND SIAI0205.ITENEMXX LIKE '%" + document.frnav.vitenem.value + "%'";
                }
              }

              if (document.frnav.vitenca.value.length > 0) {
                if (document.frnav.chnca.checked == true) {
                  cWhere205 += " AND SIAI0205.ITENCAXX = '" + document.frnav.vitenca.value + "'";
                } else {
                  cWhere205 += " AND SIAI0205.ITENCAXX LIKE '%" + document.frnav.vitenca.value + "%'";
                }
              }

              if (document.frnav.vitecon.value.length > 0) {
                if (document.frnav.chcon.checked == true) {
                  cWhere205 += " AND SIAI0205.ITECONXX = '" + document.frnav.vitecon.value + "'";
                } else {
                  cWhere205 += " AND SIAI0205.ITECONXX LIKE '%" + document.frnav.vitecon.value + "%'";
                }
              }
              
              if (document.frnav.vitedelno.value.length > 0) {
                if (document.frnav.chdelno.checked == true) {
                  cWhere205 += " AND SIAI0205.ITEDELNO = '" + document.frnav.vitedelno.value + "'";
                } else {
                  cWhere205 += " AND SIAI0205.ITEDELNO LIKE '%" + document.frnav.vitedelno.value + "%'";
                }
              }
              <?php
            break;
          }
          ?>

          <?php
          switch($kMysqlDb){
            case "CEVAXXXX":
            case "TECEVAXXXX":
            case "DECEVAXXXX":
              ?>
              if (document.frnav.vitecon.value.length > 0) {
                if (document.frnav.chcon.checked == true) {
                  cWhere205 += " AND SIAI0205.ITECONXX = '" + document.frnav.vitecon.value + "'";
                } else {
                  cWhere205 += " AND SIAI0205.ITECONXX LIKE '%" + document.frnav.vitecon.value + "%'";
                }
              }
              <?php
            break;
          }
          ?>
          
          //FILTRO ESTADO DEL ITEM
          if (document.frnav.cRegEst.value != "" ) {
            switch(document.frnav.cRegEst.value){
              case "ACTIVO":
                cWhere205 += " AND SIAI0205.REGESTXX = 'ACTIVO' ";
              break;
              case "INACTIVO":
                cWhere205 += " AND SIAI0205.REGESTXX = 'INACTIVO' ";
              break;
              case "PROVISIONAL":
                cWhere205 += " AND SIAI0205.REGESTXX = 'PROVISIONAL' ";
              break;
              case "TODOS":
                cWhere205 += "";
              break;
            }
          }else{
            cWhere205 += " AND (SIAI0205.REGESTXX = 'ACTIVO' OR SIAI0205.REGESTXX = 'PROVISIONAL') ";
          }

          cFrom += " FROM SIAI0205 ";

          if (okotr == 1) {
            cFrom += "LEFT JOIN SIAI0206 ON SIAI0205.DOIIDXXX = SIAI0206.DOIIDXXX AND ";
            cFrom += "SIAI0205.DOISFIDX = SIAI0206.DOISFIDX AND ";
            cFrom += "SIAI0205.ADMIDXXX = SIAI0206.ADMIDXXX AND ";
            cFrom += "SIAI0205.SUBID2XX = SIAI0206.SUBID2XX ";
          }

          if (okotr2 == 1) {
            cFrom += "LEFT JOIN SIAI0200 ON SIAI0205.DOIIDXXX = SIAI0200.DOIIDXXX AND ";
            cFrom += "SIAI0205.DOISFIDX = SIAI0200.DOISFIDX AND ";
            cFrom += "SIAI0205.ADMIDXXX = SIAI0200.ADMIDXXX  ";
          }

          if (okotr3 == 1) {
            cFrom += "LEFT JOIN SIAI0204 ON SIAI0205.DOIIDXXX = SIAI0204.DOIIDXXX AND ";
            cFrom += "SIAI0205.DOISFIDX = SIAI0204.DOISFIDX AND ";
            cFrom += "SIAI0205.ADMIDXXX = SIAI0204.ADMIDXXX AND ";
            cFrom += "SIAI0205.FACIDXXX = SIAI0204.FACIDXXX ";
          }

          cFrom += "WHERE";

          var cGroup = "";
          if(nSwitch == 1){
            alert(cMsj);
          }else {
            if (cSelect == "SELECT ") {
              alert('Debe Hacer alguna Seleccion');
            } else {
              if (document.frnav.cModoCon.value == "HISTORICO" && document.frnav.vAnio.value == "") {
                alert('Selecciono Historico, el A\u00F1o No Puede Ser Vacio.');
              } else {
                if (document.frnav.vfacfob1.value.length > 0 ||
                    document.frnav.vfacfob2.value.length > 0 ||
                    document.frnav.vfecLote.value.length > 0 ||
                    <?php if ($kMysqlDb != "DEGRUPOGLA" && $kMysqlDb != "TEGRUPOGLA" && $kMysqlDb != "GRUPOGLA"){ ?>
                      document.frnav.velaLote.value.length > 0 ||
                    <?php } ?>
                    document.frnav.vlote.value.length > 0 ) {
                  cSelect += ", SIAI0205.ADMIDXXX AS SUCIDXXX,SIAI0205.DOIIDXXX AS DOCIDXXX,SIAI0205.DOISFIDX AS DOSSUFXX,SIAI0205.ITEIDXXX AS ITEIDFAC, SIAI0205.FACIDXXX AS DOCFACID ";
                }

                var cSql = cSelect + cFrom + cWhere205 + cWhere;
                cSql = cSql.replace("WHERE AND", "WHERE ");
                cSql = cSql.replace("WHERE  AND", "WHERE ");
                /* Ordenamiento X DO */
                cSql = cSql + " ORDER BY SIAI0205.DOIIDXXX,SIAI0205.DOISFIDX,SIAI0205.ADMIDXXX,ABS(SIAI0205.SUBID2XX),ABS(SIAI0205.ITEIDXXX) ";
                cSql = cSql.replace("WHERE ORDER BY", " ORDER BY ");
                /* */

                // alert(cSql);
                document.frnav.sql.value = cSql;
                if (tipo == 2) {
                  document.frnav.submit();
                } else {
                }
              }
            }
          }
        }
      </script>
      <?php
      ///Importador
      if (strlen($cClo) > 0) {
        ?>
        <script language="javascript">
          document.frnav.vcliid.value = "<?php echo $cCli ?>";
          document.frnav.vclinom.value = "<?php echo $cClo ?>";
          document.getElementById('idcli').href = 'javascript:alert("No permitido")';
        </script>
        <?php
      }
      ///
      if (isset($cPlaId)) {
        $sql147 = "SELECT * FROM $kMysqlDb.siai0147 WHERE foridxxx = \"fr00003\" AND plaidxxx = \"$cPlaId\" LIMIT 0,1";
        //$res147 = $mysql->f_Ejecutar($sql147);
        $res147  = f_MySql("SELECT","",$sql147,$xConexion01,"");
        if (mysql_num_rows($res147) > 0) {
          while ($r147 = mysql_fetch_array($res147)) {
            $cContenido = $r147["placontx"];
            $aContenid = explode("|", $cContenido);
            $y = 0;
            foreach ($aContenid as $cContenido) {
              $vContenido = explode("~", $cContenido);

              switch (substr($vContenido[0], 0, 2)) {

                case "ch":
                  if (strlen($vContenido[0]) != 5 && strlen($vContenido[0]) != 6) {
                    ?>
                    <script type="text/javascript">
                      var cNombre = document.forms['frnav']['<?php echo $vContenido[0]; ?>'].value;
                      document.forms['frnav'][cNombre].checked = '<?php echo $vContenido[2]; ?>';
                    </script>
                    <?php
                  } else {
                    ?>
                    <script type="text/javascript">
                      if ('<?php echo $vContenido[2]; ?>' == "true") {
                        document.forms['frnav']['<?php echo $vContenido[0]; ?>'].checked = '<?php echo $vContenido[2]; ?>';
                      }
                    </script>
                    <?php
                  }
                  break;
                case "tx":
                  # lógica para que no se reemplace la validacion del importador
                  if($TipCli == "CLIENTE" && ($vContenido[0] == "TipCli" || $vContenido[0] == "Cliente")  ){
                    $vContenido[2] = $vContenido[0] == "TipCli" ? $TipCli : $Cliente ;
                  }elseif($TipCli == "USUARIO" && ($vContenido[0] == "TipCli" || $vContenido[0] == "Cliente")  ){
                    $vContenido[2] = $vContenido[0] == "TipCli" ? "USUARIO" : "" ;
                  }
                  ?>
                  <script type="text/javascript">
                    var cNombre = document.forms['frnav']['<?php echo $vContenido[0]; ?>'].value;
                    document.forms['frnav'][cNombre].value = '<?php echo $vContenido[2]; ?>';
                  </script>
                  <?php
                  break;
                default:
                  # lógica para que no se reemplace la validacion del importador
                  if($TipCli == "CLIENTE" && ($vContenido[0] == "TipCli" || $vContenido[0] == "Cliente")  ){
                    $vContenido[2] = $vContenido[0] == "TipCli" ? $TipCli : $Cliente ;
                  }elseif($TipCli == "USUARIO" && ($vContenido[0] == "TipCli" || $vContenido[0] == "Cliente")  ){
                    $vContenido[2] = $vContenido[0] == "TipCli" ? "USUARIO" : "" ;
                  }
                  ?>
                  <script type="text/javascript">
                    if ('<?php echo $vContenido[2]; ?>' == "true") {
                      document.forms['frnav']['<?php echo $vContenido[0]; ?>'].checked = '<?php echo $vContenido[2]; ?>';
                    } else {
                      document.forms['frnav']['<?php echo $vContenido[0]; ?>'].value = '<?php echo $vContenido[2]; ?>';
                    }


                  </script>
                  <?php
                  break;
              }
            }
            ///Importador
            if (strlen($cClo) > 0) {
              ?>
              <script language="javascript">
                document.frnav.vcliid.value = "<?php echo $cCli ?>";
                document.frnav.vclinom.value = "<?php echo $cClo ?>";
                document.getElementById('idcli').href = 'javascript:alert("No permitido")';
              </script>
              <?php
            }
            ///
          }
          ?>
          <script language="javascript">
            f_ArmaSql(1);
          </script>
          <?php
        }
      }
      ?>
      <?php
    } else {
      ?>
      <center>No Tiene habilitado este m&oacute;dulo</center>
      <?php 
    }
    ?>
    <script type="text/javascript">
      var dock0 = new dockit("dockcontent0", 0);
    </script>
  </body>
</html>
