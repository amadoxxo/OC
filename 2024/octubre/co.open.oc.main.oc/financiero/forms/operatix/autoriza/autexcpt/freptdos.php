<?php 
  namespace openComex;

  /**
   * Valid/Window DO's
   * --- Descripcion: Permite Seleccionar uno o varios DO's
   * @author Johana Arboleda Ramos <johana.arboleda@opentecnologia.com.co>
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
      <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
      <script languaje = 'javascript'>
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
                  parent.window.opener.document.getElementById('cDocSeq<?php echo $gSecuencia ?>').style.background = "Red";
                  parent.window.opener.document.getElementById('cDocSeq<?php echo $gSecuencia ?>').style.color      = "#FFFFFF";
                  parent.window.opener.document.getElementById('cSucId<?php  echo $gSecuencia ?>').style.background = "Red";
                  parent.window.opener.document.getElementById('cSucId<?php  echo $gSecuencia ?>').style.color      = "#FFFFFF";
                  parent.window.opener.document.getElementById('cDocId<?php  echo $gSecuencia ?>').style.background = "Red";
                  parent.window.opener.document.getElementById('cDocId<?php  echo $gSecuencia ?>').style.color      = "#FFFFFF";
                  parent.window.opener.document.getElementById('cDocSuf<?php echo $gSecuencia ?>').style.background = "Red";
                  parent.window.opener.document.getElementById('cDocSuf<?php echo $gSecuencia ?>').style.color      = "#FFFFFF";
                  parent.window.opener.document.getElementById('cDocTip<?php echo $gSecuencia ?>').style.background = "Red";
                  parent.window.opener.document.getElementById('cDocTip<?php echo $gSecuencia ?>').style.color      = "#FFFFFF";
                  parent.window.opener.document.getElementById('cDosTip<?php echo $gSecuencia ?>').style.background = "Red";
                  parent.window.opener.document.getElementById('cDosTip<?php echo $gSecuencia ?>').style.color      = "#FFFFFF";
                  parent.window.opener.document.getElementById('cCliId<?php  echo $gSecuencia ?>').style.background = "Red";
                  parent.window.opener.document.getElementById('cCliId<?php  echo $gSecuencia ?>').style.color      = "#FFFFFF";
                  parent.window.opener.document.getElementById('cCliDv<?php  echo $gSecuencia ?>').style.background = "Red";
                  parent.window.opener.document.getElementById('cCliDv<?php  echo $gSecuencia ?>').style.color      = "#FFFFFF";
                  parent.window.opener.document.getElementById('cCliNom<?php echo $gSecuencia ?>').style.background = "Red";
                  parent.window.opener.document.getElementById('cCliNom<?php echo $gSecuencia ?>').style.color      = "#FFFFFF";
                } else {
                  parent.window.opener.document.getElementById('cDocSeq<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                  parent.window.opener.document.getElementById('cDocSeq<?php echo $gSecuencia ?>').style.color      = "#000000";
                  parent.window.opener.document.getElementById('cSucId<?php  echo $gSecuencia ?>').style.background = "#FFFFFF";
                  parent.window.opener.document.getElementById('cSucId<?php  echo $gSecuencia ?>').style.color      = "#000000";
                  parent.window.opener.document.getElementById('cDocId<?php  echo $gSecuencia ?>').style.background = "#FFFFFF";
                  parent.window.opener.document.getElementById('cDocId<?php  echo $gSecuencia ?>').style.color      = "#000000";
                  parent.window.opener.document.getElementById('cDocSuf<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                  parent.window.opener.document.getElementById('cDocSuf<?php echo $gSecuencia ?>').style.color      = "#000000";
                  parent.window.opener.document.getElementById('cDocTip<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                  parent.window.opener.document.getElementById('cDocTip<?php echo $gSecuencia ?>').style.color      = "#000000";
                  parent.window.opener.document.getElementById('cDosTip<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                  parent.window.opener.document.getElementById('cDosTip<?php echo $gSecuencia ?>').style.color      = "#000000";
                  parent.window.opener.document.getElementById('cCliId<?php  echo $gSecuencia ?>').style.background = "#FFFFFF";
                  parent.window.opener.document.getElementById('cCliId<?php  echo $gSecuencia ?>').style.color      = "#000000";
                  parent.window.opener.document.getElementById('cCliDv<?php  echo $gSecuencia ?>').style.background = "#FFFFFF";
                  parent.window.opener.document.getElementById('cCliDv<?php  echo $gSecuencia ?>').style.color      = "#000000";
                  parent.window.opener.document.getElementById('cCliNom<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                  parent.window.opener.document.getElementById('cCliNom<?php echo $gSecuencia ?>').style.color      = "#000000";
                }
              }
            break;
            default:
              if (parent.window.opener.document.forms['frgrm']['nSecuencia'].value == "<?php echo $gSecuencia ?>") { // Estoy en la ultima fila.
                var nSwPrv = 0; // Switch de Primera Vez
                for (i=0;i<document.forms['frgrm']['oChkTra'].length;i++){
                  if (document.forms['frgrm']['oChkTra'][i].checked == true) {
                    if (nSwPrv == 0) { // Si es la primera vez que entre cambio el estado del switch
                      nSwPrv = 1;
                    } else { // Si no es la primera vez que entro adiciono una row
                      parent.window.opener.f_Add_New_Row_Do();
                    }
                    
                    var mComCru = document.forms['frgrm']['oChkTra'][i].id.split("~");
                    parent.window.opener.document.forms['frgrm']['cSucId' + parent.window.opener.document.forms['frgrm']['nSecuencia'].value].value = mComCru[0]; 
                    parent.window.opener.document.forms['frgrm']['cDocId' + parent.window.opener.document.forms['frgrm']['nSecuencia'].value].value = mComCru[1]; 
                    parent.window.opener.document.forms['frgrm']['cDocSuf'+ parent.window.opener.document.forms['frgrm']['nSecuencia'].value].value = mComCru[2];
                    parent.window.opener.document.forms['frgrm']['cDocTip'+ parent.window.opener.document.forms['frgrm']['nSecuencia'].value].value = mComCru[3];
                    parent.window.opener.document.forms['frgrm']['cCliId' + parent.window.opener.document.forms['frgrm']['nSecuencia'].value].value = mComCru[4]; 
                    parent.window.opener.document.forms['frgrm']['cCliDv' + parent.window.opener.document.forms['frgrm']['nSecuencia'].value].value = mComCru[5]; 
                    parent.window.opener.document.forms['frgrm']['cCliNom'+ parent.window.opener.document.forms['frgrm']['nSecuencia'].value].value = mComCru[6];
                    if (mComCru[3] == "REGISTRO") {
                      parent.window.opener.document.getElementById('cDocSeq'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                      parent.window.opener.document.getElementById('cDocSeq'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color      = "#FFFFFF";
                      parent.window.opener.document.getElementById('cSucId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                      parent.window.opener.document.getElementById('cSucId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color      = "#FFFFFF";
                      parent.window.opener.document.getElementById('cDocId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                      parent.window.opener.document.getElementById('cDocId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color      = "#FFFFFF";
                      parent.window.opener.document.getElementById('cDocSuf'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                      parent.window.opener.document.getElementById('cDocSuf'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color      = "#FFFFFF";
                      parent.window.opener.document.getElementById('cDocTip'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                      parent.window.opener.document.getElementById('cDocTip'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color      = "#FFFFFF";
                      parent.window.opener.document.getElementById('cCliId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                      parent.window.opener.document.getElementById('cCliId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color      = "#FFFFFF";
                      parent.window.opener.document.getElementById('cCliDv' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                      parent.window.opener.document.getElementById('cCliDv' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color      = "#FFFFFF";
                      parent.window.opener.document.getElementById('cCliNom'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "Red";
                      parent.window.opener.document.getElementById('cCliNom'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color      = "#FFFFFF";
                    } else {
                      parent.window.opener.document.getElementById('cDocSeq'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                      parent.window.opener.document.getElementById('cDocSeq'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color      = "#000000";
                      parent.window.opener.document.getElementById('cSucId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                      parent.window.opener.document.getElementById('cSucId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color      = "#000000";
                      parent.window.opener.document.getElementById('cDocId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                      parent.window.opener.document.getElementById('cDocId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color      = "#000000";
                      parent.window.opener.document.getElementById('cDocSuf'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                      parent.window.opener.document.getElementById('cDocSuf'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color      = "#000000";
                      parent.window.opener.document.getElementById('cDocTip'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                      parent.window.opener.document.getElementById('cDocTip'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color      = "#000000";
                      parent.window.opener.document.getElementById('cCliId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                      parent.window.opener.document.getElementById('cCliId' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color      = "#000000";
                      parent.window.opener.document.getElementById('cCliDv' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                      parent.window.opener.document.getElementById('cCliDv' +parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color      = "#000000";
                      parent.window.opener.document.getElementById('cCliNom'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                      parent.window.opener.document.getElementById('cCliNom'+parent.window.opener.document.forms['frgrm']['nSecuencia'].value).style.color      = "#000000";
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
        
        function f_Marca(xRows) {
        if (document.forms['frgrm']['oChkTraAll'].checked == true){
          if (xRows == 1){
            document.forms['frgrm']['oChkTra'].checked=true;
          } else {
            if (xRows > 1){
              for (i=0;i<document.forms['frgrm']['oChkTra'].length;i++){
                document.forms['frgrm']['oChkTra'][i].checked = true;
              }
            }
          }
        } else {
          if (xRows == 1){
            document.forms['frgrm']['oChkTra'].checked=false;
          } else {
            if (xRows > 1){
              for (i=0;i<document.forms['frgrm']['oChkTra'].length;i++){
                document.forms['frgrm']['oChkTra'][i].checked = false;
              }
            }
          }
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
                      $qTramites .= "docidxxx LIKE \"%$gDocId%\" AND ";
                      $qTramites .= "doctexpt = \"\" AND ";
                      $qTramites .= "regestxx = \"ACTIVO\" ";
                      $qTramites .= "ORDER BY docidxxx ASC ";
                      $xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
                      // f_Mensaje(__FILE__,__LINE__,$qTramites." ~ ".mysql_num_rows($xTramites));
                      
                      if (mysql_num_rows($xTramites) == 1) {
                        
                        $vTramites = mysql_fetch_array($xTramites);
                        
                        //Busco la el nombre del cliente
                        $qDatCli  = "SELECT ";
                        $qDatCli .= "$cAlfa.SIAI0150.*, ";
                        $qDatCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
                        $qDatCli .= "FROM $cAlfa.SIAI0150 ";
                        $qDatCli .= "WHERE ";
                        $qDatCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vTramites['cliidxxx']}\" LIMIT 0,1";
                        $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
                        if(mysql_num_rows($xDatCli) > 0) {
                          $xRDC = mysql_fetch_array($xDatCli);
                          $vTramites['clinomxx'] = $xRDC['CLINOMXX'];
                        } else {
                          $vTramites['clinomxx'] = "CLIENTE SIN NOMBRE";
                        }
                        ?>
                      
                        <script language="javascript">
                          parent.fmwork.document.forms['frgrm']['cSucId' + "<?php echo $gSecuencia ?>"].value = "<?php echo $vTramites['sucidxxx']; ?>";
                          parent.fmwork.document.forms['frgrm']['cDocId' + "<?php echo $gSecuencia ?>"].value = "<?php echo $vTramites['docidxxx']; ?>"; 
                          parent.fmwork.document.forms['frgrm']['cDocSuf'+ "<?php echo $gSecuencia ?>"].value = "<?php echo $vTramites['docsufxx']; ?>";
                          parent.fmwork.document.forms['frgrm']['cDocTip'+ "<?php echo $gSecuencia ?>"].value = "<?php echo $vTramites['doctipxx']; ?>";
                          parent.fmwork.document.forms['frgrm']['cCliId' + "<?php echo $gSecuencia ?>"].value = "<?php echo $vTramites['cliidxxx']; ?>"; 
                          parent.fmwork.document.forms['frgrm']['cCliDv' + "<?php echo $gSecuencia ?>"].value = "<?php echo f_Digito_Verificacion($vTramites['cliidxxx']); ?>"; 
                          parent.fmwork.document.forms['frgrm']['cCliNom'+ "<?php echo $gSecuencia ?>"].value = "<?php echo $vTramites['clinomxx']; ?>";

                          if ("<?php echo $vTramites['doctipxx']; ?>" == "REGISTRO") {
                            parent.fmwork.document.getElementById('cDocSeq' + "<?php echo $gSecuencia ?>").style.background = "Red";
                            parent.fmwork.document.getElementById('cDocSeq' + "<?php echo $gSecuencia ?>").style.color = "#FFFFFF";
                            parent.fmwork.document.getElementById('cSucId' + "<?php echo $gSecuencia ?>").style.background = "Red";
                            parent.fmwork.document.getElementById('cSucId' + "<?php echo $gSecuencia ?>").style.color = "#FFFFFF";
                            parent.fmwork.document.getElementById('cDocId' + "<?php echo $gSecuencia ?>").style.background = "Red";
                            parent.fmwork.document.getElementById('cDocId' + "<?php echo $gSecuencia ?>").style.color = "#FFFFFF";
                            parent.fmwork.document.getElementById('cDocSuf'+ "<?php echo $gSecuencia ?>").style.background = "Red";
                            parent.fmwork.document.getElementById('cDocSuf'+ "<?php echo $gSecuencia ?>").style.color = "#FFFFFF";
                            parent.fmwork.document.getElementById('cDocTip'+ "<?php echo $gSecuencia ?>").style.background = "Red";
                            parent.fmwork.document.getElementById('cDocTip'+ "<?php echo $gSecuencia ?>").style.color = "#FFFFFF";
                            parent.fmwork.document.getElementById('cCliId' + "<?php echo $gSecuencia ?>").style.background = "Red";
                            parent.fmwork.document.getElementById('cCliId' + "<?php echo $gSecuencia ?>").style.color = "#FFFFFF";
                            parent.fmwork.document.getElementById('cCliDv' + "<?php echo $gSecuencia ?>").style.background = "Red";
                            parent.fmwork.document.getElementById('cCliDv' + "<?php echo $gSecuencia ?>").style.color = "#FFFFFF";
                            parent.fmwork.document.getElementById('cCliNom'+ "<?php echo $gSecuencia ?>").style.background = "Red";
                            parent.fmwork.document.getElementById('cCliNom'+ "<?php echo $gSecuencia ?>").style.color = "#FFFFFF";
                          } else {
                            parent.fmwork.document.getElementById('cDocSeq' + "<?php echo $gSecuencia ?>").style.background = "#FFFFFF";
                            parent.fmwork.document.getElementById('cDocSeq' + "<?php echo $gSecuencia ?>").style.color = "#000000";
                            parent.fmwork.document.getElementById('cSucId' + "<?php echo $gSecuencia ?>").style.background = "#FFFFFF";
                            parent.fmwork.document.getElementById('cSucId' + "<?php echo $gSecuencia ?>").style.color = "#000000";
                            parent.fmwork.document.getElementById('cDocId' + "<?php echo $gSecuencia ?>").style.background = "#FFFFFF";
                            parent.fmwork.document.getElementById('cDocId' + "<?php echo $gSecuencia ?>").style.color = "#000000";
                            parent.fmwork.document.getElementById('cDocSuf'+ "<?php echo $gSecuencia ?>").style.background = "#FFFFFF";
                            parent.fmwork.document.getElementById('cDocSuf'+ "<?php echo $gSecuencia ?>").style.color = "#000000";
                            parent.fmwork.document.getElementById('cDocTip'+ "<?php echo $gSecuencia ?>").style.background = "#FFFFFF";
                            parent.fmwork.document.getElementById('cDocTip'+ "<?php echo $gSecuencia ?>").style.color = "#000000";
                            parent.fmwork.document.getElementById('cCliId' + "<?php echo $gSecuencia ?>").style.background = "#FFFFFF";
                            parent.fmwork.document.getElementById('cCliId' + "<?php echo $gSecuencia ?>").style.color = "#000000";
                            parent.fmwork.document.getElementById('cCliDv' + "<?php echo $gSecuencia ?>").style.background = "#FFFFFF";
                            parent.fmwork.document.getElementById('cCliDv' + "<?php echo $gSecuencia ?>").style.color = "#000000";
                          }
                        </script>
                        <?php 
                      } else { ?>
                        <script language="javascript">
                          parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW","<?php echo $gSecuencia ?>");
                        </script>
                      <?php }
                    break;
                    case "WINDOW":
                      $qTramites  = "SELECT * ";
                      $qTramites .= "FROM $cAlfa.sys00121 ";
                      $qTramites .= "WHERE ";
                      $qTramites .= "docidxxx LIKE \"%$gDocId%\" AND ";
                      $qTramites .= "doctexpt = \"\" AND ";
                      $qTramites .= "regestxx = \"ACTIVO\" ";
                      $qTramites .= "ORDER BY docidxxx ASC ";
                      $xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
                      // f_Mensaje(__FILE__,__LINE__,$qTramites." ~ ".mysql_num_rows($xTramites));
                      
                      if (mysql_num_rows($xTramites) > 0) { ?>
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
                            <td width = "020" Class = "name"><center><input type="checkbox" name="oChkTraAll" onClick = "javascript:f_Marca('<?php echo mysql_num_rows($xTramites) ?>')"></center></td>
                          </tr>
                          <?php while ($xRT = mysql_fetch_array($xTramites)) {
                            //Busco la el nombre del cliente
                            $qDatCli  = "SELECT ";
                            $qDatCli .= "$cAlfa.SIAI0150.*, ";
                            $qDatCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
                            $qDatCli .= "FROM $cAlfa.SIAI0150 ";
                            $qDatCli .= "WHERE ";
                            $qDatCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$xRT['cliidxxx']}\" LIMIT 0,1";
                            $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
                            if(mysql_num_rows($xDatCli) > 0) {
                              $xRDC = mysql_fetch_array($xDatCli);
                              $xRT['clinomxx'] = $xRDC['CLINOMXX'];
                            } else {
                              $xRT['clinomxx'] = "CLIENTE SIN NOMBRE";
                            }
                            
                            $cColor = ($xRT['doctipxx'] == "REGISTRO") ? "#DDDDDD" : "#FFFFFF"; ?>
                            <tr bgcolor="<?php echo $cColor ?>">
                              <td class= "name" align="center"><?php echo $xRT['sucidxxx'] ?></td>
                              <td class= "name" align="center"><?php echo $xRT['docidxxx'] ?></td>
                              <td class= "name" align="center"><?php echo $xRT['docsufxx'] ?></td>
                              <td class= "name" align="center"><?php echo $xRT['doctipxx'] ?></td>
                              <td Class = "name" align="center"><?php echo $xRT['cliidxxx'] ?></td>
                              <td Class = "name"><?php echo substr(utf8_encode($xRT['clinomxx']),0,20) ?></td>
                              <td class= "name" align="center"><?php echo $xRT['regestxx'] ?></td>
                              <td class= "name" style = "text-align:right">
                                <input type="checkbox" name="oChkTra" value = "<?php echo $i ?>"
                                  id = "<?php echo $xRT['sucidxxx']."~".
                                                   $xRT['docidxxx']."~".
                                                   $xRT['docsufxx']."~".
                                                   $xRT['doctipxx']."~".
                                                   $xRT['cliidxxx']."~".
                                                   f_Digito_Verificacion($xRT['cliidxxx'])."~".
                                                   $xRT['clinomxx']; ?>">
                              </td>
                            </tr>
                          <?php } ?>
                          <tr>
                            <td colspan="8">
                              <center>
                                <input type="button" name="Btn_Aceptar" value = "Aceptar" style="width:100;text-align:center"
                                      onclick="javascript:f_Carga_Grilla('<?php echo count($mTramites) ?>');" readonly>
                                <input type="button" name="Btn_Salir"   value = "Salir"   style="width:100;text-align:center"
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