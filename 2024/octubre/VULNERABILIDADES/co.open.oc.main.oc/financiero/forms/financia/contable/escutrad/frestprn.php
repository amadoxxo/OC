<?php
  namespace openComex;
  use FPDF;

include("../../../../libs/php/utility.php");

ini_set("memory_limit", "512M");
set_time_limit(0);

date_default_timezone_set("America/Bogota");


if ($gCcoId <> "") {
    $mAux = explode("~", $gCcoId);
    $gCcoId = $mAux[0];
    $gSucCco = $mAux[1];
    $gCcoNom = $mAux[1];
}

if ($gTerId <> "") {
    #Busco el nombre del cliente
    $qCliNom = "SELECT ";
    $qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,' ',CLINOM2X,' ',CLIAPE1X,' ',CLIAPE2X)) <> \"\",TRIM(CONCAT(CLINOM1X,' ',CLINOM2X,' ',CLIAPE1X,' ',CLIAPE2X)), CLINOMXX) AS clinomxx ";
    $qCliNom .= "FROM $cAlfa.SIAI0150 ";
    $qCliNom .= "WHERE ";
    $qCliNom .= "CLIIDXXX = \"{$gTerId}\" LIMIT 0,1";
    $xCliNom = f_MySql("SELECT", "", $qCliNom, $xConexion01, "");
    if (mysql_num_rows($xCliNom) > 0) {
        $xDDE = mysql_fetch_array($xCliNom);
    } else {
        $xDDE['clinomxx'] = "CLIENTE SIN NOMBRE";
    }
}

if ($gDirId <> "") {
    #Busco el nombre del director de cuenta
    $qNomDir = "SELECT ";
    $qNomDir .= "IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"DIRECTOR SIN NOMBRE\") AS USRNOMXX ";
    $qNomDir .= "FROM $cAlfa.SIAI0003 ";
    $qNomDir .= "WHERE ";
    $qNomDir .= "USRIDXXX = \"{$gDirId}\" LIMIT 0,1";
    $xNomDir = f_MySql("SELECT", "", $qNomDir, $xConexion01, "");
    if (mysql_num_rows($xNomDir) > 0) {
        $xRU = mysql_fetch_array($xNomDir);
    } else {
        $xRU['USRNOMXX'] = "VENDEDOR SIN NOMBRE";
    }
}

switch ($gEstado) {
    case "ACTIVO":
        $cTitulo .= "REPORTE DE TRAMITES ABIERTOS SIN FACTURAR ";
        break;
    case "FACTURADO":
        $cTitulo .= "REPORTE DE TRAMITES FACTURADOS ";
        break;
}

