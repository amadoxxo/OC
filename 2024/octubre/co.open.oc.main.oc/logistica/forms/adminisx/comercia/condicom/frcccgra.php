<?php
  namespace openComex;
  include("../../../../../financiero/libs/php/utility.php");
  include("../../../../libs/php/uticclix.php");

  /**
   * Variable para saber si hay o no errores de validacion.
   *
   * @var number
   */
  $nSwitch = 0;

  /**
   * Variable para concatenar errores de validacion u otros.
   *
   * @var string
   */
  $cMsj = "\n";

  //Eliminando espacios en blanco en el id oferta comercial.
  $_POST['cCCoId'] = trim($_POST['cCCoId']);

  switch ($_COOKIE['kModo']) {
    case "NUEVO":
    case "EDITAR":
      //INICIO DE VALIDACIONES

      //Validando que oferta comercial sea alfanumerico y tenga guion
      if (!preg_match('/^[a-zA-Z0-9\-]+$/', $_POST['cCCoId'])) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El No. Oferta Comercial debe contener valores alfanumericos y guiones.\n";
      } else {
        //Validando que contenga hasta 15 caracteres de id Oferta Comercial.
        if (strlen($_POST['cCCoId']) < 1 || strlen($_POST['cCCoId']) > 15){
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El No. Oferta Comercial admite hasta 15 caracteres.\n";
        }
      }

      //Validando que no sea vacio el NIT del cliente.
      if ($_POST['cCliId'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Nit no puede ser vacio.\n";
      } else {
        $qClientes  = "SELECT cliidxxx ";
        $qClientes .= "FROM $cAlfa.lpar0150 ";
        $qClientes .= "WHERE ";
        $qClientes .= "cliidxxx = \"{$_POST['cCliId']}\" AND ";
        $qClientes .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xClientes  = f_MySql("SELECT","",$qClientes,$xConexion01,"");

        // Validando Nit Cliente exista
        if (mysql_num_rows($xClientes) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Nit ".$_POST['cCliId']." del cliente no existe.\n";
        }
      }

      //Validando que el Tipo sea diferente a SELECCIONE.
      if ($_POST['cCondCoTip'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Se debe seleccionar un Tipo.\n";
      }

      //Validando que el Cierre de Facturación sea diferente a SELECCIONE.
      if ($_POST['cCondCoCie'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Se debe seleccionar un Cierre de Facturacion.\n";
      }

      //Validando que la Fecha Vigencia Desde no sea vacia.
      if ($_POST['dDesde'] == "" || $_POST['dDesde'] == "0000-00-00") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Se debe seleccionar una Fecha Vigencia Desde.\n";
      }

      //Validando que la Fecha Vigencia Hasta no sea vacia.
      if ($_POST['dHasta'] == "" || $_POST['dHasta'] == "0000-00-00") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Se debe seleccionar una Fecha Vigencia Hasta.\n";
      }

      //Validando que la Fecha Vigencia Hasta sea mayor que la Fecha Vigencia Desde.
      if ($_POST['dHasta'] < $_POST['dDesde']) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La Fecha de Vigencia Hasta debe ser mayor que la Fecha Vigencia Desde.\n";
      }

      //Validando que el Tipo Incremento sea diferente a SELECCIONE.
      if ($_POST['cCondCoIn'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Se debe seleccionar un Tipo Incremento.\n";
      }

      //Validando que el campo Especifique no sea vacio cuando se ha seleccionado OTRO.
      if ($_POST['cCondCoIn'] =="OTRO" && $_POST['cCondCoOt'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Se debe Especificar el Tipo Incremento.\n";
      }

      if ($nSwitch == 0) {
        $qCondiCom  = "SELECT ccoidocx ";
        $qCondiCom .= "FROM $cAlfa.lpar0151 ";
        $qCondiCom .= "WHERE ";
        $qCondiCom .= "ccoidocx = \"{$_POST['cCCoId']}\" LIMIT 0,1";
        $xCondiCom  = f_MySql("SELECT","",$qCondiCom,$xConexion01,"");

        switch ($_COOKIE['kModo']) {
          case "NUEVO":
            // Validando No. oferta no exista
            if (mysql_num_rows($xCondiCom) > 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El No. Oferta Comercial ya existe en las Condiciones Comerciales.\n";
            }
          break;
          default:
            // Validando No. oferta exista
            if (mysql_num_rows($xCondiCom) == 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El No. Oferta Comercial NO existe en las Condiciones Comerciales.\n";
            }
          break;
        }
      }
    break;

    case "CAMBIAESTADO":
      if ($_POST['cCCoId'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El ID no puede ser vacio.\n";
      } else {
        // Valida que no tenga condiciones de servicio Activas
        $qCondiServ  = "SELECT cseidxxx ";
        $qCondiServ .= "FROM $cAlfa.lpar0152 ";
        $qCondiServ .= "WHERE ";
        $qCondiServ .= "ccoidocx = \"{$_POST['cCCoId']}\" AND ";
        $qCondiServ .= "regestxx = \"ACTIVO\" ";
        $xCondiServ  = f_MySql("SELECT","",$qCondiServ,$xConexion01,"");
        if (mysql_num_rows($xCondiServ) > 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "La Condicion Comercial [".$_POST['cCCoId']."] tiene Condiciones de Servicio en estado ACTIVO.\n";
        } else {
          $qCondiCom  = "SELECT ccoidocx, regestxx ";
          $qCondiCom .= "FROM $cAlfa.lpar0151 ";
          $qCondiCom .= "WHERE ";
          $qCondiCom .= "ccoidocx = \"{$_POST['cCCoId']}\"LIMIT 0,1";
          $xCondiCom  = f_MySql("SELECT","",$qCondiCom,$xConexion01,"");
  
          // Validando Codigo exista
          if (mysql_num_rows($xCondiCom) == 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Id no existe.\n";
          } else {
            $vCondiCom = mysql_fetch_array($xCondiCom);
            $cEstado = ($vCondiCom['regestxx'] == "ACTIVO") ? "INACTIVO" : "ACTIVO";
          }
        }
      }
    break;
  }

  //Empieza a grabar
  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "NUEVO":
        $qInsert	= array(array('NAME' => 'ccoidocx','VALUE' => trim($_POST['cCCoId'])         ,'CHECK' => 'SI'),             //Id Oferta Comercial         
                          array('NAME' => 'cliidxxx','VALUE' => trim($_POST['cCliId'])         ,'CHECK' => 'SI'),             //Id Cliente         
                          array('NAME' => 'ccotipxx','VALUE' => trim($_POST['cCondCoTip'])     ,'CHECK' => 'SI'),             //Tipo         
                          array('NAME' => 'ccociexx','VALUE' => trim($_POST['cCondCoCie'])     ,'CHECK' => 'SI'),             //Dia Cierre         
                          array('NAME' => 'ccofvdxx','VALUE' => trim($_POST['dDesde'])         ,'CHECK' => 'SI'),             //Fecha Vigencia Desde         
                          array('NAME' => 'ccofvhxx','VALUE' => trim($_POST['dHasta'])         ,'CHECK' => 'SI'),             //Fecha Vigencia Hasta         
                          array('NAME' => 'ccoincxx','VALUE' => trim($_POST['cCondCoIn'])      ,'CHECK' => 'SI'),             //Incremento         
                          array('NAME' => 'ccoincox','VALUE' => trim($_POST['cCondCoOt'])      ,'CHECK' => 'NO'),             //Incremento Otros         
                          array('NAME' => 'ccoobsxx','VALUE' => trim($_POST['cCondCoObs'])     ,'CHECK' => 'NO'),             //Observacion         
                          array('NAME' => 'regusrxx','VALUE' => trim($_COOKIE['kUsrId'])       ,'CHECK' => 'SI'),             //Usuario que Creo el Registro         
                          array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')						       ,'CHECK' => 'SI'),             //Fecha de Creacion del Registro         
                          array('NAME' => 'reghcrex','VALUE' => date('H:i:s')	                 ,'CHECK' => 'SI'),             //Hora de Creacion del Registro         
                          array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')	                 ,'CHECK' => 'SI'),             //Fecha de Modificacion del Registro         
                          array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                  ,'CHECK' => 'SI'),             //Hora de Modificacion del Registro         
                          array('NAME' => 'regestxx','VALUE' => "ACTIVO"                       ,'CHECK' => 'SI'));            //Estado del Registro          

        if (!f_MySql("INSERT","lpar0151",$qInsert,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al Guardar Datos.\n";
        }
      break;
      case "EDITAR":
        $qUpdate	= array(array('NAME' => 'cliidxxx','VALUE' => trim($_POST['cCliId'])                ,'CHECK' => 'SI'),      //Id Cliente   
                          array('NAME' => 'ccotipxx','VALUE' => trim($_POST['cCondCoTip'])            ,'CHECK' => 'SI'),      //Tipo   
                          array('NAME' => 'ccociexx','VALUE' => trim($_POST['cCondCoCie'])            ,'CHECK' => 'SI'),      //Dia Cierre   
                          array('NAME' => 'ccofvdxx','VALUE' => trim($_POST['dDesde'])                ,'CHECK' => 'SI'),      //Fecha Vigencia Desde   
                          array('NAME' => 'ccofvhxx','VALUE' => trim($_POST['dHasta'])                ,'CHECK' => 'SI'),      //Fecha Vigencia Hasta   
                          array('NAME' => 'ccoincxx','VALUE' => trim($_POST['cCondCoIn'])             ,'CHECK' => 'SI'),      //Incremento   
                          array('NAME' => 'ccoincox','VALUE' => trim($_POST['cCondCoOt'])             ,'CHECK' => 'NO'),      //Incremento Otros   
                          array('NAME' => 'ccoobsxx','VALUE' => trim($_POST['cCondCoObs'])            ,'CHECK' => 'NO'),      //Observacion   
                          array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))  ,'CHECK' => 'SI'),      //Usuario que edito el Registro    
                          array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')												  ,'CHECK' => 'SI'),      //Fecha de Modificacion del Registro  
                          array('NAME' => 'reghmodx','VALUE' => date('H:i:s')		                      ,'CHECK' => 'SI'),      //Hora de Modificacion del Registro  
                          array('NAME' => 'regestxx','VALUE' => trim(strtoupper($_POST['cEstado']))   ,'CHECK' => 'SI'),      //Estado del Registro  
                          array('NAME' => 'ccoidocx','VALUE' => trim($_POST['cCCoId'])                ,'CHECK' => 'WH'));     //Id Oferta Comercial   

          if (!f_MySql("UPDATE","lpar0151",$qUpdate,$xConexion01,$cAlfa)) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error al Actualizar Datos.\n";
          }
      break;
      case "CAMBIAESTADO":
          $qUpdate  = array(array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId'])),'CHECK'=>'SI'),         //Usuario que edito el Registro   
                            array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')												,'CHECK'=>'SI'),         //Fecha de Modificacion del Registro  
                            array('NAME' => 'reghmodx','VALUE' => date('H:i:s')		                    ,'CHECK'=>'SI'),         //Hora de Modificacion del Registro  
                            array('NAME' => 'regestxx','VALUE' => $cEstado                            ,'CHECK'=>'SI'),         //Estado del Registro  
                            array('NAME' => 'ccoidocx','VALUE' => trim($_POST['cCCoId'])              ,'CHECK' => 'WH'));      //Id Oferta Comercial 

          if (!f_MySql("UPDATE","lpar0151",$qUpdate,$xConexion01,$cAlfa)) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error al Cambiar el Estado.\n";
          }
      break;
    }
  }

  if ($nSwitch == 0) {
    if ($_COOKIE['kModo'] == "NUEVO") {
      f_Mensaje(__FILE__,__LINE__,"El Registro se Creo con Exito.");
    } elseif ($_COOKIE['kModo'] == "EDITAR") {
      f_Mensaje(__FILE__,__LINE__,"El Registro se Actualizo con Exito.");
    } elseif ($_COOKIE['kModo'] == "CAMBIAESTADO") {
      f_Mensaje(__FILE__,__LINE__,"El Registro Cambio de Estado con Exito.");
    }
    ?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
      <script languaje = "javascript">
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
        document.forms['frgrm'].submit()
      </script>
  <?php }

  if ($nSwitch == 1) {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.\n");
  }
?>