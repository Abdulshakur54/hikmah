<?php
    class File{
        
        private $_file;
        
        
        public  function __construct($file) {  
            $this->_file = $_FILES[$file];
        }
        
        public function isSelected() :bool{  //checks if a file has been selected from the client side
            return (!empty($this->_file['name'])) ? true: false;
        }
        
        public function isUploaded() :bool{  //ensure that the file is uploaded
            return (is_uploaded_file($this->_file['tmp_name'])) ? true: false;
        }
        
        //this returns the file size in KB
        public function size() {  
            return ($this->_file['size']/1024);
        }
        
        public function name():string{   //returns the name of the uploaded file
            return basename($this->_file['name']);
        }
        
        
        //checks if a file has the required extension, $valExt represents the allowed extension, it should be a simple string or an array of allowed files
        public function isValidExtension($valExt) :bool{
            $ext = $this->extension();
            if(is_array($valExt)){
                return (in_array(strtolower($ext),$valExt) || in_array(strtoupper($ext),$valExt)) ?true:false;
            }
            //runs if a $valExt is a string
            return (Utility::equals($valExt, $ext)) ? true:false;
        }
        
        public function move(string $destination){
            move_uploaded_file($this->_file['tmp_name'], $destination);
        }
        
        public function extension(){
            $fileParts = explode('.', $this->name());
            return end($fileParts);
        }
        
        //this method is used to download files of any type
        public static function download($filepath, $savename){
            header('Content-Type: ' . mime_content_type($filepath)); 
            header('Content-Disposition: inline; filename="'.$savename.'"'); 
            readfile($filepath);
        }
     
    }