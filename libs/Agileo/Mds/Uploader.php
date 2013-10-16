<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Agileo_Mds_Uploader
{


    public static function getTempDirectory()
    {
        $tempDir = Zend_Registry::get('config')->uploader->tmp . '/' . date('ym');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        return $tempDir;
    }
    
    /**
     * $ffile Zend_Form_Element_File | Url
     */
    public static function moveFormFileForObject($ffile, Agileo_Object $object)
    {

        $uplConfig = Zend_Registry::get('config')->uploader;
        
        $objectName = get_class($object);
        
        $dirPath = '';
        if(!empty($uplConfig->object->{$objectName})) {
            if(!empty($uplConfig->object->{$objectName}->dirPrefix)) {
                $dirPath .= $uplConfig->object->{$objectName}->dirPrefix;
            }    
            if(!empty($uplConfig->object->{$objectName}->groupBy)) {
                if(!empty($uplConfig->object->{$objectName}->groupBy->field)) {
                    $dirPath .= self::prepareDirPathGroupBy($uplConfig->object->{$objectName}->groupBy, $object);
                }
            }
            if(!empty($uplConfig->object->{$objectName}->byId)) {
                $dirPath .= self::prepareDirPathById($object->id); 
            } else {
                $dirPath .= self::prepareDirPathByDate();
            }
        } else {
            $dirPath .= $uplConfig->default->dirPrefix;
            $dirPath .= self::prepareDirPathByDate();
        }
        if (!is_dir($uplConfig->base . $dirPath)) {
            mkdir($uplConfig->base . $dirPath, 0777, true);
        }
        
        if($ffile instanceof Zend_Form_Element_File) {
            $fileName = $dirPath . '/' . self::prepareFileName ($ffile->getValue());
            rename($ffile->getFileName(), $uplConfig->base . $fileName);
        } elseif(is_string($ffile) && substr($ffile, 0,4) == 'http') {
            if($fileContent = file_get_contents($ffile)) {
                $fileName = $dirPath . '/' . uniqid('fbav').'.jpg';
                file_put_contents($uplConfig->base . $fileName, $fileContent);
            }
        }
        
        if(!file_exists($uplConfig->base . $fileName)) {
            throw new Agileo_Exception('File move error: ' . $uplConfig->base . $fileName);
        }
        
        return $fileName;
    }
    
    public static function prepareFileName ($file, $addTimePrefix = false) 
    {
        $pathParts = pathinfo($file);
        
        $f = new Agileo_Filter_Seo();
        $filename = $f->filter($pathParts['filename']);
        
        return ($addTimePrefix ? date('ymdH').'-' : '') . $filename.'.'.strtolower($pathParts['extension']); 
        
    }
    
    public static function prepareDirPathById($id)
    {
        $hexVal = 'h'.dechex(9999999999-$id);
        $dirPrefix = '';
        while(strlen($hexVal) > 5) {
            $dirPrefix .= '/'.substr($hexVal,0,3);
            $hexVal = substr($hexVal,3);
        }
        if(!empty($hexVal)) {
            $dirPrefix .= '/'.$hexVal;
        }
        return $dirPrefix;
    }
    
    public static function prepareDirPathGroupBy($config, $object)
    {
        
        $field = $config->field;
        if(!empty($config->field) && !empty($config->salt) && !empty($object->{$config->field})) {
            $hexVal = substr(md5($config->salt.$object->{$config->field}),0,12);
            $dirPrefix = '';
            while(strlen($hexVal) > 5) {
                $dirPrefix .= '/'.substr($hexVal,0,4);
                $hexVal = substr($hexVal,4);
            }
            if(!empty($hexVal)) {
                $dirPrefix .= '/'.$hexVal;
            }
            return (!empty($config->prefix) ? '/'.$config->prefix : '').$dirPrefix.(!empty($config->sufix) ? '/'.$config->sufix : '');
        }
        return '';
    }
    
    
    public static function prepareDirPathByDate()
    {
        return '/'.date('ym').'/'.date('dH');
    }
    

}
