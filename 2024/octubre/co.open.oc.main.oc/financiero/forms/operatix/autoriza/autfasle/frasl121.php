<?php // Hola Mundo ...
  namespace openComex;
  /**
   * Comprobante P-28 (Causaciones Proveedor Clientes).
   * --- Descripcion: Permite Crear Nueva Causacion .
   * @author Alexander Gordillo <alexanderg@repremundo.com.co>
   * @version 001
   */
   include("../../../../libs/php/utility.php"); ?>
<?php if (!empty($gModo) && !empty($gFunction) && !empty($gSecuencia)) { ?>
  <html>
    <head>
      <title>Tramites Disponibles</title>
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
      <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
      <script languaje = 'javascript'>
        function f_Carga_Matriz(xValue,xChecked) {

          var mDoSel = document.forms['frgrm']['mChkTra'].value.split("|");
          
          if (xChecked == true) {
            var nEncontro = 0;
            for (y=0;y<mDoSel.length;y++) {
              if (mDoSel[y] == xValue) {
                nEncontro = 1;
              }
            }
            if (nEncontro == 0) {
              document.forms['frgrm']['mChkTra'].value += xValue + "|";
            }           
          } else {
            var cDoSel = "";
            for (y=0;y<mDoSel.length;y++) {
              if (mDoSel[y] != xValue) {
                cDoSel += mDoSel[y] + "|";
              }
            }
            document.forms['frgrm']['mChkTra'].value = cDoSel;
          }
        }

        function f_Click_Check(xRows) {
          switch (xRows) {
            case "1":
              if (document.forms['frgrm']['oChkTra'].checked == true) {
                var mComCru = document.forms['frgrm']['oChkTra'].id.split("~");
                if (document.forms['frgrm']['oChkCom'].checked == true) {
                  document.forms['frgrm']['nTotal'].value = (eval(document.forms['frgrm']['nTotal'].value) + Math.abs(eval(mComCru[9])));
                }
              }
            break;
            default:
              var mCheckOn = document.forms['frgrm']['mChkTra'].value.split("|");
              for (i=0;i<mCheckOn.length;i++) {
                if (mCheckOn[i] != "") {
                  var mComCru = document.forms['frgrm']['oChkTra'][mCheckOn[i]].id.split("~");
                  if (mCheckOn[i] != "") {
                    var mComCru = document.forms['frgrm']['oChkCom'][mCheckOn[i]].id.split("~");
                    document.forms['frgrm']['nTotal'].value = (eval(document.forms['frgrm']['nTotal'].value) + Math.abs(eval(mComCru[9])));
                  }
                }
              }
            break;
          }
        }

        function f_Carga_Grilla(xRows) {
          switch (xRows) {
            case "1":
              if (document.forms['frgrm']['oChkTra'].checked == true) {
                var mComCru = document.forms['frgrm']['oChkTra'].id.split("~");
                
                parent.window.opener.document.forms['frgrm']['cSucId' + <?php echo $gSecuencia ?>].value = mComCru[0]; 
                parent.window.opener.document.forms['frgrm']['cDocId' + <?php echo $gSecuencia ?>].value = mComCru[1]; 
                parent.window.opener.document.forms['frgrm']['cDocSuf'+ <?php echo $gSecuencia ?>].value = mComCru[2];
                parent.window.opener.document.forms['frgrm']['cDocTip'+ <?php echo $gSecuencia ?>].value = mComCru[3];
                parent.window.opener.document.forms['frgrm']['cCliId' + <?php echo $gSecuencia ?>].value = mComCru[4]; 
                parent.window.opener.document.forms['frgrm']['cCliDv' + <?php echo $gSecuencia ?>].value = mComCru[5]; 
                parent.window.opener.document.forms['frgrm']['cCliNom'+ <?php echo $gSecuencia ?>].value = mComCru[6];
                if (mComCru[3] == "REGISTRO") {
                    parent.window.opener.document.getElementById('cDocSeq<?php  echo $gSecuencia ?>').style.background = "Red";
                    parent.window.opener.document.getElementById('cDocSeq<?php  echo $gSecuencia ?>').style.color = "#FFFFFF";
                    parent.window.opener.document.getElementById('cSucId<?php  echo $gSecuencia ?>').style.background = "Red";
                    parent.window.opener.document.getElementById('cSucId<?php  echo $gSecuencia ?>').style.color = "#FFFFFF";
                    parent.window.opener.document.getElementById('cDocId<?php  echo $gSecuencia ?>').style.background = "Red";
                    parent.window.opener.document.getElementById('cDocId<?php  echo $gSecuencia ?>').style.color = "#FFFFFF";
                    parent.window.opener.document.getElementById('cDocSuf<?php echo $gSecuencia ?>').style.background = "Red";
                    parent.window.opener.document.getElementById('cDocSuf<?php echo $gSecuencia ?>').style.color = "#FFFFFF";
                    parent.window.opener.document.getElementById('cDocTip<?php echo $gSecuencia ?>').style.background = "Red";
                    parent.window.opener.document.getElementById('cDocTip<?php echo $gSecuencia ?>').style.color = "#FFFFFF";
                    parent.window.opener.document.getElementById('cDosTip<?php echo $gSecuencia ?>').style.background = "Red";
                    parent.window.opener.document.getElementById('cDosTip<?php echo $gSecuencia ?>').style.color = "#FFFFFF";
                    parent.window.opener.document.getElementById('cCliId<?php echo $gSecuencia ?>').style.background = "Red";
                    parent.window.opener.document.getElementById('cCliId<?php echo $gSecuencia ?>').style.color = "#FFFFFF";
                    parent.window.opener.document.getElementById('cCliDv<?php echo $gSecuencia ?>').style.background = "Red";
                    parent.window.opener.document.getElementById('cCliDv<?php echo $gSecuencia ?>').style.color = "#FFFFFF";
                    parent.window.opener.document.getElementById('cCliNom<?php echo $gSecuencia ?>').style.background = "Red";
                    parent.window.opener.document.getElementById('cCliNom<?php echo $gSecuencia ?>').style.color = "#FFFFFF";
                } else {
                    parent.window.opener.document.getElementById('cDocSeq<?php   echo $gSecuencia ?>').style.background = "#FFFFFF";
                    parent.window.opener.document.getElementById('cDocSeq<?php   echo $gSecuencia ?>').style.color = "#000000";
                    parent.window.opener.document.getElementById('cSucId<?php   echo $gSecuencia ?>').style.background = "#FFFFFF";
                    parent.window.opener.document.getElementById('cSucId<?php   echo $gSecuencia ?>').style.color = "#000000";
                    parent.window.opener.document.getElementById('cDocId<?php   echo $gSecuencia ?>').style.background = "#FFFFFF";
                    parent.window.opener.document.getElementById('cDocId<?php   echo $gSecuencia ?>').style.color = "#000000";
                    parent.window.opener.document.getElementById('cDocSuf<?php  echo $gSecuencia ?>').style.background = "#FFFFFF";
                    parent.window.opener.document.getElementById('cDocSuf<?php  echo $gSecuencia ?>').style.color = "#000000";
                    parent.window.opener.document.getElementById('cDocTip<?php  echo $gSecuencia ?>').style.background = "#FFFFFF";
                    parent.window.opener.document.getElementById('cDocTip<?php  echo $gSecuencia ?>').style.color = "#000000";
                    parent.window.opener.document.getElementById('cDosTip<?php  echo $gSecuencia ?>').style.background = "#FFFFFF";
                    parent.window.opener.document.getElementById('cDosTip<?php  echo $gSecuencia ?>').style.color = "#000000";
                    parent.window.opener.document.getElementById('cCliId<?php  echo $gSecuencia ?>').style.background = "#FFFFFF";
                    parent.window.opener.document.getElementById('cCliId<?php  echo $gSecuencia ?>').style.color = "#000000";
                    parent.window.opener.document.getElementById('cCliDv<?php  echo $gSecuencia ?>').style.background = "#FFFFFF";
                    parent.window.opener.document.getElementById('cCliDv<?php  echo $gSecuencia ?>').style.color = "#000000";
                    parent.window.opener.document.getElementById('cCliNom<?php  echo $gSecuencia ?>').style.background = "#FFFFFF";
                    parent.window.opener.document.getElementById('cCliNom<?php  echo $gSecuencia ?>').style.color = "#000000";
                  }
              }
            break;
            default:
              if (parent.window.opener.document.forms['frgrm']['nSecuencia'].value == "<?php echo $gSecuencia ?>") { // Estoy en la ultima fila.
                var mCheckOn = document.forms['frgrm']['mChkTra'].value.split("|");
                var nSwPrv = 0; // Switch de Primera Vez
                for (i=0;i<mCheckOn.length;i++) {
                  if (mCheckOn[i] != "") {
                    if (nSwPrv == 0) { // Si es la primera vez que entre cambio el estado del switch
                      nSwPrv = 1;
                    } else { // Si no es la primera vez que entro adiciono una row
                      parent.window.opener.f_Add_New_Row_Do();
                    }
                    
                    var mComCru = document.forms['frgrm']['oChkTra'][mCheckOn[i]].id.split("~");
                    parent.window.opener.document.forms['frgrm']['cSucId' + parent.window.opener.document.forms['frgrm']['nSecuencia'].value].value = mComCru[0]; 
                    parent.window.opener.document.forms['frgrm']['cDocId' + parent.window.opener.document.forms['frgrm']['nSecuencia'].value].value = mComCru[1]; 
                    parent.window.opener.document.forms['frgrm']['cDocSuf'+ parent.window.opener.document.forms['frgrm']['nSecuencia'].value].value = mComCru[2];
                    parent.window.opener.document.forms['frgrm']['cDocTip'+ parent.window.opener.document.forms['frgrm']['nSecuencia'].value].value = mComCru[3];
                    parent.window.opener.document.forms['frgrm']['cCliId' + parent.window.opener.document.forms['frgrm']['nSecuencia'].value].value = mComCru[4]; 
                    parent.window.opener.document.forms['frgrm']['cCliDv' + parent.window.opener.document.forms['frgrm']['nSecuencia'].value].value = mComCru[5]; 
                    parent.window.opener.document.forms['frgrm']['cCliNom'+ parent.window.opener.document.forms['frgrm']['nSecuencia'].value].value = mComCru[6];
                    if (mComCru[3] == "REGISTRO") {
                      parent.window.opener.document.getElementById('cDocSeq' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                      parent.window.opener.document.getElementById('cDocSeq' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color = "#FFFFFF";
                      parent.window.opener.document.getElementById('cSucId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                      parent.window.opener.document.getElementById('cSucId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color = "#FFFFFF";
                      parent.window.opener.document.getElementById('cDocId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                      parent.window.opener.document.getElementById('cDocId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color = "#FFFFFF";
                      parent.window.opener.document.getElementById('cDocSuf'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                      parent.window.opener.document.getElementById('cDocSuf'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color = "#FFFFFF";
                      parent.window.opener.document.getElementById('cDocTip'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                      parent.window.opener.document.getElementById('cDocTip'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color = "#FFFFFF";
                      parent.window.opener.document.getElementById('cCliId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                      parent.window.opener.document.getElementById('cCliId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color = "#FFFFFF";
                      parent.window.opener.document.getElementById('cCliDv' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                      parent.window.opener.document.getElementById('cCliDv' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color = "#FFFFFF";
                      parent.window.opener.document.getElementById('cCliNom'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                      parent.window.opener.document.getElementById('cCliNom'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color = "#FFFFFF";
                    } else {
                      parent.window.opener.document.getElementById('cDocSeq' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                      parent.window.opener.document.getElementById('cDocSeq' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color = "#000000";
                      parent.window.opener.document.getElementById('cSucId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                      parent.window.opener.document.getElementById('cSucId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color = "#000000";
                      parent.window.opener.document.getElementById('cDocId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                      parent.window.opener.document.getElementById('cDocId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color = "#000000";
                      parent.window.opener.document.getElementById('cDocSuf'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                      parent.window.opener.document.getElementById('cDocSuf'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color = "#000000";
                      parent.window.opener.document.getElementById('cDocTip'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                      parent.window.opener.document.getElementById('cDocTip'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color = "#000000";
                      parent.window.opener.document.getElementById('cCliId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                      parent.window.opener.document.getElementById('cCliId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color = "#000000";
                      parent.window.opener.document.getElementById('cCliDv' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                      parent.window.opener.document.getElementById('cCliDv' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color = "#000000";
                      parent.window.opener.document.getElementById('cCliNom'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                      parent.window.opener.document.getElementById('cCliNom'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color = "#000000";
                    }
                  }
                }
              } else {
                alert("Solo se Puede Ingresar Multiples Registros si esta Ubicado en la Ultima Posicion de los Items, Verifique.");
              }
            break;
          }
          parent.window.close();
        }

        function fnMarcarRegistro(xItem,xRows,xCheck) {

          f_Carga_Matriz(xCheck.value,xCheck.checked);
          
          if (document.forms['frgrm']['cDocDosRe'+xItem].value != "") {
            var mRegistros = document.forms['frgrm']['cDocDosRe'+xItem].value.split("~");
          }
          
          switch (xRows) {
            case "1":
              //Un solo registro
              if (document.forms['frgrm']['cDocAsoIm'+xItem].value == "SI") {
                //No se deja marcar
                if (document.forms['frgrm']['oChkTra'].checked==true) {
                  document.forms['frgrm']['oChkTra'].checked=false;
                } else {
                  document.forms['frgrm']['oChkTra'].checked=true;
                }
              }
            break;
            default:
              var xSel = false;
              for (i=0;i<document.forms['frgrm']['oChkTra'].length;i++){
                if (xItem == i) {
                  //Es en el check que dio click
                  if (document.forms['frgrm']['cDocAsoIm'+xItem].value == "SI") {
                    //Es un DO de registro y no puede cambiar el valor
                    if (document.forms['frgrm']['oChkTra'][i].checked == true){
                      document.forms['frgrm']['oChkTra'][i].checked = false;
                    } else {
                      document.forms['frgrm']['oChkTra'][i].checked = true;
                    }
                  }
                }
                //Marcando o desmarcando DO de registros asociados
                if (document.forms['frgrm']['cDocDosRe'+xItem].value != "") {
                  var mDatos = document.forms['frgrm']['oChkTra'][i].id.split("~");
                  for (j=0; j<mRegistros.length; j++) {
                    if (mRegistros[j] != "") {
                      if (mDatos[0] == mRegistros[j]) {
                        document.forms['frgrm']['oChkTra'][i].checked = xCheck.checked;
                        //Se cargan los DO de registro
                        f_Carga_Matriz(document.forms['frgrm']['oChkTra'][i].value,document.forms['frgrm']['oChkTra'][i].checked);
                      }
                    }
                  }
                }               
              }
            break;
          }
        }
      </script>
    </head>
    <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">

    <center>
      <table border ="0" cellpadding="0" cellspacing="0" width="760">
        <tr>
          <td>
            <fieldset>
              <legend>Tramites Disponibles</legend>
              <form name = "frgrm" action = "" method = "post" target = "fmpro">
                <input type = "hidden" name = "mChkTra" value = "">
                <?php
                  switch ($gModo) { 
                    case "VALID":
                      $qTramites  = "SELECT * ";
                      $qTramites .= "FROM $cAlfa.sys00121 ";
                      $qTramites .= "WHERE ";
                      $qTramites .= "docidxxx  LIKE \"%$gDocId%\" AND ";
                      $qTramites .= "docafasl != \"SI\"        AND ";
                      $qTramites .= "regestxx  = \"ACTIVO\" ";
                      $qTramites .= "ORDER BY docidxxx ASC ";
                      $xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
                      // f_Mensaje(__FILE__,__LINE__,$qTramites." ~ ".mysql_num_rows($xTramites));
                      
                      $mTramites = array(); $vTotalTamites = array();
                      if (mysql_num_rows($xTramites) == 1) {
                        $vTramites = mysql_fetch_array($xTramites);
                        
                        $vTotalTamites[count($vTotalTamites)] = "{$vTramites['docidxxx']}";
                        
                        $nIncluir = 1; 
                        if ($vTramites['doctipxx'] == "REGISTRO") {
                          //Verifico que si es un DO de Registro no este asociado a un DO de Importacion
                          $qDoReg  = "SELECT sucidxxx, docidxxx, docsufxx, doctipxx, docdosre ";
                          $qDoReg .= "FROM $cAlfa.sys00121 ";
                          $qDoReg .= "WHERE ";
                          $qDoReg .= "docdosre LIKE \"%{$vTramites['docidxxx']}%\" ";
                          $xDoReg  = f_MySql("SELECT","",$qDoReg,$xConexion01,"");
                          // f_Mensaje(__FILE__,__LINE__,$qDoReg." ~ ".mysql_num_rows($xDoReg));
                          $nEncontro = 0; $cDoAso = "";
                          while ($xRDR = mysql_fetch_array($xDoReg)) {
                            $mAuxDo = explode("~", $xRDR['docdosre']);
                            for ($nA=0; $nA<count($mAuxDo);$nA++) {
                              if ($mAuxDo[$nA] != "") {
                                if ($mAuxDo[$nA] == $vTramites['docidxxx']) {
                                  $nEncontro++;
                                  $cDoAso .= "{$xRDR['doctipxx']} [{$xRDR['sucidxxx']}-{$xRDR['docidxxx']}-{$xRDR['docsufxx']}],";
                                }
                              }
                            }
                          }
                          if ($nEncontro == 0) {
                            $nIncluir = 1;
                          }
                        }
                        
                        if ($nIncluir == 1) {
                          $i = count($mTramites);
                          $mTramites[$i] = $vTramites;
                          
                          //Buscando los DO de registro asociados  al DO
                          if ($vTramites['docdosre'] != "" && $vTramites['doctipxx'] != "REGISTRO") {
                            $mAuxDo = explode("~", $vTramites['docdosre']);
                            
                            for ($nA=0; $nA<count($mAuxDo);$nA++) {
                              if ($mAuxDo[$nA] != "") {
                                if (in_array("{$mAuxDo[$nA]}", $vTotalTamites) == false) {
                                  $vTotalTamites[count($vTotalTamites)] = "{$mAuxDo[$nA]}";
                                  //Busco el DO de Registro
                                  //Busco el tramite en la sys00121 de modulo de facturacion
                                  $qRegistro  = "SELECT * ";
                                  $qRegistro .= "FROM $cAlfa.sys00121 ";
                                  $qRegistro .= "WHERE ";
                                  $qRegistro .= "doctipxx = \"REGISTRO\"       AND ";
                                  $qRegistro .= "docidxxx = \"{$mAuxDo[$nA]}\" AND ";
                                  $qRegistro .= "docafasl != \"SI\"            AND ";
                                  $qRegistro .= "regestxx = \"ACTIVO\" ";
                                  $qRegistro .= "ORDER BY docidxxx ASC ";
                                  $xRegistro  = f_MySql("SELECT","",$qRegistro,$xConexion01,"");
                                  // f_Mensaje(__FILE__,__LINE__,$qRegistro." ~ ".mysql_num_rows($xRegistro));
                                  
                                  while ($xRR = mysql_fetch_array($xRegistro)) {
                                    $i = count($mTramites);
                                    $mTramites[$i] = $xRR;
                                  } ## while ($xRR = mysql_fetch_array($xRegistro)) { ##
                                } ## if (in_array("{$mAux01[0]}~{$mAux01[1]}", $vTramites) == false) { ##
                              } ## if ($mAuxDo[$nA] != "") { ##
                            } ## for ($nA=0; $nA<count($mAuxDo);$nA++) { ##
                          } ## if ($xRT['docdosre'] != "") { ##                         
                        }
                      
                        // f_Mensaje(__FILE__,__LINE__,count($mTramites));
                        for ($i=0; $i<count($mTramites); $i++) {
                          //Busco la el nombre del cliente
                          $qDatCli  = "SELECT ";
                          $qDatCli .= "$cAlfa.SIAI0150.*, ";
                          $qDatCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
                          $qDatCli .= "FROM $cAlfa.SIAI0150 ";
                          $qDatCli .= "WHERE ";
                          $qDatCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$mTramites[$i]['cliidxxx']}\" LIMIT 0,1";
                          $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
                          if(mysql_num_rows($xDatCli) > 0) {
                            $xRDC = mysql_fetch_array($xDatCli);
                            $mTramites[$i]['clinomxx'] = $xRDC['CLINOMXX'];
                          } else {
                            $mTramites[$i]['clinomxx'] = "CLIENTE SIN NOMBRE";
                          }
                          ?>
                        
                          <script language="javascript">
                            parent.fmwork.document.forms['frgrm']['cSucId' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mTramites[$i]['sucidxxx']; ?>";
                            parent.fmwork.document.forms['frgrm']['cDocId' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mTramites[$i]['docidxxx']; ?>"; 
                            parent.fmwork.document.forms['frgrm']['cDocSuf'+ parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mTramites[$i]['docsufxx']; ?>";
                            parent.fmwork.document.forms['frgrm']['cDocTip'+ parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mTramites[$i]['doctipxx']; ?>";
                            parent.fmwork.document.forms['frgrm']['cCliId' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mTramites[$i]['cliidxxx']; ?>"; 
                            parent.fmwork.document.forms['frgrm']['cCliDv' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo f_Digito_Verificacion($mTramites[$i]['cliidxxx']); ?>"; 
                            parent.fmwork.document.forms['frgrm']['cCliNom'+ parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mTramites[$i]['clinomxx']; ?>";

                            if ("<?php echo $mTramites[$i]['doctipxx']; ?>" == "REGISTRO") {
                              parent.fmwork.document.getElementById('cDocSeq' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                              parent.fmwork.document.getElementById('cDocSeq' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.color = "#FFFFFF";
                              parent.fmwork.document.getElementById('cSucId' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                              parent.fmwork.document.getElementById('cSucId' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.color = "#FFFFFF";
                              parent.fmwork.document.getElementById('cDocId' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                              parent.fmwork.document.getElementById('cDocId' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.color = "#FFFFFF";
                              parent.fmwork.document.getElementById('cDocSuf'+ parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                              parent.fmwork.document.getElementById('cDocSuf'+ parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.color = "#FFFFFF";
                              parent.fmwork.document.getElementById('cDocTip'+ parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                              parent.fmwork.document.getElementById('cDocTip'+ parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.color = "#FFFFFF";
                              parent.fmwork.document.getElementById('cCliId' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                              parent.fmwork.document.getElementById('cCliId' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.color = "#FFFFFF";
                              parent.fmwork.document.getElementById('cCliDv' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                              parent.fmwork.document.getElementById('cCliDv' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.color = "#FFFFFF";
                              parent.fmwork.document.getElementById('cCliNom'+ parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                              parent.fmwork.document.getElementById('cCliNom'+ parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.color = "#FFFFFF";
                            } else {
                              parent.fmwork.document.getElementById('cDocSeq' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                              parent.fmwork.document.getElementById('cDocSeq' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.color = "#000000";
                              parent.fmwork.document.getElementById('cSucId' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                              parent.fmwork.document.getElementById('cSucId' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.color = "#000000";
                              parent.fmwork.document.getElementById('cDocId' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                              parent.fmwork.document.getElementById('cDocId' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.color = "#000000";
                              parent.fmwork.document.getElementById('cDocSuf'+ parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                              parent.fmwork.document.getElementById('cDocSuf'+ parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.color = "#000000";
                              parent.fmwork.document.getElementById('cDocTip'+ parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                              parent.fmwork.document.getElementById('cDocTip'+ parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.color = "#000000";
                              parent.fmwork.document.getElementById('cCliId' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                              parent.fmwork.document.getElementById('cCliId' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.color = "#000000";
                              parent.fmwork.document.getElementById('cCliDv' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                              parent.fmwork.document.getElementById('cCliDv' + parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.color = "#000000";
                            }
                          </script>
                        <?php $nSecuencia++;
                        } 
                      } else { ?>
                        <script language="javascript">
                          parent.fmwork.fnLinks("<?php echo $gFunction ?>","WINDOW","<?php echo $gSecuencia ?>");
                        </script>
                      <?php }
                    break;
                    case "WINDOW":
                      $qTramites  = "SELECT * ";
                      $qTramites .= "FROM $cAlfa.sys00121 ";
                      $qTramites .= "WHERE ";
                      $qTramites .= "docidxxx LIKE \"%$gDocId%\" AND ";
                      $qTramites .= "docafasl != \"SI\"          AND ";
                      $qTramites .= "regestxx = \"ACTIVO\" ";
                      $qTramites .= "ORDER BY docidxxx ASC ";
                      $xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
                      // f_Mensaje(__FILE__,__LINE__,$qTramites." ~ ".mysql_num_rows($xTramites));
                      
                      $mTramites = array(); $vTotalTamites = array();
                      while ($vTramites = mysql_fetch_array($xTramites)) {
                        //Solo aplica para SIACO
                        $nIncluir = 1; //Variable que indica que el tramite fue encontrado en el modulo de aduana con las condiciones necesaria para que se muestre
                        if ($vTramites['doctipxx'] == "REGISTRO") {
                          //Verifico que si es un DO de Registro no este asociado a un DO de Importacion
                          $qDoReg  = "SELECT sucidxxx, docidxxx, docsufxx, doctipxx, docdosre ";
                          $qDoReg .= "FROM $cAlfa.sys00121 ";
                          $qDoReg .= "WHERE ";
                          $qDoReg .= "docdosre LIKE \"%{$vTramites['docidxxx']}%\" ";
                          $xDoReg  = f_MySql("SELECT","",$qDoReg,$xConexion01,"");
                          //f_Mensaje(__FILE__,__LINE__,$qDoReg." ~ ".mysql_num_rows($xDoReg));
                          $nEncontro = 0; $cDoAso = "";
                          while ($xRDR = mysql_fetch_array($xDoReg)) {
                            $mAuxDo = explode("~", $xRDR['docdosre']);
                            for ($nA=0; $nA<count($mAuxDo);$nA++) {
                              if ($mAuxDo[$nA] != "") {
                                if ($mAuxDo[$nA] == $vTramites['docidxxx']) {
                                  $nEncontro++;
                                }
                              }
                            }
                          }
                          if ($nEncontro == 0) {
                            $nIncluir = 1;
                          } else {
                            $nIncluir = 0;
                          }
                        }
                        
                        if ($nIncluir == 1) {
                          $vTramites['docasoim'] = "NO"; //DO asociado a otro DO, para los DO de registro
                          $i = count($mTramites);
                          $mTramites[$i] = $vTramites;
                          
                          $vTotalTamites[count($vTotalTamites)] = "{$vTramites['docidxxx']}";
                          
                          //Buscando los DO de registro asociados  al DO
                          if ($vTramites['docdosre'] != "" && $vTramites['doctipxx'] != "REGISTRO") {
                            $mAuxDo = explode("~", $vTramites['docdosre']);
                            
                            for ($nA=0; $nA<count($mAuxDo);$nA++) {
                              if ($mAuxDo[$nA] != "") {
                                if (in_array("{$mAuxDo[$nA]}", $vTotalTamites) == false) {
                                  $vTotalTamites[count($vTotalTamites)] = "{$mAuxDo[$nA]}";
                                  //Busco el DO de Registro
                                  //Busco el tramite en la sys00121 de modulo de facturacion
                                  $qRegistro  = "SELECT * ";
                                  $qRegistro .= "FROM $cAlfa.sys00121 ";
                                  $qRegistro .= "WHERE ";
                                  $qRegistro .= "doctipxx = \"REGISTRO\"       AND ";
                                  $qRegistro .= "docidxxx = \"{$mAuxDo[$nA]}\" AND ";
                                  $qRegistro .= "docafasl != \"SI\"            AND ";
                                  $qRegistro .= "regestxx = \"ACTIVO\" ";
                                  $qRegistro .= "ORDER BY docidxxx ASC ";
                                  $xRegistro  = f_MySql("SELECT","",$qRegistro,$xConexion01,"");
                                  //f_Mensaje(__FILE__,__LINE__,$qRegistro." ~ ".mysql_num_rows($xRegistro));
                                  
                                  while ($xRR = mysql_fetch_array($xRegistro)) {
                                    $xRR['docasoim'] = "SI"; //DO asociado a otro DO, para los DO de registro
                                    $i = count($mTramites);
                                    $mTramites[$i] = $xRR;
                                  } ## while ($xRR = mysql_fetch_array($xRegistro)) { ##
                                } ## if (in_array("{$mAux01[0]}~{$mAux01[1]}", $vTramites) == false) { ##
                              } ## if ($mAuxDo[$nA] != "") { ##
                            } ## for ($nA=0; $nA<count($mAuxDo);$nA++) { ##
                          } ## if ($xRT['docdosre'] != "") { ##                         
                        }                       
                      } ## while ($vTramites = mysql_fetch_array($xTramites)) { ##
                        
                      for ($i=0; $i<count($mTramites); $i++) {
                        //Busco la el nombre del cliente
                        $qDatCli  = "SELECT ";
                        $qDatCli .= "$cAlfa.SIAI0150.*, ";
                        $qDatCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
                        $qDatCli .= "FROM $cAlfa.SIAI0150 ";
                        $qDatCli .= "WHERE ";
                        $qDatCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$mTramites[$i]['cliidxxx']}\" LIMIT 0,1";
                        $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
                        if(mysql_num_rows($xDatCli) > 0) {
                          $xRDC = mysql_fetch_array($xDatCli);
                          $mTramites[$i]['clinomxx'] = $xRDC['CLINOMXX'];
                        } else {
                          $mTramites[$i]['clinomxx'] = "CLIENTE SIN NOMBRE";
                        }
                      }
                        
                      // Empiezo a pintar los datos en el formulario.
                      if (count($mTramites) > 0) { ?>
                        <center>
                          <table cellspacing = "0" cellpadding = "1" border = "1" width = "760">
                            <tr>
                              <td width = "040" Class = "name"><center>Suc</center></td>
                              <td width = "120" Class = "name"><center>Numero</center></td>
                              <td width = "030" Class = "name"><center>Suf</center></td>
                              <td width = "100" Class = "name"><center>Tipo</center></td>
                              <td width = "100" Class = "name"><center>Nit</center></td>
                              <td Class = "name"><center>Cliente</center></td>
                              <td width = "060" Class = "name"><center>Estado</center></td>
                              <td width = "020" Class = "name"><center>CH</center></td>
                            </tr>
                            <?php for ($i=0;$i<count($mTramites);$i++) { 
                              $cColor = ($mTramites[$i]['doctipxx'] == "REGISTRO") ? "#DDDDDD" : "#FFFFFF"; ?>
                              <tr bgcolor="<?php echo $cColor ?>">
                                <td class= "name" align="center"><?php echo $mTramites[$i]['sucidxxx'] ?></td>
                                <td class= "name" align="center"><?php echo $mTramites[$i]['docidxxx'] ?></td>
                                <td class= "name" align="center"><?php echo $mTramites[$i]['docsufxx'] ?></td>
                                <td class= "name" align="center"><?php echo $mTramites[$i]['doctipxx'] ?></td>
                                <td Class = "name" align="center"><?php echo $mTramites[$i]['cliidxxx'] ?></td>
                                <td Class = "name"><?php echo substr(utf8_encode($mTramites[$i]['clinomxx']),0,20) ?></td>
                                <td class= "name" align="center"><?php echo $mTramites[$i]['regestxx'] ?></td>
                                <td class= "name" style = "text-align:right">
                                  <input type="hidden" name = "cDocDosRe<?php echo $i ?>" value = "<?php echo $mTramites[$i]['docdosre'] ?>">
                                  <input type="hidden" name = "cDocAsoIm<?php echo $i ?>" value = "<?php echo $mTramites[$i]['docasoim'] ?>">
                                  <input type="checkbox" name="oChkTra" value = "<?php echo $i ?>"
                                    id = "<?php echo $mTramites[$i]['sucidxxx']."~".
                                                     $mTramites[$i]['docidxxx']."~".
                                                     $mTramites[$i]['docsufxx']."~".
                                                     $mTramites[$i]['doctipxx']."~".
                                                     $mTramites[$i]['cliidxxx']."~".
                                                     f_Digito_Verificacion($mTramites[$i]['cliidxxx'])."~".
                                                     $mTramites[$i]['clinomxx']; ?>"
                                    onclick = "javascript:fnMarcarRegistro('<?php echo $i ?>','<?php echo count($mTramites) ?>',this);">
                                </td>
                              </tr>
                            <?php } ?>
                            <tr>
                              <td colspan="8">
                                <center>
                                  <input type="button" name="Btn_Aceptar" value = "Aceptar" style="width:50;text-align:center"
                                    onclick="javascript:f_Carga_Grilla('<?php echo count($mTramites) ?>');" readonly>
                                  <input type="button" name="Btn_Salir"   value = "Salir"   style="width:50;text-align:center"
                                    onclick="javascript:parent.window.close()" readonly>
                                </center>
                              </td>
                            </tr>
                          </table>
                        </center>
                      <?php } else {
                        f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros, Verifique."); ?>
                        <script language="javascript">
                          parent.window.opener.document.forms['frgrm']['cSucId' + '<?php echo $gSecuencia ?>'].value = "";
                          parent.window.opener.document.forms['frgrm']['cDocSuf'+ '<?php echo $gSecuencia ?>'].value = "";
                          parent.window.opener.document.forms['frgrm']['cDocTip'+ '<?php echo $gSecuencia ?>'].value = "";
                          parent.window.opener.document.forms['frgrm']['cCliId' + '<?php echo $gSecuencia ?>'].value = ""; 
                          parent.window.opener.document.forms['frgrm']['cCliDv' + '<?php echo $gSecuencia ?>'].value = ""; 
                          parent.window.opener.document.forms['frgrm']['cCliNom'+ '<?php echo $gSecuencia ?>'].value = "";

                          parent.window.opener.document.getElementById('cDocSeq' + '<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                          parent.window.opener.document.getElementById('cDocSeq' + '<?php echo $gSecuencia ?>').style.color = "#000000";
                          parent.window.opener.document.getElementById('cSucId' + '<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                          parent.window.opener.document.getElementById('cSucId' + '<?php echo $gSecuencia ?>').style.color = "#000000";
                          parent.window.opener.document.getElementById('cDocId' + '<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                          parent.window.opener.document.getElementById('cDocId' + '<?php echo $gSecuencia ?>').style.color = "#000000";
                          parent.window.opener.document.getElementById('cDocSuf'+ '<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                          parent.window.opener.document.getElementById('cDocSuf'+ '<?php echo $gSecuencia ?>').style.color = "#000000";
                          parent.window.opener.document.getElementById('cDocTip'+ '<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                          parent.window.opener.document.getElementById('cDocTip'+ '<?php echo $gSecuencia ?>').style.color = "#000000";
                          parent.window.opener.document.getElementById('cCliId' + '<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                          parent.window.opener.document.getElementById('cCliId' + '<?php echo $gSecuencia ?>').style.color = "#000000";
                          parent.window.opener.document.getElementById('cCliDv' + '<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                          parent.window.opener.document.getElementById('cCliDv' + '<?php echo $gSecuencia ?>').style.color = "#000000";
                          parent.window.opener.document.forms['frgrm']['cDocId' + '<?php echo $gSecuencia ?>'].focus();
                          parent.window.close()
                        </script>
                        <?php
                      }
                    break;
                  }
                ?>
              </form>
            </fieldset>
          </td>
        </tr>
      </table>
    </center>
  </body>
</html>
<?php } else {
  f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos, Verifique.");
} ?>