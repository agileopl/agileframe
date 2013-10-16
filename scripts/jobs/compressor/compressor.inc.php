<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

if(empty($APP_NAME)) {
    exit;
}

$jsCssHeadFile = realpath(dirname(__FILE__) . '/../../../'.$APP_NAME.'/layouts/scripts/_headLS.php'); 

$publicPath = realpath(dirname(__FILE__) . '/../../../mds/public/'.$APP_NAME);

$jsCssContent = file_get_contents($jsCssHeadFile);  

$compressorHash = date("ymdHis");

$cssCompressFile = $publicPath . '/cmprs/' . $compressorHash . '.css';
$jsCompressFile = $publicPath . '/cmprs/' . $compressorHash . '.js';

if(preg_match_all("@appendStylesheet\(STATIC_PATH\.'/css/([^']+)'\)@i", $jsCssContent, $matches)) {
    
    $contentToCompress = '';
    foreach($matches[1] as $file) {
        $file = '/css/'.$file;
        if(file_exists($publicPath . $file)) {
            $contentToCompress .= file_get_contents($publicPath . $file);
        }
    }
    
    $cssOrginalSize = strlen($contentToCompress);
    
    $command = 'java -jar ' . $compressorJar . ' -o '.$cssCompressFile.' --charset UTF-8 --type css';
    $handle = popen($command, 'w');
    fwrite($handle, $contentToCompress);
    pclose($handle);
    

}

if(preg_match_all("@appendFile\(STATIC_PATH\.'/js/([^']+)'\)@i", $jsCssContent, $matches)) {
    
    $contentToCompress = '';
    foreach($matches[1] as $file) {
        $file = '/js/'.$file;
        if(file_exists($publicPath . $file)) {
            $contentToCompress .= file_get_contents($publicPath . $file)."\n\r";
        }
    }
    
    $jsOrginalSize = strlen($contentToCompress);
    
    $command = 'java -jar ' . $compressorJar . ' -o '.$jsCompressFile.' --charset UTF-8 --type js';
    $handle = popen($command, 'w');
    fwrite($handle, $contentToCompress);
    pclose($handle);
    //file_put_contents($jsCompressFile, $contentToCompress);

    
}

if(file_exists($cssCompressFile) && filesize($cssCompressFile) > 0 
        && file_exists($jsCompressFile) && filesize($jsCompressFile) > 0) {

    $appConfigContent = file_get_contents(BASE_PATH . '/configs/application.yaml');
    $appConfigContent = preg_replace("@compressor_".$APP_NAME."_hash: '([^']*)'@i", "compressor_".$APP_NAME."_hash: '".$compressorHash."'" , $appConfigContent); 
    file_put_contents(BASE_PATH . '/configs/application.yaml', $appConfigContent);

    T::dump($cssCompressFile.': '.file_exists($cssCompressFile) . ', orginal-size: '.sprintf("%.2f",$cssOrginalSize/1024) . 'kB'.', compress-size: '.sprintf("%.2f",filesize($cssCompressFile)/1024) . 'kB');
    T::dump($jsCompressFile.': '.file_exists($jsCompressFile) . ', orginal-size: '.sprintf("%.2f",$jsOrginalSize/1024) . 'kB'.', compress-size: '.sprintf("%.2f",filesize($jsCompressFile)/1024) . 'kB');
            
} else {
    echo 'Błąd kompresji';
}
