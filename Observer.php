<?php

require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class VladimirPopov_WebFormsProccessResult_Model_Observer {

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
     * Eksport do XML
     * @param unknown $observer
     * @throws Exception
     */
    public function exportXML($observer){
        if(!Mage::getStoreConfig('webforms/proccessresult/enable')) return;

        $webform = $observer->getWebform();

        /*
        *   Check web-form code
        *   if($webform->getCode() != 'myform') return;
        */

        $result = Mage::getModel('webforms/results')->load($observer->getResult()->getId());
        $xmlObject = new Varien_Simplexml_Config($result->toXml());

        // generate unique filename
        $destinationFolder =  Mage::getBaseDir('media') . DS . 'webforms' . DS . 'xml';
        $xmlFilename = $destinationFolder . DS . $result->getId().'.xml';
        $xlsFilename = $destinationFolder . DS . $result->getId().'.xls';

        // create folder
        if (!(is_dir($destinationFolder) || mkdir($destinationFolder, 0777, true))) {
            throw new Exception("Unable to create directory '{$destinationFolder}'.");
        }

        // export to file
        $xmlObject->getNode()->asNiceXml($xmlFilename);

        // Ponowne ładowanie pliku XML
        $xmlElement = $this->loadXMLFile($xmlFilename);
        unlink($xmlFilename); // Usunięcie niepotrzebnego już pliku XML
        if ($xmlElement == null) {
            throw new Exception("File '{$xmlFilename}' not exist.");
        }

        // Konwersja z XML do XLS
        $this->exportToXlsFile($xmlElement, $xlsFilename);
    }

}
?>
