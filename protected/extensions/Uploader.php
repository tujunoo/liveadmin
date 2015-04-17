<?php
/**
 * upload class
 * @author vincent.mi@toursforfun.com
 * @example
 *
 */

class Uploader {
        /**
         * replace exists file
         * @var integer
         */
        const EXISTS_OVERWRITE  = 0;
        /**
         * rename new file if file exists
         * @var integer
         */
        const EXISTS_RENAME     = 1;
        /**
         * ignore this upload if file exists
         * @var integer
         */
        const EXISTS_IGNORE     = 2;
        /**
         * report a error if file exists
         * @var integer
         */
        const EXISTS_ERROR              = 3;

        private $topUploadDir = '';
        private $notAllowed = array('php','exe');
        private $allowed = array('jpg','gif','jpeg','png');
        private $maxSize = 0 ;//byte
        private $rename = true;

        /**
         * [UID] [ORGNAME] and date format charactor
         * rename pat .
         * @var string
         */
        private $renameFormat = '[uid][extension]';
        private $existFile = self::EXISTS_OVERWRITE;
        private $overwritePost = true ;
        private $clear = true ;
        private $buildPath = true ;
        private $folderMask = 0755 ;
        private $error = array();

        public function __construct($topUploadDir = './'){
                $this->topUploadDir = realpath($topUploadDir);
                if($this->topUploadDir == '' || !is_writable($this->topUploadDir)){
                        throw new Exception(sprintf("upload dir %s not write able.",$topUploadDir));
                }
        }
        /**
         * create a uploader
         * @param string $topUploadDir
         * @return Uploader
         */
        public static function create($topUploadDir){
                return new Uploader($topUploadDir);
        }
        /**
         * set rename format
         * [year],[fullyear],[month],[day],[hour],[minute],[second],[timestamp],[uid],[name],[extension],[random]
         * @param string $renameMethod
         * @return Uploader
         */
        public function renameFormat($format = '[uid][extension]'){
                if($format == 'uniqid'){
                        $format = '[uid][extension]';
                }else if($format == 'keep'){
                        $format = '[name][extension]';
                }else if($format == 'archive'){
                        $format = '[fullyear]/[month][day]/[name][extension]';
                }
                $this->renameFormat = $format ;
                $this->rename(true);
                return $this ;
        }
        /**
         * set exists file proces method
         * @param integer $type
         * @return Uploader
         */
        public function existFile($type = self::EXISTS_OVERWRITE){
                if($type == self::EXISTS_RENAME|| $type == self::EXISTS_ERROR  || $type == self::EXISTS_OVERWRITE || $type == self::EXISTS_IGNORE  ){
                        $this->existFile = $type ;
                }
                return $this;
        }
        /**
         * change max size setting , if size is small the zero will no change the setting
         * @param string $size number or 20M or 500K ...
         * @return Uploader
         */
        public function maxSize($size){
                $size = strtoupper($size);
                if(is_numeric($size) && $size > 0){
                        $this->maxSize = intval($size);
                }else{
                        $value = substr($size,0,-1);
                        $unit = substr($size,-1);
                        if(is_numeric($value) && ($unit == 'K'||$unit == 'M'|| $unit == 'G')){
                                switch($unit){
                                        case 'K' : $unitAmount = 1024 ;break;
                                        case 'M' : $unitAmount = 1048576 ;break;
                                        case 'G' : $unitAmount = 1073741824 ;break;
                                        default : $unitAmount = 1;
                                }
                                $size = intval($value) * $unitAmount ;
                                if($size > 0 ){
                                        $this->maxSize = $size ;
                                }
                        }
                }
                return $this ;
        }
        /**
         * enable rename or not
         * @param boolean $rename
         * @return Uploader
         */
        public function rename($rename = true){
                $this->rename =(boolean)$rename ;
                return $this;
        }
        /**
         * set extensions allowed
         * @param mix $extensions
         * @param boolean $append
         * @return Uploader
         */
        public function allow($extensions , $append = false){
                if(!is_array($extensions)){
                        $extensions = explode(',',strval($extensions));
                }

                if($extensions !== array()){
                        if($append == true){
                                $this->allowed = array_merge($this->allowed , $extensions);
                        }else{
                                $this->allowed = $extensions;
                        }
                }
                return $this;
        }
        /**
         * set extensions not allowed
         * @param mix $extensions
         * @param boolean $append
         * @return Uploader
         */
        public function notAllow($extensions , $append = false){
                if(!is_array($extensions)){
                        $extensions = explode(',',strval($extensions));
                }

                if($append == true){
                    if($extensions !== array()){
                        $this->notAllowed = array_merge($this->notAllowed , $extensions);
                    }
                }else{
                    $this->notAllowed = $extensions;
                }

                return $this;
        }

