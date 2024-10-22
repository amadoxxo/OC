<?php
  namespace openComex;
  include("../../../../../financiero/libs/php/utility.php"); 

  switch ($_POST['cModo']) {
    case "VALIDPED":
      if ($nSwitch == 0) {   ?>
        <script language="javascript">
          parent.fmwork.document.forms['frgrm']['cStep'].value     = "2";
          parent.fmwork.document.forms['frgrm']['cStep_Ant'].value = "1";
          
          parent.fmwork.document.forms['frgrm'].target = "fmwork";
          parent.fmwork.document.forms['frgrm'].action = "framcnue.php";
          parent.fmwork.document.forms['frgrm'].submit();
        </script>
      <?php } else {
        f_Mensaje(__FILE__,__LINE__,$cMsj); ?>
        <script language="javascript">
          parent.fmwork.document.forms['frgrm']['cStep'].value     = "1";
          parent.fmwork.document.forms['frgrm']['cStep_Ant'].value = "";
          parent.fmwork.document.forms['frgrm'].action = "<?php echo $cArcPaso?>";
        </script>
      <?php }
      if ($nSwitch == 1) {
        f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique");
      }
    break;
  }
?>