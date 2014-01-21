<?php
namespace NanoFramework\Utilities;
use NanoFramework\Kernel;

/**
*
* @package NanoFramework\Utilities 
* @author Stephane BRUN
*/
class Page extends Kernel\Event\Observable
{
    protected $title;
    protected $metas;
    protected $css;
    protected $javascripts;
    protected $canonical;

    public $no_cache;

    /**
     * Constructor
     *
     * @return void
     * @author Stephane BRUN
     **/
    public function __construct($no_cache)
    {
        $this->title = "";
        $this->canonical = "";
        $this->metas = array();
        $this->css = array();
        $this->javascripts = array();
        $this->no_cache = $no_cache;
    }

    function _set_canonical($url)
    {
        $this->canonical = $url;
    }

    function _render_for_canonical()
    {
        if(!empty($this->canonical))
        {
            return "<link rel=\"canonical\" href=\"{$this->canonical}\" />\n";            
        }
    }

    function _set_title($title)
    {
        $this->title = $title;
    }

    function _render_for_title($default="")
    {
        if($this->title)
        {
            return "<title>{$this->title}</title>\n";            
        }

        return "<title>{$default}</title>\n";
    }
    
    function _add_meta($name, $content, $http_equiv=false)
    {
        array_push($this->metas, array(
            'name'       => $name,
            'content'    => $content,
            'http_equiv' => $http_equiv        
        ));
    }
    
    function _render_for_metas()
    {
        $html = array();
        $h = "";
        
        foreach($this->metas as $meta)
        {
            if($meta['http_equiv'])
            {
                $html[] = '<meta http-equiv="'.$meta['name'].'" content="'.$meta['content'].'" />';
            }
            else
            {
                $html[] = '<meta name="'.$meta['name'].'" content="'.$meta['content'].'" />';
            }
        }
        $h = implode("\n", $html)."\n"; 

        return $h;
    }
    
    function _add_css($css, $media='screen')
    {
        if(!in_array($css, $this->css))
        {        
            $this->css[$css] = array('file'=>$css, 'media'=>$media);
        }
    }

    /**
     * Render the css html tag(s)
     *
     * @return string the css html tag(s)
     * @todo Generate one single file of all the css files by media types if browser caching
     **/
    function _render_for_css()
    {
        $html = array();
        $h = "";
        
        // No browser cache of the css files
        if($this->no_cache)
        {
            foreach($this->css as $css)
            {
                if($css['media'])
                {
                    $media = 'media="'.$css['media'].'"';
                }

                $cache = "?".time();
         
                if(preg_match('/^http[s]?:\/\/(.*)$/', $css['file']))
                {
                    $html[] = '<link rel="stylesheet" href="'.$css['file'].'?'.time().'" type="text/css" '.$media.'/>';                
                }
                else
                {
                    $html[] = '<link rel="stylesheet" href="'.WEB_CSS.$css['file'].'?'.time().'" type="text/css" '.$media.'/>';                
                }

            }
            $h = implode("\n", $html)."\n"; 
        }

        // Generate one single file of all the css files by media types
        else
        {
            foreach($this->css as $css)
            {
                if($css['media'])
                {
                    $media = 'media="'.$css['media'].'"';
                }

                $cache = "?".time();
         
                if(preg_match('/^http[s]?:\/\/(.*)$/', $css['file']))
                {
                    $html[] = '<link rel="stylesheet" href="'.$css['file'].'" type="text/css" '.$media.'/>';                
                }
                else
                {
                    $html[] = '<link rel="stylesheet" href="'.WEB_CSS.$css['file'].'" type="text/css" '.$media.'/>';                
                }

            }
            $h = implode("\n", $html)."\n";             
        }

        return $h;
    }

    function _render_for_javascripts()
    {    
        $html = array();
        $h = "";
        
        foreach($this->javascripts as $javascript)
        {
            if(preg_match('/^http[s]?:\/\/(.*)$/', $javascript))
            {
                $html[] = '<script type="text/javascript" src="'.$javascript.'"></script>';                
            }
            else
            {
                $html[] = '<script type="text/javascript" src="'.WEB_JAVASCRIPTS.$javascript.'"></script>';                
            }
        }
        $h = implode("\n", $html)."\n"; 

        return $h;
    }

    function _add_javascript($javascript)
    {
        if(!in_array($javascript, $this->javascripts))
        {
            $this->javascripts[] = $javascript;
        }
   } 
}
