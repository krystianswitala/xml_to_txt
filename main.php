<?php

const XML_FILE_NAME = '36779.xml';
const TXT_FILE_NAME = '36779.txt';

/**
 * Załadowanie XML-a z pliku
 * @param string $fileName nazwa pliku
 * @return SimpleXMLElement
 */
function loadXMLFile(string $fileName): SimpleXMLElement {
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
function xmlToTxtConversion(SimpleXMLElement $xmlElem): string {
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
 * Funkcja główna
 */
function main() {
    $xmlElement = loadXMLFile(XML_FILE_NAME);
//     var_dump($xmlElement);

    if ($xmlElement !== null) {
        $txtStr = xmlToTxtConversion($xmlElement);
        var_dump($txtStr);

        // Zapis danych
        file_put_contents(TXT_FILE_NAME, $txtStr);
    }
}

// No to startujemy
main();