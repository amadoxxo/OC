<?php
  /**
   * Realiza la descarga o generación del base64 de uno o más anexos guardados en la tabla temporal.
   * @author Juan Hernandez <juan.hernandez@openits.co>
   * @package opencomex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");
  
  // Consultamos el registro en la tabla temporal
  $base64  = "";
  $qAnexo  = "SELECT ";
  $qAnexo .= "anexcont, ";
  $qAnexo .= "anexname, ";
  $qAnexo .= "anexexte  ";
  $qAnexo .= "FROM $cAlfa.{$_POST['cTabla']} ";
  
  if(isset($_POST['nId'])) {
    $qAnexo .= "WHERE ";
    $qAnexo .= "anexidxx = \"{$_POST['nId']}\" LIMIT 0,1";
  }
  $xAnexo = f_MySql("SELECT","",$qAnexo,$xConexion01,"");
  $nTotAne = mysql_num_rows($xAnexo);
  
  if ($nTotAne == 1 && isset($_POST['nId'])) {
    $xRAN = mysql_fetch_array($xAnexo);
    $base64 = $xRAN['anexcont'];

    // Verifica que la cadena base64 no esté vacía
    if (empty($base64)) {
      die("Error: El contenido base64 está vacío.");
    }

    // Decodifica el archivo base64 en binario
    $cContent = base64_decode($base64);

    // Verifica que la decodificación haya sido exitosa
    if ($cContent === false) {
      die("Error: No se pudo decodificar el contenido base64.");
    }

    if(isset($_POST['cAccion']) && $_POST['cAccion'] == 'descargar') {
      // Verifica el tipo MIME del archivo para establecer la cabecera Content-Type
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $cType = finfo_buffer($finfo, $cContent);
      finfo_close($finfo);

      // Retornar los datos en JSON
      echo json_encode([
        'type' => $cType,
        'file' => base64_encode($cContent)
      ]);
    } else if(isset($_POST['cAccion']) && $_POST['cAccion'] == 'ver') {
      // Codificamos a base64 para mostrarlo, aplica sólo para pdf
      echo base64_encode($cContent);
    }
  } else if($nTotAne > 0) {
    $zipFilename = 'archivos.zip';
    // Crear un nuevo archivo .zip en memoria
    $zip = new ZipArchive;
    $tmpFile = tempnam(sys_get_temp_dir(), $zipFilename);

    if ($zip->open($tmpFile, ZipArchive::CREATE) === TRUE) {
      
      // Recorrerlo con el while
      while($xRAN = mysql_fetch_array($xAnexo)) {
        // Decodificar cada cadena base64
        $file = base64_decode($xRAN['anexcont']);
        $name = $xRAN['anexname'];
        $ext  = $xRAN['anexexte'];

        // Agregar el archivo al .zip con un nombre único
        $zip->addFromString($name .".". $ext, $file);
      }

      // Cerrar el archivo .zip
      $zip->close();

      // Establecer las cabeceras para la descarga del archivo .zip
      header('Content-Type: application/zip');
      header('Content-Disposition: attachment; filename="' . $zipFilename . '"');
      header('Content-Length: ' . filesize($tmpFile));

      // Enviar el archivo .zip al navegador
      readfile($tmpFile);

      // Eliminar el archivo temporal
      unlink($tmpFile);
    }
  }
?>