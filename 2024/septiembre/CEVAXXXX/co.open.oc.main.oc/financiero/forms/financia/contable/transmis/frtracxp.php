<?php
	include("../../../../libs/php/utility.php");
?> 
<html>
	<head><title></title>
  	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
		<script language="javascript">
		function uRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
				parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
	  }
   	</script>		
	</head>
  <body>
  <?php
    $zMatrizCar=array(); 
    $zError=0; 
    $zCadenaError=""; 
    $i=0; 
    $bandera=0;
    switch ($zBand)	{
 			case 1: 			  
 			  $zSqlTbl = "SHOW CREATE TABLE $cAlfa.GRM01005 ";
        $zCrsTbl = mysql_query($zSqlTbl,$xConexion01);
        $zRTbl   = mysql_fetch_array($zCrsTbl);
        
        $nUse_DB_Pro = mysql_select_db($cAlfa,$xConexion01);
        $zSql=str_replace("GRM01005","TEMP1005",$zRTbl['Create Table']); 
        
        $nCrsTbl = mysql_query($zSql,$xConexion01);
 			  
 			  
 			  $zGestor = fopen($_FILES["upfile"]["tmp_name"],"r");
        while (!feof($zGestor)) {
          $variable=fgets($zGestor, 4096);
          $zBuffer[0]  =substr($variable,1,1);
          $zBuffer[1]  =substr($variable,5,3);
          $zBuffer[2]  =substr($variable,10,11);
          $zBuffer[3]  =substr($variable,22,3);
          $zBuffer[4]  =substr($variable,26,8);
          $zBuffer[5]  =substr($variable,35,8);
          $zBuffer[6]  =substr($variable,44,13);
          $zBuffer[9]  =substr($variable,95,10);
          $zBuffer[10] =substr($variable,106,16);
          $zBuffer[11] =substr($variable,123,4);
          $zBuffer[13] =substr($variable,132,2);
          $zBuffer[14] =substr($variable,135,16);
          $zBuffer[15] =substr($variable,63,30);
          
          if($i!=0){
            $zMatrizCar[$i]['COMIDXXX']=trim(str_replace('"',"",$zBuffer[0]));
            $zMatrizCar[$i]['COMCODXX']=intval(trim(str_replace('"',"",$zBuffer[1])));
            $zMatrizCar[$i]['COMCSCXX']=intval(trim(str_replace('"',"",$zBuffer[2])));
            $zMatrizCar[$i]['COMSEQXX']=intval(trim(str_replace('"',"",$zBuffer[3])));
            $zMatrizCar[$i]['COMFECXX']=trim(str_replace('"',"",$zBuffer[4]));
            $zMatrizCar[$i]['COMFECVE']=trim(str_replace('"',"",$zBuffer[5]));
            $zMatrizCar[$i]['CLIIDXXX']=intval(trim(str_replace('"',"",$zBuffer[6])));
            $zMatrizCar[$i]['PUCIDXXX']=trim(str_replace('"',"",$zBuffer[9]));
            $zMatrizCar[$i]['COMSALDO']=trim(str_replace('"',"",$zBuffer[10]));
            if($zMatrizCar[$i]['COMSALDO']>0){$zMatrizCar[$i]['COMNATXX']="D";}else{$zMatrizCar[$i]['COMNATXX']="C";}
            
            if((intval(trim(str_replace('"',"",$zBuffer[11])))=="") or intval(trim(str_replace('"',"",$zBuffer[11])))==0){
              $zBuffer[11]="30";
            }
            
            $zMatrizCar[$i]['CCOIDXXX']=intval(trim(str_replace('"',"",$zBuffer[11])));
            $zMatrizCar[$i]['COMXXXXX']=intval(trim(str_replace('"',"",$zBuffer[13])));
            $zMatrizCar[$i]['COMSALEX']=intval(trim(str_replace('"',"",$zBuffer[14])));
            $zMatrizCar[$i]['REGESTXX']="ACTIVO";
            $zMatrizCar[$i]['CLINOMXX']=trim(str_replace('"',"",$zBuffer[15]));
            $zMatrizCar[$i]['ERROR']=0;
          }
          $i++;
        }
        
        for($i=1; $i<=count($zMatrizCar); $i++){
          /**
           * Proceso de Validacion
           */
          if($zMatrizCar[$i]['COMIDXXX']  =="" or 
            $zMatrizCar[$i]['COMCODXX']   =="" or 
            $zMatrizCar[$i]['COMCSCXX']   =="" or 
            $zMatrizCar[$i]['COMSEQXX']   =="" or 
            $zMatrizCar[$i]['CLIIDXXX']   =="" or 
            $zMatrizCar[$i]['PUCIDXXX']   =="" or 
            $zMatrizCar[$i]['REGESTXX']   ==""){
            
            //wMenssage_alert($zMatrizCar[$i]['COMIDXXX'].'---'.$zMatrizCar[$i]['COMCODXX'].'---'.$zMatrizCar[$i]['COMCSCXX'].'---'.$zMatrizCar[$i]['COMSEQXX'].'---'.$zMatrizCar[$i]['COMFECXX'].'---'.$zMatrizCar[$i]['COMFECVE'].'---'.$zMatrizCar[$i]['CLIIDXXX'].'---'.$zMatrizCar[$i]['PUCIDXXX'].'---'.$zMatrizCar[$i]['COMSALDO'].'---'.$zMatrizCar[$i]['COMNATXX'].'---'.$zMatrizCar[$i]['CCOIDXXX'].'---'.$zMatrizCar[$i]['REGESTXX']);
              
            $zCadenaError .= "Error, En el registro {$zMatrizCar[$i]['COMIDXXX']}-{$zMatrizCar[$i]['COMCODXX']}-{$zMatrizCar[$i]['COMCSCXX']}-{$zMatrizCar[$i]['COMSEQXX']}-{$zMatrizCar[$i]['CLIIDXXX']}-{$zMatrizCar[$i]['PUCIDXXX']} Alguno de los Campos (Llave) viene vacio. Linea $i <br>";
            $zError++;
            $zMatrizCar[$i]['ERROR']=1;
            $bandera=1;
            
            
          }
          
          $zSqlRep  = "SELECT * ";
				  $zSqlRep .= "FROM $cAlfa.TEMP1005 ";
				  $zSqlRep .= "WHERE ";
				  $zSqlRep .= "COMIDXXX=\"{$zMatrizCar[$i]['COMIDXXX']}\" AND ";
				  $zSqlRep .= "COMCODXX=\"{$zMatrizCar[$i]['COMCODXX']}\" AND ";
				  $zSqlRep .= "COMCSCXX=\"{$zMatrizCar[$i]['COMCSCXX']}\" AND ";
				  $zSqlRep .= "COMSEQXX=\"{$zMatrizCar[$i]['COMSEQXX']}\" AND ";
				  $zSqlRep .= "CLIIDXXX=\"{$zMatrizCar[$i]['CLIIDXXX']}\" AND ";
				  $zSqlRep .= "PUCIDXXX=\"{$zMatrizCar[$i]['PUCIDXXX']}\" ";
				  $zCrsRep  =mysql_query($zSqlRep,$xConexion01);
				  if(mysql_num_rows($zCrsRep)>=1){
				    $zCadenaError .= "Error, El registro {$zMatrizCar[$i]['COMIDXXX']}-{$zMatrizCar[$i]['COMCODXX']}-{$zMatrizCar[$i]['COMCSCXX']}-{$zMatrizCar[$i]['COMSEQXX']}-{$zMatrizCar[$i]['CLIIDXXX']}-{$zMatrizCar[$i]['PUCIDXXX']} Se Encuentra Duplicado. Linea $i <br>";
            $zError++;
            $zMatrizCar[$i]['ERROR']=1;
            $bandera=1;
            
				  }
				  
				  
				  $zSqlCli  = "SELECT * ";
				  $zSqlCli .= "FROM $cAlfa.GRM00350 ";
				  $zSqlCli .= "WHERE ";
				  $zSqlCli .= "CLIIDXXX=\"{$zMatrizCar[$i]['CLIIDXXX']}\" LIMIT 0,1";
				  $zCrsCli  =mysql_query($zSqlCli,$xConexion01);
				  $RCli=mysql_fetch_array($zCrsCli);
				  if($RCli==""){
				    $zSqlPro  = "SELECT * ";
  				  $zSqlPro .= "FROM $cAlfa.GRM00123 ";
  				  $zSqlPro .= "WHERE ";
  				  $zSqlPro .= "PROIDXXX=\"{$zMatrizCar[$i]['CLIIDXXX']}\" LIMIT 0,1";
  				  $zCrsPro  =mysql_query($zSqlPro,$xConexion01);
  				  $RPro=mysql_fetch_array($zCrsPro);
  				  if($RPro==""){
  				    $zCadenaError .= "Error, En el registro {$zMatrizCar[$i]['COMIDXXX']}-{$zMatrizCar[$i]['COMCODXX']}-{$zMatrizCar[$i]['COMCSCXX']}-{$zMatrizCar[$i]['COMSEQXX']}-{$zMatrizCar[$i]['CLIIDXXX']}-{$zMatrizCar[$i]['PUCIDXXX']} El Cliente {$zMatrizCar[$i]['CLIIDXXX']} - {$zMatrizCar[$i]['CLINOMXX']} - No Existe. Linea $i <br>";
              $zError++;
              $zMatrizCar[$i]['ERROR']=1;
              $bandera=1;
              
  				  }
				  }
				  $zSqlPuc  = "SELECT * ";
				  $zSqlPuc .= "FROM $cAlfa.GRM00115 ";
				  $zSqlPuc .= "WHERE ";
				  $zSqlPuc .= "PUCIDXXX =\"{$zMatrizCar[$i]['PUCIDXXX']}\" LIMIT 0,1";
				  $zCrsPuc  =mysql_query($zSqlPuc,$xConexion01);
				  $RPuc=mysql_fetch_array($zCrsPuc);
				  if($RPuc==""){
				    $zCadenaError .= "Error, En el registro {$zMatrizCar[$i]['COMIDXXX']}-{$zMatrizCar[$i]['COMCODXX']}-{$zMatrizCar[$i]['COMCSCXX']}-{$zMatrizCar[$i]['COMSEQXX']}-{$zMatrizCar[$i]['CLIIDXXX']}-{$zMatrizCar[$i]['PUCIDXXX']} La Cuenta {$zMatrizCar[$i]['PUCIDXXX']} No Existe En Conexion GRM. Linea $i <br>";
            $zError++;
            $zMatrizCar[$i]['ERROR']=1;
            $bandera=1;
            
				  }
				  
          /**
           * Fin proceso de Validacion
           */
          
          if($zMatrizCar[$i]['ERROR']==0){
             $zIns1005 = array(array('NAME'=>'COMIDXXX','VALUE'=>trim(strtoupper($zMatrizCar[$i]['COMIDXXX']))  ,'CHECK'=>'SI'),
      							 			     array('NAME'=>'COMCODXX','VALUE'=>trim(strtoupper($zMatrizCar[$i]['COMCODXX']))  ,'CHECK'=>'SI'),
      												 array('NAME'=>'COMCSCXX','VALUE'=>trim(strtoupper($zMatrizCar[$i]['COMCSCXX']))  ,'CHECK'=>'SI'),
      												 array('NAME'=>'COMSEQXX','VALUE'=>trim(strtoupper($zMatrizCar[$i]['COMSEQXX']))  ,'CHECK'=>'SI'),
      												 array('NAME'=>'COMFECXX','VALUE'=>trim(strtoupper($zMatrizCar[$i]['COMFECXX']))  ,'CHECK'=>'NO'),
      												 array('NAME'=>'COMFECVE','VALUE'=>trim(strtoupper($zMatrizCar[$i]['COMFECVE']))  ,'CHECK'=>'NO'),
      												 array('NAME'=>'CLIIDXXX','VALUE'=>trim(strtoupper($zMatrizCar[$i]['CLIIDXXX']))  ,'CHECK'=>'SI'),
      												 array('NAME'=>'PUCIDXXX','VALUE'=>trim(strtoupper($zMatrizCar[$i]['PUCIDXXX']))  ,'CHECK'=>'SI'),
      												 array('NAME'=>'COMNATXX','VALUE'=>trim(strtoupper($zMatrizCar[$i]['COMNATXX']))  ,'CHECK'=>'SI'),
      												 array('NAME'=>'COMSALDO','VALUE'=>trim(strtoupper($zMatrizCar[$i]['COMSALDO']))  ,'CHECK'=>'SI'),
      												 array('NAME'=>'CCOIDXXX','VALUE'=>trim(strtoupper($zMatrizCar[$i]['CCOIDXXX']))  ,'CHECK'=>'SI'),
      												 array('NAME'=>'COMXXXXX','VALUE'=>trim(strtoupper($zMatrizCar[$i]['COMXXXXX']))	 ,'CHECK'=>'NO'),
      												 array('NAME'=>'COMSALEX','VALUE'=>trim(strtoupper($zMatrizCar[$i]['COMSALEX']))	 ,'CHECK'=>'NO'),
      												 array('NAME'=>'REGESTXX','VALUE'=>trim(strtoupper($zMatrizCar[$i]['REGESTXX']))	 ,'CHECK'=>'SI'));
      												 
     				if (f_MySql("INSERT","TEMP1005",$zIns1005,$xConexion01,$cAlfa)) {
     				}
          }  
        }
                  
        if($zError==0){       
          
          $zSqlDel = "DELETE FROM $cAlfa.GRM01005";
				  mysql_query($zSqlDel,$xConexion01);
          
          $cSqlUpd = "INSERT INTO $cAlfa.GRM01005 SELECT * FROM $cAlfa.TEMP1005 ";
          $nCrsTbl = mysql_query($cSqlUpd,$xConexion01);
          
          $zSqlDel = "DROP TABLE $cAlfa.TEMP1005";
				  mysql_query($zSqlDel,$xConexion01);
          
          ?>
          <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
					<script languaje = "javascript">
					  document.forms['frgrm'].submit();
					</script>
				<?}else{
				  
				  $zSqlDel = "DROP TABLE $cAlfa.TEMP1005";
				  mysql_query($zSqlDel,$xConexion01);
				  
				  ?>
}
				<table width="900" cellspacing="0" cellpadding="0" border="0" align="center"><tr><td>
		      <table border="1" cellspacing="0" cellpadding="0" width="95%" align=center>
				  <tr bgcolor = '#E1F3F7' height="20">
		        <td class="letra7" width=100% align="center"><font size=2><b>ERRORES EN EL PROCESO DE ACTUALIZACION (CXP)</b></font></td>
		      </tr>
		      </table>
		      <table border="1" cellspacing="0" cellpadding="0" width="95%" align=center>
		      <tr height="20">
    		    <td class="letra7" width=10% ><font color=black>
    		    <? echo $zCadenaError; ?>
    		    </font></td>
  		    </tr>
          </table>
          <table border="1" cellspacing="0" cellpadding="0" width="95%" align=center>
				  <tr bgcolor = '#E1F3F7' height="20">
		        <td class="letra7" width=100% align="center"><font size=1><b>Realize el Envio al Dpto de Contabilidad para que Estos sean corregidos.</b></font></td>
		      </tr>
		      </table>
				</td></tr></table>
				<table border="0" cellpadding="0" cellspacing="0" width="1000">
  					<tr>
						<td width= "909" height="21"></td>   
						<td width= "91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = 'javascript:uRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
					</tr>
  				</table>
				<?}
 		  break;
  	  default: ?>
  			<br><br>
  			<center>
  			</center>
  			<center>
  				<table cellpadding="0" cellspacing="0" border="0" width="400">
  					<tr>
  						<td width="400" align="center">
  							<fieldset>
  							<legend>Actualizaci&oacute;n Cuentas por Pagar</legend>
  							<form method='POST' enctype='multipart/form-data' action='frtracxp.php'>
  								<input type = 'hidden' name = 'zBand' value = "1">
  								<input type = 'hidden' name = 'kUsrId' value = '<?php echo $_COOKIE['kUsrId'] ?>'>
  								Archivo: <input type='file' class="letra" name='upfile'>
  								<input type='submit' class="letra" value='Aceptar' style="height:18">
  							</form>
  							</fieldset>
  						</td>
  					</tr>
  				</table>
  					<table border="0" cellpadding="0" cellspacing="0" width="300">
  					<tr>
						<td width= "209" height="21"></td>   
						<td width= "91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = 'javascript:uRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
					</tr>
  				</table>
  			</center>
  			<br>
  		<?php break; 
  	} 	 
	?>
	</body>
</html>