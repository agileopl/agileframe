<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Mail_Template
{
    
    static public $encodingIn = 'UTF-8';
    static public $encodingOut = 'UTF-8';
    
    public static function send($input)
    {

        $mail = new Zend_Mail('UTF-8');
        
        $toList = self::parseEmails($input['to']);
        if(count($toList) > 0) {
            foreach($toList as $item) {
                $mail->addTo($item->email, $item->name);
            }
        } else {
            foreach(Zend_Registry::get('config')->globals->mail->to as $item) {
                $mail->addTo($item->email, $item->name);
            }
        }
        
        if (!empty($input['from'])) {
            $item = current(self::parseEmails($input['from']));
            $mail->setFrom($item->email, $item->name);
        } else {
            $fromConf = Zend_Registry::get('config')->globals->mail->from; 
            $mail->setFrom($fromConf->email, $fromConf->name);
        }

        $mail->setSubject(self::encoding($input['subject'], $input));

        $html = self::prepareBodyHtml(!empty($input['bodyHtml']) ? $input['bodyHtml'] : '');
        $mail->setBodyHtml($html);
        
        $text = self::prepareBodyText(!empty($input['bodyText']) ? !empty($input['bodyText']) : !empty($input['bodyHtml']) ? $input['bodyHtml'] : '');
        $mail->setBodyText($text);

        try {
            $mail->send();
            Zend_Registry::get('log')->debug(print_r($mail,true));
            return true;
        } catch (Exception $e) {
            Zend_Registry::get('log')->err($e . print_r($mail, true));
            return false;
        }

    }

    public static function parseEmails($emials) {
        $parsed = array();
        if(!empty($emials)) {
            $emials = is_string($emials) ? array($emials) : $emials;
            foreach($emials as $item) {
                $pItem = new stdClass();
                
                if(is_string($item)) {
                    $pItem->email = $item;
                    $pItem->name = null;
                } elseif (is_array($item)) {
                    $pItem->email = $item[0];
                    $pItem->name = !empty($item[1]) ? self::encoding($item[1]) : null;
                } else {
                    $pItem->email = ''.$item;
                    $pItem->name = NULL;
                }
                $parsed[] = $pItem;
            }    
        }
        
        return $parsed;
    }

    private function prepareBodyHtml($string) 
    {
        return self::encoding($string);
    }
    
    private function prepareBodyText($string) 
    {
        return self::encoding(strip_tags($string));
    }

    private static function encoding($string)
    {
        if (self::$encodingIn != self::$encodingOut) {
            return iconv(self::$encodingIn, self::$encodingOut . "//IGNORE", $string);
        }
        return $string;
    }

}
