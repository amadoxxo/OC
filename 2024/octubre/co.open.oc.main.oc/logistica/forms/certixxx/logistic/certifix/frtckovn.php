<?php
namespace openComex;
/**
 * Orgainzacion de Ventas.
 * --- Descripcion: Permite Ver la información del ticket.
 * @author elian.amado@openits.co
 * @package openComex
 * @version 001
 */
  include("../../../../../financiero/libs/php/utility.php");
  include("../../../../../logistica/libs/php/utiworkf.php");

  $objVerTickets = new cTickets();
  $vDatCab = $objVerTickets->fnCabeceraTickets($nTicId, $nAnioTic);
  $vDatRep = $objVerTickets->fnDetalleTickets($nTicId, '', $nAnioTic);
?>
<html>
<head>
  <LINK rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
  <script languaje='javascript' src='<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
  <script language="javascript">
    function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
      if ("<?php echo $cOrigen ?>" == "MISTICKETS") {
        document.location="../../../operatix/workflow/mistiket/frmtiini.php";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
      } else if ("<?php echo $cOrigen ?>" == "ADMONTICKETS") {
        document.location="../../../operatix/workflow/admontic/fratiini.php";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
      } else {
        document.location="frtckini.php?cCerId=<?php echo $cCerId ?>&cAnio=<?php echo $cAnio ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
      } 
    }
  </script>
</head>
  <body topmargin=0 leftmargin=0 margnwidth=0 marginheight=0 style='margin-right:0'>
    <form name = "frgrm" action = "frtckini.php" method = "post" target="fmpro">
      <input type = "hidden" name = "cCerId"    value = "<?php echo $cCerId ?>">
      <input type = "hidden" name = "cAnio"     value = "<?php echo $cAnio ?>">
    </form>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="1050">
        <tr>
          <td>
            <fieldset>
              <legend>Ver Ticket</legend>
              <?php echo $objVerTickets->fnInfoTicket($vDatCab, $vDatRep, "SI"); ?>
            </fieldset>
          </td>
        </tr>
      </table>
    </center>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="1050">
        <tr height="21">
          <td width="959" height="21"></td>
          <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick="javascript:fnRetorna()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
        </tr>
      </table>
    </center>
  </body>
</html>
