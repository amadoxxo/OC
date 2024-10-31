<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  
  $nSwitch = 0; // Switch para Vericar la Validacion de Datos
  $cMsj    = "";
  
  switch ($_COOKIE['kModo']) {
    case "NUEVO":
      $mDos = array();
      for ($i=0; $i<$_POST['nSecuencia']; $i++) {
        if ($_POST['cSucId' .($i+1)] != "" && $_POST['cDocId' .($i+1)] != "" && $_POST['cDocSuf' .($i+1)] != "") {
          $qTramites  = "SELECT sucidxxx, docidxxx, docsufxx, doctipxx, regestxx ";
          $qTramites .= "FROM $cAlfa.sys00121 ";
          $qTramites .= "WHERE ";
          $qTramites .= "sucidxxx = \"{$_POST['cSucId' .($i+1)]}\" AND ";
          $qTramites .= "docidxxx = \"{$_POST['cDocId' .($i+1)]}\" AND ";
          $qTramites .= "docsufxx = \"{$_POST['cDocSuf'.($i+1)]}\" AND ";
          $qTramites .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
          $xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
          // f_Mensaje(__FILE__,__LINE__,$qTramites." ~ ".mysql_num_rows($xTramites));
          if (mysql_num_rows($xTramites) == 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Do [{$_POST['cSucId' .($i+1)]}-{$_POST['cDocId' .($i+1)]}-{$_POST['cDocSuf' .($i+1)]}] No Existe, o se encuentra en estado FACTURADO o INACTIVO.\n";
          }else {
            $nInd_mDos = count($mDos);
            $mDos[$nInd_mDos]['cSucId']  = $_POST['cSucId' .($i+1)];
            $mDos[$nInd_mDos]['cDocId']  = $_POST['cDocId' .($i+1)];
            $mDos[$nInd_mDos]['cDocSuf'] = $_POST['cDocSuf'.($i+1)];
          } 
        } else {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Debe Selecionar un Do en la Secuencia {$_POST['cDocSeq' .($i+1)]}.\n";
        }
      } // for ($i=0; $i<$_POST['nSecuencia']; $i++) {
      
    break;
    default:
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Modo de Grabado Viene Vacio";
    break;
  }
  /***** Ahora Empiezo a Grabar *****/
      
  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {			
      //f_Mensaje(__FILE__,__LINE__,$_COOKIE['kModo'])
      /*****************************   UPDATE    ***********************************************/
      case "NUEVO":
        foreach ($mDos as $Do) {
          $qInsert = array(array('NAME'=>'sucidxxx','VALUE'=>$Do['cSucId'],      'CHECK'=>'SI'),
                           array('NAME'=>'docidxxx','VALUE'=>$Do['cDocId'],      'CHECK'=>'SI'),
                           array('NAME'=>'docsufxx','VALUE'=>$Do['cDocSuf'],     'CHECK'=>'SI'),
                           array('NAME'=>'obstipox','VALUE'=>'AUTORIZACION_PCC', 'CHECK'=>'SI'),
                           array('NAME'=>'obsobsxx','VALUE'=>$_POST['cObs'],     'CHECK'=>'SI'),
                           array('NAME'=>'regusrxx','VALUE'=>$kUser,             'CHECK'=>'SI'),
                           array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d'),      'CHECK'=>'SI'),
                           array('NAME'=>'reghcrex','VALUE'=>date('H:i'),        'CHECK'=>'SI'),
                           array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d'),      'CHECK'=>'SI'),
                           array('NAME'=>'reghmodx','VALUE'=>date('H:i'),        'CHECK'=>'SI'),
                           array('NAME'=>'regestxx','VALUE'=>'ACTIVO',           'CHECK'=>'SI'));

          if (f_MySql("INSERT","fdob0000",$qInsert,$xConexion01,$cAlfa)) {
            //No hace Nada         
          } else {
            $cMsjAdv .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsjAdv .= "Error al Crear el Registro en la Tabla fdob0000 para el Do [{$Do['cSucId']}-{$Do['cDocId']}-{$Do['cDocSuf']}].\n";
          }

          // Actualiza los datos generales del DO
          $mUpdate = array(array('NAME'=>'docautor','VALUE'=>"SI"           ,'CHECK'=>'SI'),
                           array('NAME'=>'sucidxxx','VALUE'=>$Do['cSucId']  ,'CHECK'=>'WH'),
                           array('NAME'=>'docidxxx','VALUE'=>$Do['cDocId']  ,'CHECK'=>'WH'),
                           array('NAME'=>'docsufxx','VALUE'=>$Do['cDocSuf'] ,'CHECK'=>'WH'));
          if (!f_MySql("UPDATE","sys00121",$mUpdate,$xConexion01,$cAlfa)){
            $nSwitch = 1;
            $cMsjAdv .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsjAdv .= "Error al actualizar el campos de Autorizacion en datos generales del DO.\n";
          }
        }
      break;
    }
  }  
  
  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "NUEVO":
        f_Mensaje(__FILE__,__LINE__,"Se Realizo la Autorizacion de Pagos a Terceros.\n\n".$cMsjAdv);
      break;
      default:
        f_Mensaje(__FILE__,__LINE__,$cMsj);
      break;
    }
    ?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
    <script languaje = "javascript">
      parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      document.forms['frgrm'].submit()
    </script>
    <?php
  }
  
  if ($nSwitch == 1) {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique");
  }
?>