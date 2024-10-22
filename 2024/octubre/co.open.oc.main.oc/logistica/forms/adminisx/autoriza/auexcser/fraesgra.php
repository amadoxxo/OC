<?php
namespace openComex;
/**
 * Graba Orgainzacion de Ventas.
 * --- Descripcion: Permite Guardar una Nueva Autorizacion de exclusion de serivcio.
 * @author cristian.perdomo@openits.co@openits.co
 * @package openComex
 * @version 001
 */
include("../../../../../financiero/libs/php/utility.php");

$nSwitch = "0"; // Switch para Vericar la Validacion de Datos
$cMsj = "";

switch ($_COOKIE['kModo']) {
  case "NUEVO":
  case "EDITAR":
  break;
}	/***** Fin de la Validacion *****/

/***** Ahora Empiezo a Grabar *****/
/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
if ($nSwitch == 0) {
  switch ($_COOKIE['kModo']) {
    case "NUEVO":
      
      $checkVacio = true;
      for ($i = 0; $i < $_POST['nSecuencia_Ip']; $i++) {
    
        // Verifica si el nombre del checkbox está en el array $checkValues
        if ($_POST['cCheck'. ($i+1)]) {
          $checkVacio = false;

            $qInsert = array(
                array('NAME' => 'cliidxxx', 'VALUE' => trim($_POST['cNit' . ($i + 1)]),                       'CHECK' => 'NO'),
                array('NAME' => 'ceridxxx', 'VALUE' => trim(strtoupper($_POST['cCertiId' . ($i + 1)])),       'CHECK' => 'NO'),
                array('NAME' => 'ceranoxx', 'VALUE' => trim(strtoupper($_POST['cAnioCer' . ($i + 1)])),       'CHECK' => 'NO'),
                array('NAME' => 'cercscxx', 'VALUE' => trim(strtoupper($_POST['cCertificacion' . ($i + 1)])), 'CHECK' => 'NO'),
                array('NAME' => 'sersapxx', 'VALUE' => trim(strtoupper($_POST['cCodSap' . ($i + 1)])),        'CHECK' => 'NO'),
                array('NAME' => 'subidxxx', 'VALUE' => trim(strtoupper($_POST['cSubCerId' . ($i + 1)])),      'CHECK' => 'NO'),
                array('NAME' => 'aesobsxx', 'VALUE' => trim(strtoupper($_POST['cObservacion' . ($i + 1)])),   'CHECK' => 'NO'),
                array('NAME' => 'regusrxx', 'VALUE' => trim($_COOKIE['kUsrId']),                              'CHECK' => 'SI'),
                array('NAME' => 'regfcrex', 'VALUE' => date('Y-m-d'),                                         'CHECK' => 'SI'),
                array('NAME' => 'reghcrex', 'VALUE' => date('H:i:s'),                                         'CHECK' => 'SI'),
                array('NAME' => 'regfmodx', 'VALUE' => date('Y-m-d'),                                         'CHECK' => 'SI'),
                array('NAME' => 'reghmodx', 'VALUE' => date('H:i:s'),                                         'CHECK' => 'SI'),
                array('NAME' => 'regestxx', 'VALUE' => "ACTIVO",                                              'CHECK' => 'SI')
            );
    
            // Ejecutar la inserción si la función no falla
            if (!f_MySql("INSERT", "lpar0160", $qInsert, $xConexion01, $cAlfa)) {
                $nSwitch = 1;
                $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
                $cMsj .= "Error Guardando Datos.\n";
            }
        }
      }

      if ($checkVacio) {
          $nSwitch = 1;
          $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
          $cMsj .= "Debe seleccionar por lo menos un servicio.\n";
      }

    break;
    case "EDITAR":
      /***** Fin de Validaciones Particulares *****/
      $certificadoId = trim($_POST['cCertiId']);
      $certificadoNom = trim($_POST['cCerNomc']);
      $certificadoAno = trim($_POST['cCerAno']);
      $certificadoObser = trim($_POST['cCerObser']);
      $cNit = trim($_POST['cNit']);
      $checks = trim($_POST['cComMemo'], '|');

      if (!empty($checks)) {
        $qDelete = array(array('NAME' => 'ceridxxx', 'VALUE' => $certificadoId, 'CHECK' => 'WH'),
                        array('NAME' => 'ceranoxx', 'VALUE' => $certificadoAno, 'CHECK' => 'WH'));
        if (!f_MySql("DELETE", "lpar0160", $qDelete, $xConexion01, $cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
          $cMsj .= "Error al Eliminar los Servicios de Certificación " . $_POST['cercscxx'] . ",\n";
        }

        $subServicios = explode('|', $checks);
        foreach ($subServicios as $subServicio) {
          $dataSer = explode('~',$subServicio);
          $sersapxx = $dataSer[1];
          $subSerId = $dataSer[0];

          $qInsert = array(array('NAME' => 'cliidxxx', 'VALUE' => $cNit,                    'CHECK' => 'NO'),
                            array('NAME' => 'ceridxxx', 'VALUE' => $certificadoId,           'CHECK' => 'NO'),
                            array('NAME' => 'ceranoxx', 'VALUE' => $certificadoAno,          'CHECK' => 'NO'),
                            array('NAME' => 'cercscxx', 'VALUE' => $certificadoNom,          'CHECK' => 'NO'),
                            array('NAME' => 'sersapxx', 'VALUE' => $sersapxx,                'CHECK' => 'NO'),
                            array('NAME' => 'subidxxx', 'VALUE' => $subSerId,                'CHECK' => 'NO'),
                            array('NAME' => 'aesobsxx', 'VALUE' => $certificadoObser,        'CHECK' => 'NO'),
                            array('NAME' => 'regusrxx', 'VALUE' => trim($_COOKIE['kUsrId']), 'CHECK' => 'SI'),
                            array('NAME' => 'regfcrex', 'VALUE' => date('Y-m-d'),            'CHECK' => 'SI'),
                            array('NAME' => 'reghcrex', 'VALUE' => date('H:i:s'),            'CHECK' => 'SI'),
                            array('NAME' => 'regfmodx', 'VALUE' => date('Y-m-d'),            'CHECK' => 'SI'),
                            array('NAME' => 'reghmodx', 'VALUE' => date('H:i:s'),            'CHECK' => 'SI'),
                            array('NAME' => 'regestxx', 'VALUE' => "ACTIVO",                 'CHECK' => 'SI'));
          if (!f_MySql("INSERT", "lpar0160", $qInsert, $xConexion01, $cAlfa)) {
            $nSwitch = 1;
            $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
            $cMsj .= "Error Guardando Datos.\n";
          }
        }
      } else {
        $nSwitch = 1;
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= "Tiene que seleccionar por lo menos un subservicio.\n";
      }
    break;
    case "ELIMINAR":
      $qDelete = array(array('NAME'=>'ceranoxx','VALUE'=>trim($_POST['ceranoxx']) ,'CHECK'=>'WH'),
                       array('NAME'=>'cercscxx','VALUE'=>trim($_POST['cercscxx']) ,'CHECK'=>'WH'));

      if (!f_MySql("DELETE","lpar0160",$qDelete,$xConexion01,$cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error al Eliminar los Servicos de Certificacion ".$_POST['cercscxx'].",\n";
      } else {
        $cAnio = $_POST['ceranoxx'];
        // Consulta la certificación para actualizar el Estado
        $qCertificacion  = "SELECT ";
        $qCertificacion .= "$cAlfa.lcca$cAnio.ceridxxx, ";
        $qCertificacion .= "$cAlfa.lcca$cAnio.regestxx ";
        $qCertificacion .= "FROM $cAlfa.lcca$cAnio ";
        $qCertificacion .= "WHERE ";
        $qCertificacion .= "$cAlfa.lcca$cAnio.ceridxxx = \"{$_POST['ceridxxx']}\" LIMIT 0,1 ";
        $xCertificacion  = f_MySql("SELECT","",$qCertificacion,$xConexion01,"");

        if (mysql_num_rows($xCertificacion) > 0) {
          $vCertificacion = mysql_fetch_array($xCertificacion);

          // Actualiza el estado de la Certificación debido a que se eliminó la exclusión del servicio y este ya se permite agregar al Pedido
          $cEstado = $vCertificacion['regestxx'];
          if ($vCertificacion['regestxx'] == "PREFACTURADO") {
            $cEstado = "PREFACTURADO_PARCIAL";
          }

          $qUpdateCer = array(array('NAME'=>'regestxx','VALUE' => $cEstado          ,'CHECK'=>'NO'),
                              array('NAME'=>'ceridxxx','VALUE' => $_POST['ceridxxx'],'CHECK'=>'WH'));
          f_MySql("UPDATE","lcca$cAnio",$qUpdateCer,$xConexion01,$cAlfa);
        }
      }
    break;
  }
}

if ($nSwitch == 0) {
  if($_COOKIE['kModo']=="EDITAR"){
    f_Mensaje(__FILE__,__LINE__,"El Registro se Edito Con Exito");
  }
  if($_COOKIE['kModo']=="ELIMINAR"){
    f_Mensaje(__FILE__,__LINE__,"El Registro se Elimino Con Exito");
  }
  if($_COOKIE['kModo']=="NUEVO"){
    f_Mensaje(__FILE__,__LINE__,"El Registro se Creo Con Exito");
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
