<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Agileo_View_Helper_FluidRowCols extends Zend_View_Helper_Abstract
{

    public function fluidRowCols($data, $maxCols = 3)
    {
        
        $rcData = $this->view->trans2xCols($data, $maxCols);
        
        $span = round(12/$maxCols);
        
        $ret = '';
        
        foreach($rcData as $row => $cols) {
            
            $ret .= '<div class="row-fluid">';
            
            foreach($cols as $col => $html) {
                $ret .= '<div class="span'.$span.'">'.$html.'</div>';
            }
            
            $ret .= '</div>';
            
        }
        
        return $ret;
    }

}
