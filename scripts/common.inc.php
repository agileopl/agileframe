<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

set_time_limit(0);

// Define path to entire project
defined('BASE_PATH')
    || define('BASE_PATH', realpath(dirname(__FILE__) . '/..'));

// Define application name
defined('APPLICATION_NAME')
    || define('APPLICATION_NAME', 'scripts');

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', BASE_PATH . '/scripts');

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

defined('APPLICATION_EXTERNAL_LIBS') || define('APPLICATION_EXTERNAL_LIBS', (getenv('APPLICATION_EXTERNAL_LIBS') ? getenv('APPLICATION_EXTERNAL_LIBS') : realpath(APPLICATION_PATH . '/../../libs/')));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(BASE_PATH . '/libs'),
    realpath(BASE_PATH . '/models'),
    realpath(APPLICATION_EXTERNAL_LIBS . DIRECTORY_SEPARATOR . 'ZendFramework' . DIRECTORY_SEPARATOR . '1.12.0'),
    realpath(APPLICATION_EXTERNAL_LIBS . DIRECTORY_SEPARATOR . 'PHPImageWorkshop' . DIRECTORY_SEPARATOR . '2.0'),
    realpath(APPLICATION_EXTERNAL_LIBS . DIRECTORY_SEPARATOR . 'HTMLPurifier' . DIRECTORY_SEPARATOR . '4.4.0'),
    get_include_path(),
)));

require_once 'Zend/Application.php';

// TODO cachowanie
require_once 'Zend/Config/Yaml.php';
$config = new Zend_Config_Yaml(BASE_PATH . '/configs/application.yaml', APPLICATION_NAME, array('allow_modifications' => true));
if(APPLICATION_ENV != 'production') {
    $envConfig = new Zend_Config_Yaml(BASE_PATH . '/configs/application-'.APPLICATION_ENV.'.yaml', APPLICATION_NAME);
    $config->merge($envConfig);
}

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, $config);

$application->bootstrap()
            ->run();


class Semaphor
{

    protected static function _semaforName()
    {
        return 'cron_semafor_' . md5($_SERVER['SCRIPT_FILENAME']);
    }

    public static function check()
    {
        // set semafor
        $cacheTime = 2592000; // 30 dni
        $cache = Zend_Registry::get('cache');
        $cacheIdentity = self::_semaforName();
        if (1 || !$semafor = $cache->load($cacheIdentity)) {
            $cache->save($_SERVER['SCRIPT_FILENAME'], $cacheIdentity, array(), $cacheTime);
            Zend_Registry::get('log')->debug('Skrypt ' . $_SERVER['SCRIPT_FILENAME'] . ': start');
            return true;
        } else {
            Zend_Registry::get('log')->err('Skrypt ' . $_SERVER['SCRIPT_FILENAME'] . ' wykonuje się zbyt dlugo lub się zawiesil.');
        }
        return false;
    }

    public static function remove()
    {
        $cache = Zend_Registry::get('cache');
        $cacheIdentity = self::_semaforName();
        $cache->remove($cacheIdentity);
        Zend_Registry::get('log')->debug('Skrypt ' . $_SERVER['SCRIPT_FILENAME'] . ': stop');
    }

}