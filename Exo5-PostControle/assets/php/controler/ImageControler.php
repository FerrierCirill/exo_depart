<?php

class ImageControler {

    static private $pdfExt = ['pdf'];
    static private $imgExt = ['jpg', 'jpeg', 'gif', 'png'];
    static private $zipExt = ['zip', '7zip', 'tar.gz'];

    static private $Path      = 'assets/upload/';
    static private $imgPath   = 'assets/upload/src/';
    static private $pdfPath   = 'assets/upload/pdf/';
    static private $thumbPath = 'assets/upload/thumb/';

    static private $thumbName = 'min';

    static private $crops = [
            ['width' => 150, 'height' => 150]
        ];
    
    // Pas utilisable pour le moment pour le moment
    // $this->resize = [
    //     1080,
    //     600
    // ];

    static private $msgError_size      = 'Le fichier reçu dépasse la limite de : ';
    static private $msgError_noFile    = 'Erreur lors de l\'uplaod, aucun fichier reçu.';
    static private $msgError_upload    = 'Erreur lors de l\'uplaod, veuillez réessayer.';
    static private $msgError_noUpload  = 'Fichier non envoyer';
    static private $msgError_extention = 'Fichier jpg, png, gif ou pdf uniquement';



    private function __construct()
    {
        $pdfExt    = $this->pdfExt;     
        $imgExt    = $this->imgExt;    
        $zipExt    = $this->zipExt;     
        $Path      = $this->Path;       
        $imgPath   = $this->imgPath;    
        $pdfPath   = $this->pdfPath;    
        $thumbPath = $this->thumbPath;  
        $thumbName = $this->thumbName;  
        $crops     = $this->crops;  

        $msgError_size      = $this->msgError_size;
        $msgError_noFile    = $this->msgError_noFile;
        $msgError_upload    = $this->msgError_upload;
        $msgError_noUpload  = $this->msgError_noUpload;
        $msgError_extention = $this->msgError_extention;

        self::pathCreator();
    }


    private static function pathCreator() {
        if(!is_dir(self::$Path))      mkdir(self::$Path);
        if(!is_dir(self::$imgPath))   mkdir(self::$imgPath);
        if(!is_dir(self::$pdfPath))   mkdir(self::$pdfPath);
        if(!is_dir(self::$thumbPath)) mkdir(self::$thumbPath);
    }








