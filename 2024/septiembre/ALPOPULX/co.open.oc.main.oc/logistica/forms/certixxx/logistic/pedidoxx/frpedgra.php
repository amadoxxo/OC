<?php
  /**
   * Graba Pedido.
   * --- Descripcion: Permite Guardar en la tabla de Pedido un nuevo registro.
   * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @package opencomex
   * @version 001
   */
  include('../../../../../financiero/libs/php/utility.php');
	include("../../../../libs/php/utipedix.php");
  include("../../../../../config/config.php");

  date_default_timezone_set('America/Bogota');

  ini_set('error_reporting', E_ERROR);
  ini_set("display_errors","1");

  /**
   * Variable para saber si hay o no errores de validacion.
   *
   * @var integer
   */
  $nSwitch = 0;

  /**
   * Variable para concatenar errores de validacion u otros.
   *
   * @var string
   */
  $cMsj = "\n";

  /**
   * Año actual del sistema.
   * 
   * @var string
   */
  $cPerAno = date('Y');

  /**
   * Mes actual del sistema.
   * 
   * @var string
   */
  $cPerMes = date('m');

  /**
   * Instanciando Objeto para la creacion de las tablas temporales.
   */
  $objTablasAnualizadas          = new cEstructurasTablasAnualizadasPedido();
  $mReturnCrearTablasAnualizadas = $objTablasAnualizadas->fnCrearTablasAnualizadas();
  if($mReturnCrearTablasAnualizadas[0] == "false"){
    $nSwitch = 1;
    for($nR=1;$nR<count($mReturnCrearTablasAnualizadas);$nR++){
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= $mReturnCrearTablasAnualizadas[$nR] . "\n";
    }
  }

  // Creación de tabla anualizada en caso de que no exista
  $qTabExis = "SHOW TABLES FROM $cAlfa LIKE \"lpca$cPerAno\"";
  $xTabExis = f_MySql("SELECT","",$qTabExis,$xConexion01,"");
  if(mysql_num_rows($xTabExis) == 0){
    $cAnoCrea = $vSysStr['logistica_ano_instalacion_modulo'];

    $qCreate  = "CREATE TABLE IF NOT EXISTS $cAlfa.lpca$cPerAno LIKE $cAlfa.lpca$cAnoCrea ";
    $xCreate = mysql_query($qCreate,$xConexion01);
    if(!$xCreate) {
      $nSwitch   = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Error al crear Tabla Anualizada [lpca$cPerAno].\n".mysql_error($xConexion01);
    }else {
      $qTabExis = "SHOW TABLES FROM $cAlfa LIKE \"lpde$cPerAno\"";
      $xTabExis  = f_MySql("SELECT","",$qTabExis,$xConexion01,"");
      if( mysql_num_rows($xTabExis) == 0 ){
        $qCreate  = "CREATE TABLE IF NOT EXISTS $cAlfa.lpde$cPerAno LIKE $cAlfa.lpde$cAnoCrea ";
        $xCreate = mysql_query($qCreate,$xConexion01);
        //f_Mensaje(__FILE__,__LINE__,$qTabExis);    
        if(!$xCreate) {
          $nSwitch   = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al crear Tabla Anualizada [lpde$cPerAno].\n";
        }
      }
    }
  }

  $cPerAno = ($_COOKIE['kModo'] == "NUEVO") ? $cPerAno : $_POST['cAnio'];
  switch ($_COOKIE['kModo']) {
		case "NUEVO":
      /**
       * Datos enviados
       * @var array
       */
      $mDatos            = array();
      $mDatos            = $_POST;
      $mDatos['cModo']   = $_COOKIE['kModo']; // Modo de grabado (NUEVO,EDITAR)
      $mDatos['cPerAno'] = $cPerAno; // Anio del Pedido
      $mDatos['cPerMes'] = $cPerMes; // Mes actual

      # Creación Cabecera Pedido
      # Creando la instancia para la creacion de Cabecera Pedido
      $ObjcPedido = new cPedido();
      $mRetorna = $ObjcPedido->fnGuardarPedido($mDatos); //Se envian todos los datos que llegan por POST

      if ($mRetorna[0] == "false") {
        $nSwitch = 1;
        for ($i=6; $i<count($mRetorna); $i++) {
          $mAuxText = explode("~",$mRetorna[$i]);
          $cMsj .= ($mAuxText[0] != "") ? "Linea ".str_pad($mAuxText[0],4,"0",STR_PAD_LEFT).": " : "";
          $cMsj .= $mAuxText[1]."\n";
        }
      }
    break;
    case "EDITAR":
      /**
       * Datos enviados
       * @var array
       */
      $mDatos             = array();
      $mDatos             = $_POST;
      $mDatos['cModo']    = $_COOKIE['kModo']; // Modo de grabado (NUEVO,EDITAR)
      $mDatos['cPerAno']  = $cPerAno; // Anio del Pedido
      $mDatos['cPerMes']  = $cPerMes; // Mes actual

      # Actualizando el Detalle Pedido
      # Creando la instancia para la Actualización de Detalle Pedido
      $ObjcPedido = new cPedido();
      $mRetorna = $ObjcPedido->fnValidacionesDetallePedido($mDatos); //Se envian todos los datos que llegan por POST

      if ($mRetorna[0] == "true") {
        $mDatos['pedidxxx'] = $_POST['cPedId']; // Id del Pedido
        $mDatos['cComTPed'] = $_POST['cComTPed_Hd']; // Asigna el valor Original del Tipo de Pedido 

        // Hace el llamado del método para actualizar el Pedido
        $mRespuesta = $ObjcPedido->fnActualizarDetallePedido($mDatos); //Se envian todos los datos que llegan por POST
        if ($mRespuesta[0] == "false") {
          $nSwitch = 1;
          for ($i=1; $i<count($mRespuesta); $i++) {
            $mAuxText = explode("~",$mRespuesta[$i]);
            $cMsj .= ($mAuxText[0] != "") ? "Linea ".str_pad($mAuxText[0],4,"0",STR_PAD_LEFT).": " : "";
            $cMsj .= $mAuxText[1]."\n";
          }
        }
      } else {
        $nSwitch = 1;
        for ($i=1; $i<count($mRetorna); $i++) {
          $mAuxText = explode("~",$mRetorna[$i]);
          $cMsj .= ($mAuxText[0] != "") ? "Linea ".str_pad($mAuxText[0],4,"0",STR_PAD_LEFT).": " : "";
          $cMsj .= $mAuxText[1]."\n";
        }
      }
    break;
    case "ANULAR":
    case "DEVOLUCION":

      /**
       * Datos enviados
       * @var array
       */
      $mDatos            = array();
      $mDatos            = $_POST;
      $mDatos['cModo']   = $_COOKIE['kModo']; // Modo de grabado (ANULAR|DEVOLUCION)
      $mDatos['cPerAno'] = $cPerAno; // Anio del Pedido
      $mDatos['cPerMes'] = $cPerMes; // Mes actual

      # Actualizar estado del Pedido
      # Creando la instancia para actualizar el estado del Pedido
      $ObjcPedido = new cPedido();
      $mRetorna = $ObjcPedido->fnGuardarPedido($mDatos); //Se envian todos los datos que llegan por POST

      if ($mRetorna[0] == "false") {
        $nSwitch = 1;
        for ($i=6; $i<count($mRetorna); $i++) {
          $mAuxText = explode("~",$mRetorna[$i]);
          $cMsj .= ($mAuxText[0] != "") ? "Linea ".str_pad($mAuxText[0],4,"0",STR_PAD_LEFT).": " : "";
          $cMsj .= $mAuxText[1]."\n";
        }
      }

    break;
    default:
      f_Mensaje(__FILE__,__LINE__,"Modo de Gravado No Valido");
    break;
  }

  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "NUEVO":
      case "EDITAR":
        f_Mensaje(__FILE__,__LINE__,"Se Guardo el Pedido con Exito."); ?>

        <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
        <script languaje = "javascript">document.forms['frgrm'].submit();</script>
        <?php
      break;
      case "ANULAR":
        f_Mensaje(__FILE__,__LINE__,"Se Anulo el Pedido con exito."); ?>

        <script type="text/javascript">
          parent.window.fmwork.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        </script>
        <?php 
      break;
      case "DEVOLUCION":
        f_Mensaje(__FILE__,__LINE__,"Se realizo la Devolucion del Pedido con exito."); ?>

        <script type="text/javascript">
          parent.window.fmwork.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        </script>
        <?php 
      break;
      default:
        // No hace nada
      break;
    }
  } else {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
  }

  /**
   * Clase que permite crear la tabla aualizada de cabecera y detalle en caso de estas no existan.
   */
  class cEstructurasTablasAnualizadasPedido{

    /**
     * Permite crear las tablas anualizadas del Pedido.
     */
    function fnCrearTablasAnualizadas() {
      global $cAlfa;

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

      /**
       * Año actual del sistema.
       * 
       * @var string
       */
      $nAnio = date('Y');
      $nAnioAnterior = date('Y')-1;

      /**
       * Llamando Metodo que hace conexion
       */
      $mReturnConexionTM = $this->fnConectarDBPedido();
      if ($mReturnConexionTM[0] == "true") {
        $xConexionTM = $mReturnConexionTM[1];
      } else {
        $nSwitch = 1;
        for ($nR=1;$nR<count($mReturnConexionTM);$nR++) {
          $mReturn[count($mReturn)] = $mReturnConexionTM[$nR];
        }
      }

      if($nSwitch == 0){
        // Creación de tabla anualizada en caso de que no exista
        $qTabExis = "SHOW TABLES FROM $cAlfa LIKE \"lpca$nAnio\"";
        $xTabExis = f_MySql("SELECT","",$qTabExis,$xConexionTM,"");
        if (mysql_num_rows($xTabExis) == 0) {
          $qCreate = "CREATE TABLE IF NOT EXISTS $cAlfa.lpca$nAnio LIKE $cAlfa.lpca$nAnioAnterior ";
          $xCreate = mysql_query($qCreate,$xConexionTM);
          if (!$xCreate) {
            $nSwitch = 1;
            $vReturn[count($vReturn)] = __LINE__."~Error al crear Tabla Anualizada [lpca$nAnio].~".mysql_error($xConexion01);
          } else {

            /**
             * NOTA: Siempre que se cree una llave foránea en la tabla anualizada de cabecera [lpcaxxxx] se debe agregar la sentencia sql ALTER TABLE
             */
            $qAlter = "ALTER TABLE $cAlfa.lpca$nAnio ADD CONSTRAINT lpca{$nAnio}_ibfk_1 FOREIGN KEY (cliidxxx) REFERENCES $cAlfa.lpar0150(cliidxxx); ";
            $xAlter = mysql_query($qAlter,$xConexionTM);
            if (!$xAlter) {
              $nSwitch = 1;
              $vReturn[count($vReturn)] = __LINE__."~Error al crear FK Anualizada [lpca$nAnio].~".mysql_error($xConexion01);
            }

            // Valida si NO existe la tabla de detalle para crearla
            $qTabExis = "SHOW TABLES FROM $cAlfa LIKE \"lpde$nAnio\"";
            $xTabExis = f_MySql("SELECT","",$qTabExis,$xConexionTM,"");
            if( mysql_num_rows($xTabExis) == 0 ){
              $qCreate = "CREATE TABLE IF NOT EXISTS $cAlfa.lpde$nAnio LIKE $cAlfa.lpde$nAnioAnterior ";
              $xCreate = mysql_query($qCreate,$xConexionTM);
              if(!$xCreate) {
                $nSwitch = 1;
                $mReturn[count($mReturn)] = "Error al crear Tabla Anualizada [lpde$nAnio].";
              } else {
                /**
                 * NOTA: Siempre que se cree una llave foránea en la tabla anualizada de detalle [lpdexxxx] se debe agregar la sentencia sql ALTER TABLE
                 */
                $qAlter = "ALTER TABLE $cAlfa.lpde$nAnio ADD CONSTRAINT lpde{$nAnio}_ibfk_1 FOREIGN KEY (pedidxxx) REFERENCES $cAlfa.lpca$nAnio(pedidxxx);";
                $xAlter = mysql_query($qAlter,$xConexionTM);
                if (!$xAlter) {
                  $nSwitch = 1;
                  $vReturn[count($vReturn)] = __LINE__."~Error al crear FK Anualizada [lpde$nAnio].~".mysql_error($xConexion01);
                }
              }
            } 
          }  
        }
      }

      if($nSwitch == 0){
        $mReturn[0] = "true";
      }else{
        $mReturn[0] = "false";
      }
      return $mReturn;
    }

    /**
     * Metodo que realiza la conexion
     */
    function fnConectarDBPedido(){

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
    }##function fnConectarDBPedido(){##
  }