<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
/**
 * Skrypt tylko dla testów wyświetający linki do skryptów
 * Dla tych ktorzy odpalają skrypty w przeglądarce wystarczy ustawić hosta na katalog jobs
 */ 
$dir = $rootdir = realpath(dirname(__FILE__));
$rootdir = str_replace("\\",'/',$dir);

$d = array();
while($dirs = glob($dir . '/*', GLOB_ONLYDIR)) {
  $dir .= '/*';
  if(!$d) {
     $d=$dirs;
   } else {
      $d=array_merge($d,$dirs);
   }
}

$dirs = array();
$files = array();
foreach($d as $inx => $dir) {
    $dir = str_replace("\\",'/',$dir);
    $files = array_merge($files,glob($dir . '/*.php'));
}

?>
<div style="width:300px; float: left;">
<ul>
<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
foreach($files as $inx => $file) {
    ?><li><a href="<?php echo str_replace($rootdir, '', $file);?>" target="viewer"><?php echo str_replace($rootdir, '', $file); ?></a><?php
}
?>
</ul>
</div>
<div style="width:70%; float: left;">
    <iframe src="" name="viewer" width="100%" height="100%"/>
</div>
<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
print_r($files);
?>