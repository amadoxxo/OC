<?php
  namespace openComex;
  /**
   * Graba Ticket en el Modulo de Certificacion.
   * --- Descripcion: Permite Guardar en la tabla Tickets.
   * @author Elian Amado. <elian.amado@openits.co>
   * @package opencomex
   * @version 001
   */
  include('../../../../../config/config.php');
  include('../../../../../financiero/libs/php/utility.php');
  include('../../../../../libs/php/uticemax.php');
  include('../../../../../logistica/libs/php/utiworkf.php');

  /**
   * Switch para Vericar la Validacion de Datos.
   * 
   * @var int
   */
  $nSwitch = 0;

  /**
   * Almacena los errores generados en el proceso.
   * 
   * @var string
   */
  $cMsj = "\n";
  
  $objTicket = new cTickets();

  $vDatos = $_POST;
  $vRes = $objTicket->fnGuardarTicket($vDatos);

  if ($vRes[0] == "false") {
    $nSwitch = 1;
  }
  for ($n=1; $n<count($vRes); $n++) {
    $vAux = explode("~",$vRes[$n]);
    // $cMsj .= "Linea ".str_pad($vAux[0],4,"0",STR_PAD_LEFT).": ";
    $cMsj .= $vAux[1]."\n";
  }

  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "NUEVOTICKET":
        f_Mensaje(__FILE__,__LINE__,"\nTicket creado con exito.\n". $cMsj);
        ?>
        <form name="frgrm" action="<?php echo $_COOKIE['kIniAnt'] ?>" method="post" target="fmwork"></form>
        <script languaje="javascript">
          parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
          document.forms['frgrm'].submit()
        </script>
        <?php
      break;
      case "EDITAR":
        f_Mensaje(__FILE__,__LINE__,"\nReply creado con exito.\n". $cMsj);

        if ($_POST['cOrigen'] == "MISTICKETS") {
          $cRuta = "../../../operatix/workflow/mistiket/frmtiini.php";
          $cCerId = "";
          $cAnio = "";
        } elseif ($_POST['cOrigen'] == "ADMONTICKETS") {
          $cRuta = "../../../operatix/workflow/admontic/fratiini.php";
          $cCerId = "";
          $cAnio = "";
        } else {
          $cRuta = "frtckini.php";
        }
        ?>
        <form name="frgrm" action="<?php echo $cRuta ?>" method="post" target="fmwork">
          <input type = "hidden" name = "cCerId" value = "<?php echo $cCerId ?>">
          <input type = "hidden" name = "cAnio"  value = "<?php echo $cAnio ?>">
        </form>
        <script languaje="javascript">
          parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
          document.forms['frgrm'].submit()
        </script>
        <?php
      break;
    }
  } else {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
  }
?>
