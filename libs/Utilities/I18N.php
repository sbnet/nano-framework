<?php
namespace NanoFramework\Utilities;

/**
* I18N
*
* @author Stéphane BRUN <stephane@sbnet.fr>
* @package NanoFramework\Utilities
*/

/**
* I18N
*
* On Ubuntu (and maybe any other Debian based distribution) the lang used must also 
* be present on the system : /usr/lib/locale/<language>
*
* Use <i>locale -a</i> to list the locales installed on your system and put your .mo files under /app/locale/<language>/LC_MESSAGES
*
* I recommend to use {@link http://www.poedit.net poEdit} to generate your .mo files
*
* @package NanoFramework\Utilities
* @class I18N
* @author BRUN Stéphane <stephane@sbnet.fr>
* @version 0.0.1 - 24/11/2007 - BRUN Stéphane
*/
class I18N
{
    /**#@+
    * @access private
    */
    private static $_instance;
    private static $_langs = array(
        'en' => 'en_GB',
        'us' => 'en_US',
        'fr' => 'fr_FR'
    );
    private $domains=array();
    /**#@-*/
    
    /**
    * Redefine or add to the locales array
    *
    * @param string $lang lang to redefine ('fr', 'en', 'us', ...)
    * @param string $locale locale string ('fr_BE', ...)
    */
    public function redefine_locale($lang, $locale)
    {
        self::$_langs[$lang] = $locale;
    }
    
    /**
    * Initialize the I18N system.
    *
    * The lang (the first parameter) is automatically matched to somme values stored in an internal array
    * to create a string of the correct value. eg: 'en' is matched to 'en_GB' and 'us' is matched to 'en_US'.
    * Values that are not in this array are created by appending the upper case to the end of the string ('fr' -> 'fr_FR')
    * 
    * The array is constructed like this :
    * <code>
    * array(
    *    'en' => 'en_GB',
    *    'us' => 'en_US'
    * );
    * </code>
    *
    * You can also redefine this array at runtime using the {@link redefine_locale()} method.
    */
    public function initialize($lang, $domain, $path)
    { 
        $this->use_lang($lang);
        $this->initialize_domain($domain, $path);
        $this->use_domain($domain);
    }
    
    public function initialize_domain($domain, $path)
    {
        if(!isset($this->domains[$domain]))
        {
            bindtextdomain($domain, $path);
            $this->domains[$domain] = $path;
        }
    }
         
    public function use_domain($domain)
    {
        if($this->domains[$domain])
        {
            textdomain($domain);
        }
    }

    public function use_lang($lang, $store_in_session=true)
    {
        if(array_key_exists($lang, self::$_langs))
        {
            $lang = self::$_langs[$lang];
        }

        if($store_in_session)
        {
            $_SESSION['lang'] = $lang;
        }
        
        if(!empty($GLOBALS['env']['locale']['charset']))
        {
            $lang .= '.'.$GLOBALS['env']['locale']['charset'];
        }

        putenv('LANGUAGE='.$lang);
        putenv('LANG='.$lang);
        setlocale(LC_ALL, $lang);                
    }
    
    public function reset_lang()
    {
        $_SESSION['lang']=null;
    }
        
    public static function get_instance()
    {
        if(is_null(static::$_instance))
        {
            static::$_instance = new I18N();
        }
        return static::$_instance;
    }   
}
