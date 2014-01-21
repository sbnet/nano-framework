<?php
namespace NanoFramework\Kernel;

/**
* Response
*
* @package NanoFramework\Kernel
* @author BRUN Stéphane <stephane@sbnet.fr>
* @version 0.0.1 - 24/11/2007 - BRUN Stéphane
*/
class Response 
{
    protected static $_instance;

    private $_cookies = array();

    private $_output_type;
    private $_output_charset;
    private $_output_types = array(
        'html'       => 'Content-Type: text/html',
        'xml'        => 'Content-Type: text/xml',
        'javascript' => 'Content-Type: text/javascript',
        'json'       => 'Content-Type: text/javascript',
        'text'       => 'Content-Type: text/plain'        
    );

    public function __construct()
    {
        $this->_output_type = 'html';
        $this->_output_charset = 'utf-8';
            
        foreach($_COOKIE as $name=>$cookie)
        {
            $this->_cookies[$name]['value'] = $cookie;
        }
    }

    public static function get_instance()
    {
        if(is_null(static::$_instance))
        {               
            $c = __CLASS__;
            static::$_instance = new $c;
        }
        return static::$_instance;
    }
    
    /**
    * Set a cookie value
    *
    * @param string the cookie's name
    * @param string the cookie's value
    * @param string the cookie's expire date
    */
    public function set_cookie($name, $value, $expire=null)
    {
        $this->_cookies[$name]['value'] = $value;
        $this->_cookies[$name]['expire'] = $expire;
        setcookie($name, $value, $expire);
    }
    
    /**
    * Get a cookie value
    *
    * @param string the cookie's name
    * @return string the cookie's value
    */
    public function get_cookie($name)
    {
        return $this->_cookies[$name];
    }
    
    /**
    * Delete a cookie
    *
    * @param string the cookie's name
    */
    public function delete_cookie($name)
    {
        setcookie($name, '', time()-3600);
        if(array_key_exists($this->_cookies, $name))
        {
            unset($this->_cookies[$name]);
        }
    }

    /**
    * Send a header
    *
    * If the headers can't be sent, an error will be thrown
    *
    * @param string header to send
    */
    public function set_header($value)
    {
        if(!$this->headers_sent())
        {
            header($value);
        }
        else
        {
            throw new NanoException(_('The headers are already sent !'), E_ERROR);
        }
    }
 
    /**
    * Check if all the headers are sent.
    *
    * @return bool true if they are sent
    */
    public function headers_sent()
    {
        return headers_sent();
    }
    
    /**
    * Set the output charset
    *
    * The default charset is utf-8
    *
    * @param string $charset
    */
    public function set_charset($charset)
    {
        $this->_output_charset = $charset;
    }    

    /**
    * Set the output type, the default type is the one configured by the server, in most cases it is HTML.
    *
    * The following types are possible :
    * - html
    * - xml
    * - javascript
    * - json
    * - text
    *
    * @param string $type 
    * @param string $charset
    */
    public function set_type($type, $charset='')
    {
        $this->_output_type = $type;

        if(array_key_exists($type, $this->_output_types))
        {                  
            if($charset != '')
            {
                $this->set_charset($charset);
            }        

            $this->set_header($this->_output_types[$type].'; charset='.$this->_output_charset); 
        }
    }

    /**
    * Get the current type
    *
    * @return string
    */
    public function get_current_type()
    {
        return $this->_output_type;
    }
    
    /**
    * Add some types to the types array. Can also be used to redefine a type, simply add it to the array.
    *
    * Format : 
    * <code>
    *   array('name' => 'Content-Type: <type>', ...)
    * </code>
    *
    * @param array $types
    */
    public function add_to_types($types)
    {
        $this->_output_types = array_merge($this->_output_types, $types); 
    }    
}