        /**
         * get file extension
         * @param string $name
         * @author vincent.mi@toursforfun.com (2012-5-9)
         */
        public function getExtension($name)
        {
                $pos = strrpos($name , '.');
                return $pos=== false ? '' : strtolower(substr($name , $pos+1));
        }
        /**
         * check size
         * @param unknown_type $size
         * @author vincent.mi@toursforfun.com (2012-5-10)
         */
        private function checkSize($size){
                if($this->maxSize > 0){
                        if($size > $this->maxSize){
                                return false;
                        }
                }
                return true ;
        }
        /**
         * validate file extension
         * @param string $orgName
         * @param integer $size
         * @author vincent.mi@toursforfun.com (2012-5-9)
         */
        private function checkExtension($extension)
        {
                if(!empty($this->notAllowed)){
                        return !in_array($extension , $this->notAllowed);
                }
                if(!empty($this->allowed)){
                        return in_array($extension , $this->allowed);
                }
                return true ;
        }
        /**
         * add a error message
         * @param string $key
         * @param string $message
         */
        private function addError($key , $message){
            $message = Yii::t('main',$message);
                if(isset($this->error[$key])){
                        $this->error[$key].=','.$message ;
                }else{
                        $this->error[$key]=$message ;
                }
        }
        /**
         * get errir message
         */
        public function getError(){
                return $this->error ;
        }
        /**
         * generate a new file by the specified method
         * @param string $oldName
         * @param string $extension
         * @author vincent.mi@toursforfun.com (2012-5-10)
         */
        private function generateNewFilePath($oldName , $extension = null){
                if($extension == null){
                        $extension = $this->getExtension($oldName);
                }

                if($this->rename !== true){
                        $newName = $oldName ;
                }else{
                        $search = array(
                                        '[year]','[fullyear]','[month]','[day]',
                                        '[hour]','[minute]','[second]','[timestamp]',
                                        '[uid]','[name]','[extension]','[random]'
                                        );
                        $time = time();
                        $repExtension = $extension == ''? '': '.'.$extension;
                        $replace = array(
                                date('y' , $time) , date('Y' , $time) , date('m' , $time), date('d' , $time),
                                date('H' , $time) , date('i' , $time) , date('s' , $time), $time,
                                uniqid(),substr($oldName , 0 , strrpos($oldName, '.')),$repExtension,rand(100,1000)
                        );
                        $newName = str_replace($search, $replace, $this->renameFormat);
                }
                return $newName;
        }

