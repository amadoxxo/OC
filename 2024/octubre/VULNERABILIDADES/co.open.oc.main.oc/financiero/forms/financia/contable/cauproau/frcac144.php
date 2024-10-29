<?php
  namespace openComex;
  /**
   * --- Descripcion: ValidWindows de Categoria de Conceptos
   * @author Hair Zabala <hair.zabala@opentectnologia.com.co>
   * @package openComex
   */	
   
  include("../../../../libs/php/utility.php");
   
  /**
   *  Cookie fija
   */
  $kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlHost = $kDf[0];
  $kMysqlUser = $kDf[1];
  $kMysqlPass = $kDf[2];
  $kMysqlDb   = $kDf[3];
  $kUser      = $kDf[4];
  $kLicencia  = $kDf[5];
  $swidth     = $kDf[6];
  
  if (!empty($gWhat) && !empty($gFunction)) {
  ?>
  <html>
    <head>
      <title>Param&eacute;trica Categor&iacute;a Conceptos</title>
    	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
	   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/programs/estilo.css">
    </head>
    <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
      <center>
        <table border ="0" cellpadding="0" cellspacing="0" width="300">
          <tr>
            <td>
              <fieldset>
                <legend>Categor&iacute;as Conceptos</legend>
                <form name = "frgrm" action = "" method = "post" target = "fmpro">
                  <?php
                  switch ($gWhat) {
                    case "WINDOW":
                      $qCatCon  = "SELECT ";
                      $qCatCon .= "cacidxxx,cacdesxx,regestxx ";
                      $qCatCon .= "FROM $cAlfa.fpar0144 ";
                      $qCatCon .= "WHERE ";
											if($gFunction == "cCacId"){
												$qCatCon .= "cacidxxx LIKE \"%$gCacId%\" AND ";
											}else if($gFunction == "cCacDes"){
												$qCatCon .= "cacdesxx LIKE \"%$gCacDes%\" AND ";
											}
                      $qCatCon .= "regestxx = \"ACTIVO\" ORDER BY abs(cacidxxx) ASC";
                      $xCatCon  = f_MySql("SELECT","",$qCatCon,$xConexion01,"");
                      // f_Mensaje(__FILE__,__LINE__,$qCatCon."~".mysql_num_rows($xCatCon));
                      
                      if ($xCatCon && mysql_num_rows($xCatCon) > 0) { ?>
                        <center>
                          <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                            <tr bgcolor = '#D6DFF7'>
                              <td widht = "050" Class = "name"><center>C&oacute;digo</center></td>
                              <td widht = "400" Class = "name"><center>Descripci&oacute;n</center></td>
                              <td widht = "050" Class = "name"><center>Estado</center></td>
                            </tr>
                            <?php 
                            while ($xRTD = mysql_fetch_array($xCatCon)){?>
                              <tr>
                                <?php
                                switch($gFunction){
                                  case "cCacId":
																	case "cCacDes": ?>
                                    <td width = "050" class= "name">
                                      <a href = "javascript:window.opener.document.forms['frgrm']['cCacId'].value = '<?php echo $xRTD['cacidxxx']?>';
                                        window.opener.f_Links('cCacId','EXACT',0);
                                        window.close();"><?php echo $xRTD['cacidxxx'] ?></a>
                                    </td>
                                    <?php
                                  break;
                                }?>
                                <td width = "400" class= "name"><?php echo $xRTD['cacdesxx'] ?></td>
                                <td width = "050" class= "name"><?php echo $xRTD['regestxx'] ?></td>
                              </tr>
                              <?php 
                            }?>
                          </table>
                        </center>
                        <?php
                      }else{
                        f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
                      }
                    break;
                    case "VALID":
                      $qCatCon  = "SELECT ";
                      $qCatCon .= "cacidxxx,cacdesxx,regestxx ";
                      $qCatCon .= "FROM $cAlfa.fpar0144 ";
                      $qCatCon .= "WHERE ";
											if($gFunction == "cCacId"){
												$qCatCon .= "cacidxxx LIKE \"%$gCacId%\" AND ";
											}else if($gFunction == "cCacDes"){
												$qCatCon .= "cacdesxx LIKE \"%$gCacDes%\" AND ";
											}
                      $qCatCon .= "regestxx = \"ACTIVO\" ORDER BY abs(cacidxxx) ASC";
                      $xCatCon  = f_MySql("SELECT","",$qCatCon,$xConexion01,"");
											// f_Mensaje(__FILE__,__LINE__,$qCatCon."~".mysql_num_rows($xCatCon));
                      if (mysql_num_rows($xCatCon) > 0){
                        if (mysql_num_rows($xCatCon) == 1){
                          while ($xRTD = mysql_fetch_array($xCatCon)) {
                            switch ($gFunction){
                              case "cCacId": ?>
                                <script language = "javascript">
                                  parent.fmwork.document.forms['frgrm']['cCacId'].value  = "<?php echo $xRTD['cacidxxx'] ?>";
                                  parent.fmwork.f_Links('<?php echo $gFunction ?>','EXACT');
                                </script>
                                <?php
                              break;
															case "cCacDes": ?>
                                <script language = "javascript">
                                  parent.fmwork.document.forms['frgrm']['cCacDes'].value  = "<?php echo $xRTD['cacdesxx'] ?>";
                                  parent.fmwork.f_Links('<?php echo $gFunction ?>','EXACT');
                                </script>
                                <?php
                              break;
                            }
                          }
                        }else{
                        	?>
                          <script language = "javascript">
                            parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
                          </script>
                          <?php
                        }
                      }else{
                        switch ($gFunction){
                          case "cCacId":
													case "cCacDes": ?>
                            <script language = "javascript">
                              alert('No hay registros coincidentes');
                              parent.fmwork.document.forms['frgrm']['cCacId'].value  = "";
                              parent.fmwork.document.forms['frgrm']['cCacDes'].value  = "";
                            </script>
                            <?php
                          break;
                        }
                      }
                    break;
                    case "EXACT":
	                    $qCatCon  = "SELECT * ";
	                    $qCatCon .= "FROM $cAlfa.fpar0144 ";
	                    $qCatCon .= "WHERE ";
											if($gFunction == "cCacId"){
												$qCatCon .= "cacidxxx LIKE \"%$gCacId%\" LIMIT 0,1 ";
											}else if($gFunction == "cCacDes"){
												$qCatCon .= "cacdesxx LIKE \"%$gCacDes%\" LIMIT 0,1 ";
											}
	                    $xCatCon  = f_MySql("SELECT","",$qCatCon,$xConexion01,"");
											// f_Mensaje(__FILE__,__LINE__,$qCatCon."~".mysql_num_rows($xCatCon));
	                    $vCatCon = mysql_fetch_array($xCatCon);
	                    switch ($gFunction){
	                      case "cCacId":
												case "cCacDes":	
	                        ?>
	                        <script language = "javascript">
	                          parent.fmwork.document.forms['frgrm']['cCacId'].value   = "<?php echo $vCatCon['cacidxxx'] ?>";
	                          parent.fmwork.document.forms['frgrm']['cCacDes'].value  = "<?php echo $vCatCon['cacdesxx'] ?>";
	                        </script>
	                        <?php
	                      break;
	                    }
                    break;
                  }
                  ?>
                </form>
              </fieldset>
            </td>
          </tr>
        </table>
      </center>
    </body>
  </html>
  <?php 
  }else{
    f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos");
  }
?>