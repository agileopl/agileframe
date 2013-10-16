<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Agileo_Db_Profiler_Log extends Zend_Db_Profiler
{

    public function queryEnd($queryId)
    {
        $state = parent::queryEnd($queryId);

        if (!$this->getEnabled() || $state == self::IGNORED) {
            return null;
        }

        $profile = $this->getQueryProfile($queryId);

        $log = "DB: [" . (string) round($profile->getElapsedSecs(), 5) . "] ";
        $log .= $profile->getQuery();
        $params = $profile->getQueryParams();
        if ($params && !empty($parms)) {
            $log .= " (" . join(",", $params) . ")";
        }
        $log = preg_replace('/\s+/', ' ', $log);

        if($profile->getElapsedSecs() > 10) {
            Zend_Registry::get('log')->alert($log);
        } else if($profile->getElapsedSecs() > 5) {
            Zend_Registry::get('log')->crit($log);
        } else if($profile->getElapsedSecs() > 1) {
            Zend_Registry::get('log')->warn($log);
        } else {
            Zend_Registry::get('log')->debug($log);
        }

        return $log;
    }

}
