<?php
  namespace openComex;
  /**
	 * Ver Anexos para el módulo de Logistica.
	 * @author Juan Hernandez <juan.hernandez@openits.co>
	 * @package openComex
	 */
  include("../../../../../config/config.php");
  include('../../../../../libs/php/utility.php');
  include('../../../../../libs/php/uticecmx.php');
  include("../../../../libs/php/utigesdo.php");

  // Cookie fija
  $kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlHost = $kDf[0];
  $kMysqlUser = $kDf[1];
  $kMysqlPass = $kDf[2];
  $kMysqlDb   = $kDf[3];
  $kUser      = $kDf[4];
  $kLicencia  = $kDf[5];
  $swidth     = $kDf[6];

  $cTipoTra = $_GET['gTipOri'];
  $cAnio = substr($_GET['dFecCrea'],0, 4);
  $cLegend = "";
  $vParams = array();
  $vParams['particion_inicial'] = $cAnio;
  $vParams['particion_final'] = date('Y');
  $vParams['versiones'] = "LAST";

  $qTipDoc  = "SELECT gruidecm ";
  $qTipDoc .= "FROM $cAlfa.lpar0162 ";
  $qTipDoc .= "WHERE ";
  $qTipDoc .= "tdogruxx = \"$cTipoTra\"  LIMIT 0,1";
  $xTipDoc = f_MySql("SELECT","",$qTipDoc,$xConexion01,"");
  if (mysql_num_rows($xTipDoc) > 0) {
    $xTDO = mysql_fetch_array($xTipDoc);
    $vParams['grupo_documental'] = $xTDO['gruidecm'];
  }

  switch ($cTipoTra) {
    case 'MIF':
      $qRegLog  = "SELECT comidxxx,comprexx,comcscxx ";
      $qRegLog .= "FROM $cAlfa.lmca$cAnio ";
      $qRegLog .= "WHERE ";
      $qRegLog .= "mifidxxx = \"{$_GET['nCagId']}\" LIMIT 0,1";

      $xRegLog = f_MySql("SELECT","",$qRegLog,$xConexion01,"");
      if (mysql_num_rows($xRegLog) > 0) {
        $xRLG = mysql_fetch_array($xRegLog);
        $vParams['numero_operacion'] = $xRLG['comprexx']."-".$xRLG['comcscxx'];
        $cLegend = "MIF: ".$xRLG['comidxxx']."-".$xRLG['comprexx'].$xRLG['comcscxx'];
      }
      break;

    case 'CERTIFICACION':
      $qRegLog  = "SELECT comidxxx,comprexx,comcscxx ";
      $qRegLog .= "FROM $cAlfa.lcca$cAnio ";
      $qRegLog .= "WHERE ";
      $qRegLog .= "ceridxxx = \"{$_GET['nCagId']}\" LIMIT 0,1";

      $xRegLog = f_MySql("SELECT","",$qRegLog,$xConexion01,"");
      if (mysql_num_rows($xRegLog) > 0) {
        $xRLG = mysql_fetch_array($xRegLog);
        $vParams['numero_operacion'] = $xRLG['comprexx']."-".$xRLG['comcscxx'];
        $cLegend = "Certificaci&oacute;n: ".$xRLG['comidxxx']."-".$xRLG['comprexx'].$xRLG['comcscxx'];
      }
      break;

    case 'PEDIDO':
      $qRegLog  = "SELECT comidxxx,comprexx,comcscxx ";
      $qRegLog .= "FROM $cAlfa.lpca$cAnio ";
      $qRegLog .= "WHERE ";
      $qRegLog .= "pedidxxx = \"{$_GET['nCagId']}\" LIMIT 0,1";

      $xRegLog = f_MySql("SELECT","",$qRegLog,$xConexion01,"");
      if (mysql_num_rows($xRegLog) > 0) {
        $xRLG = mysql_fetch_array($xRegLog);
        $vParams['numero_operacion'] = $xRLG['comprexx']."-".$xRLG['comcscxx'];
        $cLegend = "Pedido: ".$xRLG['comidxxx']."-".$xRLG['comprexx'].$xRLG['comcscxx'];
      }
      break;

    default:
      # code...
      break;
  }

  $cIntegracionGestor = new cIntegracionGestorDocumentalopenECM();
  $mReturnVerAnexos = $cIntegracionGestor->fnVerDocumentosAnexos($vParams);
  
  $cTablaTemp = $mReturnVerAnexos[1];
  //Consultamos los anexos en la tabla temporal
  $qAnexos  = "SELECT ";
  $qAnexos .= "anexidxx, ";
  $qAnexos .= "anextdox, ";
  $qAnexos .= "anexname, ";
  $qAnexos .= "anexexte  ";
  $qAnexos .= "FROM $cAlfa.$cTablaTemp ";
  $xAnexos = mysql_query($qAnexos,$xConexion01);
  $nTotAne = mysql_num_rows($xAnexos);

  if($nTotAne == 0) { ?>
    <script language="javascript">
      alert('No existen anexos cargados.');
      parent.window.close();
    </script> <?php
  }

