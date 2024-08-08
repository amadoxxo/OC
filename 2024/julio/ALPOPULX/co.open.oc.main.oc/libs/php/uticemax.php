<?php
	/**
	 * uticemax.php : Utility de Clases para el Envio de Correo por SMTP
	 *
	 * Este script contiene la colecciones de clases para el envio de correo por SMTP utilizando la clase PHPMailer
	 *
	 * @package openComex
	 */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  if (!in_array($OPENINIT['pathdr']."/opencomex/config/config.php",get_included_files(),true)) {
    include($OPENINIT['pathdr']."/opencomex/config/config.php");
  }

  if (!in_array($OPENINIT['pathdr']."/opencomex/class/smtp/SMTP.php",get_included_files(),true)) {
    include($OPENINIT['pathdr']."/opencomex/class/smtp/SMTP.php");
  }
  if (!in_array($OPENINIT['pathdr']."/opencomex/class/smtp/phpmailer.php",get_included_files(),true)) {
    include($OPENINIT['pathdr']."/opencomex/class/smtp/phpmailer.php");
  }

  class cEnvioEmail {
    /**
     * Permite enviar los correo por SMTP
     */
    function fnEviarEmailSMTP($pArrayDatos) {
      /**
       * Como debe venir cargado la matriz?
       *
       * La matriz debe traer los siguentes datos cargados:
       *
       * $pArrayDatos['basedato'] // Base de datos
       * $pArrayDatos['asuntoxx'] // Asunto
       * $pArrayDatos['etiqueta'] // Etiqueta From
       * $pArrayDatos['mensajex'] // Cuerpo de Correo (en HTML)
       * $pArrayDatos['adjuntos'] // Array con el nombre del archivo [n][archivox] y la ruta completa del archivo [n][rutaxxxx]
       * $pArrayDatos['destinos'] // Array con los correos de destino
       * $pArrayDatos['destincc'] // Array con los correos con copia
       * $pArrayDatos['replytox'] // Array con los correos de respuesta
       * $pArrayDatos['servidor'] // Array con datos particulares para conexion SMTP, posiciones: ['hostxxxx'] => Host, [portxxxx] => Puerto, [userxxxx] => Usuario, [password] => Password
      */

      /**
       * Matriz para retornar los datos.
       * @var array
       */
      $vReturn = array();
      $vReturn[0] = "";

      /*
      * Switch para saber si hubo error
      * @var nSwitch
      */
      $nSwitch = 0;

      // Iniciando conexion por cada proceso
      $xConexion01 = $this->fnConectarDBSMTP();
      if (!$xConexion01) {
        $nSwitch = 1;
        $vReturn[] = "Error al Conectar con la Base de Datos.";
      }

      // Validando el asunto
      if (empty($pArrayDatos['asuntoxx'])) {
        $nSwitch = 1;
        $vReturn[] = "El Asunto no puede ser vacio.";
      }

      // Validando correos de destino
      for ($i=0; $i < count($pArrayDatos['destinos']); $i++){
        if (!preg_match('/^[^0-9][a-zA-Z0-9_.-]+([.][a-zA-Z0-9_.-]+)*[@][a-zA-Z0-9_.-]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',trim($pArrayDatos['destinos'][$i]))) {
          $nSwitch = 1;
          $vReturn[] = "El Correo [".trim($pArrayDatos['destinos'][$i])."] no es valido.";
        }
      }

      // Validando correos con copia
      for ($i=0; $i < count($pArrayDatos['destincc']); $i++){
        if (!preg_match('/^[^0-9][a-zA-Z0-9_.-]+([.][a-zA-Z0-9_.-]+)*[@][a-zA-Z0-9_.-]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',trim($pArrayDatos['destincc'][$i]))) {
          $nSwitch = 1;
          $vReturn[] = "El Correo con copia [".trim($pArrayDatos['destincc'][$i])."] no es valido.";
        }
      }

      // Validando correos de respuesta
      for ($i=0; $i < count($pArrayDatos['replytox']); $i++){
        if (!preg_match('/^[^0-9][a-zA-Z0-9_.-]+([.][a-zA-Z0-9_.-]+)*[@][a-zA-Z0-9_.-]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',trim($pArrayDatos['replytox'][$i]))) {
          $nSwitch = 1;
          $vReturn[] = "El Correo [".trim($pArrayDatos['replytox'][$i])."] no es valido.";
        }
      }

      if ($nSwitch == 0) {
        // Inicializando las variables utilizadas para el envio de correo
        $cHost     = OC_SMTP_HOST;
        $cPort     = OC_SMTP_PORT;
        $cUser     = OC_SMTP_USER; 
        $cPassword = OC_SMTP_PASSWORD;
        $cEtiqueta = "openComex";
        
        if ($pArrayDatos['servidor']['hostxxxx'] != "") {
          $cHost     = $pArrayDatos['servidor']['hostxxxx'];
          $cPort     = $pArrayDatos['servidor']['portxxxx'];
          $cUser     = $pArrayDatos['servidor']['userxxxx'];
          $cPassword = $pArrayDatos['servidor']['password'];
        } else {
          if (!empty($pArrayDatos['basedato'])) {
            $cVariables  = "\"system_smtp_host\",";
            $cVariables .= "\"system_smtp_port\",";
            $cVariables .= "\"system_smtp_user\",";
            $cVariables .= "\"system_smtp_password\",";
            $cVariables .= "\"system_smtp_from_nc\"";
            
            // Validando si la base de datos tiene activo el modulo de facturacion automatica
            $qVarSys  = "SELECT stridxxx, strvlrxx ";
            $qVarSys .= "FROM {$pArrayDatos['basedato']}.sys00002 ";
            $qVarSys .= "WHERE ";
            $qVarSys .= "stridxxx IN ($cVariables)";
            $xVarSys  = mysql_query($qVarSys,$xConexion01);
            // echo $qVarSys." ~ ".mysql_num_rows($xVarSys)."\n";
            while ($xRVS = mysql_fetch_assoc($xVarSys)) {
              $vSysStr[$xRVS['stridxxx']] = $xRVS['strvlrxx'];
            }

            if ($vSysStr['system_smtp_host'] != "") {
              $cHost     = $vSysStr['system_smtp_host'];
              $cPort     = $vSysStr['system_smtp_port'];
              $cUser     = $vSysStr['system_smtp_user'];
              $cPassword = $vSysStr['system_smtp_password'];
              $cEtiqueta = ($vSysStr['system_smtp_from_nc'] != "") ? $vSysStr['system_smtp_from_nc'] : $cEtiqueta;
            }
          }
        }

        $cEtiqueta = ($pArrayDatos['etiqueta'] != "") ? $pArrayDatos['etiqueta'] : $cEtiqueta;
        
        switch ($pArrayDatos['basedato']) {
          case "DESIACOSIP": case "TESIACOSIP": case "SIACOSIA":
          case "DEROLDANLO": case "TEROLDANLO": case "ROLDANLO":

            # segim OS Windows o Mac o Linux
            switch (strtoupper(substr(PHP_OS,0,3))) {
              case "MAC":
                $cFin = "\r" ;
                break;
              case "WIN":
                $cFin = "\r\n" ;
                break;
              default:
                $cFin = "\n" ;
                break;
            }

            if (count($pArrayDatos['adjuntos']) > 0) {
              // boundary
              $semi_rand = md5(time());
              $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

              $cHeaders  = "MIME-Version: 1.0$cFin";
              $cHeaders .= "From: $cEtiqueta <no-reply@opentecnologia.com.co>$cFin";
              $cHeaders .= "Content-type: multipart/mixed;";
              $cHeaders .= "boundary=\"{$mime_boundary}\"";

              $cMsj  = "--{$mime_boundary}\n";
              $cMsj .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
              $cMsj .= "Content-Transfer-Encoding: 7bit\n\n" . $pArrayDatos['mensajex'] . "\n\n";
              $cMsj .= "--{$mime_boundary}\n";

              // Preparo el archivo adjunto
              for ($nA=0; $nA<count($pArrayDatos['adjuntos']); $nA++) {
                $file = fopen($pArrayDatos['adjuntos'][$i]['rutaxxxx'],"rb");
                $data = fread($file,filesize($pArrayDatos['adjuntos'][$i]['rutaxxxx']));
                fclose($file);
                $data = chunk_split(base64_encode($data));
                $name = $pArrayDatos['adjuntos'][$i]['archivox'];
                $cMsj .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$name\"\n" .
                "Content-Disposition: attachment;\n" . " filename=\"$name\"\n" .
                "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
                $cMsj .= "--{$mime_boundary}--\n";
              }
            } else {
              $cHeaders  = "MIME-Version: 1.0$cFin";
              $cHeaders .= "Content-type: text/html; charset=iso-8859-1$cFin";
              $cHeaders .= "From: $cEtiqueta <no-reply@opentecnologia.com.co>$cFin";

              $cMsj = $pArrayDatos['mensajex'];
            }

            //Destinatarios con copia
            $cCopia = "";
            for ($i=0; $i < count($pArrayDatos['destincc']); $i++) {
              $cCopia .= "{$pArrayDatos['destincc'][$i]}, ";
            }
            $cCopia = substr($cCopia, 0, -2);
            if ($cCopia != "") {
              $cHeaders .= "Cc: $cCopia$cFin";
            }
            // Correo de respuesta
            $cReplyTo = "";
            for ($i=0; $i < count($pArrayDatos['replytox']); $i++){
              $cReplyTo = "{$pArrayDatos['replytox'][$i]}, ";
            }
            $cReplyTo = substr($cReplyTo, 0, -2);
            if ($cReplyTo != "") {
              $cHeaders .= "Reply-To: $cReplyTo$cFin";
            }
            //Destinatarios
            $cCorreos = "";
            for ($i=0; $i < count($pArrayDatos['destinos']); $i++) {
              $cCorreos = "{$pArrayDatos['destinos'][$i]}, ";
            }
            $cCorreos = substr($cCorreos, 0, -2);

            //Enivando el correo
            $xMail = mail($cCorreos,$pArrayDatos['asuntoxx'],$cMsj,$cHeaders);
            if(!$xMail) {
              $nSwitch = 1;
              $vReturn[] = "Error al enviar el correo.";
            }
          break;
          default:
            $mail = new PHPMailer(true);
            $mail->setLanguage('es');
    
            try {
              // Server settings
              $mail->SMTPDebug = 0;           // Enable verbose debug output (1 activa log, 0 inactiva log)
              $mail->isSMTP();                // Set mailer to use SMTP
              $mail->Host = "$cHost";         // Specify main and backup SMTP servers
              $mail->SMTPAuth = true;         // Enable SMTP authentication
              $mail->Username = "$cUser";  	  // SMTP username
              $mail->Password = "$cPassword"; // SMTP password
              #$mail->SMTPSecure = "tls";      // Enable TLS encryption, `ssl` also accepted
              $mail->SMTPAutoTLS = false;		  // Activar si se requiere desactivar completamente TLS (sin cifra
              $mail->Port = "$cPort";         // TCP port to connect to
              $mail->SetFrom("$cUser", "$cEtiqueta");
    
              //Destinatarios
              for ($i=0; $i < count($pArrayDatos['destinos']); $i++) {
                $mail->AddAddress($pArrayDatos['destinos'][$i]); // Existe un segundo parametro para indicar el nombre del destinatario
              }
              //Destinatarios con copia
              for ($i=0; $i < count($pArrayDatos['destincc']); $i++) {
                $mail->addCC($pArrayDatos['destincc'][$i]); // Existe un segundo parametro para indicar el nombre del destinatario
              }
              // Correo de respuesta
              for ($i=0; $i < count($pArrayDatos['replytox']); $i++){
                $mail->addReplyTo($pArrayDatos['replytox'][$i]); // Existe un segundo parametro para indicar el nombre del correo de respuesta
              }
              // Adjuntos
              for ($i=0; $i < count($pArrayDatos['adjuntos']); $i++){
                $mail->AddAttachment($pArrayDatos['adjuntos'][$i]['rutaxxxx'],$pArrayDatos['adjuntos'][$i]['archivox']);
              }
              // Asunto
              $mail->Subject = $pArrayDatos['asuntoxx'];
              // contenido
              $mail->isHTML(true); // Set email format to HTML
              $mail->Body    = $pArrayDatos['mensajex'];
          
              $mail->send();
              if (!empty($mail->ErrorInfo)) {
                $nSwitch = 1;
                $vReturn[] = "Error al enviar el correo: ".$mail->ErrorInfo;
              }
              
            } catch (Exception $e) {
              $nSwitch = 1;
              $vReturn[] = "Error al enviar el correo: ".$mail->ErrorInfo;
            }
          break;
        }
      }

      if ($nSwitch == 0) {
        $vReturn[0] = "true";
      } else {
        $vReturn[0] = "false";
      }

      return $vReturn;
    }

    /**
     * Metodo que realiza la conexion
     */
    function fnConectarDBSMTP(){
      $xConexion99 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT);
      return $xConexion99;
    }##function fnConectarDB(){##

    /**
     * Metodo que realiza el reinicio de la conexion
     */
    function fnReiniciarConexionDBSMTP($pConexion){
      mysql_close($pConexion);
      $xConexion01 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT,TRUE);
      return $xConexion01;
    }##function fnReiniciarConexionDB(){##
  }
?>