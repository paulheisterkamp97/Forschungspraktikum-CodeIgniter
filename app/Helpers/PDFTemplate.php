<?php
namespace App\Helpers;

use TCPDF;

class PDFTemplate
{
    public function __construct()
    {
    }

    public function createPdf($rechnungs_posten)
    {
        $rechnungs_datum = date("d.m.Y");
        $lieferdatum = date("d.m.Y");
        $pdfAuthor = "Paul Heisterkamp";
        $rechnungs_nummer = 1;
        $rechnungs_header = '
<img src="logo.png">
Musterfirma
Max Mustermann
www.musterfirma.de';

        $rechnungs_empfaenger = 'Max Musterman
Musterstraße 17
12345 Musterstadt';

        $rechnungs_footer = "Wir bitten um eine Begleichung der Rechnung innerhalb von 14 Tagen nach Erhalt. Bitte Überweisen Sie den vollständigen Betrag an:
 
<b>Empfänger:</b> Musterfirma
<b>IBAN</b>: DE85 745165 45214 12364
<b>BIC</b>: C46X453AD";

//Auflistung eurer verschiedenen Posten im Format [Produktbezeichnung, Menge, Einzelpreis]

//Höhe eurer Umsatzsteuer. 0.19 für 19% Umsatzsteuer
        $umsatzsteuer = 0.19;

        $pdfName = "Rechnung.pdf";


//////////////////////////// Inhalt des PDFs als HTML-Code \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\


// Erstellung des HTML-Codes. Dieser HTML-Code definiert das Aussehen eures PDFs.
// tcpdf unterstützt recht viele HTML-Befehle. Die Nutzung von CSS ist allerdings
// stark eingeschränkt.

        $html = '
<table cellpadding="5" cellspacing="0" style="width: 100%; ">
 <tr>
 <td>' . nl2br(trim($rechnungs_header)) . '</td>
    <td style="text-align: right">
Rechnungsnummer ' . $rechnungs_nummer . '<br>
Rechnungsdatum: ' . $rechnungs_datum . '<br>
Lieferdatum: ' . $lieferdatum . '<br>
 </td>
 </tr>
 
 <tr>
 <td style="font-size:1.3em; font-weight: bold;">
<br><br>
Rechnung
<br>
 </td>
 </tr>
 
 
 <tr>
 <td colspan="2">' . nl2br(trim($rechnungs_empfaenger)) . '</td>
 </tr>
</table>
<br><br><br>
 
<table cellpadding="5" cellspacing="0" style="width: 100%;" border="0">
 <tr style="background-color: #cccccc; padding:5px;">
 <td style="padding:5px;"><b>Bezeichnung</b></td>
 <td style="text-align: center;"><b>Menge</b></td>
 <td style="text-align: center;"><b>Einzelpreis</b></td>
 <td style="text-align: center;"><b>Preis</b></td>
 </tr>';


        $gesamtpreis = 0;

        foreach ($rechnungs_posten as $posten) {
            $menge = $posten->count;
            $einzelpreis = $posten->price;
            $preis = $menge * $einzelpreis;
            $gesamtpreis += $preis;
            $html .= '<tr>
                <td>' . $posten->name . '</td>
 <td style="text-align: center;">' . $posten->count . '</td> 
 <td style="text-align: center;">' . number_format($posten->price, 2, ',', '') . ' Euro</td>	
                <td style="text-align: center;">' . number_format($preis, 2, ',', '') . ' Euro</td>
              </tr>';
        }
        $html .= "</table>";


        $html .= '
<hr>
<table cellpadding="5" cellspacing="0" style="width: 100%;" border="0">';
        if ($umsatzsteuer > 0) {
            $netto = $gesamtpreis / (1 + $umsatzsteuer);
            $umsatzsteuer_betrag = $gesamtpreis - $netto;

            $html .= '
 <tr>
 <td colspan="3">Zwischensumme (Netto)</td>
 <td style="text-align: center;">' . number_format($netto, 2, ',', '') . ' Euro</td>
 </tr>
 <tr>
 <td colspan="3">Umsatzsteuer (' . intval($umsatzsteuer * 100) . '%)</td>
 <td style="text-align: center;">' . number_format($umsatzsteuer_betrag, 2, ',', '') . ' Euro</td>
 </tr>';
        }

        $html .= '
            <tr>
                <td colspan="3"><b>Gesamtsumme: </b></td>
                <td style="text-align: center;"><b>' . number_format($gesamtpreis, 2, ',', '') . ' Euro</b></td>
            </tr> 
        </table>
<br><br><br>';

        if ($umsatzsteuer == 0) {
            $html .= 'Nach § 19 Abs. 1 UStG wird keine Umsatzsteuer berechnet.<br><br>';
        }

        $html .= nl2br($rechnungs_footer);


//////////////////////////// Erzeugung eures PDF Dokuments \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\



// Erstellung des PDF Dokuments
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Dokumenteninformationen
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($pdfAuthor);
        $pdf->SetTitle('Rechnung ' . $rechnungs_nummer);
        $pdf->SetSubject('Rechnung ' . $rechnungs_nummer);


// Header und Footer Informationen
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Auswahl des Font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Auswahl der MArgins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Automatisches Autobreak der Seiten
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Image Scale
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Schriftart
        $pdf->SetFont('dejavusans', '', 10);

// Neue Seite
        $pdf->AddPage();

// Fügt den HTML Code in das PDF Dokument ein
        $pdf->writeHTML($html, true, false, true, false, '');

//Ausgabe der PDF

//Variante 1: PDF direkt an den Benutzer senden:
        return $pdf;

//Variante 2: PDF im Verzeichnis abspeichern:
//$pdf->Output(dirname(__FILE__).'/'.$pdfName, 'F');
//echo 'PDF herunterladen: <a href="'.$pdfName.'">'.$pdfName.'</a>';

    }


}


?>