?>
<html>
	<head>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/estilo.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/custom.css'>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/utility.js'></script>

		<script language="javascript">
      /**
       *  Realiza la descarga del anexo en pantalla.
       */
      function fnDescargar(nId, cExtension, cNombre) {
        if(cExtension == 'pdf') {
          visualizarAnexo(nId, cExtension, cNombre);
        } else {
          // Configura los parámetros para la solicitud POST
          const postData = new URLSearchParams();
          postData.append('cTabla', '<?php echo $cTablaTemp ?>');
          postData.append('nId', nId);
          postData.append('cAccion', 'descargar');

          fetch('frmifvag.php', {
            method: 'POST',
            body: postData,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
          })
          .then(response => response.json())
          .then(data => {
            // Crear un enlace de descarga
            const link = document.createElement('a');
            link.href = 'data:' + data.type + ';base64,' + data.file;
            link.download = cNombre + '.' + cExtension;

            // Simular un clic en el enlace para iniciar la descarga
            link.click();
            alert('Se descargo el archivo correctamente.');
          });
        }
      }

      /**
       *  Abre una ventana para visualizar el documento pdf.
       */
      function visualizarAnexo(nId, cExtension, cNombre) {
        // Configura los parámetros para la solicitud POST
        const postData = new URLSearchParams();
        postData.append('cTabla', '<?php echo $cTablaTemp ?>');
        postData.append('nId', nId);
        postData.append('cAccion', 'ver');

        fetch('frmifvag.php', {
          method: 'POST',
          body: postData,
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        })
        .then(response => response.text())
        .then(base64 => {
          if (!base64) {
            throw new Error('El contenido base64 está vacío.');
          }

          // El contenido del PDF en binario (decodificado del base64 que viene del PHP)
          const pdfData = atob(base64);

          // Crear un array de enteros sin signo
          const byteArray = new Uint8Array(pdfData.length);
          for (let i = 0; i < pdfData.length; i++) {
            byteArray[i] = pdfData.charCodeAt(i);
          }

          // Crear un Blob a partir de los datos binarios
          const blob = new Blob([byteArray], { type: 'application/pdf' });

          // Crear una URL para el Blob
          const url = URL.createObjectURL(blob);

          // Crear una ventana emergente con dimensiones específicas
          const width = 800;
          const height = 600;
          const left = (window.innerWidth - width) / 2 + window.screenX;
          const top = (window.innerHeight - height) / 2 + window.screenY;

          const popup = window.open('', 'pdfWindow', `width=${width},height=${height},top=${top},left=${left}`);

          // Escribir el contenido del PDF en la ventana emergente
          popup.document.write('<html><head><title>'+ cNombre + '.' + cExtension +'</title></head><body style="margin:0;"><embed width="100%" height="100%" src="' + url + '" type="application/pdf"></embed></body></html>');
          popup.document.close();
        });
      }

      /**
       *  Realiza la descarga de todos los anexos listados en la tabla en un archivo zip.
       */
      function fnDescargarTodos() {
        // Configura los parámetros para la solicitud POST
        const postData = new URLSearchParams();
        postData.append('cTabla', '<?php echo $cTablaTemp ?>');

        // Hacer la solicitud a generar_zip.php para descargar el .zip
        fetch('frmifvag.php', {
          method: 'POST',
          body: postData,
          headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        })
        .then(response => {
          if (response.ok) {
            // Obtener el archivo .zip como blob
            return response.blob();
          } else {
            throw new Error('Error al generar el archivo ZIP');
          }
        })
        .then(blob => {
          // Crear un enlace de descarga para el archivo .zip
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = 'archivos.zip';
          document.body.appendChild(a);
          a.click();
          a.remove();
          // Limpiar la URL del objeto
          window.URL.revokeObjectURL(url);
          alert('Se descargaron los anexos con exito.');
        });
      }
    </script>
	</head>
	<body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
    <center>
      <table border ="0" cellpadding="0" cellspacing="0" width="520">
        <tr>
          <td>
            <fieldset>
              <legend><?php echo $cLegend ?></legend>
              <span style="height: 2;">
                <?php 
                  if($nTotAne > 0) {
                    echo "<b>Total de documentos anexos: ".$nTotAne."</b>";
                  } else {
                    echo "No se encontraron documentos anexos asociados.";
                  }
                ?>
              </span><br><br>
              <center>
                <table border="0" cellspacing="1" cellpadding="0" width="520">
                <?php if($nTotAne > 0) { ?>
                  <!-- Encabezados -->
                  <tr bgcolor = '#D6DFF7' style="height:20px">
                    <td align="center" height="30" width ="220"><strong>Tipo Documental</strong></td>
                    <td align="center" height="30" width ="230"><strong>Archivo</strong></td>
                    <td align="center" height="30" width ="70"><strong>Descargar</strong></td>
                  </tr>
                  <!-- Rows -->
                  <?php 
                  $y = 0;
                  while ($xRAN = mysql_fetch_array($xAnexos)) {
                    $cColor = "{$vSysStr['system_row_impar_color_ini']}";
                    if($y % 2 == 0) {
                      $cColor = "{$vSysStr['system_row_par_color_ini']}";
                    } ?>
                    <tr bgcolor = "<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')"
                      onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
                      <td style="padding:2px" height="30" width ="220"><?php echo $xRAN['anextdox'] ?></td>
                      <td style="padding:2px;word-break: break-all; word-wrap: break-word;" height="30" width ="230">
                        <span><?php echo $xRAN['anexname'].".".$xRAN['anexexte'] ?></span>
                      </td>
                      <td style="padding-top:3px;padding-bottom:3px" height="30" width ="70">
                        <center>
                          <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/ver_anexos.png" onClick = "javascript:fnDescargar('<?php echo $xRAN['anexidxx'] ?>','<?php echo $xRAN['anexexte'] ?>','<?php echo $xRAN['anexname'] ?>')" style = "cursor:pointer" title="Descargar">
                        </center>
                      </td>
                    </tr>
                    <?php $y++;
                  }
                } ?>
                </table>
              </center>
            </fieldset>
          </td>
        </tr>
      </table>
		</center>
		<center>
      <table border="0" cellpadding="0" cellspacing="0" width="540">
        <tr height="21">
          <td width="329" height="21"></td>
          <?php if($nTotAne > 1) { ?>
          <td width="120" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_ok_bg120.gif" style="cursor:hand"
            onClick = "javascript:fnDescargarTodos()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Descargar Todos
          </td>
          <?php } else { ?>
          <td width="120" height="21"></td>
          <?php } ?>
          <td width="91" height="21" Class="name" name="Btn_Salir" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_cancel_bg.gif" style="cursor:hand"
            onClick = "javascript:parent.window.close();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cerrar
          </td>
        </tr>
      </table>
		</center>
	</body>
</html>