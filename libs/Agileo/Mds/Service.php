<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

use PHPImageWorkshop\ImageWorkshop;

class Agileo_Mds_Service
{

    protected $_uri = '';
    protected $_file = '';
    protected $_mime_type = '';
    protected $_type = '';
    protected $_ext = '';
    
    protected $_optionsType = array();
    protected $_options = array();

    public function __construct()
    {
        $this->_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        if (preg_match('@/(p|f|a|v)/(o|c|r|k)([^/]*)/f/(.+)@i', $this->_uri, $matches)) {

            $this->_type = $matches[1];
            $this->_file = '/'.$matches[4];
            
            $this->_optionsType = $matches[2];
            $this->_options = !empty($matches[3]) ? explode(',',substr($matches[3],1)) : array();
            
            $this->_ext = substr($this->_file, strrpos($this->_file, '.')+1);
            $this->_mime_type = $this->_getMediaMineTypeForExt($this->_ext); 
            
        } else {
            throw new Agileo_Exception('404 Not Found', 404);
        }
        
    }

    public function run()
    {
        if($this->_type == 'p') {
            $this->_runPhoto();
        } else {
            $this->_runFile();
        }
    }
    
    
    protected function _runPhoto()
    {
        
        $file = '';
        if($this->_optionsType == 'o') {
            $file = $this->_getFile ();
        } else if($this->_optionsType == 'c') {
            
            $uplConfig = Zend_Registry::get('config')->uploader;
            
            $file = $uplConfig->publicPath . $this->_uri;
            if(!file_exists($file)) {
                
                if(!file_exists($uplConfig->base . $this->_file)) {
                    throw new Agileo_Exception('MDS file no exist: ' . $uplConfig->base . $this->_file);
                }
                
                
                $destX = $this->_options[0];
                $destY = $this->_options[1];

                $layer = ImageWorkshop::initFromPath($uplConfig->base . $this->_file);
                
                // skaluj maksymalnie
                $imageSize = getimagesize ($uplConfig->base . $this->_file);
                $sourceX = $imageSize[0];
                $sourceY = $imageSize[1];

                $cropPoints = null;
                if(isset($this->_options[2])) {
                    if(isset($this->_options[3])) {
                        $cropPoints = array(
                            (int)$this->_options[2], (int)$this->_options[3], (int)$this->_options[4], (int)$this->_options[5]
                        );
                    } else {
                        $destP = $this->_options[2];
                    }
                } else {
                    $destP = 'MM';
                }
                
                if(empty($destY) && !empty($cropPoints)) {
                    $wsp= $destX / $sourceX;
                    $y1 = round($cropPoints[1]*$sourceY/100);
                    $y2 = round($cropPoints[3]*$sourceY/100);
                    
                    $destY = $wsp*($y2-$y1); 
                } elseif(empty($destX) && !empty($cropPoints)) {
                    $wsp= $destY / $sourceY;
                    $x1 = round($cropPoints[0]*$sourceX/100);
                    $x2 = round($cropPoints[2]*$sourceX/100);
                    
                    $destX = $wsp*($x2-$x1); 
                }

                $wsp= max($destX / $sourceX, $destY / $sourceY);
        
                $destResizeX= $wsp * $sourceX;
                $destResizeY= $wsp * $sourceY;
                
                $layer->resizeInPixel($destResizeX, $destResizeY, true);
                
                if(!empty($cropPoints)) {
                    $x1 = round($cropPoints[0]*$destResizeX/100);
                    $y1 = round($cropPoints[1]*$destResizeY/100);
                    $x2 = round($cropPoints[2]*$destResizeX/100);
                    $y2 = round($cropPoints[3]*$destResizeY/100);
                    
                    $rx = $x1+$destX > $destResizeX ? $x1+$destX-$destResizeX: 0;
                    $ry = $y1+$destY > $destResizeY ? $y1+$destY-$destResizeY: 0;
                    
                    $layer->cropInPixel($destX, $destY, $x1-$rx, $y1-$ry, 'LT');
                    
                } else {
                    $layer->cropInPixel($destX, $destY, 0, 0, $destP);
                }
                
                // T::dump('x1'.$cropPoints);
                // T::dump('x1'.$x1);
                // T::dump('y1'.$y1);
                // T::dump('destResizeX'.$destResizeX);
                // T::dump('destResizeY'.$destResizeY);
                // T::dump('rx'.$rx);
                // T::dump('ry'.$ry);
                // T::dump('destX'.$destX);
                // T::dump('destY'.$destY);
// 
                // echo "$destX, $destY, $x1, $y1";
                // exit;
                
                $fileParts = pathinfo($file);
                
                $layer->save($fileParts['dirname'], $fileParts['basename'], true, null, 95);
                
                if(!file_exists($file)) {
                    throw new Agileo_Exception('MDS copy error: ' . $file);
                }
                
                $image = $layer->getResult();
                
                $this->_showImage($image);
                
            }
        } else if($this->_optionsType == 'k') {
            
            $uplConfig = Zend_Registry::get('config')->uploader;
            
            $file = $uplConfig->publicPath . $this->_uri;
            if(!file_exists($file)) {
                
                if(!file_exists($uplConfig->base . $this->_file)) {
                    throw new Agileo_Exception('MDS file no exist: ' . $uplConfig->base . $this->_file);
                }
                
                $destX = $this->_options[0];
                $destY = $this->_options[1];
                $destP = !empty($this->_options[2]) ? strtoupper($this->_options[2]) : 'MM';
                $destBg = !empty($this->_options[3]) ? $this->_options[3] : '000000';
                
                $fileParts = pathinfo($file);
                
                $layer = ImageWorkshop::initFromPath($uplConfig->base . $this->_file);
                $layer->resizeInPixel($destX, $destY, true, 0, 0, $destP);
                
                $layer->save($fileParts['dirname'], $fileParts['basename'], true, $destBg, 95);
                
                if(!file_exists($file)) {
                    throw new Agileo_Exception('MDS copy error: ' . $file);
                }
                
                $image = $layer->getResult($destBg);
                
                $this->_showImage($image);
                
            }

        } else if($this->_optionsType == 'r') {
            
            $uplConfig = Zend_Registry::get('config')->uploader;
            
            $file = $uplConfig->publicPath . $this->_uri;
            if(!file_exists($file)) {
                
                if(!file_exists($uplConfig->base . $this->_file)) {
                    throw new Agileo_Exception('MDS file no exist: ' . $uplConfig->base . $this->_file);
                }
                
                $destX = $this->_options[0];
                $destY = $this->_options[1];
                
                $imageSize = getimagesize ($uplConfig->base . $this->_file);
                $sourceX = $imageSize[0];
                $sourceY = $imageSize[1];
                
                $wsp= min($destX / $sourceX, $destY / $sourceY);
        
                $destX= $wsp * $sourceX;
                $destY= $wsp * $sourceY;
                
                $fileParts = pathinfo($file);
                
                $layer = ImageWorkshop::initFromPath($uplConfig->base . $this->_file);
                $layer->resizeInPixel($destX, $destY, true);
                $layer->save($fileParts['dirname'], $fileParts['basename'], true, null, 95);
                
                if(!file_exists($file)) {
                    throw new Agileo_Exception('MDS copy error: ' . $file);
                }
                
                $image = $layer->getResult();
                
                $this->_showImage($image);
                
            }
            
        }
        
    }
    
