<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_PrettyDate extends Zend_View_Helper_Abstract
{

    public static $days = array ( 'Niedziela', 'Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek', 'Sobota' );
    public static $daysabbrs = array ( 'Nie', 'Pon', 'Wto', 'Śro', 'Czw', 'Pią', 'Sob' );
    public static $months = array ( '', 'Stycznia', 'Lutego', 'Marca', 'Kwietnia', 'Maja', 'Czerwca', 'Lipca', 'Sierpnia', 'Września', 'Października', 'Listopada', 'Grudnia' );
    public static $monthabbrs = array ( '', 'Sty', 'Lut', 'Mar', 'Kwi', 'Maj', 'Cze', 'Lip', 'Sie', 'Wrz', 'Paź', 'Lis', 'Gru' );

    public function prettyDate($date, array $options = array())
    {
        
        if(empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
            return '';
        }
        
        $time = strtotime($date);
        
        $isWithTime = strlen($date) > 10 || !empty($options['showTime']);
        
        $ymd = date('Ymd', $time);
        
        $ret = '';
        if(date('Ymd') == $ymd) {
            if($isWithTime) {
                $isNow = abs(time() - $time) < 600;
                $ret = '<span class="ptime'.($isNow ? ' now' : '').'">'.date('H:i',strtotime($date)).'</span>';
            } else {
                $ret = 'dzisiaj';
            }
        } else if(date('Ymd', strtotime('-1 day')) == $ymd) {
            $ret = 'wczoraj';
            if(!empty($options['showTime'])) {
                $ret .= ', <span class="ptime">'.date('H:i',strtotime($date)).'</span>';
            }
        } else if(date('Ymd', strtotime('+1 day')) == $ymd){
            $ret = 'jutro';
            if(!empty($options['showTime'])) {
                $ret .= ', <span class="ptime">'.date('H:i',strtotime($date)).'</span>';
            }
        } else {
            
            $ret = 
                (empty($options['hideDayName']) ? self::$days[date('w',$time)] . ', ' : '')
                . date('j', $time) 
                . ' '.self::$months[date('n', $time)] 
                . (date('Y') != date('Y', $time) ? ' '.date('Y', $time) : '');
            if(!empty($options['showTime'])) {
                $ret .= ', <span class="ptime">'.date('H:i',strtotime($date)).'</span>';
            }
        }
        
        return 
            (!empty($options['prefix']) ? $options['prefix'] : '')
            . $ret
            .(!empty($options['sufix']) ? $options['sufix'] : '')
            ;

    }

}