<?php
  namespace openComex;
  /**
   * Grillas para el formulario de Condiciones de Servicio.
   * --- Descripcion: Permite cargar las grillas en el formulario.
   * @author juan.trujillo@openits.co
   * @package opencomex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");

  $vTipos = array();

  //esta enviando un solo caso
  if (isset($gTipo)) {
    $vTipos[0] = $gTipo;
  }

  //Esta enviando varios casos
  if (isset($gParametro)) {
    $mAux = f_Explode_Array($gParametro,"|","^");
    for($i=0; $i<count($mAux);$i++) {
      if ($mAux[$i][0] != "") {
        switch ($mAux[$i][0]) {
          case "1";
            $vTipos[]    = $mAux[$i][0];
            $gCseSubServ = $mAux[$i][1];
          break;
          case "2";
            $vTipos[]     = $mAux[$i][0];
            $gCseOrgVenta = $mAux[$i][1];
          break;
          default:
            //No hace nada
          break;
        }
      }
    }
  }

  // Se obtiene el codigo de las Oficinas de venta seleccionadas
  $vCseOfiVenta = explode("|", $gCseOfiVenta);

  //Recorriendo todos los casos
  for ($nT=0; $nT<count($vTipos);$nT++) {
	  switch ($vTipos[$nT]) {
		  case "1": //SUBSERVICIO
        $cTexto  = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"570\">";
          $cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\">";
            $cTexto .= "<td Class = \"clase08\" width = \"20\" ><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_create-dir_bg.gif\" onClick =\"javascript:fnLinks(\'cSubservicios\',\'VALID\')\" style = \"cursor:hand\" alt=\"Adicionar Subservicios\" >" : "")."</center></td>";
            $cTexto .= "<td Class = \"clase08\" width = \"150\" style=\"padding-left:5px\">Id</td>";
            $cTexto .= "<td Class = \"clase08\" width = \"400\" style=\"padding-left:5px\">Descripci&oacute;n</td>";
          $cTexto .= "</tr>";
          //Primero Cargo una Matriz con los Clientes
          if ($gCseSubServ != "") {
            $mMatrizInt = explode("~",$gCseSubServ);

            $cadena = '';
            $y = 0;
            for ($i=0;$i<count($mMatrizInt);$i++) {
              if ($mMatrizInt[$i] != "") {

                $qSubServ  = "SELECT ";
                $qSubServ .= "sersapxx, ";
                $qSubServ .= "subidxxx, ";
                $qSubServ .= "subdesxx ";
                $qSubServ .= "FROM $cAlfa.lpar0012 ";
                $qSubServ .= "WHERE ";
                $qSubServ .= "sersapxx = \"$gSerSap\" AND ";
                $qSubServ .= "subidxxx = \"{$mMatrizInt[$i]}\" AND ";
                $qSubServ .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                $xSubServ = f_MySql("SELECT","",$qSubServ,$xConexion01,"");
    
                if (mysql_num_rows($xSubServ) > 0) {							
                  while ($xRSS = mysql_fetch_array($xSubServ)) {
                    $y ++;
    
                    $cId 	= $xRSS['subidxxx'];
                    $zColor = "{$vSysStr['system_row_impar_color_ini']}";
                    if($y % 2 == 0) {
                      $zColor = "{$vSysStr['system_row_par_color_ini']}";
                    }
                    $cTexto .= "<tr bgcolor = \"$zColor\" onmouseover=\"javascript:uRowColor(this,\'".$vSysStr['system_row_select_color_ini']."\')\" onmouseout=\"javascript:uRowColor(this,\'$zColor\')\">";
                      $cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:fnEliminarSubservicio(\'$cId\')\" style = \"cursor:hand\" alt=\"Borrar Subservicio: ".$mMatrizInt[$i]." - ".substr($xRSS['subdesxx'],0,60)."\">" : "")."</center></td>";
                      $cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">".substr($xRSS['subidxxx'],0,10)."</td>";
                      $cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">".substr($xRSS['subdesxx'],0,60)."</td>";
                    $cTexto .= "</tr>";
                  }
                }
              }
            }
          }
			  $cTexto .= "</table>"; ?>  
        <script languaje = "javascript">
          parent.fmwork.document.getElementById('overDivSubServ').innerHTML = '<?php echo $cTexto ?>';
        </script>
      <?php break;
      case "2": //ORGANIZACION DE VENTAS
        $cTexto  = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"570\">";
          $cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\">";
            $cTexto .= "<td Class = \"clase08\" width = \"20\" ><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_create-dir_bg.gif\" onClick =\"javascript:fnLinks(\'cOrganizacionVenta\',\'VALID\')\" style = \"cursor:hand\" alt=\"Adicionar Organizaci&oacute;n de Venta\" >" : "")."</center></td>";
            $cTexto .= "<td Class = \"clase08\" width = \"150\" style=\"padding-left:5px\">Id</td>";
            $cTexto .= "<td Class = \"clase08\" width = \"400\" style=\"padding-left:5px\">Descripci&oacute;n</td>";
          $cTexto .= "</tr>";

          //Primero Cargo una Matriz con los Clientes
          if ($gCseOrgVenta != "") {
            $mMatrizInt = explode("~",$gCseOrgVenta);

            $cadena = '';
            $y = 0;
            for ($i=0;$i<count($mMatrizInt);$i++) {
              if ($mMatrizInt[$i] != "") {
                $qOrgVenta  = "SELECT ";
                $qOrgVenta .= "orvsapxx, ";
                $qOrgVenta .= "orvdesxx ";
                $qOrgVenta .= "FROM $cAlfa.lpar0001 ";
                $qOrgVenta .= "WHERE ";
                $qOrgVenta .= "orvsapxx = \"{$mMatrizInt[$i]}\" AND ";
                $qOrgVenta .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                $xOrgVenta = f_MySql("SELECT","",$qOrgVenta,$xConexion01,"");
    
                if (mysql_num_rows($xOrgVenta) > 0) {							
                  while ($xROV = mysql_fetch_array($xOrgVenta)) {
                    $y ++;
    
                    $cId 	= $xROV['orvsapxx'];
                    $zColor = "{$vSysStr['system_row_impar_color_ini']}";
                    if($y % 2 == 0) {
                      $zColor = "{$vSysStr['system_row_par_color_ini']}";
                    }
                    $cTexto .= "<tr bgcolor = \"$zColor\" onmouseover=\"javascript:uRowColor(this,\'".$vSysStr['system_row_select_color_ini']."\')\" onmouseout=\"javascript:uRowColor(this,\'$zColor\')\">";
                      $cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:fnEliminarOrganizacionVenta(\'$cId\')\" style = \"cursor:hand\" alt=\"Borrar Organizacion de Venta: ".$mMatrizInt[$i]." - ".substr($xROV['orvdesxx'],0,60)."\">" : "")."</center></td>";
                      $cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">".substr($xROV['orvsapxx'],0,10)."</td>";
                      $cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">".substr($xROV['orvdesxx'],0,60)."</td>";
                    $cTexto .= "</tr>";
                    ?>
                    <script languaje = "javascript">
                      var cNameInput = 'cCseOfiVenta_'+'<?php echo $xROV['orvsapxx'] ?>';
                      if (parent.fmwork.document.getElementById(cNameInput) === null) {
                        var container  = parent.fmwork.document.querySelector(".idInputOficinasVentas");
                        var cTextArea  = document.createElement("textarea");
                        cTextArea.name = cNameInput;
                        cTextArea.id   = cNameInput;
                        container.appendChild(cTextArea);

                        parent.fmwork.document.getElementById(cNameInput).style.display = "none";
                      }
                    </script>
                    <?php
                  }
                }
              }
            }
          }
			  $cTexto .= "</table>"; ?>
        <script languaje = "javascript">
          parent.fmwork.document.getElementById('overDivOrgVenta').innerHTML = '<?php echo $cTexto ?>';
          parent.fmwork.fnCargarOficinaVentas();
        </script>
      <?php break;
      case "3": //OFICINA DE VENTAS
        $cTexto = "";
        if ($gCseOrgVenta != "") {
          $mOrgVentas = explode("~",$gCseOrgVenta);
          for ($j=0; $j<count($mOrgVentas);$j++) {
            $gCseOfiVenta = $vCseOfiVenta[$j];

            $cTexto .= "<fieldset id = \"".$mOrgVentas[$j]."\">";
              $cTexto .= "<legend>Oficinas de Ventas</legend>";
              $cTexto .= "<p><b>Organizaci&oacute;n de Venta: </b>".$mOrgVentas[$j]."</p><br>";

              $cTexto .= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"570\">";
                $cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\">";
                  $cTexto .= "<td Class = \"clase08\" width = \"20\" ><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_create-dir_bg.gif\" onClick =\"javascript:fnLinks(\'cOficinanVenta\',\'VALID\',\'".$mOrgVentas[$j]."\')\" style = \"cursor:hand\" alt=\"Adicionar Organizaci&oacute;n de Venta\" >" : "")."</center></td>";
                  $cTexto .= "<td Class = \"clase08\" width = \"150\" style=\"padding-left:5px\">Id</td>";
                  $cTexto .= "<td Class = \"clase08\" width = \"400\" style=\"padding-left:5px\">Descripci&oacute;n</td>";
                $cTexto .= "</tr>";
                //Primero Cargo una Matriz con los Clientes

                if ($gCseOfiVenta != "") {
                  $mMatrizInt = explode("~",$gCseOfiVenta);
                                      
                  $cadena = '';
                  $y = 0;
                  for ($i=0;$i<count($mMatrizInt);$i++) {
                    if ($mMatrizInt[$i] != "") {

                      $qOrgVenta  = "SELECT ";
                      $qOrgVenta .= "orvsapxx, ";
                      $qOrgVenta .= "ofvsapxx, ";
                      $qOrgVenta .= "ofvdesxx ";
                      $qOrgVenta .= "FROM $cAlfa.lpar0002 ";
                      $qOrgVenta .= "WHERE ";
                      $qOrgVenta .= "orvsapxx = \"{$mOrgVentas[$j]}\" AND ";
                      $qOrgVenta .= "ofvsapxx = \"{$mMatrizInt[$i]}\" AND ";
                      $qOrgVenta .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                      $xOrgVenta = f_MySql("SELECT","",$qOrgVenta,$xConexion01,"");
          
                      if (mysql_num_rows($xOrgVenta) > 0) {							
                        while ($xROV = mysql_fetch_array($xOrgVenta)) {
                          $y ++;
          
                          $cIdOfi = $xROV['ofvsapxx'];
                          $cIdOrg = $xROV['orvsapxx'];
                          $zColor = "{$vSysStr['system_row_impar_color_ini']}";
                          if($y % 2 == 0) {
                            $zColor = "{$vSysStr['system_row_par_color_ini']}";
                          }
                          $cTexto .= "<tr bgcolor = \"$zColor\" onmouseover=\"javascript:uRowColor(this,\'".$vSysStr['system_row_select_color_ini']."\')\" onmouseout=\"javascript:uRowColor(this,\'$zColor\')\">";
                            $cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:fnEliminarOficiaVenta(\'$cIdOfi\',\'$cIdOrg\')\" style = \"cursor:hand\" alt=\"Borrar Organizacion de Venta: ".$mMatrizInt[$i]." - ".substr($xROV['ofvdesxx'],0,60)."\">" : "")."</center></td>";
                            $cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">".substr($xROV['ofvsapxx'],0,10)."</td>";
                            $cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">".substr($xROV['ofvdesxx'],0,60)."</td>";                  
                          $cTexto .= "</tr>";
                        }
                      }
                    }
                  }
                }
              $cTexto .= "</table>";
            $cTexto .= "</fieldset>";
          }
        } ?>
        <script languaje = "javascript">
          parent.fmwork.document.getElementById('idOficinasVentas').innerHTML = '<?php echo $cTexto ?>';
        </script>
      <?php break;
      default:
		    //No Hace Nada
		  break;
    }
  }