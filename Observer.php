<?php
class VladimirPopov_WebFormsProccessResult_Model_Observer{
    
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
        $destinationFolder = Mage::getBaseDir('media') . DS . 'webforms' . DS . 'xml';
        $filename = $destinationFolder . DS . $result->getId().'.xml';
        
        // create folder
        if (!(@is_dir($destinationFolder) || @mkdir($destinationFolder, 0777, true))) {
            throw new Exception("Unable to create directory '{$destinationFolder}'.");
        }
        
        // export to file
        // $xmlObject->getNode()->asNiceXml($filename);
        $xmlArr = $xmlObject->getNode()->asArray();
        $xmlArrDump = var_export($xmlArr);
        @file_put_contents($filename, $xmlArrDump);
    }
    
}
?>
