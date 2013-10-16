<?php
require_once 'Agileo/Bootstrap/Core.php';

class Bootstrap extends Agileo_Bootstrap_Core
{

    public function run()
    {
        try {
            $service = new Agileo_Mds_Service();
            $service->run();
        } catch (Exception $e) {
            header('HTTP/1.0 404 Not Found');
            throw $e;
        }
    }

    protected function _initAutoloader()
    {
        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->setFallbackAutoloader(true);
    }

}