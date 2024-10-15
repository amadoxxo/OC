<?php
  namespace openComex;
  /**
	 * Grabar la Legalizacion de Formularios.
	 * --- Descripcion: Me permite grabar la legalizacion de formularios, pero no realiza un Insert sino un Update sobre las
	 * tablas 121 o la 1012, en caso de que no aplique el proceso, solo hace update sobre la tabla 121 cambiando el valor del
	 * campo de docforms a No, pero si se cAplica entonces realizara el Update en ambas tablas, en una avisandole al Do, que si
	 * tiene formularios y en la otra para asociar cada formulario al Do respectivo.
	 * @author Pedro Leon Burbano Suarez <pedrob@repremundo.com.co>
	 * @version 001
	 */
  include("../../../../libs/php/utility.php");
	$cSwitch = "0";
  ///////////////////////////////
	$cPerAno = date('Y');
  $cPerMes = date('m');
  ///////////////////////////////
	//f_Mensaje(__FILE__,__LINE__,$_COOKIE['kModo']);
	switch ($_COOKIE['kModo']) {
  	case "EDITAR";
  	  // Realizo validaciones
	    if ($_POST['cDocId'] == "") {
			  $cSwitch = "1";
			  $cError=$cError."Debe Haber Digitado un DO, Verifique\n";
			}
			if ($_POST['cDocTip'] == "") {
			  $cSwitch = "1";
			  $cError=$cError."Usted no ha Digitado Tab. Para Llenar el Tipo de Operacion.\n";
			}

		  if($_POST['cAplica']=="NO"){
				if($cSwitch == "0"){
          // Esta opción es si no cAplica, como dije antes, si No cAplica solo se hace Update a la Tabla 121
			    $zInsertCab = array(array('NAME'=>'docforms','VALUE'=>trim(strtoupper($_POST['cAplica']))  ,'CHECK'=>'SI'),
     											    array('NAME'=>'docidxxx','VALUE'=>trim(strtoupper($_POST['cDocId']))   ,'CHECK'=>'WH'),
     										      array('NAME'=>'doctipxx','VALUE'=>trim(strtoupper($_POST['cDocTip']))  ,'CHECK'=>'WH'));

			    if (f_MySql("UPDATE","sys00121",$zInsertCab,$xConexion01,$cAlfa)) {
			    } else {
				    $cSwitch = "1";
				    $cError=$cError."Error al Actualizar el Registro de Cabecera en la Tabla sys00121\n";
		      }

			    $zInsertCab = array(array('NAME'=>'doccomex','VALUE'=>''                                   ,'CHECK'=>'NO'),
     			                    array('NAME'=>'comfecsx','VALUE'=>date('Y-m-d')												 ,'CHECK'=>'SI'),
			                        array('NAME'=>'regestxx','VALUE'=>"ASIGNADO"													 ,'CHECK'=>'SI'),
			                        array('NAME'=>'doccomex','VALUE'=>trim(strtoupper($_POST['cDocId']))   ,'CHECK'=>'WH'));

				  if (f_MySql("UPDATE","ffoi0000",$zInsertCab,$xConexion01,$cAlfa)) {
				  } else {
					  $cSwitch = "1";
					  $cError=$cError."Error al Actualizar el Registro de Cabecera\n";
				  }

	        if ($cSwitch == "0") {
	          f_Mensaje_alert("El Proceso de Legalizacion ha sido Realizado con Exito"); ?>
	          <form name = "frgrm" action = "frforasi.php" method = "post" target = "fmwork"></form>
  					<script languaje = "javascript">
  						document.forms['frgrm'].submit();
  				  </script>
			      <?php }else {
		        $cError=$cError."Error de Datos en el Formulario no se Puede Actualizar, Verifique\n";
			      f_Mensaje_alert($cError);
          }
			  }else{
				  // Imprimo errores en pantalla
				  f_Mensaje_alert($cError);
			  }
		  }else{
		    /*
		    ?>
		    <script languaje = "javascript">
				  if (!confirm("Esta Seguro de Asignar Los Siguentes Formularios ?")) {
            <?php $cSwitch = "1"; ?>
				  }
				</script>
		    <?php
		    */
				if($cSwitch == "0"){
				  // verifico si a este Do, se le habian hecho operaciones antes de legalizacion de formularios
			    $cError="";
	        // realizo validaciones de checkeo, si lo que viene hay algo checkeado.
			    if ($_POST['nRecords']==0) {
			      $cSwitch = "1";
			      $cError=$cError."Usted no tiene Formularios Asignados, Verifique\n";
			    }
			    if ($_POST['cChekeados']=="") {
			      $cSwitch = "1";
			      $cError=$cError."Usted no ha Asignado Ningun Formulario al DO, Verifique\n";
			    }
		      if ($cSwitch == "0") {
		        $zMatriz01 = explode("|",$_POST['cComMemo']);
		        $nAcumVlrPagCau = 0;
		  		  for ($i=0;$i<count($zMatriz01);$i++) {

			        if ($zMatriz01[$i] != "") {
			          $zMatriz02 = explode("~", $zMatriz01[$i]);
			        }

              if ($cSwitch == "0") {
                //  Empiezo a asociar los formularios escogidos a un DO.
			          $zInsertCab = array(array('NAME'=>'doccomex','VALUE'=>trim(strtoupper($_POST['cDocId']))       ,'CHECK'=>'SI'),
     				                        array('NAME'=>'comfecsx','VALUE'=>date('Y-m-d')													   ,'CHECK'=>'SI'),
     				                        array('NAME'=>'regestxx','VALUE'=>"CONDO"													         ,'CHECK'=>'SI'),
     				                        array('NAME'=>'ptoidxxx','VALUE'=>trim(strtoupper($zMatriz02[1]))          ,'CHECK'=>'WH'),
     					  				            array('NAME'=>'seridxxx','VALUE'=>trim(strtoupper($zMatriz02[0]))          ,'CHECK'=>'WH'));

			          if (f_MySql("UPDATE","ffoi0000",$zInsertCab,$xConexion01,$cAlfa)) {
				        } else {
							    $cSwitch = "1";
							    $cError=$cError."Error al Actualizar el Registro de Cabecera en la Tabla ffoi0000\n";
						    }
				      }
    		    }

    		    if ($cSwitch == "0") {
					    // Actualizo el campo docforms EN LA TABLA 121 QUE ME DICE si el Do tiene o no formularios.
				      $zInsertCab = array(array('NAME'=>'docforms','VALUE'=>'SI'                                 ,'CHECK'=>'SI'),
      	                          array('NAME'=>'docidxxx','VALUE'=>trim(strtoupper($_POST['cDocId']))   ,'CHECK'=>'WH'),
      							      			  array('NAME'=>'doctipxx','VALUE'=>trim(strtoupper($_POST['cDocTip']))  ,'CHECK'=>'WH'));

				      if (f_MySql("UPDATE","sys00121",$zInsertCab,$xConexion01,$cAlfa)) {
				      } else {
					      $cSwitch = "1";
					      $cError=$cError."Error al Actualizar el Registro de Cabecera en la Tabla sys00121\n";
				      }
   		      }

  			    // Al Final Pregunto Que si no hubo Error? para Mostrar el Mensaje de UPDATE Satisfactorio
				    if ($cSwitch == "0") {
					    f_Mensaje_alert("El Proceso de Legalizacion ha sido Realizado con Exito"); ?>
					    <form name = "frgrm" action = "frforasi.php" method = "post" target = "fmwork"></form>
    					<script languaje = "javascript">
    						document.forms['frgrm'].submit();
    				  </script>
    				  <!--
					    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
					    <script languaje = "javascript">
						    document.forms['frgrm'].submit();
				      </script>
				      -->
				      <?php
				    }
			    } else {
            $cError=$cError."Error de Datos en el Formulario no se Puede Actualizar, Verifique\n";
				    f_Mensaje_alert($cError);
		      }
		    }else{
		      // Imprimo errores en pantalla.
		      f_Mensaje_alert($cError);
		    }
		  }
		break;
	  case "ANULAR";
		  /**
		   * realizo validaciones de Chackeo, si algo viene o no checkeado.
		   */
		  $cError="";
			if ($_POST['nRecords']==0) {
				$cSwitch = "1";
				$cError=$cError."Usted no tiene Formularios Asignados, Verifique\n";
			}
			if ($_POST['cChekeados']=="") {
				$cSwitch = "1";
				$cError=$cError."Usted no ha Escogido Ningun Formulario para Anular, Verifique\n";
			}
			if ($cSwitch == "0") {
			  $zMatriz01 = explode("|",$_POST['cComMemo']);
				for ($i=0;$i<count($zMatriz01);$i++) {
				  if ($zMatriz01[$i] != "") {
				    $zMatriz02 = explode("~", $zMatriz01[$i]);
				    /**
				     * a los formularios escogidos les cambio el estado a ANULADO
				     */
				    $zInsertCab = array(array('NAME'=>'regestxx','VALUE'=>'PRVANULADO'                         ,'CHECK'=>'SI'),
      			                    array('NAME'=>'comfecsx','VALUE'=>date('Y-m-d')												 ,'CHECK'=>'SI'),
				                        array('NAME'=>'ptoidxxx','VALUE'=>trim(strtoupper($zMatriz02[1]))      ,'CHECK'=>'WH'),
      												  array('NAME'=>'seridxxx','VALUE'=>trim(strtoupper($zMatriz02[0]))      ,'CHECK'=>'WH'));

				    if (f_MySql("UPDATE","ffoi0000",$zInsertCab,$xConexion01,$cAlfa)) {
				    } else {
					    $cSwitch = "1";
					    $cError=$cError."Error al Actualizar el Registro de Cabecera\n";
				    }
				  }
				}
				/* Al Final Pregunto Que si no hubo Error? para Mostrar el Mensaje de ANULAR Satisfactorio  */
				if ($cSwitch == "0") {
					f_Mensaje_alert("Usted Ha Anulado Provisionalmente con Exito los Formularios Escogidos"); ?>
					<form name = "frgrm" action = "frforanu.php" method = "post" target = "fmwork"></form>
					<script languaje = "javascript">
						document.forms['frgrm'].submit();
				  </script>
				<?php }
			} else {
			  $cError=$cError."Error de Datos en el Formulario no se Puede Anular, Verifique\n";
				f_Mensaje_alert($cError);
			  ?>
		  	<form name = "frgrm" action = "frforanu.php" method = "post" target = "fmwork"></form>
  			  <script languaje = "javascript">
						document.forms['frgrm'].submit();
				  </script>
				<?php
		  }
		break;
	  case "ANULARTRA";
		  /**
		   * realizo validaciones de Chackeo, si algo viene o no checkeado.
		   */
		  $cError="";
			if ($_POST['nRecords']==0) {
				$cSwitch = 1;
				$cError=$cError."No Existen Asignaciones de Formularios a Ningun DO, Verifique\n";
			}
			if ($_POST['cChekeados']=="") {
				$cSwitch = 1;
				$cError=$cError."Usted no ha Escogido Ningun DO para anular Asignacion, Verifique\n";
			}
			if ($cSwitch == 0) {
			  $zMatriz01 = explode("|",$_POST['cComMemo']);
				for ($i=0;$i<count($zMatriz01);$i++) {
				    if($zMatriz01[$i]!=""){

				      $zSqlDo = "SELECT * FROM $cAlfa.sys00121 WHERE docidxxx = \"{$zMatriz01[$i]}\" LIMIT 0,1";
			        $zCrsDo = mysql_query($zSqlDo,$xConexion01);
			        $zRDo = mysql_fetch_array($zCrsDo);

			        if($zRDo['docforms']=="SI"){
			          $zInsertCab = array(array('NAME'=>'regestxx','VALUE'=>'ASIGNADO'                            ,'CHECK'=>'SI'),
			                              array('NAME'=>'doccomex','VALUE'=>''                                    ,'CHECK'=>'NO'),
			                              array('NAME'=>'regestxx','VALUE'=>'CONDO'                               ,'CHECK'=>'WH'),
        			                      array('NAME'=>'doccomex','VALUE'=>trim(strtoupper($zMatriz01[$i]))      ,'CHECK'=>'WH'));

    					  if (f_MySql("UPDATE","ffoi0000",$zInsertCab,$xConexion01,$cAlfa)) {
    					  } else {
    						  $cSwitch = 1;
    						  $cError=$cError."Error al Actualizar el Registro de Cabecera en la 1012\n";
    					  }
			        }

  				    $zInsertCab = array(array('NAME'=>'docforms','VALUE'=>''                                    ,'CHECK'=>'NO'),
        			                    array('NAME'=>'docidxxx','VALUE'=>trim(strtoupper($zMatriz01[$i]))      ,'CHECK'=>'WH'));

  					  if (f_MySql("UPDATE","sys00121",$zInsertCab,$xConexion01,$cAlfa)) {
  					  } else {
  						  $cSwitch = 1;
  						  $cError=$cError."Error al Actualizar el Registro de Cabecera en la 121\n";
  					  }
				    }
				}
			}
			if($cSwitch==0){
				f_Mensaje(__FILE__,__LINE__,"La Asigancion fue Anulada con Exito.");?>
		  	<form name = "frgrm" action = "frforcon.php" method = "post" target = "fmwork"></form>
  			  <script languaje = "javascript">
						document.forms['frgrm'].submit();
				  </script>
				<?php
			}else{
				f_Mensaje(__FILE__,__LINE__,$cError);
			}
		break;
		case "LIBERAR";
		  /**
		   * Realizo Validaciones.
		   */
		  $cError="";
			if ($_POST['nRecords']==0) {
				$cSwitch = "1";
				$cError=$cError."Usted no tiene Formularios Asignados, Verifique\n";
			}
			if ($_POST['cChekeados']=="") {
				$cSwitch = "1";
				$cError=$cError."Usted no ha Escogido Ningun Formulario para Liberar, Verifique\n";
			}
		  if ($cSwitch == "0") {
			  $zMatriz01 = explode("|",$_POST['cComMemo']);
				for ($i=0;$i<count($zMatriz01);$i++) {
				  if ($zMatriz01[$i] != "") {
				    $zMatriz02 = explode("~", $zMatriz01[$i]);
				    /**
				     * Al formulario le asigno vacio en el campo doccomex PARA QUITARLE su asociación con el DO.
				     */
				    $zInsertCab = array(array('NAME'=>'doccomex','VALUE'=>''                                   ,'CHECK'=>'NO'),
      			                    array('NAME'=>'comfecsx','VALUE'=>date('Y-m-d')												 ,'CHECK'=>'SI'),
				                        array('NAME'=>'regestxx','VALUE'=>"ASIGNADO"													 ,'CHECK'=>'SI'),
				                        array('NAME'=>'ptoidxxx','VALUE'=>trim(strtoupper($zMatriz02[1]))      ,'CHECK'=>'WH'),
      												  array('NAME'=>'seridxxx','VALUE'=>trim(strtoupper($zMatriz02[0]))      ,'CHECK'=>'WH'));

				    if (f_MySql("UPDATE","ffoi0000",$zInsertCab,$xConexion01,$cAlfa)) {
					  } else {
						  $cSwitch = "1";
						  $cError=$cError."Error al Actualizar el Registro de Cabecera\n";
					  }
				  }
				}
				/* Al Final Pregunto Que si no hubo Error? para Mostrar el Mensaje de LIBERAR Satisfactorio  */
				if ($cSwitch == "0") {
					f_Mensaje_alert("Usted ha Liberado con Exito los Formularios Escogidos"); ?>
					<form name = "frgrm" action = "frforlib.php" method = "post" target = "fmwork"></form>
					<script languaje = "javascript">
						document.forms['frgrm'].submit();
				  </script>
				<?php }
		 } else {
			 $cError=$cError."Error de Datos en el Formulario no se Puede Liberar, Verifique\n";
			 f_Mensaje_alert($cError);
		   ?>
   	   <form name = "frgrm" action = "frforlib.php" method = "post" target = "fmwork"></form>
			 <script languaje = "javascript">
			   document.forms['frgrm'].submit();
			 </script>
		   <?php
	    }
		break;
		default:
      $cSwitch = "1";
  		f_Mensaje(__FILE__,__LINE__,"El Modo de Grabado Viene Vacio, Verifique");
		break;
	}
?>