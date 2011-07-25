<?php
namespace Zlyman;

class PublicController extends \Zend\Controller\Action
{
    public function filesAction()
    {
        $filePathParams = $this->getRequest()->getUserParams();
        $path = array();
        foreach($filePathParams as $key=>$value) {
            if($key != $this->getRequest()->getModuleKey() && $key != $this->getRequest()->getControllerKey() && $key != $this->getRequest()->getActionKey()) {
                $path[] = $key;
                $path[] = $value;
            }
        }
        
        $filename = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $path);

        if(is_file($filename)) {
            $fsize = filesize($filename); 
            $path_parts = pathinfo($filename); 
            $ext = strtolower($path_parts["extension"]); 

            switch ($ext) { 
              case "pdf": $ctype="application/pdf"; break; 

              case "zip": $ctype="application/zip"; break; 
              case "gif": $ctype="image/gif"; break; 
              case "png": $ctype="image/png"; break; 
              case "jpeg": 
              case "jpg": $ctype="image/jpg"; break; 
              default: $ctype="application/force-download"; 
            } 

            header("Pragma: public"); 
            header("Expires: 0"); 
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
            header("Cache-Control: private",false); 
            header("Content-Type: $ctype"); 
            
            readfile($filename);
        } else {
            header("HTTP/1.0 404 Not Found");
        }
    }

}

