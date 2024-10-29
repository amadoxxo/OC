<?php
  namespace openComex;
  /**
	 * Graba Cambio de Fecha Prefacturas Legalizadas.
	 * --- Descripcion: Permite Guardar el Cambio de Fecha de las Facturas Legalizadas.
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@open-eb.co>
   * @package Opencomex
	 */

  include("../../../../../libs/php/utility.php");
  include("../../../../../libs/php/utiindi.php");

  $nSwitch = 0; // Switch para Vericar la Validacion de Datos
  $nError  = 0; // Switch para Vericar la Validacion de Datos
  $cMsj    = "\n";

  switch ($_COOKIE['kModo']) {
    case "CAMBIOFECHAPFL":
			/***** Validando que llegue la fecha nueva *****/
      if ($_POST['dFecNue'] == "" || $_POST['dFecNue'] == "0000-00-00") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Debe Seleccionar una fecha nueva.\n";
      }

			/***** Validando que la observacion no sea vacia *****/
      if ($_POST['cObserv'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Debe Ingresar una Observacion.\n";
      }

      #Que la tabla (anio) de la nueva fecha exista en el sistema.
      $cPerAnoNew = substr($_POST['dFecNue'],0,4);
      $qVerTab  = "SELECT comidxxx FROM $cAlfa.fcoc$cPerAnoNew LIMIT 0,1";
      $xVerTab  = f_MySql("SELECT","",$qVerTab,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qVerTab."~".mysql_num_rows($xVerTab));
      if (!$xVerTab) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "No Existe la Tabla de Movimiento Contable para la Nueva Fecha [$cPerAnoNew].\n";
      }

      #Validando que exista la tabla del anio seleccionado
      $cPerAnio = $_POST['cPreAnio'];
      $qVerTab  = "SELECT COUNT(comidxxx) FROM $cAlfa.fcoc$cPerAnio LIMIT 0,1";
      $xVerTab  = f_MySql("SELECT","",$qVerTab,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qVerTab."~".mysql_num_rows($xVerTab));
      if (!$xVerTab) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "No Existe la Tabla de Movimiento Contable para el Anio [$cPerAnio].\n";
      }

      if($nSwitch == 0){
        $mPreFact = array();
        for ($i=0; $i<$_POST['nSecuencia']; $i++) {
          if ($_POST['cComId' .($i+1)] != "" && $_POST['cComCod' .($i+1)] != "" && $_POST['cComCsc' .($i+1)] != "" && $_POST['cComCsc2' .($i+1)] != "") {

            $cIndice = "{$_POST['cComId' .($i+1)]}-{$_POST['cComCod' .($i+1)]}-{$_POST['cComCsc' .($i+1)]}-{$_POST['cComCsc2' .($i+1)]}";
            $qFcocxxx  = "SELECT ";
            $qFcocxxx .= "$cAlfa.fcoc$cPerAnio.comidxxx, ";
            $qFcocxxx .= "$cAlfa.fcoc$cPerAnio.comcodxx, ";
            $qFcocxxx .= "$cAlfa.fcoc$cPerAnio.comcscxx, ";
            $qFcocxxx .= "$cAlfa.fcoc$cPerAnio.comcsc2x, ";
            $qFcocxxx .= "$cAlfa.fcoc$cPerAnio.comfacpr, ";
            $qFcocxxx .= "$cAlfa.fcoc$cPerAnio.comfprfe ";
            $qFcocxxx .= "FROM $cAlfa.fcoc$cPerAnio ";
            $qFcocxxx .= "WHERE ";
            $qFcocxxx .= "$cAlfa.fcoc$cPerAnio.comfacpr = \"$cIndice\" AND ";
            $qFcocxxx .= "$cAlfa.fcoc$cPerAnio.regestxx = \"ACTIVO\" LIMIT 0,1 ";
            $xFcocxxx  = f_MySql("SELECT","",$qFcocxxx,$xConexion01,"");
            // f_Mensaje(__FILE__,__LINE__,$qFcocxxx." ~ ".mysql_num_rows($xFcocxxx));

            if (mysql_num_rows($xFcocxxx) == 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "La Prefactura [{$_POST['cComId' .($i+1)]}-{$_POST['cComCod' .($i+1)]}-{$_POST['cComCsc' .($i+1)]}-{$_POST['cComCsc2' .($i+1)]}] No Existe.\n";
            }else {
              $vFcocxxx = mysql_fetch_array($xFcocxxx);

              if($nSwitch == 0) {
                $nInd_mPreFact = count($mPreFact);
                $mPreFact[$nInd_mPreFact]['cComFacpr'] = "{$_POST['cComId' .($i+1)]}-{$_POST['cComCod' .($i+1)]}-{$_POST['cComCsc' .($i+1)]}-{$_POST['cComCsc2' .($i+1)]}";
                $mPreFact[$nInd_mPreFact]['cComFech']  = $_POST['cComFech' .($i+1)];
              }
            }
          } else {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Debe Selecionar una Prefactura en la Secuencia {$_POST['cDocSeq' .($i+1)]}.\n";
          }
        } ## for ($i=0; $i<$_POST['nSecuencia']; $i++) { ##
      }
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
      case "CAMBIOFECHAPFL":
        foreach ($mPreFact as $vPreFact) {

          $vComFact = explode('-', $vPreFact['cComFacpr']);
          $cComId   = $vComFact[0];
          $cComCod  = $vComFact[1];
          $cComCsc  = $vComFact[2];
          $cComCsc2 = $vComFact[3];

          #Se cambia la fecha en los registro de cabecera
          $mUpdFcoc = array(array('NAME'=>'comfprfe','VALUE'=>$_POST['dFecNue']             ,'CHECK'=>'SI'),
                            array('NAME'=>'regfmodx','VALUE'=>date("Y-m-d")                 ,'CHECK'=>'SI'),
                            array('NAME'=>'reghmodx','VALUE'=>date("H:i:s")          			  ,'CHECK'=>'SI'),
                            array('NAME'=>'comfacpr','VALUE'=>trim($vPreFact['cComFacpr'])  ,'CHECK'=>'WH'));
          if (f_MySql("UPDATE","fcoc$cPerAnio",$mUpdFcoc,$xConexion01,$cAlfa)) {
            //no hace nada
          } else {
            $nSwitch = 1;
            $nError  = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error al Actualizar el Registro la Tabla fcoc$cPerAnio.\n";
          }


          if ($nSwitch == 0) {
            #Guardando observacion
            #Buscando el consecutivo
            $qNumSec  = "SELECT obscscxx FROM $cAlfa.fcob0000 ORDER BY ABS(obscscxx) DESC LIMIT 0,1 ";
            $xNumSec  = f_MySql("SELECT","",$qNumSec,$xConexion01,"");
            //f_Mensaje(__FILE__,__LINE__,$qNumSec."~".mysql_num_rows($xNumSec));
            if(mysql_num_rows($xNumSec) > 0) {
              $xRNS = mysql_fetch_array($xNumSec);
              $cNumSec = $xRNS['obscscxx'] + 1;
            } else {
              $cNumSec = 1;
            }
            $cNumSec = str_pad($cNumSec,5,"0",STR_PAD_LEFT);
            
            $mInsObs = array(array('NAME'=>'comidxxx','VALUE'=>$cComId                ,'CHECK'=>'SI'),
                            array('NAME'=>'comcodxx','VALUE'=>$cComCod                ,'CHECK'=>'SI'),
                            array('NAME'=>'comcscxx','VALUE'=>$cComCsc                ,'CHECK'=>'SI'),
                            array('NAME'=>'comcsc2x','VALUE'=>$cComCsc2               ,'CHECK'=>'SI'),
                            array('NAME'=>'comfecxx','VALUE'=>$_POST['dFecNue']       ,'CHECK'=>'SI'),
                            array('NAME'=>'comfecan','VALUE'=>$vPreFact['cComFech']   ,'CHECK'=>'SI'),
                            array('NAME'=>'obscscxx','VALUE'=>$cNumSec 				        ,'CHECK'=>'SI'),
                            array('NAME'=>'obsidxxx','VALUE'=>'FACTURA' 					    ,'CHECK'=>'SI'),
                            array('NAME'=>'gofidxxx','VALUE'=>'100' 							    ,'CHECK'=>'SI'),
                            array('NAME'=>'obsobsxx','VALUE'=>trim($_POST['cObserv']) ,'CHECK'=>'SI','CS'=>'NONE'),
                            array('NAME'=>'regusrxx','VALUE'=>$_COOKIE['kUsrId']      ,'CHECK'=>'SI'),
                            array('NAME'=>'regfcrex','VALUE'=>date("Y-m-d")		        ,'CHECK'=>'SI'),
                            array('NAME'=>'reghcrex','VALUE'=>date("H:i:s")		        ,'CHECK'=>'SI'),
                            array('NAME'=>'regfmodx','VALUE'=>date("Y-m-d")           ,'CHECK'=>'SI'),
                            array('NAME'=>'reghmodx','VALUE'=>date("H:i:s")		        ,'CHECK'=>'SI'),
                            array('NAME'=>'regestxx','VALUE'=>"ACTIVO"  			        ,'CHECK'=>'SI'));
            if (f_MySql("INSERT","fcob0000",$mInsObs,$xConexion01,$cAlfa)) {
              // No hace nada
            } else {
              $nSwitch = 1;
              $nError  = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "Error al Insertar la Observacion [fcob0000].\n";
            }
          }
        }
      break;
    }
  }

  if ($nSwitch == 1) {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique");
  } else {
    if($_COOKIE['kModo']=="CAMBIOFECHAPFL"){
      f_Mensaje(__FILE__,__LINE__,"Se Realizo el Cambio de Fecha de la(s) Prefactura(s) Legalizadas con Exito.!");
    }
    ?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
      <script languaje = "javascript">
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
        document.forms['frgrm'].submit()
      </script>
    <?php
   }
?>