        /**
         * build path structure
         * @param unknown_type $filePath
         * @author vincent.mi@toursforfun.com (2012-5-10)
         */
        public function buildPath($filePath){
                $pos = strrpos($filePath, DIRECTORY_SEPARATOR);
                if($pos === false){
                        return true ;
                }
                $parentFolder = substr($filePath,0,$pos);
                $fullParentFolderPath = $this->topUploadDir.DIRECTORY_SEPARATOR.$parentFolder;
                if(is_dir($fullParentFolderPath) && is_writable($fullParentFolderPath)){
                        return true ;
                }else{
                        if($this->buildPath == true){
                                $pathParts = explode(DIRECTORY_SEPARATOR , $parentFolder);
                                $currentPath = $this->topUploadDir;
                                foreach($pathParts as $p){
                                        $p = trim($p);
                                        if($p == '' || $p=='.' || $p == '..'){continue ;}
                                        $currentPath.= DIRECTORY_SEPARATOR.$p;
                                        if(!is_dir($currentPath)){
                                                $result = @mkdir($currentPath,$this->folderMask);
                                                if($result == false)    return false;
                                        }
                                }
                                return true ;
                        }else{
                                return false ;
                        }
                }
        }

        /**
         * upload a single file
         * @param string $key
         * @param array $upFileInfo
         */
        private function processFile($key , $upFileInfo){

                if($upFileInfo['error'] == UPLOAD_ERR_NO_FILE ){
                        return '';
                }

                if($upFileInfo['error'] != UPLOAD_ERR_OK){

                        switch($upFileInfo['error']){
                                case UPLOAD_ERR_INI_SIZE        : $msg = 'file exceeds the upload_max_filesize directive in php.ini.';break;
                                case UPLOAD_ERR_FORM_SIZE       : $msg = 'file exceeds the MAX_FILE_SIZE directive that was specified in form.';break;
                                case UPLOAD_ERR_PARTIAL         : $msg = 'file was only partially uploaded.';break;
                                case UPLOAD_ERR_NO_TMP_DIR      : $msg = 'missing a temporary folder.';break;
                                case UPLOAD_ERR_CANT_WRITE      : $msg = 'failed to write file to disk.';break;
                                default                                         : $msg = 'unknow upload error.';
                        }
                        $this->addError($key, $msg);
                        return '';

                }else{

                        if(!$this->checkSize($upFileInfo['size'])){
                                $this->addError($key, 'file exceeds the maxSize of uploader.');
                                return '';
                        }

                        if(!$this->checkExtension($upFileInfo['extension'])){
                                $this->addError($key, 'file type not allowed.');
                                return '';
                        }
						$upFileInfo['name'] = getCdnSafeName($upFileInfo['name']);
                        $newName = $this->generateNewFilePath($upFileInfo['name'] , $upFileInfo['extension']);
                        $absNewName = str_replace('/',DIRECTORY_SEPARATOR , $newName);

                        if(!$this->buildPath($absNewName)){
                                $this->addError($key, 'error occurred during subdirectory creating.');
                                return '';
                        }else{
                                $skipUpload = false ;
                                if($this->existFile != self::EXISTS_OVERWRITE){
                                        if(file_exists($this->topUploadDir.DIRECTORY_SEPARATOR.$absNewName)){
                                                if($this->existFile == self::EXISTS_IGNORE){
                                                        $skipUpload = true;
                                                }else if($this->existFile == self::EXISTS_RENAME){
                                                        $dotPos = strrpos($absNewName,'.');
                                                        if($dotPos === false ) $dotPos = strlen($absNewName)-1;
                                                        $newNamePart1 = substr($absNewName,0,$dotPos);
                                                        $newNamePart2 = substr($absNewName,$dotPos);

                                                        for($i=1;$i<10000;$i++){
                                                                $tmpFile = $this->topUploadDir.DIRECTORY_SEPARATOR.$newNamePart1.'_'.$i.$newNamePart2 ;
                                                                if(!file_exists($tmpFile)){
                                                                        $absNewName = $newNamePart1.'_'.$i.$newNamePart2;
                                                                        $newName = str_replace(DIRECTORY_SEPARATOR,'/',$absNewName);
                                                                        break;
                                                                }
                                                        }
                                                }else{
                                                        $this->addError($key, 'file already exists.');
                                                        return '';
                                                }
                                        }
                                }

                                if($skipUpload === true || move_uploaded_file($upFileInfo['tmp'], $this->topUploadDir.DIRECTORY_SEPARATOR.$absNewName)){
                                        return $newName;
                                }else{
                                        //echo $upFileInfo['tmp'].' '.$this->topUploadDir.DIRECTORY_SEPARATOR.$absNewName;
                                        $this->addError($key, 'error occurred during move uploaded file.');
                                        return '';
                                }
                        }
                }
        }