switch ($cTipo) {
    case 1:
        // PINTA POR PANTALLA// 
        ?>
        <html>
            <head>
                <title>Reporte de Estado de Cuenta Tramites</title>
                <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
                <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
                <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
                <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
                <script type="text/javascript">
                    function f_Imprimir(xComId, cComCod, xSucId, xDocTip, xDocId, xDocSuf, xPucId, xCcoId, xCliId, xRegFCre) { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
                        if (xSucId.length > 0 ||
                                xDocNro.length > 0 ||
                                xDocSuf.length > 0 ||
                                xRegFCre.length > 0) {

                            var cRuta = "../movimido/frmdoprn.php?" +
                                    "gComId=" + xComId +
                                    "&gComCod=" + cComCod +
                                    "&gSucId=" + xSucId +
                                    "&gDocTip=" + xDocTip +
                                    "&gDocId=" + xDocId +
                                    "&gDocSuf=" + xDocSuf +
                                    "&gPucId=" + xPucId +
                                    "&gCcoId=" + xCcoId +
                                    "&gCliId=" + xCliId +
                                    "&gRegFCre=" + xRegFCre +
                                    "&gMov=CONCEPTO" +
                                    "&gPyG=1";

                            var nX = screen.width;
                            var nY = screen.height;
                            var nNx = 0;
                            var nNy = 0;
                            var cWinOpt = "width=" + nX + ",scrollbars=1,resizable=YES,height=" + nY + ",left=" + nNx + ",top=" + nNy;
                            var cNomVen = 'zWinTrp' + Math.ceil(Math.random() * 1000);
                            cWindow = window.open(cRuta, cNomVen, cWinOpt);
                            cWindow.focus();

                        } else {
                            alert("El Numero del DO esta Vacio, Verifique");
                        }

                    }
                </script>
            </head>
            <body>
                <form name = 'frgrm' action='frinpgrf.php' method="POST">
                    <table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="2000">
                        <tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">
                            <td class="name" colspan="18" align="left">
                        <center>
                            <font size="3"><b>
                                <?php echo $cTitulo ?><br>
                                <?php echo "DESDE " . $gDesde . " HASTA " . $gHasta ?><br>
                                <?php if ($gCcoId <> "") { ?>
                                    SURCURSAL: <?php echo "[" . $gCcoId . "] " . $gCcoNom ?><br>
                                <?php
                                }
                                if ($gTerId <> "") {
                                    ?>
                                    CLIENTE: <?php echo "[" . $gTerId . "] " . $xDDE['clinomxx'] ?><br>
                                <?php
                                }
                                if ($gDirId <> "") {
                                    ?>
                                    DIRECTOR: <?php echo "[" . $gDirId . "] " . $xRU['USRNOMXX'] ?><br>
        <?php } ?>
                            </b></font>
                        </center>
                        </td>
                        </tr>
                        <tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">
                            <td class="name" colspan="18" align="left">
                        <center>
                            <font size="3">
                            <b>TOTAL TRAMITES EN ESTA CONSULTA <input type="text" name="nCanReg" style="width:80px" readonly><br>
                                </font>
                                </center>
                                </td>
                                </tr>
                                <tr height="20">
                                    <td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Tramite</font></b></td>
                                    <td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Sucursal</font></b></td>
                                    <!--<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Pedido</font></b></td>-->
                                    <td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Fecha</font></b></td>
                                    <!--<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Operaci&oacute;n</font></b></td>-->
                                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Mayor Levante</font></b></td>
                                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nit</font></b></td>
                                    <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Cliente</font></b></td>
                                    <td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Estado</font></b></td>
                                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Entrega Carpeta a Facturaci&oacute;n</font></b></td>
                                    <td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Cierre</font></b></td>

                                    <td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>D&iacute;as Transcurridos</font></b></td>
                                    <td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Entrega Factura al Cliente entre: Entrega a Facturaci&oacute;n y Generaci&oacute;n Factura</font></b></td>
                                    <td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>D&iacute;as Transcurridos entre: <br/> Generaci&oacute;n Factura y Entrega Factura al Cliente</font></b></td>

                                    <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Director</font></b></td>
                                    <td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Anticipo</font></b></td>
                                    <td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Pagos</font></b></td>
                                    <td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Saldo</font></b></td>
                                </tr>
                                <?php
                                break;
                            case 2:
                                // PINTA POR EXCEL //Reporte de Estado de Cuenta Tramites
                                $header .= 'REPORTE DE ESTADO DE CUENTA TRAMITES' . "\n";
                                $header .= "\n";
                                $data = '';
                                $title = "REPORTE DE ESTADO DE CUENTA TRAMITES.xls";

                                $data .= '<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="2000">';
                                $data .= '<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">';
                                $data .= '<td class="name" colspan="18" align="left">';
                                $data .= '<center>';
                                $data .= '<font size="3">';
                                $data .= '<b>' . $cTitulo . '<br>';
                                $data .= 'DESDE ' . $gDesde . ' HASTA ' . $gHasta . '<br>';
                                if ($gCcoId <> "") {
                                    $data .= 'SURCURSAL: ' . "[" . $gCcoId . "] " . $gCcoNom . '<br>';
                                }
                                if ($gTerId <> "") {
                                    $data .= 'CLIENTE: ' . "[" . $gTerId . "] " . $xDDE['clinomxx'] . '<br>';
                                }
                                if ($gDirId <> "") {
                                    $data .= 'DIRECTOR: ' . "[" . $gDirId . "] " . $xRU['USRNOMXX'] . '<br>';
                                }
                                $data .= '</b>';
                                $data .= '</font>';
                                $data .= '</center>';
                                $data .= '</td>';
                                $data .= '</tr>';
                                $data .= '<tr height="20">';
                                $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Tramite</font></b></td>';
                                $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Sucursal</font></b></td>';
                               // $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Pedido</font></b></td>';
                                $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Fecha</font></b></td>';
                                //$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Operaci&oacute;n</font></b></td>';
                                $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Mayor Levante</font></b></td>';
                                $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nit</font></b></td>';
                                $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Cliente</font></b></td>';
                                $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Estado</font></b></td>';
																$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Entrega Carpeta a Facturaci&oacute;n</font></b></td>';
                                $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Cierre</font></b></td>';

                                $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>D&iacute;as Transcurridos</font></b></td>';
                                $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Entrega Factura al Cliente entre: Entrega a Facturaci&oacute;n y Generaci&oacute;n Factura</font></b></td>';
                                $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>D&iacute;as Transcurridos entre: <br/> Generaci&oacute;n Factura y Entrega Factura al Cliente</font></b></td>';


                                $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Director</font></b></td>';
                                $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Anticipo</font></b></td>';
                                $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Pagos</font></b></td>';
                                $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Saldo</font></b></td>';
                                $data .= '</tr>';
                                break;
                            case 3 :
                                // PINTA POR PDF
                                $cRoot = $_SERVER['DOCUMENT_ROOT'];
                                $gTerNom = $xDDE['clinomxx'];
                                $gDirNom = $xRU['USRNOMXX'];

                                ##Switch para incluir fuente y clase pdf segun base de datos ##
                                switch ($cAlfa) {
                                    case "COLMASXX":
                                        define('FPDF_FONTPATH', "../../../../../fonts/");
                                        require("../../../../../forms/fpdf.php");
                                        break;
                                    default:
                                        define('FPDF_FONTPATH', $_SERVER['DOCUMENT_ROOT'] . $cSystem_Fonts_Directory . '/');
                                        require($_SERVER['DOCUMENT_ROOT'] . $cSystem_Class_Directory . '/fpdf/fpdf.php');
                                        break;
                                }
                                ##Fin Switch para incluir fuente y clase pdf segun base de datos ##

                                class PDF extends FPDF {

                                    function Header() {
                                        global $cRoot;
                                        global $cPlesk_Skin_Directory;
                                        global $cAlfa;
                                        global $cTitulo;
                                        global $gDesde;
                                        global $gHasta;
                                        global $gCcoId;
                                        global $gCcoNom;
                                        global $gTerId;
                                        global $gTerNom;
                                        global $gDirId;
                                        global $gDirNom;
                                        global $gCount;
                                        global $nPag;

                                        if ($cAlfa == "INTERLOG" || $cAlfa == "TEINTERLOG" || $cAlfa == "DEINTERLOG") {

                                            $this->SetXY(13, 7);
                                            $this->Cell(42, 28, '', 1, 0, 'C');
                                            $this->Cell(213, 28, '', 1, 0, 'C');

                                            // Dibujo //
                                            $this->Image($cRoot . $cPlesk_Skin_Directory . '/MaryAire.jpg', 14, 8, 40, 25);

                                            $this->SetFont('verdana', '', 12);
                                            $this->SetXY(55, 7);
                                            $this->Cell(213, 8, $cTitulo, 0, 0, 'C');
                                            $this->Ln(6);
                                            $this->SetX(55);
                                            $this->SetFont('verdana', 'B', 8);
                                            $this->Cell(213, 8, "DESDE $gDesde HASTA $gHasta", 0, 0, 'C');
                                            $this->Ln(5);
                                            $n = 20;
                                            if ($gCcoId <> "") {
                                                $this->SetFont('verdana', '', 8);
                                                $this->SetX(55);
                                                $this->Cell(213, 6, 'SURCURSAL: [' . $gCcoId . '] ' . $gCcoNom, 0, 0, 'C');
                                                $this->Ln(5);
                                                $n -= 5;
                                            }
                                            if ($gTerId <> "") {
                                                $this->SetFont('verdana', '', 8);
                                                $this->SetX(55);
                                                $this->Cell(213, 6, 'CLIENTE: [' . $gTerId . '] ' . $gTerNom, 0, 0, 'C');
                                                $this->Ln(5);
                                                $n -= 5;
                                            }
                                            if ($gDirId <> "") {
                                                $this->SetFont('verdana', '', 8);
                                                $this->SetX(55);
                                                $this->Cell(213, 6, 'DIRECTOR: [' . $gDirId . '] ' . $gDirNom, 0, 0, 'C');
                                                $this->Ln(5);
                                                $n -= 5;
                                            }

                                            $this->Ln($n);
                                            $this->SetX(13);
                                        } else {
                                            $this->SetXY(13, 6);
                                            $this->Cell(260, 28, '', 1, 0, 'C');

                                            $this->SetFont('verdana', '', 12);
                                            $this->SetXY(13, 7);
                                            $this->Cell(260, 8, $cTitulo, 0, 0, 'C');
                                            $this->Ln(6);
                                            $this->SetX(55);
                                            $this->SetFont('verdana', 'B', 8);
                                            $this->Cell(260, 8, "DESDE $gDesde HASTA $gHasta", 0, 0, 'C');
                                            $this->Ln(5);
                                            $n = 20;
                                            if ($gCcoId <> "") {
                                                $this->SetFont('verdana', '', 8);
                                                $this->SetX(13);
                                                $this->Cell(260, 6, 'SURCURSAL: [' . $gCcoId . '] ' . $gCcoNom, 0, 0, 'C');
                                                $this->Ln(5);
                                                $n -= 5;
                                            }
                                            if ($gTerId <> "") {
                                                $this->SetFont('verdana', '', 8);
                                                $this->SetX(13);
                                                $this->Cell(260, 6, 'CLIENTE: [' . $gTerId . '] ' . $gTerNom, 0, 0, 'C');
                                                $this->Ln(5);
                                                $n -= 5;
                                            }
                                            if ($gDirId <> "") {
                                                $this->SetFont('verdana', '', 8);
                                                $this->SetX(13);
                                                $this->Cell(260, 6, 'DIRECTOR: [' . $gDirId . '] ' . $gDirNom, 0, 0, 'C');
                                                $this->Ln(5);
                                                $n -= 5;
                                            }


                                            $this->Ln($n);
                                            $this->SetX(13);
                                        }
                                        if ($this->PageNo() > 1 && $nPag == 1) {
                                           /* $this->SetFillColor(11, 97, 11);
                                            $this->SetTextColor(255);*/
                                            $this->SetFont('verdana', 'B', 6);
                                            $this->SetX(13);
                                            /*$this->Cell(17, 10, "Tramite", 1, 0, 'C', 1);
                                            $this->Cell(8, 10, "Suc", 1, 0, 'C', 1);
                                           // $this->Cell(20, 5, "Pedido", 1, 0, 'C', 1);
                                            $this->Cell(12, 10, "Fecha", 1, 0, 'C', 1);
                                           // $this->Cell(18, 5, "Operacion", 1, 0, 'C', 1);
                                            $this->Cell(12, 10, "M. Levante", 1, 0, 'C', 1);
                                            $this->Cell(17, 10, "Nit", 1, 0, 'C', 1);
                                            $this->Cell(25, 10, "Cliente", 1, 0, 'C', 1);
                                            $this->Cell(10, 10, "Estado", 1, 0, 'C', 1);
																						$this->Cell(15, 10, "Ent. Carpeta", 1, 0, 'C', 1);
                                            $this->Cell(15, 10, "Cierre", 1, 0, 'C', 1);
                                            
                                            $this->Cell(25, 10, "D&iacute;as Transcurridos", 1, 0, 'C', 1);
                                            $this->Cell(25, 10, "Entrega Facturaci&oacute;n", 1, 0, 'C', 1);
                                            $this->Cell(25, 10, "D&iacute;as Transcurridos Fac. y Ent.", 1, 0, 'C', 1);
                                            
                                            $this->Cell(15, 10, "Director", 1, 0, 'C', 1);
                                            $this->Cell(10, 10, "Anticipo", 1, 0, 'C', 1);
                                            $this->Cell(10, 10, "Pagos", 1, 0, 'C', 1);
                                            $this->Cell(10, 10, "Saldo", 1, 0, 'C', 1);*/
                                            $this->SetFillColor(11, 97, 11);
                                            $this->SetTextColor(0);
                                            $this->SetWidths(array('20', '8', '15', '15', '17', '30', '13', '15', '15', '15', '20', '20', '21', '12', '12', '12'));
                                            $this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
																					 	$this->Row(array("Tramite",
																							"Suc",
																			 				"Fecha",
																			 				"M. Levante",
																			 				"Nit",
																			 				"Cliente",
																			 				"Estado",
																							"Entrega Carpeta a Fac.",
																							"Cierre",
																							 utf8_decode("Días Transcurridos"),
																							 utf8_decode("Entrega Facturación"),
																							 utf8_decode("Días Transcurridos Fac. y Ent."),
																							 "Director",
																							 "Anticipo", 
																							 "Pagos",
																							 "Saldo"
																							),true
																						);
                                            $this->SetFillColor(255);
                                            $this->SetTextColor(0);

                                            $this->SetX(13);
                                            $this->SetFont('verdana', '', 6);
                                            $this->SetAligns(array('L', 'C', 'C', 'C', 'C', 'L', 'C', 'L', 'L', 'C', 'C', 'C', 'C', 'C', 'C', 'R'));
                                            $this->SetX(13);
                                        }
                                    }

                                    function Footer() {
                                        $this->SetY(-10);
                                        $this->SetFont('verdana', '', 6);
                                        $this->Cell(0, 5, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
                                    }

                                    function SetWidths($w) {
                                        //Set the array of column widths
                                        $this->widths = $w;
                                    }

                                    function SetAligns($a) {
                                        //Set the array of column alignments
                                        $this->aligns = $a;
                                    }

                                    function Row($data, $bColor) {
                                        //Calculate the height of the row
                                        $nb = 0;
                                        for ($i = 0; $i < count($data); $i++)
                                            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
                                        $h = 4 * $nb;
                                        //Issue a page break first if needed
                                        $this->CheckPageBreak($h);
                                        //Draw the cells of the row
                                        for ($i = 0; $i < count($data); $i++) {
                                            $w = $this->widths[$i];
                                            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                                            //Save the current position
                                            $x = $this->GetX();
                                            $y = $this->GetY();
																						
																						if ( $bColor == true ) {
																							$this->SetFillColor(11, 97, 11);
																						} else {
																							$this->SetFillColor(255,255,255);
																						}
                                            //Draw the border
                                            $this->Rect($x, $y, $w, $h,'DF');
                                            //Print the text
                                            $this->MultiCell($w, 4, $data[$i], 0, $a);
                                            //Put the position to the right of the cell
                                            $this->SetXY($x + $w, $y);
                                        }
                                        //Go to the next line
                                        $this->Ln($h);
                                    }

                                    function CheckPageBreak($h) {
                                        //If the height h would cause an overflow, add a new page immediately
                                        if ($this->GetY() + $h > $this->PageBreakTrigger)
                                            $this->AddPage($this->CurOrientation);
                                    }

                                    function NbLines($w, $txt) {
                                        //Computes the number of lines a MultiCell of width w will take
                                        $cw = &$this->CurrentFont['cw'];
                                        if ($w == 0)
                                            $w = $this->w - $this->rMargin - $this->x;
                                        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
                                        $s = str_replace("\r", '', $txt);
                                        $nb = strlen($s);
                                        if ($nb > 0 and $s[$nb - 1] == "\n")
                                            $nb--;
                                        $sep = -1;
                                        $i = 0;
                                        $j = 0;
                                        $l = 0;
                                        $nl = 1;
                                        while ($i < $nb) {
                                            $c = $s[$i];
                                            if ($c == "\n") {
                                                $i++;
                                                $sep = -1;
                                                $j = $i;
                                                $l = 0;
                                                $nl++;
                                                continue;
                                            }
                                            if ($c == ' ')
                                                $sep = $i;
                                            $l+=$cw[$c];
                                            if ($l > $wmax) {
                                                if ($sep == -1) {
                                                    if ($i == $j)
                                                        $i++;
                                                } else
                                                    $i = $sep + 1;
                                                $sep = -1;
                                                $j = $i;
                                                $l = 0;
                                                $nl++;
                                            } else
                                                $i++;
                                        }
                                        return $nl;
                                    }

                                }

                                $pdf = new PDF('L', 'mm', 'Letter');
                                $pdf->AddFont('verdana', '', '');
                                $pdf->AddFont('verdana', 'B', '');
                                $pdf->AliasNbPages();
                                $pdf->SetMargins(0, 0, 0);

                                $pdf->AddPage();
                               /* $pdf->SetFillColor(11, 97, 11);
                                $pdf->SetTextColor(255);*/
                                $pdf->SetFont('verdana', 'B', 6);
                                $pdf->SetX(13);
                                /*$pdf->Cell(20, 10, "Tramite", 1, 0, 'C', 1);
                                $pdf->Cell(10, 10, "Suc", 1, 0, 'C', 1);
                                //$pdf->Cell(20, 5, "Pedido", 1, 0, 'C', 1);
                                $pdf->Cell(15, 10, "Fecha", 1, 0, 'C', 1);
                              //  $pdf->Cell(18, 5, "Operacion", 1, 0, 'C', 1);
                                $pdf->Cell(15, 10, "M. Levante", 1, 0, 'C', 1);
                                
                                $pdf->Cell(17, 10, "Nit", 1, 0, 'C', 1);
                                $pdf->Cell(25, 10, "Cliente", 1, 0, 'C', 1);
                                $pdf->Cell(10, 10, "Estado", 1, 0, 'C', 1);
																$pdf->Cell(15, 10, "Ent. Carpeta", 1, 0, 'C', 1);
                                $pdf->Cell(15, 10, "Cierre", 1, 0, 'C', 1);

                                $pdf->Cell(25, 10, utf8_decode("Días Transcurridos"), 1, 0, 'C', 1);
                                $pdf->Cell(25, 10, utf8_decode("Entrega Facturación"), 1, 0, 'C', 1);
                                $pdf->Cell(25, 10, utf8_decode("Días Transcurridos Fac. y Ent."), 1, 0, 'C', 1);

                                $pdf->Cell(15, 10, "Director", 1, 0, 'C', 1);
                                $pdf->Cell(10, 10, "Anticipo", 1, 0, 'C', 1);
                                $pdf->Cell(10, 10, "Pagos", 1, 0, 'C', 1);
                                $pdf->Cell(10, 10, "Saldo", 1, 0, 'C', 1);*/
                                $pdf->SetFillColor(11, 97, 11);
                                $pdf->SetTextColor(0);
                                $pdf->SetWidths(array('20', '8', '15', '15', '17', '30', '13', '15', '15', '15', '20', '20', '21', '12', '12', '12'));
                                $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
																$pdf->Row(array("Tramite",
																				"Suc",
																 				"Fecha",
																 				"M. Levante",
																 				"Nit",
																 				"Cliente",
																 				"Estado",
																				"Entrega Carpeta a Fac.",
																				"Cierre",
																				 utf8_decode("Días Transcurridos"),
																				 utf8_decode("Entrega Facturación"),
																				 utf8_decode("Días Transcurridos Fac. y Ent."),
																				 "Director",
																				 "Anticipo", 
																				 "Pagos",
																				 "Saldo"
																				),true
																			);
                                $pdf->SetFillColor(255);
                                $pdf->SetTextColor(0);

                                $pdf->SetX(13);
                                $pdf->SetFont('verdana', '', 6);
                                $pdf->SetAligns(array('L', 'C', 'C', 'C', 'C', 'L', 'C', 'L', 'L', 'C', 'C', 'C', 'C', 'C', 'C', 'R'));
                                $nPag = 0;
                                break;
                        }

                        #Trayendo comprobantes
                        #Rango de los A�os en donde debo buscar los datos
                        $nAnoI = (substr($gDesde, 0, 4) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : substr($gDesde, 0, 4);
                        $nAnoF = ((substr($gHasta, 0, 4) + 1) > date('Y')) ? date('Y') : (substr($gHasta, 0, 4) + 1);

                        $mTabDet = array(); //Array con nombre de las tablas temporales para los ajustes, anticipos y pagos a terceros en la tabla de detalle
                        $mTabRec = array(); //Array con nombre de las tablas temporales para los recibos de caja en la tabla de detalle

                        $mCtoAnt = array(); //Array con la marca de ancipos por cuenta-concepto
                        $mCtoPCC = array(); //Array con la marca de pcc por cuenta-concepto
                        //Buscano conceptos de causaciones automaticas pcc
                        $qCAyP121 = "SELECT DISTINCT $cAlfa.fpar0121.pucidxxx, $cAlfa.fpar0121.ctoidxxx FROM $cAlfa.fpar0121 WHERE $cAlfa.fpar0121.regestxx = \"ACTIVO\"";
                        $xCAyP121 = f_MySql("SELECT", "", $qCAyP121, $xConexion01, "");
                        //f_Mensaje(__FILE__,__LINE__,$qCAyP121."~".mysql_num_rows($xCAyP121));
                        $cCAyP121 = "";
                        while ($xRCP121 = mysql_fetch_array($xCAyP121)) {
                            $cCAyP121 .= "\"{$xRCP121['pucidxxx']}~{$xRCP121['ctoidxxx']}\",";
                            $mCtoPCC[count($mCtoPCC)] = "{$xRCP121['pucidxxx']}~{$xRCP121['ctoidxxx']}";
                        }

                        //Buscando conceptos pcc y anticipos
                        $qCtoAntyPCC = "SELECT DISTINCT $cAlfa.fpar0119.ctoantxx,$cAlfa.fpar0119.ctopccxx,$cAlfa.fpar0119.pucidxxx, $cAlfa.fpar0119.ctoidxxx FROM $cAlfa.fpar0119 WHERE ($cAlfa.fpar0119.ctoantxx = \"SI\" OR $cAlfa.fpar0119.ctopccxx = \"SI\") AND $cAlfa.fpar0119.regestxx = \"ACTIVO\"";
                        $xCtoAntyPCC = f_MySql("SELECT", "", $qCtoAntyPCC, $xConexion01, "");
                        //f_Mensaje(__FILE__,__LINE__,$qCtoAntyPCC."~".mysql_num_rows($xCtoAntyPCC));
                        $cCtoAntyPCC = "";
                        while ($xRCAP = mysql_fetch_array($xCtoAntyPCC)) {
                            $cCtoAntyPCC .= "\"{$xRCAP['pucidxxx']}~{$xRCAP['ctoidxxx']}\",";
                            if ($xRCAP['ctoantxx'] == "SI") {
                                $mCtoAnt[count($mCtoAnt)] = "{$xRCAP['pucidxxx']}~{$xRCAP['ctoidxxx']}";
                            }
                            if ($xRCAP['ctopccxx'] == "SI") {
                                $mCtoPCC[count($mCtoPCC)] = "{$xRCAP['pucidxxx']}~{$xRCAP['ctoidxxx']}";
                            }
                        }
                        $cCtoAntyPCC = $cCAyP121 . substr($cCtoAntyPCC, 0, strlen($cCtoAntyPCC) - 1);

                        for ($nPerAno = $nAnoI; $nPerAno <= $nAnoF; $nPerAno++) {
                            #Creando Tabla temporal de PCC, anticipos
                            $cFcod = "fcod" . $nPerAno;
                            $cTabFac = fnCadenaAleatoria();
                            $qNewTab = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcod";
                            $xNewTab = mysql_query($qNewTab, $xConexion01);

                            $qMovDO = "SELECT  $cAlfa.fcod$nPerAno.* ";
                            $qMovDO .= "FROM $cAlfa.fcod$nPerAno ";
                            $qMovDO .= "WHERE ";
                            if ($gDocNro <> "") {
                                $qMovDO .= "$cAlfa.fcod$nPerAno.comcsccx =  \"$gDocNro\" AND ";
                                $qMovDO .= "$cAlfa.fcod$nPerAno.comseqcx =  \"$gDocSuf\" AND ";
                            }
                            $qMovDO .= "CONCAT($cAlfa.fcod$nPerAno.pucidxxx,\"~\",$cAlfa.fcod$nPerAno.ctoidxxx) IN ($cCtoAntyPCC) AND "; //PCC Y ANTICPOS
                            $qMovDO .= "$cAlfa.fcod$nPerAno.comidxxx <> \"F\"  AND ";
                            $qMovDO .= "$cAlfa.fcod$nPerAno.comfacxx =  \"\"   AND ";
                            $qMovDO .= "$cAlfa.fcod$nPerAno.regestxx =  \"ACTIVO\" ";
                            //echo $qMovDO."<br><br>";

                            $qInsert = "INSERT INTO $cAlfa.$cTabFac $qMovDO";
                            $xInsert = mysql_query($qInsert, $xConexion01);
                            $mTabDet[$nPerAno] = $cTabFac;
                            #Fin Creando Tabla temporal de PCC, anticipos y ajustes
                            #Creando tabla temporal de recibos de caja
                            $cFcod = "fcme" . $nPerAno;
                            $cTabFac = fnCadenaAleatoria();
                            $qNewTab = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcod";
                            $xNewTab = mysql_query($qNewTab, $xConexion01);

                            $qRecCaj = "SELECT * ";
                            $qRecCaj .= "FROM $cAlfa.fcme$nPerAno ";
                            $qRecCaj .= "WHERE ";
                            if ($gDocNro <> "") {
                                $qRecCaj .= "$cAlfa.fcme$nPerAno.comcsccx = \"$gDocNro\" AND ";
                                $qRecCaj .= "$cAlfa.fcme$nPerAno.comseqcx = \"$gDocSuf\" AND ";
                            }
                            $qRecCaj .= "$cAlfa.fcme$nPerAno.regestxx IN (\"ACTIVO\",\"PROVISIONAL\") ";
                            //echo $qRecCaj."~".mysql_num_rows($xRecCaj)."<br><br>";

                            $qInsert = "INSERT INTO $cAlfa.$cTabFac $qMovDO";
                            $xInsert = mysql_query($qInsert, $xConexion01);
                            $mTabRec[$nPerAno] = $cTabFac;
                            #Fin Creando tabla temporal de recibos de caja
                        }
                        #Fin Trayendo comprobantes

                        $qDatDoi = "SELECT ";
                        $qDatDoi .= "$cAlfa.sys00121.sucidxxx, ";
                        $qDatDoi .= "$cAlfa.sys00121.docidxxx, ";
                        $qDatDoi .= "$cAlfa.sys00121.docsufxx, ";
                        $qDatDoi .= "$cAlfa.sys00121.comidxxx, ";
                        $qDatDoi .= "$cAlfa.sys00121.comcodxx, ";
                        $qDatDoi .= "$cAlfa.sys00121.ccoidxxx, ";
                        $qDatDoi .= "$cAlfa.sys00121.pucidxxx, ";
                        $qDatDoi .= "$cAlfa.sys00121.succomxx, ";
                        $qDatDoi .= "$cAlfa.sys00121.doctipxx, ";
                        $qDatDoi .= "$cAlfa.sys00121.docpedxx, ";
                        $qDatDoi .= "$cAlfa.sys00121.cliidxxx, ";
                        $qDatDoi .= "$cAlfa.sys00121.diridxxx, ";
                        $qDatDoi .= "$cAlfa.sys00121.docffecx, ";
                        $qDatDoi .= "$cAlfa.sys00121.regfcrex, ";
                        $qDatDoi .= "$cAlfa.sys00121.regestxx ";
                        $qDatDoi .= "FROM $cAlfa.sys00121 ";
                        $qDatDoi .= "WHERE ";
                        if ($gDocNro <> "") {
                            $qDatDoi .= "$cAlfa.sys00121.sucidxxx = \"$gSucId\" AND ";
                            $qDatDoi .= "$cAlfa.sys00121.docidxxx = \"$gDocNro\" AND ";
                            $qDatDoi .= "$cAlfa.sys00121.docsufxx = \"$gDocSuf\" AND ";
                        }
                        if ($gCcoId <> "") {
                            $qDatDoi .= "$cAlfa.sys00121.sucidxxx = \"$gSucCco\" AND ";
                        }
                        if ($gTerId <> "") {
                            $qDatDoi .= "$cAlfa.sys00121.cliidxxx = \"$gTerId\" AND ";
                        }
                        if ($gDirId <> "") {
                            $qDatDoi .= "$cAlfa.sys00121.diridxxx = \"$gDirId\" AND ";
                        }
                        $qDatDoi .= "$cAlfa.sys00121.regfcrex BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
                        $qDatDoi .= "$cAlfa.sys00121.regestxx = \"$gEstado\" ";
                        $qDatDoi .= "ORDER BY $cAlfa.sys00121.regfcrex";
                        $xDatDoi = f_MySql("SELECT", "", $qDatDoi, $xConexion01, "");
                        //f_Mensaje(__FILE__,__LINE__,$qDatDoi."~".mysql_num_rows($xDatDoi));
                        #Recorro Do's
                        $nCanReg = 0; //Contador de registros

                        while ($xDD = mysql_fetch_array($xDatDoi)) {
                            #Busco el nombre del cliente
                            $qCliNom = "SELECT ";
                            $qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,' ',CLINOM2X,' ',CLIAPE1X,' ',CLIAPE2X)) <> \"\",TRIM(CONCAT(CLINOM1X,' ',CLINOM2X,' ',CLIAPE1X,' ',CLIAPE2X)), CLINOMXX) AS clinomxx ";
                            $qCliNom .= "FROM $cAlfa.SIAI0150 ";
                            $qCliNom .= "WHERE ";
                            $qCliNom .= "CLIIDXXX = \"{$xDD['cliidxxx']}\" LIMIT 0,1";
                            $xCliNom = f_MySql("SELECT", "", $qCliNom, $xConexion01, "");
                            if (mysql_num_rows($xCliNom) > 0) {
                                $xRCN = mysql_fetch_array($xCliNom);
                                $xDD['clinomxx'] = $xRCN['clinomxx'];
                            } else {
                                $xDD['clinomxx'] = "CLIENTE SIN NOMBRE";
                            }

                            #Busco el nombre del director de cuenta
                            $qNomDir = "SELECT ";
                            $qNomDir .= "IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"DIRECTOR SIN NOMBRE\") AS dirnomxx ";
                            $qNomDir .= "FROM $cAlfa.SIAI0003 ";
                            $qNomDir .= "WHERE ";
                            $qNomDir .= "USRIDXXX = \"{$xDD['diridxxx']}\" LIMIT 0,1";
                            $xNomDir = f_MySql("SELECT", "", $qNomDir, $xConexion01, "");
                            if (mysql_num_rows($xNomDir) > 0) {
                                $xRND = mysql_fetch_array($xNomDir);
                                $xDD['dirnomxx'] = $xRND['dirnomxx'];
                            } else {
                                $xDD['dirnomxx'] = "DIRECTOR SIN NOMBRE";
                            }

                            $nAnticipo = 0;
                            $nPagosTer = 0;

                            $nAno01 = substr($xDD['regfcrex'], 0, 4);
                            $cTabFac = $mTabDet[$nAno01];

                            // Buscando Anticipos y PCC
                            $qMovDO = "SELECT ";
                            $qMovDO .= "$cAlfa.$cTabFac.comidxxx, ";
                            $qMovDO .= "$cAlfa.$cTabFac.comcodxx, ";
                            $qMovDO .= "$cAlfa.$cTabFac.comcscxx, ";
                            $qMovDO .= "$cAlfa.$cTabFac.comseqxx, ";
                            $qMovDO .= "$cAlfa.$cTabFac.ccoidxxx, ";
                            $qMovDO .= "$cAlfa.$cTabFac.pucidxxx, ";
                            $qMovDO .= "$cAlfa.$cTabFac.ctoidxxx, ";
                            $qMovDO .= "$cAlfa.$cTabFac.sucidxxx, ";
                            $qMovDO .= "$cAlfa.$cTabFac.docidxxx, ";
                            $qMovDO .= "$cAlfa.$cTabFac.docsufxx, ";
                            $qMovDO .= "$cAlfa.$cTabFac.commovxx, ";
                            $qMovDO .= "IF($cAlfa.$cTabFac.commovxx=\"C\",$cAlfa.$cTabFac.comvlrxx*-1,$cAlfa.$cTabFac.comvlrxx) AS anticipo, ";
                            $qMovDO .= "IF($cAlfa.$cTabFac.commovxx=\"C\",$cAlfa.$cTabFac.comvlrxx*-1,$cAlfa.$cTabFac.comvlrxx) AS pagoster  ";
                            $qMovDO .= "FROM $cAlfa.$cTabFac ";
                            $qMovDO .= "WHERE ";
                            $qMovDO .= "$cAlfa.$cTabFac.comcsccx =  \"{$xDD['docidxxx']}\" AND ";
                            $qMovDO .= "$cAlfa.$cTabFac.comseqcx =  \"{$xDD['docsufxx']}\" AND ";
                            $qMovDO .= "$cAlfa.$cTabFac.regestxx =  \"ACTIVO\" ";
                            $xMovDO = mysql_query($qMovDO, $xConexion01);
                            //echo "fcod".$nAno01."~".$qMovDO."~".mysql_num_rows($xMovDO)."<br><br>";
                            while ($xRMD = mysql_fetch_array($xMovDO)) {

                                //Anticipos
                                if (in_array("{$xRMD['pucidxxx']}~{$xRMD['ctoidxxx']}", $mCtoAnt) == true) {
                                    //echo "ANT ~~> ".$xRMD['comidxxx']."~".$xRMD['comcodxx']."~".$xRMD['comcscxx']."~".$xRMD['comseqxx']." ~~ ".$xRMD['commovxx']." ~~ ".$xRMD['anticipo']."<br>";
                                    $nSw_Incluir = 0;
                                    if ($xRMD['sucidxxx'] != "" && $xRMD['docidxxx'] != "" && $xRMD['docsufxx'] != "") {
                                        //si tiene los campos de sucursal, do y sufijo digitados se hace la comparacion con estos campos
                                        if ($xRMD['sucidxxx'] == $xDD['sucidxxx'] && $xRMD['docidxxx'] == $xDD['docidxxx'] && $xRMD['docsufxx'] == $xDD['docsufxx']) {
                                            $nSw_Incluir = 1;
                                        }
                                    } else {
                                        //Comparando por el centro de costo
                                        if ($xRMD['ccoidxxx'] == $xDD['ccoidxxx']) {
                                            $nSw_Incluir = 1;
                                        }
                                    }

                                    if ($nSw_Incluir == 1) {
                                        $nAnticipo += $xRMD['anticipo'];
                                    }
                                }

                                //PCC
                                if (in_array("{$xRMD['pucidxxx']}~{$xRMD['ctoidxxx']}", $mCtoPCC) == true) {
                                    //echo "PCC ~~> ".$xRMD['comidxxx']."~".$xRMD['comcodxx']."~".$xRMD['comcscxx']."~".$xRMD['comseqxx']." ~~ ".$xRMD['commovxx']." ~~ ".$xRMD['pagoster']."<br>";
                                    $nSw_Incluir = 0;
                                    if ($xRMD['sucidxxx'] != "" && $xRMD['docidxxx'] != "" && $xRMD['docsufxx'] != "") {
                                        //si tiene los campos de sucursal, do y sufijo digitados se hace la comparacion con estos campos
                                        if ($xRMD['sucidxxx'] == $xDD['sucidxxx'] && $xRMD['docidxxx'] == $xDD['docidxxx'] && $xRMD['docsufxx'] == $xDD['docsufxx']) {
                                            $nSw_Incluir = 1;
                                        }
                                    } else {
                                        //Comparando por el centro de costo
                                        if ($xRMD['ccoidxxx'] == $xDD['ccoidxxx']) {
                                            $nSw_Incluir = 1;
                                        }
                                    }

                                    if ($nSw_Incluir == 1) {
                                        $nPagosTer += $xRMD['pagoster'];
                                    }
                                }
                            } ## while ($xRMD = mysql_fetch_array($xMovDO)) {##

                            $nAno02 = (($nAno01 + 1) > date('Y')) ? date('Y') : ($nAno01 + 1);
                            if ($nAno02 > $nAno01) {
                                $qMovDO = str_replace($cTabFac, $mTabDet[$nAno02], $qMovDO);
                                $xMovDO = mysql_query($qMovDO, $xConexion01);
                                //echo "fcod".$nAno02."~".$qMovDO."~".mysql_num_rows($xMovDO)."<br><br>";
                                while ($xRMD = mysql_fetch_array($xMovDO)) {
                                    //Anticipos
                                    if (in_array("{$xRMD['pucidxxx']}~{$xRMD['ctoidxxx']}", $mCtoAnt) == true) {
                                        $nSw_Incluir = 0;
                                        if ($xRMD['sucidxxx'] != "" && $xRMD['docidxxx'] != "" && $xRMD['docsufxx'] != "") {
                                            //si tiene los campos de sucursal, do y sufijo digitados se hace la comparacion con estos campos
                                            if ($xRMD['sucidxxx'] == $xDD['sucidxxx'] && $xRMD['docidxxx'] == $xDD['docidxxx'] && $xRMD['docsufxx'] == $xDD['docsufxx']) {
                                                $nSw_Incluir = 1;
                                            }
                                        } else {
                                            //Comparando por el centro de costo
                                            if ($xRMD['ccoidxxx'] == $xDD['ccoidxxx']) {
                                                $nSw_Incluir = 1;
                                            }
                                        }

                                        if ($nSw_Incluir == 1) {
                                            $nAnticipo += $xRMD['anticipo'];
                                        }
                                    }

                                    //PCC
                                    if (in_array("{$xRMD['pucidxxx']}~{$xRMD['ctoidxxx']}", $mCtoPCC) == true) {
                                        $nSw_Incluir = 0;
                                        if ($xRMD['sucidxxx'] != "" && $xRMD['docidxxx'] != "" && $xRMD['docsufxx'] != "") {
                                            //si tiene los campos de sucursal, do y sufijo digitados se hace la comparacion con estos campos
                                            if ($xRMD['sucidxxx'] == $xDD['sucidxxx'] && $xRMD['docidxxx'] == $xDD['docidxxx'] && $xRMD['docsufxx'] == $xDD['docsufxx']) {
                                                $nSw_Incluir = 1;
                                            }
                                        } else {
                                            //Comparando por el centro de costo
                                            if ($xRMD['ccoidxxx'] == $xDD['ccoidxxx']) {
                                                $nSw_Incluir = 1;
                                            }
                                        }

                                        if ($nSw_Incluir == 1) {
                                            $nPagosTer += $xRMD['pagoster'];
                                        }
                                    }
                                } ## while ($xRMD = mysql_fetch_array($xMovDO)) { ##
                            }
                            //echo "Total Anticipos {$xDD['docidxxx']}-{$xDD['docsufxx']}: ".$nAnticipo."<br><br>";
                            //echo "Total PCC {$xDD['docidxxx']}-{$xDD['docsufxx']}: ".$nPagosTer."<br><br>";
                            #Fin Buscando Anticipos y PCC
                            #Recibos de caja
                            $nAno01 = substr($xDD['regfcrex'], 0, 4);
                            $cTabFac = $mTabRec[$nAno01];

                            $qRecCaj = "SELECT ";
                            $qRecCaj .= "$cAlfa.$cTabFac.sucidxxx, ";
                            $qRecCaj .= "$cAlfa.$cTabFac.docidxxx, ";
                            $qRecCaj .= "$cAlfa.$cTabFac.docsufxx, ";
                            $qRecCaj .= "$cAlfa.$cTabFac.ccoidxxx, ";
                            $qRecCaj .= "IF($cAlfa.$cTabFac.commovxx=\"D\",$cAlfa.$cTabFac.comvlrxx,0) AS pagoster ";
                            $qRecCaj .= "FROM $cAlfa.$cTabFac ";
                            $qRecCaj .= "WHERE ";
                            $qRecCaj .= "$cAlfa.$cTabFac.comcsccx = \"{$xDD['docidxxx']}\" AND ";
                            $qRecCaj .= "$cAlfa.$cTabFac.comseqcx = \"{$xDD['docsufxx']}\" AND ";
                            $qRecCaj .= "$cAlfa.$cTabFac.regestxx IN (\"ACTIVO\",\"PROVISIONAL\") ";
                            $xRecCaj = mysql_query($qRecCaj, $xConexion01);
                            //echo "Recibos de caja:<br><br>";
                            //echo "fcme".$nAno01."~".$qRecCaj."~".mysql_num_rows($xRecCaj)."<br><br>";
                            while ($xRRC = mysql_fetch_array($xRecCaj)) {
                                $nSw_Incluir = 0;
                                if ($xRRC['sucidxxx'] != "" && $xRRC['docidxxx'] != "" && $xRRC['docsufxx'] != "") {
                                    if ($xRRC['sucidxxx'] == $xDD['sucidxxx'] && $xRRC['docidxxx'] == $xDD['docidxxx'] && $xRRC['docsufxx'] == $xDD['docsufxx']) {
                                        $nSw_Incluir = 1;
                                    }
                                } else {
                                    if ($xRRC['ccoidxxx'] == "" && $xRRC['ccoidxxx']) {
                                        $nSw_Incluir = 1;
                                    }
                                }

                                if ($nSw_Incluir == 1) {
                                    $nPagosTer += $xRRC['pagoster'];
                                }
                            }

                            $nAno02 = (($nAno01 + 1) > date('Y')) ? date('Y') : ($nAno01 + 1);
                            if ($nAno02 > $nAno01) {
                                $qRecCaj = str_replace($cTabFac, $mTabRec[$nAno02], $qRecCaj);
                                $xRecCaj = mysql_query($qRecCaj, $xConexion01);
                                //echo "fcme".$nAno02."~".$qRecCaj."~".mysql_num_rows($xRecCaj)."<br><br>";
                                while ($xRRC = mysql_fetch_array($xRecCaj)) {
                                    $nSw_Incluir = 0;
                                    if ($xRRC['sucidxxx'] != "" && $xRRC['docidxxx'] != "" && $xRRC['docsufxx'] != "") {
                                        if ($xRRC['sucidxxx'] == $xDD['sucidxxx'] && $xRRC['docidxxx'] == $xDD['docidxxx'] && $xRRC['docsufxx'] == $xDD['docsufxx']) {
                                            $nSw_Incluir = 1;
                                        }
                                    } else {
                                        if ($xRRC['ccoidxxx'] == "" && $xRRC['ccoidxxx']) {
                                            $nSw_Incluir = 1;
                                        }
                                    }

                                    if ($nSw_Incluir == 1) {
                                        $nPagosTer += $xRRC['pagoster'];
                                    }
                                }
                            }
                            //echo "Total Pagos a terceros: ".$nPagosTer."<br><br>"; 
                            #Fin Buscando pagos a terceros
                            #Buscando valor formualios
                            $nValFor = 0;

                            $qDatFor = "SELECT ";
                            $qDatFor .= "$cAlfa.ffoi0000.comvlrxx AS formuxx ";
                            $qDatFor .= "FROM $cAlfa.ffoi0000 ";
                            $qDatFor .= "WHERE ";
                            $qDatFor .= "$cAlfa.ffoi0000.sucidxxx = \"{$xDD['sucidxxx']}\" AND ";
                            $qDatFor .= "$cAlfa.ffoi0000.doccomex = \"{$xDD['docidxxx']}\" AND ";
                            $qDatFor .= "$cAlfa.ffoi0000.docsufxx = \"{$xDD['docsufxx']}\" AND ";
                            $qDatFor .= "$cAlfa.ffoi0000.regestxx = \"CONDO\" ";
                            $xDatFor = f_MySql("SELECT", "", $qDatFor, $xConexion01, "");
                            //echo "Formularios:<br><br>";
                            //echo "ffoi0000~".$qDatFor."~".mysql_num_rows($xDatFor)."<br><br>";
                            while ($xDDF = mysql_fetch_array($xDatFor)) {
                                $nValFor += $xDDF['formuxx'];
                            }
                            //echo "Total Formularios: ".$nValFor."<br><br>";
                            #Fin Buscando valor formualios
                            ## Buscando Fecha Mayor De Levante y Fecha de Entrega a Facturacion ##
                            $qDat200 = "SELECT ";
                            $qDat200 .= "$cAlfa.SIAI0200.DOIMYLEV, ";
                            $qDat200 .= "$cAlfa.SIAI0200.DOIFENCA ";
                            $qDat200 .= "FROM $cAlfa.SIAI0200 ";
                            $qDat200 .= "WHERE ";
                            $qDat200 .= "$cAlfa.SIAI0200.DOIIDXXX = \"{$xDD['docidxxx']}\" AND ";
                            $qDat200 .= "$cAlfa.SIAI0200.DOISFIDX = \"{$xDD['docsufxx']}\" AND ";
                            $qDat200 .= "$cAlfa.SIAI0200.ADMIDXXX = \"{$xDD['sucidxxx']}\" AND ";
                            $qDat200 .= "$cAlfa.SIAI0200.regestxx = \"ACTIVO\" LIMIT 0,1";
                           // f_Mensaje(__FILE__,__LINE__,$qDat200);
                            $xDat200 = f_MySql("SELECT", "", $qDat200, $xConexion01, "");
                            if (mysql_num_rows($xDat200) > 0) {
                                $xDDO = mysql_fetch_array($xDat200);
                                $xDD['doimylev'] = $xDDO['DOIMYLEV'];
                                $xDD['doifenca'] = $xDDO['DOIFENCA'];
                            }

                            $nCanReg++;

                            ## Fin Buscando Fecha Mayor De Levante y Fecha de Entrega a Facturacion##
                            ## Inicio Busqueda de fecha Entrega de Factura al cliente, y dias transcurrido entre 1. Fecha entrega de facturacion y generacion de factura. 2. Fecha generacion de factura y Factura entregada al cliente ##
                            switch ($xDD['doctipxx']) {
                                case "IMPORTACION":
                                    ## Buscando Fecha de Entrega al Cliente ##
                                    $qDat200 = "SELECT ";
                                    $qDat200 .= "$cAlfa.SIAI0200.DOIFEFAC ";
                                    $qDat200 .= "FROM $cAlfa.SIAI0200 ";
                                    $qDat200 .= "WHERE ";
                                    $qDat200 .= "$cAlfa.SIAI0200.DOIIDXXX = \"{$xDD['docidxxx']}\" AND ";
                                    $qDat200 .= "$cAlfa.SIAI0200.DOISFIDX = \"{$xDD['docsufxx']}\" AND ";
                                    $qDat200 .= "$cAlfa.SIAI0200.ADMIDXXX = \"{$xDD['sucidxxx']}\" AND ";
                                    $qDat200 .= "$cAlfa.SIAI0200.regestxx = \"ACTIVO\" LIMIT 0,1";
                                    $xDat200 = f_MySql("SELECT", "", $qDat200, $xConexion01, "");
                                    if (mysql_num_rows($xDat200) > 0) {
                                        $xDDO = mysql_fetch_array($xDat200);
                                        $xDD['doifefac'] = $xDDO['DOIFEFAC'];
                                    }

                                    break;
                                case "EXPORTACION":
                                    ## Buscando Fecha Mayor De Levante y Fecha de Entrega a Facturacion ##
                                    $qDat200 = "SELECT ";
                                    $qDat200 .= "$cAlfa.siae0199.dexfentr ";
                                    $qDat200 .= "FROM $cAlfa.siae0199 ";
                                    $qDat200 .= "WHERE ";
                                    $qDat200 .= "$cAlfa.siae0199.dexidxxx = \"{$xDD['docidxxx']}\" AND ";
                                    //$qDat200 .= "$cAlfa.siae0199.docsufxx = \"{$xDD['docsufxx']}\" AND ";
                                    $qDat200 .= "$cAlfa.siae0199.admidxxx = \"{$xDD['sucidxxx']}\" AND ";
                                    $qDat200 .= "$cAlfa.siae0199.regestxx = \"ACTIVO\" LIMIT 0,1";
                                    $xDat200 = f_MySql("SELECT", "", $qDat200, $xConexion01, "");
                                    if (mysql_num_rows($xDat200) > 0) {
                                        $xDDO = mysql_fetch_array($xDat200);
                                        $xDD['doifefac'] = $xDDO['dexfentr'];
                                    }

                                    break;
                                default:
                                    ## Buscando Fecha Mayor De Levante y Fecha de Entrega a Facturacion ##
                                    $qDat200 = "SELECT ";
                                    $qDat200 .= "$cAlfa.sys00121.docfefac ";
                                    $qDat200 .= "FROM $cAlfa.sys00121 ";
                                    $qDat200 .= "WHERE ";
                                    $qDat200 .= "$cAlfa.sys00121.docidxxx = \"{$xDD['docidxxx']}\" AND ";
                                    $qDat200 .= "$cAlfa.sys00121.docsufxx = \"{$xDD['docsufxx']}\" AND ";
                                    $qDat200 .= "$cAlfa.sys00121.sucidxxx = \"{$xDD['sucidxxx']}\" AND ";
                                    $qDat200 .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" LIMIT 0,1";
                                    $xDat200 = f_MySql("SELECT", "", $qDat200, $xConexion01, "");
                                    if (mysql_num_rows($xDat200) > 0) {
                                        $xDDO = mysql_fetch_array($xDat200);
                                        $xDD['doifefac'] = $xDDO['docfefac'];
                                    }

                                    break;
                            }
                            /* SIAI0200 DO's de impo es el campo DOIFEFAC
                              para los DO de expo tabla siae0199
                              el campo es dexfentr
                              otros
                              sys0121
                              docfefac */

                            //Dias Transcurridos entre: Fecha de Entrega a Facturaci�n y Generaci�n Factura
                            $xDD['fecefygf'] = "";
                            if ($xDD['docffecx'] != '0000-00-00' && $xDD['docffecx'] != "" && $xDD['doifenca'] != '0000-00-00' && $xDD['doifenca'] != "") {
                                $vAuxFec1 = explode("-", $xDD['docffecx']);
                                $vAuxFec2 = explode("-", $xDD['doifenca']);
                                $dateHoy = mktime(0, 0, 0, $vAuxFec1[1], $vAuxFec1[2], $vAuxFec1[0]);
                                $dateVen = mktime(0, 0, 0, $vAuxFec2[1], $vAuxFec2[2], $vAuxFec2[0]);
                                $xDD['fecefygf'] = round(($dateHoy - $dateVen) / (60 * 60 * 24));
                            }
                            
                            //Dias Transcurridos entre: Generaci�n Factura y Entrega Factura al Cliente
                            $xDD['fecgfyfc'] = "";
                            if ($xDD['docffecx'] != '0000-00-00' && $xDD['docffecx'] != "" && $xDD['doifefac'] != '0000-00-00' && $xDD['doifefac'] != "") {
                                $vAuxFec1 = explode("-", $xDD['docffecx']);
                                $vAuxFec2 = explode("-", $xDD['doifefac']);
                                $dateHoy = mktime(0, 0, 0, $vAuxFec1[1], $vAuxFec1[2], $vAuxFec1[0]);
                                $dateVen = mktime(0, 0, 0, $vAuxFec2[1], $vAuxFec2[2], $vAuxFec2[0]);
                                $xDD['fecgfyfc'] = round(($dateVen - $dateHoy) / (60 * 60 * 24));
                            }
                            ## Fin Busqueda de fecha Entrega de Factura al cliente, y dias transcurrido entre 1. Fecha entrega de facturacion y generacion de factura. 2. Fecha generacion de factura y Factura entregada al cliente ##


                            switch ($cTipo) {
                                case 1:  // PINTA POR PANTALLA// 

                                    $zColorPro = "#000000";

                                    $nPagTer = $nPagosTer + $nValFor;
                                    $nSaldo = $nAnticipo + $nPagTer;
                                    ?>
                                    <tr bgcolor = "white" height="20"   style="padding-left:4px;padding-right:4px">
                                        <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>">
                                            <a href ="javascript:f_Imprimir('<?php echo $xDD['comidxxx'] ?>', '<?php echo $xDD['comcodxx'] ?>', '<?php echo $xDD['sucidxxx'] ?>', '<?php echo $xDD['doctipxx'] ?>', '<?php echo $xDD['docidxxx'] ?>', '<?php echo $xDD['docsufxx'] ?>', '<?php echo $xDD['pucidxxx'] ?>', '<?php echo $xDD['ccoidxxx'] ?>', '<?php echo $xDD['cliidxxx'] ?>', '<?php echo $xDD['regfcrex'] ?>')">
            <?php echo $xDD['docidxxx'] . "-" . $xDD['docsufxx'] ?>
                                            </a>
                                        </td>
                                        <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $xDD['sucidxxx'] ?></td>
                                        <!--<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo ($xDD['docpedxx'] != "") ? $xDD['docpedxx'] : "&nbsp;"; ?></td>-->
                                        <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($xDD['regfcrex'] != '0000-00-00' && $xDD['regfcrex'] != "") ? $xDD['regfcrex'] : "&nbsp;"; ?></td>
                                        <!-- <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $xDD['doctipxx'] ?></td>-->
                                        <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($xDD['doimylev'] != '0000-00-00' && $xDD['doimylev'] != "") ? $xDD['doimylev'] : "&nbsp;"; ?></td>
                                        <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $xDD['cliidxxx'] ?></td>
                                        <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $xDD['clinomxx'] ?></td>
                                        <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($xDD['regestxx'] <> "") ? $xDD['regestxx'] : "&nbsp;"; ?></td>
                                        <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($xDD['doifenca'] != '0000-00-00' && $xDD['doifenca'] != "") ? $xDD['doifenca'] : "&nbsp;"; ?></td>
                                        <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($xDD['docffecx'] != '0000-00-00' && $xDD['docffecx'] != "") ? $xDD['docffecx'] : "&nbsp;"; ?></td>

                                        <td class="letra7" align="center"   style = "color:<?php echo $zColorPro ?>"><?php echo ($xDD['fecefygf'] != "" || $xDD['fecefygf'] == 0) ? $xDD['fecefygf'] : "&nbsp;"; ?></td>
                                        <td class="letra7" align="center"   style = "color:<?php echo $zColorPro ?>"><?php echo ($xDD['doifefac'] != '0000-00-00' && $xDD['doifefac'] != "") ? $xDD['doifefac'] : "&nbsp;"; ?></td>
                                        <td class="letra7" align="center"   style = "color:<?php echo $zColorPro ?>"><?php echo ($xDD['fecgfyfc'] != "" || $xDD['fecgfyfc'] == 0) ? $xDD['fecgfyfc'] : "&nbsp;"; ?></td>

                                        <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo ($xDD['dirnomxx'] <> "") ? $xDD['dirnomxx'] : "&nbsp;"; ?></td>
                                        <td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo ($nAnticipo <> "") ? number_format($nAnticipo, 0, ',', '.') : "&nbsp;" ?></td>
                                        <td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo number_format($nPagTer, 0, ',', '.') ?></td>
                                        <td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo number_format($nSaldo, 0, ',', '.') ?></td>
                                    </tr>
                                    <?php
                                    break;
                                case 2:
                                    $zColorPro = "#000000";

                                    $nPagTer = $nPagosTer + $nValFor;
                                    $nSaldo = $nAnticipo + $nPagTer;

                                    $nValor01 = ($xDD['docpedxx'] != "") ? $xDD['docpedxx'] : "";
                                    $nValor02 = ($xDD['regestxx'] <> "") ? $xDD['regestxx'] : "";
                                    $nValor03 = ($xDD['dirnomxx'] <> "") ? $xDD['dirnomxx'] : "";
                                    $nValor04 = ($nAnticipo <> "") ? number_format($nAnticipo, 0, ',', '') : "";
                                    $nValor05 = ($nPagTer <> "") ? number_format($nPagTer, 0, ',', '') : "";
                                    $nValor06 = ($nSaldo <> "") ? number_format($nSaldo, 0, ',', '') : "";
                                    $nValor07 = ($xDD['docffecx'] != '0000-00-00' && $xDD['docffecx'] != "") ? $xDD['docffecx'] : "";
                                    $nValor08 = ($xDD['regfcrex'] != '0000-00-00' && $xDD['regfcrex'] != "") ? $xDD['regfcrex'] : "";
                                    $nValor09 = ($xDD['doimylev'] != '0000-00-00' && $xDD['doimylev'] != "") ? $xDD['doimylev'] : "";
                                    $nValor10 = ($xDD['doifenca'] != '0000-00-00' && $xDD['doifenca'] != "") ? $xDD['doifenca'] : "";
                                    $nValor11 = ($xDD['fecefygf'] != "" || $xDD['fecefygf'] == 0 ) ? $xDD['fecefygf'] : "";
                                    $nValor12 = ($xDD['doifefac'] != '0000-00-00' && $xDD['doifefac'] != "") ? $xDD['doifefac'] : "";
                                    $nValor13 = ($xDD['fecgfyfc'] != "" || $xDD['fecgfyfc'] == 0) ? $xDD['fecgfyfc'] : "";

                                    $data .= '<tr bgcolor = "white" height="20"   style="padding-left:4px;padding-right:4px">';
                                    $data .= '<td class="letra7" align="left"   style = "color:' . $zColorPro . '">' . $xDD['docidxxx'] . "-" . $xDD['docsufxx'] . '</td>';
                                    $data .= '<td class="letra7" align="center" style = "color:' . $zColorPro . '">' . $xDD['sucidxxx'] . '</td>';
                                    //$data .= '<td class="letra7" align="left"   style = "color:' . $zColorPro . '">' . $nValor01 . '</td>';
                                    $data .= '<td class="letra7" align="center" style = "color:' . $zColorPro . '">' . $nValor08 . '</td>';
                                   // $data .= '<td class="letra7" align="center" style = "color:' . $zColorPro . '">' . $xDD['doctipxx'] . '</td>';
                                    $data .= '<td class="letra7" align="left"   style = "color:' . $zColorPro . '">' . $nValor09 . '</td>';
                                    $data .= '<td class="letra7" align="left"   style = "color:' . $zColorPro . '">' . $xDD['cliidxxx'] . '</td>';
                                    $data .= '<td class="letra7" align="left"   style = "color:' . $zColorPro . '">' . $xDD['clinomxx'] . '</td>';
                                    $data .= '<td class="letra7" align="center" style = "color:' . $zColorPro . '">' . $nValor02 . '</td>';
																		$data .= '<td class="letra7" align="left"   style = "color:' . $zColorPro . '">' . $nValor10 . '</td>';
                                    $data .= '<td class="letra7" align="center" style = "color:' . $zColorPro . '">' . $nValor07 . '</td>';

                                    $data .= '<td class="letra7" align="left"   style = "color:' . $zColorPro . '">' . $nValor11 . '</td>';
                                    $data .= '<td class="letra7" align="left"   style = "color:' . $zColorPro . '">' . $nValor12 . '</td>';
                                    $data .= '<td class="letra7" align="left"   style = "color:' . $zColorPro . '">' . $nValor13 . '</td>';

                                    $data .= '<td class="letra7" align="left"   style = "color:' . $zColorPro . '">' . $nValor03 . '</td>';
                                    $data .= '<td class="letra7" align="right"  style = "color:' . $zColorPro . '">' . $nValor04 . '</td>';
                                    $data .= '<td class="letra7" align="right"  style = "color:' . $zColorPro . '">' . $nValor05 . '</td>';
                                    $data .= '<td class="letra7" align="right"  style = "color:' . $zColorPro . '">' . $nValor06 . '</td>';
                                    $data .= '</tr>';
                                    break;
                                case 3 :
                                    $nPag = 1;
                                    $zColorPro = "#000000";

                                    $nPagTer = $nPagosTer;
                                    $nSaldo = $nAnticipo + $nPagTer;

                                    $nValor01 = ($xDD['docpedxx'] != "") ? $xDD['docpedxx'] : "";
                                    $nValor02 = ($xDD['regestxx'] <> "") ? $xDD['regestxx'] : "";
                                    $nValor03 = ($xDD['dirnomxx'] <> "") ? $xDD['dirnomxx'] : "";
                                    $nValor04 = ($nAnticipo <> "") ? number_format($nAnticipo, 0, ',', '') : "";
                                    $nValor05 = ($nPagTer <> "") ? number_format($nPagTer, 0, ',', '') : "";
                                    $nValor06 = ($nSaldo <> "") ? number_format($nSaldo, 0, ',', '') : "";
                                    $nValor07 = ($xDD['docffecx'] != '0000-00-00' && $xDD['docffecx'] != "") ? $xDD['docffecx'] : "";
                                    $nValor08 = ($xDD['regfcrex'] != '0000-00-00' && $xDD['regfcrex'] != "") ? $xDD['regfcrex'] : "";
                                    $nValor09 = ($xDD['doimylev'] != '0000-00-00' && $xDD['doimylev'] != "") ? $xDD['doimylev'] : "";
                                    $nValor10 = ($xDD['doifenca'] != '0000-00-00' && $xDD['doifenca'] != "") ? $xDD['doifenca'] : "";
                                    $nValor11 = ($xDD['fecefygf'] != "" || $xDD['fecefygf'] == 0 ) ? $xDD['fecefygf'] : "";
                                    $nValor12 = ($xDD['doifefac'] != '0000-00-00' && $xDD['doifefac'] != "") ? $xDD['doifefac'] : "";
                                    $nValor13 = ($xDD['fecgfyfc'] != "" || $xDD['fecgfyfc'] ==0 ) ? $xDD['fecgfyfc'] : "";

                                    $pdf->SetX(13);
                                    $pdf->Row(array($xDD['docidxxx'] . "-" . $xDD['docsufxx'],
                                        $xDD['sucidxxx'],
                                       // $nValor01,
                                        $nValor08,
                                       // $xDD['doctipxx'],
                                        $nValor09,
                                        $xDD['cliidxxx'],
                                        $xDD['clinomxx'],
                                        $nValor02,
                                        $nValor10,
                                        $nValor07,
                                        $nValor11,
                                        $nValor12,
                                        $nValor13,
                                        $nValor03,
                                        $nValor04,
                                        $nValor05,
                                        $nValor06));
                                    break;
                            }//Fin Switch
                        } ## while ($xDD = mysql_fetch_array($xDatDoi)) { ##

                        switch ($cTipo) {
                            case 1: // PINTA POR PANTALLA// 
                                ?>
                                </table> 
                                </form>
                                </body>
                                </html>
                                <script type="text/javascript">
                                    document.forms['frgrm']['nCanReg'].value = "<?php echo $nCanReg ?>";
                                </script>
                                <?php
                                break;
                            case 2:
                                $data .= '<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">';
                                $data .= '<td class="name" colspan="15" align="left">';
                                $data .= '<center>';
                                $data .= '<font size="3">';
                                $data .= '<b>TOTAL TRAMITES EN ESTA CONSULTA [' . $nCanReg . ']<br>';
                                $data .= '</font>';
                                $data .= '</center>';
                                $data .= '</td>';
                                $data .= '</tr>';
                                $data .= '</table>';

                                if ($data == "") {
                                    $data = "\n(0) REGISTROS!\n";
                                }

                                header("Pragma: public");
                                header("Expires: 0");
                                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                                header("Cache-Control: private", false); // required for certain browsers
                                header("Content-type: application/octet-stream");
                                header("Content-Disposition: attachment; filename=\"" . basename($title) . "\";");

                                print $data;
                                break;
                            case 3 :

                                $nPag = 0;

                                $pdf->Ln(5);
                                $pdf->SetFont('verdana', '', 8);
                                $pdf->SetX(55);
                                $pdf->Cell(260, 6, 'TOTAL TRAMITES EN ESTA CONSULTA [' . $nCanReg . ']', 0, 0, 'C');

                                $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/pdf_" . $_COOKIE['kUsrId'] . "_" . date("YmdHis") . ".pdf";

                                $pdf->Output($cFile);

                                if (file_exists($cFile)) {
                                    chmod($cFile, intval($vSysStr['system_permisos_archivos'], 8));
                                } else {
                                    f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
                                }

                                echo "<html><script>document.location='$cFile';</script></html>";
                                break;
                        }//Fin Switch

                        function fnCadenaAleatoria($pLength = 8) {
                            $cCaracteres = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ";
                            $nCaracteres = strlen($cCaracteres);
                            $cResult = "";
                            for ($x = 0; $x < $pLength; $x++) {
                                $nIndex = mt_rand(0, $nCaracteres - 1);
                                $cResult .= $cCaracteres[$nIndex];
                            }
                            return $cResult;
                        }
                        ?>