    public static function controleUploadFile($__formInputName) {
        try {
            if (! isset($_FILES[$__formInputName])) throw new Exception(self::$msgError_noUpload, 1);
            
            if (!($_FILES[$__formInputName]['error'] == UPLOAD_ERR_OK)) {
                switch ($_FILES[$__formInputName]['error']){
                    case UPLOAD_ERR_INI_SIZE:
                        throw new Exception (self::$msgError_size. ini_get('upload_max_filesize').' .');
                        break;
                    case UPLOAD_ERR_PARTIAL:
                    case UPLOAD_ERR_NO_TMP_DIR:
                    case UPLOAD_ERR_CANT_WRITE:
                    case UPLOAD_ERR_EXTENSION:
                        throw new Exception (self::$msgError_upload);
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        throw new Exception (self::$msgError_noFile);
                        break;
                }
            }
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }














    /**
     * 
     * 
     * 
     * 
     */
    public static function upload($__formInputName, $__nameFinalFile = '', $__croping = false) {
        self::pathCreator();
        try {
            if (($e = self::controleUploadFile($__formInputName)) != true) { throw new Exception($e, 1);   }
        
            $pathInfo              = pathinfo($_FILES[$__formInputName]['name']);
            $pathInfo['extension'] = strtolower($pathInfo['extension']);
            
            if      (in_array($pathInfo['extension'], self::$pdfExt)){ $destDir =  self::$pdfPath; }
            else if (in_array($pathInfo['extension'], self::$imgExt)){ $destDir =  self::$imgPath; }
            else                                                     { throw new Exception($msgError_extention, 1);   }

            if ($__nameFinalFile == '') {
                $uniqueFileName = date('YmdHis') . '_' . microtime() . '.' .$pathInfo['extension'];
            }
            else {
                $uniqueFileName = $__nameFinalFile . '.' .$pathInfo['extension'];
            }

            if(!(move_uploaded_file($_FILES[$__formInputName]['tmp_name'], $destDir.$uniqueFileName))) throw new Exception('Erreur lors du stockage du fichier');
            
            if ($__croping) {
                self::crop($destDir.$uniqueFileName);
            }

            return true;

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public static function crop($filepath) {
        self::pathCreator();
        if(!file_exists($filepath))
            return false;

        //on récupère les données du document
            $pathInfo = pathinfo($filepath);
            $pathInfo['extension'] = strtolower($pathInfo['extension']);
            if(!in_array($pathInfo['extension'], self::$imgExt))
                return false;


        //récupération de la source de l'image d'origine
            switch ($pathInfo['extension']) {
                case 'gif':
                    $source_gd_image = imagecreatefromgif($filepath);
                    break;
                case 'jpeg':
                case 'jpg':
                    $source_gd_image = imagecreatefromjpeg($filepath);
                    break;
                case 'png':
                    $source_gd_image = imagecreatefrompng($filepath);
                    break;
            }

            $imgsize = getimagesize($filepath);

            if($source_gd_image === false)  return false;        
            if($imgsize         === false)  return false;

            
            $dossier = self::$imgPath;

        // L'image est tel en paysage (PAYSAGE ::  width > height) ou non (PORTRAIT ::  width < height)
            $paysage = ($imgsize[0] > $imgsize[1]);

        // Pour chaque element de self::$crops on redimentionne et on crop l'image
            foreach (self::$crops as $cropsParam) {

                //on créé une image "vide" (une image noire)
                    $thumbnail = imagecreatetruecolor($cropsParam['width'], $cropsParam['height']);

                //On calcule le produit en croix selon si l'image est en mode portrait ou paysage     
                // cependant si la valeur ainsi calculer est < a la valeur de notre cadre 
                // on effectue le calcul opposer 
                      
                    if ($paysage) {
                        $width_imgSrc  = floor(($imgsize[0] * $cropsParam['height']) / $imgsize[1]);
                        $height_imgSrc = $cropsParam['height'];

                        if($width_imgSrc < $cropsParam['width']) { // si trop petit
                            $width_imgSrc  = $cropsParam['width'];
                            $height_imgSrc = floor(($imgsize[1] * $cropsParam['width']) / $imgsize[0]);
                        }    
                    }
                    else {
                        $width_imgSrc  = $cropsParam['width'];
                        $height_imgSrc = floor(($imgsize[1] * $cropsParam['width']) / $imgsize[0]);

                        if($height_imgSrc < $cropsParam['height']) { // si trop petit
                            $width_imgSrc  = floor(($imgsize[0] * $cropsParam['height']) / $imgsize[1]);
                            $height_imgSrc = $cropsParam['height'];
                        }
                    }

                    $x_imgSrc = floor(($width_imgSrc  - $cropsParam['width'] ) / 2);
                    $y_imgSrc = floor(($height_imgSrc - $cropsParam['height']) / 2);
                        
                    
                //on créé une copie de notre image source
                    imagecopyresampled(
                        $thumbnail, 
                        $source_gd_image,
                        0, 0, $x_imgSrc, $y_imgSrc, 
                        $width_imgSrc,
                        $height_imgSrc,
                        $imgsize[0], 
                        $imgsize[1]);

                //et on en fait un fichier jpeg avec une qualité de 90%
                    imagejpeg($thumbnail, self::$thumbPath. $pathInfo['filename'].'_'. self::$thumbName .'_'. $cropsParam['width'] . 'x' . $cropsParam['height'] .'.jpg', 90);
                    imagedestroy($thumbnail);
            }
        imagedestroy($source_gd_image);
    }





    /**
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     */
    function resize($filepath, $thumbnailWitdh=null, $thumbnailHeight = null) {
        self::pathCreator();
        if(!file_exists($filepath))
            return false;


        //on récupère les données du document
        $pathInfo = pathinfo($filepath);
        $pathInfo['extension'] = strtolower($pathInfo['extension']);
        if(!in_array($pathInfo['extension'], self::$imgExt))
            return false;


        //récupération de la source de l'image d'origine
        switch ($pathInfo['extension']) {
            case 'GIF':
                $source_gd_image = imagecreatefromgif($filepath);
                break;
            case 'jpeg':
            case 'jpg':
                $source_gd_image = imagecreatefromjpeg($filepath);
                break;
            case 'PNG':
                $source_gd_image = imagecreatefrompng($filepath);
                break;
        }

        $imgsize = getimagesize($filepath);

        if($source_gd_image === false)  return false;        
        if($imgsize         === false)  return false;

        if(is_null($thumbnailWitdh) && is_null($thumbnailHeight))  return false;


        if(is_null($thumbnailWitdh)){
            $thumbnailWitdh = floor($thumbnailHeight*$imgsize[0]/$imgsize[1]);
        }else{
            $thumbnailHeight = floor($thumbnailWitdh*$imgsize[1]/$imgsize[0]);
        }

        //on créé une image "vide" (une image noire)
        $thumbnail = imagecreatetruecolor($thumbnailWitdh, $thumbnailHeight);

        //on créé une copie de notre image source
        imagecopyresampled($thumbnail, $source_gd_image, 0, 0, 0, 0, $thumbnailWitdh, $thumbnailHeight, $imgsize[0], $imgsize[1]);
        //et on en fait un fichier jpeg avec une qualité de 90%
        $dossier = self::$imgPath;
        imagejpeg($thumbnail, $dossier.$pathInfo['filename'].'_thumb_'.$thumbnailWitdh.'x'.$thumbnailHeight.'.jpg', 90);
        imagedestroy($source_gd_image);
        imagedestroy($thumbnail);
    }

}
