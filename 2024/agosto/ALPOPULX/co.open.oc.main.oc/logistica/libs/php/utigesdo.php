<?php

  /**
   * utigesdo.php : Utility de Clases del Modulo Logistica - Gestor Documental
   *
   * Este script contiene la colecciones de clases para el Modulo Logistica - Gestor Documental de openComex
   * Permite realizar la coexion con openECM para radicar los documentos anexos.
   * 
   * @author Juan Jose Trujillo <juan.trujillo@openits.co>
   * @package opencomex
   * @version 1.0
   */
  
  include("../../../libs/php/utility.php");
  include('../../../libs/php/uticecmx.php');

  if (!in_array($OPENINIT['pathdr']."/opencomex/class/EnDecryptText.php",get_included_files(),true)) {
    include ($OPENINIT['pathdr']."/opencomex/class/EnDecryptText.php");
  }

  ini_set('error_reporting', E_ERROR);
  ini_set("display_errors","1");

  class cIntegracionGestorDocumentalopenECM {
    /**
     * Radica los documentos anexos en la plataforma de openECM.
     */
    function fnRadicarDocumentosAnexos($pArrayParametros) {

      global $vSysStr; global $cAlfa; global $xConexion01;

      /**
       * Recibe como Parametro una Matriz con las siguientes posiciones:
       *
       * $pArrayParametros['idcompro'] //Id del Comprobante MIF/CERTIFICACION/PEDIDO
       * $pArrayParametros['anioxxxx'] //Anio del comprobante
       * $pArrayParametros['procesox'] //Indica si el proceso viene de la mif/certificacion/pedido
       * $pArrayParametros['datos']
       * $pArrayParametros['datos']['tdoidecm'] //Id del tipo documental
       * $pArrayParametros['datos']['rutaxxxx'] //Archivo
       * $pArrayParametros['datos']['nomfilex'] //Nombre del archivo
       */

      /**
       * Variable para hacer el retorno.
       * 
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = "";
      $mReturn[1] = "";
  
      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var int
       */
      $nSwitch = 0;

      /**
       * Anio del comprobante.
       * 
       * @var string
       */
      $cPerAno = $pArrayParametros['anioxxxx'];

      $ObjIntegracionAPIopenECM = new cIntegracionAPIopenECM();
      $mReturnGenerarTokenEcm   = $ObjIntegracionAPIopenECM->fnGenerarTokenEcm();

      if ($mReturnGenerarTokenEcm[0] == 'false') {
        $nSwitch = 1;
        for ($n=2; $n < count($mReturnGenerarTokenEcm); $n++) {
          $mReturn[count($mReturn)] = utf8_decode($mReturnGenerarTokenEcm[$n]);
        }
      }

      // Se válida el Id del Comprobante
      if ($pArrayParametros['idcompro'] == "") {
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "El ID del Comprobante No Puede ser Vacio";
      }

      // Se válida el Año del Comprobante
      if ($pArrayParametros['anioxxxx'] == "") {
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "El Año del Comprobante No Puede ser Vacio";
      }

      // Se válida la Extensiom del Archivo
      $vExtPermitidas = array("jpg", "jpeg", "pdf", "doc", "docx", "xls", "xlsx");
      foreach ($pArrayParametros['datos'] as $dato) {
        $vExtension = explode('.', $dato['nomfilex']);
        $cExtension = $vExtension[count($vExtension) - 1];

        if (!in_array($cExtension, $vExtPermitidas)) {
          $nSwitch = 1;
          $mReturn[count($mReturn)] = "La Extension del Archivo no es Permitida para el Documento con Nombre [{$dato['nomfilex']}]";
        }
      }

      if ($nSwitch == 0) {
        switch ($pArrayParametros['procesox']) {
          case 'MIF':
            // Valida que exista la MIF
            $vComprobante = array();
            $qMif  = "SELECT ";
            $qMif .= "$cAlfa.lmca$cPerAno.mifidxxx, ";
            $qMif .= "$cAlfa.lmca$cPerAno.comprexx, ";
            $qMif .= "$cAlfa.lmca$cPerAno.comcscxx, ";
            $qMif .= "$cAlfa.lmca$cPerAno.cliidxxx ";
            $qMif .= "FROM $cAlfa.lmca$cPerAno ";
            $qMif .= "WHERE ";
            $qMif .= "mifidxxx = \"{$pArrayParametros['idcompro']}\" AND ";
            $qMif .= "regestxx = \"ENPROCESO\" LIMIT 0,1";
            $xMif  = f_MySql("SELECT","",$qMif,$xConexion01,"");
            // echo $qMif . "<br>";
            if (mysql_num_rows($xMif) > 0) {
              $vComprobante = mysql_fetch_array($xMif);
            } else {
              $nSwitch = 1;
              $mReturn[count($mReturn)] = "La MIF con ID [{$pArrayParametros['idcompro']}] no existe o no se encuentra EN PROCESO";
            }
          break;
          case 'CERTIFICACION':
            // Valida que exista la MIF
            $vComprobante = array();
            $qCertificacion  = "SELECT ";
            $qCertificacion .= "$cAlfa.lcca$cPerAno.ceridxxx, ";
            $qCertificacion .= "$cAlfa.lcca$cPerAno.comprexx, ";
            $qCertificacion .= "$cAlfa.lcca$cPerAno.comcscxx, ";
            $qCertificacion .= "$cAlfa.lcca$cPerAno.cliidxxx ";
            $qCertificacion .= "FROM $cAlfa.lcca$cPerAno ";
            $qCertificacion .= "WHERE ";
            $qCertificacion .= "ceridxxx = \"{$pArrayParametros['idcompro']}\" AND ";
            $qCertificacion .= "regestxx = \"ENPROCESO\" LIMIT 0,1";
            $xCertificacion  = f_MySql("SELECT","",$qCertificacion,$xConexion01,"");
            // echo $qCertificacion . "<br>";
            if (mysql_num_rows($xCertificacion) > 0) {
              $vComprobante = mysql_fetch_array($xCertificacion);
            } else {
              $nSwitch = 1;
              $mReturn[count($mReturn)] = "La Certificacion con ID [{$pArrayParametros['idcompro']}] no existe o no se encuentra EN PROCESO";
            }
          break;
          case 'PEDIDO':
            // Valida que exista la MIF
            $vComprobante = array();
            $qPedido  = "SELECT ";
            $qPedido .= "$cAlfa.lpca$cPerAno.pedidxxx, ";
            $qPedido .= "$cAlfa.lpca$cPerAno.comprexx, ";
            $qPedido .= "$cAlfa.lpca$cPerAno.comcscxx, ";
            $qPedido .= "$cAlfa.lpca$cPerAno.cliidxxx ";
            $qPedido .= "FROM $cAlfa.lpca$cPerAno ";
            $qPedido .= "WHERE ";
            $qPedido .= "pedidxxx = \"{$pArrayParametros['idcompro']}\" AND ";
            $qPedido .= "regestxx = \"PROVISIONAL\" LIMIT 0,1";
            $xPedido  = f_MySql("SELECT","",$qPedido,$xConexion01,"");
            // echo $qPedido . "<br>";
            if (mysql_num_rows($xPedido) > 0) {
              $vComprobante = mysql_fetch_array($xPedido);
            } else {
              $nSwitch = 1;
              $mReturn[count($mReturn)] = "El Pedido con ID [{$pArrayParametros['idcompro']}] no existe o no se encuentra PROVISIONAL";
            }
          break;
          default:
            break;
        }

        if ($nSwitch == 0) {
          // Url de openECM para radicar documentos
          $vData['apiurlxx'] = $vSysStr['system_url_servidor_integracion_ecm'].'/81/api/radicar-documentos/radicar-documento-plataforma-externa';

          // Se arman las propiedades
          $vPropiedades[] = [
            'nit_cliente'                                            => $vComprobante['cliidxxx'],
            'prefijo_'.strtolower($pArrayParametros['procesox'])     => $vComprobante['comprexx'],
            'consecutivo_'.strtolower($pArrayParametros['procesox']) => $vComprobante['comcscxx']
          ];

          // Se arma el array con la informacion del archivo
          $mDocumentos = array();
          foreach ($pArrayParametros['datos'] as $dato) {
            // Obtiene el base64 del archivo
            if (file_exists($dato['rutaxxxx'] . '/' . $dato['nomfilex'])) {
              $cArchivoBase = base64_encode(file_get_contents($dato['rutaxxxx'] . '/' . $dato['nomfilex']));
            }

            $mDocumentos[count($mDocumentos)] = [
              'tdoId'              => $dato['tdoidecm'],
              'fat_propiedades'    => $vPropiedades,
              'fat_nombre_content' => $dato['nomfilex'],
              'file_base64'        => $cArchivoBase
            ];
          }

          // Parámetros
          $vDatos = array();
          $vDatos['contenido'] = $mDocumentos;
          $mPost = json_encode($vDatos);

          //Headers de la peticion
          $cHeaders = array('Content-Type: application/json',
                            'Content-Length: '.strlen($mPost),
                            'X-Requested-With XMLHttpRequest',
                            'Accept application/json',
                            'Authorization: Bearer '.$mReturnGenerarTokenEcm[1]);


          // Crear un nuevo recurso cURL.
          $ch = curl_init();

          // Dirección URL de la API.
          curl_setopt($ch,CURLOPT_URL,$vData['apiurlxx']);

          // TRUE para hacer un HTTP POST normal.
          // Este POST del tipo application/x-www-form-urlencoded, el más común en formularios HTML.
          curl_setopt($ch,CURLOPT_POST, 1);

          //sin verificacion https en mabiente de pruebas
          if (substr_count($_SERVER['SCRIPT_FILENAME'],"pruebas") == 1 || substr_count($_SERVER['SCRIPT_FILENAME'],"oc6.openits.io") == 1) {
            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
          }

          // Todos los datos para enviar vía HTTP "POST".
          curl_setopt($ch,CURLOPT_POSTFIELDS,$mPost);

          // TRUE para devolver el resultado de la transferencia como
          // string del valor de curl_exec() en lugar de mostrarlo directamente.
          curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

          // Número de segundos a esperar cuando se está intentado conectar.
          // Use 0 para esperar indefinidamente.
          curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);

          // Número máximo de segundos permitido para ejectuar funciones cURL.
          curl_setopt($ch,CURLOPT_TIMEOUT, 20);

          //para seguir cualquier encabezado "Location: " que el servidor envíe como parte del encabezado HTTP (observe la recursividad, PHP seguirá tantos 
          //header "Location: "
          curl_setopt($ch,CURLOPT_FOLLOWLOCATION, 1);

          //Método de petición personalizado a usar en lugar de "GET" o "HEAD" cuando se realiza una petición HTTP
          curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'POST');

          // Habilitar Headers en retorno.
          curl_setopt($ch, CURLOPT_HEADER, 1);

          // Un array de campos a configurar para el header HTTP,
          // en el formato: array('Content-type: text/plain', 'Content-length: 100')
          curl_setopt($ch,CURLOPT_HTTPHEADER, $cHeaders);

          // Establece una sesión cURL
          $cResponse = curl_exec($ch);

          // Código de respuesta.
          $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

          // Cerando una sesion cURL
          curl_close ($ch);

          $vResponse = explode("\n",trim($cResponse));

          for ($n=0; $n<count($vResponse); $n++) {
            //Si es la ultima posicion del array, contiene la informacion a retornar
            if ($n == count($vResponse)-1) {
              $oResponseJson    = json_decode($vResponse[$n], true);
              $mResponseData    = $oResponseJson['data'];
              $mResponseErrors  = $oResponseJson['errors'];
            }
          }

          // Se válida el estado de la respuesta.
          if ($httpcode != 200) {
            $nSwitch = 1;
            for ($n=0; $n < count($mResponseErrors); $n++) {
              $mReturn[count($mReturn)] = "En openECM, ".utf8_decode($mResponseErrors[$n]);
            }
          }
        }
      }

      // Actualiza el campo de documentos anexos en la tabla anualizada
      if ($nSwitch == 0) {
        $vDatos = array();
        $vDatos['idcompro'] = $pArrayParametros['idcompro'];
        $vDatos['anioxxxx'] = $pArrayParametros['anioxxxx'];
        $vDatos['procesox'] = $pArrayParametros['procesox'];
        $mRespuesta = $this->fnActualizaCamposAnexos($vDatos);

        if ($mRespuesta[0] == 'false') {
          $nSwitch = 1;
          for ($n=2; $n < count($mRespuesta); $n++) {
            $mReturn[count($mReturn)] = $mRespuesta[$n];
          }
        }
      }

      if ($nSwitch == 0) {
        $mReturn[0] = "true"; 
        $mReturn[1] = $mResponseData;
      } else {
        $mReturn[0] = "false";
      }

      return $mReturn;
    }

    /**
     * Permite actualizar los campos de documentos anexos en las tablas anualizadas.
     */
    function fnActualizaCamposAnexos($pArrayParametros) {

      global $cAlfa; global $xConexion01;

      /**
       * Recibe como Parametro una Matriz con las siguientes posiciones:
       *
       * $pArrayParametros['idcompro'] //Id del Comprobante MIF/CERTIFICACION/PEDIDO
       * $pArrayParametros['anioxxxx'] //Anio del comprobante
       * $pArrayParametros['procesox'] //Indica si el proceso viene de la mif/certificacion/pedido
       */

      /**
       * Variable para hacer el retorno.
       * 
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = "";
      $mReturn[1] = "";
  
      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var int
       */
      $nSwitch = 0;

      /**
       * Anio del comprobante.
       * 
       * @var string
       */
      $cPerAno = $pArrayParametros['anioxxxx'];

      switch ($pArrayParametros['procesox']) {
        case 'MIF':
          $qUpdate = array(array('NAME' => 'mifanexx', 'VALUE' => 'SI'                          ,'CHECK' => 'SI'),
                           array('NAME' => 'mifidxxx', 'VALUE' => $pArrayParametros['idcompro'] ,'CHECK' => 'WH'));

          if (!f_MySql("UPDATE","lmca$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
            $nSwitch = 1;
            $mReturn[count($mReturn)] = "Error al Actualizar el Campo de Documento Anexos en la MIF";
          }
        break;
        case 'CERTIFICACION':
          $qUpdate = array(array('NAME' => 'ceranexx', 'VALUE' => 'SI'                          ,'CHECK' => 'SI'),
                           array('NAME' => 'ceridxxx', 'VALUE' => $pArrayParametros['idcompro'] ,'CHECK' => 'WH'));

          if (!f_MySql("UPDATE","lcca$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
            $nSwitch = 1;
            $mReturn[count($mReturn)] = "Error al Actualizar el Campo de Documento Anexos en la CERTIFICACION";
          }
        break;
        case 'PEDIDO':
          $qUpdate = array(array('NAME' => 'pedanexx', 'VALUE' => 'SI'                          ,'CHECK' => 'SI'),
                           array('NAME' => 'pedidxxx', 'VALUE' => $pArrayParametros['idcompro'] ,'CHECK' => 'WH'));

          if (!f_MySql("UPDATE","lpca$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
            $nSwitch = 1;
            $mReturn[count($mReturn)] = "Error al Actualizar el Campo de Documento Anexos en el PEDIDO";
          }
        break;
        default:
          break;
      }

      if ($nSwitch == 0) {
        $mReturn[0] = "true"; 
      } else {
        $mReturn[0] = "false";
      }
    }

    /**
     * Obtiene los documentos anexos de la plataforma openECM.
     */
    function fnVerDocumentosAnexos($pArrayParametros) {
      global $vSysStr; global $cAlfa; global $xConexion01;

      /**
       * Recibe como Parametro una Matriz con las siguientes posiciones:
       *
       * $pArrayParametros['particion_inicial'] // Año de particion inicial
       * $pArrayParametros['particion_final']   // Año de particion final
       * $pArrayParametros['grupo_documental']  // Id del grupo documental
       * $pArrayParametros['numero_operacion']  // Numero de operación
       * $pArrayParametros['versiones']         // LAST
       */

      /**
       * Variable para hacer el retorno.
       * 
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = ""; // resultado del proceso true o false
      $mReturn[1] = ""; // nombre de la tabla temporal
  
      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var int
       */
      $nSwitch = 0;

      $ObjIntegracionAPIopenECM = new cIntegracionAPIopenECM();
      $mReturnGenerarTokenEcm   = $ObjIntegracionAPIopenECM->fnGenerarTokenEcm();

      if ($mReturnGenerarTokenEcm[0] == 'false') {
        $nSwitch = 1;
        for ($n=2; $n < count($mReturnGenerarTokenEcm); $n++) {
          $mReturn[count($mReturn)] = utf8_decode($mReturnGenerarTokenEcm[$n]);
        }
      }

      if ($nSwitch == 0) {
        // Url de openECM para descargar contenido
        $cApiUrl = $vSysStr['system_url_servidor_integracion_ecm'].'/81/api/contenidos/descargar-contenido-openetl';

        // Parámetros
        $vDatos = array();
        $vDatos['open_etl'] = $pArrayParametros;
        $mPost = json_encode($vDatos);

          //Headers de la peticion
        $cHeaders = array('Content-Type: application/json',
                          'X-Requested-With XMLHttpRequest',
                          'Accept application/json',
                          'Authorization: Bearer '.$mReturnGenerarTokenEcm[1]);

        // Crear un nuevo recurso cURL.
        $ch = curl_init();

        // Dirección URL de la API.
        curl_setopt($ch,CURLOPT_URL,$cApiUrl);

        // TRUE para hacer un HTTP POST normal.
        // Este POST del tipo application/x-www-form-urlencoded, el más común en formularios HTML.
        curl_setopt($ch,CURLOPT_POST, 1);

        //sin verificacion https en mabiente de pruebas
        if (substr_count($_SERVER['SCRIPT_FILENAME'],"pruebas") == 1 || substr_count($_SERVER['SCRIPT_FILENAME'],"oc6.openits.io") == 1) {
          curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
          curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        // Todos los datos para enviar vía HTTP "POST".
        curl_setopt($ch,CURLOPT_POSTFIELDS,$mPost);

        // TRUE para devolver el resultado de la transferencia como
        // string del valor de curl_exec() en lugar de mostrarlo directamente.
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        // Número de segundos a esperar cuando se está intentado conectar.
        // Use 0 para esperar indefinidamente.
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);

        // Número máximo de segundos permitido para ejectuar funciones cURL.
        curl_setopt($ch,CURLOPT_TIMEOUT, 20);

        //para seguir cualquier encabezado "Location: " que el servidor envíe como parte del encabezado HTTP (observe la recursividad, PHP seguirá tantos 
        //header "Location: "
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION, 1);

        //Método de petición personalizado a usar en lugar de "GET" o "HEAD" cuando se realiza una petición HTTP
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'POST');

        // Habilitar Headers en retorno.
        curl_setopt($ch, CURLOPT_HEADER, 1);

        // Un array de campos a configurar para el header HTTP,
        // en el formato: array('Content-type: text/plain', 'Content-length: 100')
        curl_setopt($ch,CURLOPT_HTTPHEADER, $cHeaders);

        // Establece una sesión cURL
        $cResponse = curl_exec($ch);

        // Código de respuesta.
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Cerando una sesion cURL
        curl_close ($ch);

        $vResponse = explode("\n",trim($cResponse));
        for ($n=0; $n<count($vResponse); $n++) {
          //Si es la ultima posicion del array, contiene la informacion a retornar
          if ($n == count($vResponse)-1) {
            $oResponseJson    = json_decode($vResponse[$n], true);
            $mResponseData    = $oResponseJson['data'];
            $mResponseErrors  = $oResponseJson['errors'];
          }
        }

        // Se válida el estado de la respuesta.
        if ($httpcode != 200) {
          $nSwitch = 1;
          for ($n=0; $n < count($mResponseErrors); $n++) {
            $mReturn[count($mReturn)] = "En openECM, ".utf8_decode($mResponseErrors[$n]);
          }
        }
      }

      if ($nSwitch == 0) {
        
        $objEstructura = new cEstructurasGestorDocumentalopenECM();
        $mReturnVerAnexos = $objEstructura->fnCrearEstructurasVerAnexosEcm($cAlfa);

        if ($mReturnVerAnexos[0] == 'false') {
          for ($n=2; $n < count($mReturnVerAnexos); $n++) {
            $mReturn[count($mReturn)] = utf8_decode($mReturnVerAnexos[$n]);
          }
        } else {
          $mReturn[0] = "true";
          $mReturn[1] = $mReturnVerAnexos[1];
          
          for ($i=0; $i < count($mResponseData); $i++) { 
            $nTipDoc = $mResponseData[$i]['tipo_documental'];
            $qTipDoc  = "SELECT tdodesxx ";
            $qTipDoc .= "FROM $cAlfa.lpar0162 ";
            $qTipDoc .= "WHERE ";
            $qTipDoc .= "tdoidecm = \"$nTipDoc\"  LIMIT 0,1";
            $xTipDoc = f_MySql("SELECT","",$qTipDoc,$xConexion01,"");

            $cTipoDoc = "";
            if (mysql_num_rows($xTipDoc) > 0) {
              $xTDO = mysql_fetch_array($xTipDoc);
              $cTipoDoc = $xTDO['tdodesxx'];
            }

            // Construye el arreglo para enviar a guardar en la tabla tempoeral
            $mInsert = array();
            $mInsert['TABLA'] = $mReturnVerAnexos[1];
            $mInsert['ANEXOS'] = array();
            // Obtiene los archivos de la repuesta de openECM
            $mArchivos = $mResponseData[$i]['archivos'];
            for($j = 0; $j < count($mArchivos); $j++) {
              $mInsert['ANEXOS'][count($mInsert['ANEXOS'])] = array(
                'TIPO_DOCUMENTAL' => $cTipoDoc,
                'NOMBRE'          => $mArchivos[$j]['nombre'],
                'EXTENSION'       => $mArchivos[$j]['extension'],
                'CONTENT'         => $mArchivos[$j]['archivo']
              );
            }

            // Guardamos la información en la tabla temporal
            $objEstructura->fnGuardarAnexosEcm($cAlfa, $mInsert);
          }

        }
      } else {
        $mReturn[0] = "false";
      }
  
      return $mReturn;
    }
  }

  class cEstructurasGestorDocumentalopenECM {
    /**
     * Metodo que realiza la conexion
     */
    function fnConectarDBGestorDocumental(){
      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var number
       */
      $nSwitch = 0;

      /**
       * Matriz para Retornar Valores
       */
      $mReturn = array();

      /**
       * Reservo Primera Posicion para retorna true o false
       */
      $mReturn[0] = "";

      $xConexion99 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);
      if($xConexion99){
        $nSwitch = 0;
      }else{
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "El Sistema no Logro Conexion con ".OC_SERVER;
      }

      if($nSwitch == 0){
        $mReturn[0] = "true";
        $mReturn[1] = $xConexion99;
      }else{
        $mReturn[0] = "false";
      }
      return $mReturn;
    }##function fnConectarDBGestorDocumental(){##

    /**
     * Metodo que se encarga de Crear la Estructura de la Tabla Temporal de los Anexos consultados en ECM.
     */
    function fnCrearEstructurasVerAnexosEcm($cAlfa){
      /**
       * Recibe como Parametro un vector con las siguientes posiciones:
       * $cAlfa //Base de datos
       */

      /**
       * Variable para saber si hay o no errores de validacion.
       * @var number
       */
      $nSwitch = 0;

      /**
       * Matriz para Retornar Valores
       * @var array
       */
      $mReturn = array();

      /**
       * Reservando Primera Posición para retorna true o false
       */
      $mReturn[0]  = ""; // Resultado del proceso
      $mReturn[1]  = ""; // Nombre de tabla

      /**
       * Llamando Metodo que hace conexion
       */
      $mReturnConexionGestor = $this->fnConectarDBGestorDocumental();
      if($mReturnConexionGestor[0] == "true"){
        $xConexion99 = $mReturnConexionGestor[1];
      }else{
        $nSwitch = 1;
        for($nR=1;$nR<count($mReturnConexionGestor);$nR++){
          $mReturn[count($mReturn)] = $mReturnConexionGestor[$nR];
        }
      }

      if($nSwitch == 0) {
        /**
         * Nombre Random para la Tabla
         */
        $cTabla = "memgesan".mt_rand(1000000000, 9999999999);

        $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
        $qNewTab .= "anexidxx INT(11) NOT NULL AUTO_INCREMENT COMMENT \"Id Temporal\", "; // Autoincremental del anexo
        $qNewTab .= "anextdox VARCHAR(255)  NOT NULL COMMENT \"Tipo Documental\", ";  // Tipo Documental
        $qNewTab .= "anexname VARCHAR(255)  NOT NULL COMMENT \"Nombre Archivo\", ";  // Nombre del archivo
        $qNewTab .= "anexexte VARCHAR(255)  NOT NULL COMMENT \"Extension Archivo\", ";  // Extensión del archivo anexo
        $qNewTab .= "anexcont LONGTEXT      NOT NULL COMMENT \"Base64 Archivo\", ";  // Contenido del archivo en base64
        $qNewTab .= "PRIMARY KEY (anexidxx)) ENGINE=MyISAM ";
        $xNewTab  = mysql_query($qNewTab,$xConexion99);

        if(!$xNewTab) {
          $mReturn[0] = "false";
          $mReturn[count($mReturn)] = "Error al Crear Tabla Temporal de Errores. ".mysql_error($xConexion99);
        } else {
          $mReturn[0] = "true";
          $mReturn[1] = $cTabla;
        }

      } else {
        $mReturn[0] = "false";
      }

      return $mReturn;
    } ## function fnCrearEstructurasVerAnexosEcm($cAlfa){ ##

    /**
     * Metodo que se encarga de Guardar los anexos consultados en openECM en la tabla temporal.
     */
    function fnGuardarAnexosEcm($cAlfa, $pArrayParametros) {
      /**
       * Recibe como Parametro un vector con las siguientes posiciones:
       * $cAlfa //Base de datos
       * $pArrayParametros['TABLA']                        // Nombre tabla tempoeral
       * $pArrayParametros['ANEXOS']                       // Arreglo que contiene los anexos
       * $pArrayParametros['ANEXOS'][n]['TIPO_DOCUMENTAL'] // Tipo documental
       * $pArrayParametros['ANEXOS'][n]['NOMBRE']          // Nombre del archivo anexo
       * $pArrayParametros['ANEXOS'][n]['EXTENSION']       // Extensión del archivo anexo
       * $pArrayParametros['ANEXOS'][n]['CONTENT']         // Contenido base64 del archivo anexo
       */

      /**
       * Variable para saber si hay o no errores de validacion.
       * @var number
       */
      $nSwitch = 0;

      /**
       * Llamando Metodo que hace conexion
       */
      $mReturnConexionGestor = $this->fnConectarDBGestorDocumental();
      if($mReturnConexionGestor[0] == "true"){
        $xConexion99 = $mReturnConexionGestor[1];
      }else{
        $nSwitch = 1;
        for($nR=1;$nR<count($mReturnConexionGestor);$nR++){
          $mReturn[count($mReturn)] = $mReturnConexionGestor[$nR];
        }
      }

      if($nSwitch == 0) {
        $qInsert  = "INSERT INTO $cAlfa.{$pArrayParametros['TABLA']} (anextdox, anexname, anexexte, anexcont) VALUES ";
        $vInsert = array();
        for ($i = 0; $i < count($pArrayParametros['ANEXOS']); $i++) {
          $qInsAnexo = "( \"{$pArrayParametros['ANEXOS'][$i]['TIPO_DOCUMENTAL']}\", ";
          $qInsAnexo .= " \"{$pArrayParametros['ANEXOS'][$i]['NOMBRE']}\", ";
          $qInsAnexo .= " \"{$pArrayParametros['ANEXOS'][$i]['EXTENSION']}\", ";
          $qInsAnexo .= " \"{$pArrayParametros['ANEXOS'][$i]['CONTENT']}\" ) ";
          
          $vInsert[count($vInsert)] = $qInsAnexo;
        }
  
        if(count($vInsert) > 0) {
          $qInsert .= implode(",", $vInsert);
          $xInsert = mysql_query($qInsert,$xConexion99);
        }
      }
    } ## function fnGuardarAnexosEcm($cAlfa, $pArrayParametros) {
  }