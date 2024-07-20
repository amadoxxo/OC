<?php
/**
 * Graba Proceso Autorización Modificar Campos Pedido.
 * Este programa guarda los conceptos que van a ser excluidos de la opción de menú Proceso Autorización Modificar Campos Pedido.
 * @author Elian Amado <elian.amado@openits.co>
 * @version 001
 */
  include("../../../../../financiero/libs/php/utility.php");


  $nSwitch = 0; // Switch para Vericar la Validacion de Datos
  $cMsj    = "";
  $cMsjAdv = "";
  
  switch ($_COOKIE['kModo']) {
    // case "NUEVO":
    // case "EDITAR":
    //   /***** Validando Sucursal del Do*****/
    //   if ($_POST['cSucId'] == "") {
    //     $nSwitch = 1;
    //     $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    //     $cMsj .= "La Sucursal del Do, no puede ser vacio \n ";
    //   }
      
    //   /***** Validando Numero Do*****/
    //   if ($_POST['cDocId'] == "") {
    //     $nSwitch = 1;
    //     $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    //     $cMsj .= "El Numero del Do, no puede ser vacio \n ";
    //   }
    //   //f_Mensaje(__FILE__,__LINE__,$_POST['cDocId']);
      
    //   /***** Validando Numero Do*****/
    //   if ($_POST['cDocSuf'] == "") {
    //     $nSwitch = 1;
    //     $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    //     $cMsj .= "El Sufijo del Do, no puede ser vacio \n ";
    //   }

    //   /***** Validando Nit del cliente *****/
    //   if ($_POST['cCliId'] == "") {
    //     $nSwitch = 1;
    //     $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    //     $cMsj .= "El Nit del Cliente no puede ser vacio \n ";
    //   }

    //   /***** Validando Nombre del cliente *****/
    //   if ($_POST['cCliNom'] == "") {
    //     $nSwitch = 1;
    //     $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    //     $cMsj .= "El Nombre del Cliente no puede ser vacio \n ";
    //   }
      
    //   /*****Validando que aplique al menos un concepto de cobro*****/
    //   if($_POST['cComMemo']==""){
    //     $nSwitch = 1;
    //     $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    //     $cMsj .= "Debe Escoger Por lo Menos Un Concepto de Cobro, Verifique \n";
    //   }

    //   /*****Validando que aplique al menos un concepto de cobro*****/
    //   $qTramites  = "SELECT sucidxxx, docidxxx, docsufxx, doctipxx, regestxx ";
    //   $qTramites .= "FROM $cAlfa.sys00121 ";
    //   $qTramites .= "WHERE ";
    //   $qTramites .= "sucidxxx  = \"{$_POST['cSucId']}\" AND ";
    //   $qTramites .= "docidxxx  = \"{$_POST['cDocId']}\" AND ";
    //   $qTramites .= "docsufxx  = \"{$_POST['cDocSuf']}\" AND ";
    //   $qTramites .= "regestxx != \"INACTIVO\" LIMIT 0,1 ";
    //   $xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
    //   // f_Mensaje(__FILE__,__LINE__,$qTramites." ~ ".mysql_num_rows($xTramites));
    //   if (mysql_num_rows($xTramites) == 0) {
    //     $nSwitch = 1;
    //     $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    //     $cMsj .= "El Do [{$_POST['cSucId']}-{$_POST['cDocId']}-{$_POST['cDocSuf']}] No Existe, o se encuentra en estado INACTIVO.\n";
    //   }
      
    //   if ( $_POST['cCcAplFa'] == "SI" ) {
    //     if ( $_POST['cTerIdInt'] == "") {
    //       $nSwitch = 1;
    //       $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    //       $cMsj .= " El Cliente tiene parametrizada en su Condicion Comercial la opcion \"Aplicar tarifas del Facturar a\", por favor seleccione el Facturar a.\n";
    //     } else {
    //       //Validando que el facturar a sea valido
    //       $qFacA  = "SELECT ";
    //       $qFacA .= "$cAlfa.SIAI0150.CLIIDXXX ";
    //       $qFacA .= "FROM $cAlfa.SIAI0150 ";
    //       $qFacA .= "WHERE ";
    //       $qFacA .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$_POST['cTerIdInt']}\" AND ";
    //       $qFacA .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1";
    //       $xFacA  = f_MySql("SELECT","",$qFacA,$xConexion01,"");
    //       if (mysql_num_rows($xFacA) == 0) {
    //         $nSwitch = 1;
    //         $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    //         $cMsj .= " El Facturar a No Exite o Se Encuentra Inactivo.\n";
    //       }
    //     }
    //   } else {
    //     $_POST['cTerIdInt'] = "";
    //   }
    // break;
  }

  /***** Ahora Empiezo a Grabar *****/
  /***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "NUEVO":
        $datos = true;
        for ($i=0; $i < $_POST['nSecuencia_Ip']; $i++) { 
          if ($_POST['cCheck'.($i+1)]) {
            $datos = false;
            $qInsert	 = array(
                array('NAME'=>'cliidxxx','VALUE'=>trim($_POST['cNIT'.($i+1)]),'CHECK'=>'NO'),
                array('NAME'=>'pedidxxx' ,'VALUE'=>trim(strtoupper($_POST['cPedId'.($i+1)]))      ,'CHECK'=>'NO'),
                array('NAME'=>'pedanoxx' ,'VALUE'=>trim(strtoupper($_POST['cAnioIds'.($i + 1)]))  ,'CHECK'=>'NO'),
                array('NAME'=>'pedcscxx' ,'VALUE'=>trim(strtoupper($_POST['cNumPedido'.($i+1)]))  ,'CHECK'=>'NO'),
                array('NAME'=>'sersapxx' ,'VALUE'=>trim(strtoupper($_POST['cCodSap'.($i+1)]))     ,'CHECK'=>'NO'),
                array('NAME'=>'subidxxx' ,'VALUE'=>trim(strtoupper($_POST['cSubCerId'.($i+1)]))   ,'CHECK'=>'NO'),
                array('NAME'=>'amcobsxx' ,'VALUE'=>trim(strtoupper($_POST['cObservacion'.($i+1)])),'CHECK'=>'NO'),
                array('NAME'=>'regusrxx' ,'VALUE'=>trim($_COOKIE['kUsrId']),'CHECK'=>'SI'),
                array('NAME'=>'regfcrex' ,'VALUE'=>date('Y-m-d') ,'CHECK'=>'SI'),
                array('NAME'=>'reghcrex' ,'VALUE'=>date('H:i:s') ,'CHECK'=>'SI'),
                array('NAME'=>'regfmodx' ,'VALUE'=>date('Y-m-d') ,'CHECK'=>'SI'),
                array('NAME'=>'reghmodx' ,'VALUE'=>date('H:i:s') ,'CHECK'=>'SI'),
                array('NAME'=>'regestxx' ,'VALUE'=> "ACTIVO"     ,'CHECK'=>'SI')
            );
            if (!f_MySql("INSERT","lpar0161",$qInsert,$xConexion01,$cAlfa)) {
              $nSwitch = 1;
              $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
              $cMsj .= "Error Guardando Datos.\n";
            }
          }
        }

        if ($datos) {
          $nSwitch = 1;
          $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
          $cMsj .= "Tiene que seleccionar minimo un subservicio.\n";
        }
      break;
      case "EDITAR":
        $pedidoId    = trim($_POST['cPedidoId']);
        $pedidoNom   = trim($_POST['cPedNom']);
        $pedidoAnio  = trim($_POST['cPedAnio']);
        $pedidoObser = trim($_POST['cPedObser']);
        $cNit        = trim($_POST['cNit']);
        $checks      = trim($_POST['cComMemo'], '|');

        if (!empty($checks)) {
          $qDelete = array(
            array('NAME' => 'pedidxxx', 'VALUE' => $pedidoId,   'CHECK' => 'WH'),
            array('NAME' => 'pedanoxx', 'VALUE' => $pedidoAnio, 'CHECK' => 'WH')
          );

          if (!f_MySql("DELETE","lpar0161",$qDelete,$xConexion01, $cAlfa)) {
            $nSwitch = 1;
            $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
            $cMsj .= "Error al Eliminar los Servicios de Pedidos " . $_POST['pedcscxx'] . ",\n";
          }

          $subServicios = explode('|', $checks);

          foreach ($subServicios as $subServicio) {

            $dataSer = explode('~',$subServicio);
            $sersapxx = $dataSer[1];
            $subSerId = $dataSer[0];

            $qInsert = array(
              array('NAME' => 'cliidxxx', 'VALUE' => $cNit,        'CHECK' => 'NO'),
              array('NAME' => 'pedidxxx', 'VALUE' => $pedidoId,    'CHECK' => 'NO'),
              array('NAME' => 'pedanoxx', 'VALUE' => $pedidoAnio,  'CHECK' => 'NO'),
              array('NAME' => 'pedcscxx', 'VALUE' => $pedidoNom,   'CHECK' => 'NO'),
              array('NAME' => 'sersapxx', 'VALUE' => $sersapxx,    'CHECK' => 'NO'),
              array('NAME' => 'subidxxx', 'VALUE' => $subSerId,    'CHECK' => 'NO'),
              array('NAME' => 'amcobsxx', 'VALUE' => $pedidoObser, 'CHECK' => 'NO'),
              array('NAME' => 'regusrxx', 'VALUE' => trim($_COOKIE['kUsrId']), 'CHECK' => 'SI'),
              array('NAME' => 'regfcrex', 'VALUE' => date('Y-m-d'), 'CHECK' => 'SI'),
              array('NAME' => 'reghcrex', 'VALUE' => date('H:i:s'), 'CHECK' => 'SI'),
              array('NAME' => 'regfmodx', 'VALUE' => date('Y-m-d'), 'CHECK' => 'SI'),
              array('NAME' => 'reghmodx', 'VALUE' => date('H:i:s'), 'CHECK' => 'SI'),
              array('NAME' => 'regestxx', 'VALUE' => "ACTIVO", 'CHECK' => 'SI'),
            );

            if (!f_MySql("INSERT", "lpar0161", $qInsert, $xConexion01, $cAlfa)) {
              $nSwitch = 1;
              $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
              $cMsj .= "Error Guardando Datos.\n";
            }
          }
        } else {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Tiene que seleccionar como minimo un subservicio ".$_POST['pedcscxx'].",\n";
        }
      break;
      case "ELIMINAR":
        $qDelete	 = array(array('NAME' => 'pedanoxx','VALUE' => trim(strtoupper($_POST['pedanoxx'])),'CHECK'=>'WH'),
                          array('NAME' => 'pedcscxx','VALUE' => trim(strtoupper($_POST['pedcscxx'])) ,'CHECK'=>'WH'));

        if (!f_MySql("DELETE","lpar0161",$qDelete,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al Eliminar el Serivcio de Pedido ".$_POST['pedcscxx'].",\n";
        }
      break;
    }
  } 

  if ($nSwitch == 0) {
    if($_COOKIE['kModo']=="NUEVO"){
      f_Mensaje(__FILE__,__LINE__,"El Registro se Creo Con Exito");
    }
    if($_COOKIE['kModo']=="EDITAR"){
      f_Mensaje(__FILE__,__LINE__,"El Registro se Actualizo Con Exito");
    }
    if($_COOKIE['kModo']=="ELIMINAR"){
      f_Mensaje(__FILE__,__LINE__,"El Registro se Elimino Con Exito");
    }
    
    ?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
    <script languaje = "javascript">
      parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      document.forms['frgrm'].submit()
    </script>
  <?php }
  
  if ($nSwitch == 1) {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique");
  } ?>