    protected function _showImage($image) {
        
        if($this->_ext == 'png') {
            header('Content-type: image/png');
            imagepng($image, null, 8); // We choose to show a PNG (quality of 8 on a scale of 0 to 9)
            exit;
            
        } elseif ($this->_ext == 'gif') {
            header('Content-type: image/gif');
            imagegif($image); // We choose to show a GIF
            exit;
            
        } else {
            header('Content-type: image/jpeg');
            imagejpeg($image, null, 95); // We choose to show a JPEG (quality of 95%)
            exit;
            
        }
        
    }
    
    protected function _runFile()
    {
        $filename = $this->_getFile ();

        // fix for IE catching or PHP bug issue
        header("Pragma: public");
        header("Expires: 0"); // set expiration time
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        // browser must download file from server instead of cache
        
        // force download dialog
        header("Content-Type: ".$this->_mime_type);               
        header("Content-Length: ".filesize($filename));
        
        @readfile($filename) or die("");
    }
    
    private function _getFile ()
    {

        $uplConfig = Zend_Registry::get('config')->uploader;
        
        $file = $uplConfig->publicPath . $this->_uri;
        if(!file_exists($file)) {
            
            if(!file_exists($uplConfig->base . $this->_file)) {
                throw new Agileo_Exception('MDS file no exist: ' . $uplConfig->base . $this->_file);
            }

            $pathParts = pathinfo($file);
            mkdir($pathParts['dirname'], 0777, true);
            if(!is_dir($pathParts['dirname'])) {
                throw new Agileo_Exception('MDS dir create error', 502);
            }
    
            @copy($uplConfig->base . $this->_file, $file);
            
            if(!file_exists($uplConfig->publicPath . $this->_uri)) {
                throw new Agileo_Exception('MDS copy error: ' . $file);
            }
            
        }
        
        return $file;
    }
    
    protected function _getMediaMineTypeForExt($ext)
    {
        $types = array();
        $types['mp3'] = 'audio/x-mp3';
        $types['wav'] = 'audio/x-wav';
        $types['mpega'] = 'audio/x-mpeg';
        $types['mpa2'] = 'audio/x-mpeg-2';
        $types['mp2a'] = 'audio/x-mpeg-2';
        $types['mmid'] = 'x-music/x-midi';
        $types['mid'] = 'x-music/x-midi';
        $types['wav'] = 'audio/x-wav';
        $types['wav'] = 'audio/x-wav';

        $types['mpeg'] = 'video/mpeg';
        $types['mpg'] = 'video/mpeg';
        $types['mpe'] = 'video/mpeg';
        $types['mpv2'] = 'video/mpeg-2';
        $types['mp2v'] = 'video/mpeg-2';
        $types['mov'] = 'video/quicktime';
        $types['qt'] = 'video/quicktime';
        $types['avi'] = 'video/x-msvideo';
        $types['movie'] = 'video/x-sgi-movie';
        $types['mpeg'] = 'video/mpeg';
        $types['mpeg'] = 'video/mpeg';

        $types['jpeg'] = 'image/jpeg';
        $types['jpg'] = 'image/jpeg';
        $types['gif'] = 'image/gif';
        $types['png'] = 'image/png';
        
        $types['swf'] = 'application/x-shockwave-flash';

        return!empty($types[$ext]) ? $types[$ext] : 'application/octet-stream';
    }
    
}
