<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");

	//f_Mensaje(__FILE__,__LINE__,$_COOKIE['kModo']);
  $nSwitch = 0;
  $cCadErr = "";

/**
 * Graba Cuentas Corrientes.
 * Este programa permite Guardar en la tabla Cuentas Corrientes.
 * @author
 * @package emisioncero
 */
	include("../../../../libs/php/utility.php");

	$nSwitch = "0"; // Switch para Vericar la Validacion de Datos
	$cCadErr = "";
	
	

	switch ($_COOKIE['kModo']) {
	  case "NUEVO":
	  	/***** Validando Codigo *****/
  		if ($_POST['cSerId'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " El serial no puede ser vacio, \n";
  		}

  		/***** Validando Codigo *****/
  		if ($_POST['cSucId'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " La Sucursal del Do no puede ser Vacio,\n";
  		}

  		/***** Validando Codigo *****/
  		if ($_POST['cDocComex'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " El DO no puede ser vacio, \n";
  		}
	/***** Validando Codigo *****/
  		if ($_POST['cDocSuf'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " El Sufijo no puede ser vacio, \n";
  		}
	/***** Validando Codigo *****/
  		if ($_POST['cDocTip'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " El Tipo de Operacion no puede ser vacio, \n";
  		}
  			/***** Validando Codigo *****/
  		if ($_POST['cCliNom'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " El nombre de el Importador no puede ser vacio, \n";
  		}
  	

	/***** Ahora Empiezo a Grabar *****/
	/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/

				if ($zSwitch == "0") {
			  $mMatriz01 = explode("|",$_POST['cComMemo']);
			  for ($i=0;$i<count($mMatriz01);$i++) {
				  if ($mMatriz01[$i] !="") {
				    $zMatriz02 = explode("~", $mMatriz01[$i]);
				    $zDir=$zMatriz02[2];
				    // a los formularios escogidos les cambio el estado a PRVGASTO
				    $zInsertCab = array(array('NAME'=>'comfepgx','VALUE'=>date('Y-m-d')                        ,'CHECK'=>'SI'),
				                        array('NAME'=>'regestxx','VALUE'=>'PRVGASTO'                           ,'CHECK'=>'SI'),
				                        array('NAME'=>'ptoidxxx','VALUE'=>trim(strtoupper($zMatriz02[1]))      ,'CHECK'=>'WH'),
      												  array('NAME'=>'seridxxx','VALUE'=>trim(strtoupper($zMatriz02[0]))      ,'CHECK'=>'WH'));

					  if (f_MySql("UPDATE","ffoi0000",$zInsertCab,$xConexion01,$cAlfa)) {
					  } else {
						  $zSwitch = "1";
						  $error=$error."Error al Actualizar el Registro de Cabecera\n";
					  }
				  }
			  }


			  $qNumSec  = "SELECT convert (max(abs(`afgidxxx`)), DECIMAL) afgidxxx ";
        $qNumSec .= "FROM $cAlfa.fpar0135 LIMIT 0,1 ";
  	    $xNumSec  = f_MySql("SELECT","",$qNumSec,$xConexion01,"");
        $vNumSec  = mysql_fetch_array($xNumSec);
        $cNumSec  = 0+$vNumSec['afgidxxx']+1;


			  /*
			  $cNumSec  = f_Consecutivo_Automatico("afgidxxx","fpar0135",$cAlfa);
        f_Mensaje(__FILE__,__LINE__,"$cNumSec, Verifique");
        */

        /*
        // Verifico Consecutivo.
				$zSqlCon = "SELECT * FROM $cAlfa.GRM00002  WHERE  DOCTDOXX = \"AFG\"  AND  DOCSUCXX=\"NACIONAL\"  LIMIT 0,1";
				$zCrsCon = mysql_query($zSqlCon,$xConexion01);
				$zRCon   = mysql_fetch_array($zCrsCon);
				$zConGr=substr(trim(date('Y-m-d')),0,4).str_pad($zRCon['DOCIDXXX'],6,"0",STR_PAD_LEFT);

				$zSqlVer = "SELECT * FROM $cAlfa.fpar0135  WHERE  afgidxxx = \"{$zConGr}\" LIMIT 0,1";
				$zCrsVer = mysql_query($zSqlVer,$xConexion01);
				$zRVerNum   = mysql_num_rows($zCrsVer);
				$zRVer   = mysql_fetch_array($zCrsVer);
				if($zRVerNum<1){
        */
			  // Inserto en la tabla 135 el registro de este proceso de legalización al gasto.
			  $zInsertCab = array(array('NAME'=>'afgidxxx','VALUE'=>$cNumSec                       ,'CHECK'=>'SI'),
      			                array('NAME'=>'diridxxx','VALUE'=>$zDir    											 ,'CHECK'=>'SI'),
      											array('NAME'=>'afgobsxx','VALUE'=>$cObserv											 ,'CHECK'=>'SI'),
      											array('NAME'=>'afgforms','VALUE'=>$_POST['cComMemo']					   ,'CHECK'=>'SI'),
      											array('NAME'=>'regusrxx','VALUE'=>$_COOKIE['kUsrId']						 ,'CHECK'=>'SI'),
      											array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')  								 ,'CHECK'=>'SI'),
      											array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')  								 ,'CHECK'=>'SI'),
      											array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d') 								 ,'CHECK'=>'SI'),
      											array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')  								 ,'CHECK'=>'SI'),
      											array('NAME'=>'regestxx','VALUE'=>'ACTIVO'  										 ,'CHECK'=>'SI'));

				if (f_MySql("INSERT","fpar0135",$zInsertCab,$xConexion01,$cAlfa)) {
				} else {
				  $zSwitch = "1";
					$error=$error."Error al Actualizar el Registro de Cabecera\n";
				}
        /*
				}else{
				  $zSwitch = "1";
				 $error=$error."El Numero de Consecutivo ya se Encuentra Registrado, Verifique.";
				}
        // Actualizo la tabla de consecutivos.
				$zInsertCab = array(array('NAME'=>'DOCIDXXX','VALUE'=>($zRCon['DOCIDXXX']+1)               ,'CHECK'=>'SI'),
      		                  array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')													 ,'CHECK'=>'SI'),
      											array('NAME'=>'DOCTDOXX','VALUE'=>'AFG'                                ,'CHECK'=>'WH'),
      											array('NAME'=>'DOCSUCXX','VALUE'=>'NACIONAL'                           ,'CHECK'=>'WH'));

				if (f_MySql("UPDATE","GRM00002",$zInsertCab,$xConexion01,$cAlfa)) {
				} else {
          $zSwitch = "1";
				 $error=$error."Error al Actualizar el Registro de Cabecera\n";
				}
				//f_Mensaje(__FILE__,__LINE__,$zSwitch);
				*/
        // Al Final Pregunto Que si no hubo Error? para Mostrar el Mensaje de ANULAR Satisfactorio
				if ($zSwitch == "0") {
				  f_Mensaje_alert("Usted Ha Autorizado con Exito los Formularios.");?>
					<form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
					<script language = "javascript">
					  parent.fmwork.f_Imp_Soporte('<?php echo $_POST['cComMemo']; ?>','<?php echo $cObserv; ?>','<?php echo $cNumSec; ?>');
						document.forms['frgrm'].submit();
				  </script>
				  <?php
        }
			} else {
			  $error=$error."Error de Datos en el Formulario, no se Puede Autorizar, Verifique\n";
				f_Mensaje_alert($error);
			  ?>
  	  	<form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
	 		    <script language = "javascript">
						document.forms['frgrm'].submit();
				  </script>
  				<?php
			}
		break;
		default:
      $zSwitch = "1";
  		f_Mensaje(__FILE__,__LINE__,"El Modo de Grabado Viene Vacio, Verifique");
		break;
	}
?>
