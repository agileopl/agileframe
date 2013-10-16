<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

include realpath(dirname(__FILE__)) . '/../../common.inc.php';

$compressorJar = APPLICATION_EXTERNAL_LIBS . '/Yuicompressor/2.4.6/yuicompressor.jar';

$APP_NAME = 'cms';

include realpath(dirname(__FILE__)) . '/compressor.inc.php';


exit;
