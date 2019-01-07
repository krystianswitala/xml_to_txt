<?php

const XML_FILE_NAME = '36779.xml';
const TXT_FILE_NAME = '36779.txt';

/**
 * Za³adowanie XML-a z pliku
 * @param string $fileName nazwa pliku
 * @return SimpleXMLElement
 */
function loadXMLFile(string $fileName): SimpleXMLElement {
    $xmlstr = "";
    
    return new SimpleXMLElement($xmlstr);
}

/**
 * Funkcja g³ówna
 */
function main() {
    $xmlElement = loadXMLFile(XML_FILE_NAME);
    var_dump($xmlElement);
}

// No to startujemy
main();