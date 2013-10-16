<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class T
{
    
    public static function cacheData($cacheId, $lifetime, $params, $function) {
        $cache = Zend_Registry::get('cache');
        $cacheIdentity = md5($cacheId);
        if (!($data = $cache->load($cacheIdentity))) {
            $data = $function($params);
            if ($lifetime > 0) {
                $cache->save($data, $cacheIdentity, array(), $lifetime);
            }
        }
        
        return $data;
    }
    
    public static function parseColor($cssColor) 
    {
        $color = array();    
        if(substr($cssColor,0,1) == '#') {
            $color['hex'] = $cssColor;
             
            $hexVal = preg_replace('/[^a-fA-F0-9]/i','',$cssColor);
            if (strlen($hexVal) == 3) {
                $hexVal = $hexVal.$hexVal;
            } 
            if (strlen($hexVal) != 6) {
                 return false; 
            } 
            $arrTmp = explode(" ", chunk_split($hexVal, 2, " ")); 
            $arrTmp = array_map("hexdec", $arrTmp);
            
            $color['r'] = $arrTmp[0];
            $color['g'] = $arrTmp[1];
            $color['b'] = $arrTmp[2];
            $color['t'] = 1;
        } else {
                
            $cssrgb = preg_replace('/[^0-9,.]/i','',$cssColor);
            
            $arrColors = explode(',',$cssrgb);
    
            if (count($arrColors) < 3) { return false; } 

            $color['r'] = $arrColors[0];
            $color['g'] = $arrColors[1];
            $color['b'] = $arrColors[2];
            $color['t'] = !empty($arrColors[3]) ? $arrColors[3] : 1;
             
            $out = '#'; 
            for($i = 0; $i<3; $i++) {
                if($arrColors[$i] < 0 || $arrColors[$i] > 255) return false;
                $arrColors[$i] = dechex($arrColors[$i]);
            }
                  
            for($i = 0; $i<3; $i++) 
                $out .= ((strlen($arrColors[$i]) < 2) ? '0' : '').$arrColors[$i]; 
            
            $color['hex'] = $out;
            
        }
        
        return (object) $color;
        
    } 
    
    public static function hex2rgb($hexVal, $rgba = false) {
        
        if($color = self::parseColor($hexVal)) {
            if($rgba) {
                return 'rgba('.$color->r.','.$color->g.','.$color->b.','.$color->t.')';
            } else {
                return 'rgb('.$color->r.','.$color->g.','.$color->b.')';
            }
        }
        return false;
         
    } 
     
    public static function rgb2hex($cssrgb) {
        if($color = self::parseColor($cssrgb)) {
            return $color->hex; 
        }
        return false;
 
    } 

    
    public static function urlEscape($url)
    {
        $seo = new Agileo_Filter_Seo();
        return $seo->filter($url);
    }

    public static function dump($arr)
    {
        echo "\n\n";
        echo '<div style="border:1px solid black;margin:2px;clear:both"><pre>';
        print_r($arr);
        echo '</pre></div>';
        echo "\n\n";
    }

    public static function parseYoutubeId($youtubeUrl)
    {
        $ytId = null;
        $youtubeUrl = trim($youtubeUrl);
        if(!empty($youtubeUrl)) {

            $result = parse_url($youtubeUrl);
    
            if (isset($result['query'])) {
                parse_str(urldecode($result['query']), $result['query']);
                if (isset($result['query']['v'])) {
                    return $result['query']['v'];
                }
            } elseif($result['host'] == 'youtu.be') {
                return substr($result['path'],1);
            }
            
        }

        return $ytId;
    }
    

    /**
     * Zabezpiecznie dla klikadełek
     *
     * @param string $elementName
     * @param int $elementId
     * @return boolean
     */
    public static function checkVotes($elementName, $elementId)
    {
        
        if(!empty(Zend_Registry::get('config')->model->quiz->testMode)) {
            return true;
        }
        
        // mechanizm oparty o cookie
        if (!self::checkVotesInCookie($elementName, $elementId)) {
            //Zend_Registry::get('log')->debug('Glos zablokowany po cookie: '.$elementName.', '.$elementId);
            return false;
        }

        // mechanizm zabezpieczający duża ilość oddawanych głosów z tego samego IP w ciągu krótkiego czasu
        $ip = self::getIp();

        $cache = Zend_Registry::get('clickbuffer');
        $cache->setLifetime(86400);
        // dzien
        $cacheIdentity = 'votescheckip';
        if (($results = $cache->load($cacheIdentity)) === false) {
            $results = array();
        }

        $defaultBan = 5;
        // jeżeli kolejny głos został oddany w krotszym czsie niż ban to wydluz ban
        // każda proba glosu podczas bana przedłuża bana o 10x
        
        if (isset($results[$elementName][$elementId][$ip])) {
            $ban = $results[$elementName][$elementId][$ip]['ban'];
            if ((time() - $results[$elementName][$elementId][$ip]['time']) < $ban) {
                $results[$elementName][$elementId]['time'] = time();
                $results[$elementName][$elementId][$ip]['ban'] = $ban < 9999999999 ? $ban * 10 : $ban;
                $results[$elementName][$elementId][$ip]['time'] = time();
                $cache->save($results, $cacheIdentity);
                //Zend_Registry::get('log')->debug('Glos zablokowany clickbuffer: '.$elementName.', '.$elementId."\n\n".print_r($results[$elementName][$elementId][$ip], true));
                return false;
            } else {
                $results[$elementName][$elementId][$ip]['ban'] = $results[$elementName][$elementId][$ip]['ban'] > $defaultBan * 10 ? $results[$elementName][$elementId][$ip]['ban'] / 10 : $defaultBan;
            }
        } else {
            $results[$elementName][$elementId][$ip]['ban'] = $defaultBan;
        }
        $results[$elementName][$elementId]['time'] = time();
        $results[$elementName][$elementId][$ip]['time'] = time();
        $cache->save($results, $cacheIdentity);

        return true;
    }

    /**
     * Dodatkowy zapis w cookie dla klikadełem żeby móc dane klikadełko ukrywać
     * @param string $elementName
     * @param int $elementId
     * @return boolean
     */
    public static function checkVotesInCookie($elementName, $elementId)
    {
        $id = self::checkVotesCookieName($elementName, $elementId);
        if (empty($_COOKIE[$id])) {
            setcookie($id, 1, time() + 259200, '/'); // 259200 - 3 dni powinno wystarczyć
            return true;
        }
        return false;
    }

    /**
     * Nazwa cookie dla klikadełka
     * @param string $elementName
     * @param int $elementId
     * @return string
     */
    public static function checkVotesCookieName($elementName, $elementId)
    {
        return 'votes' . $elementName . md5($elementId);
    }

    /**
     * Get IP
     * @return string
     */
    public static function getIp()
    {
        if (!empty($_SERVER["HTTP_CLIENT_IP"]) && self::validip($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        }

        if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            foreach (explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) {
                if (self::validip(trim($ip))) {
                    return $ip;
                }
            }
        }

        if (!empty($_SERVER["HTTP_X_FORWARDED"]) && self::validip($_SERVER["HTTP_X_FORWARDED"])) {
            return $_SERVER["HTTP_X_FORWARDED"];
        } elseif (!empty($_SERVER["HTTP_FORWARDED_FOR"]) && self::validip($_SERVER["HTTP_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        } elseif (!empty($_SERVER["HTTP_FORWARDED"]) && self::validip($_SERVER["HTTP_FORWARDED"])) {
            return $_SERVER["HTTP_FORWARDED"];
        } elseif (!empty($_SERVER["REMOTE_ADDR"])) {
            return $_SERVER["REMOTE_ADDR"];
        }
        return null;
    }
    
    
    public static function normalize($string)
    {
        // @formatter:off
        $transliteration = array(
             'Ĳ' => 'I', 'Ö' => 'O','Œ' => 'O','Ü' => 'U','ä' => 'a','æ' => 'a',
             'ĳ' => 'i','ö' => 'o','œ' => 'o','ü' => 'u','ß' => 's','ſ' => 's',
             'À' => 'A','Á' => 'A','Â' => 'A','Ã' => 'A','Ä' => 'A','Å' => 'A',
             'Æ' => 'A','Ā' => 'A','Ą' => 'A','Ă' => 'A','Ç' => 'C','Ć' => 'C',
             'Č' => 'C','Ĉ' => 'C','Ċ' => 'C','Ď' => 'D','Đ' => 'D','È' => 'E',
             'É' => 'E','Ê' => 'E','Ë' => 'E','Ē' => 'E','Ę' => 'E','Ě' => 'E',
             'Ĕ' => 'E','Ė' => 'E','Ĝ' => 'G','Ğ' => 'G','Ġ' => 'G','Ģ' => 'G',
             'Ĥ' => 'H','Ħ' => 'H','Ì' => 'I','Í' => 'I','Î' => 'I','Ï' => 'I',
             'Ī' => 'I','Ĩ' => 'I','Ĭ' => 'I','Į' => 'I','İ' => 'I','Ĵ' => 'J',
             'Ķ' => 'K','Ľ' => 'K','Ĺ' => 'K','Ļ' => 'K','Ŀ' => 'K','Ł' => 'L',
             'Ñ' => 'N','Ń' => 'N','Ň' => 'N','Ņ' => 'N','Ŋ' => 'N','Ò' => 'O',
             'Ó' => 'O','Ô' => 'O','Õ' => 'O','Ø' => 'O','Ō' => 'O','Ő' => 'O',
             'Ŏ' => 'O','Ŕ' => 'R','Ř' => 'R','Ŗ' => 'R','Ś' => 'S','Ş' => 'S',
             'Ŝ' => 'S','Ș' => 'S','Š' => 'S','Ť' => 'T','Ţ' => 'T','Ŧ' => 'T',
             'Ț' => 'T','Ù' => 'U','Ú' => 'U','Û' => 'U','Ū' => 'U','Ů' => 'U',
             'Ű' => 'U','Ŭ' => 'U','Ũ' => 'U','Ų' => 'U','Ŵ' => 'W','Ŷ' => 'Y',
             'Ÿ' => 'Y','Ý' => 'Y','Ź' => 'Z','Ż' => 'Z','Ž' => 'Z','à' => 'a',
             'á' => 'a','â' => 'a','ã' => 'a','ā' => 'a','ą' => 'a','ă' => 'a',
             'å' => 'a','ç' => 'c','ć' => 'c','č' => 'c','ĉ' => 'c','ċ' => 'c',
             'ď' => 'd','đ' => 'd','è' => 'e','é' => 'e','ê' => 'e','ë' => 'e',
             'ē' => 'e','ę' => 'e','ě' => 'e','ĕ' => 'e','ė' => 'e','ƒ' => 'f',
             'ĝ' => 'g','ğ' => 'g','ġ' => 'g','ģ' => 'g','ĥ' => 'h','ħ' => 'h',
             'ì' => 'i','í' => 'i','î' => 'i','ï' => 'i','ī' => 'i','ĩ' => 'i',
             'ĭ' => 'i','į' => 'i','ı' => 'i','ĵ' => 'j','ķ' => 'k','ĸ' => 'k',
             'ł' => 'l','ľ' => 'l','ĺ' => 'l','ļ' => 'l','ŀ' => 'l','ñ' => 'n',
             'ń' => 'n','ň' => 'n','ņ' => 'n','ŉ' => 'n','ŋ' => 'n','ò' => 'o',
             'ó' => 'o','ô' => 'o','õ' => 'o','ø' => 'o','ō' => 'o','ő' => 'o',
             'ŏ' => 'o','ŕ' => 'r','ř' => 'r','ŗ' => 'r','ś' => 's','š' => 's',
             'ť' => 't','ù' => 'u','ú' => 'u','û' => 'u','ū' => 'u','ů' => 'u',
             'ű' => 'u','ŭ' => 'u','ũ' => 'u','ų' => 'u','ŵ' => 'w','ÿ' => 'y',
             'ý' => 'y','ŷ' => 'y','ż' => 'z','ź' => 'z','ž' => 'z','Α' => 'A',
             'Ά' => 'A','Ἀ' => 'A','Ἁ' => 'A','Ἂ' => 'A','Ἃ' => 'A','Ἄ' => 'A',
             'Ἅ' => 'A','Ἆ' => 'A','Ἇ' => 'A','ᾈ' => 'A','ᾉ' => 'A','ᾊ' => 'A',
             'ᾋ' => 'A','ᾌ' => 'A','ᾍ' => 'A','ᾎ' => 'A','ᾏ' => 'A','Ᾰ' => 'A',
             'Ᾱ' => 'A','Ὰ' => 'A','ᾼ' => 'A','Β' => 'B','Γ' => 'G','Δ' => 'D',
             'Ε' => 'E','Έ' => 'E','Ἐ' => 'E','Ἑ' => 'E','Ἒ' => 'E','Ἓ' => 'E',
             'Ἔ' => 'E','Ἕ' => 'E','Ὲ' => 'E','Ζ' => 'Z','Η' => 'I','Ή' => 'I',
             'Ἠ' => 'I','Ἡ' => 'I','Ἢ' => 'I','Ἣ' => 'I','Ἤ' => 'I','Ἥ' => 'I',
             'Ἦ' => 'I','Ἧ' => 'I','ᾘ' => 'I','ᾙ' => 'I','ᾚ' => 'I','ᾛ' => 'I',
             'ᾜ' => 'I','ᾝ' => 'I','ᾞ' => 'I','ᾟ' => 'I','Ὴ' => 'I','ῌ' => 'I',
             'Θ' => 'T','Ι' => 'I','Ί' => 'I','Ϊ' => 'I','Ἰ' => 'I','Ἱ' => 'I',
             'Ἲ' => 'I','Ἳ' => 'I','Ἴ' => 'I','Ἵ' => 'I','Ἶ' => 'I','Ἷ' => 'I',
             'Ῐ' => 'I','Ῑ' => 'I','Ὶ' => 'I','Κ' => 'K','Λ' => 'L','Μ' => 'M',
             'Ν' => 'N','Ξ' => 'K','Ο' => 'O','Ό' => 'O','Ὀ' => 'O','Ὁ' => 'O',
             'Ὂ' => 'O','Ὃ' => 'O','Ὄ' => 'O','Ὅ' => 'O','Ὸ' => 'O','Π' => 'P',
             'Ρ' => 'R','Ῥ' => 'R','Σ' => 'S','Τ' => 'T','Υ' => 'Y','Ύ' => 'Y',
             'Ϋ' => 'Y','Ὑ' => 'Y','Ὓ' => 'Y','Ὕ' => 'Y','Ὗ' => 'Y','Ῠ' => 'Y',
             'Ῡ' => 'Y','Ὺ' => 'Y','Φ' => 'F','Χ' => 'X','Ψ' => 'P','Ω' => 'O',
             'Ώ' => 'O','Ὠ' => 'O','Ὡ' => 'O','Ὢ' => 'O','Ὣ' => 'O','Ὤ' => 'O',
             'Ὥ' => 'O','Ὦ' => 'O','Ὧ' => 'O','ᾨ' => 'O','ᾩ' => 'O','ᾪ' => 'O',
             'ᾫ' => 'O','ᾬ' => 'O','ᾭ' => 'O','ᾮ' => 'O','ᾯ' => 'O','Ὼ' => 'O',
             'ῼ' => 'O','α' => 'a','ά' => 'a','ἀ' => 'a','ἁ' => 'a','ἂ' => 'a',
             'ἃ' => 'a','ἄ' => 'a','ἅ' => 'a','ἆ' => 'a','ἇ' => 'a','ᾀ' => 'a',
             'ᾁ' => 'a','ᾂ' => 'a','ᾃ' => 'a','ᾄ' => 'a','ᾅ' => 'a','ᾆ' => 'a',
             'ᾇ' => 'a','ὰ' => 'a','ᾰ' => 'a','ᾱ' => 'a','ᾲ' => 'a','ᾳ' => 'a',
             'ᾴ' => 'a','ᾶ' => 'a','ᾷ' => 'a','β' => 'b','γ' => 'g','δ' => 'd',
             'ε' => 'e','έ' => 'e','ἐ' => 'e','ἑ' => 'e','ἒ' => 'e','ἓ' => 'e',
             'ἔ' => 'e','ἕ' => 'e','ὲ' => 'e','ζ' => 'z','η' => 'i','ή' => 'i',
             'ἠ' => 'i','ἡ' => 'i','ἢ' => 'i','ἣ' => 'i','ἤ' => 'i','ἥ' => 'i',
             'ἦ' => 'i','ἧ' => 'i','ᾐ' => 'i','ᾑ' => 'i','ᾒ' => 'i','ᾓ' => 'i',
             'ᾔ' => 'i','ᾕ' => 'i','ᾖ' => 'i','ᾗ' => 'i','ὴ' => 'i','ῂ' => 'i',
             'ῃ' => 'i','ῄ' => 'i','ῆ' => 'i','ῇ' => 'i','θ' => 't','ι' => 'i',
             'ί' => 'i','ϊ' => 'i','ΐ' => 'i','ἰ' => 'i','ἱ' => 'i','ἲ' => 'i',
             'ἳ' => 'i','ἴ' => 'i','ἵ' => 'i','ἶ' => 'i','ἷ' => 'i','ὶ' => 'i',
             'ῐ' => 'i','ῑ' => 'i','ῒ' => 'i','ῖ' => 'i','ῗ' => 'i','κ' => 'k',
             'λ' => 'l','μ' => 'm','ν' => 'n','ξ' => 'k','ο' => 'o','ό' => 'o',
             'ὀ' => 'o','ὁ' => 'o','ὂ' => 'o','ὃ' => 'o','ὄ' => 'o','ὅ' => 'o',
             'ὸ' => 'o','π' => 'p','ρ' => 'r','ῤ' => 'r','ῥ' => 'r','σ' => 's',
             'ς' => 's','τ' => 't','υ' => 'y','ύ' => 'y','ϋ' => 'y','ΰ' => 'y',
             'ὐ' => 'y','ὑ' => 'y','ὒ' => 'y','ὓ' => 'y','ὔ' => 'y','ὕ' => 'y',
             'ὖ' => 'y','ὗ' => 'y','ὺ' => 'y','ῠ' => 'y','ῡ' => 'y','ῢ' => 'y',
             'ῦ' => 'y','ῧ' => 'y','φ' => 'f','χ' => 'x','ψ' => 'p','ω' => 'o',
             'ώ' => 'o','ὠ' => 'o','ὡ' => 'o','ὢ' => 'o','ὣ' => 'o','ὤ' => 'o',
             'ὥ' => 'o','ὦ' => 'o','ὧ' => 'o','ᾠ' => 'o','ᾡ' => 'o','ᾢ' => 'o',
             'ᾣ' => 'o','ᾤ' => 'o','ᾥ' => 'o','ᾦ' => 'o','ᾧ' => 'o','ὼ' => 'o',
             'ῲ' => 'o','ῳ' => 'o','ῴ' => 'o','ῶ' => 'o','ῷ' => 'o','А' => 'A',
             'Б' => 'B','В' => 'V','Г' => 'G','Д' => 'D','Е' => 'E','Ё' => 'E',
             'Ж' => 'Z','З' => 'Z','И' => 'I','Й' => 'I','К' => 'K','Л' => 'L',
             'М' => 'M','Н' => 'N','О' => 'O','П' => 'P','Р' => 'R','С' => 'S',
             'Т' => 'T','У' => 'U','Ф' => 'F','Х' => 'K','Ц' => 'T','Ч' => 'C',
             'Ш' => 'S','Щ' => 'S','Ы' => 'Y','Э' => 'E','Ю' => 'Y','Я' => 'Y',
             'а' => 'A','б' => 'B','в' => 'V','г' => 'G','д' => 'D','е' => 'E',
             'ё' => 'E','ж' => 'Z','з' => 'Z','и' => 'I','й' => 'I','к' => 'K',
             'л' => 'L','м' => 'M','н' => 'N','о' => 'O','п' => 'P','р' => 'R',
             'с' => 'S','т' => 'T','у' => 'U','ф' => 'F','х' => 'K','ц' => 'T',
             'ч' => 'C','ш' => 'S','щ' => 'S','ы' => 'Y','э' => 'E','ю' => 'Y',
             'я' => 'Y','ð' => 'd','Ð' => 'D','þ' => 't','Þ' => 'T','ა' => 'a',
             'ბ' => 'b','გ' => 'g','დ' => 'd','ე' => 'e','ვ' => 'v','ზ' => 'z',
             'თ' => 't','ი' => 'i','კ' => 'k','ლ' => 'l','მ' => 'm','ნ' => 'n',
             'ო' => 'o','პ' => 'p','ჟ' => 'z','რ' => 'r','ს' => 's','ტ' => 't',
             'უ' => 'u','ფ' => 'p','ქ' => 'k','ღ' => 'g','ყ' => 'q','შ' => 's',
             'ჩ' => 'c','ც' => 't','ძ' => 'd','წ' => 't','ჭ' => 'c','ხ' => 'k',
             'ჯ' => 'j','ჰ' => 'h'
             );
        // @formatter:on
        return strtr($string, $transliteration);
    }

}