        /**
         * upload files , if overwritePost is enable  will write post variables
         * @param string $fileVarName
         * @return boolean
         */
        public function process($fileVarName)
        {
                if(!isset($_FILES[$fileVarName])){
                        return false;
                }

                if(is_array($_FILES[$fileVarName]['name'])){//mulati files .
                        $newfile = array();
                        foreach($_FILES[$fileVarName]['name'] as $fileIndex=>$name){

                                $newfile[$fileIndex] = $this->processFile(
                                                $fileVarName.'_'.$fileIndex,
                                                array(
                                                                'name'=>$name,
                                                                'error'=>$_FILES[$fileVarName]['error'][$fileIndex],
                                                                'type'=>$_FILES[$fileVarName]['type'][$fileIndex],
                                                                'size'=>$_FILES[$fileVarName]['size'][$fileIndex],
                                                                'tmp'=>$_FILES[$fileVarName]['tmp_name'][$fileIndex],
                                                                'extension'=>$this->getExtension($name),
                                                )
                                );
                        }
                }else{
                        $newfile = $this->processFile(
                                        $fileVarName,
                                        array(
                                                        'name'=>$_FILES[$fileVarName]['name'],
                                                        'error'=>$_FILES[$fileVarName]['error'],
                                                        'type'=>$_FILES[$fileVarName]['type'],
                                                        'size'=>$_FILES[$fileVarName]['size'],
                                                        'tmp'=>$_FILES[$fileVarName]['tmp_name'],
                                                        'extension'=>$this->getExtension($_FILES[$fileVarName]['name']),
                                        )
                        );
                }
                //overwrite post variable
                if($this->overwritePost){
                    $_POST[$fileVarName] = $newfile ;
                }

                if($newfile == '' || $newfile == array() ){
                        return false ; //no upload
                }else{
                        return true;
                }
        }

        /**
         * Generate thumbnails
         * According to the specified size are cut proportionally
         * The $dest do not specify displayed to the browser
         * @param $str string
         * @param $dest string
         * @param $width int
         * @param $height int
         */
        public static function generateThumb($src , $dest='auto', $width = 120, $height = 90){
            $info = getimagesize($src);
            $sRate = (float)$info[0] / $info[1] ;
            $dRate = (float)$width / $height ;

            if($dest == 'auto'){$pos = strrpos($src,'.');$dest = substr($src,0,$pos).'_'.$width.'x'.$height.substr($src,$pos);	}

            $cutWidth = $dRate*$info[1];
            if($cutWidth <= $info[0]){
                $cutHeight = $cutWidth/$dRate;
                $posX = floor(($info[0] - $cutWidth)/2);
                $posY = floor(($info[1] - $cutHeight)/2);
            }else{
                $cutHeight = $info[0]/$dRate;
                $cutWidth = $cutHeight*$dRate;
                $posX = floor(($info[0] - $cutWidth)/2);
                $posY = floor(($info[1] - $cutHeight)/2);
            }

            switch($info[2])
            {
                case IMG_GIF : $src_img = imagecreatefromgif($src);break;
                case IMG_JPG : $src_img = imagecreatefromjpeg($src);break;
                case IMG_JPEG : $src_img = imagecreatefromjpeg($src);break;
                case IMG_PNG : $src_img = imagecreatefrompng($src);break;
                default : die('unsupported image format');
            }
            $dest_img = imagecreatetruecolor($width,$height) or die ('no gd lib install');
            imagecopyresampled ( $dest_img, $src_img, 0, 0, $posX, $posY, $width, $height, $cutWidth, $cutHeight);

            switch($info[2])
            {
                case IMG_GIF : $srcImg = imagecreatefromgif($src);break;
                case IMG_JPG : $srcImg = imagecreatefromjpeg($src);break;
                case IMG_JPEG : $srcImg = imagecreatefromjpeg($src);break;
                case IMG_PNG : $srcImg = imagecreatefrompng($src);break;
                default : die('unsupported image format');
            }
            if($dest != '')
            {
                $pos = strrpos($dest,'.');
                $ext = strtolower(substr($dest,$pos+1,strlen($dest) - $pos -1));
                switch($ext)
                {
                    case 'jpeg':imagejpeg($dest_img,$dest);break;
                    case 'jpg':imagejpeg($dest_img,$dest);break;
                    case 'gif':imagegif($dest_img,$dest);break;
                    case 'png':imagepng($dest_img,$dest);break;
                    default :
                        {
                        switch($info[2])
                        {
                            case IMG_GIF : imagegif($dest_img,$dest);break;
                            case IMG_JPG : imagejpeg($dest_img,$dest);break;
                            case IMG_JPEG :imagejpeg($dest_img,$dest);break;
                            case IMG_PNG : imagepng($dest_img,$dest);break;
                        }
                        }
                }
                return $dest;
            }else{
                header("Content-type: image/PNG");
                imagepng($dest_img);
            }
            imagedestroy($dest_img);
            imagedestroy($src_img);
        }

