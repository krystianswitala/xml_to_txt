<?php
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
     * Wysłanie danych na serwer FTP
     * @param string $url
     * @param string $content
     * @return bool
     */
    private function exportToFtp($url, $content) {
        $fp = fopen($url, "w");
        if ($fp === false) {
            return false;
        }

        $writeRes = fwrite($fp, $content);
        $closeRes = fclose($fp);
        return ($writeRes === false || $closeRes === false) ? false : true;
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
        $txtFilename = $destinationFolder . DS . $result->getId().'.txt';

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

        // Konwersja z XML do TXT
        $txtContent = $this->xmlToTxtConversion($xmlElement);

        // Zapis danych...
        if (file_put_contents($txtFilename, $txtContent) == false) { // ... do pliku na lokalnym dysku
            throw new Exception("Unable to save data to file '{$txtFilename}'.");
        }
//         if ($this->exportToFtp('ftp://user:pass@server.com/' . $txtFilename, $txtContent) == false) { // ... na serwer FTP
//             throw new Exception("Unable to send data to the FTP server.");
//         }
    }

}
?>
