<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Agileo_Web_EditorEmbedHelper extends Zend_Controller_Action
{
    
    protected static $_view = null;
    
    public static function getView() 
    {
        if(empty(self::$_view)) {
            self::$_view = Zend_Layout::getMvcInstance()->getView();
        }
        return self::$_view;
    }
    
    public static function prepareDescriptionField(Agileo_Object $object, $descFieldName = 'description')
    {
        $description = $object->{$descFieldName};
        if($object->hasSubCollection('EditorEmbed')) {
            foreach($object->getSubCollection('EditorEmbed') as $item) {
                $description = mb_substr($description, 0, $item->position) . self::embedColorer ($item) . mb_substr($description, $item->position);
            }
        }
        $object->{$descFieldName} = $description;
    }
    
    public static function embedColorer (EditorEmbed $embed)
    {
        switch($embed->type) {
            case 'uatt-image' : return strpos($embed->class, 'att-gallery')  !== false ? self::embedUGallery($embed) : self::embedUImage($embed); 
            case 'uatt-video' : return self::embedUVideo($embed); 
            default : return '<!-- no embed: ' .$embed->type . ':'.$embed->object_id.' //-->';
        }
    }

    public static function embedUImage(EditorEmbed $embed)
    {
        $attach = $embed->hasSubObject('Attachment') ? $embed->getSubObject('Attachment') : null;
        
        if(!$attach || !$attach->isImage() || !$attach->isActive()) {
            return '<!-- no embed: ' .$embed->type . ':'.$embed->object_id.' //-->';
        }
        
        $align = !empty($embed->align) ? $embed->align : 'center';
        $resize = $align == 'center' ? '510x315' : '400x400';
        return '<a class="embt-image emba-'.$align.'" href="'.self::getView()->mdsImage($attach, array('resize' => '800x600')).'"><img src="'.self::getView()->mdsImage($attach, array('resize' => $resize)).'" alt="'.self::getView()->escape($attach->description).'"><em>'.self::getView()->escape($attach->description).'</em></a>';
        
    }
    
    public static function embedUGallery(EditorEmbed $embed)
    {
        $attach = $embed->hasSubObject('Attachment') ? $embed->getSubObject('Attachment') : null;
        
        if(!$attach || !$attach->isImage() || !$attach->isActive()) {
            return '<!-- no embed: ' .$embed->type . ':'.$embed->object_id.' //-->';
        }
        
        $html = '<div class="embt-gallery">
            <div id="attCarousel'.$attach->id.'" class="carousel slide">
                <div class="carousel-inner">';
        
        $html .= '<div class="item active">
                    <img src="'.self::getView()->mdsImage($attach, array('resize' => '800x500', 'crop' => 'mt')).'" alt="">
                    <div class="carousel-caption">
                      <p>'.$attach->description.'</p>
                    </div>
                  </div>';
                  
                  
        foreach($attach->getSubCollection('Attachment') as $satt) {
             if($satt->isActive()) {
                $html .= '<div class="item">
                            <img src="'.self::getView()->mdsImage($satt, array('resize' => '800x500', 'crop' => 'mt')).'" alt="">
                            <div class="carousel-caption">
                              <p>'.$satt->description.'</p>
                            </div>
                          </div>';
             }
        }
                  
        $html .= '</div>
                <a class="left carousel-control" href="#attCarousel'.$attach->id.'" data-slide="prev">&lsaquo;</a>
                <a class="right carousel-control" href="#attCarousel'.$attach->id.'" data-slide="next">&rsaquo;</a>
              </div>
           </div>';


        return $html;
        
    }

    public static function embedUVideo(EditorEmbed $embed)
    {
        $attach = $embed->hasSubObject('Attachment') ? $embed->getSubObject('Attachment') : null;
        
        if(!$attach || !$attach->isVideo() || !$attach->isActive()) {
            return '<!-- no embed: ' .$embed->type . ':'.$embed->object_id.' //-->';
        }
        
        return '<div class="embt-video">'.self::getView()->mdsVideo($attach, array('width'=>770,'height' => 433)).'</div>';
    }    
}
