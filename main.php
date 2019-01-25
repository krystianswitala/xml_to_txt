<?php

require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class XmlToTxtConverter {
    const XML_FILE_NAME = '36779.xml';
    const TXT_FILE_NAME = '36779.txt';
    const FTP_SERVER = 'cba.pl';
    const FTP_USER = 'krystianswitala';
    const FTP_PASS = '';

    /**
     * Załadowanie XML-a z pliku
     * @param string $fileName nazwa pliku
     * @return SimpleXMLElement
     */
    private function loadXMLFile($fileName) {
        if (file_exists($fileName)) {
            return simplexml_load_file($fileName);
        } else {
            return null;
        }
    }

    /**
     * Konwersja z formatu XML do TXT
     * @param SimpleXMLElement $xmlElem
     * @return string
     */
    private function xmlToTxtConversion(SimpleXMLElement $xmlElem) {
        $txtRes  = '###HIDDEN_TITLE:'.$xmlElem->hidden_title."#\r\n";
        $txtRes .= '###NAME:'.$xmlElem->name."#\r\n";
        $txtRes .= '###NACHNAME:'.$xmlElem->nachname."#\r\n";
        $txtRes .= '###STRASSE:'.$xmlElem->strasse."#\r\n";
        $txtRes .= '###PLZ:'.$xmlElem->plz."#\r\n";
        $txtRes .= '###CITY:'.$xmlElem->city."#\r\n";
        $txtRes .= '###PHONE:'.$xmlElem->phone."#\r\n";
        $txtRes .= '###EMAIL:'.$xmlElem->email."#\r\n";
        $txtRes .= '###BEMERKUNGEN:'.$xmlElem->bemerkungen."#\r\n";
        $txtRes .= '###BEDARF:'.$xmlElem->bedarf."#\r\n";
        $txtRes .= '###HIDDEN_URL:'.$xmlElem->hidden_url."#\r\n";
        return $txtRes;
    }

    /**
     * Konwersja i zapis do pliku XLS
     * @param SimpleXMLElement $xmlElem
     */
    private function exportToXlsFile(SimpleXMLElement $xmlElem, $fileName) {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Web form data");

        $sheet->setCellValue("A1", "HIDDEN_TITLE");
        $sheet->setCellValue("A2", $xmlElem->hidden_title);
        $sheet->setCellValue("B1", "NAME");
        $sheet->setCellValue("B2", $xmlElem->name);
        $sheet->setCellValue("C1", "NACHNAME");
        $sheet->setCellValue("C2", $xmlElem->nachname);
        $sheet->setCellValue("D1", "STRASSE");
        $sheet->setCellValue("D2", $xmlElem->strasse);
        $sheet->setCellValue("E1", "PLZ");
        $sheet->setCellValue("E2", $xmlElem->plz);
        $sheet->setCellValue("F1", "CITY");
        $sheet->setCellValue("F2", $xmlElem->city);
        $sheet->setCellValue("G1", "PHONE");
        $sheet->setCellValue("G2", $xmlElem->phone);
        $sheet->setCellValue("H1", "EMAIL");
        $sheet->setCellValue("H2", $xmlElem->email);
        $sheet->setCellValue("I1", "BEMERKUNGEN");
        $sheet->setCellValue("I2", $xmlElem->bemerkungen);
        $sheet->setCellValue("J1", "BEDARF");
        $sheet->setCellValue("J2", $xmlElem->bedarf);
        $sheet->setCellValue("K1", "HIDDEN_URL");
        $sheet->setCellValue("K2", $xmlElem->hidden_url);

        $sheet->getColumnDimension("A")->setAutoSize(true);
        $sheet->getColumnDimension("B")->setAutoSize(true);
        $sheet->getColumnDimension("C")->setAutoSize(true);
        $sheet->getColumnDimension("D")->setAutoSize(true);
        $sheet->getColumnDimension("E")->setAutoSize(true);
        $sheet->getColumnDimension("F")->setAutoSize(true);
        $sheet->getColumnDimension("G")->setAutoSize(true);
        $sheet->getColumnDimension("H")->setAutoSize(true);
        $sheet->getColumnDimension("I")->setAutoSize(true);
        $sheet->getColumnDimension("J")->setAutoSize(true);
        $sheet->getColumnDimension("K")->setAutoSize(true);

        $writer = new Xls($spreadsheet);
        @$writer->save($fileName);

    }

    /**
     * Wysłanie danych na serwer FTP
     * @param string $url
     * @param string $content
     * @return bool
     */
    private function exportToFtp($url, $content) {
        if (file_exists($url)) {
            unlink($url);
        }

        $fp = fopen($url, "w");
        if ($fp === false) {
            return false;
        }

        $writeRes = fwrite($fp, $content);
        $closeRes = fclose($fp);
        return ($writeRes === false || $closeRes === false) ? false : true;
    }

    /**
     * Funkcja główna
     */
    static function main() {
        $conv = new XmlToTxtConverter();
        $xmlElement = $conv->loadXMLFile(XmlToTxtConverter::XML_FILE_NAME);
//         var_dump($xmlElement);

        if ($xmlElement !== null) {
            $txtStr = $conv->xmlToTxtConversion($xmlElement);
            var_dump($txtStr);

            // Zapis danych...
            @file_put_contents(XmlToTxtConverter::TXT_FILE_NAME, $txtStr); // ... do pliku na lokalnym dysku
//             $conv->exportToFtp('ftp://krystianswitala:***@cba.pl/krystianswitala.cba.pl/'.XmlToTxtConverter::TXT_FILE_NAME, $txtStr); // ... na serwer FTP
            $conv->exportToXlsFile($xmlElement, XmlToTxtConverter::TXT_FILE_NAME.".xls");

            // Kasowanie pliku wynikowego
//             @unlink(XmlToTxtConverter::TXT_FILE_NAME);

            $dump = var_export($xmlElement, true);
            file_put_contents('dump.txt', $dump);
        }
    }
}

// No to startujemy
XmlToTxtConverter::main();
