<?php
namespace openComex;
	/**
	 * Valida los datos generales del cliente como la sucursal por donde se va a facturar,
	 * que el tipo de operacion que se va facturar este activa o cerrada y que haya cliente
	 * y a quien va dirigida la factura.
	 * @author Alexander Gordillo <alexanderg@repremundo.com.co>
	 * @package conexiongrm
	 */

	include("../../../../libs/php/utility.php");
  include("../../../../../financiero/libs/php/utility.php");
  $nSwitch = 0; $cMsj = "";

  
  if ($nSwitch == 0) {
    if (trim($cMsj,"\n") != "") {
      f_Mensaje(__FILE__,__LINE__,$cMsj."\n");
    }
    ?>
    <script language="javascript">
      parent.fmwork.document.forms['frgrm']['cStep'].value     = "2";
      parent.fmwork.document.forms['frgrm']['cStep_Ant'].value = "1";

      //Distribucion Anticipo para integracion con SAP
      if ("<?php echo $cAlfa ?>" == "ALMACAFE" || "<?php echo $cAlfa ?>" == "TEALMACAFE" || "<?php echo $cAlfa ?>" == "DEALMACAFE") {
        parent.fmwork.document.forms['frgrm']['cForImp'].value = "<?php echo $vTerCCC['ccccdant'] ?>";
      }

      parent.fmwork.fnAsignarValores();
      parent.fmwork.document.forms['frestado'].target = "fmwork";
      parent.fmwork.document.forms['frestado'].action = "frpednue.php";
      parent.fmwork.document.forms['frestado'].submit();
    </script>
  <?php } else {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.\n"); ?>
    <script language="javascript">
      parent.fmwork.document.forms['frgrm'].target = "fmwork";
      parent.fmwork.document.forms['frgrm'].action = "frpednue.php";
    </script>
  <?php }
?>
