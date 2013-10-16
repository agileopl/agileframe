<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Filter_RichEditor implements Zend_Filter_Interface
{

    public function filter($value)
    {
        require_once 'HTMLPurifier/HTMLPurifier.auto.php';

        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', Zend_Registry::get('config')->purifier->cache_dir);

        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
        $config->set('Output.FlashCompat', true);
        $config->set('HTML.FlashAllowFullScreen', 'true');

        $def = $config->getHTMLDefinition(true);
        if ($def) {
            $def->addAttribute('a', 'lang', new HTMLPurifier_AttrDef_Text(false, false, true));
            $def->addAttribute('a', 'target', new HTMLPurifier_AttrDef_Enum(
                            array('_blank', '_self', '_target', '_top')
            ));
            $def->addAttribute('a', 'data-id', new HTMLPurifier_AttrDef_Integer());
            $def->addAttribute('a', 'data-type', new HTMLPurifier_AttrDef_Text(false, false, true));
            $def->addAttribute('a', 'data-align', new HTMLPurifier_AttrDef_Text(false, false, true));
            
            $def->addElement('iframe', 'Block', 'Inline', 'Common', array(
                'width' => 'Pixels',
                'height' => 'Pixels',
                'src' => 'URI',
                'frameborder' => 'Pixels',
                'allowfullscreen' => 'Pixels'
            ));

            $max = $config->get('HTML.MaxImgLength');
            $object = $def->addElement(
                    'object', 'Inline', 'Optional: param | Flow | #PCDATA', 'Common', array(
                'type' => 'Enum#application/x-shockwave-flash',
                'width' => 'Pixels#' . $max,
                'height' => 'Pixels#' . $max,
                'data' => 'URI#embedded',
                'codebase' => new HTMLPurifier_AttrDef_Enum(array('http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0')))
            );
            $object->attr_transform_post[] = new HTMLPurifier_AttrTransform_SafeObject();

            $def->addElement('param', false, 'Empty', false, array(
                'id' => 'ID',
                'name*' => 'Text',
                'value' => 'Text'
                    )
            );
        }

        $purifier = new HTMLPurifier($config);
        $value = $purifier->purify($value);
        return preg_replace('/<!--\[if IE\]>(.*)<!\[endif\]-->/iu', '', $value);
    }

}