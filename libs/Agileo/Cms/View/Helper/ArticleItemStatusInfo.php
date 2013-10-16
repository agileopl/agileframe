<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Cms_View_Helper_ArticleItemStatusInfo extends Zend_View_Helper_Abstract
{

    public function articleItemStatusInfo(Article_Item $article)
    {
        
        $html = '';
        
        if($article->isActiveInFuture()) :
            $html .= '<span class="badge badge-info">Aktywny od: '.$this->view->prettyDate($article->public_date).'</span>';
        elseif($article->wasActiveInPast()) :
            $html .= '<span class="badge badge-important">Był aktywny do: '.$this->view->prettyDate($article->public_end_date).'</span>';
        elseif($article->isActiveToFuture()) :
            $html .= '<span class="badge badge-info">Aktywny do: '.$this->view->prettyDate($article->public_end_date).'</span>';
        endif;
           
        return '<div>'.$html.'</div>'; 
    }
    

}