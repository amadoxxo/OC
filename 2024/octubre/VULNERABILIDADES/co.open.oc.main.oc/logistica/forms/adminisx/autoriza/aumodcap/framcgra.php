<?php
namespace openComex;
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
  
  switch ($_COOKIE['kModo']) {}
  /***** Ahora Empiezo a Grabar *****/
  /***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "NUEVO":
        $datos = true;
        for ($i=0; $i < $_POST['nSecuencia_Ip']; $i++) { 
          if ($_POST['cCheck'.($i+1)]) {
            $datos = false;
            $qInsert = array(array('NAME'=>'cliidxxx','VALUE'=>trim($_POST['cNIT'.($i+1)])                     ,'CHECK'=>'NO'),
                             array('NAME'=>'pedidxxx' ,'VALUE'=>trim(strtoupper($_POST['cPedId'.($i+1)]))      ,'CHECK'=>'NO'),
                             array('NAME'=>'pedanoxx' ,'VALUE'=>trim(strtoupper($_POST['cAnioIds'.($i + 1)]))  ,'CHECK'=>'NO'),
                             array('NAME'=>'pedcscxx' ,'VALUE'=>trim(strtoupper($_POST['cNumPedido'.($i+1)]))  ,'CHECK'=>'NO'),
                             array('NAME'=>'sersapxx' ,'VALUE'=>trim(strtoupper($_POST['cCodSap'.($i+1)]))     ,'CHECK'=>'NO'),
                             array('NAME'=>'subidxxx' ,'VALUE'=>trim(strtoupper($_POST['cSubCerId'.($i+1)]))   ,'CHECK'=>'NO'),
                             array('NAME'=>'amcobsxx' ,'VALUE'=>trim(strtoupper($_POST['cObservacion'.($i+1)])),'CHECK'=>'NO'),
                             array('NAME'=>'regusrxx' ,'VALUE'=>trim($_COOKIE['kUsrId'])                       ,'CHECK'=>'SI'),
                             array('NAME'=>'regfcrex' ,'VALUE'=>date('Y-m-d')                                  ,'CHECK'=>'SI'),
                             array('NAME'=>'reghcrex' ,'VALUE'=>date('H:i:s')                                  ,'CHECK'=>'SI'),
                             array('NAME'=>'regfmodx' ,'VALUE'=>date('Y-m-d')                                  ,'CHECK'=>'SI'),
                             array('NAME'=>'reghmodx' ,'VALUE'=>date('H:i:s')                                  ,'CHECK'=>'SI'),
                             array('NAME'=>'regestxx' ,'VALUE'=> "ACTIVO"                                      ,'CHECK'=>'SI'));
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
          $qDelete = array(array('NAME' => 'pedidxxx', 'VALUE' => $pedidoId,   'CHECK' => 'WH'),
                           array('NAME' => 'pedanoxx', 'VALUE' => $pedidoAnio, 'CHECK' => 'WH'));
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

            $qInsert = array(array('NAME' => 'cliidxxx', 'VALUE' => $cNit,                    'CHECK' => 'NO'),
                             array('NAME' => 'pedidxxx', 'VALUE' => $pedidoId,                'CHECK' => 'NO'),
                             array('NAME' => 'pedanoxx', 'VALUE' => $pedidoAnio,              'CHECK' => 'NO'),
                             array('NAME' => 'pedcscxx', 'VALUE' => $pedidoNom,               'CHECK' => 'NO'),
                             array('NAME' => 'sersapxx', 'VALUE' => $sersapxx,                'CHECK' => 'NO'),
                             array('NAME' => 'subidxxx', 'VALUE' => $subSerId,                'CHECK' => 'NO'),
                             array('NAME' => 'amcobsxx', 'VALUE' => $pedidoObser,             'CHECK' => 'NO'),
                             array('NAME' => 'regusrxx', 'VALUE' => trim($_COOKIE['kUsrId']), 'CHECK' => 'SI'),
                             array('NAME' => 'regfcrex', 'VALUE' => date('Y-m-d'),            'CHECK' => 'SI'),
                             array('NAME' => 'reghcrex', 'VALUE' => date('H:i:s'),            'CHECK' => 'SI'),
                             array('NAME' => 'regfmodx', 'VALUE' => date('Y-m-d'),            'CHECK' => 'SI'),
                             array('NAME' => 'reghmodx', 'VALUE' => date('H:i:s'),            'CHECK' => 'SI'),
                             array('NAME' => 'regestxx', 'VALUE' => "ACTIVO",                 'CHECK' => 'SI'));
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
        $qDelete	 = array(array('NAME' => 'pedanoxx','VALUE' => trim(strtoupper($_POST['pedanoxx'])) ,'CHECK'=>'WH'),
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
