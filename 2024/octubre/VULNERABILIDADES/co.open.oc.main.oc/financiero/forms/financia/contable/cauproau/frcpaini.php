<?php
  namespace openComex;
/**
 * Tracking   Causacion Automatica a Terceros .
 * Este programa permite realizar consultas rapidas de las  Causacion Automatica a Terceros que se Encuentran en la Base de Datos.
 * @author
 * @package opencomex
 */

	include("../../../../libs/php/utility.php");

	$cPerAno = date('Y');

	/* Busco en la 05 que Tiene Permiso el Usuario*/
  $qUsrMen  = "SELECT * ";
  $qUsrMen .= "FROM $cAlfa.sys00005 ";
  $qUsrMen .= "WHERE ";
  $qUsrMen .= "sys00005.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
  $qUsrMen .= "sys00005.proidxxx = \"{$_COOKIE['kProId']}\" AND ";
  $qUsrMen .= "sys00005.menimgon <> '' ";
  $qUsrMen .= "ORDER BY sys00005.menordxx";
  $xUsrMen = f_MySql("SELECT","",$qUsrMen,$xConexion01,"");


  //Buscando comprobantes marcados de Cpc
  $qCpc  = "SELECT ";
  $qCpc .= "CONCAT(comidxxx,\"-\",comcodxx) AS comidxxx ";
  $qCpc .= "FROM $cAlfa.fpar0117 ";
  $qCpc .= "WHERE ";
  $qCpc .= "comidxxx = \"P\" AND ";
  $qCpc .= "comtipxx = \"CPC\" ";
  $xCpc = f_MySql("SELECT","",$qCpc,$xConexion01,"");
  $cCpc = "";
  while ($xRDB = mysql_fetch_array($xCpc)) {
    $cCpc .= "\"{$xRDB['comidxxx']}\",";
  }
  $cCpc = substr($cCpc,0,strlen($cCpc)-1);

  /**
   * Buscando el tipo de consecutivo de los comprobantes tipo R
   */
  $qComTco  = "SELECT comidxxx, comcodxx, comtcoxx ";
  $qComTco .= "FROM $cAlfa.fpar0117 ";
  $qComTco .= "WHERE ";
  $qComTco .= "comidxxx = \"P\" AND ";
	$qComTco .= "CONCAT(comidxxx,\"-\",comcodxx) IN ($cCpc) AND ";
  $qComTco .= "regestxx = \"ACTIVO\"";
  $xComTco = f_MySql("SELECT","",$qComTco,$xConexion01,"");
  $mComTco = array();
  while ($xRCT = mysql_fetch_array($xComTco)) {
  	$mComTco["{$xRCT['comidxxx']}-{$xRCT['comcodxx']}"] = $xRCT['comtcoxx'];
  }

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

			function fnAjusteAutomatico(xModo) {
				switch (document.forms['frgrm']['vRecords'].value) {
					case "1":
						if (document.forms['frgrm']['oChkCom'].checked == true) {
							var mComDat = document.forms['frgrm']['oChkCom'].id.split("~");
							if (mComDat[7] == "") {
								if (confirm("Esta Seguro de Crear el Comprobante de Anulacion Automatico para el Compobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
                  var cPathUrl = "frcaafrm.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gComFec="+mComDat[4]+"&gRegEst="+mComDat[5];
                  var nX       = screen.width;
                  var nY       = screen.height;
                  var nNx      = (nX-400)/2;
                  var nNy      = (nY-400)/2;
                  var cWinOpt  = "width=400,scrollbars=1,height=400,left="+nNx+",top="+nNy;
                  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                  document.cookie="kModo="+xModo+";path="+"/";
                  cWindow = window.open(cPathUrl,'cWinCam',cWinOpt);
                  cWindow.focus();
                  
									// var cPathUrl = "frcpagra.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gComFec="+mComDat[4]+"&gRegEst="+mComDat[5];
									// document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
									// document.cookie="kModo="+xModo+";path="+"/";
									// parent.fmpro.location = cPathUrl; // Invoco el menu.
								}
							} else {
								alert("El Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] Esta Facturado con la Factura ["+mComDat[7]+"] por tal Motivo no se Puede Crear Ajuste de Anulacion Automatico, Verifique.");
							}
						}
					break;
					default:
						var nSw_Prv = 0;
						for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
              if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0) {
                // Solo Deja Crear el ajuste de anulacion de un solo comprobante el Primero Seleccionado
                nSw_Prv = 1;
                var mComDat = document.forms['frgrm']['oChkCom'][i].id.split("~");
                if (mComDat[7] == "") {
                  if (confirm("Esta Seguro de Crear el Comprobante de Anulacion Automatico para el Compobante "+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+" ?")) {
                    var cPathUrl = "frcaafrm.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gComFec="+mComDat[4]+"&gRegEst="+mComDat[5];
                    var nX       = screen.width;
                    var nY       = screen.height;
                    var nNx      = (nX-400)/2;
                    var nNy      = (nY-400)/2;
                    var cWinOpt  = "width=400,scrollbars=1,height=400,left="+nNx+",top="+nNy;
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.cookie="kModo="+xModo+";path="+"/";
                    cWindow = window.open(cPathUrl,'cWinCam',cWinOpt);
                    cWindow.focus();

                    // var cPathUrl = "frcpagra.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gComFec="+mComDat[4]+"&gRegEst="+mComDat[5];
                    // document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    // document.cookie="kModo="+xModo+";path="+"/";
                    // parent.fmpro.location = cPathUrl; // Invoco el menu.
                  }
                } else {
                  alert("El Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] Esta Facturado con la Factura ["+mComDat[7]+"] por tal Motivo no se Puede Crear un Ajuste de Anulacion Automatico, Verifique.");
                }
              }
            }
          break;
        }
      }

    	function f_Ver(xComId,xComCod,xComCsc,xComCsc2,xComFec) {
      	var cPathUrl = "frcpanue.php?gComId="+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
      	document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
      	document.cookie="kMenDes=Ver Compra;path="+"/";
      	document.cookie="kModo=VER;path="+"/";
      	parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
      	document.location = cPathUrl; // Invoco el menu.
	    }

	  	function f_Borrar(xModo) {
				switch (document.forms['frgrm']['vRecords'].value) {
					case "1":
						if (document.forms['frgrm']['oChkCom'].checked == true) {
							var mComDat = document.forms['frgrm']['oChkCom'].id.split("~");
							if (mComDat[7] == "") {
                if (mComDat[5] == "ACTIVO" || mComDat[5] == "INACTIVO") {
                  if (mComDat[6] == "ABIERTO") {
                    if (confirm("Esta Seguro de Borrar el Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] ?")) {
                      var cPathUrl = "frcpanue.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gComFec="+mComDat[4];
                      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                      document.cookie="kMenDes=Editar Compra;path="+"/";
                      document.cookie="kModo="+xModo+";path="+"/";
                      parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
                      document.location = cPathUrl; // Invoco el menu.
                    }
                  } else {
                    alert("El Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] Pertenece a un Periodo que no esta [ABIERTO] por tal Motivo no se Puede Borrar, Verifique.");
                  }
                } else {
                  alert("Solo se Pueden Cambiar de Estado Comprobantes en Estado [ACTIVO] o [INACTIVO], Verifique.");
                }
              } else {
								alert("El Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] Esta Facturado con la Factura ["+mComDat[7]+"] por tal Motivo no se Puede Borrar, Verifique.");
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
								if (mComDat[7] == "") {
                  if (mComDat[5] == "ACTIVO" || mComDat[5] == "INACTIVO") {
                    if (mComDat[6] == "ABIERTO") {
                      if (confirm("Esta Seguro de Borrar el Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] ?")) {
                        var cPathUrl = "frcpanue.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gComFec="+mComDat[4];
                        document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                        document.cookie="kMenDes=Editar Compra;path="+"/";
                        document.cookie="kModo="+xModo+";path="+"/";
                        parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
                        document.location = cPathUrl; // Invoco el menu.
                      }
                    } else {
                      alert("El Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] Pertenece a un Periodo que no esta [ABIERTO] por tal Motivo no se Puede Borrar, Verifique.");
                    }
                  } else {
                    alert("Solo se Pueden Cambiar de Estado Comprobantes en Estado [ACTIVO] o [INACTIVO], Verifique.");
                  }
  		     			} else {
  								alert("El Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] Esta Facturado con la Factura ["+mComDat[7]+"] por tal Motivo no se Puede Borrar, Verifique.");
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
   						  if (mComDat[7] == "") {
       						if (mComDat[6] == "ABIERTO") {
  	   						  if (mComDat[5] == "ACTIVO" || mComDat[5] == "INACTIVO") {
  	  	   						if (confirm("Esta Seguro de Cambiar el Estado del Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] ?")) {
	  										var cPathUrl = "frcpagra.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gComFec="+mComDat[4]+"&gRegEst="+mComDat[5];
	  							      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
	  							      document.cookie="kModo="+xModo+";path="+"/";
	  							      parent.fmpro.location = cPathUrl; // Invoco el menu.
	  		  						}
  	   						  } else {
  	   						  	alert("Solo se Pueden Cambiar de Estado Comprobantes en Estado [ACTIVO] o [INACTIVO], Verifique.");
  	   						  }
       						} else {
       							alert("El Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] Pertenece a un Periodo que no esta [ABIERTO] por tal Motivo no se Puede Anular, Verifique.");
       						}
       					} else {
  								alert("El Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] Esta Facturado con la Factura ["+mComDat[7]+"] por tal Motivo no se Puede Anular, Verifique.");
  							}
  						}
  					break;
  					default:
  						var nSw_Prv = 0;
  						for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
  							if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0) {
   							  var mComDat = document.forms['frgrm']['oChkCom'][i].id.split("~");
   							  if (mComDat[7] == "") {
       							if (mComDat[6] == "ABIERTO") {
  	   							  if (mComDat[5] == "ACTIVO" || mComDat[5] == "INACTIVO") {
  	  	   							if (confirm("Esta Seguro de Cambiar el Estado del Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] ?")) {
  		     								nSw_Prv = 1;
  		     								var cPathUrl = "frcpagra.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gComFec="+mComDat[4]+"&gRegEst="+mComDat[5];
  								      	document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
  								      	document.cookie="kModo="+xModo+";path="+"/";
  								      	parent.fmpro.location = cPathUrl; // Invoco el menu.
  		  							  }
  	   							  } else {
  	   							  	alert("Solo se Pueden Cambiar de Estado Comprobantes en Estado [ACTIVO] o [INACTIVO], Verifique.");
  	   							  }
           					} else {
           						alert("El Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] Pertenece a un Periodo que no esta [ABIERTO] por tal Motivo no se Puede Anular, Verifique.");
           					}

           				} else {
    								alert("El Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] Esta Facturado con la Factura ["+mComDat[7]+"] por tal Motivo no se Puede Anular, Verifique.");
    							}
  							}
  						}
  					break;
  				}
	      }
	    }

      function f_Verificar(xModo) {
				switch (document.forms['frgrm']['vRecords'].value) {
					case "1":
						if (document.forms['frgrm']['oChkCom'].checked == true) {
							var mComDat = document.forms['frgrm']['oChkCom'].id.split("~");
              if (mComDat[6] == "ABIERTO") {
                if (mComDat[5] == "VERIFICAR") {
                  if (confirm("Esta Seguro de Verificar y Aprobar el Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] ?")) {
                    var cPathUrl = "frcpanue.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gComFec="+mComDat[4];
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.cookie="kMenDes=Verificar y Aprobar Compra;path="+"/";
                    document.cookie="kModo="+xModo+";path="+"/";
                    parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
                    document.location = cPathUrl; // Invoco el menu.
                  }
                } else {
                  alert("Solo se Pueden Verificar y Aprobar Comprobantes en Estado [VERIFICAR], Verifique.");
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
                if (mComDat[6] == "ABIERTO") {
                  if (mComDat[5] == "VERIFICAR") {
                    if (confirm("Esta Seguro de Verificar y Aprobar el Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] ?")) {
                      var cPathUrl = "frcpanue.php?gComId="+mComDat[0]+"&gComCod="+mComDat[1]+"&gComCsc="+mComDat[2]+"&gComCsc2="+mComDat[3]+"&gComFec="+mComDat[4];
                      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                      document.cookie="kMenDes=Verificar y Aprobar Compra;path="+"/";
                      document.cookie="kModo="+xModo+";path="+"/";
                      parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
                      document.location = cPathUrl; // Invoco el menu.
                    }
                  } else {
                    alert("Solo se Pueden Verificar y Aprobar Comprobantes en Estado [VERIFICAR], Verifique.");
                  }
                } else {
                  alert("El Comprobante ["+mComDat[0]+"-"+mComDat[1]+"-"+mComDat[2]+"-"+mComDat[3]+"] Pertenece a un Periodo que no esta [ABIERTO] por tal Motivo no se Puede Borrar, Verifique.");
                }
							}
						}
					break;
				}
	    }

	    function f_Imprimir() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
      	var zRegs = document.frgrm.vRecords.value;
      	if (zRegs == 1){
      		if (document.forms['frgrm']['oChkCom'].checked == true) {
      	  	var zNumero = 'v1';
      	  	var docun   = document.forms['frgrm']['oChkCom'].id.split('~');
      	  	var zComId  = docun[0];
      	  	var zComCod = docun[1];
      	  	var zComCsc = docun[2];
      	  	var zComCsc2= docun[3];
      	  	var zComFec= docun[4];
      	  	var prints = '|'+zComId+'~'+zComCod+'~'+zComCsc+'~'+zComCsc2+'~'+zComFec+'|';
      	  	var zRuta = 'frcpaprn.php?gUsrId=<?php echo $_COOKIE['kUsrId'] ?>&prints='+prints;
            document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),
                             strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
          	document.cookie="kMenDes=Imprimir Comprobante;path="+"/";
          	document.cookie="kModo=IMPRIMIR;path="+"/";
          	parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
          	document.location = zRuta; // Invoco el menu.
      	  	/*
      	  	var zRuta = 'frcpaprn.php?gUsrId=<?php echo $_COOKIE['kUsrId'] ?>&prints='+prints;
      	  	zWin = window.open(zRuta,"zImprime",zWinPro);
			 	    zWin.focus();
			 	    */
      	  }
      	}else{
	      	if (zRegs > 1){ //varios registros para imprimir //
	      		var prints = '|';
	      		var zLch = document.forms['frgrm']['oChkCom'].length;
			      for (i=0;i<zLch;i++){
			      	//if (document.frgrm.vCheck[i].checked == true){
			      	if (document.forms['frgrm']['oChkCom'][i].checked == true){
			      		var zNumero = 'v'+i;
			      	  var docun   = document.forms['frgrm']['oChkCom'][i].id.split('~');
				      	var zComId  = docun[0];
				      	var zComCod = docun[1];
				      	var zComCsc = docun[2];
				      	var zComCsc2= docun[3];
				      	var zComFec= docun[4];
				      	//prints += zSucId+'-'+zComId+'-'+zComCod+'-'+zComCsc+'-'+zComSuf+'-'+zNumero+'|';
				      	//prints += zComId+'-'+zComCod+'-'+zComCsc+'|';
				      	prints += '|'+zComId+'~'+zComCod+'~'+zComCsc+'~'+zComCsc2+'~'+zComFec+'|';
   	   	     	}
			      }

			      var zX      = screen.width;
			 	    var zY      = screen.height;
			 	    var alto = zY-80;
			    	var ancho = zX-100;
			 	    var zNx     = (zX-ancho)/2;
			 	    var zNy     = (zY-alto)/2;

			     	var zWinPro = 'width='+ancho+',scrollbars=1,height='+alto+',left='+zNx+',top=0';
			     	var zRuta = 'frcpaprn.php?gUsrId=<?php echo $_COOKIE['kUsrId'] ?>&prints='+prints;
            document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),
                             strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
          	document.cookie="kMenDes=Imprimir Comprobante;path="+"/";
          	document.cookie="kModo=IMPRIMIR;path="+"/";
          	parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
          	document.location = zRuta; // Invoco el menu.
			     	/*
			      var zRuta = 'frcpaprn.php?gUsrId=<?php echo $_COOKIE['kUsrId'] ?>&prints='+prints;
			      zWin = window.open(zRuta,zNumero,zWinPro);
			      //zWin = window.open(zRuta,"zImprime",zWinPro);
			 	    zWin.focus();
			 	    */
			    }
      	}
	  	}

     	function f_Link(xModId,xProId,xMenId,xForm,xOpcion,xMenDes){
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

	 		function f_Usuarios_Cco(xCcoId,xUsrId) {
        var cRuta  = "frusucco.php?&gCcoId="+xCcoId+
                     "&gDesde="+document.forms['frgrm']['dDesde'].value+
                     "&gHasta="+document.forms['frgrm']['dHasta'].value+
                     "&gUsrId="+xUsrId;
        parent.fmpro.location = cRuta;
      }

  	</script>
  </head>
  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
		  <form name = "frestado" action = "frcpagra.php" method = "post" target="fmpro">
  			<input type = "hidden" name = "cComId"  value = "">
  			<input type = "hidden" name = "cComCod" value = "">
  			<input type = "hidden" name = "cComCsc"  value = "">
  			<input type = "hidden" name = "cComCsc2" value = "">
  			<input type = "hidden" name = "cCliEst" value = "">
  			<input type = "hidden" name = "vTimesSave" value = "0">
		  </form>

		  <form name = "frgrm" action = "frcpaini.php" method = "post" target="fmwork">
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
													<?php	} else { ?>
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

				if ($vPaginas == "") {
        	$vPaginas = "1";
				}

				/**
				 * Si Viene Vacio el $cUsrId lo Cargo con la Cookie del Usuario
				 * Si no Hago el SELECT con el Usuario que me Entrega el Combo de INI
				 */
				if ($cUsrId == "") {
					$cUsrId = ($_COOKIE['kUsrId'] == "ADMIN" || $cUsrInt == "SI") ? "ALL":$_COOKIE['kUsrId'];
				}

	      /***** Sql para Buscar Periodos Abiertos *****/
				$zSqlPdo  = "SELECT CONCAT(peranoxx,permesxx) AS periodo ";
				$zSqlPdo .= "FROM $cAlfa.fpar0122 ";
				$zSqlPdo .= "WHERE ";
				$zSqlPdo .= "comidxxx = \"P\"  AND ";
				$zSqlPdo .= "regestxx = \"ABIERTO\" ";
				$zSqlPdo .= "GROUP BY peranoxx,permesxx";
				$zCrsPdo  = f_MySql("SELECT","",$zSqlPdo,$xConexion01,"");
				$cPerAbi = "";
				if (mysql_num_rows($zCrsPdo) > 0) {
					while ($zRPdo = mysql_fetch_array($zCrsPdo)) {
						$cPerAbi .= "\"".$zRPdo['periodo']."\"".",";
					}
					$cPerAbi = substr($cPerAbi,0,(strlen($cPerAbi)-1));
				}


        /**INICIO SQL**/
				if ($_POST['cPeriodos'] == "") {
					$_POST['cPeriodos'] == "20";
					$_POST['dDesde'] = substr(date('Y-m-d'),0,8)."01";
					$_POST['dHasta'] = date('Y-m-d');
				}
				$cCcoIdAux = explode("~",$cCcoId);

				if ($_POST['vSearch'] != "") {
          /**
           * Buscando los id que corresponden a las busquedas de los lefjoin
           */
           $qUsrNom  = "SELECT ";
           $qUsrNom .= "USRIDXXX ";
           $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
           $qUsrNom .= "WHERE IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") LIKE \"%{$_POST['vSearch']}%\" ";
           $xUsrNom = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
           $cUsrSearch = "";
           while ($xRUN = mysql_fetch_array($xUsrNom)) {
            $cUsrSearch .= "\"{$xRUN['USRIDXXX']}\",";
           }
           $cUsrSearch = substr($cUsrSearch,0,strlen($cUsrSearch)-1);

           $qCliNom  = "SELECT ";
           $qCliNom .= "CLIIDXXX ";
           $qCliNom .= "FROM $cAlfa.SIAI0150 ";
           $qCliNom .= "WHERE IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,IF((TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) <> \"\",(TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))),\"SIN NOMBRE\")) LIKE \"%{$_POST['vSearch']}%\" ";
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
					$qCabMov .= "$cAlfa.fcoc$iAno.comidxxx,";
					$qCabMov .= "$cAlfa.fcoc$iAno.comcodxx,";
					$qCabMov .= "$cAlfa.fcoc$iAno.comcscxx,";
					$qCabMov .= "$cAlfa.fcoc$iAno.comcsc2x,";
					$qCabMov .= "$cAlfa.fcoc$iAno.comcsc3x,";
					$qCabMov .= "$cAlfa.fcoc$iAno.comfecxx,";
					$qCabMov .= "$cAlfa.fcoc$iAno.comperxx,";
					$qCabMov .= "$cAlfa.fcoc$iAno.ccoidxxx,";
					$qCabMov .= "$cAlfa.fcoc$iAno.reghcrex,";
					$qCabMov .= "($cAlfa.fcoc$iAno.comvlrxx + $cAlfa.fcoc$iAno.comvlrnf) AS comvlrxx,";
					$qCabMov .= "$cAlfa.fcoc$iAno.regestxx,";
					$qCabMov .= "$cAlfa.fcoc$iAno.comfacxx,";
					$qCabMov .= "$cAlfa.fcoc$iAno.teridxxx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.terid2xx,";
          $qCabMov .= "$cAlfa.fcoc$iAno.regusrxx ";
				  if (substr_count($cOrderByOrder,"USRNOMXX") > 0) {
           $qCabMov .= ", IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
          }
          if (substr_count($cOrderByOrder,"CLINOMXX") > 0) {
            $qCabMov .= ", IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
          }
          if (substr_count($cOrderByOrder,"PRONOMXX") > 0) {
            $qCabMov .= ", IF($cAlfa.A.CLINOMXX <> \"\",$cAlfa.A.CLINOMXX,CONCAT($cAlfa.A.CLINOM1X,\" \",$cAlfa.A.CLINOM2X,\" \",$cAlfa.A.CLIAPE1X,\" \",$cAlfa.A.CLIAPE2X)) AS PRONOMXX ";
          }
					//////// SE HACE LEFT JOIN POR CADA TABLA ADICONAL DE LA QUE SE REQUIERE INFORMACION (DESCRIPCIONES Y NOMBRES)
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
	        //////// LAS CONDICIONES PROPIAS DEL INI
					$qCabMov .= "WHERE ";
					$qCabMov .= "$cAlfa.fcoc$iAno.comidxxx = \"P\" AND ";
					$qCabMov .= "$cAlfa.fcoc$iAno.comobs2x LIKE \"AUTOMATICA~%\" AND ";
					$qCabMov .= "CONCAT($cAlfa.fcoc$iAno.comidxxx,\"-\",$cAlfa.fcoc$iAno.comcodxx) IN ($cCpc) AND ";
					$qCabMov .= "$cAlfa.fcoc$iAno.regestxx IN (\"ACTIVO\",\"INACTIVO\",\"VERIFICAR\") AND ";

					//// CODIGO NUEVO PARA REEEMPLAZAR EL {$_POST['vSearch']}
					$qCabMov .= "(";
					$qCabMov .= "$cAlfa.fcoc$iAno.comcodxx LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qCabMov .= "$cAlfa.fcoc$iAno.comcscxx LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qCabMov .= "$cAlfa.fcoc$iAno.comcsc2x LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qCabMov .= "$cAlfa.fcoc$iAno.comcsc3x LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qCabMov .= "$cAlfa.fcoc$iAno.comfecxx LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qCabMov .= "($cAlfa.fcoc$iAno.comvlrxx + $cAlfa.fcoc$iAno.comvlrnf) LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qCabMov .= "$cAlfa.fcoc$iAno.comperxx LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qCabMov .= "$cAlfa.fcoc$iAno.reghcrex LIKE \"%{$_POST['vSearch']}%\" OR ";
				  if ($cUsrSearch != "") {
            $qCabMov .= "$cAlfa.fcoc$iAno.regusrxx IN ($cUsrSearch) OR ";
          }
          if ($cCliIdSearch != "") {
            $qCabMov .= "$cAlfa.fcoc$iAno.teridxxx IN ($cCliIdSearch) OR ";
            $qCabMov .= "$cAlfa.fcoc$iAno.terid2xx IN ($cCliIdSearch) OR ";
          }
	        $qCabMov .= "$cAlfa.fcoc$iAno.regestxx LIKE \"%{$_POST['vSearch']}%\") AND ";
	        if ($cCcoIdAux[1] <> "") {
           $qCabMov .= "$cAlfa.fcoc$iAno.ccoidxxx = \"{$cCcoIdAux[1]}\" AND ";
          }
          if ($cUsrId <> "" && $cUsrId <> "ALL") {
           $qCabMov .= "$cAlfa.fcoc$iAno.regusrxx = \"$cUsrId\" AND ";
          }
					$qCabMov .= "$cAlfa.fcoc$iAno.comfecxx BETWEEN \"{$_POST['dDesde']}\" AND \"{$_POST['dHasta']}\") ";
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
	          	$cOrdComId = str_replace("comidxxx", "CONCAT(comidxxx,\"-\",comcodxx,\"-\",comcscxx,\"-\",IF(comcsc3x != \"\", comcsc3x, comcsc2x))", $_POST[$vOrderByOrder[$z]]);
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
				$cIdCountRow = mt_rand(1000000000, 9999999999);
				$xCabMov = mysql_query($qCabMov, $xConexion01, true, $cIdCountRow);
        //f_Mensaje(__FILE__,__LINE__,$qCabMov."~".mysql_num_rows($xCabMov));
        
				$xNumRows = mysql_query("SELECT @foundRows".$cIdCountRow." AS CANTIDAD", $xConexion01, false);
				$xRNR     = mysql_fetch_array($xNumRows);
				$nRNR     = $xRNR['CANTIDAD'];

					while ($xRCC = mysql_fetch_array($xCabMov)) {
					  //Busando Nombre del usuario
					  if (substr_count($cOrderByOrder,"USRNOMXX") == 0) {
              $qUsrNom  = "SELECT ";
              $qUsrNom .= "IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
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
              $qCliNom .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,IF((TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) <> \"\",(TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))),\"SIN NOMBRE\")) AS CLINOMXX ";
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
              $qProNom .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,IF((TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) <> \"\",(TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))),\"SIN NOMBRE\")) AS CLINOMXX ";
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
              $xRCC['perestxx'] = ($xRPE['regestxx'] <> "") ? $xRPE['regestxx'] : "CERRADO";
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
   			        <legend>Causaciones Automaticas a Terceros del Periodo Seleccionado (<?php echo $nRNR ?>)</legend>
     	         	<center>
       	       		<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      		<tr>
           	        	<td class="clase08" width="11%" align="left">
          	        	<input type="text" class="letra" name = "vSearch" maxlength="20" value = "<?php echo $vSearch ?>" style= "width:80"
          	        		onblur="javascript:this.value=this.value.toUpperCase();
    																			 document.frgrm.vLimInf.value='00'; ">
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
    																			    };">
            	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:hand" title="Mostrar Todo"
    					      		onClick ="javascript:document.forms['frgrm']['cCcoId'].value='<?php echo $gUsrCco ?>';
    					      												 document.forms['frgrm']['cUsrId'].value='<?php echo $_COOKIE['kUsrId']?>';
    					      												 document.forms['frgrm']['vSearch'].value='';
    					      												 document.forms['frgrm']['vLimInf'].value='00';
    					      												 document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
    					      												 document.forms['frgrm']['vPaginas'].value='1';
    					      												 document.forms['frgrm']['vSortField'].value='';
    					      												 document.forms['frgrm']['vSortType'].value='';
    					      												 document.forms['frgrm']['vTimes'].value='';
    					      												 document.forms['frgrm']['dDesde'].value='';
    					      												 document.forms['frgrm']['dDesde'].value='<?php echo substr(date('Y-m-d'),0,8)."01";  ?>';
    					      												 document.forms['frgrm']['dHasta'].value='<?php echo date('Y-m-d');  ?>';
    					      												 document.forms['frgrm']['cPeriodos'].value='20';
    					      												 document.forms['frgrm']['cOrderByOrder'].value='';
    					      												 document.forms['frgrm'].submit()">
                  	  </td>
       	       				<td class="name" width="03%" align="left">Filas&nbsp;
       	       					<input type="text" class="letra" name = "vLimSup" value = "<?php echo $vLimSup ?>" style="width:30;text-align:right"
      					      		onblur = "javascript:uFixFloat(this);
      					      												 document.frgrm.vLimInf.value='00'; ">
       	       				</td>
       	       				<td class="name" width="06%" align="center">
       	       					<?php if (ceil($nRNR/$vLimSup) > 1) { ?>
       	       						<?php if ($vPaginas == "1") { ?>
      											<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
           	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
           	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  	style = "cursor:pointer" title="Pagina Siguiente"
       	       								onClick = "javascript:document.frgrm.vPaginas.value++;
      					      												 			document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
      					      												 			document.frgrm.submit()">
           	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  	style = "cursor:pointer" title="Ultima Pagina"
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
       	       				<td class="name" width="08%" align="center">Pag&nbsp;
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
       	       				<td class="name" width="06%" align="center">
       	       					<input type = "text" Class = "letra" style = "width:95%;text-align:center" name = "dDesde" value = "<?php
       	       					if($_POST['dDesde']=="" && $_POST['cPeriodos'] == ""){
       	       					  echo substr(date('Y-m-d'),0,8)."01";
       	       					} else{
       	       					  echo $_POST['dDesde'];
       	       					} ?>" readOnly
       	       					  onblur="javascript:document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));">
       	       				</td>
       	       				<td class="name" width="06%" align="center">
       	       					<input type = "text" Class = "letra" style = "width:95%;text-align:center" name = "dHasta" value = "<?php
       	       					  if($_POST['dHasta']=="" && $_POST['cPeriodos'] == ""){
       	       					    echo date('Y-m-d');
       	       					  } else{
       	       					    echo $_POST['dHasta'];
       	       					  }  ?>" readOnly
       	       				    onblur = "javascript:document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));">
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
                        <select Class = "letrase" name = "cCcoId" style = "width:99%" onchange="javascript:f_Usuarios_Cco(this.value,'');" >
				                    <option value = "" selected>SUCURSAL</option>
				                    <?php
				                    $qSucDat  = "SELECT sucidxxx,ccoidxxx,sucdesxx FROM $cAlfa.fpar0008 WHERE ";
				                    $qSucDat .= "regestxx = \"ACTIVO\" ORDER BY sucdesxx";
				                    $xSucDat = f_MySql("SELECT","",$qSucDat,$xConexion01,"");
				                    if (mysql_num_rows($xSucDat) > 0) {
				                      while ($xRSD = mysql_fetch_array($xSucDat)) { ?>
				                       <option value = "<?php echo $xRSD['sucidxxx']."~".$xRSD['ccoidxxx'] ?>"><?php echo $xRSD['sucdesxx'] ?></option>
				                      <?php }
				                    }  ?>
				                  </select>
				                </td>
				                <td class="name" width="13%" align="left">
				                  <select Class = "letrase" name = "cUsrId" value = "<?php echo $cUsrId ?>" style = "width:99%" >
				                    <option value = "ALL" selected>USUARIOS</option>
				                  </select>
				                  <script language="javascript">
				                   document.forms['frgrm']['cCcoId'].value = "<?php echo $cCcoId ?>";
				                   f_Usuarios_Cco(document.forms['frgrm']['cCcoId'].value,"<?php echo $cUsrId ?>");
				                  </script>
				                </td>
				                <td class="name" width="10%" align="left">
				    							<select Class = "letrase" name = "cOrderTramite" value = "<?php echo $cOrderTramite ?>" style = "width:99%" >
				    								<option value = "" >ORDENAR POR</option>
				    								<option value = "comcscxx">CONSECUTIVO 1</option>
				    								<option value = "comcsc2x">CONSECUTIVO 2</option>
				    								<?php if (f_InList($cAlfa,"UPSXXXXX","DEUPSXXXXX","TEUPSXXXXX")) { ?>
				    									<option value = "comcsc3x">CONSECUTIVO 3</option>
				    								<?php } ?>
				    								<option value = "comfecxx">FECHA COMPROBANTE</option>
				    							</select>
				    							<script language='javascript'>
				    								document.forms['frgrm']['cOrderTramite'].value = "<?php echo $cOrderTramite ?>";
				    							</script>
				     	       		</td>
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

  													while ($mBotAcc = mysql_fetch_array($xBotAcc)) {
  														switch ($mBotAcc['menopcxx']) {
  															case "BORRAR": ?>
  																<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_drop.png" onClick = "javascript:f_Borrar('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
  															<?php break;
  															case "IMPRIMIR": ?>
  																<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_print.png" onClick = "javascript:f_Imprimir()" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
  															<?php break;
  															case "CAMBIAESTADO": ?>
  																<img src = "<?php echo $cPlesk_Skin_Directory ?>/failed.jpg" onClick = "javascript:f_Cambia_Estado('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
  															<?php break;
																case "AJUSTEAUTO":
																	if ($vSysStr['system_financiero_activar_ajuste_automatico'] == 'SI') {?>
	                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_deltbl.png" onClick = "javascript:fnAjusteAutomatico('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
	                                <?php }
                                break;
                                case "VERIFICAR": 
                                  if ($vSysStr['system_activa_bpo_pcc'] == 'SI') {?>
  																<img src = "<?php echo $cPlesk_Skin_Directory ?>/ok.gif" onClick = "javascript:f_Verificar('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
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
           	         		<td class="name" width="15%">
             	         		<a href = "javascript:f_Order_By('onclick','comidxxx');" title="Ordenar">Comprobante</a>&nbsp;
             	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "comidxxx">
             	         		<input type = "hidden" name = "comidxxx" value = "<?php echo $_POST['comidxxx'] ?>" id = "comidxxx">
             	         		<script language="javascript">f_Order_By('','comidxxx')</script>
             	         	</td>
             	         	<td class="name" width="08%">
             	         		<a href = "javascript:f_Order_By('onclick','comfecxx');" title="Ordenar">Fecha</a>&nbsp;
             	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "comfecxx">
             	         		<input type = "hidden" name = "comfecxx" value = "<?php echo $_POST['comfecxx'] ?>" id = "comfecxx">
             	         		<script language="javascript">f_Order_By('','comfecxx')</script>
             	         	</td>
             	         	<td class="name" width="07%">
             	         		<a href = "javascript:f_Order_By('onclick','reghcrex');" title="Ordenar">Hora</a>&nbsp;
             	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "reghcrex">
             	         		<input type = "hidden" name = "reghcrex" value = "<?php echo $_POST['reghcrex'] ?>" id = "reghcrex">
             	         		<script language="javascript">f_Order_By('','reghcrex')</script>
             	         	</td>
             	         	<td class="name" width="08%">
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
             	         	<td class="name" width="14%">
             	         		<a href = "javascript:f_Order_By('onclick','CLINOMXX');" title="Ordenar">Cliente</a>&nbsp;
             	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "CLINOMXX">
             	         		<input type = "hidden" name = "CLINOMXX" value = "<?php echo $_POST['CLINOMXX'] ?>" id = "CLINOMXX">
             	         		<script language="javascript">f_Order_By('','CLINOMXX')</script>
             	         	</td>
             	         	<td class="name" width="14%">
             	         		<a href = "javascript:f_Order_By('onclick','PRONOMXX');" title="Ordenar">Proveedor</a>&nbsp;
             	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "PRONOMXX">
             	         		<input type = "hidden" name = "PRONOMXX" value = "<?php echo $_POST['PRONOMXX'] ?>" id = "PRONOMXX">
             	         		<script language="javascript">f_Order_By('','PRONOMXX')</script>
             	         	</td>
             	         	<td class="name" width="14%">
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

   	                     $y = 0;

   	                     for ($i=0;$i<count($mCabMov);$i++) {

   	                    	if ($y <= count($mCabMov)) { // Para Controlar el Error
  	 	                    	$zColor = "{$vSysStr['system_row_impar_color_ini']}";
  	                   	    if($y % 2 == 0) {
  	                   	    	$zColor = "{$vSysStr['system_row_par_color_ini']}";
  													} ?>
  													<!--<tr bgcolor = "<?php echo $zColor ?>">-->
  													<tr id="<?php echo $mCabMov[$i]['comidxxx'].'-'.$mCabMov[$i]['comcodxx'].'-'.$mCabMov[$i]['comcscxx'].'-'.$mCabMov[$i]['comcsc2x'] ?>" bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')"
  													  onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')"  >
  													  <td class="letra7" width="15%"><a href = javascript:f_Ver('<?php echo $mCabMov[$i]['comidxxx']?>','<?php echo $mCabMov[$i]['comcodxx']?>','<?php echo $mCabMov[$i]['comcscxx']?>','<?php echo $mCabMov[$i]['comcsc2x']?>','<?php echo $mCabMov[$i]['comfecxx']?>')>
  		                      	                             <?php echo $mCabMov[$i]['comidxxx'].'-'.$mCabMov[$i]['comcodxx'].'-'.str_pad($mCabMov[$i]['comcscxx'],10,"0",STR_PAD_LEFT).'-'.(($mCabMov[$i]['comcsc3x'] != "") ? $mCabMov[$i]['comcsc3x'] : $mCabMov[$i]['comcsc2x']) ?> </a></td>
  	       	                	<td class="letra7" width="08%"><?php echo $mCabMov[$i]['comfecxx'] ?></td>
  		                      	<td class="letra7" width="07%"><?php echo $mCabMov[$i]['reghcrex'] ?></td>
  		                      	<td class="letra7" width="08%"><?php echo $mCabMov[$i]['comperxx'] ?></td>
		                      	  <td class="letra7" width="08%"><?php echo $mCabMov[$i]['ccoidxxx'] ?></td>
  	        	              	<td class="letra7" width="14%"><?php echo substr($mCabMov[$i]['CLINOMXX'],0,28) ?></td>
  	        	              	<td class="letra7" width="14%"><?php echo substr($mCabMov[$i]['PRONOMXX'],0,28) ?></td>
                              <td class="letra7" width="14%"><?php echo substr($mCabMov[$i]['USRNOMXX'],0,28) ?></td>
  	          	              <td class="letra7" width="05%" align="right"><?php echo number_format($mCabMov[$i]['comvlrxx']) ?>&nbsp;&nbsp;&nbsp;</td>
  	          	              <td class="letra7" width="05%"><?php echo $mCabMov[$i]['regestxx'] ?></td>
  	            	            <td Class="letra7" width="02%" align="right">
  	            	              <input type="checkbox" name="oChkCom" value = "<?php echo count($mCabMov) ?>"
  	                   	    		id="<?php echo $mCabMov[$i]['comidxxx'].'~'.
  	                   	    		               $mCabMov[$i]['comcodxx'].'~'.
  	                   	    		               $mCabMov[$i]['comcscxx'].'~'.
  	                   	    		               $mCabMov[$i]['comcsc2x'].'~'.
  	                   	    		               $mCabMov[$i]['comfecxx'].'~'.
  	                   	    		               $mCabMov[$i]['regestxx'].'~'.
  	                   	    		               $mCabMov[$i]['perestxx'].'~'.
		                   	    		               $mCabMov[$i]['comfacxx'].'~'.
	                   	    		                 $mComTco["{$mCabMov[$i]['comidxxx']}-{$mCabMov[$i]['comcodxx']}"] ?>"
  	                   	    		onclick="javascript:document.forms['frgrm']['vRecords'].value='<?php echo count($mCabMov) ?>'">
  	            	            </td>
	              	        </tr>
	                	    	<?php $y++;
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
	</body>
</html>