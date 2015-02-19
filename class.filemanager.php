<?php
class FileManager {

    protected static $_instance;            //object

    public static function getInstance() { 
        if (self::$_instance === null) {            //is object not created
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    private  function __construct() {
    }

    private function __clone() {            //cancellation cloning object
    }
    
    private function __wakeup() {           //cancellation cloning object
    }

    public static function getFileList() {

        $path = self::setRoute();
        $filesAndFiles = [];

        if ($handle = opendir($path)) {

            $results = scandir($path);

            foreach ($results as $file) {
                if ($file === "." or $file === "..") continue;

                if (is_dir($path . "/" . $file)) {
                    $filesAndFiles["directories"][$file]['name'] = $file;
                    $filesAndFiles["directories"][$file]['size'] = round(self::getFilesSize($path . "/" . $file) / 1024, 3);
                } else {

                    $info = pathinfo($file);
                    $filename = basename($file,'.' . $info['extension']);

                    $filesAndFiles["files"][$filename]['full'] = $info['basename'];
                    $filesAndFiles["files"][$filename]['name'] = $info['filename'];
                    $filesAndFiles["files"][$filename]['ext'] = $info['extension'];
                    $filesAndFiles["files"][$filename]['size'] = round(filesize($path . "/" . $file) / 1024, 3);
                }
            }

            closedir($handle);

            if(isset($_GET["sortby"])) {
                
                switch ($_GET["sortby"]) {
                    case 'size':
                        if(isset($_SESSION["sorted"]) && $_SESSION["sorted"] == 'size'){
                            usort($filesAndFiles["files"], array("FileManager" , "compareDesc"));
                            usort($filesAndFiles["directories"], array("FileManager" , "compareDesc"));
                            $_SESSION["sorted"] = "";
                        } else {
                            usort($filesAndFiles["files"], array("FileManager" , "compareAsc"));
                            usort($filesAndFiles["directories"], array("FileManager" , "compareAsc"));
                            $_SESSION["sorted"] = "size";
                        }
                        break;

                    case 'name':
                        if(isset($_SESSION["sorted"]) && $_SESSION["sorted"] == 'name'){
                            usort($filesAndFiles["files"], array("FileManager" , "nameCompareDesc"));
                            usort($filesAndFiles["directories"], array("FileManager" , "nameCompareDesc"));
                            $_SESSION["sorted"] = "";
                        } else {
                            usort($filesAndFiles["files"], array("FileManager" , "nameCompare"));
                            usort($filesAndFiles["directories"], array("FileManager" , "nameCompare"));
                            $_SESSION["sorted"] = "name";
                        }
                        break;

                    case 'ext':
                        if(isset($_SESSION["sorted"]) && $_SESSION["sorted"] == 'ext'){
                            usort($filesAndFiles["files"], array("FileManager" , "extCompare"));
                        
                            $_SESSION["sorted"] = "";
                        } else {
                            usort($filesAndFiles["files"], array("FileManager" , "extCompareDesc"));
                            
                            $_SESSION["sorted"] = "ext";
                        }
                        break;
                    
                    default:
                        break;
                }
                
            }



            return $filesAndFiles;
        }
    }

    protected static function setRoute() {

        session_start();

        if(isset($_GET["back"])) {

            array_pop($_SESSION["breadcrumbs"]);
        }

        if(isset($_GET["gotodir"])) {

            $_SESSION["breadcrumbs"][] = $_GET["gotodir"];
        }

        if(isset($_SESSION["breadcrumbs"])) {
            $route = "/" . implode("/", $_SESSION["breadcrumbs"]);
        } else {
            $route = "";
        }

        return $_SERVER["DOCUMENT_ROOT"] . $route;
    }

    protected static function getFilesSize($path)
    {
        $fileSize = 0;
        $dir = scandir($path);
        
        foreach($dir as $file)
        {
            if (($file != '.') && ($file != '..'))
                if(is_dir($path . '/' . $file))
                    $fileSize += self::getFilesSize($path . '/' . $file);
                else
                    $fileSize += filesize($path . '/' . $file);
        }
        
        return $fileSize;
    }

    protected static function compareAsc($v1, $v2) {
        
        if ($v1["size"] == $v2["size"]) return 0;
        return ($v1["size"] < $v2["size"])? -1: 1;
    }

    protected static function compareDesc($v1, $v2) {
        
        if ($v1["size"] == $v2["size"]) return 0;
        return ($v1["size"] > $v2["size"])? -1: 1;
    }

    protected static function nameCompare($a, $b)  
    {  
        return strcasecmp($a["name"], $b["name"]);  
    }

    protected static function nameCompareDesc($a, $b)  
    {  
        return strcasecmp($b["name"], $a["name"]);  
    }

    protected static function extCompare($a, $b)  
    {  
        return strcasecmp($a["ext"], $b["ext"]);  
    }

    protected static function extCompareDesc($a, $b)  
    {  
        return strcasecmp($b["ext"], $a["ext"]);  
    }  
}