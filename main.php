<?php

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
     * Wysłanie danych na serwer FTP
     * @param string $ftpServer
     * @param string $ftpUser
     * @param string $ftpPass
     * @param string $fileName
     * @param string $content
     * @return bool
     */
//     private function exportToFtp($ftpServer, $ftpUser, $ftpPass, $fileName, $content) {
//         $conn = ftp_connect($ftpServer);
//         $loginRes = ftp_login($conn, $ftpUser, $ftpPass);
//         if (!$conn || !$loginRes) {
//             return false;
//         }

//         $upload = ftp_put($conn, 'krystianswitala.cba.pl/'.$fileName, $fileName, FTP_BINARY);
//         if (!$upload) {
//             return false;
//         }

//         ftp_close($conn);

//         return true;
//     }

    /**
     * Funkcja główna
     */
    static function main() {
        $conv = new XmlToTxtConverter();
        $xmlElement = $conv->loadXMLFile(XmlToTxtConverter::XML_FILE_NAME);
    //     var_dump($xmlElement);

        if ($xmlElement !== null) {
            $txtStr = $conv->xmlToTxtConversion($xmlElement);
            var_dump($txtStr);

            // Zapis danych...
            @file_put_contents(XmlToTxtConverter::TXT_FILE_NAME, $txtStr); // ... do pliku na lokalnym dysku
//             $conv->exportToFtp(XmlToTxtConverter::FTP_SERVER, XmlToTxtConverter::FTP_USER, XmlToTxtConverter::FTP_PASS, XmlToTxtConverter::TXT_FILE_NAME, $txtStr); // ... na serwer FTP
            $conv->exportToFtp('ftp://krystianswitala:***@cba.pl/krystianswitala.cba.pl/'.XmlToTxtConverter::TXT_FILE_NAME, $txtStr);

            // Kasowanie pliku wynikowego
//             @unlink(XmlToTxtConverter::TXT_FILE_NAME);

            $dump = var_export($xmlElement, true);
            file_put_contents('dump.txt', $dump);
        }
    }
}

// No to startujemy
XmlToTxtConverter::main();