        /**
         * image watermarking
         * @author panda.xiong
         * @param $src  image
         * @param $srcWater watermark image
         * @param $dstFile  new watermark image
         * @param $position watermark position
         * @param $alpha  watermark alpha
         * @param $word watermark word
         * @param $useTff use tff font, support Chinese characters, default false
         * @param $fontSize font size
         */
        public function generateWatermark($src,$srcWater="",$dstFile="",$position=1,$alpha=100,$word="",$useTff=false, $fontSize = 12){
            $data = getimagesize($src);
            if($data[0]<1 || $data[1]<1) { return 0; die(); }
            $srcW=$data[0];
            $srcH=$data[1];
            switch ($data[2]) {
                case 1:   $srcImg = @imagecreatefromgif($src);  break;
                case 2:   $srcImg = @imagecreatefromjpeg($src);  break;
                case 3:   $srcImg = @imagecreatefrompng($src);  /*imagesavealpha($srcImg, true);*/  break;
            }
            if(function_exists('imagecreatetruecolor')){
                $dstimg=imagecreatetruecolor($data[0],$data[1]);
            }else{
                $dstimg=imagecreate($data[0],$data[1]);
            }
            if(function_exists('imagecopyresampled')){
                imagecopyresampled($dstimg,$srcImg,0,0,0,0,$data[0],$data[1],$srcW,$srcH);
            }else{
                imagecopyresized($dstimg,$srcImg,0,0,0,0,$data[0],$data[1],$srcW,$srcH);
            }

            if($srcWater!=""){
                $dataLow = getimagesize($srcWater);
                switch ($dataLow[2]) {
                    case 1:   $srcImgWater = @imagecreatefromgif($srcWater);  break;
                    case 2:   $srcImgWater = @imagecreatefromjpeg($srcWater);  break;
                    case 3:   $srcImgWater = @imagecreatefrompng($srcWater); /*imagealphablending($srcImgWater,false); imagesavealpha($srcImgWater, true); */ break;
                }
                $dst_x=0;
                $dst_y=0;
                switch($position){
                    case 1: $dst_x=0; $dst_y=0; break;
                    case 2: $dst_x=intval(($data[0]-$dataLow[0])/2); $dst_y=0; break;
                    case 3: $dst_x=intval($data[0]-$dataLow[0]); $dst_y=0; break;
                    case 4: $dst_x=0; $dst_y=intval(($data[1]-$dataLow[1])/2); break;
                    case 5: $dst_x=intval(($data[0]-$dataLow[0])/2); $dst_y=intval(($data[1]-$dataLow[1])/2); break;
                    case 6: $dst_x=intval($data[0]-$dataLow[0]); $dst_y=intval(($data[1]-$dataLow[1])/2); break;
                    case 7: $dst_x=0; $dst_y=intval($data[1]-$dataLow[1]); break;
                    case 8: $dst_x=intval(($data[0]-$dataLow[0])/2); $dst_y=intval($data[1]-$dataLow[1]); break;
                    case 9: $dst_x=intval($data[0]-$dataLow[0]); $dst_y=intval($data[1]-$dataLow[1]); break;
                }
            }
            @imagecopymerge($dstimg,$srcImgWater,$dst_x, $dst_y,0,0,$dataLow[0],$dataLow[1],$alpha);
            if($word!=""){
                $font=5;
                $len=strlen($word)*9;
                $wordColor=imagecolorallocate($dstimg,0,0,0);
                $wordX=($data[0]-$len)/2;
                $wordY=($data[1]-5)/2;
                switch($position){
                    case 1: $wordX=0+$dataLow[0]+2; $wordY=0; break;
                    case 2: $wordX=intval(($data[0]-$len)/2)+2; $wordY=$dataLow[1]; break;
                    case 3: $wordX=($data[0]-$len)-$dataLow[0]-2; $wordY=0; break;
                    case 4: $wordX=0; $wordY=intval(($data[1]+$dataLow[1])/2)-6; break;
                    case 5: $wordX=intval(($data[0]-$len)/2); $wordY=($data[1]+$dataLow[1])/2-6; break;
                    case 6: $wordX=($data[0]-$len)-2; $wordY=($data[1]+$dataLow[1])/2-6; break;
                    case 7: $wordX=0+2+$dataLow[0]; $wordY=$data[1]-9-6; break;
                    case 8: $wordX=intval(($data[0]-$len)/2); $wordY=$data[1]-9-6-$dataLow[1]; break;
                    case 9: $wordX=$data[0]-$len-2-$dataLow[0]; $wordY=$data[1]-9-6; break;
                }
                if($useTff==true){
                    $font = $fontSize;
                    $ttffont = '/usr/share/fonts/chinese/TrueType/ukai.ttf';	//font path
                    @imagettftext($dstimg, $font, 0, $wordX, $wordY, $wordColor, $ttffont, $word);//write word (support Chinese characters)
                }else{
                    @imagestring($dstimg,$font,$wordX,$wordY,$word,$wordColor);
                }
            }

            if($dstFile!=""){
                switch ($data[2]) {
                    case 1:   imagegif($dstimg,$dstFile);  break;
                    case 2:   imagejpeg($dstimg,$dstFile,100);   break;
                    case 3:   /*imagesavealpha($dstimg, true);*/ imagepng($dstimg,$dstFile);  break;
                }
            }else{
                header("Content-type: image/png");
                header("Cache-Control: no-cache");
                switch ($data[2]) {
                    case 1:   imagegif($dstimg);  break;
                    case 2:   imagejpeg($dstimg,'',100);   break;
                    case 3:   /*imagesavealpha($dstimg, true);*/ imagepng($dstimg);  break;
                }
            }
            @imagedestroy($dstimg);
            @imagedestroy($srcImg);
            @imagedestroy($srcImgWater);
        }

        /**
         * create folder
         * @param $dir
         * @param int $mode
         * @return bool
         */
        public function mkdirs($dir, $mode = 0777){
                if (is_dir($dir) || @mkdir($dir, $mode)) return true;
                if (!mkdirs(dirname($dir), $mode)) return false;
                return @mkdir($dir, $mode);
        }

}

?>
